/*******************************************************************************************************
 * @brief Archivo Javascript para los eventos din?micos del m?dulo de Cumplidos                        *
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
  //document.form_item.option.value = '';
  //document.form_item.submit();
  //alert(transp);
  var standa = "satt_standa";
  var parametros = "option=getGenera&cod_transp="+transp+"&ajax=on";
  $.ajax({
      url: "../" + standa + "/inform/inf_diario_trazab.php?" + parametros,
      async: false,
      type: "POST",
      dataType: "html",
      
      success: function(data) {
          $('#generadorDivID').empty();

          $('#generadorDivID').html(data);
          $('#generadorDivID').attr('style','height:20px');
          
          

          $("#generadorID").multiselect();
          $('#generadorID_input').attr('style','height:20px;width:70%');
          $('#generadorID_input').attr("autocomplete","off");
          $('.multiselect-dropdown-arrow').attr('style','margin-top:9px');
        }
    });


}

