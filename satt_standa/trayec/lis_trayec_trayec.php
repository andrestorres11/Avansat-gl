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
    $this -> Buscar();
  else
     {
      switch($_REQUEST[opcion])
       {
        case "2":
          $this -> Resultado();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/trayec.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","BUSCAR Y LISTAR TRAYECTOS","form_list");
   $formulario -> linea("Defina la Condici&oacute;n de Busqueda",1,"t2");
   $formulario -> nueva_tabla();
   $formulario -> texto ("Trayecto","text","trayec",1,50,50,"","");
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> boton("Buscar","button\" onClick=\"aceptar_lis() ",0);
   $formulario -> boton("Todas","button\" onClick=\"form_list.submit() ",0);
   $formulario -> cerrar();
 }//FIN FUNCION ACTUALIZAR

 function Resultado()

 {

  $datos_usuario = $this -> usuario -> retornar();

  $usuario=$datos_usuario["cod_usuari"];
  
  $query = "SELECT cod_trayec,nom_trayec,cod_estado
              		FROM ".BASE_DATOS.".tab_genera_trayec
		            WHERE nom_trayec LIKE '%".$_REQUEST[trayec]."%' AND cod_estado = '1' OR cod_estado = '2'
	                ORDER BY 2";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

   $formulario = new Formulario ("index.php","post","Listado de Trayectos","form_item");
   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Trayectos(s)",0,"t2");
   $formulario -> nueva_tabla();

   if(sizeof($matriz) > 0)

   {
   $formulario -> linea("Codigo",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("Estado",1,"t");   

   for($i=0;$i<sizeof($matriz);$i++)
   {
   	$formulario -> linea($matriz[$i][0],0,"i");
   	$formulario -> linea($matriz[$i][1],0,"i");
   	
   	if($matriz[$i][2] == COD_ESTADO_ACTIVO)
   		$formulario -> linea("Activo",1,"i");
   	else
   		$formulario -> linea("Inactivo",1,"i");
   	//$formulario -> linea($matriz[$i][3],1,"i");

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

 }//fin funcion

}//FIN CLASE Proc_trayec
     $proceso = new Proc_trayec($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>