/*! \file: inf_califi_desusu.js
 *  \brief: Archivo para las sentencias JS del inform/inf_califi_desusu.php
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 11/02/2016
 *  \bug: 
 *  \warning: 
 */

/*! \fn: ("body").ready
 *  \brief: Crea los multiselect, calendarios, acordion y pestañas
 *  \author: Ing. Fabian Salinas
 *  \date:  11/02/2016
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
 *  \date:  12/02/2016
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
			url: "../" + standar + "/califi/class_califi_califi.php",
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

/*! \fn: getListActivi
 *  \brief: Carga el Select de las actividades segun operacion
 *  \author: Ing. Fabian Salinas
 *  \date:  12/02/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: obj   Objeto
 *  \return: 
 */
function getListActivi(obj) {
	try {
		var standar = $("#standaID").val();
		var attributes = 'Ajax=on&Option=getListActivi';
		attributes += '&cod_operac=' + obj.val();

		$.ajax({
			url: "../" + standar + "/califi/class_califi_califi.php",
			type: "POST",
			data: attributes,
			async: true,
			beforeSend: function() {
				BlocK('Cargando lista de Actividades...', true);
			},
			success: function(datos) {
				$("#cod_activiID").parent().html(datos);
			},
			complete: function() {
				$("#cod_activiID").multiselect().multiselectfilter();
				BlocK();
			}
		});
	} catch (e) {
		console.log("Error Function getListActivi: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

/*! \fn: getDataFormCalifi
 *  \brief: Trae la data del formulario
 *  \author: Ing. Fabian Salinas
 *  \date:  13/02/2016
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
		var usr_califi = [];
		var usr_creaci = [];
		var cod_activi = [];

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
				} else if ($(this).attr('name').substr(12, 10) == 'usr_califi') {
					usr_califi[c] = $(this).val();
					c++;
				} else if ($(this).attr('name').substr(12, 10) == 'usr_creaci') {
					usr_creaci[d] = $(this).val();
					d++;
				} else if ($(this).attr('name').substr(12, 10) == 'cod_activi') {
					cod_activi[e] = $(this).val();
					e++;
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
		if (usr_califi.length > 0) {
			result += "&usr_califi='" + usr_califi.join("','") + "'";
		}
		if (usr_creaci.length > 0) {
			result += "&usr_creaci='" + usr_creaci.join("','") + "'";
		}
		if (cod_activi.length > 0) {
			result += "&cod_activi=" + cod_activi.join();
		}


		var notSelect = [];
		x = 0;
		$(".ui-multiselect").each(function(ind, obj) {
			notSelect[x] = $(this).parent().attr('id').substr(0, 10);
			x++;
		});


		$("#form_InfCalifiDesUsuID select option:selected").each(function(ind, obj) {
			if ($(this).val() != '' && ($.inArray($(this).parent().attr('name'), notSelect) == -1)) {
				result += '&' + $(this).parent().attr('name') + '=' + $(this).val();
			}
		});

		$("#form_InfCalifiDesUsuID input[type=text]").each(function(ind, obj) {
			if ($(this).val() != '') {
				result += '&' + $(this).attr('name') + '=' + $(this).val();
			}
		});

		$("#form_InfCalifiDesUsuID input[type=hidden]").each(function(ind, obj) {
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
 *  \date: 16/02/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: 
 *  \return: 
 */
function informDetail(ind_pestan, ind_column, cod_activi) {
	try {
		var val = validaciones();

		if (!val) {
			return false;
		}

		var standar = $("#standaID").val();
		var attributes = 'Ajax=on&Option=informDetail';
		attributes += getDataFormCalifi();
		attributes += '&ind_pestan=' + ind_pestan;
		attributes += '&ind_column=' + ind_column;
		attributes += '&cod_activi=' + cod_activi;

		LoadPopupJQ('open', 'Respuesta', ($(window).height() - 50), ($(window).width() - 50), false, false, true, 'popupinfDetailID');
		var popup = $("#popupinfDetailID");

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

/*! \fn: exportTableExcel
 *  \brief: Guarda la tabla en el Hidden y da submit al formulario
 *  \author: Ing. Fabian Salinas
 *  \date:  18/02/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: idTab  String  ID de la tabla a exportar
 *  \return: 
 */
function exportTableExcel(idTab) {
	try {
		$("#exportExcelID").val("<table>" + $("#" + idTab).html() + "</table>");
		$("#form_InfCalifiDesUsuID").submit();
	} catch (e) {
		console.log("Error Function exportTableExcel: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}