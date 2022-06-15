cargando("Cargando Pagina");

$(document).ready(function() {
    //Inicializacion de indicadores
    cambioIndicadores($('input[name="pregu1con"]:checked'));
    cambioIndicadores($('input[name="pre_comveh"]:checked'));
    cambioIndicadores($('input[name="pregu2con"]:checked'));
    cambioIndicadores($('input[name="ind_preres"]:checked'));
    //executeFilter();
    inicializarTablas();
    InsSoliciValidate();
    swal.close();

    $("#bus_transp").click(function() {
        vaciaInputTransportadora(this)
    });
});

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

function asignaTransportadora(campo) {
    var standa = 'satt_standa';
    var key = $(campo).val();
    var opcion = 'consulta_transportadoras';
    var dataString = 'key=' + key + '&opcion=' + opcion;
    var nameid = $(campo).attr('id');
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php",
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

                var opcion = 'getInfoTransportadora';
                dataString = 'cod_transp=' + id + '&opcion=' + opcion;

                $.ajax({
                    url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
                    method: 'POST',
                    async: false,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $("#nom_soliciID").val(data['nom_tercer']);
                        $("#cor_soliciID").val(data['dir_emailx']);
                        $("#tel_soliciID").val(data['num_telefo']);
                        $("#cel_soliciID").val(data['num_telmov']);
                        $("#cod_transp").val(data['cod_tercer']);
                        $("#nom_soliciID").removeAttr("disabled");
                        $("#cor_soliciID").removeAttr("disabled");
                        $("#tel_soliciID").removeAttr("disabled");
                        $("#cel_soliciID").removeAttr("disabled");
                    },
                });

                //Hacemos desaparecer el resto de sugerencias
                $('#' + nameid + '-suggestions').fadeOut(1000);
                return false;
            });
        }
    });
}

function vaciaInputTransportadora(campo) {
    $(campo).val('');
    $("#nom_soliciID").val('');
    $("#cor_soliciID").val('');
    $("#tel_soliciID").val('');
    $("#cel_soliciID").val('');
    $("#cod_transp").val('');
    $("#nom_soliciID").attr("disabled", true);
    $("#cor_soliciID").attr("disabled", true);
    $("#tel_soliciID").attr("disabled", true);
    $("#cel_soliciID").attr("disabled", true);
}

function generaNuevoEstudio() {
    var cantid = $(".sol_estseg").length + 1;
    var incrme = cantid - 1;
    var standa = 'satt_standa';
    var dataString = 'opcion=darTipoDocumento';
    var tipos_documento;
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        async: false,
        dataType: 'html',
        success: function(data) {
            tipos_documento = data;
        },
    });

    $("#elements-study-seg").append(`
    <div class="card sol_estseg" style="margin:15px;">
    <div class="card-header text-center color-heading">
      Estudio de Seguridad No. ` + cantid + `
    </div>
  <div class="card-body">
    <div class="row">
      <div class="col-4 form-group">
        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Tipo de documento Conductor:</label>
        <select class="form-control form-control-sm req" id="tip_documeID` + incrme + `" name="tip_docume[` + incrme + `]">
          ` + tipos_documento + `
        </select>
      </div>
      <div class="col-4 form-group">
        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>N� de documento Conductor:</label>
        <input class="form-control form-control-sm req num" type="text" placeholder="N� de documento" id="num_documeID` + incrme + `" name="num_docume[` + incrme + `]" required>
      </div>
      <div class="col-4 form-group">
      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Nombres del Conductor:</label>
      <input class="form-control form-control-sm req" type="text" placeholder="Nombres" id="nom_personID` + incrme + `" name="nom_person[` + incrme + `]" required>
      </div>
    </div>
    <div class="row">
      <div class="col-4 form-group">
        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Primer apellido del Conductor:</label>
        <input class="form-control form-control-sm req" type="text" placeholder="Primer apellido" id="nom_apell1ID` + incrme + `" name="nom_apell1[` + incrme + `]" required>
      </div>
      <div class="col-4 form-group">
        <label for="nom_soliciID" class="labelinput">Segundo apellido del Conductor:</label>
        <input class="form-control form-control-sm" type="text" placeholder="Segundo apellido" id="nom_apell2ID` + cantid + `" name="nom_apell2[` + incrme + `]">
      </div>
      <div class="col-4 form-group">
      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>N� de celular del Conductor:</label>
      <input class="form-control form-control-sm req" type="text" placeholder="N� de celular" id="num_telmovID` + cantid + `" name="num_telmov[` + incrme + `]" required>
      </div>
    </div>
    <div class="row">
      <div class="col-4 form-group">
        <label for="nom_soliciID" class="labelinput">N� de celular 2 del Conductor:</label>
        <input class="form-control form-control-sm" type="text" placeholder="N� de celular 2" id="num_telmo2ID` + cantid + `" name="num_telmo2[` + incrme + `]">
      </div>
      <div class="col-5 form-group">
        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Email del Conductor:</label>
        <input class="form-control form-control-sm req ema" type="text" placeholder="Email" id="dir_emailxID` + cantid + `" name="dir_emailx[` + incrme + `]" required>
      </div>
      <div class="col-3 form-group">
        <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>Placa:</label>
        <input class="form-control form-control-sm req min6max6" type="text" placeholder="Placa" id="num_placaxID` + cantid + `" name="num_placax[` + incrme + `]" required>
      </div>
    </div>
    <div class="row">
        <div class="col-12 text-right">
            <button type="button" class="btn btn-danger btn-sm" onclick="borrarEstudio(this)"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
        </div>
    </div>
  </div>
  </div>`);
}

