<?php
//ini_set('error_reporting', E_ALL);
//ini_set("display_errors", 1);
@session_start();
class Solici_solici
{
	var $conexion, $cod_aplica,	$usuario, $usuario2, $cod_tercer, $arr_datsol, $interfParame, $InterfSolicitud, $_request,
		$raw, $input_post, $input_globals, $input_get, $max_file_size=2000000, $tmp_dir_path="/tmp/",$unlink_file=false;

	function Solici_solici()
	{
		$this->Main();
	}
	//function __construct(){
	//	$this->Main();
	//}
	function setArrDatSol(){
		$this->arr_datsol=array(
			"url_faroxx"=>URL_INTERF_FAROXX,
			"cod_usuari"=>$this->getInterfParame("nom_usuari"),//USR_INTERF_FAROXX,
			"pwd_clavex"=>$this->getInterfParame("clv_usuari"),//PWD_INTERF_FAROXX,
			"cod_transp"=>$this->getInterfParame("cod_transp"),//NIT_TRANSPOR,
			"nom_aplica"=>BASE_DATOS,
			"url_aplica"=>null,//$_SERVER["PHP_SELF"],//url de retorno para respuesta
			"cod_solici"=>@$_SESSION["datos_usuario"]["cod_usuari"],
			"nom_solici"=>isset($_POST["nom_solici"]) ? $_POST["nom_solici"] : @$_GET["nom_solici"],
			"mai_solici"=>isset($_POST["mai_solici"]) ? $_POST["mai_solici"] : @$_GET["mai_solici"],
			"fij_solici"=>isset($_POST["fij_solici"]) ? $_POST["fij_solici"] : @$_GET["fij_solici"],
			"cel_solici"=>isset($_POST["cel_solici"]) ? $_POST["cel_solici"] : @$_GET["cel_solici"],
			"cod_tipsol"=>null,//se actualiza mas adelante
			"nom_tipsol"=>null//se actualiza mas adelante
		);
	}
	function Main()
	{
		
		//detener por no estar logeado
		if(!array_key_exists("datos_usuario", @$_SESSION)){
			die("Your session has expired!");
		}

		//DIR_APLICA_CENTRAL existe cuando esta embedido
		if(defined("DIR_APLICA_CENTRAL")){
			$file="../".DIR_APLICA_CENTRAL."/lib/general/constantes.inc";
			$file2="../".DIR_APLICA_CENTRAL."/lib/ajax.inc";
		}else{
			$file="../lib/general/constantes.inc";
			$file2="../lib/ajax.inc";
		}
		if(file_exists($file) && file_exists($file2)){
			ob_start();
			include( $file );
			include( $file2 );
			ob_clean();
		}
		$error=array();
		if(!defined("URL_INTERF_FAROXX")){
			array_push($error, "Solici_solici::Main > Const URL_INTERF_FAROXX does not exists");
		}
		if(!defined("USR_INTERF_FAROXX")){
			array_push($error, "Solici_solici::Main > Const USR_INTERF_FAROXX does not exists");
		}
		if(!defined("PWD_INTERF_FAROXX")){
			array_push($error, "Solici_solici::Main > Const PWD_INTERF_FAROXX does not exists");
		}
		if(!defined("NIT_TRANSPOR")){
			array_push($error, "Solici_solici::Main > Const NIT_TRANSPOR does not exists");
		}
		if(!defined("BASE_DATOS")){
			array_push($error, "Solici_solici::Main > Const BASE_DATOS does not exists");
		}
		if(!defined("USUARIO")){
			array_push($error, "Solici_solici::Main > Const USUARIO does not exists");
		}
		if(!defined("DIR_APLICA_CENTRAL")){
			array_push($error, "Solici_solici::Main > Const DIR_APLICA_CENTRAL does not exists");
		}
		if(!isset($AjaxConnection)){
			array_push($error, "Solici_solici::Main > Var AjaxConnection does not exists");	
		}
		if(sizeof($error)>0){
			print implode("<br>",$error);
			exit;
		}

		$this->conexion = $AjaxConnection;
		$this->raw=isset($HTTP_RAW_POST_DATA) ? file_get_contents($HTTP_RAW_POST_DATA) : file_get_contents("php://input");
		$this->raw=json_decode($this->raw);
		$this->setInterfParame();
		$this->setArrDatSol();
		
		$file="../".DIR_APLICA_CENTRAL."/lib/InterfSolicitud.inc";
		if(!file_exists($file)){
			$file="../lib/InterfSolicitud.inc";
		}
		if(file_exists($file)){
			include($file);
			$this->_request=isset($_REQUEST["cod_servic"]) || isset($_REQUEST["option"]) ? $_REQUEST : ( isset($GLOBALS["cod_servic"]) || isset($GLOBALS["option"]) ? $GLOBALS : null );
			$option=array_key_exists("option", $this->_request) ? $this->_request["option"] : 0;
			settype($option,"integer");
			
			if(strpos($_SERVER["PHP_SELF"],"ins_solici_solici.php") && !$this->validateInterfParame())
				die("Requiere activar la interfaz con Faro, consulte con su proveedor.");

			if($option>=1 and $option<=4)
				$this->InterfSolicitud = new InterfSolicitud();

			switch($option)
			{
				case 0:
					$this->onCreateForm();
				break;
				case 1:
					$this->setSoliciRuta();
				break;
				case 2:
					$this->setSoliciSegimientoEspecial();
				break;
				case 3:
					$this->setSoliciPQR();
				break;
				case 4:
					$this->setSoliciOtros();
				break;
				case 94:
					$this->getInterfParameVolcar();
				break;
				case 95:
					$this->onRemoveFile();
				break;
				case 96:
					$this->onUploadFile();
				break;
				case 97:
					$this->getPlacasDespacho();
				break;
				case 98:
					$this->getCiudad();
				break;
				case 99:
					$this->getUsuario();
				break;
			}
		}else{
			die("Not found required files to Solici_solici::Main (".$file.")");
		}
	}

