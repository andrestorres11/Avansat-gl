/**
 * @author jose.guerrero
 */

function Listar() {
	if (document.getElementById('cod_transpID').value != 0 && (document.getElementById('cod_basculID').value != "" || document.getElementById('num_placaxID').value != "")) {
		document.getElementById('opcionID').value = 2;
		document.getElementById('formularioID').submit();
	} else {
		alert('Debe Escojer la Transportadora y Digitar un Codigo de Bascula o la Placa');
	}
}

function ImagenesDespac(num_despac, standar) {
	try {
		LoadPopup();
		var atributes = "num_despac=" + num_despac;
		AjaxGetData("../" + standar + "/protoc/list_images_despac.php?", atributes, 'result', "post", "");
	} catch (e) {
		alert("Error protocolo " + e.message);
	}
}

function updProto(i) {
	try {
		var cod_consec = document.getElementById("cod_consecID" + i);
		var nom_tiposx = document.getElementById("nom_tiposxID" + i);

		if (nom_tiposx.value == '') {
			nom_tiposx.focus();
			return alert("El Nombre del Protocolo es Obligatorio")
		}
		if (confirm("Seguro que desea Actualizar el Protocolo?")) {
			var url_archiv = document.getElementById('url_archivID');
			var dir_aplica = document.getElementById('dir_aplicaID');
			var atributes = "opcion=2&cod_consec=" + cod_consec.value + "&nom_tiposx=" + nom_tiposx.value;
			AjaxGetData("../" + dir_aplica.value + "/protoc/" + url_archiv.value + "?", atributes, 'result', "post", "valPro()");
		}
	} catch (e) {
		alert("Error updproto " + e.message);
	}
}

function actualizar(i) {
	try {
		var cod_consec = document.getElementById("cod_eventoID" + i);
		var nom_evento = document.getElementById("nom_eventoID" + i);
		var cod_tiposx = document.getElementById("cod_tiposxID" + i);
		var transp = document.getElementById("transpID");
		var acc_propue = document.getElementById("acc_propueID" + i);
		var acc_acorda = document.getElementById("acc_acordaID" + i);
		var acc_frecci = document.getElementById("acc_frecciID" + i);
		var acc_respon = document.getElementById("acc_responID" + i);

		if (nom_evento.value == '') {
			nom_evento.focus();
			return alert("El Evento es Obligatorio")
		}
		if (cod_tiposx.value == '' || cod_tiposx.value == '0') {
			cod_tiposx.focus();
			return alert("El Tipo es Obligatorio")
		}
		if (acc_propue.value == '') {
			acc_propue.focus();
			return alert("La Accion Propuesta es Obligatorio")
		}
		if (acc_acorda.value == '') {
			acc_acorda.focus();
			return alert("Accion Acordada es Obligatorio")
		}
		if (acc_frecci.value == '') {
			acc_frecci.focus();
			return alert("La Frecuencia de la Accion es Obligatorio")
		}
		if (acc_respon.value == '') {
			acc_respon.focus();
			return alert("El Responsable de la accion Acordada es Obligatorio")
		}

		if (confirm("Seguro que desea Actualizar el Protocolo?")) {
			var url_archiv = document.getElementById('url_archivID');
			var dir_aplica = document.getElementById('dir_aplicaID');
			var atributes = "opcion=3&cod_transp=" + transp.value + "&cod_tiposx=" + cod_tiposx.value;
			atributes += "&nom_evento=" + nom_evento.value + "&acc_propue=" + acc_propue.value;
			atributes += "&acc_acorda=" + acc_acorda.value + "&acc_frecci=" + acc_frecci.value;
			atributes += "&acc_respon=" + acc_respon.value + "&cod_evento=" + cod_consec.value;
			AjaxGetData("../" + dir_aplica.value + "/protoc/" + url_archiv.value + "?", atributes, 'result', "post", "valUpda()");
		}
	} catch (e) {
		alert("Error actualizar " + e.message);
	}
}

function delEvento(i) {
	try {
		var cod_consec = document.getElementById("cod_eventoID" + i);
		var nom_evento = document.getElementById("nom_eventoID" + i);
		var transp = document.getElementById("transpID");

		if (confirm("Seguro que desea Elimiar el Evento " + nom_evento.value + "?")) {
			var url_archiv = document.getElementById('url_archivID');
			var dir_aplica = document.getElementById('dir_aplicaID');
			var atributes = "opcion=4&cod_transp=" + transp.value + "&nom_evento=" + nom_evento.value;
			atributes += "&cod_evento=" + cod_consec.value;
			AjaxGetData("../" + dir_aplica.value + "/protoc/" + url_archiv.value + "?", atributes, 'result', "post", "valDel()");
		}
	} catch (e) {
		alert("Error eliminar " + e.message);
	}
}

function protocolos(transp, dir_aplica) {
	try {
		LoadPopup();
		var atributes = "opcion=5&cod_transp=" + transp;
		AjaxGetData("../" + dir_aplica + "/protoc/ins_evento_protoc.php?", atributes, 'result', "post", "");
	} catch (e) {
		alert("Error protocolo " + e.message);
	}
}

