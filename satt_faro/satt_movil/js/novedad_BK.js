/*! \file: novedad.js
 *  \brief: js para novedad.js
 *  \author: 
 *  \author: 
 *  \version: 1.0
 *  \date: dia/mes/año
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

//Nos aseguramos que estén definidas
//algunas funciones básicas
window.URL = window.URL || window.webkitURL;
navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia ||
function() {
    alert('Su navegador no soporta navigator.getUserMedia().');
};

//Este objeto guardará algunos datos sobre la cámara
window.datosVideo = {
    'StreamVideo': null,
    'url': null
}

/*! \fn: justNumbers
 *  \brief: Valida que solo se escriban numeros en los input
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function justNumbers(e)
{
	var keynum = window.event ? window.event.keyCode : e.which;
	if ((keynum == 8) || (keynum == 46))
	return true;

	return /\d/.test(String.fromCharCode(keynum));
}

/*! \fn: validar
 *  \brief: Valida los campos con *
 *  \author: 
 *	\date: dia/mes/año
 *	\date modified: 25/05/2015
 *  \param: 
 *  \return:
 */
function validar()
{
	var placa = $("#placa");
	var form = $("#form");
	var option = $("#option");
	var foto1 = $("#img_foto01ID");
	
	if( !placa.val() ){
		alert( "La placa es requerida" );
		return placa.focus();
	}
	else if( !foto1.val() ){
		alert("La Foto del Conductor es Requerida");
		return false;
	}
	else if( confirm( "¿ Esta seguro de enviar la informacion ?" ) ) {
		option.val("1");
		form.submit();
	}
	else
	{
		return false;
	}
}

/*! \fn: showCamara
 *  \brief: Muestra Popup para tomar fotos
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: num_foto
 *  \return:
 */
function showCamara(num_fotoxx)
{
	try{
		var atributes  = 'Ajax=on&Case=showCamara';
			atributes += '&num_fotoxx='+num_fotoxx;

		//Carga Popup
		LoadPopup('open');
		$.ajax({
			url: "../satt_movil/ajax_novedad.php",
			type: "POST",
			data: atributes,
			beforeSend: function(){
				$("#FormContacID").html("<center>Cargando Formulario...</center>");
			},
			success: function(data){
				$("#FormContacID").html(data);
				CenterDIV();
			}
		});
	}
	catch(e)
	{
		console.log( "Error Función showCamara: "+e.message+"\nLine: "+e.lineNumber );
    	return false;
	}
}

/*! \fn: LoadPopup
 *  \brief: Crea el PopUp
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param:
 *  \return:
 */
