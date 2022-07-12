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
            "type": 'POST',
            "beforeSend":     
            function() { 
                loading();
            },
            "complete":     
            function() { 
                swal.close();
            },
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
        'fixedColumns':   {
            left: 2,
        },
        'language' : {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "dom": "<'row'<'col-md-4'B><'col-md-4'l><'col-md-4'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>", 
        "buttons": [
            {
                extend:    'excelHtml5',
                text:      'Excel',
                titleAttr: 'Exportar a Excel',
                filename: 'Estado del servicio contratado',
                className: 'btn btnprocesodata btn-sm'
              
            },
        ]
    });


    $('#tblRegHorCont thead tr th').each( function (i) {
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

    var table = $("#tblRegHorCont").DataTable({
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
        'fixedColumns':   {
            left: 2,
        },
        'language' : {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "dom": "<'row'<'col-md-4'B><'col-md-4'l><'col-md-4'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>", 
        "buttons": [
            {
                extend:    'excelHtml5',
                text:      'Excel',
                titleAttr: 'Exportar a Excel',
                filename: 'Estado del servicio contratado',
                className: 'btn btnprocesodata btn-sm'
              
            },
        ]
    });
});

function opciontabla(){
    try {
        var opciodatos = 'setRegistros';
        var tipser = $("#tip_servic").val();
        var diastxt = $("#diastxt").val();
        $.ajax({
            url: "../" + standa + "/sertra/ajax_estser_contra.php",
            type: "POST",
            data: {
                option: opciodatos,
                tipser:tipser,
                diastxt:diastxt
            },
            dataType: "json",
            beforeSend: function() { 
                loading();
            },
            success: function(data) {
                console.log(data['data']);
                if ( $.fn.dataTable.isDataTable("#tablaRegistros") ) {
                    var table = $("#tablaRegistros").DataTable();
                }
                if($("#tablaRegistros").DataTable().data().toArray().length > 0){
                    table.rows().remove().draw();
                }
                for(var i=0; i < data['data'].length; i++){
                    table.row.add(crearFilasGeneral(data['data'][i])).draw( false );
                }
            },
            complete: function(){
                swal.close();
            }
        });
    } catch (error) {
    console.log(error);
    }  
}

function crearFilasGeneral(row){
    var tr = '<tr>';
    $.each(row, function(key, item){
        tr = tr + '<td>'+item+'</td>';
    });
    tr = tr + '</tr>';
    var tre = $(tr);
    return tre;
}


function mostrarmodal(transp){
    try {
        var vartransp=transp;
        var opciodatos = 'get_modal';
        $.ajax({
            url: "../" + standa + "/sertra/ajax_estser_contra.php",
            type: "POST",
            data: {
                option: opciodatos,
                vartransp:vartransp
            },
            dataType: "json",
            success: function(data) {
                if ( $.fn.dataTable.isDataTable("#tblRegHorCont") ) {
                    var table = $("#tblRegHorCont").DataTable();
                }
                if($("#tblRegHorCont").DataTable().data().toArray().length > 0){
                    table.rows().remove().draw();
                }
                for(var i=0; i < data.length; i++){
                    table.row.add(crearFilas(data[i], (i+1))).draw( false );
                }

                $('#tblRegHorCont tbody tr').each(function(index){
                    $(this).removeClass('odd');
                    $(this).removeClass('even');
                });
            },
            complete: function(){
                $('#numcontratadas').modal('show');
            }
        });
    } catch (error) {
    console.log(error);
    }  
}

function crearFilas(row,ind){
    let style = '';
    if(row["fec_fineal"]<row["date"]){
        style = 'background-color:#DA8686 !important';
    }
    var tr = $(`<tr>
                <td>`+ind+`</td>
                <td>`+row["nom_contro"]+`</td>
                <td>`+row["fec_inieal"]+`</td>
                <td style="`+style+`">`+row["fec_fineal"]+`</td>
                </tr>`);
    return tr;
}

function loading(){
    Swal.fire({
        title: 'Cargando',
        text: 'Por favor espere...',
        imageUrl: '../' + standa + '/imagenes/ajax-loader.gif',
        imageAlt: 'Custom image',
        showConfirmButton: false,
    })
}

