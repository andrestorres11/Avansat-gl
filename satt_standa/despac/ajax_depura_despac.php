<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_genera_parcor
{
  var $AjaxConnection;
  
  public function __construct()
  {
    include('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');
    include_once('../lib/general/functions.inc');

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
      case 'setModal':
        $this->setModal();
        break;  
      case 'generarReporte':
        $this->generarReporte();
        break; 
    }

  }

  //---------------------------------------------
  /*! \fn: setRegistros
  *  \brief:Retorna los registros para tabla inicial para el dataTable
  *  \author: Ing. Carlos Nieto
  *  \date: 02/06/2021
  *  \date modified: 
  *  \return HTML
  */
  private function setRegistros() {
    $mSql = " SELECT cod_bitaco,nit_transp,tercer.nom_tercer,fec_agrupa,COUNT(cod_agrupa) as cod_agrupa,tab_bitaco_depdes.usr_creaci , cod_agrupa as  cod_agrupa_num
              FROM `tab_bitaco_depdes` LEFT JOIN tab_tercer_tercer as tercer ON tab_bitaco_depdes.nit_transp = tercer.cod_tercer 
              GROUP BY tab_bitaco_depdes.cod_agrupa ORDER BY cod_bitaco DESC LIMIT 10";
              
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {
    		if($campo == "cod_agrupa"){
                $html = '<a style="color:blue;" onclick="abrModalDetalles('.$valor.','.$datos['cod_agrupa_num'].',/'.$datos['nom_tercer'].'/)">'.$valor.'</a>';
          		$data[$key][] = $html;	
    		}else{
    			$data[$key][] = $valor;	
    		}
    		
    	}
    }

    $return = array("data" => $data);
    echo json_encode($return);
  }

  //---------------------------------------------
  /*! \fn: generarReporte
  *  \brief:Retorna los registros con los datos solicitados por el usuario para el dataTable inical
  *  \author: Ing. Carlos Nieto
  *  \date: 02/06/2021
  *  \date modified: 
  *  \return HTML
  */

  private function generarReporte() {
    if($_REQUEST['data'][0]['value'] != 'Seleccione')//si el usuario no selecciona una empresa
    {
      $mSql = " SELECT cod_bitaco,nit_transp,tercer.nom_tercer,fec_agrupa,COUNT(cod_agrupa) as cod_agrupa,tab_bitaco_depdes.usr_creaci , cod_agrupa as  cod_agrupa_num
      FROM `tab_bitaco_depdes` LEFT JOIN tab_tercer_tercer as tercer ON tab_bitaco_depdes.nit_transp = tercer.cod_tercer 
      WHERE tab_bitaco_depdes.nit_transp = '".$_REQUEST['data'][0]['value']."'
      and tab_bitaco_depdes.fec_agrupa  >=  '".$_REQUEST['data'][1]['value']."' 
      and  tab_bitaco_depdes.fec_agrupa  <= DATE_ADD('".$_REQUEST['data'][2]['value']."', INTERVAL 1 DAY)
      GROUP BY tab_bitaco_depdes.cod_agrupa";
    }else{
      $mSql = " SELECT cod_bitaco,nit_transp,tercer.nom_tercer,fec_agrupa,COUNT(cod_agrupa) as cod_agrupa,tab_bitaco_depdes.usr_creaci , cod_agrupa as  cod_agrupa_num
      FROM `tab_bitaco_depdes` LEFT JOIN tab_tercer_tercer as tercer ON tab_bitaco_depdes.nit_transp = tercer.cod_tercer 
      WHERE tab_bitaco_depdes.fec_agrupa  >=  '".$_REQUEST['data'][1]['value']."' 
      and  tab_bitaco_depdes.fec_agrupa  <= DATE_ADD('".$_REQUEST['data'][2]['value']."', INTERVAL 1 DAY)
      GROUP BY tab_bitaco_depdes.cod_agrupa";
    }
              
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {
    		if($campo == "cod_agrupa"){
          $html = '<a style="color:blue;" onclick="abrModalDetalles('.$valor.','.$datos['cod_agrupa_num'].',/'.$datos['nom_tercer'].'/)">'.$valor.'</a>';
          $data[$key][] = $html;	
    		}else{
    			$data[$key][] = $valor;	
    		}
    		
    	}
    }

    $return = array("data" => $data);
    echo json_encode($return);
  }

  //---------------------------------------------
  /*! \fn: setModal
  *  \brief:Retorna los registros de bitacora para el dataTable del modal
  *  \author: Ing. Carlos Nieto
  *  \date: 02/06/2021
  *  \date modified: 
  *  \return HTML
  */
  private function setModal() {
    $mSql = " SELECT a.cod_bitaco, a.num_despac, b.cod_manifi, c.num_desext, d.nom_ciudad as ori, e.nom_ciudad as des, g.nom_tercer, f.num_placax,
    CONCAT(h.cod_tercer, ' - ' , h.abr_tercer) as conductor, h.num_telef1, IF(j.nom_tercer IS NULL, 'N/A', IF(i.cod_activi =".COD_FILTRO_CLIENT.",j.nom_tercer,'N/A')) AS generador,
    b.obs_llegad, b.fec_llegad, a.usr_creaci
    FROM tab_bitaco_depdes a
    LEFT JOIN tab_despac_despac b
    ON a.num_despac = b.num_despac
    LEFT JOIN tab_despac_sisext c
    ON a.num_despac = c.num_despac
    LEFT JOIN tab_genera_ciudad d
    ON b.cod_ciuori = d.cod_ciudad
    LEFT JOIN tab_genera_ciudad e
    ON b.cod_ciudes = e.cod_ciudad
    LEFT JOIN tab_despac_vehige f
    ON a.num_despac = f.num_despac
    LEFT JOIN tab_tercer_tercer g
    ON f.cod_transp = g.cod_tercer
    LEFT JOIN tab_tercer_tercer h
    ON f.cod_conduc = h.cod_tercer
    LEFT JOIN tab_tercer_activi i
    ON b.cod_client = i.cod_tercer
    LEFT JOIN tab_tercer_tercer j
    ON i.cod_tercer = j.cod_tercer
    WHERE cod_agrupa = ".$_REQUEST['data']."";
              
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = $mMatriz->ret_matrix("a");
    $data = [];
    //se organiza la informacion de la consulta segun se necesita en la vista
    foreach ($mMatriz as $key => $datos) {
      $mUlPCNov = getNovedadesDespac( $this->conexion, $datos['num_despac'], 2 );
    	foreach ($datos as $campo => $valor) {
          if($campo == 'cod_bitaco'|| $campo == 'num_despac'|| $campo == 'cod_manifi'|| $campo == 'num_desext'|| $campo == 'ori'
          || $campo == 'des'|| $campo == 'nom_tercer'|| $campo == 'num_placax'|| $campo == 'conductor'|| $campo == 'num_telef1'
          || $campo == 'generador')
          {
            $data[$key][] = $valor;	
          }
    	}

      $data[$key][] = $mUlPCNov['nom_noveda'];
      $data[$key][] = $mUlPCNov['obs_noveda'];
      $data[$key][] = $mUlPCNov['fec_noveda'];

      foreach ($datos as $campo => $valor) {
        if($campo == 'obs_llegad'|| $campo == 'fec_llegad'|| $campo == 'usr_creaci')
        {
          $data[$key][] = $valor;	
        }
      }
      
    }

    $return = array("data" => $data);
    echo json_encode($return);
  }


  /*! \fn: delReg
   *  \brief: administracion segun parametros del depurar
   *  \author: Ing. Carlos Nieto
   *  \date: 02/06/2020
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return: json
   */
    function delReg(){
      try {
        //Varibales Necesarias
        $return = [];
        foreach ($_REQUEST['cod_regist'] as $key => $value) {
          if($value["name"] == 'empresa')
          {
            $empresaId = $value["value"];
          }elseif ($value["name"] == 'fechaHasta') {
            $fechaHasta = $value["value"];
          }elseif ($value["name"] == 'ruta') {
            $ruta = $value["value"];
          }elseif ($value["name"] == 'despachos') {
            $despachos = $value["value"]; 
          }elseif ($value["name"] == 'descripcion') {
            $descripcion = $value["value"];
          }
        };

        $consulta = $this->consultarDepuracion($descripcion,$fechaHasta,$empresaId,$ruta,$despachos);
     
        if($consulta == 1){
          $return['status'] = 200;
          $return['response'] = 'Los registros se han depurado correctamente.';
        }elseif ($consulta == 2) {
          $return['status'] = 500;
          $return['response'] = 'Error al depurar.';
        }elseif ($consulta == 3){
          $return['status'] = 200;
          $return['response'] = 'Nada que depurar.';
        }

        //Devuelve estatus de la consulta
        echo json_encode($return);
      } catch (Exception $e) {
        echo 'ExcepciÃ³n delReg: ',  $e->getMessage(), "\n";
      }
    }

    /*! \fn: consultarDepuracion
   *  \brief: Realiza la consulta dde los parametros a depurar y segun los paramettros pasados escoge la opcion a ejecutar
   *  \author: Ing. Carlos Nieto
   *  \date: 02/06/2020
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return: json
   */
    
    function consultarDepuracion($descripcion,$fechaHasta,$empresaId,$ruta,$despachos) {
      try{
        $mSql = "SELECT a.num_despac, b.cod_transp
        FROM tab_despac_despac a
        INNER JOIN tab_despac_vehige b ON a.num_despac = b.num_despac
        WHERE
        a.fec_salida IS NOT NULL
        AND DATE(a.fec_salida) < '".$fechaHasta."'
        AND a.fec_llegad IS NULL
        AND a.ind_planru = 'S'
        AND a.ind_anulad = 'R'
        AND b.ind_activo = 'S'
        AND b.cod_transp = '".$empresaId."'"; 
        
        $query = new Consulta($mSql, $this->conexion);
        $respuestas = $query -> ret_matrix('a');

        if(count($respuestas) > 0)
        {
          
          if($ruta != null && $despachos != null)
          {
            $res = $this->ejecutarDepuracionDespacho($descripcion,$fechaHasta,$empresaId,$respuestas,true);
            $res = $this->ejecutarDepuracionRuta($descripcion,$fechaHasta,$empresaId);//ejecuta la depuracion de ruta
            $this->insertarBitacoraDepuracion($respuestas);//guarda en historial 
          }elseif ($ruta == null && $despachos == null) {
            $res = 2;
          }elseif ($ruta != null) {
            $res = $this->ejecutarDepuracionRuta($descripcion,$fechaHasta,$empresaId);//ejecuta la depuracion de ruta
            $this->insertarBitacoraDepuracion($respuestas);//guarda en historial 
          }elseif ($despachos != null) {
            $res = $this->ejecutarDepuracionDespacho($descripcion,$fechaHasta,$empresaId,$respuestas,false);
          }
          
        }else{
          $res = 3; //estado nada que depurar
        }

        return $res; 

      }catch(Exception $e){
        return 2;//estado error 500 
      }
    }

