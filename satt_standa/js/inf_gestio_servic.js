function cargarTarifaIndividual(elemento) {
    var trActual = $(elemento).parent('td').parent('tr');
    var cod_servic = trActual.find('.cod_serviClass').first().val();
    var tip_tarifa = trActual.find('[name="tip_tarifa"]').first().val();
    var tar_servic = trActual.find('.tar_servicClass').first();
    try {
        //Get data
        data = {
            cod_servic
        }
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=7',
            dataType: 'json',
            type: "post",
            data,
            async: false,
            success: function(data) {
                if (data['status'] == 200) {
                    if (tip_tarifa == 'diurna') {
                        tar_servic.val('$ ' + data['tar_diurna']);
                    } else {
                        tar_servic.val('$ ' + data['tar_noctur']);
                    }
                }
            }
        });
    } catch (error) {
        console.log(error);
    }

    // Calculo Total en base a la cantidad
    reCalculateTotal(elemento);
}

function reCalculateTotal(elemento) {
    var trActual = $(elemento).parent('td').parent('tr');
    var cod_sersol = trActual.find('.cod_serviSolasiClass').first().val();
    var tar_servic = trActual.find('.tar_servicClass').first();
    var can_servic = parseInt(trActual.find('[name="can_servic"]').first().val());
    var tot_servic = trActual.find('[name="tot_servic"]').first();
    var tip_tarifa = trActual.find('[name="tip_tarifa"]').first().val()
    tar_unitar = tar_servic.val();
    tar_unitar = parseInt(tar_unitar.replace('$', '').replace(/(^\s+|\s+$)/g, ''));
    var total = parseInt((tar_unitar * can_servic));
    console.log(total);
    tot_servic.val('$ ' + total);
    calcularTotal();
    //Actualiza los datos en la tabla
    try {
        //Get data
        data = {
            cod_sersol,
            total,
            can_servic,
            tip_tarifa
        }
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=8',
            dataType: 'json',
            type: "post",
            data,
            async: false,
            success: function(data) {
                if (data['status'] == 100) {
                    alert("Error al actualizar la base de datos");
                }
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function updateCostoProveedor(cos_provee) {
    var cod_solici = $("#cod_soliciID").val();
    try {
        //Get data
        data = {
            cos_provee,
            cod_solici
        }
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=13',
            dataType: 'json',
            type: "post",
            data,
            async: false,
            success: function(data) {
                if (data['status'] == 100) {
                    alert("Error al actualizar la base de datos");
                }
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function deleteService(elemento) {
    var trActual = $(elemento).parent('td').parent('tr');
    var cod_sersol = trActual.find('.cod_serviSolasiClass').first().val();
    Swal.fire({
        title: '�Esta seguro que quiere eliminar el servicio?',
        text: "Este proceso no se podr� revertir",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#336600',
        cancelButtonColor: '#AAA',
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            try {
                //Get data
                data = {
                    cod_sersol
                }
                $.ajax({
                    url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=9',
                    dataType: 'json',
                    type: "post",
                    data,
                    async: false,
                    success: function(data) {
                        if (data['status'] == 200) {
                            Swal.fire(
                                'Eliminado',
                                'El servicio ha sido eliminado',
                                'success'
                            )
                            $(trActual).remove();
                            calcularTotal();
                        }
                    }
                });
            } catch (error) {
                console.log(error);
            }
        }
    })
}

function addService() {
    $("#addService").empty();
    var cod_solici = $("#cod_soliciID").val();
    try {
        //Get data
        data = {
            cod_solici
        }
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=10',
            dataType: 'json',
            type: "post",
            data,
            async: false,
            success: function(data) {
                $("#addService").append(data);
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function saveNewService() {
    var cod_servic = $("#cod_servicAdd").val();
    var cod_solici = $("#cod_soliciID").val();
    try {
        //Get data
        data = {
            cod_solici,
            cod_servic
        }
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=11',
            dataType: 'json',
            type: "post",
            data,
            async: false,
            success: function(data) {
                if (data['status'] == 200) {
                    Swal.fire(
                        'Nuevo Servicio A�adido',
                        'El servicio ha sido registrado',
                        'success'
                    )
                    darServiciosPorSolicitud();
                }
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function darServiciosPorSolicitud() {
    var cod_solici = $("#cod_soliciID").val();
    try {
        //Get data
        data = {
            cod_solici
        }
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=12',
            dataType: 'json',
            type: "post",
            data,
            async: false,
            success: function(data) {
                $("#lisServicID").empty();
                $("#lisServicID").append(data);
            }
        });
    } catch (error) {
        console.log(error);
    }
    calcularTotal();
}

function calcularTotal() {
    var total = 0;
    $('.totalServic').each(function() {
        valor = $(this).val().replace('$', '').replace(/(^\s+|\s+$)/g, '');
        console.log(valor);
        total += Number(valor);
    });
    $('#val_facturID').val("$ " + total);
    llenarRetabilidad();
}

/* -------- Edicion de Trayecto -------- */

function busquedaCiudad(campo) {
    var standa = 'satt_standa';
    var key = $(campo).val();
    var opcion = 'consulta_ciudades';
    var dataString = 'key=' + key + '&opcion=' + opcion;
    var nameid = $(campo).attr('id');
    $.ajax({
        url: "../" + standa + "/asicar/ajax_asiste_carret.php",
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

                var ciu_origen = $("#ciu_origen");
                var ciu_destin = $("#ciu_destin");

                if (ciu_origen.val() != "" && ciu_destin.val()) {
                    consultaCostoTrayecto(ciu_origen.val(), ciu_destin.val());
                }

                return false;
            });
        }
    });
}

function consultaCostoTrayecto(ciu_origen, ciu_destin) {
    var standa = 'satt_standa';
    var parametros = "opcion=busqueda_costoAcompa";
    data = {
        ciu_origen,
        ciu_destin
    }
    $.ajax({
        url: "../" + standa + "/asicar/ajax_asiste_carret.php?" + parametros,
        method: 'POST',
        data,
        async: false,
        dataType: "json",
        beforeSend: function() {
            $.blockUI({ message: 'Cargando... Por favor espere' });
        },
        success: function(data) {
            if (data['validacion']) {
                $("#tot_servicEditTrayec").val("$ " + data['val_tarifa']);
            } else {
                alert("Tarifa no encontrada. Intente con otros valores");
                $("#ciu_origen").val("");
                $("#ciu_destin").val("");
                $("#tot_servicEditTrayec").val("$ 0");
            }
        },
        complete: function() {
            $.unblockUI();
        }
    });
}

function vaciaInput(elemento) {
    $(elemento).val('');
}

function editTrayect() {
    $("#editTrayectServic").css("display", "table-row");
}

function saveEditTrayect() {
    var ciu_origen = $("#ciu_origen").val();
    var ciu_destin = $("#ciu_destin").val();
    var cod_servic = $("#cod_sertra").val();
    var cod_solici = $("#cod_soliciID").val();
    if (ciu_origen != "" && ciu_destin) {
        try {
            //Get data
            data = {
                ciu_origen,
                ciu_destin,
                cod_servic,
                cod_solici
            }
            $.ajax({
                url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=15',
                dataType: 'json',
                type: "post",
                data,
                async: false,
                success: function(data) {
                    if (data['status'] == 200) {
                        Swal.fire(
                            'Edicion Exitosa',
                            'El trayecto ha sido modificado, el proceso ha sido registrado en la bitacora',
                            'success'
                        )
                        darServiciosPorSolicitud();
                    }
                }
            });
        } catch (error) {
            console.log(error);
        }
    } else {
        alert("complete los datos");
    }
}