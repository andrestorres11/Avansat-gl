<?php

function pedir_placa($link, $fi1, $fi2, $fi3, $cont, $usuario)
{
  $fec_actual = date("Y-m-d H:i:s");

  //QUERY PARA LAS PLACAS
  $query = "SELECT b.num_placax,a.num_despac
              FROM ".BASE_DATOS.".tab_despac_despac a,
		   		   ".BASE_DATOS.".tab_despac_vehige b,
		   		   ".BASE_DATOS.".tab_despac_seguim c,
		   		   ".BASE_DATOS.".tab_genera_contro d
             WHERE a.num_despac = b.num_despac AND
		   		   a.num_despac = c.num_despac AND
                   c.cod_contro = d.cod_contro AND
                   d.ind_urbano = '1' AND
                   a.fec_salida Is Not Null AND
                   a.fec_salida <= NOW() AND
                   a.fec_llegad Is Null AND
                   a.ind_anulad = 'R'
	   ";

  if($fi1)
     $query .= " AND  b.cod_transp = $fi1";
  if($fi2)
     $query .= " AND  c.cod_contro = $fi2";
  if($fi3)
     $query .= " AND  b.cod_conduc = $fi3";

  $query .= " GROUP BY 2 ORDER BY 1";

  $consulta = new Consulta($query, $link);
  $matriz   = $consulta -> ret_matriz();

  if(!$matriz)
  {
   echo "<wml>\n"
      ." <card title=\"NOVEDAD\">"
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

     echo"  <p>\nPlaca: (".$matriz[$reg_inicio][0]."-".$matriz[$reg_final][0].") \n";

     }

     if($n_pages == $pages)

     {

     echo"  <p>\nPlaca: (".$matriz[$inicio][0]."-".$matriz[$ultimo][0].") \n";

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

     echo "            <option value=\"".$matriz[$j][1]."\">\n"

         ."            ".$matriz[$j][1]." - (".$matriz[$j][0].")\n"

         ."            </option>\n";

    }//fin for $j

    echo "  </select>";

     echo"  <do type=\"accept\" label=\"Aceptar\">\n"

        ."   <go href=\"index.php\">\n"

        ."    <postfield name=\"despac\" value=\"\$despac\"/>\n"

        ."    <postfield name=\"op\" value=\"5\"/>"

        ."    <postfield name=\"usuario\" value=\"$usuario\"/>\n"

        ."    <postfield name=\"cont\" value=\"$n_pages\"/>\n"

        ."    <postfield name=\"fi1\" value=\"$fi1\"/>\n"

        ."    <postfield name=\"fi2\" value=\"$fi2\"/>\n"

        ."    <postfield name=\"fi3\" value=\"$fi3\"/>\n"

        ."    <postfield name=\"sad_bd\" value=\"$sad_bd\"/>\n"

        ."   </go>\n"

        ."  </do>\n"

        ."  </p>"

        ." </card>"

        ."</wml>";

}



