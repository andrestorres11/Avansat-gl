function aceptar_ins(aux)
{ 
    var fecini = document.getElementById('feciniID');
    var horini = document.getElementById('horiniID');
    var fecfin = document.getElementById('fecfinID');
    var horfin = document.getElementById('horfinID');
    if(aux==1){
      var fecini = document.getElementById('feciniID');
      var horini = document.getElementById('horiniID');
      var fecfin = document.getElementById('fecfinID');
      var horfin = document.getElementById('horfinID');
      if(document.getElementById('usuariID').value=='0'){
        return alert('El Usuario es Obligatorio');
      }
      if(fecini.value==''){
        return alert('La Fecha Inicial es Obligatorio');
      }
      if(horini.value==''){
        return alert('La Hora Inicial es Obligatorio');
      }
      if(fecfin.value==''){
        return alert('La Fecha Final es Obligatorio');
      }
      if(horfin.value==''){
        return alert('La Hora Final es Obligatorio');
      }
      fecAdicArray = fecini.value.split("-"); 
      horAdicArray = horini.value.split(":");
      var fecIni = new Date(fecAdicArray[0],Number(fecAdicArray[1])-1,fecAdicArray[2],horAdicArray[0],horAdicArray[1],0);
      fecAdicArray = fecfin.value.split("-"); 
      horAdicArray = horfin.value.split(":");
      var fecFin = new Date(fecAdicArray[0],Number(fecAdicArray[1])-1,fecAdicArray[2],horAdicArray[0],horAdicArray[1],0);
      if(fecIni>=fecFin){
        return alert('La Fecha Inicial no Puede Ser Mayor a la Final');
      }
      document.getElementById('formularioID').submit();
    }
    if(aux==2){
      fecini.value="";
      horini.value="";
      fecfin.value="";
      horfin.value="";
      horfin.value="";
      document.getElementById('usuariID').value='';
      document.getElementById('formularioID').submit();
    }

    if(aux==3)
    {
      try 
    	{
        var formulario = document.getElementById('formularioID');
    		var transp = Number(document.getElementById('transpID').value);
    		paso = false;
    		var x=0;
        for (var i = 0; i < transp; i++) 
    		{
    			
    			if ( document.getElementById('tercerID'+ i).checked ) 
    			{
            x += parseInt( document.getElementById('despacID'+ i).innerHTML);            
    				paso = true;
    			}
    		}
        
        if(paso==false)
          return alert("Debe Seleccionar Almenos una Empresa");
        
    		
    		if (confirm("Numero de Despachos Seleccionado "+x+". Esta Seguro de Registrar el Horario de Monitoreo?")) 
    		{
    			document.getElementById('opcionID').value=2;
          formulario.submit();
    		}
    		
    	} 
    	catch (e) 
    	{
    		alert("Error " + e.message);
    	}
    }
}


function aceptar_cam()
{ 
  var usuari = document.getElementById('usuariID').value;
  if(usuari.value =='' || usuari.value =='0')
    return alert('El Usuario es Obligatorio');
	if (confirm("Esta Seguro de Cambiar la Bandeja de Entrada del Usuario?")) 
	{
		document.getElementById('opcionID').value=2;
    document.getElementById('formularioID').submit();
	}
}





function listar()
{ 
  document.getElementById('opcionID').value=2;
  document.getElementById('formularioID').submit();
	
    		
}


function actualizar()
{ 
    var anulad = document.getElementById('anuladID');
    var x = document.getElementById('numID').value;
    if(anulad.checked){
      var obs = document.getElementById('obs_anuladID');
      if(obs.value=='')
        return alert("La Observacion de Anulado del Horario es obligatoria");
    }
    var numanul = Number(document.getElementById('numanulID').value);
    for (var i = 0; i <= numanul-1; i++) 
		{
			
			if (document.getElementById('anuladID'+ i).checked) 
			{
        if( document.getElementById('obs_anuladID'+ i).value=='')
				  return alert('La Observacion de Anulado de la Empresa '+ document.getElementById('anuladID'+ i).value +' Es Obligatoria');
			}
		}
		if (confirm("Numero de Despachos Seleccionado "+x+". Esta Seguro de Registrar el Horario de Monitoreo?")) 
		{
			document.getElementById('opcionID').value=4;
      document.getElementById('formularioID').submit();
		}
}



