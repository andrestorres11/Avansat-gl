$(function() {
    $("body").removeAttr("class");
    $(".accordion").accordion({
        collapsible: true,
        heightStyle: "content",
        icons: {
            "header": "ui-icon-circle-arrow-e",
            "activeHeader": "ui-icon-circle-arrow-s"
        }
    }).click(function() {
        $("body").removeAttr("class");
    })
    $("#contenido").css({
        height: 'auto',
    });
    $("#tabs").tabs({
        beforeLoad: function(event, ui) {
            ui.jqXHR.fail(function() {
                ui.panel.html("Cargado...");
            });
        }
    });
    $("#fec_iniciaID").datepicker();
    $("#fec_iniciaID").datepicker('option', {
        dateFormat: 'yy-mm-dd'
    });
    $("#fec_finaliID").datepicker();
    $("#fec_finaliID").datepicker('option', {
        dateFormat: 'yy-mm-dd'
    });
});

/* ! \fn: getInforme
 *  \brief: funcion para pintar el general del informe de asignacion de carga
 *  \author: Ing. Alexander Correa
 *  \date: 03/03/2016
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return 
 */
function getInforme() {
    var conn = checkConnection();
    if (conn) {
        var val = validaciones();
        if (val) {
            var parametros = getDataForm();
            var standa = $("#standa").val();
            $.ajax({
                type: "POST",
                url: "../" + standa + "/inform/ajax_inform_inform.php",
                data: "&Ajax=on&Option=GetInformAsignaCargax&standa=" + standa + "&" + parametros,
                async: true,
                beforeSend: function() {
                    BlocK('Cargando Detalle...', 1);
                },
                success: function(datos) {
                    BlocK();
                    $("#generaID").html("");
                    $("#generaID").append(datos);
                    pintarTotales();
                }
            });
        }
    } else {
        swal({
            title: "Informe de Carga Laboral",
            text: "Por favor verifica tu conexión a internet.",
            type: "error"
        });
    }

}

/* ! \fn: pintarTotales
 *  \brief: pinta los totales despues de pintar el informe general
 *  \author: Ing. Alexander Correa
 *  \date: 03/03/2016
 *  \date modified: dia/mes/año
 *  \param: 
 *  \return
 */
function pintarTotales() {
    var generados,
        finalizados,
        transito,
        pendientes,
        pfinali,
        ptransi,
        ppendie,
        fec_inicia,
        fec_finali;

    fec_inicia = $("#fec_iniciaID").val();
    fec_finali = $("#fec_finaliID").val();
    generados = parseInt($("#generados").val());
    finalizados = parseInt($("#finalizados").val());
    transito = parseInt($("#transito").val());
    pendientes = parseInt($("#pendientes").val());

    pfinali = (finalizados * 100 / generados).toPrecision(3);
    ptransi = (transito * 100 / generados).toPrecision(3);
    ppendie = (pendientes * 100 / generados).toPrecision(3);

    $("#genera").append(generados);
    $("#finali").append(finalizados);
    $("#pfinal").append(pfinali + " % ");
    $("#transi").append(transito);
    $("#ptrans").append(ptransi + " % ");
    $("#pendie").append(pendientes);
    $("#ppendi").append(ppendie + " % ");
}

/* ! \fn: getDetalleCargax
 *  \brief: funcion para mostar el detallado de la asignacion de carga
 *  \author: Ing. Alexander Correa
 *  \date: 07/03/2016
 *  \date modified: dia/mes/año
 *  \param: tipo     => int => indica la seccion que se esta consultando (todos = 0, finalizaodos = 1, transito = 2, pendiente = 3)
 *  \param: fec_inicia   => date => fecha inicial para mostrar el detallado 
 *  \param: fec_finali   => date => fecha   final para mostrar el detallado 
 *  \return 
 */

function getDetalleCargax() {
    var conn = checkConnection();
    if (conn) {
        var val = validaciones();
        if (val) {
            fec_inicia = $("#fec_iniciaID").val();

            fec_finali = $("#fec_finaliID").val();
            var standa = $("#standa").val();

            $.ajax({
                type: "POST",
                url: "../" + standa + "/inform/ajax_inform_inform.php",
                data: "&Ajax=on&Option=getDetalleCargax&fec_inicia=" + fec_inicia + "&fec_finali=" + fec_finali,
                async: true,
                beforeSend: function() {
                    BlocK('Cargando Detalle...', 1);

                },
                success: function(data) {
                    BlocK();
                    $("#cargaxID").html("");
                    $("#cargaxID").append(data);
                }
            });
        }
    } else {
        swal({
            title: "Informe de Carga Laboral",
            text: "Por favor verifica tu conexión a internet.",
            type: "error"
        });
    }

}
