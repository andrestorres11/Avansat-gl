function Validate()
{
  try
  {
  	var Standa = $("#standaID").val();

  	var fec_inicia = $("#fec_iniciaID");
  	var fec_finali = $("#fec_finaliID");

  	var cod_tipdes = $("#cod_tipdesID");
  	var cod_produc = $("#cod_producID");
	
  	var date_inicia = new Date( fec_inicia.val() + "T" + '00:00:00' ); 
    var date_finali = new Date( fec_finali.val() + "T" + '23:59:59' );

	if( date_inicia > date_finali )
    {
      Alerta( 'Atenci\xf3n', 'La fecha Inicial no puede ser mayor a la fecha Final', fec_inicia );
    }
    else
    {
      $.ajax({
        url: "../" + Standa + "/infast/ajax_sintes_operac.php",
        data: "option=getInform&fec_inicia=" + fec_inicia.val() + "&fec_finali=" + fec_finali.val() + "&cod_tipdes=" + cod_tipdes.val() + "&cod_produc=" + cod_produc.val(),
        type: "POST",
        async: true,
        beforeSend: function( obj )
        {
          $.blockUI({ 
            theme:     true, 
            title:    'Sintesis Operacion', 
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

function getData( pri_nivelx, seg_nivelx, ind_soluci )
{
  try
  {
    var Standa = $("#standaID").val();

    $("#PopUpID").dialog({
      modal : true,
      resizable : true,
      draggable: true,
      title: "Detalles",
      width: $(document).width() - 400,
      heigth : 500,
      position:['middle',25], 
      bgiframe: true,
      closeOnEscape: false,
      show : { effect: "drop", duration: 300 },
      hide : { effect: "drop", duration: 300 }
    });

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_sintes_operac.php",
      data: "option=getData&pri_nivelx=" + pri_nivelx + "&seg_nivelx=" + seg_nivelx+ "&ind_soluci=" + ind_soluci,
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
  }
}

function getDataDetail( pri_nivelx, seg_nivelx, fec_cortex, ind_soluci)
{
  try
  {
    var Standa = $("#standaID").val();

    $("#PopUpID").dialog({
      modal : true,
      resizable : true,
      draggable: true,
      title: "Detalles",
      width: $(document).width() - 400,
      heigth : 500,
      position:['middle',25], 
      bgiframe: true,
      closeOnEscape: false,
      show : { effect: "drop", duration: 300 },
      hide : { effect: "drop", duration: 300 }
    });

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_sintes_operac.php",
      data: "option=getDataDetail&pri_nivelx=" + pri_nivelx + "&seg_nivelx=" + seg_nivelx+ "&fec_cortex=" + fec_cortex+ "&ind_soluci=" + ind_soluci,
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
  }
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

function Export()
{ 
  var Standa = $("#standaID").val();
  window.open("../" + Standa + "/infast/ajax_sintes_operac.php?option=expInformExcel", '', '');
}