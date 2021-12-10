//Load Functions
    $("#filter > .container").width($("body").width() - 35);

    $(window).resize(function(){
        $("#filter > .container").width($("body").width() - 35);
    });

    loadSelectableFields();
    executeFilter();

    //Activate filter tables
    var table = $('#filter #dataTable_DashBoard').DataTable({
        language: formatlanguage(),
        dom: 'Bflrtip',
        buttons: [
            'excelHtml5',
            'csvHtml5'
        ],
        "pageLength": 100,
        "initComplete": function (json) {

            //Search inputs dataTable
            $('#filter #dataTable_DashBoard thead th').each( function () {
                var title = $(this).text();
                if($('input', this).length == 0)
                    $(this).html( '<label style="display:none;">'+title+'</label><input type="text" style="width: 100%; min-width: 50px;" title="'+title+'" placeholder="'+title+'" />' );
                    $("input", this).on("click", function(){
                        return false;
                    });
            } );

            //Create search event
            table.columns().every( function(){
                var that = this;
            
                $('input', this.header()).on('keyup change', function(){
                    if(that.search() !== this.value){
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
        },
        "order": [[ 5, "desc" ]]
    });

//Reload old dashboard
    //Filters
    if(filterData != null)
    {
        for(let key in filterData)
        {
            if($("#" + key).length != 0){
                $("#" + key).val(filterData[key]);
            }
        }

        //Table
        //executeFilter();
    }

    /*//Table
    if(tableData != null){
        executeFilter(tableData);
    }*/

    //Load graphic
    if(despacho != null && despacho != "" && despacho != undefined)
        dashboardDialogEvent(despacho, origen, destino, placa, viaje);

//Events Functions
    //Add event form
        $("#filtrosEspecificos input").on("keyup", function(e){

            e.preventDefault();

            //Validate enter key
            if(e.key == "Enter")
            {
                executeFilter();
            }
        });
        $("#filtrosEspecificos select, #filtrosEspecificos #fec_finxxx, #filtrosEspecificos #fec_inicio").on("change", function(e){

            e.preventDefault();

            if((e.target == $("#fec_inicio")[0] || e.target == $("#fec_finxxx")[0]) && ($("#fec_inicio").val() == "" || $("#fec_finxxx").val() == "")){
                return false;
            }

            executeFilter();
        });
        $("#tipoDeDespacho, #filtrosGenerales input").on("click", function(e){
            executeFilter();
        });

       
    //Add events dialog
        //Close events
        $("#dashBoardTableTrans .dialog .closeWindow i").on("click", function(){
            $(this).parents(".dialog").css("display", "none");
        });
        $("#dashBoardTableTrans .dialog").on("click", function(e){
            
            if (e.target !== this)
                return;

            $(this).css("display", "none");
        });

        $("#dashBoardTableTrans .dashBoardDialog .descargueEvent, #dashBoardTableTrans .dashBoardDialog .cargueEvent").on("click", function(e){

            var object = $(this);
            var numDespac = $("#dashBoardTableTrans .dashBoardDialog #noDespacho a").text();

            if(object.hasClass("descargueEvent")){
                var option = 5;
                var id = "descargue";
            }else{
                var option = 4;
                var id = "cargue";
            }
            
            //Get Descargue table necessary data
            $.ajax({
                url: '../satt_standa/despac/filterData.php?opcion=' + option,
                data: {"num_despac": numDespac},
                type: 'get',
                dataType: 'json',
                success: function(data){
                    
                    if(data.rows.length > 0)
                        createDialogTable(data, $("#" + id + " table"), id + "Table");
                    else{
                        if($("#" + id + " table thead").children().length != 0){
                            $("#" + id + " table tbody").empty();
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
                    errorAjax(jqXHR, exception, "Error al cargar la tabla.", $("#descargue table tbody"), "table")
                }
            });
        });

        $("#dashBoardTableTrans #cargueDialog, #dashBoardTableTrans #descargueDialog, #dashBoardTableTrans #totalDialog").on("click", function(){

            //get necessary data
            var name = $(this).attr("id");
            var dialog = $("#dashBoardTableTrans ." + name);

            //Create necessary data
            var url = "";

            //Select data url
            switch (name) {
                case "cargueDialog":

                    //Select path
                    url = "../satt_standa/despac/filterData.php?opcion=6";

                    break;
                case "descargueDialog":

                    //Select path
                    url = "../satt_standa/despac/filterData.php?opcion=7";

                    break;
                case "totalDialog":

                    //Select path
                    url = "../satt_standa/despac/filterData.php?opcion=8";

                    break;
            
                default:
                    return false;
            }

            //Get dialog data
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                success: function(data){

                    //Stats data
                    $.each(data["stats"], function(index, value)
                    {
                        if(index == "Despachos sin ruta" && value > 0){
                            $("." + camelCaseFormat(index) + " .statsCount", dialog).html("<a href='index.php?cod_servic=1309&window=central'>" + value + "</a>");
                        }else{
                            $("." + camelCaseFormat(index) + " .statsCount", dialog).html(value);
                        }
                    });
                    
                    if(name != "totalDialog")
                    {

                        //Select type
                        var typeDataTable = "comparativePercentage";

                        //Create structure
                        var structure = {

                            "model": {
                                "Por Cumplir": 0,
                                "Cumplidas": 0,
                                "No Cumplidas": 0
                            },
                            "addClass": {
                                "Por Cumplir": "btn-warning",
                                "Cumplidas": "btn-success",
                                "No Cumplidas": "btn-danger",
                            }

                        }

                        //Format pie data
                        var radius = "65%";
                        var center = ["50%", "60%"];
                        var dataToSend = [
                            {
                                "name": "Por Cumplir",
                                "value": (data["stats"]["Por Cumplir"] * 100 / data["stats"]["Total Citas"]).toFixed(2),
                                "itemStyle": {
                                    "color": "#f7c81e"
                                },
                                "formatterType": "percentage"
                            },
                            {
                                "name": "Cumplidas",
                                "value": (data["stats"]["Cumplidas"] * 100 / data["stats"]["Total Citas"]).toFixed(2),
                                "itemStyle": {
                                    "color": "#419645"
                                },
                                "formatterType": "percentage"
                            },
                            {
                                "name": "No Cumplidas",
                                "value": (data["stats"]["No Cumplidas"] * 100 / data["stats"]["Total Citas"]).toFixed(2),
                                "itemStyle": {
                                    "color": "#b33b16"
                                },
                                "formatterType": "percentage"
                            }
                        ]

                        var pieData = [createPieGraphicFormat(radius, center, dataToSend)];

                        //Create pie graphic
                        createGraphics("", "", pieData, $(".pieGraphic", dialog));

                        //Clean containers
                        $(".siteProgressView", dialog).empty();

                        //Site progress
                        createViewSiteProgress(data["citasPorCentro"], $(".siteProgressView", dialog), "cargueProgress", typeDataTable, structure);

                    }else{

                        //Select type
                        var typeDataTable = "percentage";

                        //Create structure
                        var structure = {

                            "model": {
                                "Percentage": 0,
                                "Quantity": 0
                            }

                        }

                        //Create pie graphics
                        createPieGraphicsTotal($(".pieGraphic", dialog), data["graphics"]);

                        //Create gps pie graphics
                        createPieGraphicsTotal($(".gpsPieGraphics", dialog), data["gps"]);

                        //Clean containers
                        $(".destinosFrecuentesPercentageView", dialog).empty();
                        $(".origenesFrecuentesPercentageView", dialog).empty();
                        $(".eventosPercentageView", dialog).empty();

                        //Site progress
                        createViewSiteProgress(data["destinosFrecuentes"], $(".destinosFrecuentesPercentageView", dialog), "destinosFrecuentesPercentageView", typeDataTable, structure);
                        createViewSiteProgress(data["origenesFrecuentes"], $(".origenesFrecuentesPercentageView", dialog), "origenesFrecuentesPercentageView", typeDataTable, structure);
                        createViewSiteProgress(data["eventos"], $(".eventosPercentageView", dialog), "eventosPercentageView", typeDataTable, structure);

                    }

                    // Progressbar
                    if ($(".progress .progress-bar")[0]) {
                        $('.progress .progress-bar').progressbar();
                    }
                },
                beforeSend: function(){
                    loadAjax("start");
                },
                complete: function(){
                    loadAjax("end");
                },
                error: function(jqXHR, exception){
                    errorAjax(jqXHR, exception, "Ha ocurrido un error al generar el gr&aacute;fico.", "", "alert");
                    return false;
                }
            });
            
            //Hide others dialogs
            $("#dashBoardTableTrans .dialog").hide();

            //Show dialog
            $("#dashBoardTableTrans ." + name).show();
        });