/*! \despacinf_despac_itiner.js
 *  \brief: Archivo que contiene las sentencias JS para el informe Despachos Finalizados (Informes > Operacion trafico > Finalizados)
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/04/2016
 *  \bug: 
 *  \warning: 
 */

var idForm = 'form_infDespacErrorItinerariosID';
var standar;

/*! \fn: ("body").ready
 *  \brief: Crea los multiselect, calendarios, acordion y pestaÃ±as. Carga la variable standar.
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

    //PestaÃ±as
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
        source: function (request, response)
        {
            if( $( "*:focus" ).attr('name') == 'nom_conduc' ){
                option = 'getConduc';
            }else{
                option = 'getCiudades';
            }

            $.ajax(
            {
                url: "../" + standar + "/despac/inf_despac_itiner.php?Ajax=on&Option="+option+"&standa=" + standar,
                dataType: "json",
                data: 
                {
                    term: request.term,
                },
                success: function (data)
                {
                    response(data);
                }
            });
        },
        minLength: 3,
        delay: 100, 
        select: function (event, ui)
        {
            switch( $(this).attr('name') ){
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
 *  \param: ind_pestan  String  Indicador de la pestaÃ±a a la que pertenece
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
            url: "../" + standar + "/despac/inf_despac_itiner.php",
            type: "POST",
            data: attributes,
            async: true,
            beforeSend: function() {
                BlocK('Generando Informe...', true);
            },
            success: function(datos) {
                $("#" + id_div).siblings("div").empty();
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
        var tip_despac =[];

        if( $("#nom_ciuoriID").val() == '' ){
            $("#cod_ciuoriID").val('');
        }
        if( $("#nom_ciudesID").val() == '' ){
            $("#cod_ciudesID").val('');
        }
        if( $("#nom_conducID").val() == '' ){
            $("#cod_conducID").val('');
        }

        var a = 0,
            b = 0;         

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
        var va3 = true;
        var msj;
        var fec_finali = $("#fec_finaliID").val();
        var fec_inicia = $("#fec_iniciaID").val();

        if( fec_finali != '' && fec_inicia != '' ){
            var dias = getDiasTrascurridos(fec_finali, fec_inicia);

            if (dias > 31) {
                msj = 'El rango de fechas no debe superar los 31 dias.';
                va2 = false;
            } else if (dias < 0) {
                msj = 'El rango de fechas no es valido.';
                va2 = false;
            }
        }
        
        if(!va2){
            setTimeout(function() {
                inc_alerta('fec_iniciaID', msj);
                inc_alerta('fec_finaliID', msj);
            }, 530);
            return false;
        }else if (!val || !data) {
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

/*! \fn: showDesinatarios
 *  \brief: Muestra los destinatarios por despacho
 *  \author: Ing. Edgar Felipe Clavijo Santoyo
 *  \date: 06/06//2019
 *  \date modified: dd/mm/aaaa
 *  \param: object Object Elemento clickeado
 */
