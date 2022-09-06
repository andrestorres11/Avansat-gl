//-----------------------------------
//LISTADO DE LOS PROPIETARIOS DE VEHICULOS
function ListPropie()
{
  try
  {
		//----------
  	if( !isLineaSeleccionada() )
  	{
  		alert( "Por favor seleccione una Linea" );
  		return false;
  	}
		//----------
   
    var cons = document.getElementById('consul_propieID').value;
    var cod_servic = document.getElementById('cod_servicID').value;
    var fStandar = document.getElementById('fStandarID').value;

    LoadPopup();
    var atributes;
    atributes = "Case=list_propie";
    atributes += "&cons=" + cons;
    AjaxGetData('../' + fStandar + '/vehicu/ajax_vehicu.php?', atributes, 'popupDIV', 'post');
  }
  catch( e )
  {
    alert( "Error Funcion ListPropie: " + e.message );
  }
}
//-----------------------------------

//-----------------------------------
function SetListado( type )
{
  try 
  {
  	var fStandar = '';
  	var fRow = '';
  	if( document.getElementById( 'fStandarID' ) )
    	var fStandar = document.getElementById( 'fStandarID' ).value;

  	if( document.getElementById( 'ActualRowID' ) )
    	var fRow = document.getElementById( 'ActualRowID' ).value;

    //----------------------------------------------------------------------
    var tercer = document.getElementById( "DLLink"+fRow+"-0" ).innerHTML;
    var nom_tercer = document.getElementById( "DLCell"+fRow+"-1" ).innerHTML;
    //----------------------------------------------------------------------

    var atributes = "Case=get_" + type + "";
    atributes += "&cod_tercer="+tercer;
    atributes += "&nom_tercer="+nom_tercer;
    AjaxGetXml( '../'+fStandar+'/vehicu/ajax_vehicu.php?', atributes, 'post', 'ClosePopup();' );
    ClosePopup();
    return false;
  }
  catch(e)
  {
    alert( 'Error Funcion SetListado: ' + e );
  }
}
//-----------------------------------

//-----------------------------------
function ListTenedo()
{
  try
  {
		//----------
  	if( !isLineaSeleccionada() )
  	{
  		alert( "Por favor seleccione una Linea" );
  		return false;
  	}
		//----------
   
    var cons = document.getElementById('consul_tenedoID').value;
    var cod_servic = document.getElementById('cod_servicID').value;
    var fStandar = document.getElementById('fStandarID').value;

    LoadPopup();
    var atributes;
    atributes = "Case=list_tenedo";
    atributes += "&cons=" + cons;
    AjaxGetData('../' + fStandar + '/vehicu/ajax_vehicu.php?', atributes, 'popupDIV', 'post');    
  }
  catch( e )
  {
    alert( "Error Funcion ListTenedo: " + e.message );
  }
}
//-----------------------------------

//-----------------------------------
function ListConduc()
{
  try
  {
		//----------
  	if( !isLineaSeleccionada() )
  	{
  		alert( "Por favor seleccione una Linea" );
  		return false;
  	}
		//----------
   
    var cons = document.getElementById('consul_conducID').value;
    var cod_servic = document.getElementById('cod_servicID').value;
    var fStandar = document.getElementById('fStandarID').value;

    LoadPopup();
    var atributes;
    atributes = "Case=list_conduc";
    atributes += "&cons=" + cons;

    AjaxGetData('../' + fStandar + '/vehicu/ajax_vehicu.php?', atributes, 'popupDIV', 'post');    
  }
  catch( e )
  {
    alert( "Error Funcion ListConduc: " + e.message );
  }
}
//-----------------------------------

//-----------------------------------
function isLineaSeleccionada()
{
	//----------
  var linea = document.getElementById('lineaID').value;
  if( linea == 0 || linea == null )
  	return false;
	//----------
	return true;
}
//-----------------------------------




function getLimitFecha()
{
	var hoy = new Date();
	
	var dia = hoy.getFullYear() + "-";
	
	if (hoy.getMonth() + 1 < 10) dia += "0" + (hoy.getMonth() + 1);
	else dia += (hoy.getMonth() + 1);
	
	dia += "-";
	
	if (hoy.getDate() < 10) dia += "0" + (hoy.getDate());
	else dia += (hoy.getDate());
	
	return dia;
}

