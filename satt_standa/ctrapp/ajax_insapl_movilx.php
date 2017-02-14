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
			
			default:
				echo "hola";
				break;
		}
	}

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

	function datosConductor(){

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
		$patron = array("(\¬)", "(\.)", "(\,)", "(\ )", "(ñ)", "(Ñ)", "(\°)", "(\º)", "(&)", "(Â)", "(\()", "(\))", "(\/)", "(\´)", "(\¤)", "(\Ã)", "(\‘)", "(\ƒ)", "(\â)", "(\€)", "(\˜)", "(\¥)", "(Ò)", "(Í)", "(\É)", "(\Ãƒâ€šÃ‚Â)", "(\·)", "(\ª)", "(\-)", "(\+)", "(\Ó)", "(\ü)", "(\Ü)", "(\é)", "(\;)", "(\¡)", "(\!)", "(\`)", "(\<)", "(\>)", "(\_)", "(\#)", "(\ö)", "(\À)", "(\¿)", "(\Ã±)", "(\±)", "(\*)", "(Ú)", "(\%)", "(\|)", "(\ò)", "(\Ì)", "(\:)", "(\Á)", "(\×)", "(\@)", "(\ )", "(\Ù)", "(\á)", "(\–)", "(\")", "(\È)", "(\])", "(\')", "(\í)", "(\Ç)","(\Nš)","(\‚)", "(\ó)", "(\ )", "(\ )", "(\ï½)", "(\?)" );
  		$reemplazo = array("", "", "", "", "n", "N", "", "", "Y", "", "", "", "", "", "", "", "", "", "", "", "", "", "O", "I", "E", "", "", "a", "", "", "O","U","U", "e", "", "", "", "", "", "", "", "", "", "A", "", "", "", "", "", "", "", "", "I", "", "A", "", "", "", "U", "a", "", "", "E", "", "", "i", "", "N","", "", "", "", "" , "", ""  ); 

		$tercer['cod_hashxx'] =preg_replace( $patron, $reemplazo, $aes -> cypher($tercer['cod_tercer'], $tercer['fec_creaci']) ) ;
  
		echo json_encode($tercer);
	}

	function guardarUsuario(){ 
    $pri_clave = "";
    for ($i=0; $i<6; $i++){
        $pri_clave .=  dechex(rand(0,15));
    }
 
 	 	$pri_clave = base64_encode($pri_clave);
 	 	$decodedPass = base64_decode($pri_clave);

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
			"source" => "SAT",
			"ind_admini" => $_REQUEST['ind_admini']
 
		);	
	
		if(	$consulta ){

			include URL_ARCHIV_STANDA."interf/app/APIClienteApp/controlador/UsuarioControlador.php";
			$cliente = new UsuarioControlador();
 
			$respuesta = $cliente -> registrar($data); 
 
			if($respuesta == "ok"){

	            $mCabece = 'MIME-Version: 1.0' . "\r\n";
                $mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                $mCabece .= 'From: Aplicacion SAT ' . "\r\n";

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
}

$ajax = new AjaxInsertarAutorizacion();

?>