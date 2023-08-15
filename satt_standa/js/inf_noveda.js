/**
 * @author jose.guerrero
 */

function Listar()
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
		  document.getElementById('opcionID').value = 2;
      document.getElementById('formularioID').submit();
  }
  catch (e)
  {
    alert( "Error Listar " + e.message);
  }
}


function infoNoveda(tipo,usuari,fec_ini,fec_fin,horini,horfin){
  try
    {
      var url_archiv = document.getElementById( 'url_archivID' );
      var dir_aplica = document.getElementById( 'dir_aplicaID' );
      LoadPopup();
      var atributes  = "opcion=3";
		  atributes += "&tipo=" + tipo +"&usuari="+ usuari +"&fec_inicial=" + fec_ini +"&fec_final=" + fec_fin;
       atributes += "&horaini=" + horini +"&horafin=" + horfin;
      AjaxGetData( "../"+dir_aplica.value+"/inform/"+url_archiv.value+"?", atributes, 'popupDIV', "post" );
    }
    catch (e)
    {
        alert("Error -> infoNoveda() " + e.message);
    }
}

function exportarXls(  )//Jorge 27-03-2012
{
  var dir_aplica = document.getElementById( 'dir_aplicaID' );
  var base_d = document.getElementById( 'basedID' );
  top.window.open("../"+dir_aplica.value+"/export/exp_inf_noveda.php?url="+base_d.value+"&db="+base_d.value);
}

function exportarXls2(  )
{
  var dir_aplica = document.getElementById( 'dir_aplicaID' );
  var base_d = document.getElementById( 'basedID' );
  top.window.open("../"+dir_aplica.value+"/export/exp_inf_noveda_total.php?url="+base_d.value+"&db="+base_d.value);
}