function LoadPopup( type )
{
	try
	{
		if(type == 'open')
		{

			$('<div id="FormContacID"><center>Cargando...</center></div>').dialog({
				width : 100,
				heigth: 50,
				modal: true,
				closeOnEscape: false,
				resizable: false,
				draggable: false,
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
		console.log("Error Función LoadPopup: "+e.message+"\nLine: "+e.lineNumber);
	}
}

/*! \fn: CenterDiv
 *  \brief: Centra el PopUP
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function CenterDIV()
{
	var WindowH = $(window).width();
	var popupH =  $('.ui-dialog').outerWidth();
	var Left = ((WindowH - popupH) / 2);
	$(".ui-dialog").css({"width": ($(window).width() - 8 )+"px" , "left":"0px", top : "200px"});
}

/*! \fn: Inicam
 *  \brief: Solicita permisos al navegador para encender la camara
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function Inicam(obj)
{
	try
	{
		navigator.getUserMedia({
			'audio': false,
			'video': true
		}, function(streamVideo) {
			datosVideo.StreamVideo = streamVideo;
			datosVideo.url = window.URL.createObjectURL(streamVideo);
			$('#camara').attr('src', datosVideo.url);
		}, function() {
			alert('No fue posible obtener acceso a la cámara.');
		});
	}
	catch(e)
	{
		console.log( "Error Fuction Inicam: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

/*! \fn: Detcam
 *  \brief: Apaga la camara
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function Detcam()
{
	try
	{
		if (datosVideo.StreamVideo) {
			datosVideo.StreamVideo.stop();
			window.URL.revokeObjectURL(datosVideo.url);
		}
	}
	catch(e)
	{
		console.log( "Error Fuction Detcam: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

/*! \fn: Fotocam
 *  \brief: Toma Foto
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function Fotocam()
{
	try
	{
		var oCamara, oFoto, oContexto, w, h;

		oCamara = $('#camara');
		oFoto = $('#foto');
		w = oCamara.width();
		h = oCamara.height();
		oFoto.attr({
			'width': w,
			'height': h
		});
		oContexto = oFoto[0].getContext('2d');
		oContexto.drawImage(oCamara[0], 0, 0, w, h);
	}
	catch(e)
	{
		console.log( "Error Fuction Fotocam: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

/*! \fn: download
 *  \brief: 
 *  \author: 
 *	\date: dia/mes/año
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function download()
{
	mydata=mycanvas.toDataURL();
	mydata=mydata.replace("image/png",'image/octet-stream');
	document.location.href =mydata;
}

/*! \fn: guardarFoto
 *  \brief: Guarda la foto en un input del formulario
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2025
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function guardarFoto()
{
	try
	{
		var canvasFoto = $("#foto");
		var foto = canvasFoto[0].toDataURL("image/jpeg", 0.3); // obtenemos la imagen como jpeg
		var num_fotoxx = $("#num_fotoxxID").val();

		if(num_fotoxx == 1)
			$("#img_foto01ID").val(foto);
		else if(num_fotoxx == 2)
			$("#img_foto02ID").val(foto);

		LoadPopup('close');
	}
	catch(e)
	{
		console.log( "Error Fuction guardarFoto: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

/*! \fn: showDespacRecome
 *  \brief: Muestra las recomendaciones por solucionar del despacho(s)
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2025
 *	\date modified: dia/mes/año
 *  \param: NumDespac
 *  \return:
 */
function showDespacRecome(NumDespac)
{
	try
	{
		var atributes  = 'Ajax=on&Case=SoluciRecome';

		var mNumDespac = NumDespac.split(",");
		for (var i = 0; i < mNumDespac.length; i++) {
			atributes += '&num_despac['+i+']=' + mNumDespac[i];
		};
		
		//Carga PopUp
		LoadPopup('open');
		$.ajax({
			url: "./ajax_novedad.php",
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
				CenterDIV();
			}
		});
	}
	catch(e)
	{
		console.log( "Error Función showDespacRecome: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

/*! \fn: setSoluciReocme
 *  \brief: Envia la solucion a las recomendaciones
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
function setSoluciRecome()
{
	try
	{
		var formulario = $("#formSoluciRecomeID");
		var codsRecome = $("#cod_recomeID").val();
		var numsDespac = $("#num_despacID").val();
		var param = "";
		var cod_recome = "";
		var cod_contro = "";
		var obs_ejecuc = "";
		var cod_tipoxx = "";
		var ind_requer = "";
		var valida = true;

		codsRecome = codsRecome.split('|');
		numsDespac = numsDespac.split('|');

		for (var i = 0; i < numsDespac.length; i++) {
			param += '&num_despac['+i+']=' + numsDespac[i];
		};

		for (var i = 0; i < codsRecome.length; i++) 
		{

			cod_recome = $("#InputsForm"+i+"ID").attr("cod_recome");
			cod_contro = $("#InputsForm"+i+"ID").attr("cod_contro");
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
			param += "&data["+i+"][cod_recome]=" + cod_recome + "&data["+i+"][obs_ejecuc]=" + obs_ejecuc ; 
			param += "&data["+i+"][cod_contro]=" + cod_contro ; 
		};


		if(valida == true)
		{
			var atributes  = 'Ajax=on&Case=UpdSoluciRecome';
			atributes += param;

			$.ajax({
				url: "./ajax_novedad.php",
				type: "POST", 
				data: atributes,
				async: false,
				complete: function(){
					LoadPopup('close');
					alert("Se ha Guardo la Solucion a la Recomendacion.")
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