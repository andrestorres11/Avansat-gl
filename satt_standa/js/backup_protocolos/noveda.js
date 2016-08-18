/*! \file: novedad.js
  *  \brief: 
  *  \author: 
  *  \author: 
  *  \version: 
  *  \date: 25/05/2015
  *  \bug: 
  *  \bug: 
  *  \warning: 
  */ 


function UpperText( campo )
{
  var ant_text = campo.val();
  campo.val( ant_text.toUpperCase() );
}


function aceptar_ins(formulario)
{
  try {
    
    if( $("#ind_protocID").val() == 'yes' )
    {
      var form_ins = $("#form_insID");
      var tot_protoc = $("#tot_protocID").val();
      
      form_ins.append('<input type="hidden" name="tot_protoc_" id="tot_protoc_ID" value="'+tot_protoc+'"/>');

      for ( var k = 0; k < tot_protoc; k++ )
      {
        form_ins.append('<input type="hidden" name="protoc'+k+'_" id="protoc'+k+'_ID" value="'+$("#protoc"+k+"ID").val()+'"/>');
        form_ins.append('<input type="hidden" name="obs_protoc'+k+'_" id="obs_protoc'+k+'_ID" value="'+$("#obs_protoc"+k+"ID").val()+'"/>');
      }
      var ind_activo = $("input[name='ind_activo']:checked").val();
      form_ins.append('<input type="hidden" name="ind_activo_" id="ind_activo_ID" value="'+ind_activo+'"/>');
            
    }
        
    validacion = true;
    formulario = document.form_ins;
    var sitio = document.getElementById('sitioID');
    var sit = document.getElementById('sitID');
    var nov_especi = document.getElementById('nov_especiID');
    var obs = document.getElementById('obsID');
    var noveda = document.getElementById('novedadID');
    var cod_lastpc = document.getElementById('cod_lastpcID');
    var cod_contro = document.getElementById('cod_controID');
    //Convirtienedo a Date la fecha del sistema
    var fecnov = document.getElementById('fecnovID');
    var hornov = document.getElementById('hornovID');
    var fecnovArray = fecnov.value.split("-");
    var hornovArray = hornov.value.split(":");
    var fecSistema = new Date(fecnovArray[2],Number(fecnovArray[1])-1,fecnovArray[0], hornovArray[0], hornovArray[1], hornovArray[2]);
    
    if(sit.value=='0')
    {
      alert("EL Antes/Sitio es Requerido");
      validacion=false;
      return sit.focus();
    }
    if(sitio.value=='')
    {
      alert("EL Sitio es Requerido");
      validacion=false;
      return sitio.focus();
    }
    if (document.getElementById("date")) {
      //Convirtiendo a Date la fecha a adicionar que seleccionan en el focmulario
      var date = document.getElementById("date");
      var hora = document.getElementById("hora");
      
      fecAdicArray = date.value.split("-"); 
      horAdicArray = hora.value.split(":");
      var fecAdic = new Date(fecAdicArray[0],Number(fecAdicArray[1])-1,fecAdicArray[2],horAdicArray[0],horAdicArray[1],0);
    }
		if (document.getElementById("tiemID") && sit.value=='N') {
			  if (document.getElementById("tiemID").value == '') {
					window.alert('Digite El tiempo Adicional es Obligatorio');
					return document.getElementById("tiemID").focus();
				}		
    }
    
    if( formulario.ind_calcon.value == '1' )
    {
      var num_califi = document.getElementById("num_califiID");
      var obs_califi = document.getElementById("obs_califiID");
      if( num_califi.value == '')
      {
        alert("Debe seleccionar una calificación para el conductor!");
        return false;
      }
      if( obs_califi.value == '')
      {
        alert("Debe digitar una observación para el conductor!");
        return false;
      }
    }
    
    
    if (formulario.novedadID.value == 0) {
      window.alert("La Novedad es Requerida")
      validacion = false
      if (nov_especi.value=="1" && obs.value==""){
        window.alert("La Observacion es Requerida ")
        validacion = false
        return formulario.obsID.focus();  
      }
      return formulario.novedadID.focus();
    }
    else
     {
       if (nov_especi.value=="1" && obs.value==""){
        window.alert("La Observacion es Requerida ")
        validacion = false
        formulario.obsID.focus();  
      }
      if (document.getElementById("date") && (date.value == "" || hora.value == "")) {
        window.alert('Digite El tiempo de Duracion de La novedad');
        return date.focus();
      }
      else {
        if (validacion) {
           if(parseInt(noveda.value)!='9998')
           {
             if( fecAdic <= fecSistema )
             {
               window.alert("La fecha a adicionar debe ser mayor a la fecha actual.");
               return date.focus();
             }
             else
             {
               if (confirm('Esta Seguro que Desea Insertar La novedad?')) 
               {
                 formulario.opcion.value = 3;
                 formulario.submit();
               } 
             }  
             
           }else{
             try
              {
                if (cod_lastpc.value == cod_contro.value && sit.value == 'S') {
                  window.alert("No se puede realizar cambio de ruta en el sitio del ultimo puesto del plan de ruta");
                  return noveda.focus();
                }
                else {
                  var url_archiv = document.getElementById('url_archivID');
                  var dir_aplica = document.getElementById('dir_aplicaID');
                  var despac = document.getElementById('despacID').value;
                  LoadPopup();
                  var atributes = "opcion=4";
                  atributes += "&despac=" + despac;
                  AjaxGetData("../" + dir_aplica.value + "/despac/" + url_archiv.value + "?", atributes, 'popupDIV', "post");
                }
              }
              catch (e)
              {
                  alert("Error -> Aceptar_ins() " + e.message);
              }
           }
          }
        }
      }
  }
  catch(e)
  {
    alert("Error funcion aceptar_ins " + e.message + '\n' + e.stack);
  }
}