function nuevo() {
	try {
		var nom_evento = document.getElementById("nom_eventoID");
		var cod_tiposx = document.getElementById("cod_tiposxID");
		var transp = document.getElementById("transpID");
		var acc_propue = document.getElementById("acc_propueID");
		var acc_acorda = document.getElementById("acc_acordaID");
		var acc_frecci = document.getElementById("acc_frecciID");
		var acc_respon = document.getElementById("acc_responID");

		if (nom_evento.value == '') {
			nom_evento.focus();
			return alert("El Evento es Obligatorio")
		}
		if (cod_tiposx.value == '' || cod_tiposx.value == '0') {
			cod_tiposx.focus();
			return alert("El Tipo es Obligatorio")
		}
		if (acc_propue.value == '') {
			acc_propue.focus();
			return alert("La Accion Propuesta es Obligatorio")
		}
		if (acc_acorda.value == '') {
			acc_acorda.focus();
			return alert("Accion Acordada es Obligatorio")
		}
		if (acc_frecci.value == '') {
			acc_frecci.focus();
			return alert("La Frecuencia de la Accion es Obligatorio")
		}
		if (acc_respon.value == '') {
			acc_respon.focus();
			return alert("El Responsable de la accion Acordada es Obligatorio")
		}

		if (confirm("Seguro que desea Actualizar el Protocolo?")) {
			var url_archiv = document.getElementById('url_archivID');
			var dir_aplica = document.getElementById('dir_aplicaID');
			var atributes = "opcion=2&cod_transp=" + transp.value + "&cod_tiposx=" + cod_tiposx.value;
			atributes += "&nom_evento=" + nom_evento.value + "&acc_propue=" + acc_propue.value;
			atributes += "&acc_acorda=" + acc_acorda.value + "&acc_frecci=" + acc_frecci.value;
			atributes += "&acc_respon=" + acc_respon.value;
			AjaxGetData("../" + dir_aplica.value + "/protoc/" + url_archiv.value + "?", atributes, 'result', "post", "valNuevo()");
		}
	} catch (e) {
		alert("Error nuevo " + e.message);
	}
}

function delProto(i) {
	try {
		var cod_consec = document.getElementById("cod_consecID" + i);
		if (confirm("Seguro que desea Eliminar el Protocolo?")) {
			var url_archiv = document.getElementById('url_archivID');
			var dir_aplica = document.getElementById('dir_aplicaID');
			var atributes = "opcion=4&cod_consec=" + cod_consec.value;
			AjaxGetData("../" + dir_aplica.value + "/protoc/" + url_archiv.value + "?", atributes, 'result', "post", "delPro()");
		}
	} catch (e) {
		alert("Error delproto " + e.message);
	}
}

function delPro() {
	try {
		var tipo = document.getElementById("tipoID");

		if (tipo.value = '')
			return alert('Error al Eliminar el Protocolo');
		else
			alert('Se Elimino el Protocolo con Exito');

		document.getElementById('opcionID').value = 1;
		return document.getElementById('formularioID').submit();
	} catch (e) {
		alert("Error al Eliminar el Protocolo.");
	}
}

function valPro() {
	try {
		var cod_tipoID = document.getElementById("cod_tipoID");

		if (cod_tipoID.value = '')
			return alert('Error al Actualizar el Protocolo ');
		else
			return alert('Se Actualizo el Protocolo con Exito');
	} catch (e) {
		alert("Error valPro " + e.message);
	}
}

function solucionNoEfectiva(index, cod_transp, cod_noveda, num_consec) {
	loadPopupNew("open", cod_transp, cod_noveda, num_despac, num_consec);
	var dir_aplica = document.getElementById('dir_aplicaID');
	var num_despac = $("#num_despacID").val();
	var parame = "&cod_noveda=" + cod_noveda + "&num_despac=" + num_despac + "&num_consec=" + num_consec;

	$.ajax({
		url: '../' + dir_aplica.value + '/despac/ajax_despachos.php?option=paintSoluci' + parame,
		type: 'POST',
		async: false,
		success: function(data) {
			$("#popUpLoaded").html(data);
		}
	})
}

function solucionEfectiva(index, cod_transp, cod_noveda, num_consec) {
	var dir_aplica = document.getElementById('dir_aplicaID');
	var num_despac = $("#num_despacID").val();
	var parame = "&cod_noveda=" + cod_noveda + "&num_despac=" + num_despac + "&num_consec=" + num_consec;

	$.ajax({
		url: '../' + dir_aplica.value + '/despac/ajax_despachos.php?option=solucionaEfectiva' + parame,
		type: 'POST',
		async: false,
		success: function(data) {
			$("#popUpLoaded").html(data);
			location.reload();
		}
	})
}

function loadPopupNew(event, cod_transp, cod_noveda, num_despac, num_consec) {
	var dir_aplica = document.getElementById('dir_aplicaID');
	var cod_servic = document.getElementById('cod_servicID');
	var opcion = document.getElementById('opcionID');

	if (event == "open") {
		$('<div id="popUpLoaded"></div>').dialog({
			modal: true,
			resizable: false,
			draggable: false,
			title: "Detalles",
			width: 800,
			height: 450,
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
				Aceptar: function() {
					var parame = "";
					var num_despac = $("#num_despacID").val();
					var observ = $("#des_novPop").val();
					parame += "&num_despac=" + num_despac + "&cod_transp=" + cod_transp + "&cod_servic=" + cod_servic.value;
					parame += "&opcion=" + opcion.value + "&obs_noveda=" + observ + "&ind_protoc=yes" + "&cod_noveda=" + cod_noveda;
					parame += "&num_consec=" + num_consec + "&tot_protoc_=" + 1;
					$.ajax({
						url: '../' + dir_aplica.value + '/despac/ajax_despachos.php?option=setNovedaEfecti',
						type: 'POST',
						async: false,
						data: parame,
						success: function(data) {

							alert("se ha registrado la novedad exitosamente en el sistema");
							location.reload();
						}
					})

					$(this).dialog('destroy').remove();
				},
				Cancelar: function() {
					loadPopupNew("close");
				}
			}

		});
	} else {
		$("#popUpLoaded").dialog('destroy').remove();
	}
}

