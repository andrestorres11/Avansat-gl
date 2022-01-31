function Seleccionar( i )
{
	try 
    {
		var codigo = document.getElementById( "cell" + i + "_0" );
		var nombre = document.getElementById( "cell" + i + "_1" );
		var celular = document.getElementById( "cell" + i + "_2" );
		
		var cod_conduc = document.getElementById( "cod_conduc" );
		var nom_conduc = document.getElementById( "nom_conduc" );
		var cel_conduc = document.getElementById( "cel_conduc" );
		
		cod_conduc.value = codigo.innerHTML;
		nom_conduc.value = nombre.innerHTML;
		cel_conduc.value = celular.innerHTML;
		
		ClosePopup() ;
		
    } 
    catch (e) 
    {
        alert("Error -> listar_auto() " + e.message);
    }
}

function BuscarConductor( transp )
{
	try 
    {
		var fil_cedula = document.getElementById( "fil_cedula" );
		var fil_nombre = document.getElementById( "fil_nombre" );
		
		//var url_archiv = document.getElementById( "url_archiv" );
		var url_archiv = "inf_despac_conduc.php";
		var aplica_central = document.getElementById( "aplica_central" );
		
		LoadPopup();
        var atributes  = "opcion=buscar";
		if( fil_cedula )
			atributes += "&fil_cedula=" + fil_cedula.value;
		if( fil_nombre )
			atributes += "&fil_nombre=" + fil_nombre.value;
		
		atributes += "&cod_transp=" + transp;
				
        //AjaxGetData( "../" + aplica_central.value + "/despac/"+ url_archiv.value  +"?", atributes, 'result', "post" );		
        AjaxGetData( "../" + aplica_central.value + "/despac/"+ url_archiv  +"?", atributes, 'result', "post" );		
		
    } 
    catch (e) 
    {
        alert("Error -> BuscarConductor() " + e.message);
    }
}

function CargarCondutores( transp )
{
	try 
    {
		LoadPopup();
        var atributes = "opcion=filtros";
			atributes += "&cod_transp=" + transp;

		
		var url_archiv = "inf_despac_conduc.php";
		//var url_archiv = document.getElementById( "url_archiv" );
		var aplica_central = document.getElementById( "aplica_central" );

        //AjaxGetData( "../"+aplica_central.value+"/despac/"+ url_archiv.value +"?", atributes, 'filtros', "post" );
        AjaxGetData( "../"+aplica_central.value+"/despac/"+ url_archiv +"?", atributes, 'filtros', "post" );
		//BuscarConductor( transp );

    } 
    catch (e) 
    {
        alert("Error -> listar_auto() " + e.message);
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

		 var width = Math.round( screen.width / 1.5 );
		 var height = Math.round( screen.height / 1.5 );
		 var left = Math.round( screen.width / 10 );
		 var top = Math.round( screen.height / 15 );



		objPopup.style.width = String(width) + "px";
		objPopup.style.height = String(height) + "px";
		objPopup.style.left = String(left) + "px";
		objPopup.style.top = String(top) + "px";
		objPopup.style.visibility = "visible";
		objPopup.scrollIntoView( true );
	}
	catch (e)
	{
		alert("Error " + e.message);
	}
}


function ClosePopup()   {
    //LockAplication( "unlock" );
    var objPopup = document.getElementById( "popupDIV" );
    objPopup.style.width = "0px";
    objPopup.style.height = "0px";
    objPopup.style.left = "0px";
    objPopup.style.top = "0px";
    //objPopup.innerHTML = "";
    objPopup.style.visibility = "hidden";
}

