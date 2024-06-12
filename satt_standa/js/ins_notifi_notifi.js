$(document).ready(function() {
    closePopUp('popID');
    $("#fec_iniID").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
    });
    $("#fec_finID").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
    });
    preFunction();
    $('.error').fadeOut();
});

/*! \fn: preFunction
 *  \brief: Ejecuta acciones despues de cargado el script
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function preFunction() {
    $('.error').fadeOut();
    $("input[name=multiselect_cod_asiresID]").click(function() {
        getNomUsuario();
    });
    $(".ui-multiselect-none").click(function() {
        $("#ind_notusrID").multiselect('destroy');
        $("#ind_notusrID").html("");
        $("#ind_notusrID").multiselect().multiselectfilter();
    });
    $(".ui-multiselect-all").click(function() {
        getNomUsuario();
    });
    $(':file').change(function() {
        var file = $(this).val();
        switch (file.substring(file.lastIndexOf('.') + 1).toLowerCase()) {
            case 'jpg':
            case 'jpeg':
            case 'bmp':
            case 'tiff':
            case 'png':
            case 'pdf':
            case 'doc':
            case 'docx':
            case 'xls':
            case 'xlsx':
            case 'csv':
            case 'zip':
            case 'rar':
                //alert("ok");
                break;
            default:
                $(this).focus().after('<span class="error-notifi" onclick="cerrarAlert()">Formato no permitido</span>');
                $(this).val("");
                break;
        }
        var filesize = $(this)[0].files[0];
        if (filesize.size >= "5000000") {
            $(this).focus().after('<span class="error-notifi" onclick="cerrarAlert()">tamaño del archivo no permitido</span>');
            //alert("tamaño del archivo no permitido");
            $(this).val("");
        }
    });
}

/*! \fn: validarKey
 *  \brief: validad campos que deban cumplir con ciertas condiciones
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: min :cantida minima de caracteres
 *  \param: max :cantida maxima de caracteres
 *  \param: type:tipo de caracteres a procesar (numerico=num,alfanumerico=alpa)
 *  \param: campo: id de la etiqueta a validar
 *  \return: 
 */
function validarKey(min, max, type, campo) {
    $('.error').fadeOut();
    if ($('#' + campo).val().length < min) {
        $('#' + campo).focus().after('<span class="error-notifi" onclick="cerrarAlert()">Valor minimo no cumple</span>');
        //$('#'+campo).val("");
    }
    if ($('#' + campo).val().length > max) {
        $('#' + campo).focus().after('<span class="error-notifi" onclick="cerrarAlert()">Valor maximo superado</span>');
        $('#' + campo).val("");
    }
    if (type == "num") {
        if (!$('#' + campo).val().match(/^[0-9]+$/)) {
            $('#' + campo).focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Numerico</span>');
            $('#' + campo).val("");
        }
    }
    if (campo == "num_horlabID") {
        hl = $('#' + campo).val();
        if (hl > 24) {
            $('#' + campo).focus().after('<span class="error-notifi" onclick="cerrarAlert()">Maximo 24 Hrs</span>');
            $('#' + campo).val("");
        }
    }
}

/*! \fn: ValidarFecha
 *  \brief: validad la fecha que deban cumplir con ciertas condiciones
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: campo: id de la etiqueta a validar
 *  \return: 
 */
function ValidarFecha(campo) {
    var RegExPattern = '^([0-9]{4}[-/]?((0[13-9]|1[012])[-/]?(0[1-9]|[12][0-9]|30)|(0[13578]|1[02])[-/]?31|02[-/]?(0[1-9]|1[0-9]|2[0-8]))|([0-9]{2}(([2468][048]|[02468][48])|[13579][26])|([13579][26]|[02468][048]|0[0-9]|1[0-6])00)[-/]?02[-/]?29)$';
    if ((campo.match(RegExPattern)) && (campo != '')) {
        return true;
    } else {
        return false;
    }
}

/*! \fn: btnGeneral
 *  \brief: genera los reportes generales y los muestra en su repectivo tb
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function btnGeneral() {
    $('.error').fadeOut();
    var fec_iniID = $("#fec_iniID").val();
    var fec_finID = $("#fec_finID").val();
    var standa = $("#standaID").val();
    var formData = "option=getFormGeneral&standa=" + standa + "&fec_iniID=" + fec_iniID + "&fec_finID=" + fec_finID;
    if (fec_iniID != "") {
        if (fec_finID != "") {
            if (Date.parse(fec_iniID) < Date.parse(fec_finID)) {
                $.ajax({
                    url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                    type: "POST",
                    data: formData,
                    async: true,
                    cache: false,
                    beforeSend: function() {

                    },
                    success: function(data) {
                        $("#tabgeneral").html(data);
                        $("#secID").css({ "height": "auto" });
                        //alert(data);
                    }
                });
            } else {
                $("#fec_iniID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">las fecha ' + fec_iniID + ' es mayor q ' + fec_finID + '</span>');
                //alert("las fecha "+fec_iniID+" es mayor q "+fec_finID);
            }
        } else {
            $("#fec_finID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
            //alert("Seleciones una fecha");
        }
    } else {
        $("#fec_iniID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
        //alert("Seleciones una fecha");
    }
}

/*! \fn: btnSubModulos
 *  \brief: genera los diferentes modulos que se encuentan
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: cod_notifi tipo de notificacion a mostar
 *  \return: 
 */
