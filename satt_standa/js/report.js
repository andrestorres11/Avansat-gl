 
 function Registrar()
 {
	try
	{
		var cod_tercer = document.getElementById( "cod_tercer" );
		var num_placax = document.getElementById( "num_placax" );
		var cod_contro = document.getElementById( "cod_contro" );
		var obs_repnov = document.getElementById( "obs_repnov" );
    var date = document.getElementById("date");
    var hora = document.getElementById("hora");
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
		
		if( !cod_tercer.value )
		{
			alert( "La Transportadora es Requerida." );
			return cod_tercer.focus();
		}
		
		if( !num_placax.value )
		{
			alert( "La Placa es Requerida." );
			return num_placax.focus();
		}
		
		if( !cod_contro.value )
		{
			alert( "El Pusto de Control es Requerido." );
			return cod_contro.focus();
		}
		
		if( !obs_repnov.value )
		{
			alert( "La Observacion es Requerida." );
			return obs_repnov.focus();
		}
		
		if( confirm( "Esta Seguro de Registrar la Novedad?" ) )
		{
			formulario.submit();
		}
	}
	catch( e )
	{
		alert( "Error " + e.message );
	}
 }