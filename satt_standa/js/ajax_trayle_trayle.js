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

	var res = $("#resultado").val();
	var ope = $("#opera").val();
	var con = $("#conductor").val();
	var opc = $("#opcionID").val();
	var DIR_APLICA_CENTRAL = $("#standaID").val();
	if (res) {
		if (res == 1) {
			var src = "../" + DIR_APLICA_CENTRAL + "/imagenes/ok.png";
			var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
			mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
			mensaje += "<label>traylers</label>";
			mensaje += "<div style='width:97%'>";
			mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Se " + ope + " El Trayler de: <b>" + con + "</b> Exitosamente.<br></font><br><img src='" + src + "'>";
			mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
		} else if (res == 0) {
			if (opc != 911) {
				var src = "../" + DIR_APLICA_CENTRAL + "/imagenes/bad.png";
				var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
				mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
				mensaje += "<label>traylers</label>";
				mensaje += "<div style='width:97%'>";
				mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Ocurrió un Error inesperado <br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font><br><img src='" + src + "'>";
				mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
			} else {
				var src = "../" + DIR_APLICA_CENTRAL + "/imagenes/bad.png";
				var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
				mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
				mensaje += "<label>traylers</label>";
				mensaje += "<div style='width:97%'>";
				mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Ocurrió un Error, el número de remolque ingresado ya esta asociado a otro trayle <br> verifique e intente nuevamente.<br></font><br><img src='" + src + "'>";
				mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
			}
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

});



