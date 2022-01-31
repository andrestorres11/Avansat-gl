/**
 * @author jose.guerrero
 */
function aceptar_ins(){
	tipo = document.getElementById("tip_tarifaID");
	if(tipo.value==''){
		return alert("EL Tipo de Tarifa es Obligatorio");
	}else{
		if(tipo.value=='D'){
			can_minim = document.getElementById("val_tarifaID");
			if(can_minim.value=='' || can_minim.value=='0'){
				return alert('El Valor del Despacho es Requerido');
			}
			if(!valFechas())
				return false;
			if (confirm("Seguro que Desea Ingresar La Tarifa Para Despachos ")) {
	  		document.getElementById('opcionID').value = 2;
	  		document.getElementById('formularioID').submit();
	  	}
		}else{
			if(tipo.value=='N'){
				val_minim = document.getElementById("val_minimID");
				if(Number(val_minim.value)<=0){
					val_minim.focus();
					return alert('Valor Minimo Debe Ser Mayor a 0')
				}
				if(!valFechas())
					return false;
				val = true;
				var value  = new Array();
				var table = $('table#tarifas' + (parseInt($('#maxtarifaID').val())) + 'ID td.celda_info');
				table.each(function(i,e){
						var obj = $(this).find(':input').first();
						if(obj.val()=='' ){
							obj.focus();
							alert(i==0 ? 'La Cantidad Minima es Requerida' : (i==1 ? 'La Cantidad Maxima es Requerida' : 'El Valor es Requerida' ) );
						  return val= false;
						}
						value.push(obj.val());
				});
				if(value[0]>value[1])
					return alert('La Cantidad Minima No Debe Superar la Maxima');
				if(!val)
					return false;
				if (confirm("Seguro que Desea Ingresar La Tarifa Para Despachos, NOTA: El Valor de la Ultima Tarifa Sera Tomada Para un Numero de Novedades que Superen la Cantidad Maxima de la Misma")) {
		  		document.getElementById('opcionID').value = 2;
		  		document.getElementById('formularioID').submit();
	  		}
			}
		}
	}
}

function valFechas(){
	fecini = document.getElementById("feciniID");
	fecfin = document.getElementById("fecfinID");
	if(fecini.value==''){
		return alert('La Fecha Inicial es Requerido');
	}
	if(fecfin.value==''){
		return alert('La Fecha Final es Requerido');
	}
  var feciniArray = fecini.value.split("-");
  var fecini = new Date(feciniArray[0],Number(feciniArray[1])-1,feciniArray[2]);
  var fecfinArray = fecfin.value.split("-");
  var fecfin = new Date(fecfinArray[0],Number(fecfinArray[1])-1,fecfinArray[2]);
	if (fecini > fecfin) {
  	alert('La Fecha inicial de Salida  NO puede ser Mayor a la Fecha Final de Salida');
  	return false;
	}
	return true;
}



function anular(){
	tipo = document.getElementById("tip_tarifaID");
	if (tipo.value == '') {
  	return alert("EL Tipo de Tarifa es Obligatorio");
  }
  else {
  	obs_anulad = document.getElementById('obs_anuladID');
  	if (obs_anulad.value == '') 
  		return alert('La Observacion de Anulado es Obligatoria');
		if (confirm("Seguro que Desea Anular la Tarifa")) {
			document.getElementById('opcionID').value = 2;
  		document.getElementById('formularioID').submit();
		}		
	}
}


function valTipo(){
	if (document.getElementById("tip_tarifaID").value != '' && document.getElementById('cod_tercerID').value!='') 
		document.getElementById('formularioID').submit();
}


