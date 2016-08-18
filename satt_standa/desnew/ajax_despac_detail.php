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
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    
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
   
    return $CAMPO;
  }
  
  
  
}

$proceso = new Ajax();
 ?>