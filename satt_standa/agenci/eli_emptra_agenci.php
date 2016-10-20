<?php

class Mod_agencias
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
     $this -> Formulario();
     break;
    case "3":
     $this -> Eliminar();
     break;
   }
  }
 }

 function Buscar()
 {
  $datos_usuario = $this -> usuario -> retornar();

  $inicio[0][0] = 0;
  $inicio[0][1] = "-";

  $formulario = new Formulario ("index.php","post","BUSCAR AGENCIA","form_agenci");
  $formulario -> linea("Busqueda de Agencias",0,"t2");

  $formulario -> nueva_tabla();

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

   $formulario -> lista("Transportadora","transp",$transpor,1);
  }

  $formulario -> texto("Nombre Agencia","text","nombre",1,15,30,"","");

  $formulario -> nueva_tabla();
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("opcion",1,0);
  $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
  $formulario -> botoni("Aceptar","form_agenci.submit()",0);
 }

 function Resultado()
 {
   $query = "SELECT a.cod_agenci,a.nom_agenci,a.cod_ciudad,
		    		a.dir_agenci,a.tel_agenci,a.con_agenci,a.dir_emailx,
		    		a.num_faxxxx
               FROM ".BASE_DATOS.".tab_genera_agenci a,
                    ".BASE_DATOS.".tab_transp_agenci b
              WHERE a.cod_agenci = b.cod_agenci AND
              		a.nom_agenci LIKE '%".$_REQUEST[nombre]."%'
             ";

   if($_REQUEST[transp])
    $query .= " AND b.cod_transp = '".$_REQUEST[transp]."'";

   $query .= " GROUP BY 1 ORDER BY 1";

   $consec = new Consulta($query, $this -> conexion);
   $agencias = $consec -> ret_matriz();

   $formulario = new Formulario ("index.php","post","AGENCIAS","form_lista");

   $formulario -> linea("Se Encontro un Total de ".sizeof($agencias)." Agencia(s)",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("C&oacute;digo",0,"t");
   $formulario -> linea("Agencia",0,"t");
   $formulario -> linea("Ciudad",0,"t");
   $formulario -> linea("Direcci&oacute;n",0,"t");
   $formulario -> linea("Tel&eacute;fono",0,"t");
   $formulario -> linea("Contacto",0,"t");
   $formulario -> linea("E-mail",0,"t");
   $formulario -> linea("Fax",1,"t");

   $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);

   for($i=0;$i<sizeof($agencias);$i++)
   {
   	$agencias[$i][0] = "<a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]."&opcion=2&agenci=".$agencias[$i][0]." \"target=\"centralFrame\">".$agencias[$i][0]."</a>";
    $ciudad_a = $objciud -> getSeleccCiudad($agencias[$i][2]);

    $formulario -> linea($agencias[$i][0],0,"i");
    $formulario -> linea($agencias[$i][1],0,"i");
    $formulario -> linea($ciudad_a[0][1],0,"i");
    $formulario -> linea($agencias[$i][3],0,"i");
    $formulario -> linea($agencias[$i][4],0,"i");
    $formulario -> linea($agencias[$i][5],0,"i");
    $formulario -> linea($agencias[$i][6],0,"i");
    $formulario -> linea($agencias[$i][7],1,"i");
   }

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }

 function Formulario()
 {
  $datos_usuario = $this -> usuario -> retornar();

  $inicio[0][0] = 0;
  $inicio[0][1] = "-";

  $query = "SELECT a.cod_agenci,a.nom_agenci,a.cod_ciudad,a.dir_agenci,
  		           a.tel_agenci,a.num_faxxxx,a.dir_emailx,a.con_agenci,
  		     	   c.abr_tercer
  		      FROM ".BASE_DATOS.".tab_genera_agenci a,
  		      	   ".BASE_DATOS.".tab_transp_agenci b,
  		      	   ".BASE_DATOS.".tab_tercer_tercer c
  		     WHERE a.cod_agenci = ".$_REQUEST[agenci]." AND
  		     	   a.cod_agenci = b.cod_agenci AND
  		     	   b.cod_transp = c.cod_tercer
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $agenci = $consulta -> ret_matriz();

  $query = "SELECT a.cod_agedes
  		      FROM ".BASE_DATOS.".tab_despac_despac a
  		     WHERE a.cod_agedes = ".$_REQUEST[agenci]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $exides = $consulta -> ret_matriz();

  $query = "SELECT a.cod_agenci
  		      FROM ".BASE_DATOS.".tab_despac_vehige a
  		     WHERE a.cod_agenci = ".$_REQUEST[agenci]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $exiveh = $consulta -> ret_matriz();

  $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
  $ciudad_a = $objciud -> getSeleccCiudad($agenci[0][2]);

  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/agenci.js\"></script>\n";
  $formulario = new Formulario ("index.php","post","ELIMINAR AGENCIA","form_agenci");
  $formulario -> linea("Informaci&oacute;n B&aacute;sica",0,"t2");

  $formulario -> nueva_tabla();
  $formulario -> linea("Transportadora",0,"t");
  $formulario -> linea($agenci[0][8],1,"i");
  $formulario -> linea("C&oacute;digo",0,"t");
  $formulario -> linea($agenci[0][0],1,"i");
  $formulario -> linea("Nombre de la Agencia",0,"t");
  $formulario -> linea($agenci[0][1],1,"i");
  $formulario -> linea("Ciudad",0,"t");
  $formulario -> linea($ciudad_a[0][1],1,"i");
  $formulario -> linea("Direcci&oacute;n",0,"t");
  $formulario -> linea($agenci[0][3],1,"i");
  $formulario -> linea("Tel&eacute;fono",0,"t");
  $formulario -> linea($agenci[0][4],1,"i");
  $formulario -> linea("Fax",0,"t");
  $formulario -> linea($agenci[0][5],1,"i");
  $formulario -> linea("E-mail",0,"t");
  $formulario -> linea($agenci[0][6],1,"i");
  $formulario -> linea("Contacto",0,"t");
  $formulario -> linea($agenci[0][7],1,"i");

  $formulario -> nueva_tabla();
  $formulario -> oculto("agenci",$_REQUEST[agenci],0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("opcion",3,0);
  $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);

  if(!$exides && !$exiveh)
   $formulario -> botoni("Eliminar","if(confirm('Esta Seguro de Eliminar la Agencia.?')){form_agenci.submit()}",0);
  else
   $formulario -> linea("Imposible Eliminar Esta Agencia. Se Encuentra Relacionada a Uno o Mas Despachos",1,"e");
  $formulario -> cerrar();
 }

 function Eliminar()
 {
  $query = "DELETE FROM ".BASE_DATOS.".tab_genera_agenci
  		          WHERE cod_agenci = ".$_REQUEST[agenci]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion,"BR");

  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
   $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Eliminar Otra Agencia</a></b>";

   $mensaje =  "La Agencia Se Elimino con Exito".$link_a;
   $mens = new mensajes();
   $mens -> correcto("ELIMINAR AGENCIAS",$mensaje);
  }
 }
}

$proceso = new Mod_agencias($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>