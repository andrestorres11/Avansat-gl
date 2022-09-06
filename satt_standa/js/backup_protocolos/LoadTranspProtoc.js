$(document).ready(function() 
{
  var Standa = $( "#StandaID" ).val();
  var filter = $( "#filterID" ).val();
  
  $( "#cod_transpID" ).autocomplete({
    source: "../"+ Standa +"/protoc/ajax_protoc_transp.php?option=getTransp&standa="+Standa+"&filter="+filter,
    minLength: 2, 
    delay: 100
  });
});


function ValidateTransp()
{
  try
  {
    var Standa = $("#StandaID").val();
    var filter = $("#filterID").val();
    var transp = $("#cod_transpID").val();
    if( transp == '' )
    {
      alert("Digite la Transportadora");
      $("#cod_transpID").focus();
      return false;
    }
    else
    {
      var cod_transp = transp.split("-")[0].trim();
      
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/protoc/ajax_protoc_transp.php",
        data: "option=ValidateTransp&cod_transp="+cod_transp+"&filter="+filter,
        async: false,
        success : 
          function ( data ) 
          { 
            if( data == 'n' )
            {
              alert("La Transportadora no Existe");
              $("#cod_transpID").focus();
              return false;
            }
            else
            {
              ShowMainList( cod_transp );
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

function ShowMainList( cod_transp )
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    $("#transpID").val( cod_transp );

    $("#resultID").css( {'background-color':'#f0f0f0', 'border':'1px solid #c9c9c9','padding':'5px', 'width':'98%', 'min-height':'50px','-moz-border-radius':'5px 5px 5px 5px', '-webkit-border-radius':'5px 5px 5px 5px', 'border-top-left-radius':'5px', 'border-top-right-radius':'5px', 'border-bottom-right-radius':'5px', 'border-bottom-left-radius':'5px'} );
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/protoc/ajax_protoc_transp.php",
      data: "option=ShowMainList&standa="+Standa+"&cod_transp="+cod_transp,
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

function addTab() 
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    var tabs = $( "#tabs" ).accordion({collapsible:true,active: false});
    var noveda = $("#novedaID").val().split("(")[0];
    var size = noveda.length >= 20 ? 'style="font-size:9px;"' : '' ;
    if ( noveda.length >= 25 )
    {
      size = 'style="font-size:8px;"';
    }
    var tabTemplate = "<li aria-controls='#{aria}'><a "+size+"href='#{href}'>#{label}</a><span class='ui-icon ui-icon-close' role='presentation'></span></li>";
    var id = "tabs-" + noveda.split("-")[0].trim();
    
    if( $("#tabs").find( "#"+id ).val() == '' )
    {
      alert("La novedad ya existe");
      return false;
    }
    else
    {
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/protoc/ajax_protoc_transp.php",
        data: "option=getNewRow&standa="+Standa+"&cod_noveda="+noveda.split("-")[0].trim(),
        async: false,
        success: function( datos )
        {
          var ContentTable = datos;
          var tabContent = "<div>&nbsp;<br>" + noveda + "<br>&nbsp;</div><div  id='"+ id +"'>"+ContentTable+"</div>";
          
          tabs.append( tabContent );
          tabs.accordion( "destroy" );
          
          $( "#tabs" ).accordion({collapsible:true,active: false});
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

function NewNovedad()
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    
    $("#PopUpID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Selecci\xf3n de Novedad",
      width: $(document).width() - 700,
      heigth : 500,
      position:['middle',25], 
      bgiframe: true,
      closeOnEscape: false,
      show : { effect: "drop", duration: 300 },
      hide : { effect: "drop", duration: 300 },
      open: function(event, ui) { 
            $(this).parent().children().children('.ui-dialog-titlebar-close').hide();},
      buttons: {
        Continuar : function() 
        { 
          addTab();
          $(this).dialog('close');
        },
        Cerrar : function() 
        { 
          $(this).dialog('close');
        }
      }
    });
  
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/protoc/ajax_protoc_transp.php",
      data: "option=NewNovedad&standa="+Standa,
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

function SaveAllProtocols()
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    var cod_transp = $( "#transpID" ).val();
    
    var attr = 'option=SaveAllProtocols&Standa='+Standa+'&cod_transp='+cod_transp;
    var i;
    var j;
    $(".item").each( function( iSelect, select ) {
      $select = $(select);
      i = $select.attr("id").split("-")[1].trim();
      attr += '&noveda['+i+']=' + i;
      $select.find("option").each(function( iOption, option ){
        j = $(option).val();
        attr += '&noveda['+i+']['+j+']=' + j;
      });      
    });
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/protoc/ajax_protoc_transp.php",
      data: attr,
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

function DerogaProtocolo( num_id )
{
  $("#asi_protoc-"+num_id+"-ID option:selected" ).each(function() {
    mHtml = '<option value="'+ $(this).val() +'">' + $(this).text() + '</option>';
    $(this).remove();
    $("#all_protoc"+num_id+"-ID").append( mHtml );
  }); 
}

function AsignaProtocolo( num_id )
{
  $("#all_protoc"+num_id+"-ID option:selected" ).each(function() {
    mHtml = '<option value="'+ $(this).val() +'">' + $(this).text() + '</option>';
    $(this).remove();
    $("#asi_protoc-"+num_id+"-ID").append( mHtml );
  });
}

function deleteProtoc (cod_transp, cod_noveda){

  if(confirm("Desea Eliminar El protocolo")){

    var Standa = $( "#StandaID" ).val();
    var attr = "option=deleteProtoc&cod_transp="+cod_transp+"&cod_noveda="+cod_noveda;
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/protoc/ajax_protoc_transp.php",
      data: attr,
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
}