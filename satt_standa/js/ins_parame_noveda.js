/*! \file: ins_parame_noveda.js
 *  \brief: 
 *  \author: 
 *  \author: 
 *  \version: 
 *  \date: 04/04/2016
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

 
 /*! \fn: openTabs
 * \brief: Realiza peticion ajax para llenar los tab con el contenido especifico
 * \author: Edward Serrano
 * \date: 06/04/2017
 * \date modified: dia/mes/año
 * \param: tab: idenficador de tab
 * \return valor que retorna
 */
 function openTabs(tab)
 {
 	try {
	 	var standa = $("#standaID").val();
	 	var perfil = $("#perfilesID").val();
	 	var mdata="Ajax=on&opcion=getFormSoltie&tab="+tab+"&perfil="+perfil;
	 	$.ajax({
	 		url:"../"+ standa +"/noveda/ins_parame_noveda.php",
	 		type:"POST",
	 		data:mdata,
	 		cache:false,
	 		success:function(data){
	 			$("#resultDiv").html(data);
	 			$("#sec1").css({"height":"auto"});
	 		}		
	 	});
	}
	catch(err) 
	{
    	console.log("Error funcion openTabs:"+err.message);
	}
 }

 /*! \fn: opciones
 * \brief: 
 * \author: Edward Serrano
 * \date: 06/04/2017
 * \date modified: dia/mes/año
 * \param: tab: idenficador de tab
 * \return valor que retorna
 */
 function opciones(indicador, cod_noveda)
 {
 	try {
 		
 		if(indicador==1)
 		{
 			var standa = $("#standaID").val();
	      	closePopUp('popID');
	      	LoadPopupJQNoButton('open', 'EDITAR NOVEDAD ', ($(window).height() - 40), ($(window).width() - 40), false, false, true);
	      	var popup = $("#popID");
	      	var mdata = "Ajax=on&opcion=getFormIndi&indicador="+ indicador +"&cod_noveda="+cod_noveda;
	      	$.ajax({
	      		url: "../"+ standa + "/noveda/ins_parame_noveda.php",
	      		type: "POST",
	      		data: mdata,
	      		success:function(data){
	      			popup.html(data);
	      		}
	      	});
 		}
 		else if(indicador==2)
 		{
 			var standa = $("#standaID").val();
	      	closePopUp('popID');
	      	confirmGL("Desea Elimiar la parametrizacion del al noveda","inactivarNovedad("+cod_noveda+")");
 		}
 		
 	}
	catch(err) 
	{
    	console.log("Error funcion opciones:"+err.message);
	}
 }

 /*! \fn: checkAll
 * \brief: chequea todos los input del tab
 * \author: Edward Serrano
 * \date: 06/04/2017
 * \date modified: dia/mes/año
 * \param: 
 * \return valor que retorna
 */
 function checkAll()
 {
 	try {
 		var estadoCheck = null;
 		if( $("#SeleccionM").is(":checked") )
 		{
 			estadoCheck = 1;
 		}
 		else
 		{
 			estadoCheck = 0;
 		}
 		$("#secNovedadesP").find("input[type=checkbox]").each(function(key,value){
 			dato = $(this);
 			if(estadoCheck == 1)
 			{
 				dato.attr("checked", "checked");
 			}
 			else
 			{
 				dato.removeAttr("checked");
 			}
 		});
 	}
	catch(err) 
	{
    	console.log("Error funcion checkAll:"+err.message);
	}
 }

 /*! \fn: cerrarPopup
 * \brief: Cierra popup de los novedades individuales
 * \author: Edward Serrano
 * \date: 07/04/2017
 * \date modified: dia/mes/año
 * \param: 
 * \return 
 */
 function cerrarPopup()
 {
 	try
 	{
 		closePopUp('popID');
 	}
 	catch(err)
 	{
 		console.los("Error funcion cerrarPopup: "+ err.message);
 	}
 }

 /*! \fn: almacenarNovedad
 * \brief: catura formulario y envia para almacenar
 * \author: Edward Serrano
 * \date: 07/03/2017
 * \date modified: dia/mes/año
 * \param: 
 * \return 
 */
 function almacenarNovedad(indicador)
 {
 	try
 	{
 		var standa = $("#standaID").val();
 		var mdata  = "";
 		if(validarData(indicador)!=false)
 		{
 			
 			mdata = "Ajax=on&opcion=almacenarNovedad&" + validarData(indicador);
 			$.ajax({
 				url: "../"+standa+"/noveda/ins_parame_noveda.php",
 				type: "POST",
 				data: mdata,
 				success:function(data){
 					if(data=="ok")
 					{
 						mensaje("Parametrizacion de Novedad","La parametrizacion novedad ha sido almacenada.");
 						var tabActivo="";
 						$(".ind_tab").each(function(key,value){
					 		dato = $(this);
					 		if(dato.hasClass("ui-state-active"))
					 		{
					 			tabActivo = dato.attr("id");
					 		}
					 	});
					 	cerrarPopup();
 						openTabs(tabActivo);
 					}
 					else
 					{
 						mensaje("Error","Error al almacernar la parametrizacion de novedad.");
 					}
 				}
 			});
 		}
 	}
 	catch(err)
 	{
 		console.los("Error funcion almacenarNovedad: "+err.message);
 	}
 }

 /*! \fn: validarData
 * \brief: Validacion de formulario
 * \author: Edward Serrano
 * \date: 07/03/2017
 * \date modified: dia/mes/año
 * \param: 
 * \return datos a procesar
 */
 function validarData(indicador)
 {
 	try
 	{
 		if(indicador==1)
 		{
 			var cod_perfil = $("#perfilesID").val();
 			var ind_apsees = ($("#ind_apsees").is(":checked")?1:0);
 			var ind_tisees = ($("#ind_tisees").is(":checked")?1:0);
 			var num_minuto = ($("#num_minuto").val()!=""?$("#num_minuto").val():0);
 			var cod_noveda = "";
 			var atributos  = "";
 			$("#secNovedadesP").find("input[type=checkbox]").each(function(key,value){
		 		dato = $(this);
		 		if(dato.is(":checked"))
		 		{
		 			cod_noveda += dato.val()+",";
		 		}
		 	});
 		}
 		else if(indicador==2)
 		{
 			var cod_perfil = $("#perfilesID").val();
	 		var ind_apsees = ($("#ind_apseesIn").is(":checked")?1:0);
	 		var ind_tisees = ($("#ind_tiseesIn").is(":checked")?1:0);
	 		var num_minuto = $("#num_minutoIn").val();
	 		var cod_noveda = $("#cod_novedaIn").val();
	 	}

	 	if(cod_noveda != "")
		{
	 		if((ind_tisees==1 && num_minuto>0) || (ind_tisees==0))
	 		{
		 		atributos = "cod_perfil="+cod_perfil+"&ind_apsees="+ind_apsees+"&ind_tisees="+ind_tisees+"&cod_noveda="+cod_noveda+"&num_minuto="+num_minuto;
	 			return atributos;
	 		}
	 		else
	 		{
	 			mensaje("Parametrizacion de Novedad","los minutos deben ser mayor a 0");
	 			return false;
	 		}
		}
		else
		{
			mensaje("Parametrizacion de Novedad","debe seleccionar al menos una novedad");
	 		return false;
		}
 	}
 	catch(err)
 	{
 		console.los("Error funcion validarData: "+err.message);
 	}
 }

  /*! \fn: activarMinuto
 * \brief: Validacion de formulario
 * \author: Edward Serrano
 * \date: 10/03/2017
 * \date modified: dia/mes/año
 * \param: 
 * \return datos a procesar
 */
 function activarMinuto(indicador)
 {
 	try
 	{
 		if(indicador==1)
 		{
 			if($("#ind_tisees").is(":checked"))
 			{
 				$("#num_minuto").attr('disabled', false);
 			}
 			else
 			{
 				$("#num_minuto").attr('disabled', 'disabled');
 				$("#num_minuto").val('');
 			}
 			
 			
 		}
 		else if(indicador==2)
 		{
 			if($("#ind_tiseesIn").is(":checked"))
 			{
 				$("#num_minutoIn").attr('disabled', false);
 			}
 			else
 			{
 				$("#num_minutoIn").attr('disabled', 'disabled');
 				$("#num_minutoIn").val('');

 			}
	 	}
 	}
 	catch(err)
 	{
 		console.los("Error funcion validarData: "+err.message);
 	}
 }

  /*! \fn: activarMinuto
 * \brief: Validacion de formulario
 * \author: Edward Serrano
 * \date: 10/03/2017
 * \date modified: dia/mes/año
 * \param: 
 * \return datos a procesar
 */
 function cerrarTab()
 {
 	try
 	{
 		$("#resultDiv").html("");
 		$("#sec1").css({"height":"auto"});
 	}
 	catch(err)
 	{
 		console.los("Error funcion validarData: "+err.message);
 	}
 }

   /*! \fn: inactivarNovedad
 * \brief: Validacion de formulario
 * \author: Edward Serrano
 * \date: 10/03/2017
 * \date modified: dia/mes/año
 * \param: 
 * \return datos a procesar
 */
 function inactivarNovedad(cod_noveda)
 {
 	try
 	{
 		var standa = $("#standaID").val();
 		var cod_perfil = $("#perfilesID").val();
 		var mdata = "Ajax=on&opcion=inactivarNovedad&cod_noveda="+cod_noveda+"&cod_perfil="+cod_perfil;
 		$.ajax({
	    	url: "../"+ standa + "/noveda/ins_parame_noveda.php",
	    	type: "POST",
	    	data: mdata,
	    	success:function(data){
	    		if(data=="ok")
	    		{
	    			mensaje("Eliminar Novedad","La parametrizacion novedad ha sido eliminada.");
	    			var tabActivo="";
 					$(".ind_tab").each(function(key,value){
					 	dato = $(this);
					 	if(dato.hasClass("ui-state-active"))
					 	{
					 		tabActivo = dato.attr("id");
					 	}
					});
					cerrarPopup();
 					openTabs(tabActivo);
	    		}
	    		else
	    		{
	    			mensaje("Error","Error al eliminar la parametrizacion de novedad.");	
	    		}
	    	}
	    });
 	}
 	catch(err)
 	{
 		console.los("Error funcion validarData: "+err.message);
 	}
 }

 function mensaje(titulo,txt) {
  try {
    closePopUp('popupMensajeID');
    LoadPopupJQNoButton('open', titulo, 150, 300, false, false, false, 'popupMensajeID');
    var popup = $("#popupMensajeID");
    popup.parent().children().children('.ui-dialog-titlebar-close').hide();

    var msj = '<div style="text-align:center">' + txt + '<br><br><br><br>';
    msj += '<input type="button" name="no" id="noID" value="Cerrar" style="cursor:pointer" onclick="closePopUp(\'popupMensajeID\');" class="crmButton small save"/><div>';

    popup.append(msj);
  } catch (e) {
    console.log("Error Function confirmGL: " + e.message + "\nLine: " + e.lineNumber);
    return false;
  }
}