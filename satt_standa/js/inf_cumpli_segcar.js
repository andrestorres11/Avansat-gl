/*! \file: inf_cumpli_segcar.js
 *  \brief: Script para el informe Indicador llamadas a cita de Cargue 
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 26/01/2015
 *  \bug: 
 *  \warning: 
 */

$("body").ready(function() {
	//Acordion
	$(".accordion").accordion({
		heightStyle: "content",
		collapsible: true
	});

	$("#contenido").css({
		height: 'auto',
	});

	//Calendarios
	$("#fec_iniciaID, #fec_finaliID").datepicker({
		changeMonth: false,
		changeYear: false,
		dateFormat: "yy-mm-dd"
	});

	//Autocompletables
	var standa = $("#standaID").val();
	var attributes = '&Ajax=on&standa=' + standa;

	$("#nom_transpID").autocomplete({
		source: "../" + standa + "/inform/class_gerenc_callce.php?Option=getTransp" + attributes,
		minLength: 3,
		select: function(event, ui) {
			$("#cod_transpID").val(ui.item.id);
		}
	});

	//Pestañas
	$("#tabs").tabs({
		beforeLoad: function(event, ui) {
			ui.jqXHR.fail(function() {
				ui.panel.html("Cargado...");
			});
		}
	});
});

