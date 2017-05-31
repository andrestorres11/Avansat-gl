/*! \file: inf_bandej_entra3.js
 *  \brief: Archivo para las sentencias JS de la bandeja
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 16/06/2015
 *  \bug: 
 *  \warning: 
 */

/*! \fn: $("body").ready
 *  \brief: Acciones que se ejecutan al cargar el body
 *  \author: Ing. Fabian Salinas
 *  \date: 16/06/2015
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
$("body").ready(function() {
    //Crea Pestañas
    $("#tabs").tabs({
        beforeLoad: function(event, ui) {
            ui.jqXHR.fail(function() {
                ui.panel.html("Cargado...");
            });
        }
    });

    //Multi Select
    $("#cod_usuariID").multiselect().multiselectfilter();
    $("#cod_transpID").multiselect().multiselectfilter();
    $("#pun_cargueID").multiselect().multiselectfilter();
    $("#tip_producID").multiselect().multiselectfilter();

    //Onclick Pestañas
    $("#liGenera").click(function() {
        verifiData();
        generalReport("infoGeneral", "tabs-1");
    });

    $("#liCargue").click(function() {
        generalReport("infoCargue", "tabs-2");
        Actualizarbadge("2", "liCargue");
    });

    $("#liTransi").click(function() {
        generalReport("infoTransito", "tabs-3");
        Actualizarbadge("3", "liTransi");
    });

    $("#liDescar").click(function() {
        generalReport("infoDescargue", "tabs-4");
        Actualizarbadge("5", "liDescar");
    });

    $("#liPernoc").click(function() {
        generalReport("infoPernoctacion", "tabs-5");
    });

    $("#liPreCar").click(function() {
        getDataSelectPRC();
        Actualizarbadge("1", "liPreCar");
    });

    $("#generarprc").click(function() {
        if( ($('#hor_inicio').val() != "" && $('#hor_finxxx').val() != "") )
        {
            if($('#hor_inicio').val() < $('#hor_finxxx').val() )
            {
                generalReport("infoPreCargue", "tabs-6-a");
           }
            else
            {
                alert("La hora incial debe ser mayor a la final.");
            }
        }
        else if( ($('#hor_inicio').val() == "" && $('#hor_finxxx').val() == "") ){
            generalReport("infoPreCargue", "tabs-6-a");
        }
        else
        {
            alert("Se deben diligenciar los campos de hora.");
        }

    });

    //
    $("#cod_transpID option[value=" + $("#sel_transpID").val() + "]").attr("selected", "selected");
    $("#cod_usuariID option[value=" + $("#sel_usuariID").val() + "]").attr("selected", "selected");

    //Ejecuta la busqueda por los filtros especificos
    $(".Style2DIV input[type=text]").each(function(i, o) {
        $(this).blur(function() {
            showDetailSearch($(this));
        });
    });

    //Crea Acordion
    $("#accordionID").accordion({
        heightStyle: "content",
        collapsible: true
    });

    //
    $("#tableID").css('height', ($(window).height() - 166));
    $("#tableID").css('overflow', 'scroll');
});

/*! \fn: verifiData
 *  \brief: Verifica que se apliquen los filtros minimos
 *  \author: Ing. Fabian Salinas
 *  \date: 16/06/2015
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function verifiData() {
    try {
        var ind_filact = $("#ind_filactID");
        var attributes = getParameFilter();

        if (attributes == '') {
            ind_filact.val('');
            return false;
        } else {
            ind_filact.val('1');
        }
    } catch (e) {
        console.log("Error Fuction verifiData: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: getParameFilter
 *  \brief: Trae los parametros  Filtro del formulario
 *  \author: Ing. Fabian Salinas
 *  \date: 16/06/2015
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function getParameFilter() {
    try {
        var box_checke = $("input[type=checkbox]:checked");
        var rad_checke = $("input[type=radio]:checked");
        var cod_transp = '""';
        var cod_usuari = '""';
        var pun_cargue = '""';
        var tip_produc = '""';
        var hor_inicio = $("#hor_inicio").val();
        var hor_finxxx = $("#hor_finxxx").val();
        var attributes = '';

        box_checke.each(function(i, o) {
            if ($(this).attr("name") == 'multiselect_cod_transpID')
                cod_transp += ',"' + $(this).val() + '"';
            else if ($(this).attr("name") == 'multiselect_cod_usuariID')
                cod_usuari += ',"' + $(this).val() + '"';
            else if ($(this).attr("name") == 'multiselect_pun_cargueID')
                pun_cargue += ',"' + $(this).val() + '"';
            else if ($(this).attr("name") == 'multiselect_tip_producID')
                tip_produc += ',"' + $(this).val() + '"';
            else {
                attributes += '&' + $(this).attr("name");
                attributes += '=' + $(this).val();
            }
        });

        rad_checke.each(function(i, o) {
            attributes += '&' + $(this).attr("name");
            attributes += '=' + $(this).val();
        });

        if (cod_transp != '""' && cod_transp != '"",""') {
            attributes += '&cod_transp=' + cod_transp;
        }

        if (cod_usuari != '""' && cod_usuari != '"",""') {
            attributes += '&cod_usuari=' + cod_usuari;
        }

        if (pun_cargue != '""' && pun_cargue != '"",""') {
            attributes += '&pun_cargue=' + pun_cargue;
        }

        if (tip_produc != '""' && tip_produc != '"",""') {
            attributes += '&tip_produc=' + tip_produc;
        }

        if (hor_inicio != '' ) {
            attributes += '&hor_inicio=' + hor_inicio;
        }

        if (hor_finxxx != '' ) {
            attributes += '&hor_finxxx=' + hor_finxxx;
        }


        return attributes;
    } catch (e) {
        console.log("Error Fuction getParameFilter: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: showDetailSearch
 *  \brief: Solicita el ajax para mostrar el detalle de la busqueda especifica
 *  \author: Ing. Fabian Salinas
 *  \date: 16/06/2015
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function showDetailSearch(obj) {
    try {
        if (obj.val() == '')
            return false;

        var pop = $(".ui-dialog").length;
        if (pop > 0)
            return false;

        var Standar = $("#standaID");
        var attributes = 'Ajax=on&Option=detailSearch&standa=' + Standar.val();
        attributes += '&window=' + $("#windowID").val();
        attributes += '&ind_entran=' + $("#ind_entranID:checked").val();
        attributes += '&ind_fintra=' + $("#ind_fintraID:checked").val();
        attributes += '&' + obj.attr("name") + '=' + obj.val();

        //Load PopUp
        LoadPopupJQ('open', 'Resultados de la Busqueda', ($(window).height() - 50), ($(window).width() - 50), false, false, true);
        var popup = $("#popID");
        $.ajax({
            url: "../" + Standar.val() + "/inform/class_despac_trans3.php",
            type: "POST",
            data: attributes,
            async: false,
            beforeSend: function() {
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.html("<center><img src=\"../" + Standar.val() + "/imagenes/ajax-loader.gif\"></center>");
            },
            success: function(data) {
                popup.html(data);
                popup.css('overflow-y', 'true');
                popup.css('overflow-x', 'true');
            }
        });
    } catch (e) {
        console.log("Error Fuction showDetailSearch: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: showDetailBand
 *  \brief: Solicita el ajax para mostrar el detallado del resultado de las pestañas
 *  \author: Ing. Fabian Salinas
 *  \date: 16/06/2015
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function showDetailBand(ind_filtro, ind_etapax, cod_transp) {
    try {
        var pop = $(".ui-dialog").length;

        if (pop > 0)
            return false;

        var Standar = $("#standaID");
        var attributes = 'Ajax=on&Option=detailBand&standa=' + Standar.val();
        attributes += '&window=' + $("#windowID").val();
        attributes += '&ind_filact=' + $("#ind_filactID").val();
        attributes += getParameFilter();
        attributes += '&ind_filtro=' + ind_filtro;
        attributes += '&ind_etapax=' + ind_etapax;
        attributes += '&cod_transp=' + cod_transp;

        //Load PopUp
        LoadPopupJQ('open', 'Detalle Bandeja', ($(window).height() - 50), ($(window).width() - 50), false, false, true);
        var popup = $("#popID");
        $.ajax({
            url: "../" + Standar.val() + "/inform/class_despac_trans3.php",
            type: "POST",
            data: attributes,
            async: false,
            beforeSend: function() {
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.html("<center><img src=\"../" + Standar.val() + "/imagenes/ajax-loader.gif\"></center>");
            },
            success: function(data) {
                popup.html(data);
                popup.css('overflow-y', 'true');
                popup.css('overflow-x', 'true');
            }
        });
    } catch (e) {
        console.log("Error Fuction showDetailBand: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: LoadPopupJQ
 *  \brief: Crea o destruye PopUp
 *  \author: Ing. Fabian Salinas
 *	\date: 24/06/2015
 *	\date modified: dia/mes/año
 *  \param: opcion   String   open, close
 *  \param: titulo   String   Titulo del PopUp
 *  \param: alto   	 Integer  Altura PopUp
 *  \param: ancho    Integer  Ancho PopUp
 *  \param: redimen  Boolean  True = Redimencionable
 *  \param: dragg    Boolean  True = El PopUp se puede arrastras
 *  \param: lockBack Boolean  True = Bloquea el BackGround
 *  \return: 
 */
