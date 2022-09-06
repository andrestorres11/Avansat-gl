function Validate()
{
  fec_inicia = document.getElementById("fec_iniciaID");
  fec_finali = document.getElementById("fec_finaliID");
  option = document.getElementById("optionID");
  
  if( !fec_inicia.value )
  {
    alert("Seleccione la Fecha Inicial");
    fec_inicia.focus();
    return false;
  }
  else if( !fec_finali.value )
  {
    alert("Seleccione la Fecha Final");
    fec_finali.focus();
    return false;
  }
  else if( fec_inicia.value > fec_finali.value)
  {
    alert("La Fecha Inicial no debe ser Mayor a la Fecha Final");
    fec_inicia.focus();
    return false;
  }
  else
  {
    option.value = 'result';
    form.submit();
  }
}

function Details( hora, tercer, fecha )
{  
  try
  {
    var url_archiv = document.getElementById( 'url_archivID' );
    var dir_aplica = document.getElementById( 'dir_aplicaID' );
    var atributes  =  "option=details&hora="+ hora +"&cod_usuari="+ tercer +"&fecha="+ fecha;
    $( "#popupDIV" ).html( "<center><img src='http://www.anieto2k.com/wp-content/uploads/2006/03/loading3.gif' /></center>" );
    AjaxGetData( "../"+dir_aplica.value+"/inform/"+url_archiv.value, atributes, 'popupDIV', "post" );
    $( "#popupDIV" ).dialog( "open" );
  }
  catch (e)
  {
    alert("Error -> infoNovedades() " + e.message);
  }
}

function exportarXls(  )//Jorge 27-03-2012
{
  var dir_aplica = document.getElementById( 'dir_aplicaID' );
  var base_d = document.getElementById( 'basedID' );
  //top.window.open("../"+dir_aplica.value+"/inform/inf_tipoxx_noveda.php?option=expInformExcel");
  top.window.open("../"+dir_aplica.value+"/inform/inf_proyec_usuari.php?option=xls");
}