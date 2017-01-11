$(document).ready(function() 
{
  $("#fec_iniID, #fec_finID").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
  }); 
 preFunction();
});

function preFunction(){
  $("input[name=multiselect_cod_asiresID]").click(function(){
      getNomUsuario();
  });
  $(".ui-multiselect-none").click(function(){
      $("#ind_notusrID").multiselect('destroy');
      $("#ind_notusrID").html("");
      $("#ind_notusrID").multiselect().multiselectfilter();
  });
  $(".ui-multiselect-all").click(function(){
      getNomUsuario();
  });
  
}
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
    var formData = "option=getFormNuevaNotifi&standa=" + standa +"&idForm="+id;
    closePopUp('popID');
    LoadPopupJQNoButton('open', 'NUEVA NOTIFICACION', ($(window).height() - 40), ($(window).width() - 40), false, false, true);
    var popup = $("#popID");
    $.ajax({
        url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
        type: "POST",
        data: formData,
        async: true,
        beforeSend: function(obj) {
            BlocK('Nueva Notificacion...', true);
            popup.parent().children().children('.ui-dialog-titlebar-close').hide();
        },
        success: function(data) {
            popup.html(data);
            BlocK();
            $("#cod_asiresID").multiselect().multiselectfilter();
            $("#ind_notusrID").multiselect().multiselectfilter();
            preFunction();
        }
    });
  }

  function getFechaDatapick(iddp){
     $("#"+iddp).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
      });
  }

  function limpiarForm(){
    $("#nom_asuntoID").val("");
    $("#fec_creaci").val("");
    $("#NotificadoPorID").val("");
    $("#fec_vigencID").val("");
    $("#obs_notifiID").val("");
    closePopUp('popID');
  }

  function ValidateForm(){
    alert($("#obs_notifiID").val());
    var standa = $("#standaID").val();
    if($("#nom_asuntoID").val().length>5 && $("#nom_asuntoID").val().length<100)
    {
      if(validateChekBox("cod_asiresID")!="")
      {
        if(validateChekBox("ind_notusrID")!="")
        {
          if($("#fec_vigencID").val()!="")
          {
            if($("#obs_notifiID").val()!="")
            {
              alert("ok");
            }
            else
            {
              alert("Campo es requerida");
              $("#obs_notifiID").focus();
            }
          }
          else
          {
            alert("Campo de seleccion requerida");
            $("#fec_vigencID").focus();   
          }
        }
        else
        {
          alert("Campo de seleccion requerida");
          $("#ind_notusrID").focus();
        } 
      }
      else
      {
        alert("Campo de seleccion requerida");
        $("#cod_asiresID").focus();
      } 
    }
    else
    {
      alert("No cumple la longuitud requerida");
      $("#nom_asuntoID").focus();
    }
    //alert(limitCampos("string","#nom_asuntoID","<=","3"));
  }

  function validateChekBox(campoval){
    var cod_Respon="";
    var box_checke = $("input[type=checkbox]:checked");
    box_checke.each(function(i, o) {
      if ($(this).attr("name") == 'multiselect_'+campoval)
      {
        if($(this).val()!="0"){
          cod_Respon += ",'" + $(this).val() + "'";
        }
      }
    });
    return cod_Respon; 
  }

  function getNomUsuario(){
    $("#ind_notusrID").multiselect('destroy');
    var cod_Respon=validateChekBox("cod_asiresID");
    var standa = $("#standaID").val();
    var formData = "option=getNomUsuario&standa=" + standa + "&cod_Respon=" + cod_Respon;
    $.ajax({
        url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
        type: "POST",
        data: formData,
        async: true,
        success: function(data) {
          $("#ind_notusrID").html(data);
          $("#ind_notusrID").multiselect('destroy');
          $("#ind_notusrID").multiselect().multiselectfilter();
        }
    }); 
  }