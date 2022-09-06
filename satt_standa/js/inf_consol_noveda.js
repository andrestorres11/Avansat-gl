function ValidaInform()
{
  try {
		  var fec_ini = document.getElementById('fec_inicialID');
      if (fec_ini.value == '') {
        alert('La Fecha Inicial es Obligatoria');
        return fec_ini.focus();
      }
      var fec_final = document.getElementById('fec_finalID');
      if (fec_final.value == '') {
        alert('La Fecha Final es Obligatoria');
        return fec_final.focus();
      }
      document.getElementById('formularioID').submit();
  }
  catch (e)
  {
    alert( "Error Listar " + e.message);
  }
}

function exportarXls2(  )
{
  var dir_aplica = document.getElementById( 'dir_aplicaID' );
  var base_d = document.getElementById( 'basedID' );
  top.window.open("../"+dir_aplica.value+"/inform/inf_consol_noveda.php?option=expInformExcel");
}

function exportarXls(  )
{
  var dir_aplica = document.getElementById( 'dir_aplicaID' );
  var base_d = document.getElementById( 'basedID' );
  top.window.open("../"+dir_aplica.value+"/inform/inf_consol_noveda.php?option=expInformExcel");
}


function infoNoveda(ini,fin,usuari,fec_ini,fec_fin){
  try
    {
      var url_archiv = document.getElementById( 'url_archivID' );
      var dir_aplica = document.getElementById( 'dir_aplicaID' );
      LoadPopup();
      var atributes  =  "option=getDetalles&ini=" + ini +"&fin=" + fin +"&cod_transp="+ usuari +"&fecha_ini=" + fec_ini +"&fecha_fin=" + fec_fin;
      AjaxGetData( "../"+dir_aplica.value+"/inform/"+url_archiv.value+"?", atributes, 'popupDIV', "post" );
    }
    catch (e)
    {
        alert("Error -> infoNoveda() " + e.message);
    }
}