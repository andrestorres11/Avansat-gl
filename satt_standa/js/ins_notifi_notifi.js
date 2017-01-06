$(document).ready(function() 
{
  $("#fec_iniID, #fec_finID").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
  });
});

function ValidarFecha(campo) {
      var RegExPattern = '^([0-9]{4}[-/]?((0[13-9]|1[012])[-/]?(0[1-9]|[12][0-9]|30)|(0[13578]|1[02])[-/]?31|02[-/]?(0[1-9]|1[0-9]|2[0-8]))|([0-9]{2}(([2468][048]|[02468][48])|[13579][26])|([13579][26]|[02468][048]|0[0-9]|1[0-6])00)[-/]?02[-/]?29)$';
      if ((campo.match(RegExPattern)) && (campo!='')) {
            return true;
      } else {
            return false;
      }
}

function btnGeneral()
  {
      var fec_iniID=$("#fec_iniID").val();
      var fec_finID=$("#fec_finID").val();
      var standa = $("#standaID").val();
      var formData = "option=getFormGeneral&standa=" + standa + "&fec_iniID=" + fec_iniID + "&fec_finID=" + fec_finID;
      if(fec_iniID!="" )
      {
          if(fec_finID!="" )
          {
              if(Date.parse(fec_iniID)<Date.parse(fec_finID))
              {
                  $.ajax({
                      url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                      type: "POST",
                      data: formData,
                      async: true,
                      beforeSend: function() {
                  
                      },
                      success: function(data) {
                          $("#tabgeneral").html(data);
                          $("#notifiID").css({"height":"auto"});
                          //alert(data);
                      }
                  });
              }else
              {
                alert("las fecha "+fec_iniID+" es mayor q "+fec_finID);
              } 
          }else
          {
            $("#fec_finID").focus();
            alert("Seleciones una fecha");
          }
      }else
      {
        $("#fec_iniID").focus();
        alert("Seleciones una fecha");
      }
  }

  function btnSubModulos(cod_notifi)
  {   
      var identificador;
      var fec_iniID=$("#fec_iniID").val();
      var fec_finID=$("#fec_finID").val();
      var standa = $("#standaID").val();
      switch(cod_notifi){
        case 1:
          identificador="tabinfoet";
          break;

        case 2:
          identificador="tabinfclf";
          break;

        case 3:
          identificador="tabinfsup";
          break;

        case 4:
          identificador="tabinfcon";
          break;

        case 5:
          identificador="tabinfcli";
          break;
      }
      //alert(permisos);
      var formData = "option=getForm&standa=" + standa + "&cod_notifi=" + cod_notifi +  "&fec_iniID=" + fec_iniID + "&fec_finID=" + fec_finID;
      if(fec_iniID!="" )
      {
          if(fec_finID!="" )
          {
              if(Date.parse(fec_iniID)<Date.parse(fec_finID))
              {
                  $.ajax({
                      url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
                      type: "POST",
                      data: formData,
                      async: true,
                      beforeSend: function() {
                  
                      },
                      success: function(data) {
                          $("#"+identificador).html(data);
                          $("#notifiID").css({"height":"auto"});
                          //alert(data);
                      }
                  });
              }else
              {
                alert("las fecha "+fec_iniID+" es mayor q "+fec_finID);
              } 
          }else
          {
            $("#fec_finID").focus();
            alert("Seleciones una fecha");
          }
      }else
      {
        $("#fec_iniID").focus();
        alert("Seleciones una fecha");
      }
  }

  function NuevaNoti(id)
  {
    var standa = $("#standaID").val();
    var formData = "option=getFormNuevaNotifi&standa=" + standa;
    closePopUp('popID');
    LoadPopupJQNoButton('open', 'NUEVA NOTIFICACION', ($(window).height() - 40), ($(window).width() - 40), false, false, true);
    var popup = $("#popID");
    $.ajax({
        url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
        type: "POST",
        data: formData,
        async: true,
        beforeSend: function(obj) {
            BlocK('Generando Informe...', true);
            popup.parent().children().children('.ui-dialog-titlebar-close').hide();
        },
        success: function(data) {
            popup.html(data);
            BlocK();
        }
    });
  }