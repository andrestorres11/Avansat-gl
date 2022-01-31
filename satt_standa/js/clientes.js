function puntos(donde,caracter)
{
pat = /[\*,\+,\(,\),\?,\\,\$,\[,\],\^]/
valor = donde.value
largo = valor.length
crtr = true
if(isNaN(caracter) || pat.test(caracter) == true)
        {
        if (pat.test(caracter)==true)
                {caracter = "\\" + caracter}
        carcter = new RegExp(caracter,"g")
        valor = valor.replace(carcter,"")
        donde.value = valor
        crtr = false
        }
else
        {
        var nums = new Array()
        cont = 0
        for(m=0;m<largo;m++)
                {
                if(valor.charAt(m) == "." || valor.charAt(m) == " ")
                        {continue;}
                else{
                        nums[cont] = valor.charAt(m)
                        cont++
                        }

                }
        }


var cad1="",cad2="",tres=0
if(largo > 3 && crtr == true)
        {
        for (k=nums.length-1;k>=0;k--)
                {
                cad1 = nums[k]
                cad2 = cad1 + cad2
                tres++
                if((tres%3) == 0)
                        {
                        if(k!=0){
                                cad2 = "." + cad2
                                }
                        }
                }
         donde.value = cad2

        }
}



function aceptar_sin_poliza(formulario)
{
    validacion = true
    formulario = document.form_poliza
    formulario.opcion.value= 3;
    formulario.submit();
}



function aceptar_poliza(formulario)
{
    validacion = true
    formulario = document.form_poliza

    var fec_ini = new String(formulario.ano.value+'-'+formulario.mes.value+'-'+formulario.dia.value)
    var fec_fin = new String(formulario.ano2.value+'-'+formulario.mes2.value+'-'+formulario.dia2.value)

    if(formulario.poliza.value == "")
    {
     window.alert("El numero de la Poliza es requerido")
     validacion = false
    }
else if(!cfec_actual(fec_fin))
{
        alert("La fecha de Vigencia Final debe ser Mayor\n a la Fecha Actual")
        validacion = false
        formulario.dia2.focus()
}
else if(!compar_fec(fec_ini,fec_fin))
{
        alert("La fecha de Vigencia Final debe ser Mayor\n a la Fecha de Vigencia Inicial")
        validacion = false
        formulario.dia2.focus()
}
    else if(formulario.valmax.value == "")
    {
     window.alert("El valor maximo por despacho es requerido")
     validacion = false
    }
    else if(formulario.modelo.value == "")
    {
     window.alert("La Antigüedad de los vehículos  es requerida")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 2;
    formulario.submit();
    }
}



function aceptar_sin_poliza1(formulario)
{
    validacion = true
    formulario = document.form_poliza
    formulario.opcion.value= 4;
    formulario.submit();
}