	function validateInterfParame(){
		$data=$this->getInterfParame(null);
		if(is_array($data) && sizeof($data)>0){
			return true;
		}else{
			return false;
		}
	}

	function encrypt($str){
		$str2="";
		for($a=0;$a<strlen($str);$a++){
	        $str2.=str_pad(dechex(ord($str[$a])),2,0,STR_PAD_LEFT);
		}
		return $str2;
	}
	function decrypt($str){
		$str4="";
		for($a=0;$a<strlen($str);$a++){
		        $str3=$str[$a].$str[$a+1];
		        $str4.=chr(hexdec($str3));
		        $a+=1;
		}
		return $str4;
	}

	/*
	return > array("tip_format","bin_archiv")
	- tip_format: formato del archivo (xlsx, xls, doc, docx, pdf, jpg,
png, jpeg, zip, rar)
	- bin_archiv: string (binario del archivo) en base64
	*/
	function getFileComplex($file){
		if(file_exists($file)){
			$pr = pathinfo($file);
			return array("tip_format"=>$pr['extension'],"bin_archiv"=>$this->getBinaryFile($file));
		}
		return array("tip_format"=>"","bin_archiv"=>"");
	}
	function getBinaryFile($file){
		$fn = $file;
		$g = fopen($fn, "r");
		$c = fread($g, filesize($fn));
		fclose($g);
		if($this->unlink_file){
			@unlink($file);
		}
		return base64_encode($c);
	}
	function checkFile($type,$size){
		switch ($type) {
			case 'image/png':
			case 'image/jpeg':
			case 'application/pdf':
			case 'application/x-excel':
			case 'application/x-compressed':
				if($size>0 and $size<=$this->max_file_size){
					return true;
				}
			break;
		}
		return false;
	}

