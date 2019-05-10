//Global variables
var createdTables = [];

//Functions
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
    }else{
        var row = element;
    }

    //Add data
    dashboardDialog.find("#viaje").html(row.find(".viaje").html());
    dashboardDialog.find("#placa").html(row.find(".placa").html());
    dashboardDialog.find("#noDespacho").html(row.find(".noDespacho").html());
    dashboardDialog.find("#ruta").html("<span class='primary'>Ruta</span>: <span class='secundary'>" + row.find(".origen").html() + " - " + row.find(".destino").html() + "</span>");

    //Get necessary graphic data
    $.ajax({
        url: '../satt_standa/despac/filterData.php?opcion=3',
        data: {"num_despac": row.find(".noDespacho").html()},
        type: 'get',
        dataType: 'json',
        success: function(data){

            //Grid graphic
            grid = {
                "left": 15,
                "right": 15,
                "top": 80,
            }

            if(!createGraphics(data["xAxis"], data["yAxis"], data["data"], "", $('#dashBoardTableTrans #echart_scatter'), grid)){
                console.log("Ha ocurrido un error generando el gráfico");
            }
        },
        beforeSend: function(){
            loadAjax("start")
        },
        complete: function(){
            loadAjax("end")
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error al cargar el gráfico.", $("#dashBoardTableTrans #echart_scatter"), "HTML")
        }
    });

    //Get Cargue table necessary data
    $.ajax({
        url: '../satt_standa/despac/filterData.php?opcion=4',
        data: {"num_despac": row.find(".noDespacho").html()},
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
        data: {"num_despac": row.find(".noDespacho").html()},
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

function loadAjax(type){
    if(type == "start"){
        $.blockUI({ message: '<div class="bg-primary">Espere un momento</div>' });
    }else{
        $.unblockUI();
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

    //Format error
    if (jqXHR.status === 0) {
        message = 'Sin conexión.\n Verifique su red.';
    } else if (jqXHR.status == 404) {
        message = 'Página solicitada no encontrada. [404]';
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
            
            //Necessary data
            var necessaryData = data["cod_tipdes"];

            //Calculate size
            var size = 12 / necessaryData["rowMaxQuantity"];

            //Create selectable fields
            createSelectableFields(
                necessaryData["type"],
                necessaryData["rowContainer"],
                necessaryData["elementContainer"],
                size,
                necessaryData["rowMaxQuantity"],
                necessaryData["data"],
                "cod_tipdes",
                necessaryData["name"],
                $(necessaryData["container"]));
            
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
function createSelectableFields(type, rowContainer, elementContainer, size, quantity, data, name, visualName, container){

    var cloneRowContainer = (rowContainer != null && rowContainer != "" && rowContainer != undefined) ? $("<" + rowContainer + ">") : null;
    var cloneElementContainer = (elementContainer != null && elementContainer != "" && elementContainer != undefined) ? $("<" + elementContainer + ">") : null;
    var cloneElement;
    var parentElement;

    //Select elemets to create
    switch (type) {

        case "checkbox":
            cloneElement = $("<input>").attr("type", "checkbox").attr("name", name + "[]");

            //Add Bootstrap styles
            cloneRowContainer.addClass("checkbox");
            break;

        case "radio":
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
            parentElement = $("<select>").attr("name", name)
            cloneElement = $("<option>");
            break;
    }

    //Assign value and html to elements
    if(parentElement != undefined){

        //Go through data
        for (var index = 0; index < data.length; index++) {
            
            //Clone element
            var element = cloneElement.clone();
            element.val(data[index]["id"]);
            element.html(data[index]["value"]);

            //Assign element to parent
            parentElement.append(element);
        }

        //Create visual name
        var visualNameElement = $("<b>").addClass("fieldLabel");
        visualNameElement.html(visualName);

        //Assign parent to element container
        if(cloneElementContainer != null){

            //Assign container size
            cloneElementContainer.addClass("col-sm-" + size);

            cloneElementContainer.append(parentElement);
        }else{
            
            cloneElementContainer = parentElement;

            //Assign container size
            cloneElementContainer.addClass("col-sm-" + size);
        }

        //Assign container elements
        if(cloneRowContainer != null){

            //Assign container size
            cloneRowContainer.addClass("col-sm-12");
            
            cloneRowContainer.append(cloneElementContainer);
        }else{
            cloneRowContainer = cloneElementContainer;

            //Assign container size
            cloneRowContainer.addClass("col-sm-12");
        }

        //Assign container row to container
        container.append(cloneRowContainer);

        //Assing visual name
        if(type == "checkbox" || type == "radio")
            visualNameElement.insertAfter(parentElement);
        else
            visualNameElement.insertBefore(parentElement);

    }else{

        //Create necessary variables
        var banValidateRow = 0;

        //Go through data
        for (var index = 0; index < data.length; index++) {
            
            //Clone element
            var element = cloneElement.clone();
            element.val(data[index]["id"]);

            //Create visual name
            var visualNameElement = $("<b>").addClass("fieldLabel");
            visualNameElement.html(data[index]["value"]);

            //Assign element to element container
            if(cloneElementContainer != null){

                //Clone element container
                var elementContainer = cloneElementContainer.clone();

                //Assign container size
                elementContainer.addClass("col-sm-" + size);

                elementContainer.append(element);
            }else{
                var elementContainer = element;

                //Assign container size
                elementContainer.addClass("col-sm-" + size);
            }

            //Validate new row
            if((index + 1) % (quantity + 1) == 0 || index == 0){

                //Validate first row
                if(index != 0){

                    //Assign old container row to container
                    container.append(rowContainer);
                }

                //Clone row container
                var rowContainer = (cloneRowContainer != null) ? cloneRowContainer.clone() : null;
            }

            //Assign container elements
            if(rowContainer != null){

                //Assign container size
                rowContainer.addClass("col-sm-12");
                
                rowContainer.append(elementContainer);

                //Assing visual name
                if(type == "checkbox" || type == "radio")
                    visualNameElement.insertAfter(element);
                else
                    visualNameElement.insertBefore(element);

            }else{
                console.log("Es necesario un contenedor de fila");
                break;
            }
        }

        //Assign container row to container
        container.append(rowContainer);
    }
}

//Create tr
function createTr(row){

//Validate request
    //Itinerario
    var itinerario = "";
    if(row["itinerario"] == "Iniciado"){
        itinerario = "success";
    }else{
        itinerario = "danger";
    }
    
    //Reporte
    var reporte = "danger";
    if(row["reporte"] != ''){
        reporte = row["reporte"];
    }

    //Estado de Cargue
    var estadoDeCargue = "";
    if(row["estadoDeCargue"] == "A tiempo"){
        estadoDeCargue = "warning";
    }else if(row["estadoDeCargue"] == "Adelantado"){
        estadoDeCargue = "success";
    }else{
        estadoDeCargue = "danger";
    }
    
    //Fecha Cargue
    var fechaDeCargue = "danger";
    if(row["colorCargue"] != ''){
        fechaDeCargue = row["colorCargue"];
    }

    //Estado de Descargue
    var estadoDeDescargue = "";
    if(row["estadoDeDescargue"] == "A tiempo"){
        estadoDeDescargue = "warning";
    }else if(row["estadoDeDescargue"] == "Adelantado"){
        estadoDeDescargue = "success";
    }else{
        estadoDeDescargue = "danger";
    }

    //Fecha Descargue
    var fechaDeDescargue = "danger";
    if(row["colorDescargue"] != ''){
        fechaDeDescargue = row["colorDescargue"];
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

//Create Elements
    //Tr
    var tr = $("<tr>");

    //Td
    var tdNoDespacho = $("<td>").addClass('openDashBoardDialog').addClass('noDespacho').addClass("loadDashboars").css({"white-space": "nowrap"}).html(row["noDespacho"]);
    var tdItinerario = $("<td>").addClass('itinerario');
    var tdReporte = $("<td>").addClass('reporte');
    var tdEtapa = $("<td>").addClass('etapa');
    var tdPlaca = $("<td>").addClass('placa').css({"white-space": "nowrap"}).html(row["placa"]);
    var tdViaje = $("<td>").addClass('openDashBoardDialog').addClass('viaje').css({"white-space": "nowrap"}).html(row["viaje"]);
    var tdOrigen = $("<td>").addClass('origen').css({"white-space": "nowrap"}).html(row["origen"]);
    var tdDestino = $("<td>").addClass('destino').css({"white-space": "nowrap"}).html(row["destino"]);
    var tdEstadoDeCargue = $("<td>").addClass('estadoDeCargue');
    var tdFechaDeCargue = $("<td>").addClass('fechaDeCargue');
    var tdEstadoDeDescargue = $("<td>").addClass('estadoDeDescargue');
    var tdFechaDeDescargue = $("<td>").addClass('fechaDeDescargue');
    var tdProcesoDeEntrega = $("<td>").addClass('procesoDeEntrega');
    var tdLocalizacion = $("<td>").addClass('tdLocalizacion').html(row["localizacion"]).css({"text-align": "initial"});
    var tdCumplimientoDePlanDeRuta = $("<td>").addClass('cumplimientoDePlanDeRuta').addClass("project_progress");
    var tdConductor = $("<td>").addClass('conductor').css({"white-space": "nowrap"}).html(row["conductor"]);
    var tdNoConductor = $("<td>").addClass('noConductor').css({"white-space": "nowrap"}).html(row["noConductor"]);
    var tdPoseedor = $("<td>").addClass('poseedor').css({"white-space": "nowrap"}).html(row["poseedor"]);

    //I
    var iReporte = $("<i>").addClass("glyphicon glyphicon-off btn-" + reporte);

    //B
    var bEtapa = $("<b>").html(row["etapa"]);

    //Buttons
    var spanItinerario = $("<span>").addClass("btn btn-" + itinerario + " btn-xs").html(row["itinerario"]);
    var spanEstadoDeCargue = $("<span>").addClass("btn btn-" + estadoDeCargue).html(row["estadoDeCargue"]);
    var spanFechaDeCargue = $("<span>").addClass("btn btn-" + fechaDeCargue).html(row["fechaDeCargue"]);
    var spanEstadoDeDescargue = $("<span>").addClass("btn btn-" + estadoDeDescargue).html(row["estadoDeDescargue"]);
    var spanFechaDeDescargue = $("<span>").addClass("btn btn-" + fechaDeDescargue).html(row["fechaDeDescargue"]);

    //Div
    var divProcesoDeEntrega1 = $("<div>").addClass("verticalTable");
    var divProcesoDeEntrega11 = $("<div>").addClass("btn-warning").html("T");
    var divProcesoDeEntrega12 = $("<div>").html(row["procesoDeEntrega"]["t"]);
    var divProcesoDeEntrega13 = $("<div>").addClass("btn-success").html("D");
    var divProcesoDeEntrega14 = $("<div>").html(row["procesoDeEntrega"]["d"]);
    var divProcesoDeEntrega15 = $("<div>").addClass("btn-danger").html("P");
    var divProcesoDeEntrega16 = $("<div>").html(row["procesoDeEntrega"]["p"]);
    var divProcesoDeEntrega17 = $("<div>").addClass("btn-success").html("C");
    var divProcesoDeEntrega18 = $("<div>").html(row["procesoDeEntrega"]["c"]);
    var divProcesoDeEntrega19 = $("<div>").addClass("btn-danger").html("N");
    var divProcesoDeEntrega20 = $("<div>").html(row["procesoDeEntrega"]["n"]);
    var divCumplimientoDePlanDeRuta1 = $("<div>").addClass("progress progress_sm");
    var divCumplimientoDePlanDeRuta11 = $("<div>").
                                            addClass("progress-bar btn-" + cumplimientoDePlanDeRuta).
                                            attr("role", "progressbar").
                                            attr("data-transitiongoal", row["cumplimientoDePlanDeRuta"]).
                                            html(row["cumplimientoDePlanDeRuta"] + "%").attr("title", row["cumplimientoDePlanDeRuta"] + "%");

//Assign elements
    //Itinerario
    tdItinerario.append(spanItinerario);

    //Reporte
    tdReporte.append(iReporte);

    //Etapa
    tdEtapa.append(bEtapa);

    //Estado de Cargue
    tdEstadoDeCargue.append(spanEstadoDeCargue);

    //Fecha Cargue
    tdFechaDeCargue.append(spanFechaDeCargue);

    //Estado de Descargue
    tdEstadoDeDescargue.append(spanEstadoDeDescargue);

    //Fecha Descargue
    tdFechaDeDescargue.append(spanFechaDeDescargue);

    //Proceso de entrega
    divProcesoDeEntrega1.append(divProcesoDeEntrega11);
    divProcesoDeEntrega1.append(divProcesoDeEntrega12);
    divProcesoDeEntrega1.append(divProcesoDeEntrega13);
    divProcesoDeEntrega1.append(divProcesoDeEntrega14);
    divProcesoDeEntrega1.append(divProcesoDeEntrega15);
    divProcesoDeEntrega1.append(divProcesoDeEntrega16);
    divProcesoDeEntrega1.append(divProcesoDeEntrega17);
    divProcesoDeEntrega1.append(divProcesoDeEntrega18);
    divProcesoDeEntrega1.append(divProcesoDeEntrega19);
    divProcesoDeEntrega1.append(divProcesoDeEntrega20);
    tdProcesoDeEntrega.append(divProcesoDeEntrega1);

    //Cumplimiento de Plan de Ruta
    divCumplimientoDePlanDeRuta1.append(divCumplimientoDePlanDeRuta11);
    tdCumplimientoDePlanDeRuta.append(divCumplimientoDePlanDeRuta1);

    //Tr
    tr.append(tdNoDespacho);
    tr.append(tdItinerario);
    tr.append(tdReporte);
    tr.append(tdEtapa);
    tr.append(tdPlaca);
    tr.append(tdViaje);
    tr.append(tdOrigen);
    tr.append(tdDestino);
    tr.append(tdEstadoDeCargue);
    tr.append(tdFechaDeCargue);
    tr.append(tdEstadoDeDescargue);
    tr.append(tdFechaDeDescargue);
    tr.append(tdProcesoDeEntrega);
    tr.append(tdLocalizacion);
    tr.append(tdCumplimientoDePlanDeRuta);
    tr.append(tdConductor);
    tr.append(tdNoConductor);
    tr.append(tdPoseedor);

    return tr;
}

function executeFilter(){

    //Get data
    $.ajax({
        url: '../satt_standa/despac/filterData.php?opcion=1',
        dataType: 'json',
        async: false,
        type: "post",
        data: $("#filter").serialize(),
        beforeSend: function(){
            loadAjax("start");
        },
        success: function(data){

            //Validate empty
            if(objectLength(data) == 0){
                personalizedAlert("danger", "No se encontraron registros.", "No se ha encontrado ningún registro, con el filtro especificado, por favor valídelo.", true, $("#dashBoardTableTrans"));
            }

            //Clean table body
            table.clear();
            counter = 1;

            //Go through json data
            for(var i=0; i < data.length; i++){

                table.row.add(createTr(data[i])).draw( false );
            }

            // Progressbar
            if ($(".progress .progress-bar")[0]) {
                $('.progress .progress-bar').progressbar();
            }

            //Add table controllers event
            $("#filter #dataTable_DashBoard").on("DOMSubtreeModified", function(){

                // Progressbar
                if ($(".progress .progress-bar")[0]) {
                    $('.progress .progress-bar').progressbar();
                }

                //Add event dasbhboard dialog
                $(".openDashBoardDialog").unbind("click");
                $(".openDashBoardDialog").on("click", function(){
                    
                    dashboardDialogEvent(this);
                });
            });

            //Add event dasbhboard dialog
            $(".openDashBoardDialog").on("click", function(){
                
                dashboardDialogEvent(this);
            });
        },
        complete: function(){

            loadAjax("end");
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error al cargar la tabla.", $("#filter #dataTable_DashBoard tbody"), "table")
        }
    });
}

//GRAPHICS FUNCTION
function createGraphics(xLabels, yLabels, data, formatter, idGraphic, grid = {}) {

    function tooltip(params){

        //Validate format type
        if(params.data.formatterType != undefined){

            var formatterType = params.data.formatterType;

            //Validate percentage format
            if(formatterType == "percentage"){
                return params.name + ":<br> * " + params.value + "%";
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

    //Validate formatter
    if(formatter == ""){
        formatter = "{b0}: {c0}";
    }

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
        if(banToGoThrough == 0){
            if(index != "withoutAxes")
                legendNames.push(index);
        }else{
            legendNames.push(value["name"]);
        }

        //Get series
        if(banToGoThrough == 0){

            if(index != "withoutAxes"){
                
                //Assign tooltip
                value["tooltip"] =  {
                    trigger: 'item',
                    formatter: function(params) {
                        return tooltip(params);
                    }
                };

                seriesObject.push(value);
            }else{

                //Graphics without axes
                $.each(value, function(index, value1){

                    value1["z"] = zIndex;
                    
                    //Assign tooltip to each data
                    $.each(value1["data"], function(index, value){
                        value["tooltip"] =  {
                            trigger: 'item',
                            formatter: function(params) {
                                return tooltip(params);
                            }
                        };
                    });

                    
                    seriesObject.push(value1);
                    zIndex++;

                });
            }

        }
    });

    //Get series
    if(banToGoThrough == 1){
        
        //Assign tooltip to each data
        $.each(data[0]["data"], function(index, value){
            
            value["tooltip"] =  {
                trigger: 'item',
                formatter: function(params) {
                    return tooltip(params);
                }
            };
        });
        
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
    if ( idGraphic.length ){ 
        
        var echartScatter = echarts.init(idGraphic[0]);

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
        var dataTableDialog = table.DataTable({language: formatlanguage()});
        createdTables.push(name);
    }

    //Clean table
    dataTableDialog.clear();

    //Create rows
    $.each(data["rows"], function(index, value){

        var tr = $("<tr>");
        var color = "btn-warning";

        //Go through fields
        $.each(value, function(index1, value1){

            //Create necessary variables
            if(index1 == "color"){

                color = "btn-" + value1;

            }else if(index1 == "Cumplió"){

                //Create cell
                var td = $("<td>").addClass("centerContent");

                //Create div clone
                var div = $("<div>");

                //Create icons
                var iAdelantado = $("<i>").addClass("glyphicon glyphicon-ok btn-disabled");
                var iATiempo = $("<i>").addClass("glyphicon glyphicon-ok btn-disabled");
                var iAtrasado = $("<i>").addClass("glyphicon glyphicon-ok btn-disabled");

                //Validate active
                if(value1 == "Adelantado"){
                    iAdelantado.removeClass("btn-disabled");
                    iAdelantado.addClass("btn-success");
                }else if(value1 == "A tiempo"){
                    iATiempo.removeClass("btn-disabled");
                    iATiempo.addClass("btn-warning");
                }else if(value1 == "Atrasado"){
                    iAtrasado.removeClass("btn-disabled");
                    iAtrasado.addClass("btn-danger");
                }

                //Assign icons
                var divContainer = div.clone().css({"display": "flex", "flex-flow": "row nowrap", "justify-content": "space-around"});
                divContainer.append(div.clone().append(iAdelantado));
                divContainer.append(div.clone().append(iATiempo));
                divContainer.append(div.clone().append(iAtrasado));
                td.append(divContainer);

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

    //Create necessary functinons
    function createViewDependingOnType(name, localData){

        //Create elements
        var divContainer = $("<div>").addClass("principalContainer row").css({"line-height": "21.5px"});
        var divName = $("<div>").addClass("col-md-5 col-sm-5 col-xs-5 name").html(name).attr("title", name);
        var divSpacing = $("<div>").addClass("col-md-1 col-sm-1 col-xs-1");
        var divProgress =  $("<div>").addClass("progress progress_sm").addClass("col-md-6 col-sm-6 col-xs-6").css({"padding": 0});

        //Validate type
        if(type == "percentage"){

            //Create elements
            var subDiv = $("<div>").
                            addClass("progress-bar").
                            attr("role", "progressbar").
                            attr("data-transitiongoal", localData["percentage"]).
                            html(localData["percentage"] + "%").attr("title", localData["percentage"] + "%");
            
            
            //Assign elements
            divContainer.append(divName);
            divContainer.append(divSpacing);
            divContainer.append(divProgress.append(subDiv));
            container.append(divContainer);
        } else if(type == "comparativePercentage"){

            //Calculate total and quantity data
            var quantity = 0;
            var total = 0;
            $.each(localData, function(index, value){

                quantity++;
                total += value;

            });


            $.each(localData, function(index, value){

                //Calculate percentage
                var percentage = ((value * 100) / total).toFixed(0);

                //Create interface
                var subDiv = $("<div>").
                    addClass("progress-bar btn-success").
                    addClass(structure["addClass"][index]).
                    attr("role", "progressbar").
                    attr("data-transitiongoal", percentage).
                    html(percentage + "%").attr("title", index + ": " + percentage + "%");

                divProgress.append(subDiv);
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
                personalizedAlert("danger", "Ha ocurrido un error.", "Ha ocurrido un error con la información solicitada y no se ha podido generar el Dashboard", true, container);
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
                countValidate++;
                classChilds = validateClassToShow(value["subData"], classCollapse + count, firstClassCollapse, countValidate);
            }

            //Assign new class and child class
            $(value).addClass(classCollapse);
            $(value).attr({"data-toggle": "collapse", "data-target": classChilds});
            if(classCollapse != firstClassCollapse){
                $(value).addClass("collapse");
                $(value).css({"padding-left": (countValidate * 5) + "px"});
            }else{
                $(value).css({"padding-left": "0px"});
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
    validateClassToShow(elements, classCollapse, classCollapse, 1);
}