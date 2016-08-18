

/**
 * @author jose.guerrero
 */

function borrar(){
  $(".ui-multiselect-header").children().next().children().html("");
}

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
      document.getElementById('optionID').value = 'getInforme';
      document.getElementById('formularioID').submit();
  }
  catch (e){
    alert( "Error Listar " + e.message);
  }
}


function Listar2()
{
  try {
      var fec_ini = document.getElementById('fec_corteID');
      if (fec_ini.value == '') {
        alert('La Fecha de Corte es Obligatoria');
        return fec_ini.focus();
      }
      document.getElementById('optionID').value = 'getInforme';
      document.getElementById('formularioID').submit();
  }
  catch (e)
  {
    alert( "Error Listar2 " + e.message);
  }
}




function infoNoveda(tipo,usuari,fec_ini,fec_fin){
  try
    {
      var url_archiv = document.getElementById( 'url_archivID' );
      var dir_aplica = document.getElementById( 'dir_aplicaID' );
      LoadPopup();
      var atributes  =  "option=getDetalles&tipo=" + tipo +"&cod_usuar="+ usuari +"&fecha_ini=" + fec_ini +"&fecha_fin=" + fec_fin;
      AjaxGetData( "../"+dir_aplica.value+"/inform/"+url_archiv.value+"?", atributes, 'popupDIV', "post" );
    }
    catch (e)
    {
        alert("Error -> infoNoveda() " + e.message);
    }
}


function infoNoveda2(tipo, usuari,fecha,hora, rango){
  try
    {
      var url_archiv = document.getElementById( 'url_archivID' );
      var dir_aplica = document.getElementById( 'dir_aplicaID' );
      LoadPopup();
      var atributes  =  "option=getDetalles&&cod_usuar="+ usuari +"&fecha_corte=" + fecha +"&hora_corte=" + hora + "&ran_corte="+ rango +"&tipo="+ tipo;
      AjaxGetData( "../"+dir_aplica.value+"/inform/"+url_archiv.value+"?", atributes, 'popupDIV', "post" );
    }
    catch (e)
    {
        alert("Error -> infoNoveda2() " + e.message);
    }
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
  top.window.open("../"+dir_aplica.value+"/inform/inf_numero_alarma.php?option=expInformExcel");
}
function pintarExcel(){
  var tabla = $('#TablaDetalle').parent().html();
  $('#datosPintarID').val(tabla);
  $("#formulario").submit();
}
$(function() { 
   $("#usuarioID").multiselect().multiselectfilter();   
  $("#perfilID").multiselect().multiselectfilter();   
  $( "#fec_iniciaID,#fec_finaliID" ).datepicker({
    dateFormat: "yy-mm-dd"
  });
  $( "#hor_iniciaID,#hor_finaliID" ).timepicker({
    timeFormat:"hh:00",
    showSecond: false,
    showMinute: false
  });
        
  $.mask.definitions["A"]="[12]";
  $.mask.definitions["M"]="[01]";
  $.mask.definitions["D"]="[0123]";
  $.mask.definitions["H"]="[012]";
  $.mask.definitions["N"]="[012345]";
  $.mask.definitions["n"]="[0123456789]";
  
  $( "#fec_inicialID,#fec_finalID" ).mask("Annn-Mn-Dn");
  $( "#horainiID,#horafinID" ).mask("Hn:Nn");

  $("#acordeonID").accordion({
        heightStyle: "content",
        collapsible: true,
  });
  $("#tabs").tabs({
        beforeLoad: function (event, ui) {
            ui.jqXHR.fail(function () {
                ui.panel.html("Cargado...");
            });
        }
  });
  $("#contenido").css("height", "auto");

  $("#liGenera").click(function () {
    try{
        var Standa = $("#standaID").val();
        var fec_inicia = $("#fec_iniciaID").val();
        var fec_finali = $("#fec_finaliID").val();
        var hor_inicia = $("#hor_iniciaID").val();
        var hor_finali = $("#hor_finaliID").val();

        var date_inicia = new Date(fec_inicia + "T" + hor_inicia +":00");
        var date_finali = new Date(fec_finali + "T" + hor_finali +":00");
        var tipo= $("#tipoID").val();
        var cod_usuari = 0;
        var cod_perfil = 0;
        if(tipo == 0){
          Alerta('Atenci\xf3n', 'Debe seleccionar el tipo de informe', $("#tipoID"));
          return false;
        }
        if(tipo == 1){
        var date1 = new Date(fec_inicia + "T" + hor_inicia +":00");
        var date2 = new Date(fec_inicia + "T" + hor_finali +":00");
          if(date1 > date2){
            Alerta('Atenci\xf3n', 'Para el reporte diario, la hora inicial no puede ser menor a la final', $("#hor_iniciaID"));
            return false;
          }
        }

        $("input[type=checkbox]:checked").each(function(i,o){
          
          if( $(this).attr("name") == 'multiselect_usuarioID' ){
            if( cod_usuari == 0 ){
              cod_usuari = '"'+ $(this).val() +'"';
            }else{
              cod_usuari += ',"'+ $(this).val() +'"';
            }
          }
          if($(this).attr("name") == 'multiselect_perfilID' ){
            if( cod_perfil == 0 ){
              cod_perfil =  $(this).val() ;
            }else{
              cod_perfil += ','+ $(this).val();
            }
          }
        });
        if(cod_usuari == 0 && cod_perfil == 0){
          Alerta('Atenci\xf3n', 'Debe seleccionar por lo menos un usuario o un perfil', $(".ui-multiselect-header"));
          return false;
        }
        if (date_inicia > date_finali){
            Alerta('Atenci\xf3n', 'La fecha Inicial no puede ser mayor a la fecha Final', $("#fec_iniciaID"));
            return false;
        }else{
            $.ajax({
                url: "../" + Standa + "/inform/ajax_inform_inform.php",
                data: "Option=getInform&Ajax=on&fec_inicia=" + fec_inicia + "&fec_finali=" + fec_finali+"&hor_inicia="+hor_inicia+"&hor_finali="+hor_finali+"&usuario="+cod_usuari+"&tipo="+tipo+"&perfil="+cod_perfil,
                type: "POST",
                async: true,
                beforeSend: function (obj){
                    $.blockUI({
                        theme: true,
                        title: 'Novedades por usuario',
                        draggable: false,
                        message: '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
                    });
                },
                success: function (data){
                    $.unblockUI();
                    $("#generaID").html(data);
                    $("#tabla").css('overflow', 'scroll');
                    $("#tabla").css('height', ( $(window).height() - 195 ) );
                }
            });
        }
    }catch (e){
        console.log("Error en liGenera.click() "+e.message);
    }
  });
  
});

