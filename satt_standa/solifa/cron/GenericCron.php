<?php
error_reporting(E_ALL); 
ini_set('display_errors', '1');
include ("LogHelper.php");
include ("DbMysqli.php");
include ("Setting.php");

class GenericCron {
	/*!
   	* \var $instancedb
   	* \brief var thaqthget access your conection instance
   	*/
	private $instancedb=null;
	
	private $dbs=[];

	//variables de conexión
	private $db_host=null;
	private $db_user=null;
	private $db_pwd=null;
	private $db_name=null;
	private $db_port=null;
	private $db_socket=null;

	//variables para mantener configuaciones
	private $config=null;
	private $deploy=null;
	private $tmpCurrConfig=null;
	private $currentDbName=null;
	private $APP=null;
	private $DB=null;
	private $SCRIPT=null;

	private $separator="\n\n";//"<br>";

	//flag to rewrite sql sentence, encode your vars in utf8
	protected $special_filter=array();

	//tablas mínimas que deben existir para hacer cualquier proceso
	private $req_table_client_standa=["tab_genera_servic","tab_servic_servic"];
	private $req_table_client=["tab_genera_usuari","tab_genera_ciudad","tab_tercer_tercer","tab_genera_perfil","tab_perfil_servic"];

	/*! \fn: __construct
	 *  \brief: inicia el objeto
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    20/12/2016
	 */
	public function __construct($config){

		//unserialize config file
		if(file_exists($config)){
			$config=file_get_contents($config);
			$config=unserialize($config);
		}

		$this->config=$config;
		if(is_null(@$this->config->DEPLOY)){
			LogHelper::log("Configuration file is not valid");
			return false;
			exit;
		}

		LogHelper::log("---------------- begin stage ------------------");
		while($this->getServer()){
			LogHelper::log("---------------- open cycle ------------------");
			LogHelper::log("---------------- get server --------------------");
			LogHelper::log($this->deploy->NAME);
			LogHelper::log("--------------------------------------------");
			while($this->getConn()){
				LogHelper::log("---------------- get deploy --------------------");
				LogHelper::log($this->tmpCurrConfig);
				LogHelper::log("\n");
				while($this->getDbName()){
					LogHelper::log("---------------- get dbname --------------------");
					LogHelper::log(self::getCurrentDbName());
					$this->fnChildControllerDelegate();
					LogHelper::log("\n");
				}
				LogHelper::log("--------------------------------------------");
				LogHelper::log("\n");
			}
			LogHelper::log("---------------- close cycle ------------------");
			LogHelper::log("\n<hr>\n");
			LogHelper::log("\n\n\n");
		}
		LogHelper::log("\n<hr>\n");
		LogHelper::log("---------------- end stage ------------------");
		LogHelper::log("\n<hr>\n");
	}
	private function t($s,$withPreg=1){
		foreach ($this->special_filter as $k => $v) {
			//get find values valid only
			$v=$withPreg==1 ? preg_replace("/[^0-9A-Za-z ]/", "%", $v) : $v;
			$s=str_replace($k, $v, $s);
		}
		return $s;
	}
	protected function fnChildControllerDelegate(){		
		echo "GenericCron > fnChildControllerDelegate > Method to rewrite, set method in child Class \n";
		//default analytics
		switch(self::getStandaDbName()){
			case "consultor":
			break;
			case "sate_standa":
			break;
			case "satc_standa":
			break;
			case "satb_sta155":
			break;
			case "satt_standa":
			break;
			default:
			break;
		}
	}

