<?php

 function bitacora($cod_plonit, $operacion, $usr, $conex,$tabla)
  {
  $fec_actual = date("Y-m-d H:i:s");
  $query = "SELECT MAX(num_consul)+1
            FROM crt.tab_genera_bitaco";
  $consulta = new Consulta($query, $conex);
  $num_consul = $consulta -> ret_matriz();

  $query1="INSERT INTO crt.tab_genera_bitaco
          VALUES ('".$num_consul[0][0]."','$usr','$cod_plonit','$operacion','$tabla','$fec_actual')";
  $cons = new Consulta($query1, $conex);
  }//Fin bitacora
?>