function validar_form(formulario)
{
	var plac = /[a-zA-Z]{3}[0-9]{3}/
	var modelo = /^[0-9]{4}/
	var cilindraje = /[0-9]/
	var regcarga = /[0-9]/
	var vpeso = /^[0-9]+\.?[0-9]*$/
	var puertas = /^[0-9]{1,2}$/
	var vfec = /^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/
	fecha = new Date();
	anno = "1950"
	
	var hoy = getLimitFecha();
	
	validacion = true
	formulario = document.form_vehiculos
	
	var conf = String(formulario.config.value)
	var eje_conf = conf.substring(conf.length - 1)
	
	var valGPS = valCamposGPS();
	
	if (!formulario.transp.value) 
	{
		window.alert("La Transportadora es Requerida")
		validacion = false
		formulario.transp.focus()
	}
	else if (!formulario.placa.value) 
	{
		window.alert("El Numero de la Placa es Requerido")
		validacion = false
		formulario.placa.focus()
	}
	else if (!plac.test(formulario.placa.value)) 
	{
		window.alert("El Numero de la Placa Debe Iniciar con Tres Letras, Seguido de Tres Numeros")
		validacion = false
		formulario.placa.focus()
	}
	else if (!formulario.marca.value) 
	{
		window.alert("Escoja una Marca")
		validacion = false
		formulario.marca.focus()
	}

//-------------------------------------
	else if (!formulario.linea.value) 
	{
		window.alert("Escoja una Linea")
		validacion = false
		formulario.linea.focus()
	}
	else if (!formulario.modelo.value) 
	{
		window.alert("El modelo es Requerido")
		validacion = false
		formulario.modelo.focus()
	}
	else if (!modelo.test(formulario.modelo.value)) 
	{
		window.alert("EL modelo solo debe contener Numeros")
		validacion = false
		formulario.modelo.focus()
	}
	else if ((formulario.repote.value != "") && (!modelo.test(formulario.repote.value))) 
	{
		window.alert("El año de Repotenciado no es Valido")
		validacion = false
		formulario.repote.focus()
	}
	else if (!formulario.color.value) 
	{
		window.alert("Escoja un Color")
		validacion = false
		formulario.color.focus()
	}
	else if (!formulario.tipvin.value) 
	{
		window.alert("Escoja Tipo de Vinculacion")
		validacion = false
		formulario.tipvin.focus()
	}
	else if (!formulario.carroc.value) 
	{
		window.alert("Escoja una Carroceria")
		validacion = false
		formulario.carroc.focus()
	}
	else if (!formulario.motor.value) 
	{
		window.alert("Digite # de Motor")
		validacion = false
		formulario.motor.focus()
	}
	else if (!formulario.serie.value) 
	{
		window.alert("Digite # de Serie")
		validacion = false
		formulario.serie.focus()
	}
	else if ( formulario.peso.value && formulario.peso.value > 20) 
	{
		window.alert("El peso Vacio no debe ser mayor a 20 Toneladas")
		validacion = false
		formulario.peso.focus()
	}
	else if ( formulario.peso.value && !vpeso.test(formulario.peso.value)) 
	{
		window.alert("El Peso no es Valido")
		validacion = false
		formulario.peso.focus()
	}
	else if ( formulario.capaci.value && !vpeso.test(formulario.capaci.value)) 
	{
		window.alert("La capacidad no es Valida")
		validacion = false
		formulario.capaci.focus()
	}
	else if ( formulario.capaci.value && formulario.capaci.value > 40) 
	{
		window.alert("La Capacidad debe ser menor o igual a 40 Tn")
		validacion = false
		formulario.capaci.focus()
	}
	else if (!formulario.config.value) 
	{
		window.alert("La configuración es Requerida")
		validacion = false
		formulario.config.focus()
	}
	else if (formulario.config.value != '2' && formulario.config.value != '3' && formulario.config.value != '4' && !formulario.trayler.value) 
	{
		window.alert("El Remolque es Requerido")
		validacion = false
		formulario.trayler.focus()
	}
	else if (!formulario.poliza.value) 
	{
		window.alert("El Numero de SOAT es Requerido")
		validacion = false
		formulario.poliza.focus()
	}
	else if (!formulario.asegra.value) 
	{
		window.alert("La Aseguradora del SOAT es Requerida")
		validacion = false
		formulario.asegra.focus()
	}
	else if (!formulario.fecha1.value) 
	{
		window.alert("La Fecha de Vencimiento del SOAT es Requerida")
		validacion = false
		formulario.fecha1.focus()
	}
	else if (formulario.fecha1.value < hoy) 
	{
		window.alert("La Fecha de Vencimiento del SOAT no Puede ser Menor a la Fecha de hoy " + hoy)
		validacion = false;
		return formulario.fecha1.focus()
	}
//-------------------------------------
/*
	else if (!formulario.color.value) 
	{
		window.alert("Escoja un Color")
		validacion = false
		formulario.color.focus()
	}
*/
	else if ( formulario.regnal.value && !regcarga.test(formulario.regnal.value)) 
	{
		window.alert("El Registro Nacional de Carga Solo debe contener numeros")
		validacion = false
		formulario.regnal.focus()
	}
	
	else if ( formulario.fecha1.value && formulario.fecha1.value < hoy) 
	{
		window.alert("La Fecha de Vencimiento del SOAT no Puede ser Menor a la Fecha de hoy " + hoy)
		validacion = false
		return formulario.fecha1.focus()
	}
	else if (!formulario.tenedo.value) 
	{
		window.alert("El Poseedor es Requerido")
		validacion = false
		formulario.tenedo.focus()
	}
	else if (!formulario.propie.value) 
	{
		window.alert("El Propietario es Requerido")
		validacion = false
		formulario.propie.focus()
	}
	else if (!formulario.conduc.value) 
	{
		window.alert("El Conductor es Requerido")
		validacion = false
		formulario.conduc.focus()
	}
	else if (document.getElementById("ejes") != null) 
	{
		if (formulario.config.value != '2' || formulario.config.value != '3' || formulario.config.value != '4') 
		{
			if (formulario.ejes.value != eje_conf) 
			{
				alert("Los Ejes del Trayler no corresponden a la configuracion del vehiculo")
				validacion = false
				formulario.trayler.focus()
			}
			else 
			{
				if (confirm("Confirma que desea Ingresar el Vehiculo placas " + formulario.placa.value)) 
				{
					formulario.opcion.value = 4
					formulario.submit()
					return validacion
				}
			}
		}
	}
	else if (valGPS != "") 
	{
		window.alert(valGPS)
		validacion = false
	}
	else if (confirm("Confirma que desea Ingresar el Vehiculo placas " + formulario.placa.value)) 
	{
		formulario.opcion.value = 4
		formulario.submit()
		return validacion
	}
}

