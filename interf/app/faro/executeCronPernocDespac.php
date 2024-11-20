<?php
/*! \file: executeCronPernocDespac.php
 *  \brief: Cron que guarda la novedad Pernoctacion, que fueron gestionadas como novedades Pernoctacion futura
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 23/03/2016
 *  \bug: 
 *  \warning: Se usa la libreria de conexion de SAT GL para poder usar InsertNovedad.inc
 */

$mHost = explode('.', $_SERVER['HTTP_HOST']);
if( $mHost[0] == 'web7' ){
  $mFile = "dev/";
}else{
  $mFile = "";
}

@include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );       //Constantes generales.
@include_once( '/var/www/html/ap/interf/lib/Mail.class.php' );
@include_once( "/var/www/html/ap/{$mFile}satt_faro/constantes.inc" );
@include_once( "/var/www/html/ap/{$mFile}".DIR_APLICA_CENTRAL."/lib/general/conexion_lib.inc" );
@include_once( "/var/www/html/ap/{$mFile}".DIR_APLICA_CENTRAL."/lib/interfaz_lib_sat.inc" );
@include_once( "/var/www/html/ap/{$mFile}".DIR_APLICA_CENTRAL."/lib/general/functions.inc" ); //Funciones generales.
@include_once( "/var/www/html/ap/{$mFile}".DIR_APLICA_CENTRAL."/despac/InsertNovedad.inc" );

$fConexion = new Conexion(HOST, USUARIO, CLAVE, BASE_DATOS); //Se crea la conexión a la base de datos
$fInsertNovedad = new InsertNovedad( '3302', '3', '1', $fConexion );

//Para que el cache del Webservice no se quede pegado
ini_set("soap.wsdl_cache_enabled", "0");
try
{
  echo "Inicia Verificacion<br>";

  $mSql = " SELECT a.*, 
                   b.nom_contro, d.cod_transp, c.cod_manifi, 
                   d.num_placax, e.usr_emailx, f.nom_noveda 
              FROM ".BASE_DATOS.".tab_despac_perno2 a 
        INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
                ON a.cod_contro = b.cod_contro 
        INNER JOIN ".BASE_DATOS.".tab_despac_despac c 
                ON a.num_despac = c.num_despac 
        INNER JOIN ".BASE_DATOS.".tab_despac_vehige d 
                ON a.num_despac = d.num_despac 
        INNER JOIN ".BASE_DATOS.".tab_genera_usuari e 
                ON a.usr_creaci = e.cod_usuari 
        INNER JOIN ".BASE_DATOS.".tab_genera_noveda f 
                ON 6 = f.cod_noveda 
             WHERE a.ind_pernoc = 2 
               AND a.fec_inicio <= NOW() ";
  $mConsult = new Consulta($mSql, $fConexion);
  $mData = $mConsult -> ret_matrix('a');

  echo "<pre>mData<br>"; print_r($mData); echo "</pre>";
  die();

  foreach ($mData as $row) {
    $mEnvData = array();
    $mFecAct = date("Y-m-d H:i:s");

    $mPcxNextxx = getNextPC( $fConexion, $row['num_despac'] );
    $mUltNoveda = getNovedadesDespac( $fConexion, $row['num_despac'], 2 );
    $mTransp = getTransTipser( $fConexion, " AND a.cod_transp = $row[cod_transp] ", array('a.cod_tipser') );
    $mTieAdicio = abs( (strtotime($row['fec_reinic']) - strtotime($mFecAct)) ) / 60;
    $mTieAdicio = round($mTieAdicio);

    if( $mUltNoveda['fec_crenov'] != '' ){
      $mTieUltNov = abs( (strtotime($mFecAct) - strtotime($mUltNoveda['fec_crenov'])) ) / 60;
      $mTieUltNov = round($mTieUltNov);
    }else{
      $mTieUltNov = 0;
    }

    $mEnvData['email'] = $row['usr_emailx'];
    $mEnvData['virtua'] = $mPcxNextxx['ind_virtua'];
    $mEnvData['tip_servic'] = $mTransp[0]['cod_tipser'];
    $mEnvData['celular'] = '';
    $mEnvData['despac'] = $row['num_despac'];
    $mEnvData['contro'] = $mPcxNextxx['cod_contro'];
    $mEnvData['noveda'] = 6;
    $mEnvData['tieadi'] = $mTieAdicio;
    $mEnvData['fecact'] = $mFecAct;
    $mEnvData['fecnov'] = $mFecAct;
    $mEnvData['usuari'] = $row['usr_creaci'];
    $mEnvData['nittra'] = $row['cod_transp'];
    $mEnvData['indsit'] = '1';
    $mEnvData['sitio'] = $row['nom_contro'];
    $mEnvData['tie_ultnov'] = $mTieUltNov;
    $mEnvData['tiem'] = '0';
    $mEnvData['rutax'] = $mPcxNextxx['cod_rutasx'];
    $mEnvData['observ'] = "EL CONDUCTOR INFORMA QUE SE ENCUENTRA UBICADO EN: $row[obs_ubicac]";
    $mEnvData['observ'] .= $row['obs_parque'] == "" ? "" : ", PARQUEADERO: $row[obs_parque]";
    $mEnvData['observ'] .= $row['obs_hotelx'] == "" ? "" : ", HOTEL: $row[obs_hotelx]";
    $mEnvData['observ'] .= "; REINICIA RUTA: $row[fec_reinic].";

    echo '<pre style="color: black"> mEnvData:: '; print_r($mEnvData); echo '</pre>';

    $fInsertNovedad->InsertarNovedadNC( BASE_DATOS, $mEnvData, 0 );

    $mSql = "UPDATE satt_faro.tab_despac_perno2 
                SET ind_pernoc = 1 
              WHERE cod_consec = $row[cod_consec]";
    $mConsult = new Consulta($mSql, $fConexion );
    echo '<hr>';
  }

  echo "<br>Fin Verificacion";
}
catch( Exception $e )
{
  echo "<pre>";
  print_r( $e );
  echo "</pre>";

  $mFolder = date("Y-m-d")."_CronPernocDespac.txt";
  $mFile = fopen("logs/".$mFolder, "a+");
  fwrite($mFile, date("Y-m-d H:i:s")."|".$e->getCode()."|".$e->getMessage()."|".$e->getLine()."\n");
  fclose($mFile);

  chmod("logs/".$mFolder, 0775);

  return FALSE;
}

?>