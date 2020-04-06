<?php
/****************************************************************************
NOMBRE:   Clase de suspensiones
FUNCION:  PROVEE LAS FUNCIONES NECESARIAS PARA EL MANEJO DE LA INFORMACION
          DE LOS SUSPENDIDOS
FECHA DE MODIFICACION: 25/02/2020
CREADO POR: Ing. Luis Carlos Manrique
MODIFICADO POR: Luis Carlos Manrique

****************************************************************************/
/*ini_set('error_reporting', E_ALL);
ini_set("display_errors", 1);*/
class suspensiones {
  //Variables de clase
  var $AjaxConnection;   

  function __construct()
  {
    if(isset($_REQUEST['cod_tercer'])){
      $this->SetSuspensiones();
    }
    
  }


  /*! \fn: SetSuspensiones
   *  \brief: Consume el API generado para consultar suspenciones
   *  \author: Ing. Luis Manrique
   *  \date: 27-02-2020
   *  \date modified: dd/mm/aaaa
   *  \param: $cod_tercer = Nit
   *          $cod_usuari = usuario de sesión
   *  \return: array u json
   */
  function SetSuspensiones($cod_tercer = null, $cod_usuari = null, $eje_noveda = null, $ajax = null)
  {
  	if($_SERVER['SERVER_NAME'] == 'ut.intrared.net'){
  		$urlWS = "https://ut.intrared.net/ap/consultor/app/client/fact_vencida_faro.php";
  	}else{
  		$urlWS = "https://dev.intrared.net:8083/ap/cmaya/ut/consultor/app/client/facturacionVencida/fact_vencida_faro.php";
  	}
	

    //Captura el codigo del tercero por Request o por parametro.
    $cod_tercer = isset($_REQUEST['cod_tercer']) ? $_REQUEST['cod_tercer'] : $cod_tercer;
    //Valida codigo de tercero - Usuario de sesión.
    if(!is_null($cod_tercer)){

      chdir(__DIR__."/../");
      include_once '../lib/general/constantes.inc';
      include_once '../lib/ajax.inc';
      include_once '../lib/general/festivos.php';
      $this -> conexion = $AjaxConnection;

      //Consume Api con parametros
        $dataTerceros = json_decode(file_get_contents($urlWS.'?cod_tercero='.$cod_tercer), true);
    }else if(!is_null($cod_usuari)){
      include '../'.DIR_APLICA_CENTRAL.'/lib/ajax.inc';
      include_once '../'.DIR_APLICA_CENTRAL.'/lib/general/festivos.php';
      $this -> conexion = $AjaxConnection;

      //Se crea la consulta para traer el Nit
      $sql =  "SELECT   * 
                 FROM   ".BASE_DATOS.".tab_aplica_filtro_usuari ".
               "WHERE   cod_usuari = '".$cod_usuari."'";

      //Ejecuta la consulta
      $consulta = new Consulta( $sql, $this -> conexion );
      $cod_tercer = $consulta->ret_matrix( 'a' );

      //Consume Api con parametros
      $dataTerceros = json_decode(file_get_contents($urlWS.'?cod_tercero='.$cod_tercer[0]['clv_filtro']), true);

    }else if(!is_null($ajax)){
        chdir(__DIR__."/../");
        include '../lib/ajax.inc';
        include_once '../lib/general/festivos.php';

        //Consume Api de toda la data
        $dataTerceros = json_decode(file_get_contents($urlWS), true);
    }else{
      include '../'.DIR_APLICA_CENTRAL.'/lib/ajax.inc';
      include_once '../'.DIR_APLICA_CENTRAL.'/lib/general/festivos.php';
      $this -> conexion = $AjaxConnection;

      //Consume Api de toda la data
      $dataTerceros = json_decode(file_get_contents($urlWS), true);
    }

    //Codifica en Hson
    $raw_data = json_encode($dataTerceros);

    //Codifica en Array
    $cReturn = json_decode($raw_data, true);

    //Se recorre el arreglo
    foreach ($cReturn as $key => $value) {

      //Valida si la factura tiene dias en prologa, para asignarlos a la fehca vencimiento
      if(!is_null($value['dias_prorro'])){
        $cReturn[$key]['fec_vencin'] = date("Y-m-d",strtotime($value['fec_vencin']."+ ".$value['dias_prorro']." days")); 
      }

      //Valida si tiene nota contable, si la tiene, le resta el valor y del recaudo al valor total
      if(!is_null($value['nota_contable'])){
        $cReturn[$key]['val_totalx'] = $value['val_totalx']-$value['val_recaud']-$value['nota_contable']; 
      }

      //Recorre los campos
      foreach ($value as $keyCampo => $valueCampo) {
        
        if($keyCampo == 'fec_vencin'){

          //Asigna los dias que se asigna para hacer el pago
          $cReturn[$key]['fec_vencin'] = date("Y-m-d",strtotime($value['fec_vencin']."+ 90 days")); 

          //Valida dias festivos
          $cReturn[$key]['fec_vencin'] = $this->valFest($cReturn[$key]['fec_vencin']);
          $cReturn[$key]['dia'] = date("D", strtotime($cReturn[$key]['fec_vencin']));

          //Valida fines de semana para correr los dias
          if($cReturn[$key]['dia'] == 'Sun'){
            $cReturn[$key]['fec_vencin'] = date("Y-m-d",strtotime($cReturn[$key]['fec_vencin']."+ 1 days"));
          }elseif($cReturn[$key]['dia'] == 'Sat'){
            $cReturn[$key]['fec_vencin'] = date("Y-m-d",strtotime($cReturn[$key]['fec_vencin']."+ 2 days"));
          }

          //Valida dias festivos
          $cReturn[$key]['fec_vencin'] = $this->valFest($cReturn[$key]['fec_vencin']);

        }         
      }
    }

    //Identifica que caso aplica para ser suspendido o por suspender
    $segSusp = [];
    foreach ($cReturn as $key => $value) {
      if($value['fec_vencin']." 15:00:00" < date('Y-m-d H:i:s')){
        $segSusp['suspendido'][] = $value;
      }else{
        $segSusp['porSuspender'][] = $value;
      }
    }

    //Valida Estado de la novedad para ejecutar
    if ($eje_noveda == 1) {
      //Valida las novedades que reporta Aviso de suspensión y servicio suspendido
      $this->novDespachosAS_SS($segSusp);

      //Valida las novedades que Avivasión del servicio
      $this->novDespachosSA($segSusp);
    }

    //Retorna la data dependiendo de la solicitud
    if(isset($_REQUEST['cod_tercer'])){
      echo json_encode($segSusp, true);
    }else{
      return $segSusp;
    }
  }

