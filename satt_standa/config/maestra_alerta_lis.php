
<?php
/****************************************************************************
NOMBRE:   MODULO_LISTAR DE ALARMAS.PHP
FUNCION:  LISTAR ALARMAS
AUTOR: LEONARDO ROMERO
FECHA CREACION : 24 AGOSTO
****************************************************************************/
class Proc_alerta
{
 var $conexion,
     $usuario;//una conexion ya establecida a la base de datos
    //Metodos
 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $datos_usuario = $this -> usuario -> retornar();
  $this -> principal();
 }
//********METODOS
 function principal()
 {
  if(!isset($GLOBALS[opcion]))
     $this -> Resultado();
  else
     {
      switch($GLOBALS[opcion])
       {
        case "1":
          $this -> Formulario();
          break;
        case "2":
          $this -> Listar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL
 function Resultado()

 {

   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT a.cod_alarma,a.nom_alarma,a.cant_tiempo,a.cod_colorx
            FROM ".BASE_DATOS.".tab_genera_alarma a
               ORDER BY 3";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  for ($i =0 ; $i < sizeof($matriz); $i++)

          $matriz[$i][3] = "<td style ='font-size: 8pt;color: #000000;background-color: ".$matriz[$i][3]."'></td>";

   $formulario = new Formulario ("index.php","post","Listar Alarmas","form_item");
   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Alarma(s)",0,"t2");
   $formulario -> nueva_tabla();

   if(sizeof($matriz) > 0)
   {
   $formulario -> linea("Codigo Alarma",0,"t");

   $formulario -> linea("Nombre",0,"t");

   $formulario -> linea("Tiempo de Alarma",0,"t");

   $formulario -> linea("Color",1,"t");

   for($i=0;$i<sizeof($matriz);$i++)

   {
   	$formulario -> linea($matriz[$i][0],0,"i");
   	$formulario -> linea($matriz[$i][1],0,"i");
   	$formulario -> linea($matriz[$i][2],0,"i");
    echo $matriz[$i][3]."</tr><tr>";
   }//fin for

   }//fin if

   $formulario -> nueva_tabla();

   $formulario -> botoni("volver","javascript:history.go(-1)",0);

   $formulario -> nueva_tabla();

   $formulario -> oculto("usuario","$usuario",0);

   $formulario -> oculto("opcion",1,0);

   $formulario -> oculto("valor",$valor,0);

   $formulario -> oculto("window","central",0);

   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

   $formulario -> cerrar();

 }//FIN FUNCION LISTAR

}//FIN CLASE Proc_alerta
     $proceso = new Proc_alerta($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>
