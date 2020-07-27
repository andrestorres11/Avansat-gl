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
    var can_servic = trActual.find('[name="can_servic"]').first().val();
    var tot_servic = trActual.find('[name="tot_servic"]').first();
    var tip_tarifa = trActual.find('[name="tip_tarifa"]').first().val()
    tar_unitar = tar_servic.val();
    tar_unitar = tar_unitar.replace('$', '').replace(/(^\s+|\s+$)/g, '');
    total = (tar_unitar * can_servic);
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

function deleteService(elemento) {
    var trActual = $(elemento).parent('td').parent('tr');
    var cod_sersol = trActual.find('.cod_serviSolasiClass').first().val();
    Swal.fire({
        title: '¿Esta seguro que quiere eliminar el servicio?',
        text: "Este proceso no se podrá revertir",
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
                        'Nuevo Servicio Añadido',
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