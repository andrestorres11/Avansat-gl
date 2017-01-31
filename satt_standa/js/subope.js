function enableSubope(obj){
	row = $(obj).parent().parent();
	data = row.find("[name^=cod_subope]").val(); 
	ajaxStatusSubope(data, 1);
}

function disableSubope(obj){
	row = $(obj).parent().parent();
	data = row.find("[name^=cod_subope]").val(); 
	ajaxStatusSubope(data, 0);
}

function ajaxStatusSubope(cod_subope, new_estado){

	standa = $("#standaID").val();

	data = {
		"id": cod_subope,
		"estado": new_estado,
		"op": "switchStatus"
	};

	$.ajax({
		"url": "../"+standa+"/subope/ajax_subope_subope.php",
		"data": data,
		beforeSend: function(){
			$("<div id='load'><center><h2>Cargando</h2><br><img src='../"+standa+"/images/puntos.gif'></img></center><div>").dialog({
				modal:true,
				draggable:false,
				resizable:false
			});
		},
		success: function(data){ 
			location.reload();
		}
	});

}

function saveSubope(){


	standa = $("#standaID").val();

	cod_operac = $("#cod_operacID").val();
	nom_subope = $("#nom_subopeID").val();

	data = {
		"cod_operac": cod_operac,
		"nom_subope": nom_subope,
		"op": "saveSubope"
	};

	$.ajax({
		"url": "../"+standa+"/subope/ajax_subope_subope.php",
		"data": data,
		beforeSend: function(){
			$("<div id='load'><center><h2>Cargando</h2><br><img src='../"+standa+"/images/puntos.gif'></img></center><div>").dialog({
				modal:true,
				draggable:false,
				resizable:false
			});
		},
		success: function(data){ 
			location.reload();
		}
	});


}