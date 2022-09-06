$(document).ready(function() {

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
			mensaje += "<label>Conductores</label>";
			mensaje += "<div style='width:97%'>";
			mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Se " + ope + " El Conductor: <b>" + con + "</b> Exitosamente.<br></font><br><img src='" + src + "'>";
			mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
		} else if (res == 0) {
			if (opc != 911) {
				var src = "../" + DIR_APLICA_CENTRAL + "/imagenes/bad.png";
				var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
				mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
				mensaje += "<label>Conductores</label>";
				mensaje += "<div style='width:97%'>";
				mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Ocurrió un Error inesperado <br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font><br><img src='" + src + "'>";
				mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
			} else {
				var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
				mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
				mensaje += "<label>Conductores</label>";
				mensaje += "<div style='width:97%'>";
				mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>Ocurrió un Error al registrar el tercero, <br> el númer ode documento o NIT ya esta asociado a otro tercero.<br>verifique e intente nuevamente.</font><br><img src='" + src + "'>";
				mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";
			}
		}
		LoadPopupJQNoButton('open', 'Resultado de la Operación', 'auto', 'auto', false, false, true);
		var popup = $("#popID");
		popup.parent().children().children('.ui-dialog-titlebar-close').hide();
		popup.append(mensaje); // //lanza el popUp
	}
	// validacion para saber si es un usuaraio administrador o de una transportadora y mostrar los datos de la misma
	var total = $("#total").val();

	if (total == 1) {
		mostrar();
	} else {
		$("#datos").css("display", "none");
	}
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
			$("#cod_transpID").val(ui.item.id);
			$("#boton").empty();
			$("#boton").append(boton);
			$("body").removeAttr("class");
		}
	});
	// para buscar la ciudad del conductor
	
	$("#ciudadID").autocomplete({
		source: "../" + standa + "/transp/ajax_transp_transp.php?Option=getCiudades" + attributes + "&cod_paisxx=" + $("#cod_paisxxID").val(),
		minLength: 3,
		select: function(event, ui) {
			$("#cod_ciudadID").val(ui.item.id);
		}
	});

	$("#paisID").autocomplete({
		source: "../" + standa + "/transp/ajax_transp_transp.php?Option=buscarPaises" + attributes,
		minLength: 3,
		select: function(event, ui) {
			$("#cod_paisxxID").val(ui.item.id);
			traerTipDocumento(ui.item.id);
		}
	});
});

