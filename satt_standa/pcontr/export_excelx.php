<?php
session_start();
ini_set('memory_limit', '128M');
$archivo = "Informe_estudio_seguridad_".date("Y_m_d").".xls";
header('Content-Type: application/octetstream');
header('Expires: 0');
header('Content-Disposition: attachment; filename="'.$archivo.'"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

ob_start("ob_gzhandler");
echo $HTML = $_SESSION["HTML"];
ob_end_flush();
?>