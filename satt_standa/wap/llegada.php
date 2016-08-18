<?php

function pedir_despacho($link, $fi1, $fi2, $fi3, $cont, $usuario)
{
  $fec_actual = date("Y-m-d H:i:s");

  //QUERY PARA LAS PLACAS
  $query = "SELECT a.num_despac,d.num_placax
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_despac_vehige d,
                 ".BASE_DATOS.".tab_tercer_tercer e,
                 ".BASE_DATOS.".tab_genera_ciudad f,
                 ".BASE_DATOS.".tab_genera_ciudad g,
                 ".BASE_DATOS.".tab_vehicu_vehicu i
           WHERE a.num_despac = d.num_despac AND
                 d.cod_conduc = e.cod_tercer AND
                 a.cod_ciuori = f.cod_ciudad AND
                 a.cod_ciudes = g.cod_ciudad AND
                 i.num_placax = d.num_placax AND
                 a.fec_salida Is Not Null AND
                 a.fec_salida <= NOW() AND
                 a.fec_llegad Is Null AND
                 a.ind_anulad = 'R'
	   ";

  if($fi1)
     $query .= " AND  h.cod_client = $fi1";
  if($fi3)
     $query .= " AND  d.cod_conduc = $fi3";

  $query .= " GROUP BY 1 ORDER BY 1";

  $consulta = new Consulta($query, $link);
  $matriz   = $consulta -> ret_matriz();

  if(!$matriz)
  {
   echo "<wml>\n"
      ." <card title=\"LLEGADA\">"
      ."  <p align=\"center\">\n"
      ."   <b>:: ERROR ::</b><br/>\n"
      ."   No Se Encuentran Despachos Actualmente en Ruta.\n"
      ."  </p>\n"
      ."   <do type=\"accept\" label=\"Menu\">\n"
      ."    <go href=\"index.php\">\n"
      ."     <postfield name=\"op\" value=\"ok\"/>\n"
      ."     <postfield name=\"usuario\" value=\"$usuario\"/>\n"
      ."     <postfield name=\"fi1\" value=\"$fi1\"/>\n"
      ."     <postfield name=\"fi2\" value=\"$fi2\"/>\n"
      ."     <postfield name=\"fi3\" value=\"$fi3\"/>\n"
      ."    </go>\n"
      ."   </do>\n"
      ." </card>\n"
      ."</wml>";
      exit;
  }

  //numero de registros por pagina
  $records=9;

  //numero de paginas a imprimir
  $pages = ceil(sizeof($matriz)/$records);

  //pagina en la que va
  $n_pages = $cont+1;

  //limite de inicio de la pagina
  $inicio=($n_pages-1)*$records;

  //limite de final de la pagina
  $final=$n_pages*$records;

  //registro de inicio de la pagina
  $reg_inicio=($n_pages-1)*$records;

  //registro de final de la pagina
  $reg_final=($n_pages*$records)-1;

  //ultimo registro de la matriz
  $ultimo=sizeof($matriz)-1;

  //ultimo registro de la matriz
  $tamano=sizeof($matriz);



     echo "<wml>\n"

        ."<card title=\"".NOM_TRANMENU."\">";

     //titulo y rango de placas

     if($n_pages < $pages)

     {

     echo"  <p>\nDespacho: (".$matriz[$reg_inicio][0]."-".$matriz[$reg_final][0].") \n";

     }

     if($n_pages == $pages)

     {

     echo"  <p>\nDespacho: (".$matriz[$inicio][0]."-".$matriz[$ultimo][0].") \n";

     }

     //lista de placas

     echo"           <select name=\"despac\">";

     //siguiente si hay mas

     if($n_pages < $pages)

     {

     echo "            <option value=\"\">\n"

         ."            SIGUIENTE \n"

         ."            </option>\n";

     }



    for($j=$inicio;$j<sizeof($matriz) AND $j<$final;$j++)

    {

     echo "            <option value=\"".$matriz[$j][0]."\">\n"

         ."            ".$matriz[$j][0]." - (".$matriz[$j][1].")\n"

         ."            </option>\n";

    }//fin for $j

    echo "  </select>";

    echo "  <do type=\"accept\" label=\"Aceptar\">\n"

        ."   <go href=\"index.php\">\n"

	."    <postfield name=\"despac\" value=\"\$despac\"/>"

        ."    <postfield name=\"op\" value=\"3\"/>"

        ."    <postfield name=\"usuario\" value=\"$usuario\"/>\n"

        ."    <postfield name=\"cont\" value=\"$n_pages\"/>\n"

        ."    <postfield name=\"fi1\" value=\"$fi1\"/>\n"

        ."    <postfield name=\"fi2\" value=\"$fi2\"/>\n"

        ."    <postfield name=\"fi3\" value=\"$fi3\"/>\n"

        ."   </go>\n"

        ."  </do>\n"

        ."  </p>\n"

        ." </card>\n"

        ."</wml>\n";

}



