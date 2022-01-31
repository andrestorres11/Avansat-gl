<?php

mysql_connect("aglbd.intrared.net","satt_faro","sattfaro")or die("Error al conectar con el servidor ".mysql_error());
mysql_select_db("satt_faro") or die("No se pudo usar satt_faro");

$query = "SELECT * FROM tab_despac_images where num_despac = '1718704' ";

$result = mysql_query($query);

//header('Content-type:image/png');

while($fila = mysql_fetch_row($result)){
	echo "<img src='".$fila[4]."' />";
}

?>