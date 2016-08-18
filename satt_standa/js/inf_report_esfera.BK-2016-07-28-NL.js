function Validar()
{
var fec_inicio = document.getElementById("fec_incial");
var fec_finali = document.getElementById("fec_finali");
  
  if( !fec_inicio.value )
  {
    alert("Ingrese fecha inicial");
    fec_inicio.focus();
    return false;
  }
  else if( !fec_finali.value )
  {
    alert("Ingrese fecha final");
    fec_finali.focus();
    return false;
  }
  else if( fec_inicio.value > fec_finali.value )
  {
    alert("La fecha inicial debe ser menor a la fecha final");
    fec_inicio.focus();
    return false;
  }
  else
  {
    formulario.submit();
    return true;
  }

}