	function getNewFileName($filename){
		if(!empty($filename)){
			$file_p=explode(".",$filename);
			$filename_ext=is_array($file_p) ? $file_p[sizeof($file_p)-1] : "";
			$filename_hash=md5($filename.time());
			$filename_new=strlen($filename_ext)>0 ? "$filename_hash.$filename_ext" : $filename_hash;
			return $filename_new;
		}else{
			return $filename;
		}
	}
	function onRemoveFile(){
		if(gettype($this->raw)=="object"){
			if(gettype($this->raw->hash)=="string"){
				$filename=$this->decrypt($this->raw->hash);
				if(@unlink($filename)){
					echo json_encode(array(
    					//"message"=>"The file $filename has been removed",
    					"message"=>"El archivo $filename fue eliminado."
    				));
				}else{
					echo json_encode(array(
    					//"message"=>"The file $filename it could not removed",
    					"message"=>"El archivo $filename no se pudo eliminar."
    				));
				}
			}
		}
	}
	function onUploadFile(){
		if(isset($_FILES) && sizeof($_FILES)>0){
				$file=$_FILES[0];  						
				$filename=$file['name'];
				$filetype=$file['type'];
				$filesize=$file['size'];
				$fileerror=$file['error'];
				$target=$this->tmp_dir_path.$this->getNewFileName($file['name']);
				if($this->checkFile( $filetype, $filesize )){
					if(!file_exists($target)){
        			if(move_uploaded_file($file['tmp_name'],$target)){
        				echo json_encode(array(
        					//"message"=>"The file $filename has valid",
        					"message"=>"El archivo '$filename' es v&aacute;lido",
        					"filename"=>$filename,
        					"url"=>$this->encrypt($target),
        					"type"=>$filetype,
        					"size"=>$filesize,
        					"error"=>$fileerror
        				));
        			}else{
        				echo json_encode(array(
        					//"message"=>"Internal error to move_uploaded_file, please, contact your provider.",
        					"message"=>"Error interno, consulte con su proveedor (id:347f).",
        					"filename"=>$filename,
        					"url"=>"",
        					"type"=>$filetype,
        					"size"=>$filesize,
        					"error"=>$fileerror
        				));
        			}
        		}
    		}else{
    			echo json_encode(array(
					//"message"=>"The file $filename has not valid",
					"message"=>"El archivo '$filename' no es v&aacute;lido.",
					"filename"=>$filename,
					"url"=>"",
					"type"=>$filetype,
					"size"=>$filesize,
					"error"=>$fileerror
				));
    		}
		}
	}

	function getPlacasDespacho(){
		$sql='SELECT a.num_placax as "key", a.num_placax as "value" FROM tab_despac_vehige a, tab_despac_despac b '.
					'WHERE '.
					'a.num_despac = b.num_despac AND '.
					'b.ind_anulad = \'R\' AND '.
					'a.ind_activo = \'S\' AND '.
					'b.ind_planru = \'S\' AND '.
					'b.fec_salida IS NOT NULL AND '.
					'b.fec_llegad IS NULL';
		$consulta = new Consulta( $sql, $this->conexion );
		$datos    = $consulta->ret_matriz( 'a' );
		//$datosc   = $this->arrIso2ascii($datos);
		print_r(json_encode($datos));
	}

	function getInterfParameVolcar(){
		print_r($this->getInterfParame(null));
	}
	
	function getInterfParame($k){
		if(isset($k) && gettype($k)=="string"){
			if(is_array($this->interfParame) && array_key_exists($k, $this->interfParame)){
				if($k=="clv_usuari"){$this->interfParame[$k]=base64_decode($this->interfParame[$k]);}
				return $this->interfParame[$k];
			}
		}else{
			return $this->interfParame;
		}
	}
	function existTable($db,$arrTbn){
		$sql="show tables from $db";
		$consulta = new Consulta( $sql, $this->conexion );
		$datos = $consulta->ret_matriz( 'a' );
		$arrRes=array();
		foreach($datos as $row){
			foreach($row as $idCol => $valCol){
				if(in_array($valCol,$arrTbn)){
					array_push($arrRes, $valCol);
				}
			}
		}
		return $arrRes;
	}
	function setInterfParame(){
		$cod_transp = $this->getTranspUsuari();
		$datos = array(
						"cod_transp" => $cod_transp,
						"nom_operad" => "InterfSolicitud",
						"nom_usuari" => "InterfSolicitud",
						"clv_usuari" => "c09HeWcyMXEtYl9f",
						"ind_estado" => "1"
					);
		$this->interfParame = $datos;
	}

	function getCiudad(){
		//$sql="select a.cod_ciudad, a.nom_ciudad, b.nom_depart from tab_genera_ciudad a inner join tab_genera_depart b on b.cod_depart=a.cod_depart order by b.nom_depart,a.nom_ciudad";
		$sql='select distinct a.cod_ciudad as "key",concat(a.nom_ciudad," - ",b.nom_depart) as "value" from tab_genera_ciudad a left join tab_genera_depart b on b.cod_depart=a.cod_depart where a.cod_ciudad>1 and a.nom_ciudad is not null and b.nom_depart is not null order by b.nom_depart,a.nom_ciudad';
		$consulta = new Consulta( $sql, $this->conexion );
		$datos    = $consulta->ret_matriz( 'a' );
		$datosc   = $this->arrIso2ascii($datos);
		print_r(json_encode($datosc));
	}

	function arrIso2ascii($arr){
		if(sizeof($arr)>0){
			foreach($arr as $k => $v){
				$arr[$k]=$v;
				foreach($v as $k2 => $v2){
					$arr[$k][$k2] = $this->iso2ascii($v2);
				}
			}
		}
		return $arr;
	}

