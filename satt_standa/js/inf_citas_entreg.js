/*! \file: inf_report_esfera.js
 *  \brief: Archivo para las sentencias JS del inform/inf_report_esfera.php 
 *          (Informes > Gestion operacion > Indicador de Registros Ideales)
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 18/04/2016
 *  \bug: 
 *  \warning: 
 */

var idForm = 'form_InfReportEsperaID';
var standar;

/*! \fn: ("body").ready
 *  \brief: Crea los multiselect, calendarios, acordion y pestañas
 *  \author: Ing. Fabian Salinas
 *  \date:  18/04/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: 
 *  \return: 
 */
$("body").ready(function() {
    //Calendarios
    $("#fec_iniciaID, #fec_finaliID").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd"
    });

    //Multiselect
    $(".multiSel").children().multiselect().multiselectfilter();

    //Pestañas
    $("#tabs").tabs({
        beforeLoad: function(event, ui) {
            ui.jqXHR.fail(function() {
                ui.panel.html("Cargado...");
            });
        }
    });

    standar = $("#standaID").val();
    $("#secID").css('height','auto');

});


 


/*! \fn: report
 *  \brief: Realiza la peticion ajax para pintar el informe General
 *  \author: Ing. Fabian Salinas
 *  \date:  18/04/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: ind_pestan  String  Indicador de la pestaña a la que pertenece
 *  \param: id_div      String  ID del Div donde se pintara la respuesta del ajax
 *  \return: 
 */
