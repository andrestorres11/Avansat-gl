$(document).ready(function(){

	var standa = $("[name=standa]").val(); 
	$("#ind_adminiID").change(function() {
		var id = $(this).children(":selected").val();
		var transp = $('#cod_transpID').val();
		var op = 'datosConductor';
		var busq = 'buscarConductor';

		if(id==4){
			op = 'datosHojadeVidaCT';
			busq = 'buscarAsistente';
		}

		$( "[name=num_docume]" ).autocomplete({
			source: "../"+standa+"/ctrapp/ajax_insapl_movilx.php?op="+busq+"&activity="+id+"&nit_transp="+transp,
			minLength: 3, 
			delay: 100,
			select: function(event, ui){ 
				$.ajax({
					url:"../"+standa+"/ctrapp/ajax_insapl_movilx.php?op="+op,
					data:{"cod_tercer":ui.item.value},
					success: function(result){
						data = JSON.parse(result);
						console.log(data);
						$("[name=tip_person]").val(data.cod_tipper);
						$("[name=tip_docume]").val(data.cod_tipdoc);
						$("[name=nom_usuari]").val(data.nom_tercer);
						$("[name=nom_appel1]").val(data.nom_apell1);
						$("[name=nom_appel2]").val(data.nom_apell2); 
						$("[name=num_telef1]").val(data.num_telef1);
						$("[name=num_telef2]").val(data.num_telef2);
						$("[name=num_movilx]").val(data.num_telmov);   
						$("[name=num_direcc]").val(data.dir_domici); 
						$("[name=nom_emailx]").val(data.dir_emailx); 
						$("[name=cod_seriex]").val(data.cod_hashxx); 
	
					}
				});
			}
		});
	});

	$( "#num_documeID" ).change(function() {
		$("[name=tip_person]").val('');
		$("[name=tip_docume]").val('');
		$("[name=nom_usuari]").val('');
		$("[name=nom_appel1]").val('');
		$("[name=nom_appel2]").val(''); 
		$("[name=num_telef1]").val('');
		$("[name=num_telef2]").val('');
		$("[name=num_movilx]").val('');   
		$("[name=num_direcc]").val(''); 
		$("[name=nom_emailx]").val(''); 
		$("[name=cod_seriex]").val(''); 
	});

	$("#nom_usrappID").change(function() {
		var standa = $("[name=standa]").val(); 
		var nom_usrapp = $("[name=nom_usrapp]").val();
		var cod_transp = $("[name=cod_transp]").val();

		$.ajax({
			url:"../"+standa+"/ctrapp/ajax_insapl_movilx.php?op=validarUsuario",
			data:{
					"cod_usuari":nom_usrapp,
					"cod_transp":cod_transp,
				 },
			beforeSend: function() {
				Swal.fire({
					title: 'Cargando',
					text: 'Por favor espere...',
					imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
					imageAlt: 'Custom image',
					showConfirmButton: false,
				});
			},
			success: function(result){
				dataF = JSON.parse(result);
				
				if(dataF['response'] == "ok"){
					msn = "Usuario Disponible";

					Swal.fire({
						title: 'Correcto!',
						text: msn,
						type: 'success',
						confirmButtonColor: '#336600'
					});
				}
				else{
					msn = dataF['error'];
					$("[name=nom_usrapp]").val('');

					Swal.fire({
						title: 'Error!',
						text: msn,
						type: 'error',
						confirmButtonColor: '#336600'
					});
				}

				
			}
		});

	});
	
	
	// validacion para saber si es un usuaraio administrador o de una transportadora y mostrar los datos de la misma
	var total = $("#total").val();

	if (total == 1) {
		mostrar();
	} else {
		$("#datos").css("display", "none");
	}
	
	//Autocompletables
	var Standa = $("#standaID").val();
	var attributes = '&Ajax=on&standa=' + Standa;
	var boton = "";
	$("#nom_transpID").autocomplete({
		source: "../" + Standa + "/transp/ajax_transp_transp.php?Option=buscarTransportadora" + attributes,
		minLength: 3,
		select: function(event, ui) {
			boton = "<input type='button' id='nuevo' value='Listado' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all' onclick='mostrar();'>";
			$("#cod_tercerID").val(ui.item.id);
			$("#boton").empty();
			$("#boton").append(boton);
			$("body").removeAttr("class");
		}
	});
      
});


