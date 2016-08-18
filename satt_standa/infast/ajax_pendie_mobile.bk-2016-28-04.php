<?php
ini_set('display_errors', false);
session_start();

class AjaxPendientesMobile
{
  var $conexion;
  var $TIPCIT = array();
  var $TIPNOV = array();

  public function __construct()
  {
    $this -> TIPCIT[1][0] = '1';
    $this -> TIPCIT[1][1] = 'CITAS DE CARGUE';
    $this -> TIPCIT[2][0] = '2';
    $this -> TIPCIT[2][1] = 'NOVEDADES EN RUTA';
    $this -> TIPCIT[3][0] = '3';
    $this -> TIPCIT[3][1] = 'CITAS DE DESCARGUE';
    
    $this -> TIPNOV['CARGUE'] = array('186', '188');
    $this -> TIPNOV['RUTAXX'] = array('257', '258');
    $this -> TIPNOV['DESCAR'] = array('259', '260', '261');
    
    $_AJAX = $_REQUEST;
    include_once('../lib/bd/seguridad/aplica_filtro_perfil_lib.inc');
    include_once('../lib/bd/seguridad/aplica_filtro_usuari_lib.inc');
    include_once('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');
    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
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
        
        .StyleMainDIV
          {
            background-color: rgb(240, 240, 240);
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 99%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .StyleDIV
          {
            background-color: #FFFFFF;
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 100%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .TRform
          {
            padding-right:3px; 
            padding-top:15px; 
            font-family:Trebuchet MS, Verdana, Arial; 
            font-size:12px;
          }
        </style>';
  }

  protected function getTipdes( $cod_tipdes = NULL )
  {
    $mSelect = "SELECT cod_tipdes, nom_tipdes 
                  FROM ".BASE_DATOS.".tab_genera_tipdes 
                 WHERE 1 = 1";

    if( $cod_tipdes != NULL )
    {
      $mSelect .= " AND cod_tipdes = '".$cod_tipdes."' ";
    }             

    $mSelect .= " ORDER BY 1";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
  }

  protected function loadInform( $mData )
  {
    $this -> Style();
    $_TIPDES = $this -> getTipdes();
    $colspan = sizeof( $_TIPDES );

    $mSelect = "SELECT a.num_despac, a.cod_noveda, b.cod_transp, 
    				   c.cod_tipdes, a.eta_notify
                  FROM ".BASE_DATOS.".tab_notify_mobile a 
            INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
                    ON a.num_despac = b.num_despac
            INNER JOIN ".BASE_DATOS.".tab_despac_despac c 
                    ON a.num_despac = c.num_despac
                 WHERE a.ind_soluci = '0' 
                 GROUP BY 1, 2 ";

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DESPAC = $consulta -> ret_matriz();

    $arr_despac = array();
    foreach( $_DESPAC as $regist )
    {
      $tip_noveda = $regist['eta_notify'];
      $arr_despac[$tip_noveda][ $regist['cod_transp'] ][ $regist['cod_noveda'] ][ $regist['cod_tipdes'] ]['totalx'] ++;
      $arr_despac[$tip_noveda][ $regist['cod_transp'] ][ $regist['cod_noveda'] ][ $regist['cod_tipdes'] ]['despac'] .= $arr_despac[ $regist['cod_transp'] ][$tip_noveda][ $regist['cod_tipdes'] ]['despac'] != '' ? ','.$regist['num_despac'] : $regist['num_despac'];
    }

    echo '<script>
          $(function() {
            $( "#mainInformID" ).tabs();
          });
          </script>';

    $mHtml  = '<div id="mainInformID">';
    
    $mHtml .= '<ul>';
    foreach( $this -> TIPCIT as $row )
      $mHtml .= '<li><a href="#'.str_replace( ' ', '_', $row[1] ).'ID">'.$row[1].'</a></li>';
    $mHtml .= '</ul>';
    
    foreach( $this -> TIPCIT as $row )
    {
      $mHtml .= '<div id="'.str_replace( ' ', '_', $row[1] ).'ID">';
        $mHtml .= '<div class="StyleDIV">';
        
          $mHtml .= '<table width="100%" cellpadding="0" cellspacing="1">';
          
            $mTransp = $arr_despac[ $row[0] ];
            
            switch( $row[0] )
            {
              case '1':

                $mArrNoveda = array( 
                                '330' => 'REPORTA ACCIDENTE',
                                '331' => 'REPORTA VARADA',
                                '332' => 'REPORTA TRANCON EN LA VIA',
                                '333' => 'REPORTA HURTO',
                                '334' => 'REPORTA NOVEDAD EN PUESTO DE CONTROL',
                                '335' => 'OTRO EVENTO'
                              );
                $mHtml .= '<tr>';
                  $mHtml .= '<td rowspan="2" class="CellHead" align="center">EMPRESA</td>';
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">ACCIDENTE</td>';  
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">VARADA</td>';  
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">TRANCON EN LA VIA</td>';  
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">HURTO</td>';  
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">NOVEDAD SITIO CARGUE</td>';  
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">OTRO EVENTO</td>';  
                $mHtml .= '</tr>';
                
                $mHtml .= '<tr>';
                for( $i = 0; $i < count( $mArrNoveda ); $i++ )
                  foreach( $_TIPDES as $modali )
                    $mHtml .= '<td class="CellHead" style="font-size:9px;">'.$modali['nom_tipdes'].'</td>';  
                $mHtml .= '</tr>';

                foreach( $mTransp as $cod_transp => $mInfo )
                {
                  $mHtml .= '<tr class="row">';
                    $mHtml .= '<td nowrap class="cellInfo">'.$this -> getTercer( $cod_transp ).'</td>';  
                    
                    foreach( $mArrNoveda as $cod_noveda => $nom_noveda )
                    {
                      foreach( $_TIPDES as $modali )
                      {
                        $vlr_indica = (int)$mInfo[$cod_noveda][ $modali['cod_tipdes'] ]['totalx'];
                        $mDespac = $mInfo[$cod_noveda][ $modali['cod_tipdes'] ]['despac'];
                        $mTotal = $vlr_indica > 0 ? '<a onclick="showDetails(\''.$mDespac.'\', \''.$row[0].'\', \''.$cod_noveda.'\')" style="cursor:pointer; text-decoration:none; color:#00660F;">'.$vlr_indica.'</a>' : '- - -';
                        $mHtml .= '<td class="cellInfo" align="center">'.$mTotal.'</td>';  
                      }
                    }

                  $mHtml .= '</tr>';
                }
                
              break;

              case '2':

                $mArrNoveda = array( 
                                '330' => 'REPORTA ACCIDENTE',
                                '331' => 'REPORTA VARADA',
                                '332' => 'REPORTA TRANCON EN LA VIA',
                                '333' => 'REPORTA HURTO',
                                '334' => 'REPORTA NOVEDAD EN PUESTO DE CONTROL',
                                '335' => 'OTRO EVENTO'
                              );
                $mHtml .= '<tr>';
                  $mHtml .= '<td rowspan="2" class="CellHead" align="center">EMPRESA</td>';
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">ACCIDENTE</td>';  
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">VARADA</td>';  
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">TRANCON EN LA VIA</td>';  
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">HURTO</td>';  
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">NOVEDAD P/C</td>';  
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">OTRO EVENTO</td>';  
                $mHtml .= '</tr>';

                $mHtml .= '<tr>';
                for( $i = 0; $i < count( $mArrNoveda ); $i++ )
                  foreach( $_TIPDES as $modali )
                    $mHtml .= '<td class="CellHead" style="font-size:9px;">'.$modali['nom_tipdes'].'</td>';  
                $mHtml .= '</tr>';

                foreach( $mTransp as $cod_transp => $mInfo )
                {
                  $mHtml .= '<tr class="row">';
                    $mHtml .= '<td nowrap class="cellInfo">'.$this -> getTercer( $cod_transp ).'</td>';  
                    
                    foreach( $mArrNoveda as $cod_noveda => $nom_noveda )
                    {
                      foreach( $_TIPDES as $modali )
                      {
                        $vlr_indica = (int)$mInfo[$cod_noveda][ $modali['cod_tipdes'] ]['totalx'];
                        $mDespac = $mInfo[$cod_noveda][ $modali['cod_tipdes'] ]['despac'];
                        $mTotal = $vlr_indica > 0 ? '<a onclick="showDetails(\''.$mDespac.'\', \''.$row[0].'\', \''.$cod_noveda.'\')" style="cursor:pointer; text-decoration:none; color:#00660F;">'.$vlr_indica.'</a>' : '- - -';
                        $mHtml .= '<td class="cellInfo" align="center">'.$mTotal.'</td>';  
                      }
                    }

                  $mHtml .= '</tr>';
                }

              break;

              case '3':
                $mArrNoveda = array( 
                                '259' => 'NOTIFICA LLEGADA A DESTINATARIO',
                                '260' => 'NOTIFICA NOVEDAD EN DESTINATARIO',
                                '261' => 'NOTIFICA SALIDA DE DESTINATARIO Y CUMPLIDO',
                              );
                $mHtml .= '<tr>';
                  $mHtml .= '<td rowspan="2" class="CellHead" align="center">EMPRESA</td>';
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">LLEGADA TARDE</td>';
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">NOVEDAD DESTINATARIO</td>';
                  $mHtml .= '<td colspan="'.$colspan.'" class="CellHead">CUMPLIDO</td>';
                $mHtml .= '</tr>';

                $mHtml .= '<tr>';
                for( $i = 0; $i < count( $mArrNoveda ); $i++ )
                  foreach( $_TIPDES as $modali )
                    $mHtml .= '<td class="CellHead" style="font-size:9px;">'.$modali['nom_tipdes'].'</td>';  
                $mHtml .= '</tr>';

                foreach( $mTransp as $cod_transp => $mInfo )
                {
                  $mHtml .= '<tr class="row">';
                    $mHtml .= '<td nowrap class="cellInfo">'.$this -> getTercer( $cod_transp ).'</td>';  
                    
                    foreach( $mArrNoveda as $cod_noveda => $nom_noveda )
                    {
                      foreach( $_TIPDES as $modali )
                      {
                        $vlr_indica = (int)$mInfo[$cod_noveda][ $modali['cod_tipdes'] ]['totalx'];
                        $mDespac = $mInfo[$cod_noveda][ $modali['cod_tipdes'] ]['despac'];
                        $mTotal = $vlr_indica > 0 ? '<a onclick="showDetails(\''.$mDespac.'\', \''.$row[0].'\', \''.$cod_noveda.'\')" style="cursor:pointer; text-decoration:none; color:#00660F;">'.$vlr_indica.'</a>' : '- - -';
                        $mHtml .= '<td class="cellInfo" align="center">'.$mTotal.'</td>';  
                      }
                    }

                  $mHtml .= '</tr>';
                }
              break;
            
            }

          $mHtml .= '</table>';
        
        $mHtml .= '</div>';
      $mHtml .= '</div>';
    }

    $mHtml .= '</div>';

    echo $mHtml;
  }

  protected function showDetails( $mData )
  {
    $mSelect = "SELECT 
                  a.num_despac, b.num_desext, c.num_placax
                FROM  
                  ".BASE_DATOS.".tab_notify_mobile a 
                LEFT JOIN 
                  ".BASE_DATOS.".tab_despac_sisext b
                ON
                  a.num_despac = b.num_despac 
                INNER JOIN
                  ".BASE_DATOS.".tab_despac_vehige c
                ON
                  a.num_despac = c.num_despac 
                WHERE
                  a.num_despac IN( ".$mData['num_despac']." ) AND
                  a.cod_noveda = '".$mData['cod_noveda']."' AND
                  a.eta_notify = '".$mData['tip_alarma']."' AND 
                  a.ind_soluci = '0' 
                GROUP BY
                  a.num_despac";

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DESPAC = $consulta -> ret_matriz();

    $this -> Style();

    $mHtml  = '<div class="StyleMainDIV" align="center">';
    $mHtml .= '<table width="100%" cellspacing="1" cellpadding="0">';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td colspan="5">Total: <b>'.sizeof( $_DESPAC ).' Despachos</b><br>Tipo de Notificaci&oacute;n: <b>'.$this -> TIPCIT[$mData['tip_alarma']][1].'</b><br><br></td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
    $mHtml .= '<td class="CellHead" align="center">DESPACHO</td>';
    $mHtml .= '<td class="CellHead" align="center">VIAJE</td>';
    $mHtml .= '<td class="CellHead" align="center">PLACA</td>';
    $mHtml .= '<td class="CellHead" align="center">No. NOTIFICACIONES</td>';
    $mHtml .= '<td class="CellHead" align="center">TIEMPO<br><small>Total Minutos</small></td>';
    $mHtml .= '</tr>';
    
    for( $i = 0; $i < sizeof( $_DESPAC ) ; $i++ )
    {
      $row = $_DESPAC[$i];

      $mSelect = "SELECT
                    num_consec, fec_creaci
                  FROM 
                    ".BASE_DATOS.".tab_notify_mobile 
                  WHERE
                    num_despac = '".$row['num_despac']."' AND
                    cod_noveda = '".$mData['cod_noveda']."' AND
                    eta_notify = '".$mData['tip_alarma']."' AND 
                    ind_soluci = '0' 
                  ORDER BY 
                    2, 1 ";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $num_notify = $consulta -> ret_matriz();

      $_DESPAC[$i]['can_notify'] = sizeof( $num_notify );
      $_DESPAC[$i][3] = sizeof( $num_notify );
      $_DESPAC[$i]['tie_prinot'] = $this -> getDateDiffNow( $num_notify[0]['fec_creaci']);
      $_DESPAC[$i][4] = $this -> getDateDiffNow( $num_notify[0]['fec_creaci']);
    }

    $new_despac = $this -> BubbleSort( $_DESPAC, 4 );

    $count = 0;
    foreach( $new_despac as $row )
    {
      $style = $count % 2 == 0 ? 'cellInfo1' : 'cellInfo2';
      $mHtml .= '<tr>';
      $color = $this -> getCodColor( $row['tie_prinot'] );
      $content = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      $mSpan = '<span style="background-color:#'.$color.'; padding:1px solid #'.$color.';">'.$content.'</span>';
      $mHref = '<a href="index.php?cod_servic=3302&window=central&despac='.$row['num_despac'].'&opcion=1" style="cursor:pointer; text-decoration:none; color:#00660F;" target="_blank">'.$row['num_despac'].'</a>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$mSpan.'&nbsp;'.$mHref.'&nbsp;'.$mSpan.'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_desext'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['num_placax'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['can_notify'].'</td>';
      $mHtml .= '<td class="'.$style.'" align="center">'.$row['tie_prinot'].'</td>';
      $mHtml .= '</tr>';
      $count++;
    }
    
    $mHtml .= '</table>';
    $mHtml .= '</div>';
  
    echo $mHtml;
  }

  protected function getCodColor( $tie_alarma )
  {
    $mSelect = "SELECT cod_colorx, cant_tiempo 
                  FROM ".BASE_DATOS.".tab_genera_alarma 
                 ORDER BY 2";

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $cod_colorx = $consulta -> ret_matriz();

    $des_colorx = '';
    $ant_minuto = 0;
    for( $j = 0; $j < sizeof( $cod_colorx ); $j++ )
    {
      $sig_minuto = $cod_colorx[$j]['cant_tiempo']; 
      if( $tie_alarma > $ant_minuto && $tie_alarma <= $sig_minuto )
        $des_colorx = $cod_colorx[$j]['cod_colorx'];
      $ant_minuto = $sig_minuto + 1;
    }

    if( $tie_alarma > $sig_minuto )
      $des_colorx = $cod_colorx[sizeof($cod_colorx)-1]['cod_colorx'];

    return $des_colorx;
  }

  protected function BubbleSort( $A, $index )
  {
    $n = sizeof( $A );
    for( $i = 1; $i < $n ; $i++ )
    {
      for( $j = 0 ; $j < $n - $i ; $j++ )
      {
        if( $A[$j][$index] <= $A [$j+1][$index] )
          {
            $k = $A[$j+1]; $A[$j+1]=$A[$j]; $A[$j]=$k;
          }
      }
    }
    return $A;
  }

  protected function getDateDiffNow( $fecha_fin ) {
    $time_inicio = strtotime( date('Y-m-d H:i:s') );
    $time_fin = strtotime( $fecha_fin );

    return (int)( round( ( $time_inicio - $time_fin ) / 60 ) );
    
  }

  protected function getTercer( $cod_tercer )
  {
    $mSelect = "SELECT UPPER( abr_tercer ) AS abr_tercer 
                  FROM ".BASE_DATOS.".tab_tercer_tercer 
                 WHERE cod_tercer = '".$cod_tercer."' 
                 LIMIT 1";
  
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $result = $consulta -> ret_matriz();
    return $result[0][0]; 
  }
}

$proceso = new AjaxPendientesMobile();

?>