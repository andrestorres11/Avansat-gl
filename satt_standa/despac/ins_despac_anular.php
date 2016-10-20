<?php

class Proc_anula
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
   switch($_REQUEST[opcion])
   {
        case "1":
          $this -> Resultado();
          break;
        case "2":
          $this -> Formulario();
          break;
        case "3":
          $this -> Actualizar();
          break;
        default:
          $this -> Seleccion();
          break;
   }
 }

 function Seleccion()
 {
   $feactual = date("Y/m/d");

   $formulario = new Formulario ("index.php","post","OPCIONES DE ANULACION O REVERSION DE DESPACHOS","form_item","","","100%");

   $formulario -> nueva_tabla();
   $formulario -> linea("Escoga Una Opci&oacute;n",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> radio("Anular un despacho","fil",1,1,1);
   $formulario -> radio("Reversar Salida de un despacho","fil",2,0,1);
   $formulario -> radio("Reversar Llegada de un despacho","fil",3,0,1);
   $formulario -> caja ("Filtrar por Fecha","por_fecha","1",0,1);
   $formulario -> fecha_calendar("Fecha Inicial","fecini","form_item",$feactual,"yyyy/mm/dd",0);
   $formulario -> fecha_calendar("Fecha Final","fecfin","form_item",$feactual,"yyyy/mm/dd",1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);

   $formulario -> boton("Aceptar","button\" onClick=\"form_item.submit()",1);
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
                    ".BASE_DATOS.".tab_genera_ciudad c,
                    ".BASE_DATOS.".tab_genera_depart d,
                    ".BASE_DATOS.".tab_genera_paises e,
                    ".BASE_DATOS.".tab_despac_vehige b
             ";

        if($_REQUEST[fil] == 2)
         $query .= " LEFT JOIN ".BASE_DATOS.".tab_despac_noveda f ON b.num_despac = f.num_despac
                 LEFT JOIN ".BASE_DATOS.".tab_despac_contro g ON b.num_despac = g.num_despac
               ";

         $query .= "WHERE a.num_despac = b.num_despac AND
                    a.cod_ciuori = c.cod_ciudad AND
                    c.cod_depart = d.cod_depart AND
                    c.cod_paisxx = d.cod_paisxx AND
                    d.cod_paisxx = e.cod_paisxx AND
                    a.ind_anulad != 'A'
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

  if($_REQUEST[fil] == 1)
  {
   $query .= " AND a.fec_llegad IS NULL";
   $campfecha = "fec_despac";
  }
  else if($_REQUEST[fil] == 2)
  {
   $query .= " AND a.fec_salida IS NOT NULL AND a.fec_llegad IS NULL
   		       AND f.num_despac IS NULL
   		       AND g.num_despac IS NULL
   		     ";
   $campfecha = "fec_salida";
  }
  else if($_REQUEST[fil] == 3)
  {
   $query .= " AND a.fec_llegad IS NOT NULL";
   $campfecha = "fec_llegad";
  }

  if($_REQUEST[por_fecha])
  $query .= " AND a.".$campfecha." BETWEEN '".$fechaini."' AND '".$fechafin."'";

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
                    ".BASE_DATOS.".tab_genera_ciudad c,
                    ".BASE_DATOS.".tab_genera_depart d,
                    ".BASE_DATOS.".tab_genera_paises e,
                    ".BASE_DATOS.".tab_despac_vehige b
             ";

        if($_REQUEST[fil] == 2)
         $query .= " LEFT JOIN ".BASE_DATOS.".tab_despac_noveda f ON b.num_despac = f.num_despac
         		     LEFT JOIN ".BASE_DATOS.".tab_despac_contro g ON b.num_despac = g.num_despac
         		   ";

         $query .= "WHERE a.num_despac = b.num_despac AND
                    a.cod_ciudes = c.cod_ciudad AND
                    c.cod_depart = d.cod_depart AND
                    c.cod_paisxx = d.cod_paisxx AND
                    d.cod_paisxx = e.cod_paisxx AND
                    a.ind_anulad != 'A'
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

  if($_REQUEST[fil] == 1)
  {
   $query .= " AND a.fec_llegad IS NULL";
   $campfecha = "fec_despac";
  }
  else if($_REQUEST[fil] == 2)
  {
   $query .= " AND a.fec_salida IS NOT NULL AND a.fec_llegad IS NULL
   		       AND f.num_despac IS NULL
   		       AND g.num_despac IS NULL
   		     ";
   $campfecha = "fec_salida";
  }
  else if($_REQUEST[fil] == 3)
  {
   $query .= " AND a.fec_llegad IS NOT NULL";
   $campfecha = "fec_llegad";
  }

  if($_REQUEST[por_fecha])
  $query .= " AND a.".$campfecha." BETWEEN '".$fechaini."' AND '".$fechafin."'";

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
  $consec = new Consulta($query, $this -> conexion);?><script>alert(<?=$query?>);</script><?php
  $destinos = $consec -> ret_matriz();

  if($_REQUEST[ciudes])
   $destinos = array_merge($destinos,$todos);
  else
   $destinos = array_merge($titdes,$destinos);

   $query = "SELECT a.num_despac,a.cod_manifi,a.ind_anulad,a.cod_ciuori,
                    a.cod_ciudes,c.abr_tercer,b.num_placax,b.num_trayle,
                    d.abr_tercer
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_tercer_tercer c,
                    ".BASE_DATOS.".tab_tercer_tercer d,
                    ".BASE_DATOS.".tab_despac_vehige b
             ";

        if($_REQUEST[fil] == 2)
         $query .= " LEFT JOIN ".BASE_DATOS.".tab_despac_noveda f ON b.num_despac = f.num_despac
                 LEFT JOIN ".BASE_DATOS.".tab_despac_contro g ON b.num_despac = g.num_despac
               ";

         $query .= "WHERE a.num_despac = b.num_despac AND
                    b.cod_transp = c.cod_tercer AND
                    b.cod_conduc = d.cod_tercer AND
                    a.ind_anulad != 'A'
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

  if($_REQUEST[fil] == 1)
  {
   $query .= " AND a.fec_llegad IS NULL";
   $campfecha = "fec_despac";
  }
  else if($_REQUEST[fil] == 2)
  {
   $query .= " AND a.fec_salida IS NOT NULL AND a.fec_llegad IS NULL
   		       AND f.num_despac IS NULL
   		       AND g.num_despac IS NULL
   		     ";
   $campfecha = "fec_salida";
  }
  else if($_REQUEST[fil] == 3)
  {
   $query .= " AND a.fec_llegad IS NOT NULL";
   $campfecha = "fec_llegad";
  }

  if($_REQUEST[por_fecha])
  $query .= " AND a.".$campfecha." BETWEEN '".$fechaini."' AND '".$fechafin."'";

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
     $estado = "Inactivo";

    $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
    $ciudad_o = $objciud -> getSeleccCiudad($matriz[$i][3]);
    $ciudad_d = $objciud -> getSeleccCiudad($matriz[$i][4]);

    $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&despac=".$matriz[$i][0]."&fil=".$_REQUEST[fil]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

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
   $formulario -> oculto("fil",$_REQUEST[fil],0);
   $formulario -> oculto("fecini",$_REQUEST[fecini],0);
   $formulario -> oculto("fecfin",$_REQUEST[fecfin],0);
   $formulario -> oculto("por_fecha",$_REQUEST[por_fecha],0);

   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }

 function Formulario()
 {
  $datos_usuario = $this -> usuario -> retornar();

  if($_REQUEST[fil] == 1)
   $nomopera = "Anular el";
  else if($_REQUEST[fil] == 2)
   $nomopera = "Reversar la Salida del";
  else if($_REQUEST[fil] == 3)
   $nomopera = "Reversar la Llegada del";

  $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_ins");

  $formulario -> oculto("fil",$_REQUEST[fil],0);
  $formulario -> oculto("despac",$_REQUEST[despac],0);
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("opcion",3,0);
  $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);

  $listado_prin = new Despachos($_REQUEST[cod_servic],3,$this -> aplica,$this -> conexion,2);
  $listado_prin  -> Encabezado($_REQUEST[despac],$datos_usuario);

  $formulario -> nueva_tabla();
  $formulario -> texto("Observaciones","textarea","obs",1,24,2,"","");

  $formulario -> nueva_tabla();
  $formulario -> boton("Aceptar","button\" onClick=\"if(form_ins.obs.value != ''){if(confirm('Esta Seguro de ".$nomopera." Despacho.?')){form_ins.submit()}}else alert('Las Observaciones son Obligatorias.');",1);
  $formulario -> cerrar();

    echo '<tr><td><div id="AplicationEndDIV"></div>
          <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>
          <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
            <div id="filtros" ></div>
            <div id="result" ></div>
      </div><div id="alg"><table></table></div></td></tr>';

 }

 function Actualizar()
 {
  $datos_usuario = $this -> usuario -> retornar();
  $usuario = $datos_usuario["cod_usuari"];

  $fec_actual= date("Y-m-d H:i:s");

  $query = "SELECT a.num_placax,a.cod_rutasx
	          FROM ".BASE_DATOS.".tab_despac_vehige a
	     	 WHERE a.num_despac = ".$_REQUEST[despac]."
	   ";

  $consulta = new Consulta($query, $this -> conexion);
  $placax = $consulta -> ret_vector();

  $query = "SELECT a.cod_transp
  		      FROM ".BASE_DATOS.".tab_despac_vehige a
  		     WHERE a.num_despac = ".$_REQUEST[despac]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $transdes = $consulta -> ret_matriz();

  if($_REQUEST[fil] == 1)
  {
   $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                  SET ind_activo = 'N',
                      obs_anulad = '".$_REQUEST[obs]."\n',
                      usr_modifi = '".$usuario."',
                      fec_modifi = '".$fec_actual."'
                WHERE num_despac = ".$_REQUEST[despac]."
             ";

   $consulta = new Consulta($query, $this -> conexion,"BR");

   $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                SET  ind_anulad = 'A',
                     usr_modifi = '".$usuario."',
                     fec_modifi = '".$fec_actual."'
               WHERE num_despac = ".$_REQUEST[despac]."
            ";

   $update1 = new Consulta($query, $this -> conexion,"R");

	//Manejo de la Interfaz Aplicaciones SAT
/*  	$interfaz = new Interfaz_SAT(BASE_DATOS,$transdes[0][0],$usuario,$this -> conexion);

  	if($interfaz -> totalact > 0)
  	{
   	 for($i = 0; $i < $interfaz -> totalact; $i++)
   	 {
      $homolodespac = $interfaz -> getHomoloDespac($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$_REQUEST[despac]);

      if($homolodespac["DespacHomolo"] > 0)
      {
       $despac_ws["despac"] = $_REQUEST[despac];
       $despac_ws["fechax"] = $fec_actual;
       $despac_ws["observ"] = $_REQUEST[obs];
	   $despac_ws["tipoan"] = "1";

       $resultado_ws = $interfaz -> insAnular($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$despac_ws);

       if($resultado_ws["Confirmacion"] == "OK")
        $mensaje_sat .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">La Anulacion del Despacho Fue Registrada Exitosamente en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"]."</b>.";
       else
        $mensaje_sat .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">Se Presento un Error al Insertar la Anulacion del Despacho en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"]."</b>. :: ".$resultado_ws["Confirmacion"];
      }
   	 }
  	}
*/
	//Manejo de Interfaz GPS
/*  	$interf_gps = new Interfaz_GPS();
  	$interf_gps -> Interfaz_GPS_envio($transdes[0][0],BASE_DATOS,$usuario,$this -> conexion);

  	for($i = 0; $i  < $interf_gps -> cant_interf; $i++)
  	{
	 if($interf_gps -> getVehiculo($placax[0],$interf_gps -> cod_operad[$i][0],$transdes[0][0]))
	 {
	  $idgps = $interf_gps -> getIdGPS($placax[0],$interf_gps -> cod_operad[$i][0],$transdes[0][0]);

	  if($interf_gps -> setLlegadGPS($interf_gps -> cod_operad[$i][0],$transdes[0][0],$placax[0],$idgps,$_REQUEST[despac]))
	  {
	    $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">Se Finalizo Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0]."</b> Correctamente.";
	  }
	  else
	  {
	    $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">Ocurrio un Error al Finalizar Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0].".</b>";
	  }
	 }
  	}
*/
    if($consulta = new Consulta("COMMIT", $this -> conexion))
	{
	 $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Anular Otro Despacho</a></b>";

     $mensaje = "Se Anulo el Despacho <b>".$_REQUEST[despac]."</b> Exitosamente.".$mensaje_sat.$mensaje_gps;
     $mens = new mensajes();
     $mens -> correcto("ANULAR DESPACHOS", $mensaje.$link_a);
	}
  }

  if($_REQUEST[fil] == 2)
  {
   $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                SET ind_activo = 'R',
                    obs_anulad = '".$_REQUEST[obs]."\n',
                    usr_modifi = '".$usuario."',
                    fec_modifi = '".$fec_actual."'
              WHERE num_despac = ".$_REQUEST[despac]."
            ";

   $consultare = new Consulta($query, $this -> conexion,"BR");

   $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                SET fec_salida = NULL,
                    usr_modifi = '".$usuario."',
                    fec_modifi = '".$fec_actual."'
              WHERE num_despac = ".$_REQUEST[despac]."
            ";

   $update1 = new Consulta($query, $this -> conexion,"R");

   //Manejo de la Interfaz Aplicaciones SAT
/*   $interfaz = new Interfaz_SAT(BASE_DATOS,$transdes[0][0],$this -> usuario_aplicacion,$this -> conexion);

   if($interfaz -> totalact > 0)
   {
    for($i = 0; $i < $interfaz -> totalact; $i++)
   	{
     $homolodespac = $interfaz -> getHomoloDespac($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$_REQUEST[despac]);

     if($homolodespac["DespacHomolo"] > 0)
     {
      $despac_ws["despac"] = $_REQUEST[despac];
      $despac_ws["fechax"] = $fec_actual;
      $despac_ws["observ"] = $_REQUEST[obs];
	  $despac_ws["tipoan"] = "2";

      $resultado_ws = $interfaz -> insAnular($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$despac_ws);

      if($resultado_ws["Confirmacion"] == "OK")
       $mensaje_sat .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">La Reversion de la Salida Para el Despacho Fue Registrada Exitosamente en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"]."</b>.";
      else
       $mensaje_sat .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">Se Presento un Error al Insertar la Reversion de la Salida Para el Despacho en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"]."</b>. :: ".$resultado_ws["Confirmacion"];
     }
   	}
   }
*/
   //Manejo de Interfaz GPS
   /*$interf_gps = new Interfaz_GPS();
   $interf_gps -> Interfaz_GPS_envio($transdes[0][0],BASE_DATOS,$_REQUEST[usuario],$this -> conexion);

   for($i = 0; $i  < $interf_gps -> cant_interf; $i++)
   {
	if($interf_gps -> getVehiculo($placax[0],$interf_gps -> cod_operad[$i][0],$transdes[0][0]))
	{
	 $idgps = $interf_gps -> getIdGPS($placax[0],$interf_gps -> cod_operad[$i][0],$transdes[0][0]);

	 if($interf_gps -> setLlegadGPS($interf_gps -> cod_operad[$i][0],$transdes[0][0],$placax[0],$idgps,$_REQUEST[despac]))
	  $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">Se Finalizo Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0]."</b> Correctamente.";
	 else
	  $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">Ocurrio un Error al Finalizar Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0].".</b>";
	}
   }
*/
   if($consulta = new Consulta("COMMIT", $this -> conexion))
   {
   	$link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Reversar Otro Despacho</a></b>";

    $mensaje = "Se Reverso la Salida del Despacho <b>".$_REQUEST[despac]."</b> Exitosamente.".$mensaje_sat.$mensaje_gps;
    $mens = new mensajes();
    $mens -> correcto("REVERSAR SALIDA DE DESPACHOS", $mensaje.$link_a);
   }
  }

  if($_REQUEST[fil] == 3)
  {
   $nodasalida = 0;

   //Manejo de la Interfaz Aplicaciones SAT
/*   $interfaz = new Interfaz_SAT(BASE_DATOS,$transdes[0][0],$usuario,$this -> conexion);

   if($interfaz -> totalact > 0)
   {
    for($i = 0; $i < $interfaz -> totalact; $i++)
   	{
     $homolodespac = $interfaz -> getHomoloDespac($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$_REQUEST[despac]);

     if($homolodespac["DespacHomolo"] > 0)
     {
      $despac_ws["despac"] = $_REQUEST[despac];
      $despac_ws["fechax"] = $fec_actual;
      $despac_ws["observ"] = $_REQUEST[obs];
	  $despac_ws["tipoan"] = "3";
	  $despac_ws["placax"] = $placax[0];
	  $despac_ws["rutasx"] = $placax[1];

      $resultado_ws = $interfaz -> insAnular($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$despac_ws);

      if($resultado_ws["Confirmacion"] == "OK")
       $mensaje_sat .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">La Anulacion del Despacho Fue Registrada Exitosamente en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"]."</b>.";
      else
      {
       $mensaje_sat .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">Se Presento un Error al Insertar la Reversion de la Llegada Para el Despacho en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"]."</b>. :: ".$resultado_ws["Confirmacion"];
	     $nodasalida = 1;
      }
     }
   	}
   }
   */

   //Manejo de Interfaz GPS
   /*$interf_gps = new Interfaz_GPS();
   $interf_gps -> Interfaz_GPS_envio($transdes[0][0],BASE_DATOS,$_REQUEST[usuario],$this -> conexion);

   for($i = 0; $i  < $interf_gps -> cant_interf; $i++)
   {
	if($interf_gps -> getVehiculo($placax[0],$interf_gps -> cod_operad[$i][0],$transdes[0][0]))
	{
	 $idgps = $interf_gps -> getIdGPS($placax[0],$interf_gps -> cod_operad[$i][0],$transdes[0][0]);

	 if($interf_gps -> setSalidaGPS($interf_gps -> cod_operad[$i][0],$transdes[0][0],$placax[0],$idgps,$_REQUEST[despac]))
	 {
	  if($interf_gps -> setAcTimeRepor($interf_gps -> cod_operad[$i][0],$transdes[0][0],$placax[0],$idgps,$_REQUEST[despac],$fec_actual,$interf_gps -> val_timtra[$i][0]))
	   $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">Activado Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0].".</b>";
	 }
	 else
	  $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/error.gif\">Ocurrio un Error al Activar Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0].".</b>";
	}
	else
	 $mensaje_gps .= "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">No se Activo Seguimiento GPS Operador <b>".$interf_gps -> nom_operad[$i][0].". El Vehiculo no se Ecuentra Relacionado con el Operador.</b>";
   }
*/
   if(!$nodasalida)
   {
    $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                 SET fec_llegad = NULL,
                     usr_modifi = '".$usuario."',
                     fec_modifi = '".$fec_actual."'
               WHERE num_despac = ".$_REQUEST[despac]."
             ";

    $update1 = new Consulta($query, $this -> conexion,"BR");

	if($consulta = new Consulta("COMMIT", $this -> conexion))
    {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Reversar Otro Despacho</a></b>";

     $mensaje = "Se Reverso la Llegada del Despacho <b>".$_REQUEST[despac]."</b> Exitosamente.".$mensaje_sat.$mensaje_gps;
     $mens = new mensajes();
     $mens -> correcto("REVERSAR LLEGADA DE DESPACHOS", $mensaje.$link_a);
    }
   }
   else
   {
    $mens = new mensajes();
    $mens -> error("REVERSAR LLEGADA DE DESPACHOS", $mensaje_sat);
   }
  }
 }

}

$proceso = new Proc_anula($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
