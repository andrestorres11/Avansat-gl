<?php

if(isset($GLOBALS["fpass"]))
{
	DEFINE("host","bd10.intrared.net:3306");
	DEFINE("usu","jpreciado");
	DEFINE("clv","Jorge_2015");
	DEFINE("bd","satt_faro");

	if($GLOBALS["fpass"] == "8256e0201424b7755f79e6af5e6de58e")
	{
		if(isset($GLOBALS["opcion"]))
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
										<form action='busca_rutas.php' method='POST'>
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
										<form action='busca_rutas.php' method='POST'>
											Nit Cliente: <input type='text' name='transport'><br>
											cod_ciuori: <input type='text' name='origen'><br>
											cod_ciudes: <input type='text' name='destino'><br>
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
	$query = "SELECT a.*
							FROM ".bd.".tab_genera_rutasx a, 
							".bd.".tab_genera_ruttra b
							WHERE a.cod_rutasx = b.cod_rutasx
							AND a.cod_ciuori = '".$_POST['origen']."'
							AND a.cod_ciudes = '".$_POST['destino']."'
							AND b.cod_transp = '".$_POST['transport']."'
							AND a.ind_estado = 1 group by 1";
	$resultado= mysql_query($query) or die("no realizo la consulta");

	if(mysql_num_rows($resultado)>0)
	{
		while($fila = mysql_fetch_assoc($resultado))
		{
			echo "cod_rutasx: 	".$fila['cod_rutasx']."<br>";
			echo "nom_rutasx: ".$fila['nom_rutasx']."<br>";
			echo "<hr>";
		}
	}	
}

?>