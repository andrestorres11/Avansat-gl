$("body").ready(function(){

	//Acordion
	$("#accordionID").accordion({
		heightStyle: "content",
		collapsible: true
	});

	$("#tableID").css('height', ( $(window).height() - 166 ) );
	$("#tableID").css('overflow', 'scroll');


	//Autocompletables
	var standa = $("#standaID").val();
	var attributes  = '&Ajax=on&standa='+standa;

	$("#nom_transpID").autocomplete({
		source: "../"+ standa +"/inform/class_gerenc_callce.php?Option=getTransp"+ attributes,
		minLength: 3,
		select: function( event, ui ) {
			$("#cod_transpID").val( ui.item.id );
		}
	});
});

$(function() {
	//Pestañas
	$( "#tabs" ).tabs({
		beforeLoad: function( event, ui ) {
			ui.jqXHR.fail(function() {
				ui.panel.html( "Cargado..." );
			});
		}
	});

	$("#liGenera").click(function(){
		report( "0", "tabs-0" );
	});

	//Fechas
	$( "#fec_iniciaID, #fec_finalxID" ).datepicker({
		changeMonth: true,
		changeYear: true
	});

	$.mask.definitions["A"]="[12]";
	$.mask.definitions["M"]="[01]";
	$.mask.definitions["D"]="[0123]";

	$.mask.definitions["H"]="[012]";
	$.mask.definitions["N"]="[012345]";
	$.mask.definitions["n"]="[0123456789]";

	$( "#fec_iniciaID, #fec_finalxID" ).mask("Annn-Mn-Dn");
});

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
		console.log( "Error Fuction LoadPopupJQ: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function justNumbers(e)
{
	var keynum = window.event ? window.event.keyCode : e.which;
	if ((keynum == 8) || (keynum == 46))
	return true;

	return /\d/.test(String.fromCharCode(keynum));
}

function report( ind_tipdes, id_div )
{
	try
	{
		var standar = $("#standaID");
		var pop = $(".ui-dialog").length;

		if( pop > 0 )
			return false;

		if( validate() == false )
			return false;

		//Load PopUp
		LoadPopupJQ( 'open', 'Cargando...', 'auto', 'auto', false, false, false );
		var popup = $("#popID");

		//Atributos del Ajax
		var attributes  = 'Ajax=on&Option=generateReport';
			attributes += '&ind_tipdes='+ind_tipdes;
			attributes += getParameFilter();

		//Ajax
		$.ajax({
			url: "../"+ standar.val() + "/inform/class_gerenc_callce.php",
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
				$("#"+id_div).css("height", ( $(window).height() - 150 ) );
				$("#"+id_div).css("overflow", "scroll");
			},
			complete: function(){
				LoadPopupJQ('close');
			}
		});
	}
	catch(e)
	{
		console.log( "Error Fuction generalReport: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function getParameFilter()
{
	try
	{
		var attributes = '';

		$("input[type=checkbox]:checked").each(function(i,o){
			attributes += '&'+ $(this).attr("name");
			attributes += '='+ $(this).val();
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

		return attributes;
	}
	catch(e)
	{
		console.log( "Error Fuction getParameFilter: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function validate()
{
	try
	{
		var ind_filact = $("#ind_filactID");
		var fec_inicia = $("#fec_iniciaID");
		var fec_finalx = $("#fec_finalxID");
		var filtro = true;

		if( $("#num_despacID").val() == '' && $("#num_manifiID").val() == '' && $("#num_viajexID").val() == '' )
			filtro = false;

		if( $("#nom_transpID").val() == '' ){
			ind_filact.val('');
			$("#cod_transpID").val('');
			alert("Por Favor Seleccione una Transportadora.");
			return false;
		}else if( filtro == false && (fec_inicia.val() == '' || fec_finalx.val() == '') ){
			ind_filact.val('');
			alert("Por Favor Seleccione un Rango de Fechas Valido.");
			return false;
		}else if( filtro == false && (fec_inicia.val() > fec_finalx.val()) ){
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
		console.log( "Error Fuction validate: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function showDetail( ind_tipdes, fec_inicia, fec_finalx, est_llamad )
{
	try
	{
		var pop = $(".ui-dialog").length;
		if( pop > 0 )
			return false;

		var standar = $("#standaID");
		var attributes  = 'Ajax=on&Option=detail';
			attributes += getParameFilter();
			attributes += '&ind_tipdes='+ ind_tipdes;
			attributes += '&fec_inicia='+ fec_inicia;
			attributes += '&fec_finalx='+ fec_finalx;
			attributes += '&est_llamad='+ est_llamad;

		//Load PopUp
		LoadPopupJQ( 'open', 'Registro de Llamadas', ( $(window).height() - 50 ), ( $(window).width() - 50 ), false, false, true );
		var popup = $("#popID");
		$.ajax({
			url: "../"+ standar.val() + "/inform/class_gerenc_callce.php",
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
		console.log( "Error Fuction showDetail: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function PlayAudioCall(num_despac, num_consec, standa)
{
	try
	{
		PopUpJuery("open");
		$.ajax({
			url: "../"+ standa +"/inform/inf_indica_callce.php",
			data: "Ajax=on&opcion=2&num_despac="+num_despac+"&num_consec="+num_consec,
			type: "post",
			success: function(data){
				$("#DialogCallID").html( data );
			}
		});
	}
	catch(e)
	{
		console.log( "Error Fuction PlayAudioCall: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function PopUpJuery(option)
{
	try
	{
		if(option == "open")
		{
			$("<div id=\'DialogCallID\'><center>Cargando Audio...<br>Por favor espere</center></div>").dialog({
				modal: true,
				resizable: false,
				draggable: false,
				closeOnEscape: false,
				width: "324px",
				height: "100px",
				title: "Reproductor de llamadas",
				open: function(){
					$("#DialogCallID").css({height: "60px"});
				},
				close: function(){
					PopUpJuery("close")
				}
			})
		}
		else
		{
			$("#DialogCallID").dialog("destroy").remove();
		}
	}
	catch(e)
	{
		console.log( "Error Fuction PopUpJuery: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}