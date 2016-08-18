function aceptar(formulario)
{
	var correo = /^(.+\@.+\..+)$/

	if(formulario.nitemp.value == 0)
    {
     window.alert("El Numero de NIT de la Empresa es Obligatorio.");
     formulario.nitemp.focus();
    }
    else if(formulario.razsoc.value == 0)
    {
     window.alert("La Razon Social de la Empresa es Obligatoria.");
     formulario.razsoc.focus();
    }
    else if(formulario.telpri.value == 0)
    {
     window.alert("El Numero de Telefono de la Empresa es Obligatorio.");
     formulario.telpri.focus();
    }
    else if(formulario.dirpri.value == 0)
    {
     window.alert("La Direccion de la Empresa es Obligatoria.");
     formulario.dirpri.focus();
    }
    else if(formulario.dirmai.value == 0)
    {
     window.alert("El Correo Electronico de Contacto es Obligatorio.");
     formulario.dirmai.focus();
    }
    else if (!correo.test(formulario.dirmai.value))
    {
     window.alert("El Correo Electronico Debe Estar Definido de la Forma\nxxxxxxx@xxxxx.xxx.xx.");
     formulario.dirmai.focus();
    }
    else if(formulario.contac.value == 0)
    {
     window.alert("El Contacto de la Empresa es Obligatorio.");
     formulario.contac.focus();
    }
	else
	{
     if(confirm("Desea Actualizar La configuracion de la Empresa?"))
	 {
	  formulario.opcion.value = 2;
	  formulario.submit();
     }
    }
}