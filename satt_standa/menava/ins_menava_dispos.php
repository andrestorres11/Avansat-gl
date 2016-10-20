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
  if(!isset($_REQUEST[opcion]))
    $this -> Formulario();
  else
     {
      switch($_REQUEST[opcion])
       {
          case "1":
          $this -> Formulario();
          break;
          case "2":
          $this -> Insertar();
          break;

       }
     }
 }

 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $indfilt = 0;
   $inicio[0][0] = 0;
   $inicio[0][1] = "-";

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/menava.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","DISPOSITIVOS","form_item");

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
   	for($j = 0; $j < sizeof($activado); $j++)
   	{
   	 eval("\$cont = \$_REQUEST[cont".$j."];");

   	 if(!$cont)
   	 {
   	  $query = "SELECT a.cod_transp,a.nom_bdsata,a.dir_dispos,a.cod_operad,
   					  a.ind_estado
   				 FROM ".BD_STANDA.".tab_mensaj_dispos a
   			    WHERE a.cod_transp = '".$activado[$j][0]."' AND
   					  a.nom_bdsata = '".BASE_DATOS."'
   			  ";

   	  $consulta = new Consulta($query, $this -> conexion);
      $disactiv = $consulta -> ret_matriz();

	  for($i = 0; $i < sizeof($disactiv); $i++)
	  {
	   eval("\$disabl".$j."[\$i] = 1;");
       eval("\$selecc".$j."[\$i] = 1;");
       eval("\$numdis".$j."[\$i] = \$disactiv[\$i][2];");
       eval("\$operad".$j."[\$i] = \$disactiv[\$i][3];");
       eval("\$estado".$j."[\$i] = \$disactiv[\$i][4];");
	  }

	  $cont = sizeof($disactiv);
   	 }
   	 else
   	 {
   	  eval("\$disabl".$j." = \$_REQUEST[disabl".$j."];");
   	  eval("\$selecc".$j." = \$_REQUEST[selecc".$j."];");
      eval("\$numdis".$j." = \$_REQUEST[numdis".$j."];");
      eval("\$operad".$j." = \$_REQUEST[operad".$j."];");
      eval("\$estado".$j." = \$_REQUEST[estado".$j."];");
   	 }

     $query = "SELECT a.cod_operad,CONCAT(a.nom_operad,' (@',a.dns_operad,')')
    		     FROM ".BD_STANDA.".tab_mensaj_operad a
    		    WHERE a.ind_estado = '1' AND
    		   		    ((a.cod_transp = '".$activado[$j][0]."' AND
    		   		    a.nom_bdsata = '".BASE_DATOS."') OR
    		   		    (a.cod_transp IS NULL AND
    		   		    a.nom_bdsata IS NULL))
    		   		    ORDER BY 2
    		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $operadorl = $consulta -> ret_matriz();

     $formulario -> nueva_tabla();
     $formulario -> linea("Listado de Dispositivos Relacionados Transportadora :: ".$activado[$j][1]."",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> linea("",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("# Dispositivo",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Operador",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Activo",1,"t");

     for($i = 0; $i < $cont; $i++)
     {
      $operador = array_merge($inicio,$operadorl);

      eval("\$operad = \$operad".$j."[\$i];");
      eval("\$selecc = \$selecc".$j."[\$i];");
      eval("\$disabl = \$disabl".$j."[\$i];");
      eval("\$numdis = \$numdis".$j."[\$i];");
      eval("\$estado = \$estado".$j."[\$i];");

      if($operad)
      {
       $query = "SELECT a.cod_operad,CONCAT(a.nom_operad,' (@',a.dns_operad,')')
    		       FROM ".BD_STANDA.".tab_mensaj_operad a
    		      WHERE a.cod_operad = ".$operad." AND
    		   		    ((a.cod_transp = '".$activado[$j][0]."' AND
    		   		    a.nom_bdsata = '".BASE_DATOS."') OR
    		   		    (a.cod_transp IS NULL AND
    		   		    a.nom_bdsata IS NULL))
    		   		    ORDER BY 2
    		    ";

       $consulta = new Consulta($query, $this -> conexion);
       $operador_a = $consulta -> ret_matriz();

       $operador = array_merge($operador_a,$operador);
      }

	  if(($i == $cont-1) && $numdis == "")
	   $formulario -> caja("","selecc".$j."[".$i."]",$activado[$j][0],1,0);
	  else
       $formulario -> caja("","selecc".$j."[".$i."]",$activado[$j][0],$selecc,0);

      if(!$disabl)
      {
	   $formulario -> texto("","text","numdis".$j."[".$i."]",0,30,60,"",$numdis);
	   $formulario -> lista("","operad".$j."[".$i."]",$operador,0);
	  }
	  else
	  {
	   $formulario -> linea("",0,"i");
	   $formulario -> linea($numdis,0,"i");
	   $formulario -> linea("",0,"i");
	   $formulario -> linea($operador_a[0][1],0,"i");
	   $formulario -> oculto("traorig".$j."[".$i."]",$activado[$j][0],0);
	   $formulario -> oculto("numdis".$j."[".$i."]",$numdis,0);
	   $formulario -> oculto("operad".$j."[".$i."]",$operad,0);
	   $formulario -> oculto("disabl".$j."[".$i."]",$disabl,0);
	  }

	  if(($i == $cont-1) && $numdis == "")
	   $formulario -> caja("","estado".$j."[".$i."]",1,1,1);
	  else
	   $formulario -> caja("","estado".$j."[".$i."]",1,$estado,1);
     }

     $formulario -> oculto("cont".$j."",$cont,0);

     if(!$cont)
     {
      $formulario -> nueva_tabla();
      $formulario -> linea("No Existen Dispositivos o E-mails Registrados a Esta Transportadora.",1,"e");
     }

     $formulario -> nueva_tabla();
     $formulario -> boton("Nuevo","button\" onClick=\"form_item.cont$j.value++; form_item.submit();",1);
   	}
   }
   else
   {
   	$formulario -> nueva_tabla();
   	$formulario -> linea("Para Realizar Operaciones en Esta Opci&oacute;n Debe Inicialmente Activar el Envio de Mensajes en la Opci&oacute;n :: Configuracion > Mensajes de Texto > Configuracion",1,"e");
   }

   $formulario -> nueva_tabla();
   $formulario -> oculto("maximo",sizeof($activado),0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);

   $formulario -> nueva_tabla();
   $formulario -> boton("Aceptar","button\" onClick=\"dispositivo(form_item)",0);
   $formulario -> cerrar();

 }

 function Insertar()
 {
  $datos_usuario = $this -> usuario -> retornar();
  $usuario=$datos_usuario["cod_usuari"];

  $consulta = new Consulta("SELECT NOW()", $this -> conexion,"BR");

  for($j = 0; $j < $_REQUEST[maximo]; $j++)
  {
   eval("\$cont = \$_REQUEST[cont".$j."];");
   eval("\$selecc = \$_REQUEST[selecc".$j."];");
   eval("\$numdis = \$_REQUEST[numdis".$j."];");
   eval("\$operad = \$_REQUEST[operad".$j."];");
   eval("\$estado = \$_REQUEST[estado".$j."];");
   eval("\$traorig = \$_REQUEST[traorig".$j."];");

   for($i = 0; $i < $cont; $i++)
   {
    if($selecc[$i])
    {
     $query = "SELECT a.dir_dispos
    			FROM ".BD_STANDA.".tab_mensaj_dispos a
    		   WHERE a.cod_transp = '".$selecc[$i]."' AND
    		   		 a.nom_bdsata = '".BASE_DATOS."' AND
    		   		 a.dir_dispos = '".$numdis[$i]."' AND
    		   		 a.cod_operad = '".$operad[$i]."'
    		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $existe = $consulta -> ret_matriz();

     if(!$estado[$i])
      $estado[$i] = 0;

     if($existe)
      $query = "UPDATE ".BD_STANDA.".tab_mensaj_dispos
     			   SET ind_estado = '".$estado[$i]."'
     			 WHERE cod_transp = '".$selecc[$i]."' AND
    		   		   nom_bdsata = '".BASE_DATOS."' AND
    		   		   dir_dispos = '".$numdis[$i]."' AND
    		   		   cod_operad = '".$operad[$i]."'
     		   ";
     else
      $query = "INSERT INTO ".BD_STANDA.".tab_mensaj_dispos
     					   (cod_transp,nom_bdsata,dir_dispos,cod_operad,
     					   	ind_estado)
     				VALUES ('".$selecc[$i]."','".BASE_DATOS."','".$numdis[$i]."',
     						".$operad[$i].",'".$estado[$i]."')
     		  ";

     $consulta = new Consulta($query, $this -> conexion,"R");
    }
    else if($traorig[$i])
    {
     $query = "DELETE FROM ".BD_STANDA.".tab_mensaj_dispos
    				 WHERE cod_transp = '".$traorig[$i]."' AND
    		   		       nom_bdsata = '".BASE_DATOS."' AND
    		   		       dir_dispos = '".$numdis[$i]."' AND
    		   		  	   cod_operad = ".$operad[$i]."
    		  ";

     $consulta = new Consulta($query, $this -> conexion,"R");
    }
   }
  }

  if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $mensaje =  "Se Actualizo la Informaci&oacute;n de los Dispositivos Exitosamente.";
   $mens = new mensajes();
   $mens -> correcto("DISPOSITIVOS",$mensaje);
  }

 }

}//FIN CLASE PROC_CONTRO
     $proceso = new Proc_contro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>