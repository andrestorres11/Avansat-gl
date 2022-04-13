$(document).ready(function() {
    executeFilter();
});

//VALIDACIONES FORMULARIOS
function PorGestioValidate() {
    $("#PorGestio").validate({
        rules: {
            val_factur: {
                required: true
            },
            val_cosser: {
                required: true
            }
        },
        messages: {
            val_factur: {
                required: "Por favor ingrese el valor a facturar"
            },
            val_cosser: {
                required: "Por favor ingrese el costo del servicio"
            }
        },
        submitHandler: function(form) {
            almacenarDatosPorGestio();
        }
    });
}

function PorAprobValidate() {
    $("#porAprobCliente").validate({
        rules: {
            AproServicio: {
                required: true
            }
        },
        messages: {
            AproServicio: {
                required: "Por favor Seleccione una opción"
            }
        },
        submitHandler: function(form) {
            almacenarDatosPorAprobCliente();
        }
    });
}

function PorAsigProveedor() {
    var cod_provee = $("#num_docproID").val();
    if (validaProveedor(cod_provee)) {
        $("#PorAsignProveedor").validate({
            rules: {
                num_docpro: {
                    required: true
                }
            },
            messages: {
                num_docpro: {
                    required: "Por favor seleccione el proveedor"
                }
            },
            submitHandler: function(form) {
                almacenarDatosPorAsigProveedor();
            }
        });
    } else {
        alert('No se ha encontrado un proveedor');
    }
}

