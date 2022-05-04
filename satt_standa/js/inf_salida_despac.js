// http://davidstutz.github.io/bootstrap-multiselect/index.html#configuration-options-onSelectAll;
$(document).ready(function() { 
    
    ini_proceso();
    prop_btn_transp();
    

});


function ini_proceso()
{
    
    var standa = $("#standaID").val();
     
    
    $.ajax({
        url: '../'+ standa +'/inform/ajax_salida_despac.php?opcion=1',
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


    $.ajax({
        url: '../'+ standa +'/inform/ajax_salida_despac.php?opcion=2',
        type: "post",
        async: false,
        success: function(data) {
            $('#Cod_tiposerv').append(data);
            //prop_btn_transp()
            
        },
        error: function() {
            console.log("error");
        }
    });
}


function inicializarTablasHoras(){
    
    var standa = $("#standaID").val();
    var Cod_transp = $("#Cod_transp").val();
    var Cod_tiposerv = $("#Cod_tiposerv").val();
    var fec_inicio = $("#fec_inicio").val();
    var fec_finxxx = $("#fec_finxxx").val();
    var hor_inicio = $("#hor_inicia").val();
    var hor_finxxx = $("#hor_finxxx").val();
    var opciodatos = 3;

if(Cod_tiposerv===null || Cod_tiposerv==""){
    alert("Seleccione un tipo de servicio");
    return;
}

var day1 = new Date(fec_inicio); 
var day2 = new Date(fec_finxxx);
var difference= Math.abs(day2-day1);
var days = difference/(1000 * 3600 * 24)

if(days>8){
    alert("Seleccione solo un rango de fechas maximo de 8 dias");
    return;
}



    $.ajax({
        url: "../" + standa + "/inform/ajax_salida_despac.php",
        data: ({ opcion:opciodatos, Cod_transp:Cod_transp, Cod_tiposerv:Cod_tiposerv, fec_inicio:fec_inicio, fec_finxxx:fec_finxxx, hor_inicio:hor_inicio, hor_finxxx:hor_finxxx}),
        type: 'POST',
        dataType: "json",
        error: function (xhr, error, code)
        {
            console.log(error);
            $("#conttablas").empty();
            alert('No hay Datos');
        },
        beforeSend : 
            function () 
			{ 
                document.getElementById('openModal').style.display = 'block';
                
            },
        success: function(data) {

            console.log(data['titulostbl']);
            console.log(data['contentbl']);
            console.log(data['idtable']);
            var newdattitulo = data['titulostbl'];
            var newdattablas = data['contentbl'];
            var newnametablas=data['idtable'];
            var length = newdattitulo.length;
            $("#conttablas").empty();
            for (var i = 0; i < length; i++) {
                $("#conttablas").append(newdattitulo[i]);
                $("#conttablas").append(newdattablas[i]);
                drawtbl(newnametablas[i]);

            }
            document.getElementById('openModal').style.display = 'none';
           
            
        }    
    } );




     
}



function opciontabla(){

        inicializarTablasHoras();

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
        url: '../'+ standa +'/inform/ajax_salida_despac.php',
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

function drawtbl(name_table){
    
    var table=$('#' + name_table).DataTable({
        'destroy': true,
        'fixedColumns': true,
        'fixedColumns': {
            left: 3
        },
        'sScrollX':true,
        'bAutoWidth':true,
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
        },
        {
            extend:    'csv',
            text:      'CSV',
            titleAttr: 'Exportar a Archivo Plano',
            className: 'btn btnprocesodata btn-sm'
        }
    ]

    });
   
    table.draw();
    

}

function dathortransp(transp,fech,hor){

    var vartransp=transp;
    var varfech=fech;    
    var opciodatos = 5;
    var varhor=hor.toString();
    
    if (varhor.length<2){
        varhor="0"+varhor;
    }
    
    var standa = $("#standaID").val();
    
    $.ajax({
        url: "../" + standa + "/inform/ajax_salida_despac.php",
        data: ({ opcion:opciodatos, vartransp:vartransp, varfech:varfech, varhor:varhor}),
        type: 'POST',
        dataType: "json",
        error: function (xhr, error, code)
        {
            console.log(error);
           
            alert('No hay Datos');
        },
        beforeSend : 
            function () 
			{ 
                document.getElementById('openModal').style.display = 'block';
            },
        success: function(data) {

            $("#titleventmodal").empty();
            $('#titleventmodal').append(data['numreg'] + ' Despacho(s) encontrados de la empresa de transporte '+ data['transportadora']);
            $("#contenidotbl").empty();
           $('#contenidotbl').append(data['conttbody']);
           $('#numhortransp').modal('show'); 
            drawtbl('table_detalle');
            document.getElementById('openModal').style.display = 'none';
           console.log(data['transportadora']);
           
            
        }    
    } );

   //alert(vartransp +' -- ' + varfech + ' -- ' + varhor);

   
    
   
}

function prop_btn_transp(){
    $(' .multi_select').selectpicker()
    
}


















