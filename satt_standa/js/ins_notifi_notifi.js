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
  $(':file').change(function(){
      var file = $(this).val();
      switch(file.substring(file.lastIndexOf('.') + 1).toLowerCase()){
        case 'jpg' : case 'jpeg': case 'bmp' : case 'tiff' : case 'png' : case 'pdf' : case 'doc' : case 'docx' : case 'xls' : case 'xlsx' : case 'cvs' : case 'zip' : case 'rar' :
          //alert("ok");
        break;
        default:
          alert("tipo de archivo no permitido");
          $(this).val("");
        break;
      }
      var filesize=$(this)[0].files[0];
      if(filesize.size>="5000000")
      {
          alert("tamaño del archivo no permitido");
          $(this).val("");
      }
      
    
        
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
                alert("las fecha "+fec_iniID+" es mayor que "+fec_finID);
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
    var formData = "option=getFormNuevaNotifi&standa=" + standa +"&idForm="+id + "&ActionForm=ins";
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

  function ValidateFormComun(){
    var standa = $("#standaID").val();
    var nom_asunto = $("#nom_asuntoID").val();
    var fec_creaci = $("#fec_creaciID").val();
    var usr_creaci = $("#usr_creaciID").val();
    var cod_asires = validateChekBox("cod_asiresID");
    var ind_notusr = validateChekBox("ind_notusrID");
    var fec_vigenc = $("#fec_vigencID").val();
    var ind_respue = $("input:radio[name=ind_respue]:checked").val();
    var obs_notifi = $("#obs_notifiID").val();
    var cod_tipnot = $("#cod_tipnotID").val();
    if(nom_asunto.length>5 && nom_asunto.length<100)
    {
      if(validateChekBox("cod_asiresID")!="")
      {
        if(validateChekBox("ind_notusrID")!="")
        {
          if($("#fec_vigencID").val()!="")
          {
            if($("#obs_notifiID").val()!="")
            {
              var mdata = new FormData();
              mdata.append("option","NuevaNotifiComun");
              mdata.append("nom_asunto",nom_asunto);
              mdata.append("fec_creaci",fec_creaci);
              mdata.append("usr_creaci",usr_creaci);
              mdata.append("cod_asires",cod_asires);
              mdata.append("ind_notusr",ind_notusr);
              mdata.append("fec_vigenc",fec_vigenc);
              mdata.append("ind_respue",ind_respue);
              mdata.append("obs_notifi",obs_notifi);
              mdata.append("cod_tipnot",cod_tipnot);
              $("#newNotifi").find("input[type=file]").each(function(){
                  mdata.append($(this).attr('name'),$(this)[0].files[0]);
              });
              console.log(mdata);
              $.ajax({
                url:"../" + standa + "/notifi/ajax_notifi_notifi.php",
                type:'POST',
                contentType:false,
                data: mdata,
                //async: false,
                processData:false,
                cache:false,
                success:function(data){
                  alert(data);
                  if(data=="OK")
                  {
                    alert("Se creo la notificacion correctamente");
                  }
                  else
                  {
                    alert("error al crear la notificaion");
                  }
                  
                }
              });
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

  function ValidateFormExt(){

    var standa = $("#standaID").val();
    var ind_enttur = $("input:radio[name=ind_enttur]:checked").val();
    var nom_asunto = $("#nom_asuntoID").val();
    var fec_creaci = $("#fec_creaciID").val();
    var usr_creaci = $("#usr_creaciID").val();
    var num_horlab = $("#num_horlabID").val();
    var cod_asires = validateChekBox("cod_asiresID");
    var ind_notusr = validateChekBox("ind_notusrID");
    var fec_vigenc = $("#fec_vigencID").val();
    var ind_respue = $("input:radio[name=ind_respue]:checked").val();
    var obs_notifi = $("#obs_notifiID").val();
    var cod_tipnot = $("#cod_tipnotID").val();
    var mdata = new FormData();
    mdata.append("option","NuevaNotifiComun");
    var status={};
    status.NOTNULL=[];
    var jsonArray={};
    jsonArray.SUPERVISORES=[];
    $("#jsonFormDigi").find("input[type=text]").each(function(){
        dato=$(this);
        if($(this).val()!="")
        {
          jsonArray.SUPERVISORES.push(dato.attr('name'), dato.val());
        }
        else
        {
          status.NOTNULL.push(dato.attr('name'), dato.val());
        }   
    });
    jsonArray.CONTROLADORES=[];
    $("#jsonContro").find("input[type=text]").each(function(){
        dato=$(this);
        if($(this).val()!="")
        {
          jsonArray.CONTROLADORES.push(dato.attr('name'), dato.val());
        }
        else
        {
          status.NOTNULL.push(dato.attr('name'), dato.val());
        }   
    });
    jsonArray.ESTADO_VEHICULOS=[];
    $("#jsonEstVehi").find("input[type=text]").each(function(){
        dato=$(this);
        if($(this).val()!="")
        {
          jsonArray.ESTADO_VEHICULOS.push(dato.attr('name'), dato.val());
        }
        else
        {
          status.NOTNULL.push(dato.attr('name'), dato.val());
        }   
    });
    jsonArray.ENCUESTAS=[];
    $("#jsonEncu").find("input[type=text]").each(function(){
        dato=$(this);
        if($(this).val()!="")
        {
          jsonArray.ENCUESTAS.push(dato.attr('name'), dato.val());
        }
        else
        {
          status.NOTNULL.push(dato.attr('name'), dato.val());
        }   
    });
    jsonArray.ESPECIFICAS=[];
    $("#jsonEspeci").find("input[type=text]").each(function(){
        dato=$(this);
        if($(this).val()!="")
        {
          jsonArray.ESPECIFICAS.push(dato.attr('name'), dato.val());
        }
        else
        {
          status.NOTNULL.push(dato.attr('name'), dato.val());
        }   
    });
    jsonArray.ASISTENCIAS=[];
    $("#jsonAsist").find("input[type=text]").each(function(){
        dato=$(this);
        if($(this).val()!="")
        {
          jsonArray.ASISTENCIAS.push(dato.attr('name'), dato.val());
        }
        else
        {
          status.NOTNULL.push(dato.attr('name'), dato.val());
        }   
    });
    jsonArray.RECURSOS_ASIGNADOS=[];
    $("#jsonRecurAsi").find("input[type=text]").each(function(){
        dato=$(this);
        if($(this).val()!="")
        {
          jsonArray.RECURSOS_ASIGNADOS.push(dato.attr('name'), dato.val());
        }
        else
        {
          status.NOTNULL.push(dato.attr('name'), dato.val());
        }   
    });
    console.log(status.NOTNULL.length);
    if(status.NOTNULL.length>0)
    {
      alert("Los datos del frormulario de diligenciamiento son requeridos");
      console.log(status.NOTNULL);
    }
    var myJsonString = JSON.stringify(jsonArray);
    //console.log(jsonArray);         
    //console.log(myJsonString);         
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

  function ActioFormularios(){
    alert("funcion");
  }