function ChangeZona()
{
  try
  {
    var cod_zonaxx = $("#cod_zonaxx_ID");
    var nom_zonaxx = $("#nom_zonaxx_ID");
    var ind_estado = $("#ind_estado_ID");
    var Standa = $("#StandaID").val();
    
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/zonasx/ajax_zonasx_zonasx.php",
        data: "option=EditZona&cod_zonaxx="+$("#cod_zonaxx_ID").val()+"&nom_zonaxx="+nom_zonaxx.val()+"&ind_estado="+ind_estado.val(),
        async: false,
        success : 
          function ( data ) 
          {
            $("#PopUpID").dialog('close');
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
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function DropZona()
{
  try
  {
    var cod_zonaxx = $("#cod_zonaxx_ID");
    var Standa = $("#StandaID").val();
    
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/zonasx/ajax_zonasx_zonasx.php",
        data: "option=DropZona&cod_zonaxx="+$("#cod_zonaxx_ID").val(),
        async: false,
        success : 
          function ( data ) 
          {
            $("#PopUpID").dialog('close');
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

function EditZona( num_zonaxx )
{
  try
  {
    var cod_zonaxx = num_zonaxx.text();
    var Standa = $("#StandaID").val();
    
    $("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Modificaci\xf3n de Zona",
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
          ChangeZona();
        },
        Eliminar : function() 
        { 
          DropZona();
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
      url: "../"+ Standa +"/zonasx/ajax_zonasx_zonasx.php",
      data: "option=FormEdit&Standa="+Standa+"&cod_zonaxx="+cod_zonaxx,
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

function InsertZona()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_zonaxx = $("#cod_zonaxxID");
    var nom_zonaxx = $("#nom_zonaxxID");
    
    if( cod_zonaxx.val() == '' )
    {
      alert("Digite el Codigo de la Zona");
      cod_zonaxx.focus();
      return false;
    }
    else if( nom_zonaxx.val() == '' )
    {
      alert("Digite el Nombre de la Zona");
      nom_zonaxx.focus();
      return false;
    }
    else
    {
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/zonasx/ajax_zonasx_zonasx.php",
        data: "option=ValidateZona&cod_zonaxx="+$("#cod_zonaxxID").val(),
        async: false,
        success : 
          function ( data ) 
          {
            if( data == 'yes')
            {
              alert("La Zona con el Codigo: "+cod_zonaxx.val()+", ya existe");
              cod_zonaxx.focus();
              return false;
            } 
            else
            {
              NewZona();
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

function NewZona()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_zonaxx = $("#cod_zonaxxID");
    var nom_zonaxx = $("#nom_zonaxxID");
  
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/zonasx/ajax_zonasx_zonasx.php",
        data: "option=InsertZona&cod_zonaxx="+$("#cod_zonaxxID").val()+"&nom_zonaxx="+nom_zonaxx.val(),
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
      url: "../"+ Standa +"/zonasx/ajax_zonasx_zonasx.php",
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
      url: "../"+ Standa +"/zonasx/ajax_zonasx_zonasx.php",
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