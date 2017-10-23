<?php
$db_base_local=<<<EOF
/************* gl **************/
use mysql;
use `satt_standa`;
DROP TABLE `satt_standa`.`tab_errorx_solici`;
CREATE TABLE `satt_standa`.`tab_errorx_solici` (
	`cod_consec` INT(6) NOT NULL,
	`cod_tipsol` INT(1) NOT NULL,
	`xml_reques` text NOT NULL,
	`xml_respon` text NOT NULL,
	`cod_respon` INT(4) NOT NULL,
	`msg_respon` VARCHAR(100) NOT NULL,
	`usr_creaci`VARCHAR(20) NOT NULL,
	`fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`cod_consec`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='BITÁCORA DE ERRORES CON RESPECTO AL ENVIO DE LA INFORMACIÓN POR EL WS AL GL';
show tables;


use `satt_faro`;
/************* existe en el servidor **************/
/************* NO existe en el servidor **************/
drop table if exists `satt_faro`.`tab_solici_estado`;
CREATE TABLE `satt_faro`.`tab_solici_estado` (
	`cod_estado` int(1) NOT NULL,
	`nom_estado` VARCHAR(13) NOT NULL,
	`num_porcen` int(2) NOT NULL,
	`usr_creaci` VARCHAR(15) NOT NULL,
	`fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`usr_modifi` VARCHAR(15) NULL,
	`fec_modifi` VARCHAR(15) NULL,
	primary key (`cod_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='';
drop table if exists `satt_faro`.`tab_solici_tiposx`;
CREATE TABLE `satt_faro`.`tab_solici_tiposx` (
	`cod_tipsol` int(1) not null,
	`nom_tipsol` VARCHAR(25) NOT NULL,
	`usr_creaci` VARCHAR(15) NOT NULL,
	`fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`usr_modifi` VARCHAR(15) NULL,
	`fec_modifi` VARCHAR(15) NULL,
	primary key (`cod_tipsol`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Tipos de solicitud(Pestañas = 4) 1. Creación de rutas, 2. Seguimiento especial, 3. PQR, 4. Otras solicitudes';
drop table if exists `satt_faro`.`tab_solici_subtip`;
CREATE TABLE `satt_faro`.`tab_solici_subtip` (
	`cod_subtip` int(1) NOT NULL,
	`nom_subtip` VARCHAR(20) NOT NULL,
	`cod_tipsol` int(1) NOT NULL,
	`usr_creaci` VARCHAR(15) NOT NULL,
	`fec_creaci` DATETIME not null,
	`usr_modifi` VARCHAR(15) NULL,
	`fec_modifi` VARCHAR(15) NULL,
	primary key (`cod_subtip`),
	constraint `tab_solici_subtip_ctfk_1` foreign key (`cod_tipsol`) references `satt_faro`.`tab_solici_tiposx` (`cod_tipsol`) on delete cascade on update cascade
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Subtipo de solicitud';
drop table if exists `satt_faro`.`tab_solici_datosx`;
CREATE TABLE `satt_faro`.`tab_solici_datosx` (
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
	constraint `tab_solici_datosx_ctfk_1` foreign key (`cod_transp`) references `satt_faro`.`tab_tercer_tercer` (`cod_tercer`) on delete cascade on update cascade
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Datos de los solicitantes, lo cual se registra una única vez, varios solicitantes (cod_usrsol -> UK) pertenecen a la misma empresa (cod_transp)';
DROP TABLE IF EXISTS `satt_faro`.`tab_solici_solici`;
CREATE TABLE `satt_faro`.`tab_solici_solici` (
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
	constraint `tab_solici_solici_csfk_1` foreign key (`cod_solici`) references `satt_faro`.`tab_solici_datosx` (`cod_solici`) on delete cascade on update cascade,
	constraint `tab_solici_solici_cefk_1` foreign key (`cod_estado`) references `satt_faro`.`tab_solici_estado` (`cod_estado`) on delete cascade on update cascade,
	constraint `tab_solici_solici_ctfk_1` foreign key (`cod_tipsol`) references `satt_faro`.`tab_solici_tiposx` (`cod_tipsol`) on delete cascade on update cascade,
	constraint `tab_solici_solici_csfk_2` foreign key (`cod_subtip`) references `satt_faro`.`tab_solici_subtip` (`cod_subtip`) on delete cascade on update cascade,
	constraint `tab_solici_solici_ccfk_1` foreign key (`cod_ciuori`) references `satt_faro`.`tab_genera_ciudad` (`cod_ciudad`) on delete cascade on update cascade,
	constraint `tab_solici_solici_ccfk_2` foreign key (`cod_ciudes`) references `satt_faro`.`tab_genera_ciudad` (`cod_ciudad`) on delete cascade on update cascade
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Datos de la solicitud que realiza un usuario de una empresa, dependiendo de cod_tipsol, se llenan algunos campos que estan como nulos, ya que dependiendo del tipo de solicitud se diligencian unos campos y otros no.';
DROP TABLE IF EXISTS `satt_faro`.`tab_solici_seguim`;
CREATE TABLE `satt_faro`.`tab_solici_seguim` (
	`num_seguim` INT(11) NOT NULL AUTO_INCREMENT,
	`num_solici` INT(11) NOT NULL,
	`cod_estado` INT(1) NOT NULL,
	`obs_seguim` TEXT NOT NULL,
	`dir_archiv` VARCHAR(30) NULL,
	`usr_creaci` VARCHAR(15) NOT NULL,
	`fec_creaci` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`num_seguim`),
	CONSTRAINT `tab_solici_seguim_nsfk_1` FOREIGN KEY (`num_solici`) REFERENCES `satt_faro`.`tab_solici_solici` (`num_solici`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `tab_solici_seguim_cefk_1` FOREIGN KEY (`cod_estado`) REFERENCES `satt_faro`.`tab_solici_estado` (`cod_estado`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `tab_solici_seguim_ucfk_1` FOREIGN KEY (`usr_creaci`) REFERENCES `satt_faro`.`tab_genera_usuari` (`cod_usuari`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Datos de seguimiento de cada soicitud, son como historiales de una tarea, es necesario registrar el cod_estado por historial para saber que usuario hizo cambios';
show tables;
/************* trigger **************/
delimiter $$
CREATE TRIGGER setSerguimEstado AFTER INSERT ON `satt_faro`.`tab_solici_seguim` 
	FOR EACH ROW 
	BEGIN
		UPDATE `satt_faro`.`tab_solici_solici` SET `cod_estado`=NEW.cod_estado WHERE `num_solici`=NEW.num_solici;
	END$$
delimiter ;
SHOW TRIGGERS;
/************* trigger **************/
/************* NO existe en el servidor **************/
/************* gl **************/

/************* satb_ **************/
use mysql;
drop database if exists `satb_trmula`;
drop database if exists `satb_sta155`;
create database if not exists `satb_sta155`;
use `satb_sta155`;
CREATE TABLE `satb_sta155`.`tab_genera_servic` (
	`cod_servic` int(4) NOT NULL DEFAULT '0',
	`nom_servic` varchar(50) COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
	`des_servic` text COLLATE latin1_spanish_ci,
	`rut_archiv` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
	`rut_jscrip` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
	`bod_jscrip` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
	`cod_aplica` int(4) NOT NULL DEFAULT '0',
	`ind_ordenx` int(11) NOT NULL DEFAULT '0' COMMENT 'Indicador de Orden para el menu',
	`usr_creaci` varchar(15) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'Administrador',
	`fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`usr_modifi` varchar(15) COLLATE latin1_spanish_ci DEFAULT NULL,
	`fec_modifi` datetime DEFAULT NULL,
	PRIMARY KEY (`cod_servic`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Servicios de las diferentes aplicaciones';
CREATE TABLE `satb_sta155`.`tab_servic_servic` (
	`cod_serpad` int(4) NOT NULL DEFAULT '0',
	`cod_serhij` int(4) NOT NULL DEFAULT '0',
	PRIMARY KEY (`cod_serhij`),
	KEY `cod_serpad` (`cod_serpad`),
	CONSTRAINT `tab_servic_servic_ibfk_1` FOREIGN KEY (`cod_serpad`) REFERENCES `tab_genera_servic` (`cod_servic`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `tab_servic_servic_ibfk_2` FOREIGN KEY (`cod_serhij`) REFERENCES `tab_genera_servic` (`cod_servic`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Relaciona servicios para los menus en cascada';

create database if not exists `satb_trmula`;
use `satb_trmula`;
CREATE TABLE `satb_trmula`.`tab_genera_usuari` (
  `cod_consec` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Codigo usuario, consecutivo tabla',
  `cod_usuari` varchar(15) COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
  `num_cedula` int(12) DEFAULT NULL COMMENT 'numero cedula usuario',
  `clv_usuari` varchar(150) COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
  `nom_usuari` varchar(50) COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
  `usr_emailx` varchar(100) COLLATE latin1_spanish_ci DEFAULT NULL,
  `ali_usuari` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'alias del usuario',
  `ind_cambio` char(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT '0' COMMENT 'Indicador de Cambio',
  `num_diasxx` int(3) DEFAULT NULL COMMENT 'Numero de Dias para la caducidad',
  `fec_cambio` datetime DEFAULT NULL COMMENT 'Fecha de cambio',
  `clv_anteri` varchar(30) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Contraseña anterior',
  `cod_perfil` int(4) DEFAULT NULL,
  `cod_grupox` int(11) DEFAULT NULL,
  `cod_priori` int(11) NOT NULL DEFAULT '0',
  `cod_contro` int(4) DEFAULT NULL,
  `cod_inicio` int(4) DEFAULT NULL,
  `ind_estado` char(1) COLLATE latin1_spanish_ci DEFAULT '1' COMMENT 'indicativo de estado 1 = activo 0= inactivo',
  `usr_interf` varchar(15) COLLATE latin1_spanish_ci DEFAULT NULL COMMENT 'Usuario Interfaz',
  `usr_creaci` varchar(10) COLLATE latin1_spanish_ci NOT NULL,
  `fec_creaci` datetime NOT NULL,
  PRIMARY KEY (`cod_usuari`),
  KEY `cod_perfil` (`cod_perfil`),
  KEY `cod_consec` (`cod_consec`),
  KEY `cod_grupox` (`cod_grupox`)
) ENGINE=InnoDB AUTO_INCREMENT=2443 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Usuarios de las aplicaciones';
CREATE TABLE `satb_trmula`.`tab_genera_ciudad` (
  `cod_paisxx` int(3) NOT NULL DEFAULT '0',
  `cod_depart` int(3) NOT NULL DEFAULT '0',
  `cod_ciudad` int(11) NOT NULL DEFAULT '0',
  `nom_ciudad` varchar(50) COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
  `abr_ciudad` varchar(20) COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
  `ind_estado` char(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT '1',
  `val_icaxxx` float NOT NULL DEFAULT '0',
  `usr_creaci` varchar(15) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'Administrador',
  `fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `usr_modifi` varchar(15) COLLATE latin1_spanish_ci DEFAULT NULL,
  `fec_modifi` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_paisxx`,`cod_depart`,`cod_ciudad`),
  UNIQUE KEY `cod_ciudad` (`cod_ciudad`),
  KEY `abr_ciudad` (`abr_ciudad`),
  KEY `nom_ciudad` (`nom_ciudad`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
CREATE TABLE `satb_trmula`.`tab_tercer_tercer` (
  `cod_tercer` varchar(10) COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
  `num_verifi` char(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT '0',
  `cod_tipdoc` char(2) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'C',
  `cod_terreg` int(3) DEFAULT NULL,
  `nom_apell1` varchar(30) COLLATE latin1_spanish_ci DEFAULT NULL,
  `nom_apell2` varchar(30) COLLATE latin1_spanish_ci DEFAULT NULL,
  `nom_tercer` varchar(100) COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
  `abr_tercer` varchar(50) COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
  `dir_domici` varchar(100) COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
  `num_telef1` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `num_telef2` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `num_telmov` varchar(30) COLLATE latin1_spanish_ci DEFAULT NULL,
  `num_faxxxx` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `cod_paisxx` int(3) NOT NULL DEFAULT '3',
  `cod_depart` int(3) NOT NULL DEFAULT '1',
  `cod_ciudad` int(11) NOT NULL DEFAULT '1',
  `dir_emailx` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `dir_urlweb` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `cod_estado` char(1) COLLATE latin1_spanish_ci NOT NULL DEFAULT '1',
  `dir_ultfot` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `obs_tercer` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `obs_aproba` text COLLATE latin1_spanish_ci,
  `usr_creaci` varchar(30) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'Administrador',
  `fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `usr_modifi` varchar(30) COLLATE latin1_spanish_ci DEFAULT NULL,
  `fec_modifi` datetime DEFAULT NULL,
  PRIMARY KEY (`cod_tercer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
CREATE TABLE `satb_trmula`.`tab_genera_perfil` (
	`cod_perfil` int(4) NOT NULL DEFAULT '0',
	`nom_perfil` varchar(50) COLLATE latin1_spanish_ci NOT NULL DEFAULT '',
	`usr_creaci` varchar(15) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'Administrador',
	`fec_creaci` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	`usr_modifi` varchar(15) COLLATE latin1_spanish_ci DEFAULT NULL,
	`fec_modifi` datetime DEFAULT NULL,
	PRIMARY KEY (`cod_perfil`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='guarda los perfiles de usuario';
CREATE TABLE `satb_trmula`.`tab_perfil_servic` (
	`cod_perfil` int(4) NOT NULL DEFAULT '0',
	`cod_servic` int(4) NOT NULL DEFAULT '0',
	PRIMARY KEY (`cod_perfil`,`cod_servic`),
	KEY `cod_servic` (`cod_servic`),
	CONSTRAINT `tab_perfil_servic_ibfk_1` FOREIGN KEY (`cod_servic`) REFERENCES `satb_sta155`.`tab_genera_servic` (`cod_servic`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `tab_perfil_servic_ibfk_2` FOREIGN KEY (`cod_perfil`) REFERENCES `tab_genera_perfil` (`cod_perfil`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Permisos de los perfiles sobre los servicios';
/************* satb_ **************/

EOF;