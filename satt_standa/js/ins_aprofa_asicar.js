cargando("Cargando Pagina");

$(document).ready(function() {
    swal.close();
    inicializarTablas();
    swal.close();
});


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

//usada
function alertSuccess(msj) {
    Swal.fire({
        title: 'Registrado!',
        text: msj,
        type: 'success',
        confirmButtonColor: '#336600'
    }).then((result) => {
        if (result.value) {
            location.reload();
        }
    })
}

//usada
function alertError($msj) {
    Swal.fire({
        title: 'Error!',
        text: $msj,
        type: 'error',
        confirmButtonColor: '#336600'
    })
}

function cargando(texto) {
    var standa = 'satt_standa';
    Swal.fire({
        title: 'Cargando',
        text: texto,
        imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
        imageAlt: 'Custom image',
        showConfirmButton: false,
        allowOutsideClick: false,
    })
}

//usado
function inicializarTablas() {
    $('#tabla_inf_PorAprobar thead tr th').each(function(i) {
        var title = $(this).text();
        $(this).html('<label style="display:none;">' + title + '</label><input type="text" placeholder="Buscar ' + title + '" />');

        $('input', this).on('keyup change', function() {
            if (table.column(i).search() !== this.value) {
                table
                    .column(i)
                    .search(this.value)
                    .draw();
            }
        });
    });
    $('#tabla_inf_Aprobadas thead tr th').each(function(i) {
        var title = $(this).text();
        $(this).html('<label style="display:none;">' + title + '</label><input type="text" placeholder="Buscar ' + title + '" />');

        $('input', this).on('keyup change', function() {
            if (table.column(i).search() !== this.value) {
                table
                    .column(i)
                    .search(this.value)
                    .draw();
            }
        });
    });

    rellenaTablas();

    var table = $('#tabla_inf_PorAprobar').DataTable({
        "processing": true,
        "deferRender": true,
        "autoWidth": false,
        "search": {
            "regex": true,
            "caseInsensitive": false,
        },
        "bDestroy": true,
        'paging': true,
        'info': true,
        'filter': true,
        'orderCellsTop': true,
        'fixedHeader': true,
        'language': {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "dom": "<'row w100 pb-2'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row w100'<'col-sm-12 col-md-7'i><'col-sm-12 col-md-5'p>>",
    });

    var table = $('#tabla_inf_Aprobadas').DataTable({
        "processing": true,
        "deferRender": true,
        "autoWidth": false,
        "search": {
            "regex": true,
            "caseInsensitive": false,
        },
        "bDestroy": true,
        'paging': true,
        'info': true,
        'filter': true,
        'orderCellsTop': true,
        'fixedHeader': true,
        'language': {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "dom": "<'row w100 pb-2'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row w100'<'col-sm-12 col-md-7'i><'col-sm-12 col-md-5'p>>",
    });

}

function executeFilter() {
    rellenaTablas();
}

//usado
function rellenaTablas() {
    var standa = 'satt_standa';
    //Variables
    var dataString = 'opcion=getRegistros';
    var transportadora = $("#transportadoraID").val();
    var num_solici = $("#num_soliciID").val();
    var fec_inicio = $("#fec_inicio").val();
    var fec_finalx = $("#fec_finxxx").val();
    var tipser = $("#tipserID").val();
    var regional = $("#regionalID").val();
    $.ajax({
        data: {
            transportadora,
            num_solici,
            fec_inicio,
            fec_finalx,
            tipser,
            regional
        },
        type: "POST",
        url: "../" + standa + "/asicar/ajax_aprofa_asicar.php?" + dataString,
        dataType: "json",
        beforeSend: function() {
            cargando('Cargando la información...');
        },
        success: function(data) {
            var table = $('#tabla_inf_PorAprobar').DataTable();
            table.clear();
            table.rows.add(data['PorAprobar']).draw();
            var table = $('#tabla_inf_Aprobadas').DataTable();
            table.clear();
            table.rows.add(data['Aprobados']).draw();
        },
        complete: function() {
            swal.close();
        },
        error: function() {
            alertError('Error, por favor contacte a soporte');
        }

    });
}

function openModal(num_solici){
    var standa = 'satt_standa';
    //Variables
    var dataString = 'opcion=getInfoSolicitud';
    var num_solici = num_solici;
    $.ajax({
        data: {
            num_solici
        },
        type: "POST",
        url: "../" + standa + "/asicar/ajax_aprofa_asicar.php?" + dataString,
        dataType: "json",
        beforeSend: function() {
            cargando('Cargando la información...');
        },
        success: function(data) {
            $("#title-modal-AprobarFacturaModal").empty();
            $("#title-modal-AprobarFacturaModal").append("Solicitud #"+data[0].num_solici);
            $("#num_soliciID").val(data[0].num_solici);
            $("#nom_transpID").val(data[0].nom_transp);
            $("#tip_servicID").val(data[0].tip_servic);
            $("#nom_soliciID").val(data[0].nom_solici);
            $("#cor_soliciID").val(data[0].cor_solici);
            $("#tel_soliciID").val(data[0].tel_solici);
            $("#cel_soliciID").val(data[0].cel_solici);
            $("#aseguraID").val(data[0].nom_asegur);
            $("#polizaID").val(data[0].num_poliza);
            $("#fec_soliciID").val(data[0].fec_solici);
            $("#fec_finaliID").val(data[0].fec_finali);

            // Supongamos que 'data' contiene la información de los servicios
            var servicioTable = $('#servicTable'); // Obtén la tabla

            var servicios = data.servicios;
            
            servicioTable.find('tr').remove();
            


            // Recorre los datos y agrega las filas a la tabla
            // Recorre los servicios y agrega las filas a la tabla
            var total = 0;
            $.each(servicios, function(index, servicio) {
                var costo = parseFloat(servicio.val_servic) / parseFloat(servicio.can_servic);
                var cantidad = servicio.can_servic;
                var subtotal = parseFloat(servicio.val_servic);
                total += subtotal;

                var row = '<tr>' +
                    '<td scope="row">' + (index + 1) + '</td>' +
                    '<td>' + servicio.des_servic + '</td>' +
                    '<td>$' + costo.toFixed(2) + '</td>' +
                    '<td>' + cantidad + '</td>' +
                    '<td>$' + subtotal.toFixed(2) + '</td>' +
                    '</tr>';

                servicioTable.append(row);
            });

            $('<tr>' +
                '<th colspan="4" style="text-align: right; background-color:#fff">Total</th>' +
                '<td style="background-color:#fff" id="totalServicRow">$' + total.toFixed(2) + '</td>' +
                '</tr>').appendTo(servicioTable);

            $("#AprobarFacturaModal").modal('toggle');
        },
        complete: function() {
            swal.close();
        },
        error: function() {
            alertError('Error, por favor contacte a soporte');
        }

    });
}

function sendAproba(){
    var standa = 'satt_standa';
    //Variables
    var dataString = 'opcion=aprobaAsiste';
    var num_solici = $("#num_soliciID").val();
    var obs_aprofa = $("#obs_aprobaID").val();
    $.ajax({
        data: {
            num_solici,
            obs_aprofa
        },
        type: "POST",
        url: "../" + standa + "/asicar/ajax_aprofa_asicar.php?" + dataString,
        dataType: "json",
        beforeSend: function() {
            cargando('Guardando la Aprobación de la Factura');
        },
        success: function(data) {
           swal.close();
           if(data.codRespue==1000){
                alertSuccess(data.msgRespue);
           }else{
                alertError('Error: ' + data.msgRespue);
           }
        },
        error: function() {
            swal.close();
            alertError('Error, por favor contacte a soporte');
        }

    });
}

