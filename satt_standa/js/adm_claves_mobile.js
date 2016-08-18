function Validate()
{
  try
  {
    var Standa = $("#standaID").val();
    var num_placax = $("#num_placaxID");

    if( num_placax.val() == '' )
    {
      Alerta( 'Atenci\xf3n', 'Por favor Digite la Placa', num_placax );
    }
    else
    {
      $.ajax({
        url: "../" + Standa + "/aplmov/ajax_claves_mobile.php",
        data : 'option=FormPassword&num_placax=' + num_placax.val(),
        method : 'POST',
        beforeSend : 
          function () 
          { 
            $("#resultID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
          },
        success : 
          function ( data ) 
          { 
            $("#resultID").html( data );
          }
      });
    }
  }
  catch( e )
  {
    console.log( e.message );
  }
  
}

function ValidatePassword()
{
  try
  {
    var Standa = $("#standaID").val();
    var nue_passwo = $("#nue_passwoID");
    var rep_passwo = $("#rep_passwoID");
    var num_despac = $("#num_despacID");

    if( nue_passwo.val() == '' )
    {
      nue_passwo.val(''); 
      rep_passwo.val('');
      Alerta( 'Atenci\xf3n', 'Por favor Digite la Nueva Clave', nue_passwo );
    }
    else if( rep_passwo.val() == '' )
    {
      nue_passwo.val(''); 
      rep_passwo.val('');
      Alerta( 'Atenci\xf3n', 'Por favor Confirme la Clave', rep_passwo );
    }
    else if( nue_passwo.val() != rep_passwo.val() )
    {
      nue_passwo.val(''); 
      rep_passwo.val(''); 
      Alerta( 'Atenci\xf3n', 'Las Claves No Coinciden', nue_passwo );
    }
    else
    {
      $.ajax({
        url: "../" + Standa + "/aplmov/ajax_claves_mobile.php",
        data : 'option=ChangeClave&num_despac=' + num_despac.val() + '&nue_passwo=' + nue_passwo.val(),
        method : 'POST',
        beforeSend : 
          function () 
          { 
            $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
          },
        success : 
          function ( data ) 
          { 
            $("#PopUpID").dialog('destroy');
            if( data == 'y' )
            {
              Alerta( 'Completo', 'La Clave para el Despacho: ' + num_despac.val() + ' Se Actualiz&oacute; Correctamente.', '' );
            }
            else
            {
              Alerta( 'Atenci\xf3n', 'La Clave No Pudo ser Actualizada', '' );
            }
          }
      });
    }
  }
  catch( e )
  {
    console.log( e.message );
  }
}


function setPassword( checked_element )
{
  try
  {
    var Standa = $("#standaID").val();
    $("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Cambiar Clave SAT Trafico Movil",
      width: $(document).width() - 200,
      heigth : 500,
      position:['middle',25], 
      bgiframe: true,
      closeOnEscape: false,
      buttons: {
          Aceptar : function() 
          { 
            ValidatePassword();
          },
          Cancelar : function() 
          { 
            $(this).dialog('close');
          }
        },
      show : { effect: "puff", duration: 300 },
      hide : { effect: "puff", duration: 300 }
    });
  
    $.ajax({
        url: "../" + Standa + "/aplmov/ajax_claves_mobile.php",
        data : 'option=newPassword&num_despac=' + checked_element.attr('id'),
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
          }
      });
  }
  catch( e )
  {
    console.log( e.message );
  }
}

function Alerta( title, message, focus )
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