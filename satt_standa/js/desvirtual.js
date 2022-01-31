function aceptar_ins(formulario)
{
    validacion = true;
    formulario = document.form_ins;

    if(formulario.puesto.value == 0)
    {
     window.alert("El Puesto de Control es Requerido.")
     validacion = false
     formulario.puesto.focus()
    }
    else if(formulario.novedad.value == 0)
    {
     window.alert("La Novedad es Requerida.")
     validacion = false
     formulario.novedad.focus()
    }
    else if(formulario.soltie.value == "1" && formulario.tiemp_adicis.value == "")
    {
     window.alert("El Tiempo de la Novedad es Requerido.")
     validacion = false
     formulario.tiemp_adicis.focus()
    }
    else if(formulario.obs.value == "")
    {
     window.alert("La Observación es Requerida")
     validacion = false
     formulario.obs.focus()
    }
    else
    {
	if(confirm("Desea insertar la Nota"))
	{
    	 formulario.opcion.value= 2;
    	 formulario.submit();
	}
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

