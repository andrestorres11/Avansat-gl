<?php

class Proc_trayle
{
 var $conexion,
 	 $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca )
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
     $this -> Resultado();
     break;
    case "2":
     $this -> Detalle();
     break;
   }
  }
 }

  function Buscar()
  {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $inicio[0][0] = 0;
   $inicio[0][1] = "-";

   $formulario = new Formulario ("index.php","post","BUSCAR REMOLQUES","form_act");
   $formulario -> linea("Seleccionar Filtro Para la Busqueda de Remolques",0,"t2");

   if($datos_usuario["cod_perfil"] == "")
    $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   else
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);

   if($filtro -> listar($this -> conexion))
   {
     $datos_filtro = $filtro -> retornar();
     $formulario -> oculto("transp",$datos_filtro[clv_filtro],0);
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

    if($GLOBALS[transp])
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

    $formulario -> nueva_tabla();
    $formulario -> lista("Transportadora","transp",$transpor,1);
   }

   $formulario -> nueva_tabla();
   $formulario -> radio("Remolque","fil",1,0,1);
   $formulario -> radio("Activos","fil",2,0,0);
   $formulario -> texto ("","text","remolq",1,50,255,"","");
   $formulario -> radio("Inactivos","fil",3,0,1);
   $formulario -> radio("Pendientes","fil",4,0,1);
   $formulario -> radio("Todos","fil",0,1,1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> botoni("Buscar","form_act.submit()",0);
   $formulario -> botoni("Cancelar","form_act.reset()",1);
   $formulario -> cerrar();
 }//FIN FUNCION BUSCAR

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT a.num_trayle,b.nom_martra,a.ano_modelo,a.nro_ejes,
  				   a.nom_propie,a.ind_estado
              FROM ".BASE_DATOS.".tab_vehige_trayle a,
              	   ".BASE_DATOS.".tab_vehige_martra b,
              	   ".BASE_DATOS.".tab_transp_trayle c
           	 WHERE a.cod_marcax = b.cod_martra AND
				   a.num_trayle = c.num_trayle
           ";

  if($GLOBALS[transp])
   $query .= " AND c.cod_transp = '".$GLOBALS[transp]."'";
  if($GLOBALS[fil] == 1)
   $query = $query." AND a.num_trayle LIKE '%".$GLOBALS[remolq]."%'";
  else if($GLOBALS[fil] == 2)
   $query = $query." AND a.ind_estado = ".COD_ESTADO_ACTIVO."";
  else if($GLOBALS[fil] == 3)
   $query = $query." AND a.ind_estado = ".COD_ESTADO_INACTI."";
  else if($GLOBALS[fil] == 4)
   $query = $query." AND a.ind_estado = ".COD_ESTADO_PENDIE."";

  $query = $query." GROUP BY 1 ORDER BY 2";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  $formulario = new Formulario ("index.php","post","LISTADO DE REMOLQUES","form_item");
  $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Remolque(s)",0,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Remolque",0,"t");
  $formulario -> linea("Marca",0,"t");
  $formulario -> linea("Modelo",0,"t");
  $formulario -> linea("Nro Ejes",0,"t");
  $formulario -> linea("Propietario",0,"t");
  $formulario -> linea("Estado",1,"t");

  for($i = 0; $i < sizeof($matriz); $i++)
  {
   if($matriz[$i][5] != COD_ESTADO_ACTIVO)
    $estilo = "ie";
   else
    $estilo = "i";

   if($matriz[$i][5] == COD_ESTADO_ACTIVO)
    $estado = "Activo";
   else if($matriz[$i][5] == COD_ESTADO_INACTI)
    $estado = "Inactivo";
   else if($matriz[$i][5] == COD_ESTADO_PENDIE)
    $estado = "Pendiente";

   $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&trayle=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   $formulario -> linea($matriz[$i][0],0,$estilo);
   $formulario -> linea($matriz[$i][1],0,$estilo);
   $formulario -> linea($matriz[$i][2],0,$estilo);
   $formulario -> linea($matriz[$i][3],0,$estilo);
   $formulario -> linea($matriz[$i][4],0,$estilo);
   $formulario -> linea($estado,1,$estilo);
  }

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }//FIN FUNCION

 function Detalle()
 {
  //Query para traer los datos del  Trayler
  $query = "SELECT a.num_trayle,b.nom_martra,a.ano_modelo,a.nro_ejes  ,a.tra_pesoxx,
                   a.tra_anchox,a.tra_altoxx,a.tra_largox,a.tra_volpos,a.tra_capaci,
                   a.tip_tramit,c.nom_carroc,a.ser_chasis,a.nom_propie,a.cod_config,
                   a.dir_fottra
           FROM ".BASE_DATOS.".tab_vehige_trayle a,".BASE_DATOS.".tab_vehige_martra b,
                ".BASE_DATOS.".tab_vehige_carroc c
           WHERE a.cod_marcax = b.cod_martra AND
                 a.cod_carroc = c.cod_carroc AND
                 a.num_trayle = '$GLOBALS[trayle]'";

   $consulta = new Consulta($query, $this -> conexion);
   $matriz = $consulta -> ret_matriz();


  $fecha = new Fecha();
  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/traylers.js\"></script>\n";

  $formulario = new Formulario ("index.php","post","REMOLQUE","form_trayle");

  $formulario -> nueva_tabla();
  $formulario -> linea("Datos B&aacute;sicos",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Remolque",0,"t");
  $formulario -> linea($matriz[0][0],0,"i");
  $formulario -> linea("Marca",0,"t");
  $formulario -> linea($matriz[0][1],1,"i");
  $formulario -> linea("Modelo",0,"t");
  $formulario -> linea($matriz[0][2],0,"i");
  $formulario -> linea("Nro Ejes",0,"t");
  $formulario -> linea($matriz[0][3],1,"i");
  $formulario -> linea("Peso Vacio",0,"t");
  $formulario -> linea($matriz[0][4],0,"i");
  $formulario -> linea("Ancho",0,"t");
  $formulario -> linea($matriz[0][5],1,"i");
  $formulario -> linea("Alto",0,"t");
  $formulario -> linea($matriz[0][6],0,"i");
  $formulario -> linea("Largo",0,"t");
  $formulario -> linea($matriz[0][7],1,"i");
  $formulario -> linea("Vol. Posterior",0,"t");
  $formulario -> linea($matriz[0][8],0,"i");
  $formulario -> linea("Capacidad",0,"t");
  $formulario -> linea($matriz[0][9],1,"i");
  $formulario -> linea("Tiempo Tramite",0,"t");
  $formulario -> linea($matriz[0][10],0,"i");
  $formulario -> linea("Carroceria",0,"t");
  $formulario -> linea($matriz[0][11],1,"i");
  $formulario -> linea("Serie Chasis",0,"t");
  $formulario -> linea($matriz[0][12],0,"i");
  $formulario -> linea("Propietario",0,"t");
  $formulario -> linea($matriz[0][13],1,"i");
  $formulario -> linea("Configuraci&oacute;n",0,"t");
  $formulario -> linea($matriz[0][14],0,"i");
  $formulario -> linea("",0,"t");
  $formulario -> linea("",1,"i");


  $formulario -> nueva_tabla();
  $formulario -> linea("Foto del Remolque",1,"t2");

  if(!$matriz[0][15])
   $matriz[0][15] = "../".DIR_APLICA_CENTRAL."/imagenes/vehicu.gif";
  else
   $matriz[0][15] = URL_REMOLQ.$matriz[0][15];

  echo "<td align=\"center\" class=\"celda\" rowspan=\"6\"><img src=\"".$matriz[0][15]."\" alt=\"fotografia\" width=\"120\" height=\"100\" align=\"center\" ></td>";


  $formulario -> nueva_tabla();
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("tercero","$tercero",0);

  $formulario -> oculto("listo",1,0);
  $formulario -> oculto("opcion",1,0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
  $formulario -> botoni("Volver","volver()",0);

  $formulario -> cerrar();

 }

 function Volver()
 {
  $this -> Buscar();
 }
}//FIN CLASE PROC_TRAYLE
     $proceso = new Proc_trayle($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
?>