// const { doDuring } = require("async");

/* \file: ins_sertra_sertra.js
 *  \brief: archivo con mutltiples funciones ajax y css
 *  \author : Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 08/02/2016       
 *  \bug:
 *  \warning:
 */
 function autocomplete(){
    $("#cod_agenciID").multiselect().multiselectfilter();
 }
$("body").ready(function() {
 $("#cod_agenciID option[value=" + $("#sel_agenciID").val() + "]").attr("selected", "selected");
});
var parameters = "";
$(function() {
    var standa = $("#standaID").val();
    var attributes = '&Ajax=on&standa=' + standa;
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
    })
    setTimeout(function() {
        $("div").css({
            height: 'auto',
        });
    }, 500);


    $("#nom_transpID").autocomplete({
        source: "../" + standa + "/transp/ajax_transp_transp.php?Option=buscarTransportadora" + attributes,
        minLength: 3,
        select: function(event, ui) {
            boton = "<input type='button' id='nuevo' value='Configurar' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all' onclick='mostrar();'>";
            $("#cod_transpID").val(ui.item.id);
            $("#nom_transp").val(trim(ui.item.value));
            $("#boton").empty();
            $("#boton").append(boton);
            $("body").removeAttr("class");
        }
    });

    $("#tabs").tabs({
        beforeLoad: function(event, ui) {
            ui.jqXHR.fail(function() {
                ui.panel.html("Cargado...");
            });
        }
    });

    $("#fec_iniciaID").datepicker();
    $("#fec_iniciaID").datepicker('option', {
        dateFormat: 'yy-mm-dd'
    });
    $("#fec_finaliID").datepicker();
    $("#fec_finaliID").datepicker('option', {
        dateFormat: 'yy-mm-dd'
    });
});

/* ! \fn: mostrar
 *  \brief: mostrar los datos de la configuracion actual de la transportdora
 *  \author: Ing. Alexander Correa
 *  \date: 08/02/2016
 *  \date modified: dia/mes/año
 *  \param:
 *  \return
 */
function mostrar() {
    var conn = checkConnection();
    if (conn) {
        $("#form3").empty();
        var transp = $("#cod_transpID").val();
        var standa = $("#standaID").val();
        var nom_transp = $("#nom_transp").val();
        var parametros = "Option=getDataFomrmTipSer&Ajax=on&cod_transp=" + transp + "&nom_transp=" + nom_transp + "&" + parameters;
        
        $.ajax({
            url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
            type: "POST",
            data: parametros,
            async: true,
            beforeSend: function(obj) {
                $.blockUI({
                    theme: true,
                    title: 'Tipo de Servicio',
                    draggable: false,
                    message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Cargando formulario</p></center>'
                });
            },
            success: function(data) {
                $.unblockUI();
                // setTimeout(function() {
                $("#form3").html(data); // pinta los datos de la consulta
                // }, 2000);
                $(".accordion").accordion({
                    collapsible: true,
                    heightStyle: "content",
                    icons: {
                        "header": "ui-icon-circle-arrow-e",
                        "activeHeader": "ui-icon-circle-arrow-s"
                    }
                }).click(function() {
                    $("body").removeAttr("class");
                })
                setTimeout(function() {
                    $("div").css({
                        height: 'auto',
                    });
                }, 800);
                $("#form3").css("display", "none");
                $("#form3").fadeIn(3000);

                var x = 0;
                $("input[name*='eal']").each(function() {
                    if ($(this).attr('checked')) {
                        $("#precio" + x).attr('disabled', false);
                        $("#fecini" + x).attr('disabled', false);
                        $("#fecfin" + x).attr('disabled', false);
                        $("#precio" + x).attr('obl', 1);
                        $("#fecini" + x).attr('obl', 1);
                        $("#fecfin" + x).attr('obl', 1);
                    } else {
                        $("#precio" + x).attr('disabled', true);
                        $("#fecini" + x).attr('disabled', true);
                        $("#fecfin" + x).attr('disabled', true);
                        $("#precio" + x).removeAttr('obl');
                        $("#fecini" + x).removeAttr('obl');
                        $("#fecfin" + x).removeAttr('obl');
                    }
                    x++;
                });

			    $("#val_registID, #val_despacID").keyup(function(){
			    	switch ($(this).attr("id")) {
					  case 'val_registID':
					  	$("#val_despacID").removeAttr("validate").removeAttr("minlength").removeAttr("obl").val("0");
					  	$("#val_registID").attr("validate", "numero").attr("minlength", "3").attr("obl", "1");
					    break;
					  case 'val_despacID':
					  	$("#val_registID").removeAttr("validate").removeAttr("minlength").removeAttr("obl").val("0");
					  	$("#val_despacID").attr("validate", "numero").attr("minlength", "3").attr("obl", "1");
					    break;
					}
			    });
            },
            complete: function() {
                enableDisable(1);
                enableDisable(2);
                enableDisable(3);
                enableDisable(4);
                enableDisable(5);
                $(".fecha").datepicker({
                    dateFormat: 'yy-mm-dd'
                });

                $(".hora").timepicker({
                    timeFormat: "hh:mm",
                    showSecond: false
                });
                $("#conf_ealID h3").trigger( "click" );
                $("#conf_etapasID h3").trigger( "click" );
            }
        });



    } else {
        swal({
            title: "Parametrización",
            text: "Por favor verifica tu conexión a internet.",
            type: "warning"
        });
    }
}
/* ! \fn: CreateConfig
 *  \brief: funcion para configurar el horario laboral de una empresa
 *  \author: Ing. Alexander Correa
 *  \date: 08/02/2016
 *  \date modified: dia/mes/año
 *  \param: cod_tercer     => string => codigo de la empresa   
 *  \param: cod_ciudad     => string => codigo de ciudad en la cual aplica el horario     
 *  \return return
 */
