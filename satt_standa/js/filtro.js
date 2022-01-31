function aceptar_ins(formulario)
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

function aceptar_lis(formulario)
{
    validacion = true
    formulario = document.form_list
    if(formulario.filtro.value == "")
    {
     window.alert("El Filtro es Requerido")
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
    if(formulario.filtro.value == "")
    {
     window.alert("El Filtro es Requerido")
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
    formulario = document.form_eli
    if(formulario.filtro.value == "")
    {
     window.alert("El Filtro es Requerido")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 1;
    formulario.submit();
    }
}

