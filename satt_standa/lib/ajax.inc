<?php

//include( "../lib/general/conexion_lib.inc" );

session_start();

//------------------------
if( !defined( 'HOST' ) )
	define( 'HOST', $_SESSION["HOST"] );
//------------------------
if( !defined( 'BASE_DATOS' ) )
	define( 'BASE_DATOS', $_SESSION["BASE_DATOS"] );
//------------------------
if( !defined( 'USUARIO' ) )
	define( 'USUARIO', $_SESSION["USUARIO"] );
//------------------------
if( !defined( 'CLAVE' ) )
	define( 'CLAVE', $_SESSION["CLAVE"] );
//------------------------
if( !defined( 'BD_STANDA' ) )
	define( 'BD_STANDA', 'satt_standa' );
	//define( 'BD_STANDA', $_SESSION["BD_STANDA"] );
//------------------------
if( !defined( 'DIR_APLICA_CENTRAL' ) )
	define( 'DIR_APLICA_CENTRAL', $_SESSION["DIR_APLICA_CENTRAL"] );
//------------------------
if( !defined( 'NOM_URL_APLICA' ) )
	define( 'NOM_URL_APLICA', $_SESSION["NOM_URL_APLICA"] );
//------------------------
if( !defined( 'NIT_TRANSPOR' ) )
	define( 'NIT_TRANSPOR', $_SESSION["NIT_TRANSPOR"] );
//------------------------

include_once( "../lib/general/conexion_lib.inc" );
include_once( "../lib/general/functions.inc" );
include_once( "../lib/general/selects.inc" );
include_once( "../lib/general/dinamic_list.inc" );
include_once( "../lib/general/form_lib.inc" );
include_once( "../lib/GeneralFunctions.inc" );
include_once( "../lib/general/tabla_lib.inc" );
include_once( "../lib/general/xml.inc" );
include_once( "../lib/general/xmlparser.inc" );
include_once( "../lib/bd/seguridad/aplica_filtro_perfil_lib.inc" );
include_once( "../lib/bd/seguridad/aplica_filtro_usuari_lib.inc" );
include_once("../lib/mensajes_lib.inc");
include_once("../lib/general/FormLib.class.php");

$AjaxConnection = new Conexion( HOST, USUARIO, CLAVE, BASE_DATOS );

if ( isset( $_GET ["Ajax"] ) ) $_AJAX = $_GET;
if ( isset( $_POST["Ajax"] ) ) $_AJAX = $_POST;

//header('Content-type: text/xml; charset=iso-8859-1');
?>