function valCamposGPS()
{
	var frm = document.forms[0];
	var cont = 0;
	var prm = 1;
	
	while (frm.elements[cont]) 
	{
		if (frm.elements[cont].type == "checkbox") 
		{
			if (prm == 1) prm = 0;
			else 
			{
				if (frm.elements[cont].checked == true) 
				{
					if (frm.elements[cont + 1].type == "text") 
					{
						if (frm.elements[cont + 1].value == "") 
						{
							frm.elements[cont + 1].focus();
							return "Debe Digitar el Numero de ID del Dispositivo";
						}
					}
				}
			}
		}
		cont++;
	}
	
	return "";
}

function aceptar_update(formulario)
{
	var plac = /[a-zA-Z]{3}[0-9]{3}/
	var modelo = /^[0-9]{4}/
	var cilindraje = /[0-9]/
	var regcarga = /[0-9]/
	var vpeso = /^[0-9]+\.?[0-9]*$/
	var puertas = /^[0-9]{1,2}$/
	var vfec = /^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/
	fecha = new Date();
	anno = "1950"

	validacion = true
	formulario = document.form_actua
	var hoy = getLimitFecha();
	
	var valGPS = valCamposGPS();
	

	//-------------------------------------
	if (!formulario.placa.value) 
	{
		window.alert("El Numero de la Placa es Requerido")
		validacion = false
		formulario.placa.focus()
	}
	else if (!formulario.marca.value) 
	{
		window.alert("Escoja una Marca")
		validacion = false
		formulario.marca.focus()
	}
	else if (!formulario.linea.value) 
	{
		window.alert("Escoja una Linea")
		validacion = false
		formulario.linea.focus()
	}
	else if (!formulario.modelo.value) 
	{
		window.alert("El modelo es Requerido")
		validacion = false
		formulario.modelo.focus()
	}
	else if ((formulario.repote.value != "") && (!modelo.test(formulario.repote.value))) 
	{
		window.alert("El año de Repotenciado no es Valido")
		validacion = false
		formulario.repote.focus()
	}
	else if (!formulario.colorx.value) 
	{
		window.alert("Escoja un Color")
		validacion = false
		formulario.colorx.focus()
	}
	else if (!formulario.tipveh.value) 
	{
		window.alert("Escoja Tipo de Vinculacion")
		validacion = false
		formulario.tipveh.focus()
	}
	else if (!formulario.carroc.value) 
	{
		window.alert("Escoja una Carroceria")
		validacion = false
		formulario.carroc.focus()
	}
	else if (!formulario.motor.value) 
	{
		window.alert("Digite # de Motor")
		validacion = false
		formulario.motor.focus()
	}
	else if (!formulario.serie.value) 
	{
		window.alert("Digite # de Serie")
		validacion = false
		formulario.serie.focus()
	}
	else if (!formulario.pesva.value) 
	{
		window.alert("Digite Peso Vacio")
		validacion = false
		formulario.pesva.focus()
	}
	else if (!formulario.capaci.value) 
	{
		window.alert("Digite Capacidad")
		validacion = false
		formulario.capaci.focus()
	}
	else if (!formulario.config.value) 
	{
		window.alert("Escoja Configuracion")
		validacion = false
		formulario.config.focus()
	}
	else if (!formulario.regnal.value) 
	{
		window.alert("Digite # Registro Nacional de Carga")
		validacion = false
		formulario.regnal.focus()
	}
	else if (!formulario.numsoa.value) 
	{
		window.alert("El Numero de SOAT es Requerido")
		validacion = false
		formulario.numsoa.focus()
	}
	else if (!formulario.asesoa.value) 
	{
		window.alert("La Aseguradora del SOAT es Requerida")
		validacion = false
		formulario.asesoa.focus()
	}
	else if (!formulario.vigsoa.value) 
	{
		window.alert("La Fecha de Vencimiento del SOAT es Requerida")
		validacion = false
		formulario.vigsoa.focus()
	}
	else if (formulario.vigsoa.value < hoy) 
	{
		window.alert("La Fecha de Vencimiento del SOAT no Puede ser Menor a la Fecha de hoy " + hoy)
		validacion = false;
		return formulario.vigsoa.focus()
	}
	else if (formulario.config.value != '2' && formulario.config.value != '3' && formulario.config.value != '4' && !formulario.trayler.value) 
	{
		window.alert("El Remolque es Requerido")
		validacion = false
		formulario.trayler.focus()
	}
	else if (!formulario.tenedo.value) 
	{
		window.alert("El Poseedor es Requerido")
		validacion = false
		formulario.tenedo.focus()
	}
	else if (!formulario.propie.value) 
	{
		window.alert("El Propietario es Requerido")
		validacion = false
		formulario.propie.focus()
	}
	else if (!formulario.conduc.value) 
	{
		window.alert("El Conductor es Requerido")
		validacion = false
		formulario.conduc.focus()
	}
	//-------------------------------------
	

	else if (valGPS != "") 
	{
		window.alert(valGPS)
		validacion = false
	}
	else 
	{
		if (confirm('Esta Seguro que Desea Actualizar el Vehículo?')) 
		{
			formulario.opcion.value = 3
			formulario.submit()
			return validacion
		}
	}
}


