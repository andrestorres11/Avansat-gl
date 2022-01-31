function aceptar_ins(formulario)
{
    validacion = true;
    formulario = document.form_ins;

    if (formulario.novedad.value == 0)
    {
     window.alert("La Novedad es Requerida")
     validacion = false
     formulario.novedad.focus()
    }
    else if(formulario.obs.value == "")
    {
     window.alert("La Observación es Requerida")
     validacion = false
     formulario.obs.focus()
    }
    else if(formulario.codpc.value == 0)
    {
     window.alert("Seleccione el Puesto de Control")
     validacion = false
     formulario.codpc.focus()
    }
    else if(document.getElementById('duracion') != null)
    {
     if(formulario.tiem_duraci.value == "")
     {
        window.alert('Digite El tiempo de Duracion en Minutos de La novedad')
        formulario.tiem_duraci.focus()
     }
     else
     {   if(confirm('Esta Seguro que Desea Insertar La nota de Controlador?'))
            {
                formulario.opcion.value= 3;
                formulario.submit();
            }
     }
    }
    else  if(confirm('Esta Seguro que Desea Insertar La nota de Controlador?'))
    {
    formulario.opcion.value= 2;
    formulario.submit();
    }
}

function aceptar_act(formulario)
{
    validacion = true;
    formulario = document.form_act;
    if(formulario.obs.value == "")
    {
     window.alert("La Observación es Requerida")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 3;
    formulario.submit();
    }
}