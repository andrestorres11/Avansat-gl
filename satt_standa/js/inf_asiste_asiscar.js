$(document).ready(function() {

    init_table();
    $('#formSearch').validate({
        rules: {
            datInit: {
                required: true
            },
            dateEnd: {
                required: true
            }
        },
        messages: {
            datInit: {
                required: "Por favor ingrese una fecha de inicio"
            },
            dateEnd: {
                required: "Por favor ingrese una fecha final"
            }
        },
        submitHandler: function(form) {
            
            const optionTransp=$('#optionTransp option:selected').toArray().map(item => item.value);
            const optionProv= $('#optionProv option:selected').toArray().map(item => item.value);
            const optionRegio= $('#optionRegio option:selected').toArray().map(item => item.value);
            const optionTipSer=$('#optionTipSer option:selected').toArray().map(item => item.value);
            const datInit= $('#datInit').val();
            const datEnd= $('#datEnd').val();
            
            query(optionTransp,optionProv,optionRegio,optionTipSer,datInit,datEnd);
        }

    });
    
});

function query(optionTransp,optionProv,optionRegio,optionTipSer,datInit,datEnd)
    {
        try {
            //Get data
            let optionTransp_='';
            optionTransp.forEach(element => {
                optionTransp_=optionTransp_+element+',';
            });
            optionTransp_=optionTransp_.substring(0, optionTransp_.length - 1);

            let optionProv_='';
            optionProv.forEach(element => {
                optionProv_=optionProv_+element+',';
            });
            optionProv_=optionProv_.substring(0, optionProv_.length - 1); 

            let optionRegio_='';
            optionRegio.forEach(element => {
                optionRegio_=optionRegio_+element+',';
            });
            optionRegio_=optionRegio_.substring(0, optionRegio_.length - 1); 

            let optionTipSer_='';
            optionTipSer.forEach(element => {
                 optionTipSer_=optionTipSer_+element+',';
            });
            optionTipSer_=optionTipSer_.substring(0, optionTipSer_.length - 1);

            var table = $('#tabla_results').DataTable({
                "ajax": {
                    "url": "../satt_standa/asicar/ajax_asiste_asiscar.php",
                    "data": {
                        optionTransp: optionTransp_,
                        optionProv:optionProv_,
                        optionRegio:optionRegio_,
                        optionTipSer:optionTipSer_,
                        datInit:datInit,
                        datEnd:datEnd,
                    },
                    "type": 'POST'
                },
                columns: [
                    { "data": "n" },
                    { "data": "id" },
                    { "data": "nom_asiste" },
                    { "data": "name" },
                    { "data": "nom_region" },
                    { "data": "nom_solici" },
                    { "data": "cor_solici" },
                    { "data": "cel_solici" },
                    { "data": "ase_solici" },
                    { "data": "num_poliza" },
                    { "data": "nameCond" },
                    { "data": "ce1_transp" },
                    { "data": "num_placax" },
                    { "data": "ciudorig" },
                    { "data": "ciuddest" },
                    { "data": "valcli" },
                    { "data": "val_cospro" },
                    { "data": "nomprovee" },
                    { "data": "cod_provee" },
                    { "data": "sol_antic" },
                    { "data": "rte_fnt" },
                    { "data": "rte_Ica" },
                    { "data": "salrest" },
                    { "data": "utili" },
                    { "data": "rent" },
                    { "data": "fec_creaci" },
                    { "data": "fec_modifi" },
                    ],  
                'processing': true,
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
                var PageInfo = $('#tabla_results').DataTable().page.info();
                    table.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                        cell.innerHTML = i + 1 + PageInfo.start;
                    } );
            } );

        } catch (error) {
        console.log(error);
        }
    }
function init_table()
{
    $('#tabla_results thead tr th').each( function (i) {
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
}