function CreateConfig(cod_tercer, cod_ciudad) {
    try {
        var conn = checkConnection();
        if (conn) {
            var standa = $("#standaID").val();
            $("#PopUpID").dialog({
                modal: true,
                resizable: false,
                draggable: false,
                title: "Creaci\xf3n de Parametrizaci\xf3n",
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
                        NewParametrizacion(cod_ciudad);
                    },
                    Cerrar: function() {
                        $(this).dialog('close');
                    }
                }
            });
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "&Ajax=on&Option=CreateConfig&standa=" + standa + "&cod_tercer=" + cod_tercer + "&cod_ciudad=" + cod_ciudad,
                async: true,
                beforeSend: function() {
                    $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
                },
                success: function(datos) {
                    if (datos == '1') {
                        closePopUp("PopUpID");
                        swal({
                            title: "Parametrización",
                            text: "Todos los días ya parametrizados.",
                            type: "warning"
                        });
                    } else {
                        $("#PopUpID").html(datos);
                    }

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

/* ! \fn: setFestivos
 *  \brief: busca los festivos registrados
 *  \author: Ing. Alexander Correa
 *  \date: 08/02/2016
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return 
 */
function setFestivos() {
    try {
        var conn = checkConnection();
        if (conn) {
            $("#PopUpID").html("");
            if ($("#FestivosID").is(':visible'))
                $("#FestivosID").hide('blind');

            var ind_config = $("#ind_configID").val();
            var cod_ciudad = $("#cod_ciudadID").val();
            var sel_yearxx = $("#sel_yearxxID").val();
            var standa = $("#standaID").val();
            if (sel_yearxx != "") {
                $("#PopUpID").dialog({
                    modal: true,
                    resizable: false,
                    draggable: false,
                    title: "Festivos del año " + sel_yearxx,
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
                            NewParametrizacion(cod_ciudad);
                        },
                        Cerrar: function() {
                            $(this).dialog('close');
                        }
                    }
                });
                var standa = $("#standaID").val();
                var cod_transp = $("#cod_transpID").val();
                parameters = getDataForm();
                $.ajax({
                    type: "POST",
                    url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                    data: "Ajax=on&Option=getFestivos&standa=" + standa + "&sel_yearxx=" + sel_yearxx + "&cod_transp=" + cod_transp + "&ind_config=" + ind_config + "&cod_ciudad=" + cod_ciudad,
                    async: true,
                    beforeSend: function() {

                        $.blockUI({
                            theme: true,
                            title: 'Tipo de Servicio',
                            draggable: false,
                            message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Cargando calendario</p></center>'
                        });
                    },
                    success: function(datos) {
                        $.unblockUI();
                        $("#PopUpID").html(datos);
                        $(".fecha").datepicker();
                        $.mask.definitions["A"] = "[12]";
                        $.mask.definitions["M"] = "[01]";
                        $.mask.definitions["D"] = "[0123]";
                        $(".fecha").mask("Annn-Mn-Dn");
                    }
                });
            }
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


/* ! \fn: InsertFestivo
 *  \brief: inserta un nuevo festivo para una empresa en una ciudad
 *  \author: Ing. Alexander Correa
 *  \date: 08/02/2016
 *  \date modified: dia/mes/año
 *  \param: cod_tercer     => string => nit de la empresa    
 *  \param: ind_config     => int => indicador de configuracion   
 *  \param: cod_ciudad     => string => codigo de ciudad en la cual aplica el festivo    
 *  \return return
 */
function InsertFestivo(cod_tercer, ind_config, cod_ciudad) {
    try {
        var conn = checkConnection();
        if (conn) {
            var standa = $("#standaID").val();
            var fec_insert = $("#fec_insertID").val();

            if (fec_insert == '') {
                setTimeout(function() {
                    inc_alerta("fec_insertID", "Por favor ingresa la fecha a configurar.");
                }, 510);
            } else {
                $.ajax({
                    type: "POST",
                    url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                    data: "Ajax=on&Option=InsertFestivo&standa=" + standa + "&fec_insert=" + fec_insert + "&cod_transp=" + cod_tercer + "&ind_config=" + ind_config + "&cod_ciudad=" + cod_ciudad,
                    async: true,
                    beforeSend: function() {

                        $.blockUI({
                            theme: true,
                            title: 'Registrando Festivo',
                            draggable: false,
                            message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Registrando Festivo</p></center>'
                        });
                    },
                    success: function(datos) {
                        $.unblockUI();
                        if (datos == '9999') {
                            swal({
                                title: "Registrar Festivo",
                                text: "Atención esta fecha ya esta configurada como festivo.",
                                type: "warning"
                            });
                        } else if (datos == '1991') {
                            swal({
                                title: "Registrar Festivo",
                                text: "No se ha podido Registrar el festivo, por favor intenta nuevamente.",
                                type: "warning"
                            });
                        } else {
                            setFestivos();
                            swal({
                                title: "Registrar Festivo",
                                text: "Festivo Registrado Correctamente.",
                                type: "success"
                            });
                        }
                    }
                });
            }
        } else {
            swal({
                title: "Registrar Festivo",
                text: "Por favor verifica tu conexión a internet.",
                type: "warning"
            });
        }
    } catch (e) {
        console.log(e.message);
        return false;
    }
}

/* ! \fn: deleteFestivo
 *  \brief: elimina un festivo para una empresa en una ciudad
 *  \author: Ing. Alexander Correa
 *  \date: 08/02/2016
 *  \date modified: dia/mes/año
 *  \param: cod_tercer     => string => nit de la empresa    
 *  \param: ind_config     => int => indicador de configuracion   
 *  \param: cod_ciudad     => string => codigo de ciudad en la cual aplica el festivo    
 *  \param: ano     => string => año    
 *  \param: mes     => string => mes   
 *  \param: dia     => string => dia    
 *  \return return
 */
function deleteFestivo(cod_transp, ind_config, cod_ciudad, ano, mes, dia) {
    try {
        var conn = checkConnection();
        if (conn) {
            var standa = $("#standaID").val();
            swal({
                title: "Eliminar Festivo",
                text: "¿Realmente Deseas Eliminar el festivo seleccionado?",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                setTimeout(function() {
                    $.ajax({
                        type: "POST",
                        url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                        data: "Ajax=on&Option=deleteFestivo&standa=" + standa + "&cod_transp=" + cod_transp + "&ind_config=" + ind_config + "&cod_ciudad=" + cod_ciudad + "&ano=" + ano + "&mes=" + mes + "&dia=" + dia,
                        async: true,
                        beforeSend: function() {
                            $.blockUI({
                                theme: true,
                                title: 'Eliminando Festivo',
                                draggable: false,
                                message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Eliminando Festivo</p></center>'
                            });
                        },
                        success: function(datos) {
                            $.unblockUI();
                            if (datos == '9999') {
                                swal({
                                    title: "Eliminar Festivo",
                                    text: "No se ha podido eliminar el festivo, por favor intenta nuevamente.",
                                    type: "warning"
                                });
                            } else {
                                setFestivos();
                                swal({
                                    title: "Eliminar Festivo",
                                    text: "Festivo Eliminada Correctamente.",
                                    type: "success"
                                });

                            }
                        }
                    });
                }, 1000);
            });
        }
    } catch (e) {
        console.log(e.message);
        return false;
    }
}

/* ! \fn: habilitar
 *  \brief: habilita o deshabilita los campos de fecha y precio en las eal contratadas
 *  \author: Ing. Alexander Correa
 *  \date: 08/02/2016
 *  \date modified: dia/mes/año
 *  \param: key     => int => indicador de la eal seleccionada    
 *  \return 
 */
function habilitar(key) {
    if ($('#eal' + key).attr('checked')) {
        $("#precio" + key).attr('disabled', false);
        $("#fecini" + key).attr('disabled', false);
        $("#fecfin" + key).attr('disabled', false);
        $("#precio" + key).attr('obl', 1);
        $("#fecini" + key).attr('obl', 1);
        $("#fecfin" + key).attr('obl', 1);
    } else {
        $("#precio" + key).attr('disabled', true);
        $("#fecini" + key).attr('disabled', true);
        $("#fecfin" + key).attr('disabled', true);
        $("#precio" + key).removeAttr('obl');
        $("#fecini" + key).removeAttr('obl');
        $("#fecfin" + key).removeAttr('obl');
    }
}


/* ! \fn: registrarTipoServicio
 *  \brief: inserta toda la configuracion de una transportadora
 *  \author: Ing. Alexander Correa
 *  \date: 08/02/2016
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return 
 */
function registrarTipoServicio() {
    var conn = checkConnection();
    if (conn) {
        var val = validaciones();
        var fec_inicia = $("#fec_iniciaID").val();
        var fec_finali = $("#fec_finaliID").val();
        var fi = new Date(fec_inicia);
        var ff = new Date(fec_finali);
        if (fi > ff) {
            setTimeout(function() {
                inc_alerta("fec_iniciaID", "Fecha inicial mayor a la final.");
            }, 510);
            setTimeout(function() {
                inc_alerta("fec_finaliID", "Fecha final mayor a la inicial.");
            }, 510);
            val = false;
        }
        var tie_trazab = $("#tie_trazabID").val();
        if (tie_trazab) {
            if (isNaN(tie_trazab)) {
                setTimeout(function() {
                    inc_alerta("tie_trazabID", "Solo se aceptan números");
                }, 510);
                val = false;
            } else {
                if (parseFloat(tie_trazab) > 12) {
                    setTimeout(function() {
                        inc_alerta("tie_trazabID", "Rango máximo de 12 horas");
                    }, 510);
                    val = false;
                }
            }
        }
        if (val == true) {
            swal({
                title: "Tipo de Servicio",
                text: "¿Realmente Deseas Actualizar la configuración de la transportadora?",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                var standa = $("#standaID").val();

                $.ajax({
                    type: "POST",
                    url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                    data: "Ajax=on&Option=registrarTipoServicio&" + getDataForm(),
                    async: true,
                    success: function(datos) {
                        if (datos == 1) {
                            swal({
                                title: "Tipo de Servicio",
                                text: "Datos almacenados correctamente",
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: true,
                                showLoaderOnConfirm: true,
                            }, function() {
                                $("#formulario").submit();
                            });
                        } else {
                            swal({
                                title: "Eliminar Festivo",
                                text: "Error al registrar los datos, intenta nuevamente. Si el error persiste por favor informar.",
                                type: "warning"
                            });
                        }
                    }

                });
            });
        }
    } else {
        swal({
            title: "Parametrización",
            text: "Por favor verifica tu conexión a internet.",
            type: "warning"
        });
    }
}


/* ! \fn: enableDisable
 *  \brief: activa e inactiva varios input en el formulario
 *  \author: Ing. Alexander Correa
 *  \date: 08/02/206    
 *  \date modified: dia/mes/año
 *  \param:   indicador => int => indica cual check se ha seleccionado 
 *  \return return
 */
function enableDisable(indicador) {
    switch (indicador) {
        case 1:
            var campos = ["tie_carnacID", "tie_carurbID", "tie_carexpID", "tie_carimpID", "tie_cartr1ID", "tie_cartr2ID"];
            if ($("#ind_segcarID").attr('checked')) {
                activar(true, campos);
            } else {
                activar(false, campos);
            }
            break;
        case 2:
            var campos = ["tie_controID", "tie_conurbID", "tie_traexpID", "tie_traimpID", "tie_tratr1ID", "tie_tratr2ID"];

            if ($("#ind_segtraID").attr('checked') && $("#ind_planruID").attr('checked') == false) {
                activar(true, campos);
            } else {
                activar(false, campos);
            }
            break;
        case 3:
            var campos = ["tie_controID", "tie_conurbID", "tie_traexpID", "tie_traimpID", "tie_tratr1ID", "tie_tratr2ID"];
            if ($("#ind_planruID").attr('checked')) {
                activar(false, campos);
            } else {
                if ($("#ind_segtraID").attr('checked')) {
                    activar(true, campos);
                }
            }
            break;
        case 4:
            var campos = ["tie_desnacID", "tie_desurbID", "tie_desexpID", "tie_desimpID", "tie_destr1ID", "tie_destr2ID"];
            if ($("#ind_segdesID").attr('checked')) {
                activar(true, campos);
            } else {
                activar(false, campos);
            }
            break;
        case 5:
            var campos = ["hor_pe1urbID", "hor_pe2urbID", "hor_pe1nacID", "hor_pe2nacID", "hor_pe1impID", "hor_pe2impID",
                "hor_pe1expID", "hor_pe2expID", "hor_pe1tr1ID", "hor_pe2tr1ID", "hor_pe1tr2ID", "hor_pe2tr2ID"
            ];
            if ($("#ind_conperID").attr('checked')) {
                activar(true, campos);
            } else {
                activar(false, campos);
            }
            break;
        case 6:
            var campos = ["tie_prcnacID", "tie_prcurbID", "tie_prcexpID", "tie_prcimpID", "tie_prctr1ID", "tie_prctr2ID"];
            if ($("#ind_segprcID").attr('checked')) {
                activar(true, campos);
            } else {
                activar(false, campos);
            }
            break;
    }
}
/* ! \fn: activar
 *  \brief: complementaria de la funcion EnableDisable
 *  \author: Ing. Alexander Correa
 *  \date: 08/02/2016
 *  \date modified: dia/mes/año
 *  \param: ind     => boolean => indice de los campos a habilirar o inabilitar    
 *  \param: campos     => array => arreglo con los campos a tratar    
 *  \return 
 */
function activar(ind, campos) {
    if (ind == true) {
        $.each(campos, function(i, val) {
            $("#" + val).attr('disabled', false);
            $("#" + val).attr('obl', 1);
        });
    } else {
        $.each(campos, function(i, val) {
            $("#" + val).attr('disabled', true);
            $("#" + val).removeAttr('obl');
        });
    }
}

/* ! \fn: newParametrizacion
 *  \brief: registra un dia laboral de la semana
 *  \author: Ing. Alexander Correa
 *  \date: 08/02/2016
 *  \date modified: dia/mes/año
 *  \param: ind_config     => int    => indicador de la configuracion que se quiere regstrar    
 *  \param: cod_ciudad     => string => indicador de la ciudad para la que aplica la configuracion  
 *  \return 
 */
function NewParametrizacion(cod_ciudad) {
    inc_remover_alertas();
    var conn = checkConnection();
    var ind_config = 0; contador = 0;
    if (conn) {
        var standa = $("#standaID").val();
        var transp = $("#cod_transpID").val();
        var hor_ingres = $("#hor_ingresID").val();
        var hor_salida = $("#hor_salidaID").val();
        var errores = false;
        var nue_combin = "";
        if (!hor_ingres) {
            setTimeout(function() {
                inc_alerta("hor_ingresID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!hor_salida) {
            setTimeout(function() {
                inc_alerta("hor_salidaID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }

        $('input[name="nom_diasxx"]').each(function(index) {
            if ($(this).is(':checked')) {
                if(ind_config != $(this).attr("ind_config")){
                    ind_config = $(this).attr("ind_config");
                    contador ++;
                }else{
                }
                nue_combin += nue_combin != '' ? '|' + $(this).val() : $(this).val();
            }
        });
        if (contador > 1) {
            swal({
                title: "Parametrización",
                text: "Solo se pueden seleccionar días de una lista.",
                type: "warning"
            });
            errores = true;
        }else{
            $("#ind_configID").val(ind_config);
            $("#cod_ciudadID").val(ind_config);
        }
        if (nue_combin == "") {
            swal({
                title: "Parametrización",
                text: "Seleccione por lo menos un día de la semana.",
                type: "warning"
            });
            errores = true;
        }
        if (!errores) {
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=NewParametrizacion&standa=" + standa + "&cod_transp=" + transp + "&nue_combin=" + nue_combin + "&hor_saledi=" + hor_salida + "&hor_ingedi=" + hor_ingres + "&ind_config=" + ind_config + parameters,
                async: true,
                beforeSend: function() {
                    $.blockUI({
                        theme: true,
                        title: 'Registrando configuración',
                        draggable: false,
                        message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Registrando...</p></center>'
                    });
                },
                success: function(datos) {
                    $.unblockUI();
                    if (datos == '1000') {
                        swal({
                            title: "Parametrización",
                            text: "Configuración registrada con éxito.",
                            type: "success"
                        });
                        $("#PopUpID").dialog('close');
                        mostrar();
                    } else {
                        swal({
                            title: "Parametrización",
                            text: "Error al registrar la configuración, por favor intenta nuevamente.",
                            type: "warning"
                        });
                        swal("");
                    }
                }

            });
        }
    } else {
        swal({
            title: "Parametrización",
            text: "Por favor verifica tu conexión a internet.",
            type: "warning"
        });
    }
}


/* ! \fn: validaCampo
 *  \brief: funcion para confirmar que no se exeda el rango maximo de 12 horas y que solo se inserten numeros
 *  \author: Ing. Alexander Correa
 *  \date: 08/02/2016
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return 
 */
function validaCampo() {
    var tie_trazab = $("#tie_trazabID").val();
    if (tie_trazab) {
        if (isNaN(tie_trazab)) {
            inc_alerta("tie_trazabID", "Solo se aceptan números");
        } else {
            if (parseFloat(tie_trazab) > 12) {
                inc_alerta("tie_trazabID", "Rango máximo de 12 horas");
            }
        }
    }
}

/* ! \fn: deleteConfiguracion
 *  \brief: elimina una parametrizacio laboral
 *  \author: Ing. Alexander Correa
 *  \date: 16/05/2016
 *  \date modified: dia/mes/año
 *  \param: cod_transp => int => codigo de la transportadora a la que se le eliminara el dia laboral    
 *  \param: dia => string => dia(s) a eliminar 
 *  \return 
 */
function deleteConfiguracion(cod_transp, dia, ind_config) {
    try {
        swal({
            title: "Eliminar Configuración",
            text: "¿Realmente Deseas eliminar la configuración seleccionada?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function() {
            var standa = $("#standaID").val();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=deleteConfiguracion&cod_transp=" + cod_transp + "&dia=" + dia + "&ind_config=" + ind_config,
                async: true,
                success: function(datos) {
                    if (datos == 1) {
                        swal({
                            title: "Eliminar Configuración",
                            text: "Datos eliminados con éxito.",
                            type: "success"
                        }, function() {
                            parameters = getDataForm();
                            mostrar();
                        });
                    } else {
                        swal({
                            title: "Eliminar Configuración",
                            text: "Error al eliminar los datos, intenta nuevamente. Si el error persiste por favor informar.",
                            type: "error"
                        });
                    }
                }

            });
        });
    } catch (e) {
        console.log(e.message);
    }
}
/* ! \fn: deleteContac
 *  \brief: elimina una contacto
 *  \author: Ing. Andres Torres
 *  \date: 08/02/2018
 *  \date modified: dia/mes/año
 *  \param: cod_transp => int => codigo de la transportadora a la que se le eliminara el contacto
 *  \param: ema_contac => llave del contacto 
 *  \return 
 */
function deleteContac(cod_transp, ema_contac) {
    try {
        swal({
            title: "Eliminar Contacto",
            text: "¿Realmente Deseas eliminar el Contacto seleccionado?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function() {
            var standa = $("#standaID").val();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=deleteContac&cod_transp=" + cod_transp + "&ema_contac=" + ema_contac,
                async: true,
                success: function(datos) {
                    if (datos == 1) {
                        swal({
                            title: "Eliminar Contacto",
                            text: "Datos eliminados con éxito.",
                            type: "success"
                        }, function() {
                            parameters = getDataForm();
                            mostrar();
                        });
                    } else {
                        swal({
                            title: "Eliminar Contacto",
                            text: "Error al eliminar los datos, intenta nuevamente. Si el error persiste por favor informar.",
                            type: "error"
                        });
                    }
                }

            });
        });
    } catch (e) {
        console.log(e.message);
    }
}
/* ! \fn: deletePartic
 *  \brief: elimina una particularidad
 *  \author: Ing. Andres Martinez
 *  \date: 08/02/2018
 *  \date modified: dia/mes/año
 *  \param: cod_transp => int => codigo de la transportadora a la que se le eliminara el contacto
 *  \param: ema_contac => llave del contacto 
 *  \return 
 */
function deletePartic(cod_transp, cod_partic) {
    try {
        swal({
            title: "Eliminar Particularidad",
            text: "¿Realmente Deseas eliminar la Particularidad seleccionada?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function() {
            var standa = $("#standaID").val();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=deletePartic&cod_transp=" + cod_transp + "&cod_partic=" + cod_partic,
                async: true,
                success: function(datos) {
                    if (datos == 1) {
                        swal({
                            title: "Eliminar Particularidad",
                            text: "Datos eliminados con éxito.",
                            type: "success"
                        }, function() {
                            parameters = getDataForm();
                            mostrar();
                        });
                    } else {
                        swal({
                            title: "Eliminar Particularidad",
                            text: "Error al eliminar los datos, intenta nuevamente. Si el error persiste por favor informar.",
                            type: "error"
                        });
                    }
                }

            });
        });
    } catch (e) {
        console.log(e.message);
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
function CreatePartic(cod_transp, tip_servic) {
    try {
        var conn = checkConnection();
        var standa = $("#standaID").val();
        var transp = $("#cod_transpID").val();

        if (conn) {
            var standa = $("#standaID").val();
            var transp = $("#cod_transpID").val();
            var tip_servic = $("#cod_tipserInserID").val();
            var fec_partic = $("#fec_particID").val();
            var des_partic = $("#des_particID").val();
            $("#PopUpID").dialog({
                modal: true,
                resizable: false,
                draggable: false,
                title: "Creación de Particularidad",
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
                        NewPartic(tip_servic);
                    },
                    Cerrar: function() {
                        $(this).dialog('close');
                    }
                }
            });
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=CreatePartic&standa=" + standa + "&cod_transp=" + transp + "&tip_servic=" + tip_servic + "&fec_partic=" + fec_partic + "&des_partic=" + des_partic +"&ind_edicio=0",
                async: true,
                beforeSend: function() {
                    $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
                },
                success: function(datos) {
                    if (datos == '1') {
                        closePopUp("PopUpID");
                        swal({
                            title: "Creación de Particularidad",
                            text: "Se creo la particularidad correctamente.",
                            type: "success"
                        });
                    } else {
                        $("#PopUpID").html(datos);
                    }

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

function NewPartic(tip_servic) {
    var conn = checkConnection();
    var ind_config = 0; contador = 0;
    
    if (conn) {
        var standa = $("#standaID").val();
        var transp = $("#cod_transpID").val();
        var tip_servic = $("#cod_tipserInserID").val();
        var fec_partic = $("#fec_particID").val();
        var des_partic = $("#des_particID").val();

        var errores = false;
        if (!tip_servic) {
            setTimeout(function() {
                inc_alerta("tip_servicID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!fec_partic) {
            setTimeout(function() {
                inc_alerta("fec_particID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!des_partic) {
            setTimeout(function() {
                inc_alerta("des_particID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!errores) {
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=NewPartic&standa=" + standa + "&cod_transp=" + transp + "&tip_servic=" + tip_servic + "&fec_partic=" + fec_partic + "&des_partic=" + des_partic,
                async: true,
                beforeSend: function() {
                    $.blockUI({
                        theme: true,
                        title: 'Registrando particularidad',
                        draggable: false,
                        message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Registrando...</p></center>'
                    });
                },
                success: function(datos) {
                    $.unblockUI();
                    if (datos == '1000') {
                        swal({
                            title: "Parametrización",
                            text: "Particularidad registrada con éxito.",
                            type: "success"
                        });
                        $("#PopUpID").dialog('close');
                        mostrar();
                    } else {
                        swal({
                            title: "Contacto",
                            text: "Error al registrar la configuración, por favor intenta nuevamente.",
                            type: "warning"
                        });
                        swal("");
                    }
                }

            });
        }
    } else {
        swal({
            title: "Parametrización",
            text: "Por favor verifica tu conexión a internet.",
            type: "warning"
        });
    }
}

/* ! \fn: CreateContac
 *  \brief: funcion para insertar un contacto
 *  \author: Ing. Andres torres
 *  \date: 08/02/2018
 *  \date modified: dia/mes/año
 *  \param: cod_tercer     => string => codigo de la empresa   
 *  \param: cod_ciudad     => string => email cliente     
 *  \return return
 */
function CreateContac(cod_transp, ema_contac, id_contac) {
    try {
        var conn = checkConnection();
        var standa = $("#standaID").val();
        var transp = $("#cod_transpID").val();

        if (conn) {
            var standa = $("#standaID").val();
            var transp = $("#cod_transpID").val();
            var nom_contac = $("#nom_contacID").val();
            var ema_contac = $("#ema_contacID").val();
            var tel_contac = $("#tel_contacID").val();
            var car_contac = $("#car_contacID").val();
            var obs_contac = $("#obs_contacID").val();
            var cod_agenci = "";
            $("#PopUpID").dialog({
                modal: true,
                resizable: false,
                draggable: false,
                title: "Creación de Contacto",
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
                        NewContac(ema_contac);
                    },
                    Cerrar: function() {
                        $(this).dialog('close');
                    }
                }
            });
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=CreateContac&standa=" + standa + "&cod_transp=" + transp + "&ema_contac=" + ema_contac + "&nom_contac=" + nom_contac + "&obs_contac=" + obs_contac + "&car_contac=" + car_contac + "&tel_contac=" + tel_contac + "&cod_agenci=" + cod_agenci + "&ind_edicio=0",
                async: true,
                beforeSend: function() {
                    $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
                },
                success: function(datos) {
                    if (datos == '1') {
                        closePopUp("PopUpID");
                        swal({
                            title: "Creación de Contacto",
                            text: "Se creo el contacto correctamente.",
                            type: "success"
                        });
                    } else {
                        $("#PopUpID").html(datos);
                        $("#cod_agenciID").multiselect().multiselectfilter();
                    }

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

/* ! \fn: NewContac
 *  \brief: registra un contacto en el sistema
 *  \author: Ing. Torres
 *  \date: 13/02/2018
 *  \date modified: dia/mes/año
 *  \param: ind_config     => int    => indicador de la configuracion que se quiere regstrar    
 *  \param: cod_ciudad     => string => indicador de la ciudad para la que aplica la configuracion  
 *  \return 
 */
function NewContac(ema_contac) {
    var conn = checkConnection();
    var ind_config = 0; contador = 0;
    if (conn) {
        var standa = $("#standaID").val();
        var transp = $("#cod_transpID").val();
        var nom_contac = $("#nom_contacID").val();
        var ema_contac = $("#ema_contacID").val();
        var tel_contac = $("#tel_contacID").val();
        var car_contac = $("#car_contacID").val();
        var obs_contac = $("#obs_contacID").val();
        var cod_agenci = $("#cod_agenciID").val();
        var cod_agenci = "";
        var box_checke = $("input[id^=ui-multiselect-cod_agenciID-option-]:checked");

         box_checke.each(function(i, o) {
            if ($(o).attr("defaultValue") != '') {
                cod_agenci += $(o).attr("defaultValue")+",";
            };
        });
        var pos = cod_agenci.lastIndexOf(',');
        var cambio ='';
        var cod_agenci = cod_agenci.substring(0,pos) + cambio + cod_agenci.substring(pos+1);

        var errores = false;
        var nue_combin = "";
        if (!nom_contac) {
            setTimeout(function() {
                inc_alerta("nom_contacID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!ema_contac) {
            setTimeout(function() {
                inc_alerta("ema_contacID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!tel_contac) {
            setTimeout(function() {
                inc_alerta("tel_contacID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!car_contac) {
            setTimeout(function() {
                inc_alerta("car_contacID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!obs_contac) {
            setTimeout(function() {
                inc_alerta("obs_contacID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!cod_agenci) {
            setTimeout(function() {
                inc_alerta("cod_agenciID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }

        if (!errores) {
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=NewContac&standa=" + standa + "&cod_transp=" + transp + "&ema_contac=" + ema_contac + "&nom_contac=" + nom_contac + "&obs_contac=" + obs_contac + "&car_contac=" + car_contac + "&tel_contac=" + tel_contac + "&cod_agenci=" + cod_agenci,
                async: true,
                beforeSend: function() {
                    $.blockUI({
                        theme: true,
                        title: 'Registrando contacto',
                        draggable: false,
                        message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Registrando...</p></center>'
                    });
                },
                success: function(datos) {
                    $.unblockUI();
                    if (datos == '1000') {
                        swal({
                            title: "Parametrización",
                            text: "Contacto registrado con éxito.",
                            type: "success"
                        });
                        $("#PopUpID").dialog('close');
                        mostrar();
                    } else {
                        swal({
                            title: "Contacto",
                            text: "Error al registrar la configuración, por favor intenta nuevamente.",
                            type: "warning"
                        });
                        swal("");
                    }
                }

            });
        }
    } else {
        swal({
            title: "Parametrización",
            text: "Por favor verifica tu conexión a internet.",
            type: "warning"
        });
    }
}

/* ! \fn: EditaContac
 *  \brief: funcion para insertar un contacto
 *  \author: Ing. Andres torres
 *  \date: 08/02/2018
 *  \date modified: dia/mes/año
 *  \param: cod_tercer     => string => codigo de la empresa   
 *  \param: cod_ciudad     => string => email cliente     
 *  \return return
 */
function EditaContac(cod_transp, email, id_contac) {
    try {
        var conn = checkConnection();
        var standa = $("#standaID").val();
        var transp = $("#cod_transpID").val();
        var nom_contac = $("#nom_contac"+id_contac).html();
        var ema_contac = $("#ema_contac"+id_contac).html();
        var tel_contac = $("#tel_contac"+id_contac).html();
        var car_contac = $("#car_contac"+id_contac).html();
        var nom_agenci = $("#nom_agenci"+id_contac).html();
        var obs_contac = $("#obs_contac"+id_contac).html();
        var cod_agenci = $("#cod_agenci"+id_contac).html();
        if (conn) {
            var standa = $("#standaID").val();
            $("#PopUpID").dialog({
                modal: true,
                resizable: false,
                draggable: false,
                title: "Creación de Contacto",
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
                        editContac(email);
                    },
                    Cerrar: function() {
                        $(this).dialog('close');
                    }
                }
            });
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=CreateContac&standa=" + standa + "&cod_transp=" + transp + "&ema_contac=" + ema_contac + "&nom_contac=" + nom_contac + "&obs_contac=" + obs_contac + "&car_contac=" + car_contac + "&tel_contac=" + tel_contac + "&nom_agenci=" + nom_agenci + "&ind_edicio=1",
                async: true,
                beforeSend: function() {
                    $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
                },
                success: function(datos) {
                    if (datos == '1') {
                        closePopUp("PopUpID");
                        swal({
                            title: "Creación de Contacto",
                            text: "Se creo el contacto correctamente.",
                            type: "success"
                        });
                    } else {
                        $("#PopUpID").html(datos);
                        $("#cod_agenciID").multiselect().multiselectfilter();
                        
                        var box_checke = $("input[id^=ui-multiselect-cod_agenciID-option-]");
                        var agencias = $("#cod_agenci"+id_contac).val().split(",");
                        box_checke.each(function(i, o) {
                            for (var i = 0; i < agencias.length; i++ ) {
                                if ($(o).attr("defaultValue") == agencias[i] ) {
                                    $(o).attr("checked", "checked");
                                }
                            }
                        });

                    }

                },
                complete: function(){
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

/* ! \fn: editContac
 *  \brief: registra un contacto en el sistema
 *  \author: Ing. Torres
 *  \date: 13/02/2018
 *  \date modified: dia/mes/año
 *  \param: ind_config     => int    => indicador de la configuracion que se quiere regstrar    
 *  \param: cod_ciudad     => string => indicador de la ciudad para la que aplica la configuracion  
 *  \return 
 */
function editContac(email) {
    var conn = checkConnection();
    var ind_config = 0; contador = 0;
    if (conn) {
        var standa = $("#standaID").val();
        var transp = $("#cod_transpID").val();
        var nom_contac = $("#nom_contacID").val();
        var ema_contac = $("#ema_contacID").val();
        var tel_contac = $("#tel_contacID").val();
        var car_contac = $("#car_contacID").val();
        var obs_contac = $("#obs_contacID").val();
        var cod_agenci = "";
        var box_checke = $("input[id^=ui-multiselect-cod_agenciID-option-]:checked");

         box_checke.each(function(i, o) {
            if ($(o).attr("defaultValue") != '') {
                cod_agenci += $(o).attr("defaultValue")+",";
            };
        });
        var pos = cod_agenci.lastIndexOf(',');
        var cambio ='';
        var cod_agenci = cod_agenci.substring(0,pos) + cambio + cod_agenci.substring(pos+1);
        var errores = false;

        var nue_combin = "";
        if (!nom_contac) {
            setTimeout(function() {
                inc_alerta("nom_contacID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!ema_contac) {
            setTimeout(function() {
                inc_alerta("ema_contacID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!tel_contac) {
            setTimeout(function() {
                inc_alerta("tel_contacID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!car_contac) {
            setTimeout(function() {
                inc_alerta("car_contacID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!obs_contac) {
            setTimeout(function() {
                inc_alerta("obs_contacID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!cod_agenci) {
            setTimeout(function() {
                inc_alerta("cod_agenciID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!errores) {
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=editContac&standa=" + standa + "&cod_transp=" + transp + "&ema_contac=" + ema_contac + "&nom_contac=" + nom_contac + "&obs_contac=" + obs_contac + "&car_contac=" + car_contac + "&tel_contac=" + tel_contac + "&cod_agenci=" + cod_agenci + "&email=" + email,
                async: true,
                beforeSend: function() {
                    $.blockUI({
                        theme: true,
                        title: 'Actualizando contacto',
                        draggable: false,
                        message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Actualizando...</p></center>'
                    });
                },
                success: function(datos) {
                    $.unblockUI();
                    if (datos == '1000') {
                        swal({
                            title: "Parametrización",
                            text: "Contacto Actualizado con éxito.",
                            type: "success"
                        });
                        $("#PopUpID").dialog('close');
                        mostrar();
                    } else {
                        swal({
                            title: "Contacto",
                            text: "Error al registrar la configuración, por favor intenta nuevamente.",
                            type: "warning"
                        });
                        swal("");
                    }
                }

            });
        }
    } else {
        swal({
            title: "Parametrización",
            text: "Por favor verifica tu conexión a internet.",
            type: "warning"
        });
    }
}

/* ! \fn: EditaPartic
 *  \brief: funcion para insertar un contacto
 *  \author: Ing. Andres Martinez
 *  \date: 08/02/2018
 *  \date modified: dia/mes/año
 *  \param: cod_tercer     => string => codigo de la empresa   
 *  \param: cod_ciudad     => string => email cliente     
 *  \return return
 */
function EditaPartic(cod_transp, cod_partic) {
    try {
        var conn = checkConnection();
        var standa = $("#standaID").val();
        var transp = cod_transp;
        if (conn) {
            var standa = $("#standaID").val();
            $("#PopUpID").dialog({
                modal: true,
                resizable: false,
                draggable: false,
                title: "Editar Particularidad",
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
                        editPartic(cod_partic);
                    },
                    Cerrar: function() {
                        $(this).dialog('close');
                    }
                }
            });
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=CreatePartic&standa=" + standa + "&cod_transp=" + transp +"&cod_partic=" + cod_partic + "&ind_edicio=1",
                async: true,
                beforeSend: function() {
                    $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
                },
                success: function(datos) {
                    if (datos == '1') {
                        closePopUp("PopUpID");
                        swal({
                            title: "Creación de Particularidad",
                            text: "Se creo el contacto correctamente.",
                            type: "success"
                        });
                    } else {
                        $("#PopUpID").html(datos);
                    }

                },
                complete: function(){
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
/* ! \fn: editPartic
 *  \brief: registra un contacto en el sistema
 *  \author: Ing. Torres
 *  \date: 13/02/2018
 *  \date modified: dia/mes/año
 *  \param: ind_config     => int    => indicador de la configuracion que se quiere regstrar    
 *  \param: cod_ciudad     => string => indicador de la ciudad para la que aplica la configuracion  
 *  \return 
 */
function editPartic(cod_partic) {
    
    var conn = checkConnection();
    var ind_config = 0; contador = 0;
    
    if (conn) {
        var standa = $("#standaID").val();
        var transp = $("#cod_transpID").val();
        var tip_servic = $("#cod_tipserEditID").val();
        var fec_partic = $("#fec_particID").val();
        var des_partic = $("#des_particID").val();

        var errores = false;
        if (!tip_servic) {
            setTimeout(function() {
                inc_alerta("tip_servicID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!fec_partic) {
            setTimeout(function() {
                inc_alerta("fec_particID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!des_partic) {
            setTimeout(function() {
                inc_alerta("des_particID", "Campo Obligatorio.");
            }, 511);
            errores = true;
        }
        if (!errores) {
            parameters = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/sertra/ajax_sertra_sertra.php",
                data: "Ajax=on&Option=editPartic&standa=" + standa + "&cod_transp=" + transp + "&tip_servic=" + tip_servic + "&fec_partic=" + fec_partic + "&des_partic=" + des_partic + "&cod_partic=" + cod_partic,
                async: true,
                beforeSend: function() {
                    $.blockUI({
                        theme: true,
                        title: 'Actualizando Particularidad',
                        draggable: false,
                        message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Actualizando...</p></center>'
                    });
                },
                success: function(datos) {
                    $.unblockUI();
                    if (datos == '1000') {
                        swal({
                            title: "Parametrización",
                            text: "Particularidad Actualizada con éxito.",
                            type: "success"
                        });
                        $("#PopUpID").dialog('close');
                        mostrar();
                    } else {
                        swal({
                            title: "Contacto",
                            text: "Error al registrar la configuración, por favor intenta nuevamente.",
                            type: "warning"
                        });
                        swal("");
                    }
                }

            });
        }
    } else {
        swal({
            title: "Parametrización",
            text: "Por favor verifica tu conexión a internet.",
            type: "warning"
        });
    }
}