	function getUsuario(){
		//$usuario=isset($_SESSION["datos_usuario"]) ? $_SESSION["datos_usuario"] : array();
		$nom_solici=@$_SESSION["datos_usuario"]["nom_usuari"];
		$mai_solici=@$_SESSION["datos_usuario"]["usr_emailx"];
		$fij_solici="";
		$cel_solici="";

		$usuario=<<<EOF
[
	{"key":"nom_solici","value":"$nom_solici"},
	{"key":"mai_solici","value":"$mai_solici"},
	{"key":"fij_solici","value":"$fij_solici"},
	{"key":"cel_solici","value":"$cel_solici"}
]
EOF;
		print_r(json_encode(json_decode($usuario)));
	}

	/*
	* Pintar el onCreateForm para realizar solicitudes
	*/
	function onCreateForm(){
		if($this->validateInterfParame()){
			$window=isset($this->_request["window"]) ? $this->_request["window"] : "";
			$cod_servic=isset($this->_request["cod_servic"]) ? $this->_request["cod_servic"] : "";
			$option=isset($this->_request["option"]) ? $this->_request["option"] : "";

			$error=false;
			$dirsolifa='../'.DIR_APLICA_CENTRAL.'/solifa/';
			$filejs='../'.DIR_APLICA_CENTRAL.'/js/mod_solici_solici.js';
			$filecss='../'.DIR_APLICA_CENTRAL.'/estilos/mod_solici_solici.css';
			if(file_exists($filejs) && file_exists($filecss)){
				//IncludeJS( "mod_solici_solici.js" );//No se incluye, porque se requiere enviar un dato
				//IncludeCSS no aplica, la crearon seguramente para modificar estilos a traves de javascript

				$formulario = new Formulario ("index.php","post","<div>SOL. A ASISTENCIA LOGISTICA</div>","form_list");
				$js='<script id="_45462213DEf">'.
			        'var ds="'.$dirsolifa.'",'.
			        'cs=parseInt("'.$cod_servic.'"),'.
			        'wd="'.$window.'",'.
			        'ot=parseInt("'.$option.'"),'.
			        'script = document.createElement("script");'.
			        'script.type = "text/javascript";'.
			        'script.src = "'.$filejs.'?t='.rand(10,99).'";'.
			        'document.getElementsByTagName("head")[0].appendChild(script);'.
				'</script>';
	      		echo '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/estilos/mod_solici_solici.css">';
				echo '<div class="ins_solici_solici">Cargando...'.$js.'</div>';
				echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/jquery17.js"></script>';
				$formulario->cerrar();

			}else{
				die("Not found required files to Solici_solici::onCreateForm");
			}
		}else{
			$formulario = new Formulario ("","get","<div>SOLICITUD A FARO</div>","void");
			echo '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/estilos/mod_solici_solici.css">';
			echo '<div class="ins_solici_solici alert alert-warning"><strong>Requiere activar la interfaz con Faro, consulte con su proveedor.</strong></div>';
			$formulario->cerrar();
		}
	}

