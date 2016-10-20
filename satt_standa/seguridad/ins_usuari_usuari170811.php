<?php


class Proc_usuari
{
 var $conexion,
 	 $cod_aplica,
     $usuario;

 function Proc_usuari($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }

 function principal()
 {
  if(!isset($_REQUEST[opcion]))
     $this -> Formulario1();
  else
  {
      switch($_REQUEST[opcion])
      {
        case "1":
          $this -> Formulario1();
          break;
        case "2":
          $this -> Formulario2();
          break;
        case "3":
          $this -> Insertar();
          break;
      }//FIN SWITCH
  }// FIN ELSE GLOBALS OPCION
 }

 function Formulario1()
 {
  $inicio[0][0] = 0;
  $inicio[0][1] = '-';
  $query = "SELECT cod_perfil, nom_perfil
             FROM ".BASE_DATOS.".tab_genera_perfil
             WHERE ";

  if($this -> usuario -> cod_perfil != COD_PERFIL_SUPERUSR)
   $aux[] = " cod_perfil <> '".COD_PERFIL_SUPERUSR."'";

  if( $this -> usuario -> cod_perfil != COD_PERFIL_ADMINIST && $this -> usuario -> cod_perfil != COD_PERFIL_SUPERUSR)
   $aux[] .= " cod_perfil <> '".COD_PERFIL_ADMINIST."'";
  
  $query .= implode(" AND ", $aux);
  
  $query .= " ORDER BY nom_perfil";

  $consulta = new Consulta($query, $this -> conexion);
  $perfiles = $consulta -> ret_matriz();
  $perfiles = array_merge($inicio,$perfiles);

  if($_REQUEST[perfil])
  {
   $query = "SELECT cod_perfil, nom_perfil
               FROM ".BASE_DATOS.".tab_genera_perfil
              WHERE cod_perfil = ".$_REQUEST[perfil]."
                	ORDER BY 1
             ";

   $consulta = new Consulta($query, $this -> conexion);
   $perfil_a = $consulta -> ret_matriz();

   $perfiles = array_merge($perfil_a,$perfiles);
  }

  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/usuari.js\"></script>\n";
  $formulario = new Formulario("index.php","post","INSERTAR USUARIOS", "form_ins");

  $formulario -> linea("Informaci&oacute;n B&aacute;sica del Usuario",1,"t2");

  $formulario -> nueva_tabla();
  $formulario -> texto("Usuario","text","usuari", 1, 10,  20,"","$_REQUEST[usuari]");
  $formulario -> texto("Clave","password","clave1", 1, 10,  20,"","$_REQUEST[clave1]");
  $formulario -> texto("Confirmar Clave","password","clave2", 1, 10,  20,"","$_REQUEST[clave2]");
  $formulario -> texto("Nombre","text","nombre", 1,50,150,"","$_REQUEST[nombre]");
  $formulario -> texto("Correo","text","mail", 1,50,150,"","$_REQUEST[mail]");
  $formulario -> lista("Perfil", "perfil\" onChange=\"form_ins.submit()", $perfiles, 1);

  $formulario -> nueva_tabla();
  $formulario -> linea("Asignaci&oacute;n de Permisos",0,"t2");

  $formulario -> nueva_tabla();

  if($_REQUEST[perfil])
  {
   $query = "SELECT a.cod_servic, a.nom_servic
               FROM ".BASE_DATOS.".tab_perfil_servic c,
               		".CENTRAL.".tab_genera_servic a LEFT JOIN
		    		".CENTRAL.".tab_servic_servic b ON
		    		a.cod_servic = b.cod_serhij
              WHERE b.cod_serhij IS NULL AND
              		a.cod_servic = c.cod_servic AND
              		c.cod_perfil = ".$_REQUEST[perfil]."
                    GROUP BY 1 ORDER BY 1";

   $consulta = new Consulta($query, $this -> conexion);
   $servpadr= $consulta -> ret_matriz();

   for($i = 0; $i < sizeof($servpadr); $i++)
   {
	$query = "SELECT a.cod_servic,a.nom_servic
		        FROM ".CENTRAL.".tab_genera_servic a,
			         ".CENTRAL.".tab_servic_servic b,
			         ".BASE_DATOS.".tab_perfil_servic c
		   	   WHERE a.cod_servic = b.cod_serhij AND
			 		 a.cod_servic = c.cod_servic AND
			 		 c.cod_perfil = ".$_REQUEST[perfil]." AND
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
			  		  c.cod_perfil = ".$_REQUEST[perfil]." AND
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
  }
  else
  {
   $query = "SELECT a.cod_servic, a.nom_servic
               FROM ".CENTRAL.".tab_genera_servic a LEFT JOIN
		    		".CENTRAL.".tab_servic_servic b ON
		    		a.cod_servic = b.cod_serhij
              WHERE b.cod_serhij IS NULL
                    GROUP BY 1 ORDER BY 1";

   $consulta = new Consulta($query, $this -> conexion);
   $servpadr= $consulta -> ret_matriz();

   $l = 0;

   for($i = 0; $i < sizeof($servpadr); $i++)
   {
	$query = "SELECT a.cod_servic,a.nom_servic
		        FROM ".CENTRAL.".tab_genera_servic a,
			         ".CENTRAL.".tab_servic_servic b
		   	   WHERE a.cod_servic = b.cod_serhij AND
			 		 b.cod_serpad = '".$servpadr[$i][0]."'
		 ";

	$consulta = new Consulta($query, $this -> conexion);
	$servhijo = $consulta -> ret_matriz();


	$formulario -> linea("",1,"i");
	$formulario -> linea("Modulo :: ".$servpadr[$i][1]."",1,"t2");

	if(!$servhijo)
	{
	 $formulario -> caja ("".$servpadr[$i][1]."","permi[$l]",$servpadr[$i][0],0,1);
	 $l++;
	}

	for($j = 0; $j < sizeof($servhijo); $j++)
	{
     $query = "SELECT a.cod_servic, a.nom_servic
                 FROM ".CENTRAL.".tab_genera_servic a,
                      ".CENTRAL.".tab_servic_servic b
                WHERE a.cod_servic = b.cod_serhij AND
			  		  b.cod_serpad = ".$servhijo[$j][0] ;

     $consulta = new Consulta($query, $this -> conexion);
	 $sniveles = $consulta -> ret_matriz();

     if($sniveles)
     {

	   $formulario -> linea("",1,"i");
	   $formulario -> linea(">>> SubNivel :: ".$servhijo[$j][1]."",1,"h");

	   for($k = 0; $k < sizeof($sniveles); $k++)
	   {
		if(($k+1)%2 == 0)
		 $formulario -> caja ("".$sniveles[$k][1]."","permi[$l]",$sniveles[$k][0],0,1);
		else
		 $formulario -> caja ("".$sniveles[$k][1]."","permi[$l]",$sniveles[$k][0],0,0);
	  	$l++;
	   }
     }
	 else
	 {
	  if(($j+1)%2 == 0)
	   $formulario -> caja ("".$servhijo[$j][1]."","permi[$l]",$servhijo[$j][0],0,1);
	  else
	   $formulario -> caja ("".$servhijo[$j][1]."","permi[$l]",$servhijo[$j][0],0,0);
	  $l++;
	 }
	}
   }
  }

  $formulario -> nueva_tabla();
  $formulario -> oculto("opcion",1,0);
  $formulario -> oculto("cod_servic", $_REQUEST[cod_servic], 0);
  $formulario -> oculto("window","central", 0);
  $formulario -> boton("Aceptar", "button\" onClick=\"aceptar_ins1()", 0);
  $formulario -> cerrar();
 }

 function Formulario2()
 {
  if($_REQUEST[perfil])
  {
   $query = "SELECT a.nom_filtro,b.clv_filtro
	       	   FROM ".CENTRAL.".tab_genera_filtro a,
	       	   		".BASE_DATOS.".tab_aplica_filtro_perfil b
	      	  WHERE a.cod_filtro = b.cod_filtro AND
	      	  		b.cod_perfil = ".$_REQUEST[perfil]."
	      	  		ORDER BY a.cod_filtro
	    	";

   $consulta = new Consulta($query, $this -> conexion);
   $filtros = $consulta -> ret_matriz();

   $query = "SELECT a.nom_perfil
	       	   FROM ".BASE_DATOS.".tab_genera_perfil a
	      	  WHERE a.cod_perfil = ".$_REQUEST[perfil]."
	    	";

   $consulta = new Consulta($query, $this -> conexion);
   $perfil = $consulta -> ret_matriz();

   $formulario = new Formulario("index.php","post","INFORMACION DEL USUARIO", "form_ins");

   $formulario -> linea("Informaci&oacute;n B&aacute;sica",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Usuario",0,"t");
   $formulario -> linea($_REQUEST[usuari],1,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($_REQUEST[nombre],1,"i");
   $formulario -> linea("E-mail",0,"t");
   $formulario -> linea($_REQUEST[mail],1,"i");
   $formulario -> linea("Perfil",0,"t");
   $formulario -> linea($perfil[0][0],1,"i");

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
  }
  else
  {
   $query = "SELECT a.cod_filtro,a.nom_filtro,a.cod_queryx
               FROM ".CENTRAL.".tab_genera_filtro a
             		ORDER BY 1
            ";

   $consulta = new Consulta($query, $this -> conexion);
   $matriz = $consulta -> ret_matriz();

   $inicio[0][0] = 0;
   $inicio[0][1] = '-';

   $formulario = new Formulario("index.php","post","INSERTAR USUARIOS", "form_ins");

   $formulario -> nueva_tabla();
   $formulario -> linea("Informaci&oacute;n Base del Usuario",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Usuario",0,"t");
   $formulario -> linea($_REQUEST[usuari],1,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($_REQUEST[nombre],1,"i");
   $formulario -> linea("E-mail",0,"t");
   $formulario -> linea($_REQUEST[mail],1,"i");

   $formulario -> nueva_tabla();
   $formulario -> linea("Servicios",1,"t2");

   $formulario -> nueva_tabla();

   $filtrosel = $_REQUEST[codigos];

   for($i = 0; $i < sizeof($matriz); $i++)
   {
   	if($filtrosel[0] && $matriz[$i][0] == COD_FILTRO_EMPTRA)
   	{
     $formulario -> caja("".$matriz[$i][1]."","seleccfil[$i]\" disabled ", "".$matriz[$i][0]."", 1,0);
     $formulario -> oculto("seleccion[$i]",$matriz[$i][0],0);
   	}
    else if($filtrosel[0])
     $formulario -> caja("".$matriz[$i][1]."","seleccion[$i]", "".$matriz[$i][0]."", 0,0);
    else
     $formulario -> caja("".$matriz[$i][1]."","seleccion[$i]\" disabled ", "".$matriz[$i][0]."",0,0);

    if($matriz[$i][2])
    {
     $query = $querysel = $matriz[$i][2];

     //if($matriz[$i][0] != COD_FILTRO_EMPTRA)
      //$query .= " AND c.cod_transp = '".$filtrosel[0]."'";

     $query .= " GROUP BY 1 ORDER BY 2";

     $consulta = new Consulta($query, $this -> conexion);
     $porfiltro = $consulta -> ret_matriz();

     $matriz1 = array_merge($inicio,$porfiltro);

     if($filtrosel[0] && $matriz[$i][0] == COD_FILTRO_EMPTRA)
     {
      $querysel .= " AND a.cod_tercer = '".$filtrosel[0]."'";
      $querysel .= " GROUP BY 1 ORDER BY 2";

      $consulta = new Consulta($querysel, $this -> conexion);
      $portrans = $consulta -> ret_matriz();

      $matriz1 = array_merge($portrans,$matriz1);
     }

     if($matriz[$i][0] == COD_FILTRO_EMPTRA)
      $formulario -> lista_titulo("", "codigos[$i]\" onChange=\"form_ins.submit()", $matriz1, 1);
     else
      $formulario -> lista_titulo("", "codigos[$i]", $matriz1, 1);
    }
   }

   $servicios = $_REQUEST[permi];
   $servicios = array_merge($servicios);

   $formulario -> nueva_tabla();

   $formulario -> oculto("max_ser","".sizeof($matriz)."", 0);
   for($i = 0; $i < sizeof($servicios); $i++)
    $formulario -> oculto("permi[$i]", $servicios[$i], 0);
  }

  $formulario -> nueva_tabla();
  $formulario -> oculto("usuari",$_REQUEST[usuari], 0);
  $formulario -> oculto("clave1",$_REQUEST[clave1], 0);
  $formulario -> oculto("nombre",$_REQUEST[nombre], 0);
  $formulario -> oculto("mail",$_REQUEST[mail], 0);
  $formulario -> oculto("perfil",$_REQUEST[perfil], 0);

  $formulario -> oculto("opcion",$_REQUEST[opcion], 0);
  $formulario -> oculto("cod_servic", $_REQUEST["cod_servic"], 0);
  $formulario -> oculto("window","central", 0);
  $formulario -> boton("Insertar", "button\" onClick=\"if(confirm('Esta Seguro de Insertar el Usuario.?')){form_ins.opcion.value = 3; form_ins.submit();}", 0);
  $formulario -> cerrar();

 }

 function Insertar()
 {
   $fec_actual = date("Y-m-d H:i:s");
   $clave = base64_encode($_REQUEST[clave1]);

   if(!$_REQUEST[perfil])
    $perfil = "NULL";
   else
    $perfil = $_REQUEST[perfil];

   $query = "INSERT INTO ".BASE_DATOS.".tab_genera_usuari
   		                 (cod_usuari,clv_usuari,nom_usuari,usr_emailx,
   		    			  cod_perfil)
   		    	  VALUES ('".$_REQUEST[usuari]."','".$clave."','".$_REQUEST[nombre]."','".$_REQUEST[mail]."',
   		    	  		  ".$perfil.")
   		    ";

   $consulta = new Consulta($query, $this -> conexion,"BR");

   if(!$_REQUEST[perfil])
   {
   	//reasignacion de variables
    $servicios = $_REQUEST[permi];
    $filtros = $_REQUEST[seleccion];
    $codigos = $_REQUEST[codigos];

    $query = "INSERT INTO ".BASE_DATOS.".tab_aplica_usuari
                  VALUES ('".$this -> cod_aplica."','".$_REQUEST[usuari]."') ";

    $consulta = new Consulta($query, $this -> conexion, "R");

    for($i = 0; $i < sizeof($servicios); $i++)
    {

	$query = "SELECT cod_servic
                         FROM ".BASE_DATOS.".tab_servic_usuari
                        WHERE cod_servic = ".$servicios[$i]." AND
                              cod_usuari = '".$_REQUEST[usuari]."' ";

             $consulta = new Consulta($query, $this -> conexion);
             $matriz2 = $consulta -> ret_matriz();
       if(!sizeof($matriz2))
       {  
       $query = "INSERT INTO ".BASE_DATOS.".tab_servic_usuari
                 VALUES (".$servicios[$i].",'".$_REQUEST[usuari]."') ";

       $consulta = new Consulta($query, $this -> conexion, "R");

       $bandera1 = 0;
       

	}else{
	  $bandera= 1;
	  $hijo = $servicios[$i];
	}
       $cont = 0;

       while(!$bandera1)
       {
          $query = "SELECT a.cod_serpad,b.nom_servic
                      FROM ".CENTRAL.".tab_servic_servic a,
                           ".CENTRAL.".tab_genera_servic b
                     WHERE a.cod_serpad = b.cod_servic AND
                           a.cod_serhij = '".$hijo."' ";

          $consulta = new Consulta($query, $this -> conexion);
          $matriz1 = $consulta -> ret_matriz();

          if(!sizeof($matriz1))
             break;
          else
          {
             $query = "SELECT cod_servic
                         FROM ".BASE_DATOS.".tab_servic_usuari
                        WHERE cod_servic = ".$matriz1[0][0]." AND
                              cod_usuari = '".$_REQUEST[usuari]."' ";

             $consulta = new Consulta($query, $this -> conexion);
             $matriz2 = $consulta -> ret_matriz();

             if(!sizeof($matriz2))
             {
               $query = "INSERT INTO ".BASE_DATOS.".tab_servic_usuari
                         VALUES (".$matriz1[0][0].",'".$_REQUEST[usuari]."') ";

               $consulta = new Consulta($query, $this -> conexion, "R");

               $hijo = $matriz1[0][0];
             }//fin if
             else
              break;
          }//fin if
        }//fin while
   }//fin for

   for($i=0;$i<$_REQUEST[max_ser];$i++)
   {
     if($filtros[$i] != Null)
     {
       //query de insercion
       $query = "INSERT INTO ".BASE_DATOS.".tab_aplica_filtro_usuari
                 VALUES ('".COD_APLICACION."','".$filtros[$i]."',
                         '".$_REQUEST[usuari]."','".$codigos[$i]."') ";

       $consulta = new Consulta($query, $this -> conexion, "R");
     }//fin if $filtros[$i]
   }//fin for $i
  }

  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Insertar Otro Usuario</a></b>";

     $mensaje =  "El Usuario <b>".$_REQUEST[usuari]."</b> Se Inserto con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("INSERTAR USUARIO",$mensaje);
  }

 }

}

$proceso = new Proc_usuari($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

?>