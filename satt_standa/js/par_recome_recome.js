/*! \file: par_recome_recome.js
 *  \brief: js para par_recome_recome.php
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/*! \fn: MainLoad
 *  \brief: 
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function MainLoad()
{
  try
  {
    blockScreen( "Cargando..." );
    var Standa = $( "#StandaID" ).val();

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/recome/ajax_recome_recome.php",
      data: "option=MainLoad&standa="+Standa,
      async: false,
      success: function( datos )
      {
        UnblockScreen();
        $( "#mainDiv" ).html( datos );
      }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

/*! \fn: setEncBoceto
 *  \brief: 
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function setEncBoceto( elemento )
{
  try
  {
    $('label[for=val_itemxx_consec_ID]').html( elemento.val().toUpperCase() + ":&nbsp;&nbsp;" );
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

/*! \fn: setReqBoceto
 *  \brief: 
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function setReqBoceto( elemento )
{
  try
  {
    var valor = elemento.is(':checked') ? '*' : '';
    $('label[for=val_encabe_consec_ID]').html( valor + "&nbsp;" );
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

/*! \fn: DelRecomend
 *  \brief: Eliminar Recomendaciones
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function DelRecomend( elemento )
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    if( confirm("¿Realmente Desea Eliminar El Recomendado con el Consecutivo " + elemento + "?" ) )
    {
      blockScreen( "Cargando..." );

      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/recome/ajax_recome_recome.php",
        data: "option=DelRecomend&standa="+Standa+"&num_consec="+elemento,
        async: false,
        success: function( datos )
        {
          LoadPopupRecome('close');
          UnblockScreen();
          if( datos == '1000' )
            Alerta( "Correcto", "El Recomendado fue Eliminada Exitosamente", '', 'MainLoad' );
          else
            Alerta( "Atenci\xf3n", "El Recomendado no Pudo ser Eliminada. \n", '', 'MainLoad' );
            
        }
      });

    }
  }
  catch( e )
  {
    console.log( "Error Función DelRecomend: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}

/*! \fn: UpdRecomend
 *  \brief: Actualizar recomendaciones
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function UpdRecomend( elemento )
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    var tex_encabe = $("#texEncbeID").val();
    var ind_requer = $("#indRequID").is(':checked') == true ? '1' : '0';
    var des_texto = $("#des_textoID").val();

    if( confirm( "¿Desea Guardar la Nueva Configuracion del Recomendado " + elemento + "?" ) )
    {
      blockScreen( "Cargando..." );

      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/recome/ajax_recome_recome.php",
        data: "option=UpdRecomend&standa="+Standa+"&num_consec="+elemento+"&tex_encabe="+tex_encabe+"&ind_requer="+ind_requer+"&des_texto="+des_texto,
        async: false,
        success: function( datos )
        {
          LoadPopupRecome('close');
          UnblockScreen();
          if( datos == '1000' )
            Alerta( "Correcto", "El Recomendado fue Actualizado Exitosamente", "", "MainLoad" ); 
          else
            Alerta( "Atenci\xf3n", "El Recomendado no Pudo ser Actualizado, por Favor Intente Nuevamente", '', 'MainLoad' );
        }
      });
    }
  }
  catch( e )
  {
    console.log( "Error Función UpdRecomend: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}

/*! \fn: EditorRecome
 *  \brief: PopUp Editor de recomendaciones
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function EditorRecome( codRecome )
{
  try
  {
    var Standa = $( "#StandaID" ).val();

    LoadPopupRecome('open');
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/recome/ajax_recome_recome.php",
      data: "option=EditorRecome&standa="+Standa+"&num_consec="+codRecome.text(),
      beforeSend: function(){
        $("#FormContacID").html("<center>Cargando Formulario...</center>");
      },
      success: function(data){
        $("#FormContacID").html(data);
        CenterDIVRecome();
      } 
    });
  }
  catch(e)
  {
    console.log( "Error Función EditorRecome: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}

/*! \fn: CenterDIVRecome
 *  \brief: Centrar PopUp
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function CenterDIVRecome()
{
  var WindowH = $(window).width();
  var popupH =  $('.ui-dialog').outerWidth();
  var Left = ((WindowH - popupH) / 2);
  $(".ui-dialog").css({"width": ($(window).width() - 50 )+"px" , "left":"10px", top : "200px"});
}

/*! \fn: LoadPopupRecome
 *  \brief: Crea PopUp
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function LoadPopupRecome( type )
{
 try
 {
   if(type == 'open')
   {

     $('<div id="FormContacID"><center>Cargando...</center></div>').dialog({
       width : 100,
       heigth: 50,
       modal: true,
       closeOnEscape: false,
       resizable: false,
       draggable: false,
       close: function(){
         $("#FormContacID").dialog("destroy").remove();
       },
       buttons:{
         Cerrar:function(){
           LoadPopupRecome( "close" );
         }
       }
     });
   }
   else
   {
     $("#FormContacID").dialog("destroy").remove();
   }

 }
 catch(e)
 {
   alert("Error en:LoadPopupRecome "+e.message+"\nLine: "+e.lineNumber);
 }
}

/*! \fn: InsertEvento
 *  \brief: 
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function InsertEvento( sel )
{
  try
  {       
    var Standa = $( "#StandaID" ).val();
    var Html = $( "#ToRegID" ).html();
    
    var cod_tipoxx = $( "#cod_tipoxxID" ).val();
    var tex_encabe = $( "#tex_encabeID" ).val();
    var des_textox = $( "#des_textoxID" ).val();
    var ind_requer = $( "#ind_requerID" ).is(':checked')  ? '1' : '0';

    if( cod_tipoxx == '' )
    {
      Alerta( "Atenci\xf3n", "Seleccione el Tipo", $( "#cod_tipoxxID" ), '' );
      return false;
    }
    else if( tex_encabe == '' )
    {
      Alerta( "Atenci\xf3n", "Digite el Encabezado", $( "#tex_encabeID" ), '' );
      return false; 
    }
    else if( des_textox == '' )
    {
      Alerta( "Atenci\xf3n", "Digite el Texto", $( "#des_textoxID" ), '' );
      return false; 
    }
    else
    {
      blockScreen( "Cargando..." );
      if ( $("#formHtmlID").is (':visible') )
       $("#formHtmlID").hide( 'blind' );
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/recome/ajax_recome_recome.php",
        data: "option=InsertEvento&standa="+Standa+"&Html="+Html.replace( /&nbsp;/g, "/-/" )+"&cod_tipoxx="+cod_tipoxx+"&tex_encabe="+tex_encabe+"&des_textox="+des_textox+"&ind_requer="+ind_requer,
        async: false,
        success: function( datos )
        {
          UnblockScreen();
          if( datos )
            Alerta( "Correcto", "El Recomendado fue Insertada Exitosamente", '', 'MainLoad' );
          else
            Alerta( "Atenci\xf3n", "El Recomendado no Pudo ser Insertada Exitosamente, por Favor Intente Nuevamente", '', 'MainLoad' );
            
        }
      });
    }
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  } 
}

/*! \fn: SetForm
 *  \brief: 
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function SetForm( sel )
{
  try
  {
    if( sel.val() != '' )
    {
      blockScreen( "Cargando..." );
      var Standa = $( "#StandaID" ).val();
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/recome/ajax_recome_recome.php",
        data: "option=SetForm&standa="+Standa+"&sel="+sel.val(),
        async: false,
        success: function( datos )
        {
          UnblockScreen();
          
          $( "#tex_encabeID, #ind_requerID, #des_textoxID" ).show();
          
          $( "#tex_encabeID" ).val('');
          $( "#ind_requerID" ).removeAttr("checked");

          if ( $("#formHtmlID").is (':visible') )
            $("#formHtmlID").hide( 'blind' );
      
          $( "#formHtmlID" ).html( datos );
          $("#formHtmlID").show( 'blind' );
        }
      });
    }
    else
    {
      if ( $("#formHtmlID").is (':visible') )
            $("#formHtmlID").hide( 'blind' );
    }
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  } 
} 

/*! \fn: blockScreen
 *  \brief: 
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function blockScreen( msj )
{
  try
  {
  	$.blockUI({
  	  css: { 
  	  	top: '10px',
  	  	left: '', 
  	  	right: '10px',
  	  	border: 'none',   
        padding: '15px',      
        backgroundColor: '#001100',             
                         '-webkit-border-radius': '10px',             
                         '-moz-border-radius': '10px',             
        opacity: .7,             
        color: '#fff'         
      },
      centerY: 0, 
  	  message: "<h1>" + msj + "</h1>" 
  	}); 
  }
  catch( e )
  {
  	console.log( e.message );
  	return false;
  }
}

/*! \fn: InsertRango
 *  \brief: 
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function InsertRango()
{
  try
  {
    var med_rangox = $("#med_rangoxID").val();
    
    if( med_rangox != '' )
    {
      $('#rango_consec_ID').text( med_rangox.toUpperCase() );
      $("#med_rangoxID").val('');
    }
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

/*! \fn: InsertParame
 *  \brief: 
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function InsertParame()
{
  try
  {
    var par_insert = $("#par_insertID").val();
    
    if( par_insert != '' )
    {
      $('#val_itemxx_consec_ID').append( '<option value="'+ par_insert.toUpperCase() +'">'+ par_insert.toUpperCase() +'</option>' );
      $("#par_insertID").val('');
    }
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

/*! \fn: UnblockScreen
 *  \brief: 
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function UnblockScreen()
{
  try
  {
  	$.unblockUI(); 
  }
  catch( e )
  {
  	console.log( e.message );
  	return false;
  }
}

/*! \fn: Alerta
 *  \brief: 
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function Alerta( title, message, focus, adi_func )
{
  try
  {
    $("<div id='msgBox'>"+message+"</div>").dialog({
      modal    : true,
      resizable: false,
      draggable: false,
      title    : title,
      left: 190,
      open: function(event, ui) { 
              $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
            },
      buttons:  {  
                  Aceptar : function(){
                    $(this).dialog('destroy').remove();
                    
                    if( adi_func == 'MainLoad' )
                      MainLoad();
                    
                    if( focus != '' )
                    {
                      focus.focus();
                    }
                  }
                }
    });
  }
  catch( e )
  {
    console.log( e.message );
  }
}