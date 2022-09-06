<?php
class Proc_fletes
{
 var $conexion,
 	 $cod_aplica,
     $paginador,
     $usuario;

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
          $this -> Datos();
          break;
        case "3":
          $this -> Insertar();
          break;
        case "4":
          $this -> listarFleteDespac();
          break;
        default:
          $this -> Buscar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $titori[0][0] = 0;
   $titori[0][1] = "Origen";
   $titdes[0][0] = 0;
   $titdes[0][1] = "Destino";
   $titcar[0][0] = 0;
   $titcar[0][1] = "Carroceria";
   $tittra[0][0] = 0;
   $tittra[0][1] = "Trayecto";
   $titpor[0][0] = 0;
   $titpor[0][1] = "Transportadora";

   $todos[0][0] = 0;
   $todos[0][1] = "Todos";

   $query = "SELECT b.cod_ciudad,CONCAT(b.abr_ciudad,' (',LEFT(c.abr_depart,4),') - ',LEFT(d.nom_paisxx,3))
               FROM ".BASE_DATOS.".tab_tablax_fletes a,
               		".BASE_DATOS.".tab_genera_ciudad b,
               		".BASE_DATOS.".tab_genera_depart c,
               		".BASE_DATOS.".tab_genera_paises d
              WHERE a.cod_ciuori = b.cod_ciudad AND
               		b.cod_depart = c.cod_depart AND
               		b.cod_paisxx = c.cod_paisxx AND
               		c.cod_paisxx = d.cod_paisxx
		    ";

