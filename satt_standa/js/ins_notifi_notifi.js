$(document).ready(function() 
{
  $("#fec_iniID, #fec_finID").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
  }); 
 preFunction();
 $('.error').fadeOut();
});

function preFunction(){
  $('.error').fadeOut();
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
          $(this).focus().after('<span class="error" onclick="cerrarAlert()">Formato no permitido</span>');
          $(this).val("");
        break;
      }
      var filesize=$(this)[0].files[0];
      if(filesize.size>="5000000")
      {
          $(this).focus().after('<span class="error" onclick="cerrarAlert()">tamaño del archivo no permitido</span>');
          //alert("tamaño del archivo no permitido");
          $(this).val("");
      }
  });
  $('#num_horlabID, #numb_enturnoID, #numb_ausenteID, #numb_incapacID, #numb_cargueID, #numb_transiID, #numb_descarID, #numb_caremprID, #numb_penllegID, #numb_pernotaID, #numb_seguespID, #numb_recomenID, #numb_preventID, #numb_hurtadoID, #numb_accidenID, #numb_transboID, #numb_enrealiID, #numb_registrID, #numb_subaspgID, #numb_esrealiID, #numb_esconscID, #numb_espendeID, #numb_espendaID, #numb_asrealiID, #numb_asconscID, #numb_aspendeID, #numb_aspendaID').keyup(function(){
    $('.error').fadeOut();
      if($(this).val().length>2)
      {
        $(this).focus().after('<span class="error" onclick="cerrarAlert()">Valor maximo superado</span>');
        $(this).val("");
      }
      if(!$(this).val().match(/^[0-9]+$/)) 
      {
        $(this).focus().after('<span class="error" onclick="cerrarAlert()">Campo Numerico</span>');
        $(this).val("");
      }
      //alert($(this).val());

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
      $('.error').fadeOut();
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
                      cache:false,
                      beforeSend: function() {
                  
                      },
                      success: function(data) {
                          $("#tabgeneral").html(data);
                          $("#secID").css({"height":"auto"});
                          //alert(data);
                      }
                  });
              }else
              {
                $("#fec_iniID").focus().after('<span class="error" onclick="cerrarAlert()">las fecha '+fec_iniID+' es mayor q '+fec_finID+'</span>');
                //alert("las fecha "+fec_iniID+" es mayor q "+fec_finID);
              } 
          }else
          {
            $("#fec_finID").focus().after('<span class="error" onclick="cerrarAlert()">Campo Requerido</span>');
            //alert("Seleciones una fecha");
          }
      }else
      {
        $("#fec_iniID").focus().after('<span class="error" onclick="cerrarAlert()">Campo Requerido</span>');
        //alert("Seleciones una fecha");
      }
  }

  function btnSubModulos(cod_notifi)
  {   
      $('.error').fadeOut();
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
                      cache:false,
                      success: function(data) {
                          $("#"+identificador).html("");
                          $("#"+identificador).html(data);
                          $("#secID").css({"height":"auto"});
                          //alert(data);
                      }
                  });
              }else
              {
                $("#fec_iniID").focus().after('<span class="error" onclick="cerrarAlert()">las fecha '+fec_iniID+' es mayor q '+fec_finID+'</span>');
                //alert("las fecha "+fec_iniID+" es mayor que "+fec_finID);
              } 
          }else
          {
            $("#fec_finID").focus().after('<span class="error" onclick="cerrarAlert()">Campo Requerido</span>');
            //alert("Seleciones una fecha");
          }
      }else
      {
        $("#fec_iniID").focus().after('<span class="error" onclick="cerrarAlert()">Campo Requerido</span>');
        //alert("Seleciones una fecha");
      }
  }

  function NuevaNoti(id)
  {
    $('.error').fadeOut();
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
        cache:false,
        beforeSend: function(obj) {
            popup.parent().children().children('.ui-dialog-titlebar-close').hide();
        },
        success: function(data) {
            popup.html(data);
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
    $('.error').fadeOut();
  }

  function ValidateFormComun(accion){
    var standa = $("#standaID").val();
    var nom_asunto = $("#nom_asuntoID").val();
    var fec_creaci = $("#fec_creaciID").val();
    var usr_creaci = $("#usr_creaciID").val();
    var cod_asires = validateChekBox("cod_asiresID");
    var ind_notusr = validateChekBox("ind_notusrID");
    var fec_vigenc = $("#fec_vigencID").val();
    var ind_respue = $("input:radio[name=ind_respue]:checked").val();
    var ind_enttur = $("input:radio[name=ind_enttur]:checked").val();
    var obs_notifi = $("#obs_notifiID").val();
    var cod_tipnot = $("#cod_tipnotID").val();
    var num_horlab = $("#num_horlabID").val();
    band=1;
    $('.error').fadeOut();
    if(nom_asunto.length>5 && nom_asunto.length<100)
    {
      if(validateChekBox("cod_asiresID")!="")
      {
        if(cod_tipnot==2 || cod_tipnot==3 || cod_tipnot==5)
        {
          if(validateChekBox("ind_notusrID")!="")
          {
            band=1;
          }
          else
          {
             band=0;
          }          
        }
        if(band==1)
        {
          if($("#fec_vigencID").val()!="")
          {
            if($("#obs_notifiID").val()!="")
            {
              if(cod_tipnot==3 || cod_tipnot==4)
              {
                if((num_horlab.length>0 && num_horlab.length<3) || (num_horlab.val()>24))
                {
                  var mdata = new FormData();
                  if(accion=="ins")
                  {
                    mdata.append("option","NuevaNotifiExten");
                  }
                  else if(accion=="idi")
                  {
                    mdata.append("option","EditNotifiExten");
                  }
                  
                  mdata.append("nom_asunto",nom_asunto);
                  mdata.append("fec_creaci",fec_creaci);
                  mdata.append("usr_creaci",usr_creaci);
                  mdata.append("cod_asires",cod_asires);
                  mdata.append("ind_notusr",ind_notusr);
                  mdata.append("fec_vigenc",fec_vigenc);
                  mdata.append("ind_respue",ind_respue);
                  mdata.append("obs_notifi",obs_notifi);
                  mdata.append("cod_tipnot",cod_tipnot);
                  mdata.append("num_horlab",num_horlab);
                  mdata.append("ind_enttur",ind_enttur);
                  $("#Document").find("input[type=file]").each(function(){
                      mdata.append($(this).attr('name'),$(this)[0].files[0]);
                  });
                  console.log(mdata);
                  returnJson = ValidateFormExt(cod_tipnot);
                  console.log(returnJson);
                  if(returnJson.NOTNULL)
                  {
                    $.each(returnJson.NOTNULL,function(i,v){
                      $("#"+v+"ID").focus().after('<span class="error" onclick="cerrarAlert()">Campo Requerido</span>');
                      
                    });
                    alert("error en el json");
                  }
                  else
                  {
                    var myJsonString = JSON.stringify(returnJson);
                    console.log(myJsonString);
                    mdata.append("jso_notifi",myJsonString);
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
                        /*if(data=="OK")
                        {
                          alert("Se creo la notificacion correctamente");
                        }
                        else
                        {
                          alert("error al crear la notificaion");

                        }*/
                        
                      }
                    }); 
                  }
                }
                else
                {
                  $("#num_horlabID").focus().after('<span class="error" onclick="cerrarAlert()">Campo Requerido</span>');
                }
              }
              else
              {
                var mdata = new FormData();
                if(accion=="ins")
                {
                  mdata.append("option","NuevaNotifiComun");
                }
                else if(accion=="idi")
                {
                  mdata.append("option","EditNotifiComun");
                }
                
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
              
            }
            else
            {
              $("#obs_notifiID").focus().after('<span class="error" onclick="cerrarAlert()">Campo Requerido</span>');
              return false;
            }
          }
          else
          {
            $("#fec_vigencID").focus().after('<span class="error" onclick="cerrarAlert()">Campo Requerido</span>');
            return false;
          }
        }
        else
        {
          $("#ind_notusrID").focus().after('<span class="error" onclick="cerrarAlert()">Campo Requerido</span>');  
          return false;
        } 
      }
      else
      {
        $("#cod_asiresID").focus().after('<span class="error" onclick="cerrarAlert()">Campo Requerido</span>');  
        return false;
      } 
    }
    else
    {
      $("#nom_asuntoID").focus().after('<span class="error" onclick="cerrarAlert()">Campo Requerido</span>');  
      return false;
    }
    //alert(limitCampos("string","#nom_asuntoID","<=","3"));
  }

  function ValidateFormExt(cod_tipnot){
    var mdata = new FormData();
    mdata.append("option","NuevaNotifiComun");
    var status={};
    status.NOTNULL=[];
    var jsonArray={};
    if(cod_tipnot==3)
    {
      jsonArray.SUPERVISORES={};
      $("#jsonFormDigi").find("input[type=text]").each(function(){
          dato=$(this);
          if($(this).val()!="")
          {
            jsonArray.SUPERVISORES[dato.attr('name')]=dato.val();
          }
          else
          {
            status.NOTNULL.push(dato.attr('name'), dato.val());
          }   
      });
      jsonArray.CONTROLADORES={};
      $("#jsonContro").find("input[type=text]").each(function(){
          dato=$(this);
          if($(this).val()!="")
          {
            jsonArray.CONTROLADORES[dato.attr('name')]=dato.val();
          }
          else
          {
            status.NOTNULL.push(dato.attr('name'), dato.val());
          }   
      });
      jsonArray.ENCUESTAS={};
      $("#jsonEncu").find("input[type=text]").each(function(){
          dato=$(this);
          if($(this).val()!="")
          {
            jsonArray.ENCUESTAS[dato.attr('name')]=dato.val();
          }
          else
          {
            status.NOTNULL.push(dato.attr('name'), dato.val());
          }   
      });
      jsonArray.ESPECIFICAS={};
      $("#jsonEspeci").find("input[type=text]").each(function(){
          dato=$(this);
          if($(this).val()!="")
          {
            jsonArray.ESPECIFICAS[dato.attr('name')]=dato.val();
          }
          else
          {
            status.NOTNULL.push(dato.attr('name'), dato.val());
          }   
      });
      jsonArray.ASISTENCIAS={};
      $("#jsonAsist").find("input[type=text]").each(function(){
          dato=$(this);
          if($(this).val()!="")
          {
            jsonArray.ASISTENCIAS[dato.attr('name')]=dato.val();
          }
          else
          {
            status.NOTNULL.push(dato.attr('name'), dato.val());
          }   
      });
    }
    jsonArray.ESTADO_VEHICULOS={};
    $("#jsonEstVehi").find("input[type=text]").each(function(){
        dato=$(this);
        if($(this).val()!="")
        {
          jsonArray.ESTADO_VEHICULOS[dato.attr('name')]=dato.val();
        }
        else
        {
          status.NOTNULL.push(dato.attr('name'), dato.val());
        }   
    });
    jsonArray.RECURSOS_ASIGNADOS={};
    $("#jsonRecurAsi").find("input[type=text]").each(function(){
        dato=$(this);
        if($(this).val()!="")
        {
          jsonArray.RECURSOS_ASIGNADOS[dato.attr('name')]=dato.val();
        }
        else
        {
          status.NOTNULL.push(dato.attr('name'), dato.val());
        }   
    });
    if(status.NOTNULL.length>0)
    {
      return status;
    }
    else
    {
      return jsonArray;
    }            
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
    $('.error').fadeOut();
    $("#ind_notusrID").multiselect('destroy');
    var cod_Respon=validateChekBox("cod_asiresID");
    var standa = $("#standaID").val();
    var formData = "option=getNomUsuario&standa=" + standa + "&cod_Respon=" + cod_Respon;
    $.ajax({
        url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
        type: "POST",
        data: formData,
        async: true,
        cache:false,
        success: function(data) {
          $("#ind_notusrID").html(data);
          $("#ind_notusrID").multiselect('destroy');
          $("#ind_notusrID").multiselect().multiselectfilter();
        }
    }); 
  }

  function cerrarAlert(){
    //alert($(this));
    //console.log($(this).hasClass('error'));
    $(".error").fadeOut();
  }

  function editarNotifi(row){
      var objeto = $(row).parent().parent();
      var cod_notifi = objeto.find("input[id^=cod_notifi]").val();
      var nom_asunto = objeto.find("input[id^=nom_asunto]").val();
      var cod_tipnot = objeto.find("input[id^=cod_tipnot]").val();
      var ind_notres = objeto.find("input[id^=ind_notres]").val();
      var ind_notusr = objeto.find("input[id^=ind_notusr]").val();
      var temp_ind_notres = ind_notres.split(',');
      var temp_ind_notusr = ind_notusr.split(',');
      var standa = $("#standaID").val();
      closePopUp('popID');
      LoadPopupJQNoButton('open', 'EDITAR NOTIFICACION '+cod_notifi, ($(window).height() - 40), ($(window).width() - 40), false, false, true);
      var popup = $("#popID");
      if(cod_notifi!="" && nom_asunto!="" && cod_tipnot!="")
      {
        var formData = "option=getFormNuevaNotifi&standa=" + standa + "&cod_notifi=" + cod_notifi + "&nom_asunto=" + nom_asunto + "&idForm=" + cod_tipnot + "&ActionForm=idi";
        $.ajax({
            url: "../" + standa + "/notifi/ajax_notifi_notifi.php",
            type: "POST",
            data: formData,
            async: true,
            cache:false,
            beforeSend: function(obj) {
              popup.parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            success: function(data) {
              popup.html(data);
              $("#cod_asiresID").multiselect().multiselectfilter();
              preFunction();
              $("input[name=multiselect_cod_asiresID]").each(function( i,v){
                  for(var xx=0;xx<temp_ind_notres.length;xx++)
                  {
                    if(temp_ind_notres[xx]==$(this).val())
                    {
                      //console.log($(this));
                      $(this).attr("aria-selected",true);
                      $(this).attr("checked",true);
                    }
                    
                  }
              });
              getNomUsuario();
            },
            complete:function(){
              //console.log("multiselect_ind_notusrID:"+$("input[name=multiselect_ind_notusrID]").val());
              //$("#ind_notusrID").multiselect('destroy');
              $("#ind_notusrID").multiselect().multiselectfilter();
              $("input[name=multiselect_ind_notusrID]").each(function( i,v){
                  /*for(var yy=0;yy<temp_ind_notusr.length;yy++)
                  {
                    if(temp_ind_notusr[yy]==$(this).val())
                    {
                      console.log($(this));
                      $(this).attr("aria-selected",true);
                      $(this).attr("checked",true);
                    }
                    
                  }*/
                  console.log($(this));
              });
            }
        });
        
      }
      else
      {
        alert("error al editar usuario");
      }
  }

  function FormeliminarNotifi(row)
  {
    var objeto = $(row).parent().parent();
    var cod_notifi = objeto.find("input[id^=cod_notifi]").val();
    var nom_asunto = objeto.find("input[id^=nom_asunto]").val();
    var cod_tipnot = objeto.find("input[id^=cod_tipnot]").val();
    var standa = $("#standaID").val();
    closePopUp('popID');
    LoadPopupJQNoButton('open', 'ELIMINAR NOTIFICACION '+cod_notifi, ($(window).height() - 40), ($(window).width() - 40), false, false, true);
    var popup = $("#popID");
    if(cod_notifi!="" && nom_asunto!="" && cod_tipnot!="")
    {
      var formData = "option=getFormNuevaNotifi&standa=" + standa + "&cod_notifi=" + cod_notifi + "&nom_asunto=" + nom_asunto + "&idForm=" + cod_tipnot + "&ActionForm=eli";
      $.ajax({
          url:"../" + standa + "/notifi/ajax_notifi_notifi.php",
          type: "POST",
          data: formData,
          async: true,
          cache:false,
          beforeSend: function(obj) {
            popup.parent().children().children('.ui-dialog-titlebar-close').hide();
          },
          success:function(data){
            popup.html(data);
            $("#cod_asiresID").multiselect().multiselectfilter();
            $("#ind_notusrID").multiselect().multiselectfilter();
          }
      }); 
    }
  }

  function eliminarNotifi()
  {
    var standa = $("#standaID").val();
    var nom_asunto = $("#nom_asuntoID").val();
    var cod_tipnot = $("#cod_tipnotID").val();
    var cod_notifi = $("#cod_notifiID").val();
    var cod_usuari = $("#cod_usuari").val();

    if(nom_asunto!="" && cod_tipnot!="" && cod_notifi!="" && cod_usuari!="")
    {
      var formDataEli = "option=elimiNotifi&standa=" + standa + "&cod_notifi=" + cod_notifi + "&nom_asunto=" + nom_asunto + "&cod_tipnot=" + cod_tipnot + "&ActionForm=eli";
      $.ajax({
          url:"../" + standa + "/notifi/ajax_notifi_notifi.php",
          type: "POST",
          data: formDataEli,
          async: true,
          cache:false,
          success:function(data){
            alert(data);
            //limpiarForm();
          }
      }); 
    }
  }