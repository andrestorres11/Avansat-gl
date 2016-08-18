//------------------------------------------------------------------
//@funct.  :  ChangeDespacServicio()
//@author  :  MIGUEL A. GARCIA [ MIK ]
//@brief   :  Funcion que cambia el valor del campo correspondiente al servicio
//            que se debe mostrar en el informe.
//------------------------------------------------------------------
function ChangeDespacServicio( servicio )
{
  try 
  {
    formulario = document.getElementById('formularioID');

    if ( servicio ) 
    {
      formulario.opcion.value = 2;
      formulario.servicio.value = servicio;
    }
    formulario.submit();
  }
  catch (e)
  {
    alert( "Error ChangeDespacServicio(): " + e.message);
  }
}
//------------------------------------------------------------------
