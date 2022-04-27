$(document).ready(function() {

    var valBusdefect = $("#busdefect").val();

    if(valBusdefect != undefined){
        traerInformation(valBusdefect);
    }else{
        $("#crearBuscarInfo").on("click", function(){
            traerInformation();
        });

        $("#nom_transp").on("click", function(){
            vaciaInput(this);
        });
    }

    $('#accordion').on('hidden.bs.collapse', toggleChevron);
    $('#accordion').on('shown.bs.collapse', toggleChevron);

});

function cargando(){
    var standa = $("#nom_standa").val();
    Swal.fire({
        title: 'Cargando',
        text: 'Por favor espere...',
        imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
        showConfirmButton: false,
        closeOnClickOutside: false
    });
}


function busTranspor(campo){
    var standa = $("#nom_standa").val();
    var key = $(campo).val();
    var opcion = 'getTransportadoras';
    var dataString = 'key=' + key + '&opcion=' + opcion;
    var nameid = $(campo).attr('id');
    $.ajax({
        url: "../" + standa + "/factur/ajax_factur_resume.php",
        method: 'POST',
        data: dataString,
        success: function(data) {
            //Escribimos las sugerencias que nos manda la consulta
            $('#' + nameid + '-suggestions').fadeIn(1000).html(data);
            //Al hacer click en alguna de las sugerencias
            $('.suggest-element').on('click', function() {
                //Obtenemos la id unica de la sugerencia pulsada
                var id = $(this).attr('id');
                //asignamos el valor del id al input de cod_transp
                $("#cod_transp").val(id);
                //Editamos el valor del input con data de la sugerencia pulsada
                $(campo).val($('#' + id).attr('data'));
                //Desbloqueamos el boton
                $("#crearBuscarInfo").removeAttr('disabled');
                //Hacemos desaparecer el resto de sugerencias
                $('#' + nameid + '-suggestions').fadeOut(1000);
                
                return false;
            });
        }
    });
}


function vaciaInput(input){
    $(input).val("");
    //Vaciamos el input oculto con el codigo de la empresa transportadora
    $("#cod_transp").val("");
    $("#crearBuscarInfo").attr('disabled','disabled');
}

function toggleChevron(e) {
    $(e.target)
      .prev('.card-header')
      .find("i.fa")
      .toggleClass('fa-sort-desc fa-sort-asc');
}

function traerInformation(elemento = ''){
    if(elemento != ''){
        var cod_transp = elemento;
    }else{
        var cod_transp = $("#cod_transp").val();
    }
    var standa = $("#nom_standa").val();
    var opcion = 'getInfo';
    var dataString = 'cod_transp=' + cod_transp + '&opcion=' + opcion;
    if(cod_transp != undefined){
        $.ajax({
            url: "../" + standa + "/factur/ajax_factur_resume.php",
            method: 'POST',
            data: dataString,
            beforeSend: function() {
                cargando();
            },
            success: function(data) {
                $("#bodyViewInfo").empty();
                var new_div = $(data).hide();
                $('#bodyViewInfo').append(new_div);
                initTables('inf_tabfac');
                new_div.slideDown();
            },
            complete: function() {
                swal.close();
            }
        });
    }
}


//funcion que inicializa todas las tablas usando la libreria datatable
function initTables(id_table){
    
    var tabla = '#'+id_table;
    var table = $(tabla).DataTable({
            "bDestroy": true,
            'processing': true,
            "deferRender": true,
            "autoWidth": false,
            "search": {
                "regex": true,
                "caseInsensitive": false,
            },
            'paging': true,
            'info': true,
            'filter': true,
            'orderCellsTop': true,
            'fixedHeader': true,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "dom": "<'row'<'col-sm-12 col-md-3'B><'col-sm-12 col-md-3'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "buttons": [
                'excelHtml5',
                'csvHtml5',
            ],
            "order": [[ 0, "desc" ], [ 4, "desc" ]],
    });

    $(".dt-buttons .btn-group").parent().parent().addClass('mb-2');
}

