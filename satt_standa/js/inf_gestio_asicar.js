
$(document).ready(function(){
    executeFilter();
});


//VALIDACIONES FORMULARIOS
function PorGestioValidate(){
    $("#PorGestio").validate({
        rules: {
          val_factur: {
            required:true
          },
          val_cosser: {
            required: true
          }
        },
        messages: {
            val_factur:{
            required: "Por favor ingrese el valor a facturar"
          },
          val_cosser: {
            required: "Por favor ingrese el costo del servicio"
          }
        },
        submitHandler: function(form){
          almacenarDatosPorGestio();
        }
      });
}

    function PorAprobValidate(){
  $("#PorAprobarCliente").validate({
    rules: {
        AproServicio: {
        required:true
      },
      costAproxServicio: {
        required: true
      }
    },
    messages: {
        AproServicio:{
        required: "Por favor Seleccione una opción"
      },
      costAproxServicio: {
        required: "Por favor ingrese el costo aproximado del servicio"
      }
    },
    submitHandler: function(form){
        almacenarDatosPorAprobCliente();
    }
});
}

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
function loadAjax(x){
    try {
        if(x == "start"){
            $.blockUI({ message: '<div>Espere un momento</div>' });
        }else{
            $.unblockUI();
        }
    } catch (error) {
        console.log(error);
    }
    
  }

function changeTitleModal(title){
    $("#title-modal").empty();
    $("#title-modal").append(title);
}
  
