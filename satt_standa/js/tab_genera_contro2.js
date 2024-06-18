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

    // $("#cod_ciudadID").chosen();

    $("#img").change(function () {
        filePreview(this);
    });

});

function filePreview(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#imgUpload').attr('src', e.target.result);
            $('#imgUpload').attr('src', e.target.result);
            // $('#uploadForm').after('<img src="'+e.target.result+'" width="450" height="300"/>');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Jquery Dependency
$(".loan-input").on("focusout", null, function() {
    var input = $(this).val();
    $(this).val(numeral(input).format('$ 000.00'));
});

$("#cod_colorxID").spectrum({
    color: "#285c00",
    preferredFormat: "hex",
});

$(".FormularioVia").validate({
    rules: {
        nom_contro: {
            required: true
        },
        cod_ciudad: {
            required: true
        },
        tipFormul: {
            required: true
        },
        val_latitu: {
            required: true
        },
        val_longit: {
            required: true
        },
        cod_colorx: {
            required: true
        },
    },
    messages: {
        nom_contro: {
            required: "Por favor escriba el nombre del control"
        },
        cod_ciudad: {
            required: "Por favor escoja una ciudad"
        },
        tipFormul: {
            required: "Seleccione tipo de puesto"
        },
        val_latitu: {
            required: "Por favor escriba la latitud"
        },
        val_longit: {
            required: "Por favor escriba la longitud"
        },
        cod_colorx: {
            required: "Seleccione un color"
        },
    },
    submitHandler: function(form) {
        almacenarDatos();
    }
});

function almacenarDatos() {
    var cod_contro = $('#cod_controID').val();
    var opcion;
    if(cod_contro==0){
        opcion = 'registrar';
    }else{
        opcion = 'editar';
    }

    var standa = 'satt_standa';
    var dataString = 'option=' + opcion + '&cod_contro=' + cod_contro;

    $.ajax({
        url: "../" + standa + "/pcontr/ajax_genera_contro.php?" + dataString,
        method: 'POST',
        data: $(".FormularioVia").serialize(),
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

