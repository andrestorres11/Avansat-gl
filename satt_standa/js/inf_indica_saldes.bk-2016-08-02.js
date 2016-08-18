$(function() {
	var standa = $("#standaID").val();
	var attributes = '&Ajax=on&standa=' + standa;
	$("body").removeAttr("class");
	$(".accordion").accordion({
		collapsible: true,
		heightStyle: "content",
		icons: {
			"header": "ui-icon-circle-arrow-e",
			"activeHeader": "ui-icon-circle-arrow-s"
		}
	}).click(function() {
		$("body").removeAttr("class");
	})
	$("#contenido").css({
		height: 'auto',
	});

	$("#nom_transpID").autocomplete({
		source: "../" + standa + "/transp/ajax_transp_transp.php?Option=buscarTransportadora" + attributes,
		minLength: 3,
		select: function(event, ui) {
			$("#cod_transpID").val(ui.item.id);
			$("body").removeAttr("class");
		}
	});

	$("#tabs").tabs({
		beforeLoad: function(event, ui) {
			ui.jqXHR.fail(function() {
				ui.panel.html("Cargado...");
			});
		}
	});

	$("#fec_iniciaID").datepicker();
	$("#fec_iniciaID").datepicker('option', {
		dateFormat: 'yy-mm-dd'
	});
	$("#fec_finaliID").datepicker();
	$("#fec_finaliID").datepicker('option', {
		dateFormat: 'yy-mm-dd'
	});

});

function getInforme() {
	inc_remover_alertas(); //remueve todos los mensajes
	var standa = $("#standaID").val();
	var transp = $("#cod_transpID").val();
	var fec_inicia = $("#fec_iniciaID").val();
	var fec_finali = $("#fec_finaliID").val();
	var errores = false;
	//validamos que las fechas y ls transportadoras hayan sido ingresadas correctamente

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
		var con = checkConnection();
		if (con) {
			var dataForm = getDataForm();
			$.ajax({
				url: "../" + standa + "/inform/ajax_inform_inform.php",
				data: "Option=getInformIndSaldes&Ajax=on&" + dataForm,
				type: "POST",
				async: true,
				beforeSend: function(obj) {
					$.blockUI({
						theme: true,
						title: 'Indicador de Salida de Despachos',
						draggable: false,
						message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
					});
				},
				success: function(data) {
					$("#generaID").html("");
					$.unblockUI();
					$("#generaID").html(data);
					$("#tabla").css('overflow', 'scroll');
					/*pintar();
					pintarPie();*/
					$("#tabla").css('height', ($(window).height() - 195));
				}
			});
		} else {
			setTimeout(function() {
				inc_alerta("hidden", "Por favor verifica tu conexión a internet");
			}, 510);

		}
	}
}

function pintar() {
	var tipo = parseInt($("#tipo").val());
	var total = parseInt($("#total").val());
	var fec_inicia = $("#fec_iniciaID").val();
	var fec_finali = $("#fec_finaliID").val();
	var titulo = "";
	var categorias = new Array();
	var cumplidas = new Array();
	var nocumplidas = new Array();
	for (var i = 0; i <= total; i++) {

		categorias[i] = $("#fecha" + i).children().html();
		titulo = 'Fecha';
		cumplidas[i] = parseInt($("#cumplida" + i).children().html());
		nocumplidas[i] = parseInt($("#nocumplida" + i).children().html());
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
				text: 'Indicador Salida de Despachos'
			},
			subtitle: {
				text: 'Gráfica de Indicador Salida de Despacho del ' + fec_inicia + ' al ' + fec_finali
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
				name: 'Despachos con el Tiempo Acordado',
				data: cumplidas,
				color: '#3A8104'
			}, {
				name: 'Despachos Fuera del Tiempo Acordado',
				data: nocumplidas,
				color: '#EFD31C'
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
	var tipo = parseInt($("#tipo").val());
	if (tipo) {
		var cumplidas = parseInt($("#tcumplidas").children().html());
		var incumplidas = parseInt($("#tincumplidas").children().html());
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
					name: 'Despachos Fuera del Tiempo Acordado: ' + incumplidas,
					y: incumplidas,
					sliced: true,
					selected: true,
					color: '#EFD31C'
				}, {
					name: 'Despachos con el Tiempo Acordado: ' + cumplidas,
					y: cumplidas,
					color: '#3A8104'
				}]
			}]
		});
	}
}

function getDetalleSalDes(fec_inicia, fec_finali, indicador) {
	var conn = checkConnection();
	if (conn) {
		var standa = $("#standaID").val();
		var cod_transp = $("#cod_transpID").val();
		var tip_despac = $("#tip_despacID").val();
		var num_despac = $("#num_despacID").val();
		var num_viajex = $("#num_viajexID").val();
		var cod_manifi = $("#num_manifiID").val();
		$.ajax({
			url: "../" + standa + "/inform/ajax_inform_inform.php",
			data: "Option=getDetalleSalDes&Ajax=on&fec_inicia=" + fec_inicia +"&indicador="+indicador+ "&fec_finali=" + fec_finali + "&cod_transp=" + cod_transp + "&tip_despac=" + tip_despac + "&num_despac=" + num_despac + "&num_viajex=" + num_viajex + "&cod_manifi=" + cod_manifi,
			type: "POST",
			async: true,
			beforeSend: function(obj) {
				$("#PopUpID").html('');
				$.blockUI({
					theme: true,
					title: 'Detalle Eal cumplidas en Ruta',
					draggable: false,
					message: '<center><img src="../' + standa + '/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
				});
			},
			success: function(data) {
				$.unblockUI();
				LoadPopupJQ('open', 'Detalle', ($(window).height() - 40), ($(window).width() - 40), false, false, true);
				var popup = $("#popID");
				popup.parent().children().children('.ui-dialog-titlebar-close').hide();
				popup.append(data); // //lanza el popUp
				$(".scroll").css('overflow', 'scroll');
				$(".scroll").css('height', ($(window).height() - 195));
			}
		});
	} else {
		setTimeout(function() {
			inc_alerta("notify", "Por favor verifica tu conexión a internet");
		}, 510);
	}
}

function getExcelSalDes() {
	var detalle = $("#dataDetalle").parent().html();
	$("#exportExcelID").val(detalle);
	$("#eal").submit();
}