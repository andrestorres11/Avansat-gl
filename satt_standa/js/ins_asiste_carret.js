loadAjax('start');


function loadAjax(x) {
    try {
        if (x == "start") {
            $.blockUI({ message: '<div>Espere un momento</div>' });
        } else {
            $.unblockUI();
        }
    } catch (error) {
        console.log(error);
    }

}


$(document).ready(function() {
    $.unblockUI();
});
/////////////////////////////////////////////////////////////////////////////////////////////

function cargaFormulario(valor) {
    $("#con-formul").empty();
    $(".btn-sm").attr('disabled', true);
    if (valor.value == 1) {
        desbloqueaInputs();
        $("#con-formul").append(`<div class="card text-center" style="margin:15px;">
        <div class="card-header color-heading">
                      Ubicación del Vehí­culo
                    </div>
        <div class="card-body">
          <div class="row">
            <div class="offset-1 col-3">
              <input class="form-control form-control-sm" type="text" placeholder="Url Operador GPS" id="url_opegpsID" name="url_opegps">
              </div>
              <div class="col-3">
                <input class="form-control form-control-sm" type="text" placeholder="Operador GPS" id="nom_opegpsID" name="nom_opegps">
                </div>
                <div class="col-4">
                  <input class="form-control form-control-sm" type="text" placeholder="Usuario" id="nom_usuariID" name="nom_usuari">
                  </div>
                </div>
                <div class="row mt-3">
                  <div class="offset-1 col-3">
                    <input class="form-control form-control-sm" type="text" placeholder="Contraseña" id="con_vehicuID" name="con_vehicu">
                    </div>
                    <div class="col-4">
                      <input class="form-control form-control-sm" type="text" placeholder="Ubicación" id="ubi_vehicuID" name="ubi_vehicu">
                      </div>
                      <div class="col-3">
                        <input class="form-control form-control-sm" type="text" placeholder="Punto de Referencia" id="pun_refereID" name="pun_refere">
                        </div>
                      </div>
                      <div class="row mt-3">
                        <div class="offset-1 col-10">
                          <textarea class="form-control" id="des_asisteID" name="des_asiste" rows="3" placeholder="Breve descripción de la asistencia"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>`);
    } else if (valor.value == 2) {
        desbloqueaInputs();
        $("#con-formul").append(`<div class="card text-center" style="margin:15px;">
        <div class="card-header color-heading">
        Trayecto del servicio
        </div>
      <div class="card-body">

        <div class="row">
          <div class="offset-1 col-3">
            <input class="form-control form-control-sm" type="text" placeholder="Fecha y hora del servicio" id="fec_servicID" name="fec_servic" required>
          </div>
          <div class="col-3">
            <input class="form-control form-control-sm" type="text" placeholder="Ciudad de Origen" id="ciu_origen" name="ciu_origen" onkeyup="busquedaCiudad(this)" autocomplete="off" required>
            <div id="ciu_origen-suggestions" class="suggestions"></div>
          </div>
          <div class="col-4">
            <input class="form-control form-control-sm" type="text" placeholder="Dirección" id="dir_ciuoriID" name="dir_ciuori" required>
          </div>
        </div>

        <div class="row mt-3">
          <div class="offset-4 col-3">
            <input class="form-control form-control-sm" type="text" placeholder="Ciudad de Destino" id="ciu_destin" name="ciu_destin" onkeyup="busquedaCiudad(this)" autocomplete="off" required>
            <div id="ciu_destin-suggestions" class="suggestions"></div>
          </div>
          <div class="col-4">
            <input class="form-control form-control-sm" type="text" placeholder="Dirección" id="dir_ciudesID" name="dir_ciudes" required>
          </div>
        </div>
        <hr>
        <div class="row mt-3">
            <label for="inputPassword" class="offset-7 col-2 col-form-label"><h5 class="text-label-big">Costo del Servicio</h5></label>
            <div class="col-2">
              <input type="text" class="form-control form-control-sm" id="tar_acompa" name="tar_acompa" placeholder="$ 0.00" disabled>
            </div>
        </div>

        <div class="row mt-3">
                        <div class="offset-1 col-10">
                          <textarea class="form-control" id="obs_acompaID" name="obs_acompa" rows="3" placeholder="Observaciones" required></textarea>
                        </div>
                      </div>

      </div>
      </div>`);

        $('#fec_servicID').datetimepicker({
            sideBySide: true,
            icons: {
                up: "fa fa-chevron-circle-up",
                down: "fa fa-chevron-circle-down",
                next: 'fa fa-chevron-circle-right',
                previous: 'fa fa-chevron-circle-left'
            }
        });

        $("#ciu_origen").on('click', function() {
            $("#ciu_origen").val("");
        });
        $("#ciu_destin").on('click', function() {
            $("#ciu_destin").val("");
        });

    }
    traeServicios(valor.value);
}