function Alerta(title, message, focus){
    try
    {
        $("<div id='msgBox'>" + message + "</div>").dialog({
            modal: true,
            resizable: false,
            draggable: false,
            title: title,
            left: 190,
            open: function (event, ui) {
                $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            buttons: {
                Aceptar: function () {
                    $(this).dialog('destroy').remove();
                    if (focus != '')
                    {
                        focus.focus();
                    }
                }
            }
        });
    }
    catch (e)
    {
        console.log(e.message);
    }
}

/*! \fn: detalle()
 *  \brief: muestra el detalle de las novedades por usuario por dia
 *  \author: Ing. Alexander Correa
 *  \date: 01/12/2015
 *  \date modified: 01/12/2015
 *  \param: tipo 
 *  \param: fencha inicial
 *  \param: fencha final
 *  \param: usuarios
 *  \return html con los datos
 */

 function detalle(tipo,fec_inicia,fec_finali,usuarios){
      var Standa = $("#standaID").val();
      $.ajax({
          url: "../" + Standa + "/inform/ajax_inform_inform.php",
          data: "Option=getDetalle&Ajax=on&fec_inicia=" + fec_inicia + "&fec_finali=" + fec_finali+"&tipo="+tipo+"&usuarios="+usuarios+"&standa="+Standa,
          type: "POST",
          async: true,
          beforeSend: function (obj){
              $("#PopUpID").html('');
              $.blockUI({
                  theme: true,
                  title: 'Detalle de novedades por usuario',
                  draggable: false,
                  message: '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
              });
          },
          success: function (data){
              $.unblockUI();
            LoadPopupJQ('open', 'Detalle', ($(window).height() - 40), ($(window).width() - 40), false, false, true);
            var popup = $("#popID");
            popup.parent().children().children('.ui-dialog-titlebar-close').hide();
            popup.append(data);// //lanza el popUp
            $(".scroll").css('overflow', 'scroll');
            $(".scroll").css('height', ( $(window).height() - 195 ) );


          }
      });
 }

 function exportTableExcel( idTable ){
  $(".ui-button").trigger( "click" );
  try{
    $("#"+idTable).table2excel({
      exclude: ".noExl",
      name: "excel"
    });
  }
  catch(e)
  {
    console.log( "Error Function exportTableExcel: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}
