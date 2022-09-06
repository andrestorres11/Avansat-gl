$(document).ready(function() 
{
  var Standa = $( "#StandaID" ).val();
  var filter = $( "#filterID" ).val();
  
  $( "#cod_transpID" ).autocomplete({
    source: "../"+ Standa +"/comuni/ajax_modulo_comuni.php?option=getTransp&standa="+Standa+"&filter="+filter,
    minLength: 2, 
    delay: 100
  }).bind( "autocompleteclose", function(event, ui){ValidateTransp();} );

  $( "#cod_transpID" ).bind( "autocompletechange", function(event, ui){ ValidateTransp(); } );

});

function SaveCiudad()
{
  try
  {
    $("#AlarmaID").hide();
    var cod_ciudad = $("#cod_ciuoriID");
    var cod_transp = $("#cod_transpID");

    if( confirm("Realmente desea Insertar la Ciudad " + cod_ciudad.val().split(' - ')[1] +'?') )
    {
      newCiudad( cod_ciudad.val().split(' - ')[0], cod_transp.val().split(' - ')[0] );
    }
    else
    {
      $("#cod_ciuoriID").val('');
      $("#cod_ciuoriID").focus();
    }
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function DeleteOrigen( cod_ciudad, cod_transp )
{
  try
  { 
    var Standa = $( "#StandaID" ).val();

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/transp/ajax_transp_origen.php",
      data: "option=DeleteCiudad&cod_ciudad="+cod_ciudad.text()+"&cod_transp="+cod_transp,
      async: false,
      success: function( datos )
      {
        $("#loading").remove();
        if( datos == 'y' )
        {
          ShowForm( $("#cod_transpID").val() );
          $("#AlarmaID").html('<span style="background:none repeat scroll 0 0 #9FFFB3;border: 1px solid #20A53A;border-radius:4px 4px 4px 4px;color:#333333;font-family:Arial;font-size:12px;padding:10px;margin:10px;width:100%;" id="messageID">Registro Eliminado con exito</span>');
          $("#AlarmaID").show("blind");
        }
        else
        {
          ShowForm( $("#cod_transpID").val() );
          $("#AlarmaID").html('<span style="background:none repeat scroll 0 0 #FFB1B1;border: 1px solid #930000;border-radius:4px 4px 4px 4px;color:#333333;font-family:Arial;font-size:12px;padding:10px;margin:10px;width:100%;" id="messageID">El Registro No fue Eliminado</span>');
          $("#AlarmaID").show("blind");
          $("#cod_ciuoriID").val('');
          $("#cod_ciuoriID").focus();
        }
      }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function newCiudad( cod_ciudad, cod_transp )
{
  try
  { 
    var Standa = $( "#StandaID" ).val();

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/transp/ajax_transp_origen.php",
      data: "option=InsertCiudad&cod_ciudad="+cod_ciudad+"&cod_transp="+cod_transp,
      async: false,
      beforeSend: function()
      {
        $("#cod_ciuoriID").focus().after("<span id='loading'><img src=\'../" + Standa + "/imagenes/ajax-loader3.gif\' /></span>");
      },
      success: function( datos )
      {
        $("#loading").remove();
        if( datos == 'y' )
        {
          ShowForm( $("#cod_transpID").val() );
          $("#AlarmaID").html('<span style="background:none repeat scroll 0 0 #9FFFB3;border: 1px solid #20A53A;border-radius:4px 4px 4px 4px;color:#333333;font-family:Arial;font-size:12px;padding:10px;margin:10px;width:100%;" id="messageID">Registro Insertado con exito</span>');
          $("#AlarmaID").show("blind");
        }
        else
        {
          ShowForm( $("#cod_transpID").val() );
          $("#AlarmaID").html('<span style="background:none repeat scroll 0 0 #FFB1B1;border: 1px solid #930000;border-radius:4px 4px 4px 4px;color:#333333;font-family:Arial;font-size:12px;padding:10px;margin:10px;width:100%;" id="messageID">El Registro No fue Insertado</span>');
          $("#AlarmaID").show("blind");
          $("#cod_ciuoriID").val('');
          $("#cod_ciuoriID").focus();
        }
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
    if( !cod_transp )
    {
      alert("Digite el Cliente");
      return false;
    }
    else
    {
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
        data: "option=ValidateTransp&standa="+Standa+"&filter="+filter+"&cod_transp="+cod_transp.split('-')[0],
        async: false,
        success: function( datos )
        {
          if( datos == 'n' )
          {
            alert("El Cliente < "+ cod_transp +" > no existe");
            return false;
          }
          else
          {
            ShowForm( cod_transp );
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

function ShowForm( cod_transp )
{
  try
  {
    $(".tablaList").hide("blind");
    nom_transp = cod_transp.split('-')[1].trim();
    cod_transp = cod_transp.split('-')[0].trim();
    var Standa = $( "#StandaID" ).val();
    $("#transpID").val( cod_transp );

    $("#resultID").css( {'background-color':'#f0f0f0', 'border':'1px solid #c9c9c9','padding':'5px', 'width':'98%', 'min-height':'50px','-moz-border-radius':'5px 5px 5px 5px', '-webkit-border-radius':'5px 5px 5px 5px', 'border-top-left-radius':'5px', 'border-top-right-radius':'5px', 'border-bottom-right-radius':'5px', 'border-bottom-left-radius':'5px'} );
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/transp/ajax_transp_origen.php",
      data: "option=MainForm&standa="+Standa+"&cod_transp="+cod_transp+"&nom_transp="+nom_transp.replace(/&/g,"/-/"),
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