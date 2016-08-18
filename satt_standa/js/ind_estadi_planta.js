function Validate()
{
  try
  {
    var Standa = $("#standaID").val();
    var fec_inicia = $("#fec_iniciaID").val();
    var fec_finali = $("#fec_finaliID").val();
    var hor_inicia = $("#hor_iniciaID").val();
    var hor_finali = $("#hor_finaliID").val();
    
    var cod_mercan = $("#cod_mercanID").val().replace(/&/g,"/-/");
    var cod_ciuori = $("#cod_ciuoriID").val();
    // var cod_tipdes = $("#cod_tipdesID").val();
    var cod_tipveh = $("#cod_tipvehID").val();
    
    var date_inicia = new Date( fec_inicia + "T" + hor_inicia ); 
    var date_finali = new Date( fec_finali + "T" + hor_finali ); 
    
    if( date_inicia > date_finali )
    {
      Alerta( 'Atenci\xf3n', 'La fecha Inicial no puede ser mayor a la fecha Final', $("#fec_iniciaID") );
    }
    else
    {
      $.ajax({
        url: "../" + Standa + "/infast/ajax_estadi_planta.php",
        data: "option=getInform&fec_inicia=" + fec_inicia + "&fec_finali=" + fec_finali + "&hor_inicia=" + hor_inicia + "&hor_finali=" + hor_finali + "&cod_mercan=" + cod_mercan + "&cod_ciuori=" + cod_ciuori + "&cod_tipveh=" + cod_tipveh,
        type: "POST",
        async: true,
        beforeSend: function( obj )
        {
          $.blockUI({ 
            theme:     true, 
            title:    'Estadia en Planta', 
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

function Export()
{ 
  var Standa = $("#standaID").val();
  window.open("../" + Standa + "/infast/ajax_estadi_planta.php?option=expInformExcel", '', '');
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

function Details( cod_ciuori, cod_mercan, cod_clasex )
{
  try
  {
    var Standa = $("#standaID").val();
    
    $("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Detalles",
      width: $(document).width() - 200,
      heigth : 500,
      position:['middle',25], 
      bgiframe: true,
      closeOnEscape: false,
      show : { effect: "puff", duration: 300 },
      hide : { effect: "puff", duration: 300 }
    });

    $.ajax({
      url: "../" + Standa + "/infast/ajax_estadi_planta.php",
      data : 'standa=' + Standa +'&option=Details&cod_ciuori=' + cod_ciuori + '&cod_mercan=' + cod_mercan + '&cod_clasex=' + cod_clasex,
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