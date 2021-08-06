<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_genera_horari
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
      case 'delReg':
        $this->delReg();
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
    $mSql = " SELECT  a.cod_horari AS codigo,
                      a.nom_horari,
                      a.com_diasxx,
                      a.hor_inicia,
                      a.hor_finalx,
                      a.cod_colorx,
                      IF(a.ind_estado=1,'ACTIVO','INACTIVO') as 'ind_estado',
                      a.cod_horari
                FROM  ".BASE_DATOS.".tab_config_horari a";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = self::cleanArray($mMatriz->ret_matrix("a"));

    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {
      /*if($campo == "ind_estado"){
            if($ind_intgps== 'NO'){
              if($valor == 'ACTIVO'){
                  $html ='<button onclick="updEst(this)" value="'.$datos['cod_operad'].'" data-estado="1" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
                }else{
                  $html ='<button onclick="updEst(this)" value="'.$datos['cod_operad'].'" data-estado="0" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
                }
              $html .='<button onclick="formRegistro(\'form\', \'xl\', this.value)" value="'.$datos['cod_operad'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
            }else{
              $html= '<label for="Name">Operador Estandar</label>';
            }    
          $data[$key][] = $valor;
          $data[$key][] = $html;	
        }else{
          $data[$key][] = $valor;	
        }*/ 

    		if($campo == "cod_horari"){
                $html = '<button onclick="delReg(this)" value="'.$datos['cod_horari'].'" data-estado="'.$datos['cod_horari'].'" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
          			$html .='<button onclick="formRegistro(\'form\', \'xl\', this.value)" value="'.$datos['cod_horari'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
          		$data[$key][] = $html;	
    		}elseif($campo == "cod_colorx"){
          $html = '<div style="width:100% ; height: 28px; background-color:'.$datos['cod_colorx'].'" !important></div>';
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
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */

    function form(){
      try { 
      	//Valida si existe  para consultarlos o crearlos en vacio
        if($_REQUEST['cod_regist'] == ''){
          $mData = $arrayName = array('cod_horari' => '', 'nom_horari' => '', 'cod_colorx' => '', 'com_diasxx' => '', 'hor_inicia' => '', 'hor_finalx' => '');
          $titlo = "Registrar Horarios";
        }else{
          $mSql = " SELECT  a.nom_horari,
                            a.cod_colorx,  
                            a.com_diasxx,
                            a.hor_inicia,
                            a.hor_finalx,
                            a.cod_horari
                      FROM  ".BASE_DATOS.".tab_config_horari a
                     WHERE  a.cod_horari = ".$_REQUEST['cod_regist'];
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
                    <form role="form" id="registHorari">
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


    function ValidaDias($dias, $dia ){
      if(in_array($dia, $dias)){
          return "checked";
      }
    }

     /*! \fn: darCampos
   *  \brief: retorna la informacion del formulario para registro y actualización
   *  \author: Ing. Andres Martinez
   *  \date: 02/06/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function darCampos($datos=""){

    $dias = explode("|", $datos['com_diasxx']);

      $campos = '
          <div class="row">
            <div class="form-group col-md-8">
              <label for="name">Nombre Horario:</label>
              <input type="text" class="form-control" id="nom_horari" name="nom_horari" placeholder="Nombre" value="'.$datos['nom_horari'].'">
            </div>
          
            <div class="form-group col-md-4">
              <label for="color">Color:</label>
              <input type="color" class="form-control" id="cod_colorx" name="cod_colorx" value="'.$datos['cod_colorx'].'">
            </div>
          </div>
          <div class="row">
            <div class="panel panel-success col-md-12">
              <h5>Dias de la semana</h5>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label>Lunes</label>
                <input type="checkbox" class="form-control form-control-sm" value="L" name="checkL" id="checkL" '.self::ValidaDias($dias, "L").' style="height: auto;"/>
            </div>  
            <div class="form-group col-md-6">
              <label>Martes</label>
                <input type="checkbox" class="form-control form-control-sm" value="M" name="checkM" id="checkM" '.self::ValidaDias($dias, "M").' style="height: auto;"/>
            </div>
          </div>
          
          <div class="row">
            <div class="form-group col-md-6">
              <label>Miercoles</label>
                <input type="checkbox" class="form-control form-control-sm" value="X" name="checkX" id="checkX" '.self::ValidaDias($dias, "X").' style="height: auto;"/>
            </div>
            <div class="form-group col-md-6">
              <label>Jueves</label>
                <input type="checkbox" class="form-control form-control-sm" value="J" name="checkJ" id="checkJ" '.self::ValidaDias($dias, "J").' style="height: auto;"/>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">  
              <label>Viernes</label>
                <input type="checkbox" class="form-control form-control-sm" value="V" name="checkV" id="checkV" '.self::ValidaDias($dias, "V").' style="height: auto;"/>
            </div>
            <div class="form-group col-md-6">
              <label>Sabado</label>
                <input type="checkbox" class="form-control form-control-sm" value="S" name="checkS" id="checkS" '.self::ValidaDias($dias, "S").' style="height: auto;"/>
            </div>
          </div> 
          <div class="row">
            <div class="form-group col-md-6">  
              <label>Domingo</label>
                <input type="checkbox" class="form-control form-control-sm" value="D" name="checkD" id="checkD" '.self::ValidaDias($dias, "D").' style="height: auto;"/>
            </div>
            <div class="form-group col-md-6">  
              <label>Festivo</label>
                <input type="checkbox" class="form-control form-control-sm" value="F" name="checkF" id="checkF" '.self::ValidaDias($dias, "F").' style="height: auto;"/>
            </div>
          </div> 
          <div class="row">
            <div class="form-group col-md-6">
              <label for="horini">Hora Inicio:</label>
              <input type="time" class="form-control" id="hor_inicia" name="hor_inicia" placeholder="Hora Inicio" value="'.$datos['hor_inicia'].'">
            </div>
            <div class="form-group col-md-6">
              <label for="horfin">Hora Fin:</label>
              <input type="time" class="form-control" id="hor_finalx" name="hor_finalx" placeholder="Hora Fin" value="'.$datos['hor_finalx'].'">
            </div>
          </div>
           
          <input type="hidden" name="horariID" value="'.$datos['cod_horari'].'">
        ';
      
        return $campos;
    }

    


  /*! \fn: delReg
   *  \brief: Elimina el registro
   *  \author: Ing. Andres Martinez
   *  \date: 02/06/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: json
   */
    function delReg(){
      try {
        //Varibales Necesarias
        $return = [];

        //Geera query
        $mQuery = "DELETE FROM ".BASE_DATOS.".tab_config_horari WHERE cod_horari = '".$_REQUEST['cod_regist']."';";
        $consulta = new Consulta($mQuery, $this -> conexion);   
        if($consulta == true){
          $return['status'] = 200;
          $return['response'] = 'El registro se ha eliminado correctamente.';
        }else{
          $return['status'] = 500;
          $return['response'] = 'Error al eliminar.';
        }

        //Devuelve estatus de la consulta
        echo json_encode($return);
      } catch (Exception $e) {
        echo 'Excepcion delReg: ',  $e->getMessage(), "\n";
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

  function StringDias($datos){
    $diasLetra=array("L", "M", "X", "J", "V", "S", "D", "F");
    $dias=array();
    $stringDias="";
    foreach ($diasLetra as $dia) {
      $inputletra = "check".$dia;
      if($datos[$inputletra]== $dia){
        $dias[]=$dia;
      }
      
    }
    
    foreach ($dias as $key => $dia) {
      $stringDias.=$dia;

      if($key+1 < COUNT($dias)){
        $stringDias.="|";
      }

    }
    return $stringDias;
  }


    /*! \fn: regForm
   *  \brief: Actualiza o crea nuevos registros de correos
   *  \author: Ing. Andres Martinez
   *  \date: 02/06/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
  
    function regForm(){
        try {
              
                $codigo = empty($_REQUEST['horariID']) ? '' : "cod_horari = ".$_REQUEST['horariID'].", ";
                $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_config_horari 
                                SET  $codigo
                                    nom_horari = '".$_REQUEST['nom_horari']."',
                                    cod_colorx = '".$_REQUEST['cod_colorx']."',
                                    com_diasxx= '".self::StringDias($_REQUEST)."',
                                    hor_inicia= '".$_REQUEST['hor_inicia']."',
                                    hor_finalx= '".$_REQUEST['hor_finalx']."',
                                    fec_creaci = NOW(),
                                    usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."'
                        ON DUPLICATE KEY UPDATE 	
                                  nom_horari = '".$_REQUEST['nom_horari']."',
                                  cod_colorx = '".$_REQUEST['cod_colorx']."',
                                  com_diasxx= '".self::StringDias($_REQUEST)."',
                                  hor_inicia= '".$_REQUEST['hor_inicia']."',
                                  hor_finalx= '".$_REQUEST['hor_finalx']."',
                                    usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                                    fec_modifi = NOW()              
                                ";
                $consulta = new Consulta($mQuery, $this -> conexion); 
                /*if($consulta == true){
            
                  $return['status'] = 200;
                  if(empty($_REQUEST['horariID'])){
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
                  
                  if(empty($_REQUEST['horariID'])){
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

$proceso = new ajax_genera_horari();
 ?>