//funcion para mostrar la lista de los conductores de una transportadora
function mostrar() {
	$("#form3").empty();
	var transp = $("#cod_transpID").val();
	var standa = $("#standaID").val();
	var parametros = "Option=listaConductores&Ajax=on&cod_transp=" + transp;
	$.ajax({
		url: "../" + standa + "/conduc/ajax_conduc_conduc.php",
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
function registrar(operacion, cod_conduc) {
	//cierra popUp si hay inicialiado
	LoadPopupJQNoButton('close');
	//valido los datos generales del formulario
	var val = validaciones();
	var standa = $("#standaID").val();
	if (val == true) {
		//crea el popUp para el mensaje de  respuesta del guardado  

		var file = $("#imagen").val();
		if (!file) {
			LoadPopupJQNoButton('open', 'Resultado de la Operación', 'auto', 'auto', false, false, true);
			var popup = $("#popID");
			var formData = "Option=" + operacion + "&Ajax=on&cod_conduc="+cod_conduc+"&";
			formData += getDataForm(); //agrega los datos consignados en el formulario
			$.ajax({
				url: "../" + standa + "/conduc/ajax_conduc_conduc.php",
				type: "POST",
				data: formData,
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

//funcion de confirmacion para la edicion, eliminacion e inactivacion de conductores
function confirmar(operacion, cod_conduc) {

	LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', 'auto', false, false, true);
	var popup = $("#popID");
	var Conductor = $("#nom_conducID").val();
	if (!Conductor) {
		Conductor = $("#abr_terer").val();
	}
	var onclick = "onclick='registrar(\"";
	onclick += operacion;
	onclick += "\", "+cod_conduc+")'";
	var msj = "<div style='text-align:center'>¿Está seguro de <b>" + operacion + "</b> el Conductor: <b>" + Conductor + "?</b><br><br><br><br>";
	msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' " + onclick + " class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/> &nbsp;&nbsp;&nbsp;&nbsp";
	msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><div>";

	popup.parent().children().children('.ui-dialog-titlebar-close').hide();

	popup.append(msj); // //lanza el popUp
}

//funcion para las opciones del dinamicList
function editarConductor(tipo, objeto) {

	var DLRow = $(objeto).parent().parent();
	var cod_conduc = DLRow.find("input[id^=cod_tercer]").val();
	var nom_conduc = DLRow.find("input[id^=nom_tercer]").val();
	$("#cod_conducID").val(cod_conduc);
	$("#nom_conducID").val(nom_conduc);
	//alert(cod_tercer+" - "+nom_transp);


	if (tipo == 1) {
		confirmar('activar', cod_conduc);
	} else if (tipo == 2) {
		confirmar('inactivar', cod_conduc);
	} else if (tipo == 3) {
		LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', '300', false, false, true);
		var popup = $("#popID");
		var conductor = $("#nom_conducID").val();
		var msj = "<div style='text-align:center'>¿Está seguro de <b>Imprimir</b> el conductor: <b>" + conductor + "?</b><br><br><br><br>";
		msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='imprimir()' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
		msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";

		popup.parent().children().children('.ui-dialog-titlebar-close').hide();
		popup.append(msj); // //lanza el popUp
	} else {
		LoadPopupJQNoButton('open', 'Confirmar Operación', 'auto', '300', false, false, true);
		var popup = $("#popID");
		var conductor = $("#nom_conducID").val();
		var msj = "<div style='text-align:center'>¿Está seguro de <b>editar</b> el conductor: <b>" + conductor + "?</b><br><br><br><br>";
		msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='formulario()' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
		msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";

		popup.parent().children().children('.ui-dialog-titlebar-close').hide();
		popup.append(msj); // //lanza el popUp
	}
}

function agregarExperiencia() {
	var cantidad = parseInt($("#cantidadID").val()) + 1;
	var tabla = '<hr>';
	tabla += '<table width="100%" cellspacing="0" cellpadding="3" border="0" align="center">';
	tabla += '<tbody>';
	tabla += '<tr>';
	tabla += '<td width="25%" valign="" height="" align="right" colspan="" rowspan="" class="celda_etiqueta" id="tag0TD">';
	tabla += '<label " id="tag0ID" name="tag0" onblur="this.className=' + "campo_texto" + ';" onfocus="this.className=' + "campo_texto_on" + ';" onkeypress="return NumericInput( event )"> Empresa:</label>';
	tabla += '</td>';
	tabla += '<td width="25%" valign="" height="" align="left" colspan="" rowspan="" class="celda_info" id="empresa0TD">'
	tabla += '<input type="text" validate="alpha" minlength="5" maxlength="100" id="empresa' + cantidad + '" name="empresa[' + cantidad + ']" onblur="this.className=' + "campo_texto" + ';" onfocus="this.className=' + "campo_texto_on" + ';" onkeypress="return AlphaInput( event )" class="campo_texto">';
	tabla += '</td>';
	tabla += '</tr>';
	tabla += '<tr>';
	tabla += '<td width="25%" valign="" height="" align="right" colspan="" rowspan="" class="celda_etiqueta" id="tag0TD">';
	tabla += '<label " id="tag0ID" name="tag0" onblur="this.className=' + "campo_texto" + ';" onfocus="this.className=' + "campo_texto_on" + ';" onkeypress="return NumericInput( event )">Teléfono:</label>';
	tabla += '</td>';
	tabla += '<td width="25%" valign="" height="" align="left" colspan="" rowspan="" class="celda_info" id="telefono0TD">';
	tabla += '<input type="text" validate="numero" minlength="7" maxlength="10" id="telefono' + cantidad + '" name="telefono[' + cantidad + ']" onblur="this.className=' + "campo_texto" + ';" onfocus="this.className=' + "campo_texto_on" + ';" class="campo_texto">';
	tabla += '</td>';
	tabla += '<td width="25%" valign="" height="" align="right" colspan="" rowspan="" class="celda_etiqueta" id="tag0TD">';
	tabla += '<label " id="tag0ID" name="tag0" onblur="this.className=' + "campo_texto" + ';" onfocus="this.className=' + "campo_texto_on" + ';" onkeypress="return NumericInput( event )">Viajes:</label>';
	tabla += '</td>';
	tabla += '<td width="25%" valign="" height="" align="left" colspan="" rowspan="" class="celda_info" id="viajes0TD">';
	tabla += '<input type="text" validate="numero" minlength="1" maxlength="4" id="viajes' + cantidad + '" name="viajes[' + cantidad + ']" onblur="this.className=' + "campo_texto" + ';" onfocus="this.className=' + "campo_texto_on" + ';" class="campo_texto">';
	tabla += '</td>';
	tabla += '</tr>';
	tabla += '<tr>';
	tabla += '<td width="25%" valign="" height="" align="right" colspan="" rowspan="" class="celda_etiqueta" id="tag0TD">';
	tabla += '<label " id="tag0ID" name="tag0" onblur="this.className=' + "campo_texto" + ';" onfocus="this.className=' + "campo_texto_on" + ';" onkeypress="return NumericInput( event )">Antigüedad:</label>';
	tabla += '</td>';
	tabla += '<td width="25%" valign="" height="" align="left" colspan="" rowspan="" class="celda_info" id="antiguedad0TD">';
	tabla += '<input type="text" validate="alpha" minlength="5" maxlength="50" id="antiguedad' + cantidad + '" name="antiguedad[' + cantidad + ']" onblur="this.className=' + "campo_texto" + ';" onfocus="this.className=' + "campo_texto_on" + ';" class="campo_texto">';
	tabla += '</td>';
	tabla += '<td width="25%" valign="" height="" align="right" colspan="" rowspan="" class="celda_etiqueta" id="tag0TD">';
	tabla += '<label " id="tag0ID" name="tag0" onblur="this.className=' + "campo_texto" + ';" onfocus="this.className=' + "campo_texto_on" + ';" onkeypress="return NumericInput( event )"> Mercancia:</label>';
	tabla += '</td>';
	tabla += '<td width="25%" valign="" height="" align="left" colspan="" rowspan="" class="celda_info" id="mercancia0TD">';
	tabla += '<input type="text" validate="dir" minlength="7" maxlength="50" id="mercancia' + cantidad + '" name="mercancia[' + cantidad + ']" onblur="this.className=' + "campo_texto" + ';" onfocus="this.className=' + "campo_texto_on" + ';" class="campo_texto">';
	tabla += '</td>';
	tabla += '</tr>';
	tabla += '</tbody>';
	tabla += '</table>';

	$(tabla).appendTo('#form5').hide().show("slow");
	$("#sec5").css("height", "auto");
	$("#cantidadID").val(cantidad);

}


function ocultarBotones() {
	$("#botones").hide();
}

function mostrarBotones() {
	$("#botones").show();
}

function imagen() {
	var url = $("#url").val();

	var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
	mensaje += "<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
	mensaje += "<div >";
	mensaje += "<div style='background-color:#FFFFFF'><br><img width='400px' height='400px' src='" + url + "'>";
	mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";

	LoadPopupJQNoButton('open', 'Foto del Conductor', 'auto', 'auto', false, false, true);
	var popup = $("#popID");
	popup.parent().children().children('.ui-dialog-titlebar-close').hide();
	popup.append(mensaje); // //lanza el popUp
}

function comprobar() {
	try {
		var documento = $("#cod_tercerID").val();
		var standa = $("#standaID").val();
		var parametros = "Option=verificar&Ajax=on&documento=" + documento;
		console.log(parametros);
		$.ajax({
			url: "../" + standa + "/conduc/ajax_conduc_conduc.php",
			type: "POST",
			data: parametros,
			async: false,
			dataType: "json",
			success: function(data) {
				if (data != 1) {
					$('#cod_tipdocID > option[value="' + data.principal.cod_tipdoc + '"]').attr('selected', 'selected');
					$("#cod_tercerID").attr("readonly", true);
					$("#nom_tercerID").val(data.principal.nom_tercer);
					$("#nom_apell1").val(data.principal.nom_apell1);
					$("#nom_apell2").val(data.principal.nom_apell2);
					$('#cod_grupsaID > option[value="' + data.principal.cod_grupsa + '"]').attr('selected', 'selected');
					$('#cod_tipsexID > option[value="' + data.principal.cod_tipsex + '"]').attr('selected', 'selected');
					$("#dir_domici").val(data.principal.dir_domici);
					$("#dir_domici").val(data.principal.dir_domici);
					$("#ciudadID").val(data.principal.abr_ciudad);
					$("#num_telef1").val(data.principal.num_telef1);
					$("#num_telef2").val(data.principal.num_telef2);
					$("#num_telmov").val(data.principal.num_telmov);
					$('#cod_operadID > option[value="' + data.principal.cod_operad + '"]').attr('selected', 'selected');
					$('#cod_califiID > option[value="' + data.principal.cod_califi + '"]').attr('selected', 'selected');
					$("#num_licencID").val(data.principal.num_licenc);
					$('#num_catlicID > option[value="' + data.principal.num_catlic + '"]').attr('selected', 'selected');
					$("#fec_venlicID").val(data.principal.fec_venlic);
					$("#nom_epsxxx").val(data.principal.nom_epsxxx);
					$("#nom_arpxxx").val(data.principal.nom_arpxxx);
					$("#nom_pensio").val(data.principal.nom_pensio);
					$("#nom_refper").val(data.principal.nom_refper);
					$("#tel_refper").val(data.principal.tel_refper);
					$("#cod_ciudadID").val(data.principal.cod_ciudad);
					if (data.cod_propie == 1) {
						$("#cod_propie").attr("checked", true);
					}
					if (data.cod_tenedo == 1) {
						$("#cod_tenedo").attr("checked", true);
					}
					var x = 0;
					$.each(data.referencias, function(key, value) {
						x++;
						if (key != 0) {
							agregarExperiencia();
						}
						$("#empresa" + key).val(value.nom_empre);
						$("#telefono" + key).val(value.tel_empre);
						$("#viajes" + key).val(value.num_viajes);
						$("#antiguedad" + key).val(value.num_atigue);
						$("#mercancia" + key).val(value.nom_mercan);
					});
					$("#controlID").val(x);

					/*var src = "../"+standa+"/imagenes/bad.png";
						var mensaje = "<div style='text-align:center; background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
	                  			mensaje +=	"<div style='background-color:#3A8104; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px;'>";
	                  				mensaje +=	"<label>Conductores</label>";
	                  					mensaje += "<div style='width:97%'>";
		                  					mensaje += "<div style='background-color:#FFFFFF'><font color='#000000'>El número de documento: <b>"+documento+"</b> <br> Ya esta asociado a otro conductor, verifique e intente nuevamente.<br><img src='"+src+"'>";
		        		mensaje += "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br></div></div></div></div></div>";	          
	           			LoadPopupJQNoButton( 'open', 'Error', 'auto', 'auto', false, false, true );
	           			var popup = $("#popID");
						popup.parent().children().children('.ui-dialog-titlebar-close').hide();
						popup.append(mensaje);// //lanza el popU*/
				}
			}
		});
	} catch (e) {
		console.log("Error Fuction comprobar: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function traerTipDocumento(cod_paisxx){
	var standa = $("#standaID").val();
	var parametros = "Ajax=on&standa=" + standa + "&cod_paisxx=" + cod_paisxx;
	$.ajax({
		url: "../" + standa + "/transp/ajax_transp_transp.php?",
		type: "POST",
		data: parametros + "&Option=buscaTipDocumentosPersona",
		async: false,
		success: function(data) {
			$("#cod_tipdocID").empty();
			var obj  = jQuery.parseJSON( data );
			for(var i = 0; i < obj.length; i++){
				$("#cod_tipdocID").append('<option value="'+obj[i]['value']+'">'+obj[i]['label']+'</option>');
			}			
		}
	});

	$.ajax({
		url: "../" + standa + "/transp/ajax_transp_transp.php?",
		type: "POST",
		data: parametros + "&Option=darValidacionesporPais",
		async: false,
		success: function(data) {
			var obj  = jQuery.parseJSON( data );
			$("#cod_tercerID").removeAttr('validate');
			$("#cod_tercerID").attr('validate', obj['validation']['validate']);
			$('#cod_tercerID').get(0).type = obj['validation']['type'];		
		}
	});

	$("#ciudadID").autocomplete({
		source: "../" + standa + "/transp/ajax_transp_transp.php?" + parametros + "&Option=getCiudades",
		minLength: 3,
		select: function(event, ui) {
			$("#cod_ciudadID").val(ui.item.id);
		}
	});

	

}

function limpiarInput(elemento){
	$(elemento).val('');
	$('#cod_paisxxID').val('');
}