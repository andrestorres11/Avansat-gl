function CheckAll( cb_total )
{
  try
  {
    if( cb_total.is( ":checked" ) )
      $(".ck:checkbox:not(:checked)").attr("checked", "checked");
    else
      $(".ck:checkbox:checked").removeAttr("checked");
  }
  catch( e )
  {
    console.log( e.message );
  }
}

function ActualizaContacto()
{
  try
  {
    var total = $( "#totalID" ).val();
    var Standa = $( "#StandaID" ).val();
    var attr = "";
    var complement = "option=ActualizaContacto&Standa=" + Standa + "&tot=" + total;
    var llave;
    var ema_conpri;
    var ema_otrcon;
    var counter = 0;
    
    for( var i = 0; i < total; i++ )
    {
      llave = $( "#key_" + i + "ID" );
      if( llave.is( ":checked" ) )
      {
        ema_conpri = $( "#ema_conpri_" + i + "ID" );
        ema_otrcon = $( "#ema_otrcon_" + i + "ID" );
        attr += "&key[" + i +"]="+llave.val();
        attr += "&ema_conpri[" + i +"]="+ema_conpri.val();
        attr += "&ema_otrcon[" + i +"]="+ema_otrcon.val();
        counter++;
      }
    }
    
    if( attr == "" )
    { 
      Alerta( 'Atenci\xf3n', 'Seleccione por lo Menos una Fila', '' );
      return false;
    }
    else
    {
      $.ajax({
        url: "../" + Standa + "/protoc/ajax_protoc_matcom.php",
        data: complement + attr,
        type: "POST",
        async: true,
        beforeSend: function( obj )
        {
          var men = counter == 1 ? ' Correo' : ' Correos';
          $.blockUI({ 
            theme:     true, 
            title:     'Editar Correos Matriz de Comunicaci\xf3n',
            draggable: false,
            message:   '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Actualizando ' + counter + men + '</p></center>'
          });
        },
        success: function( data )
        {
          setTimeout(function(){ $.unblockUI() }, 3000);
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

function getData()
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    var nom_usuari = $("#nom_usuariID").val();
    var dir_coract = $("#dir_coractID").val();
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/protoc/ajax_protoc_matcom.php",
      data: "option=getData&Standa="+Standa+"&nom_usuari="+nom_usuari+"&dir_coract="+dir_coract,
      async: false,
      beforeSend : 
        function () 
        { 
          $.blockUI({ 
            theme:     true, 
            title:    'Editar Correos Matriz de Comunicaci\xf3n', 
            draggable: false,
            message:  '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Validando</p></center>'
          });
        },
      success : 
        function ( data ) 
        { 
          $.unblockUI();
          $("#nom_usuariID").val( data.split('|')[0] );
          $("#dir_coractID").val( data.split('|')[1] );
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

function Validate( )
{
  try
  {
    $("#resultID").html( "&nbsp" );
    var Standa = $( "#StandaID" ).val();
    var dir_coract = $("#dir_coractID");
    var dir_cornue = $("#dir_cornueID");
    var expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if( dir_coract.val() == '' )
    { 
      Alerta( 'Atenci\xf3n: Correo Actual', 'Digite el Correo Actual', dir_coract );
      return false;
    }
    else if( !expr.test( dir_coract.val() ) )
    {
      Alerta( 'Atenci\xf3n: Correo Actual', 'Direcci\xf3n de Correo "' + dir_coract.val() + '" Incorrecta', dir_coract );
      return false;
    }
    else if( dir_cornue.val() == '' )
    { 
      Alerta( 'Atenci\xf3n: Nuevo Correo', 'Digite el Correo Nuevo', dir_cornue );
      return false;
    }
    else if( !expr.test( dir_cornue.val() ) )
    {
      Alerta( 'Atenci\xf3n: Nuevo Correo', 'Direcci\xf3n de Correo "' + dir_cornue.val() + '" Incorrecta', dir_cornue );
      return false;
    }
    else
    {
      $.ajax({
        url: "../" + Standa + "/protoc/ajax_protoc_matcom.php",
        data: "option=getCorreos&dir_coract=" + dir_coract.val() + "&dir_cornue=" + dir_cornue.val(),
        type: "POST",
        async: true,
        beforeSend: function( obj )
        {
          $.blockUI({ 
            theme:     true, 
            title:     'Editar Correos Matriz de Comunicaci\xf3n',
            draggable: false,
            message:   '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Buscando Coincidencias</p></center>'
          });
        },
        success: function( data )
        {
          $.unblockUI();
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