	private function getConfigByKey($id){
		try{
			foreach ($this->config as $key => $value) {
				if($key==$id){
					return $value;
				}
			}
			return null;
		}catch(Exception $e){
			return null;
		}
	}
	private function getElement($config,$id){
		try{
			$config=self::getConfigByKey($config);
			foreach($config as $obj){
				if($obj->ID==$id){
					return $obj;
				}
			}
			return null;
		}catch(Exception $e){
			return null;
		}
	}
	private function cleanConn(){
		$this->db_host="disabled";
		$this->db_user="disabled";
		$this->db_pwd="disabled";
		$this->db_name="disabled";
		$this->db_port="disabled";
		$this->db_socket="disabled";
		$this->instancedb=null;
		$this->dbs=[];
	}
	private function setConn(){
		//soporte inicialmente para MYSQL - MariaDB
		//actualizar conexión
		if(!is_null($this->DB)){
			if($this->DB->ENGINE=="MYSQL" || $this->DB->ENGINE=="MariaDB"){
				$this->db_host=$this->DB->CONN->HOST;
				$this->db_user=$this->DB->CONN->USER;
				$this->db_pwd=$this->DB->CONN->PWD;
				$this->db_name=$this->DB->CONN->DBNAME;
  				$this->db_port=$this->DB->CONN->PORT;
  				$this->db_socket=$this->DB->CONN->SOCKET;
			}
		}	
	}
	private function findDb(){
		//limpiar registros viejos
		$this->dbs=[];
		//conectar a la db
		if($r=$this->setInstance()){
			//filtrar por el prefijo
			$query=self::t("show databases");
			$this->instancedb->setQuery($query);
			$this->instancedb->setFetchAll();
			$fetch=$this->instancedb->getFetchAll();
			if(sizeof($fetch)>0){
				foreach($fetch as $kf => $vf){
					if(!is_null($this->APP->PREFIX)){
						if(strpos($vf["Database"],$this->APP->PREFIX)>-1){
							array_push($this->dbs, $vf["Database"]);
						}
					}
				}
			}
		}
	}
	private function getServer(){
		$this->tmpCurrConfig = null;
		if(is_null($this->deploy)){
			$this->deploy = current($this->config->DEPLOY);
		}else{
			$this->deploy = next($this->config->DEPLOY);
		}
		if(is_object($this->deploy)){
			return true;
		}
		return false;
	}
	private function getConn(){
		if(is_null($this->tmpCurrConfig)){
			$this->tmpCurrConfig = current($this->deploy->DEV);
		}else{
			$this->tmpCurrConfig = next($this->deploy->DEV);
		}		
		if(is_object($this->tmpCurrConfig)){
			$this->APP=$this->getElement("APP",$this->tmpCurrConfig->REF_APP);
			$this->DB=$this->getElement("DB",$this->tmpCurrConfig->REF_DB);
			$this->SCRIPT=$this->getElement("SCRIPT",$this->tmpCurrConfig->REF_SCRIPT);
			if(!is_null($this->APP) && !is_null($this->DB) && !is_null($this->SCRIPT)){
				$this->setConn();
				$this->findDb();
				return true;
			}
			return false;
		}
		return false;
	}

	private function getDbName(){
		if(is_null($this->currentDbName)){
			$this->currentDbName = current($this->dbs);
		}else{
			$this->currentDbName = next($this->dbs);
		}
		if(is_string($this->currentDbName)){
			return true;
		}
		$this->currentDbName = null;
		$this->dbs=null;
		return false;
	}
	
	protected function getCurrentDbName(){
		return $this->currentDbName;
	}

	protected function getStandaDbName(){
		return $this->APP->STANDA;
	}

	protected function isStandaDbName(){
		return $this->getCurrentDbName()==$this->getStandaDbName();
	}

	protected function isRawDate($value){
		$pattern='~\b(CURDATE|TIMESTAMPDIFF|DATE_ADD|INTERVAL|CURRENT_DATE|NOW()|CURRENT_TIMESTAMP)\b~i';
		if(preg_match($pattern, $value)){
			return true;
		}
		return false;
	}

