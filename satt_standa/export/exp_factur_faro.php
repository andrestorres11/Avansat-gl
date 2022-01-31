<?php

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

include ("../lib/general/constantes.inc");
include ("../lib/general/conexion_lib.inc");
include ("../lib/general/form_lib.inc");
include ("../lib/general/paginador_lib.inc");
include("../../".$_REQUEST['url']."/constantes.inc");

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
    #ob_clean() ;

		header('Content-Type: application/octetstream');
		header('Expires: 0');
		header('Content-Disposition: attachment; filename="'.$archivo.'.xls"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		ob_start();
		ob_flush();
		ob_end_clean();
		ob_flush();
		#ob_end_clean();
		$formulario = new Formulario ("index.php","post",$archivo,"form_item", "","");
    $formulario -> nueva_tabla();
		$formulario -> linea("Numero Total de Despachos entre ".$_REQUEST["fecini"]." hasta el ".$_REQUEST["fecfin"]."(".sizeof($factur).")",1,"t2");
		$formulario -> nueva_tabla();
    $formulario -> linea("#",0,"t2");
		$formulario -> linea("Despacho",0,"t2");
		$formulario -> linea("Agencia",0,"t2");
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
		$formulario -> linea("Novedades",0,"t2");
		$formulario -> linea("No. EAL Registradas",0,"t2");
		$formulario -> linea("No. EAL Cumplidas",1,"t2");
		for($i = 0; $i < sizeof($factur); $i++)
		{
		  
      
      if(!strcmp($factur[$i][5], $comp)==0){
        
        $formulario -> nueva_tabla();
      	$formulario -> linea("Empresa",0,"t2");
	      $formulario -> linea("Total Despachos",0,"t2");
	      $formulario -> linea("Total Novedades",1,"t2");
        
        $formulario -> linea($factur[$i-1][5],0,"t1");
		    $formulario -> linea($j,0,"t1");
		    $formulario -> linea($totNovedad,1,"t1");
        
        $formulario -> nueva_tabla();
        
        $j = ($i < sizeof($factur) ? 0 : $j);
        $totNovedad = ($i < sizeof($factur) ? 0 : $totNovedad );
        $comp = $factur[$i][5];
      }
  
      $formulario -> linea(($j+1),0,"t1");
			$formulario -> linea($factur[$i][0],0,"t1");
			$formulario -> linea($factur[$j]['agenci'],0,"t1");
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
			$formulario -> linea($factur[$i][16],0,"t1");
			$formulario -> linea($factur[$i][17],0,"t1");
			$formulario -> linea($factur[$i][18],1,"t1");
      
        $totNovedad += $factur[$i][16];
		
        $j++;
      
		}
		$formulario -> nueva_tabla();
		$formulario -> linea("Empresa",0,"t2");
		$formulario -> linea("Total Despachos",0,"t2");
		$formulario -> linea("Total Novedades",1,"t2");
		$formulario -> linea($factur[$i-1][5],0,"t1");
		$formulario -> linea($j,0,"t1");
		$formulario -> linea($totNovedad,1,"t1");
    
		if($_SESSION['tarifa']!=''){
			if($_SESSION['tarifa'][4]=='D'){
				$formulario -> linea("Fechas De Tarifas: ".$_SESSION['tarifa'][2]." al ".$_SESSION['tarifa'][3],1,"t1");
				$formulario -> linea("Tipo de Tarifa: Despachos",1,"t1");
				$formulario -> linea("Valor Minimo : $".number_format($_SESSION['tarifa'][1],0),1,"t1");
				$formulario -> linea("Valor Por Despacho : $".number_format($_SESSION['val'][0],0),1,"t1");
				$formulario -> linea("Valor a Cobrar : $".number_format($_SESSION['total'], 0),1,"t1");
			}
			if($_SESSION['tarifa'][4]=='N'){
				$formulario -> linea("Fechas De Tarifas: ".$_SESSION['tarifa'][2]." al ".$_SESSION['tarifa'][3],1,"t1");
				$formulario -> linea("Tipo de Tarifa: Despachos",1,"t1");
				$formulario -> linea("Valor Minimo : $".number_format($_SESSION['tarifa'][1],0),1,"t1");
				$formulario -> linea("Configuracion de Rangos de Novedades: ".$_SESSION['val'][0]." hasta ".$_SESSION['val'][1],1,"t1");
				$formulario -> linea("Valor Por Novedad : $".number_format($_SESSION['val'][2],0),1,"t1");
				$formulario -> linea("Valor a Cobrar : $".number_format($_SESSION['total'], 0),1,"t1");
			}
		}
		$formulario -> cerrar();

		ob_end_flush();
  }


}
$proceso = new Proc_Factur_Faro();
?>