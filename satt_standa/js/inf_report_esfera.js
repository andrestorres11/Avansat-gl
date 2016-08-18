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
        var data = getDataFormReportEsfera();

        if (!validaFormReportEsfera(data)) {
            return false;
        }

        var attributes = 'Ajax=on&Option=informGeneral';
        attributes += data;
        attributes += '&ind_pestan=' + ind_pestan;

        $.ajax({
            url: "../" + standar + "/inform/inf_report_esfera.php",
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
                BlocK();
            }
        });
    } catch (e) {
        console.log("Error Function report: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: getDataFormReportEsfera
 *  \brief: Trae la data del formulario
 *  \author: Ing. Fabian Salinas
 *  \date:  18/04/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: 
 *  \return: 
 */
function getDataFormReportEsfera() {
    try {
        var result = '';
        var cod_contro = [];
        var cod_transp = [];
        var banderaTransp = false;

        var a = 0,
            b = 0;
        $("input[type=checkbox]:checked").each(function(ind, obj) {
            if ($(this).val() != '') {
                if ($(this).attr('name').substr(12, 10) == 'cod_transp') {
                    if ($(this).val() != '') {
                        cod_transp[a] = $(this).val();
                        a++;
                        banderaTransp = true;
                    }
                } else if ($(this).attr('name').substr(12, 10) == 'cod_contro') {
                    if ($(this).val() != '') {
                        cod_contro[b] = $(this).val();
                        b++;
                    }
                }
            }
        });

        if (banderaTransp == false) {
            setTimeout(function() {
                inc_alerta('cod_transpID', 'Por favor seleccione al menos una transportadora.');
            }, 530);
            return false;
        }

        if (cod_contro.length > 0) {
            result += "&cod_contro=" + cod_contro.join();
        }
        if (cod_transp.length > 0) {
            result += "&cod_transp=" + cod_transp.join();
        }

        $("#" + idForm + " input[type=text]").each(function(ind, obj) {
            if ($(this).val() != '') {
                result += '&' + $(this).attr('name') + '=' + $(this).val();
            }
        });

        $("#" + idForm + " input[type=hidden]").each(function(ind, obj) {
            if ($(this).val() != '') {
                result += '&' + $(this).attr('name') + '=' + $(this).val();
            }
        });

        return result;
    } catch (e) {
        console.log("Error Function getDataFormReportEsfera: " + e.message + "\nLine: " + e.lineNumber);
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
function expTabExcelReportEsfera(idTab) {
    try {
        $("#exportExcelID").val("<table>" + $("#" + idTab).html() + "</table>");
        $("#form_InfReportEsperaID").submit();
    } catch (e) {
        console.log("Error Function expTabExcelNovupd: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
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
