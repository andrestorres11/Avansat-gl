/*! \file: par_califi_califi.js
 *  \brief: JS para calificaciones de Despachos y Usuarios
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 09/02/2016
 *  \bug: 
 *  \warning: 
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

function califiDespac(cod_activi, cod_itemsx) {
	try {
		//Inicio tratamiento de datos
		var radio;
		var stop = false;
		var cod = [];
		var val = [];
		var por = [];
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
		var x=0;
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

function tableCalifiUsuari() {
	try {
		var val = validaciones();
		var ban = true;
		var usu = getUsuariSelect();

		if (usu.length < 1) {
			inc_alerta("cod_consecID", "Debe selecionar por lo menos una opción");
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