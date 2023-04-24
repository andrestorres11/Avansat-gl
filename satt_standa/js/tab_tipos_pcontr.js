/* ! \file: ins_genera_viasxx
 *  \brief: permite visualizar correctamente las vistas en ins_genera_viasxx.php ademas de realizar algunas funciones ajax
 *  \author: Ing. Andres torres
 *  \version: 1.0
 *  \date: 14/09/2021
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
            "url": "../" + standa + "/rutas/ajax_tipos_pcontr.php",
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
            $("#crear").html('<div><a  class="small-box-footer btn btn-success btn-sm" data-toggle="modal" data-target="#regService" aria-controls="tablaRegistros"><span>Nuevo tipo de PC</span></a></div><br>');
        }

    });

    $("#guardEdicion").on("click", null, function() {
        guardEdicion();
    });
});



// Jquery Dependency
$(".loan-input").on("focusout", null, function() {
    var input = $(this).val();
    $(this).val(numeral(input).format('$ 000.00'));
});

$(".FormularioVia").validate({
    rules: {
        abr_servic: {
            required: true
        },
        des_servic: {
            required: true
        },
        tipSolici: {
            required: true
        },
        tipFormul: {
            required: true
        },
        nom_campox: {
            required: true
        },
        tar_diurna: {
            required: true
        },
        tar_noctur: {
            required: true
        },
        hor_ininoc: {
            required: true
        },
        hor_finnoc: {
            required: true
        }
    },
    messages: {
        abr_servic: {
            required: "Por favor escriba la abreviatura del servicio"
        },
        des_servic: {
            required: "Por favor escriba la descripcion del servicio"
        },
        tipSolici: {
            required: "Seleccion el tipo de de solicitud"
        },
        tipFormul: {
            required: "Seleccione formulario asociado al servicio"
        },
        nom_campox: {
            required: "Por favor escriba el nombre del campo"
        },
        tar_diurna: {
            required: "Registre la tarifa diurna"
        },
        tar_noctur: {
            required: "Registre la tarifa nocturna"
        },
        hor_ininoc: {
            required: "Registre la hora de inicio"
        },
        hor_finnoc: {
            required: "Ingrese la hora de fin"
        }
    },
    submitHandler: function(form) {
        almacenarDatos();
    }
});

function almacenarDatos() {
    var standa = 'satt_standa';
    var opcion = 'registrar';
    var dataString = 'option=' + opcion;
    var data = new FormData(document.getElementById('FormularioVia'));
    $.ajax({
        url: "../" + standa + "/rutas/ajax_tipos_pcontr.php?" + dataString,
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
        },
    });
}

function guardEdicion() {
    var standa = 'satt_standa';
    var opcion = 'registrar';
    var dataString = 'option=' + opcion;
    var data = new FormData(document.getElementById('FormEdit'));
    $.ajax({
        url: "../" + standa + "/rutas/ajax_tipos_pcontr.php?" + dataString,
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
        },
    });
}

function cargando(texto) {
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

function cargaImagen(elemento) {
    input_file = $("#rut_iconoxID");
    if (elemento.files && elemento.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#previewImagen + img').remove();
            $('#previewImagen').after('<img src="' + e.target.result + '" width="40%"/>');
            input_file.addClass('pt-3');
        }
        reader.readAsDataURL(elemento.files[0]);
    }
}

function cargaImagenE(elemento) {
    input_file = $("#rut_iconoxID_e");
    if (elemento.files && elemento.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#previewImagen_e + img').remove();
            $('#previewImagen_e').after('<img src="' + e.target.result + '" width="40%"/>');
            input_file.addClass('pt-3');
        }
        reader.readAsDataURL(elemento.files[0]);
    }
}

function edit(cod) {
    var standa = 'satt_standa';
    var opcion = 'edit-forminput';
    var dataString = 'option=' + opcion + '&cod=' + cod;
    $.ajax({
        url: "../" + standa + "/rutas/ajax_tipos_pcontr.php?" + dataString,
        method: 'POST',
        data: '',
        async: false,
        dataType: "json",
        success: function(data) {
            $("#modal-edit").empty();
            $("#modal-edit").append(data);
            $("#editService").modal("show");
        }
    });
}

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
                    url: "../" + standa + "/rutas/ajax_tipos_pcontr.php",
                    type: "post",
                    data: ({ option: 'updEst', estado: $(objet).attr('data-estado'), cod_tpcont: $(objet).val() }),
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

function logicDelete(code, option) {
    var palabra = 'deshabilitar';
    var number = 0;
    if (option == 'enable') {
        palabra = 'habilitar';
        var number = 1;
    }
    var titulo = 'Esta seguro';
    var texto = 'Que desea ' + palabra + ' este registro?';
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
            ajaxLogicDelete(code, number);
        }
    })
}

function ajaxLogicDelete(code, option) {
    swal.close();
    var standa = 'satt_standa';
    var dataString = 'option=disaenable';
    var data = new FormData();
    data.append('cod_tpcont', code);
    data.append('ind_status', option);
    $.ajax({
        url: "../" + standa + "/rutas/ajax_tipos_pcontr.php?" + dataString,
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

function opeEdit(code, option) {
    var standa = 'satt_standa';
    var dataString = 'option=getRegistro&cod_tpcont=' + code;
    $.ajax({
        url: "../" + standa + "/rutas/ajax_tipos_pcontr.php?" + dataString,
        method: 'POST',
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            cargando("Guardando. Por favor espere.")
        },
        success: function(data) {
            swal.close();
            console.log(data['nom_tpcont']);
            $("#nom_tpcontID_e").val(data['nom_tpcont']);
            $("#cod_tpcont").val(data['cod_tpcont']);
            $('#previewImagen_e + img').remove();
            $('#previewImagen_e').after('<img src="' + data['rut_iconoxC'] + '" width="40%"/>');
            $("#img_iconx").val(data['rut_iconox']);
            $("#ediRegistro").modal("show");
        },
    });
}

function decode_utf8(word) {
    return decodeURIComponent(escape(word));
}