function eliminar()
{
		
	try
	{    	
		var fRow = document.getElementById( 'ActualRowID' ).value;
    	var tercer = document.getElementById( "DLLink"+fRow+"-0" ).innerHTML;
    	document.getElementById( 'cod_client' ).value = tercer;
    	document.getElementById( 'opcionID' ).value = 2;
		formulario = document.formData;
		if(confirm("desea eliminar el cliente " + tercer)){
			formulario.submit();
		}
	}
	catch(e)
	{
		console.log( "Error Fuction Actualizar: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}