function validar_remdes(desurb)
{
	var frm = document.forms[0];
	var cont = 0;
	var tot = 0;
	
	if (desurb == 0) 
	{
		while (frm.elements[cont]) 
		{
			if (frm.elements[cont].type == "checkbox" && frm.elements[cont].checked == true) 
			{
				if (frm.elements[cont + 1].value == "") 
				{
					frm.elements[cont + 1].focus();
					return "Debe Especificar el Documento/Codigo";
				}
				else if (frm.elements[cont + 2].value == "") 
				{
					frm.elements[cont + 2].focus();
					return "Debe Especificar el Nombre";
				}
				else if (frm.elements[cont + 4].value == "0") 
				{
					frm.elements[cont + 4].focus();
					return "Debe Seleccionar la Ciudad";
				}
				else if (frm.elements[cont + 7].value == "" | frm.elements[cont + 7].value == "0") 
				{
					frm.elements[cont + 6].focus();
					return "Debe Asignar el Flete";
				}
			}
			
			cont++;
		}
	}
	else 
	{
		while (frm.elements[cont]) 
		{
			if (frm.elements[cont].type == "checkbox" && frm.elements[cont].checked == true) 
			{
				if (frm.elements[cont + 1].value == "") 
				{
					frm.elements[cont + 1].focus();
					return "Debe Especificar el Documento/Codigo";
				}
				else if (frm.elements[cont + 2].value == "") 
				{
					frm.elements[cont + 2].focus();
					return "Debe Especificar el Nombre";
				}
				else if (frm.elements[cont + 4].value == "0") 
				{
					frm.elements[cont + 4].focus();
					return "Debe Seleccionar la Ciudad";
				}
				else if (frm.elements[cont + 5].value == "") 
				{
					frm.elements[cont + 5].focus();
					return "Debe Especificar la Direccion";
				}
				else if (frm.elements[cont + 6].value == "") 
				{
					frm.elements[cont + 6].focus();
					return "Debe Especificar el Telefono";
				}
				else if (frm.elements[cont + 10].value == "" | frm.elements[cont + 10].value == "0") 
				{
					frm.elements[cont + 9].focus();
					return "Debe Asignar el Flete";
				}
			}
			
			cont++;
		}
	}
	
	return "";
}

function Cant_Remesas()
{
	var frm = document.forms[0];
	var cont = 0;
	var tot = 0;
	
	while (frm.elements[cont]) 
	{
		if (frm.elements[cont].type == "checkbox" && frm.elements[cont].checked == true) 
		{
			tot++;
		}
		cont++;
	}
	
	if (tot > 0) return "";
	else 		
		return "Debe Seleccionar por lo Menos una Remesa.";
}

function TotalPesoDestinatarios()
{
	var frm = document.forms[0];
	var cont = cont_aux = j = 0;
	var ValorTotal = 0;
	
	while (frm.elements[cont]) 
	{
		if (frm.elements[cont].type == "checkbox" && frm.elements[cont].checked == true) 
		{
			for (i = 0; i < 15; i++) 
			{
				namepesrem = "pesrem[" + j + "]";
				
				if (frm.elements[cont + i].name == namepesrem) 
				{
					ValorTotal += parseFloat(frm.elements[cont + i].value);
					i = 15;
					j++;
				}
			}
		}
		cont++;
	}
	
	return ValorTotal;
}



function validar_remision(j)
{
	formulario = document.form_remesa
	if (!formulario.elements[j].value) return false
	else if (!formulario.elements[j + 1].value) return false
	else if (!formulario.elements[j + 2].value) return false
	else if ((!formulario.elements[j + 4].value) && (!formulario.elements[j + 5].value)) return false
	return true;
}

function validar_manifi(campo, minimo, maximo)
{

	if (minimo == '') minimo = '0';
	if (!maximo) maximo = '9999999';
	
	if ((campo.value.length != 7) || (campo.value < minimo) || (campo.value > maximo)) 
	{
		window.alert("Debe Digitar un # de Manifiesto Valido  para esta agencia\n  el rango para esta agencia es desde " + minimo + " hasta " + maximo);
		campo.value = '';
		campo.focus();
	}
}

function validar_remesas(formulario, total_remesas)
{
	for (i = 0; i < total_remesas; i++) 
	{
		if (!document.getElementById('rem' + i).value) return false;
		if (!document.getElementById('pes' + i).value) return false;
		if (!document.getElementById('abrempa' + i).value) return false;
		if (!document.getElementById('abrmerc' + i).value) return false;
		if (!document.getElementById('abrclie' + i).value) return false;
		if (!document.getElementById('remit' + i).value) return false;
		if (!document.getElementById('desti' + i).value) return false;
		if (!document.getElementById('dest' + i).value) return false;
	}
	return true;
}

