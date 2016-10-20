/* ! \file: ins_seguim_adicio.js
 *  \brief: archivo con multiples funcionalidades js para el servicio Transportadoras -> Seguimiento Adicional
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 08/07/2016
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */
var standa, attributes, parametros;
$(function() {
    standa = $("#standa").val();
    attributes = '&Ajax=on&standa=' + standa;
    getServicesAditionals();
    setTimeout(function() {
        $("div").css("height", "auto");
    }, 1000);

    $(".date").datepicker({
        dateFormat: 'yy-mm-dd'
    });

    $(".time").timepicker({
        timeFormat: "hh:mm",
        showSecond: false
    });
    $('#nom_transp').autocomplete({
        source: '../' + standa + '/transp/ajax_transp_transp.php?Option=buscarTransportadora' + attributes,
        minLength: 3,
        select: function(event, ui) {
            $('#cod_transp').val(ui.item.id);
            $('#nom_transp').val(trim(ui.item.value));
            $('body').removeAttr('class');
        }
    });

});

/* ! \fn: registrar
 *  \brief: valida el formulario  y registra un seguimiento adicional para una transportadora
 *  \author: Ing. Alexander Correa
 *  \date: 08/07/2016
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return 
 */
function registrar() {
    var conn = checkConnection();
    if (conn) {
        var val = validaciones();
        var cod_transp = $("#cod_transp").val();
        if (!cod_transp) {
            setTimeout(function() {
                inc_alerta("nom_transp", "Por favor ingresa una transportadora válida.");
            }, 530);
            val = false;
        }
        var fec_inicia, fec_finali;
        var fec_inicia = $("#fec_inicia").val() + " " + $("#hor_inicia").val() + ":00";
        var fec_finali = $("#fec_finali").val() + " " + $("#hor_finali").val() + ":00";
        var f = new Date();
        var and = "";

        if (Date.parse(fec_inicia) <= Date.parse(fec_finali)) {
            if (!$("#cod_consec").val() == 0) {
                if (Date.parse(fec_inicia) < f) {
                    setTimeout(function() {
                        inc_alerta("fec_inicia", "La fecha inicial no puede ser menor a la actual.");
                    }, 530);
                    val = false;
                }
            }
            if (val) {
                parametros = getDataForm();
                attributes = "Ajax=on&Option=registrarSeguimAditional&";
                $.ajax({
                    url: '../' + standa + '/transp/ajax_transp_transp.php',
                    type: 'POST',
                    data: attributes + parametros,
                    async: true,
                    beforeSend: function(obj) {
                        BlocK("Registrando Informacion...");
                    },
                    success: function(data) {
                        setTimeout(function() {
                            BlocK();
                        }, 1000);
                        if (data == -1) {
                            swal({
                                title: 'Seguimiento Adicional',
                                text: 'Ya existe una configuración similar para la transportadora seleccionada.',
                                type: 'error'
                            });
                        } else if (data == 1) {
                            swal({
                                title: 'Seguimiento Adicional',
                                text: 'Datos almacenados con éxito.',
                                type: 'success'
                            });
                            $("#fec_inicia").removeAttr('disabled');
                            $("#hor_inicia").removeAttr('disabled');
                            setTimeout(function() {
                                getServicesAditionals();
                            }, 1000);

                        } else {
                            swal({
                                title: 'Seguimiento Adicional',
                                text: 'Ocurrió un error inesperado al registrar los datos, por favor intenta nuevamente, si el error persiste por favor informar al proveedor.',
                                type: 'error'
                            });
                        }

                    },
                    complete: function() {
                        $("#cod_transp").val("");
                        $("#nom_transp").val("");
                        $("#fec_inicia").val("");
                        $("#fec_finali").val("");
                        $("#hor_inicia").val("");
                        $("#hor_finali").val("");
                        $("#cod_consec").val("0");

                    }
                });
            }
        } else {
            setTimeout(function() {
                inc_alerta("fec_inicia", "La fecha inicial no puede ser mayor a la final.");
            }, 530);

        }
    } else {
        swal({
            title: 'Seguimiento Adicional',
            text: 'Por favor verifica tu conexión a internet.',
            type: 'warning'
        });
    }
}

