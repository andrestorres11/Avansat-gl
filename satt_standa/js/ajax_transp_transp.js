$(document).ready(function(){ 
	$("#datos").css("display", "none");
	$(".accordion").accordion({
		collapsible : true,          
		heightStyle : "content",
		icons: { "header" : "ui-icon-circle-arrow-e", "activeHeader": "ui-icon-circle-arrow-s" }
	}).click(function(){
		$("body").removeAttr("class");
	   });	

	$("input[type=button]").button();
	//Autocompletables
	var standa = $("#standaID").val();
	var attributes  = '&Ajax=on&standa='+standa;
	var boton = "";
	$("#nom_transpID").autocomplete({
		source: "../"+ standa +"/transp/ajax_transp_transp.php?Option=buscarTransportadora"+ attributes,
		minLength: 3,
		search: function( event, ui ) {
			$( "#boton").empty();	
			boton = "<input type='button' id='nuevo' value='Nuevo' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all' onclick='formulario();'>&nbsp;&nbsp;&nbsp;";
			// boton += "<input type='button' id='nuevo' value='Listado' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all' onclick='listado();'>";
			$( "#boton").append(boton);	
		},
		select: function( event, ui ) {
			boton = "<input type='button' id='editar' value='Ver/Editar' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all' onclick='formulario();'>&nbsp;&nbsp;&nbsp;";
			// boton += "<input type='button' id='nuevo' value='Listado' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all' onclick='listado();'>";
			$("#cod_tercerID").val( ui.item.id );
			$( "#boton").empty();		
			$( "#boton").append(boton);	
			$("body").removeAttr("class");
		}
	});
	// para buscar el pais para la transportadora
	$("#nom_paisxxID").autocomplete({
		source: "../" + standa + "/transp/ajax_transp_transp.php?Option=buscarPaises" + attributes,
		minLength: 3,
		select: function(event, ui) {
			boton = "<input type='button' id='nuevo' value='Listado' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all' onclick='mostrar();'>";
			$("#cod_paisxxID").val(ui.item.id);
			$("#boton").empty();
			$("#boton").append(boton);
			$("body").removeAttr("class");
		}
	});

	var paisxx = $("#cod_paisxxID").val();
	// para buscar la ciudad de la transportadora
	$("#ciudadID").autocomplete({
		source: "../"+ standa +"/transp/ajax_transp_transp.php?Option=getCiudades"+ attributes +"&cod_paisxx="+paisxx,
		minLength: 3,
		select: function( event, ui ) {
			$("#cod_ciudadID").val( ui.item.id );
		}
	});
	// para buscar la ciudad de la agencia
	
	$("#abr_ciudadID").autocomplete({
		source: "../"+ standa +"/transp/ajax_transp_transp.php?Option=getCiudades"+ attributes +"&cod_paisxx="+paisxx,
		minLength: 3,
		select: function( event, ui ) {
			$("#cod_ciudaaID").val( ui.item.id );
		}
	});	

	
});


