<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
header('Content-Type: text/html; charset=UTF-8');
ini_set('memory_limit', '4096M');

class InformViajes {

    var $conexion,
        $cod_aplica,
        $usuario;
    var $cNull = array(array('', '- Todos -'));
    var $cNullt = array();

    function __construct($co, $us, $ca) {
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;

        switch ($_REQUEST[opcion]) {
            case 99:
                $this->getInform();
                break;
            case 98:
                $this->getInform2();
                break;
            default:
                $this->Listar();
                break;
        }
    }

    function getInform() {
    
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/informes.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/homolo.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/bootstrap.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
       // echo '<!-- jQuery -->
        //<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        //<script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery/dist/jquery.min.js"></script>';
        echo "<script type='text/javascript' language='JavaScript' src='../".DIR_APLICA_CENTRAL."/js/jquery.js'></script>\n";
        echo "<script type='text/javascript' language='JavaScript' src='../".DIR_APLICA_CENTRAL."/js/jquery.blockUI.js'></script>\n";
        echo "<script type='text/javascript' language='JavaScript' src='../".DIR_APLICA_CENTRAL."/js/functions.js'></script>";
        echo "<script>
        $.blockUI({
            theme: true,
            title: 'Cargando informe',
            draggable: false,
            message: '<center><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ajax-loader2.gif\" /><p>Generando informe</p></center>'
        });
         function expTabExcelDesp() {
                $.blockUI({
                    theme: true,
                    title: 'Generando  excel',
                    draggable: false,
                    message: '<center><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ajax-loader2.gif\" /><p>Generando Excel</p></center>'
                });
                setTimeout(function(){
                    $.unblockUI();
                }, 6000);
                $('#exportExcelID').val('<table>'+$('#export').html()+'</table>');
            }</script>";

        $mSelect1 = "SELECT 
                       t.num_despac as 'num_viajex',
                       t.cod_manifi as 'cod_manifi',
                       t.fec_despac as 'fec_despac',
                       b.nom_tipdes as 'nom_tipdes', 
                       c.nom_paisxx as 'nom_paiori', 
                       d.nom_depart as 'nom_depori', 
                       e.nom_ciudad as 'nom_ciuori', 
                       f.nom_paisxx as 'nom_paides', 
                       g.nom_depart as 'nom_depdes', 
                       h.nom_ciudad as 'nom_ciudes', 
                       t.cod_operad as 'cod_operad', 
                       t.fec_citcar as 'fec_citcar', 
                       t.hor_citcar as 'hor_citcar', 
                       t.nom_sitcar as 'nom_sitcar', 
                       t.val_flecon as 'val_flecon', 
                       t.val_despac as 'val_despac', 
                       t.val_antici as 'val_antici', 
                       t.val_retefu as 'val_retefu', 
                       t.nom_carpag as 'nom_carpag', 
                       t.nom_despag as 'nom_despag', 
                       t.cod_agedes as 'cod_agedes', 
                       t.val_pesoxx as 'val_pesoxx', 
                       t.obs_despac as 'obs_despac', 
                       t.fec_llegad as 'fec_llegad', 
                       t.obs_llegad as 'obs_llegad', 
                       t.ind_planru as 'ind_planru', 
                       i.nom_rutasx as 'nom_rutasx', 
                       t.ind_anulad as 'ind_anulad', 
                       l.nom_marcax as 'nom_marcax', 
                       m.nom_lineax as 'nom_lineax', 
                       z.cod_conduc as 'cod_conduc',
                       n.nom_colorx as 'nom_colorx', 
                       o.nom_ciudad as 'ciu_conduc', 
                       s.des_mercan as 'nom_mercan',
                       CONCAT(w.nom_apell1,' ',w.nom_apell2,' ',w.nom_tercer) as 'nom_conduc',
                       CONCAT(w.num_telef1, ' ',w.num_telef1) as 'tel_conduc',
                       w.num_telmov as 'cel_conduc',
                       w.dir_domici as 'dir_conduc',
                       x.num_catlic as 'cat_liccon',
                       z.num_placax as 'num_placax',
                       v.num_config as 'num_config',
                       q.nom_carroc as 'nom_carroc',
                       v.num_motorx as 'num_motorx',
                       v.num_chasis as 'num_chasis',
                       v.num_poliza as 'num_poliza',
                       v.nom_asesoa as 'nom_soatxx',
                       v.fec_vigfin as 'fec_finsoa',
                       v.num_tarpro as 'num_tarpro',
                       v.cod_tenedo as 'cod_poseed',
                       CONCAT(k.nom_apell1,' ',k.nom_apell2,' ',k.nom_tercer) as 'nom_poseed',
                       k.dir_domici as 'dir_poseed',
                       r.nom_ciudad as 'ciu_poseed',
                       z.num_trayle as 'num_remolq',
                       t.num_poliza as 'num_poliza',
                       y.nom_tipveh as 'nom_tipveh',
                       t.ind_anulad as 'ind_anulad',
                       j.nom_tercer as 'nom_asegur',
                       aa.nom_tercer as 'nom_transp',
                       b.cod_tipdes as 'cod_tipdes',
                       aa.cod_tercer as 'cod_transp'
                  FROM ".BASE_DATOS.".tab_despac_despac t 
	              LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes b ON t.cod_tipdes = b.cod_tipdes 
	              LEFT JOIN ".BASE_DATOS.".tab_genera_paises c ON t.cod_paiori = c.cod_paisxx 
	              LEFT JOIN ".BASE_DATOS.".tab_genera_depart d ON t.cod_paiori = d.cod_paisxx 
	                    AND t.cod_depori = d.cod_depart 
	              LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad e ON t.cod_paiori = e.cod_paisxx 
	                    AND t.cod_depori = e.cod_depart 
	                    AND t.cod_ciuori = e.cod_ciudad 
	            LEFT JOIN ".BASE_DATOS.".tab_genera_paises f ON t.cod_paides = f.cod_paisxx 
	            LEFT JOIN ".BASE_DATOS.".tab_genera_depart g ON t.cod_paides = g.cod_paisxx 
	                AND t.cod_depdes = g.cod_depart 
	            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad h ON t.cod_paides = h.cod_paisxx 
	                AND t.cod_depdes = h.cod_depart 
	                AND t.cod_ciudes = h.cod_ciudad
                LEFT JOIN ".BASE_DATOS.".tab_despac_seguim u ON u.num_despac = t.num_despac
	            LEFT JOIN ".BASE_DATOS.".tab_genera_rutasx i ON u.cod_rutasx = i.cod_rutasx 
	            LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer j ON t.cod_asegur = j.cod_tercer
                LEFT JOIN ".BASE_DATOS.".tab_despac_vehige z ON t.num_despac = z.num_despac 
                LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer aa ON aa.cod_tercer = z.cod_transp
                LEFT JOIN ".BASE_DATOS.".tab_vehicu_vehicu v ON v.num_placax = z.num_placax
	            LEFT JOIN ".BASE_DATOS.".tab_genera_marcas l ON v.cod_marcax = l.cod_marcax 
	            LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas m ON v.cod_marcax = m.cod_marcax 
	                AND v.cod_lineax = m.cod_lineax 
	            LEFT JOIN ".BASE_DATOS.".tab_vehige_colore n ON v.cod_colorx = n.cod_colorx
                LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer w ON z.cod_conduc = w.cod_tercer
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad o ON w.cod_ciudad = o.cod_ciudad
                LEFT JOIN ".BASE_DATOS.".tab_tercer_conduc x ON z.cod_conduc = x.cod_tercer
	            LEFT JOIN ".BASE_DATOS.".tab_vehige_config p ON v.num_config = p.num_config
	            LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc q ON v.cod_carroc = q.cod_carroc
                LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k ON v.cod_tenedo = k.cod_tercer
	            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad r ON k.cod_ciudad  = r.cod_ciudad 
                LEFT JOIN ".BASE_DATOS.".tab_despac_remesa s ON t.num_despac = s.num_despac
                LEFT JOIN ".BASE_DATOS.".tab_genera_tipveh y ON v.cod_tipveh = y.cod_tipveh
	                AND t.fec_salida IS NOT NULL 
	                AND t.fec_salida <= NOW() 
	                AND (
		                t.fec_llegad IS NULL 
		                OR t.fec_llegad = '0000-00-00 00:00:00'
	                ) 
	                AND t.ind_planru = 'S' 
	                AND t.ind_anulad = 'R' 
	                AND z.ind_activo = 'S' ";
        $mSelect2 .="WHERE t.fec_despac BETWEEN '".$_REQUEST['fec_inicia']." 00:00:00' AND '".$_REQUEST['fec_finali']." 23:59:59' ";

        if( $_REQUEST['ind_finali'] == '1' ) {
        	$mSelect2 .= "AND t.fec_llegad IS NOT NULL ";
        }

        if ($_REQUEST['num_viajex'] != '')
            $mSelect2 .= " AND t.num_despac = '".$_REQUEST['num_viajex']."'";

        if ($_REQUEST['num_placax'] != '')
            $mSelect2 .= " AND z.num_placax = '".$_REQUEST['num_placax']."'";

        if ($_REQUEST['cod_tipdes'] != '')
            $mSelect2 .= " AND t.cod_tipdes = '".$_REQUEST['cod_tipdes']."'";

        if ($_REQUEST['trans_'] != ''){
            $mSelect2 .= " AND z.cod_transp IN (".$_REQUEST['trans_'].")";  
        }  

        if ($_REQUEST['cod_noveda'] != '') {
            $mSql1 = $mSelect1;
            $mSql1 .= "INNER JOIN ".BASE_DATOS.".tab_despac_noveda as aaa
                           ON t.num_despac = aaa.num_despac ";
            $mSql1 .= $mSelect2;
            $mSql1 .= " AND aaa.cod_noveda = '".$_REQUEST['cod_noveda']."'";

            $mSql2 = $mSelect1;
            $mSql2 .= "INNER JOIN ".BASE_DATOS.".tab_despac_contro aab
                           ON t.num_despac = aab.num_despac ";
            $mSql2 .= $mSelect2;
            $mSql2 .= " AND aab.cod_noveda = '".$_REQUEST['cod_noveda']."'";

            $mSelect = "( ".$mSql1." GROUP BY t.num_despac ) 
                  UNION  
                  ( ".$mSql2." GROUP BY t.num_despac ) ";
        } else {
            $mSelect = $mSelect1 . $mSelect2 . " GROUP BY t.num_despac ";
        }
        $consulta = new Consulta($mSelect, $this->conexion);
        $_INFORM = $consulta->ret_matriz();
        
      
        $formulario = new Formulario("../".DIR_APLICA_CENTRAL."/lib/exportExcel.php", "post", "Informe de Trazabilidad ", "frm_trazab\" id=\"frm_trazabID");
                echo '<input type="image" align="left" style="margin-left:25px;" name="botondeenvio" onclick="expTabExcelDesp()" src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" >
				<br>';
        $mHtml = "<table border='1' id='export' class='table table-bordered'>";
        if (sizeof($_INFORM) > 0) {
            $size = $_REQUEST['ind_noveda'] == '1' ? '72' : '68';
            $mHtml .="<thead>";
            $mHtml .="<tr>";
            $mHtml .= "<th  colspan='$size' >SE ENCONTRARON ".sizeof($_INFORM)." REGISTROS</th>";
            $mHtml .="</tr>";
            $mHtml .= "<tr>";
            $mHtml .= "<th class=cellHead >No.</th>";
            $mHtml .= "<th class=cellHead >Nit</th>";
            $mHtml .= "<th class=cellHead >Transportadora</th>";
            $mHtml .= "<th class=cellHead >Despacho</th>";
            $mHtml .= "<th class=cellHead >Manifiesto</th>";
            $mHtml .= "<th class=cellHead >Fecha Despacho</th>";
            $mHtml .= "<th class=cellHead >Tipo Despacho</th>";
            $mHtml .= "<th class=cellHead >Pais Origen</th>";
            $mHtml .= "<th class=cellHead >Dpto Origen</th>";
            $mHtml .= "<th class=cellHead >Ciudad Origen</th>";
            $mHtml .= "<th class=cellHead >Pais Destino</th>";
            $mHtml .= "<th class=cellHead >Dpto Destino</th>";
            $mHtml .= "<th class=cellHead >Ciudad Destino</th>";
            $mHtml .= "<th class=cellHead >Operador</th>";
            $mHtml .= "<th class=cellHead >Fecha Cita Cargue</th>";
            $mHtml .= "<th class=cellHead >Nombre Sitio Cargue</th>";
            $mHtml .= "<th class=cellHead >Valor Flete Conductor</th>";
            $mHtml .= "<th class=cellHead >Valor Despacho</th>";
            $mHtml .= "<th class=cellHead >Valor Anticipo</th>";
            $mHtml .= "<th class=cellHead >Valor Retefuente</th>";
            $mHtml .= "<th class=cellHead >Encargado Pagar Cargue</th>";
            $mHtml .= "<th class=cellHead >Encargado Pagar Descargue</th>";
            $mHtml .= "<th class=cellHead >Agencia</th>";
            $mHtml .= "<th class=cellHead >Peso(Kg)</th>";
            $mHtml .= "<th class=cellHead >Observaciones</th>";
            $mHtml .= "<th class=cellHead >Fecha Cita Descargue</th>";
            $mHtml .= "<th class=cellHead >Observaciones Llegada</th>";
            $mHtml .= "<th class=cellHead >Ruta</th>";
            $mHtml .= "<th class=cellHead >Plan de Ruta</th>";
            $mHtml .= "<th class=cellHead >Anulado</th>";
            $mHtml .= "<th class=cellHead >C.C. Conductor</th>";
            $mHtml .= "<th class=cellHead >Nombre Conductor</th>";
            $mHtml .= "<th class=cellHead >Tel Conductor</th>";
            $mHtml .= "<th class=cellHead >Cel Conductor</th>";
            $mHtml .= "<th class=cellHead >Direcci&oacute;n Conductor</th>";
            $mHtml .= "<th class=cellHead >Ciudad Conductor</th>";
            $mHtml .= "<th class=cellHead >Categor&iacute;a Licencia</th>";
            $mHtml .= "<th class=cellHead >P&oacute;liza</th>";
            $mHtml .= "<th class=cellHead >Aseguradora</th>";
            $mHtml .= "<th class=cellHead >Placa</th>";
            $mHtml .= "<th class=cellHead >Remolque</th>";
            $mHtml .= "<th class=cellHead >Tipo Veh&iacute;culo</th>";
            $mHtml .= "<th class=cellHead >Modelo</th>";
            $mHtml .= "<th class=cellHead >Marca</th>";
            $mHtml .= "<th class=cellHead >L&iacute;nea</th>";
            $mHtml .= "<th class=cellHead >Color</th>";
            $mHtml .= "<th class=cellHead >Configuraci&oacute;n</th>";
            $mHtml .= "<th class=cellHead >Carrocer&iacute;a</th>";
            $mHtml .= "<th class=cellHead >No. Chasis</th>";
            $mHtml .= "<th class=cellHead >No. Motor</th>";
            $mHtml .= "<th class=cellHead >SOAT</th>";
            $mHtml .= "<th class=cellHead >Vcto SOAT</th>";
            $mHtml .= "<th class=cellHead >Aseguradora SOAT</th>";
            $mHtml .= "<th class=cellHead >Tarjeta Propiedad</th>";
            $mHtml .= "<th class=cellHead >C.C. Poseedor</th>";
            $mHtml .= "<th class=cellHead >Nombre Poseedor</th>";
            $mHtml .= "<th class=cellHead >Direcci&oacute;n Poseedor</th>";
            $mHtml .= "<th class=cellHead >Ciudad Poseedor</th>";
            $mHtml .= "<th class=cellHead >Mercanc&iacute;a</th>";
            $mHtml .= "<th class=cellHead >Anulado</th>";
            if ($_REQUEST['ind_noveda'] == '1') {
                $mHtml .= "<th class=cellHead >
                    <table border='1' id='export' class='table table-bordered'>
                    <thead>
                        <tr>
                            <th class=cellHead style='font-size:9px;' width='12%' >Sitio de seguimiento</th>
                            <th class=cellHead style='font-size:9px;' width='12%'>Novedad</th>
                            <th class=cellHead style='font-size:9px;' width='12%'>Observaci&oacute;n del Controlador</th>
                            <th class=cellHead style='font-size:9px;' width='12%'>Fecha y Hora</th>
                            <th class=cellHead style='font-size:9px;' width='12%'>Tiempo</th>
                            <th class=cellHead style='font-size:9px;' width='12%'>Diferencia T</th>
                            <th class=cellHead style='font-size:9px;' width='12%'>Cumplimiento Seguimiento</th>
                            <th class=cellHead style='font-size:9px;' width='12%'>Porcentaje de Cumplimiento</th>
                        </tr>
                    </thead>
                    </table>
                </th>";
            }
            $mHtml .="</tr>";
            $mHtml .="</thead>";
            $mHtml .="<tbody>";
            $count = 1;
            foreach ($_INFORM as $key => $row) {
                $mArrayNove = array_merge(InformViajes::getDespacContro($row['num_viajex']), InformViajes::getDespacNoveda($row['num_viajex']));
                if(sizeof($mArrayNove)==0){
                    $sizeNovedad=1;
                }else{
                    $sizeNovedad=sizeof($mArrayNove);
                }
                $mSizeNoved = $_REQUEST['ind_noveda'] == '1' ? $sizeNovedad : '1';
                $tip_transp = 'DESCONOCIDA';
                $tip_transp_number=(isset($row['tip_transp']) ? $row['tip_transp']:'0' );
                if ($tip_transp_number == '1'){
                    $tip_transp = 'FLOTA PROPIA';
                }
                elseif ($tip_transp_number == '2'){
                    $tip_transp = 'TERCEROS';
                }
                elseif ($tip_transp_number == '3'){
                    $tip_transp = 'EMPRESAS';
                }
                
                if ($row[71]) {
                    $href1 = '<a href="?cod_servic=3302&window=central&despac='.$row[71].'&opcion=1" target="_blank">';
                    $href2 = '</a>';
                } else {
                    $href1 = '';
                    $href2 = '';
                }
                $mHtml .= "<tr>";
                $mHtml .= "<td class='cellInfo' align ='left'>".$count."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['cod_transp'] !='' ? $row['cod_transp']:'')."</td>"; //Nit empresa de Transporte
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['nom_transp'] !='' ? $row['nom_transp']:'')."</td>"; //Nombre empresa de Transporte
                $mHtml .= "<td class='cellInfo' align ='left' >".$href1.$row['num_viajex'].$href2."</td>"; //Viaje
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['cod_manifi'] !='' ? $row['cod_manifi']:'')."</td>"; //Manifiesto
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['fec_despac'] !='' ? $this->toFecha($row['fec_despac']):'-')."</td>"; //Fecha Despacho
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['nom_tipdes'] !='' ? strtoupper($row['nom_tipdes']):'')."</td>"; //Tipo Despacho
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['nom_paiori'] !='' ? strtoupper($row['nom_paiori']):'')."</td>"; //Pais Origen
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['nom_depori'] !='' ? strtoupper($row['nom_depori']):'')."</td>"; //Dpto Origen
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['nom_ciuori'] !='' ? strtoupper($row['nom_ciuori']):'')."</td>"; //Ciudad Origen
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_paides'] !='' ? strtoupper($row['nom_paides']):'' )."</td>"; //Pais Destino
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_depdes'] != '' ? strtoupper($row['nom_depdes']):'' )."</td>"; //Dpto Destino
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_ciudes'] != '' ? strtoupper($row['nom_ciudes']):'' )."</td>"; //Ciudad Destino
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['cod_operad'] != '' ? $row['cod_operad']:'' )."</td>"; //Operador
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['fec_citcar'] != '' ? $this->toFecha($row['fec_citcar']." ".$row['hor_citcar']):'-')."</td>"; //Fecha Cita de Cargue
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_sitcar'] != '' ? $row['nom_sitcar'] : 'DESCONOCIDO' )."</td>"; //Nombre Sitio de Cargue
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['val_flecon'] !='' ? number_format($row['val_flecon'], 0, '.', '.'):'0')."</td>"; //Valor Flete Conductor
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['val_despac'] !='' ? number_format($row['val_despac'], 0, '.', '.'):'0')."</td>"; //Valor Despacho
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['val_antici'] !='' ? number_format($row['val_antici'], 0, '.', '.'):'0')."</td>"; //Valor Anticipo
                $mHtml .= "<td class='cellInfo' align ='left' >".($row['val_retefu'] !='' ? number_format($row['val_retefu'], 0, '.', '.'):'0')."</td>"; //Valor Retefuente
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_carpag'] != '' ? $row['nom_carpag']:'' )."</td>"; //Encargado de Pagar Cargue
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_despag'] != '' ? $row['nom_despag']:'' )."</td>"; //Encargado de pagar Descargue
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['cod_agedes'] != '' ? $row['cod_agedes']:'' )."</td>"; //Agencia
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['val_pesoxx'] != '' ? $row['val_pesoxx']:'0' )."</td>"; //Peso Kg
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['obs_despac'] != '' ? $row['obs_despac'] : '-' )."</td>"; //Observaciones
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['fec_llegad'] != '' ? $this->toFecha($row['fec_llegad']) : '-' )."</td>"; //Fecha Cita de Descargue
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['obs_llegad'] != ' ' && $row['obs_llegad'] != '' ? $row['obs_llegad'] : '-' )."</td>"; //Observaciones Llegada
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_rutasx'] != '' ? $row['nom_rutasx'] : 'DESCONOCIDA' )."</td>"; //Ruta
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['ind_planru'] == 'S' ? 'SI' : 'NO' )."</td>"; //Plan de Ruta
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['ind_anulad'] == 'A' ? 'SI' : 'NO' )."</td>"; //Anulado
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['cod_conduc'] != '' ? $row['cod_conduc'] : 'DESCONOCIDO' )."</td>"; // CC. Conductor
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_conduc'] != '' ? $row['nom_conduc'] : 'DESCONOCIDO' )."</td>"; // Nombre Conductor
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['tel_conduc'] != '' ? $row['tel_conduc'] : 'DESCONOCIDO' )."</td>"; // Tel conductor
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['cel_conduc'] != '' ? $row['cel_conduc'] : 'DESCONOCIDO' )."</td>"; // Cel conductor
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['dir_conduc'] != '' ? $row['dir_conduc'] : 'DESCONOCIDA' )."</td>"; // Direccion Conductor
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['ciu_conduc'] != '' ? $row['ciu_conduc'] : 'DESCONOCIDA' )."</td>"; // Ciudad Conductor
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['cat_liccon'] != '' ? $row['cat_liccon'] : 'DESCONOCIDA' )."</td>"; // Categoria Licencia
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['num_poliza'] != '' ? $row['num_poliza'] : 'DESCONOCIDA' )."</td>"; // Poliza
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_asegur'] != '' ? $row['nom_asegur'] : 'DESCONOCIDA' )."</td>"; //Aseguradora
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['num_placax'] != '' ? $row['num_placax'] : 'DESCONOCIDA' )."</td>"; //Placa
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['num_remolq'] != '' ? $row['num_remolq'] : 'DESCONOCIDO' )."</td>"; //Remolque
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_tipveh'] != '' ? $row['nom_tipveh'] : 'DESCONOCIDO' )."</td>"; //Tipo Vehiculo
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_marcax'] != '' ? $row['nom_marcax'] : 'DESCONOCIDO' )."</td>"; //Modelo
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_lineax'] != '' ? $row['nom_lineax'] : 'DESCONOCIDA' )."</td>"; //Marca
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_lineax'] != '' ? $row['nom_lineax'] : 'DESCONOCIDA' )."</td>"; //Linea
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_colorx'] != '' ? $row['nom_colorx'] : 'DESCONOCIDO' )."</td>"; //Color
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['num_config'] != '' ? $row['num_config'] : 'DESCONOCIDA' )."</td>"; //Confuguracion
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_carroc'] != '' ? $row['nom_carroc'] : 'DESCONOCIDA' )."</td>"; //Carroceria
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['num_chasis'] != '' ? $row['num_chasis'] : 'DESCONOCIDO' )."</td>"; //No Chasis
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['num_motorx'] != '' ? $row['num_motorx'] : 'DESCONOCIDO' )."</td>"; //No motor
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['num_poliza'] != '' ? $row['num_poliza'] : 'DESCONOCIDO' )."</td>"; //SOAT
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['fec_finsoa'] != '' ? $this->toFecha($row['fec_finsoa']) : 'DESCONOCIDA' )."</td>"; //Vcto Soat
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_soatxx'] != '' ? $row['nom_soatxx'] : 'DESCONOCIDA' )."</td>"; //Aseguradora SOAT
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['num_tarpro'] != '' ? $row['num_tarpro'] : 'DESCONOCIDA' )."</td>"; //Tarjeta Propiedad
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['cod_poseed'] != '' ? $row['cod_poseed'] : 'DESCONOCIDA' )."</td>"; //CC Posedor
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['nom_poseed'] != '' ? $row['nom_poseed'] : 'DESCONOCIDO' )."</td>"; // Nombre Poseedor
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['dir_poseed'] != '' ? $row['dir_poseed'] : 'DESCONOCIDA' )."</td>"; // Direccion Poseedor
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['ciu_poseed'] != '' ? $row['ciu_poseed'] : 'DESCONOCIDA' )."</td>"; // Ciudad Poseedor
                $mHtml .= "<td class='cellInfo' align ='left' >".( isset($row['nom_mercan']) ? $row['nom_mercan'] : 'DESCONOCIDO' )."</td>"; //Mercancia
                $mHtml .= "<td class='cellInfo' align ='left' >".( $row['ind_anulad'] == 'A' ? 'SI' : 'NO' )."</td>"; //Anulado
                if ($_REQUEST['ind_noveda'] == '1') {
                    $horainicio="";
                    $horafin="";
                    $hours="";
                    $resultsegui=0;
                    $resultsegui= InformViajes::getTiempoTransport($row['cod_transp'], $row['cod_tipdes']) ;
                    $resprom=0;
                    if($mSizeNoved > 1){
                        for ($y = 0; $y < $mSizeNoved; $y++) {
                            if($y == 0){
                                $horainicio= strtotime($mArrayNove[$y][3]);
                                $result=0;
                            }else{
                                $horafin=$horainicio;
                                $horainicio= strtotime($mArrayNove[$y][3]);
                                $diff = abs($horainicio - $horafin); 
                                $years = floor($diff / (365*60*60*24));
                                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                                $hours = floor(($diff - $years * 365*60*60*24  - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
                                $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24- $hours*60*60)/ 60); 
                                $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));
                                $result=($hours * 60) + $minutes;
                                // printf("%d years, %d months, %d days, %d hours, " . "%d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds);  
                                // sacar Promedio de efectividad del seguimiento
                                $operacc=$resultsegui-$result;
                                    if($operacc >=0){
                                        $resprom++ ;
                                    }
                            }                            
                        }
                        $promediofin=($resprom * 100)/($mSizeNoved-1);
                        $mHtml2 = "<table border='1' class='table table-bordered table-sm pt-0'>";
                        $mHtml2 .="<tbody>";
                        for ($x = 0; $x < $mSizeNoved; $x++) {
                            $nomSitiox = $mArrayNove[$x][0] == '' ? $mArrayNove[$x][4] : $mArrayNove[$x][0];
                            $mHtml2 .= '<tr>';
                            $mHtml2 .= '<td class="cellInfo"  width="12%">'.$nomSitiox.'&nbsp;</td>'; //Sitio de Seguimiento
                            $mHtml2 .= '<td class="cellInfo" width="12%">'.$mArrayNove[$x][1].'&nbsp;</td>'; //Novedad
                            $mHtml2 .= '<td class="cellInfo"  width="12%">'.$mArrayNove[$x][2].'&nbsp;</td>'; //Observacion del controlador
                            $mHtml2 .= '<td class="cellInfo" width="12%">'.$mArrayNove[$x][3].'&nbsp;</td>'; //Fecha y Hora
                            
                            
                            $horafin=$horainicio;
                            $horainicio= strtotime($mArrayNove[$x][3]);
                            $diff = abs($horainicio - $horafin); 
                            $years = floor($diff / (365*60*60*24));
                            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                            $hours = floor(($diff - $years * 365*60*60*24  - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
                            $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24- $hours*60*60)/ 60); 
                            $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));
                            $result=($hours * 60) + $minutes;
                            
                            $mHtml2 .= '<td class="cellInfo" width="12%">'.($result !='' ? $result:'&nbsp;').'</td>'; //Tiempo
                            $cal=($resultsegui-$result);
                            $mHtml2 .= '<td class="cellInfo" width="12%">'.($cal !=0 ? $cal:'&nbsp;').'</td>'; //Diferencia T
                            $opera=$resultsegui-$result;
                            if($opera >=0){
                                $resseg="Si";
                            }else{
                                $resseg="No";
                            }
                            $mHtml2 .= '<td class="cellInfo" width="12%" >'.$resseg.' &nbsp;</td>'; //Cumplimiento Seguimiento
                            $mHtml2 .= '<td class="cellInfo" align ="left" width="12%">'.($promediofin !=0 ? round($promediofin,2):0).'%</td>'; //Porcentaje de Cumplimiento
                            $mHtml2 .='</tr>';
                        }
                        $mHtml2 .="</tbody>";
                        $mHtml2 .= "</table>";
                    }
                }

                $mHtml .= "<td class='cellInfo' align ='left' >".$mHtml2; //seguimiento
                $mHtml .= "</td>";
                $mHtml .= "</tr>";
                $count++;
                $mHtml2='';
            }
            $mHtml .="</tbody>";
        }

        $mHtml .="</table>";
        
        //$_SESSION[xls_InformViajes] = $mHtml;
        echo $mHtml;
        $archivo = "informe_operaciones".date("Y_m_d_H_i").".xls";
        $mHtml2 ='<input type="hidden" name="standa" id="standaID" value="'.DIR_APLICA_CENTRAL.'">';
        $mHtml2 .='<input type="hidden" name="window" id="windowID" value="'.$_REQUEST['window'].'">';
        $mHtml2 .='<input type="hidden" name="cod_servic" id="cod_servicID" value="'.$_REQUEST['cod_servic'].'">';
        $mHtml2 .='<input type="hidden" name="cod_ciuori" id="cod_ciuoriID">';
        $mHtml2 .='<input type="hidden" name="cod_ciudes" id="cod_ciudesID">';
        $mHtml2 .='<input type="hidden" name="cod_conduc" id="cod_conducID">';
        $mHtml2 .='<input type="hidden" name="nameFile" id="nameFileID" value="'.$archivo.'">';
        $mHtml2 .='<input type="hidden" name="OptionExcel" id="OptionExcelID" value="_REQUEST">';
        $mHtml2 .='<input type="hidden" name="exportExcel" id="exportExcelID" value="">';
        echo $mHtml2;
        echo "<script>$.unblockUI();</script>";
        $formulario->cerrar();
    }

    function getInform2() {

        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/informes.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/homolo.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/bootstrap.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
       // echo '<!-- jQuery -->
        //<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        //<script src="../' . DIR_APLICA_CENTRAL . '/js/dashboard/vendors/jquery/dist/jquery.min.js"></script>';
        echo "<script type='text/javascript' language='JavaScript' src='../".DIR_APLICA_CENTRAL."/js/jquery.js'></script>\n";
        echo "<script type='text/javascript' language='JavaScript' src='../".DIR_APLICA_CENTRAL."/js/jquery.blockUI.js'></script>\n";
        echo "<script type='text/javascript' language='JavaScript' src='../".DIR_APLICA_CENTRAL."/js/functions.js'></script>";
        echo "<script>
        $.blockUI({
            theme: true,
            title: 'Cargando informe',
            draggable: false,
            message: '<center><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ajax-loader2.gif\" /><p>Generando informe</p></center>'
        });
         function expTabExcelDesp() {
                $.blockUI({
                    theme: true,
                    title: 'Generando  excel',
                    draggable: false,
                    message: '<center><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ajax-loader2.gif\" /><p>Generando Excel</p></center>'
                });
                setTimeout(function(){
                    $.unblockUI();
                }, 6000);
                $('#exportExcelID').val('<table>'+$('#export').html()+'</table>');
            }</script>";

        $mSelect1 = "SELECT 
                       t.num_despac as 'num_viajex',
                       t.cod_manifi as 'cod_manifi',
                       t.fec_despac as 'fec_despac',
                       b.nom_tipdes as 'nom_tipdes', 
                       c.nom_paisxx as 'nom_paiori', 
                       d.nom_depart as 'nom_depori', 
                       e.nom_ciudad as 'nom_ciuori', 
                       f.nom_paisxx as 'nom_paides', 
                       g.nom_depart as 'nom_depdes', 
                       h.nom_ciudad as 'nom_ciudes', 
                       t.cod_operad as 'cod_operad', 
                       t.fec_citcar as 'fec_citcar', 
                       t.hor_citcar as 'hor_citcar', 
                       t.nom_sitcar as 'nom_sitcar', 
                       t.val_flecon as 'val_flecon', 
                       t.val_despac as 'val_despac', 
                       t.val_antici as 'val_antici', 
                       t.val_retefu as 'val_retefu', 
                       t.nom_carpag as 'nom_carpag', 
                       t.nom_despag as 'nom_despag', 
                       t.cod_agedes as 'cod_agedes', 
                       t.val_pesoxx as 'val_pesoxx', 
                       t.obs_despac as 'obs_despac', 
                       t.fec_llegad as 'fec_llegad', 
                       t.obs_llegad as 'obs_llegad', 
                       t.ind_planru as 'ind_planru', 
                       i.nom_rutasx as 'nom_rutasx', 
                       t.ind_anulad as 'ind_anulad', 
                       l.nom_marcax as 'nom_marcax', 
                       m.nom_lineax as 'nom_lineax', 
                       z.cod_conduc as 'cod_conduc',
                       n.nom_colorx as 'nom_colorx', 
                       o.nom_ciudad as 'ciu_conduc', 
                       s.des_mercan as 'nom_mercan',
                       CONCAT(w.nom_apell1,' ',w.nom_apell2,' ',w.nom_tercer) as 'nom_conduc',
                       CONCAT(w.num_telef1, ' ',w.num_telef1) as 'tel_conduc',
                       w.num_telmov as 'cel_conduc',
                       w.dir_domici as 'dir_conduc',
                       x.num_catlic as 'cat_liccon',
                       z.num_placax as 'num_placax',
                       v.num_config as 'num_config',
                       q.nom_carroc as 'nom_carroc',
                       v.num_motorx as 'num_motorx',
                       v.num_chasis as 'num_chasis',
                       v.num_poliza as 'num_poliza',
                       v.nom_asesoa as 'nom_soatxx',
                       v.fec_vigfin as 'fec_finsoa',
                       v.num_tarpro as 'num_tarpro',
                       v.cod_tenedo as 'cod_poseed',
                       CONCAT(k.nom_apell1,' ',k.nom_apell2,' ',k.nom_tercer) as 'nom_poseed',
                       k.dir_domici as 'dir_poseed',
                       r.nom_ciudad as 'ciu_poseed',
                       z.num_trayle as 'num_remolq',
                       t.num_poliza as 'num_poliza',
                       y.nom_tipveh as 'nom_tipveh',
                       t.ind_anulad as 'ind_anulad',
                       j.nom_tercer as 'nom_asegur',
                       aa.nom_tercer as 'nom_transp',
                       b.cod_tipdes as 'cod_tipdes',
                       aa.cod_tercer as 'cod_transp'
                  FROM ".BASE_DATOS.".tab_despac_despac t 
	              LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes b ON t.cod_tipdes = b.cod_tipdes 
	              LEFT JOIN ".BASE_DATOS.".tab_genera_paises c ON t.cod_paiori = c.cod_paisxx 
	              LEFT JOIN ".BASE_DATOS.".tab_genera_depart d ON t.cod_paiori = d.cod_paisxx 
	                    AND t.cod_depori = d.cod_depart 
	              LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad e ON t.cod_paiori = e.cod_paisxx 
	                    AND t.cod_depori = e.cod_depart 
	                    AND t.cod_ciuori = e.cod_ciudad 
	            LEFT JOIN ".BASE_DATOS.".tab_genera_paises f ON t.cod_paides = f.cod_paisxx 
	            LEFT JOIN ".BASE_DATOS.".tab_genera_depart g ON t.cod_paides = g.cod_paisxx 
	                AND t.cod_depdes = g.cod_depart 
	            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad h ON t.cod_paides = h.cod_paisxx 
	                AND t.cod_depdes = h.cod_depart 
	                AND t.cod_ciudes = h.cod_ciudad
                LEFT JOIN ".BASE_DATOS.".tab_despac_seguim u ON u.num_despac = t.num_despac
	            LEFT JOIN ".BASE_DATOS.".tab_genera_rutasx i ON u.cod_rutasx = i.cod_rutasx 
	            LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer j ON t.cod_asegur = j.cod_tercer
                LEFT JOIN ".BASE_DATOS.".tab_despac_vehige z ON t.num_despac = z.num_despac 
                LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer aa ON aa.cod_tercer = z.cod_transp
                LEFT JOIN ".BASE_DATOS.".tab_vehicu_vehicu v ON v.num_placax = z.num_placax
	            LEFT JOIN ".BASE_DATOS.".tab_genera_marcas l ON v.cod_marcax = l.cod_marcax 
	            LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas m ON v.cod_marcax = m.cod_marcax 
	                AND v.cod_lineax = m.cod_lineax 
	            LEFT JOIN ".BASE_DATOS.".tab_vehige_colore n ON v.cod_colorx = n.cod_colorx
                LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer w ON z.cod_conduc = w.cod_tercer
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad o ON w.cod_ciudad = o.cod_ciudad
                LEFT JOIN ".BASE_DATOS.".tab_tercer_conduc x ON z.cod_conduc = x.cod_tercer
	            LEFT JOIN ".BASE_DATOS.".tab_vehige_config p ON v.num_config = p.num_config
	            LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc q ON v.cod_carroc = q.cod_carroc
                LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k ON v.cod_tenedo = k.cod_tercer
	            LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad r ON k.cod_ciudad  = r.cod_ciudad 
                LEFT JOIN ".BASE_DATOS.".tab_despac_remesa s ON t.num_despac = s.num_despac
                LEFT JOIN ".BASE_DATOS.".tab_genera_tipveh y ON v.cod_tipveh = y.cod_tipveh
	                AND t.fec_salida IS NOT NULL 
	                AND t.fec_salida <= NOW() 
	                AND (
		                t.fec_llegad IS NULL 
		                OR t.fec_llegad = '0000-00-00 00:00:00'
	                ) 
	                AND t.ind_planru = 'S' 
	                AND t.ind_anulad = 'R' 
	                AND z.ind_activo = 'S' ";
        $mSelect2 .="WHERE t.fec_despac BETWEEN '".$_REQUEST['fec_inicia']." 00:00:00' AND '".$_REQUEST['fec_finali']." 23:59:59' ";

        if( $_REQUEST['ind_finali'] == '1' ) {
        	$mSelect2 .= "AND t.fec_llegad IS NOT NULL ";
        }

        if ($_REQUEST['num_viajex'] != '')
            $mSelect2 .= " AND t.num_despac = '".$_REQUEST['num_viajex']."'";

        if ($_REQUEST['num_placax'] != '')
            $mSelect2 .= " AND z.num_placax = '".$_REQUEST['num_placax']."'";

        if ($_REQUEST['cod_tipdes'] != '')
            $mSelect2 .= " AND t.cod_tipdes = '".$_REQUEST['cod_tipdes']."'";

        if ($_REQUEST['trans_'] != ''){
            $mSelect2 .= " AND z.cod_transp IN (".$_REQUEST['trans_'].")";  
        }  

        if ($_REQUEST['cod_noveda'] != '') {
            $mSql1 = $mSelect1;
            $mSql1 .= "INNER JOIN ".BASE_DATOS.".tab_despac_noveda as aaa
                           ON t.num_despac = aaa.num_despac ";
            $mSql1 .= $mSelect2;
            $mSql1 .= " AND aaa.cod_noveda = '".$_REQUEST['cod_noveda']."'";

            $mSql2 = $mSelect1;
            $mSql2 .= "INNER JOIN ".BASE_DATOS.".tab_despac_contro aab
                           ON t.num_despac = aab.num_despac ";
            $mSql2 .= $mSelect2;
            $mSql2 .= " AND aab.cod_noveda = '".$_REQUEST['cod_noveda']."'";

            $mSelect = "( ".$mSql1." GROUP BY t.num_despac ) 
                  UNION  
                  ( ".$mSql2." GROUP BY t.num_despac ) ";
        } else {
            $mSelect = $mSelect1 . $mSelect2 . " GROUP BY t.num_despac ";
        }
        $consulta = new Consulta($mSelect, $this->conexion);
        $_INFORM = $consulta->ret_matriz();
        
      
        $formulario = new Formulario("../".DIR_APLICA_CENTRAL."/lib/exportExcel.php", "post", "Informe de Trazabilidad ", "frm_trazab\" id=\"frm_trazabID");
                echo '<input type="image" align="left" style="margin-left:25px;" name="botondeenvio" onclick="expTabExcelDesp()" src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" >
				<br>';
        $mHtml = "<table border='1' id='export' class='table table-bordered'>";
        if (sizeof($_INFORM) > 0) {
            
            $mHtml .="<thead>";
            $mHtml .= "<tr>";
            $mHtml .= "<th class=cellHead >Nit</th>";
            $mHtml .= "<th class=cellHead >Despacho</th>";
            $mHtml .= "<th class=cellHead >Sitio de seguimiento</th>";
            $mHtml .= "<th class=cellHead >Novedad</th>";
            $mHtml .= "<th class=cellHead >Observaci&oacute;n del Controlador</th>";
            $mHtml .= "<th class=cellHead >Fecha y Hora</th>";
            $mHtml .= "<th class=cellHead >Tiempo</th>";
            $mHtml .= "<th class=cellHead >Diferencia T</th>";
            $mHtml .= "<th class=cellHead >Cumplimiento Seguimiento</th>";
            $mHtml .= "<th class=cellHead >Porcentaje de Cumplimiento</th>";
            $mHtml .="</tr>";
            $mHtml .="</thead>";
            $mHtml .="<tbody>";
            $count = 1;
            foreach ($_INFORM as $key => $row) {
                $mArrayNove = array_merge(InformViajes::getDespacContro($row['num_viajex']), InformViajes::getDespacNoveda($row['num_viajex']));
                if(sizeof($mArrayNove)==0){
                    $sizeNovedad=1;
                }else{
                    $sizeNovedad=sizeof($mArrayNove);
                }
                $mSizeNoved =  $sizeNovedad;
    
                $horainicio="";
                $horafin="";
                $hours="";
                $resultsegui=0;
                $resultsegui= InformViajes::getTiempoTransport($row['cod_transp'], $row['cod_tipdes']) ;
                $resprom=0;
                if($mSizeNoved > 1){
                    for ($y = 0; $y < $mSizeNoved; $y++) {
                        if($y == 0){
                            $horainicio= strtotime($mArrayNove[$y][3]);
                            $result=0;
                        }else{
                            $horafin=$horainicio;
                            $horainicio= strtotime($mArrayNove[$y][3]);
                            $diff = abs($horainicio - $horafin); 
                            $years = floor($diff / (365*60*60*24));
                            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                            $hours = floor(($diff - $years * 365*60*60*24  - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
                            $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24- $hours*60*60)/ 60); 
                            $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));
                            $result=($hours * 60) + $minutes;
                            // printf("%d years, %d months, %d days, %d hours, " . "%d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds);  
                            // sacar Promedio de efectividad del seguimiento
                            $operacc=$resultsegui-$result;
                                if($operacc >=0){
                                    $resprom++ ;
                                }
                        }                            
                    }
                    $promediofin=($resprom * 100)/($mSizeNoved-1);
                    
                    for ($x = 0; $x < $mSizeNoved; $x++) {
                        $nomSitiox = $mArrayNove[$x][0] == '' ? $mArrayNove[$x][4] : $mArrayNove[$x][0];
                        $mHtml .= '<tr>';
                        $mHtml .= "<td class='cellInfo' align ='left' >".($row['cod_transp'] !='' ? $row['cod_transp']:'')."</td>"; //Nit empresa de Transporte
                        $mHtml .= "<td class='cellInfo' align ='left' >".$row['num_viajex']."</td>"; //Viaje
                        $mHtml .= '<td class="cellInfo" align ="left" >'.$nomSitiox.'&nbsp;</td>'; //Sitio de Seguimiento
                        $mHtml .= '<td class="cellInfo" align ="left">'.$mArrayNove[$x][1].'&nbsp;</td>'; //Novedad
                        $mHtml .= '<td class="cellInfo" align ="left">'.$mArrayNove[$x][2].'&nbsp;</td>'; //Observacion del controlador
                        $mHtml .= '<td class="cellInfo" align ="left">'.$mArrayNove[$x][3].'&nbsp;</td>'; //Fecha y Hora
                        
                        
                        $horafin=$horainicio;
                        $horainicio= strtotime($mArrayNove[$x][3]);
                        $diff = abs($horainicio - $horafin); 
                        $years = floor($diff / (365*60*60*24));
                        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                        $hours = floor(($diff - $years * 365*60*60*24  - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
                        $minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24- $hours*60*60)/ 60); 
                        $seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minutes*60));
                        $result=($hours * 60) + $minutes;
                        
                        $mHtml .= '<td class="cellInfo" width="12%">'.($result !='' ? $result:'&nbsp;').'</td>'; //Tiempo
                        $cal=($resultsegui-$result);
                        $mHtml .= '<td class="cellInfo" width="12%">'.($cal !=0 ? $cal:'&nbsp;').'</td>'; //Diferencia T
                        $opera=$resultsegui-$result;
                        if($opera >=0){
                            $resseg="Si";
                        }else{
                            $resseg="No";
                        }
                        $mHtml .= '<td class="cellInfo" width="12%" >'.$resseg.' &nbsp;</td>'; //Cumplimiento Seguimiento
                        $mHtml .= '<td class="cellInfo" align ="left" width="12%">'.($promediofin !=0 ? round($promediofin,2):0).'%</td>'; //Porcentaje de Cumplimiento
                        $mHtml .='</tr>';
                    }
                    
                }
                $count++;
                
            }
            $mHtml .="</tbody>";
        }

        $mHtml .="</table>";
        
        //$_SESSION[xls_InformViajes] = $mHtml;
        echo $mHtml;
        $archivo = "informe_operaciones".date("Y_m_d_H_i").".xls";
        $mHtml2 ='<input type="hidden" name="standa" id="standaID" value="'.DIR_APLICA_CENTRAL.'">';
        $mHtml2 .='<input type="hidden" name="window" id="windowID" value="'.$_REQUEST['window'].'">';
        $mHtml2 .='<input type="hidden" name="cod_servic" id="cod_servicID" value="'.$_REQUEST['cod_servic'].'">';
        $mHtml2 .='<input type="hidden" name="cod_ciuori" id="cod_ciuoriID">';
        $mHtml2 .='<input type="hidden" name="cod_ciudes" id="cod_ciudesID">';
        $mHtml2 .='<input type="hidden" name="cod_conduc" id="cod_conducID">';
        $mHtml2 .='<input type="hidden" name="nameFile" id="nameFileID" value="'.$archivo.'">';
        $mHtml2 .='<input type="hidden" name="OptionExcel" id="OptionExcelID" value="_REQUEST">';
        $mHtml2 .='<input type="hidden" name="exportExcel" id="exportExcelID" value="">';
        echo $mHtml2;
        echo "<script>$.unblockUI();</script>";
        $formulario->cerrar();
    }

    function toFecha($date) {
        $fecha = explode(" ", $date);
        $mes='';
        $fec1 = explode("-", $fecha[0]);
        switch ((int) $fec1[1]) {
            case 1:
            	$mes = 'ENERO';
                break;
            case 2:
            	$mes = 'FEBRERO';
                break;
            case 3:
            	$mes = 'MARZO';
                break;
            case 4:
            	$mes = 'ABRIL';
                break;
            case 5:
            	$mes = 'MAYO';
                break;
            case 6:
            	$mes = 'JUNIO';
                break;
            case 7:
            	$mes = 'JULIO';
                break;
            case 8:
            	$mes = 'AGOSTO';
                break;
            case 9:
            	$mes = 'SEPTIEMBRE';
                break;
            case 10:
            	$mes = 'OCTUBRE';
                break;
            case 11:
            	$mes = 'NOVIEMBRE';
                break;
            case 12:
            	$mes = 'DICIEMBRE';
                break;
        }

        return $mes.' '.$fec1[2].' DE '.$fec1[0].' '.$fecha[1];
    }

    function Listar() {
        if ($_REQUEST['fec_inicia'] == NULL || $_REQUEST['fec_inicia'] == '') {
            $fec_actual = strtotime('-7 day', strtotime(date('Y-m-d')));
            $_REQUEST['fec_inicia'] = date('Y-m-d', $fec_actual);
        }

        if ($_REQUEST['fec_finali'] == NULL || $_REQUEST['fec_finali'] == ''){
            $_REQUEST['fec_finali'] = date('Y-m-d');
        }

        include_once( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );
        echo "<link rel=\"stylesheet\" href=\"../".DIR_APLICA_CENTRAL."/estilos/dinamic_list.css\" type=\"text/css\">";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/dinamic_list.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/multiselect/jquery.multiselect.filter.min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/multiselect/jquery.multiselect.min.js\"></script>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_operac_genera.js\"></script>\n";
        

        $mSelect = "SELECT cod_tipdes, nom_tipdes 
                      FROM ".BASE_DATOS.".tab_genera_tipdes 
                  GROUP BY 1 
                  ORDER BY 2";
        $consulta = new Consulta($mSelect, $this->conexion);
        $_TIPDES = $consulta->ret_matriz();

        $_NOVEDA = InformViajes::getNovedades();
        $_TRANSP = InformViajes::getTransport();
        $_TIPINF = array(array(1,'General'),array(2,'seguimiento'));
        /*         * *********************** FOMULARIO ************************ */
        $formulario = new Formulario("index.php", "post", "Informe de Operaciones", "form\" id=\"formID");

        $formulario->texto("Fecha Inicial:", "text", "fec_inicia\" readonly id=\"fec_iniciaID", 0, 10, 10, "", $_REQUEST['fec_inicia']);
        $formulario->texto("Fecha Final:", "text", "fec_finali\" readonly id=\"fec_finaliID", 1, 10, 10, "", $_REQUEST['fec_finali']);

        $formulario->texto("No. Viaje", "text", "num_viajex\" id=\"num_viajexID", 1, 15, 15, "", $_REQUEST['num_viajex']);

        $formulario->texto("Placa:", "text", "num_placax\" id=\"num_placaxID", 0, 15, 15, "", $_REQUEST['num_placax']);
        $formulario->lista("Tipo Despachos:", "cod_tipdes\" id=\"cod_tipdesID", array_merge($this->cNull, $_TIPDES), 1);
        
        
        $formulario->lista("Transportadora", "cod_transp\" id=\"cod_transpID", array_merge($this->cNullt, $_TRANSP), 0);
        $formulario->lista("Tipo informe", "tip_inform\" id=\"tip_informID", $_TIPINF, 1);
        
        $formulario->lista("Novedad:", "cod_noveda\" id=\"cod_novedaID", array_merge($this->cNull, $_NOVEDA), 1);

        $formulario->nueva_tabla2(0,0,0,'nov1');
        $formulario->linea("Novedades", 1, "t");
        $formulario->nueva_tabla2(0,0,0,'nov2');
        $formulario->radio("Si", "ind_noveda\" id=\"ind_novedaID", 1, 1, 0);
        $formulario->radio("No", "ind_noveda\" id=\"ind_novedaID", 2, 0, 1);

        $formulario->nueva_tabla();
        $formulario->linea("Estado", 1, "t");
        $formulario->nueva_tabla();
        $formulario->radio("Finalizados", "ind_finali\" id=\"ind_finaliID", 1, 1, 0);
        $formulario->radio("En Ruta", "ind_finali\" id=\"ind_finaliID", 2, 0, 1);

        $formulario->nueva_tabla();
        $formulario->botoni("Buscar", "", 0);

        $formulario->nueva_tabla();
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("opcion\" id=\"opcionID",'', 0);
        $formulario->oculto("cod_servic", $_REQUEST['cod_servic'], 0);
        $formulario->oculto("trans_\" id=\"trans_", 0, 0);
        $formulario->cerrar();
    }

    private function getNovedades() {
        $mSelect = '(
                    SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                    FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl = "1" AND nom_noveda LIKE "%NER /%" 
                  )
                  UNION ALL 
                  (
                   SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                     FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl ="1" AND 
                         ( nom_noveda LIKE "%NEC /%" OR  nom_noveda LIKE "%NICC /%" )
                  )
                  UNION ALL 
                  (
                   SELECT cod_noveda, UPPER( nom_noveda ) AS nom_noveda
                     FROM '.BASE_DATOS.'.tab_genera_noveda 
                    WHERE ind_visibl = "1" AND 
                          nom_noveda LIKE "%NED /%"
                  ) ';
        $consulta = new Consulta($mSelect, $this->conexion);
        return $_NOVEDA = $consulta->ret_matriz('i');
    }

    private function getDespacNoveda($numDespac) {
        $mSql = "SELECT c.nom_sitiox AS 'Sitio de seguimiento', 
                    b.nom_noveda AS 'Novedad', 
                    a.des_noveda AS 'Observaci&oacute;n del Controlador', 
                    a.fec_noveda AS 'Fecha y Hora', 
                    d.nom_contro AS 'Sitio de seguimiento 2', 
                    a.cod_sitiox
               FROM ".BASE_DATOS.".tab_despac_noveda a 
         INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                 ON a.cod_noveda = b.cod_noveda 
         INNER JOIN ".BASE_DATOS.".tab_despac_sitio c 
                 ON a.cod_sitiox = c.cod_sitiox 
          LEFT JOIN ".BASE_DATOS.".tab_genera_contro d 
                 ON a.cod_contro = d.cod_contro 
              WHERE a.num_despac = '$numDespac'
            ";
        $mConsult = new Consulta($mSql, $this->conexion);
        return $mResult = $mConsult->ret_matrix('i');
    }

    private function getDespacContro($numDespac) {
        $mSql = "SELECT c.nom_sitiox AS 'Sitio de seguimiento', 
                    b.nom_noveda AS 'Novedad', 
                    a.obs_contro AS 'Observaci&oacute;n del Controlador', 
                    a.fec_contro AS 'Fecha y Hora', 
                    d.nom_contro AS 'Sitio de seguimiento 2', 
                    a.cod_sitiox
               FROM ".BASE_DATOS.".tab_despac_contro a 
         INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                 ON a.cod_noveda = b.cod_noveda 
         INNER JOIN ".BASE_DATOS.".tab_despac_sitio c 
                 ON a.cod_sitiox = c.cod_sitiox 
          LEFT JOIN ".BASE_DATOS.".tab_genera_contro d 
                 ON a.cod_contro = d.cod_contro 
              WHERE a.num_despac = '$numDespac'
              ORDER BY a.fec_contro ASC
            ";
        $mConsult = new Consulta($mSql, $this->conexion);
        return $mResult = $mConsult->ret_matrix('i');
    }


    //Metodo que obtiene las transportadora de la base de datos
    private function getTransport(){
        $mSql=" SELECT 
                    b.cod_tercer, 
                    b.nom_tercer 
                FROM 
                    ".BASE_DATOS.".tab_tercer_emptra a 
                INNER JOIN tab_tercer_tercer b ON a.cod_tercer = b.cod_tercer 
                AND b.cod_estado = 1
                ORDER BY b.nom_tercer ASC
                ;
        ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult = $mConsult->ret_matriz('a');
        return $this->utf8_converter($mResult);
    }

