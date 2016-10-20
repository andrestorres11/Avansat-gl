<?php

if(isset($_REQUEST["fpass"]))
{
	DEFINE("host","aglbd.intrared.net");
	DEFINE("usu","jpreciado");
	DEFINE("clv","Jorge_2015");
	DEFINE("bd","satt_standa");

	if($_REQUEST["fpass"] == "8256e0201424b7755f79e6af5e6de58e")
	{
		if(isset($_REQUEST["opcion"]))
		{
			busqueda();
		}
		else
		{
			formulario();	
		}
	}
	else
	{
		echo "Error_Contrase&ntildea";
		die();
	}
}
else
{
	pass();
}

function pass()
{
	echo $html = "<html>
									<body>
										<form action='busca_servicio.php' method='POST'>
											Contrase&ntildea: <input type='text' name='fpass'><br>
											<input type='submit' value='Buscar'>
										</form>
									</body>
								</html>";		
}

function formulario()
{
	echo $html2 = "<html>
									<body>
										<form action='busca_servicio.php' method='POST'>
											Servicio: <input type='text' name='fname'><br>
											<input type='hidden' name='opcion' value='1'>
											<input type='hidden' name='fpass' value='8256e0201424b7755f79e6af5e6de58e'>
											<input type='submit' value='Buscar'>
										</form>
									</body>
								</html>";
}

function busqueda()
{
	mysql_connect(host,usu,clv) or die("Fallo de Conexion");

	$query = "SELECT a.cod_servic, a.nom_servic, a.rut_archiv, a.rut_jscrip
							FROM ".bd.".tab_genera_servic a
							WHERE cod_servic = '".$_POST['fname']."'";

	$resultado= mysql_query($query);

	if(mysql_num_rows($resultado)>0)
	{
		while($fila = mysql_fetch_assoc($resultado))
		{
			echo "Servicio: 	".$fila['cod_servic']."<br>";
			echo "Nombre del servicio 	: ".$fila['nom_servic']."<br>";
			echo "Ruta archivo: 	".$fila['rut_archiv']."<br>";
			echo "Ruta Javascript: 	".$fila['rut_jscrip']."<br>";
		}
	}	
}

?>