/* ! \file: asi_horari_monito.js
 *  \brief: Archivo para manejar la asignacion de turnos
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 24/02/2016
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

$(function() {
    $(".date").datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 0
    });

    $(".time").timepicker({
        timeFormat: "hh:mm",
        showSecond: false
    });

    setTimeout(function() {
        $("#contenido div[name=sec]").css('height', 'auto');
        $("#usuarios div[name=sec]").css('height', 'auto');
        $("#datos div[name=sec]").css('height', 'auto');
        $("#secundarios div[name=sec]").css('height', 'auto');
        $("#usuarios h3").trigger("click");
        $("#datos h3").trigger("click");
        $("#secundarios h3").trigger("click");
        $("#secundarios").attr('class', 'hidden accordion ui-accordion ui-widget ui-helper-reset ui-accordion-icons');
    }, 1000);
});

/*! \fn: edita
 *  \brief: Crear el PopUp para editar la carga laboral de un controlador
 *  \author: Ing. Fabian Salinas
 *  \date: 06/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: key  integer  Identificador de la fila del controlador
 *  \return: 
 */
function edita(key) {
    try {
        swal({
            title: "Editar Carga Laboral",
            text: "\u00BFRealmente Deseas Editar la carga seleccionada?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: true,
            showLoaderOnConfirm: true,
        }, function() {
            var html = '';
            var libre = '';

            html += '<div class="Style2DIV">';
                html += '<div class="contenido">';
                    html += '<div class="col-sm-12 text-center cellHead">Editar Carga del Usuario hector.a</div>';
                    html += '<div class="col-sm-12">&nbsp;</div>';

                    html += '<div class="col-sm-6 text-right">';
                        html += 'Transportadoras con despachos libres: ';
                    html += '</div>';
                    html += '<div class="col-sm-6 text-left">';
                        html += '<select id="editTranspID" onchange="agregarTranspACarga()">';
                            html += '<option value="" libredespac="0">Seleccione una Opción</option>';
                            $("#transpID option").each(function(i, obj){
                                libre = $(this).attr('libredespac');
                                html += '<option value="' + $(this).val() + '" libredespac="' + libre + '" totdespac="' + $(this).attr('totdespac') + '" cod_grupox="' + $(this).attr('cod_grupox') + '" label="' + $(this).html() + '" ' + ( libre == '0' ? 'class="hidden"' : '' ) + '>' + $(this).html() + '</option>';
                            });
                        html += '</select>';
                    html += '</div>';
                    html += '<div id="editCarga_contenedor" key="' + key + '">';

                    $("#transpControl_" + key + " option").each(function(i, obj) {
                        html += '<div class="col-sm-12">';
                            html += '<div class="col-sm-12" name="editCarga_RowTransp" cantdespac="' + $(this).attr('cantdespac') + '" cod_transp="' + $(this).val() + '" cod_grupox="' + $(this).attr('cod_grupox') + '">';
                                html += '<div class="col-sm-2 text-right" name="libredespac">' + $(this).attr('cantdespac') + '</div>';
                                html += '<div class="col-sm-8 text-left" name="label">' + $(this).html() + '</div>';
                                html += '<div class="col-sm-2"><img width="16px" height="16px" class="pointer" onclick="RetirarTransp( $(this) )" src="../satt_standa/images/delete.png"></div>';
                            html += '</div>';
                        html += '</div>';
                    });

                    html += '</div>';
                    html += '<div class="text-center">';
                        html += '<input type="button" class="small save ui-button ui-widget ui-state-default ui-corner-all" value="&nbsp; Aceptar &nbsp;" onclick="aceptarEditCarga()">';
                        html += '<input type="button" class="small save ui-button ui-widget ui-state-default ui-corner-all" value="&nbsp; Cancelar &nbsp;" onclick="closePopUp()">';
                    html += '</div>';
                html += '</div>';
            html += '</div>';

            LoadPopupJQNoButton('open', 'Editar Carga Laboral', 'auto', '600px', false, false, true);
            var popup = $("#popID");
            popup.parent().children().children('.ui-dialog-titlebar-close').hide();
            popup.append(html);
            
            $('.ui-dialog').css({
                position:'fixed',
                left: ($(window).width() - $('.ui-dialog').outerWidth())/2,
                top: ($(window).height() - $('.ui-dialog').outerHeight())/2
            });
        });
    } catch (e) {
        console.log("Error Function edita: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: RetirarTransp
 *  \brief: Retira una transportadora de la carga laboral del controlador
 *  \author: Ing. Fabian Salinas
 *  \date: 06/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  obejct  Objeto que activa la funcion
 *  \return: 
 */
function RetirarTransp( obj ){
    try {
        var objData = $(obj).parent().parent();
        var option = $("#editTranspID option[value=" + objData.attr('cod_transp') + "]");

        var libredespac = parseInt(option.attr('libredespac')) + parseInt(objData.attr('cantdespac'));

        option.attr('libredespac', libredespac);
        option.removeAttr('class');
        option.html( libredespac + " - " + option.attr('label') );

        objData.parent().remove();
    } catch (e) {
        console.log("Error Function RetirarTransp: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: agregarTranspACarga
 *  \brief: Agrega la transportadora a la carga laboral del controlador
 *  \author: Ing. Fabian Salinas
 *  \date: 07/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function agregarTranspACarga(){
    try {
        var transp = $("#editTranspID option:selected");

        if( transp.val() == '' ) {
            return false;
        }

        var cantdespac = 0;
        var existe = false;
        $("#editCarga_contenedor div[name=editCarga_RowTransp]").each(function(){
            if( $(this).attr('cod_transp') == transp.val() ) {
                cantdespac = parseInt( $(this).attr('cantdespac') ) + parseInt( transp.attr('libredespac') );

                $(this).attr('cantdespac', cantdespac);
                $(this).find('div[name=libredespac]').html( cantdespac );

                existe = true;
            }
        });

        if( !existe ) {
            var html = '';
            html += '<div class="col-sm-12">'; 
                html += '<div class="col-sm-12" name="editCarga_RowTransp" cantdespac="' + transp.attr('libredespac') + '" cod_transp="' + transp.val() + '" cod_grupox="' + transp.attr('cod_grupox') + '">';
                    html += '<div class="col-sm-2 text-right" name="libredespac">' + transp.attr('libredespac') + '</div>';
                    html += '<div class="col-sm-8 text-left" name="label">' + transp.attr('label') + '</div>';
                    html += '<div class="col-sm-2"><img width="16px" height="16px" class="pointer" onclick="RetirarTransp( $(this) )" src="../satt_standa/images/delete.png"></div>';
                html += '</div>';
            html += '</div>';

            $("#editCarga_contenedor").append( html );
        }

        transp.attr('libredespac', '0');
        transp.html( transp.attr('label') );
        transp.attr('class', 'hidden');
        $("#editTranspID").val('');
    } catch (e) {
        console.log("Error Function agregarTranspACarga: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/* ! \fn: mostrar
 *  \brief: muestra los usuarios disponibles para carga laboral en la fecha ingresada
 *  \author: Ing. Alexander Correa
 *  \date: 24/02/2016
 *  \date modified: dia/mes/año
 *  \param:     
 *  \return 
 */
function mostrar() {
    $("#secundariosID").html('');

    var val = validaciones();
    if (val) {
        var hoy = new Date();
        var fec_inicia = $("#fec_inicio").val();
        var hor_inicia = $("#hor_inicio").val();
        var fec_finali = $("#fec_finali").val();
        var hor_finali = $("#hor_finali").val();
        fecini = fec_inicia + " " + hor_inicia + ":00";
        fecsal = fec_finali + " " + hor_finali + ":59";
        fecini = new Date(fecini.replace(/-/g, '/'));
        fecsal = new Date(fecsal.replace(/-/g, '/'));

        if (fecsal < hoy) {
            setTimeout(function() {
                inc_alerta("fec_finali", "Fecha Final menor a la actual.");
            }, 510);
            val = false;
        }
        if (fecini > fecsal) {
            setTimeout(function() {
                inc_alerta("fec_inicio", "Fecha Inicial mayor a la final.");
            }, 510);
            val = false;
        } else {
            var timeDiff = Math.abs(fecsal.getTime() - fecini.getTime());
            var diffHours = (timeDiff / (1000 * 3600));
            if (diffHours > 12) {
                setTimeout(function() {
                    inc_alerta("fec_inicio", "La diferencia entre fechas no puede ser mayor a 12 horas.");
                }, 510);
                val = false;
            }
        }
        if (fecsal < hoy) {
            setTimeout(function() {
                inc_alerta("fec_finali", "Fecha Final menor a la actual.");
            }, 510);
            val = false;
        }

        var valUsers = false;
        $("#usuariosID input[name^=users]:checked").each(function(){
            valUsers = true;
        });
        if( !valUsers ) {
            setTimeout(function() {
                inc_alerta("usuarios_0", "Seleccione al menos un usuario.");
            }, 510);
            val = false;
        }

        var valTransp = false;
        $("#usuariosID input[name^=transp]:checked").each(function(){
            valTransp = true;
        });
        if( !valTransp ) {
            setTimeout(function() {
                inc_alerta("transp_0", "Seleccione al menos una transportadora.");
            }, 510);
            val = false;
        }

        if (val == true) {
            var standa = $("#standa").val();
            var parametros = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/config/ajax_horari_monito.php",
                data: "&Ajax=on&Option=getData&standa=" + standa + "&" + parametros,
                async: true,
                beforeSend: function() {
                    BlocK("Cargando...", true);
                },
                success: function(datos) {
                    $("#datosID").html("");
                    $("#datosID").append(datos);
                    $("#usuarios h3").trigger("click");
                    $("#datos h3").trigger("click");
                },
                complete: function() {
                    BlocK();
                }
            });
        }
    }
}

/* ! \fn: ver
 *  \brief: muestra una ventana emergente con un formulario
 *  \author: Ing. Alexander Correa
 *  \date: 26/02/2016
 *  \date modified: dia/mes/año
 *  \param: datos    => html => formulario  
 *  \param: key  => int => elemento a sobreescrivir en la vsta principal    
 *  \return 
 */
function ver(datos, key) {
    LoadPopupJQNoButton('open', 'Editar Carga Laboral', 'auto', 'auto', false, true, true);
    var popup = $("#popID");
    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
    popup.append(datos); // lanza el popUp
}

/* ! \fn: eliminarDiv
 *  \brief: elimina una empresa asignada a un usuario para su monitoreo
 *  \author: Ing. Alexander Correa
 *  \date: 26/02/2016
 *  \date modified: dia/mes/año
 *  \param: key  => int => id del div a eliminar    
 *  \return 
 */
function eliminarDiv(key) {
    $("#div" + key).remove();
    $("#total").val($("#total").val() - 1);
}

/* ! \fn: addDiv()
 *  \brief: agrega una empresa a un controlador para su monitoreo
 *  \author: Ing. Alexander Correa
 *  \date: 26/02/2016
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return 
 */
function addDiv() {
    var total = $("#total").val();
    var empresa = $("#transp option:selected").text();
    var nit = $("#empresa").val();
    var despac = $("#descac").val();
    var des = $("#" + nit).val();
    var standa = $("#standa").val();

    var val = true;
    if (nit == "") {
        setTimeout(function() {
            inc_alerta("transp", "Por favor seleccione una opci\u00F3n");
        }, 510);
        val = false;
    }

    $(".empresa").each(function(ind, obj) {
        if ($(this).val() == nit) {
            setTimeout(function() {
                inc_alerta("transp", "Empresa ya parametrizada para el usuario");
            }, 510);
            val = false;
        }
    });
    if (val == true) {

        var html = '<div class="col-sm-12" id="div' + (total - 1 + 2) + '">';
        html += '<div class="col-sm-5"><label class="company">' + empresa + '</label><input type="hidden" class="empresa" name="empresa[]" id="empresa' + (total - 1 + 2) + '" value="' + nit + '" ></div>';
        html += '<div class="col-sm-5 despacho">' + despac + '</div>';
        html += '<div class="col-sm-2"><a onclick="eliminarDiv(' + (total - 1 + 2) + ')" class="pointer"><img src="../' + standa + '/images/delete.png" width="16px" height="16px" ></a></div>';
        html += '</div>';
        if (total > -1) {
            $("#div" + total).after(html);
        } else {
            $("#div").append(html);
        }
        var x = $("#total").val();
        $("#total").val(x - 1 + 2);

    }
}

/* ! \fn: registrar
 *  \brief: ajax para registrar la informacion en el formulario
 *  \author: Ing. Alexander Correa
 *  \date: 29/02/2016
 *  \date modified: 07/09/2016
 *  \modified by: Ing. Fabian Salinas
 *  \param: 
 *  \return 
 */
function registrar(){
    try {
        var standa = $("#standa").val();

        swal({
            title: "Registrar Pre-planeaci\u00F3n carga laboral",
            text: "\u00BFRealmente Deseas registrar estas Cargas laborales?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: true,
            showLoaderOnConfirm: true,
        }, function() {
            var parametros = traerParametrosRegistrarCarga();

            $.ajax({
                type: "POST",
                url: "../" + standa + "/config/ajax_horari_monito.php",
                data: parametros,
                async: true,
                beforeSend: function() {
                    BlocK("Cargando...", true);
                },
                success: function(datos) {
                    if (datos == 1) {
                        swal({
                            title: "Asignar Carga Laboral",
                            text: "Datos Registrados con éxito.",
                            type: "success",
                            showCancelButton: true,
                            closeOnConfirm: true,
                            showLoaderOnConfirm: true,
                        }, function() {
                            $("#form_asi_monito").submit();
                        });
                    } else {
                        swal({
                            title: "Asignar Carga Laboral",
                            text: "Error al registrar los datos.",
                            type: "error"
                        });
                    }
                },
                complete: function() {
                    BlocK();
                }
            });
        });
    } catch (e) {
        console.log("Error Function registrar: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: listUsuarios
 *  \brief: muestra los usuarios disponibles para carga laboral en la fecha ingresada
 *  \author: Ing. Fabian Salinas
 *  \date: 16/08/2016
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function listUsuarios() {
    $("#datosID").html("");

    var val = validaciones();
    if (val) {
        var hoy = new Date();
        var fec_inicia = $("#fec_inicio").val();
        var hor_inicia = $("#hor_inicio").val();
        var fec_finali = $("#fec_finali").val();
        var hor_finali = $("#hor_finali").val();
        fecini = fec_inicia + " " + hor_inicia + ":00";
        fecsal = fec_finali + " " + hor_finali + ":59";
        fecini = new Date(fecini.replace(/-/g, '/'));
        fecsal = new Date(fecsal.replace(/-/g, '/'));

        if (fecsal < hoy) {
            setTimeout(function() {
                inc_alerta("fec_finali", "Fecha Final menor a la actual.");
            }, 510);
            val = false;
        }
        if (fecini > fecsal) {
            setTimeout(function() {
                inc_alerta("fec_inicio", "Fecha Inicial mayor a la final.");
            }, 510);
            val = false;
        } else {
            var timeDiff = Math.abs(fecsal.getTime() - fecini.getTime());
            var diffHours = (timeDiff / (1000 * 3600));
            if (diffHours > 12) {
                setTimeout(function() {
                    inc_alerta("fec_inicio", "La diferencia entre fechas no puede ser mayor a 12 horas.");
                }, 510);
                val = false;
            }
        }
        if (val == true) {
            var standa = $("#standa").val();
            var parametros = getDataForm();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/config/ajax_horari_monito.php",
                data: "&Ajax=on&Option=listUsuarios&standa=" + standa + "&" + parametros,
                async: true,
                beforeSend: function() {
                    BlocK("Cargando...", true);
                },
                success: function(datos) {
                    $("#usuariosID").html(datos);
                    $("#contenido h3").trigger("click");
                    $("#usuarios h3").trigger("click");
                },
                complete: function() {
                    BlocK();
                }
            });
        }
    }
}

/*! \fn: aceptarEditCarga
 *  \brief: Edita la carga laboral del controlador
 *  \author: Ing. Fabian Salinas
 *  \date: 07/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param:
 *  \return: 
 */
function aceptarEditCarga(){
    try {
        var key = $("#editCarga_contenedor").attr('key');
        var divData = $("#empres" + key);
        var htmlLabel = '';
        var htmlSelect = '<select id="transpControl_' + key + '" name="transpControl" class="hidden">';
        var label;
        var cantDespac = 0;

        //Construye el contenido del div
        $("#editCarga_contenedor div[name=editCarga_RowTransp]").each(function(){
            label = $(this).find('div[name=label]').html();
            htmlLabel += '- ' + label + '<br>';
            htmlSelect += '<option cantdespac="' + $(this).attr('cantdespac') + '" value="' + $(this).attr('cod_transp') + '" cod_grupox="' + $(this).attr('cod_grupox') + '">' + label + '</option>';
            cantDespac += parseInt($(this).attr('cantdespac'));
        });

        htmlSelect += '</select>';

        //Llena el div con la nueva carga laboral para el controlador
        $("#cantDespac_" + key).html(cantDespac);
        divData.html( htmlLabel + htmlSelect );

        //Actualiza la info de los despachos libres
        $("#editTranspID option[libredespac!=0]").each(function(){
            $("#transpID option[value=" + $(this).val() + "]").attr( 'libredespac', $(this).attr('libredespac') );
        });

        closePopUp();
    } catch (e) {
        console.log("Error Function aceptarEditCarga: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: traerParametrosRegistrarCarga
 *  \brief: Trae los parametros para guardar la asignacion de carga laboral
 *  \author: Ing. Fabian Salinas
 *  \date: 07/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param:
 *  \return: string
 */
function traerParametrosRegistrarCarga(){
    try {
        var tipDistri = $("input[name=tipDistri]:checked");
        var parametros = "Ajax=on&Option=RegistrarDatos&standa=" + $("#standa").val();

        parametros += "&tip_distri=" + tipDistri.val();
        parametros += "&fec_inicio=" + $("#fec_inicio").val();
        parametros += "&hor_inicio=" + $("#hor_inicio").val();
        parametros += "&fec_finali=" + $("#fec_finali").val();
        parametros += "&hor_finali=" + $("#hor_finali").val();

        $("input[name^=users]").each(function(){
            parametros += "&users[]=" + $(this).val();
        });
        
        $("input[name^=usuarios]").each(function(){
            parametros += "&usuarios[]=" + $(this).val();
        });

        $("input[name^=usr_emailx]").each(function(){
            parametros += "&usr_emailx[]=" + $(this).val();
        });

        var despachos, ids, cat, transp;
        $("select[name=transpControl]").each(function(i, sel){
            despachos = '';
            ids = '';
            cat = '';
            transp = '';

            $(sel).find('option').each(function(j, opt){
                despachos += $(opt).attr('cantdespac') + ",";
                ids += $(opt).val() + ",";
                cat += $(opt).attr('cod_grupox') + ",";
                transp += '- '+ $(opt).html() + '<br>';
            });

            parametros += "&despachos[]=" + despachos.substring(0, despachos.length-1);
            parametros += "&ids[]=" + ids.substring(0, ids.length-1);
            parametros += "&cat[]=" + cat.substring(0, cat.length-1);
            parametros += "&transp[]=" + transp;
        });

        $("input[name^=ind_seg]:checked").each(function(){
            parametros += "&" + $(this).attr('name') + "=1";
        });

        if( tipDistri.val() == 'manual' ) {
            $("select[name=tip_despac_user]").each(function(i, sel){
                $(sel).find('option').each(function(j, opt){
                    parametros += "&tipDespacUser_" + $(sel).attr('consec_user') + "[" + $(opt).val() + "]=1";
                });
            });
        }

        return parametros;
    } catch (e) {
        console.log("Error Function traerParametrosRegistrarCarga: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: mostrarByTipDistri
 *  \brief: Llama la funcion para mostar la siguiente fase según el tipo de distribución
 *  \author: Ing. Fabian Salinas
 *  \date: 08/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param:
 *  \return: 
 */
function mostrarByTipDistri(){
    try {
        var tipDistri = $("#formID input[name^=tipDistri]:checked").val();

        switch (tipDistri) {
            case 'basic':
                mostrar();
                break;
            case 'manual':
                mostrarManual();
                break;
            default:
                return false;
        }
    } catch (e) {
        console.log("Error Function mostrarByTipDistri: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: mostrarManual
 *  \brief: Muestra el formulario de distribución manual
 *  \author: Ing. Fabian Salinas
 *  \date: 08/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param:
 *  \return: 
 */
function mostrarManual(){
    try {
        $("#usuarios h3").trigger("click");
        $("#secundarios h3").trigger("click");
        $("#datos h3").trigger("click");

        var param = "";

        $("#usuariosID input[type=checkbox]:checked").each(function(){
            param += "&" + $(this).attr('name') + "=" + $(this).val();
        });

        $("#formID input[type=text]").each(function(){
            if( $(this).val() != "" ) {
                param += "&" + $(this).attr('name') + "=" + $(this).val();
            }
        });

        pintarTabDistriManual(param);
        pintarTabAignacionVacia(param);
    } catch (e) {
        console.log("Error Function mostrarManual: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: pintarTabDistriManual
 *  \brief: Realiza la petición ajax para monstar la tabla de "distribucion manual"
 *  \author: Ing. Fabian Salinas
 *  \date: 08/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param:
 *  \return: 
 */
function pintarTabDistriManual(param){
    try {
        var standa = $("#standa").val();
        var parametros = "Ajax=on&Option=pintarTabDistriManual" + param;

        $.ajax({
            type: "POST",
            url: "../" + standa + "/config/ajax_horari_monito.php",
            data: parametros,
            async: true,
            beforeSend: function() {
                BlocK("Cargando...", true);
            },
            success: function(datos) {
                $("#secundariosID").html(datos);
            },
            complete: function() {
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Function pintarTabDistriManual: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: pintarTabAignacionVacia
 *  \brief: Realiza la petición ajax para monstar la tabla de "asignacion de usuarios por transportadora" vacia
 *  \author: Ing. Fabian Salinas
 *  \date: 08/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param:
 *  \return: 
 */
function pintarTabAignacionVacia(param){
    try {
        var standa = $("#standa").val();
        var parametros = "Ajax=on&Option=pintarTabAignacionVacia" + param;

        $.ajax({
            type: "POST",
            url: "../" + standa + "/config/ajax_horari_monito.php",
            data: parametros,
            async: true,
            success: function(datos) {
                $("#datosID").html(datos);
            }
        });
    } catch (e) {
        console.log("Error Function pintarTabAignacionVacia: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: quitarDisableCheckbox
 *  \brief: Quita el attr disabled de los checkbox destro de un objeto
 *  \author: Ing. Fabian Salinas
 *  \date: 15/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: idContenedor  string  Id del contenedor
 *  \return: 
 */
function quitarDisableCheckbox( idContenedor ){
    try {
        $("#" + idContenedor + " input[type=checkbox]").each(function(){
            $(this).removeAttr('disabled');
        });
    } catch (e) {
        console.log("Error Function quitarDisableCheckbox: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: formCargaManualUser
 *  \brief: Formulario para editar la carga manual del usuario
 *  \author: Ing. Fabian Salinas
 *  \date: 09/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  object  Objeto que activa la funcion
 *  \return: 
 */
function formCargaManualUser( obj ){
    try {
        var tabUsuarios = $("#tabUsuarios");
        var usractual = tabUsuarios.attr('usractual');

        if( usractual == "" ) {
            quitarDisableCheckbox("tabAsignarTipDes");
            quitarDisableCheckbox("tabAsignarEtapas");
        } else if( usractual != $(obj).val() ) {
            $("#secundariosID input[type=checkbox]:checked").each(function(){
                $(this).attr('checked', false);
            });

            $("#secundariosID input[type=checkbox]").each(function(){
                if( $(this).attr('user_' + $(obj).val()) == '1' ) {
                    $(this).attr('checked', true);
                }
            });
        }

        tabUsuarios.attr('usractual', $(obj).val() );
    } catch (e) {
        console.log("Error Function formCargaManualUser: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: calcularCantDespacPorUsuario
 *  \brief: Calula la cantidad de despachos asignados a un controlador y lo pinta en la tabla de visualizacion
 *  \author: Ing. Fabian Salinas
 *  \date: 19/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  object  objeto que activa el evento
 *  \return: 
 */
function calcularCantDespacPorUsuario( obj ){
    try {
        var user = $("#tabUsuarios input[name=manual_user]:checked");

        // Indica si el usuario tiene checkeado este input en el attr usuario del cliente
        if( $(obj).is(":checked") ) {
            $(obj).attr('user_' + user.val(), '1');
        } else {
            $(obj).attr('user_' + user.val(), '0');
        }

        var tabAsignarTipDes = $("#tabAsignarTipDes");
        var tabsTranspAsignacion = $("#tabsTranspAsignacion");
        var checkAsignacion;

        // Quita el check de la asignacion por transportadora
        tabsTranspAsignacion.find("input[type=checkbox]").each(function(){
            $(this).attr('checked', false);
            $(this).attr('user_' + user.val(), '0');
        });


        $("#tabAsignarEtapas input[type=checkbox]:checked").each(function(i, etapa){
            tabAsignarTipDes.find("input[type=checkbox]:checked").each(function(j, tipdes){
                checkAsignacion = tabsTranspAsignacion.find("input[type=checkbox][name=" + $(etapa).val() + "][cod_tipdes=" + $(tipdes).val() + "]");
                checkAsignacion.attr("checked", true);
                checkAsignacion.attr('user_' + user.val(), '1');
            });
        });

        var rowUser = $("#datosID div[name=rowUserAsignacion][consec=" + user.val() + "]");
        var bandera = false, segcar = false, segtra = false, segdes = false;
        var cantDespac = 0, cantTotal = 0;
        var labelTransp = "", htmlOptionsDespac = "";
        var tipDespacUser = {};
        var x;

        limpiarUsuarioDistriManual( rowUser );

        // Recorre las tablas por transportadora
        $("#secundariosID div[name=transpTipser]").each(function(i, tab){
            if( i == 0 ) {
                $(tab).find("div[name=rowTipDes]").each(function(k, row){
                    x = $(row).attr('cod_tipdes');
                    tipDespacUser[x] = "0";
                });
            }

            labelTransp += "- " + $(tab).attr('nom_transp') + "<br>";
            cantDespac = 0;

            // Recorre los input checkeados y suma la cantidad de despachos
            $(tab).find("input[type=checkbox]:checked").each(function(j, check){
                bandera = true;

                switch( $(check).attr('name') ) {
                    case "tipdes_cargue": segcar = true; break;
                    case "tipdes_transi": segtra = true; break;
                    case "tipdes_descar": segdes = true; break;
                }

                cantDespac += parseInt( $(check).attr('cant_despac') );
                x = $(check).val();
                tipDespacUser[x] = "1";
            });

            cantTotal += cantDespac;
            htmlOptionsDespac += '<option cod_grupox="' + $(tab).attr('cod_grupox') + '" cantdespac="' + cantDespac + '" value="' + $(tab).attr('cod_transp') + '">' + $(tab).attr('nom_transp') + '</option>';
        });

        // Agrega la data al usuario
        if( bandera == true ) {
            var htmlOptionsTipDespac = "";
            var htmlEmpres = labelTransp;
            htmlEmpres += '<select id="transpControl_0" name="transpControl" class="hidden">';
            htmlEmpres += htmlOptionsDespac;
            htmlEmpres += '</select>';

            $.each(tipDespacUser, function(key, val){
                if( val != "0" ) {
                    htmlOptionsTipDespac += '<option value="' + key + '">' + key + '</option>';
                }
            });

            rowUser.find("input[name^=despachos]").val(cantTotal);
            rowUser.find("label[name=cantDespac]").html(cantTotal);
            rowUser.find("div[name=empres]").html(htmlEmpres);
            rowUser.find("select[name=tip_despac_user]").html(htmlOptionsTipDespac);

            if( segcar == true ) {
                rowUser.find("input[name^=ind_segcar]").attr('checked', true);
            }
            if( segtra == true ) {
                rowUser.find("input[name^=ind_segtra]").attr('checked', true);
            }
            if( segdes == true ) {
                rowUser.find("input[name^=ind_segdes]").attr('checked', true);
            }
        }
    } catch (e) {
        console.log("Error Function calcularCantDespacPorUsuario: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: limpiarUsuarioDistriManual
 *  \brief: Limpia la data de la tabla del usuario, para la distribucion manual
 *  \author: Ing. Fabian Salinas
 *  \date: 13/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: row  object  (Div) fila a afectar
 *  \return: 
 */
function limpiarUsuarioDistriManual( row ){
    try {
        row.find("input[name^=despachos]").val('');
        row.find("label[name=cantDespac]").html('0');
        row.find("div[name=empres]").html('');
        row.find("select[name=tip_despac_user]").html('');

        row.find("input[type=checkbox]:checked").each(function(){
            $(this).attr('checked', false);
        });
    } catch (e) {
        console.log("Error Function limpiarUsuarioDistriManual: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: limpiarDivs
 *  \brief: Limpiar Divs al cambiar de tipo de distribucion
 *  \author: Ing. Fabian Salinas
 *  \date: 16/09/2016
 *  \date modified: dd/mm/aaaa
 *  \param: obj  object  objecto que activa la funcion
 *  \return: 
 */
function limpiarDivs( obj ){
    try {
        var form = $("#formID");

        if( $(obj).val() != form.attr('distriactual') ) {
            $("#datosID").html('');
            $("#usuariosID").html('');
            $("#secundariosID").html('');
            form.attr('distriactual', $(obj).val());

            if( $(obj).val() == 'basic' ) {
                $("#secundarios").attr('class', 'hidden accordion ui-accordion ui-widget ui-helper-reset ui-accordion-icons');
            } else {
                $("#secundarios").attr('class', 'accordion ui-accordion ui-widget ui-helper-reset ui-accordion-icons');
            }
        }
    } catch (e) {
        console.log("Error Function limpiarDivs: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}