   if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }

   if($_REQUEST[ciudes])
    $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";
   if($_REQUEST[ciuori])
    $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
   if($_REQUEST[carroc])
    $query .= " AND a.cod_carroc = ".$_REQUEST[carroc]."";
   if($_REQUEST[trayec])
    $query .= " AND a.cod_trayec = ".$_REQUEST[trayec]."";
   if($_REQUEST[transp])
    $query .= " AND a.cod_transp = ".$_REQUEST[transp]."";

   $query .= " GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $ciuori = $consulta -> ret_matriz();

   if($_REQUEST[ciuori])
    $ciuori = array_merge($ciuori,$todos);
   else
    $ciuori = array_merge($titori,$ciuori);

   $query = "SELECT b.cod_ciudad,CONCAT(b.abr_ciudad,' (',LEFT(c.abr_depart,4),') - ',LEFT(d.nom_paisxx,3))
               FROM ".BASE_DATOS.".tab_tablax_fletes a,
               		".BASE_DATOS.".tab_genera_ciudad b,
               		".BASE_DATOS.".tab_genera_depart c,
               		".BASE_DATOS.".tab_genera_paises d
              WHERE a.cod_ciudes = b.cod_ciudad AND
               		b.cod_depart = c.cod_depart AND
               		b.cod_paisxx = c.cod_paisxx AND
               		c.cod_paisxx = d.cod_paisxx
		    ";

   if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }

   if($_REQUEST[ciudes])
    $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";
   if($_REQUEST[ciuori])
    $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
   if($_REQUEST[carroc])
    $query .= " AND a.cod_carroc = ".$_REQUEST[carroc]."";
   if($_REQUEST[trayec])
    $query .= " AND a.cod_trayec = ".$_REQUEST[trayec]."";
   if($_REQUEST[transp])
    $query .= " AND a.cod_transp = ".$_REQUEST[transp]."";

   $query .= " GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $ciudes = $consulta -> ret_matriz();

   if($_REQUEST[ciudes])
    $ciudes = array_merge($ciudes,$todos);
   else
    $ciudes = array_merge($titdes,$ciudes);

   $query = "SELECT b.cod_carroc,b.nom_carroc
               FROM ".BASE_DATOS.".tab_tablax_fletes a,
               		".BASE_DATOS.".tab_vehige_carroc b
              WHERE a.cod_carroc = b.cod_carroc
		    ";

   if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }

   if($_REQUEST[ciudes])
    $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";
   if($_REQUEST[ciuori])
    $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
   if($_REQUEST[carroc])
    $query .= " AND a.cod_carroc = ".$_REQUEST[carroc]."";
   if($_REQUEST[trayec])
    $query .= " AND a.cod_trayec = ".$_REQUEST[trayec]."";
   if($_REQUEST[transp])
    $query .= " AND a.cod_transp = ".$_REQUEST[transp]."";

   $query .= " GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $carroc = $consulta -> ret_matriz();

   if($_REQUEST[carroc])
    $carroc = array_merge($carroc,$todos);
   else
    $carroc = array_merge($titcar,$carroc);

   $query = "SELECT b.cod_trayec,b.nom_trayec
               FROM ".BASE_DATOS.".tab_tablax_fletes a,
               		".BASE_DATOS.".tab_genera_trayec b
              WHERE a.cod_trayec = b.cod_trayec
		    ";

   if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }

   if($_REQUEST[ciudes])
    $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";
   if($_REQUEST[ciuori])
    $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
   if($_REQUEST[carroc])
    $query .= " AND a.cod_carroc = ".$_REQUEST[carroc]."";
   if($_REQUEST[trayec])
    $query .= " AND a.cod_trayec = ".$_REQUEST[trayec]."";
   if($_REQUEST[transp])
    $query .= " AND a.cod_transp = ".$_REQUEST[transp]."";

   $query .= " GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $trayec = $consulta -> ret_matriz();

   if($_REQUEST[trayec])
    $trayec = array_merge($trayec,$todos);
   else
    $trayec = array_merge($tittra,$trayec);

   $query = "SELECT b.cod_tercer,b.abr_tercer
               FROM ".BASE_DATOS.".tab_tablax_fletes a,
               		".BASE_DATOS.".tab_tercer_tercer b
              WHERE a.cod_transp = b.cod_tercer
		    ";

   if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }

   if($_REQUEST[ciudes])
    $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";
   if($_REQUEST[ciuori])
    $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
   if($_REQUEST[carroc])
    $query .= " AND a.cod_carroc = ".$_REQUEST[carroc]."";
   if($_REQUEST[trayec])
    $query .= " AND a.cod_trayec = ".$_REQUEST[trayec]."";
   if($_REQUEST[transp])
    $query .= " AND a.cod_transp = ".$_REQUEST[transp]."";

   $query .= " GROUP BY 1 ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $transp = $consulta -> ret_matriz();

   if($_REQUEST[transp])
    $transp = array_merge($transp,$todos);
   else
    $transp = array_merge($titpor,$transp);

   $query = "SELECT a.cod_consec,a.cod_ciuori,a.cod_ciudes,a.cod_carroc,
  				    a.cod_trayec,a.cod_transp,a.ind_tonela,a.cod_estado,
  				    a.val_costos
               FROM ".BASE_DATOS.".tab_tablax_fletes a
              WHERE 1
		    ";

   if($datos_usuario["cod_perfil"] == "")
   {
	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }
   else
   {
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
    }
   }

   if($_REQUEST[ciudes])
    $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes]."";
   if($_REQUEST[ciuori])
    $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori]."";
   if($_REQUEST[carroc])
    $query .= " AND a.cod_carroc = ".$_REQUEST[carroc]."";
   if($_REQUEST[trayec])
    $query .= " AND a.cod_trayec = ".$_REQUEST[trayec]."";
   if($_REQUEST[transp])
    $query .= " AND a.cod_transp = ".$_REQUEST[transp]."";

   $query .= " GROUP BY 1 ORDER BY 8,1";

   $matriz = $this -> paginador -> ejecPaginador($_REQUEST[npagina],$query);

   $query_exp = $query;

   $formulario = new Formulario ("index.php","post","Listado de Fletes","form_lis");

   $query_exp = base64_encode($query_exp);
   $exp .= "url=".NOM_URL_APLICA."&db=".BASE_DATOS."&query_exp=".$query_exp."";
   $formulario -> nueva_tabla();
   $formulario -> imagen("Exportar","../".DIR_APLICA_CENTRAL."/imagenes/excel.jpg","Exportar",30,30,0,"onClick=\"top.window.open('../".DIR_APLICA_CENTRAL."/export/exp_tabla_fletes.php?".$exp."')\"",1,0);

   $formulario -> nueva_tabla();
   $formulario -> linea("<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&opcion=2 \"target=\"centralFrame\">===> Insertar Nuevo Registro <===</a>",1,"h");

   $formulario -> nueva_tabla();
   $formulario -> linea("Se Encontro un Total de ".$this -> paginador -> totalr." Flete(s)",0,"t2");

   $i=0;
   $varadici[$i]["nomvar"] = "ciudes";
   $varadici[$i]["valvar"] = $_REQUEST[ciudes];$i++;
   $varadici[$i]["nomvar"] = "ciuori";
   $varadici[$i]["valvar"] = $_REQUEST[ciuori];$i++;
   $varadici[$i]["nomvar"] = "carroc";
   $varadici[$i]["valvar"] = $_REQUEST[carroc];$i++;
   $varadici[$i]["nomvar"] = "trayec";
   $varadici[$i]["valvar"] = $_REQUEST[trayec];$i++;
   $varadici[$i]["nomvar"] = "transp";
   $varadici[$i]["valvar"] = $_REQUEST[transp];$i++;

   $this -> paginador -> getPaginas($formulario,"npagina",$varadici);

   $formulario -> nueva_tabla();
   $formulario -> linea("Codigo",0,"t");
   $formulario -> lista_titulo("Origen","ciuori\" onChange=\"form_lis.submit()",$ciuori,0);
   $formulario -> lista_titulo("Destino","ciudes\" onChange=\"form_lis.submit()",$ciudes,0);
   $formulario -> lista_titulo("Carroceria","carroc\" onChange=\"form_lis.submit()",$carroc,0);
   $formulario -> lista_titulo("Trayecto","trayec\" onChange=\"form_lis.submit()",$trayec,0);
   $formulario -> lista_titulo("Transportadora","transp\" onChange=\"form_lis.submit()",$transp,0);
   $formulario -> linea("Por Tonelada",0,"t");
   $formulario -> linea("Valor (Unit)",0,"t");
   $formulario -> linea("Estado",1,"t");

   $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);

   for($i=0;$i<sizeof($matriz);$i++)
   {
	$query = "SELECT nom_carroc
             FROM ".BASE_DATOS.".tab_vehige_carroc
             WHERE cod_carroc = ".$matriz[$i][3]."
            ";

    $consulta = new Consulta($query, $this -> conexion);
    $carroc = $consulta -> ret_matriz();

    $query = "SELECT nom_trayec
             FROM ".BASE_DATOS.".tab_genera_trayec
             WHERE cod_trayec = ".$matriz[$i][4]."
            ";

    $consulta = new Consulta($query, $this -> conexion);
    $trayec = $consulta -> ret_matriz();

   	$query = "SELECT abr_tercer
             FROM ".BASE_DATOS.".tab_tercer_tercer
             WHERE cod_tercer = ".$matriz[$i][5]."
            ";

    $consulta = new Consulta($query, $this -> conexion);
    $transp = $consulta -> ret_matriz();

    $ciudad_o = $objciud -> getSeleccCiudad($matriz[$i][1]);
    $ciudad_d = $objciud -> getSeleccCiudad($matriz[$i][2]);

    if($matriz[$i][6] == COD_ESTADO_ACTIVO)
     $text_indica = "Si";
    else
     $text_indica = "No";

    if($matriz[$i][7] == COD_ESTADO_ACTIVO)
    {
     $text_estado = "Activo";
     $estilo = "i";
    }
    else
    {
     $text_estado = "Inactivo";
     $estilo = "ie";
    }

    $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&flete=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   	$formulario -> linea($matriz[$i][0],0,$estilo);
   	$formulario -> linea($ciudad_o[0][1],0,$estilo);
   	$formulario -> linea($ciudad_d[0][1],0,$estilo);
   	$formulario -> linea($carroc[0][0],0,$estilo);
   	$formulario -> linea($trayec[0][0],0,$estilo);
   	$formulario -> linea($transp[0][0],0,$estilo);
    $formulario -> linea($text_indica,0,$estilo);
    $formulario -> linea("$ ".number_format($matriz[$i][8]),0,$estilo);
    $formulario -> linea($text_estado,1,$estilo);
   }

   $this -> paginador -> getPaginas($formulario,"npagina",$varadici);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",$_REQUEST[opcion],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();

 }//FIN BUSCAR

