	var natural, juridico;


	$(document).ready(function() {

		natural = $("#natural").html();
		juridico = $("#juridico").html();

		$("#botones").css("display", "none");
		$("#pintar").css("display", "none");
		$("#juridico").css("display", "none");
		$("#natural").css("display", "none");
		$("#datos").css("display", "none");

		if ($("#cod_tipdoc").val() == "N") {
			$("#natural").remove();
			$("#juridico").remove();
			$("#pintar").html(juridico);
			$("#pintar").fadeIn(2000);
			$("#botones").fadeIn(2000);
		} else if ($("#cod_tipdoc").val() == "E" || $("#cod_tipdoc").val() == "C") {
			$("#natural").remove();
			$("#juridico").remove();
			$("#pintar").html(natural);
			$("#pintar").fadeIn(2000);
			$("#botones").fadeIn(2000);
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
				$("#cod_tercerID").val(ui.item.id);
				$("#boton").empty();
				$("#boton").append(boton);
				$("body").removeAttr("class");
			}
		});
		// para buscar la ciudad del tercero
		var paisxx = $("#cod_paisxxID").val();
		$("#ciudadID").autocomplete({
			source: "../" + standa + "/transp/ajax_transp_transp.php?Option=getCiudades" + attributes + "&cod_paisxx=" + paisxx,
			minLength: 3,
			select: function(event, ui) {
				$("#cod_ciudadID").val(ui.item.id);
			}
		});
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



	//funcion para mostrar la lista de los terceros de una transportadora
	function mostrar() {
		$("#form3").empty();
		var transp = $("#cod_tercerID").val();
		var standa = $("#standaID").val();
		var parametros = "Option=listaTerceros&Ajax=on&cod_transp=" + transp;
		$.ajax({
			url: "../" + standa + "/recurs/ajax_tercer_tercer.php",
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
	 *		  recibe un string con la operaci�n a realizar registrar o modificar  *
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
			//crea el popUp para el mensaje de  respuesta del guardado  
			LoadPopupJQNoButton('open', 'Resultado de la Operaci\u00F3n', 'auto', 'auto', false, false, true);
			var popup = $("#popID");

			var parametros = "Option=" + operacion + "&Ajax=on&";
			parametros += getDataForm(); //agrega los datos consignados en el formulario
			$.ajax({
				url: "../" + standa + "/recurs/ajax_tercer_tercer.php",
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
	function confirmar(operacion) {

		LoadPopupJQNoButton('open', 'Confirmar Operaci\u00F3n', 'auto', 'auto', false, false, true);
		var popup = $("#popID");
		var tercero = $("#nom_tercerID").val();
		var onclick = "onclick='registrar(\"";
		onclick += operacion;
		onclick += "\")'";
		var msj = "<div style='text-align:center'¿Est\u00E1 seguro de <b>" + operacion + "</b> el Tercero: <b>" + tercero + "?</b><br><br><br><br>";
		msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' " + onclick + " class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/> &nbsp;&nbsp;&nbsp;&nbsp";
		msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><div>";

		popup.parent().children().children('.ui-dialog-titlebar-close').hide();

		popup.append(msj); // //lanza el popUp
	}

	//funcion para las opciones del dinamicList
	function editarTercero(tipo, objeto) {
		var DLRow = $(objeto).parent().parent();
		var cod_transp = DLRow.find("input[id^=cod_agenci]").val();
		var nom_conduc = DLRow.find("input[id^=abr_tercer]").val();
		$("#cod_agenciID").val(cod_transp);
		$("#nom_tercerID").val(nom_conduc);

		if (tipo == 1) {
			confirmar('activar');
		} else if (tipo == 2) {
			confirmar('inactivar');
		} else {
			LoadPopupJQNoButton('open', 'Confirmar Operaci\u00F3n', 'auto', 'auto', false, false, true);
			var popup = $("#popID");
			var conductor = $("#nom_tercerID").val();
			var msj = "<div style='text-align:center'>¿Est\u00E1 seguro de <b>editar</b> el tercero: <b>" + conductor + "?</b><br><br><br><br>";
			msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='formulario()' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
			msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";

			popup.parent().children().children('.ui-dialog-titlebar-close').hide();
			popup.append(msj); // //lanza el popUp
		}
	}


	function verificar() {
		try {
			var codigo = $("#cod_tipterID").val();
			$("#pintar").fadeOut(1200);
			$("#botones").fadeOut(1200);
			$("#natural").remove();
			$("#juridico").remove();
			if (codigo == 1) {
				$("#pintar").html(juridico);
				$("#pintar").fadeIn(1200);
				$("#botones").fadeIn(1200);
			} else {
				$("#pintar").html(natural);
				$("#pintar").fadeIn(1200);
				$("#botones").fadeIn(1200);
			}
			var standa = $("#standaID").val();
			var attributes = '&Ajax=on&standa=' + standa;
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
			var paisxx = $("#cod_paisxxID").val();
			$("#ciudadID").autocomplete({
				source: "../" + standa + "/transp/ajax_transp_transp.php?Option=getCiudades" + attributes + "&cod_paisxx=" + paisxx,
				minLength: 3,
				select: function(event, ui) {
					$("#cod_ciudadID").val(ui.item.id);
				}
			});

		} catch (e) {
			console.log("Error Fuction verificar: " + e.message + "\nLine: " + e.lineNumber);
			return false;
		}
	}

	function comprobar() {
		try {
			var tercero = $("#cod_tercerID").val();
			var standa = $("#standaID").val();
			var parametros = "Option=verificar&Ajax=on&tercero=" + tercero;
			$.ajax({
				url: "../" + standa + "/recurs/ajax_tercer_tercer.php",
				type: "POST",
				data: parametros,
				async: false,
				dataType: "json",
				success: function(data) {
					console.log(data);
					var tipo = $("#cod_tipterID").val();
					if (data.principal.cod_tipdoc === "N") { //si es persona juridica
						if (tipo != 1) {
							$('#cod_tipterID > option[value="1"]').attr('selected', 'selected');
							verificar();
						}
						setTimeout(function() {
							$("#cod_tercerID").val(tercero);
							$("#num_verifiID").val(data.principal.num_verifi);
							$("#nom_tercerID").val(data.principal.nom_tercer);
							$("#abr_tercerID").val(data.principal.abr_tercer);
							$('#cod_terregID > option[value="' + data.principal.cod_terreg + '"]').attr('selected', 'selected');
							$("#ciudadID").val(data.principal.abr_ciudad);
							$("#cod_ciudadID").val(data.principal.cod_ciudad);
							$("#dir_domiciID").val(data.principal.dir_domici);
							$("#num_telef1ID").val(data.principal.num_telef1);
							$("#num_telef2ID").val(data.principal.num_telef2);
							$("#num_telmovID").val(data.principal.num_telmov);
							$("#num_faxxxID").val(data.principal.num_faxxxx);
							$("#dir_urlwebID").val(data.principal.dir_urlweb);
							$("#dir_emailxID").val(data.principal.dir_emailx);
							$("#obs_tercer").val(data.principal.obs_tercer);
							$.each(data.activities, function(key, value) {
								$('input[type="checkbox"]').each(function() {
									var check = $(this).val();
									if (value == check) {
										$(this).attr("checked", "checked");
									}
								});
							});
						}, 1210);

					} else if (data.principal.cod_tipdoc === "C" || data.principal.cod_tipdoc === "E") { // si no es persona natural
						if (tipo != 2) {
							$('#cod_tipterID > option[value="2"]').attr('selected', 'selected');
							verificar();
						}
						setTimeout(function() {
							$("#cod_tercerID").val(tercero);
							$("#nom_tercerID").val(data.principal.nom_tercer);
							$("#nom_apell1ID").val(data.principal.nom_apell1);
							$("#nom_apell2ID").val(data.principal.nom_apell2);
							$("#ciudadID").val(data.principal.abr_ciudad);
							$("#cod_ciudadID").val(data.principal.cod_ciudad);
							$("#dir_domiciID").val(data.principal.dir_domici);
							$("#num_telef1ID").val(data.principal.num_telef1);
							$("#num_telef2ID").val(data.principal.num_telef2);
							$("#num_telmovID").val(data.principal.num_telmov);
							$("#num_faxxxID").val(data.principal.num_faxxxx);
							$("#dir_urlwebID").val(data.principal.dir_urlweb);
							$("#dir_emailxID").val(data.principal.dir_emailx);
							$("#obs_tercer").val(data.principal.obs_tercer);
							$.each(data.activities, function(key, value) {
								$('input[type="checkbox"]').each(function() {
									var check = $(this).val();
									if (value == check) {
										$(this).attr("checked", "checked");
									}
								});
							});
						}, 1210);
					}
				}
			});
		} catch (e) {
			console.log("Error Funcion comprobar: " + e.message + "\nLine: " + e.lineNumber);
			return false;
		}
	}
