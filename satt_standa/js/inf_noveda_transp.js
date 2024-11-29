document.addEventListener("DOMContentLoaded", () => {// también puede usar window.addEventListener('load', (event) => {
  BlocK();
});

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

      var des_transi = document.getElementById('des_transiID');
      var des_final = document.getElementById('des_finalID');
      
      valCheck = des_transi.checked || des_final.checked ? true: false;
      if (!valCheck) {
        alert('Debe marcar minimo un tipo de informe: transito / finalizados.');
        return des_transi.focus();
      }

      BlocK('Cargando Detalle...', 1);
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
  top.window.open("../"+dir_aplica.value+"/inform/inf_noveda_transp.php?option=expInformExcel");
}

function exportarXls(  )
{
  var dir_aplica = document.getElementById( 'dir_aplicaID' );
  var base_d = document.getElementById( 'basedID' );
  top.window.open("../"+dir_aplica.value+"/inform/inf_noveda_transp.php?option=expInformExcel");
}


function infoNoveda(tipo,usuari,fec_ini,fec_fin,des_transi,des_final){
  try
    {
      var url_archiv = document.getElementById( 'url_archivID' );
      var dir_aplica = document.getElementById( 'dir_aplicaID' );
      LoadPopup();
      var atributes  =  "option=getDetalles&tipo=" + tipo +"&cod_transp="+ usuari +"&fecha_ini=" + fec_ini +"&fecha_fin=" + fec_fin+"&des_transi="+des_transi+"&des_final="+des_final;
      AjaxGetData( "../"+dir_aplica.value+"/inform/"+url_archiv.value+"?", atributes, 'popupDIV', "post" );
    }
    catch (e)
    {
        alert("Error -> infoNoveda() " + e.message);
    }
}