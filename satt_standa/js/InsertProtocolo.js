function AddGrid()
{
  try
  {
    var Standa = $("#StandaID").val();
    var counter = $("#counterID").val();
    var cod_tipdes = $("#cod_tipdes"+(counter-1)+"ID");
    if( cod_tipdes.val() == '' )
    {
      alert("Fila:"+counter+", Columna:4\n\nSeleccione el Tipo de Despacho.");
    }
    else
    {
      var new_counter = $("#counterID").val();
      new_counter++;
      $.ajax({
      type: "POST",
      url: "../"+ Standa +"/protoc/ajax_protoc_protoc.php",
      data: "option=generateDynamicGrid&counter="+ new_counter + "&ind_ajax=yes",
      async: false,
      success : 
        function ( data ) 
        { 
          $('#matcomID').append( data );
          $("#counterID").val( new_counter );
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

function MainLoad()
{
  try
  {
    var Standa = $("#StandaID").val();
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/protoc/ajax_protoc_protoc.php",
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
      url: "../"+ Standa +"/protoc/ajax_protoc_protoc.php",
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

function ChangeProtocolo()
{
  try
  {
    var Standa = $("#StandaID").val();
    
    var cod_protoc = $("#cod_protoc_ID");
    var des_protoc = $("#des_protoc_ID");
    var tex_protoc = $("#tex_protoc_ID");
    var cod_respon = $("#cod_respon_ID");
    var tie_respon = $("#tie_respon_ID");
    var ind_estado = $("#ind_estado_ID");
    
    var emailReg = 	/[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
    
    if( des_protoc.val() == '' )
    {
      alert("Digite Descripci\xf3n del Protocolo");
      des_protoc.focus();
      return false;
    }
    else if( cod_respon.val() == '' )
    {
      alert("Seleccione Tipo de Responsable del Protocolo");
      cod_respon.focus();
      return false;
    }
    else if( tie_respon.val() == '' )
    {
      alert("Digite Tiempo del Protocolo");
      tie_respon.focus();
      return false;
    }
    else if( isNaN( tie_respon.val() ) ) 
    {
      alert("El Tiempo del Protocolo debe ser num\xe9rico");
      tie_respon.focus();
      return false;
    }
    else if( ind_estado.val() == '' ) 
    {
      alert("Seleccione un Estado");
      ind_estado.focus();
      return false;
    }
    else
    {
      var validator = true;
      var counter = $("#counterID").val();
      var add = '&counter='+counter;
      for( var i = 0; i < counter; i++ )
      {
        if( $("#cod_tipdes"+i+"ID").val() == '' )
        {
          alert("Fila:"+( parseInt( i ) + 1 )+", Columna:4\n\nSeleccione el Tipo de Despacho.");
          validator = false;
          return false;
        }
        else
        {
          add += '&cod_ciuori'+i+'='+$("#cod_ciuori"+i+"ID").val();
          add += '&cod_ciudes'+i+'='+$("#cod_ciudes"+i+"ID").val();
          add += '&cod_produc'+i+'='+$("#cod_produc"+i+"ID").val().replace(/&/g,"/-/");
          add += '&cod_tipdes'+i+'='+$("#cod_tipdes"+i+"ID").val();
          add += '&ema_conpri'+i+'='+$("#ema_conpri"+i+"ID").val();
          add += '&ema_otrcon'+i+'='+$("#ema_otrcon"+i+"ID").val();
        }
      }
      if(validator)
      {
        var attr = "option=ChangeProtocolo&Standa=" + Standa + "&des_protoc="+des_protoc.val() + "&tex_protoc="+tex_protoc.val() + "&cod_respon="+cod_respon.val() + "&tie_respon="+tie_respon.val()+"&ind_estado="+ind_estado.val()+ "&cod_protoc="+cod_protoc.val()+add;
        $.ajax({
          type: "POST",
          url: "../"+ Standa +"/protoc/ajax_protoc_protoc.php",
          data: attr,
          async: false,
          beforeSend : 
            function () 
            { 
              $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
            },
          success : 
            function ( datos ) 
            { 
              $("#PopUpID").dialog('close');
              mainList();
              if( datos == "y" )
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
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function InsertProtocolo()
{
  try
  {
    var Standa = $("#StandaID").val();
    
    var des_protoc = $("#des_protocID");
    var tex_protoc = $("#tex_protocID");
    var cod_respon = $("#cod_responID");
    var tie_respon = $("#tie_responID");
    
    var emailReg = 	/[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
	
    if( des_protoc.val() == '' )
    {
      alert("Digite Descripci\xf3n del Protocolo");
      des_protoc.focus();
      return false;
    }
    else if( cod_respon.val() == '' )
    {
      alert("Seleccione Tipo de Responsable del Protocolo");
      cod_respon.focus();
      return false;
    }
    else if( tie_respon.val() == '' )
    {
      alert("Digite Tiempo del Protocolo");
      tie_respon.focus();
      return false;
    }
    else if( isNaN( tie_respon.val() ) ) 
    {
      alert("El Tiempo del Protocolo debe ser num\xe9rico");
      tie_respon.focus();
      return false;
    }
    else
    {
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/protoc/ajax_protoc_protoc.php",
        data: "option=Insert&Standa=" + Standa + "&des_protoc="+des_protoc.val() + "&tex_protoc="+tex_protoc.val() + "&cod_respon="+cod_respon.val() + "&tie_respon="+tie_respon.val(),
        async: false,
        beforeSend : 
          function () 
          { 
            $("#mainListID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
          },
        success : 
          function ( datos ) 
          { 
            //$("#mainListID").html( datos );
            MainLoad();
            
            if( datos == "y" )
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
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function EditProtocolo( num_protoc )
{
  try
  {
    var cod_protoc = num_protoc.text();
    var Standa = $("#StandaID").val();
    
    $("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Modificaci\xf3n de Protocolo",
      width: 1100,
      heigth : 500,
      position:['middle',25], 
      bgiframe: true,
      closeOnEscape: false,
      show : { effect: "drop", duration: 300 },
      hide : { effect: "drop", duration: 300 },
      open: function(event, ui) { 
            $(this).parent().children().children('.ui-dialog-titlebar-close').hide();},
      buttons: {
        Eliminar : function()
        {
          deleteProtoc(cod_protoc);
        },
        Continuar : function() 
        { 
          ChangeProtocolo();
          // SendContact( option, cod_cennot, contact_id );
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
      url: "../"+ Standa +"/protoc/ajax_protoc_protoc.php",
      data: "option=FormEdit&Standa="+Standa+"&cod_protoc="+cod_protoc,
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

function deleteProtoc(num_protoc){

  var Standa = $("#StandaID").val();
  var parame = "option=deleteProtoc&num_protoc="+num_protoc;

  if (confirm("Desea Eliminar el Protocolo "+num_protoc+"?")) {

    $.ajax({
      url: "../"+ Standa +"/protoc/ajax_protoc_protoc.php",
      data:parame,
      beforeSend:function(){
        $('<div id="popupLoad"><center><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></center></div>').dialog();
      },
      success:function(){
        $("#popupLoad").dialog("destroy").remove(); ; 
      }
    });
  }

}
