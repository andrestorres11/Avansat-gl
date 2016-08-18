<?php

class Proc_despac
{
 var $conexion,
     $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }

 function principal()
 {
 ini_set("memory_limit", "1024M");
  if(!isset($GLOBALS[opcion]))
    $this -> Buscar();
  else
     {
      switch($GLOBALS[opcion])
       {
        case "2":
          $this -> Resultado();
          break;
        case "3":
          $this -> Formulario();
          break;
        case "4":
          $this -> Actualizar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Buscar()
 {
  $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
   $datos_usuario = $this -> usuario -> retornar();

   $inicio[0][0] = "0";
   $inicio[0][1] = "-";

   $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(c.abr_depart,4),') - ',LEFT(d.nom_paisxx,3))
               FROM ".BASE_DATOS.".tab_genera_ciudad a,
                    ".BASE_DATOS.".tab_despac_despac b,
                    ".BASE_DATOS.".tab_genera_depart c,
                    ".BASE_DATOS.".tab_genera_paises d,
                    ".BASE_DATOS.".tab_despac_vehige e
              WHERE a.cod_ciudad = b.cod_ciuori AND
                    a.cod_depart = c.cod_depart AND
                    a.cod_paisxx = c.cod_paisxx AND
                    c.cod_paisxx = d.cod_paisxx AND
                    b.num_despac = e.num_despac
            ";

   if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_client = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_client = '$datos_filtro[clv_filtro]' ";
    }
   }

   $query .= " GROUP BY 1 ORDER BY 2";

   $consec = new Consulta($query, $this -> conexion);
   $origen = $consec -> ret_matriz();
   $origen = array_merge($inicio,$origen);

   $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(c.abr_depart,4),') - ',LEFT(d.nom_paisxx,3))
               FROM ".BASE_DATOS.".tab_genera_ciudad a,
                    ".BASE_DATOS.".tab_despac_despac b,
                    ".BASE_DATOS.".tab_genera_depart c,
                    ".BASE_DATOS.".tab_genera_paises d,
                    ".BASE_DATOS.".tab_despac_vehige e
              WHERE a.cod_ciudad = b.cod_ciudes AND
                    a.cod_depart = c.cod_depart AND
                    a.cod_paisxx = c.cod_paisxx AND
                    c.cod_paisxx = d.cod_paisxx AND
                    b.num_despac = e.num_despac
            ";

   if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_client = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_client = '$datos_filtro[clv_filtro]' ";
    }
   }

   $query .= " GROUP BY 1 ORDER BY 2";

   $consec = new Consulta($query, $this -> conexion);
   $destin = $consec -> ret_matriz();
   $destin = array_merge($inicio,$destin);

   $formulario = new Formulario ("index.php","post","BUSCAR DESPACHOS","form_list");

   $formulario -> nueva_tabla();
   $formulario -> linea("Especifique las Condiciones de B&uacute;squeda",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> lista("Ciudad de Origen","b_ciuori",$origen,0);
   $formulario -> lista("Ciudad de Destino","b_ciudes",$destin,0);

   $formulario -> nueva_tabla();
   $formulario -> linea("Opciones Detalladas",1,"t2");
   $formulario -> nueva_tabla();
   if($datos_usuario["cod_perfil"] == "")
    $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   else
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);

   if(!$filtro -> listar($this -> conexion))
   {
    $query = "SELECT a.cod_tercer,a.abr_tercer
   				FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			     	 ".BASE_DATOS.".tab_tercer_activi b,
   			     	 ".BASE_DATOS.".tab_despac_vehige c
   			   WHERE a.cod_tercer = b.cod_tercer AND
   			         a.cod_tercer = c.cod_transp AND
   			         b.cod_activi = ".COD_FILTRO_EMPTRA."
   			         GROUP BY 1 ORDER BY 2
   			 ";

    $consulta = new Consulta($query, $this -> conexion);
    $resul = $consulta -> ret_matriz();
    
    $j = 0;
    for($i = 0; $i < sizeof($resul); $i++)
    {
     if($objciud -> GeneralFunctions -> ViewVisibilityInterf(array("transp" => $resul[$i][0])))
     {
      $transpor[$j][0] = $resul[$i][0];
      $transpor[$j][1] = $resul[$i][1];
      $j++;
     }
    }
    
    $transpor = array_merge($inicio,$transpor);
    $formulario -> lista("Transportadora","transp\" onChange=\"form_insert.submit()",$transpor,1);
   }
   $formulario -> radio("Despacho","fil",1,0,0);
   $formulario -> texto ("","text","despac",1,10,6,"","");
   $formulario -> radio("Vehiculo","fil",2,0,1);
   $formulario -> radio("Todos","fil",0,1,1);

   $formulario -> nueva_tabla();
   $formulario -> linea("Selecci&oacute;n Para el Rango de Fecha",1,"t2");

   $feactual = date("Y/m/d");

   $formulario -> nueva_tabla();
   $formulario -> fecha_calendar("Fecha Inicial","fecini","form_list",$feactual,"yyyy/mm/dd",0);
   $formulario -> fecha_calendar("Fecha Final","fecfin","form_list",$feactual,"yyyy/mm/dd",1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> botoni("Buscar","form_list.submit()",0);
   $formulario -> cerrar();
 }

 function Resultado()
 {   
   $datos_usuario = $this -> usuario -> retornar();

   $fechaini = $GLOBALS[fecini]." 00:00:00";
   $fechafin = $GLOBALS[fecfin]." 23:59:59";

   $titori[0][0] = 0;
   $titori[0][1] = "Origen";
   $titdes[0][0] = 0;
   $titdes[0][1] = "Destino";
   $todos[0][0] = 0;
   $todos[0][1] = "Todos";

   $query = "SELECT c.cod_ciudad,CONCAT(c.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_despac_vehige b,
                    ".BASE_DATOS.".tab_genera_ciudad c,
                    ".BASE_DATOS.".tab_genera_depart d,
                    ".BASE_DATOS.".tab_genera_paises e
              WHERE a.num_despac = b.num_despac AND
                    a.cod_ciuori = c.cod_ciudad AND
                    c.cod_depart = d.cod_depart AND
                    c.cod_paisxx = d.cod_paisxx AND
                    d.cod_paisxx = e.cod_paisxx
            ";

  if($GLOBALS[b_ciuori])
   $query .= " AND a.cod_ciuori = ".$GLOBALS[b_ciuori];
  if($GLOBALS[b_ciudes])
   $query .= " AND a.cod_ciudes = ".$GLOBALS[b_ciudes];
  if($GLOBALS[transp])
   $query .= " AND b.cod_transp = ".$GLOBALS[transp];

  if($GLOBALS[ciuori])
   $query .= " AND a.cod_ciuori = ".$GLOBALS[ciuori];
  if($GLOBALS[ciudes])
   $query .= " AND a.cod_ciudes = ".$GLOBALS[ciudes];

  if($GLOBALS[manifi])
   $query .= " AND a.cod_manifi = '".$GLOBALS[manifi]."'";
  if($GLOBALS[numdes])
   $query .= " AND a.num_despac = '".$GLOBALS[numdes]."'";
  if($GLOBALS[vehicu])
   $query .= " AND b.num_placax = '".$GLOBALS[vehicu]."'";
  if($GLOBALS[trayle])
   $query .= " AND b.num_trayle = '".$GLOBALS[trayle]."'";

  if($GLOBALS[fil] == 1)
   $query .= " AND a.num_despac = '".$GLOBALS[despac]."'";
  else if($GLOBALS[fil] == 2)
   $query .= " AND a.ind_anulad = 'R'";
  else if($GLOBALS[fil] == 3)
   $query .= " AND a.ind_anulad = 'A'";
  else if($GLOBALS[fil] == 4)
   $query .= " AND b.num_placax = '".$GLOBALS[despac]."'";

  $query .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";

  if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
    }
   }

  $query .= " GROUP BY 1 ORDER BY 2";
  $consec = new Consulta($query, $this -> conexion);
  $origenes = $consec -> ret_matriz();

  if($GLOBALS[ciuori])
   $origenes = array_merge($origenes,$todos);
  else
   $origenes = array_merge($titori,$origenes);

   $query = "SELECT c.cod_ciudad,CONCAT(c.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_despac_vehige b,
                    ".BASE_DATOS.".tab_genera_ciudad c,
                    ".BASE_DATOS.".tab_genera_depart d,
                    ".BASE_DATOS.".tab_genera_paises e
              WHERE a.num_despac = b.num_despac AND
                    a.cod_ciudes = c.cod_ciudad AND
                    c.cod_depart = d.cod_depart AND
                    c.cod_paisxx = d.cod_paisxx AND
                    d.cod_paisxx = e.cod_paisxx
            ";

  if($GLOBALS[b_ciuori])
   $query .= " AND a.cod_ciuori = ".$GLOBALS[b_ciuori];
  if($GLOBALS[b_ciudes])
   $query .= " AND a.cod_ciudes = ".$GLOBALS[b_ciudes];
  if($GLOBALS[transp])
   $query .= " AND b.cod_transp = ".$GLOBALS[transp];

  if($GLOBALS[ciuori])
   $query .= " AND a.cod_ciuori = ".$GLOBALS[ciuori];
  if($GLOBALS[ciudes])
   $query .= " AND a.cod_ciudes = ".$GLOBALS[ciudes];

  if($GLOBALS[manifi])
   $query .= " AND a.cod_manifi = '".$GLOBALS[manifi]."'";
  if($GLOBALS[numdes])
   $query .= " AND a.num_despac = '".$GLOBALS[numdes]."'";
  if($GLOBALS[vehicu])
   $query .= " AND b.num_placax = '".$GLOBALS[vehicu]."'";
  if($GLOBALS[trayle])
   $query .= " AND b.num_trayle = '".$GLOBALS[trayle]."'";

  if($GLOBALS[fil] == 1)
   $query .= " AND a.num_despac = '".$GLOBALS[despac]."'";
  else if($GLOBALS[fil] == 2)
   $query .= " AND a.ind_anulad = 'R'";
  else if($GLOBALS[fil] == 3)
   $query .= " AND a.ind_anulad = 'A'";
  else if($GLOBALS[fil] == 4)
   $query .= " AND b.num_placax = '".$GLOBALS[despac]."'";

  $query .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";

  if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
    }
   }

  $query .= " GROUP BY 1 ORDER BY 2";
  $consec = new Consulta($query, $this -> conexion);
  $destinos = $consec -> ret_matriz();

  if($GLOBALS[ciudes])
   $destinos = array_merge($destinos,$todos);
  else
   $destinos = array_merge($titdes,$destinos);

   $query = "SELECT a.num_despac,a.cod_manifi,a.ind_anulad,a.cod_ciuori,
                    a.cod_ciudes,c.abr_tercer,b.num_placax,b.num_trayle,
                    d.abr_tercer
               FROM ".BASE_DATOS.".tab_despac_despac a LEFT JOIN
               		".BASE_DATOS.".tab_despac_remdes e
               		ON a.num_despac = e.num_despac,
                    ".BASE_DATOS.".tab_despac_vehige b,
                    ".BASE_DATOS.".tab_tercer_tercer c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.num_despac = b.num_despac AND
                    b.cod_transp = c.cod_tercer AND
                    b.cod_conduc = d.cod_tercer
            ";

  if($GLOBALS[b_ciuori])
   $query .= " AND a.cod_ciuori = ".$GLOBALS[b_ciuori];
  if($GLOBALS[b_ciudes])
   $query .= " AND a.cod_ciudes = ".$GLOBALS[b_ciudes];
  if($GLOBALS[transp])
   $query .= " AND b.cod_transp = ".$GLOBALS[transp];

  if($GLOBALS[ciuori])
   $query .= " AND a.cod_ciuori = ".$GLOBALS[ciuori];
  if($GLOBALS[ciudes])
   $query .= " AND a.cod_ciudes = ".$GLOBALS[ciudes];

  if($GLOBALS[manifi])
   $query .= " AND a.cod_manifi = '".$GLOBALS[manifi]."'";
  if($GLOBALS[numdes])
   $query .= " AND a.num_despac = '".$GLOBALS[numdes]."'";
  if($GLOBALS[vehicu])
   $query .= " AND b.num_placax = '".$GLOBALS[vehicu]."'";
  if($GLOBALS[trayle])
   $query .= " AND b.num_trayle = '".$GLOBALS[trayle]."'";

  if($GLOBALS[fil] == 1)
   $query .= " AND a.num_despac = '".$GLOBALS[despac]."'";
  else if($GLOBALS[fil] == 2)
   $query .= " AND a.ind_anulad = 'R'";
  else if($GLOBALS[fil] == 3)
   $query .= " AND a.ind_anulad = 'A'";
  else if($GLOBALS[fil] == 4)
   $query .= " AND b.num_placax = '".$GLOBALS[despac]."'";

  $query .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";

  if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
    }
	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
    }
   }

  $query .= " GROUP BY 1 ORDER BY 2";
  $consec = new Consulta($query, $this -> conexion);
  $resul = $consec -> ret_matriz();
  
  $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
  
  $j = 0;
  for($i = 0; $i < sizeof($resul); $i++)
  {
   if($objciud -> GeneralFunctions -> ViewVisibilityInterf(array("transp" => $resul[$i][5])))
   {
    $matriz[$j][0] = $resul[$i][0];
    $matriz[$j][1] = $resul[$i][1];
    $matriz[$j][2] = $resul[$i][2];
    $matriz[$j][3] = $resul[$i][3];
    $matriz[$j][4] = $resul[$i][4];
    $matriz[$j][5] = $resul[$i][5];
    $matriz[$j][6] = $resul[$i][6];
    $matriz[$j][7] = $resul[$i][7];
    $matriz[$j][8] = $resul[$i][8];    
    $j++;
   }
  }

  $formulario = new Formulario ("index.php","post","LISTADO DE DESPACHOS","form_item");

  $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Despacho(s).",0,"t2");

  $formulario -> nueva_tabla();
  $formulario -> texto("Despacho","text","numdes\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()",0,6,6,"",$GLOBALS[numdes],"","",1);
  $formulario -> texto("Número de Transporte","text","manifi\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()",0,7,7,"",$GLOBALS[manifi],"","",1);
  $formulario -> linea("Estado",0,"t");
  $formulario -> lista_titulo("","ciuori\" onChange=\"form_item.submit()",$origenes,0);
  $formulario -> lista_titulo("","ciudes\" onChange=\"form_item.submit()",$destinos,0);
  $formulario -> linea("Transportadora",0,"t");
  $formulario -> texto("Vehiculo","text","vehicu\" onChange=\"form_item.submit()",0,6,6,"",$GLOBALS[vehicu],"","",1);
  $formulario -> texto("Remolque","text","trayle\" onChange=\"form_item.submit()",0,6,6,"",$GLOBALS[trayle],"","",1);
  $formulario -> linea("Conductor",1,"t");

   for($i = 0; $i < sizeof($matriz); $i++)
   {
   	if($matriz[$i][2] == "A")
     $estilo = "ie";
    else
     $estilo = "i";

    if($matriz[$i][2] == "R")
     $estado = "Activo";
    else if($matriz[$i][2] == "A")
     $estado = "Anulado";

    $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
    $ciudad_o = $objciud -> getSeleccCiudad($matriz[$i][3]);
    $ciudad_d = $objciud -> getSeleccCiudad($matriz[$i][4]);

    $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&numdespac=".$matriz[$i][0]."&opcion=3 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

    $formulario -> linea($matriz[$i][0],0,$estilo);
    $formulario -> linea($matriz[$i][1],0,$estilo);
    $formulario -> linea($estado,0,$estilo);
    $formulario -> linea($ciudad_o[0][1],0,$estilo);
    $formulario -> linea($ciudad_d[0][1],0,$estilo);
    $formulario -> linea($matriz[$i][5],0,$estilo);
    $formulario -> linea($matriz[$i][6],0,$estilo);
    $formulario -> linea($matriz[$i][7],0,$estilo);
    $formulario -> linea($matriz[$i][8],1,$estilo);
   }

   $formulario -> nueva_tabla();
   $formulario -> oculto("b_ciuori",$GLOBALS[b_ciuori],0);
   $formulario -> oculto("b_ciudes",$GLOBALS[b_ciudes],0);
   $formulario -> oculto("transp",$GLOBALS[transp],0);
   $formulario -> oculto("fil",$GLOBALS[fil],0);
   $formulario -> oculto("fecini",$GLOBALS[fecini],0);
   $formulario -> oculto("fecfin",$GLOBALS[fecfin],0);

   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }

  function Formulario()
  {
   ini_set('memory_limit','128M');
   $datos_usuario = $this -> usuario -> retornar();
   $usuario = $datos_usuario["cod_usuari"];
   
   $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);

   $inicio[0][0]= 0;
   $inicio[0][1]= "-";

   $query = "SELECT a.ind_desurb
  		       FROM ".BASE_DATOS.".tab_config_parame a
  		      WHERE a.ind_desurb = '1'
  		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $desurb = $consulta -> ret_matriz();

   $query = "SELECT a.ind_remdes
  		      FROM ".BASE_DATOS.".tab_config_parame a
  		     WHERE a.ind_remdes = '1'
  		   ";

   $consulta = new Consulta($query, $this -> conexion);
   $manredes = $consulta -> ret_matriz();

   if(!$GLOBALS[recdesp])
   {
   //jorge a.num_carava, a.cod_tipdes
    $query = "SELECT a.num_despac,a.cod_manifi,b.cod_agenci,DATE_FORMAT(a.fec_despac,'%Y/%m/%d'),
                   a.cod_ciuori,a.cod_ciudes,b.cod_rutasx,a.cod_client,
                   a.val_declara,b.num_placax,b.num_trayle,a.val_flecon,
                   a.val_despac,a.val_antici,a.val_retefu,a.nom_carpag,
                   a.nom_despag,a.fec_pagoxx,b.obs_medcom,a.obs_despac,
                   b.cod_transp,b.cod_conduc,a.val_pesoxx,a.gps_operad,
                   a.gps_usuari,a.gps_paswor,a.cod_asegur, a.num_poliza, 
                   a.num_carava, a.cod_tipdes, SUBSTRING( c.num_viajex, 4, 9 ) AS num_viajex
                FROM ".BASE_DATOS.".tab_despac_despac a,
                   ".BASE_DATOS.".tab_despac_vehige b LEFT JOIN ".BASE_DATOS.".tab_despac_viajex c ON b.num_despac = c.num_despac                  
               WHERE a.num_despac = b.num_despac AND
                   a.num_despac = ".$GLOBALS[numdespac]."
             ";

    $consec = new Consulta($query, $this -> conexion);
    $matriz = $consec -> ret_matriz();

    $GLOBALS[transp] = $matriz[0][20];
    $GLOBALS[manifi] = $matriz[0][1];
    $GLOBALS[viaje] = $matriz[0]["num_viajex"];
    $GLOBALS[agencia] = $matriz[0][2];
    $GLOBALS[fecman] = $matriz[0][3];
    $GLOBALS[ciuori] = $matriz[0][4];
    $GLOBALS[ciudes] = $matriz[0][5];
    $GLOBALS[ruta] = $matriz[0][6];
    $GLOBALS[generador] = $matriz[0][7];
    $GLOBALS[valdec] = $matriz[0][8];
    $GLOBALS[placa] = $matriz[0][9];
    $GLOBALS[conduc] = $matriz[0][21];
    $GLOBALS[l_trayle] = $matriz[0][10];
    $GLOBALS[flete] = $matriz[0][11];
    $GLOBALS[despac] = $matriz[0][12];
    $GLOBALS[antici] = $matriz[0][13];
    $GLOBALS[retefu] = $matriz[0][14];
    $GLOBALS[carpag] = $matriz[0][15];
    $GLOBALS[despag] = $matriz[0][16];
    $GLOBALS[fecha_p] = $matriz[0][17];
    $GLOBALS[medcom] = $matriz[0][18];
    $GLOBALS[obsgrl] = $matriz[0][19];
    $GLOBALS[pesoxx] = $matriz[0][22];
    $GLOBALS[recdesp] = 1;
    $GLOBALS[asegur] = $matriz[0]['cod_asegur'];
    $GLOBALS[poliza] = $matriz[0]['num_poliza'];
    $GLOBALS[carava] = $matriz[0]['num_carava']; //jorge
    $GLOBALS[cod_tipdes] = $matriz[0]['cod_tipdes'];//jorge
   }
    
     $query = "SELECT a.cod_tercer,a.abr_tercer
  		      FROM ".BASE_DATOS.".tab_tercer_tercer a,
                 ".BASE_DATOS.".tab_tercer_activi b
  		     WHERE a.cod_tercer = b.cod_tercer AND
                 b.cod_activi = 7
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $asegura = $consulta -> ret_matriz();

  $asegura = array_merge($inicio,$asegura);
  if($GLOBALS[asegur]){
    $query = "SELECT a.cod_tercer,a.abr_tercer
  		      FROM ".BASE_DATOS.".tab_tercer_tercer a,
                 ".BASE_DATOS.".tab_tercer_activi b
  		     WHERE a.cod_tercer = b.cod_tercer AND
                 b.cod_activi = 7 AND
                 a.cod_tercer ='".$GLOBALS[asegur]."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $aseg = $consulta -> ret_matriz();
  $asegura = array_merge($aseg,$asegura);
    
  }
    
    
   $inicio[0][0]= 0;
   $inicio[0][1]= "-";

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/despac.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/puntos.js\"></script>\n";   
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","ACTUALIZAR DESPACHO","form_insert","","");

   $formulario -> linea("Datos Basicos del Despacho",1,"t2");
   $formulario -> nueva_tabla();

   if($datos_usuario["cod_perfil"] == "")
    $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   else
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);

   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $formulario -> oculto("transp",$datos_filtro[clv_filtro],0);
    $GLOBALS[transp] = $datos_filtro[clv_filtro];

   }
   else
   {
    $query = "SELECT a.cod_tercer,a.abr_tercer
   				FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			     	 ".BASE_DATOS.".tab_tercer_activi b
   			   WHERE a.cod_tercer = b.cod_tercer AND
   			         b.cod_activi = ".COD_FILTRO_EMPTRA."
   			         ORDER BY 2
   			 ";

    $consulta = new Consulta($query, $this -> conexion);
    $resul = $consulta -> ret_matriz();
    
    $j = 0;
    for($i = 0; $i < sizeof($resul); $i++)
    {
     if($objciud -> GeneralFunctions -> ViewVisibilityInterf(array("transp" => $resul[$i][0])))
     {
      $transpor[$j][0] = $resul[$i][0];
      $transpor[$j][1] = $resul[$i][1];
      $j++;
     }
    }    
    
    $transpor = array_merge($inicio,$transpor);

    if($GLOBALS[transp])
    {
     if($objciud -> GeneralFunctions -> ViewVisibilityInterf(array("transp" => $GLOBALS[transp])))
     {
      $query = "SELECT a.cod_tercer,a.abr_tercer
   			     FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			          ".BASE_DATOS.".tab_tercer_activi b
   			    WHERE a.cod_tercer = b.cod_tercer AND
   			          b.cod_activi = ".COD_FILTRO_EMPTRA." AND
   			          a.cod_tercer = '".$GLOBALS[transp]."'
   			          ORDER BY 2
   			  	  ";

      $consulta = new Consulta($query, $this -> conexion);
      $transp_a = $consulta -> ret_matriz();
      $transpor = array_merge($transp_a,$transpor);
     }
    }

    $formulario -> nueva_tabla();
    $formulario -> lista("Transportadora","transp\" onChange=\"form_insert.submit()",$transpor,1);
   }

   if($GLOBALS[transp])
   {
   	$formulario -> texto("Número de Transporte","text","manifi\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,7,7,"",$GLOBALS[manifi]);
    $formulario -> texto("Viaje VJ-(00000000)","text","viaje\" maxlength=\"8\" size=\"8\" id=\"viaje\" onkeypress=\"return NumericInput(event)",1,9,9,"",$GLOBALS["viaje"]);
 
   	$query = "SELECT a.cod_tercer,a.abr_tercer
   			    FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			         ".BASE_DATOS.".tab_tercer_activi b,
   			         ".BASE_DATOS.".tab_transp_tercer c
   			   WHERE a.cod_tercer = b.cod_tercer AND
   			         a.cod_tercer = c.cod_tercer AND
   			         c.cod_transp = '".$GLOBALS[transp]."' AND
   			         b.cod_activi = ".COD_FILTRO_CLIENT."
   			 ";

   	$consulta = new Consulta($query, $this -> conexion);
    $listgene = $consulta -> ret_matriz();

    $listgene = array_merge($inicio,$listgene);

    if($GLOBALS[generador])
    {
     $query = "SELECT a.cod_tercer,a.abr_tercer
   			     FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			          ".BASE_DATOS.".tab_tercer_activi b,
   			          ".BASE_DATOS.".tab_transp_tercer c
   			    WHERE a.cod_tercer = b.cod_tercer AND
   			          a.cod_tercer = c.cod_tercer AND
   			          c.cod_transp = '".$GLOBALS[transp]."' AND
   			          b.cod_activi = ".COD_FILTRO_CLIENT." AND
   			          a.cod_tercer = '".$GLOBALS[generador]."'
   			  ";

   	 $consulta = new Consulta($query, $this -> conexion);
     $listgene_a = $consulta -> ret_matriz();

     $listgene = array_merge($listgene_a,$listgene);
    }

   	$query = "SELECT ind_retdpa,val_minret,val_maxret
      			FROM ".BASE_DATOS.".tab_config_parame
      			";

    $consulta = new Consulta($query, $this -> conexion);
    $paramret = $consulta -> ret_matriz();

   	//Consulta el tipo de validaciÃ³n segun parametrizado del valor declarado.
    $query = "SELECT ind_valpol
                FROM ".BASE_DATOS.".tab_config_parame
             ";

    $consulta = new Consulta($query, $this -> conexion);
    $pardec = $consulta -> ret_vector();

    $query = "SELECT cod_perfil
                FROM ".BASE_DATOS.".tab_autori_perfil
               WHERE cod_perfil = '".$this -> usuario -> cod_perfil."' AND
              		 cod_autori = '1'";

    $consec = new Consulta($query, $this -> conexion);
    $autfec = $consec -> ret_arreglo();

    $query = "SELECT a.cod_agenci,a.nom_agenci
                FROM ".BASE_DATOS.".tab_genera_agenci a,
               		 ".BASE_DATOS.".tab_transp_agenci b
               WHERE a.cod_agenci = b.cod_agenci AND
               		 b.cod_transp = '".$GLOBALS[transp]."'
           	  ";

    if($datos_usuario["cod_perfil"] == "")
    {
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND cod_agenci = '$datos_filtro[clv_filtro]' ";
     }
    }
    else
    {
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND cod_agenci = '$datos_filtro[clv_filtro]' ";
     }
    }

    $query .= " ORDER BY 2";

    $consulta = new Consulta($query, $this -> conexion);
    $agencia = $consulta -> ret_matriz();

    $agencias = array_merge($inicio,$agencia);

    if($GLOBALS[agencia])
    {
     $query = "SELECT a.cod_agenci,a.nom_agenci
     		     FROM ".BASE_DATOS.".tab_genera_agenci a
     		    WHERE a.cod_agenci = ".$GLOBALS[agencia]."
     		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $agencia_a = $consulta -> ret_matriz();

     $agencias = array_merge($agencia_a,$agencia);
    }

    $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);

    //trae las ciudades de Origen
    $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
                FROM ".BASE_DATOS.".tab_genera_ciudad a,
		     		 ".BASE_DATOS.".tab_genera_rutasx b,
		     		 ".BASE_DATOS.".tab_genera_ruttra c,
		     		 ".BASE_DATOS.".tab_genera_depart d,
		     		 ".BASE_DATOS.".tab_genera_paises e
               WHERE a.cod_ciudad = b.cod_ciuori AND
		     		 b.cod_depori = d.cod_depart AND
		     		 b.cod_paiori = d.cod_paisxx AND
		     		 d.cod_paisxx = e.cod_paisxx AND
		     		 b.cod_rutasx = c.cod_rutasx AND
		     		 c.cod_transp = '".$GLOBALS[transp]."' AND
		     		 b.ind_estado = '".COD_ESTADO_ACTIVO."'
           	     	 GROUP BY 1 ORDER BY 2
           	  ";

    $consulta = new Consulta($query, $this -> conexion);
    $ciuoris = $consulta -> ret_matriz();

    $ciuoris = array_merge($inicio,$ciuoris);

    if($GLOBALS[ciuori])
    {
   	 $ciudad_a = $objciud -> getSeleccCiudad($GLOBALS[ciuori]);
   	 $ciuoris = array_merge($ciudad_a,$ciuoris);

     //trae las ciudades de destino
     $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
                 FROM ".BASE_DATOS.".tab_genera_ciudad a,
		     		  ".BASE_DATOS.".tab_genera_rutasx b,
		     		  ".BASE_DATOS.".tab_genera_ruttra c,
		     		  ".BASE_DATOS.".tab_genera_depart d,
		     		  ".BASE_DATOS.".tab_genera_paises e
                WHERE a.cod_ciudad = b.cod_ciudes AND
		     		  b.cod_depdes = d.cod_depart AND
		     		  b.cod_paides = d.cod_paisxx AND
		     		  d.cod_paisxx = e.cod_paisxx AND
                      b.cod_ciuori = ".$GLOBALS[ciuori]." AND
		     		  b.cod_rutasx = c.cod_rutasx AND
		     		  c.cod_transp = '".$GLOBALS[transp]."' AND
		     		  b.ind_estado = '".COD_ESTADO_ACTIVO."'
           	     	  GROUP BY 1 ORDER BY 2
           	  ";

     $consulta = new Consulta($query, $this -> conexion);
     $ciudess = $consulta -> ret_matriz();

     $ciudess = array_merge($inicio,$ciudess);

     if($GLOBALS[ciudes])
     {
      $ciudad_a = $objciud -> getSeleccCiudad($GLOBALS[ciudes]);
      $ciudess = array_merge($ciudad_a,$ciudess);

      //trae las rutas segun la ciudad de origen y la ciudad de destino
      $query = "SELECT a.cod_rutasx,a.nom_rutasx
                  FROM ".BASE_DATOS.".tab_genera_rutasx a,
                 	   ".BASE_DATOS.".tab_genera_ruttra b
                 WHERE a.cod_rutasx = b.cod_rutasx AND
                       b.cod_transp = '".$GLOBALS[transp]."' AND
                       a.cod_ciuori = ".$GLOBALS[ciuori]." AND
                       a.cod_ciudes = ".$GLOBALS[ciudes]." AND
		      		   a.ind_estado = '".COD_ESTADO_ACTIVO."'
                       GROUP BY 1 ORDER BY 2 ";

      $consulta = new Consulta($query, $this -> conexion);
      $rutas = $consulta -> ret_matriz();

      $rutas = array_merge($inicio,$rutas);

      if($GLOBALS[ruta])
      {
       $query = "SELECT cod_rutasx,nom_rutasx
                   FROM ".BASE_DATOS.".tab_genera_rutasx
                  WHERE cod_rutasx = ".$GLOBALS[ruta]."
           	       	    ORDER BY 2 ";

       $consulta = new Consulta($query, $this -> conexion);
       $ruta_a = $consulta -> ret_matriz();

       $rutas = array_merge($ruta_a,$rutas);
      }
     }
    }

	if($GLOBALS[placa])
	{
     //TRAE LOS DATOS DEL VEHICULO Y DEL CONDUCTOR
     $query = "SELECT a.num_placax,b.nom_marcax,c.nom_lineax,d.nom_colorx,
                      e.nom_carroc,a.ano_modelo,a.num_config,a.cod_conduc,
                 	  a.cod_propie,a.cod_tenedo,a.cod_carroc
            	 FROM ".BASE_DATOS.".tab_vehicu_vehicu a,
                 	  ".BASE_DATOS.".tab_genera_marcas b,
                 	  ".BASE_DATOS.".tab_vehige_lineas c,
                 	  ".BASE_DATOS.".tab_vehige_colore d,
                 	  ".BASE_DATOS.".tab_vehige_carroc e,
                 	  ".BASE_DATOS.".tab_vehige_config f,
                 	  ".BASE_DATOS.".tab_transp_vehicu i 
           	 	WHERE a.cod_marcax = b.cod_marcax AND
                 	  a.cod_marcax = c.cod_marcax AND
                 	  a.cod_lineax = c.cod_lineax AND
                 	  a.cod_colorx = d.cod_colorx AND
                 	  a.cod_carroc = e.cod_carroc AND
                 	  a.num_config = f.num_config AND
                 	  a.num_placax = i.num_placax AND
                 	  i.cod_transp = '".$GLOBALS[transp]."' AND
                 	  a.ind_estado = ".COD_ESTADO_ACTIVO." AND
                 	  a.num_placax = '".$GLOBALS[placa]."'
            ";

     $consulta = new Consulta($query, $this -> conexion);
     $placas = $consulta -> ret_matriz();

     if($placas)
     {
      $query = "SELECT a.cod_tercer,a.abr_tercer
      		      FROM ".BASE_DATOS.".tab_tercer_tercer a,
      		      	   ".BASE_DATOS.".tab_tercer_activi b,
      		      	   ".BASE_DATOS.".tab_transp_tercer c
      		     WHERE a.cod_tercer = b.cod_tercer AND
      		      	   a.cod_tercer = c.cod_tercer AND
      		      	   a.cod_estado = '".COD_ESTADO_ACTIVO."' AND
      		      	   b.cod_activi = ".COD_FILTRO_PROPIE." AND
      		      	   c.cod_transp = '".$GLOBALS[transp]."' AND
      		      	   a.cod_tercer = '".$placas[0][8]."'
      		   ";

      $consulta = new Consulta($query, $this -> conexion);
      $propihab = $consulta -> ret_matriz();

      $menerrtercer = NULL;

      if(!$propihab)
      {
       $menerrtercer = "El Propietario Asignado al Vehiculo, no Se Encuentra Relacionado a la Transportadora &oacute; no se Encuentra Activo.</br>";
      }

      $query = "SELECT a.cod_tercer,a.abr_tercer
      		      FROM ".BASE_DATOS.".tab_tercer_tercer a,
      		      	   ".BASE_DATOS.".tab_tercer_activi b,
      		      	   ".BASE_DATOS.".tab_transp_tercer c
      		     WHERE a.cod_tercer = b.cod_tercer AND
      		      	   a.cod_tercer = c.cod_tercer AND
      		      	   a.cod_estado = '".COD_ESTADO_ACTIVO."' AND
      		      	   b.cod_activi = ".COD_FILTRO_PROPIE." AND
      		      	   c.cod_transp = '".$GLOBALS[transp]."' AND
      		      	   a.cod_tercer = '".$placas[0][9]."'
      		   ";

      $consulta = new Consulta($query, $this -> conexion);
      $propihab = $consulta -> ret_matriz();

      if(!$propihab)
      {
       $menerrtercer .= "El Poseedor Asignado al Vehiculo, no Se Encuentra Relacionado a la Transportadora &oacute; no se Encuentra Activo.";
      }

      //trae los conductores
      $query = "SELECT a.cod_tercer, a.abr_tercer,a.num_telmov
                  FROM ".BASE_DATOS.".tab_tercer_tercer a,
                       ".BASE_DATOS.".tab_tercer_conduc b,
                       ".BASE_DATOS.".tab_transp_tercer c
                 WHERE a.cod_tercer = b.cod_tercer AND
                       a.cod_tercer = c.cod_tercer AND
                       c.cod_transp = '".$GLOBALS[transp]."' AND
                       a.cod_estado = ".COD_ESTADO_ACTIVO."
					   ORDER BY 2
			";

      $consulta = new Consulta($query, $this -> conexion);
      $conducs  = $consulta -> ret_matriz();

      $conducs = array_merge($inicio,$conducs);

      if($GLOBALS[conduc])
	  {
       $query = "SELECT a.cod_tercer,a.abr_tercer,a.num_telmov
                   FROM ".BASE_DATOS.".tab_tercer_tercer a,
                        ".BASE_DATOS.".tab_tercer_conduc b,
      		            ".BASE_DATOS.".tab_transp_tercer c
                  WHERE a.cod_tercer = c.cod_tercer AND
                        a.cod_tercer = b.cod_tercer AND
                        a.cod_estado = ".COD_ESTADO_ACTIVO." AND
                        a.cod_tercer = '".$GLOBALS[conduc]."' AND
                        c.cod_transp = '".$GLOBALS[transp]."'
                 ";

       $consulta = new Consulta($query, $this -> conexion);
       $conduc_e  = $consulta -> ret_matriz();

       $conducs = array_merge($conduc_e,$conducs);
	  }
	  else
	  {
	   $query = "SELECT a.cod_tercer,a.abr_tercer,a.num_telmov
      		       FROM ".BASE_DATOS.".tab_tercer_tercer a,
      		            ".BASE_DATOS.".tab_tercer_conduc b,
      		            ".BASE_DATOS.".tab_transp_tercer c
      		      WHERE a.cod_tercer = b.cod_tercer AND
      		            a.cod_tercer = c.cod_tercer AND
                        a.cod_estado = ".COD_ESTADO_ACTIVO." AND
      		            c.cod_transp = '".$GLOBALS[transp]."' AND
      		            a.cod_tercer = '".$placas[0][7]."'
      		    ";

       $consulta = new Consulta($query, $this -> conexion);
       $conduc_e = $consulta -> ret_matriz();

       $conducs = array_merge($conduc_e,$conducs);
	  }
     }
     else
     {
      if($GLOBALS[placa])
       $mensaje_vehi = "El Vehiculo con Placas <b>".$GLOBALS[placa]."</b> No se Existe en el Sistema &oacute; no se Encuentra Activo.";

      unset($GLOBALS[placa]);
     }
	}

    $formulario -> lista("Agencia", "agencia", $agencias, 0);

    $fec_manifi = $GLOBALS[fecman];

    if($autfec)
    {
     if(!$GLOBALS[fecman])
      $GLOBALS[fecman]= $fec_manifi;

     $formulario -> fecha_calendar("Fecha(YYYY/MM/DD)","fecman","form_insert",$GLOBALS[fecman],"yyyy/mm/dd",1);
    }
    else
    {
     $fec = $GLOBALS[fecman];
     $formulario -> linea("Fecha",0,"t","","","RIGHT");
     $formulario -> linea($fec_manifi,1,"i");
     $formulario -> oculto("fecman", $fec, 0);
    }

    $formulario -> lista("Origen", "ciuori\" onChange=\"form_insert.submit()", $ciuoris, 0);
    $formulario -> lista("Destino", "ciudes\" onChange=\"form_insert.submit()", $ciudess, 1);
    $formulario -> lista("Ruta", "ruta", $rutas, 0);
    $formulario -> lista("Generador", "generador",$listgene, 1);
    $formulario -> texto ("Valor Declarado del Despacho","text","valdec\" onkeyup=\"puntos(this,this.value.charAt(this.value.length-1))",0,9,12,"","$GLOBALS[valdec]");
    $formulario -> texto ("Peso (Tn)","text","pesoxx\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,5,5,"","$GLOBALS[pesoxx]");
    $formulario -> texto("No. Caravana","text","carava",0,4,4,"","$GLOBALS[carava]");//jorge
    $formulario -> texto("Operador Gps","text","gps_operad",1,15,15,"",$GLOBALS['gps_operad'],"","",0); 
    $formulario -> texto("Usuario Gps","text","gps_usuari",0,15,15,"",$GLOBALS['gps_usuari'],"","",0);
    $formulario -> texto("Contraseña Gps","password","gps_paswor",1,15,15,"",$GLOBALS['gps_paswor'],"","",0);
    $formulario -> lista("Aseguradora","asegur",$asegura,0);
    $formulario -> texto ("No. Poliza","text","poliza",1,20,15,"","$GLOBALS[poliza]");
    $formulario -> oculto("cod_tipdes",$GLOBALS[cod_tipdes],0);//jorge
    
    if($placas)
     $formulario -> oculto("regplaca",1,0);
    else
     $formulario -> oculto("regplaca",0,0);

    $formulario -> nueva_tabla();
    $formulario -> linea("Informaci&oacute;on del Vehiculo",1,"t2");

    if($mensaje_vehi)
    {
     $formulario -> nueva_tabla();
     $formulario -> linea($mensaje_vehi,1,"e");
    }

    $formulario -> nueva_tabla();
    $formulario -> texto ("Placa","text","placa\" onChange=\"if(this.value){form_insert.submit()}else{this.focus()}\"",0,8,8,"",$GLOBALS[placa]);

    if($placas)
    {
     $conduc_a[0][0] = $placas[0][7];
     $conduc_a[0][1] = $placas[0][8];

     $formulario -> linea ("Marca",0,"t","","","RIGHT");
     $formulario -> linea ($placas[0][1],1,"i");
     $formulario -> linea ("Color",0,"t","","","RIGHT");
     $formulario -> linea ($placas[0][3],0,"i");
     $formulario -> linea ("Carroceria",0,"t","","","RIGHT");
     $formulario -> linea ($placas[0][4],1,"i");
     $formulario -> linea ("Modelo",0,"t","","","RIGHT");
     $formulario -> linea ($placas[0][5],0,"i");
     $formulario -> linea ("Configuraci&oacute;n",0,"t","","","RIGHT");
     $formulario -> linea ($placas[0][6],1,"i");

     $formulario -> nueva_tabla();
     $formulario -> linea ("Informaci&oacute;n del Conductor",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> lista("Conductor", "conduc\" onChange=\"form_insert.submit();", $conducs, 1);
     $formulario -> linea ("CC",0,"t","","","RIGHT");
     $formulario -> linea ($conduc_e[0][0],1,"i");
     $formulario -> linea ("Tel&eacute;fono Movil",0,"t","","","RIGHT");
     $formulario -> linea ($conduc_e[0][2],1,"i");

     $query = "SELECT a.num_trayle,MAX(a.num_noveda)
     		     FROM ".BASE_DATOS.".tab_trayle_placas a,
		      		  ".BASE_DATOS.".tab_vehige_trayle b,
		      		  ".BASE_DATOS.".tab_transp_trayle c
     		    WHERE a.num_trayle = b.num_trayle AND
		      		  a.num_trayle = c.num_trayle AND
		      		  c.cod_transp = '".$GLOBALS[transp]."' AND
		      		  b.ind_estado = '".COD_ESTADO_ACTIVO."' AND
		      	 	  a.num_placax = '".$placas[0][0]."'
		      		  GROUP BY 1
     		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $trayler = $consulta -> ret_vector();

     $query = "SELECT a.num_trayle,a.num_trayle
     		     FROM ".BASE_DATOS.".tab_vehige_trayle a,
     		     	  ".BASE_DATOS.".tab_transp_trayle b
	        	WHERE a.ind_estado = '".COD_ESTADO_ACTIVO."' AND
		      		  a.num_trayle = b.num_trayle AND
		      		  b.cod_transp = '".$GLOBALS[transp]."'
     		      	  ORDER BY 1
     		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $listatra = $consulta -> ret_matriz();

     $listatra = array_merge($inicio,$listatra);

     $formulario -> nueva_tabla();
     $formulario -> linea ("Informaci&oacute;n del Remolque",1,"t2");

     $formulario -> nueva_tabla();
     if($placas[0][6] == "2" || $placas[0][6] == "3" || $placas[0][6] == "4")
     {
      $formulario -> linea ("La Configuraci&oacute;n Actual del Vehiculo no Solicita Remolque",0,"i");
      $formulario -> oculto("l_trayle",null,0);
      $formulario -> oculto("soltrayle","0",0);
     }
     else if($trayler)
     {
       if(!$GLOBALS[l_trayle])
       {
        $mi_trayler[0][0] = $trayler[0];
        $mi_trayler[0][1] = $trayler[0];
       }
       else
       {
        $mi_trayler[0][0] = $GLOBALS[l_trayle];
        $mi_trayler[0][1] = $GLOBALS[l_trayle];
       }

       $listatra = array_merge($mi_trayler,$listatra);

       $formulario -> lista("Remolque:", "l_trayle", $listatra, 1);
       $formulario -> oculto("soltrayle","1",0);
     }
     else
     {
       if($GLOBALS[l_trayle])
       {
        $mi_trayler[0][0] = $GLOBALS[l_trayle];
        $mi_trayler[0][1] = $GLOBALS[l_trayle];

        $listatra = array_merge($mi_trayler,$listatra);
       }

       $formulario -> lista("Remolque:", "l_trayle", $listatra, 1);
       $formulario -> linea ("El Vehiculo Solicita una Asignaci&oacute;n De Remolque en su Informaci&oacute;n Base",1,"t2");
       $formulario -> oculto("soltrayle","1",0);
     }

     //vigencia final de Revision Mecanica num_tarpro
     $query = "SELECT a.fec_revmec
                 FROM ".BASE_DATOS.".tab_vehicu_vehicu a
                WHERE a.fec_revmec < NOW() AND
                      a.ind_estado = '".COD_ESTADO_ACTIVO."' AND
                      a.num_placax = '".$GLOBALS[placa]."'
               ";

     $consulta = new Consulta($query, $this -> conexion);
     $revmec = $consulta -> ret_matriz();

     //vigencia final de la licencia de conducion del conductor
     $query = "SELECT a.fec_venlic,b.abr_tercer
                 FROM ".BASE_DATOS.".tab_tercer_conduc a,
                 	  ".BASE_DATOS.".tab_tercer_tercer b
                WHERE a.cod_tercer= b.cod_tercer AND
                      a.cod_tercer = '".$conduc_e[0][0]."' AND
                      a.fec_venlic < NOW()
              ";

     $consulta = new Consulta($query, $this -> conexion);
     $vlicondu = $consulta -> ret_matriz();
    }

    $formulario -> nueva_tabla();
    $formulario -> linea("Informaci&oacute;n Adicional",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> texto ("Medios de Comunicaci&oacute;n:","textarea","medcom",0,20,2,"","$GLOBALS[medcom]");
    $formulario -> texto ("Observaciones Generales:","textarea","obsgrl",1,20,2,"","$GLOBALS[obsgrl]");

    if($manredes)
    {
     $query = "SELECT b.cod_remdes, a.num_remdes, a.nom_remdes, a.obs_adicio,
     				  b.cod_ciudad, b.dir_destin, b.val_pesoxx, b.num_docume,
   			          b.num_pedido,
   			          if(c.ind_tonela = '".COD_ESTADO_ACTIVO."',c.val_costos / c.can_tonela,c.val_costos),
   			          if(c.ind_tonela = '".COD_ESTADO_ACTIVO."',(c.val_costos / c.can_tonela) * b.val_pesoxx,c.val_costos),
   			          if(b.cod_tabfle IS NOT NULL,d.nom_trayec,'-'),
   			          if(c.ind_tonela != '".COD_ESTADO_ACTIVO."',' (Viaje)',''),
   			          b.val_bultox, b.cod_tabfle
   			    FROM ".BASE_DATOS.".tab_genera_remdes a LEFT JOIN
   			         ".BASE_DATOS.".tab_despac_remdes b ON
   			         a.cod_remdes = b.cod_remdes LEFT JOIN
   			         ".BASE_DATOS.".tab_tablax_fletes c ON
   			         b.cod_tabfle = c.cod_consec LEFT JOIN
   			         ".BASE_DATOS.".tab_genera_trayec d ON
   			         c.cod_trayec = d.cod_trayec
   			   WHERE a.cod_remdes = b.cod_remdes AND
   			         a.ind_remdes = '2' AND
   			         b.num_despac = ".$GLOBALS[numdespac]."
   			 ";

   	 $consulta = new Consulta($query, $this -> conexion);
     $liremdes = $consulta -> ret_matriz();

     if(!$GLOBALS[maxrem])
     {
      for($i = 0; $i < sizeof($liremdes); $i++)
      {
       $codrem[$i] = $liremdes[$i][0];
       $docrem[$i] = $liremdes[$i][1];
       $obsrem[$i] = $liremdes[$i][3];
       $nomrem[$i] = $liremdes[$i][2];
       $ciurem[$i] = $liremdes[$i][4];
       $dirrem[$i] = $liremdes[$i][5];
       $pesrem[$i] = $liremdes[$i][6];
       $bulrem[$i] = $liremdes[$i][13];
       $remsel[$i] = 1;
       $refrem[$i] = $liremdes[$i][7];
       $pedrem[$i] = $liremdes[$i][8];
       $codfle[$i] = $liremdes[$i][14];

       if($desurb)
       {
        $query = "SELECT a.tel_contro,a.val_longit,a.val_latitu
       	 	        FROM ".BASE_DATOS.".tab_genera_contro a,
       		       		 ".BASE_DATOS.".tab_destin_contro b
       		       WHERE a.cod_contro = b.cod_contro AND
       		       		 b.cod_remdes = ".$liremdes[$i][0]."
       		     ";

       	$consulta = new Consulta($query, $this -> conexion);
        $remdescont = $consulta -> ret_matriz();

        $contel[$i] = $remdescont[0][0];
        $conlon[$i] = $remdescont[0][1];
        $conlat[$i] = $remdescont[0][2];
       }
      }

      $GLOBALS[maxrem] = sizeof($liremdes);
      $indicasins = 1;
     }
     else
     {
      $codrem = $GLOBALS[codrem];
      $docrem = $GLOBALS[docrem];
      $obsrem = $GLOBALS[obsrem];
      $nomrem = $GLOBALS[nomrem];
      $ciurem = $GLOBALS[ciurem];
      $dirrem = $GLOBALS[dirrem];
      $pesrem = $GLOBALS[pesrem];
      $bulrem = $GLOBALS[bulrem];
      $remsel = $GLOBALS[remsel];
      $refrem = $GLOBALS[refrem];
      $pedrem = $GLOBALS[pedrem];
      $contel = $GLOBALS[contel];
      $conlon = $GLOBALS[conlon];
      $conlat = $GLOBALS[conlat];
      $tabfle = $GLOBALS[tabfle];
      $fleuni = $GLOBALS[fleuni];
      $codfle = $GLOBALS[codfle];

      $indicasins = 0;
     }

     $formulario -> nueva_tabla();
     $formulario -> linea("Selecci&oacute;n de Destinatarios",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> linea("",0,"t");
     $formulario -> linea("(S/N)",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Documento/C&oacute;digo",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Nombre",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Observaciones",0,"t");
     //$formulario -> linea("",0,"t");
     $formulario -> linea("Ciudad",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Direcci&oacute;n",0,"t");

     if($desurb)
     {
      $formulario -> linea("",0,"t");
      $formulario -> linea("Telefono",0,"t");
      $formulario -> linea("",0,"t");
      $formulario -> linea("Longitud",0,"t");
      $formulario -> linea("",0,"t");
      $formulario -> linea("Latitud",0,"t");
     }

     $formulario -> linea("",0,"t");
     $formulario -> linea("Bultos",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Peso (Tn)",0,"t");
     $formulario -> linea("",0,"t");     
     $formulario -> linea("Valor (Unit)",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Valor Flete",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Remisi&oacute;n",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Pedido",1,"t");
     
     $ciudades = $objciud -> getListadoCiudades();
     $ciurem_t = array_merge($inicio,$ciudades);

	 $matpopup[0]["nomvar"] = "tipoxx";
	 $matpopup[0]["valorx"] = "2";
	 $matpopup[1]["nomvar"] = "transport";
	 $matpopup[1]["valorx"] = $transpor[0][0];
	 $matpopup[2]["nomvar"] = "indice";
	 $matpopup[2]["valorx"] = "rem";

     for($i = 0; $i < $GLOBALS[maxrem]; $i++)
     {
      if($ciurem[$i])
      {
       $ciurem_a = $objciud -> getSeleccCiudad($ciurem[$i]);
       $ciurem_s = array_merge($ciurem_a,$ciurem_t);
      }
      else
       $ciurem_s = $ciurem_t;   
       
      $estado = 0;
      if($remsel[$i] || $i == $GLOBALS[maxrem] - 1)
       $estado = 1;

	  if($indicasins)
	   $formulario -> oculto("codrem".$i,$codrem[$i],0);
	  else
	  {
       eval("\$sasignado = \$GLOBALS[codrem".$i."];");

       if($sasignado != "n")
        $formulario -> oculto("codrem".$i,$sasignado,0);
       else
        $formulario -> oculto("codrem".$i,"n",0);
	  }

      array_push( $matpopup, array( "nomvar" => "filter", "valorx" => FALSE ) );

      $formulario -> caja("","remsel[$i]",1,$estado,0);
      $formulario -> texto ("","text","docrem[$i]\" id=\"docrem$i",0,10,10,"",$docrem[$i],"$i;5",3192,NULL,0,$matpopup);
      $formulario -> texto ("","text","nomrem[$i]\" id=\"nomrem$i",0,20,32,"",$nomrem[$i]);
      $formulario -> texto ("","text","obsrem[$i]\" id=\"obsrem$i",0,60,250,"",$obsrem[$i]);
      $formulario -> lista ("","ciurem[$i]\" id=\"ciurem$i\" onClick=\"validarflete(".$i.");",$ciurem_s,0);
      $formulario -> texto ("","text","dirrem[$i]\" id=\"dirrem$i",0,20,32,"",$dirrem[$i]);

      if($desurb)
      {
       $formulario -> texto("","text","contel[$i]\"  onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''} \" id=\"telrem$i",0,30,20,"",$contel[$i]);
	   $formulario -> texto("","text","conlon[$i]\" id=\"lonrem$i",0,30,30,"",$conlon[$i]);
       $formulario -> texto("","text","conlat[$i]\" id=\"latrem$i",0,30,30,"",$conlat[$i]);
      }

      $matpopup[3]["nomvar"] = "ciuoritab";
	  $matpopup[3]["valorx"] = $GLOBALS[ciuori];
	  $matpopup[4]["nomvar"] = "carroctab";
	  $matpopup[4]["valorx"] = $placas[0][10];
	  
	  $idelement[0]["nomvar"] = "ciurem$i";	  
	  $idelement[1]["nomvar"] = "pesrem$i";	  
	          
      $formulario -> texto ("","text","bulrem[$i]\" id=\"bulrem$i",0,5,5,"",$bulrem[$i]);
      $formulario -> texto ("","text","pesrem[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onClick=\"validarflete(".$i.")\" onChange=\"if(form_insert.bulrem$i.value == ''){form_insert.bulrem$i.value = (this.value * 20);}\" id=\"pesrem$i",0,5,5,"",$pesrem[$i]);    
      $formulario -> texto ("","text","fleuni[$i]\" id=\"fleunirem$i\" readonly ",0,5,5,"",$liremdes[$i][9]);      
      $formulario -> texto ("","text","tabfle[$i]\" id=\"tabflerem$i\" readonly ",0,10,30,"",$liremdes[$i][10],"$i;4",3193,NULL,0,$matpopup,$idelement);
      $formulario -> oculto("codfle[$i]\" id=\"codflerem$i",$codfle[$i],0);
      $formulario -> texto ("","text","refrem[$i]",0,10,10,"",$refrem[$i]);
      $formulario -> texto ("","text","pedrem[$i]",1,10,10,"",$pedrem[$i]);      
     }

	 $formulario -> nueva_tabla();
     $formulario -> botoni("Otro","form_insert.maxrem.value++; form_insert.submit();",1);

     $formulario -> oculto("maxrem",$GLOBALS[maxrem],0);
    }

    $formulario -> nueva_tabla();

    if($desurb)
     $formulario -> oculto("desurb",1,0);
    else
     $formulario -> oculto("desurb",0,0);

    $formulario -> oculto("transpor",$transpor[0][0],0);
    $formulario -> oculto("mrevmec",sizeof($revmec),0);
    $formulario -> oculto("frevmec",$revmec[0][0],0);
    $formulario -> oculto("mvlicondu",sizeof($vlicondu),0);
    $formulario -> oculto("fvlicondu",$vlicondu[0][0],0);
    $formulario -> oculto("nconduc",$vlicondu[0][1],0);
    $formulario -> oculto("parvalol",$pardec[0],0);
    $formulario -> oculto("usuario","$usuario",0);

    if(!$menerrtercer)
     $formulario -> botoni("Aceptar","aceptar(document.form_insert, 'Esta Seguro de Actualizar el Despacho?', 4)",0);
    else
     $formulario -> linea($menerrtercer,1,"e");
   }

   $formulario -> oculto("numdespac",$GLOBALS[numdespac],0);
   $formulario -> oculto("recdesp",$GLOBALS[recdesp],0);
   $formulario -> oculto("manredes",$manredes,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",3,0);
   $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
   $formulario -> cerrar();
  }

  function Actualizar()
 {
   $fec_actual = date("Y-m-d H:i:s");
   $hor_actual = date("H:i:s");

   $trayec = $GLOBALS[trayec];
   $GLOBALS[fecman]= $GLOBALS[fecman]." ".$hor_actual;

   if(!$GLOBALS[generador])
    $GLOBALS[generador] = "NULL";
   else
    $GLOBALS[generador] = "'".$GLOBALS[generador]."'";

   if(!$GLOBALS[l_trayle])
    $GLOBALS[l_trayle] = "null";
   else
    $GLOBALS[l_trayle] = "'".$GLOBALS[l_trayle]."'";

    if(!$GLOBALS[pesoxx])
     $GLOBALS[pesoxx] = 0;
     
    //jorge
    if(!$GLOBALS[carava])
			$GLOBALS[carava] = 0;

    $query = "SELECT a.cod_paisxx,a.cod_depart
    		    FROM ".BASE_DATOS.".tab_genera_ciudad a
    		   WHERE a.cod_ciudad = ".$GLOBALS[ciuori]."
    		 ";

    $consulta = new Consulta($query, $this -> conexion);
    $paidepori = $consulta -> ret_matriz();

    $query = "SELECT a.cod_paisxx,a.cod_depart
    		    FROM ".BASE_DATOS.".tab_genera_ciudad a
    		   WHERE a.cod_ciudad = ".$GLOBALS[ciudes]."
    		 ";

    $consulta = new Consulta($query, $this -> conexion);
    $paidepdes = $consulta -> ret_matriz();

    $GLOBALS[valdec] = str_replace('.','',$GLOBALS[valdec]);
    $GLOBALS[flete] = str_replace('.','',$GLOBALS[flete]);
    $GLOBALS[antici] = str_replace('.','',$GLOBALS[antici]);
    $GLOBALS[despac] = str_replace('.','',$GLOBALS[despac]);
    $GLOBALS["asegur"] = $GLOBALS["asegur"] ? "'".$GLOBALS["asegur"]."'" : 'NULL' ;
    //query de actualizaciÃ³n de despachos jorge
    $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                 SET cod_manifi = '$GLOBALS[manifi]',
              	     fec_despac = '$GLOBALS[fecman]',
              	     cod_client = ".$GLOBALS[generador].",
              	     cod_paiori = ".$paidepori[0][0].",
              	     cod_depori = ".$paidepori[0][1].",
              	     cod_ciuori = ".$GLOBALS[ciuori].",
              	     cod_paides = ".$paidepdes[0][0].",
              	     cod_depdes = ".$paidepdes[0][1].",
              	     cod_ciudes = ".$GLOBALS[ciudes].",
              	     val_flecon = NULL,
              	     val_despac = NULL,
              	     val_antici = NULL,
              	     val_retefu = NULL,
              	     nom_carpag = NULL,
              	     nom_despag = NULL,
              	     cod_agedes = '$GLOBALS[agencia]',
              	     fec_pagoxx = NULL,
              	     val_pesoxx = ".$GLOBALS[pesoxx].",
              	     num_carava = '".$GLOBALS[carava]."',
              	     obs_despac = '$GLOBALS[obsgrl]',
              	     val_declara = '$GLOBALS[valdec]',
                     cod_asegur = ".$GLOBALS[asegur].",
                     num_poliza = '$GLOBALS[poliza]',
              	     usr_modifi = '$GLOBALS[usuario]',
              	     fec_modifi = '$fec_actual',
                     gps_operad = '".$GLOBALS[gps_operad]."',
                     gps_usuari = '".$GLOBALS[gps_usuari]."',
                     gps_operad = '".$GLOBALS[gps_operad]."'
               WHERE num_despac = ".$GLOBALS[numdespac]."
              ";

    $consulta = new Consulta($query, $this -> conexion, "BR");
    //jorge quite los campos de gps y los coloque en despac_despac
    $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
             	 SET cod_transp = '$GLOBALS[transp]',
              	     cod_agenci = '$GLOBALS[agencia]',
              	     cod_rutasx = '$GLOBALS[ruta]',
              	     cod_conduc = '$GLOBALS[conduc]',
              	     num_placax = '$GLOBALS[placa]',
              	     num_trayle = ".$GLOBALS[l_trayle].",
              	     obs_medcom = '$GLOBALS[medcom]',
              	     usr_modifi = '$GLOBALS[usuario]',
              	     fec_modifi = '$fec_actual'
               WHERE num_despac = ".$GLOBALS[numdespac]."
            ";


    $consulta = new Consulta($query, $this -> conexion, "R");

    if($GLOBALS[manredes])
    {
     $docrem = $GLOBALS[docrem];
     $obsrem = $GLOBALS[obsrem];
     $nomrem = $GLOBALS[nomrem];
     $ciurem = $GLOBALS[ciurem];
     $dirrem = $GLOBALS[dirrem];
     $pesrem = $GLOBALS[pesrem];
     $bulrem = $GLOBALS[bulrem];
     $remsel = $GLOBALS[remsel];
     $refrem = $GLOBALS[refrem];
     $pedrem = $GLOBALS[pedrem];
     $contel = $GLOBALS[contel];
     $conlon = $GLOBALS[conlon];
     $conlat = $GLOBALS[conlat];
     $codfle = $GLOBALS[codfle];

     if(sizeof($remsel))
     {
     for($i = 0; $i < $GLOBALS[maxrem]; $i++)
     {
      eval("\$existvalucod = \$GLOBALS[codrem".$i."];");

      if(!$existvalucod)
       $existvalucod = "n";

      $query = "SELECT MAX(a.cod_remdes)
      		      FROM ".BASE_DATOS.".tab_genera_remdes a
      		   ";

      $consulta = new Consulta($query, $this -> conexion);
      $consecut = $consulta -> ret_matriz();
      $consecut[0][0]++;

      $query = "SELECT a.cod_paisxx,a.cod_depart
      		      FROM ".BASE_DATOS.".tab_genera_ciudad a
      		     WHERE a.cod_ciudad = ".$ciurem[$i]."
      		   ";

      $consulta = new Consulta($query, $this -> conexion);
      $paisdept = $consulta -> ret_matriz();

      if(!$obsrem[$i])
       $obsrem[$i] = "NULL";
      else
       $obsrem[$i] = "'".$obsrem[$i]."'";

      if(!$refrem[$i])
       $refrem[$i] = "NULL";
      else
       $refrem[$i] = "'".$refrem[$i]."'";

      if(!$pedrem[$i])
       $pedrem[$i] = "NULL";
      else
       $pedrem[$i] = "'".$pedrem[$i]."'";

      if(!$pesrem[$i])
       $pesrem[$i] = "NULL";
      else
       $pesrem[$i] = "'".$pesrem[$i]."'";
       
      if(!$bulrem[$i])
       $bulrem[$i] = "NULL";
      else
       $bulrem[$i] = "'".$bulrem[$i]."'";

      if(!$dirrem[$i])
       $dirrem[$i] = "NULL";
      else
       $dirrem[$i] = "'".$dirrem[$i]."'";

      if(!$conlon[$i])
       $conlon[$i] = "NULL";
      else
       $conlon[$i] = "'".$conlon[$i]."'";

      if(!$conlat[$i])
       $conlat[$i] = "NULL";
      else
       $conlat[$i] = "'".$conlat[$i]."'";

      if($remsel[$i] && $existvalucod == "n")
      {
       $query = "INSERT INTO ".BASE_DATOS.".tab_genera_remdes
       		                 (cod_remdes,num_remdes,nom_remdes,obs_adicio,
       		    			  cod_transp,ind_remdes,ind_estado,usr_creaci,
       		    			  fec_creaci)
       		    	VALUES   (".$consecut[0][0].",'".$docrem[$i]."','".$nomrem[$i]."',".$obsrem[$i].",
       		    			  '".$GLOBALS[transp]."','2','".COD_ESTADO_ACTIVO."','".$GLOBALS[usuario]."',
       		    			  '".$fec_actual."')
       		    ";

       $coddes = $consecut[0][0];
      }
      else if($remsel[$i] && $existvalucod != "n")
      {
       $query = "UPDATE ".BASE_DATOS.".tab_genera_remdes
       		        SET num_remdes = '".$docrem[$i]."',
       		            nom_remdes = '".$nomrem[$i]."',
       		            obs_adicio = ".$obsrem[$i].",
       		            usr_modifi = '".$GLOBALS[usuario]."',
       		            fec_modifi = '".$fec_actual."'
       		      WHERE cod_remdes = ".$existvalucod."
       		    ";

       $coddes = $existvalucod;
      }

      $consulta = new Consulta($query, $this -> conexion, "R");

      $query = "SELECT MAX(a.cod_contro)
	 		      FROM ".BASE_DATOS.".tab_genera_contro a
	             WHERE cod_contro != ".CONS_CODIGO_PCLLEG."
	 		   ";

	  $consulta = new Consulta($query, $this -> conexion);
      $consecut_con = $consulta -> ret_matriz();
      $consecut_con[0][0]++;

      if($GLOBALS[desurb] && ($remsel[$i] && $existvalucod == "n"))
      {
       $query = "INSERT INTO ".BASE_DATOS.".tab_genera_contro
     		                 (cod_contro,nom_contro,cod_ciudad,nom_encarg,
     		                  dir_contro,tel_contro,val_longit,val_latitu,
     		                  ind_urbano,usr_creaci,fec_creaci
     		                 )
     		          VALUES (".$consecut_con[0][0].",'".$nomrem[$i]."',".$ciurem[$i].",'".$nomrem[$i]."',
     		                  ".$dirrem[$i].",'".$contel[$i]."',".$conlon[$i].",".$conlat[$i].",
     		                  '".COD_ESTADO_ACTIVO."','".$GLOBALS[usuario]."','".$fec_actual."')
     		  ";

       $consulta = new Consulta($query, $this -> conexion,"R");

       $query = "INSERT INTO ".BASE_DATOS.".tab_destin_contro
     		               (cod_remdes,cod_contro)
     		        VALUES (".$coddes.",".$consecut_con[0][0].")
     		  ";

       $consulta = new Consulta($query, $this -> conexion,"R");
      }
      else
      {
       $query = "SELECT cod_contro
       		       FROM ".BASE_DATOS.".tab_destin_contro
       		      WHERE cod_remdes = ".$coddes."
       		    ";

       $consulta = new Consulta($query, $this -> conexion);
       $codcondes = $consulta -> ret_matriz();

       if($codcondes)
       {
        $query = "UPDATE ".BASE_DATOS.".tab_genera_contro
       		         SET nom_contro = '".$nomrem[$i]."',
       		             cod_ciudad = ".$ciurem[$i].",
       		             nom_encarg = '".$nomrem[$i]."',
       		             dir_contro = ".$dirrem[$i].",
       		             tel_contro = '".$contel[$i]."',
       		             val_longit = ".$conlon[$i].",
       		             val_latitu = ".$conlat[$i].",
       		             usr_modifi = '".$GLOBALS[usuario]."',
       		             fec_modifi = '".$fec_actual."'
       		       WHERE cod_contro = ".$codcondes[0][0]."
       		     ";

       	$consulta = new Consulta($query, $this -> conexion,"R");

       	$query = "INSERT INTO ".BASE_DATOS.".tab_destin_contro
     		               (cod_remdes,cod_contro)
     		        VALUES (".$coddes.",".$codcondes[0][0].")
     		  ";

        $consulta = new Consulta($query, $this -> conexion,"R");
       }
       else
       {
        $query = "INSERT INTO ".BASE_DATOS.".tab_genera_contro
     		                 (cod_contro,nom_contro,cod_ciudad,nom_encarg,
     		                  dir_contro,tel_contro,val_longit,val_latitu,
     		                  ind_urbano,usr_creaci,fec_creaci
     		                 )
     		          VALUES (".$consecut_con[0][0].",'".$nomrem[$i]."',".$ciurem[$i].",'".$nomrem[$i]."',
     		                  ".$dirrem[$i].",'".$contel[$i]."','".$conlon[$i]."','".$conlat[$i]."',
     		                  '".COD_ESTADO_ACTIVO."','".$GLOBALS[usuario]."','".$fec_actual."')
     		  ";

     	$consulta = new Consulta($query, $this -> conexion,"R");

        $query = "INSERT INTO ".BASE_DATOS.".tab_destin_contro
     		                  (cod_remdes,cod_contro)
     		           VALUES (".$coddes.",".$consecut_con[0][0].")
     		  ";

        $consulta = new Consulta($query, $this -> conexion,"R");
       }
      }

      if($remsel[$i])
      {
       $query = "SELECT a.num_despac,a.cod_remdes
       		       FROM ".BASE_DATOS.".tab_despac_remdes a
       		      WHERE a.num_despac = ".$GLOBALS[numdespac]." AND
       		    		a.cod_remdes = ".$coddes."
       		    ";

       $consulta = new Consulta($query, $this -> conexion);
       $existreldes = $consulta -> ret_matriz();

       if(!$existreldes)
        $query = "INSERT INTO ".BASE_DATOS.".tab_despac_remdes
       		    			  (num_despac, cod_remdes, num_docume, num_pedido,
       		    			   val_bultox, val_pesoxx, cod_paisxx, cod_depart,
       		    			   cod_ciudad, dir_destin, obs_adicio, cod_tabfle)
       		    	 VALUES   (".$GLOBALS[numdespac].", ".$coddes.", ".$refrem[$i].", ".$pedrem[$i].",
       		    			   ".$bulrem[$i].", ".$pesrem[$i].", ".$paisdept[0][0].", ".$paisdept[0][1].",
       		    			   ".$ciurem[$i].", ".$dirrem[$i].", ".$obsrem[$i].",".$codfle[$i].")
       		     ";
       else
        $query = "UPDATE ".BASE_DATOS.".tab_despac_remdes
       		    	 SET num_docume = ".$refrem[$i].",
       		    	     num_pedido = ".$pedrem[$i].",
       		    	     val_pesoxx = ".$pesrem[$i].",
       		    	     val_bultox = ".$bulrem[$i].",
       		    	     cod_paisxx = ".$paisdept[0][0].",
       		    	     cod_depart = ".$paisdept[0][1].",
       		    	     cod_ciudad = ".$ciurem[$i].",
       		    	     dir_destin = ".$dirrem[$i].",
       		    	     obs_adicio = ".$obsrem[$i].",
       		    	     cod_tabfle = ".$codfle[$i]."
       		       WHERE num_despac = ".$GLOBALS[numdespac]." AND
       		    	     cod_remdes = ".$coddes."
        		 ";

       $consulta = new Consulta($query, $this -> conexion, "R");
      }
     }
     }
    }

    # actualiza los datos de viaje en caso de que exista
    if( $_POST["viaje"] )
    {
      $mUpdate = " UPDATE ".BASE_DATOS.".tab_despac_viajex 
                      SET num_viajex = 'VJ-{$_POST[viaje]}',
                          usr_modifi = '{$GLOBALS[usuario]}',
                          fec_modifi = '{$fec_actual}'
                    WHERE num_despac = '{$GLOBALS[numdespac]}'  ";
       
      $consulta = new Consulta($mUpdate, $this -> conexion, "R");
    }

    if($insercion = new Consulta("COMMIT", $this -> conexion))
    {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Actualizar Otro Despacho</a></b>";

     $mensaje =  "El Despacho # <b>".$GLOBALS[numdespac]."</b> Se Actualizo con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("ACTUALIZAR DESPACHOS",$mensaje);
    }
 }

}//FIN CLASE Proc_despac

     $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>