$(document).ready(function() {
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


    function validaFechaIngDes() {
        var fecha1 = new Date($("#fec_ingdes").val());
        var fecha2 = new Date($("#fec_cumdes").val());
        var hora1 = moment($("#hor_ingdes").val(), 'H:mm:ss');
        var hora2 = moment($("#hor_cumdes").val(), 'H:mm:ss');;
        if (fecha1 < fecha2) {
            $("#fec_ingdes").val('');
            showError("La fecha de entrada debe ser mayor a la fecha de llegada");
        }
        if (hora1 < hora2) {
            $("#hor_ingdes").val('');
            showError("La hora de entrada debe ser mayor a la hora de llegada");
        }
    }

    function validaFechaSalDes() {
        var fecha1 = new Date($("#fec_saldes").val());
        var fecha2 = new Date($("#fec_ingdes").val());
        var hora1 = moment($("#hor_saldes").val(), 'H:mm:ss');
        var hora2 = moment($("#hor_ingdes").val(), 'H:mm:ss');;
        if (fecha1 < fecha2) {
            $("#fec_saldes").val('');
            showError("La fecha de salida debe ser mayor a la fecha de entrada");
        }
        if (hora1 < hora2) {
            $("#hor_saldes").val('');
            showError("La hora de salida debe ser mayor a la hora de entrada");
        }

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


});