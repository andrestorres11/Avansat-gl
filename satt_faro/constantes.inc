<?php

define("DIR_APLICA_CENTRAL", "satt_standa");
@include("../" . DIR_APLICA_CENTRAL . "/lib/general/constantes.inc");

define("NOM_URL_APLICA","satt_faro");//constante para el nombre del directorio - aplicación
define("BASE_DATOS", "satt_faro");// constante para la base de datos de la aplicacion
define("BD_STANDA","satt_standa");// constante para la base de datos Estandar de SAT Básico.

define("USUARIO", "satt_faro");// constante para la base de datos de la aplicacion
define("CLAVE", "sattfaro");// constante para la base de datos de la aplicacion
define("MOVIL_USUARIO", "satb_movil");// constante para el usuario de movil
define("MOVIL_CLAVE", "satb_movil1");// constante para el clave de movil

define("DIREC_APLICA", "http://".URL_DIREC_APLICA."/satt_faro/");// constante para direccion de la pagina
define("URL_APLICA", "http://".URL_DIREC_APLICA."/satt_faro/");// constante para direccion de la pagina
define("URL_STANDA", "http://".URL_DIREC_APLICA."/satt_standa/");// constante para direccion de standa
define("PAGINA", "http://".URL_DIREC_APLICA."/satt_faro/");// constante para direccion de la pagina
define("URL_ARCHIV", URL_ARCHIV_STANDA."satt_faro/");// constante para foto del conductor

define("NIT_TRANSPOR", "999999999");// constante NIT DE LA EMPRESA
define("NOMSAD", "CENTRO LOGISTICO FARO");// CONSTANTE PARA NOMBRE DE LA APLICACION
define("NOM_TRANMENU", "SATT FARO");// Nombre Aplicacion WAP
define("COD_NOVEDAD", "9, 57,27, 13, 52,70 , 12");// Codigos de novedades

$mHost = explode('.', $_SERVER['HTTP_HOST']);

switch ($mHost[0]) {
	case 'web7': $mBD  = 'bd7.intrared.net:3306'; break;
	case 'web13': $mBD = 'bd13.intrared.net:3306'; break;
	case 'avansatgl': $mBD = 'aglbd.intrared.net'; break;
	default: $mBD = 'aglbd.intrared.net'; break; #Esta URL No existe!
}




define("HOST", $mBD);
define("HOST_WEB", $mHost[0]);   #Host Actual
define("HOST_WEB_DEV", "web7");  #Host Desarrollo
define("HOST_WEB_TES", "web13"); #Host Pruebas
define("HOST_WEB_PRO", "avansatgl"); #Host Produccion

?>
