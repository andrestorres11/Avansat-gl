<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_tipos_pcontr
{
  var $AjaxConnection;
  
  public function __construct()
  {
    include('../lib/ajax.inc');
    
    include_once('../lib/general/constantes.inc');
    include_once('../../'.BASE_DATOS.'/constantes.inc');
    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST['option']) {
      case 'edit-forminput':
        $this->editForm();
        break;
      case "disaenable":
        $this->disaenable();
        break;
      case 'setRegistros':
        $this->setRegistros();
        break;
      case 'registrar':
        $this->regForm();
        break;
      case 'getRegistro':
        $this->getRegistro();
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
    $mSql = " SELECT  a.cod_tpcont,
                      a.nom_tpcont,
                      a.rut_iconox,
                      a.usr_creaci,
                      a.fec_creaci,
                      IF(a.usr_modifi IS NULL, 'n/a', a.usr_modifi) as usr_modifi,
                      IF(a.fec_modifi IS NULL, 'n/a', a.fec_modifi) as fec_modifi,
                      a.ind_estado
                FROM  ".BASE_DATOS.".tab_tipos_pcontr a";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz =  $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
      foreach ($datos as $campo => $valor) {
        
    		if($campo == "ind_estado"){
    			$btn = '<button type="button" class="btn btn-info btn-sm" style="margin-left:7px !important;" onclick="opeEdit('.$datos['cod_tpcont'].',1)"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
          $btn_opcion = '';
          if($valor==1){
             $btn_opcion = '<button type="button" class="mr-3 btn btn-danger btn-sm" onclick="logicDelete('.$datos['cod_tpcont'].', `disable`)"><i class="fa fa-times" aria-hidden="true"></i></button>';
          }else{
              $btn_opcion = '<button type="button" class="mr-3 btn btn-success btn-sm" onclick="logicDelete('.$datos['cod_tpcont'].', `enable`)"><i class="fa fa-check" aria-hidden="true"></i></button>';
          }
          $data[$key][] = '<center>'.$btn_opcion.''.$btn.'</center>';	
    		}else if($campo=='rut_iconox'){
          $img = '<center><img src="'.$valor.'" width="25px;"></center>';
          $data[$key][] = $img;	
        }else{
    			$data[$key][] = $valor;	
    		}
    	}
    }
    $return = array("data" => $data);
    echo json_encode($return);
  }


  /*! \fn: disaenable
   *  \brief: Actualiza el estado del registro
   *  \author: Ing. Andres Torres
   *  \date: 28/04/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
  function disaenable(){
    $sql = "UPDATE ".BASE_DATOS.".tab_tipos_pcontr SET
                ind_estado = '".$_REQUEST['ind_status']."',
                fec_modifi = NOW(),
                usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."'
            WHERE 
              cod_tpcont = '".$_REQUEST['cod_tpcont']."'";
    $query = new Consulta($sql, $this -> conexion);
    if($query){
        $info['status']=1000;
        $info['response']='Actualizacion realizada con exito';
    }else{
        $info['status']=2000;
        $info['response']='La informacion no pudo ser guardada. Intente nuevamente.';
    }
    echo json_encode($info);
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
        $codigo = empty($_REQUEST['cod_tpcont']) ? '' : "cod_tpcont = ".$_REQUEST['cod_tpcont'].", ";
        $rut_files =  "../../".BASE_DATOS."/";
        $nombre = '';
        if($_FILES['rut_iconox'] != null || $_FILES['rut_iconox'] != ''){
            $ext = explode(".", ($_FILES['rut_iconox']['name']));
            $nombre = 'files/img_tpcont/'.time().".".end($ext);
            $ruta_completa = $rut_files.''.$nombre;
            if (move_uploaded_file($_FILES['rut_iconox']['tmp_name'], $ruta_completa)) {
                $info['img_status']=1000;
                $info['img_msjxxx']='Imagen Cargada';
                $act_imagen =  "rut_iconox = '".$nombre."', ";
            }else{
                $info['img_status']=2000;
                $info['img_msjxxx']='La imagen no pudo ser cargada. Intente nuevamente.';
                if($_REQUEST['img_iconx']!=''){
                  $nombre = $_REQUEST['img_iconx'];
                  $info['img_status']=1000;
                }
            }
        }

        //Consulta
	      $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_tipos_pcontr
	                       SET  $codigo
                              nom_tpcont= '".$_REQUEST['nom_tpcont']."',
                              rut_iconox= '".$nombre."',
                              ind_estado= '1',
                              usr_creaci= '".$_SESSION['datos_usuario']['cod_usuari']."',
                              fec_creaci= NOW()
	      ON DUPLICATE KEY UPDATE nom_tpcont= '".$_REQUEST['nom_tpcont']."',
                             rut_iconox= '".$nombre."',
                             ind_estado= '1',
                             usr_modifi= '".$_SESSION['datos_usuario']['cod_usuari']."',
                             fec_modifi= NOW()";
	    $consulta = new Consulta($mQuery, $this -> conexion); 
	    if($consulta == true){
	      $return['status'] = 200;
        $return['response'] = 'El registro ha sido almacenado correctamente.';
        if($info['img_status']!=1000){
          $return['response'].=" ".$info['img_msjxxx'];
        }
	      
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
        $cod = $_REQUEST['cod'];
        $mQuery = "SELECT * FROM ".BASE_DATOS.".tab_tipos_pcontr a WHERE a.cod_tpcont='$cod'";
        $consulta = new Consulta($mQuery, $this -> conexion); 
        $dato = $consulta->ret_matriz("a")[0];

        $html = '<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editService">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center header-modal">
                EDITAR TIPO DE PUESTO DE CONTROL
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
                            <label class="col-12 control-label"><span class="redObl">*</span> Nombre del tipo de pc</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Nombre del tipo de pc" id="nom_tpcontEID" name="nom_tpcont" value="'.$dato['nom_tpcont'].'" required>
                        </div>
                    </div>
                   
                    <input type="hidden" name="cod_tpcont" value="'.$cod.'">
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

    function getRegistro(){
      $mSql = " SELECT  a.cod_tpcont,
      a.nom_tpcont,
      a.rut_iconox
      FROM  ".BASE_DATOS.".tab_tipos_pcontr a
      WHERE a.cod_tpcont = '".$_REQUEST['cod_tpcont']."'";
      $mMatriz = new Consulta($mSql, $this->conexion);
      $mMatriz =  $mMatriz->ret_matrix("a")[0];
      if($mMatriz['rut_iconox']!='' || $mMatriz['rut_iconox']!=NULL){
        $mMatriz['rut_iconoxC'] = DIREC_APLICA."".$mMatriz['rut_iconox'];
      }
      echo json_encode(self::cleanArray($mMatriz));
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

$proceso = new ajax_tipos_pcontr();
 ?>