	/*! \fn: setInstance
	 *  \brief: optiene la instancia de conexion y controla las excepciones para programas complejos
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    29/09/2016
	 */
	private function setInstance(){
		try{
			//LogHelper::log(get_class($this).'::setInstance > ');
			$this->instancedb=new DbMysqli(
						array(
							"host"=>$this->db_host,
							"user"=>$this->db_user,
							"pwd"=>$this->db_pwd,
							"db"=>$this->db_name,
              				"port"=>$this->db_port,
              				"socket"=>$this->db_socket
						)
				);
			return $this->instancedb->getErrorFatal();
		}catch(Exception $e){
			LogHelper::error($e);
		}
	}
	/*! \fn: getCodServicMax
	 *  \brief: obtiene el código de servicio máximo
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    20/12/2016
	 */
	protected function getCodServicMax(){
		try{
			$query = self::t("SELECT max(cod_servic) as max FROM ".$this->getStandaDbName().".tab_genera_servic",1);
			$this->instancedb->setQuery($query);
			$this->instancedb->setFetchAll();
			$fetch=$this->instancedb->getFetchAll();
			$max=0;
			if(sizeof($fetch)>0){
				$max=$fetch[0]["max"];
				settype($max,'integer');
			}
			return $max;
		}catch(Exception $e){
			LogHelper::error($e);
			return -1;
		}
	}
	/*! \fn: getCodServic
	 *  \brief: obtiene el código de servicio de acuerdo al filtro
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    20/12/2016
	 */
	protected function getCodServic($filter=""){
		try{
			$query = self::t("SELECT cod_servic FROM ".self::getStandaDbName().".tab_genera_servic where $filter",1);
			$this->instancedb->setQuery($query);
			$this->instancedb->setFetchAll();
			$fetch=$this->instancedb->getFetchAll();
			$cod_servic=-1;
			if(sizeof($fetch)>0){
				$cod_servic=$fetch[0]["cod_servic"];
				settype($cod_servic,'integer');
			}else{
				LogHelper::error(get_class($this)."::getCodServic > db > ".self::getStandaDbName()." > filter > $filter > without results > sql > \n$query");
			}			
			return $cod_servic;
		}catch(Exception $e){
			LogHelper::error($e);
			return -2;
		}
	}

