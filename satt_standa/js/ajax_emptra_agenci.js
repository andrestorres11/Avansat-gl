$(document).ready(function(){
	var standa = $("#standaID").val();
	var attributes  = '&Ajax=on&standa='+standa;
	$("#nom_transpID").autocomplete({
		source: "../"+ standa +"/transp/ajax_transp_transp.php?Option=buscarTransportadora"+ attributes,
		minLength: 3,		
		select: function( event, ui ) {
			$("#cod_tercerID").val( ui.item.id );
			$("body").removeAttr("class");
		}
	});
	$("#ciudadID").autocomplete({
		source: "../"+ standa +"/transp/ajax_transp_transp.php?Option=getCiudades"+ attributes,
		minLength: 3,
		select: function( event, ui ) {
			$("#cod_ciudadID").val( ui.item.id );
		}
	});
});

function editarAgencia(tipo, objeto){
	console.log(objeto); 
	var DLRow = $( objeto ).parent().parent();
	var cod_agenci = DLRow.find("input[id^=cod_agenci]").val();
	var nom_agenci = DLRow.find("input[id^=nom_agenci]").val();
	$("#cod_agenciID").val(cod_agenci);
	$("#nom_agenciID").val(nom_agenci);
	if(tipo == 1){
		confirmar('activar');
	}else if(tipo == 2){
		confirmar('inactivar');
	}else{
		LoadPopupJQNoButton( 'open', 'Confirmar Operación', 'auto', 'auto', false, false, true );
		var popup = $("#popID");
		var msj = "<div style='text-align:center'>¿Está seguro de <b>editar</b> la Agencia: <b>" +nom_agenci+ "?</b><br><br><br><br>";
			msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='formulario()' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
			msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";
			
			popup.parent().children().children('.ui-dialog-titlebar-close').hide();
			popup.append(msj);// //lanza el popUp
	}
}

function confirmar(operacion){

	LoadPopupJQNoButton( 'open', 'Confirmar Operación', 'auto', 'auto', false, false, true );
	var popup = $("#popID");
	var agencia = $("#nom_agenciID").val();
 	var onclick = "onclick='registrar(\"";
 		onclick+=operacion;
 		onclick+="\")'";
	var msj = "<div style='text-align:center'>¿Está seguro de <b>"+operacion+"</b> la Agencia: <b>" +agencia+ "?</b><br><br><br><br>";
	msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' "+onclick+" class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
	msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";
	
	popup.parent().children().children('.ui-dialog-titlebar-close').hide();
	       
	popup.append(msj);// //lanza el popUp
}

/******************************************************************************
 *	\fn: registrar												  			  *
 *	\brief: funcion para registros nuevos y modificaciones de agencias        *
 *		  recibe un string con la operación a realizar registrar o modificar  *
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
	var standa = $("#standaID").val();
	if(val == true){
		if(operacion == 'modificar' || operacion == 'registrar'){
			var trans = $("#cod_tercerID").val();
			var ciudad = $("#cod_ciudadID").val();
			if(trans == '' || trans == null ){
				inc_alerta("nom_transpID", "Por favor ingrese una transportadora válida.");
				val = false;
			}
			if(ciudad == '' || ciudad == null ){
				inc_alerta("ciudadID", "Por favor ingrese una ciudad válida.");
				val = false;
			}
		}
	}
	if(val == true){
			//crea el popUp para el mensaje de  respuesta del guardado  
			LoadPopupJQNoButton( 'open', 'Resultado de la Operación', 'auto', 'auto', false, false, true );
			var popup = $("#popID");

			var parametros = "Option="+operacion+"&Ajax=on&";
			parametros += getDataForm(); //agrega los datos consignados en el formulario
			$.ajax({
			   url: "../"+ standa +"/agenci/ajax_emptra_agenci.php",
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



function cancelar(){
	LoadPopupJQNoButton('close');
  	$("#opcionID").val("");
  	$("#cod_servicID").val("");
  	$("#form_transporID").submit();
}
	
function confirmado(){
	LoadPopupJQNoButton('close');
	$("#opcionID").val("");
	document.form_transpor.submit();
}