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
    setlocale(LC_MONETARY, 'es_CO');
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
    $mSql = " SELECT  a.id,
                      a.abr_servic,
                      b.nom_asiste,
                      c.nom_formul,
                      a.nom_campox,
                      a.tar_diurna,
                      a.tar_noctur,
                      a.hor_ininoc,
                      a.hor_finnoc,
                      a.ind_estado
                FROM  ".BASE_DATOS.".tab_servic_asicar a
                INNER JOIN ".BASE_DATOS.".tab_formul_asiste b ON
                a.tip_asicar = b.id
                INNER JOIN ".BASE_DATOS.".tab_formul_formul c ON
                a.cod_formul = c.cod_consec
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
    		}elseif($campo == "tar_diurna" || $campo == "tar_noctur"){
          $data[$key][] = money_format('%n',$valor);
	
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
        $mQuery = "UPDATE ".BASE_DATOS.".tab_servic_asicar
                      SET ind_estado = $estado,
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                    WHERE id = ".$_REQUEST['cod_servicasi'];
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
        $codigo = empty($_REQUEST['cod_servicasi']) ? '' : "id = ".$_REQUEST['cod_servicasi'].", ";
        $tar_diurna = preg_replace("/[^0-9,.]/", "", $_REQUEST['tar_diurna']);
        $tar_noctur = preg_replace("/[^0-9,.]/", "", $_REQUEST['tar_noctur']);
        //Consulta
	    $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_servic_asicar
	                       SET  $codigo
                              abr_servic= '".$_REQUEST['abr_servic']."',
                              des_servic= '".$_REQUEST['des_servic']."',
                              tip_asicar= '".$_REQUEST['tipSolici']."',
                              cod_formul= '".$_REQUEST['tipFormul']."',
                              nom_campox= '".$_REQUEST['nom_campox']."',
                              tar_diurna= '".$tar_diurna."',
                              tar_noctur= '".$tar_noctur."',
                              hor_ininoc= '".$_REQUEST['hor_ininoc']."',
                              hor_finnoc= '".$_REQUEST['hor_finnoc']."',
	                            ind_estado = 1,
	                            usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."',
	                            fec_creaci = NOW()
	   ON DUPLICATE KEY UPDATE 	abr_servic= '".$_REQUEST['abr_servic']."',
                              des_servic= '".$_REQUEST['des_servic']."',
                              tip_asicar= '".$_REQUEST['tipSolici']."',
                              cod_formul= '".$_REQUEST['tipFormul']."',
                              nom_campox= '".$_REQUEST['nom_campox']."',
                              tar_diurna= '".$_REQUEST['tar_diurna']."',
                              tar_noctur= '".$_REQUEST['tar_noctur']."',
                              hor_ininoc= '".$_REQUEST['hor_ininoc']."',
                              hor_finnoc= '".$_REQUEST['hor_finnoc']."',
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
        $cod = $_REQUEST['cod'];
        $mQuery = "SELECT * FROM ".BASE_DATOS.".tab_servic_asicar a WHERE a.id='$cod'";
        $consulta = new Consulta($mQuery, $this -> conexion); 
        $dato = $consulta->ret_matriz("a")[0];

        $datos_option = $this->darOpcionAsistencia($dato['tip_asicar']);
        $datos_option_formul = $this->darFormulario($dato['cod_formul']);

        $html = '<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editService">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center header-modal">
                EDITAR SERVICIO
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
                            <label class="col-12 control-label"><span class="redObl">*</span> Abreviatura del Servicio</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Abreviatura del Servicio" id="abr_servicEID" name="abr_servic" value="'.$dato['abr_servic'].'" required>
                        </div>
                        <div class="col-md-8">
                            <label class="col-12 control-label"><span class="redObl">*</span> Descripción del Servicio</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Descripcion del Servicio" id="des_servicEID" name="des_servic" value="'.$dato['des_servic'].'" required>
                        </div>
                    </div>

                    <div class="row margin-top-row">
                        <div class="col-md-4">
                            <label class="col-12 control-label"><span class="redObl">*</span> Tipo de Solicitud</label>
                            <select class="form-control form-control-sm" id="tipSoliciEID" name="tipSolici" required>
                                <option value="">Escoja Opción</option>
                                '.$datos_option.'
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="col-12 control-label"><span class="redObl">*</span> Formulario</label>
                            <select class="form-control form-control-sm" id="tipFormulEID" name="tipFormul" required>
                                <option value="">Escoja Opción</option>
                                '.$datos_option_formul.'
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="col-12 control-label"><span class="redObl">*</span> Nombre del Campo</label>
                            <input class="form-control form-control-sm" type="text" placeholder="Nombre del campo" id="nom_campoxEID" name="nom_campox" value="'.$dato['nom_campox'].'" required>
                        </div>
                    </div>

                    <hr>
                    <div class="row margin-top-row">
                        <div class="col-md-3">
                            <label class="col-12 control-label"><span class="redObl">*</span> Tarifa Diurna</label>
                            <input class="form-control form-control-sm loan-input" type="text" placeholder="Tarifa Diurna" id="tar_diurnaEID" name="tar_diurna" value="'.$dato['tar_diurna'].'" required>
                        </div>
                        <div class="col-md-3">
                            <label class="col-12 control-label"><span class="redObl">*</span> Tarifa Nocturna</label>
                            <input class="form-control form-control-sm loan-input" type="text" placeholder="Tarifa Nocturna" id="tar_nocturEID" name="tar_noctur" value="'.$dato['tar_noctur'].'" required>
                        </div>
                        <div class="col-md-3">
                            <label class="col-12 control-label"><span class="redObl">*</span> Hora de inicio Nocturna</label>
                            <input class="form-control form-control-sm form_datetime" type="time" id="hor_ininocE" name="hor_ininoc" value="'.$dato['hor_ininoc'].'" required>
                        </div>
                        <div class="col-md-3">
                            <label class="col-12 control-label"><span class="redObl">*</span> Hora de fin Nocturna</label>
                            <input class="form-control form-control-sm hasDatepicker" type="time" id="hor_finnocEID" name="hor_finnoc" value="'.$dato['hor_finnoc'].'" required>
                        </div>
                    </div>
                    <div class="row margin-top-row">
                        <div class="col-md-12">
                            <p>Para las horas que no esten en el rango de hora de inicio noctura y hora de fin nocturna tomara por defecto la tarifa diurna.</p>
                        </div>
                    </div>
                    <input type="hidden" name="cod_servicasi" value="'.$cod.'">
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

    function darOpcionAsistencia($cod){
      $sql="SELECT a.id,a.nom_asiste FROM ".BASE_DATOS.".tab_formul_asiste a WHERE a.ind_estado=1;";
      $consulta = new Consulta($sql, $this->conexion);
      $respuestas = $consulta->ret_matriz("a");
      $html='';
      foreach($respuestas as $asistencia){
        if($asistencia['id']==$cod){
          $html.='<option value="'.$asistencia['id'].'" selected>'.$asistencia['nom_asiste'].'</option>';
        }else{
          $html.='<option value="'.$asistencia['id'].'">'.$asistencia['nom_asiste'].'</option>';
        }
      }
      return utf8_encode($html);
    }
  
    function darFormulario($cod){
      $sql="SELECT a.cod_consec,a.nom_formul FROM ".BASE_DATOS.".tab_formul_formul a WHERE a.ind_estado=1;";
      $consulta = new Consulta($sql, $this->conexion);
      $respuestas = $consulta->ret_matriz("a");
      $html='';
      foreach($respuestas as $formulario){
        if($formulario['cod_consec']==$cod){
          $html.='<option value="'.$formulario['cod_consec'].'" selected>'.$formulario['nom_formul'].'</option>';
        }else{
          $html.='<option value="'.$$formulario['cod_consec'].'">'.$formulario['nom_formul'].'</option>';
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

$proceso = new ajax_servic_asicar();
 ?>