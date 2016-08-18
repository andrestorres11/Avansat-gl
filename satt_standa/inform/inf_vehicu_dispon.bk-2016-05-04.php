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
        case "1":
          $this -> Listar();
          break;
      }
  }
 }

 function Buscar()
 {
  $datos_usuario = $this -> usuario -> retornar();

  $fec_actual = date("Y/m/d");

  $inicio[0][0] = 0;
  $inicio[0][1] = "-";

  $query = "SELECT j.cod_ciudad,CONCAT(j.nom_ciudad,' (',LEFT(k.nom_depart,4),') - ',LEFT(l.nom_paisxx,4))
              FROM ".BASE_DATOS.".tab_despac_despac a,
                   ".BASE_DATOS.".tab_despac_vehige d,
                   ".BASE_DATOS.".tab_vehicu_vehicu i,
                   ".BASE_DATOS.".tab_genera_ciudad j,
                   ".BASE_DATOS.".tab_genera_depart k,
                   ".BASE_DATOS.".tab_genera_paises l
             WHERE a.cod_ciudes = j.cod_ciudad AND
                   j.cod_depart = k.cod_depart AND
                   j.cod_paisxx = k.cod_paisxx AND
                   k.cod_paisxx = l.cod_paisxx AND
                   a.num_despac = d.num_despac AND
                   i.num_placax = d.num_placax AND
                   a.fec_salida Is Not Null AND
                   a.fec_salida <= NOW() AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S'
	   ";

  if($datos_usuario["cod_perfil"] == "")
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }
  else
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }

  $query = $query." GROUP BY 1 ORDER BY 2";

  $consulta = new Consulta($query, $this -> conexion);
  $ciudes = $consulta -> ret_matriz();

  $ciudes = array_merge($inicio,$ciudes);

  $formulario = new Formulario ("index.php","post","VEHICULOS DISPONIBLES","form_insert","","");
  $formulario -> linea("Ingrese los Criterios de Busqueda",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> lista("Ciudad Destino","ciudes",$ciudes,1);

  $formulario -> nueva_tabla();
  $formulario -> fecha_calendar("Fecha","fecbus","form_insert",$fec_actual,"yyyy/mm/dd",1);

  $formulario -> nueva_tabla();
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("opcion",1,0);
  $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
  $formulario -> botoni("Buscar","form_insert.submit()",1);
  $formulario -> cerrar();

 }

 function Listar()
 {
  $datos_usuario = $this -> usuario -> retornar();

  $GLOBALS[fecbus] = str_replace("/","-",$GLOBALS[fecbus]);

  $fechaadic = date("Y-m-d", strtotime("".$GLOBALS[fecbus]." +5 day"));
  $fechadism = date("Y-m-d", strtotime("".$GLOBALS[fecbus]." -5 day"));

  $query = "SELECT d.num_placax,CONCAT(j.nom_ciudad,' (',LEFT(k.nom_depart,4),') - ',LEFT(l.nom_paisxx,4)),
  		           CONCAT(m.nom_ciudad,' (',LEFT(n.nom_depart,4),') - ',LEFT(o.nom_paisxx,4)),
  		           e.abr_tercer,e.num_telmov,e.num_telef1,d.fec_llegpl
              FROM ".BASE_DATOS.".tab_despac_despac a,
                   ".BASE_DATOS.".tab_despac_vehige d,
                   ".BASE_DATOS.".tab_tercer_tercer e,
                   ".BASE_DATOS.".tab_vehicu_vehicu i,
                   ".BASE_DATOS.".tab_genera_ciudad j,
                   ".BASE_DATOS.".tab_genera_depart k,
                   ".BASE_DATOS.".tab_genera_paises l,
                   ".BASE_DATOS.".tab_genera_ciudad m,
                   ".BASE_DATOS.".tab_genera_depart n,
                   ".BASE_DATOS.".tab_genera_paises o
             WHERE d.cod_conduc = e.cod_tercer AND
             	   a.cod_ciuori = j.cod_ciudad AND
                   j.cod_depart = k.cod_depart AND
                   j.cod_paisxx = k.cod_paisxx AND
                   k.cod_paisxx = l.cod_paisxx AND
             	   a.cod_ciudes = m.cod_ciudad AND
                   m.cod_depart = n.cod_depart AND
                   m.cod_paisxx = n.cod_paisxx AND
                   n.cod_paisxx = o.cod_paisxx AND
                   a.num_despac = d.num_despac AND
                   i.num_placax = d.num_placax AND
                   a.fec_salida Is Not Null AND
                   a.fec_llegad Is Null AND
                   a.fec_salida <= NOW() AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S'
	   ";

  if($GLOBALS[ciudes])
   $query .= " AND a.cod_ciudes = ".$GLOBALS[ciudes]."";
  if($GLOBALS[fecbus])
   $query .= " AND d.fec_llegpl BETWEEN '".$GLOBALS[fecbus]." 00:00:00' AND '".$fechaadic." 23:59:59'";

  if($datos_usuario["cod_perfil"] == "")
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }
  else
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }

  $query = $query." GROUP BY 1";

  $consulta = new Consulta($query, $this -> conexion);
  $desruta = $consulta -> ret_matriz();

  $query = "SELECT d.num_placax,CONCAT(j.nom_ciudad,' (',LEFT(k.nom_depart,4),') - ',LEFT(l.nom_paisxx,4)),
  		           CONCAT(m.nom_ciudad,' (',LEFT(n.nom_depart,4),') - ',LEFT(o.nom_paisxx,4)),
  		           e.abr_tercer,e.num_telmov,e.num_telef1,a.fec_llegad
              FROM ".BASE_DATOS.".tab_despac_despac a,
                   ".BASE_DATOS.".tab_despac_vehige d,
                   ".BASE_DATOS.".tab_tercer_tercer e,
                   ".BASE_DATOS.".tab_vehicu_vehicu i,
                   ".BASE_DATOS.".tab_genera_ciudad j,
                   ".BASE_DATOS.".tab_genera_depart k,
                   ".BASE_DATOS.".tab_genera_paises l,
                   ".BASE_DATOS.".tab_genera_ciudad m,
                   ".BASE_DATOS.".tab_genera_depart n,
                   ".BASE_DATOS.".tab_genera_paises o
             WHERE d.cod_conduc = e.cod_tercer AND
             	   a.cod_ciuori = j.cod_ciudad AND
                   j.cod_depart = k.cod_depart AND
                   j.cod_paisxx = k.cod_paisxx AND
                   k.cod_paisxx = l.cod_paisxx AND
             	   a.cod_ciudes = m.cod_ciudad AND
                   m.cod_depart = n.cod_depart AND
                   m.cod_paisxx = n.cod_paisxx AND
                   n.cod_paisxx = o.cod_paisxx AND
                   a.num_despac = d.num_despac AND
                   i.num_placax = d.num_placax AND
                   a.fec_salida Is Not Null AND
                   a.fec_llegad Is not Null AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S'
	   ";

  if($GLOBALS[ciudes])
   $query .= " AND a.cod_ciudes = ".$GLOBALS[ciudes]."";
  if($GLOBALS[fecbus])
   $query .= " AND a.fec_llegad BETWEEN '".$fechadism." 00:00:00' AND '".$GLOBALS[fecbus]." 23:59:59'";

  if($datos_usuario["cod_perfil"] == "")
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }
  else
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }

  $query = $query." GROUP BY 1";

  $consulta = new Consulta($query, $this -> conexion);
  $desllega = $consulta -> ret_matriz();

  $formulario = new Formulario ("index.php","post","VEHICULOS DISPONIBLES","form_insert","","");
  $formulario -> linea(sizeof($desruta)." Vehiculo(s) en Ruta Con Llegada Planeada Desde ".$GLOBALS[fecbus]." Hasta ".$fechaadic."",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Placa",0,"t");
  $formulario -> linea("Origen",0,"t");
  $formulario -> linea("Destino",0,"t");
  $formulario -> linea("Conductor",0,"t");
  $formulario -> linea("Celular",0,"t");
  $formulario -> linea("Telefono",0,"t");
  $formulario -> linea("Llegada Planeada",1,"t");

  for($i = 0; $i < sizeof($desruta); $i++)
  {
   $formulario -> linea($desruta[$i][0],0,"i");
   $formulario -> linea($desruta[$i][1],0,"i");
   $formulario -> linea($desruta[$i][2],0,"i");
   $formulario -> linea($desruta[$i][3],0,"i");
   $formulario -> linea($desruta[$i][4],0,"i");
   $formulario -> linea($desruta[$i][5],0,"i");
   $formulario -> linea($desruta[$i][6],1,"i");
  }

  $formulario -> nueva_tabla();
  $formulario -> linea(sizeof($desllega)." Vehiculo(s) Con Llegada Desde ".$fechadism." Hasta ".$GLOBALS[fecbus]."",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Placa",0,"t");
  $formulario -> linea("Origen",0,"t");
  $formulario -> linea("Destino",0,"t");
  $formulario -> linea("Conductor",0,"t");
  $formulario -> linea("Celular",0,"t");
  $formulario -> linea("Telefono",0,"t");
  $formulario -> linea("Llegada",1,"t");

  for($i = 0; $i < sizeof($desllega); $i++)
  {
   $formulario -> linea($desllega[$i][0],0,"i");
   $formulario -> linea($desllega[$i][1],0,"i");
   $formulario -> linea($desllega[$i][2],0,"i");
   $formulario -> linea($desllega[$i][3],0,"i");
   $formulario -> linea($desllega[$i][4],0,"i");
   $formulario -> linea($desllega[$i][5],0,"i");
   $formulario -> linea($desllega[$i][6],1,"i");
  }

  $formulario -> cerrar();
 }


 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_item");

   $mRuta = array("link"=>0, "finali"=>0, "opcurban"=>0, "lleg"=>NULL, "tie_ultnov"=>NULL);#Fabian
   $listado_prin = new Despachos($GLOBALS[cod_servic],2,$this -> aplica,$this -> conexion);
   $listado_prin  -> Encabezado($GLOBALS[despac],$datos_usuario,0,$mRuta);
   #$listado_prin  -> PlanDeRuta($GLOBALS[despac],$formulario,0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("despac",$GLOBALS[despac],0);
   $formulario -> oculto("opcion",$GLOBALS[opcion],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

   $formulario -> cerrar();
 }

}

$proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);



?>
