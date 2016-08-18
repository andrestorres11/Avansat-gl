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


function Confir_Cambio(estado)
{
    formulario = document.form_princi
    if(document.getElementById('obs_historID').value!='')
    {
      if (confirm('Esta Seguro que Desea ' + estado + ' el Usuario?')) {
        formulario.opcion.value = 2;
        formulario.submit();
      }  
    }else{
      alert("La Observacion es Requerida.")
    }
}



function aceptar_ins1(formulario)
{
    //EXPRESIONES REGULARES PARA VALIDAR FORMATOS
    var correo = /^(.+\@.+\..+)$/

    validacion = true
    formulario = document.form_ins
    if(formulario.usuari.value == "")
    {
     window.alert("El Usuario es Requerido")
     validacion = false
     formulario.usuari.focus()
    }
    else if(formulario.clave1.value == "")
    {
     window.alert("La Clave es Requerida")
     validacion = false
     formulario.clave1.focus()
    }
    else if(formulario.clave2.value == "")
    {
     window.alert("Confirme La Clave ")
     validacion = false
     formulario.clave2.focus()
    }
    else if(formulario.clave1.value != formulario.clave2.value)
    {
     window.alert("La Clave no fue Confirmada con Exito")
     validacion = false
     formulario.clave2.focus()
    }
    else if(formulario.nombre.value == "")
    {
     window.alert("El Nombre es Requerido")
     validacion = false
     formulario.nombre.focus()
    }
    else if(formulario.mail.value == "")
    {
     window.alert("El Correo Electronico es Requerido")
     validacion = false
     formulario.mail.focus()
    }
    else if (!correo.test(formulario.mail.value))
    {
     window.alert("El Correo Electronico debe contener el siguiente formato:\n\n\t soporte@intrared.net \n\n\t soporte@intrared.com.co ")
     validacion = false
     formulario.mail.focus()
    }
    else if(!valid_cajas(1) && formulario.perfil.value == 0)
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

function aceptar_ins2(formulario)
{
   validacion = true
   formulario = document.form_ins

   if(confirm('Desea Insertar El usuario '))
   {
   formulario.opcion.value= 2;
   formulario.submit();
   }
}

function aceptar_ins1(formulario)
{
    //EXPRESIONES REGULARES PARA VALIDAR FORMATOS
    var correo = /^(.+\@.+\..+)$/

    validacion = true
    formulario = document.form_ins
    if(formulario.usuari.value == "")
    {
     window.alert("El Usuario es Requerido")
     validacion = false
     formulario.usuari.focus()
    }
    else if(formulario.clave2.value == "" && formulario.clave1.value != "")
    {
     window.alert("Confirme La Clave ")
     validacion = false
     formulario.clave2.focus()
    }
    else if(formulario.clave1.value != formulario.clave2.value)
    {
     window.alert("La Clave no fue Confirmada con Exito")
     validacion = false
     formulario.clave2.focus()
    }
    else if(formulario.nombre.value == "")
    {
     window.alert("El Nombre es Requerido")
     validacion = false
     formulario.nombre.focus()
    }
    else if(formulario.mail.value == "")
    {
     window.alert("El Correo Electronico es Requerido")
     validacion = false
     formulario.mail.focus()
    }
    else if (!correo.test(formulario.mail.value))
    {
     window.alert("El Correo Electronico debe contener el siguiente formato:\n\n\t soporte@intrared.net \n\n\t soporte@intrared.com.co ")
     validacion = false
     formulario.mail.focus()
    }
    else if(!valid_cajas(1) && formulario.perfil.value == 0)
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

function aceptar_act2(formulario)
{
   validacion = true
   formulario = document.form_ins

   if(confirm('Esta seguro de Actualizar el Usuario'))
   {
   formulario.opcion.value= 2;
   formulario.submit();
   }
}

function cambiar_clave()
{
        formulario = document.form_clavex
        if(formulario.clave.checked)
        {
                if(formulario.clv_usuari.value == window.prompt("Digite la Clave Actual",''))
                {
                        formulario.new_clv_usuari.disabled = false
                        formulario.new_clv_usuari.value = ''
                        formulario.new_confirma.disabled = false
                        formulario.new_confirma.value = ''
                        formulario.new_clv_usuari.focus();
                }
                else
                {
                        window.alert("Contraseña no Coincide")
                        formulario.new_clv_usuari.disabled = true
                        formulario.new_confirma.disabled = true
                        formulario.clave.checked = false
                }
        }
                else
                {
                        formulario.new_clv_usuari.disabled = true
                        formulario.new_confirma.disabled = true
                }
}

function validar(mensaje)
{
    var correo = /^(.+\@.+\..+)$/
        formulario = document.form_clavex

         if(!formulario.nom_usuari.value)
        {
                window.alert("Digite el  Nombre")
                  formulario.nom_usuari.focus()
        }
        else if(formulario.usr_emailx.value == "")
        {
                  window.alert("Digite el Correo Electronico")
                  formulario.usr_emailx.focus()
        }
        else if(!correo.test(formulario.usr_emailx.value))
        {
                  window.alert("Correo electronico Invalido")
                  formulario.usr_emailx.focus()
        }

        else if(formulario.clave.checked)
        {
             if(!formulario.new_clv_usuari.value)
                        {
                         window.alert("Digite la Nueva Contraseña")
                           formulario.new_clv_usuari.focus()
                        }
             else  if(!formulario.new_confirma.value)
                        {
                        window.alert(" Confirme Contraseña")
                          formulario.new_confirma.focus()
                        }
                else if(formulario.new_confirma.value != formulario.new_clv_usuari.value)
                        {
                         window.alert("Contraseña no Coincide")
                           formulario.new_confirma.focus()
                        }
                else if (confirm("Desea Cambiar los Datos de "+ formulario.nom_usuari.value))
                     {   formulario.actual.value = 1;
                            formulario.submit()
                        }

        }
        else if (confirm("Desea Cambiar los Datos de "+ formulario.nom_usuari.value))
             {   formulario.actual.value = 1;
                   formulario.submit()
                }

}
