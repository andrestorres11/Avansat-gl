$(function () {
    $("#acordeonID").accordion({
        heightStyle: "content",
        collapsible: true,
    });

    $("#fec_iniciaID, #fec_finaliID").datepicker({
        changeMonth: true,
        changeYear: true
    });
    $("#contenido").css("height", "auto");

    $("#tabs").tabs({
        beforeLoad: function (event, ui) {
            ui.jqXHR.fail(function () {
                ui.panel.html("Cargado...");
            });
        }
    });

    $('.fila').hover(function () {
        $(this).css("backgraund-color", "#9AD9AE !important");
    }, function () {
        $(this).css("backgraund-color", "#FFFFFF !important");
    });
    $("#liGenera").click(function () {
        Validate("generaID");
    });

    $("#liTransi").click(function () {
        Validate("transiID");
    });

    $("#liCargue").click(function () {
        Validate("cargueID");
    });

    $("#liDescar").click(function () {
        Validate("descarID");
    });

});


function Validate(pestana)
{
    try
    {
        var Standa = $("#standaID").val();
        var fec_inicia = $("#fec_iniciaID").val();
        var fec_finali = $("#fec_finaliID").val();

        var date_inicia = new Date(fec_inicia + "T" + "00:00:00");
        var date_finali = new Date(fec_finali + "T" + "23:59:59");


        // capturar checkeados productos
        var cod_produc = [];
        $("input[name^='produc']:checked").each(function (i, v) {
            cod_produc[i] = $(this).val();
        });
        // capturar checkeados tipo de despacho
        var cod_tipdes = [];
        $("input[name^='tipdes']:checked").each(function (i, v) {
            cod_tipdes[i] = $(this).val();
        });


        if (date_inicia > date_finali)
        {
            Alerta('Atenci\xf3n', 'La fecha Inicial no puede ser mayor a la fecha Final', $("#fec_iniciaID"));
        }
        else
        {
            $.ajax({
                url: "../" + Standa + "/infast/ajax_soluci_noveda.php",
                data: "Option=getInform&pestana=" + pestana + "&Ajax=on&fec_inicia=" + fec_inicia + "&fec_finali=" + fec_finali + "&cod_produc=" + cod_produc.join(",") + "&cod_tipdes=" + cod_tipdes.join(","),
                type: "POST",
                async: true,
                beforeSend: function (obj)
                {
                    $.blockUI({
                        theme: true,
                        title: 'Solucion de Novedades',
                        draggable: false,
                        message: '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Generando Informe</p></center>'
                    });
                },
                success: function (data)
                {
                    $.unblockUI();
                    $("#" + pestana).html(data);
                }
            });
        }

    }
    catch (e)
    {
        console.log(e.message);
    }
}

function getDataDetail(niv_datosx, cod_tipdes, fec_detall)
{
    try
    {
        var Standa = $("#standaID").val();

        $.ajax({
            url: "../" + Standa + "/infast/ajax_soluci_noveda.php",
            data: "option=getDataDetail&niv_datosx=" + niv_datosx + "&cod_tipdes=" + cod_tipdes + "&fec_detall=" + fec_detall,
            type: "POST",
            async: true,
            beforeSend: function (obj)
            {
                $("#PopUpID").html('');
                $.blockUI({
                    theme: true,
                    title: 'Solucion de Novedades',
                    draggable: false,
                    message: '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Generando Detalle</p></center>'
                });
            },
            success: function (data)
            {
                $.unblockUI();
                $("#PopUpID").dialog({
                    modal: true,
                    resizable: true,
                    draggable: false,
                    title: "Detalles",
                    width: $(document).width() - 200,
                    heigth: 500,
                    position: ['middle', 25],
                    bgiframe: true,
                    closeOnEscape: false,
                    show: {effect: "puff", duration: 300},
                    hide: {effect: "puff", duration: 300}
                });
                $("#PopUpID").html(data);
            }
        });
    }
    catch (e)
    {
        console.log(e.message);
    }
}

function getData(niv_datosx, cod_tipdes)
{
    try
    {
        var Standa = $("#standaID").val();

        $.ajax({
            url: "../" + Standa + "/infast/ajax_soluci_noveda.php",
            data: "option=getData&niv_datosx=" + niv_datosx + "&cod_tipdes=" + cod_tipdes,
            type: "POST",
            async: true,
            beforeSend: function (obj)
            {
                $("#PopUpID").html('');
                $.blockUI({
                    theme: true,
                    title: 'Solucion de Novedades',
                    draggable: false,
                    message: '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Generando Detalle</p></center>'
                });
            },
            success: function (data)
            {
                $.unblockUI();
                $("#PopUpID").dialog({
                    modal: true,
                    resizable: true,
                    draggable: false,
                    title: "Detalles",
                    width: $(document).width() - 200,
                    heigth: 500,
                    position: ['middle', 25],
                    bgiframe: true,
                    closeOnEscape: false,
                    show: {effect: "puff", duration: 300},
                    hide: {effect: "puff", duration: 300}
                });
                $("#PopUpID").html(data);
            }
        });
    }
    catch (e)
    {
        console.log(e.message);
    }
}

function Export()
{
    var Standa = $("#standaID").val();
    window.open("../" + Standa + "/infast/ajax_soluci_noveda.php?option=expInformExcel", '', '');
}


