 function completeClients()
{
    var Standa = $("#StandaID").val();
         var clientes = [];
         var nombres = [];
        $("#PopDestinPadres").find("input[type='checkbox']:checked").each(function(){

            clientes.push($("#PopDestinPadres #DLCell"+$(this).val()+"-1").html());
            nombres.push($("#PopDestinPadres #DLCell"+$(this).val()+"-2").html());
        }); 
 
    $("#loadCliente").autocomplete({
        source: "../" + Standa + "/desnew/ajax_despac_destin.php?option=getCliente&standa=" + Standa + "&clientes="+clientes+ "&nombres="+nombres, 
        minLength: 2,
        delay: 100,
        select: function( event, ui ) {
 
            var cod_client = ui.item.label.split("-")[0].trim();
            var current = 0;
            $("#tabClientes input[type='checkbox']").each(function(){
                current++;
            });
            current++;
            $.ajax({
            url: "../" + Standa + "/desnew/ajax_despac_destin.php",
            data: 'standa=' + Standa + '&option=addClient&cod_client=' + cod_client + "&current=" + current,
            method: 'POST',
                
            success:
                    function (data)
                    {
                        $("#tabClientes").append(data);
                        $("[name='fecha']").datepicker();
                        $("[name='hora']").timepicker();
                    }
            });
      }
    });
}
 

