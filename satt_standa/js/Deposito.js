function ChangeDeposito()
{
  try
  {
    var cod_deposi = $("#cod_deposi_ID");
    var nom_deposi = $("#nom_deposi_ID");
    var ind_estado = $("#ind_estado_ID");
    var Standa = $("#StandaID").val();
    
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/deposi/ajax_deposi_deposi.php",
        data: "option=EditDeposito&cod_deposi="+$("#cod_deposi_ID").val().replace(/&/g,"/-/")+"&nom_deposi="+nom_deposi.val()+"&ind_estado="+ind_estado.val(),
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

function DropDeposito()
{
  try
  {
    var cod_deposi = $("#cod_deposi_ID");
    var Standa = $("#StandaID").val();
    
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/deposi/ajax_deposi_deposi.php",
        data: "option=DropDeposito&cod_deposi="+$("#cod_deposi_ID").val().replace(/&/g,"/-/"),
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

function EditDeposito( num_deposi )
{
  try
  {
    var cod_deposi = num_deposi.text();
    var Standa = $("#StandaID").val();
    
    $("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Modificaci\xf3n de Deposito",
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
          ChangeDeposito();
        },
        Eliminar : function() 
        { 
          DropDeposito();
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
      url: "../"+ Standa +"/deposi/ajax_deposi_deposi.php",
      data: "option=FormEdit&Standa="+Standa+"&cod_deposi="+cod_deposi.replace(/&/g,"/-/"),
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

function InsertDeposito()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_deposi = $("#cod_deposiID");
    var nom_deposi = $("#nom_deposiID");
    
    if( cod_deposi.val() == '' )
    {
      alert("Digite el Codigo del Deposito");
      cod_deposi.focus();
      return false;
    }
    else if( nom_deposi.val() == '' )
    {
      alert("Digite el Nombre del Deposito");
      nom_deposi.focus();
      return false;
    }
    else
    {
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/deposi/ajax_deposi_deposi.php",
        data: "option=ValidateDeposi&cod_deposi="+$("#cod_deposiID").val().replace(/&/g,"/-/"),
        async: false,
        success : 
          function ( data ) 
          {
            if( data == 'yes')
            {
              alert("El Deposito con el Codigo: "+cod_deposi.val()+", ya existe");
              cod_deposi.focus();
              return false;
            } 
            else
            {
              NewDeposito();
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

function NewDeposito()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_deposi = $("#cod_deposiID");
    var nom_deposi = $("#nom_deposiID");
  
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/deposi/ajax_deposi_deposi.php",
        data: "option=InsertDeposito&cod_deposi="+$("#cod_deposiID").val().replace(/&/g,"/-/")+"&nom_deposi="+nom_deposi.val(),
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
  // alert("EN DESARROLLO");
  try
  {
    var Standa = $("#StandaID").val();
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/deposi/ajax_deposi_deposi.php",
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
      url: "../"+ Standa +"/deposi/ajax_deposi_deposi.php",
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