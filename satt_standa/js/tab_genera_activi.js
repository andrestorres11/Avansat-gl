$(document).ready(function() { 
    tableHtml();
});

function tableHtml()
{
    $.ajax({
        url: '../satt_standa/actdes/ajax_genera_activi.php?opcion=1',
        type: "post",
        async: false,
        success: function(data) {
            $("#pills-general").empty();
            $("#pills-general").append(data);
            GetDataFromTable();
        },
        error: function() {
            console.log("error");
        }
    });
}

function GetDataFromTable() {
    try {
        //Get data
        $.ajax({
            url: '../satt_standa/actdes/ajax_genera_activi.php?opcion=2',
            dataType: 'json',
            type: "post",
            beforeSend: function() {
                loadAjax("start");
            },
            success: function(data) {

                    table_general = $("#tabla_inf_general tbody");
                    $("#tabla_inf_general resultado_info_general").remove();
                    table_general.empty();
                    for (var i = 0; i < data.length; i++) {
                        table_general.append(rowTable(data[i],i+1));
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
function rowTable(row,i) {
    //Create Elements 
    //Tr
    var tr = $(`<tr>
        <td class='can_totalx' style='text-align: center;'><a href="#" onclick="openEditModal('`+ row["cod_titulo"] +`')">` + i + `</a></td>
        <td class='can_totalx' style='text-align: center;'>` + row["des_titulo"] + `</td>
        <td class='can_totalx' style='text-align: center;'>` + row["est_titulo"] + `</td>
        <td class='can_totalx' style='text-align: center;'><a href="#" onclick="ChageStatus('`+ row["est_titulo"] +`','`+ row["cod_titulo"] +`')">Cambiar Estado</a></td>
    </tr>"`);

    return tr;
}

function openEditModal(cod_titulo) {
    $.ajax({
        url: '../satt_standa/actdes/ajax_genera_activi.php?opcion=4',
        type: "post",
        data:{cod_titulo :cod_titulo},
        async: false,
        success: function(data) {
            $("#openEditModal").modal("show");
            $("#openEditModalID").empty();
            $("#openEditModalID").append(data);
        },
        error: function() {
            console.log("error");
        }
    });
}

function openCreateModalJs() {
    $("#openCreateModal").modal("show");
}

function InsertNewTitle() {
    formData = $("#newActivityForm").serializeArray();

    if(formData[0].value != '')
    {    
        $.ajax({
        url: '../satt_standa/actdes/ajax_genera_activi.php?opcion=3',
        dataType: 'json',
        type: "post",
        data: {NewformData : formData},
        success: function(data) {
           if(data.status == 200)
           {
            $('#openCreateModal').modal('hide');
            Swal.fire({
                title: 'Registrado!',
                text: "Nuevo tipo de actividad registrada con exito!",
                type: 'success',
                confirmButtonColor: '#336600'
            }).then((result) => {
                if (result.value) {
                    tableHtml();
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
            text: 'El campo nombre del nuevo tipo Actividad es obligatorio.',
            type: 'error',
            confirmButtonColor: '#336600'
        });
    }
}

function editTitle(cod_titulo) {
    formData = $("#editActivityForm").serializeArray();

    if(formData[0].value != '')
    {    
        $.ajax({
        url: '../satt_standa/actdes/ajax_genera_activi.php?opcion=5',
        dataType: 'json',
        type: "post",
        data: {NewformData : formData, cod_titulo : cod_titulo},
        success: function(data) {
           if(data.status == 200)
           {
            $('#openEditModal').modal('hide');
            Swal.fire({
                title: 'Registrado!',
                text: "Tipo de actividad editada con exito!",
                type: 'success',
                confirmButtonColor: '#336600'
            }).then((result) => {
                if (result.value) {
                    tableHtml();
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
            text: 'El campo nombre del nuevo tipo Actividad es obligatorio.',
            type: 'error',
            confirmButtonColor: '#336600'
        });
    }
}

function ChageStatus(est_titulo,cod_titulo)
{
    let status = est_titulo == 'Activo' ? 0 : 1;

    $.ajax({
        url: '../satt_standa/actdes/ajax_genera_activi.php?opcion=6',
        dataType: 'json',
        type: "post",
        data: {status : status,cod_titulo:cod_titulo},
        success: function(data) {
           if(data.status == 200)
           {
            Swal.fire({
                title: 'Registrado!',
                text: "Estatus de actividad actualizado con exito!",
                type: 'success',
                confirmButtonColor: '#336600'
            }).then((result) => {
                if (result.value) {
                    tableHtml();
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

