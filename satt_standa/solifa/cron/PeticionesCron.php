<?php
error_reporting(E_ALL); 
ini_set('display_errors', '1');
include ("GenericCron.php");
include ("Entity/tabGeneraServic.php");

class PeticionesCron extends GenericCron {
	
	//flag to rewrite sql sentence, encode your vars in utf8
	//el filtro cambia los caracteres especiales por %, con el fin de permitiar llegar al dato sin problemas de codificación
	//ajustelo el metodo "t" en GenericCron en caso de necesitar algo extra o deshabilitarlo
	protected $special_filter=array(
			"{{usuario_cron}}"=>"Administrador",//todos
			"{{cod_aplica}}"=>1,//todos
			"{{sindato}}"=>"",//todos
			"{{controltrafico1}}"=>"Control Tráfico",
			"{{gestionoperacion}}"=>533,
			"{{soliciafaro1}}"=>"Solicitudes a faro",//cliente faro y faro
			"{{soliciafaro2}}"=>"solifa/inf_solici_solici.php",//exclusivamente faro
			"{{soliciafaro3}}"=>"Solicitud",//exclusivamente cliente de faro
			"{{soliciafaro4}}"=>"solifa/ins_solici_solici.php",//exclusivamente cliente de faro
			"{{soliciafaro5}}"=>"Solicitudes pendientes",//exclusivamente cliente de faro
			"{{soliciafaro6}}"=>"solifa/pen_solici_solici.php"//exclusivamente cliente de faro
		);


	protected function fnChildControllerDelegate(){

		//Adjust at your convenience this
		//here too can a group of SAt exclude
		switch(self::getStandaDbName()){
			//case "consultor":
			case "sate_standa":
			case "satc_standa":
			case "satb_sta155":
				if(self::existsGuarantees()){
					if(self::isStandaDbName()){
						self::getConfigClientStanda();
					}else{
						self::getConfigClientClient();
					}
				}
			break;
			case "satt_standa":
				if(self::existsGuarantees()){
					if(self::isStandaDbName()){
						self::getConfigFaroStanda();
					}else{
						self::getConfigFaroClient();
					}	
				}
			break;
			default:
			break;
		}
	}

	protected function getCodServicControlTrafico(){
		return self::getCodServic("nom_servic LIKE '%{{controltrafico1}}%'");
	}
	
	/*****************************************************************/ 
	/************************* external client ***********************/ 
	/*****************************************************************/ 
	protected function getCodServicSoliciafaro1(){
		return self::getCodServic("lower(nom_servic) LIKE lower('%{{soliciafaro1}}%')");
	}

	//metodo de prueba para guardar una entidad
	protected function setCodServicSoliciafaro1(){
		try{
			$tabGeneraServic = new tabGeneraServic;
			$tabGeneraServic->setcodServic(self::getCodServicMax()+1);
			$tabGeneraServic->setnomServic('{{soliciafaro1}}');
			$tabGeneraServic->setdesServic('{{soliciafaro1}}');
			$tabGeneraServic->setcodAplica('{{cod_aplica}}');
			$tabGeneraServic->setusrCreaci('{{usuario_cron}}');
			$tabGeneraServic->setfecCreaci(date("Y-m-d H:i:s",time()));
			$validation=$tabGeneraServic->validation();
			if(empty($validation)){
				return self::saveEntity(self::getStandaDbName().".tab_genera_servic",$tabGeneraServic);
			}else{
				LogHelper::error($validation);
				return -1;
			}
		}catch(Exception $e){
			LogHelper::error($e);
			return -2;
		}
	}


	protected function getCodServicSoliciafaro1Hijo1(){
		return self::getCodServic("lower(nom_servic) LIKE lower('%{{soliciafaro3}}%') and lower(des_servic) LIKE lower('%{{soliciafaro3}}%') and lower(rut_archiv) LIKE lower('%{{soliciafaro4}}%')");
	}

