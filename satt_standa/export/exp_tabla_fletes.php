<?php
//error_reporting("E_ERROR");

include ("../lib/general/constantes.inc");
include ("../lib/general/conexion_lib.inc");
include ("../lib/general/form_lib.inc");
include ("../lib/general/paginador_lib.inc");
include ("../lib/general/pdf_lib.inc");
include ("../despac/Despachos.inc");
include("../../".$GLOBALS['url']."/constantes.inc");

class Proc_exp_fletes
{
 var $conexion;

 function Proc_exp_fletes()
 {
  $this -> conexion = new Conexion("bd10.intrared.net:3306", USUARIO, CLAVE, $GLOBALS["db"]);
  $this -> Listar();
 }

 function Listar()
 {
   $query = base64_decode($GLOBALS[query_exp]);
   $consulta = new Consulta($query, $this -> conexion);
   $matriz = $consulta -> ret_matriz();

   $fechoract = date("Y-m-d h:i a");
   $archivo = "Tabla_Fletes_".$fechoract;
   $this -> expListadoExcel($archivo,$matriz,$fechoract);
 }

 function expListadoExcel($archivo,$matriz,$fechoract)
 {
  $archivo .= ".xls";
  header('Content-Type: application/octetstream');
  header('Expires: 0');
  header('Content-Disposition: attachment; filename="'.$archivo.'"');
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  header('Pragma: public');

  $formulario = new Formulario ("index.php","post","Tabla de Fletes","form_fletes");
  $formulario -> linea("",1,"t");
  $formulario -> linea ("Fecha Reporte :: ",1,"t2");
  $formulario -> linea ($fechoract,1,"t2");

  $formulario -> nueva_tabla();

  $formulario -> linea("",0,"t");
  $formulario -> linea("",0,"t");
  $formulario -> linea("",0,"t");
  $formulario -> linea("FLETES",1,"t",0,0,"center");
  $formulario -> linea("",1,"t");

  $formulario -> linea ("Codigo",0,"t");
  $formulario -> linea ("Ciudad Origen",0,"t");
  $formulario -> linea ("Ciudad Destino",0,"t");
  $formulario -> linea ("Carroceria",0,"t");
  $formulario -> linea ("Trayectoria",0,"t");
  $formulario -> linea ("Transportadora",0,"t");
  $formulario -> linea ("Por Tonelada",0,"t");
  $formulario -> linea ("Valor (Unit)",0,"t");
  $formulario -> linea ("Estado",1,"t");

  for($i = 0; $i < sizeof($matriz); $i++)
  {
   $query = "SELECT nom_carroc
             FROM ".BASE_DATOS.".tab_vehige_carroc
             WHERE cod_carroc = ".$matriz[$i][3]."
            ";

    $consulta = new Consulta($query, $this -> conexion);
    $carroc = $consulta -> ret_matriz();

    $query = "SELECT nom_trayec
             FROM ".BASE_DATOS.".tab_genera_trayec
             WHERE cod_trayec = ".$matriz[$i][4]."
            ";

    $consulta = new Consulta($query, $this -> conexion);
    $trayec = $consulta -> ret_matriz();

   	$query = "SELECT abr_tercer
             FROM ".BASE_DATOS.".tab_tercer_tercer
             WHERE cod_tercer = ".$matriz[$i][5]."
            ";

    $consulta = new Consulta($query, $this -> conexion);
    $transp = $consulta -> ret_matriz();

    $query = "SELECT nom_ciudad
             FROM ".BASE_DATOS.".tab_genera_ciudad
             WHERE cod_ciudad = ".$matriz[$i][1]."
            ";

    $consulta = new Consulta($query, $this -> conexion);
    $ciudad_o = $consulta -> ret_matriz();

    $query = "SELECT nom_ciudad
             FROM ".BASE_DATOS.".tab_genera_ciudad
             WHERE cod_ciudad = ".$matriz[$i][2]."
            ";

    $consulta = new Consulta($query, $this -> conexion);
    $ciudad_d = $consulta -> ret_matriz();

   	$formulario -> linea($matriz[$i][0],0,"i");
   	$formulario -> linea($ciudad_o[0][0],0,"i");
   	$formulario -> linea($ciudad_d[0][0],0,"i");
   	$formulario -> linea($carroc[0][0],0,"i");
   	$formulario -> linea($trayec[0][0],0,"i");
   	$formulario -> linea($transp[0][0],0,"i");

   	if($matriz[$i][6] == COD_ESTADO_ACTIVO)
   		$formulario -> linea("Si",0,"i");
   	else
   		$formulario -> linea("No",0,"i");

   	$formulario -> linea($matriz[$i][8],0,"i");

   	if($matriz[$i][7] == COD_ESTADO_ACTIVO)
   		$formulario -> linea("Activo",1,"i");
   	else
   		$formulario -> linea("Inactivo",1,"i");
  }
  $formulario -> cerrar();
 }
}
class PDF extends FPDF
{
 function Header()
 {
  $this->SetFont('Arial','B',8);
 }

 function Footer()
 {
  $this -> SetY(-15);
  $this -> SetFont('Arial','I',8);
  $this -> Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
 }
}
$proceso = new Proc_exp_fletes();
?>