//------------------------------------------------------------------
function aceptar_update_sinValidar(formulario)
{
	var plac = /[a-zA-Z]{3}[0-9]{3}/
	var modelo = /^[0-9]{4}/
	var cilindraje = /[0-9]/
	var regcarga = /[0-9]/
	var vpeso = /^[0-9]+\.?[0-9]*$/
	var puertas = /^[0-9]{1,2}$/
	var vfec = /^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/
	fecha = new Date();
	anno = "1950"

	validacion = true
	formulario = document.form_actua
	var hoy = getLimitFecha();
	
	var valGPS = valCamposGPS();

		if (confirm('Esta Seguro que Desea Actualizar el Vehículo?')) 
		{
			formulario.opcion.value = 3
			formulario.submit()
			return true
		}
}
//------------------------------------------------------------------


function aceptar_reser(formulario)
{
	validacion = true
	formulario = document.form_reservas
	if (formulario.placa.value == "") 
	{
		window.alert("Digite una Placa")
		validacion = false
	}
	else if (formulario.num_regis.value == 0) 
	{
		window.alert("La Placa no esta Registrada o no esta Aprobada")
		validacion = false
	}
	else if (formulario.ciudad.value == 0) 
	{
		window.alert("Escoja una Ciudad")
		validacion = false
	}
	else 
	{
		if (confirm('Esta Seguro que Desea Insertar la Reserva?')) 
		{
			formulario.opcion.value = 6
			formulario.submit()
			return validacion
		}
	}
}

