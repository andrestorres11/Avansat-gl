function validar_cass(desurb)
{
 var frm = document.forms[0];
 var cont = 0;
 var tot = 0;

 if(desurb == 0)
 {
  while(frm.elements[cont])
  {
   if(frm.elements[cont].type == "checkbox" && frm.elements[cont].checked == true)
   {
    if(frm.elements[cont+1].value == "")
    {
     frm.elements[cont+1].focus();
     return "Debe Especificar el Documento/Codigo";
    }
    else if(frm.elements[cont+2].value == "")
    {
     frm.elements[cont+2].focus();
     return "Debe Especificar el Nombre";
    }
    else if(frm.elements[cont+4].value == "0")
    {
     frm.elements[cont+4].focus();
     return "Debe Seleccionar el Tipo";
    }
    cont += 5;
   }

   cont++;
  }
 }
 else
 {
  while(frm.elements[cont])
  {
   if(frm.elements[cont].type == "checkbox" && frm.elements[cont].checked == true)
   {
    if(frm.elements[cont+1].value == "")
    {
     frm.elements[cont+1].focus();
     return "Debe Especificar el Documento/Codigo";
    }
    else if(frm.elements[cont+2].value == "")
    {
     frm.elements[cont+2].focus();
     return "Debe Especificar el Nombre";
    }
    else if(frm.elements[cont+4].value == "0")
    {
     frm.elements[cont+4].focus();
     return "Debe Seleccionar el Tipo";
    }
    else if(parseInt(frm.elements[cont+4].value) == 2)
    {
     if(frm.elements[cont+5].value == "")
     {
      frm.elements[cont+5].focus();
      return "Debe Especificar la Direccion";
     }
     else if(frm.elements[cont+6].value == "0")
     {
      frm.elements[cont+6].focus();
      return "Debe Seleccionar la Ciudad";
     }
     else if(frm.elements[cont+7].value == "")
     {
      frm.elements[cont+7].focus();
      return "Debe Especificar el Telefono";
     }
    }

    cont += 10;
   }

   cont++;
  }
 }

 return "";
}

function aceptar_remdes()
{
  var formulario = document.forms[0];
  var valida_cass = "";//validar_cass(formulario.desurb.value); 

  if(valida_cass != "")
  {
   window.alert(valida_cass);
  }
  else
  {
   if(confirm("Est Seguro de Actualizar la Informacion del Formulario.?"))
   {
    formulario.opcion.value = 4;
    formulario.submit();
   }
  }
}

function genera_digito(nit, dig)
{
        ceros = "000000";
        li_peso= new Array();
        li_peso[0] = 71;
        li_peso[1] = 67;
        li_peso[2] = 59;
        li_peso[3] = 53;
        li_peso[4] = 47;
        li_peso[5] = 43;
        li_peso[6] = 41;
        li_peso[7] = 37;
        li_peso[8] = 29;
        li_peso[9] = 23;
        li_peso[10] = 19;
        li_peso[11] = 17;
        li_peso[12] = 13;
        li_peso[13] = 7;
        li_peso[14] = 3;

        ls_str_nit = ceros + nit.value;
        li_suma = 0;
        for(i = 0; i < 15; i++)
        {
                    li_suma += ls_str_nit.substring(i,i+1) * li_peso[i];
        }
        digito_chequeo = li_suma%11;
        if (digito_chequeo >= 2)
                digito_chequeo = 11 - digito_chequeo;
        dig.value = digito_chequeo
}

function validar_nivel(cant)
{
          var i;
          var counter=0;
          var frm = document.forms[0];
          var chi = frm.checkeditems;
          var offset = 0;
          cont = 0;

    while(frm.elements[cont])
    {
            if(frm.elements[cont].type == "checkbox")
                break
        else
                cont += 1
    }

          for (i=0;i < cant; i++)
          {
            if (frm.elements[cont].checked)
            {
              if(frm.elements[cont+1].value != '' && frm.elements[cont+2].value != '')
                 counter++;
            }
            cont += 3
          } // next (i)

          if ((chi)&&(counter==0)){
           return (false);
          }
          else
          {
           return(true);
          }
}
function validar_nit2(formulario)
{
    var terc = /^[0-9]{5,9}$/
    if (!terc.test(form_clientes.nit.value))
       {
           alert("El Nit o Cédula debe ser sin puntos(.), ni Guiones(-), ni Comas(,)");
           form_clientes.nit.focus()
           return false
       }
      return true
}


