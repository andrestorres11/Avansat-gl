/*************FUNCIONES******************/
$(document).ready(function() {

    let myTable = $('#table_id').DataTable({

        columnDefs: [{
            orderable: false,
            className: 'checkValidates',
            targets: 0
        }],
        select: {
            style: 'os',
            selector: 'td:first-child'
        },
        "lengthMenu": [
            [10, 25, 50, 75, 100, -1],
            [10, 25, 50, 75, 100, "Todos"]
        ],

        "language": {
            "emptyTable": "<i>No hay datos disponibles en la tabla.</i>",
            "info": "Del _START_ al _END_ de _TOTAL_ ",
            "infoEmpty": "Mostrando 0 registros de un total de 0.",
            "infoFiltered": "(filtrados de un total de _MAX_ registros)",
            "infoPostFix": "(actualizados)",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "<span style='font-size:15px;'>Buscar:</span>",
            "searchPlaceholder": "Dato para buscar",
            "zeroRecords": "No se han encontrado coincidencias.",
            "paginate": {
                "first": "Primera",
                "last": "ultima",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": "Ordenacion ascendente",
                "sortDescending": "Ordenacion descendente"
            }
        }
    });


    $("#checkAll").on("click", function() {
        if (myTable.rows({
                selected: true
            }).count() > 0) {
            myTable.rows().deselect();
            return;
        }

        myTable.rows().select();


        $(".checkValidate").prop("checked", this.checked);
    });

    // if all checkbox are selected, check the selectall checkbox and viceversa  
    $(".checkValidate").on("click", function() {
        if ($(".checkValidate").length == $(".case:checked").length) {
            $("#selectall").prop("checked", true);
        } else {
            $("#selectall").prop("checked", false);
        }
    });
});

function mostrarMensaje(mensaje) {
    Swal.fire({
        title: mensaje,
        icon: 'success',
        confirmButtonText: 'Listo',
        cancelButtonText: 'Cancelar',
        showCancelButton: false,
        showCloseButton: true
    });
}

function validarClic(opcion, id_producto) {
    if (opcion == 'eliminar') {
        var texto_titulo = 'Seguro desea eliminar el producto?';
        var mensaje = 'Producto eliminado';
    }
    event.preventDefault();
    Swal.fire({
        title: texto_titulo,
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value === true) {
            document.querySelector('#formulario_eliminar_' + id_producto).submit();
        }
    });
}

function Validator() {
    if ($('#archivo').val() == '') {
        $('#errorID').hide();
        $('#errorID').html("<div class='alert alert-danger' role='alert' >No ha seleccionado ningun archivo.</div>");
        $('#errorID').show('slow');
    } else {
        $('#formID').submit();
    }
}

function ValidateIt(rut_archiv) {
    var patt = /\.csv$/g;
    if (!patt.test(rut_archiv.val())) {
        $('#errorID').hide();
        $('#errorID').html("<div class='alert alert-danger' role='alert' >La extension del archivo es Incorrecta.</div>");
        $('#errorID').show('slow');
        rut_archiv.val('');
        return false;
    }
}

function anularPedido(num_pedido) {
    var texto_titulo = 'Seguro desea Anular el Pedido?';
    var mensaje = 'Pedido Anulado';
    var standa = "satt_standa";
    var parametros = "Option=anulaPedido&pedido=" + num_pedido + "";
    event.preventDefault();
    Swal.fire({
        title: texto_titulo,
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.value === true) {
            $.ajax({
                url: "../" + standa + "/planea/ajax_aproba_pedido.php?" + parametros,
                data: parametros,
                type: "POST",
                dataType: "json", // tell jQuery not to set contentType
                beforeSend: function() {
                    Swal.fire({
                        title: 'Cargando',
                        type: 'success',
                        text: 'Por favor espere...',
                        imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
                        imageAlt: 'Custom image',
                        showConfirmButton: false,
                    })
                },
                success: function(data) {
                    //console.log(data);
                    if (data['status'] == 1) {
                        Swal.fire({
                            title: data['title'],
                            text: data['text'],
                            type: data['type'],
                            confirmButtonColor: '#454545'
                        }).then((result) => {
                            if (result.value) {
                                location.reload();
                            }
                        })
                    } else if (data['status'] == 2) {
                        Swal.fire({
                            title: data['title'],
                            text: data['text'],
                            type: data['type'],
                            confirmButtonColor: '#454545'
                        }).then((result) => {
                            if (result.value) {
                                location.reload();
                            }
                        })
                    } else {
                        ErrorAlerta("Error no fue posible realizar el registro. Verifique la informacion e intente de nuevo");
                    }
                }
            });
        }
    });
}

function prePlaneaPedidos() {
    let contador = 0;
    let pedidos = "";
    let texto_titulo = 'Seguro desea Planear Estos Pedidos?';
    let mensaje = 'Pedidos planeados';
    let standa = "satt_standa";

    var DatosSeleccionados = [];

    $("input[type=checkbox]:checked").each(function() {
        var trActual = $(this).parent('td').parent('tr');
        DatosSeleccionados.pedido = $(this).val();
        DatosSeleccionados.linea = trActual.find('.input_linea').first().val();
        DatosSeleccionados.push({ "pedido": $(this).val(), "linea": trActual.find('.input_linea').first().val() });
        contador++;
    });

    if (contador < 1) {
        alert("Seleccione al menos un pedido");
    } else {
        let parametros = "Option=prePlaneaPedidos";
        event.preventDefault();
        Swal.fire({
            title: texto_titulo,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.value === true) {
                $.ajax({
                    url: "../" + standa + "/planea/ajax_aproba_pedido.php?" + parametros,
                    data: { dato: DatosSeleccionados },
                    type: "POST",
                    dataType: "json", // tell jQuery not to set contentType
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Cargando',
                            type: 'success',
                            text: 'Por favor espere...',
                            imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
                            imageAlt: 'Custom image',
                            showConfirmButton: false,
                        })
                    },
                    success: function(data) {
                        //console.log(data);
                        if (data['status'] == 1) {
                            Swal.fire({
                                title: data['title'],
                                text: data['text'],
                                type: data['type'],
                                confirmButtonColor: '#454545'
                            }).then((result) => {
                                if (result.value) {
                                    location.reload();
                                }
                            })
                        } else if (data['status'] == 2) {
                            Swal.fire({
                                title: data['title'],
                                text: data['text'],
                                type: data['type'],
                                confirmButtonColor: '#454545'
                            }).then((result) => {
                                if (result.value) {
                                    location.reload();
                                }
                            })
                        } else {
                            ErrorAlerta("Error no fue posible realizar el registro. Verifique la informacion e intente de nuevo");
                        }
                    }
                });
            }
        });

    }


}