function btnSubModulos(cod_notifi) {
    $('.error').fadeOut();
    var identificador;
    var fec_iniID = $("#fec_iniID").val();
    var fec_finID = $("#fec_finID").val();
    var standa = $("#standaID").val();
    identificador = "tabResult";
    //alert(permisos);
    var formData = "option=getForm&standa=" + standa + "&cod_notifi=" + cod_notifi + "&fec_iniID=" + fec_iniID + "&fec_finID=" + fec_finID;
    if (fec_iniID != "") {
        if (fec_finID != "") {
            if (Date.parse(fec_iniID) < Date.parse(fec_finID)) {
                $.ajax({
                    url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                    type: "POST",
                    data: formData,
                    async: true,
                    cache: false,
                    success: function(data) {
                        $("#" + identificador).html("");
                        $("#" + identificador).html(data);
                        $("#secID").css({ "height": "auto" });
                        //alert(data);
                    }
                });
            } else {
                $("#fec_iniID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">las fecha ' + fec_iniID + ' es mayor q ' + fec_finID + '</span>');
                //alert("las fecha "+fec_iniID+" es mayor que "+fec_finID);
            }
        } else {
            $("#fec_finID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
            //alert("Seleciones una fecha");
        }
    } else {
        $("#fec_iniID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
        //alert("Seleciones una fecha");
    }
}

/*! \fn: NuevaNoti
 *  \brief: genera el formulario para una nueva notificacion
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: id tipo de notificacion a mostar
 *  \return: 
 */
function NuevaNoti(id) {
    $('.error').fadeOut();
    var standa = $("#standaID").val();
    NomNotifi = "";
    switch (id) {
        case 1:
            NomNotifi = " OET";
            break;
        case 2:
            NomNotifi = " CLF";
            break;
        case 3:
            NomNotifi = " SUPERVISORES";
            break;
        case 4:
            NomNotifi = " CONTROLADORES";
            break;
        case 5:
            NomNotifi = " CLIENTES";
            break;
    }
    if (id == '3') {
        var formData = "option=getFormNuevaNotifi2&standa=" + standa + "&idForm=" + id + "&ActionForm=ins";

    } else {
        var formData = "option=getFormNuevaNotifi&standa=" + standa + "&idForm=" + id + "&ActionForm=ins";
    }
    $("#popID").remove();
    closePopUp('popID');
    LoadPopupJQNoButton('open', 'NUEVA NOTIFICACION ' + NomNotifi, ($(window).height() - 40), ($(window).width() - 40), false, false, true);
    var popup = $("#popID");
    popup.html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
    setTimeout(function() {
        $.ajax({
            url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
            type: "POST",
            data: formData,
            async: true,
            cache: false,
            beforeSend: function(obj) {
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            success: function(data) {
                setTimeout(function() {
                    popup.html(data);
                    $("#cod_asiresID").multiselect().multiselectfilter();
                    $("#ind_notusrID").multiselect().multiselectfilter();
                    preFunction();
                }, 1000);
            }
        });

    }, 1000);
}

/*! \fn: getFechaDatapick
 *  \brief: captura la fecha del id enviado
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: iddp identificador de la etiqueta
 *  \return: 
 */
function getFechaDatapick(iddp) {
    $("#" + iddp).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
    });
}

/*! \fn: limpiarForm
 *  \brief: limpia el formulario y cierra el popap
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function limpiarForm() {
    $("#nom_asuntoID").val("");
    $("#fec_creaci").val("");
    $("#NotificadoPorID").val("");
    $("#fec_vigencID").val("");
    $("#obs_notifiID").val("");
    closePopUp('popID');
    $("#popID").remove();
    $('.error').fadeOut();
}

/*! \fn: ValidateFormComun
 *  \brief: valida el formulario para proceder a ejecutar la accion enviada
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: accion proceso a realizar (ins=insertar , idi= editar)
 *  \return: 
 */
function ValidateFormComun(accion) {
    var standa = $("#standaID").val();
    var nom_asunto = $("#nom_asuntoID").val().toUpperCase();
    var fec_creaci = $("#fec_creaciID").val();
    var usr_creaci = $("#usr_creaciID").val();
    var cod_asires = validateChekBox("cod_asiresID");
    var ind_notusr = validateChekBox("ind_notusrID");
    var fec_vigenc = $("#fec_vigencID").val();
    var ind_respue = $("input:radio[name=ind_respue]:checked").val();
    var ind_enttur;
    var obs_notifi = $("#obs_notifiID").val();
    var cod_tipnot = $("#cod_tipnotID").val();
    var num_horlab = $("#num_horlabID").val();
    var cod_notifi = $("#cod_notifiID").val();
    if (accion == "ins") {
        ind_enttur = $("input:radio[name=ind_enttur]:checked").val();
    } else {
        ind_enttur = $("#ind_entturID").val();
    }
    band = 1;
    $('.error').fadeOut();
    if (nom_asunto.length > 5 && nom_asunto.length < 100) {
        if (validateChekBox("cod_asiresID") != "") {
            if (validateChekBox("ind_notusrID") != "") {
                if ($("#fec_vigencID").val() != "") {
                    if (obs_notifi.length > 5 && nom_asunto.length < 3000) {
                        if ((cod_tipnot == 3 && ind_enttur == 1) || (cod_tipnot == 4 && ind_enttur == 1)) {
                            if ((num_horlab.length > 0 && num_horlab.length < 3)) {
                                console.log("9");
                                var mdata = new FormData();
                                if (accion == "ins") {
                                    mdata.append("option", "NuevaNotifiExten");
                                } else if (accion == "idi") {
                                    mdata.append("option", "EditNotifiExten");
                                    mdata.append("cod_notifi", cod_notifi);
                                }
                                mdata.append("ActionForm", accion);
                                mdata.append("nom_asunto", nom_asunto);
                                mdata.append("fec_creaci", fec_creaci);
                                mdata.append("usr_creaci", usr_creaci);
                                mdata.append("cod_asires", cod_asires);
                                mdata.append("ind_notusr", ind_notusr);
                                mdata.append("fec_vigenc", fec_vigenc);
                                mdata.append("ind_respue", ind_respue);
                                mdata.append("obs_notifi", obs_notifi);
                                mdata.append("cod_tipnot", cod_tipnot);
                                mdata.append("num_horlab", num_horlab);
                                mdata.append("ind_enttur", ind_enttur);
                                $("#Document").find("input[type=file]").each(function() {
                                    mdata.append($(this).attr('name'), $(this)[0].files[0]);
                                });



                                console.log(mdata);
                                returnJson = ValidateFormExt(cod_tipnot);
                                console.log(returnJson);
                                if (returnJson.NOTNULL) {

                                    $.each(returnJson.NOTNULL, function(i, v) {
                                        acordionopen = $("#" + v + "ID").parents("DIV").parents("DIV").attr("id");
                                        openClass = $("#" + acordionopen).children("h3").hasClass("ui-state-active");
                                        if (openClass == false) {
                                            $("#" + acordionopen).accordion({ active: 0 });
                                        }
                                        $("#" + v + "ID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
                                    });
                                    //alert("error en el json");
                                } else {
                                    var confirmacion = confirm("Desea " + ((accion == "ins") ? "almacenar" : "editar") + " la notificacion?");
                                    if (confirmacion) {
                                        var myJsonString = JSON.stringify(returnJson);
                                        console.log(myJsonString);
                                        mdata.append("jso_notifi", myJsonString);
                                        $.ajax({
                                            url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                                            type: 'POST',
                                            contentType: false,
                                            data: mdata,
                                            //async: false,
                                            processData: false,
                                            cache: false,
                                            success: function(data) {
                                                //alert(data);
                                                if (data == "OK") {
                                                    alert("Se " + ((accion == "ins") ? "almaceno" : "edito") + " la notificacion correctamente");
                                                    limpiarForm();
                                                    location.reload();
                                                } else {
                                                    alert("error al crear la notificaion");
                                                }
                                            }
                                        });
                                    }
                                }
                            } else {
                                acordionopen = $("#num_horlabID").parents("DIV").parents("DIV").attr("id");
                                openClass = $("#" + acordionopen).children("h3").hasClass("ui-state-active");
                                if (openClass == false) {
                                    $("#" + acordionopen).accordion({ active: 0 });
                                }
                                $("#num_horlabID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
                            }
                        } else {
                            var confirmacion = confirm("Desea " + ((accion == "ins") ? "almacenar" : "editar") + " la notificacion?");
                            if (confirmacion) {
                                var mdata = new FormData();
                                if (accion == "ins") {
                                    mdata.append("option", "NuevaNotifiComun");
                                } else if (accion == "idi") {
                                    mdata.append("option", "EditNotifiComun");
                                    mdata.append("cod_notifi", cod_notifi);
                                }

                                mdata.append("ActionForm", accion);
                                mdata.append("nom_asunto", nom_asunto);
                                mdata.append("fec_creaci", fec_creaci);
                                mdata.append("usr_creaci", usr_creaci);
                                mdata.append("cod_asires", cod_asires);
                                mdata.append("ind_notusr", ind_notusr);
                                mdata.append("fec_vigenc", fec_vigenc);
                                mdata.append("ind_respue", ind_respue);
                                mdata.append("obs_notifi", obs_notifi);
                                mdata.append("cod_tipnot", cod_tipnot);
                                mdata.append("ind_enttur", ind_enttur);
                                $("#Document").find("input[type=file]").each(function() {
                                    mdata.append($(this).attr('name'), $(this)[0].files[0]);
                                });

                                console.log(mdata);
                                $.ajax({
                                    url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                                    type: 'POST',
                                    contentType: false,
                                    data: mdata,
                                    //async: false,
                                    processData: false,
                                    cache: false,
                                    success: function(data) {
                                        //alert(data);
                                        if (data == "OK") {
                                            alert("Se " + ((accion == "ins") ? "almaceno" : "edito") + " la notificacion correctamente");
                                            limpiarForm();
                                            location.reload();
                                        } else {
                                            alert("error al " + ((accion == "ins") ? "almacenar" : "editar") + " la notificaion");

                                        }
                                    }
                                });
                            }
                        }

                    } else {
                        acordionopen = $("#obs_notifiID").parents("DIV").parents("DIV").attr("id");
                        openClass = $("#" + acordionopen).children("h3").hasClass("ui-state-active");
                        if (openClass == false) {
                            $("#" + acordionopen).accordion({ active: 0 });
                        }
                        $("#obs_notifiID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
                        //return false;
                    }
                } else {
                    acordionopen = $("#fec_vigencID").parents("DIV").parents("DIV").attr("id");
                    openClass = $("#" + acordionopen).children("h3").hasClass("ui-state-active");
                    if (openClass == false) {
                        $("#" + acordionopen).accordion({ active: 0 });
                    }
                    $("#fec_vigencID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
                    //return false;
                }
            } else {
                acordionopen = $("#ind_notusrID").parents("DIV").parents("DIV").attr("id");
                openClass = $("#" + acordionopen).children("h3").hasClass("ui-state-active");
                if (openClass == false) {
                    $("#" + acordionopen).accordion({ active: 0 });
                }
                $("#ind_notusrID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
                //return false;
            }
        } else {
            acordionopen = $("#cod_asiresID").parents("DIV").parents("DIV").attr("id");
            openClass = $("#" + acordionopen).children("h3").hasClass("ui-state-active");
            if (openClass == false) {
                $("#" + acordionopen).accordion({ active: 0 });
            }
            $("#cod_asiresID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
            //return false;
        }
    } else {
        acordionopen = $("#nom_asuntoID").parents("DIV").parents("DIV").attr("id");
        openClass = $("#" + acordionopen).children("h3").hasClass("ui-state-active");
        if (openClass == false) {
            $("#" + acordionopen).accordion({ active: 0 });
        }
        $("#nom_asuntoID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
        //return false;
    }
    //alert(limitCampos("string","#nom_asuntoID","<=","3"));
}

/*! \fn: ValidateFormExt
 *  \brief: valida el formulario y genera el json para los formularios extendidos
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: cod_tipnot tipo de notificacion a procesar (3 = Supervisores, 4 = Controladores)
 *  \return: 
 */
function ValidateFormExt(cod_tipnot) {
    var mdata = new FormData();
    mdata.append("option", "NuevaNotifiComun");
    var status = {};
    status.NOTNULL = [];
    var jsonArray = {};
    if (cod_tipnot == 3) {
        jsonArray.SUPERVISORES = {};
        $("#jsonFormDigi").find("input[type=text], textarea").each(function() {
            dato = $(this);
            if ($(this).val() != "") {
                jsonArray.SUPERVISORES[dato.attr('name')] = dato.val();
            } else {
                status.NOTNULL.push(dato.attr('name'), dato.val());
            }
        });
        jsonArray.CONTROLADORES = {};
        $("#jsonContro").find("input[type=text], textarea").each(function() {
            dato = $(this);
            if ($(this).val() != "") {
                jsonArray.CONTROLADORES[dato.attr('name')] = dato.val();
            } else {
                status.NOTNULL.push(dato.attr('name'), dato.val());
            }
        });
        /*jsonArray.ENCUESTAS = {};
        $("#jsonEncu").find("input[type=text]").each(function() {
            dato = $(this);
            if ($(this).val() != "") {
                jsonArray.ENCUESTAS[dato.attr('name')] = dato.val();
            } else {
                status.NOTNULL.push(dato.attr('name'), dato.val());
            }
        });*/
        jsonArray.ESPECIFICAS = {};
        $("#jsonEspeci").find("input[type=text], textarea").each(function() {
            dato = $(this);
            if ($(this).val() != "") {
                jsonArray.ESPECIFICAS[dato.attr('name')] = dato.val();
            } else {
                status.NOTNULL.push(dato.attr('name'), dato.val());
            }
        });
        jsonArray.ASISTENCIAS = {};
        $("#jsonAsist").find("input[type=text]").each(function() {
            dato = $(this);
            if ($(this).val() != "") {
                jsonArray.ASISTENCIAS[dato.attr('name')] = dato.val();
            } else {
                status.NOTNULL.push(dato.attr('name'), dato.val());
            }
        });
    }
    jsonArray.ESTADO_VEHICULOS = {};
    $("#jsonEstVehi").find("input[type=text]").each(function() {
        dato = $(this);
        if ($(this).val() != "") {
            jsonArray.ESTADO_VEHICULOS[dato.attr('name')] = dato.val();
        } else {
            status.NOTNULL.push(dato.attr('name'), dato.val());
        }
    });
    jsonArray.RECURSOS_ASIGNADOS = {};
    $("#jsonRecurAsi").find("input[type=text]").each(function() {
        dato = $(this);
        if ($(this).val() != "") {
            jsonArray.RECURSOS_ASIGNADOS[dato.attr('name')] = dato.val();
        } else {
            status.NOTNULL.push(dato.attr('name'), dato.val());
        }
    });
    if (status.NOTNULL.length > 0) {
        return status;
    } else {
        return jsonArray;
    }
}

/*! \fn: validateChekBox
 *  \brief: valida los campos seleccionados en el multiselect
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: campoval tipo de multselect a procesar 
 *  \return: 
 */
function validateChekBox(campoval) {
    var cod_Respon = "";
    var box_checke = $("input[type=checkbox]:checked");
    box_checke.each(function(i, o) {
        if ($(this).attr("name") == 'multiselect_' + campoval) {
            if ($(this).val() != "0") {
                cod_Respon += ",'" + $(this).val() + "'";
            }
        }
    });
    return cod_Respon;
}

/*! \fn: getNomUsuario
 *  \brief: optine los usuarios que son seleccionados del multiselect de responsables
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function getNomUsuario() {
    $('.error').fadeOut();
    $("#ind_notusrID").multiselect('destroy');
    var cod_Respon = validateChekBox("cod_asiresID");
    var standa = $("#standaID").val();
    var formData = "option=getNomUsuario&standa=" + standa + "&cod_Respon=" + cod_Respon;
    $.ajax({
        url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
        type: "POST",
        data: formData,
        async: true,
        cache: false,
        success: function(data) {
            $("#ind_notusrID").html(data);
            $("#ind_notusrID").multiselect('destroy');
            $("#ind_notusrID").multiselect().multiselectfilter();
        }
    });
}

/*! \fn: cerrarAlert
 *  \brief: cierra los alert de los campos que no cumplen con las condiciones
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function cerrarAlert() {
    $(".error-notifi").fadeOut();
}

/*! \fn: editarNotifi
 *  \brief: captura las campos de la tabla y genera el formuario a editar
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: row datos qeu se encuentan en la tabla
 *  \return: 
 */
function editarNotifi(row) {
    var objeto = $(row).parent().parent();
    var cod_notifi = objeto.find("input[id^=cod_notifi]").val();
    var nom_asunto = objeto.find("input[id^=nom_asunto]").val().toUpperCase();
    var cod_tipnot = objeto.find("input[id^=cod_tipnot]").val();
    var ind_notres = objeto.find("input[id^=ind_notres]").val();
    var ind_notusr = objeto.find("input[id^=ind_notusr]").val();
    var temp_ind_notres = ind_notres.split(',');
    var temp_ind_notusr = ind_notusr.split(',');
    var standa = $("#standaID").val();
    $("#popID").remove();
    closePopUp('popID');
    LoadPopupJQNoButton('open', 'EDITAR NOTIFICACION ' + cod_notifi, ($(window).height() - 40), ($(window).width() - 40), false, false, true);
    var popup = $("#popID");
    if (cod_notifi != "" && nom_asunto != "" && cod_tipnot != "") {
        var formData = "option=getFormNuevaNotifi&standa=" + standa + "&cod_notifi=" + cod_notifi + "&nom_asunto=" + nom_asunto + "&idForm=" + cod_tipnot + "&ActionForm=idi";
        popup.html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
        setTimeout(function() {
            $.ajax({
                url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                type: "POST",
                data: formData,
                async: true,
                cache: false,
                beforeSend: function(obj) {
                    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                },
                success: function(data) {
                    setTimeout(function() {
                        popup.html(data);
                        $("#cod_asiresID").multiselect().multiselectfilter();
                        preFunction();
                        $("input[name=multiselect_cod_asiresID]").each(function(i, v) {
                            for (var xx = 0; xx < temp_ind_notres.length; xx++) {
                                if (temp_ind_notres[xx] == $(this).val()) {
                                    //console.log($(this));
                                    $(this).attr("aria-selected", true);
                                    $(this).attr("checked", true);
                                }

                            }
                        });
                        getNomUsuario();
                    }, 1000);
                },
                complete: function() {
                    $("#ind_notusrID").multiselect().multiselectfilter();
                    setTimeout(function() {
                        $("input[name=multiselect_ind_notusrID]").each(function(i, v) {
                            for (var yy = 0; yy < temp_ind_notusr.length; yy++) {
                                if (temp_ind_notusr[yy] == $(this).val()) {
                                    //console.log($(this));
                                    $(this).attr("aria-selected", true);
                                    $(this).attr("checked", true);
                                }
                            }
                        });
                    }, 5000);
                }
            });
        }, 4000);
    } else {
        alert("error al editar usuario");
    }
}

/*! \fn: FormeliminarNotifi
 *  \brief: captura las campos de la tabla y genera el formuario a eliminar
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: row datos qeu se encuentan en la tabla
 *  \return: 
 */
function FormeliminarNotifi(row) {
    var objeto = $(row).parent().parent();
    var cod_notifi = objeto.find("input[id^=cod_notifi]").val();
    var nom_asunto = objeto.find("input[id^=nom_asunto]").val().toUpperCase();
    var cod_tipnot = objeto.find("input[id^=cod_tipnot]").val();
    var ind_notres = objeto.find("input[id^=ind_notres]").val();
    var ind_notusr = objeto.find("input[id^=ind_notusr]").val();
    var temp_ind_notres = ind_notres.split(',');
    var temp_ind_notusr = ind_notusr.split(',');
    var standa = $("#standaID").val();
    $("#popID").remove();
    closePopUp('popID');
    LoadPopupJQNoButton('open', 'ELIMINAR NOTIFICACION ' + cod_notifi, ($(window).height() - 40), ($(window).width() - 40), false, false, true);
    var popup = $("#popID");
    if (cod_notifi != "" && nom_asunto != "" && cod_tipnot != "") {
        var formData = "option=getFormNuevaNotifi&standa=" + standa + "&cod_notifi=" + cod_notifi + "&nom_asunto=" + nom_asunto + "&idForm=" + cod_tipnot + "&ActionForm=eli";
        popup.html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
        setTimeout(function() {
            $.ajax({
                url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                type: "POST",
                data: formData,
                async: true,
                cache: false,
                beforeSend: function(obj) {
                    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                },
                success: function(data) {
                    setTimeout(function() {
                        popup.html(data);
                        $("#cod_asiresID").multiselect().multiselectfilter();
                        preFunction();
                        $("input[name=multiselect_cod_asiresID]").each(function(i, v) {
                            for (var xx = 0; xx < temp_ind_notres.length; xx++) {
                                if (temp_ind_notres[xx] == $(this).val()) {
                                    //console.log($(this));
                                    $(this).attr("aria-selected", true);
                                    $(this).attr("checked", true);
                                }
                            }
                        });
                        getNomUsuario();
                    }, 1000);
                },
                complete: function() {
                    $("#ind_notusrID").multiselect().multiselectfilter();
                    setTimeout(function() {
                        $("input[name=multiselect_ind_notusrID]").each(function(i, v) {
                            for (var yy = 0; yy < temp_ind_notusr.length; yy++) {
                                if (temp_ind_notusr[yy] == $(this).val()) {
                                    //console.log($(this));
                                    $(this).attr("aria-selected", true);
                                    $(this).attr("checked", true);
                                }
                            }
                        });
                    }, 5000);
                }
            });
        }, 4000);
    } else {
        alert("No se pudo cargar el formulario");
    }
}

/*! \fn: eliminarNotifi
 *  \brief: valida el formulario a elimiar y ejecuta el proceso
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function eliminarNotifi() {
    var standa = $("#standaID").val();
    var nom_asunto = $("#nom_asuntoID").val().toUpperCase();
    var fec_creaci = $("#fec_creaciID").val();
    var fec_vigenc = $("#fec_vigencID").val();
    var ind_respue = $("#ind_respueID").val();
    var obs_notifi = $("#obs_notifiID").val();
    var cod_tipnot = $("#cod_tipnotID").val();
    var cod_notifi = $("#cod_notifiID").val();
    var cod_usuari = $("#cod_usuari").val();
    var usr_creaci = $("#usr_creaciID").val();
    var confirmacion = confirm("Desea eliminar la notificacion?");
    if (confirmacion) {
        if (nom_asunto != "" && cod_tipnot != "" && cod_notifi != "" && cod_usuari != "") {
            var formDataEli = "option=elimiNotifi&standa=" + standa + "&cod_notifi=" + cod_notifi + "&nom_asunto=" + nom_asunto + "&cod_tipnot=" + cod_tipnot + "&fec_creaci=" + fec_creaci + "&fec_vigenc=" + fec_vigenc + "&ind_respue=" + ind_respue + "&obs_notifi=" + obs_notifi + "&usr_creaci" + usr_creaci + "&ActionForm=eli";
            $.ajax({
                url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                type: "POST",
                data: formDataEli,
                async: true,
                cache: false,
                success: function(data) {
                    if (data == "OK") {
                        alert("Se elimino la notificacion correctamente");
                        limpiarForm();
                        location.reload();
                    } else {
                        alert("Error al eliminar la plantilla");
                    }

                }
            });
        }
    }
}

/*! \fn: FormResponNotifi
 *  \brief: captura las campos de la tabla y genera el formuario a reponder
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: row datos qeu se encuentan en la tabla
 *  \return: 
 */
function FormResponNotifi(row) {
    var objeto = $(row).parent().parent();
    var cod_notifi = objeto.find("input[id^=cod_notifi]").val();
    var nom_asunto = objeto.find("input[id^=nom_asunto]").val().toUpperCase();
    var cod_tipnot = objeto.find("input[id^=cod_tipnot]").val();
    var ind_notres = objeto.find("input[id^=ind_notres]").val();
    var ind_notusr = objeto.find("input[id^=ind_notusr]").val();
    var temp_ind_notres = ind_notres.split(',');
    var temp_ind_notusr = ind_notusr.split(',');
    var standa = $("#standaID").val();
    $("#popID").remove();
    closePopUp('popID');
    LoadPopupJQNoButton('open', 'RESPONDER NOTIFICACION ' + cod_notifi, ($(window).height() - 40), ($(window).width() - 40), false, false, true);
    var popup = $("#popID");
    if (cod_notifi != "" && nom_asunto != "" && cod_tipnot != "") {
        var formData = "option=getFormNuevaNotifi&standa=" + standa + "&cod_notifi=" + cod_notifi + "&nom_asunto=" + nom_asunto + "&idForm=" + cod_tipnot + "&ActionForm=rep";
        popup.html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
        setTimeout(function() {
            $.ajax({
                url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                type: "POST",
                data: formData,
                async: true,
                cache: false,
                beforeSend: function(obj) {
                    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                },
                success: function(data) {
                    setTimeout(function() {
                        popup.html(data);
                        $("#cod_asiresID").multiselect().multiselectfilter();
                        preFunction();
                        $("input[name=multiselect_cod_asiresID]").each(function(i, v) {
                            for (var xx = 0; xx < temp_ind_notres.length; xx++) {
                                if (temp_ind_notres[xx] == $(this).val()) {
                                    //console.log($(this));
                                    $(this).attr("aria-selected", true);
                                    $(this).attr("checked", true);
                                }
                            }
                        });
                        getNomUsuario();
                    }, 1000);
                },
                complete: function() {
                    $("#ind_notusrID").multiselect().multiselectfilter();
                    setTimeout(function() {
                        $("input[name=multiselect_ind_notusrID]").each(function(i, v) {
                            for (var yy = 0; yy < temp_ind_notusr.length; yy++) {
                                if (temp_ind_notusr[yy] == $(this).val()) {
                                    //console.log($(this));
                                    $(this).attr("aria-selected", true);
                                    $(this).attr("checked", true);
                                }
                            }
                        });
                    }, 5000);
                }
            });
        }, 4000);
    } else {
        alert("No se pudo cargar el formulario");
    }
}

/*! \fn: responNotifi
 *  \brief: valida el formulario a responder y ejecuta el proceso
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \return: 
 */
function responNotifi() {
    var standa = $("#standaID").val();
    var nom_asunto = $("#nom_asuntoID").val().toUpperCase();
    var cod_tipnot = $("#cod_tipnotID").val();
    var cod_notifi = $("#cod_notifiID").val();
    var obs_respon = $("#obs_responID").val();
    var confirmacion = confirm("Desea Responder la notificacion?");
    if (confirmacion) {
        if (obs_respon != "") {
            if (nom_asunto != "" && cod_tipnot != "" && cod_notifi != "" && obs_respon != "") {
                var formDataRes = "option=responderNotifi&standa=" + standa + "&cod_notifi=" + cod_notifi + "&nom_asunto=" + nom_asunto + "&obs_respon=" + obs_respon + "&idForm=" + cod_tipnot + "&ActionForm=rep";
                $.ajax({
                    url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                    type: "POST",
                    data: formDataRes,
                    async: true,
                    cache: false,
                    success: function(data) {
                        if (data == "OK") {
                            alert("Su respuesta fue enviada correctamente");
                            limpiarForm();
                            location.reload();
                        } else {
                            alert("Error al responder la notificacion");
                        }
                    }
                });
            } else {
                alert("Los campos obligatorios no se encuentan");
            }
        } else {
            $("#obs_responID").focus().after('<span class="error-notifi" onclick="cerrarAlert()">Campo Requerido</span>');
        }
    }
}

/*! \fn: verArchivos
 *  \brief: procesa la visualizacion de archivos
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: cod_consec identificador del archivo a eliminar
 *  \return: 
 */
function verArchivos(cod_consec) {
    var standa = $("#standaID").val();
    var cod_notifi = $("#cod_notifiID").val();
    window.open("index.php?window=central&cod_servic=20151235&menant=20151235&cod_consec=" + cod_consec + "&cod_notifi=" + cod_notifi, '_blank');
}

/*! \fn: delArchivos
 *  \brief: captura el identificador el archivo a elimar y genera el proceso
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: cod_consec identificador del archivo a eliminar
 *  \return: 
 */
function delArchivos(cod_consec) {
    var standa = $("#standaID").val();
    var cod_tipnot = $("#cod_tipnotID").val();
    var cod_notifi = $("#cod_notifiID").val();
    var confirmacion = confirm("Desea eliminar el archivo adjunto?");
    if (confirmacion) {
        if (cod_tipnot != "" && cod_notifi != "") {
            var formDataEli = "option=elimiDocument&standa=" + standa + "&cod_notifi=" + cod_notifi + "&cod_tipnot=" + cod_tipnot + "&cod_consec=" + cod_consec;
            $.ajax({
                url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                type: "POST",
                data: formDataEli,
                async: true,
                cache: false,
                success: function(data) {
                    if (data == "OK") {
                        alert("Se elimino el archivo correctamente");
                        $("#Efile_" + cod_consec + "ID").parent().parent().html("<td id='tag0TD' class='celda_titulo' rowspan='' colspan='1' width='' valign='' height='' align='right'><label onfocus='this.className='campo_texto_on';' onblur='this.className='campo_texto';' name='tag0' id='tag0ID' colspan='1'>ADJUNTO :</label></td><td id='file_2IDTD' class='celda_info' rowspan='' colspan='6' width='100%' valign='' height='' align='left'><input name='file_" + cod_consec + "' id='file_" + cod_consec + "ID' colspan='6' type='file'></td>");
                    } else {
                        alert("Error al eliminar el documento");
                    }
                }
            });
        } else {
            alert("los campos de referencias se encuantran vacios");
        }
    }
}

/*! \fn: CambioForm
 *  \brief: cambia el formulario en el tab supervisor y controlador dependiento la notificacion 
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: ind identificador formulario a cambiar
 *  \return: 
 */
function CambioForm(ind) {
    var standa = $("#standaID").val();
    var cod_tipnot = $("#cod_tipnotID").val();
    var formData = "option=getFormNuevaNotifi&standa=" + standa + "&idForm=" + cod_tipnot + "&radio=" + ind + "&ActionForm=ins";
    closePopUp('popID');
    LoadPopupJQNoButton('open', 'NUEVA NOTIFICACION', ($(window).height() - 40), ($(window).width() - 40), false, false, true);
    var popup = $("#popID");
    $.ajax({
        url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
        type: "POST",
        data: formData,
        async: true,
        cache: false,
        beforeSend: function(obj) {
            popup.html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
            popup.parent().children().children('.ui-dialog-titlebar-close').hide();
        },
        success: function(data) {
            popup.html(data);
            $("#cod_asiresID").multiselect().multiselectfilter();
            $("#ind_notusrID").multiselect().multiselectfilter();
            preFunction();
        }
    });
    //alert("fn");
}

/*! \fn: FormResponNotifi
 *  \brief: captura las campos de la tabla y genera el formuario a reponder
 *  \author: Edward Serrano
 *  \date: 17/01/2017
 *  \date modified: dia/mes/año
 *  \param: row datos qeu se encuentan en la tabla
 *  \return: 
 */
function verNotifi(row) {
    var objeto = $(row).parent().parent();
    var cod_notifi = objeto.find("input[id^=cod_notifi]").val();
    var nom_asunto = objeto.find("input[id^=nom_asunto]").val().toUpperCase();
    var cod_tipnot = objeto.find("input[id^=cod_tipnot]").val();
    var ind_notres = objeto.find("input[id^=ind_notres]").val();
    var ind_notusr = objeto.find("input[id^=ind_notusr]").val();
    var temp_ind_notres = ind_notres.split(',');
    var temp_ind_notusr = ind_notusr.split(',');
    var standa = $("#standaID").val();
    $("#popID").remove();
    closePopUp('popID');
    LoadPopupJQNoButton('open', 'VER NOTIFICACION ' + cod_notifi, ($(window).height() - 40), ($(window).width() - 40), false, false, true);
    var popup = $("#popID");
    if (cod_notifi != "" && nom_asunto != "" && cod_tipnot != "") {
        var formData = "option=getFormNuevaNotifi&standa=" + standa + "&cod_notifi=" + cod_notifi + "&nom_asunto=" + nom_asunto + "&idForm=" + cod_tipnot + "&ActionForm=ver";
        popup.html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
        setTimeout(function() {
            $.ajax({
                url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                type: "POST",
                data: formData,
                async: true,
                cache: false,
                beforeSend: function(obj) {
                    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                },
                success: function(data) {
                    setTimeout(function() {
                        popup.html(data);
                        $("#cod_asiresID").multiselect().multiselectfilter();
                        preFunction();
                        $("input[name=multiselect_cod_asiresID]").each(function(i, v) {
                            for (var xx = 0; xx < temp_ind_notres.length; xx++) {
                                if (temp_ind_notres[xx] == $(this).val()) {
                                    //console.log($(this));
                                    $(this).attr("aria-selected", true);
                                    $(this).attr("checked", true);
                                }
                            }
                        });
                        getNomUsuario();
                    }, 1000);
                },
                complete: function() {
                    $("#ind_notusrID").multiselect().multiselectfilter();
                    setTimeout(function() {
                        $("input[name=multiselect_ind_notusrID]").each(function(i, v) {
                            for (var yy = 0; yy < temp_ind_notusr.length; yy++) {
                                if (temp_ind_notusr[yy] == $(this).val()) {
                                    //console.log($(this));
                                    $(this).attr("aria-selected", true);
                                    $(this).attr("checked", true);
                                }
                            }
                        });
                    }, 5000);
                }
            });
        }, 4000);
    } else {
        alert("No se pudo cargar el formulario");
    }
}