function copiar(formulario)
{
   formulario = document.form_transpor
   posic = formulario.ciures.selectedIndex
   formulario.nomsed.value = " ("+ formulario.ciures[posic].text +")"
   formulario.dirsed.value = formulario.direc.value
   formulario.telsed.value = formulario.telef.value

}

function siguiente(formulario)
{
    validacion = true
    var tel = /^[0-9]{4}/
    var correo = /^(.+\@.+\..+)$/

        if(formulario.tercer.value == "")
        {
            window.alert("El NIT de la Transportadora es Requerido")
            formulario.tercer.focus()
        }
        else if(isNaN(formulario.tercer.value))
        {
                window.alert("El NIT solo debe contener numeros")
                formulario.tercer.focus()
        }
        else if(formulario.tercer.value <= 0)
        {
                window.alert("El NIT solo debe contener numeros validos")
                formulario.tercer.focus()
        }
        else if(formulario.dijver.value == "")
        {
            window.alert("El Digito de Verificación es requerido")
            formulario.dijver.focus()
        }
        else if(isNaN(formulario.dijver.value))
        {
            window.alert("El Digito de Verificación solo debe contener numeros")
            formulario.dijver.focus()
        }
        else if(formulario.abr.value == "")
        {
           window.alert("La abreviatura es requerida")
           formulario.abr.focus()
        }
        else if(formulario.abr.value.length < 5)
        {
           window.alert("La abreviatura Debe contener mas de 5 caracteres")
           formulario.abr.focus()
        }
        else if(formulario.nom.value == "")
        {
           window.alert("La Razon Social es Requerida")
           formulario.nom.focus()
        }
        else if((formulario.cod_minis.value != "") && (formulario.cod_minis.value <= 0))
        {
                window.alert("El codigo de la empresa solo debe contenes numeros positivos")
                formulario.cod_minis.focus()
        }
        else if((formulario.cod_minis.value != "") && (isNaN(formulario.cod_minis.value)))
        {
                window.alert("El codigo de la empresa solo debe contenes numeros")
                formulario.cod_minis.focus()
        }
        else if((formulario.cod_minis.value != "") && (formulario.cod_minis.value.length < 4))
        {
                window.alert("El codigo de la empresa debe contener 4 digitos")
                formulario.cod_minis.focus()
        }
        else if(formulario.ciures.value == 0)
        {
           window.alert("La Ciudad es Requerida")
           formulario.ciures.focus()
        }
        else if(formulario.direc.value == "")
        {
            window.alert("La Dirección es Requerida")
            formulario.direc.focus()
        }
        else if(formulario.telef.value == "")
        {
            window.alert("El Número de Teléfono es Requerido")
            formulario.telef.focus()
        }
        else if(!tel.test(formulario.telef.value))
        {
            window.alert("El Número de Teléfono debe comenzar por numeros")
            formulario.telef.focus()
        }
        else if(formulario.regimen.value == 0)
        {
            window.alert("El Regimen es Requerido")
            formulario.regimen.focus()
        }
        else if((formulario.codregi.value != "") && (isNaN(formulario.codregi.value)))
        {
                window.alert("El Codigo Regional solo debe contener numeros")
                formulario.codregi.focus()
        }
        else if((formulario.codregi.value != "") && (formulario.codregi.value.length < 3))
        {
                window.alert("El Codigo Regional solo debe contener tres digitos")
                formulario.codregi.focus()
        }
        else if((formulario.codregi.value != "") && (formulario.codregi.value <= 0))
        {
                window.alert("El Codigo Regional solo debe contener numeros positivos")
                formulario.codregi.focus()
        }
        else if((formulario.numresol.value != "") && (isNaN(formulario.numresol.value)))
        {
                window.alert("El Numero de Resolucion solo debe contener Numeros")
                formulario.numresol.focus()
        }
        else if((formulario.ragnini.value != "") && (formulario.ragnini.value.length < 7))
        {
                window.alert("Verifique el Rango de Manifiestos")
                formulario.ragnini.focus()
        }
        else if((formulario.ragnfin.value != "") && (formulario.ragnfin.value.length < 7))
        {
                window.alert("Verifique el Rango de Manifiestos")
                formulario.ragnfin.focus()
        }
        else if(formulario.nomsed.value == "")
        {
                window.alert("El Nombre de la sede Principal es Requerido")
                formulario.nomsed.focus()
        }
        else if(formulario.ciused.value == 0)
        {
                window.alert("La Ciudad de la Sede Principal es Requerido ")
                formulario.ciused.focus()
        }
        else if(formulario.dirsed.value == "")
        {
                window.alert("La Direccion de La sede principal Requerida")
                formulario.dirser.focus()
        }
        else if(formulario.conage.value == "")
        {
                window.alert("El Contacto de La sede principal es Requerido")
                formulario.conage.focus()
        }
        else if(formulario.telsed.value == "")
        {
                window.alert("El Numero de Telefono de La sede principal es Requerido")
                formulario.telsed.focus()
        }
        else if ((formulario.email.value != "") && (!correo.test(formulario.email.value)))
        {
                window.alert("El Correo Electronico debe contener el siguiente formato:\n\n\t soporte@intrared.net \n\n\t soporte@intrared.com.co ")
                formulario.email.focus()
        }
        else
        {
         if(confirm("Esta Seguro de Insertar la Transportadora.?"))
         {
          formulario.opcion.value = 2;
          formulario.submit();
         }
        }
}

