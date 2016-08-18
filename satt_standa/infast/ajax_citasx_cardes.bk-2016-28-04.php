<?php
ini_set('display_errors', false);
session_start();

class AjaxIndicadorCitasDescargue
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
    $archivo = "Indicador_Citas_Descargue_".date( "Y_m_d" ).".xls";
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
        
        .StyleDIV
          {
            background-color: rgb(240, 240, 240);
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 99%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
        /*.Style2DIV
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
  
  protected function getOrigen( $_AJAX )
  {
    $mSql = "SELECT b.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3)),
                    b.cod_depart
               FROM ".BASE_DATOS.".tab_genera_ciudad b, 
                    ".BASE_DATOS.".tab_genera_depart d,
                    ".BASE_DATOS.".tab_genera_paises e
              WHERE b.cod_depart = d.cod_depart AND
                    b.cod_paisxx = d.cod_paisxx AND
                    d.cod_paisxx = e.cod_paisxx AND
                    b.ind_estado = '1' AND
                    (b.cod_ciudad LIKE '%". $_AJAX['term'] ."%' OR CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3)) LIKE '%". $_AJAX['term'] ."%' )
              GROUP BY 1
              LIMIT 10";
    
    $consulta = new Consulta( $mSql, $this -> conexion );
    $transpor = $consulta -> ret_matriz();
    
    $data = array();
    for($i=0, $len = count($transpor); $i<$len; $i++){
       $data [] = '{"label":"'.$transpor[$i][0].' - '.utf8_encode( $transpor[$i][1] ).'","value":"'.$transpor[$i][0].' - '.utf8_encode( $transpor[$i][1] ).'"}'; 
    }
    echo '['.join(', ',$data).']';
    
  }
  
  function getTipDes( $cod_tipdes = NULL )
  {
    $mSelect = "SELECT cod_tipdes, UPPER( nom_tipdes ) as nom_tipdes 
                  FROM ".BASE_DATOS.".tab_genera_tipdes 
                 WHERE 1 = 1 ";
    
    if( $cod_tipdes != NULL )
      $mSelect .= " AND cod_tipdes = '".$cod_tipdes."' ";
    
    $mSelect .= " ORDER BY 2";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  /**********************************************************************/
  /* @Function verifyCumplimiento()                                     */
  /* @Author   Felipe Malaver                                           */
  /* Esta es la funcion mas importante de todo el desarrollo porque es  */
  /* el que determina el cumplimiento o incumplimiento de la cita de    */
  /* descargue                                                          */
  /**********************************************************************/
  protected function verifyCumplimiento( $num_despac, $num_factur )
  {
    $mSelect = "SELECT
                  a.num_despac, a.fec_citdes, a.hor_citdes, 
                  a.ind_cumdes, a.fec_cumdes, a.nom_destin    
                FROM 
                  ".BASE_DATOS.".tab_despac_destin a
                WHERE
                  a.num_despac = '".$num_despac."' AND
                  a.num_docume = '".$num_factur."' ";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DESTIN = $consulta -> ret_matriz();
    
    $ind_cumplido = '0';
    if( stristr( strtolower( $_DESTIN[0]['nom_destin'] ), 'corona' ) || stristr( strtolower( $_DESTIN[0]['nom_destin'] ), 'sodimac' ) )
        {
      if( $_DESTIN[0]['ind_cumdes'] == '1' )
        $ind_cumplido = '1';
      else
        $ind_cumplido = '0';
        }
    elseif( $_DESTIN[0]['fec_citdes'] != '0000-00-00' && $_DESTIN[0]['hor_citdes'] != '0000:00:00' )
    {
      $fec_finali = $_DESTIN[0]['fec_citdes'] . " " . $_DESTIN[0]['hor_citdes'];
      if( $_DESTIN[0]['ind_cumdes'] == '1' && $this -> dateDiff( $_DESTIN[0]['fec_cumdes'], $fec_finali, 30 ) )
        $ind_cumplido = '1';
      else
        $ind_cumplido = '0';
    }
    else
    {
      $fec_finali = $_DESTIN[0]['fec_citdes'] . " " . $_DESTIN[0]['hor_citdes'];
      if( $_DESTIN[0]['ind_cumdes'] == '1' && $fec_finali == $_DESTIN[0]['fec_cumdes'] )
        $ind_cumplido = '1';
      else
        $ind_cumplido = '0';
    }

    return $ind_cumplido;
  }
  
  protected function getData( $mData )
  {

    $mData['date_inicia'] = $mData['fec_inicia']." ".$mData['hor_inicia'];
    $mData['date_finali'] = $mData['fec_finali']." ".$mData['hor_finali'];
    
    $mSelect = "SELECT 
                  a.num_despac, c.num_docume, a.cod_tipdes, 
                  c.fec_citdes, c.hor_citdes, DATE( a.fec_salida ) AS fec_salida
                FROM 
                  ".BASE_DATOS.".tab_despac_despac a, 
                       ".BASE_DATOS.".tab_despac_vehige b,
                  ".BASE_DATOS.".tab_despac_destin c,
                  ".BASE_DATOS.".tab_despac_sisext d,
                  ".BASE_DATOS.".tab_despac_corona e
                WHERE 
                  a.num_despac = b.num_despac AND 
                  a.num_despac = c.num_despac AND 
                  a.num_despac = d.num_despac AND 
                  a.num_despac = e.num_dessat AND 
                  b.cod_transp = '". $this -> NIT_CORONA ."' AND 
                  a.fec_salida IS NOT NULL AND 
                  a.fec_llegad IS NOT NULL AND 
                  a.ind_anulad != 'A' AND 
                  b.ind_activo = 'S' AND 
                  c.fec_citdes IS NOT NULL AND
                  c.fec_citdes != '0000-00-00' AND 
                  c.hor_citdes != '00:00:00' AND 
                  a.fec_salida BETWEEN '".$mData['date_inicia']."' AND '".$mData['date_finali']."' ";
    
    /* FILTROS ******************************************************************************************/
    $mSelect .= $mData['cod_ciuori'] != '' ? " AND a.cod_ciuori = '".$mData['cod_ciuori']."'" : "";
    $mSelect .= $mData['cod_ciudes'] != '' ? " AND a.cod_ciudes = '".$mData['cod_ciudes']."'" : "";
    $mSelect .= $mData['cod_tipdes'] != '' ? " AND a.cod_tipdes = '".$mData['cod_tipdes']."'" : "";
    $mSelect .= $mData['cod_produc'] != '' ? " AND d.cod_mercan = '".$mData['cod_produc']."'" : "";
    $mSelect .= $mData['num_viajex'] != '' ? " AND d.num_desext = '".$mData['num_viajex']."'" : "";
    $mSelect .= $mData['cod_zonaxx'] != '' ? " AND e.cod_instal = '".$mData['cod_zonaxx']."'" : "";
    $mSelect .= $mData['cod_canalx'] != '' ? " AND e.cod_canalx = '".$mData['cod_canalx']."'" : "";
    $mSelect .= $mData['cod_tiptra'] != '' ? " AND e.tip_transp = '".$mData['cod_tiptra']."'" : "";
    $mSelect .= $mData['num_pedido'] != '' ? " AND e.num_pedido = '".$mData['num_pedido']."'" : "";
    $mSelect .= $mData['nom_poseed'] != '' ? " AND e.nom_poseed LIKE '%".$mData['nom_poseed']."%'" : "";    
    /****************************************************************************************************/

    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DESPAC = $consulta -> ret_matriz();
    
    $mResponse = array();
    $mDetailsx = array();
    
    foreach( $_DESPAC as $row )
      {
      $ind_cumpli = $this -> verifyCumplimiento( $row['num_despac'], $row['num_docume'] );
      /* DATOS DE EL PRIMER RECUADRO DEL INDICADOR */
      /* Recuadro general **************************/
      $mResponse[$ind_cumpli]['g']['totalx'] ++;
      $mResponse[$ind_cumpli]['g']['despac'] .= $mResponse[$ind_cumpli]['g']['despac'] != '' ? ';'.$row['num_despac'].'|'.$row['num_docume'] : $row['num_despac'].'|'.$row['num_docume'];
      /* Recuadro por cada tipo de Despacho ********/
      $mResponse[$ind_cumpli][ $row['cod_tipdes'] ]['totalx'] ++;
      $mResponse[$ind_cumpli][ $row['cod_tipdes'] ]['despac'] .= $mResponse[$ind_cumpli][$row['cod_tipdes']]['despac'] != '' ? ';'.$row['num_despac'].'|'.$row['num_docume'] : $row['num_despac'].'|'.$row['num_docume'];
      /*********************************************/
    
      /* DATOS DE EL SEGUNDO RECUADRO DEL INDICADOR */
      /* Recuadro general ***************************/
      $mDetailsx[$ind_cumpli]['g'][ $row['fec_salida'] ]['totalx'] ++;
      $mDetailsx[$ind_cumpli]['g'][ $row['fec_salida'] ]['despac'] .= $mDetailsx[$ind_cumpli]['g'][ $row['fec_salida'] ]['despac'] != '' ? ';'.$row['num_despac'].'|'.$row['num_docume'] : $row['num_despac'].'|'.$row['num_docume'];
      /* Recuadro por cada tipo de Despacho *********/
      $mDetailsx[$ind_cumpli][ $row['cod_tipdes'] ][ $row['fec_salida'] ]['totalx'] ++;
      $mDetailsx[$ind_cumpli][ $row['cod_tipdes'] ][ $row['fec_salida'] ]['despac'] .= $mDetailsx[$ind_cumpli][ $row['cod_tipdes'] ][ $row['fec_salida'] ]['despac'] != '' ? ';'.$row['num_despac'].'|'.$row['num_docume'] : $row['num_despac'].'|'.$row['num_docume'];
      /**********************************************/
    }
    $mReturnxx['mResponse'] = $mResponse;
    $mReturnxx['mDetailsx'] = $mDetailsx;
      
    return $mReturnxx;
  }
  
  protected function DetailsIndicador( $mData )
  {
    $_INFORM = $_SESSION['INFORM'];
    
    $mRespon = $_INFORM['mResponse'];
    $mDetail = $_INFORM['mDetailsx'];
    
    if( $mData['fec_subniv'] == '' )
    {
      if( $mData['ind_cumpli'] == 'T' )
      {
        $mDespac  = $mRespon[0][ $mData['cod_tipser'] ]['despac']; 
        $mDespac .= $mDespac != '' && $mRespon[1][ $mData['cod_tipser'] ]['despac'] != '' ? ';'.$mRespon[1][ $mData['cod_tipser'] ]['despac'] : $mRespon[1][ $mData['cod_tipser'] ]['despac']; 
      }
      else
      {
        $mDespac = $mRespon[ $mData['ind_cumpli'] ][ $mData['cod_tipser'] ]['despac']; 
        }
      }
    else
    {
      if( $mData['ind_cumpli'] == 'T' )
  {
        $mDespac  = $mDetail[0][ $mData['cod_tipser'] ][ $mData['fec_subniv'] ]['despac']; 
        $mDespac .= $mDespac != '' && $mDetail[1][ $mData['cod_tipser'] ][ $mData['fec_subniv'] ]['despac'] != '' ? ';'.$mDetail[1][ $mData['cod_tipser'] ][ $mData['fec_subniv'] ]['despac'] : $mDetail[1][ $mData['cod_tipser'] ][ $mData['fec_subniv'] ]['despac']; 
    }
    else
    {
        $mDespac = $mDetail[ $mData['ind_cumpli'] ][ $mData['cod_tipser'] ][ $mData['fec_subniv'] ]['despac']; 
      }
    }
    
    $mConsul = explode ( ';', $mDespac );
                    
    $mHtml  = '<table width="100%" cellpadding="0" cellspacing="1">';
    
    $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead99" style="font-size:12px; color:#FFFFFF;" colspan="24">Se Encontr&oacute; un Total de '.count( $mConsul ).' Despachos</td>';
      $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
        $mHtml .= '<td class="CellHead">Despacho SAT</td>';
        $mHtml .= '<td class="CellHead">Viaje</td>';
        $mHtml .= '<td class="CellHead">Monifiesto</td>';
        $mHtml .= '<td class="CellHead">Fecha Despacho</td>';
        $mHtml .= '<td class="CellHead">Tipo Despacho</td>';
        $mHtml .= '<td class="CellHead">Origen</td>';
        $mHtml .= '<td class="CellHead">Destino</td>';
        $mHtml .= '<td class="CellHead">Fecha Cita Cargue</td>';
        $mHtml .= '<td class="CellHead">cumplimiento Cita Cargue</td>';
        $mHtml .= '<td class="CellHead">Nombre Sitio Cargue</td>';
        $mHtml .= '<td class="CellHead">Peso(Kg)</td>';
        $mHtml .= '<td class="CellHead">Observaciones</td>';
        $mHtml .= '<td class="CellHead">Ultima Novedad</td>';
        $mHtml .= '<td class="CellHead">C.C. Conductor</td>';
        $mHtml .= '<td class="CellHead">Nombre Conductor</td>';
        $mHtml .= '<td class="CellHead">Celular Conductor</td>';
        $mHtml .= '<td class="CellHead">Solicitud</td>';
        $mHtml .= '<td class="CellHead">Pedido</td>';
        $mHtml .= '<td class="CellHead">Placa</td>';
        $mHtml .= '<td class="CellHead">Tipo Vehiculo</td>';
        $mHtml .= '<td class="CellHead">Poseedor</td>';
        $mHtml .= '<td class="CellHead">Tipo Transportadora</td>';
        $mHtml .= '<td class="CellHead">Mercancia/Negocio</td>';
        $mHtml .= '<td class="CellHead">Cliente(s)</td>';
        $mHtml .= '<td class="CellHead">cumplimiento Cita Descargue</td>';
        $mHtml .= '<td class="CellHead">Novedad Cita Descargue</td>';
        $mHtml .= '<td class="CellHead">Fecha Llegada al Cliente</td>';
        $mHtml .= '<td class="CellHead">Fecha Entrada a Descargue</td>';
        $mHtml .= '<td class="CellHead">Fecha Salida del Descargue</td>';
        $mHtml .= '<td class="CellHead">Fecha del Cumplido</td>';
    $mHtml .= '</tr>';
    
      include_once('classData.php');
      $cData = new generaData( $this -> conexion );
    
      foreach( $mConsul as $row )
  {
        $mDataDespac = explode( '|', $row );
        $mHtml .= $cData -> getDataCitaDescargue( $mDataDespac );
    }
    
    $mHtml .= '</table>';
                    
    echo '<span id="excelID" onclick="Export();" style="color: #FFFFFF; cursor:pointer; font-family: Trebuchet MS,Verdana,Arial; font-size: 13px;">[Excel]</span><br><br>';
    $_SESSION['LIST_TOTAL'] = $mHtml;
    echo $mHtml;
  }
  
  protected function getInform( $mData )
  {
    $_SESSION['INFORM'] = $_INFORM = $this -> getData( $mData );
    
    $mRespCump = $_INFORM['mResponse'][1];
    $mRespIncu = $_INFORM['mResponse'][0];
    $mDetaCump = $_INFORM['mDetailsx'][1];
    $mDetaIncu = $_INFORM['mDetailsx'][0];
    
    $this -> Style();
    
    $_TIPDES = $this -> getTipDes();
                 
    echo '<script>
          $(function() {
            $( "#mainInformID" ).tabs();
          });
          </script>';
    
    $mHtml  = '<div id="mainInformID">';
    
      $mHtml .= '<ul>';
        $mHtml .= '<li><a href="#generalID">GENERAL</a></li>';
      
        foreach( $_TIPDES as $row )
      {
          $mHtml .= '<li><a href="#'.str_replace( ' ', '_', $row['nom_tipdes'] ).'ID">'.$row['nom_tipdes'].'</a></li>';
          }
    
      $mHtml .= '</ul>';
    
      $mHtml .= '<div id="generalID">';
        $mHtml .= '<div class="StyleDIV">';
          $mHtml .= '<table width="98%" cellpadding="0" cellspacing="1">';
      
            $mTitle = 'INDICADOR GENERAL DE CITAS DE DESCARGUE COMPRENDIDO ENTRE '.$mData['fec_inicia'].' Y '.$mData['fec_finali']; 
      
            $mHtml .= '<tr>';
              $mHtml .= '<td class="CellHead" colspan="5">'.$mTitle.'</td>';
      $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead" align="center">GENERADAS</td>';
                $mHtml .= '<td class="CellHead" align="center">CUMPLIDAS</td>';
                $mHtml .= '<td class="CellHead" align="center">PORCENTAJE</td>';
                $mHtml .= '<td class="CellHead" align="center">NO CUMPLIDAS</td>';
                $mHtml .= '<td class="CellHead" align="center">PORCENTAJE</td>';
    $mHtml .= '</tr>';

              /* TOTALES PARA LOS HIPERVINCULOS *****************/
              /**/ $mTotcum = (int)$mRespCump['g']['totalx']; /**/
              /**/ $mTotinc = (int)$mRespIncu['g']['totalx']; /**/
              /**/ $mTotalx = $mTotcum + $mTotinc;            /**/
              /**/ $mPorcum = ( $mTotcum * 100 ) / $mTotalx;  /**/
              /**/ $mPorinc = ( $mTotinc * 100 ) / $mTotalx;  /**/
              /**************************************************/
  
              /* HIPERVINCULOS **********************************/
              $hTotcum = $mTotcum != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'1\', \'g\', \'\' );">'.$mTotcum.'</a>' : '0';
              $hTotinc = $mTotinc != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'0\', \'g\', \'\' );">'.$mTotinc.'</a>' : '0';
              $hTotalx = $mTotalx != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'T\', \'g\', \'\' );">'.$mTotalx.'</a>' : '0';
              /**************************************************/
    
              $mHtml .= '<tr>';
                $mHtml .= '<td class="cellInfo" align="center">'.$hTotalx.'</td>';
                $mHtml .= '<td class="cellInfo" align="center">'.$hTotcum.'</td>';
                $mHtml .= '<td class="cellInfo" align="center">'.round( $mPorcum, 2 ).' %</td>';
                $mHtml .= '<td class="cellInfo" align="center">'.$hTotinc.'</td>';
                $mHtml .= '<td class="cellInfo" align="center">'.round( $mPorinc, 2 ).' %</td>';
              $mHtml .= '</tr>';
    
            $mHtml .= '</table>';
                 
            $mHtml .= '<br><br>';
    
            $mHtml .= '<table width="98%" cellpadding="0" cellspacing="1">';
      
              $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead" colspan="6">DETALLADO POR DIAS</td>';
              $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead" align="center">FECHA</td>';
                $mHtml .= '<td class="CellHead" align="center">GENERADAS</td>';
                $mHtml .= '<td class="CellHead" align="center">CUMPLIDAS</td>';
                $mHtml .= '<td class="CellHead" align="center">%</td>';
                $mHtml .= '<td class="CellHead" align="center">NO CUMPLIDAS</td>';
                $mHtml .= '<td class="CellHead" align="center">%</td>';
    $mHtml .= '</tr>';
    
              $mFecini = $mData['fec_inicia'];
              $mFecfin = $mData['fec_finali'];
              while( strtotime( $mFecini ) <= strtotime( $mFecfin ) ) 
    {
                /* TOTALES PARA LOS HIPERVINCULOS ***************************/
                /**/ $mTotcum = (int)$mDetaCump['g'][$mFecini]['totalx']; /**/
                /**/ $mTotinc = (int)$mDetaIncu['g'][$mFecini]['totalx']; /**/
                /**/ $mTotalx = $mTotcum + $mTotinc;                      /**/
                /**/ $mPorcum = ( $mTotcum * 100 ) / $mTotalx;            /**/
                /**/ $mPorinc = ( $mTotinc * 100 ) / $mTotalx;            /**/
                /************************************************************/

                /* HIPERVINCULOS **********************************/
                $hTotcum = $mTotcum != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'1\', \'g\', \''.$mFecini.'\' );">'.$mTotcum.'</a>' : '0';
                $hTotinc = $mTotinc != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'0\', \'g\', \''.$mFecini.'\' );">'.$mTotinc.'</a>' : '0';
                $hTotalx = $mTotalx != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'T\', \'g\', \''.$mFecini.'\' );">'.$mTotalx.'</a>' : '0';
                /**************************************************/
      $mHtml .= '<tr class="row">';
                  $mHtml .= '<td class="cellInfo"><b>'.$this -> readDate( $mFecini ).'</b></td>';
                  $mHtml .= '<td class="cellInfo" align="center">'.$hTotalx.'</td>';
                  $mHtml .= '<td class="cellInfo" align="center">'.$hTotcum.'</td>';
                  $mHtml .= '<td class="cellInfo" align="center">'.round( $mPorcum, 2 ).' %</td>';
                  $mHtml .= '<td class="cellInfo" align="center">'.$hTotinc.'</td>';
                  $mHtml .= '<td class="cellInfo" align="center">'.round( $mPorinc, 2 ).' %</td>';
      $mHtml .= '</tr>';
    
                $mFecini = date( "Y-m-d", strtotime($mFecini . " + 1 day" ) );
    }

            $mHtml .= '</table>';
  
          $mHtml .= '</div>';
        $mHtml .= '</div>';
        
        foreach( $_TIPDES as $row )
      {
          $mHtml .= '<div id="'.str_replace( ' ', '_', $row['nom_tipdes'] ).'ID">';
            $mHtml .= '<div class="StyleDIV">';
              $mHtml .= '<table width="98%" cellpadding="0" cellspacing="1">';
    
              $mTitle = 'INDICADOR DE CITAS DE DESCARGUE COMPRENDIDO ENTRE '.$mData['fec_inicia'].' Y '.$mData['fec_finali']; 
    
    $mHtml .= '<tr>';
              $mHtml .= '<td class="CellHead" colspan="5">'.$mTitle.'</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead" align="center">GENERADAS</td>';
                $mHtml .= '<td class="CellHead" align="center">CUMPLIDAS</td>';
                $mHtml .= '<td class="CellHead" align="center">PORCENTAJE</td>';
                $mHtml .= '<td class="CellHead" align="center">NO CUMPLIDAS</td>';
                $mHtml .= '<td class="CellHead" align="center">PORCENTAJE</td>';
    $mHtml .= '</tr>';
    
              /* TOTALES PARA LOS HIPERVINCULOS *****************/
              /**/ $mTotcum = (int)$mRespCump[$row['cod_tipdes']]['totalx']; /**/
              /**/ $mTotinc = (int)$mRespIncu[$row['cod_tipdes']]['totalx']; /**/
              /**/ $mTotalx = $mTotcum + $mTotinc;            /**/
              /**/ $mPorcum = ( $mTotcum * 100 ) / $mTotalx;  /**/
              /**/ $mPorinc = ( $mTotinc * 100 ) / $mTotalx;  /**/
              /**************************************************/

              /* HIPERVINCULOS **********************************/
              $hTotcum = $mTotcum != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'1\', \''.$row['cod_tipdes'].'\', \'\' );">'.$mTotcum.'</a>' : '0';
              $hTotinc = $mTotinc != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'0\', \''.$row['cod_tipdes'].'\', \'\' );">'.$mTotinc.'</a>' : '0';
              $hTotalx = $mTotalx != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'T\', \''.$row['cod_tipdes'].'\', \'\' );">'.$mTotalx.'</a>' : '0';
              /**************************************************/
              
              $mHtml .= '<tr>';
                $mHtml .= '<td class="cellInfo" align="center">'.$hTotalx.'</td>';
                $mHtml .= '<td class="cellInfo" align="center">'.$hTotcum.'</td>';
                $mHtml .= '<td class="cellInfo" align="center">'.round( $mPorcum, 2 ).' %</td>';
                $mHtml .= '<td class="cellInfo" align="center">'.$hTotinc.'</td>';
                $mHtml .= '<td class="cellInfo" align="center">'.round( $mPorinc, 2 ).' %</td>';
      $mHtml .= '</tr>';
    
    $mHtml .= '</table>';
  
            $mHtml .= '<br><br>';
        
            $mHtml .= '<table width="98%" cellpadding="0" cellspacing="1">';
    
    $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead" colspan="6">DETALLADO POR DIAS</td>';
    $mHtml .= '</tr>';
    
    $mHtml .= '<tr>';
                $mHtml .= '<td class="CellHead" align="center">FECHA</td>';
                $mHtml .= '<td class="CellHead" align="center">GENERADAS</td>';
                $mHtml .= '<td class="CellHead" align="center">CUMPLIDAS</td>';
                $mHtml .= '<td class="CellHead" align="center">%</td>';
                $mHtml .= '<td class="CellHead" align="center">NO CUMPLIDAS</td>';
                $mHtml .= '<td class="CellHead" align="center">%</td>';
    $mHtml .= '</tr>';
    
              $mFecini = $mData['fec_inicia'];
              $mFecfin = $mData['fec_finali'];
              while( strtotime( $mFecini ) <= strtotime( $mFecfin ) ) 
    {
                /* TOTALES PARA LOS HIPERVINCULOS ***************************/
                /**/ $mTotcum = (int)$mDetaCump[$row['cod_tipdes']][$mFecini]['totalx']; /**/
                /**/ $mTotinc = (int)$mDetaIncu[$row['cod_tipdes']][$mFecini]['totalx']; /**/
                /**/ $mTotalx = $mTotcum + $mTotinc;                      /**/
                /**/ $mPorcum = ( $mTotcum * 100 ) / $mTotalx;            /**/
                /**/ $mPorinc = ( $mTotinc * 100 ) / $mTotalx;            /**/
                /************************************************************/

                /* HIPERVINCULOS **********************************/
                $hTotcum = $mTotcum != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'1\', \''.$row['cod_tipdes'].'\', \''.$mFecini.'\' );">'.$mTotcum.'</a>' : '0';
                $hTotinc = $mTotinc != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'0\', \''.$row['cod_tipdes'].'\', \''.$mFecini.'\' );">'.$mTotinc.'</a>' : '0';
                $hTotalx = $mTotalx != 0 ? '<a style="cursor:pointer; color:#336600; text-decoration:none;" onclick="DetailsCumdes( \'T\', \''.$row['cod_tipdes'].'\', \''.$mFecini.'\' );">'.$mTotalx.'</a>' : '0';
                /**************************************************/
      $mHtml .= '<tr class="row">';
                  $mHtml .= '<td class="cellInfo"><b>'.$this -> readDate( $mFecini ).'</b></td>';
                  $mHtml .= '<td class="cellInfo" align="center">'.$hTotalx.'</td>';
                  $mHtml .= '<td class="cellInfo" align="center">'.$hTotcum.'</td>';
                  $mHtml .= '<td class="cellInfo" align="center">'.round( $mPorcum, 2 ).' %</td>';
                  $mHtml .= '<td class="cellInfo" align="center">'.$hTotinc.'</td>';
                  $mHtml .= '<td class="cellInfo" align="center">'.round( $mPorinc, 2 ).' %</td>';
      $mHtml .= '</tr>';

                $mFecini = date( "Y-m-d", strtotime($mFecini . " + 1 day" ) );
    }
    
    $mHtml .= '</table>';
          $mHtml .= '</div>';
        $mHtml .= '</div>';
  }
  
    $mHtml .= '</div>';

    echo $mHtml;
  }
  
  function dateDiff( $fec_inicia, $fec_finali, $limit ) 
  {
    $time_ini = strtotime( $fec_inicia );
    $time_fin = strtotime( $fec_finali );

    $diff = round( ( $time_fin - $time_ini ) / 60 );
    
    return $diff <= $limit ? true : false;
  }
  
  function readDate( $mDate )
  {
    $week = date( 'w', strtotime( $mDate ) );
    $date = explode('-', $mDate);
    
    switch ( $week ) 
    {
      case 0:$dia = 'Domingo'; break;
      case 1:$dia = 'Lunes';break;
      case 2:$dia = 'Martes'; break;
      case 3:$dia = 'Mi&eacute;rcoles'; break;
      case 4:$dia = 'Jueves'; break;
      case 5:$dia = 'Viernes'; break;
      case 6:$dia = 'Sabado'; break;
      }
  
    switch ( $date[1] ) 
  {
      case 1:$mes = 'Enero'; break;
      case 2:$mes = 'Febrero'; break;
      case 3:$mes = 'Marzo'; break;
      case 4:$mes = 'Abril'; break;
      case 5:$mes = 'Mayo'; break;
      case 6:$mes = 'Junio'; break;
      case 7:$mes = 'Julio'; break;
      case 8:$mes = 'Agosto'; break;
      case 9:$mes = 'Septiembre'; break;
      case 10:$mes = 'Octubre'; break;
      case 11:$mes = 'Noviembre'; break;
      case 12:$mes = 'Diciembre'; break;
  }
    return $dia.", ".$date[2]." de ".$mes." de ".$date[0];
  }
  }
  
$proceso = new AjaxIndicadorCitasDescargue();
 ?>