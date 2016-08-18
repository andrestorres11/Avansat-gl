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
   $formulario -> caja("Filtrar por Fechas", "fil_fechas", "1" , 0, 1);
   
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
   $query .= " AND b.num_placax = '".$GLOBALS[despac]."'";
  
  if($GLOBALS[fil_fechas] == '1')
    $query .= " AND a.fec_salida BETWEEN '".$fechaini."' AND '".$fechafin."'";

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
   $query .= " AND b.num_placax = '".$GLOBALS[despac]."'";

  if($GLOBALS[fil_fechas] == '1')
    $query .= " AND a.fec_salida BETWEEN '".$fechaini."' AND '".$fechafin."'";

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
   $query .= " AND b.num_placax = '".$GLOBALS[despac]."'";

  if($GLOBALS[fil_fechas] == '1')
    $query .= " AND a.fec_salida BETWEEN '".$fechaini."' AND '".$fechafin."'";

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

   if(!$GLOBALS[recdesp])
   {
   //jorge a.num_carava, a.cod_tipdes
    $query = "SELECT a.num_despac,a.cod_manifi,b.cod_agenci,DATE_FORMAT(a.fec_despac,'%Y/%m/%d'),
               		 a.cod_ciuori,a.cod_ciudes,b.cod_rutasx,a.cod_client,
               		 a.val_declara,b.num_placax,b.num_trayle,a.val_flecon,
               		 a.val_despac,a.val_antici,a.val_retefu,a.nom_carpag,
               		 a.nom_despag,a.fec_pagoxx,b.obs_medcom,	a.obs_despac,
               		 b.cod_transp,b.cod_conduc,a.val_pesoxx,a.gps_operad,
                   a.gps_usuari,a.gps_paswor,a.cod_asegur, a.num_poliza, 
                   a.num_carava, a.cod_tipdes,a.tie_contra,a.obs_tiemod
                FROM ".BASE_DATOS.".tab_despac_despac a,
               		 ".BASE_DATOS.".tab_despac_vehige b              		 
               WHERE a.num_despac = b.num_despac AND
               		 a.num_despac = ".$GLOBALS[numdespac]."
             ";

    $consec = new Consulta($query, $this -> conexion);
    $matriz = $consec -> ret_matriz();

    $GLOBALS[transp] = $matriz[0][20];
    $GLOBALS[manifi] = $matriz[0][1];
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
    $GLOBALS[tie_seguim] = $matriz[0]['tie_contra'];
    $GLOBALS[obs_tiedes] = $matriz[0]['obs_tiemod'];
   }
    
    
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/despac.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/puntos.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/ins_tiempo_pordes.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","INSERTAR TIEMPO DEL DESPACHO","form_insert","","");

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
      $transpor = $consulta -> ret_matriz();
     }
    }

    $formulario -> nueva_tabla();
	$formulario -> texto("Núm. Despacho: ","text","despac\" readonly=\"readonly",0,15,0,"",$GLOBALS[numdespac],"","",1);
    $formulario -> texto("Transportadora: ","text","transp\" readonly=\"readonly",0,35,0,"",$transpor[0][1],"","",1);
	
	$formulario -> nueva_tabla();
	$formulario -> texto("Tiempo de Seguimiento: ","text","tie_seguim\" onkeypress= \"return soloNumeros(event);\"",3,3,3,"",$GLOBALS['tie_seguim'],"","",1);
	
	$formulario -> nueva_tabla();
	$formulario -> linea("Observaciones:",1,"");
	$formulario -> texto("","textarea","obs_tiedes\" ",0,70,4,"",$GLOBALS['obs_tiedes'],"","",1);
   }
   
   $formulario -> oculto("numdespac",$GLOBALS[numdespac],0);
   $formulario -> oculto("manredes",$manredes,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",4,0);
   $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],1);
   $formulario -> oculto("usuario","$usuario",0);
   
   $formulario -> botoni("Aceptar","Validar()",1);
   $formulario -> cerrar();
  }

  function Actualizar()
 {
    //query de actualizaciÃ³n de despachos jorge
    $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                 SET tie_contra = '".$GLOBALS['tie_seguim']."',
              	     ind_tiemod = '1',
              	     obs_tiemod = '".$GLOBALS['obs_tiedes']."',
              	     usr_modifi = '".$GLOBALS['usuario']."',
              	     fec_modifi = NOW()
               WHERE num_despac = ".$GLOBALS['numdespac'].";";
    $consulta = new Consulta($query, $this -> conexion, "BR");

    if($insercion = new Consulta("COMMIT", $this -> conexion))
    {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Actualizar Otro Tiempo de Seguimiento</a></b>";

     $mensaje =  "El Despacho # <b>".$GLOBALS[numdespac]."</b> Se Actualizo con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("TIEMPO DE SEGUIMIENTO",$mensaje);


     $query = "SELECT a.cod_manifi, b.num_placax, b.cod_transp 
				 FROM ".BASE_DATOS.".tab_despac_despac a, 
				      ".BASE_DATOS.".tab_despac_vehige b 
 		        WHERE a.num_despac = b.num_despac 
				  AND a.num_despac = '".$GLOBALS[numdespac]."'";

	 $consulta = new Consulta($query, $this -> conexion);
     $despac_data = $consulta -> ret_matriz('a');
	  
	 //Cambiamos el tiempo de Seguimiento en el SAT si tiene activa la Interfaz
	 //Se reporta la novedad a la aplicacion del cliente
     $query = "SELECT nom_operad, nom_usuari, clv_usuari 
                 FROM ".BASE_DATOS.".tab_interf_parame 
			    WHERE cod_operad = '50'
				  AND ind_estado = '1'
				  AND cod_transp = '".$despac_data[0]['cod_transp']."'";

     $consulta = new Consulta($query, $this -> conexion);
     $data = $consulta -> ret_matriz('a');
			
     //Ruta Web Service.
     if( $data )
     {
	   //CONSULTAR URL WSDL.
		$query = "SELECT a.url_webser	
		            FROM  ".BD_STANDA.".tab_genera_server a,
					           ".BASE_DATOS.".tab_transp_tipser b
				       WHERE a.cod_server = b.cod_server AND
				             b.cod_transp = '".$despac_data[0]['cod_transp']."' 
				    ORDER BY b.fec_creaci DESC ";

		 $consulta = new Consulta($query, $this -> conexion);
     $url_webser = $consulta -> ret_matriz('a');
		 $url_webser = $url_webser[0]['url_webser'];
		 
		   ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
			try
			{
				//echo $url_webser;
				$oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
        $mParams = array( "nom_usuari" => $data[0]['nom_usuari'], 
								          "pwd_clavex" => $data[0]['clv_usuari'], 
								          "nom_aplica" => $data[0]['nom_operad'], 
								          "num_manifi" => $despac_data[0]["cod_manifi"], 
								          "num_placax" => $despac_data[0]["num_placax"],
								          "tie_contra" => $GLOBALS['tie_seguim'],
								          "obs_tiemod" => $GLOBALS['obs_tiedes']
								                    );
       /*echo "<pre>";
        print_r( $mParams );
        echo "</pre>";*/
				//Métodos disponibles en el WS
				$mResult = $oSoapClient -> __call( 'setTiempoSeguim', $mParams );
        //echo "RESPUESTA=".$mResult;
        
				$mResult = explode("; ", $mResult);
				$mCodResp = explode(":", $mResult[0]);
				$mMsgResp = explode(":", $mResult[1]);

				if ("1000" != $mCodResp[1])
				{
					$error_ = $mMsgResp[1];
				}
			}
			catch( SoapFault $e )
			{
      echo "<pre>";
      print_r( $e );
      echo "</pre>";
				$error_ = $e -> getMessage();
			}
	  }
    }
 }

}//FIN CLASE Proc_despac

     $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>