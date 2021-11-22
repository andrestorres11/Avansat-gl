$(document).ready(function() {
    $("#datos").css("display", "none");
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

    $("input[type=button]").button();
    //Autocompletables
    var standa = $("#standaID").val();
    var attributes = '&Ajax=on&standa=' + standa;
    var boton = "";
    $("#nom_transpID").autocomplete({
        source: "../" + standa + "/transp/ajax_transp_transp.php?Option=buscarTransportadora" + attributes,
        minLength: 3,
        select: function(event, ui) {
            boton = "<input type='button' id='nuevo' value='Listado' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all' onclick='mostrar();'>";
            $("#cod_tercerID").val(ui.item.id);
            $("#boton").empty();
            $("#boton").append(boton);
            $("body").removeAttr("class");
        }
    });
    // para buscar la ciudad del tercero
    $("#ciudadID").autocomplete({
        source: "../" + standa + "/transp/ajax_transp_transp.php?Option=getCiudades" + attributes,
        minLength: 3,
        select: function(event, ui) {
            $("#cod_ciudadID").val(ui.item.id);
        }
    });

    $("#cod_marcaxID").change(function() {
        var marca = $("#cod_marcaxID").val();
        var standa = $("#standaID").val();
        var parametros = '&Ajax=on&Option=getLineas&standa=' + standa + "&marca=" + marca;
        $.ajax({
            url: "../" + standa + "/vehicu/ajax_trayle_trayle.php",
            type: "POST",
            data: parametros,
            async: false,
            success: function(data) {
                $("#cod_lineaxIDTD").empty();
                $("#cod_lineaxIDTD").append(data);
            }
        });
    });

    var res = $("#resultado").val();
    var ope = $("#opera").val();
    var con = $("#placa").val();
    var opc = $("#opcionID").val();
    var DIR_APLICA_CENTRAL = $("#standaID").val();
    if (res) {
        if (res == 1) {
            var src = "../" + DIR_APLICA_CENTRAL + "/imagenes/ok.png";
            var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
            mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
            mensaje += "<label>Vehiculos</label>";
            mensaje += "<div style='width:97%'>";
            mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Se " + ope + " El Vehiculo con placa: <b>" + con + "</b> Exitosamente.<br></font><br><img src='" + src + "'>";
            mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
        } else if (res == 0) {
            if (opc != 911) {
                var src = "../" + DIR_APLICA_CENTRAL + "/imagenes/bad.png";
                var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                mensaje += "<label>Vehiculos</label>";
                mensaje += "<div style='width:97%'>";
                mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Ocurrió un Error inesperado <br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font><br><img src='" + src + "'>";
                mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
            } else {
                var src = "../" + DIR_APLICA_CENTRAL + "/imagenes/bad.png";
                var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
                mensaje += "<label>Vehiculos</label>";
                mensaje += "<div style='width:97%'>";
                mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Ocurrió un Error, la placa ingresada ya esta asociado a otro vehículo <br> verifique e intente nuevamente.<br></font><br><img src='" + src + "'>";
                mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
            }
        } else if (res == 2) {
            var src = "../" + DIR_APLICA_CENTRAL + "/imagenes/bad.png";
            var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
            mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
            mensaje += "<label>Vehiculos</label>";
            mensaje += "<div style='width:97%'>";
            mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Error, el conductor seleccionado para el vehículo esta marcado como tal,<br>pero no esta registrado en la tabla de conductores.<br>Realice el registro del conductor e intente nuevamente.<br></font><br><img src='" + src + "'>";
            mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
        }
        LoadPopupJQNoButton('open', 'Resultado de la Operación', 'auto', 'auto', false, false, true);
        var popup = $("#popID");
        popup.parent().children().children('.ui-dialog-titlebar-close').hide();
        popup.append(mensaje); // //lanza el popU
    }
    // validacion para saber si es un usuaraio administrador o de una transportadora y mostrar los datos de la misma
    var total = $("#total").val();

    if (total == 1) {
        $("#DatosBasicosID").css({
            display: 'none'
        });
        mostrar();
    } else {
        $("#datos").css("display", "none");
    }

    $("#form88").css('padding', '0px');
    $("#form88").css('padding-top', '3px');
    $("#form88").css('padding-bottom', '3px');

    $(".contentAccordionForm").css('padding', '0px');
    $(".contentAccordionForm").css('padding-top', '3px');
    $(".contentAccordionForm").css('padding-bottom', '3px');
});



