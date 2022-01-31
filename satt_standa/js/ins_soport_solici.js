
$("body").ready(function(){
  $("#nom_navegaID").val(navigator.appCodeName);
});

function transmitir_spg( aplica )
{
  try
  {

      var nom_usuari = $("#nom_usuariID");
      var ema_usuari = $("#ema_usuariID");
      var cel_usuari = $("#cel_usuariID");
      var nom_navega = $("#nom_navegaID");
      var cod_errorx = $("#cod_errorxID");
      var obs_asunto = $("#obs_asuntoID");
      var obs_mensaj = $("#obs_mensajID"); 
      var fil_attach = document.getElementById("doc_adjuntID"); 
      //var fil_attach = $("#doc_adjuntID"); 

      if(nom_usuari.val() == '') {
        alert("Digite el Nombre completo");
        return nom_usuari.focus();
      }      

      if(ema_usuari.val() == '') {
        alert("Digite el email!");
        return ema_usuari.focus();
      }

      if(cel_usuari.val() == '') {
        alert("Digite el numero de celular");
        return cel_usuari.focus();
      }      

      if(nom_navega.val() == '') {
        alert("Seleccione el navegador que usa!");
        return nom_navega.focus();
      }
      
      if(cod_errorx.val() == '') {
        alert("Seleccion el tipo de errror a reportar");
        return cod_errorx.focus();
      }
      
      if(obs_asunto.val() == '') {
        alert("Digite el asunto de la solicitud");
        return obs_asunto.focus();
      }
      
      if(obs_mensaj.val() == '') {
        alert("Digite la descripcion del problema");
        return obs_mensaj.focus();
      }

      if(fil_attach.value != '' ){
        var file = fil_attach.files;       
        if(file[0].size > 2001436) {
          alert("Por fabor verifique que el archivo adjunto no supere 2 MB");
          return fil_attach.click();
        } 
      }
      
      if( confirm("Desea realizar la solicitud de soporte") ) {
        $("form").submit();
      }
      
  }
  catch( e )
  {
    console.log( "Error en transmitir_spg: "+e.message+"\nlineNumber: "+e.lineNumber );
    return false;
  }
}
 