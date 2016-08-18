function ValidaFiltros()
{
  var placax = document.getElementById("num_placaxID");
  var despac = document.getElementById("num_despacID");
  if( !placax.value && !despac.value )
  {
    var filfec = document.getElementById("ind_fecID");
    if( !filfec.checked )
    {
      alert( "Debe Filtar por fechas" );
      filfec.checked = true;
      return false;      
    }
    else
    {
      var fecini = document.form_insert.fecini;
      var fecfin = document.form_insert.fecfin;
      if( !fecini.value )
      {
        alert( "Seleccione Fecha Inicial" );
        fecini.focus();
        return false;
      }
      else if( !fecfin.value )
      {
        alert( "Seleccione Fecha Final" );
        fecfin.focus();
        return false;
      }
      else
        form_insert.submit();
    }
  }
  else
  {
    form_insert.submit();
  }
}