function getManifiesto(){
	try{
	var manifiesto = $("#manifi").val();
	var transp = $("#transp").val();
	var standa = $("#aplica_central").val();
	var parametros = "Option=VerificarManifiesto&Ajax=on&manifiesto="+manifiesto+"&transp="+transp;
	console.log(parametros);
	$.ajax({
			   url: "../"+ standa +"/despac/ajax_despac_despac.php",
	           type: "POST",
	           data: parametros,
	           async: false,
	           success: function(data){
	           		if(data == 0){
						alert("El número de manifiesto ya esta asociado a otro despacho");
						return false;
	           		}

	           }
	     	});
	}
	catch(e){
		console.log( "Error Fuction comprobar: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function aceptar(formulario, mensaje, opcion)
{
	var dup = $("#duplicar").val();
	if(dup == 0){
		var res = getManifiesto();
		if(res == false){
			return res;
		}
	}
	validacion = true;
	totalpesodest = TotalPesoDestinatarios();
	
	resulremdes = validar_remdes(formulario.desurb.value);
	
	var ind_reqcam = document.getElementById( "ind_reqcamID" );
	var cod_conduc = document.getElementById( "cod_conduc" );
	var nom_conduc = document.getElementById( "nom_conduc" );
	var cel_conduc = document.getElementById( "cel_conduc" );
			
	if (formulario.transp.value == 0) 
	{
		alert( "La Transportadora es requerida." );
		return formulario.trans.focus();
	}
	
	
	if (formulario.manifi.value == "") 
	{
		alert( "El Numero de Documento es Requerido." );
		return formulario.manifi.focus();
	}
	
	if (formulario.agencia.value == 0) 
	{
		alert( "La Agencia es Requerida." );
		return formulario.agencia.focus();
	}
	
	if (formulario.ciuori.value == 0) 
	{
		alert( "El Origen es Requerido" );
		return formulario.ciuori.focus();
	}
	
	if (formulario.ciudes.value == 0) 
	{
		alert( "El Destino es Requerido." )
		return formulario.ciudes.focus();
	}
	
	if (formulario.ruta.value == 0) 
	{
		alert( "La Ruta es Requerida." );
		return formulario.ruta.focus();
	}
	
	if (formulario.cod_tipdes.value == 0) 
	{
		alert( "El tipo de despacho es requerido." );
		return formulario.cod_tipdes.focus();
	}
	
	/*if( ind_reqcam && formulario.generador.value == 0 )
	{
		alert( "El Generador es Requerido." );
		return formulario.generador.focus();
	}*/
	
	if ( formulario.placa.value == 0 ) 
	{
		alert( "La Placa es Requerida." );
		return formulario.placa.focus();
	}
	
	if ( cod_conduc && !cod_conduc.value ) 
	{
		alert( "El Conductor es Requerido" );
		return cod_conduc.focus();		
	}
	
	if ( nom_conduc && !nom_conduc.value ) 
	{
		alert( "El Nombre del Conductor es Requerido" );
		return nom_conduc.focus();		
	}
	
	if ( cel_conduc && !cel_conduc.value ) 
	{
		alert( "El Celular del Conductor es Requerido" );
		return cel_conduc.focus();		
	}
	
	if (formulario.soltrayle.value == 1 && formulario.l_trayle.value == 0) 
	{
		alert( "El Remolque es Requerido." )
		return formulario.conduc.focus();
	}
	
	if ( formulario.pesoxx.value != "" && parseFloat(totalpesodest) > parseFloat(formulario.pesoxx.value) ) 
	{
		alert( "El Peso Total de Destinatarios, no Debe Superar el Definido en el Despacho." );
		return formulario.pesoxx.focus();
	}
	
	if (resulremdes != '') 
	{
		alert( resulremdes );
		return false;
	}
	
	
	if ( confirm( mensaje ) ) 
	{
		formulario.opcion.value = opcion;
		formulario.submit();
	}
	
	return true;
}


function aceptar_insert(formulario)
{
	//EXPRESIONES REGULARES PARA VALIDAR LOS FORMATOS
	var placa = /^[a-zA-Z]{3}[0-9]{3}/
	var manifies = /[0-9]/
	var flet = /[0-9]/
	validacion = true
	formulario = document.form_insert
	
	if (formulario.manifi.value == "") 
	{
		window.alert("El Manifiesto es Requerido")
		validacion = false
		formulario.manifi.focus()
	}
	else if (!manifies.test(formulario.manifi.value)) 
	{
		alert("El Numero de Manifiesto debe ser sin puntos(.), ni Guiones(-), ni Comas(,)");
		validacion = false
		formulario.manifi.focus()
	}
	else if (formulario.manifi.value.length < 7) 
	{
		window.alert("El manifiesto debe tener minimo 7 digitos")
		validacion = false
		formulario.manifi.focus()
	}
	else if (formulario.cliente.value == 0) 
	{
		window.alert("El Cliente es Requerido")
		validacion = false
		formulario.cliente.focus()
	}
	else if (formulario.asegra.value == 0) 
	{
		window.alert("La Aseguradora es Requerida")
		validacion = false
		formulario.asegra.focus()
	}
	else if (formulario.agencia.value == 0) 
	{
		window.alert("La Agencia es Requerida")
		validacion = false
		formulario.agencia.focus()
	}
	else if (formulario.ciuori.value == 0) 
	{
		window.alert("El Origen es Requerido")
		validacion = false
		formulario.ciuori.focus()
	}
	else if (formulario.ciudes.value == 0) 
	{
		window.alert("El Destino es Requerido")
		validacion = false
		formulario.ciudes.focus()
	}
	else if (formulario.ruta.value == 0) 
	{
		window.alert("La Ruta es Requerida")
		validacion = false
		formulario.ruta.focus()
	}
	else if (formulario.fecpla.value == "") 
	{
		window.alert("La Fecha de Salida es Requerida ")
		validacion = false
		formulario.fecpla.focus()
	}
	else if (Validar_F(formulario.fecpla.value) == false) 
	{
		validacion = false
		formulario.fecpla.focus()
	}
	else if (formulario.horpla.value == "") 
	{
		window.alert("La Hora es Requerida")
		validacion = false
		formulario.horpla.focus()
	}
	else if (Validar_H(formulario.horpla.value) == false) 
	{
		validacion = false
		formulario.horpla.focus()
	}
	else if (formulario.placa.value == "") 
	{
		window.alert("La placa es Requerida")
		validacion = false
		formulario.placa.focus()
	}
	else if (!placa.test(formulario.placa.value) && formulario.placa.value != "") 
	{
		window.alert("El Formato de la placa es AAA000")
		validacion = false
		formulario.placa.focus()
	}
	else if (formulario.conduc.value == 0) 
	{
		window.alert("El conductor es Requerido")
		validacion = false
		formulario.conduc.focus()
	}
	else if (formulario.mercan.value == 0) 
	{
		window.alert("La Mercancia es Requerida")
		validacion = false
		formulario.mercan.focus()
		
	}
	else if (formulario.tipdes.value == 0) 
	{
		window.alert("El Tipo de Despacho es Requerido")
		validacion = false
		formulario.tipdes.focus()
	}
	else if (formulario.val_flecli.value == "") 
	{
		window.alert("El Valor del Flete del Cliente es Requerido")
		validacion = false
		formulario.val_flecli.focus()
	}
	else if (!flet.test(formulario.val_flecli.value)) 
	{
		window.alert("El Valor del Flete del Cliente solo debe contener numeros ")
		validacion = false
		formulario.val_flecli.focus()
	}
	else if (formulario.val_flecon.value == "") 
	{
		window.alert("El Valor del Flete del Conductor es Requerido")
		validacion = false
		formulario.val_flecon.focus()
	}
	else if (!flet.test(formulario.val_flecon.value)) 
	{
		window.alert("El Valor del Flete del Conductor solo debe contener numeros ")
		validacion = false
		formulario.val_flecon.focus()
	}
	else 
	{
		if (confirm("Desea Insertar el Despacho.?")) 
		{
			formulario.opcion.value = 2;
			formulario.submit();
		}
	}
}

function eliminar()
{
	var frm = document.forms[0];
	var cont = 0;
	var tot = 0;
	
	while (frm.elements[cont]) 
	{
		if (frm.elements[cont].type == "checkbox" && frm.elements[cont].checked == true) 
		{
			tot++;
		}
		cont++;
	}
	
	if (tot > 0) 
	{
		if (confirm("Esta Seguro de Eliminar los Despachos Seleccionados.?")) 
		{
			frm.opcion.value = 3;
			frm.submit();
		}
	}
	else window.alert("Debe Seleccionar por lo Menos un Despacho Para Eliminar.");
}

function validarflete(indice)
{
	var frm = document.forms[0];
	var var1 = "fleunirem" + indice;
	var var2 = "tabflerem" + indice;
	
	document.getElementById(var1).value = 0;
	document.getElementById(var2).value = 0;
}


  
