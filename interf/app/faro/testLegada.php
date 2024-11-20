<?php
 
 include ("faro.php");




$fNomUsuari = "soporte"; 
$fPwdClavex = "mysql2015"; 
$fCodTransp = "830004861"; 
$fNumManifi = "0112011992";
$fNumPlacax = "SNH932"; 
$fCodNoveda = "2"; 
$fCodContro = "9999"; 
$fTimDuraci = "0"; 
$fFecNoveda = "2016-02-20 07:23:15"; 
$fDesNoveda = "Prueba registro de finalizados"; 
$fNomNoveda = NULL; 
$fNomContro = NULL;
$fNomSitiox = NULL; 
$fNumViajex = "VJ-437670";



 
 $mData = setNovedadNC( $fNomUsuari,$fPwdClavex,$fCodTransp,$fNumManifi,$fNumPlacax,$fCodNoveda,
 						$fCodContro,$fTimDuraci,$fFecNoveda,$fDesNoveda,$fNomNoveda,$fNomContro,
 						$fNomSitiox,$fNumViajex);
 echo "<pre>"; print_r( $mData); echo "</pre>";
?>