//funcion para mostrar la lista de los Vehiculos de una transportadora
function mostrar() {
    $("#form3").empty();
    var transp = $("#cod_tercerID").val();
    var standa = $("#standaID").val();
    var parametros = "Option=listaVehiculos&Ajax=on&cod_transp=" + transp;
    $.ajax({
        url: "../" + standa + "/vehicu/ajax_trayle_trayle.php",
        type: "POST",
        data: parametros,
        async: false,

        success: function(data) {
            $("#sec2").css("height", "auto");
            $("#form3").append(data); // pinta los datos de la consulta					
        }
    });
    $("#datos").fadeIn(3000); // visualza los datos despues de pintarlos
}


/******************************************************************************
 *	\fn: registrar												  			  *
 *	\brief: funcion para registros nuevos y modificaciones de conductores     *
 *		  recibe un string con la operación a realizar registrar o modificar  *
 *  \author: Ing. Alexander Correa 											  *
 *  \date: 31/08/2015														  *
 *  \date modified: 														  *
 *  \param: operacion: string con la operacion a realizar.					  *
 *  \param: 																  *
 *  \return popUp con el resultado de la operacion							  *
 ******************************************************************************/
function registrar(operacion) {
    //cierra popUp si hay inicialiado
    LoadPopupJQNoButton('close');
    //valido los datos generales del formulario
    quitarValidacionesGps();
    var val = validaciones();
    var standa = $("#standaID").val();
    if (val == true) {
        var file = $("#imagen").val();
        if (!file) {
            //crea el popUp para el mensaje de  respuesta del guardado  
            LoadPopupJQNoButton('open', 'Resultado de la Operación', 'auto', 'auto', false, false, true);
            var popup = $("#popID");

            var parametros = "Option=" + operacion + "&Ajax=on&";
            parametros += getDataForm(); //agrega los datos consignados en el formulario
            $.ajax({
                url: "../" + standa + "/vehicu/ajax_trayle_trayle.php",
                type: "POST",
                data: parametros,
                async: false,
                beforeSend: function() {
                    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                },
                success: function(data) {
                    popup.append(data); // lanza el popUp
                }
            });
        } else {
            $("#operacion").val(operacion);
            var conf = $("#num_configID").val();
            if (conf != 2 && conf != 3 && conf != 4 && conf != '2' && conf != '3' && conf != '4') {

                var datos = [
                    ["num_trayleID", "select", true]
                ];
                var v = inc_validar(datos);
                if (v == true) {
                    $("#form_vehicuID").submit();
                } else {
                    return false;
                }
            } else {
                $("#form_vehicuID").submit();
            }
        }
    }
}

function confirmado() {
    LoadPopupJQNoButton('close');
    $("#opcionID").val("");
    document.form_transpor.submit();
}



//funcion para resetear un formulario en el caso de registros nuevos
function borrar() {
    $("#form_transporID")[0].reset();
}

//funcion de confirmacion para la edicion, activacion e inactivacion de vehiculos
function confirmar(operacion) {

    LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', 'auto', false, false, true);
    var popup = $("#popID");
    var placa = $("#placa").val();
    if (!placa) {
        placa = $("#num_placaxID").val();
    }
    var onclick = "onclick='registrar(\"";
    onclick += operacion;
    onclick += "\")'";
    if (operacion == "registrarVehiculo") {
        operacion = "registar";
    } else if (operacion == "modificarVehiculo") {
        operacion = "modificar";
    } else if (operacion == "inactivarVehiculo") {
        operacion = "inactivar";
    } else if (operacion == "activarVehiculo") {
        operacion = "activar";
    }
    var msj = "<div style='text-align:center'>¿Está seguro de <b>" + operacion + "</b> el vehiculo con placa: <b>" + placa + "?</b><br><br><br><br>";
    msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' " + onclick + " class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/> &nbsp;&nbsp;&nbsp;&nbsp";
    msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><div>";

    popup.parent().children().children('.ui-dialog-titlebar-close').hide();

    popup.append(msj); // //lanza el popUp
}

