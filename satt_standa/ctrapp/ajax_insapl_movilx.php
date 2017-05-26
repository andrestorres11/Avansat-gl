<?php 

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

			case 'datosConductor':
				$this -> datosConductor();
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
			
			default:
				echo "hola";
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
  
		$query = "SELECT a.cod_tercer,
						 a.abr_tercer
					FROM ".BASE_DATOS.".tab_tercer_tercer a 
			  INNER JOIN ".BASE_DATOS.".tab_tercer_conduc b 
			  		  ON a.cod_tercer = b.cod_tercer
				   WHERE a.cod_tercer LIKE '%".$_REQUEST['term']."%'
				   	 AND a.cod_tercer NOT IN(
				   	 							SELECT cod_tercer
					   							FROM ".BASE_DATOS.".tab_usuari_movilx
					  							WHERE 1=1
					  						)";

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
			  INNER JOIN ".BASE_DATOS.".tab_tercer_conduc b 
			  		  ON a.cod_tercer = b.cod_tercer
				   WHERE a.cod_tercer = '".$_REQUEST['cod_tercer']."'";

		$consulta = new Consulta($query, $this -> conexion);
		$tercer = $consulta -> ret_matriz("a");
		$tercer = $tercer[0];

		$aes = new Cypher();

		$tercer['cod_hashxx'] = $aes -> cypher($tercer['cod_tercer'], $tercer['fec_creaci']);
  		
  		if($estadoReturn == NULL)
  		{
			echo json_encode($tercer);
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
    $pri_clave = "";
    for ($i=0; $i<6; $i++){
        $pri_clave .=  dechex(rand(0,15));
    }
 
 	 	$pri_clave = base64_encode($pri_clave);
 	 	$decodedPass = base64_decode($pri_clave);
 	 	if($_REQUEST['Restablecer'] == "Restablecer")
 	 	{
 	 		$query = "UPDATE ".BASE_DATOS.".tab_usuari_movilx SET 
							  clv_usuari = '".$pri_clave."',
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
							  '".$pri_clave."',  
							  '".$_REQUEST['cod_hashxx']."',  
							  '".$_REQUEST['ind_activo']."',  
							  '".$_REQUEST['ind_admini']."',
							  '".$_SESSION['datos_usuario']['cod_usuari']."',  
							  NOW());";
 	 	}

		$consulta = new Consulta($query, $this -> conexion);

		$nit = "SELECT a.clv_filtro 
				  FROM ".BASE_DATOS.".tab_aplica_filtro_perfil a
				 WHERE a.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."'";

		$nit = new Consulta($nit, $this -> conexion);
		$nit = $nit -> ret_matriz("a");
 
		$nit = $nit[0]['clv_filtro'];

		$data = array(

			"cod_tercer" => $_REQUEST['cod_tercer'],
			"cod_usuari" => $_REQUEST['cod_usuari'],
			"clv_usuari" => $pri_clave,
			"cod_hashxx" => $_REQUEST['cod_hashxx'],
			"ind_activo" => $_REQUEST['ind_activo'],
			"nit_transp" => $nit,
			"nom_databa" => BASE_DATOS,
			"usr_creaci" => $_SESSION['datos_usuario']['cod_usuari'],
			"usr_modifi" => $_SESSION['datos_usuario']['cod_usuari'],
			"source" => "SAT",
			"ind_admini" => $_REQUEST['ind_admini']
 
		);	
	
		if(	$consulta ){

			include URL_ARCHIV_STANDA."interf/app/APIClienteApp/controlador/UsuarioControlador.php";
			$cliente = new UsuarioControlador();
			if($_REQUEST['Restablecer'] == "Restablecer")
 	 		{
 	 			$respuesta = $cliente -> actualizar($data);
 			}
 			else
 			{
				$respuesta = $cliente -> registrar($data); 
 			}
 
			if($respuesta == "ok"){

	            $mCabece = 'MIME-Version: 1.0' . "\r\n";
                $mCabece .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $mCabece .= 'From: Aplicacion SAT <avansat@intrared.net>' . "\r\n";

				$tmpl_file = "planti/planti_usuari_appsat.html"; 
                $thefile = implode("", file($tmpl_file));
                $thefile = addslashes($thefile);
                $thefile = "\$r_file=\"" . $thefile . "\";";
                eval($thefile);
                $mHtmlxx = $r_file;
                mail($_REQUEST['mail'], "Código de activación aplicación AVANSAT ", $mHtmlxx, $mCabece); 
				echo "ok";
			}
			else{
				echo "no";
			}

		}
		else{
			echo "no";
		}

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
		$mQuery = "SELECT   a.cod_transp, a.cod_tercer, c.cod_usuari, b.nom_tercer, b.nom_apell1, b.nom_apell2, b.dir_emailx, c.clv_usuari, IF( b.fec_creaci IS NULL OR a.fec_creaci = '', 'N/A', b.fec_creaci) AS fec_creaci, c.cod_tercer AS cod_pendie, c.ind_activo AS ind_estado
                   FROM  
                          ".BASE_DATOS.".tab_transp_tercer a INNER JOIN 
                          ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_tercer = b.cod_tercer INNER JOIN
                          ".BASE_DATOS.".tab_usuari_movilx c ON b.cod_tercer = c.cod_tercer
                  WHERE
                          a.ind_estado = 1 AND
                          a.cod_transp = '".$_REQUEST["cod_transp"]."'   ";  
        
		//----------------------------------------------------------------------------------------------------------------------------
		$cList = new DinamicList( $this -> conexion, $mQuery , 4 );
		$cList -> SetHeader( "Transportadora", "field:a.cod_transp" );
		$cList -> SetHeader( "Doc.Conductor", "field:a.cod_tercer" );
		$cList -> SetHeader( "Usuario APP", "field:c.cod_usuari" );
		$cList -> SetHeader( "Nombre conductor", "field:b.nom_tercer" );
		$cList -> SetHeader( "Primer apellido", "field:b.nom_apell1" );
		$cList -> SetHeader( "Segundo apellido", "field:b.nom_apell2" );
		$cList -> SetHeader( "Correo", "field:b.dir_emailx" );
		$cList -> SetHeader( "Contraseña", "field:c.clv_usuari" );
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
	          $consultaFinal = new Consulta("COMMIT", $this -> conexion);
	          echo "ok";
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
}

$ajax = new AjaxInsertarAutorizacion();
?>