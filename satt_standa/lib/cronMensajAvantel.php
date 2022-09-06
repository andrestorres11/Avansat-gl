<?php
include ("general/constantes.inc");
include ("general/tabla_lib.inc");
include ("general/conexion_lib.inc");
include ("EnvioMensajes.inc");

$obj = new EnvMensaj();
$obj -> EnvMensajCron();
$obj -> EjecProcesCron();
?>
