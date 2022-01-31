function aceptar_ins(formulario)
{
    validacion = true;
    formulario = document.form_ins;

    $valid_pc = validar_pc();

    if($valid_pc != "")
    {
     window.alert($valid_pc)
     validacion = false
    }
    else
    {
     if(confirm("Desea Generar la Salida Para la caravana # " + formulario.carava.value + ".?"))
     {
      formulario.opcion.value= 2;
      formulario.submit();
     }
    }
}

function validar_pc()
{
	//EXPRESIONES REGULARES PARA VALIDAR LOS FORMATOS
    	var tiemp     = /[0-9]/

	var i;
        var frm = document.forms[0];
        cont = 0;

    	while(frm.elements[cont])
    	{
         if(frm.elements[cont].type == "checkbox" && frm.elements[cont+1].value != "0" && frm.elements[cont+1].type == "select-one")
	 {
	   if(frm.elements[cont+2].value == "" || parseInt(frm.elements[cont+2].value) < 1)
	   {
	    frm.elements[cont+2].focus()
	    return "Debe Indicar el Tiempo para La Novedad."
	   }
	   else if(frm.elements[cont+2].value && !tiemp.test(frm.elements[cont+2].value))
	   {
	    frm.elements[cont+2].focus()
	    return "El Valor del Tiempo es Numerico."
	   }
	  cont += 2
	 }
	 else if(frm.elements[cont].type == "checkbox" && frm.elements[cont+1].value == "0" && frm.elements[cont+2].value != "")
	 {
	  frm.elements[cont+1].focus()
	   return "Debe Indicar el La Novedad."
	  cont += 2
	 }
         cont += 1	
    	}

	return "";
}

function aceptar_insert2(formulario)
{
  validacion = true;
  formulario = document.form_insert;
  formulario.opcion.value= 3;
  formulario.submit();
}

function aceptar_lis(formulario)
{
    validacion = true
    formulario = document.form_list
    if(Validar_F(formulario.fecini.value) == false)
    {
     validacion = false
    }
    else if(Validar_F(formulario.fecfin.value) == false)
    {
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
    if(Validar_F(formulario.fecini.value) == false)
    {
     validacion = false
    }
    else if(Validar_F(formulario.fecfin.value) == false)
    {
     validacion = false
    }
    else
    {
     formulario.opcion.value= 1;
     formulario.submit();
    }
}

function aceptar_act2(formulario)
{
    validacion = true;
    formulario = document.form_act;
    if(formulario.manifi.value == "")
    {
     window.alert("El Manifiesto es Requerido")
     validacion = false
    }
    else if(formulario.cliente.value == 0)
    {
     window.alert("El Cliente es Requerido")
     validacion = false
    }
    else if(formulario.asegra.value == 0)
    {
     window.alert("La Aseguradora es Requerida")
     validacion = false
    }
    else if(formulario.transpor.value == 0)
    {
     window.alert("La Transportadora es Requerida")
     validacion = false
    }
    else if(formulario.ciuori.value == 0)
    {
     window.alert("El Origen es Requerido")
     validacion = false
    }
    else if(formulario.ciudes.value == 0)
    {
     window.alert("El Destino es Requerido")
     validacion = false
    }
    else if(formulario.ruta.value == 0)
    {
     window.alert("La Ruta es Requerida")
     validacion = false
    }
    else if(Validar_F(formulario.fecpla.value) == false)
    {
     validacion = false
    }
    else if(Validar_H(formulario.horpla.value) == false)
    {
     validacion = false
    }
    else if(formulario.placa.value == "")
    {
     window.alert("La Placa es Requerida")
     validacion = false
    }
    else if(formulario.regplaca.value == 0)
    {
     window.alert("El Vehículo no esta Registrado ")
     validacion = false
    }
    else if(formulario.mercan.value == 0)
    {
     window.alert("La Mercancia es Requerida")
     validacion = false
    }
    else if(formulario.tipdes.value == 0)
    {
     window.alert("El Tipo de Despacho es Requerido")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 3;
    formulario.submit();
    }
}

function aceptar_eli(formulario)
{
    validacion = true
    formulario = document.form_eli
    formulario.opcion.value= 1;
    formulario.submit();
}
