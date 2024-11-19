<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_genera_hoceva
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
  *  \author: Ing. Luis Manrique
  *  \date: 27/01/2020
  *  \date modified: 21/12/2015
  *  \return BOOL
  */
  private function setRegistros() {
    $mSql = " SELECT  a.cod_hoceva,
                      a.rid_hoceva,
                      a.des_hoceva,
                      a.oet_hoceva,
                      a.ind_estado
                FROM  ".BASE_DATOS.".tab_genera_hoceva a";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz =  $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {
    		if($campo == "ind_estado"){
    			if($valor == 1){
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_hoceva'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
          		}else{
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_hoceva'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
          		}
          			$html .='<button onclick="edit('.$datos['cod_hoceva'].')" value="'.$datos['cod_hoceva'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
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
   *  \author: Ing. Luis Manrique
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
        $mQuery = "UPDATE ".BASE_DATOS.".tab_genera_hoceva
                      SET ind_estado = $estado,
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                    WHERE cod_hoceva = ".$_REQUEST['cod_hoceva'];
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
   *  \brief: Actualzia o crea registros de bancos
   *  \author: Ing. Luis Manrique
   *  \date: 28/04/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function regForm(){
      try {
        //Varibales Necesarias
        $return = [];
        $codigo = empty($_REQUEST['cod_hoceva']) ? '' : "cod_hoceva = ".$_REQUEST['cod_hoceva'].", ";
        //Consulta
	    $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_genera_hoceva
	                       SET  $codigo
                              rid_hoceva= '".$_REQUEST['rid_hoceva']."',
                              des_hoceva= '".$_REQUEST['des_hoceva']."',
                              oet_hoceva= '".$_REQUEST['oet_hoceva']."',
	                            ind_estado = 1,
	                            usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."',
	                            fec_creaci = NOW()
	   ON DUPLICATE KEY UPDATE 	rid_hoceva= '".$_REQUEST['rid_hoceva']."',
                              des_hoceva= '".$_REQUEST['des_hoceva']."',
                              oet_hoceva= '".$_REQUEST['oet_hoceva']."',
	                            usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
	                            fec_modifi = NOW()";
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
        echo 'Excepción updEst: ',  $e->getMessage(), "\n";
      }
    }

    function editForm(){
      try {
        $cod_hoceva = $_REQUEST['cod_hoceva'];
        $mQuery = "SELECT * FROM ".BASE_DATOS.".tab_genera_hoceva a WHERE a.cod_hoceva='$cod_hoceva'";
        $consulta = new Consulta($mQuery, $this -> conexion); 
        $dato = $consulta->ret_matriz("a")[0];

        $html = '<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editService">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center header-modal">
                EDITAR HOMOLOGACION DE RUTA
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
                            <label class="col-12 control-label"><span class="redObl">*</span> Codigo RID (Ceva)</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Codigo RID (Ceva)" id="rid_hocevaID" name="rid_hoceva" value="'.$dato['rid_hoceva'].'" required>
                        </div>
                          <div class="col-md-4">
                            <label class="col-12 control-label"><span class="redObl">*</span> Alias (Ceva)</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Alias (Ceva)" id="des_hocevaID" name="des_hoceva" value="'.$dato['des_hoceva'].'" required>
                        </div>
                          <div class="col-md-4">
                            <label class="col-12 control-label"><span class="redObl">*</span> Codigo Avansat GL</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Codigo Avansat GL" id="oet_hocevaID" name="oet_hoceva" value="'.$dato['oet_hoceva'].'" required>
                        </div>
                    </div>

                    <input type="hidden" name="cod_hoceva" value="'.$cod_hoceva.'">
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

$proceso = new ajax_genera_hoceva();
 ?>