function siguiente_act(formulario)
{
    validacion = true
    var tel = /^[0-9]{4}/
    var correo = /^(.+\@.+\..+)$/

        if(formulario.tercer.value == "")
        {
            window.alert("El NIT de la Transportadora es Requerido")
            formulario.tercer.focus()
        }
        else if(isNaN(formulario.tercer.value))
        {
                window.alert("El NIT solo debe contener numeros")
                formulario.tercer.focus()
        }
        else if(formulario.tercer.value <= 0)
        {
                window.alert("El NIT solo debe contener numeros validos")
                formulario.tercer.focus()
        }
        /*else if(formulario.dijver.value == "")
        {
            window.alert("El Digito de Verificación es requerido")
            formulario.dijver.focus()
        }
        else if(isNaN(formulario.dijver.value))
        {
            window.alert("El Digito de Verificación solo debe contener numeros")
            formulario.dijver.focus()
        }*/
        else if(formulario.abr.value == "")
        {
           window.alert("La abreviatura es requerida")
           formulario.abr.focus()
        }
        else if(formulario.abr.value.length < 5)
        {
           window.alert("La abreviatura Debe contener mas de 5 caracteres")
           formulario.abr.focus()
        }
        else if(formulario.nom.value == "")
        {
           window.alert("La Razon Social es Requerida")
           formulario.nom.focus()
        }
        else if((formulario.cod_minis.value != "") && (formulario.cod_minis.value <= 0))
        {
                window.alert("El codigo de la empresa solo debe contenes numeros positivos")
                formulario.cod_minis.focus()
        }
        else if((formulario.cod_minis.value != "") && (isNaN(formulario.cod_minis.value)))
        {
                window.alert("El codigo de la empresa solo debe contenes numeros")
                formulario.cod_minis.focus()
        }
        else if((formulario.cod_minis.value != "") && (formulario.cod_minis.value.length < 4))
        {
                window.alert("El codigo de la empresa debe contener 4 digitos")
                formulario.cod_minis.focus()
        }
        else if(formulario.ciures.value == 0)
        {
           window.alert("La Ciudad es Requerida")
           formulario.ciures.focus()
        }
        else if(formulario.direc.value == "")
        {
            window.alert("La Dirección es Requerida")
            formulario.direc.focus()
        }
        else if(formulario.telef.value == "")
        {
            window.alert("El Número de Teléfono es Requerido")
            formulario.telef.focus()
        }
        else if(!tel.test(formulario.telef.value))
        {
            window.alert("El Número de Teléfono debe comenzar por numeros")
            formulario.telef.focus()
        }
        else if(formulario.regimen.value == 0)
        {
            window.alert("El Regimen es Requerido")
            formulario.regimen.focus()
        }
        else if((formulario.codregi.value != "") && (isNaN(formulario.codregi.value)))
        {
                window.alert("El Codigo Regional solo debe contener numeros")
                formulario.codregi.focus()
        }
        else if((formulario.codregi.value != "") && (formulario.codregi.value.length < 3))
        {
                window.alert("El Codigo Regional solo debe contener tres digitos")
                formulario.codregi.focus()
        }
        else if((formulario.codregi.value != "") && (formulario.codregi.value <= 0))
        {
                window.alert("El Codigo Regional solo debe contener numeros positivos")
                formulario.codregi.focus()
        }
        else if((formulario.numresol.value != "") && (isNaN(formulario.numresol.value)))
        {
                window.alert("El Numero de Resolucion solo debe contener Numeros")
                formulario.numresol.focus()
        }
        else if((formulario.ragnini.value != "") && (formulario.ragnini.value.length < 7))
        {
                window.alert("Verifique el Rango de Manifiestos")
                formulario.ragnini.focus()
        }
        else if((formulario.ragnfin.value != "") && (formulario.ragnfin.value.length < 7))
        {
                window.alert("Verifique el Rango de Manifiestos")
                formulario.ragnfin.focus()
        }
        else
        {
         if(confirm("Esta Seguro de Actualizar la Transportadora.?"))
         {
          formulario.opcion.value = 3;
          formulario.submit();
         }
        }
}

