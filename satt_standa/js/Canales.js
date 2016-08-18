function ChangeCanal()
{
  try
  {
    var cod_produc = $("#cod_produc_ID");
    var cod_canalx = $("#cod_canalx_ID");
    var nom_canalx = $("#nom_canalx_ID");
    var ind_estado = $("#ind_estado_ID");
    var Standa = $("#StandaID").val();
    
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/canalx/ajax_canalx_canalx.php",
        data: "option=EditCanal&cod_canalx="+$("#cod_canalx_ID").val().replace(/&/g,"/-/")+"&cod_produc="+$("#cod_produc_ID").val().replace(/&/g,"/-/")+"&nom_canalx="+nom_canalx.val()+"&ind_estado="+ind_estado.val(),
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

function DropCanal()
{
  try
  {
	var cod_produc = $("#cod_produc_ID");
    var cod_canalx = $("#cod_canalx_ID");
    var Standa = $("#StandaID").val();
    
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/canalx/ajax_canalx_canalx.php",
        data: "option=DropCanal&cod_canalx="+$("#cod_canalx_ID").val().replace(/&/g,"/-/")+"&cod_produc="+$("#cod_produc_ID").val().replace(/&/g,"/-/"),
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

function EditCanal( num_canalx )
{
  try
  {
	var cod_produc = num_canalx.parent().siblings('input[type=hidden]').val();
    var cod_canalx = num_canalx.text();
    var Standa = $("#StandaID").val();
    
    $("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Modificaci\xf3n de Canal",
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
          ChangeCanal();
        },
        Eliminar : function() 
        { 
          DropCanal();
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
      url: "../"+ Standa +"/canalx/ajax_canalx_canalx.php",
      data: "option=FormEdit&Standa="+Standa+"&cod_canalx="+cod_canalx.replace(/&/g,"/-/")+"&cod_produc="+cod_produc.replace(/&/g,"/-/"),
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

function InsertCanal()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_produc = $("#cod_producID");
    var cod_canalx = $("#cod_canalxID");
    var nom_canalx = $("#nom_canalxID");
    
    if( cod_produc.val() == '' )
    {
      alert("Seleccione el Producto");
      cod_produc.focus();
      return false;
    }
	else if( cod_canalx.val() == '' )
    {
      alert("Digite el Codigo del Canal");
      cod_canalx.focus();
      return false;
    }
    else if( nom_canalx.val() == '' )
    {
      alert("Digite el Nombre del Canal");
      nom_canalx.focus();
      return false;
    }
    else
    {
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/canalx/ajax_canalx_canalx.php",
        data: "option=ValidateCanal&cod_canalx="+$("#cod_canalxID").val().replace(/&/g,"/-/")+"&cod_produc="+cod_produc.val().replace(/&/g,"/-/"),
        async: false,
        success : 
          function ( data ) 
          {
            if( data == 'yes')
            {
              alert("Para el Producto "+ $("#cod_producID option:selected").text() +" y el consecutivo "+cod_canalx.val()+", ya existe un nombre Asignado");
              cod_canalx.focus();
              return false;
            } 
            else
            {
              NewCanal();
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

function NewCanal()
{
  try
  {
    var Standa = $("#StandaID").val();
    var cod_produc = $("#cod_producID");
    var cod_canalx = $("#cod_canalxID");
    var nom_canalx = $("#nom_canalxID");
  
    $.ajax({
        type: "POST",
        url: "../"+ Standa +"/canalx/ajax_canalx_canalx.php",
        data: "option=InsertCanal&cod_canalx="+$("#cod_canalxID").val().replace(/&/g,"/-/")+"&cod_produc="+$("#cod_producID").val().replace(/&/g,"/-/")+"&nom_canalx="+nom_canalx.val(),
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
      url: "../"+ Standa +"/canalx/ajax_canalx_canalx.php",
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
      url: "../"+ Standa +"/canalx/ajax_canalx_canalx.php",
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