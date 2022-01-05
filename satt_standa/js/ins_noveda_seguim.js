$(document).ready(function() {
    inicializarTablas(0);
});


function inicializarTablas(ind_aplfil){
    var standa = $("#standaID").val();
    $('#table_data thead tr th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<label style="display:none;">'+title+'</label><input type="text" placeholder="Buscar '+title+'" />' );
 
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );
    var tabla = "#table_data";    
    nom_noveda = $("#nom_noveda").val();
    cod_noveda = $("#cod_noveda").val();
    cod_etapax = $("#cod_etapax").val();
    cod_riesgo = $("#cod_riesgo").val();
    apl_filtro = ind_aplfil;
    var table = $(tabla).DataTable({
        "ajax": {
            "url": "../" + standa + "/novseg/ajax_noveda_seguim.php",
            "data": ({ opcion: 'getRegistros', nom_noveda: nom_noveda, cod_noveda: cod_noveda, cod_etapax: cod_etapax,
                       cod_riesgo: cod_riesgo, apl_filtro: apl_filtro}),
            "type": 'POST',
            'dataSrc':'',
        },
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
            ]
    });

    $(".dt-buttons .btn-group").parent().parent().addClass('mb-2');
   
}


function cargaImagen(elemento){
    input_file = $("#rut_iconoxID");
    if (elemento.files && elemento.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#previewImagen + img').remove();
            $('#previewImagen').after('<img src="'+e.target.result+'" width="50%"/>');
            input_file.addClass('pt-3');
        }
        reader.readAsDataURL(elemento.files[0]);
    }
}

function validateForm(){
    formulario = $("#FormRegist");
    formulario.removeData('validator');
    var validator = formulario.validate({ignore: []
    });

    $('.req').each(function () {
        $(this).rules("add", {
            required: true,
            messages: {
                required: "Este campo es requerido"
            }
        });
      });

    if(formulario.valid()){
        guardarData(); 
    }
}

function guardarData(){
    var standa = $("#standaID").val();
    var dataString = 'opcion=guardar';
    var data = new FormData(document.getElementById('FormRegist'));
    $.ajax({
        url: "../" + standa + "/novseg/ajax_noveda_seguim.php?" + dataString,
        method: 'POST',
        data,
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            cargando("Guardando. Por favor espere.")
        },
        success: function(data) {
            if (data['status'] == 1000) {
                $('#NuevoRegistro').modal('hide');
                swal.close();
                Swal.fire({
                    title: 'Registrado!',
                    text: data['response'],
                    type: 'success',
                    confirmButtonColor: '#336600'
                }).then((result) => {
                    if (result.value) {
                        location.reload();
                    }
                })
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data['response'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                })
            }
        },
    });
}

function cargando(texto){
    var standa = 'satt_standa';
    Swal.fire({
        title: 'Cargando',
        text: texto,
        imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
        imageAlt: 'Custom image',
        showConfirmButton: false,
        allowOutsideClick: false,
    })
}


function logicDelete(code, option){
    var palabra = 'deshabilitar';
    var number = 0;
    if(option=='enable'){
        palabra = 'habilitar';
        var number = 1;
    }
    var titulo= '�Esta seguro';
    var texto = 'Que desea '+palabra+' este registro?';
    Swal.fire({
        title: titulo,
        text: texto,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#336600',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
      }).then((result) => {
        if (result.value) {
            ajaxLogicDelete(code,number);
        }
      })
}

function ajaxLogicDelete(code, option){
            swal.close();
            var standa = $("#standaID").val();
            var dataString = 'opcion=disaenable';
            var data = new FormData();
            data.append('cod_noveda', code);
            data.append('ind_status', option);
            $.ajax({
                url: "../" + standa + "/novseg/ajax_noveda_seguim.php?" + dataString,
                method: 'POST',
                data,
                async: false,
                dataType: "json",
                contentType: false,
                processData: false,
                beforeSend: function() {
                    cargando("Guardando. Por favor espere.")
                },
                success: function(data) {
                    if (data['status'] == 1000) {
                        swal.close();
                        Swal.fire({
                            title: 'Registrado!',
                            text: data['response'],
                            type: 'success',
                            confirmButtonColor: '#336600'
                        }).then((result) => {
                            if (result.value) {
                                location.reload();
                            }
                        })
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data['response'],
                            type: 'error',
                            confirmButtonColor: '#336600'
                        })
                    }
                },
            });
}

function opeEdit(code, option){
    var opcion = 'Nueva';
    var nom_btn = 'Crear';
    $("#ind_updateID").val(option);
    if(option==1){
        $('#cod_novedaSpace').show();
        opcion = 'Editar';
        nom_btn = 'Actualizar';
        var standa = $("#standaID").val();
        var dataString = 'opcion=getRegistro';
        var data = new FormData();
        data.append('cod_noveda', code);
        $.ajax({
            url: "../" + standa + "/novseg/ajax_noveda_seguim.php?" + dataString,
            method: 'POST',
            data,
            async: false,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function(data) {
            $("#nom_observaID").empty();
            $("#cod_novedaID").val(data['cod_noveda']);
            $("#cod_novedaVID").val(data['cod_noveda']);
            $("#nom_novedaID").val(data['nom_noveda']);
            $("#cod_etapaxID option[value='"+data['cod_etapax']+"']").attr("selected", "selected");
            $("#cod_riesgoID option[value='"+data['cod_riesgo']+"']").attr("selected", "selected");
            $("#nom_observaID").append(data['nom_observ']);
            $('#previewImagen + img').remove();
            $('#previewImagen').after('<img src="'+data['rut_iconox']+'" width="50%"/>');
            },
        });
        $('#rut_iconoxID').removeClass('req');
    }else{
        $('#rut_iconoxID').addClass('req');
        $('#cod_novedaSpace').hide();
    }

    $("#title-modal").empty();
    $("#title-modal").append(opcion + ' Novedad');

    $("#nom_btnID").empty();
    $("#nom_btnID").append(nom_btn + ' Novedad');

    
    $("#NuevoRegistro").modal("show");
}