function LoadPopupJQ(opcion, titulo, alto, ancho, redimen, dragg, lockBack) {
    try {
        if (opcion == 'close') {
            $("#popID").dialog("destroy").remove();
        } else {
            $("<div id='popID' name='pop' />").dialog({
                height: alto,
                width: ancho,
                modal: lockBack,
                title: titulo,
                closeOnEscape: false,
                resizable: redimen,
                draggable: dragg,
                buttons: {
                    Cerrar: function() { LoadPopupJQ('close') }
                }
            });
        }
    } catch (e) {
        console.log("Error Fuction LoadPopupJQ: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: justNumbers
 *  \brief: Solo permite digitar numeros
 *  \author: Ing. Fabian Salinas
 *  \date: 16/06/2015
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function justNumbers(e) {
    var keynum = window.event ? window.event.keyCode : e.which;
    if ((keynum == 8) || (keynum == 46))
        return true;

    return /\d/.test(String.fromCharCode(keynum));
}

/*! \fn: generalReport
 *  \brief: Solicita el ajax para mostrar el Informe General
 *  \author: Ing. Fabian Salinas
 *  \date: 16/06/2015
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function generalReport(ind_etapax, id_div) {
    try {
        var standar = $("#standaID");
        var pop = $(".ui-dialog").length;

        if (pop > 0)
            return false;
        //LoadPopupJQ('close');

        //Load PopUp
        LoadPopupJQ('open', 'Cargando...', 'auto', 'auto', false, false, false);
        var popup = $("#popID");

        //Atributos del Ajax
        var atributes = 'Ajax=on&Option=' + ind_etapax;
        atributes += '&standa=' + standar.val();
        atributes += '&ind_filact=' + $("#ind_filactID").val();
        atributes += '&sel_transp=' + $("#sel_transpID").val();
        atributes += '&sel_usuari=' + $("#sel_usuariID").val();
        atributes += getParameFilter();

        //Ajax
        $.ajax({
            url: "../" + standar.val() + "/inform/class_despac_trans3.php",
            type: "POST",
            data: atributes,
            async: true,
            beforeSend: function() {
                popup.parent().children().children('.ui-dialog-titlebar-close').hide();
                popup.html("<center><img src=\"../" + standar.val() + "/imagenes/ajax-loader.gif\"></center>");
                $(".ui-dialog-buttonpane").remove(); // Quitar la zona de botones
                $(".ui-dialog").animate({ "left": ($(window).width() - 135), "top": ($(window).height() - 155) }, 2000);
            },
            success: function(data) {
                $("#" + id_div).html(data);
                $("#" + id_div).css("height", ($(window).height() - 166));
                $("#" + id_div).css("overflow", "scroll");
            },
            complete: function() {
                LoadPopupJQ('close');
            }
        });

    } catch (e) {
        console.log("Error Fuction generalReport: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: generalReport
 *  \brief: Solicita el ajax para mostrar el Informe General
 *  \author: Ing. Fabian Salinas
 *  \date: 16/06/2015
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function getDataSelectPRC() {
    try {
        var standar = $("#standaID");
        //Atributos del Ajax
        var atributes = 'Ajax=on&Option=getLisCiudadOrigne&mIndEtapa=ind_segprc';
        //Ajax
        $.ajax({
            url: "../" + standar.val() + "/inform/class_despac_trans3.php",
            type: "POST",
            data: atributes,
            async: true,
            success: function(data) {
                $("#pun_cargueID").append(data).multiselect("refresh");
            },
            complete: function() {
            }
        });

    } catch (e) {
        console.log("Error Fuction generalReport: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: generalReport
 *  \brief: Solicita el ajax para mostrar el Informe General
 *  \author: Ing. Fabian Salinas
 *  \date: 16/06/2015
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function exportExcel() {
    try {
        location.href="?window=central&cod_servic=1366&menant=1366&opcion=10";
    } catch (e) {
        console.log("Error Fuction exportExcel: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: Actualizarbadge
 *  \brief: Actualiza badge de las novedades moviles
 *  \author: Edward Serrano
 *  \date: 15/05/2015
 *  \date modified: dd/mm/aaaa
 *  \param: etapa: etapa a cual se va a realizar la actualizacion del conteo
 *  \param: etiqueta: idetificador donde se va a actualizar la informacion
 *  \return: 
 */
function Actualizarbadge(etapa, etiqueta) {
    try {
        var standar = $("#standaID");
        //Atributos del Ajax
        var atributes = 'Ajax=on&Option=getConteoNem&etapa='+etapa+'&transp='+getTrans();
        //Ajax
        //result = null;
        $.ajax({
            url: "../" + standar.val() + "/inform/class_despac_trans3.php",
            type: "POST",
            data: atributes,
            //async: false,
            cache:false,
            success: function(data) {
                if(data>0)
                {
                    $("#"+etiqueta).children().addClass("badge");
                    $("#"+etiqueta).children().html(data);
                }
                else
                {
                    $("#"+etiqueta).children().removeClass("badge");
                    $("#"+etiqueta).children().html('');
                }
            },
            complete: function() {
            }
        });
    } catch (e) {
        console.log("Error Fuction Actualizarbadge: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: getTrans
 *  \brief: Obtengo las transportadoras seleccionadas
 *  \author: Edward Serrano
 *  \date: 15/05/2015
 *  \date modified: dd/mm/aaaa
 *  \param: 
 *  \return: 
 */
function getTrans(etapa) {
    var box_checke = $("input[type=checkbox]:checked");
    var cod_transp = '""';
    box_checke.each(function(i, o) {
        if ($(this).attr("name") == 'multiselect_cod_transpID')
        {
            cod_transp += ',' + $(this).val();
        }
    });

    return cod_transp;
}