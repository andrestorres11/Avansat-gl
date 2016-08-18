function aceptar_insert(formulario)
{
	var correo = /^(.+\@.+\..+)$/

    validacion = true
    formulario = document.form_agenci

    if(formulario.transp.value == 0)
    {
     window.alert("La Seleccion de la Transpotadora es Requerida.")
     formulario.transp.focus()
    }
    else if(formulario.nom.value == "")
    {
     window.alert("El Nombre de la Agencia es Requerido")
     formulario.nom.focus()
    }
    else if(formulario.ciudad.value== 0)
    {
     window.alert("La Ciudad es Requerida")
     formulario.ciudad.focus()
    }
    else if(formulario.dir.value== "")
    {
     window.alert("La Direccion es Requerida")
     formulario.dir.focus()
    }
    else if(formulario.tel.value == "")
    {
     window.alert("El Telefono es Requerido")
     formulario.tel.focus()
    }
    else if (formulario.mail.value != "" && !correo.test(formulario.mail.value))
    {
     window.alert("El Correo Electronico debe contener el siguiente formato:\n\n\t soporte@intrared.net \n\n\t soporte@intrared.com.co ")
     formulario.mail.focus()
    }
    else if(formulario.con.value == "")
    {
     window.alert("El Contacto es Requerido")
     formulario.con.focus()
    }
    else
    {
       if(confirm("Esta Seguro que Desea Ingresar la Agencia "+formulario.nom.value+"?"))
        {
         formulario.opcion.value= 2;
         formulario.submit();
        }
    }
}

function aceptar_actualizar(formulario)
{

    validacion = true
    formulario = document.form_agenci

    var correo = /^(.+\@.+\..+)$/

    if(formulario.nom.value == "")
    {
     window.alert("El Nombre de la Agencia es Requerido")
     formulario.nom.focus()
    }
    else if(formulario.ciudad.value== 0)
    {
     window.alert("La Ciudad es Requerida")
     formulario.ciudad.focus()
    }
    else if(formulario.dir.value== "")
    {
     window.alert("La Direccion es Requerida")
     formulario.dir.focus()
    }
    else if(formulario.tel.value == "")
    {
     window.alert("El Telefono es Requerido")
     formulario.tel.focus()
    }
    else if (formulario.mail.value != "" && !correo.test(formulario.mail.value))
    {
     window.alert("El Correo Electronico debe contener el siguiente formato:\n\n\t soporte@intrared.net \n\n\t soporte@intrared.com.co ")
     formulario.mail.focus()
    }
    else if(formulario.con.value == "")
    {
     window.alert("El Contacto es Requerido")
     formulario.con.focus()
    }
    else
    {
       if(confirm("Esta Seguro que Desea Actualizar la Agencia "+formulario.nom.value+"?"))
        {
         formulario.opcion.value= 3;
         formulario.submit();
        }
    }
}