function borrarEstudio(elemento) {
    $(elemento).parent().parent().parent().parent().remove();
}

function validateInicial() {
    $('#InsSolici').removeData('validator');
    var validator = $("#InsSolici").validate({
        ignore: []
    });

    $('.req').each(function() {
        $(this).rules("add", {
            required: true,
            messages: {
                required: "Este campo es requerido"
            }
        });
    });

    $('.num').each(function() {
        $(this).rules("add", {
            number: true,
            messages: {
                number: "Solo se aceptan numeros"
            }
        })
    });

    $('.ema').each(function() {
        $(this).rules("add", {
            email: true,
            messages: {
                email: "El correo no es v�lido"
            }
        })
    });

    $('.min6max6').each(function() {
        $(this).rules("add", {
            minlength: 6,
            maxlength: 6,
            messages: {
                minlength: "Debe tener almenos 6 caracteres",
                maxlength: "Solo es permitido maximo 6 caracteres",
            }
        })
    });

    if ($("#InsSolici").valid()) {
        almacenarSolicitud();
    }
}
//VALIDACIONES FORMULARIOS

//Validacion de formulario de solicitud

function InsSoliciValidate() {
    $("#InsSolici").validate({
        rules: {
            'tip_docume[]': {
                required: true
            },
            'num_docume[]': {
                required: true,
                number: true,
            },
            nom_person: {
                required: true
            },
            nom_apell1: {
                required: true
            },
            num_telmov: {
                required: true
            },
            dir_emailx: {
                required: true,
                email: true,
            },
            num_placax: {
                required: true,
                minlength: 6,
                maxlength: 6
            }
        },
        messages: {
            tip_docume: {
                required: "El tipo de documento es requerido"
            },
            num_docume: {
                required: "El n�mero de documento es requerido",
                number: "Solo se aceptan n�meros"
            },
            nom_person: {
                required: "El nombre del conductor es requerido"
            },
            nom_apell1: {
                required: "El apellido del conductor es requerido"
            },
            num_telmov: {
                required: "El numero de celular requerido"
            },
            dir_emailx: {
                required: "La direccion de correo es requerido",
                email: "El correo no es v�lido"
            },
            num_placax: {
                minlength: "Debe tener almenos 6 caracteres",
                maxlength: "Solo es permitido maximo 6 caracteres",
                required: "La placa del vehiculo es requerida"
            }
        },
        submitHandler: function(form) {
            almacenarSolicitud(form);
        }
    });
}

function addReq(elemento) {
    $(elemento + ' input').each(function() {
        if ($(this).attr('sol')) {
            $(this).addClass('req');
        }
    });

    $(elemento + ' select').each(function() {
        if ($(this).attr('sol')) {
            $(this).addClass('req');
        }
    });

    $(elemento + ' input[type="file"]').each(function() {
        $(this).addClass('ncarg');
    });
}

function removeReq(elemento) {
    $(elemento + ' input').each(function() {
        if ($(this).attr('sol')) {
            $(this).removeClass('req error');
            $(this).removeAttr('aria-invalid');
            $('#' + $(this).attr('id') + '-error').remove();
        }
    });
    $(elemento + ' select').each(function() {
        if ($(this).attr('sol')) {
            $(this).removeClass('req error');
            $(this).removeAttr('aria-invalid');
            $('#' + $(this).attr('id') + '-error').remove();
        }
    });

    $(elemento + ' input[type="file"]').each(function() {
        $(this).removeClass('docreq error');
        $(this).removeAttr('aria-invalid');
        $('#' + $(this).attr('id') + '-error').remove();
    });

}

