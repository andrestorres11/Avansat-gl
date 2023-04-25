cargando("Cargando Pagina");

$(document).ready(function() {
    //Usada
    getFormulRegist();
    initFormulRegist();
    //Formulario Propietario
    $(".esPropieClass").on('click', function() {
        const FormAsi = $(this).attr("showForm");
        if ($(this).is(':checked')) {
            $("#" + FormAsi).slideUp();
            $('#' + FormAsi + ' .form-control').each(function() {
                $(this).removeAttr('validate');
            });
        } else {
            $("#" + FormAsi).slideDown('slow');
            $('#' + FormAsi + ' .form-control').each(function() {
                $(this).attr('validate', true);
            });
        }
    });

    //Inicializacion de indicadores
    cambioIndicadores($('input[name="pregu1con"]:checked'));
    cambioIndicadores($('input[name="pre_comveh"]:checked'));
    cambioIndicadores($('input[name="pregu2con"]:checked'));
    cambioIndicadores($('input[name="ind_preres"]:checked'));
    //executeFilter();
    inicializarTablas();
    swal.close();

    $("#bus_transp").click(function() {
        vaciaInputTransportadora(this)
    });

    $("input[type=file]").on("change", (e) => {
        const archivo = $(e.target)[0].files[0];
        let nombArchivo = archivo.name;
        var extension = nombArchivo.split(".").slice(-1);
        extension = extension[0];
        let extensiones = ["jpg", "png", "jpeg", "pdf"];

        if (extensiones.indexOf(extension) === -1) {
            alert("Extensi�n NO permitida (Solo se aceptan jpg, png, jpeg o pdf)");
            $(e.target).val('');
        }

    });

    $('.ciu_despac').focusout(function() {
        setTimeout(busquedaRuta, 1000);
    });

    //setInterval(refreshInfoRealTime, 1000);
});

//Usada
function getFormulRegist() {
    //Default
    var formActi = 'formVehicuSolici';
    var formHide1 = 'formConducSolici';
    var formHide2 = 'formCombinadoSolici';
    $("#" + formHide1).slideUp();
    $("#" + formHide2).slideUp();
    $("#" + formActi).slideDown('slow');

    $("[name='tip_estudi']").change(function() {
        var opcion = $(this).val();
        if (opcion == "V") {
            $("#formConducSolici").slideUp();
            $("#formCombinadoSolici").slideUp();
            $("#formVehicuSolici").slideDown('slow');
            formActi = 'formVehicuSolici';
            formHide1 = 'formConducSolici';
            formHide2 = 'formCombinadoSolici';
        } else if (opcion == "C") {
            $("#formVehicuSolici").slideUp();
            $("#formCombinadoSolici").slideUp();
            $("#formConducSolici").slideDown('slow');
            formActi = 'formConducSolici';
            formHide1 = 'formVehicuSolici';
            formHide2 = 'formCombinadoSolici';
        } else if (opcion == "CV") {
            $("#formVehicuSolici").slideUp();
            $("#formConducSolici").slideUp();
            $("#formCombinadoSolici").slideDown('slow');
            formActi = 'formCombinadoSolici';
            formHide1 = 'formVehicuSolici';
            formHide2 = 'formConducSolici';
        }
        $('#' + formHide1 + ' .form-control').each(function() {
            $(this).removeAttr('validate');
        });
        $('#' + formHide2 + ' .form-control').each(function() {
            $(this).removeAttr('validate');
        });
        $('#' + formActi + ' .form-control').each(function() {
            $(this).attr('validate', true);
        });
    });


}

function initFormulRegist() {
    $('#num_documeConID').blur(function() {
        setTercerFormulConduc($(this).val());
    });
    $('#num_documePosID').blur(function() {
        setTercerFormulPoseed($(this).val());
    });
    $('#num_documeProID').blur(function() {
        setTercerFormulPoseed($(this).val());
    });
    $('#num_placaxID').blur(function() {
        setTercerFormulVehicu($(this).val());
    });
}

