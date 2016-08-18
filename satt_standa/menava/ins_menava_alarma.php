<?php

class Proc_contro
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
    $this -> Formulario();
  else
     {
      switch($GLOBALS[opcion])
       {
          case "1":
          $this -> Formulario();
          break;
          case "2":
          $this -> Insertar();
          break;

       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $inicio[0][0] = 0;
   $inicio[0][1] = "-";

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/menava.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","ALARMAS","form_item","",0);

   $query = "SELECT a.cod_alarma,CONCAT(a.nom_alarma,' (',a.cant_tiempo,')'),
   					a.cod_colorx
	       	   FROM ".BASE_DATOS.".tab_genera_alarma a
	       	   		ORDER BY a.cant_tiempo
	    ";

   $consulta = new Consulta($query, $this -> conexion);
   $alarmas = $consulta -> ret_matriz();

   $consulta = new Consulta($query, $this -> conexion);
   $activado = $consulta -> ret_matriz();

   $query = "SELECT a.cod_transp,b.abr_tercer
	       	   FROM ".BD_STANDA.".tab_mensaj_bdsata a,
	       	   		".BASE_DATOS.".tab_tercer_tercer b
	          WHERE a.nom_bdsata = '".BASE_DATOS."' AND
		    	    a.cod_transp = b.cod_tercer AND
		    	    a.ind_estado = '1'
	    ";

   if($datos_usuario["cod_perfil"] == "")
   {
   	$filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
     $indfilt = 1;
    }
   }
   else
   {
   	$filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
     $datos_filtro = $filtro -> retornar();
     $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
     $indfilt = 1;
    }
   }

   $query .= " ORDER BY 2";

   $consulta = new Consulta($query, $this -> conexion);
   $activado = $consulta -> ret_matriz();

   $totreg = 0;

   if($activado)
   {
   	$a = 0;

   	for($l = 0; $l < sizeof($activado); $l++)
   	{
   	 $formulario -> oculto("listransp[$l]",$activado[$l][0],0);

   	 $query = "SELECT a.cod_operad,a.nom_operad,a.dns_operad,
    				  if(a.ind_emailx = '1','E-mail','SMS'),
    				  if(a.ind_estado = '1','Activo','Inactivo')
    			 FROM ".BD_STANDA.".tab_mensaj_operad a,
    				  ".BD_STANDA.".tab_mensaj_dispos b
    		    WHERE a.cod_operad = b.cod_operad AND
    		   		  ((a.cod_transp = '".$activado[$l][0]."' AND
    		   		  a.nom_bdsata = '".BASE_DATOS."') OR
    		   		  (a.cod_transp IS NULL AND
    		   		  a.nom_bdsata IS NULL))
    		   		  GROUP BY 1 ORDER BY 2
    		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $operador = $consulta -> ret_matriz();

   	 $formulario -> nueva_tabla();
   	 $formulario -> linea("Asignaci&oacute;n de Alarmas a Dispositivos Transportadora :: ".$activado[$l][1]."",0,"t2");

   	 $formulario -> nueva_tabla();
   	 $formulario -> linea("",0,"t");
   	 $formulario -> linea("",0,"t");
   	 $formulario -> linea("",0,"t");
   	 $formulario -> linea("Novedad Alarma",0,"t");

   	 for($j = 0; $j < sizeof($alarmas); $j++)
   	 {
   	  if($j == sizeof($alarmas)-1)
   	   $formulario -> linea("<td style ='font-size: 8pt;color: #000000;background-color: ".$alarmas[$j][2]."'>".$alarmas[$j][1]."</td>",1,"t");
   	  else
   	   $formulario -> linea("<td style ='font-size: 8pt;color: #000000;background-color: ".$alarmas[$j][2]."'>".$alarmas[$j][1]."</td>",0,"t");
     }

   	 for($i = 0; $i < sizeof($operador); $i++)
   	 {
   	  $query = "SELECT a.dir_dispos,if(a.ind_estado = '1','Activo','Inactivo')
   	 			 FROM ".BD_STANDA.".tab_mensaj_dispos a
   	 			WHERE a.cod_transp = '".$activado[$l][0]."' AND
   	 				  a.nom_bdsata = '".BASE_DATOS."' AND
   	 				  a.cod_operad = ".$operador[$i][0]."
   	 		  ";

      $consulta = new Consulta($query, $this -> conexion);
      $disposit = $consulta -> ret_matriz();

   	  $formulario -> linea($operador[$i][1]." :: ".$operador[$i][2]." :: Envio Mensaje ".$operador[$i][3]." :: Estado ".$operador[$i][4],1,"h");
   	  $formulario -> linea("# Dispositivo",0,"t");
   	  $formulario -> linea("Estado",0,"t");

   	  for($j = 0; $j < sizeof($alarmas); $j++)
   	  {
   	   $formulario -> linea("",0,"t");
   	   if($j == sizeof($alarmas)-1)
   	    $formulario -> linea("",1,"t");
   	   else
   	    $formulario -> linea("",0,"t");
   	  }

      for($j = 0; $j < sizeof($disposit); $j++)
      {
       $query = "SELECT a.ind_novala
       			   FROM ".BD_STANDA.".tab_mensaj_dispos a
       			  WHERE a.cod_transp = '".$activado[$l][0]."' AND
       			  	    a.nom_bdsata = '".BASE_DATOS."' AND
       			  	    a.dir_dispos = '".$disposit[$j][0]."' AND
       			  	    a.cod_operad = ".$operador[$i][0]." AND
       			  	    a.ind_novala = '1'
       		    ";

       $consulta = new Consulta($query, $this -> conexion);
       $novastes = $consulta -> ret_matriz();

       $formulario -> linea($disposit[$j][0]."@".$operador[$i][2],0,"i");
       $formulario -> linea($disposit[$j][1],0,"i");
       if($novastes)
        $formulario -> caja("","novala[$a]",$disposit[$j][0]."|".$operador[$i][0]."|".$activado[$l][0],1,0);
       else
        $formulario -> caja("","novala[$a]",$disposit[$j][0]."|".$operador[$i][0]."|".$activado[$l][0],0,0);

       $b = 0;

       for($k = 0; $k < sizeof($alarmas); $k++)
       {
        $asigchek = 0;

        $query = "SELECT a.cod_alarma
       			    FROM ".BD_STANDA.".tab_mensaj_alarma a
       			   WHERE a.cod_transp = '".$activado[$l][0]."' AND
       			  		 a.nom_bdsata = '".BASE_DATOS."' AND
       			  		 a.dir_dispos = '".$disposit[$j][0]."' AND
       			  		 a.cod_operad = ".$operador[$i][0]." AND
       			  		 a.cod_alarma = ".$alarmas[$k][0]."
       			 ";

        $consulta = new Consulta($query, $this -> conexion);
        $registes = $consulta -> ret_matriz();

        if($registes)
         $asigchek = 1;

        if($k == sizeof($alarmas)-1)
         $formulario -> caja("","codigo[$a][".$b."]",$disposit[$j][0]."|".$operador[$i][0]."|".$alarmas[$k][0]."|".$activado[$l][0],$asigchek,1);
        else
         $formulario -> caja("","codigo[$a][".$b."]",$disposit[$j][0]."|".$operador[$i][0]."|".$alarmas[$k][0]."|".$activado[$l][0],$asigchek,0);

        $b++;
       }

      $a++;
      }
   	 }
   	}

    $formulario -> nueva_tabla();
    $formulario -> oculto("cont",$a,0);
    $formulario -> oculto("totala",sizeof($alarmas),0);
    $formulario -> oculto("opcion",2,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
    $formulario -> boton("Aceptar","button\" onClick=\"if(confirm('Está Seguro de Actualizar la Informacion de Alarmas?')){form_item.submit()}",0);
    $formulario -> cerrar();
   }
   else
   {
   	$formulario -> nueva_tabla();
   	$formulario -> linea("Para Realizar Operaciones en Esta Opción Debe Inicialmente Activar el Envio de Mensajes en la Opción :: Configuracion > Mensajes de Texto > Configuracion",1,"e");
   }
 }

 function Insertar()
 {
  $datos_usuario = $this -> usuario -> retornar();
  $usuario=$datos_usuario["cod_usuari"];

  $novala = $GLOBALS[novala];
  $codigo = $GLOBALS[codigo];
  $listransp = $GLOBALS[listransp];

  for($i = 0; $i < sizeof($listransp); $i++)
  {
   $query = "UPDATE ".BD_STANDA.".tab_mensaj_dispos
  			    SET ind_novala = '0'
  			  WHERE cod_transp = '".$listransp[$i]."' AND
  			 	    nom_bdsata = '".BASE_DATOS."'
  		    ";

   $consulta = new Consulta($query, $this -> conexion,"BR");

   $query = "DELETE FROM ".BD_STANDA.".tab_mensaj_alarma
  			       WHERE cod_transp = '".$listransp[$i]."' AND
  			 	         nom_bdsata = '".BASE_DATOS."'
  		    ";

   $consulta = new Consulta($query, $this -> conexion,"R");
  }

  for($i = 0; $i < $GLOBALS[cont]; $i++)
  {
   if($novala[$i])
   {
    $novala_m = explode("|",$novala[$i]);

    $query = "UPDATE ".BD_STANDA.".tab_mensaj_dispos
  			     SET ind_novala = '1'
  			   WHERE cod_transp = '".$novala_m[2]."' AND
  			 	     nom_bdsata = '".BASE_DATOS."' AND
  			 	     dir_dispos = '".$novala_m[0]."' AND
  			 	     cod_operad = ".$novala_m[1]."
  		   ";

    $consulta = new Consulta($query, $this -> conexion,"R");
   }

   for($j = 0; $j < $GLOBALS[totala]; $j++)
   {
    if($codigo[$i][$j])
    {
     $codigo_m = explode("|",$codigo[$i][$j]);

     $query = "INSERT INTO ".BD_STANDA.".tab_mensaj_alarma
     					  (cod_transp,nom_bdsata,dir_dispos,cod_operad,
     					   cod_alarma)
     			   VALUES ('".$codigo_m[3]."','".BASE_DATOS."','".$codigo_m[0]."',
     					   ".$codigo_m[1].",".$codigo_m[2].")
     		      ";
     $consulta = new Consulta($query, $this -> conexion,"R");
    }
   }
  }

  if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $mensaje =  "Se Actualizo la Informaci&oacute;n de las Alarmas Exitosamente.";
   $mens = new mensajes();
   $mens -> correcto("ALARMAS",$mensaje);
  }

 }

}//FIN CLASE PROC_CONTRO
     $proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>