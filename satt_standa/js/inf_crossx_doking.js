/*! \file: inf_crossx_doking.js
 *  \brief: JS para el informe Cross Doking /inform/inf_crossx_doking.php
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 08/10/2015
 *  \bug: 
 *  \warning: 
 */

$("body").ready(function(){
	//Acordion
	$("#accordionID").accordion({
		heightStyle: "content",
		collapsible: true
	});

	//Calendarios
	$( "#fec_iniciaID, #fec_finaliID" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy-mm-dd"
	});

	//Multiselect
	$("#cod_producID").multiselect();

	//Autocompletables
	var standa = $("#standaID").val();
	var attributes  = '&Ajax=on&standa='+standa;

	$("#nom_transpID").autocomplete({
		source: "../"+ standa +"/inform/class_gerenc_callce.php?Option=getTransp"+ attributes,
		minLength: 3,
		select: function( event, ui ) {
			$("#cod_transpID").val( ui.item.id );

			centroDistri( ui.item.id );
		}
	});

	//Pestañas
	$( "#tabs" ).tabs({
		beforeLoad: function( event, ui ) {
			ui.jqXHR.fail(function() {
				ui.panel.html( "Cargado..." );
			});
		}
	});

	$("#liGenera").click(function(){
		report( "g", "tabs-g" );
	});
	$("#liCrossD").click(function(){
		report( "0", "tabs-0" );
	});
});