  /*! \fn: valFest
   *  \brief: Valida de forma recursiva los festivos
   *  \author: Ing. Luis Manrique
   *  \date: 02-03-2020
   *  \date modified: dd/mm/aaaa
   *  \param: $fecha = fecha a validar
   *  \return: date
   */

  function valFest($fecha){
    $yea = date("Y", strtotime($fecha)); 
    $mon = date("m", strtotime($fecha));
    $day = date("d", strtotime($fecha));
    $festivo = new Festivos($yea);
    if($festivo->esFestivo($day,$mon)){ 
      $fecha = date("Y-m-d",strtotime($fecha."+ 1 days"));
      $fecha = $this->valFest($fecha);
    }
    return  $fecha;
  }

  /*! \fn: novDespachosAS_SS
   *  \brief: Valida los terceros que tengan la novedad de Aviso de suspensión y Servicio Suspendido (9271 o 9272)
   *  \author: Ing. Luis Manrique
   *  \date: 10-03-2020
   *  \date modified: dd/mm/aaaa
   *  \param: $fecha = fecha a validar
   *  \return: 
   */

  function novDespachosAS_SS($data){
    //Array necesario
    $cod_tercer = [];
    $est_suspen = [];

    //Recorre la data para dejar la factura más antigua por si hay más de una factura
    foreach ($data as $estado => $empresas) {
      foreach ($empresas as $ident => $campos) {
        if(!array_search($campos['cod_tercer'], $cod_tercer)){
          //Llena arreglo para codigos de terceros
          $cod_tercer[] = $campos['cod_tercer'];
          //Llena arreglo donde la llave es el cod  del tercero y el valor es el estado
          $est_suspen[$campos['cod_tercer']] = $estado;
        }
      }
    }

    //Consulta los codigos de terceros

    $mSql =  "SELECT  a.num_despac,
                      b.cod_transp
                FROM  ".BASE_DATOS.".tab_despac_despac a
          INNER JOIN  ".BASE_DATOS.".tab_despac_vehige b 
                  ON  a.num_despac = b.num_despac 
                      AND b.cod_transp IN(".join(",", $cod_tercer).")
                      AND a.fec_salida IS NOT NULL 
                      AND a.fec_salida <= NOW() 
                      AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00') 
                      AND a.ind_planru = 'S' 
                      AND a.ind_anulad in ('R') ";

    //Ejecuta la consulta
    $consulta = new Consulta( $mSql, $this -> conexion );
    $num_despac = $consulta->ret_matrix( 'a' );

    //Recorre los despachos que retornó la empresa
    foreach ($num_despac as $ident => $campo) {

      //Consulta para los despachos que presentan novedad 9271 o 9272
      $mSql =  "SELECT  a.num_despac,
                        a.cod_noveda,
                        DATE(a.fec_creaci) AS fec_creaci
                  FROM  ".BASE_DATOS.".tab_despac_contro a
                 WHERE  a.num_despac  = ".$campo['num_despac']."
                        AND a.cod_noveda IN (9271,9272) 
              ORDER BY  a.fec_creaci DESC
                 LIMIT  1";


      //Ejecuta la consulta
      $consulta = new Consulta( $mSql, $this -> conexion );
      $cod_noveda = $consulta->ret_matrix( 'a' );

      //Envia los datos a la función para validar si insertó una novedad
      $insert = $this->valRegNovSS($cod_noveda, $est_suspen[$campo['cod_transp']], $campo['cod_transp']);

      //Valida si insertó una novedad
      if($insert == 0){

    	//Consulta para los despachos que no presentan novedad para aplicar algun anuncio
      $mSql =  "SELECT  a.num_despac
                  FROM  ".BASE_DATOS.".tab_despac_contro a
                 WHERE  a.num_despac  = ".$campo['num_despac']."
                        AND a.cod_noveda IN (9271,9272)
                        AND DATE(a.fec_creaci) BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND CURDATE()
              ORDER BY  a.fec_creaci DESC";

        //Ejecuta la consulta
        $consulta = new Consulta( $mSql, $this -> conexion );
        $cod_noveda = $consulta->ret_matrix( 'a' );

        //Valida si la consulta no trae resultados para crea la novedad
        if (count($cod_noveda) == 0){
	      	//Envia los datos a la función valRegNovAS par registrar la novedad correspondiente
	       	$this->valRegNovAS($campo['num_despac'], $est_suspen[$campo['cod_transp']], $campo['cod_transp']);
        }
      }      
    }
  }


