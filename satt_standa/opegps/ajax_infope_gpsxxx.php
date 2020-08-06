<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_infope_gpsxxx
{
  var $AjaxConnection;
  
  public function __construct()
  {
    include('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST['option']) {
      case 'setRegistros':
        $this->setRegistros();
        break;
    }

  }

  //---------------------------------------------
  /*! \fn: setRegistros
  *  \brief:Retorna los registros para el dataTable
  *  \author: Ing. Cristian Andrés Torres
  *  \date: 02/06/2020
  *  \date modified: 
  *  \return HTML
  */
  private function setRegistros() {
    $mSql = " SELECT  
                a.nit_operad,
                a.nom_operad,
                IF(a.ind_estado=1,'ACTIVO','INACTIVO') as 'ind_estado',
                IF(a.ind_intgps=1,'SI','NO') as 'ind_intgps'
                FROM  ".BD_STANDA.".tab_genera_opegps a ORDER BY a.nom_operad ASC";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz =  self::cleanArray($mMatriz->ret_matrix("a"));
    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {
    			$data[$key][] = $valor;	
    	}
    }

    $return = array("data" => $data);
    echo json_encode($return);
  }


  function cleanArray($array){

    $arrayReturn = array();

    //Convert function
    $convert = function($value){
        if(is_string($value)){
            return utf8_encode($value);
        }
        return $value;
    };

    //Go through data
    foreach ($array as $key => $value) {
        //Validate sub array
        if(is_array($value)){
            //Clean sub array
            $arrayReturn[$convert($key)] = self::cleanArray($value);
        }else{
            //Clean value
            $arrayReturn[$convert($key)] = $convert($value);
        }
    }
    //Return array
    return $arrayReturn;
}


  
}

$proceso = new ajax_infope_gpsxxx();
 ?>