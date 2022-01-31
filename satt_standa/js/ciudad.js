 function RegistrarCiudad()
 {
 	try
	{
		var formulario = document.formulario;
		
		if( !formulario.cod_depart.value )
		{
			alert( "El Departamento es Requerido." );
			return formulario.cod_depart.focus();
		}
		
		if( !formulario.nom_ciudad.value )
		{
			alert( "El Nombre de la Ciudad es Requerido." );
			return formulario.nom_ciudad.focus();
		}
		
		if( !formulario.abr_ciudad.value )
		{
			alert( "La Abreviatura de la Ciudad es Requerida." );
			return formulario.abr_ciudad.focus();
		}
				
		if( confirm( "Esta Seguro de Registrar la Ciudad?" ) )
		{
			formulario.submit();
		}
	}
	catch( e )
	{
		alerrt( "Error " + e.message );
	}
 }