function validateFase1() {
    $('#dataSolicitud').removeData('validator');
    var validator = $("#dataSolicitud").validate({
        ignore: []
    });

    $('#dataSolicitud #pills-documentos .ncarg').each(function() {
        $(this).rules("add", {
            required: true,
            extension: "pdf|jpg|png|docx",
            messages: {
                extension: "Formato no aceptado",
                required: "Debe adjuntar el documento"
            }
        });
    });

    if ($("#dataSolicitud").valid()) {
        almacenarFase1();
    }
}

function traeLineas(elemento) {
    var standa = 'satt_standa';
    var dataString = 'opcion=getLineas&cod_marcax=' + $(elemento).val();
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        async: false,
        dataType: 'html',
        success: function(data) {
            $("#cod_lineaxID").empty();
            $("#cod_lineaxID").append(data);
        },
    });
}

function validateEstudioSoliciFinal() {

    if ($('#check_conposeed').is(':checked')) {
        removeReq('#pills-poseedor');
    } else {
        addReq('#pills-poseedor');
    }

    if ($('#check_conpropiet').is(':checked')) {
        removeReq('#pills-propietario');
        $('#check_pospropiet').attr('checked', true);
    } else {
        addReq('#pills-propietario');
    }

    if ($('#check_pospropiet').is(':checked')) {
        removeReq('#pills-propietario');
    } else {
        addReq('#pills-propietario');
    }

    $('#dataSolicitud').removeData('validator');
    var validator = $("#dataSolicitud").validate({
        ignore: []
    });

    $('#dataSolicitud .docreq').each(function() {
        $(this).rules("add", {
            required: true,
            extension: "jpg|png|jpeg",
            messages: {
                extension: "Formato no aceptado",
                required: "Debe adjuntar el documento"
            }
        });
    });

    $('#dataSolicitud .req').each(function() {
        $(this).rules("add", {
            required: true,
            messages: {
                required: "Este campo es requerido"
            }
        });
    });
    $('#dataSolicitud .num').each(function() {
        $(this).rules("add", {
            number: true,
            messages: {
                number: "Solo se aceptan numeros"
            }
        })
    });
    $('#dataSolicitud .ema').each(function() {
        $(this).rules("add", {
            email: true,
            messages: {
                email: "El correo no es v�lido"
            }
        })
    });
    $("#dataSolicitud").valid();
    var ele = $("#dataSolicitud :input.error:first");
    if (ele.is(':hidden')) {
        var tabToShow = ele.closest('.tab-pane');
        if (tabToShow.attr('id') != '"pills-vehiculo') {
            $("#" + tabToShow.attr('id') + "-tab").addClass("active");
            $("#" + tabToShow.attr('id')).addClass("active");
            $("#" + tabToShow.attr('id')).addClass("show");
            $("#pills-vehiculo-tab").removeClass("active");
            $("#pills-vehiculo").removeClass("show");
            $("#pills-vehiculo").removeClass("active");
        }
    }

    if ($("#dataSolicitud").valid()) {
        almacenarEstudioFinal();
    }

}

function consInfoSolicitud(elemento) {
    var cod_solici = $(elemento).attr('data-dato');
    var standa = 'satt_standa';
    var dataString = 'opcion=armaProcesoSolicitud&cod_solici=' + cod_solici;
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        async: false,
        dataType: 'html',
        success: function(data) {
            $("#title-modal-procSol").empty();
            $("#title-modal-procSol").append('Proceso estudio de seguridad No. ' + cod_solici);
            $("#cont_procesoSolicitudModal").empty();
            $("#cont_procesoSolicitudModal").append(data);
            $("#procesoSolicitudModal").modal("show");
        },
    });
}

