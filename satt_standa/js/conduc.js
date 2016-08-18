function numero(val)
{
    return !(isNaN(val));
}

function volver(formulario)
{
             formulario = document.form_conduc
             formulario.opcion.value = 3;
             formulario.submit();

}

function validar_cedula(formulario)
{
    var terc = /^[0-9]{5,9}$/
    if (!terc.test(form_conduc.numdocu.value))
       {
           alert("La Cédula debe ser sin puntos(.), ni Guiones(-), ni Comas(,) ni Espacios");
           form_conduc.nit.focus()
           return false
       }
      return true
}

function aceptar_insert(formulario)
{
    validacion = true
    formulario = document.form_conduc

	    if(formulario.transp.value == 0)
        {
            window.alert("La Seleccion de Transportadora es Requerida")
                validacion = false
                formulario.transp.focus();
        }
        else if(formulario.tipdocu.value == 0)
        {
            window.alert("El tipo de Documento es requerido")
                validacion = false
                formulario.tipdocu.focus();
        }
        else if(formulario.tipdocu.value == "N")
        {
                window.alert("El Tipo de Documento no Puede ser NIT")
                validacion = false
                formulario.tipdocu.focus();
        }
        else if(formulario.numdocu.value == "" || !numero(formulario.numdocu.value))
        {
            window.alert("El Número del documento es Requerido")
                validacion = false
                formulario.numdocu.focus();
        }
        else if(formulario.nom.value == "")
        {
            window.alert("El Nombre es requerido")
                validacion = false
                formulario.nom.focus();
        }
        else if(formulario.apell1.value == "")
        {
            window.alert("El Primer Apellido es Requerido")
                validacion = false
                formulario.apell1.focus();
        }
        else if(formulario.dir1.value == "")
        {
            window.alert("La Dirección es Requerida")
                validacion = false
                formulario.dir1.focus();
        }
        else if(formulario.ciudad.value == 0)
        {
            window.alert("La Ciudad es Requerida")
                validacion = false
                formulario.ciudad.focus();
        }
        else if(formulario.tel.value == "" || !numero(formulario.tel.value))
        {
            window.alert("El Teléfono 1 es Requerido")
                validacion = false
                formulario.tel.focus();
        }
        else if(formulario.celu.value == "")
        {
            window.alert("El Teléfono Movil es Requerido")
                validacion = false
                formulario.celu.focus();
        }
        else if(!compar_fec(formulario.fec_actual.value,formulario.feclic.value))
        {
            window.alert("La Fecha de Vencimiento de la Licencia Debe ser Mayor a la Fecha Actual")
                validacion = false
                formulario.feclic.focus();
        }
        else if((formulario.pasjudi.value != "") && (!val_fectexto(formulario.fecpas.value)))
        {
            window.alert("La Fecha de Vencimiento del Pasado Judicial es Requerida")
                validacion = false
                formulario.fecpas.focus();
        }
        else
        {
                if (confirm("Seguro que Desea Ingresar el Conductor "+formulario.nom.value+" "+formulario.apell1.value+"?"))
                {
                  formulario.opcion.value = 1;
                  formulario.submit();
                  return validacion
                }

        }
}

function aceptar_update()
{
    validacion = true
    formulario = document.form_conduc

        if(formulario.tipdocu.value==0)
        {
            window.alert("El tipo de Documento es requerido")
                validacion = false
                formulario.tipdocu.focus();
        }
        else if(formulario.tipdocu.value == "N")
        {
                window.alert("El Tipo de Documento no Puede ser NIT")
                validacion = false
                formulario.tipdocu.focus();
        }
        else if(!formulario.nom.value)
        {
            window.alert("El Nombre es requerido")
                validacion = false
                formulario.nom.focus();
        }
        else if(!formulario.apell1.value)
        {
            window.alert("El Primer Apellido es Requerido")
                validacion = false
                formulario.apell1.focus();
        }
                else if(!formulario.dir1.value)
        {
            window.alert("La Dirección es Requerida")
                validacion = false
                formulario.dir1.focus();
        }
        else if(!formulario.ciudad.value)
        {
            window.alert("La Ciudad es Requerida")
                validacion = false
                formulario.ciudad.focus();
        }
        else if(!formulario.tel.value || !numero(formulario.tel.value))
        {
            window.alert("El Teléfono 1 es Requerido")
                validacion = false
                formulario.tel.focus();
        }
        else if(!formulario.celu.value)
        {
            window.alert("El Teléfono Movil es Requerido")
                validacion = false
                formulario.celu.focus();
        }
        else if(!compar_fec(formulario.fec_actual.value,formulario.feclic.value))
        {
            window.alert("La Fecha de Vencimiento de la Licencia Debe ser Mayor a la Fecha Actual")
                validacion = false
                formulario.feclic.focus();
        }
        else if((formulario.pasjudi.value != "") && (!val_fectexto(formulario.fecpas.value)))
        {
            window.alert("La Fecha de Vencimiento del Pasado Judicial es Requerida")
                validacion = false
                formulario.fecpas.focus();
        }
        else if (confirm("Seguro que Desea Actualizar el Conductor "+formulario.nom.value+"?"))
         {
                  formulario.opcion.value = 3;
                  formulario.submit();

                  return validacion
         }
}

function aceptar_delete()
{
    formulario = document.form_conduc

         if (confirm("Seguro que Desea Eliminar el Conductor "+formulario.nom.value+"?"))
         {
                  formulario.opcion.value = 3;
                  formulario.submit();
                  return true
         }
                    return false
}

function aceptar_lis(formulario)
{
    validacion = true
    formulario = document.form_list

        if(formulario.conduc.value == 0)
        {
            window.alert("El Nombre es requerido")
                validacion = false
        }
}