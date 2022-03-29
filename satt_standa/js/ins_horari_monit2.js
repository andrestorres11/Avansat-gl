/*! \file: monit
 *  \brief: archivo con multiples funciones jquey para el archivo ins_horari_monito.php
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 05/02/2016
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */


/*! \fn: 
 *  \brief: funcion que ese ejecuta cuando la vista php a cargado completamente    
 *  \author: Ing. Alexander Correa
 *   \date: 05/02/2016
 *   \date modified: dia/mes/año
 *  \param: 
 */
var val = true;
$(function() {
    $(".date").datepicker({
        dateFormat: 'yy-mm-dd'
    });
    $(".hora").timepicker({
        timeFormat: "hh:mm",
        showSecond: false
    });
    $("body").removeAttr("class");
    $(".accordion").accordion({
        collapsible: true,
        heightStyle: "content",
        icons: {
            "header": "ui-icon-circle-arrow-e",
            "activeHeader": "ui-icon-circle-arrow-s"
        }
    }).click(function() {
        $("body").removeAttr("class");
    });
    $("#contenido").css({
        height: 'auto'
    });
    var standa = $("#standa").val();
    $("#nom_usuari0ID").autocomplete({
        source: "../" + standa + "/config/ajax_horari_monito.php?Option=buscarUsuario&Ajax=on",
        minLength: 3,
        select: function(event, ui) {
            $("#cod_consec0ID").val(ui.item.id);
            $("body").removeAttr("class");
        }
    });
    $("#nom_usuari0IDfil").autocomplete({
        source: "../" + standa + "/config/ajax_horari_monito.php?Option=buscarUsuario&Ajax=on",
        minLength: 3,
        select: function(event, ui) {
            $("#cod_consecfil0ID").val(ui.item.id);
            $("body").removeAttr("class");
        }
    });
    getDataList();
});


/*! \fn: deleteDiv
 *  \brief: funcion para eliminar una linea completa del formulario de insercion
 *  \author: Ing. Alexander Correa
 *   \date: 05/02/2016
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */

function deleteDiv(indice) {
    $("#div" + indice).remove();
    $("#total").val($("#total").val() - 1);
}


/*! \fn: addUserForm
 *  \brief: funcion para agregar una linea completa al formulario de insercion
 *  \author: Ing. Alexander Correa
 *   \date: 05/02/2016
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */
function addUserForm() {
    var total = $("#total").val();
    var standa = $("#standa").val();
    var datos = ' <div class="col-md-12" id="div' + total + '">';
    datos += '<div class="col-md-1 derecha" >Usuario<font style="color:red">*</font>: </div>';
    datos += '<div class="col-md-1 izquierda"><input type="text" id="nom_usuari' + total + 'ID" validate="dir" obl="1" maxlength="30" minlength="5" name="nom_usuari[]" class="ancho"><input type="hidden" name="cod_consec[]" id="cod_consec' + total + 'ID" value=""></div>';
    datos += '<div class="col-md-1 derecha">Fecha y Hora de Inicio<font style="color:red">*</font>:</div>';
    datos += '<div class="col-md-3 izquierda">';
    datos += '<div class="col-md-7"><input class="ancho date" validate="date" obl="1" maxlength="10" minlength="10" type="text" name="fecini[]" id="fecini' + total + '"></div>';
    datos += '<div class="col-md-5"><input class="ancho hora" validate="dir" obl="1" maxlength="5" minlength="5" type="text" name="horini[]" id="horini' + total + '"></div>';
    datos += '</div>';
    datos += '<div class="col-md-2 derecha">Fecha y Hora de salida<font style="color:red">*</font>:</div>';
    datos += '<div class="col-md-3 izquierda">';
    datos += '<div class="col-md-7"><input class="ancho date" validate="date" obl="1" maxlength="10" minlength="10" type="text" name="fecsal[]" id="fecsal' + total + '" ></div>';
    datos += '<div class="col-md-5"><input class="ancho hora" validate="dir" obl="1" maxlength="5" minlength="5" type="text" name="horsal[]" id="horsal' + total + '" ></div>';
    datos += '</div>';
    datos += '<div class="col-md-1"><img width="16px" height="16px" src="../' + standa + '/images/delete.png" onclick="deleteDiv(' + total + ')"></div>';
    datos += '</div>';
    if (total > 0) {
        $("#div" + (total - 1)).after(datos);
    } else {
        $("#primero").append(datos);
    }
    $(".date").datepicker({
        dateFormat: 'yy-mm-dd'
    });
    $(".hora").timepicker({
        timeFormat: "hh:mm",
        showSecond: false
    });
    var consec = "#cod_consec" + total + "ID";
    var usuari = "#nom_usuari" + total + "ID";
    $(usuari).autocomplete({
        source: "../" + standa + "/config/ajax_horari_monito.php?Option=buscarUsuario&Ajax=on",
        minLength: 3,
        select: function(event, ui) {
            $(consec).val(ui.item.id);
            $("body").removeAttr("class");
        }
    });
    $("#total").val(++total);
}


