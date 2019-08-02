<?php
//ini_set('error_reporting', E_ALL);
//ini_set("display_errors", 1);
ini_set('memory_limit','1024M');
@session_start();
class Solici_solici
{
	var $conexion, $cod_aplica,	$usuario, $usuario2, $cod_tercer, $arr_datsol, $interfParame, $InterfSolicitud, $_request,
		$raw, $input_post, $input_globals, $input_get, $max_file_size=2000000, $tmp_dir_path="/tmp/",$unlink_file=false,$cSession;

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
		try{
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
			$this->cSession = $_SESSION["datos_usuario"];
			$error=array();
			/*if(!defined("URL_INTERF_FAROXX")){
				array_push($error, "Solici_solici::Main > Const URL_INTERF_FAROXX does not exists");
			}
			if(!defined("USR_INTERF_FAROXX")){
				array_push($error, "Solici_solici::Main > Const USR_INTERF_FAROXX does not exists");
			}
			if(!defined("PWD_INTERF_FAROXX")){
				array_push($error, "Solici_solici::Main > Const PWD_INTERF_FAROXX does not exists");
			}*/
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
			//$this->setArrDatSol();	
			
			$file="../".DIR_APLICA_CENTRAL."/lib/InterfSolicitud.inc";
			if(!file_exists($file)){
				$file="../lib/InterfSolicitud.inc";
			}
			//if(file_exists($file)){
				//include($file);
				$this->_request=isset($_REQUEST["cod_servic"]) || isset($_REQUEST["option"]) ? $_REQUEST : ( isset($GLOBALS["cod_servic"]) || isset($GLOBALS["option"]) ? $GLOBALS : null );
				if(empty($this->_request))
					die("Sin par&aacute;metros");
				
				$option=array_key_exists("option", $this->_request) ? $this->_request["option"] : 0;
				settype($option,"integer");
				
				if(strpos($_SERVER["PHP_SELF"],"inf_solici_solici.php") && !$this->validateInterfParame())
					die("Requiere activar la interfaz con Faro, consulte con su proveedor.");

				if($option>=1 and $option<=4)
					$this->InterfSolicitud = new InterfSolicitud();

				switch($option)
				{
					case 0:
						$this->onCreateForm();
					break;

					/*case 1:
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
					break;*/


					case 5:
						$this->getInfGeneral();
					break;
					case 6:
						$this->getInfGeneralDetalle();
					break;
					case 7:
						$this->getInfGeneralDetalle2();					
					break;

					case 8:
						$this->getInfGestiona();
					break;
					case 9:
						$this->getInfGestionaDetalle();
					break;
					case 10:
						$this->getInfGestionaDetalle2();
					break;

					case 11:
						$this->getInfEnProceso();
					break;
					case 12:
						$this->getInfEnProcesoDetalle();
					break;
					case 13:
						$this->getInfEnProcesoDetalle2();
					break;

					case 14:
						$this->getInfPorGestionar();
					break;
					case 15:
						$this->getInfPorGestionarDetalle();
					break;
					case 16:
						$this->getInfPorGestionarDetalle2();
					break;
					case 17:
						$this->getInfDetalleRespuesta();
					break;
					case 18:
						$this->getInfDetalleSeguimiento();
					break;


					case 50:
						$this->setSoliciRespuesta();
					break;


					case 93:
						$this->getTercerTransp();
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
			//}else{
				//die("Not found required files to Solici_solici::Main (".$file.")");
			//}
		}catch(Exception $e){
			print_r($e);
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

	function setSoliciFile($fil_archiv){
		try{
			$t=array("code"=>"","error"=>"","status"=>false,"path"=>"");
			//$faro_file_path=DirSolici;//"/var/www/html/ap/satt_faro/files/solici/";
			$fn="";
			if(!empty($fil_archiv) && !empty($fil_archiv->tip_format)){
				$fe=!empty($fil_archiv->tip_format) ? '.'.$fil_archiv->tip_format : $fil_archiv->tip_format;
				//$fn=$faro_file_path.md5(rand(1000,9999).time().$fe).$fe;
				$fn=md5(rand(1000,9999).time().$fe).$fe;
				$t["path"]=$fn;
				if(file_put_contents(DirSolici.$fn, base64_decode($fil_archiv->bin_archiv))){
					$t["status"]=true;
				}else{
					$t["code"]=1005;
					$t["error"]="Error de escritura";
				}
			}
			return json_decode(json_encode($t));
		}catch(Exception $e){
			return json_decode(json_encode(array("code"=>$e->getCode(),"error"=>$e->getMessage(),"status"=>false,"path"=>"")));
		}
	}
	function setSoliciRespuesta(){
		try{
			$error=array();
			//insertar seguimiento
			if(gettype($this->raw)=="object"){
				//echo "Validando archivo: <br>";
				if(gettype($this->raw->dir_archiv)=="string" && strlen($this->raw->dir_archiv)>0){
					$this->raw->dir_archiv=$this->decrypt($this->raw->dir_archiv);
					$fapi=pathinfo($this->raw->dir_archiv);
					$dir_destino="../../" . BASE_DATOS ."/". URL_SOLICI;
					$fil_destino=$dir_destino . $fapi["basename"];
					if(file_exists($this->raw->dir_archiv)){
						if(file_put_contents($fil_destino, file_get_contents($this->raw->dir_archiv))){
							@unlink($this->raw->dir_archiv);
							$this->raw->dir_archiv=$fapi["basename"];
						}else{
							$error="El Archivo no pudo ser movido al directorio: $dir_destino";
							print json_encode(array("message"=>$error,"status"=>"error"));
							return false;
						}
					}else{
						$error="El Archivo subido como ".$this->raw->dir_archiv." NO existe, consulte con el Administrador de la aplicaci&oacute;n";
						print json_encode(array("message"=>$error,"status"=>"error"));
						return false;
					}
				}
			
				$formato =	"insert into tab_solici_seguim (num_solici,cod_estado,obs_seguim,dir_archiv,usr_creaci,fec_creaci) values(%d,%d,'%s','%s','%s',%s);";
				$sql=sprintf($formato,$this->raw->num_solici,$this->raw->cod_estado,$this->raw->obs_seguim,$this->raw->dir_archiv,@$_SESSION["datos_usuario"]["cod_usuari"],"now()");
				
				$consulta = new Consulta( $sql, $this->conexion );
				print json_encode(array("message"=>"Registro con &eacute;xito el seguimiento","status"=>"success"));
				//print json_encode(array("message"=>$sql,"status"=>"success"));
				/**************** Envio mail *********************/

		      	$dataMail = (object) array(
		                              'nom_solici'  =>  'Gestion Solicitud',
		                              'nom_cliente'  =>  $this->getTransSolici($this->raw->num_solici)[0]['abr_tercer'],
		                              'date'  =>  date("Y-m-d H:i:s"),
		                              'num_solici'  =>  $this->raw->num_solici,
		                              'cod_usuari'  =>  str_replace("'"," ",$_SESSION["datos_usuario"]["nom_usuari"]),
									  'year'  =>  date("Y"),
									  'asunto' => "Gestion Solicitud",
									  'inform'=> $this->getTransSolici($this->raw->num_solici)[0]['dir_usrmai'],
		                              'cod_estado'  =>  $this->getEstado($this->raw->cod_estado)[0]['nom_estado'],
		                              'obs_solici'  =>  $this->raw->obs_seguim,
		                              'mailTo'  =>  $this->getTransSolici($this->raw->num_solici)[0]['dir_usrmai'].",".$_SESSION["datos_usuario"]["usr_emailx"].",".SUPERVISOR.",maribel.garcia@eltransporte.org",
		                          );
		      	$this->sendMailSolifa($dataMail);
				return false;
			}
		}catch(Exception $e){
			print json_encode(array("message"=>$e->getMessage(),"status"=>"error"));
			return false;
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
		$datos    = $consulta->ret_matrix( 'a' );
		//$datosc   = $this->arrIso2ascii($datos);
		print_r(json_encode($datos));
	}

	function getTercerTransp(){
		$trans = $this->getTransLogin();
		$sql='select t.cod_tercer as "key", t.abr_tercer as "value"  from tab_tercer_tercer t inner join (select distinct a.cod_transp from satt_faro.tab_solici_datosx a order by a.cod_transp) sd on sd.cod_transp=t.cod_tercer inner join tab_tercer_activi ta on ta.cod_tercer=t.cod_tercer and ta.cod_activi = 1 '.($trans != NULL?' and t.cod_tercer='.$trans:'');
		$consulta = new Consulta( $sql, $this->conexion );
		$datos    = $consulta->ret_matrix( 'a' );
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
		$datos = $consulta->ret_matrix( 'a' );
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
		$rtbn=$this->existTable(BASE_DATOS,array("tab_interf_parame","tab_interf_parame_temp"));
		if(sizeof($rtbn)>0){
			foreach($rtbn as $c => $table){
				$sql='SELECT cod_operad,cod_transp,nom_operad,nom_usuari,clv_usuari,val_timtra,ind_intind,ind_operad,ind_estado,url_webser '.
						'FROM '.BASE_DATOS.'.'.$table.' '.
						'WHERE '.
						'cod_operad=50 and '.
						'ind_estado=1 '.
						'limit 1';
				$consulta = new Consulta( $sql, $this->conexion );
				$datos = $consulta->ret_matrix( 'a' );
				if(sizeof($datos)>0){
					$this->interfParame = $datos[0];
					continue;
				}
			}
		}
	}

	function getCiudad(){
		//$sql="select a.cod_ciudad, a.nom_ciudad, b.nom_depart from tab_genera_ciudad a inner join tab_genera_depart b on b.cod_depart=a.cod_depart order by b.nom_depart,a.nom_ciudad";
		$sql='select distinct a.cod_ciudad as "key",concat(a.nom_ciudad," - ",b.nom_depart) as "value" from tab_genera_ciudad a left join tab_genera_depart b on b.cod_depart=a.cod_depart where a.cod_ciudad>1 and a.nom_ciudad is not null and b.nom_depart is not null order by b.nom_depart,a.nom_ciudad';
		$consulta = new Consulta( $sql, $this->conexion );
		$datos    = $consulta->ret_matrix( 'a' );
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


	function getInfGeneral(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,0);
		if(!empty($r) && sizeof($r)>0){
			echo json_encode($r);
		}else{
			return "";
		}
	}
	function getInfGeneralDetalle(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,2);
		if(!empty($r) && sizeof($r)>0){
			echo json_encode($r);
		}else{
			return "";
		}
	}
	function getInfGeneralDetalle2(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,4);
		if(!empty($r) && sizeof($r)>0){
			echo json_encode($r);
		}else{
			return "";
		}
	}
	function getInfGestiona(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$estados=array(3);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,1);
		if(!empty($r) && array_key_exists(3, $r)){
			echo json_encode($r[3]);
		}else{
			return "";
		}
	}
	function getInfGestionaDetalle(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$estados=array(3);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,3);
		if(!empty($r) && array_key_exists(3, $r)){
			echo json_encode($r[3]);
		}else{
			return "";
		}
	}
	function getInfGestionaDetalle2(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$estados=array(3);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,5);
		if(!empty($r) && sizeof($r)>0){
			echo json_encode($r);
		}else{
			return "";
		}
	}
	function getInfEnProceso(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$estados=array(2);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,1);
		if(!empty($r) && array_key_exists(2, $r)){
			echo json_encode($r[2]);
		}else{
			return "";
		}
	}
	function getInfEnProcesoDetalle(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$estados=array(2);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,3);
		if(!empty($r) && array_key_exists(2, $r)){
			echo json_encode($r[2]);
		}else{
			return "";
		}
	}
	function getInfEnProcesoDetalle2(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$estados=array(2);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,5);
		if(!empty($r) && sizeof($r)>0){
			echo json_encode($r);
		}else{
			return "";
		}
	}
	function getInfPorGestionar(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$estados=array(1);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,1);
		if(!empty($r) && array_key_exists(1, $r)){
			echo json_encode($r[1]);
		}else{
			return "";
		}
	}
	function getInfPorGestionarDetalle(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$estados=array(1);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,3);
		if(!empty($r) && array_key_exists(1, $r)){
			echo json_encode($r[1]);
		}else{
			return "";
		}
	}
	function getInfPorGestionarDetalle2(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$estados=array(1);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,5);
		if(!empty($r) && sizeof($r)>0){
			echo json_encode($r);
		}else{
			return "";
		}
	}

	function getInfDetalleRespuesta(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$estados=array(1,2,3);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,6);
		if(!empty($r) && sizeof($r)>0){
			$r2=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,7);
			echo json_encode(array("solici"=>$r,"seguim"=>$r2));
		}else{
			return "";
		}
	}

	function getInfDetalleSeguimiento(){
		extract($this->getDataFilter(),EXTR_OVERWRITE);
		$estados=array(1,2,3);
		$r=$this->getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estados,7);
		if(!empty($r) && sizeof($r)>0){
			echo json_encode($r);
		}else{
			return "";
		}
	}
	

	function getDataFilter(){
		$window=isset($this->_request["window"]) ? $this->_request["window"] : "";
		$cod_servic=isset($this->_request["cod_servic"]) ? $this->_request["cod_servic"] : "";
		$option=isset($this->_request["option"]) ? $this->_request["option"] : "";
		$lis_transp=isset($this->_request["lis_transp"]) ? $this->_request["lis_transp"] : NULL;
		$fec_inifil=isset($this->_request["fec_inifil"]) ? $this->_request["fec_inifil"] : date("Y-m-d");
		$fec_finfil=isset($this->_request["fec_finfil"]) ? $this->_request["fec_finfil"] : date("Y-m-d");
		$num_solici=isset($this->_request["num_solici"]) ? $this->_request["num_solici"] : NULL;

		$p="/^([0-9]{4})-([01]{1})([0-9]{1})-([0123]{1})([0-9]{1})/";
		if(!preg_match($p, $fec_inifil)){
			die("La fecha no es v&aacute;lida (inicio)");
		}
		if(!preg_match($p, $fec_finfil)){
			die("La fecha no es v&aacute;lida (fin)");
		}

		$fec_mod_max=60 * 60 * 24 * 365 * 100;//diferencia maxima entre fecha 1 y fecha 2 (100 años)
		$fec_inifil="$fec_inifil 00:00:00";
		$fec_finfil="$fec_finfil 23:59:59";
		$fec_finmax=date("Y-m-d")." 23:59:59";
		$fec_inifil_v=strtotime($fec_inifil);
		$fec_finfil_v=strtotime($fec_finfil);
		$fec_finmax_v=strtotime($fec_finmax);
		if(($fec_finfil_v-$fec_inifil_v)>=0 && ($fec_finfil_v-$fec_inifil_v)<$fec_mod_max && ($fec_finmax_v-$fec_finfil_v)>=0){
		}else{
			die("La fecha no esta en el rango permitido");
		}
		if(!empty($num_solici)){
			settype($num_solici,"integer");
			if(!is_numeric($num_solici) || $num_solici<1){
				die("El n&uacute;mero de solicitud no es v&aacute;lido");
			}
		}else{
			$num_solici=htmlentities($num_solici, ENT_QUOTES);
		}

		$lis_transp_a=strpos($lis_transp,",") ? explode(",",$lis_transp) : array($lis_transp);
		$lis_transp_a2=array();
		foreach($lis_transp_a as $v){
			array_push($lis_transp_a2, "'".htmlentities($v, ENT_QUOTES))."'";
		}
		$lis_transp=implode(",",$lis_transp_a);
		$tiposx=array(1,2,3,4);
		$estados=array(1,2,3);
		return array(
			"lis_transp"=>$lis_transp,
			"fec_inifil"=>$fec_inifil,
			"fec_finfil"=>$fec_finfil,
			"num_solici"=>$num_solici,
			"tiposx"=>$tiposx,
			"estados"=>$estados
		);
	}
	function getDateFormat($type,$lang,$value){
		switch ($lang) {
			case 'es':
				$d=array("Domingo","Lunes","Martes","Mi&eacute;rcoles","Jueves","Viernes","S&aacute;bado");
				$m=array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","diciembre");
			break;
			default:
				$d=array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
				$m=array("","January","February","March","April","May","June","July","August","September","October","November","December");
			break;
		}
		switch ($type) {
			case 'format1':
				$str=date("w d n Y",strtotime($value));
				$str1=explode(" ",$str);
				$str1[0]=$d[$str1[0]];
				$str1[2]=$m[$str1[2]];
				$str=implode(" ",$str1);
			break;
			default:
				$str=date("Y-m-d H:i:s",strtotime($value));
			break;
		}
		return $str;
	}
	function getInfSolici($lis_transp,$fec_inifil,$fec_finfil,$num_solici,$tiposx,$estado,$tipo){
		try{
			$where="1>0 ";
			if(!empty($lis_transp)){
				$where.="and b.cod_transp in ($lis_transp) ";
			}
			if(!empty($fec_inifil) && !empty($fec_finfil)){
				$where.="and a.fec_creaci between '$fec_inifil' and '$fec_finfil' ";
			}
			if(!empty($num_solici)){
				$where.="and a.num_solici = $num_solici ";
			}
			if(!empty($tiposx)){
				$where.="and a.cod_tipsol in (".implode(",",$tiposx).") ";
			}
			if(!empty($estado)){
				$where.="and a.cod_estado in (".implode(",",$estado).") ";
			}


			switch ($tipo) {
				case 0:
					$query = "SELECT 
						c.nom_estado,c.cod_estado,count(c.cod_estado) as ces
					FROM ".BASE_DATOS.".tab_solici_solici a 
					inner join ".BASE_DATOS.".tab_solici_datosx b on b.cod_solici=a.cod_solici 
					inner join ".BASE_DATOS.".tab_solici_estado c on c.cod_estado=a.cod_estado 
					inner join ".BASE_DATOS.".tab_solici_tiposx d on d.cod_tipsol=a.cod_tipsol 
						WHERE $where
						group by a.cod_estado order by a.cod_estado ";
				break;
				case 1:
					$query = "SELECT 
						c.nom_estado,c.cod_estado,count(c.cod_estado) as ces, 
						d.nom_tipsol,d.cod_tipsol, count(d.cod_tipsol) as cts 
					FROM ".BASE_DATOS.".tab_solici_solici a 
					inner join ".BASE_DATOS.".tab_solici_datosx b on b.cod_solici=a.cod_solici 
					inner join ".BASE_DATOS.".tab_solici_estado c on c.cod_estado=a.cod_estado 
					inner join ".BASE_DATOS.".tab_solici_tiposx d on d.cod_tipsol=a.cod_tipsol 
						WHERE $where
						group by a.cod_estado,a.cod_tipsol order by a.cod_estado,a.cod_tipsol ";
				break;
				case 2:
					$query = "SELECT 
						substr(a.fec_creaci,1,10) as fecha,
						c.nom_estado,c.cod_estado,count(c.cod_estado) as ces
					FROM ".BASE_DATOS.".tab_solici_solici a 
					inner join ".BASE_DATOS.".tab_solici_datosx b on b.cod_solici=a.cod_solici 
					inner join ".BASE_DATOS.".tab_solici_estado c on c.cod_estado=a.cod_estado 
					inner join ".BASE_DATOS.".tab_solici_tiposx d on d.cod_tipsol=a.cod_tipsol 
						WHERE $where
						group by substr(a.fec_creaci,1,10) desc, a.cod_estado order by a.fec_creaci desc, a.cod_estado ";
				break;
				case 3:
					$query = "SELECT 
						b.cod_transp,
						c.nom_estado,c.cod_estado,count(c.cod_estado) as ces, 
						d.nom_tipsol,d.cod_tipsol, count(d.cod_tipsol) as cts 
					FROM ".BASE_DATOS.".tab_solici_solici a 
					inner join ".BASE_DATOS.".tab_solici_datosx b on b.cod_solici=a.cod_solici 
					inner join ".BASE_DATOS.".tab_solici_estado c on c.cod_estado=a.cod_estado 
					inner join ".BASE_DATOS.".tab_solici_tiposx d on d.cod_tipsol=a.cod_tipsol 
						WHERE $where
						group by b.cod_transp,a.cod_estado,a.cod_tipsol order by b.cod_transp,a.cod_estado,a.cod_tipsol ";
				break;


				case 4:
					$query = "SELECT 
						substr(a.fec_creaci,1,10) as fecha,
						c.nom_estado,c.cod_estado,count(c.cod_estado) as ces
					FROM ".BASE_DATOS.".tab_solici_solici a 
					inner join ".BASE_DATOS.".tab_solici_datosx b on b.cod_solici=a.cod_solici 
					inner join ".BASE_DATOS.".tab_solici_estado c on c.cod_estado=a.cod_estado 
					inner join ".BASE_DATOS.".tab_solici_tiposx d on d.cod_tipsol=a.cod_tipsol 
						WHERE $where
						group by substr(a.fec_creaci,1,10) desc, a.cod_estado order by a.fec_creaci desc, a.cod_estado ";
				break;
				case 5:
					$query = "SELECT 
						a.*,
						b.cod_transp,
						b.cod_transp as nom_transp, /*nom_tercer*/
						c.nom_estado,d.nom_tipsol,
						e.nom_subtip
					FROM ".BASE_DATOS.".tab_solici_solici a 
					inner join ".BASE_DATOS.".tab_solici_datosx b on b.cod_solici=a.cod_solici 
					inner join ".BASE_DATOS.".tab_solici_estado c on c.cod_estado=a.cod_estado 
					inner join ".BASE_DATOS.".tab_solici_tiposx d on d.cod_tipsol=a.cod_tipsol 
					left join ".BASE_DATOS.".tab_solici_subtip e on e.cod_subtip=a.cod_subtip and e.cod_tipsol=a.cod_tipsol 
						WHERE $where
						order by a.num_solici,b.fec_creaci desc,b.cod_transp,a.cod_estado,a.cod_tipsol ";
				break;
				case 6:
					$query = "SELECT 
						a.*,
						b.*,
						b.cod_transp as nom_transp, /*este valor se saca filtrando el front con el listado de transportadoras nom_tercer*/
						c.nom_estado,d.nom_tipsol,
						e.nom_subtip
					FROM ".BASE_DATOS.".tab_solici_solici a 
					inner join ".BASE_DATOS.".tab_solici_datosx b on b.cod_solici=a.cod_solici 
					inner join ".BASE_DATOS.".tab_solici_estado c on c.cod_estado=a.cod_estado 
					inner join ".BASE_DATOS.".tab_solici_tiposx d on d.cod_tipsol=a.cod_tipsol 
					left join ".BASE_DATOS.".tab_solici_subtip e on e.cod_subtip=a.cod_subtip and e.cod_tipsol=a.cod_tipsol 
						WHERE $where
						order by a.num_solici,b.fec_creaci desc,b.cod_transp,a.cod_estado,a.cod_tipsol ";
				break;
				case 7:
					$query = "SELECT 
						f.*,
						g.nom_estado
					FROM ".BASE_DATOS.".tab_solici_solici a 
					inner join ".BASE_DATOS.".tab_solici_datosx b on b.cod_solici=a.cod_solici 
					inner join ".BASE_DATOS.".tab_solici_estado c on c.cod_estado=a.cod_estado 
					inner join ".BASE_DATOS.".tab_solici_tiposx d on d.cod_tipsol=a.cod_tipsol 
					left join ".BASE_DATOS.".tab_solici_subtip e on e.cod_subtip=a.cod_subtip and e.cod_tipsol=a.cod_tipsol 
					inner join ".BASE_DATOS.".tab_solici_seguim f on f.num_solici=a.num_solici 
					inner join ".BASE_DATOS.".tab_solici_estado g on g.cod_estado=f.cod_estado 
						WHERE $where
						order by a.num_solici,f.fec_creaci desc,f.num_seguim desc";
				break;
			}
				
			/* CONSULTA PARA TRAER LAS CONFIGURACION A ANS */
			$query_cumplidos = "SELECT x.*
					FROM ".BASE_DATOS.".tab_solici_solici a 
					inner join ".BASE_DATOS.".tab_solici_datosx b on b.cod_solici=a.cod_solici 
					inner join ".BASE_DATOS.".tab_solici_config x on x.cod_tipsol=a.cod_tipsol 
					and x.cod_subtip=a.cod_subtip 
					WHERE $where
						order by a.num_solici";


			$consulta = new Consulta( $query, $this -> conexion );
		    $result = $consulta -> ret_matrix( 'a' );

		    $r=array();
		    if(!empty($result)  && sizeof($result)>0){

		    	foreach($result as $k => $d){
		    		switch ($tipo) {
						case 0:
							if(!array_key_exists("count", $r)){
			    				$r=array("count"=>0);
			    			}
							$r["count"]+=$d["ces"];
			    			$r["estado".$d["cod_estado"]]=$d["ces"];
						break;
						case 1:
							if(!array_key_exists($d["cod_estado"], $r)){
				    			$r[$d["cod_estado"]]=array(
				    					"count"=>0
				    				);
				    		}
				    		$r[$d["cod_estado"]]["count"]+=$d["ces"];
				    		$r[$d["cod_estado"]]["tipsol".$d["cod_tipsol"]]=$d["cts"];
						break;
						case 2:
							$f=$d["fecha"];
							$fp=$this->getDateFormat("format1","es",$f);
							$fc=str_replace("-", "", $f);
							if(!array_key_exists($fc,$r)){
			    				$r[$fc]=array("count"=>0,"fecha"=>$fp);
			    			}
							$r[$fc]["count"]+=$d["ces"];
			    			$r[$fc]["estado".$d["cod_estado"]]=$d["ces"];
						break;
						case 3:
							$s=crc32($d["cod_transp"]);
				    		if(!array_key_exists($d["cod_estado"], $r)){
				    			$r[$d["cod_estado"]]=array();
				    		}
				    		if(!array_key_exists($s, $r[$d["cod_estado"]])){
				    			$r[$d["cod_estado"]][$s]=array(
			    					"count"=>0,
			    					"cod_transp"=>$d["cod_transp"]
			    				);
				    		}
			    			$r[$d["cod_estado"]][$s]["count"]+=$d["cts"];
			    			$r[$d["cod_estado"]][$s]["tipsol".$d["cod_tipsol"]]=$d["cts"];
						break;


						case 4:
							$f=$d["fecha"];
							$fp=$this->getDateFormat("format1","es",$f);
							$fc=str_replace("-", "", $f);
							if(!array_key_exists($fc,$r)){
			    				$r[$fc]=array("count"=>0,"fecha"=>$fp);
			    			}
							$r[$fc]["count"]+=$d["ces"];
			    			$r[$fc]["estado".$d["cod_estado"]]=$d["ces"];
						break;
						case 5:
							//datos extras
							$d["det_solici"]=$this->setMessageDetail($d,false);
							if(!empty($d["dir_archiv"])){
								$d["dir_archiv"]=URL_SOLICI.$d["dir_archiv"];
							}
							settype($d["cod_estado"], "integer");
							$d["fec_difere"]=$this->setCalcTimeDiff($d["fec_creaci"],$d["fec_modifi"]);
							//iconv('UTF-8', 'ISO-8859-1', $d["nom_tipsol"]);
							$d["nom_tipsol"]=utf8_encode($d["nom_tipsol"]);
							$d["obs_solici"]=htmlentities($d["obs_solici"],ENT_QUOTES);
							$d["nom_viaxxx"]=htmlentities($d["nom_viaxxx"],ENT_QUOTES);
							$d["fec_creaci"]=!empty($d["fec_creaci"]) ? $d["fec_creaci"] : "";
							$d["fec_modifi"]=!empty($d["fec_modifi"]) ? $d["fec_modifi"] : "";

							/* CONSULTA PARA VERIFICAR EL TIPO DE PERFIL SEGUN SU USUARIO */			
							$query_perfil = " SELECT t2.cod_perfil AS cod_perfil FROM tab_genera_usuari t1
								JOIN tab_genera_perfil t2
								ON (t1.cod_perfil = t2.cod_perfil) 
								where t1.cod_usuari = '".$d["usr_modifi"]."'; ";
							$consulta_pefil = new Consulta( $query_perfil, $this -> conexion );
		    			$result_pefil = $consulta_pefil -> ret_matrix('a');
		    			$verificar_perfil=	$result_pefil[0]['cod_perfil'];

		    			if($verificar_perfil == 1 || $verificar_perfil == 7 || $verificar_perfil == 8 || $verificar_perfil ==73)
		    			{
		    				$d["user_modifi"]= 0;
		    			}
		    			else
		    			{
		    				$d["user_modifi"] = 1;
		    			}
		    			
							$consulta_cumplidos = new Consulta( $query_cumplidos, $this -> conexion );
		    			$result_cumplidos = $consulta_cumplidos -> ret_matrix( 'a' );
		    			foreach ($result_cumplidos as $key => $cumplidos) {
								$d["fec_inicia"] = $cumplidos["fec_inicia"];
								$d["fec_finali"] = $cumplidos["fec_finali"];
								$d["dia_calend"] = $cumplidos["dia_calend"];
								$d["tip_tiempo"] = $cumplidos["tip_tiempo"];
								$d["tie_respue"] = $cumplidos["tie_respue"];
							}

							$r[]=$d;
						break;
						case 6:
							//datos extras
							//$d["det_solici"]=htmlspecialchars_decode($this->setMessageDetail($d,true),ENT_QUOTES);
							$d["det_solici"]=$this->setMessageDetail($d,true);
							if(!empty($d["dir_archiv"])){
								$d["dir_archiv"]=URL_SOLICI.$d["dir_archiv"];
							}else{
								$d["dir_archiv"]="";
							}
							settype($d["cod_estado"], "integer");
							$d["fec_difere"]=$this->setCalcTimeDiff($d["fec_creaci"],$d["fec_modifi"]);
							//iconv('UTF-8', 'ISO-8859-1', $d["nom_tipsol"]);
							$d["nom_tipsol"]=utf8_encode($d["nom_tipsol"]);
							$d["obs_solici"]=htmlentities($d["obs_solici"],ENT_QUOTES);
							$d["nom_viaxxx"]=htmlentities($d["nom_viaxxx"],ENT_QUOTES);
							$d["fec_creaci"]=!empty($d["fec_creaci"]) ? $d["fec_creaci"] : "";
							$d["fec_modifi"]=!empty($d["fec_modifi"]) ? $d["fec_modifi"] : "";
							$r=$d;
						break;
						case 7:
							settype($d["cod_estado"], "integer");
							$r[]=$d;
						break;
					}
		    	}
		    }

		    $r2=array();
		    //esto se realiza ya que al enviar el servidor, todo ok, pero el indice del registro variable por el navegador, osea cambia el orden del indice ascendente y este indice especialmente se crear por el dato de la fecha
		    if($tipo==2){
		    	if(is_array($r) && !empty($r)){
		    		foreach($r as $k => $v){
		    			array_push($r2, $v);
		    		}
		    		$r=$r2;
		    	}
		    }
		    return $r;
		}catch(Exception $e){}
	}

	function setCalcTimeDiff($a,$b){
		try{
			if(!empty($a) && !empty($b)){
				$a=strtotime($a);
				$b=strtotime($b);
				$diff=$b-$a;

				$r="";
				if($diff>(60*60*24)){
					$r.="".(($diff-($diff%(60*60*24)))/(60*60*24))." D&iacute;as, ";
					$diff=$diff%(60*60*24);
				}
				if($diff>(60*60)){
					$r.="".(($diff-($diff%(60*60)))/(60*60))." Horas, ";
					$diff=$diff%(60*60);
				}
				if($diff>(60)){
					$r.="".(($diff-($diff%(60)))/(60))." Min";
					$diff=$diff%(60);
				}
				return $r;
			}
			return "";
		}catch(Exception $e){
			return "Error ".$e->getMessage();
		}
	}

	function setMessageDetail($d,$isFull){
		try{
			$msg="";
			settype($d["cod_tipsol"],"integer");
			settype($d["cod_subtip"],"integer");
			if(!empty($d["cod_subtip"]) && !empty($d["nom_subtip"])){
				$msg.="".utf8_encode($d["nom_subtip"]).", ";
			}
			if(!empty($d["cod_ciuori"])){
				$msg.="Origen: ".$this->getNombreCiudad($d["cod_ciuori"])[0]['nom_ciudad']." (".$d["cod_ciuori"]."), ";
			}
			if(!empty($d["cod_ciudes"])){
				$msg.="Destino: ".$this->getNombreCiudad($d["cod_ciudes"])[0]['nom_ciudad']." (".$d["cod_ciudes"]."), ";
			}
			if(!empty($d["nom_viaxxx"]) && !$isFull){
				$msg.="V&iacute;a: ".$d["nom_viaxxx"].", ";
			}elseif(!empty($d["nom_viaxxx"]) && $isFull){
				$msg.="Vía: ".$d["nom_viaxxx"].", ";
			}
			if(!empty($d["lis_placas"])){
				$msg.="Placa(s): ".$d["lis_placas"].", ";
			}
			if(!empty($d["cod_tipsol"])==2 && $d["cod_subtip"]==1){
				$msg.="Placa(s): Todas, ";
			}
			if(!empty($d["obs_solici"]) && !$isFull){
				$msg.="Observaci&oacute;n: ".$d["obs_solici"].", ";
			}elseif(!empty($d["obs_solici"]) && $isFull){
				$msg.="Observación: ".$d["obs_solici"].", ";
			}
			if(!empty($d["dir_archiv"])){
				$msg.="(con archivo adjunto)";
			}
			return $isFull ? $msg : substr($msg, 0,139);
		}catch(Exception $e){}
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
			$dircentral='../'.DIR_APLICA_CENTRAL.'/';
			https://avansatgl.intrared.net:8083/ap/satt_standa/images/excel_logo.png
			$dirsolifa='../'.DIR_APLICA_CENTRAL.'/solifa/';
			$imgexcel='../'.DIR_APLICA_CENTRAL.'/images/excel_logo.png';
			$filejqjs='../'.DIR_APLICA_CENTRAL.'/js/jquery17.js';
			$filejs='../'.DIR_APLICA_CENTRAL.'/js/mod_solici_inform.js';
			$filecss='../'.DIR_APLICA_CENTRAL.'/estilos/mod_solici_inform.css';

			if(file_exists($filejs) && file_exists($filecss) && file_exists($filejqjs)){
				//IncludeJS( "mod_solici_inform.js" );//No se incluye, porque se requiere enviar un dato
				//IncludeCSS no aplica, la crearon seguramente para modificar estilos a traves de javascript
				$jq='<script type = "text/javascript" src="'.$filejqjs.'"></script>';
				$formulario = new Formulario ("index.php","post","<div>SOLICITUD A FARO</div>","form_list");
				$js='<script id="_45462213DEf">'.
			        'var ds="'.$dirsolifa.'",'.
			        'dc="'.$dircentral.'",'.
			        'xls="'.$imgexcel.'",'.
			        'cs=parseInt("'.$cod_servic.'"),'.
			        'wd="'.$window.'",'.
			        'ot=parseInt("'.$option.'"),'.
			        'script = document.createElement("script");'.
			        'script.type = "text/javascript";'.
			        'script.src = "'.$filejs.'?t='.rand(10,99).'";'.
			        'document.getElementsByTagName("head")[0].appendChild(script);'.
				'</script>';
	      		echo '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/estilos/mod_solici_inform.css">';
				echo '<div class="inf_solici_solici">Cargando...'.$jq.$js.'</div>';
				$formulario->cerrar();

			}else{
				die("Not found required files to Solici_solici::onCreateForm");
			}
		}else{
			$formulario = new Formulario ("","get","<div>SOLICITUD A FARO</div>","void");
			echo '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/estilos/mod_solici_inform.css">';
			echo '<div class="inf_solici_solici alert alert-warning"><strong>Requiere activar la interfaz con Faro, consulte con su proveedor.</strong></div>';
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
					foreach ($this->raw->rutaxx as $id => $obj) {
						$this->InterfSolicitud->setSoliciRuta(
							$this->arr_datsol,
							$obj
						);
					}
					echo json_encode($this->InterfSolicitud->getResult());
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
				settype($this->raw->ind_segesp,"integer");
				$this->InterfSolicitud->setSoliciSegimientoEspecial(
					$this->arr_datsol,
					$this->raw->ind_segesp,	
					$this->raw->fec_iniseg,	
					$this->raw->fec_finseg,	
					$this->raw->lis_placax,	
					$this->raw->obs_solici
				);
				echo json_encode($this->InterfSolicitud->getResult());						
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
				settype($this->raw->ind_pqrsxx,"integer");
				$this->InterfSolicitud->setSoliciPQR(
					$this->arr_datsol,
					$this->raw->ind_pqrsxx,	
					$this->raw->nom_pqrsxx,	
					$this->raw->obs_pqrsxx,	
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
				echo $r;
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
				echo $r;
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

	function encode2($msg){
	    $msg = str_replace("á","&aacute;", $msg);
	    $msg = str_replace("é","&eacute;", $msg);
	    $msg = str_replace("í","&iacute;", $msg);
	    $msg = str_replace("ó","&oacute;", $msg);
	    $msg = str_replace("ú","&uacute;", $msg);
	    $msg = str_replace("Á","&Aacute;", $msg);
	    $msg = str_replace("É","&Eacute;", $msg);
	    $msg = str_replace("Í","&Iacute;", $msg);
	    $msg = str_replace("Ó","&Oacute;", $msg);
	    $msg = str_replace("Ú","&Uacute;", $msg);
	    $msg = str_replace("ñ","&ntilde;", $msg);
	    $msg = str_replace("Ñ","&Ntilde;", $msg);
	    $msg = str_replace("à","&agrave;", $msg);
	    $msg = str_replace("À","&Agrave;", $msg);
	    $msg = str_replace("Ç","&Ccedil;", $msg);
	    $msg = str_replace("ç","&ccedil;", $msg);
	    $msg = str_replace("ï","&iuml;", $msg);
	    $msg = str_replace("Ï","&Iuml;", $msg);
	    $msg = str_replace("ò","&ograve;", $msg);
	    $msg = str_replace("Ò","&Ograve;",  $msg);
	    $msg = str_replace("ü","&uuml;",  $msg);
	    $msg = str_replace("Ü","&Uuml;",  $msg);
	    return $msg;
	}

	function getNombreCiudad($cod_ciudad){
		$sql='SELECT a.nom_ciudad FROM tab_genera_ciudad a WHERE cod_ciudad="'.$cod_ciudad.'"';
		$consulta = new Consulta( $sql, $this->conexion );
		return $datos = $consulta->ret_matrix( 'a' );
	}

	function sendMailSolifa($data = NULL)
	{
	    try
	    {
		    $mCabece = 'MIME-Version: 1.0' . "\r\n";
		    $mCabece .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		    $mCabece .= 'From: ASISTENCIA LOGISTICA <no-replay@grupooet.com>' . "\r\n";
		    $tmpl_file = '/var/www/html/ap/satt_standa/planti/pla_solifa_solifa.html';
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
		    mail( $mailToS, "sol. ".$data->asunto , '<div name="_faro_07">' . $mHtmlxx . '</div>', $mCabece );
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
		    $sql =  "SELECT c.abr_tercer, b.dir_usrmai, a.obs_solici, a.nom_asunto $campos FROM ".BASE_DATOS.".tab_solici_solici a ".
		              "INNER JOIN ".BASE_DATOS.".tab_solici_datosx b ON a.cod_solici = b.cod_solici ".
		              "INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON b.cod_transp = c.cod_tercer ".
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

  	function getTransLogin()
	{
	    try{
		    $mSql = "SELECT clv_filtro AS cod_transp
					   				FROM ".BASE_DATOS.".tab_aplica_filtro_perfil 
					   			WHERE cod_perfil= ".$this->cSession["cod_perfil"];
			$mConsult = new Consulta( $mSql, $this->conexion );
			$mTransp = $mConsult -> ret_matrix('a');
			return $mTransp = $mTransp[0]['cod_transp'];
			
	    }
	    catch(Exception $e)
	    {
	    	return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
	    }
  	}
}

$proceso = new Solici_solici();