	protected function setCodServicSoliciafaro1Hijo1(){
		try{
			$tabGeneraServic = new tabGeneraServic;
			$tabGeneraServic->setcodServic(self::getCodServicMax()+1);
			$tabGeneraServic->setnomServic('{{soliciafaro3}}');
			$tabGeneraServic->setdesServic('{{soliciafaro3}}');
			$tabGeneraServic->setRutArchiv('{{soliciafaro4}}');
			$tabGeneraServic->setcodAplica('{{cod_aplica}}');
			$tabGeneraServic->setusrCreaci('{{usuario_cron}}');
			$tabGeneraServic->setfecCreaci(date("Y-m-d H:i:s",time()));
			$validation=$tabGeneraServic->validation();
			if(empty($validation)){
				return self::saveEntity(self::getStandaDbName().".tab_genera_servic",$tabGeneraServic);
			}else{
				LogHelper::error($validation);
				return -1;
			}
		}catch(Exception $e){
			LogHelper::error($e);
			return -2;
		}
	}

	protected function getCodServicSoliciafaro1Hijo2(){
		return self::getCodServic("lower(nom_servic) LIKE lower('%{{soliciafaro5}}%') and lower(des_servic) LIKE lower('%{{soliciafaro5}}%') and lower(rut_archiv) LIKE lower('%{{soliciafaro6}}%')");
	}

	protected function setCodServicSoliciafaro1Hijo2(){
		try{
			$tabGeneraServic = new tabGeneraServic;
			$tabGeneraServic->setcodServic(self::getCodServicMax()+1);
			$tabGeneraServic->setnomServic('{{soliciafaro5}}');
			$tabGeneraServic->setdesServic('{{soliciafaro5}}');
			$tabGeneraServic->setRutArchiv('{{soliciafaro6}}');
			$tabGeneraServic->setcodAplica('{{cod_aplica}}');
			$tabGeneraServic->setusrCreaci('{{usuario_cron}}');
			$tabGeneraServic->setfecCreaci(date("Y-m-d H:i:s",time()));
			$validation=$tabGeneraServic->validation();
			if(empty($validation)){
				return self::saveEntity(self::getStandaDbName().".tab_genera_servic",$tabGeneraServic);
			}else{
				LogHelper::error($validation);
				return -1;
			}
		}catch(Exception $e){
			LogHelper::error($e);
			return -2;
		}
	}



	/*****************************************************************/ 
	/******************************** gl *****************************/ 
	/*****************************************************************/ 
	protected function getcodServicGestionOperacion(){
		return self::getCodServic("cod_servic = {{gestionoperacion}}");
	}

	protected function getCodServicSoliciafaro(){
		return self::getCodServic("lower(nom_servic) LIKE lower('%{{soliciafaro1}}%')");
	}

	protected function setCodServicSoliciafaro(){
		try{
			$tabGeneraServic = new tabGeneraServic;
			$tabGeneraServic->setcodServic(self::getCodServicMax()+1);
			$tabGeneraServic->setnomServic('{{soliciafaro1}}');
			$tabGeneraServic->setdesServic('{{soliciafaro1}}');
			$tabGeneraServic->setRutArchiv('{{soliciafaro2}}');
			$tabGeneraServic->setcodAplica('{{cod_aplica}}');
			$tabGeneraServic->setusrCreaci('{{usuario_cron}}');
			$tabGeneraServic->setfecCreaci(date("Y-m-d H:i:s",time()));
			$validation=$tabGeneraServic->validation();
			if(empty($validation)){
				return self::saveEntity(self::getStandaDbName().".tab_genera_servic",$tabGeneraServic);
			}else{
				LogHelper::error($validation);
				return -1;
			}
		}catch(Exception $e){
			LogHelper::error($e);
			return -2;
		}
	}

	
	private function getConfigFaroStanda(){
		
		LogHelper::log("Set data config to faro standa client:");
		
		//traer el codigo de servicio maximo
		LogHelper::log("Constraint 1: get maximum service code");
		$codServicMax = self::getCodServicMax();
		if($codServicMax<0 || gettype($codServicMax)!="integer")
			return false;

		//codigo de servicio de control trafico 
		LogHelper::log("Constraint 2: get {{gestionoperacion}} service code");
		$codServicGestionOperacion = self::getcodServicGestionOperacion();
		if($codServicGestionOperacion<0 || gettype($codServicGestionOperacion)!="integer")
			return false;

		//obtenerl el codigo de servicio {{soliciafaro1}}
		$codServicSoliciafaro = self::addCodServic("Soliciafaro",null);
		if($codServicSoliciafaro<0 || gettype($codServicSoliciafaro)!="integer")
			return false;

		LogHelper::log("Set parent and children relation service:");
		self::addCodServicRel($codServicGestionOperacion,$codServicSoliciafaro);


	}
	private function getConfigFaroClient(){
		LogHelper::log("Set data config to faro client:");
		if(self::getCodServicSoliciafaro()>-1) self::addServicAndProfile(1,self::getCodServicSoliciafaro());
		LogHelper::log("Create tables and triggers to client:");
		self::execQueryConfigFaro();

	}

