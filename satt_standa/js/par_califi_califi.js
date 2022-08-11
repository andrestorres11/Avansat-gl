/*! \file: par_califi_califi.js
 *  \brief: JS para calificaciones de Despachos y Usuarios
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 09/02/2016
 *  \bug: 
 *  \warning: 
 */

/*! \fn: formAuditarDespac
 *  \brief: Hace la peticion Ajax para crear el formulario de auditar despacho y lo pinta en un Popup
 *  \author: Ing. Fabian Salinas
 *  \date: 09/02/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function formAuditarDespac() {
    try {
        var standar = $("#central").val();
        var attributes = 'Ajax=on&Option=formAuditarDespac';

        closePopUp('popupAuditarID');
        LoadPopupJQNoButton('open', 'Auditar Despacho', 'auto', 'auto', true, true, false, 'popupAuditarID');
        var popup = $("#popupAuditarID");

        $.ajax({
            url: "../" + standar + "/califi/class_califi_califi.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.html("<center><img src=\"../" + standar + "/imagenes/ajax-loader.gif\"></center>");
            },
            success: function(datos) {
                popup.html(datos);
            }
        });
    } catch (e) {
        console.log("Error Function formAuditarDespac: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/* ! \fn: CreatePartic
 *  \brief: funcion para insertar una particularidad
 *  \author: Ing. Andres Martinez
 *  \date: 08/02/2018
 *  \date modified: dia/mes/año
 *  \param: cod_tercer     => string => codigo de la empresa   
 *  \param: cod_ciudad     => string => email cliente     
 *  \return return
 */
function CreateObserva(num_despac, cod_transp) {
    try {
        var conn = checkConnection();

        if (conn) {
            var standa = $("#central").val();
            $("#popapObservacionID").dialog({
                modal: true,
                resizable: false,
                draggable: false,
                title: "Creación de Observacion",
                width: 800,
                heigth: 500,
                position: ['middle', 25],
                bgiframe: true,
                closeOnEscape: false,
                show: {
                    effect: "drop",
                    duration: 300
                },
                hide: {
                    effect: "drop",
                    duration: 300
                },
                open: function(event, ui) {
                    $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
                },
                buttons: {
                    Guardar: function() {
                        NewObserva(num_despac, cod_transp);
                    },
                    Cerrar: function() {
                        $(this).dialog('close');
                    }
                }
            });
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/califi/class_califi_califi.php",
                data: "Ajax=on&Option=CreateObserva" + "&cod_transp=" + cod_transp + "&num_despac=" + num_despac + "&ind_edicio=0",
                async: true,
                beforeSend: function() {
                    $("#popapObservacionID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
                },
                success: function(datos) {
                    if (datos == '1') {
                        closePopUp("popapObservacionID");
                        swal.fire({
                            title: "Creación de Observacion",
                            text: "Se creo la Observacion correctamente.",
                            type: "success"
                        });
                    } else {
                        $("#popapObservacionID").html(datos);
                    }

                }
            });
        } else {
            swal.fire({
                title: "Parametrización",
                text: "Por favor verifica tu conexión a internet.",
                type: "warning"
            });

        }
    } catch (e) {
        console.log(e.message);
        return false;
    }
}

