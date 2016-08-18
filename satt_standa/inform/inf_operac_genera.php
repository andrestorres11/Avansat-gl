<?php

header('Content-Type: text/html; charset=UTF-8');
ini_set('memory_limit', '2048M');

class InformViajes {

    var $conexion,
        $cod_aplica,
        $usuario;
    var $cNull = array(array('', '- Todos -'));

    function __construct($co, $us, $ca) {
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;

        switch ($_REQUEST[opcion]) {
            case 99:
                $this->getInform();
                break;

            case 1:
                $this->exportExcel();
                break;

            default:
                $this->Listar();
                break;
        }
    }

    function getInform() {
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/informes.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/homolo.css' type='text/css'>\n";

        $mSelect1 = "SELECT a.num_despac, a.cod_manifi, a.fec_despac,
                       b.nom_tipdes, c.nom_paisxx, d.nom_depart, 
                       e.nom_ciudad, f.nom_paisxx, g.nom_depart, 
                       h.nom_ciudad, a.cod_operad, a.fec_citcar, 
                       a.hor_citcar, a.nom_sitcar, a.val_flecon, 
                       a.val_despac, a.val_antici, a.val_retefu, 
                       a.nom_carpag, a.nom_despag, a.cod_agedes, 
                       a.val_pesoxx, a.obs_despac, t.fec_llegad, 
                       a.obs_llegad, a.ind_planru, i.nom_rutasx, 
                       a.ind_anulad, a.num_poliza, a.con_telef1, 
                       a.con_telmov, a.con_domici, a.gps_operad, 
                       a.gps_usuari, a.gps_paswor, a.gps_idxxxx, 
                       a.ema_client, j.abr_tercer, a.num_consol, 
                       a.num_solici, a.cod_conduc, a.num_placax,
                       a.num_trayle, a.tip_vehicu, a.num_pedido, 
                       a.ano_modelo, l.nom_marcax, m.nom_lineax, 
                       n.nom_colorx, a.nom_conduc, o.nom_ciudad,
                       a.cod_config, q.nom_carroc, a.num_chasis, 
                       a.num_motorx, a.num_soatxx, a.dat_vigsoa, 
                       a.nom_ciasoa, a.num_tarpro, a.cat_licenc, 
                       a.cod_poseed, a.nom_poseed, r.nom_ciudad, 
                       a.dir_poseed, a.cod_estado, a.nom_estado, 
                       a.tip_transp, a.cod_instal, s.nom_produc, 
                       a.ind_modifi, a.ind_anudes, a.num_dessat,
                       a.cod_respon, a.msg_respon
                  FROM ".BASE_DATOS.".tab_despac_corona a
             LEFT JOIN ".BASE_DATOS.".tab_genera_tipdes b
                    ON a.cod_tipdes = b.cod_tipdes
             LEFT JOIN ".BASE_DATOS.".tab_genera_paises c
                    ON a.cod_paiori = c.cod_paisxx
             LEFT JOIN ".BASE_DATOS.".tab_genera_depart d
                    ON a.cod_paiori = d.cod_paisxx
                   AND a.cod_depori = d.cod_depart
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad e
                    ON a.cod_paiori = e.cod_paisxx
                   AND a.cod_depori = e.cod_depart
                   AND a.cod_ciuori = e.cod_ciudad
             LEFT JOIN ".BASE_DATOS.".tab_genera_paises f
                    ON a.cod_paides = f.cod_paisxx
             LEFT JOIN ".BASE_DATOS.".tab_genera_depart g
                    ON a.cod_paides = g.cod_paisxx
                   AND a.cod_depdes = g.cod_depart
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad h
                    ON a.cod_paides = h.cod_paisxx
                   AND a.cod_depdes = h.cod_depart
                   AND a.cod_ciudes = h.cod_ciudad
             LEFT JOIN ".BASE_DATOS.".tab_genera_rutasx i
                    ON a.cod_rutasx = i.cod_rutasx
             LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer j
                    ON a.cod_asegur = j.cod_tercer
             LEFT JOIN ".BASE_DATOS.".tab_genera_marcas l
                    ON a.cod_marcax = l.cod_marcax
             LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas m
                    ON a.cod_marcax = m.cod_marcax
                   AND a.cod_lineax = m.cod_lineax
             LEFT JOIN ".BASE_DATOS.".tab_vehige_colore n
                    ON a.cod_colorx = n.cod_colorx
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad o
                    ON a.ciu_conduc = o.cod_ciudad
             LEFT JOIN ".BASE_DATOS.".tab_vehige_config p
                    ON a.cod_config = p.num_config
             LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc q
                    ON a.cod_carroc = q.cod_carroc
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad r
                    ON a.ciu_poseed = r.cod_ciudad
             LEFT JOIN ".BASE_DATOS.".tab_genera_produc s
                    ON a.cod_mercan = s.cod_produc ";

        if( $_REQUEST['ind_finali'] == '1' ) {
        	$mSelect1 .= "
             LEFT JOIN ".BASE_DATOS.".tab_despac_despac t 
                    ON a.num_dessat = t.num_despac ";
        } else {
        	$mSelect1 .= "
        	 INNER JOIN ".BASE_DATOS.".tab_despac_despac t 
                     ON a.num_dessat = t.num_despac
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige z ON t.num_despac = z.num_despac 
					AND t.fec_salida IS NOT NULL 
					AND t.fec_salida <= NOW() 
					AND (t.fec_llegad IS NULL OR t.fec_llegad = '0000-00-00 00:00:00')
					AND t.ind_planru = 'S' 
					AND t.ind_anulad = 'R'
					AND z.ind_activo = 'S' ";

        }

        $mSelect2 .="WHERE a.fec_despac BETWEEN '".$_REQUEST['fec_inicia']." 00:00:00' AND '".$_REQUEST['fec_finali']." 23:59:59' ";

        if( $_REQUEST['ind_finali'] == '1' ) {
        	$mSelect2 .= "AND t.fec_llegad IS NOT NULL ";
        }

        if ($_REQUEST['num_dessat'] != '')
            $mSelect2 .= " AND a.num_dessat = '".$_REQUEST['num_dessat']."'";

        if ($_REQUEST['num_viajex'] != '')
            $mSelect2 .= " AND a.num_despac = '".$_REQUEST['num_viajex']."'";

        if ($_REQUEST['num_solici'] != '')
            $mSelect2 .= " AND a.num_solici = '".$_REQUEST['num_solici']."'";

        if ($_REQUEST['num_pedido'] != '')
            $mSelect2 .= " AND a.num_pedido = '".$_REQUEST['num_pedido']."'";

        if ($_REQUEST['num_placax'] != '')
            $mSelect2 .= " AND a.num_placax = '".$_REQUEST['num_placax']."'";

        if ($_REQUEST['cod_tipdes'] != '')
            $mSelect2 .= " AND a.cod_tipdes = '".$_REQUEST['cod_tipdes']."'";


        if ($_REQUEST['cod_noveda'] != '') {
            $mSql1 = $mSelect1;
            $mSql1 .= "INNER JOIN ".BASE_DATOS.".tab_despac_noveda y
                           ON a.num_dessat = y.num_despac ";
            $mSql1 .= $mSelect2;
            $mSql1 .= " AND y.cod_noveda = '".$_REQUEST['cod_noveda']."'";

            $mSql2 = $mSelect1;
            $mSql2 .= "INNER JOIN ".BASE_DATOS.".tab_despac_contro y
                           ON a.num_dessat = y.num_despac ";
            $mSql2 .= $mSelect2;
            $mSql2 .= " AND y.cod_noveda = '".$_REQUEST['cod_noveda']."'";

            $mSelect = "( ".$mSql1." GROUP BY a.num_despac ) 
                  UNION  
                  ( ".$mSql2." GROUP BY a.num_despac ) ";
        } else {
            $mSelect = $mSelect1 . $mSelect2 . " GROUP BY a.num_despac ";
        }

        $consulta = new Consulta($mSelect, $this->conexion);
        $_INFORM = $consulta->ret_matriz();


        $formulario = new Formulario("?", "post", "Informe de Trazabilidad ", "frm_trazab\" id=\"frm_trazabID");

        echo '<a href="index.php?cod_servic='.$_REQUEST['cod_servic'].'&window=central&opcion=1 "target="centralFrame"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" border="0"></a>';

        $mHtml = "<table border='1'>";
        if (sizeof($_INFORM) > 0) {
            $size = $_REQUEST['ind_noveda'] == '1' ? '72' : '68';
            $mHtml .= "<tr>";
            $mHtml .= "<th class=cellHead colspan='$size' >SE ENCONTRARON ".sizeof($_INFORM)." REGISTROS</th>";
            $mHtml .= "</tr>";

            $mHtml .= "<tr>";
            $mHtml .= "<th class=cellHead >No.</th>";
            $mHtml .= "<th class=cellHead >Viaje</th>";
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
            $mHtml .= "<th class=cellHead >Consolidado</th>";
            $mHtml .= "<th class=cellHead >Solicitud</th>";
            $mHtml .= "<th class=cellHead >Pedido</th>";
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
            $mHtml .= "<th class=cellHead >Estado Viaje</th>";
            $mHtml .= "<th class=cellHead >Tipo Transportadora</th>";
            $mHtml .= "<th class=cellHead >Lugar Instalaci&oacute;n</th>";
            $mHtml .= "<th class=cellHead >Mercanc&iacute;a</th>";
            $mHtml .= "<th class=cellHead >Modificado</th>";
            $mHtml .= "<th class=cellHead >Anulado</th>";
            $mHtml .= "<th class=cellHead >No. Despacho SATT</th>";
            $mHtml .= "<th class=cellHead >Cod. Respuesta Astrans</th>";
            $mHtml .= "<th class=cellHead >Mensaje Respuesta Astrans</th>";
            if ($_REQUEST['ind_noveda'] == '1') {
                $mHtml .= "<th class=cellHead >Sitio de seguimiento</th>";
                $mHtml .= "<th class=cellHead >Novedad</th>";
                $mHtml .= "<th class=cellHead >Observaci&oacute;n del Controlador</th>";
                $mHtml .= "<th class=cellHead >Fecha y Hora</th>";
            }
            $mHtml .= "</tr>";

            $count = 1;
            foreach ($_INFORM as $row) {
                $mArrayNove = array_merge(InformViajes::getDespacContro($row[71]), InformViajes::getDespacNoveda($row[71]));
                $mSizeNoved = $_REQUEST['ind_noveda'] == '1' ? sizeof($mArrayNove) : '1';

                if ($row['tip_transp'] == '1')
                    $tip_transp = 'FLOTA PROPIA';
                elseif ($row['tip_transp'] == '2')
                    $tip_transp = 'TERCEROS';
                elseif ($row['tip_transp'] == '3')
                    $tip_transp = 'EMPRESAS';
                else
                    $tip_transp = 'DESCONOCIDA';

                if ($row[71]) {
                    $href1 = '<a href="?cod_servic=3302&window=central&despac='.$row[71].'&opcion=1" target="_blank">';
                    $href2 = '</a>';
                } else {
                    $href1 = '';
                    $href2 = '';
                }

                $mHtml .= "<tr class='row'>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".$count."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".$href1.$row[0].$href2."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".$row[1]."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".$this->toFecha($row[2])."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".strtoupper($row[3])."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".strtoupper($row[4])."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".strtoupper($row[5])."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".strtoupper($row[6])."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".strtoupper($row[7])."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".strtoupper($row[8])."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".strtoupper($row[9])."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".$row[10]."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".$this->toFecha($row[11]." ".$row[12])."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[13] != '' ? $row[13] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".number_format($row[14], 0, '.', '.')."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".number_format($row[15], 0, '.', '.')."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".number_format($row[16], 0, '.', '.')."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".number_format($row[17], 0, '.', '.')."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".$row[18]."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".$row[19]."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".$row[20]."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".$row[21]."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[22] != '' ? $row[22] : '-' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[23] != '' ? $this->toFecha($row[23]) : '-' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[24] != ' ' && $row[24] != '' ? $row[24] : '-' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[26] != '' ? $row[26] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[25] == 'S' ? 'SI' : 'NO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[27] == 'A' ? 'SI' : 'NO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[40] != '' ? $row[40] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[49] != '' ? $row[49] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[29] != '' ? $row[29] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[30] != '' ? $row[30] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[31] != '' ? $row[31] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[50] != '' ? $row[50] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[59] != '' ? $row[59] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[28] != '' ? $row[28] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[37] != '' ? $row[37] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[38] != '' ? $row[38] : 'N/A' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[39] != '' ? $row[39] : 'N/A' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[44] != '' ? $row[44] : 'N/A' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[41] != '' ? $row[41] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[42] != '' ? $row[42] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[43] != '' ? $row[43] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[45] != '' ? $row[45] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[46] != '' ? $row[46] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[47] != '' ? $row[47] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[48] != '' ? $row[48] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[51] != '' ? $row[51] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[52] != '' ? $row[52] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[53] != '' ? $row[53] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[54] != '' ? $row[54] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[55] != '' ? $row[55] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[56] != '' ? $this->toFecha($row[56]) : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[57] != '' ? $row[57] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[58] != '' ? $row[58] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[60] != '' ? $row[60] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[61] != '' ? $row[61] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[63] != '' ? $row[63] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[62] != '' ? $row[62] : 'DESCONOCIDA' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[64] != '' ? $row[64]." - ".$row[65] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".$tip_transp."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[67] != '' ? $row[67] : 'N/A' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[68] != '' ? $row[68] : 'DESCONOCIDO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[69] == '1' ? 'SI' : 'NO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[70] == '1' ? 'SI' : 'NO' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[71] != '' ? $row[71] : 'N/A' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[72] != '' ? $row[72] : 'N/A' )."</td>";
                $mHtml .= "<td class='cellInfo' align ='left' rowspan='$mSizeNoved'>".( $row[73] != '' ? $row[73] : 'N/A' )."</td>";

                if ($_REQUEST['ind_noveda'] == '1') {
                    if ($mSizeNoved > 0) {
                        for ($x = 0; $x < $mSizeNoved; $x++) {
                            $nomSitiox = $mArrayNove[$x][0] == '' ? $mArrayNove[$x][4] : $mArrayNove[$x][0];
                            $mHtml .= $x == 0 ? '' : '<tr>';
                            $mHtml .= '<td class="cellInfo" >'.$nomSitiox.'&nbsp;</td>';
                            $mHtml .= '<td class="cellInfo" >'.$mArrayNove[$x][1].'&nbsp;</td>';
                            $mHtml .= '<td class="cellInfo" >'.$mArrayNove[$x][2].'&nbsp;</td>';
                            $mHtml .= '<td class="cellInfo" >'.$mArrayNove[$x][3].'&nbsp;</td>';
                            $mHtml .= $x == 0 ? '' : '</tr>';
                        }
                    } else {
                        $mHtml .= '<td colspan="4" align="center"><b>SIN NOVEDAD</b></td>';
                    }
                }

                $mHtml .= "</tr>";
                $count++;
            }
        } else {
            $mHtml .= "<tr>";
            $mHtml .= "<th class=cellHead width='3%' >NO SE ENCONTRARON REGISTROS</th>";
            $mHtml .= "</tr>";
        }
        $mHtml .= "</table>";

        $_SESSION[xls_InformViajes] = $mHtml;
        echo $mHtml;

        $formulario->cerrar();
    }

