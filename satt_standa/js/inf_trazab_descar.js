/*! \file: inf_trazab_descar.js
 *  \brief: Archivo que contiene las sentencias JS para el informe Despachos Finalizados (Informes > Operacion trafico > Finalizados)
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/04/2016
 *  \bug: 
 *  \warning: 
 */

var idForm = 'form_InfTrazabDespacID';
var standar;

/*! \fn: ("body").ready
 *  \brief: Crea los multiselect, calendarios, acordion y pestañas. Carga la variable standar.
 *  \author: Ing. Fabian Salinas
 *  \date:  25/04/2016
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
    var option;


    //Autocompletable Ciudad Origen
    $("#nom_ciuoriID, #nom_ciudesID, #nom_conducID").autocomplete({
        source: function(request, response) {
            if ($("*:focus").attr('name') == 'nom_conduc') {
                option = 'getConduc';
            } else {
                option = 'getCiudades';
            }

            $.ajax({
                url: "../" + standar + "/inform/inf_trazab_descar.php?Ajax=on&Option=" + option + "&standa=" + standar,
                dataType: "json",
                data: {
                    term: request.term,
                },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 3,
        delay: 100,
        select: function(event, ui) {
            switch ($(this).attr('name')) {
                case 'nom_ciuori':
                    $("#cod_ciuoriID").val(ui.item.codex);
                    break;
                case 'nom_ciudes':
                    $("#cod_ciudesID").val(ui.item.codex);
                    break;
                case 'nom_conduc':
                    $("#cod_conducID").val(ui.item.codex);
                    break;
            }
        }
    });
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
        var data = getDataFormDespacFinali();

        if (!validaFormDespacFinali(data)) {
            return false;
        }

        var attributes = 'Ajax=on&Option=inform';
        attributes += data;
        attributes += '&ind_pestan=' + ind_pestan;

        $.ajax({
            url: "../" + standar + "/inform/inf_trazab_descar.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Generando Informe...', true);
            },
            success: function(datos) {
                $("#" + id_div).html(datos);
                //$("#" + id_div + " #formID").css("overflow", "scroll");
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

/*! \fn: getDataFormDespacFinali
 *  \brief: Trae la data del formulario
 *  \author: Ing. Fabian Salinas
 *  \date:  18/04/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: 
 *  \return: 
 */
function getDataFormDespacFinali() {
    try {
        var result = '';
        var cod_client = [];
        var cod_transp = [];
        var banderaTransp = false;

        if ($("#nom_ciuoriID").val() == '') {
            $("#cod_ciuoriID").val('');
        }
        if ($("#nom_ciudesID").val() == '') {
            $("#cod_ciudesID").val('');
        }
        if ($("#nom_conducID").val() == '') {
            $("#cod_conducID").val('');
        }

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
                } else if ($(this).attr('name').substr(12, 10) == 'ind_estado') {
                    if ($(this).val() != '') {
                        cod_client[b] = $(this).val();
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

        if (cod_client.length > 0) {
            result += "&ind_estado=" + cod_client.join();
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
        console.log("Error Function getDataFormDespacFinali: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: validaFormDespacFinali
 *  \brief: Valida el formulario 
 *  \author: Ing. Fabian Salinas
 *  \date: 20/04/2016
 *  \date modified: dd/mm/aaaa
 *  \param: data  boolean
 *  \return: boolean
 */
function validaFormDespacFinali(data) {
    try {
        var val = validaciones();
        var va2 = true;
        var msj;
        var fec_finali = $("#fec_finaliID").val();
        var fec_inicia = $("#fec_iniciaID").val();

        if (fec_finali != '' && fec_inicia != '') {
            var dias = getDiasTrascurridos(fec_finali, fec_inicia);

            if (dias > 31) {
                msj = 'El rango de fechas no debe superar los 31 dias.';
                va2 = false;
            } else if (dias < 0) {
                msj = 'El rango de fechas no es valido.';
                va2 = false;
            }
        } else if ($("#num_despacID").val() == '' && $("#num_viajexID").val() == '' && $("#num_placaxID").val() == '') {
            msj = 'Seleccione un rango de fechas valido.';
            va2 = false;
        }

        if (!va2) {
            setTimeout(function() {
                inc_alerta('fec_iniciaID', msj);
                inc_alerta('fec_finaliID', msj);
            }, 530);
            return false;
        } else if (!val || !data) {
            return false;
        } else {
            return true;
        }
    } catch (e) {
        console.log("Error Fuction validaFormDespacFinali: " + e.message + "\nLine: " + e.lineNumber);
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

/*! \fn: expTabExcelReportEsfera
 *  \brief: Guarda la tabla en el Hidden y da submit al formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 28/04/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: idTab  String  ID de la tabla a exportar
 *  \return: 
 */
function expTabExcelDespacFinali(idTab) {
    try {
        $("#exportExcelID").val("<table>" + $("#" + idTab).html() + "</table>");
        $("#form_InfTrazabDespacID").submit();
    } catch (e) {
        console.log("Error Function expTabExcelNovupd: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: detaliData
 *  \brief: funcion para el detalle de la tabla
 *  \author: Ing. Miguel Romero
 *    \date: 05/08/2016 
 *  \param: fecha = fecha de los despachos  
 */

function detailData(fecha) {

    ind_pestan = 'report';

    data = getDataFormDespacFinali();

    attributes = 'Ajax=on&Option=inform';
    attributes += data;
    attributes += '&ind_pestan=' + ind_pestan + "&ind_filtro=1&fec_filtro=" + fecha + "&indDetalle=1&idTable=ui78";

    LoadPopupJQ('open', 'Resultados de la Busqueda', ($(window).height() - 50), ($(window).width() - 50), false, false, true);
    var popup = $("#popID");

    $.ajax({
        url: "../" + standar + "/inform/inf_trazab_descar.php",
        type: "POST",
        data: attributes,
        async: true,
        beforeSend: function() {
            BlocK('Generando Informe...', true);
        },
        success: function(datos) {
            popup.html(datos);
            popup.css('overflow-y', 'true');
            popup.css('overflow-x', 'true');
            //$("#" + id_div + " #formID").css("overflow", "scroll");
        },
        complete: function() {
            BlocK();
        }
    });
}