function NewObserva(num_despac, cod_transp) {
    var conn = checkConnection();
    var ind_config = 0;
    contador = 0;

    if (conn) {
        var standa = $("#central").val();
        var obs_obsgen = $("#des_observID").val();

        var errores = false;
        if (!obs_obsgen) {
            setTimeout(function() {
                inc_alerta("des_observID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!errores) {
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/califi/class_califi_califi.php",
                data: "Ajax=on&Option=NewObserva" + "&cod_transp=" + cod_transp + "&num_despac=" + num_despac + "&obs_obsgen=" + obs_obsgen,
                async: true,
                beforeSend: function() {
                    $.blockUI({
                        theme: true,
                        title: 'Registrando Observacion',
                        draggable: false,
                        message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Registrando...</p></center>'
                    });
                },
                success: function(datos) {
                    $.unblockUI();
                    if (datos == '1000') {
                        $("#popapObservacionID").dialog('close');
                        swal.fire({
                            title: "Parametrización",
                            text: "Observacion registrada con éxito.",
                            type: "success",
                            icon: "success",
                            showCancelButton: false,
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                        $("#popapObservacionID").dialog('close');
                    } else {
                        swal.fire({
                            title: "Observacion",
                            text: "Error al registrar la configuración, por favor intenta nuevamente.",
                            type: "warning"
                        });
                        swal.fire("");
                    }
                }

            });
        }
    } else {
        swal.fire({
            title: "Parametrización",
            text: "Por favor verifica tu conexión a internet.",
            type: "warning"
        });
    }
}

/* ! \fn: CreatePartic
 *  \brief: funcion para insertar una particularidad
 *  \author: Ing. Andres Martinez
 *  \date: 08/02/2018
 *  \date modified: dia/mes/año
 *  \param: cod_tercer     => string => codigo de la empresa   
 *  \param: cod_ciudad     => string => email cliente     
 *  \return return
 */
function EditGPS(num_despac) {
    try {
        var conn = checkConnection();

        if (conn) {
            var standa = $("#central").val();
            $("#popupGpsID").dialog({
                modal: true,
                resizable: false,
                draggable: false,
                title: "Editar Operador GPS",
                width: 800,
                heigth: 500,
                position: ['middle', 25],
                bgiframe: true,
                closeOnEscape: false,
                show: {
                    effect: "drop",
                    duration: 300
                },
                hide: {
                    effect: "drop",
                    duration: 300
                },
                open: function(event, ui) {
                    $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
                },
                buttons: {
                    Editar: function() {
                        editaGps(num_despac);
                    },
                    Cerrar: function() {
                        $(this).dialog('close');
                    }
                }
            });
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/califi/class_califi_califi.php",
                data: "Ajax=on&Option=EditGPS&standa=" + standa + "&num_despac=" + num_despac + "&ind_edicio=1",
                async: true,
                beforeSend: function() {
                    $("#popupGpsID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
                },
                success: function(datos) {
                    if (datos == '1') {
                        closePopUp("popupGpsID");
                        swal({
                            title: "Actualizacion GPS",
                            text: "Se Actualizo correctamente.",
                            type: "success"

                        });
                    } else {
                        $("#popupGpsID").html(datos);
                    }

                },
                complete: function() {
                    $("#ui-multiselect-cod_agenciID-option-0").click();
                }
            });
        } else {
            swal({
                title: "Parametrización",
                text: "Por favor verifica tu conexión a internet.",
                type: "warning"
            });

        }
    } catch (e) {
        console.log(e.message);
        return false;
    }
}

function habIdOperaGps(elemento) {
    var standa = $("#central").val();
    var cod_opegps = $(elemento).val();
    $.ajax({
        type: "POST",
        url: "../" + standa + "/califi/class_califi_califi.php",
        data: "Ajax=on&Option=IndUsaIDGPS&standa=" + standa + "&cod_opegps=" + cod_opegps,
        async: true,
        success: function(data) {
            if (data != '') {
                var datos = $.parseJSON(data);
                if (datos[0][0] == 'S') {
                    $("#gps_idxxxxID").attr("validate", "text");
                    $("#gps_idxxxxID").attr("obl", "1");
                    $("#gps_idxxxxID").attr("minlength", "1");
                    $("#gps_idxxxxID").attr("maxlength", "15");
                } else {
                    $("#gps_idxxxxID").removeAttr("validate");
                    $("#gps_idxxxxID").removeAttr("obl");
                    $("#gps_idxxxxID").removeAttr("minlength");
                    $("#gps_idxxxxID").removeAttr("maxlength");
                }
            }
        }
    });
}


/* ! \fn: editaGps
 *  \brief: registra un contacto en el sistema
 *  \author: Ing. Torres
 *  \date: 13/02/2018
 *  \date modified: dia/mes/año
 *  \param: ind_config     => int    => indicador de la configuracion que se quiere regstrar    
 *  \param: cod_ciudad     => string => indicador de la ciudad para la que aplica la configuracion  
 *  \return 
 */
function editaGps(num_despac) {

    var conn = checkConnection();

    if (conn) {
        var standa = $("#central").val();
        var gps_operad = $("#cod_operadEditID").val();
        var gps_usuari = $("#gps_usuariID").val();
        var gps_paswor = $("#gps_pasworID").val();
        var gps_idxxxx = $("#gps_idxxxxID").val();

        var attrigpd = $("#gps_idxxxxID").attr('obl');
        var errores = false;
        if (!gps_operad) {
            setTimeout(function() {
                inc_alerta("cod_operadEditID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!gps_usuari) {
            setTimeout(function() {
                inc_alerta("gps_usuariID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!gps_paswor) {
            setTimeout(function() {
                inc_alerta("gps_pasworID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (attrigpd == 1) {
            if (!gps_idxxxx) {
                setTimeout(function() {
                    inc_alerta("gps_idxxxxID", "Campo Obligatorio.");
                }, 511);
                errores = true;
            }
        }
        if (!errores) {
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/califi/class_califi_califi.php",
                data: "Ajax=on&Option=editaGps&standa=" + standa + "&gps_operad=" + gps_operad + "&gps_usuari=" + gps_usuari + "&gps_paswor=" + gps_paswor + "&gps_idxxxx=" + gps_idxxxx + "&num_despac=" + num_despac,
                async: true,
                beforeSend: function() {
                    $.blockUI({
                        theme: true,
                        title: 'Actualizando Operador GPS',
                        draggable: false,
                        message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Actualizando...</p></center>'
                    });
                },
                success: function(datos) {
                    $.unblockUI();
                    if (datos == '1000') {
                        swal.fire({
                            title: "Parametrización",
                            text: "Operador GPS Actualizado con éxito.",
                            type: "success",
                            icon: "success",
                            showCancelButton: false,
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                        $("#popupGpsID").dialog('close');
                    } else {
                        swal.fire({
                            title: "Operador Gps",
                            text: "Error al registrar la configuración, por favor intenta nuevamente.",
                            type: "warning"
                        });
                        swal.fire("");
                    }
                }

            });
        }
    } else {
        swal.fire({
            title: "Parametrización",
            text: "Por favor verifica tu conexión a internet.",
            type: "warning"
        });
    }
}

/*! \fn: getListActivi
 *  \brief: Hace la Peticion Ajax para traer la lista de actividades y las agrega las opciones al select
 *  \author: Ing. Fabian Salinas
 *  \date: 09/02/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function getListActivi(obj) {
    try {
        var standar;
        var OptCalifi;

        if ($("#central").length < 1) {
            standar = $("#standaID").val();
            OptCalifi = '';
        } else {
            standar = $("#central").val();
            OptCalifi = 'tableCalifiDespac()';
        }

        var attributes = 'Ajax=on&Option=getListActivi';
        attributes += '&cod_operac=' + obj.val();
        attributes += '&fun_javasc=' + OptCalifi;

        $.ajax({
            url: "../" + standar + "/califi/class_califi_califi.php",
            type: "POST",
            data: attributes,
            async: true,
            success: function(datos) {
                $("#cod_activiIDTD").html(datos);
            },
            complete: function() {
                if ($("#central").length > 1)
                    tableCalifiDespac();
                else
                    tableCalifiUsuari();
            }
        });
    } catch (e) {
        console.log("Error Function getListActivi: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: tableCalifiDespac
 *  \brief: Hace la peticion Ajax para traer la tabla de calificacion despachos
 *  \author: Ing. Fabian Salinas
 *  \date: 09/02/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function tableCalifiDespac() {
    try {
        cod_activi = $("#popupAuditarID #cod_activiID").val();
        tab_califi = $("#popupAuditarID #tab_auditaID");

        if (cod_activi == '') {
            tab_califi.html('');
        } else {
            var standar = $("#central").val();
            var attributes = 'Ajax=on&Option=tableCalifiDespac';
            attributes += '&cod_activi=' + cod_activi;
            attributes += '&num_despac=' + $("#despac").val();

            $.ajax({
                url: "../" + standar + "/califi/class_califi_califi.php",
                type: "POST",
                data: attributes,
                async: true,
                success: function(datos) {
                    tab_califi.html(datos);
                }
            });
        }
    } catch (e) {
        console.log("Error Function tableCalifiDespac: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: califiDespac
 *  \brief: Captura los datos del formulario y hace la peticion ajax para guardar la calificacion del despacho
 *  \author: Ing. Fabian Salinas
 *  \date: 09/02/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function califiDespac(cod_activi, cod_itemsx) {
    try {
        //Inicio tratamiento de datos
        var radio;
        var stop = false;
        var cod = [];
        var val = [];
        var por = [];
        var usr = [];
        var cna = 0; //Cantida de NA
        var pna = 0; //Porcentaje acumulado NA
        var tsi = 0; //Total porcentaje Si
        var tno = 0; //Total porcentaje No
        var standar = $("#central").val();
        var attributes = 'Ajax=on&Option=insertCalifiDesUsu';

        attributes += '&num_despac=' + $("#despac").val();
        attributes += '&cod_activi=' + cod_activi;
        attributes += '&obs_califi=' + $("#popupAuditarID #obs_califiID").val();


        cod_itemsx = cod_itemsx.split('|');
        $.each(cod_itemsx, function(key, i) {
            radio = $("#popupAuditarID #ind0_opcion" + i + "ID:checked");
            usr[key] = $("#popupAuditarID #usr0_califi" + i + "ID").val();

            if (!radio.val()) {
                stop = true;
                inc_alerta("ind0_opcion" + i + "ID", "Seleccione una Opcion");
            } else {
                cod[key] = radio.attr('cod_itemsx');
                val[key] = radio.val();
                por[key] = radio.attr('val_porcen');

                if (radio.val() == 'NA') {
                    pna += parseInt(radio.attr('val_porcen'));
                    cna++;
                }
            }
        });

        if (stop == true)
            return false;

        pna = pna / (cod.length - cna);
        pna = Math.round(pna, 1);

        var sum = 0;
        for (var i = 0; i < cod.length; i++) {
            attributes += '&itemsx[cod][' + i + ']=' + cod[i];
            attributes += '&itemsx[val][' + i + ']=' + val[i];
            attributes += '&itemsx[usr][' + i + ']=' + usr[i];

            if (val[i] == 'NA')
                attributes += '&itemsx[por][' + i + ']=0';
            else {
                sum = parseInt(por[i]) + pna;
                attributes += '&itemsx[por][' + i + ']=' + sum;

                if (val[i] == 'SI')
                    tsi += sum;
                else
                    tno += sum;
            }
        };

        attributes += '&itemsx[tsi]=' + tsi;
        attributes += '&itemsx[tno]=' + tno;
        attributes += '&val_cumpli=' + tsi;
        //Fin tratamiento de datos

        //Inicia Ajax
        LoadPopupCalifi('open', 'Auditar Despacho', 'auto', 'auto', true, true, false);
        var popup = $("#popID");

        $.ajax({
            url: "../" + standar + "/califi/class_califi_califi.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.html("<center><img src=\"../" + standar + "/imagenes/ajax-loader.gif\"></center>");
            },
            success: function(datos) {
                if (datos != '1')
                    popup.html(datos);
                else {
                    popup.html('Auditoria registrada con exito.');
                    tableCalifiDespac();
                }
            }
        });
        //Fin Ajax
    } catch (e) {
        console.log("Error Function califiDespac: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: califiDespac
 *  \brief: Captura los datos del formulario y hace la peticion ajax para guardar la calificacion del usuario
 *  \author: Ing. Fabian Salinas
 *  \date: 09/02/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function califiUsuari(cod_activi, cod_itemsx) {
    try {
        var radio;
        var stop = false;
        var cod = [];
        var val = [];
        var por = [];
        var usr = [];
        var cna; //Cantida de NA
        var pna; //Porcentaje acumulado NA
        var tsi; //Total porcentaje Si
        var tno; //Total porcentaje No
        var standar = $("#standaID").val();
        var idConte = '';
        var usu = '';
        var num = '';
        var attributes = 'Ajax=on&Option=insertCalifiDesUsu';

        attributes += '&cod_activi=' + cod_activi;

        cod_itemsx = cod_itemsx.split('|');

        //<Recorre tablas>
        var x = 0;
        $(".tab_porcalif").each(function() {
            cna = 0; //Cantida de NA
            pna = 0; //Porcentaje acumulado NA
            tsi = 0; //Total porcentaje Si
            tno = 0; //Total porcentaje No

            idConte = $(this).attr('id');
            usu = $(this).attr('name');
            usr[x] = usu;
            num = $(this).attr('consec');

            $.each(cod_itemsx, function(key, i) {
                radio = $("#" + idConte + " #ind" + num + "_opcion" + i + "ID:checked");

                if (!radio.val()) {
                    stop = true;
                    inc_alerta("ind" + num + "_opcion" + i + "ID", "Seleccione una Opcion");
                } else {
                    cod[key] = radio.attr('cod_itemsx');
                    val[key] = radio.val();
                    por[key] = radio.attr('val_porcen');

                    if (radio.val() == 'NA') {
                        pna += parseInt(radio.attr('val_porcen'));
                        cna++;
                    }
                }
            });

            pna = pna / (cod.length - cna);
            pna = Math.round(pna, 1);

            var sum = 0;
            for (var i = 0; i < cod.length; i++) {
                attributes += '&itemsx[' + usu + '][cod][' + i + ']=' + cod[i];
                attributes += '&itemsx[' + usu + '][val][' + i + ']=' + val[i];

                if (val[i] == 'NA')
                    attributes += '&itemsx[' + usu + '][por][' + i + ']=0';
                else {
                    sum = parseInt(por[i]) + pna;
                    attributes += '&itemsx[' + usu + '][por][' + i + ']=' + sum;

                    if (val[i] == 'SI')
                        tsi += sum;
                    else
                        tno += sum;
                }
            };

            attributes += '&itemsx[' + usu + '][tsi]=' + tsi;
            attributes += '&itemsx[' + usu + '][tno]=' + tno;
            attributes += '&obs_califi[' + usu + ']=' + $("#" + idConte + " #obs_califiID").val();
            x++;
        });
        //</Recorre tablas>

        if (stop == true)
            return false;

        attributes += '&usr_califi=' + usr.join('|');


        //<Ajax>
        LoadPopupCalifi('open', 'Auditar Usuarios', 'auto', 'auto', true, true, false);
        var popup = $("#popID");

        $.ajax({
            url: "../" + standar + "/califi/class_califi_califi.php",
            type: "POST",
            data: attributes,
            async: false,
            beforeSend: function() {
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.html("<center><img src=\"../" + standar + "/imagenes/ajax-loader.gif\"></center>");
            },
            success: function(datos) {
                if (datos != '1')
                    popup.html(datos);
                else {
                    popup.html('Auditoria registrada con exito.');
                    tableCalifiUsuari();
                }
            }
        });
        //</Ajax>
    } catch (e) {
        console.log("Error Function califiUsuari: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: tableCalifiDespac
 *  \brief: Hace la peticion Ajax para traer la tabla de calificacion usuarios
 *  \author: Ing. Fabian Salinas
 *  \date: 09/02/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function tableCalifiUsuari() {
    try {
        var val = validaciones();
        var ban = true;
        var usu = getUsuariSelect();

        if (usu.length < 1) {
            inc_alerta("cod_consecID", "Debe selecionar por lo menos una opciÃ³n");
            ban = false;
        }

        if (!ban || !val)
            return false;

        var standar = $("#standaID").val();
        var attributes = 'Ajax=on&Option=tableCalifiUsuari';
        attributes += '&cod_activi=' + $("#cod_activiID").val();
        attributes += '&usr_califi=' + usu.join('|');

        $.ajax({
            url: "../" + standar + "/califi/class_califi_califi.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Generando Formulario...', true);
            },
            success: function(datos) {
                $("#formCalifiID").html(datos);
            },
            complete: function() {
                $("#secCalifiID").css({
                    height: 'auto'
                        //color: 'black'
                });
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Function tableCalifiUsuari: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: getUsuariSelect
 *  \brief: Trae los usuarios seleccionados en el multiselect
 *  \author: Ing. Fabian Salinas
 *  \date: 09/02/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: Array
 */
function getUsuariSelect() {
    try {
        var usu = [];
        var j = 0;

        $("input[type=checkbox]:checked").each(function(i, o) {
            if ($(this).attr("name") == 'multiselect_cod_consecID' && $(this).val() != '') {
                usu[j] = $(this).val();
                j++;
            }
        });

        return usu;
    } catch (e) {
        console.log("Error Function getUsuariSelect: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: LoadPopupCalifi
 *  \brief: Crea o destruye PopUp
 *  \author: Ing. Fabian Salinas
 *  \date: 24/06/2016
 *  \date modified: dia/mes/año
 *  \param: opcion   String   open, close
 *  \param: titulo   String   Titulo del PopUp
 *  \param: alto     Integer  Altura PopUp
 *  \param: ancho    Integer  Ancho PopUp
 *  \param: redimen  Boolean  True = Redimencionable
 *  \param: dragg    Boolean  True = El PopUp se puede arrastras
 *  \param: lockBack Boolean  True = Bloquea el BackGround
 *  \return: 
 */
function LoadPopupCalifi(opcion, titulo, alto, ancho, redimen, dragg, lockBack) {
    try {
        if (opcion == 'hidden') {
            $("#popID").dialog('close');
        } else if (opcion == 'close') {
            $("#popID").dialog("destroy").remove();
        } else {
            $("<div id='popID' name='pop' />").dialog({
                height: alto,
                width: ancho,
                modal: lockBack,
                title: titulo,
                closeOnEscape: false,
                resizable: redimen,
                draggable: dragg,
                buttons: {
                    Cerrar: function() {
                        LoadPopupCalifi('close')
                    }
                }
            });
        }
    } catch (e) {
        console.log("Error Function LoadPopupCalifi: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

function redirectUrl(url) {
    window.location.href = url;
}

/* ! \fn: ReeItiner
 *  \brief: funcion para realizar el envio del itinerario
 *  \author: Ing. Cristian Andrés Torres
 *  \date: 02/06/2022
 *  \date modified: dia/mes/año
 *  \param: num_despac   => string => número del despacho     
 *  \param: num_placax   => string => número de placa      
 *  \return return
 */
function ReeItiner(num_despac, num_placax) {
    try {
        var conn = checkConnection();

        if (conn) {
            var standa = $("#central").val();
            swal.fire({
                title: "Confirmación",
                text: "¿Está seguro de reenviar el itinerario?",
                type: "warning",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var attributes = 'Ajax=on&Option=reeItiner';
                    attributes += '&num_despac=' + num_despac;
                    attributes += '&num_placax=' + num_placax;
                    $.ajax({
                        url: "../" + standa + "/califi/class_califi_califi.php",
                        type: "POST",
                        data: attributes,
                        dataType: "json",
                        async: true,
                        beforeSend: function() {
                            swal.close();
                            Swal.fire({
                                title: 'Cargando',
                                text: 'Por favor espere...',
                                imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
                                imageAlt: 'Custom image',
                                showConfirmButton: false,
                            });
                        },
                        success: function(resp) {
                            swal.fire({
                                title: resp['title'],
                                text: resp['text'],
                                type: resp['type'],
                                icon: resp['type'],
                                html: resp['info'],
                                showCancelButton: false,
                                confirmButtonText: 'Aceptar'
                            })
                        },
                    });

                }
            })
        } else {
            swal({
                title: "Parametrización",
                text: "Por favor verifica tu conexión a internet.",
                type: "warning"
            });

        }
    } catch (e) {
        console.log(e.message);
        return false;
    }
}

/* ! \fn: ReeItiner
 *  \brief: funcion para realizar el reenvio de novedades a tms
 *  \author: Ing. Cristian Andrés Torres
 *  \date: 02/08/2022
 *  \date modified: dia/mes/año
 *  \param: num_despac   => string => número del despacho   
 *  \return return
 */
function ReeNovedades(num_despac) {
    try {
        var conn = checkConnection();

        if (conn) {
            var standa = $("#central").val();
            var cod_servic = $("#cod_servic").val();
            var cod_aplica = $("#cod_aplica").val();
            swal.fire({
                title: "Confirmación",
                text: "¿Está seguro de reenviar las novedades?",
                type: "warning",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var attributes = 'Ajax=on&Option=reeNovedades&cod_servic='+cod_servic+'&cod_aplica='+cod_aplica;
                    attributes += '&num_despac=' + num_despac;
                    $.ajax({
                        url: "../" + standa + "/califi/class_califi_califi.php",
                        type: "POST",
                        data: attributes,
                        dataType: "json",
                        async: true,
                        beforeSend: function() {
                            swal.close();
                            Swal.fire({
                                title: 'Cargando',
                                text: 'Por favor espere...',
                                imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
                                imageAlt: 'Custom image',
                                showConfirmButton: false,
                            });
                        },
                        success: function(resp) {
                            swal.fire({
                                title: resp['title'],
                                text: resp['msj'],
                                type: resp['type'],
                                icon: resp['type'],
                                html: resp['info'],
                                showCancelButton: false,
                                confirmButtonText: 'Aceptar'
                            })
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            var msj = '';
                            if (jqXHR.status === 0) {
                                msj = 'No hay conexión: Verifique su red.';
                              } else if (jqXHR.status == 404) {
                                msj = 'La página a la que intenta acceder no existe [404]';
                              } else if (jqXHR.status == 500) {
                                msj = 'Error interno del servidor [500].';
                              } else if (textStatus === 'parsererror') {
                                msj = 'Requested JSON parse failed.';
                              } else if (textStatus === 'timeout') {
                                msj = 'Tiempo de espera excedido';
                              } else if (textStatus === 'abort') {
                                msj = 'Petición Cancelada';
                              } else {
                                msj = 'Uncaught Error: ' + jqXHR.responseText;
                              }

                            swal.fire({
                                title: 'Error',
                                text: msj,
                                type: 'error',
                                icon: 'error',
                                showCancelButton: false,
                                confirmButtonText: 'Aceptar'
                            })
                        }
                    });

                }
            })
        } else {
            swal({
                title: "Parametrización",
                text: "Por favor verifica tu conexión a internet.",
                type: "warning"
            });

        }
    } catch (e) {
        console.log(e.message);
        return false;
    }
}