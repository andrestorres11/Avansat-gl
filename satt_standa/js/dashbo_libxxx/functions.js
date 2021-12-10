class Graphics
{

    constructor(container, name, series, additionalParams = null)
    {
        this.container = container;
        this.name = name;
        this.series = series;
        this.additionalParams = additionalParams;
        this.graphic = {};
        this.eChart;
        this.timeOut;
    }

    createGraphic()
    {
        //Validate Errors
        if(this.validateErrors()){

            //Validate type of instance
            this.validateInstanceType();

            //Clean container
            $(this.container).removeAttr("_echarts_instance_");

            //Initial styles
            let height = "350px";
            if($(this.container).height() != 0 && $(this.container).height() > 100){
                height = $(this.container).outerHeight( true );
            }

            $(this.container).css({
                "height": height,
                "min-width": "300px"
            });

            //Create option code
            this.formatCode();

            //Ejecute eChart lib
            this.eChart = echarts.init(this.container);

            //Draw graphic
            this.eChart.setOption(this.graphic);

            //Assign resposive attributes
            this.assignResponsiveAttributes();
        }
        
    }

    formatCode()
    {
        //Create default params

            //Legend
            this.graphic["legend"] = {
                data: this.getLegend(),
                bottom: 0
            }
            
            //X axis
            this.graphic["xAxis"] = {
                "type": "category",
                boundaryGap: false,
            };

            //Y axis
            this.graphic["yAxis"] = {
                "type": "value"
            };

            //Tooltip
            this.graphic["tooltip"] = {
                trigger: 'axis',
                backgroundColor: "#000a",
                textStyle: {
                    color: "#ccc"
                },
                showDelay: 0,
                axisPointer: {
                    type: 'cross',
                    lineStyle: {
                        type: 'dashed',
                        width: 1
                    }
                }
            }

            //Menu options
            this.graphic["toolbox"] = {
                feature: {
                    saveAsImage: {
                        show: true,
                        title: "Guardar Imagen."
                    }
                }
            };

            //Graphic position
            this.graphic["grid"] = {
                top: "25",
                left: "60",
                right: "3%",
                bottom: "70"
            }

            //Graphic serie
            this.graphic["series"] = this.series;

        //Validate Series
        this.validateSeries();

        //Validate additional parameters
        if(this.additionalParams != null)
        {
            this.validateAdditionalParams();
        }
    }

    validateErrors()
    {

        if(this.container == null || this.container.length == 0)
        {
            personalizedAlert(
                "danger",
                "Error en gr&aacute;fico '" + this.name + "'",
                "El contenedor seleccionado se encuentra vac&iacute;o o no se encuentra, no se puede crear el gr&aacute;fico",
                true,
                $("body"));

                return false;
        }
        
        if(this.series == "" || this.series == undefined || this.series == [])
        {
            personalizedAlert(
                "danger",
                "Error en gr&aacute;fico '" + this.name + "'",
                "Sin datos, no se puede crear el gr&aacute;fico",
                true,
                $("body"));

                this.container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gr&aacute;fico</div>
                `);

                return false;
        }

        return true;
    }

    validateInstanceType()
    {
        if(this.container[0] != undefined){
            this.container = this.container[0];
        }
    }

    assignResponsiveAttributes()
    {
        
        //Create resize event
        $(window).on('resize', () => {
            if(this.eChart != null && this.eChart != undefined){
                this.eChart.resize();
            }
        });
    }

    validateAdditionalParams()
    {
        for(let param in this.additionalParams)
        {
            if(this.graphic[param] == undefined)
            {
                this.graphic[param] = this.additionalParams[param];
            }
            else if(this.additionalParams[param] == "")
            {
                this.graphic[param] = "";
            }
            else
            {
                $.extend(this.graphic[param], this.additionalParams[param]);
            }
        }
    }

    getLegend()
    {
        //Create necessary variables
        let result = [];

        for(let serie in this.series)
        {
            if(this.series[serie].name != undefined)
            {
                if($.inArray(this.series[serie].name, result) == -1)
                {
                    result.push(this.series[serie].name);
                }
            }else if(this.series[serie].type == 'pie')
            {
                for(let data in this.series[serie].data)
                {
                    if($.inArray(this.series[serie].data[data].name, result) == -1)
                    {
                        result.push(this.series[serie].data[data].name);
                    }
                }
            }
        }

        return result;
    }

    validateSeries()
    {
        //Go through all series
        for(let serie in this.series)
        {
            //Validate serie type and set default values
                let defaultValue = {};
                switch (this.series[serie].type)
                {
                    case "pie":
                        
                        //Create default style
                        this.graphic.legend["bottom"] = "";
                        this.graphic.legend["top"] = "15";
                        defaultValue = {
                            radius: "50%",
                            center: ["50%", "60%"],
                            label: {
                                normal: {
                                    formatter: "{a|{b}}{abg|}\n{hr|}\n{c|{c}}{s|}{d|{d}%}{per|}",
                                    rich: {
                                        a: {
                                            color: '#999',
                                            lineHeight: 22,
                                            padding: [2, 7],
                                            background: '#ccc',
                                            align: 'center'
                                        },
                                        abg: {
                                            backgroundColor: '#333',
                                            align: 'right',
                                            width: '100%',
                                            height: 22,
                                            borderRadius: [4, 4, 0, 0]
                                        },
                                        hr: {
                                            borderColor: '#aaa',
                                            width: '100%',
                                            borderWidth: 0.5,
                                            height: 0
                                        },
                                        c: {
                                            color: '#fff',
                                            lineHeight: 22,
                                            padding: [2, 7],
                                            background: '#ccc',
                                            align: 'center'
                                        },
                                        s:{
                                            
                                        },
                                        d: {
                                            color: '#fff',
                                            lineHeight: 22,
                                            padding: [2, 7],
                                            background: '#ccc',
                                            align: 'center'
                                        },
                                        per: {
                                            color: '#eee',
                                            backgroundColor: '#334455',
                                            width: '100%',
                                            align: 'right',
                                            padding: [2, 4],
                                            borderRadius: 2,
                                            borderRadius: [0, 0, 4, 0]
                                        }
                                    }
                                }
                            },
                            labelLine: {
                                "lineStyle": {
                                    "color": 'rgba(0, 0, 0, 0.7)'
                                },
                                "smooth": 0.2,
                                "length": 10,
                                "length2": 20
                            }
                        };

                        this.graphic["xAxis"] = "";
                        this.graphic["yAxis"] = "";

                        break;

                    case "line":
                        break;
                
                    default:
                        break;
                }

                //Merge series array with default array
                $.extend(defaultValue, this.series[serie]);

                //Update series array
                this.series[serie] = defaultValue;

            
            //Validate data type
                let quantityShow = "{c}";
                let dataShow = "{d}";
                if(this.series[serie].dataType != undefined)
                {
                    switch (this.series[serie].dataType) {
                        case "percentage":
                            dataShow = "{d} %";
                            break;
                    
                        default:
                            break;
                    }
                }
            
            //Validate existence of tooltip type
                if(this.series[serie].tooltipType != undefined)
                {
                    
                    //Validate type tooltip and create them
                    let defaultTooltip = {};
                    switch (this.series[serie].tooltipType)
                    {
                        case "normal":
                            defaultTooltip = {
                                trigger: 'item',
                                formatter: "{b}:<br/>*" + quantityShow
                            }
                            break;

                        case "stylized":
                            defaultTooltip = {
                                trigger: 'item',
                                padding: 0,
                                formatter: `
                                    <div class='stylized'>
                                        <div class='head'>{b}</div>
                                        <div class='body'>
                                            <div class='first'>` + quantityShow + `</div>
                                            <div class='second'>` + dataShow + `</div>
                                        </div>
                                    </div>   
                                `
                            }
                            break;
                        
                        case "function":
                            defaultTooltip = {
                                trigger: 'item',
                                formatter: this.series[serie].tooltipValue
                            }
                            break;
                        default:
                            break;
                    }

                    //Merge tooltip array with default array
                    $.extend(this.graphic["tooltip"], defaultTooltip);
                }
        }
    }



    createDonutGraphicFormat(size, position, data, labelType = "normal", formatterLabel){

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
            "data": data,
            "tooltipType": "function",
            "tooltipValue": function(params){
                if(params.name != "")
                    return params.name + "<br> * " + params.value + "%";
            }
        };

    }

    createMultipleDonutGraphics(){

        //Validate Errors
        if(this.validateErrors()){

            //Validate type of instance
            this.validateInstanceType();

            $(this.container).css({
                "height": "180px",
                "min-width": "300px"
            });

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
            var total = objectLength(this.series);

            //Calcular posición
            var sizeAllGrapichs = total * 20;
            var emptySpace = 100 - sizeAllGrapichs;
            var gap = emptySpace / (total + 1);
            var first = 0;

            $.each(this.series, (index, value) => {

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
                var radius = [40, 50];
                var center = [first + "%", "50%"];
                var dataToSend = [
                    {name:'', value: 100 - value["Percentage"], itemStyle: labelBottom, formatterType: "none"},
                    {
                        name: index,
                        value: value["Percentage"],
                        itemStyle: {
                            color: color
                        }
                    }
                ];
                var pieData1 = this.createDonutGraphicFormat(radius, center, dataToSend, "centerShow", index + "\n" + value["Quantity"]);

                //Add to pie array
                pieData.push(pieData1);

                //Increment variables
                count++;

            });

            //Update series
            this.series = pieData;

            //Create pie graphic
            this.createGraphic();
        }
    }
}