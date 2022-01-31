<?php

class Proc_trayec
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
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/trayec.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","INSERTAR TRAYECTO","form_insert");
   $formulario -> linea("Datos Basicos del Trayecto",1,"t2");
   $formulario -> nueva_tabla();
   $formulario -> texto ("Nombre del Trayecto","text","nom",1,50,50,"",$_REQUEST[nom]);

   $formulario -> nueva_tabla();

   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
   $formulario -> boton("Insertar","button\" onClick=\"ins_tab_trayec() ",0);
   $formulario -> boton("Borrar","reset",1);
   $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA

 function Insertar()
 {
   $fec_actual = date("Y-m-d H:i:s");
   //trae el consecutivo de la tabla
   $query = "SELECT Max(cod_trayec) AS maximo
               FROM ".BASE_DATOS.".tab_genera_trayec
            ";

   $consec = new Consulta($query, $this -> conexion);
   $ultimo = $consec -> ret_matriz();
   $ultimo[0][0]++;

   //query de insercion
   $query = "INSERT INTO ".BASE_DATOS.".tab_genera_trayec
   						 (cod_trayec,nom_trayec,cod_estado,usr_creaci,
   						  fec_creaci)
                  VALUES (".$ultimo[0][0].",'".$_REQUEST[nom]."',".COD_ESTADO_ACTIVO.",'".$_REQUEST[usuario]."',
   						  '".$fec_actual."')
   		    ";

   $consulta = new Consulta($query, $this -> conexion,"BR");

   if($insercion = new Consulta("COMMIT", $this -> conexion))
    {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Insertar Otro Trayecto</a></b>";

     $mensaje =  "El Trayecto <b>".$_REQUEST[nom]."</b> Se Inserto con Exito".$mensaje_sat.$link_a;
     $mens = new mensajes();
     $mens -> correcto("INSERTAR TRAYECTO",$mensaje);
    }

 }//FIN FUNCTION INSERTAR



}//FIN CLASE PROC_TRAYEC
     $proceso = new Proc_trayec($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>