//Metodo que obtiene el tiempo contratado en transito de una transportadora de la base de datos
function getTiempoTransport($transp, $tipserv){
   
    $campo="";
    switch ($tipserv) {
        case 1:// Urbano
            $campo='tie_conurb as "Urbano"';
            break;
        case 2:// Nacional
            $campo='tie_contro as "Nacional"';
            break;
        case 3://Importacion
            $campo='tie_traimp as "Importacion"';
            break;
        case 4://Exportacion
            $campo='tie_traexp as "Exportacion"';
            break;
        case 5://XD Tramo 1
            $campo='tie_tratr1 as "XD Tramo 1"';
            break;
        case 6://XD Tramo 2
            $campo='tie_tratr2 as "XD Tramo 2"';
            break;
        
    }
    $mSql="";
    if($campo!=""){
        $mSql=' SELECT num_consec , cod_transp,cod_tipser,'.$campo.' 
    FROM tab_transp_tipser 
    WHERE cod_transp LIKE "%'.$transp.'%" 
    ORDER BY tab_transp_tipser.num_consec
    DESC limit 1
    ';
    
    $mConsult = new Consulta($mSql, $this->conexion);
    $mResult = $mConsult->ret_matriz('a');
   
    $resulttipseg=$mResult[0][3];
    }else{
        // Paqueteo o Reexpedicion
        $resulttipseg=10;
    }
    

    return $resulttipseg;
}



    function utf8_converter($array)
    {
    array_walk_recursive($array, function(&$item, $key){
        if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
        }
    });
    return $array;
    }

}

$_INFORM = new InformViajes($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>