<?php
error_reporting("E_ERROR");

class Proc_despac
{

 var $conexion,
 	 $cod_aplica,
     $usuario,
     $paginador;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> paginador = new Paginador_listado($_REQUEST[opcion],$_REQUEST[cod_servic],$this -> conexion);
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
          $this -> Listar();
          break;
        default:
          $this -> Buscar();
          break;
      }
  }
 }

 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $inicio[0][0] = "0";
   $inicio[0][1] = "-";

   $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3))
  			   FROM ".BASE_DATOS.".tab_genera_ciudad a,
  			        ".BASE_DATOS.".tab_genera_depart b,
  			        ".BASE_DATOS.".tab_genera_paises c,
  			        ".BASE_DATOS.".tab_despac_despac d,
  			        ".BASE_DATOS.".tab_despac_vehige e,
  			        ".BASE_DATOS.".tab_vehicu_vehicu f
  			  WHERE a.cod_depart = b.cod_depart AND
  			        a.cod_paisxx = b.cod_paisxx AND
  			        b.cod_paisxx = c.cod_paisxx AND
  			        a.cod_ciudad = d.cod_ciuori AND
  			        a.cod_depart = d.cod_depori AND
  			        a.cod_paisxx = d.cod_paiori AND
  			        d.num_despac = e.num_despac AND
  			        e.num_placax = f.num_placax AND
  			        a.ind_estado = '1'
  		   ";

  if($datos_usuario["cod_perfil"] == "")
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }
  else
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }

   $query .= " GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $ciuori = $consulta -> ret_matriz();
   $ciuori = array_merge($inicio,$ciuori);

   $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3))
  			   FROM ".BASE_DATOS.".tab_genera_ciudad a,
  			        ".BASE_DATOS.".tab_genera_depart b,
  			        ".BASE_DATOS.".tab_genera_paises c,
  			        ".BASE_DATOS.".tab_despac_despac d,
  			        ".BASE_DATOS.".tab_despac_vehige e,
  			        ".BASE_DATOS.".tab_vehicu_vehicu f
  			  WHERE a.cod_depart = b.cod_depart AND
  			        a.cod_paisxx = b.cod_paisxx AND
  			        b.cod_paisxx = c.cod_paisxx AND
  			        a.cod_ciudad = d.cod_ciudes AND
  			        a.cod_depart = d.cod_depdes AND
  			        a.cod_paisxx = d.cod_paides AND
  			        d.num_despac = e.num_despac AND
  			        e.num_placax = f.num_placax AND
  			        a.ind_estado = '1'
  		   ";

  if($datos_usuario["cod_perfil"] == "")
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }
  else
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }

   $query .= " GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $ciudes = $consulta -> ret_matriz();
   $ciudes = array_merge($inicio,$ciudes);

   $query = "SELECT g.cod_tercer,g.abr_tercer
  			   FROM ".BASE_DATOS.".tab_genera_ciudad a,
  			        ".BASE_DATOS.".tab_genera_depart b,
  			        ".BASE_DATOS.".tab_genera_paises c,
  			        ".BASE_DATOS.".tab_despac_despac d,
  			        ".BASE_DATOS.".tab_despac_vehige e,
  			        ".BASE_DATOS.".tab_vehicu_vehicu f,
  			        ".BASE_DATOS.".tab_tercer_tercer g
  			  WHERE a.cod_depart = b.cod_depart AND
  			        a.cod_paisxx = b.cod_paisxx AND
  			        b.cod_paisxx = c.cod_paisxx AND
  			        a.cod_ciudad = d.cod_ciudes AND
  			        a.cod_depart = d.cod_depdes AND
  			        a.cod_paisxx = d.cod_paides AND
  			        d.num_despac = e.num_despac AND
  			        e.num_placax = f.num_placax AND
  			        e.cod_conduc = g.cod_tercer AND
  			        a.ind_estado = '1'
  		   ";

  if($datos_usuario["cod_perfil"] == "")
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }
  else
  {
   //PARA EL FILTRO DE CONDUCTOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_conduc = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE PROPIETARIO
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_propie = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE POSEEDOR
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND f.cod_tenedo = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE ASEGURADORA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_transp = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DEL CLIENTE
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND d.cod_client = '$datos_filtro[clv_filtro]' ";
   }
   //PARA EL FILTRO DE LA AGENCIA
   $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $query = $query . " AND e.cod_agenci = '$datos_filtro[clv_filtro]' ";
   }
  }

   $query .= " GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $conduc = $consulta -> ret_matriz();
   $conduc = array_merge($inicio,$conduc);

   $formulario = new Formulario ("index.php","post","TRAZABILIDAD DE DOCUMENTOS","form_list");

   $formulario -> nueva_tabla();
   $formulario -> linea("Especifique las Condiciones de B&uacute;squeda",1,"t2");

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

	$formulario -> nueva_tabla();
    $formulario -> lista("Transportadora","transp\" onChange=\"form_insert.submit()",$transpor,1);
   }

   $formulario -> nueva_tabla();
   $formulario -> texto("Despacho","text","despac",1,10,10,"","");
   $formulario -> texto("Nï¿½mero de Transporte","text","docume",1,10,10,"","");
   $formulario -> texto("Remisi&oacute;n","text","remisi",1,10,10,"","");
   $formulario -> texto("Pedido","text","pedido",1,10,10,"","");
   $formulario -> texto("Placa","text","placax",1,6,6,"","");

   $formulario -> lista("Conductor","conduc",$conduc,1);

   $formulario -> lista("Ciudad de Origen","ciuori",$ciuori,0);
   $formulario -> lista("Ciudad de Destino","ciudes",$ciudes,1);

   $formulario -> nueva_tabla();
   $formulario -> linea("Selecci&oacute;n Para el Rango de Fecha",1,"t2");

   $feactual = date("Y-m-d");

   $formulario -> nueva_tabla();
   $formulario -> fecha_calendar("Fecha Inicial","fecini","form_list",$feactual,"yyyy-mm-dd",0);
   $formulario -> fecha_calendar("Fecha Final","fecfin","form_list",$feactual,"yyyy-mm-dd",1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> botoni("Buscar","form_list.submit()",0);
   $formulario -> cerrar();
 }

 function Listar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);

   $fechaini = $_REQUEST[fecini]." 00:00:00";
   $fechafin = $_REQUEST[fecfin]." 23:59:59";

   $todos[0][0] = 0;
   $todos[0][1] = "Todos";
   $titori[0][0] = 0;
   $titori[0][1] = "Origen";
   $titdes[0][0] = 0;
   $titdes[0][1] = "Destino";
   $titcon[0][0] = 0;
   $titcon[0][1] = "Conductor";
   $tittra[0][0] = 0;
   $tittra[0][1] = "Transportadora";
   $titdesti[0][0] = 0;
   $titdesti[0][1] = "Desposito/Destinatario";

   $query = "SELECT k.cod_ciudad,CONCAT(k.abr_ciudad,' (',LEFT(l.abr_depart,4),') - ',LEFT(m.nom_paisxx,3))
               FROM ".BASE_DATOS.".tab_genera_ciudad k,
                    ".BASE_DATOS.".tab_genera_depart l,
                    ".BASE_DATOS.".tab_genera_paises m,
               		".BASE_DATOS.".tab_despac_vehige d,
                    ".BASE_DATOS.".tab_vehicu_vehicu i,
                    ".BASE_DATOS.".tab_despac_despac a LEFT JOIN
                    ".BASE_DATOS.".tab_despac_remdes j ON
                    a.num_despac = j.num_despac
              WHERE a.num_despac = d.num_despac AND
                    i.num_placax = d.num_placax AND
                    a.cod_ciuori = k.cod_ciudad AND
                    a.cod_depori = k.cod_depart AND
                    a.cod_paiori = k.cod_paisxx AND
                    k.cod_depart = l.cod_depart AND
                    k.cod_paisxx = l.cod_paisxx AND
                    l.cod_paisxx = m.cod_paisxx
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

  if($_REQUEST[bciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[bciuori]."";
  if($_REQUEST[bciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[bciudes]."";
  if($_REQUEST[bconduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[bconduc]."'";
  if($_REQUEST[btransp])
   $query .= " AND d.cod_transp = '".$_REQUEST[btransp]."'";
  if($_REQUEST[bdespac])
   $query .= " AND a.num_despac = ".$_REQUEST[bdespac]."";
  if($_REQUEST[bdocume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[bdocume]."'";
  if($_REQUEST[bremisi])
   $query .= " AND j.num_docume = '".$_REQUEST[bremisi]."'";
  if($_REQUEST[bpedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[bpedido]."'";
  if($_REQUEST[bplacax])
   $query .= " AND d.num_placax = '".$_REQUEST[bplacax]."'";
  if($_REQUEST[bdestinat])
   $query .= " AND j.cod_remdes = '".$_REQUEST[bdestinat]."'";

  if($_REQUEST[transp])
   $query .= " AND d.cod_transp = '".$_REQUEST[transp]."'";
  if($_REQUEST[despac])
   $query .= " AND a.num_despac = ".$_REQUEST[despac]."";
  if($_REQUEST[docume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[docume]."'";
  if($_REQUEST[remisi])
   $query .= " AND j.num_docume = '".$_REQUEST[remisi]."'";
  if($_REQUEST[pedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[pedido]."'";
  if($_REQUEST[placax])
   $query .= " AND d.num_placax = '".$_REQUEST[placax]."'";
  if($_REQUEST[conduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[conduc]."'";
  if($_REQUEST[ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
  if($_REQUEST[ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";

  $query .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";

  $query .=  " GROUP BY 1 ORDER BY 2";

  $consulta = new Consulta($query, $this -> conexion);
  $ciuori = $consulta -> ret_matriz();

  if($_REQUEST[bciuori])
   $ciuori = array_merge($ciuori,$todos);
  else
   $ciuori = array_merge($titori,$ciuori);

  $query = "SELECT k.cod_ciudad,CONCAT(k.abr_ciudad,' (',LEFT(l.abr_depart,4),') - ',LEFT(m.nom_paisxx,3))
               FROM ".BASE_DATOS.".tab_genera_ciudad k,
                    ".BASE_DATOS.".tab_genera_depart l,
                    ".BASE_DATOS.".tab_genera_paises m,
               		".BASE_DATOS.".tab_despac_vehige d,
                    ".BASE_DATOS.".tab_vehicu_vehicu i,
                    ".BASE_DATOS.".tab_despac_despac a LEFT JOIN
                    ".BASE_DATOS.".tab_despac_remdes j ON
                    a.num_despac = j.num_despac
              WHERE a.num_despac = d.num_despac AND
                    i.num_placax = d.num_placax AND
                    a.cod_ciudes = k.cod_ciudad AND
                    a.cod_depdes = k.cod_depart AND
                    a.cod_paides = k.cod_paisxx AND
                    k.cod_depart = l.cod_depart AND
                    k.cod_paisxx = l.cod_paisxx AND
                    l.cod_paisxx = m.cod_paisxx
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

  if($_REQUEST[bciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[bciuori]."";
  if($_REQUEST[bciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[bciudes]."";
  if($_REQUEST[bconduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[bconduc]."'";
  if($_REQUEST[btransp])
   $query .= " AND d.cod_transp = '".$_REQUEST[btransp]."'";
  if($_REQUEST[bdespac])
   $query .= " AND a.num_despac = ".$_REQUEST[bdespac]."";
  if($_REQUEST[bdocume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[bdocume]."'";
  if($_REQUEST[bremisi])
   $query .= " AND j.num_docume = '".$_REQUEST[bremisi]."'";
  if($_REQUEST[bpedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[bpedido]."'";
  if($_REQUEST[bplacax])
   $query .= " AND d.num_placax = '".$_REQUEST[bplacax]."'";
  if($_REQUEST[bdestinat])
   $query .= " AND j.cod_remdes = '".$_REQUEST[bdestinat]."'";

  if($_REQUEST[transp])
   $query .= " AND d.cod_transp = '".$_REQUEST[transp]."'";
  if($_REQUEST[despac])
   $query .= " AND a.num_despac = ".$_REQUEST[despac]."";
  if($_REQUEST[docume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[docume]."'";
  if($_REQUEST[remisi])
   $query .= " AND j.num_docume = '".$_REQUEST[remisi]."'";
  if($_REQUEST[pedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[pedido]."'";
  if($_REQUEST[placax])
   $query .= " AND d.num_placax = '".$_REQUEST[placax]."'";
  if($_REQUEST[conduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[conduc]."'";
  if($_REQUEST[ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
  if($_REQUEST[ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";

  $query .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";

  $query .=  " GROUP BY 1 ORDER BY 2";

  $consulta = new Consulta($query, $this -> conexion);
  $ciudes = $consulta -> ret_matriz();

  if($_REQUEST[bciudes])
   $ciudes = array_merge($ciudes,$todos);
  else
   $ciudes = array_merge($titdes,$ciudes);


   $query = "SELECT e.cod_tercer,e.abr_tercer
               FROM ".BASE_DATOS.".tab_despac_vehige d,
                    ".BASE_DATOS.".tab_tercer_tercer e,
                    ".BASE_DATOS.".tab_vehicu_vehicu i,
                    ".BASE_DATOS.".tab_despac_despac a LEFT JOIN
                    ".BASE_DATOS.".tab_despac_remdes j ON
                    a.num_despac = j.num_despac
              WHERE a.num_despac = d.num_despac AND
                    d.cod_conduc = e.cod_tercer AND
                    i.num_placax = d.num_placax
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

  if($_REQUEST[bciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[bciuori]."";
  if($_REQUEST[bciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[bciudes]."";
  if($_REQUEST[bconduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[bconduc]."'";
  if($_REQUEST[btransp])
   $query .= " AND d.cod_transp = '".$_REQUEST[btransp]."'";
  if($_REQUEST[bdespac])
   $query .= " AND a.num_despac = ".$_REQUEST[bdespac]."";
  if($_REQUEST[bdocume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[bdocume]."'";
  if($_REQUEST[bremisi])
   $query .= " AND j.num_docume = '".$_REQUEST[bremisi]."'";
  if($_REQUEST[bpedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[bpedido]."'";
  if($_REQUEST[bplacax])
   $query .= " AND d.num_placax = '".$_REQUEST[bplacax]."'";
  if($_REQUEST[bdestinat])
   $query .= " AND j.cod_remdes = '".$_REQUEST[bdestinat]."'";

  if($_REQUEST[transp])
   $query .= " AND d.cod_transp = '".$_REQUEST[transp]."'";
  if($_REQUEST[despac])
   $query .= " AND a.num_despac = ".$_REQUEST[despac]."";
  if($_REQUEST[docume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[docume]."'";
  if($_REQUEST[remisi])
   $query .= " AND j.num_docume = '".$_REQUEST[remisi]."'";
  if($_REQUEST[pedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[pedido]."'";
  if($_REQUEST[placax])
   $query .= " AND d.num_placax = '".$_REQUEST[placax]."'";
  if($_REQUEST[conduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[conduc]."'";
  if($_REQUEST[ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
  if($_REQUEST[ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";


  $query .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";

  $query .=  " GROUP BY 1 ORDER BY 2";

  $consulta = new Consulta($query, $this -> conexion);
  $conduc = $consulta -> ret_matriz();

  if($_REQUEST[bconduc])
   $conduc = array_merge($conduc,$todos);
  else
   $conduc = array_merge($titcon,$conduc);

   $query = "SELECT e.cod_tercer,e.abr_tercer
               FROM ".BASE_DATOS.".tab_despac_vehige d,
                    ".BASE_DATOS.".tab_tercer_tercer e,
                    ".BASE_DATOS.".tab_vehicu_vehicu i,
                    ".BASE_DATOS.".tab_despac_despac a LEFT JOIN
                    ".BASE_DATOS.".tab_despac_remdes j ON
                    a.num_despac = j.num_despac
              WHERE a.num_despac = d.num_despac AND
                    d.cod_transp = e.cod_tercer AND
                    i.num_placax = d.num_placax
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

  if($_REQUEST[bciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[bciuori]."";
  if($_REQUEST[bciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[bciudes]."";
  if($_REQUEST[bconduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[bconduc]."'";
  if($_REQUEST[btransp])
   $query .= " AND d.cod_transp = '".$_REQUEST[btransp]."'";
  if($_REQUEST[bdespac])
   $query .= " AND a.num_despac = ".$_REQUEST[bdespac]."";
  if($_REQUEST[bdocume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[bdocume]."'";
  if($_REQUEST[bremisi])
   $query .= " AND j.num_docume = '".$_REQUEST[bremisi]."'";
  if($_REQUEST[bpedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[bpedido]."'";
  if($_REQUEST[bplacax])
   $query .= " AND d.num_placax = '".$_REQUEST[bplacax]."'";
  if($_REQUEST[bdestinat])
   $query .= " AND j.cod_remdes = '".$_REQUEST[bdestinat]."'";

  if($_REQUEST[transp])
   $query .= " AND d.cod_transp = '".$_REQUEST[transp]."'";
  if($_REQUEST[despac])
   $query .= " AND a.num_despac = ".$_REQUEST[despac]."";
  if($_REQUEST[docume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[docume]."'";
  if($_REQUEST[remisi])
   $query .= " AND j.num_docume = '".$_REQUEST[remisi]."'";
  if($_REQUEST[pedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[pedido]."'";
  if($_REQUEST[placax])
   $query .= " AND d.num_placax = '".$_REQUEST[placax]."'";
  if($_REQUEST[conduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[conduc]."'";
  if($_REQUEST[ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
  if($_REQUEST[ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";


  $query .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";

  $query .=  " GROUP BY 1 ORDER BY 2";

  $consulta = new Consulta($query, $this -> conexion);
  $transp = $consulta -> ret_matriz();

  if($_REQUEST[btransp])
   $transp = array_merge($transp,$todos);
  else
   $transp = array_merge($tittra,$transp);

   $query = "SELECT k.cod_remdes,k.nom_remdes
               FROM ".BASE_DATOS.".tab_despac_vehige d,
                    ".BASE_DATOS.".tab_vehicu_vehicu i,
                    ".BASE_DATOS.".tab_despac_despac a LEFT JOIN
                    ".BASE_DATOS.".tab_despac_remdes j ON
                    a.num_despac = j.num_despac LEFT JOIN
                    ".BASE_DATOS.".tab_genera_remdes k ON
                    j.cod_remdes = k.cod_remdes
              WHERE a.num_despac = d.num_despac AND
                    i.num_placax = d.num_placax
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

  if($_REQUEST[bciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[bciuori]."";
  if($_REQUEST[bciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[bciudes]."";
  if($_REQUEST[bconduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[bconduc]."'";
  if($_REQUEST[btransp])
   $query .= " AND d.cod_transp = '".$_REQUEST[btransp]."'";
  if($_REQUEST[bdespac])
   $query .= " AND a.num_despac = ".$_REQUEST[bdespac]."";
  if($_REQUEST[bdocume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[bdocume]."'";
  if($_REQUEST[bremisi])
   $query .= " AND j.num_docume = '".$_REQUEST[bremisi]."'";
  if($_REQUEST[bpedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[bpedido]."'";
  if($_REQUEST[bplacax])
   $query .= " AND d.num_placax = '".$_REQUEST[bplacax]."'";
  if($_REQUEST[bdestinat])
   $query .= " AND j.cod_remdes = '".$_REQUEST[bdestinat]."'";

  if($_REQUEST[transp])
   $query .= " AND d.cod_transp = '".$_REQUEST[transp]."'";
  if($_REQUEST[despac])
   $query .= " AND a.num_despac = ".$_REQUEST[despac]."";
  if($_REQUEST[docume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[docume]."'";
  if($_REQUEST[remisi])
   $query .= " AND j.num_docume = '".$_REQUEST[remisi]."'";
  if($_REQUEST[pedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[pedido]."'";
  if($_REQUEST[placax])
   $query .= " AND d.num_placax = '".$_REQUEST[placax]."'";
  if($_REQUEST[conduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[conduc]."'";
  if($_REQUEST[ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
  if($_REQUEST[ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";


  $query .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";

  $query .= " GROUP BY 1 ORDER BY 2";

  $consulta = new Consulta($query, $this -> conexion);
  $destinat = $consulta -> ret_matriz();

  if($_REQUEST[bdestinat])
   $destinat = array_merge($destinat,$todos);
  else
   $destinat = array_merge($titdesti,$destinat);

   $query = "SELECT a.num_despac,a.cod_manifi,k.nom_remdes,j.val_pesoxx,
                    j.num_docume,j.num_pedido,a.cod_ciuori,j.cod_ciudad,
                    f.abr_tercer,d.num_placax,e.abr_tercer,
                    DATE_FORMAT(a.fec_despac,'%H:%i %d-%m-%Y'),
                    DATE_FORMAT(a.fec_salida,'%H:%i %d-%m-%Y'),
                    DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y'),
                    a.ind_anulad,j.cod_remdes,
                    if(m.ind_tonela = '".COD_ESTADO_ACTIVO."',m.val_costos / m.can_tonela,m.val_costos),
   			        if(m.ind_tonela = '".COD_ESTADO_ACTIVO."',(m.val_costos / m.can_tonela) * j.val_pesoxx,m.val_costos),
   			        if(j.cod_tabfle IS NOT NULL,l.nom_trayec,'-'),
   			        if(m.ind_tonela != '".COD_ESTADO_ACTIVO."',' (Viaje)',''),
   			        a.cod_ciudes, j.val_pesoxx, a.obs_llegad
               FROM ".BASE_DATOS.".tab_despac_vehige d,
                    ".BASE_DATOS.".tab_tercer_tercer e,
                    ".BASE_DATOS.".tab_tercer_tercer f,
                    ".BASE_DATOS.".tab_vehicu_vehicu i,
                    ".BASE_DATOS.".tab_despac_despac a LEFT JOIN
                    ".BASE_DATOS.".tab_despac_remdes j ON
                    a.num_despac = j.num_despac LEFT JOIN
                    ".BASE_DATOS.".tab_genera_remdes k ON
                    j.cod_remdes = k.cod_remdes LEFT JOIN
                    ".BASE_DATOS.".tab_tablax_fletes m ON
                    j.cod_tabfle = m.cod_consec LEFT JOIN
                    ".BASE_DATOS.".tab_genera_trayec l ON
                    m.cod_trayec = l.cod_trayec
              WHERE a.num_despac = d.num_despac AND
                    d.cod_conduc = e.cod_tercer AND
                    d.cod_transp = f.cod_tercer AND
                    k.cod_remdes = j.cod_remdes AND
                    i.num_placax = d.num_placax
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

  if($_REQUEST[bciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[bciuori]."";
  if($_REQUEST[bciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[bciudes]."";
  if($_REQUEST[bconduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[bconduc]."'";
  if($_REQUEST[btransp])
   $query .= " AND d.cod_transp = '".$_REQUEST[btransp]."'";
  if($_REQUEST[bdespac])
   $query .= " AND a.num_despac = ".$_REQUEST[bdespac]."";
  if($_REQUEST[bdocume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[bdocume]."'";
  if($_REQUEST[bremisi])
   $query .= " AND j.num_docume = '".$_REQUEST[bremisi]."'";
  if($_REQUEST[bpedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[bpedido]."'";
  if($_REQUEST[bplacax])
   $query .= " AND d.num_placax = '".$_REQUEST[bplacax]."'";
  if($_REQUEST[bdestinat])
   $query .= " AND j.cod_remdes = '".$_REQUEST[bdestinat]."'";

  if($_REQUEST[transp])
   $query .= " AND d.cod_transp = '".$_REQUEST[transp]."'";
  if($_REQUEST[despac])
   $query .= " AND a.num_despac = ".$_REQUEST[despac]."";
  if($_REQUEST[docume])
   $query .= " AND a.cod_manifi = '".$_REQUEST[docume]."'";
  if($_REQUEST[remisi])
   $query .= " AND j.num_docume = '".$_REQUEST[remisi]."'";
  if($_REQUEST[pedido])
   $query .= " AND j.num_pedido = '".$_REQUEST[pedido]."'";
  if($_REQUEST[placax])
   $query .= " AND d.num_placax = '".$_REQUEST[placax]."'";
  if($_REQUEST[conduc])
   $query .= " AND d.cod_conduc = '".$_REQUEST[conduc]."'";
  if($_REQUEST[ciuori])
   $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
  if($_REQUEST[ciudes])
   $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";


  $query .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";
  $query .=  " GROUP BY j.num_despac,j.cod_remdes ORDER BY 1,3,4";

  //echo $query;

  $despac = $this -> paginador -> ejecPaginador($_REQUEST[npagina],$query);

  $formulario = new Formulario ("index.php","post","Trazabilidad de Documentos","form_item");

  $query_exp = $this -> paginador -> query;
  $exp .= "&url=".NOM_URL_APLICA."&db=".BASE_DATOS."&fi=".$fechaini."&ff=".$fechafin."&eciuori=".$_REQUEST[bciuori]."&eciudes=".$_REQUEST[bciudes]."&econduc=".$_REQUEST[bconduc]."&etransp=".$_REQUEST[btransp]."&edespac=".$_REQUEST[bdespac]."&edocume=".$_REQUEST[bdocume]."&eremisi=".$_REQUEST[bremisi]."&epedido=".$_REQUEST[bpedido]."&eplacax=".$_REQUEST[bplacax]."&edestinat=".$_REQUEST[bdestinat]."&ciuori=".$_REQUEST[ciuori]."&ciudes=".$_REQUEST[ciudes]."&conduc=".$_REQUEST[conduc]."&transp=".$_REQUEST[transp]."&despac=".$_REQUEST[despac]."&docume=".$_REQUEST[docume]."&remisi=".$_REQUEST[remisi]."&pedido=".$_REQUEST[pedido]."&placax=".$_REQUEST[placax]."";
  $formulario -> nueva_tabla();
  $formulario -> imagen("Exportar","../".DIR_APLICA_CENTRAL."/imagenes/excel.jpg","Exportar",30,30,0,"onClick=\"top.window.open('../".DIR_APLICA_CENTRAL."/export/exp_trazab_docume.php?".$exp."')\"",1,0);

  $ind = 0;
  $varadici[$ind]["nomvar"] = "transp";
  $varadici[$ind]["valvar"] = $_REQUEST[transp];$ind++;
  $varadici[$ind]["nomvar"] = "bciuori";
  $varadici[$ind]["valvar"] = $_REQUEST[bciuori];$ind++;
  $varadici[$ind]["nomvar"] = "bciudes";
  $varadici[$ind]["valvar"] = $_REQUEST[bciudes];$ind++;
  $varadici[$ind]["nomvar"] = "bconduc";
  $varadici[$ind]["valvar"] = $_REQUEST[bconduc];$ind++;
  $varadici[$ind]["nomvar"] = "btransp";
  $varadici[$ind]["valvar"] = $_REQUEST[btransp];$ind++;
  $varadici[$ind]["nomvar"] = "bdespac";
  $varadici[$ind]["valvar"] = $_REQUEST[bdespac];$ind++;
  $varadici[$ind]["nomvar"] = "bdocume";
  $varadici[$ind]["valvar"] = $_REQUEST[bdocume];$ind++;
  $varadici[$ind]["nomvar"] = "bremisi";
  $varadici[$ind]["valvar"] = $_REQUEST[bremisis];$ind++;
  $varadici[$ind]["nomvar"] = "bpedido";
  $varadici[$ind]["valvar"] = $_REQUEST[bpedido];$ind++;
  $varadici[$ind]["nomvar"] = "bplacax";
  $varadici[$ind]["valvar"] = $_REQUEST[bplacax];$ind++;
  $varadici[$ind]["nomvar"] = "bdestinat";
  $varadici[$ind]["valvar"] = $_REQUEST[bdestinat];$ind++;
  $varadici[$ind]["nomvar"] = "transp";
  $varadici[$ind]["valvar"] = $_REQUEST[transp];$ind++;
  $varadici[$ind]["nomvar"] = "despac";
  $varadici[$ind]["valvar"] = $_REQUEST[despac];$ind++;
  $varadici[$ind]["nomvar"] = "docume";
  $varadici[$ind]["valvar"] = $_REQUEST[docume];$ind++;
  $varadici[$ind]["nomvar"] = "remisi";
  $varadici[$ind]["valvar"] = $_REQUEST[remisi];$ind++;
  $varadici[$ind]["nomvar"] = "pedido";
  $varadici[$ind]["valvar"] = $_REQUEST[pedido];$ind++;
  $varadici[$ind]["nomvar"] = "placax";
  $varadici[$ind]["valvar"] = $_REQUEST[placax];$ind++;
  $varadici[$ind]["nomvar"] = "conduc";
  $varadici[$ind]["valvar"] = $_REQUEST[conduc];$ind++;
  $varadici[$ind]["nomvar"] = "ciuori";
  $varadici[$ind]["valvar"] = $_REQUEST[ciuori];$ind++;
  $varadici[$ind]["nomvar"] = "ciudes";
  $varadici[$ind]["valvar"] = $_REQUEST[ciudes];$ind++;
  $varadici[$ind]["nomvar"] = "fecini";
  $varadici[$ind]["valvar"] = $fechaini;$ind++;
  $varadici[$ind]["nomvar"] = "fecfin";
  $varadici[$ind]["valvar"] = $fechafin;$ind++;

  $this -> paginador -> getPaginas( $formulario , "npagina" , $varadici);

  $formulario -> linea ("Se Encontro un Total de ".$this -> paginador -> totalr." Documento(s).",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> texto("Despacho","text","bdespac\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()",0,6,6,"",$_REQUEST[bdespac],"","",1);
  $formulario -> texto("Número de Transporte","text","bdocume\" onChange=\"form_item.submit()",0,7,7,"",$_REQUEST[bdocume],"","",1);
  $formulario -> lista_titulo("","bdestinat\" onChange=\"form_item.submit()",$destinat,0);
  $formulario -> texto("Remisi&oacute;n","text","bremisi\" onChange=\"form_item.submit()",0,10,10,"",$_REQUEST[bremisi],"","",1);
  $formulario -> texto("Pedido","text","bpedido\" onChange=\"form_item.submit()",0,10,10,"",$_REQUEST[bpedido],"","	",1);
  $formulario -> lista_titulo("","bciuori\" onChange=\"form_item.submit()",$ciuori,0);
  $formulario -> lista_titulo("","bciudes\" onChange=\"form_item.submit()",$ciudes,0);
  $formulario -> linea ("Peso (Tn)",0,"t");
  $formulario -> linea ("Bultos",0,"t");
  $formulario -> linea ("Valor (Unit)",0,"t");
  $formulario -> linea ("Valor Flete",0,"t");
  $formulario -> linea ("Trayecto",0,"t");
  $formulario -> lista_titulo("","btransp\" onChange=\"form_item.submit()",$transp,0);
  $formulario -> texto("Placa","text","bplacax\" onChange=\"form_item.submit()",0,6,6,"",$_REQUEST[bplacax],"","",1);
  $formulario -> lista_titulo("","bconduc\" onChange=\"form_item.submit()",$conduc,0);
  $formulario -> linea("Fecha/Despacho",0,"t");
  $formulario -> linea("Fecha/Salida",0,"t");
  $formulario -> linea("Fecha/Llegada",0,"t");
  $formulario -> linea("Observ. Llegada",0,"t");



  //Nuevas Filas.
  $formulario -> linea("Hora/Fecha Control Lugar de Entrega",0,"t");
  $formulario -> linea("Hora/Fecha Novedad Lugar de Entrega",0,"t");
  $formulario -> linea("Hora/Fecha Control Destinatario",0,"t");
  $formulario -> linea("Hora/Fecha Novedad Destinatario",0,"t");
  $formulario -> linea("Fecha Cumplido Fisico",0,"t");
  $formulario -> linea("Novedad Cumplido",0,"t");

  $formulario -> linea("Estado",1,"t");



  for($i = 0; $i < sizeof($despac); $i++)
  {
   $ciudad_o = $objciud -> getSeleccCiudad($despac[$i][6]);
   $ciudad_d = $objciud -> getSeleccCiudad($despac[$i][20]);

   if($despac[$i][14] == "R")
   {
   	$estado = "Activo";
   	$estilo = "i";
   }
   else if($despac[$i][12] == "A")
   {
   	$estado = "Anulado";
   	$estilo = "ie";
   }

	$fec_cumpli = "	SELECT a.fec_cumpli
  			 		FROM ".BASE_DATOS.".tab_despac_despac a
			 		WHERE num_despac = '".$despac[$i][0]."'
					ORDER BY 1 DESC";

	$fec_cumpli = new Consulta($fec_cumpli, $this -> conexion);
  	$fec_cumpli = $fec_cumpli -> ret_matriz();
	$fec_cumpli = $fec_cumpli[0][fec_cumpli];

	$fec_noveda = "SELECT e.fec_noveda,e.fec_creaci
					FROM
					".BASE_DATOS.".tab_despac_despac a,
					".BASE_DATOS.".tab_despac_remdes b,
					".BASE_DATOS.".tab_destin_contro c,
					".BASE_DATOS.".tab_despac_seguim d left join
					".BASE_DATOS.".tab_despac_noveda e on
					d.num_despac = e.num_despac and
					d.cod_rutasx = e.cod_rutasx and
					d.cod_contro = e.cod_contro
					WHERE
					a.num_despac = b.num_despac and
					b.cod_remdes = c.cod_remdes and
					a.num_despac = d.num_despac and
					c.cod_contro = d.cod_contro and
					a.num_despac = '".$despac[$i][0]."'
					GROUP BY a.num_despac ";// c.cod_contro = '".CONS_CODIGO_PCLLEG."' GROUP BY 1,2

	$fec_noveda = new Consulta($fec_noveda, $this -> conexion);
  	$fec_noveda = $fec_noveda -> ret_matriz(1);
	$fec_noveda = $fec_noveda[0];

	$nov_entreg = "SELECT c.fec_noveda,c.fec_creaci
					FROM
					".BASE_DATOS.".tab_despac_seguim b,
					".BASE_DATOS.".tab_despac_noveda c
					WHERE
					b.num_despac = c.num_despac and
					c.cod_contro = '".CONS_CODIGO_PCLLEG."' and
					b.num_despac = '".$despac[$i][0]."'
					GROUP BY 1 ";

	$nov_entreg = new Consulta($nov_entreg, $this -> conexion);
  	$nov_entreg = $nov_entreg -> ret_matriz(1);
	$nov_entreg = $nov_entreg[0];

	$mQuery = "SELECT a.nom_novcum
				 FROM ".BASE_DATOS.".tab_genera_novcum a,
					  ".BASE_DATOS.".tab_noveda_cumpli b
				WHERE a.cod_novcum = b.cod_novcum AND
					  b.num_despac = '".$despac[$i][0]."'
			  ";

	$mConsulta  = new Consulta( $mQuery, $this -> conexion );
  	$nov_cumpli = $mConsulta -> ret_matriz();
  	if ( $nov_cumpli[0][0] )
	 $nov_cumpli = $nov_cumpli[0][0];
	else
	 $nov_cumpli = "-";

   $formulario -> linea ($despac[$i][0],0,$estilo);
   $formulario -> linea ($despac[$i][1],0,$estilo);
   $formulario -> linea ($despac[$i][2],0,$estilo);
   $formulario -> linea ($despac[$i][4],0,$estilo);
   $formulario -> linea ($despac[$i][5],0,$estilo);
   $formulario -> linea ($ciudad_o[0][1],0,$estilo);
   $formulario -> linea ($ciudad_d[0][1],0,$estilo);
   $formulario -> linea ($despac[$i][3],0,$estilo);
   $formulario -> linea ($despac[$i][21],0,$estilo);
   $formulario -> linea ("<div align='right'>".number_format($despac[$i][16].$despac[$i][19],2,',','.')." \$"."</div>",0,$estilo);
   $formulario -> linea ("<div align='right'>".number_format($despac[$i][17],2,',','.')." \$"."</div>",0,$estilo);
   $formulario -> linea ($despac[$i][18],0,$estilo);
   $formulario -> linea ($despac[$i][8],0,$estilo);
   $formulario -> linea ($despac[$i][9],0,$estilo);
   $formulario -> linea ($despac[$i][10],0,$estilo);
   $formulario -> linea ($despac[$i][11],0,$estilo);
   $formulario -> linea ($despac[$i][12],0,$estilo);
   $formulario -> linea ($despac[$i][13],0,$estilo);
   $formulario -> linea ($despac[$i][22],0,$estilo);

   //Nuevas columnas.

   $formulario -> linea ($nov_entreg[fec_noveda],0,$estilo);
   $formulario -> linea ($nov_entreg[fec_creaci],0,$estilo);
   $formulario -> linea ($fec_noveda[fec_noveda],0,$estilo);
   $formulario -> linea ($fec_noveda[fec_creaci],0,$estilo);
   $formulario -> linea ($fec_cumpli,0,$estilo);
   $formulario -> linea ($nov_cumpli,0,$estilo);
   $formulario -> linea ($estado,1,$estilo);

  }

  $this -> paginador -> getPaginas($formulario,"npagina",$varadici);

  $formulario -> nueva_tabla();
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
  $formulario -> oculto("opcion",$_REQUEST[opcion],0);

  $formulario -> oculto("transp",$_REQUEST[transp],0);
  $formulario -> oculto("despac",$_REQUEST[despac],0);
  $formulario -> oculto("docume",$_REQUEST[docume],0);
  $formulario -> oculto("remisi",$_REQUEST[remisi],0);
  $formulario -> oculto("pedido",$_REQUEST[pedido],0);
  $formulario -> oculto("placax",$_REQUEST[placax],0);
  $formulario -> oculto("conduc",$_REQUEST[conduc],0);
  $formulario -> oculto("ciuori",$_REQUEST[ciuori],0);
  $formulario -> oculto("ciudes",$_REQUEST[ciudes],0);
  $formulario -> oculto("fecini",$_REQUEST[fecini],0);
  $formulario -> oculto("fecfin",$_REQUEST[fecfin],0);

  $formulario -> cerrar();
 }
}
$proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
