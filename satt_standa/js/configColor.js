$(document).ready(function() {
    $.unblockUI();
	$("#col_botin1ID").spectrum({
		color: "#285c00",
		preferredFormat: "hex",
	});

	$("#col_botin2ID").spectrum({
		preferredFormat: "hex",
		color: "#285c00"
	});
	$("input[name=color]").change(function(){
		alert($('input[name=color]').val());
		$('#colorseleccionado').val($(this).val());
	});

});

$(document).ready(function() { 
	$("#aceptarID").click("uploadAjax");
})

function resetlog_empres(){
	try{
		$("#fot_logotiID").val('');
	}catch (e) {
        alert("Error Fuction resetlog_empres: " + e.message + "\nLine: " + e.lineNumber);
    }
}

function reset_fonpan(){
	try{
		$("#log_fonpanID").val('');
	}catch (e) {
        alert("Error Fuction reset_fonpan: " + e.message + "\nLine: " + e.lineNumber);
    }
}

function resetColor(obj){
	try{
		if (obj == 1) {
			$("#col_botin1ID").spectrum({
			color: "#285c00"
			});	
		}else{
			$("#col_botin2ID").spectrum({
			color: "#285c00"
			});	
		}
	}catch (e) {
        alert("Error Fuction resetColor: " + e.message + "\nLine: " + e.lineNumber);
    }
}

function Insertar()
{	
	var standa = $("#standarID").val();
	var fot_logoti = $("#fot_logotiID").val();
	var log_fonpan = $("#log_fonpanID").val();
	var col_botin1 = $("#col_botin1ID").val();
	var col_botin2 = $("#col_botin2ID").val();
	var val = true;
	var mensaje = "";

	if (fot_logoti == '') {
		val = false;
		mensaje += "El logo quedo con la configuracion estandar. <br>";
		fot_logoti = "../sate_standa/imagenes/avansat-empresarial.png";
	}
	if (log_fonpan == '' && col_botin1 == ''){
		val = false;
		mensaje += "El fondo de pantalla quedo con la configuracion estandar. <br>";
		log_fonpan = "../imagenes/login/9.jpg";
	}
	if (log_fonpan == ''){
		log_fonpan = "../imagenes/login/9.jpg";
	}
	if (col_botin2 == ''){
		val = false;
		mensaje += "El Boton ingresar quedo con la configuracion estandar. <br>";
		col_botin2 = "hsv(0, 100%, 100%)";
	}
	var parametros = "Option=1&Ajax=on&standa=" + standa + "&fot_logoti=" + fot_logoti + "&log_fonpan=" + log_fonpan + "&col_botin1=" + col_botin1 + "&col_botin2=" + col_botin2 + "&val=" + val + "&mensaje=" + mensaje;
	var flag = confirm("Esta de acuerdo a la aclaracion informada en la parte superior?");
	if (flag == true) {
		$.ajax({
		    url: "../" + standa + "/config/ins_config_loginx.php",
		    type: "POST",
		    data: parametros,
		    async: false,
		    beforeSend: function() {
		        //BlocK("Insertando configuracion del login...", true);
		    },
		    success: function(data) {
		    	$("#form_logID").html(data);
			},
		    complete: function() {
		        //BlocK();
		    }
		});
	}
}

function uploadAjax(){
	var standa = $("#standarID").val();
    $(function(){
        $("#form_log").on("submit", function(e){
            e.preventDefault();
            var f = $(this);
            var formData = new FormData(document.getElementById("form_log"));
            //formData.append("dato", "valor");
            formData.append(f.attr("name"), $(this)[0].files[0]);
            $.ajax({
                url: "ins_config_loginx.php",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
         		processData: false
            })
                .done(function(res){
                    $("#mensaje").html("Respuesta: " + res);
                });
        });
    });
}

function insert(form_log){
	if(confirm('Esta de acuerdo a la Nota De Aclaracion informada en la parte inferior?')){
		form_log.submit();
	}
}