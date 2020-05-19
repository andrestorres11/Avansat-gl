function aceptar_ins(formulario)
{
    validacion = true
    formulario = document.form_ins
    if(formulario.ruta.value == "")
    {
     window.alert("La Ruta es Requerida")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 1;
    formulario.submit();
    }
}

function aceptar_ins2(formulario)
{
    validacion = true
    formulario = document.form_ins

    if(formulario.transpor.value == 0)
    {
     window.alert("La Transportadora es Requerida")
     validacion = false
    }
    else
    {
     if(confirm("Desea Asignar la Transportadora Seleccionada a la Ruta.?"))
     {
      formulario.opcion.value= 3;
      formulario.submit();
     }
    }
}

function ruttra()
{
    formulario = document.form_ins

    if(confirm('Esta seguro que desea eliminar la ruta?'))
    	{
	    formulario.opcion.value= 3;
	    formulario.submit();
        }
}
function aceptar_lis(formulario)
{
    validacion = true
    formulario = document.form_list
    if(formulario.ruta.value == "")
    {
     window.alert("La Ruta es Requerida")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 2;
    formulario.submit();
    }
}

function aceptar_act(formulario)
{
    validacion = true
    formulario = document.form_act
    if(formulario.ruta.value == "")
    {
     window.alert("La Ruta es Requerida")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 1;
    formulario.submit();
    }
}

function aceptar_eli(formulario)
{
    validacion = true
    formulario = document.form_item
    if(confirm("Esta Seguro de Desasignar la Transportadora a la Ruta.?"))
    {
     formulario.opcion.value= 3;
     formulario.submit();
    }
}
function aceptar_act2(formulario)
{
    validacion = true
    formulario = document.form_item
    if(formulario.nombre.value == '')
    {
     window.alert("El Nombre la Rutas es Requerido")
     validacion = false
    }
    else if(formulario.origen.value == formulario.destino.value)
    {
     window.alert("El Origen Debe ser Diferente al Destino")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 3;
    formulario.submit();
    }
}

function ActualizarRuta()
{
	try
	{
		var formulario = document.getElementById( "formularioID" ); 
		
		if( confirm( "Esta Seguro de Actualizar la Ruta ?" ) )
		{
			formulario.submit();
		}
	}
	catch( e )
	{
		alert( "Error " + e.message );
	}
}
