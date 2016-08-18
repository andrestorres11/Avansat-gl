function MainLoad()
{
  try
  {
    blockScreen( "Cargando..." );
    var Standa = $( "#StandaID" ).val();

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/protoc/ajax_subcau_protoc.php",
      data: "option=MainLoad&standa="+Standa,
      async: false,
      success: function( datos )
      {
        UnblockScreen();
        $( "#mainDiv" ).html( datos );
      }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function setEncBoceto( elemento )
{
  try
  {
    $('label[for=val_itemxx_consec_ID]').html( elemento.val().toUpperCase() + ":&nbsp;&nbsp;" );
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function setReqBoceto( elemento )
{
  try
  {
    var valor = elemento.is(':checked') ? '*' : '';
    $('label[for=val_encabe_consec_ID]').html( valor + "&nbsp;" );
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function DeleteSubcausa( elemento )
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    if( confirm("Realmente Desea Eliminar La Subcausa con el Consecutivo " + elemento.text() + "?" ) )
    {
      blockScreen( "Cargando..." );

      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/protoc/ajax_subcau_protoc.php",
        data: "option=DeleteSubcausa&standa="+Standa+"&num_consec="+elemento.text(),
        async: false,
        success: function( datos )
        {
          UnblockScreen();
          if( datos == '1000' )
            Alerta( "Correcto", "La Subcausa fue Eliminada Exitosamente", '', 'MainLoad' );
          else
            Alerta( "Atenci\xf3n", "La Subcausa no Pudo ser Eliminada, por Favor Intente Nuevamente", '', 'MainLoad' );
            
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

function InsertEvento( sel )
{
  try
  {       
    var Standa = $( "#StandaID" ).val();
    var Html = $( "#ToRegID" ).html();
    
    var cod_tipoxx = $( "#cod_tipoxxID" ).val();
    var tex_encabe = $( "#tex_encabeID" ).val();
    var des_textox = $( "#des_textoxID" ).val();
    var ind_requer = $( "#ind_requerID" ).is(':checked')  ? '1' : '0';

    if( cod_tipoxx == '' )
    {
      Alerta( "Atenci\xf3n", "Seleccione el Tipo", $( "#cod_tipoxxID" ), '' );
      return false;
    }
    else if( tex_encabe == '' )
    {
      Alerta( "Atenci\xf3n", "Digite el Encabezado", $( "#tex_encabeID" ), '' );
      return false; 
    }
    else if( des_textox == '' )
    {
      Alerta( "Atenci\xf3n", "Digite el Texto", $( "#des_textoxID" ), '' );
      return false; 
    }
    else
    {
      blockScreen( "Cargando..." );
      if ( $("#formHtmlID").is (':visible') )
       $("#formHtmlID").hide( 'blind' );
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/protoc/ajax_subcau_protoc.php",
        data: "option=InsertEvento&standa="+Standa+"&Html="+Html.replace( /&nbsp;/g, "/-/" )+"&cod_tipoxx="+cod_tipoxx+"&tex_encabe="+tex_encabe+"&des_textox="+des_textox+"&ind_requer="+ind_requer,
        async: false,
        success: function( datos )
        {
          UnblockScreen();
          if( datos )
            Alerta( "Correcto", "La Subcausa fue Insertada Exitosamente", '', 'MainLoad' );
          else
            Alerta( "Atenci\xf3n", "La Subcausa no Pudo ser Insertada Exitosamente, por Favor Intente Nuevamente", '', 'MainLoad' );
            
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

function SetForm( sel )
{
  try
  {
    if( sel.val() != '' )
    {
      blockScreen( "Cargando..." );
      var Standa = $( "#StandaID" ).val();
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/protoc/ajax_subcau_protoc.php",
        data: "option=SetForm&standa="+Standa+"&sel="+sel.val(),
        async: false,
        success: function( datos )
        {
          UnblockScreen();
          
          $( "#tex_encabeID, #ind_requerID, #des_textoxID" ).show();
          
          $( "#tex_encabeID" ).val('');
          $( "#ind_requerID" ).removeAttr("checked");

          if ( $("#formHtmlID").is (':visible') )
            $("#formHtmlID").hide( 'blind' );
      
          $( "#formHtmlID" ).html( datos );
          $("#formHtmlID").show( 'blind' );
        }
      });
    }
    else
    {
      if ( $("#formHtmlID").is (':visible') )
            $("#formHtmlID").hide( 'blind' );
    }
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  } 
} 

function blockScreen( msj )
{
  try
  {
  	$.blockUI({
  	  css: { 
  	  	top: '10px',
  	  	left: '', 
  	  	right: '10px',
  	  	border: 'none',   
        padding: '15px',      
        backgroundColor: '#001100',             
                         '-webkit-border-radius': '10px',             
                         '-moz-border-radius': '10px',             
        opacity: .7,             
        color: '#fff'         
      },
      centerY: 0, 
  	  message: "<h1>" + msj + "</h1>" 
  	}); 
  }
  catch( e )
  {
  	console.log( e.message );
  	return false;
  }
}

function InsertRango()
{
  try
  {
    var med_rangox = $("#med_rangoxID").val();
    
    if( med_rangox != '' )
    {
      $('#rango_consec_ID').text( med_rangox.toUpperCase() );
      $("#med_rangoxID").val('');
    }
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function InsertParame()
{
  try
  {
    var par_insert = $("#par_insertID").val();
    
    if( par_insert != '' )
    {
      $('#val_itemxx_consec_ID').append( '<option value="'+ par_insert.toUpperCase() +'">'+ par_insert.toUpperCase() +'</option>' );
      $("#par_insertID").val('');
    }
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function UnblockScreen()
{
  try
  {
  	$.unblockUI(); 
  }
  catch( e )
  {
  	console.log( e.message );
  	return false;
  }
}

function Alerta( title, message, focus, adi_func )
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
                    
                    if( adi_func == 'MainLoad' )
                      MainLoad();
                    
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