<?php
session_start();

class AjaxSintesisOperacion
{
  var $conexion;

  public function __construct()
  {
    $_AJAX = $_REQUEST;
    include_once('../lib/bd/seguridad/aplica_filtro_perfil_lib.inc');
    include_once('../lib/bd/seguridad/aplica_filtro_usuari_lib.inc');
    include_once('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');
    include_once('../lib/general/functions.inc');
    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }
  
  public function expInformExcel()
  {    
    $archivo = "Sintesis_Operacion_".date( "Y_m_d" ).".xls";
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
          font-family:Times New Roman;
          font-size:11px;
          background-color: #285c00;
          color:#FFFFFF;
          padding: 4px;
        }
        
        .CellHead99
        {
          font-family:Times New Roman;
          font-size:11px;
          background-color: #285c00;
          color:#FFFFFF;
          padding: 4px;
          text-align:left;
        }
        
        .cellInfo1
        {
          font-family:Times New Roman;
          font-size:11px;
          background-color: #EBF8E2;
          padding: 2px;
        }
        
        .cellInfo2
        {
          font-family:Times New Roman;
          font-size:11px;
          background-color: #DEDFDE;
          padding: 2px;
        }
        
        .cellInfo
        {
          font-family:Times New Roman;
          font-size:11px;
          background-color: #FFFFFF;
          padding: 2px;
        }
        
        tr.row:hover  td
        {
          background-color: #9ad9ae;
        }

