function validar_form(formulario)
{
	//var tray     = /^[a-zA-Z0-9]{6}/
	var tray = /[a-zA-Z]{1}[0-9]{5}/
	
	var vpeso = /^[0-9]+\.?[0-9]*$/
	var num = /[0-9]{4}/
	
	
	formulario = document.form_trayler
		
	if (formulario.transp.value == '0') 
	{
		window.alert("La Transportadora es Requerida")
		validacion = false
		formulario.transp.focus()
	}
	else if (formulario.trayler.value == "") 
	{
		window.alert("El Numero del Remolque es Requerido")
		validacion = false
		formulario.trayler.focus()
	}
	else if (!tray.test(formulario.trayler.value)) 
	{
		window.alert("El Numero del Remolque Debe Iniciar con una Letra, Seguido de Cincon Numeros")
		validacion = false
		formulario.trayler.focus()
	}
	else if (formulario.marca.value == '0') 
	{
		window.alert("La Marca es Requerida")
		validacion = false
		formulario.marca.focus()
	}
	else if (formulario.propie.value == '0') 
	{
		window.alert("El Propietario es Requerido")
		validacion = false
		formulario.propie.focus()
	}
	else 
	{
		if (confirm('Esta Seguro que Desea Insertar el Trayler?')) 
		{
			formulario.opcion.value = 2;
			formulario.submit();
		}
	}
	
}

function Eliminar_form(formulario)
{
	formulario = document.form_trayler
	if (confirm('Esta Seguro que Desea Eliminar el Trayler?')) 
	{
		formulario.opcion.value = 2;
		formulario.submit();
	}
	
}

function volver(formulario)
{
	formulario = document.form_trayle
	formulario.opcion.value = 5;
	formulario.submit();
}

function actualizar_form(formulario)
{

	var tray = /^[a-zA-Z0-9]{6}/
	var vpeso = /^[0-9]+\.?[0-9]*$/
	var num = /[0-9]{4}/
	
	
	formulario = document.form_trayler
	
	if (formulario.trayler.value == "" || !tray.test(formulario.trayler.value)) 
	{
		window.alert("El Numero de Trayler es Requerido y no debe tener ms que Numeros o Letras.")
		validacion = false
		formulario.trayler.focus()
	}
	else if (formulario.marca.value == '0') 
	{
		window.alert("La Marca es Requerida")
		validacion = false
		formulario.marca.focus()
	}
	
	else if (formulario.propie.value == '0') 
	{
		window.alert("El Propietario es Requerido")
		validacion = false
		formulario.propie.focus()
	}
	else 
	{
		if (confirm('Esta Seguro que Desea Actualizar el Trayler?')) 
		{
			formulario.opcion.value = 3;
			formulario.submit();
		}
	}
	
}
