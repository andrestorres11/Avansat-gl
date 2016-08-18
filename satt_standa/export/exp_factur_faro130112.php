<?php
//error_reporting("E_ERROR");

include ("../lib/general/constantes.inc");
include ("../lib/general/conexion_lib.inc");
include ("../lib/general/form_lib.inc");
include ("../lib/general/paginador_lib.inc");
include ("../lib/general/pdf_lib.inc");
include("../../".$GLOBALS['url']."/constantes.inc");

class Proc_Factur_Faro
{
 var $conexion;
 	 

 function Proc_Factur_Faro()
 {
		ini_set('memory_limit','128M');
    session_start();
    $fechoract = date("Y-m-d h:i a");
    $archivo = "Facturacion_Faro_".$fechoract;
    $factur = $_SESSION[factur];

		header('Content-Type: application/octetstream');
		header('Expires: 0');
		header('Content-Disposition: attachment; filename="'.$archivo.'.xls"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
    
		$formulario = new Formulario ("index.php","post",$archivo,"form_item", "","");
    $formulario -> nueva_tabla();
		$formulario -> linea("Numero Total de Despachos entre ".$GLOBALS["fecini"]." hasta el ".$GLOBALS["fecfin"]."(".sizeof($factur).")",1,"t2");
		$formulario -> nueva_tabla();
		$formulario -> linea("Despacho",0,"t2");
		$formulario -> linea("Manifiesto",0,"t2");
		$formulario -> linea("Ciudad Origen",0,"t2");
		$formulario -> linea("Ciudad Destino",0,"t2");
		$formulario -> linea("Placa",0,"t2");
		$formulario -> linea("Conductor",0,"t2");
		$formulario -> linea("Cedula",0,"t2");
		$formulario -> linea("Celular",0,"t2");
		$formulario -> linea("Seguimiento Faro",0,"t2");
		$formulario -> linea("Fecha de Salida",0,"t2");
		$formulario -> linea("Fecha de Llegada",0,"t2");
		$formulario -> linea("Empresa Transportadora",0,"t2");
		$formulario -> linea("Generador",0,"t2");
		$formulario -> linea("Novedades",1,"t2");
		for($i = 0; $i < sizeof($factur); $i++)
		{
			$formulario -> linea($factur[$i][0],0,"t1");
			$formulario -> linea($factur[$i][4],0,"t1");
			$formulario -> linea($factur[$i][2],0,"t1");
			$formulario -> linea($factur[$i][3],0,"t1");
			$formulario -> linea($factur[$i][7],0,"t1");
			$formulario -> linea($factur[$i][8],0,"t1");
			$formulario -> linea($factur[$i][9],0,"t1");
			$formulario -> linea($factur[$i][12],0,"t1");
			$formulario -> linea($factur[$i][10],0,"t1");
			$formulario -> linea($factur[$i][1],0,"t1");
			$formulario -> linea($factur[$i][6],0,"t1");
			$formulario -> linea($factur[$i][11],0,"t1");
			$formulario -> linea($factur[$i][5],0,"t1");
			$formulario -> linea($factur[$i][16],1,"t1");
		
		}
		
		

		$formulario -> cerrar();
 }

 }
$proceso = new Proc_Factur_Faro();
?>