/*! \fn: registrar
 *  \brief: funcion para almacenar los datos en la base de datos
 *  \author: Ing. Alexander Correa
 *   \date: 11/02/2016
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */

function registrar() {
    var conn = checkConnection();
    if (conn) {
        var standa = $("#standa").val();
        val = validaciones();
        var usuarios = new Array();
        $('input[id^=fecini]').each(function(x, obj) {
            var fecini = $("#fecini" + x).val();
            var fecsal = $("#fecsal" + x).val();
            var horini = $("#horini" + x).val();
            var horsal = $("#horsal" + x).val();
            fecini = fecini + " " + horini + ":00";
            fecsal = fecsal + " " + horsal + ":59";
            var usuario = $("#nom_usuari" + x + "ID").val();
            if (!getUsuario(usuarios, usuario)) {
                usuarios.push(usuario);
                $.ajax({
                    type: "POST",
                    url: "../" + standa + "/config/ajax_horari_monito.php",
                    data: "&Ajax=on&Option=comprobar&standa=" + standa + "&fec_inicio=" + fecini + "&fec_finali=" + fecsal + "&usuario=" + usuario,
                    async: false,
                    success: function(datos) {
                        if (datos == 1) {
                            setTimeout(function() {
                                inc_alerta("fecsal" + x, "Cruce de horario.");
                            }, 510);
                            val = false;
                        }
                    }
                });
            } else {
                setTimeout(function() {
                    inc_alerta("nom_usuari" + x + "ID", "Usuario repetido.");
                }, 510);
                val = false;
            }
            fecini = new Date(fecini.replace(/-/g, '/'));
            fecsal = new Date(fecsal.replace(/-/g, '/'));
            if (fecini < fecsal) {
                var timeDiff = Math.abs(fecsal.getTime() - fecini.getTime());
                var diffHours = (timeDiff / (1000 * 3600));
                if (diffHours > 12) {
                    setTimeout(function() {
                        inc_alerta("fecini" + x, "La diferencia entre fechas no puede ser mayor a 12 horas.");
                    }, 510);
                    val = false;
                }
            } else {
                setTimeout(function() {
                    inc_alerta("fecini" + x, "Fecha inicial mayor a la final.");
                }, 510);
                val = false;
            }
        });
        if (val) {
            var parametros = getDataForm();
            var standa = $("#standa").val();
            swal({
                title: "Registrar Pre-planeaci\u00F3n carga laboral",
                text: "\u00BFRealmente Deseas registrar las configuraciones ingresadas?",
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                $.ajax({
                    type: "POST",
                    url: "../" + standa + "/config/ajax_horari_monito.php",
                    data: "&Ajax=on&Option=registrar&standa=" + standa + "&" + parametros,
                    async: true,
                    success: function(datos) {
                        if (datos == 1) {
                            swal({
                                title: "Registrar Pre-planeaci\u00F3n carga laboral",
                                text: "Datos Registrados con Éxito",
                                type: "info",
                                showCancelButton: false,
                                closeOnConfirm: true,
                                showLoaderOnConfirm: true,
                            }, function() {
                                limpiar();
                                getDataList();
                            });

                        } else {
                            swal("Error al registrar los datos.");
                        }
                    }
                });
            });

        }
    } else {
        swal({
            title: "Registrar Pre-planeaci\u00F3n carga laboral",
            text: "Por favor verifica tu conexi\u00F3n a internet.",
            type: "error"
        });

    }

}


