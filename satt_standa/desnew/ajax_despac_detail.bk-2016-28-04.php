<?php

class Ajax
{
  var $conexion;
  var $ind_estado = array();
  var $ind_estado_ = array();
  
  public function __construct()
  { 
    $_AJAX = $_REQUEST;
    include('../lib/ajax.inc');
    include_once( "../lib/general/dinamic_list.inc" );
    include_once('../lib/general/constantes.inc');

    $this -> conexion = $AjaxConnection;
    $this -> Details( $_AJAX );
  }
  
  private function Details( $_AJAX )
  {
    $this -> Style();
    
    $mSelect = "SELECT nom_campo, fec_creaci, num_consec
                  FROM ".BASE_DATOS.".tab_bitaco_corona 
                 WHERE num_dessat = '".$_AJAX['num_despac']."' 
                   AND ind_modifi = '2'
                 GROUP BY 1
                 ORDER BY fec_creaci";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$_BITACO = $consulta -> ret_matriz();
    
    if( sizeof( $_BITACO ) > 0 )
    {
      $mHtml = "<table width='100%' cellspacing='1' cellpadding='0'>";
      foreach( $_BITACO as $row )
      {
        $mHtml .= '<tr>';
        $mHtml .= '<td class="cellInfo1" colspan="2" align="center">Fecha: '.$row['fec_creaci'].'</td>';
        $mHtml .= '</tr>';
        $mHtml .= '<tr>';
        $mHtml .= '<td class="cellInfo1" align="center">Campo</td>';
        $mHtml .= '<td class="cellInfo1" align="center">Valor Anterior</td>';
        $mHtml .= '</tr>';
        
        foreach( explode(',', $row['nom_campo'] ) as $_campo )
        {
          $_RES = $this -> getCampo( $_campo, $_AJAX['num_despac'],$row['num_consec'] );
          $mHtml .= '<tr>';
          $mHtml .= '<td class="cellInfo" align="center">'. $_RES[0] .'</td>';
          $mHtml .= '<td class="cellInfo" align="center">'. $_RES[1] .'</td>';
          $mHtml .= '</tr>';
        }
      }
      $mHtml .= "</table>";
      echo $mHtml;
      
    }

  }
  
  private function getCampo( $nom_campo, $num_despac, $num_consec )
  {
    
    $mSelect = "SELECT ".$nom_campo." FROM ".BASE_DATOS.".tab_bitaco_corona WHERE num_dessat = '".$num_despac."' AND num_consec = '".$num_consec."'";
    $consulta = new Consulta( $mSelect, $this -> conexion );
		$ANTERIOR = $consulta -> ret_matriz();
    
    if( $nom_campo == 'cod_manifi' )
    {
      $CAMPO[0] = 'No. Manifiesto';
      $CAMPO[1] = $ANTERIOR[0][0];
    }
    if( $nom_campo == 'fec_despac' )
    {
      $CAMPO[0] = 'Fecha Despacho';
      $CAMPO[1] = $ANTERIOR[0][0];
    }
    if( $nom_campo == 'cod_paiori' )
    {
      $CAMPO[0] = 'Pa&iacute;s Origen';
      $CAMPO[1] = $ANTERIOR[0][0];
    }
    if( $nom_campo == 'cod_depori' )
    {
      $CAMPO[0] = 'Departamento Origen';
      $CAMPO[1] = $ANTERIOR[0][0];
    }
    if( $nom_campo == 'cod_ciuori' )
    {
      $CAMPO[0] = 'Ciudad Origen';
      $CAMPO[1] = $ANTERIOR[0][0];
    }
    if( $nom_campo == 'cod_paides' )
    {
      $CAMPO[0] = 'Pa&iacute;s Destino';
      $CAMPO[1] = $ANTERIOR[0][0];
    }
    if( $nom_campo == 'cod_depdes' )
    {
      $CAMPO[0] = 'Departamento Destino';
      $CAMPO[1] = $ANTERIOR[0][0];
    }
    if( $nom_campo == 'cod_ciudes' )
    {
      $CAMPO[0] = 'Ciudad Destino';
      $CAMPO[1] = $ANTERIOR[0][0];
    }
    if( $nom_campo == 'cod_operad' )
    {
      $CAMPO[0] = 'Operador';
      $CAMPO[1] = $ANTERIOR[0][0];
    }
    if( $nom_campo == 'fec_citcar' )
    {
      $CAMPO[0] = 'Fecha Cita de Cargue';
      $CAMPO[1] = $ANTERIOR[0][0];
    }
    if( $nom_campo == 'hor_citcar' )
    {
      $CAMPO[0] = 'Hora Cita de Cargue';
      $CAMPO[1] = $ANTERIOR[0][0];
    }
    // if( $nom_campo == 'nom_sitcar' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'val_flecon' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'val_despac' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'val_antici' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'val_retefu' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'nom_carpag' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'nom_despag' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_agedes' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'val_pesoxx' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'obs_despac' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'fec_llegad' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'obs_llegad' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'ind_planru' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_rutasx' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'ind_anulad' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'num_poliza' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'con_telef1' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'con_telmov' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'con_domici' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'gps_operad' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'gps_usuari' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'gps_paswor' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'gps_idxxxx' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'ema_client' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_asegur' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'num_consol' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'num_solici' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_conduc' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'num_placax' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'num_trayle' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'tip_vehicu' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'num_pedido' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'ano_modelo' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_marcax' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_lineax' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_colorx' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'nom_conduc' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'ciu_conduc' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_config' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_carroc' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'num_chasis' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'num_motorx' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'num_soatxx' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'dat_vigsoa' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'nom_ciasoa' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'num_tarpro' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cat_licenc' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_poseed' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'nom_poseed' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'ciu_poseed' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'dir_poseed' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_estado' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'nom_estado' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'tip_transp' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_instal' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    // if( $nom_campo == 'cod_mercan' )
    // {
      // $CAMPO[0] = '';
      // $CAMPO[1] = '';
      // $CAMPO[2] = '';
    // }
    return $CAMPO;
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
  
}

$proceso = new Ajax();
 ?>