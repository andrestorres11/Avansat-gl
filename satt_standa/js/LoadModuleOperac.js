/*-------------------------------------------------------------------
#@company: Intrared.net                                         -----
#author: Felipe Malaver (felipe.malaver@intrared.net )          -----       
#@date:  2013-12-12                                             -----                                
#@brief: Script que realiza todas las validaciones y métodos    -----                                          
#        AJAX del módulo 'Operaciones'                          -----                                          
#------------------------------------------------------------------*/

$(document).ready(function() 
{
  var Standa = $( "#StandaID" ).val();
  var filter = $( "#filterID" ).val();
  
  $( "#cod_transpID" ).autocomplete({
    source: "../"+ Standa +"/centro/ajax_centro_operac.php?option=getTransp&standa="+Standa+"&filter="+filter,
    minLength: 2, 
    delay: 100
  });
  
});

function ClearContact()
{
  $("#nom_contacID,#car_contacID,#ema_contacID,#dir_contacID,#tel_contacID,#cod_tipdesID,#cod_ciuconID").css("border","0.5px solid #eeeeee");
  $("#error").remove();
}

function SendContact( option, cod_cennot, contact_id )
{  
  try
  {
    var Standa = $("#StandaID").val();
    var attribs = 'option=InsertContact&standa=' + Standa +'&opc=' + option;
    if( option != 'delete' )
    {
      var style = "style='color:#285C00; padding: 0px 4px; position:absolute; font-family:Trebuchet MS, Verdana, Arial; font-size:10px;'";
      var emailreg = /^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/;
      ClearContact();
      
      if( $("#nom_contacID").val() == '' )
      {
        $("#nom_contacID").css("border","2px solid #285C00");
        $("#nom_contacID").focus().after("<span id='error'" + style + ">Obligatorio</span>");
        return false;
      }
      else if( $("#car_contacID").val() == '' )
      {
        $("#car_contacID").css("border","2px solid #285C00");
        $("#car_contacID").focus().after("<span id='error'" + style + ">Obligatorio</span>");
        return false;
      }
      else if( $("#ema_contacID").val() == '' )
      {
        $("#ema_contacID").css("border","2px solid #285C00");
        $("#ema_contacID").focus().after("<span id='error'" + style + ">Obligatorio</span>");
        return false;
      }
      else if( !emailreg.test($("#ema_contacID").val()) )
      {
        $("#ema_contacID").css("border","2px solid #285C00");
        $("#ema_contacID").focus().after("<span id='error'" + style + ">Correo Incorrecto. Ej: prueba@mail.com</span>");
        return false;
      }
      else if( $("#dir_contacID").val() == '' )
      {
        $("#dir_contacID").css("border","2px solid #285C00");
        $("#dir_contacID").focus().after("<span id='error'" + style + ">Obligatorio</span>");
        return false;
      }
      else if( $("#tel_contacID").val() == '' )
      {
        $("#tel_contacID").css("border","2px solid #285C00");
        $("#tel_contacID").focus().after("<span id='error'" + style + ">Obligatorio</span>");
        return false;
      }
      else if( isNaN( $("#tel_contacID").val() ) )
      {
        $("#tel_contacID").css("border","2px solid #285C00");
        $("#tel_contacID").focus().after("<span id='error'" + style + ">Campo Num&eacute;rico</span>");
        return false;
      }
      else if( $("#cod_tipdesID").val() == '' )
      {
        $("#cod_tipdesID").css("border","2px solid #285C00");
        $("#cod_tipdesID").focus().after("<span id='error'" + style + ">Obligatorio</span>");
        return false;
      }
      else if( $("#cod_ciuconID").val() == '' )
      {
        $("#cod_ciuconID").css("border","2px solid #285C00");
        $("#cod_ciuconID").focus().after("<span id='error'" + style + ">Obligatorio</span>");
        return false;
      }
      else
      {
        
        attribs += '&nom_contac=' + $("#nom_contacID").val();
        attribs += '&car_contac=' + $("#car_contacID").val();
        attribs += '&ema_contac=' + $("#ema_contacID").val();
        attribs += '&dir_contac=' + $("#dir_contacID").val();
        attribs += '&tel_contac=' + $("#tel_contacID").val();
        attribs += '&cod_tipdes=' + $("#cod_tipdesID").val();
        attribs += '&cod_ciucon=' + $("#cod_ciuconID").val();
      }
    }
    
    attribs += '&transp=' + $("#transpID").val();
    attribs += '&cod_cennot=' + cod_cennot;
    attribs += '&contact_id=' + contact_id;

      $.ajax({
        url: "../"+ Standa +"/centro/ajax_centro_operac.php",
        data : attribs,
        method : 'POST',
        beforeSend : 
          function () 
          { 
            $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
          },
        success : 
          function ( data ) 
          { 
            //$("#PopUpID").html( data );
            $("#PopUpID").dialog('close');
            ShowAllNotify( $("#transpID").val() );
            
          }
      });  
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function Contact( option, cod_transp, cod_cennot, contact_id )
{
  var title_popup;
  if( option == 'insert' )
    title_popup = 'Inserci\xf3n';
  else if(  option == 'update' )
    title_popup = 'Actualizaci\xf3n';
  else if(  option == 'delete' )
    title_popup = 'Eliminaci\xf3n';
  try
  {
    var Standa = $("#StandaID").val();
    var cod_transp = $("#transpID").val();

    $("#PopUpID").dialog({
        modal : true,
        resizable : false,
        draggable: false,
        title: title_popup + " de Contacto",
        width: $(document).width() - 400,
        heigth : 500,
        position:['middle',25], 
        bgiframe: true,
        closeOnEscape: false,
        show : { effect: "drop", duration: 300 },
        hide : { effect: "drop", duration: 300 },
        open: function(event, ui) { 
              $(this).parent().children().children('.ui-dialog-titlebar-close').hide();},
        buttons: {
          Continuar : function() 
          { 
            SendContact( option, cod_cennot, contact_id );
          },
          Cerrar : function() 
          { 
            $(this).dialog('close');
            ShowAllNotify( $("#transpID").val() );
          }
        }
      });

      $.ajax({
      url: "../"+ Standa +"/centro/ajax_centro_operac.php",
      data : 'standa=' + Standa +'&option=FormManageContact&cod_transp=' + cod_transp + '&opc=' + option + '&cod_transp=' + cod_transp + '&cod_cennot=' + cod_cennot  + '&contact_id=' + contact_id,
      method : 'POST',
      beforeSend : 
        function () 
        { 
          $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
        },
      success : 
        function ( data ) 
        { 
          $("#PopUpID").html( data );
          ClearContact();
        }
    }); 
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function Manage( option, id_notify )
{
  var title_popup;
  if( option == 'insert' )
    title_popup = 'Inserci\xf3n';
  else if(  option == 'update' )
    title_popup = 'Actualizaci\xf3n';
  else if(  option == 'delete' )
    title_popup = 'Eliminaci\xf3n';
  try
  {
    var Standa = $("#StandaID").val();
    var cod_transp = $("#transpID").val();

    $("#PopUpID").dialog({
        modal : true,
        resizable : false,
        draggable: false,
        title: title_popup + " de Operador",
        width: $(document).width() - 700,
        heigth : 500,
        position:['middle',25], 
        bgiframe: true,
        closeOnEscape: false,
        show : { effect: "drop", duration: 300 },
        hide : { effect: "drop", duration: 300 },
        open: function(event, ui) { 
              $(this).parent().children().children('.ui-dialog-titlebar-close').hide();},
        buttons: {
          Continuar : function() 
          { 
            SendNotify( option );
          },
          Cerrar : function() 
          { 
            $(this).dialog('close');
            ShowAllNotify( $("#transpID").val() );
          }
        }
      });

      $.ajax({
      url: "../"+ Standa +"/centro/ajax_centro_operac.php",
      data : 'standa=' + Standa +'&option=FormManageNotify&id_notify=' + id_notify + '&opc=' + option + '&cod_transp=' + cod_transp,
      method : 'POST',
      beforeSend : 
        function () 
        { 
          $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
        },
      success : 
        function ( data ) 
        { 
          $("#PopUpID").html( data );
          ClearForm();
        }
    }); 
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function SendNotify( option )
{  
  try
  {
    var Standa = $("#StandaID").val();
    var attribs = 'option=InsertCennot&standa=' + Standa +'&opc=' + option;
    
    if( option != 'delete' )
    {
      var style = "style='color:#285C00; padding: 0px 4px; position:absolute; font-family:Trebuchet MS, Verdana, Arial; font-size:10px;'";
      ClearForm();
      
      if( $("#nom_cennotID").val() == '' )
      {
        $("#nom_cennotID").css("border","2px solid #285C00");
        $("#nom_cennotID").focus().after("<span id='error'" + style + ">Obligatorio</span>");
        return false;
      }
      else if( $("#des_cennotID").val() == '' )
      {
        $("#des_cennotID").css("border","2px solid #285C00");
        $("#des_cennotID").focus().after("<span id='error'" + style + ">Obligatorio</span>");
        return false;
      }
      else
      {
        
        attribs += "&nom_cennot=" + $("#nom_cennotID").val(); 
        attribs += "&des_cennot=" + $("#des_cennotID").val(); 
      }
    }
    attribs += "&notify=" + $("#notifyID").val(); 
    attribs += "&transp=" + $("#transpID").val(); 
    
    $.ajax({
      url: "../"+ Standa +"/centro/ajax_centro_operac.php",
      data : attribs,
      method : 'POST',
      beforeSend : 
        function () 
        { 
          $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
        },
      success : 
        function ( data ) 
        { 
          //$("#PopUpID").html( data );
          $("#PopUpID").dialog('close');
          ShowAllNotify( $("#transpID").val() );
          
        }
    });  
      
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function ClearForm()
{
  $("#nom_cennotID,#des_cennotID").css("border","0.5px solid #eeeeee");
  $("#error").remove();
}

function ShowAllNotify( cod_transp )
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    $("#transpID").val( cod_transp );

    $("#resultID").css( {'background-color':'#f0f0f0', 'border':'1px solid #c9c9c9','padding':'5px', 'width':'98%', 'min-height':'50px','-moz-border-radius':'5px 5px 5px 5px', '-webkit-border-radius':'5px 5px 5px 5px', 'border-top-left-radius':'5px', 'border-top-right-radius':'5px', 'border-bottom-right-radius':'5px', 'border-bottom-left-radius':'5px'} );
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/centro/ajax_centro_operac.php",
      data: "option=LoadNotify&standa="+Standa+"&cod_transp="+cod_transp,
      async: false,
      beforeSend: function()
      {
        $("#resultID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function( datos )
      {
        $("#resultID").html( datos );
      }
    });
    
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function ValidateTransp()
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    var filter = $( "#filterID" ).val();
    var cod_transp = $( "#cod_transpID" ).val();
    var transp
    if( !cod_transp )
    {
      alert("Digite la Transportadora");
      return false;
    }
    else
    {
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/centro/ajax_centro_operac.php",
        data: "option=ValidateTransp&standa="+Standa+"&filter="+filter+"&cod_transp="+cod_transp.split('-')[0],
        async: false,
        success: function( datos )
        {
          if( datos == 'n' )
          {
            alert("La Transportadora < "+ cod_transp +" > no existe");
            return false;
          }
          else
          {
            ShowAllNotify( cod_transp.split('-')[0] );
          }
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

