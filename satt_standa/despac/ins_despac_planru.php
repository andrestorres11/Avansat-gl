<?php

class Proc_Plan_Ruta
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
    $this -> Listar();
  else
  {
      switch($_REQUEST[opcion])
      {
        case "1":
          $this -> Listar();
          break;
        case "2":
          $this -> Formulario();
          break;
        case "3":
          $this -> Insertar();
          break;
      }//FIN SWITCH
  }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Listar()
 {
  ini_set("memory_limit", "256M");
  $datos_usuario = $this -> usuario -> retornar();

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
                    d.cod_paisxx = e.cod_paisxx AND
                    a.ind_anulad != 'A' AND
                    a.ind_planru = 'N'
            ";

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
                    d.cod_paisxx = e.cod_paisxx AND
                    a.ind_anulad != 'A' AND
                    a.ind_planru = 'N'
            ";

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
                    d.abr_tercer, a.usr_creaci, a.fec_creaci
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_despac_vehige b,
                    ".BASE_DATOS.".tab_tercer_tercer c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.num_despac = b.num_despac AND
                    b.cod_transp = c.cod_tercer AND
                    b.cod_conduc = d.cod_tercer AND
                    a.ind_anulad != 'A' AND
                    a.ind_planru = 'N'
            ";

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
  $query .= " AND DATE( a.fec_despac )
              BETWEEN DATE_SUB( CURDATE( ) , INTERVAL 3 MONTH )
              AND CURDATE( )";
              
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
  $formulario -> linea("Conductor",0,"t");
  $formulario -> linea("Usuario creador",0,"t");
  $formulario -> linea("Fecha Creaci�n",1,"t");

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

    $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&despac=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

    $formulario -> linea($matriz[$i][0],0,$estilo);
    $formulario -> linea($matriz[$i][1],0,$estilo);
    $formulario -> linea($estado,0,$estilo);
    $formulario -> linea($ciudad_o[0][1],0,$estilo);
    $formulario -> linea($ciudad_d[0][1],0,$estilo);
    $formulario -> linea($matriz[$i][5],0,$estilo);
    $formulario -> linea($matriz[$i][6],0,$estilo);
    $formulario -> linea($matriz[$i][7],0,$estilo);
    $formulario -> linea($matriz[$i][8],0,$estilo);
    $formulario -> linea($matriz[$i][9],0,$estilo);
    $formulario -> linea($matriz[$i][10],1,$estilo);

   }

   $formulario -> nueva_tabla();
   $formulario -> oculto("b_ciuori",$_REQUEST[b_ciuori],0);
   $formulario -> oculto("b_ciudes",$_REQUEST[b_ciudes],0);
   $formulario -> oculto("transp",$_REQUEST[transp],0);
   $formulario -> oculto("fil",$_REQUEST[fil],0);
   $formulario -> oculto("fecini",$_REQUEST[fecini],0);
   $formulario -> oculto("fecfin",$_REQUEST[fecfin],0);

   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();

 }

 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario = $datos_usuario["cod_usuari"];

   $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);

   $inicio[0][0] = 0;
   $inicio[0][1] = "-";

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

   $query = "SELECT a.cod_ciudes
   		       FROM ".BASE_DATOS.".tab_despac_despac a
   		      WHERE a.num_despac = ".$_REQUEST[despac]."
   		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $ciudesorig = $consulta -> ret_matriz();

   $query = "SELECT a.cod_transp,a.cod_rutasx,b.cod_manifi
	           FROM ".BASE_DATOS.".tab_despac_vehige a,
		    		".BASE_DATOS.".tab_despac_despac b
	      	  WHERE a.num_despac = b.num_despac AND
		    		a.num_despac = '".$_REQUEST[despac]."'
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $transp = $consulta -> ret_matriz();

   $query = "SELECT a.ind_feplle
  		       FROM ".BASE_DATOS.".tab_config_parame a
  		      WHERE a.ind_feplle = '1'
  		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $feplle = $consulta -> ret_matriz(); 
   
   if(!$_REQUEST[rutasx])
    $_REQUEST[rutasx] = $rutaorig = $transp[0][1];

   if(!$_REQUEST[ciudestr])
    $nomcampciudest = "a.cod_ciudes";
   else
    $nomcampciudest = "'".$_REQUEST[ciudestr]."'";

   $query = "SELECT c.cod_rutasx,c.nom_rutasx,a.obs_despac,a.cod_ciuori,a.cod_ciudes
	       	   FROM ".BASE_DATOS.".tab_despac_despac a,
		    		".BASE_DATOS.".tab_despac_vehige b,
		    		".BASE_DATOS.".tab_genera_rutasx c,
		    		".BASE_DATOS.".tab_genera_ruttra d
	      	  WHERE a.num_despac = ".$_REQUEST[despac]." AND
		    		a.num_despac = b.num_despac AND
		    		a.cod_ciuori = c.cod_ciuori AND
		    		c.cod_ciudes = ".$nomcampciudest." AND
		    		b.cod_transp = d.cod_transp AND
		    		c.cod_rutasx = d.cod_rutasx AND
		    		c.ind_estado = '1'
		    		GROUP BY 1 ORDER BY 2
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $rutasx = $consulta -> ret_matriz();

   $query = "SELECT a.cod_noveda,a.nom_noveda
	       	   FROM ".BASE_DATOS.".tab_genera_noveda a
	      	  WHERE a.ind_tiempo = '1' AND
		    		a.ind_alarma = 'N'
		    		ORDER BY 2
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $noveda = $consulta -> ret_matriz();

   $noveda = array_merge($inicio,$noveda);
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/planru.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","PLAN DE RUTA","form_ins");

   $formulario -> nueva_tabla();
   $formulario -> linea("Plan de Ruta Para el Despacho # ".$_REQUEST[despac]." - Documento #/Despacho ".$transp[0][2]."",1,"t2");

   if($manredes)
   {
   	$query = "SELECT a.cod_ciudad
   			    FROM ".BASE_DATOS.".tab_despac_remdes a
   			   WHERE a.num_despac = ".$_REQUEST[despac]." AND
   			    	 a.cod_ciudad != ".$ciudesorig[0][0]."
    			   	 GROUP BY 1 ORDER BY 1
   			 ";

   	$consulta = new Consulta($query, $this -> conexion);
    $ciudesti = $consulta -> ret_matriz();

    for($i = 0; $i < sizeof($ciudesti); $i++)
    {
     $ciudad_s = $objciud -> getSeleccCiudad($ciudesti[$i][0]);
     $ciudesti[$i][1] = $ciudad_s[0][1];
    }

    $ciudesti = array_merge($inicio,$ciudesti);

    if($_REQUEST[ciudestr])
    {
     $ciudad_s = $objciud -> getSeleccCiudad($ciudesorig[0][0]);
     $ciudad_o = $ciudad_s;
     $ciudesti = array_merge($ciudesti,$ciudad_o);

     $ciudad_s = $objciud -> getSeleccCiudad($_REQUEST[ciudestr]);
     $ciudad_o = $ciudad_s;
     $ciudesti = array_merge($ciudad_o,$ciudesti);
    }

    $formulario -> nueva_tabla();
    $formulario -> linea("Seleccionar Nuevo Destino",0,"t2");

    $formulario -> nueva_tabla();
    $formulario -> lista("Ciudades Destinatarios","ciudestr\" onChange=\"form_ins.submit()",$ciudesti,1);
   }

   $formulario -> nueva_tabla();
   $formulario -> linea("Selecci&oacute;n de Ruta",1,"h");

   $formulario -> nueva_tabla();
   $formulario -> linea("",0,"t");
   $formulario -> linea("",0,"t");
   $formulario -> linea("C&oacute;digo Ruta",0,"t");
   $formulario -> linea("Ruta a Seguir",0,"t");
   $formulario -> linea("Origen",0,"t");
   $formulario -> linea("Destino",1,"t");

   if($rutasx && $_REQUEST[rutasx])
   {
    for($i = 0; $i < sizeof($rutasx); $i++)
    {
   	 $ciudad_o = $objciud -> getSeleccCiudad($rutasx[$i][3]);
     $ciudad_d = $objciud -> getSeleccCiudad($rutasx[$i][4]);

	 if($_REQUEST[rutasx] == $rutasx[$i][0])
	  $formulario -> radio("","rutasx\"",$rutasx[$i][0],1,0);
	 else
	  $formulario -> radio("","rutasx\" onClick=\"form_ins.submit()",$rutasx[$i][0],0,0);

	 $formulario -> linea($rutasx[$i][0],0,"i");
	 $formulario -> linea($rutasx[$i][1],0,"i");
	 $formulario -> linea($ciudad_o[0][1],0,"i");
	 $formulario -> linea($ciudad_d[0][1],1,"i");
    }
   }
   else
   {
   	$formulario -> nueva_tabla();
   	$formulario -> linea("No Existen Rutas Registradas &oacute; Relacionadas a la Transportadora, Origen - Destino",1,"e");
   	$_REQUEST[rutasx] = NULL;
   }
   
   /*** NUEVA VALIDACION CORONA *******************/
   $mSelect = "SELECT fec_citcar, hor_citcar 
                 FROM ".BASE_DATOS.".tab_despac_despac
                WHERE num_despac = '".$_REQUEST['despac']."'";

   $consulta = new Consulta($mSelect, $this -> conexion);
   $fec_citcar = $consulta -> ret_matriz(); 
   
   if( $fec_citcar[0][0] != '' && $fec_citcar[0][1] != '' )
   {
     $formulario -> oculto("ind_valcit\" id=\"ind_valcitID","1",0);
   }
   else
   {
     $formulario -> oculto("ind_valcit\" id=\"ind_valcitID","0",0);
   }
   
   $fec_valida = $fec_citcar[0][0]." ".$fec_citcar[0][1];
   
   if(!$_REQUEST['fec_citcar'])
    $fevalida = $fec_valida;
   else
    $fevalida = $_REQUEST['fec_citcar'];
   
   $formulario -> nueva_tabla();
   $formulario -> linea("Fecha Cita de Cargue",1,"h");
   $formulario -> nueva_tabla();
   $formulario -> fecha_calendar("Fecha/Hora","fec_citcar","form_ins",$fevalida,"yyyy-mm-dd hh:ii",0,1);   
   // $formulario -> linea($fec_valida,0,"i");
   /***********************************************/
   $formulario -> nueva_tabla();
   if($feplle)
	   $formulario -> linea("Fecha Programada de Llegada",1,"h");
   else
       $formulario -> linea("Fecha Programada de Salida",1,"h");
    
   $formulario -> nueva_tabla();
   if(!$_REQUEST[fecprosal])
    $feactual = date("Y-m-d H:i");
   else
    $feactual = $_REQUEST[fecprosal];

   $feccal = $feactual;

   $formulario -> fecha_calendar("Fecha/Hora","fecprosal","form_ins",$feactual,"yyyy-mm-dd hh:ii",0,1);   
 
   $formulario -> nueva_tabla();
   $formulario -> linea("Puesto de Control",1,"h");

   $query = "SELECT a.cod_contro,a.nom_contro,c.val_duraci,
		    		if(a.ind_virtua = '0','Fisico','Virtual')
	       	   FROM ".BASE_DATOS.".tab_genera_contro a,
		    		".BASE_DATOS.".tab_genera_ruttra b,
		    		".BASE_DATOS.".tab_genera_rutcon c
	      	  WHERE c.cod_rutasx = '".$_REQUEST[rutasx]."' AND
		    		c.cod_contro = a.cod_contro AND
		    		b.cod_rutasx = c.cod_rutasx AND
		    		b.cod_transp = '".$transp[0][0]."' AND
		    		b.cod_contro = c.cod_contro AND
		    		c.ind_estado = '1' AND
		    		a.ind_estado = '1'
		    		ORDER BY 3
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $pcontr = $consulta -> ret_matriz();

   $formulario -> nueva_tabla();
   $formulario -> linea("",0,"t");
   $formulario -> linea("S/N",0,"t");
   $formulario -> linea("C&oacute;digo",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("Puesto",0,"t");
   $formulario -> linea("",0,"t");
   $formulario -> linea("Novedad",0,"t");
   $formulario -> linea("",0,"t");
   $formulario -> linea("Tiempo Estimado",0,"t");
   $formulario -> linea("Fecha y Hora Planeada",1,"t");

   $pcontro=$_REQUEST[pcontro];
   $pctime=$_REQUEST[pctime];
   $pcnove=$_REQUEST[pcnove];

   $pctime_urban = $_REQUEST[pctime_urban];

   $pctime_ursize = sizeof($pctime_urban);
   
   $tiemacu = 0;
   $numpcon = sizeof($pcontr);//numero de puestos de control del plan de ruta

   for($i = 0; $i < $numpcon; $i++)
   {
	if(!$_REQUEST[pcontro])
	 $pcontro[$i] = 1;

	$temp_nove = $noveda;

    if($pcnove[$i] != "0")
	{
	 $query = "SELECT a.cod_noveda,a.nom_noveda
		     FROM ".BASE_DATOS.".tab_genera_noveda a
		    WHERE a.cod_noveda = '".$pcnove[$i]."'
		  ";

	 $consulta = new Consulta($query, $this -> conexion);
   	 $nove_selec = $consulta -> ret_matriz();

	 $temp_nove = array_merge($nove_selec,$temp_nove);
	}	
	if($i == 0){
		for($j=0; $j < $pctime_ursize; $j++){
			$pctime_uracu += $pctime_urban[$j];
		}
	}
   if($feplle)
   {	   
	   $valfin = $pcontr[$numpcon - 1][2] + $tiemacu + $pctime_uracu;
	   $query = "SELECT DATE_SUB('".$feactual."', INTERVAL ".$valfin." MINUTE)";		
	   $consulta = new Consulta($query, $this -> conexion);
	   $timeless = $consulta -> ret_matriz();
	   $feccal = $timeless[0][0];
	   $tiempcum = $tiemacu + $pcontr[$i][2] + $pctime_uracu;	      
   }
   else
   {
	$tiempcum = $tiemacu + $pcontr[$i][2];
   }
   
	$query = "SELECT DATE_ADD('".$feccal."', INTERVAL ".$tiempcum." MINUTE)";
	
	$consulta = new Consulta($query, $this -> conexion);
   	$timemost = $consulta -> ret_matriz();
	$tiemacu += $pctime[$i];

	if($pcontr[$i][0] == CONS_CODIGO_PCLLEG)
	{
	 $formulario -> caja("","pcontro[$i]\" disabled ",$pcontr[$i][0],1,0);
	 $formulario -> linea("-",0,"i");
	 $formulario -> linea($pcontr[$i][1],0,"i");
	 $formulario -> linea($pcontr[$i][3],0,"i");
	 $formulario -> linea("",0,"t");
	 $formulario -> linea("-",0,"i");
	 $formulario -> linea("",0,"t");
	 $formulario -> linea("-",0,"i");
	 $formulario -> linea($timemost[0][0],1,"i");
	 $formulario -> oculto("pcontro[$i]",$pcontr[$i][0],0);
	}
	else
	{
	 $formulario -> caja("","pcontro[$i]",$pcontr[$i][0],$pcontro[$i]);	  
	 $formulario -> linea($pcontr[$i][0],0,"i");
	 $formulario -> linea($pcontr[$i][1],0,"i");
	 $formulario -> linea($pcontr[$i][3],0,"i");
	 $formulario -> lista("","pcnove[$i]",$temp_nove,0);
	 $formulario -> texto("","text","pctime[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_ins.submit()",0,10,4,"",$pctime[$i]);
	 $formulario -> linea($timemost[0][0],1,"i");	
	}
   }   

   if($manredes && $desurb)
   {
   	$query = "SELECT a.cod_remdes
   			    FROM ".BASE_DATOS.".tab_despac_remdes a,
   			   		 ".BASE_DATOS.".tab_destin_contro b
   			   WHERE a.num_despac = ".$_REQUEST[despac]." AND
   			   		 a.cod_remdes = b.cod_remdes
   			   		 GROUP BY 1
   			 ";

   	$consulta = new Consulta($query, $this -> conexion);
    $existdes = $consulta -> ret_matriz();

    if($existdes)
    {
     $formulario -> nueva_tabla();
     $formulario -> linea("Puestos de Control Urbanos - Destinatarios",1,"h");

     $formulario -> nueva_tabla();
     $formulario -> linea("",0,"t");
     $formulario -> linea("S/N",0,"t");
     $formulario -> linea("C&oacute;digo",0,"t");
     $formulario -> linea("Nombre",0,"t");
     $formulario -> linea("Puesto",0,"t");
     $formulario -> linea("Direcci&oacute;n",0,"t");
     $formulario -> linea("Ciudad",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Tiempo Estimado",1,"t");

     $formulario -> oculto("desurb",1,0);

     for($i = 0; $i < sizeof($existdes); $i++)
     {
      $query = "SELECT a.cod_contro,a.nom_contro,if(a.ind_virtua = '0','Fisico','Virtual'),a.dir_contro,
		    		   a.cod_ciudad
	       	      FROM ".BASE_DATOS.".tab_genera_contro a,
		    		   ".BASE_DATOS.".tab_destin_contro b,
		    		   ".BASE_DATOS.".tab_despac_remdes c
	      	     WHERE a.cod_contro = b.cod_contro AND
		    		   b.cod_remdes = c.cod_remdes AND
		    		   c.cod_remdes = ".$existdes[$i][0]."
	          ";

      $consulta = new Consulta($query, $this -> conexion);
      $pcontr = $consulta -> ret_matriz();

      $query = "SELECT SUM(a.fec_planea)
       	          FROM ".BASE_DATOS.".tab_despac_seguim a
      		     WHERE a.cod_contro = ".$pcontr[0][0]."
      		   ";

      $consulta = new Consulta($query, $this -> conexion);
      $tiemppla = $consulta -> ret_matriz();

      $query = "SELECT SUM(a.fec_noveda)
      		      FROM ".BASE_DATOS.".tab_despac_noveda a
      		     WHERE a.cod_contro = ".$pcontr[0][0]."
      		   ";

      $consulta = new Consulta($query, $this -> conexion);
      $tiempeje = $consulta -> ret_matriz();

      if(!$tiemppla[0][0])
       $tiemppla[0][0] = 0;

      if(!$tiempeje[0][0])
       $tiempeje[0][0] = 0;

      $query = "SELECT (TIME_TO_SEC(TIMEDIFF('".$tiemppla[0][0]."','".$tiempeje[0][0]."'))/60)/2
      		   ";

      $consulta = new Consulta($query, $this -> conexion);
      $tiemprod = $consulta -> ret_matriz();

      if(!$tiemprod[0][0])
       $tiemprod[0][0] = 50;
      else
       $tiemprod[0][0] = floor($tiemprod[0][0]);

      if(!$pctime_urban[$i])
       $pctime_urban[$i] = $tiemprod[0][0];

      $ciudad_se = $objciud -> getSeleccCiudad($pcontr[0][4]);

      $formulario -> caja("","yahoo\" disabled ",1,1,0);
      $formulario -> oculto("pcontro_urban[$i]",$pcontr[0][0],0);
      $formulario -> linea($pcontr[0][0],0,"i");
      $formulario -> linea($pcontr[0][1],0,"i");
      $formulario -> linea($pcontr[0][2],0,"i");
      $formulario -> linea($pcontr[0][3],0,"i");
      $formulario -> linea($ciudad_se[0][1],0,"i");
      $formulario -> texto("","text","pctime_urban[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_ins.submit()",1,10,4,"",$pctime_urban[$i]);
     }
    }
   }
   
	 if($feplle)
	   {
		$formulario -> nueva_tabla();
	    $formulario -> linea("Fecha Programada de Salida",1,"h");
	    
	    $formulario -> nueva_tabla();
	    $formulario -> linea("Fecha/Hora",0,"t");
		$fecpllle = $timeless[0][0];	 
	    $formulario -> linea($fecpllle,1,"i"); 
	   }
	  else
	  {
	  	if($pctime_uracu)
	  	{	    
			$query = "SELECT DATE_ADD('".$timemost[0][0]."', INTERVAL ".$pctime_uracu." MINUTE)";
			$consulta = new Consulta($query, $this -> conexion);
   			$fecpllle = $consulta -> ret_matriz();
   			$_REQUEST[fecprosal] = $fecpllle[0][0];   			
		}
	    else
	    {
	    	$_REQUEST[fecprosal]= $timemost[0][0];	    	
	    }	    	 
	  }  
   //Nelson 12/02/2013
   if($_REQUEST[ope_gpsxxx])
     $formulario -> texto("","hidden","id_gps", 0,3,3,0, $this -> ValidaIdGps($_REQUEST[ope_gpsxxx]));
   else
     $formulario -> texto("","hidden","id_gps", 0,3,3,0, $_REQUEST[id_gps]);
   
   $formulario -> nueva_tabla();
   $formulario -> linea("Datos Gps",1,"h");
   $formulario -> nueva_tabla();
   $formulario -> lista("Gps","ope_gpsxxx\" onChange=\"SelectGps()",$this -> getGps($_REQUEST[ope_gpsxxx]),0,0);
   
   if($this -> ValidaIdGps($_REQUEST[ope_gpsxxx]) == 1)
     $formulario -> texto("ID:","text","idx_gpsxxx",0,20,20,0,$_REQUEST[idx_gpsxxx]);
   
   $formulario -> texto("Usuario:","text","usr_gpsxxx",0,20,20,0,$_REQUEST[usr_gpsxxx]);
   $formulario -> texto("Clave:","text","clv_gpsxxx",0,20,20,0,$_REQUEST[clv_gpsxxx]);
   
   
   $formulario -> nueva_tabla();
   $formulario -> linea("Observaciones del Plan de Ruta",1,"h");

   if(!$_REQUEST[obs])
    $_REQUEST[obs] = $rutasx[0][2];

   $formulario -> nueva_tabla();
   $formulario -> texto("","textarea","obs",1,60,4,"SOFT",$_REQUEST[obs]);

/***
 * Generar salida Inmediata
 * */
   if(!$_REQUEST[fecsalida])
    $fesalida = date("Y-m-d H:i");
   else
    $fesalida = $_REQUEST[fecsalida];

   $formulario -> nueva_tabla();
   $formulario -> linea("Generar Salida",1,"h");

   $formulario -> nueva_tabla();
   echo '<td align="right" class="celda"><input align="rigth" type="checkbox" value="1" name="ind_salida" id="ind_salida"></td>';
//   $formulario -> caja("",'ind_salida" align="rigth',1,0,0);
   $formulario -> linea("Generar Salida Inmediata",0,"i");
   $formulario -> fecha_calendar("Fecha/Hora Salida","fecsalida","form_ins",$fesalida,"yyyy/mm/dd hh:ii",0,1);   
/***
 * Generar salida Inmediata
 * */
   
   $formulario -> nueva_tabla();

   if($desurb)
    $formulario -> oculto("desurb",1,0);
   else
    $formulario -> oculto("desurb",0,0);

   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("despac",$_REQUEST[despac],0);
   $formulario -> oculto("transp",$transp[0][0],0);
   $formulario -> oculto("opcion",$_REQUEST[opcion],0);
   $formulario -> oculto("usuario",$usuario,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
	
   if($feplle)
   	$formulario -> oculto("fecpllle",$fecpllle,0);
   
   $formulario -> nueva_tabla();
   $formulario -> linea("",1,"h");
   if($_REQUEST[rutasx])
    $formulario -> boton("Insertar","button\" onClick=\"aceptar_insert()",1);

   $formulario -> cerrar();
 }
   /******************************************************************
    * @ Function ValidaIdGps                                         *
    * @ Brief    Consulta si la operadora seleccionada usa ID        *
    * @ Param    int $ope_gpsxxx C�digo de la operadora              *
    * @ Return   int $matrix[0][0] con el indicador de la operadora  *
    *****************************************************************/ 
   function ValidaIdGps ($ope_gpsxxx)
   {
      $mQuery =  "SELECT ind_usaidx
                     FROM ".BD_STANDA.".tab_genera_opegps
                    WHERE ind_estado = '1' AND
                    cod_operad = '$ope_gpsxxx'"; 
       $consulta = new Consulta($mQuery, $this -> conexion, "BR");
       $matrix = $consulta -> ret_matriz();
       return $matrix[0][0];
   }
 
   /******************************************************************
    * @ Function getGps                                              *
    * @ Brief    Consulta las operadoras disponibles                 *
    * @ Param    int $ope_gpsxxx C�digo de la operadora              *
    * @ Return   array                                               *
    *****************************************************************/ 
   function getGps($ope_gpsxxx)
   {
     if($ope_gpsxxx)
     {
       $mQuery =  "SELECT cod_operad, nom_operad, ind_usaidx
                     FROM ".BD_STANDA.".tab_genera_opegps
                    WHERE ind_estado = '1' AND
                    cod_operad = '$ope_gpsxxx'"; 
       $consulta = new Consulta($mQuery, $this -> conexion, "BR");
       $matrix = $consulta -> ret_matriz();
     }
     
     $mSql = "SELECT cod_operad, nom_operad, ind_usaidx
                FROM ".BD_STANDA.".tab_genera_opegps
               WHERE ind_estado = '1'";
    $consulta = new Consulta($mSql, $this -> conexion, "BR");
    $matriz = $consulta -> ret_matriz();
    
    $Select = array(array('---','---'));
    
    if( $ope_gpsxxx )
      $matriz = array_merge($matrix ,$Select,$matriz );
    else
      $matriz = array_merge($Select, $matriz);
    
    return $matriz;
               
   }
  
 function Insertar()
 {      
    /*echo "<pre>";
    print_r( $_REQUEST );
    echo "</pre>";
    die();*/
   $fec_actual = date("Y-m-d H:i:s");
   
   if(isset($_REQUEST[fecpllle]))
   {
    $fecpla = $_REQUEST[fecpllle];   
   }
   else
   {
    $fecpla = $_REQUEST[fecprosal];	
   }
   
   // Inserta datos del gps asignado al despacho
   if( $_REQUEST[id_gps] == '0' or $_REQUEST[id_gps] == '1')
   {
     $mMaxConsecGpsDespac = "SELECT MAX(cod_consec)+1 FROM ".BASE_DATOS.".tab_despac_gpsxxx" ;
     $consulta = new Consulta($mMaxConsecGpsDespac, $this -> conexion, "BR");
     $mMaxConsec = $consulta -> ret_matriz();
     $mMaxConsec = $mMaxConsec[0][0];

     if ($_REQUEST[idx_gpsxxx])
        $_REQUEST[idx_gpsxxx] = $_REQUEST[idx_gpsxxx] == '' ?  'NULL': $_REQUEST[idx_gpsxxx] ; 
     else
        $_REQUEST[idx_gpsxxx] = 'NULL';     
  
     $mInsertGpsDespac = "INSERT INTO ".BASE_DATOS.".tab_despac_gpsxxx 
                                 (
                                   num_despac, cod_consec, idx_gpsxxx, cod_opegps, 
                                   nom_usrgps, clv_usrgps, usr_creaci, fec_creaci
                                 )
                                 VALUES
                                 (
                                  '".$_REQUEST[despac]."','$mMaxConsec','".$_REQUEST[idx_gpsxxx]."','".$_REQUEST[ope_gpsxxx]."',
                                  '".$_REQUEST[usr_gpsxxx]."','".base64_encode($_REQUEST[clv_gpsxxx])."','".$_REQUEST[usuario]."', NOW()
                                  )";
     $insercion = new Consulta($mInsertGpsDespac, $this -> conexion,"R");
   }
   
   $fecpla = str_replace("/","-",$fecpla);

   $pcontro=$_REQUEST[pcontro];
   $pctime=$_REQUEST[pctime];
   $pcnove=$_REQUEST[pcnove];

   $pcontro_urban=$_REQUEST[pcontro_urban];
   $pctime_urban=$_REQUEST[pctime_urban];

   $query = "SELECT a.cod_contro,c.val_duraci
	       	   FROM ".BASE_DATOS.".tab_genera_contro a,
		    		".BASE_DATOS.".tab_genera_rutcon c
	      	  WHERE c.cod_rutasx = '".$_REQUEST[rutasx]."' AND
		    		c.cod_contro = a.cod_contro AND
		    		c.ind_estado = '1'
		    		ORDER BY 2
	    ";

   $consulta = new Consulta($query, $this -> conexion, "BR");
   $matriz = $consulta -> ret_matriz();
   $matriz = $this -> changeDuplicateValDuraci( $matriz );
   $k = 0;

   for($i = 0; $i < sizeof($matriz); $i++)
   {
	for($j = 0; $j < sizeof($pcontro); $j++)
	{
	 if(!$pcontro[$j])
	  $pcontro[$j] = 0;

	 if($pcontro[$j] == $matriz[$i][0])
	 {
	  $pernoc[$k][0] = $matriz[$i][0];
	  $pernoc[$k][1] = $matriz[$i][1];
	  if($pcnove[$i] != "0")
	  {
	   $pernoc[$k][2] = $pcnove[$i];
	   $pernoc[$k][3] = $pctime[$i];
	  }
	  else
	  {
	   $pernoc[$k][2] = 0;
	   $pernoc[$k][3] = 0;
	  }
	  $k++;
	  $j = sizeof($pcontro) + 1;
	 }
	}
   }

   $tieacu = 0;

   for($i = 0; $i < sizeof($pernoc); $i++)
   {
	 $tiepla = $tieacu + $pernoc[$i][1];

	 $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
			       (num_despac,cod_rutasx,cod_contro,fec_planea,
				fec_alarma,usr_creaci,fec_creaci,usr_modifi,
				fec_modifi)
                   	VALUES (".$_REQUEST[despac].",'".$_REQUEST[rutasx]."','".$pernoc[$i][0]."',
                     		DATE_ADD('$fecpla', INTERVAL ".$tiepla." MINUTE),
                     		DATE_ADD('$fecpla', INTERVAL ".$tiepla." MINUTE),
                     		'".$_REQUEST[usuario]."','$fec_actual',NULL,NULL)";

    	 $insercion = new Consulta($query, $this -> conexion,"R");

	 if($pernoc[$i][3] > 0)
	 {
	  $query = "INSERT INTO ".BASE_DATOS.".tab_despac_pernoc
			        (num_despac,cod_rutasx,cod_contro,cod_noveda,
				 val_pernoc,usr_creaci,fec_creaci,usr_modifi,
				 fec_modifi)
			 VALUES (".$_REQUEST[despac].",".$_REQUEST[rutasx].",".$pernoc[$i][0].",
				 ".$pernoc[$i][2].",".$pernoc[$i][3].",'".$_REQUEST[usuario]."',
				 '".$fec_actual."',NULL,NULL)
		   ";

	  $insercion = new Consulta($query, $this -> conexion,"R");
	 }

	 if($pernoc[$i][3] > 0)
	  $tieacu += $pernoc[$i][3];
   }

   $ultimo = $pernoc[sizeof($pernoc)-1][1] + $tieacu;

   $query = "SELECT DATE_ADD('$fecpla', INTERVAL ".$ultimo." MINUTE)
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $timlle = $consulta -> ret_matriz();

   if($pcontro_urban)
   {
   	for($i = 0; $i < sizeof($pcontro_urban); $i++)
   	{
   	 $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
			       		   (num_despac,cod_rutasx,cod_contro,fec_planea,
							fec_alarma,usr_creaci,fec_creaci,usr_modifi,
							fec_modifi)
                   	VALUES (".$_REQUEST[despac].",'".$_REQUEST[rutasx]."','".$pcontro_urban[$i]."',
                     		DATE_ADD('".$timlle[0][0]."', INTERVAL ".$pctime_urban[$i]." MINUTE),
                     		DATE_ADD('".$timlle[0][0]."', INTERVAL ".$pctime_urban[$i]." MINUTE),
                     		'".$_REQUEST[usuario]."','$fec_actual',NULL,NULL)";

     $insercion = new Consulta($query, $this -> conexion,"R");

     $ultimo = $pctime_urban[$i];
   	}

   	$query = "SELECT DATE_ADD('".$timlle[0][0]."', INTERVAL ".$ultimo." MINUTE)
	     ";

    $consulta = new Consulta($query, $this -> conexion);
    $timlle = $consulta -> ret_matriz();
   }

   /* NUEVA VALIDACION DE CORONA   ********************************/
   $det_feccit = explode( ' ', $_REQUEST['fec_citcar'] );
   $mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_despac
                  SET fec_citcar = '".$det_feccit[0]."',
                      hor_citcar = '".$det_feccit[1]."'
                WHERE num_despac = '".$_REQUEST[despac]."'";

   $insercion = new Consulta($mUpdate, $this -> conexion,"R");
   /***************************************************************/
   
   $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
		SET fec_salipl = '".$fecpla."',
		    fec_llegpl = '".$timlle[0][0]."',
		    cod_rutasx = '".$_REQUEST[rutasx]."',
		    ind_activo = 'S',
		    usr_modifi = '".$_REQUEST[usuario]."',
		    fec_modifi = '".$fec_actual."'
	      WHERE num_despac = '".$_REQUEST[despac]."'
	    ";

   $insercion = new Consulta($query, $this -> conexion,"R");

   if($_REQUEST[ciudestr])
   {
   	$query = "SELECT a.cod_paisxx,a.cod_depart
   			    FROM ".BASE_DATOS.".tab_genera_ciudad a
   			   WHERE a.cod_ciudad = ".$_REQUEST[ciudestr]."
   			 ";

   	$consulta = new Consulta($query, $this -> conexion);
    $infpaide = $consulta -> ret_matriz();
   }

   $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
				SET ind_planru = 'S',
		    		obs_despac = '".$_REQUEST[obs]."',";
 		if($_REQUEST[ciudestr])
 		 $query .= "cod_paides = ".$infpaide[0][0].",
		    	    cod_depdes = ".$infpaide[0][1].",
		    	    cod_ciudes = ".$_REQUEST[ciudestr].",";
 		 $query .= "usr_modifi = '".$_REQUEST[usuario]."',
		    	    fec_modifi = '".$fec_actual."'
	      	  WHERE num_despac = '".$_REQUEST[despac]."'
	    	";

   $insercion = new Consulta($query, $this -> conexion,"R");

   if($consulta = new Consulta("COMMIT", $this -> conexion))
   {
      /*
      * Se le da salida inmediata si tiene seleccionado el check despues de asignar pla de ruta
      */
     
     if( $_REQUEST[ind_salida] == '1' )
     {
       $fecsalida = $_REQUEST[fecsalida];

       $fecsalida = str_replace("/","-",$fecsalida);
       
       $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
  	 	  SET fec_salipl = '".$fecsalida."',
  		    ind_activo = 'S',
  		    usr_modifi = '".$_REQUEST[usuario]."',
  		    fec_modifi = '".$fec_actual."'
  	      WHERE num_despac = '".$_REQUEST[despac]."'
  	    ";
  
       $insercion = new Consulta($query, $this -> conexion,"R");
  
       $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
  	 	  SET fec_salida= '".$fecsalida."',
  		    usr_modifi = '".$_REQUEST[usuario]."',
  		    fec_ultnov = '".$fecsalida."',
                  fec_salsis= NOW(),
  		    fec_modifi = '".$fec_actual."'
  	      WHERE num_despac = '".$_REQUEST[despac]."'
  	    ";
  
       $insercion = new Consulta($query, $this -> conexion,"R");
       
       if($consulta = new Consulta("COMMIT", $this -> conexion))
       {
         /*******
         * 
         *  Se crea el despacho en Destino Seguro
         * 
         * *****/
          $query = "SELECT b.nom_usuari, b.clv_usuari, a.cod_transp
        	      FROM ".BASE_DATOS.".tab_despac_vehige a,
                     ".BASE_DATOS.".tab_interf_parame b
        	     WHERE a.num_despac = '".$_REQUEST[despac]."'
                     AND a.cod_transp = b.cod_transp
                     AND b.cod_operad = '35' ";
      
          $consulta = new Consulta($query, $this -> conexion);
          $datos_ds = $consulta -> ret_matriz();
          
          if( $datos_ds ) 
          {
            include( "kd_xmlrpc.php" );
            
            define( "XMLRPC_DEBUG", true );
            define( "SITEDS", "www.destinoseguro.net" );
            define( "LOCATIONDS", "/WS/server.php" );
            
            $query = "SELECT a.cod_manifi, a.cod_ciuori, a.cod_ciudes,
                             b.num_placax, c.cod_marcax, c.cod_carroc,
                             c.cod_colorx, c.ano_modelo, b.cod_conduc,
                             d.nom_tercer, d.nom_apell1,
                             d.dir_domici, d.num_telef1, d.num_telmov, IF(a.cod_client IS NULL, '0',a.cod_client) as cod_client,
                             IF(a.cod_client IS NULL,'',e.abr_tercer) as abr_tercer, a.obs_despac
        	      FROM ".BASE_DATOS.".tab_despac_vehige b,
                     ".BASE_DATOS.".tab_vehicu_vehicu c,
                     ".BASE_DATOS.".tab_tercer_tercer d,
                     ".BASE_DATOS.".tab_despac_despac a LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer e ON a.cod_client = e.cod_tercer
        	     WHERE a.num_despac = '".$_REQUEST[despac]."'
                 AND a.num_despac = b.num_despac
                 AND b.num_placax = c.num_placax
                 AND b.cod_conduc = d.cod_tercer ";
      
            $consulta = new Consulta( $query, $this -> conexion );
            $despacho = $consulta -> ret_matriz();
            
            $datosDespac['usuario'] = $datos_ds[0][0];
            $datosDespac['clave'] = $datos_ds[0][1];
            $datosDespac['fecha'] = date( "Y-m-d", strtotime( $fecsalida ) );
            $datosDespac['hora'] = date( "H:i:s", strtotime( $fecsalida ) );
            $datosDespac['nittra'] = $datos_ds[0][2];
            $datosDespac['manifiesto'] = $despacho[0][0];
            $datosDespac['ruta'] = $despacho[0][1]."-".$despacho[0][2];
            $datosDespac['placa'] = $despacho[0][3];
            $datosDespac['c_marca'] = $despacho[0][4];
            $datosDespac['c_carroc'] = $despacho[0][5];
            $datosDespac['c_color'] = $despacho[0][6];
            $datosDespac['tipo_v'] = '2';
            $datosDespac['modelo'] = $despacho[0][7];
            $datosDespac['cedula'] = $despacho[0][8];
            $datosDespac['nombres'] = $despacho[0][9];
            $datosDespac['apellidos'] = $despacho[0][10];
            $datosDespac['direccion'] = $despacho[0][11];
            $datosDespac['telefono'] = $despacho[0][12];
            $datosDespac['celular'] = $despacho[0][13];
            $datosDespac['nitgen'] = $despacho[0][14];
            $datosDespac['nomgen'] = $despacho[0][15];
            $datosDespac['c_sucur'] = '1';
            $datosDespac['observacion'] = $despacho[0][16];
      
            /* XMLRPC_prepare works on an array and converts it to XML-RPC parameters */
            list( $success, $response ) = XMLRPC_request
                                        ( SITEDS,
                                          LOCATIONDS,
                                          'wsds.InsertarDespacho',
                                          array( XMLRPC_prepare( $datosDespac ),
                                                 'HarryFsXMLRPCClient' )
                                        );
            $mReturn = explode( "-", $response['faultString'] );
            
            if( 0 == $mReturn[0] )
            {
              $mMessage = "******** Encabezado ******** \n";
              $mMessage .= "Fecha y hora: ".date( "Y-m-d H:i" )." \n";
              $mMessage .= "Empresa de transporte: ".$datosDespac['nittra']." \n";
              $mMessage .= "Numero de manifiesto: ".$datosDespac['manifiesto']." \n";
              $mMessage .= "Placa del vehiculo: ".$datosDespac['placa']." \n";
              $mMessage .= "Conductor del vehiculo: ".$datosDespac['cedula']." \n";
              $mMessage .= "Ruta: ".$datosDespac['ruta']." \n";
              $mMessage .= "******** Detalle ******** \n";
              $mMessage .= "Codigo de error: ".$mReturn[1]." \n";
              $mMessage .= "Mesaje de error: ".$mReturn[2]." \n";
              mail( "soporte.ingenieros@intrared.net", "Web service Trafico-Destino seguro", $mMessage,'From: soporte.ingenieros@intrared.net' );
            }
            //print_r($response);
          }
  
          /*******
           * 
           *  Fin Interfaz Destino Seguro
           * 
           * *****/
       }
     }
     
     
     /*
      * Fin salida inmediata
      */
      
  
   	 $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Insertar Otro Plan de Ruta</a></b>";

     if( $_REQUEST[ind_salida] == '1' && $consulta )
     {
          $consultaNit = "SELECT a.clv_filtro FROM ".BASE_DATOS.".tab_aplica_filtro_perfil a WHERE a.cod_perfil = ".$_SESSION['datos_usuario']['cod_perfil']." ";
          $nit = new Consulta($consultaNit, $this->conexion);
          $nit = $nit->ret_matriz();
          $nit = $nit[0]['clv_filtro'];

          if ($this->getInterfParame('85', $nit) == true)
          {         

            require_once URL_ARCHIV_STANDA."/interf/app/APIClienteApp/controlador/DespachoControlador.php";
            $controlador = new DespachoControlador();
            $response    = $controlador->registrar($this->conexion, $_REQUEST[despac], $nit);
            $mensaje     = $response->msg_respon;

            $mens = new mensajes();
            if ($response->cod_respon == 1000) {

              $mens->correcto("REGISTRO MOVIL", $mensaje);

            } else {
              $mens->advert("REGISTRO MOVIL", $mensaje);
            }
          }
          $mensaje = "Se Genero el Plan de Ruta y se le dio salida inmediata Para el Despacho # <b>".$_REQUEST[despac]."</b> Exitosamente.";
     }
     else
       $mensaje = "Se Genero el Plan de Ruta Para el Despacho # <b>".$_REQUEST[despac]."</b> Exitosamente.";
     
     $mens = new mensajes();
     $mens -> correcto("PLAN DE RUTA", $mensaje);
   }
   
   
   
   
 }

 function changeDuplicateValDuraci( $matriz )
 {
    do
    {
      $continue = FALSE;
      for( $i = 0, $total = count( $matriz ); $i < $total; $i++ )
      {
        for( $j = $i +1; $j < $total; $j++ )
        {
          if( $matriz[$i]['val_duraci'] == $matriz[$j]['val_duraci'] )
          {
            $matriz[$j]['val_duraci'] = $matriz[$j]['val_duraci'] + 2;
            $matriz[$j][1] = $matriz[$j]['val_duraci'];
            $continue = TRUE;
          }
        }
      }
    } while( $continue == TRUE );
    return $matriz;
 }

  //---------------------------------------------
  /*! \fn: getInterfParame
   *  \brief:Verificar la interfaz con destino seguro esta activa
   *  \author: Nelson Liberato
   *  \date: 21/12/2015
   *  \date modified: 21/12/2015
   *  \return BOOL
   */
  function getInterfParame($mCodInterf = NULL, $nit = NULL) {
    $mSql = "SELECT ind_estado
                   FROM ".BASE_DATOS.".tab_interf_parame a
                  WHERE a.cod_operad = '".$mCodInterf."'
                    AND a.cod_transp = '".$nit."'";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = $mMatriz->ret_matriz("a");
    return $mMatriz[0]['ind_estado'] == '1'?true:false;
  }

}//FIN CLASE

   $proceso = new Proc_Plan_Ruta($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);



?>