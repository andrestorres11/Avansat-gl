function ChangeTiptra()
{
  try
  {
    var cod_tiptra = $("#cod_tiptra_ID");
    var nom_tiptra = $("#nom_tiptra_ID");
    var ind_estado = $("#ind_estado_ID");
    var Standa = $("#StandaID").val();
    
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/tiptra/ajax_tiptra_tiptra.php",
        data: "option=EditTiptra&cod_tiptra="+$("#cod_tiptra_ID").val()+"&nom_tiptra="+nom_tiptra.val()+"&ind_estado="+ind_estado.val(),
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

function DropTiptra()
{
  try
  {
    var cod_tiptra = $("#cod_tiptra_ID");
    var Standa = $("#StandaID").val();
    
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/tiptra/ajax_tiptra_tiptra.php",
        data: "option=DropTiptra&cod_tiptra="+$("#cod_tiptra_ID").val(),
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

function EditTiptra( num_tiptra )
{
  try
  {
    var cod_tiptra = num_tiptra.text();
    var Standa = $("#StandaID").val();
    
    $("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Modificaci\xf3n de Tiptra",
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
          ChangeTiptra();
        },
        Eliminar : function() 
        { 
          DropTiptra();
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
      url: "../"+ Standa +"/tiptra/ajax_tiptra_tiptra.php",
      data: "option=FormEdit&Standa="+Standa+"&cod_tiptra="+cod_tiptra,
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

function InsertTiptra()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_tiptra = $("#cod_tiptraID");
    var nom_tiptra = $("#nom_tiptraID");
    
    if( cod_tiptra.val() == '' )
    {
      alert("Digite el Codigo de la Tiptra");
      cod_tiptra.focus();
      return false;
    }
    else if( nom_tiptra.val() == '' )
    {
      alert("Digite el Nombre de la Tiptra");
      nom_tiptra.focus();
      return false;
    }
    else
    {
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/tiptra/ajax_tiptra_tiptra.php",
        data: "option=ValidateTiptra&cod_tiptra="+$("#cod_tiptraID").val(),
        async: false,
        success : 
          function ( data ) 
          {
            if( data == 'yes')
            {
              alert("La Tiptra con el Codigo: "+cod_tiptra.val()+", ya existe");
              cod_tiptra.focus();
              return false;
            } 
            else
            {
              NewTiptra();
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

function NewTiptra()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_tiptra = $("#cod_tiptraID");
    var nom_tiptra = $("#nom_tiptraID");
  
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/tiptra/ajax_tiptra_tiptra.php",
        data: "option=InsertTiptra&cod_tiptra="+$("#cod_tiptraID").val()+"&nom_tiptra="+nom_tiptra.val(),
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
      url: "../"+ Standa +"/tiptra/ajax_tiptra_tiptra.php",
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
      url: "../"+ Standa +"/tiptra/ajax_tiptra_tiptra.php",
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