<?php

class Proc_salida
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

     $this -> Listar();
  else
     {
      switch($GLOBALS[opcion])
       {
       	case "0":
          $this -> Listar();
          break;
        case "1":
          $this -> Formulario();
          break;
        case "2":
          $this -> Insertar();
          break;
       }
     }
 }

 function Listar()
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
                    d.cod_paisxx = e.cod_paisxx AND
                    a.ind_anulad != 'A' AND
                    a.ind_planru = 'S' AND
                    a.fec_salida IS NULL
            ";

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
                    d.cod_paisxx = e.cod_paisxx AND
                    a.ind_anulad != 'A' AND
                    a.ind_planru = 'S' AND
                    a.fec_salida IS NULL
            ";

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
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_despac_vehige b,
                    ".BASE_DATOS.".tab_tercer_tercer c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.num_despac = b.num_despac AND
                    b.cod_transp = c.cod_tercer AND
                    b.cod_conduc = d.cod_tercer AND
                    a.ind_anulad != 'A' AND
                    a.ind_planru = 'S' AND
                    a.fec_salida IS NULL
            ";

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
  $formulario -> texto("Despacho","text","numdes\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()",0,6,6,"",$GLOBALS[numdes],"","",1);
  $formulario -> texto("Documento/Despacho","text","manifi\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()",0,7,7,"",$GLOBALS[manifi],"","",1);
  $formulario -> linea("Estado",0,"t");
  $formulario -> lista_titulo("","ciuori\" onChange=\"form_item.submit()",$origenes,0);
  $formulario -> lista_titulo("","ciudes\" onChange=\"form_item.submit()",$destinos,0);
  $formulario -> linea("Transportadora",0,"t");
  $formulario -> texto("Vehiculo","text","vehicu\" onChange=\"form_item.submit()",0,6,6,"",$GLOBALS[vehicu],"","",1);
  $formulario -> texto("Remolque","text","trayle\" onChange=\"form_item.submit()",0,6,6,"",$GLOBALS[trayle],"","",1);
  $formulario -> linea("Conductor",1,"t");
  $formulario -> oculto("opcion",0,0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
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

    $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&despac=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

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

   
   $formulario -> cerrar();
 }//FIN FUNCION

 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

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

   $query = "SELECT a.cod_rutasx,a.fec_salipl
	       FROM ".BASE_DATOS.".tab_despac_vehige a
	      WHERE a.num_despac = ".$GLOBALS[despac]."
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $rutasx = $consulta -> ret_matriz();

   $query = "SELECT a.cod_contro,a.nom_contro,b.fec_planea,
		    if(a.ind_virtua = '0','Fisico','Virtual')
	       FROM ".BASE_DATOS.".tab_genera_contro a,
		    ".BASE_DATOS.".tab_despac_seguim b
	      WHERE a.cod_contro = b.cod_contro AND
		    b.num_despac = ".$GLOBALS[despac]." AND
		    b.cod_rutasx = ".$rutasx[0][0]."
		    ORDER BY b.fec_planea
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $pcontr = $consulta -> ret_matriz();

   $query = "SELECT a.cod_noveda,a.nom_noveda
	       FROM ".BASE_DATOS.".tab_genera_noveda a
	      WHERE a.ind_alarma = 'N' AND
		    a.ind_tiempo = '1'
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $noveda = $consulta -> ret_matriz();

   $noveda = array_merge($inicio,$noveda);

    $query = "SELECT cod_perfil
               FROM ".BASE_DATOS.".tab_autori_perfil
              WHERE cod_perfil = '".$this->usuario->cod_perfil."' AND
		    		cod_autori = '2'";

   $consulta = new Consulta($query, $this -> conexion);
   $parfecsal = $consulta -> ret_matriz();

   if(!$GLOBALS[fecprosal])
    $feactual = $rutasx[0][1];
   else
    $feactual = $GLOBALS[fecprosal];

   $feactual = str_replace("/","-",$feactual);

   $query = "SELECT if('".$feactual."' > NOW(),'1','0')
	        ";

   $consulta = new Consulta($query, $this -> conexion);
   $valfec = $consulta -> ret_matriz();


   #Inicio formulario
   $formulario = new Formulario ("index.php","post","SALIDA DE DESPACHOS","form_ins");

   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("despac",$GLOBALS[despac],0);
   $formulario -> oculto("rutasx",$rutasx[0][0],0);
   $formulario -> oculto("opcion",$GLOBALS[opcion],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> oculto("pernoctar_ind",1,0);
   $formulario -> oculto("transpor",NIT_TRANSPOR,0);

   $listado_prin = new Despachos($GLOBALS[cod_servic],2,$this -> aplica,$this -> conexion);
   $listado_prin  -> Encabezado($GLOBALS[despac],$datos_usuario);


   $formulario -> nueva_tabla();
   $formulario -> linea("Fecha Programada de Salida",1,"h");

   if(!$parfecsal && $valfec[0][0] == "1")
   {
    $feactual = date("Y-m-d H:i");
    $formulario -> nueva_tabla();
   	$formulario -> linea("<div align = \"center\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">No Puede Asignar una Fecha Posterior a la Actual Para la Salida de Vehiculos. Se Asignara La Fecha y Hora Actual</div>",1,"i");
   }

   $feccal = $feactual;

   $formulario -> nueva_tabla();
   $formulario -> fecha_calendar("Fecha/Hora","fecprosal","form_ins",$feactual,"yyyy/mm/dd hh:ii",1,1);

   $formulario -> nueva_tabla();
   $formulario -> linea("Tiempos de Pernoctaci&oacute;n",1,"h");

   $formulario -> nueva_tabla();
   $formulario -> linea("",0,"t");
   $formulario -> linea("",0,"t");
   $formulario -> linea("C&oacute;digo",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("Puesto",0,"t");
   $formulario -> linea("",0,"t");
   $formulario -> linea("Novedad",0,"t");
   $formulario -> linea("",0,"t");
   $formulario -> linea("Tiempo Estimado",0,"t");
   $formulario -> linea("Fecha y Hora Planeada",1,"t");

   $tiemacu = 0;
   $pcnovel = $GLOBALS[pcnove];
   $pctimel = $GLOBALS[pctime];

   for($i = 0; $i < sizeof($pcontr); $i++)
   {
	$query = "SELECT a.cod_noveda,a.nom_noveda,b.val_pernoc
		    FROM ".BASE_DATOS.".tab_genera_noveda a,
			 ".BASE_DATOS.".tab_despac_pernoc b
		   WHERE a.cod_noveda = b.cod_noveda AND
			 b.num_despac = ".$GLOBALS[despac]." AND
			 b.cod_contro = ".$pcontr[$i][0]." AND
			 b.cod_rutasx = ".$rutasx[0][0]."
		 ";

	$consulta = new Consulta($query, $this -> conexion);
   	$pernoc = $consulta -> ret_matriz();

	$query = "SELECT a.val_duraci
		    FROM ".BASE_DATOS.".tab_genera_rutcon a,
			 ".BASE_DATOS.".tab_despac_vehige b
		   WHERE a.cod_rutasx = b.cod_rutasx AND
			 a.cod_contro = ".$pcontr[$i][0]." AND
			 b.num_despac = ".$GLOBALS[despac]."
		 ";

	$consulta = new Consulta($query, $this -> conexion);
   	$duraci = $consulta -> ret_matriz();

   	if($manredes && $desurb)
	{
	 $query = "SELECT a.cod_contro
	 		     FROM ".BASE_DATOS.".tab_destin_contro a,
	 		          ".BASE_DATOS.".tab_despac_remdes b
	 		    WHERE a.cod_remdes = b.cod_remdes AND
	 		          a.cod_contro = ".$pcontr[$i][0]." AND
	 		          b.num_despac = ".$GLOBALS[despac]."
	 		  ";

	 $consulta = new Consulta($query, $this -> conexion);
   	 $aplicaurb = $consulta -> ret_matriz();
	}
	else
	 $aplicaurb = NULL;

        if($GLOBALS[pcnove] && $pcontr[$i][0] != CONS_CODIGO_PCLLEG && !$aplicaurb)
	{
	 $pernoc[0][2] = $pctimel[$i];

	 $query = "SELECT a.cod_noveda,a.nom_noveda
		     FROM ".BASE_DATOS.".tab_genera_noveda a
		    WHERE a.cod_noveda = ".$pcnovel[$i]."
		 ";

	$consulta = new Consulta($query, $this -> conexion);
   	$novselant = $consulta -> ret_matriz();

	 $nuenov = array_merge($novselant,$noveda);
	}
	else
	{
	 $nuenov = array_merge($pernoc,$noveda);
	}

	$tiempcum = $tiemacu + $duraci[0][0];

	$query = $query = "SELECT DATE_ADD('".$feccal."', INTERVAL ".$tiempcum." MINUTE)
		 	  ";

	$consulta = new Consulta($query, $this -> conexion);
   	$timemost = $consulta -> ret_matriz();

	$tiemacu += $pernoc[0][2];

	if($aplicaurb)
	 $matriz_urbanos[] = $pcontr[$i];
	else
	{
	 $formulario -> caja("","pcontro[$i]\" disabled ",$pcontr[$i][0],1,0);

	 if($pcontr[$i][0] == CONS_CODIGO_PCLLEG)
	 {
	  $formulario -> linea("-",0,"i");
	  $formulario -> linea($pcontr[$i][1],0,"i");
	  $formulario -> linea($pcontr[$i][3],0,"i");
	  $formulario -> linea("",0,"t");
	  $formulario -> linea("-",0,"i");
	  $formulario -> linea("",0,"t");
	  $formulario -> linea("-",0,"i");
	 }
	 else
	 {
	  $formulario -> linea($pcontr[$i][0],0,"i");
	  $formulario -> linea($pcontr[$i][1],0,"i");
	  $formulario -> linea($pcontr[$i][3],0,"i");
	  $formulario -> lista("","pcnove[$i]",$nuenov,0);
	  $formulario -> texto("","text","pctime[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_ins.submit()",0,10,4,"",$pernoc[0][2]);
	 }

	 $formulario -> linea($timemost[0][0],1,"i");
	 $formulario -> oculto("pcontr[$i]",$pcontr[$i][0],0);

	 $ultimo_tiempla = $pcontr[$i][2];
	 $ultimo_minutos = $tiempcum;
	}
   }

   if($matriz_urbanos)
   {
    $formulario -> nueva_tabla();
    $formulario -> linea("Puestos de Control Urbanos - Destinatarios",0,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("",0,"t");
    $formulario -> linea("",0,"t");
    $formulario -> linea("C&oacute;digo",0,"t");
    $formulario -> linea("Nombre",0,"t");
    $formulario -> linea("Puesto",0,"t");
    $formulario -> linea("Fecha y Hora Planeada",1,"t");

    for($i = 0; $i < sizeof($matriz_urbanos); $i++)
    {
     $query = "SELECT (TIME_TO_SEC(TIMEDIFF('".$matriz_urbanos[$i][2]."', '".$ultimo_tiempla."'))/60)
     		  ";

     $consulta = new Consulta($query, $this -> conexion);
   	 $tiempcum = $consulta -> ret_matriz();
   	 $tiempcum[0][0] += $ultimo_minutos;

     $query = "SELECT DATE_ADD('".$feccal."', INTERVAL ".$tiempcum[0][0]." MINUTE)
		 	  ";

	   $consulta = new Consulta($query, $this -> conexion);
   	 $timemost = $consulta -> ret_matriz();

     $formulario -> caja("","pcontro_urban[$i]\" disabled ",$matriz_urbanos[$i][0],1,0);
     $formulario -> linea($matriz_urbanos[$i][0],0,"i");
  	 $formulario -> linea($matriz_urbanos[$i][1],0,"i");
  	 $formulario -> linea($matriz_urbanos[$i][3],0,"i");
  	 $formulario -> linea($timemost[0][0],1,"i");
  	 $formulario -> oculto("pcontr_urban[$i]",$matriz_urbanos[$i][0],0);
  	 $formulario -> oculto("fecpla_urban[$i]",$timemost[0][0],0);
    }
   }


   $formulario -> nueva_tabla();
   $formulario -> linea("",1,"h");
   $formulario -> boton("Insertar","button\" onClick=\"aceptar_ins()",1);
   $formulario -> cerrar();

  //Para la carga del Popup
  echo '<div id="AplicationEndDIV"></div>
        <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
          <div id="result" ></div>
        </div>
        ';
 }

 function Insertar()
 {
     $datos_usuario = $this -> usuario -> retornar();
     $usuario=$datos_usuario["cod_usuari"];

     $fec_actual = date("Y-m-d H:i:s");
     $fecpla = $GLOBALS[fecprosal];

   $fecpla = str_replace("/","-",$fecpla);

   $formulario = new Formulario ("index.php","post","Salida de Despachos","form_item");

   $query = "SELECT cod_transp, num_placax
             FROM ".BASE_DATOS.".tab_despac_vehige
            WHERE num_despac = '".$GLOBALS[despac]."'";
    
   $consulta = new Consulta($query, $this -> conexion);
   $data_despac = $consulta -> ret_matriz();
   
   //Validacion de despacho en ruta para Megacarga
   if( $data_despac[0][0] == '830100365' )
   {
     $query = "SELECT 1 ".
               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                    "".BASE_DATOS.".tab_despac_vehige b ".
              "WHERE a.num_despac = b.num_despac ".
                "AND b.num_placax = '".$data_despac[0][1]."' ".
                "AND a.fec_salida IS NOT NULL ".
                "AND a.ind_anulad = 'R' ".
                "AND a.fec_llegad IS NULL ";
      
     $consulta = new Consulta($query, $this -> conexion);
     $enruta = $consulta -> ret_matriz();
     
     if( count( $enruta ) > 0 )
     {
       $mensaje =  "El Vechiculo con Placas ".$data_despac[0][1]." se Encuentra en Ruta Actualmente, Reporte Primero su Llegada";
       $mens = new mensajes();
       $mens -> advert("SALIDA DE DESPACHOS",$mensaje);
       die();
     }
   }
   
   $pcontro=$GLOBALS[pcontr];
   $pctime=$GLOBALS[pctime];
   $pcnove=$GLOBALS[pcnove];

   $pcontr_urban = $GLOBALS[pcontr_urban];
   $fecpla_urban = $GLOBALS[fecpla_urban];

   for($i = 0; $i < sizeof($pcontro); $i++)
   {
    $pc_interfSATT[$i][0] = $pcontro[$i];
    $pc_interfSATT[$i][1] = $pctime[$i];

    if($pcontro[$i] == CONS_CODIGO_PCLLEG)
     $pc_interfSATT[$i][2] = "0";
    else
     $pc_interfSATT[$i][2] = $pcnove[$i];
   }

   $query = "SELECT a.num_placax
	       FROM ".BASE_DATOS.".tab_despac_vehige a
	      WHERE a.num_despac = ".$GLOBALS[despac]."
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $placax = $consulta -> ret_matriz();

   $GLOBALS[placa] = $placax[0][0];

      $query = "DELETE FROM ".BASE_DATOS.".tab_despac_seguim
		   WHERE num_despac = ".$GLOBALS[despac]." AND
			 cod_rutasx = ".$GLOBALS[rutasx]."
	    ";

    $insercion = new Consulta($query, $this -> conexion,"BR");

    //La Eliminacion de los registros de pernoctacion relacionados a este plan de ruta
    //se eliminan en cascada.

    $tieacu = 0;

    for($i = 0; $i <= sizeof($pcontro); $i++)
    {
     if($pcontro[$i])
     {

	$query = "SELECT a.val_duraci
		    FROM ".BASE_DATOS.".tab_genera_rutcon a
		   WHERE a.cod_rutasx = ".$GLOBALS[rutasx]." AND
			 a.cod_contro = ".$pcontro[$i]."
		 ";

	$consulta = new Consulta($query, $this -> conexion);
   	$tieori = $consulta -> ret_matriz();

	$ultimo = $tieacu + $tieori[0][0];

	 $tiepla = $tieacu + $tieori[0][0];

	 $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
			       (num_despac,cod_rutasx,cod_contro,fec_planea,
				fec_alarma,usr_creaci,fec_creaci,usr_modifi,
				fec_modifi)
                   	VALUES (".$GLOBALS[despac].",'".$GLOBALS[rutasx]."','".$pcontro[$i]."',
                     		DATE_ADD('$fecpla', INTERVAL ".$tiepla." MINUTE),
                     		DATE_ADD('$fecpla', INTERVAL ".$tiepla." MINUTE),
                     		'".$GLOBALS[usuario]."','$fec_actual',NULL,NULL)";

    	 $insercion = new Consulta($query, $this -> conexion,"R");

	 if($pcnove[$i] != "0" && $pcontro[$i] != CONS_CODIGO_PCLLEG)
	 {
	  $query = "INSERT INTO ".BASE_DATOS.".tab_despac_pernoc
			        (num_despac,cod_rutasx,cod_contro,cod_noveda,
				 val_pernoc,usr_creaci,fec_creaci,usr_modifi,
				 fec_modifi)
			 VALUES (".$GLOBALS[despac].",".$GLOBALS[rutasx].",".$pcontro[$i].",
				 ".$pcnove[$i].",".$pctime[$i].",'".$GLOBALS[usuario]."',
				 '".$fec_actual."',NULL,NULL)
		   ";

	  $insercion = new Consulta($query, $this -> conexion,"R");
	 }

	 if($pcnove[$i] != "0")
	  $tieacu += $pctime[$i];
     }
    }

    for($i = 0; $i < sizeof($pcontr_urban); $i++)
    {
     $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
			       		   (num_despac,cod_rutasx,cod_contro,fec_planea,
							fec_alarma,usr_creaci,fec_creaci,usr_modifi,
							fec_modifi)
                   	VALUES (".$GLOBALS[despac].",'".$GLOBALS[rutasx]."','".$pcontr_urban[$i]."',
                     		'".$fecpla_urban[$i]."',
                     		'".$fecpla_urban[$i]."',
                     		'".$GLOBALS[usuario]."','$fec_actual',NULL,NULL)";

     $insercion = new Consulta($query, $this -> conexion,"R");
    }

    $query = "SELECT DATE_ADD('$fecpla', INTERVAL ".$ultimo." MINUTE)
	    ";

    $consulta = new Consulta($query, $this -> conexion);
    $timlle = $consulta -> ret_matriz();

    $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
		SET fec_salipl = '".$fecpla."',
		    ind_activo = 'S',
		    fec_llegpl = '".$timlle[0][0]."',
		    usr_modifi = '".$GLOBALS[usuario]."',
		    fec_modifi = '".$fec_actual."'
	      WHERE num_despac = '".$GLOBALS[despac]."'
	    ";

    $insercion = new Consulta($query, $this -> conexion,"R");

    $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
		SET fec_salida= '".$fecpla."',
        fec_salsis= NOW(),
		    usr_modifi = '".$GLOBALS[usuario]."',
		    fec_modifi = '".$fec_actual."',
        fec_ultnov = '".$fecpla."'
	      WHERE num_despac = '".$GLOBALS[despac]."'
	    ";

    $insercion = new Consulta($query, $this -> conexion,"R");

  $query = "SELECT a.cod_transp,a.num_placax,a.cod_rutasx
	      FROM ".BASE_DATOS.".tab_despac_vehige a
	     WHERE a.num_despac = ".$GLOBALS[despac]."
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $transpor = $consulta -> ret_matriz();
  
  
  
  /*******
   * 
   *  Se crea el despacho en Destino Seguro
   * 
   * *****/
    $query = "SELECT b.nom_usuari, b.clv_usuari, a.cod_transp
  	      FROM ".BASE_DATOS.".tab_despac_vehige a,
               ".BASE_DATOS.".tab_interf_parame b
  	     WHERE a.num_despac = '".$GLOBALS[despac]."'
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
  	     WHERE a.num_despac = '".$GLOBALS[despac]."'
           AND a.num_despac = b.num_despac
           AND b.num_placax = c.num_placax
           AND b.cod_conduc = d.cod_tercer ";

      $consulta = new Consulta( $query, $this -> conexion );
      $despacho = $consulta -> ret_matriz();
      
      $datosDespac['usuario'] = $datos_ds[0][0];
      $datosDespac['clave'] = $datos_ds[0][1];
      $datosDespac['fecha'] = date( "Y-m-d", strtotime( $fecpla ) );
      $datosDespac['hora'] = date( "H:i:s", strtotime( $fecpla ) );
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

//
  //Manejo de la Interfaz Aplicaciones SAT
/*  $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$this -> usuario_aplicacion,$this -> conexion);

  if($interfaz -> totalact > 0)
  {
   for($i = 0; $i < $interfaz -> totalact; $i++)
   {
    //query para traer el nombre de la ruta
    $query = "SELECT nom_rutasx
		FROM ".BASE_DATOS.".tab_genera_rutasx
	       WHERE cod_rutasx = ".$transpor[0][2]."
	     ";

    $consulta = new Consulta($query, $this -> conexion);
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
		WHERE a.num_despac = '".$GLOBALS[despac]."' AND
		      b.num_despac = a.num_despac AND
		      a.cod_agenci = c.cod_agenci
	      ";

     $consulta = new Consulta($query, $this -> conexion);
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
		WHERE a.num_despac = '".$GLOBALS[despac]."'
	      ";

     $consulta = new Consulta($query, $this -> conexion);
     $generador = $consulta -> ret_vector();

     if($generador)
     {
      $query = "SELECT a.cod_tercer,a.cod_tipdoc,a.nom_apell1,a.nom_apell2,
		       a.nom_tercer,a.abr_tercer,a.dir_domici,a.num_telef1,
		       a.num_telmov,a.dir_emailx,a.cod_ciudad,a.obs_tercer
		  FROM ".BASE_DATOS.".tab_tercer_tercer a
		 WHERE a.cod_tercer = '".$generador[0]."'
	       ";

      $consulta = new Consulta($query, $this -> conexion);
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
               WHERE b.num_despac = '".$GLOBALS[despac]."' AND
		     a.num_placax = b.num_placax AND
		     a.cod_propie = c.cod_tercer
	      ";

     $consulta = new Consulta($query, $this -> conexion);
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
               WHERE b.num_despac = '".$GLOBALS[despac]."' AND
		     a.num_placax = b.num_placax AND
		     a.cod_tenedo = c.cod_tercer
	      ";

     $consulta = new Consulta($query, $this -> conexion);
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
               WHERE b.num_despac = '".$GLOBALS[despac]."' AND
		     a.num_placax = b.num_placax AND
		     a.cod_conduc = c.cod_tercer AND
		     c.cod_tercer = d.cod_tercer
	      ";

     $consulta = new Consulta($query, $this -> conexion);
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
               WHERE b.num_despac = '".$GLOBALS[despac]."' AND
		     c.cod_tercer = b.cod_conduc AND
		     d.cod_tercer = c.cod_tercer
	      ";

     $consulta = new Consulta($query, $this -> conexion);
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
               WHERE a.num_despac = '".$GLOBALS[despac]."' AND
		     a.num_placax = b.num_placax
	      ";

     $consulta = new Consulta($query, $this -> conexion);
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
		      a.num_despac = '".$GLOBALS[despac]."'
	      ";

     $consulta = new Consulta($query, $this -> conexion);
     $datfin = $consulta -> ret_vector();

     $query = "SELECT a.cod_contro
		 FROM ".BASE_DATOS.".tab_despac_seguim a
		WHERE a.cod_rutasx = ".$datfin[6]." AND
		      a.num_despac = ".$GLOBALS[despac]."
		      ORDER BY a.fec_planea
	      ";

     $consulta = new Consulta($query, $this -> conexion);
     $planru_d = $consulta -> ret_matriz();

     $planru_ws = $planru_d[0][0];

     for($j = 1; $j < sizeof($planru_d); $j++)
      $planru_ws .= "|".$planru_d[$j][0];

     $query = "SELECT a.cod_contro,a.cod_noveda,a.val_pernoc
		 FROM ".BASE_DATOS.".tab_despac_pernoc a
		WHERE a.cod_rutasx = ".$datfin[6]." AND
		      a.num_despac = ".$GLOBALS[despac]."
	      ";

     $consulta = new Consulta($query, $this -> conexion);
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

     $despac_ws["despac"] = $GLOBALS[despac];
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
     {
      $mensaje_sat .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">El Vehiculo con Placas <b>".$GLOBALS[placa]."</b> ha Salido Correctamente en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].".";
     }
     else
     {
      $mensaje_sat .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">Existe un Error al Insertar el Despacho en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"]."</b>. :: ".$resultado_ws["Confirmacion"];
      $nopassint = 1;
     }
    }
    else
     $mensaje_sat .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">La Ruta <b>".$nomrut[0]."</b> no se Encuentra Actualmente Homologada en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].". No hay Seguimiento.</br>";
   }
  }
*/
  $query = "SELECT a.cod_transp
  		      FROM ".BASE_DATOS.".tab_despac_vehige a
  		     WHERE a.num_despac = ".$GLOBALS[despac]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $transdes = $consulta -> ret_matriz();

  //Manejo de Interfaz GPS
  /*$interf_gps = new Interfaz_GPS();
  $interf_gps -> Interfaz_GPS_envio($transdes[0][0],BASE_DATOS,$GLOBALS[usuario],$this -> conexion);

  for($i = 0; $i  < $interf_gps -> cant_interf; $i++)
  {
	if($interf_gps -> getVehiculo($GLOBALS[placa],$interf_gps -> cod_operad[$i][0],$transdes[0][0]))
	{
	 $idgps = $interf_gps -> getIdGPS($GLOBALS[placa],$interf_gps -> cod_operad[$i][0],$transdes[0][0]);

	 if($interf_gps -> setSalidaGPS($interf_gps -> cod_operad[$i][0],$transdes[0][0],$GLOBALS[placa],$idgps,$GLOBALS[despac]))
	 {
	    if($interf_gps -> setAcTimeRepor($interf_gps -> cod_operad[$i][0],$transdes[0][0],$GLOBALS[placa],$idgps,$GLOBALS[despac],$fec_actual,$interf_gps -> val_timtra[$i][0]))
	    {
	    	$mensaje_gps = "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">Activado Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0].".</b>";
	    }
	 }
	 else
	 {
	    $mensaje_gps = "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">Ocurrio un Error al Activar Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0].".</b>";
	 }
	}
	else
	{
	 $mensaje_gps = "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">No se Activo Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0]."</b>. El Vehiculo no se Ecuentra Relacionado con el Operador.";
	}
  }
*/
  if(!$nopassint)
  {
   if($consulta = new Consulta ("COMMIT", $this -> conexion))
   {
    $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Insertar Otra Salida</a></b>";

     $mensaje = "El Vehiculo <b>".$GLOBALS[placa]."</b> Asignado al Despacho # <b>".$GLOBALS[despac]."</b> Salio Exitosamente.".$mensaje_sat.$mensaje_gps;
     $mens = new mensajes();
     $mens -> correcto("SALIDA DE DESPACHOS", $mensaje);
   }
  }
  else
  {
   $mensaje =  $mensaje_sat."<br>".$mensaje_gps;
   $mens = new mensajes();
   $mens -> advert("SALIDA DE DESPACHOS",$mensaje);
  }


  $formulario -> cerrar();
 }

}//FIN CLASE PROC_DESCARGUE
     $proceso = new Proc_salida($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
