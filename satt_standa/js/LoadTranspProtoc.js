$(document).ready(function ()
{
    var Standa = $("#StandaID").val();
    var filter = $("#filterID").val();

    $("#cod_transpID").autocomplete({
        source: "../" + Standa + "/protoc/ajax_protoc_transp.php?option=getTransp&standa=" + Standa + "&filter=" + filter,
        minLength: 2,
        delay: 100
    });
});


function ValidateTransp()
{
    try
    {
        var Standa = $("#StandaID").val();
        var filter = $("#filterID").val();
        var transp = $("#cod_transpID").val();
        if (transp == '')
        {
            alert("Digite la Transportadora");
            $("#cod_transpID").focus();
            return false;
        }
        else
        {
            var cod_transp = transp.split("-")[0].trim();

            $.ajax({
                type: "POST",
                url: "../" + Standa + "/protoc/ajax_protoc_transp.php",
                data: "option=ValidateTransp&cod_transp=" + cod_transp + "&filter=" + filter,
                async: false,
                success:
                        function (data)
                        {
                            if (data == 'n')
                            {
                                alert("La Transportadora no Existe");
                                $("#cod_transpID").focus();
                                return false;
                            }
                            else
                            {
                                ShowMainList(cod_transp);
                            }
                        }
            });
        }

    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }

}

