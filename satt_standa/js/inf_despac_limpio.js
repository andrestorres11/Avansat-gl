function detalleDespachos(fila){
 
	var Standa = $("#standaID").val(); 
	var fec_inicia = $("#consec"+fila).val();
	var fec_finali = $("#fec_finali").val();
	var cod_transp = $("#cod_transp").val();
	var parame = "";
	parame += "&num_dessat="+$("#num_dessatID").val()+"&num_placa="+$("#num_placaID").val()+"&cod_modali="+$("#cod_modaliID").val();
	parame += "&num_viajex="+$("#num_viajexID").val()+"&fec_finali="+fec_inicia;
	$( "<div id='popInfoDetallDespac'></div>" ).dialog({

		modal : true,
		resizable : false,
		draggable: false,
		title: "Detalles",
		width: $(document).width() - 400,
		height: $(document).height(),
		position:['middle',25], 
		bgiframe: true,
		closeOnEscape: false,
		show : { effect: "drop", duration: 300 },
		hide : { effect: "drop", duration: 300 },
		open: function(event, ui) { 
			$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
			$('popInfoDetallDespac').css('overflow-y','true');
			$('.ui-dialog').css('height',$(document).height()); 
		},
		buttons:  {  
                  Aceptar : function(){
                    $(this).dialog('destroy').remove();
					}
				}

		});

 	$.ajax({
	  url: "../" + Standa + "/inform/ajax_despac_limpio.php?option=getDetailDespac&fec_inicia="+fec_inicia+"&cod_transp="+cod_transp+parame,
	type: "POST",
    async: true,
    beforeSend : function ( data ){
    	$("#popInfoDetallDespac").html( "<center><br><br><br><br><br><img src=\"../"+Standa+"/imagenes/ajax-loader.gif\"></center>" );
    }, 
    success: function( data )
    { 
      $("#popInfoDetallDespac").html( data );
    }

	});

}
function detalleDespachosTotal(){
 
	var Standa = $("#standaID").val(); 
	var fec_inicia = $("#fec_iniciaID").val();
	 console.log(fec_inicia); 
	var cod_transp = $("#cod_transp").val();
	var parame = "";
	parame += "&num_dessat="+$("#num_dessatID").val()+"&num_placa="+$("#num_placaID").val()+"&cod_modali="+$("#cod_modaliID").val();
	parame += "&num_viajex="+$("#num_viajexID").val()+"&fec_finali="+$("#fec_finaliID").val();
	$( "<div id='popInfoDetallDespac'></div>" ).dialog({

		modal : true,
		resizable : false,
		draggable: false,
		title: "Detalles",
		width: $(document).width() - 400,
		height: $(document).height(),
		position:['middle',25], 
		bgiframe: true,
		closeOnEscape: false,
		show : { effect: "drop", duration: 300 },
		hide : { effect: "drop", duration: 300 },
		open: function(event, ui) { 
			$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
			$('popInfoDetallDespac').css('overflow-y','true');
			$('.ui-dialog').css('height',$(document).height()); 
		},
		buttons:  {  
                  Aceptar : function(){
                    $(this).dialog('destroy').remove();
					}
				}

		});

 	$.ajax({
	  url: "../" + Standa + "/inform/ajax_despac_limpio.php?option=getDetailDespac&fec_inicia="+fec_inicia+"&cod_transp="+cod_transp+parame,
	type: "POST",
    async: true,
    beforeSend : function ( data ){
    	$("#popInfoDetallDespac").html( "<center><br><br><br><br><br><img src=\"../"+Standa+"/imagenes/ajax-loader.gif\"></center>" );
    }, 
    success: function( data )
    { 
      $("#popInfoDetallDespac").html( data );
    }

	});

}

function exportExcel(){
	var Standa = $("#standaID").val(); 
 
	    window.location = "../" + Standa + "/inform/ajax_despac_limpio.php?option=exportExcel";
 
}

$( document ).ready(function() {
	$("#TransportadoraID").click(function(){
		$("#cod_transpID").val("");
	});
	$("#nom_modaliID").click(function(){
		$("#cod_modaliID").val("");
	});
  
});