function validarTipo() {
	try {
		var nom = document.getElementById('nom_tiposxID');
		if (nom.value == '') {
			return alert("El Nombre del Tipo es Obligatoria");
		}
		if (confirm("Seguro que desea Insertar el Tipo de Protocolo?")) {
			document.getElementById('opcionID').value = 3;
			document.getElementById('formularioID').submit();
		}
	} catch (e) {
		alert("Error validarTipo " + e.message);
	}
}

function LoadPopup() {
	try {
		var objEnd = document.getElementById("AplicationEndDIV");
		//LockAplication("lock");
		var objPopup = document.getElementById("popupDIV");
		var width = screen.height;
		var height = screen.width;
		var width = Math.round(screen.width / 1.5);
		var height = Math.round(screen.height / 1.5);
		var left = Math.round(screen.width / 10);
		var top = Math.round(screen.height / 15);

		objPopup.style.width = String(width) + "px";
		objPopup.style.height = String(height) + "px";
		objPopup.style.left = String(left) + "px";
		objPopup.style.top = String(top) + "px";
		objPopup.style.visibility = "visible";
		objPopup.scrollIntoView(true);
	} catch (e) {
		alert("Error " + e.message);
	}
}

function ClosePopup() {
	//LockAplication( "unlock" );
	var objPopup = document.getElementById("popupDIV");
	objPopup.style.width = "0px";
	objPopup.style.height = "0px";
	objPopup.style.left = "0px";
	objPopup.style.top = "0px";
	//objPopup.innerHTML = "";
	objPopup.style.visibility = "hidden";
}

function valNuevo() {
	try {
		if (document.getElementById('evenID').value == 1) {
			alert("Evento Reguistrado Con Exito");
			document.getElementById('opcionID').value = 1;
			document.getElementById('formularioID').submit();
		} else {
			alert("Error al Insertar Evento");
		}
	} catch (e) {
		alert("Error al Insertar Evento");
	}
}

function valDel() {
	try {
		if (document.getElementById('evenID').value == 1) {
			alert("Evento Eliminado Con Exito");
			document.getElementById('opcionID').value = 1;
			document.getElementById('formularioID').submit();
		} else {
			alert("Error al Eliminar Evento");
		}
	} catch (e) {
		alert("Error al Eliminar Evento");
	}
}

function valUpda() {
	try {
		if (document.getElementById('evenID').value == 1) {
			alert("Evento Registrado Con Exito");
		} else {
			alert("Error al Actualizar Evento");
		}
	} catch (e) {
		alert("Error al Actualizar Evento");
	}
}


//----------------------------------------------------------------------------------------------------
$("#AplicationEndDIV").ready(function() {
	//----------------------------------------------------
	$("[id^=gridContain]").each(function(i, o) {

		var obj = $(this).parent().parent().parent().parent().clone();
		var objData = $("#nom_destin_y" + i).parent().parent().clone();
		var objDatax = $("#nom_destin_x" + i).parent().parent().clone();

		html = "<table style='width:98%'>" + objDatax.html() + "</table>" + "<table style='width:98%'>" + objData.html() + "</table>" + "<table style='width:98%'>" + obj.html() + "</table>";

		$("#caja" + i).html(html);

		$(this).parent().parent().parent().parent().remove();
		$("#nom_destin_y" + i).parent().parent().hide();
		$("#nom_destin_x" + i).parent().parent().hide();
	});

	$("[id^=accordeon]").each(function() {
		$(this).accordion({
			collapsible: true,
			active: false
		});
	})
});

$(document).ready(function() {
	$('input:radio[name=ind_cumpli]').click(function() {
		var ind_cumpli = $(this).val();
		$("#frm_cumpli").animate({
			opacity: 1
		}, 1500);
		GetNovedades(ind_cumpli, null);
	});

	$('input:radio[name^=ind_cliente_cumdes]').click(function() {
		var index2 = parseInt($(this).attr('id').replace(/[^\d]/g, '').replace(/^\s+|\s+$/g, ""));
		var ind_cumpli = $(this).val();
		$("#frm_cliente_cumdes" + index2).animate({
			opacity: '+=1'
		}, 1500);
		GetNovedadesCliente(ind_cumpli, index2);
	});
	var NumCheckDestin = $("input[name^=ind_cumdes]").length;
	var NumCheckClient = $("input[name^=ind_cliente_cumdes]").length;
	var TotalRadio = NumCheckDestin + NumCheckClient;
	var NumCheckCarga = $("input[name^=ind_cumpli]").length;
});

