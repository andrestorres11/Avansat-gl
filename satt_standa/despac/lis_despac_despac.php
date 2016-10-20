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
  if(!isset($_REQUEST[opcion]))
    $this -> Buscar();
  else
     {
      switch($_REQUEST[opcion])
       {
        case "2":
          $this -> Resultado();
          break;
        case "3":
          $this -> Datos();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Buscar()
 {
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
    $transpor = $consulta -> ret_matriz();
    $transpor = array_merge($inicio,$transpor);

    $formulario -> lista("Transportadora","transp\" onChange=\"form_insert.submit()",$transpor,1);
   }
   $formulario -> radio("Despacho","fil",1,0,1);
   $formulario -> radio("Vehiculo","fil",4,0,1);
   $formulario -> radio("Activos","fil",2,0,0);
   $formulario -> texto ("","text","despac",1,10,6,"","");
   $formulario -> radio("Anulados","fil",3,0,1);
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
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> botoni("Buscar","form_list.submit()",0);
   $formulario -> cerrar();
 }

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $fechaini = $_REQUEST[fecini]." 00:00:00";
   $fechafin = $_REQUEST[fecfin]." 23:59:59";

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

  if($_REQUEST[b_ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[b_ciuori];
  if($_REQUEST[b_ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[b_ciudes];
  if($_REQUEST[transp])
   $query .= " AND b.cod_transp = ".$_REQUEST[transp];

  if($_REQUEST[ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori];
  if($_REQUEST[ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes];

  if($_REQUEST[manifi])
   $query .= " AND a.cod_manifi = '".$_REQUEST[manifi]."'";
  if($_REQUEST[numdes])
   $query .= " AND a.num_despac = '".$_REQUEST[numdes]."'";
  if($_REQUEST[vehicu])
   $query .= " AND b.num_placax = '".$_REQUEST[vehicu]."'";
  if($_REQUEST[trayle])
   $query .= " AND b.num_trayle = '".$_REQUEST[trayle]."'";

  if($_REQUEST[fil] == 1)
   $query .= " AND a.num_despac = '".$_REQUEST[despac]."'";
  else if($_REQUEST[fil] == 2)
   $query .= " AND a.ind_anulad = 'R'";
  else if($_REQUEST[fil] == 3)
   $query .= " AND a.ind_anulad = 'A'";
  else if($_REQUEST[fil] == 4)
   $query .= " AND b.num_placax = '".$_REQUEST[despac]."'";

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

  if($_REQUEST[ciuori])
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

  if($_REQUEST[b_ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[b_ciuori];
  if($_REQUEST[b_ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[b_ciudes];
  if($_REQUEST[transp])
   $query .= " AND b.cod_transp = ".$_REQUEST[transp];

  if($_REQUEST[ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori];
  if($_REQUEST[ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes];

  if($_REQUEST[manifi])
   $query .= " AND a.cod_manifi = '".$_REQUEST[manifi]."'";
  if($_REQUEST[numdes])
   $query .= " AND a.num_despac = '".$_REQUEST[numdes]."'";
  if($_REQUEST[vehicu])
   $query .= " AND b.num_placax = '".$_REQUEST[vehicu]."'";
  if($_REQUEST[trayle])
   $query .= " AND b.num_trayle = '".$_REQUEST[trayle]."'";

  if($_REQUEST[fil] == 1)
   $query .= " AND a.num_despac = '".$_REQUEST[despac]."'";
  else if($_REQUEST[fil] == 2)
   $query .= " AND a.ind_anulad = 'R'";
  else if($_REQUEST[fil] == 3)
   $query .= " AND a.ind_anulad = 'A'";
  else if($_REQUEST[fil] == 4)
   $query .= " AND b.num_placax = '".$_REQUEST[despac]."'";

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

  if($_REQUEST[ciudes])
   $destinos = array_merge($destinos,$todos);
  else
   $destinos = array_merge($titdes,$destinos);

   $query = "SELECT a.num_despac,a.cod_manifi,a.ind_anulad,a.cod_ciuori,
                    a.cod_ciudes,c.abr_tercer,b.num_placax,b.num_trayle,
                    d.abr_tercer
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_despac_vehige b,
                    ".BASE_DATOS.".tab_tercer_tercer c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.num_despac = b.num_despac AND
                    b.cod_transp = c.cod_tercer AND
                    b.cod_conduc = d.cod_tercer
            ";

  if($_REQUEST[b_ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[b_ciuori];
  if($_REQUEST[b_ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[b_ciudes];
  if($_REQUEST[transp])
   $query .= " AND b.cod_transp = ".$_REQUEST[transp];

  if($_REQUEST[ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori];
  if($_REQUEST[ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes];

  if($_REQUEST[manifi])
   $query .= " AND a.cod_manifi = '".$_REQUEST[manifi]."'";
  if($_REQUEST[numdes])
   $query .= " AND a.num_despac = '".$_REQUEST[numdes]."'";
  if($_REQUEST[vehicu])
   $query .= " AND b.num_placax = '".$_REQUEST[vehicu]."'";
  if($_REQUEST[trayle])
   $query .= " AND b.num_trayle = '".$_REQUEST[trayle]."'";

  if($_REQUEST[fil] == 1)
   $query .= " AND a.num_despac = '".$_REQUEST[despac]."'";
  else if($_REQUEST[fil] == 2)
   $query .= " AND a.ind_anulad = 'R'";
  else if($_REQUEST[fil] == 3)
   $query .= " AND a.ind_anulad = 'A'";
  else if($_REQUEST[fil] == 4)
   $query .= " AND b.num_placax = '".$_REQUEST[despac]."'";

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
  $matriz = $consec -> ret_matriz();

  $formulario = new Formulario ("index.php","post","LISTADO DE DESPACHOS","form_item");

  $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Despacho(s).",0,"t2");

  $formulario -> nueva_tabla();
  $formulario -> texto("Despacho","text","numdes\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()",0,6,6,"",$_REQUEST[numdes],"","",1);
  $formulario -> texto("Documento/Despacho","text","manifi\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()",0,7,7,"",$_REQUEST[manifi],"","",1);
  $formulario -> linea("Estado",0,"t");
  $formulario -> lista_titulo("","ciuori\" onChange=\"form_item.submit()",$origenes,0);
  $formulario -> lista_titulo("","ciudes\" onChange=\"form_item.submit()",$destinos,0);
  $formulario -> linea("Transportadora",0,"t");
  $formulario -> texto("Vehiculo","text","vehicu\" onChange=\"form_item.submit()",0,6,6,"",$_REQUEST[vehicu],"","",1);
  $formulario -> texto("Remolque","text","trayle\" onChange=\"form_item.submit()",0,6,6,"",$_REQUEST[trayle],"","",1);
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

    $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
    $ciudad_o = $objciud -> getSeleccCiudad($matriz[$i][3]);
    $ciudad_d = $objciud -> getSeleccCiudad($matriz[$i][4]);

    $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&despac=".$matriz[$i][0]."&opcion=3 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

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
   $formulario -> oculto("b_ciuori",$_REQUEST[b_ciuori],0);
   $formulario -> oculto("b_ciudes",$_REQUEST[b_ciudes],0);
   $formulario -> oculto("transp",$_REQUEST[transp],0);
   $formulario -> oculto("fil",$_REQUEST[fil],0);
   $formulario -> oculto("fecini",$_REQUEST[fecini],0);
   $formulario -> oculto("fecfin",$_REQUEST[fecfin],0);

   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }

 function Datos()
 {
   $query = "SELECT a.ind_remdes
  		      FROM ".BASE_DATOS.".tab_config_parame a
  		     WHERE a.ind_remdes = '1'
  		   ";

   $consulta = new Consulta($query, $this -> conexion);
   $manredes = $consulta -> ret_matriz();

   $query = "SELECT a.ind_desurb
  		       FROM ".BASE_DATOS.".tab_config_parame a
  		      WHERE a.ind_desurb = '1'
  		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $desurb = $consulta -> ret_matriz();

   //jorge
   $query = "SELECT a.num_despac,a.cod_manifi,c.nom_agenci,a.fec_despac,
               		a.cod_ciuori,a.cod_ciudes,d.nom_rutasx,e.abr_tercer,
               		a.val_declara,b.num_placax,g.nom_marcax,h.nom_lineax,
               		i.nom_colorx,f.ano_modelo,j.nom_carroc,k.num_config,
               		l.cod_tercer,l.abr_tercer,IF( a.con_telmov IS NULL OR a.con_telmov = '', l.num_telmov, a.con_telmov ),b.num_trayle,
               		a.val_flecon,a.val_despac,a.val_antici,a.val_retefu,
               		a.nom_carpag,a.nom_despag,a.fec_pagoxx,b.obs_medcom,
               		a.obs_despac,m.abr_tercer,a.val_pesoxx, UPPER( n.nom_tipdes ) AS nom_tipdes,
                  IF(a.num_carava='0','Sin Caravana',a.num_carava) as num_carava
               FROM ".BASE_DATOS.".tab_despac_vehige b,
               		".BASE_DATOS.".tab_genera_agenci c,
               		".BASE_DATOS.".tab_genera_rutasx d,
                  ".BASE_DATOS.".tab_despac_despac a LEFT JOIN
               		".BASE_DATOS.".tab_tercer_tercer e ON
               		a.cod_client = e.cod_tercer,
               		".BASE_DATOS.".tab_vehicu_vehicu f,
                 	".BASE_DATOS.".tab_genera_marcas g,
                 	".BASE_DATOS.".tab_vehige_lineas h,
                 	".BASE_DATOS.".tab_vehige_colore i,
                 	".BASE_DATOS.".tab_vehige_carroc j,
                 	".BASE_DATOS.".tab_vehige_config k,
                 	".BASE_DATOS.".tab_tercer_tercer l,
                 	".BASE_DATOS.".tab_tercer_tercer m,
                 	".BASE_DATOS.".tab_genera_tipdes n
              WHERE a.num_despac = b.num_despac AND
               		b.cod_agenci = c.cod_agenci AND
               		b.cod_rutasx = d.cod_rutasx AND
               		b.num_placax = f.num_placax AND
               		f.cod_marcax = g.cod_marcax AND
               		f.cod_marcax = h.cod_marcax AND
               		f.cod_lineax = h.cod_lineax AND
               		f.cod_colorX = i.cod_colorX AND
               		f.cod_carroc = j.cod_carroc AND
               		f.num_config = k.num_config AND
               		b.cod_conduc = l.cod_tercer AND
               		b.cod_transp = m.cod_tercer AND
               		a.cod_tipdes = n.cod_tipdes AND
               		a.num_despac = ".$_REQUEST[despac]."
            ";

   $consec = new Consulta($query, $this -> conexion);
   $matriz = $consec -> ret_matriz();

   $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
   $origen = $objciud -> getSeleccCiudad($matriz[0][4]);
   $destino = $objciud -> getSeleccCiudad($matriz[0][5]);

   if(!$matriz[0][7])
    $matriz[0][7] = "-";
   if(!$matriz[0][8])
    $matriz[0][8] = "0";

   $formulario = new Formulario ("index.php","post","DETALLE DEL DESPACHO","form_item");
   $formulario -> linea("Informaci&oacute;n General del Despacho",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Despacho",0,"t");
   $formulario -> linea($matriz[0][0],0,"i");
   $formulario -> linea("Documento/Despacho",0,"t");
   $formulario -> linea($matriz[0][1],1,"i");
   $formulario -> linea("Transportadora",0,"t");
   $formulario -> linea($matriz[0][29],0,"i");
   $formulario -> linea("Agencia",0,"t");
   $formulario -> linea($matriz[0][2],1,"i");
   $formulario -> linea("Generador",0,"t");
   $formulario -> linea($matriz[0][7],0,"i");
   $formulario -> linea("Fecha",0,"t");
   $formulario -> linea($matriz[0][3],1,"i");
   $formulario -> linea("Origen",0,"t");
   $formulario -> linea($origen[0][1],0,"i");
   $formulario -> linea("Destino",0,"t");
   $formulario -> linea($destino[0][1],1,"i");
   $formulario -> linea("Ruta",0,"t");
   $formulario -> linea($matriz[0][6],0,"i");
   $formulario -> linea("Valor Declarado",0,"t");
   $formulario -> linea("\$ ".number_format($matriz[0][8]),1,"i");
   $formulario -> linea("Peso",0,"t");
   $formulario -> linea($matriz[0][30]." (Tn)",0,"i");
   $formulario -> linea("Tipo Despacho",0,"t");
   $formulario -> linea($matriz[0][31],1,"i");
   $formulario -> linea("No. Caravana",0,"t");//jorge
   $formulario -> linea($matriz[0]['num_carava'],1,"i");//jorge

   $formulario -> nueva_tabla();
   $formulario -> linea("Informaci&oacute;n del Vehiculo",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Placa",0,"t");
   $formulario -> linea($matriz[0][9],0,"i");
   $formulario -> linea("Marca y L&iacute;nea",0,"t");
   $formulario -> linea($matriz[0][10]." :: ".$matriz[0][11],1,"i");
   $formulario -> linea("Color",0,"t");
   $formulario -> linea($matriz[0][12],0,"i");
   $formulario -> linea("Carroceria",0,"t");
   $formulario -> linea($matriz[0][14],1,"i");
   $formulario -> linea("Modelo",0,"t");
   $formulario -> linea($matriz[0][13],0,"i");
   $formulario -> linea("Configuraci&oacute;n",0,"t");
   $formulario -> linea($matriz[0][15],1,"i");
   $formulario -> linea("Remolque",0,"t");
   $formulario -> linea($matriz[0][19],0,"i");

   $formulario -> nueva_tabla();
   $formulario -> linea("Informaci&oacute;n del Conductor",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($matriz[0][17],1,"i");
   $formulario -> linea("Documento",0,"t");
   $formulario -> linea(number_format($matriz[0][16]),1,"i");
   $formulario -> linea("Tel&eacute;fono Celular",0,"t");
   $formulario -> linea($matriz[0][18],1,"i");

   $formulario -> nueva_tabla();
   $formulario -> linea("Informaci&oacute;n Adicional",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Medios de Comunicaci&oacute;n",0,"t","15%");
   $formulario -> linea($matriz[0][27],0,"i","35%");
   $formulario -> linea("Observaciones Generales",0,"t","15%");
   $formulario -> linea($matriz[0][28],0,"i","35%");

   if($manredes)
   {
   	$query = "SELECT a.num_remdes,a.nom_remdes,a.obs_adicio,b.cod_ciudad,
   			         b.dir_destin,b.val_pesoxx,b.num_docume,b.cod_remdes,
   			         b.num_pedido
   			    FROM ".BASE_DATOS.".tab_genera_remdes a,
   			         ".BASE_DATOS.".tab_despac_remdes b
   			   WHERE a.cod_remdes = b.cod_remdes AND
   			         a.ind_remdes = '2' AND
   			         b.num_despac = ".$_REQUEST[despac]."
   			 ";

   	$consulta = new Consulta($query, $this -> conexion);
    $liremdes = $consulta -> ret_matriz();

    $formulario -> nueva_tabla();
    $formulario -> linea("Selecci&oacute;n de Destinatarios",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("Documento/C&oacute;digo",0,"t");
    $formulario -> linea("Nombre",0,"t");
    $formulario -> linea("Observaciones",0,"t");
    $formulario -> linea("Ciudad",0,"t");
    $formulario -> linea("Direcci&oacute;n",0,"t");

    if($desurb)
    {
     $formulario -> linea("Telefono",0,"t");
     $formulario -> linea("Longitud",0,"t");
     $formulario -> linea("Latitud",0,"t");
    }

    $formulario -> linea("Peso (Tn)",0,"t");
    $formulario -> linea("Remisi&oacute;n",0,"t");
    $formulario -> linea("Pedido",1,"t");

    if($liremdes)
    {
     for($i = 0; $i < sizeof($liremdes); $i++)
     {
      $ciudestin = $objciud -> getSeleccCiudad($liremdes[$i][3]);

	  $formulario -> linea($liremdes[$i][0],0,"i");
	  $formulario -> linea($liremdes[$i][1],0,"i");
	  $formulario -> linea($liremdes[$i][2],0,"i");
	  $formulario -> linea($ciudestin[0][1],0,"i");
	  $formulario -> linea($liremdes[$i][4],0,"i");


      if($desurb)
      {
       $query = "SELECT a.tel_contro,a.val_longit,a.val_latitu
       		       FROM ".BASE_DATOS.".tab_genera_contro a,
       		            ".BASE_DATOS.".tab_destin_contro b
       		      WHERE a.cod_contro = b.cod_contro AND
       		            b.cod_remdes = ".$liremdes[$i][7]."
       		    ";

       $consulta = new Consulta($query, $this -> conexion);
       $descontr = $consulta -> ret_matriz();

       $formulario -> linea($descontr[0][0],0,"i");
       $formulario -> linea($descontr[0][1],0,"i");
       $formulario -> linea($descontr[0][2],0,"i");
      }

	  $formulario -> linea($liremdes[$i][5],0,"i");
	  $formulario -> linea($liremdes[$i][6],0,"i");
	  $formulario -> linea($liremdes[$i][8],1,"i");
     }
    }
    else
    {
     $formulario -> nueva_tabla();
     $formulario -> linea("No se Encontrar&oacute;n Destinatarios Relacionados al Despacho",1,"e");
    }
   }

   $formulario -> nueva_tabla();
   $formulario -> boton("Volver","button\" onClick=\"javascript:history.go(-1)",0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",3,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }

}//FIN CLASE Proc_despac

     $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>