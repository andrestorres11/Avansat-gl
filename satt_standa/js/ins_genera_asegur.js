/*! \file: ins_genera_asegur.js
 *  \brief: 
 *  \author: 
 *  \author: 
 *  \version: 
 *  \date: 04/04/2016
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */
$(document).ready(function() {
    $('#FormRegist').removeData('validator');
    var validator = $("#FormRegist").validate({
        ignore: []
    });

    $('#cod_transpID').on('blur', function() {
        console.log("Blur event triggered");
        var nit = $(this).val();
        if (nit.length > 0 && $.isNumeric(nit)) {
            consultaDV(nit);
            consultaTransp(nit);
        } else {
            $('#dv').val('');
        }
    });

    if($('#ase_asignaID').length){
        var ase_asigna = $("#ase_asignaID").val();
        $("#emp_transp").val(ase_asigna);
        $("#emp_transp").prop('disabled',true);
        consultaInformacion();
    }
});


function consultaDV(nit){
    var standa = $("#standaID").val();
    var opcion = 'getDV';
    var dataString = 'nit=' + nit + '&opcion=' + opcion;
    $.ajax({
        url: "../" + standa + "/transp/ajax_genera_asegur.php",
        method: 'POST',
        data: dataString,
        success: function(data) {
            $('#dv').val(data);
        }
    });
}

