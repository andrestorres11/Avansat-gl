$(document).ready(function() 
{
  var Standa = $( "#StandaID" ).val();
  var filter = $( "#filterID" ).val();
  
  $( "#cod_transpID" ).autocomplete({
    source: "../"+ Standa +"/comuni/ajax_modulo_comuni.php?option=getTransp&standa="+Standa+"&filter="+filter,
    minLength: 2, 
    delay: 100
  }).bind( "autocompleteclose", function(event, ui){ValidateTransp();} );

  $( "#cod_transpID" ).bind( "autocompletechange", function(event, ui){ ValidateTransp(); } );

  // Listar
  $( "#cod_transp2ID" ).autocomplete({
      source: "../"+ Standa +"/comuni/ajax_modulo_comuni.php?option=getTransp&standa="+Standa+"&filter="+filter,
      minLength: 2, 
      delay: 100
    }).bind( "autocompleteclose", function(event, ui){ValidateTransp();} );
   $( "#cod_transp2ID" ).bind( "autocompletechange", function(event, ui){ ValidateTransp(); } );
  
});

function SetUsuari( nom_campox, tip_consul, cod_transp )
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    $("#AlarmaID").hide("blind");
    $("#FormElementsID").hide("blind");
    
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
      data: "option=SetUsuari&standa="+Standa+"&nom_campox="+nom_campox.val()+"&tip_consul="+tip_consul+"&cod_transp="+cod_transp,
      async: false,
      beforeSend: function()
      {
        $("#filtrosID").hide("blind");
      },
      success: function( datos )
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
          $("#AlarmaID").html('<span width="60%" style="background:none repeat scroll 0 0 #FFFFAD;border: 1px solid #939300;border-radius:4px 4px 4px 4px;color:#333333;font-family:Arial;font-size:12px;padding:10px;width:100%;" id="messageID">El Usuario No se Encuentra Asociado a la Transportadora</span>');
          $("#AlarmaID").show("blind");
          $("#nom_usuariID").focus();
        }
        else
        {
          $("#ema_usuariID").val( datos.split('|')[2] );
          $("#cod_usuariID").val( datos.split('|')[0] );
          $("#nom_usuariID").val( datos.split('|')[1] );
          setFormElements( datos.split('|')[0], '', '' );
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

function CancelEditForm()
{
  try
  {
    $("#FormElementsID").hide("blind");
    setFormElements( $("#cod_usuariID").val(), '', '' );
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function UpdateConfig( cod_noveda, cod_usuari )
{
  try
  { 
    var attribs = '&cod_usuari=' + cod_usuari+'&cod_noveda=' + cod_noveda;
    var Standa = $( "#StandaID" ).val();

    $(".cargueP, .cargueS, .transiP, .transiS, .descarP, .descarS").each( function(){
      if( $(this).is( ':checked' ) )
      {
        attribs += '&cod_tipres=' + $(this).val();
      }
    });

    var bandera = false;
    var i = 0;

    bandera = false;
    $(".tipope").each( function(){
      if( $(this).is( ':checked' ) )
      {
        bandera = true;
        attribs += '&cod_tipdes[' + i + ']=' + $(this).val();
        i++;
      }
    });

    if( !bandera )
    {
      alert("Seleccione por lo Menos un Tipo de Operaci\xf3n a Configurar");
      return false;
    }

    i = 0;
    bandera = false;
    $(".produc").each( function(){
      if( $(this).is( ':checked' ) )
      {
        bandera = true;
        attribs += '&cod_produc[' + i + ']=' + $(this).val();
        i++;
      }
    });

    if( !bandera )
    {
      alert("Seleccione por lo Menos un Producto a Configurar");
      return false;
    }

    i = 0;
    bandera = false;
    $(".origen").each( function(){
      if( $(this).is( ':checked' ) )
      {
        bandera = true;
        attribs += '&cod_ciuori[' + i + ']=' + $(this).val();
        i++;
      }
    });

    if( !bandera )
    {
      alert("Seleccione por lo Menos un Origen a Configurar");
      return false;
    }

    i = 0;
    $(".zonaxx").each( function(){
      if( $(this).is( ':checked' ) )
      {
        attribs += '&cod_zonaxx[' + i + ']=' + $(this).val();
        i++;
      }
    });
    
    i = 0;
    $(".tiptra").each( function(){
      if( $(this).is( ':checked' ) )
      {
        attribs += '&cod_tiptra[' + i + ']=' + $(this).val();
        i++;
      }
    });
    
    i = 0;
    $(".canalx").each( function(){
      if( $(this).is( ':checked' ) )
      {
        attribs += '&cod_canalx[' + i + ']=' + $(this).val();
        i++;
      }
    });
    
    i = 0;
    $(".destin").each( function(){
      if( $(this).is( ':checked' ) )
      {
        attribs += '&cod_ciudes[' + i + ']=' + $(this).val();
        i++;
      }
    });

	i = 0;
    $(".deposi").each( function(){
      if( $(this).is( ':checked' ) )
      {
        attribs += '&cod_deposi[' + i + ']=' + $(this).val();
        i++;
      }
    });

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
      data: "option=UpdateConfig" + attribs,
      async: false,
      beforeSend: function()
      {
        $("#resultID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function( datos )
      {
        $("#resultID").html( datos );
        $("#resultID").show("blind");
      }
    });
    
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function setFormElements( cod_usuari, ind_editar, cod_noveda )
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    var cod_transp = $( "#cod_transpID" ).val().split(' - ')[0];

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
      data: "option=setFormElements&cod_usuari="+cod_usuari+"&cod_transp="+cod_transp+"&ind_editar="+ind_editar+"&cod_noveda="+cod_noveda,
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

function InsertConfig()
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    var bandera = false;
    var attribs = '&cod_usuari=' + $("#cod_usuariID").val();
    var i = 0;
    $(".cargueP, .cargueS, .transiP, .transiS, .descarP, .descarS").each( function(){
      if( $(this).is( ':checked' ) )
      {
        bandera = true;
        attribs += '&inf_noveda[' + i + '][cod_noveda]=' + $(this).attr('id').replace('ID', '') + '&inf_noveda[' + i + '][cod_tipres]=' + $(this).val();
        i++;
      }
    });

    if( !bandera )
    {
      alert("Seleccione por lo Menos una Novedad a Configurar");
      return false;
    }

    i = 0;
    bandera = false;
    $(".tipope").each( function(){
      if( $(this).is( ':checked' ) )
      {
        bandera = true;
        attribs += '&cod_tipdes[' + i + ']=' + $(this).val();
        i++;
      }
    });

    if( !bandera )
    {
      alert("Seleccione por lo Menos un Tipo de Operaci\xf3n a Configurar");
      return false;
    }

    i = 0;
    bandera = false;
    $(".produc").each( function(){
      if( $(this).is( ':checked' ) )
      {
        bandera = true;
        attribs += '&cod_produc[' + i + ']=' + $(this).val();
        i++;
      }
    });

    if( !bandera )
    {
      alert("Seleccione por lo Menos un Producto a Configurar");
      return false;
    }

    i = 0;
    bandera = false;
    $(".origen").each( function(){
      if( $(this).is( ':checked' ) )
      {
        bandera = true;
        attribs += '&cod_ciuori[' + i + ']=' + $(this).val();
        i++;
      }
    });

    if( !bandera )
    {
      alert("Seleccione por lo Menos un Origen a Configurar");
      return false;
    }

    i = 0;
    $(".zonaxx").each( function(){
      if( $(this).is( ':checked' ) )
      {
        attribs += '&cod_zonaxx[' + i + ']=' + $(this).val();
        i++;
      }
    });
    
    i = 0;
    $(".tiptra").each( function(){
      if( $(this).is( ':checked' ) )
      {
        attribs += '&cod_tiptra[' + i + ']=' + $(this).val();
        i++;
      }
    });
    
    i = 0;
    $(".canalx").each( function(){
      if( $(this).is( ':checked' ) )
      {
        attribs += '&cod_canalx[' + i + ']=' + $(this).val();
        i++;
      }
    });
    
    i = 0;
    $(".destin").each( function(){
      if( $(this).is( ':checked' ) )
      {
        attribs += '&cod_ciudes[' + i + ']=' + $(this).val();
        i++;
      }
    });

	i = 0;
    $(".deposi").each( function(){
      if( $(this).is( ':checked' ) )
      {
        attribs += '&cod_deposi[' + i + ']=' + $(this).val();
        i++;
      }
    });

    if( validateResponse( attribs ) )
    {
      $.ajax({
	    type: "POST",
	    url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
	    data: "option=InsertConfig" + attribs,
	    async: false,
	    beforeSend: function()
	    {
	      $("#resultID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
	    },
	    success: function( datos )
	    {
	      $("#resultID").html( datos );
	      $("#resultID").show("blind");
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

function validateResponse( attribs )
{
  try
  {
  	var Standa = $( "#StandaID" ).val();
  	var resultado = false;
  	$.ajax({
      type: "POST",
      url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
      data: "option=validateResponse" + attribs,
      async: false,
      beforeSend: function()
      {
        $("#resultID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function( datos )
      {
        if( datos == 'n' )
        {
          resultado = true;
        }
        else
        {
          $("#resultID").html( datos );
          $("#resultID").show("blind");
          resultado = false;
        }
      }
    });
  	return resultado;
  }
  catch( e )
  {
  	console.log( e.message );
  }
}

function EditConfig( cod_noveda, cod_usuari )
{
  try
  {
    $("#FormElementsID").hide("blind");
    setFormElements( cod_usuari, 'yes', cod_noveda );
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  } 
}

function DeleteConfig( cod_noveda, cod_usuari )
{
  try
  {
    var Standa = $( "#StandaID" ).val();

    if( confirm("Realmente Desea Eliminar la Configurac\xf3in Parametrizada?") )
    {
      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
        data: "option=DeleteConfig&standa="+Standa+"&cod_noveda="+cod_noveda+"&cod_usuari="+cod_usuari,
        async: false,
        success: function( datos )
        {
          $("#resultID").html( datos );
          $("#resultID").show("blind");
        }
      });
    }

    console.log( cod_noveda + " - " + cod_usuari );
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  } 
}

function UpCiudes()
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    var filter = $( "#filterID" ).val();
    var cod_ciudes = $("#cod_ciudesID").val().split(' - ')[0].split('|')[1];
    
    if( $("#cod_ciudesID").val() == '' )
    {
      return false;
    }

    if ($( "#destin" + cod_ciudes + "ID" ).length)
    {
      $("#cod_ciudesID").val('');
      return false;
    }

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
      data: "option=UpCiudes&standa="+Standa+"&filter="+filter+"&cod_ciudes="+cod_ciudes,
      async: false,
      success: function( datos )
      {
        if( datos != 'n' )
        {
          $('#gridDestinID tr').last().before( datos );
          $("#cod_ciudesID").val('');
        }
      }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function ValidateTransp( opc )
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    var filter = $( "#filterID" ).val();
    var listar = $( "#listarID" ).val();
    if(listar == "listar")
    var cod_transp = $( "#cod_transp2ID" ).val();
    else
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
        url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
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
            if(listar == 'listar')
            {
               $("#cod_transp2ID").val( cod_transp.split('-')[0].trim() );
               $("#nom_empresID").html( cod_transp.split('-')[1].trim() );
            }
            else
            {
              ShowForm( cod_transp, listar, opc );
            }
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

function DeSelectAll( clase, boton, clasegen )
{
  try
  {
    $( '.' + clase +'P, .'+ clase + 'S, .'+ clasegen + 'P, .'+ clasegen + 'S'  ).attr('checked', false );
    $( 'label[for='+ clasegen + 'P], label[for='+ clasegen + 'S]' ).removeClass('ui-state-active');
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function SelectAll( clase, boton )
{
  try
  {
    $( '.' + clase ).attr('checked',( boton.is( ':checked' ) ) ? true : false );
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function ShowForm( cod_transp, opcion , opc )
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
      url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
      data: "option="+( opcion == "listar" ? "ListarCorreos" : "MainForm")+"&standa="+Standa+"&cod_transp="+cod_transp+"&nom_transp="+nom_transp.replace(/&/g,"/-/")+"&duplici="+opc,
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

function LoadConfigUsuari(date, novedad )
{
  try
  {
   
    AjaxJquery("LoadDetailUsuari", date, novedad);
  }
  catch(e)
  {
    alert("Error en LoadConfigUsuari()"+e.message+"\nLine:"+e.lineNumber);
  }
}


function LoadDialog( option, novedad )
{
  try
  {
    if( option )
    {
      var list = [];
      list["c"] = "CARGUE";
      list["t"] = "TRANSITO";
      list["d"] = "DESCARGUE";
   
      var html = '<div id="MessageDialogID"><center><b>Cargando</b></center></div>';
      $(html).dialog({
        modal: true,
        draggable: false,
        resizable: false,
        closeonEscape: false,
        title: "NOVEDADES EN "+list[novedad],
        open: function(){
          
        },
        close: function(){
          $("#MessageDialogID").dialog("destroy").remove();
        }
      })
    }
    else
    {
      $("#MessageDialogID").dialog("destroy").remove();
    }
  }
  catch(e)
  {
    alert("Error en LoadDialog()"+e.message+"\nLine:"+e.lineNumber);
  }
}

function AjaxJquery(opcion, date, novedad)
{
  try
  {
    var Standa = $( "#StandaID" ).val();
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
      data: "option="+opcion+"&cod_usuari="+date+"&cla_noveda="+novedad,
      beforeSend: function() {
         LoadDialog("open", novedad);
      },
      success: function(data) {
        $("#MessageDialogID").html(data); 
      },
      complete: function() {
        ResizeDialog();
      } 
    });
  }
  catch(e)
  {
    alert("Error en AjaxJquery()"+e.message+"\nLine:"+e.lineNumber);
  }
}

  function ResizeDialog()
  {
    try
    {
      var dialog = $(".ui-dialog");
      var w = $(window).width(); 
      var h = $(window).height(); 
      dialog.animate({top: "10px", "left":"10px"}).css({"width":(w - 20)+"px", "height":(h - 20)+"px","overflow":"auto" });
    }
    catch(e)
    {
      alert("Error en AjaxJquery()"+e.message+"\nLine:"+e.lineNumber);
    }
  }

  function ListarDuplicados()
  {
    try
    {
      var Standa = $( "#StandaID" ).val();
      var cod_transp = $( "#cod_transp2ID" ).val();
      var cod_noveda = $( "#cod_novedaID" ).val();
      var tip_correo = $( "#tip_correoID" ).val();
      var cod_ciudes = $( "#cod_ciudesID" ).val();
      var cod_ciuori = $( "#cod_ciuoriID" ).val();
      var cod_produc = $( "#cod_producID" ).val();
      var cod_operad = $( "#cod_operacID" ).val();
      var cod_tiptra = $( "#cod_tiptraID" ).val();
      var cod_zonasx = $( "#cod_zonasxID" ).val();
      var cod_canalx = $( "#cod_canalxID" ).val();
      var cod_deposi = $( "#cod_deposiID" ).val();
      var novedad = "";

      if(!cod_noveda)
      {
        alert("Por favor seleccione por lo menos una novedad.");
        $( "#cod_novedaID" ).click();
        return false;
      }

      $.ajax({
        type: "POST",
        url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
        data: "option=ListarDuplicados&cod_transp="+cod_transp+"&cod_noveda="+cod_noveda+"&cod_ciudes="+cod_ciudes+
                                     "&cod_ciuori="+cod_ciuori+"&cod_produc="+cod_produc+"&cod_tipdes="+cod_operad+
                                     "&cod_tiptra="+cod_tiptra+"&cod_zonasx="+cod_zonasx+"&cod_canalx="+cod_canalx+
                                     "&cod_deposi="+cod_deposi+"&tip_correo="+tip_correo,
        beforeSend: function() {
           LoadDialog("open", novedad);
        },
        success: function(data) {
          $("#MessageDialogID").html(data); 
        },
        complete: function() {
          ResizeDialog();
        } 
      });
    }
    catch(e)
    {
      alert("Error en ListarDuplicados: "+e.message+"\nLine:"+e.lineNumber);
    }
  }



  function ListatAsignacion(banderaListAsig){
      try{
        var Standa = $( "#standaID" ).val();
        if(banderaListAsig==0)
        {
          $("#resultadoUsuarioID").html(); 
          //var cod_transpID=('#cod_transpID').val();
          var paraID;
          var copiaID;
          var mTypeDestin;
          var nom_usuarioID=$('#nom_usuarioID').val();
          var cod_nomUsuarioID=$('#cod_nomUsuarioID').val();
          
          if($('#paraID').is(':checked')){
              paraID="P";
          }else{
              paraID="0";
          }
          if($('#copiaID').is(':checked')){

              copiaID="C";
          }else{
              copiaID="0";
          }
          if((paraID!="0" && copiaID!="0") || (paraID=="P" && copiaID=="0") || (paraID=="0" && copiaID=="C"))
          {
              if(nom_usuarioID!="")
              {
                $.ajax({
                    type: "POST",
                    url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
                    data: "option=ListarAsignacionesUsuarios&cod_nomUsuarioID="+cod_nomUsuarioID+"&nom_usuario="+nom_usuarioID+"&mTypeDestin[0]="+paraID+"&mTypeDestin[1]="+copiaID,
                    beforeSend: function() {
                       DialogAsignacion("Buscando datos relacionados ");
                    },
                    success: function(data) {
                      $("#resultadoUsuarioID").html(data); 
                      $('#ExportExcelUsuariosID').attr('disabled', false);
                      $("#sec2ID").css({"height":"auto"});
                    },
                    complete: function() {
                      $( "#MessageDialogID" ).dialog( "destroy" ).remove();
                    },
                    timeout:100000
                  });
              }else{
                  alert("La selecci\xf3n de un usuario es de car\xe1cter obligatorio");
                  $('#nom_usuarioID').focus();
              }

          }
          else
          {
              alert("La selecci\xf3n de un tipo de contacto es de car\xe1cter obligatorio");
                  $('#paraID').focus();
          }
 
          
            
        }else if(banderaListAsig==1)
        {
          $("#resultUsuarioCaract").html();
          var SeltNovedadID=$('#SeltNovedadID').val();
          var SeltTipCorreoID=$('#SeltTipCorreoID').val();
          var cod_tipdes=$('#SeltTipOperacionID').val();
          var cod_ciuori=$('#SeltTipOrigenID').val();
          var cod_ciudes=$('#SeltTipDestinoID').val();
          var cod_produc=$('#SeltTipProductoID').val();
          var cod_zonaxx=$('#SeltTipZonaID').val();
          var cod_canalx=$('#SeltTipCanalID').val();
          var cod_deposi=$('#SeltTipDepositoID').val();
          var srtcod_ciudes="";
          var srtcod_zonaxx="";
          var srtcod_canalx="";
          var srtcod_deposi="";

          if(cod_ciudes!='0')
          {
            srtcod_ciudes="&cod_ciudes="+cod_ciudes;
          }

          if(cod_zonaxx!='0')
          {
            srtcod_zonaxx="&cod_zonaxx="+cod_zonaxx;
          }

          if(cod_canalx!='0')
          {
            srtcod_canalx="&cod_canalx="+cod_canalx;
          }

          if(cod_deposi!='0')
          {
            srtcod_deposi="&cod_deposi="+cod_deposi;
          }

          if(SeltNovedadID!="0")
          {
              if(SeltTipCorreoID!="0")
              {
                  if(cod_tipdes!="0")
                  {
                      if(cod_ciuori!="0")
                      {
                          if(cod_produc!="0")
                          {
                              $.ajax({
                                type: "POST",
                                url: "../"+ Standa +"/comuni/ajax_modulo_comuni.php",
                                data: "option=ListarAsignacionesCaract&SeltNovedadID="+SeltNovedadID
                                +"&SeltTipCorreoID="+SeltTipCorreoID+"&cod_tipdes="+cod_tipdes
                                +"&cod_ciuori="+cod_ciuori+srtcod_ciudes+"&cod_produc="+cod_produc
                                +srtcod_zonaxx+srtcod_canalx+srtcod_deposi,
                                beforeSend: function() {
                                   DialogAsignacion("Buscando datos relacionados ");
                                },
                                success: function(data) {
                                  $("#resultUsuarioCaract").html(data);
                                  $('#ExportExcelCaraID').attr('disabled', false);
                                  $("#sec2ID").css({"height":"auto"});
                                },
                                complete: function() {
                                  $( "#MessageDialogID" ).dialog( "destroy" ).remove();
                                } 
                              });
                          }
                          else
                          {
                              alert("La selecci\xf3n de un producto es de car\xe1cter obligatorio");
                              $('#SeltTipProductoID').focus();
                          }
                      }
                      else
                      {
                          alert("La selecci\xf3n de un origen es de car\xe1cter obligatorio");
                          $('#SeltTipOrigenID').focus();
                      }
                  }
                  else
                  {
                      alert("La selecci\xf3n de una operacion es de car\xe1cter obligatorio");
                      $('#SeltTipOperacionID').focus();  
                  }
              }
              else
              {
                  alert("La selecci\xf3n de un tipo de correo es de car\xe1cter obligatorio");
                  $('#SeltTipCorreoID').focus();
              }
          }
          else 
          {
              alert("La selecci\xf3n de una novedad es de car\xe1cter obligatorio");
              $('#SeltNovedadID').focus();
          }

        }    
      }catch(e){
        alert("Error en ListatAsignacion: "+e.message+"\nLine:"+e.lineNumber);
      }
      
    
  }

  function DialogAsignacion(titulo){
    try
    {
      var html = '<div id="MessageDialogID"><center><b>Cargando</b></center></div>';
      $(html).dialog({
        modal: true,
        draggable: false,
        resizable: false,
        closeonEscape: false,
        title: titulo,
        open: function(){
          
        },
        close: function(){
          $("#MessageDialogID").dialog("destroy").remove();
        }
      })
      
     
    }
    catch(e)
    {
      alert("Error en LoadDialog()"+e.message+"\nLine:"+e.lineNumber);
    }

  }

  function getNomTrans(){
      try
      {
          var Standa = $( "#standaID" ).val();
          $("#nom_transpID").autocomplete({
              source: "../" + Standa + "/comuni/ajax_modulo_comuni.php?option=getNomTrans",
              minLength: 3,
              select: function(event, ui) {
                  $("#cod_transpID").val(ui.item.id);
              }
          });

           
      }catch(e){
        alert("Error en getNomTrans: "+e.message+"\nLine:"+e.lineNumber);
      }
      
    
  }

  function getNomUsuario(){
      try
      {
          var Standa = $( "#standaID" ).val();
          $("#nom_usuarioID").autocomplete({
              source: "../" + Standa + "/comuni/ajax_modulo_comuni.php?option=getNomUsuario",
              minLength: 3,
              select: function(event, ui) {
                  $("#cod_nomUsuarioID").val(ui.item.id);
              }
          });

           
      }catch(e){
        alert("Error en getNomUsuario: "+e.message+"\nLine:"+e.lineNumber);
      }
      
    
  }

  function ExportExcelUs(){
      try
      {
          window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#Tbl_AsignacionUserID').html()));

           
      }catch(e){
        alert("Error en ExportExcelUs: "+e.message+"\nLine:"+e.lineNumber);
      }
      
    
  }

  function ExportExcelCara(){
      try
      {   
          window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#Tbl_AsignacionCaraID').html()));

           
      }catch(e){
        alert("Error en ExportExcelCara: "+e.message+"\nLine:"+e.lineNumber);
      }
      
    
  }

