$(document).ready(function() 
{
  var Standa = $( "#StandaID" ).val();
  var filter = $( "#filterID" ).val();
  var cod_transp = $("#cod_transpID");
  
  cod_transp.autocomplete({
    source: "../"+ Standa +"/infast/ajax_indica_solnov.php?option=getTransp&standa="+Standa+"&filter="+filter,
    minLength: 2, 
    delay: 100
  }).bind( "autocompleteclose", function(event, ui){ValidateTransp(3);} );

  cod_transp.bind( "autocompletechange", function(event, ui){ ValidateTransp(3); } );

  if( cod_transp.val() != '' && $("#ind_configID").val() != '1' ){
    ciudadTransp(Standa, cod_transp.val() );
  }
});

function deleteFestivo( cod_transp, ind_config, cod_ciudad, ano, mes, dia )
{
  try
  {
    var Standa = $( "#StandaID" ).val();

    if( confirm("Realmente Desea Eliminar el Festivo?") )
    {
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/infast/ajax_indica_solnov.php",
        data: "option=deleteFestivo&standa="+Standa+"&cod_transp="+cod_transp+"&ind_config="+ind_config+"&cod_ciudad="+cod_ciudad+"&ano="+ano+"&mes="+mes+"&dia="+dia,
        async: false,
        beforeSend: function()
        {
          blockScreen( "Eliminando Fecha" );
        },
        success: function( datos )
        {
          UnblockScreen();
          
          if( datos == '9999' )
          {
            Alerta( 'Atenci\xf3n', 'La Fecha No se ha Podido Eliminar la Fecha', '', '' );
          }
          else
          {
            Alerta( 'Correcto', 'La Fecha ha sido Eliminada Correctamente', '', 'fest' );
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

function ValidateTransp( origen )
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
        url: "../"+ Standa +"/infast/ajax_indica_solnov.php",
        data: "option=ValidateTransp&standa="+Standa+"&filter="+filter+"&cod_transp="+cod_transp.split('-')[0],
        async: false,
        success: function( datos )
        {
          if( datos == 'n' )
          {
            $("#ciudadTD").html('');
            alert("El Cliente < "+ cod_transp +" > no existe");
            return false;
          }
          else
          {
            if( origen == 3 )
            {
              ciudadTransp(Standa, cod_transp);
            }

            if( origen == 1 || origen == 2 )
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

function ciudadTransp(Standa, cod_transp)
{
  try{
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_indica_solnov.php",
      data: "option=getCiudadTransp&Ajax=on&standa="+Standa+"&cod_transp="+cod_transp.split('-')[0],
      async: false,
      success: function( datos ){
        $("#ciudadTD").html(datos);
      }
    });
  }
  catch(e)
  {
    console.log( "Error Function ciudadTransp: "+e.message+"\nLine: "+e.lineNumber );
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
    var ind_config = $("#ind_configID").val();
    var cod_ciudad = $("#cod_ciudadID").val();

    $("#transpID").val( cod_transp );

    $("#resultID").css( {'background-color':'#f0f0f0', 'border':'1px solid #c9c9c9','padding':'5px', 'width':'98%', 'min-height':'50px','-moz-border-radius':'5px 5px 5px 5px', '-webkit-border-radius':'5px 5px 5px 5px', 'border-top-left-radius':'5px', 'border-top-right-radius':'5px', 'border-bottom-right-radius':'5px', 'border-bottom-left-radius':'5px'} );
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_indica_solnov.php",
      data: "option=MainForm&standa="+Standa+"&cod_transp="+cod_transp+"&nom_transp="+nom_transp.replace(/&/g,"/-/")+"&ind_config="+ind_config+"&cod_ciudad="+cod_ciudad,
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

function CreateConfig( cod_tercer, ind_config, cod_ciudad )
{
  try
  {
  	var Standa = $( "#StandaID" ).val();
    
  	$("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Creaci\xf3n de Parametrizaci\xf3n",
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
          NewParametrizacion( ind_config, cod_ciudad );
        },
        Cerrar : function() 
        { 
          $(this).dialog('close');
          var cod_transp = $( "#cod_transpID" ).val();
          ShowForm( cod_transp );
        }
      }
    });

  	$.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_indica_solnov.php",
      data: "option=CreateConfig&standa="+Standa+"&cod_tercer="+cod_tercer+"&ind_config="+ind_config+"&cod_ciudad="+cod_ciudad,
      async: false,
      beforeSend: function()
      {
        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function( datos )
      {
        $("#PopUpID").html( datos );
      }
    });

  }
  catch( e )
  {
  	console.log( e.message );
    return false;
  }
}

function NewParametrizacion( ind_config, cod_ciudad )
{
  try
  {
  	var Standa = $( "#StandaID" ).val();
  	
  	var cod_usuari = $( "#cod_usuariID" ).val();
  	
  	var hor_ingedi = $( "#hor_ingresID" ).val();
  	var hor_saledi = $( "#hor_salidaID" ).val();

  	var nue_combin = '';
  	$( 'input[name="nom_diasxx"]' ).each( function( index ) {
	  if( $( this ).is( ':checked' ) )
	  	nue_combin += nue_combin != '' ? '|' + $( this ).val() :  $( this ).val();
	});

	if( nue_combin == '' )
	{
	  Alerta( 'Atenci\xf3n', 'Seleccione por lo Menos un D\xeda de la Semana', '', '' );
	}
	else
	{
	  $.ajax({
        type: "POST",
        url: "../"+ Standa +"/infast/ajax_indica_solnov.php",
        data: "option=NewParametrizacion&standa="+Standa+"&cod_usuari="+cod_usuari+"&nue_combin="+nue_combin+"&hor_saledi="+hor_saledi+"&hor_ingedi="+hor_ingedi+"&ind_config="+ind_config+"&cod_ciudad="+cod_ciudad,
        async: false,
        beforeSend: function()
        {
          $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
        },
        success: function( resultado )
        {
	      $("#PopUpID").dialog("destroy");
	        
	      if( resultado == '1000')
	        Alerta( 'Realizado', 'Configuraci\xf3n Creada Correctamente', '', 'setForm' );
	      else
	        Alerta( 'Error', 'La configuraci\xf3n no pudo ser Creada Correctamente, por favor intente nuevamente', '', 'setForm' );
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

function InsertFestivo( cod_tercer, ind_config, cod_ciudad )
{
  try
  {
  	var Standa = $( "#StandaID" ).val();
  	var fec_insert = $( "#fec_insertID" ).val();

  	if( fec_insert == '' )
  	{
  	  Alerta( 'Atenci\xf3n', 'Seleccione la fecha a configurar', $( "#fec_insertID" ), '' );
  	}
  	else
  	{
  	  $.ajax({
        type: "POST",
        url: "../"+ Standa +"/infast/ajax_indica_solnov.php",
        data: "option=InsertFestivo&standa="+Standa+"&fec_insert="+fec_insert+"&cod_transp="+cod_tercer+"&ind_config="+ind_config+"&cod_ciudad="+cod_ciudad,
        async: false,
        beforeSend: function()
        {
          blockScreen( "Registrando Fecha" );
        },
        success: function( datos )
        {
          UnblockScreen();
          
          if( datos == '9999' )
          {
			      Alerta( 'Atenci\xf3n', 'La Fecha Ya se encuentra Configurada como una festividad', '', '' );
          }
          else if( datos == '1991' )
          {
          	Alerta( 'Atenci\xf3n', 'La Fecha No ha sido Configurada como una festividad. Por favor Intente Nuevamente', '', '' );
          }
          else
          {
          	Alerta( 'Correcto', 'La Fecha ha sido Configurada como una festividad', '', 'fest' );
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

function SetFestivos( sel )
{
  try
  {
    if ( $("#FestivosID").is (':visible') )
	  $("#FestivosID").hide( 'blind' );

    var ind_config = $("#ind_configID").val();
    var cod_ciudad = $("#cod_ciudadID").val();
  	
  	if( sel.val() != '' )
  	{
  	  
  	  blockScreen( "Cargando Festivos de " + sel.val() );

   	  var Standa = $( "#StandaID" ).val();
   	  var cod_transp = $( "#cod_transpID" ).val();

  	  $.ajax({
        type: "POST",
        url: "../"+ Standa +"/infast/ajax_indica_solnov.php",
        data: "option=getFestivos&standa="+Standa+"&sel_yearxx="+sel.val()+"&cod_transp="+cod_transp.split(" - ")[0]+"&ind_config="+ind_config+"&cod_ciudad="+cod_ciudad,
        async: false,
        success: function( datos )
        {
          UnblockScreen();
          $("#FestivosID").html( datos );
          $("#FestivosID").show( 'blind' );
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

function EditConfig( cod_tercer, cod_diasxx, ind_config, cod_ciudad )
{
  try
  {
  	var Standa = $( "#StandaID" ).val();
    
  	$("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Modificaci\xf3n de Parametrizaci\xf3n",
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
          ChangeParametrizacion( ind_config, cod_ciudad );
        },
        Eliminar : function() 
        { 
          DropParametrizacion( ind_config, cod_ciudad );
        },
        Cerrar : function() 
        { 
          $(this).dialog('close');
          var cod_transp = $( "#cod_transpID" ).val();
          ShowForm( cod_transp );
        }
      }
    });

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_indica_solnov.php",
      data: "option=EditForm&standa="+Standa+"&cod_tercer="+cod_tercer+"&cod_diasxx="+cod_diasxx+"&ind_config="+ind_config+"&cod_ciudad="+cod_ciudad,
      async: false,
      beforeSend: function()
      {
        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function( datos )
      {
        $("#PopUpID").html( datos );
      }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function ChangeParametrizacion( ind_config, cod_ciudad )
{
  try
  {
  	var Standa = $( "#StandaID" ).val();
  	
  	var com_diasxx = $( "#com_diasxxID" ).val();
  	var cod_usuari = $( "#cod_usuariID" ).val();
  	
  	var hor_ingedi = $( "#hor_ingediID" ).val();
  	var hor_saledi = $( "#hor_salediID" ).val();

  	var nue_combin = '';
  	$( 'input[name="nom_diaxxx"]' ).each( function( index ) {
	  if( $( this ).is( ':checked' ) )
	  	nue_combin += nue_combin != '' ? '|' + $( this ).val() :  $( this ).val();
	});

	if( nue_combin == '' )
	{
	  Alerta( 'Atenci\xf3n', 'Seleccione por lo Menos un D\xeda de la Semana', '', '' );
	}
	else
	{
	  $.ajax({
        type: "POST",
        url: "../"+ Standa +"/infast/ajax_indica_solnov.php",
        data: "option=ChangeParametrizacion&standa="+Standa+"&com_diasxx="+com_diasxx+"&cod_usuari="+cod_usuari+"&nue_combin="+nue_combin+"&hor_saledi="+hor_saledi+"&hor_ingedi="+hor_ingedi+"&ind_config="+ind_config+"&cod_ciudad="+cod_ciudad,
        async: false,
        beforeSend: function()
        {
          $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
        },
        success: function( resultado )
        {
	      $("#PopUpID").dialog("destroy");
	        
	      if( resultado == '1000')
	        Alerta( 'Realizado', 'Configuraci\xf3n Actualizada Correctamente', '', 'setForm' );
	      else
	        Alerta( 'Error', 'La configuraci\xf3n no pudo ser Actualizada Correctamente, por favor intente nuevamente', '', 'setForm' );
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

function DropParametrizacion( ind_config, cod_ciudad )
{
  try
  {
  	var Standa = $( "#StandaID" ).val();
  	
  	var com_diasxx = $( "#com_diasxxID" ).val();
  	var cod_usuari = $( "#cod_usuariID" ).val();

  	$.ajax({
      type: "POST",
      url: "../"+ Standa +"/infast/ajax_indica_solnov.php",
      data: "option=DropParametrizacion&standa="+Standa+"&com_diasxx="+com_diasxx+"&cod_usuari="+cod_usuari+"&ind_config="+ind_config+"&cod_ciudad="+cod_ciudad,
      async: false,
      beforeSend: function()
      {
        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function( resultado )
      {
        $("#PopUpID").dialog("destroy");
        
        if( resultado == '1000')
          Alerta( 'Realizado', 'Configuraci\xf3n Eliminada Correctamente', '', 'setForm' );
        else
          Alerta( 'Error', 'La configuraci\xf3n no pudo ser Eliminada Correctamente, por favor intente nuevamente', '', 'setForm' );
      }
    });

  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function setForm( )
{
  var cod_transp = $( "#cod_transpID" ).val();
  ShowForm( cod_transp );
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
                    
                    if( adi_func == 'fest' )
                    	SetFestivos( $("#sel_yearxxID") ); 
                    
                    if( adi_func == 'setForm' )
                      setForm();
                    
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