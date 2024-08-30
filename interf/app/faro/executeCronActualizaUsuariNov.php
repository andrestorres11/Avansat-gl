<?php


$mSql1 = " UPDATE satt_faro.tab_despac_noveda a 
	   INNER JOIN satt_faro.tab_genera_usuari b 
			   ON a.usr_creaci = b.cod_usuari
			  SET a.cod_usrcre = b.cod_consec
			WHERE a.cod_usrcre = '0' ";


$mSql2 = " UPDATE satt_faro.tab_despac_contro a 
	   INNER JOIN satt_faro.tab_genera_usuari b 
			   ON a.usr_creaci = b.cod_usuari
			  SET a.cod_usrcre = b.cod_consec
			WHERE a.cod_usrcre = '0' ";


// Create connection
$conn = mysql_connect('aglbd.intrared.net', 'satt_faro', 'sattfaro');

// Check connection
if (mysql_errno() ) {
    die("Connection failed: " . mysql_error() );
}else
	echo "Connected successfully";

mysql_select_db ( 'satt_faro', $conn );

$result = mysql_query($mSql1, $conn);
echo "<pre> result: ";	print_r($result);	echo "</pre>";

$result = mysql_query($mSql2, $conn);
echo "<pre> result: ";	print_r($result);	echo "</pre>";

mysql_close($conn);

?>
