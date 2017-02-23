<?php
ini_set('display_errors', false);
session_start();

class AjaxDespacUsuari
{
  var $conexion;
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
    $archivo = "Informe_".date( "Y_m_d" ).".xls";
    header('Content-Type: application/octetstream');
    header('Expires: 0');
    header('Content-Disposition: attachment; filename="'.$archivo.'"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    echo $_SESSION['LIST_TOTAL']; 
  }
  
  protected function Details( $mData )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    if(  $mData['fil'] == 't'  )
    {
      $_CONSUL = $_SESSION['TOTALY'];
      $Query = $_CONSUL[$mData['col']][$mData['inf']]['despac'];
    }
    elseif( $mData['col'] == 't' )
    {
      $_CONSUL = $_SESSION['TOTALX'];
      $Query = $_CONSUL[$mData['fil']][$mData['inf']]['despac'];
    }
    else
    {
      $_CONSUL = $_SESSION['NOVEDA'];
      $Query = $_CONSUL[$mData['fil']][$mData['inf']][$mData['col']]['despac'];
    }
    $mSelect = "SELECT a.num_despac, a.num_consec, ";
    if( $mData['inf'] == 'p' )
    {
      $mSelect .= " a.cod_contro, a.cod_rutasx, a.cod_consec, a.cod_noveda, 
                    a.cod_sitiox, a.obs_noveda, a.fec_noveda as det_noveda,";
    }
    else
    {
      $mSelect .= " a.cod_contr2, a.cod_rutas2, a.cod_conse2, a.cod_noved2, 
                    a.cod_sitio2, a.obs_noved2, a.fec_noved2 as det_noveda,";
    }
    
    if( $mData['typ'] == 'd' )
    {
      if( $mData['inf'] == 'p' )
        $mSelect .= " DATE( a.fec_noveda ) AS fec_noveda ";
      else
        $mSelect .= " DATE( a.fec_noved2 ) AS fec_noveda ";
    }    
    elseif( $mData['typ'] == 'w' )
    {
      if( $mData['inf'] == 'p' )
        $mSelect .= " CONCAT( YEAR( a.fec_noveda ), '-', WEEKOFYEAR(DATE( a.fec_noveda )) ) AS fec_noveda ";
      else
        $mSelect .= " CONCAT( YEAR( a.fec_noved2 ), '-', WEEKOFYEAR(DATE( a.fec_noved2 )) ) AS fec_noveda ";
    }    
    else
    {
      if( $mData['inf'] == 'p' )
        $mSelect .= " DATE_FORMAT( a.fec_noveda, '%Y-%m') AS fec_noveda ";
      else
        $mSelect .= " DATE_FORMAT( a.fec_noved2, '%Y-%m') AS fec_noveda ";
    }
    $mSelect .= ", b.num_desext, c.num_placax FROM ". BASE_DATOS .".tab_protoc_asigna a 
             LEFT JOIN ". BASE_DATOS .".tab_despac_sisext b
                    ON a.num_despac = b.num_despac
             LEFT JOIN ". BASE_DATOS .".tab_despac_vehige c
                    ON a.num_despac = c.num_despac ";
    
    #$mSelect .= " WHERE a.num_despac IN (".$Query.")";
    $mSelect .= " WHERE 1 = 1 ";
    
    if( $mData['inf'] == 'p' )
      $mSelect .= " AND a.ind_ejecuc = '0'";
    else
      $mSelect .= " AND a.ind_ejecuc = '1'";
    
    # Se ajusta ya que el usuario que ejecuta la solucion no es el mismo usuairo asignado
    if( $mData['fil'] != 't' )
    {
      if( $mData['inf'] == 'p' ) {
        $mSelect .= " AND a.usr_asigna = UPPER('".$mData['fil']."') ";
      }
      else {
        $mSelect .= " AND a.usr_ejecut = UPPER('".$mData['fil']."') ";
      }
    }

    if( $mData['typ'] == 'm' )
    {
      if( $mData['inf'] == 'p' ) {
      $mSelect .= " AND a.fec_noveda BETWEEN '".$mData["fec_ini"]."' AND '".$mData["fec_fin"]."' ";
      }
      else {
      $mSelect .= " AND a.fec_noved2 BETWEEN '".$mData["fec_ini"]."' AND '".$mData["fec_fin"]."' ";
      }
    }

    if( $mData['typ'] == 'd' )
    {
      if( $mData['inf'] == 'p' /*|| $mData['inf'] == 'e' */) {
      $mSelect .= " AND a.fec_noveda BETWEEN '".$mData["fec_ini"]."' AND '".$mData["fec_fin"]."' ";
      }
      else {
      $mSelect .= " AND a.fec_noved2 BETWEEN '".$mData["fec_ini"]."' AND '".$mData["fec_fin"]."' ";
      }
    }

    if( $mData['typ'] == 'w' )
    {
      if( $mData['inf'] == 'p' /*|| $mData['inf'] == 'e' */) {
      $mSelect .= " AND a.fec_noveda BETWEEN '".$mData["fec_ini"]."' AND '".$mData["fec_fin"]."' ";
      }
      else {
      $mSelect .= " AND a.fec_noved2 BETWEEN '".$mData["fec_ini"]."' AND '".$mData["fec_fin"]."' ";
      }
    }
    
      
    $mSelect .= " ORDER BY 1,2";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DATA = $consulta -> ret_matriz();
    
