$(document).ready(function() { 
    selectTable(1);
    globalThis.filterType = 1;
    globalThis.codActdes;
    globalThis.FechaAejecutar;
    globalThis.horaFin;
    globalThis.intiHour;
    globalThis.lastHour;
    globalThis.intiDate;
    globalThis.lastDate;
    globalThis.frecuencySelect;
    globalThis.codPeriod;
    globalThis.dateTime = true;
    globalThis.showNotify = true;
    globalThis.row = [];


});

function notifyUser(data) {   
    globalThis.showNotify = false;
    let adminOrOpe = globalThis.filterType == 1 ? 'administrativas ' : 'operativas ';

    Swal.fire({
        title: 'Actividad(es) a Ejecutar!',
        text:'Tiene '+ data.length +' actividad(es) '+ globalThis.filterType == 1 ? 'administrativas ' : 'operativas '+'pendientes  por ejecutar',
        html: `
        <p>Tiene `+ data.length + ` actividad(es)  `+ adminOrOpe + ` pendientes  por ejecutar</p>
        <ul class="list-group" id="notify"></ul>`,       
        type: 'info',
        confirmButtonColor: '#336600'
    })
    notify = $("#notify");
    notify.empty();
    let i = 0;
    data.forEach(element => {
        i++;
        let html = rowToNotify(element,i);
        notify.append(html);
    });
    
}