  /*! \fn: novDespachosSA
   *  \brief: Valida los terceros que tengan la novedad de Aviso de suspensión y Servicio Suspendido (9271 o 9272)
   *  \author: Ing. Luis Manrique
   *  \date: 11-03-2020
   *  \date modified: dd/mm/aaaa
   *  \param: $fecha = fecha a validar
   *  \return: 
   */


  function novDespachosSA($data){
    //Array necesario
    $cod_tercer = [];
    $est_suspen = [];
    //Recorre la data para dejar la factura más antigua por si hay más de una factura
    foreach ($data as $estado => $empresas) {
      foreach ($empresas as $ident => $campos) {
        if(!array_search($campos['cod_tercer'], $cod_tercer)){
          $cod_tercer[] = $campos['cod_tercer'];
          $est_suspen[$campos['cod_tercer']] = $estado;
        }
      }
    }

    //Consulta los codigos de terceros
    $mSql =  "SELECT  a.num_despac,
                      b.cod_transp
                FROM  ".BASE_DATOS.".tab_despac_despac a
          INNER JOIN  ".BASE_DATOS.".tab_despac_vehige b 
                  ON  a.num_despac = b.num_despac 
                      AND b.cod_transp NOT IN(".join(",", $cod_tercer).")
                      AND a.fec_salida IS NOT NULL 
                      AND a.fec_salida <= NOW() 
                      AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00') 
                      AND a.ind_planru = 'S' 
                      AND a.ind_anulad in ('R') 
          INNER JOIN  ".BASE_DATOS.".tab_despac_contro c 
                  ON  a.num_despac = c.num_despac 
                      AND c.cod_noveda IN (9271,9272) 
                      AND DATE(c.fec_creaci) BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND CURDATE()
            ORDER BY  c.fec_creaci DESC
            ";   

    //Ejecuta la consulta
    $consulta = new Consulta( $mSql, $this -> conexion );
    $num_despac = $consulta->ret_matrix( 'a' );

     

    //Valida si tiene información
    if(count($num_despac) > 0){
    	//Recorre los despachos que retornó la empresa
	    foreach ($num_despac as $ident => $campo) {

	    	$mSql =  "SELECT  a.num_despac
		                FROM  ".BASE_DATOS.".tab_despac_contro a 
		               WHERE  a.num_despac = ".$campo['num_despac']." 
		                      AND a.cod_noveda IN (9273) 
		                      AND DATE(a.fec_creaci) BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND CURDATE()
		            ";   

		    //Ejecuta la consulta
		    $consulta = new Consulta( $mSql, $this -> conexion );
		    $num_despac = $consulta->ret_matrix( 'a' );

		    //Valida si la consulta no trae resultados para crea la novedad
	        if (count($num_despac) == 0){
		      	//Envia los datos a la función valRegNovSA par registrar la novedad correspondiente
		       	$this->valRegNovSA($campo['num_despac'],$campo['cod_transp']);
	        }
	    }
    }
  }

