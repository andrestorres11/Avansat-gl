function setForgot()
{
  try
  {
    var Standa = $("#standaID").val();
    var Bd = $("#clientID").val();
    $("#forgotID").dialog({
      modal : true,
      resizable : false,
      draggable: false,
      title: "Recuperar Clave de Acceso",
      width: $(document).width()-900,
      heigth : 300,
      position:["middle",100], 
      bgiframe: true,
      closeOnEscape: true,
      show : { effect: "fade", duration: 500 },
      hide : { effect: "fade", duration: 500 }
    });

    $.ajax({
      url: "../" + Standa + "/forgot/ajax_forgot_forgot.php",
      data : 'standa=' + Standa +'&option=setForgot&bd='+Bd,
      method : 'POST',
      success : 
        function ( data ) 
        { 
          $("#forgotID").html( data );
        }
    });
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}

function sendForgot()
{
  try
  {
    var Standa = $("#standaID").val();
    var cod_usuari = $("#cod_usuariID");
    var ema_usuari = $("#ema_usuariID");
    var email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    if( cod_usuari.val() == '' )
    {
      alert("Digite el Usuario");
      cod_usuari.focus();
      return false;
    }
    else if( ema_usuari.val() == '' )
    {
      alert("Digite el Correo Registrado en la Plataforma");
      ema_usuari.focus();
      return false;
    }
    else if( !email.test( ema_usuari.val() ) )
    {
      alert("Correo Incorrecto");
      ema_usuari.focus();
      return false;
    }
    else
    {
      $.ajax({
        url: "../" + Standa + "/forgot/ajax_forgot_forgot.php",
        data : 'standa=' + Standa +'&option=sendForgot&ema_usuari=' + ema_usuari.val()+'&cod_usuari=' + cod_usuari.val(),
        method : 'POST',
        beforeSend : 
        function () 
        { 
          bloquear();
        },
        success : 
          function ( data ) 
          { 
            desbloquear();
            if( data == 'y' )
            {
              $("#forgotID").dialog('destroy');
              alert("Estimado Usuario, al correo se ha enviado una notificacion para continuar con el proceso de recuperacion de su Clave");
            }
            else
            {
              alert("El correo No Coincide con el Registrado");
              return false;
            }
          }
      });
    }
  }
  catch( e )
  {
    console.log( e.message );
    return false;
  }
}


function desbloquear()
{
  $.unblockUI();
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