function editarVehiculo(tipo, objeto) {
    var DLRow = $(objeto).parent().parent();
    var num_placax = DLRow.find("input[id^=num_placax]").val();
    $("#placa").val(num_placax);

    if (tipo == 1) {
        confirmar('activarVehiculo');
    } else if (tipo == 2) {
        confirmar('inactivarVehiculo');
    } else {
        LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', 'auto', false, false, true);
        var popup = $("#popID");
        var conductor = $("#nom_tercerID").val();
        var msj = "<div style='text-align:center'>¿Está seguro de <b>editar</b> el vehiculo de placa: <b>" + num_placax + "?</b><br><br><br><br>";
        msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='formulario()' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
        msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";

        popup.parent().children().children('.ui-dialog-titlebar-close').hide();
        popup.append(msj); // //lanza el popUp
    }
}

function imagen(url) {
    var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
    mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
    mensaje += "<div>";
    mensaje += "<div style='background-color:#FFFFFF'><br><img width='400px' height='400px' src='" + url + "'>";
    mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";

    LoadPopupJQNoButton('open', 'Foto del Vehículo', 'auto', 'auto', false, false, true);
    var popup = $("#popID");
    popup.parent().children().children('.ui-dialog-titlebar-close').hide();
    popup.append(mensaje); // //lanza el popUp
}

