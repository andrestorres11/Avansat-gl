<?php

class GeneralFunctions
{
 var $cConexion;

 function GeneralFunctions($mConexion)
 {
  $this -> cConexion = $mConexion;
 }

 function ViewVisibilityInterf($mParame)
 {
  $interfaz = new Interfaz_SAT(BASE_DATOS,$mParame["transp"],"",$this -> cConexion,1,0);

  if($interfaz -> totalact > 0)
  {
   $mQuery = "SELECT a.cod_operad,a.cod_transp
  		        FROM ".BASE_DATOS.".tab_interf_parame a
  		       WHERE a.cod_transp = '".$mParame["transp"]."' AND
  		     	     a.ind_intind = '1'
  		     ";

    $mConsulta = new Consulta($mQuery, $this -> cConexion);
    $mVisibi = $mConsulta -> ret_matriz();

    if($mVisibi)
     return 1;
    else
     return 0;
  }
  else
   return 1;
 }
}
?>