<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_genera_concor
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
  *  \author: Ing. Cristian Andrés Torres
  *  \date: 02/06/2020
  *  \date modified: 
  *  \return HTML
  */
  private function setRegistros() {
    $mSql = " SELECT a.num_remdes,   
                    a.dir_emailx,  
                    IF(((b.nom_tercer='') OR (b.nom_tercer IS NULL) OR (b.cod_tercer = '')),'GESTIÓN DE ASISTENCIA',b.nom_tercer),
                    IF(a.ind_seguim=1,'SI','NO') as 'ind_seguim',
                    IF(a.ind_infmen=1,'SI','NO') as 'ind_infmen',
                    a.cod_concor
                    
                FROM  ".BASE_DATOS.".tab_genera_concor a
                LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer b ON a.num_remdes = b.cod_tercer";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {
    		if($campo == "cod_concor"){
                $html = '<button onclick="delReg(this)" value="'.$datos['cod_concor'].'" data-estado="'.$datos['cod_concor'].'" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
          			$html .='<button onclick="formRegistro(\'form\', \'xl\', this.value)" value="'.$datos['cod_concor'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
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
   *  \brief: Crea el formulario de para registrar información del banco
   *  \author: Ing. Cristian Andres Torres
   *  \date: 02/06/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */

    function form(){
      try { 
      	//Valida si existe codgi de bancos para consultarlos o crearlos en vacio
        if($_REQUEST['cod_regist'] == ''){
          $mData = $arrayName = array('cod_concor' => '', 'dir_emailx' => '', 'num_remdes' => '', 'ind_infmen' => '', 'ind_seguim' => '');
          $titlo = "Registrar Correos";
          $action = 0;
        }else{
          $mSql = " SELECT  a.dir_emailx,
                            a.num_remdes,
                            ind_infmen,
                            ind_seguim,
                            a.cod_concor
                      FROM  ".BASE_DATOS.".tab_genera_concor a
                     WHERE  a.cod_concor = ".$_REQUEST['cod_regist'];
          $mMatriz = new Consulta($mSql, $this->conexion);
          $mData = $mMatriz->ret_matrix("a")[0];
          $titlo = "Actualizar";
          $action = 1;
        }

        $html .= '
                  <div class="panel panel-success">
                    <div class="panel-heading">
                      <h4>'.$titlo.'</h4>
                    </div>
                  <div class="panel-body">
                    <form role="form" id="registCorreo">
                      <div class="well">';

        $html .= $this->darCampos($mData, $action).'</div>
                    </form>
                  </div>
                </div>';

          echo utf8_decode($html);
      } catch (Exception $e) {
        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
      }
    }

     /*! \fn: darCampos
   *  \brief: retorna la informacion del formulario para registro y actualización
   *  \author: Ing. Cristian Andres Torres
   *  \date: 02/06/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function darCampos($datos="", $action=0){

        if($datos['ind_seguim'] ==1){
            $checkedSeguim="checked";
        }
        if($datos['ind_infmen'] ==1){
            $checkedInform="checked";
        }
      $campos = '
                        <div class="row">
                          <div class="col-12">
                            <div class="form-group">
                              <label for="correos"><span style="color:red">Puede registrar mas de un correo separado por (,)</span></label>
                              <input type="text" class="form-control" id="correos" name="correos" placeholder="Correo(s)" value="'.$datos['dir_emailx'].'">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-12">
                            <div class="form-group">
                              <label for="cliente">Cliente:</label>
                              <select class="form-control" id="cliente" name="cliente">
                                <option value="" style="background-color:#dff0d8; color:#3c763d">GESTIÓN DE ASISTENCIA</option>
                                '.$this->darClientes($datos['num_remdes']).'
                              </select>
                            </div>
                          </div>
                        </div>
                        <label>Informe de seguimientos mensual</label>
                        <input type="checkbox" class="form-control form-control-sm"  name="ind_infmen" id="ind_infmen" '.$checkedInform.' style="height: auto;"/>
                        <label> Informe de seguimientos diario</label>
                        <input type="checkbox" class="form-control form-control-sm"  name="ind_seguim" id="ind_seguim" '.$checkedSeguim.' style="height: auto;"/>
                        <input type="hidden" name="correoID" value="'.$datos['cod_concor'].'">
                        <input type="hidden" name="actionID" id="action" value="'.$action.'">
        ';
      
        return $campos;
    }

     /*! \fn: darClientes
   *  \brief: retorna html con las opciones de los datos de los clientes
   *  \author: Ing. Cristian Andres Torres
   *  \date: 02/06/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function darClientes($cod_cliente=""){
      $mSql = "SELECT a.cod_tercer, b.abr_tercer FROM ".BASE_DATOS.".tab_tercer_emptra a INNER JOIN tab_tercer_tercer b ON a.cod_tercer = b.cod_tercer ORDER BY b.abr_tercer ASC";
      $mMatriz = new Consulta($mSql, $this->conexion);
      $mData = $mMatriz->ret_matrix("a");
      $html="";
      foreach ($mData as $datos){
        $selected="";
        if($datos['cod_tercer'] == $cod_cliente){
          $selected ="selected";
        }
        $html.='<option value="'.$datos['cod_tercer'].'" '.$selected.'>'.strtoupper($datos['abr_tercer']).'</option>';
      }
      return $html;

    }


  /*! \fn: delReg
   *  \brief: Elimina el registro
   *  \author: Ing. Cristian Andrés Torres
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
        $mQuery = "DELETE FROM ".BASE_DATOS.".tab_genera_concor WHERE cod_concor = '".$_REQUEST['cod_regist']."';";
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
        echo 'Excepción delReg: ',  $e->getMessage(), "\n";
      }
    }

    /*! \fn: regForm
   *  \brief: Actualiza o crea nuevos registros de correos
   *  \author: Ing. Cristian Andres Torres
   *  \date: 02/06/2020
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: HTML 
   */
    function regForm(){
        try {
            //Consulta
            if($this->validaExitencia($_REQUEST['cliente'],$_REQUEST['action'])){
            
                $codigo = empty($_REQUEST['correoID']) ? '' : "cod_concor = ".$_REQUEST['correoID'].", ";
                $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_genera_concor 
                                SET  $codigo
                                    dir_emailx = '".$_REQUEST['correos']."',
                                    num_remdes = '".$_REQUEST['cliente']."',
                                    ind_infmen= '".($_REQUEST['ind_infmen'] == 'on'? 1 : 0)."',
                                    ind_seguim= '".($_REQUEST['ind_seguim'] == 'on'? 1 : 0)."',
                                    usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."',
                                    fec_creaci = NOW()
                        ON DUPLICATE KEY UPDATE 	
                                    dir_emailx  = '".$_REQUEST['correos']."',
                                    num_remdes = '".$_REQUEST['cliente']."',
                                    ind_infmen= '".($_REQUEST['ind_infmen'] == 'on'? 1 : 0)."',
                                    ind_seguim= '".($_REQUEST['ind_seguim'] == 'on'? 1 : 0)."',
                                    usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                                    fec_modifi = NOW()              
                                ";
                $consulta = new Consulta($mQuery, $this -> conexion); 
                
                if($consulta == true){
                $return['status'] = 200;
                $return['response'] = 'El registro ha sido creado exitosamente.';
                }else{
                $return['status'] = 500;
                $return['response'] = 'Error al realizar el registro.';
                }
            }else{
                $return['status'] = 500;
                $return['response'] = 'La transportadora ya contiene correos asociados a ella.';
            }
            echo json_encode($return);
        } catch (Exception $e) {
            echo 'Excepción regForm: ',  $e->getMessage(), "\n";
        }
    }

    function validaExitencia($num_remdes, $action){
        
      $mQuery = "SELECT COUNT(*) FROM ".BASE_DATOS.".tab_genera_concor WHERE num_remdes = '$num_remdes'";
      $consulta = new Consulta($mQuery, $this -> conexion); 
      $cantidad= $consulta->ret_matrix()[0][0];
      
      if($cantidad>=1 && $action =0){
        return false;
      }
      return true;
    }

}

$proceso = new ajax_genera_concor();
 ?>