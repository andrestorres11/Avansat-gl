$(document).ready(function(){

	var standa = $("[name=standa]").val(); 

	$( "[name=num_docume]" ).autocomplete({
		source: "../"+standa+"/ctrapp/ajax_insapl_movilx.php?op=buscarConductor",
		minLength: 3, 
		delay: 100,
		select: function(event, ui){ 

			$.ajax({
				url:"../"+standa+"/ctrapp/ajax_insapl_movilx.php?op=datosConductor",
				data:{"cod_tercer":ui.item.value},
				success: function(result){
					data = JSON.parse(result);
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


function guardar(){ 
	var standa = $("[name=standa]").val(); 
	var cod_tercer = $("[name=num_docume]").val();
	var cod_usuari = $("[name=nom_usrapp]").val();
	var cod_hashxx = $("[name=cod_seriex]").val();
	var ind_activo = $("#cod_estadoID").val();
	var ind_admini = $("#ind_adminiID").val();
	var nit_transpor = $("#nit_transpor").val();
	var mail = $("[name=nom_emailx]").val();
  
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
						"mail": mail
					 },
				success: function(result){
					result=result.replace("\n","");
					console.log("-"+result+"-");
					if(result=="ok"){
						alert("Se ha registrado el usuario Exitosamente");
						document.forms[0].reset();
					}
					else{
						alert("ha ocurrido un error en el registro del usuario\npor favor intente mas tarde");
					}

				}
			});
	}
	else{
		alert("le han faltado campos por llenar\npor favor verifique\n"+message);
	}


}