function mainList()
{
    try
    {
        var Standa = $("#StandaID").val();
        var fec_inicia = $("#fec_iniciaID").val();
        var fec_finali = $("#fec_finaliID").val();

        $.ajax({
            type: "POST",
            url: "../" + Standa + "/desnew/ajax_despac_destin.php",
            data: "option=mainList&Standa=" + Standa + "&fec_inicia=" + fec_inicia + "&fec_finali=" + fec_finali,
            async: false,
            beforeSend:
                    function ()
                    {
                        $("#mainListID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader.gif" /></td></tr><tr><td></td></tr></table>');
                    },
            success:
                    function (data)
                    {
                        $("#mainListID").html(data);
                    }
        });
    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}

function SetDestinatarios(num_despac)
{
    try
    {

        var Standa = $("#StandaID").val();
        $("#num_despacID").val(num_despac.text());
        var viajex = num_despac.parent().next().text() ; 
        // console.log( $("#num_despacID") );
        $("#PopUpID").dialog({
            modal: true,
            resizable: false,
            draggable: false,
            title: "Destinatarios del Despacho No." + num_despac.text(),
            width: $(document).width() - 400,
            heigth: 500,
            position: ['middle', 25],
            bgiframe: true,
            closeOnEscape: false,
            show: {effect: "drop", duration: 300},
            hide: {effect: "drop", duration: 300},
            open: function (event, ui)
            {
                $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
            }
        });

        $.ajax({
            url: "../" + Standa + "/desnew/ajax_despac_destin.php",
            data: 'standa=' + Standa + '&option=SetDestinatarios&num_despac=' + num_despac.text() + "&num_viajex=" + viajex,
            method: 'POST',
            beforeSend:
                    function ()
                    {
                        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
                    },
            success:
                    function (data)
                    {
                        $("#PopUpID").html(data);
                    }
        });
    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}

// - Funcion para cargar los clientes del despacho cuando no existen destinatario
function PopDestinPadres(){
        
try{

    var num_despac = $("#num_despacID").val();
    var viajex = $("#num_viajex").val();
    var Standa = $("#StandaID").val();
    $("<div id=\"PopDestinPadres\"></div>").dialog({
        modal: true,
        resizable: false,
        draggable: false,
        title: "Clientes del Despacho No." + num_despac,
        width: $(document).width() -1000 ,
        heigth: 100,
        position: ['middle', 25],
        
        closeOnEscape: false,
        show: {effect: "drop", duration: 300},
        hide: {effect: "drop", duration: 300},
        open: function (event, ui)
        {
            $("#clientes").val("");
            $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
        },
        close: function(){
            $("#PopDestinPadres").dialog("destroy").remove(); 
        },
        buttons: {
            "Cerrar": function () {
                $(this).dialog("close");
            },
            "Aceptar": function (){
                var flagMessage = true;
                var message = "Le han faltado campos por DILIGENCIOR\npor favor revise: \n";
                var clientes = [];
                    $("#PopDestinPadres").find("input[type='checkbox']:checked").each(function(){
                        
                        if($("#PopDestinPadres #DLCell"+$(this).val()+"-3").children().val() == ''){
                            message += "\n-fecha del Cliente:  "+$("#PopDestinPadres #DLCell"+$(this).val()+"-2").html();
                            flagMessage = false;
                        }
                        if($("#PopDestinPadres #DLCell"+$(this).val()+"-4").children().val() == ''){
                            message += "\n-hora del Cliente:  "+$("#PopDestinPadres #DLCell"+$(this).val()+"-2").html();
                            flagMessage = false;
                        }

                        clientes.push($("#PopDestinPadres #DLCell"+$(this).val()+"-1").html()+"/"+$("#PopDestinPadres #DLCell"+$(this).val()+"-3").children().val()+"/"+$("#PopDestinPadres #DLCell"+$(this).val()+"-4").children().val());

                    }); 
 
                  if(!flagMessage){
                        alert(message);
                        return false;
                    }else{

                $("#clientes").val(clientes.join("|"));
                $(this).dialog("close");
                    }
            }
        }
        
    });

    $.ajax({
        url: "../" + Standa + "/desnew/ajax_despac_destin.php",
        data: 'standa=' + Standa + '&option=loadPopupPadres&num_despac=' + num_despac + "&num_viajex=" + viajex ,
        method: 'POST',
        beforeSend:  function ()
        {
            $("#PopDestinPadres").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
        },
        success:  function (data)
        {
            $("#PopDestinPadres").html(data);
            $("[name='fecha']").datepicker();
            $("[name='hora']").timepicker();
        }
    });
}catch(e)
    {
        console.log(e.message);
        return false;
    }

} 

function AjaxEliminar(destinatario)
{
    if(confirm("Desea Eliminar el destinatario?"))
    {
        try
        { 
            var num_despac = $("#num_despacID").val();
            var viajex = $("#num_viajex").val();
            var Standa = $("#StandaID").val();
            var client = $("#PopDestinPadres #DLCell"+destinatario+"-1").html();
    
            $.ajax({
                url: "../" + Standa + "/desnew/ajax_despac_destin.php",
                data: 'standa=' + Standa + '&option=AjaxEliminar&num_despac=' + num_despac + "&num_viajex=" + viajex + "&cod_destin=" + client ,
                method: 'POST',
/*                beforeSend:  function ()
                {
                    $("#PopDestinPadres").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
                },*/
                success:  function (data)
                {
                    $("#PopDestinPadres #DLRowInfo"+destinatario).remove();

                }
            });
        }
        catch(e)
        {
            console.log( "Error Fuction AjaxEliminar: "+e.message+"\nLine: "+e.lineNumber );
            return false;
        }
    }
}


function AddGrid()
{
    try
    {
        var Standa = $("#StandaID").val();
        var counter = $("#counterID").val();
        $("#loading").remove();

        if ($("#num_factur" + counter + "ID").val() == '')
        {
            $("#loading").remove();
            $("#num_factur" + counter + "ID").focus().after("<span id='loading'><br>Digite El Documento</span>");
            return false;
        }
        else if ($("#cod_ciudad" + counter + "ID").val() == '')
        {
            $("#loading").remove();
            $("#cod_ciudad" + counter + "ID").focus().after("<span id='loading'><br>Seleccione La Ciudad</span>");
            return false;
        }
        else if ($("#fec_citdes" + counter + "ID").val() == '')
        {
            $("#loading").remove();
            $("#fec_citdes" + counter + "ID").focus().after("<span id='loading'><br>Seleccione Fecha de Descargue</span>");
            return false;
        }
        else if ($("#hor_citcar" + counter + "ID").val() == '')
        {
            $("#loading").remove();
            $("#hor_citcar" + counter + "ID").focus().after("<span id='loading'><br>Seleccione Hora de Descargue</span>");
            return false;
        }
        else
        {

            $.ajax({
                type: "POST",
                url: "../" + Standa + "/desnew/ajax_despac_destin.php",
                data: "ind_ajax=1&option=ShowDestinNew&counter=" + (parseInt(counter) + 1),
                async: false,
                success: function (datos)
                {
                    $("#DestinID").append(datos);
                    $("#counterID").val((parseInt(counter) + 1));
                }
            });

            $('#datdesID').parent().css('height', 'auto');
        }

    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}
function addrow(fila) {
    var Standa = $("#StandaID").val();
    var genera = $("#hiddenIdGenera").val();
    var transp = $("#hiddenIdTransp").val();

    $.ajax({
        type: "POST",
        url: "../" + Standa + "/desnew/ajax_despac_destin.php",
        data: "ind_ajax=1&option=addRow&counter=" + (parseInt(fila) + 1) + "&cod_genera=" + genera + "&cod_transp=" + transp,
        async: false,
        success: function (datos)
        {
            //$("#divCeldas" + fila + "").appendTo(datos);
            $("#datdes" + fila + "ID table").next().append(datos);
        }
    });

}
function InserDestin()
{
    try
    {
        var Standa = $("#StandaID").val();
        var clientes = $("#clientes").val();
        var citcar = $("input[type=radio][name='ind_citaxx']:checked").val();
        var viajex = $("#num_viajex").val();
        var params = "option=InserDestin&Standa=" + Standa + "&num_despac=" + $("#num_despacID").val() + "&ind_citaxx=" + citcar + "&clientes=" + clientes + "&num_viajex=" + viajex;
        var mData = new Array(3);
        var bandera = "Ingrese los siguientes campos: \n";
        $("br").remove();
        $("div[id^='datdes']").each(function (index, obj) {
            if ($("#nom_destin" + index + "ID").val() != '') {
                params += "&mData[" + index + "][nom_destin]=" + $("#nom_destin" + index + "ID").val();
                if ($("#cod_ciudad" + index + "ID").val() != '') {
                    params += "&mData[" + index + "][cod_ciudad]=" + $("#cod_ciudad" + index + "ID").val();
                } else {
                    $(this).focus();
                    bandera += "CIUDAD " + (index + 1) + "\n";
                }
                params += "&mData[" + index + "][dir_destin]=" + $("#dir_destin" + index + "ID").val();
                params += "&mData[" + index + "][nom_contac]=" + $("#nom_contac" + index + "ID").val();
                if ($("#fec_citdes" + index + "ID").val() != '') {
                params += "&mData[" + index + "][fec_citdes]=" + $("#fec_citdes" + index + "ID").val();
                } else {
                    $(this).focus();
                    bandera += "FECHA CITA DESCARGUE " + (index + 1) + "\n";
                }                                
                if ($("#hor_citcar" + index + "ID").val() != '') {
                params += "&mData[" + index + "][hor_citdes]=" + $("#hor_citcar" + index + "ID").val();
                } else {
                    $(this).focus();
                    bandera += "HORA CITA DESCARGUE " + (index + 1) + "\n";
                }
                $(this).find("label[id^='cod_generaList']").each(function (index2, obj2) {
                    if ($(this).attr("value") != '') {
                        params += "&mData[" + index + "][factur" + index2+ "][cod_genera]=" + $(this).attr("value");
                    } else {
                        $(this).focus();
                        bandera += "GENERADOR " + (index + 1) + "\n";
                    }
                });
                $(this).find("input[id^='num_factura']").each(function (index2, obj2) {
                    params += "&mData[" + index + "][factur" + index2 + "][num_factura]=" + $(this).val();
                });
                $(this).find("input[id^='doc_alterna']").each(function (index2, obj2) {
                    params += "&mData[" + index + "][factur" + index2 + "][doc_alterna]=" + $(this).val();
                });
            }
        });
        if (bandera != "Ingrese los siguientes campos: \n") {
            alert(bandera);
            return false;
        }
        $.ajax({
            type: "POST",
            url: "../" + Standa + "/desnew/ajax_despac_destin.php",
            data: params,
            async: false,
            beforeSend:
                    function ()
                    {

                        $("#PopUpID").html('<table align="center"><tr><td><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /></td></tr><tr><td></td></tr></table>');
                    },
            success:
                    function (data)
                    {
                        $("#PopUpID").dialog("close");
                        mainList();
                        $("#ResultInsertID").html(data);
                    }
        });
    }
    catch (e)
    {
        console.log(e.message);
        return false;
    }
}


function DropGrid(div_id)
{
    $("#datdes" + div_id + "ID").remove();
    $("#counterID").val($("#DestinID > div").last().attr('id').substr(6).split("ID")[0]);
    // alert( $("#counterID").val() );
}