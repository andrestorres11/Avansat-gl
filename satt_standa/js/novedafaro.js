
function aceptar_ins(formulario)
{
  try {
    validacion = true;
    formulario = document.form_ins;
    var sitio = document.getElementById('sitioID');
    var sit = document.getElementById('sitID');
    var nov_especi = document.getElementById('nov_especiID');
    var obs = document.getElementById('obsID');
    var noveda = document.getElementById('novedadID');
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
             else if (confirm('Esta Seguro que Desea Insertar La novedad?')) {
               formulario.opcion.value = 3;
               formulario.submit();
             }  
           }else{
             try
              {
                var url_archiv = document.getElementById( 'url_archivID' );
                var dir_aplica = document.getElementById( 'dir_aplicaID' );
                var despac = document.getElementById( 'despacID' ).value;
                LoadPopup();
                var atributes  = "opcion=4";
          		  atributes += "&despac=" + despac;
                AjaxGetData( "../"+dir_aplica.value+"/despac/"+url_archiv.value+"?", atributes, 'popupDIV', "post" );
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


function aceptar_act(){
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


function valSit(){
  var sit = document.getElementById('sitID');
  var pc  = document.getElementById('pcID');
  var sitio  = document.getElementById('sitioID');
  if (sit.value=="S"){
    sitio.value = pc.value;
    sitio.readOnly=true;
  }else{
    sitio.readOnly=false;
  }

}