function setTercerFormulConduc(cod_tercer) {
    var standa = 'satt_standa';
    var dataString = 'cod_tercer=' + cod_tercer + '&opcion=getTercer';
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php",
        method: 'POST',
        data: dataString,
        dataType: "json",
        success: function(data) {
            if (!data['regi']) {
                Swal.fire({
                    title: 'Alerta',
                    text: 'El Conductor tiene un estudio vigente por finalizar o uno finalizado no vencido',
                    type: 'error',
                    confirmButtonColor: '#336600'
                });
                $("#num_documeConID").val('');
                return false;
            }
            if (data['resp'] == true) {
                $("#tip_documeConID option[value='" + data['data']['cod_tipdoc'] + "']").attr('selected', 'selected');
                $("#tip_documeConID").attr('disabled', true);
                $("#num_documeConID").attr('disabled', true);
                $("#nom_personConID").attr('disabled', true);
                $("#nom_personConID").val(data['data']['nom_person']);
                $("#nom_apell1ConID").attr('disabled', true);
                $("#nom_apell1ConID").val(data['data']['nom_apell1']);
                $("#nom_apell2ConID").attr('disabled', true);
                $("#nom_apell2ConID").val(data['data']['nom_apell2']);
                $("#num_telmovConID").val(data['data']['num_telmov']);
                $("#num_telmo2ConID").val(data['data']['num_telmo2']);
                $("#dir_emailxConID").val(data['data']['dir_emailx']);
                Swal.fire({
                    title: 'Advertencia',
                    text: 'El Conductor (' + data['data']['cod_tercer'] + ') ya se encuentra registrado.',
                    type: 'warning',
                    confirmButtonColor: '#336600'
                });
            }
        }
    });
}

function setTercerFormulPoseed(cod_tercer) {
    var standa = 'satt_standa';
    var dataString = 'cod_tercer=' + cod_tercer + '&opcion=getTercer';
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php",
        method: 'POST',
        data: dataString,
        dataType: "json",
        success: function(data) {
            if (data['resp'] == true) {
                $("#tip_documePosID option[value='" + data['data']['cod_tipdoc'] + "']").attr('selected', 'selected');
                $("#tip_documePosID").attr('disabled', true);
                $("#num_documePosID").attr('disabled', true);
                $("#nom_personPosID").attr('disabled', true);
                $("#nom_personPosID").val(data['data']['nom_person']);
                $("#nom_apell1PosID").attr('disabled', true);
                $("#nom_apell1PosID").val(data['data']['nom_apell1']);
                $("#nom_apell2PosID").attr('disabled', true);
                $("#nom_apell2PosID").val(data['data']['nom_apell2']);
                Swal.fire({
                    title: 'Advertencia',
                    text: 'El Poseedor (' + data['data']['cod_tercer'] + ') ya se encuentra registrado.',
                    type: 'warning',
                    confirmButtonColor: '#336600'
                });
            }
        }
    });
}

function setTercerFormulPropie(cod_tercer) {
    var standa = 'satt_standa';
    var dataString = 'cod_tercer=' + cod_tercer + '&opcion=getTercer';
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php",
        method: 'POST',
        data: dataString,
        dataType: "json",
        success: function(data) {
            if (data['resp'] == true) {
                $("#tip_documeProID option[value='" + data['data']['cod_tipdoc'] + "']").attr('selected', 'selected');
                $("#tip_documeProID").attr('disabled', true);
                $("#num_documeProID").attr('disabled', true);
                $("#nom_personProID").attr('disabled', true);
                $("#nom_personProID").val(data['data']['nom_person']);
                $("#nom_apell1ProID").attr('disabled', true);
                $("#nom_apell1ProID").val(data['data']['nom_apell1']);
                $("#nom_apell2ProID").attr('disabled', true);
                $("#nom_apell2ProID").val(data['data']['nom_apell2']);
                Swal.fire({
                    title: 'Advertencia',
                    text: 'El Poseedor (' + data['data']['cod_tercer'] + ') ya se encuentra registrado.',
                    type: 'warning',
                    confirmButtonColor: '#336600'
                });
            }
        }
    });
}

