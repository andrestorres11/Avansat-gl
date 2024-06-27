<?php 
/*ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);*/

  define("URL_ARCHIV_STANDA", "/var/www/html/ap/");
class AjaxInsertarAutorizacion
{
	var $conexion = NULL;

	function __construct()
	{	
		include("../lib/general/constantes.inc");
		include("../lib/ajax.inc");
		$this -> conexion = $AjaxConnection;
   
		switch ($_REQUEST['op']) {

			case 'buscarConductor':
				$this -> buscarConductor();
				break;

			case 'buscarAsistente':
				$this-> buscarAsistente();
				break;

			case 'datosConductor':
				$this -> datosConductor();
				break;

			case 'datosHojadeVidaCT':
				$this->datosHojadeVidaCT();
				break;

			case 'guardarUsuario':
				$this -> guardarUsuario();
				break;			
			case 'listaUsuariosMoviles':
				$this -> listaUsuariosMoviles();
				break;
			case 'incativarUsuario':
				$this -> incativarUsuario();
				break;
			case 'RestablecerUsuario':
				$this -> RestablecerUsuario();
				break;
			case 'validarUsuario':
				$this -> validateUniqueUser();
				break;
		}
	}

	/*! \fn: buscarConductor
     *  \brief: Busca el conductor
     *  \author: Edward Serrano
     *  \date: 26/05/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
	function buscarConductor(){
		switch ($_REQUEST['activity']) {
			case '0':
				$activity = 4;
				break;
			
			case '3':
				$activity = COD_FILTRO_PROVEE;
				break;
			
			default:
				$activity = 11;
				break;
		}
		$query = "SELECT a.cod_tercer, a.abr_tercer 
					FROM tab_tercer_tercer a 
			  INNER JOIN tab_tercer_activi b 
			          ON a.cod_tercer = b.cod_tercer
    				 AND b.cod_activi = '".$activity."'
			  -- INNER JOIN tab_transp_tercer c ON c.cod_tercer = a.cod_tercer 
			  		 -- AND c.cod_transp = '".$_REQUEST['nit_transp']."'
				   WHERE a.cod_tercer LIKE '%".$_REQUEST['term']."%' 
					 AND a.cod_tercer NOT IN( SELECT cod_tercer 
												FROM tab_usuari_movilx 
											   WHERE 1 = 1)
				";
		if($_REQUEST['activity'] == 3){
			$query = "SELECT a.cod_docume as 'cod_tercer', CONCAT(a.pri_apelli, ' ', a.seg_apelli, ' ', a.nom_contra) as 'abr_tercer'
			FROM ".BASE_DATOS.".tab_hojvid_ctxxxx a 
				WHERE a.cod_docume LIKE '%".$_REQUEST['term']."%' 
					AND a.cod_docume NOT IN( SELECT cod_tercer 
												FROM tab_usuari_movilx 
											WHERE 1 = 1)
					AND a.ind_estado = 1
					AND a.cod_activi = '".$activity."'";
		}

		$consulta = new Consulta($query, $this -> conexion);
		$tercer = $consulta -> ret_matriz("a");
  
		$data = array();
		for($i=0, $len = count($tercer); $i<$len; $i++){
		 	$data [] = '{"label":"'.$tercer[$i]['cod_tercer'] . " - " . $tercer[$i]['abr_tercer'].'","value":"'. ($tercer[$i]['cod_tercer']).'"}'; 
		}
		echo '['.join(', ',$data).']';

	}

	function buscarAsistente(){

		$query = "SELECT a.cod_tercer,a.abr_tercer
					FROM ".BASE_DATOS.".tab_tercer_tercer a 
					INNER JOIN ".BASE_DATOS.".tab_tercer_activi b ON a.cod_tercer = b.cod_tercer AND b.cod_activi = '14'
					WHERE a.cod_tercer LIKE '%".$_REQUEST['term']."%'";

		$consulta = new Consulta($query, $this -> conexion);
		$tercer = $consulta -> ret_matriz("a");

		$data = array();
		for($i=0, $len = count($tercer); $i<$len; $i++){
		$data [] = '{"label":"'.$tercer[$i]['cod_tercer'] . " - " . $tercer[$i]['abr_tercer'].'","value":"'. ($tercer[$i]['cod_tercer']).'"}'; 
		}
		echo '['.join(', ',$data).']';

	}

	/*! \fn: datosConductor
     *  \brief: Obtiene los datos del conducto
     *  \author: Edward Serrano
     *  \date: 26/05/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
	function datosConductor($estadoReturn = NULL){

		include("../ctrapp/seguridad/AESClass.php");

		$query = "SELECT IF(ISNULL(a.nom_tercer) OR a.nom_tercer = '', 'N/A' , a.nom_tercer) AS nom_tercer,
						 IF(ISNULL(a.nom_apell1) OR a.nom_apell1 = '', 'N/A' , a.nom_apell1) AS nom_apell1,
						 IF(ISNULL(a.nom_apell2) OR a.nom_apell2 = '', 'N/A' , a.nom_apell2) AS nom_apell2,
						 IF(ISNULL(a.num_telef1) OR a.num_telef1 = '', 'N/A' , a.num_telef1) AS num_telef1,
						 IF(ISNULL(a.num_telef2) OR a.num_telef2 = '', 'N/A' , a.num_telef2) AS num_telef2,
						 IF(ISNULL(a.num_telmov) OR a.num_telmov = '', 'N/A' , a.num_telmov) AS num_telmov, 
						 IF(ISNULL(a.dir_domici) OR a.dir_domici = '', 'N/A' , a.dir_domici) AS dir_domici,
						 IF(ISNULL(a.dir_emailx) OR a.dir_emailx = '', 'N/A' , a.dir_emailx) AS dir_emailx,
						 IF(ISNULL(a.fec_creaci) OR a.fec_creaci = '', 'N/A' , a.fec_creaci) AS fec_creaci,
						 IF(ISNULL(a.cod_tercer) OR a.cod_tercer = '', 'N/A' , a.cod_tercer) AS cod_tercer,
						 IF(a.cod_tipdoc = 'C' , 'N'   , 'J' ) AS cod_tipper,
						 IF(ISNULL(a.cod_tipdoc) OR a.cod_tipdoc = '', 'C'   , a.cod_tipdoc ) AS cod_tipdoc 
					FROM ".BASE_DATOS.".tab_tercer_tercer a 
			  LEFT JOIN ".BASE_DATOS.".tab_tercer_conduc b 
			  		  ON a.cod_tercer = b.cod_tercer
				   WHERE a.cod_tercer = '".$_REQUEST['cod_tercer']."'";

		$consulta = new Consulta($query, $this -> conexion);
		$tercer = $consulta -> ret_matriz("a");
		$tercer = $tercer[0];

		$aes = new Cypher();

		$tercer['cod_hashxx'] = $aes -> cypher($tercer['cod_tercer'], $tercer['fec_creaci']);
  		
  		if($estadoReturn == NULL)
  		{
			//echo json_encode($tercer);

			//echo "<pre>"; print_r( $tercer ); echo "</pre>";
			$data = array();
			foreach ($tercer AS $mIndex => $conduc) {
			
			 	$data [] = '"'.$mIndex.'" : "'.$conduc.'"'; 
			}
			echo '{'.join(', ',$data).'}';
  		}
  		else
  		{
  			return json_encode($tercer);
  		}
	}


	/*! \fn: datosHojadeVidaCT
     *  \brief: Obtiene los datos de la hoja de vida CT
     *  \author: Cristian Andrés Torres
     *  \date: 13/13/2021
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
	function datosHojadeVidaCT($estadoReturn = NULL){

		include("../ctrapp/seguridad/AESClass.php");

		$query = "SELECT IF(ISNULL(a.nom_tercer) OR a.nom_tercer = '', 'N/A' , a.nom_tercer) AS nom_tercer,
						 IF(ISNULL(a.nom_apell1) OR a.nom_apell1 = '', 'N/A' , a.nom_apell1) AS nom_apell1,
						 IF(ISNULL(a.nom_apell2) OR a.nom_apell2 = '', 'N/A' , a.nom_apell2) AS nom_apell2,
						 IF(ISNULL(a.num_telef1) OR a.num_telef1 = '', 'N/A' , a.num_telef1) AS num_telef1,
						 IF(ISNULL(a.num_telef2) OR a.num_telef2 = '', 'N/A' , a.num_telef2) AS num_telef2,
						 IF(ISNULL(a.num_telmov) OR a.num_telmov = '', 'N/A' , a.num_telmov) AS num_telmov, 
						 IF(ISNULL(a.dir_domici) OR a.dir_domici = '', 'N/A' , a.dir_domici) AS dir_domici,
						 IF(ISNULL(a.dir_emailx) OR a.dir_emailx = '', 'N/A' , a.dir_emailx) AS dir_emailx,
						 IF(ISNULL(a.fec_creaci) OR a.fec_creaci = '', 'N/A' , a.fec_creaci) AS fec_creaci,
						 IF(ISNULL(a.cod_tercer) OR a.cod_tercer = '', 'N/A' , a.cod_tercer) AS cod_tercer,
						 IF(a.cod_tipdoc = 'C' , 'N'   , 'J' ) AS cod_tipper,
						 IF(ISNULL(a.cod_tipdoc) OR a.cod_tipdoc = '', 'C'   , a.cod_tipdoc ) AS cod_tipdoc 
					FROM ".BASE_DATOS.".tab_tercer_tercer a 
			  LEFT JOIN ".BASE_DATOS.".tab_tercer_conduc b 
			  		  ON a.cod_tercer = b.cod_tercer
				   WHERE a.cod_tercer = '".$_REQUEST['cod_tercer']."'";

		$consulta = new Consulta($query, $this -> conexion);
		$tercer = $consulta -> ret_matriz("a");
		$tercer = $tercer[0];

		$aes = new Cypher();

		$tercer['cod_hashxx'] = $aes -> cypher($tercer['cod_tercer'], $tercer['fec_creaci']);
  		
  		if($estadoReturn == NULL)
  		{
			//echo json_encode($tercer);

			//echo "<pre>"; print_r( $tercer ); echo "</pre>";
			$data = array();
			foreach ($tercer AS $mIndex => $conduc) {
			
			 	$data [] = '"'.$mIndex.'" : "'.$conduc.'"'; 
			}
			echo '{'.join(', ',$data).'}';
  		}
  		else
  		{
  			return json_encode($tercer);
  		}
	}

	/*! \fn: guardarUsuario
     *  \brief: Realiza el registro y re establecimiento de las contraseñas de los usuarios
     *  \author: Edward Serrano
     *  \date: 26/05/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
	function guardarUsuario(){ 
    
    
		$password = $this->generatePassword();
 	 	$pri_clave2 = base64_encode($password);
		$decodedPass = $password;

 	 	if($_REQUEST['Restablecer'] == "Restablecer")
 	 	{
 	 		$query = "UPDATE ".BASE_DATOS.".tab_usuari_movilx SET 
							  clv_usuari = '".$pri_clave2."',
							  cod_hashxx = '".$_REQUEST['cod_hashxx']."',
							  ind_activo = '".$_REQUEST['ind_activo']."',
							  usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
							  fec_modifi = NOW() 
					  	WHERE 
							  cod_tercer = '".$_REQUEST['cod_tercer']."' AND
							  cod_usuari = '".$_REQUEST['cod_usuari']."';";
 	 	}
 	 	else
 	 	{
 	 		$mQuery = "SELECT a.cod_transp, a.cod_tercer 
				  			FROM ".BASE_DATOS.".tab_transp_tercer a
				 			WHERE a.cod_transp = '".$_REQUEST['cod_transp']."' AND
				 				  a.cod_tercer = '".$_REQUEST['cod_tercer']."'; ";

			$mTransTercer = new Consulta($mQuery, $this -> conexion);
			$mTransTercer = $mTransTercer -> ret_matriz("a");
			if(count($mTransTercer) == 0)
			{
				$query2 = "INSERT INTO ".BASE_DATOS.".tab_transp_tercer (
 	 					      cod_transp,
 	 					      cod_tercer,
 	 					      ind_estado,
 	 					      usr_creaci,
 	 					      fec_creaci
 	 						  )
 	 				  VALUES (
 	 				  		  '".$_REQUEST['cod_transp']."',
 	 				  		  '".$_REQUEST['cod_tercer']."',
 	 				  		  1,
 	 				  		  '".$_SESSION['datos_usuario']['cod_usuari']."', 
 	 				  		  NOW());";
 	 			$consulta2 = new Consulta($query2, $this -> conexion);
			}

 	 		$query = "INSERT INTO ".BASE_DATOS.".tab_usuari_movilx ( 
							  cod_tercer ,
							  cod_usuari ,
							  clv_usuari ,
							  cod_hashxx ,
							  ind_activo ,
							  ind_admini ,
							  usr_creaci ,
							  fec_creaci 
							  )
					  VALUES (
							  '".$_REQUEST['cod_tercer']."',  
							  '".$_REQUEST['cod_usuari']."',  
							  '".$pri_clave2."',  
							  '".$_REQUEST['cod_hashxx']."',  
							  '".$_REQUEST['ind_activo']."',  
							  '".$_REQUEST['ind_admini']."',
							  '".$_SESSION['datos_usuario']['cod_usuari']."',  
							  NOW());";
 	 	}

		$consulta = new Consulta($query, $this -> conexion);

		$nit = $_REQUEST['cod_transp'];

		$tip_usuari = NULL;
		if( isset($_REQUEST['ind_admini']) ){
			$tip_usuari = $_REQUEST['ind_admini'];
		}else if( isset($_REQUEST['tip_usuari']) ){
			$tip_usuari = $_REQUEST['tip_usuari'];
		}

		$data = array(

			"cod_tercer" => $_REQUEST['cod_tercer'],
			"cod_usuari" => $_REQUEST['cod_usuari'],
			"clv_usuari" => $decodedPass,
			"cod_hashxx" => $_REQUEST['cod_hashxx'],
			"ind_activo" => $_REQUEST['ind_activo'],
			"nit_transp" => $nit,
			"nom_databa" => BASE_DATOS,
			"usr_creaci" => $_SESSION['datos_usuario']['cod_usuari'],
			"usr_modifi" => $_SESSION['datos_usuario']['cod_usuari'],
			"source" => "SAT",
			"ind_admini" => $tip_usuari,
			"url_fotcon" => NULL,
			"dir_emailx" => $_REQUEST['mail'],
			"nom_usuari" => $_REQUEST['nom_usuari'],
			"ape_usuari" => $_REQUEST['nom_appel1'],
			"tel_usuari" => $_REQUEST['num_telef1'],
 			"ind_aprova" => 0,
            "ind_estilo" => 0,
		);	

		

		$error = '';
		$pattern = '/20[0-9]/i';
        include_once('../lib/InterfCentralApps.inc');
        $centralApps = new IntefCentralApps('central');
	
		if(	$consulta ){

			$respuesta = "ok";
			$response = $centralApps ->auth_token(); //solicitud de token
			
			if(!preg_match($pattern, $response['code'])){
				$respuesta = "no";
				$error = 'Fallo autentificacion hacia central ';
			}

			$responsex = $centralApps ->rewNewTokenTranport($response['response']->renew,$nit);
			
			if(!preg_match($pattern, $responsex)){
				$respuesta = "no";
				$error = 'Se genero un error Generando token de transportadora ';
			}

			$campos = '&cod_tercer='.intval($_REQUEST['cod_tercer']); //envio de filtro por url cod_usuari=xxxx&nit_transp=xxxxx
          	$responseData = $centralApps -> viewPetition('usuari-appsat',$campos);
			
			if(!preg_match($pattern, $responseData['code'])){
				$respuesta = "no";
				$error = 'Se genero un error validando usuario ';
			}

			$cod_consec = count($responseData['response']->items)>0 ? $responseData['response']->items[0]->cod_consec:0;

			if($_REQUEST['Restablecer'] == "Restablecer" || $cod_consec!=0)
 	 		{
				$responseUpdate = $centralApps ->updatePetition('usuari-appsat',$data,$cod_consec);
				
				if(!preg_match($pattern, $responseUpdate['code'])){
					$respuesta = "no";
					$error = 'Se genero un error actualizando usuario ';
				}else{
					$campos = '&cod_tercer='.intval($_REQUEST['cod_tercer']).'&nit_transp='.$nit;
					$responseTransportData = $centralApps -> viewPetition('transp-usuari',$campos);

					if(!preg_match($pattern, $responseTransportData['code'])){
						$respuesta = "no";
						$error = 'Se genero un error validando usuario ';
					}
              
					if(count($responseTransportData['response']->items)==0){ //insertar si no existe

						$dataTransp = array(
						"nit_transp" => $_REQUEST['cod_transp'],
						"cod_tercer" => $_REQUEST['cod_tercer'],
						"fec_creaci" => date("Y-m-d H:i:s"),
						);

						$responseInsert = $centralApps ->createPetition('transp-usuari',$dataTransp);
						if(!preg_match($pattern, $responseInsert['code'])){
							$respuesta = "no";
							$error = 'Se genero un error actualizando usuario ';
						}

					}
				}
 	 			
 			}
 			else
 			{
				$responseInsert = $centralApps ->createPetition('usuari-appsat',$data);
				if(!preg_match($pattern, $responseInsert['code'])){
					$respuesta = "no";
					$error = 'Se genero un error creando usuario ';
				}

				$campos = '&cod_tercer='.intval($_REQUEST['cod_tercer']).'&nit_transp='.$nit;
				$responseTransportData = $centralApps -> viewPetition('transp-usuari',$campos);

				if(!preg_match($pattern, $responseTransportData['code'])){
					$respuesta = "no";
					$error = 'Se genero un error validando usuario ';
				}
			
				if(count($responseTransportData['response']->items)==0){ //insertar si no existe

					$dataTransp = array(
					"nit_transp" => $nit,
					"cod_tercer" => $_REQUEST['cod_tercer'],
					"fec_creaci" => date("Y-m-d H:i:s"),
					);

					$responseInsert = $centralApps ->createPetition('transp-usuari',$dataTransp);
					if(!preg_match($pattern, $responseInsert['code'])){
						$respuesta = "no";
						$error = 'Se genero un error actualizando usuario ';
					}
				}
 			}
 
			if($respuesta == "ok"){

	            $mCabece = 'MIME-Version: 1.0' . "\r\n";
                $mCabece .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $mCabece .= 'From: Aplicacion SAT <avansat@intrared.net>' . "\r\n";

                $mYear = date("Y");
                $banner = "https://".$_SERVER['HTTP_HOST']."/ap/".BASE_DATOS."/images/banner.jpg";
				$tmpl_file = "planti/planti_usuari_appsat2.html"; 
                $thefile = implode("", file($tmpl_file));
                $thefile = addslashes($thefile);
                $thefile = "\$r_file=\"" . $thefile . "\";";
                eval($thefile);
				$mHtmlxx = $r_file;
				$asunto="Código de activación aplicación AVANSAT ";

				if($_REQUEST['ind_admini']==2){
					$asunto="Código de activación aplicación de INSPECCIÓN VEHICULAR ";
				}

				$to= $_REQUEST['mail'];
				$subject = 'OET-AVANSATGL';
				$from = 'notificaciones@faro.com.co';

				mail($to,$subject,$asunto,"From: <$from>","-f $from -r $from");

				echo json_encode(array("response"=>"ok","error"=>""));
			}
			else{

				echo json_encode(array("response"=>"no","error"=>$error));
			}

		}
		else{
			echo json_encode(array("response"=>"no","error"=>$error));
		}

	}

	private function generatePassword()
	{
		$key = "";
		$pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
		$max = strlen($pattern)-1;
		for($i = 0; $i < 6; $i++){
			$key .= substr($pattern, mt_rand(0,$max), 1);
		}
		return $key;
	}

	/*! \fn: listaUsuariosMoviles
     *  \brief: Lista los usuarios regitrados para movil
     *  \author: Edward Serrano
     *  \date: 26/05/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
	public function listaUsuariosMoviles()
	{
		$mQuery = "SELECT   a.cod_transp, a.cod_tercer, c.cod_usuari, b.nom_tercer, b.nom_apell1, b.nom_apell2, 
		IFNULL((SELECT `num_placax` FROM `tab_vehicu_vehicu` WHERE `cod_conduc`=a.cod_tercer LIMIT 1),'-') as 'num_placax',
		b.dir_emailx, c.clv_usuari,
		IF(c.ind_admini=0,'CONDUCTOR',IF(c.ind_admini=1,'ADMINISTRADOR',IF(c.ind_admini = 4,'INSPECCIONES','-'))) as 'ind_admini', IF( b.fec_creaci IS NULL OR a.fec_creaci = '', 'N/A', b.fec_creaci) AS fec_creaci, c.cod_tercer AS cod_pendie, c.ind_activo AS ind_estado
                   
				   
				   FROM  
                          ".BASE_DATOS.".tab_transp_tercer a INNER JOIN 
                          ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_tercer = b.cod_tercer INNER JOIN
                          ".BASE_DATOS.".tab_usuari_movilx c ON b.cod_tercer = c.cod_tercer
                  WHERE
                          a.ind_estado = 1 AND
                          a.cod_transp = '".$_REQUEST["cod_transp"]."'   ";  
        
        $_SESSION["queryXLS"] = $mQuery;
        
		//----------------------------------------------------------------------------------------------------------------------------
		$cList = new DinamicList( $this -> conexion, $mQuery , 4 );
		$cList -> SetClose('no');
		$cList -> SetCreate("Agregar Usuario", "onclick:NuevoUsuario()");
		$cList -> SetExcel("Excel", "onclick:exportExcel('opcion=4')");
		$cList -> SetHeader( "Transportadora", "field:a.cod_transp" );
		$cList -> SetHeader( "Doc.Conductor", "field:a.cod_tercer" );
		$cList -> SetHeader( "Usuario APP", "field:c.cod_usuari" );
		$cList -> SetHeader( "Nombre conductor", "field:b.nom_tercer" );
		$cList -> SetHeader( "Primer apellido", "field:b.nom_apell1" );
		$cList -> SetHeader( "Segundo apellido", "field:b.nom_apell2" );
		$cList -> SetHeader( "Placa", "field:c.clv_usuari" );
		$cList -> SetHeader( "Correo", "field:b.dir_emailx" );

		$cList -> SetHeader( "Contrasena", "field:c.clv_usuari" );
		$cList -> SetHeader( "Tipo Usuario", "field:c.ind_admini" );

		$cList -> SetOption(utf8_decode("Opciones"),"field:cod_option; width:1%; onclikDisable:editarUsuarioMovil( 2, this ); onclikEnable:editarUsuarioMovil( 1, this ); onclikEdit:editarUsuarioMovil( 99, this );" );
		$cList -> SetHidden("cod_tercer", "1" ); 
		$cList -> SetHidden("nom_tercer", "3" ); 
		$cList -> SetClose( "no" );
		$cList -> Display( $this -> conexion );
		echo $cList -> GetHtml();
		$_SESSION["DINAMIC_LIST"]   = $cList;
	}

	/*! \fn: incativarUsuario
     *  \brief: Realiza la activacion e inactivacion de los usuarios moviles
     *  \author: Edward Serrano
     *  \date: 26/05/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
	public function incativarUsuario()
	{
		try
        {
			$nit = $_REQUEST['cod_transp'];
        	$ind_activo = null;
			switch ($_REQUEST['action']) {
				case 'activarUsuario':
					$ind_activo = "1";
					break;

				case 'inactivarUsuario':
					$ind_activo = "0";
					break;
			}
			
	        $mSql = "UPDATE ".BASE_DATOS.".tab_usuari_movilx 
	                        SET 
	                              ind_activo = '".$ind_activo."',
	                              usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
	                              fec_modifi = NOW()
	                        WHERE cod_tercer = '".$_REQUEST['cod_tercer']."'";

	        $consulta = new Consulta($mSql, $this -> conexion, "BR");
	        if($consulta)
	        {
				$pattern = '/20[0-9]/i';
        		include_once( '../lib/InterfCentralApps.inc');
        		$centralApps = new IntefCentralApps('central');
				
				$error = '';
				$respuesta = "ok";
				$response = $centralApps ->auth_token(); //solicitud de token
				if(!preg_match($pattern, $response['code'])){
					$respuesta = "no";
					$error = 'Fallo autentificacion hacia central ';
				}

				$responsex = $centralApps ->rewNewTokenTranport($response['response']->renew,$nit);
				if(!preg_match($pattern, $responsex)){
					$respuesta = "no";
					$error = 'Se genero un error Generando token de transportadora ';
				}

				$campos = '&cod_tercer='.intval($_REQUEST['cod_tercer']); //envio de filtro por url cod_usuari=xxxx&nit_transp=xxxxx
				$responseData = $centralApps -> viewPetition('usuari-appsat',$campos);

				if(!preg_match($pattern, $responseData['code'])){
					$respuesta = "no";
					$error = "Error validando usuario central!";
				}

				$cod_consec = count($responseData['response']->items)>0 ? $responseData['response']->items[0]->cod_consec:0;

				if($cod_consec!=0)
				{
					$data = array(
						"ind_activo" => $ind_activo,
					);	

					$responseUpdate = $centralApps ->updatePetition('usuari-appsat',$data,$cod_consec);
					if(!preg_match($pattern, $responseUpdate['code'])){
						$respuesta = "no";
						$error = "Error Actualizando usuario central!";
					}
				}

				if($respuesta == "ok"){
					$consultaFinal = new Consulta("COMMIT", $this -> conexion);
					echo "ok";
				}else{
					$consultaFinal = new Consulta("ROLLBACK", $this -> conexion);
					echo "error";
				}
	        }
	        else
	        {
	          $consultaFinal = new Consulta("ROLLBACK", $this -> conexion);
	          echo "error";
	        }
       }
      catch(Exception $e)
      {
        echo "Error en la funcion incativarUsuario:",  $e->getMessage(), "\n";
      }
	}

	/*! \fn: RestablecerUsuario
    * \brief: Restablece el usuario Movil
    * \author: Edward Serrano
    * \date: 24/05/2017
    * \date modified: dia/mes/año
    * \param: paramatro
    * \return valor que retorna
    */
    public function RestablecerUsuario()
    {
      try
      {
        $cod_hashxx = json_decode($this->datosConductor(true));
        $_REQUEST['cod_hashxx'] = $cod_hashxx->cod_hashxx;
        //se declara para condicionar insert en la funcion guardarUsuario
        $_REQUEST['Restablecer'] = "Restablecer";
		$this->guardarUsuario();  

      }
      catch(Exception $e)
      {
        echo "Error en la funcion RestablecerUsuario:",  $e->getMessage(), "\n";
      }
    }