	function setSoliciRuta(){
		$this->arr_datsol["cod_tipsol"]=1;
		$this->arr_datsol["nom_tipsol"]="Creacion de rutas";
		if(gettype($this->raw)=="object"){
			if(gettype($this->raw->solici)=="object" && gettype($this->raw->rutaxx)=="array"){
				if(sizeof($this->raw->rutaxx)>0){
					$this->arr_datsol["nom_solici"]=$this->raw->solici->nom_solici;
					$this->arr_datsol["mai_solici"]=$this->raw->solici->mai_solici;
					$this->arr_datsol["fij_solici"]=$this->raw->solici->fij_solici;
					$this->arr_datsol["cel_solici"]=$this->raw->solici->cel_solici;
					$this->arr_datsol["cod_usrsol"]=NULL;
					foreach ($this->raw->rutaxx as $id => $obj) {
						$this->InterfSolicitud->setSoliciRuta(
							$this->arr_datsol,
							$obj
						);
					}
					$r = json_encode($this->InterfSolicitud->getResult());
					
					if (strpos($r,"code_resp:1000;") || strpos($r,"code_resp: 1000;")) {
					/**************** Envio mail *********************/
					$explode = explode(",", $r); 
					$explode = explode(":", $explode[4]);
					$num_solici = explode(".",$explode[1]);
				
			      	$dataMail = (object) array(
			                              'nom_solici'  =>  'Creacion Ruta',
			                              'nom_cliente'  =>  $this->getTransSolici($num_solici[0])[0]['abr_tercer'],
			                              'date'  =>  date("Y-m-d H:i:s"),
			                              'num_solici'  =>  $num_solici[0],
			                              'cod_usuari'  =>  str_replace("'"," ",$_SESSION["datos_usuario"]["nom_usuari"]),
										  'year'  =>  date("Y"),
										  'asunto' => "Creacion Ruta: ".$this->getTransSolici($num_solici[0],"1")[0]['origen']." - ".$this->getTransSolici($num_solici[0], "1")[0]['destino']." Via ".$this->getTransSolici($num_solici[0])[0]['nom_viaxxx'],
			                              'cod_estado'  =>  "Abierta",
			                              'obs_solici'  =>  "Solicitud De Creacion Ruta: ".$this->getTransSolici($num_solici[0],"1")[0]['origen']." - ".$this->getTransSolici($num_solici[0], "1")[0]['destino']." Via ".$this->getTransSolici($num_solici[0])[0]['nom_viaxxx'],
			                              'mailTo'  =>  $this->getTransSolici($num_solici[0])[0]['dir_usrmai'].",".$_SESSION["datos_usuario"]["usr_emailx"].",".SUPERVISOR.",maribel.garcia@eltransporte.org",
			                          );
			      	$this->sendMailSolifa($dataMail);
					$obs_config = $this->getTransSolici($num_solici[0], "1")[0]['obs_config'];
				}
				echo "<pre style='color:green'>";
					echo $r;	
				echo "</pre>";
				echo "<pre style='color:red'>";
					print_r($obs_config);
				echo "</pre>";
				}
			}
		}
	}

	function setSoliciSegimientoEspecial(){
		$this->arr_datsol["cod_tipsol"]=2;
		$this->arr_datsol["nom_tipsol"]="Seguimiento especial";

		if(gettype($this->raw)=="object"){
			if(gettype($this->raw->solici)=="object"
				&& (gettype($this->raw->ind_segesp)=="integer" || gettype($this->raw->ind_segesp)=="string")
				&& gettype($this->raw->fec_iniseg)=="string"
				&& gettype($this->raw->fec_finseg)=="string"
				&& gettype($this->raw->lis_placax)=="string"
				&& gettype($this->raw->obs_solici)=="string"
				){
				$this->arr_datsol["nom_solici"]=$this->raw->solici->nom_solici;
				$this->arr_datsol["mai_solici"]=$this->raw->solici->mai_solici;
				$this->arr_datsol["fij_solici"]=$this->raw->solici->fij_solici;
				$this->arr_datsol["cel_solici"]=$this->raw->solici->cel_solici;
				$this->arr_datsol["cod_usrsol"]=NULL;
				settype($this->raw->ind_segesp,"integer");
				$this->InterfSolicitud->setSoliciSegimientoEspecial(
					$this->arr_datsol,
					$this->raw->ind_segesp,	
					$this->raw->fec_iniseg,	
					$this->raw->fec_finseg,	
					$this->raw->lis_placax,	
					$this->raw->obs_solici
				);
				$r = json_encode($this->InterfSolicitud->getResult());

				if (strpos($r,"code_resp:1000;") || strpos($r,"code_resp: 1000;")) {
					/**************** Envio mail *********************/
					$explode = explode(",", $r); 
					$explode = explode(":", $explode[6]);
					$num_solici = explode(".",$explode[1]);
				
			      	$dataMail = (object) array(
			                              'nom_solici'  =>  'Seguimiento especial',
			                              'nom_cliente'  =>  $this->getTransSolici($num_solici[0])[0]['abr_tercer'],
			                              'date'  =>  date("Y-m-d H:i:s"),
			                              'num_solici'  =>  $num_solici[0],
			                              'cod_usuari'  =>  str_replace("'"," ",$_SESSION["datos_usuario"]["nom_usuari"]),
										  'year'  =>  date("Y"),
										  'asunto' => "Solicitud De Seguimiento especial a ".$this->getTransSolici($num_solici[0], "2")[0]['nom_subtip'],
			                              'cod_estado'  =>  "Abierta",
			                              'obs_solici'  =>  "Solicitud De Seguimiento especial a ".$this->getTransSolici($num_solici[0], "2")[0]['nom_subtip'],
			                              'mailTo'  =>  $this->getTransSolici($num_solici[0])[0]['dir_usrmai'].",".$_SESSION["datos_usuario"]["usr_emailx"].",".SUPERVISOR.",maribel.garcia@eltransporte.org",
			                          );
			      	$this->sendMailSolifa($dataMail);
					$obs_config = $this->getTransSolici($num_solici[0], "2")[0]['obs_config'];
				}
				echo "<pre style='color:green'>";
					echo $r;	
				echo "</pre>";
				echo "<pre style='color:red'>";
					print_r($obs_config);
				echo "</pre>";
			}
		}
	}