function listar()
{ 
  document.getElementById('opcionID').value=2;
  document.getElementById('formularioID').submit();
	
    		
}




function infoHorari()
{
  try {
     var fRow = document.getElementById("ActualRowID").value;
		  var consec = document.getElementById("DLLink" + fRow + "-0").innerHTML;
      var url_archiv = document.getElementById('url_archivID');
		  var dir_aplica = document.getElementById('dir_aplicaID');
      LoadPopup();
      var atributes = "opcion=3&cod_consec="+consec;
      AjaxGetData("../" + dir_aplica.value + "/config/" + url_archiv.value + "?", atributes,'result', "post","");
  }
  catch (e)
  {
    alert( "Error infoHorari " + e.message);
  }
}



function LoadPopup()
{
	try 
	{
		var objEnd = document.getElementById("AplicationEndDIV");
		//LockAplication("lock");
		var objPopup = document.getElementById("popupDIV");
		var width = screen.height;
		var height = screen.width;
		var width = Math.round(screen.width / 1.5);
		var height = Math.round(screen.height / 1.5);
		var left = Math.round(screen.width / 10);
		var top = Math.round(screen.height / 15);
		
		objPopup.style.width = String(width) + "px";
		objPopup.style.height = String(height) + "px";
		objPopup.style.left = String(left) + "px";
		objPopup.style.top = String(top) + "px";
		objPopup.style.visibility = "visible";
		objPopup.scrollIntoView(true);
	} 
	catch (e) 
	{
		alert("Error " + e.message);
	}
}

function ClosePopup()
{
	//LockAplication( "unlock" );
	var objPopup = document.getElementById("popupDIV");
	objPopup.style.width = "0px";
	objPopup.style.height = "0px";
	objPopup.style.left = "0px";
	objPopup.style.top = "0px";
	//objPopup.innerHTML = "";
	objPopup.style.visibility = "hidden";
}


function sum(i){
  x = parseInt( document.getElementById('despacID'+ i).innerHTML);
  if (x != 0) {
    if (document.getElementById('tercerID' + i).checked) {
      document.getElementById("numID").value = parseInt(document.getElementById("numID").value) + x;
    }
    else 
      document.getElementById("numID").value = parseInt(parseInt(document.getElementById("numID").value)- x);
  }
}


function ActHorari(){
    var fRow = document.getElementById("ActualRowID").value;
		var consec = document.getElementById("DLLink" + fRow + "-0").innerHTML;
    document.getElementById('opcionID').value=3;
    document.getElementById('cod_consecID').value=consec;
    return document.getElementById('formularioID').submit();
  
  try 
    	{
        var formulario = document.getElementById('formularioID');
    		var transp = Number(document.getElementById('transpID').value);
    		paso = false;
    		var x=0;
        for (var i = 0; i < transp; i++) 
    		{
    			
    			if (document.getElementById('tercerID'+ i) .checked) 
    			{
            x += parseInt( document.getElementById('despacID'+ i).innerHTML);            
    				paso = true;
    			}
    		}
        
        if(paso==false)
          return alert("Debe Seleccionar Almenos una Empresa");
        
    		
    		if (confirm("Numero de Despachos Seleccionado "+x+". Esta Seguro de Registrar el Horario de Monitoreo?")) 
    		{
          
    			document.getElementById('opcionID').value=3;
          formulario.submit();
    		}
    		
    	} 
    	catch (e) 
    	{
    		alert("Error " + e.message);
    	}
  
  
}