function insertar_llegada($despac, $link, $usuario, $fi1, $fi2, $fi3)
{
  $fec_actual = date("Y-m-d H:i:s");

  $query = "SELECT MAX(e.fec_noveda)
              FROM ".BASE_DATOS.".tab_despac_vehige c,
		   ".BASE_DATOS.".tab_despac_seguim d,
                   ".BASE_DATOS.".tab_despac_noveda e
             WHERE c.num_despac = d.num_despac AND
                   c.num_despac = e.num_despac AND
                   c.num_despac = ".$despac."
	   ";

  $consulta = new Consulta($query, $link);
  $ultrep = $consulta -> ret_arreglo();

  if(isset($ultrep[0]))
  {
    if ($fec_actual <= $ultrep[0])
    {
     echo "<wml>\n"
      ." <card title=\"LLEGADA\">"
      ."  <p align=\"center\">\n"
      ."   <b>:: ERROR ::</b><br/>\n"
      ."   La Fecha de Llegada Debe ser Mayor a la Fecha de la Ultima Novedad\n"
      ."  </p>\n"
      ."   <do type=\"accept\" label=\"Menu\">\n"
      ."    <go href=\"index.php\">\n"
      ."     <postfield name=\"op\" value=\"ok\"/>\n"
      ."     <postfield name=\"usuario\" value=\"$usuario\"/>\n"
      ."     <postfield name=\"fi1\" value=\"$fi1\"/>\n"
      ."     <postfield name=\"fi2\" value=\"$fi2\"/>\n"
      ."     <postfield name=\"fi3\" value=\"$fi3\"/>\n"
      ."    </go>\n"
      ."   </do>\n"
      ." </card>\n"
      ."</wml>";
    }
  }

    //actualiza la hora de llegada del despacho
    $query="UPDATE ".BASE_DATOS.".tab_despac_despac
               SET fec_llegad = '$fec_actual',
                   obs_llegad = '$GLOBALS[obs]',
		   usr_modifi = '".$usuario."',
		   fec_modifi = '".$fec_actual."'
             WHERE num_despac = '$despac' ";

   $consulta = new Consulta($query, $link,"BR");

   //Consulta la ruta asignada al Despacho
   $query = "SELECT a.cod_rutasx,a.num_placax
                  FROM ".BASE_DATOS.".tab_despac_vehige a
                  WHERE a.num_despac = '$despac'
                   ";

   $consulta = new Consulta($query, $link);
   $ruta_sad = $consulta -> ret_vector();

  //Manejo de la Interfaz Aplicaciones SAT
  $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$usuario,$link);

  if($interfaz -> totalact > 0)
  {
   for($i = 0; $i < $interfaz -> totalact; $i++)
   {
    $homolodespac = $interfaz -> getHomoloDespac($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$despac);

    if($homolodespac["DespacHomolo"] > 0)
    {
       $despac_ws["despac"] = $despac;
       $despac_ws["fechax"] = $fec_actual;
       $despac_ws["observ"] = $GLOBALS[obs];

       $resultado_ws = $interfaz -> insLlegad($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$despac_ws);

       if($resultado_ws["Confirmacion"] == "OK")
        $mensaje_sat .= "La Llegada Fue Registrada Exitosamente en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].".<br/>";
       else
        $mensaje_sat .= "Se Presento un Error al Insertar la Llegada en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].". :: ".$resultado_ws["Confirmacion"]."<br/>";
    }
   }
  }

  //Manejo de Interfaz GPS

  /*$interf_gps = new Interfaz_GPS();
  $interf_gps -> Interfaz_GPS_envio(NIT_TRANSPOR,BASE_DATOS,$GLOBALS[usuario],$link);

  for($i = 0; $i  < $interf_gps -> cant_interf; $i++)
  {
        if($interf_gps -> getVehiculo($ruta_sad[1],$interf_gps -> cod_operad[$i][0],NIT_TRANSPOR))
        {
         $idgps = $interf_gps -> getIdGPS($ruta_sad[1],$interf_gps -> cod_operad[$i][0],NIT_TRANSPOR);

         if($interf_gps -> setLlegadGPS($interf_gps -> cod_operad[$i][0],NIT_TRANSPOR,$ruta_sad[1],$idgps,$despac))
         {
            $mensaje = "Se Finalizo Seguimiento GPS Operador ".$interf_gps -> nom_operad[$i][0]." Correctamente.<br/>";
         }
         else
         {
            $mensaje = "Ocurrio un Error al Finalizar Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0].".<br/>";
         }

         echo $mensaje;
        }
  }
*/
  if($consulta = new Consulta("COMMIT", $link))
  {
    echo "<wml>\n"
        ." <card title=\"LLEGADA\">"
	."  <p align=\"center\">\n"
	."   El Despac ".$despac." Llego Correctamente en El Sistema\n"
	."  </p>\n"
	."   <do type=\"accept\" label=\"Menu\">\n"
	."    <go href=\"index.php\">\n"
	."     <postfield name=\"op\" value=\"ok\"/>\n"
	."     <postfield name=\"usuario\" value=\"$usuario\"/>\n"
	."     <postfield name=\"fi1\" value=\"$fi1\"/>\n"
	."     <postfield name=\"fi2\" value=\"$fi2\"/>\n"
	."     <postfield name=\"fi3\" value=\"$fi3\"/>\n"
	."    </go>\n"
	."   </do>\n"
	." </card>\n"
	."</wml>";exit;
  }
}

if(isset($despac) AND $despac != "")
 insertar_llegada($despac, $link, $usuario, $fi1, $fi2, $fi3);
else
 pedir_despacho($link, $fi1, $fi2, $fi3, $cont, $usuario);

?>
