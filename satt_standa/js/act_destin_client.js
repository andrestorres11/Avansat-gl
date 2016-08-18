function editar()
{
	try
	{    	
		var fRow = document.getElementById( 'ActualRowID' ).value;
    	var tercer = document.getElementById( "DLLink"+fRow+"-0" ).innerHTML;
    	document.getElementById( 'cod_client' ).value = tercer;
    	document.getElementById( 'opcionID' ).value = 1;
		formulario = document.formData;
		formulario.submit();
	}
	catch(e)
	{
		console.log( "Error Fuction Actualizar: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function actualizar()
{

	var cod_cliente = $("input[name='cod_client']");

try{

		var nom_cliente = $("input[name='nom_client']").val();
		var flag = true;
		var message = "Revise Los siguientes campos\n"

		if(cod_cliente.val() == '' || cod_cliente.val() == null){
			message += "-Codigo\n";
			flag = false;
		}

		if(nom_cliente == '' || nom_cliente == null){
			message += "-Nombre\n";
			flag = false;
		}

		if(flag == false){
			alert(message);
		}

		else{

			if(confirm("Desea ingresar el Cliente: " + nom_cliente)){
				$("#opcionID").val(2);
				formulario = document.form_item;
	            formulario.submit();
			}
			
		}

	}
catch(e)
{
	console.log( "Error Fuction actualizar: "+e.message+"\nLine: "+e.lineNumber );
	return false;
}
}
