/*! \file: inf_proyec_noveda.js
 *  \brief: Archivo que contiene las sentencias JS para el informe de proyeccion de novedades(Informes > Gestion de operacion > Proyeccion de nov)
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \bug: 
 *  \warning: 
 */

var idForm = 'form_InfProyecNovedaID';
var standar;

/*! \fn: ("body").ready
 *  \brief: Crea los multiselect, acordion y pestañas. Carga la variable standar.
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \modified by: 
 *  \param: 
 *  \return: 
 */
$("body").ready(function() {

    //Multiselect
    $(".multiSel").children().multiselect().multiselectfilter();
    $("#busq_transp").multiselect();
    //Pestañas
    $("#tabs").tabs({
        beforeLoad: function(event, ui) {
            ui.jqXHR.fail(function() {
                ui.panel.html("Cargado...");
            });
        }
    });
    standar = $("#standaID").val();
    $('#form_InfProyecNovedaID').submit(function(event) {
        event.preventDefault();
        var ProyecNoveda = getDataFormProyecNoveda();
        if (ProyecNoveda[0].length != 0) {
            $.ajax({
                type: 'POST',
                url: '../satt_standa/inform/ajax_proyec_noveda.php?opcion=1&Ajax=on',
                contentType: "application/x-www-form-urlencoded; charset=UTF-8", // $_POST
                data: { myData: ProyecNoveda },
                async: true,
                dataType: 'JSON',
                beforeSend: function() {
                    BlocK('Generando Reporte...', true);
                },
                success: function(data) {
                    BlocK('...', false);
                    $('#tag0ID').empty();
                    $('#tag0ID').append(data);
                },
                error: function(data) {
                    //Cuando la interacción retorne un error, se ejecutará esto.
                    console.log('error', data);

                }
            })
        } else {
            alert('El campo tranportadoras es obligatorio');
        }
    })
});


/*! \fn: getDataFormProyecNoveda
 *  \brief: Trae y organiza la data del formulario de los multiselect
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: 
 *  \return: 
 */
function getDataFormProyecNoveda() {
    try {
        var busq_transp = [];
        var tip_service = [];
        var combinedArray = [];

        var a = 0,
            b = 0;

        $("input[type=checkbox]:checked").each(function(ind, obj) {
            if ($(this).val() != '') {
                if ($(this).attr('name').substr(12, 10) == 'busq_trans') {
                    if ($(this).val() != '') {
                        busq_transp[a] = $(this).val();
                        a++;
                        banderaTransp = true;
                    }
                } else if ($(this).attr('name').substr(12, 10) == 'tip_servic') {
                    if ($(this).val() != '') {
                        tip_service[b] = $(this).val();
                        b++;
                    }
                }
            }
        });

        combinedArray = [busq_transp, tip_service];

        return combinedArray;
    } catch (e) {
        console.log("Error Function getDataFormProyecNoveda: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}


/*! \fn: expTabExcelDespacFinali
 *  \brief: Guarda la tabla en el Hidden y da submit al formulario
 *  \author: Ing. Carlos Nieto
 *  \author: carlos.nieto@intrared.net
 *  \version: 1.0
 *  \date: 17/06/2021
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: idTab  String  ID de la tabla a exportar
 *  \return: 
 */
function expTabExcelDespacFinali(idTab) {
    try {
        $("#exportExcelID").val("<table>" + $("#" + idTab).html() + "</table>");
        $("#form_InfDespacFinaliID").submit();
    } catch (e) {
        console.log("Error Function expTabExcelNovupd: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}