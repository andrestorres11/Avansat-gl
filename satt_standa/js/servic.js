function aceptar_ins1(formulario)
{
    validacion = true
    formulario = document.form_ins
    if(formulario.nombre.value == "")
    {
     window.alert("El Nombre es Requerido")
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
    if(formulario.nombre.value == "")
    {
     window.alert("El Nombre es Requerido")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 2;
    formulario.submit();
    }
}

function aceptar_lis(formulario)
{
    validacion = true
    formulario = document.form_list
    if(formulario.servic.value == "")
    {
     window.alert("El Servicio es Requerido")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 1;
    formulario.submit();
    }
}

