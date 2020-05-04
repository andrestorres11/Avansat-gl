/* ! \file: inf_estser_contra
 *  \brief: permite visualizar correctamente las vistas en inf_estser_contra.php ademas de realizar algunas funciones ajax
 *  \author: Ing. Luis Manrique
 *  \version: 1.0
 *  \date: 27/04/2020
 *  \bug: 
 *  \warning: 
 */

var standa = 'satt_standa';
$(function() {

    $('#contenedor #tablaRegistros thead tr th').each( function (i) {
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

    var table = $("#contenedor #tablaRegistros").DataTable({
        "ajax": {
            "url": "../" + standa + "/sertra/ajax_estser_contra.php",
            "data": ({option:'setRegistros'}),
            "type": 'POST'
        },
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
        'language' : {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "dom": "<'row'<'col-md-4'B><'col-md-4'l><'col-md-4'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>", 
        "buttons": [
            'copyHtml5',
            {
              extend: 'excelHtml5',
              filename: 'Estado del servicio contratado'
            },
            {
              extend: 'csvHtml5',
              filename: 'Estado del servicio contratado'
            },
            {
              extend: 'pdfHtml5',
              orientation: 'landscape',
              pageSize: 'A2',
              filename: 'Estado del servicio contratado'
            }
        ]
    });
});
