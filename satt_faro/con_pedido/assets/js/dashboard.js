$(document).ready(function() {

    $("#fec_cumdes").on("change keyup paste click", function() {
        validaFechaCumdes();

    });
    $("#hor_cumdes").on("change keyup paste click", function() {
        validaFechaCumdes();

    });

    $("#fec_ingdes").on("change keyup paste click", function() {
        if (validaDigigenci1()) {
            validaFechaIngDes();
        } else {
            showError("debe diligenciar los datos relacionados a la hora y fecha de llegada");
            $(this).val('');
        }

    });
    $("#hor_ingdes").on("change keyup paste click", function() {
        if (validaDigigenci1()) {
            validaFechaIngDes();
        } else {
            showError("debe diligenciar los datos relacionados a la hora y fecha de llegada");
            $(this).val('');
        }
    });
    $("#fec_saldes").on("change keyup paste click", function() {

        if (validaDigigenci2()) {
            validaFechaSalDes();
        } else {
            showError("debe diligenciar los datos relacionados a la hora y fecha de entrada");
            $(this).val('');
        }


    });
    $("#hor_saldes").on("change keyup paste click", function() {
        if (validaDigigenci2()) {
            validaFechaSalDes();
        } else {
            showError("debe diligenciar los datos relacionados a la hora y fecha de entrada");
            $(this).val('');
        }
    });

    function validaFechaCumdes() {
        var fecha1 = new Date($("#fec_ingdes").val());
        var fecha2 = new Date($("#fec_saldes").val());
        var fecha3 = new Date($("#fec_cumdes").val());
        var hora1 = moment($("#hor_ingdes").val(), 'H:mm:ss');
        var hora2 = moment($("#hor_saldes").val(), 'H:mm:ss');
        var hora3 = moment($("#hor_cumdes").val(), 'H:mm:ss');

        //remove
        var fechaRem1 = moment(fecha1).subtract(15, 'minutes').format('YYYY-MM-DD');
        var horaRem1 = moment(hora1).subtract(15, 'minutes');

        if (fecha3 > fecha2 && fecha3 > fecha1) {
            $("#fec_cumdes").val('');
            showError("La fecha de llegada debe ser menor a la fecha de entrada y de salida");
        }
        if (hora3 > hora2 && hora3 > hora1) {
            $("#hor_cumdes").val('');
            showError("La hora de llegada debe ser menor a la fecha de entrada y de salida");
        }

        if (fecha1 != '' && hora1 != '') {
            if (hora3 > horaRem1) {
                $("#hor_ingdes").val('');
                showError("La hora de llegada debe ser menor 15 minutos con respecto a la hora de entrada");
            }
        }
        validateDisabledBtn();
    }

    function validaFechaIngDes() {
        var fecha1 = new Date($("#fec_ingdes").val());
        var fecha2 = new Date($("#fec_cumdes").val());
        var hora1 = moment($("#hor_ingdes").val(), 'H:mm:ss');
        var hora2 = moment($("#hor_cumdes").val(), 'H:mm:ss');

        var fecha3 = new Date($("#fec_saldes").val());
        var hora3 = moment($("#hor_saldes").val(), 'H:mm:ss');

        //remove
        var fechaRem3 = moment(fecha3).subtract(15, 'minutes').format('YYYY-MM-DD');
        var horaRem3 = moment(hora3).subtract(15, 'minutes');

        //Se aÃ±aden 15 minutos de diferencia a hora y fecha para la comparacion

        var fechaAdd = moment(fecha2).add(15, 'minutes').format('YYYY-MM-DD');
        var horaAdd = moment(hora2).add(15, 'minutes');

        if (fecha1 < fecha2) {
            $("#fec_ingdes").val('');
            showError("La fecha de entrada debe ser mayor a la fecha de llegada");
        }
        if (hora1 < hora2) {
            $("#hor_ingdes").val('');
            showError("La hora de entrada debe ser mayor a la hora de llegada");
        }

        //Restriccion 15 minutos
        if (fecha1 < fechaAdd) {
            $("#fec_ingdes").val('');
            showError("La fecha de entrada debe ser mayor 15 minutos con respecto a la hora de llegada");
        }

        //Restriccion 15 minutos
        if (hora1 < horaAdd) {
            $("#hor_ingdes").val('');
            showError("La hora de entrada debe ser mayor 15 minutos con respecto a la hora de llegada");
        }

        //Validacion Extra;
        if (fecha3 != '' && hora3 != '') {
            if (hora1 > horaRem3) {
                $("#hor_ingdes").val('');
                showError("La hora de entrada debe ser menor 15 minutos con respecto a la hora de salida");
            }
        }

        validateDisabledBtn();
    }

    function validaFechaSalDes() {
        var fecha1 = new Date($("#fec_saldes").val());
        var fecha2 = new Date($("#fec_ingdes").val());
        var hora1 = moment($("#hor_saldes").val(), 'H:mm:ss');
        var hora2 = moment($("#hor_ingdes").val(), 'H:mm:ss');

        //Se añaden 15 minutos de diferencia a hora y fecha para la comparacion

        var fechaAdd = moment(fecha2).add(15, 'minutes').format('YYYY-MM-DD');
        var horaAdd = moment(hora2).add(15, 'minutes');

        if (fecha1 < fecha2) {
            $("#fec_saldes").val('');
            showError("La fecha de salida debe ser mayor a la fecha de entrada");
        }
        if (hora1 < hora2) {
            $("#hor_saldes").val('');
            showError("La hora de salida debe ser mayor a la hora de entrada");
        }

        //Restriccion 15 minutos
        if (fecha1 < fechaAdd) {
            $("#fec_saldes").val('');
            showError("La fecha de entrada debe ser mayor 15 minutos con respecto a la hora de entrada");
        }

        //Restriccion 15 minutos
        if (hora1 < horaAdd) {
            $("#hor_saldes").val('');
            showError("La hora de entrada debe ser mayor 15 minutos con respecto a la hora de entrada");
        }

        validateDisabledBtn();

    }

    function validaDigigenci1() {
        if ($("#fec_cumdes").val() != '' && $("#hor_cumdes").val()) {
            return true;
        }
        return false;
    }

    function validaDigigenci2() {
        if ($("#fec_ingdes").val() != '' && $("#hor_ingdes").val()) {
            return true;
        }
        return false;
    }

    function showError(msj) {
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: msj,
        })
    }


    $("#buttonTimeLogistics").on("click", function() {
        Swal.fire({
            title: 'Confirmar',
            text: "¿Esta seguro de almacenar los datos?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $("#timeLogisticsForm").submit();
            }
        })
    });


    function validateDisabledBtn() {
        fecha1 = $("#fec_cumdes").val();
        hora1 = $("#hor_cumdes").val();
        fecha2 = $("#fec_ingdes").val();
        hora2 = $("#hor_ingdes").val();
        fecha3 = $("#fec_saldes").val();
        hora3 = $("#hor_saldes").val();
        if (fecha1 != '' && hora1 != '' && fecha2 != '' && hora2 != '' && fecha3 != '' && hora3 != '') {
            $("#buttonTimeLogistics").prop('disabled', false);
        } else {
            $("#buttonTimeLogistics").prop('disabled', true);
        }
    }


    function validateGeneral() {
        fecha1 = $("#fec_cumdes").val();
        hora1 = $("#hor_cumdes").val();
        fecha2 = $("#fec_ingdes").val();
        hora2 = $("#hor_ingdes").val();
        fecha3 = $("#fec_saldes").val();
        hora3 = $("#hor_saldes").val();

        //Add
        var fechaAdd2 = moment(fecha2).add(15, 'minutes').format('YYYY-MM-DD');
        var horaAdd2 = moment(hora2).add(15, 'minutes');

        //remove
        var fechaRem3 = moment(fecha2).subtract(15, 'minutes').format('YYYY-MM-DD');
        var horaRem3 = moment(hora2).subtract(15, 'minutes');

        if (fecha1 != '' && hora1 != '' && fecha2 != '' && hora2 != '' && fecha3 != '' && hora3 != '') {

            if (fecha2 > fecha3) {
                showError("La fecha de entrada debe ser menor 15 minutos correspondiente a la fecha de salida");
                $("#fec_ingdes").val('');
            }

            if (hora2 > horaRem3) {
                showError("La hora de entrada debe ser menor 15 minutos correspondiente a la hora de salida");
                $("#hor_ingdes").val('');
            }






            if (fecha1 > fecha2) {
                showError("Error validate");
            }
            if (hora1 > hora2) {
                showError("Error validate");
            }
            if (fecha1 > fecha3) {
                showError("Error validate");
            }
            if (hora1 > hora2) {
                showError("Error validate");
            }
            if (fecha2 < fecha1) {
                showError("Error validate");
            }
            if (hora2 < hora1) {
                showError("Error validate");
            }
            if (fecha3 < fecha2) {
                showError("Error validate");
            }
            if (hora3 < hora2) {
                showError("Error validate");
            }

        }
    }


});