function showDesinatarios(object)
{
    //Get "num_despac" cell
    let num_despac = $(object).parent().siblings()[2].innerHTML;

    //Get "Destinatarios"
    $.ajax({
        url: "../" + standar + "/despac/inf_despac_itiner.php",
        type: "POST",
        dataType: 'json',
        data: {Option: "getDestinatarios", Ajax: "on", num_despac: num_despac},
        async: true,
        beforeSend: function() {
            BlocK('Generando Informe...', true);
        },
        success: function(data) {

            console.log(data);

            //Create "Destinatarios" popUp
            let popUp = $(`
                            <div id='popUpP' onClick='closePopUpP( event, this )' style='
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background-color: #0005;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                overflow-x: scroll;
                            '>
                                <div style='
                                    background: #dde4e8;
                                    padding: 20px;
                                    border-radius: 20px;
                                    position: absolute;
                                    top: 0;
                                '>
                                    <form id="updateDestinatarios">
                                        <table style='
                                            border: 1px solid #000;
                                        '></table>
                                    </form>
                                </div>
                            </div>
                        `);

                        

            //Get sizes
            let operadorGPS = data["GPS"][0]["Operador GPS"].length * 7;
            let usuarioGPS = data["GPS"][0]["Usuario GPS"].length * 7;
            let contrasenaGPS = data["GPS"][0]["Contrasena GPS"].length * 7;
            let idGPS = data["GPS"][0]["Id GPS"].length * 7;

            //Create select codes GPS
            let cod_operad = "<option value=''>--Seleccione--</option>";
            let correctOperad = false;
            data["cod_operad"].forEach(value => {
                if(value["cod_operad"] == data["GPS"][0]["Operador GPS"]){
                    correctOperad = true;
                    cod_operad += "<option selected='selected' value='" + value["cod_operad"] + "'>" + value["nom_operad"] + "</option>";
                }else{
                    cod_operad += "<option value='" + value["cod_operad"] + "'>" + value["nom_operad"] + "</option>";
                }
            });

            let gps = $(`
                <tr>
                    <th class="CellHead" colspan="4" style="text-align:left">Configuración GPS</th>
                </tr>
                <tr>
                    <td id="tag0TD" class="cellInfo1" valign="" rowspan="" colspan="" height="">
                        <labelname="tag0" id="tag0ID">Operador GPS: </label><br>
                        <select type="text" name="gps_operad" id="` + num_despac + `CodOperad" onChange="validateGPS(event)" value="` + data["GPS"][0]["Operador GPS"] + `" style='width: ` + operadorGPS + `px; min-width: 110px;'>
                            ` + cod_operad + `
                        </select>
                    </td>
                    <td id="tag0TD" class="cellInfo1" valign="" rowspan="" colspan="" height="">
                        <labelname="tag0" id="tag0ID">Id GPS: </label><br>
                        <input type="text" class="campo_texto" name="gps_idxxxx" style='width: ` + idGPS + `px; min-width: 80px;' value="` + data["GPS"][0]["Id GPS"] + `" size="20"/>
                    </td>
                    <td id="tag0TD" class="cellInfo1" valign="" rowspan="" colspan="" height="">
                        <labelname="tag0" id="tag0ID">Usuario GPS: </label><br>
                        <input type="text" class="campo_texto" name="gps_usuari" id="gps_usuari" style='width: ` + usuarioGPS + `px; min-width: 80px;' value="` + data["GPS"][0]["Usuario GPS"] + `" size="20" ` + (correctOperad ? "required" : "") + `/>
                    </td>
                    <td id="tag0TD" class="cellInfo1" valign="" rowspan="" colspan="" height="">
                        <labelname="tag0" id="tag0ID">Contraseña GPS: </label><br>
                        <input type="text" class="campo_texto" name="gps_paswor" id="gps_paswor" style='width: ` + contrasenaGPS + `px; min-width: 80px;' value="` + data["GPS"][0]["Contrasena GPS"] + `" size="20" ` + (correctOperad ? "required" : "") + `/>
                    </td>
                </tr>
            `);

            if(data["destin"].length != 0){
                
                //Go through data
                data["destin"].forEach(destin => {

                    //Get sizes
                    let direccion = destin["Direccion"].length * 7;
                    let latitid = destin["Latitud"].length * 7;
                    let longitud = destin["Longitud"].length * 7;
                    
                    //Create "Destinatario" intarface
                    let interface = $(`
                        <tr>
                            <th class="CellHead" colspan="4" style="text-align:left">C&oacute;digo: ` + destin["Codigo"] + ` | Cliente: ` + destin["Nombre"] + ` | Ciudad: ` + destin["Ciudad"] + `</th>
                        </tr>
                        <tr>
                            <td id="tag0TD" class="cellInfo1" valign="" rowspan="" colspan="2" height="">
                                <input type="hidden" name="cod_remdes[]" value="` + destin["Codigo"] + `" />
                                <input type="hidden" name="num_despac" value="` + destin["Despacho"] + `" />
                                <labelname="tag0" id="tag0ID">Direcci&oacute;n: </label><br>
                                <input type="text" class="campo_texto" name="dir_remdes[]" style='width: ` + direccion + `px; min-width: 80px;' value="` + destin["Direccion"] + `" size="20" required/>
                            </td>
                            <td id="tag0TD" class="cellInfo1" valign="" rowspan="" colspan="" height="">
                                <labelname="tag0" id="tag0ID">Longitud: </label><br>
                                <input type="text" class="campo_texto" name="cod_longit[]" style='width: ` + longitud + `px; min-width: 80px;' value="` + destin["Longitud"] + `" size="20" required/>
                            </td>
                            <td id="tag0TD" class="cellInfo1" valign="" rowspan="" colspan="" height="">
                                <labelname="tag0" id="tag0ID">Latitud: </label><br>
                                <input type="text" class="campo_texto" name="cod_latitu[]" style='width: ` + latitid + `px; min-width: 80px;' value="` + destin["Latitud"] + `" size="20" required/>
                            </td>
                        </tr>
                    `);

                    //Add "Destinatario" intarface to popUp
                    $(" > div table", popUp).append(interface);
                });
            }

            //Create button interface
            let button = $(`
                    <tr>
                        <th class="CellHead" colspan="4" style="text-align:center"></th>
                    </tr>
                    <tr>
                        <th class="CellHead" colspan="4" style="text-align:center">
                            <input type="submit" value="Actualizar" />
                            <button onClick="closePopUpP( event, this )">Cerrar</button>
                        </th>
                    </tr>
            `);

            //Add button to popUp
            $(" > div table", popUp).append(gps, button);

            //Add popUp to DOM
            $("body").append(popUp);

            //Create event submit form
            $("#updateDestinatarios").bind("submit", function(e){

                //Prevent submit
                e.preventDefault();

                //Get formData
                formData = $(this).serialize()+"&Option=updateDestinatarios&Ajax=on&num_despac="+num_despac;

                //Get "Destinatarios"
                $.ajax({
                    url: "../" + standar + "/despac/inf_despac_itiner.php",
                    type: "POST",
                    //dataType: 'json',
                    data: formData,
                    async: true,
                    beforeSend: function() {
                        BlocK('Generando Informe...', true);
                    },
                    success: function(data) {
                        if(data == "Correct"){
                            alert("Destinatarios modificados con éxito.");
                            $("#popUpP").remove();
                        }else{
                            alert("Ha ocurrido un error");
                        }
                    },
                    complete: function() {
                        BlocK();
                    }
                });
            });
        },
        complete: function() {
            BlocK();
        }
    });
}
function closePopUpP(event, object){

    if(event.target.tagName == "BUTTON"){
        $("#popUpP").remove();
    }else{
        if(event.target == object){
            $("#popUpP").remove();
        }
    }
}
function validateGPS(event){
    if($(event.target).val() == ""){
        $("#gps_paswor").removeAttr("required");
        $("#gps_usuari").removeAttr("required");
    }else{
        $("#gps_paswor").attr("required", "required");
        $("#gps_usuari").attr("required", "required");
    }
}

