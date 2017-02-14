$(function() {
    $("#sec1").css("height", "auto");
    $("#sec2").css("height", "auto");
    $("#contenido").css("height", "auto");
    var standa = $("#standaID").val();
    var attributes = '&Ajax=on&standa=' + standa;
    $("#usuarioID").autocomplete({
        source: "../" + standa + "/extenc/ajax_extenc_extenc.php?Option=buscarUsuario" + attributes,
        minLength: 3,
        select: function(event, ui) {
            $("#usr_extenc").val(ui.item.id);
            $("body").removeAttr("class");
        }
    });

    $("#acordeonID").accordion({
        heightStyle: "content",
        collapsible: true,
    });

    $("#fec_iniciaID, #fec_finaliID").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
    });

    $("#contenido").css("height", "auto");

    $("#tabs").tabs({
        beforeLoad: function(event, ui) {
            ui.jqXHR.fail(function() {
                ui.panel.html("Cargado...");
            });
        }
    });


    $("body").removeAttr("class");

});

function informeLlamadasEntrantes(pestana) {
    var fec_inicia = $("#fec_iniciaID").val();
    var fec_finali = $("#fec_finaliID").val();
    var cod_operac = $("#cod_operacID").val();
    var errores = false;
    //valido la fecha inicial y la final
    if (!fec_inicia) {
        setTimeout(function() {
            inc_alerta("fec_iniciaID", "Por favor ingresa una fecha inicial");
        }, 510);
        errores = true;
    }
    if (!cod_operac) {
        setTimeout(function() {
            inc_alerta("cod_operacID", "Por favor selecciona un tipo de operación");
        }, 510);
        errores = true;
    }
    if (!fec_finali) {
        setTimeout(function() {
            inc_alerta("fec_iniciaID", "Por favor ingresa una fecha final");
        }, 510);
        errores = true;
    }
    if (Date.parse(fec_inicia) <= Date.parse(fec_finali)) { //valido que  la inicial sea menor a la final
        if (errores == false) {
            var conn = checkConnection(); //valido la conexion a internet
            if (conn == true) {
                var num_celula = $("#num_celulaID").val();
                var standa = $("#standaID").val();
                //llamo a la funcion ajax para pintar los datos 
                var formData = "Option=informeLlamadasEntrantes&Ajax=on&fec_inicia=" + fec_inicia + "&fec_finali=" + fec_finali + "&cod_operac=" + cod_operac + "&num_celula=" + num_celula + "&pestana=" + pestana;
                $.ajax({
                    url: "../" + standa + "/extenc/ajax_extenc_extenc.php",
                    type: "POST",
                    data: formData,
                    async: true,
                    beforeSend: function(obj) {
                        $("#" + pestana).html('');
                        $.blockUI({
                            theme: true,
                            title: 'Informe de Llamadas Entrantes',
                            draggable: false,
                            message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Generando Reporte</p></center>'
                        });
                    },
                    success: function(data) {
                        $.unblockUI();
                        $("#" + pestana).append(data);
                    }
                });
            } else {
                setTimeout(function() {
                    inc_alerta("ocultos", "No tienes conexión a internet por favor verifica.");
                }, 510);
            }
        }
    } else {
        setTimeout(function() {
            inc_alerta("fec_iniciaID", "La fecha inicial no puede ser mayor a la final");
        }, 510);
    }
}

function registrarOperacion() {
    var val = validaciones();
    if (val == true) {
        var parametros = getDataForm();
        var standa = $("#standaID").val();
        var attributes = "&standa=" + standa + "&Ajax=on&Option=registrarOperacion&" + parametros;
        var conn = checkConnection();
        if (conn == true) {
            $.ajax({
                url: "../" + standa + "/extenc/ajax_extenc_extenc.php",
                type: "POST",
                data: attributes,
                async: false,
                success: function(data) {
                    if (data == 0) { // configuracion ya registrada
                        var src = "../" + standa + "/imagenes/bad.png";
                        var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<label>Extensiones</label>";
                        mensaje += "<div style='width:97%'>";
                        mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Operación ya registrada para el usuario seleccionado <br> debe inactivar primero la existente.<br><img src='" + src + "'>";
                        mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
                    } else if (data == 1) { // procedimiento correcto
                        var src = "../" + standa + "/imagenes/ok.png";
                        var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<label>Extensiones</label>";
                        mensaje += "<div style='width:97%'>";
                        mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Se registró la operación Exitosamente.<br></font><br><img src='" + src + "'>";
                        mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='formulario();' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
                    } else if (data == 2) { // error en el registro
                        var src = "../" + standa + "/imagenes/bad.png";
                        var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<label>Extensiones</label>";
                        mensaje += "<div style='width:97%'>";
                        mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Ocurrió un Error inesperado <br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font><br><img src='" + src + "'>";
                        mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
                    }
                    LoadPopupJQNoButton('open', 'Resultado de la Operación', 'auto', 'auto', false, false, true);
                    var popup = $("#popID");
                    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                    popup.append(mensaje); // //lanza el popUp
                }

            });
        } else {
            setTimeout(function() {
                inc_alerta("registrar", "No tienes conexión a internet, por favor verifica");
            }, 510);
        }
    }
}