/******************************************************************************
 *	\fn: registrar												  			  *
 *	\brief: funcion para registros nuevos y modificaciones de transportadoras *
 *		  recibe un string con la operaciÃ³n a realizar registrar o modificar  *
 *  \author: Ing. Alexander Correa 											  *
 *  \date: 31/08/2015														  *
 *  \date modified: 														  *
 *  \param: operacion: string con la operacion a realizar.					  *
 *  \param: 																  *
 *  \return popUp con el resultado de la operacion							  *
******************************************************************************/
function registrar(operacion){
	//cierra popUp si hay inicialiado
	LoadPopupJQNoButton('close');
	//valido los datos generales del formulario
	var val = validaciones();
	if(val == true){ 
		//si pasa la validaciÃ³n general valido los datos condicionales

		//certificaciones iso y basc
		var iso = $("#ind_cerisoID").attr("checked") ? true : false;
		var basc = $("#ind_cerbasID").attr("checked") ? true : false;
		var datos = [];
		if(iso && basc){ //si esta marcado iso y basc valido la fecha de vigencia de los mismos
			datos[0] = ['fec_cerisoID', 'date', 10, 10, true];
			datos[1] = ['fec_cerisoID', 'mayor'];
			datos[2] = ['fec_cerbasID', 'date', 10, 10, true];
			datos[3] = ['fec_cerbasID', 'mayor'];
		}else if(iso){ // si solo esta marcado iso
			datos[0] = ['fec_cerisoID', 'date', 10, 10, true];
			datos[1] = ['fec_cerisoID', 'mayor'];
		}else if(basc){ // si solo esta marcado basc
			datos[0] = ['fec_cerbasID', 'date', 10, 10, true];
			datos[1] = ['fec_cerbasID', 'mayor'];
		}else{

		}
		var standa = $("#standaID").val();
		var validar = inc_validar(datos);

		if (validar){
			//crea el popUp para el mensaje de  respuesta del guardado  
			LoadPopupJQNoButton( 'open', 'Resultado de la OperaciÃ³n', 'auto', 'auto', false, false, true );
			var popup = $("#popID");

			var parametros = "Option="+operacion+"&Ajax=on&";
			parametros += getDataForm(); //agrega los datos consignados en el formulario
			$.ajax({
			   url: "../"+ standa +"/transp/ajax_transp_transp.php",
	           type: "POST",
	           data: parametros,
	           async: false,
	           beforeSend: function(){
	           	popup.parent().children().children('.ui-dialog-titlebar-close').hide();
	           },
	           success: function(data){
	           		popup.append(data);// lanza el popUp
	           }
         	});
		}
		
	}
}

//funcion para mostrar la lista de las transportadoras
function mostrar() {
	$("#form3").empty();
	var paisxx = $("#cod_paisxxID").val();
	var standa = $("#standaID").val();
	var parametros = "Option=listaTransportadoras&Ajax=on&cod_paisxx=" + paisxx;
	$.ajax({
		url: "../" + standa + "/transp/ajax_transp_transp.php",
		type: "POST",
		data: parametros,
		async: false,

		success: function(data) {
			$("#sec2").css("height", "auto");
			$("#form3").append(data); // pinta los datos de la consulta					
		}
	});
	$("#datos").fadeIn(3000); // visualza los datos despues de pintarlos
}


function confirmado(){
	LoadPopupJQNoButton('close');
	$("#opcionID").val("");
	document.form_transpor.submit();
}



//funcion para resetear un formulario en el caso de registros nuevos
function borrar(){
	$("#form_transporID")[0].reset();
}

//funcion de confirmacion para la edicion, eliminacion e inactivacion de transportadoras
function confirmar(operacion){

	LoadPopupJQNoButton( 'open', 'Confirmar Operación', 'auto', 'auto', false, false, true );
	var popup = $("#popID");
	var transportadora = $("#nom_tercerID").val();
 	var onclick = "onclick='registrar(\"";
 		onclick+=operacion;
 		onclick+="\")'";
	var msj = "<div style='text-align:center'>¿Está seguro de <b>"+operacion+"</b> la transportadora: <b>" +transportadora+ "?</b><br><br><br><br>";
	msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' "+onclick+" class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
	msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";
	
	popup.parent().children().children('.ui-dialog-titlebar-close').hide();
	       
	popup.append(msj);// //lanza el popUp
}

//funcion para las opciones del dinamicList
function editarDistribuidora(tipo, objeto){

	var DLRow = $( objeto ).parent().parent();
	var cod_tercer = DLRow.find("input[id^=cod_transp]").val();
	var nom_transp = DLRow.find("input[id^=abr_tercer]").val();
	$("#cod_tercerID").val(cod_tercer);
	$("#nom_tercerID").val(nom_transp);
	// alert(cod_tercer+" - "+nom_transp);


	if(tipo == 1){
		confirmar('activar');
	}else if(tipo == 2){
		confirmar('inactivar');
	}else{
		LoadPopupJQNoButton( 'open', 'Confirmar Operación', 'auto', 'auto', false, false, true );
		var popup = $("#popID");
		var transportadora = $("#nom_tercerID").val();
		var msj = "<div style='text-align:center'>¿Está seguro de <b>editar</b> la transportadora: <b>" +transportadora+ "?</b><br><br><br><br>";
			msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='formulario()' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
			msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";
			
			popup.parent().children().children('.ui-dialog-titlebar-close').hide();
			popup.append(msj);// //lanza el popUp
	}
}