	function setSoliciPQR(){
		$this->arr_datsol["cod_tipsol"]=3;
		$this->arr_datsol["nom_tipsol"]="PQR";

		if(gettype($this->raw)=="object"){
			if(gettype($this->raw->solici)=="object"
				&& (gettype($this->raw->ind_pqrsxx)=="integer" || gettype($this->raw->ind_pqrsxx)=="string")
				&& gettype($this->raw->nom_pqrsxx)=="string"
				&& gettype($this->raw->obs_pqrsxx)=="string"
				){
				$this->arr_datsol["nom_solici"]=$this->raw->solici->nom_solici;
				$this->arr_datsol["mai_solici"]=$this->raw->solici->mai_solici;
				$this->arr_datsol["fij_solici"]=$this->raw->solici->fij_solici;
				$this->arr_datsol["cel_solici"]=$this->raw->solici->cel_solici;
				$this->arr_datsol["cod_usrsol"]=NULL;
				settype($this->raw->ind_pqrsxx,"integer");
				$this->InterfSolicitud->setSoliciPQR(
					$this->arr_datsol,
					$this->raw->ind_pqrsxx,	
					$this->raw->nom_pqrsxx,	
					$this->raw->obs_pqrsxx,	
					$this->getFileComplex($this->decrypt($this->raw->fil_archiv))
				);
				switch ($this->raw->ind_pqrsxx) {
					case '1':
						$tipqr = 'peticion';
						break;
					case '2':
						$tipqr = 'queja';
						break;
					case '3':
						$tipqr = 'sugerencia';
						break;
					case '4':
						$tipqr = 'felicitacion';
						break;
					default:
						$tipqr = 'peticion';
						break;
				}
				$r=json_encode($this->InterfSolicitud->getResult());
				if(strpos($r,"code_resp:1000;") || strpos($r,"code_resp: 1000;")){
					//cuando transmita con existo, limpiar del servidor del cliente el archivo
					$this->raw->hash=$this->raw->fil_archiv;
					ob_start();
					$this->onRemoveFile();
					ob_clean();
				}
				if (strpos($r,"code_resp:1000;") || strpos($r,"code_resp: 1000;")) {
					/**************** Envio mail *********************/
					$explode = explode(",", $r); 
					$explode = explode(":", $explode[4]);
					$num_solici = explode(".",$explode[1]);
			      	$dataMail = (object) array(
			                              'nom_solici'  =>  $tipqr,
			                              'nom_cliente'  =>  $this->getTransSolici($num_solici[0])[0]['abr_tercer'],
			                              'date'  =>  date("Y-m-d H:i:s"),
			                              'num_solici'  =>  $num_solici[0],
			                              'cod_usuari'  =>  str_replace("'"," ",$_SESSION["datos_usuario"]["nom_usuari"]),
										  'year'  =>  date("Y"),
										  'asunto' => $this->getTransSolici($num_solici[0])[0]['nom_asunto'],
			                              'cod_estado'  =>  "Abierta",
			                              'obs_solici'  =>  $this->getTransSolici($num_solici[0])[0]['obs_solici'],
			                              'mailTo'  =>  $this->getTransSolici($num_solici[0])[0]['dir_usrmai'].",".$_SESSION["datos_usuario"]["usr_emailx"].",".SUPERVISOR.",maribel.garcia@eltransporte.org",
			                          );
			      	$this->sendMailSolifa($dataMail);
			      	$obs_config = $this->getTransSolici($num_solici[0])[0]['obs_config'];
				}
				echo "<pre style='color:green'>";
					echo $r;	
				echo "</pre>";
				echo "<pre style='color:red'>";
					print_r($obs_config);
				echo "</pre>";
			}
		}
	}

