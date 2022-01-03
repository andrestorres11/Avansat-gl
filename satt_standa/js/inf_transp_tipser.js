// http://davidstutz.github.io/bootstrap-multiselect/index.html#configuration-options-onSelectAll;
$(document).ready(function() { 
    
    ini_proceso();
    prop_btn_transp();
    
});


function ini_proceso()
{
    
    var standa = $("#standaID").val();
     
    
    $.ajax({
        url: '../'+ standa +'/sertra/ajax_transp_tipser.php?opcion=1',
        type: "post",
        async: false,
        success: function(data) {
            $('#Cod_transp').append(data);
            //prop_btn_transp()
            
        },
        error: function() {
            console.log("error");
        }
    });
}


function inicializarTablasHoras(opcionradio, nomtabladin){
    
    var standa = $("#standaID").val();
    var cod_transp = $("#Cod_transp").val();
    var fec_inicio = $("#fec_inicio").val();
    var fec_finxxx = $("#fec_finxxx").val();
    var opciodatos = opcionradio;
    var tabla = nomtabladin;
    
    $(tabla + ' thead tr th').each( function (i) {
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
    
    var table = $(tabla).DataTable({
        destroy: true,
        "ajax": {
            "url": "../" + standa + "/sertra/ajax_transp_tipser.php",
            "data": ({ opcion:opciodatos, cod_transp:cod_transp, fec_inicio:fec_inicio, fec_finxxx:fec_finxxx}),
            "type": 'POST',
            'dataSrc':'',
           
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
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "dom": "<'row'<'col-sm-12 col-md-3'B><'col-sm-12 col-md-3'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "buttons": [
                {
                    extend:    'excelHtml5',
                    text:      'Excel',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btnprocesodata btn-sm'
                }
            ]

    });

    table.on( 'draw.dt', function () {
        var PageInfo = $(tabla).DataTable().page.info();
            table.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            } );
    } );
     
}



function opciontabla(){
    
    
   radioselecc=$('input[name=optradio]:checked', '#filter').val(); 
    
   htmldat="";
    if (radioselecc==="1"){
        
        htmldat ='<thead><tr>';
        htmldat +='<th>No</th>';
        htmldat +='<th>Nit</th>';
        htmldat +='<th>Nombre Empresa</th>';
        htmldat +='<th>Dias</th>';
        htmldat +='<th>Hora Inicial</th>';
        htmldat +='<th>Hora Final</th>';
        htmldat +='<th>Usuario</th>';
        htmldat +='<th>fecha de M</th>';
        htmldat +='</tr></thead>';
        htmldat +='<tbody><tr id="resultado_info_registradas">';
        htmldat +='</tr></tbody>';
        $('#table_data2').hide;
        $('#table_data').show;
        if( $.fn.DataTable.isDataTable('#table_data2') == true){
            $('#table_data2').DataTable().destroy();
            $('#table_data2').empty();
        }
        $('#table_data').html(htmldat);
        inicializarTablasHoras("2","#table_data");
    }else{
        
        htmldat ='<thead>';
        htmldat +='<tr><tr>';

        htmldat +='<td colspan="3" style=" border: 0px"></td>';
        htmldat +='<td colspan="2">T. CL Faro - PreCargue</td>';
        htmldat +='<td colspan="2">T. Contratado - PreCargue</td>';
        htmldat +='<td colspan="2">T. CL Faro - Cargue</td>';
        htmldat +='<td colspan="2">T. Contratado - Cargue</td>';
        htmldat +='<td colspan="2">T. CL Faro - Transito</td>';
        htmldat +='<td colspan="2">T. Contratado - Transito</td>';
        htmldat +='<td colspan="2">T. CL Faro - Descargue</td>';
        htmldat +='<td colspan="2">T. Contratado - Descargue</td>';
        htmldat +='</tr>';


        htmldat +='<th>No</th>';
        htmldat +='<th>Nit</th>';
        htmldat +='<th>Nombre Empresa</th>';
        htmldat +='<th>Nacional</th>';
        htmldat +='<th>Urbano</th>';
        htmldat +='<th>Nacional</th>';
        htmldat +='<th>Urbano</th>';
        htmldat +='<th>Nacional</th>';
        htmldat +='<th>Urbano</th>';
        htmldat +='<th>Nacional</th>';
        htmldat +='<th>Urbano</th>';
        htmldat +='<th>Urbano</th>';
        htmldat +='<th>Nacional</th>';
        htmldat +='<th>Urbano</th>';
        htmldat +='<th>Nacional</th>';
        htmldat +='<th>Urbano</th>';
        htmldat +='<th>Nacional</th>';
        htmldat +='<th>Urbano</th>';
        htmldat +='<th>Nacional</th>';

        htmldat +='<th>Usuario</th>';
        htmldat +='<th>fecha de M</th>';
        htmldat +='</tr></thead>';
        htmldat +='<tbody><tr id="resultado_info_registradas">';
        htmldat +='</tr></tbody>';
        if( $.fn.DataTable.isDataTable('#table_data') == true){
            $('#table_data').DataTable().destroy();
            $('#table_data').empty();
        }
        $('#table_data2').html(htmldat);

        inicializarTablasHoras("3","#table_data2");

    }

    

    



}

function prop_btn_transp(){
    $(' .multi_select').selectpicker()
    
}


