//Create tr rowGeneral
function rowGeneral(row){
//Create Elements
    //Tr
    var tr = $(`<tr>
        <td class='can_totalx' style='text-align: center;'>`+row["total"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_gestio"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+calcularPorcentaje(row["total"],row["por_gestio"])+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_aprcli"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+calcularPorcentaje(row["total"],row["por_aprcli"])+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_asipro"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+calcularPorcentaje(row["total"],row["por_asipro"])+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["enx_proces"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+calcularPorcentaje(row["total"],row["enx_proces"])+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["xxx_finali"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+calcularPorcentaje(row["total"],row["xxx_finali"])+`%</td>
    </tr>"`);

    return tr;
}

//Create tr rowEspecifico por dia
function rowEspecifico(row){

//Create Elements
    //Tr
    var tr = $(`<tr>
        <td class='can_totalx' style='text-align: center;'>`+row["abr_tercer"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["total"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_gestio"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+calcularPorcentaje(row["total"],row["por_gestio"])+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_aprcli"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+calcularPorcentaje(row["total"],row["por_aprcli"])+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["por_asipro"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+calcularPorcentaje(row["total"],row["por_asipro"])+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["enx_proces"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+calcularPorcentaje(row["total"],row["enx_proces"])+`%</td>
        <td class='can_totalx' style='text-align: center;'>`+row["xxx_finali"]+`</td>
        <td class='can_totalx' style='text-align: center;'>`+calcularPorcentaje(row["total"],row["xxx_finali"])+`%</td>
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
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=1&tipoInforme=gen',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize(),
            beforeSend: function(){
                loadAjax("start");
            },
            success: function(data){
                var fecha_inicio=$("#fec_inicio").val();
                var fecha_finalx=$("#fec_finxxx").val();
                $("#text_general_fec").html("<center>INDICADOR DE SOLICITUDES DEL PERIODO "+fecha_inicio+" AL "+fecha_finalx+"</center>");  
                table_general=$("#tabla_inf_general tbody");
                $("#tabla_inf_general resultado_info_general").remove();
                table_general.empty();
                for(var i=0; i < data.length; i++){  
                table_general.append(createTr(data[i], 'gen'));
                }
                informeEspecifico();
                informePorGestionar(1);
                informePorGestionar(2);
                informePorGestionar(3);
                informePorGestionar(4);
                informePorGestionar(5);
                //Validate empty
                /*if(objectLength(data) == 0){
                    personalizedAlert("danger", "No se encontraron registros.", "No se ha encontrado ning&uacute;n registro, con el filtro especificado, por favor val&iacute;delo.", true, $("#dashBoardTableTrans"));
                    return false;
                }*/

            },
            complete: function(){
                loadAjax("end");
            },
            error: function(jqXHR, exception){
                errorAjax(jqXHR, exception, "Error al cargar el campo.", "", "alert")
            }
        });
    } catch (error) {
        console.log(error);
    }   
}

//Trae los datos del informe especifico
function informeEspecifico(){
    try {
        //Get data
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=1&tipoInforme=esp',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize(),
            success: function(data){
                table_especifica=$("#tabla_inf_especifico tbody");
                table_especifica.empty();
                for(var i=0; i < data.length; i++){       
                    table_especifica.append(createTr(data[i], 'esp'));
                }

                //Validate empty
                /*if(objectLength(data) == 0){
                    personalizedAlert("danger", "No se encontraron registros.", "No se ha encontrado ning&uacute;n registro, con el filtro especificado, por favor val&iacute;delo.", true, $("#dashBoardTableTrans"));
                    return false;
                }*/
            }
        });
    } catch (error) {
        console.log(error);
    }  
}

function nomTablaSuperior(code){
    if(code==1){
        return "tabla_inf_porGestionar";
    }else if(code==2){
        return "tabla_inf_porAproCliente";
    }else if(code==3){
        return "tabla_inf_AsignacionAPro";
    }else if(code==4){
        return "tabla_inf_EnProceso";
    }else if(code==5){
        return "tabla_inf_Finalizados";
    }
}

function nomTablaInferior(code){
    if(code==1){
        return "resultado_porGestionar";
    }else if(code==2){
        return "resultado_info_AproCliente";
    }else if(code==3){
        return "resultado_info_AsignacionAPro";
    }else if(code==4){
        return "resultado_info_EnProceso";
    }else if(code==5){
        return "resultado_info_Finalizados";
    }
}
//Trae los datos del informe por gestionar
function informePorGestionar(code){
    try {
        //Get data
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=1&tipoInforme=porGest',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize()+"&code="+code+"&tabla="+nomTablaInferior(code),
            async: false,
            success: function(data){
                var nombre_tablaSup=nomTablaSuperior(code);
                table_especifica=$("#"+nombre_tablaSup);
                table_especifica.empty();
                table_especifica.append(data);
            },
            error: function(){
                console.log("error");
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function resultaIndiviua(cod_asiste,cod_client,code){
    try {
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=1&tipoInforme=porGestInvidu',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize() + "&cod_client=" + cod_client + "&cod_asiste=" + cod_asiste+"&code="+code+"&tabla="+nomTablaInferior(code),
            async: false,
            success: function(data){
                var nombre_tablaSup=nomTablaSuperior(code);
                tablaPorGestio=$("#"+nombre_tablaSup);
                tablaPorGestio.empty();
                tablaPorGestio.append(data);
            },
            error: function(){
                console.log("error");
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function abrModalPorGestio(cod_solici,cod_estado){
    try {
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=2',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize() + "&cod_solici=" + cod_solici,
            async: false,
            success: function(data){
                llenarCamposModal(data[0]);
                llenarFinalTipoSolici(cod_solici,data[0]['tip_solici']);
                llenarFormularioSegunEstado(cod_estado,cod_solici);
                $("#cod_soliciID").val(cod_solici);
                llenarBitacora(cod_solici);

            },
            error: function(){
                console.log("error");
            }
        });
        $("#PorGestioModal").modal("show");
        changeTitleModal('Gestion de solicitud No. '+cod_solici);
    } catch (error) {
        console.log(error);
    }
}

function llenarCamposModal(data){
    $("#tip_soliciID").val(data['nom_asiste']);
    $("#nom_soliciID").val(data['nom_solici']);
    $("#ema_soliciID").val(data['cor_solici']);
    $("#tel_soliciID").val(data['tel_solici']);
    $("#cel_soliciID").val(data['cel_solici']);
    $("#nom_aseguraID").val(data['ase_solici']);
    $("#nom_polizaID").val(data['num_poliza']);

    $("#num_transpID").val(data['num_transp']);
    $("#nom_transpID").val(data['nom_transp']);
    $("#ap1_transpID").val(data['ap1_transp']);
    $("#ap2_transpID").val(data['ap2_transp']);
    $("#ce1_transpID").val(data['ce1_transp']);
    $("#ce2_transpID").val(data['ce2_transp']);

    $("#num_placaID").val(data['num_placax']);
    $("#nom_marcaxID").val(data['mar_vehicu']);
    $("#nom_colorxID").val(data['col_vehicu']);
    $("#tip_transpID").val(data['tip_vehicu']);
    $("#num_remolqID").val(data['num_remolq']);
}

function llenarFinalTipoSolici(cod_solici,tip_solici){
    try {
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=3',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize() + "&cod_solici=" + cod_solici + "&tip_solici=" + tip_solici,
            async: false,
            success: function(data){
                $("#con-formul").empty();
                $("#con-formul").append(data);
            },
            error: function(){
                console.log("error");
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function llenarFormularioSegunEstado(cod_estado,cod_solici){
    try {
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=6',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize() + "&cod_estado=" + cod_estado + "&cod_solici=" + cod_solici,
            async: false,
            success: function(data){
                $("#formul-estado").empty();
                $("#formul-estado").append(data);
            },
            error: function(){
                console.log("error");
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function llenarBitacora(cod_solici){
    try {
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=4',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize() + "&cod_solici=" + cod_solici ,
            async: false,
            success: function(data){
                $("#bitacoRespuesta").empty();
                $("#bitacoRespuesta").append(data);
            },
            error: function(){
                console.log("error");
            }
        });
    } catch (error) {
        console.log(error);
    }
}



function calcularPorcentaje(total,cantidad){
    porcentaje =  parseInt(cantidad)/parseInt(total)*100;
    return parseInt(porcentaje);
}


function llenarRetabilidad(){
    val_facturar = $("#val_facturID").val();
    cos_servicio = $("#val_cosserID").val();

    if(val_facturar!="" && cos_servicio!=""){
        total = (parseInt(val_facturar)/parseInt(cos_servicio))*100;
        total = Math.round(total);
        if(total!=undefined){
            $("#val_rentabID").val(total+"%");
        }
    }
}

function razonFinali(){
    if ($('#verFinSolici').prop('checked') ) {
        $("#rzn-fin").empty();
        $("#rzn-fin").append(`
        <div class="row mt-4">
            <div class="offset-5 col-6">
                <textarea class="form-control border border-danger" id="raz_finaliID" name="raz_finali" rows="2" placeholder="Especifique la razon" required></textarea>
            </div>
        </div>`);
    }else{
        $("#rzn-fin").empty();
    }
}

function almacenarDatosPorGestio(){
    var standa = 'satt_standa';
    var dataString = 'opcion=5';
    var File=$('#adjuntoFileID')[0].files[0];
    var data = new FormData();
    data.append('file', File);
    data.append('obs_gestio',$("#obs_gestioID").val());
    data.append('val_factur',$("#val_facturID").val());
    data.append('val_cosser',$("#val_cosserID").val());
    data.append('cod_solici',$("#cod_soliciID").val());
    data.append('tipoSol',"porGestio");
    if($("#raz_finaliID").length){
        data.append('raz_finali',$("#raz_finaliID").val());
    }

    
  $.ajax({
            url: "../" + standa + "/asicar/ajax_gestio_asicar.php?"+dataString,
            method: 'POST',
            data,
            async: false,
            dataType: "json",
            contentType: false,
            processData: false,
            beforeSend: function(){
                loadAjax("start")
            },
            success: function(data) {
              if(data['status'] == 200){
                $('#PorGestioModal').modal('hide');
                Swal.fire({
                  title:'Registrado!',
                  text:  data['response'],
                  type: 'success',
                  confirmButtonColor: '#336600'
                }).then((result) => {
                    if (result.value) {
                        executeFilter();
                    }
                })
            }else{
                Swal.fire({
                  title:'Error!',
                  text:  data['response'],
                  type: 'error',
                  confirmButtonColor: '#336600'
                })
            }
            },
            complete: function(){
                loadAjax("end")
            },
        });
}

function almacenarDatosPorAprobCliente(){
    var standa = 'satt_standa';
    var dataString = 'opcion=5';
    var File=$('#adjuntoFileID')[0].files[0];
    var data = new FormData();
    data.append('file', File);
    data.append('apr_servic',$('input:radio[name=AproServicio]:checked').val());
    data.append('obs_aprser',$("#obs_aprserID").val());
    data.append('cos_aprser',$("#costAproxServicioID").val());
    data.append('cod_solici',$("#cod_soliciID").val());
    data.append('tipoSol',"porAprobCliente");
    if($("#raz_finaliID").length){
        data.append('raz_finali',$("#raz_finaliID").val());
    }

  $.ajax({
            url: "../" + standa + "/asicar/ajax_gestio_asicar.php?"+dataString,
            method: 'POST',
            data,
            async: false,
            dataType: "json",
            contentType: false,
            processData: false,
            beforeSend: function(){
                loadAjax("start")
            },
            success: function(data) {
              if(data['status'] == 200){
                $('#PorGestioModal').modal('hide');
                Swal.fire({
                  title:'Registrado!',
                  text:  data['response'],
                  type: 'success',
                  confirmButtonColor: '#336600'
                }).then((result) => {
                    if (result.value) {
                        executeFilter();
                    }
                })
            }else{
                Swal.fire({
                  title:'Error!',
                  text:  data['response'],
                  type: 'error',
                  confirmButtonColor: '#336600'
                })
            }
            },
            complete: function(){
                loadAjax("end")
            },
        });
}