	/*! \fn: setCodServic
	 *  \brief: inserta un codigo de servicio
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    20/12/2016
	 */
	protected function setCodServic($cod_servic=null,$nom_servic=null,
			$des_servic=null,$rut_archiv=null,$rut_jscrip=null,
			$bod_jscrip=null,$cod_aplica=null,$usr_creaci=null,$fec_creaci=null,
			$usr_modifi=null,$fec_modifi=null){
		try{
			$error=false;
			if(empty($cod_servic)){
				LogHelper::error(get_class($this)."::setCodServic > cod_servic is required!"); $error=true;
			}
			if(empty($nom_servic)){
				LogHelper::error(get_class($this)."::setCodServic > nom_servic is required!"); $error=true;
			}
			if(empty($cod_aplica)){
				LogHelper::error(get_class($this)."::setCodServic > cod_aplica is required!"); $error=true;
			}
			if(empty($usr_creaci)){
				LogHelper::error(get_class($this)."::setCodServic > usr_creaci is required!"); $error=true;
			}
			if(empty($fec_creaci)){
				LogHelper::error(get_class($this)."::setCodServic > fec_creaci is required!"); $error=true;
			}

			if($error==true)
				return -1;

			$fec_creaci = self::isRawDate($fec_creaci) ? $fec_creaci : "'$fec_creaci'";
			$query = self::t("INSERT INTO ".self::getStandaDbName().".tab_genera_servic (
					cod_servic,nom_servic,des_servic,
					rut_archiv,rut_jscrip,bod_jscrip,
				cod_aplica,usr_creaci,fec_creaci
				) VALUES (
					$cod_servic,'$nom_servic',
					'$des_servic','$rut_archiv','$rut_jscrip',
					'$bod_jscrip',$cod_aplica,'$usr_creaci',
					$fec_creaci
				);",1);
			$this->instancedb->setQuery($query);
			$this->instancedb->setAffectedRows();
			return $this->instancedb->getAffectedRows();
		}catch(Exception $e){
			LogHelper::error($e);
			return -1;
		}
	}
	/*! \fn: saveEntity
	 *  \brief: inserta un codigo de servicio que llega de una entidad
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    20/12/2016
	 */
	protected function saveEntity($table,$entity){
		try{
			//$fec_creaci = self::isRawDate($fec_creaci) ? $fec_creaci : "'$fec_creaci'";
			$column = array();
			$values = array();
			
			$reflector = new ReflectionClass($entity);
			$properties = $reflector->getProperties();
			foreach ($properties as $value) {
				$st1="";
				$ch1 = explode("_",$value->name);
				if(sizeof($ch1)>0){
					foreach ($ch1 as $key2 => $value2) {
						$st1.=ucfirst($value2);
					}
				}
				array_push($column, $value->name);
				$v=call_user_func(array($entity, "get".ucfirst($st1)), null);
				switch (gettype($v)) {
					case "boolean":
					case "integer":
					case "double":
					break;

					case "NULL":
						$v="NULL";	
					break;

					case "array":
					case "object":
					case "resource":
					case "unknown type":
					case "string":
						$v="'$v'";						
					break;
				}
				array_push($values, $v);
			}
			
			$format="INSERT INTO %s (%s) values(%s)";
			$query = self::t(sprintf($format,$table,implode(",",$column),implode(",",$values)),0);
			$this->instancedb->setQuery($query);
			$this->instancedb->setAffectedRows();
			return $this->instancedb->getAffectedRows();
		}catch(Exception $e){
			LogHelper::error($e);
			return -1;
		}
	}
	/*! \fn: existsCodServicRel
	 *  \brief: verifica si existe una relación de padre a hijo de un servicio
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    20/12/2016
	 */
	protected function existsCodServicRel($data1,$data2){
		try{
			$query = self::t("SELECT 1 FROM ".self::getStandaDbName().".tab_servic_servic where cod_serpad='$data1' and cod_serpad='$data2'",1);
			$this->instancedb->setQuery($query);
			$this->instancedb->setFetchAll();
			$fetch=$this->instancedb->getFetchAll();
			if(sizeof($fetch)>0){
				return 1;
			}else{
				return 0;
			}
		}catch(Exception $e){
			LogHelper::error($e);
			return -1;
		}
	}
	/*! \fn: setCodServicRel
	 *  \brief: ingresa la relación de padre a hijo de un servicio
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    20/12/2016
	 */
	protected function setCodServicRel($parent,$child){
		try{
			$query = self::t("INSERT INTO ".self::getStandaDbName().".tab_servic_servic (cod_serpad,cod_serhij) VALUES ($parent,$child);",1);
			$this->instancedb->setQuery($query);
			LogHelper::log(get_class($this)."::setCodServicRel > Success, Already has relationships created between parent and child, info: parent: $parent, child $child");
			return true;
		}catch(Exception $e){
			LogHelper::log(get_class($this)."::setCodServicRel > Error, Set service relation with info: parent: $parent, child: $child");
			return false;
		}
	}
	/*! \fn: addCodServicRel
	 *  \brief: busca y relaciona un servicio (padre a hijo)
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    20/12/2016
	 */
	protected function addCodServicRel($parent,$child){
		LogHelper::log(get_class($this)."::addCodServicRel > parent: $parent | child:$child");
		if(self::existsCodServicRel($parent,$child)==0){
			self::setCodServicRel($parent,$child);
		}
	}
	/*! \fn: existsServicAndProfile
	 *  \brief: verifica si existe una relación de padre a hijo de un servicio
	 *  \author: INTRARED <fesus.rocuts@intrared.net>
	 *  \version: 1.0
	 *  \date:    20/12/2016
	 */
	protected function existsServicAndProfile($profile,$service){
		try{
			$query = self::t("SELECT 1 FROM ".self::getCurrentDbName().".tab_perfil_servic where cod_perfil=$profile and cod_servic=$service",1);
			$this->instancedb->setQuery($query);
			$this->instancedb->setFetchAll();
			$fetch=$this->instancedb->getFetchAll();
			if(sizeof($fetch)>0){
				return 1;
			}else{
				return 0;
			}
		}catch(Exception $e){
			LogHelper::error($e);
			return -1;
		}
	}