    $mHtml  = '<table width="100%" cellpadding="0" cellspacing="1">';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td class="CellHead" align="center">Despacho</td>';
    $mHtml .= '<td class="CellHead" align="center">Viaje</td>';
    $mHtml .= '<td class="CellHead" align="center">Placa</td>';
    $mHtml .= '<td class="CellHead" align="center">Sitio</td>';
    $mHtml .= '<td class="CellHead" align="center">Novedad</td>';
    $mHtml .= '<td class="CellHead" align="center">Fecha</td>';
    $mHtml .= '<td class="CellHead" style="width:300px" align="center">Observaci&oacute;n</td>';
    $mHtml .= '</tr>';
    $count = 0;
    foreach( $_DATA as $row )
    {
      if( $mData['fil'] != 't' && $mData['col'] != 't' )
      {
        if( $row['fec_noveda'] == $mData['col'] )
        {
          $count++;
          $_NOVEDA = $this -> getNomNoveda( $row[5] );
          $_SITIOX = $this -> getNomSitiox( $row[6] );
          $mHtml .= '<tr class="row">';
          $mHtml .= '<td class="cellInfo" align="center"><a href="index.php?cod_servic=3302&window=central&despac='.$row[0].'&opcion=1" style="cursor:pointer; color:#3A8104; text-decoration:none;" target="_blank">'.$row[0].'</a></td>';
          $mHtml .= '<td class="cellInfo" align="center">'.$row['num_desext'].'</td>';
          $mHtml .= '<td class="cellInfo" align="center">'.$row['num_placax'].'</td>';
          $mHtml .= '<td class="cellInfo" align="center">'.$_SITIOX.'</td>';
          $mHtml .= '<td class="cellInfo" align="center">'.$_NOVEDA.'</td>';
          $mHtml .= '<td class="cellInfo" align="center">'.$row[8].'</td>';
          $mHtml .= '<td class="cellInfo" align="left"><div style="border:0px solid black;width:300px;height:40px;overflow:scroll;overflow-y:scroll;overflow-x:hidden; padding: 5px;">'.$row[7].'</div></td>';
          $mHtml .= '</tr>';
        }
      }
      elseif( $mData['fil'] == 't' && $mData['col'] != 't' )
      {
        if( $row['fec_noveda'] == $mData['col'] )
        {
          $count++;
          $_NOVEDA = $this -> getNomNoveda( $row[5] );
          $_SITIOX = $this -> getNomSitiox( $row[6] );
          $mHtml .= '<tr class="row">';
          $mHtml .= '<td class="cellInfo" align="center"><a href="index.php?cod_servic=3302&window=central&despac='.$row[0].'&opcion=1" style="cursor:pointer; color:#3A8104; text-decoration:none;" target="_blank">'.$row[0].'</a></td>';
          $mHtml .= '<td class="cellInfo" align="center">'.$row['num_desext'].'</td>';
          $mHtml .= '<td class="cellInfo" align="center">'.$row['num_placax'].'</td>';
          $mHtml .= '<td class="cellInfo" align="center">'.$_SITIOX.'</td>';
          $mHtml .= '<td class="cellInfo" align="center">'.$_NOVEDA.'</td>';
          $mHtml .= '<td class="cellInfo" align="center">'.$row[8].'</td>';
          $mHtml .= '<td class="cellInfo" align="left"><div style="border:0px solid black;width:300px;height:40px;overflow:scroll;overflow-y:scroll;overflow-x:hidden; padding: 5px;">'.$row[7].'</div></td>';
          $mHtml .= '</tr>';
        }
      }
      elseif( $mData['fil'] != 't' && $mData['col'] == 't' )
      {
        $count++;
        $_NOVEDA = $this -> getNomNoveda( $row[5] );
        $_SITIOX = $this -> getNomSitiox( $row[6] );
        $mHtml .= '<tr class="row">';
        $mHtml .= '<td class="cellInfo" align="center"><a href="index.php?cod_servic=3302&window=central&despac='.$row[0].'&opcion=1" style="cursor:pointer; color:#3A8104; text-decoration:none;" target="_blank">'.$row[0].'</a></td>';
        $mHtml .= '<td class="cellInfo" align="center">'.$row['num_desext'].'</td>';
        $mHtml .= '<td class="cellInfo" align="center">'.$row['num_placax'].'</td>';
        $mHtml .= '<td class="cellInfo" align="center">'.$_SITIOX.'</td>';
        $mHtml .= '<td class="cellInfo" align="center">'.$_NOVEDA.'</td>';
        $mHtml .= '<td class="cellInfo" align="center">'.$row[8].'</td>';
        $mHtml .= '<td class="cellInfo" align="left"><div style="border:0px solid black;width:300px;height:40px;overflow:scroll;overflow-y:scroll;overflow-x:hidden; padding: 5px;">'.$row[7].'</div></td>';
        $mHtml .= '</tr>';
      }
      elseif( $mData['fil'] == 't' && $mData['col'] == 't' )
      {
        $count++;
        $_NOVEDA = $this -> getNomNoveda( $row[5] );
        $_SITIOX = $this -> getNomSitiox( $row[6] );
        $mHtml .= '<tr class="row">';
        $mHtml .= '<td class="cellInfo" align="center"><a href="index.php?cod_servic=3302&window=central&despac='.$row[0].'&opcion=1" style="cursor:pointer; color:#3A8104; text-decoration:none;" target="_blank">'.$row[0].'</a></td>';
        $mHtml .= '<td class="cellInfo" align="center">'.$row['num_desext'].'</td>';
        $mHtml .= '<td class="cellInfo" align="center">'.$row['num_placax'].'</td>';
        $mHtml .= '<td class="cellInfo" align="center">'.$_SITIOX.'</td>';
        $mHtml .= '<td class="cellInfo" align="center">'.$_NOVEDA.'</td>';
        $mHtml .= '<td class="cellInfo" align="center">'.$row[8].'</td>';
        $mHtml .= '<td class="cellInfo" align="left"><div style="border:0px solid black;width:300px;height:40px;overflow:scroll;overflow-y:scroll;overflow-x:hidden; padding: 5px;">'.$row[7].'</div></td>';
        $mHtml .= '</tr>';
      }
    }
    $_SESSION['LIST_TOTAL'] = $mHtml;
    echo 'SE ENCONTRARON '.$count.' REGISTROS<br><br><span id="excelID" onclick="Export();" style="color: #FFFFFF; cursor:pointer; font-family: Trebuchet MS,Verdana,Arial; font-size: 13px;">[Excel]</span><br><br>'.$mHtml;

