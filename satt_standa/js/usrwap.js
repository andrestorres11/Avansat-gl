function aceptar_ins(formulario)
{
    validacion = true
    formulario = document.form_ins
    if(formulario.usuari.value == "")
    {
     window.alert("El Usuario es Requerido")
     validacion = false
     formulario.usuari.focus()
    }
    else if(formulario.clave1.value == "")
    {
     window.alert("La Clave es Requerida")
     validacion = false
     formulario.clave1.focus()
    }
    else if(formulario.clave2.value == "")
    {
     window.alert("Confirme La Clave ")
     validacion = false
     formulario.clave2.focus()
    }
    else if(formulario.clave1.value != formulario.clave2.value)
    {
     window.alert("La confirmacion de la clave ")
     validacion = false
     formulario.clave2.focus()
    }
    else if (confirm("Confirma que Insertar el Usuario para WAP?"))
    {
    formulario.opcion.value= 1;
    formulario.submit();
    }
}

function aceptar_act(formulario)
{
    validacion = true
    formulario = document.form_act

    if(formulario.usuari.value == "")
    {
     window.alert("El Usuario es Requerido")
     validacion = false
    }
    else if(formulario.clave2.value == "" && formulario.clave1.value != "")
    {
       window.alert("Confirme La Nueva Clave ")
       validacion = false
       formulario.clave2.focus()
    }
    else if(formulario.clave1.value != formulario.clave2.value)
    {
       window.alert("La Clave no fue Confirmada con Exito")
       validacion = false
       formulario.clave2.focus()
    }
    else if (confirm("Confirma que Desea Actualizar el Usuario para WAP?"))
    {
      formulario.opcion.value= 2;
      formulario.submit();
    }
}

function eli_usuario(formulario)
{
    validacion = true
    formulario = document.form_eli


    if (confirm("Confirma que Desea Actualizar el Usuario para WAP?"))
    {
      formulario.opcion.value= 2;
      formulario.submit();
    }
}