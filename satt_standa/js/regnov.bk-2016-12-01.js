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