/*
 @author Jasson David Cruz Cardozo
 @brief Cerrar el popup de consolidaciones 
*/
function cerrarDialog()
{
	try {
		$( "#dialog" ).dialog( "close" );
	}
	catch (e) {
		alert( "Error en la Funcion cerrarDialog: " + e.message );
	}	
}//Fin Funcion cerrarDialog

/*
 @author Jasson David Cruz Cardozo
 @brief Validacion de viajes seleccionados 
*/
function validarConsolidacionViajes()
{
	try
	{
		//Variable que contiene el numero de viajes que se deben validar
		var num_viajes = document.getElementById("numViajesId").value;
		//Variable que contiene el nit de la transportadora 
		var nitTra = document.getElementById("nitTranspId").value;		
		//Variable que contiene el numero de la placa 
		var placax = document.getElementById("numPlacaxId").value;
		//Variable que contara el numero de viajes seleccionados
		var flag = 0;
		//Viariable que contiende el numero de los desapchos seleccionados
		var viajes = "";

		//For que recorrera los viajes para verificar el estado
		for( i = 0; i < num_viajes; i++ )
		{
			var radio = document.getElementById("radio" + i + "1").checked;
			var despacho = document.getElementById("viaje" + i ).value;
			
			//Sumando el numero de viajes que se consolidaran		
			console.log(i);
			if( radio == true )
			{
				//variable acumuladora con el numero de viajes
				flag += 1;
				//variable con los numeros de viajes concatenados
				despacho = flag > 1 ? "," + despacho : despacho ; 
				viajes = viajes + despacho ;
			}//FIN IF radio
			
		}//Fin For 
			
		console.log("viajes: "+num_viajes +" -  variable flag: "+flag );
		//Validando que tengo por lo menos 2 viajes para consolidar
		if( flag < 2 )
		{
			alert( 'Se requiere mínimo 2 viajes para realizar la consolidación.' );
		}
		else
		{
			$.ajax({
			  type: "POST",
			  url: "../satt_standa/consol/ajax_consol_viajes.php",
			  data: "option=ConsolidarViajesAutomatico&viajes=" + viajes + "&placa=" + placax + "&nitTransp=" + nitTra + "",
 			  async: false,
			  beforeSend : 
				function () 
				{ 
				  $("#dialog").html('<table align="center"><tr><td><img src="../satt_standa/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
				},
			  success : 
				function ( data ) 
				{ 
				  //---------------------------------------------------------------
				  // Cambiando el titulo del popup
				  //---------------------------------------------------------------
				  var titulo = '<b>Consolidaci&oacute;n de Viajes Manualmente - Selección de Ruta</b>';
				  $("#dialog").parent().find("span.ui-dialog-title").html( titulo );
				  //---------------------------------------------------------------
				  $("#dialog").html( data );
				}
			});		
		}//Fin If validacion
	}
	catch(e)
	{
		console.log( "Error Function validarConsolidacionViajes: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
	
}//Fin Funcion validarConsolidacionViajes

function newDestin()
{
  try
  {
  	var counter = $("#counterID").val();
  	$.ajax({
		  type: "POST",
		  url: "../satt_standa/consol/ajax_consol_viajes.php",
		  data: "option=ShowDestin&counter="+ counter + "&ind_ajax=1",
		  async: false,
		  success : 
			function ( datos ) 
			{ 
			  if( $("#DestinID").find('div').length == 0 )
  			    $("#DestinID").html( datos );
			  else
			    $("#DestinID").find("div:last-child").after( datos );
			  	
	          $("#counterID").val( ( parseInt( counter ) + 1 ) );
			}
		});
  }
  catch( e )
  {
  	console.log( e.message );
  }
}

/*
 @author Jasson David Cruz Cardozo
 @brief Validacion de viajes seleccionados 
*/
function validarRutaConsolidacion()
{
	try
	{	
		//-------------------------------------------------------------------
		//Variable que contiene el numero de viajes que se deben validar
		var cod_rutasx = document.getElementsByName("codRutasx");
		//Variable para realizar validacion de ruta seleccionada
		var num_rutas = 0;
		//Codigo de la ruta seleccionada por el usuario
		var cod_rutas = null;
		//-------------------------------------------------------------------
		//Variable que contiene los viajes que se deben consolidar
		var viajes = document.getElementById("viajesId").value;
		//Variable que contiene el nit de la transportadora 
		var nitTra = document.getElementById("nitTranspId").value;		
		//Variable que contiene el numero de la placa de los despachos
		var placax = document.getElementById("placaId").value;
		//-------------------------------------------------------------------

		for( i = 0 ; i < cod_rutasx.length ; i++ ) 
		{
			if(cod_rutasx.item(i).checked == false) 
			{
				num_rutas++;
			}
		}//Fin For recorrido de radios seleccionados
		
		if( num_rutas == cod_rutasx.length) 
		{
			alert("Debe seleccionar una ruta para continuar con el proceso de consolidación.");
			return false;
		} 
		else 
		{
			cod_rutas = $('input:radio[name=codRutasx]:checked').val();
			
			atributes = "option=validacionDestinos";
			atributes += "&viajes=" + viajes + "";
			atributes += "&placa=" + placax + "";
			atributes += "&nitTransp=" + nitTra + "";
			atributes += "&cod_ruta=" + cod_rutas + "";
		
			$.ajax({
			  type: "POST",
			  url: "../satt_standa/consol/ajax_consol_viajes.php",
			  data: atributes ,
 			  async: false,
			  beforeSend : 
				function () 
				{ 
				  $("#dialog").html('<table align="center"><tr><td><img src="../satt_standa/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
				},
			  success : 
				function ( data ) 
				{ 
				  //---------------------------------------------------------------
				  // Cambiando el titulo del popup
				  //---------------------------------------------------------------
				  var titulo = '<b>Consolidaci&oacute;n de Viajes Manualmente - Destinatarios</b>';
				  $("#dialog").parent().find("span.ui-dialog-title").html( titulo );
				  //---------------------------------------------------------------
				  $("#dialog").html( data );
				}
			});			
		}
	}
	catch (e)
	{
		alert( "Error en la Funcion validarRutaConsolidacion: " + e.message );
	}	
	
}//Fin Funcion validarRutaConsolidacion	

/*
 @author Jasson David Cruz Cardozo
 @brief Validacion de viajes seleccionados 
*/
function validarFechasDestin()
{
	try
	{	
		//-------------------------------------------------------------------
		//Variable que contiene el numero de viajes que se deben validar
		var cod_rutasx = document.getElementById("codRutasxId");
		//Variable que contiene los viajes que se consolidaran
		var viajes = document.getElementById("viajesId");		
		//Variable que contiene la placa de los despachos
		var placa = document.getElementById("numPlacaxId");		
		//Variable que contiene el nit de la transportadora
		var nitTransp = document.getElementById("nitTranspId");						
		//Variable que contiene el numero de fechas que se deben validar 
		var destin = document.getElementById("numDestinId").value;
		//Variable que contiene los datos concatenados 
		var data = '';
		//-------------------------------------------------------------------

		for( i = 0 ; i < destin ; i++ ) 
		{
			despa = document.getElementById('num_despac' + i + 'Id');
			docum = document.getElementById('num_docume' + i + 'Id');
			fecha = document.getElementById('fec_citdes' + i + 'ID');
			horax = document.getElementById('hor_citdes' + i + 'ID');

			//-------------------------------------------
			//Fecha Minima para una cita de descargue
			//-------------------------------------------
			fecMin = Date.parse('2011-01-01');
			//-------------------------------------------
			//Fecha que se toma desde el formulario
			//-------------------------------------------
			fecCom = Date.parse(fecha.value);
			//-------------------------------------------
			
			if( fecCom > fecMin )
			{
				if( horax.value == '00:00:00' )
				{
					alert( 'La hora de cita de descargue ' + horax.value + ' no es válida, corrijala e intentelo nuevamente.' );
					horax.focus();
					return false;				
				}
				else
				{
					//--------------------------------------------------------------------------------------
					// Informacion concatenada del destinatario y despacho
					//--------------------------------------------------------------------------------------
					data += despa.value + '|' + docum.value + '|' + fecha.value + '|' + horax.value + '||';
					//--------------------------------------------------------------------------------------
				}
			}
			else
			{
				alert( 'La fecha de cita de descargue ' + fecha.value + ' no es válida, corrijala e intentelo nuevamente.' );
				fecha.focus();
				return false;
			}
		}//Fin For recorrido de radios seleccionados

		var ind_nuedes = '0';
		var adicionalx = '';
		var nom_itemxx = '';
		var val_messag = '';
		var ind_breakx = false;
		var cou_nuedes = 0;
		if( $("#DestinID > div").size() > 0 )
		{
		  ind_nuedes = '1';
		  
		  $("#DestinID > div").each(function( i ) 
		  {
		  	cou_nuedes++;
		  	if( ind_breakx )
		  	  return false;
		  	$(this).find(':input').each(function( j ) 
		  	{
		  	  nom_itemxx = $(this).attr('name').substr(0, 10);
		  	  rea_itemxx = $(this).attr('name');
		  	  
		  	  if( nom_itemxx != 'num_docalt' && $(this).val() == '' )
		  	  {
		  	  	ind_breakx = true;
		  	  	val_messag = nom_itemxx == 'cod_ciudad' ? 'Seleccione' : 'Digite';
		  	    alert( val_messag + " el Campo" );
		  	    $(this).focus();
		  	    return false;
		  	  }
		  	  else
			  {
  				adicionalx += '&' + rea_itemxx + '=' + $(this).val();
			  }
		  	});
		  });
		}
		
		adicionalx += '&ind_nuedes=' + ind_nuedes;
		adicionalx += '&cou_nuedes=' + cou_nuedes;
		//Elimninando los 2 ultimos || para validar mas facil
		data = data.substring(0, data.length-2);

		atributes = "option=saveFechasDestin";
		atributes += "&tip_consol=automatica";
		atributes += "&viajes=" + viajes.value + "";
		atributes += "&placa=" + placa.value + "";
		atributes += "&nitTransp=" + nitTransp.value + "";
		atributes += "&cod_ruta=" + cod_rutasx.value + "";
		atributes += "&data=" + data + "";

		$.ajax({
		  type: "POST",
		  url: "../satt_standa/consol/ajax_consol_viajes.php",
		  data: atributes + adicionalx,
		  async: false,
		  beforeSend : 
			function () 
			{ 
			  $("#dialog").html('<table align="center"><tr><td><img src="../satt_standa/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
			},
		  success : 
			function ( data ) 
			{ 
			  //---------------------------------------------------------------
			  // Cambiando el titulo del popup
			  //---------------------------------------------------------------
			  var titulo = '<b>Consolidaci&oacute;n de Viajes Manualmente - Ordenamiento de Viajes</b>';
			  $("#dialog").parent().find("span.ui-dialog-title").html( titulo );
			  //---------------------------------------------------------------
			  $("#dialog").html( data );
			}
		});			
	}
	catch (e)
	{
		alert( "Error en la Funcion validaFechasDestin: " + e.message );
	}
	
}//FIN FUNCION validarFechasDestin

/*
 @author Jasson David Cruz Cardozo
 @brief Agregar un nuevo destinatario en la grilla
*/
function AddGrid( tipo , counter )
{
  try
  {
    var Standa = 'satt_standa';
    counter = tipo == 'validate' ? counter  : $( "#counterID" ).val() ;

    $("#loading").remove();

    if( $( "#num_factur" + counter + "ID" ).val() == '' )
    {
      $("#loading").remove();
      $( "#num_factur" + counter + "ID" ).focus().after("<span id='loading'><br>Digite El Documento</span>");
      return false;
    }
   	else if( $( "#num_docalt" + counter + "ID" ).val() == '' )
    {
      $("#loading").remove();
      $( "#num_docalt" + counter + "ID" ).focus().after("<span id='loading'><br>Digite El Documento Alterno</span>");
      return false;
    }    
   	else if( $( "#nom_destin" + counter + "ID" ).val() == '' )
    {
      $("#loading").remove();
      $( "#nom_destin" + counter + "ID" ).focus().after("<span id='loading'><br>Digite El Destinatario</span>");
      return false;
    }        
    else if( $( "#cod_ciudad" + counter + "ID" ).val() == '' )
    {
      $("#loading").remove();
      $( "#cod_ciudad" + counter + "ID" ).focus().after("<span id='loading'><br>Seleccione La Ciudad</span>");
      return false;
    }
    else if( $( "#dir_destin" + counter + "ID" ).val() == '' )
    {
      $("#loading").remove();
      $( "#dir_destin" + counter + "ID" ).focus().after("<span id='loading'><br>Digite La Direccion Del Destinatario</span>");
      return false;
    }    
    else if( $( "#nom_contac" + counter + "ID" ).val() == '' )
    {
      $("#loading").remove();
      $( "#nom_contac" + counter + "ID" ).focus().after("<span id='loading'><br>Digite El Numero De Contacto</span>");
      return false;
    }        
    else if( $( "#fec_citdes" + counter + "ID" ).val() == '' )
    {
      $("#loading").remove();
      $( "#fec_citdes" + counter + "ID" ).focus().after("<span id='loading'><br>Seleccione Fecha de Descargue</span>");
      return false;
    }
    else if( $( "#hor_citcar" + counter + "ID" ).val() == '' )
    {
      $("#loading").remove();
      $( "#hor_citcar" + counter + "ID" ).focus().after("<span id='loading'><br>Seleccione Hora de Descargue</span>");
      return false;
    }
    else
    {
		if( tipo != 'validate' )
		{
	      $.ajax({
	        type: "POST",
	        url: "../satt_standa/consol/ajax_consol_viajes.php",
	        data: "ind_ajax=1&option=ShowDestin&counter=" + ( parseInt( counter ) + 1 ),
	        async: false,
	        success: function( datos )
	        {
	          $("#DestinID").append( datos );
	          $( "#counterID" ).val( ( parseInt( counter ) + 1 ) );
	        }
	      });
      
      	  $('#datdesID').parent().css('height','auto');    
		}
		else
		{
			return true;
		}	

    }
    
  }
  catch( e )
  {
    alert( "Error en la Funcion AddGrid: " + e.message );
    return false;
  }
}//Fin Funcion AddGrid

/*
 @author Jasson David Cruz Cardozo
 @brief Eliminar un destinatario de la grilla
*/
function DropGrid( div_id )
{
  $( "#datdes"+div_id+"ID" ).remove();
  $("#counterID").val( $("#DestinID > div").last().attr('id').substr(6).split("ID")[0] );
}//Fin Funcion DropGrid

/*
 @author Jasson David Cruz Cardozo
 @brief Validacion para enviar los destinatarios al siguiente paso
*/
function InserDestin()
{
  try
  {
    var Standa = $("#StandaID").val();
    var counter = $( "#counterID" ).val();
    var atributes = 'counter=' + counter;
  
    var num_factur,num_docalt,cod_genera,nom_destin,cod_ciudad,dir_destin,nom_contac,fec_citdes,hor_citcar;

    for( var i = 0; i <= counter; i++ )
    {

	  if( !AddGrid( 'validate' , i ) )
	  {
	  	return false;
	  }

      num_factur = $( "#num_factur"+i+"ID" ).val();
      atributes += '&num_factur'+i+'=' + num_factur;
      
      num_docalt = $( "#num_docalt"+i+"ID" ).val();
      atributes += '&num_docalt'+i+'=' + num_docalt;
      
      nom_destin = $( "#nom_destin"+i+"ID" ).val();
      atributes += '&nom_destin'+i+'=' + nom_destin;
      
      cod_ciudad = $( "#cod_ciudad"+i+"ID" ).val();
      atributes += '&cod_ciudad'+i+'=' + cod_ciudad;
      
      dir_destin = $( "#dir_destin"+i+"ID" ).val();
      atributes += '&dir_destin'+i+'=' + dir_destin;
      
      nom_contac = $( "#nom_contac"+i+"ID" ).val();
      atributes += '&nom_contac'+i+'=' + nom_contac;
      
      fec_citdes = $( "#fec_citdes"+i+"ID" ).val();
      atributes += '&fec_citdes'+i+'=' + fec_citdes;
      
      hor_citcar = $( "#hor_citcar"+i+"ID" ).val();
      atributes += '&hor_citcar'+i+'=' + hor_citcar;
    }

	atributes += "&option=ConsolGeneral";
	atributes += "&tip_consol=manual";
	atributes += "&viajes=" + $("#viajesId").val() + "";
	atributes += "&placa=" + $("#numPlacaxId").val() + "";
	atributes += "&nitTransp=" + $("#nitTranspId").val() + "";
	atributes += "&cod_ruta=" + $("#codRutasxId").val() + "";

    $.ajax({
      type: "POST",
      url: "../satt_standa/consol/ajax_consol_viajes.php?",
      data: atributes,
      async: false,
	  beforeSend : 
		function () 
		{ 
		  $("#dialog").html('<table align="center"><tr><td><img src="../satt_standa/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
		},
	  success : 
		function ( data ) 
		{ 
		  //---------------------------------------------------------------
		  // Cambiando el titulo del popup
		  //---------------------------------------------------------------
		  var titulo = '<b>Consolidaci&oacute;n de Viajes</b>';
		  $("#dialog").parent().find("span.ui-dialog-title").html( titulo );
		  //---------------------------------------------------------------
		  $("#dialog").html( data );
		}
    });
  }
  catch( e )
  {
    alert( "Error en la Funcion InserDestin: " + e.message );
    return false;
  }
}

function refreshDespac()
{
	viaje = $( "#viajeID" ).val();
	urlServer = $( "#urlServerID" ).val();
	url = /*urlServer + */'index.php?cod_servic=3302&window=central&despac=' + viaje + '&opcion=1';
	window.location.href = url;
}

function findRoutes( nit_transp )
{
  try
  {
	var viajesID = $("#viajesID").val();
	var placaID  = $("#placaID").val();
  	var cod_ciuori = $("#cod_ciuoriID");
  	var cod_ciudes = $("#cod_ciudesID");
  	
  	if( cod_ciuori.val() == '' )
  	{
  	  alert("Seleccione el Origen");
  	  cod_ciuori.focus();
  	  return false;
  	}
  	else if( cod_ciudes.val() == '' )
  	{
  	  alert("Seleccione el Destino");
  	  cod_ciudes.focus();
  	  return false;
  	}
  	else
  	{
  	  $.ajax({
       type: "POST",
       url: "../satt_standa/consol/ajax_consol_viajes.php",
       data: "option=findRoutes&cod_ciudes="+cod_ciudes.val()+"&cod_ciuori="+cod_ciuori.val()+"&nit_transp="+nit_transp+"&viajes="+viajesID+"&placa="+placaID,
       async: false,
	   beforeSend : 
	     function() 
	     { 
		   $("#rutasxID").html('<table align="center"><tr><td><img src="../satt_standa/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
		 },
	   success : 
		 function ( data ) 
		 { 
		   $("#rutasxID").html( data );
		 }
     });
  	}
  }
  catch( e )
  {
  	console.log( e.message );
  }
}