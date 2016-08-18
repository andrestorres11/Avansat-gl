<?php

class eli_conduc_conduc
{
 var $conexion,
 	 $cod_aplica,
     $usuario;//una conexion ya establecida a la base de datos

 //Metodos
 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }

//********METODOS
 function principal()
 {
      switch($GLOBALS[opcion])
      {
        case "1":
          $this -> Resultado();
        break;

        case "2":
          $this -> Datos();
        break;

        case "3":
          $this -> Eliminar();
        break;

        default:
          $this -> Buscar();
        break;
     }//FIN SWITCH
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
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
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

  if($GLOBALS[transp])
   $query .= " AND b.cod_transp = '".$GLOBALS[transp]."'";
  if($GLOBALS[fil] == 1)
   $query = $query." AND a.cod_tercer = '".$GLOBALS[tercer]."'";
  else if($GLOBALS[fil] == 2)
   $query = $query." AND a.abr_tercer LIKE '%".$GLOBALS[tercer]."%'";
  else if($GLOBALS[fil] == 3)
   $query = $query." AND a.cod_estado = ".COD_ESTADO_ACTIVO."";
  else if($GLOBALS[fil] == 4)
   $query = $query." AND a.cod_estado = ".COD_ESTADO_INACTI."";
  else if($GLOBALS[fil] == 5)
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

  $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);

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

   $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&conduc=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

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
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }

 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   //datos
          $query = "SELECT a.cod_tercer,a.abr_tercer,
                    a.abr_tercer, a.dir_domici, b.nom_ciudad,
                    a.num_telef1, IF(e.cod_tipsex = 1,'Masculino','Femenino'), a.num_telmov, e.num_licenc,
                    e.fec_venlic, IF(cod_estado = 1,'Habilitado','Deshabilitado'),
                    a.obs_tercer, d.nom_activi, a.dir_ultfot
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_tercer_conduc e,
                    ".BASE_DATOS.".tab_genera_ciudad b,
                    ".BASE_DATOS.".tab_tercer_activi c,
                    ".BASE_DATOS.".tab_genera_activi d
              WHERE a.cod_ciudad = b.cod_ciudad AND
                    a.cod_tercer = c.cod_tercer AND
                    a.cod_tercer = e.cod_tercer AND
                    c.cod_activi = d.cod_activi AND
                    a.cod_tercer = '$GLOBALS[conduc]'
              ORDER BY 7";
        $consec = new Consulta($query, $this -> conexion);
          $matriz = $consec -> ret_matriz();

   $query = "SELECT a.cod_conduc
   		       FROM ".BASE_DATOS.".tab_despac_vehige a
   		      WHERE a.cod_conduc = '".$GLOBALS[conduc]."'
   		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $existdes = $consulta -> ret_matriz();

   $query = "SELECT a.cod_conduc
   		       FROM ".BASE_DATOS.".tab_vehicu_vehicu a
   		      WHERE a.cod_conduc = '".$GLOBALS[conduc]."'
   		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $existveh = $consulta -> ret_matriz();

   $formulario = new Formulario ("index.php","post","CONDUCTORES","form_conduc");

   $formulario -> linea("Datos del Conductor",1,"t2");

   if($matriz[0][13])
    echo "<td align=\"center\" class=\"celda\"><img src=\"".URL_CONDUC.$matriz[0][13]."\" alt=\"fotografia\" width=\"80\" height=\"100\"></td>";
   else
 	echo "<td align=\"center\" class=\"celda\" colspan=\"2\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/conduc.jpg\" alt=\"fotografia\" width=\"80\" height=\"100\" align=\"center\" ></td></tr><tr>";


   $formulario -> nueva_tabla();
   $formulario -> linea("No Identificaci&oacute;n",0,"25%");
   $formulario -> linea($matriz[0][0],0,"i","25%");
   $formulario -> linea("Nombre",0,"25%");
   $formulario -> linea($matriz[0][1],1,"i","25%");
   $formulario -> linea("Abreviatura",0);
   $formulario -> linea($matriz[0][2],0,"i");
   $formulario -> linea("Sexo",0);
   $formulario -> linea($matriz[0][6],1,"i");
   $formulario -> linea("Direcci&oacute;n",0);
   $formulario -> linea($matriz[0][3],0,"i");
   $formulario -> linea("Ciudad",0);
   $formulario -> linea($matriz[0][4],1,"i");
   $formulario -> linea("Tel&eacute;fono",0);
   $formulario -> linea($matriz[0][5],0,"i");
   $formulario -> linea("Tel&eacute;fono M&oacute;vil",0);
   $formulario -> linea($matriz[0][7],1,"i");
   $formulario -> linea("Licencia",0);
   $formulario -> linea($matriz[0][8],0,"i");
   $formulario -> linea("Vencimiento",0);
   $formulario -> linea($matriz[0][9],1,"i");
   $formulario -> linea("Estado",0);
   $formulario -> linea($matriz[0][10],0,"i");
   $formulario -> linea("Observaciones",0);
   $formulario -> linea($matriz[0][11],1,"i");

   $formulario -> nueva_tabla();
   $formulario -> linea("Actividades",1,"t2");

   for($i=0;$i<sizeof($matriz);$i++)
    $formulario -> linea($matriz[$i][12],1,"i");

   $formulario -> nueva_tabla();
   $formulario -> oculto("conduc",$matriz[0][0],0);
   $formulario -> oculto("nom",$matriz[0][1],0);

   if(!$existdes && !$existveh)
    $formulario -> botoni("Eliminar","if(confirm('Seguro que Desea Eliminar el Conductor')){form_conduc.submit()}",0);
   else
    $formulario -> linea("No es Posible Eliminar el Conductor, Puede que se Encuentre Asignado a un Vehiculo o Relacionado a un Despachos.",1,"e");

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",3,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }//FIN FUNCION DATOS

 function Eliminar()
 {
  $query = "DELETE FROM ".BASE_DATOS.".tab_tercer_conduc
                  WHERE cod_tercer = '$GLOBALS[conduc]'";

  $insercion = new Consulta($query, $this -> conexion,"BR");

  $query = "DELETE FROM ".BASE_DATOS.".tab_tercer_activi
                  WHERE cod_tercer = '".$GLOBALS[conduc]."' AND
                        cod_activi = ".COD_FILTRO_CONDUC."
           ";

  $insercion = new Consulta($query, $this -> conexion,"R");

  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
   $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Eliminar Otro Conductor</a></b>";

   $mensaje =  "Se Elimino el Conductor Exitosamente".$link_a;
   $mens = new mensajes();
   $mens -> correcto("ELIMINAR CONDUCTORES",$mensaje);
  }
 }

}//FIN CLASE eli_conduc_conduc

$proceso = new eli_conduc_conduc($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>