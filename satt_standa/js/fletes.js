function aceptar()
{
    var tiemp = /^([0-9])*$/
    formulario = document.form_item

    if(formulario.transp.value == "0")
    {
     window.alert("La Transportadora es Obligatoria.")
     formulario.transp.focus()
    }
    else if(formulario.ciuori.value == "0")
    {
     window.alert("El Origen es Obligatorio.")
     formulario.ciuori.focus()
    }
    else if(formulario.ciudes.value == "0")
    {
     window.alert("El Destino es Obligatorio.")
     formulario.ciudes.focus()
    }
    else if(formulario.carroc.value == "0")
    {
     window.alert("La Carroceria es Obligatoria.")
     formulario.carroc.focus()
    }
    else if(formulario.trayec.value == "0")
    {
     window.alert("El Trayecto es Obligatorio.")
     formulario.trayec.focus()
    }
    else if(formulario.canton.value == "" | formulario.canton.value == 0)
    {
     window.alert("La Cantidad de Toneladas son Obligatorias y diferente de 0.")
     formulario.canton.focus()
    }
    else if(formulario.coston.value == "" | formulario.coston.value == 0)
    {
     window.alert("El Costo por Tonelada es Obligatorio y diferente de 0.")
     formulario.coston.focus()
    }
    else
    {
     if(formulario.flete.value == "")
      mensaje = "Insertar"
     else
      mensaje = "Actualizar"

     if(confirm("Desea " + mensaje + " el Flete?"))
     {
      formulario.opcion.value= 3
      formulario.submit()
     }
    }
}