function report( ind_pestan, id_div )
{
	try
	{
		var standar = $("#standaID");
		var pop = $(".ui-dialog").length;
		var report = '';

		if( pop > 0 )
			return false;

		if( validate() == false )
			return false;

		switch(ind_pestan){
			case 'g':
				report = 'generateReportG';
				break;
			case '0':
				report = 'generateReport';
				break;
			default:
				report = 'generateReport2';
		}

		//Load PopUp
		LoadPopupJQ( 'open', 'Cargando...', 'auto', 'auto', false, false, false );
		var popup = $("#popID");

		//Atributos del Ajax
		var attributes  = 'Ajax=on&Option='+report;
			attributes += '&ind_pestan='+ind_pestan;
			attributes += getParameFilter();

		//Ajax
		$.ajax({
			url: "../"+ standar.val() + "/lib/ClassCrossDoking.php",
			type: "POST", 
			data: attributes,
			async: true,
			beforeSend: function(){
				popup.parent().children().children('.ui-dialog-titlebar-close').hide();
				popup.html("<center><img src=\"../"+standar.val()+"/imagenes/ajax-loader.gif\"></center>");
				$(".ui-dialog-buttonpane").remove(); // Quitar la zona de botones
				$(".ui-dialog").animate({ "left": ( $(window).width() - 135 ), "top": ( $(window).height() - 155 ) }, 2000 );
			},
			success: function(data){
				$("#"+id_div).html(data);
				$("#"+id_div).css("height", ( $(window).height() - 155 ) );
				$("#"+id_div).css("overflow", "scroll");
			},
			complete: function(){
				LoadPopupJQ('close');
			}
		});
	}
	catch(e)
	{
		console.log( "Error Function report: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function validate()
{
	try
	{
		var ind_filact = $("#ind_filactID");
		var fec_inicia = $("#fec_iniciaID");
		var fec_finali = $("#fec_finaliID");
		var filtro = true;

		if( $("#num_despacID").val() == '' && $("#num_manifiID").val() == '' && $("#num_viajexID").val() == '' )
			filtro = false;

		if( $("#nom_transpID").val() == '' ){
			ind_filact.val('');
			$("#cod_transpID").val('');
			alert("Por Favor Seleccione una Transportadora.");
			return false;
		}else if( filtro == false && (fec_inicia.val() == '' || fec_finali.val() == '') ){
			ind_filact.val('');
			alert("Por Favor Seleccione un Rango de Fechas Valido.");
			return false;
		}else if( filtro == false && (fec_inicia.val() > fec_finali.val()) ){
			ind_filact.val('');
			alert("La Fecha Inicial debe ser Menor que la Fecha Final.");
			return false;
		}else{
			ind_filact.val('1');
			return true;
		}
	}
	catch(e)
	{
		console.log( "Error Function validate: "+e.message+"\nLine: "+e.lineNumber );
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
function LoadPopupJQ( opcion, titulo, alto, ancho, redimen, dragg, lockBack )
{
	try
	{
		if( opcion == 'close' ){
			$("#popID").dialog("destroy").remove();
		}else{
			$("<div id='popID' name='pop' />").dialog({
				height: alto, 
				width: ancho, 
				modal: lockBack,
				title: titulo, 
				closeOnEscape: false, 
				resizable: redimen, 
				draggable: dragg,
				buttons: {
					Cerrar: function(){ LoadPopupJQ('close') }
				}
			});
		}	
	}
	catch(e)
	{
		console.log( "Error Function LoadPopupJQ: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function getParameFilter()
{
	try
	{
		var attributes = '';
		var cod_produc = '""';

		$("input[type=checkbox]:checked").each(function(i,o){
			if( $(this).attr("name") == 'multiselect_cod_producID' ){
				cod_produc += ',"'+ $(this).val() +'"';
			}else{
				attributes += '&'+ $(this).attr("name");
				attributes += '='+ $(this).val();
			}
		});

		$("input[type=radio]:checked").each(function(i,o){
			attributes += '&'+ $(this).attr("name");
			attributes += '='+ $(this).val();
		});

		$("input[type=text]").each(function(i,o){
			if( $(this).val() != '' ){
				attributes += '&'+ $(this).attr("name");
				attributes += '='+ $(this).val();
			}
		});

		$("select").each(function(i,o){
			if( $(this).val() != '' ){
				attributes += '&'+ $(this).attr("name");
				attributes += '='+ $(this).val();
			}
		});

		$("input[type=hidden]").each(function(i,o){
			if( $(this).val() != '' ){
				attributes += '&'+ $(this).attr("name");
				attributes += '='+ $(this).val();
			}
		});

		if( cod_produc != '""' && cod_produc != '"",""' )
			attributes += '&cod_produc='+ cod_produc;

		return attributes;
	}
	catch(e)
	{
		console.log( "Error Function getParameFilter: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function centroDistri( cod_transp )
{
	try
	{
		var standa = $("#standaID").val();
		var attributes  = 'Ajax=on&Option=getCiudadTransp&standa='+standa;

		if( !cod_transp )
			attributes += '&cod_transp='+ $("#cod_transpID").val();
		else
			attributes += '&cod_transp='+ cod_transp;

		$.ajax({
			url: "../"+ standa +"/lib/ClassCrossDoking.php",
			type: "POST",
			data: attributes, 
			async: false,
			success: function(data){
				$("#ciudadTD").html(data);
			}
		});
	}
	catch(e)
	{
		console.log( "Error Function centroDistri: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function exportTableExcel( idTable )
{
	try{
		/*$("#"+idTable).table2excel({
			exclude: ".noExl",
			name: "Informe_CrossDokin"
		});*/

		var mHtml = $("#"+idTable).html();
		//mHtml = mHtml.replace('width=""', '');
	    //mHtml = mHtml.replace('height=""', '');

		$("#exportExcelID").val( "<html><body><table width='100%' height='100%' border='1' cellspacing='1' cellpadding='1' >" + mHtml + "</table></body></html>" );
		$("#form_CrossDokingID").submit();
	}
	catch(e)
	{
		console.log( "Error Function exportTableExcel: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function printTotal()
{
	try{
		$("#totalDespac1").html( $("#totalDespac").html() );
		$("#totalClient1").html( $("#totalClient").html() );
	}
	catch(e)
	{
		console.log( "Error Function printTotal: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function showDetail( fecIni, fecFin )
{
	try
	{
		var pop = $(".ui-dialog").length;

		if( pop > 0 )
			return false;

		var standa = $("#standaID").val();
		var attributes  = 'Ajax=on&Option=generateReport2&standa='+standa;
			attributes += getParameFilter();
			attributes += '&fec_inicia='+fecIni;
			attributes += '&fec_finali='+fecFin;

		$.ajax({
			url: "../"+ standa + "/lib/ClassCrossDoking.php",
			type: "POST", 
			data: attributes,
			async: true,
			beforeSend: function(){
				$.blockUI({ message: '<h1> Generando Informe...</h1>', 
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
			},
			success: function(data){
				$("#divTableID").html(data);
			},
			complete: function(){
				$.unblockUI();
			}
		});
	}
	catch(e)
	{
		console.log( "Error Function showDetail: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function clearDiv()
{
	try{
		$("#divTableID").html("");
	}
	catch(e)
	{
		console.log( "Error Function clearDiv: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}