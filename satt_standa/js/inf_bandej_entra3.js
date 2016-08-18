$(function() {
	$( "#tabs" ).tabs({
		beforeLoad: function( event, ui ) {
			ui.jqXHR.fail(function() {
				ui.panel.html( "Cargado..." );
			});
		}
	});

	$("#cod_usuariID").multiselect().multiselectfilter(); 
	$("#cod_transpID").multiselect().multiselectfilter(); 

	$("#liGenera").click(function(){
		verifiData();
		generalReport( "infoGeneral", "tabs-1" );
	});

	$("#liCargue").click(function(){
		generalReport( "infoCargue", "tabs-2" );
	});

	$("#liTransi").click(function(){
		generalReport( "infoTransito", "tabs-3" );
	});

	$("#liDescar").click(function(){
		generalReport( "infoDescargue", "tabs-4" );
	});
});

$("body").ready(function(){
	$("#cod_transpID option[value="+ $("#sel_transpID").val() +"]").attr("selected", "selected");
	$("#cod_usuariID option[value="+ $("#sel_usuariID").val() +"]").attr("selected", "selected");

	$("input[type=text]").each(function(i,o){
		$(this).blur(function(){
			showDetailSearch( $(this) );
		});
	});

	$("#accordionID").accordion({
		heightStyle: "content",
		collapsible: true
	});

	$("#tableID").css('height', ( $(window).height() - 166 ) );
	$("#tableID").css('overflow', 'scroll');
});

function verifiData()
{
	try
	{
		var ind_filact = $("#ind_filactID");
		var attributes = getParameFilter();

		if( attributes == '' ){
			ind_filact.val('');
			return false;
		}else{
			ind_filact.val('1');
		}
	}
	catch(e)
	{
		console.log( "Error Fuction verifiData: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function getParameFilter()
{
	try
	{
		var box_checke = $("input[type=checkbox]:checked");
		var rad_checke = $("input[type=radio]:checked");
		var cod_transp = '""';
		var cod_usuari = '""';
		var attributes = '';

		box_checke.each(function(i,o){
			if( $(this).attr("name") == 'multiselect_cod_transpID' )
				cod_transp += ',"'+ $(this).val() +'"';
			else if( $(this).attr("name") == 'multiselect_cod_usuariID' )
				cod_usuari += ',"'+ $(this).val() +'"';
			else{
				attributes += '&'+ $(this).attr("name");
				attributes += '='+ $(this).val();
			}
		});

		rad_checke.each(function(i,o){
			attributes += '&'+ $(this).attr("name");
			attributes += '='+ $(this).val();
		});

		if( cod_transp != '""' && cod_transp != '"",""' ){
			attributes += '&cod_transp='+ cod_transp;
		}

		if( cod_usuari != '""' && cod_usuari != '"",""' ){
			attributes += '&cod_usuari='+ cod_usuari;
		}

		return attributes;
	}
	catch(e)
	{
		console.log( "Error Fuction getParameFilter: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function showDetailSearch( obj )
{
	try
	{
		if( obj.val() == '' )
			return false;

		var pop = $(".ui-dialog").length;
		if( pop > 0 )
			return false;

		var Standar   = $("#standaID");
		var attributes  = 'Ajax=on&Option=detailSearch&standa='+Standar.val();
			attributes += '&window='+ $("#windowID").val();
			attributes += '&ind_entran='+ $("#ind_entranID:checked").val();
			attributes += '&ind_fintra='+ $("#ind_fintraID:checked").val();
			attributes += '&'+ obj.attr("name") +'='+ obj.val();

		//Load PopUp
		LoadPopupJQ( 'open', 'Resultados de la Busqueda', ( $(window).height() - 50 ), ( $(window).width() - 50 ), false, false, true );
		var popup = $("#popID");
		$.ajax({
			url: "../"+ Standar.val() + "/inform/class_despac_trans3.php",
			type: "POST", 
			data: attributes,
			async: false,
			beforeSend: function(){
				popup.parent().children().children('.ui-dialog-titlebar-close').hide();
				popup.html("<center><img src=\"../"+Standar.val()+"/imagenes/ajax-loader.gif\"></center>");
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
		console.log( "Error Fuction showDetailSearch: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function showDetailBand( ind_filtro, ind_etapax, cod_transp )
{
	try
	{
		var pop = $(".ui-dialog").length;

		if( pop > 0 )
			return false;

		var Standar   = $("#standaID");
		var attributes  = 'Ajax=on&Option=detailBand&standa='+Standar.val();
			attributes += '&window='+ $("#windowID").val();
			attributes += '&ind_filact='+ $("#ind_filactID").val();
			attributes += getParameFilter();
			attributes += '&ind_filtro='+ ind_filtro;
			attributes += '&ind_etapax='+ ind_etapax;
			attributes += '&cod_transp='+ cod_transp;
		
		//Load PopUp
		LoadPopupJQ( 'open', 'Detalle Bandeja', ( $(window).height() - 50 ), ( $(window).width() - 50 ), false, false, true );
		var popup = $("#popID");
		$.ajax({
			url: "../"+ Standar.val() + "/inform/class_despac_trans3.php",
			type: "POST", 
			data: attributes,
			async: false,
			beforeSend: function(){
				popup.parent().children().children('.ui-dialog-titlebar-close').hide();
				popup.html("<center><img src=\"../"+Standar.val()+"/imagenes/ajax-loader.gif\"></center>");
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
		console.log( "Error Fuction showDetailBand: "+e.message+"\nLine: "+e.lineNumber );
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

function generalReport( ind_etapax, id_div )
{
	try
	{
		var standar = $("#standaID");
		var pop = $(".ui-dialog").length;

		if( pop > 0 )
			return false;
			//LoadPopupJQ('close');

		//Load PopUp
		LoadPopupJQ( 'open', 'Cargando...', 'auto', 'auto', false, false, false );
		var popup = $("#popID");

		//Atributos del Ajax
		var atributes  = 'Ajax=on&Option='+ind_etapax;
		    atributes += '&standa='+standar.val();
		    atributes += '&ind_filact='+$("#ind_filactID").val();
		    atributes += '&sel_transp='+$("#sel_transpID").val();
		    atributes += '&sel_usuari='+$("#sel_usuariID").val();
		    atributes += getParameFilter();
		
		//Ajax
		$.ajax({
			url: "../"+ standar.val() + "/inform/class_despac_trans3.php",
			type: "POST", 
			data: atributes,
			async: true,
			beforeSend: function(){
				popup.parent().children().children('.ui-dialog-titlebar-close').hide();
				popup.html("<center><img src=\"../"+standar.val()+"/imagenes/ajax-loader.gif\"></center>");
				$(".ui-dialog-buttonpane").remove(); // Quitar la zona de botones
				$(".ui-dialog").animate({ "left": ( $(window).width() - 135 ), "top": ( $(window).height() - 155 ) }, 2000 );
			},
			success: function(data){
				$("#"+id_div).html(data);
				$("#"+id_div).css("height", ( $(window).height() - 166 ) );
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