/* ! \file: con_pedido_client.js
 *  \brief: Documento que ejecuta eventos para el modulo de consulta Pedido
 *  \author: Ing. Luis Manrique
 *  \version: 1.0
 *  \date: 04/09/2020
 *  \warning:
 */

//Definición de variables
var standa,urlApli,pedido;

//Definición de eventos ajax en carga
$(document)
  .ajaxStart(function () {
    $.blockUI({ message: '<div class="bg-primary">Espere un momento</div>' });
  })
  .ajaxStop(function () {
    $.unblockUI();
  });

/*! \fn: document.ready
 *  \brief: Carga los eventos necesarios para 
 *  \author: Ing. Luis Manrique
 *  \date: 04/09/2020
 *  \date modified: dd/mm/aaaa
 *  \return: N/A
 */

$(function(){
	standa = $("#standa").val();
	urlApli = $("#urlApli").val();
	pedido = $("#pedido").text();
	createTimeLine(standa);

	$("#formPedRecibido").submit(function(e){
		try {
			e.preventDefault();
			let opcion= "recibido";
			let ped_recibi = $("#ped_recibi").val() == 'on'?1:0;
			$.ajax({
			  	url: "../../../"+standa+"/despac/con_pedido_client.php",
				data: ({"NOM_URL_APLICA":urlApli,pedido,opcion,ped_recibi}),
			  	type: "post",
			  	dataType: "json",
			  	success: function(data) {
			  		let tipo, titulo;
			  		if (data['status'] == 202) {
			  			tipo = "success";
			  			titulo = "Registrado!!";
			  		}else{
			  			tipo = "error";
			  			titulo = "Ops!!";
			  		}

			  		alertSweet(tipo, titulo, data['response'], 'Actualizar');
				}
		  	});
		}
		catch(err) {
		  console.error("Error metodo formPedRecibido: "+err.message);
		}
	});

	valPedRecib(standa);
});

function createTimeLine(standa){
	try {
	  $.ajax({
	  	url: "../../../"+standa+"/despac/con_pedido_client.php",
		data: ({"NOM_URL_APLICA":urlApli,pedido}),
	  	type: "post",
	  	dataType: "json",
	  	success: function(data) {
	  		if (data['datos'].length > 0) {
		  		$.each(data['datos'], function( id, campos ) {
				  	$.each(campos, function( campo, valor ) {
					  	$("#"+campo).text(valor);
					});
				});
	  			$(".timeline").append(data['html']);
	  		}else{
	  			$("section").html(data['html']);
	  		}
		}
	  });

	}
	catch(err) {
		console.error("Error metodo createTimeLine: "+err.message);
	}
}

function formEncuesta(){
	try {
		let opcion= "formEncuesta";
		$.ajax({
		  	url: "../../../"+standa+"/despac/con_pedido_client.php",
			data: ({"NOM_URL_APLICA":urlApli,pedido,opcion}),
		  	type: "post",
		  	dataType: "json",
		  	success: function(data) {
		  		if (data['status'] == 202) {
		  			alertSweet("form", '', data['response']);
		  			$("#ama_conduc").on("change", function(){
		  				if ($(this).val() == 3 || $(this).val() == 4) {
		  					$(".ocultoDiv").show(500);
		  					$(".ocultoDiv textarea").attr("required","required");
		  				}else{
		  					$(".ocultoDiv").hide(500);
		  					$(".ocultoDiv textarea").removeAttr("required");
		  				}
		  			});

		  			$("#formEncuesta").submit(function(e){
						try {
							e.preventDefault();
							let form = $(this).serialize();
							let opcion= "insertEncuesta";
							form = form+"&NOM_URL_APLICA="+urlApli+"&pedido="+pedido+"&opcion="+opcion;
							$.ajax({
							  	url: "../../../"+standa+"/despac/con_pedido_client.php",
								data: form,
							  	type: "post",
							  	dataType: "json",
							  	success: function(data) {
							  		swal.close();
							  		let tipo, titulo;
							  		if (data['status'] == 202) {
							  			tipo = "success";
							  			titulo = "Registrado!!";
							  		}else{
							  			tipo = "error";
							  			titulo = "Ops!!";
							  		}
							  		alertSweet(tipo, titulo, data['response']);
								}
						  	});
						}
						catch(err) {
						  console.error("Error metodo formEncuesta: "+err.message);
						}
					});
		  		}else{
		  			alertSweet("error", "Ops!!", data['response']);
		  		}
			}
	  	});
	}
	catch(err) {
	  console.error("Error metodo formEncuesta: "+err.message);
	}
}


function valPedRecib(standa){
	try {
	  let opcion= "valPedRecib";
	  $.ajax({
	  	url: "../../../"+standa+"/despac/con_pedido_client.php",
		data: ({"NOM_URL_APLICA":urlApli,pedido,opcion}),
	  	type: "post",
	  	dataType: "json",
	  	success: function(data) {
	  		if (data['status'] == 202) {
	  			if (data['response'] == 1) {
	  				$("#ped_recibi").attr("checked","checked").attr("disabled","disabled");
	  				$("#con_recped").parents(".form-row").remove();
	  			}
	  		}else{
	  			console.error("Error metodo createTimeLine: "+data['response']);
	  		}
		}
	  });

	}
	catch(err) {
		console.error("Error metodo createTimeLine: "+err.message);
	}
}


function alertSweet(tipo, titulo = null, html, event = ''){
	try {

		if (tipo == "form") {
			Swal.fire({
				  html: html,
				  showConfirmButton: false,
				  allowOutsideClick: false,
				  showCancelButton: true,
				  cancelButtonText: 'Cancelar',
			});
		}else if(event == 'Actualizar'){
			Swal.fire({
				  type: tipo,
				  title: titulo,
				  html: html,
				  onClose: () => {
				    location.reload();
				  }
			});
		}else{
			Swal.fire({
				  type: tipo,
				  title: titulo,
				  html: html,
			});
		}
	}
	catch(err) {
	  console.error("Error metodo alertSweet: "+err.message);
	}
}