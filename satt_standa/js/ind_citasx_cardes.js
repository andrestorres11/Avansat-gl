function Validate()
{
  try
  {
    var attribs = 'option=getInform';
    var Standa = $("#standaID").val();
    var fec_inicia = $("#fec_iniciaID").val();
    attribs += '&fec_inicia=' + fec_inicia;
    var fec_finali = $("#fec_finaliID").val();
    attribs += '&fec_finali=' + fec_finali;
    var hor_inicia = $("#hor_iniciaID").val();
    attribs += '&hor_inicia=' + hor_inicia;
    var hor_finali = $("#hor_finaliID").val();
    attribs += '&hor_finali=' + hor_finali;
    var date_inicia = new Date( fec_inicia + "T" + hor_inicia ); 
    var date_finali = new Date( fec_finali + "T" + hor_finali );

    var cod_ciuori = $("#cod_ciuoriID").val();
    attribs += '&cod_ciuori=' + cod_ciuori.split(' - ')[0];
    var cod_ciudes = $("#cod_ciudesID").val();
    attribs += '&cod_ciudes=' + cod_ciudes.split(' - ')[0];
    var cod_tipdes = $("#cod_tipdesID").val();
    attribs += '&cod_tipdes=' + cod_tipdes;
    var cod_produc = $("#cod_producID").val();
    attribs += '&cod_produc=' + cod_produc;
    var num_viajex = $("#num_viajexID").val();
    attribs += '&num_viajex=' + num_viajex;
    var num_pedido = $("#num_pedidoID").val();
    attribs += '&num_pedido=' + num_pedido;
    var cod_zonaxx = $("#cod_zonaxxID").val();
    attribs += '&cod_zonaxx=' + cod_zonaxx;
    var cod_canalx = $("#cod_canalxID").val();
    attribs += '&cod_canalx=' + cod_canalx;
    var cod_tiptra = $("#cod_tiptraID").val();
    attribs += '&cod_tiptra=' + cod_tiptra;
    var nom_poseed = $("#nom_poseedID").val();
    attribs += '&nom_poseed=' + nom_poseed;
    var cod_transp = $("#cod_transpID").val();
    attribs += '&cod_transp=' + cod_transp;
    
    if(cod_transp == '')
    {
      Alerta( 'Atenci\xf3n', 'La transportadora es de caracter obligatorio', $("#cod_transpID") );
    }
    else if( date_inicia > date_finali )
    {
      Alerta( 'Atenci\xf3n', 'La fecha Inicial no puede ser mayor a la fecha Final', $("#fec_iniciaID") );
    }
    else
    {
      $.ajax({
        url: "../" + Standa + "/infast/ajax_citasx_cardes.php",
        data: attribs,
        type: "POST",
        async: true,
        beforeSend: function( obj )
        {
          $.blockUI({ 
            theme:     true, 
            title:    'Citas de Cargue y Descargue', 
            draggable: false,
            message:  '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
          });
        },
        success: function( data )
        {
          $.unblockUI();
          $(".tablaList").hide("blind");
          $("#downarrowID").show();
          $("#resultID").show();
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

function showPrefilters()
{
  try
  {
    $("#downarrowID").hide();
    $("#resultID").hide();
    $(".tablaList").show("blind");
  }
  catch( e )
  {
    console.log( e.message );
  }

}

function DetailsCumdes( ind_cumpli, cod_tipser, fec_subniv )
{
  try
  {
    var Standa = $("#standaID").val();
    
    $("#PopUpID").dialog({
      modal : true,
      resizable : true,
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
      url: "../" + Standa + "/infast/ajax_citasx_cardes.php",
      data : 'standa=' + Standa +'&option=DetailsIndicador&fec_subniv=' + fec_subniv + '&cod_tipser=' + cod_tipser + '&ind_cumpli=' + ind_cumpli+ '&cod_transp=' +$("#cod_transpID").val(),
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
        },
      complete :
        function ()
        {
          $("#PopUpID").css( "height", "550" );
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
  window.open("../" + Standa + "/infast/ajax_citasx_cardes.php?option=expInformExcel", '', '');
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