function HomologarPuesto()
{	
	try
	{
		var puestos = document.getElementById( "puestos" );
		var formulario = document.getElementById( "formulario" );
		var opcion = document.getElementById( "opcion" );
		var puestos = document.getElementById( "puestos" );
		
		var paso = false;
		
		for( i = 0; i < Number( puestos.value ); i++ )
		{			
			var puesto = document.getElementById( "puesto_" + i );
			
			if( puesto )
			{  
				if( puesto.type == "checkbox" &&  puesto.checked || puesto.type == "hidden" && puesto.value )
				{
					paso = true;
				}
			}
		}
		
		if( !paso )
		{
			return alert( "Debe seleccionar por lo menos 1 puesto" );
		}
		
		if( confirm( "¿ Esta seguro de registrar la homologacion ?" ) )
		{
			opcion.value = "3";
			formulario.submit();
		}
	}
	catch( e )
	{
		alert( "Error " + e.message );
	}
}