function ShowProtocNoveda( attr )
{
  try
  {
    var Standa = $("#dir_aplicaID").val();
    attr += "&option=ShowProtocNoveda";

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/desnew/ajax_despac_novpro.php",
      data: attr,
      async: false,
      beforeSend: function()
      {
        $("#newPopupID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function( datos )
      {
        $("#newPopupID").html( datos );
      }
    });
    
    $("#newPopupID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Protocolo(s) asignado(s) a la Novedad",
      width: $(document).width() - 500,
      heigth : 700,
      position:['middle',25], 
      bgiframe: true,
      closeOnEscape: false,
      show : { effect: "drop", duration: 300 },
      hide : { effect: "drop", duration: 300 },
       open: function(event, ui) 
       { 
         $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
       }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}


function ShowNovedaSoluci( attr )
{
  try
  {
    var Standa = $("#dir_aplicaID").val();
    attr += "&option=ShowNovedaSoluci";

    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/desnew/ajax_despac_novpro.php",
      data: attr,
      async: false,
      beforeSend: function()
      {
        $("#PopupAsiID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function( datos )
      {
        $("#PopupAsiID").html( datos );
      }
    });
    
    $("#PopupAsiID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Solucion de Novedades",
      width: $(document).width() - 500,
      heigth : 700,
      position:['middle',25], 
      bgiframe: true,
      closeOnEscape: false,
      show : { effect: "drop", duration: 300 },
      hide : { effect: "drop", duration: 300 },
       open: function(event, ui) 
       { 
         $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
       }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function SaveAsignaciones()
{
  try
  {
    var total = $("#tot_pendieID").val();
    var llave;
    var validator = false;
    var variables = '';
    for( var i = 0; i < total; i++ )
    {
      llave = $( "#key_" + i + "ID" );
      if( llave.is(':checked') )
      {
        validator = true;
        variables += variables != '' ? '/-/' + llave.val() : llave.val();
      }
    }

    if( validator )
    {
      $("#PopupAsiID").dialog("close");
      $("#ind_resoluID").val( variables );
    }
    else
    {
      alert( "Seleccione por lo menos una novedad" );
      return false;
    }
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function SaveProtocols()
{
  try
  {
    /***********************************/
    var total = $("#tot_protocID").val();
    var des;
    var obsID = '';
    var val_itemxx;
    var ind_requer;
    var cod_tipoxx;
    var des_textox;
    var tex_encabe;
    var prefijo = '';
    var is_checked = true;
    var msj_alerta = '';
    /***********************************/

    for (var j = 0; j < total; j++ )
    {
      $("[id^=cod_subcau]").each(function(){
        ind_requer = $("#ind_requer"+$(this).val()+"ID").val();
        val_itemxx = $("#val_itemxx"+$(this).val()+"ID").val();
        tex_encabe = $("#tex_encabe"+$(this).val()+"ID").val();
        cod_tipoxx = $("#cod_tipoxx"+$(this).val()+"ID").val();
        des_textox = $("#des_texto"+$(this).val()+"ID").val();

        if( cod_tipoxx == '4' )
        {
          $("input[type=radio][name=val_itemxx"+$(this).val()+"]").each(function( indice, elemento ){
            
            if( ind_requer == '1' )
            {
              if( $(elemento).is(':checked') )
              {
                is_checked = false;
              }
            }
          });

          if( is_checked && ind_requer == '1' )
          {
            msj_alerta += msj_alerta != '' ? '\n- ' + tex_encabe : '- ' + tex_encabe; 
          }
        }
        else
        {
          if( ind_requer == '1' && val_itemxx == '' )
          {
            msj_alerta += msj_alerta != '' ? '\n- ' + tex_encabe : '- ' + tex_encabe;
          }
        }
        
        if( msj_alerta == '' )
        {
          switch( cod_tipoxx )
          {
            case '1':
            case '2':
              if( ind_requer == '0' && val_itemxx == '' )
                obsID += obsID != '' ? ", " + des_textox + " N/A"  : des_textox + " " + "N/A";
              else
                obsID += obsID != '' ? ", " + des_textox + " " + val_itemxx : des_textox + " " + val_itemxx;
            break;

            case '3':
              if( ind_requer == '0' && val_itemxx == '' )
                obsID += obsID != '' ? ", " + des_textox + " N/A"  : des_textox + " " + "N/A";
              else
                obsID += obsID != '' ? ", " + des_textox + " " + val_itemxx : des_textox + " " + val_itemxx;
              
              obsID += " " + $("#rango"+$(this).val()+"ID").text();
            break;

            case '4':
              $("input[type=radio][name=val_itemxx"+$(this).val()+"]").each(function( indice, elemento ){
                  if( $(elemento).is(':checked') )
                  {
                    if($(elemento).val() == 'S')
                      prefijo = "SI ";
                    else
                      prefijo = "NO ";
                  }
              });

              if( ind_requer == '0' && prefijo == '' )
                obsID += obsID != '' ? ", SE DESCONOCE SI " + des_textox  : " SE DESCONOCE SI " + des_textox;
              else
                obsID += obsID != '' ? ", " + prefijo + des_textox  : + prefijo + des_textox;
            break;
          }
        }
      });
    }

    var radio = $("input[name='ind_activo']:checked").length;
    
    if( msj_alerta != '' )
    {
      alert("Por Favor Verifique los siguientes campos en el Formulario:\n" + msj_alerta );
      return false;
    }
    else if( radio == 0 )
    {
    alert("Seleccione una Opci\xf3n");
    return false;
    }
    else
    {
      $("#newPopupID").dialog("close");
      $("#obsID").val( "EL CONDUCTOR INFORMA QUE " + obsID.toUpperCase() );
      var limit = 2000;
      nueva_longitud = limit - $("#obsID").val().length;
      if( nueva_longitud < 0 )
      {
         text = $("#obsID").val();
         $("#obsID").val( text.substr( 0,limit ) );
         $("#obsID").parent().find("#counter").html("Queda(n)<b> 0</b> Caracter(es) para Escribir");
      }
      else
      {
         $("#obsID").parent().find("#counter").html("Queda(n)<b> "+nueva_longitud+"</b> Caracter(es) para Escribir");
      }
      
    }

  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function CamRuta(ruta)
{
  try
  {
    var url_archiv = document.getElementById( 'url_archivID' );
    var dir_aplica = document.getElementById( 'dir_aplicaID' );
    var despac = document.getElementById( 'despacID' ).value;
    
    var atributes  = "opcion=4";
	  atributes += "&despac=" + despac + "&rutasx=" + ruta;
    try{
      var controbase = document.getElementById( 'controbaseID' ).value;
      var rutalsel = document.getElementById( 'rutaselID' ).value;
      var tmplle = document.getElementById( 'tmplleID' ).value;
      atributes += "&controbase=" + controbase + "&rutasel=" + rutalsel + "&tmplle=" +tmplle;
    }
    catch (e)
    {
    }
    try{
      var tmplle = document.getElementById( 'tmplleID' ).value;
      atributes += "&tmplle=" +tmplle;
    }
    catch (e)
    {
    }
    try{
      
      var fechaprog = document.getElementById( 'fechaprogID' ).value;
      atributes += "&fechaprog=" + fechaprog;
    }
    catch (e)
    {
    }
    AjaxGetData( "../"+dir_aplica.value+"/despac/"+url_archiv.value+"?", atributes, 'popupDIV', "post" );
  }
  catch (e)
  {
      alert("Error -> CamRuta() " + e.message);
  }
}

function guarllegad()
{
  try {
    validacion = true;
    formulario = document.form_ins;
    var obs = document.getElementById('obs_llegadID');
    if (obs.value != '') {
      if (confirm('Esta Seguro que Desea  Dar llegada al Despacho?')) {
        formulario.opcion.value = 7;
        formulario.submit();
      }
    }else
      return alert('La Observacion es Requerida');    
  }
  catch(e)
  {
    alert("Error funcion guarllegad " + e.message + '\n' + e.stack);
  }
}

function CamInsertar()
{
  try
  {
    var url_archiv = document.getElementById( 'url_archivID' );
    var dir_aplica = document.getElementById( 'dir_aplicaID' );
    var despac = document.getElementById( 'despacID' ).value;
    var rutalsel = document.getElementById( 'rutaselID' ).value;
    var totapc = document.getElementById( 'totapcID' ).value;
    var atributes  = "opcion=5";
	  atributes += "&despac=" + despac + "&rutasx=" + rutalsel + "&totapc=" + totapc;
    var controbase = document.getElementById( 'controbaseID' ).value;
    var tmplle = document.getElementById( 'tmplleID' ).value;
    atributes += "&controbase=" + controbase + "&rutasel=" + rutalsel + "&tmplle=" +tmplle;
    for(i=0;i<=totapc-1;i++) {
      pcontro = document.getElementById( 'pcontroID'+escape(i) ).value;
  		pcnove = document.getElementById( 'pcnoveID'+escape(i) ).value;
      pctime = document.getElementById( 'pctimeID'+escape(i) ).value;
    	atributes += '&pcontro'+i+'=' + escape( pcontro );
    	atributes += '&pcnove'+i+'=' + escape( pcnove );
    	atributes += '&pctime'+i+'=' + escape( pctime );
    } 
    var tmplle = document.getElementById( 'tmplleID' ).value;
    atributes += "&tmplle=" +tmplle;
    var fechaprog = document.getElementById( 'fechaprogID' ).value;
    atributes += "&fechaprog=" + fechaprog;
    ClosePopup();
    AjaxGetData( "../"+dir_aplica.value+"/despac/"+url_archiv.value+"?", atributes, 'popupDIV', "post","Guarda();" );
  }
  catch (e)
  {
      alert("Error -> CamInsertar() " + e.message);
  }
}

function Guarda()
{
  formulario = document.form_ins;
  document.getElementById('opcionID').value = 3;
  document.getElementById('form_insID').submit();
}

function aceptar_act()
{
    validacion = true
    formulario = document.form_act
    if(formulario.noveda.value == "")
    {
     window.alert("Digite el Nombre de la Novedad")
     formulario.noveda.focus()
     validacion = false
    }
    else
    {
    formulario.opcion.value= 1;
    formulario.submit();
    }
}


function ins_tab_noveda(formulario)
{
    validacion = true
    formulario = document.form_insert
    if(formulario.nom.value == "")
    {
     window.alert("El Nombre es Requerido")
     validacion = false
     formulario.nom.focus()
    }
    else
    {
    formulario.opcion.value= 2;
    formulario.submit();
    }
}

function aceptar_lis(){
    validacion = true
    formulario = document.form_list
    if(formulario.noveda.value == "")
    {
     window.alert("Digite el Nombre de la Novedad")
     formulario.noveda.focus()
     validacion = false
    }
    else
    {
    formulario.opcion.value= 2;
    formulario.submit();
    }
}

function aceptar_eli(){
    validacion = true
    formulario = document.form_eli
    if(formulario.noveda.value == "")
    {
     window.alert("Digite el Nombre de la Novedad")
     formulario.noveda.focus()
     validacion = false
    }
    else
    {
    formulario.opcion.value= 1;
    formulario.submit();
    }
}

function aceptar_inscarava(){
    validacion = true;
    formulario = document.form_ins;

    if(formulario.novedad.value == 0)
    {
     window.alert("La Novedad es Requerida")
     validacion = false
     formulario.novedad.focus()
    }
    else if(document.getElementById('duracion') != null)
    {
     if(formulario.tiem_duraci.value == "")
     {
        window.alert('Digite El tiempo de Duracion en Minutos de La novedad')
        formulario.tiem_duraci.focus()
     }
     else
     {   if(confirm('Esta Seguro que Desea Insertar La novedad?'))
            {
                formulario.opcion.value= 3;
                formulario.submit();
            }
     }
    }
    else if(confirm("Esta Seguro de Ingresar La novedad?"))
    {
    formulario.opcion.value= 3;
    formulario.submit();
    }
}

/*! \fn: setSoluciRecome
 *  \brief: Solucion de recomendaciones
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return: 
 */
function setSoluciRecome()
{
  try
  {
    var formulario = $("#formSoluciRecomeID");
    var codsRecome = $("#cod_recomeID").val();
    var param = "";
    var num_condes = "";
    var obs_ejecuc = "";
    var cod_tipoxx = "";
    var ind_requer = "";
    var valida = true;

    codsRecome = codsRecome.split('|');

    for (var i = 0; i < codsRecome.length; i++) 
    //formulario.find("span").each(function(i,obj)
    {

      num_condes = $("#InputsForm"+i+"ID").attr("num_condes");
      cod_tipoxx = $("#InputsForm"+i+"ID").attr("cod_tipoxx");
      ind_requer = $("#InputsForm"+i+"ID").attr("ind_requer");

      if(cod_tipoxx == 4){
        objeto = $('input:radio[name=val_itemxx'+( codsRecome[i] )+']:checked');
        obs_ejecuc = objeto.val();
        
        if(obs_ejecuc == 'S')
          obs_ejecuc = 'SI';
        else if(obs_ejecuc == 'N')
          obs_ejecuc = 'NO';
        else
          obs_ejecuc = '';
        
      }else{
        objeto = $("#val_itemxx"+( codsRecome[i] )+"ID");
        obs_ejecuc = objeto.val();
      }

      if(ind_requer == 1 && obs_ejecuc == '' )
      {
        alert("El Campo "+(i+1)+" es Obligatorio. \nPor Favor Validar Informacion.");
        objeto.focus();
        valida = false;
        return false;
      }

      // Agrega datos del parametros URL
      param += "&data["+i+"][num_condes]=" + num_condes + "&data["+i+"][obs_ejecuc]=" + obs_ejecuc ; 
    };
    
    
    if(valida == true)
    {
      var fStandar   = $("#dir_aplicaID").val();
      var atributes  = 'Ajax=on&Option=UpdSoluciRecome&standa='+fStandar;
          atributes += '&num_despac=' + $("#num_despacID").val();
          atributes += param;
      
      $.ajax({
        url: "../"+ fStandar + "/despac/ajax_despac_recome.php",
        type: "POST", 
        data: atributes,
        async: false,
        success: function(data){
          var txt = data.replace(/\|\|/g, "\n");
          $("#obsID").val(txt);
          $("#ind_solRecID").val("1");
        },
        complete: function(){
          LoadPopupJQ2('close');
        }
      });
    }
    
    
  }
  catch(e)
  {
    console.log( "Error Fuction setSoluciRecome: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}


function valSit()
{
  var sit = document.getElementById('sitID');
  var pc  = document.getElementById('pcID');
  var sitio  = document.getElementById('sitioID');
  var indShowSoluci = document.getElementById('indShowSoluciID').value;

  if (sit.value=="S"){
    sitio.value = pc.value;
    sitio.readOnly=true;
    if(indShowSoluci == 1){
      showSoluciRecome();
    }
  }else{
    sitio.readOnly=false;
  }

}

function CamDefini(element)
{
  try
  {
	  if(element.checked == true)
	  	ind_defini=1;
	  else
	  	ind_defini=0;
		var url_archiv = document.getElementById( 'url_archivID' );
    var dir_aplica = document.getElementById( 'dir_aplicaID' );
		var num_despac = document.getElementById( 'num_despacID' );
    var atributes  = "opcion=6";
	  atributes += "&ind_defini=" + ind_defini + "&num_despac=" +num_despac.value;
    AjaxGetData( "../"+dir_aplica.value+"/despac/"+url_archiv.value+"?", atributes, 'popupDIV', "post" );
  }
  catch (e)
  {
    alert( "Error CamDefini: " + e.message );
  }
}

/*! \fn: setObserRecome
 *  \brief: Se envian las recomendaciones
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return: codContro PcPadre
 */
function setObserRecome()
{
  var codConsec = new Array();
  var arrayText = new Array();
  var arrayRecome = new Array();

  var i=0;
  $("input[type=checkbox]:checked").each(function(){
    arrayText[i] = $(this).val();
    codConsec[i] = $(this).val().split('=> ');
    i++;
  });
  
  for (var i = 0; i < codConsec.length; i++) {
    arrayRecome[i] = codConsec[i][0];
  };

  var numRecome = arrayRecome.join("|");
  var textNoved = arrayText.join("\n*");

  if( textNoved == ""){
    alert("Por Favor Seleccione Por lo Menos una Recomendacion ");
  }else{
    $("#obsID").val("Se deja recomendado en la EAL.\n*"+textNoved);
    $("#num_recomeID").val(numRecome);
    LoadPopupJQ2('close');
  }
}

/*! \fn: showDespacRecome
 *  \brief: PopUp con las recomendaciones para asignar al despacho
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: NumDespac
 *  \return:
 */
function showDespacRecome(num_despac)
{
  try
  {
    var fStandar   = $("#dir_aplicaID");
    var atributes  = 'Ajax=on&Option=FormDespacRecome&standa='+fStandar;
        atributes += '&num_despac=' + num_despac;

    //Carga PopUp
    LoadPopupJQ2('open');
      $.ajax({
        url: "../"+ fStandar.val() + "/despac/ajax_despac_recome.php",
        type: "POST", 
        data: atributes,
        async: false,
        beforeSend: function(){
          $("#FormContacID").html("<center>Cargando Formulario...</center>");
        },
        success: function(data){
          $("#FormContacID").html(data);
        },
        complete: function(){
          CenterDIV1();
        }
      });
  }
  catch(e)
  {
    console.log( "Error Función showDespacRecome: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}

/*! \fn: showRutasTransp
 *  \brief: Muestra las rutas opcionales de la transportadora
 *  \author: Ing. Fabian Salinas
 *  \date: 27/05/2015
 *  \date modified: dia/mes/año
 *  \param: Num_Despac, CodTransportadora, CodRuta, CodPuestoControl
 *  \return:
 */
function showRutasTransp(num_despac, cod_transp, cod_rutasx, cod_contro )
{
  try
  {
    var fStandar  = $("#dir_aplicaID").val();
    var atributes = 'Ajax=on&Option=FormNewRuta&standa='+fStandar;
        atributes += '&num_despac=' + num_despac;
        atributes += '&cod_transp=' + cod_transp;
        atributes += '&cod_rutasx=' + cod_rutasx;
        atributes += '&cod_contro=' + cod_contro;

    //Carga PopUp
    LoadPopupJQ2('open');
    $.ajax({
      url: "../"+ fStandar + "/despac/ajax_despac_despac.php",
      type: "POST", 
      data: atributes,
      async: false,
      beforeSend: function(){
        $("#FormContacID").html("<center>Cargando Formulario...</center>");
      },
      success: function(data){
        $("#FormContacID").html(data);
      },
      complete: function(){
        CenterDIV1();
      }
    });
  }
  catch(e)
  {
    console.log( "Error Fuction showRutasTransp: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}

/*! \fn: showSoluciRecome
 *  \brief: PopUp con las recomendaciones asignadas al despacho, para dar solucion
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function showSoluciRecome()
{
  try
  {
    var fStandar   = $("#dir_aplicaID");
    var atributes  = 'Ajax=on&Option=SoluciRecome&standa='+fStandar.val();
        atributes += '&num_despac='+ $("#num_despacID").val();
        atributes += '&cod_contro='+ $("#cod_controID").val();
    
    //Load PopUp
    LoadPopupJQ2('open');
    $.ajax({
      url: "../"+ fStandar.val() + "/despac/ajax_despac_recome.php",
      type: "POST", 
      data: atributes,
      async: false,
      beforeSend: function(){
        $("#FormContacID").html("<center>Cargando Formulario...</center>");
      },
      success: function(data){
        $("#FormContacID").html(data);
      },
      complete: function(){
        CenterDIV1();
      }
    });
  }
  catch(e)
  {
    console.log( "Error Fuction showSoluciRecome: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}

/*! \fn: showDetallePc
 *  \brief: PopUp con el detalle del puesto de control (Si el PC es hijo muestra el detalle del padre)
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: CodContro
 *  \return:
 */
function showDetallePc(cod_contro)
{
  try{
    var fStandar = $("#dir_aplicaID");
    var atributes  = 'Ajax=on&Option=DetallePcPadre';
        atributes += '&cod_contro=' + cod_contro;

    //Carga PopUp
    LoadPopupJQ2('open');
      $.ajax({
        url: "../"+ fStandar.val() + "/despac/ajax_despac_despac.php",
        type: "POST", 
        data: atributes,
        beforeSend: function(){
          $("#FormContacID").html("<center>Cargando Formulario...</center>");
        },
        success: function(data){
          $("#FormContacID").html(data);
          CenterDIV1();
        }
      });
  }
  catch(e)
  {
    console.log( "Error Función showDetallePc: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}

/*! \fn: LoadPopupJQ2
 *  \brief: Crea PopUp
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function LoadPopupJQ2( type )
{
  try
  {
    if(type == 'open')
    {
      $('<div id="FormContacID"><center>Cargando...</center></div>').dialog({
        width : 'auto',
        heigth: 'auto',
        modal: true,
        closeOnEscape: false,
        resizable: false,
        draggable: false,
        open: function (event, ui){
          //$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
        },
        close: function(){
          $("#FormContacID").dialog("destroy").remove();
        }
      });
    }
    else
    {
      $("#FormContacID").dialog("destroy").remove();
    }

  }
  catch(e)
  {
    alert("Error function LoadPopupJQ2 "+e.message+"\nLine: "+e.lineNumber);
  }
}

/*! \fn: CenterDiv1
 *  \brief: Centra el PopUp
 *  \author: Ing. Fabian Salinas
 *  \date: 25/05/2015
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function CenterDIV1()
{
  try
  {
    $("#FormContacID").parent().css({heigth:"auto", width:"auto","max-heigth":"auto",  left: ($(window).width() - $("#FormContacID").parent().outerWidth())/2,  right: ($(window).width() - $("#FormContacID").parent().outerWidth())/2});
  }
  catch(e)
  {
      alert(e.message);
  }
}


/*------------------------------FUNCIÓN PARA ACTIVAR EL MAPA CON SEGUIMIENTO GPS EN OPENSTREETMAPS------------------------------------------*/
function showMapOpen()
{     
  var size_notgps = document.getElementById( "size_notgpsID" ).value;
  var mostrar_mapa = document.getElementById( "mos_mapa" ).value;
  //alert(mostrar_mapa);
  if(mostrar_mapa ==1)
  {
    //Se crea el mapa a partir de un DIV en el formulario
    $("#map").geomap({ 
      center: [-64.3963597921893, 4.6036797038499815],
      zoom: 5,
      pannable: 'checked',
      scroll: 'zoom',
      shape: function (e, geo) 
      {
        map.geomap('append', geo);
      }
    });
    var detalles = document.getElementById( "detalles" ).value;
    //alert(detalles);
    if(detalles == 1)
    {
      for( i=0; i < size_notgps; i++ )
      {
        var val_latitu = document.getElementById( "val_latitu" + i + "ID" ).value;
        var val_longit = document.getElementById( "val_longit" + i + "ID" ).value;
        var obs_contro = document.getElementById( "obs_contro" + i + "ID" ).value;
        var observacion = document.getElementById( "obs_contro" + i + "ID" ).value;
        observacion = observacion.split("Velocidad");
        $("#map").geomap( "append", 
        { 
          type: "Point", 
          coordinates: [ val_longit, val_latitu] 
        }, 
        { 
          color: "#00f" 
          }, 
          observacion[0] )
      }
    }
    else
    {
      for( i=0; i < size_notgps; i++ )
      {
        var val_latitu = document.getElementById( "val_latitu" + i + "ID" ).value;
        var val_longit = document.getElementById( "val_longit" + i + "ID" ).value;
        var obs_contro = document.getElementById( "obs_contro" + i + "ID" ).value;
        var observacion = document.getElementById( "obs_contro" + i + "ID" ).value;
        observacion = observacion.split("Velocidad");
        $("#map").geomap( "append", { type: "Point", coordinates: [ val_longit, val_latitu ] } );
      }
    }
  }
}  
/*---------------------------------------------------------------------------------------------------------------------------------------------------*/

// Funcion para validación alfanumérica (Solo Números y Letras AZ - az: 09)
function TextInputAlpha( fEvent ){
  var fKeyPressed = ( fEvent.which ) ? fEvent.which : fEvent.keyCode;
  return !( ( fKeyPressed < 65 || fKeyPressed > 90 )  && ( fKeyPressed < 97 || fKeyPressed > 122 ) && 
            ( fKeyPressed < 48 || fKeyPressed > 57 )  && ( fKeyPressed != 8 )  && ( fKeyPressed != 9 ) && ( fKeyPressed != 32 ) );
}


/* FUNCIONES PARA ELIMINAR O EDITAR ---------------------------------------------------------------------------------------------------------------- */
//Despacho, Pcontrol, CodNovedad, FecRegist, CodConsec, origen, usuario, objeto
function ActionNovedad(d,p,n,f,c,t,u,o,a)
{
  try
  {    
    var Action = a == 1 ? 'update' : 'delete';
    LoadPopupJQ('open', o, Action,d,p,n,f,c,t,u);
  }
  catch(e)
  {
    alert("Error en ActionNovedad: "+e.message+"\nLine: "+e.lineNumber);
  }
}

/*! \fn: LoadPopupJQ
 *  \brief: Crea PopUp
 *  \author: 
 *  \date: 
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function LoadPopupJQ(type,object,action,despac,puesto,novedad,date,consec,table,user)
{
  try
  {
 
    var ObjectTr = $( object ).parent().parent().parent();
    var NomNoveda = ObjectTr.find("td").first().find("b").html();

    if(type == 'open')
    {
      $('<div id="DialogNovedaID"><center>CARGANDO ...</center></div>').dialog({
          modal :true,
          resizable: false,
         
          closeOnEscape: false,
          width: "auto",
          heigth: "auto",
          title:  (action == 'update' ? "Actualizar Novedad: " : "Eliminar Novedad: "  )+NomNoveda, 
          open: function(){
            $(".ui-dialog-titlebar-close").remove();
          },
          buttons: {
            
            "Cancelar": function(){
              LoadPopupJQ("close");

            },
            "Aceptar": function(){

               //Carga Ajax segun la accion
              if(action == 'delete') {
                if(confirm("Desea Eliminar El Registro De La Novedad?")) {
                  DeleteNoveda(ObjectTr, despac,consec,puesto,novedad,date,table,user);
                }
              }

              if(action == 'update') {
                var NewCodNoveda = $("#cod_novedaNID").val();
                var NewNomNoveda = $("#novedadListID").val();
                var Msg  = "Recuerde que selecciono una novedad diferente a la Actual:\n";
                    Msg += "Novedad Actual: "+$("#actaulNovedadID").html()+"  \n";
                    Msg += "Nueva Novedad:  "+$("#novedadListID").val()+" \n";
                var mExtra = NewCodNoveda != '--' ? Msg : "";
                if(confirm("Desea Actualizar El Registro De La Novedad?\n"+mExtra)) {
                  UpdateNoveda(ObjectTr, despac,consec,puesto,novedad,date,table,user);
                }                
              }
            }
          }
      });
    }
    else
    {
      $("#DialogNovedaID").dialog("destroy").remove();
      return false;
    }

    // Ejecuta ajax para la accion
    var central = $("#dir_aplicaID").val();
    var param = 'Option='+(action == 'update' ? 'FormNovedaUpdate' : 'FormDeleteNoveda' );
        param +='&despac='+despac;
        param +='&puesto='+puesto;
        param +='&novedad='+novedad;
        param +='&date='+date;
        param +='&consec='+consec;
        param +='&table='+table;
        param +='&user='+user;

    $.ajax({
      type: "POST",
      url: '../'+central+'/despac/ajax_despac_despac.php',
      data: param,
      beforeSend: function()
      {
        $("#DialogNovedaID").html("<center>CARGANDO FORMULARIO PARA ACTUALIZAR NOVEDAD<br>POR FAVOR ESPERE</center>");
          $(".ui-dialog-buttonpane").hide();
      },
      success: function(data){
        $("#DialogNovedaID").html(data);
        $(".ui-dialog-buttonpane").show();
      },
      complete: function(){
        $(".ui-dialog").css({ left: ($(window).width() - $(".ui-dialog").outerWidth()) / 2 }).animate({top: "-=50"}, 1000);     
      }
    });


    
  }
  catch(e)
  {
    alert("Error en LoadPopupJQ: "+e.message+"\nLine: "+e.lineNumber);
  }
}

function DeleteNoveda(o,d,c,p,n,f,t,u)
{
  try
  {
      $("#DialogNovedaID").html("<center>Eliminando...</center>");
      $(".ui-dialog").css({ left: ($(window).width() - $(".ui-dialog").outerWidth()) / 2 });
      
      var central = $("#dir_aplicaID").val();
      var param = 'Option=DeleteNoveda';
          param +='&despac='+d;
          param +='&puesto='+p;
          param +='&novedad='+n;
          param +='&date='+f;
          param +='&consec='+c;
          param +='&table='+t;
          param +='&user='+u;


      $.ajax({
        type: "POST",
        url: '../'+central+'/despac/ajax_despac_despac.php',
        data: param,
        beforeSend: function()
        {
          $("#DialogNovedaID").html("<center>CARGANDO FORMULARIO PARA ELIMINAR LA NOVEDAD<br>POR FAVOR ESPERE</center>");
          $(".ui-dialog-buttonpane").remove();
        },
        success: function(data){
          $("#DialogNovedaID").html(data);
          $(".ui-dialog").css({ left: ($(window).width() - $(".ui-dialog").outerWidth()) / 2 });
          $(o).remove();
        },
        complete: function(){
          setTimeout(function(){ $(".ui-dialog").animate({opacity: "-=1",top: "-=400"}, 1500, function(){ LoadPopupJQ("close") } );      }, 2000);
          
        }
      });
     
  }
  catch(e)
  {
    alert("Error en DeleteNoveda: "+e.message+"\nLine: "+e.lineNumber);
  }
}

function setNovedadNueva(n)
{
    try
    {
      var novedad = n.item.label;
      if(novedad == 'Ninguna') {
        $("#cod_novedaNID").val("--");
        return false;
      }

      var codNoveda = novedad.split("-");
      alert(codNoveda[0]);
      $("#cod_novedaNID").val(codNoveda[0]);

    }
    catch(e)
    {
      alert("Error en setNovedadNueva: "+e.message+"\nLine: "+e.lineNumber);
    }
}

function UpdateNoveda(o,d,c,p,n,f,t,u)
{
  try
  {
      var OldCodNoveda = $("#cod_novedaID").val();
      var NewCodNoveda = $("#cod_novedaNID").val();
      var NewNomNoveda = $("#novedadListID").val();
      var desc = $("#descripcionID").val();
      var central = $("#dir_aplicaID").val();
      var param = 'Option=UpdateNoveda';
          param +='&despac='+d;
          param +='&puesto='+p;
          param +='&novedad='+n;
          param +='&date='+f;
          param +='&consec='+c;
          param +='&table='+t;
          param +='&user='+u;
          param +='&desc='+desc;
          param +='&nCod='+NewCodNoveda;
          param +='&oCod='+OldCodNoveda;


      $.ajax({
        type: "POST",
        url: '../'+central+'/despac/ajax_despac_despac.php',
        data: param,
        beforeSend: function()
        {
          $("#DialogNovedaID").html("<center>CARGANDO FORMULARIO PARA ELIMINAR LA NOVEDAD<br>POR FAVOR ESPERE</center>");
          //$(".ui-dialog-buttonpane").remove();
        },
        success: function(data){
          $("#DialogNovedaID").html(data);
          $(".ui-dialog").css({ left: ($(window).width() - $(".ui-dialog").outerWidth()) / 2 });
          //$(o).remove();
        },
        complete: function(){
          //setTimeout(function(){ $(".ui-dialog").animate({opacity: "-=1",top: "-=400"}, 1500, function(){ LoadPopupJQ("close") } );      }, 2000);
          
        }
      });
     
  }
  catch(e)
  {
    alert("Error en UpdateNoveda: "+e.message+"\nLine: "+e.lineNumber);
  }
}

function updRuta()
{
  try
  {
    var cod_rutnew = $("#FormContacID").find("input[type=radio]:checked");
    var fStandar = $("#dir_aplicaID");
    var atributes  = 'Ajax=on&Option=CambioRuta';
        atributes += '&cod_rutnew=' + cod_rutnew.val();
        atributes += '&num_despac=' + $("#num_despacID").val();
        atributes += '&cod_contro=' + $("#cod_controID").val();
        atributes += '&cod_rutold=' + $("#cod_rutoldID").val();

    if(cod_rutnew.val() == null)
    {
      alert("Por Favor Seleccione una Ruta");
      return false;
    }
    else
    {
      if(confirm("Esta seguro que desa realizar el cambio de ruta."))
      {
        $.ajax({
          url: "../"+ fStandar.val() + "/despac/ajax_despac_despac.php",
          type: "POST", 
          data: atributes,
          async: false,
          complete: function(){
            LoadPopupJQ2("close");
          }
        });
      }else{
        LoadPopupJQ2("close");
      }
    }
  }
  catch(e)
  {
    console.log( "Error Fuction updRuta: "+e.message+"\nLine: "+Error.lineNumber );
    return false;
  }
}
