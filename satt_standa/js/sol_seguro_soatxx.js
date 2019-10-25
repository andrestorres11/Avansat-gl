$(function(){
	//Adicionar atributo multiple a Files
	$("#tar_propieID").attr("multiple", "multiple");

	//Valida campos necesarios
	loadFieldsPlacax();

	//Limpia campo correo indicando que la empresa pagará el SOAT
	$("#pag_empresID").on("click", function(){
		if($(this).is(':checked')){
			$("#dir_emailxID").val("");
		}
	});
});

/*! \fn: loadFilesAut
  *  \brief: Funcion que genera el autocomplete en la placa y llene la cantidad de campos
  *  \author: Ing. Luis Manrique
  *  \date: 23/10/2019
  *  \return n/a
  */ 

function loadFieldsPlacax()
{
	var standa = $("#standarID").val();
	var parametros = "Option=loadFieldsPlacax&Ajax=on";
	$("#num_placaxID").autocomplete({
      source: "../" + standa + "/segurm/sol_seguro_soatxx.php?"+parametros,
      minLength: 2,
      delay: 100,
      select: function(event, ui) {
        $(this).val(ui.item.value);
        $("#ano_modeloID").val(ui.item.ano_modelo);
        $("#nom_marcaxID").val(ui.item.nom_marcax);
        $("#nom_lineaxID").val(ui.item.nom_lineax);
        $("#num_motorxID").val(ui.item.num_motorx);
        $("#num_seriexID").val(ui.item.num_seriex);
        $("#cod_tercerID").val(ui.item.cod_propie);
        $("#nom_tercerID").val(ui.item.nom_tercer);
        $("#nom_apell1ID").val(ui.item.nom_apell1);
        $("#nom_apell2ID").val(ui.item.nom_apell2);
        $("#dir_domiciID").val(ui.item.dir_domici);
        $("#num_telmovID").val(ui.item.num_telmov);
        $("#dir_emailxID").val(ui.item.dir_emailx);
      }
  	});

  	$("#num_placaxID").on("change", function(){
		if($(this).val() == ''){
			$("#frm_solSoatID input:not([type='button']), #frm_solSoatID select, #frm_solSoatID textarea").each(function(){
				$(this).val("");
			});
		}
	});
};

/*! \fn: validateFields
  *  \brief: Funcion valida capos requeridos
  *  \author: Ing. Luis Manrique
  *  \date: 23/10/2019
  *  \return n/a
  */ 

function validateFields()
{	
	var ban = true;

	$("#frm_solSoatID input:not([type='file']), #frm_solSoatID select, #frm_solSoatID textarea").each(function(){
		if($(this).val() == ""){
			var label = $('#frm_solSoatID label[for="'+$(this).attr("id")+'"]').text();
			Swal.fire({
			  type: 'info',
			  title: 'Campo Faltante!',
			  text: 'El campo '+ label + ' esta vacio.',
  			  confirmButtonColor: '#336600'
			})
			ban = false;
			return false;
		}
	});
	return ban;
};

/*! \fn: SubmitForm
  *  \brief: Funcion que Envia la información junto con archivos al metodo sendmail en php
  *  \author: Ing. Luis Manrique
  *  \date: 23/10/2019
  *  \return n/a
  */ 

function SubmitForm()
{
	try 
	{
		if(validateFields()){
			var form = new FormData(document.getElementById('frm_solSoatID'));
	        if (jQuery('#tar_propieID').length !== 0){
			    jQuery.each(jQuery('#tar_propieID')[0].files, function(i, file) {
			        form.append('tar_propie['+i+']', file);
			    });
	        }

			var standa = $("#standarID").val();
			var parametros = "Option=sendmail&Ajax=on";

			Swal.fire({
			  title: '¿Estas seguro?',
			  text: "¿Estas seguro que desea enviar esta solicitud?",
			  type: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#336600',
			  cancelButtonColor: '#aaa',
			  confirmButtonText: 'Si, confirmar'
			}).then((result) => {
			  if (result.value) {
			  	$.ajax({
					url: "../" + standa + "/segurm/sol_seguro_soatxx.php?"+parametros,
					type: "post",
					data: form,
					processData: false,  // tell jQuery not to process the data
			  		contentType: false,   // tell jQuery not to set contentType
			  		beforeSend: function()
				    {
				      Swal.fire({
							  title:'Cargando',
						      text: 'Por favor espere...',
						      imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
  							  imageAlt: 'Custom image',
  							  showConfirmButton: false,
							})
				    },
					success: function(data) {
						//console.log(data);
						if(data == 1){
							Swal.fire({
						      title:'Correo Enviado!',
						      text: 'Su solicitud a sido generada, en unos minutos estará recibiendo respuesta',
						      type: 'success',
						      confirmButtonColor: '#336600'
						    }).then((result) => {
			  					if (result.value) {
									location.reload();
			  					}
							})
						}else if(data == 2){
							Swal.fire({
							  title:'Correo no enviado',
						      text: 'Se debe actualizar la información de la empresa, Nombre y/o Email',
						      type: 'error',
						      confirmButtonColor: '#336600'
							})
						}else{
							Swal.fire({
							  title:'Correo no enviado',
						      text: 'Se a presentado un error en la solicitud, contacte a su administrador del servicio',
						      type: 'error',
						      confirmButtonColor: '#336600'
							})
						}
					}
				});
			    
			  }
			})
		}
	} 
	catch (e) 
	{
		console.log("Error SubmitForm " + e.message);
	}
}