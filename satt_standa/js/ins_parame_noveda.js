/*! \file: ins_parame_noveda.js
 *  \brief: 
 *  \author: 
 *  \author: 
 *  \version: 
 *  \date: 04/04/2016
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */
$(document).on("ready", function() {

});

function autocompletable(campo) {
    var standa = $("#standaID").val();
    var key = $(campo).val();
    var opcion = 'getTransportadoras';
    var dataString = 'key=' + key + '&opcion=' + opcion;
    var nameid = $(campo).attr('id');
    $.ajax({
        url: "../" + standa + "/novseg/ajax_parame_noveda.php",
        method: 'POST',
        data: dataString,
        success: function(data) {
            //Escribimos las sugerencias que nos manda la consulta
            $('#' + nameid + '-suggestions').fadeIn(1000).html(data);
            //Al hacer click en alguna de las sugerencias
            $('.suggest-element').on('click', function() {
                //Obtenemos la id unica de la sugerencia pulsada
                var id = $(this).attr('id');
                //Editamos el valor del input con data de la sugerencia pulsada
                $(campo).val($('#' + id).attr('data'));
                //Hacemos desaparecer el resto de sugerencias
                $('#' + nameid + '-suggestions').fadeOut(1000);
                //Desahinilita el bloqueo del boton
                $('#btnBuscar').removeAttr('disabled');
                return false;
            });
        }
    });
}

//funcion que inicializa todas las tablas usando la libreria datatable
function initTables(id_table) {
    $('#' + id_table + ' thead tr th').each(function(i) {
        var title = $(this).text();
        if (i != 0) {
            $(this).html('<label style="display:none;">' + title + '</label><input type="text" placeholder="Buscar ' + title + '" />');
            $('input', this).on('keyup change', function() {
                if (table.column(i).search() !== this.value) {
                    table
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        }
    });
    var tabla = '#' + id_table;
    var table = $(tabla).DataTable({
        "Destroy": true,
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

function vaciaInput(elemento) {
    $(elemento).val('');
    $('#btnBuscar').attr('disabled', true);
}

function consultaInformacion() {
    var standa = $("#standaID").val();
    var opcion = 'getInfo';
    var transp = $("#emp_transp").val().split("-")[0];
    var dataString = 'transp=' + transp + '&opcion=' + opcion;
    $.ajax({
        url: "../" + standa + "/novseg/ajax_parame_noveda.php",
        method: 'POST',
        data: dataString,
        beforeSend: function() {
            cargando("Buscando... Por favor espere.");
        },
        success: function(data) {
            $("#cont").empty();
            $("#cont").append(data);
            var busqueda = $('.conten-table');
            busqueda.each(function() {
                console.log($(this).attr('id'));
                initTables($(this).attr('id'));
            });
        },
        complete: function() {
            swal.close();
        }
    });


}

function cargando(texto) {
    var standa = $("#standaID").val();
    Swal.fire({
        title: 'Cargando',
        text: texto,
        imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
        imageAlt: 'Custom image',
        showConfirmButton: false,
        allowOutsideClick: false,
    })
}

function changeTableUsu(table_num) {
    $("#tableUsu").val(table_num);
}

function saveInfo() {
    var standa = $("#standaID").val();
    var opcion = 'save';
    var transp = $("#emp_transp").val().split("-")[0];
    var dataString = '&cod_transp=' + transp + '&opcion=' + opcion;
    var table = $('#table_' + $("#tableUsu").val()).DataTable();
    var data = table.$('input, select').serialize() + dataString;

    $.ajax({
        url: "../" + standa + "/novseg/ajax_parame_noveda.php",
        method: 'POST',
        data,
        dataType: "json",
        beforeSend: function() {
            cargando("Guardando... Por favor espere.");
        },
        success: function(data) {
            swal.close();
            if (data['status'] == 100) {
                Swal.fire({
                    title: 'Proceso Exitoso',
                    text: data['msj'],
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
                    text: data['msj'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                })
            }
        },
    });
}

//selecciona todos los checkbox de una tabla
function selectedAll(elemento) {
    var cod_etapax = $(elemento).attr('data');
    var booleanval = false;
    if ($(elemento).is(':checked')) {
        booleanval = true;
    }
    $('.chkb_' + cod_etapax).each(function() {
        $(this).prop("checked", booleanval);
        selectGemelo(this);
    });
    $('.colcheck_' + cod_etapax).each(function() {
        $(this).prop("checked", booleanval);
        selectGemelo(this);
    });
}

//selecciona todos los checkbox de una fila
function selectRow(elemento) {
    var cod_etapax = $(elemento).attr('data');
    var booleanval = false;
    if ($(elemento).is(':checked')) {
        booleanval = true;
    }
    var busqueda = $(elemento).parent().siblings('td').find('.chkb_' + cod_etapax);
    busqueda.each(function() {
        $(this).prop("checked", booleanval);
        selectGemelo(this);
    });
}

//duplica la informacion de una tabla para la pestaña de TODOS
function selectGemelo(elemento) {
    var fila = $(elemento).parent().parent();
    var condicionales = ['manale', 'fuepla', 'soltie'];
    //Limpia segun los condicionales
    $(fila).find('td input').each(function() {
        if ($(this).attr("data") != undefined) {
            const nameElemento = $(elemento).attr('data').split("_")[0];
            const nameCam = $(this).attr('data').split("_")[0];
            const EndCam = $(this).attr('data').split("_")[1];
            if (condicionales.includes(nameCam)) {
                condicionales.forEach(function(valor) {
                    nameattr = "." + valor + '_' + EndCam + "";
                    if (valor != nameElemento) {
                        $(nameattr).prop("checked", false);
                    }
                });

            }
        }
    });

    var booleanval = false;
    if ($(elemento).is(':checked')) {
        booleanval = true;
    }
    var clasegemela = $(elemento).attr('data');
    var busqueda = $('.' + clasegemela);
    busqueda.each(function() {
        $(this).prop("checked", booleanval);
    });

}

function insertGemelo(elemento) {
    var clasegemela = $(elemento).attr('class');
    var busqueda = $('.' + clasegemela);
    var valor = $(elemento).val();
    busqueda.each(function() {
        $(this).val(valor);
    });
}

function Selector(elemento) {
    if ($(elemento).is(':checked')) {
        $(elemento).attr('checked', 'checked');
    } else {
        $(elemento).removeAttr('checked');
    }

}

function disableTime(elemento) {
    var Row = $(elemento).parent().parent();
    var code = $(elemento).attr('code');
    var manale = Row.find('.manale_' + code);
    var soltie = Row.find('.soltie_' + code);

    if ($(manale).is(':checked') || $(soltie).is(':checked')) {
        var tiempo = Row.find('.tiempo_' + code);
        $(tiempo).val(0);
        $(tiempo).attr('disabled', true);
    }

    if ($(manale).is(':not(:checked)') && $(soltie).is(':not(:checked)')) {
        var tiempo = Row.find('.tiempo_' + code);
        $(tiempo).attr('disabled', false);
    }
}