function aceptar_confir(formulario)
{
	validacion = true
	formulario = document.form_reservas
	if (formulario.placa.value == "") 
	{
		window.alert("Digite una Placa")
		validacion = false
	}
	else if (formulario.num_regis.value == 0) 
	{
		window.alert("La Placa no esta Registrada o no esta Aprobada")
		validacion = false
	}
	else if (formulario.max_reserv.value == 0) 
	{
		window.alert("La Placa no tiene Reservas pendientes")
		validacion = false
	}
	else 
	{
		if (confirm('Esta Seguro que Desea Confirmar el Vehículo?')) 
		{
			formulario.opcion.value = 9
			formulario.submit()
			return validacion
		}
	}
}

function aceptar_cancel(formulario)
{
	validacion = true
	formulario = document.form_reservas
	if (formulario.placa.value == "") 
	{
		window.alert("Digite una Placa")
		validacion = false
	}
	else if (formulario.num_regis.value == 0) 
	{
		window.alert("La Placa no esta Registrada o no esta Aprobada")
		validacion = false
	}
	else 
	{
		if (confirm('Esta Seguro que Desea Cancelar el Vehículo?')) 
		{
			formulario.opcion.value = 10
			formulario.submit()
			return validacion
		}
	}
}

function aceptar_apr(formulario)
{
	validacion = true
	formulario = document.form_apr
	if (formulario.placa.value == "") 
	{
		window.alert("Digite una Placa")
		validacion = false
	}
	else if (formulario.num_regis.value == 0) 
	{
		window.alert("La Placa no esta Registrada")
		validacion = false
	}
	else 
	{
		formulario.opcion.value = 2
		formulario.submit()
		return validacion
	}
}

function reser_pedido(formulario)
{
	validacion = true
	formulario = document.form_reservas
	if (formulario.placa.value == "") 
	{
		window.alert("Digite una Placa")
		validacion = false
	}
	else if (formulario.num_regis.value == 0) 
	{
		window.alert("La Placa no esta Registrada o no esta Aprobada")
		validacion = false
	}
	else if (formulario.ciudad.value == 0) 
	{
		window.alert("Escoja una Ciudad")
		validacion = false
	}
	else 
	{
		if (confirm('Esta Seguro que Desea Ingresar la Reserva?')) 
		{
			formulario.opcion.value = 5
			formulario.submit()
			return validacion
		}
	}
}

function aceptar_delete(formulario)
{
	validacion = true
	formulario = document.form_vehiculos
	
	if (confirm('Esta Seguro que Desea Eliminar el Vehículo?')) 
	{
		formulario.opcion.value = 3
		formulario.submit()
		return validacion
	}
}

function actua_actua(formulario)
{
	validacion = true
	formulario = document.form_actua
	
	if (confirm('Esta Seguro que Desea Actualizar el Vehículo?')) 
	{
		formulario.opcion.value = 3
		formulario.submit()
		return validacion
	}
}

function confir(formulario)
{
	validacion = true
	formulario = document.form_confir
	if (formulario.num_regis.value == 0) 
	{
		window.alert("La Placa no esta Registrada o no esta Aprobada")
		validacion = false
	}
	else if (formulario.max_reserv.value == 0) 
	{
		window.alert("La Placa no tiene Reservas pendientes")
		validacion = false
	}
	else 
	{
		if (confirm('Esta Seguro que Desea Confirmar el Vehículo?')) 
		{
			formulario.opcion.value = 7
			formulario.submit()
			return validacion
		}
	}
}
