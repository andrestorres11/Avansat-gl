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
            'dom': \"<'row'<'col-sm-3 col-md-3'B><'col-sm-3 col-md-3'l><'col-sm-3 col-md-6'f>><'row'<'col-sm-3'tr>><'row'<'col-sm-3 col-md-5'i><'col-sm-3 col-md-7'p>>\",
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
        $mSql="SELECT a.cod_solici, a.fec_creaci, a.cor_solici, 
                a.tel_solici, a.cel_solici, a.cod_estcon, 
                a.cod_tipest, a.ind_estseg, a.obs_estseg, 
                a.fec_finsol, a.fec_recdoc,a.fec_venest, b.cod_tercer as 'cod_conduc', 
                b.cod_tipdoc as 'tip_doccon', b.nom_apell1 as 'nom_ape1con', b.nom_apell2 as 'nom_ape2con', 
                b.nom_person 'nom_nomcon', b.num_licenc as 'num_liccon', c.nom_catlic as 'nom_catcon', 
                b.fec_venlic as 'fec_vliccon', b.nom_arlxxx as 'nom_arlcon', b.nom_epsxxx as 'nom_epscon', 
                b.num_telmov as 'num_movcon', b.num_telefo as 'num_telcon', d.nom_ciudad as 'nom_ciucon', 
                b.dir_domici as 'dir_rescon', b.dir_emailx as 'dir_emacon', 
                b.ind_precom, b.val_compar, b.ind_preres, 
                b.val_resolu, e.num_placax, e.num_remolq,
                f.nom_marcax, g.nom_lineax, e.ano_modelo, 
                h.nom_colorx, i.cod_carroc, e.num_chasis, 
                e.num_motorx, e.num_soatxx, e.fec_vigsoa, 
                e.num_lictra, j.nom_operad, e.usr_gpsxxx, 
                e.clv_gpsxxx, e.obs_opegps, e.fre_opegps,
                e.ind_precom as 'ind_vehcom', e.val_compar as 'val_comveh', e.ind_preres as 'ind_preveh',
                e.val_resolu as 'val_resveh', k.cod_tercer as 'cod_propie', k.cod_tipdoc as 'tip_docpro', 
                k.nom_apell1 as 'nom_ape1pro', k.nom_apell2 as 'nom_ape2pro', k.nom_person 'nom_nompro',   
                k.num_telmov as 'num_movpro', k.num_telefo as 'num_telpro', 
                l.nom_ciudad as 'nom_ciupro', k.dir_domici as 'dir_respro', 
                k.dir_emailx as 'dir_emapro'
            FROM ".BASE_DATOS.".tab_estseg_solici a 
       LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer b ON a.cod_conduc = b.cod_tercer 
       LEFT JOIN ".BASE_DATOS.".tab_genera_catlic c ON b.cod_catlic = c.cod_catlic 
       LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad d ON b.cod_ciudad = d.cod_ciudad
       LEFT JOIN ".BASE_DATOS.".tab_estseg_vehicu e ON a.cod_vehicu = e.num_placax
       LEFT JOIN ".BASE_DATOS.".tab_genera_marcas f ON e.cod_marcax = f.cod_marcax
       LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas g ON e.cod_marcax = g.cod_marcax AND e.cod_lineax = g.cod_lineax
       LEFT JOIN ".BASE_DATOS.".tab_vehige_colore h ON e.cod_colorx = h.cod_colorx
       LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc i ON e.cod_carroc = i.cod_carroc
       LEFT JOIN satt_standa.tab_genera_opegps j ON e.cod_opegps = j.cod_operad
       LEFT JOIN ".BASE_DATOS.".tab_estseg_tercer k ON e.cod_propie = k.cod_tercer
       LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad l ON k.cod_ciudad = l.cod_ciudad

            $where
        ";

        $consulta  = new Consulta($mSql, $this -> conexion);
        $data = $consulta -> ret_matriz();
        if(count($data) == 0 ){
            $where ='WHERE 1=1 ';
            $where .= $_GET['cod_transp']  ? " AND a.cod_emptra IN (".$_GET['cod_transp'].") " : "";
            $where .= $_GET['cod_solici'] ? " AND a.cod_solici = '".$_GET['cod_solici']."'" : "";
            $where .= $_GET['fec_inicia'] && $_GET['fec_finali'] ? " AND a.fec_creaci BETWEEN '".$_GET['fec_inicia']."' AND '".$_GET['fec_finali']."'" : ""; 

            $mSql="SELECT 
                a.cod_solici,
                b.nom_tipest,
                a.cod_emptra,
                c.nom_tercer,
                d.cod_tercer as 'num_docume', 
                UCASE(
                    CONCAT(
                        d.nom_apell1, ' ', d.nom_apell2, ' ', 
                        d.nom_person
                    )
                ) as nom_conduct,
                e.num_placax,  
                a.ind_estseg, 
                a.obs_estseg,
                a.usr_estseg,
                a.fec_creaci,
                a.usr_modifi,
                a.fec_finsol,
                a.fec_recdoc
                    FROM ".BASE_DATOS.".tab_estseg_solici a 
                        INNER JOIN .tab_estseg_tipoxx b ON a.cod_tipest = b.cod_tipest 
                        INNER JOIN .tab_tercer_tercer c ON a.cod_emptra = c.cod_tercer 
                        LEFT JOIN .tab_estseg_tercer d ON a.cod_conduc = d.cod_tercer 
                        LEFT JOIN .tab_estseg_vehicu e ON a.cod_vehicu = e.num_placax 

                $where
            ";
            
            $consulta  = new Consulta($mSql, $this -> conexion);
            $data1 = $consulta -> ret_matriz();
        }

        if(count($data) == 0){

            echo '
            <table id="tablaRegistros" class="table table-striped table-bordered table-sm" style="width: 90vw;font-size:10px;">
                <thead>
                <tr>
                    <th>No. Codigo Solic</th>
                    <th>Tipo Solicitud</th>
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
                foreach($data1 as $value){
                    echo "<tr>";
                    echo "<td>".$value['cod_solici']."</td>";
                    echo "<td>".$value['nom_tipest']."</td>";
                    echo "<td>".$value['cod_emptra']."</td>";
                    echo "<td>".$value['nom_tercer']."</td>";
                    echo "<td>".$value['num_docume']."</td>";
                    echo "<td>".$value['nom_conduct']."</td>";
                    echo "<td>".$value['num_placax']."</td>";
                    echo "<td>".($value['ind_estseg'] =='A' ? 'Aprobado':($value['ind_estudi']=='R' ? 'Rechazado':($value['ind_estudi']=='P' ? 'Pendiente':'N/a')))."</td>";
                    echo "<td>".$value['obs_estseg']."</td>";
                    echo "<td>".$value['usr_estseg']."</td>";
                    echo "<td>".$value['fec_creaci']."</td>";
                    echo "<td>".$value['usr_modifi']."</td>";
                    echo "<td>".$value['fec_finsol']."</td>";
                    echo "</tr>";
                }
            echo '</tbody>
            </table>';
        
        }else{

            echo '
            <table id="tablaRegistros" class="table table-striped table-bordered table-sm" style="width: 100%;font-size:10px;">
                <thead>
                    <tr>
                        <th>Número de Solicitud</th>
                        <th>Fecha de Solicitud</th>
                        <th>Correo</th>
                        <th>Telefono</th>
                        <th>Celular</th>
                        <th>Estudio</th>
                        <th>Tipo de Estudio</th>
                        <th>Resultado</th>
                        <th>Observación</th>
                        <th>Fecha/Hora llegada documentos</th>
                        <th>Fecha de finalización</th>
                        <th>Fecha de vencimiento</th>
                        <th>Código del Conductor</th>
                        <th>Tipo de documento</th>
                        <th>Primer Apellido</th>
                        <th>Segundo Apellido</th>
                        <th>Nombres</th>
                        <th>Número de Licencia</th>
                        <th>Categoria</th>
                        <th>Vencimiento de Licencia</th>
                        <th>ARL</th>
                        <th>EPS</th>
                        <th>Celular</th>
                        <th>Telefono</th>
                        <th>Ciudad</th>
                        <th>Dirección</th>
                        <th>Correo</th>
                        <th>¿Presenta Comparendos?</th>
                        <th>Valor</th>
                        <th>¿Presenta Resoluciones?</th>
                        <th>Valor</th>
                        <th>Placa</th>
                        <th>Remolque</th>
                        <th>Marca</th>
                        <th>Linea</th>
                        <th>Modelo</th>
                        <th>Color</th>
                        <th>Carrocería</th>
                        <th>Chasis</th>
                        <th>Número Motor</th>
                        <th>SOAT</th>
                        <th>Vigencia SOAT</th>
                        <th>Licencia de transito</th>
                        <th>Operador GPS</th>
                        <th>Usuario</th>
                        <th>Contraseña</th>
                        <th>Observacion</th>
                        <th>Frecuencia</th>
                        <th>¿Presenta Comparendos?</th>
                        <th>Valor</th>
                        <th>¿Presenta Resoluciones?</th>
                        <th>Valor</th>
                        <th>Código Propietario</th>
                        <th>Tipo de Documento</th>
                        <th>Primer Apellido</th>
                        <th>Segundo Apellido</th>
                        <th>Nombres</th>
                        <th>Telefono</th>
                        <th>Telefono</th>
                        <th>Ciudad</th>
                        <th>Direccion</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody>';
                foreach($data as $registro){
					if($registro['ind_estseg']== 'A'){
                        $registro['ind_estseg'] = 'APROBADO';
                    }elseif($registro['ind_estseg'] == 'P'){
                        $registro['ind_estseg'] = 'PENDIENTE';
                    }elseif($registro['ind_estseg'] == 'R'){
                        $registro['ind_estseg'] = 'RECHAZADO';
                    }elseif($registro['ind_estseg'] == 'C'){
                        $registro['ind_estseg'] = 'CANCELADO';
                    }
            
                    if($registro['cod_tipest']== 'C'){
                        $registro['cod_tipest'] = 'CONDUCTOR';
                    }elseif($registro['cod_tipest'] == 'V'){
                        $registro['cod_tipest'] = 'VEHICULO';
                    }elseif($registro['cod_tipest'] == 'CV'){
                        $registro['cod_tipest'] = 'COMBINADO (CONDUCTOR/VEHICULO)';
                    }
            
            
                    if($registro['cod_estcon']== 1){
                        $registro['cod_estcon'] = 'PRIMARIO';
                    }elseif($registro['cod_estcon'] == 2){
                        $registro['cod_estcon'] = 'ESTANDAR';
                    }elseif($registro['cod_estcon'] == 3){
                        $registro['cod_estcon'] = 'FULL';
                    }else{
                        $registro['cod_estcon'] = 'NO ESPECIFICADO';
                    }
            
                    echo "<tr>";
            
                    echo "<td>".$registro['cod_solici']."</td>";
                    echo "<td>".$registro['fec_creaci']."</td>";
                    echo "<td>".$registro['cor_solici']."</td>";
                    echo "<td>".$registro['tel_solici']."</td>";
                    echo "<td>".$registro['cel_solici']."</td>";
                    echo "<td>".$registro['cod_estcon']."</td>";
                    echo "<td>".$registro['cod_tipest']."</td>";
                    echo "<td>".$registro['ind_estseg']."</td>";
                    echo "<td>".$registro['obs_estseg']."</td>";
                    echo "<td>".$registro['fec_recdoc']."</td>";
                    echo "<td>".$registro['fec_finsol']."</td>";
                    echo "<td>".$registro['fec_venest']."</td>";
                    echo "<td>".$registro['cod_conduc']."</td>";
                    echo "<td>Cédula de Ciudadanía</td>";
                    echo "<td>".$registro['nom_ape1con']."</td>";
                    echo "<td>".$registro['nom_ape2con']."</td>";
                    echo "<td>".$registro['nom_nomcon']."</td>";
                    echo "<td>".$registro['num_liccon']."</td>";
                    echo "<td>".$registro['nom_catcon']."</td>";
                    echo "<td>".$registro['fec_vliccon']."</td>";
                    echo "<td>".$registro['nom_arlcon']."</td>";
                    echo "<td>".$registro['nom_epscon']."</td>";
                    echo "<td>".$registro['num_movcon']."</td>";
                    echo "<td>".$registro['num_telcon']."</td>";
                    echo "<td>".$registro['nom_ciucon']."</td>";
                    echo "<td>".$registro['dir_rescon']."</td>";
                    echo "<td>".$registro['dir_emacon']."</td>";
                    echo "<td>".$registro['ind_precom']."</td>";
                    echo "<td>".$registro['val_compar']."</td>";
                    echo "<td>".$registro['ind_preres']."</td>";
                    echo "<td>".$registro['val_resolu']."</td>";
                    echo "<td>".$registro['num_placax']."</td>";
                    echo "<td>".$registro['num_remolq']."</td>";
                    echo "<td>".$registro['nom_marcax']."</td>";
                    echo "<td>".$registro['nom_lineax']."</td>";
                    echo "<td>".$registro['ano_modelo']."</td>";
                    echo "<td>".$registro['nom_colorx']."</td>";
                    echo "<td>".$registro['cod_carroc']."</td>";
                    echo "<td>".$registro['num_chasis']."</td>";
                    echo "<td>".$registro['num_motorx']."</td>";
                    echo "<td>".$registro['num_soatxx']."</td>";
                    echo "<td>".$registro['fec_vigsoa']."</td>";
                    echo "<td>".$registro['num_lictra']."</td>";
                    echo "<td>".$registro['nom_operad']."</td>";
                    echo "<td>".$registro['usr_gpsxxx']."</td>";
                    echo "<td>".$registro['clv_gpsxxx']."</td>";
                    echo "<td>".$registro['obs_opegps']."</td>";
                    echo "<td>".$registro['fre_opegps']."</td>";
                    echo "<td>".$registro['ind_vehcom']."</td>";
                    echo "<td>".$registro['val_comveh']."</td>";
                    echo "<td>".$registro['ind_preveh']."</td>";
                    echo "<td>".$registro['val_resveh']."</td>";
                    echo "<td>".$registro['cod_propie']."</td>";
                    echo "<td>Cédula de Ciudadanía</td>";
                    echo "<td>".$registro['nom_ape1pro']."</td>";
                    echo "<td>".$registro['nom_ape2pro']."</td>";
                    echo "<td>".$registro['nom_nompro']."</td>";
                    echo "<td>".$registro['num_movpro']."</td>";
                    echo "<td>".$registro['num_telpro']."</td>";
                    echo "<td>".$registro['nom_ciupro']."</td>";
                    echo "<td>".$registro['dir_respro']."</td>";
                    echo "<td>".$registro['dir_emapro']."</td>";
                    echo "</tr>";
                }
            echo '</tbody>
            </table>';
        }
    }
}

$_INFORM = new InfEstudiSeguriTable();
?>