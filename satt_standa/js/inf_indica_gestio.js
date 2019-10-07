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

//------------------------------------------------------------------
//@funct.  :  exporExcel()
//@author  :  Ing. Luis Manrique
//@brief   :  Funcion que permite exportar tabla a excel.
//------------------------------------------------------------------
function exporExcel( idTable, idBoton, name )
{
  try 
  {
    var wb = XLSX.utils.table_to_book(document.getElementById(idTable), {sheet:"Sheet JS"});
    var wbout = XLSX.write(wb, {bookType:'xls', bookSST:true, type: 'binary'});
    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
    }
    //Asigna el evento
    saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), name+'.xls');
  }
  catch (e)
  {
    alert( "Error exporExcel(): " + e.message);
  }
}
//------------------------------------------------------------------