function consultaTransp(nit){
    var standa = $("#standaID").val();
    var opcion = 'getTransp';
    var dataString = 'nit=' + nit + '&opcion=' + opcion;
    $.ajax({
        url: "../" + standa + "/transp/ajax_genera_asegur.php", // Verifica que esta ruta es correcta
        method: 'POST',
        data: dataString,
        dataType: 'json', // A√±adido dataType para manejar JSON
        success: function(response) {
            if (response.length > 0) {
                var data = response[0]; // Accede al primer objeto en el array
                $('#nom_transpID').val(data.nom_tercer || '');
                $('#abr_transpID').val(data.abr_tercer || '');
                $('#cod_ciudadID').val(data.cod_ciudad || '');
                $('#nom_direccID').val(data.dir_domici || '');
                $('#num_telefoID').val(data.num_telefo || '');
                $('#cor_transpID').val(data.dir_emailx || '');
                $('#regimenID').val(data.cod_terreg || '');
                $('#reg_transpID').val(0);
                // Mostrar el mensaje y ocultarlo despu√©s de 5 segundos
                $('.alert-info').fadeIn().delay(5000).fadeOut();
            } else {
                // Habilitar los campos y ocultar el mensaje
                $('#nom_transpID').val('');
                $('#abr_transpID').val('');
                $('#cod_ciudadID').val('');
                $('#nom_direccID').val('');
                $('#num_telefoID').val('');
                $('#cor_transpID').val('');
                $('#regimenID').val('');
                $('#nom_transpID, #abr_transpID, #cod_ciudadID, #nom_direccID, #num_telefoID, #cor_transpID, #regimenID').prop('disabled', false);
                $('.alert-info').hide();
                $('#reg_transpID').val(1);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("AJAX error:", textStatus, errorThrown);
        }
    });
}

function reiniciateFormul(){

}

function autocompletable(campo) {
    var standa = $("#standaID").val();
    var key = $(campo).val();
    var opcion = 'getTransportadoras';
    var dataString = 'key=' + key + '&opcion=' + opcion;
    var nameid = $(campo).attr('id');
    $.ajax({
        url: "../" + standa + "/transp/ajax_genera_asegur.php",
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
        url: "../" + standa + "/transp/ajax_genera_asegur.php",
        method: 'POST',
        data: dataString,
        beforeSend: function() {
            cargando("Buscando... Por favor espere.");
        },
        success: function(data) {
            $("#cont").empty();
            $("#cont").append(data);
            var busqueda = $('.conten-table');
            $("#cod_asegurSID").val(transp);
            $("#nuevoAseguradoBtn").show();
            busqueda.each(function() {
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



function Selector(elemento) {
    if ($(elemento).is(':checked')) {
        $(elemento).attr('checked', 'checked');
    } else {
        $(elemento).removeAttr('checked');
    }

}

function limpia(elemento) {
    $(elemento).val("");
}

function busquedaCiudad(campo) {
    var standa = $("#standaID").val();
    var key = $(campo).val();
    var opcion = 'consultaCiudades';
    var dataString = 'key=' + key + '&opcion=' + opcion;
    var nameid = $(campo).attr('id');
    $.ajax({
        url: "../" + standa + "/transp/ajax_genera_asegur.php",
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

function validacionesCampos(formulario) {
    $('#' + formulario + ' .form-control').each(function() {
        if ($(this).attr('validate') != undefined) {
            var idcamp = "#" + this.id;
            if ($(this).hasClass("req")) {
                $(idcamp).rules("add", {
                    required: true,
                    messages: {
                        required: "Este campo es requerido"
                    }
                });
            }

            if ($(this).hasClass("num")) {
                $(idcamp).rules("add", {
                    number: true,
                    messages: {
                        number: "Solo se aceptan numeros"
                    }
                });
            }

            if ($(this).hasClass("ema")) {
                $(idcamp).rules("add", {
                    email: true,
                    messages: {
                        email: "El correo no es v·lido"
                    }
                });
            }

            if ($(this).hasClass("min6max6")) {
                $(idcamp).rules("add", {
                    minlength: 6,
                    maxlength: 6,
                    messages: {
                        minlength: "Debe tener almenos 6 caracteres",
                        maxlength: "Solo es permitido maximo 6 caracteres",
                    }
                });
            }

        } else {
            $(this.id).rules("remove");
        }
    });
}


function saveInfo(formulario) {
    validacionesCampos(formulario);
    if ($("#" + formulario).valid()) {
        var standa = $("#standaID").val();
        var data = new FormData(document.getElementById(formulario));
        var dataString = 'opcion=save';

        $("#dv").prop("disabled", false);
        data.append("dv", $("#dv").val());
        $("#dv").prop("disabled", true);

        if ($('#cod_asegurSID').length) {
            var ase_asigna = $("#cod_asegurSID").val();
            data.append("ase_asigna", ase_asigna);
        }

        $.ajax({
            url: "../" + standa + "/transp/ajax_genera_asegur.php?" + dataString,
            method: 'POST',
            data: data,
            async: false,
            dataType: "json",
            contentType: false,
            processData: false,
            beforeSend: function () {
                cargando("Creando la asignaciÛn. Por favor espere.")
            },
            success: function (data) {
                if (data['status'] == 100) {
                    $('#NuevoAseguradoModal').modal('hide');
                    consultaInformacion();
                    swal.close();
                    Swal.fire(
                        'Asignado',
                        data['response'],
                        'success'
                    );
                    
                } else {
                    alertError(data['response']);
                }
            },
        });
    } else {
        alertError('Hay campos obligatorios sin llenar.');
    }
}


function unassignment(cod_asegur, cod_tercer) {
    // Mostrar una confirmaciÛn usando SweetAlert2
    Swal.fire({
        title: 'øEst·s seguro?',
        text: "No podr·s revertir esta acciÛn.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'SÌ, desasignar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {  // Cambia 'result.isConfirmed' a 'result.value' para versiones anteriores
            // Preparar los datos que se enviaran en la solicitud AJAX
            var data = new FormData();
            data.append('cod_asegur', cod_asegur);
            data.append('cod_tercer', cod_tercer);
            data.append('opcion', 'unassignment');  // Supongo que la opciÔøΩn es 'unassignment'

            var standa = $("#standaID").val();
            $.ajax({
                url: "../" + standa + "/transp/ajax_genera_asegur.php",
                method: 'POST',
                data: data,
                async: false,
                dataType: "json",
                contentType: false,
                processData: false,
                beforeSend: function() {
                    cargando("Realizando la desasignaciÛn. Por favor espere.");
                },
                success: function(response) {
                    if (response.status == 100) {

                        var table = $('#table').DataTable();
                        table.rows('tr[data-id="' + cod_asegur+'-'+cod_tercer + '"]').remove().draw();

                        Swal.fire(
                            'Desasignado!',
                            'La desasignaciÛn se ha realizado correctamente.',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Error!',
                            'Hubo un problema al realizar la desasignaciÛn.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error!',
                        'Hubo un problema al realizar la desasignaciÛn.',
                        'error'
                    );
                }
            });
        } else {
            // Si el usuario cancela, no se hace nada
            Swal.fire(
                'Cancelado',
                'La desasignaciÛn ha sido cancelada.',
                'error'
            );
        }
    });
}


//usada
function alertError($msj) {
    Swal.fire({
        title: 'Error!',
        text: $msj,
        type: 'error',
        confirmButtonColor: '#336600'
    })
}

function alertSuccess(msj) {
    Swal.fire({
        title: 'Registrado!',
        text: msj,
        type: 'success',
        confirmButtonColor: '#336600'
    }).then((result) => {
        if (result.value) {
            location.reload();
        }
    })
}