/* ! \fn: getServicesAditionals
 *  \brief: carga la lita de servicios adicionales
 *  \author: Ing. Alexander Correa
 *  \date: 08/07/2016
 *  \date modified: dia/mes/año
 *  \param: 	
 *  \return 
 */
function getServicesAditionals() {
    $.ajax({
        url: '../' + standa + '/transp/ajax_transp_transp.php',
        type: 'POST',
        data: attributes + "&Option=getServicesAditionals",
        async: true,
        beforeSend: function(obj) {
            BlocK("Cargando datos...", true);
        },
        success: function(data) {
            $("#tabla").empty();
            $("#tabla").append(data);
        },
        complete: function() {
            setTimeout(function() {
                BlocK();
            }, 1000);
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
}


/* ! \fn: editarConfiguracion
 *  \brief: carga los datos de una configuracion registrada para modificarla
 *  \author: Ing. Alexander Correa
 *  \date: 11/07/2016
 *  \date modified: dia/mes/año
 *  \param: obj	 => obj => objeto html con la informacion a editar	
 *  \return 
 */
function editarConfiguracion(obj) {
    var DLRow = $(obj).parent().parent();
    var cod_transp = DLRow.find("input[id^=cod_transp]").val();
    var nom_transp = DLRow.find("input[id^=abr_tercer]").val();
    var fec_inicia = DLRow.find("input[id^=fec_inicia]").val();
    var fec_finali = DLRow.find("input[id^=fec_finali]").val();
    var cod_consec = DLRow.find("input[id^=cod_consec]").val();
    var f = new Date();
    if (Date.parse(fec_inicia) < f) {
        $("#fec_inicia").attr('disabled', 'true');
        $("#hor_inicia").attr('disabled', 'true');
    }
    var hor_inicia = fec_inicia.substring(11, 16);
    var hor_finali = fec_finali.substring(11, 16);
    var fec_inicia = fec_inicia.substring(0, 10);
    var fec_finali = fec_finali.substring(0, 10);
    $("#cod_transp").val(cod_transp);
    $("#nom_transp").val(nom_transp);
    $("#fec_inicia").val(fec_inicia);
    $("#fec_finali").val(fec_finali);
    $("#hor_inicia").val(hor_inicia);
    $("#hor_finali").val(hor_finali);
    $("#cod_consec").val(cod_consec);
}

/* ! \fn: inactivarConfig
 *  \brief: inactiva una configuracioón registrada de una empresa
 *  \author: Ing. Alexander Correa
 *  \date: 11/07/2016
 *  \date modified: dia/mes/año
 *  \param: obj	 => object => objeto html con los datos	
 *  \return 
 */
function inactivarConfig(obj) {
    var DLRow = $(obj).parent().parent();
    var cod_consec = DLRow.find("input[id^=cod_consec]").val();
    var conn = checkConnection();
    if (conn) {
        swal({
            title: "Seguimiento Adicional",
            text: "¿Realmente Deseas inactivar la parametrización seleccionada?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function() {
            $.ajax({
                type: "POST",
                url: '../' + standa + '/transp/ajax_transp_transp.php',
                data: "Ajax=on&Option=inactivarConfig&cod_consec=" + cod_consec,
                async: true,
                success: function(datos) {
                    if (datos == 1) {
                        swal({
                            title: "Seguimiento Adicional",
                            text: "Configuración inactivada con éxito.",
                            type: "success",
                            showCancelButton: false,
                            closeOnConfirm: true,
                            showLoaderOnConfirm: true,
                        }, function() {
                            getServicesAditionals();
                        });
                    } else {
                        swal({
                            title: "Seguimiento Adicional",
                            text: "Error al inactivar la configuración, intenta nuevamente. Si el error persiste por favor informar.",
                            type: "error"
                        });
                    }
                }

            });
        });
    } else {
        swal({
            title: 'Seguimiento Adicional',
            text: 'Por favor verifica tu conexión a internet.',
            type: 'warning'
        });
    }
}
