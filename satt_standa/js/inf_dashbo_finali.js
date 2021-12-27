function generateGraphics(fec_inicio = null, fec_finxxx = null){

    //Get "Despachos realizados por tipo de operaci�n" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_finali.php?opcion=1',
        type: 'post',
        data: {fec_inicio, fec_finxxx},
        dataType: 'json',
        beforeSend: function(){
            loadAjax("start");
        },
        success: function(data)
        {
            //Instance graphic container
            let container = $("#desReaPorTipOpeGraphic");

            //Validate empty data
            if(objectLength(data) == 0){
                container.html(` 
                    <div class='btn-danger'>Sin datos, no se puede crear el gr&aacute;fico</div>
                `);
                return;
            }else{
                container.html("");
            }

            //Create total
            let total = {};
            total["type"] = "line";
            total["name"] = "Total";
            total["symbol"] = "none";
            total["data"] = [];
            total["lineStyle"] = {
                opacity: 0
            }
            totalData = {}

            //Add necessary series data
            for(let serie in data)
            {
                let color = randomColor("pastel");
                data[serie]["type"] = "line";
                data[serie]["symbol"] = "none";
                data[serie]["stack"] = "a";
                data[serie]["itemStyle"] = {
                    color: color
                };
                data[serie]["areaStyle"] = {
                    color: color
                };

                //Fill total data
                for(let subData in data[serie].data)
                {
                    //Validate exist of data
                    if(totalData[data[serie].data[subData][0]] == undefined){
                        totalData[data[serie].data[subData][0]] = 0;
                    }

                    totalData[data[serie].data[subData][0]] += Number(data[serie].data[subData][1]);
                }
            }

            console.log(totalData);

            //Reformat total data
            $.each(totalData, (key, value) => 
            {
                total["data"].push([key, value]);
            })

            //Assign total data to graphic data
            data.push(total);

            //Create "Despachos realizados por tipo de operaci�n" graphic
            grapich = new Graphics(
                container,
                "Despachos realizados por tipo de operaci&oacute;n",
                data,
                {
                    dataZoom: [{
                        type: 'inside',
                        throttle: 50
                    }]
                }
            );
            grapich.createGraphic();
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error en la petici&oacute;n", $("#desReaPorTipOpeGraphic"), "HTML");
        },
        complete: function(){
            loadAjax("finished");
        }
    })




    //Get "Despachos finalizados" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_finali.php?opcion=2',
        type: 'post',
        data: {fec_inicio, fec_finxxx},
        dataType: 'json',
        beforeSend: function(){
            loadAjax("start");
        },
        success: function(data)
        {
            //Instance graphic container
            let container = $("#desFinGraphic");

            //Validate empty data
            if(objectLength(data) == 0){
                container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gr&aacute;fico</div>
                `);
                return;
            }else{
                container.html("");
            }

            //Create colors
            let colors = [
                "#559bda",
                "#f07725",
                "#a6a6a6",
                "#fdc51d"
            ]

            //Add necessary series data
            for(let serie in data){
                data[serie]["type"] = "pie";
                data[serie]["radius"] = "73%";
                data[serie]["label"] = "";
                data[serie]["labelLine"] = "";
                data[serie]["tooltipType"] = "stylized";
                data[serie]["dataType"] = "percentage";

                    //Assign colors
                for(let subSerie in data[serie].data){
                    data[serie].data[subSerie]["itemStyle"] = {
                        color: colors[subSerie]
                    }
                }
            }

            

            //Create "Despachos finalizados" graphic
            grapich = new Graphics(
                container,
                "Despachos finalizados",
                data,
                {
                    label: ""
                }
            );
            grapich.createGraphic();
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error en la petici&oacute;n", $("#desFinGraphic"), "HTML");
        },
        complete: function(){
            loadAjax("finished");
        }
    });

    //Get "Vehiculos Pendientes por llegada" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_finali.php?opcion=8',
        type: 'post',
        data: {fec_inicio, fec_finxxx},
        dataType: 'json',
        beforeSend: function(){
            loadAjax("start");
        },
        success: function(data)
        {
            //Instance graphic container
            let container = $("#vehiPenLlegGraphic");

            //Validate empty data
            if(objectLength(data) == 0){
                container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gr&aacute;fico</div>
                `);
                return;
            }else{
                container.html("");
            }

            //Create colors
            let colors = [
                "#559bda",
                "#f07725",
                "#a6a6a6",
                "#fdc51d"
            ]

            //Add necessary series data
            for(let serie in data){
                data[serie]["type"] = "pie";
                data[serie]["radius"] = "73%";
                data[serie]["label"] = "";
                data[serie]["labelLine"] = "";
                data[serie]["tooltipType"] = "stylized";
                data[serie]["dataType"] = "percentage";

                    //Assign colors
                for(let subSerie in data[serie].data){
                    data[serie].data[subSerie]["itemStyle"] = {
                        color: colors[subSerie]
                    }
                }
            }

            

            //Create "Despachos finalizados" graphic
            grapich = new Graphics(
                container,
                "Vehiculos Pendientes por llegada",
                data,
                {
                    label: ""
                }
            );
            grapich.createGraphic();
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error en la petici&oacute;n", $("#vehiPenLlegGraphic"), "HTML");
        },
        complete: function(){
            loadAjax("finished");
        }
    });

    //Get "Informe de uso de veh�culo por planta" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_finali.php?opcion=3',
        type: 'post',
        data: {fec_inicio, fec_finxxx},
        dataType: 'json',
        beforeSend: function(){
            loadAjax("start");
        },
        success: function(data)
        {
            //Instance graphic container
            let container = $("#infUsoVehPorPlaFinGraphic");

            //Validate empty data
            if(objectLength(data) == 0){
                container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gr&aacute;fico</div>
                `);
                return;
            }else{
                container.html("");
            }

            //Assign stats values
            for(let stat in data["stats"]){
                $("#" + camelCaseFormatter(stat) + " .statsCount").html(data["stats"][stat]);
            }

            //Add necessary series data
            for(let serie in data["graphic"]){

                let color = randomColor("grandRange");

                data["graphic"][serie]["type"] = "line";
                data["graphic"][serie]["symbol"] = "none";
                data["graphic"][serie]["dataType"] = "percentage";
                data["graphic"][serie]["itemStyle"] = {
                    color: color
                };
            }

            //Create "Informe de uso de vehiculo por planta" graphic
            grapich = new Graphics(
                container,
                "Informe de uso de vehiculo por planta",
                data["graphic"],
                {
                    legend: {
                        top: 0,
                        bottom: ""
                    },
                    grid: {
                        top: 45
                    },
                    xAxis: {
                        data: data["xData"],
                        axisLabel: {
                            interval: 0,
                            fontSize: 10,
                            rotate: 30,
                            formatter: function(value)
                            {
                                if(value.length > 17)
                                    return value.slice(0, 17) + " ...";
                                else
                                    return value;
                                
                            }
                        },
                        boundaryGap: true
                    }
                }
            );
            grapich.createGraphic();
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error en la petici&oacute;n", $("#infUsoVehPorPlaFinGraphic"), "HTML");
        },
        complete: function(){
            loadAjax("finished");
        }
    });

    //Get "Citas de Cargue" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_finali.php?opcion=4',
        type: 'post',
        data: {fec_inicio, fec_finxxx},
        dataType: 'json',
        beforeSend: function(){
            loadAjax("start");
        },
        success: function(data)
        {
            //Instance graphic container
            let container = $("#citCarGraphic");

            //Validate empty data
            if(objectLength(data) == 0){
                container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gr&aacute;fico</div>
                `);
                return;
            }else{
                container.html("");
            }

            graphic = new Graphics(
                container,
                "Citas de Cargue",
                data,
                {
                    legend: ""
                }
            );
            
            graphic.createMultipleDonutGraphics();
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error en la petici&oacute;n", $("#citCarGraphic"), "HTML");
        },
        complete: function(){
            loadAjax("finished");
        }
    });

    //Get "Citas de Descargue" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_finali.php?opcion=5',
        type: 'post',
        data: {fec_inicio, fec_finxxx},
        dataType: 'json',
        beforeSend: function(){
            loadAjax("start");
        },
        success: function(data)
        {
            //Instance graphic container
            let container = $("#citDesGraphic");

            //Validate empty data
            if(objectLength(data) == 0){
                container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gr&aacute;fico</div>
                `);
                return;
            }else{
                container.html("");
            }

            graphic = new Graphics(
                container,
                "Citas de Descargue",
                data,
                {
                    legend: ""
                }
            );
            
            graphic.createMultipleDonutGraphics();
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error en la petici&oacute;n", $("#citDesGraphic"), "HTML");
        },
        complete: function(){
            loadAjax("finished");
        }
    });

    //Get "Itinerario" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_finali.php?opcion=6',
        type: 'post',
        data: {fec_inicio, fec_finxxx},
        dataType: 'json',
        beforeSend: function(){
            loadAjax("start");
        },
        success: function(data)
        {
            //Instance graphic container
            let container = $("#itiGraphic");

            //Validate empty data
            if(objectLength(data) == 0){
                container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gr&aacute;fico</div>
                `);
                return;
            }else{
                container.html("");
            }

            graphic = new Graphics(
                container,
                "Itinerario",
                data,
                {
                    legend: ""
                }
            );
            
            graphic.createMultipleDonutGraphics();
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error en la petici&oacute;n", $("#itiGraphic"), "HTML");
        },
        complete: function(){
            loadAjax("finished");
        }
    });



    //Get "Top de Eventos" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_finali.php?opcion=7',
        type: 'post',
        data: {fec_inicio, fec_finxxx},
        dataType: 'json',
        beforeSend: function(){
            loadAjax("start");
        },
        success: function(data)
        {
            //Instance graphic container
            let container = $("#topEveGraphic");

            //Validate empty data
            if(objectLength(data) == 0){
                container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gr&aacute;fico</div>
                `);
                return;
            }else{
                container.html("");
            }

            //Add necessary series data
            for(let serie in data){

                let color = randomColor("neutral");

                data[serie]["type"] = "line";
                data[serie]["symbol"] = "circle";
                data[serie]["symbolSize"] = "7";
                data[serie]["dataType"] = "percentage";
                data[serie]["itemStyle"] = {
                    color: color
                };
            }

            //Create "Top de Eventos" graphic
            grapich = new Graphics(
                container,
                "Top de Eventos",
                data,
                {
                    grid: {
                        bottom: 200,
                    },
                    xAxis: {
                        boundaryGap: true
                    },
                    dataZoom: [{
                        type: 'inside',
                        throttle: 50
                    }]
                }
            );
            grapich.createGraphic();
        },
        error: function(jqXHR, exception){
            errorAjax(jqXHR, exception, "Error en la petici&oacute;n", $("#topEveGraphic"), "HTML");
        },
        complete: function(){
            loadAjax("finished");
        }
    });
}

generateGraphics();

//Validation style
$("#formFilter input, #formFilter textarea, #formFilter select").on("change focus", function()
{
    if($(this)[0].validity.valid == false){
        $(this).addClass("invalid");
        $(this).removeClass("valid");
    }else{
        $(this).addClass("valid");
        $(this).removeClass("invalid");
    }
});

//Create filter event
$("#filter").on("click", function()
{
    //Get necessary data
    let fec_inicio = $("#fec_inicio").val();
    let fec_finxxx = $("#fec_finxxx").val()

    //Validate max range
    if(fec_inicio != "" && fec_finxxx != "")
    {
        //Convert dates to date object
        fec_inicio = new Date(fec_inicio).getTime();
        fec_finxxx = new Date(fec_finxxx).getTime();
        
        //Calculate difference
        let days = (fec_finxxx - fec_inicio) / (1000*60*60*24);

        if(days > 30)
        {
            personalizedAlert(
                "warning",
                "Superada cantidad de d&iacute;as seleccionables (30 d&iacute;as)",
                "Para evitar lentitud en el proceso de ejcuci&oacute;n en los Dashboard, se solicita que m&aacute;ximo se soliciten 30 d&iacute;as de informaci&oacute;n",
                true,
                $("body"));
            
            return false;
        }
    }

    generateGraphics($("#fec_inicio").val(), $("#fec_finxxx").val());
});




