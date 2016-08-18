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
  
  if(!isset($GLOBALS[opcion]))
     $this -> Formulario1();
  else
  {
      switch($GLOBALS[opcion])
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
  $bandeja[0][0] = '1';
  $bandeja[0][1] = 'Clasica';
  $bandeja[1][0] = '2';
  $bandeja[1][1] = 'Turnos';
  $query = "SELECT cod_perfil, nom_perfil
             FROM ".BASE_DATOS.".tab_genera_perfil
             WHERE 1=1 ";

  if($this -> usuario -> cod_perfil != COD_PERFIL_SUPERUSR)
    $query  .= " AND cod_perfil <> '".COD_PERFIL_SUPERUSR."'";

  if( $this -> usuario -> cod_perfil != COD_PERFIL_ADMINIST && $this -> usuario -> cod_perfil != COD_PERFIL_SUPERUSR)
   $query  .= " AND cod_perfil <> '".COD_PERFIL_ADMINIST."'";
  
  //$query .= implode(" AND ", $aux);
  
  $query .= " ORDER BY nom_perfil";

  $consulta = new Consulta($query, $this -> conexion);
  $perfiles = $consulta -> ret_matriz();
  $perfiles = array_merge($inicio,$perfiles);

  if($GLOBALS[perfil])
  {
   $query = "SELECT cod_perfil, nom_perfil
               FROM ".BASE_DATOS.".tab_genera_perfil
              WHERE cod_perfil = ".$GLOBALS[perfil]."
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
  $formulario -> texto("Usuario","text","usuari", 1, 10,  20,"","$GLOBALS[usuari]");
  $formulario -> texto("Cédula","text","cedula\" size=\"12\" onkeypress=\"return Numeric(event)\" maxlength=\"12\" id=\"cedulaID", 1, 10,  20,"","$GLOBALS[cedula]");
  $formulario -> texto("Clave","password","clave1", 1, 10,  20,"","$GLOBALS[clave1]");
  $formulario -> texto("Confirmar Clave","password","clave2", 1, 10,  20,"","$GLOBALS[clave2]");
  $formulario -> texto("Nombre","text","nombre", 1,50,150,"","$GLOBALS[nombre]");
  $formulario -> texto("Correo","text","mail", 1,50,150,"","$GLOBALS[mail]");
  $formulario -> lista("Perfil", "perfil\" onChange=\"form_ins.submit()", $perfiles, 1);
  $formulario -> lista("Bandeja", "bandeja\" ", $bandeja, 1);
  $checked0   = $GLOBALS['ind_cambio'] == 'on' ? 'checked="checked"' : 'checked="checked"';
  $num_diasxx = $GLOBALS['num_diasxx'] == '60' ? '60' : '30';
  $checked1   = $num_diasxx == '30' ? 'checked="checked"' : NULL;
  $checked2   = $num_diasxx == '60' ? 'checked="checked"' : NULL;
  $formulario -> nueva_tabla();
  $formulario -> linea("Caducidad de Contraseña",1,"t2");
  $formulario -> nueva_tabla();
  $html .= '<tr>';
  $html .= '<td width="20%" align="right" style="background:#CCCCCC;"><label for="ind_cambioID">Aplicar Caducidad( s/n ):</label></td>';
  $html .= '<td width="13%" align="left"  style="background:#CCCCCC;"><input type="checkbox" name="ind_cambio" id="ind_cambioID" '.$checked0.'  /></td>';
  $html .= '<td width="20%" align="right" style="background:#CCCCCC;"><label for="ind_diasxx30ID">Caducar cada 30 días:</label></td>';
  $html .= '<td width="14%" align="left"  style="background:#CCCCCC;"><input type="radio" name="ind_diasxx" id="ind_diasxx30ID"  '.$checked1.'" onclick="document.getElementById( \'num_diasxxID\' ).value=\'30\';" /></td>';
  $html .= '<td width="20%" align="right" style="background:#CCCCCC;"><label for="ind_diasxx60ID">Caducar cada 60 días:</label></td>';
  $html .= '<td width="13%" align="left"  style="background:#CCCCCC;"><input type="radio" name="ind_diasxx" id="ind_diasxx60ID"  '.$checked2.'  onclick="document.getElementById( \'num_diasxxID\' ).value=\'60\';" /><input type="hidden" name="num_diasxx" id="num_diasxxID" value="'.$num_diasxx.'" /></td>';
  $html .= '</tr>';
  $html .= '</table>';
  echo $html;
  
      
  $formulario -> nueva_tabla();
  $formulario -> linea("Asignaci&oacute;n de Permisos",0,"t2");

  $formulario -> nueva_tabla();

  if($GLOBALS[perfil])
  {
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
  $formulario -> oculto("cod_servic", $GLOBALS[cod_servic], 0);
  $formulario -> oculto("window","central", 0);
  $formulario -> boton("Aceptar", "button\" onClick=\"aceptar_ins1()", 0);
  $formulario -> cerrar();
 }

 function Formulario2()
 {
    if( isset( $GLOBALS['perfil'] ) )
    {
      //--------------------------------------------------
      // SE VERIFICA SI EL PERFIL ESCOGIDO ES EL 'BIOMETRICO PARA EAL'
      //--------------------------------------------------
      $mPerfil =  "
                   SELECT a.cod_perfil
                   FROM   ".BASE_DATOS.".tab_genera_perfil a
                   WHERE  a.nom_perfil like '%Biometrico para EAL%' AND 
                          a.cod_perfil = " . $GLOBALS['perfil'] . ";
                  ";

      $consulta = new Consulta($mPerfil, $this -> conexion);
      $mPerfil = $consulta -> ret_matriz();
      $mTotal = count( $mPerfil );
      //--------------------------------------------------
    }
    else
      $mTotal = 0;

  //if($GLOBALS[perfil])
  if( isset( $GLOBALS[perfil] ) && ( $mTotal == 0 ) )
  {
   $query = "SELECT a.nom_filtro,b.clv_filtro
	       	   FROM ".CENTRAL.".tab_genera_filtro a,
	       	   		".BASE_DATOS.".tab_aplica_filtro_perfil b
	      	  WHERE a.cod_filtro = b.cod_filtro AND
	      	  		b.cod_perfil = ".$GLOBALS[perfil]."
	      	  		ORDER BY a.cod_filtro
	    	";

   $consulta = new Consulta($query, $this -> conexion);
   $filtros = $consulta -> ret_matriz();

   $query = "SELECT a.nom_perfil
	       	   FROM ".BASE_DATOS.".tab_genera_perfil a
	      	  WHERE a.cod_perfil = ".$GLOBALS[perfil]."
	    	";

   $consulta = new Consulta($query, $this -> conexion);
   $perfil = $consulta -> ret_matriz();

   $formulario = new Formulario("index.php","post","INFORMACION DEL USUARIO", "form_ins");

   $formulario -> linea("Informaci&oacute;n B&aacute;sica",1,"t2");
   echo '<input type="hidden" name="ind_cambio" id="ind_cambioID" value="'.$GLOBALS['ind_cambio'].'" />';
   echo '<input type="hidden" name="ind_diasxx" id="ind_diasxxID" value="'.$GLOBALS['ind_diasxx'].'" />';
   echo '<input type="hidden" name="num_diasxx" id="num_diasxxID" value="'.$GLOBALS['num_diasxx'].'" />';
   $formulario -> nueva_tabla();
   $formulario -> linea("Usuario",0,"t");
   $formulario -> linea($GLOBALS[usuari],1,"i");
   $formulario -> linea("Cédula",0,"t");
   $formulario -> linea($GLOBALS[cedula],1,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($GLOBALS[nombre],1,"i");
   $formulario -> linea("E-mail",0,"t");
   $formulario -> linea($GLOBALS[mail],1,"i");
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
   if($GLOBALS[bandeja]==1)
     $bandeja = 'Clasica';
   else
     $bandeja = 'Turnos';
   $formulario = new Formulario("index.php","post","INSERTAR USUARIOS", "form_ins");

   $formulario -> nueva_tabla();
   $formulario -> linea("Informaci&oacute;n Base del Usuario",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Usuario",0,"t");
   $formulario -> linea($GLOBALS[usuari],1,"i"); 
   $formulario -> linea("Cédula",0,"t");
   $formulario -> linea($GLOBALS[cedula],1,"i");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea($GLOBALS[nombre],1,"i");
   $formulario -> linea("E-mail",0,"t");
   $formulario -> linea($GLOBALS[mail],1,"i");
   $formulario -> linea("Bandeja",0,"t");
   $formulario -> linea($bandeja,1,"i");
   $formulario -> nueva_tabla();
   $formulario -> linea("Servicios",1,"t2");

   $formulario -> nueva_tabla();

   $filtrosel = $GLOBALS[codigos];
		
		for( $i = 0; $i < sizeof($matriz); $i++ )
		{
			if($filtrosel[0] && $matriz[$i][0] == COD_FILTRO_EMPTRA)
			{
				$formulario -> caja("".$matriz[$i][1]."","seleccfil[$i]\" disabled ", "".$matriz[$i][0]."", 1,0);
				$formulario -> oculto("seleccion[$i]",$matriz[$i][0],0);
			}
			else if($filtrosel[0])
				$formulario -> caja("".$matriz[$i][1]."","seleccion[$i]", "".$matriz[$i][0]."", 0,0);
			else
				$formulario -> caja("".$matriz[$i][1]."","seleccion[$i]\"  ", "".$matriz[$i][0]."",0,0);
			
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

   $servicios = $GLOBALS[permi];
   $servicios = array_merge($servicios);

   $formulario -> nueva_tabla();

   $formulario -> oculto("max_ser","".sizeof($matriz)."", 0);
   for($i = 0; $i < sizeof($servicios); $i++)
    $formulario -> oculto("permi[$i]", $servicios[$i], 0);
  }

  $formulario -> nueva_tabla();
  $formulario -> oculto("usuari",$GLOBALS[usuari], 0);
  $formulario -> oculto("cedula",$GLOBALS[cedula], 0);
  $formulario -> oculto("clave1",$GLOBALS[clave1], 0);
  $formulario -> oculto("nombre",$GLOBALS[nombre], 0);
  $formulario -> oculto("mail",$GLOBALS[mail], 0);
  $formulario -> oculto("perfil",$GLOBALS[perfil], 0);
  $formulario -> oculto("bandeja",$GLOBALS[bandeja], 0);

  $formulario -> oculto("opcion",$GLOBALS[opcion], 0);
  $formulario -> oculto("cod_servic", $GLOBALS["cod_servic"], 0);
  $formulario -> oculto("window","central", 0);
  $formulario -> boton("Insertar", "button\" onClick=\"if(confirm('Esta Seguro de Insertar el Usuario.?')){form_ins.opcion.value = 3; form_ins.submit();}", 0);
  $formulario -> cerrar();

 }

 function Insertar()
 {
   $fec_actual = date("Y-m-d H:i:s");
   $clave = base64_encode($GLOBALS[clave1]);

   if(!$GLOBALS[perfil])
    $perfil = "NULL";
   else
    $perfil = $GLOBALS[perfil];

    $query = "INSERT INTO ".BASE_DATOS.".tab_genera_usuari
   		       (cod_usuari, num_cedula, clv_usuari,nom_usuari,usr_emailx,
   		    	 cod_perfil,cod_inicio)
   		    	 VALUES ('".$GLOBALS[usuari]."','".$GLOBALS[cedula]."','".$clave."','".$GLOBALS[nombre]."','".$GLOBALS[mail]."',
   		    	 ".$perfil.", '".$GLOBALS[bandeja]."')";
   
   //------------------------------------------
   // SE OBTIENE EL PUESTO DE CONTROL
   //------------------------------------------
   if( isset( $GLOBALS['codigos'] ) )
    $cod_contro = $GLOBALS['codigos'][3];
   else
    $cod_contro = NULL;

   if( isset( $GLOBALS['seleccion'][3] ) && $GLOBALS['seleccion'][3] != Null )
   {
     $query = "INSERT INTO ".BASE_DATOS.".tab_genera_usuari
               (cod_usuari,clv_usuari,nom_usuari,usr_emailx,
               cod_perfil,cod_inicio, cod_contro)
               VALUES ('".$GLOBALS[usuari]."','".$clave."','".$GLOBALS[nombre]."','".$GLOBALS[mail]."',
               ".$perfil.", '".$GLOBALS[bandeja]."', '" . $cod_contro . "')";
      
      if($GLOBALS['seleccion'][3] == 7)
      {
        $query = "DELETE FROM ".BASE_DATOS.".tab_aplica_filtro_usuari
                  WHERE cod_usuari = '".$GLOBALS[usuari]."' 
                    AND cod_filtro = '7'
                 ";
        //echo "<hr>".$query;
        $consulta = new Consulta($query, $this -> conexion,"R");

        $query = "INSERT INTO ".BASE_DATOS.".tab_aplica_filtro_usuari (
                                                                        cod_aplica,
                                                                        cod_filtro,
                                                                        cod_usuari,
                                                                        clv_filtro )
                                                               VALUES (
                                                                        '".COD_APLICACION."',  '7',  '".$GLOBALS[usuari]."',  '".$cod_contro."' )
                 ";
        //echo "<hr>".$query;
        $consulta = new Consulta($query, $this -> conexion, "R");
      }
   }
   //------------------------------------------

   $consulta = new Consulta($query, $this -> conexion,"BR");
   
   $ind_cambio = $GLOBALS['ind_cambio'] == 'on' ? '1' : '0';
   $num_diasxx = $GLOBALS['num_diasxx'];
   $fec_cambio = $GLOBALS['ind_cambio'] == 'on' ? date( 'Y-m-d H:i:s' ) : NULL;
   $mSql  = "UPDATE ".BASE_DATOS.".tab_genera_usuari 
             SET ind_cambio = '".$ind_cambio."',
             num_diasxx = '".$num_diasxx."',
             fec_cambio = '".$fec_cambio."' 
             WHERE cod_usuari = '".$GLOBALS['usuari']."'  ";
        
        
        //echo '<hr />' . $mSql;
        
        $consulta = new Consulta( $mSql, $this -> conexion, "R" );
         
   if(!$GLOBALS[perfil])
   {
   	//reasignacion de variables
    $servicios = $GLOBALS[permi];
    $filtros = $GLOBALS[seleccion];
    $codigos = $GLOBALS[codigos];

    $query = "INSERT INTO ".BASE_DATOS.".tab_aplica_usuari
                  VALUES ('".$this -> cod_aplica."','".$GLOBALS[usuari]."') ";

    $consulta = new Consulta($query, $this -> conexion, "R");

    for($i = 0; $i < sizeof($servicios); $i++)
    {

	$query = "SELECT cod_servic
                         FROM ".BASE_DATOS.".tab_servic_usuari
                        WHERE cod_servic = ".$servicios[$i]." AND
                              cod_usuari = '".$GLOBALS[usuari]."' ";

             $consulta = new Consulta($query, $this -> conexion);
             $matriz2 = $consulta -> ret_matriz();
       if(!sizeof($matriz2))
       {  
       $query = "INSERT INTO ".BASE_DATOS.".tab_servic_usuari
                 VALUES (".$servicios[$i].",'".$GLOBALS[usuari]."') ";

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
                              cod_usuari = '".$GLOBALS[usuari]."' ";

             $consulta = new Consulta($query, $this -> conexion);
             $matriz2 = $consulta -> ret_matriz();

             if(!sizeof($matriz2))
             {
               $query = "INSERT INTO ".BASE_DATOS.".tab_servic_usuari
                         VALUES (".$matriz1[0][0].",'".$GLOBALS[usuari]."') ";

               $consulta = new Consulta($query, $this -> conexion, "R");

               $hijo = $matriz1[0][0];
             }//fin if
             else
              break;
          }//fin if
        }//fin while
   }//fin for

   for($i=0;$i<$GLOBALS[max_ser];$i++)
   {
     if($filtros[$i] != Null)
     {
       //query de insercion
       $query = "INSERT INTO ".BASE_DATOS.".tab_aplica_filtro_usuari
                 VALUES ('".COD_APLICACION."','".$filtros[$i]."',
                         '".$GLOBALS[usuari]."','".$codigos[$i]."') ";

       $consulta = new Consulta($query, $this -> conexion, "R");
     }//fin if $filtros[$i]
   }//fin for $i
  }

  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Insertar Otro Usuario</a></b>";

     $mensaje =  "El Usuario <b>".$GLOBALS[usuari]."</b> Se Inserto con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("INSERTAR USUARIO",$mensaje);
  }

 }

}

$proceso = new Proc_usuari($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

?>