function report(ind_pestan, id_div) {
    try {
    
        var val = true; 
        var dias = getDiasTrascurridos($("#fec_finaliID").val(), $("#fec_iniciaID").val());
        var msj;

        if (dias < 0) {
            msj = 'El rango de fechas no es valido.';

            setTimeout(function() {
                inc_alerta('fec_iniciaID', msj);
                inc_alerta('fec_finaliID', msj);
            }, 530);
            val = false;
        }

        if (!val) {
            return false;
        }  

 
        var cliente = [];
        var indexCl = 0;
        var negocio = [];
        var indexNe = 0;

        $('input[type=checkbox][name=multiselect_cod_clientID]').each(function(i,o){
            if($(this).attr('aria-selected') == 'true' && $(this).val().length > 0 )
            {
                cliente[(indexCl++)] = $(this).val();
            }
        });        

        $('input[type=checkbox][name=multiselect_cod_negociID]').each(function(i,o){
            if($(this).attr('aria-selected') == 'true' && $(this).val().length > 0 )
            {
                negocio[(indexNe++)] = $(this).val();
            }
        });


        var attributes = 'Ajax=on&Option=informGeneral';
        attributes += '&ind_pestan=' + ind_pestan;
        attributes += cliente.length > 0 ? '&cod_client=' + cliente.join(",") : '';
        attributes += negocio.length > 0 ? '&cod_negoci=' + negocio.join(","): '';
        attributes += $("#fec_finaliID").val() ? '&fec_finali=' + $("#fec_finaliID").val() : '';
        attributes += $("#fec_iniciaID").val() ? '&fec_inicia=' + $("#fec_iniciaID").val() : '';

        $.ajax({
            url: "../" + standar + "/inform/inf_citasx_entreg.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Generando Informe...', true);
            },
            success: function(datos) {
                $("#" + id_div).html(datos);
                $("#" + id_div + " #formID").css("overflow", "scroll");
                $("#" + id_div + " #totalx_1ID").html($("#" + id_div + " #total_1TD").html());
                $("#" + id_div + " #totalx_2ID").html($("#" + id_div + " #total_2TD").html());
            },
            complete: function() {
                setPieChart();
                BlocK();
                $("#secID").css('height','auto');
            }
        });
    } catch (e) {
        console.log("Error Function report: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}



/*! \fn: setPieChart
 *  \brief: Muetsra la gráfica
 *  \author: Ing. Nelson Liberato
 *  \date:  22/02/2018
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: 
 *  \return: 
 */
function setPieChart() 
{
    try
    {
        var cumplidos = parseFloat($("#cumplidosID").val());
        var No_cumplidos = parseFloat($("#No_cumplidosID").val());

        console.log(cumplidos);
        console.log(No_cumplidos);
 
        data1 = [[['Cumplidos', cumplidos],['No cumplidos', No_cumplidos]]];
        toolTip1 = ['Cumplidos', 'No cumplidos'];
     
        var plot1 = jQuery.jqplot('chart1', 
            data1,
            {
                title: ' Gráfica indicador ', 
                animate:true,
                seriesDefaults: {
                    shadow: false, 
                    renderer: jQuery.jqplot.PieRenderer, 
                    rendererOptions: { padding: 6, sliceMargin: 2, showDataLabels: true }
                },
                legend: {
                    show: true,
                    location: 'e',
                    renderer: $.jqplot.EnhancedPieLegendRenderer,
                    rendererOptions: {
                        numberColumns: 1,
                        toolTips: toolTip1
                    }
                },
            }
        );
    } catch (e) {
        console.log("Error Function setPieChart: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}



 

/*! \fn: expTabExcelReportEsfera
 *  \brief: Guarda la tabla en el Hidden y da submit al formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 18/04/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: idTab  String  ID de la tabla a exportar
 *  \return: 
 */
function expTabExcelReportx(idTab) {
    try {
        // $("#exportExcelID").val("<table>" + $("#" + idTab).html() + "</table>");
        // $("form").submit();

        
        $("#imageBinPlotID").val( jqplotToImg($("#chart1")) );
        $('#form_InfGestionCitasDeEntregaID').submit();
        

    } catch (e) {
        console.log("Error Function expTabExcelReportx: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}




function jqplotToImg(obj) {
    var newCanvas = document.createElement("canvas");
    newCanvas.width = obj.find("canvas.jqplot-base-canvas").width();
    newCanvas.height = obj.find("canvas.jqplot-base-canvas").height()+10;
    var baseOffset = obj.find("canvas.jqplot-base-canvas").offset();

    // make white background for pasting
    var context = newCanvas.getContext("2d");
    context.fillStyle = "rgba(255,255,255,1)";
    context.fillRect(0, 0, newCanvas.width, newCanvas.height);

    obj.children().each(function () {
    // for the div's with the X and Y axis
        if ($(this)[0].tagName.toLowerCase() == 'div') {
            // X axis is built with canvas
            $(this).children("canvas").each(function() {
                var offset = $(this).offset();
                newCanvas.getContext("2d").drawImage(this,
                    offset.left - baseOffset.left,
                    offset.top - baseOffset.top
                );
            });
            // Y axis got div inside, so we get the text and draw it on the canvas
            $(this).children("div").each(function() {
                var offset = $(this).offset();
                var context = newCanvas.getContext("2d");
                context.font = $(this).css('font-style') + " " + $(this).css('font-size') + " " + $(this).css('font-family');
                context.fillStyle = $(this).css('color');
                context.fillText($(this).text(),
                    offset.left - baseOffset.left,
                    offset.top - baseOffset.top + $(this).height()
                );
            });
        } else if($(this)[0].tagName.toLowerCase() == 'canvas') {
            // all other canvas from the chart
            var offset = $(this).offset();
            newCanvas.getContext("2d").drawImage(this,
                offset.left - baseOffset.left,
                offset.top - baseOffset.top
            );
        }
    });

    // add the point labels
    obj.children(".jqplot-point-label").each(function() {
        var offset = $(this).offset();
        var context = newCanvas.getContext("2d");
        context.font = $(this).css('font-style') + " " + $(this).css('font-size') + " " + $(this).css('font-family');
        context.fillStyle = $(this).css('color');
        context.fillText($(this).text(),
            offset.left - baseOffset.left,
            offset.top - baseOffset.top + $(this).height()*3/4
        );
    });

    // add the title
    obj.children("div.jqplot-title").each(function() {
        var offset = $(this).offset();
        var context = newCanvas.getContext("2d");
        context.font = $(this).css('font-style') + " " + $(this).css('font-size') + " " + $(this).css('font-family');
        context.textAlign = $(this).css('text-align');
        context.fillStyle = $(this).css('color');
        context.fillText($(this).text(),
            newCanvas.width / 2,
            offset.top - baseOffset.top + $(this).height()
        );
    });

    // add the legend
    obj.children("table.jqplot-table-legend").each(function() {
        var offset = $(this).offset();
        var context = newCanvas.getContext("2d");
        context.strokeStyle = $(this).css('border-top-color');
        context.strokeRect(
            offset.left - baseOffset.left,
            offset.top - baseOffset.top,
            $(this).width(),$(this).height()
        );
        context.fillStyle = $(this).css('background-color');
        context.fillRect(
            offset.left - baseOffset.left,
            offset.top - baseOffset.top,
            $(this).width(),$(this).height()
        );
    });

    // add the rectangles
    obj.find("div.jqplot-table-legend-swatch").each(function() {
        var offset = $(this).offset();
        var context = newCanvas.getContext("2d");
        context.fillStyle = $(this).css('background-color');
        context.fillRect(
            offset.left - baseOffset.left,
            offset.top - baseOffset.top,
            $(this).parent().width(),$(this).parent().height()
        );
    });

    obj.find("td.jqplot-table-legend").each(function() {
        var offset = $(this).offset();
        var context = newCanvas.getContext("2d");
        context.font = $(this).css('font-style') + " " + $(this).css('font-size') + " " + $(this).css('font-family');
        context.fillStyle = $(this).css('color');
        context.textAlign = $(this).css('text-align');
        context.textBaseline = $(this).css('vertical-align');
        context.fillText($(this).text(),
            offset.left - baseOffset.left,
            offset.top - baseOffset.top + $(this).height()/2 + parseInt($(this).css('padding-top').replace('px',''))
        );
    });

    // convert the image to base64 format
    return newCanvas.toDataURL("image/png");
}


/*! \fn: getDiasTrascurridos
 *  \brief: Trae los dias transcurridos entre dos fechas
 *  \author: Ing. Fabian Salinas
 *  \date: 18/04/2016
 *  \date modified: dd/mm/aaaa
 *  \param: fec1  Date  Fecha 1
 *  \param: fec2  Date  Fecha 2
 *  \return: Integer
 */
function getDiasTrascurridos(fec1, fec2) {
    try {
        return Math.floor((Date.parse(fec1) - Date.parse(fec2)) / 86400000);
    } catch (e) {
        console.log("Error Fuction getDiasTrascurridos: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: infDetail
 *  \brief: Realiza la peticion Ajax para pintar el informe Detallado
 *  \author: Ing. Fabian Salinas
 *  \date: 20/04/2016
 *  \date modified: dd/mm/aaaa
 *  \param: ind_pestan  String  Indicador Pestaña
 *  \return: 
 */
function infDetail(cod_transp, cod_contro) {
    try {
        var data = getDataFormReportEsfera();

        if (!validaFormReportEsfera(data)) {
            return false;
        }

        var attributes = 'Ajax=on&Option=infDetail';
        attributes += data;

        if (cod_transp) {
            attributes += '&cod_transp=' + cod_transp;
        }
        if (cod_contro) {
            attributes += '&cod_contro=' + cod_contro;
        }

        $.ajax({
            url: "../" + standar + "/inform/inf_report_esfera.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Generando Informe...', true);
            },
            success: function(datos) {
                LoadPopupJQ('open', 'DETALLE', ($(window).height() - 50), ($(window).width() - 50), false, false, true, 'popupinfDetailRepEsfID');
                var popup = $("#popupinfDetailRepEsfID");
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.html(datos);
            },
            complete: function() {
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Fuction infDetail: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: validaFormReportEsfera
 *  \brief: Valida el formulario 
 *  \author: Ing. Fabian Salinas
 *  \date: 20/04/2016
 *  \date modified: dd/mm/aaaa
 *  \param: data  boolean
 *  \return: boolean
 */
function validaFormReportEsfera(data) {
    try {
        var val = validaciones();
        var dias = getDiasTrascurridos($("#fec_finaliID").val(), $("#fec_iniciaID").val());
        var msj;

        if (dias > 31) {
            msj = 'El rango de fechas no debe superar los 31 dias.';

            setTimeout(function() {
                inc_alerta('fec_iniciaID', msj);
                inc_alerta('fec_finaliID', msj);
            }, 530);
            val = false;
        } else if (dias < 0) {
            msj = 'El rango de fechas no es valido.';

            setTimeout(function() {
                inc_alerta('fec_iniciaID', msj);
                inc_alerta('fec_finaliID', msj);
            }, 530);
            val = false;
        }

        if (!val || !data) {
            return false;
        } else {
            return true;
        }
    } catch (e) {
        console.log("Error Fuction validaFormReportEsfera: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}
