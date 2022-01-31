<?php
ini_set('error_reporting', E_ALL);
ini_set("display_errors", 1);
@session_start();
class Solici_solici
{
	var $conexion, $cod_aplica,	$usuario, $usuario2, $cod_tercer, $arr_datsol, $interf,
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
			"cod_usuari"=>$this->getInterf("nom_usuari"),//USR_INTERF_FAROXX,
			"pwd_clavex"=>$this->getInterf("clv_usuari"),//PWD_INTERF_FAROXX,
			"cod_transp"=>$this->getInterf("cod_transp"),//NIT_TRANSPOR,
			"nom_aplica"=>BASE_DATOS,
			"url_aplica"=>null,//$_SERVER["PHP_SELF"],//url de retorno para respuesta
			"cod_solici"=>@$_SESSION["datos_usuario"]["cod_usuari"],
			"nom_solici"=>isset($_POST["nom_solici"]) ? $_POST["nom_solici"] : @$_GET["nom_solici"],
			"mai_solici"=>isset($_POST["mai_solici"]) ? $_POST["mai_solici"] : @$_GET["mai_solici"],
			"fij_solici"=>isset($_POST["fij_solici"]) ? $_POST["fij_solici"] : @$_GET["fij_solici"],
			"cel_solici"=>isset($_POST["cel_solici"]) ? $_POST["cel_solici"] : @$_GET["cel_solici"]
			//"cod_tipsol"=>null,//Tipos de solicitud(Pestañas = 4) 1: Creación de rutas, 2:Seguimiento especial, 3: PQR, 4: Otras solicitudes 
			//"nom_tipsol"=>null//Tipos de solicitud(Pestañas = 4) 1: Creación de rutas, 2:Seguimiento especial, 3: PQR, 4: Otras solicitudes
			//cod_usrsol = equivalente en este caso a cod_solici


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
		$this->setInterf();
		$this->setArrDatSol();

