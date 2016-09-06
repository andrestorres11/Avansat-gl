function busq_serv()
{
	validacion = true;
	formulario = document.form_ins;

	if(formulario.placa.value == "" && formulario.cod_manifi.value == "")
	{
		window.alert("Debe Digitar el numero de la placa o el numero de manifiesto.");
		validacion = false;
		return formulario.placa.focus();
	}
	else 
	{
		formulario.opcion.value = 3;
		formulario.submit();
	}
}

function aceptar_ins(formulario)
{
	validacion = true;
	formulario = document.form_ins;
	var fec = new Date();
	var date = document.getElementById("date");
	var hora = document.getElementById("hora");
	var obs = document.getElementById("obsID");
	var nov_especi = document.getElementById("nov_especiID");
	var ano= (date.value).substr(0,4);
	var mes= +(date.value).substr(5,2)-1;
	var dia= (date.value).substr(8,2);
	var hor= (hora.value).substr(0,2);
	var min= (hora.value).substr(3,2);
	var fecnov= new Date(ano,mes,dia,hor,min,"00");

	if(obs.value == ""){
		window.alert("La Observacion es Requerida")
		validacion = false
		return obs.focus();
	}else if(obs.value.length < 9 ) {
		window.alert("La Observacion Requiere minimo 9 caracteres")
		validacion = false
		return obs.focus();
	}

	if(date.value==""){
		window.alert("La Fecha de la Novedad es Requerida")
		validacion = false
		return date.focus();
	}
	if(hora.value==""){
		window.alert("La Hora de la Novedad es Requerida")
		validacion = false
		return hora.focus();
	}

	if ( (date.value+' '+hora.value) > document.getElementById('dateSystemID').value)
	{
		alert ("La Fecha y Hora de la  Novedad no Puede ser Mayor a la Fecha Actual");
		return date.focus();
	}
	if (formulario.contro.value == 0) 
	{
		window.alert("El Puesto de Control es Requerido")
		validacion = false
		formulario.contro.focus()
	}
	if (formulario.novedad.value == 0) 
	{
		window.alert("La Novedad es Requerida")
		validacion = false
		formulario.novedad.focus()
	}
	if( formulario.novedad.value == '6' )
	{
		var d = formulario.fecnov.value.split("-");
		var fecact = d[1] +'/'+ d[2] +'/'+ d[0] +' '+ formulario.hornov.value;
		var fecact = new Date(fecact);

		d = formulario.fecha.value.split("-");
		var tiesol = d[1] +'/'+ d[2] +'/'+ d[0] +' '+ formulario.hor.value +':00';
		var tiesol = new Date(tiesol);

		var diff = Math.floor((tiesol-fecact)/60000);

		if( diff > 720 ){
			alert("El Tiempo Solicitado no debe ser mayor a 12 horas.");
			return false;
		}
	}
	
	if (formulario.soltie.value == 1) 
	{
		var date = document.getElementById("fecha");
		var hora = document.getElementById("hor");
		var ano= (date.value).substr(0,4);
		var mes= +(date.value).substr(5,2)-1;
		var dia= (date.value).substr(8,2);
		var hor= (hora.value).substr(0,2);
		var min= (hora.value).substr(3,2);
		var fecnov= new Date(ano,mes,dia,hor,min,"00");
		if(date.value==""){
			window.alert("La Fecha del Tiempo Adicional es Requerida")
			validacion = false
			return date.focus();
		}
		if(hora.value==""){
			window.alert("La Hora del Tiempo Adicional es Requerida")
			validacion = false
			return hora.focus();
		}
		if (date.value < document.getElementById('dateSystemID').value) 
		{
			alert ("La Fecha y Hora Solicitado no Puede ser Menor a la Fecha Actual");
			return date.focus();
		}
	}
	if (nov_especi.value==1)
	{
		if (obs.value=="")
		{
			alert ("La Observacion es Requerida");
			return obs.focus();
		}
	}
	if(validacion=true){
		if (confirm("Desea Ingresar la Novedad al Sistema.")) 
		{
			formulario.opcion.value = 2;
			formulario.submit();
		}
	}else{
	}
}

