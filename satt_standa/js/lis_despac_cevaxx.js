$(document).ready(function() {
    inicializarTablas(0);
});


function inicializarTablas(ind_aplfil){
    var standa = $("#standaID").val();
    $('#table_data thead tr th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<label style="display:none;">'+title+'</label><input type="text" placeholder="Buscar '+title+'" />' );
 
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );
    var tabla = "#table_data";    
    fec_inicia = $("#fec_inicia").val();
    fec_finxxx = $("#fec_finxxx").val();
    cod_manifi = $("#cod_manifi").val();
    apl_filtro = ind_aplfil;
    var table = $(tabla).DataTable({
        "ajax": {
            "url": "../" + standa + "/inform/ajax_despac_cevaxx.php",
            "data": ({ opcion: 'getRegistros', fec_inicia: fec_inicia, fec_finxxx: fec_finxxx, cod_manifi: cod_manifi,apl_filtro: apl_filtro}),
            "type": 'POST',
            'dataSrc':'',
        },
        "bDestroy": true,

            'processing': true,
            "deferRender": true,
            "autoWidth": false,
            "search": {
                "regex": true,
                "caseInsensitive": false,
            },
            'paging': true,
            'info': true,
            'filter': true,
            'orderCellsTop': true,
            'fixedHeader': true,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "dom": "<'row'<'col-sm-12 col-md-3'B><'col-sm-12 col-md-3'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "buttons": [
                'excelHtml5',
                'csvHtml5',
            ]
    });

    $(".dt-buttons .btn-group").parent().parent().addClass('mb-2');
   
}


function cargando(texto){
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



