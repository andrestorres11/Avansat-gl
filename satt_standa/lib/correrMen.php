<?php

include ("./EnvioMensajes.inc");

if($GLOBALS[envmenext])
{
 $infomen = explode("|",$GLOBALS[envmenext]);

 $datos_men["tipopc"] = $infomen[1];
 $datos_men["manifi"] = $infomen[2];
 $datos_men["placa"] = $infomen[3];
 $datos_men["contro"] = $infomen[4];
 $datos_men["dirdis"] = $infomen[5];
 $datos_men["dnsope"] = $infomen[6];
 $datos_men["observ"] = $infomen[7];
 $datos_men["fecnov"] = $infomen[8];
 $datos_men["tipvoc"] = $infomen[9];
 $datos_men["demora"] = $infomen[10];
 $datos_men["noveda"] = $infomen[11];
 $datos_men["nomtra"] = $infomen[12];

 $obj = new EnvMensaj();

 if($infomen[0] == 1)
  $obj -> ConstrucEmail($datos_men);
 else
  $obj -> ConstrucSMS($datos_men);
}

?>
