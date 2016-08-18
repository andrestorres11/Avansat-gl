/*******************************************************************************************************
 * @brief Archivo Javascript para los eventos dinámicos del módulo de Cumplidos                        *
 * @version 0.1                                                                                        *
 * @ultima_modificacion 04 de Febrero de 2010                                                          *
 * @author Christiam Barrera Arango                                                                    *
 * @company Intrared.net LTDA                                                                          *
 *******************************************************************************************************/

//------------------------------------------------------------------------------------- 
function ShowSection( fIndex )
{
  try 
  {
    
  }
  catch( e )
  {
    alert( 'Error Function ShowSection : ' + e.message );
  }
}
function SelectTrasnp()
{
  var transp = document.form_item.cod_transp.value;
  document.form_item.option.value = '';
  document.form_item.submit();
  //alert(transp);
  
}