function registFormDinamic(cod_person, tip_refere, cod_identi) {
    var key = cod_person + "_" + tip_refere + "_" + cod_identi;

    $('#dataSolicitud').removeData('validator');
    var validator = $("#dataSolicitud").validate({
        ignore: []
    });

    if (tip_refere != 'L') {
        var divId = '#InsReferenceFyP_' + key;
        $('#dataSolicitud ' + divId + ' .Req_ReferenceFyP').each(function() {
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "Este campo es requerido"
                }
            });
            console.log($(this).attr('id'));
        });
        if ($("#dataSolicitud").valid()) {
            almacenarReferencia(key);
        }
    } else {
        var divId = '#InsReferenceLaboral_' + key;
        $('#dataSolicitud ' + divId + ' .Req_ReferenceLaboral').each(function() {
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "Este campo es requerido"
                }
            });
        });

        if ($("#dataSolicitud").valid()) {
            almacenarReferenciaLaboral(key);
        }

    }
}

function llenaParentesco(elemento, cod_person, tip_refere, cod_identi) {
    var cod_parent = $(elemento).val();
    var standa = 'satt_standa';
    var dataString = 'opcion=getParentesco&cod_parent=' + cod_parent;
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function(data) {
            $("#nom_parentID_" + cod_person + "_" + tip_refere + "_" + cod_identi).val(data['nom_parent']);
        },
    });

    if (cod_parent == 10) {
        $("#nom_parentID_" + cod_person + "_" + tip_refere + "_" + cod_identi).val('');
        $("#div-cual-input_" + cod_person + "_" + tip_refere + "_" + cod_identi).css('display', 'inline');
    } else {
        $("#div-cual-input_" + cod_person + "_" + tip_refere + "_" + cod_identi).css('display', 'none');
    }

}

function guardarGPS() {
    $('#regOpeGPS').removeData('validator');
    var validator = $("#regOpeGPS").validate({
        ignore: []
    });

    $('#regOpeGPS .req').each(function() {
        $(this).rules("add", {
            required: true,
            messages: {
                required: "Este campo es requerido"
            }
        });
    });

    if ($("#regOpeGPS").valid()) {
        registrarGPS();
    }

}

function registrarGPS() {
    var standa = 'satt_standa';
    var dataString = 'opcion=guardarGPS';
    var data = new FormData(document.getElementById('regOpeGPS'));
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        data,
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            cargando("Guardando...")
        },
        success: function(data) {
            if (data['status'] == 200) {
                $("#nom_operadID").val('');
                $("#url_gpsxxxID").val('');
                $("#nit_operadID").val('');
                $("#nit_verifiID").val('');
                $("#ind_usaidxID").prop("checked", false);
                $("#ind_cronxxID").prop("checked", false);
                $("#ind_rndcxxID").prop("checked", false);
                $("#ind_intgpsID").prop("checked", false);
                $("#cod_opegpsID").empty();
                $("#cod_opegpsID").append(data['info']);
                $("#cod_opegpsID").append(data['nuevoOpe']);
                $('#modalregOpeGps').modal('hide');
                swal.close();
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

function almacenarSolicitud() {
    var standa = 'satt_standa';
    var dataString = 'opcion=guardarSolicitud';
    var data = new FormData(document.getElementById('InsSolici'));

    data.append('nom_solici', $("#nom_soliciID").val());
    data.append('cor_solici', $("#cor_soliciID").val());
    data.append('tel_solici', $("#tel_soliciID").val());
    data.append('cel_solici', $("#cel_soliciID").val());

    if ($("#cod_transp").val() == undefined || $("#cod_transp").val() == '') {
        Swal.fire({
            title: 'Advertencia',
            text: 'No tiene una empresa transportadora asignada o disponible',
            type: 'warning',
            confirmButtonColor: '#336600'
        })
        return;
    }
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        data,
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            cargando("Creando la solicitud. Por favor espere.")
        },
        success: function(data) {
            if (data['status'] == 200) {
                $('#NuevaSolicitudModal').modal('hide');
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

function almacenarFase1() {
    var standa = 'satt_standa';
    var dataString = 'opcion=guardadoFase1';
    var data = new FormData(document.getElementById('dataSolicitud'));
    data.append('ind_guarf1', true);
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        data,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            cargando("Guardando la informaci�n. Por favor espere.");
        },
        success: function(data) {
            swal.close();
            if (data['status'] == 200) {
                Swal.fire({
                    title: 'Registrado!',
                    text: data['response'],
                    type: 'success',
                    confirmButtonColor: '#336600'
                }).then((result) => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data['response'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                });
            }
        },
    });
}

function almacenarEstudioFinal() {
    var standa = 'satt_standa';
    var dataString = 'opcion=guardado';
    var data = new FormData(document.getElementById('dataSolicitud'));
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        data,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            cargando("Guardando la informaci�n. Por favor espere.")
        },
        success: function(data) {

            if (data['status'] == 200) {

                envioArchivoFinSolici(data['cod_estseg'], data['emails'], data);




            } else {
                swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: data['response'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                });
            }
        },
    });
}


