<?php

class AjaxFacturFaro
{
  var $conexion;
  
  public function __construct()
  {
    $_AJAX = $_REQUEST;
    include('../lib/ajax.inc');
    include_once( "../lib/general/dinamic_list.inc" );

    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }
  
  private function SetAgencia( $_AJAX )
  {
    $mSelect = "SELECT a.cod_agenci, b.nom_agenci
                  FROM ".BASE_DATOS.".tab_transp_agenci a, 
                       ".BASE_DATOS.".tab_genera_agenci b
                 WHERE a.cod_agenci = b.cod_agenci
                   AND a.cod_transp = '".$_AJAX['cod_empres']."'
                 ORDER BY 2";
                 
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $agencias = $consulta -> ret_matriz();
  
    $result = '<option value="">- Seleccione -</option>';
    foreach( $agencias as $row )
    {
      $result .= '<option value="'.$row['cod_agenci'].'">'.$row['nom_agenci'].'</option>';
    }
    echo $result;
  }
}

$proceso = new AjaxFacturFaro();
 ?>