/*! \fn: reenvio
 *  \brief: Reenvia a itinerario el despacho
 *  \author: Ing. Nelson Liberato
 *  \date: 2019-05-22 
 *  \return: 
 */
function reenvio() 
{
    try 
    {
        var tableCheck = $(".DLTable input[type=checkbox]:checked:not(:disabled)");
        if(tableCheck.length <= 0){
            return alert("Debe seleccionar al menos un despacho para retransmitir a Integrador GPS!");
        }
        //-- $.each(tableCheck, function(i, o) {
        //--     console.log( o );
        //-- })

        $("#OptionID").val('setForm');
        setTimeout(function() {
            $("form").submit();
        }, 530);
    } 
    catch (e) 
    {
        console.log("Error Function expTabExcelNovupd: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}


function goBack()
{
    try 
    {
        $("form").submit();
    } 
    catch (e) 
    {
        console.log("Error Function goBack: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}

/*! \fn: valiChecked
 *  \brief: Valida si el check principal para marcar el resto de checks
 *  \author: Ing. Luis Manrique
 *  \date:  03/09/2019
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: object
 *  \return: 
 */
function valiChecked(objet) {
    var despachos = "";

    $(".DLTable input[type=checkbox]:not(#DLCheckAll)").each(function(){
        var classchecked = "";
        var tr = $(this).parent().parent();
        var attr = tr.attr('data-class'); 
        classchecked = tr.attr("class");
        console.log(attr);
        if(attr == undefined){
            tr.attr("data-class", classchecked);
        }
    });


    if($(objet).is(":checked")){
        $(".DLTable input[type=checkbox]:not(#DLCheckAll)").each(function(){
            $(this).attr("checked", "checked");
            var tr = $(this).parent().parent();
            var classchecked = tr.attr("data-class");
            if (tr.hasClass("DLRowClick") == false){
                tr.removeClass (classchecked);
                tr.addClass ("DLRowClick");
            }
            if($(this).is(":checked")){
                var id = $(this).attr("id");
                id = id.replace("DLCheck","DLCell");
                var despacho = $(this).parents("td").siblings("#"+id+"-1").text();

                if(despachos == ""){
                    despachos = despacho;
                }else{
                    despachos += "::"+despacho;
                }
            }
        });
        $("#DLSelectedRowsID").val(despachos);
    }else{
        $(".DLTable input[type=checkbox]:not(#DLCheckAll)").each(function(){
            $(this).removeAttr("checked");
            var tr = $(this).parent().parent();
            var classchecked = tr.attr("data-class");
            if (tr.hasClass("DLRowClick") == true){
                tr.removeClass("DLRowClick");
                tr.addClass(classchecked);
            }
        });
        $("#DLSelectedRowsID").val("");
    }
}