function envioArchivoFinSolici(cod_estseg, emails, information) {
    var standa = 'satt_standa';
    var dataString = 'cod_estseg=' + cod_estseg;
    $.ajax({
        url: "../" + standa + "/estseg/inf_estseg_pdfxxx.php?" + dataString,
        xhrFields: { responseType: 'blob' },
        success: function(data) {
            var blob = new Blob([data]);
            pdf = blob;
            var data = new FormData();
            data.append('file', pdf);
            data.append('emails', emails);
            data.append('cod_estseg', cod_estseg);
            var infoenv = 'opcion=sendEmail';
            $.ajax({
                data: data,
                type: "POST",
                url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + infoenv,
                cache: false,
                contentType: false,
                processData: false,
                xhrFields: { responseType: 'blob' },
                success: function(backParam) {
                    swal.close();
                    Swal.fire({
                        title: 'Registrado!',
                        text: information['response'],
                        type: 'success',
                        confirmButtonColor: '#336600'
                    }).then((result) => {
                        if (information['redire'] == 1) {
                            window.location.href = information['page'];
                        } else {
                            location.reload();
                        }
                    });
                }
            });
        }
    });
}

function cambioIndicadores(elemento) {
    var name_compl = 'com_' + $(elemento).attr('name');
    var input_compl = $('#' + name_compl);
    if ($(elemento).val() == 1) {
        input_compl.show();
    } else {
        input_compl.hide();
    }
}

