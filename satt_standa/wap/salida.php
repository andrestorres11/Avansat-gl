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
                 a.fec_salida Is Null AND
                 a.fec_llegad Is Null AND
                 a.ind_anulad = 'R' AND
                 a.ind_planru = 'S'
		";

  if($fi1)
     $query .= " AND  h.cod_client = '".$fi1."'";
  if($fi3)
     $query .= " AND  d.cod_conduc = '".$fi3."'";

  $query .= " GROUP BY 1 ORDER BY 1";

  $consulta = new Consulta($query, $link);
  $matriz   = $consulta -> ret_matriz();

  if(!$matriz)
  {
   echo "<wml>\n"
      ." <card title=\"SALIDA\">"
      ."  <p align=\"center\">\n"
      ."   <b>:: ERROR ::</b><br/>\n"
      ."   No Se Encuentran Despachos Pendientes por Salir.\n"
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

        ."    <postfield name=\"op\" value=\"2\"/>"

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



function insertar_salida($despac, $link, $usuario, $fi1, $fi2, $fi3)
{
  $fec_actual = date("Y-m-d H:i:s");

   $query = "SELECT a.num_placax,a.cod_rutasx
	       FROM ".BASE_DATOS.".tab_despac_vehige a
	      WHERE a.num_despac = ".$despac."
	    ";

   $consulta = new Consulta($query, $link);
   $placax = $consulta -> ret_matriz();

      //La Eliminaciin de los registros de pernoctacin relacionados a este plan de ruta
    //se eliminan en cascada.

    $query = "SELECT b.cod_contro,b.val_duraci
		FROM ".BASE_DATOS.".tab_despac_seguim a,
		     ".BASE_DATOS.".tab_genera_rutcon b
	       WHERE a.num_despac = ".$despac." AND
		     a.cod_rutasx = b.cod_rutasx AND
		     a.cod_contro = b.cod_contro
		     ORDER BY b.val_duraci
	     ";

    $consulta = new Consulta($query, $link);
    $pcontro = $consulta -> ret_matriz();

    $query = "DELETE FROM ".BASE_DATOS.".tab_despac_seguim
		    WHERE num_despac = ".$despac." AND
			  cod_rutasx = ".$placax[0][1]."
	     ";

    $consulta = new Consulta($query, $link, "BR");

    $tieacu = 0;

    for($i = 0; $i < sizeof($pcontro); $i++)
    {
	 $tiepla = $tieacu + $pcontro[$i][1];

	 $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
			       (num_despac,cod_rutasx,cod_contro,fec_planea,
				fec_alarma,usr_creaci,fec_creaci,usr_modifi,
				fec_modifi)
                   	VALUES (".$despac.",".$placax[0][1].",".$pcontro[$i][0].",
                     		DATE_ADD('$fec_actual', INTERVAL ".$tiepla." MINUTE),
                     		DATE_ADD('$fec_actual', INTERVAL ".$tiepla." MINUTE),
                     		'".$usuario."','$fec_actual',NULL,NULL)";

    	 $insercion = new Consulta($query, $link,"R");
    }

    $query = "SELECT DATE_ADD('$fec_actual', INTERVAL ".$tiepla." MINUTE)
	    ";

    $consulta = new Consulta($query, $link);
    $timlle = $consulta -> ret_matriz();

    $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
		SET fec_salipl = '".$fec_actual."',
		    ind_activo = 'S',
		    fec_llegpl = '".$timlle[0][0]."',
		    usr_modifi = '".$usuario."',
		    fec_modifi = '".$fec_actual."'
	      WHERE num_despac = '".$despac."'
	    ";

    $insercion = new Consulta($query, $link,"R");

    $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
		SET fec_salida= '".$fec_actual."',
		    usr_modifi = '".$usuario."',
		    fec_modifi = '".$fec_actual."'
	      WHERE num_despac = '".$despac."'
	    ";

    $insercion = new Consulta($query, $link,"R");

  $query = "SELECT a.cod_transp,a.num_placax,a.cod_rutasx
	      FROM ".BASE_DATOS.".tab_despac_vehige a
	     WHERE a.num_despac = ".$despac."
	   ";

  $consulta = new Consulta($query, $link);
  $transpor = $consulta -> ret_matriz();

  //Manejo de la Interfaz Aplicaciones SAT
  $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$usuario,$link);

  if($interfaz -> totalact > 0)
  {
   for($i = 0; $i < $interfaz -> totalact; $i++)
   {
    //query para traer el nombre de la ruta
    $query = "SELECT nom_rutasx
		FROM ".BASE_DATOS.".tab_genera_rutasx
	       WHERE cod_rutasx = ".$transpor[0][2]."
	     ";

    $consulta = new Consulta($query, $link);
    $nomrut = $consulta -> ret_vector();

    $homolocon = $interfaz -> getHomoloTranspRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$transpor[0][2]);

    if($homolocon["RUTAHomolo"] > 0)
    {
     //query para traer la agencia del despacho.
     $query = "SELECT c.cod_agenci,b.cod_ciuori,b.cod_ciudes,c.nom_agenci,
		      c.con_agenci,c.cod_ciudad,c.dir_agenci,c.tel_agenci,
		      c.dir_emailx
	         FROM ".BASE_DATOS.".tab_despac_vehige a,
		      ".BASE_DATOS.".tab_despac_despac b,
		      ".BASE_DATOS.".tab_genera_agenci c
		WHERE a.num_despac = '".$despac."' AND
		      b.num_despac = a.num_despac AND
		      a.cod_agenci = c.cod_agenci
	      ";

     $consulta = new Consulta($query, $link);
     $datbas = $consulta -> ret_vector();

     $agenci_ws["agenci"] = $datbas[0];
     $agenci_ws["nombre"] = $datbas[3];
     $agenci_ws["contac"] = $datbas[4];
     $agenci_ws["ciudad"] = $datbas[5];
     $agenci_ws["direcc"] = $datbas[6];
     $agenci_ws["telefo"] = $datbas[7];
     $agenci_ws["correo"] = $datbas[8];

     //query para traer el primer cliente del Despacho
     $query = "SELECT MIN(a.cod_client)
	         FROM ".BASE_DATOS.".tab_despac_remesa a
		WHERE a.num_despac = '".$despac."'
	      ";

     $consulta = new Consulta($query, $link);
     $generador = $consulta -> ret_vector();

     if($generador)
     {
      $query = "SELECT a.cod_tercer,a.cod_tipdoc,a.nom_apell1,a.nom_apell2,
		       a.nom_tercer,a.abr_tercer,a.dir_domici,a.num_telef1,
		       a.num_telmov,a.dir_emailx,a.cod_ciudad,a.obs_tercer
		  FROM ".BASE_DATOS.".tab_tercer_tercer a
		 WHERE a.cod_tercer = '".$generador[0]."'
	       ";

      $consulta = new Consulta($query, $link);
      $genera = $consulta -> ret_vector();

      $genera_ws["tercer"] = $genera[0];
      $genera_ws["tipdoc"] = $genera[1];
      $genera_ws["nombre"] = $genera[4]." ".$genera[2]." ".$genera[3];
      $genera_ws["abrevi"] = $genera[5];
      $genera_ws["direcc"] = $genera[6];
      $genera_ws["telefo"] = $genera[7];
      $genera_ws["celula"] = $genera[8];
      $genera_ws["correo"] = $genera[9];
      $genera_ws["ciudad"] = $genera[10];
      $genera_ws["licenc"] = "";
      $genera_ws["catlic"] = "";
      $genera_ws["venlic"] = "";
      $genera_ws["observ"] = $genera[11];
      $genera_ws["estado"] = "1";
      $genera_ws["activi"] = "1";
     }

     //query para traer el Propietario
     $query = "SELECT c.cod_tercer,c.cod_tipdoc,c.nom_apell1,c.nom_apell2,
		      c.nom_tercer,c.abr_tercer,c.dir_domici,c.num_telef1,
		      c.num_telmov,c.dir_emailx,c.cod_ciudad,c.obs_tercer
               	 FROM ".BASE_DATOS.".tab_vehicu_vehicu a,
		      ".BASE_DATOS.".tab_despac_vehige b,
		      ".BASE_DATOS.".tab_tercer_tercer c
               WHERE b.num_despac = '".$despac."' AND
		     a.num_placax = b.num_placax AND
		     a.cod_propie = c.cod_tercer
	      ";

     $consulta = new Consulta($query, $link);
     $propie = $consulta -> ret_vector();

     $propie_ws["tercer"] = $propie[0];
     $propie_ws["tipdoc"] = $propie[1];
     $propie_ws["nombre"] = $propie[4]." ".$propie[2]." ".$propie[3];
     $propie_ws["abrevi"] = $propie[5];
     $propie_ws["direcc"] = $propie[6];
     $propie_ws["telefo"] = $propie[7];
     $propie_ws["celula"] = $propie[8];
     $propie_ws["correo"] = $propie[9];
     $propie_ws["ciudad"] = $propie[10];
     $propie_ws["licenc"] = "";
     $propie_ws["catlic"] = "";
     $propie_ws["venlic"] = "";
     $propie_ws["observ"] = $propie[11];
     $propie_ws["estado"] = "1";
     $propie_ws["activi"] = "2";

     //query para traer el Tenedor
     $query = "SELECT c.cod_tercer,c.cod_tipdoc,c.nom_apell1,c.nom_apell2,
		      c.nom_tercer,c.abr_tercer,c.dir_domici,c.num_telef1,
		      c.num_telmov,c.dir_emailx,c.cod_ciudad,c.obs_tercer
               	 FROM ".BASE_DATOS.".tab_vehicu_vehicu a,
		      ".BASE_DATOS.".tab_despac_vehige b,
		      ".BASE_DATOS.".tab_tercer_tercer c
               WHERE b.num_despac = '".$despac."' AND
		     a.num_placax = b.num_placax AND
		     a.cod_tenedo = c.cod_tercer
	      ";

     $consulta = new Consulta($query, $link);
     $tenedo = $consulta -> ret_vector();

     $tenedo_ws["tercer"] = $tenedo[0];
     $tenedo_ws["tipdoc"] = $tenedo[1];
     $tenedo_ws["nombre"] = $tenedo[4]." ".$tenedo[2]." ".$tenedo[3];
     $tenedo_ws["abrevi"] = $tenedo[5];
     $tenedo_ws["direcc"] = $tenedo[6];
     $tenedo_ws["telefo"] = $tenedo[7];
     $tenedo_ws["celula"] = $tenedo[8];
     $tenedo_ws["correo"] = $tenedo[9];
     $tenedo_ws["ciudad"] = $tenedo[10];
     $tenedo_ws["licenc"] = "";
     $tenedo_ws["catlic"] = "";
     $tenedo_ws["venlic"] = "";
     $tenedo_ws["observ"] = $tenedo[11];
     $tenedo_ws["estado"] = "1";
     $tenedo_ws["activi"] = "3";

     //query para traer el Conductor
     $query = "SELECT c.cod_tercer,c.cod_tipdoc,c.nom_apell1,c.nom_apell2,
		      c.nom_tercer,c.abr_tercer,c.dir_domici,c.num_telef1,
		      c.num_telmov,c.dir_emailx,c.cod_ciudad,c.obs_tercer,
		      d.num_licenc,d.num_catlic,d.fec_venlic
               	 FROM ".BASE_DATOS.".tab_vehicu_vehicu a,
		      ".BASE_DATOS.".tab_despac_vehige b,
		      ".BASE_DATOS.".tab_tercer_tercer c,
		      ".BASE_DATOS.".tab_tercer_conduc d
               WHERE b.num_despac = '".$despac."' AND
		     a.num_placax = b.num_placax AND
		     a.cod_conduc = c.cod_tercer AND
		     c.cod_tercer = d.cod_tercer
	      ";

     $consulta = new Consulta($query, $link);
     $conduc = $consulta -> ret_vector();

     $conduc_ws["tercer"] = $conduc[0];
     $conduc_ws["tipdoc"] = $conduc[1];
     $conduc_ws["nombre"] = $conduc[4]." ".$conduc[2]." ".$conduc[3];
     $conduc_ws["abrevi"] = $conduc[5];
     $conduc_ws["direcc"] = $conduc[6];
     $conduc_ws["telefo"] = $conduc[7];
     $conduc_ws["celula"] = $conduc[8];
     $conduc_ws["correo"] = $conduc[9];
     $conduc_ws["ciudad"] = $conduc[10];
     $conduc_ws["licenc"] = $conduc[12];
     $conduc_ws["catlic"] = $conduc[13];
     $conduc_ws["venlic"] = $conduc[14];
     $conduc_ws["observ"] = $conduc[11];
     $conduc_ws["estado"] = "1";
     $conduc_ws["activi"] = "4";

     //query para traer el Conductor del Despacho
     $query = "SELECT c.cod_tercer,c.cod_tipdoc,c.nom_apell1,c.nom_apell2,
		      c.nom_tercer,c.abr_tercer,c.dir_domici,c.num_telef1,
		      c.num_telmov,c.dir_emailx,c.cod_ciudad,c.obs_tercer,
		      d.num_licenc,d.num_catlic,d.fec_venlic
               	 FROM ".BASE_DATOS.".tab_despac_vehige b,
		      ".BASE_DATOS.".tab_tercer_tercer c,
		      ".BASE_DATOS.".tab_tercer_conduc d
               WHERE b.num_despac = '".$despac."' AND
		     c.cod_tercer = b.cod_conduc AND
		     d.cod_tercer = c.cod_tercer
	      ";

     $consulta = new Consulta($query, $link);
     $conduc_d = $consulta -> ret_vector();

     $conduc_d_ws["tercer"] = $conduc_d[0];
     $conduc_d_ws["tipdoc"] = $conduc_d[1];
     $conduc_d_ws["nombre"] = $conduc_d[4]." ".$conduc_d[2]." ".$conduc_d[3];
     $conduc_d_ws["abrevi"] = $conduc_d[5];
     $conduc_d_ws["direcc"] = $conduc_d[6];
     $conduc_d_ws["telefo"] = $conduc_d[7];
     $conduc_d_ws["celula"] = $conduc_d[8];
     $conduc_d_ws["correo"] = $conduc_d[9];
     $conduc_d_ws["ciudad"] = $conduc_d[10];
     $conduc_d_ws["licenc"] = $conduc_d[12];
     $conduc_d_ws["catlic"] = $conduc_d[13];
     $conduc_d_ws["venlic"] = $conduc_d[14];
     $conduc_d_ws["observ"] = $conduc[11];
     $conduc_d_ws["estado"] = "1";
     $conduc_d_ws["activi"] = "4";

     //query para traer el Vehiculo
     $query = "SELECT b.num_placax,b.cod_marcax,b.cod_lineax,'1',
		      b.cod_colorx,b.cod_carroc,b.cod_propie,b.cod_tenedo,
		      b.cod_conduc
               	 FROM ".BASE_DATOS.".tab_despac_vehige a,
		      ".BASE_DATOS.".tab_vehicu_vehicu b
               WHERE a.num_despac = '".$despac."' AND
		     a.num_placax = b.num_placax
	      ";

     $consulta = new Consulta($query, $link);
     $vehicu = $consulta -> ret_vector();

     $vehicu_ws["placax"] = $vehicu[0];
     $vehicu_ws["marcax"] = $vehicu[1];
     $vehicu_ws["lineax"] = $vehicu[2];
     $vehicu_ws["clasex"] = $vehicu[3];
     $vehicu_ws["colorx"] = $vehicu[4];
     $vehicu_ws["carroc"] = $vehicu[5];
     $vehicu_ws["propie"] = $vehicu[6];
     $vehicu_ws["poseed"] = $vehicu[7];
     $vehicu_ws["conduc"] = $vehicu[8];

     if($generador)
     {
      //inserta o Actualiza el generador de vehiculo
      $interfaz -> insTercer($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$genera_ws);
     }

     //inserta o Actualiza el propietario de vehiculo
     $interfaz -> insTercer($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$propie_ws);

     //inserta o Actualiza el tenedor del vehiculo
     $interfaz -> insTercer($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$tenedo_ws);

     //inserta o Actualiza el conductor del vehiculo
     $interfaz -> insTercer($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$conduc_ws);

     //inserta o Actualiza el conductor asignado en el Despacho
     $interfaz -> insTercer($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$conduc_d_ws);

     //inserta o Actualiza Agencia
     $interfaz -> insAgenci($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$agenci_ws);

     //inserta o Actualiza el Vehiculo del despacho
     $interfaz -> insVehicu($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$vehicu_ws);

     //query para traer las observaciones y datos finales
     $query = "SELECT a.cod_manifi,a.fec_despac,a.cod_ciuori,a.cod_ciudes,
		      a.obs_despac,a.fec_salida,b.cod_rutasx,b.obs_proesp,
		      b.obs_medcom,b.fec_salipl
		 FROM ".BASE_DATOS.".tab_despac_despac a,
		      ".BASE_DATOS.".tab_despac_vehige b
		WHERE a.num_despac = b.num_despac AND
		      a.num_despac = '".$despac."'
	      ";

     $consulta = new Consulta($query, $link);
     $datfin = $consulta -> ret_vector();

     $query = "SELECT a.cod_contro
		 FROM ".BASE_DATOS.".tab_despac_seguim a
		WHERE a.cod_rutasx = ".$datfin[6]." AND
		      a.num_despac = ".$despac."
		      ORDER BY a.fec_planea
	      ";

     $consulta = new Consulta($query, $link);
     $planru_d = $consulta -> ret_matriz();

     $planru_ws = $planru_d[0][0];

     for($j = 1; $j < sizeof($planru_d); $j++)
      $planru_ws .= "|".$planru_d[$j][0];

     $query = "SELECT a.cod_contro,a.cod_noveda,a.val_pernoc
		 FROM ".BASE_DATOS.".tab_despac_pernoc a
		WHERE a.cod_rutasx = ".$datfin[6]." AND
		      a.num_despac = ".$despac."
	      ";

     $consulta = new Consulta($query, $link);
     $pernoc = $consulta -> ret_matriz();

     if($pernoc)
     {
      $precon_ws = $pernoc[0][0];
      $prenov_ws = $pernoc[0][1];
      $pretie_ws = $pernoc[0][2];

      for($j = 1; $j < sizeof($pernoc); $j++)
      {
       $precon_ws .= "|".$pernoc[$j][0];
       $prenov_ws .= "|".$pernoc[$j][1];
       $pretie_ws .= "|".$pernoc[$j][2];
      }
     }
     else
     {
      $precon_ws = "";
      $prenov_ws = "";
      $pretie_ws = "";
     }

     if($generador)
      $genera_d = $genera_ws["tercer"];
     else
      $genera_d = "";

     $despac_ws["despac"] = $despac;
     $despac_ws["manifi"] = $datfin[0];
     $despac_ws["genera"] = $genera_d;
     $despac_ws["fechax"] = $datfin[1];
     $despac_ws["ciuori"] = $datfin[2];
     $despac_ws["ciudes"] = $datfin[3];
     $despac_ws["agenci"] = $agenci_ws["agenci"];
     $despac_ws["observ"] = $datfin[4];
     $despac_ws["conduc"] = $conduc_d_ws["tercer"];
     $despac_ws["placax"] = $vehicu_ws["placax"];
     $despac_ws["salida"] = $datfin[5];
     $despac_ws["llegad"] = "";
     $despac_ws["obslle"] = "";
     $despac_ws["rutasx"] = $datfin[6];
     $despac_ws["proesp"] = $datfin[7];
     $despac_ws["medcom"] = $datfin[8];
     $despac_ws["salipl"] = $datfin[9];
     $despac_ws["llegpl"] = "";
     $despac_ws["planru"] = $planru_ws;
     $despac_ws["precon"] = $precon_ws;
     $despac_ws["prenov"] = $prenov_ws;
     $despac_ws["pretie"] = $pretie_ws;

     $resultado_ws = $interfaz -> insSalida($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$despac_ws);

     if($resultado_ws["Confirmacion"] == "OK")
      $mensaje_sat .= "El Vehiculo con Placas ".$placax[0][0]." ha Salido Correctamente en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].".<br/>";
     else
      $mensaje_sat .= "Existe un Error al Insertar el Despacho en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].". :: ".$resultado_ws["Confirmacion"]."<br/>";
    }
    else
     $mensaje_sat .= "La Ruta ".$nomrut[0]." no se Encuentra Actualmente Homologada en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].". No hay Seguimiento.<br/>";
   }
  }

  //Manejo de Interfaz GPS

  /*$interf_gps = new Interfaz_GPS();
  $interf_gps -> Interfaz_GPS_envio(NIT_TRANSPOR,BASE_DATOS,$usuario,$link,$nocex_satt);

  for($i = 0; $i  < $interf_gps -> cant_interf; $i++)
  {
	if($interf_gps -> getVehiculo($placax[0][0],$interf_gps -> cod_operad[$i][0],NIT_TRANSPOR))
	{
	 $idgps = $interf_gps -> getIdGPS($placax[0][0],$interf_gps -> cod_operad[$i][0],NIT_TRANSPOR);

	 if($interf_gps -> setSalidaGPS($interf_gps -> cod_operad[$i][0],NIT_TRANSPOR,$placax[0][0],$idgps,$despac))
	 {
	    if($interf_gps -> setAcTimeRepor($interf_gps -> cod_operad[$i][0],NIT_TRANSPOR,$placax[0][0],$idgps,$despac,$fec_actual,$interf_gps -> val_timtra[$i][0]))
	    {
	    	$mensaje = "Activado Seguimiento GPS Operador ".$interf_gps -> nom_operad[$i][0].".<br/>";
	    }
	 }
	 else
	 {
	    $mensaje = "Ocurrio un Error al Activar Seguimiento GPS Operador ".$interf_gps -> nom_operad[$i][0].".<br/>";
	 }
	}
	else
	{
	 $mensaje = "No se Activo Seguimiento GPS Operador ".$interf_gps -> nom_operad[$i][0].". El Vehiculo no se Ecuentra Relacionado con el Operador.<br/>";
	}
  }
*/
  if($consulta = new Consulta ("COMMIT", $link))
  {
   echo "<wml>\n"
        ." <card title=\"SALIDA\">"
	."  <p align=\"center\">\n"
	."   El Vehiculo ".$placax[0][0]." Salio Correctamente en El Sistema\n"
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

if(isset($despac) AND $despac != "")
 insertar_salida($despac, $link, $usuario, $fi1, $fi2, $fi3);
else
 pedir_despacho($link, $fi1, $fi2, $fi3, $cont, $usuario);

?>