/*! \fn: ejecutarDepuracionRuta
   *  \brief: ejecuta la depuracion cuando se seleciona por ruta
   *  \author: Ing. Carlos Nieto
   *  \date: 02/06/2020
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return: json
   */
    function ejecutarDepuracionRuta ($descripcion,$fechaHasta,$empresaId) {
      try{
        $mSql = "UPDATE tab_despac_despac a
        INNER JOIN tab_despac_vehige b ON a.num_despac = b.num_despac
        SET a.fec_llegad = NOW(),
        a.fec_modifi = NOW(),
        a.usr_modifi = '".$_SESSION['datos_usuario']['nom_usuari']."',
        a.obs_llegad = '".$descripcion."'
        WHERE
        a.fec_salida IS NOT NULL
        AND DATE(a.fec_salida) < '".$fechaHasta."'
        AND a.fec_llegad IS NULL
        AND a.ind_planru = 'S'
        AND a.ind_anulad = 'R'
        AND b.ind_activo = 'S'
        AND b.cod_transp = '".$empresaId."'"; 
        
        $query = new Consulta($mSql, $this->conexion);

        return 1; //estado depurado con exito

      }catch(Exception $e){
        return 2;//estado error 500 
      }
    }

    /*! \fn: insertarBitacoraDepuracion
   *  \brief: inserta los datos consultados a las tablas de bitacoras de depuracion,
   *  \author: Ing. Carlos Nieto
   *  \date: 02/06/2020
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return: json
   */
    function insertarBitacoraDepuracion ($arrayConsulta) {
      try{
        $cod_transp = $arrayConsulta[0]['cod_transp'];//nit de transportadora.

      $mSql = "INSERT INTO ".BASE_DATOS.".tab_agrupa_depdes 
      (usr_agrupa,fec_agrupa,nit_transp,usr_creaci,fec_creaci) 
      VALUES (
        '".$_SESSION['datos_usuario']['nom_usuari']."',
         NOW(),
        '".$cod_transp."', 
        '".$_SESSION['datos_usuario']['nom_usuari']."',
        NOW())"; 
      
      $query = new Consulta($mSql, $this->conexion);
      $lastId = mysql_insert_id();//Obtiene el ultimo id ingresado con el insert.

      foreach ($arrayConsulta as $key => $value) {

        $mySql = "INSERT INTO ".BASE_DATOS.".tab_bitaco_depdes
        (num_despac,cod_agrupa,fec_agrupa,nit_transp,usr_creaci,fec_creaci)
        VALUES (
          '".$value['num_despac']."',
          '".$lastId."',
          NOW(),
          '".$cod_transp."',
          '".$_SESSION['datos_usuario']['nom_usuari']."',
          NOW())"; 
        
        $query = new Consulta($mySql, $this->conexion); 

      }
      return 1; //estado depurado con exito

      }catch(Exception $e){
        return 2;//estado error 500 
      }
  }

   /*! \fn: ejecutarDepuracionDespacho
   *  \brief: ejecuta la depuracion cuando se seleciona por depuracion
   *  \author: Ing. Carlos Nieto
   *  \date: 02/06/2020
   *  \date modified: dia/mes/aÃ±o
   *  \param: 
   *  \return: json
   */
    function ejecutarDepuracionDespacho($descripcion,$fechaHasta,$empresaId,$respuestas,$boolean) {
      try{
        $arrayParaBitacora = [];
        foreach ($respuestas as $key => $value) {

          $res = getNextPC($this->conexion,  $value['num_despac']);

          if(isset($res['ind_finrut']))
          {
            $mSql = "UPDATE tab_despac_despac a
            SET a.fec_llegad = NOW(),
            a.fec_modifi = NOW(),
            a.usr_modifi = '".$_SESSION['datos_usuario']['nom_usuari']."',
            a.obs_llegad = '".$descripcion."'
            WHERE a.num_despac = '".$value['num_despac']."'"; 

            array_push($arrayParaBitacora,$value);
            
            $query = new Consulta($mSql, $this->conexion);          
          }
        }
        if(count($arrayParaBitacora) > 0 && $boolean == false)//si el array tiene datos y no se ha guardado en la bitacora
        {
          $res = $this->insertarBitacoraDepuracion($arrayParaBitacora);//guarda en historial 
        }else{
          $res = 3; //estado nada que depurar
        }
        return $res;

      }catch(Exception $e){
        return 2;//estado error 500 
      }
    }

    
  
}

$proceso = new ajax_genera_parcor();
 ?>