function aceptar_insert(formulario)
{
    formulario = document.form_insert
    if(formulario.mermin.value == "")
    {
     window.alert("La Mercancia del Ministerio es Requerido")
     formulario.mercan.focus();
    }
    else if(formulario.abr.value == "")
    {
     window.alert("La Abreviatura es Requerida")
     formulario.abr.focus()
    }
    else if(confirm('Esta Seguro de Insertar la Mercancia ? '))
    {
    formulario.opcion.value= 1;
    formulario.submit();
    }
}
function aceptar_update(formulario)
{
    validacion = true
    formulario = document.form_item
    if(formulario.abr.value == "")
    {
     window.alert("La Abreviatura es Requerida")
     validacion = false
    }
    else if(confirm('Esta Seguro de Actualizar la Mercancia ? '))
    {
    formulario.opcion.value= 3;
    formulario.submit();
    }
}

function aceptar_lis(formulario)
{
    validacion = true
    formulario = document.form_list
    if(formulario.mercan.value == "")
    {
     window.alert("La Mercancia es Requerida")
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
    if(formulario.mercan.value == "")
    {
     window.alert("La Mercancia es Requerida")
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
    if(formulario.mercan.value == "")
    {
     window.alert("La Mercancia es Requerida")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 1;
    formulario.submit();
    }
}