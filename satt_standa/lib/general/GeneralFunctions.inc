<?php

class GeneralFunctions
{
 var $cConexion;

 function __construct ( $mConexion )
 {
  $this -> cConexion = $mConexion;
  $this -> ExecuteGeneralProcess();
 }

 function ExecuteGeneralProcess()
 {
   $query = "SELECT ind_inhall, mes_inhall
               FROM ".BASE_DATOS.".tab_emptra_config
              WHERE ind_inhall = '1'
              ";

   $consulta = new Consulta($query, $this -> cConexion);
   $inddesha = $consulta -> ret_arreglo();

   if ( $inddesha && $inddesha[1] != '0' )
   {
   	 $query = "SELECT a.cod_tercer,c.obs_aproba
                 FROM ".BASE_DATOS.".tab_tercer_conduc a,
                 	  ".BASE_DATOS.".tab_manifi_cargax b,
                 	  ".BASE_DATOS.".tab_tercer_tercer c
                WHERE a.cod_tercer = b.cod_conduc AND
                 	  a.cod_tercer = c.cod_tercer AND
                 	  now() > DATE_ADD((SELECT MAX(c.fec_emisio) FROM ".BASE_DATOS.".tab_manifi_cargax c WHERE c.cod_conduc = b.cod_conduc), INTERVAL ".$inddesha[1]." MONTH) AND
                 	  c.cod_estado != '2'
                 	  GROUP BY 1
              ";

     $consulta = new Consulta($query, $this -> cConexion,"BR");
     $tercinha = $consulta -> ret_matriz();

     for ( $i = 0; $i < sizeof( $tercinha ); $i++)
     {
       $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer
       		        SET cod_estado = '2',
       		        	obs_aproba = '".$tercinha[$i][1]."\\nInh: Automaticamente por el Sistema, Mas de ".$inddesha[1]." Meses sin Cargar.'
       		      WHERE cod_tercer = '".$tercinha[$i][0]."'
       		    ";

       $consulta = new Consulta($query, $this -> cConexion,"R");
     }

     $query = "SELECT a.cod_tercer,c.obs_aproba
                 FROM ".BASE_DATOS.".tab_tercer_conduc a,
                 	  ".BASE_DATOS.".tab_tercer_tercer c
                WHERE a.cod_tercer NOT IN (SELECT b.cod_conduc FROM ".BASE_DATOS.".tab_manifi_cargax b) AND
                 	  a.cod_tercer = c.cod_tercer AND
                 	  now() > DATE_ADD(a.fec_creaci, INTERVAL ".$inddesha[1]." MONTH) AND
                 	  c.cod_estado != '2'
                 	  GROUP BY 1
              ";

     $consulta = new Consulta($query, $this -> cConexion,"BR");
     $tercinha = $consulta -> ret_matriz();

     for ( $i = 0; $i < sizeof( $tercinha ); $i++)
     {
       $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer
       		        SET cod_estado = '2',
       		        	obs_aproba = '".$tercinha[$i][1]."\\nInh: Automaticamente por el Sistema, Mas de ".$inddesha[1]." Meses sin Cargar.'
       		      WHERE cod_tercer = '".$tercinha[$i][0]."'
       		    ";

       $consulta = new Consulta($query, $this -> cConexion,"R");
     }


     $query = "SELECT a.num_placax,a.obs_aproba
                 FROM ".BASE_DATOS.".tab_vehicu_vehicu a,
                 	  ".BASE_DATOS.".tab_manifi_cargax b
                WHERE a.num_placax = b.num_placax AND
                 	  now() > DATE_ADD((SELECT MAX(c.fec_emisio) FROM ".BASE_DATOS.".tab_manifi_cargax c WHERE c.num_placax = b.num_placax), INTERVAL ".$inddesha[1]." MONTH) AND
                 	  a.ind_aproba != '0'
                 	  GROUP BY 1
              ";

     $consulta = new Consulta($query, $this -> cConexion);
     $vehcinha = $consulta -> ret_matriz();

     for ( $i = 0; $i < sizeof( $vehcinha ); $i++)
     {
       $query = "UPDATE ".BASE_DATOS.".tab_vehicu_vehicu
       		        SET ind_aproba = '0',
       		        	obs_aproba = '".$vehcinha[$i][1]."\\nInh: Automaticamente por el Sistema, Mas de ".$inddesha[1]." Meses sin Cargar.'
       		      WHERE num_placax = '".$vehcinha[$i][0]."'
       		    ";

       $consulta = new Consulta($query, $this -> cConexion,"R");
     }

     $query = "SELECT a.num_placax,a.obs_aproba
                 FROM ".BASE_DATOS.".tab_vehicu_vehicu a
                WHERE a.num_placax  NOT IN (SELECT b.num_placax FROM ".BASE_DATOS.".tab_manifi_cargax b) AND
                 	  now() > DATE_ADD(a.fec_creaci, INTERVAL ".$inddesha[1]." MONTH) AND
                 	  a.ind_aproba != '0'
                 	  GROUP BY 1
              ";

     $consulta = new Consulta($query, $this -> cConexion);
     $vehcinha = $consulta -> ret_matriz();

     for ( $i = 0; $i < sizeof( $vehcinha ); $i++)
     {
       $query = "UPDATE ".BASE_DATOS.".tab_vehicu_vehicu
       		        SET ind_aproba = '0',
       		        	obs_aproba = '".$vehcinha[$i][1]."\\nInh: Automaticamente por el Sistema, Mas de ".$inddesha[1]." Meses sin Cargar.'
       		      WHERE num_placax = '".$vehcinha[$i][0]."'
       		    ";

       $consulta = new Consulta($query, $this -> cConexion,"R");
     }




     $consulta = new Consulta("COMMIT", $this -> cConexion);
   }
 }

 function ViewVisibilityInterf($mParame)
 {
  $interfaz = new Interfaz_SAT(BASE_DATOS,$mParame["transp"],"",$this -> cConexion,1,0);

  if($interfaz -> totalact > 0)
  {
    $mQuery = "SELECT a.cod_operad,a.cod_transp
  		        FROM ".BASE_DATOS.".tab_interf_parame_temp a
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

 function ShowMessageExecute( $mParame )
 {
   $cForm = new Form( "action:index.php?cod_servic=".$mParame["servic"]."; method:post; name:frm_messag" );

   $cForm -> Table();

     if ( $mParame["typems"] == "ok" )
       ShowOk( $mParame["messag"] );
     else if ( $mParame["typems"] == "error" )
       ShowError( $mParame["messag"] );

   $cForm -> CloseTable();

   $cForm -> Table();
     $cForm -> StyleButton( "name:back; size:200; value:".$mParame["valbck"]."; align:center; end:yes; onclick:BackGeneralForm( 'frm_messag', '".$mParame["Action"]."')" );
   $cForm -> CloseTable();

   $cForm -> Hidden( "name:window; value:central" );
   $cForm -> Hidden( "name:Action" );

   $cForm -> CloseForm();
 }
}
?>