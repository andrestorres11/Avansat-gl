<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_genera_viasxx
{
  var $AjaxConnection;
  private static $mListaDespachosTMS = [];
  private static $arrayTransportadora = [];
  private static $arrayTodas = [];
  private static $arrayDiferencias = [];
  
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
    self::$mListaDespachosTMS = self::getCurl([
      'PARAMS' => ['codTercer' => $_REQUEST['cod_tercer']  ]
    ]);

    //aqui recorremos los servidores
    foreach (self::$mListaDespachosTMS as $key => $value) {
      
      for ($i=0; $i < count($value['data']); $i++) { 
        $value['data'][$i]['server'] = self::getNameServer($value['server']);
        //guardamos en una variable global el generico de los despachos
        self::$arrayTodas[] = $value['data'][$i];

        //luego guardamos separado por transportadora para el detallado.
        self::$arrayTransportadora[$value['data'][$i]['cod_transp']][] = $value['data'][$i];
      }
    }

    foreach (self::$arrayTransportadora as $key => $value) {
      $cantidadGl = self::getcantidadDespachosEnRutaGL($value[0]);
      //aqui se guarda la data que se muestra en el listado
      if ($cantidadGl != count($value)) {
        self::$arrayDiferencias[] = [
          'cod_transp' => $value[0]['cod_transp'],
          'nom_transp' => $value[0]['nom_transp'],
          'url_transp' => $value[0]['server'],
          'can_avansa' => $cantidadGl,
          'can_trafic' => count($value),
          'can_difere' => count($value) - $cantidadGl
        ] ;
      }
      
    }

    $data = [];

    foreach (self::$arrayDiferencias as $key => $datos) {
      foreach ($datos as $campo => $valor) {
        $data[$key][] = $valor;	
      }
    }
    $return = array("data" => $data);
    echo json_encode($return);
    
  }

  /*! \fn: getNameServer
	 *  \brief: traduce el nombre del servidor 
	 *  \author: Ing. Andres Torres
	 *	\date: 2021-10-11 
	 *  \param: 
	 *  \return:
	 */
	private function getNameServer($mServer = NULL)
	{

    switch ($mServer) {
      case 'abbd.intrared.net':
        $server = 'Avansat Basico';
        break;
      
      case 'a2bd.intrared.net':
        $server = 'Avansat 2';
        break;

      case 'a3bd.intrared.net':
        $server = 'Avansat 3';
        break;

      case 'a4bd.intrared.net':
        $server = 'Avansat 4';
        break;

      case 'a5bd.intrared.net':
        $server = 'Avansat 5';
        break;
      
      case 'a6bd.intrared.net':
        $server = 'Avansat 6';
        break;

      default:
        $server = 'No se encuentra';
        break;
    }

    return $server;
  }


  /*! \fn: getCurl
	 *  \brief: Genera el informe 
	 *  \author: Ing. Nelson Liberato
	 *	\date: 2021-07-08 
	 *  \param: 
	 *  \return:
	 */
	private function getCurl($mData = NULL)
	{
		try 
		{
			$s = curl_init();
			curl_setopt($s,CURLOPT_URL, URL_INTERF_CENTRAL.'/ap/interf/DespachosActivosTms.php');
			curl_setopt($s,CURLOPT_HTTPHEADER,array('Expect:'));
			curl_setopt($s,CURLOPT_TIMEOUT,30); 
			curl_setopt($s,CURLOPT_RETURNTRANSFER,true);  
			curl_setopt($s,CURLOPT_POST,true);
			curl_setopt($s,CURLOPT_POSTFIELDS, http_build_query($mData['PARAMS']));
			$mResponse = curl_exec($s);
			curl_close($s);
			//echo "<pre class='vainaRara' style='color: blue;'>MIerda: "; print_r( $mResponse ); echo "</pre>";  

			return json_decode($mResponse, true);
		} 
		catch (Exception $e) 
		{
			
		}
	}	

  /*! \fn: getcantidadDespachosEnRutaGL
	 *  \brief: Genera el informe 
	 *  \author: Ing. Nelson Liberato
	 *	\date: 2021-07-09 
	 *  \param: 
	 *  \return:
	 */
	private function getcantidadDespachosEnRutaGL($mData = NULL)
	{
		try 
		{
			$sql = "
			SELECT SUM(a.can_despac) AS can_despac
			  FROM 
				( 
					SELECT COUNT(b.num_despac) AS can_despac 
				 					  FROM ".BASE_DATOS.".tab_despac_vehige a
				 			    INNER JOIN ".BASE_DATOS.".tab_despac_despac b ON a.num_despac = b.num_despac
				 					  WHERE 1 = 1
				 					  	AND a.cod_transp = '".$mData['cod_transp']."'
				 					  	AND a.ind_activo = 'S'   
				 					  	AND b.ind_planru = 'S'
				 					  	AND b.ind_anulad = 'R'
				 					  	AND b.fec_salida IS NOT NULL
				 					  	AND b.fec_llegad IS NULL
				 				   GROUP BY b.num_despac
				) a ";
				//echo "<pre class='cellInfo1' style='color: blue;'>ajustado"; print_r($sql); echo "</pre>"; 
			$consult = new Consulta($sql, $this -> conexion );
			$mCantidad = $consult->ret_matrix('a');
			return $mCantidad[0]['can_despac'];
		} 
		catch (Exception $e) 
		{
			
		}
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
        $mQuery = "UPDATE ".BASE_DATOS.".tab_genera_viasxx
                      SET ind_estado = $estado,
                          usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                          fec_modifi = NOW()
                    WHERE cod_viasxx = ".$_REQUEST['cod_viasxx'];
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
        $codigo = empty($_REQUEST['cod_viasxx']) ? '' : "cod_viasxx = ".$_REQUEST['cod_viasxx'].", ";
        //Consulta
	    $mQuery = "INSERT INTO  ".BASE_DATOS.".tab_genera_viasxx
	                       SET  $codigo
                              nom_viasxx= '".$_REQUEST['nom_viasxx']."',
                              ind_estado= '1',
                              usr_creaci= '".$_SESSION['datos_usuario']['cod_usuari']."',
                              fec_creaci= NOW()
	   ON DUPLICATE KEY UPDATE nom_viasxx= '".$_REQUEST['nom_viasxx']."',
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
        echo 'Excepción updEst: ',  $e->getMessage(), "\n";
      }
    }

    function editForm(){
      try {
        $cod = $_REQUEST['cod'];
        $mQuery = "SELECT * FROM ".BASE_DATOS.".tab_genera_viasxx a WHERE a.cod_viasxx='$cod'";
        $consulta = new Consulta($mQuery, $this -> conexion); 
        $dato = $consulta->ret_matriz("a")[0];

        $html = '<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editService">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center header-modal">
                EDITAR VIA
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
                            <input class="form-control form-control-sm" type="text" placeholder="Nombre de la via" id="nom_viasxxEID" name="nom_viasxx" value="'.$dato['nom_viasxx'].'" required>
                        </div>
                    </div>
                   
                    <input type="hidden" name="cod_viasxx" value="'.$cod.'">
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