function aceptar(formulario)
{
    validacion = true
    formulario = document.form_poliza

       if(formulario.poliza.value == "")
        {
            window.alert("El Número de la Poliza es Requerido")
                validacion = false
        }
       else if(formulario.asegra.value == '0')
        {
            window.alert("La Aseguradora es Requerida")
                validacion = false
        }
        else if(formulario.valmax.value == "")
        {
            window.alert("El Valor Maximo por Despacho es Requerido")
                validacion = false
        }
        else if(formulario.modelo.value == "")
        {
            window.alert("El Rango de Vehículos es Requerido")
                validacion = false
        }
        else
        {
             if (confirm("Esta Seguro que Desea Ingresar la Configuración de la Empresa ?"))
                {
                 formulario.opcion.value = 2;
                 formulario.submit();
                 return validacion
                }

        }
}

function sin_poliza(formulario)
{
             formulario = document.form_poliza

             if (confirm("Esta Seguro que Desea Ingresar la Configuración de la Empresa ?"))
                {
                 formulario.opcion.value = 2;
                 formulario.submit();
                 return validacion
                }
}

function actualizar(formulario)
{
    validacion = true
    formulario = document.form_poliza

       if(formulario.poliza.value == "")
        {
            window.alert("El Número de la Poliza es Requerido")
                validacion = false
        }
       else if(formulario.asegra.value == '0')
        {
            window.alert("La Aseguradora es Requerida")
                validacion = false
        }
        else if(formulario.valmax.value == "")
        {
            window.alert("El Valor Maximo por Despacho es Requerido")
                validacion = false
        }
        else if(formulario.modelo.value == "")
        {
            window.alert("El Rango de Vehículos es Requerido")
                validacion = false
        }
        else
        {
             if (confirm("Esta Seguro que Desea Actualizar la Configuración de la Empresa ?"))
                {
                 formulario.opcion.value = 4;
                 formulario.submit();
                 return validacion
                }

        }
}


function compro_cuenta(formulario)
{
    validacion = true
    formulario = document.form_compro

       if(formulario.tiptra.value == '0')
        {
            window.alert("La Transacción es Requerida")
                validacion = false
        }
        else if(formulario.tipcom.value == '0')
        {
            window.alert("El Tipo de Comprobante es Requerido")
                validacion = false
        }
        else if(formulario.agencia.value == '0')
        {
            window.alert("La Agencia es Requerida")
                validacion = false
        }
        else
        {
             if (confirm("Esta Seguro que Desea Ingresar los Datos ?"))
                {
                 formulario.opcion.value = 2;
                 formulario.submit();
                 return validacion
                }

        }
}

function acep_autori(formulario)
{
    validacion = true
    formulario = document.form_nivaut

      if(!validar_nivel(formulario.maximo.value))
      {
            alert("Seleccione El Nivel de Autorización y Valores para Continuar")
            validacion = false
       }
      else
       {
       if (confirm("Esta Seguro que Desea dar Niveles de Autorizacion al Perfil --> "+formulario.nperfi.value+"?\n...Los Campos con Valores en 0 - 0 Serán de solo lectura para este perfil..."))
             {
                 formulario.opcion.value = 2;
                 formulario.submit();
                 return validacion
              }

       }
}

function acep_autori(formulario)
{
    validacion = true
    formulario = document.form_nivaut

      if(!validar_nivel(formulario.maximo.value))
      {
            alert("Seleccione El Nivel de Autorización y Valores para Continuar")
            validacion = false
       }
      else
       {
       if (confirm("Esta Seguro que Desea dar Niveles de Autorizacion al Perfil --> "+formulario.nperfi.value+"?\n...Los Campos con Valores en 0 - 0 Serán de solo lectura para este perfil..."))
             {
                 formulario.opcion.value = 2;
                 formulario.submit();
                 return validacion
              }

       }
}