function setTercerFormulVehicu(num_placax) {
    var standa = 'satt_standa';
    var dataString = 'num_placax=' + num_placax + '&opcion=getVehicu';
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php",
        method: 'POST',
        data: dataString,
        dataType: "json",
        success: function(data) {
            if (!data['regi']) {
                Swal.fire({
                    title: 'Advertencia',
                    text: 'El vehiculo (' + data['data']['num_placax'] + ') tiene un estudio vigente por finalizar o uno finalizado no vencido',
                    type: 'warning',
                    confirmButtonColor: '#336600'
                });
                $("#num_placaxID").val('');
                return false;
            }
        }
    });
}

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
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php",
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
                    url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
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
                        $("#tip_estudioc").val(data['nom_tipest']);
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
      <label for="nom_soliciID" class="labelinput"><div class="obl">*</div>N�mero de celular del Conductor:</label>
      <input class="form-control form-control-sm req" type="text" placeholder="N�mero de celular" id="num_telmovID` + cantid + `" name="num_telmov[` + incrme + `]" required>
      </div>
    </div>
    <div class="row">
      <div class="col-4 form-group">
        <label for="nom_soliciID" class="labelinput">N�mero de celular 2 del Conductor:</label>
        <input class="form-control form-control-sm" type="text" placeholder="N�mero de celular 2" id="num_telmo2ID` + cantid + `" name="num_telmo2[` + incrme + `]">
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

function validacionesCampos(formulario) {
    $('#' + formulario + ' .form-control[id]').each(function() {
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
                        email: "El correo no es v�lido"
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
            console.log(this.id);
        }
    });

    $('#' + formulario + ' .inputDocument').each(function() {
        if ($(this).attr('validate') != undefined) {
            if ($(this).hasClass("docreq")) {
                $(this).rules("add", {
                    required: true,
                    messages: {
                        required: "Debe adjuntar el documento."
                    }
                });
            }
        }
    });

}

//usado
function validateInicial() {
    $('#InsSolici').removeData('validator');


    var validator = $("#InsSolici").validate({
        ignore: []
    });

    validacionesCampos("InsSolici");

    if ($("#InsSolici").valid()) {
        almacenarSolicitud();
    }
}

//VALIDACIONES FORMULARIOS
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
        $(this).addClass('docreq');
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
            extension: "jpg|png|jpeg",
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

function traeLineasSolici(elemento) {
    var standa = 'satt_standa';
    var dataString = 'opcion=getLineas&cod_marcax=' + $(elemento).val();
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        async: false,
        dataType: 'html',
        success: function(data) {
            $("#cod_lineaSID").empty();
            $("#cod_lineaSID").append(data);
        },
    });
}

function traeLineas(elemento) {
    var standa = 'satt_standa';
    var dataString = 'opcion=getLineas&cod_marcax=' + $(elemento).val();
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        async: false,
        dataType: 'html',
        success: function(data) {
            $("#cod_lineaxID").empty();
            $("#cod_lineaxID").append(data);
        },
    });
}

//usado
function validateEstudioSoliciFinal() {
    $('#dataSolicitud').removeData('validator');
    var validator = $("#dataSolicitud").validate({
        ignore: []
    });
    validacionesCampos("dataSolicitud");

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
    } else {
        $("#modalGuardadoFinal").modal("hide");
        alertError('Hay campos obligatorios sin llenar.');
    }

}

