$(document).ready(function() {
  var standa = $("#standaID").val();
  var filter = $("#filterID").val();
  $("#nom_transpID").autocomplete({
    source: "../" + standa + "/desnew/ajax_desnew_despac.php?option=getTransp&standa=" + standa + "&filter=" + filter,
    minLength: 3,
    delay: 100,
    select: function(event, ui) {
      boton = "<input type='button' id='nuevo' value='Crear Despacho' class='small save ui-button ui-widget ui-state-default ui-corner-all' onclick='ValidateTransp();'>";
      $("#cod_transpID").val(ui.item.id);
      $("#boton").empty();
      $("#boton").append(boton);
      $("body").removeAttr("class");
    }
  });
  $("#datos").css('display', 'none');
  $("#nom_transpID").css('width', '100%');
  $("body").removeAttr("class");



});

function propietario() {
  if ($("#con_propieID").attr("checked")) {
    $("#propietario").fadeOut('500');
    $("#dataPropietario").fadeOut('500');
  } else {
    $("#propietario").fadeIn('500');
    $("#dataPropietario").fadeIn('500');
  }
}

function poseedor() {
  if ($("#con_poseedID").attr("checked")) {
    $("#poseedor").fadeOut('500');
    $("#dataPoseedor").fadeOut('500');
  } else {
    $("#poseedor").fadeIn('500');
    $("#dataPoseedor").fadeIn('500');
  }
}

function AddGrid() {
  try {
    var standa = $("#standaID").val();
    var counter = $("#counterID").val();
    var cod_transp = $("#cod_transpID").val();
    $("#loading").remove();
    //alert( $("#num_factur0ID").val() );


    if ($("#num_factur" + counter + "ID").val() == '') {
      $("#loading").remove();
      $("#num_factur" + counter + "ID").focus().after("<span id='loading'><br>Digite El Documento</span>");
      return false;
    } else if ($("#num_docalt" + counter + "ID").val() == '') {
      $("#loading").remove();
      $("#num_docalt" + counter + "ID").focus().after("<span id='loading'><br>Digite El Documento Alterno</span>");
      return false;
    } else if ($("#cod_genera" + counter + "ID").val() == '') {
      $("#loading").remove();
      $("#cod_genera" + counter + "ID").focus().after("<span id='loading'><br>Seleccione el Generador</span>");
      return false;
    } else if ($("#nom_destin" + counter + "ID").val() == '') {
      $("#loading").remove();
      $("#nom_destin" + counter + "ID").focus().after("<span id='loading'><br>Digite el Destinatario</span>");
      return false;
    } else if ($("#cod_ciudad" + counter + "ID").val() == '') {
      $("#loading").remove();
      $("#cod_ciudad" + counter + "ID").focus().after("<span id='loading'><br>Seleccione La Ciudad</span>");
      return false;
    } else if ($("#dir_destin" + counter + "ID").val() == '') {
      $("#loading").remove();
      $("#dir_destin" + counter + "ID").focus().after("<span id='loading'><br>Direcci&oacute;n del Destinatario</span>");
      return false;
    } else if ($("#nom_contac" + counter + "ID").val() == '') {
      $("#loading").remove();
      $("#nom_contac" + counter + "ID").focus().after("<span id='loading'><br>Digite el n&uacute;mero de Ccontacto</span>");
      return false;
    } else if ($("#fec_citdes" + counter + "ID").val() == '') {
      $("#loading").remove();
      $("#fec_citdes" + counter + "ID").focus().after("<span id='loading'><br>Seleccione Fecha de Descargue</span>");
      return false;
    } else if ($("#hor_citcar" + counter + "ID").val() == '') {
      $("#loading").remove();
      $("#hor_citcar" + counter + "ID").focus().after("<span id='loading'><br>Seleccione Hora de Descargue</span>");
      return false;
    } else {

      $.ajax({
        type: "POST",
        url: "../" + standa + "/desnew/ajax_desnew_despac.php",
        data: "option=ShowDestin&counter=" + (parseInt(counter) + 1) + "&cod_transp=" + cod_transp,
        async: false,
        success: function(datos) {
          $('#datdesID').append(datos);
          $("#counterID").val((parseInt(counter) + 1));
        }
      });
      $('#datdesID').parent().css('height', 'auto');
    }
  } catch (e) {
    console.log(e.message);
    return false;
  }
}