function registrarGrupo() {
    var val = validaciones();
    if (val == true) {
        var parametros = getDataForm();
        var standa = $("#standaID").val();
        var attributes = "&standa=" + standa + "&Ajax=on&Option=registrarGrupo&" + parametros;
        var conn = checkConnection();
        if (conn == true) {
            $.ajax({
                url: "../" + standa + "/extenc/ajax_extenc_extenc.php",
                type: "POST",
                data: attributes,
                async: false,
                success: function(data) {
                    if (data == 0) { // configuracion ya registrada
                        var src = "../" + standa + "/imagenes/bad.png";
                        var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<label>Grupos</label>";
                        mensaje += "<div style='width:97%'>";
                        mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Grupo ya registrada para el usuario seleccionado <br> debe inactivar primero la existente.<br><img src='" + src + "'>";
                        mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
                    } else if (data == 1) { // procedimiento correcto
                        var src = "../" + standa + "/imagenes/ok.png";
                        var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<label>Grupos</label>";
                        mensaje += "<div style='width:97%'>";
                        mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Se registró el grupo Exitosamente.<br></font><br><img src='" + src + "'>";
                        mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='formulario();' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
                    } else if (data == 2) { // error en el registro
                        var src = "../" + standa + "/imagenes/bad.png";
                        var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<label>Grupos</label>";
                        mensaje += "<div style='width:97%'>";
                        mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Ocurrió un Error inesperado <br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font><br><img src='" + src + "'>";
                        mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
                    }
                    LoadPopupJQNoButton('open', 'Resultado de la Operación', 'auto', 'auto', false, false, true);
                    var popup = $("#popID");
                    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                    popup.append(mensaje); // //lanza el popUp
                }

            });
        } else {
            setTimeout(function() {
                inc_alerta("registrar", "No tienes conexión a internet, por favor verifica");
            }, 510);
        }
    }
}

function registrar() {
    var val = validaciones();
    if (val == true) {
        var parametros = getDataForm();
        var standa = $("#standaID").val();
        var tipo = $("cod_tipox").val();
        var usr_extenc = $("#usr_extenc").val();
        var attributes = "&standa=" + standa + "&Ajax=on&Option=registrarExtencion&usr_extenc=" + usr_extenc + "&" + parametros;
        var conn = checkConnection();
        if (conn == true) {
            $.ajax({
                url: "../" + standa + "/extenc/ajax_extenc_extenc.php",
                type: "POST",
                data: attributes,
                async: false,
                success: function(data) {
                    if (data == 0) { // configuracion ya registrada
                        var src = "../" + standa + "/imagenes/bad.png";
                        var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<label>Extensiones</label>";
                        mensaje += "<div style='width:97%'>";
                        mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Configuración ya registrada para el usuario seleccionado <br> debe inactivar primero la existente.<br><img src='" + src + "'>";
                        mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
                    } else if (data == 1) { // procedimiento correcto
                        var src = "../" + standa + "/imagenes/ok.png";
                        var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<label>Extensiones</label>";
                        mensaje += "<div style='width:97%'>";
                        mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Se registró la extensión Exitosamente.<br></font><br><img src='" + src + "'>";
                        mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='formulario();' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
                    } else if (data == 2) { // error en el registro
                        var src = "../" + standa + "/imagenes/bad.png";
                        var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                        mensaje += "<label>Extensiones</label>";
                        mensaje += "<div style='width:97%'>";
                        mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Ocurrió un Error inesperado <br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font><br><img src='" + src + "'>";
                        mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
                    }
                    LoadPopupJQNoButton('open', 'Resultado de la Operación', 'auto', 'auto', false, false, true);
                    var popup = $("#popID");
                    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                    popup.append(mensaje); // //lanza el popUp
                }

            });
        } else {
            setTimeout(function() {
                inc_alerta("registrar", "No tienes conexión a internet, por favor verifica");
            }, 510);
        }
    }
}

