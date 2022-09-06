<?php

class Hab_conduc_conduc
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
        case "1":
          $this -> Resultado();
          break;
        case "2":
          $this -> Datos();
          break;
        case "3":
          $this -> Actualizar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $inicio[0][0] = 0;
   $inicio[0][1] = "-";

   $formulario = new Formulario ("index.php","post","BUSCAR CONDUCTORES","form_act");
   $formulario -> linea("Seleccionar Filtro Para la Busqueda de Conductores",0,"t2");

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
   $formulario -> radio("C&eacute;dula","fil",1,0,1);
   $formulario -> radio("Nombre Tercero","fil",2,0,1);
   $formulario -> radio("Activos","fil",3,0,0);
   $formulario -> texto ("","text","tercer",1,50,255,"","");
   $formulario -> radio("Inactivos","fil",4,0,1);
   $formulario -> radio("Pendientes","fil",5,0,1);
   $formulario -> radio("Todos","fil",0,1,1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> botoni("Buscar","form_act.submit()",0);
   $formulario -> botoni("Cancelar","form_act.reset()",1);
   $formulario -> cerrar();
 }//FIN FUNCION BUSCAR

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT a.cod_tercer,a.nom_tercer,a.nom_apell1,a.nom_apell2,
                   a.cod_ciudad,a.num_telef1,a.num_telmov,a.dir_domici,
                   a.dir_emailx,a.cod_estado
              FROM ".BASE_DATOS.".tab_tercer_tercer a,
              	   ".BASE_DATOS.".tab_transp_tercer b,
              	   ".BASE_DATOS.".tab_tercer_conduc c
             WHERE a.cod_tercer = b.cod_tercer AND
             	   a.cod_tercer = c.cod_tercer
           ";

  if($_REQUEST[transp])
   $query .= " AND b.cod_transp = '".$_REQUEST[transp]."'";
  if($_REQUEST[fil] == 1)
   $query = $query." AND a.cod_tercer = '".$_REQUEST[tercer]."'";
  else if($_REQUEST[fil] == 2)
   $query = $query." AND a.abr_tercer LIKE '%".$_REQUEST[tercer]."%'";
  else if($_REQUEST[fil] == 3)
   $query = $query." AND a.cod_estado = ".COD_ESTADO_ACTIVO."";
  else if($_REQUEST[fil] == 4)
   $query = $query." AND a.cod_estado = ".COD_ESTADO_INACTI."";
  else if($_REQUEST[fil] == 5)
   $query = $query." AND a.cod_estado = ".COD_ESTADO_PENDIE."";

  $query = $query." GROUP BY 1 ORDER BY 2";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  $formulario = new Formulario ("index.php","post","LISTADO DE CONDUCTORES","form_item");
  $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Conductore(s)",0,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("CC",0,"t2");
  $formulario -> linea("Nombre",0,"t2");
  $formulario -> linea("Ciudad",0,"t2");
  $formulario -> linea("Telefono",0,"t2");
  $formulario -> linea("Celular",0,"t2");
  $formulario -> linea("Direcci&oacute;n",0,"t2");
  $formulario -> linea("E-mail",0,"t2");
  $formulario -> linea("Estado",1,"t2");

  $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);

  for($i = 0; $i < sizeof($matriz); $i++)
  {
   if($matriz[$i][9] != COD_ESTADO_ACTIVO)
    $estilo = "ie";
   else
    $estilo = "i";

   if($matriz[$i][9] == COD_ESTADO_ACTIVO)
    $estado = "Activo";
   else if($matriz[$i][9] == COD_ESTADO_INACTI)
    $estado = "Inactivo";
   else if($matriz[$i][9] == COD_ESTADO_PENDIE)
    $estado = "Pendiente";


   $ciudad_a = $objciud -> getSeleccCiudad($matriz[$i][4]);

   $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&conduc=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   $formulario -> linea($matriz[$i][0],0,$estilo);
   $formulario -> linea($matriz[$i][1]." ".$matriz[$i][2]." ".$matriz[$i][3],0,$estilo);
   $formulario -> linea($ciudad_a[0][1],0,$estilo);
   $formulario -> linea($matriz[$i][5],0,$estilo);
   $formulario -> linea($matriz[$i][6],0,$estilo);
   $formulario -> linea($matriz[$i][7],0,$estilo);
   $formulario -> linea($matriz[$i][8],0,$estilo);
   $formulario -> linea($estado,1,$estilo);
  }

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }

 function Datos()
 {
   //datos
          $query = "SELECT a.cod_tercer,a.abr_tercer,
                    a.abr_tercer, a.dir_domici, b.nom_ciudad,
                    a.num_telef1, IF(e.cod_tipsex = 1,'Masculino','Femenino'), a.num_telmov, e.num_licenc,
                    e.fec_venlic, IF(cod_estado = 1,'Habilitado','Deshabilitado'),
                    a.obs_tercer, d.nom_activi, a.dir_ultfot,a.cod_estado
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_conduc e,
                    ".BASE_DATOS.".tab_genera_ciudad b,
                    ".BASE_DATOS.".tab_tercer_activi c,
                    ".BASE_DATOS.".tab_genera_activi d
              WHERE a.cod_ciudad = b.cod_ciudad AND
                    a.cod_tercer = c.cod_tercer AND
                    a.cod_tercer = e.cod_tercer AND
                    c.cod_activi = d.cod_activi AND
                    a.cod_tercer = '$_REQUEST[conduc]'
              ORDER BY 7";
        $consec = new Consulta($query, $this -> conexion);
        $matriz = $consec -> ret_matriz();

   $formulario = new Formulario ("index.php","post","CONDUCTORES","form_conduc");

   $formulario -> linea("Datos del Conductor",1,"t2");
   if($matriz[0][13])
    echo "<td align=\"center\" class=\"celda\"><img src=\"".URL_CONDUC.$matriz[0][13]."\" alt=\"fotografia\" width=\"80\" height=\"100\"></td>";
   else
    echo "<td align=\"center\" class=\"celda\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/conduc.jpg\" alt=\"fotografia\" width=\"80\" height=\"100\"></td>";

   $formulario -> nueva_tabla();
   $formulario -> linea("No Identificaci&oacute;n",0,"t");
   $formulario -> linea($matriz[0][0],0,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($matriz[0][1],1,"i");
   $formulario -> linea("Abreviatura",0,"t");
   $formulario -> linea($matriz[0][2],0,"i");
   $formulario -> linea("Sexo",0,"t");
   $formulario -> linea($matriz[0][6],1,"i");
   $formulario -> linea("Direcci&oacute;n",0,"t");
   $formulario -> linea($matriz[0][3],0,"i");
   $formulario -> linea("Ciudad",0,"t");
   $formulario -> linea($matriz[0][4],1,"i");
   $formulario -> linea("Tel&eacute;fono",0,"t");
   $formulario -> linea($matriz[0][5],0,"i");
   $formulario -> linea("Tel&eacute;fono M&oacute;vil",0,"t");
   $formulario -> linea($matriz[0][7],1,"i");
   $formulario -> linea("Licencia",0,"t");
   $formulario -> linea($matriz[0][8],0,"i");
   $formulario -> linea("Vencimiento",0,"t");
   $formulario -> linea($matriz[0][9],1,"i");
   $formulario -> linea("Estado",0,"t");
   $formulario -> linea($matriz[0][10],0,"i");
   $formulario -> linea("Observaciones",0,"t");
   $formulario -> linea($matriz[0][11],1,"i");

   $formulario -> nueva_tabla();
   $formulario -> linea("Actividades",1,"t2");

   for($i=0;$i<sizeof($matriz);$i++)
    $formulario -> linea($matriz[$i][12],1,"i");

   $formulario -> nueva_tabla();
   $formulario -> linea("Estado Actual del Conductor",1,"t2");

   $estact = $estina = $estpen = 0;

   if($matriz[0][14] == COD_ESTADO_ACTIVO)
    $estact = 1;
   else if($matriz[0][14] == COD_ESTADO_INACTI)
    $estina = 1;
   else if($matriz[0][14] == COD_ESTADO_PENDIE)
    $estpen = 1;

   $formulario -> nueva_tabla();
   $formulario -> radio("Activo","estcon",COD_ESTADO_ACTIVO,$estact,0);
   $formulario -> radio("Inactivo","estcon",COD_ESTADO_INACTI,$estina,0);
   $formulario -> radio("Pendiente","estcon",COD_ESTADO_PENDIE,$estpen,1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("conduc",$_REQUEST[conduc],0);
   $formulario -> oculto("opcion",3,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> botoni("Aceptar","if(confirm('Esta Seguro de Modificar el Estado del Conductor.?')){form_conduc.submit()}",1);
   $formulario -> cerrar();
 }//FIN FUNCION DATOS

 function Actualizar()
 {

  $datos_usuario = $this -> usuario -> retornar();
  $usuario = $datos_usuario["cod_usuari"];

  if(!$_REQUEST[estcon])
   $_REQUEST[estcon] = 0;

  $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer
  			   SET cod_estado = '".$_REQUEST[estcon]."',
  			  	   usr_modifi = '".$usuario."',
  			  	   fec_modifi = NOW()
  			 WHERE cod_tercer = '".$_REQUEST[conduc]."'
  		   ";

  $insercion = new Consulta($query, $this -> conexion,"BR");

  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
   $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Activar/Inactivar Otro Conductor</a></b>";

   $mensaje =  "Se Actualizo el Conductor Exitosamente".$link_a;
   $mens = new mensajes();
   $mens -> correcto("ACTIVAR/INACTIVAR CONDUCTORES",$mensaje);
  }
 }

}//FIN CLASE
   $proceso = new Hab_conduc_conduc($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>