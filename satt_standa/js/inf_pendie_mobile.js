function loadInform()
{
  try
  {
    var Standa = $("#standaID").val();

  	$.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_pendie_mobile.php",
      data: "option=loadInform",
      async: false,
      beforeSend : 
        function () 
        { 
          $("#resultID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
        },
      success : 
        function ( data ) 
        { 
          $("#resultID").html( data );
        }
    });
  }
  catch( e )
  {
  	console.log( e.message );
  }
}

function showDetails( num_despac, tip_alarma, cod_noveda )
{
  try
  {
	var Standa = $("#standaID").val();
  	
	$("#detailsID").dialog({
      modal : true,
      draggable: false,
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
      url: "../"+ Standa +"/infast/ajax_pendie_mobile.php",
      data: "option=showDetails&num_despac=" + num_despac + "&tip_alarma=" + tip_alarma + "&cod_noveda=" + cod_noveda,
      async: false,
      beforeSend : 
        function () 
        { 
          $("#detailsID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
        },
      success : 
        function ( data ) 
        { 
          $("#detailsID").html( data );
        }
    });

  }
  catch( e )
  {
    console.log( e.message );
  } 
}