function ShowMainList(cod_transp)
{
    try
    {
        var Standa = $("#StandaID").val();
        $("#transpID").val(cod_transp);

        $("#resultID").css({'background-color': '#f0f0f0', 'border': '1px solid #c9c9c9', 'padding': '5px', 'width': '98%', 'min-height': '50px', '-moz-border-radius': '5px 5px 5px 5px', '-webkit-border-radius': '5px 5px 5px 5px', 'border-top-left-radius': '5px', 'border-top-right-radius': '5px', 'border-bottom-right-radius': '5px', 'border-bottom-left-radius': '5px'});
        $.ajax({
            type: "POST",
            url: "../" + Standa + "/protoc/ajax_protoc_transp.php",
            data: "option=ShowMainList&standa=" + Standa + "&cod_transp=" + cod_transp,
            async: false,
            beforeSend: function ()
            {
                $("#resultID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
            },
            success: function (datos)
            {
                $("#resultID").html(datos); 
                $('.ui-accordion-content').css('width','96%');
            }
        });
    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}

function addTab()
{
    try
    {
        var Standa = $("#StandaID").val();
        var tabs = $("#tabs").accordion({collapsible: true, active: false});
        var noveda = $("#novedaID").val().split("(")[0];
        var size = noveda.length >= 20 ? 'style="font-size:9px;"' : '';
        if (noveda.length >= 25)
        {
            size = 'style="font-size:8px;"';
        }
        var tabTemplate = "<li aria-controls='#{aria}'><a " + size + "href='#{href}'>#{label}</a><span class='ui-icon ui-icon-close' role='presentation'></span></li>";
        var id = "tabs-" + noveda.split("-")[0].trim();

        if ($("#tabs").find("#" + id).val() == '')
        {
            alert("La novedad ya existe");
            return false;
        }
        else
        {
            $.ajax({
                type: "POST",
                url: "../" + Standa + "/protoc/ajax_protoc_transp.php",
                data: "option=getNewRow&standa=" + Standa + "&cod_noveda=" + noveda.split("-")[0].trim(),
                async: false,
                success: function (datos)
                {
                    var ContentTable = datos;
                    var tabContent = "<div>&nbsp;<br>" + noveda + "<br>&nbsp;</div><div  id='" + id + "'>" + ContentTable + "</div>";

                    tabs.append(tabContent);
                    tabs.accordion("destroy");

                    $("#tabs").accordion({collapsible: true, active: false});
                }
            });
        }
    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}
 
function NewNovedad()
{
    try
    {
        var Standa = $("#StandaID").val();

        $("#PopUpID").dialog({
            modal: true,
            resizable: false,
            draggable: false,
            title: "Selecci\xf3n de Novedad",
            width: $(document).width() - 700,
            heigth: 500,
            position: ['middle', 25],
            bgiframe: true,
            closeOnEscape: false,
            show: {effect: "drop", duration: 300},
            hide: {effect: "drop", duration: 300},
            open: function (event, ui) {
                $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            buttons: {
                Continuar: function ()
                {
                    addTab();
                    $(this).dialog('close');
                },
                Cerrar: function ()
                {
                    $(this).dialog('close');
                }
            }
        });

        $.ajax({
            type: "POST",
            url: "../" + Standa + "/protoc/ajax_protoc_transp.php",
            data: "option=NewNovedad&standa=" + Standa,
            async: false,
            beforeSend: function ()
            {
                $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
            },
            success: function (datos)
            {
                $("#PopUpID").html(datos);
            }
        });

    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}

// Funcion para cargar formulario de contactos
function ShowFormContac() {
    try
    {
        // variables

        var Standa = $("#StandaID").val();
        var cod_transp = $("#transpID").val();
        var attr = 'option=ShowFormContac&Standa=' + Standa + '&cod_transp=' + cod_transp;

        // Cargar el PopUp
        LoadPopup('open');
        $.ajax({
            url: "../" + Standa + "/protoc/ajax_protoc_transp.php",
            type: "POST",
            data: attr,
            beforeSend: function () {
                $("#FormContacID").html("<center>Cargando Formulario...</center>");
            },
            success: function (data) {
                $("#FormContacID").html(data);
            }
        });

    }
    catch (e)
    {
        alert("Error en:ShowFormContac " + e.message + "\nLine: " + e.lineNumber);
    }

}


function LoadPopup(type)
{
    try
    {
        if (type == 'open')
        {

            $('<div id="FormContacID" width="100%"><center>Cargando...</center></div>').dialog({
                width: 650,
                heigth: 50,
                modal: true,
                closeOnEscape: false,
                resizable: false,
                draggable: false,
                close: function () {
                    $("#FormContacID").dialog("destroy").remove();
                },
                buttons: {
                    aceptar: function () {
                        var correo = $("#divPopup #pop_emailxID").val();
                        var celular = $("#divPopup #pop_celtacID").val();
                        var nombre = "";
                        var cargo = "";
                        var expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

                        if (!expr.test(correo))
                        {
                            alert('Atenci\xf3n: Correo Actual \n Direcci\xf3n de Correo "' + correo + '" Incorrecta', correo);
                            return false;
                        }
                        else if (!/^([0-9])*$/.test(celular)) {
                            alert("El valor " + celular + " no es un número");
                            return false;
                        }
                        var messagesPopup = "Se requieren los siguientes campos \n";

                        if ($("#divPopup #pop_contacID").val() != "") {
                            nombre = $("#divPopup #pop_contacID").val();
                        } else {
                            messagesPopup += "- Contacto Principal \n";
                        }
                        if ($("#divPopup #pop_emailxID").val() != "") {
                            correo = $("#divPopup #pop_emailxID").val();
                        } else {
                            messagesPopup += "- E-mail \n";
                        }
                        if ($("#divPopup #pop_celtacID").val() != "") {
                            celular = $("#divPopup #pop_celtacID").val();
                        } else {
                            messagesPopup += "- Celular \n";
                        }
                        if ($("#divPopup #pop_cargoxID").val() != "") {
                            cargo = $("#divPopup #pop_cargoxID").val();
                        } else {
                            messagesPopup += "- Cargo \n";
                        }
                        if (messagesPopup != "Se requieren los siguientes campos \n") {
                            alert(messagesPopup);
                            return false;
                        }
                        else {

                            sendData();
                            LoadPopup("close");

                        }

                    },
                    Cerrar: function () {
                        LoadPopup("close");
                    }
                }
            });
        }
        else
        {
            $("#FormContacID").dialog("destroy").remove();
        }

    }
    catch (e)
    {
        alert("Error en:ShowFormContac " + e.message + "\nLine: " + e.lineNumber);
    }

}

function sendData() {
    try
    {

        var correo = $("#divPopup #pop_emailxID").val();
        var celular = $("#divPopup #pop_celtacID").val();
        var nombre = $("#divPopup #pop_contacID").val();
        var cargo = $("#divPopup #pop_cargoxID").val();
        var agencia = $("#divPopup #cod_agenci").val();


        var Standa = $("#StandaID").val();
        var cod_transp = $("#transpID").val();
        var attr = 'option=FillFormContac&Standa=' + Standa + '&cod_transp=' + cod_transp + '&nom_contac=' + nombre + '&dir_correo=' + correo + '&num_telmov=' + celular + '&nom_cargox=' + cargo + "&cod_agenci=" + agencia;

        // Cargar el PopUp
        $.ajax({
            url: "../" + Standa + "/protoc/ajax_protoc_transp.php",
            type: "POST",
            data: attr,
            beforeSend: function () {

            },
            success: function (data) {
                $("#tableDataBody").append(data);
            }
        });

    }
    catch (e)
    {
        alert("Error en:ShowFormContac " + e.message + "\nLine: " + e.lineNumber);
    }
}

function deleterow(obj)
{
console.log(obj);
    $(obj).parent().parent().remove();


}


function SaveAllProtocols()
{
    try
    {
        var Standa = $("#StandaID").val();
        var cod_transp = $("#transpID").val();
        var novedads = new Array();

        var attr = 'option=SaveAllProtocols&Standa=' + Standa + '&cod_transp=' + cod_transp ;
        var i;
        var j;
        var k = 0;
        $(".item").each(function (iSelect, select) {
            $select = $(select);
            i = $select.attr("id").split("-")[1].trim();
            attr += '&noveda[' + i + ']=' + i;
            $select.find("option").each(function (iOption, option) {
               
                console.log($(option).val());
                j = $(option).val();
                novedads[ k ] = j;
                attr += '&noveda[' + i + '][' + j + ']=' + j;
                k++
            });
        });

        var nom_contacs = new Array();
        var dir_correos = new Array();
        var num_telmovs = new Array();
        var nom_cargosx = new Array();
        var ver_email = new Array();
        var nov_email = new Array();
        var cod_agenci = new Array();

        i = 0;

        $("input[id^='res_contacID']").each(function () {

            nom_contacs[ i ] = $(this).val();
            i++;

        });

        i = 0;

        $("input[id^='res_celtacID']").each(function () {

            num_telmovs[ i ] = $(this).val();
            i++;

        });

        i = 0;

        $("input[id^='res_cargoxID']").each(function () {

            nom_cargosx[ i ] = $(this).val();
            i++;

        });

        i = 0;

        $("input[id^='res_emailxID']").each(function () {

            dir_correos[ i ] =   $(this).val();
            i++;

        });        
        i = 0;

        $("input[id^='cod_agenciID']").each(function () {

            cod_agenci[ i ] =   $(this).val();
            i++;

        });

        i = 0;
 

        $("input[id^='ver_ema']:checked").each(function () {

            nov_email[ i ] = $(this).val()+"¬"+novedads[ i ];
            ver_email[ i ] = $(this).val();
            i++;

        });


        attr += "&nom_contacs=" + nom_contacs.join("|");
        attr += "&num_telmovs=" + num_telmovs.join("|");
        attr += "&nom_cargosx=" + nom_cargosx.join("|");
        attr += "&dir_correos=" + dir_correos.join("|");
        attr += "&ver_emailxx=" + ver_email.join("|");
        attr += "&nov_emailxx=" + nov_email.join("|");
        attr += "&cod_agenci=" + cod_agenci.join("|");
        attr += "&num_contact=" + dir_correos.length;

        $.ajax({
            type: "POST",
            url: "../" + Standa + "/protoc/ajax_protoc_transp.php",
            data: attr,
            async: false,
            beforeSend: function ()
            {
                $("#resultID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
            },
            success: function (datos)
            {
                $("#resultID").html(datos);
            }
        });
    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}

function DerogaProtocolo(num_id)
{
    $("#asi_protoc-" + num_id + "-ID option:selected").each(function () {
        mHtml = '<option value="' + $(this).val() + '">' + $(this).text() + '</option>';
        $(this).remove();
        $("#all_protoc" + num_id + "-ID").append(mHtml);
    });
}

function AsignaProtocolo(num_id)
{
    $("#all_protoc" + num_id + "-ID option:selected").each(function () {
        mHtml = '<option value="' + $(this).val() + '">' + $(this).text() + '</option>';
        $(this).remove();
        $("#asi_protoc-" + num_id + "-ID").append(mHtml);
    });
}
function deleteProtoc (cod_transp, cod_noveda){

  if(confirm("Desea Eliminar El protocolo")){

    var Standa = $( "#StandaID" ).val();
    var attr = "option=deleteProtoc&cod_transp="+cod_transp+"&cod_noveda="+cod_noveda;
    $.ajax({
      type: "POST",
      url: "../"+ Standa +"/protoc/ajax_protoc_transp.php",
      data: attr,
      async: false,
      beforeSend: function()
      {
        $("#resultID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
      },
      success: function( datos )
      {
        $("#resultID").html( datos );
      }
    });
  }
}