        .onlyCell:hover
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
            font-family:Times New Roman; 
            font-size:12px;
          }
        </style>';
  }
  
  function getProduc( $cod_produc = NULL )
  {
  	$mSelect = "SELECT cod_produc, nom_produc 
                  FROM ".BASE_DATOS.".tab_genera_produc 
                 WHERE ind_estado = '1'";
    
    if( $cod_produc != NULL )
	  $mSelect .= " AND cod_produc = '".$cod_produc."'";

    $mSelect .= "GROUP BY 1 
                 ORDER BY 2";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
  }

  function getTipDes( $cod_tipdes = '' )
  {
    $mSelect = "SELECT cod_tipdes, UPPER( nom_tipdes ) AS nom_tipdes 
                  FROM ".BASE_DATOS.".tab_genera_tipdes 
                 WHERE ind_estado = 1 ";
    
    if( $cod_tipdes != '' )
      $mSelect .= " AND cod_tipdes = '".$cod_tipdes."' ";
    
    $mSelect .= " ORDER BY 2";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
  }
  
  function getInform( $mData )
  {
    $this -> Style();
    
    $_TIPDES = $this -> getTipDes( $mData['cod_tipdes'] );
    
    $mSelect = " SELECT a.num_despac, a.cod_tipdes, 
                        a.fec_llegad, DATE( a.fec_despac ) AS fec_salida,
                        c.des_mercan
                   FROM ".BASE_DATOS.".tab_despac_despac a
                  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
                    ON a.num_despac = b.num_despac
                  INNER JOIN ".BASE_DATOS.".tab_despac_remesa c
              		  ON a.num_despac = c.num_despac";
    $mSelect .= " WHERE a.ind_anulad != 'A'
                    AND b.ind_activo = 'S'
                    AND a.fec_despac BETWEEN '".$mData['fec_inicia']." 00:00:00' AND '".$mData['fec_finali']." 23:59:59' ";
    
    
                    if( $mData['cod_transp'] != '' ){
                $mSelect .= " AND b.cod_transp = '".$mData['cod_transp']."'";
    }

    if( $mData['cod_tipdes'] != '' )
    {
      $mSelect .= " AND a.cod_tipdes = '".$mData['cod_tipdes']."'";
    }
    
    if( $mData['cod_produc'] != '' )
    {
      $mSelect .= " AND c.cod_mercan = '".$mData['cod_produc']."'";
    }
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $arr_despac = $consulta -> ret_matriz();

    $_DESPAC = array();
    $_ESPECI = array();

    foreach( $arr_despac as $row)
    {
      // CONTEO GENERAL --------------------------------------------
      $_DESPAC['g']['g']['totalx']++;
      $_DESPAC['g']['g']['despac'] .= $_DESPAC['g']['g']['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

      // CONTEO GENERAL POR TIPO DE DESPACHO ----------------------
      $_DESPAC['g'][ $row['cod_tipdes'] ]['totalx']++;
      $_DESPAC['g'][ $row['cod_tipdes'] ]['despac'] .= $_DESPAC['g'][ $row['cod_tipdes'] ]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
      
      // CONTEO ESPECIFICO POR FECHA -------------------------------
      $_ESPECI['g'][ $row['fec_salida'] ]['g']['totalx']++;
      $_ESPECI['g'][ $row['fec_salida'] ]['g']['despac'] .= $_ESPECI['g'][ $row['fec_salida'] ]['g']['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

      // CONTEO ESPECIFICO POR FECHA Y TIPO DE DESPACHO ----------- 
      $_ESPECI['g'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['totalx']++;
      $_ESPECI['g'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['despac'] .= $_ESPECI['g'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
      
      //VERIFICA LA CANTIDAD DE NOVEDADES PENDIENTES POR DESPACHO
      $mCantPendDespac = $this -> verifySolucion( $row['num_despac'] ) ;
      
      if($row['fec_llegad'] != '' )
      {
        // CONTEO PARA DESPACHOS FINALIZADOS ----------------------
        $_DESPAC['f']['g']['totalx']++;
        $_DESPAC['f']['g']['despac'] .= $_DESPAC['f']['g']['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

        // CONTEO PARA FINALIZADOS POR TIPO DE DESPACHO -----------
        $_DESPAC['f'][ $row['cod_tipdes'] ]['totalx']++;
        $_DESPAC['f'][ $row['cod_tipdes'] ]['despac'] .= $_DESPAC['f'][ $row['cod_tipdes'] ]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

        // CONTEO ESPECIFICO PARA FINALIZADOS POR FECHA -----------
        $_ESPECI['f'][ $row['fec_salida'] ]['g']['totalx']++;
        $_ESPECI['f'][ $row['fec_salida'] ]['g']['despac'] .= $_ESPECI['f'][ $row['fec_salida'] ]['g']['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

        // CONTEO ESPECIFICO POR FECHA Y TIPO DE DESPACHO --------- 
        $_ESPECI['f'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['totalx']++;
        $_ESPECI['f'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['despac'] .= $_ESPECI['f'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
      }
      elseif( $this -> verifyLlegada( $row['num_despac'] ) ) 
      {
        // CONTEO PARA DESPACHOS POR LLEGADA ----------------------
        $_DESPAC['l']['g']['totalx']++;
        $_DESPAC['l']['g']['despac'] .= $_DESPAC['l']['g']['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

        // CONTEO PARA POR LLEGADA POR TIPO DE DESPACHO -----------
        $_DESPAC['l'][ $row['cod_tipdes'] ]['totalx']++;
        $_DESPAC['l'][ $row['cod_tipdes'] ]['despac'] .= $_DESPAC['l'][ $row['cod_tipdes'] ]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

        // CONTEO ESPECIFICO PARA POR LLEGADA POR FECHA -----------
        $_ESPECI['l'][ $row['fec_salida'] ]['g']['totalx']++;
        $_ESPECI['l'][ $row['fec_salida'] ]['g']['despac'] .= $_ESPECI['l'][ $row['fec_salida'] ]['g']['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

        // CONTEO ESPECIFICO POR FECHA Y TIPO DE DESPACHO --------- 
        $_ESPECI['l'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['totalx']++;
        $_ESPECI['l'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['despac'] .= $_ESPECI['l'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
      } 
      else
      {
        // CONTEO PARA DESPACHOS EN TRANSITO ----------------------
        $_DESPAC['t']['g']['totalx']++;
        $_DESPAC['t']['g']['despac'] .= $_DESPAC['t']['g']['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

        // CONTEO PARA EN TRANSITO POR TIPO DE DESPACHO -----------
        $_DESPAC['t'][ $row['cod_tipdes'] ]['totalx']++;
        $_DESPAC['t'][ $row['cod_tipdes'] ]['despac'] .= $_DESPAC['t'][ $row['cod_tipdes'] ]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

        // CONTEO ESPECIFICO PARA EN TRANSITO POR FECHA -----------
        $_ESPECI['t'][ $row['fec_salida'] ]['g']['totalx']++;
        $_ESPECI['t'][ $row['fec_salida'] ]['g']['despac'] .= $_ESPECI['t'][ $row['fec_salida'] ]['g']['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

        // CONTEO ESPECIFICO POR FECHA Y TIPO DE DESPACHO --------- 
        $_ESPECI['t'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['totalx']++;
        $_ESPECI['t'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['despac'] .= $_ESPECI['t'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
      }

      if( $mCantPendDespac != 0 ) 
      {
        for ($i=0; $i < $mCantPendDespac; $i++) { 
          
          // CONTEO PARA DESPACHOS POR SOLUCION ---------------------
          $_DESPAC['s']['g']['totalx']++;
          $_DESPAC['s']['g']['despac'] .= $_DESPAC['s']['g']['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

          // CONTEO PARA POR SOLUCION POR TIPO DE DESPACHO ----------
          $_DESPAC['s'][ $row['cod_tipdes'] ]['totalx']++;
          $_DESPAC['s'][ $row['cod_tipdes'] ]['despac'] .= $_DESPAC['s'][ $row['cod_tipdes'] ]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

          // CONTEO ESPECIFICO PARA POR SOLUCION POR FECHA ----------
          $_ESPECI['s'][ $row['fec_salida'] ]['g']['totalx']++;
          $_ESPECI['s'][ $row['fec_salida'] ]['g']['despac'] .= $_ESPECI['s'][ $row['fec_salida'] ]['g']['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];

          // CONTEO ESPECIFICO POR FECHA Y TIPO DE DESPACHO --------- 
          $_ESPECI['s'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['totalx']++;
          $_ESPECI['s'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['despac'] .= $_ESPECI['s'][ $row['fec_salida'] ][ $row['cod_tipdes'] ]['despac'] != '' ? ','.$row['num_despac'] : $row['num_despac'];
        }
      }
    }

    $_SESSION['DESPAC'] = $_DESPAC;
    $_SESSION['ESPECI'] = $_ESPECI;

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
        
          $mHtml .= '<tr>';
          	$mTitle = 'SINTESIS GENERAL DE LA OPERACION COMPRENDIDO ENTRE '.$mData['fec_inicia'].' Y '.$mData['fec_finali']; 
            if( $mData['cod_produc'] != '' )
            {
        	  $_PRODUC = $this -> getProduc( $mData['cod_produc'] );
        	  $mTitle .= ', PARA: '.strtoupper( $_PRODUC[0]['nom_produc'] );
            }
            
            $mHtml .= '<td class="CellHead" colspan="9">'.$mTitle.'</td>';
          $mHtml .= '</tr>';
        
          $mHtml .= '<tr>';
            $mHtml .= '<td class="CellHead" >GENERADOS</td>';
            $mHtml .= '<td class="CellHead" >FINALIZADOS</td>';
            $mHtml .= '<td class="CellHead" >%</td>';
            $mHtml .= '<td class="CellHead" ><small>EN TRANSITO</small></td>';
            $mHtml .= '<td class="CellHead" >%</td>';
            $mHtml .= '<td class="CellHead" ><small>PENDIENTES LLEGADA</small></td>';
            $mHtml .= '<td class="CellHead" >%</td>';
            $mHtml .= '<td class="CellHead" ><small>NOVEDADES PENDIENTES SOLUCI&Oacute;N</small></td>';
            $mHtml .= '<td class="CellHead" >%</td>';
          $mHtml .= '</tr>';

          $mHtml .= '<tr>';
            
            // LINKS -------------------------------------
            $mLinkG = (int)$_DESPAC['g']['g']['totalx'] > 0 ? '<a onclick="getData(\'g\', \'g\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_DESPAC['g']['g']['totalx'].'</a>' : '0';
            $mLinkF = (int)$_DESPAC['f']['g']['totalx'] > 0 ? '<a onclick="getData(\'f\', \'g\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_DESPAC['f']['g']['totalx'].'</a>' : '0';
            $mLinkT = (int)$_DESPAC['t']['g']['totalx'] > 0 ? '<a onclick="getData(\'t\', \'g\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_DESPAC['t']['g']['totalx'].'</a>' : '0';
            $mLinkL = (int)$_DESPAC['l']['g']['totalx'] > 0 ? '<a onclick="getData(\'l\', \'g\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_DESPAC['l']['g']['totalx'].'</a>' : '0';
            $mLinkS = (int)$_DESPAC['s']['g']['totalx'] > 0 ? '<a onclick="getData(\'s\', \'g\', \'1\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_DESPAC['s']['g']['totalx'].'</a>' : '0';
            //--------------------------------------------

            // PORCENTAJES -------------------------------
            $mTotal = (int)$_DESPAC['g']['g']['totalx'];
            $mPercentF = ( (int)$_DESPAC['f']['g']['totalx'] * 100 )/ $mTotal; 
            $mPercentT = ( (int)$_DESPAC['t']['g']['totalx'] * 100 )/ $mTotal; 
            $mPercentL = ( (int)$_DESPAC['l']['g']['totalx'] * 100 )/ $mTotal; 
            $mPercentS = ( (int)$_DESPAC['s']['g']['totalx'] * 100 )/ $mTotal; 
            // -------------------------------------------

            $mHtml .= '<td class="cellInfo onlyCell" align="center">'.$mLinkG.'</td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center">'.$mLinkF.'</td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center">'.round( $mPercentF, 2 ).' %</td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center">'.$mLinkT.'</td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center">'.round( $mPercentT, 2 ).' %</td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center">'.$mLinkL.'</td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center">'.round( $mPercentL, 2 ).' %</td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center">'.$mLinkS.'</td>';
            $mHtml .= '<td class="cellInfo onlyCell" align="center">'.round( $mPercentS, 2 ).' %</td>';
          $mHtml .= '</tr>';

        $mHtml .= '</table><br><br>';
        
        $mHtml .= '<table width="98%" cellpadding="0" cellspacing="1">';

          $mHtml .= '<tr>';
            $mHtml .= '<td class="CellHead" colspan="10">DETALLADO POR DIAS</td>';
          $mHtml .= '</tr>';

          $mHtml .= '<tr>';
            $mHtml .= '<td class="CellHead" colspan="6">OPERACION</td>';
            $mHtml .= '<td class="CellHead" colspan="2">RESPONSABILIDAD TRA</td>';
            $mHtml .= '<td class="CellHead" colspan="2">RESPONSABILIDAD GENERADORES</td>';
          $mHtml .= '</tr>';
        
          $mHtml .= '<tr>';
            $mHtml .= '<td class="CellHead">FECHA</td>';
            $mHtml .= '<td class="CellHead">GENERADOS</td>';
            $mHtml .= '<td class="CellHead">FINALIZADOS</td>';
            $mHtml .= '<td class="CellHead">%</td>';
            $mHtml .= '<td class="CellHead"><small>EN TRANSITO</small></td>';
            $mHtml .= '<td class="CellHead">%</td>';
            $mHtml .= '<td class="CellHead"><small>PENDIENTES LLEGADA</small></td>';
            $mHtml .= '<td class="CellHead">%</td>';
            $mHtml .= '<td class="CellHead"><small>NOVEDADES PENDIENTES SOLUCI&Oacute;N</small></td>';
            $mHtml .= '<td class="CellHead">%</td>';
          $mHtml .= '</tr>';

          $mFecini = $mData['fec_inicia'];
          $mFecfin = $mData['fec_finali'];
          while( strtotime( $mFecini ) <= strtotime( $mFecfin ) ) 
          {
            // LINKS -------------------------------------
            $mLinkG = (int)$_ESPECI['g'][ $mFecini ]['g']['totalx'] > 0 ? '<a onclick="getDataDetail(\'g\', \'g\', \''.$mFecini.'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_ESPECI['g'][ $mFecini ]['g']['totalx'].'</a>' : '0';
            $mLinkF = (int)$_ESPECI['f'][ $mFecini ]['g']['totalx'] > 0 ? '<a onclick="getDataDetail(\'f\', \'g\', \''.$mFecini.'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_ESPECI['f'][ $mFecini ]['g']['totalx'].'</a>' : '0';
            $mLinkT = (int)$_ESPECI['t'][ $mFecini ]['g']['totalx'] > 0 ? '<a onclick="getDataDetail(\'t\', \'g\', \''.$mFecini.'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_ESPECI['t'][ $mFecini ]['g']['totalx'].'</a>' : '0';
            $mLinkL = (int)$_ESPECI['l'][ $mFecini ]['g']['totalx'] > 0 ? '<a onclick="getDataDetail(\'l\', \'g\', \''.$mFecini.'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_ESPECI['l'][ $mFecini ]['g']['totalx'].'</a>' : '0';
            $mLinkS = (int)$_ESPECI['s'][ $mFecini ]['g']['totalx'] > 0 ? '<a onclick="getDataDetail(\'s\', \'g\', \''.$mFecini.'\', \'1\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_ESPECI['s'][ $mFecini ]['g']['totalx'].'</a>' : '0';
            //--------------------------------------------

            // PORCENTAJES -------------------------------
            $mTotal = (int)$_ESPECI['g'][ $mFecini ]['g']['totalx'];
            $mPercentF = ( (int)$_ESPECI['f'][ $mFecini ]['g']['totalx'] * 100 )/ $mTotal; 
            $mPercentT = ( (int)$_ESPECI['t'][ $mFecini ]['g']['totalx'] * 100 )/ $mTotal; 
            $mPercentL = ( (int)$_ESPECI['l'][ $mFecini ]['g']['totalx'] * 100 )/ $mTotal; 
            $mPercentS = ( (int)$_ESPECI['s'][ $mFecini ]['g']['totalx'] * 100 )/ $mTotal; 
            // -------------------------------------------

            $mHtml .= '<tr class="row">';
            $mHtml .= '<td class="cellInfo"><b>'.$this -> readDate( $mFecini ).'</b></td>';
            $mHtml .= '<td class="cellInfo" align="center">'.$mLinkG.'</td>';
            $mHtml .= '<td class="cellInfo" align="center">'.$mLinkF.'</td>';
            $mHtml .= '<td class="cellInfo" align="center">'.round( $mPercentF, 2 ).' %</td>';
            $mHtml .= '<td class="cellInfo" align="center">'.$mLinkT.'</td>';
            $mHtml .= '<td class="cellInfo" align="center">'.round( $mPercentT, 2 ).' %</td>';
            $mHtml .= '<td class="cellInfo" align="center">'.$mLinkL.'</td>';
            $mHtml .= '<td class="cellInfo" align="center">'.round( $mPercentL, 2 ).' %</td>';
            $mHtml .= '<td class="cellInfo" align="center">'.$mLinkS.'</td>';
            $mHtml .= '<td class="cellInfo" align="center">'.round( $mPercentS, 2 ).' %</td>';
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

            $mHtml .= '<tr>';
          	  $mTitle = 'SINTESIS GENERAL DE LA OPERACION COMPRENDIDO ENTRE '.$mData['fec_inicia'].' Y '.$mData['fec_finali'].', PARA DESPACHOS TIPO: '.$row['nom_tipdes']; 
              if( $mData['cod_produc'] != '' )
              {
        	    $_PRODUC = $this -> getProduc( $mData['cod_produc'] );
        	    $mTitle .= ', Y MERCANCIA: '.strtoupper( $_PRODUC[0]['nom_produc'] );
              }
            
              $mHtml .= '<td class="CellHead" colspan="9">'.$mTitle.'</td>';
            $mHtml .= '</tr>';
        
            $mHtml .= '<tr>';
              $mHtml .= '<td class="CellHead" >GENERADOS</td>';
              $mHtml .= '<td class="CellHead" >FINALIZADOS</td>';
              $mHtml .= '<td class="CellHead" >%</td>';
              $mHtml .= '<td class="CellHead" ><small>EN TRANSITO</small></td>';
              $mHtml .= '<td class="CellHead" >%</td>';
              $mHtml .= '<td class="CellHead" ><small>PENDIENTES LLEGADA</small></td>';
              $mHtml .= '<td class="CellHead" >%</td>';
              $mHtml .= '<td class="CellHead" ><small>PENDIENTES SOLUCI&Oacute;N</small></td>';
              $mHtml .= '<td class="CellHead" >%</td>';
            $mHtml .= '</tr>';

            $mHtml .= '<tr>';
            
              // LINKS -------------------------------------
              $mLinkG = (int)$_DESPAC['g'][ $row['cod_tipdes'] ]['totalx'] > 0 ? '<a onclick="getData(\'g\', \''. $row['cod_tipdes'] .'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_DESPAC['g'][ $row['cod_tipdes'] ]['totalx'].'</a>' : '0';
              $mLinkF = (int)$_DESPAC['f'][ $row['cod_tipdes'] ]['totalx'] > 0 ? '<a onclick="getData(\'f\', \''. $row['cod_tipdes'] .'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_DESPAC['f'][ $row['cod_tipdes'] ]['totalx'].'</a>' : '0';
              $mLinkT = (int)$_DESPAC['t'][ $row['cod_tipdes'] ]['totalx'] > 0 ? '<a onclick="getData(\'t\', \''. $row['cod_tipdes'] .'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_DESPAC['t'][ $row['cod_tipdes'] ]['totalx'].'</a>' : '0';
              $mLinkL = (int)$_DESPAC['l'][ $row['cod_tipdes'] ]['totalx'] > 0 ? '<a onclick="getData(\'l\', \''. $row['cod_tipdes'] .'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_DESPAC['l'][ $row['cod_tipdes'] ]['totalx'].'</a>' : '0';
              $mLinkS = (int)$_DESPAC['s'][ $row['cod_tipdes'] ]['totalx'] > 0 ? '<a onclick="getData(\'s\', \''. $row['cod_tipdes'] .'\', \'1\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_DESPAC['s'][ $row['cod_tipdes'] ]['totalx'].'</a>' : '0';
              //--------------------------------------------

              // PORCENTAJES -------------------------------
              $mTotal = (int)$_DESPAC['g'][ $row['cod_tipdes'] ]['totalx'];
              $mPercentF = ( (int)$_DESPAC['f'][ $row['cod_tipdes'] ]['totalx'] * 100 )/ $mTotal; 
              $mPercentT = ( (int)$_DESPAC['t'][ $row['cod_tipdes'] ]['totalx'] * 100 )/ $mTotal; 
              $mPercentL = ( (int)$_DESPAC['l'][ $row['cod_tipdes'] ]['totalx'] * 100 )/ $mTotal; 
              $mPercentS = ( (int)$_DESPAC['s'][ $row['cod_tipdes'] ]['totalx'] * 100 )/ $mTotal; 
              // -------------------------------------------

              $mHtml .= '<td class="cellInfo onlyCell" align="center">'.$mLinkG.'</td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center">'.$mLinkF.'</td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center">'.round( $mPercentF, 2 ).' %</td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center">'.$mLinkT.'</td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center">'.round( $mPercentT, 2 ).' %</td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center">'.$mLinkL.'</td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center">'.round( $mPercentL, 2 ).' %</td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center">'.$mLinkS.'</td>';
              $mHtml .= '<td class="cellInfo onlyCell" align="center">'.round( $mPercentS, 2 ).' %</td>';
            $mHtml .= '</tr>';

          $mHtml .= '</table><br><br>';
        
          $mHtml .= '<table width="98%" cellpadding="0" cellspacing="1">';

            $mHtml .= '<tr>';
              $mHtml .= '<td class="CellHead" colspan="10">DETALLADO POR DIAS</td>';
            $mHtml .= '</tr>';

            $mHtml .= '<tr>';
              $mHtml .= '<td class="CellHead" colspan="6">OPERACION CORONA</td>';
              $mHtml .= '<td class="CellHead" colspan="2">RESPONSABILIDAD OET</td>';
              $mHtml .= '<td class="CellHead" colspan="2">RESPONSABILIDAD L&T</td>';
            $mHtml .= '</tr>';
        
            $mHtml .= '<tr>';
              $mHtml .= '<td class="CellHead">FECHA</td>';
              $mHtml .= '<td class="CellHead">GENERADOS</td>';
              $mHtml .= '<td class="CellHead">FINALIZADOS</td>';
              $mHtml .= '<td class="CellHead">%</td>';
              $mHtml .= '<td class="CellHead"><small>EN TRANSITO</small></td>';
              $mHtml .= '<td class="CellHead">%</td>';
              $mHtml .= '<td class="CellHead"><small>PENDIENTES LLEGADA</small></td>';
              $mHtml .= '<td class="CellHead">%</td>';
              $mHtml .= '<td class="CellHead"><small>PENDIENTES SOLUCI&Oacute;N</small></td>';
              $mHtml .= '<td class="CellHead">%</td>';
            $mHtml .= '</tr>';
            
            $mFecini = $mData['fec_inicia'];
            $mFecfin = $mData['fec_finali'];
            while( strtotime( $mFecini ) <= strtotime( $mFecfin ) ) 
            {
              // LINKS -------------------------------------
              $mLinkG = (int)$_ESPECI['g'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'] > 0 ? '<a onclick="getDataDetail(\'g\', \''. $row['cod_tipdes'] .'\', \''.$mFecini.'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_ESPECI['g'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'].'</a>' : '0';
              $mLinkF = (int)$_ESPECI['f'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'] > 0 ? '<a onclick="getDataDetail(\'f\', \''. $row['cod_tipdes'] .'\', \''.$mFecini.'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_ESPECI['f'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'].'</a>' : '0';
              $mLinkT = (int)$_ESPECI['t'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'] > 0 ? '<a onclick="getDataDetail(\'t\', \''. $row['cod_tipdes'] .'\', \''.$mFecini.'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_ESPECI['t'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'].'</a>' : '0';
              $mLinkL = (int)$_ESPECI['l'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'] > 0 ? '<a onclick="getDataDetail(\'l\', \''. $row['cod_tipdes'] .'\', \''.$mFecini.'\', \'0\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_ESPECI['l'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'].'</a>' : '0';
              $mLinkS = (int)$_ESPECI['s'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'] > 0 ? '<a onclick="getDataDetail(\'s\', \''. $row['cod_tipdes'] .'\', \''.$mFecini.'\', \'1\')" style="cursor:pointer; color:#000000; text-decoration:none;">'.$_ESPECI['s'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'].'</a>' : '0';
              //--------------------------------------------

              // PORCENTAJES -------------------------------
              $mTotal = (int)$_ESPECI['g'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'];
              $mPercentF = ( (int)$_ESPECI['f'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'] * 100 )/ $mTotal; 
              $mPercentT = ( (int)$_ESPECI['t'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'] * 100 )/ $mTotal; 
              $mPercentL = ( (int)$_ESPECI['l'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'] * 100 )/ $mTotal; 
              $mPercentS = ( (int)$_ESPECI['s'][ $mFecini ][ $row['cod_tipdes'] ]['totalx'] * 100 )/ $mTotal; 
              // -------------------------------------------

              $mHtml .= '<tr class="row">';
              $mHtml .= '<td class="cellInfo"><b>'.$this -> readDate( $mFecini ).'</b></td>';
              $mHtml .= '<td class="cellInfo" align="center">'.$mLinkG.'</td>';
              $mHtml .= '<td class="cellInfo" align="center">'.$mLinkF.'</td>';
              $mHtml .= '<td class="cellInfo" align="center">'.round( $mPercentF, 2 ).' %</td>';
              $mHtml .= '<td class="cellInfo" align="center">'.$mLinkT.'</td>';
              $mHtml .= '<td class="cellInfo" align="center">'.round( $mPercentT, 2 ).' %</td>';
              $mHtml .= '<td class="cellInfo" align="center">'.$mLinkL.'</td>';
              $mHtml .= '<td class="cellInfo" align="center">'.round( $mPercentL, 2 ).' %</td>';
              $mHtml .= '<td class="cellInfo" align="center">'.$mLinkS.'</td>';
              $mHtml .= '<td class="cellInfo" align="center">'.round( $mPercentS, 2 ).' %</td>';
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

  function getData( $mData )
  {
    $_DESPAC = $_SESSION['DESPAC'];
    
    include_once('classData.php');

    $cData = new generaData( $this -> conexion );
    $mDespachos = $cData -> getDataGeneralDespac( $_DESPAC[ $mData['pri_nivelx'] ][ $mData['seg_nivelx'] ]['despac'], $mData['ind_soluci'], $mData['pri_nivelx']  ); 

    $mHtml .= $mDespachos;

    $_SESSION['LIST_TOTAL'] = $mHtml;

    echo '<span id="excelID" onclick="Export();" style="color: #FFF; cursor:pointer; font-family: Trebuchet MS,Verdana,Arial; font-size: 13px;">[Excel]</span><br><br>';
    echo $mHtml;
  }

  function getDataDetail( $mData )
  {
    $_ESPECI = $_SESSION['ESPECI'];
    
    include_once('classData.php');

    $cData = new generaData( $this -> conexion );
    $mDespachos = $cData -> getDataGeneralDespac( $_ESPECI[ $mData['pri_nivelx'] ][ $mData['fec_cortex'] ][ $mData['seg_nivelx'] ]['despac'], $mData['ind_soluci'], $mData['pri_nivelx'] ); 

    $mHtml .= $mDespachos;

    $_SESSION['LIST_TOTAL'] = $mHtml;

    echo '<span id="excelID" onclick="Export();" style="color: #FFF; cursor:pointer; font-family: Trebuchet MS,Verdana,Arial; font-size: 13px;">[Excel]</span><br><br>';
    echo $mHtml;
  }

  function verifyLlegada( $num_despac ) 
  {
    $mSelect = "SELECT a.cod_contro 
                  FROM ".BASE_DATOS.".vis_despac_seguim a 
                 WHERE a.num_despac = '".$num_despac."' 
                 ORDER BY a.fec_planea DESC 
                 LIMIT 1 ";
    
    $consul = new Consulta( $mSelect, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );
    
    $cod_contro = $matriz[0]['cod_contro'];
    
    $mSelect  = "SELECT 1  
                   FROM ".BASE_DATOS.".vis_despac_noveda a 
                  WHERE a.num_despac = '".$num_despac."' 
                    AND a.cod_contro IN ( '".$cod_contro."' ) ";
    
    $consul = new Consulta( $mSelect, $this -> conexion );
    $matriz = $consul -> ret_matrix( 'a' );

    return sizeof( $matriz ) > 0 ? TRUE : FALSE;
  }

  function verifySolucion( $num_despac )
  {
    $mSelect = "SELECT a.num_despac 
                  FROM ".BASE_DATOS.".tab_protoc_asigna a
            INNER JOIN ".BASE_DATOS.".tab_despac_despac b
                    ON a.num_despac = b.num_despac
                 WHERE a.num_despac = '".$num_despac."' 
                   AND a.ind_ejecuc = '0'
                   AND b.fec_llegad IS NULL";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $mSoluci = $consulta -> ret_matriz();

    return sizeof( $mSoluci ) ;
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

$proceso = new AjaxSintesisOperacion();
 ?>