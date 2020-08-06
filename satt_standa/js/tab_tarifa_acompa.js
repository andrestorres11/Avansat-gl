/* ! \file: tab_tarifa_acompa
 *  \brief: permite visualizar correctamente las vistas en tab_tarifa_acompa.php ademas de realizar algunas funciones ajax
 *  \author: Ing. Cristian Torres
 *  \version: 1.0
 *  \date: 17/07/2020
 *  \bug: 
 *  \warning: 
 */

var standa = 'satt_standa';
$(function() {

    $('#contenedor #tablaRegistros thead tr th').each(function(i) {
        var title = $(this).text();
        $(this).html('<label style="display:none;">' + title + '</label><input type="text" placeholder="Buscar ' + title + '" />');

        $('input', this).on('keyup change', function() {
            if (table.column(i).search() !== this.value) {
                table
                    .column(i)
                    .search(this.value)
                    .draw();
            }
        });
    });

    var table = $("#contenedor #tablaRegistros").DataTable({
        "ajax": {
            "url": "../" + standa + "/serasi/ajax_tarifa_acompa.php",
            "data": ({ option: 'setRegistros' }),
            "type": 'POST'
        },
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
        "dom": "<'row'<'col-md-4'<'#crear'>>><'row'<'col-md-4'B><'col-md-4'l><'col-md-4'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>",
        "buttons": [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
        ],
        fnInitComplete: function() {
            $("#crear").html('<div><a  class="small-box-footer btn btn-success btn-sm" data-toggle="modal" data-target="#regService" aria-controls="tablaRegistros"><span>Crear nueva tarifa</span></a></div><br>');
        }

    });
});

/*! \fn: busquedaCiudad
 *  \brief: Realiza la busqueda por ajax de la ciudad segun los datos ingresados en inout y muestra las diferentes opciones del mismo
 *  \author: Ing. Cristian Torres
 *  \date: 17/07/2020
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return: html
 */
function busquedaCiudad(campo) {
    var standa = 'satt_standa';
    var key = $(campo).val();
    var opcion = 'consulta_ciudades';
    var dataString = 'key=' + key + '&option=' + opcion;
    var nameid = $(campo).attr('id');
    $.ajax({
        url: "../" + standa + "/serasi/ajax_tarifa_acompa.php",
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
                return false;
            });
        }
    });
}

/*! \fn: busquedaCiudadEdicion
 *  \brief: Realiza la busqueda por ajax de la ciudad segun los datos ingresados en inout y muestra las diferentes opciones del mismo UNICAMENTE para la modal de edicion
 *  \author: Ing. Cristian Torres
 *  \date: 17/07/2020
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return: html
 */
function busquedaCiudadEdicion(campo) {
    var standa = 'satt_standa';
    var key = $(campo).val();
    var opcion = 'consulta_ciudades';
    var dataString = 'key=' + key + '&option=' + opcion;
    var nameid = $(campo).attr('id');
    $.ajax({
        url: "../" + standa + "/serasi/ajax_tarifa_acompa.php",
        method: 'POST',
        data: dataString,
        success: function(data) {
            //Escribimos las sugerencias que nos manda la consulta
            $('#' + nameid + '-suggestionse').fadeIn(1000).html(data);
            //Al hacer click en alguna de las sugerencias
            $('.suggest-element').on('click', function() {
                //Obtenemos la id unica de la sugerencia pulsada
                var id = $(this).attr('id');
                //Editamos el valor del input con data de la sugerencia pulsada
                $(campo).val($('#' + id).attr('data'));
                //Hacemos desaparecer el resto de sugerencias
                $('#' + nameid + '-suggestionse').fadeOut(1000);
                return false;
            });
        }
    });
}

/*! \fn: loan-input
 *  \brief: Hace la conversion del formato del campo de tarifas
 *  \author: Ing. Cristian Torres
 *  \date: 17/07/2020
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
// Jquery Dependency
$(".loan-input").on("focusout", null, function() {
    var input = $(this).val();
    $(this).val(numeral(input).format('$ 000.00'));
});

function borrado_input(elemento) {
    $(elemento).val("");
}



$(".FormularioTarAcomp").validate({
    rules: {
        ciu_origen: {
            required: true
        },
        ciu_destin: {
            required: true
        },
        val_tarifa: {
            required: true
        }
    },
    messages: {
        ciu_origen: {
            required: "Por favor seleccione la ciudad de origen"
        },
        ciu_destin: {
            required: "Por favor seleccione la ciudad de destino"
        },
        val_tarifa: {
            required: "Por favor ingrese el valor de la tarifa"
        }
    },
    submitHandler: function(form) {
        almacenarDatos();
    }
});

/*! \fn: almacenarDatos
 *  \brief: Almacena los datos de las nuevas tarifas ingresadas por medio de ajax y confirma respuesa de la misma.
 *  \author: Ing. Cristian Torres
 *  \date: 17/07/2020
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function almacenarDatos() {
    var standa = 'satt_standa';
    var opcion = 'registrar';
    var dataString = 'option=' + opcion;
    $.ajax({
        url: "../" + standa + "/serasi/ajax_tarifa_acompa.php?" + dataString,
        method: 'POST',
        data: $(".FormularioTarAcomp").serialize(),
        async: false,
        dataType: "json",
        success: function(data) {
            if (data['status'] == 200) {
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

        }
    });
}

/*! \fn: edit
 *  \brief: Trae el html por ajax del formulario de edicion de un registro con los datos cargados del mismo.
 *  \author: Ing. Cristian Torres
 *  \date: 17/07/2020
 *  \date modified: dia/mes/año
 *  \param: cod (codigo del la tarifa REGISTRO)
 *  \return:
 */
function edit(cod) {
    var standa = 'satt_standa';
    var opcion = 'edit-forminput';
    var dataString = 'option=' + opcion + '&cod=' + cod;
    $.ajax({
        url: "../" + standa + "/serasi/ajax_tarifa_acompa.php?" + dataString,
        method: 'POST',
        data: '',
        async: false,
        dataType: "json",
        success: function(data) {
            $("#modal-edit").empty();
            $("#modal-edit").append(data);
            $("#editAcompa").modal("show");
        }
    });
}

/*! \fn: updEst
 *  \brief: Actualiza el estado del registro
 *  \author: Ing. Cristian Torres
 *  \date: 17/07/2020
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function updEst(objet) {
    var estText = $(objet).attr('data-estado') == 1 ? 'desactivar' : 'activar';
    try {
        Swal.fire({
            title: decode_utf8('¿Estas seguro?'),
            text: decode_utf8("¿Estas seguro que desea " + estText + " este registro?"),
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#336600',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Si, confirmar'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "../" + standa + "/serasi/ajax_tarifa_acompa.php",
                    type: "post",
                    data: ({ option: 'updEst', estado: $(objet).attr('data-estado'), cod_acompa: $(objet).val() }),
                    dataType: "json",
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Cargando',
                            text: 'Por favor espere...',
                            imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
                            imageAlt: 'Custom image',
                            showConfirmButton: false,
                        })
                    },
                    success: function(data) {
                        if (data['status'] == 200) {
                            Swal.fire({
                                title: 'Registrado!',
                                text: data['response'],
                                type: 'success',
                                confirmButtonColor: '#336600'
                            }).then((result) => {
                                if (result.value) {
                                    var table = $('#contenedor #tablaRegistros').DataTable();
                                    table.ajax.reload();
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
                    }
                });
            }
        });
    } catch (error) {
        console.error(error);
    }
}

function decode_utf8(word) {
    return decodeURIComponent(escape(word));
}