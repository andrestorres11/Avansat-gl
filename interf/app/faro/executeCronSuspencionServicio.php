<?php

/* ! \file: executeCronSuspencionServicio
 *  \brief: cron para el envio de correos de suspencion de servicio
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 20/04/2016
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */
$mHost = explode('.', $_SERVER['HTTP_HOST']);

switch ($mHost[0]) {
    case 'web7': $mBD = 'bd7.intrared.net:3306';
        break;
    case 'web13': $mBD = 'bd13.intrared.net:3306';
        break;
    case 'web10': $mBD = 'aglbd.intrared.net';
        break;
    default: $mBD = 'demo.intrared.net';
        break; #Esta URL No existe!
}

$sql = "SELECT a.dir_emailx, c.cod_tercer, c.abr_tercer, b.obs_bitaco, d.des_tipser, b.fec_operac  FROM satt_faro.tab_bitaco_susema a 
					INNER JOIN satt_faro.tab_bitaco_suspen b ON b.cod_bitsus = a.cod_bitsus 
					INNER JOIN satt_faro.tab_tercer_tercer c ON c.cod_tercer = b.cod_transp 
					INNER JOIN satt_faro.tab_genera_tipser d ON d.cod_tipser = b.cod_tipser
					WHERE NOW() BETWEEN fec_inicio AND fec_finali AND b.tip_bitaco = 0 ";


$conn = mysqli_connect($mBD, 'satt_faro', 'sattfaro', 'satt_faro');

if (mysql_errno()) {
    die("Connection failed: " . mysql_error());
} else
    

$result = mysqli_query($conn, $sql);

$mAsunto = "Suspención del servicio.";
$cc = "faroavansat@eltransporte.com";
$mCabece = 'MIME-Version: 1.0' . "\r\n";
$mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
$mCabece .= 'From: Centro_Logistico_FARO ' . "\r\n";
$tmpl_file = '/var/www/html/ap/interf/app/faro/planti/plantilla_suspencion.html';

while ($fila = mysqli_fetch_array($result)) {

    $cod_tercer = $fila['cod_tercer'];
    $dir_emailx = $fila['dir_emailx'];
    $abr_tercer = $fila['abr_tercer'];
    $obs_bitaco = $fila['obs_bitaco'];
    $des_tipser = $fila['des_tipser'];
    $fec_operac = $fila['fec_operac'];

    $mHtmlxx = "<!doctype html>
<center id='content' style='margin-top: 1%;'>
    <table width='100%' border='0' align='center' cellpadding='0' cellspacing='0' style='background-color:#f0f0f0;'>
        <tr>
            <td style='width: 25%;'><img src='https://avansatgl.intrared.net/ap/satt_faro/imagenes/logo.gif'></td>
            <td colspan='2' style='width: 75%;background-color: #F0F0F0 !important;color: #000000 !important;'>
                <br>
                <h1 style='color:#0B4D0F'>SUSPENCIÓN DE SERVICO</h1>
                <br>
                <br>
            </td>
        </tr>
        <tr>
        	<td colspan='2' style='text-align: center; background-color: #35650F; color: #FFFFFF' > <b>DATOS DEL CLIENTE</b></td>
        </tr>
        <tr>
            <td colspan='2' style='font-family:Trebuchet MS, Verdana, Arial;font-size:13px;color:#OOOOOO;padding: 4px;'>
                NIT: $cod_tercer
            </td>
        </tr>
        <tr>	
            <td colspan='2' style='font-family:Trebuchet MS, Verdana, Arial;font-size:13px;color:#OOOOOO;padding: 4px;'>
                Empresa: $abr_tercer
            </td>
        </tr>
        <tr>
            <td colspan='2' style='font-family:Trebuchet MS, Verdana, Arial;font-size:13px;color:#OOOOOO;padding: 4px;'>
                Servicio a suspender: $des_tipser
            </td>
        </tr>
        <tr>
            <td colspan='2' style='font-family:Trebuchet MS, Verdana, Arial;font-size:13px;color:#OOOOOO;padding: 4px;'>
                Apartir de: $fec_operac
            </td>
        </tr>
        <tr>
            <td colspan='2' style='font-family:Trebuchet MS, Verdana, Arial;font-size:13px;color:#OOOOOO;padding: 4px;'>
                Observaci&oacute;n: $obs_bitaco
            </td>
        </tr>
        <tr>
            <td colspan='2' style='font-family:Trebuchet MS, Verdana, Arial;font-size:13px;color:#OOOOOO;padding: 4px;'>
                Notificado a: $dir_emailx
            </td>
        </tr> 
        <tr>
            <td colspan='2' style='font-family:Trebuchet MS, Verdana, Arial;font-size:13px;color:#OOOOOO;padding: 4px;'>
                <font style='color:red'><b>NOTA:</font> Si Ya realizó el pago, por favor hacer caso omiso a esta notificación.</b>
            </td>
        </tr>        
    </table>    
</center>";

    mail($dir_emailx, $mAsunto,  $mHtmlxx , $mCabece . ' Cc: ' . $cc . "\r\n");
    
}
echo $mHtmlxx;

?>