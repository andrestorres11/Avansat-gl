function setColor(color, nom) {
        var f = document.ins_alert;
        var nom_cam = nom;


        if (color) {
                f.color.value = color;
                f.color.disabled;
        }
        //test.style.background = f.color.value;
        document.getElementById('test').style.background = '#' + f.color.value;
        //fix for mozilla: does this work with ie? opera ok.
		window.close()
}

function aceptar_insert(formulario)
{
    validacion = true
	var num = /[0-9]/
    formulario = document.ins_alert
	if (formulario.nom_ala.value == "")
	{
     window.alert("El Nombre de la alarma es necesario")
     validacion = false
     formulario.nom_ala.focus()		
	}
    else if(formulario.tiempo.value == "")
    {
     window.alert("El Tiempo de la alarma es necesario")
     validacion = false
     formulario.tiempo.focus()
    }
	else if (!num.test(formulario.tiempo.value))
	{
     window.alert("El Tiempo de la alarma Debe Contener Solo Numeros")
     validacion = false
     formulario.tiempo.focus()		
	}
    else
    {
    formulario.opcion.value= 2;
    formulario.submit();
    }
}