//funcion para mostrar la lista de los traylers de una transportadora
function mostrar() {
	$("#form3").empty();
	var transp = $("#cod_tercerID").val();
	var standa = $("#standaID").val();
	var parametros = "Option=listaTrayles&Ajax=on&cod_transp=" + transp;
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

	$("#form88").css('padding', '0px');
    $("#form88").css('padding-top', '3px');
    $("#form88").css('padding-bottom', '3px');

    $(".contentAccordionForm").css('padding', '0px');
    $(".contentAccordionForm").css('padding-top', '3px');
    $(".contentAccordionForm").css('padding-bottom', '3px');
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
			document.getElementById("form_transporID").submit();
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

function addOpeGps(){
	num_trayle = $("#num_trayleID").val();
	if(validaDataGps() && num_trayle !=''){
		var standa = $("#standaID").val();
		var parametros = "Option=saveOpeGps&Ajax=on";
		var cod_opegps = $('#cod_opegpsID').val();
		var usr_gpsxxx = $('#usr_gpsxxxID').val();
		var clv_gpsxxx = $('#clv_gpsxxxID').val();
		var idx_gpsxxx = $('#idx_gpsxxxID').val();
		data = {
			num_trayle, cod_opegps,usr_gpsxxx,clv_gpsxxx,idx_gpsxxx
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
		if(num_trayle == ''){
			alert("Diligencie el numero de remolque.");
		}
	}
}

function addInputOpsGps(cod_opegps){
	elementoClonado = $('#form88').clone().appendTo('#formGps');
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
	var num_trayle = $("#num_trayleID").val();
	if(num_trayle !=''){
		var standa = $("#standaID").val();
		var parametros = "Option=deteleOpeGps&Ajax=on";
		var cod_opegps = cod_opegps;
		data = {
			num_trayle, cod_opegps
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

//funcion de confirmacion para la edicion, activacion e inactivacion de traylers
function confirmar(operacion) {

	LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', 'auto', false, false, true);
	var popup = $("#popID");
	var tercero = $("#nom_tercerID").val();
	if (!tercero) {
		tercero = $("#dueno").val();
	}
	var onclick = "onclick='registrar(\"";
	onclick += operacion;
	onclick += "\")'";
	var msj = "<div style='text-align:center'>¿Está seguro de <b>" + operacion + "</b> el Trayler de: <b>" + tercero + "?</b><br><br><br><br>";
	msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' " + onclick + " class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/> &nbsp;&nbsp;&nbsp;&nbsp";
	msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><div>";

	popup.parent().children().children('.ui-dialog-titlebar-close').hide();

	popup.append(msj); // //lanza el popUp
}

function editarRemolque(tipo, objeto) {
	var DLRow = $(objeto).parent().parent();
	var num_trayle = DLRow.find("input[id^=num_trayle]").val();
	var nom_propie = DLRow.find("input[id^=nom_propie]").val();
	$("#cod_tercerID").val(num_trayle);
	$("#nom_tercerID").val(nom_propie);

	if (tipo == 1) {
		confirmar('activar');
	} else if (tipo == 2) {
		confirmar('inactivar');
	} else {
		LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', 'auto', false, false, true);
		var popup = $("#popID");
		var conductor = $("#nom_tercerID").val();
		var msj = "<div style='text-align:center'>¿Está seguro de <b>editar</b> el Trayler de: <b>" + conductor + "?</b><br><br><br><br>";
		msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='formulario()' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
		msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";

		popup.parent().children().children('.ui-dialog-titlebar-close').hide();
		popup.append(msj); // //lanza el popUp
	}
}

function imagen() {
	var url = $("#url").val();
	var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
	mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
	mensaje += "<div>";
	mensaje += "<div style='background-color:#FFFFFF'><br><img width='400px' height='400px' src='" + url + "'>";
	mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";

	LoadPopupJQNoButton('open', 'Foto del Trayler', 'auto', 'auto', false, false, true);
	var popup = $("#popID");
	popup.parent().children().children('.ui-dialog-titlebar-close').hide();
	popup.append(mensaje); // //lanza el popUp
}

function comprobar() {
	try {
		var trayle = $("#num_trayleID").val();
		var standa = $("#standaID").val();
		var parametros = "Option=verificar&Ajax=on&trayle=" + trayle;

		$.ajax({
			url: "../" + standa + "/vehicu/ajax_trayle_trayle.php",
			type: "POST",
			data: parametros,
			async: false,
			dataType: "json",
			success: function(data) {
				$('#cod_marcaxID > option[value="' + data.cod_marcax + '"]').attr('selected', 'selected');
				$("#ano_modeloID").val(data.ano_modelo);
				$('#cod_configID > option[value="' + data.cod_config + '"]').attr('selected', 'selected');
				$("#tra_pesoxxID").val(data.tra_pesoxx);
				$("#tra_capaciID").val(data.tra_capaci);
				$("#tra_anchoxID").val(data.tra_anchox);
				$("#tra_altoxxID").val(data.tra_altoxx);
				$("#tra_largoxID").val(data.tra_largox);
				$("#tra_volposID").val(data.tra_largox);
				$("#tip_tramitID").val(data.tip_tramit);
				$('#cod_carrocID > option[value="' + data.cod_carroc + '"]').attr('selected', 'selected');
				$("#ser_chasisID").val(data.ser_chasis);
				$("#nom_propieID").val(data.nom_propie);
				$('#cod_coloreID > option[value="' + data.cod_colore + '"]').attr('selected', 'selected');
				/*if(data != 1){
						var src = "../"+standa+"/imagenes/bad.png";
						var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
	                  			mensaje +=	"<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
	                  				mensaje +=	"<label>traylers</label>";
	                  					mensaje += "<div style='width:97%'>";
		                  					mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>El número de Remolque: <b>"+trayle+"</b> <br> Ya esta asociado a otro remolque, verifique e intente nuevamente.<br><img src='"+src+"'>";
		        		mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";	          
	           			LoadPopupJQNoButton( 'open', 'Error', 'auto', 'auto', false, false, true );
	           			var popup = $("#popID");
						popup.parent().children().children('.ui-dialog-titlebar-close').hide();
						popup.append(mensaje);// //lanza el popU
	           		}*/

			}
		});
	} catch (e) {
		console.log("Error Fuction comprobar: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function quitarValidacionesGps(){
    var elemento = $('#form88').find('.inputgps');
    elemento.find('select,input').each(function() {
        var obl = $(this).attr('obl','');
    })
}