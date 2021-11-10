<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_genera_viasxx
{
  var $AjaxConnection;
  
  public function __construct()
  {
    include('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST['option']) {
      case 'edit-forminput':
        $this->editForm();
        break;
      case 'updEst':
        $this->updEst();
        break;
      case 'setRegistros':
        $this->setRegistros();
        break;
      case 'registrar':
        $this->regForm();
        break;
    }

  }

  //---------------------------------------------
  /*! \fn: setRegistros
  *  \brief:Retorna los registros para el dataTable
  *  \author: Ing. Andres Torres
  *  \date: 27/01/2020
  *  \date modified: 21/12/2015
  *  \return BOOL
  */
  private function setRegistros() {
    $mSql = " SELECT  a.cod_salvia,
                      a.nom_salvia,
                      a.usr_creaci,
                      a.fec_creaci,
                      IF(a.usr_modifi IS NULL, 'n/a', a.usr_modifi) as usr_modifi,
                      IF(a.fec_modifi IS NULL, 'n/a', a.fec_modifi) as fec_modifi,
                      a.ind_estado
                FROM  ".BASE_DATOS.".tab_genera_salvia a";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz =  $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
      foreach ($datos as $campo => $valor) {
        
    		if($campo == "ind_estado"){
    			$data[$key][] = $valor == 1 ? 'Activo' : 'inactivo';	
    			if($valor == 1){
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_salvia'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
          		}else{
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_salvia'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
          		}
          			$html .='<button onclick="edit('.$datos['cod_salvia'].')" value="'.$datos['cod_salvia'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
          		$data[$key][] = $html;	
    		}else{
    			$data[$key][] = $valor;	
    		}
    	}
    }
    $return = array("data" => $data);
    echo json_encode($return);
  }


  /*! \fn: updEst
   *  \brief: Actualiza el estado del registro
   *  \author: Ing. Andres Torres
   *  \date: 28/04/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function updEst(){
      try {
        //Varibales Necesarias
        $return = [];
        $estado = $_REQUEST['estado'] == 1 ? 0 : 1;

        //Geera query
        $mQuery = "UPDATE ".BASE_DATOS.".tab_genera_salvia
                      SET ind_estado = $estado,
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                    WHERE cod_salvia = ".$_REQUEST['cod_salvia'];
        $consulta = new Consulta($mQuery, $this -> conexion);   
        if($consulta == true){
          $return['status'] = 200;
          $return['response'] = 'El registro ha sido actualizado exitosamente.';
        }else{
          $return['status'] = 500;
          $return['response'] = 'Error al realizar la actualzación.';
        }

        //Devuelve estatus de la consulta
        echo json_encode($return);
      } catch (Exception $e) {
        echo 'Excepción updEst: ',  $e->getMessage(), "\n";
      }
    }

    /*! \fn: regForm
   *  \brief: Actualzia o crea registros de vias
   *  \author: Ing. Andres Torres
   *  \date: 28/04/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function regForm(){
      try {
        //Varibales Necesarias
        $return = [];
        $codigo = empty($_REQUEST['cod_salvia']) ? '' : "cod_salvia = ".$_REQUEST['cod_salvia'].", ";
        //Consulta
	    $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_genera_salvia
	                       SET  $codigo
                              nom_salvia= '".$_REQUEST['nom_salvia']."',
                              ind_estado= '1',
                              usr_creaci= '".$_SESSION['datos_usuario']['cod_usuari']."',
                              fec_creaci= NOW()
	   ON DUPLICATE KEY UPDATE nom_salvia= '".$_REQUEST['nom_salvia']."',
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
        echo 'Excepcion updEst: ',  $e->getMessage(), "\n";
      }
    }

    function editForm(){
      try {
        $cod = $_REQUEST['cod'];
        $mQuery = "SELECT * FROM ".BASE_DATOS.".tab_genera_salvia a WHERE a.cod_salvia='$cod'";
        $consulta = new Consulta($mQuery, $this -> conexion); 
        $dato = $consulta->ret_matriz("a")[0];

        $html = '<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editService">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center header-modal">
                EDITAR SALIDA VIAL
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Los campos marcados con (<span class="redObl">*</span>) son OBLIGATORIOS para el registro del servicio en el sistema.</p>
                        </div>
                    </div>
                    <div class="row margin-top-row">
                        <div class="col-md-4">
                            <label class="col-12 control-label"><span class="redObl">*</span> Nombre de la via</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Nombre de la via" id="nom_salviaEID" name="nom_salvia" value="'.$dato['nom_salvia'].'" required>
                        </div>
                    </div>
                   
                    <input type="hidden" name="cod_salvia" value="'.$cod.'">
            </div>
            <div class="modal-footer"><center>
                <button  id="guarServicEdit" type="submit" class="swal2-confirm swal2-styled" aria-label="" style="display: inline-block; background-color: rgb(51, 102, 0); border-left-color: rgb(51, 102, 0); border-right-color: rgb(51, 102, 0);">Guardar</button>
                <button type="button" data-dismiss="modal" class="swal2-cancel swal2-styled" aria-label="" style="display: inline-block; background-color: rgb(170, 170, 170);">Cancelar</button></center>
            </div>
        </div>
        </form>
      </div>
    </div>';
      echo json_encode($html);  
      } catch(Exception $e) {
        echo 'Excepción updEst: ',  $e->getMessage(), "\n";
      }
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

$proceso = new ajax_genera_viasxx();
 ?>