function valTarifa(){
	tipo = document.getElementById("tip_tarifaID");
	if(tipo.value==''){
		return alert("EL Tipo de Tarifa es Obligatorio");
	}
	cod_tercer = document.getElementById("cod_tercerID");
	if(cod_tercer.value==''){
		return alert("La Transportadora es Obligatorio");
	}else{
		if(tipo.value=='D'){
			can_minim = document.getElementById("val_tarifaID");
			if(can_minim.value=='' || can_minim.value=='0'){
				return alert('El Valor del Despacho es Requerido');
			}
			if(!valFechas())
				return false;
			if (confirm("Seguro que Desea Ingresar La Tarifa Para Despachos ")) {
	  		document.getElementById('opcionID').value = 2;
	  		document.getElementById('formularioID').submit();
	  	}
		}else{
			if(tipo.value=='N'){
				val_minim = document.getElementById("val_minimID");
				if(Number(val_minim.value)<=0){
					val_minim.focus();
					return alert('Valor Minimo Debe Ser Mayor a 0')
				}
				if(!valFechas())
					return false;
				val = true;
				var value  = new Array();
				var table = $('table#tarifas' + (parseInt($('#maxtarifaID').val())) + 'ID td.celda_info');
				table.each(function(i,e){
						var obj = $(this).find(':input').first();
						if(obj.val()=='' ){
							obj.focus();
							alert(i==0 ? 'La Cantidad Minima es Requerida' : (i==1 ? 'La Cantidad Maxima es Requerida' : 'El Valor es Requerida' ) );
						  return val= false;
						}
						value.push(obj.val());
				});
				if(value[0]>value[1])
					return alert('La Cantidad Minima No Debe Superar la Maxima');
				if(!val)
					return false;
				if (confirm("Seguro que Desea Ingresar La Tarifa Para Despachos, NOTA: El Valor de la Ultima Tarifa Sera Tomada Para un Numero de Novedades que Superen la Cantidad Maxima de la Misma")) {
		  		document.getElementById('opcionID').value = 2;
		  		document.getElementById('formularioID').submit();
	  		}
			}
		}
	}
}




function makeTable(){
	
	var value  = new Array();
	var objxx  = new Array();
	if(parseInt($('#maxtarifaID').val())==0){
		var table  = $('div#tarifasID table[id]').first();
	}else{
		var table = $('div#tarifasID table#tarifas'+(parseInt($('#maxtarifaID').val()))+'ID').first();
	}
	table.find(":input").each(function(i,e){
		value.push($(this).val());
		objxx.push($(this));
  });
	if(value[0]=='' || parseInt(value[0])<0){
		alert('La Cantidad Minima es Requerida');
		objxx[0].val('');
		objxx[0].focus();
		return false;
	}
	if(value[1]=='' || parseInt(value[1])<0){
		alert('La Cantidad Maxima es Requerida');
		objxx[1].val('');
		objxx[1].focus();
		return false;
	}
	if(Number(value[2])<=0 ){
		alert('El Valor Es Requerido');
		objxx[2].val('');
		objxx[2].focus();
		return false;
	}
	
	if(parseInt(value[0])>parseInt(value[1])){
		alert('El Valor 1 es mayor que el Valor 2');
		objxx[0].focus();
		return false;
	}
	
	var tableN   = table.clone(true);
  $('div#tarifasID').append(tableN);
  tableN.attr('id','tarifas'+(parseInt($('#maxtarifaID').val())+1)+'ID');
  $('#tarifas'+(parseInt($('#maxtarifaID').val())+1)+'ID td.celda_info').each(function(i,e){
		var obj = $(this).find(':input').first();
		if(i==0){
			obj.val(parseInt(value[i+1])+1);	
			obj.attr('readonly','readonly');
		}else{
			if(obj.attr('readonly'))
				obj.removeAttr('readonly');
			obj.val('');	
		}
		
  });
  $('#maxtarifaID').val( (parseInt($('#maxtarifaID').val())+1) );
	
}

function deleteTable(){
	
	if (parseInt($('#maxtarifaID').val()) != 0) {
  	$('div#tarifasID table#tarifas' + (parseInt($('#maxtarifaID').val())) + 'ID').first().remove();
		$('#maxtarifaID').val( parseInt($('#maxtarifaID').val())-1 );
		$('table#tarifas' + (parseInt($('#maxtarifaID').val())) + 'ID').first().find(':input').each(function(i,e){
			if(i>0){
				$(this).removeAttr('readonly');
			}else{
				if($('#maxtarifaID').val()==0)
					$(this).removeAttr('readonly');
			}
		});
	}
}


function infoTarifa()
{
  try {
      var fRow = document.getElementById("ActualRowID").value;
		  var consec = document.getElementById("DLLink" + fRow + "-0").innerHTML;
      var url_archiv = document.getElementById('url_archivID');
		  var dir_aplica = document.getElementById('dir_aplicaID');
		  LoadPopup();
      var atributes = "opcion=3&cod_tarifa="+consec;
      AjaxGetData("../" + dir_aplica.value + "/tarifa/" + url_archiv.value + "?", atributes,'result', "post","");
  }
  catch (e)
  {
    alert( "Error infoTarifa " + e.message);
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

