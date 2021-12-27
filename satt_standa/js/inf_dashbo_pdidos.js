$(function() {
    //Ejecutar Funciones  
    loadSelect();
    generateGraphics();
    validationStyle("formFirstilter");
    validationStyle("formSecondFilter");
    $("#filter").on("click", function() {
        generateGraphics();
    });

    $("#filPedRem").on("click", function() {
        popUpRemPed();
    });
});



/*! \fn: loadSelect
 *  \brief: Funcion que Asignar las opciones por cada campo
 *  \author: Luis Carlos Manrique Boada
 *  \date: 2019-08-02
 *  \param:  
 */
function loadSelect() {
    //Asignar las opciones por cada campo
    $("select").each(function() {
        var select = $(this).attr("id");
        switch (select) {
            case "cliente":
                var url = '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=7';
                break;
            case "negocios":
                var url = '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=8';
                break;
            case "canal":
                var url = '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=9';
                break;
            case "tipoOperacion":
                var url = '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=10';
                break;
            case "origen":
                var url = '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=11';
                break;
            case "destino":
                var url = '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=11';
                break;
            default:
                var url = "";
                break;
        }


        //Ejecuta la opci?n dependendiendo del campo enviado
        if (url != "") {
            $.ajax({
                url: url,
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    $.each(data, function(key, value) {
                        $("#" + select).append("<option value='" + value[0] + "'>" + value[1] + "</option>");
                    });


                    //Asigna el evento multiselecci?n al campo
                    switch (select) {
                        case "cliente":
                            $("#cliente").multiselect();
                            break;
                        case "negocios":
                            $("#negocios").multiselect();
                            break;
                        case "canal":
                            $("#canal").multiselect();
                            break;
                        case "origen":
                            $("#origen").multiselect();
                            break;
                        case "destino":
                            $("#destino").multiselect();
                            break;
                        default:
                            break;
                    }
                }
            });
        }

    });
}

/*! \fn: capValMult
 *  \brief: Funci?n que captura el valor de los campos Multiselect
 *  \author: Luis Carlos Manrique Boada
 *  \date: 2019-08-05
 *  \param:  string(campo)
 */

function capValMult(campo) {
    var idCampo = "";
    $.each($("#" + campo + "_multiSelect .multiselect-checkbox"), function() {
        if ($(this).is(':checked')) {
            if ($(this).attr("data-val") != -1) {
                if (idCampo == "") {
                    idCampo = '"' + $(this).attr("data-val") + '"';
                } else {
                    idCampo += ',"' + $(this).attr("data-val") + '"';
                }
            }
        }
    });
    return idCampo;
}

/*! \fn: generateGraphics
 *  \brief: Funcion que recibe la informaci?n por Json, la modela para Graficar
 *  \author: Luis Carlos Manrique Boada
 *  \date: 2019-08-06
 *  \param: string(campo)
 */

