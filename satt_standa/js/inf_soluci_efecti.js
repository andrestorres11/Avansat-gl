$("document").ready(function () {
var dir_aplica = $("#standaID").val();

		$("#fec_iniciaID").datepicker();
		$("#fec_finaliID").datepicker();
		$("#btnPrincipal").click(function(){
			$("#opcionID").val(99);
	})
 
	$("#TransportadoraID").click(function(){
		$("#cod_transpID").val("");
	});
	$("#nom_modaliID").click(function(){
		$("#cod_modaliID").val("");
	});

	    /*!
	*  \brief: autocompleta el campo de la transportadora
	*  \author: Ing. Miguel Romero "mamafoka" 
	*  \date: 30/06/2015
	*  \date modified: dia/mes/año 
	*/

	       
	$("#TransportadoraID").autocomplete({
	  source:"../"+dir_aplica+"/inform/ajax_despac_limpio.php?option=getTransportadoras",
	  minLength: 2,
	  select: function(event, ui){
	    nom_transp = ui.item.label;
	    arr_transp = nom_transp.split("-") ;
	    $("#cod_transpID").val(arr_transp[0]);
	  }
	});
	        

	/*!
	*  \brief: autocompleta el campo de la Modalidad
	*  \author: Ing. Miguel Romero "mamafoka" 
	*  \date: 30/06/2015
	*  \date modified: dia/mes/año 
	*/

	$("#nom_modaliID").autocomplete({
	  source:"../"+dir_aplica+"/inform/ajax_despac_limpio.php?option=getModalidades",
	  minLength: 2,
	  select: function(event, ui){
	    nom_modali = ui.item.label;
	    arr_modali = nom_modali.split("-") ;
	    $("#cod_modaliID").val(arr_modali[0]);
	  }
	});
 
});

function send(){

	var fechaInicial = $("#fec_iniciaID").val();
	var fechaFinal = $("#fec_finaliID").val();
	var transportadora = $("#cod_transpID").val();

	var message = 'Debe llenar los siguientes campos:\n';
	var flag = true;
	if (fechaInicial == '') {
		message += "-fecha inicial\n";
		flag = false;
	};	
	if (fechaFinal == '') {
		message += "-fecha final\n";
		flag = false;
	};	
	if (transportadora == '') {
		message += "-transportadora\n";
		flag = false;
	};

	if(flag == false){

		alert(message);
	}else{

		form.submit()
	}

}


function detalleDespachos (index){

	var dir_aplica = $("#standaID").val();

	var fec_inicia = "";
	var fec_finali = "";
		cod_transp = $("#cod_transpID").val();
	var parame = "";

	$("input[type=hidden]").each(function(){
		parame += "&"+$(this).attr("name")+"="+$(this).val();	
	});

	if(index == "total"){

		fec_inicia = $("#fec_iniciaID").val();
		fec_finali = $("#fec_finaliID").val();

	}else{
		fec_inicia = $("#consec"+index).val();
		fec_finali = $("#consec"+index).val(); 
	}

	$("<div id='popUpLoaded'></div>").dialog({
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
		},
		buttons:{
			cerrar: function(){
				$("#popUpLoaded").dialog("destroy").remove();
			}
		}
	});

	      parame += "&fec_inicia="+fec_inicia+"&fec_finali="+fec_finali;
		  parame += "&cod_transp="+cod_transp;  

	$.ajax({
	      url: '../'+dir_aplica+'/inform/ajax_soluci_efecti.php?option=getDetailDespac'+parame,
	      type:'POST', 
	      beforeSend: function(){	 
	    	$("#popUpLoaded").html( '<center><br><br><br><br><br><img src="../'+dir_aplica+'/imagenes/ajax-loader.gif"></center>' );
	      }, 
	      success : function (data){ 
	           $("#popUpLoaded").html(data); 

	        }
	      }) 
}
function exportExcel(){
	var Standa = $("#standaID").val(); 
 
	    window.location = "../" + Standa + "/inform/ajax_soluci_efecti.php?option=exportExcel";
 
}

