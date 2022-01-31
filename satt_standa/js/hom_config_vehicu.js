function InsertHomolo()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_homolo = $("#cod_homoloID");
    var cod_clasex = $("#cod_clasexID");
    
    if( cod_homolo.val() == '' )
    {
      Alerta( 'Atenci\xf3n', 'Digite el C\xf3digo de Homologaci\xf3n', cod_homolo );
      return false;
    }
    else if( cod_clasex.val() == '' )
    {
      Alerta( 'Atenci\xf3n', 'Seleccione la Clase', cod_clasex );
      return false;
    }
    else
    {
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/infast/ajax_config_vehicu.php",
        data: "option=ValidateHomolo&cod_homolo="+cod_homolo.val() + "&cod_clasex="+cod_clasex.val(),
        async: false,
        success : 
          function ( data ) 
          {
            if( data == 'yes')
            {
              Alerta( 'Informaci\xf3n', 'La Configuracion Aplicada ya se encuentra Homologada', '' );
              return false;
            }
            else
            {
              NewHomolo();
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

function DeleteHomolo( $param )
{
  try
  {
    var attr = new Array( 2 );
    var Standa = $("#StandaID").val();
    
    $param.parent().siblings().each( function( index ) 
    {
      attr[index] = $( this ).text();
    });

    //console.log( attr );
    //return false;
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_config_vehicu.php",
      data: "option=DeleteHomolo&cod_homolo="+ attr[0] + "&cod_clasex="+attr[1],
      async: false,
      success : 
        function ( data ) 
        {
          MainLoad();
          if( data == 'y' )
          {
            $("#messageID").css({ "background": "none repeat scroll 0 0 #CAFFD7",
                                  "border": "1px solid #49E981",
                                  "border-radius": "4px 4px 4px 4px",
                                  "color": "#333333",
                                  "display": "none",
                                  "font-family": "Arial",
                                  "font-size": "12px",
                                  "padding": "10px",
                                  "width": "100%" 
                                });
            $("#messageID").html( "<span>Registro Eliminado Correctamente</span>");
          }
          else
          {
            $("#messageID").css({ "background":"none repeat scroll 0 0 #FFCECE",
                                  "border": "1px solid #E18F8E",
                                  "border-radius": "4px 4px 4px 4px",
                                  "color": "#333333",
                                  "display": "none",
                                  "font-family": "Arial",
                                  "font-size": "12px",
                                  "padding": "10px",
                                  "width": "100%" 
                                });
            $("#messageID").html( "<span>El Registro no fue Eliminado</span>");
          }
          $("#messageID").show( "slide" );
        }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function NewHomolo()
{
   try
  {
    var Standa = $("#StandaID").val();
    var cod_homolo = $("#cod_homoloID");
    var cod_clasex = $("#cod_clasexID");
  
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_config_vehicu.php",
      data: "option=InsertHomolo&cod_homolo="+cod_homolo.val() + "&cod_clasex="+cod_clasex.val(),
      async: false,
      success : 
        function ( data ) 
        {
          MainLoad();
          if( data == 'y' )
          {
            $("#messageID").css({ "background": "none repeat scroll 0 0 #CAFFD7",
                                  "border": "1px solid #49E981",
                                  "border-radius": "4px 4px 4px 4px",
                                  "color": "#333333",
                                  "display": "none",
                                  "font-family": "Arial",
                                  "font-size": "12px",
                                  "padding": "10px",
                                  "width": "100%" 
                                });
            $("#messageID").html( "<span>Registro Insertado Correctamente</span>");
          }
          else
          {
            $("#messageID").css({ "background":"none repeat scroll 0 0 #FFCECE",
                                  "border": "1px solid #E18F8E",
                                  "border-radius": "4px 4px 4px 4px",
                                  "color": "#333333",
                                  "display": "none",
                                  "font-family": "Arial",
                                  "font-size": "12px",
                                  "padding": "10px",
                                  "width": "100%" 
                                });
            $("#messageID").html( "<span>El Registro no fue Insertado</span>");
          }
          $("#messageID").show( "slide" );
        }
    });
  }
   catch( e )
  {
    console.log( e.message );
    return false;
  }
}


function MainLoad()
{
  try
  {
    var Standa = $("#StandaID").val();
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_config_vehicu.php",
      data: "option=MainLoad",
      async: false,
      beforeSend : 
        function () 
        { 
          $("#mainFormID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
        },
      success : 
        function ( data ) 
        { 
          $("#mainFormID").html( data );
           mainList();
        }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}


function mainList()
{
  try
  {
    var Standa = $("#StandaID").val();
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_config_vehicu.php",
      data: "option=mainList&Standa="+Standa,
      async: false,
      beforeSend : 
        function () 
        { 
          $("#mainListID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
        },
      success : 
        function ( data ) 
        { 
          $("#mainListID").html( data );
        }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
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