function aceptar_poliza1(formulario)
{
    validacion = true
    formulario = document.form_poliza
    if(formulario.poliza.value == "")
    {
     window.alert("El numero de la Poliza es requerido")
     validacion = false
    }
    else if(formulario.valmax.value == "")
    {
     window.alert("El valor maximo por despacho es requerido")
     validacion = false
    }
    else if(formulario.modelo.value == "")
    {
     window.alert("La Antigüedad de los vehículos  es requerida")
     validacion = false
    }
    else
    {
    formulario.opcion.value= 3;
    formulario.submit();
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


function copiar_datos(formulario)
{
    validacion = true
    formulario = document.form_clientes
    posic = formulario.ciures.selectedIndex

    formulario.nomsed.value = formulario.nom.value + " ("+ formulario.ciures[posic].text +")"
    formulario.dirsed.value = formulario.dire.value
    formulario.telsed.value = formulario.telef.value
    formulario.ciused.value = formulario.ciures.value
}

function aceptar_delete(formulario)
{
    validacion = true
    formulario = document.form_client
    if (confirm("Seguro que Desea Eliminar el Cliente "+formulario.nom.value+"?"))
                {
                  formulario.opcion.value = 2;
                  formulario.submit();
                  return validacion
                }
}


function eliminar_sede(formulario)
{
    validacion = true
    formulario = document.form_client
    if (confirm("Seguro que Desea Eliminar la sede "+formulario.nom.value+ "del Cliente"+formulario.nclien.value+"?"))
                {
                  formulario.opcion.value = 3;
                  formulario.submit();
                  return validacion
                }
}

function aceptar_insert(formulario)
{
    validacion = true
    formulario = document.form_clientes
     var tel = /^[0-9]{6,10}$/
     var dec = /^[0-9]/
     var correo = /^(.+\@.+\..+)$/

        if(formulario.tipdocu.value == 0)
        {
            window.alert("El tipo de Documento es requerido")
                validacion = false
        }
        else if(formulario.nom.value == "")
        {
            window.alert("El Nombre o Razón Social es requerido")
                validacion = false
        }

         else if(formulario.nit.value == 0)
        {
            window.alert("El Número del documento es Requerido")
                validacion = false
        }


        else if(formulario.ciures.value == "0")
        {
            window.alert("La ciudad de Residencia Requerido")
                validacion = false
        }
        else if(formulario.terreg.value == "0")
        {
            window.alert("El Regimen es Requerido")
                validacion = false
        }

        else if(formulario.dire.value == 0)
        {
            window.alert("La Dirección es Requerida")
                validacion = false
        }
        else if(formulario.telef.value == 0)
        {
            window.alert("El Número de Teléfono es Requerido")
                validacion = false
        }

       else if (!tel.test(form_clientes.telef.value))
       {
           alert("El Número de Teléfono debe ser un campo numerico\n No debe ser superior a 10 caracteres\n e inferior a 6 caracteres ");
           form_clientes.telef.focus()
           return false
       }
        else if(formulario.nomsed.value == "")
        {
            window.alert("El Nombre de la Sede Principal es Requerido")
                validacion = false
        }
        else if(formulario.ciused.value == "")
        {
            window.alert("La Ciudad de la Sede Principal es Requerido")
                validacion = false
        }
        else if(formulario.dirsed.value == "")
        {
            window.alert("La Dirección de la Sede Principal es Requerido")
                validacion = false
        }
        else if(formulario.contsed.value == "")
        {
            window.alert("El Contacto de la Sede Principal es Requerido")
                validacion = false
        }
        else if (!tel.test(form_clientes.telsed.value))
        {
           alert("El Número de Teléfono de la Sede Principal debe ser un campo\nnumerico. No debe ser superior a 10 caracteres e inferior a 6 caracteres ");
           form_clientes.stel.focus()
           return false
        }
        else if (!correo.test(formulario.email.value) && formulario.email.value != "")
        {
           window.alert("El Correo Electronico debe contener el siguiente formato:\n\n\t soporte@intrared.net \n\n\t soporte@intrared.com.co ")
           validacion = false
           formulario.email.focus()
        }


        else if(formulario.valida.value == "2")
        {
             if(formulario.tipdocu.value == "N")
             {
                window.alert("El Tipo de Documento no Puede ser NIT")
                validacion = false
             }

             else if(formulario.apell1.value == "")
             {
                window.alert("El Apellido1 es Requerido")
                validacion = false
             }
             else
              {
                if (confirm("Seguro que Desea Ingresar el Cliente "+formulario.nom.value+"?"))
                {
                  formulario.opcion.value = 1;
                  formulario.submit();
                  return validacion
                }

              }
        }
        else if((formulario.valida.value == "1") || (formulario.valida.value == ""))
        {
            if(formulario.tipdocu.value == "C")
             {
                window.alert("El Tipo de Documento no Puede ser Cedula")
                validacion = false
             }
             else if(formulario.abr.value == "")
             {
                window.alert("La Abreviatura es Requierida")
                validacion = false
             }
              else
              {
                if (confirm("Seguro que Desea Ingresar el Cliente "+formulario.nom.value+"?"))
                {
                  formulario.opcion.value = 1;
                  formulario.submit();
                  return validacion
                }

              }
        }

        else
        {
                if (confirm("Seguro que Desea Ingresar el Cliente "+formulario.nom.value+"?"))
                {
                  formulario.opcion.value = 1;
                  formulario.submit();
                  return validacion
                }

        }
}

function aceptar_update(formulario)
{
    validacion = true
    formulario = document.form_clientes

     var tel = /^[0-9]{6,10}$/
     var dec = /^[0-9]/
     var correo = /^(.+\@.+\..+)$/


        if(formulario.tipdocu.value == 0)
        {
            window.alert("El tipo de Documento es requerido")
                validacion = false
        }
        else if(formulario.nom.value == "")
        {
            window.alert("El Nombre o Razón Social es requerido")
                validacion = false
        }

         else if(formulario.nit.value == 0)
        {
            window.alert("El Número del documento es Requerido")
                validacion = false
        }
        else if(formulario.terreg.value == 0)
        {
            window.alert("El Regimen es Requerido")
                validacion = false
        }

        else if(formulario.ciures.value == "0")
        {
            window.alert("La ciudad de Residencia Requerido")
                validacion = false
        }
        else if(formulario.dire.value == 0)
        {
            window.alert("La Dirección es Requerida")
                validacion = false
        }
        else if(formulario.telef.value == 0)
        {
            window.alert("El Número de Teléfono es Requerido")
                validacion = false
        }
        else if (!tel.test(form_clientes.telef.value))
        {
           alert("El Número de Teléfono debe ser un campo numerico\nNo debe ser superior a 10 caracteres e inferior a 6 caracteres ");
           form_clientes.telef.focus()
           return false
        }

        else if (!correo.test(formulario.smail.value) && formulario.smail.value != "")
        {
           window.alert("El Correo Electronico debe contener el siguiente formato:\n\n\t soporte@intrared.net \n\n\t soporte@intrared.com.co ")
           validacion = false
           formulario.smail.focus()
        }
        else if(formulario.snom.value == "")
        {
            window.alert("El Nombre de la Sede Principal es Requerido")
                validacion = false
        }

        else if(formulario.sdir.value == "")
        {
            window.alert("La Dirección de la Sede Principal es Requerido")
                validacion = false
        }

        else if(formulario.ciused.value == "")
        {
            window.alert("La Ciudad de la Sede Principal es Requerido")
                validacion = false
        }


        else if(formulario.contsed.value == "")
        {
            window.alert("El Contacto de la Sede Principal es Requerido")
                validacion = false
        }
        else if(formulario.stel.value == "")
        {
            window.alert("El Teléfono de la Sede Principal es Requerido")
                validacion = false
        }
        else if (!tel.test(form_clientes.stel.value))
        {
           alert("El Número de Teléfono de la Sede Principal debe ser un campo\nnumerico. No debe ser superior a 10 caracteres e inferior a 6 caracteres ");
           form_clientes.stel.focus()
           return false
        }


        else if(formulario.valida.value == "2")
        {
             if(formulario.tipdocu.value == "N")
             {
                window.alert("El Tipo de Documento no Puede ser NIT")
                validacion = false
             }

             else if(formulario.apell1.value == "")
             {
                window.alert("El Apellido1 es Requerido")
                validacion = false
             }
             else
              {
                if (confirm("Seguro que Desea Ingresar el Cliente "+formulario.nom.value+"?"))
                {
                  formulario.opcion.value = 1;
                  formulario.submit();
                  return validacion
                }

              }
        }
        else if((formulario.valida.value == "1") || (formulario.valida.value == ""))
        {
            if(formulario.tipdocu.value == "C")
             {
                window.alert("El Tipo de Documento no Puede ser Cedula")
                validacion = false
             }
             else if(formulario.abr.value == "")
             {
                window.alert("La Abreviatura es Requierida")
                validacion = false
             }
              else
              {
                if (confirm("Seguro que Desea Actualizar el Cliente "+formulario.nom.value+"?"))
                {
                  formulario.opcion.value = 1;
                  formulario.submit();
                  return validacion
                }

              }
        }

        else
        {
                if (confirm("Seguro que Desea Actualizar el Cliente "+formulario.nom.value+"?"))
                {
                  formulario.opcion.value = 1;
                  formulario.submit();
                  return validacion
                }

        }
}
function acep_remite(formulario)
{
    validacion = true
    formulario = document.form_nivaut
    if (confirm("Seguro que Desea Ingresar los Remitentes para el Cliente "+formulario.nclient.value+"?"))
                {
                  formulario.opcion.value = 2;
                  formulario.submit();
                  return validacion
                }
}
function acep_destin(formulario)
{
    validacion = true
    formulario = document.form_nivaut
    if (confirm("Seguro que Desea Ingresar los Destinatarios para el Cliente "+formulario.nclient.value+"?"))
                {
                  formulario.opcion.value = 2;
                  formulario.submit();
                  return validacion
                }
}