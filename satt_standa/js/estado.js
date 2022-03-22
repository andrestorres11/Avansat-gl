function aceptar_apr(formulario)
{
    validacion = true
    formulario = document.form_apr
    if(formulario.placa.value == "")
    {
     window.alert("Digite una Placa")
     validacion = false
    }
    else if(formulario.num_regis.value == 0)
    {
     window.alert("La Placa no esta Registrada")
     validacion = false
    }
    else
    {
     formulario.opcion.value = 2
     formulario.submit()
     return validacion
    }
}