function generateGraphics() {

    //Variables que contienen el valor de los campos necesarios
    var cliente = capValMult("cliente");
    var negocios = capValMult("negocios");
    var canal = capValMult("canal");
    var tipoOperacion = $("#tipoOperacion").val();
    var origen = capValMult("origen");
    var destino = capValMult("destino");

    //Despachos realizados por tipo de Operaci?n" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=1',
        data: { cliente, negocios, canal, tipoOperacion, origen, destino },
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            loadAjax("start");
        },
        success: function(data) {
            //Instance graphic container
            let container = $("#rem30DaysLastGraphic");

            //Validate empty data
            if (objectLength(data) == 0) {
                container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gráfico</div>
                `);
                return;
            } else {
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
            for (let serie in data) {
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
                for (let subData in data[serie].data) {
                    //Validate exist of data
                    if (totalData[data[serie].data[subData][0]] == undefined) {
                        totalData[data[serie].data[subData][0]] = 0;
                    }

                    totalData[data[serie].data[subData][0]] += Number(data[serie].data[subData][1]);
                }
            }

            //Reformat total data
            $.each(totalData, (key, value) => {
                total["data"].push([key, value]);
            })

            //Assign total data to graphic data
            data.push(total);

            //Create "Despachos realizados por tipo de operaci?n" graphic
            grapich = new Graphics(
                container,
                "Despachos realizados por tipo de operación",
                data, {
                    dataZoom: [{
                        type: 'inside',
                        throttle: 50
                    }]
                }
            );
            grapich.createGraphic();
        },
        error: function(jqXHR, exception) {
            errorAjax(jqXHR, exception, "Error en la petición", $("#rem30DaysLastGraphic"), "HTML");
        },
        complete: function() {
            loadAjax("finished");
        }
    })

    /*
        //Get "TOP 10 Destinos de Remisiones por Cumplir" data
        $.ajax({
            url: '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=3',
            data: { cliente, negocios, canal, tipoOperacion, origen, destino },
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                loadAjax("start");
            },
            success: function(data) {
                //Instance graphic container
                let container = $("#topTenDesRemCum");

                //Validate empty data
                if (objectLength(data) == 0) {
                    container.html(`
                        <div class='btn-danger'>Sin datos, no se puede crear el gr?fico</div>
                    `);
                    return;
                } else {
                    container.html("");
                }

                //Add necessary series data
                for (let serie in data) {

                    let color = randomColor("neutral");

                    data[serie]["type"] = "bar";
                    data[serie]["itemStyle"] = {
                        color: color
                    };
                }

                //Create "TOP 10 Destinos de Remisiones por Cumplir" graphic
                grapich = new Graphics(
                    container,
                    "TOP 10 Destinos de Remisiones por Cumplir",
                    data, {
                        grid: {
                            bottom: 100,
                            left: 110,
                        },
                        xAxis: {
                            boundaryGap: true,
                            "type": "value",
                        },
                        yAxis: {
                            "type": "category",
                        },
                        dataZoom: [{
                            type: 'inside',
                            throttle: 50
                        }]
                    }
                );


                grapich.createGraphic();
            },
            error: function(jqXHR, exception) {
                errorAjax(jqXHR, exception, "Error en la petici?n", $("#topTenDesRemCum"), "HTML");
            },
            complete: function() {
                loadAjax("finished");
            }
        });
    */

    //Get "TOP 10 Destinos más frecuentes y recientes (Ciudad)" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=4',
        data: { cliente, negocios, canal, tipoOperacion, origen, destino },
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            loadAjax("start");
        },
        success: function(data) {
            //Instance graphic container
            let container = $("#topTenDesFreRecCiu");

            //Validate empty data
            if (objectLength(data) == 0) {
                container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gráfico</div>
                `);
                return;
            } else {
                container.html("");
            }

            //Add necessary series data
            for (let serie in data) {

                let color = randomColor("neutral");

                data[serie]["type"] = "bar";
                data[serie]["itemStyle"] = {
                    color: color
                };
            }

            //Create "TOP 10 Destinos más frecuentes y recientes (Ciudad)" graphic
            grapich = new Graphics(
                container,
                "TOP 10 Destinos más frecuentes y recientes (Ciudad)",
                data, {
                    grid: {
                        bottom: 100,
                        left: 110,
                    },
                    xAxis: {
                        boundaryGap: true,
                        "type": "value",
                    },
                    yAxis: {
                        "type": "category",
                    },
                    dataZoom: [{
                        type: 'inside',
                        throttle: 50
                    }]
                }
            );


            grapich.createGraphic();
        },
        error: function(jqXHR, exception) {
            errorAjax(jqXHR, exception, "Error en la petición", $("#topTenDesFreRecCiu"), "HTML");
        },
        complete: function() {
            loadAjax("finished");
        }
    });


    //Get "Consultar el estado de mi Pedido/Remisi?n" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=5',
        data: { cliente, negocios, canal, tipoOperacion, origen, destino },
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            loadAjax("start");
        },
        success: function(data) {
            //Instance graphic container
            let container = $("#estRemGraphic");

            //Validate empty data
            if (objectLength(data) == 0) {
                container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gr?fico</div>
                `);
                return;
            } else {
                container.html("");
            }

            //Add necessary series data
            for (let serie in data) {

                let color = randomColor("neutral");

                data[serie]["type"] = "bar";
                data[serie]["itemStyle"] = {
                    color: color
                };
            }

            //Create "Consultar el estado de mi Pedido/Remisi?n" graphic
            grapich = new Graphics(
                container,
                "Consultar el estado de mi Pedido/Remisi?n",
                data, {
                    grid: {
                        bottom: 100,
                        left: 150,
                    },
                    xAxis: {
                        boundaryGap: true,
                        "type": "value",
                    },
                    yAxis: {
                        "type": "category",
                    },
                    dataZoom: [{
                        type: 'inside',
                        throttle: 20
                    }]
                }
            );
            grapich.createGraphic();
        },
        error: function(jqXHR, exception) {
            errorAjax(jqXHR, exception, "Error en la petici?n", $("#estRemGraphic"), "HTML");
        },
        complete: function() {
            loadAjax("finished");
        }
    });

    //Get "TOP 10 Destinos m?s frecuentes y recientes (Estado)" data
    /*
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=6',
        data: { cliente, negocios, canal, tipoOperacion, origen, destino },
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            loadAjax("start");
        },
        success: function(data) {
            //Instance graphic container
            let container = $("#topTenDesFreRecEst");

            //Validate empty data
            if (objectLength(data) == 0) {
                container.html(`
                    <div class='btn-danger'>Sin datos, no se puede crear el gr?fico</div>
                `);
                return;
            } else {
                container.html("");
            }

            //Add necessary series data
            for (let serie in data) {

                let color = randomColor("neutral");

                data[serie]["type"] = "bar";
                data[serie]["itemStyle"] = {
                    color: color
                };
            }

            //Create "TOP 10 Destinos m?s frecuentes y recientes (Estado)" graphic
            grapich = new Graphics(
                container,
                "TOP 10 Destinos m?s frecuentes y recientes (Estado)",
                data, {
                    grid: {
                        bottom: 100,
                        left: 110,
                    },
                    xAxis: {
                        boundaryGap: true,
                        "type": "value",
                    },
                    yAxis: {
                        "type": "category",
                    },
                    dataZoom: [{
                        type: 'inside',
                        throttle: 50
                    }]
                }
            );
            grapich.createGraphic();
        },
        error: function(jqXHR, exception) {
            errorAjax(jqXHR, exception, "Error en la petici?n", $("#topTenDesFreRecEst"), "HTML");
        },
        complete: function() {
            loadAjax("finished");
        }
    });
    */

    //Get "TOP 10 Destinos más frecuentes y recientes (Ciudad)" data
    $.ajax({
        url: '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=14',
        data: { cliente, negocios, canal, tipoOperacion, origen, destino },
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
            loadAjax("start");
        },
        success: function(data) {
            //Instance graphic container
            let container = $("#topConfiRecu");

            //Validate empty data
            if (objectLength(data) == 0) {
                container.html(`
                <div class='btn-danger'>Sin datos, no se puede crear el gráfico</div>
            `);
                return;
            } else {
                container.html("");
            }

            //Add necessary series data
            for (let serie in data) {

                let color = randomColor("neutral");

                data[serie]["type"] = "bar";
                data[serie]["itemStyle"] = {
                    color: color
                };
            }

            //Create "TOP 10 Destinos más frecuentes y recientes (Ciudad)" graphic
            grapich = new Graphics(
                container,
                "TOP 10 Configuracion de vehiculo mas usada en los ultimos 30 dias",
                data, {
                    grid: {
                        bottom: 100,
                        left: 110,
                    },
                    xAxis: {
                        boundaryGap: true,
                        "type": "value",
                    },
                    yAxis: {
                        "type": "category",
                    },
                    dataZoom: [{
                        type: 'inside',
                        throttle: 50
                    }]
                }
            );


            grapich.createGraphic();
        },
        error: function(jqXHR, exception) {
            errorAjax(jqXHR, exception, "Error en la petición", $("#topConfiRecu"), "HTML");
        },
        complete: function() {
            loadAjax("finished");
        }
    });




}





//Validation style
function validationStyle(divParent) {
    $("#" + divParent + " input, #" + divParent + " textarea, #" + divParent + " select").on("change focus click", function() {
        if ($(this)[0].validity.valid == false) {
            $(this).addClass("invalid");
            $(this).removeClass("valid");
        } else {
            $(this).addClass("valid");
            $(this).removeClass("invalid");
        }
    });
}

/*! \fn: validFieldNeces
 *  \brief: Funcion que valida campos necesarios
 *  \author: Luis Carlos Manrique Boada
 *  \date: 2019-08-16
 *  \param: objerFile: objeto del campo;
 */

function validFieldNeces(objerFile, divPrint) {
    if (objerFile.attr("type") == 'radio') {
        if (!objerFile.is(':checked')) {
            personalizedAlert("danger", "Campos Requeridos", "Debe diligenciar todos los campos requeridos", true, divPrint);
            objerFile.parent().parent().css({ "border-color": "#a94442", "color": "#a94442", "background": "#eac8c8" });
            e.stopPropagation();
        } else {
            objerFile.parent().parent().css({ "border-color": "#3c763d", "color": "#3c763d", "background": "#b5deb6" });
        }
    }
    if (objerFile[0].validity.valid == false) {
        personalizedAlert("danger", "Campos Requeridos", "Debe diligenciar todos los campos requeridos", true, divPrint);
        e.stopPropagation();
    }
}


/*! \fn: loadSelect
 *  \brief: Funcion que Asignar las opciones por cada campo
 *  \author: Luis Carlos Manrique Boada
 *  \date: 2019-08-02
 *  \param:  
 */
function popUpRemPed(e) {
    //Captura el valor del campo
    var remisionPedido = $("#remisionPedido").val();

    //Valida campos necesarios
    validFieldNeces($("#remisionPedido"), $("#finallyGraphics"));
    validFieldNeces($("input[name='pedido-remision']"), $("#finallyGraphics"));

    $.ajax({
        type: "POST",
        url: '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=12',
        data: { remisionPedido },
        beforeSend: function() {
            loadAjax("start");
        },
        success: function(data) {
            if (data == 0) {
                personalizedAlert("danger", "Atención", "No se encontraton registros", true, $("#finallyGraphics"))
            } else {
                //Crea el formulario
                $("#finallyGraphics").append(data);

                //Asignar Style campos
                validationStyle("sendPrinEmail");

                //Elimina el formulario
                $(".closeWindow").children("i").on("click", function() {
                    $(this).parents(".dashBoardDialog").remove();
                });

                //Imprime el formulario
                $("#printPedRem").on("click", function() {
                    var node = document.getElementById('conten-print');
                    domtoimage.toPng(node)
                        .then(function(dataUrl) {
                            var img = new Image();
                            img.src = dataUrl;
                            $("body").append("<div id='imgTemp'><img src='" + dataUrl + "'></div>").appendTo("body");
                            $("#finallyGraphics, #sendPrinEmail, .closeWindow, #conten-print").css("display", "none");
                            $("#finallyGraphics").siblings("table").css("display", "none");
                            setTimeout(function() {
                                window.print();
                            }, 0.5);
                            setTimeout(function() {
                                $("#imgTemp").remove();
                                $("#finallyGraphics").siblings("table").css("display", "");
                                $("#finallyGraphics, #sendPrinEmail, .closeWindow, #conten-print").css("display", "");
                            }, 1);
                        });

                });

                //Envia correo Electronico con PDF al email indicado 
                $("#sendPedRem").on("click", function() {
                    //Variables necesarias
                    var nomDest = $("#nomDes").text();
                    var email = $("#email").val();
                    var node = document.getElementById('conten-print');

                    validFieldNeces($("#email"), $("#conten-print"));

                    //Campurar la Remisi?n y enviarla como Imagen
                    domtoimage.toPng(node)
                        .then(function(dataUrl) {
                            var img = new Image();
                            img.src = dataUrl;
                            var imgdata = dataUrl.replace(/^data:image\/(png|jpg);base64,/, "");
                            //Envia la imagen en base64 para se enviada por correo
                            $.ajax({
                                type: "POST",
                                url: '../satt_standa/despac/fil_dashbo_pdidos.php?opcion=13',
                                data: { imgdata, email, remisionPedido, nomDest },
                                beforeSend: function() {
                                    loadAjax("start");
                                },
                                success: function(data) {
                                    console.log(data);
                                    if (data == 'OK') {
                                        personalizedAlert("success", "Envio correo", "Correo enviado exitosamente", true, $("#contDialog"))
                                    } else {
                                        personalizedAlert("danger", "Envio correo", "El correo no fue enviado: ".data, true, $("#contDialog"))
                                    }
                                },
                                error: function(jqXHR, exception) {
                                    errorAjax(jqXHR, exception, "Error en la petici?n", data, "HTML");
                                },
                                complete: function() {
                                    loadAjax("finished");
                                }
                            });
                        })
                        .catch(function(error) {
                            personalizedAlert("danger", "Oops", "Something went wrong ".error, true, $("#contDialog"))
                            console.error('oops, something went wrong!', error);
                        });
                });
            }
        },
        error: function(jqXHR, exception) {
            errorAjax(jqXHR, exception, "Error en la petición", $("#estRemGraphic"), "HTML");
        },
        complete: function() {
            loadAjax("finished");
            cargarEasyZoom();
        }
    });
}

function cargarEasyZoom() {
    var $easyzoom = $('.easyzoom').easyZoom();

    // Setup thumbnails example
    var api1 = $easyzoom.filter('.easyzoom--with-thumbnails').data('easyZoom');

    $('.thumbnails').on('click', 'a', function(e) {
        var $this = $(this);

        e.preventDefault();

        // Use EasyZoom's `swap` method
        api1.swap($this.data('standard'), $this.attr('href'));
    });

    // Setup toggles example
    var api2 = $easyzoom.filter('.easyzoom--with-toggle').data('easyZoom');

    $('.toggle').on('click', function() {
        var $this = $(this);

        if ($this.data("active") === true) {
            $this.text("Switch on").data("active", false);
            api2.teardown();
        } else {
            $this.text("Switch off").data("active", true);
            api2._init();
        }
    });
}