//usado
function valSaveGestioSolici() {
    Swal.fire({
        type: 'warning',
        title: '�Desea guardar y terminar la gesti�n de la solicitud?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Terminar',
        cancelButtonText: 'Pre-Guardar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'

    }).then((result) => {
        if (result.value) {
            $("#modalGuardadoFinal").modal("show");
        } else {
            $("#modalPreGuardadoF1").modal("show");
        }
    })
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

//usada
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

//usada
function alertError($msj) {
    Swal.fire({
        title: 'Error!',
        text: $msj,
        type: 'error',
        confirmButtonColor: '#336600'
    })
}

//usado
function almacenarSolicitud() {
    var standa = 'satt_standa';
    var dataString = 'opcion=guardarSolicitud';
    var data = new FormData(document.getElementById('InsSolici'));

    data.append('nom_solici', $("#nom_soliciID").val());
    data.append('cor_solici', $("#cor_soliciID").val());
    data.append('tel_solici', $("#tel_soliciID").val());
    data.append('cel_solici', $("#cel_soliciID").val());

    if ($('#genDespac').is(':checked')) {
        data.append('ind_credes', 1);
    } else {
        data.append('ind_credes', 0);
    }

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
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
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
            if (data['status'] == 100) {
                $('#NuevaSolicitudModal').modal('hide');
                swal.close();
                alertSuccess(data['response']);
            } else {
                alertError(data['response']);
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

//usada
function almacenarEstudioFinal() {
    var obs_gestio = $('#obs_gestio_f').val();
    var ind_estudi = $('input[name="ind_estudi"]:checked').val();
    if (ind_estudi == '' || ind_estudi == undefined) {
        Swal.fire({
            title: '�Error!',
            text: 'Debe diligenciar la respuesta final de la solicitud.',
            type: 'error',
            confirmButtonColor: '#336600'
        });
        $('input[name="ind_estudi"]').addClass("val-error");
        $('input[name="ind_estudi"]').on('click', function() {
            $('input[name="ind_estudi"]').removeClass("val-error");
        });
        return false;
    }
    if (obs_gestio == '') {
        Swal.fire({
            title: '�Error!',
            text: 'Debe diligenciar la observaci�n.',
            type: 'error',
            confirmButtonColor: '#336600'
        });
        $("#obs_gestio_f").addClass("val-error");
        $("#obs_gestio_f").on('click', function() {
            $("#obs_gestio_f").removeClass("val-error");
        });
        return false;
    }
    var standa = 'satt_standa';
    var dataString = 'opcion=guardado';
    var data = new FormData(document.getElementById('dataSolicitud'));
    data.append('obs_gestio', obs_gestio);
    data.append('ind_estudi', ind_estudi);
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
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
                generaPDFSend(data['cod_solici'], data['emails'], data);
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

//usada
function cambioIndicadores(elemento) {
    var name_compl = 'com_' + $(elemento).attr('name');
    var input_compl = $('#' + name_compl);
    if ($(elemento).val() == 1) {
        input_compl.show();
    } else {
        input_compl.hide();
    }
}

//usada
function generaPDFSend(cod_solici, emails, information) {
    var standa = 'satt_standa';
    var dataString = 'cod_solici=' + cod_solici;
    var rut_pdfxxx = $("#rut_estpdfID").val();
    if (rut_pdfxxx == '' || rut_pdfxxx == null) {
        rut_pdfxxx = 'estsegv2/inf_pdfxxx_genera.php';
    }
    $.ajax({
        url: "../" + standa + "/" + rut_pdfxxx + "?" + dataString,
        xhrFields: { responseType: 'blob' },
        success: function(data) {
            var blob = new Blob([data]);
            pdf = blob;
            var data = new FormData();
            data.append('file', pdf);
            data.append('emails', emails);
            data.append('cod_solici', cod_solici);
            var infoenv = 'opcion=sendEmail';
            $.ajax({
                data: data,
                type: "POST",
                url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + infoenv,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(backParam) {
                    swal.close();
                    if (backParam.status) {
                        Swal.fire({
                            title: 'Registrada',
                            text: '',
                            type: 'success',
                            confirmButtonColor: '#336600'
                        }).then((result) => {
                            if (information['redire'] == 1) {
                                window.location.href = information['page'];
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: '�Error!',
                            text: backParam.error.message,
                            type: 'warning',
                            confirmButtonColor: '#336600'
                        }).then((result) => {
                            if (information['redire'] == 1) {
                                window.location.href = information['page'];
                            } else {
                                location.reload();
                            }
                        });
                    }

                }
            });
        }
    });
}

//usada
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
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
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
                    <td>` + $('#obs_refereID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val() + `</td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="borrarReferenciaFyP('` + data['ultimo'] + `','` + data['person'] + `',this)"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                    </tr>`);

                $('#nom_refereID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('');
                $('#nom_parentID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('Padre');
                $('#dir_domiciID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('');
                $('#num_telefoID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('');
                $('#nom_parentID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('1');
                $('#obs_refereID_' + data['person'] + '_' + data['tip_refere'] + '_' + data['cod_identi']).val('');

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

//usada
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
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
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

//usada
function borrarReferenciaFyP(cod_refere, cod_person, elemento) {
    var standa = 'satt_standa';
    var dataString = 'opcion=borrarReferenceFyP&cod_refere=' + cod_refere + '&cod_person=' + cod_person;
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
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

//usada
function borrarReferenciaLaboral(cod_refere, cod_person, elemento) {
    var standa = 'satt_standa';
    var dataString = 'opcion=borrarReferenceLaboral&cod_refere=' + cod_refere + '&cod_person=' + cod_person;
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
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
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php",
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
function preguardado() {
    if ($('#obs_gestio').val() != '') {
        $('#modalPreGuardadoF1').modal('toggle');
        var standa = 'satt_standa';
        var dataString = 'opcion=preguardado';
        var data = new FormData(document.getElementById('dataSolicitud'));
        data.append('obs_gestio', $('#obs_gestio').val());
        $.ajax({
            url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
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
    } else {
        Swal.fire({
            title: '�Error!',
            text: 'Debe diligenciar la observaci�n.',
            type: 'error',
            confirmButtonColor: '#336600'
        });
        $("#obs_gestio").addClass("val-error");
        $("#obs_gestio").on('click', function() {
            $("#obs_gestio").removeClass("val-error");
        });
    }
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

//usado
function openModalViewPdf(elemento) {
    var cod_solici = $(elemento).attr('data-dato');
    //visualizarPDFModal
    $("#visualizarPDFModal").modal('toggle');
    $("#title-modal-viewPDF").empty();
    $("#title-modal-viewPDF").append('Estudio de seguridad No. ' + cod_solici);
    $("#btn-pdf").attr('data-dato', cod_solici);
    $("#btn-sendpdf").attr('data-dato', cod_solici);
    $("#visualizarPDFModal").modal('toggle');
}

function atrasModalViewPdf(elemento) {
    $("#visualizarPDFModal").modal('toggle');
    consInfoSolicitud(elemento);
}

//usado
function openModalViewDocuments(elemento) {
    var standa = 'satt_standa';
    var cod_solici = $(elemento).attr('data-dato');
    $("#title-modal-viewDocuments").empty();
    $("#title-modal-viewDocuments").append('Estudio de seguridad No. ' + cod_solici);
    $("#generaZip").attr('data-dato', cod_solici);
    $("#generaZip").attr("href", "index.php?cod_servic=202109152&window=central&opcion=downloadZip&cod_estseg=" + cod_solici);
    $("#tbody_document").empty();
    $("#visualizarDocumentos").modal('toggle');

    var dataString = 'opcion=getListDocuments&cod_solici=' + cod_solici;
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        async: false,
        dataType: 'html',
        success: function(data) {
            $("#tbody_document").append(data);
        },
    });

}

function atrasModalViewDocuments(elemento) {
    $("#visualizarDocumentos").modal('toggle');
    consInfoSolicitud(elemento);
}

function downloadZip(elemento) {
    var cod_solici = $(elemento).attr('data-dato');
    var standa = 'satt_standa';
    var dataString = 'opcion=generaZip&cod_solici=' + cod_solici;
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        xhrFields: { responseType: 'blob' },
        beforeSend: function() {
            cargando('Estamos generando el archivo zip. Por favor espere');
        },
        success: function(data) {
            var blob = new Blob([data]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "Resultados_EstudioSeguridad_" + cod_solici + ".zip";
            link.click();
        },
        complete: function() {
            swal.close();
        },
    });
}

//usado
function viewPdf(elemento) {
    var cod_solici = $(elemento).attr('data-dato');
    var standa = 'satt_standa';
    var dataString = 'opcion=getPDFGenerado&cod_solici=' + cod_solici;
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        data: dataString,
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            cargando("Consultado PDF Generado. Por favor espere...")
        },
        success: function(data) {
            swal.close();
            if (data.status) {
                window.open(data.resp.file_url, '_blank');
                Swal.fire({
                    title: '�Exito!',
                    text: data.message,
                    type: 'success',
                    confirmButtonColor: '#336600'
                }).then((result) => {
                    swal.close();
                })

            } else {
                alertError(data.error.message);
            }
        }
    });
}

function sendPdfEmail(elemento) {
    var cod_solici = $(elemento).attr('data-dato');
    var standa = 'satt_standa';
    var dataString = 'opcion=reSendEmail';
    var data = new FormData();
    data.append('emails', $("#ema_envarch").val());
    data.append('cod_solici', cod_solici);
    $.ajax({
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
        method: 'POST',
        data,
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            cargando("Alistando todo para enviar el correo. Por favor espere...")
        },
        success: function(data) {
            swal.close();
            if (data.status) {
                $("#ema_envarch").val('');
                $("#resp-sendEmail").append(`<div class="alert alert-success" role="alert">
                                                <i class="fa fa-check-circle" aria-hidden="true"></i> ` + data.message + `
                                            </div>`);
                $("#resp-sendEmail").fadeOut(10000);
            } else {
                alertError(data.error.message);
            }
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

//usado
function inicializarTablas() {
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
    $('#tabla_inf_finalizadas thead tr th').each(function(i) {
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

    rellenaTablas();

    var table = $('#tabla_inf_registradas').DataTable({
        "processing": true,
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

    var table = $('#tabla_inf_finalizadas').DataTable({
        "processing": true,
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

function executeFilter() {
    rellenaTablas();
}

//usado
function rellenaTablas() {
    var standa = 'satt_standa';
    //Variables
    var dataString = 'opcion=getRegistros';
    var cod_emptra = $("#transportadoraID").val();
    var num_solici = $("#num_soliciID").val();
    var fec_inicio = $("#fec_inicio").val();
    var fec_finalx = $("#fec_finxxx").val();
    fil_fechas = false;
    if ($('#fil_fechasID').is(':checked')) {
        fil_fechas = true;
    }
    $.ajax({
        data: {
            cod_emptra,
            num_solici,
            fec_inicio,
            fec_finalx,
            fil_fechas
        },
        type: "POST",
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
        dataType: "json",
        beforeSend: function() {
            cargando('Cargando la informaci�n...');
        },
        success: function(data) {
            var table = $('#tabla_inf_registradas').DataTable();
            table.clear();
            table.rows.add(data['registrados']).draw();
            var table = $('#tabla_inf_finalizadas').DataTable();
            table.clear();
            table.rows.add(data['finalizados']).draw();
        },
        complete: function() {
            swal.close();
        },
        error: function() {
            alertError('Error, por favor contacte a soporte');
        }

    });
}

function refreshInfoRealTime() {
    var standa = 'satt_standa';
    //Variables
    var dataString = 'opcion=getRegistros';
    var cod_emptra = $("#transportadoraID").val();
    var num_solici = $("#num_soliciID").val();
    var fec_inicio = $("#fec_inicio").val();
    var fec_finalx = $("#fec_finxxx").val();
    fil_fechas = false;
    if ($('#fil_fechasID').is(':checked')) {
        fil_fechas = true;
    }
    $.ajax({
        data: {
            cod_emptra,
            num_solici,
            fec_inicio,
            fec_finalx,
            fil_fechas
        },
        type: "POST",
        url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
        dataType: "json",
        success: function(data) {
            var table = $('#tabla_inf_registradas').DataTable();
            table.clear();
            table.rows.add(data['registrados']).draw();
            var table = $('#tabla_inf_finalizadas').DataTable();
            table.clear();
            table.rows.add(data['finalizados']).draw();
        },
    });
}

function busquedaRuta() {

    var ciu_origen = $("#ciu_origen").val();
    var ciu_destin = $("#ciu_destin").val();

    if (ciu_origen != '' && ciu_destin != '') {
        var standa = 'satt_standa';
        var dataString = 'opcion=getRutas&ciu_origen=' + ciu_origen + '&ciu_destin=' + ciu_destin;
        $.ajax({
            url: "../" + standa + "/estsegv2/ajax_genera_estseg.php?" + dataString,
            method: 'POST',
            async: false,
            dataType: 'html',
            success: function(data) {
                $("#rut_despacID").empty();
                $("#rut_despacID").append(data);
            },
        });
    }
}