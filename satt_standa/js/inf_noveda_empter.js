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
});

function report( ind_pestan, id_div )
{
	try
	{
		var standar = $("#standaID");
		var report = '';

		switch(ind_pestan){
			case 'g':
				report = 'generateReportG';
				break;
		}

		var attr = getParameFilter();
		if( attr == false )
			return false;

		//Atributos del Ajax
		var attributes  = 'Ajax=on&Option='+report;
			attributes += '&ind_pestan='+ind_pestan;
			attributes += attr; 

		//Ajax
		$.ajax({
			url: "../"+ standar.val() + "/inform/inf_noveda_empter.php",
			type: "POST", 
			data: attributes,
			async: true,
			beforeSend: function(){
				blocK(true);
			},
			success: function(data){
				$("#"+id_div).html(data);
				$("#"+id_div).css("height", ( $(window).height() - 155 ) );
				$("#"+id_div).css("overflow", "scroll");
			},
			complete: function(){
				blocK();
			}
		});
	}
	catch(e)
	{
		console.log( "Error Function report: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function blocK( ind )
{
	try{
		if( ind == true ){
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
		}else{
			$.unblockUI();
		}
	}
	catch(e)
	{
		console.log( "Error Function blocK: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function showDetail( fec1, fec2, ind )
{
	try{
		var standar = $("#standaID");
		var attributes = 'Ajax=on&Option=detailReport';
		attributes += '&fec_inicia=' + fec1;
		attributes += '&fec_finali=' + fec2;
		attributes += '&ind_report=' + ind;

		LoadPopupJQ( 'open', 'Detalle', ( $(window).height() - 50 ), ( $(window).width() - 50 ), false, false, true );
		var popup = $("#popID");

		$.ajax({
			url: "../"+ standar.val() + "/inform/inf_noveda_empter.php",
			type: "POST", 
			data: attributes,
			async: false,
			beforeSend: function(){
				popup.parent().children().children('.ui-dialog-titlebar-close').hide();
				popup.html("<center><img src=\"../"+standar.val()+"/imagenes/ajax-loader.gif\"></center>");
			},
			success: function(data){
				popup.html(data);
				popup.css('overflow-y', 'true');
				popup.css('overflow-x', 'true');
			}
		});
	}
	catch(e)
	{
		console.log( "Error Function showDetail: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function getParameFilter()
{
	try{
		var attributes = '';
		var dato;
		var bandera = false;
		var result = true;

		dato = $("#num_viajexID");
		if( dato.val() != '' ){
			bandera = true;
			attributes += '&' + dato.attr('name') + '=' + dato.val();
		}

		var fec1 = $("#fec_iniciaID");
		var fec2 = $("#fec_finaliID");
		f1 = new Date( fec1.val() );
		f2 = new Date( fec2.val() );

		if( fec1.val() == '' && fec2.val() == '' && bandera == false ){
			alert("Por favor seleccione un parametro de busqueda.");
			result = false;
		}else{
			if( bandera == false ){
				if( fec1.val() != '' && fec2.val() != '' && f1 <= f2 ){
					attributes += '&' + fec1.attr("name") + '=' + fec1.val();
					attributes += '&' + fec2.attr("name") + '=' + fec2.val();
				}else{
					alert("El rango de fechas no es valido.");
					result = false;
				}
			}
		}

		if( result == false )
			return false;
		else
			return attributes;
	}
	catch(e)
	{
		console.log( "Error Function getParameFilter: "+e.message+"\nLine: "+e.lineNumber );
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

function exportTableExcelNovEmpTer()
{
	try{
		$("#form_NovedaEmpterID").submit();
	}
	catch(e)
	{
		console.log( "Error Function exportTableExcel: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}