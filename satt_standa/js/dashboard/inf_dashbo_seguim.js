$(function() { 
    $("#GeneraID").hide();
    $("#NovID").hide();
    $( document ).ajaxStart(function() {
      loadAjax("start");
    });
    $( document ).ajaxStop(function() {
      loadAjax("finished");
    });


    $("#cod_transpID").multiselect().multiselectfilter();
    $("#liGenera, #liNov").click(function () {
      $(".apexcharts-canvas").empty();
      $(".grafic").empty();
        try{
            obj = $(this);
            var Standa = $("#standaID").val();
            var cod_transp = 0;
            $("input[type=checkbox]:checked").each(function(i,o){
          
              if( $(this).attr("name") == 'multiselect_cod_transpID' ){
                if( cod_transp == 0 ){
                  cod_transp = '"'+ $(this).val() +'"';
                }else{
                  cod_transp += ',"'+ $(this).val() +'"';
                }
              }
            });
            const collection = document.getElementsByClassName("tipserv");
            let tip_servic20=false;let tip_servic21=false;let tip_servic22=false;
            let name_servic1='';let name_servic2='';let name_servic3='';
            Array.from(collection).forEach((element,index)  => {
              var obj1=$(element);
              switch(index){
                case 0:
                  tip_servic20=$("#"+obj1.attr("id")).is(":checked");
                  name_servic1=''+obj1.attr("id");
                break;
                case 1:
                  tip_servic21=$("#"+obj1.attr("id")).is(":checked");
                  name_servic2=''+obj1.attr("id");
                break;
                case 2:
                  tip_servic22=$("#"+obj1.attr("id")).is(":checked");
                  name_servic3=''+obj1.attr("id");
                break;
              }
            });
            
            
            
            if(cod_transp == 0 || cod_transp=='""'){
              alert('Atencion: Debe seleccionar la transportadora');
              return false;
            }
            
            if( obj.attr("tipo") == "gen" )
            { 
              
                    $(".apexcharts-canvas").empty();
                    $("#GeneraID").show();
                    $("#NovID").hide();
                    $("#liGenera").attr("tipo","invalid");
                    $.ajax({
                        url: '../satt_standa/despac/ajax_inf_dashbo_seguim.php?Option=getGrafic1&Ajax=on',
                        data: {cod_transp:cod_transp,name_servic1:tip_servic20,name_servic2:tip_servic21,name_servic3:tip_servic22},
                        type: 'post',
                        dataType: 'json',
                        success: function(data) {
                            if(Array.isArray(data.Nacional) || Array.isArray(data.Urbano))
                            {
                               var options = {
                                  series: [{
                                    name: "Nacional",
                                    data: data.Nacional,
                                }, {
                                  name: "Urbanos",
                                  data: data.Urbano
                                }],
                                  chart: {
                                  height: 300,
                                  type: 'area'
                                },
                                dataLabels: {
                                  enabled: true
                                },
                                title: {
                                  text: 'TIPOS DE DESPACHOS POR DIA',
                                  align: 'left'
                                },
                                stroke: {
                                  curve: 'smooth'
                                },
                                xaxis: {
                                  categories: data.dias
                                },
                              };
                              var chart = new ApexCharts(document.querySelector("#Graphic1"), options);
                              chart.render();
                            }else{
                              $("#Graphic1").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                            }
                        },
                        error: function(jqXHR, exception) {
                            errorAjax(jqXHR, exception, "Error en la peticin", $("#rem30DaysLastGraphic"), "HTML");
                        }
                    })
        
                    $.ajax({
                      url: '../satt_standa/despac/ajax_inf_dashbo_seguim.php?Option=getGrafic2_3_4&Ajax=on',
                      data: {cod_transp:cod_transp,name_servic1:tip_servic20,name_servic2:tip_servic21,name_servic3:tip_servic22},
                      type: 'post',
                      dataType: 'json',
                      success: function(data) {
                        
                        
                          if(Array.isArray(data.data1))
                          {
                            var options = {
                              series: data.data1,
                              chart: {
                              width: 450,
                              height: 400,
                              type: 'pie',
                            },
                            labels: ['Programacion para el corte', 'Programacion pendientes'],
                            title: {
                              text: 'VEHI.PROCESO PRE-CARGE:PROGRAMACION',
                              align: 'left'
                            },
                            responsive: [{
                              breakpoint: 200,
                              options: {
                                chart: {
                                  width: 150
                                },
                                legend: {
                                  position: 'bottom'
                                }
                              }
                            }]
                            };
                            var chart1 = new ApexCharts(document.querySelector("#Graphic2"), options);
                            chart1.render();
                          }else{
                            $("#Graphic2").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                          }
                          
                          if(Array.isArray(data.data2))
                          {
                            var options2 = {
                              series: data.data2,
                              //series: [1,2],
                              chart: {
                              type: 'donut',
                              width: 400,
                              height: 400,
                            },
                            labels: ['Anulados', 'En Planta'],
                            title: {
                              text: 'VEHI.PROCESO PRE-CARGE:REGISTROS',
                              align: 'left'
                            },
                            responsive: [{
                              breakpoint: 200,
                              options: {
                                chart: {
                                  width: 150
                                },
                                legend: {
                                  position: 'bottom'
                                }
                              }
                            }]
                            };
                            var chart2 = new ApexCharts(document.querySelector("#Graphic3"), options2);
                            chart2.render();
                          }else{
                            $("#Graphic3").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                          }
                          if(Array.isArray(data.data3))
                          {
                            var options4 = {
                              series: data.data3,
                              //series: [1,2,3,4,5,6],
                              chart: {
                              width: 500,
                              height: 500,
                              type: 'pie',
                            },
                            labels: ['Porteria','Sin Comunicacion','Transito a planta','Con novedad no llegada a planta','Con novedad llegada a planta','A cargo empresa'],
                            title: {
                              text: 'VEHI.PROCESO PRE-CARGE:ESTADOS',
                              align: 'left'
                            },
                            responsive: [{
                              breakpoint: 200,
                              options: {
                                chart: {
                                  width: 150
                                },
                                legend: {
                                  position: 'bottom'
                                }
                              }
                            }]
                            };
                            var chart4 = new ApexCharts(document.querySelector("#Graphic4"), options4);
                            chart4.render();
                          }else{
                            $("#Graphic4").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                          }
                      },
                      error: function(jqXHR, exception) {
                          errorAjax(jqXHR, exception, "Error en la peticin", $("#rem30DaysLastGraphic"), "HTML");
                      }
                    })
        
                    $.ajax({
                    url: '../satt_standa/despac/ajax_inf_dashbo_seguim.php?Option=getGrafic5&Ajax=on',
                    data: {cod_transp},
                    type: 'post',
                    dataType: 'json',
                    success: function(data) {
                      if(Array.isArray(data.data1))
                      {
                        var options = {
                          series: [{
                          data: data.data1
                        }],
                          chart: {
                          height: 350,
                          type: 'bar',
                        },
                        plotOptions: {
                          bar: {
                            columnWidth: '30%',
                            distributed: true,
                          }
                        },
                        colors:['#F6CEEC','#F5A9F2','#F781F3','#FA58F4'],
                        dataLabels: {
                          enabled: true
                        },
                        legend: {
                          show: false
                        },
                        title: {
                          text: 'VEHICULOS PROCESO CARGE',
                          align: 'left'
                        },
                        xaxis: {
                          categories: ['Aviso control cargue (0-30 MIN)','Alerta cargue (31-60 MIN)','Sin cargue (61-90 MIN)','Novedad en cargue (91 MIN)']
                        }
                        };
                        var chart5 = new ApexCharts(document.querySelector("#Graphic5"), options);
                        chart5.render();
                      }else{
                        $("#Graphic5").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                      }
                    },
                    error: function(jqXHR, exception) {
                        errorAjax(jqXHR, exception, "Error en la peticin", $("#rem30DaysLastGraphic"), "HTML");
                    },
                    })
        
                    $.ajax({
                    url: '../satt_standa/despac/ajax_inf_dashbo_seguim.php?Option=getGrafic6&Ajax=on',
                    data: {cod_transp:cod_transp,name_servic1:tip_servic20,name_servic2:tip_servic21,name_servic3:tip_servic22},
                    type: 'post',
                    dataType: 'json',
                    success: function(data) {
                      if(Array.isArray(data.data1))
                      {
                        var options = {
                          series: [{
                          data: data.data1
                        }],
                          chart: {
                          type: 'bar',
                          height: 330
                        },
                        plotOptions: {
                          bar: {
                            barHeight: '100%',
                            distributed: true,
                            horizontal: true,
                            dataLabels: {
                              position: 'bottom'
                            },
                          }
                        },
                        colors: ['#FFFF66', '#FF9900','#FF0000','#CC33FF'],
                        dataLabels: {
                          enabled: true,
                          textAnchor: 'start',
                          style: {
                            colors: ['#fff']
                          },
                          offsetX: 0,
                          dropShadow: {
                            enabled: true
                          }
                        },
                        stroke: {
                          width: 1,
                          colors: ['#fff']
                        },
                        xaxis: {
                          categories: ['(SEGUIMIENTO) (0-30 MIN)','ALARMA NARANJA (31-60 MIN)','ALARMA ROJA (61-90 MIN)','ALARMA VIOLETA (91 MIN) hasta solucion'],
                        },
                        yaxis: {
                          labels: {
                            show: false
                          }
                        },
                        title: {
                          text: 'VEHICULOS PROCESO TRANSITO',
                          align: 'left'
                        },
                        tooltip: {
                          theme: 'dark',
                          x: {
                            show: false
                          },
                          y: {
                            title: {
                              formatter: function () {
                                return ''
                              }
                            }
                          }
                        }
                        };
                        var chart6 = new ApexCharts(document.querySelector("#Graphic6"), options);
                        chart6.render();
                      }else{
                        $("#Graphic6").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                      }
                    },
                    error: function(jqXHR, exception) {
                        errorAjax(jqXHR, exception, "Error en la peticin", $("#rem30DaysLastGraphic"), "HTML");
                    }
                    })
        
                    $.ajax({
                    url: '../satt_standa/despac/ajax_inf_dashbo_seguim.php?Option=getGrafic7&Ajax=on',
                    data: {cod_transp:cod_transp,name_servic1:tip_servic20,name_servic2:tip_servic21,name_servic3:tip_servic22},
                    type: 'post',
                    dataType: 'json',
                    success: function(data) {
                      if(Array.isArray(data.data1))
                      {
                        var options = {
                          series: data.data1,
                          chart: {
                          width: 500,
                          height: 450,
                          type: 'pie',
                        },
                        colors: ['rgb(188, 245, 169)', 'rgb(1, 223, 1)','rgb(8, 138, 8)','rgb(11, 97, 11)'],
                        labels: ['Proximo a descargue (0-30 MIN)','En descargue (31-60 MIN)','Sin descargue (61-90 MIN)','Novedad en descargue (91 MIN)'],
                        title: {
                          text: 'VEHI.PROCESO DESCARGUE',
                          align: 'left'
                        },
                        responsive: [{
                          breakpoint: 200,
                          options: {
                            chart: {
                              width: 100
                            },
                            legend: {
                              position: 'bottom'
                            }
                          }
                        }]
                        };
                        var chart7 = new ApexCharts(document.querySelector("#Graphic7"), options);
                        chart7.render();
                      }else{
                        $("#Graphic7").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                      }
                      
                    },
                    error: function(jqXHR, exception) {
                        errorAjax(jqXHR, exception, "Error en la peticin", $("#rem30DaysLastGraphic"), "HTML");
                    }
                    })
        
                    $.ajax({
                    url: '../satt_standa/despac/ajax_inf_dashbo_seguim.php?Option=getGrafic8&Ajax=on',
                    data: {cod_transp:cod_transp,name_servic1:tip_servic20,name_servic2:tip_servic21,name_servic3:tip_servic22},
                    type: 'post',
                    dataType: 'json',
                    success: function(data) {
                      if(Array.isArray(data.data))
                      {
                        var options = {
                          series: data.data,
                          chart: {
                          width: 370,
                          height: 370,
                          type: 'pie',
                        },
                        labels: data.names,
                        title: {
                          text: 'NOVEDADES EN RUTA',
                          align: 'left'
                        },
                        responsive: [{
                          breakpoint: 200,
                          options: {
                            chart: {
                              width: 150
                            },
                            legend: {
                              position: 'bottom'
                            }
                          }
                        }]
                        };
                        var chart8 = new ApexCharts(document.querySelector("#Graphic8"), options);
                        chart8.render();
                      }else{
                        $("#Graphic8").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                      }
                      
        
        
                    },
                    error: function(jqXHR, exception) {
                        errorAjax(jqXHR, exception, "Error en la peticin", $("#rem30DaysLastGraphic"), "HTML");
                    }
                    })
        
                    $.ajax({
                      url: '../satt_standa/despac/ajax_inf_dashbo_seguim.php?Option=getGrafic9&Ajax=on',
                      data: {cod_transp:cod_transp,name_servic1:tip_servic20,name_servic2:tip_servic21,name_servic3:tip_servic22},
                      type: 'post',
                      dataType: 'json',
                      success: function(data) {
                        if(Array.isArray(data.data))
                        {
                          var options = {
                            series: data.data,
                            chart: {
                            type: 'donut',
                            width: 350,
                            height: 350,
                          },
                          labels: data.names,
                          title: {
                            text: 'ESTADOS GPS',
                            align: 'left'
                          },
                          responsive: [{
                            breakpoint: 200,
                            options: {
                              chart: {
                                width: 150
                              },
                              legend: {
                                position: 'bottom'
                              }
                            }
                          }]
                          };
                          var chart9 = new ApexCharts(document.querySelector("#Graphic9"), options);
                          chart9.render();
                        }else{
                          $("#Graphic9").append('<h1 style="text-align: center;color: #337ab7;">No hay datos</h1>');
                        }
                      },
                      error: function(jqXHR, exception) {
                          errorAjax(jqXHR, exception, "Error en la peticin", $("#rem30DaysLastGraphic"), "HTML");
                      }
                    })
        
                    $.ajax({
                      url: '../satt_standa/despac/ajax_inf_dashbo_seguim.php?Option=getGrafic10&Ajax=on',
                      data: {cod_transp:cod_transp,name_servic1:tip_servic20,name_servic2:tip_servic21,name_servic3:tip_servic22},
                      type: 'post',
                      dataType: 'json',
                      success: function(data) {
                        if(Array.isArray(data.data))
                        {
                          var options = {
                            series: data.data,
                            chart: {
                            type: 'donut',
                            width: 400,
                            height: 400,
                          },
                          labels: ['Con integrador','Sin integrador'],
                          title: {
                            text: 'ESTADO DE EMPRESAS',
                            align: 'left'
                          },
                          responsive: [{
                            breakpoint: 200,
                            options: {
                              chart: {
                                width: 150
                              },
                              legend: {
                                position: 'bottom'
                              }
                            }
                          }]
                          };
                          var chart10 = new ApexCharts(document.querySelector("#Graphic10"), options);
                          chart10.render();
                        }else{
                          $("#Graphic10").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                        }
                        
                      },
                      error: function(jqXHR, exception) {
                          errorAjax(jqXHR, exception, "Error en la peticin", $("#rem30DaysLastGraphic"), "HTML");
                      }
                    })
        
                    $.ajax({
                      url: '../satt_standa/despac/ajax_inf_dashbo_seguim.php?Option=getGrafic11&Ajax=on',
                      data: {cod_transp:cod_transp,name_servic1:tip_servic20,name_servic2:tip_servic21,name_servic3:tip_servic22},
                      type: 'post',
                      dataType: 'json',
                      success: function(data) {
                        if(Array.isArray(data.data))
                        {
                          var options = {
                            series: data.data,
                            chart: {
                            type: 'donut',
                            width: 400,
                            height: 400,
                          },
                          labels: data.names,
                          title: {
                            text: 'ITINERARIO',
                            align: 'left'
                          },
                          responsive: [{
                            breakpoint: 200,
                            options: {
                              chart: {
                                width: 150
                              },
                              legend: {
                                position: 'bottom'
                              }
                            }
                          }]
                          };
                          var chart11 = new ApexCharts(document.querySelector("#Graphic11"), options);
                          chart11.render();
                        }else{
                          $("#Graphic11").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                        }
          
                      },
                      error: function(jqXHR, exception) {
                          errorAjax(jqXHR, exception, "Error en la peticin", $("#rem30DaysLastGraphic"), "HTML");
                      }
                    })


                      //$("#GeneraID").html(data); 
            }
            else if( obj.attr("tipo") == "nov" )
            {
              const date_ = new Date();
              $(".apexcharts-canvas").empty();
              $("#GeneraID").hide();
              $("#NovID").show();
              $("#liNov").attr("tipo","invalid");
              $.ajax({
                url: '../satt_standa/despac/ajax_inf_dashbo_seguim.php?Option=getGrafic12&Ajax=on',
                data: {cod_transp:cod_transp,name_servic1:tip_servic20,name_servic2:tip_servic21,name_servic3:tip_servic22},
                type: 'post',
                dataType: 'json',
                success: function(data) {
                  if(Array.isArray(data.data) && Array.isArray(data.names))
                  {
                    var options = {
                      series: [{
                      data: data.data
                    }],
                      chart: {
                      type: 'bar',
                      height: 300
                    },
                    plotOptions: {
                      bar: {
                        barHeight: '80%',
                        distributed: true,
                        horizontal: true,
                        dataLabels: {
                          position: 'bottom'
                        },
                      }
                    },
                    //colors: ['#FFFF66', '#FF9900','#FF0000','#CC33FF'],
                    dataLabels: {
                      enabled: true,
                      textAnchor: 'start',
                      style: {
                        colors: ['#fff']
                      },
                      offsetX: 0,
                      dropShadow: {
                        enabled: true
                      }
                    },
                    stroke: {
                      width: 1,
                      colors: ['#fff']
                    },
                    xaxis: {
                      categories: data.names,
                    },
                    yaxis: {
                      labels: {
                        show: false
                      }
                    },
                    title: {
                      text: 'PRODUCTIVIDAD POR HORA '+date_.getHours()+':00 - '+date_.getHours()+':59',
                      align: 'left'
                    },
                    tooltip: {
                      theme: 'dark',
                      x: {
                        show: false
                      },
                      y: {
                        title: {
                          formatter: function () {
                            return ''
                          }
                        }
                      }
                    }
                    };
                    var chart12 = new ApexCharts(document.querySelector("#Graphic12"), options);
                    chart12.render();
                  }else{
                    $("#Graphic12").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                  }
                },
                error: function(jqXHR, exception) {
                    errorAjax(jqXHR, exception, "Error en la peticin", $("#rem30DaysLastGraphic"), "HTML");
                }
                })

                $.ajax({
                  url: '../satt_standa/despac/ajax_inf_dashbo_seguim.php?Option=getGrafic13&Ajax=on',
                  data: {cod_transp:cod_transp,name_servic1:tip_servic20,name_servic2:tip_servic21,name_servic3:tip_servic22},
                  type: 'post',
                  dataType: 'json',
                  success: function(data) {
                    if(Array.isArray(data.users) && (Array.isArray(data.data1) || Array.isArray(data.data2) || Array.isArray(data.data3) || Array.isArray(data.data4)))
                    {
                      var options13 = {
                        series: [{
                        name: '(SEGUIMIENTO) (0-30 MIN)',
                        data: data.data1
                      }, {
                        name: 'ALARMA NARANJA (31-60 MIN)',
                        data: data.data2
                      }, {
                        name: 'ALARMA ROJA (61-90 MIN)',
                        data: data.data3
                      }, {
                        name: 'ALARMA VIOLETA (91 MIN) hasta solucion',
                        data: data.data4
                      }],
                        chart: {
                        type: 'bar',
                        height: 300,
                        stacked: true,
                        toolbar: {
                          show: false
                        },
                        zoom: {
                          enabled: false
                        }
                      },
                      colors: ['#FFFF66', '#FF9900','#FF0000','#CC33FF'],
                      responsive: [{
                        breakpoint: 200,
                        options: {
                          legend: {
                            position: 'bottom',
                            offsetX: -10,
                            offsetY: 0
                          }
                        }
                      }],
                      title: {
                        text: 'ESTADOS DE ASIGNACION POR OPERADOR',
                        align: 'left'
                      },
                      plotOptions: {
                        bar: {
                          horizontal: false,
                          borderRadius: 10
                        },
                      },
                      xaxis: {
                        categories: data.users,
                      },
                      legend: {
                        position: 'right',
                        offsetY: 40
                      },
                      };
              
                      var chart13 = new ApexCharts(document.querySelector("#Graphic13"), options13);
                      chart13.render();
                    }else{
                      $("#Graphic13").append('<h1 class="grafic" style="text-align: center;color: #337ab7;">No hay datos</h1>');
                    }

                  },
                  error: function(jqXHR, exception) {
                      errorAjax(jqXHR, exception, "Error en la peticin", $("#rem30DaysLastGraphic"), "HTML");
                  }
                });
                
                $('#iframe1').attr('src', "../satt_standa/inform/inf_dashbo_seguim_table1.php?cod_transp="+cod_transp+"&tip_servic20="+tip_servic20+"&tip_servic21="+tip_servic21+"&tip_servic22="+tip_servic22);
                $('#iframe2').attr('src', "../satt_standa/inform/inf_dashbo_seguim_table2.php?cod_transp="+cod_transp+"&tip_servic20="+tip_servic20+"&tip_servic21="+tip_servic21+"&tip_servic22="+tip_servic22);
                             
            }else if( obj.attr("tipo") == "invalid" )
            {
              
              alert("Por favor actualize la pagina!");
              $("#GeneraID").hide();
              $("#NovID").hide();
            }

        }catch (e){
        console.log("Error en liGenera.click() "+e.message);
        }
    });
    
    /*$('#iframe1').ready(function () {
      loadAjax("start");
    });
    $('#iframe1').load(function () {
      loadAjax("finished");
    });
    $('#iframe2').ready(function () {
      loadAjax("start");
    });
    $('#iframe2').load(function () {
      loadAjax("finished");
    });*/
});