function Alerta(title, message, focus)
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

function calTotalTables()
{
    try
    {
        console.log("Hola mundo");

        var tableGeneral = $("#generalID").find("table").next();

        console.log(tableGeneral);
    }
    catch (e)
    {
        console.log("Error Fuction calTotalTables: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}
/*! \fn: general
 *  \brief: trae el detallado general de la consulta de acuerdo a la pestaña
 *  \author: Ing. Alexander Correa
 *  \date: 09/11/2015
 *  \date modified: dia/mes/año
 *  \param: pestana pestaña para la cual se esta haciendo el detallado
 *  \param: 
 *  \return pinta en un popUp la infomacíon de la consulta
 */

function general(pestana) {
    var fec_inicia = $("#fec_iniciaID").val();
    var fec_finali = $("#fec_finaliID").val();
    var Standa = $("#standaID").val();
    // capturar checkeados productos
    var cod_produc = [];
    $("input[name^='produc']:checked").each(function (i, v) {
        cod_produc[i] = $(this).val();
    });
    // capturar checkeados tipo de despacho
    var cod_tipdes = [];
    $("input[name^='tipdes']:checked").each(function (i, v) {
        cod_tipdes[i] = $(this).val();
    });

    $.ajax({
        url: "../" + Standa + "/infast/ajax_soluci_noveda.php",
        data: "Option=getDataGeneral&Ajax=on&pestana=" + pestana + "&fec_inicia=" + fec_inicia + "&fec_finali=" + fec_finali + "&cod_produc=" + cod_produc + "&cod_tipdes=" + cod_tipdes,
        type: "POST",
        async: true,
        beforeSend: function (obj)
        {
            $("#PopUpID").html('');
            $.blockUI({
                theme: true,
                title: 'Solucion de Novedades',
                draggable: false,
                message: '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Generando Detalle</p></center>'
            });
        },
        success: function (data) {
            $.unblockUI();
            LoadPopupJQ('open', 'Detalle', ($(window).height() - 40), ($(window).width() - 40), false, false, true);
            var popup = $("#popID");
            popup.parent().children().children('.ui-dialog-titlebar-close').hide();
            popup.append(data);// //lanza el popUp
            var total = $("#total").val();
            $("#totlaID").append("Se encontr&oacute; un total de " + total + " registros");
        }
    });

}

/*! \fn: getData()
 *  \brief: muestra el reporte por pestaña para los solucionados, generados y no solucionados
 *  \author: Ing. Alexander Correa
 *  \date: 13/11/2015
 *  \date modified: dia/mes/año
 *  \param: tipo (string)= indica si es mayor o menor a 4 horas o no solucionados
 *  \param: fecha inicial = fecha inicial de la consulta
 *  \param: fecha final = fecha final de la consulta
 *  \param: pestaña = pestaña de la consulta
 *  \param: novedad = novedad para la cual se hace la consulta
 *  \param: unico = indicador para saber si consulto el dia habil anterior o ya se mandan las fechas completas
 *  \return html
 */

function generarReporte(tipo, fec_inicia, fec_finali, pestana, novedad, unico) {
    var Standa = $("#standaID").val();
    // capturar checkeados productos
    var cod_produc = [];
    $("input[name^='produc']:checked").each(function (i, v) {
        cod_produc[i] = $(this).val();
    });
    // capturar checkeados tipo de despacho
    var cod_tipdes = [];
    $("input[name^='tipdes']:checked").each(function (i, v) {
        cod_tipdes[i] = $(this).val();
    });

    $.ajax({
        url: "../" + Standa + "/infast/ajax_soluci_noveda.php",
        data: "Option=getDataGeneral&Ajax=on&pestana=" + pestana + "&fec_inicia=" + fec_inicia + "&fec_finali=" + fec_finali + "&cod_produc=" + cod_produc + "&cod_tipdes=" + cod_tipdes + "&tipo=" + tipo+"&novedad="+novedad+"&unico="+unico,
        type: "POST",
        async: true,
        beforeSend: function (obj)
        {
            $("#PopUpID").html('');
            $.blockUI({
                theme: true,
                title: 'Solucion de Novedades',
                draggable: false,
                message: '<center><img src="../' + Standa + '/imagenes/ajax-loader2.gif" /><p>Generando Detalle</p></center>'
            });
        },
        success: function (data) {
            $.unblockUI();
            LoadPopupJQ('open', 'Detalle', ($(window).height() - 40), ($(window).width() - 40), false, false, true);
            var popup = $("#popID");
            popup.parent().children().children('.ui-dialog-titlebar-close').hide();
            popup.append(data);// //lanza el popUp 

        }
    });
}

function exportTableExcel( idTable )
{
  try{
   $("#form_novedadesID").submit();
  }
  catch(e)
  {
    console.log( "Error Function exportTableExcel: "+e.message+"\nLine: "+e.lineNumber );
    return false;
  }
}
function getExcel(){
     var total = $('#total').val();
     var standa = $('#standaID').val();
     var datos = $('#data2').html();
     $('#totalID').html('Se encontr&oacute; un total de ' + total + ' registros <img src=\"../'+standa+'/imagenes/excel.jpg\"  style=\"cursor:pointer\" onclick=\"exportTableExcel(\'detalle\')\"/>');
     $('#hidden').empty();
     $('#hidden').html(datos);
}