function traeServicios(cod_tipAsi) {
    var standa = 'satt_standa';
    var parametros = "opcion=dar_serviciosAsistencia";
    data = {
        cod_tipAsi
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
            $("#ser_asiten").empty();
            $("#ser_asiten").append(data);
        },
        complete: function() {
            $.unblockUI();
        }
    });
}

function desbloqueaInputs() {
    $(".form-control-sm").removeAttr("disabled");
    $(".btn-sm").removeAttr("disabled");
    $("#tip_soliciID").val($("#tipFormulID").val());
}


$("#FormularioSolici").validate({
    rules: {
        tipFormul: {
            required: true
        },
        nom_solici: {
            required: true
        },
        ema_solici: {
            required: true,
            email: true
        },
        tel_solici: {
            number: true
        },
        cel_solici: {
            required: true,
            number: true
        },
        num_transp: {
            required: true,
            number: true
        },
        nom_transp: {
            required: true
        },
        ap1_transp: {
            required: true
        },
        ce1_transp: {
            required: true,
            number: true
        },
        num_placax: {
            required: true
        },
        nom_marcax: {
            required: true
        },
        nom_colorx: {
            required: true
        }
    },
    messages: {
        tipFormul: {
            required: "Por favor seleccione el tipo de solicitud"
        },
        nom_solici: {
            required: "Por favor digite el nombre del solicitante"
        },
        ema_solici: {
            required: "Por favor ingrese su email",
            email: "Por favor ingrese un email valido"
        },
        tel_solici: {
            number: "Solo son permitidos numeros"
        },
        cel_solici: {
            required: "Ingrese un numero de celular",
            number: "Solo se permiten ingresar numeros"
        },
        num_transp: {
            required: "Ingrese el numero de documento del transportador",
            number: "Solo se permiten ingresar numeros"
        },
        nom_transp: {
            required: "Ingrese el nombre del transportista"
        },
        ap1_transp: {
            required: "Ingrese el Apellido del transportista"
        },
        ce1_transp: {
            required: "Ingrese el numero de celular del transportista",
            number: "Solo se permiten ingresar numeros"
        },
        num_placax: {
            required: "Ingrese el numero de la placa del vehiculo"
        },
        nom_marcax: {
            required: "Ingrese la marca"
        },
        nom_colorx: {
            required: "Ingrese el color"
        }
    },
    submitHandler: function(form) {
        almacenarDatos();
    }
});


//Busqueda de Informacion

//Busqueda Transportista
$("#num_transpID").on('focusout', function() {
    var codigo = $("#num_transpID").val();
    busquedaDatosTransportista(codigo);
});

$("#num_placaID").on('focusout', function() {
    var placa = $("#num_placaID").val();
    busquedaDatosVehiculo(placa);
});


function busquedaDatosTransportista(cod_conduc) {
    var standa = 'satt_standa';
    var parametros = "opcion=busqueda_transportador";
    data = {
        cod_conduc
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
                $("#nom_transpID").val(data['nom_transp']);
                $("#ap1_transpID").val(data['ap1_transp']);
                $("#ap2_transpID").val(data['ap2_transp']);
                $("#ce1_transpID").val(data['ce1_transp']);
            }
        },
        complete: function() {
            $.unblockUI();
        }
    });
}

function busquedaDatosVehiculo(placa) {
    var parametros = "opcion=busqueda_vehiculo";
    var standa = 'satt_standa';
    data = {
        placa
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
                $("#nom_marcaxID").val(data['nom_marcax']);
                $("#nom_colorxID").val(data['nom_colorx']);
            }
        },
        complete: function() {
            $.unblockUI();
        }
    });
}

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
                console.log("ejecutando");
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
                $("#tar_acompa").val("$ " + data['val_tarifa']);
            } else {
                alert("Tarifa no encontrada. Intente con otros valores");
                $("#ciu_origen").val("");
                $("#ciu_destin").val("");
            }
        },
        complete: function() {
            $.unblockUI();
        }
    });
}

function almacenarDatos() {
    var standa = 'satt_standa';
    var opcion = 'registrar';
    var dataString = 'opcion=' + opcion;
    var services = JSON.stringify(recorgeServicio());
    $.ajax({
        url: "../" + standa + "/asicar/ajax_asiste_carret.php?" + dataString,
        method: 'POST',
        data: $("#FormularioSolici").serialize() + "&services=" + services,
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

function recorgeServicio() {
    var ServiciosSeleccionados = [];
    $('input[type=checkbox]:checked').each(function() {
        ServiciosSeleccionados.push({ "servicio": $(this).val() });
    });
    return ServiciosSeleccionados;
}