//Create tr
function createTr(row, tipoInforme) {
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

function changeTitleModal(title, numsolicitud = null) {
    $("#title-modal").empty();
    $("#idnumservicio").val(numsolicitud);
    $("#title-modal").append(title);
}

//Create tr rowGeneral
function rowGeneral(row) {
    //Create Elements
    //Tr
    var tr = $(`<tr>
        <td class='can_totalx' style='text-align: center;'>` + row["total"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["por_gestio"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["por_gestio"]) + `%</td>
        <td class='can_totalx' style='text-align: center;'>` + row["por_aprcli"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["por_aprcli"]) + `%</td>
        <td class='can_totalx' style='text-align: center;'>` + row["por_asipro"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["por_asipro"]) + `%</td>
        <td class='can_totalx' style='text-align: center;'>` + row["enx_proces"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["enx_proces"]) + `%</td>
        <td class='can_totalx' style='text-align: center;'>` + row["xxx_finali"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["xxx_finali"]) + `%</td>
        <td class='can_totalx' style='text-align: center;'>` + row["est_cancel"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["est_cancel"]) + `%</td>
    </tr>"`);

    return tr;
}

//Create tr rowEspecifico por dia
function rowEspecifico(row) {

    //Create Elements
    //Tr
    var tr = $(`<tr>
        <td class='can_totalx' style='text-align: center;'>` + row["abr_tercer"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["total"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["por_gestio"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["por_gestio"]) + `%</td>
        <td class='can_totalx' style='text-align: center;'>` + row["por_aprcli"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["por_aprcli"]) + `%</td>
        <td class='can_totalx' style='text-align: center;'>` + row["por_asipro"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["por_asipro"]) + `%</td>
        <td class='can_totalx' style='text-align: center;'>` + row["enx_proces"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["enx_proces"]) + `%</td>
        <td class='can_totalx' style='text-align: center;'>` + row["xxx_finali"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["xxx_finali"]) + `%</td>
        <td class='can_totalx' style='text-align: center;'>` + row["est_cancel"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + calcularPorcentaje(row["total"], row["est_cancel"]) + `%</td>
    </tr>"`);

    return tr;
}

//Create tr en la tabla modal
function rowModal(row) {
    //Create Elements
    //Tr
    var tr = $(`<tr>
        <td class='id' style='text-align: center;'>` + row["num_despac"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["cod_manifi"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["num_pedido"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["ciu_origen"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["ciu_destin"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["num_placax"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_conduc"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_transp"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_client"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["tip_pedido"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["nom_produc"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["can_pedida"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["pes_pedida"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["est_salida"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["est_retorn"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["num_estbue"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["num_estmal"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["tot_saldox"] + `</td>
        
    </tr>"`);
    return tr;
}

function executeFilter() {
    try {
        //Get data
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=1&tipoInforme=gen',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize(),
            beforeSend: function() {
                loadAjax("start");
            },
            success: function(data) {
                var fecha_inicio = $("#fec_inicio").val();
                var fecha_finalx = $("#fec_finxxx").val();
                $("#text_general_fec").html("<center>INDICADOR DE SOLICITUDES DEL PERIODO " + fecha_inicio + " AL " + fecha_finalx + "</center>");
                table_general = $("#tabla_inf_general tbody");
                $("#tabla_inf_general resultado_info_general").remove();
                table_general.empty();
                for (var i = 0; i < data.length; i++) {
                    table_general.append(createTr(data[i], 'gen'));
                }
                informeEspecifico();
                informePorGestionar(1);
                informePorGestionar(2);
                informePorGestionar(3);
                informePorGestionar(4);
                informePorGestionar(5);
                informePorGestionar(6);
                //Validate empty
                /*if(objectLength(data) == 0){
                    personalizedAlert("danger", "No se encontraron registros.", "No se ha encontrado ningun registro, con el filtro especificado, por favor val&iacute;delo.", true, $("#dashBoardTableTrans"));
                    return false;
                }*/

            },
            complete: function() {
                loadAjax("end");
            },
            error: function(jqXHR, exception) {
                errorAjax(jqXHR, exception, "Error al cargar el campo.", "", "alert")
            }
        });
    } catch (error) {
        console.log(error);
    }
}

//Trae los datos del informe especifico
function informeEspecifico() {
    try {
        //Get data
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=1&tipoInforme=esp',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize(),
            success: function(data) {
                table_especifica = $("#tabla_inf_especifico tbody");
                table_especifica.empty();
                for (var i = 0; i < data.length; i++) {
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

function nomTablaSuperior(code) {
    if (code == 1) {
        return "tabla_inf_porGestionar";
    } else if (code == 2) {
        return "tabla_inf_porAproCliente";
    } else if (code == 3) {
        return "tabla_inf_AsignacionAPro";
    } else if (code == 4) {
        return "tabla_inf_EnProceso";
    } else if (code == 5) {
        return "tabla_inf_Finalizados";
    } else if (code == 6) {
        return "tabla_inf_Canceladas";
    }
}

function nomTablaInferior(code) {
    if (code == 1) {
        return "resultado_porGestionar";
    } else if (code == 2) {
        return "resultado_info_AproCliente";
    } else if (code == 3) {
        return "resultado_info_AsignacionAPro";
    } else if (code == 4) {
        return "resultado_info_EnProceso";
    } else if (code == 5) {
        return "resultado_info_Finalizados";
    } else if (code == 6) {
        return "resultado_info_Canceladas";
    }
}
//Trae los datos del informe por gestionar
function informePorGestionar(code) {
    try {
        //Get data
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=1&tipoInforme=porGest',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize() + "&code=" + code + "&tabla=" + nomTablaInferior(code),
            async: false,
            success: function(data) {
                var nombre_tablaSup = nomTablaSuperior(code);
                table_especifica = $("#" + nombre_tablaSup);
                console.log(nombre_tablaSup);
                table_especifica.empty();
                table_especifica.append(data);
            },
            error: function() {
                console.log("error");
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function resultaIndiviua(cod_asiste, cod_client, code) {
    try {
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=1&tipoInforme=porGestInvidu',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize() + "&cod_client=" + cod_client + "&cod_asiste=" + cod_asiste + "&code=" + code + "&tabla=" + nomTablaInferior(code),
            async: false,
            success: function(data) {
                var nombre_tablaSup = nomTablaSuperior(code);
                tablaPorGestio = $("#" + nombre_tablaSup);
                tablaPorGestio.empty();
                tablaPorGestio.append(data);
            },
            error: function() {
                console.log("error");
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function abrModalPorGestio(cod_solici, cod_estado) {
    try {
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=2',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize() + "&cod_solici=" + cod_solici,
            async: false,
            success: function(data) {
                llenarCamposModal(data[0]);
                llenarFinalTipoSolici(cod_solici, data[0]['tip_solici']);
                llenarFormularioSegunEstado(cod_estado, cod_solici);
                $("#cod_soliciID").val(cod_solici);
                llenarBitacora(cod_solici);

            },
            error: function() {
                console.log("error");
            }
        });
        $("#PorGestioModal").modal("show");
        changeTitleModal('Gestion de solicitud No. ' + cod_solici, cod_solici);
    } catch (error) {
        console.log(error);
    }
}

function llenarCamposModal(data) {
    $("#tip_soliciID").val(data['nom_asiste']);
    $("#nom_soliciID").val(data['nom_solici']);
    $("#ema_soliciID").val(data['cor_solici']);
    $("#tel_soliciID").val(data['tel_solici']);
    $("#cel_soliciID").val(data['cel_solici']);
    $("#nom_aseguraID").val(data['nom_asegura']);
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

function llenarFinalTipoSolici(cod_solici, tip_solici) {
    try {
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=3',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize() + "&cod_solici=" + cod_solici + "&tip_solici=" + tip_solici,
            async: false,
            success: function(data) {
                $("#con-formul").empty();
                $("#con-formul").append(data);
            },
            error: function() {
                console.log("error");
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function llenarFormularioSegunEstado(cod_estado, cod_solici) {
    try {
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=6',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize() + "&cod_estado=" + cod_estado + "&cod_solici=" + cod_solici,
            async: false,
            success: function(data) {
                $("#formul-estado").empty();
                $("#formul-estado").append(data);
            },
            error: function() {
                console.log("error");
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function llenarBitacora(cod_solici) {
    try {
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=4',
            dataType: 'json',
            type: "post",
            data: $("#filter").serialize() + "&cod_solici=" + cod_solici,
            async: false,
            success: function(data) {
                $("#bitacoRespuesta").empty();
                $("#bitacoRespuesta").append(data);
            },
            error: function() {
                console.log("error");
            }
        });
    } catch (error) {
        console.log(error);
    }
}



function calcularPorcentaje(total, cantidad) {
    porcentaje = parseInt(cantidad) / parseInt(total) * 100;
    return parseInt(porcentaje);
}


function llenarRetabilidad() {
    val_facturar = $("#val_facturID").val().replace('$', '').replace(/(^\s+|\s+$)/g, '');
    cos_servicio = $("#val_cosserID").val().replace('$', '').replace(/(^\s+|\s+$)/g, '');
    updateCostoProveedor(cos_servicio);
    if (val_facturar != "" && cos_servicio != "") {
        total1 = parseInt(val_facturar) - parseInt(cos_servicio);
        total = (parseInt(total1) / parseInt(val_facturar)) * 100;
        total = Math.round(total);
        if (total != undefined) {
            $("#val_rentabID").val(total + "%");
        }
    }
}

function razonFinali() {
    if ($('#verFinSolici').prop('checked')) {
        $("#rzn-fin").empty();
        $("#rzn-fin").append(`
        <div class="row mt-4">
            <div class="offset-5 col-6">
                <textarea class="form-control border border-danger" id="raz_finaliID" name="raz_finali" rows="2" placeholder="Especifique la razon" required></textarea>
            </div>
        </div>`);
    } else {
        $("#rzn-fin").empty();
    }
}

function buscarProveedor() {
    var doc_proove = $("#num_docproID").val();
    try {
        //Get data
        data = {
            doc_proove
        }
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=14',
            dataType: 'json',
            type: "post",
            data,
            async: false,
            success: function(data) {
                if (data['status'] == 200) {
                    $("#nom_proveeID").val(data['nom_contra']);
                    $("#ap1_proveeID").val(data['pri_apelli']);
                    $("#ap2_proveeID").val(data['seg_apelli']);
                    $("#num_proveeID").val(data['num_celula']);
                    $("#cor_proveeID").val(data['dir_emailx']);
                }
            }
        });
    } catch (error) {
        console.log(error);
    }
}

function almacenarDatosPorGestio() {
    var standa = 'satt_standa';
    var dataString = 'opcion=5';
    var File = $('#adjuntoFileID')[0].files[0];
    var data = new FormData();
    data.append('file', File);
    data.append('obs_gestio', $("#obs_gestioID").val());
    data.append('val_factur', $("#val_facturID").val());
    data.append('val_cosser', $("#val_cosserID").val());
    data.append('cod_solici', $("#cod_soliciID").val());
    data.append('tipoSol', "porGestio");
    if ($("#raz_finaliID").length) {
        data.append('raz_finali', $("#raz_finaliID").val());
    }


    $.ajax({
        url: "../" + standa + "/asicar/ajax_gestio_asicar.php?" + dataString,
        method: 'POST',
        data,
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            loadAjax("start")
        },
        success: function(data) {
            if (data['status'] == 200) {
                $('#PorGestioModal').modal('hide');
                Swal.fire({
                    title: 'Registrado!',
                    text: data['response'],
                    type: 'success',
                    confirmButtonColor: '#336600'
                }).then((result) => {
                    if (result.value) {
                        executeFilter();
                    }
                })
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data['response'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                })
            }
        },
        complete: function() {
            loadAjax("end")
        },
    });
}

function almacenarDatosPorAprobCliente() {
    var standa = 'satt_standa';
    var dataString = 'opcion=5';
    var File = $('#adjuntoFileID')[0].files[0];
    var data = new FormData();
    data.append('file', File);
    data.append('apr_servic', $('input:radio[name=AproServicio]:checked').val());
    data.append('obs_aprser', $("#obs_aprserID").val());
    data.append('cos_aprser', $("#costAproxServicioID").val());
    data.append('cod_solici', $("#cod_soliciID").val());
    data.append('tipoSol', "porAprobCliente");
    if ($("#raz_finaliID").length) {
        data.append('raz_finali', $("#raz_finaliID").val());
    }

    $.ajax({
        url: "../" + standa + "/asicar/ajax_gestio_asicar.php?" + dataString,
        method: 'POST',
        data,
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            loadAjax("start")
        },
        success: function(data) {
            if (data['status'] == 200) {
                $('#PorGestioModal').modal('hide');
                Swal.fire({
                    title: 'Registrado!',
                    text: data['response'],
                    type: 'success',
                    confirmButtonColor: '#336600'
                }).then((result) => {
                    if (result.value) {
                        executeFilter();
                    }
                })
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data['response'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                })
            }
        },
        complete: function() {
            loadAjax("end")
        },
    });
}


function almacenarDatosPorAsigProveedor() {
    var standa = 'satt_standa';
    var dataString = 'opcion=5';
    var File = $('#adjuntoFileID')[0].files[0];
    var data = new FormData();
    data.append('file', File);
    data.append('cod_provee', $('#num_docproID').val());
    data.append('obs_asipro', $("#obs_asiproID").val());
    data.append('cod_solici', $("#cod_soliciID").val());
    data.append('tipoSol', "porAsignProveedor");
    if ($("#raz_finaliID").length) {
        data.append('raz_finali', $("#raz_finaliID").val());
    }

    $.ajax({
        url: "../" + standa + "/asicar/ajax_gestio_asicar.php?" + dataString,
        method: 'POST',
        data,
        async: false,
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function() {
            loadAjax("start")
        },
        success: function(data) {
            if (data['status'] == 200) {
                $('#PorGestioModal').modal('hide');
                Swal.fire({
                    title: 'Registrado!',
                    text: data['response'],
                    type: 'success',
                    confirmButtonColor: '#336600'
                }).then((result) => {
                    if (result.value) {
                        executeFilter();
                    }
                })
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data['response'],
                    type: 'error',
                    confirmButtonColor: '#336600'
                })
            }
        },
        complete: function() {
            loadAjax("end")
        },
    });
}

function validaProveedor(cod_provee) {
    var validator = false;
    try {
        //Get data
        data = {
            cod_provee
        }
        $.ajax({
            url: '../satt_standa/asicar/ajax_gestio_asicar.php?opcion=16',
            dataType: 'json',
            type: "post",
            data,
            async: false,
            success: function(data) {
                if (data['status']) {
                    validator = true;
                }
            }
        });
    } catch (error) {
        console.log(error);
    }
    return validator;
}

function busquedaProveedor(campo) {
    var standa = 'satt_standa';
    var key = $(campo).val();
    var opcion = '14';
    var dataString = 'key=' + key + '&opcion=' + opcion;
    var nameid = $(campo).attr('id');
    $.ajax({
        url: "../" + standa + "/asicar/ajax_gestio_asicar.php",
        method: 'POST',
        data: dataString,
        success: function(data) {
            //Escribimos las sugerencias que nos manda la consulta
            $('#' + nameid + '-suggestions').fadeIn(1000).html(data);
            //Al hacer click en alguna de las sugerencias
            $('.suggest-element').on('click', function() {
                console.log('ejecutando...');
                //Obtenemos la id unica de la sugerencia pulsada
                var id = $(this).attr('id');
                //Editamos el valor del input con data de la sugerencia pulsada
                $(campo).val($('#' + id).attr('data'));
                //Hacemos desaparecer el resto de sugerencias
                $('#' + nameid + '-suggestions').fadeOut(1000);

                traerProveedor(id);

            });
        }
    });
}

function traerProveedor(id) {
    var standa = 'satt_standa';
    var dataString = 'code=' + id + '&opcion=17';
    $.ajax({
        url: "../" + standa + "/asicar/ajax_gestio_asicar.php",
        method: 'POST',
        data: dataString,
        async: false,
        dataType: 'json',
        success: function(data) {
            console.log(data['nom_contra']);
            $("#nom_proveeID").val(data['nom_contra']);
            $("#ap1_proveeID").val(data['pri_apelli']);
            $("#ap2_proveeID").val(data['seg_apelli']);
            $("#num_proveeID").val(data['num_celula']);
            $("#cor_proveeID").val(data['dir_emailx']);
        }
    });
}

function vaciarInput(campo) {
    $(campo).val('');
    $("#nom_proveeID").val('');
    $("#ap1_proveeID").val('');
    $("#ap2_proveeID").val('');
    $("#num_proveeID").val('');
    $("#cor_proveeID").val('');
}

function subirArchivostmp()
    {
        var standa = 'satt_standa';
        
        

            var Form = new FormData($('#filesForm')[0]);
           

        $.ajax({

            url: "../" + standa + "/asicar/ajax_gestio_asicar.php?opcion=18",
            type: "post",
            data : Form,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(data)
            {
                
                console.log(data);
                
                var datarrayarchivos=data;
                var numelementarch=datarrayarchivos.length;
                if(numelementarch==0){
                    Swal.fire("errr",
                    "No hay Archivos Seleccionados",
                    "error");
                }else{
                    Swal.fire("Transaccion Exitosa",
                    "Archivos Agregados",
                    "success");
                };
                for(i=0;i<numelementarch;i++){
                    
                    $("#listararchivos").append("<tr>" +
                    "<td>"+datarrayarchivos[i]['name']+"</td> " + 
                    "<td><textarea id='txtobserv' style=font-size: 12px;' rows='1' placeholder='Observaciones'></textarea></td>" +
                    "<td>" +
                    "<a href='#' class='btn btn-xs btn-danger' style='padding: 0.06rem 0.5rem;' onclick='deleterow()'><span class='fa fa-trash'></span></a>"  +
                     "</td>" +
                     "<td>" +
                     "<input id='archtemp' name='archtemp' type='hidden' value='" + datarrayarchivos[i]['tmp_name'] + "'>" +
                     "</td>" +
                     "</tr>");
                }        
            }
        });

        
        
    }

    function subirArchivos()
    {
        var standa = 'satt_standa';
        var dataString = 'opcion=19';
        var cod_solici= $("#idnumservicio").val();
        var id_estado= $("#id_estado").val();
        var txt_observaciones= $("#txt_observaciones").val();
        var idinicio= $("#idinicio").val();
    if((id_estado!='0') && ($("#txt_observaciones").val()!='') || id_estado =='5'){ 
    
        $.ajax({
            url: "../" + standa + "/asicar/ajax_gestio_asicar.php?" + dataString,
            method: 'POST',
            data: {cod_solici : cod_solici, id_estado : id_estado, txt_observaciones : txt_observaciones, idinicio : idinicio},
            dataType: "json",
            success: function(data)
            {
                console.log(data);
                llenarBitacora(data['solicitud'])
                

                if(data['proximo']=='4'){
                    $('#form_principal').trigger("reset");
                    $('#filesForm').trigger("reset");
                    $("#tbodyid").empty();
                }
                if(data['proximo']=='5'){
                    $("#PorGestioModal").modal('hide')
                    executeFilter();
                
                }
                Swal.fire('Transaccion Exitosa',
                    'Informacion Almacenada',
                    'success');
            
            }

        });   
    }else{


        Swal.fire(
            'Error',
            'Seleccione datos necesarios (Estado, Servicio, Archivos)',
            'error'); 
    
    }
    
    }

    function deleterow(){
        event.target.parentNode.parentNode.parentNode.parentNode.remove();
    }
    



    function getIndex(){
        if($("#id_estado").val()!='0'){
            actdesactserv('mostrar');

        }else{
            actdesactserv('ocultar');
            actdesacttxtobserv('ocultar');
            actdesactbntinput('ocultar');
            mostocultbtn('ocultar');
            mostoculttbl('ocultar');
        }
        
    }

    function actdesactserv(paramserv){
        if(paramserv=='mostrar'){
            $('#id_servicio').show();
            $('#label_servicio').show();
        }else{
            $('#id_servicio').hide();
            $('#label_servicio').hide();
        }
    }

    function getindexservi(){
        if($("#id_servicio").val()!='0'){
            actdesacttxtobserv('mostrar');
            actdesactbntinput('mostrar');
        }else{
            actdesacttxtobserv('ocultar');
            actdesactbntinput('ocultar');
            mostocultbtn('ocultar');
            mostoculttbl('ocultar');
        }
    
    }

    function actdesacttxtobserv(paramobserv){
        if(paramobserv=='mostrar'){
            $('#txt_observaciones').show();
        }else{
            $('#txt_observaciones').hide();
        }

    }

    function actdesactbntinput(paraminputfile){
        if(paraminputfile=='mostrar'){
            $('#archivo').show();
        }else{
            $('#archivo').hide();
        }
    }


    function getindexinput(){
        var contfile=$('#archivo');
        var imagen = document.getElementById("archivo").files;

        if(imagen.length == 0)
        {
            mostocultbtn('ocultar')
            
        }else
        {
            mostocultbtn('mostrar')
        }
        
    }

    function mostocultbtn(parambtn){
        if(parambtn=='ocultar'){
            $("#cargafilebtn").hide();
        }else{
            $("#cargafilebtn").show();
        }
    }

    function mostoculttbl(paramtbl){
        if(paramtbl=='ocultar'){
            $("#listararchivos").hide();
        }else{
            $("#listararchivos").show();
        }
        
    }

    function mostoculbtnenviar(paramtblbtnenv){
        if(paramtblbtnenv=='ocultar'){
            $("#btnenviar").hide();
        }else{
            $("#btnenviar").show();
        }
        
    }

    function subirArchivostmp2()
    {
        var standa = 'satt_standa';
        var data = new FormData($('#filesForm')[0]);
        data.append('cod_solici', $("#idnumservicio").val());
        data.append('id_estado', $("#id_estado").val());
        data.append('id_servicio', $("#id_servicio").val());
        data.append('id_textservi', $("#id_servicio option:selected").text());
        data.append('txt_observaciones', $("#txt_observaciones").val());
        data.append('idinicio', $("#idinicio").val());

        var imagen = document.getElementById("archivo").files;

        if((($("#id_estado").val()!='0') && ($("#id_servicio").val()!='0')  && imagen.length != 0  )){

            $.ajax({

                url: "../" + standa + "/asicar/ajax_gestio_asicar.php?opcion=21",
                type: "post",
                data : data,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(data)
                {
                    console.log(data);
                
                    mostoculttbl('mostrar');
                    mostoculbtnenviar('mostrar');
                    $("#tbodyid").html(data['regtbl']);
                    $("#idinicio").val(data['idinicio']);

                    mostocultbtn('ocultar');
                }

            });

        }else{
            Swal.fire(
                        'Error',
                        'Seleccione datos necesarios (Estado, Servicio, Archivos)',
                        'error');
        }
        


    }

    function datosold(id, value){
        var regid = id.split('-');
      var result=regid[1];
        $('#vrold-'+ result).val(value);
    }

    function actobserv(numreg){
    
      var standa = 'satt_standa'; 
      var regid = numreg.split('-');
      var result=regid[1];
      var oldvalue=$("#vrold-"+result).val();
      var newvalue=$("#"+ numreg).val();

      if(oldvalue != newvalue){

        $.ajax({

        url: "../" + standa + "/asicar/ajax_gestio_asicar.php?opcion=22",
        type: "post",
        data : {regid : result, datnew:newvalue},
        success: function(data)
        {
          console.log(data);
           
        }
    });

      }
    }

    function eliminaregarchi(numdelreg){
    
        var standa = 'satt_standa'; 
        var regid = numdelreg.split('-');
        
        var resultado=regid[1];
        
        var regeliminar=$('#txtfile-'+ resultado).val();
        
        Swal.fire({
            title: 'Esta usted seguro?',
            text: "El registro sera eliminado y No podr?s revertir esto!",
            
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, eliminarlo!'
          }).then((result) => {
            if (result['value']) {
                $.ajax({
                    url: "../" + standa + "/asicar/ajax_gestio_asicar.php?opcion=23",
                    type: "post",
                    data : {regid : resultado, regeliminar:regeliminar},
                    dataType: 'json',
                    success: function(data)
                    {
                    
                    if(data['status'] == 200){
                        $('#fila-' + resultado).remove();
                        Swal.fire(
                            'Eliminado!',
                            'Archivo fue eliminado.',
                            'success'
                        )
                        
                    }else{
                        Swal.fire(
                            'Error!',
                            'No se pudo completar la operacion.',
                            'error'
                        )
                    }
                    
                    }
                });      

            }
            
          })
        
          
    }