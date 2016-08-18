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
     $this -> Listado();
  else
  {
      switch($GLOBALS[opcion])
      {
        case "1":
          $this -> Formulario();
          break;
        case "2":
          $this -> CamEstado();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }

 function Listado()
 {
   $datos_usuario = $this -> usuario -> retornar();

   $query = "SELECT a.cod_usuari,a.nom_usuari,a.usr_emailx,
   					if(a.cod_perfil IS NULL,'-',b.nom_perfil),if(a.ind_estado='1','Activo','Inactivo')
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
   $formulario -> linea("Perfil",0,"t");
   $formulario -> linea("Estado",1,"t");

   for($i = 0; $i < sizeof($matriz); $i++)
   {
   	$matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&opcion=1&usuari=".$matriz[$i][0]." \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   	$formulario -> linea($matriz[$i][0],0,"i");
   	$formulario -> linea($matriz[$i][1],0,"i");
   	$formulario -> linea($matriz[$i][2],0,"i");
   	$formulario -> linea($matriz[$i][3],0,"i");
    $formulario -> linea($matriz[$i][4],1,"i");
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

   $query = "SELECT a.cod_usuari,a.nom_usuari,a.usr_emailx,if(ind_estado='1','Activo','Inactivo'),
             ind_estado
	       	   FROM ".BASE_DATOS.".tab_genera_usuari a
	      	  WHERE a.cod_usuari = '".$GLOBALS[usuari]."'
	    	";

   $consulta = new Consulta($query, $this -> conexion);
   $usuario = $consulta -> ret_matriz();
   $estado='Inactivar';
   if($usuario[0][3]=='Inactivo')
    $estado='Activar';

              //trae los datos de la bitacora del usuario

   $query = "SELECT obs_histor, fec_creaci,if(ind_estado= '1','Activo','Inactivo')

              FROM ".BASE_DATOS.".tab_usuari_histor

             WHERE cod_usuari = '$GLOBALS[usuari]'";

   $consulta = new Consulta($query, $this -> conexion);

   $matriz = $consulta -> ret_matriz();
   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/usuari.js\"></script>\n";
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
   $formulario -> linea("Estado",0,"t");
   $formulario -> linea($usuario[0][3],1,"i");
   echo "<td class=\"celda_titulo\"><b>OBSERVACION</b></td>";
   echo "<td class=\"celda_info\">";
   echo "<textarea name='obs_histor' id='obs_historID'  cols='50' Rows='4'></textarea>";
   echo "</td>";
    
  $formulario -> nueva_tabla();
  $formulario -> linea("Observacion",0,"t");
  $formulario -> linea("Fecha",0,"t");
  $formulario -> linea("Estado",1,"t");
  
  for($i=0;$i<sizeof($matriz);$i++)
  {
    
    echo "<td align='left' class='celda_etiqueta'   width:30%><b>";
     $limit = 50;
     $x = 0;
     for ( $s = 0; $s < strlen( $matriz[$i][0] ); $s++ ) {
       echo $matriz[$i][0][$s];
       $x ++;
       if ( $x == $limit ) {
         echo "<br>";
         $x = 0;
       }
     }
     echo "</td>";
    $formulario -> linea($matriz[$i][1],0,"celda");
    $formulario -> linea($matriz[$i][2],1,"celda");
  
  }
    
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuari",$GLOBALS[usuari], 0);
   $formulario -> oculto("opcion",2, 0);
   $formulario -> oculto("estado", $usuario[0][4], 0);
   $formulario -> oculto("cod_servic", $GLOBALS["cod_servic"], 0);
   $formulario -> oculto("window","central", 0);
   $formulario -> botoni($estado,"onClick=Confir_Cambio('$estado')",0);
   $formulario -> cerrar();
 }

 function CamEstado()
 {
  $datos_usuario = $this -> usuario -> retornar(); 
  $fec_actual = date("Y-m-d H:i:s");
   //busca el maximo cod_histor
   $query ="SELECT MAX(cod_histor)
           FROM ".BASE_DATOS.".tab_usuari_histor";
   $consulta = new Consulta($query, $this -> conexion);
   $cod_histor = $consulta -> ret_matriz();
   $cod_histor= $cod_histor[0][0]+1;
  
  $estado='1';
  if($GLOBALS[estado]==1) 
    $estado=0;
    //actualiza el estado del usuario
  $query= "UPDATE ".BASE_DATOS.".tab_genera_usuari ".
             "SET ind_estado='$estado' 
              WHERE cod_usuari='".$GLOBALS[usuari]."'";
  $insercion = new Consulta($query, $this -> conexion,"BR");
  //INSERTA la observacion en la bitacora
  $query= "INSERT INTO  ".BASE_DATOS.".tab_usuari_histor
           (cod_histor, cod_usuari,obs_histor , ind_estado , usr_creaci,fec_creaci)
           VALUES('$cod_histor', '".$GLOBALS[usuari]."','".$GLOBALS[obs_histor]."','$estado','". $datos_usuario["cod_usuari"]."',NOW())";
  $insercion = new Consulta($query, $this -> conexion,"RC");

  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Activar/Inactivar Otro Usuario</a></b>";

     $mensaje =  "TRANSACCION EXITOSA".$link_a;
     $mens = new mensajes();
     $mens -> correcto("ELIMINAR USUARIO",$mensaje);
  }
 }

}

$proceso = new Proc_usuari($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

?>