function verifyTercero(cod_tercer, elemento, tip_tercer) {
  //alert( tip_tercer );
  try {
    $("#loading").remove();
    var standa = $("#standaID").val();
    var radio = $("#tip_per" + tip_tercer + "ID").val();
    $.ajax({
      type: "POST",
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: "option=verifyTercero&standa=" + standa + "&cod_tercer=" + cod_tercer + "&tip_tercer=" + tip_tercer + "&radio=" + radio,
      async: false,
      beforeSend: function() {
        $("#" + elemento).focus().after("<span id='loading'> <img src=\'../" + standa + "/imagenes/ajax-loader3.gif\' /></span>");
      },
      success: function(datos) {
        $("#loading").remove();
        if (datos.split('-')[0] == 'yes') {
          $("#tip_doc" + tip_tercer + "ID").val(datos.split('-')[1]);
          $("#tip_doc" + tip_tercer + "ID").attr('readonly', true);
          $("#num_div" + tip_tercer + "ID").val(datos.split('-')[2]);
          $("#num_div" + tip_tercer + "ID").attr('readonly', true);
          if (radio == 'N') {
            $("#nom_ter" + tip_tercer + "ID").val(datos.split('-')[3]);
            $("#nom_ter" + tip_tercer + "ID").attr('readonly', true);
          } else {
            $("#nom_ter" + tip_tercer + "ID").val(datos.split('-')[4]);
            $("#nom_ter" + tip_tercer + "ID").attr('readonly', true);
            $("#ape_ter" + tip_tercer + "ID").val(datos.split('-')[5]);
            $("#ape_ter" + tip_tercer + "ID").attr('readonly', true);
          }
          $("#cel_ter" + tip_tercer + "ID").val(datos.split('-')[6]);
          $("#cel_ter" + tip_tercer + "ID").attr('readonly', true);
          $("#ema_ter" + tip_tercer + "ID").val(datos.split('-')[7]);
          $("#ema_ter" + tip_tercer + "ID").attr('readonly', true);
        } else {
          $("#tip_doc" + tip_tercer + "ID").val("");
          $("#tip_doc" + tip_tercer + "ID").attr('readonly', false);
          $("#num_div" + tip_tercer + "ID").val(GenerateDV($("#num_doc" + tip_tercer + "ID").val()));
          $("#num_div" + tip_tercer + "ID").attr('readonly', true);
          if (radio == 'N') {
            $("#nom_ter" + tip_tercer + "ID").val("");
            $("#nom_ter" + tip_tercer + "ID").attr('readonly', false);
          } else {
            $("#nom_ter" + tip_tercer + "ID").val("");
            $("#nom_ter" + tip_tercer + "ID").attr('readonly', false);
            $("#ape_ter" + tip_tercer + "ID").val("");
            $("#ape_ter" + tip_tercer + "ID").attr('readonly', false);
          }
          $("#cel_ter" + tip_tercer + "ID").val("");
          $("#cel_ter" + tip_tercer + "ID").attr('readonly', false);
          $("#ema_ter" + tip_tercer + "ID").val("");
          $("#ema_ter" + tip_tercer + "ID").attr('readonly', false);
        }
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function verifyConduc(cod_tercer, elemento, tip_tercer) {
  //alert( tip_tercer );
  try {
    $("#loading").remove();
    var standa = $("#standaID").val();
    $.ajax({
      type: "POST",
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: "option=verifyConduc&standa=" + standa + "&cod_tercer=" + cod_tercer + "&tip_tercer=" + tip_tercer,
      async: false,
      beforeSend: function() {
        $("#" + elemento).focus().after("<span id='loading'> <img src=\'../" + standa + "/imagenes/ajax-loader3.gif\' /></span>");
      },
      success: function(datos) {
        $("#loading").remove();
        if (datos.split('/')[0] == 'yes') {
          $("#lic_conducID").val(datos.split('/')[1]);
          $("#lic_conducID").attr('readonly', true);
          $("#fec_venlicID").val(datos.split('/')[2]);
          $("#fec_venlicID").attr('readonly', true);
        } else {
          $("#lic_conducID").val("");
          $("#lic_conducID").attr('readonly', false);
          $("#fec_venlicID").val("");
          $("#fec_venlicID").attr('readonly', false);
        }
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function ShowForm(cod_transp) {
  try {

    nom_transp = cod_transp.split('-')[1].trim();
    cod_transp = cod_transp.split('-')[0].trim();
    var standa = $("#standaID").val();
    $("#cod_transpID").val(cod_transp);
    $("#resultID").css({
      'background-color': '#f0f0f0',
      'border': '1px solid #c9c9c9',
      'padding': '5px',
      'width': '98%',
      'min-height': '50px',
      '-moz-border-radius': '5px 5px 5px 5px',
      '-webkit-border-radius': '5px 5px 5px 5px',
      'border-top-left-radius': '5px',
      'border-top-right-radius': '5px',
      'border-bottom-right-radius': '5px',
      'border-bottom-left-radius': '5px'
    });
    $.ajax({
      type: "POST",
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: "option=MainForm&standa=" + standa + "&cod_transp=" + cod_transp + "&nom_transp=" + nom_transp,
      async: false,
      beforeSend: function() {
        $("#resultID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function(datos) {
        $("#resultID").html(datos);
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function ValidateTransp() {
  try {
    var standa = $("#standaID").val();
    var filter = $("#filterID").val();
    var cod_transp = $("#nom_transpID").val();
    var conn = checkConnection(); //verifica si existe conexion a internet
    if (conn == true) {
      $.ajax({
        type: "POST",
        url: "../" + standa + "/desnew/ajax_desnew_despac.php",
        data: "option=ValidateTransp&standa=" + standa + "&filter=" + filter + "&cod_transp=" + cod_transp.split('-')[0],
        async: false,
        success: function(datos) {
          if (datos == 'n') {
            setTimeout(function() {
              inc_alerta("nom_transpID", "El Cliente < " + cod_transp + " > no existe");
            }, 510);

            return false;
          } else {
            inc_remover_alertas();
            ShowForm(cod_transp);
          }
        }
      });
    } else {
      setTimeout(function() {
        inc_alerta("nuevo", "Por favor verifica tu conexión a internet.");
      }, 510);
    }
    $("#fec_citcar").datepicker();
    $("#fec_citcar").datepicker('option', {
      dateFormat: 'yy-mm-dd'
    });
    $("#hor_citcar").timepicker({
      showSecond: false
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function InsertDespac() {
  //$(".accordion").accordion({active: -1});
  setTimeout("ClearAllAccordion()", 350);
}

function ClearAllAccordion() {
  try {
    $("#loading").remove();
    // ------ VALIDACION POR PESTAÑAS ------
    var standa = $("#standaID").val();
    var conn = checkConnection();
    if (conn == true) {
      var val = validaciones();
      var cod_conduc = $("#cod_conducID").val();
      var gps = $("#cod_opegpsID").val();
      var ogps = $("#gps_otroxxID").val();
      if (gps != "") {
        var user = $("#usr_gpsxxxID").val();
        var pass = $("#clv_gpsxxxID").val();
        if (!user) {
          setTimeout(function() {
            inc_alerta("usr_gpsxxxID", "Este Campo es obligatorio si seleccionas operador GPS.");
          }, 510);
          val = false;
        }
        if (!pass) {
          setTimeout(function() {
            inc_alerta("clv_gpsxxxID", "Este Campo es obligatorio si seleccionas operador GPS.");
          }, 510);
          val = false;
        }
      }if (ogps != "") {
        var user = $("#usr_gpsxxxID").val();
        var pass = $("#clv_gpsxxxID").val();
        if (!user) {
          setTimeout(function() {
            inc_alerta("usr_gpsxxxID", "Este Campo es obligatorio si ingresas otro GPS.");
          }, 510);
          val = false;
        }
        if (!pass) {
          setTimeout(function() {
            inc_alerta("clv_gpsxxxID", "Este Campo es obligatorio si ingresas otro GPS.");
          }, 510);
          val = false;
        }
      }
      if (val == true) {
        inc_remover_alertas();
        $.ajax({
          type: "POST",
          url: "../" + standa + "/desnew/ajax_desnew_despac.php",
          data: "option=verifyConduc&standa=" + standa + "&cod_tercer=" + cod_conduc,
          async: false,
          success: function(datos) {
            if (datos == 'no') {
              setTimeout(function() {
                inc_alerta("cod_conducID", "El Conductor no Existe");
              }, 510);

            } else {
              SaveDespacho();
            }
          }
        });
      } else {
        $("#accbasID").children().css({
          height: 'auto'
        });
      }
    } else {
      setTimeout(function() {
        inc_alerta("nuevo", "Por favor verifica tu conexión a internet.");
      }, 510);
    }

    // *************************************
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function SaveDespacho() {
  try {
    var params = 'option=InsertDespacho';
    var standa = $("#standaID").val();
    params += '&standa=' + standa;
    params += getDataForm();
    var cod_transp = $("#cod_transpID").val();
    $.ajax({
      type: "POST",
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: params,
      async: false,
      beforeSend: function() {
        $("#resultID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td>Registrando Despacho</td></tr></table>');
      },
      success: function(datos) {
        $("#resultID").html(datos);
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function SetRemolque(cod_config) {
  try {
    var expr = /[a-zA-Z]/;
    if (cod_config.match(expr))
      $("#tex_remolqID").text("*");
    else
      $("#tex_remolqID").text("");
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function setFormTercero(type, zona, tercer) {
  try {
    $("#loading").remove();
    var standa = $("#standaID").val();
    $.ajax({
      type: "POST",
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: "option=setFormTercero&standa=" + standa + "&type=" + type + "&tercer=" + tercer,
      async: false,
      beforeSend: function() {
        $("#" + zona).html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function(datos) {
        $("#" + zona).html(datos);
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function SetLineas() {
  try {
    var cod_transp = $("#transpID").val();
    var cod_marcax = $("#cod_marcaxID").val();
    var standa = $("#standaID").val();
    $.ajax({
      type: "POST",
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: "option=SetLineas&standa=" + standa + "&cod_transp=" + cod_transp + "&cod_marcax=" + cod_marcax,
      async: false,
      beforeSend: function() {
        $("#cod_marcaxID").focus().after("<span id='loading'> <img src=\'../" + standa + "/imagenes/ajax-loader3.gif\' /></span>");
      },
      success: function(datos) {
        $("#loading").remove();
        $("#cod_lineaxID").html(datos);
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function SetDestinos() {
  try {
    var cod_transp = $("#cod_transpID").val();
    var cod_ciuori = $("#cod_ciuoriID").val();
    var standa = $("#standaID").val();
    $.ajax({
      type: "POST",
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: "option=SetDestinos&standa=" + standa + "&cod_transp=" + cod_transp + "&cod_ciuori=" + cod_ciuori,
      async: false,
      beforeSend: function() {
        $("#cod_ciudesID").focus().after("<span id='loading'> <img src=\'../" + standa + "/imagenes/ajax-loader3.gif\' /></span>");
      },
      success: function(datos) {
        $("#loading").remove();
        $("#cod_ciudesID").html(datos);
        $("#cod_rutaxxID").html('<option value="">- Seleccione -</option>');
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function NumericInput(evt) {
  var keyPressed = (evt.which) ? evt.which : event.keyCode;
  return !(keyPressed > 31 && (keyPressed < 48 || keyPressed > 57));
}

function SetRutas() {
  try {
    var cod_transp = $("#cod_transpID").val();
    var cod_ciuori = $("#cod_ciuoriID").val();
    var cod_ciudes = $("#cod_ciudesID").val();
    var standa = $("#standaID").val();
    $.ajax({
      type: "POST",
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: "option=SetRutas&standa=" + standa + "&cod_transp=" + cod_transp + "&cod_ciuori=" + cod_ciuori + "&cod_ciudes=" + cod_ciudes,
      async: false,
      beforeSend: function() {
        $("#cod_rutaxxID").focus().after("<span id='loading'> <img src=\'../" + standa + "/imagenes/ajax-loader3.gif\' /></span>");
      },
      success: function(datos) {
        $("#loading").remove();
        $("#cod_rutaxxID").html(datos);
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function puntos(donde, caracter) {
  pat = /[\*,\+,\(,\),\?,\\,\$,\[,\],\^]/
  valor = donde.value
  largo = valor.length
  crtr = true
  if (isNaN(caracter) || pat.test(caracter) == true) {
    if (pat.test(caracter) == true) {
      caracter = "\\" + caracter
    }
    carcter = new RegExp(caracter, "g");
    valor = valor.replace(carcter, "");
    donde.value = valor
    crtr = false;
  } else {
    var nums = new Array();
    cont = 0;
    for (m = 0; m < largo; m++) {
      if (valor.charAt(m) == "." || valor.charAt(m) == " ") {
        continue;
      } else {
        nums[cont] = valor.charAt(m);
        cont++;
      }
    }
  }
  var cad1 = "",
    cad2 = "",
    tres = 0
  if (largo > 3 && crtr == true) {
    for (k = nums.length - 1; k >= 0; k--) {
      cad1 = nums[k]
      cad2 = cad1 + cad2
      tres++
      if ((tres % 3) == 0) {
        if (k != 0) {
          cad2 = "." + cad2
        }
      }
    }
    donde.value = cad2
  }
}

function PopupVehiculos() {
  try {
    var standa = $("#standaID").val();
    var cod_transp = $("#cod_transpID").val();
    $("#PopUpID").dialog({
      modal: true,
      resizable: false,
      draggable: false,
      title: " Selecci\xf3n del Veh\xedculo",
      width: $(document).width() - 200,
      heigth: 500,
      position: ['middle', 25],
      bgiframe: true,
      closeOnEscape: false,
      show: {
        effect: "drop",
        duration: 300
      },
      hide: {
        effect: "drop",
        duration: 300
      }
    });
    $.ajax({
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: 'standa=' + standa + '&option=LoadVehiculos&cod_transp=' + cod_transp,
      method: 'POST',
      beforeSend: function() {
        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function(data) {
        $("#PopUpID").html(data);
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function SetVehiculo(num_placax, nom_marcax, nom_lineax, nom_colorx, nom_carroc, num_modelo, num_config, cod_tenedo, cod_conduc, nom_tenedo, nom_conduc, cod_propie, nom_propie) {
  try {
    $("#num_placaxID").focus();
    $("#num_placaxID").val(num_placax);
    var submit = $("#submitID").val();
    if (submit == 1) {
      document.form_insert.submit();
    }
    $("#des_marcaxID").html("<b>Marca:&nbsp;&nbsp;&nbsp;</b>");
    $("#nom_marcaxID").html("&nbsp;" + nom_marcax);
    $("#des_colorxID").html("<b>Color:&nbsp;&nbsp;&nbsp;</b>");
    $("#nom_colorxID").html("&nbsp;" + nom_colorx);
    $("#des_carrocID").html("<b>Carrocer&iacute;a:&nbsp;&nbsp;&nbsp;</b>");
    $("#nom_carrocID").html("&nbsp;" + nom_carroc);
    $("#des_modeloID").html("<b>Modelo:&nbsp;&nbsp;&nbsp;</b>");
    $("#nom_modeloID").html("&nbsp;" + num_modelo);
    $("#des_configID").html("<b>Configuraci&oacute;n:&nbsp;&nbsp;&nbsp;</b>");
    $("#nom_configID").html("&nbsp;" + num_config);
    $("#des_codproID").html("<b>Documento Propietario:&nbsp;&nbsp;&nbsp;</b>");
    $("#nom_codproID").html("&nbsp;" + cod_propie);
    $("#des_nomproID").html("<b>Nombre Propietario:&nbsp;&nbsp;&nbsp;</b>");
    $("#nom_nomproID").html("&nbsp;" + nom_propie);
    $("#des_codtenID").html("<b>Documento Tenedor:&nbsp;&nbsp;&nbsp;</b>");
    $("#nom_codtenID").html("&nbsp;" + cod_tenedo);
    $("#des_nomtenID").html("<b>Nombre Tenedor:&nbsp;&nbsp;&nbsp;</b>");
    $("#nom_nomtenID").html("&nbsp;" + nom_tenedo);
    //$("#lab_conducID").html( "<b>Nota:</b> Si desea cambiar el conductor para el despacho por favor haga doble click sobre el campo." );

    $("#des_codconID").html("<b>* Documento Conductor:&nbsp;&nbsp;&nbsp;</b>");
    $("#nom_codconID").html('<input type="text" value="' + cod_conduc + '" name="cod_conduc" id="cod_conducID" obl="1" minlength="6" maxlength="10" validate="numero" onchange="ValidateExistConduc( this.value );" size="15" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" />');
    $("#des_nomconID").html("<b>Nombre Conductor:&nbsp;&nbsp;&nbsp;</b>");
    $("#nom_nomconID").html("&nbsp;" + nom_conduc);
    var expr = /[a-zA-Z]/;
    if (num_config.match(expr)) {
      $("#des_numremID").html("<b>* Remolque:&nbsp;&nbsp;&nbsp;</b>");
      $("#nom_numremID").html('<input type="text" name="cod_remolq" id="cod_remolqID" obl="1" minlength="4" validate="alpha" maxlength="7" size="10">');
    } else {
      $("#des_numremID").html("&nbsp;&nbsp;&nbsp;");
      $("#nom_numremID").html('<input type="hidden" name="cod_remolq" id="cod_remolqID" value="not" maxlenght="7" size="10">');
    }
    $("#PopUpID").dialog('close');
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function ValidateExistConduc(num_conduc) {
  try {

    $("#loading").remove();
    var standa = $("#standaID").val();
    var cod_transp = $("#transpID").val();
    $("#nom_nomconID").html("&nbsp;");
    if (num_conduc != '') {

      $.ajax({
        url: "../" + standa + "/desnew/ajax_desnew_despac.php",
        data: 'standa=' + standa + '&option=ValidateExistConduc&cod_transp=' + cod_transp + '&num_conduc=' + num_conduc,
        method: 'POST',
        beforeSend: function() {
          $("#cod_conducID").focus().after("<span id='loading'> <img src=\'../" + standa + "/imagenes/ajax-loader3.gif\' /></span>");
        },
        success: function(data) {
          $("#loading").remove();
          if (data == "no") {
            FormInsertNewConduc(num_conduc, cod_transp);
          } else {
            setLabelConduc(num_conduc);
          }
        }
      });
    }
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function FormInsertNewConduc(num_conduc, cod_transp) {
  try {
    var standa = $("#standaID").val();
    $("#PopUpID").dialog({
      modal: true,
      resizable: false,
      draggable: false,
      title: " Selecci\xf3n de Conductor",
      width: $(document).width() - 200,
      heigth: 500,
      position: ['middle', 25],
      bgiframe: true,
      closeOnEscape: false,
      show: {
        effect: "drop",
        duration: 300
      },
      hide: {
        effect: "drop",
        duration: 300
      }
    });
    $.ajax({
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: 'standa=' + standa + '&option=FormInsertNewConduc&cod_transp=' + cod_transp + '&cod_conduc=' + num_conduc,
      method: 'POST',
      beforeSend: function() {
        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function(data) {
        $("#PopUpID").html(data);
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function InsertConduc() {
  try {
    if (validateConductor()) {
      SaveNewConductor();
    }
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function SaveNewConductor() {
  try {
    // PARAMETROS----------------------------------
    var params = 'option=SaveNewConductor';
    var standa = $("#standaID").val();
    params += '&standa=' + standa;
    var transp = $("#transpID").val();
    params += '&transp=' + transp;
    var num_doccon = $("#num_docconID").val();
    params += '&num_doccon=' + num_doccon;
    var num_divcon = $("#num_divconID").val();
    params += '&num_divcon=' + num_divcon;
    var tip_doccon = $("#tip_docconID").val();
    params += '&tip_doccon=' + tip_doccon;
    var nom_tercon = $("#nom_terconID").val();
    params += '&nom_tercon=' + nom_tercon;
    var ape_tercon = $("#ape_terconID").val();
    params += '&ape_tercon=' + ape_tercon;
    var cel_tercon = $("#cel_terconID").val();
    params += '&cel_tercon=' + cel_tercon;
    var ema_tercon = $("#ema_terconID").val();
    params += '&ema_tercon=' + ema_tercon;
    var lic_conduc = $("#lic_conducID").val();
    params += '&lic_conduc=' + lic_conduc;
    var fec_venlic = $("#fec_venlicID").val();
    params += '&fec_venlic=' + fec_venlic;
    //---------------------------------------------
    $.ajax({
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: params,
      method: 'POST',
      beforeSend: function() {
        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function(data) {
        $("#PopUpID").dialog('close');
        $("#nom_nomconID").html("&nbsp;" + data);
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function encode_utf8(s) {
  return unescape(encodeURIComponent(s));
}

function setLabelConduc(cod_conduc) {
  try {
    $("#loading").remove();
    var standa = $("#standaID").val();
    $.ajax({
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: 'standa=' + standa + '&option=setLabelConduc&num_conduc=' + cod_conduc,
      method: 'POST',
      beforeSend: function() {
        $("#cod_conducID").focus().after("<span id='loading'> <img src=\'../" + standa + "/imagenes/ajax-loader3.gif\' /></span>");
      },
      success: function(data) {
        $("#loading").remove();
        $("#nom_nomconID").html("&nbsp;" + data);
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function newVehiculo() {
  try {
    var standa = $("#standaID").val();
    var cod_transp = $("#cod_transpID").val();
    $.ajax({
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: 'standa=' + standa + '&option=FormnewVehiculo&cod_transp=' + cod_transp,
      method: 'POST',
      beforeSend: function() {
        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function(data) {
        $("#PopUpID").html(data);
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function InsertVehiculo() {
  var standa = $("#standaID").val();
  var exp_placa = /[a-zA-Z]{3}[0-9]{3}/;
  var nue_placax = $("#nue_placaxID");
  var cod_marcax = $("#cod_marcaxID");
  var cod_lineax = $("#cod_lineaxID");
  var cod_colorx = $("#cod_colorxID");
  var cod_carroc = $("#cod_carrocID");
  var cod_config = $("#cod_configID");
  var val_capaci = $("#val_capaciID");
  var num_modelo = $("#num_modeloID");
  // var num_remolq = $("#num_remolqID");
  var num_soatxx = $("#num_soatxxID");
  var nom_asesoa = $("#nom_asesoaID");
  var fec_vencim = $("#fec_vencimID");
  $("#loading").remove();
  var tip_perpro = true;
  var des_perpro = '';
  $("input[name=tip_perpro]").each(function() {
    if ($(this).is(":checked")) {
      tip_perpro = false;
      des_perpro = $(this).val();
    }
  });
  var tip_perten = true;
  var des_perten = '';
  $("input[name=tip_perten]").each(function() {
    if ($(this).is(":checked")) {
      tip_perten = false;
      des_perten = $(this).val();
    }
  });
  //alert( des_perpro );

  if (!nue_placax.val() || nue_placax.val() == '') {
    $("#loading").remove();
    nue_placax.focus().after("<span id='loading'>-> Digite la Placa</span>");
    return false;
  } else if (!nue_placax.val().match(exp_placa)) {
    $("#loading").remove();
    nue_placax.focus().after("<span id='loading'>-> Formato Incoorecto (AAA000)</span>");
    return false;
  } else if (!cod_marcax.val() || cod_marcax.val() == '') {
    $("#loading").remove();
    cod_marcax.focus().after("<span id='loading'>-> Seleccione la Marca</span>");
    return false;
  } else if (!cod_lineax.val() || cod_lineax.val() == '') {
    $("#loading").remove();
    cod_lineax.focus().after("<span id='loading'>-> Seleccione la L&iacute;nea</span>");
    return false;
  } else if (!cod_colorx.val() || cod_colorx.val() == '') {
    $("#loading").remove();
    cod_colorx.focus().after("<span id='loading'><br>-> Seleccione el Color</span>");
    return false;
  } else if (!cod_carroc.val() || cod_carroc.val() == '') {
    $("#loading").remove();
    cod_carroc.focus().after("<span id='loading'>-> Seleccione la Carrocer&iacute;a</span>");
    return false;
  } else if (!cod_config.val() || cod_config.val() == '') {
    $("#loading").remove();
    cod_config.focus().after("<span id='loading'>-> Seleccione la Configuraci&oacute;n</span>");
    return false;
  } else if (!val_capaci.val() || val_capaci.val() == '') {
    $("#loading").remove();
    val_capaci.focus().after("<span id='loading'>-> Digite la Capacidad</span>");
    return false;
  } else if (!num_modelo.val() || num_modelo.val() == '') {
    $("#loading").remove();
    num_modelo.focus().after("<span id='loading'>-> Digite el Modelo</span>");
    return false;
  }
  // else if( !num_remolq.val() || num_remolq.val() == '' )  // {
  // $("#loading").remove();
  // num_remolq.focus().after("<span id='loading'>-> Seleccione el Remolque</span>");
  // return false;
  // }
  else if (!num_soatxx.val() || num_soatxx.val() == '') {
    $("#loading").remove();
    num_soatxx.focus().after("<span id='loading'>-> Digite el SOAT</span>");
    return false;
  } else if (!nom_asesoa.val() || nom_asesoa.val() == '') {
    $("#loading").remove();
    nom_asesoa.focus().after("<span id='loading'>-> Digite la Aseguradora</span>");
    return false;
  } else if (!fec_vencim.val() || fec_vencim.val() == '') {
    $("#loading").remove();
    fec_vencim.focus().after("<span id='loading'>-> Fecha de Vencimiento SOAT</span>");
    return false;
  }
  var valProp = true;
  var valTene = true;
  var propietario = $("#con_propieID").val();
  if (propietario != 1) {
    valProp = validatePropietario(des_perpro)
    if (tip_perpro) {
      $("#loading").remove();
      alert("Seleccione un tipo de Persona para el Propietario");
      return false;
    }
  } else {
    tip_perpro = false;
  }
  var tenedor = $("#con_poseedID").val();
  if (tenedor != 1) {
    valTene = validateTenedor(des_perten);
    if (tip_perten) {
      $("#loading").remove();
      alert("Seleccione un tipo de Persona para el Tenedor");
      return false;
    }
  } else {
    tip_perten = false;
  }

  if (!tip_perpro && valProp && !tip_perten && valTene && validateConductor()) {
    SaveNewVehiculo();
  } 

}

function validateConductor() {
  $("#loading").remove();
  var email = /^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
  var tip_doccon = $("#tip_docconID");
  var num_doccon = $("#num_docconID");
  var num_divcon = $("#num_divconID");
  var cel_tercon = $("#cel_terconID");
  var ema_tercon = $("#ema_terconID");
  var nom_tercon = $("#nom_terconID");
  var ape_tercon = $("#ape_terconID");
  var lic_conduc = $("#lic_conducID");
  var fec_venlic = $("#fec_venlicID");
  if (!num_doccon.val() || num_doccon.val() == '') {
    $("#loading").remove();
    num_divcon.after("<span id='loading'>-> Digite N&uacute;mero Documento</span>");
    num_doccon.focus();
    return false;
  } else if (!tip_doccon.val() || tip_doccon.val() == '') {
    $("#loading").remove();
    tip_doccon.focus().after("<span id='loading'>-> Seleccione Tipo Documento</span>");
    return false;
  } else if (!nom_tercon.val() || nom_tercon.val() == '') {
    $("#loading").remove();
    nom_tercon.focus().after("<span id='loading'>-> Digite el Nombre del Conductor</span>");
    return false;
  } else if (!ape_tercon.val() || ape_tercon.val() == '') {
    $("#loading").remove();
    ape_tercon.focus().after("<span id='loading'>-> Digite el Apellido del Conductor</span>");
    return false;
  } else if (!cel_tercon.val() || cel_tercon.val() == '') {
    $("#loading").remove();
    cel_tercon.focus().after("<span id='loading'>-> Celular del Conductor</span>");
    return false;
  } else if (!ema_tercon.val() || ema_tercon.val() == '') {
    $("#loading").remove();
    ema_tercon.focus().after("<span id='loading'>-> Correo del Conductor</span>");
    return false;
  }
  /*else if( !email.test( ema_tercon.val() ) )  {
   $("#loading").remove();
   ema_tercon.focus().after("<span id='loading'>-> Correo Incorrecto (prueba@prueba.com)</span>");
   return false;
   }*/
  else if (!lic_conduc.val() || lic_conduc.val() == '') {
    $("#loading").remove();
    lic_conduc.focus().after("<span id='loading'>-> Numero Licencia</span>");
    return false;
  } else if (!fec_venlic.val() || fec_venlic.val() == '') {
    $("#loading").remove();
    fec_venlic.focus().after("<span id='loading'>-> Fecha Vencimiento</span>");
    return false;
  } else {
    return true;
  }
}

function validateTenedor(des_perten) {
  var email = /^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
  $("#loading").remove();
  var tip_docten = $("#tip_doctenID");
  var num_docten = $("#num_doctenID");
  var num_divten = $("#num_divtenID");
  var cel_terten = $("#cel_tertenID");
  var ema_terten = $("#ema_tertenID");
  if (!num_docten.val() || num_docten.val() == '') {
    $("#loading").remove();
    num_divten.after("<span id='loading'>-> Digite N&uacute;mero Documento</span>");
    num_docten.focus();
    return false;
  } else if (!tip_docten.val() || tip_docten.val() == '') {
    $("#loading").remove();
    tip_docten.focus().after("<span id='loading'>-> Seleccione Tipo Documento</span>");
    return false;
  }

  if (des_perten == 'N') {
    var nom_terten = $("#nom_terproID");
    if (!nom_terten.val() || nom_terten.val() == '') {
      $("#loading").remove();
      nom_terten.focus().after("<span id='loading'>-> Digite el Nombre del Poseedor</span>");
      return false;
    }
  } else {
    var nom_terten = $("#nom_tertenID");
    var ape_terten = $("#ape_tertenID");
    if (!nom_terten.val() || nom_terten.val() == '') {
      $("#loading").remove();
      nom_terten.focus().after("<span id='loading'>-> Digite el Nombre del Poseedor</span>");
      return false;
    } else if (!ape_terten.val() || ape_terten.val() == '') {
      $("#loading").remove();
      ape_terten.focus().after("<span id='loading'>-> Digite el Apellido del Poseedor</span>");
      return false;
    }
  }

  if (!cel_terten.val() || cel_terten.val() == '') {
    $("#loading").remove();
    cel_terten.focus().after("<span id='loading'>-> Celular del Poseedor</span>");
    return false;
  } else if (!ema_terten.val() || ema_terten.val() == '') {
    $("#loading").remove();
    ema_terten.focus().after("<span id='loading'>-> Correo del Poseedor</span>");
    return false;
  }
  /*else if( !email.test( ema_terten.val() ) )  {
   $("#loading").remove();
   ema_terten.focus().after("<span id='loading'>-> Correo Incorrecto (prueba@prueba.com)</span>");
   return false;
   }*/
  else {
    return true;
  }
}

function validatePropietario(des_perpro) {
  $("#loading").remove();
  var email = /^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
  var tip_docpro = $("#tip_docproID");
  var num_docpro = $("#num_docproID");
  var num_divpro = $("#num_divproID");
  var cel_terpro = $("#cel_terproID");
  var ema_terpro = $("#ema_terproID");
  if (!num_docpro.val() || num_docpro.val() == '') {
    $("#loading").remove();
    num_divpro.after("<span id='loading'>-> Digite N&uacute;mero Documento</span>");
    num_docpro.focus();
    return false;
  } else if (!tip_docpro.val() || tip_docpro.val() == '') {
    $("#loading").remove();
    tip_docpro.focus().after("<span id='loading'>-> Seleccione Tipo Documento</span>");
    return false;
  }

  if (des_perpro == 'N') {
    var nom_terpro = $("#nom_terproID");
    if (!nom_terpro.val() || nom_terpro.val() == '') {
      $("#loading").remove();
      nom_terpro.focus().after("<span id='loading'>-> Digite el Nombre del Propietario</span>");
      return false;
    }
  } else {
    var nom_terpro = $("#nom_terproID");
    var ape_terpro = $("#ape_terproID");
    if (!nom_terpro.val() || nom_terpro.val() == '') {
      $("#loading").remove();
      nom_terpro.focus().after("<span id='loading'>-> Digite el Nombre del Propietario</span>");
      return false;
    } else if (!ape_terpro.val() || ape_terpro.val() == '') {
      $("#loading").remove();
      ape_terpro.focus().after("<span id='loading'>-> Digite el Apellido del Propietario</span>");
      return false;
    }
  }

  if (!cel_terpro.val() || cel_terpro.val() == '') {
    $("#loading").remove();
    cel_terpro.focus().after("<span id='loading'>-> Celular del Propietario</span>");
    return false;
  } else if (!ema_terpro.val() || ema_terpro.val() == '') {
    $("#loading").remove();
    ema_terpro.focus().after("<span id='loading'>-> Correo del Propietario</span>");
    return false;
  }
  /*else if( !email.test( ema_terpro.val() ) )  {
   $("#loading").remove();
   ema_terpro.focus().after("<span id='loading'>-> Correo Incorrecto (prueba@prueba.com)</span>");
   return false;
   }*/
  else {
    return true;
  }
}

function ValidateVehiculo(num_placax) {
  try {
    $("#loading").remove();
    var standa = $("#standaID").val();
    var cod_transp = $("#cod_transpID").val();
    $.ajax({
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: 'standa=' + standa + '&option=ValidateVehiculo&cod_transp=' + cod_transp + '&num_placax=' + num_placax.value,
      method: 'POST',
      beforeSend: function() {
        $("#nue_placaxID").focus().after("<span id='loading'> <img src=\'../" + standa + "/imagenes/ajax-loader3.gif\' /></span>");
      },
      success: function(data) {
        $("#loading").remove();
        if (data == 'existe') {
          $("#nue_placaxID").focus().after("<span id='loading'>* La placa ya se encuentra registrada</span>");
          $("#nue_placaxID").focus();
          return false;
        } else if (data == 'asignar') {
          FormAsignacionVehiculo(cod_transp, num_placax.value);
          return false;
        }
        /*else
                           {
                           alert(data);
                           }*/
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }

}

function SaveNewVehiculo() {
  try {
    var des_perpro = '';
    $("input[name=tip_perpro]").each(function() {
      if ($(this).is(":checked")) {
        des_perpro = $(this).val();
      }
    });
    var des_perten = '';
    $("input[name=tip_perten]").each(function() {
      if ($(this).is(":checked")) {
        des_perten = $(this).val();
      }
    });
    // PARAMETROS----------------------------------
    var params = 'option=SaveNewVehiculo';
    var standa = $("#standaID").val();
    params += '&standa=' + standa;
    var transp = $("#cod_transpID").val();
    params += '&transp=' + transp;
    var num_placax = $("#nue_placaxID").val();
    params += '&num_placax=' + num_placax;
    var cod_marcax = $("#cod_marcaxID").val();
    params += '&cod_marcax=' + cod_marcax;
    var cod_lineax = $("#cod_lineaxID").val();
    params += '&cod_lineax=' + cod_lineax;
    var cod_colorx = $("#cod_colorxID").val();
    params += '&cod_colorx=' + cod_colorx;
    var cod_carroc = $("#cod_carrocID").val();
    params += '&cod_carroc=' + cod_carroc;
    var cod_config = $("#cod_configID").val();
    params += '&cod_config=' + cod_config;
    var val_capaci = $("#val_capaciID").val();
    params += '&val_capaci=' + val_capaci;
    var num_modelo = $("#num_modeloID").val();
    params += '&num_modelo=' + num_modelo;
    // var num_remolq = $("#num_remolqID").val();
    // params += '&num_remolq=' + num_remolq;

    var num_soatxx = $("#num_soatxxID").val();
    params += '&num_soatxx=' + num_soatxx;
    var nom_asesoa = $("#nom_asesoaID").val();
    params += '&nom_asesoa=' + nom_asesoa;
    var fec_vencim = $("#fec_vencimID").val();
    params += '&fec_vencim=' + fec_vencim;
    var num_docpro = $("#num_docproID").val();
    params += '&num_docpro=' + num_docpro;
    var num_divpro = $("#num_divproID").val();
    params += '&num_divpro=' + num_divpro;
    var tip_docpro = $("#tip_docproID").val();
    params += '&tip_docpro=' + tip_docpro;
    params += '&des_perpro=' + des_perpro;
    if (des_perpro == 'N ') {
      var nom_terpro = $("#nom_terproID").val();
      params += '&nom_terpro=' + nom_terpro;
    } else {
      var nom_terpro = $("#nom_terproID").val();
      params += '&nom_terpro=' + nom_terpro;
      var ape_terpro = $("#ape_terproID").val();
      params += '&ape_terpro=' + ape_terpro;
    }
    var cel_terpro = $("#cel_terproID").val();
    params += '&cel_terpro=' + cel_terpro;
    var ema_terpro = $("#ema_terproID").val();
    params += '&ema_terpro=' + ema_terpro;
    var num_docten = $("#num_doctenID").val();
    params += '&num_docten=' + num_docten;
    var num_divten = $("#num_divtenID").val();
    params += '&num_divten=' + num_divten;
    var tip_docten = $("#tip_doctenID").val();
    params += '&tip_docten=' + tip_docten;
    params += '&des_perten=' + des_perten;
    if (des_perten == 'N ') {
      var nom_terten = $("#nom_tertenID").val();
      params += '&nom_terten=' + nom_terten;
    } else {
      var nom_terten = $("#nom_tertenID").val();
      params += '&nom_terten=' + nom_terten;
      var ape_terten = $("#ape_tertenID").val();
      params += '&ape_terten=' + ape_terten;
    }
    var cel_terten = $("#cel_tertenID").val();
    params += '&cel_terten=' + cel_terten;
    var ema_terten = $("#ema_tertenID").val();
    params += '&ema_terten=' + ema_terten;
    var num_doccon = $("#num_docconID").val();
    params += '&num_doccon=' + num_doccon;
    var num_divcon = $("#num_divconID").val();
    params += '&num_divcon=' + num_divcon;
    var tip_doccon = $("#tip_docconID").val();
    params += '&tip_doccon=' + tip_doccon;
    var nom_tercon = $("#nom_terconID").val();
    params += '&nom_tercon=' + nom_tercon;
    var ape_tercon = $("#ape_terconID").val();
    params += '&ape_tercon=' + ape_tercon;
    var cel_tercon = $("#cel_terconID").val();
    params += '&cel_tercon=' + cel_tercon;
    var ema_tercon = $("#ema_terconID").val();
    params += '&ema_tercon=' + ema_tercon;
    var lic_conduc = $("#lic_conducID").val();
    params += '&lic_conduc=' + lic_conduc;
    var fec_venlic = $("#fec_venlicID").val();
    params += '&fec_venlic=' + fec_venlic;
    params += '&con_propie=' + $("#con_propieID").val();
    params += '&con_poseed=' + $("#con_poseedID").val();
    
    //---------------------------------------------
    $.ajax({
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: params,
      method: 'POST',
      beforeSend: function() {
        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function(data) {
        //console.log( data );
        //$("#PopUpID").html( data );
        PopupVehiculos();
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function SaveAsignVehicu(cod_transp, num_placax) {
  try {
    var standa = $("#standaID").val();
    $.ajax({
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: 'standa=' + standa + '&option=SaveAsignVehicu&cod_transp=' + cod_transp + '&num_placax=' + num_placax,
      method: 'POST',
      beforeSend: function() {
        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function(data) {
        PopupVehiculos();
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function FormAsignacionVehiculo(cod_transp, num_placax) {
  try {
    var standa = $("#standaID").val();
    $.ajax({
      url: "../" + standa + "/desnew/ajax_desnew_despac.php",
      data: 'standa=' + standa + '&option=FormAsignacionVehiculo&cod_transp=' + cod_transp + '&num_placax=' + num_placax,
      method: 'POST',
      beforeSend: function() {
        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function(data) {
        $("#PopUpID").html(data);
      }
    });
  } catch (e) {
    console.log(e.message);
    return false;
  }
}

function SetZeros(fValue, fZeros) {
  var fCdr = '';
  var fExit = 0;
  var i = 1;
  while (!fExit) {
    var fSize = fValue.length + i;
    if (i > fZeros || fSize > fZeros)
      fExit = 1;
    else
      fCdr = fCdr + '0';
    i++;
  }
  fValue = fCdr + fValue;
  return String(fValue);
}

function GenerateDV(fValue) {
  if (!fValue)
    return '';
  var fFormat = new Array(71, 67, 59, 53, 47, 43, 41, 37, 29, 23, 19, 17, 13, 7, 3);
  fValue = SetZeros(String(fValue), 15);
  var fTemp = 0;
  for (var i = 0; i <= 14; i++) {
    fTemp += fValue.substring(i, i + 1) * fFormat[i];
  }
  var fCdr = fTemp % 11;
  var fReturn = (fCdr == 0 || fCdr == 1) ? fCdr : 11 - fCdr;
  return fReturn;
}

function otroSitioCargue() {
  var cantidad = parseInt($("#cantidadID").val()) + 1;
  var tabla = '<hr>';
  tabla += '<table width="100%"  cellspacing="0" cellpadding="0" border="0">';
  tabla += '<tr>';
  tabla += '<td align="right" width="20%" class="label-tr">Fecha de cita de cargue: </td>';
  tabla += '<td class="label-tr"><input type="text" id="fec_citcar' + cantidad + '" readonly  name="fec_citcar[' + cantidad + ']" obl="1" validate="date" maxlength="10" minlength="10"></td>';
  tabla += '<td align="right" width="20%" class="label-tr">hora de cita de cargue:</td>';
  tabla += '<td class="label-tr"><input type="text" id="hor_citar' + cantidad + '" readonly  name="hor_citar[' + cantidad + ']" obl="1" validate="hora" maxlength="5" minlength="5"></td>';
  tabla += '</tr>';
  tabla += '<tr>';
  tabla += '<td align="right" width="20%" class="label-tr">Sitio de Cargue</td>';
  tabla += '<td class="label-tr"><input type="text" id="sit_cargue' + cantidad + '" name="sit_cargue[' + cantidad + ']" obl="1" validate="dir" maxlength="100" minlength="4"></td>';
  tabla += '<td class="label-tr" colspan="2"></td>';
  tabla += '</tr>';
  tabla += '</table>';
  $(tabla).appendTo('#tableCargue').hide().show("slow");
  $("#datcarID").parent().css("height", "auto");
  $("#cantidadID").val(cantidad);
  $("#fec_citcar" + cantidad).datepicker();
  $("#fec_citcar" + cantidad).datepicker('option', {
    dateFormat: 'yy-mm-dd'
  });
  $("#hor_citar" + cantidad).timepicker({
    showSecond: false
  });
}