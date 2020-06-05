

//Create tr
function createTr(row, tipoInforme){
    switch (tipoInforme) {
      case 'gen':
        return rowGeneral(row);
        break;
      case 'esp':
        return rowEspecifico(row);
        break;
      case 'mod':
        return rowModal(row);
        break;  
      default:
        return rowGeneral(row);
        break;
    }
}

//Create tr rowGeneral
function rowGeneral(row){
//Create Elements
    //Tr
    var tr = $(`<tr>
        <td class='can_totalx' style='text-align: center;'>`+row["nom_remdes"]+`</td>
        <td class='can_totalx' style='text-align: center;color:#0646DA; cursor: pointer;font-weight: bold;' onclick="llenarModal(1,0,'`+row["cod_client"]+`',0)">`+row["reg_genera"]+`</td>
        <td class='can_totalx' style='text-align: center;color:#0646DA; cursor: pointer;font-weight: bold;' onclick="llenarModal(1,0,'`+row["cod_client"]+`',1)">`+row["des_finali"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["est_retorn"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_est_retorn"]+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["est_pendie"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_est_penret"]+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["est_entreg"]+`</td>
    </tr>"`);

    return tr;
}

//Create tr rowEspecifico por dia
function rowEspecifico(row){

//Create Elements
    //Tr
    var tr = $(`<tr>
        <td class='id' style='text-align: center;'>`+row["fec_despac"]+`</td>
        <td class='can_totalx' style='text-align: center;color:#0646DA; cursor: pointer;font-weight: bold;' onclick="llenarModal(2,'`+row["fec_despac"]+`',0,0)" >`+row["reg_genera"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["est_retorn"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_est_retorn"]+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["est_pendie"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_est_penret"]+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["est_entreg"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_est_entreg"]+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["est_pendie"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_est_penret"]+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["est_entreg"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_est_entreg"]+`%</td>
    </tr>"`);

    return tr;
}

//Create tr en la tabla modal
function rowModal(row){
    //Create Elements
    //Tr
    var tr = $(`<tr>
        <td class='id' style='text-align: center;'>`+row["num_despac"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["cod_manifi"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["num_pedido"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["ciu_origen"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["ciu_destin"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["num_placax"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["nom_conduc"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["nom_transp"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["nom_client"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["tip_pedido"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["nom_produc"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["can_pedida"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["pes_pedida"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["est_salida"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["est_retorn"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["num_estbue"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["num_estmal"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["tot_saldox"]+`</td>
        
    </tr>"`);
    return tr;
}

function executeFilter(){
    try {
        //Get data
        $.ajax({
            url: '../satt_standa/despac/ajax_gestio_asicar.php?opcion=1&tipoInforme=gen',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize(),
            beforeSend: function(){
                loadAjax("start")
            },
            success: function(data){
                var fecha_inicio=$("#fec_inicio").val();
                var fecha_finalx=$("#fec_finxxx").val();
                $("#text_general_fec").html("INDICADOR DE SOLICITUDES DEL PERIODO "+fecha_inicio+" AL "+fecha_finalx);  
                table_general=$("#tabla_inf_general tbody");
                $("#tabla_inf_general resultado_info_general").remove();
                table_general.empty();
                for(var i=0; i < data.length; i++){  
                table_general.append(createTr(data[i], 'gen'));
                }
                vaciarExcelGeneral();
                descargarExcelGeneral();
                informeEspecifico();

                //Validate empty
                if(objectLength(data) == 0){
                    personalizedAlert("danger", "No se encontraron registros.", "No se ha encontrado ning&uacute;n registro, con el filtro especificado, por favor val&iacute;delo.", true, $("#dashBoardTableTrans"));
                    return false;
                }

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

//llena los datos por dia segun las fechas solicitadas en el informe
function informeEspecifico(){
    try {
        //Get data
        $.ajax({
            url: '../satt_standa/despac/ajax_despac_estibas.php?opcion=1&tipoInforme=esp',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize(),
            beforeSend: function(){
                loadAjax("start")
            },
            success: function(data){
                table_especifica=$("#tabla_inf_especifico tbody");
                table_especifica.empty();
                for(var i=0; i < data.length; i++){       
                    table_especifica.append(createTr(data[i], 'esp'));
                }

                //Validate empty
                if(objectLength(data) == 0){
                    personalizedAlert("danger", "No se encontraron registros.", "No se ha encontrado ning&uacute;n registro, con el filtro especificado, por favor val&iacute;delo.", true, $("#dashBoardTableTrans"));
                    return false;
                }


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


// llena modal con la de acuerdo con los datos ejecutados en el metodo al dar clic
function llenarModal(ver,fec,cli,tip){
    try {
        //Get data
        urlv='&ver='+ver+'&fec='+fec+'&cli='+cli+'&tip='+tip;
        $.ajax({
            url: '../satt_standa/despac/ajax_despac_estibas.php?opcion=1&tipoInforme=mod'+urlv,
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize(),
            beforeSend: function(){
                loadAjax("start")
            },
            success: function(data){
                console.log(data); 
                tabla_modal=$("#resultado_info_especifico_m");
                tabla_modal.html('');

                for(var i=0; i < data.length; i++){       
                    tabla_modal.append(createTr(data[i],'mod'));
                }
                vaciarExcel();
                descargarExcel();
                $("#modal").modal("show");
                //Validate empty
                if(objectLength(data) == 0){
                    personalizedAlert("danger", "No se encontraron registros.", "No se ha encontrado ning&uacute;n registro, con el filtro especificado, por favor val&iacute;delo.", true, $("#dashBoardTableTrans"));
                    return false;
                }

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

function vaciarExcel(){
    $("#tabla_inf_especifico_m").tableExport().remove();
}

function descargarExcel(){
    $("#tabla_inf_especifico_m").tableExport({
        formats: ["xlsx","csv"], //Tipo de archivos a exportar ("xlsx","txt", "csv", "xls")
        position: 'button',  // Posicion que se muestran los botones puedes ser: (top, bottom)
        bootstrap: true,//Usar lo estilos de css de bootstrap para los botones (true, false)
        fileName: "Informe_estibas",    //Nombre del archivo 
    });
}

function vaciarExcelGeneral(){
    $("#tabla_inf_general").tableExport().remove();
}

function descargarExcelGeneral(){
    $("#tabla_inf_general").tableExport({
        formats: ["xlsx","csv"], //Tipo de archivos a exportar ("xlsx","txt", "csv", "xls")
        position: 'button',  // Posicion que se muestran los botones puedes ser: (top, bottom)
        bootstrap: true,//Usar lo estilos de css de bootstrap para los botones (true, false)
        fileName: "Informe_estibas_general",    //Nombre del archivo 
    });
}