function report(ind_pestan, id_div) {
	try {
		inc_remover_alertas(); //remueve todos los mensajes
		var transp = $("#cod_transpID").val();
		var fec_inicia = $("#fec_iniciaID").val();
		var fec_finali = $("#fec_finaliID").val();
		var errores = false;
		var standar = $("#standaID");
		var report = '';

		switch (ind_pestan) {
			case 'g':
				report = 'generateReportG';
				break;
			default:
				report = 'generateReport';
		}

		if (!transp) {
			setTimeout(function() {
				inc_alerta("nom_transpID", "Este Campo es Obligatorio");
			}, 510);
			errores = true;
		}
		if (!fec_inicia) {
			setTimeout(function() {
				inc_alerta("fec_iniciaID", "Este Campo es Obligatorio");
			}, 510);
			errores = true;
		}
		if (!fec_finali) {
			setTimeout(function() {
				inc_alerta("fec_finaliID", "Este Campo es Obligatorio");
			}, 510);
			errores = true;
		}
		var ini = new Date(fec_inicia);
		var fin = new Date(fec_finali);
		if (fin < ini) {
			setTimeout(function() {
				inc_alerta("fec_iniciaID", "Fecha inicial mayor a la final");
			}, 510);
			setTimeout(function() {
				inc_alerta("fec_finaliID", "Fecha final  menor a la inicial");
			}, 510);
			errores = true;
		}

		if (errores == false) {
			//Atributos del Ajax
			var attributes = 'Ajax=on&Option=' + report;
			attributes += '&ind_pestan=' + ind_pestan;
			attributes += getParameFilter();

			//Ajax
			$.ajax({
				url: "../" + standar.val() + "/inform/inf_cumpli_segcar.php",
				type: "POST",
				data: attributes,
				async: true,
				beforeSend: function() {
					blocK(true);
				},
				success: function(data) {
					$("#" + id_div).html(data);
					$("#" + id_div).css("height", ($(window).height() - 145));
					$("#" + id_div).css("overflow", "scroll");
				},
				complete: function() {
					//pintar();
					//pintarPie();
					blocK();
				}
			});
		}
	} catch (e) {
		console.log("Error Function report: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function getParameFilter() {
	try {
		var attributes = '';

		$("input[type=checkbox]:checked").each(function(i, o) {
			attributes += '&' + $(this).attr("name");
			attributes += '=' + $(this).val();
		});

		$("input[type=radio]:checked").each(function(i, o) {
			attributes += '&' + $(this).attr("name");
			attributes += '=' + $(this).val();
		});

		$("input[type=text]").each(function(i, o) {
			if ($(this).val() != '') {
				attributes += '&' + $(this).attr("name");
				attributes += '=' + $(this).val();
			}
		});

		$("select").each(function(i, o) {
			if ($(this).val() != '') {
				attributes += '&' + $(this).attr("name");
				attributes += '=' + $(this).val();
			}
		});

		$("input[type=hidden]").each(function(i, o) {
			if ($(this).val() != '') {
				attributes += '&' + $(this).attr("name");
				attributes += '=' + $(this).val();
			}
		});

		return attributes;
	} catch (e) {
		console.log("Error Function getParameFilter: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function blocK(ind) {
	try {
		if (ind == true) {
			$.blockUI({
				message: '<h1> Generando Informe...</h1>',
				css: {
					border: 'none',
					padding: '15px',
					backgroundColor: '#438710',
					'-webkit-border-radius': '20px',
					'-moz-border-radius': '20px',
					opacity: .8,
					color: '#fff'
				}
			});
		} else {
			$.unblockUI();
		}
	} catch (e) {
		console.log("Error Function blocK: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function pintar() {
	var tipo = $("#tipo").val();
	var total = parseInt($("#total").val());
	var fec_inicia = $("#fec_iniciaID").val();
	var fec_finali = $("#fec_finaliID").val();
	var categorias = new Array();
	var cum_segcar = new Array();
	var inc_segfar = new Array();
	var inc_seglyt = new Array();

	for (var i = 0; i <= total; i++) {
		categorias[i] = $("#fecha" + i).html();
		cum_segcar[i] = parseInt($("#cum_segcar" + i).children().html());
		inc_segfar[i] = parseInt($("#inc_segfar" + i).children().html());
		inc_seglyt[i] = parseInt($("#inc_seglyt" + i).children().html());
	}

	if (tipo) {
		$('#container').highcharts({
			credits: {
				enabled: false
			},
			chart: {
				type: 'column'
			},
			title: {
				text: 'Indicador Llamadas a Cita de Cargue'
			},
			subtitle: {
				text: 'Gráfica del Indicador Llamadas a Cita de Cargue  del ' + fec_inicia + ' al ' + fec_finali
			},
			xAxis: {
				categories: categorias,
				crosshair: true
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Cantidad'
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:8px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0; bacgraund-color:#EBF8E2">{series.name}: </td>' +
					'<td style="padding:0"><b>{point.y}</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				}
			},
			series: [{
				name: $("#name_segcar").html(),
				data: cum_segcar,
				color: '#3A8104'
			}, {
				name: $("#name_segfar").html(),
				data: inc_segfar,
				color: '#EFD31C'
			}, {
				name: $("#name_seglyt").html(),
				data: inc_seglyt,
				color: '#F7A35C'
			}]
		});
	}

	$(".highcharts-container").css({
		left: '-13.567px'
	});

	$(".highcharts-background").attr({
		fill: '#EBF8E2'
	});

	$(".highcharts-button").html("");
}

function pintarPie() {
	var tipo = $("#tipo").val();
	if (tipo) {
		var tot_segcar = parseInt($("#tot_segcar").children().html());
		var tot_segfar = parseInt($("#tot_segfar").children().html());
		var tot_seglyt = parseInt($("#tot_seglyt").children().html());
		$('#container2').highcharts({
			credits: {
				enabled: false
			},
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {
				text: 'Indicador Salida de Despacho Acumulado'
			},
			tooltip: {
				pointFormat: '<b>\u00A0 \u00A0 \u00A0 \u00A0 \u00A0 {point.percentage:.1f} %</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: false
					},
					showInLegend: true
				}
			},
			series: [{
				name: 'Registros',
				colorByPoint: true,
				data: [{
					name: $("#name_segcar").html() + tot_segcar,
					y: tot_segcar,
					sliced: true,
					selected: true,
					color: '#3A8104'
				}, {
					name: $("#name_segfar").html() + tot_segfar,
					y: tot_segfar,
					color: '#EFD31C'
				}, {
					name: $("#name_seglyt").html() + tot_seglyt,
					y: tot_seglyt,
					color: '#F7A35C'
				}]
			}]
		});
	}
}

function showDetail(fec_inicia, fec_finali, cum_segcar) {
	try {
		var standar = $("#standaID").val();

		//Atributos del Ajax
		var attributes = 'Ajax=on&Option=showDetail';
		attributes += getParameFilter();
		attributes += '&cum_segcar=' + cum_segcar;
		attributes += '&fec_inicia=' + fec_inicia;
		attributes += '&fec_finali=' + fec_finali;

		LoadPopupJQ('open', 'Detalle', ($(window).height() - 50), ($(window).width() - 50), false, false, true);
		var popup = $("#popID");

		//Ajax
		$.ajax({
			url: "../" + standar + "/inform/inf_cumpli_segcar.php",
			type: "POST",
			data: attributes,
			async: true,
			beforeSend: function() {
				popup.parent().children().children('.ui-dialog-titlebar-close').hide();
				popup.html("<center><img src=\"../" + standar + "/imagenes/ajax-loader.gif\"></center>");
				//$(".ui-dialog-buttonpane").remove(); // Quitar la zona de botones
			},
			success: function(data) {
				popup.html(data);
				popup.css('overflow-y', 'true');
				popup.css('overflow-x', 'true');
			}
		});
	} catch (e) {
		console.log("Error Function showDetail: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

/*! \fn: LoadPopupJQ
 *  \brief: Crea o destruye PopUp
 *  \author: Ing. Fabian Salinas
 *	\date: 24/06/2015
 *	\date modified: dia/mes/año
 *  \param: opcion   String   open, close
 *  \param: titulo   String   Titulo del PopUp
 *  \param: alto   	 Integer  Altura PopUp
 *  \param: ancho    Integer  Ancho PopUp
 *  \param: redimen  Boolean  True = Redimencionable
 *  \param: dragg    Boolean  True = El PopUp se puede arrastras
 *  \param: lockBack Boolean  True = Bloquea el BackGround
 *  \return: 
 */
function LoadPopupJQ(opcion, titulo, alto, ancho, redimen, dragg, lockBack) {
	try {
		if (opcion == 'close') {
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
						LoadPopupJQ('close')
					}
				}
			});
		}
	} catch (e) {
		console.log("Error Function LoadPopupJQ: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}

function exportTableExcel(idTab) {
	try {
		$("#exportExcelID").val( "<table>"+ $("#"+idTab).html() +"</table>" );
		$("#formSeguimCargueID").submit();
	} catch (e) {
		console.log("Error Function exportTableExcel: " + e.message + "\nLine: " + e.lineNumber);
		return false;
	}
}