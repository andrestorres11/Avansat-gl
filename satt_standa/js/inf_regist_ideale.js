/*! \file: inf_regist_ideale.js
 *  \brief: Archivo para las sentencias JS del inform/inf_regist_ideale.php 
 *          (Informes > Gestion operacion > Indicador de Registros Ideales)
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 05/04/2016
 *  \bug: 
 *  \warning: 
 */

var idForm = 'form_InfRegistIdealeID';
var standar;

/*! \fn: ("body").ready
 *  \brief: Crea los multiselect, calendarios, acordion y pestañas
 *  \author: Ing. Fabian Salinas
 *  \date:  29/02/2016
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
 *  \brief: Realiza la peticion ajax para pintar el informe
 *  \author: Ing. Fabian Salinas
 *  \date:  29/02/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: ind_pestan  String  Indicador de la pestaña a la que pertenece
 *  \param: id_div      String  ID del Div donde se pintara la respuesta del ajax
 *  \return: 
 */
function report(ind_pestan, id_div) {
    try {
        var val = validaciones();
        var data = getDataFormRegistIdeale();

        if (!val || !data) {
            return false;
        }

        var attributes = 'Ajax=on&Option=informGeneral';
        attributes += data;
        attributes += '&ind_pestan=' + ind_pestan;

        $.ajax({
            url: "../" + standar + "/inform/inf_regist_ideale.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Generando Informe...', true);
            },
            success: function(datos) {
                $("#" + id_div).html(datos);
                $("#" + id_div + " #totalx_1ID").html($("#" + id_div + " #total_1ID").html());
                $("#" + id_div + " #totalx_2ID").html($("#" + id_div + " #total_2ID").html());
                $("#" + id_div + " #totalx_3ID").html($("#" + id_div + " #total_3ID").html());
                $("#" + id_div + " #totalx_4ID").html($("#" + id_div + " #total_4ID").html());
                $("#" + id_div + " #totalx_5ID").html($("#" + id_div + " #total_5ID").html());
                $("#" + id_div + " #totalx_6ID").html($("#" + id_div + " #total_6ID").html());
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

/*! \fn: getDataFormRegistIdeale
 *  \brief: Trae la data del formulario
 *  \author: Ing. Fabian Salinas
 *  \date:  05/04/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: 
 *  \return: 
 */
function getDataFormRegistIdeale() {
    try {
        var result = '';
        var cod_tipdes = [];
        var cod_transp = [];
        var banderaTransp = false;

        var a = 0,
            b = 0,
            c = 0,
            d = 0,
            e = 0;
        $("input[type=checkbox]:checked").each(function(ind, obj) {
            if ($(this).val() != '') {
                if ($(this).attr('name').substr(0, 10) == 'cod_tipdes') {
                    cod_tipdes[a] = $(this).val();
                    a++;
                } else if ($(this).attr('name').substr(12, 10) == 'cod_transp') {
                    if( $(this).val() != ''){
                        cod_transp[b] = $(this).val();
                        b++;
                        banderaTransp = true;
                    }
                } else {
                    result += '&' + $(this).attr('name') + '=' + $(this).val();
                }
            }
        });

        if( banderaTransp == false ){
            setTimeout(function(){
                inc_alerta('cod_transpID', 'Por favor seleccione al menos una transportadora.');
            }, 530);
            return false;
        }

        if (cod_tipdes.length > 0) {
            result += "&cod_tipdes=" + cod_tipdes.join();
        }
        if (cod_transp.length > 0) {
            result += "&cod_transp='" + cod_transp.join("','") + "'";
        }


        var notSelect = [];
        x = 0;
        $(".ui-multiselect").each(function(ind, obj) {
            notSelect[x] = $(this).parent().attr('id').substr(0, 10);
            x++;
        });


        $("#" + idForm + " select option:selected").each(function(ind, obj) {
            if ($(this).val() != '' && ($.inArray($(this).parent().attr('name'), notSelect) == -1)) {
                result += '&' + $(this).parent().attr('name') + '=' + $(this).val();
            }
        });

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
        console.log("Error Function getDataFormRegistIdeale: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: infDetail
 *  \brief: Realiza la peticion Ajax para pintar el informe Detallado
 *  \author: Ing. Fabian Salinas
 *  \date: 08/04/2016
 *  \date modified: dd/mm/aaaa
 *  \param: ind_pestan  String  Indicador Pestaña
 *  \return: 
 */
function infDetail(ind_pestan, fec_inicia, fec_finali, cod_transp) {
    try {
        var val = validaciones();
        var data = getDataFormRegistIdeale();

        if (!val || !data) {
            return false;
        }

        var attributes = 'Ajax=on&Option=infDetail';
        attributes += data;
        attributes += '&ind_pestan=' + ind_pestan;
        attributes += '&fec_inicia=' + fec_inicia;
        attributes += '&fec_finali=' + fec_finali;

        if (cod_transp) {
            attributes += '&cod_transp=' + cod_transp;
        }
        
        $.ajax({
            url: "../" + standar + "/inform/inf_regist_ideale.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Generando Informe...', true);
            },
            success: function(datos) {
                LoadPopupJQ('open', 'DETALLE', ($(window).height() - 50), ($(window).width() - 50), false, false, true, 'popupinfDetailID');
                var popup = $("#popupinfDetailID");
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

/*! \fn: expTabExcelRegIde
 *  \brief: Guarda la tabla en el Hidden y da submit al formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 011/04/2016
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: idTab  String  ID de la tabla a exportar
 *  \return: 
 */
function expTabExcelRegIde(idTab) {
    try {
        $("#exportExcelID").val("<table>" + $("#" + idTab).html() + "</table>");
        $("#form_InfRegistIdealeID").submit();
    } catch (e) {
        console.log("Error Function expTabExcelNovupd: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}