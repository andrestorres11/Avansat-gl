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
          $this -> Eliminar();
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
  $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);

  $query = "SELECT a.cod_tercer,a.nom_tercer,a.abr_tercer,a.cod_ciudad,a.dir_domici,
                   a.num_telef1,a.num_telef2,a.num_telmov,a.num_faxxxx,d.nom_activi,e.nom_repleg,
                   e.cod_minins,e.num_resolu,e.fec_resolu,f.nom_terreg,e.num_region,e.ran_iniman,ran_finman,
                   e.ind_gracon,e.ind_ceriso,e.fec_ceriso,e.ind_cerbas,e.fec_cerbas,e.otr_certif,
                   e.ind_cobnal,e.ind_cobint,e.nro_habnal,e.fec_resnal
            FROM ".BASE_DATOS.".tab_tercer_tercer a,
                 ".BASE_DATOS.".tab_tercer_activi c,
            	 ".BASE_DATOS.".tab_genera_activi d,
                 ".BASE_DATOS.".tab_tercer_emptra e,
            	 ".BASE_DATOS.".tab_genera_terreg f
           WHERE a.cod_tercer = e.cod_tercer AND
                 a.cod_tercer = c.cod_tercer AND
                 c.cod_activi = d.cod_activi AND
                 a.cod_terreg = f.cod_terreg AND
                 a.cod_tercer = '$GLOBALS[tercer]'";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  $query = "SELECT a.cod_agenci,a.nom_agenci,a.con_agenci,a.dir_emailx,
  				   a.cod_ciudad,a.dir_agenci,a.tel_agenci,a.num_faxxxx
              FROM ".BASE_DATOS.".tab_genera_agenci a,
                   ".BASE_DATOS.".tab_transp_agenci b
             WHERE a.cod_agenci = b.cod_agenci AND
             	   b.cod_transp = '".$GLOBALS[tercer]."'
                   GROUP BY 1 ORDER BY 2";

  $consulta = new Consulta($query, $this -> conexion);
  $agencias = $consulta -> ret_matriz();

  $formulario = new Formulario ("index.php","post","INFORMACION DE LA TRANSPORTADORA","form_item");

  $formulario -> linea("Informaci&oacute;n B&aacute;sica de la Transportadora",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Nit o CC",0,"t");
   $formulario -> linea($matriz[0][0],0,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($matriz[0][1],1,"i");
   $formulario -> linea("Abreviatura",0,"t");
   $formulario -> linea($matriz[0][2],0,"i");
   $formulario -> linea("C&oacute;digo de Empresa",0,"t");
   $formulario -> linea($matriz[0][11],1,"i");
   $formulario -> linea("Direcci&oacute;n",0,"t");
   $formulario -> linea($matriz[0][4],0,"i");
   $formulario -> linea("Tel&eacute;fono",0,"t");
   $formulario -> linea($matriz[0][5],1,"i");
   $formulario -> linea("R&eacute;gimen",0,"t");
   $formulario -> linea($matriz[0][14],0,"i");

   $formulario -> nueva_tabla();
   $formulario -> linea("Actividades",1,"t2");

   $formulario -> nueva_tabla();
   for($i=0;$i<sizeof($matriz);$i++)
   {
    $formulario -> linea($matriz[$i][9],1,"i");
   }

   $formulario -> nueva_tabla();
   $formulario -> linea("Agencias",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("C&oacute;digo",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("Contacto",0,"t");
   $formulario -> linea("E-mail",0,"t");
   $formulario -> linea("Ciudad",0,"t");
   $formulario -> linea("Direcci&oacute;n",0,"t");
   $formulario -> linea("T&eacute;lefono",0,"t");
   $formulario -> linea("Fax",1,"t");

   for($i=0;$i<sizeof($agencias);$i++)
   {
   	$matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&tercer=".$matriz[$i][0]."&opcion=3 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
    $ciudad_a = $objciud -> getSeleccCiudad($agencias[$i][4]);

    $formulario -> linea($agencias[$i][0],0,"i");
    $formulario -> linea($agencias[$i][1],0,"i");
    $formulario -> linea($agencias[$i][2],0,"i");
    $formulario -> linea($agencias[$i][3],0,"i");
    $formulario -> linea($ciudad_a[0][1],0,"i");
    $formulario -> linea($agencias[$i][5],0,"i");
    $formulario -> linea($agencias[$i][6],0,"i");
    $formulario -> linea($agencias[$i][7],1,"i");
   }

   $query = "SELECT a.cod_transp
   			   FROM ".BASE_DATOS.".tab_genera_ruttra a
   			  WHERE a.cod_transp = '".$GLOBALS[tercer]."'
   			";

   $consulta = new Consulta($query, $this -> conexion);
   $existrut = $consulta -> ret_matriz();

   $query = "SELECT a.cod_transp
   			   FROM ".BASE_DATOS.".tab_despac_vehige a
   			  WHERE a.cod_transp = '".$GLOBALS[tercer]."'
   			";

   $consulta = new Consulta($query, $this -> conexion);
   $existdes = $consulta -> ret_matriz();

   $formulario -> nueva_tabla();
   $formulario -> oculto("tercer",$GLOBALS[tercer],0);
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",4,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

   $formulario -> nueva_tabla();

   if(!$existrut && !$existdes)
    $formulario -> botoni("Eliminar","if(confirm('Esta Seguro de Eliminar la Transportadora.?')){form_item.submit()}",0);
   else
    $formulario -> linea("Imposible Eliminar la Transportadora. Se Encuentra Asignada a una Ruta &oacute; a un Despacho.",1,"e");


   $formulario -> cerrar();
 }

 function Eliminar()
 {
  $query = "DELETE FROM ".BASE_DATOS.".tab_despac_vehige
  		          WHERE cod_transp = '".$GLOBALS[tercer]."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion,"BR");

  $query = "DELETE FROM ".BASE_DATOS.".tab_tercer_tercer
  		          WHERE cod_tercer = '".$GLOBALS[tercer]."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion,"R");

  if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Eliminar Otra Transportadora</a></b>";

   $mensaje = "Se Elimino la Transportadora Exitosamente.".$link_a;
   $mens = new mensajes();
   $mens -> correcto("ELIMINAR TRANSPORTADORA",$mensaje);
  }
 }

}

$proceso = new Proc_tercer($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>