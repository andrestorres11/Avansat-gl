var pop = "popupConfirPernocID";
var urlBase;

/*! \fn: verifyConfirPernoc
 *  \brief: Verifica si la transportadora tiene parametrizado la Verificacion de Pernoctacion
 *  \author: Ing. Fabian Salinas
 *  \date: 07/03/2016
 *  \date modified: dd/mm/aaaa
 *  \param: ind_origen  Integer  Indica de donde llaman la función, 
 *                               1=Control Trafico > Seguimiento, 
 *                               2=Registro paso EAL
 *  \param: num_despac  Integer  Numero del despacho
 *  \param: cod_contro  Integer  Codigo del PC donde se registrara la pernoctacion
 *  \return: 
 */
function verifyConfirPernoc(ind_origen, num_despac, cod_contro) {
    try {
        urlBase = "../satt_standa";

        var atr = "Ajax=on&Option=verifyConfirPernoc";
        atr += "&ind_origen=" + ind_origen;
        atr += "&num_despac=" + num_despac;

        if (cod_contro)
            atr += "&cod_contro=" + cod_contro;

        $.ajax({
            url: urlBase + "/despac/class_confir_pernoc.php",
            type: "POST",
            data: atr,
            async: false,
            success: function(data) {
                if (data) {
                    if (ind_origen == 1) {
                        $("#planRutaID").hide();
                        $("#llegadaID").hide();
                        $("#descargueID").hide();
                        $("#cargueID").hide();
                    }

                    PopupJQ('open', 'Confirmar Pernoctacion', ($(window).height() / 1.5), '650px', true, true, false, pop);
                    $(".ui-dialog-buttonpane").remove(); // Quitar la zona de botones
                    $("#" + pop).parent().children().children('.ui-dialog-titlebar-close').hide();
                    $("#" + pop).html(data);
                    $("#" + pop).css("height", "auto");
                }
            }
        });
    } catch (e) {
        console.log("Error Fuction verifyConfirPernoc: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: formReporPernoc
 *  \brief: Peticion Ajax para traer el formulario segun opcion (Pernocta/No pernocta)
 *  \author: Ing. Fabian Salinas
 *  \date: 09/03/2016
 *  \date modified: dd/mm/aaaa
 *  \param: ind_option  Integer  Indicador Opcion 1=Pernocta 2=No Pernocta
 *  \param: obj  Objeto  Objeto que activa el evento
 *  \return: 
 */
function formReporPernoc(ind_option, obj) {
    try {
        var opc = new Array('1', '2');
        var option = $("#ind_optionID");

        if (option.val() == ind_option)
            return false;

        option.val(ind_option);

        var atr = "Ajax=on&ind_option=" + ind_option;
        atr += "&num_despac=" + $("#" + pop + " #num_despacID").val();
        atr += "&cod_contrx=" + $("#" + pop + " #cod_contrxID").val();

        if (ind_option == 3)
            atr += "&Option=formNoReporPernoc";
        else if ($.inArray(ind_option, opc))
            atr += "&Option=formReportPernoc";
        else
            atr += "&Option=x";

        $.ajax({
            url: urlBase + "/despac/class_confir_pernoc.php",
            type: "POST",
            data: atr,
            async: false,
            beforeSend: function(){
                BlocK('Generando...', true);
            },
            success: function(data) {
                $("#tabConfirPernocID").html(data);
            },
            complete: function() {
                $("#hor_inicioID, #hor_reinicID").timepicker({
                    timeFormat: "hh:mm",
                    showSecond: false
                });

                $("#fec_inicioID, #fec_reinicID").datepicker({
                    dateFormat: 'yy-mm-dd'
                });

                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Fuction formReporPernoc: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: savePernoc
 *  \brief: Guarda la confirmacion de la pernoctacion
 *  \author: Ing. Fabian Salinas
 *  \date: 09/03/2016
 *  \date modified: dd/mm/aaaa
 *  \param:    
 *  \return: 
 */
function savePernoc() {
    try {
        var opc = new Array(1, 2);
        var val = true;
        var ind_option = $("#" + pop + " #ind_optionID").val();
        var lis_contro = $("#" + pop).find('select');
        var ind_origen = $("#" + pop + " #ind_origenID").val();

        var atr = "Ajax=on&Option=savePernoc";
        atr += "&ind_option=" + ind_option;
        atr += "&num_despac=" + $("#" + pop + " #num_despacID").val();
        atr += "&ind_origen=" + ind_origen;
        atr += "&ind_pernoc=" + $("#" + pop + " input[type='radio']:checked").val();
        atr += "&cod_servic=" + $("#cod_servicID").val();

        if (lis_contro.attr('name') != 'cod_contro') {
            atr += "&cod_contro=" + $("#" + pop + " #cod_contrxID").val();
        } else {
            if (lis_contro.val() != '')
                atr += "&cod_contro=" + lis_contro.val();
            else {
                setTimeout(function() {
                    inc_alerta("cod_controID", "Este campo es Obligatorio");
                }, 510);
                val = false;
            }
        }

        if ($.inArray(ind_option, opc)) {
            $("#tabConfirPernocID input[type=text]").each(function(i, obj) {
                if ($(obj).attr('obl') == 1 && $(obj).val() == "") {
                    val = false;
                    setTimeout(function() {
                        inc_alerta($(obj).attr('id'), "Este campo es Obligatorio");
                    }, 510);
                } else {
                    atr += "&" + $(obj).attr('name') + "=" + $(obj).val();
                }
            });
        } else {
            var obs_justif = $("#obs_justifID").val();

            if (!obs_justif) {
                inc_alerta("obs_justifID", "Este campo es Obligatorio");
                val = false;
            } else {
                atr += "&obs_justif=" + obs_justif;
            }
        }

        if (val == false)
            return false;

        var txt;

        $.ajax({
            url: urlBase + "/despac/class_confir_pernoc.php",
            type: "POST",
            data: atr,
            async: false,
            beforeSend: function(){
                BlocK('Generando...', true);
            },
            success: function(data) {
                if (data == 1)
                    txt = "Seguimiento Pernoctacion Registrado con Exito.";
                else
                    txt = "Fallo al Registrar Seguimiento Pernoctacion.";
            },
            complete: function(){
                BlocK();
            }
        });

        closePopUp(pop);
        LoadPopupJQNoButton('open', 'Seguimiento Pernoctacion', 'auto', 'auto', false, false, false, 'popAlertID');
        var popup = $("#popAlertID");
        popup.parent().children().children('.ui-dialog-titlebar-close').hide();

        var msj = '<div style="text-align:center">' + txt + '<br><br><br><br>';
        msj += '<input type="button" name="si" id="siID" value=" Cerrar " style="cursor:pointer" onclick="closePopUp(\'popAlertID\'); submitForm();" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"/>';

        popup.append(msj);
    } catch (e) {
        console.log("Error Fuction savePernoc: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: submitForm
 *  \brief: Submit al Formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 17/03/2016
 *  \date modified: dd/mm/aaaa
 *  \param:     
 *  \return: 
 */
function submitForm(  ){
    try {
        $("form").submit();
    } catch (e) {
        console.log("Error Fuction submitForm: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: PopupJQ
 *  \brief: Crea o destruye PopUp
 *  \author: Ing. Fabian Salinas
 * \date: 24/06/2015
 * \date modified: dia/mes/año
 *  \param: opcion   String   open, close
 *  \param: titulo   String   Titulo del PopUp
 *  \param: alto   Integer  Altura PopUp
 *  \param: ancho    Integer  Ancho PopUp
 *  \param: redimen  Boolean  True = Redimencionable
 *  \param: dragg    Boolean  True = El PopUp se puede arrastras
 *  \param: lockBack Boolean  True = Bloquea el BackGround
 *  \return: 
 */
function PopupJQ(opcion, titulo, alto, ancho, redimen, dragg, lockBack, idPopup) {
    try {
        var id = 'popID';
        if (idPopup)
            id = idPopup;

        if (opcion == 'close') {
            $("#" + id).dialog("destroy").remove();
        } else {
            $("<div id='" + id + "' name='pop' />").dialog({
                height: alto,
                width: ancho,
                modal: lockBack,
                title: titulo,
                closeOnEscape: false,
                resizable: redimen,
                draggable: dragg,
                buttons: {
                    Cerrar: function() {
                        closePopUp(id);
                    }
                }
            });
        }
    } catch (e) {
        console.log("Error Fuction LoadPopupJQ: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}
