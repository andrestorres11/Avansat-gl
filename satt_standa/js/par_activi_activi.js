$("body").ready(function() {
	//Acordion
	$(".accordion").accordion({
		heightStyle: "content",
		collapsible: true
	});
});

function registActivi() {
	try {
		var val = validaciones();

		if (val) {
			if (confirm("Esta Seguro que Desea Registrar la Actividad?"))
				$("#form_RegistActiviID").submit();
		}
	} catch (e) {
		console.log("Error Function registActivi: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function editActivi(ind_estado, obj) {
	try {
		if (obj.attr('name').slice(0, 10) != 'cod_activi') {
			console.log('No se encontro el Hidden cod_activi');
			return false;
		} else {
			var txt = 'Esta Seguro que Desea ';
			if (obj.val() == '1')
				txt += 'Activar la Actividad?';
			else
				txt += 'Desactivar la Actividad?';

			confirmGL(txt, "editActivi2(\'" + ind_estado + "\', \'" + obj.val() + "\');");
		}
	} catch (e) {
		console.log("Error Function editActivi: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function editActivi2(ind_estado, cod_activi) {
	try {
		var standar = $("#standaID").val();
		var attributes = 'Ajax=on&Option=editActivi';
		attributes += '&ind_estado=' + ind_estado;
		attributes += '&cod_activi=' + cod_activi;

		$.ajax({
			url: "../" + standar + "/config/par_activi_activi.php",
			type: "POST",
			data: attributes,
			async: false,
			complete: function() {
				$("#form_RegistActiviID").submit();
			}
		});
	} catch (e) {
		console.log("Error Function editActivi2: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function formEditItem(obj) {
	try {
		if (obj.attr('name').slice(0, 10) != 'cod_activi') {
			console.log('No se encontro el Hidden cod_activi');
			return false;
		} else {
			LoadPopupJQNoButton('open', 'Editar Items de la Actividad', ($(window).height() - 50), '550px', false, false, true, 'popupItemsID');
			ajaxEditItem(obj.val());
		}
	} catch (e) {
		console.log("Error Function formEditItem: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function registItem(cod_activi) {
	try {
		var standar = $("#standaID").val();
		var attributes = 'Ajax=on&Option=registItem';
		attributes += '&cod_activi=' + cod_activi;
		attributes += '&nom_itemsx=' + $("#nom_itemsxID").val();

		LoadPopupJQNoButton('open', 'Respuesta', 'auto', 'auto', false, false, true, 'popupItemOkID');
		var popup = $("#popupItemOkID");

		$.ajax({
			url: "../" + standar + "/config/par_activi_activi.php",
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
		console.log("Error Function registItem: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function editItem(ind_estado, cod_itemsx, cod_activi) {
	try {
		var standar = $("#standaID").val();
		var attributes = 'Ajax=on&Option=editItem';
		attributes += '&ind_estado=' + ind_estado;
		attributes += '&cod_itemsx=' + cod_itemsx;

		$.ajax({
			url: "../" + standar + "/config/par_activi_activi.php",
			type: "POST",
			data: attributes,
			async: true,
			success: function(datos) {
				//console.log(datos);
			},
			complete: function() {
				ajaxEditItem(cod_activi);
			}
		});
	} catch (e) {
		console.log("Error Function editItem: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function ajaxEditItem(cod_activi) {
	try {
		var popup = $("#popupItemsID");

		var standar = $("#standaID").val();
		var attributes = 'Ajax=on&Option=formEditItem';
		attributes += '&cod_activi=' + cod_activi;

		$.ajax({
			url: "../" + standar + "/config/par_activi_activi.php",
			type: "POST",
			data: attributes,
			async: false,
			beforeSend: function() {
				popup.parent().children().children('.ui-dialog-titlebar-close').hide();
				popup.html("<center><img src=\"../" + standar + "/imagenes/ajax-loader.gif\"></center>");
			},
			success: function(datos) {
				popup.html(datos);
			}
		});
	} catch (e) {
		console.log("Error Function ajaxEditItem: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function verifyItem(cod_activi) {
	try {
		if ($("#nom_itemsxID").val() == '') {
			inc_alerta('nom_itemsxID', 'Este Campo es Obligatorio.');
			return false;
		} else {
			confirmGL('Esta Seguro que Desea Registrar el Item?', 'registItem( ' + cod_activi + ' )');
		}
	} catch (e) {
		console.log("Error Function verifyItem: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function calPorcentaje() {
	try {
		var porcen = 0;
		var label = $("#totPorcenID");

		$("#popupItemsID input[id^='val_porcenID']").each(function(i, obj) {
			porcen += parseInt($(this).val());
		});

		if (porcen > 100)
			label.html('<font style="color:red"><b>' + porcen + ' %</b></font>');
		else if (porcen < 100)
			label.html('<font style="color:#E0BB18"><b>' + porcen + ' %</b></font>');
		else
			label.html('<b>' + porcen + " %</b>");
	} catch (e) {
		console.log("Error Function calPorcentaje: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function updateItems(cod_activi) {
	try {
		//$('.inc_alerta').remove();
		inc_remover_alertas();

		setTimeout(function(){
			var porcen = 0;
			var tot = 0;
			var attributes = 'Ajax=on&Option=updateItems';
			var i = 0;
			var bandera = true;

			$("#popupItemsID input[id^='val_porcenID']").each(function(index, obj) {
				porcen = parseInt($(this).val());
				tot += porcen;
				attributes += '&cod_itemsx[' + i + ']=' + $(this).attr('name');
				attributes += '&val_porcen[' + i + ']=' + $(this).val();
				i++;

				if (porcen == 0) {
					inc_alerta($(this).attr('id'), 'El Porcentaje debe ser Mayor a 0');

					bandera = false;
				}
			});

			if (tot != 100) {
				inc_alerta('totPorcenID', 'El Porcentaje Total debe ser Igual al 100%');
				bandera = false;
			}

			if (bandera == false) {
				return false;
			} else {
				var standar = $("#standaID").val();

				$.ajax({
					url: "../" + standar + "/config/par_activi_activi.php",
					type: "POST",
					data: attributes,
					async: true,
					success: function(datos) {
						//console.log(datos);
					},
					complete: function() {
						closePopUp('popupItemsID');
					}
				});
			}
		}, 510);

	} catch (e) {
		console.log("Error Function updateItems: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}