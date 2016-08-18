<?php

class Proc_tercer
{
 var $conexion,
     $usuario,
     $cod_aplica;

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
        case "2":
          $this -> Resultado();
          break;
        case "3":
          $this -> Datos();
          break;
        case "4":
          $this -> Actualizar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $formulario = new Formulario ("index.php","post","BUSCAR TRANSPORTADORAS","form_list");
   $formulario -> radio("NIT","fil",1,0,1);
   $formulario -> radio("Nombre","fil",2,0,1);
   $formulario -> radio("Activas","fil",3,0,0);
   $formulario -> texto ("","text","tercer",1,50,255,"","");
   $formulario -> radio("Inactivas","fil",4,0,1);
   $formulario -> radio("Pendientes","fil",5,0,1);
   $formulario -> radio("Todas","fil",0,1,1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> botoni("Buscar","form_list.submit()",0);
   $formulario -> cerrar();
 }

 function Resultado()
 {
   $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);

   $query = "SELECT a.cod_tercer,a.nom_tercer,a.num_telef1,a.cod_ciudad,a.dir_domici
              FROM ".BASE_DATOS.".tab_tercer_tercer a,
                   ".BASE_DATOS.".tab_tercer_activi b
             WHERE a.cod_tercer = b.cod_tercer AND
                   b.cod_activi = ".COD_FILTRO_EMPTRA."
             ";

   if($GLOBALS[fil] == 1)
    $query .= " AND a.cod_tercer = '".$GLOBALS[tercer]."'";
   else if($GLOBALS[fil] == 2)
    $query .= " AND a.abr_tercer LIKE '%".$GLOBALS[tercer]."%'";
   else if($GLOBALS[fil] == 3)
    $query .= " AND a.cod_estado = ".COD_ESTADO_ACTIVO."";
   else if($GLOBALS[fil] == 4)
    $query .= " AND a.cod_estado = ".COD_ESTADO_INACTI."";
   else if($GLOBALS[fil] == 5)
    $query .= " AND a.cod_estado = ".COD_ESTADO_PENDIE."";

   $query .= " GROUP BY 1 ORDER BY 2";

   $consec = new Consulta($query, $this -> conexion);
   $matriz = $consec -> ret_matriz();

   $formulario = new Formulario ("index.php","post","LISTADO DE TRANSPORTADORAS","form_item");
   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Transportadora(s).",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("NIT",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("Tel&eacute;fono",0,"t");
   $formulario -> linea("Ciudad",0,"t");
   $formulario -> linea("Direcci&oacute;n",1,"t");

   for($i = 0; $i < sizeof($matriz); $i++)
   {
    $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&tercer=".$matriz[$i][0]."&opcion=3 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
    $ciudad_a = $objciud -> getSeleccCiudad($matriz[$i][3]);

    $formulario -> linea($matriz[$i][0],0,"i");
    $formulario -> linea($matriz[$i][1],0,"i");
    $formulario -> linea($matriz[$i][2],0,"i");
    $formulario -> linea($ciudad_a[0][1],0,"i");
    $formulario -> linea($matriz[$i][4],1,"i");
   }

