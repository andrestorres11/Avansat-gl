<?php

// error_reporting(E_ALL);
// ini_set('display_errors', '1');

class ajax_genera_contro
{
  var $AjaxConnection;
  
  public function __construct()
  {
    include('../lib/ajax.inc');
    include('../lib/ajax.inc');
    include('../lib/general/Despachos.inc');

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST['option']) {
      case 'updEst':
        $this->updEst();
        break;
      case 'registrar':
        $this->regForm();
        break;
      case 'editar':
        $this->ediForm();
        break;
    }

  }



  /*! \fn: updEst
   *  \brief: Actualiza el estado del registro
   *  \author: Ing. Andres Torres
   *  \date: 28/04/2020
   *  \date modified: dia/mes/a�o
   *  \param: 
   *  \return: HTML 
   */
    function updEst(){

        //Varibales Necesarias
        $return = [];
        $estado = $_REQUEST['estado'] == 1 ? 0 : 1;

        //Geera query
        $mQuery = "UPDATE ".BASE_DATOS.".tab_genera_contro
                      SET ind_estado = $estado,
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                    WHERE cod_contro = ".$_REQUEST['cod_contro'];
        $consulta = new Consulta($mQuery, $this -> conexion);   

        if($consulta == true){

          $mensaje = $estado ==0 ?"Se inactiv&oacute; el puesto  de control exitosamente.":"Se activ&oacute; el puesto  de control exitosamente.";
          $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
          $mens = new mensajes();
          $mens -> correcto2("ESTADO",$mensaje);

        }

      
    }

    /*! \fn: regForm
   *  \brief: Actualzia o crea registros de vias
   *  \author: Ing. Andres Torres
   *  \date: 28/04/2020
   *  \date modified: dia/mes/a�o
   *  \param: 
   *  \return: HTML 
   */
    function regForm(){
      try {
        //Varibales Necesarias
        $return = [];
        $consecutivo = $this->getConsecContro();
        $codigo = empty($_REQUEST['cod_contro']) ? "cod_contro = ".$consecutivo[0]['cod_contro'].", " : "cod_contro = ".$_REQUEST['cod_contro'].", ";
        //Consulta
	    $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_genera_contro
	                       SET  $codigo
                              nom_contro= '".$_REQUEST['nom_contro']."',
                              cod_ciudad = '".$_REQUEST['cod_ciudad']."',
                              cod_tpcont = '".$_REQUEST['tipFormul']."',
                              dir_contro = '".$_REQUEST['dir_contro']."',
                              tel_contro = '".$_REQUEST['tel_contro']."',
                              val_longit = '".$_REQUEST['val_longit']."',
                              val_latitu = '".$_REQUEST['val_latitu']."',
                              url_wazexx = '".$_REQUEST['url_wazexx']."',
                              ind_estado= '1',
                              usr_creaci= '".$_SESSION['datos_usuario']['cod_usuari']."',
                              fec_creaci= NOW()
	   ON DUPLICATE KEY UPDATE nom_contro= '".$_REQUEST['nom_contro']."',
                             cod_ciudad= '".$_REQUEST['cod_ciudad']."',
                             dir_contro= '".$_REQUEST['dir_contro']."',
                             cod_tpcont = '".$_REQUEST['tipFormul']."',
                             tel_contro= '".$_REQUEST['tel_contro']."',
                             val_longit= '".$_REQUEST['val_longit']."',
                             val_latitu= '".$_REQUEST['val_latitu']."',
                             url_wazexx= '".$_REQUEST['url_wazexx']."',
                             ind_estado= '1',
                             usr_modifi= '".$_SESSION['datos_usuario']['cod_usuari']."',
                             fec_modifi= NOW()";
	    $consulta = new Consulta($mQuery, $this -> conexion);   
	    if($consulta == true){
	      $return['status'] = 200;
	      $return['response'] = 'El registro ha sido almacenado correctamente.';
	    }else{
	      $return['status'] = 500;
	      $return['response'] = 'Error al realizar el registro.';
	    }
        echo json_encode($return);
      } catch (Exception $e) {
        echo 'Excepci?n updEst: ',  $e->getMessage(), "\n";
      }
    }


    function ediForm(){
      try {
        //Varibales Necesarias
        $return = [];
        
        //Consulta
        $mQuery = "UPDATE ".BASE_DATOS.".tab_genera_contro
                              SET
                                nom_contro= '".$_REQUEST['nom_contro']."',
                                cod_ciudad= '".$_REQUEST['cod_ciudad']."',
                                dir_contro= '".$_REQUEST['dir_contro']."',
                                cod_tpcont = '".$_REQUEST['tipFormul']."',
                                tel_contro= '".$_REQUEST['tel_contro']."',
                                val_longit= '".$_REQUEST['val_longit']."',
                                val_latitu= '".$_REQUEST['val_latitu']."',
                                url_wazexx= '".$_REQUEST['url_wazexx']."',
                                ind_estado= '1',
                                usr_modifi= '".$_SESSION['datos_usuario']['cod_usuari']."',
                                fec_modifi= NOW()
                              WHERE cod_contro= '".$_REQUEST['cod_contro']."'";
	    $consulta = new Consulta($mQuery, $this -> conexion);   
	    if($consulta == true){
	      $return['status'] = 200;
	      $return['response'] = 'Se ha actualizado el puesto control: '.$_REQUEST['cod_contro'].' correctamente.';
	    }else{
	      $return['status'] = 500;
	      $return['response'] = 'Error al realizar la actualización.';
	    }
        echo json_encode($return);
      } catch (Exception $e) {
        echo 'Excepci?n updEst: ',  $e->getMessage(), "\n";
      }
    }


  function getConsecContro(){
    $query = "SELECT MAX(a.cod_contro)+1 as cod_contro
      FROM " . BASE_DATOS . ".tab_genera_contro a
      WHERE a.cod_contro NOT IN ('9999', '9997')";
      $consulta = new Consulta($query, $this->conexion);
      $respuesta = $consulta->ret_matriz("a");
    
    return $respuesta;

  }


  
}


$proceso = new ajax_genera_contro();
 ?>