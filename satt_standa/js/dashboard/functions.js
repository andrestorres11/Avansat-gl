//Global variables
var createdTables = [];

//Functions
function camelCaseFormat(string)
{
    string = string.replace(/ /g, "");
    return string.charAt(0).toLowerCase() + string.slice(1);
}

function randomColor(type = "pastel")
{
    switch (type)
    {
        case "pastel":

            return "hsl(" + 360 * Math.random() + ',' +
            (25 + 70 * Math.random()) + '%,' + 
            (85 + 10 * Math.random()) + '%)';
        
        case "opaque":

            return "hsl(" + 360 * Math.random() + ',' +
            20 + '%,' + 
            30 + '%)';
        
        case "neutral":

            return "hsl(" + 360 * Math.random() + ',' +
            50 + '%,' + 
            40 + '%)';

        case "grandRange":

            return "hsl(" + 360 * Math.random() + ',' +
            (60 + 20 * Math.random()) + '%,' + 
            (25 + 17 * Math.random()) + '%)';
        
        default:

            return "hsl(0, 0%, 0%)";
    }
}

function compareObjects(value, other, values = false) {

	// Get the value type
	var type = Object.prototype.toString.call(value);

	// If the two objects are not the same type, return false
	if (type !== Object.prototype.toString.call(other)) return false;

	// If items are not an object or array, return false
	if (['[object Array]', '[object Object]'].indexOf(type) < 0) return false;

	// Compare the length of the length of the two items
	var valueLen = type === '[object Array]' ? value.length : Object.keys(value).length;
	var otherLen = type === '[object Array]' ? other.length : Object.keys(other).length;
    if (valueLen !== otherLen) return false;

	// Compare two items
	var compare = function (item1, item2) {

		// Get the object type
		var itemType = Object.prototype.toString.call(item1);

		// If an object or array, compare recursively
		if (['[object Array]', '[object Object]'].indexOf(itemType) >= 0) {
			if (!isEqual(item1, item2)) return false;
		}

		// Otherwise, do a simple comparison
		else {

			// If the two items are not the same type, return false
			if (itemType !== Object.prototype.toString.call(item2)) return false;

			// Else if it's a function, convert to a string and compare
			// Otherwise, just compare
			if (itemType === '[object Function]') {
				if (item1.toString() !== item2.toString()) return false;
			} else {
                if(values)
				    if (item1 !== item2) return false;
			}

		}
	};

    // Compare properties
    if (type === '[object Array]') {
        for (var i = 0; i < valueLen; i++) {
            if (compare(value[i], other[i]) === false) return false;
        }
    } else {
        for (var key in value) {
            if (value.hasOwnProperty(key)) {
                if (compare(value[key], other[key]) === false) return false;
            }
        }
    }
 

	// If nothing failed, return true
	return true;

}

function dashboardDialogEvent(object){

    var dashboardDialog = $("#dashBoardTableTrans .dashBoardDialog");
    var element = $(object);

    if(element.prop("tagName") == "TD"){
        var row = element.parent();
    }else if(element.prop("tagName") == "A")
    {
        var row = element.parent().parent();
    }else{
        var row = element;
    }

    var despacho = row.find(".noDespacho a").html()

    //Add data
    dashboardDialog.find("#viaje").html("<a href='index.php?cod_servic=3302&window=central&despac=" + despacho + "&tie_ultnov=20511&opcion=1&etapa=prc' target='_blank'>" + row.find(".viaje a").html() + "</a>");
    dashboardDialog.find("#placa").html(row.find(".placa").html());
    dashboardDialog.find("#noDespacho").html("<a href='index.php?cod_servic=3302&window=central&despac=" + despacho + "&tie_ultnov=20511&opcion=1&etapa=prc' target='_blank'>" + despacho + "</a>");
    dashboardDialog.find("#ruta").html("<span class='primary'>Ruta</span>: <span class='secundary'>" + row.find(".origen").html() + " - " + row.find(".destino").html() + "</span>");

    //Get necessary graphic data
    $.ajax({
        url: '../satt_standa/despac/filterData.php?opcion=3',
        data: {"num_despac": despacho},
        type: 'get',
        dataType: 'json',
        success: function(data){

            //Grid graphic
            grid = {
                "left": 70,
                "right": 15,
                "top": 100,
                "bottom": 100,
            }

            if(!createGraphics(data["xAxis"], data["yAxis"], data["data"], $('#dashBoardTableTrans #echart_scatter'), grid)){
                console.log("Ha ocurrido un error generando el gr&aacute;fico");
            }
        },
        beforeSend: function(){
            loadAjax("start")
        },
        complete: function(){
            loadAjax("end")
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error al cargar el gr&aacute;fico.", $("#dashBoardTableTrans #echart_scatter"), "HTML")
        }
    });

    //Get Cargue table necessary data
    $.ajax({
        url: '../satt_standa/despac/filterData.php?opcion=4',
        data: {"num_despac": despacho},
        type: 'get',
        dataType: 'json',
        success: function(data){
            createDialogTable(data, $("#cargue table"), "cargueTable");
        },
        beforeSend: function(){
            loadAjax("start")
        },
        complete: function(){
            loadAjax("end")
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error al cargar la tabla.", $("#cargue table tbody"), "table")
        }
    });

    //Get Cargue table necessary data
    $.ajax({
        url: '../satt_standa/despac/filterData.php?opcion=5',
        data: {"num_despac": despacho},
        type: 'get',
        dataType: 'json',
        success: function(data){
            createDialogTable(data, $("#descargue table"), "descargueTable");
        },
        beforeSend: function(){
            loadAjax("start")
        },
        complete: function(){
            loadAjax("end")
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error al cargar la tabla.", $("#descargue table tbody"), "table")
        }
    });

    //Show dialog
    dashboardDialog.css("display", "block");
}

