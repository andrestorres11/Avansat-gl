/* ! \file: ins_genera_bancox
 *  \brief: permite visualizar correctamente las vistas en inf_operad_gpsxxx.php ademas de realizar algunas funciones ajax
 *  \author: Ing. Cristian Andrés Torres
 *  \version: 1.0
 *  \date: 02/06/2020
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
            "url": "../" + standa + "/opegps/ajax_infope_gpsxxx.php",
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
        'orderCellsTop': false,
        'fixedHeader': true,
        'language' : {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "dom": "<'row'<'col-md-4'<'#crear'>>><'row'<'col-md-4'B><'col-md-4'l><'col-md-4'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>", 
        "buttons": [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
        ],
        "order": [[ 2, "asc" ]],
        fnInitComplete: function(){
           /*$("#crear").html('<div><a tabindex="0" onclick="formRegistro(\'form\', \'xl\')" class="small-box-footer btn btn-success btn-sm" data-toggle="modal" data-target="#modal-xl" aria-controls="tablaRegistros"><span>Crear registro</span></a></div><br>');*/
        }

    });
});


function decode_utf8(word) {
  return decodeURIComponent(escape(word));
}