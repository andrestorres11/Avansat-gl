function configuracion(formulario)
{
    var dispos = VActivacion();

    if(dispos != "")
    {
     alert(dispos);
    }
    else if (confirm("Esta Seguro de Actualizar los Parametros de Mensajes.?"))
    {
     formulario.opcion.value = 2;
     formulario.submit();
    }
}

function VActivacion()
{
   var frm = document.forms[0];
   cont = 0;
   cant = 0;

    while(frm.elements[cont])
    {
     if((frm.elements[cont].type == "text" && (frm.elements[cont].value != "" && frm.elements[cont].value != 0)) && frm.elements[cont+1].checked == false)
     {
      frm.elements[cont+1].focus();
      return "Debe Seleccionar la Casilla de Activacion";
      cont += 2;
     }
     else if((frm.elements[cont].type == "text" && (frm.elements[cont].value == "" && frm.elements[cont].value != 0)) && frm.elements[cont+1].checked == true)
     {
      frm.elements[cont].focus();
      return "Debe Definir el Tiempo de Intervalo";
      cont += 2;
     }
     else
      cont += 1;
    }

   return "";
}

function operador(formulario)
{
    if(formulario.transp.value == 0)
    {
     alert("Debe Seleccionar la Transportador.");
     formulario.transp.focus();
    }
    else if(formulario.nombre.value == "")
    {
     alert("Debe Especificar el Nombre del Operador.");
     formulario.nombre.focus();
    }
    else if(formulario.dns.value == "")
    {
     alert("Debe Especificar el DNS del Operador.");
     formulario.dns.focus();
    }
    else if (confirm("Esta Seguro de Insertar el Operador.?"))
    {
     formulario.opcion.value = 2;
     formulario.submit();
    }
}

function dispositivo(formulario)
{
    var dispos = validar();

    if(dispos != "")
    {
     alert(dispos);
    }
    else if (confirm("Esta Seguro de Actualizar el Listado de Dispositivos.?"))
    {
     formulario.opcion.value = 2;
     formulario.submit();
    }
}

function validar()
{
   var frm = document.forms[0];
   cont = 0;
   cant = 0;

    while(frm.elements[cont])
    {
     if(frm.elements[cont].type == "checkbox" && frm.elements[cont].checked == true)
     {
      cant++;
      if(frm.elements[cont+1].value == "")
      {
       frm.elements[cont+1].focus();
       return "Debe Especificar el # del Dispositivo";
      }
      else if(frm.elements[cont+2].value == "0")
      {
       frm.elements[cont+2].focus();
       return "Debe Especificar el Operador del Dispositivo";
      }
     }
     cont += 4;
    }

   if(cant == 0)
    return "Debe Especificar por lo Menos un Dispositivo"

   return ""
}