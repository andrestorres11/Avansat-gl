<?php
//error_reporting("E_ERROR");

include ("../lib/general/constantes.inc");
include ("../lib/general/conexion_lib.inc");
include ("../lib/general/form_lib.inc");
include ("../lib/general/paginador_lib.inc");
include("../../".$_REQUEST['url']."/constantes.inc");
include ("../despac/Despachos.inc");


class Exp_Nov_Emp
{
 var $conexion;
 	 

 function Exp_Nov_Emp()
 {
		ini_set('memory_limit','128M');
    session_start();
    $fechoract = date("Y-m-d h:i a");
    $archivo = "Novedades Transpartadoras ".$fechoract;
    header('Content-Type: application/octetstream');
		header('Expires: 0');
		header('Content-Disposition: attachment; filename="'.$archivo.'.xls"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
    
	
    $matrix = $_SESSION['LIST_ARRAY'];
    for ( $k = 0; $k < sizeof( $matrix ); $k++ )
    {
      unset($matrix[$k][0]);
      unset($matrix[$k][1]);
      unset($matrix[$k][2]);
      unset($matrix[$k][3]);
      unset($matrix[$k][4]);
    } 
    //Se genera el html que se va a exportar a excel
    $html  = NULL;
    $html .= '<table align="center" cellspacing="0" cellpadding="0">';
    $keys = array_keys( $matrix[0] );
    $html .= '<tr>';
    //Se imprimen las cabeceras del arreglo
    for ( $k = 0; $k < sizeof( $keys ); $k++ )
    {
      $html .= '<th align="center">';
      $html .= $keys[$k];
      $html .= '</th>';
    } 
    $html .= '</tr>';
    for ( $r = 0; $r < sizeof( $matrix ); $r++ )
    {
      $html .= '<tr>';
      for ( $c = 0; $c < sizeof( $keys ); $c++ )
      {
        $html .= '<td align="left">';
        $html .= $matrix[$r][$keys[$c]];
        $html .= '</td>';
      }
      $html .= '</tr>';
    }
    $html .= '</table>';
    echo $html;
 }

 }
$proceso = new Exp_Nov_Emp();
?>