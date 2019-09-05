<?php

            ini_set('display_errors', true);
            error_reporting(E_ALL & ~E_NOTICE);
$mCon = $this -> conexion;

include("../".DIR_APLICA_CENTRAL."/lib/InterfGPS.inc");


$mInterfGps = new InterfGPS( $this -> conexion );

$mInterfGps -> setPlacaIntegradorGPS( '3621051', ['ind_transa' => 'I'] );



?>