function editarConexion(tipo, objeto) {
    var DLRow = $(objeto).parent().parent();
    var cod_extenc = DLRow.find("input[id^=cod_extenc]").val();
    var usr_extenc = DLRow.find("input[id^=usr_extenc]").val();
    $("#usrID").val(usr_extenc);
    $("#extencID").val(cod_extenc);
    if (tipo == 2) {
        confirmar2('inactivar');
    } else if (tipo == 1) {
        inc_alerta("registrar", "Una vez inactivada una extensión no se puede activar. Debes crear una nueva");
    }

}

function editarOperacion(tipo, objeto) {
    var DLRow = $(objeto).parent().parent();
    var cod_operac = DLRow.find("input[id^=cod_operac]").val();
    var nom_operac = DLRow.find("input[id^=nom_operac]").val();
    $("#codID").val(cod_operac);
    $("#operacID").val(nom_operac);
    if (tipo == 2) {
        confirmar('inactivar');
    } else if (tipo == 1) {
        confirmar('activar');
    }

}

function editarGrupo(tipo, objeto) {
    var DLRow = $(objeto).parent().parent();
    var cod_grupox = DLRow.find("input[id^=cod_grupox]").val();
    var nom_grupox = DLRow.find("input[id^=nom_grupox]").val();
    $("#codgrID").val(cod_grupox);
    $("#grupoxID").val(nom_grupox);
    if (tipo == 2) {
        confirmar3('inactivar');
    } else if (tipo == 1) {
        confirmar3('activar');
    }

}
//funcion de confirmacion para la edicion, eliminacion e inactivacion de conductores
function confirmar(operacion) {

    LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', 'auto', false, false, true);
    var popup = $("#popID");
    var cod_operac = $("#codID").val();
    var nomo_perac = $("#operacID").val();
    var onclick = "onclick='register(\"";
    onclick += operacion + "Operacion";
    onclick += "\")'";
    var msj = "<div style='text-align:center'>¿Está seguro de <b>" + operacion + "</b> la operacion: <b>" + nomo_perac + "?</b><br><br><br><br>";
    msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' " + onclick + " class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/> &nbsp;&nbsp;&nbsp;&nbsp";
    msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><div>";

    popup.parent().children().children('.ui-dialog-titlebar-close').hide();

    popup.append(msj); // //lanza el popUp
}

function confirmar2(operacion) {

    LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', 'auto', false, false, true);
    var popup = $("#popID");
    var usuario = $("#usrID").val();
    var extenci = $("#extencID").val();
    var onclick = "onclick='register(\"";
    onclick += operacion;
    onclick += "\")'";
    var msj = "<div style='text-align:center'>¿Está seguro de <b>" + operacion + "</b> la operacion de: <b>" + usuario + "?</b><br><br><br><br>";
    msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' " + onclick + " class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/> &nbsp;&nbsp;&nbsp;&nbsp";
    msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><div>";

    popup.parent().children().children('.ui-dialog-titlebar-close').hide();

    popup.append(msj); // //lanza el popUp
}

function confirmar3(operacion) {

    LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', 'auto', false, false, true);
    var popup = $("#popID");
    var grupox = $("#grupoxID").val();

    var onclick = "onclick='register(\"";
    onclick += operacion + "Grupo";
    onclick += "\")'";
    var msj = "<div style='text-align:center'>¿Está seguro de <b>" + operacion + "</b> el grupo: <b>" + grupox + "?</b><br><br><br><br>";
    msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' " + onclick + " class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/> &nbsp;&nbsp;&nbsp;&nbsp";
    msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><div>";

    popup.parent().children().children('.ui-dialog-titlebar-close').hide();

    popup.append(msj); // //lanza el popUp
}

