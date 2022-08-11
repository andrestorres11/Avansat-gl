$(document).ready(function($) {

	$("#login-button").click(function(){
		var validacion = true;

		var usuario = $("#usuario").val();
		var pass = $("#clave").val();

		if(usuario.length>25){
			alert('El usuario no puede contener mas de 25 caracteres');
			validacion = false;
			return false;
		}

		if(pass.length>20){
			alert('La clave no puede contener mas de 20 caracteres');
			validacion = false;
			return false;
		}

		if(validacion){
			$("#login").submit();
		}
	});
	var img_log = $("#img_logID").val();
	var img_fon = $("#img_fonID").val();
	var col_fon = $("#col_fonID").val();
	var col_bot = $("#col_botID").val();
	if (col_fon != '#285c00' && img_fon == "../satt_standa/imagenes/11.jpg") {
		$("#wrapper").css("background-image", "none");
		//$("#wrapper").css("background-color", ""+ col_fon +" !important");
	}else{
		$("#wrapper").css("background-image", "url("+img_fon+")");
	}
	$("#login-button").css("background-color", col_bot);
});

var getClave = function(){
	try{
		var Standa = $("#standaID").val();
		var bd = $("#bdID").val();
		$("#getClave").dialog({
	      modal : true,
	      draggable: false,
	      resizable : true,
	      position:{ of: window, my: "center", at: "center" },
	      title: "Recuperar Clave de Acceso",
	      width: $(document).width()-900,
	      height : 400,
	      bgiframe: true,
	      closeOnEscape: true,
	      closeText: '',
	      show : { effect: "fade", duration: 500 },
	      hide : { effect: "fade", duration: 500 }
	    });
	    $.ajax({
	      url: "../" + Standa + "/forgot/ajax_forgot_forgot.php",
	      data : 'standa=' + Standa +'&option=setForgot&bd='+bd,
	      method : 'POST',
	      success : 
	        function ( data ) 
	        {
	          	$("#getClave").html( data );
	          	$("button.ui-button").removeClass('ui-button-icon-only');
	        }
	    });
	}
	catch(e){
		console.log(e.message);
		alert("Error en function getClave");
		return false;
	}
}

function sendClave(event){
	try{
		event.preventDefault();

		var expMail = /[A-Za-z0-9_\-]{2,}@[a-z]{2,}\.[a-z]{2,}/;

		var validacion = true;
		var Standa = $("#standaID").val();
		var bd = $("#bdID").val();
		var usuario = $("#usuarioPass");
		var email = $("#mailPass");

		if(usuario.val() == ''){
			validacion = false;
			alert("Digite el Usuario");
	      	usuario.focus();
	      	return false;
		}

		if(email.val() == ''){
			validacion = false;
			alert("Digite el correo asociado al usuario");
	      	email.focus();
	      	return false;
		}

		if(!expMail.test( email.val() )){
			validacion = false;
			alert("Digite un correo valido");
	      	email.focus();
	      	return false;
		}

		if(validacion == true){
			$.ajax({
		        url: "../" + Standa + "/forgot/ajax_forgot_forgot.php",
		        data : 'standa=' + Standa +'&option=sendForgot&ema_usuari=' + email.val()+'&cod_usuari=' + usuario.val()+'&bd='+bd,
		        method : 'POST',
		        beforeSend : function () 
		        { 
		          bloquear();
		        },
		        success :  function ( data )   
		        { 
		            desbloquear();
		            if( data == 'y' )
		            {
		            	$("#olvido").remove();
		              	alert("Estimado Usuario, al correo se ha enviado su nueva Clave");
		            }
		            else
		            {
		              alert("El correo No Coincide con el Registrado");
		              return false;
		            }
		            $("#getClave").parent().remove();
		            $(".ui-widget-overlay").remove();

		          }
		    });
		}

	}
	catch(e){
		console.log(e.message);
		alert("Error en function sendClave");
		return false;
	}
}

function bloquear()
{
  $.blockUI({
    css: { 
      border: 'none',   
      padding: '15px',      
      backgroundColor: '#001100',             
                       '-webkit-border-radius': '10px',             
                       '-moz-border-radius': '10px',             
      opacity: .5,             
      color: '#fff'         
    },
    
    overlayCSS: { 
      backgroundColor: '#001100', width: '100%', top: 0, left: 0 
    }
  });   
}


function desbloquear()
{
  $.unblockUI();
}

function validaIngresar(form)
{
        if(!form.usuario.value || !form.clave.value)
        {
                alert('Digite Usuario y Clave');
                form.usuario.focus();
                return(false)
        }
        else form.op.value = 1;
}