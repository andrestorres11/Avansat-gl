<?php

class Proc_perfil
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
     $this -> Listado();
  else
  {
      switch($GLOBALS[opcion])
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

   $query = "SELECT a.cod_perfil,a.nom_perfil
               FROM ".BASE_DATOS.".tab_genera_perfil a
              WHERE a.cod_perfil != ".COD_PERFIL_SUPERUSR." AND
					a.cod_perfil != ".COD_PERFIL_ADMINIST."
            ";

   $query .= " ORDER BY 1";

   $consulta = new Consulta($query, $this -> conexion);
   $matriz = $consulta -> ret_matriz();

   $formulario = new Formulario("index.php","post","LSITADO DE PERFILES", "form_ins");

   $formulario -> nueva_tabla();
   $formulario -> linea("Se Econtro un Total de ".sizeof($matriz)." Perfil(es)",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Codigo",0,"t");
   $formulario -> linea("Nombre",1,"t");

   for($i = 0; $i < sizeof($matriz); $i++)
   {
   	$matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&opcion=1&perfil=".$matriz[$i][0]." \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   	$formulario -> linea($matriz[$i][0],0,"i");
   	$formulario -> linea($matriz[$i][1],1,"i");
   }

   $formulario -> nueva_tabla();
   $formulario -> oculto("opcion",$GLOBALS[opcion], 0);
   $formulario -> oculto("cod_servic", $GLOBALS["cod_servic"], 0);
   $formulario -> oculto("window","central", 0);
   $formulario -> cerrar();
 }

 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $query = "SELECT a.nom_filtro,b.clv_filtro
	       	   FROM ".CENTRAL.".tab_genera_filtro a,
	       	   		".BASE_DATOS.".tab_aplica_filtro_perfil b
	      	  WHERE a.cod_filtro = b.cod_filtro AND
	      	  		b.cod_perfil = ".$GLOBALS[perfil]."
	      	  		ORDER BY a.cod_filtro
	    	";

   $consulta = new Consulta($query, $this -> conexion);
   $filtros = $consulta -> ret_matriz();

   $query = "SELECT a.cod_perfil,a.nom_perfil
	       	   FROM ".BASE_DATOS.".tab_genera_perfil a
	      	  WHERE a.cod_perfil = ".$GLOBALS[perfil]."
	    	";

   $consulta = new Consulta($query, $this -> conexion);
   $perfil = $consulta -> ret_matriz();

   $query = "SELECT a.cod_servic, a.nom_servic
               FROM ".BASE_DATOS.".tab_perfil_servic c,
               		".CENTRAL.".tab_genera_servic a LEFT JOIN
		    		".CENTRAL.".tab_servic_servic b ON
		    		a.cod_servic = b.cod_serhij
              WHERE b.cod_serhij IS NULL AND
              		a.cod_servic = c.cod_servic AND
              		c.cod_perfil = ".$GLOBALS[perfil]."
                    GROUP BY 1 ORDER BY 1";

   $consulta = new Consulta($query, $this -> conexion);
   $servpadr= $consulta -> ret_matriz();

   $consecut[0][0] += 1;

   $formulario = new Formulario ("index.php","post","INFORMACION DEL PERFIL","form_princi");
   $formulario -> nueva_tabla();
   $formulario -> linea("Informaci&oacute;n B&aacute;sica del Perfil",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("C&oacute;digo",0,"t");
   $formulario -> linea($GLOBALS[perfil],1,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($perfil[0][1],1,"i");

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
			         ".BASE_DATOS.".tab_perfil_servic c
		   	   WHERE a.cod_servic = b.cod_serhij AND
			 		 a.cod_servic = c.cod_servic AND
			 		 c.cod_perfil = ".$GLOBALS[perfil]." AND
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
                      ".BASE_DATOS.".tab_perfil_servic c
                WHERE a.cod_servic = b.cod_serhij AND
			  		  a.cod_servic = c.cod_servic AND
			  		  c.cod_perfil = ".$GLOBALS[perfil]." AND
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

   $query = "SELECT a.cod_perfil
   		       FROM ".BASE_DATOS.".tab_genera_usuari a
   		      WHERE a.cod_perfil = ".$GLOBALS[perfil]."
   		    ";

   $consulta = new Consulta($query, $this -> conexion);
   $exisusu = $consulta -> ret_matriz();

   $formulario -> nueva_tabla();
   $formulario -> oculto("opcion",2, 0);
   $formulario -> oculto("cod_servic", $GLOBALS["cod_servic"], 0);
   $formulario -> oculto("window","central", 0);
   $formulario -> oculto("perfil",$GLOBALS[perfil], 0);
   if($exisusu)
    $formulario -> linea("Imposible Eliminar Este Perfil, Tiene Relacionado Uno &oacute; Varios Usuarios.",1,"e");
   else
    $formulario -> boton("Eliminar", "button\" onClick=\"if(confirm('Esta Seguro de Eliminar el Perfil.?')){form_princi.submit()}", 0);

   $formulario -> cerrar();
 }
	function Eliminar()
	{
		$insercion = new Consulta( "START TRANSACTION", $this -> conexion );
		
		//Filtro de Novedades.
		$query = "DELETE FROM " . BASE_DATOS . ".tab_perfil_noveda
				  WHERE cod_perfil = '$GLOBALS[perfil]' ";

        $consulta = new Consulta( $query, $this->conexion, "R" );
		
		$query = "DELETE FROM ".BASE_DATOS.".tab_genera_perfil
				  WHERE cod_perfil = '$GLOBALS[perfil]' ";

		$consulta = new Consulta($query, $this -> conexion,"R");
  
		

  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Eliminar Otro Perfil</a></b>";

     $mensaje =  "El Perfil Se Elimino con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("ELIMINAR PERFILES",$mensaje);
  }
 }

}

$proceso = new Proc_perfil($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

?>