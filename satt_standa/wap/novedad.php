<?php
function pedir_placa( $link, $fi1, $fi2, $fi3, $cont, $usuario )
{
  //$fec_actual = date( "Y-m-d H:i:s" );
  echo "<wml> \n".
       "<card title=\"".NOM_TRANMENU."\">".
       "  <p>Placa: <input name=\"despac\" />".
       "  <do type=\"accept\" label=\"Aceptar\"> \n".
       "   <go href=\"index.php\"> \n".
       "    <postfield name=\"despac\" value=\"\$despac\"/> \n".
       "    <postfield name=\"op\" value=\"1\"/>".
       "    <postfield name=\"usuario\" value=\"$usuario\"/> \n".
       "    <postfield name=\"cont\" value=\"$n_pages\"/> \n".
       "    <postfield name=\"fi1\" value=\"$fi1\"/> \n".
       "    <postfield name=\"fi2\" value=\"$fi2\"/> \n".
       "    <postfield name=\"fi3\" value=\"$fi3\"/> \n".
       "    <postfield name=\"sad_bd\" value=\"$sad_bd\"/> \n".
       "   </go> \n".
       "  </do> \n".
       "  </p>".
       " </card>".
       "</wml>";
}

function pedir_novedad($despac, $link, $usuario, $fi1, $fi2, $fi3, $vallis, $consel)
{
  if( FALSE === @ereg( "^([aA-zZ]{3}[0-9]{3})", $despac ) )
  {
    echo "<wml> \n ".
         "  <card title=\"".NOM_TRANMENU."\"> \n ".
         "  <p>\n La placa ingresada no cumple con el formato de placa AAA001. \n </p> \n ".
         "  </card> \n ".
         "</wml> \n ";
     die();
  }

  $placax = $despac;
  //QUERY PARA LAS PLACAS
  $fQuerySelNumDesp = "SELECT a.num_despac ".
                        "FROM ".BASE_DATOS.".tab_despac_vehige a, ".
                             "".BASE_DATOS.".tab_despac_despac b ".
                       "WHERE a.num_despac = b.num_despac ".
                         "AND a.ind_activo = 'S' ".
                         "AND b.fec_salida Is Not Null ".
                         "AND b.fec_llegad Is Null ".
                         "AND a.num_placax = '".$placax."' ";
  if( $fi1 )
     $fQuerySelNumDesp .= "AND  a.cod_transp = '".$fi1."' ";

  if( $fi3 )
     $fQuerySelNumDesp .= "AND  a.cod_conduc = '".$fi3."' ";

  $fQuerySelNumDesp .= " GROUP BY 1 ORDER BY 1 ";

  $consulta = new Consulta( $fQuerySelNumDesp, $link );
  $despac   = $consulta -> ret_matriz();

  if( 0 != count( $despac ) )
    $despac = $despac[0][0];
  else
  {
    echo "<wml> \n ".
         "  <card title=\"LLEGADA\"> \n ".
         "  <p>\n El vehiculo ".$placax." no se encuentra en ruta. \n </p> \n ".
         "  </card> \n ".
         "</wml> \n ";
     die();
  }

  //para validar los Links de los despachos pendientes
  //trae la ultima fecha de la novedad
  $query = "SELECT b.fec_planea ".
             "FROM ".BASE_DATOS.".tab_despac_noveda a, ".
                  "".BASE_DATOS.".tab_despac_seguim b, ".
                  "".BASE_DATOS.".tab_despac_vehige d ".
            "WHERE d.num_despac = ".$despac." ". 
              "AND d.num_despac = b.num_despac ".
              "AND d.cod_rutasx = b.cod_rutasx ".
              "AND a.num_despac = b.num_despac ".
              "AND a.cod_contro = b.cod_contro ".
              "AND a.fec_noveda = ( SELECT MAX(c.fec_noveda) ".
                                     "FROM ".BASE_DATOS.".tab_despac_noveda c ".
                                    "WHERE c.num_despac = a.num_despac ".
                                      "AND c.cod_rutasx = a.cod_rutasx ) ";

  $consulta = new Consulta( $query, $link );
  $maximo   = $consulta -> ret_vector();

  //trae la ultima fecha de la nota de controlador
  $query = "SELECT b.fec_planea ".
             "FROM ".BASE_DATOS.".tab_despac_contro a, ".
                  "".BASE_DATOS.".tab_despac_seguim b, ".
                  "".BASE_DATOS.".tab_despac_vehige d ".
            "WHERE d.num_despac = '".$despac."' ".
              "AND d.num_despac = b.num_despac ".
              "AND d.cod_rutasx = b.cod_rutasx ".
              "AND a.num_despac = b.num_despac ".
              "AND a.cod_contro = b.cod_contro ".
              "AND a.fec_contro = ( SELECT MAX(c.fec_contro) ".
                                     "FROM ".BASE_DATOS.".tab_despac_contro c ".
                                    "WHERE c.num_despac = a.num_despac ".
                                      "AND c.cod_rutasx = a.cod_rutasx ) ";

  $consulta = new Consulta( $query, $link );
  $maximo_c = $consulta -> ret_vector();

  if($maximo[0] > $maximo_c[0])
    $fecplanult = $maximo[0];
  else
    $fecplanult = $maximo_c[0];

  $query = "SELECT  b.cod_contro,b.nom_contro,e.fec_salipl ".
             "FROM ".BASE_DATOS.".tab_genera_contro b, ".
                  "".BASE_DATOS.".tab_despac_seguim d, ".
                  "".BASE_DATOS.".tab_despac_vehige e ".
            "WHERE b.cod_contro = d.cod_contro ".
              "AND e.num_despac = d.num_despac ".
              "AND e.cod_rutasx = d.cod_rutasx ".
              "AND d.fec_planea >= '".$fecplanult."' ".
              "AND e.num_despac = '".$despac."' ";

  if($fi1)
    $query = $query."AND e.cod_transp = '".$fi1."' ";
  if($fi2)
    $query = $query."AND b.cod_contro = '".$fi2."' ";
  if($fi3)
    $query = $query."AND e.cod_conduc = '".$fi3."' ";

  $query = $query."ORDER BY d.fec_planea ";

  $consulta   = new Consulta( $query, $link );
  $matrizlink = $consulta -> ret_matriz();

  if( !$matrizlink )
    error( $placax[0], $usuario, $link, $sad_bd, $fi1, $fi2, $fi3 );
  else
  {
    echo "<wml> \n ".
         "  <card title=\"NOVEDAD\"> \n ".
         "    <p>\n Despacho # $num_despac (<b>$placax[0]</b>) <br/> P/C ".
         "      <select name=\"contro\">";

	  for($i = 0; $i < sizeof($matrizlink); $i++)
	  {
		  echo " <option value=\"".$matrizlink[$i][0]."\"> \n ".
           "  ".$matrizlink[$i][1]." \n ".
           " </option> \n ";
	  }

    echo "  </select> \n ".
		     "  <br/> Novedad ".
         "  <select name=\"noveda\"> \n ";

    $query = "SELECT cod_noveda, nom_noveda ".
               "FROM ".BASE_DATOS.".tab_genera_noveda ".
		          "WHERE ind_tiempo = '0' ".
              "ORDER BY 1 ";

    $consulta   = new Consulta($query, $link);
   	$regnov = $consulta -> ret_matriz();

	  $carepa = 9;//Cantidad de Registros por paginador
	  $totpag = floor((sizeof($regnov) / $carepa)) + 1;//Total de paginadores a utilizar

	  if( $vallis == "ava1" )
	    $pagact = $_REQUEST[pagact] + 1;
	  elseif( $vallis == "ret1" )
	    $pagact = $_REQUEST[pagact] - 1;
	  else
	    $pagact = 1;

	  if( $pagact > 1 )
	  {
		  echo "   <option value=\"ret1\">".
           "    >>> ".($pagact - 1)." >>>".
           "   </option> \n ";
	  }

	  for($r = 0; $r < sizeof($regnov); $r++)
    {
		  if( ( $r + 1 <= ( $carepa * $pagact ) ) && ( $r + 1 > ( $carepa * $pagact - $carepa ) ) )
		  {
        echo "   <option value=\"".$regnov[$r][0]."\">".
             "    (".$regnov[$r][0].")".$regnov[$r][1]."".
             "   </option> \n ";
		  }
    }

	  if( $pagact < $totpag )
	  {
		  echo "   <option value=\"ava1\">".
           "    >>> ".($pagact + 1)." >>>".
           "   </option> \n ";
	  }
    echo "  </select> \n ".
         "  <do type=\"accept\" label=\"Aceptar\"> \n ".
         "   <go href=\"index.php\"> \n ".
         "    <postfield name=\"contro\" value=\"\$contro\"/> \n ".
         "    <postfield name=\"noveda\" value=\"\$noveda\"/> \n ".
         "    <postfield name=\"despac\" value=\"$despac\"/> \n ".
         "    <postfield name=\"op\" value=\"1\"/>".
         "    <postfield name=\"usuario\" value=\"$usuario\"/> \n ".
         "    <postfield name=\"pagact\" value=\"$pagact\"/> \n ".
         "    <postfield name=\"fi1\" value=\"$fi1\"/> \n ".
         "    <postfield name=\"fi2\" value=\"$fi2\"/> \n ".
         "    <postfield name=\"fi3\" value=\"$fi3\"/> \n ".
         "   </go> \n ".
         "  </do> \n ".
         "  </p> \n ".
         " </card> \n ".
         "</wml>\n";
  }//fin if
}