function GetNovedades(ind_cumpli, index) {
	var dir_aplica = document.getElementById('dir_aplicaID');

	$.ajax({
		url: '../' + dir_aplica.value + '/despac/ajax_despachos.php?option=GetNovedades',
		type: 'POST',
		async: false,
		data: {
			ind_cumpli: ind_cumpli,
			cita: (index == null ? 'C' : 'D')
		},
		success: function(mdata) {
			res_error = $.parseJSON(mdata);
			if (index == null)
				var mySelect = document.getElementById("nov_cumpli");
			else
				var mySelect = document.getElementById("nov_cumdes" + index);

			mySelect.options.length = 0;
			mySelect.options[0] = new Option("--", "");
			if (res_error.length > 0) {
				for (var i = 0; i < res_error.length; i++) {
					mySelect.options[i + 1] = new Option(res_error[i].label, res_error[i].value);
				}
			}
		}
	});
}

function GetNovedadesCliente(ind_cumpli, index) {
	var dir_aplica = document.getElementById('dir_aplicaID');

	$.ajax({
		url: '../' + dir_aplica.value + '/despac/ajax_despachos.php?option=GetNovedadesCliente',
		type: 'POST',
		async: false,
		data: {
			ind_cumpli: ind_cumpli,
			cita: (index == null ? 'C' : 'D')
		},
		success: function(mdata) {
			res_error = $.parseJSON(mdata);
			if (index == null)
				var mySelect = document.getElementById("nov_cumdes_cliente");
			else
				var mySelect = document.getElementById("nov_cumdes_cliente" + index);

			mySelect.options.length = 0;
			mySelect.options[0] = new Option("--", "");
			if (res_error.length > 0) {
				for (var i = 0; i < res_error.length; i++) {
					mySelect.options[i + 1] = new Option(res_error[i].label, res_error[i].value);
				}
			}
		}
	});
}

function GetNovedadesDescargue(ind_cumpli, index) {
	var dir_aplica = document.getElementById('dir_aplicaID');

	$.ajax({
		url: '../' + dir_aplica.value + '/despac/ajax_despachos.php?option=GetNovedadesDescargue',
		type: 'POST',
		async: false,
		data: {
			ind_cumpli: ind_cumpli,
			cita: (index == null ? 'C' : 'D')
		},
		success: function(mdata) {
			res_error = $.parseJSON(mdata);

			var mySelect = document.getElementById("nov_cumdes" + index);

			mySelect.options.length = 0;
			mySelect.options[0] = new Option("--", "");
			if (res_error.length > 0) {
				for (var i = 0; i < res_error.length; i++) {
					mySelect.options[i + 1] = new Option(res_error[i].label, res_error[i].value);
				}
			}
		}
	});
}