function register(operacion) {
    var standa = $("#standaID").val();
    //cierra popUp si hay inicialiado
    LoadPopupJQNoButton('close');
    //valido los datos generales del formulario
    //crea el popUp para el mensaje de  respuesta del guardado 
    LoadPopupJQNoButton('open', 'Resultado de la Operación', 'auto', 'auto', false, false, true);
    var popup = $("#popID");
    var extenci = $("#extencID").val();
    var operaci = $("#codID").val();
    var grupox = $("#codgrID").val();
    var formData = "Option=" + operacion + "&Ajax=on&cod_extenc=" + extenci + "&cod_operac=" + operaci + "&cod_grupox=" + grupox;
    $.ajax({
        url: "../" + standa + "/extenc/ajax_extenc_extenc.php",
        type: "POST",
        data: formData,
        async: true,
        beforeSend: function(obj) {
            $.blockUI({
                theme: true,
                title: 'Informe de Llamadas Entrantes',
                draggable: false,
                message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Generando Detalle</p></center>'
            });
            popup.parent().children().children('.ui-dialog-titlebar-close').hide();
        },
        success: function(data) {
            $.unblockUI();
            popup.append(data); // lanza el popUp
        }
    });
}

function detalle(cod_operac, fec_inicia, fec_finali, tipo_consulta) {
    var fecha_inicial = $("#fec_iniciaID").val();
    var fecha_final = $("#fec_finaliID").val();
    var formData = "Option=GetDetalle&Ajax=on&cod_operac=" + cod_operac + "&fec_inicia=" + fec_inicia + "&fec_finali=" + fec_finali + "&tipo=" + tipo_consulta + "&fecha_inicial=" + fecha_inicial + "&fecha_final=" + fecha_final + "&num_celula=" + $("#num_celulaID").val();
    var standa = $("#standaID").val();
    //cierra popUp si hay inicialiado
    closePopUp('popID');
    //valido los datos generales del formulario
    //crea el popUp para el mensaje de  respuesta del guardado 
    LoadPopupJQNoButton('open', 'Detalle', ($(window).height() - 40), ($(window).width() - 40), false, false, true);
    var popup = $("#popID");
    var conn = checkConnection(); //valido la conexion a internet
    if (conn == true) {
        $.ajax({
            url: "../" + standa + "/extenc/ajax_extenc_extenc.php",
            type: "POST",
            data: formData,
            async: true,
            beforeSend: function(obj) {
                BlocK('Generando Informe...', true);
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            success: function(data) {
                popup.html(data);
                BlocK();
            }
        });
    } else {
        setTimeout(function() {
            inc_alerta("ocultos", "No tienes conexión a internet por favor verifica.");
        }, 510);
    }
}

function cerrarElPopUp() {
    LoadPopupJQNoButton('close');
}

function getFileAudio(telefono, consecutivo, standa) {
    try {
        PopUpJuery2("open");
        $.ajax({
            url: "../" + standa + "/extenc/ajax_extenc_extenc.php",
            data: "Ajax=on&Option=LoadCallPlay&cod_consec=" + consecutivo + "&num_telefo=" + telefono,
            type: "post",
            success: function(data) {
                $("#DialogCallID").html(data);
            }
        });

    } catch (e) {
        alert(e.message + " - " + e.lineNumber);
    }
}

function PopUpJuery2(option) {
    try {
        if (option == "open") {
            $("<div id=\'DialogCallID\'><center>Cargando Audio...<br>Por favor espere</center></div>").dialog({
                modal: true,
                resizable: false,
                draggable: false,
                closeOnEscape: false,
                width: "324px",
                height: "100px",
                title: "Reproductor de Llamadas",
                open: function() {
                    $("#DialogCallID").css({ height: "60px" });
                },
                close: function() {
                    PopUpJuery2("close");
                }
            })
        } else {
            $("#DialogCallID").dialog("destroy").remove();
        }

    } catch (e) {
        alert(e.message + " - " + e.lineNumber);
    }
}

/*! \fn: pintarExcel
 *  \brief: Solicita la generacion del documento Excel
 *  \author: Ing. Fabian Salinas
 *  \date: 06/04/2016
 *  \date modified: dd/mm/aaaa
 *  \param: idTable  String  ID de la tabla a Exportar
 *  \return: 
 */
function pintarExcel(idTable) {
    $("#"+idTable+" img").remove();
    var pop = $(".ui-dialog").length;

    tabla = "<table>" + $("#"+idTable).html() + "</table>";
    $('#exportExcelID').val(tabla);
    $("#formulario").submit();

    if( pop < 1 ){
        informeLlamadasEntrantes('generaID');
    }else{
        closePopUp();
    }

}