	function setSoliciOtros(){
		$this->arr_datsol["cod_tipsol"]=4;
		$this->arr_datsol["nom_tipsol"]="Otras";

		if(gettype($this->raw)=="object"){
			if(gettype($this->raw->solici)=="object"
				&& gettype($this->raw->nom_otroxx)=="string"
				&& gettype($this->raw->obs_otroxx)=="string"
				){
				$this->arr_datsol["nom_solici"]=$this->raw->solici->nom_solici;
				$this->arr_datsol["mai_solici"]=$this->raw->solici->mai_solici;
				$this->arr_datsol["fij_solici"]=$this->raw->solici->fij_solici;
				$this->arr_datsol["cel_solici"]=$this->raw->solici->cel_solici;
				$this->arr_datsol["cod_usrsol"]=NULL;							
				$this->InterfSolicitud->setSoliciOtros(
					$this->arr_datsol,
					$this->raw->nom_otroxx,	
					$this->raw->obs_otroxx,	
					$this->getFileComplex($this->decrypt($this->raw->fil_archiv))
				);
				$r=json_encode($this->InterfSolicitud->getResult());
				if(strpos($r,"code_resp:1000;") || strpos($r,"code_resp: 1000;")){
					//cuando transmita con existo, limpiar del servidor del cliente el archivo
					$this->raw->hash=$this->raw->fil_archiv;
					ob_start();
					$this->onRemoveFile();
					ob_clean();
				}
				if (strpos($r,"code_resp:1000;") || strpos($r,"code_resp: 1000;")) {
					/**************** Envio mail *********************/
					$explode = explode(",", $r); 
					$explode = explode(":", $explode[4]);
					$num_solici = explode(".",$explode[1]);
				
			      	$dataMail = (object) array(
			                              'nom_solici'  =>  'Gestion Solicitud',
			                              'nom_cliente'  =>  $this->getTransSolici($num_solici[0])[0]['abr_tercer'],
			                              'date'  =>  date("Y-m-d H:i:s"),
			                              'num_solici'  =>  $num_solici[0],
			                              'cod_usuari'  =>  str_replace("'"," ",$_SESSION["datos_usuario"]["nom_usuari"]),
										  'year'  =>  date("Y"),
										  'asunto' => $this->getTransSolici($num_solici[0])[0]['nom_asunto'],
			                              'cod_estado'  =>  "Abierta",
			                              'obs_solici'  =>  $this->getTransSolici($num_solici[0])[0]['obs_solici'],
			                              'mailTo'  =>  $this->getTransSolici($num_solici[0])[0]['dir_usrmai'].",".$_SESSION["datos_usuario"]["usr_emailx"].",".SUPERVISOR.",maribel.garcia@eltransporte.org",
			                          );
			      	$this->sendMailSolifa($dataMail);
			      	$obs_config = $this->getTransSolici($num_solici[0])[0]['obs_config'];
				}
				echo "<pre style='color:green'>";
					echo $r;	
				echo "</pre>";
				echo "<pre style='color:red'>";
					print_r($obs_config);
				echo "</pre>";
			}
		}
	}

	function iso2ascii($str) { 
		$arr=array( 
		  chr(161)=>'A', chr(163)=>'L', chr(165)=>'L', chr(166)=>'S', chr(169)=>'S', 
		  chr(170)=>'S', chr(171)=>'T', chr(172)=>'Z', chr(174)=>'Z', chr(175)=>'Z', 
		  chr(177)=>'a', chr(179)=>'l', chr(181)=>'l', chr(182)=>'s', chr(185)=>'s', 
		  chr(186)=>'s', chr(187)=>'t', chr(188)=>'z', chr(190)=>'z', chr(191)=>'z', 
		  chr(192)=>'R', chr(193)=>'A', chr(194)=>'A', chr(195)=>'A', chr(196)=>'A', 
		  chr(197)=>'L', chr(198)=>'C', chr(199)=>'C', chr(200)=>'C', chr(201)=>'E', 
		  chr(202)=>'E', chr(203)=>'E', chr(204)=>'E', chr(205)=>'I', chr(206)=>'I', 
		  chr(207)=>'D', chr(208)=>'D', chr(209)=>'N', chr(210)=>'N', chr(211)=>'O', 
		  chr(212)=>'O', chr(213)=>'O', chr(214)=>'O', chr(216)=>'R', chr(217)=>'U', 
		  chr(218)=>'U', chr(219)=>'U', chr(220)=>'U', chr(221)=>'Y', chr(222)=>'T', 
		  chr(223)=>'s', chr(224)=>'r', chr(225)=>'a', chr(226)=>'a', chr(227)=>'a', 
		  chr(228)=>'a', chr(229)=>'l', chr(230)=>'c', chr(231)=>'c', chr(232)=>'c', 
		  chr(233)=>'e', chr(234)=>'e', chr(235)=>'e', chr(236)=>'e', chr(237)=>'i', 
		  chr(238)=>'i', chr(239)=>'d', chr(240)=>'d', chr(241)=>'n', chr(242)=>'n', 
		  chr(243)=>'o', chr(244)=>'o', chr(245)=>'o', chr(246)=>'o', chr(248)=>'r', 
		  chr(249)=>'u', chr(250)=>'u', chr(251)=>'u', chr(252)=>'u', chr(253)=>'y', 
		  chr(254)=>'t' 
		); 
		return strtr($str,$arr); 
	} 

	
	function getTranspUsuari()
	{
		try
		{
			$mSql = "SELECT a.clv_filtro
						FROM ".BASE_DATOS.".tab_aplica_filtro_perfil a
						WHERE a.cod_perfil = ".$_SESSION['datos_usuario']['cod_perfil']." 
					";
			$mConsult = new Consulta( $mSql, $this->conexion );
			$result = $mConsult -> ret_matriz('a');
			if(sizeof($result)>0)
			{
				return $result[0]['clv_filtro'];
			}
			else
			{
				return 0;
			}
		}
		catch (Exception $e)
		{
			echo "Error en la funcion getTranspUsuari: ", $e->getMessage();
		}
	}