function guardar(action){
	var standa = $("[name=standa]").val(); 
	var nom_usuari =  $("[name=nom_usuari]").val();
	var nom_appel1 = $("[nom_appel1]").val();
	var cod_tercer = $("[name=num_docume]").val();
	var cod_usuari = $("[name=nom_usrapp]").val();
	var cod_hashxx = $("[name=cod_seriex]").val();
	var num_telef1 = $("[num_telef1]").val();
	var ind_activo = $("#cod_estadoID").val();
	var ind_admini = $("#ind_adminiID").val();
	var nit_transpor = $("#nit_transpor").val();
	var mail = $("[name=nom_emailx]").val();
	var cod_transp = $("[name=cod_transp]").val();
  	var mailregexp = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i 
	var message = "";
	var flag = true;

	if(cod_tercer == ''){
		message += "\n-Numero de Documento";
		flag = false;
	}	
	if(cod_usuari == ''){
		message += "\n-Usuario a Generar";
		flag = false;
	}	
	if(cod_hashxx == ''){
		message += "\n-Serie";
		flag = false;
	}	
	if(ind_activo == ''){
		message += "\n-Estado";
		flag = false;
	}	
	if(ind_admini == ''){
		message += "\n-Tipo de Usuario";
		flag = false;
	}	
	if(mail == '')
	{
		message += "\n-E-mail";
		flag = false;
	}
	if( !mailregexp.test( mail ) )
	{
		message += "\n-Verificar el formato del E-mail";
		flag = false;
	}
	if(flag == true){
			$.ajax({
				url:"../"+standa+"/ctrapp/ajax_insapl_movilx.php?op=guardarUsuario",
				data:{
						"cod_tercer":cod_tercer,
						"cod_usuari":cod_usuari,
						"cod_hashxx":cod_hashxx,
						"ind_activo":ind_activo,
						"ind_admini":ind_admini,
						"nit_transpor": nit_transpor,
						"mail": mail,
						"cod_transp": cod_transp,
						"nom_usuari": nom_usuari,
						"nom_appel1": nom_appel1,
						"num_telef1": num_telef1,
					 },
				beforeSend: function() {
					Swal.fire({
						title: 'Cargando',
						text: 'Por favor espere...',
						imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
						imageAlt: 'Custom image',
						showConfirmButton: false,
					})
				},
				success: function(result){
					dataF = JSON.parse(result);
					
					if(dataF['response'] == "ok"){
						msn = "Se ha registrado el usuario Exitosamente";
						document.forms[0].reset();

						Swal.fire({
							title: 'Correcto!',
							text: msn,
							type: 'success',
							confirmButtonColor: '#336600'
						});
					}
					else{
						msn = dataF['error'];

						Swal.fire({
							title: 'Error!',
							text: msn,
							type: 'error',
							confirmButtonColor: '#336600'
						});
					}

				}
			});
	}
	else{
		msn = "le han faltado campos por llenar\npor favor verifique\n"+message;

		Swal.fire({
			title: 'Error!',
			text: msn,
			type: 'error',
			confirmButtonColor: '#336600'
		})
	}


}


function mostrar() {
	$("#form3").empty();
	var transp = $("#cod_tercerID").val();
	var standa = $("#standaID").val();
	var parametros = "op=listaUsuariosMoviles&Ajax=on&cod_transp=" + transp;
	$.ajax({
		url: "../" + standa + "/ctrapp/ajax_insapl_movilx.php",
		type: "POST",
		data: parametros,
		async: false,
		
		success: function(data) {
			$("#sec1").css("height", "auto");
			$("#sec2").css("height", "auto");
			$("#form3").append(data); // pinta los datos de la consulta
			unCyfre();
		}
	});
	$("#datos").fadeIn(3000); // visualza los datos despues de pintarlos
}


function unCyfre()
{
	$(".DLTable").find("tr").each(function (i,v){
        if($(this).children().attr("tagName").toLowerCase()=="td")
        {
            contra = $(this).children().eq(7).html();
            //contra = Base64.decode(contra);
            $(this).children().eq(7).html( atob(contra) );
        }
    });     
}


function editarUsuarioMovil(tipo, objeto)
{
	var DLRow = $(objeto).parent().parent();
	var cod_tercer = DLRow.find("input[id^=cod_tercer]").val();
	var nom_tercer = DLRow.find("input[id^=nom_tercer]").val();
	$("#conductor").val(cod_tercer);

	if (tipo == 1) {
		confirmar('activarUsuario', DLRow);
	} else if (tipo == 2) {
		confirmar('inactivarUsuario', DLRow);
	} else {
		LoadPopupJQNoButton('open', 'Confirmar Operacion', 'auto', 'auto', false, false, true);
		var popup = $("#popID");
		var conductor = $("#nom_tercerID").val();
		var msj = "<div style='text-align:center'>Esta seguro de <b>editar</b> el usuario: <b>" + nom_tercer + "?</b><br><br><br><br>";
		msj += "<input type='button' name='si' id='siID' value='Si' style='cursor:pointer' onclick='formulario()' class='crmButton small save'/> &nbsp;&nbsp;&nbsp;&nbsp";
		msj += "<input type='button' name='no' id='noID' value='No' style='cursor:pointer' onclick='closePopUp()' class='crmButton small save'/><div>";

		popup.parent().children().children('.ui-dialog-titlebar-close').hide();
		popup.append(msj); // //lanza el popUp
	}
}