/*! \fn: getDataList
 *  \brief: funcion que trae los datos ya almacenados en la base de datos
 *  \author: Ing. Alexander Correa
 *   \date: 11/02/2016
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */

function getDataList() {

    var fechini=$("#fechinifiltro").val();
    var fechafin=$("#fechfinfiltro").val();
    var userprog=$("#cod_consecfil0ID").val();

       
    var conn = checkConnection();
    if (conn) {
        var standa = $("#standa").val();

        $.ajax({
            type: "POST",
            url: "../" + standa + "/config/ajax_horari_monito.php",
            data: "&Ajax=on&Option=getDataList&standa=" + standa + "&fechini=" + fechini + "&fechafin=" + fechafin + "&userprog=" + userprog ,
            async: false,
            beforeSend: function() {
                $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
            },
            success: function(datos) {
                $("#lista").html("");
                $("#lista").append(datos)
            },
            complete: function() {
                $("#sec2").css("height", "auto !important");
                $(".accordion").accordion({
                    collapsible: true,
                    heightStyle: "content",
                    icons: {
                        "header": "ui-icon-circle-arrow-e",
                        "activeHeader": "ui-icon-circle-arrow-s"
                    }
                }).click(function() {
                    $("body").removeAttr("class");
                });
            }
        });
        $("#sec2").removeAttr('style');


    } else {
        swal("Por favor verifica tu conexi\u00F3n a internet.");
    }
}


/*! \fn: getUsuario
 *  \brief: funcion para verificar que no se repita un usaurio en el formulario de registro
 *  \author: Ing. Alexander Correa
 *   \date: 15/02/2016
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */

function getUsuario(usuarios, usuario) {
    var aux = new Array();
    aux.push(usuario);
    var x = false;
    for (var i = 0; i < usuarios.length; i++) {
        if (usuarios[i] === aux[0]) {
            x = true;
            break;
        }
    }
    return x;
}

/*! \fn: EliminarConfiguracion 
 *  \brief: elimina una configuracion registrada
 *  \author: Ing. Alexander Correa
 *   \date: 16/02/2016
 *   \date modified: dia/mes/año
 *  \param: 
 *  \param: 
 *  \return 
 */
function EliminarConfiguracion(obj) {
    var standa = $("#standa").val();
    var DLRow = $(obj).parent().parent();
    var cod_consec = DLRow.find("input[id^=cod_consec]").val();
    swal({
        title: "Registrar Pre-planeaci\u00F3n carga laboral",
        text: "\u00BFRealmente Deseas eliminar la configuraci\u00F3n seleccionada?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function() {
        $.ajax({
            type: "POST",
            url: "../" + standa + "/config/ajax_horari_monito.php",
            data: "&Ajax=on&Option=EliminarConfiguracion&cod_consec=" + cod_consec,
            async: true,
            success: function(datos) {
                if (datos == 1) {
                    swal({
                        title: "Registrar Pre-planeaci\u00F3n carga laboral",
                        text: "Datos Eliminados con Éxito",
                        type: "info",
                        showCancelButton: false,
                        closeOnConfirm: true,
                        showLoaderOnConfirm: true,
                    }, function() {
                        getDataList();

                    });

                } else {
                    swal("Error al eliminar los datos.");
                }
            }
        });
    });
}

function limpiar() {
    $('input[type=text]').each(function() {
        $(this).val("");
    });
}
function valfiltro() {
    var fechini=$("#fechinifiltro").val();
    var fechafin=$("#fechfinfiltro").val();
    var userprog=$("#cod_consecfil0ID").val();

    if (fechini !='' && fechafin !='' ){
        getDataList();
    }else{
        alert("llene todos los datos de fechas");
    }
    
}