	function sendMailSolifa($data = NULL)
	{
	    try
	    {	
		    $mCabece = 'MIME-Version: 1.0' . "\r\n";
		    $mCabece .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		    $mCabece .= 'From: Sol. Asistencia Logistica <webmaster@grupooet.com>' . "\r\n";
		    $tmpl_file = '/var/www/html/ap/satt_standa/planti/pla_solifa_abierta.html';
		    $thefile = implode("", file($tmpl_file));
		    $thefile = addslashes($thefile);
		    $thefile = "\$r_file=\"" . $thefile . "\";";
		    eval($thefile);
		    $mHtmlxx = $r_file;
		    if($_SERVER['HTTP_HOST'] == 'dev.intrared.net:8083')
		    {
		      	$mailToS = "edward.serrano@intrared.net, maribel.garcia@eltransporte.org";
		    }
		    else
		    {
		      	$mailToS = $data->mailTo;
		    }
		    mail( $mailToS, "sol. ".$data->asunto, '<div name="_faro_07">' . $mHtmlxx . '</div>', $mCabece );
	    }
	    catch(Exception $e)
	    {
	      return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
	    }
	}

	function getTransSolici($num_solici,$proceso)
	{
	    try{
	    	if ($proceso == '1') {
	    		$campos = ", d.nom_ciudad AS origen, e.nom_ciudad AS destino, a.nom_viaxxx ";
	    		$joins = "INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d ON d.cod_ciudad = a.cod_ciuori
		              INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e ON e.cod_ciudad = a.cod_ciudes";
	    	}elseif($proceso == '2'){
    			$campos = ", f.nom_subtip ";
    			$joins = "INNER JOIN ".BASE_DATOS.".tab_solici_subtip f ON f.cod_tipsol = a.cod_tipsol AND f.cod_subtip = a.cod_subtip";
	    	}
		    $sql =  "SELECT c.abr_tercer, b.dir_usrmai, a.obs_solici, a.nom_asunto, h.obs_config $campos FROM ".BASE_DATOS.".tab_solici_solici a ".
		              "INNER JOIN ".BASE_DATOS.".tab_solici_datosx b ON a.cod_solici = b.cod_solici ".
		              "INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON b.cod_transp = c.cod_tercer ".
		              "INNER JOIN ".BASE_DATOS.".tab_solici_config h ON h.cod_tipsol = a.cod_tipsol AND h.cod_subtip = a.cod_subtip ".
		              "$joins WHERE ".
					  "a.num_solici=$num_solici";
			
		    $consulta = new Consulta( $sql, $this->conexion );
			return $consulta->ret_matrix( 'a' );
	    }
	    catch(Exception $e)
	    {
	    	return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
	    }
  	}

  	function getEstado($cod_estado)
	{
	    try{
		    $sql =  "SELECT * FROM ".BASE_DATOS.".tab_solici_estado ".
		              "WHERE ".
		              "cod_estado=$cod_estado";
		    $consulta = new Consulta( $sql, $this->conexion );
			return $consulta->ret_matrix( 'a' );
	    }
	    catch(Exception $e)
	    {
	    	return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
	    }
  	}

}

$proceso = new Solici_solici();