   $formulario -> nueva_tabla();
   $formulario -> botoni("Volver","javascript:history.go(-1)",0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("valor",$valor,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }


 function Datos()
 {
  $query = "SELECT a.cod_tercer,a.nom_tercer,a.abr_tercer,a.cod_ciudad,
  				   a.dir_domici,a.num_telef1,a.num_telef2,a.num_telmov,
  				   a.num_faxxxx,a.cod_estado
              FROM ".BASE_DATOS.".tab_tercer_tercer a
             WHERE a.cod_tercer = '".$GLOBALS[tercer]."'
           ";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
  $ciudad = $objciud -> getSeleccCiudad($matriz[0][3]);

  $estpen = $estact = $estina = 0;

  if($matriz[0][9] == COD_ESTADO_PENDIE)
  {
   $nomestado = "Pendiente";
   $estpen = 1;
  }
  else if($matriz[0][9] == COD_ESTADO_ACTIVO)
  {
   $nomestado = "Activa";
   $estact = 1;
  }
  else if($matriz[0][9] == COD_ESTADO_INACTI)
  {
   $nomestado = "Inactiva";
   $estina = 1;
  }

   $formulario = new Formulario ("index.php","post","ESTADO DE LA TRANSPORTADORA","form_item");

   $formulario -> linea("Datos de la Transportadora",0,"t2");
   $formulario -> nueva_tabla();
   $formulario -> linea("Nit o CC",0,"t");
   $formulario -> linea($matriz[0][0],0,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($matriz[0][1],1,"i");
   $formulario -> linea("Abreviatura",0,"t");
   $formulario -> linea($matriz[0][2],0,"i");
   $formulario -> linea("Ciudad",0,"t");
   $formulario -> linea($ciudad[0][1],1,"i");
   $formulario -> linea("Direcci&oacute;n",0,"t");
   $formulario -> linea($matriz[0][4],0,"i");
   $formulario -> linea("Tel&eacute;fono 1",0,"t");
   $formulario -> linea($matriz[0][5],1,"i");
   $formulario -> linea("Tel&eacute;fono 2",0,"t");
   $formulario -> linea($matriz[0][6],0,"i");
   $formulario -> linea("Tel&eacute;fono M&oacute;vil",0,"t");
   $formulario -> linea($matriz[0][7],1,"i");
   $formulario -> linea("Fax",0,"t");
   $formulario -> linea($matriz[0][8],0,"i");
   $formulario -> linea("Estado Actual",0,"t");
   $formulario -> linea($nomestado,1,"i");

   $formulario -> nueva_tabla();
   $formulario -> linea("Estado",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> radio("Pendiente","estado",COD_ESTADO_PENDIE,$estpen,0);
   $formulario -> radio("Activa","estado",COD_ESTADO_ACTIVO,$estact,0);
   $formulario -> radio("Inactiva","estado",COD_ESTADO_INACTI,$estina,0);

   $formulario -> nueva_tabla();
   $formulario -> linea("Observaciones",0,"t2");
   $formulario -> linea("",1,"t2");
   $formulario -> texto ("Observaciones:","textarea","obs",0,35,2,"","");

   $formulario -> nueva_tabla();
   $formulario -> botoni("Aceptar","if(form_item.obs.value != ''){if(confirm('Esta Seguro de Cambiar el Estado de la Transportadora.?')){form_item.submit()}}else{alert('Las Observaciones del Cambio de Estado son Obligatioras.')}",0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("tercer", $GLOBALS[tercer],0);
   $formulario -> oculto("opcion",4,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }

 function Actualizar()
 {
  $datos_usuario = $this -> usuario -> retornar();
  $usuario = $datos_usuario["cod_usuari"];

  if(!$GLOBALS[estado])
   $GLOBALS[estado] = 0;

  $query = "SELECT a.obs_aproba
  			  FROM ".BASE_DATOS.".tab_tercer_tercer a
  		     WHERE a.cod_tercer = '".$GLOBALS[tercer]."'
  		   ";

  $consec = new Consulta($query, $this -> conexion);
  $observ = $consec -> ret_matriz();

  $observ[0][0] .= "\n".$GLOBALS[obs];

  $query = "UPDATE ".BASE_DATOS.".tab_tercer_tercer
  		       SET cod_estado = '".$GLOBALS[estado]."',
  		           obs_aproba = '".$observ[0][0]."',
  		           usr_modifi = '".$usuario."',
  		           fec_modifi = NOW()
  		     WHERE cod_tercer = '".$GLOBALS[tercer]."'
  		   ";

  $consec = new Consulta($query, $this -> conexion,"BR");

  if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Activar/Inactivar Otra Transportadora</a></b>";

   $mensaje = "Se Actualizo el Estado de la Transportadora Exitosamente.".$link_a;
   $mens = new mensajes();
   $mens -> correcto("ACTIVA/INACTIVAR TRANSPORTADORA",$mensaje);
  }
 }

}//FIN CLASE
     $proceso = new Proc_tercer($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>