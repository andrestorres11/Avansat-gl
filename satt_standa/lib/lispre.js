function validar(cant)
{
          var i;
          var counter=0;
          var frm = document.forms[0];
          var chi = frm.checkeditems;
          var offset = 0;

          while (frm.elements[offset].type!="checkbox")
          offset++;

          for (i=0;i < cant; i++){

            if (frm.elements[offset].checked) {
              counter++;
            }
            offset++;

          } // next (i)

          if (counter==0){
           return (false);
          }
          else
          {
           return(true);
          }
}
function insertar_tarifa(formulario)
{

    formulario = document.form_lispre
           if (confirm('Esta Seguro que Desea Insertar el Servicio?'))
               {
            formulario.opcion.value = 4;
                formulario.submit();
                }

}
function cotizar(formulario)
{
  formulario = document.form_cotiza
  if(!validar(formulario.maximos.value))
    {
            alert("Debe Seleccionar Minimo un Servicio, de Acuerdo a la Clase\n o Carroceria del Vehículo")
    }
  else{
         if (confirm('Esta Seguro que Desea Ingresar la(s) Cotizaciones?'))
               {
                formulario.opcion.value = 3;
                formulario.submit();
                }
      }

}

function productos(formulario)
{
  validacion = true
  formulario = document.form_cotiza
  formulario.opcion.value = 5;
  formulario.submit();
  return validacion;
}

function actualizar_tarifa(formulario)
{
  formulario = document.form_lispre1
  formulario.opcion.value =4;
  formulario.submit();

}

function listar_producto(formulario)
{
   formulario = document.form_lista
  if(!validar(formulario.maximo.value))
    {
            alert("Debe Seleccionar Minimo un Servicio")
    }
  else{
      formulario.opcion.value = 2;
      formulario.submit();
      }

}

function listar_combo(formulario)
{
  validacion = true
  formulario = document.form_lista
  formulario.opcion.value = 6;
  formulario.submit();
  return validacion;
}

function validar_insertar(formulario)
{
    validacion = true
    formulario = document.form_lispre
        if(formulario.nombre.value == "")
        {
            window.alert("El Nombre es requerido")
                validacion = false
        }
        else if(formulario.tipser.value == 0)
        {
            window.alert("El Tipo de Servicio es Requerido")
                validacion = false
        }
          else if(formulario.uniser.value == 0)
        {
            window.alert("La Unidad de Servicio es Requerida")
                validacion = false
        }
        else if(formulario.origen.value == 0)
        {
            window.alert("La Ciudad de Origen es Requerida")
                validacion = false
        }
        else if(formulario.destino.value == 0)
        {
            window.alert("La Ciudad de Destino es Requerida")
                validacion = false
        }

        else
        {
                if (confirm("Seguro que Desea Ingresar La Tarifa "+formulario.nombre.value+"?"))
                {
                  formulario.opcion.value = 2;
                  formulario.submit();
                  return validacion
                }

        }
}

function validar_actualizar(formulario)
{
    validacion = true
    formulario = document.form_lispre
        if(formulario.nombre.value == "")
        {
            window.alert("El Nombre es requerido")
                validacion = false
        }
        else if(formulario.tipser.value == 0)
        {
            window.alert("El Tipo de Servicio es Requerido")
                validacion = false
        }
        else if(formulario.origen.value == 0)
        {
            window.alert("La Ciudad de Origen es Requerida")
                validacion = false
        }
        else if(formulario.destino.value == 0)
        {
            window.alert("La Ciudad de Destino es Requerida")
                validacion = false
        }
        else if(formulario.unidad.value == 0)
        {
            window.alert("La Unidad es Requerida")
                validacion = false
        }
        else
        {
                if (confirm('Esta Seguro que Desea Actualizar el Servicio?'))
                {

                formulario.opcion.value = 2;
                formulario.submit();
                return validacion
                }

        }
}