	protected function setServicAndProfile($profile,$service){
		try{
			$query = self::t("INSERT INTO ".self::getCurrentDbName().".tab_perfil_servic (cod_perfil,cod_servic) VALUES ($profile,$service);",1);
			$this->instancedb->setQuery($query);
			LogHelper::log(get_class($this)."::setServicAndProfile > Success, Already has relationships created between profile and service, info: profile: $profile, service $service");
			return true;
		}catch(Exception $e){
			LogHelper::error(get_class($this)."::setServicAndProfile > Error, Set service relation with info: profile: $profile, service: $service");
			return false;
		}
	}

	protected function addServicAndProfile($profile,$service){
		LogHelper::log(get_class($this)."::addServicAndProfile > profile: $profile | service:$service");
		if(self::existsServicAndProfile($profile,$service)==0){
			self::setServicAndProfile($profile,$service);
		}
	}

	protected function execQuery($sql){
		try{
			$query = self::t($sql,1);
			LogHelper::log(get_class($this)."::execQuery > sql > ");
			LogHelper::log($sql);
			$this->instancedb->setQuery($query);
			return true;
		}catch(Exception $e){
			LogHelper::error($e);
			return false;
		}
	}

	protected function getAllTables(){
		try{
			$this->instancedb->setQuery("use ".self::getCurrentDbName());
			$this->instancedb->setQuery("show tables;");
			$this->instancedb->setFetchAll();
			$tables=$this->instancedb->getFetchAll();
			$ct=[];
			if(sizeof($tables)>0){
				foreach($tables as $table){
					foreach($table as $column){
						array_push($ct, $column);
					}
				}
			}
			return $ct;
		}catch(Exception $e){
			LogHelper::log($e);
			return array();
		}
	}

	protected function existsGuarantees(){
		$tables=self::getAllTables();
		if(sizeof($tables)>0){
			if(self::isStandaDbName()){
				foreach($this->req_table_client_standa as $t){
					if(!in_array($t,$tables)){
						LogHelper::error("Alert > List of tables names is incomplete, please, contact your db adminitrator");
						return false;
					}
				}
			}else{
				foreach($this->req_table_client as $t){
					if(!in_array($t,$tables)){
						LogHelper::error("Alert > List of tables names is incomplete, please, contact your db adminitrator");
						return false;
					}
				}
			}
		}else{
			return false;
		}
		return true;
	}

	protected function error_critical(){ 
		throw new \Exception("Stop!");
	}

	protected function getFn($fn,$data){
		try{
			if(!method_exists($this, $fn))
				self::error_critical();
			return call_user_func(array($this, $fn), $data);
		}catch(Exception $e){
			LogHelper::error($e);
			exit;
		}
	}

	//metodo para obtener el codigo de servicio, o crearlo si es el caso
	protected function forceGetCodServic($suffix=null,$data=null){
		try{
			$cod_servic = self::getFn("getCodServic".$suffix,$data);
			if($cod_servic<0){
				if(self::getFn("setCodServic".$suffix,$data)>0){
					LogHelper::log(get_class($this)."::forceGetCodServic > setCodServic$suffix > OK, Service saved");
					$cod_servic = self::getFn("getCodServic".$suffix,$data);
				}else{
					LogHelper::error(get_class($this)."::forceGetCodServic > setCodServic$suffix > Error, Service not saved");
					return -1;
				}
			}
			settype($cod_servic, 'integer');
			LogHelper::log("Service Code getCodServic$suffix: $cod_servic");
			return $cod_servic;
		}catch(Exception $e){
			LogHelper::error($e);
			return -2;
		}
	}
	//alias of forceGetCodServic
	protected function addCodServic($suffix=null,$data=null){
		return self::forceGetCodServic($suffix,$data);
	}
}
