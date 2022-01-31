/*! \file: inf_despac_finali.js
 *  \brief: Archivo que contiene las sentencias JS para el informe Despachos Finalizados (Informes > Operacion trafico > Finalizados)
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/04/2016
 *  \bug: 
 *  \warning: 
 */

var idForm = 'form_InfDespacFinaliID';
var standar;

/*! \fn: ("body").ready
 *  \brief: Crea los multiselect, calendarios, acordion y pesta√±as. Carga la variable standar.
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
    //$(".multiSel").children().multiselect().multiselectfilter();

    //Pesta√±as
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
                url: "../" + standar + "/inform/inf_despac_finali.php?Ajax=on&Option="+option+"&standa=" + standar,
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

//Global variables
var tablesCreated = [];

/*! \fn: formatlanguage
 *  \brief: Realiza traducciÛn de los dataTables
 *  \author: Ing. Felipe Clavijo
 *  \date:  09/05/2019
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: 
 *  \return: Object
 */
function formatlanguage(){
    var language = 
        {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    
    return language;
}

/*! \fn: report
 *  \brief: Realiza la peticion ajax para pintar el informe General
 *  \author: Ing. Felipe Clavijo
 *  \date:  10/05/2019
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: id_form   String   ID de la tabla en la cual se va a imprimir el ajax
 *  \return: 
 */
function report(id_form) {

    var formData = $("#" + id_form).serializeArray();
    var sendData = {
        "Ajax": "on",
        "Option": "inform",
        "data": {
            "op" : "consultaUsuarios"
        }
    };
    var table = $("#dataTableReport");

    //Clean div
    $("tbody", table).empty();
    
    //Go through form data
    $.each(formData, function(index, value){
        
        if(value["value"] != "")
            if(value["name"] != "standa" && value["name"] != "cod_servic" && value["name"] != "window")
                sendData.data[value["name"]] = value["value"];
            else
                sendData[value["name"]] = value["value"];
    });

    //Validate inform type selected
    if(sendData.data["tipoInforme"] == undefined){
        $("tbody", table).html("<div class='alert alert-warning' style='display: block;'>Seleccionar informe.<br>Se solicita que seleccione un tipo de informe valido para poder continuar.</div>");
        return false;
    }
    
    try {

        $.ajax({
            url: "../" + standar + "/inform/inf_usuari_appxxx.php",
            type: "POST",
            data: sendData,
            dataType: "json",
            async: true,
            beforeSend: function() {
                LockAplication( 
                    "lock",
                    `
                        <span style='line-height: 40px; vertical-align: middle;'>Cargando, por favor espere.</span>
                        <img style='height: 40px;' src='../` + standar + `/js/DataTables/svg/load.svg'>
                    `
                );
            },
            success: function(datos) {

                //Validate data
                if(datos == null || datos == ""){
                    $("tbody", table).html(`<div class='alert alert-warning' style='display: block;'>Sin datos.<br>No se han encontrado registros con el filtro seleccionado.</div>`);
                    return false;
                }

                //Create titles
                titles = [
                    "Id",
                    "Usuario",
                    "Nombre del Conductor",
                    "Correo ElectrÛnico",
                    "Celular",
                    "Tipo Transporte",
                    "Cantidad Registros"
                ];

                //Create rows
                rows = datos;

                //Create JSON to create table
                data = {"titles": titles, "rows" : rows};

                createDataTable(data, table, "inf_usuari_appxx");
            },
            complete: function() {
                LockAplication( "unlock" );
            }
        });
    } catch (e) {
        console.log("Error Function report: " + e.message + "\nLine: " + e.lineNumber);
        return false;
    }
}


/*! 
 *  \fn: formatlanguage
 *  \brief: Realiza traducciÛn de los dataTables
 *  \author: Ing. Felipe Clavijo
 *  \date:  09/05/2019
 *  \date modified: dd/mm/aaaa
 *  \modified by: 
 *  \param: data   Object   => JSON de la informaciÛn de la tabla
 *                  {
 *                      "titles": [
 *                          
 *                      ],
 *                      "rows" : [
 *                          {
 *                              
 *                          }
 *                      ]
 *                  }
 *  \param: table  Object   => Instancia del objeto table $("#table")
 *  \param: name   String   => Nombre de la tabla, para almacenamiento en array tablesCreated
 *  \return: Object
 */

function createDataTable(data, table, name){

    //Validate thead
    if($("thead", table).length == 0){
        table.append($("<thead>"));
    }

    //Validate tbody
    if($("tbody", table).length == 0){
        table.append($("<tbody>"));
    }else{
        $("tbody", table).empty();
    }

    //Create titles
    if($("thead", table).children().length == 0){
        var tr = $("<tr>");
        $.each(data["titles"], function(index, value){

            var th = $("<th>");
            th.html(value);

            tr.append(th);
            
        });
        $("thead", table).append(tr);
    }

    //Validate created tables
    if(tablesCreated.find(function(element){ return element == name; })){
        var dataTableDialog = table.DataTable();
    }else{
        var dataTableDialog = table.DataTable({
            language: formatlanguage(),
            dom: 'Bfrtip',
            buttons: [
                'excel'
            ] 
        });
        tablesCreated.push(name);
    }

    //Clean table
    dataTableDialog.clear();

    //Create rows
    $.each(data["rows"], function(index, value){

        var tr = $("<tr>");

        //Go through fields
        $.each(value, function(index1, value1){

            //Create cell
            var td = $("<td>");

            //Assign value
            td.html(value1);

            //Assign cell to row
            if(td != undefined)
                tr.append(td);

        });

        dataTableDialog.row.add(tr).draw( false );
    });
}