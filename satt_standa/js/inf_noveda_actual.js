/*! \file: inf_noveda_actual.js
 *  \brief: Archivo para las sentencias JS del inform/inf_noveda_actual.php (Informe de novedades actualizadas)
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 29/02/2016
 *  \bug: 
 *  \warning: 
 */

/*! \fn: ("body").ready
 *  \brief: Crea los multiselect, calendarios, acordion y pestañas
 *  \author: Ing. Fabian Salinas
 *  \date:  29/02/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: 
 *  \return: 
 */
$("body").ready(function() {
	//Calendarios
	$("#fec_iniciaID, #fec_finaliID").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy-mm-dd"
	});

	//Multiselect
	$(".multiSel").children().multiselect().multiselectfilter();

	//Pestañas
	$("#tabs").tabs({
		beforeLoad: function(event, ui) {
			ui.jqXHR.fail(function() {
				ui.panel.html("Cargado...");
			});
		}
	});
});

/*! \fn: report
 *  \brief: Realiza la peticion ajax para pintar el informe
 *  \author: Ing. Fabian Salinas
 *  \date:  29/02/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: ind_pestan  String  Indicador de la pestaña a la que pertenece
 *  \param: id_div      String  ID del Div donde se pintara la respuesta del ajax
 *  \return: 
 */
function report(ind_pestan, id_div) {
	try {
		var val = validaciones();

		if (!val) {
			return false;
		}

		var standar = $("#standaID").val();
		var attributes = 'Ajax=on&Option=informGeneral';
		attributes += getDataFormCalifi();
		attributes += '&ind_pestan=' + ind_pestan;

		$.ajax({
			url: "../" + standar + "/inform/inf_noveda_actual.php",
			type: "POST",
			data: attributes,
			async: false,
			beforeSend: function() {
				BlocK('Generando Informe...', true);
			},
			success: function(datos) {
				$("#" + id_div).html(datos);
			},
			complete: function() {
				BlocK();
			}
		});
	} catch (e) {
		console.log("Error Function report: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

/*! \fn: getDataFormCalifi
 *  \brief: Trae la data del formulario
 *  \author: Ing. Fabian Salinas
 *  \date:  29/02/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: 
 *  \return: 
 */
function getDataFormCalifi() {
	try {
		var result = '';
		var cod_tipdes = [];
		var cod_transp = [];

		var a = 0,
			b = 0,
			c = 0,
			d = 0,
			e = 0;
		$("input[type=checkbox]:checked").each(function(ind, obj) {
			if ($(this).val() != '') {
				if ($(this).attr('name').substr(0, 10) == 'cod_tipdes') {
					cod_tipdes[a] = $(this).val();
					a++;
				} else if ($(this).attr('name').substr(12, 10) == 'cod_transp') {
					cod_transp[b] = $(this).val();
					b++;
				} else {
					result += '&' + $(this).attr('name') + '=' + $(this).val();
				}
			}
		});

		if (cod_tipdes.length > 0) {
			result += "&cod_tipdes=" + cod_tipdes.join();
		}
		if (cod_transp.length > 0) {
			result += "&cod_transp='" + cod_transp.join("','") + "'";
		}


		var notSelect = [];
		x = 0;
		$(".ui-multiselect").each(function(ind, obj) {
			notSelect[x] = $(this).parent().attr('id').substr(0, 10);
			x++;
		});


		$("#form_InfBitacoUpdDespacID select option:selected").each(function(ind, obj) {
			if ($(this).val() != '' && ($.inArray($(this).parent().attr('name'), notSelect) == -1)) {
				result += '&' + $(this).parent().attr('name') + '=' + $(this).val();
			}
		});

		$("#form_InfBitacoUpdDespacID input[type=text]").each(function(ind, obj) {
			if ($(this).val() != '') {
				result += '&' + $(this).attr('name') + '=' + $(this).val();
			}
		});

		$("#form_InfBitacoUpdDespacID input[type=hidden]").each(function(ind, obj) {
			if ($(this).val() != '') {
				result += '&' + $(this).attr('name') + '=' + $(this).val();
			}
		});

		return result;
	} catch (e) {
		console.log("Error Function getDataFormCalifi: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

/*! \fn: informDetail
 *  \brief: Realiza la peticion ajax para pintar el detallado del informe
 *  \author: Ing. Fabian Salinas
 *  \date: 01/03/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: 
 *  \return: 
 */
function informDetail(fec_inicia, fec_finali) {
	try {
		var val = validaciones();

		if (!val) {
			return false;
		}

		var standar = $("#standaID").val();
		var attributes = 'Ajax=on&Option=informDetail';
		attributes += getDataFormCalifi();
		attributes += '&fec_inicia=' + fec_inicia;
		attributes += '&fec_finali=' + fec_finali;

		LoadPopupJQ('open', 'Respuesta', ($(window).height() - 50), ($(window).width() - 50), false, false, true, 'popupinfDetailID');
		var popup = $("#popupinfDetailID");
		popup.parent().children().children('.ui-dialog-titlebar-close').hide();

		$.ajax({
			url: "../" + standar + "/inform/inf_noveda_actual.php",
			type: "POST",
			data: attributes,
			async: false,
			beforeSend: function() {
				BlocK('Generando Informe...', true);
			},
			success: function(datos) {
				popup.html(datos);
			},
			complete: function() {
				BlocK();
			}
		});
	} catch (e) {
		console.log("Error Function informDetail" + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

/*! \fn: expTabExcelNovupd
 *  \brief: Guarda la tabla en el Hidden y da submit al formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 01/03/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: idTab  String  ID de la tabla a exportar
 *  \return: 
 */
function expTabExcelNovupd(idTab) {
	try {
		$("#exportExcelID").val("<table>" + $("#" + idTab).html() + "</table>");
		$("#form_InfBitacoUpdDespacID").submit();
	} catch (e) {
		console.log("Error Function expTabExcelNovupd: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}