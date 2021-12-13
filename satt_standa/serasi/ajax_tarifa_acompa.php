<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_servic_asicar
{
  var $AjaxConnection;
  
  public function __construct()
  {
    include('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST['option']) {
      case 'consulta_ciudades':
        $this->consultaCiudades();
      break;
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
  
  /*! \fn: consultaCiudades
  *  \brief:Consulta y retorna un html con las ciudades según los nombres ingresados
  *  \author: Ing. Cristian Torres
  *  \date: 17/07/2020
  *  \date modified:
  *  \return html
  */
  public function consultaCiudades(){
    $busqueda = $_REQUEST['key'];
    $sql="SELECT a.cod_ciudad,a.nom_ciudad FROM tab_genera_ciudad a WHERE a.ind_estado = 1 AND a.nom_ciudad LIKE '%$busqueda%' ORDER BY a.nom_ciudad LIMIT 3";

      $resultado = new Consulta($sql, $this->conexion);
      $resultados = $resultado->ret_matriz();
      $htmls='';
      foreach($resultados as $valor){
        $htmls.='<div><a class="suggest-element" data="'.$valor['cod_ciudad'].' - '.$valor['nom_ciudad'].'" id="'.$valor['cod_ciudad'].'">'.$valor['nom_ciudad'].'</a></div>';
      }
      echo utf8_decode($htmls);
  }

  //---------------------------------------------
  /*! \fn: setRegistros
  *  \brief:Retorna los registros para el dataTable
  *  \author: Ing. Cristian Torres
  *  \date: 17/07/2020
  *  \date modified:
  *  \return
  */
  private function setRegistros() {
    $mSql = " SELECT  a.id,
                      b.nom_ciudad as 'ciu_origen',
                      c.nom_ciudad as 'ciu_destin',
                      CONCAT('$ ',a.val_tarifa),
                      a.ind_estado
                FROM  ".BASE_DATOS.".tab_tarifa_acompa a
                INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b ON
                a.ciu_origen = b.cod_ciudad
                INNER JOIN ".BASE_DATOS.".tab_genera_ciudad c ON
                a.ciu_destin = c.cod_ciudad
                ";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz =  $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {
    		if($campo == "ind_estado"){
    			if($valor == 1){
          			$html ='<button onclick="updEst(this)" value="'.$datos['id'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
          		}else{
          			$html ='<button onclick="updEst(this)" value="'.$datos['id'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
          		}
          			$html .='<button onclick="edit('.$datos['id'].')" value="'.$datos['id'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
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
   *  \author: Ing. Cristian Torres
   *  \date: 17/07/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
    function updEst(){
      try {
        //Varibales Necesarias
        $return = [];
        $estado = $_REQUEST['estado'] == 1 ? 0 : 1;

        //Geera query
        $mQuery = "UPDATE ".BASE_DATOS.".tab_tarifa_acompa
                      SET ind_estado = $estado,
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                    WHERE id = ".$_REQUEST['cod_acompa'];
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
   *  \brief: Actualzia o crea registros de nuevas tarifas
   *  \author: Ing. Cristian Torres
   *  \date: 17/07/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function regForm(){
      try {
        //Varibales Necesarias
        if(isset($_REQUEST['ciu_origen'])){
          $ciu_origen=$this->separarCodigoCiudad($_REQUEST['ciu_origen']);
        }
        if(isset($_REQUEST['ciu_destin'])){
          $ciu_destin=$this->separarCodigoCiudad($_REQUEST['ciu_destin']);
        }
        $return = [];
        $codigo = empty($_REQUEST['cod_acompa']) ? '' : "id = ".$_REQUEST['cod_acompa'].", ";
        $val_tarifa = preg_replace("/[^0-9,.]/", "", $_REQUEST['val_tarifa']);
        //Consulta
        if(!$this->validaRegistro($ciu_origen,$ciu_destin)){
            $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_tarifa_acompa
                              SET  $codigo
                                    ciu_origen= '".$ciu_origen."',
                                    ciu_destin= '".$ciu_destin."',
                                    val_tarifa= '".$val_tarifa."',
                                    ind_estado = 1,
                                    usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."',
                                    fec_creaci = NOW()
          ON DUPLICATE KEY UPDATE 	ciu_origen= '".$ciu_origen."',
                                    ciu_destin= '".$ciu_destin."',
                                    val_tarifa= '".$val_tarifa."',
                                    usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                                    fec_modifi = NOW()";
      
        }else{
            $mQuery = "UPDATE 
                        ".BASE_DATOS.".tab_tarifa_acompa
                      SET 
                          val_tarifa = '".$val_tarifa."',
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                      WHERE 
                          ciu_origen= '".$ciu_origen."' AND
                          ciu_destin= '".$ciu_destin."'";
        }

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
    
     /*! \fn: separarCodigoCiudad
   *  \brief: Separa el espacio el codigo y el nombre de la ciudad y retorna el codigo
   *  \author: Ing. Cristian Torres
   *  \date: 17/07/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: int cod_ciudad 
   */
    function separarCodigoCiudad($dato){
      $cod_ciudad = explode(" ", $dato);
      return trim($cod_ciudad[0]);
    }

     /*! \fn: editForm
   *  \brief: Retorna el html con los campos del registro a modificar
   *  \author: Ing. Cristian Torres
   *  \date: 17/07/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: html
   */
    function editForm(){
      try {
        $cod = $_REQUEST['cod'];
        $mQuery = "SELECT  a.id,
                    a.ciu_origen,
                    b.nom_ciudad as 'nom_origen',
                    a.ciu_destin,
                    c.nom_ciudad as 'nom_destin',
                    CONCAT('$ ',a.val_tarifa) as 'val_tarifa',
                    a.ind_estado
                  FROM  ".BASE_DATOS.".tab_tarifa_acompa a
                  INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b ON
                    a.ciu_origen = b.cod_ciudad
                  INNER JOIN ".BASE_DATOS.".tab_genera_ciudad c ON
                    a.ciu_destin = c.cod_ciudad
                  WHERE a.id='$cod'";
        $consulta = new Consulta($mQuery, $this -> conexion); 
        $dato = $consulta->ret_matriz("a")[0];

        $html = '<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editAcompa">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header text-center header-modal">
                  REGISTRAR NUEVA TARIFA
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                      <div class="row">
                           <div class="col-md-12">
                              <p>Los campos marcados con (<span class="redObl">*</span>) son OBLIGATORIOS para el registro de la tarifa en el sistema.</p>
                           </div>
                      </div>
                      <div class="row margin-top-row">
                          <div class="col-md-4">
                              <label class="col-12 control-label"><span class="redObl">*</span> Ciudad de Origen</label>
                              <input class="form-control form-control-sm" type="text" placeholder="Origen" id="ciu_origen" name="ciu_origen" onkeyup="busquedaCiudadEdicion(this)" onclick="borrado_input(this)" required  autocomplete="off" value="'.$dato['ciu_origen'].' - '.$dato['nom_origen'].'">
                              <div id="ciu_origen-suggestionse" class="suggestions" style="top:50px"></div>
                          </div>
                          <div class="col-md-4">
                              <label class="col-12 control-label"><span class="redObl">*</span> Ciudad de Destino</label>
                              <input class="form-control form-control-sm" type="text" placeholder="Destino" id="ciu_destin" name="ciu_destin" onkeyup="busquedaCiudadEdicion(this)" onclick="borrado_input(this)" required  autocomplete="off" value="'.$dato['ciu_destin'].' - '.$dato['nom_destin'].'">
                              <div id="ciu_destin-suggestionse" class="suggestions" style="top:50px"></div>
                          </div>
                          <div class="col-md-4">
                              <label class="col-12 control-label"><span class="redObl">*</span> Tarifa</label>
                              <input class="form-control form-control-sm loan-input" type="text" placeholder="Tarifa" id="val_tarifa" name="val_tarifa" required  autocomplete="off" value="'.$dato['val_tarifa'].'">
                          </div>
                          <input type="hidden" name="cod_acompa" value="'.$cod.'">
                      </div>
              </div>
              <div class="modal-footer"><center>
                  <button id="guarAcompa" type="submit" class="swal2-confirm swal2-styled" aria-label="" style="display: inline-block; background-color: rgb(51, 102, 0); border-left-color: rgb(51, 102, 0); border-right-color: rgb(51, 102, 0);">Guardar</button>
                  <button type="button" data-dismiss="modal" class="swal2-cancel swal2-styled" aria-label="" style="display: inline-block; background-color: rgb(170, 170, 170);">Cancelar</button></center>
              </div>
          </div>
        </div>
      </div>';
      echo json_encode($html);  
      } catch(Exception $e) {
        echo 'Excepción updEst: ',  $e->getMessage(), "\n";
      }
    }
    
    function validaRegistro($cod_ciuori,$cod_ciudes){
      $mQuery = "SELECT  COUNT(*) FROM ".BASE_DATOS.".tab_tarifa_acompa a WHERE a.ciu_origen = '$cod_ciuori' AND a.ciu_destin = '$cod_ciudes'";
      $consulta = new Consulta($mQuery, $this -> conexion);
      $registros =  $consulta->ret_matrix()[0][0];
      if($registros>0){
        return true;
      }else{
        return false;
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

$proceso = new ajax_servic_asicar();
 ?>