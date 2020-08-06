/* ! \file: ins_hojvid_ctxxxx
 *  \brief: permite visualizar correctamente las vistas en ins_genera_bancox.php ademas de realizar algunas funciones ajax
 *  \author: Ing. Luis Manrique
 *  \version: 1.0
 *  \date: 27/04/2020
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
            "url": "../" + standa + "/recurs/ajax_hojvid_ctxxxx.php",
            "data": ({ option: 'setRegistros' }),
            "type": 'POST'
        },
        "responsive": true,
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
            $("#crear").html('<div><a tabindex="0" onclick="formRegistro(\'form\', \'xl\')" class="small-box-footer btn btn-success btn-sm" data-toggle="modal" data-target="#modal-xl" aria-controls="tablaRegistros"><span>Crear registro</span></a></div><br>');
        }

    });

});

//---------------------------------------------
/*! \fn: formRegistro
 *  \brief: Genera popup con el formulario a diligenciar 
 *  \author: Ing. Luis Manrique
 *  \date: 29/04/2020
 *  \date modified: 
 *  \return html
 */

function formRegistro(modulo, tam, cod_docume = null) {
    try {
        var boton = cod_docume == null ? 'Crear' : 'Actualizar';
        Swal.fire({
            title: decode_utf8('¿Estas seguro?'),
            text: decode_utf8("¿Estas seguro que desea " + boton + " este registro?"),
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#336600',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Si, confirmar'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "../" + standa + "/recurs/ajax_hojvid_ctxxxx.php",
                    type: "post",
                    data: ({ option: modulo, tam: tam, cod_docume: cod_docume }),
                    success: function(data) {
                        Swal.fire({
                            html: data,
                            width: 800,
                            padding: '0.2em',
                            showCancelButton: true,
                            confirmButtonColor: '#336600',
                            cancelButtonColor: '#aaa',
                            confirmButtonText: boton,
                            allowOutsideClick: false,
                            onOpen: () => {
                                inputDate("dat_basico");
                                inputDate("tip_contra");
                                inputList("dat_basico");
                                inputList("tip_contra");
                                validaDesbloqueoActivi();
                                $('.money').mask("#.##0", { reverse: true });

                            },
                            preConfirm: () => {
                                if (!validateFields()) {
                                    return false;
                                }
                            }
                        }).then((result) => {
                            if (result.value) {
                                var form = $("#dat_basico").serialize() + "&" + $("#tip_contra").serialize();
                                form = form + "&option=regForm"
                                $.ajax({
                                    url: "../" + standa + "/recurs/ajax_hojvid_ctxxxx.php",
                                    type: "post",
                                    data: form,
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
                        })

                        $(".cerrar").on("click", function() {
                            var div = $(this).parents('.fade');
                            setTimeout(function() {
                                $(div).remove();
                            }, 500);
                        });
                    }
                });
            }
        });

    } catch (error) {
        console.error(error);
    }
}

//---------------------------------------------
/*! \fn: updEst
 *  \brief: Genera popup con la confirmación para actualziar estado
 *  \author: Ing. Luis Manrique
 *  \date: 28/04/2020
 *  \date modified: 
 *  \return html
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
                    url: "../" + standa + "/recurs/ajax_hojvid_ctxxxx.php",
                    type: "post",
                    data: ({ option: 'updEst', estado: $(objet).attr('data-estado'), cod_docume: $(objet).val() }),
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

//---------------------------------------------
/*! \fn: validateFields
 *  \brief: Toma la función validaciones y actualiza la visual y gestión de los datos
 *  \author: Ing. Luis Manrique
 *  \date: 28/04/2020
 *  \date modified: 
 *  \return boolean
 */

function validateFields(field = null) {
    var ban = true;

    if (!validaciones()) {
        if (field == null) {
            $(".inc_alert").each(function() {
                $(this).addClass("label label-danger");
                $(this).text(decode_utf8($(this).text())).css({ "cursor": "pointer", "position": "absolute", "transform": "translate(-70px, 0px)" });
                $(this).siblings().css({ "border-color": "red" });
            });
            ban = false;
        } else {
            $(".inc_alert").each(function() {
                if ($(this).siblings().attr("id") != $(field).attr("id")) {
                    $(this).remove();
                    $(this).siblings().css({ "border-color": "none" });
                } else {
                    $(this).addClass("label label-danger");
                    $(this).text(decode_utf8($(this).text())).css({ "cursor": "pointer", "position": "absolute", "transform": "translate(-70px, 0px)" });
                    $(this).siblings().css({ "border-color": "red" });
                }
            });

            $(".validate").each(function() {
                if ($(this).attr("id") == $(field).attr("id")) {
                    if ($(this).siblings().attr("id") == undefined) {
                        $(this).css({ "border-color": "green" });
                    }
                }
            });

            ban = false;
        }
    } else {
        $(".validate").each(function() {
            $(this).css({ "border-color": "green" });
        });
    }

    return ban;
};

//---------------------------------------------
/*! \fn: decode_utf8
 *  \brief: Decodifica textos
 *  \author: Ing. Luis Manrique
 *  \date: 28/04/2020
 *  \date modified: 
 *  \return string
 */

function decode_utf8(word) {
    return decodeURIComponent(escape(word));
}


//---------------------------------------------
/*! \fn: inputDate
 *  \brief: Asigna datepicker a los camopos con clase date
 *  \author: Ing. Luis Manrique
 *  \date: 28/04/2020
 *  \date modified: 
 *  \return 
 */
function inputDate(form) {
    $("#" + form + " .date").each(function() {
        // Rango de fechas
        $('#' + $(this).attr("id")).datetimepicker();
        // Selector de rango de fechas con selector de tiempo
    });

    $('.date').datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'ES'
    });
}


//---------------------------------------------
/*! \fn: inputList
 *  \brief: Asigna listas a los campos select
 *  \author: Ing. Luis Manrique
 *  \date: 28/04/2020
 *  \date modified: 
 *  \return string
 */
function inputList(form) {

    $("#" + form + " .list").each(function() {
        // Rango de fechas
        $('#' + $(this).attr("id")).autocomplete({
            serviceUrl: "../" + standa + "/recurs/ajax_hojvid_ctxxxx.php?option=dataList&file=" + $(this).attr("id"),
            onSelect: function(suggestion) {
                $('#' + suggestion.campo).val(suggestion.data);
                //Elimina 
                if ($('#nom_activi').val() == 'Proveedor') {
                    $("#usr_asigna").removeAttr('obl');
                    $("#cod_perfil").removeAttr('obl');
                    $("#nom_perfil").removeAttr('obl');
                }
            }
        });
    });
}

function validaDesbloqueoActivi() {
    if ($('#nom_activi').val() == 'Proveedor') {
        $("#usr_asigna").removeAttr('obl');
        $("#cod_perfil").removeAttr('obl');
        $("#nom_perfil").removeAttr('obl');
    }
}