function pedir_novedad($despac, $link, $usuario, $fi1, $fi2, $fi3, $vallis, $consel)
{
   $query = "SELECT a.num_placax
	       FROM ".BASE_DATOS.".tab_despac_vehige a
	      WHERE a.num_despac = ".$despac."
	    ";

   $consulta = new Consulta($query, $link);
   $placax   = $consulta -> ret_vector();

   $query="SELECT  b.cod_contro,b.nom_contro,e.fec_salipl
              FROM ".BASE_DATOS.".tab_genera_contro b,
                   ".BASE_DATOS.".tab_despac_seguim d,
                   ".BASE_DATOS.".tab_despac_vehige e,
                   ".BASE_DATOS.".tab_genera_contro f
             WHERE b.cod_contro = d.cod_contro AND
                   e.num_despac = d.num_despac AND
                   e.cod_rutasx = d.cod_rutasx AND
                   d.cod_contro = f.cod_contro AND
                   f.ind_urbano = '1' AND
                   e.num_despac = ".$despac."
	   ";

   if($fi1)
    $query = $query." AND e.cod_transp = '$fi1'";
   if($fi2)
    $query = $query." AND b.cod_contro = '$fi2'";
   if($fi3)
    $query = $query." AND e.cod_conduc = '$fi3'";

   $query = $query." ORDER BY d.fec_planea";

   $consulta   = new Consulta($query, $link);
   $matrizlink = $consulta -> ret_matriz();

    if(!$matrizlink)
     error($placax[0], $usuario, $link, $sad_bd, $fi1, $fi2, $fi3);
    else
    {
    echo "<wml>\n"

        ."<card title=\"NOVEDAD URBANOS\">"

        ."  <p>\n Despacho # $despac (<b>$placax[0]</b>) <br/> P/C "

        ."           <select name=\"contro\">";

	    for($i = 0; $i < sizeof($matrizlink); $i++)
	    {
		echo " <option value=\"".$matrizlink[$i][0]."\">\n"
                    ."  ".$matrizlink[$i][1]."\n"
                    ." </option>\n";
	    }

            echo "  </select>\n"
		."  <br/> Novedad "
                ."  <select name=\"noveda\">\n";

            $query = "SELECT cod_noveda, nom_noveda
                        FROM ".BASE_DATOS.".tab_genera_noveda
		       WHERE ind_tiempo = '0'
                    	     ORDER BY 1";

            $consulta   = new Consulta($query, $link);
   	    $regnov = $consulta -> ret_matriz();

	    $carepa = 9;//Cantidad de Registros por paginador
	    $totpag = floor((sizeof($regnov) / $carepa)) + 1;//Total de paginadores a utilizar

	    if($vallis == "ava1")
	     $pagact = $GLOBALS[pagact] + 1;
	    else if($vallis == "ret1")
	     $pagact = $GLOBALS[pagact] - 1;
	    else
	     $pagact = 1;

	    if($pagact > 1)
	    {
		 echo "   <option value=\"ret1\">"
                     ."    >>> ".($pagact - 1)." >>>"
                     ."   </option>\n";
	    }

	    for($r = 0; $r < sizeof($regnov); $r++)
            {
		if(($r + 1 <= ($carepa * $pagact)) && ($r + 1 > ($carepa * $pagact - $carepa)))
		{
                 echo "   <option value=\"".$regnov[$r][0]."\">"
                     ."    (".$regnov[$r][0].")".$regnov[$r][1].""
                     ."   </option>\n";
		}
            }

	    if($pagact < $totpag)
	    {
		 echo "   <option value=\"ava1\">"
                     ."    >>> ".($pagact + 1)." >>>"
                     ."   </option>\n";
	    }

            echo "  </select>"

                ."  <do type=\"accept\" label=\"Aceptar\">\n"

                ."   <go href=\"index.php\">\n"

                ."    <postfield name=\"contro\" value=\"\$contro\"/>\n"

                ."    <postfield name=\"noveda\" value=\"\$noveda\"/>\n"

                ."    <postfield name=\"despac\" value=\"$despac\"/>\n"

                ."    <postfield name=\"op\" value=\"5\"/>"

                ."    <postfield name=\"usuario\" value=\"$usuario\"/>\n"

                ."    <postfield name=\"pagact\" value=\"$pagact\"/>\n"

		."    <postfield name=\"fi1\" value=\"$fi1\"/>\n"

                ."    <postfield name=\"fi2\" value=\"$fi2\"/>\n"

                ."    <postfield name=\"fi3\" value=\"$fi3\"/>\n"

                ."   </go>\n"

                ."  </do>\n"

                ."  </p>"

                ." </card>"

                ."</wml>";
      }//fin if
}



