<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

class InfEstudiSeguriTable {

    function __construct() {
        include('../lib/ajax.inc');
        @include_once( "../lib/general/constantes.inc" );
        $this -> conexion = $AjaxConnection;
        echo '
        <!-- Bootstrap -->
        <link href="../js/dashboard/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Font Awesome -->
        <link href="../js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
        
        <!-- Datatables -->
        <link href="../js/dashboard/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="../js/dashboard/vendors/datatables.net-buttons/css/buttons.dataTables.min.css" rel="stylesheet">
        <link href="../js/dashboard/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="../js/dashboard/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="../js/dashboard/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

        <!-- Float Menu -->
        <link href="../js/dashboard/button.css" rel="stylesheet">
        <link href="../js/dashboard/floatMenu.css" rel="stylesheet">

        <!-- Custom Theme Style -->
        <link href="../js/dashboard/inf_dashbo_dashbo.css" rel="stylesheet">
        <link href="../estilos/informes.css" rel="stylesheet">
        <style>
        #tablaRegistros_wrapper{
            width: fit-content;
        }
        </style>
    ';
    echo '

        <!-- jQuery -->
        <script src="../js/dashboard/vendors/jquery/dist/jquery.min.js"></script>
        
        <!-- jQuery FrameWork -->
        <script src="../js/functions.js"></script>
        <script src="../js/jquery.blockUI2019.js"></script>

        <!-- Bootstrap -->
        <script src="../js/dashboard/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

        <!-- bootstrap-progressbar -->
        <script src="../js/dashboard/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

        <!-- Datatables -->
        <script src="../js/dashboard/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="../js/dashboard/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="../js/dashboard/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="../js/dashboard/vendors/jszip/dist/jszip.min.js"></script>
        <script src="../js/dashboard/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="../js/dashbo_libxxx/functions.js"></script>
    ';
   echo "<script>
    $(document).ready(function () {
        $('#tablaRegistros').DataTable({
            dom: 'Bflrtip',
            responsive: true,
            'autoWidth': false,
            'search': {
                    'regex': true,
                'caseInsensitive': false,
            },
            'pageLength': 5,
            'paging': true,
            'info': true,
            'filter': true,
            'orderCellsTop': true,
            'fixedHeader': true,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json'
            },
            'dom': \"<'row'<'col-sm-12 col-md-3'B><'col-sm-12 col-md-3'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>\",
            'buttons': [
                'excelHtml5',
                'csvHtml5',
            ]
        });
        
    });
    </script>";
    $where ='WHERE 1=1 ';
    $where .= $_GET['cod_transp']  ? " AND a.cod_emptra IN (".$_GET['cod_transp'].") " : "";
    $where .= $_GET['cod_solici'] ? " AND a.cod_solici = '".$_GET['cod_solici']."'" : "";
    $where .= $_GET['fec_inicia'] && $_GET['fec_finali'] ? " AND a.fec_creaci BETWEEN '".$_GET['fec_inicia']."' AND '".$_GET['fec_finali']."'" : "";  
        $mSql="SELECT 
            a.cod_solici, 
            b.cod_estseg, 
            a.cod_emptra,
            a.nom_solici,
            c.num_docume,
            UCASE(CONCAT(c.nom_apell1,' ',c.nom_apell2, ' ',c.nom_person)) as nom_conduct,
            UCASE(d.num_placax) as 'license_plate',
            b.ind_estudi,
            b.obs_estudi,
            a.fec_creaci as fec_sol,
            b.fec_modifi as fec_resp,
            b.usr_creaci as usr_creat,
            b.usr_modifi as usr_modif
                FROM ".BASE_DATOS.".tab_solici_estseg a
                INNER JOIN tab_relaci_estseg b ON a.cod_solici = b.cod_solici
                INNER JOIN tab_estudi_person c ON b.cod_conduc = c.cod_segper
                INNER JOIN tab_estudi_vehicu d ON b.cod_vehicu = d.cod_segveh

            $where
        ";
        $consulta  = new Consulta($mSql, $this -> conexion);
        $data = $consulta -> ret_matriz();
        echo '
            <table id="tablaRegistros" class="table table-striped table-bordered table-sm" style="width: 90vw;font-size:10px;">
                <thead>
                <tr>
                    <th>No. Codigo Solic</th>
                    <th>No. Codigo EstSeg</th>
                    <th>Nit Empresa</th>
                    <th>Nombre Empresa</th>
                    <th>No. Documento Conductor</th>
                    <th>Nombre Conductor</th>
                    <th>Placa</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                    <th>Usuario Creación</th>
                    <th>Fecha Solicitud</th>
                    <th>Usuario Modificación</th>
                    <th>Fecha Respuesta</th>
                </tr>
                </thead>
                <tbody>';
                foreach($data as $value){
					echo "<tr>";
					echo "<td>".$value['cod_solici']."</td>";
					echo "<td>".$value['cod_estseg']."</td>";
                    echo "<td>".$value['cod_emptra']."</td>";
                    echo "<td>".$value['nom_solici']."</td>";
                    echo "<td>".$value['num_docume']."</td>";
                    echo "<td>".$value['nom_conduct']."</td>";
                    echo "<td>".$value['license_plate']."</td>";
                    echo "<td>".($value['ind_estudi'] =='A' ? 'Aprobado':($value['ind_estudi']=='R' ? 'Rechazado':($value['ind_estudi']=='P' ? 'Pendiente':'N/a')))."</td>";
                    echo "<td>".$value['obs_estudi']."</td>";
                    echo "<td>".$value['usr_creat']."</td>";
                    echo "<td>".$value['fec_sol']."</td>";
                    echo "<td>".$value['usr_modif']."</td>";
                    echo "<td>".$value['fec_resp']."</td>";
					echo "</tr>";
                }
            echo '</tbody>
            </table>';
    }
}

$_INFORM = new InfEstudiSeguriTable();
?>