function almacenarReferencia(key) {
    var standa = 'satt_standa';
    var dataString = 'opcion=insReferenciaPyF';
    var data = new FormData();
    data.append('nom_refereE', $("#nom_refereID_" + key).val());
    data.append('cod_parentE', $("#cod_parentID_" + key).val());
    data.append('dir_domiciE', $("#dir_domiciID_" + key).val());
    data.append('num_telefoE', $("#num_telefoID_" + key).val());
    data.append('nom_parentE', $("#nom_parentID_" + key).val());
    data.append('cod_personE', $("#cod_person_" + key).val());
    data.append('cod_refereE', $("#cod_refere_" + key).val());
    data.append('cod_identiE', $("#cod_identi_" + key).val());
    data.append('obs_refereE', $("#obs_refereID_" + key).val());
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        data,
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            loadAjax("start")
        },
        success: function(data) {
            if (data['status'] == 200) {
                $("#none_" + data['person'] + "_" + data['tip_refere'] + '_' + data['cod_identi']).empty();
                $("#table-referen_" + data['person'] + "_" + data['tip_refere'] + '_' + data['cod_identi']).append(`<tr>
                    <td>` + $('#nom_refereID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val() + `</td>
                    <td>` + $('#nom_parentID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val() + `</td>
                    <td>` + $('#dir_domiciID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val() + `</td>
                    <td>` + $('#num_telefoID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val() + `</td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="borrarReferenciaFyP('` + data['ultimo'] + `','` + data['person'] + `',this)"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                    </tr>`);

                $('#nom_refereID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('');
                $('#nom_parentID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('Padre');
                $('#dir_domiciID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('');
                $('#num_telefoID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('');
                $('#nom_parentID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('1');

                Swal.fire({
                    title: 'Registrado!',
                    text: data['response'],
                    type: 'success',
                    confirmButtonColor: '#336600'
                });

            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data['response'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                });
            }
        },
        complete: function() {
            loadAjax("end");
        },
    });
}

function almacenarReferenciaLaboral(key) {
    var standa = 'satt_standa';
    var dataString = 'opcion=insReferenciaLaboral';
    var data = new FormData();
    data.append('nom_transpE', $("#nom_transpID_" + key).val());
    data.append('num_telefoE', $("#num_telefoID_" + key).val());
    data.append('inf_suminiE', $("#inf_suminiID_" + key).val());
    data.append('num_viajesE', $("#num_viajesID_" + key).val());
    data.append('cod_personE', $("#cod_person_" + key).val());
    data.append('cod_refereE', $("#cod_refere_" + key).val());
    data.append('cod_identiE', $("#cod_identi_" + key).val());
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        data,
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            loadAjax("start")
        },
        success: function(data) {
            if (data['status'] == 200) {
                $("#none_" + data['person'] + "_" + data['tip_refere'] + '_' + data['cod_identi']).empty();
                $("#table-referen_" + data['person'] + "_" + data['tip_refere'] + '_' + data['cod_identi']).append(`<tr>
                    <td>` + $('#nom_transpID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val() + `</td>
                    <td>` + $('#num_telefoID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val() + `</td>
                    <td>` + $('#inf_suminiID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val() + `</td>
                    <td>` + $('#num_viajesID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val() + `</td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="borrarReferenciaLaboral('` + data['ultimo'] + `','` + data['person'] + `',this)"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                    </tr>`);

                $('#nom_transpID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('');
                $('#num_telefoID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('');
                $('#inf_suminiID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('');
                $('#num_viajesID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('');

                Swal.fire({
                    title: 'Registrado!',
                    text: data['response'],
                    type: 'success',
                    confirmButtonColor: '#336600'
                });

            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data['response'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                });
            }
        },
        complete: function() {
            loadAjax("end");
        },
    });
}

function borrarReferenciaFyP(cod_refere, cod_person, elemento) {
    var standa = 'satt_standa';
    var dataString = 'opcion=borrarReferenceFyP&cod_refere=' + cod_refere + '&cod_person=' + cod_person;
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            loadAjax("start")
        },
        success: function(data) {
            if (data['status'] == 200) {
                $(elemento).parent().parent().remove();
            }
        },
        complete: function() {
            loadAjax("end");
        },
    });
}

function borrarReferenciaLaboral(cod_refere, cod_person, elemento) {
    var standa = 'satt_standa';
    var dataString = 'opcion=borrarReferenceLaboral&cod_refere=' + cod_refere + '&cod_person=' + cod_person;
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            loadAjax("start")
        },
        success: function(data) {
            if (data['status'] == 200) {
                $(elemento).parent().parent().remove();
            }
        },
        complete: function() {
            loadAjax("end");
        },
    });
}



function changePestana(elemento) {
    if (elemento == 1) {
        $("#pills-conductor-tab").removeClass("active");
        $("#pills-conductor").removeClass("show");
        $("#pills-conductor").removeClass("active");

        $("#pills-poseedor-tab").addClass("active");
        $("#pills-poseedor").addClass("active");
        $("#pills-poseedor").addClass("show");
    } else if (elemento == 2) {
        $("#pills-poseedor-tab").removeClass("active");
        $("#pills-poseedor").removeClass("show");
        $("#pills-poseedor").removeClass("active");

        $("#pills-propietario-tab").addClass("active");
        $("#pills-propietario").addClass("active");
        $("#pills-propietario").addClass("show");
    } else if (elemento == 3) {
        $("#pills-propietario-tab").removeClass("active");
        $("#pills-propietario").removeClass("show");
        $("#pills-propietario").removeClass("active");

        $("#pills-vehiculo-tab").addClass("active");
        $("#pills-vehiculo").addClass("active");
        $("#pills-vehiculo").addClass("show");
    } else if (elemento == 0) {
        $("#pills-poseedor-tab").removeClass("active");
        $("#pills-poseedor").removeClass("show");
        $("#pills-poseedor").removeClass("active");
        $("#pills-conductor-tab").addClass("active");
        $("#pills-conductor").addClass("active");
        $("#pills-conductor").addClass("show");
    } else if (elemento == 4) {
        $("#pills-propietario-tab").removeClass("active");
        $("#pills-propietario").removeClass("show");
        $("#pills-propietario").removeClass("active");
        $("#pills-poseedor-tab").addClass("active");
        $("#pills-poseedor").addClass("active");
        $("#pills-poseedor").addClass("show");
    } else if (elemento == 5) {
        $("#pills-vehiculo-tab").removeClass("active");
        $("#pills-vehiculo").removeClass("show");
        $("#pills-vehiculo").removeClass("active");
        $("#pills-propietario-tab").addClass("active");
        $("#pills-propietario").addClass("active");
        $("#pills-propietario").addClass("show");
    }
}

function busquedaCiudad(campo) {
    var standa = 'satt_standa';
    var key = $(campo).val();
    var opcion = 'consultaCiudades';
    var dataString = 'key=' + key + '&opcion=' + opcion;
    var nameid = $(campo).attr('id');
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php",
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

//Vacia campo sugerencia de ciudades
function limpia(elemento) {
    $(elemento).val("");
}

//pre guardado de la fase 1 inicial --documentacion
function preguardadoF1() {
    $('#modalPreGuardadoF1').modal('toggle');
    var standa = 'satt_standa';
    var dataString = 'opcion=guardadoFase1';
    var data = new FormData(document.getElementById('dataSolicitud'));
    data.append('cod_gestio', $('#cod_gestio').val());
    data.append('obs_gestio', $('#obs_gestio').val());
    if ($('#check_canEstSol').attr('checked')) {
        data.append('ind_cancel', $('#check_canEstSol').val());
    } else {
        data.append('ind_cancel', 0);
    }
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        data,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            cargando("Pre Guardando la informaci�n. Por favor espere.")
        },
        success: function(data) {
            swal.close();
            if (data['status'] == 200) {
                Swal.fire({
                    title: 'Registrado!',
                    text: data['response'],
                    type: 'success',
                    confirmButtonColor: '#336600'
                }).then((result) => {
                    if (data['redire'] == 1) {
                        window.history.go(-1);
                    } else {
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data['response'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                });
            }
        },
    });
}

function preguardado() {
    var standa = 'satt_standa';
    var dataString = 'opcion=preguardado';
    var data = new FormData(document.getElementById('dataSolicitud'));
    data.append('cod_gestio', $('#cod_gestioF2').val());
    data.append('obs_gestio', $('#obs_gestioF2').val());
    if ($('#check_canEstSolF2').attr('checked')) {
        data.append('ind_cancel', $('#check_canEstSolF2').val());
    } else {
        data.append('ind_cancel', 0);
    }
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        data,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            cargando("Pre Guardando la informaci�n. Por favor espere.")
        },
        success: function(data) {
            swal.close();
            if (data['status'] == 200) {
                Swal.fire({
                    title: 'Registrado!',
                    text: data['response'],
                    type: 'success',
                    confirmButtonColor: '#336600'
                }).then((result) => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data['response'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                });
            }
        },
    });

}


function PorAprobValidate() {
    $("#porAprobCliente").validate({
        rules: {
            AproServicio: {
                required: true
            }
        },
        messages: {
            AproServicio: {
                required: "Por favor Seleccione una opci�n"
            }
        },
        submitHandler: function(form) {
            almacenarDatosPorAprobCliente();
        }
    });
}


function openModalViewPdf(elemento) {
    var cod_solici = $(elemento).attr('data-dato');
    var cod_estseg = $(elemento).attr('data-code');
    $("#procesoSolicitudModal").modal('toggle');
    $("#title-modal-viewPDF").empty();
    $("#title-modal-viewPDF").append('Estudio de seguridad No. ' + cod_estseg);
    $("#btn-pdf").attr('data-code', cod_estseg);
    $("#btn-sendpdf").attr('data-code', cod_estseg);
    $("#btn-atrasPDF").attr('data-dato', cod_solici);
    $("#visualizarPDFModal").modal('toggle');
}

function atrasModalViewPdf(elemento) {
    $("#visualizarPDFModal").modal('toggle');
    consInfoSolicitud(elemento);
}

function openModalViewDocuments(elemento) {
    var standa = 'satt_standa';
    var cod_solici = $(elemento).attr('data-dato');
    var cod_estseg = $(elemento).attr('data-code');
    $("#procesoSolicitudModal").modal('toggle');
    $("#title-modal-viewDocuments").empty();
    $("#title-modal-viewDocuments").append('Estudio de seguridad No. ' + cod_estseg);
    $("#btn-atrasDocuments").attr('data-dato', cod_solici);
    $("#generaZip").attr('data-code', cod_estseg);
    $("#generaZip").attr("href", "index.php?cod_servic=202109152&window=central&opcion=downloadZip&cod_estseg=" + cod_estseg);
    $("#tbody_document").empty();
    var dataString = 'opcion=getListDocuments&cod_estseg=' + cod_estseg;
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        async: false,
        dataType: 'html',
        success: function(data) {
            $("#tbody_document").append(data);
        },
    });
    $("#visualizarDocumentos").modal('toggle');
}

function atrasModalViewDocuments(elemento) {
    $("#visualizarDocumentos").modal('toggle');
    consInfoSolicitud(elemento);
}

function downloadZip(elemento) {
    var cod_estseg = $(elemento).attr('data-code');
    var standa = 'satt_standa';
    var dataString = 'cod_estseg=' + cod_estseg;
    var dataString = 'opcion=generaZip&cod_estseg=' + cod_estseg;
    $.ajax({
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        xhrFields: { responseType: 'blob' },
        beforeSend: function() {
            cargando('Estamos generando el documento. Por favor espere');
        },
        success: function(data) {
            var blob = new Blob([data]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "Resultados_EstudioSeguridad_" + cod_estseg + ".zip";
            link.click();
        },
        complete: function() {
            swal.close();
        },
    });
}

function viewPdf(elemento) {
    var cod_estseg = $(elemento).attr('data-code');
    var standa = 'satt_standa';
    var dataString = 'cod_estseg=' + cod_estseg;

    $.ajax({
        url: "../" + standa + "/estseg/inf_estseg_pdfxxx.php?" + dataString,
        xhrFields: { responseType: 'blob' },
        beforeSend: function() {
            cargando('Estamos generando el documento. Por favor espere');
        },
        success: function(data) {
            var blob = new Blob([data]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "Resultados_EstudioSeguridad_" + cod_estseg + ".pdf";
            link.click();
        },
        complete: function() {
            swal.close();
        },
    });
}

function sendPdfEmail(elemento) {
    var cod_estseg = $(elemento).attr('data-code');
    var standa = 'satt_standa';
    var dataString = 'cod_estseg=' + cod_estseg;
    var pdf;
    $.ajax({
        url: "../" + standa + "/estseg/inf_estseg_pdfxxx.php?" + dataString,
        xhrFields: { responseType: 'blob' },
        beforeSend: function() {
            cargando('Estamos generando el documento para realizar su envio. Por favor espere');
        },
        success: function(data) {
            var blob = new Blob([data]);
            pdf = blob;
            envioArchivo(pdf, cod_estseg);
        }
    });
}

function envioArchivo(archivo, cod_estseg) {
    var standa = 'satt_standa';
    var dataString = 'opcion=sendEmail';
    var data = new FormData();
    data.append('file', archivo);
    data.append('emails', $("#ema_envarch").val());
    data.append('cod_estseg', cod_estseg);
    $.ajax({
        data: data,
        type: "POST",
        url: "../" + standa + "/estseg/ajax_genera_estseg.php?" + dataString,
        cache: false,
        contentType: false,
        processData: false,
        xhrFields: { responseType: 'blob' },
        success: function(backParam) {
            swal.close();
            $("#ema_envarch").val('');
            $("#resp-sendEmail").append(`<div class="alert alert-success" role="alert">
                                            <i class="fa fa-check-circle" aria-hidden="true"></i> Correo Enviado
                                        </div>`);
            $("#resp-sendEmail").fadeOut(10000);
        }
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


function inicializarTablas() {
    var standa = 'satt_standa';
    $('#tabla_inf_registradas thead tr th').each(function(i) {
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

    rellenaTablas(1);
    rellenaTablas(2);
    rellenaTablas(3);

}


function executeFilter() {
    rellenaTablas(1);
    rellenaTablas(2);
    rellenaTablas(3);
}

function rellenaTablas(indicador) {

    var standa = 'satt_standa';
    if (indicador == 1) {
        var tabla = "#tabla_inf_registradas";
    } else if (indicador == 2) {
        var tabla = "#tabla_inf_enprogreso";
    } else if (indicador == 3) {
        var tabla = "#tabla_inf_finalizadas";
    }

    $(tabla + ' thead tr th').each(function(i) {
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

    //Variables
    transportadora = $("#transportadoraID").val();
    num_solici = $("#num_soliciID").val();
    estado = $("#estado_ID").val();
    fecha_inicio = $("#fec_inicio").val();
    fecha_final = $("#fec_finxxx").val();
    var table = $(tabla).DataTable({
        "ajax": {
            "url": "../" + standa + "/estseg/ajax_genera_estseg.php",
            "data": ({
                opcion: 'getRegistros',
                tip_inform: indicador,
                transportadora: transportadora,
                estado: estado,
                fecha_inicio: fecha_inicio,
                fecha_final: fecha_final,
                num_solici: num_solici
            }),
            "type": 'POST'
        },
        'processing': true,
        "deferRender": true,
        "autoWidth": false,
        "search": {
            "regex": true,
            "caseInsensitive": false,
        },
        "bDestroy": true,
        'paging': true,
        'info': true,
        'filter': true,
        'orderCellsTop': true,
        'fixedHeader': true,
        'language': {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "dom": "<'row w100 pb-2'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row w100'<'col-sm-12 col-md-7'i><'col-sm-12 col-md-5'p>>",
    });
}