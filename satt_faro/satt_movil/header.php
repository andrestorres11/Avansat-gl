<?php
session_start();//Abrir Session!.
include ("lib/fn.php");
include ("lib/conexion.inc");
include ("lib/form.php");

if( $_GET[foto] )
{
	header("Content-type:  image/jpeg");
	
	$conexion = new Conexion( "localhost:444", "satb_movil", "satb_movil1", "satt_faro", "" );
	
	$query = "SELECT bin_fotoxx
			  FROM tab_despac_images 
			  WHERE num_despac = '$_GET[num_despac]' AND
					cod_contro = '$_GET[cod_contro]' 
			  ORDER BY fec_creaci DESC";
		
	$fotos = $conexion -> Consultar( $query, "a" );
		
	echo $fotos[bin_fotoxx];
	die();
}


?>
<!DOCTYPE html>
<html>
<head>
<title>Esferas - Reporte de Novedades</title>
<style>
body
{
    padding:0px;
    margin:0px;
    font-family:Verdana;
    font-size:14px;
    background-color:#fff;
	width:auto;
}

#head, #footer
{
    background-color:#090;
	background: -webkit-gradient(linear, left top, left bottom, from( #37799d ), to( #0a1e33 )); 
	background: -moz-linear-gradient(top, #37799d, #0a1e33 ); 
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#37799d', endColorstr='#0a1e33');
    color:#fff;
    text-align:center;
    padding:10px;
    font-weight:bold;
    border-bottom:1px #ccc solid;
}

.campo
{
    border:1px solid #ddd;
	-webkit-border-radius: 50px;
	-moz-border-radius: 50px;
	border-radius: 50px;
	padding:3px 10px;
	/*background-color:#CEFFCE;*/
}

.error
{
	text-align:center;
	color:#900;	
	padding:5px;
}

a:link {color:#fff; text-decoration:none}      /* unvisited link */
a:visited {color:#fff; text-decoration:none}  /* visited link */
a:hover {color:#900; text-decoration:none}  /* mouse over link */
a:active {color:#fff; text-decoration:none}  /* selected link */
</style>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div id="head" >ESFERAS DE ASISTENCIA LOG&Iacute;STICA</div>