	private function execQueryConfigFaro(){
		$array=[];
		$array[]="use `".self::getCurrentDbName()."`;";
		$array[]="CREATE TABLE IF NOT EXISTS `".self::getCurrentDbName()."`.`tab_solici_estado` (
		`cod_estado` int(1) NOT NULL,
		`nom_estado` VARCHAR(13) NOT NULL,
		`num_porcen` int(2) NOT NULL,
		`usr_creaci` VARCHAR(15) NOT NULL,
		`fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`usr_modifi` VARCHAR(15) NULL,
		`fec_modifi` VARCHAR(15) NULL,
		primary key (`cod_estado`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='';";
	 	$array[]="CREATE TABLE IF NOT EXISTS `".self::getCurrentDbName()."`.`tab_solici_tiposx` (
		`cod_tipsol` int(1) not null,
		`nom_tipsol` VARCHAR(25) NOT NULL,
		`usr_creaci` VARCHAR(15) NOT NULL,
		`fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`usr_modifi` VARCHAR(15) NULL,
		`fec_modifi` VARCHAR(15) NULL,
		primary key (`cod_tipsol`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Tipos de solicitud(Pestañas = 4) 1. Creación de rutas, 2. Seguimiento especial, 3. PQR, 4. Otras solicitudes';";
	 	$array[]="CREATE TABLE IF NOT EXISTS `".self::getCurrentDbName()."`.`tab_solici_subtip` (
		`cod_subtip` int(1) NOT NULL,
		`nom_subtip` VARCHAR(20) NOT NULL,
		`cod_tipsol` int(1) NOT NULL,
		`usr_creaci` VARCHAR(15) NOT NULL,
		`fec_creaci` DATETIME not null,
		`usr_modifi` VARCHAR(15) NULL,
		`fec_modifi` VARCHAR(15) NULL,
		primary key (`cod_subtip`),
		constraint `tab_solici_subtip_ctfk_1` foreign key (`cod_tipsol`) references `".self::getCurrentDbName()."`.`tab_solici_tiposx` (`cod_tipsol`) on delete cascade on update cascade
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Subtipo de solicitud';";
	 	$array[]="CREATE TABLE IF NOT EXISTS `".self::getCurrentDbName()."`.`tab_solici_datosx` (
		`cod_solici` int(4) NOT NULL,
		`cod_transp` VARCHAR(10) NOT NULL,
		`nom_aplica` VARCHAR(13) NOT NULL,
		`url_aplica` VARCHAR(100) NULL,
		`cod_usrsol` VARCHAR(15) NOT NULL,
		`nom_usrsol` VARCHAR(60) NOT NULL,
		`dir_usrmai` VARCHAR(50) NOT NULL,
		`num_usrfij` int(7) NULL,
		`num_usrcel` int(10) NOT NULL,
		`usr_creaci` VARCHAR(15) NOT NULL,
		`fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`usr_modifi` VARCHAR(15) NULL,
		`fec_modifi` VARCHAR(15) NULL,
		primary key (`cod_solici`),
		constraint `tab_solici_datosx_ctfk_1` foreign key (`cod_transp`) references `".self::getCurrentDbName()."`.`tab_tercer_tercer` (`cod_tercer`) on delete cascade on update cascade
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Datos de los solicitantes, lo cual se registra una única vez, varios solicitantes (cod_usrsol -> UK) pertenecen a la misma empresa (cod_transp)';";
	 	$array[]="CREATE TABLE IF NOT EXISTS `".self::getCurrentDbName()."`.`tab_solici_solici` (
		`num_solici` int(11) not null AUTO_INCREMENT,
		`cod_solici` int(4) not null,
		`cod_estado` int(1) not null,
		`cod_tipsol` INT(1) not null,
		`cod_subtip` INT(1) not null,
		`cod_ciuori` int(11) null,
		`cod_ciudes` int(11) null,
		`nom_viaxxx` VARCHAR(100) null,
		`fec_iniseg` DATETIME null,
		`fec_finseg` DATETIME null,
		`lis_placas` VARCHAR(255) null,
		`nom_asunto` VARCHAR(100) null,
		`dir_archiv` VARCHAR(30) null,
		`obs_solici` VARCHAR(255) null,
		`usr_creaci` VARCHAR(15) not null,
		`fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		`usr_modifi` VARCHAR(15) null,
		`fec_modifi` VARCHAR(15) null,
		primary key (`num_solici`),
		constraint `tab_solici_solici_csfk_1` foreign key (`cod_solici`) references `".self::getCurrentDbName()."`.`tab_solici_datosx` (`cod_solici`) on delete cascade on update cascade,
		constraint `tab_solici_solici_cefk_1` foreign key (`cod_estado`) references `".self::getCurrentDbName()."`.`tab_solici_estado` (`cod_estado`) on delete cascade on update cascade,
		constraint `tab_solici_solici_ctfk_1` foreign key (`cod_tipsol`) references `".self::getCurrentDbName()."`.`tab_solici_tiposx` (`cod_tipsol`) on delete cascade on update cascade,
		constraint `tab_solici_solici_csfk_2` foreign key (`cod_subtip`) references `".self::getCurrentDbName()."`.`tab_solici_subtip` (`cod_subtip`) on delete cascade on update cascade,
		constraint `tab_solici_solici_ccfk_1` foreign key (`cod_ciuori`) references `".self::getCurrentDbName()."`.`tab_genera_ciudad` (`cod_ciudad`) on delete cascade on update cascade,
		constraint `tab_solici_solici_ccfk_2` foreign key (`cod_ciudes`) references `".self::getCurrentDbName()."`.`tab_genera_ciudad` (`cod_ciudad`) on delete cascade on update cascade
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Datos de la solicitud que realiza un usuario de una empresa, dependiendo de cod_tipsol, se llenan algunos campos que estan como nulos, ya que dependiendo del tipo de solicitud se diligencian unos campos y otros no.';";
		$array[]="CREATE TABLE IF NOT EXISTS `".self::getCurrentDbName()."`.`tab_solici_seguim` (
		`num_seguim` INT(11) NOT NULL AUTO_INCREMENT,
		`num_solici` INT(11) NOT NULL,
		`cod_estado` INT(1) NOT NULL,
		`obs_seguim` TEXT NOT NULL,
		`dir_archiv` VARCHAR(30) NULL,
		`usr_creaci` VARCHAR(15) NOT NULL,
		`fec_creaci` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (`num_seguim`),
		CONSTRAINT `tab_solici_seguim_nsfk_1` FOREIGN KEY (`num_solici`) REFERENCES `".self::getCurrentDbName()."`.`tab_solici_solici` (`num_solici`) ON DELETE CASCADE ON UPDATE CASCADE,
		CONSTRAINT `tab_solici_seguim_cefk_1` FOREIGN KEY (`cod_estado`) REFERENCES `".self::getCurrentDbName()."`.`tab_solici_estado` (`cod_estado`) ON DELETE CASCADE ON UPDATE CASCADE,
		CONSTRAINT `tab_solici_seguim_ucfk_1` FOREIGN KEY (`usr_creaci`) REFERENCES `".self::getCurrentDbName()."`.`tab_genera_usuari` (`cod_usuari`) ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Datos de seguimiento de cada soicitud, son como historiales de una tarea, es necesario registrar el cod_estado por historial para saber que usuario hizo cambios';
	";
		/*
		//trigger toca ejecutarlo a mano temporalmente...
		$array[]="use `".self::getCurrentDbName()."`;";
		$array[]="DELIMITER $$";
		$array[]="CREATE TRIGGER setSerguimEstado AFTER INSERT ON `satt_faro`.`tab_solici_seguim` FOR EACH ROW BEGIN UPDATE `satt_faro`.`tab_solici_solici` SET `cod_estado`=NEW.cod_estado WHERE `num_solici`=NEW.num_solici; END; $$";
		$array[]="DELIMITER ;";
		*/
		
		$c=0;
		while($c<sizeof($array)){
			if(self::execQuery($array[$c])){
				LogHelper::log("Success, Query executed successfully");
			}else{
				LogHelper::log("Error, query execution failed!");
			}
			$c++;
		}
	}

	private function getConfigClientStanda(){

		LogHelper::log("Set data config to standa client:");

		//traer el codigo de servicio maximo
		LogHelper::log("Constraint 1: get maximum service code");
		$codServicMax = self::getCodServicMax();
		if($codServicMax<0 || gettype($codServicMax)!="integer")
			return false;

		//codigo de servicio de control trafico 
		LogHelper::log("Constraint 2: get {{controltrafico1}} service code");
		$codServicControlTrafico = self::getCodServicControlTrafico();
		if($codServicControlTrafico<0 || gettype($codServicControlTrafico)!="integer")
			return false;

		LogHelper::log("Get the updated list to service code:");
		//obtenerl el codigo de servicio {{soliciafaro1}}
		$codServicSoliciafaro1 = self::addCodServic("Soliciafaro1",null);
		if($codServicSoliciafaro1<0 || gettype($codServicSoliciafaro1)!="integer")
			return false;

		//obtenerl el codigo de servicio {{soliciafaro1}}
		$codServicSoliciafaro1Hijo1 = self::addCodServic("Soliciafaro1Hijo1",null);
		if($codServicSoliciafaro1Hijo1<0 || gettype($codServicSoliciafaro1Hijo1)!="integer")
			return false;

		$codServicSoliciafaro1Hijo2 = self::addCodServic("Soliciafaro1Hijo2",null);
		if($codServicSoliciafaro1Hijo2<0 || gettype($codServicSoliciafaro1Hijo2)!="integer")
			return false;

		//crear relación entre padre e hijo
		LogHelper::log("Set parent and children relation service:");
		self::addCodServicRel($codServicControlTrafico,$codServicSoliciafaro1);
		self::addCodServicRel($codServicSoliciafaro1,$codServicSoliciafaro1Hijo1);
		self::addCodServicRel($codServicSoliciafaro1,$codServicSoliciafaro1Hijo2);

	}

	private function getConfigClientClient(){
		LogHelper::log("Set data config to client:");
		LogHelper::log("Set profile and service to client:");
		if(self::getCodServicSoliciafaro1()>-1) self::addServicAndProfile(1,self::getCodServicSoliciafaro1());
		if(self::getCodServicSoliciafaro1Hijo1()>-1) self::addServicAndProfile(1,self::getCodServicSoliciafaro1Hijo1());
		if(self::getCodServicSoliciafaro1Hijo2()>-1) self::addServicAndProfile(1,self::getCodServicSoliciafaro1Hijo2());
		LogHelper::log("Create tables and triggers to client:");
		self::execQueryConfigClient();
	}

	private function execQueryConfigClient(){
		$array=[];
		$array[]="use `".self::getCurrentDbName()."`;";
		$array[]="CREATE TABLE IF NOT EXISTS `".self::getCurrentDbName()."`.`tab_errorx_solici` (
		`cod_consec` INT(6) NOT NULL,
		`cod_tipsol` INT(1) NOT NULL,
		`xml_reques` text NOT NULL,
		`xml_respon` text NOT NULL,
		`cod_respon` INT(4) NOT NULL,
		`msg_respon` VARCHAR(100) NOT NULL,
		`usr_creaci`VARCHAR(20) NOT NULL,
		`fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (`cod_consec`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='BITÁCORA DE ERRORES CON RESPECTO AL ENVIO DE LA INFORMACIÓN POR EL WS AL GL';";
		$c=0;
		while($c<sizeof($array)){
			if(self::execQuery($array[$c])){
				LogHelper::log("Success, Query executed successfully");
			}else{
				LogHelper::log("Error, query execution failed!");
			}
			$c++;
		}
	}
}

new PeticionesCron($setting_file);