    // echo "<pre>";
    // print_r( $mData );
    // echo "</pre>";
  }
  
  
             
  private function getNomNoveda( $cod_noveda )
  {
    $mSelect = "SELECT CONCAT( cod_noveda, ' - ', nom_noveda ) AS nom_noveda 
                  FROM ".BASE_DATOS.".tab_genera_noveda 
                 WHERE cod_noveda = '".$cod_noveda."'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DATA = $consulta -> ret_matriz();
    return $_DATA[0][0];
  }

  private function getNomSitiox( $cod_sitioz )
  {
    $mSelect = "SELECT nom_sitiox 
                  FROM ".BASE_DATOS.".tab_despac_sitio 
                 WHERE cod_sitiox = '".$cod_sitioz."'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DATA = $consulta -> ret_matriz();
    return $_DATA[0][0];
  }
    
  
  function getSemanas( $ano )
  {
    $resultado = NUll;
    for($mes = 1; $mes <= 12; $mes++)
    {
      $ultimodiames = date('t', mktime(0, 0, 0, $mes, 1, $ano));
      for($dia = 1; $dia <= $ultimodiames; $dia++)
      {
        $diasemana = date('w', mktime(0, 0, 0, $mes, $dia, $ano));
        
        if ( $diasemana == 0 )
          $diasemana = 7;
        
        $semana = date('W', mktime(0, 0, 0, $mes, $dia, $ano));
        
        if($diasemana == 1 )
          $resultado[$semana][0] = date('Y-m-d', mktime(0, 0, 0, $mes, $dia, $ano));
        if($diasemana == 7 )
          $resultado[$semana][1] = date('Y-m-d', mktime(0, 0, 0, $mes, $dia, $ano));
      }
    }  
    return $resultado;
  }
  
  function getUsuario( $cod_usuari = '' )
  {
    $mSelect = "SELECT UPPER(cod_usuari), UPPER( nom_usuari ) AS nom_usuari 
                  FROM ".BASE_DATOS.".tab_genera_usuari 
                 WHERE usr_emailx LIKE '%corona%'";
    
    if( $cod_usuari != '' )
    $mSelect .= " AND cod_usuari = '".$cod_usuari."' ";
    
    $mSelect .= " GROUP BY 1 
                  ORDER BY 2";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  function formatDate( $date, $mode )
  {
    $fecha = explode('-', $date);
    $arr_month = array( 1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 8 => 'Jul', 7 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic');
    switch( $mode )
    {
      case 'd/m':
        $to = $fecha[2]."/".$arr_month[(int)$fecha[1]];
      break;
    }
    return $to;
  }
  
  function setFechas( $year, $month )
  {
    switch( (int)$month )
    {
      case 1: case 3: case 5: case 7: case 8: case 10: case 12:
        $dia_fin = 31;
      break;
      
      case 4: case 6: case 9: case 11:
        $dia_fin = 30;
      break;
      
      case 2:
        $dia_fin = 28;
        if( (!( $year % 4 ) && ( $year % 100 ) ) || !( $year % 400 ) )
          $dia_fin = 29;
      break;
    }
    return '01 al '.$dia_fin;
  }
  
  function InfMensual( $mData )
  {
    /**************************************************/
    $date_ini = explode('-', $mData['fec_inicia'] );
    $date_fin = explode('-', $mData['fec_finali'] );
    /**************************************************/
    $ano_ini = $date_ini[0];
    $mes_ini = $date_ini[1];
    $dia_ini = $date_ini[2];
    /**************************************************/
    $ano_fin = $date_fin[0];
    $mes_fin = $date_fin[1];
    $dia_fin = $date_fin[2];
    /**************************************************/
    $_USUARI = $this -> getUsuario( $mData['cod_usuari'] );
    $_FECHAS = array();
    
    for( $i = $ano_ini; $i <= $ano_fin; $i++ )
    { 
      if( $ano_ini == $ano_fin )
      {
        for( $j = $mes_ini; $j <= $mes_fin; $j++ )
        {
          $month = $j;
          if( strlen( $j ) == 1 )
            $month = '0'.$j;
                  
          $_FECHAS[] = $i."-".$month;
        } 
      }
      else
      {
        if( $i == $ano_ini )
        {
          for( $j = $mes_ini; $j <= 12; $j++ )
          {
            $month = $j;
            if( strlen( $j ) == 1 )
              $month = '0'.$j;
            
            $_FECHAS[] = $i."-".$month;
          }
        }
        elseif( $i == $ano_fin )
        {
          for( $j = 1; $j <= $mes_fin; $j++ )
          {
            $month = $j;
            if( strlen( $j ) == 1 )
              $month = '0'.$j;
              
            $_FECHAS[] = $i."-".$month;
          }
        }
        else
        {
          for( $j = 1; $j <= 12; $j++ )
          {
            $month = $j;
            if( strlen( $j ) == 1 )
              $month = '0'.$j;
            
            $_FECHAS[] = $i."-".$month;
          }
        }
      }
    }
    $mHtml  = '<table width="100%" cellpadding="0" cellspacing="1">';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td class="CellHead" rowspan="2" align="center"><b>Usuario</b></td>';
    $meses = array( 1 => 'Enero',  2 => 'Febrero',  3 => 'Marzo',  4 => 'Abril',  5 => 'Mayo',  6 => 'Junio',  7 => 'Julio',  8 => 'Agosto',  9 => 'Setiempre',  10 => 'Octubre',  11 => 'Noviembre',  12 => 'Diciembre' );
    
    foreach( $_FECHAS as $row )
    {
      $mes = explode( '-', $row );
      $mHtml .= '<td class="CellHead" colspan="2" align="center">'.($meses[(int)$mes[1]]).' de '.$mes[0].'<br><i><small style="color:#E7E975; font-size:12px;">'.($this -> setFechas( $mes[0], $mes[1] )).'</small></i></td>';
    }
    $mHtml .= '<td class="CellHead" colspan="2" align="center">TOTAL</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    foreach( $_FECHAS as $row )
    {
      $mHtml .= '<td class="CellHead" align="center">Pendientes</td>';
      $mHtml .= '<td class="CellHead" align="center">Resueltas</td>';
    }
    
    $mHtml .= '<td class="CellHead" align="center">Pendientes</td>';
    $mHtml .= '<td class="CellHead" align="center">Resueltas</td>';
    $mHtml .= '</tr>';
    
    $count = 0;
    $_NOVEDA = $this -> GetNovedadesMensuales( $mData );
    $_TOTALX = array();
    $_TOTALY = array();
    foreach( $_USUARI as $usuario )
    {
      $ASIGNA = $_NOVEDA[$usuario[0]];
      if( sizeof( $ASIGNA ) <= 0 )
        continue;
      
      $style = 'cellInfo';
      $mHtml .= '<tr class="row">';
      $mHtml .= '<td nowrap class="CellHead" align="left">'.$usuario[1].'</td>';
      foreach( $_FECHAS as $row )
      {
        $_TOTALX[$usuario[0]]['p']['countx'] += (int)$ASIGNA['p'][$row]['countx'];
        $_TOTALX[$usuario[0]]['p']['despac'] .= $_TOTALX[$usuario[0]]['p']['despac'] != '' && $ASIGNA['p'][$row]['despac'] != '' ? ','.$ASIGNA['p'][$row]['despac'] : $ASIGNA['p'][$row]['despac'];
        
        $_TOTALX[$usuario[0]]['e']['countx'] += (int)$ASIGNA['e'][$row]['countx'];
        $_TOTALX[$usuario[0]]['e']['despac'] .= $_TOTALX[$usuario[0]]['e']['despac'] != '' && $ASIGNA['e'][$row]['despac'] != '' ? ','.$ASIGNA['e'][$row]['despac'] : $ASIGNA['e'][$row]['despac'];
        
        $_TOTALY[$row]['p']['countx'] += (int)$ASIGNA['p'][$row]['countx'];
        $_TOTALY[$row]['p']['despac'] .= $_TOTALY[$row]['p']['despac'] != '' && $ASIGNA['p'][$row]['despac'] != '' ? ','.$ASIGNA['p'][$row]['despac'] : $ASIGNA['p'][$row]['despac'];
        
        $_TOTALY[$row]['e']['countx'] += (int)$ASIGNA['e'][$row]['countx'];
        $_TOTALY[$row]['e']['despac'] .= $_TOTALY[$row]['e']['despac'] != '' && $ASIGNA['e'][$row]['despac'] != '' ? ','.$ASIGNA['e'][$row]['despac'] : $ASIGNA['e'][$row]['despac'];
        
        $_L_P = round($ASIGNA['p'][$row]['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \''.$usuario[0].'\', \'p\', \'m\' );"': '';
        $_L_E = round($ASIGNA['e'][$row]['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \''.$usuario[0].'\', \'e\', \'m\' );"': '';
        $mHtml .= '<td '.$_L_P.' class="'.$style.'" align="center">'.round($ASIGNA['p'][$row]['countx']).'</td>';
        $mHtml .= '<td '.$_L_E.' class="'.$style.'" align="center">'.round($ASIGNA['e'][$row]['countx']).'</td>';
      }
      
      $_L_P = round($_TOTALX[$usuario[0]]['p']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \''.$usuario[0].'\', \'p\', \'m\' );"': '';
      $_L_E = round($_TOTALX[$usuario[0]]['e']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \''.$usuario[0].'\', \'e\', \'m\' );"': '';
        
      $mHtml .= '<td '.$_L_P.' class="'.$style.'" align="center">'.round($_TOTALX[$usuario[0]]['p']['countx']).'</td>';
      $mHtml .= '<td '.$_L_E.' class="'.$style.'" align="center">'.round($_TOTALX[$usuario[0]]['e']['countx']).'</td>';
      $mHtml .= '</tr>';
      $count++;
    }
    
    $mHtml .= '<tr>';
    $mHtml .= '<td class="CellHead2" align="center"><b>TOTAL</b></td>';
    foreach( $_FECHAS as $row )
    {
      
      $_TOTALY['t']['p']['countx'] += (int)$_TOTALY[$row]['p']['countx'];
      $_TOTALY['t']['p']['despac'] .= $_TOTALY['t']['p']['despac'] != '' && $_TOTALY[$row]['p']['despac'] != '' ? ','.$_TOTALY[$row]['p']['despac'] : $_TOTALY[$row]['p']['despac'];
      
      $_TOTALY['t']['e']['countx'] += (int)$_TOTALY[$row]['e']['countx'];
      $_TOTALY['t']['e']['despac'] .= $_TOTALY['t']['e']['despac'] != '' && $_TOTALY[$row]['e']['despac'] != ''  ? ','.$_TOTALY[$row]['e']['despac'] : $_TOTALY[$row]['e']['despac'];
      
      $_L_P = round($_TOTALY[$row]['p']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \'t\', \'p\', \'m\' );"': '';
      $_L_E = round($_TOTALY[$row]['e']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \'t\', \'e\', \'m\' );"': '';
      $mHtml .= '<td '.$_L_P.' class="CellHead" align="center">'.round($_TOTALY[$row]['p']['countx']).'</td>';
      $mHtml .= '<td '.$_L_E.' class="CellHead" align="center">'.round($_TOTALY[$row]['e']['countx']).'</td>';
    }
    $_L_P = round($_TOTALY['t']['p']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \'t\', \'p\', \'m\' );"': '';
    $_L_E = round($_TOTALY['t']['e']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \'t\', \'e\', \'m\' );"': '';
    $mHtml .= '<td  '.$_L_P.' class="CellHead" align="center">'.round($_TOTALY['t']['p']['countx']).'</td>';
    $mHtml .= '<td  '.$_L_E.' class="CellHead" align="center">'.round($_TOTALY['t']['e']['countx']).'</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '</table>';
    
    $_SESSION['LIST_TOTAL'] = $mHtml;
    
    $_SESSION['TOTALY'] = $_TOTALY;
    $_SESSION['TOTALX'] = $_TOTALX;
    $_SESSION['NOVEDA'] = $_NOVEDA;
    
    echo '<span id="excelID" onclick="Export();" style="color: #35650F; cursor:pointer; font-family: Trebuchet MS,Verdana,Arial; font-size: 13px;">[Excel]</span><br><br>';
    echo $mHtml;
  }
  
  function InfSemanal( $mData )
  {
    /**************************************************/
    $date_ini = explode('-', $mData['fec_inicia'] );
    $date_fin = explode('-', $mData['fec_finali'] );
    /**************************************************/
    $ano_ini = $date_ini[0];
    $mes_ini = $date_ini[1];
    $dia_ini = $date_ini[2];
    /**************************************************/
    $ano_fin = $date_fin[0];
    $mes_fin = $date_fin[1];
    $dia_fin = $date_fin[2];
    /**************************************************/
    $_USUARI = $this -> getUsuario( $mData['cod_usuari'] );
    $_FECHAS = array();
    
    $week_ini = $this -> getWeekofYear( $mData['fec_inicia'] );
    $week_fin = $this -> getWeekofYear( $mData['fec_finali'] );
    
    for( $i = $ano_ini; $i <= $ano_fin; $i++ )
    { 
      if( $ano_ini == $ano_fin )
      {
        for( $j = $week_ini; $j <= $week_fin; $j++ )
        {
          $week = $j;
          if( strlen( $j ) == 1 )
            $week = '0'.$j;
                  
          $_FECHAS[] = $i."-".$week;
        } 
      }
      else
      {
        if( $i == $ano_ini )
        {
          for( $j = $week_ini; $j <= 52; $j++ )
          {
            $week = $j;
            if( strlen( $j ) == 1 )
              $week = '0'.$j;

            $_FECHAS[] = $i."-".$week;
          }
        }
        elseif( $i == $ano_fin )
        {
          for( $j = 1; $j <= $week_fin; $j++ )
          {
            $week = $j;
            if( strlen( $j ) == 1 )
              $week = '0'.$j;

            $_FECHAS[] = $i."-".$week;
          }
        }
        else
        {
          for( $j = 1; $j <= 52; $j++ )
          {
            $week = $j;
            if( strlen( $j ) == 1 )
              $week = '0'.$j;
            
            $_FECHAS[] = $i."-".$week;
          }
        }
      }
    }
    
    $arr_weeks = array();
    for( $m = $ano_ini; $m <= $ano_fin; $m++ )
    {
      $arr_weeks[$m] = $this -> getSemanas( $m );
    }
    
    $mHtml  = '<table width="100%" cellpadding="0" cellspacing="1">';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td class="CellHead" rowspan="2" align="center"><b>Usuario</b></td>';
    foreach( $_FECHAS as $row )
    {
      
      $periodo = explode( '-', $row );
      $mHtml .= '<td class="CellHead" colspan="2" align="center">Semana '.$periodo[1].' - '.$periodo[0].'<br><i><small style="color:#E7E975; font-size:12px;">'.($this -> formatDate( $arr_weeks[$periodo[0]][$periodo[1]][0], 'd/m' )).' a '.($this -> formatDate( $arr_weeks[$periodo[0]][$periodo[1]][1], 'd/m' )).'</small></i></td>';
    }
    $mHtml .= '<td class="CellHead" colspan="2" align="center">TOTAL</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    foreach( $_FECHAS as $row )
    {
      $mHtml .= '<td class="CellHead" align="center">Pendientes</td>';
      $mHtml .= '<td class="CellHead" align="center">Resueltas</td>';
    }
    
    $mHtml .= '<td class="CellHead" align="center">Pendientes</td>';
    $mHtml .= '<td class="CellHead" align="center">Resueltas</td>';
    $mHtml .= '</tr>';
    
    $count = 0;
    $_NOVEDA = $this -> GetNovedadesSemanales( $mData );
    $_TOTALX = array();
    $_TOTALY = array();
    
    foreach( $_USUARI as $usuario )
    {
      $ASIGNA = $_NOVEDA[$usuario[0]];
      if( sizeof( $ASIGNA ) <= 0 )
        continue;
      $style = 'cellInfo';
      $mHtml .= '<tr class="row">';
      $mHtml .= '<td nowrap class="CellHead" align="left">'.$usuario[1].'</td>';
      foreach( $_FECHAS as $row )
      {
        $rowDelimiter=explode("-", $row);
        if($rowDelimiter[1]>='01' && $rowDelimiter[1]<='09')
        {
          $row= $rowDelimiter[0]."-".(int) $rowDelimiter[1];
        }
        $_TOTALX[$usuario[0]]['p']['countx'] += (int)$ASIGNA['p'][$row]['countx'];
        $_TOTALX[$usuario[0]]['p']['despac'] .= $_TOTALX[$usuario[0]]['p']['despac'] != '' && $ASIGNA['p'][$row]['despac'] != '' ? ','.$ASIGNA['p'][$row]['despac'] : $ASIGNA['p'][$row]['despac'];
        
        $_TOTALX[$usuario[0]]['e']['countx'] += (int)$ASIGNA['e'][$row]['countx'];
        $_TOTALX[$usuario[0]]['e']['despac'] .= $_TOTALX[$usuario[0]]['e']['despac'] != '' && $ASIGNA['e'][$row]['despac'] != '' ? ','.$ASIGNA['e'][$row]['despac'] : $ASIGNA['e'][$row]['despac'];
        
        $_TOTALY[$row]['p']['countx'] += (int)$ASIGNA['p'][$row]['countx'];
        $_TOTALY[$row]['p']['despac'] .= $_TOTALY[$row]['p']['despac'] != '' && $ASIGNA['p'][$row]['despac'] != '' ? ','.$ASIGNA['p'][$row]['despac'] : $ASIGNA['p'][$row]['despac'];
        
        $_TOTALY[$row]['e']['countx'] += (int)$ASIGNA['e'][$row]['countx'];
        $_TOTALY[$row]['e']['despac'] .= $_TOTALY[$row]['e']['despac'] != '' && $ASIGNA['e'][$row]['despac'] != '' ? ','.$ASIGNA['e'][$row]['despac'] : $ASIGNA['e'][$row]['despac'];
        
        $_L_P = round($ASIGNA['p'][$row]['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \''.$usuario[0].'\', \'p\', \'w\' );"': '';
        $_L_E = round($ASIGNA['e'][$row]['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \''.$usuario[0].'\', \'e\', \'w\' );"': '';
        $mHtml .= '<td '.$_L_P.' class="'.$style.'" align="center">'.round($ASIGNA['p'][$row]['countx']).'</td>';
        $mHtml .= '<td '.$_L_E.' class="'.$style.'" align="center">'.round($ASIGNA['e'][$row]['countx']).'</td>';
      }
      
      $_L_P = round($_TOTALX[$usuario[0]]['p']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \''.$usuario[0].'\', \'p\', \'w\' );"': '';
      $_L_E = round($_TOTALX[$usuario[0]]['e']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \''.$usuario[0].'\', \'e\', \'w\' );"': '';
        
      $mHtml .= '<td '.$_L_P.' class="'.$style.'" align="center">'.round($_TOTALX[$usuario[0]]['p']['countx']).'</td>';
      $mHtml .= '<td '.$_L_E.' class="'.$style.'" align="center">'.round($_TOTALX[$usuario[0]]['e']['countx']).'</td>';
      $mHtml .= '</tr>';
      $count++;
    }
    
    $mHtml .= '<tr>';
    $mHtml .= '<td class="CellHead2" align="center"><b>TOTAL</b></td>';
    foreach( $_FECHAS as $row )
    {
      $rowDelimiter=explode("-", $row);
      if($rowDelimiter[1]>='01' && $rowDelimiter[1]<='09')
      {
        $row= $rowDelimiter[0]."-".(int) $rowDelimiter[1];
      }
      $_TOTALY['t']['p']['countx'] += (int)$_TOTALY[$row]['p']['countx'];
      $_TOTALY['t']['p']['despac'] .= $_TOTALY['t']['p']['despac'] != '' && $_TOTALY[$row]['p']['despac'] != '' ? ','.$_TOTALY[$row]['p']['despac'] : $_TOTALY[$row]['p']['despac'];
      
      $_TOTALY['t']['e']['countx'] += (int)$_TOTALY[$row]['e']['countx'];
      $_TOTALY['t']['e']['despac'] .= $_TOTALY['t']['e']['despac'] != '' && $_TOTALY[$row]['e']['despac'] != '' ? ','.$_TOTALY[$row]['e']['despac'] : $_TOTALY[$row]['e']['despac'];
      
      $_L_P = round($_TOTALY[$row]['p']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \'t\', \'p\', \'w\' );"': '';
      $_L_E = round($_TOTALY[$row]['e']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \'t\', \'e\', \'w\' );"': '';
      $mHtml .= '<td '.$_L_P.' class="CellHead" align="center">'.round($_TOTALY[$row]['p']['countx']).'</td>';
      $mHtml .= '<td '.$_L_E.' class="CellHead" align="center">'.round($_TOTALY[$row]['e']['countx']).'</td>';
    }
    $_L_P = round($_TOTALY['t']['p']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \'t\', \'p\', \'w\' );"': '';
    $_L_E = round($_TOTALY['t']['e']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \'t\', \'e\', \'w\' );"': '';
    $mHtml .= '<td  '.$_L_P.' class="CellHead" align="center">'.round($_TOTALY['t']['p']['countx']).'</td>';
    $mHtml .= '<td  '.$_L_E.' class="CellHead" align="center">'.round($_TOTALY['t']['e']['countx']).'</td>';
    $mHtml .= '</tr>';
    $mHtml .= '</table>';
    
    $_SESSION['LIST_TOTAL'] = $mHtml;
    
    $_SESSION['TOTALY'] = $_TOTALY;
    $_SESSION['TOTALX'] = $_TOTALX;
    $_SESSION['NOVEDA'] = $_NOVEDA;
    
    echo '<span id="excelID" onclick="Export();" style="color: #35650F; cursor:pointer; font-family: Trebuchet MS,Verdana,Arial; font-size: 13px;">[Excel]</span><br><br>';
    echo $mHtml;
    
  }
  
  function getWeekofYear( $date )
  {
    return date( "W", strtotime( $date ) );
  }
  
  function GetNovedadesDiarias( $mData )
  {
    $date_ini = $mData['fec_inicia']." ".$mData['hor_inicia'];
    $date_fin = $mData['fec_finali']." ".$mData['hor_finali'];
    
    $mSelect = "SELECT num_despac, num_consec, DATE(fec_noveda) AS fec_noveda, UPPER(usr_asigna) AS usr_asigna
                  FROM ".BASE_DATOS.".tab_protoc_asigna 
                 WHERE ind_ejecuc = '0' 
                   AND fec_noveda BETWEEN '".$date_ini."' AND '".$date_fin."' 
                 ORDER BY 3";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_NOVEDAP = $consulta -> ret_matriz();
    
    $_DATA = array();
    
    foreach( $_NOVEDAP as $row )
    {
      $_DATA[$row['usr_asigna']]['p'][$row['fec_noveda']]['countx']++;
      $_DATA[$row['usr_asigna']]['p'][$row['fec_noveda']]['despac'] .= $_DATA[$row['usr_asigna']]['p'][$row['fec_noveda']]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
    }
    
    $mSelect = "SELECT num_despac, num_consec, DATE(fec_noved2) AS fec_noveda, UPPER(usr_ejecut) AS usr_asigna
                  FROM ".BASE_DATOS.".tab_protoc_asigna 
                 WHERE ind_ejecuc = '1' 
                   AND fec_noved2 >= '".$date_ini."' AND fec_noved2 <= '".$date_fin."'  
                 ORDER BY 3";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_NOVEDAE = $consulta -> ret_matriz();
    
    foreach( $_NOVEDAE as $row )
    {
      $_DATA[$row['usr_asigna']]['e'][$row['fec_noveda']]['countx']++;
      $_DATA[$row['usr_asigna']]['e'][$row['fec_noveda']]['despac'] .= $_DATA[$row['usr_asigna']]['e'][$row['fec_noveda']]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
    }
    return $_DATA;
  }
  
  function GetNovedadesSemanales( $mData )
  {
    $date_ini = $mData['fec_inicia']." ".$mData['hor_inicia'];
    $date_fin = $mData['fec_finali']." ".$mData['hor_finali'];
    
    $mSelect = "SELECT num_despac, num_consec, CONCAT( YEAR( fec_noveda ), '-', WEEKOFYEAR(DATE( fec_noveda )) )AS fec_noveda, UPPER(usr_asigna) AS usr_asigna
                  FROM ".BASE_DATOS.".tab_protoc_asigna 
                 WHERE ind_ejecuc = '0' 
                   AND fec_noveda BETWEEN '".$date_ini."' AND '".$date_fin."' 
                 ORDER BY 3";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_NOVEDAP = $consulta -> ret_matriz();
    
    $_DATA = array();
    
    foreach( $_NOVEDAP as $row )
    {
      $_DATA[$row['usr_asigna']]['p'][$row['fec_noveda']]['countx']++;
      $_DATA[$row['usr_asigna']]['p'][$row['fec_noveda']]['despac'] .= $_DATA[$row['usr_asigna']]['p'][$row['fec_noveda']]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
    }
    
    $mSelect = "SELECT num_despac, num_consec, CONCAT( YEAR( fec_noved2 ), '-', WEEKOFYEAR(DATE( fec_noved2 )) )AS fec_noveda, UPPER(usr_ejecut) AS usr_asigna
                  FROM ".BASE_DATOS.".tab_protoc_asigna 
                 WHERE ind_ejecuc = '1' 
                   AND fec_noved2 >= '".$date_ini."' AND fec_noved2 <= '".$date_fin."'  
                 ORDER BY 3";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_NOVEDAE = $consulta -> ret_matriz();
    
    foreach( $_NOVEDAE as $row )
    {
      $_DATA[$row['usr_asigna']]['e'][$row['fec_noveda']]['countx']++;
      $_DATA[$row['usr_asigna']]['e'][$row['fec_noveda']]['despac'] .= $_DATA[$row['usr_asigna']]['e'][$row['fec_noveda']]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
    }
    return $_DATA;
  }
  
  function GetNovedadesMensuales( $mData )
  {
    $date_ini = $mData['fec_inicia']." ".$mData['hor_inicia'];
    $date_fin = $mData['fec_finali']." ".$mData['hor_finali'];
    
    $mSelect = "SELECT num_despac, num_consec, DATE_FORMAT( fec_noveda, '%Y-%m') AS fec_noveda, UPPER(usr_asigna) AS usr_asigna
                  FROM ".BASE_DATOS.".tab_protoc_asigna 
                 WHERE ind_ejecuc = '0' 
                   AND fec_noveda BETWEEN '".$date_ini."' AND '".$date_fin."' 
                 ORDER BY 3";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_NOVEDAP = $consulta -> ret_matriz();
    
    $_DATA = array();
    
    foreach( $_NOVEDAP as $row )
    {
      $_DATA[$row['usr_asigna']]['p'][$row['fec_noveda']]['countx']++;
      $_DATA[$row['usr_asigna']]['p'][$row['fec_noveda']]['despac'] .= $_DATA[$row['usr_asigna']]['p'][$row['fec_noveda']]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
    }
    
    $mSelect = "SELECT num_despac, num_consec, DATE_FORMAT( fec_noved2, '%Y-%m') AS fec_noveda, UPPER(usr_ejecut) AS usr_asigna 
                  FROM ".BASE_DATOS.".tab_protoc_asigna 
                 WHERE ind_ejecuc = '1' 
                   AND fec_noved2 >= '".$date_ini."' AND fec_noved2 <= '".$date_fin."' 
                 ORDER BY 3";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_NOVEDAE = $consulta -> ret_matriz();
    foreach( $_NOVEDAE as $row )
    {
      $_DATA[$row['usr_asigna']]['e'][$row['fec_noveda']]['countx']++;
      $_DATA[$row['usr_asigna']]['e'][$row['fec_noveda']]['despac'] .= $_DATA[$row['usr_asigna']]['e'][$row['fec_noveda']]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
    }
    return $_DATA;
  }
  
  function InfDiario( $mData )
  {
    /**************************************************/
    $date_ini = explode('-', $mData['fec_inicia'] );
    $date_fin = explode('-', $mData['fec_finali'] );
    /**************************************************/
    $ano_ini = $date_ini[0];
    $mes_ini = $date_ini[1];
    $dia_ini = $date_ini[2];
    /**************************************************/
    $ano_fin = $date_fin[0];
    $mes_fin = $date_fin[1];
    $dia_fin = $date_fin[2];
    /**************************************************/
    $_USUARI = $this -> getUsuario( $mData['cod_usuari'] );
    $_FECHAS = array();
    
    for( $i = $ano_ini; $i <= $ano_fin; $i++ )
    { 
      if( $ano_ini == $ano_fin )
      {
        for(  $j = $mes_ini; $j <= $mes_fin; $j++  )
        {
          if( $mes_ini == $mes_fin )
          {
            for( $k = $dia_ini; $k <= $dia_fin; $k++ )
            {
              $day = $k;
              if( strlen( $k ) == 1 )
                $day = '0'.$k;
                
              $fecactual = $ano_ini.'-'.$mes_ini.'-'.$day;
              $_FECHAS[] = $fecactual;
            }
          }
          else
          {
            if( $j == $mes_ini )
            {
              for( $l = $dia_ini; $l <= 31; $l++ )
              {
                $day = $l;
                if( strlen( $l ) == 1 )
                  $day = '0'.$l;
                
                if( $this -> validateFecha( $ano_ini, $mes_ini, $day ) )
                {
                  $fecactual = $ano_ini.'-'.$mes_ini.'-'.$day;
                  $_FECHAS[] = $fecactual;
                }
              }
            }
            elseif( $j == $mes_fin )
            {
              for( $l = 1; $l <= $dia_fin; $l++ )
              {
                $day = $l;
                if( strlen( $l ) == 1 )
                  $day = '0'.$l;
                
                if( $this -> validateFecha( $ano_ini, $mes_fin, $day ) )
                {
                  $fecactual = $ano_ini.'-'.$mes_fin.'-'.$day;
                  $_FECHAS[] = $fecactual;
                }
              }
            }
            else
            {
              for( $l = 1; $l <= 31; $l++ )
              {
                $day = $l;
                if( strlen( $l ) == 1 )
                  $day = '0'.$l;
                
                $month = $j;
                if( strlen( $j ) == 1 )
                  $month = '0'.$j;
                
                if( $this -> validateFecha( $ano_ini, $month, $day ) )
                {
                  $fecactual = $ano_ini.'-'.$month.'-'.$day;
                  $_FECHAS[] = $fecactual;
                }
              }
            }
          }
        }
      }
      else
      {
        if( $i == $ano_ini )
        {
          for( $j = $mes_ini; $j <= 12; $j++ )
          {
            for( $l = $dia_ini; $l <= 31; $l++ )
            {
              $day = $l;
              if( strlen( $l ) == 1 )
                $day = '0'.$l;
              
              $month = $j;
              if( strlen( $j ) == 1 )
                $month = '0'.$j;
              
              if( $this -> validateFecha( $i, $month, $day ) )
              {
                $fecactual = $i.'-'.$month.'-'.$day;
                $_FECHAS[] = $fecactual;
              }
            }
          }
        }
        elseif( $i == $ano_fin )
        {
          for( $j = 1; $j <= $mes_fin; $j++ )
          {
            for( $l = 1; $l <= $dia_fin; $l++ )
            {
              $day = $l;
              if( strlen( $l ) == 1 )
                $day = '0'.$l;
                
              $month = $j;
              if( strlen( $j ) == 1 )
                $month = '0'.$j;
              
              if( $this -> validateFecha( $i, $month, $day ) )
              {
                $fecactual = $i.'-'.$month.'-'.$day;
                $_FECHAS[] = $fecactual;
              }
            }
          }
        }
        else
        {
          for( $j = 1; $j <= 12; $j++ )
          {
            for( $l = 1; $l <= 31; $l++ )
            {
              $day = $l;
              if( strlen( $l ) == 1 )
                $day = '0'.$l;
                
              $month = $j;
              if( strlen( $j ) == 1 )
                $month = '0'.$j;
              
              if( $this -> validateFecha( $i, $month, $day ) )
              {
                $fecactual = $i.'-'.$month.'-'.$day;
                $_FECHAS[] = $fecactual;
              }
            }
          }
        }
      }
    }
        
    $mHtml  = '<table width="100%" cellpadding="0" cellspacing="1">';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td class="CellHead" rowspan="2" align="center"><b>Usuario</b></td>';
    foreach( $_FECHAS as $row )
    {
      $mHtml .= '<td class="CellHead" colspan="2" align="center">'. ( $this -> toFecha( $row ) ).'</td>';
    }
    $mHtml .= '<td class="CellHead" colspan="2" align="center">TOTAL</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    foreach( $_FECHAS as $row )
    {
      $mHtml .= '<td class="CellHead" align="center">Pendientes</td>';
      $mHtml .= '<td class="CellHead" align="center">Resueltas</td>';
    }
    
    $mHtml .= '<td class="CellHead" align="center">Pendientes</td>';
    $mHtml .= '<td class="CellHead" align="center">Resueltas</td>';
    $mHtml .= '</tr>';
    
    $count = 0;
    $_NOVEDA = $this -> GetNovedadesDiarias( $mData );
    $_TOTALX = array();
    $_TOTALY = array();
    foreach( $_USUARI as $usuario )
    {
      $ASIGNA = $_NOVEDA[$usuario[0]];
      if( sizeof( $ASIGNA ) <= 0 )
        continue;
      
      $style = 'cellInfo';
      $mHtml .= '<tr class="row">';
      $mHtml .= '<td nowrap class="CellHead" align="left">'.$usuario[1].'</td>';
      foreach( $_FECHAS as $row )
      {
        $_TOTALX[$usuario[0]]['p']['countx'] += (int)$ASIGNA['p'][$row]['countx'];
        $_TOTALX[$usuario[0]]['p']['despac'] .= $_TOTALX[$usuario[0]]['p']['despac'] != '' && $ASIGNA['p'][$row]['despac'] ? ','.$ASIGNA['p'][$row]['despac'] : $ASIGNA['p'][$row]['despac'];
        
        $_TOTALX[$usuario[0]]['e']['countx'] += (int)$ASIGNA['e'][$row]['countx'];
        $_TOTALX[$usuario[0]]['e']['despac'] .= $_TOTALX[$usuario[0]]['e']['despac'] != '' && $ASIGNA['e'][$row]['despac'] ? ','.$ASIGNA['e'][$row]['despac'] : $ASIGNA['e'][$row]['despac'];
        
        $_TOTALY[$row]['p']['countx'] += (int)$ASIGNA['p'][$row]['countx'];
        $_TOTALY[$row]['p']['despac'] .= $_TOTALY[$row]['p']['despac'] != '' && $ASIGNA['p'][$row]['despac'] != '' ? ','.$ASIGNA['p'][$row]['despac'] : $ASIGNA['p'][$row]['despac'];
        
        $_TOTALY[$row]['e']['countx'] += (int)$ASIGNA['e'][$row]['countx'];
        $_TOTALY[$row]['e']['despac'] .= $_TOTALY[$row]['e']['despac'] != '' && $ASIGNA['e'][$row]['despac'] != '' ? ','.$ASIGNA['e'][$row]['despac'] : $ASIGNA['e'][$row]['despac'];
        
        $_L_P = round($ASIGNA['p'][$row]['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \''.$usuario[0].'\', \'p\', \'d\' );"': '';
        $_L_E = round($ASIGNA['e'][$row]['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \''.$usuario[0].'\', \'e\', \'d\' );"': '';
        $mHtml .= '<td '.$_L_P.' class="'.$style.'" align="center">'.round($ASIGNA['p'][$row]['countx']).'</td>';
        $mHtml .= '<td '.$_L_E.' class="'.$style.'" align="center">'.round($ASIGNA['e'][$row]['countx']).'</td>';
      }
      
      $_L_P = round($_TOTALX[$usuario[0]]['p']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \''.$usuario[0].'\', \'p\', \'d\' );"': '';
      $_L_E = round($_TOTALX[$usuario[0]]['e']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \''.$usuario[0].'\', \'e\', \'d\' );"': '';
        
      $mHtml .= '<td '.$_L_P.' class="'.$style.'" align="center">'.round($_TOTALX[$usuario[0]]['p']['countx']).'</td>';
      $mHtml .= '<td '.$_L_E.' class="'.$style.'" align="center">'.round($_TOTALX[$usuario[0]]['e']['countx']).'</td>';
      $mHtml .= '</tr>';
      $count++;
    }
    
    $mHtml .= '<tr>';
    $mHtml .= '<td class="CellHead2" align="center"><b>TOTAL</b></td>';
    foreach( $_FECHAS as $row )
    {
      
      $_TOTALY['t']['p']['countx'] += (int)$_TOTALY[$row]['p']['countx'];
      $_TOTALY['t']['p']['despac'] .= $_TOTALY['t']['p']['despac'] != '' && $_TOTALY[$row]['p']['despac'] != '' ? ','.$_TOTALY[$row]['p']['despac'] : $_TOTALY[$row]['p']['despac'];
      
      $_TOTALY['t']['e']['countx'] += (int)$_TOTALY[$row]['e']['countx'];
      $_TOTALY['t']['e']['despac'] .= $_TOTALY['t']['e']['despac'] != '' && $_TOTALY[$row]['e']['despac'] != '' ? ','.$_TOTALY[$row]['e']['despac'] : $_TOTALY[$row]['e']['despac'];
      
      $_L_P = round($_TOTALY[$row]['p']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \'t\', \'p\', \'d\' );"': '';
      $_L_E = round($_TOTALY[$row]['e']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\''.$row.'\', \'t\', \'e\', \'d\' );"': '';
      $mHtml .= '<td '.$_L_P.' class="CellHead" align="center">'.round($_TOTALY[$row]['p']['countx']).'</td>';
      $mHtml .= '<td '.$_L_E.' class="CellHead" align="center">'.round($_TOTALY[$row]['e']['countx']).'</td>';
    }
    $_L_P = round($_TOTALY['t']['p']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \'t\', \'p\', \'d\' );"': '';
    $_L_E = round($_TOTALY['t']['e']['countx']) != '0' ? 'style="cursor:pointer;" onclick="Details(\'t\', \'t\', \'e\', \'d\' );"': '';
    $mHtml .= '<td  '.$_L_P.' class="CellHead" align="center">'.round($_TOTALY['t']['p']['countx']).'</td>';
    $mHtml .= '<td  '.$_L_E.' class="CellHead" align="center">'.round($_TOTALY['t']['e']['countx']).'</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '</table>';
    
    $_SESSION['LIST_TOTAL'] = $mHtml;
    
    $_SESSION['TOTALY'] = $_TOTALY;
    $_SESSION['TOTALX'] = $_TOTALX;
    $_SESSION['NOVEDA'] = $_NOVEDA;
    
    echo '<span id="excelID" onclick="Export();" style="color: #35650F; cursor:pointer; font-family: Trebuchet MS,Verdana,Arial; font-size: 13px;">[Excel]</span><br><br>';
    echo $mHtml;
  }
  
  function toFecha( $date )
  {
    $fecha = explode( '-', $date );
    $month = array( 1 => 'Enero',  2 => 'Febrero',  3 => 'Marzo',  4 => 'Abril',  5 => 'Mayo',  6 => 'Junio',  7 => 'Julio',  8 => 'Agosto',  9 => 'Setiempre',  10 => 'Octubre',  11 => 'Noviembre',  12 => 'Diciembre' );
    $fechats = strtotime( $date ); 

    switch ( date( 'w', $fechats ) ) 
    {
      case 0: $ND = "Domingo"; break;
      case 1: $ND = "Lunes"; break;
      case 2: $ND = "Martes"; break;
      case 3: $ND = "Mi&eacute;rcoles"; break;
      case 4: $ND = "Jueves"; break;
      case 5: $ND = "Viernes"; break;
      case 6: $ND = "S&aacute;bado"; break;
    } 
    return $ND."<br>".$fecha[2]." de ".$month[(int)$fecha[1]]." / ".$fecha[0];
  }
  
  function validateFecha( $ano, $mes, $dia )
  {
    return checkdate( $mes, $dia, $ano );
  }
  
  function getInform( $mData )
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    
    switch( $mData['cod_tipinf'] )
    {
      case 'D':
        $this -> InfDiario( $mData );
      break;
      
      case 'S':
        $this -> InfSemanal( $mData );
      break;
      
      case 'M':
        $this -> InfMensual( $mData );
      break;
      
    }
    
  }
}

$proceso = new AjaxDespacUsuari();
 ?>