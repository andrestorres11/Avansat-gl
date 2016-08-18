$(document).ready(function() 
{
  var Standa = $( "#StandaID" ).val();
  var filter = $( "#filterID" ).val();
  
  $( "#cod_transpID" ).autocomplete({
    source: "../"+ Standa +"/comuni/ajax_usuari_tempor.php?option=getTransp&standa="+Standa+"&filter="+filter,
    minLength: 2, 
    delay: 100
  }).bind( "autocompleteclose", function(event, ui){ValidateTransp();} );

  $( "#cod_transpID" ).bind( "autocompletechange", function(event, ui){ ValidateTransp(); } );
  
});

function validateFechas()
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    
    var cod_usuari = $("#cod_usuariID"); 
    var des_motivo = $("#des_motivoID"); 
    var fec_inicia = $("#fec_iniciaID"); 
    var fec_finali = $("#fec_finaliID");
    var cod_reempl = $("#cod_reemplID"); 

    var date_inicia = new Date( fec_inicia.val() + "T" + '00:00:00' ); 
    var date_finali = new Date( fec_finali.val() + "T" + '23:59:59' );

    if( cod_usuari.val() == '' )
    {
      alert("Digite el Nombre, Correo y/o Usuario a Reemplazar");
      cod_usuari.focus();
      return false;
    }
    else if( des_motivo.val() == '' )
    {
      alert("Seleccione el Motivo de Inactivaci\xf3n");
      des_motivo.focus();
      return false;
    }
    else if( fec_inicia.val() == '' )
    {
      alert("Digite y/o Seleccione la fecha Inicial de Corte");
      fec_inicia.focus();
      return false;
    }
    else if( fec_finali.val() == '' )
    {
      alert("Digite y/o Seleccione la fecha Final de Corte");
      fec_finali.focus();
      return false;
    }
    else if( date_inicia > date_finali )
    {
      alert("La fecha Inicial no puede ser mayor a la fecha Final");
      fec_inicia.focus();
      return false;
    }
    else if( cod_reempl.val() == '' )
    {
      alert("Digite el Nombre, Correo y/o Usuario Reemplazante");
      cod_reempl.focus();
      return false;
    }
    else
    {
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/comuni/ajax_usuari_tempor.php",
        data: "option=validateFechas&cod_usuari="+cod_usuari.val()+"&des_motivo="+des_motivo.val()+"&fec_finali="+fec_finali.val()+"&fec_inicia="+fec_inicia.val()+"&cod_reempl="+cod_reempl.val(),
        async: false,
        success: function( datos )
        {
          if( datos == 'y' )
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
          $("#des_motivoID").val('');
          $("#fec_iniciaID").val('');
          $("#fec_finaliID").val('');
          ShowDataForm( cod_usuari.val() );
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

function SetUsuari( nom_campox, tip_consul, cod_transp, mainind )
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    $("#AlarmaID").hide("blind");
    $("#FormElementsID").hide("blind");
    $("#InsertarID").hide();
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/comuni/ajax_usuari_tempor.php",
      data: "option=SetUsuari&standa="+Standa+"&nom_campox="+nom_campox.val()+"&tip_consul="+tip_consul+"&cod_transp="+cod_transp,
      async: false,
      beforeSend: function()
      {
        $("#filtrosID").hide("blind");
      },
      success: function( datos )
      {
        if( mainind == '1' )
        {
          $("#cod_usuariID").val('');
          $("#nom_usuariID").val('');
          $("#ema_usuariID").val('');

          if( datos == 'n' )
          {
            $("#AlarmaID").html('<span width="60%" style="background:none repeat scroll 0 0 #FFCECE;border: 1px solid #E18F8E;border-radius:4px 4px 4px 4px;color:#333333;font-family:Arial;font-size:12px;padding:10px;width:100%;" id="messageID">La Consulta No Obtuvo Resultados</span>');
            $("#AlarmaID").show("blind");
            $("#nom_usuariID").focus();
          }
          else if( datos == 'r' )
          {
            $("#AlarmaID").html('<span width="60%" style="background:none repeat scroll 0 0 #FFFFAD;border: 1px solid #939300;border-radius:4px 4px 4px 4px;color:#333333;font-family:Arial;font-size:12px;padding:10px;width:100%;" id="messageID">El Usuario a Reemplazar No se Encuentra Asociado a la Transportadora</span>');
            $("#AlarmaID").show("blind");
            $("#nom_usuariID").focus();
          }
          else
          {
            $("#ema_usuariID").val( datos.split('|')[2] );
            $("#cod_usuariID").val( datos.split('|')[0] );
            $("#nom_usuariID").val( datos.split('|')[1] );
          }
        }
        else
        {
          $("#cod_reemplID").val('');
          $("#nom_reemplID").val('');
          $("#ema_reemplID").val('');

          if( datos == 'n' )
          {
            $("#AlarmaID").html('<span width="60%" style="background:none repeat scroll 0 0 #FFCECE;border: 1px solid #E18F8E;border-radius:4px 4px 4px 4px;color:#333333;font-family:Arial;font-size:12px;padding:10px;width:100%;" id="messageID">La Consulta No Obtuvo Resultados</span>');
            $("#AlarmaID").show("blind");
            $("#nom_reemplID").focus();
          }
          else if( datos == 'r' )
          {
            $("#AlarmaID").html('<span width="60%" style="background:none repeat scroll 0 0 #FFFFAD;border: 1px solid #939300;border-radius:4px 4px 4px 4px;color:#333333;font-family:Arial;font-size:12px;padding:10px;width:100%;" id="messageID">El Usuario Reemplazante No se Encuentra Asociado a la Transportadora</span>');
            $("#AlarmaID").show("blind");
            $("#nom_reemplID").focus();
          }
          else
          {
            $("#ema_reemplID").val( datos.split('|')[2] );
            $("#cod_reemplID").val( datos.split('|')[0] );
            $("#nom_reemplID").val( datos.split('|')[1] );
            ShowDataForm( $("#cod_usuariID").val() );
            $("#InsertarID").show();
          }
        }
        $("#filtrosID").show("blind");
      }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function ShowDataForm( cod_usuari )
{
  try
  { 
    $("#messageID").hide( "slide" );
    var Standa = $( "#StandaID" ).val();
    var cod_transp = $( "#cod_transpID" ).val().split(' - ')[0];

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/comuni/ajax_usuari_tempor.php",
      data: "option=ShowDataForm&cod_usuari="+cod_usuari+"&cod_transp="+cod_transp,
      async: false,
      success: function( datos )
      {
        $("#FormElementsID").html( datos );
      }
    });

    $("#FormElementsID").show("blind");
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
        url: "../"+ Standa +"/comuni/ajax_usuari_tempor.php",
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
      url: "../"+ Standa +"/comuni/ajax_usuari_tempor.php",
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