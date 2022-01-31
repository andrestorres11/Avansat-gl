<?php

class Proc_usuari
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
     $this -> Listado();
  else
  {
      switch($_REQUEST[opcion])
      {
        case "1":
          $this -> Formulario();
          break;
        case "2":
          $this -> Eliminar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }

 function Listado()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $query = "SELECT a.cod_usuari,a.nom_usuari,a.usr_emailx,
   					if(a.cod_perfil IS NULL,'-',b.nom_perfil)
               FROM ".BASE_DATOS.".tab_genera_usuari a LEFT JOIN
               		".BASE_DATOS.".tab_genera_perfil b ON
               		a.cod_perfil = b.cod_perfil
               		ORDER BY 1
            ";

   $consulta = new Consulta($query, $this -> conexion);
   $matriz = $consulta -> ret_matriz();

   $formulario = new Formulario("index.php","post","LSITADO DE USUARIOS", "form_ins");

   $formulario -> nueva_tabla();
   $formulario -> linea("Se Econtro un Total de ".sizeof($matriz)." Usuario(s)",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Usuario",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("E-mail",0,"t");
   $formulario -> linea("Perfil",1,"t");

   for($i = 0; $i < sizeof($matriz); $i++)
   {
   	$matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&opcion=1&usuari=".$matriz[$i][0]." \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   	$formulario -> linea($matriz[$i][0],0,"i");
   	$formulario -> linea($matriz[$i][1],0,"i");
   	$formulario -> linea($matriz[$i][2],0,"i");
   	$formulario -> linea($matriz[$i][3],1,"i");
   }

   $formulario -> nueva_tabla();
   $formulario -> oculto("opcion",$_REQUEST[opcion], 0);
   $formulario -> oculto("cod_servic", $_REQUEST["cod_servic"], 0);
   $formulario -> oculto("window","central", 0);
   $formulario -> cerrar();
 }

 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $query = "SELECT a.cod_usuari,a.nom_usuari,a.usr_emailx,a.cod_perfil
	       	   FROM ".BASE_DATOS.".tab_genera_usuari a
	      	  WHERE a.cod_usuari = '".$_REQUEST[usuari]."'
	    	";

   $consulta = new Consulta($query, $this -> conexion);
   $usuario = $consulta -> ret_matriz();

   if($usuario[0][3])
   {
    $valorxon = $usuario[0][3];
    $nomtab = "tab_perfil_servic";
    $nomcam = "cod_perfil";
   }
   else
   {
    $valorxon = $_REQUEST[usuari];
    $nomtab = "tab_servic_usuari";
    $nomcam = "cod_usuari";
   }

   $query = "SELECT a.cod_servic, a.nom_servic
               FROM ".BASE_DATOS.".".$nomtab." c,
               		".CENTRAL.".tab_genera_servic a LEFT JOIN
		    		".CENTRAL.".tab_servic_servic b ON
		    		a.cod_servic = b.cod_serhij
              WHERE b.cod_serhij IS NULL AND
              		a.cod_servic = c.cod_servic AND
              		c.".$nomcam." = '".$valorxon."'
                    GROUP BY 1 ORDER BY 1";

   $consulta = new Consulta($query, $this -> conexion);
   $servpadr= $consulta -> ret_matriz();

   if($usuario[0][3])
    $query = "SELECT a.nom_filtro,b.clv_filtro
	       	    FROM ".CENTRAL.".tab_genera_filtro a,
	       	   		 ".BASE_DATOS.".tab_aplica_filtro_perfil b
	      	   WHERE a.cod_filtro = b.cod_filtro AND
	      	  		 b.cod_perfil = ".$usuario[0][3]."
	      	  		 ORDER BY a.cod_filtro
	    	 ";
   else
    $query = "SELECT a.nom_filtro,b.clv_filtro
	       	    FROM ".CENTRAL.".tab_genera_filtro a,
	       	   		 ".BASE_DATOS.".tab_aplica_filtro_usuari b
	      	   WHERE a.cod_filtro = b.cod_filtro AND
	      	  		 b.cod_usuari = '".$_REQUEST[usuari]."'
	      	  		 ORDER BY a.cod_filtro
	    	 ";

   $consulta = new Consulta($query, $this -> conexion);
   $filtros = $consulta -> ret_matriz();

   $formulario = new Formulario ("index.php","post","INFORMACION DEL USUARIO","form_princi");
   $formulario -> nueva_tabla();
   $formulario -> linea("Informaci&oacute;n B&aacute;sica del Usuario",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Usuario",0,"t");
   $formulario -> linea($usuario[0][0],1,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($usuario[0][1],1,"i");
   $formulario -> linea("E-mail",0,"t");
   $formulario -> linea($usuario[0][2],1,"i");

   if($usuario[0][3])
   {
   	$query = "SELECT a.nom_perfil
   			    FROM ".BASE_DATOS.".tab_genera_perfil a
   			   WHERE a.cod_perfil = ".$usuario[0][3]."
   			 ";

    $consulta = new Consulta($query, $this -> conexion);
    $elperfil = $consulta -> ret_matriz();

    $formulario -> linea("Perfil",0,"t");
    $formulario -> linea($elperfil[0][0],1,"i");
   }

   if(sizeof($filtros))
   {
   	$formulario -> nueva_tabla();
    $formulario -> linea("Filtros Asignados",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("Nombre del Filtro",0,"t");
    $formulario -> linea("Valor Asignado",1,"t");

    for($i = 0; $i < sizeof($filtros); $i++)
    {
     if($filtros[$i][0] == COD_FILTRO_AGENCI)
	  $query = "SELECT a.nom_agenci
	  		      FROM ".BASE_DATOS.".tab_genera_agenci a
	  		     WHERE a.cod_agenci = ".$filtros[$i][1]."
	 		   ";
	 else
	  $query = "SELECT a.abr_tercer
	  		      FROM ".BASE_DATOS.".tab_tercer_tercer a
	  		     WHERE a.cod_tercer = '".$filtros[$i][1]."'
	  		   ";

	 $consulta = new Consulta($query, $this -> conexion);
     $valfiltr = $consulta -> ret_matriz();

	 $formulario -> linea($filtros[$i][0],0,"i");
     $formulario -> linea($valfiltr[0][0],1,"i");
    }
   }

   $formulario -> nueva_tabla();
   $formulario -> linea("Permisos Asignados",1,"t2");
   $formulario -> nueva_tabla();

   for($i = 0; $i < sizeof($servpadr); $i++)
   {
	$query = "SELECT a.cod_servic,a.nom_servic
		        FROM ".CENTRAL.".tab_genera_servic a,
			         ".CENTRAL.".tab_servic_servic b,
			         ".BASE_DATOS.".".$nomtab." c
		   	   WHERE a.cod_servic = b.cod_serhij AND
			 		 a.cod_servic = c.cod_servic AND
			 		 c.".$nomcam." = '".$valorxon."' AND
			 		 b.cod_serpad = '".$servpadr[$i][0]."'
		 ";

	$consulta = new Consulta($query, $this -> conexion);
	$servhijo = $consulta -> ret_matriz();


	$formulario -> linea("",1,"i");
	$formulario -> linea("Modulo :: ".$servpadr[$i][1]."",1,"t2");

	if(!$servhijo)
	 $formulario -> linea($servpadr[$i][1],1,"i");

	for($j = 0; $j < sizeof($servhijo); $j++)
	{
     $query = "SELECT a.cod_servic, a.nom_servic
                 FROM ".CENTRAL.".tab_genera_servic a,
                      ".CENTRAL.".tab_servic_servic b,
                      ".BASE_DATOS.".".$nomtab." c
                WHERE a.cod_servic = b.cod_serhij AND
			  		  a.cod_servic = c.cod_servic AND
			  		  c.".$nomcam." = '".$valorxon."' AND
			  		  b.cod_serpad = ".$servhijo[$j][0] ;

     $consulta = new Consulta($query, $this -> conexion);
	 $sniveles = $consulta -> ret_matriz();

     if($sniveles)
     {

	   $formulario -> linea("",1,"i");
	   $formulario -> linea(">>> SubNivel :: ".$servhijo[$j][1]."",1,"h");

	   for($k = 0; $k < sizeof($sniveles); $k++)
		$formulario -> linea($sniveles[$k][1],1,"i");
     }
	 else
	  $formulario -> linea($servhijo[$j][1],1,"i");
	}
   }

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuari",$_REQUEST[usuari], 0);
   $formulario -> oculto("opcion",2, 0);
   $formulario -> oculto("cod_servic", $_REQUEST["cod_servic"], 0);
   $formulario -> oculto("window","central", 0);
   $formulario -> boton("Eliminar", "button\" onClick=\"if(confirm('Esta Seguro de Eliminar el Usuario.?')){form_princi.opcion.value = 2; form_princi.submit();}", 0);
   $formulario -> cerrar();
 }

 function Eliminar()
 {
  $query = "DELETE FROM ".BASE_DATOS.".tab_genera_usuari
  		          WHERE cod_usuari = '".$_REQUEST[usuari]."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion,"BR");

  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Eliminar Otro Usuario</a></b>";

     $mensaje =  "El Usuario Se Elimino con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("ELIMINAR USUARIO",$mensaje);
  }
 }

}

$proceso = new Proc_usuari($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

?>