function comprobar() {
    try {
        var placa = $("#num_placaxID").val();
        var standa = $("#standaID").val();
        var parametros = "Option=verificarPlaca&Ajax=on&placa=" + placa;
        $.ajax({
            url: "../" + standa + "/vehicu/ajax_trayle_trayle.php",
            type: "POST",
            data: parametros,
            async: true,
            dataType: "json",
            beforeSend: function(obj) {
                $.blockUI({
                    theme: true,
                    title: 'Agregar Vehículo',
                    draggable: false,
                    message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Buscando Vehículo</p></center>'
                });
            },
            success: function(data) {
                $.unblockUI();
                var select = "<select id ='cod_lineaxID' class='form_01' validate='select' obl='1' name='vehicu[cod_lineax]'>";
                $.each(data.lineas, function(key, value) {
                    if (!value.cod_lineax) {
                        select += "<option value=''>Seleccione un elemento de la lista</option>";
                    } else {
                        select += "<option value='" + value.cod_lineax + "'>" + value.nom_lineax + "</option>";
                    }
                });
                select += "</select>";
                $("#cod_lineaxIDTD").html("");
                $("#cod_lineaxIDTD").html(select);
                $('#cod_marcaxID > option[value="' + data.principal.cod_marcax + '"]').attr('selected', 'selected');
                $('#cod_lineaxID > option[value="' + data.principal.cod_lineax + '"]').attr('selected', 'selected');
                $("#ano_modeloID").val(data.principal.ano_modelo);
                $("#ano_repoteID").val(data.principal.ano_repote);
                $('#cod_colorxID > option[value="' + data.principal.cod_colorx + '"]').attr('selected', 'selected');
                $('#cod_tipvehID > option[value="' + data.principal.cod_tipveh + '"]').attr('selected', 'selected');
                $('#cod_carrocID > option[value="' + data.principal.cod_carroc + '"]').attr('selected', 'selected');
                $("#num_motorxID").val(data.principal.num_motorx);
                $("#num_seriexID").val(data.principal.num_seriex);
                $("#val_pesoveID").val(data.principal.val_pesove);
                $("#val_capaciID").val(data.principal.val_capaci);
                $('#num_configID > option[value="' + data.principal.num_config + '"]').attr('selected', 'selected');
                $("#nom_vinculID").val(data.principal.nom_vincul);
                $("#fec_vigvinID").val(data.principal.fec_vigvin);
                $("#num_agasesID").val(data.principal.num_agases);
                $("#fec_vengasID").val(data.principal.fec_vengas);
                $("#num_tarproID").val(data.principal.num_tarpro);
                $('#cod_califiID > option[value="' + data.principal.cod_califi + '"]').attr('selected', 'selected');
                $("#num_polizaID").val(data.principal.num_poliza);
                $("#nom_asesoaID").val(data.principal.nom_asesoa);
                $("#fec_vigfinID").val(data.principal.fec_vigfin);
                $('#num_trayleID > option[value="' + data.principal.num_trayle + '"]').attr('selected', 'selected');
                $("#cod_tenedo").val(data.principal.cod_tenedo);
                $("#nom_poseed").val(data.principal.nom_poseed);
                $("#cod_propie").val(data.principal.cod_propie);
                $("#nom_propie").val(data.principal.nom_propie);
                $("#cod_conduc").val(data.principal.cod_conduc);
                $("#nom_conduc").val(data.principal.nom_conduc);
            }
        });
    } catch (e) {
        console.log("Error Funcion comprobar: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

function getTercerTransp(activi) {
    try {
        var mensaje;
        switch (activi) {
            case 4:
                mensaje = "Lista Conductores";
                break;
            case 5:
                mensaje = "Lista de Propietarios";
                break;
            case 6:
                mensaje = "Lista de Poseedores";
                break;

        }
        var transp = $("#cod_transpID").val();
        var standa = $("#standaID").val();
        var parametros = "Option=getTercerTransp&Ajax=on&transp=" + transp + "&cod_activi=" + activi;
        $.ajax({
            url: "../" + standa + "/vehicu/ajax_trayle_trayle.php",
            type: "POST",
            data: parametros,
            async: false,
            success: function(data) {
                LoadPopupJQNoButton('open', mensaje, 'auto', 'auto', false, false, true);
                var popup = $("#popID");
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.append(data); // //lanza el popUp
            }
        });
    } catch (e) {
        console.log("Error Funcion getPoseedores: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

function pintar(objeto, activi) {
    try {

        var DLRow = $(objeto).parent().parent();
        var nombre = $(objeto).parent().next().html();
        var documento = $(objeto).parent().children().html();
        switch (activi) {
            case 4:
                $("#cod_conduc").val(documento);
                $("#nom_conduc").val(nombre);
                break;
            case 5:
                $("#cod_propie").val(documento);
                $("#nom_propie").val(nombre);
                break;
            case 6:
                $("#cod_tenedo").val(documento);
                $("#nom_poseed").val(nombre);
                break;

        }

        closePopUp();
    } catch (e) {
        console.log("Error Fuction pintar: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

function validaIdGPS(objeto) {
    try {
        var standa = $("#standaID").val();
        var parametros = "Option=getValidaIdGPS&Ajax=on&cod_operad=" + $(objeto).val();
        $.ajax({
            url: "../" + standa + "/vehicu/ajax_trayle_trayle.php",
            type: "POST",
            data: parametros,
            async: false,
            success: function(data) {
                var idgps = jQuery.parseJSON(data);
                console.log(idgps.ind_usaidx);
                if (idgps.ind_usaidx == 'S') {
                    $('#idx_gpsxxxID').attr('obl', '1');
                } else {
                    $('#idx_gpsxxxID').removeAttr('obl');
                }
            }
        });

    } catch (e) {
        console.log("Error Fuction validaIdGPS: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

function addOpeGps(){
	num_placax = $("#num_placaxID").val();
	if(validaDataGps() && num_placax !=''){
		var standa = $("#standaID").val();
		var parametros = "Option=saveOpeGps&Ajax=on";
		var cod_opegps = $('#cod_opegpsID').val();
		var usr_gpsxxx = $('#usr_gpsxxxID').val();
		var clv_gpsxxx = $('#clv_gpsxxxID').val();
		var idx_gpsxxx = $('#idx_gpsxxxID').val();
        var ind_vehicu = 1;
		data = {
			num_placax, cod_opegps,usr_gpsxxx,clv_gpsxxx,idx_gpsxxx,ind_vehicu
		};
		$.ajax({
			url: "../" + standa + "/vehicu/ajax_trayle_trayle.php?"+parametros,
			type: "POST",
			data,
			dataType: "json",
			async: false,
			success: function(data) {
				alert(data['msj']);
				if(data['status']==1000){
					addInputOpsGps(cod_opegps);
					$('#cod_opegpsID').val('');
					$('#usr_gpsxxxID').val('');
					$('#clv_gpsxxxID').val('');
					$('#idx_gpsxxxID').val('');
				}
			}
		});
		
	}else{
		if(num_placax == ''){
			alert("Diligencie el numero de placa.");
		}
	}
}

function addInputOpsGps(cod_opegps){
	elementoClonado = $('#form88').clone().appendTo('#formGps');
    $(elementoClonado).attr('id','elem_'+cod_opegps);
		var num_elementos = $(".opegps").length-1;
		elementoClonado.find('.inputgps').find('select option').each(function() {
			if($(this).val() == cod_opegps){
				$(this).attr('selected',true);
			}
		});
		elementoClonado.find('.inputgps').find('select,input').each(function() {
			var name = $(this).attr('name');
			var id_name = $(this).attr('id');
			$(this).attr('name',name + '[' + num_elementos + ']');
			$(this).attr('id',id_name + num_elementos );
			$(this).attr('disabled',true);
			if(id_name + num_elementos == 'idx_gpsxxxID' + num_elementos){
				$(this).parent().parent().append(`<td align="center">
													<img style="cursor:pointer;" width="25px" onclick="deleteOpeGps(`+cod_opegps+`,this)" src="../satt_standa/images/delete.png">
												 </td>`);
			}
		});		

}

function deleteOpeGps(cod_opegps, elemento){
	var num_placax = $("#num_placaxID").val();
	if(num_placax !=''){
		var standa = $("#standaID").val();
		var parametros = "Option=deteleOpeGps&Ajax=on";
		var cod_opegps = cod_opegps;
        var ind_vehicu = 1;
		data = {
			num_placax, cod_opegps, ind_vehicu
		};
		$.ajax({
			url: "../" + standa + "/vehicu/ajax_trayle_trayle.php?"+parametros,
			type: "POST",
			data,
			dataType: "json",
			async: false,
			success: function(data) {
			alert(data['msj']);
				if(data['status']==1000){
					var contenedor = $(elemento).closest('.contentAccordionForm');
					contenedor.remove();
				}
			}
		});
	}
}

function validaDataGps(){
		var elemento = $('#form88').find('.inputgps');
		try {
			var datos = [];
			var i = 0;	
		elemento.find('select,input').each(function() {
            var obl = $(this).attr('obl');
            if(obl=='1'){
                $(this).attr('obl','1');
            }
			
		})
        elemento.find('input[validate]').each(function(index) {
            var obj = ""; // id del campo; si es radio es el name
            var tipo = ""; // tipo de dato a validar. Consultar tipos en : validator.js
            var min = ""; // cantidad minima de caracteres
            var max = ""; // cantidad maxima de carcteres
            var obl = ""; // obligatorio booleano

            if ($(this).attr("type") == "radio") {
                obj = $(this).attr("name");
            } else {
                obj = $(this).attr("id");
            }

            tipo = $(this).attr("validate");
            if (tipo == "placa") {
                if ($(this).attr("obl") === '1') {
                    datos[i] = [obj, tipo, true];
                } else {
                    datos[i] = [obj, tipo];
                }
            } else {
                if ($(this).attr("minlength")) {
                    min = $(this).attr("minlength");
                } else {
                    min = 1;
                }
                if ($(this).attr("maxlength")) {
                    max = $(this).attr("maxlength");
                } else {
                    max = 50;
                }
                if ($(this).attr("type") != "file") {
                    if ($(this).attr("obl") === '1') {
                        datos[i] = [obj, tipo, min, max, true];

                    } else {
                        datos[i] = [obj, tipo, min, max];
                    }
                } else {
                    min = $(this).attr("format");
                    arreglo = min.split(",");
                    if ($(this).attr("obl") === '1') {
                        obl = true;
                    } else {
                        obl = false;
                    }
                    datos[i] = [obj, tipo, arreglo, obl];
                }
            }
            i++;
        });

        elemento.find('select[validate]').each(function(index) {
            var obj = ""; // id del campo; si es radio es el name
            var tipo = ""; // tipo de dato a validar. Consultar tipos en : validator.js
            var obl = ""; // obligatorio booleano

            obj = $(this).attr("id");
            tipo = $(this).attr("validate");
            if ($(this).attr("obl") == 1) {
                datos[i] = [obj, tipo, true];
            } else {
                datos[i] = [obj, tipo];
            }
            i++;

        });
		var validacion = inc_validar(datos);

        return validacion;
    } catch (e) {
        console.log("Error Fuction validaciones: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
		
}

function quitarValidacionesGps(){
    var elemento = $('#form88').find('.inputgps');
    elemento.find('select,input').each(function() {
        var obl = $(this).attr('obl','');
    })
}