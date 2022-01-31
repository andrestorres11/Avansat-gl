function InsertHomolo()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_produc = $("#cod_producID");
    var cod_clasex = $("#cod_clasexID");
    var hor_estima = $("#hor_estimaID");
    
    if( cod_produc.val() == '' )
    {
      Alerta( 'Atenci\xf3n', 'Seleccione el Producto', cod_produc );
      return false;
    }
    else if( cod_clasex.val() == '' )
    {
      Alerta( 'Atenci\xf3n', 'Seleccione la Clase', cod_clasex );
      return false;
    }
    else if( hor_estima.val() == '' )
    {
      Alerta( 'Atenci\xf3n', 'Digite las Horas Estimadas', hor_estima );
      return false;
    }
    else
    {
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/infast/ajax_tiempo_logist.php",
        data: "option=ValidateHomolo&cod_produc=" + cod_produc.val().replace(/&/g,"/-/") + "&cod_clasex="+cod_clasex.val(),
        async: false,
        success : 
          function ( data ) 
          {
            if( data == 'yes')
            {
              Alerta( 'Informaci\xf3n', 'Los tiempos ya se encuentran registrados para esa combinaci\xf3n', '' );
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

function EditHomolo( $param )
{
  try
  {
    var Standa = $("#StandaID").val();
    var index = $param.attr('id').replace('DLLink','').split('-')[0];
    var cod_produc = $("#cod_produc" + index + "ID").val();
    var cod_clasex = $("#cod_clasex" + index + "ID").val();
    
    $("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Modificaci\xf3n de Tiempos",
      width: 800,
      heigth : 500,
      position:['middle',25], 
      bgiframe: true,
      closeOnEscape: false,
      show : { effect: "drop", duration: 300 },
      hide : { effect: "drop", duration: 300 },
      open: function(event, ui) { 
            $(this).parent().children().children('.ui-dialog-titlebar-close').hide();},
      buttons: {
        Guardar : function() 
        { 
          ChangeTiempos();
        },
        Eliminar : function() 
        { 
          DropTiempos();
        },
        Cerrar : function() 
        { 
          $(this).dialog('close');
          MainLoad();
        }
      }
    });
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_tiempo_logist.php",
      data: "option=FormEdit&Standa=" + Standa + "&cod_produc=" + cod_produc.replace(/&/g,"/-/") + "&cod_clasex=" + cod_clasex,
      async: false,
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
    return false;
  }
}

function DropTiempos()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_produc = $("#cod_produc_ID");
    var cod_clasex = $("#cod_clasex_ID");
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_tiempo_logist.php",
      data: "option=DropTiempos&cod_produc=" + cod_produc.val().replace(/&/g,"/-/") + "&cod_clasex="+cod_clasex.val(),
      async: false,
      success : 
        function ( data ) 
        {
          $("#PopUpID").dialog('destroy');
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

function ChangeTiempos()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_produc = $("#cod_produc_ID");
    var cod_clasex = $("#cod_clasex_ID");
    var hor_estima = $("#hor_estima_ID");
    
    if( hor_estima.val() == '' )
    {
      Alerta( 'Atenci\xf3n', 'Digite las Horas Estimadas', hor_estima );
      return false;
    }
    else
    {
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/infast/ajax_tiempo_logist.php",
        data: "option=ChangeTiempos&cod_produc=" + cod_produc.val().replace(/&/g,"/-/") + "&hor_estima="+hor_estima.val() + "&cod_clasex="+cod_clasex.val(),
        async: false,
        success : 
          function ( data ) 
          {
            $("#PopUpID").dialog('destroy');
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
              $("#messageID").html( "<span>Registro Actualizado Correctamente</span>");
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
              $("#messageID").html( "<span>El Registro no fue Actualizado</span>");
            }
            $("#messageID").show( "slide" );
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

function NewHomolo()
{
   try
  {
    var Standa = $("#StandaID").val();
    var cod_produc = $("#cod_producID");
    var cod_clasex = $("#cod_clasexID");
    var hor_estima = $("#hor_estimaID");
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_tiempo_logist.php",
      data: "option=InsertHomolo&cod_produc=" + cod_produc.val().replace(/&/g,"/-/") + "&hor_estima="+hor_estima.val() + "&cod_clasex="+cod_clasex.val(),
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
      url: "../"+ Standa +"/infast/ajax_tiempo_logist.php",
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
      url: "../"+ Standa +"/infast/ajax_tiempo_logist.php",
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