function Datos()
 {
   $inicio[0][0]= 0;
   $inicio[0][1]= "-";

   $datos_usuario = $this -> usuario -> retornar();
   $usuario = $datos_usuario["cod_usuari"];

   $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
   $listado_ciudades = $objciud -> getListadoCiudades();

   $ciuori = array_merge($inicio,$listado_ciudades);
   $ciudes = array_merge($inicio,$listado_ciudades);

   $query = "SELECT a.cod_carroc,a.nom_carroc
               FROM ".BASE_DATOS.".tab_vehige_carroc a
              WHERE a.ind_estado = ".COD_ESTADO_ACTIVO."
                    ORDER BY 2
            ";

   $consulta = new Consulta($query, $this -> conexion);
   $carroc = $consulta -> ret_matriz();
   $carroc = array_merge($inicio,$carroc);

   $query = "SELECT a.cod_trayec,a.nom_trayec
               FROM ".BASE_DATOS.".tab_genera_trayec a
              WHERE a.cod_estado = ".COD_ESTADO_ACTIVO."
             		ORDER BY 1
            ";

   $consulta = new Consulta($query, $this -> conexion);
   $trayec = $consulta -> ret_matriz();
   $trayec = array_merge($inicio,$trayec);

   if($_REQUEST[flete])
   {
   	$indton = $activo = 0;

    $query = "SELECT a.cod_consec,a.cod_ciuori,a.cod_ciudes,a.cod_carroc,
  				     b.nom_carroc,a.cod_trayec,c.nom_trayec,a.ind_tonela,
  				     a.can_tonela,a.val_costos,a.cod_estado,a.cod_transp
  			    FROM ".BASE_DATOS.".tab_tablax_fletes a,
  			    	 ".BASE_DATOS.".tab_vehige_carroc b,
  			    	 ".BASE_DATOS.".tab_genera_trayec c
           	   WHERE a.cod_consec = ".$_REQUEST[flete]." AND
  			    	 a.cod_carroc = b.cod_carroc AND
  			    	 a.cod_trayec = c.cod_trayec
             ";

    $consulta = new Consulta($query, $this -> conexion);
    $matriz = $consulta -> ret_matriz();

    $_REQUEST[transp] = $matriz[0][11];

    $ciudad_o = $objciud -> getSeleccCiudad($matriz[0][1]);
    $ciudad_d = $objciud -> getSeleccCiudad($matriz[0][2]);

    $ciuori = array_merge($ciudad_o,$ciuori);
    $ciudes = array_merge($ciudad_d,$ciudes);

    $carroc_a[0][0] = $matriz[0][3];
    $carroc_a[0][1] = $matriz[0][4];
    $carroc = array_merge($carroc_a,$carroc);

    $trayec_a[0][0] = $matriz[0][5];
    $trayec_a[0][1] = $matriz[0][6];
    $trayec = array_merge($trayec_a,$trayec);

    if($matriz[0][7] == COD_ESTADO_ACTIVO)
     $indton = 1;

    $_REQUEST[canton] = $matriz[0][8];
    $_REQUEST[coston] = $matriz[0][9];

	if($matriz[0][10] == COD_ESTADO_ACTIVO)
     $activo = 1;

   }
   else
   {
   	$indton = 1;
   	$activo = 1;
   }

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/fletes.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","Tabla Fletes","form_item");
   $formulario -> linea("Informaci&oacute;n del Flete",0,"t2");

   if($datos_usuario["cod_perfil"] == "")
    $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   else
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);

   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $formulario -> oculto("transp",$datos_filtro[clv_filtro],0);
    $_REQUEST[transp] = $datos_filtro[clv_filtro];
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
    $transpor = $consulta -> ret_matriz();
    $transpor = array_merge($inicio,$transpor);

    if($_REQUEST[transp])
    {
     $query = "SELECT a.cod_tercer,a.abr_tercer
   			     FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			          ".BASE_DATOS.".tab_tercer_activi b
   			    WHERE a.cod_tercer = b.cod_tercer AND
   			          b.cod_activi = ".COD_FILTRO_EMPTRA." AND
   			          a.cod_tercer = '".$_REQUEST[transp]."'
   			          ORDER BY 2
   			  	  ";

     $consulta = new Consulta($query, $this -> conexion);
     $transp_a = $consulta -> ret_matriz();
     $transpor = array_merge($transp_a,$transpor);
    }

    $formulario -> nueva_tabla();
    $formulario -> lista("Transportadora","transp",$transpor,1);
   }

   $formulario -> nueva_tabla();

   $formulario -> lista("Origen","ciuori",$ciuori,0);
   $formulario -> lista("Destino","ciudes",$ciudes,1);
   $formulario -> lista("Carrocer&acute;a","carroc",$carroc,0);
   $formulario -> lista("Trayecto","trayec",$trayec,1);
   $formulario -> texto("Toneladas (Cant)","text","canton\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,10,10,"",$_REQUEST[canton]);
   $formulario -> texto("Valor x Ton \$","text","coston\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,10,10,"",$_REQUEST[coston]);
   $formulario -> caja("Por Tonelada","indton",1,$indton,0);
   $formulario -> caja("Activo","estado",1,$activo,1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> oculto("flete",$_REQUEST[flete],0);

   if(!$_REQUEST[flete])
    $formulario -> boton("Insertar","button\" onClick=\"aceptar() ",0);
   else
    $formulario -> boton("Actualizar","button\" onClick=\"aceptar() ",0);

   $formulario -> boton("Volver","button\" onClick=\"javascript:history.go(-1)",0);
   $formulario -> cerrar();
 }//FIN DATOS

 function Insertar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario = $datos_usuario["cod_usuari"];

   $fec_actual = date("Y-m-d H:i:s");

   $query = "SELECT a.cod_paisxx,a.cod_depart
               FROM ".BASE_DATOS.".tab_genera_ciudad a
              WHERE a.cod_ciudad = ".$_REQUEST[ciuori]."
            ";

   $ciudad = new Consulta($query, $this -> conexion);
   $ciuori = $ciudad -> ret_matriz();

   $query = "SELECT a.cod_paisxx,a.cod_depart
               FROM ".BASE_DATOS.".tab_genera_ciudad a
              WHERE a.cod_ciudad = ".$_REQUEST[ciudes]."
            ";

   $ciudad = new Consulta($query, $this -> conexion);
   $ciudes = $ciudad -> ret_matriz();

   if(!$_REQUEST[estado])
    $_REQUEST[estado] = COD_ESTADO_INACTI;

   if(!$_REQUEST[indton])
    $_REQUEST[indton] = COD_ESTADO_INACTI;

   if($_REQUEST[flete])
   {
   	$nom_actp = "Actualizo";

   	$query = "UPDATE ".BASE_DATOS.".tab_tablax_fletes
               SET cod_paiori = ".$ciuori[0][0].",
               	   cod_depori = ".$ciuori[0][1].",
               	   cod_ciuori = ".$_REQUEST[ciuori].",
               	   cod_paides = ".$ciudes[0][0].",
               	   cod_depdes = ".$ciudes[0][1].",
               	   cod_ciudes = ".$_REQUEST[ciudes].",
                   cod_carroc = ".$_REQUEST[carroc].",
                   cod_trayec = ".$_REQUEST[trayec].",
                   cod_transp = ".$_REQUEST[transp].",
                   ind_tonela = ".$_REQUEST[indton].",
                   val_costos = ".$_REQUEST[coston].",
                   can_tonela = ".$_REQUEST[canton].",
                   cod_estado = ".$_REQUEST[estado].",
                   usr_modifi = '".$usuario."',
                   fec_modifi = '".$fec_actual."'
             WHERE cod_consec = ".$_REQUEST[flete]."
           ";
   }
   else
   {
   	$nom_actp = "Inserto";

   	//trae el consecutivo de la tabla
    $query = "SELECT Max(cod_consec) AS maximo
                FROM ".BASE_DATOS.".tab_tablax_fletes
             ";

    $consec = new Consulta($query, $this -> conexion);
    $ultimo = $consec -> ret_matriz();
    $ultimo[0][0]++;

    $query = "INSERT INTO ".BASE_DATOS.".tab_tablax_fletes
   						 (cod_consec,cod_paiori,cod_depori,cod_ciuori,
   						  cod_paides,cod_depdes,cod_ciudes,cod_carroc,
   						  cod_trayec,cod_transp,val_costos,ind_tonela,
   						  can_tonela,cod_estado,usr_creaci,fec_creaci)
                  VALUES (".$ultimo[0][0].",".$ciuori[0][0].",".$ciuori[0][1].",".$_REQUEST[ciuori].",
   						  ".$ciudes[0][0].",".$ciudes[0][1].",".$_REQUEST[ciudes].",".$_REQUEST[carroc].",
   						  ".$_REQUEST[trayec].",".$_REQUEST[transp].",".$_REQUEST[coston].",".$_REQUEST[indton].",
   						  ".$_REQUEST[canton].",".$_REQUEST[estado].",'".$_REQUEST[usuario]."','".$fec_actual."')
   		    ";
   }

   if($consulta = new Consulta($query, $this -> conexion,"BRC"))
   {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Volver al Listado Principal</a></b>";

     $mensaje =  "El Flete Se ".$nom_actp." Exitosamente.".$link_a;
     $mens = new mensajes();
     $mens -> correcto("TABLA FLETES",$mensaje);
    }
 }

 function listarFleteDespac()
 {
  $formulario = new Formulario ("index.php","post","Tabla de Fletes","form_item");

  if($_REQUEST[ciuoritab] && $_REQUEST[carroctab] && $_REQUEST[valobj0])
  {
   $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);

   $query = "SELECT a.cod_consec,a.cod_ciuori,a.cod_ciudes,b.nom_carroc,
  		            c.nom_trayec,a.ind_tonela,a.val_costos,a.can_tonela
  		       FROM ".BASE_DATOS.".tab_tablax_fletes a,
  		            ".BASE_DATOS.".tab_vehige_carroc b,
  		            ".BASE_DATOS.".tab_genera_trayec c
  		      WHERE a.cod_carroc = b.cod_carroc AND
  		            a.cod_trayec = c.cod_trayec AND
  		            a.cod_transp = '".$_REQUEST[transport]."' AND
  		            a.cod_ciuori = ".$_REQUEST[ciuoritab]." AND
  		            a.cod_ciudes = ".$_REQUEST[valobj0]." AND
  		            a.cod_carroc = ".$_REQUEST[carroctab]." AND
  		            a.cod_estado = '".COD_ESTADO_ACTIVO."'
            ";

   $consulta = new Consulta($query, $this -> conexion);
   $tablafle = $consulta -> ret_matriz();

   $formulario -> linea ("Se Encontro un Total de ".sizeof($tablafle)." Registro(s)",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Consecutivo",0,"t");
   $formulario -> linea("Origen",0,"t");
   $formulario -> linea("Destino",0,"t");
   $formulario -> linea("Carrocer&iacute;a",0,"t");
   $formulario -> linea("Trayecto",0,"t");
   $formulario -> linea("Por Tonelada",0,"t");
   $formulario -> linea("Valor (Unit)",1,"t");

   for($i = 0; $i < sizeof($tablafle); $i++)
   {
    if($tablafle[$i][5] == COD_ESTADO_ACTIVO)
    {
     $nom_estado = "Si";
     $costos = $tablafle[$i][6] / $tablafle[$i][7];
     $valtot = ($tablafle[$i][6] / $tablafle[$i][7]) * $_REQUEST[valobj1];
    }
    else
    {
     $nom_estado = "No";
     $costos = $valtot = $tablafle[$i][6];     
    }
        
    $tablafle[$i][0] = "<a href=# onClick=\"opener.document.forms[0].tabfle".$_REQUEST[indice].$_REQUEST[codigo].".value='".$valtot."'; opener.document.forms[0].fleuni".$_REQUEST[indice].$_REQUEST[codigo].".value='".$costos."'; opener.document.forms[0].codfle".$_REQUEST[indice].$_REQUEST[codigo].".value='".$tablafle[$i][0]."'; top.close()\">".$tablafle[$i][0]."</a>";

    $ciudad_o = $objciud -> getSeleccCiudad($tablafle[$i][1]);
    $ciudad_d = $objciud -> getSeleccCiudad($tablafle[$i][2]);    

    $formulario -> linea($tablafle[$i][0],0,"i");
    $formulario -> linea($ciudad_o[0][1],0,"i");
    $formulario -> linea($ciudad_d[0][1],0,"i");
    $formulario -> linea($tablafle[$i][3],0,"i");
    $formulario -> linea($tablafle[$i][4],0,"i");
    $formulario -> linea($nom_estado,0,"i");
    $formulario -> linea("\$ ".number_format($costos),1,"i");
   }
  }
  else
   $formulario -> linea ("Debe Seleccionar Ciudad Origen, Ciudad Destino y la Placa del Vehiculo.",1,"e");

  $formulario -> cerrar();
 }

}//FIN CLASE Proc_fletes
     $proceso = new Proc_fletes($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>