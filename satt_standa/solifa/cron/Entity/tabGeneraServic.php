<?php
/*
 cod_servic | int(4)      | NO   | PRI | 0                   |       |
| nom_servic | varchar(50) | NO   |     |                     |       |
| des_servic | text        | YES  |     | NULL                |       |
| rut_archiv | varchar(50) | YES  |     | NULL                |       |
| rut_jscrip | varchar(50) | YES  |     | NULL                |       |
| bod_jscrip | varchar(50) | YES  |     | NULL                |       |
| cod_aplica | int(4)      | NO   | MUL | 0                   |       |
| usr_creaci | varchar(15) | NO   |     | Administrador       |       |
| fec_creaci | datetime    | NO   |     | 0000-00-00 00:00:00 |       |
| usr_modifi | varchar(15) | YES  |     | NULL                |       |
| fec_modifi | datetime    | YES  |     | NULL                |       |
*/

class tabGeneraServic {

	private $cod_servic=null;
	private $nom_servic=null;
	private $des_servic=null;
	private $rut_archiv=null;
	private $rut_jscrip=null;
	private $bod_jscrip=null;
	private $cod_aplica=null;
	private $usr_creaci=null;
	private $fec_creaci=null;
	private $usr_modifi=null;
	private $fec_modifi=null;

	public function getCodServic(){
		return $this->cod_servic;
	}
	public function setCodServic($value){
		$this->cod_servic=$value;
	}

	public function getNomServic(){
		return $this->nom_servic;
	}
	public function setNomServic($value){
		$this->nom_servic=$value;
	}

	public function getDesServic(){
		return $this->des_servic;
	}
	public function setDesServic($value){
		$this->des_servic=$value;
	}

	public function getRutArchiv(){
		return $this->rut_archiv;
	}
	public function setRutArchiv($value){
		$this->rut_archiv=$value;
	}

	public function getRutJscrip(){
		return $this->rut_jscrip;
	}
	public function setRutJscrip($value){
		$this->rut_jscrip=$value;
	}

	public function getBodJscrip(){
		return $this->bod_jscrip;
	}
	public function setBodJscrip($value){
		$this->bod_jscrip=$value;
	}

	public function getCodAplica(){
		return $this->cod_aplica;
	}
	public function setCodAplica($value){
		$this->cod_aplica=$value;
	}

	public function getUsrCreaci(){
		return $this->usr_creaci;
	}
	public function setUsrCreaci($value){
		$this->usr_creaci=$value;
	}

	public function getFecCreaci(){
		return $this->fec_creaci;
	}
	public function setFecCreaci($value){
		$this->fec_creaci=$value;
	}

	public function getUsrModifi(){
		return $this->usr_modifi;
	}
	public function setUsrModifi($value){
		$this->usr_modifi=$value;
	}

	public function getFecModifi(){
		return $this->fec_modifi;
	}
	public function fetFecModifi($value){
		$this->fec_modifi=$value;
	}

	public function validation(){
		$error=array();
		if(empty($this->cod_servic)){
			array_push($error, get_class($this)." > cod_servic is required!");
		}
		if(empty($this->nom_servic)){
			array_push($error, get_class($this)." > nom_servic is required!");
		}
		if(empty($this->cod_aplica)){
			array_push($error, get_class($this)." > cod_aplica is required!");
		}
		if(empty($this->usr_creaci)){
			array_push($error, get_class($this)." > usr_creaci is required!");
		}
		if(empty($this->fec_creaci)){
			array_push($error, get_class($this)." > fec_creaci is required!");
		}
		return $error;
	}
}