function objectLength( object ) {
    var length = 0;
    for( var key in object ) {
        if( object.hasOwnProperty(key) ) {
            ++length;
        }
    }
    return length;
};

function formatlanguage(){
    var language = 
        {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    
    return language;
}

function loadAjax(x){
    try {
        if(x == "start"){
            $.blockUI({ message: '<div class="bg-primary">Espere un momento</div>' });
        }else{
            $.unblockUI();
        }
    } catch (error) {
        console.log(error);
    }
    
}

function personalizedAlert(type, title, message, automaticClosing, globalContainer){

    //Create Objects
    var div = $("<div>");
    var a = $("<a>");
    var strong = $("<strong>");
    var divText = $("<div>");

    //Assign Attributes
    div.attr("class", "alerts alert alert-" + type + " alert-dismissible fade in");
    div.hide();
    a.attr({"href": "#", "class": "close", "data-dismiss": "alert", "aria-label": "close"});
    a.html("&times;");
    strong.html(title);
    divText.html(message);

    //Add Objects
    div.append(strong, a, divText);

    //Show Object
    globalContainer.append(div);
    div.slideDown(300);

    //Validate automatic closing
    if(automaticClosing){

        //Close
        setTimeout(function(){
            div.fadeOut("slow", function(){
                this.remove();
            });
        }, 4000);
    }
}
function errorAjax(jqXHR, exception, title, elementMessage, typeMessage){

    console.log(jqXHR, exception);

    //Format error
    if (jqXHR.status === 0) {
        message = 'Sin conexi&oacuten.\n Verifique su red.';
    } else if (jqXHR.status == 404) {
        message = 'P&aacute;gina solicitada no encontrada. [404]';
    } else if (jqXHR.status == 500) {
        message = 'Error del servidor [500].';
    } else if (exception === 'parsererror') {
        message = 'Ha ocurrido un error con la solicitud.';
    } else if (exception === 'timeout') {
        message = 'Tiempo agotado para esta solicitud.';
    } else if (exception === 'abort') {
        message = 'Ha ocurrido un error con la solicitud.';
    } else {
        message = 'Error desconocido.\n' + jqXHR.responseText;
    }

    //validate the ways to show the error
    if(typeMessage == "table"){
        $(elementMessage).html(`
            <tr>
                <td colspan='100' class='btn-danger'>` + title + "<br>" + message + `</td>
            </tr>
        `);
        return true;
    }else if(typeMessage == "HTML"){
        $(elementMessage).html("<div class='btn-danger'>" + title + "<br>" + message + "</div>");
        return true;
    }else if(typeMessage == "alert"){
        personalizedAlert("danger", title, message, true, $("#dashBoardTableTrans"));
        return true;
    }else if(elementMessage == "" || elementMessage.length == 0 || elementMessage == undefined || elementMessage == null){
        personalizedAlert("danger", title, message, true, $("#dashBoardTableTrans"));
        return true;
    }else{
        $(elementMessage).html("<div class='btn-danger'>" + title + "<br>" + message + "</div>");
        return true;
    }
    
}
function loadSelectableFields(){
    
    $.ajax({
        url: '../satt_standa/despac/filterData.php?opcion=2',
        dataType: 'json',
        async: false,
        success: function(data){
            
            for (const key in data)
            {
                if (data.hasOwnProperty(key))
                {
                    //Necessary data
                    const necessaryData = data[key];
                    let size = null;

                    //Calculate size
                    if(necessaryData["rowMaxQuantity"] > 12)
                        size = 1
                    else
                        size = 12 / necessaryData["rowMaxQuantity"];

                    //Create selectable fields
                    createSelectableFields(
                        necessaryData["type"],
                        necessaryData["rowContainer"],
                        necessaryData["elementContainer"],
                        size,
                        necessaryData["data"],
                        key,
                        necessaryData["name"],
                        $(necessaryData["container"]));
                }
            }
        },
        beforeSend: function(){
            loadAjax("start")
        },
        complete: function(){
            loadAjax("end")
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error al cargar el campo.", "", "alert")
        }
    });

    
}
function createSelectableFields(type, rowContainer, elementContainer, size, data, name, visualName, container){
    
    var cloneRowContainer = (rowContainer != null && rowContainer != "" && rowContainer != undefined) ? $("<" + rowContainer + ">") : null;
    var cloneElementContainer = (elementContainer != null && elementContainer != "" && elementContainer != undefined) ? $("<" + elementContainer + ">") : null;
    var cloneElement = null;
    var parentElement = null;
    let orderField = "second";

    //Select elemets to create
    switch (type) {

        case "checkbox":
            orderField = "first";
            cloneElement = $("<input>").attr("type", "checkbox").attr("name", name + "[]");

            //Add Bootstrap styles
            cloneRowContainer.addClass("checkbox");
            break;

        case "radio":
            orderField = "first";
            cloneElement = $("<input>").attr("type", "radio").attr("name", name);
            break;

        case "select":
            parentElement = $("<select>").attr("name", name).addClass("form-control");
            cloneElement = $("<option>");

            //Add empty option
            parentElement.append(cloneElement.html("--Seleccione--").val(""));
            break;

        case "selectMultiple":
            parentElement = $("<select>").attr("name", name + "[]").attr("multiple", "multiple").addClass("form-control");
            cloneElement = $("<option>");
            break;
        
        default:
            parentElement = $("<select>").attr("name", name).addClass("form-control");
            cloneElement = $("<option>");
            break;
    }

    //Validate no row container
    if(rowContainer == elementContainer){
        cloneRowContainer = container;
    }

    //Validate subtype
    if(parentElement != null)
    {
        //Fill parent element
        for (const key in data)
        {
            if (data.hasOwnProperty(key))
            {
                const subitemData = data[key];
                
                //Create subitem
                let subitem = cloneElement.clone();

                //Fill subitem
                subitem.html(subitemData.value).val(subitemData.id)

                //Assign subitem to parent
                parentElement.append(subitem)
            }
        }

        //Fill elementContainer
        if(rowContainer == "tr" || rowContainer == "td")
        {
            visualNameContainer = cloneElementContainer.clone().addClass("col-sm-" + size).html(visualName);
            parentElementContainer = cloneElementContainer.clone().addClass("col-sm-" + size).append(parentElement);
            
            //Fill rowContainer
            if(orderField == "second")
                cloneRowContainer.append(visualNameContainer, parentElementContainer);
            else
                loneRowContainer.append(parentElementContainer, visualNameContainer);
        }
        else
        {
            visualNameContainer = $("<span>").addClass("fieldLabel").html(visualName);
            parentElementContainer = parentElement;

            //Fill rowContainer
            if(orderField == "second")
                cloneRowContainer.append(cloneElementContainer
                                            .clone()
                                            .addClass("col-sm-" + size)
                                            .append(
                                                visualNameContainer,
                                                parentElementContainer
                                            )
                )
            else
                cloneRowContainer.append(cloneElementContainer
                                        .clone()
                                        .addClass("col-sm-" + size)
                                        .append(
                                            parentElementContainer,
                                            visualNameContainer
                                        )
                )
        }


        //Create element
        container.append(cloneRowContainer);
    }else{
        
        //Add Bootstrap styles
        cloneRowContainer.addClass("row");

        //Fill rowContainer
        for (const key in data) {
            if (data.hasOwnProperty(key)) {
                const elementData = data[key];
                
                //Create and fill element
                let element = cloneElement.clone().val(elementData.id)

                //Fill elementContainer
                if(rowContainer == "tr")
                {
                    visualNameContainer = cloneElementContainer.clone().addClass("col-sm-" + size).html(elementData.value);
                    parentElementContainer = cloneElementContainer.clone().addClass("col-sm-" + size).append(element);
                    
                    //Fill rowContainer
                    if(orderField == "second")
                        cloneRowContainer.append(visualNameContainer, parentElementContainer);
                    else
                        loneRowContainer.append(parentElementContainer, visualNameContainer);
                }
                else
                {
                    visualNameContainer = $("<span>").addClass("fieldLabel").html(elementData.value);
                    parentElementContainer = element;

                    //Fill rowContainer
                    if(orderField == "second")
                        cloneRowContainer.append(cloneElementContainer
                                                    .clone()
                                                    .addClass("col-sm-" + size)
                                                    .append(
                                                        visualNameContainer,
                                                        parentElementContainer
                                                    )
                        )
                    else
                        cloneRowContainer.append(cloneElementContainer
                                                .clone()
                                                .addClass("col-sm-" + size)
                                                .append(
                                                    parentElementContainer,
                                                    visualNameContainer
                                                )
                        )
                }
            }
        }

        //Create element
        container.append(cloneRowContainer);
    }
}

//Create tr
function createTr(row){

//Validate request
    //Itinerario
    var itinerario = "";
    if(row["itinerario"] == "Por Iniciar"){
        itinerario = "danger";
    }else if(row["itinerario"] == "No Requiere"){
        itinerario = "white";
    }else{
        itinerario = "success";
    }
    
    //Reporte
    var reporte = "";
    if(row["reporte"] != ''){
        reporte = row["reporte"];
    }

    //Estado de Cargue - Fecha Cargue
    var estadoDeCargue = "";
    var fechaDeCargue = "";
    if(row["estadoDeCargue"] == "Atrasado" || row["estadoDeCargue"] == "No cumpli&oacute"){
        estadoDeCargue = "danger";
        fechaDeCargue = "danger";
    }else if(row["estadoDeCargue"] == "A tiempo" || row["estadoDeCargue"] == "Adelantado" || row["estadoDeCargue"] == "Cumpli&oacute"){
        estadoDeCargue = "success";
        fechaDeCargue = "success";
    }
    
    //Estado de Descargue - Fecha de Descargue
    var estadoDeDescargue = "";
    var fechaDeDescargue = "";
    if(row["estadoDeDescargue"] == "Atrasado" || row["estadoDeDescargue"] == "No cumpli&oacute"){
        estadoDeDescargue = "danger";
        fechaDeDescargue = "danger";
    }else if(row["estadoDeDescargue"] == "A tiempo" || row["estadoDeDescargue"] == "Adelantado" || row["estadoDeDescargue"] == "Cumpli&oacute"){
        estadoDeDescargue = "success";
        fechaDeDescargue = "success";
    }else{
        row["estadoDeDescargue"] = "Pendiente";
    }

    //Cumplimiento de Plan de Ruta
    var cumplimientoDePlanDeRuta = "";
    if(row["cumplimientoDePlanDeRuta"] > 80){
        cumplimientoDePlanDeRuta = "success";
    }else if(row["cumplimientoDePlanDeRuta"] > 30 && row["cumplimientoDePlanDeRuta"] <= 80){
        cumplimientoDePlanDeRuta = "warning";
    }else{
        cumplimientoDePlanDeRuta = "danger";
    }


    //Clean null
    if(row["estadoDeDescargue"] == null){
        row["estadoDeDescargue"] = " ";
    }
    
    if(row["fechaDeDescargue"] == null){
        row["fechaDeDescargue"] = " ";
    }
    
    if(row["localizacion"] == null){
        row["localizacion"] = " ";
    }

//Create Elements
    //Tr
    var tr = $(`<tr>
        <td class='openDashBoardDialog viaje' style='white-space: nowrap;'><a href='#' onclick='dashboardDialogEvent(this); return false;'>`+row["viaje"]+`</a></td>
        <td class='openDashBoardDialog noDespacho loadDashboars' id=`+row["noDespacho"]+`' style='white-space: nowrap;'><a href='index.php?cod_servic=3302&window=central&despac=` + row["noDespacho"] + `&tie_ultnov=20511&opcion=1&etapa=prc' target='_blank'>` + row["noDespacho"] + `</a></td>
        <td class='itinerario'><span class='btn btn-` + itinerario + ` btn-xs'>`+row["itinerario"]+`</spam></td>
        <td class='reporte'><i class='glyphicon glyphicon-off btn-` + reporte +`'</i></td>
        <td class='etapa'><b>`+row["etapa"]+`</td>
        <td class='tiempo `+row["colorAlarma"]+`' ><b>`+row["tiempoAlarma"]+`</td>
        <td class='placa' style='white-space: nowrap;'>`+row["placa"]+`</td>
        <td class='operadorGPS' style='white-space: nowrap;'>`+row["operadorGPS"]+`</td>
        <td class='transportadora' style='white-space: nowrap;'>`+row["transportadora"]+`</td>
        <td class='origen' style='white-space: nowrap;'>`+row["origen"]+`</td>
        <td class='destino' style='white-space: nowrap;'>`+row["destino"]+`</td>
        <td class='estadoDeCargue'><span class='btn btn-` + estadoDeCargue + `'>`+row["estadoDeCargue"]+`</spam></td>
        <td class='fechaDeCargue'><span class='btn btn-` + fechaDeCargue + `'>`+row["fechaDeCargue"]+`</spam></td>
        <td class='estadoDeDescargue'><span class='btn btn-` + estadoDeDescargue + `'>`+row["estadoDeDescargue"]+`</spam></td>
        <td class='fechaDeDescargue'><span class='btn btn-` + fechaDeDescargue + `'>`+row["fechaDeDescargue"]+`</spam></td>
        <td class='procesoDeEntrega'>
            <div class='verticalTable'>
                <div class='btn-warning'>T</div>
                <div>` + row["procesoDeEntrega"]["t"] + `</div>
                <div class='btn-success' style="background-color:#67ab3d !important">D</div>
                <div>` + row["procesoDeEntrega"]["d"] + `</div>
                <div class='btn-danger'>P</div>
                <div>` + row["procesoDeEntrega"]["p"] + `</div>
                <div class='btn-success' style="background-color:#67ab3d !important">C</div>
                <div>` + row["procesoDeEntrega"]["c"] + `</div>
                <div class='btn-danger'>N</div>
                <div>` + row["procesoDeEntrega"]["n"] + `</div>
            </div>
        </td>
        <td class='tdLocalizacion' style='text-align: initial;'>`+row["localizacion"]+`</td>"
        <td class='fechaNovedad' style='white-space: nowrap;'>`+row["fechaNovedad"]+`</td>
        <td class='usuarioNovedad' style='white-space: nowrap;'>`+row["usuarioNovedad"]+`</td>
        <td class="cumplimientoDePlanDeRuta project_progress">
            <div class="progress progress_sm">
            <div class="progress-bar btn-` + cumplimientoDePlanDeRuta + `" role="progressbar" data-transitiongoal="` + row["cumplimientoDePlanDeRuta"] + `" title="` + row["cumplimientoDePlanDeRuta"] + `%"
            style="
                width: ` + row["cumplimientoDePlanDeRuta"] + `%;
            ">` + row["cumplimientoDePlanDeRuta"] + `%</div>
            </div>
        </td>
        <td class='conductor' style='white-space: nowrap;'>`+row["conductor"]+`</td>
        <td class='celularConductor' style='white-space: nowrap;'>`+(row["celularConductor"] ? row["celularConductor"]:'N/a')+`</td>"
        <td class='poseedor' style='white-space: nowrap;'>`+row["poseedor"]+`</td>
        <td class='ultimaNovedad' style='white-space: nowrap;'>`+row["nom_noveda"]+`</td></tr>"`);

    return tr;
}

function executeFilter(){

    try {
        //Get data
        $.ajax({
            url: '../satt_standa/despac/filterData.php?opcion=1',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize(),
            beforeSend: function(){
                loadAjax("start")
            },
            success: function(data){

                console.log(data);   

                //Clean table body
                table.rows().remove().draw();
                counter = 1;

                //Validate empty
                if(objectLength(data) == 0){
                    personalizedAlert("danger", "No se encontraron registros.", "No se ha encontrado ning&uacute;n registro, con el filtro especificado, por favor val&iacute;delo.", true, $("#dashBoardTableTrans"));
                    return false;
                }

                //Go through json data
                for(var i=0; i < data.length; i++){
                    //console.log(createTr(data[i]));         
                    table.row.add(createTr(data[i])).draw( false );
                }

                //Count Data
                $("#countRowsDataTable").html(" | DESPACHOS ENCONTRADOS: " + data.length);

                // Progressbar
                /*if ($(".progress .progress-bar")[0]) {
                    $('.progress .progress-bar').progressbar();
                }*/

                //Add table controllers event
                $("#filter #dataTable_DashBoard").on("DOMSubtreeModified", function(){

                    // Progressbar
                    /*if ($(".progress .progress-bar")[0]) {
                        $('.progress .progress-bar').progressbar();
                    }

                    //Add event dasbhboard dialog
                    $(".openDashBoardDialog a").unbind("click");
                    $(".openDashBoardDialog a").on("click", function(){
                        
                        dashboardDialogEvent(this);
                    });*/
                });

                //Add event dasbhboard dialog
                /*$(".openDashBoardDialog a").on("click", function(){
                    
                    dashboardDialogEvent(this);
                });*/
            },
            complete: function(){
                loadAjax("end")
            },
            error: function(jqXHR, exception){
                errorAjax(jqXHR, exception, "Error al cargar el campo.", "", "alert")
            }
        });
    } catch (error) {
        console.log(error);
    }

    
}

//GRAPHICS FUNCTION
function createGraphics(xLabels, yLabels, data, container, grid = {}, legend = true, tooltipData = true) {

    //Validate empty data
    if(objectLength(data) == 0 || objectLength(data["data"]) || objectLength(data["seires"])){
        $(container).html("<div class='alert alert-warning' style='font-size: 10pt;'>Sin datos.<br>No se han encontrado registros para generar este gr&aacute;fico.</div>");
        return false;
    }

    function tooltip(params){

        //Validate format type
        if(params.data.formatterType != undefined){

            var formatterType = params.data.formatterType;

            //Validate function format
            if(typeof formatterType == "function"){
                return formatterType(params.data);
            }

            //Validate percentage format
            if(formatterType == "percentage"){
                return params.name + ":<br> * " + params.value + "%";
            }

            //Validate none format
            if(formatterType == "none"){
                return "";
            }

        }

        //Validate normal format
        if(params.value[2] != undefined){
            return params.value[2];
        }else if (params.value[1] != undefined) {
            return params.seriesName + ' :<br/>' + params.value[0] + ' : ' + params.value[1];
        } else {
            return params.seriesName + ' :<br/>' + params.name + ' : ' + params.value;
        }
    }

    if( typeof (echarts) === 'undefined'){ return false; }

    //Create necessary variables
    var banToGoThrough = 0;
    var zIndex = 100;

    //Segment necessary data
    var legendNames = [];
    var seriesObject = [];
    var axis = {
        xAxis: [],
        yAxis: []
    };

    //Validate data to go through
    if(objectLength(data) > 1){
        var dataToGoThrough = data;
    }else{
        var dataToGoThrough = data[0]["data"];
        banToGoThrough = 1;
    }

    $.each(dataToGoThrough, function(index, value){
        
        //Get legend names
        if(legend){
            if(banToGoThrough == 0){
                if(index != "withoutAxes")
                    legendNames.push(index);
            }else{
                legendNames.push(value["name"]);
            }
        }
        

        //Get series
        if(banToGoThrough == 0){

            if(index != "withoutAxes"){
                
                if(tooltipData == true){
                    //Assign tooltip
                    value["tooltip"] =  {
                        trigger: 'item',
                        formatter: function(params) {
                            return tooltip(params);
                        }
                    };
                }
                

                seriesObject.push(value);
            }else{

                //Graphics without axes
                $.each(value, function(index, value1){

                    value1["z"] = zIndex;
                    
                    if(tooltipData == true){
                        //Assign tooltip to each data
                        $.each(value1["data"], function(index, value){
                            value["tooltip"] =  {
                                trigger: 'item',
                                formatter: function(params) {
                                    return tooltip(params);
                                }
                            };
                        });
                    }

                    
                    seriesObject.push(value1);
                    zIndex++;

                });
            }

        }
    });

    //Get series
    if(banToGoThrough == 1){
        
        if(tooltipData == true){
            //Assign tooltip to each data
            $.each(data[0]["data"], function(index, value){
                
                value["tooltip"] =  {
                    trigger: 'item',
                    formatter: function(params) {
                        return tooltip(params);
                    }
                };
            });
        }
        
        seriesObject.push(data[0]);
    }

    //Validate x and y axes
    if(xLabels != "" && yLabels != ""){
        axis = {
            xAxis: [{
                type: xLabels.type,
                scale: true,
                data: xLabels.data,
                axisLabel: {
                    formatter: xLabels.formatter,
                    interval: 0,
                    rotate: 30
                },
                splitLine: {
                    show: true
                }
            }],
            yAxis: [{
                type: yLabels.type,
                scale: true,
                data: yLabels.data,
                axisLabel: {
                    formatter: yLabels.formatter
                },
                splitLine: {
                    show: true
                }
            }]
        }
    }

    //echart Scatter
    if ( container.length ){ 
        
        var echartScatter = echarts.init(container[0]);

        echartScatter.setOption({
            title: {
                text: "",
                subtext: ""
            },
            tooltip: {
                trigger: 'axis',
                showDelay: 0,
                axisPointer: {
                    type: 'cross',
                    lineStyle: {
                        type: 'dashed',
                        width: 1
                    }
                }
            },
            grid: grid,
            legend: {
                data: legendNames,
                left: 'left',
                itemWidth: 30,
                itemHeight: 30,
                padding: 15
            },
            toolbox: {
                show: true,
                feature: {
                    saveAsImage: {
                        show: true,
                        title: "Guardar Imagen"
                    }
                }
            },
            xAxis: axis["xAxis"],
            yAxis: axis["yAxis"],
            series: seriesObject
        });

    }

    return true;
}

function createDialogTable(data, table, name){

    //Create titles
    if($("thead", table).children().length == 0){
        var tr = $("<tr>");
        $.each(data["titles"], function(index, value){

            if(value != "color"){
                var th = $("<th>");
                th.html(value);

                tr.append(th);
            }
            
        });
        $("thead", table).append(tr);
    }

    //Validate created tables
    if(createdTables.find(function(element){ return element == name; })){
        var dataTableDialog = table.DataTable();
    }else{
        var dataTableDialog = table.DataTable({
            language: formatlanguage(),
            "initComplete": function () {

                //Search inputs dataTable
                $('thead th', table).each( function ()
                {
                    var title = $(this).text();
                    if($('input', this).length == 0)
                    {
                        $(this).html( '<input type="text" placeholder=" '+title+'" />' );
                        $("input", this).on("click", function(){
                            return false;
                        });
                    }
                } );

                //Create search event
                dataTableDialog.columns().every( function(){
                    var that = this;
                
                    $('input', this.header()).on('keyup change', function(){
                        if(that.search() !== this.value){
                            that
                                .search(this.value)
                                .draw();
                        }
                    });
                });
            }
        });
        createdTables.push(name);
    }

    //Clean table
    dataTableDialog.rows().remove();

    

    //Create rows
    $.each(data["rows"], function(index, value){

        var tr = $("<tr>");
        var color = "btn-warning";

        //Go through fields
        $.each(value, function(index1, value1){

            //Create necessary variables
            color = "btn-" + value["color"];
            if(index1 == "color"){

                color = "btn-" + value1;

            }else if(index1 == "Cumpli&oacute;"){

                //Create cell
                var td = $("<td>");

                //Create span
                var span = $("<span>").addClass("btn " + color).html(value1);

                //Assign span
                td.append(span);

            }else if(index1 == "Fecha y Hora Programada"){

                //Create cell
                var td = $("<td>");

                //Create span
                var span = $("<span>").addClass("btn " + color).html(value1);

                //Assign span
                td.append(span);

            }else{

                //Create cell
                var td = $("<td>");

                //Assign value
                td.html(value1);
            }

            //Assign cell to row
            if(td != undefined)
                tr.append(td);

        });

        dataTableDialog.row.add(tr).draw( false );
    });
}

function createViewSiteProgress(data, container, name, type, structure){

    //Validate empty data
    if(objectLength(data) == 0){
        $(container).html("<div class='alert alert-warning' style='font-size: 10pt;'>Sin datos.<br>No se han encontrado registros para generar este gr&aacute;fico.</div>");
        return false;
    }

    //Create necessary functinons
    function createViewDependingOnType(name, localData){

        //Create elements
        var divContainer = $("<div>").addClass("principalContainer row").css({"line-height": "21.5px"});
        var divName = $("<div>").addClass("col-md-5 col-sm-5 col-xs-5 name").html(name).attr("title", name).css({"padding-left": "3px"});
        var divSpacing = $("<div>").addClass("col-md-1 col-sm-1 col-xs-1").css({"padding": 0});
        var divProgress =  $("<div>").addClass("progress progress_sm").addClass("col-md-6 col-sm-6 col-xs-6").css({"padding": 0});

        //Validate type
        if(type == "percentage"){

            //Create elements
            divProgress.attr("title", name + ": " + localData["Percentage"].toFixed(2) + "%\nCantidad: " + localData["Quantity"]);
            var subDiv = $("<div>").css({"background": randomColor("grandRange")}).
                            addClass("progress-bar").
                            attr("role", "progressbar").
                            attr("data-transitiongoal", localData["Percentage"]).
                            html(localData["Percentage"].toFixed(2) + "%");
            
            
            //Assign elements
            divContainer.append(divName);
            divContainer.append(divSpacing);
            divContainer.append(divProgress.append(subDiv));
            container.append(divContainer);
        } else if(type == "comparativePercentage"){

            //Calculate total data
            var total = 0;
            $.each(localData, function(index, value){
    
                total += value;
    
            });

            divName.attr("title", name + "\nCantidad: " + total);


            $.each(localData, function(index, value){

                //Calculate percentage
                var percentage = ((value * 100) / total).toFixed(0);

                if(percentage != 0){
                    //Create interface
                    var subDiv = $("<div>").
                    addClass("progress-bar btn-success").
                    addClass(structure["addClass"][index]).
                    attr("role", "progressbar").
                    attr("data-transitiongoal", percentage).
                    html(percentage + "%").attr("title", index + ": " + percentage + "% \nCantidad: " + value);

                    divProgress.append(subDiv);
                }
                
            });

            
            //Assign elements
            divContainer.append(divName);
            divContainer.append(divSpacing);
            divContainer.append(divProgress);
            container.append(divContainer);

        }

        return divContainer;
        
    }
    function iteration(array, elements){

        //Create necessary variables
        var count = 0;

        //Go through data
        $.each(array, function(index, value){

            //Create local data to validate
            var localData = {};
            $.each(value, function(index, value){
                
                if(index != "subElements"){
                    localData[index] = value;
                }
            });

            //Valdiate data
            if(!compareObjects(structure["model"], localData)){
                personalizedAlert("danger", "Ha ocurrido un error.", "Ha ocurrido un error con la informacin solicitada y no se ha podido generar el Dashboard", true, container);
                return false;
            }
            

            //Create element
            elements[count] = createViewDependingOnType(index, localData);

            //Validate new iteration
            if(value["subElements"] != undefined){

                 //Create view site progress
                elements[count]["subData"] = {};
                iteration(value["subElements"], elements[count]["subData"]);
            }

            count++;

        });
    }
    function validateClassToShow(elements, classCollapse, firstClassCollapse, countValidate){

        //Create necessary variables
        var count = 0;
        var classChilds = "";

        //Go through elements
        $.each(elements, function(index, value){

            //Validate new subsegment
            if(objectLength(value) > 2){
                countValidate += " * ";
                classChilds = validateClassToShow(value["subData"], classCollapse + count, firstClassCollapse, countValidate);
            }

            //Assign new class and child class
            $(value).addClass(classCollapse);
            $(value).attr({"data-toggle": "collapse", "data-target": classChilds});
            if(classCollapse != firstClassCollapse){
                $(value).addClass("collapse");
                $(".name", value).html( countValidate + $(".name", value).html() );
            }else{
                $(value).css({"padding-left": "0px"});
                countValidate = " * ";
            }

            count++;
        });

        return "." + classCollapse;
    }


    //Create necessary variables
    var classCollapse = "collaps" + name + "Elements";
    var elements = {};

    //Create view site progress
    iteration(data, elements);

    //Validates collapse class
    validateClassToShow(elements, classCollapse, classCollapse, "");

    //Return
    return true;
}

function createPieGraphicFormat(size, position, data, labelType = "normal", formatterLabel){

    //Validate empty data
    if(objectLength(data) == 0){
        $(container).html("<div class='alert alert-warning' style='font-size: 10pt;'>Sin datos.<br>No se han encontrado registros para generar este gr&aacute;fico.</div>");
        return false;
    }

    var label = {};
    var labelLine = {};

    if(labelType == "normal"){
        label = {
            "textStyle": {
                "color": 'rgba(0, 0, 0, 0.7)'
            },
            "formatter": formatterLabel
        };
        labelLine = {
            "lineStyle": {
                "color": 'rgba(0, 0, 0, 0.7)'
            },
            "smooth": 0.2,
            "length": 10,
            "length2": 20
        };
    }else if(labelType == "centerShow"){
        label = {
            "show": true,
            "position": "center",
            "textStyle": {
                "fontSize": "10",
                "fontWeight": 'bold',
                "color": 'rgba(0, 0, 0, 0.7)'
            },
            "formatter": formatterLabel
        };
    }

    return {
        "type": "pie",
        "radius": size,
        "center": position,
        "label": label,
        "labelLine": labelLine,
        "data": data
    };

}

function createPieGraphicsTotal(container, data){

    //Validate empty data
    if(objectLength(data) == 0){
        $(container).html("<div class='alert alert-warning' style='font-size: 10pt;'>Sin datos.<br>No se han encontrado registros para generar este gr&aacute;fico.</div>");
        return false;
    }

    var labelBottom = {
        normal : {
            color: '#ccc',
            label : {
                show : true,
                position : 'center'
            },
            labelLine : {
                show : false
            }
        },
        emphasis: {
            color: 'rgba(0,0,0,0)'
        }
    };

    //Create data
    var pieData = [];
    var count = 0;
    var total = objectLength(data);

    //Calcular posici&oacuten
    var sizeAllGrapichs = total * 20;
    var emptySpace = 100 - sizeAllGrapichs;
    var gap = emptySpace / (total + 1);
    var first = 0;

    $.each(data, function(index, value){

        //Create necessary variables
        var color = "";

        //Get increase
        if(count == 0){
            first += gap + 10;
        }else{
            first += gap + 20;
        }

        //Validate specific color
        if(value["color"] != undefined && value["color"] != "" && value["color"] != null){
            color = value["color"];
        }

        //Format pie data
        var radius = [35, 40];
        var center = [first + "%", "50%"];
        var dataToSend = [
            {name:'', value: 100 - value["Percentage"], itemStyle: labelBottom, formatterType: "none"},
            {
                name: index,
                value: value["Percentage"],
                formatterType: function(data){
                    return data.name + ":<br> * " + data.value + "%";
                },
                itemStyle: {
                    color: color
                }
            }
        ];
        var pieData1 = createPieGraphicFormat(radius, center, dataToSend, "centerShow", index + "\n" + value["Quantity"]);

        //Add to pie array
        pieData.push(pieData1);

        //Increment variables
        count++;

    });

    //Create pie graphic
    createGraphics("", "", pieData, container);
}

/*! \fn: exportExcel
 *  \brief: redireccion exportar dentro de la misma clase
 *  \author: Luis Manrique
 *  \date modified: 
 *  \param: opcion: opcion de la clase donde se encuntra la funcion de exportar
 *  \return No devuelve valores pero retorna un nÃºmero con el valor de Y
 */
  function exportExcel(name) 
  {
    try {
      window.open("../satt_standa/despac/filterData.php?opcion=exportExcel&name="+name);
      
    } catch (e) {
      console.log("Error Fuction pintar: " + e.message + "\nLine: " + e.lineNumber);
      return false;
    }
  }