function validateNoveda()
{
	try{
		if( $("#novedadID").val() == '6' ){
			LoadPopupJQ( 'open', 'PROTOCOLO DE PERNOCTACION EAL', 'auto', 'auto', false, false, false );
			$("#popID").html( builderHtml() );
		}
	}
	catch(e)
	{
		console.log( "Error Function validateNoveda: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function builderHtml()
{
	try{
		var a = "";

		a += "";
		a += "<style>";
		a += 	".cellHead {";
		a += 		"background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #00660f 0%, #00660f 100%) repeat scroll 0 0;";
		a += 		"color: #fff;";
		a += 		"padding: 5px 10px;";
		a += 		"text-align: center;";
		a += 	"}";
		a += 	".cellInfo1 {";
		a += 		"background-color: #ebf8e2;";
		a += 		"font-family: Trebuchet MS,Verdana,Arial;";
		a += 		"font-size: 11px;";
		a += 		"padding: 2px;";
		a += 	"}";
		a += 	".Style2DIV {";
		a += 		"background-color: rgb(240, 240, 240);";
		a += 		"border: 1px solid rgb(201, 201, 201);";
		a += 		"border-radius: 5px;";
		a += 		"min-height: 50px;";
		a += 		"padding: 5px;";
		a += 		"width: 99%;";
		a += 	"}";
		a += "</style>";

		a += "<div class='Style2DIV'>";
		a += 	"<table width='100%' cellspacing='0' cellpadding='0'>";
		a += 		"<tr>";
		a += 			"<th class='CellHead' colspan='2'>&nbsp;</th>";
		a += 		"</tr>";
		a += 		"<tr>";
		a += 			"<td class='cellInfo1' align='right'>* Parqueadero:</td>";
		a += 			"<td class='cellInfo1' align='left'><input type='text' name='nom_parque' id='nom_parqueID' /></td>";
		a += 		"</tr>";
		a += 		"<tr>";
		a += 			"<td class='cellInfo1' align='right'>Hotel:</td>";
		a += 			"<td class='cellInfo1' align='left'><input type='text' name='nom_hotelx' id='nom_hotelxID' /></td>";
		a += 		"</tr>";
		a += 		"<tr>";
		a += 			"<td class='cellInfo1' align='right'>* Hora de reinicio:</td>";
		a += 			"<td class='cellInfo1' align='left'><input type='text' name='hor_reinic' id='hor_reinicID' /></td>";
		a += 		"</tr>";
		a += 	"</table>";
		a +=	"<input type='button' value='Aceptar' onClick='validateProtocolo()' />";
		a += "</div>";

		return a;
	}
	catch(e)
	{
		console.log( "Error Function builderHtml: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function validateProtocolo()
{
	try{
		var parque = $("#nom_parqueID");
		var hotelx = $("#nom_hotelxID");
		var reinic = $("#hor_reinicID");
		var txt = '';

		if( parque.val() == '' ){
			alert("El nombre del parqueadero es obligatorio.");
			parque.focus();
			return false;
		}else if( reinic.val() == '' ){
			alert("La hora de reinicio es obligatoria.");
			reinic.focus();
			return false;
		}else{
			txt += 'EL CONDUCTOR REPORTA PERNOCTACION EN LA EAL PARQUEDERO '+ parque.val().toUpperCase() +'. ';
		}

		if( hotelx.val() != '' )
			txt += 'HOTEL '+ hotelx.val().toUpperCase() +'. ';

		txt += 'HORA DE REINICIO '+ reinic.val().toUpperCase() +'. ';

		LoadPopupJQ('close');
		$("#obsID").val(txt);
		$("#form_insID").submit();
	}
	catch(e)
	{
		console.log( "Error Function validateProtocolo: "+e.message+"\nLine: "+e.lineNumber );
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