function Guardar(action)
{
	if(action == "reset")
	{
		RestablecerUsuario();
	}
	else if(action == "forward")
	{
		window.location.href ="index.php?window=central&cod_servic="+$("#cod_servicID").val()+"&menant="+$("#cod_servicID").val();
	}
	else
	{
		var mail = $("#dir_emailxID");
		mailregexp = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i 

		if(mail.val() == '')
		{
			alert("Digite un correo para el conductor!");
			mail.focus();
			return false;
		}

		if( !mailregexp.test( mail.val() ) )
		{
			alert("Verificar el formato del correo del usuario!");
			mail.focus();
			return false;
		}

		$("#opcion").val( "2" );
		$("#action").val( action );
		$("form").submit();
	}

}

function confirmar(action, obj)
{
	var cod_tercer = obj.find("input[id^=cod_tercer]").val();
	var nom_tercer = obj.find("input[id^=nom_tercer]").val();
	var cod_transp = $("[name=cod_transp]").val();
	var standa = $("#standaID").val();
	var parametros = "op=incativarUsuario&Ajax=on&cod_tercer=" + cod_tercer + "&action=" + action+"&cod_transp="+cod_transp;
	$.ajax({
		url: "../" + standa + "/ctrapp/ajax_insapl_movilx.php",
		type: "POST",
		data: parametros,
		async: false,
		beforeSend: function() {
			Swal.fire({
				title: 'Cargando',
				text: 'Por favor espere...',
				imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
				imageAlt: 'Custom image',
				showConfirmButton: false,
			})
		},
		success: function(data) {

			msn = "El usuario" + nom_tercer + " ha sido "+(action=='activarUsuario'?"activado":"inactivado");
			Swal.fire({
				title: 'Estado Usuario!',
				text: msn,
				type: 'success',
				confirmButtonColor: '#336600'
			}).then((result) => {
				if (result.value) {
					location.reload();
				}
			});
			
		}
	});
}

function CerrarPopuUS()
{	
	closePopUp();
	mostrar();
}

function RestablecerUsuario()
{
	var standa = $("#standaID").val();
	var formRest = getDataFormRest();
	var parametros = "op=RestablecerUsuario&Ajax=on"+formRest;
	$.ajax({
		url: "../" + standa + "/ctrapp/ajax_insapl_movilx.php",
		type: "POST",
		data: parametros,
		async: false,
		beforeSend: function() {
			Swal.fire({
				title: 'Cargando',
				text: 'Por favor espere...',
				imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
				imageAlt: 'Custom image',
				showConfirmButton: false,
			})
		},
		success: function(data) {
			dataF = JSON.parse(data);
			if(dataF['response'] == "ok")
			{
				msn = "Se ha re establecido correctamente la contraseña.";
				Swal.fire({
					title: 'Correcto!',
					text: msn,
					type: 'success',
					confirmButtonColor: '#336600'
				});
			}
			else
			{
				msn = dataF['error'];

				Swal.fire({
					title: 'Error!',
					text: msn,
					type: 'error',
					confirmButtonColor: '#336600'
				});
			}
		}
	});
}

function getDataFormRest()
{
	var mData = "";
	var Validate = true;
	//validacion del formato mail
	var mail = $("#dir_emailxID");
	mailregexp = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i 
	if(mail.val() == '')
	{
		alert("Digite un correo para el conductor!");
		mail.focus();
		return false;
	}
	if( !mailregexp.test( mail.val() ) )
	{
		alert("Verificar el formato del correo del usuario!");
		mail.focus();
		return false;
	}
	mData +="&mail=" + $("#dir_emailxID").val();
	$("#form1").find("input[type=hidden]").each(function(i, v){
		mData +="&"+[$(this).attr("name")] + "=" + $(this).val();
	});
	//console.log(mData);
	return mData;
}

function NuevoUsuario()
{
	$("#opcionID").val( "3" );
	$("form").submit();
}
