function Listar()
{
  var Standa = $("#standaID").val();
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
      document.getElementById('optionID').value = 'getInforme';
      
      document.getElementById('formularioID').submit();
      
  }
  catch (e){
    alert( "Error Listar " + e.message);
  }
}
function load(standa) { 
  $.blockUI({
    theme: true,
    title: 'Detalle de novedades por usuario',
    draggable: false,
    message: '<center><img src="../'+standa+'/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
  });
}
function exportarXls(  )//Jorge 27-03-2012
{
  var dir_aplica = document.getElementById( 'dir_aplicaID' );
  var base_d = document.getElementById( 'basedID' );
  top.window.open("../"+dir_aplica.value+"/inform/inf_tipoxx_noveda.php?option=expInformExcel");
}

function exportarXls2(  )
{
  var dir_aplica = document.getElementById( 'standaID' );
  var base_d = document.getElementById( 'basedID' );
  //var tabla = $('#TablaDetalle').html();
  top.window.open("../"+dir_aplica.value+"/inform/inf_tipoxx_noveda.php?option=expInformExcel");
}

function pintarExcel(){
  var tabla = $('#TablaDetalle').parent().html();
  $('#datosPintarID').val(tabla);
  $("#formulario").submit();
}

function infoNoveda(tipo,usuari,fec_ini,fec_fin){
  try
    {
      var url_archiv = document.getElementById( 'url_archivID' );
      var dir_aplica = document.getElementById( 'dir_aplicaID' );
      //console.log(url_archiv);
      var atributes  =  "option=getDetalles&tipo=" + tipo +"&cod_usuar="+ usuari +"&fecha_ini=" + fec_ini +"&fecha_fin=" + fec_fin;
      $.ajax({
        url: "../"+dir_aplica.value+"/inform/"+url_archiv.value,
        data: atributes,
        type: "POST",
        async: true,
        beforeSend: function (obj){
            $("#PopUpID").html('');
            $.blockUI({
                theme: true,
                title: 'Novedad',
                draggable: false,
                message: '<center><img src="../' + dir_aplica.value + '/imagenes/ajax-loader2.gif" /><p>Generando Detalle novedad</p></center>'
            });
        },
        success: function (data){
          //console.log(data);
            $.unblockUI();
            LoadPopupJQ('open', 'Detalle Novedad', ($(window).height() - 40), ($(window).width() - 40), false, false, true);
            var popup = $("#popID");
            popup.parent().children().children('.ui-dialog-titlebar-close').hide();
            popup.append(data);// //lanza el popUp
            $(".scroll").css('overflow', 'scroll');
            $(".scroll").css('height', ( $(window).height() - 195 ) );
        }
      });

      /*LoadPopup();
      var atributes  =  "option=getDetalles&tipo=" + tipo +"&cod_usuar="+ usuari +"&fecha_ini=" + fec_ini +"&fecha_fin=" + fec_fin;
      console.log(atributes);
      AjaxGetData( "../"+dir_aplica.value+"/inform/"+url_archiv.value+"?", atributes, 'popupDIV', "post" );*/
    }
    catch (e)
    {
        alert("Error -> infoNoveda() " + e.message);
    }
}