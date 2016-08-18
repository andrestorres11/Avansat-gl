function insertar()
{
    validacion = true;
    formulario = document.form_princi;

    if(formulario.nom.value == "")
    {
     window.alert("Debe Especificar el Nombre.")
     validacion = false
     formulario.nom.focus()
    }
    else if(!valid_cajas(1))
    {
     window.alert("Debe Seleccionar Por lo Menos una Opcion.")
     validacion = false
    }
    else
    {
      formulario.opcion.value= 1;
      formulario.submit();
    }
}

function actualizar()
{
    validacion = true;
    formulario = document.form_princi;

    if(formulario.nom.value == "")
    {
     window.alert("Debe Especificar el Nombre.")
     validacion = false
     formulario.nom.focus()
    }
    else if(!valid_cajas(1))
    {
     window.alert("Debe Seleccionar Por lo Menos una Opcion.")
     validacion = false
    }
    else
    {
      formulario.opcion.value= 2;
      formulario.submit();
    }
}

function eliminar()
{
    validacion = true;
    formulario = document.form_princi;

    if(confirm("Confirma que Desea Eliminar el Perfil "+ formulario.nom.value +"?."))
    {
     formulario.opcion.value= 3;
     formulario.submit();
    }
}

function todos(chkbox)
{
    for (var i=0;i < document.forms[0].elements.length;i++)
    {
     var elemento = document.forms[0].elements[i];
     if (elemento.type == "checkbox")
     {
      elemento.checked = chkbox.checked
     }
    }
}

function valid_cajas(opc)
{
   validacion = false
   var frm = document.forms[0];
   var cont = 0;
   var tota = 0;

   while(frm.elements[cont])
   {
    if(frm.elements[cont].type == "checkbox")
    {
     if(opc == 2 && tota > 0)
     {
      if(frm.elements[cont].checked == true)
      {
	validacion = true;
      }
     }
     else if(opc == 1)
     {
      if(frm.elements[cont].checked == true)
      {
	validacion = true;
      }
     }

     tota++
    }
    cont += 1
   }

   return validacion;
}