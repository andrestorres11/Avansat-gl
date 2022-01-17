// http://davidstutz.github.io/bootstrap-multiselect/index.html#configuration-options-onSelectAll;
$(document).ready(function() { 
    
    ini_proceso();
    prop_btn_transp();
    
});


function ini_proceso()
{
    
    var standa = $("#standaID").val();
     
    
    $.ajax({
        url: '../'+ standa +'/inform/ajax_progra_turnos.php?opcion=1',
        type: "post",
        async: false,
        success: function(data) {
            $('#Cod_funcionario').append(data);
            //prop_btn_transp()
            
        },
        error: function() {
            console.log("error");
        }
    });
}


function inicializarTablasHoras(opcionradio, nomtabladin){
    
    var standa = $("#standaID").val();
    var Cod_funcionario = $("#Cod_funcionario").val();
    var fec_inicio = $("#fec_inicio").val();
    var fec_finxxx = $("#fec_finxxx").val();
    var opciodatos = opcionradio;
    var tabla = nomtabladin;
    
    
    var table = $(tabla).DataTable({
        destroy: true,
        "ajax": {
            "url": "../" + standa + "/inform/ajax_progra_turnos.php",
            "data": ({ opcion:opciodatos, Cod_funcionario:Cod_funcionario, fec_inicio:fec_inicio, fec_finxxx:fec_finxxx}),
            "type": 'POST',
            'dataSrc':'',
            error: function (xhr, error, code)
            {
                console.log(error);
                
                $(tabla).DataTable().destroy();
                $(tabla).empty();
                $(tabla).html("<h2>No hay Datos</h2>");

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
        
        head_tbl_prgtur();
        
        
    }else{
        
        htmldat ='<thead>';
        htmldat +='<tr>';

        htmldat +='<th>No</th>';
        htmldat +='<th>Funcionario</th>';
        htmldat +='<th>Novedad</th>';
        htmldat +='<th>Fecha</th>';
        htmldat +='<th>Hora incial</th>';
        htmldat +='<th>Hora Final</th>';
        htmldat +='<th>Observación</th>';
        htmldat +='<th>Usuario</th>';
        htmldat +='<th>Fecha</th>';
       
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

function head_tbl_prgtur(){
    var standa = $("#standaID").val();
    var Cod_funcionario = $("#Cod_funcionario").val();
    var fec_inicio = $("#fec_inicio").val();
    var fec_finxxx = $("#fec_finxxx").val();
    
    var opciodatos = 2;

    $('#table_data2').hide;
    $('#table_data').show;
    if( $.fn.DataTable.isDataTable('#table_data2') == true){
        $('#table_data2').DataTable().destroy();
        $('#table_data2').empty();
    }
    if( $.fn.DataTable.isDataTable('#table_data') == true){
        $('#table_data').DataTable().destroy();
        $('#table_data').empty();
    }
    $.ajax({
        url: '../'+ standa +'/inform/ajax_progra_turnos.php',
        data: {"opcion":opciodatos, "Cod_funcionario":Cod_funcionario, "fec_inicio":fec_inicio, "fec_finxxx":fec_finxxx},
        type: "post",
        async: false,
        success: function(data) {
            var tabla = "#table_data";
            $(tabla).append(data);
            var table=$(tabla).DataTable({
                destroy: true,
                fixedColumns: true,
                fixedColumns: {
                    left: 2
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
            
        },
        error: function() {
            console.log("error");
        }
    });

   

}

function prop_btn_transp(){
    $(' .multi_select').selectpicker()
    
}


