function SaveCumpli() {
	try {
		var message = new Array();
		var ind_timext = $('input:radio[name=ind_timext]:checked').val();

		if ($('#fec_cumpli').val().replace(/^\s+|\s+$/g, "") == '')
			message.push("- Fecha de Noveda");
		if ($('#hor_cumpli').val().replace(/^\s+|\s+$/g, "") == '')
			message.push("- Hora de Noveda");
		if ($('#nov_cumpli').val().replace(/^\s+|\s+$/g, "") == '')
			message.push("- Tipo de Noveda");
		if ($('input:radio[name=ind_cumpli]:checked').val() == 0 && $('#obs_cumpli').val().replace(/^\s+|\s+$/g, "") == '')
			message.push("- Observacion Noveda");

		if (ind_timext == '1') {
			var fec_extrax = $('#fec_extraxID').val();
			var hor_extrax = $('#hor_extraxID').val();

			if (fec_extrax.replace(/^\s+|\s+$/g, "") == '')
				message.push("- Fecha Extra");
			if (hor_extrax.replace(/^\s+|\s+$/g, "") == '')
				message.push("- Hora Extra");
		}

		if (message.length > 0) {
			var label = message.length == 1 ? 'el Siguiente Campo' : 'los Siguientes Campos';
			alert("Por Favor Completar " + label + "\n\n" + message.join("\n"));
			return false;
		}

		var fec1 = $("#dateActualID").val();
		var fec2 = $("#fec_extraxID").val() + " " + $("#hor_extraxID").val();
		if (fec1 > fec2) {
			alert("El tiempo extra no es valido.");
			return false;
		}

		var ind_timext = $('input:radio[name=ind_timext]:checked').val();
		var dir_aplica = document.getElementById('dir_aplicaID');
		var atributes = '&ind_cumpli=' + $('input:radio[name=ind_cumpli]:checked').val();
		atributes += '&fec_cumpli=' + $('#fec_cumpli').val();
		atributes += '&hor_cumpli=' + $('#hor_cumpli').val();
		atributes += '&nov_cumpli=' + $('#nov_cumpli').val();
		atributes += '&obs_cumpli=' + $('#obs_cumpli').val();
		atributes += '&num_despac=' + $('#num_despacID').val();
		atributes += '&opcion=' + $('#opcionID').val();
		atributes += '&cod_servic=' + $('#cod_servicID').val();
		atributes += '&dir_aplica=' + $('#dir_aplicaID').val();
		atributes += '&ind_timext=' + ind_timext;

		if (ind_timext == '1') {
			var fec_extrax = $('#fec_extraxID').val();
			var hor_extrax = $('#hor_extraxID').val();
			atributes += '&fec_extrax=' + fec_extrax;
			atributes += '&hor_extrax=' + hor_extrax;
		}

		if (!confirm('Realmente desea Registrar la Novedad de ' + ($('input:radio[name=ind_cumpli]:checked').val() == 1 ? 'Cumplimiento' : 'Incumplimiento') + ' - Cita de Cargue ...?'))
			return false;

		$.ajax({
			url: '../' + dir_aplica.value + '/despac/ajax_despachos.php?option=SetCitaCargue' + atributes,
			type: 'POST',
			async: true,
			beforeSend: function(obj) {
				$.blockUI({
					theme: true,
					title: 'Registro de Novedad',
					draggable: false,
					message: '<center><img src="../' + dir_aplica.value + '/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
				});
			},
			success: function(mdata) {
				res_error = $.parseJSON(mdata);
				alert(res_error.msg_respon);
				if (res_error.cod_respon == '200') {
					$('form').submit();
				}
			},
			complete: function() {
				$.unblockUI();
			}
		});
	} catch (e) {
		console.log("Error Function SaveCumpli: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function validateCumpliSeguimCargue2() {
	try {
		var num_despac = $("#num_despacID").val();
		var standa = $("#dir_aplicaID").val();
		var attributes = 'Ajax=on&Option=validateCumpliSeguimCargue&standa=' + standa;
		attributes += '&num_despac=' + num_despac;

		$.ajax({
			url: "../" + standa + "/inform/inf_cumpli_segcar.php",
			type: "POST",
			data: attributes,
			async: false,
			success: function(datos) {
				if (datos != '1') {
					confirmCumpliSeguimCargue(standa, num_despac, 0);
				}
			}
		});
	} catch (e) {
		console.log("Error Function validateCumpliSeguimCargue2: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function SaveFechaAdicio(index) {
	var dir_aplica = document.getElementById("dir_aplicaID");
	var tip_fechax = $("#tip_comple" + index);
	var fec_comple = $("#fec_comple" + index);
	var hor_comple = $("#hor_comple" + index);

	var atributes = '';
	atributes += "&num_despac=" + $("#num_despacID").val();
	atributes += "&opcion=" + $("#opcionID").val();
	atributes += "&cod_servic=" + $("#cod_servicID").val();
	atributes += "&dir_aplica=" + $("#dir_aplicaID").val();
	atributes += "&num_docume=" + $("#num_docume" + index).val();

	if (tip_fechax.val() == '') {
		alert("Seleccione el campo que Desea Registrar");
		return false;
	} else if (fec_comple.val() == '') {
		alert("Seleccione la Fecha que Desea Registrar");
		return false;
	} else if (hor_comple.val() == '') {
		alert("Seleccione la hora que Desea Registrar");
		return false;
	} else {
		$.ajax({
			url: '../' + dir_aplica.value + '/despac/ajax_despachos.php?option=SaveFechaAdicio',
			type: 'POST',
			async: true,
			data: atributes + "&tip_fechax=" + tip_fechax.val() + "&fec_comple=" + fec_comple.val() + "&hor_comple=" + hor_comple.val(),
			beforeSend: function(obj) {
				$.blockUI({
					theme: true,
					title: 'Registro de Novedad',
					draggable: false,
					message: '<center><img src="../' + dir_aplica.value + '/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
				});
			},
			success: function(mdata) {
				console.log(mdata);
				if (mdata == '200') {
					$('form').submit();
				}
			},
			complete: function() {
				$.unblockUI();
			}
		});
	}
}

function SaveCumdes(index) {
	var message = new Array();
	var dir_aplica = document.getElementById("dir_aplicaID");
	var atributes = "ind_cumdes=" + $("input:radio[name=ind_cumdes" + index + "]:checked").val();
	atributes += "&fec_cumdes=" + $("#fec_cumdes" + index).val();
	atributes += "&hor_cumdes=" + $("#hor_cumdes" + index).val();
	atributes += "&nov_cumdes=" + $("#nov_cumdes" + index).val();
	atributes += "&obs_cumdes=" + $("#obs_cumdes" + index).val();
	atributes += "&num_despac=" + $("#num_despacID").val();
	atributes += "&opcion=" + $("#opcionID").val();
	atributes += "&cod_servic=" + $("#cod_servicID").val();
	atributes += "&dir_aplica=" + $("#dir_aplicaID").val();
	atributes += "&num_docume=" + $("#num_docume" + index).val();
	atributes += "&num_docum2=" + $("#num_docum2" + index).val();
	atributes += "&nom_destin=" + $("#nom_destin" + index).val();
	atributes += "&otr_destin=" + $("#otr_destin" + index).val();
	atributes += "&ind_finali=" + $("#ind_finali" + index).val();

	if ($("#fec_cumdes" + index).val().replace(/^\s+|\s+$/g, "") == "")
		message.push("- Fecha de Noveda");
	if ($("#hor_cumdes" + index).val().replace(/^\s+|\s+$/g, "") == "")
		message.push("- Hora de Noveda");
	if ($("#nov_cumdes" + index).val().replace(/^\s+|\s+$/g, "") == "")
		message.push("- Tipo de Noveda");
	if ($("input:radio[name=ind_cumdes" + index + "]:checked").val() == 0 && $("#obs_cumdes" + index).val().replace(/^\s+|\s+$/g, "") == "")
		message.push("- Observacion Noveda");

	if (message.length > 0) {
		var label = message.length == 1 ? "el Siguiente Campo" : "los Siguientes Campos";
		alert("Por Favor Completar " + label + "\n\n" + message.join("\n"));
		return false;
	}

	if (!confirm("Realmente desea Registrar la Novedad de " + ($("input:radio[name=ind_cumdes" + index + "]:checked").val() == 1 ? "Cumplimiento" : "Incumplimiento") + " - Cita de Descargue ...?"))
		return false;

	$.ajax({
		url: '../' + dir_aplica.value + '/despac/ajax_despachos.php?option=SetCitaDescargue',
		type: 'POST',
		async: true,
		data: atributes,
		beforeSend: function(obj) {
			$.blockUI({
				theme: true,
				title: 'Registro de Novedad',
				draggable: false,
				message: '<center><img src="../' + dir_aplica.value + '/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
			});
		},
		success: function(mdata) {

			res_error = $.parseJSON(mdata);
			alert(res_error.msg_respon);
			if (res_error.cod_respon == '200') {
				$('form').submit();
			}
		},
		complete: function() {
			$.unblockUI();
		}
	});
}

function SaveCumdesCliente(index, cliente) {
	var message = new Array();
	var dir_aplica = document.getElementById("dir_aplicaID");
	var atributes = "ind_cliente_cumdes=" + $("input:radio[name=ind_cliente_cumdes" + index + "]:checked").val();
	atributes += "&fec_cumdes_cliente=" + $("#fec_cumdes_cliente" + index).val();
	atributes += "&hor_cumdes_cliente=" + $("#hor_cumdes_cliente" + index).val();
	atributes += "&nov_cumdes_cliente=" + $("#nov_cumdes_cliente" + index).val();
	atributes += "&obs_cumdes_cliente=" + $("#obs_cumdes_cliente" + index).val();
	atributes += "&cod_cliente=" + cliente;
	atributes += "&num_despac=" + $("#num_despacID").val();
	atributes += "&opcion=" + $("#opcionID").val();
	atributes += "&cod_servic=" + $("#cod_servicID").val();
	atributes += "&dir_aplica=" + $("#dir_aplicaID").val();
	if ($("#fec_cumdes_cliente" + index).val().replace(/^\s+|\s+$/g, "") == "")
		message.push("- Fecha de Noveda");
	if ($("#hor_cumdes_cliente" + index).val().replace(/^\s+|\s+$/g, "") == "")
		message.push("- Hora de Noveda");
	if ($("#nov_cumdes_cliente" + index).val().replace(/^\s+|\s+$/g, "") == "")
		message.push("- Tipo de Noveda");
	if ($("input:radio[name=ind_cliente_cumdes" + index + "]:checked").val() == 0 && $("#obs_cumdes_cliente" + index).val().replace(/^\s+|\s+$/g, "") == "")
		message.push("- Observacion Noveda");
	if ($("#obs_cumdes_cliente" + index + "").val() == '' && $("#obs_cumdes_cliente" + index).val().replace(/^\s+|\s+$/g, "") == "")
		message.push("- Observacion Noveda");
	if (message.length > 0) {
		var label = message.length == 1 ? "el Siguiente Campo" : "los Siguientes Campos";
		alert("Por Favor Completar " + label + "\n\n" + message.join("\n"));
		return false;
	}

	if (!confirm("Realmente desea Registrar la Novedad de " + ($("input:radio[name=ind_cliente_cumdes" + index + "]:checked").val() == 1 ? "Cumplimiento" : "Incumplimiento") + " - Cita de Descargue ...?"))
		return false;

	$.ajax({
		url: '../' + dir_aplica.value + '/despac/ajax_despachos.php?option=SetCitaDescargueClientes',
		type: 'POST',
		async: true,
		data: atributes,
		beforeSend: function(obj) {
			$.blockUI({
				theme: true,
				title: 'Registro de Novedad',
				draggable: false,
				message: '<center><img src="../' + dir_aplica.value + '/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
			});
		},
		success: function(mdata) {

			res_error = $.parseJSON(mdata);
			alert(res_error.msg_respon);
			if (res_error.cod_respon == '200') {
				$('form').submit();
			}
		},
		complete: function() {
			$.unblockUI();
		}
	});
}

/* FUNCIONES PARA ELIMINAR O EDITAR ---------------------------------------------------------------------------------------------------------------- */
//Despacho, Pcontrol, CodNovedad, FecRegist, CodConsec, origen, usuario, objeto
function ActionNovedad(d, p, n, f, c, t, u, o, a) {
	try {
		var Action = a == 1 ? 'update' : 'delete';
		LoadPopupJQ('open', o, Action, d, p, n, f, c, t, u);
	} catch (e) {
		alert("Error en ActionNovedad: " + e.message + "\nLine: " + e.lineNumber);
	}
}

function LoadPopupJQ(type, object, action, despac, puesto, novedad, date, consec, table, user) {
	try {
		var ObjectTr = $(object).parent().parent().parent();
		var NomNoveda = ObjectTr.find("td").first().find("b").html();

		if (type == 'open') {
			$('<div id="DialogNovedaID"><center>CARGANDO ...</center></div>').dialog({
				modal: true,
				resizable: false,
				closeOnEscape: false,
				width: "auto",
				heigth: "auto",
				title: (action == 'update' ? "Actualizar Novedad: " : "Eliminar Novedad: ") + NomNoveda,
				open: function() {
					$(".ui-dialog-titlebar-close").remove();
				},
				buttons: {
					"Cancelar": function() {
						LoadPopupJQ("close");
					},
					"Aceptar": function() {
						//Carga Ajax segun la accion
						if (action == 'delete') {
							if (confirm("Desea Eliminar El Registro De La Novedad?")) {
								DeleteNoveda(ObjectTr, despac, consec, puesto, novedad, date, table, user);
							}
						}

						if (action == 'update') {
							var NewCodNoveda = $("#cod_novedaNID").val();
							var NewNomNoveda = $("#novedadListID").val();
							var Msg = "Recuerde que selecciono una novedad diferente a la Actual:\n";
							Msg += "Novedad Actual: " + $("#actaulNovedadID").html() + "  \n";
							Msg += "Nueva Novedad:  " + $("#novedadListID").val() + " \n";
							var mExtra = NewCodNoveda != '--' ? Msg : "";
							if (confirm("Desea Actualizar El Registro De La Novedad?\n" + mExtra)) {
								UpdateNoveda(ObjectTr, despac, consec, puesto, novedad, date, table, user);
							}
						}
					}
				}
			});
		} else {
			$("#DialogNovedaID").dialog("destroy").remove();
			return false;
		}

		// Ejecuta ajax para la accion
		var central = $("#dir_aplicaID").val();
		var param = 'Option=' + (action == 'update' ? 'FormNovedaUpdate' : 'FormDeleteNoveda');
		param += '&despac=' + despac;
		param += '&puesto=' + puesto;
		param += '&novedad=' + novedad;
		param += '&date=' + date;
		param += '&consec=' + consec;
		param += '&table=' + table;
		param += '&user=' + user;

		$.ajax({
			type: "POST",
			url: '../' + central + '/despac/ajax_despac_despac.php',
			data: param,
			beforeSend: function() {
				$("#DialogNovedaID").html("<center>CARGANDO FORMULARIO PARA ACTUALIZAR NOVEDAD<br>POR FAVOR ESPERE</center>");
				$(".ui-dialog-buttonpane").hide();
			},
			success: function(data) {
				$("#DialogNovedaID").html(data);
				$(".ui-dialog-buttonpane").show();
			},
			complete: function() {
				$(".ui-dialog").css({
					left: ($(window).width() - $(".ui-dialog").outerWidth()) / 2
				}).animate({
					top: "-=50"
				}, 1000);
			}
		});
	} catch (e) {
		alert("Error en LoadPopupJQ: " + e.message + "\nLine: " + e.lineNumber);
	}
}

function DeleteNoveda(o, d, c, p, n, f, t, u) {
	try {
		$("#DialogNovedaID").html("<center>Eliminando...</center>");
		$(".ui-dialog").css({
			left: ($(window).width() - $(".ui-dialog").outerWidth()) / 2
		});

		var central = $("#dir_aplicaID").val();
		var param = 'Option=DeleteNoveda';
		param += '&despac=' + d;
		param += '&puesto=' + p;
		param += '&novedad=' + n;
		param += '&date=' + f;
		param += '&consec=' + c;
		param += '&table=' + t;
		param += '&user=' + u;

		$.ajax({
			type: "POST",
			url: '../' + central + '/despac/ajax_despac_despac.php',
			data: param,
			beforeSend: function() {
				$("#DialogNovedaID").html("<center>CARGANDO FORMULARIO PARA ELIMINAR LA NOVEDAD<br>POR FAVOR ESPERE</center>");
				$(".ui-dialog-buttonpane").remove();
			},
			success: function(data) {
				$("#DialogNovedaID").html(data);
				$(".ui-dialog").css({
					left: ($(window).width() - $(".ui-dialog").outerWidth()) / 2
				});
				$(o).remove();
			},
			complete: function() {
				setTimeout(function() {
					$(".ui-dialog").animate({
						opacity: "-=1",
						top: "-=400"
					}, 1500, function() {
						LoadPopupJQ("close")
					});
				}, 2000);
			}
		});
	} catch (e) {
		alert("Error en DeleteNoveda: " + e.message + "\nLine: " + e.lineNumber);
	}
}

function setNovedadNueva(n) {
	try {
		var novedad = n.item.label;
		if (novedad == 'Ninguna') {
			$("#cod_novedaNID").val("--");
			return false;
		}

		var codNoveda = novedad.split("-");
		alert(codNoveda[0]);
		$("#cod_novedaNID").val(codNoveda[0]);
	} catch (e) {
		alert("Error en setNovedadNueva: " + e.message + "\nLine: " + e.lineNumber);
	}
}

function UpdateNoveda(o, d, c, p, n, f, t, u) {
	try {
		var OldCodNoveda = $("#cod_novedaID").val();
		var NewCodNoveda = $("#cod_novedaNID").val();
		var NewNomNoveda = $("#novedadListID").val();
		var desc = $("#descripcionID").val();
		var central = $("#dir_aplicaID").val();
		var param = 'Option=UpdateNoveda';
		param += '&despac=' + d;
		param += '&puesto=' + p;
		param += '&novedad=' + n;
		param += '&date=' + f;
		param += '&consec=' + c;
		param += '&table=' + t;
		param += '&user=' + u;
		param += '&desc=' + desc;
		param += '&nCod=' + NewCodNoveda;
		param += '&oCod=' + OldCodNoveda;

		$.ajax({
			type: "POST",
			url: '../' + central + '/despac/ajax_despac_despac.php',
			data: param,
			beforeSend: function() {
				$("#DialogNovedaID").html("<center>CARGANDO FORMULARIO PARA ELIMINAR LA NOVEDAD<br>POR FAVOR ESPERE</center>");
			},
			success: function(data) {
				$("#DialogNovedaID").html(data);
				$(".ui-dialog").css({
					left: ($(window).width() - $(".ui-dialog").outerWidth()) / 2
				});
			}
		});
	} catch (e) {
		alert("Error en UpdateNoveda: " + e.message + "\nLine: " + e.lineNumber);
	}
}

function timeExtra(option) {
	try {
		if (option == 1) {
			var standa = $("#dir_aplicaID").val();
			var attributes = 'Ajax=on&option=timeExtra&standa=' + standa;

			$.ajax({
				type: 'POST',
				url: '../' + standa + '/despac/ajax_despachos.php?' + attributes,
				async: false,
				jsonCallback: 'time_extra',
				contentType: "application/json",
				dataType: 'json',
				beforeSend: function() {
					console.log("Generando Informe");
				},
				success: function(json) {
					$("#tdHourExtID").html(json.tdHourExtID + json.hidden);
					$("#tdDateExtID").html(json.tdDateExtID);

					$(function() {
						$("#fec_extraxID").datepicker();
						$("#hor_extraxID").timepicker({
							timeFormat: "hh:mm:ss",
							showSecond: false
						});
					});
				},
				complete: function() {
					console.log("Informe Generado");
				},
				error: function(xhr, ajaxOptions, thrownError) {
					console.log(xhr.status);
					console.log(ajaxOptions);
					console.log(thrownError);
				}
			});
		} else {
			$("#tdHourExtID").html("");
			$("#tdDateExtID").html("");
		}
	} catch (e) {
		console.log("Error Function timeExtra: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function GetOtrosDestin(ind_cumpli, index) {
	try {
		$("#frm_cumdes" + index).animate({
			opacity: '+=1'
		}, 1500);

		var standa = $("#dir_aplicaID").val();
		var attributes = 'Ajax=on&option=getOtrosDestin&standa=' + standa;
		attributes += '&num_despac=' + $("#num_despacID").val();
		attributes += '&nom_destin=' + $("#nom_destin" + index).val();
		attributes += '&index=' + index;

		$.ajax({
			url: "../" + standa + "/despac/ajax_despachos.php",
			type: "POST",
			data: attributes,
			async: true,
			beforeSend: function() {
				GetNovedadesDescargue(ind_cumpli, index);
			},
			success: function(data) {
				if (data != '1') {
					OpenPopupJQ('open', 'Destinatarios Pendientes', 'auto', 'auto', false, false, false);
					var popup = $("#OpenpopID");
					popup.parent().children().children('.ui-dialog-titlebar-close').hide();
					$(".ui-dialog-buttonpane").remove(); // Quitar la zona de botones
					popup.html(data);
				} else {
					if (confirm("Desea Finalizar el Despacho?")) {
						$("#ind_finali" + index).val('1');
					} else {
						$("#ind_finali" + index).val('0');
					}
				}
			}
		});
	} catch (e) {
		console.log("Error Function GetOtrosDestin: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function validateDestin(i) {
	try {
		var destin = "";
		var finali = true;

		$("#OpenpopID input[type=checkbox]:checked").each(function() {
			if (destin == "") {
				destin += $(this).val();
			} else {
				destin += "|" + $(this).val();
			}
		});

		$("#OpenpopID input[type=checkbox]:not(:checked)").each(function() {
			finali = false;
		});

		if (finali == true) {
			if (confirm("Desea Finalizar el Despacho?")) {
				$("#ind_finali" + i).val('1');
			} else {
				$("#ind_finali" + i).val('0');
			}
		} else {
			$("#ind_finali" + i).val('0');
		}

		$("#otr_destin" + i).val(destin);

		OpenPopupJQ('close');
	} catch (e) {
		console.log("Error Function validateDestin: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

/*! \fn: OpenPopupJQ
 *  \brief: Crea o destruye PopUp
 *  \author: Ing. Fabian Salinas
 *  \date: 24/06/2015
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
function OpenPopupJQ(opcion, titulo, alto, ancho, redimen, dragg, lockBack) {
	try {
		if (opcion == 'close') {
			$("#OpenpopID").dialog("destroy").remove();
		} else {
			$("<div id='OpenpopID' name='Openpop' />").dialog({
				height: alto,
				width: ancho,
				modal: lockBack,
				title: titulo,
				closeOnEscape: false,
				resizable: redimen,
				draggable: dragg,
				buttons: {
					Cerrar: function() {
						OpenPopupJQ('close')
					}
				}
			});
		}
	} catch (e) {
		console.log("Error Function OpenPopupJQ: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function b64EncodeUnicode(str) {
	return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
		return String.fromCharCode('0x' + p1);
	}));
}