		$file="../".DIR_APLICA_CENTRAL."/lib/InterfSolicitud.inc";
		if(!file_exists($file)){
			$file="../lib/InterfSolicitud.inc";
		}
		if(file_exists($file)){
			include($file);
			$_req=isset($_REQUEST["cod_servic"]) ? $_REQUEST : ( isset($GLOBALS["cod_servic"]) ? $GLOBALS : null );
			$option=array_key_exists("option", $_req) ? $_req["option"] : 0;
			settype($option,"integer");
			if($option>=1 and $option<=4)
				$InterfSolicitud = new InterfSolicitud();

			switch($option)
			{
				//form para enviar solicitudes
				case 0:
					$this->Form($_req);
				break;
				//Creacion de rutas
				case 1:
					$this->arr_datsol["cod_tipsol"]=$option;
					$this->arr_datsol["nom_tipsol"]="Creacion de rutas";
					if(gettype($this->raw)=="object"){
						if(gettype($this->raw->solici)=="object" && gettype($this->raw->rutaxx)=="array"){
							if(sizeof($this->raw->rutaxx)>0){
								$this->arr_datsol["nom_solici"]=$this->raw->solici->nom_solici;
								$this->arr_datsol["mai_solici"]=$this->raw->solici->mai_solici;
								$this->arr_datsol["fij_solici"]=$this->raw->solici->fij_solici;
								$this->arr_datsol["cel_solici"]=$this->raw->solici->cel_solici;
								foreach ($this->raw->rutaxx as $id => $obj) {
									$InterfSolicitud->setSoliciRuta(
										$this->arr_datsol,
										$obj
									);
								}
								echo json_encode($InterfSolicitud->getResult());
							}
						}
					}
				break;
				//Seguimiento especial
				case 2:
					$this->arr_datsol["cod_tipsol"]=$option;
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
							$InterfSolicitud->setSoliciSegimientoEspecial(
								$this->arr_datsol,
								$this->raw->ind_segesp,	
								$this->raw->fec_iniseg,	
								$this->raw->fec_finseg,	
								$this->raw->lis_placax,	
								$this->raw->obs_solici
							);
							echo json_encode($InterfSolicitud->getResult());						
						}
					}
				break;
				//PQR
				case 3:
					$this->arr_datsol["cod_tipsol"]=$option;
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
							$InterfSolicitud->setSoliciPQR(
								$this->arr_datsol,
								$this->raw->ind_pqrsxx,	
								$this->raw->nom_pqrsxx,	
								$this->raw->obs_pqrsxx,	
								$this->getFileComplex($this->decrypt($this->raw->fil_archiv))
							);
							echo json_encode($InterfSolicitud->getResult());
						}
					}
				break;
				//Otras
				case 4:
					$this->arr_datsol["cod_tipsol"]=$option;
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
							$InterfSolicitud->setSoliciOtros(
								$this->arr_datsol,
								$this->raw->nom_otroxx,	
								$this->raw->obs_otroxx,	
								$this->getFileComplex($this->decrypt($this->raw->fil_archiv))
							);
							echo json_encode($InterfSolicitud->getResult());
						}
					}
				break;
				case 94:
					$this->getInterfDump();
					//$this->getInterf();
				break;
				case 95:
					$this->removeFile();
				break;
				case 96:
					$this->uploadFile();
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
			unlink($file);
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
	function removeFile(){
		if(gettype($this->raw)=="object"){
			if(gettype($this->raw->hash)=="string"){
				$filename=$this->decrypt($this->raw->hash);
				if(unlink($filename)){
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
	function uploadFile(){
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

	function getInterfDump(){
		print_r($this->getInterf(null));
	}
	
	function getInterf($k){
		if(isset($k) && gettype($k)=="string"){
			if(is_array($this->interf) && array_key_exists($k, $this->interf)){
				if($k=="clv_usuari"){$this->interf[$k]=base64_decode($this->interf[$k]);}
				return $this->interf[$k];
			}
		}else{
			return $this->interf;
		}
	}
	function setInterf(){
		$sql='SELECT cod_operad,cod_transp,nom_operad,nom_usuari,clv_usuari,val_timtra,ind_intind,ind_operad,ind_estado,url_webser '.
					'FROM tab_interf_parame '.
					'WHERE '.
					'cod_operad=50 and '.
					'ind_estado=1 '.
					'limit 1';
		$consulta = new Consulta( $sql, $this->conexion );
		$datos = $consulta->ret_matriz( 'a' );
		$this->interf = sizeof($datos)>0 ? $datos[0] : $datos;
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
	* Pintar el Form para realizar solicitudes
	*/
	function Form($_req){
		$window=isset($_req["window"]) ? $_req["window"] : "";
		$cod_servic=isset($_req["cod_servic"]) ? $_req["cod_servic"] : "";
		$option=isset($_req["option"]) ? $_req["option"] : "";

		$error=false;
		$dirsolifa='../'.DIR_APLICA_CENTRAL.'/solifa/';
		$filejs='../'.DIR_APLICA_CENTRAL.'/js/mod_solici_solici.js';
		$filecss='../'.DIR_APLICA_CENTRAL.'/estilos/mod_solici_solici.css';
		if(file_exists($filejs) && file_exists($filecss)){
			//IncludeJS( "mod_solici_solici.js" );//No se incluye, porque se requiere enviar un dato
			//IncludeCSS no aplica, la crearon seguramente para modificar estilos a traves de javascript

			$formulario = new Formulario ("index.php","post","<div>Solicutd a faro</div>","form_list");
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
			$formulario->cerrar();

		}else{
			die("Not found required files to Solici_solici::Form");
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
}

$proceso = new Solici_solici();














/*


//archivo de configuracion
include("setting.php");

$dir_path=DIR_PATH;
$directory_path="";
$mimetype_avaible=array("application/pdf");
$msg=json_decode(json_encode(array("message"=>"","slug"=>"","id"=>"")));
if(isset($_FILES) && sizeof($_FILES)>0){
  foreach($_FILES as $file){
    if(isset($_REQUEST["filename"]) && !empty($_REQUEST["filename"])){
      $file['name']=$_REQUEST["filename"];
    }
    if(isset($_REQUEST["type"]) && !empty($_REQUEST["type"])){
      $directory_path=$_REQUEST["type"].DF;
    }

    //crear directorio en caso de no existir
    if (!file_exists($dir_path.$directory_path.basename($file['name'])) && !is_dir($dir_path.$directory_path)) {
        mkdir($dir_path.$directory_path,0775);
    }

    $target = $dir_path.$directory_path.basename($file['name']) ;
    if(in_array($file['type'],$mimetype_avaible)){
      if(!file_exists($target)){
        if(move_uploaded_file($file['tmp_name'],$target)){
          $msg->id=5;
          $msg->slug="success";
          $msg->message=sprintf(FILELOG01,$file['name']);
        }else{
          $msg->id=4;
          $msg->slug="error";
          $msg->message=FILELOG02;
        }
      }else {
        $msg->id=3;
        $msg->slug="error";
        $msg->message=sprintf(FILELOG03,$file['name']);
      }
    }else{
      $msg->id=2;
      $msg->slug="error";
      $msg->message=sprintf(FILELOG04,$file['type']);
    }
  }
}else{
  $msg->id=1;
  $msg->slug="error";
  $msg->message=FILELOG05;
}
echo json_encode($msg);


*/




/*

$mime_types_map = array(
    '3dm' => 'x-world/x-3dmf', '3dmf' => 'x-world/x-3dmf', 'a' => 'application/octet-stream', 'aab' => 'application/x-authorware-bin',
    'aam' => 'application/x-authorware-map', 'aas' => 'application/x-authorware-seg', 'abc' => 'text/vnd.abc', 'acgi' => 'text/html',
    'afl' => 'video/animaflex', 'ai' => 'application/postscript', 'aif' => 'audio/aiff', 'aif' => 'audio/x-aiff',
    'aifc' => 'audio/aiff', 'aifc' => 'audio/x-aiff', 'aiff' => 'audio/aiff', 'aiff' => 'audio/x-aiff',
    'aim' => 'application/x-aim', 'aip' => 'text/x-audiosoft-intra', 'ani' => 'application/x-navi-animation', 'aos' => 'application/x-nokia-9000-communicator-add-on-software',
    'aps' => 'application/mime', 'arc' => 'application/octet-stream', 'arj' => 'application/arj', 'arj' => 'application/octet-stream',
    'art' => 'image/x-jg', 'asf' => 'video/x-ms-asf', 'asm' => 'text/x-asm', 'asp' => 'text/asp',
    'asx' => 'application/x-mplayer2', 'asx' => 'video/x-ms-asf', 'asx' => 'video/x-ms-asf-plugin', 'au' => 'audio/basic',
    'au' => 'audio/x-au', 'avi' => 'application/x-troff-msvideo', 'avi' => 'video/avi', 'avi' => 'video/msvideo',
    'avi' => 'video/x-msvideo', 'avs' => 'video/avs-video', 'bcpio' => 'application/x-bcpio', 'bin' => 'application/mac-binary',
    'bin' => 'application/macbinary', 'bin' => 'application/octet-stream', 'bin' => 'application/x-binary', 'bin' => 'application/x-macbinary',
    'bm' => 'image/bmp', 'bmp' => 'image/bmp', 'bmp' => 'image/x-windows-bmp', 'boo' => 'application/book',
    'book' => 'application/book', 'boz' => 'application/x-bzip2', 'bsh' => 'application/x-bsh', 'bz' => 'application/x-bzip',
    'bz2' => 'application/x-bzip2', 'c' => 'text/plain', 'c' => 'text/x-c', 'c++' => 'text/plain',
    'cat' => 'application/vnd.ms-pki.seccat', 'cc' => 'text/plain', 'cc' => 'text/x-c', 'ccad' => 'application/clariscad',
    'cco' => 'application/x-cocoa', 'cdf' => 'application/cdf', 'cdf' => 'application/x-cdf', 'cdf' => 'application/x-netcdf',
    'cer' => 'application/pkix-cert', 'cer' => 'application/x-x509-ca-cert', 'cha' => 'application/x-chat', 'chat' => 'application/x-chat',
    'class' => 'application/java', 'class' => 'application/java-byte-code', 'class' => 'application/x-java-class', 'com' => 'application/octet-stream',
    'com' => 'text/plain', 'conf' => 'text/plain', 'cpio' => 'application/x-cpio', 'cpp' => 'text/x-c',
    'cpt' => 'application/mac-compactpro', 'cpt' => 'application/x-compactpro', 'cpt' => 'application/x-cpt', 'crl' => 'application/pkcs-crl',
    'crl' => 'application/pkix-crl', 'crt' => 'application/pkix-cert', 'crt' => 'application/x-x509-ca-cert', 'crt' => 'application/x-x509-user-cert',
    'csh' => 'application/x-csh', 'csh' => 'text/x-script.csh', 'css' => 'application/x-pointplus', 'css' => 'text/css',
    'cxx' => 'text/plain', 'dcr' => 'application/x-director', 'deepv' => 'application/x-deepv', 'def' => 'text/plain',
    'der' => 'application/x-x509-ca-cert', 'dif' => 'video/x-dv', 'dir' => 'application/x-director', 'dl' => 'video/dl',
    'dl' => 'video/x-dl', 'doc' => 'application/msword', 'dot' => 'application/msword', 'dp' => 'application/commonground',
    'drw' => 'application/drafting', 'dump' => 'application/octet-stream', 'dv' => 'video/x-dv', 'dvi' => 'application/x-dvi',
    'dwf' => 'drawing/x-dwf', 'dwf' => 'model/vnd.dwf', 'dwg' => 'application/acad', 'dwg' => 'image/vnd.dwg',
    'dwg' => 'image/x-dwg', 'dxf' => 'application/dxf', 'dxf' => 'image/vnd.dwg', 'dxf' => 'image/x-dwg',
    'dxr' => 'application/x-director', 'el' => 'text/x-script.elisp', 'elc' => 'application/x-bytecode.elisp', 'elc' => 'application/x-elc',
    'env' => 'application/x-envoy', 'eps' => 'application/postscript', 'es' => 'application/x-esrehber', 'etx' => 'text/x-setext',
    'evy' => 'application/envoy', 'evy' => 'application/x-envoy', 'exe' => 'application/octet-stream', 'f' => 'text/plain',
    'f' => 'text/x-fortran', 'f77' => 'text/x-fortran', 'f90' => 'text/plain', 'f90' => 'text/x-fortran',
    'fdf' => 'application/vnd.fdf', 'fif' => 'application/fractals', 'fif' => 'image/fif', 'fli' => 'video/fli',
    'fli' => 'video/x-fli', 'flo' => 'image/florian', 'flx' => 'text/vnd.fmi.flexstor', 'fmf' => 'video/x-atomic3d-feature',
    'for' => 'text/plain', 'for' => 'text/x-fortran', 'fpx' => 'image/vnd.fpx', 'fpx' => 'image/vnd.net-fpx',
    'frl' => 'application/freeloader', 'funk' => 'audio/make', 'g' => 'text/plain', 'g3' => 'image/g3fax',
    'gif' => 'image/gif', 'gl' => 'video/gl', 'gl' => 'video/x-gl', 'gsd' => 'audio/x-gsm',
    'gsm' => 'audio/x-gsm', 'gsp' => 'application/x-gsp', 'gss' => 'application/x-gss', 'gtar' => 'application/x-gtar',
    'gz' => 'application/x-compressed', 'gz' => 'application/x-gzip', 'gzip' => 'application/x-gzip', 'gzip' => 'multipart/x-gzip',
    'h' => 'text/plain', 'h' => 'text/x-h', 'hdf' => 'application/x-hdf', 'help' => 'application/x-helpfile',
    'hgl' => 'application/vnd.hp-hpgl', 'hh' => 'text/plain', 'hh' => 'text/x-h', 'hlb' => 'text/x-script',
    'hlp' => 'application/hlp', 'hlp' => 'application/x-helpfile', 'hlp' => 'application/x-winhelp', 'hpg' => 'application/vnd.hp-hpgl',
    'hpgl' => 'application/vnd.hp-hpgl', 'hqx' => 'application/binhex', 'hqx' => 'application/binhex4', 'hqx' => 'application/mac-binhex',
    'hqx' => 'application/mac-binhex40', 'hqx' => 'application/x-binhex40', 'hqx' => 'application/x-mac-binhex40', 'hta' => 'application/hta',
    'htc' => 'text/x-component', 'htm' => 'text/html', 'html' => 'text/html', 'htmls' => 'text/html',
    'htt' => 'text/webviewhtml', 'htx' => 'text/html', 'ice' => 'x-conference/x-cooltalk', 'ico' => 'image/x-icon',
    'idc' => 'text/plain', 'ief' => 'image/ief', 'iefs' => 'image/ief', 'iges' => 'application/iges',
    'iges' => 'model/iges', 'igs' => 'application/iges', 'igs' => 'model/iges', 'ima' => 'application/x-ima',
    'imap' => 'application/x-httpd-imap', 'inf' => 'application/inf', 'ins' => 'application/x-internett-signup', 'ip' => 'application/x-ip2',
    'isu' => 'video/x-isvideo', 'it' => 'audio/it', 'iv' => 'application/x-inventor', 'ivr' => 'i-world/i-vrml',
    'ivy' => 'application/x-livescreen', 'jam' => 'audio/x-jam', 'jav' => 'text/plain', 'jav' => 'text/x-java-source',
    'java' => 'text/plain', 'java' => 'text/x-java-source', 'jcm' => 'application/x-java-commerce', 'jfif' => 'image/jpeg',
    'jfif' => 'image/pjpeg', 'jfif-tbnl' => 'image/jpeg', 'jpe' => 'image/jpeg', 'jpe' => 'image/pjpeg',
    'jpeg' => 'image/jpeg', 'jpeg' => 'image/pjpeg', 'jpg' => 'image/jpeg', 'jpg' => 'image/pjpeg',
    'jps' => 'image/x-jps', 'js' => 'application/x-javascript', 'jut' => 'image/jutvision', 'kar' => 'audio/midi',
    'kar' => 'music/x-karaoke', 'ksh' => 'application/x-ksh', 'ksh' => 'text/x-script.ksh', 'la' => 'audio/nspaudio',
    'la' => 'audio/x-nspaudio', 'lam' => 'audio/x-liveaudio', 'latex' => 'application/x-latex', 'lha' => 'application/lha',
    'lha' => 'application/octet-stream', 'lha' => 'application/x-lha', 'lhx' => 'application/octet-stream', 'list' => 'text/plain',
    'lma' => 'audio/nspaudio', 'lma' => 'audio/x-nspaudio', 'log' => 'text/plain', 'lsp' => 'application/x-lisp',
    'lsp' => 'text/x-script.lisp', 'lst' => 'text/plain', 'lsx' => 'text/x-la-asf', 'ltx' => 'application/x-latex',
    'lzh' => 'application/octet-stream', 'lzh' => 'application/x-lzh', 'lzx' => 'application/lzx', 'lzx' => 'application/octet-stream',
    'lzx' => 'application/x-lzx', 'm' => 'text/plain', 'm' => 'text/x-m', 'm1v' => 'video/mpeg',
    'm2a' => 'audio/mpeg', 'm2v' => 'video/mpeg', 'm3u' => 'audio/x-mpequrl', 'man' => 'application/x-troff-man',
    'map' => 'application/x-navimap', 'mar' => 'text/plain', 'mbd' => 'application/mbedlet', 'mc$' => 'application/x-magic-cap-package-1.0',
    'mcd' => 'application/mcad', 'mcd' => 'application/x-mathcad', 'mcf' => 'image/vasa', 'mcf' => 'text/mcf',
    'mcp' => 'application/netmc', 'me' => 'application/x-troff-me', 'mht' => 'message/rfc822', 'mhtml' => 'message/rfc822',
    'mid' => 'application/x-midi', 'mid' => 'audio/midi', 'mid' => 'audio/x-mid', 'mid' => 'audio/x-midi',
    'mid' => 'music/crescendo', 'mid' => 'x-music/x-midi', 'midi' => 'application/x-midi', 'midi' => 'audio/midi',
    'midi' => 'audio/x-mid', 'midi' => 'audio/x-midi', 'midi' => 'music/crescendo', 'midi' => 'x-music/x-midi',
    'mif' => 'application/x-frame', 'mif' => 'application/x-mif', 'mime' => 'message/rfc822', 'mime' => 'www/mime',
    'mjf' => 'audio/x-vnd.audioexplosion.mjuicemediafile', 'mjpg' => 'video/x-motion-jpeg', 'mm' => 'application/base64', 'mm' => 'application/x-meme',
    'mme' => 'application/base64', 'mod' => 'audio/mod', 'mod' => 'audio/x-mod', 'moov' => 'video/quicktime',
    'mov' => 'video/quicktime', 'movie' => 'video/x-sgi-movie', 'mp2' => 'audio/mpeg', 'mp2' => 'audio/x-mpeg',
    'mp2' => 'video/mpeg', 'mp2' => 'video/x-mpeg', 'mp2' => 'video/x-mpeq2a', 'mp3' => 'audio/mpeg3',
    'mp3' => 'audio/x-mpeg-3', 'mp3' => 'video/mpeg', 'mp3' => 'video/x-mpeg', 'mpa' => 'audio/mpeg',
    'mpa' => 'video/mpeg', 'mpc' => 'application/x-project', 'mpe' => 'video/mpeg', 'mpeg' => 'video/mpeg',
    'mpg' => 'audio/mpeg', 'mpg' => 'video/mpeg', 'mpga' => 'audio/mpeg', 'mpp' => 'application/vnd.ms-project',
    'mpt' => 'application/x-project', 'mpv' => 'application/x-project', 'mpx' => 'application/x-project', 'mrc' => 'application/marc',
    'ms' => 'application/x-troff-ms', 'mv' => 'video/x-sgi-movie', 'my' => 'audio/make', 'mzz' => 'application/x-vnd.audioexplosion.mzz',
    'nap' => 'image/naplps', 'naplps' => 'image/naplps', 'nc' => 'application/x-netcdf', 'ncm' => 'application/vnd.nokia.configuration-message',
    'nif' => 'image/x-niff', 'niff' => 'image/x-niff', 'nix' => 'application/x-mix-transfer', 'nsc' => 'application/x-conference',
    'nvd' => 'application/x-navidoc', 'o' => 'application/octet-stream', 'oda' => 'application/oda', 'omc' => 'application/x-omc',
    'omcd' => 'application/x-omcdatamaker', 'omcr' => 'application/x-omcregerator', 'p' => 'text/x-pascal', 'p10' => 'application/pkcs10',
    'p10' => 'application/x-pkcs10', 'p12' => 'application/pkcs-12', 'p12' => 'application/x-pkcs12', 'p7a' => 'application/x-pkcs7-signature',
    'p7c' => 'application/pkcs7-mime', 'p7c' => 'application/x-pkcs7-mime', 'p7m' => 'application/pkcs7-mime', 'p7m' => 'application/x-pkcs7-mime',
    'p7r' => 'application/x-pkcs7-certreqresp', 'p7s' => 'application/pkcs7-signature', 'part' => 'application/pro_eng', 'pas' => 'text/pascal',
    'pbm' => 'image/x-portable-bitmap', 'pcl' => 'application/vnd.hp-pcl', 'pcl' => 'application/x-pcl', 'pct' => 'image/x-pict',
    'pcx' => 'image/x-pcx', 'pdb' => 'chemical/x-pdb', 'pdf' => 'application/pdf', 'pfunk' => 'audio/make',
    'pfunk' => 'audio/make.my.funk', 'pgm' => 'image/x-portable-graymap', 'pgm' => 'image/x-portable-greymap', 'pic' => 'image/pict',
    'pict' => 'image/pict', 'pkg' => 'application/x-newton-compatible-pkg', 'pko' => 'application/vnd.ms-pki.pko', 'pl' => 'text/plain',
    'pl' => 'text/x-script.perl', 'plx' => 'application/x-pixclscript', 'pm' => 'image/x-xpixmap', 'pm' => 'text/x-script.perl-module',
    'pm4' => 'application/x-pagemaker', 'pm5' => 'application/x-pagemaker', 'png' => 'image/png', 'pnm' => 'application/x-portable-anymap',
    'pnm' => 'image/x-portable-anymap', 'pot' => 'application/mspowerpoint', 'pot' => 'application/vnd.ms-powerpoint', 'pov' => 'model/x-pov',
    'ppa' => 'application/vnd.ms-powerpoint', 'ppm' => 'image/x-portable-pixmap', 'pps' => 'application/mspowerpoint', 'pps' => 'application/vnd.ms-powerpoint',
    'ppt' => 'application/mspowerpoint', 'ppt' => 'application/powerpoint', 'ppt' => 'application/vnd.ms-powerpoint', 'ppt' => 'application/x-mspowerpoint',
    'ppz' => 'application/mspowerpoint', 'pre' => 'application/x-freelance', 'prt' => 'application/pro_eng', 'ps' => 'application/postscript',
    'psd' => 'application/octet-stream', 'pvu' => 'paleovu/x-pv', 'pwz' => 'application/vnd.ms-powerpoint', 'py' => 'text/x-script.phyton',
    'pyc' => 'applicaiton/x-bytecode.python', 'qcp' => 'audio/vnd.qcelp', 'qd3' => 'x-world/x-3dmf', 'qd3d' => 'x-world/x-3dmf',
    'qif' => 'image/x-quicktime', 'qt' => 'video/quicktime', 'qtc' => 'video/x-qtc', 'qti' => 'image/x-quicktime',
    'qtif' => 'image/x-quicktime', 'ra' => 'audio/x-pn-realaudio', 'ra' => 'audio/x-pn-realaudio-plugin', 'ra' => 'audio/x-realaudio',
    'ram' => 'audio/x-pn-realaudio', 'ras' => 'application/x-cmu-raster', 'ras' => 'image/cmu-raster', 'ras' => 'image/x-cmu-raster',
    'rast' => 'image/cmu-raster', 'rexx' => 'text/x-script.rexx', 'rf' => 'image/vnd.rn-realflash', 'rgb' => 'image/x-rgb',
    'rm' => 'application/vnd.rn-realmedia', 'rm' => 'audio/x-pn-realaudio', 'rmi' => 'audio/mid', 'rmm' => 'audio/x-pn-realaudio',
    'rmp' => 'audio/x-pn-realaudio', 'rmp' => 'audio/x-pn-realaudio-plugin', 'rng' => 'application/ringing-tones', 'rng' => 'application/vnd.nokia.ringing-tone',
    'rnx' => 'application/vnd.rn-realplayer', 'roff' => 'application/x-troff', 'rp' => 'image/vnd.rn-realpix', 'rpm' => 'audio/x-pn-realaudio-plugin',
    'rt' => 'text/richtext', 'rt' => 'text/vnd.rn-realtext', 'rtf' => 'application/rtf', 'rtf' => 'application/x-rtf',
    'rtf' => 'text/richtext', 'rtx' => 'application/rtf', 'rtx' => 'text/richtext', 'rv' => 'video/vnd.rn-realvideo',
    's' => 'text/x-asm', 's3m' => 'audio/s3m', 'saveme' => 'application/octet-stream', 'sbk' => 'application/x-tbook',
    'scm' => 'application/x-lotusscreencam', 'scm' => 'text/x-script.guile', 'scm' => 'text/x-script.scheme', 'scm' => 'video/x-scm',
    'sdml' => 'text/plain', 'sdp' => 'application/sdp', 'sdp' => 'application/x-sdp', 'sdr' => 'application/sounder',
    'sea' => 'application/sea', 'sea' => 'application/x-sea', 'set' => 'application/set', 'sgm' => 'text/sgml',
    'sgm' => 'text/x-sgml', 'sgml' => 'text/sgml', 'sgml' => 'text/x-sgml', 'sh' => 'application/x-bsh',
    'sh' => 'application/x-sh', 'sh' => 'application/x-shar', 'sh' => 'text/x-script.sh', 'shar' => 'application/x-bsh',
    'shar' => 'application/x-shar', 'shtml' => 'text/html', 'shtml' => 'text/x-server-parsed-html', 'sid' => 'audio/x-psid',
    'sit' => 'application/x-sit', 'sit' => 'application/x-stuffit', 'skd' => 'application/x-koan', 'skm' => 'application/x-koan',
    'skp' => 'application/x-koan', 'skt' => 'application/x-koan', 'sl' => 'application/x-seelogo', 'smi' => 'application/smil',
    'smil' => 'application/smil', 'snd' => 'audio/basic', 'snd' => 'audio/x-adpcm', 'sol' => 'application/solids',
    'spc' => 'application/x-pkcs7-certificates', 'spc' => 'text/x-speech', 'spl' => 'application/futuresplash', 'spr' => 'application/x-sprite',
    'sprite' => 'application/x-sprite', 'src' => 'application/x-wais-source', 'ssi' => 'text/x-server-parsed-html', 'ssm' => 'application/streamingmedia',
    'sst' => 'application/vnd.ms-pki.certstore', 'step' => 'application/step', 'stl' => 'application/sla', 'stl' => 'application/vnd.ms-pki.stl',
    'stl' => 'application/x-navistyle', 'stp' => 'application/step', 'sv4cpio' => 'application/x-sv4cpio', 'sv4crc' => 'application/x-sv4crc',
    'svf' => 'image/vnd.dwg', 'svf' => 'image/x-dwg', 'svr' => 'application/x-world', 'svr' => 'x-world/x-svr',
    'swf' => 'application/x-shockwave-flash', 't' => 'application/x-troff', 'talk' => 'text/x-speech', 'tar' => 'application/x-tar',
    'tbk' => 'application/toolbook', 'tbk' => 'application/x-tbook', 'tcl' => 'application/x-tcl', 'tcl' => 'text/x-script.tcl',
    'tcsh' => 'text/x-script.tcsh', 'tex' => 'application/x-tex', 'texi' => 'application/x-texinfo', 'texinfo' => 'application/x-texinfo',
    'text' => 'application/plain', 'text' => 'text/plain', 'tgz' => 'application/gnutar', 'tgz' => 'application/x-compressed',
    'tif' => 'image/tiff', 'tif' => 'image/x-tiff', 'tiff' => 'image/tiff', 'tiff' => 'image/x-tiff',
    'tr' => 'application/x-troff', 'tsi' => 'audio/tsp-audio', 'tsp' => 'application/dsptype', 'tsp' => 'audio/tsplayer',
    'tsv' => 'text/tab-separated-values', 'turbot' => 'image/florian', 'txt' => 'text/plain', 'uil' => 'text/x-uil',
    'uni' => 'text/uri-list', 'unis' => 'text/uri-list', 'unv' => 'application/i-deas', 'uri' => 'text/uri-list',
    'uris' => 'text/uri-list', 'ustar' => 'application/x-ustar', 'ustar' => 'multipart/x-ustar', 'uu' => 'application/octet-stream',
    'uu' => 'text/x-uuencode', 'uue' => 'text/x-uuencode', 'vcd' => 'application/x-cdlink', 'vcs' => 'text/x-vcalendar',
    'vda' => 'application/vda', 'vdo' => 'video/vdo', 'vew' => 'application/groupwise', 'viv' => 'video/vivo',
    'viv' => 'video/vnd.vivo', 'vivo' => 'video/vivo', 'vivo' => 'video/vnd.vivo', 'vmd' => 'application/vocaltec-media-desc',
    'vmf' => 'application/vocaltec-media-file', 'voc' => 'audio/voc', 'voc' => 'audio/x-voc', 'vos' => 'video/vosaic',
    'vox' => 'audio/voxware', 'vqe' => 'audio/x-twinvq-plugin', 'vqf' => 'audio/x-twinvq', 'vql' => 'audio/x-twinvq-plugin',
    'vrml' => 'application/x-vrml', 'vrml' => 'model/vrml', 'vrml' => 'x-world/x-vrml', 'vrt' => 'x-world/x-vrt',
    'vsd' => 'application/x-visio', 'vst' => 'application/x-visio', 'vsw' => 'application/x-visio', 'w60' => 'application/wordperfect6.0',
    'w61' => 'application/wordperfect6.1', 'w6w' => 'application/msword', 'wav' => 'audio/wav', 'wav' => 'audio/x-wav',
    'wb1' => 'application/x-qpro', 'wbmp' => 'image/vnd.wap.wbmp', 'web' => 'application/vnd.xara', 'wiz' => 'application/msword',
    'wk1' => 'application/x-123', 'wmf' => 'windows/metafile', 'wml' => 'text/vnd.wap.wml', 'wmlc' => 'application/vnd.wap.wmlc',
    'wmls' => 'text/vnd.wap.wmlscript', 'wmlsc' => 'application/vnd.wap.wmlscriptc', 'word' => 'application/msword', 'wp' => 'application/wordperfect',
    'wp5' => 'application/wordperfect', 'wp5' => 'application/wordperfect6.0', 'wp6' => 'application/wordperfect', 'wpd' => 'application/wordperfect',
    'wpd' => 'application/x-wpwin', 'wq1' => 'application/x-lotus', 'wri' => 'application/mswrite', 'wri' => 'application/x-wri',
    'wrl' => 'application/x-world', 'wrl' => 'model/vrml', 'wrl' => 'x-world/x-vrml', 'wrz' => 'model/vrml',
    'wrz' => 'x-world/x-vrml', 'wsc' => 'text/scriplet', 'wsrc' => 'application/x-wais-source', 'wtk' => 'application/x-wintalk',
    'xbm' => 'image/x-xbitmap', 'xbm' => 'image/x-xbm', 'xbm' => 'image/xbm', 'xdr' => 'video/x-amt-demorun',
    'xgz' => 'xgl/drawing', 'xif' => 'image/vnd.xiff', 'xl' => 'application/excel', 'xla' => 'application/excel',
    'xla' => 'application/x-excel', 'xla' => 'application/x-msexcel', 'xlb' => 'application/excel', 'xlb' => 'application/vnd.ms-excel',
    'xlb' => 'application/x-excel', 'xlc' => 'application/excel', 'xlc' => 'application/vnd.ms-excel', 'xlc' => 'application/x-excel',
    'xld' => 'application/excel', 'xld' => 'application/x-excel', 'xlk' => 'application/excel', 'xlk' => 'application/x-excel',
    'xll' => 'application/excel', 'xll' => 'application/vnd.ms-excel', 'xll' => 'application/x-excel', 'xlm' => 'application/excel',
    'xlm' => 'application/vnd.ms-excel', 'xlm' => 'application/x-excel', 'xls' => 'application/excel', 'xls' => 'application/vnd.ms-excel',
    'xls' => 'application/x-excel', 'xls' => 'application/x-msexcel', 'xlt' => 'application/excel', 'xlt' => 'application/x-excel',
    'xlv' => 'application/excel', 'xlv' => 'application/x-excel', 'xlw' => 'application/excel', 'xlw' => 'application/vnd.ms-excel',
    'xlw' => 'application/x-excel', 'xlw' => 'application/x-msexcel', 'xm' => 'audio/xm', 'xml' => 'application/xml',
    'xml' => 'text/xml', 'xmz' => 'xgl/movie', 'xpix' => 'application/x-vnd.ls-xpix', 'xpm' => 'image/x-xpixmap',
    'xpm' => 'image/xpm', 'x-png' => 'image/png', 'xsr' => 'video/x-amt-showrun', 'xwd' => 'image/x-xwd',
    'xwd' => 'image/x-xwindowdump', 'xyz' => 'chemical/x-pdb', 'z' => 'application/x-compress', 'z' => 'application/x-compressed',
    'zip' => 'application/x-compressed', 'zip' => 'application/x-zip-compressed', 'zip' => 'application/zip', 'zip' => 'multipart/x-zip',
    'zoo' => 'application/octet-stream', 'zsh' => 'text/x-script.zsh',
);

*/