	public function validateUniqueUser(){
		include_once('../lib/InterfCentralApps.inc');
		$respuesta = "ok";
        $centralApps = new IntefCentralApps('central');

		$response = $centralApps ->auth_token(); //solicitud de token
			
		if(!preg_match($pattern, $response['code'])){
			$respuesta = "no";
			$error = 'Fallo autentificaci?n hacia central ';
		}

		$responsex = $centralApps ->rewNewTokenTranport($response['response']->renew,$_REQUEST['cod_transp']);
		if(!preg_match($pattern, $responsex)){
			$respuesta = "no";
			$error = 'Se genero un error Generando token de transportadora ';
		}

		$campos = '&cod_usuari='.$_REQUEST['cod_usuari'];
		$responseData = $centralApps -> viewPetition('usuari-appsat',$campos);

		if(!preg_match($pattern, $responseData['code'])){
			$respuesta = "no";
			$error = 'Se genero un error validando usuario ';
			
		}
  
		if(count($responseData['response']->items[0])>0){ //insertar si no existe
			$respuesta = "no";
			$error = 'Usuario ya en uso, utiliza un nuevo usuario';
		}else{
			$respuesta = "ok";
			$error = '';
		}

		echo json_encode(array("response"=>$respuesta,"error"=>$error));
	}
}

$ajax = new AjaxInsertarAutorizacion();
?>