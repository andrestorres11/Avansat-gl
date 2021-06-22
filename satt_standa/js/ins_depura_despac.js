/* ! \file: ins_depura_despac
 *  \brief: permite visualizar correctamente las vistas en ins_depura_despac.php ademas de realizar algunas funciones ajax
 *  \author: Ing. Carlos Nieto
 *  \version: 1.0
 *  \date: 02/06/2021
 *  \bug: 
 *  \warning: 
 */

var standa = 'satt_standa';
var nom_empre = '';
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
            "url": "../" + standa + "/despac/ajax_depura_despac.php",
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
        "dom": "<'row'<'col-sm-12 col-md-3'B><'col-sm-12 col-md-3'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "buttons": [
            'excelHtml5',
            'csvHtml5',
        ]
    });
});

//---------------------------------------------
/*! \fn: getNombre
 *  \brief: obtiene el nombre de la empresa seleccionada en el select
 *  \author: Ing. Carlos Nieto
 *  \date: 02/06/2021
 *  \date modified: 
 *  \return html
 */

function getNombre() {
    this.nom_empre = '';
    var selectBox = document.getElementById("empresa");
    this.nom_empre = selectBox.options[selectBox.selectedIndex].text;
}
//---------------------------------------------
/*! \fn: generarReporte
 *  \brief: Genera una nueva consulta sql en la tabla principal segun los parametros establecidos por el usuario
 *  \author: Ing. Carlos Nieto
 *  \date: 02/06/2021
 *  \date modified: 
 *  \return html
 */

function generarReporte() {
    val = $("#busquedaFiltro").serializeArray()
    if (val[0]['value'] != '' && val[1]['value'] != '' && val[2]['value'] != '') {
        var table = $("#contenedor #tablaRegistros").DataTable({
            "ajax": {
                "url": "../" + standa + "/despac/ajax_depura_despac.php",
                "data": ({ option: 'generarReporte', data: val }),
                "type": 'POST'
            },
            'processing': true,
            "deferRender": true,
            "autoWidth": false,
            'destroy': true,
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
    } else {
        Swal.fire({
            title: 'Error!',
            text: 'Debes seleccionar al menos los campos de fechas',
            type: 'error',
            confirmButtonColor: '#336600'
        })
    }
}


//---------------------------------------------
/*! \fn: delReg
 *  \brief: Genera popup con la confirmaci�n para depurar los registros
 *  \author: Ing. Carlos Nieto
 *  \date: 02/06/2021
 *  \date modified: 
 *  \return html
 */

function delReg() {
    try {
        val = $("#filter").serializeArray();

        if (val[0]['value'] != '' && val[1]['value'] != '' && val[2]['value'] != '' && val[3]['value'] != '') {
            Swal.fire({
                title: '�Estas seguro?',
                text: "�Esta seguro que desea realizar la depuracion de los despachos de la transportadora " + this.nom_empre + "?",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#336600',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Si, confirmar'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "../" + standa + "/despac/ajax_depura_despac.php",
                        type: "post",
                        data: ({ option: 'delReg', cod_regist: $("#filter").serializeArray() }),
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
                                    title: '�Depuracion Exitosa!',
                                    text: data['response'],
                                    type: 'success',
                                    confirmButtonColor: '#336600'
                                }).then((result) => {
                                    if (result.value) {
                                        var table = $('#contenedor #tablaRegistros').DataTable();
                                        table.ajax.reload();
                                    }
                                })
                                $('#MoDepuracion').modal('hide');
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
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Debes seleccionar los campos',
                type: 'error',
                confirmButtonColor: '#336600'
            })
        }
    } catch (error) {
        console.error(error);
    }
}

//---------------------------------------------
/*! \fn: abrModalDetalles
 *  \brief: Genera una modal con la informacion en bitacora de cada registro
 *  \author: Ing. Carlos Nieto
 *  \date: 02/06/2021
 *  \date modified: 
 *  \return html
 */

function abrModalDetalles(cod_agrupa, cod_agrupa_num, nom_empre) {
    try {
        $('#contenedor #tablaModal thead tr th').each(function(i) {
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

        var table = $("#contenedor #tablaModal").DataTable({
            "ajax": {
                url: "../" + standa + "/despac/ajax_depura_despac.php",
                type: "post",
                data: ({ option: 'setModal', data: cod_agrupa_num }),
                dataType: "json",
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
            'destroy': true,
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
        $("#modalInformeTitulo").html(cod_agrupa + " Despachos depurados empresa de transporte " + nom_empre.toString().slice(1, -1));
        $("#detalles").modal("show");
    } catch (error) {
        console.log(error);
    }
}

//---------------------------------------------
/*! \fn: validateFields
 *  \brief: Toma la funci�n validaciones y actualiza la visual y gesti�n de los datos
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