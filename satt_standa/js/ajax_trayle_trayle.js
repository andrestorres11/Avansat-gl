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
			$("#sec1").css("height", "auto");
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