function Validate()
{
  try
  {
    var Standa = $("#standaID").val();
    var fec_inicia = $("#fec_iniciaID").val();
    var fec_finali = $("#fec_finaliID").val();
    var hor_inicia = $("#hor_iniciaID").val();
    var hor_finali = $("#hor_finaliID").val();
    var cod_usuari = $("#cod_usuariID").val();
    var cod_tipinf = $("#cod_tipinfID").val();
    
    var date_inicia = new Date( fec_inicia + "T" + hor_inicia ); 
    var date_finali = new Date( fec_finali + "T" + hor_finali ); 
    
    if( date_inicia > date_finali )
    {
      Alerta( 'Atenci\xf3n', 'La fecha Inicial no puede ser mayor a la fecha Final', $("#fec_iniciaID") );
    }
    else if( !cod_tipinf )
    {
      Alerta( 'Atenci\xf3n', 'Seleccione el Tipo de Informe', $("#cod_tipinfID") );
    }
    else
    {
      $.ajax({
        url: "../" + Standa + "/infast/ajax_usuari_novcor.php",
        data: "option=getInform&fec_inicia=" + fec_inicia + "&fec_finali=" + fec_finali + "&hor_inicia=" + hor_inicia + "&hor_finali=" + hor_finali + "&cod_usuari=" + cod_usuari + "&cod_tipinf=" + cod_tipinf,
        type: "POST",
        async: true,
        beforeSend: function( obj )
        {
          $.blockUI({ 
            theme:     true, 
            title:    'Usuario Asignado a Novedad', 
            draggable: false,
            message:  '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
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

function Details( col, fil, inf, typ )
{
  try
  {
    var Standa = $("#standaID").val();
    var fec_ini = $("#fec_iniciaID").val()+" "+$("#hor_iniciaID").val();
    var fec_fin = $("#fec_finaliID").val()+" "+$("#hor_finaliID").val();
    
    $("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: " Detalles",
      width: $(document).width() - 600,
      heigth : 700,
      position:['middle',25], 
      bgiframe: true,
      closeOnEscape: false,
      show : { effect: "drop", duration: 300 },
      hide : { effect: "drop", duration: 300 }
    });

      $.ajax({
      url: "../"+ Standa +"/infast/ajax_usuari_novcor.php",
      data : 'standa=' + Standa +'&option=Details&col='+col+'&fil='+fil+'&inf='+inf+'&typ='+typ+"&fec_ini="+fec_ini+"&fec_fin="+fec_fin,
      method : 'POST',
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
  }
}

function Export()
{ 
  var Standa = $("#standaID").val();
  window.open("../" + Standa + "/infast/ajax_usuari_novcor.php?option=expInformExcel", '', '');
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