function executeFilter() {
    try {
        //Get data
        $.ajax({
            url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=4',
            dataType: 'json',
            type: "post",
            data: {initDate:$("#fec_inicio").val(), lastDate:$("#fec_finxxx").val(), 'OperativeOrAdmin' : globalThis.filterType },
            beforeSend: function() {
                loadAjax("start");
            },
            success: function(data) {
                if(globalThis.showNotify == true)
                {
                    notifyUser(data);
                    globalThis.showNotify = false;
                }

                var fecha_inicio = $("#fec_inicio").val();
                var fecha_finalx = $("#fec_finxxx").val();

                $("#text_general_fec").html("<center>ACTIVIDADES PENDIENTES POR GESTIONAR DEL " + fecha_inicio + " AL " + fecha_finalx + "</center>");
                    table_general = $("#tabla_inf_general tbody");
                    $("#tabla_inf_general resultado_info_general").remove();
                    table_general.empty();
                    for (var i = 0; i < data.length; i++) {
                        table_general.append(createTr(data[i], globalThis.filterType == 1 ? 'admin' : 'operative'));
                    } 
                executeFilterDoneData(globalThis.filterType);
            },
            complete: function() {
                loadAjax("end");
            },
            error: function(jqXHR, exception) {
                loadAjax("end");
                errorAjax(jqXHR, exception, "Error al cargar el campo.", "", "alert")
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function executeFilterDoneData(OperativeOrAdmin) {
    try {
        //Get data
        $.ajax({
            url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=5',
            dataType: 'json',
            type: "post",
            data: {initDate:$("#fec_inicio").val(), lastDate:$("#fec_finxxx").val(), 'OperativeOrAdmin' : OperativeOrAdmin },
            beforeSend: function() {
                loadAjax("start");
            },
            success: function(data) {
                var fecha_inicio = $("#fec_inicio").val();
                var fecha_finalx = $("#fec_finxxx").val();

                $("#text_general_eje").html("<center>ACTIVIDADES EJECUTADAS DEL " + fecha_inicio + " AL " + fecha_finalx + "</center>");
                    table_general = $("#tabla_inf_especifico tbody");
                    $("#tabla_inf_especifico resultado_info_especifico").remove();
                    table_general.empty();
                    for (var i = 0; i < data.length; i++) {
                        table_general.append(createTr(data[i], globalThis.filterType == 1 ? 'Done' : 'operativeDone'));
                    }

            },
            complete: function() {
                loadAjax("end");
            },
            error: function(jqXHR, exception) {
                loadAjax("end");
                errorAjax(jqXHR, exception, "Error al cargar el campo.", "", "alert")
            }
        });
    } catch (error) {
        console.log(error);
    }
}

//Create tr rowAdmin
function rowAdmin(row) {
    //Create Elements 
    //Tr
    var tr = $(`<tr>
        <td class='can_totalx' style='text-align: center;'><a href="#" onclick="OpenHistoryModal(` + row["cod_actdes"] +`,'`+ row["FechaAejecutar"] + `','`+ row["hor_finalx"] + `','`+ 2 +`','`+ row["cod_period"] + `')">` + row["cod_actdes"] + `</a></td>
        <td class='can_totalx' style='text-align: center; background-color: ` + timerColor(row["OverTime"]) + `;'>` + row["OverTime"].toFixed() + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["tit_activi"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["des_activi"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatAMPM(row["fec_inicia"] + ' ' + row["hor_inicia"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_perfil"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_usuari"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_inicia"] + ' ' + row["hor_inicia"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_finalx"] + ' ' + row["hor_finalx"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["des_period"] + ' ' + row["con_frecue"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_ulteje"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row['cod_period'] != '6' ? row["FechaAejecutar"] + ' ' + row["hor_inicia"]:row["FechaAejecutar"]) + `</td>
    </tr>"`);

    return tr;
}

//Create tr rowAdmin
function rowOperative(row) {
    //Create Elements
    //Tr
    var tr = $(`<tr>
        <td class='can_totalx' style='text-align: center;'><a href="#" onclick="OpenHistoryModal(` + row["cod_actdes"] +`,'`+ row["FechaAejecutar"] + `','`+ row["hor_finalx"] + `','`+ 2 +`','`+ row["cod_period"] + `')">` + row["cod_actdes"] + `</a></td>
        <td class='can_totalx' style='text-align: center; background-color: ` + timerColor(row["OverTime"]) + `'>` + row["OverTime"].toFixed() + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["tit_activi"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["des_activi"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatAMPM(row["fec_inicia"] + ' ' + row["hor_inicia"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_perfil"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_usuari"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["abr_tercer"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["num_placax"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_noveda"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_inicia"] + ' ' + row["hor_inicia"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_finalx"] + ' ' + row["hor_finalx"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["des_period"] + ' ' + row["con_frecue"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_ulteje"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["FechaAejecutar"] + ' ' + row["hor_inicia"]) + `</td>
    </tr>"`);

    return tr;
}

//Create tr rowAdmin
function rowAdminDone(row) {
    //Create Elements
    var tr = $(`<tr>
        <td class='can_totalx' style='text-align: center;'><a href="#" onclick="OpenHistoryModal(` + row["cod_actdes"] +`,'`+ row["FechaAejecutar"] + `','`+ row["hor_finalx"] + `',`+ 1 + `)">` + row["cod_actdes"] + `</a></td>
        <td class='can_totalx' style='text-align: center; background-color: ` + timerColor(row["time"]) + `'>` + row["time"].toFixed() + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["tit_activi"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["des_activi"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatAMPM(row['cod_period'] != '6' ? row["fec_inicia"] + ' ' + row["hor_inicia"] : row["fec_dbejec"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_perfil"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_usuari"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_inicia"] + ' ' + row["hor_inicia"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_finalx"] + ' ' + row["hor_finalx"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["des_period"] + ' ' + row["con_frecue"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["sta_ejecuc"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_ejecuc"]) + `</td>
    </tr>"`);

    return tr;
}

function rowToNotify(row,i) {
    //Create Elements
    var tr = $(`
    <li class="list-group-item d-flex align-items-center" style="border: none">
    <span class="badge badge-primary badge-pill mr-5">` +  i  + `</span>
    ` +  row["tit_activi"] + `
    </li>`);

    return tr;
}

function rowOperativeDone(row) {
    //Create Elements
    var tr = $(`<tr>
        <td class='can_totalx' style='text-align: center;'><a href="#" onclick="OpenHistoryModal(` + row["cod_actdes"] +`,'`+ row["FechaAejecutar"] + `','`+ row["hor_finalx"] + `',`+ 1 + `)">` + row["cod_actdes"] + `</a></td>
        <td class='can_totalx' style='text-align: center; background-color: ` + timerColor(row["time"]) + `'>` + row["time"].toFixed() + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["tit_activi"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["des_activi"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatAMPM(row['cod_period'] != '6' ? row["fec_inicia"] + ' ' + row["hor_inicia"] : row["fec_dbejec"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_perfil"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_usuari"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["abr_tercer"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["num_placax"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_noveda"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_inicia"] + ' ' + row["hor_inicia"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_finalx"] + ' ' + row["hor_finalx"]) + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["des_period"] + ' ' + row["con_frecue"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["sta_ejecuc"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_ejecuc"]) + `</td>
    </tr>"`);

    return tr;
}

function rowHistoricalDone(row) {
    //Create Elements
    var tr = $(`<tr>
        <td class='can_totalx' style='text-align: center;'>` + formatDate(row["fec_ejecuc"]) +`</td>
        <td class='can_totalx' style='text-align: center;'>` + row["usr_creaci"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` +'% '+ row["val_porcen"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["sta_ejecuc"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["obs_ejecuc"] + `</td>
    </tr>"`);

    return tr;
}

//Create tr
function createTr(row, tipoInforme) {
    switch (tipoInforme) {
        case 'admin':
            return rowAdmin(row);
            break;
        case 'Done':
            return rowAdminDone(row);
            break;
        case 'operative':
            return rowOperative(row);
            break;
        case 'operativeDone':
            return rowOperativeDone(row);
            break;
        case 'Historical':
            return rowHistoricalDone(row);
            break;    
        default:
            return rowAdmin(row);
            break;
    }
}

function selectTable(boolean) {
    var fecha_inicio = $("#fec_inicio").val();
    var fecha_finalx = $("#fec_finxxx").val();

    if(boolean == 1){

        globalThis.filterType = 1;
        globalThis.showNotify = true;
        $.ajax({
            url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=3',
            type: "post",
            async: false,
            data: {'initDate' : fecha_inicio, 'lastDate' :fecha_finalx, 'indi':1 },
            success: function(data) {
                $("#pills-general").empty();
                $("#pills-general").append(data);
                executeFilter();
            },
            error: function() {
                console.log("error");
            }
        });
    }

    if(boolean == 2 )
    {
        globalThis.filterType = 2;
        globalThis.showNotify = true;
        $.ajax({
            url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=2',
            type: "post",
            async: false,
            data: {'initDate' : fecha_inicio, 'lastDate' :fecha_finalx, 'indi':2 },
            success: function(data) {
                $("#pills-general").empty();
                $("#pills-general").append(data);
                executeFilter();
            },
            error: function() {
                console.log("error");
            }
        });
    }

    if(boolean == 3 )
    {
        globalThis.filterType = 3;
        globalThis.showNotify = true;
        $.ajax({
            url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=17',
            type: "post",
            async: false,
            data: {'initDate' : fecha_inicio, 'lastDate' :fecha_finalx, 'indi':1 },
            success: function(data) {
                $("#pills-general").empty();
                $("#pills-general").append(data);
                executeFilter();
            },
            error: function() {
                console.log("error");
            }

        });

    }

}

function OpenHistoryModal(event,FechaAejecutar,hora,done,codPer) {    
    globalThis.codActdes = event;
    globalThis.FechaAejecutar = FechaAejecutar;
    globalThis.horaFin = hora;
    globalThis.codPeriod = codPer;

    try {
        if(done == 1)
        {
            $.ajax({
                url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=6',
                type: "post",
                async: false,
                data: {cod_activ : event },
                  async: false,
                  success: function(data) {
                    $("#IdhistoryModal").empty();
                    $("#IdhistoryModal").append(data);
                    $("#buttonNewObj").empty();
                    GetHistoricalData(event);
                    $("#historyModal").modal("show");          
                  },
                  error: function() {
                      console.log("error");
                  }
              });
        }else{
            $.ajax({

                url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=6',
                type: "post",
                async: false,
                data: {cod_activ : event },
                  async: false,
                  success: function(data) {
                    $("#IdhistoryModal").empty();
                    $("#IdhistoryModal").append(data);
                    $("#buttonNewObj").empty();
                    $("#buttonNewObj").append('<button type="button" class="btn btn-success" style="background-color:#509334" onclick="FormHistorical()">Nueva Observaci?n</button>');
                    GetHistoricalData(event);
                    $("#historyModal").modal("show");          
                  },
                  error: function() {
                      console.log("error");
                  }
              });
        }
          
          
      } catch (error) {
          console.log(error);
      }
}

function GetHistoricalData(event) {    
    try {
        globalThis.codActdes = event;
          $.ajax({
            url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=7',
            type: "post",
            dataType: 'json',
            data: {cod_activ : event },
              async: false,
              success: function(data) {  
                if(data.length > 0)
                {
                    $("#title-modal-history").html("<center>DESCRIPCION DE LA ACTIVIDAD  No " + event + "</center>");
                    $('#ActivityType').val(data[0].tip_actdes);  
                    $('#ActivityWork').val(data[0].tit_activi);    
                    $('#profile').val(data[0].nom_perfil);    
                    $('#initDate').val(formatDate(data[0].fec_inicia + ' ' + data[0].hor_inicia));    
                    $('#LatsDate').val(formatDate(data[0].fec_finalx + ' ' + data[0].hor_finalx));    
                    $('#user').val(data[0].nom_usuari);    
                    $('#fecuency').val(data[0].des_period);    
                    $('#days').val(data[0].con_frecue);    
                    $('#description').val(data[0].des_activi);      
                    table_general = $("#table_History tbody");
                    $("#table_History resultado_Historical").remove();
                    table_general.empty();
                    for (var i = 0; i < data.length; i++) {
                        table_general.append(createTr(data[i],'Historical'));
                    }  
                }else{
                    $("#title-modal-history").html("<center>DESCRIPCION DE LA ACTIVIDAD  No " + event + "</center>");
                    $("#IdhistoryModal").empty();
                    $("#IdhistoryModal").append('<p>Lo sentimos no existe informaci&oacute;n relacionada a esta actividad a&uacute;n.</p><div class="row  mt-3"><div class="col-12" id="formHistorial"></div></div>');
                }
              },
              error: function() {
                  console.log("error");
              }
          });
          
      } catch (error) {
          console.log(error);
      }
}

function FormHistorical()
{
    $.ajax({
        url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=8',
        type: "post",
        async: false,
        success: function(data) {
            $("#formHistorial").empty();
            $("#formHistorial").append(data);
            executeFilter();
        },
        error: function() {
            console.log("error");
        }
    });
}

function insertNewObj() {
     try {
         formData = $("#NewObj").serializeArray();
         if(formData[1]['value'] != '' &&  formData[0]['value'] != '0')
         {
            $.ajax({
                url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=9',
                dataType: 'json',
                type: "post",
                data: {NewObj : formData, codActdes : globalThis.codActdes, FechaAejecutar: globalThis.FechaAejecutar, horaFin :  globalThis.horaFin, codPeriod : globalThis.codPeriod},
                success: function(data) {
                   if(data.status == 200)
                   {
                    $('#historyModal').modal('hide');
                    Swal.fire({
                        title: 'Registrado!',
                        text: "Observaci&oacute;n registrada con exito!",
                        type: 'success',
                        confirmButtonColor: '#336600'
                    }).then((result) => {
                        if (result.value) {
                            selectTable(globalThis.filterType);
                        }
                    })
                   }else{
                    Swal.fire({
                        title: 'Error!',
                        text: 'Ha sucedido un error, por favor intente de nuevo',
                        type: 'error',
                        confirmButtonColor: '#336600'
                    });
                   }
                }
            });
         }else{
            Swal.fire({
                title: 'Error!',
                text: 'Por favor diligencie todos los campos.',
                type: 'error',
                confirmButtonColor: '#336600'
            });
         }
    } catch (error) {
        console.log(error);
    } 
}

function openNewActiviModal(value) {
    callFormOptionDateTime(12);
    getTitleData();
    $.ajax({
        url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=10',
        type: "post",
        async: false,
        success: function(data) {
            $("#newActiviModal").modal("show");
            $("#dinamycSelects").empty();
            $("#dinamycSelects").append(data);
            executeFilter();
        },
        error: function() {
            console.log("error");
        }
    });
}

function formatAMPM(hour) {
    var date = new Date(hour);
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
}

function formatDate(time) {
    return time == null ? 'Sin informaci&oacute;n' : new Date(time).toLocaleString('es-CO', {hour12: true});
}

function loadAjax(x) {
    try {
        if (x == "start") {
            $.blockUI({ message: '<div>Espere un momento</div>' });
        } else {
            $.unblockUI();
        }
    } catch (error) {
        console.log(error);
    }

}

function selectFuntion(Elemento) {
    var valor = $(Elemento).val();
    if(valor == 2)
    {
        $.ajax({
            url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=1',
            type: "post",
            async: false,
            success: function(data) {
                $("#ajaxOption").empty();
                $("#ajaxOption").append(data);
            },
            error: function() {
                console.log("error");
            }
        });
    }else{
        $("#ajaxOption").empty();
    }
}

function callFormOptionFrecuency(Elemento) {
    var valor = $(Elemento).val();

    if(valor == 1 && globalThis.dateTime == true)
    {
        callFormOptionDateTime(13);
        globalThis.dateTime = false;
    }else if(valor != 1 && globalThis.dateTime == false){
        callFormOptionDateTime(12);
        globalThis.dateTime = true;
    }
    $.ajax({
        url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=11',
        type: "post",
        async: false,
        data : {valueOption : valor},
        success: function(data) {
            $("#valueOption").empty();
            $("#valueOption").append(data);
        },
        error: function() {
            console.log("error");
        }
    });
}

function callFormOptionDateTime(Elemento) {
    $.ajax({
        url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion='+Elemento,
        type: "post",
        async: false,
        success: function(data) {
            $("#dateTime").empty();
            $("#dateTime").append(data);
        },
        error: function() {
            console.log("error");
        }
    });
}

function getTitleData() {
    $.ajax({
        url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=15',
        type: "post",
        async: false,
        success: function(data) {
            $("#activityTitleSpace").empty();
            $("#activityTitleSpace").append(data);
        },
        error: function() {
            console.log("error");
        }
    });
}

function InsertNewActivity() {
    formData = $("#newActivityForm").serializeArray();
    if(validateNewActivytiForm(formData))
    {
        $.ajax({
        url: '../satt_standa/actdes/ajax_activi_desarr.php?opcion=14',
        dataType: 'json',
        type: "post",
        data: {NewformData : formData},
        success: function(data) {
           if(data.status == 200)
           {
            $('#newActiviModal').modal('hide');
            Swal.fire({
                title: 'Registrado!',
                text: "Nueva actividad registrada con exito!",
                type: 'success',
                confirmButtonColor: '#336600'
            }).then((result) => {
                if (result.value) {
                    selectTable(globalThis.filterType);
                }
            })
           }else{
            Swal.fire({
                title: 'Error!',
                text: 'Ha sucedido un error, por favor intente de nuevo',
                type: 'error',
                confirmButtonColor: '#336600'
            });
           }
        }
        });
    }
}

function validateNewActivytiForm(formData)
{
    pass = true;
    let today = new Date().toISOString().slice(0, 10)


    let m = formData.some(function(item) {
        return item.name == 'RadioOptions';
      });
      if(!m)
      {
          Swal.fire({
              title: 'Error!',
              text: 'El campo tipo actividad es obligatorio, por favor intente de nuevo',
              type: 'error',
              confirmButtonColor: '#336600'
          });
          pass =  false;
      }  
      
    formData.forEach(element => {
       
        if(element.name != 'user' && element.name != 'novedad')
        {
            if(element.value == 0 || element.value == '')
            {
                Swal.fire({
                    title: 'Error!',
                    text: 'El campo '+element.name+' es obligatorio, por favor intente de nuevo',
                    type: 'error',
                    confirmButtonColor: '#336600'
                });
                pass =  false;
            }
            if(element.name == 'InitDate')
            {
                globalThis.intiDate = element.value;
            }
            if(element.name == 'FinishDate')
            {
                globalThis.lastDate = element.value;
            }
            if(element.name == 'InitHour')
            {
                globalThis.intiHour = element.value;
            }
            if(element.name == 'FinishHour')
            {
                globalThis.lastHour = element.value;
            }
            if(element.name == 'frecuencySelect')
            {
                globalThis.frecuencySelect = element.value;
            }
            if(globalThis.intiDate > globalThis.lastDate && globalThis.frecuencySelect != '1')
            {
                Swal.fire({
                    title: 'Error!',
                    text: 'El campo fecha inicial debe ser menor al campo fecha final, por favor intente de nuevo',
                    type: 'error',
                    confirmButtonColor: '#336600'
                });
                pass =  false;
            }
            if(globalThis.intiDate < today || globalThis.lastDate < today)
            {
                Swal.fire({
                    title: 'Error!',
                    text: 'Los campos de fechas deben ser igual o mayores a la fecha actual, por favor intente de nuevo',
                    type: 'error',
                    confirmButtonColor: '#336600'
                });
                pass =  false;
            }

            let initDateComplete = moment(globalThis.intiDate+ " " + globalThis.intiHour);
            let finiDateComplete = moment(globalThis.lastDate+ " " + globalThis.lastHour);
            if(initDateComplete > finiDateComplete)
            {
                Swal.fire({
                    title: 'Error!',
                    text: 'El campo hora inicial debe ser menor al campo hora final, por favor intente de nuevo',
                    type: 'error',
                    confirmButtonColor: '#336600'
                });
                pass =  false;
            }
        }
    });

    return pass;
}

function timerColor(endTime)
{
    if(endTime <= 30)
    {
        val = 'FFFF66';
    }
    if(endTime > 30 && endTime < 61)
    {
        val = 'FF9900';
    }
    if(endTime > 60 && endTime < 91)
    {
        val = 'FF0000';
    }
    if(endTime > 90)
    {
        val = 'CC33FF';
    }
    console.log('color',val);

    return val;

}