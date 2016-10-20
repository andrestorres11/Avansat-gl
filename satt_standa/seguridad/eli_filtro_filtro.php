<?php
/****************************************************************************
NOMBRE:   ELI_FILTRO.PHP
FUNCION:  ELIMINAR FILTROS
****************************************************************************/
class Proc_filtro
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
          $this -> Formulario();
          break;
        case "3":
          $this -> Eliminar();
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

   echo "<script language=\"JavaScript\" src=\"js/filtro.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","BUSCAR Y LISTAR FILTROS","form_list");
   $formulario -> linea("INSERTE UN TEXTO PARA INICIAR LA BUSQUEDA",1);
   $formulario -> nueva_tabla();
   $formulario -> texto ("TEXTO","text","filtro",1,50,255,"","");
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("valor",$valor,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> boton("Buscar","button\" onClick=\"aceptar_eli() ",0);
   $formulario -> boton("Ver Todas","button\" onClick=\"form_list.submit() ",0);
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
  $query = "SELECT cod_filtro,nom_filtro,cod_queryx
            FROM ".CENTRAL.".tab_genera_filtro
           WHERE nom_filtro LIKE '%$_REQUEST[filtro]%'
        ORDER BY 2";
  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();
  for($i=0;$i<sizeof($matriz);$i++)
      $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&filtro=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   $formulario = new Formulario ("index.php","post","RESULTADO DE LA CONSULTA","form_item");
   $formulario -> linea("<b>SE ENCONTRARON ".sizeof($matriz)." REGISTROS</b>",0);
   $formulario -> nueva_tabla();
   if(sizeof($matriz) > 0)
   {
   $formulario -> linea("CÓDIGO",0);
   $formulario -> linea("NOMBRE",0);
   $formulario -> linea("CÓDIGO SQL",0);
   $formulario -> linea("OPCIONES",1);
   for($i=0;$i<sizeof($matriz);$i++)
   {
     if($matriz[$i][2] != Null)
     {
       $query=$matriz[$i][2];
       $consulta = new Consulta($query, $this -> conexion);
       $matriz1 = $consulta -> ret_matriz();
     }//fin if
     if($i%2 == 0)
     {
      echo "<td class=\"celda2\">".$matriz[$i][0]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][1]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][2]."</td>";
      if($matriz[$i][2] != Null)
         $formulario -> lista_titulo("", "opcion", $matriz1, 1);
      else
         echo "<td></td>";
     }//fin if
     else
     {
      echo "<td class=\"celda\">".$matriz[$i][0]."</td>";
      echo "<td class=\"celda\">".$matriz[$i][1]."</td>";
      echo "<td class=\"celda\">".$matriz[$i][2]."</td>";
      if($matriz[$i][2] != Null)
         $formulario -> lista_titulo("", "opcion", $matriz1, 1);
      else
         echo "<td></td>";
     }//fin else
   }//fin for
   }//fin if
   $formulario -> nueva_tabla();
   $formulario -> boton("Volver <==","button\" onClick=\"javascript:history.go(-1)",0);
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("valor",$valor,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }//FIN FUNCION ACTUALIZAR
// *****************************************************
// FUNCION QUE PRESENTA EL FORMULARIO DE CAPTURA
// *****************************************************
 function Formulario()
 {
  $query = "SELECT cod_filtro,nom_filtro,cod_queryx
            FROM ".CENTRAL.".tab_genera_filtro
           WHERE cod_filtro = '$_REQUEST[filtro]' ";
  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

   echo "<script language=\"JavaScript\" src=\"js/filtro.js\"></script>\n";
   $formulario = new Formulario("index.php","post","<b>ACTUALIZAR FILTROS</b>", "form_ins");
   echo "<td class=\"celda\"><b>CÓDIGO</b></td><td class=\"celda2\">".$matriz[0][0]."</td></tr><tr>";
   echo "<td class=\"celda\"><b>NOMBRE</b></td><td class=\"celda2\">".$matriz[0][1]."</td></tr><tr>";
   echo "<td class=\"celda\"><b>CÓDIGO SQL</b></td><td class=\"celda2\">".$matriz[0][2]."</td></tr><tr>";
 	 $formulario -> nueva_tabla();
   $formulario -> oculto("opcion",3, 0);
   $formulario -> oculto("filtro","$_REQUEST[filtro]", 0);
   $formulario -> oculto("cod_servic", $_REQUEST["cod_servic"], 0);
   $formulario -> oculto("window","central", 0);
   $formulario -> boton("Eliminar","submit\" onClick=\"return confirm('ESTA SEGURO QUE DESEA ELIMINAR ESTE REGISTRO')",0);
   $formulario -> boton("Borrar","reset",0);
   $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA
// *****************************************************
// FUNCION QUE ACTUALIZA
// *****************************************************
 function Eliminar()
 {
  $query = "DELETE FROM ".CENTRAL.".tab_genera_filtro
             WHERE cod_filtro = '$_REQUEST[filtro]' ";
  $consec = new Consulta($query, $this -> conexion);
  echo "<br><br><b>TRANSACCION EXITOSA<br>EL FILTRO $_REQUEST[nombre] FUE ELIMINADO <b>";
  $formulario = new Formulario ("index.php","post","","form_item");
  $formulario -> boton("Volver <==","button\" onClick=\"javascript:history.go(-2)",0);
  $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA
// *********************************************************************************
// *********************************************************************************
}//FIN CLASE PROC_ACTIVI
     $proceso = new Proc_filtro($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>
