$(document).ready(function() 
{
  var Standa = $( "#StandaID" ).val();
  var filter = $( "#filterID" ).val();
  
  $( "#cod_transpID" ).autocomplete({
    source: "../"+ Standa +"/desnew/ajax_desnew_despac.php?option=getTransp&standa="+Standa+"&filter="+filter,
    minLength: 2, 
    delay: 100
  });
  
});

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
        url: "../"+ Standa +"/impact/ajax_impact_impact.php",
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
            MainForm( cod_transp.split('-')[0] );
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

function MainForm( cod_transp )
{
  try
  {
    var Standa = $("#StandaID").val();
    
    $.ajax({
        url: "../"+ Standa +"/impact/ajax_impact_impact.php",
        data : "Standa="+Standa+"&option=MainForm&cod_transp="+cod_transp,
        method : 'POST',
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
    return false;
  }
}

function NumericInput( evt )
{
  var keyPressed = (evt.which) ? evt.which : event.keyCode;
  return !(keyPressed > 31 && (keyPressed < 48 || keyPressed > 57) );
}

function SaveImpacto( cod_transp )
{
  try
  {
    var Standa = $("#StandaID").val();
    
    var cod_colorx = $("#colorpicker").val();
    var des_impact = $("#des_impactID");
    var ran_inicia = $("#ran_iniciaID");
    var ran_finali = $("#ran_finaliID");
    
    if( des_impact.val() == '' )
    {
      alert("Digite Descripci\xf3n");
      des_impact.focus();
      return false;
    }
    else if( ran_inicia.val() == '' )
    {
      alert("Digite Rango Inicial");
      ran_inicia.focus();
      return false;
    }
    else if( ran_finali.val() == '' )
    {
      alert("Digite Rango Final");
      ran_finali.focus();
      return false;
    }
    else if( parseInt( ran_inicia.val() ) > parseInt( ran_finali.val() ) )
    {
      alert("El rango inicial no debe ser mayor al rango final");
      ran_finali.focus();
      return false;
    }
    else
    {
      $.ajax({
        url: "../"+ Standa +"/impact/ajax_impact_impact.php",
        data : "option=SaveImpacto&Standa="+Standa+"&des_impact="+des_impact.val()+"&ran_inicia="+ran_inicia.val()+"&ran_finali="+ran_finali.val()+"&cod_colorx="+cod_colorx+"&cod_transp="+cod_transp,
        method : 'POST',
        beforeSend : 
          function () 
          { 
            $("#resultID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
          },
        success : 
          function ( data ) 
          { 
            // $("#resultID").html(data);
            MainForm( cod_transp );
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

function SaveMatcom( cod_transp )
{
  try
  {
    var Standa = $("#StandaID").val();
    var emailReg = 	/[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
    
    var validator = true;
    var counter = $("#counterID").val();
    var add = '&counter='+counter;
    for( var i = 0; i < counter; i++ )
    {
      if( $("#cod_tipdes"+i+"ID").val() == '' )
      {
        alert("Fila:"+( parseInt( i ) + 1 )+", Columna:4\n\nSeleccione el Tipo de Despacho.");
        validator = false;
        return false;
      }
      else
      {
        add += '&cod_ciuori'+i+'='+$("#cod_ciuori"+i+"ID").val();
        add += '&cod_ciudes'+i+'='+$("#cod_ciudes"+i+"ID").val();
        add += '&cod_produc'+i+'='+$("#cod_produc"+i+"ID").val().replace(/&/g,"/-/");
        add += '&cod_tipdes'+i+'='+$("#cod_tipdes"+i+"ID").val();
        add += '&ema_conpri'+i+'='+$("#ema_conpri"+i+"ID").val();
        add += '&ema_otrcon'+i+'='+$("#ema_otrcon"+i+"ID").val();
      }
    }
    if(validator)
    {
      var attr = "option=SaveMatcom&Standa=" + Standa +"&cod_transp="+cod_transp + add;
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/impact/ajax_impact_impact.php",
        data: attr,
        async: false,
        beforeSend : 
          function () 
          { 
            $.blockUI({ 
              theme:     true, 
              title:    'Matriz de Impacto', 
              draggable: false,
              message:  '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Cargando...</p></center>'
            });
            
          },
        success : 
          function ( datos ) 
          { 
            setTimeout( function(){$.unblockUI();},2000);
            setTimeout( function(){if( datos == "y" )
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
              $("#messageID").html( "<span>Matriz Parametrizada Correctamente</span>");
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
              $("#messageID").html( "<span>La Matriz no fue parametrizada</span>");
            }
            $("#messageID").show( "slide" );},2000);
            //MainForm( cod_transp );
            
            
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

function AddGrid()
{
  try
  {
    var Standa = $("#StandaID").val();
    var counter = $("#counterID").val();
    var cod_tipdes = $("#cod_tipdes"+(counter-1)+"ID");
    if( cod_tipdes.val() == '' )
    {
      alert("Fila:"+counter+", Columna:4\n\nSeleccione el Tipo de Despacho.");
    }
    else
    {
      var new_counter = $("#counterID").val();
      new_counter++;
      $.ajax({
      type: "POST",
      url: "../"+ Standa +"/impact/ajax_impact_impact.php",
      data: "option=generateDynamicGrid&counter="+ new_counter + "&ind_ajax=yes",
      async: false,
      success : 
        function ( data ) 
        { 
          $('#matcomID').append( data );
          $("#counterID").val( new_counter );
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

function DropImpacto( cod_transp, cod_impacto )
{
  try
  {
    var Standa = $("#StandaID").val();
    
    $.ajax({
        url: "../"+ Standa +"/impact/ajax_impact_impact.php",
        data : "option=DropImpacto&Standa="+Standa+"&cod_impacto="+cod_impacto+"&cod_transp="+cod_transp,
        method : 'POST',
        beforeSend : 
          function () 
          { 
            $("#resultID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
          },
        success : 
          function ( data ) 
          { 
            // $("#resultID").html(data);
            MainForm( cod_transp );
          }
      }); 
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function SendEditImpacto( cod_transp, cod_impacto )
{
  try
  {
    var Standa = $("#StandaID").val();
    
    var cod_colorx = $("#colorpicker2").val();
    var des_impact = $("#des_impact_ID");
    var ran_inicia = $("#ran_inicia_ID");
    var ran_finali = $("#ran_finali_ID");
    
    if( des_impact.val() == '' )
    {
      alert("Digite Descripci\xf3n");
      des_impact.focus();
      return false;
    }
    else if( ran_inicia.val() == '' )
    {
      alert("Digite Rango Inicial");
      ran_inicia.focus();
      return false;
    }
    else if( ran_finali.val() == '' )
    {
      alert("Digite Rango Final");
      ran_finali.focus();
      return false;
    }
    else if( parseInt( ran_inicia.val() ) > parseInt( ran_finali.val() ) )
    {
      alert("El rango inicial no debe ser mayor al rango final");
      ran_finali.focus();
      return false;
    }
    else
    {
      $.ajax({
        url: "../"+ Standa +"/impact/ajax_impact_impact.php",
        data : "option=SendEditImpacto&Standa="+Standa+"&des_impact="+des_impact.val()+"&ran_inicia="+ran_inicia.val()+"&ran_finali="+ran_finali.val()+"&cod_colorx="+cod_colorx+"&cod_impacto="+cod_impacto+"&cod_transp="+cod_transp,
        method : 'POST',
        beforeSend : 
          function () 
          { 
            $("#FormEditID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
          },
        success : 
          function ( data ) 
          { 
            //$("#FormEditID").html(data);
            MainForm( cod_transp );
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

function EditImpacto( cod_transp, cod_impacto )
{
  try
  {
    var Standa = $("#StandaID").val();
    
    $.ajax({
      url: "../"+ Standa +"/impact/ajax_impact_impact.php",
      data : "option=EditImpacto&Standa="+Standa+"&cod_impacto="+cod_impacto+"&cod_transp="+cod_transp,
      method : 'POST',
      success : 
        function ( data ) 
        { 
          $("#FormEditID").html( data );
          $("#FormEditID").fadeIn();
        }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}