function insertar_novedad($placa,$despac, $contro, $noveda, $usuario, $link, $fi1, $fi2, $fi3)
{
  $fec_actual = date("Y-m-d H:i:s");

  $query= "SELECT MAX(e.fec_noveda)
             FROM ".BASE_DATOS.".tab_despac_vehige c,
             	  ".BASE_DATOS.".tab_despac_seguim d,
                  ".BASE_DATOS.".tab_despac_noveda e
            WHERE c.num_despac = d.num_despac AND
                  c.num_despac = e.num_despac AND
                  c.num_despac = ".$despac."
          ";

  $consulta = new Consulta($query, $link);
  $ultrep = $consulta -> ret_matriz();

  $regist["nittra"] = NIT_TRANSPOR;
  $regist["despac"] = $despac;
  $regist["contro"] = $contro;
  $regist["noveda"] = $noveda;
  $regist["tieadi"] = 0;
  $regist["observ"] = "Novedad Via WAP";
  $regist["fecnov"] = $fec_actual;
  $regist["fecact"] = $fec_actual;
  $regist["ultrep"] = $ultrep[0][0];
  $regist["usuari"] = $usuario;

  $consulta = new Consulta("SELECT NOW()", $link,"BR");

  $transac_nov = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$link);
  $RESPON = $transac_nov -> InsertarNovedadPC(BASE_DATOS,$regist,1);

  if($RESPON[0]["indica"])
  {
   $query = "SELECT a.num_placax
   			   FROM ".BASE_DATOS.".tab_despac_vehige a
   			  WHERE a.num_despac = ".$despac."
   		    ";

   $consulta = new Consulta($query, $link);
   $placav = $consulta -> ret_matriz();

   $query = "SELECT a.nom_noveda
   			   FROM ".BASE_DATOS.".tab_genera_noveda a
   			  WHERE a.cod_noveda = ".$noveda."
   		    ";

   $consulta = new Consulta($query, $link);
   $nomnoved = $consulta -> ret_matriz();

   $consulta = new Consulta ("COMMIT", $link);
   $mensaje = $RESPON[0]["mensaj"];

   echo "<wml>\n"
        ." <card title=\"NOVEDAD\">"
	    ."  <p align=\"center\">\n"
	    ."   Placa: <b>".$placav[0][0]."</b><br/>"
	    ."   Novedad: <b>".$nomnoved[0][0]."</b><br/>"
	    ."   FUE INSERTADA<br/>\n"
	    ."   Numero de Reporte: ".$despac."1".$contro.$noveda." <br/>\n"
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
  else
  {
   echo "<wml>\n"
      ." <card title=\"NOVEDAD\">"
      ."  <p align=\"center\">\n"
      ."   <b>:: ERROR ::</b><br/>\n"
      ."   ".$RESPON[0]["mensaj"]."\n"
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
}//fin function

function error_sal($mensaje, $usuario, $link, $sad_bd, $fi1, $fi2, $fi3){
  echo "<wml>\n"

      ." <card title=\"NOVEDAD\">"

      ."  <p align=\"center\">\n"

      ."   <b> -- Mensaje -- </b><br/>"

      ."    <br/>"

      ."   $mensaje<br/>\n"

      ."  </p>\n"

      ."   <do type=\"accept\" label=\"Menu\">\n"

      ."    <go href=\"index.php\">\n"

      ."     <postfield name=\"op\" value=\"ok\"/>\n"

      ."     <postfield name=\"usuario\" value=\"$usuario\"/>\n"

      ."     <postfield name=\"fi1\" value=\"$fi1\"/>\n"

      ."     <postfield name=\"fi2\" value=\"$fi2\"/>\n"

      ."     <postfield name=\"fi3\" value=\"$fi3\"/>\n"

      ."     <postfield name=\"sad_bd\" value=\"$sad_bd\"/>\n"

      ."    </go>\n"

      ."   </do>\n"

      ." </card>\n"

      ."</wml>";
      exit;
  }


function error($placa, $usuario, $link, $sad_bd, $fi1, $fi2, $fi3){
  echo "<wml>\n"

      ." <card title=\"NOVEDAD\">"

      ."  <p align=\"center\">\n"

      ."   <b>- ERROR -</b><br/>\n"

      ."   El vehiculo $placa no esta en ruta<br/>\n"

      ."   por esta oficina de control<br/>\n"

      ."  </p>\n"

      ."   <do type=\"accept\" label=\"Menu\">\n"

      ."    <go href=\"index.php\">\n"

      ."     <postfield name=\"op\" value=\"ok\"/>\n"

      ."     <postfield name=\"usuario\" value=\"$usuario\"/>\n"

      ."     <postfield name=\"fi1\" value=\"$fi1\"/>\n"

      ."     <postfield name=\"fi2\" value=\"$fi2\"/>\n"

      ."     <postfield name=\"fi3\" value=\"$fi3\"/>\n"

      ."     <postfield name=\"sad_bd\" value=\"$sad_bd\"/>\n"

      ."    </go>\n"

      ."   </do>\n"

      ." </card>\n"

      ."</wml>";
        }



if(isset($despac) AND $despac != "")

{

  if(!isset($contro))
     pedir_novedad($despac, $link, $usuario, $fi1, $fi2, $fi3,null,null);
  else
  {
     if($noveda == "ava1" || $noveda == "ret1")
      pedir_novedad($despac, $link, $usuario, $fi1, $fi2, $fi3, $noveda ,$contro);
     else
      insertar_novedad($num_placa,$despac,$contro, $noveda,$usuario, $link, $fi1, $fi2, $fi3);
  }

}

else

     pedir_placa($link, $fi1, $fi2, $fi3, $cont, $usuario);

?>
