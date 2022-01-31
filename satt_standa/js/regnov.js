function busq_serv()
{
	validacion = true;
	formulario = document.form_ins;

	if(formulario.placa.value == "" && formulario.cod_manifi.value == "")
	{
		window.alert("Debe Digitar el numero de la placa o el numero de manifiesto.");
		validacion = false;
		return formulario.placa.focus();
	}
	else 
	{
		formulario.opcion.value = 3;
		formulario.submit();
	}
}

//Nos aseguramos que estén definidas
//algunas funciones básicas
window.URL = window.URL || window.webkitURL;
navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia ||
function() {
    alert('Su navegador no soporta navigator.getUserMedia().');
};

window.datosVideo = {
    'StreamVideo': null,
    'url': null
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
  
		$("[name=bin_fotcon]").val(foto); 
		$("#fot_actual").attr("src",foto); 

		LoadPopupRegnov('close');
	}
	catch(e)
	{
		console.log( "Error Fuction guardarFoto: "+e.message+"\nLine: "+e.lineNumber );
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
/*! \fn: showDialog
 *  \brief: Muestra Popup para tomar fotos
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param: num_foto
 *  \return:
 */
function showDialog(num_fotoxx)
{
	var standa = $("[name=standa]").val();
	try{
		var atributes  = 'AJAX=on&op=showCamara'; 

		//Carga Popup
		LoadPopupRegnov('open');
		$.ajax({
			url: "../"+standa+"/despac/ajax_fotos.php",
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
		console.log( "Error Función showDialog: "+e.message+"\nLine: "+e.lineNumber );
    	return false;
	}
}

/*! \fn: LoadPopupRegnov
 *  \brief: Crea el PopUp
 *  \author: Ing. Fabian Salinas
 *	\date: 25/05/2015
 *	\date modified: dia/mes/año
 *  \param:
 *  \return:
 */
function LoadPopupRegnov( type )
{
	try
	{
		if(type == 'open')
		{

			$('<div id="FormContacID"><center>Cargando...</center></div>').dialog({
				width : 100,
				heigth: 500,
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

function aceptar_ins(formulario)
{
	validacion = true;
	formulario = document.form_ins;
	var fec = new Date();
	var date = document.getElementById("date");
	var hora = document.getElementById("hora");
	var obs = document.getElementById("obsID");
	var nov_especi = document.getElementById("nov_especiID");
	var ano= (date.value).substr(0,4);
	var mes= +(date.value).substr(5,2)-1;
	var dia= (date.value).substr(8,2);
	var hor= (hora.value).substr(0,2);
	var min= (hora.value).substr(3,2);
	var fecnov= new Date(ano,mes,dia,hor,min,"00");

	if(obs.value == ""){
		window.alert("La Observacion es Requerida")
		validacion = false
		return obs.focus();
	}else if(obs.value.length < 9 ) {
		window.alert("La Observacion Requiere minimo 9 caracteres")
		validacion = false
		return obs.focus();
	}

	if(date.value==""){
		window.alert("La Fecha de la Novedad es Requerida")
		validacion = false
		return date.focus();
	}
	if(hora.value==""){
		window.alert("La Hora de la Novedad es Requerida")
		validacion = false
		return hora.focus();
	}

	if ( (date.value+' '+hora.value) > document.getElementById('dateSystemID').value)
	{
		alert ("La Fecha y Hora de la  Novedad no Puede ser Mayor a la Fecha Actual");
		return date.focus();
	}
	if (formulario.contro.value == 0) 
	{
		window.alert("El Puesto de Control es Requerido")
		validacion = false
		formulario.contro.focus()
	}
	if (formulario.novedad.value == 0) 
	{
		window.alert("La Novedad es Requerida")
		validacion = false
		formulario.novedad.focus()
	}
	if( formulario.novedad.value == '6' )
	{
		var d = formulario.fecnov.value.split("-");
		var fecact = d[1] +'/'+ d[2] +'/'+ d[0] +' '+ formulario.hornov.value;
		var fecact = new Date(fecact);

		d = formulario.fecha.value.split("-");
		var tiesol = d[1] +'/'+ d[2] +'/'+ d[0] +' '+ formulario.hor.value +':00';
		var tiesol = new Date(tiesol);

		var diff = Math.floor((tiesol-fecact)/60000);

		if( diff > 720 ){
			alert("El Tiempo Solicitado no debe ser mayor a 12 horas.");
			return false;
		}
	}
	
	if (formulario.soltie.value == 1) 
	{
		var date = document.getElementById("fecha");
		var hora = document.getElementById("hor");
		var ano= (date.value).substr(0,4);
		var mes= +(date.value).substr(5,2)-1;
		var dia= (date.value).substr(8,2);
		var hor= (hora.value).substr(0,2);
		var min= (hora.value).substr(3,2);
		var fecnov= new Date(ano,mes,dia,hor,min,"00");
		if(date.value==""){
			window.alert("La Fecha del Tiempo Adicional es Requerida")
			validacion = false
			return date.focus();
		}
		if(hora.value==""){
			window.alert("La Hora del Tiempo Adicional es Requerida")
			validacion = false
			return hora.focus();
		}
		if (date.value < document.getElementById('dateSystemID').value) 
		{
			alert ("La Fecha y Hora Solicitado no Puede ser Menor a la Fecha Actual");
			return date.focus();
		}
	}
	if (nov_especi.value==1)
	{
		if (obs.value=="")
		{
			alert ("La Observacion es Requerida");
			return obs.focus();
		}
	}
	if(validacion=true){
		if (confirm("Desea Ingresar la Novedad al Sistema.")) 
		{
			formulario.opcion.value = 2;
			formulario.submit();
		}
	}else{
	}
}

function validateNoveda()
{
	try{
		if( $("#novedadID").val() == '6' ){
			LoadPopupJQ( 'open', 'PROTOCOLO DE PERNOCTACION EAL', 'auto', 'auto', false, false, false );
			$("#popID").html( builderHtml() );
		}
	}
	catch(e)
	{
		console.log( "Error Function validateNoveda: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function builderHtml()
{
	try{
		var a = "";

		a += "";
		a += "<style>";
		a += 	".cellHead {";
		a += 		"background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #00660f 0%, #00660f 100%) repeat scroll 0 0;";
		a += 		"color: #fff;";
		a += 		"padding: 5px 10px;";
		a += 		"text-align: center;";
		a += 	"}";
		a += 	".cellInfo1 {";
		a += 		"background-color: #ebf8e2;";
		a += 		"font-family: Trebuchet MS,Verdana,Arial;";
		a += 		"font-size: 11px;";
		a += 		"padding: 2px;";
		a += 	"}";
		a += 	".Style2DIV {";
		a += 		"background-color: rgb(240, 240, 240);";
		a += 		"border: 1px solid rgb(201, 201, 201);";
		a += 		"border-radius: 5px;";
		a += 		"min-height: 50px;";
		a += 		"padding: 5px;";
		a += 		"width: 99%;";
		a += 	"}";
		a += "</style>";

		a += "<div class='Style2DIV'>";
		a += 	"<table width='100%' cellspacing='0' cellpadding='0'>";
		a += 		"<tr>";
		a += 			"<th class='CellHead' colspan='2'>&nbsp;</th>";
		a += 		"</tr>";
		a += 		"<tr>";
		a += 			"<td class='cellInfo1' align='right'>* Parqueadero:</td>";
		a += 			"<td class='cellInfo1' align='left'><input type='text' name='nom_parque' id='nom_parqueID' /></td>";
		a += 		"</tr>";
		a += 		"<tr>";
		a += 			"<td class='cellInfo1' align='right'>Hotel:</td>";
		a += 			"<td class='cellInfo1' align='left'><input type='text' name='nom_hotelx' id='nom_hotelxID' /></td>";
		a += 		"</tr>";
		a += 		"<tr>";
		a += 			"<td class='cellInfo1' align='right'>* Hora de reinicio:</td>";
		a += 			"<td class='cellInfo1' align='left'><input type='text' name='hor_reinic' id='hor_reinicID' /></td>";
		a += 		"</tr>";
		a += 	"</table>";
		a +=	"<input type='button' value='Aceptar' onClick='validateProtocolo()' />";
		a += "</div>";

		return a;
	}
	catch(e)
	{
		console.log( "Error Function builderHtml: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

function validateProtocolo()
{
	try{
		var parque = $("#nom_parqueID");
		var hotelx = $("#nom_hotelxID");
		var reinic = $("#hor_reinicID");
		var txt = '';

		if( parque.val() == '' ){
			alert("El nombre del parqueadero es obligatorio.");
			parque.focus();
			return false;
		}else if( reinic.val() == '' ){
			alert("La hora de reinicio es obligatoria.");
			reinic.focus();
			return false;
		}else{
			txt += 'EL CONDUCTOR REPORTA PERNOCTACION EN LA EAL PARQUEDERO '+ parque.val().toUpperCase() +'. ';
		}

		if( hotelx.val() != '' )
			txt += 'HOTEL '+ hotelx.val().toUpperCase() +'. ';

		txt += 'HORA DE REINICIO '+ reinic.val().toUpperCase() +'. ';

		LoadPopupJQ('close');
		$("#obsID").val(txt);
		$("#form_insID").submit();
	}
	catch(e)
	{
		console.log( "Error Function validateProtocolo: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}

/*! \fn: LoadPopupJQ
 *  \brief: Crea o destruye PopUp
 *  \author: Ing. Fabian Salinas
 *	\date: 24/06/2015
 *	\date modified: dia/mes/año
 *  \param: opcion   String   open, close
 *  \param: titulo   String   Titulo del PopUp
 *  \param: alto   	 Integer  Altura PopUp
 *  \param: ancho    Integer  Ancho PopUp
 *  \param: redimen  Boolean  True = Redimencionable
 *  \param: dragg    Boolean  True = El PopUp se puede arrastras
 *  \param: lockBack Boolean  True = Bloquea el BackGround
 *  \return: 
 */
function LoadPopupJQ( opcion, titulo, alto, ancho, redimen, dragg, lockBack )
{
	try
	{
		if( opcion == 'close' ){
			$("#popID").dialog("destroy").remove();
		}else{
			$("<div id='popID' name='pop' />").dialog({
				height: alto, 
				width: ancho, 
				modal: lockBack,
				title: titulo, 
				closeOnEscape: false, 
				resizable: redimen, 
				draggable: dragg,
				buttons: {
					Cerrar: function(){ LoadPopupJQ('close') }
				}
			});
		}	
	}
	catch(e)
	{
		console.log( "Error Function LoadPopupJQ: "+e.message+"\nLine: "+e.lineNumber );
		return false;
	}
}




function transporSusp(valor, tipo){
    try{
        var standa = 'satt_standa';
        var factu = '';

        $.ajax({
            url: "../" + standa + "/despac/ajax_regist_noveda.php",
            type: "post",
            dataType: "json",
            data: {valor: valor, tipo: tipo, opcion: 'nitEmpresa'},
            success: function(codTercer) {
            	if(codTercer == 'null' || codTercer['cod_transp'].length > 0){
            		var text = codTercer['nom_tercer'];
            		$.ajax({
			            url: "../" + standa + "/lib/general/suspensiones.php",
			            type: "post",
			            dataType: "json",
			            data: {cod_tercer: codTercer['cod_transp']},
			            success: function(data) {
			                $.each(data['suspendido'], function(estado, arrayDatos) {
			                    if(valor == arrayDatos['cod_tercer']){
			                    	if(factu == ''){
			                    		factu = arrayDatos['num_factur'];
			                    	}else{
			                    		factu += ", "+arrayDatos['num_factur'];
			                    	}
			                        
			                    }
			                });
			                Swal.fire({
			                  title:'Suspension!',
			                  html: 'La empresa <b>' +text+ '</b> con el nit <b>' +codTercer['cod_transp']+ '</b> no se le puede registrar el reporte ya se encuetra suspendida por cartera.',
			                  type: 'info',
			                  confirmButtonColor: '#336600',
			                  confirmButtonText: 'Listo'
			                }).then((result) => {
			                    if (result.value) {
			                        $('input[name ="placa"]').val("");
			                        $('input[name ="cod_manifi"]').val("");
			                    }
			                });

			                return false;
			          	}
			        });
            	}
          	}
        });        
    }catch(e){
        alert( "Error " + e.message );
    }
}
