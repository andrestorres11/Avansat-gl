<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_genera_region
{
  var $AjaxConnection;
  
  public function __construct()
  {
    include('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST['option']) {
      case 'form':
        $this->form();
        break;
      case 'updEst':
        $this->updEst();
        break;
      case 'regForm':
        $this->regForm();
        break;
      case 'setRegistros':
        $this->setRegistros();
        break;
    }

  }

  //---------------------------------------------
  /*! \fn: setRegistros
  *  \brief:Retorna los registros para el dataTable
  *  \author: Ing. Andres Martinez
  *  \date: 02/08/2021
  *  \date modified: 
  *  \return HTML
  */
  private function setRegistros() {
    $mSql = " SELECT  a.cod_region AS codigo,
                      a.nom_region,
                      IF(a.ind_estado=1,'ACTIVO','INACTIVO') as 'ind_estado',
                      IF(a.ind_estado=1,'ACTIVO','INACTIVO') as estado,
                      a.cod_region
                FROM  ".BASE_DATOS.".tab_genera_region a";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = self::cleanArray($mMatriz->ret_matrix("a"));

    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {

    		if($campo == "estado"){
          if($valor == 'ACTIVO'){
                $html = '<button onclick="updEst(this)" value="'.$datos['cod_region'].'" data-estado="1" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
          }else{
              $html = '<button onclick="updEst(this)" value="'.$datos['cod_region'].'" data-estado="0" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
          }
              $html .='<button onclick="formRegistro(\'form\', \'xl\', this.value)" value="'.$datos['cod_region'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
          		$data[$key][] = $html;	
    		}else{
    			$data[$key][] = $valor;	
    		}
    		
    	}
    }

    $return = array("data" => $data);
    echo json_encode($return);
  }

   /*! \fn: form
   *  \brief: Crea el formulario de para registrar informacion
   *  \author: Ing. Andres Martinez
   *  \date: 02/06/2020
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return: HTML 
   */

    function form(){
      try { 
      	//Valida si existe  para consultarlos o crearlos en vacio
        if($_REQUEST['cod_regist'] == ''){
          $mData = $arrayName = array('cod_region' => '', 'nom_region' => '');
          $titlo = "Registrar Regional";
        }else{
          $mSql = " SELECT  a.nom_region,
                            a.cod_region
                      FROM  ".BASE_DATOS.".tab_genera_region a
                     WHERE  a.cod_region = ".$_REQUEST['cod_regist'];
          $mMatriz = new Consulta($mSql, $this->conexion);
          $mData = $mMatriz->ret_matrix("a")[0];
          $titlo = "Actualizar";
        }

        $html .= '
                  <div class="panel panel-success">
                    <div class="panel-heading">
                      <h4>'.$titlo.'</h4>
                    </div>
                  <div class="panel-body">
                    <form role="form" id="registRegional">
                      <div class="well">';

        $html .= $this->darCampos($mData).'</div>
                    </form>
                  </div>
                </div>';

          echo utf8_decode($html);
      } catch (Exception $e) {
        echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
      }
    }


     /*! \fn: darCampos
   *  \brief: retorna la informacion del formulario para registro y actualizaciÃ³n
   *  \author: Ing. Andres Martinez
   *  \date: 02/06/2020
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return: HTML 
   */
    function darCampos($datos=""){
      $campos = '
          <div class="row">
            <div class="form-group col-md-12">
              <label for="name">Nombre Regional:</label>
              <input type="text" class="form-control" id="nom_region" name="nom_region" placeholder="Nombre" value="'.$datos['nom_region'].'" required>
            </div>

          </div>
          
          <input type="hidden" name="regionID" value="'.$datos['cod_region'].'">
        ';
      
        return $campos;
    }

    


  /*! \fn: delReg
   *  \brief: Elimina el registro
   *  \author: Ing. Andres Martinez
   *  \date: 02/06/2020
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return: json
   */
    function updEst(){
      try {
      //Varibales Necesarias
        $return = [];
        $estado = $_REQUEST['estado'] == 1 ? 0 : 1;

        //Geera query

        $mQuery = "UPDATE ".BASE_DATOS.".tab_genera_region 
                      SET ind_estado = $estado,
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                    WHERE cod_region = ".$_REQUEST['cod_regist'];
        
        $consulta = new Consulta($mQuery, $this -> conexion);   
        if($consulta == true){
          $return['status'] = 200;
          $return['response'] = 'El registro ha sido actualizado exitosamente.';
        }else{
          $return['status'] = 500;
          $return['response'] = 'Error al realizar la actualzacion.';
        }

        //Devuelve estatus de la consulta
        echo json_encode($return);
      }catch (Exception $e){
        echo 'Excepcion updEst: ',  $e->getMessage(), "\n";
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



    /*! \fn: regForm
   *  \brief: Actualiza o crea nuevos registros de correos
   *  \author: Ing. Andres Martinez
   *  \date: 02/06/2020
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return: HTML 
   */
  
    function regForm(){
        try {
              
                $codigo = empty($_REQUEST['regionID']) ? '' : "cod_region = ".$_REQUEST['regionID'].", ";
                $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_genera_region 
                                SET  $codigo
                                    nom_region = '".$_REQUEST['nom_region']."',
                                    fec_creaci = NOW(),
                                    usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."'
                        ON DUPLICATE KEY UPDATE 	
                                    nom_region = '".$_REQUEST['nom_region']."',
                                    ind_estado = 1,
                                    usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                                    fec_modifi = NOW()              
                                ";
                $consulta = new Consulta($mQuery, $this -> conexion); 
                /*if($consulta == true){
            
                  $return['status'] = 200;
                  if(empty($_REQUEST['regionID'])){
                    $return['response'] = 'El Operador GPS ha sido registrado exitosamente.';
                  }else{
                    $return['response'] = 'El Operador GPS ha sido Actualizado exitosamente.';
                  }
                }else{
                  $return['status'] = 500;
                  $return['response'] = 'Error al realizar el registro.';
                }*/
                if($consulta == true){
                  $return['status'] = 200;
                  
                  if(empty($_REQUEST['regionID'])){
                    $return['response'] = 'El registro ha sido creado exitosamente.';
                  }else{
                    $return['response'] = 'El registro ha sido Actualizado exitosamente.';
                  }
            }else{
                $return['status'] = 500;
                $return['response'] = 'La transportadora ya contiene correos asociados a ella.';
            }
            echo json_encode($return);
        } catch (Exception $e) {
            echo 'Excepcion regForm: ',  $e->getMessage(), "\n";
        }
    }


}


$proceso = new ajax_genera_region();
 ?>
