<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_genera_matpro
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
  *  \author: Arnulfo Castañeda Yaima
  *  \date: 14/11/2024
  *  \date modified:
  *  \return BOOL
  */
  private function setRegistros() {
    $mSql = " SELECT  a.cod_matpr,
                      a.nom_event,
                      b.nom_etapax,
                      a.des_protoc,
                      a.ind_estado
                FROM  ".BASE_DATOS.".tab_genera_matpro a
                INNER JOIN ".BASE_DATOS.". tab_genera_etapax b ON a.cod_etapax = b.cod_etapax";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz =  $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {
    		if($campo == "ind_estado"){
    			if($valor == 1){
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_matpr'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
          		}else{
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_matpr'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
          		}
          			$html .='<button onclick="edit('.$datos['cod_matpr'].')" value="'.$datos['cod_matpr'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
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
   *  \author: Arnulfo Castañeda Yaima
   *  \date: 14/11/2024
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
        $mQuery = "UPDATE ".BASE_DATOS.".tab_genera_matpro
                      SET ind_estado = $estado,
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                    WHERE cod_matpr = ".$_REQUEST['cod_matpr'];
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
   *  \brief: Actualzia o crea registros de los protocolos
   *  \author: Arnulfo Castañeda Yaima
   *  \date: 14/11/2024
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function regForm(){
      try {
        //Varibales Necesarias
        $return = [];
        $codigo = empty($_REQUEST['cod_matpr']) ? '' : "cod_matpr = ".$_REQUEST['cod_matpr'].", ";
        //Consulta
	    $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_genera_matpro
	                       SET  $codigo
                              nom_event= '".$_REQUEST['nom_event']."',
                              cod_etapax= '".$_REQUEST['cod_etapax']."',
                              des_protoc= '".$_REQUEST['des_protoc']."',
	                            ind_estado = 1,
	                            usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."',
	                            fec_creaci = NOW()
	   ON DUPLICATE KEY UPDATE 	nom_event= '".$_REQUEST['nom_event']."',
                              cod_etapax= '".$_REQUEST['cod_etapax']."',
                              des_protoc= '".$_REQUEST['des_protoc']."',
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
        $cod_matpr = $_REQUEST['cod_matpr'];
        $mQuery = "SELECT * FROM ".BASE_DATOS.".tab_genera_matpro a WHERE a.cod_matpr='$cod_matpr'";
        $consulta = new Consulta($mQuery, $this -> conexion); 
        $dato = $consulta->ret_matriz("a")[0];

        $datos_option_etapa = $this->darEtapa($cod_matpr);

        $html = '<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editService">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center header-modal">
                ACTUALIZAR PROTOCOLO
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                    <div class="row">
                      <div class="col-md-12">
                        <p>Los campos marcados con (<span class="redObl">*</span>) son OBLIGATORIOS para la actualizacion del protocolo en el sistema.</p>
                      </div>
                    </div>

                    <div class="row margin-top-row">
                      <div class="col-md-8">
                        <label class="col-12 control-label"><span class="redObl">*</span> Nombre del Evento</label>
                        <input class="form-control form-control-sm" type="text" placeholder="Nombre del Evento" id="nom_eventID" name="nom_event" value="'.$dato['nom_event'].'" required>
                      </div>
                      <div class="col-md-4">
                        <label class="col-12 control-label"><span class="redObl">*</span> Etapa</label>
                        <select class="form-control form-control-sm" id="cod_etapaxID" name="cod_etapax" required>
                          <option value="">Escoja Opcion</option>
                          '.$datos_option_etapa.'
                        </select>
                      </div>

                    </div>
                    
                    <div class="row margin-top-row">
                      <div class="col-md-12">
                        <label for="observacion" class="form-label"><span class="redObl">*</span> Gestiones a realizar en caso de:</label>
                        <textarea class="form-control" id="des_protocID" name="des_protoc" rows="10" maxlength="1500" style="resize: none;" required>'.$dato['des_protoc'].'</textarea>
                      </div>
                    </div>
                    
                    <input type="hidden" name="cod_matpr" value="'.$cod_matpr.'">
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
        echo 'ExcepciÃ³n updEst: ',  $e->getMessage(), "\n";
      }
    }

    function darEtapa($cod_matpr){
      $sql="SELECT a.cod_etapax,a.nom_etapax FROM ".BASE_DATOS.".tab_genera_etapax a WHERE a.ind_estado=1;";
      $consulta = new Consulta($sql, $this->conexion);
      $respuestas = $consulta->ret_matriz("a");
      $html='';
      foreach($respuestas as $etapa){
        if($etapa['cod_etapax']==$cod_matpr){
          $html.='<option value="'.$etapa['cod_etapax'].'" selected>'.$etapa['nom_etapax'].'</option>';
        }else{
          $html.='<option value="'.$etapa['cod_etapax'].'">'.$etapa['nom_etapax'].'</option>';
        }
      }
      return utf8_encode($html);
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

$proceso = new ajax_genera_matpro();
 ?>