function insertar_novedad( $placa, $despac, $contro, $noveda, $usuario, $link, $fi1, $fi2, $fi3 )
{
  $fec_actual = date( "Y-m-d H:i:s" );

  $Query =  "SELECT a.num_despac, b.num_placax ".
              "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                   "".BASE_DATOS.".tab_despac_vehige b, ".
                   "".BASE_DATOS.".tab_despac_despac c, ".
                   "".BASE_DATOS.".tab_despac_vehige d ".
             "WHERE a.num_despac = b.num_despac ".
               "AND c.num_despac = d.num_despac ".
               "AND a.cod_paiori = c.cod_paiori ".
               "AND a.cod_depori = c.cod_depori ".
               "AND a.cod_ciuori = c.cod_ciuori ".
               "AND a.cod_paides = c.cod_paides ".
               "AND a.cod_depdes = c.cod_depdes ".
               "AND a.cod_ciudes = c.cod_ciudes ".
               "AND b.num_placax = d.num_placax ".
               "AND b.cod_rutasx = d.cod_rutasx ".
               "AND c.num_despac = '".$despac."' ";

  $consulta = new Consulta( $Query, $link );
  $fDespachos = $consulta -> ret_matriz();

  $fExito = FALSE;

  $consulta = new Consulta( "SELECT NOW()", $link,"BR" );

  foreach( $fDespachos as $despac )
  {
    $query= "SELECT MAX(e.fec_noveda) ".
              "FROM ".BASE_DATOS.".tab_despac_vehige c, ".
               	   "".BASE_DATOS.".tab_despac_seguim d, ".
                   "".BASE_DATOS.".tab_despac_noveda e ".
             "WHERE c.num_despac = d.num_despac ".
               "AND c.num_despac = e.num_despac ".
               "AND c.num_despac = '".$despac[0]."' ";

    $consulta = new Consulta( $query, $link );
    $ultrep = $consulta -> ret_matriz();

    if( NULL === $ultrep[0][0] || "" == $ultrep[0][0] )
    {
      $query= "SELECT MAX( b.fec_contro ) ".
                "FROM ".BASE_DATOS.".tab_despac_contro b ".
               "WHERE  b.num_despac = '".$despac[0]."' ";

      $consulta = new Consulta( $query, $link );
      $ultrep = $consulta -> ret_matriz();

      if( NULL === $ultrep[0][0] || "" == $ultrep[0][0] )
      {
        $query= "SELECT MIN( b.fec_planea ) ".
                  "FROM ".BASE_DATOS.".tab_despac_seguim  b ".
                 "WHERE b.num_despac = '".$despac[0]."' ";

        $consulta = new Consulta( $query, $link );
        $ultrep = $consulta -> ret_matriz();
      }
    }

    $regist["nittra"] = NIT_TRANSPOR;
    $regist["despac"] = $despac[0];
    $regist["contro"] = $contro;
    $regist["noveda"] = $noveda;
    $regist["tieadi"] = 0;
    $regist["observ"] = "Novedad Via WAP";
    $regist["fecnov"] = $fec_actual;
    $regist["fecact"] = $fec_actual;
    $regist["ultrep"] = $ultrep[0][0];
    $regist["usuari"] = $usuario;

    $transac_nov = new Despachos( $_REQUEST[cod_servic], $_REQUEST[opcion], $this -> aplica, $link );
    $RESPON = $transac_nov -> InsertarNovedadPC( BASE_DATOS, $regist, 1 );

    $fNumRepor = array();

    if( $RESPON[0]["indica"] )
    {
      $fNumRepor[] = $despac[0]."-".$contro."-".$noveda;
      $fExito = TRUE;
    }
  }

  $consulta = new Consulta ( "COMMIT", $link );
  $mensaje = $RESPON[0]["mensaj"];

  if( $fExito )
  {
    $query = "SELECT a.nom_noveda ".
               "FROM ".BASE_DATOS.".tab_genera_noveda a ".
              "WHERE a.cod_noveda = '".$noveda."' ";

    $consulta = new Consulta( $query, $link );
    $nomnoved = $consulta -> ret_matriz();

    echo "<wml> \n ".
         " <card title=\"NOVEDAD\">".
         "  <p align=\"center\"> \n ".
         "   Placa: <b>".$fDespachos[0][1]."</b><br/>".
         "   Novedad: <b>".$nomnoved[0][0]."</b><br/>".
         "   FUE INSERTADA<br/> \n ";

    foreach( $fNumRepor as $report )
      echo "   Numero de Reporte: ".$report." <br/> \n ";

    echo "  </p> \n ".
         "   <do type=\"accept\" label=\"Menu\"> \n ".
         "    <go href=\"index.php\"> \n ".
         "     <postfield name=\"op\" value=\"ok\"/> \n ".
         "     <postfield name=\"usuario\" value=\"$usuario\"/> \n ".
         "     <postfield name=\"fi1\" value=\"$fi1\"/> \n ".
         "     <postfield name=\"fi2\" value=\"$fi2\"/> \n ".
         "     <postfield name=\"fi3\" value=\"$fi3\"/> \n ".
         "    </go> \n ".
         "   </do> \n ".
         " </card> \n ".
         "</wml>";
  }
  else
  {
    echo "<wml> \n".
         " <card title=\"NOVEDAD\">".
         "  <p align=\"center\"> \n".
         "   <b>:: ERROR ::</b><br/> \n".
         "   ".$RESPON[0]["mensaj"]." \n".
         "  </p> \n".
         "   <do type=\"accept\" label=\"Menu\"> \n".
         "    <go href=\"index.php\"> \n".
         "     <postfield name=\"op\" value=\"ok\"/> \n".
         "     <postfield name=\"usuario\" value=\"$usuario\"/> \n".
         "     <postfield name=\"fi1\" value=\"$fi1\"/> \n".
         "     <postfield name=\"fi2\" value=\"$fi2\"/> \n".
         "     <postfield name=\"fi3\" value=\"$fi3\"/> \n".
         "    </go> \n".
         "   </do> \n".
         " </card> \n".
         "</wml>";
    exit;
  }
}//fin function

function error_sal( $mensaje, $usuario, $link, $sad_bd, $fi1, $fi2, $fi3 )
{
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

function error( $placa, $usuario, $link, $sad_bd, $fi1, $fi2, $fi3 )
{
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



if( isset($despac) AND $despac != "" )
{
  if( !isset( $contro ) )
    pedir_novedad( $despac, $link, $usuario, $fi1, $fi2, $fi3, null, null );
  else
  {
    if( $noveda == "ava1" || $noveda == "ret1" )
      pedir_novedad( $despac, $link, $usuario, $fi1, $fi2, $fi3, $noveda ,$contro );
    else
      insertar_novedad( $num_placa,$despac,$contro, $noveda,$usuario, $link, $fi1, $fi2, $fi3 );
  }
}
else
  pedir_placa( $link, $fi1, $fi2, $fi3, $cont, $usuario );

?>
