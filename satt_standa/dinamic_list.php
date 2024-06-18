<?php

//---------------------------------------
if( !class_exists( 'Tabla_mensaje' ) )
  include( 'lib/general/tabla_lib.inc' );
//---------------------------------------

include( "lib/general/conexion_lib.inc" );
include( "lib/general/dinamic_list.inc" );

session_start();


define( BASE_DATOS , $_SESSION["BASE_DATOS"] );
define( DIR_APLICA_CENTRAL , $_SESSION["DIR_APLICA_CENTRAL"] );
define( ESTILO , $_SESSION["ESTILO"] );
define( HOST , $_SESSION["HOST"] );
define( USUARIO , $_SESSION["USUARIO"] );
define( CLAVE , $_SESSION["CLAVE"] );

if( isset($_SESSION["conexionADS"]) && $_SESSION["conexionADS"] == TRUE )
{

  define( HOST_CENTRAL_POLIZA, $_SESSION["HOST_CENTRAL_POLIZA"] );
  define( USR_CENTRAL_POLIZA, $_SESSION["USR_CENTRAL_POLIZA"] );
  define( PWD_CENTRAL_POLIZA, $_SESSION["PWD_CENTRAL_POLIZA"] );
  define( DB_PANEL_CONTROL, $_SESSION["DB_PANEL_CONTROL"] );
  define( DB_ADS_CASS, $_SESSION["DB_ADS_CASS"] );    
  
  $conexion = new Conexion( HOST_CENTRAL_POLIZA, USR_CENTRAL_POLIZA, PWD_CENTRAL_POLIZA, DB_PANEL_CONTROL );
}
else
{
  $conexion = new Conexion( HOST , USUARIO, CLAVE, BASE_DATOS );
}

if ( isset( $_GET["Action"] ) ) $_AJAX = $_GET;
if ( isset( $_POST["Action"] ) ) $_AJAX = $_POST;

if ( !isset( $_AJAX["Sort_Col"] ) ) $_AJAX["Sort_Col"] = "";
if ( !isset( $_AJAX["Way"] ) ) $_AJAX["Way"] = "";
if ( !isset( $_AJAX["Filters"] ) ) $_AJAX["Filters"] = "";
if ( !isset( $_AJAX["Limit"] ) ) $_AJAX["Limit"] = "";
if ( !isset( $_AJAX["Page"] ) ) $_AJAX["Page"] = "";
if ( !isset( $_AJAX["Selected"] ) ) $_AJAX["Selected"] = "";
if ( !isset( $_AJAX["Search"] ) ) $_AJAX["Search"] = "";

$list = $_SESSION["DINAMIC_LIST"];
$list -> Ajax( $conexion, $_AJAX["Action"], $_AJAX["Sort_Col"], $_AJAX["Way"], $_AJAX["Filters"], $_AJAX["Limit"],  $_AJAX["Page"], $_AJAX["Selected"], $_AJAX["Search"] );

echo $list -> GetHtml();

die();

?>