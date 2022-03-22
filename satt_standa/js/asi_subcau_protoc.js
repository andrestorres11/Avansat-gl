$(document).ready(function ()
{


    var countlist = false;
    $("input[type=button]").button();
    var Standa = $("#StandaID").val();

    $("#nom_protocID").autocomplete({
        source: "../" + Standa + "/protoc/ajax_asisub_protoc.php?option=getProtoc&standa=" + Standa,
        minLength: 2,
        delay: 100
    }).bind("autocompleteclose", function (event, ui) {
        ValidateProtoc();
    });

    $("#nom_emptraID").autocomplete({
        source: "../" + Standa + "/protoc/ajax_asisub_protoc.php?option=getEmptra&standa=" + Standa,
        minLength: 2,
        delay: 100
    }).bind("autocompleteclose", function (event, ui) {
        ValidateEmptra();
    });


       
     $("div[id^='DinamicList']").next().removeAttr("id").attr("id","DinamicList2Div").find(".DLTable tr").each(function(){
        $(this).removeAttr("onclick");
     }); 

});

function SendSubcausas()
{
    try
    {

      if ($("#nom_emptraID").val()) {

        var Standa = $("#StandaID").val();
        var cod_transp = $("#nom_emptraID").val();
        var orden = '';
        var verifi = '';

        $("#sortable").children().each(function () {
            orden += orden != '' ? '|' + $(this).attr("id") : $(this).attr("id");
        });

        $("#DinamicList2DIV input[type=checkbox][id^=DLCheck]:not([id^=DLCheckAll])").each(function(){
            if ($(this).is(':checked'))
            {
                valor = $(this).parent().next().text();
                verifi += verifi != '' ? ',' + valor : valor;
            }
            else{

                verifi += verifi != '' ? ',' + '0' : '0';
            }
        });

        var cod_protoc = $("#nom_protocID").val();

        $.ajax({
            type: "POST",
            url: "../" + Standa + "/protoc/ajax_asisub_protoc.php",
            data: "option=SendSubcausas&standa=" + Standa + "&cod_protoc=" + cod_protoc.split('-')[0] + "&orden=" + orden + "&cod_transp=" + cod_transp.split('-')[0] + "&cod_verify=" +verifi,
            async: false,
            success: function (datos)
            {
                if (datos == '1000')
                    Alerta("Correcto", "Las Subcausas fueron Parametrizadas Carrectamente", '', 'MainLoad');
                else
                    Alerta("Atenci\xf3n", "Las Subcausas No fueron Parametrizadas, Por favor Intente Nuevamente", '', 'MainLoad');
            }
        });

     }else{
      alert("Digite la empresa");
      return false;
     }
    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}

function ValidateProtoc()
{
    try
    {
        var Standa = $("#StandaID").val();
        var cod_protoc = $("#nom_protocID").val();

        if (!cod_protoc)
        {
            alert("Digite el Protocolo");
            return false;
        }
        else
        {
            $.ajax({
                type: "POST",
                url: "../" + Standa + "/protoc/ajax_asisub_protoc.php",
                data: "option=ValidateProtoc&standa=" + Standa + "&cod_protoc=" + cod_protoc.split('-')[0],
                async: false,
                success: function (datos)
                {
                    if (datos == 'y')
                    {
                        ShowForm();
                    }
                    else
                    {
                        alert("El Protocolo < " + cod_protoc + " > no existe");
                        return false;
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

 
function ValidateEmptra()
{
    try
    {
        var Standa = $("#StandaID").val();
        var cod_tercer = $("#nom_emptraID").val();

        if (!cod_tercer)
        {
            alert("Digite la transportadora");
            return false;
        }
        else
        {
            $.ajax({
                type: "POST",
                url: "../" + Standa + "/protoc/ajax_asisub_protoc.php",
                data: "option=ValidateEmptra&standa=" + Standa + "&cod_tercer=" + cod_tercer.split('-')[0],
                async: false,
                success: function (datos)
                {
                    if (datos != 'y')
                    {
                        alert("La transportadora < " + cod_tercer + " > no existe");
                        return false;
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

function SetSubcausas()
{
    try
    {
        var Standa = $("#StandaID").val();
        var cod_transp = $("#nom_emptraID").val();
        var checks = '';
        var valor;
        $("#DinamicListDIV input[type=checkbox][id^=DLCheck]:not([id^=DLCheckAll])").each(function () {
            if ($(this).is(':checked'))
            {
                valor = $(this).parent().next().text();
                checks += checks != '' ? ', ' + valor : valor;
            }
        });

        if (checks == '')
        {
            Alerta('Atenci\xf3n', 'Seleccione por lo Menos una Subcausa', '', '');
            return false;
        }
        else
        {
            blockScreen("Cargando...");

            console.log(checks);

            $.ajax({
                type: "POST",
                url: "../" + Standa + "/protoc/ajax_asisub_protoc.php",
                data: "option=SetSubcausas&standa=" + Standa + "&checks=" + checks + "&cod_transp=" + cod_transp.split('-')[0],
                async: false,
                success: function (datos)
                {
                    UnblockScreen();
                    $("#OrderID").html(datos);
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

function ShowForm( )
{
    var Standa = $("#StandaID").val();
    var cod_protoc = $("#nom_protocID").val();
    $("#DynamicID").show("blind");
}

function MainLoad()
{
    try
    {
        blockScreen("Cargando...");
        var Standa = $("#StandaID").val();

        $.ajax({
            type: "POST",
            url: "../" + Standa + "/protoc/ajax_asisub_protoc.php",
            data: "option=MainLoad&standa=" + Standa,
            async: false,
            success: function (datos)
            {
                UnblockScreen();
                $("#mainDiv").html(datos);
            }
        });
    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}

function blockScreen(msj)
{
    try
    {
        $.blockUI({
            css: {
                top: '10px',
                left: '',
                right: '10px',
                border: 'none',
                padding: '15px',
                backgroundColor: '#001100',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .7,
                color: '#fff'
            },
            centerY: 0,
            message: "<h1>" + msj + "</h1>"
        });
    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}

function UnblockScreen()
{
    try
    {
        $.unblockUI();
    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}

function Alerta(title, message, focus, adi_func)
{
    try
    {
        $("<div id='msgBox'>" + message + "</div>").dialog({
            modal: true,
            resizable: false,
            draggable: false,
            title: title,
            left: 190,
            open: function (event, ui) {
                $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
            },
            buttons: {
                Aceptar: function () {
                    $(this).dialog('destroy').remove();

                    if (adi_func == 'MainLoad')
                        MainLoad();

                    if (focus != '')
                    {
                        focus.focus();
                    }
                }
            }
        });
    }
    catch (e)
    {
        console.log(e.message);
    }
}