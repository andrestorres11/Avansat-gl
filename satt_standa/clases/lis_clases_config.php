<?php
/****************************************************************************
Ing.Elkin Javier Beleño Correa
23-11-2005
Intrared.net
****************************************************************************/
class Lis_clases_config
{
 var $conexion,
     $usuario;//una conexion ya establecida a la base de datos
    //Metodos
 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> cod_filtro = $cf;
  $datos_usuario = $this -> usuario -> retornar();
  $this -> principal();
 }
//********METODOS
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
          $this -> Datos();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 // *****************************************************
//FUNCION BUSCAR
// *****************************************************
 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   echo "<script language=\"JavaScript\" src=\"clases.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","Bucar y Listar Clases","form_act");
   $formulario -> linea("Ingrese un Texto para iniciar la busqueda",1);
   $formulario -> nueva_tabla();
   $formulario -> texto ("Texto","text","clasex",1,50,255,"","");
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);

   $formulario -> botoni("Buscar","form_act.submit()",0);
   $formulario -> botoni("Borrar","form_act.reset",1);
   $formulario -> cerrar();
 }//FIN FUNCION ACTUALIZAR
// *********************************************************************************
// *****************************************************
//FUNCION RESULTADO
// *****************************************************
 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
  $query = "SELECT cod_clasex,nom_clasex
            FROM ".BASE_DATOS.".tab_genera_clases
           WHERE nom_clasex LIKE '%$_REQUEST[clasex]%'
        ORDER BY 2";
  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();
  for($i=0;$i<sizeof($matriz);$i++)
        $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&clasex=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   $formulario = new Formulario ("index.php","post","Resultado de la Consulta","form_item");
   $formulario -> linea("<b>Se Encontraron ".sizeof($matriz)." Registros</b>",0);
   $formulario -> nueva_tabla();
   if(sizeof($matriz) > 0)
   {
   $formulario -> linea("Codigo",0);
   $formulario -> linea("Clase",1);
   for($i=0;$i<sizeof($matriz);$i++)
   {
     if($i%2 == 0)
     {
      echo "<td class=\"celda2\">".$matriz[$i][0]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][1]."</td></tr><tr>";
     }//fin if
     else
     {
      echo "<td class=\"celda\">".$matriz[$i][0]."</td>";
      echo "<td class=\"celda\">".$matriz[$i][1]."</td></tr><tr>";
     }//fin else
   }//fin for
   }//fin if
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);


   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }//FIN FUNCION ACTUALIZAR
// *********************************************************************************
// *****************************************************
//FUNCION DATOS
// *****************************************************
 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
  $query = "SELECT cod_clasex,nom_clasex,obs_clasex
            FROM ".BASE_DATOS.".tab_genera_clases
           WHERE cod_clasex = '$_REQUEST[clasex]'";
  $consulta = new Consulta($query, $this -> conexion);
  $matriz = $consulta -> ret_matriz();
     $inicio[0][0]='-';
        $inicio[0][1]='-';
   $formulario = new Formulario ("index.php","post","Listado de Clases","form_item");
   $formulario -> nueva_tabla();
   echo "<td class=\"celda\">Codigo</td><td class=\"celda2\">".$matriz[0][0]."</td></tr><tr>";
   echo "<td class=\"celda\">Nombre</td><td class=\"celda2\">".$matriz[0][1]."</td></tr><tr>";

   $formulario -> nueva_tabla();


      $query = "SELECT a.num_config,b.nom_config
                   FROM ".BASE_DATOS.".tab_config_clasex a, ".BASE_DATOS.".tab_vehige_config b
                   WHERE  a.num_config = b.num_config AND
                          a.cod_clasex = '$_REQUEST[clasex]'";
      $consulta = new Consulta($query, $this -> conexion);
      $resulta2 = $consulta -> ret_matriz();

    $formulario -> nueva_tabla();
    $formulario -> linea("Configuraciones Asignadas",1);
    $formulario -> nueva_tabla();

    $formulario -> linea("Numero",0);
    $formulario -> linea("Nombre",1);

   for($i=0; $i<sizeof($resulta2); $i++)
   {
     echo "<td class=\"celda\"> ".$resulta2[$i][0]." </td>";
     echo "<td class=\"celda\"> ".$resulta2[$i][1]." </td>";
     echo "</tr><tr>";
   }
   $formulario -> nueva_tabla();
   $formulario -> linea("Observaciones",1);
   echo "<td class=\"celda2\"><b>".$matriz[0][2]."</td><br></tr><tr>";
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> botoni("Volver","history.go(-2)",0);
   $formulario -> cerrar();
 }//FIN FUNCION
// *********************************************************************************


}//FIN CLASE Lis_clases_config
   $proceso = new Lis_clases_config($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>