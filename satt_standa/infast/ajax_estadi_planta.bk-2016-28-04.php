<?php
ini_set('display_errors', false);
session_start();

class AjaxEstadiaPlanta
{
  var $conexion;
  var $NIT_CORONA = '860068121';
  public function __construct()
  {
    $_AJAX = $_REQUEST;
    include_once('../lib/bd/seguridad/aplica_filtro_perfil_lib.inc');
    include_once('../lib/bd/seguridad/aplica_filtro_usuari_lib.inc');
    include_once('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');
    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }
  
  public function expInformExcel()
  {    
    $archivo = "Indicador_Estadia_Planta_".date( "Y_m_d" ).".xls";
    header('Content-Type: application/octetstream');
    header('Expires: 0');
    header('Content-Disposition: attachment; filename="'.$archivo.'"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    echo $_SESSION['LIST_TOTAL']; 
  }
  
  private function Style()
  {
    echo '
        <style>
        .CellHead
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:13px;
          background-color: #35650F;
          color:#FFFFFF;
          padding: 4px;
        }
        
        .CellHead2
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:13px;
          background-color: #35650F;
          color:#FFFFFF;
          padding: 4px;
        }
        
        .cellInfo1
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #EBF8E2;
          padding: 2px;
        }
        
        .cellInfo2
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #DEDFDE;
          padding: 2px;
        }
        
        .cellInfo
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #FFFFFF;
          padding: 2px;
        }
        
        tr.row:hover  td
        {
          background-color: #9ad9ae;
        }
        
        /*.StyleDIV
          {
            background-color: rgb(240, 240, 240);
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 99%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .Style2DIV
          {
            background-color: #FFFFFF;
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 96%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }*/
          .TRform
          {
            padding-right:3px; 
            padding-top:15px; 
            font-family:Trebuchet MS, Verdana, Arial; 
            font-size:12px;
          }
        </style>';
  }
  
  public function getInform( $mData )
  {
    $this -> Style();
    
    $mData['date_inicia'] = $mData['fec_inicia']." ".$mData['hor_inicia'];
    $mData['date_finali'] = $mData['fec_finali']." ".$mData['hor_finali'];
    
    $mSelect = "SELECT b.num_despac, a.cod_ciuori, a.cod_mercan, a.tip_vehicu,
                  d.cod_clasex
                  FROM ".BASE_DATOS.".tab_despac_corona a 
             LEFT JOIN ".BASE_DATOS.".tab_homolo_config d
                    ON d.cod_homolo = a.tip_vehicu, 
                       ".BASE_DATOS.".tab_despac_despac b,
                       ".BASE_DATOS.".tab_despac_vehige c
                 WHERE a.num_dessat = b.num_despac
                   AND a.num_dessat = c.num_despac
                   AND b.fec_salida IS NOT NULL
                   AND b.fec_llegad IS NOT NULL
                   AND b.ind_anulad != 'A'
                   AND c.ind_activo = 'S'
                   AND a.fec_plalle IS NOT NULL 
                   AND a.fec_plalle != '0000-00-00 00:00:00' 
                   AND a.fec_salida IS NOT NULL 
                   AND a.fec_salida != '0000-00-00 00:00:00' 
                   AND b.fec_salida BETWEEN '".$mData['date_inicia']."' AND '".$mData['date_finali']."' ";
    
    if( $mData['cod_mercan'] != '' )
    {
      $mSelect .= " AND a.cod_mercan = '".str_replace( '/-/', '&', $mData['cod_mercan'] )."' ";
    }
    if( $mData['cod_ciuori'] != '' )
    {
      $mSelect .= " AND a.cod_ciuori = '".$mData['cod_ciuori']."' ";
    }
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $arr_despac = $consulta -> ret_matriz();
    $_DESPAC = array();

    foreach( $arr_despac as $row )
    {
      $cod_ciuori = $row['cod_ciuori'] != '' ? $row['cod_ciuori'] : 'descon';
      $cod_mercan = $row['cod_mercan'] != '' ? $row['cod_mercan'] : 'descon';
      $tip_vehicu = $row['cod_clasex'] != '' ? $row['cod_clasex'] : $row['tip_vehicu'];
      $_DESPAC[$cod_ciuori][$cod_mercan][$tip_vehicu]['totalx']++;
      $_DESPAC[$cod_ciuori][$cod_mercan][$tip_vehicu]['despac'] .= $_DESPAC[$cod_ciuori][$cod_mercan][$tip_vehicu]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
    }
    $_SESSION['DESPAC'] = $_DESPAC;

    /*echo "<pre>";
    print_r( $_DESPAC );
    echo "</pre>";
    */

    $this -> Style();
    
	$mHtml  = '<table width="100%" cellpadding="0" cellspacing="1">';
      $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="6" align="center"><b>INDICADOR ESTADIA EN PLANTA</b></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" colspan="15" align="center">PER&Iacute;ODO ENTRE: '.( $mData['fec_inicia'].' '.$mData['hor_inicia'] ).' Y '.( $mData['fec_finali'].' '.$mData['hor_finali'] ).'</td>';
      $mHtml .= '</tr>'; 

      $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead" align="center">ORIGEN</td>';
        $mHtml .= '<td class="CellHead" align="center">NEGOCIO</td>';
        $mHtml .= '<td class="CellHead" align="center">CLASE</td>';
        $mHtml .= '<td class="CellHead" align="center">TIEMPO ESTABLECIDO</td>';
        $mHtml .= '<td class="CellHead" align="center">TIEMPO PROMEDIO</td>';
        $mHtml .= '<td class="CellHead" align="center">CANTIDAD</td>';
      $mHtml .= '</tr>';
      $count = 0;
    foreach( $_DESPAC as $cod_ciuori => $despac )
    {
      $ciudad = $this -> getCiudad( $cod_ciuori );
      
      foreach( $despac as $cod_mercan => $details )
      {
        $mercan = $this -> getMercan( $cod_mercan );
        foreach( $details as $cod_clasex => $inform )
        {      
          $clasex = $this -> getClasex( $cod_clasex );
          $tie_establ = sizeof( $clasex ) > 0 ? $this -> getTiempos( $clasex[0][0], $cod_mercan ) : '0' ;
          $homolo = sizeof( $clasex ) > 0 ?  $clasex[0][1]: $cod_clasex.'(N/H)';
          $promed = $this -> getPromedios( $inform['despac'] );
          $style = $count % 2 == 0 ? 'cellInfo1' : 'cellInfo2';
          $color = $promed > $tie_establ ? '#E12626' : '#467AFF';
          $ind_clasex = sizeof( $clasex ) > 0 ? $clasex[0][0] : $cod_clasex; 
          $mHtml .= '<tr>';
            $mHtml .= '<td class="'.$style.'" align="left">'.$ciudad[0][1].'</td>';
            $mHtml .= '<td class="'.$style.'" align="left">'.$mercan[0][1].'</td>';
            $mHtml .= '<td class="'.$style.'" align="left">'.$homolo.'</td>';
            $mHtml .= '<td class="'.$style.'" align="center">'.$tie_establ.'</td>';
            $mHtml .= '<td style="color:'.$color.';" class="'.$style.'" align="center">'.$promed.'</td>';
            $mHtml .= '<td class="'.$style.'" align="center"><a style="cursor:pointer;" onclick="Details(\''.$cod_ciuori.'\', \''.$cod_mercan.'\', \''.$ind_clasex.'\' );">'.$inform['totalx'].'</a></td>';
          $mHtml .= '</tr>';
          $count++;
        }
      }
    }
    
    echo '<span id="excelID" onclick="Export();" style="color: #35650F; cursor:pointer; font-family: Trebuchet MS,Verdana,Arial; font-size: 13px;">[Excel]</span><br><br>';
    $_SESSION['LIST_TOTAL'] = $mHtml;
    echo $mHtml;
    
  }

  protected function Details( $mData )
  {
  	$_DESPAC = $_SESSION['DESPAC'];

    
    $despac_details = $_DESPAC[ $mData['cod_ciuori'] ][ $mData['cod_mercan'] ][ $mData['cod_clasex'] ];
    $num_despac = $despac_details['despac'];
    -
    $mSelect = "SELECT a.num_despac, a.cod_manifi, a.fec_despac,
                       b.nom_tipdes, c.nom_paisxx, d.nom_depart, 
                       e.nom_ciudad, f.nom_paisxx, g.nom_depart, 
                       h.nom_ciudad, a.cod_operad, a.fec_citcar, 
                       a.hor_citcar, a.nom_sitcar, a.val_flecon, 
                       a.val_despac, a.val_antici, a.val_retefu, 
                       a.nom_carpag, a.nom_despag, a.cod_agedes, 
                       a.val_pesoxx, a.obs_despac, a.fec_llegad, 
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
                       a.fec_plalle, a.fec_salida
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
                    ON a.cod_mercan = s.cod_produc
                    
                 WHERE a.num_dessat IN(".$num_despac.")";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_RESULT = $consulta -> ret_matriz();
    
    $mHtml  = '<table width="100%" cellpadding="0" cellspacing="1">';
    
    $message = 'Se Encontraron '.sizeof( $_RESULT ).' Despachos.';
    $mHtml .= '<tr>';
      $mHtml .= '<td class="CellHead" style="text-align:left;" colspan="48" align="left">'.$message.'</td>';
    $mHtml .= '<tr>';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td class="CellHead" align="center">Despacho</td>';
    $mHtml .= '<td class="CellHead" align="center">Viaje</td>';
    $mHtml .= '<td class="CellHead" align="center">Manifiesto</td>';
    $mHtml .= '<td class="CellHead" align="center">Fecha Despacho</td>';
    $mHtml .= '<td class="CellHead" align="center">Tipo Despacho</td>';
    $mHtml .= '<td class="CellHead" align="center">Origen</td>';
    $mHtml .= '<td class="CellHead" align="center">Destino</td>';
    $mHtml .= '<td class="CellHead" align="center">Fecha Llegada a Planta</td>';
    $mHtml .= '<td class="CellHead" align="center">Fecha Salida de Planta</td>';
    $mHtml .= '<td class="CellHead" align="center">Fecha Cita Cargue</td>';
    $mHtml .= '<td class="CellHead" align="center">Nombre Sitio Cargue</td>';
    $mHtml .= '<td class="CellHead" align="center">Valor Flete Conductor</td>';
    $mHtml .= '<td class="CellHead" align="center">Valor Despacho</td>';
    $mHtml .= '<td class="CellHead" align="center">Valor Anticipo</td>';
    $mHtml .= '<td class="CellHead" align="center">Valor Retefuente</td>';
    $mHtml .= '<td class="CellHead" align="center">Encargado Pagar Cargue</td>';
    $mHtml .= '<td class="CellHead" align="center">Encargado Pagar Descargue</td>';
    $mHtml .= '<td class="CellHead" align="center">Agencia</td>';
    $mHtml .= '<td class="CellHead" align="center">Peso(Kg)</td>';
    $mHtml .= '<td class="CellHead" align="center">Observaciones</td>';
    $mHtml .= '<td class="CellHead" align="center">C.C. Conductor</td>';
    $mHtml .= '<td class="CellHead" align="center">Nombre Conductor</td>';
    $mHtml .= '<td class="CellHead" align="center">Tel Conductor</td>';
    $mHtml .= '<td class="CellHead" align="center">Cel Conductor</td>';
    $mHtml .= '<td class="CellHead" align="center">Direcci&oacute;n Conductor</td>';
    $mHtml .= '<td class="CellHead" align="center">Ciudad Conductor</td>';
    $mHtml .= '<td class="CellHead" align="center">Categor&iacute;a Licencia</td>';
    $mHtml .= '<td class="CellHead" align="center">P&oacute;liza</td>';
    $mHtml .= '<td class="CellHead" align="center">Consolidado</td>';
    $mHtml .= '<td class="CellHead" align="center">Solicitud</td>';
    $mHtml .= '<td class="CellHead" align="center">Pedido</td>';
    $mHtml .= '<td class="CellHead" align="center">Placa</td>';
    $mHtml .= '<td class="CellHead" align="center">Tipo Veh&iacute;culo</td>';
    $mHtml .= '<td class="CellHead" align="center">Modelo</td>';
    $mHtml .= '<td class="CellHead" align="center">Marca</td>';
    $mHtml .= '<td class="CellHead" align="center">L&iacute;nea</td>';
    $mHtml .= '<td class="CellHead" align="center">Color</td>';
    $mHtml .= '<td class="CellHead" align="center">Configuraci&oacute;n</td>';
    $mHtml .= '<td class="CellHead" align="center">Carrocer&iacute;a</td>';
    $mHtml .= '<td class="CellHead" align="center">No. Chasis</td>';
    $mHtml .= '<td class="CellHead" align="center">No. Motor</td>';
    $mHtml .= '<td class="CellHead" align="center">SOAT</td>';
    $mHtml .= '<td class="CellHead" align="center">Vcto SOAT</td>';
    $mHtml .= '<td class="CellHead" align="center">Tarjeta Propiedad</td>';
    $mHtml .= '<td class="CellHead" align="center">C.C. Poseedor</td>';
    $mHtml .= '<td class="CellHead" align="center">Nombre Poseedor</td>';
    $mHtml .= '<td class="CellHead" align="center">Direcci&oacute;n Poseedor</td>';
    $mHtml .= '<td class="CellHead" align="center">Ciudad Poseedor</td>';
    $mHtml .= '<td class="CellHead" align="center">Mercancia</td>';
    $mHtml .= '</tr>';
    
    foreach( $_RESULT as $row )
    {
      $fec_cargue = $row[72] != '' && $row[72] != '0000-00-00 00:00:00' ? $row[72] : 'DESCONOCIDA';
      $fec_salpla = $row[73] != '' && $row[73] != '0000-00-00 00:00:00' ? $row[73] : 'DESCONOCIDA';
      $mHtml .= '<tr class="row">';
        $mHtml .= '<td class="cellInfo" align ="center"><a style="text-decoration:none; color:#006F1A;" href="index.php?cod_servic=3302&window=central&despac='.$row[71].'&tie_ultnov=0&opcion=1" target="_blank">'.$row[71].'</a></td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[0].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[1].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[2].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[3].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center" nowrap>'.$row[6].'('.substr( $row[5], 0, 4 ).'-'.substr( $row[4], 0, 3).')'.'</td>';
        $mHtml .= '<td class="cellInfo" align ="center" nowrap>'.$row[9].'('.substr( $row[8], 0, 4 ).'-'.substr( $row[7], 0, 3).')'.'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( trim( $fec_cargue ) != '' ? $fec_cargue : 'DESCONOCIDA' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $fec_salpla ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[11].' '.$row[12].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[13].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.number_format( $row[14], 0, '.', '.' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.number_format( $row[15], 0, '.', '.' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.number_format( $row[16], 0, '.', '.' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.number_format( $row[17], 0, '.', '.' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[18].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[19].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[20].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[21].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[22].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[40].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.$row[49].'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[29] != '' ? $row[29] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[30] != '' ? $row[30] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[31] != '' ? $row[31] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[50] != '' ? $row[50] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[59] != '' ? $row[59] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[28] != '' ? $row[28] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[38] != '' ? $row[38] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[39] != '' ? $row[39] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[44] != '' ? $row[44] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[41] != '' ? $row[41] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[43] != '' ? $row[43] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[45] != '' ? $row[45] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[46] != '' ? $row[46] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[47] != '' ? $row[47] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[48] != '' ? $row[48] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[51] != '' ? $row[51] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[52] != '' ? $row[52] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[53] != '' ? $row[53] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[54] != '' ? $row[54] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[55] != '' ? $row[55] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[56] != '' ? $row[56] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[58] != '' ? $row[58] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[60] != '' ? $row[60] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[61] != '' ? $row[61] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[63] != '' ? $row[63] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[62] != '' ? $row[62] : 'N/A' ).'</td>';
        $mHtml .= '<td class="cellInfo" align ="center">'.( $row[68] != '' ? $row[68] : 'N/A' ).'</td>';
      $mHtml .= '</tr>';
    }
    $mHtml .= '</table>';
    echo '<span id="excelID" onclick="Export();" style="color: #FFFFFF; cursor:pointer; font-family: Trebuchet MS,Verdana,Arial; font-size: 13px;">[Excel]</span>&nbsp;&nbsp;';
    $_SESSION['LIST_TOTAL'] = $mHtml;
    
    if( $mData['tip_column'] == 'cumpli' )
      echo '<span id="tiemposID" onclick="IndicadorCitcar(\''.$mData['cod_tipser'].'\');" style="color: #FFFFFF; cursor:pointer; font-family: Trebuchet MS,Verdana,Arial; font-size: 13px;">[Rangos]</span>';
    
    echo '<br><br>'.$mHtml;
    
  }
  
  protected function getPromedios( $despac )
  {
    $mSelect = "SELECT num_dessat, TIMESTAMPDIFF( MINUTE, fec_plalle, fec_salida ) / 60 AS diferecia 
                  FROM ".BASE_DATOS.".tab_despac_corona 
                 WHERE num_dessat IN(".$despac.")";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $tiemposx = $consulta -> ret_matriz();
    $suma = 0;
    foreach( $tiemposx as $row )
      $suma += (float)$row[1];
    return round( $suma/sizeof($tiemposx), 2); 
  }
  
  protected function getTiempos( $cod_clasex, $cod_mercan )
  {
    $mSelect = "SELECT hor_estima 
                  FROM ".BASE_DATOS.".tab_produc_tiempo 
                 WHERE cod_produc = '".$cod_mercan."'
                   AND cod_clasex = '".$cod_clasex."'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $homolo = $consulta -> ret_matriz();
    
    return sizeof( $homolo ) > 0 ? $homolo[0][0] : 0;
  }
  
  protected function getClasex( $cod_clasex )
  {
    $mSelect = "SELECT cod_clasex, nom_clasex
                  FROM ".BASE_DATOS.".tab_vehige_clases
                 WHERE cod_clasex = '".$cod_clasex."'
                   AND ind_estado = '1'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  protected function getMercan( $cod_mercan )
  {
    $mSelect = "SELECT cod_produc, nom_produc
                  FROM ".BASE_DATOS.".tab_genera_produc
                 WHERE cod_produc = '".$cod_mercan."'
                   AND ind_estado = '1'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  protected function getCiudad( $cod_ciudad = NULL )
  {
    $mSelect = "SELECT a.cod_ciudad, CONCAT(UPPER(a.abr_ciudad),' (',LEFT(b.abr_depart,4),')' )
                  FROM " . BASE_DATOS . ".tab_genera_ciudad a,
                       " . BASE_DATOS . ".tab_genera_depart b
                 WHERE a.cod_depart = b.cod_depart AND
                       a.cod_paisxx = b.cod_paisxx";
    
    if( $cod_ciudad != NULL )
    {
      $mSelect .= " AND a.cod_ciudad = '".$cod_ciudad."'";
    }
    
    $mSelect .= " GROUP BY 1 
                 ORDER BY 2";
                 
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
  }
}

$proceso = new AjaxEstadiaPlanta();
 ?>