  /*! \fn: valRegNovSS
   *  \brief: Valida que novedad aplica para ser insertada en los despachos con novedades de suspensión creadas en dias anteriores
   *  \author: Ing. Luis Manrique
   *  \date: 11-03-2020
   *  \date modified: dd/mm/aaaa
   *  \param: $data = información del despacho
   *		  $estado = Estado de la empresa
   *  \return: boleano
   */

  function valRegNovSS($data, $estado, $cod_tercer){

  	//Valida si tiene información
    if (count($data) > 0){

    	//Variable necesaria
	    $values = [];

	    //Recorre la data
	    foreach ($data as $key => $value) {

	    	//Valida si el registro es diferente a hoy para seguir el proceso
	    	if($value['fec_creaci'] != date("Y-m-d")){

	    		//Identifica el tipo de novedad y de Observación dependiendo del estado de la empresa
		       	$novedad = $estado == 'porSuspender' ? 9271 : 9272;
		       	$observ = $estado == 'porSuspender' ? utf8_decode("AV - AVISO DE SUSPENSIÓN.") : utf8_decode("AV - SUSPENSIÓN DEL SERVICIO.");

		       	//Valida datos adicionales para hacer el registro de la novedad
		    	$dContro = $this->setDatosContro($value["num_despac"]);

		    	//Llena la posición de la inserción
		        $values[] = '('.$value["num_despac"].', 
		       				 '.$dContro['cod_contro'].', 
		       				 '.$dContro['cod_rutasx'].', 
		       				 '.$dContro['cod_consec'].', 
		       				 "'.$observ.'",
		       				 '.$novedad.',
		       				 0,
		       				 NOW(),
		       				 124788,
		       				 "InterfSuspen",
		       				 NOW())';
		    }
	    }

	    //Si hay registros en valores realiza la inserción de la novedad
	    if(count($values) > 0){
		    $this->insertNovedaS($values, $cod_tercer, $value["num_despac"]);
		    return 1;
  		}else{
  			return 0;
  		}
  	}else{
  		return 0;
  	}
  }


  /*! \fn: valRegNovAS
   *  \brief: Valida que novedad aplica para ser insertada en los despachos sin novedades de suspensión creadas
   *  \author: Ing. Luis Manrique
   *  \date: 11-03-2020
   *  \date modified: dd/mm/aaaa
   *  \param: $data = información del despacho
   *		  $estado = Estado de la empresa
   *  \return: boleano
   */

  function valRegNovAS($num_despac, $estado, $cod_tercer){

  	//Variable necesaria
    $values = [];

    //Identifica el tipo de novedad y de Observación dependiendo del estado de la empresa
   	$novedad = $estado == 'porSuspender' ? 9271 : 9272;
   	$observ = $estado == 'porSuspender' ? utf8_decode("AV - AVISO DE SUSPENSIÓN.") : utf8_decode("AV - SUSPENSIÓN DEL SERVICIO.");

   	//Valida datos adicionales para hacer el registro de la novedad
	  $dContro = $this->setDatosContro($num_despac);

	  //Llena la posición de la inserción
    $values[] = '('.$num_despac.', 
   				 '.$dContro['cod_contro'].', 
   				 '.$dContro['cod_rutasx'].', 
   				 '.$dContro['cod_consec'].', 
   				 "'.$observ.'",
   				 '.$novedad.',
   				 0,
   				 NOW(),
   				 124788,
   				 "InterfSuspen",
   				 NOW())';
	    
    //Si hay registros en valores realiza la inserción de la novedad
    if(count($values) > 0){
	   $this->insertNovedaS($values, $cod_tercer, $num_despac);
	  }
  }

  /*! \fn: valRegNovAS
   *  \brief: Valida que novedad aplica para ser insertada en los despachos sin novedades de suspensión creadas
   *  \author: Ing. Luis Manrique
   *  \date: 11-03-2020
   *  \date modified: dd/mm/aaaa
   *  \param: $data = información del despacho
   *		  $estado = Estado de la empresa
   *  \return: boleano
   */

  function valRegNovSA($num_despac, $cod_tercer){

  	//Variable necesaria
    $values = [];

   	//Valida datos adicionales para hacer el registro de la novedad
  	$dContro = $this->setDatosContro($num_despac);

  	//Llena la posición de la inserción
      $values[] = '('.$num_despac.', 
           				 '.$dContro['cod_contro'].', 
           				 '.$dContro['cod_rutasx'].', 
           				 '.$dContro['cod_consec'].', 
           				 "'.utf8_decode("AV - SUSPENSIÓN RETIRADA.").'",
           				 9273,
           				 0,
           				 NOW(),
           				 124788,
           				 "InterfSuspen",
           				 NOW())';
  	    
      //Si hay registros en valores realiza la inserción de la novedad
      if(count($values) > 0){
  	   $this->insertNovedaS($values, $cod_tercer, $num_despac);
  	}
  }

  /*! \fn: setDatosContro
   *  \brief: Retorna la información de control, consecutivo y ruta
   *  \author: Ing. Luis Manrique
   *  \date: 11-03-2020
   *  \date modified: dd/mm/aaaa
   *  \param: $num_despac = Número del despacho
   *  \return: Array
   */

  function setDatosContro($num_despac){

  	//Variable necesaria
  	$return = [];

  	//Consulta para identificar consecutivo
  	$mSql = "SELECT (MAX( a.cod_consec ) + 1) AS cod_consec
                       FROM ".BASE_DATOS.".tab_despac_contro a,
                            ".BASE_DATOS.".tab_despac_vehige b
                      WHERE a.num_despac = b.num_despac AND
                            a.cod_rutasx = b.cod_rutasx AND
                            b.num_despac = ".$num_despac;

    $consulta = new Consulta( $mSql, $this -> conexion );
  	$cod_consec = $consulta->ret_matrix( 'a' );

  	//Identifica si no retorna datos para asignale un valor
  	$return['cod_consec'] = count($cod_consec[0]['cod_consec']) > 0 ? $cod_consec[0]['cod_consec'] : 1;

  	//Consulta para traer información del codigo de control y codigo de ruta
      $mSql = " SELECT a.cod_contro,
      				 a.cod_rutasx,
      				 c.nom_contro
                  FROM ".BASE_DATOS.".tab_despac_seguim a
            INNER JOIN ".BASE_DATOS.".tab_despac_vehige b
              	    ON a.num_despac = b.num_despac 
              	       AND b.num_despac = ".$num_despac."
            INNER JOIN ".BASE_DATOS.".tab_genera_contro c
            		    ON a.cod_contro = c.cod_contro
                 WHERE a.ind_estado = 1 
              ORDER BY a.fec_creaci ASC
                 LIMIT 1";
                      
      $consulta = new Consulta( $mSql, $this -> conexion );
  	$cod_conrut = $consulta->ret_matrix( 'a' );

  	//Asigna el valor al arreglo para retornarlo
  	$return['cod_contro'] = $cod_conrut[0]['cod_contro'];
  	$return['nom_contro'] = $cod_conrut[0]['nom_contro'];
  	$return['cod_rutasx'] = $cod_conrut[0]['cod_rutasx'];

  	return $return;
  }

  /*! \fn: insertNovedaS
   *  \brief: Genera la inserción de la novedad
   *  \author: Ing. Luis Manrique
   *  \date: 11-03-2020
   *  \date modified: dd/mm/aaaa
   *  \param: $values = Array con los valores a insertar
   *  \return:
   */

  function insertNovedaS($values, $cod_tercer, $fNumDespac){

    //Valida si tiene enlace con faro
    $interf = $this->getInterfParame(50, $cod_tercer);
    if($interf['status']){

      //Datos Necesarios para el envio de WS
      $dataInterf = $this->getDataInterf($cod_tercer, $values);

      //Instancia SOAPCL para poder consumir el WS
      $client = new SoapClient($dataInterf['url'], array("trace" => 1,'encoding' => 'ISO-8859-1',"stream_context" => stream_context_create(array('ssl' => array('verify_peer' => false,'allow_self_signed' => true,),)),)); 

      //Se crea el arreglo para el envio del WS
      $send = array("nom_usuari" => $interf['data']['nom_usuari'],
                    "pwd_clavex" => $interf['data']['clv_usuari'],
                    "nom_aplica" => $interf['data']['nom_operad'],
                    "num_manifi" => $dataInterf['cod_manifi'],
                    "num_placax" => $dataInterf['num_placax'], 
                    "cod_novbas" => 0,
                    "cod_conbas" => $dataInterf['cod_pcxbas'],
                    "tim_duraci" => 0,
                    "fec_noveda" => date('Y-m-d H:i'),
                    "des_noveda" => $dataInterf["des_noveda"], 
                    "nom_contro" => $dataInterf['nom_contro'],
                    "nom_sitiox" => substr($dataInterf['nom_contro'], 0, 50),
                    "cod_confar" => NULL,
                    'cod_novfar' => $dataInterf['cod_noveda'], 
                    'nom_noveda' => $dataInterf['nom_noveda'], 
                    'ind_alarma' => $dataInterf['ind_alarma'], 
                    'ind_tiempo' => $dataInterf['ind_tiempo'], 
                    'nov_especi_' => $dataInterf['nov_especi'],
                    'ind_manala' => $dataInterf['ind_manala']
                    );

      //Se consume el WS
      $result = $client -> __soapCall("setNovedadNC", $send );  

      //Divide el resultado para validar valores de respuesta
      $resultArray = explode(";", $result);
      $code_resp = explode(":", $resultArray[0]);
      $msg_resp = explode(":", $resultArray[1]);

      //Valida si la respuesta fue satisfactoria
      if ($code_resp[1] == '1000') {
        //Consulta de inservción de la novedad
        $mSql = "INSERT INTO ". BASE_DATOS . ".tab_despac_contro
                             (num_despac, 
                             cod_contro, 
                             cod_rutasx, 
                             cod_consec, 
                             obs_contro, 
                             cod_noveda, 
                             tiem_duraci, 
                             fec_contro,
                             cod_sitiox, 
                             usr_creaci,
                             fec_creaci)
                      VALUES ".join(",",$values);

        if(new Consulta($mSql, $this->conexion, 'R') === FALSE ) {
        	//Registro logs Novedades
            $this->regLogs("Consulta", $cod_tercer, $fNumDespac, $mSql);
        }
      }else{
      	//Registro logs Novedades
        $this->regLogs("Respuesta WS", $cod_tercer, $fNumDespac, $result);
      }
    }
  }

   //---------------------------------------------
  /*! \fn: getInterfParame
   *  \brief:Verificar la interfaz con faro esta activa
   *  \author: Luis Manrique
   *  \date: 18/03/2020
   *  \date modified: 
   *  \return array
   */
  function getInterfParame($mCodInterf = NULL, $nit = NULL) {
    //Variable necesaria
  	$interf = [];

    //Consulta el estado 
    $mSql = "SELECT ind_estado,
    				        nom_usuari,
    				        clv_usuari,
    				        nom_operad
               FROM ".BASE_DATOS.".tab_interf_parame a
              WHERE a.cod_operad = '".$mCodInterf."'
                    AND a.cod_transp = '".$nit."'";
    $mMatriz = new Consulta($mSql, $this -> conexion);
    $mMatriz = $mMatriz->ret_matriz("a");

    //Asigna valores al array
    $interf['status'] = $mMatriz[0]['ind_estado'] == '1'?true:false;
    $interf['data'] = $mMatriz[0];

    //Retorna Array
    return $interf;
  }


  //---------------------------------------------
  /*! \fn: getDataInterf
   *  \brief:Consulta los datos necesarios para enviar la novedad en WS
   *  \author: Luis Manrique
   *  \date: 18/03/2020
   *  \date modified: 
   *  \return array
   */
  function getDataInterf($cod_tercer, $values) {

    //Arreglo Necesario
  	$dataInterf = [];
    
    //CONSULTAR URL WSDL.
	  $query = "SELECT a.url_webser    
	              FROM ". BD_STANDA .".tab_genera_server a
	        INNER JOIN ". BASE_DATOS . ".tab_transp_tipser b 
	        		    ON a.cod_server = b.cod_server
	             WHERE b.cod_transp = '" . $cod_tercer . "'
	                   AND a.ind_estado = 1 
	          ORDER BY b.fec_creaci DESC ";

	  $consulta = new Consulta( $query, $this -> conexion );
	  $url_webser = $consulta->ret_matrix( 'a' );

    //Asigna posición en el Array
	  $dataInterf['url'] = $url_webser[0]["url_webser"];

	  //Limpia arreglo para extraer los datos necesarios del arreglo 
	  $values = explode(',', $values[0]);
	  $values = $this->cleanArray($values);

	  //Consume el metodo de datos de control
	  $dContro = $this->setDatosContro($values[0]);

	  //Asigna posición en el Array
	  $dataInterf['nom_contro'] = $dContro['nom_contro'];
	  $dataInterf["des_noveda"] = $values[4];

    //Consulta datos necesarios para el arreglo 
	  $mQuerySelPcxbas = "SELECT a.cod_pcxbas 
              	          FROM ". BASE_DATOS . ".tab_homolo_trafico a
              	    INNER JOIN ". BASE_DATOS . ".tab_genera_contro b 
              	    		    ON a.cod_pcxfar = b.cod_contro
              	         WHERE a.cod_transp = '" . $cod_tercer . "'
              	               AND b.nom_contro = '" . $dContro['nom_contro'] . "'
              	               AND a.cod_rutfar = '" . $dContro['cod_rutasx'] . "'";
	  $consulta = new Consulta( $mQuerySelPcxbas, $this -> conexion );
	  $mCodPcxbas = $consulta->ret_matrix( 'a' );

    //Asigna posición en el Array
	  $dataInterf['cod_pcxbas'] = $mCodPcxbas[0]["cod_pcxbas"];

    //Consulta datos necesarios para el arreglo
	  $mSql = "SELECT a.cod_manifi,
	  				        b.num_placax 
	          FROM ". BASE_DATOS . ".tab_despac_despac a
	    INNER JOIN ". BASE_DATOS . ".tab_despac_vehige b 
	    		ON a.num_despac = b.num_despac
	         	   AND a.num_despac = '" . $values[0] . "'";
	  $consulta = new Consulta( $mSql, $this -> conexion );
	  $mDespac = $consulta->ret_matrix( 'a' );

    //Asigna posición en el Array
	  $dataInterf['cod_manifi'] = $mDespac[0]["cod_manifi"];
	  $dataInterf['num_placax'] = $mDespac[0]["num_placax"];

    //Asigna posición en el Array
	  $mSql = "SELECT a.nom_noveda,
      	  				  a.ind_alarma,
      	  				  a.ind_tiempo,
      	  				  a.nov_especi, 
      	  				  a.ind_manala
  	          FROM  ". BASE_DATOS . ".tab_genera_noveda a
  	         WHERE  a.cod_noveda = " . $values[5];
	  $consulta = new Consulta( $mSql, $this -> conexion );
	  $mNoveda = $consulta->ret_matrix( 'a' );

    //Asigna posición en el Array
	  $dataInterf['cod_noveda'] = $values[5];
	  $dataInterf['nom_noveda'] = $mNoveda[0]["nom_noveda"];
	  $dataInterf['ind_alarma'] = $mNoveda[0]["ind_alarma"];
	  $dataInterf['ind_tiempo'] = $mNoveda[0]["ind_tiempo"];
	  $dataInterf['nov_especi'] = $mNoveda[0]["nov_especi"];
	  $dataInterf['ind_manala'] = $mNoveda[0]["ind_manala"];

    //Retorna Array
    return $dataInterf;	
  }

  //---------------------------------------------
  /*! \fn: cleanArray
   *  \brief: Limpia el arreglo de datos innecesarios 
   *  \author: Luis Manrique
   *  \date: 18/03/2020
   *  \date modified: 
   *  \return array
   */
  function cleanArray($array){
    
    //Reemplaza los datos que no se necesitan
  	$array = str_replace(array("(",")",'"',"\n","\r","\t"), "", $array);

    //Recorre el arreglo para limpiarlo de espacios inncesarios
  	foreach ($array as $ident => $campo) {
  		$array[$ident] = trim($campo);
  		if (is_array($campo)) {
  			foreach ($campo as $nCampo => $vCampo) {
    			$array[$ident][$nCampo] = trim($vCampo);
    		}	
  		}
  	}

    //Retorna el array
  	return $array;
  }

  //---------------------------------------------
  /*! \fn: regLogs
   *  \brief: Registra en los logs el registro de errores
   *  \author: Luis Manrique
   *  \date: 25/03/2020
   *  \date modified: 
   *  \return 
   */

  function regLogs($tipo, $cod_tercer, $fNumDespac ,$data){
  	$mFilex = fopen('/var/www/html/ap/interf/app/faro/logs/log_suspen_'.date("Y-m-d").'.log', "a+");
    fwrite($mFilex, "------------------------------------".date("Y-m-d H:i:s")."-------------------------------------------------------------\n");
    fwrite($mFilex, "Empresa: ".$cod_tercer." \n");
    fwrite($mFilex, "Despacho: ".$fNumDespac." \n");
    fwrite($mFilex, $tipo.": \n".$data."\n\n");
    fclose($mFilex);
  }

}//fin clase


$_SUSP = new suspensiones();

?>