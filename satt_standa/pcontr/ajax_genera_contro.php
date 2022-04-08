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
    $mSql = " SELECT  a.cod_contro,
                      a.nom_contro,
                      b.nom_ciudad,
                      a.dir_contro,
                      a.tel_contro,
                      a.val_longit,
                      a.val_latitu,
                      a.url_wazexx,
                      a.ind_estado
                FROM  ".BASE_DATOS.".tab_genera_contro a
                INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b ON a.cod_ciudad = b.cod_ciudad";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz =  $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
      foreach ($datos as $campo => $valor) {
        
    		if($campo == "ind_estado"){
    			$data[$key][] = $valor == 1 ? 'Activo' : 'inactivo';	
    			if($valor == 1){
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_contro'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;';
          		}else{
          			$html ='<button onclick="updEst(this)" value="'.$datos['cod_contro'].'" data-estado="'.$datos['ind_estado'].'" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i></button>&nbsp;';
          		}
          			$html .='<button onclick="edit('.$datos['cod_contro'].')" value="'.$datos['cod_contro'].'" class="btn btn-info"><i class="fa fa-edit" aria-hidden="true"></i></button>';
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
        $mQuery = "UPDATE ".BASE_DATOS.".tab_genera_contro
                      SET ind_estado = $estado,
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                    WHERE cod_contro = ".$_REQUEST['cod_contro'];
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
        echo 'Excepci?n updEst: ',  $e->getMessage(), "\n";
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

    function editForm(){
      try {
        $cod = $_REQUEST['cod'];
        $mQuery = "SELECT * FROM ".BASE_DATOS.".tab_genera_contro a WHERE a.cod_contro='$cod'";
        $consulta = new Consulta($mQuery, $this -> conexion); 
        $dato = $consulta->ret_matriz("a")[0];
        // echo "<pre>";
        //   print_r($dato);
        // echo "</pre>";
        // die();
        $datos_option = $this->darOpcionAsistencia();

        $ciudades = $this->getListadoCiudades($dato['cod_ciudad']);
        $TiposPc = $this->TiposPc($dato['cod_tpcont']);
    

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
                                <label class="col-12 control-label"><span class="redObl">*</span> Nombre</label>
                                <input class="form-control form-control-sm" type="text" placeholder="Nombre" id="nom_controID" name="nom_contro" value="'.$dato['nom_contro'].'" required>
                            </div>
                            
                            <div class="col-md-8">
                                <label class="col-12 control-label">Direccion</label>
                                <input class="form-control form-control-sm" type="text" placeholder="Direccion" id="dir_controID" name="dir_contro" value="'.$dato['dir_contro'].'">
                            </div>
                        </div>
      
                        <div class="row margin-top-row">
                            <div class="col-md-4">
                                <label class="col-12 control-label"> Telefono</label>
                                <input class="form-control form-control-sm" type="text" placeholder="Telefono" id="tel_controID" name="tel_contro" value="'.$dato['tel_contro'].'">
                            </div>
                            <div class="col-md-4">
                                <label class="col-12 control-label"><span class="redObl">*</span> Ciudad</label>
                                <select class="form-control form-control-sm" id="cod_ciudadID" name="cod_ciudad"  required>
                                    '.$ciudades.'
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="col-12 control-label"><span class="redObl">*</span>Tipo Puesto De control</label>
                                <select class="form-control form-control-sm" id="tipFormulID" name="tipFormul" required>
                                  <option value="">Escoja Opción</option>
                                  '.$TiposPc.'
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="row margin-top-row">
                            <div class="col-md-3">
                                <label class="col-12 control-label"><span class="redObl">*</span> Latitud</label>
                                <input class="form-control form-control-sm" type="text" placeholder="Latitud" id="val_latituID" name="val_latitu" value="'.$dato['val_latitu'].'" required>
                            </div>
                            <div class="col-md-3">
                                <label class="col-12 control-label"><span class="redObl">*</span> Longitud</label>
                                <input class="form-control form-control-sm" type="text" placeholder="Longitud" id="val_longitID" name="val_longit" value="'.$dato['val_longit'].'" required>
                            </div>
                            <div class="col-md-6">
                                <label class="col-12 control-label">Url Waze</label>
                                <input class="form-control form-control-sm" type="text" placeholder="Waze" id="url_wazexxID" name="url_wazexx" value="'.$dato['url_wazexx'].'">
                            </div>
                        </div>
                        <div class="row margin-top-row">
                            <div class="col-md-3">
                                <label class="col-12 control-label">Color</label>
                                <input class="form-control form-control-sm" type="text" placeholder="color" id="cod_colorxID" name="cod_colorx" value="'.$dato['cod_colorx'].'" required>
                            </div>
                            <div class="col-md-3">
                                <div class="card" style="width: 18rem;">
                                    <img src="https://www.sinrumbofijo.com/wp-content/uploads/2016/05/default-placeholder.png" class="card-img-top" alt="..." style="height: 60%; object-fit: cover; width: 200px; object-position: center center;" id="imgUpload">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="file" class="form-control-file" name="image" id="img" accept="image/*">
                            </div>
                        </div>
                     
                      <input type="hidden" name="cod_contro" value="'.$cod.'">
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
        echo 'Excepci?n updEst: ',  $e->getMessage(), "\n";
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

  function darOpcionAsistencia(){
    $sql="SELECT a.id,a.nom_asiste FROM ".BASE_DATOS.".tab_formul_asiste a WHERE a.ind_estado=1;";
    $consulta = new Consulta($sql, $this->conexion);
    $respuesta = $consulta->ret_matriz("a");
    $html='';
    foreach($respuesta as $dato){
      $html.='<option value="'.$dato['id'].'">'.$dato['nom_asiste'].'</option>';
    }
    return utf8_encode($html);
  }

  function TiposPc($cod){
    $sql="SELECT a.cod_tpcont,a.nom_tpcont FROM ".BASE_DATOS.".tab_tipos_pcontr a WHERE a.ind_estado=1;";
    $consulta = new Consulta($sql, $this->conexion);
    $respuestas = $consulta->ret_matriz("a");
    $html='';
    foreach($respuestas as $tipopuesto){
      if($tipopuesto['cod_tpcont']==$cod){
        $html.='<option value="'.$tipopuesto['cod_tpcont'].'" selected>'.$tipopuesto['nom_tpcont'].'</option>';
      }else{
        $html.='<option value="'.$tipopuesto['cod_tpcont'].'">'.$tipopuesto['nom_tpcont'].'</option>';
      }
    }
    return utf8_encode($html);
  }

  /*! \fn: getListadoCiudades
    *  \brief: Trae el listado de ciudades
    *  \author: 
    *  \date: 
    *  \date modified: dd/mm/aaaa
    *  \param: 
    *  \return: Matriz
  */
  function getListadoCiudades($ciudad)
  {
      $query = "SELECT a.cod_ciudad,CONCAT(UPPER(a.abr_ciudad),' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3),' - ',a.cod_ciudad) as nom_ciudad
        FROM " . BASE_DATOS . ".tab_genera_ciudad a,
              " . BASE_DATOS . ".tab_genera_depart b,
              " . BASE_DATOS . ".tab_genera_paises c
        WHERE a.cod_depart = b.cod_depart AND
              a.cod_paisxx = b.cod_paisxx AND
              b.cod_paisxx = c.cod_paisxx 
          GROUP BY 1 ORDER BY 2 ";
      $consulta = new Consulta($query, $this->conexion);
      $respuesta = $consulta->ret_matriz("a");

      foreach($respuesta as $dato){
        if ($ciudad == $dato['cod_ciudad']) {
          $html .='<option value="'.$dato['cod_ciudad'].'" selected>'.$dato['nom_ciudad'].'</option>';
        }else{
          $html .='<option value="'.$dato['cod_ciudad'].'">'.$dato['nom_ciudad'].'</option>';
        }
      }

      return utf8_encode($html);
  }

  function getConsecContro(){
    $query = "SELECT MAX(a.cod_contro)+1 as cod_contro
      FROM " . BASE_DATOS . ".tab_genera_contro a
      WHERE a.cod_contro NOT IN ('9999', '9997')";
      $consulta = new Consulta($query, $this->conexion);
      $respuesta = $consulta->ret_matriz("a");
    
    return $respuesta;

  }

  function getSeleccCiudad($ciudad, $despac = NULL)
  {
      $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3),' - ',a.cod_ciudad), UPPER( a.abr_ciudad )
                  FROM " . BASE_DATOS . ".tab_genera_ciudad a,
                        " . BASE_DATOS . ".tab_genera_depart b,
                        " . BASE_DATOS . ".tab_genera_paises c
                  WHERE a.cod_depart = b.cod_depart AND
                        a.cod_paisxx = b.cod_paisxx AND
                        b.cod_paisxx = c.cod_paisxx AND
                        a.cod_ciudad = '" . $ciudad . "' 
              GROUP BY 1 ORDER BY 2";

      $consulta = new Consulta($query, $this->conexion);
      return $ciudades = $consulta->ret_matriz();
  }
  
}


$proceso = new ajax_genera_contro();
 ?>