    function toFecha($date) {
        $fecha = explode(" ", $date);

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

        echo '<script>
	        jQuery(function($) 
	        {
				$( "#fec_iniciaID, #fec_finaliID" ).datepicker({
					changeMonth: true,
					changeYear: true
				});

				$.mask.definitions["A"]="[12]";
				$.mask.definitions["M"]="[01]";
				$.mask.definitions["D"]="[0123]";

				$.mask.definitions["H"]="[012]";
				$.mask.definitions["N"]="[012345]";
				$.mask.definitions["n"]="[0123456789]";

				$( "#fec_iniciaID, #fec_finaliID" ).mask("Annn-Mn-Dn");
	        });
        </script>';

        $mSelect = "SELECT cod_tipdes, nom_tipdes 
                      FROM ".BASE_DATOS.".tab_genera_tipdes 
                  GROUP BY 1 
                  ORDER BY 2";
        $consulta = new Consulta($mSelect, $this->conexion);
        $_TIPDES = $consulta->ret_matriz();

        $_NOVEDA = InformViajes::getNovedades();

        /*         * *********************** FOMULARIO ************************ */
        $formulario = new Formulario("index.php", "post", "Informe de Operaciones", "form\" id=\"formID");

        $formulario->texto("Fecha Inicial:", "text", "fec_inicia\" readonly id=\"fec_iniciaID", 0, 10, 10, "", $_REQUEST['fec_inicia']);
        $formulario->texto("Fecha Final:", "text", "fec_finali\" readonly id=\"fec_finaliID", 1, 10, 10, "", $_REQUEST['fec_finali']);

        $formulario->texto("No. Despacho( SATT ):", "text", "num_dessat\" id=\"num_dessatID", 0, 15, 15, "", $_REQUEST['num_dessat']);
        $formulario->texto("No. Viaje", "text", "num_viajex\" id=\"num_viajexID", 1, 15, 15, "", $_REQUEST['num_viajex']);

        $formulario->texto("No. Solicitud:", "text", "num_solici\" id=\"num_soliciID", 0, 15, 15, "", $_REQUEST['num_solici']);
        $formulario->texto("No. Pedido", "text", "num_pedido\" id=\"num_pedidoID", 1, 15, 15, "", $_REQUEST['num_pedido']);

        $formulario->texto("Placa:", "text", "num_placax\" id=\"num_placaxID", 0, 15, 15, "", $_REQUEST['num_placax']);
        $formulario->lista("Tipo Despachos:", "cod_tipdes\" id=\"cod_tipdesID", array_merge($this->cNull, $_TIPDES), 1);

        $formulario->lista("Novedad:", "cod_noveda\" id=\"cod_novedaID", array_merge($this->cNull, $_NOVEDA), 1);

        $formulario->nueva_tabla();
        $formulario->linea("Novedades", 1, "t");
        $formulario->nueva_tabla();
        $formulario->radio("Si", "ind_noveda\" id=\"ind_novedaID", 1, 1, 0);
        $formulario->radio("No", "ind_noveda\" id=\"ind_novedaID", 2, 0, 1);

        $formulario->nueva_tabla();
        $formulario->linea("Estado", 1, "t");
        $formulario->nueva_tabla();
        $formulario->radio("Finalizados", "ind_finali\" id=\"ind_finaliID", 1, 1, 0);
        $formulario->radio("En Ruta", "ind_finali\" id=\"ind_finaliID", 2, 0, 1);

        $formulario->nueva_tabla();
        $formulario->botoni("Buscar", "$('#formID').submit();", 0);

        $formulario->nueva_tabla();
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("opcion\" id=\"opcionID", 99, 0);
        $formulario->oculto("cod_servic", $_REQUEST['cod_servic'], 0);
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
            ";
        $mConsult = new Consulta($mSql, $this->conexion);
        return $mResult = $mConsult->ret_matrix('i');
    }

    private function exportExcel() {
        session_start();
        $archivo = "informe_operaciones".date("Y_m_d_H_i").".xls";
        header('Content-Type: application/octetstream');
        header('Expires: 0');
        header('Content-Disposition: attachment; filename="'.$archivo.'"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        ob_clean();
        echo $HTML = $_SESSION[xls_InformViajes];
    }
}

$_INFORM = new InformViajes($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>