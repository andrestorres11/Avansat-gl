/*******************************************************************************************************
 * @brief Archivo Javascript para los eventos dinámicos del módulo de calificación                     *
 * @version 0.1                                                                                        *
 * @ultima_modificacion 30 de Mayo de 2013                                                             *
 * @author Nelson Gabriel Liberato                                                                     *
 * @company Intrared.net LTDA                                                                          *
 *******************************************************************************************************/

function savior() {
  document.getElementById("rndcID").parentNode.style.display="block";
  document.getElementById("saviorID").style.display="none";
}
 
//------------------------------------------------------------------------------------- 
function ShowSection( fIndex )
{
  try 
  {
    var fSection = document.getElementById( "section" + fIndex + "ID" );
    var fLink = document.getElementById( "SectionLink" + fIndex + "ID" );
    switch ( fIndex ) 
    {
      case '1' : var fLabel = "Listado de Manifiestos Pendientes por Cumplir"; break;
      case '2' : var fLabel = "Datos Básicos"; break;
      case '3' : var fLabel = "Datos del Pago"; break;
      case '4' : var fLabel = "Detalle de Manifiesto"; break;
      case '5' : var fLabel = "Detalle de Remisiones"; break;
      case '6' : var fLabel = "Observaciones"; break;
    }
    if ( fSection.style.display == "none" ) 
    {
      fSection.style.display = "block";
      fLink.title = "Ocultar Sección";
      fLink.innerHTML = "- " + fLabel;
    }
    else 
    {
      fSection.style.display = "none";
      fLink.title = "Desplegar Sección";
      fLink.innerHTML = "+ " + fLabel;
    }
  }
  catch( e )
  {
    alert( 'Error Function ShowSection : ' + e.message );
  }
}


function DrawList()
{
  try
  {
    var fStandar    =  document.getElementById( 'standarID' ).value;
    var num_placax  =  document.getElementById( 'num_placaxID' );
    var cod_ciuori  =  document.getElementById( 'cod_ciuoriID' );
    var cod_ciudes  =  document.getElementById( 'cod_ciudesID' );
    var cod_agedes  =  document.getElementById( 'cod_agedesID' );
    
    var atributes = "Ajax=on&Case=draw_list";
    atributes    += '&num_placax=' + num_placax.value;
    atributes    += '&cod_ciuori=' + cod_ciuori.value;
    atributes    += '&cod_ciudes=' + cod_ciudes.value;
    atributes    += '&cod_agedes=' + cod_agedes.value;
    
    AjaxGetDataLocked( '../' + fStandar + '/califi/ajax_califi_conduc.php?', atributes, 'ContainerDIV', 'post' );
  }
  catch( e )
  {
    alert( 'Error Function DrawList : ' + e.message );
  }
}


function SendManifi( fSource )
{
  try
  {
    var frm_cumpli  =  document.getElementById( 'frm_cumpliID' );
    var cod_manifi  =  document.getElementById( 'cod_manifiID' );
    var fAction     =  document.getElementById( 'ActionID' );    
    cod_manifi.value = trim( fSource.innerHTML );
    fAction.value    = 'template';
    frm_cumpli.submit();
  }
  catch( e )
  {
    alert( 'Error Function SendManifi : ' + e.message );
  }
}


function InsertCumpli()
{
  try
  {
    var frm_cumpli  =  document.getElementsByName( 'form_ins' );
    var num_califi  =  document.getElementById( 'num_califiID' );
    var obs_califi  =  document.getElementById( 'obs_califiID' );
    var nom_conduc  =  document.getElementById( 'nom_conducID' );
    var ape_conduc  =  document.getElementById( 'ape_conducID' );
    
   
    if ( !num_califi.value )
    {
      
      alert( 'Seleccione una calificación para el conductor.' );
      
      num_califi.focus();
      return false;
    }
    if ( !obs_califi.value )
    {
     
      alert( 'Digite una observación para el conductor.' );
      
      obs_califi.focus();
      return false;
    }
    
    var fConfirm = '¿Desea calificar al conductor: '+nom_conduc.value+' '+ape_conduc.value+'?';
    if ( confirm( fConfirm ) )
    {
     document.getElementById( 'opcionID' ).value = '2';
     document.form_ins.submit();
    }
    
  }
  catch( e )
  {
    alert( 'Error Function InsertCumpli : ' + e.message );
  }
}        

