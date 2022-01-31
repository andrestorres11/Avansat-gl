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
        case "3":
          $this -> Actualizar();
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
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> boton("Aceptar","button\" onClick=\"form_list.submit() ",0);
   $formulario -> cerrar();
 }

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT cod_trayec,nom_trayec,cod_estado
              FROM ".BASE_DATOS.".tab_genera_trayec
           	 WHERE nom_trayec LIKE '%".$_REQUEST[trayec]."%' AND
		 		   cod_estado = ".COD_ESTADO_ACTIVO." OR
              	   cod_estado = ".COD_ESTADO_INACTI."
        		   ORDER BY 2";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

   $formulario = new Formulario ("index.php","post","Listado de Trayectos","form_item");
   $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Trayecto(s)",0,"t2");
   $formulario -> nueva_tabla();

   $formulario -> linea("Codigo",0,"t");
   $formulario -> linea("Nombre",0,"t");
   $formulario -> linea("Estado",1,"t");
   
   for($i=0;$i<sizeof($matriz);$i++)
   {
   	$matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&trayec=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   	$formulario -> linea($matriz[$i][0],0,"i");
   	$formulario -> linea($matriz[$i][1],0,"i");
   	
   	if($matriz[$i][2] == COD_ESTADO_ACTIVO)
   		$formulario -> linea("Activo",1,"i");
   	else
   		$formulario -> linea("Inactivo",1,"i");   	
   }

   $formulario -> nueva_tabla();
   $formulario -> boton("Volver","button\" onClick=\"javascript:history.go(-1)",0);
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }//FIN FUNCION ACTUALIZAR


 function Datos()
 {
  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/trayec.js\"></script>\n";
  
  $query = "SELECT cod_trayec,nom_trayec,cod_estado
            FROM ".BASE_DATOS.".tab_genera_trayec
           WHERE cod_trayec = ".$_REQUEST[trayec]."
           ";
           
  $consulta = new Consulta($query, $this -> conexion);
  $matriz = $consulta -> ret_matriz();

   $formulario = new Formulario ("index.php","post","Actualizar Trayecto","form_act");
   $formulario -> linea("Informaci&oacute;n B&aacute;sica del Trayecto",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("C&oacute;digo",0,"t","","","RIGHT");
   $formulario -> linea($matriz[0][0],1,"i");
   
   $formulario -> texto ("Nombre o descripcion del Trayecto:","text","nombre",1,50,50,"","".$matriz[0][1]."");

   if($matriz[0][2] == COD_ESTADO_ACTIVO)
     $formulario -> caja ("Estado:","estado","1",1,1);
   else
     $formulario -> caja ("Estado:","estado","1",0,1);

   $formulario -> nueva_tabla();
   $formulario -> oculto("maximo",$interfaz -> cant_interf,0);
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("trayec",$_REQUEST[trayec],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   //$formulario -> boton("Actualizar","button\" onClick=\"if(confirm('Desea Actualiza el Trayecto.?')){form_item.opcion.value = 3; form_item.submit();}",0);
   $formulario -> boton("Actualizar","button\" onClick=\"aceptar_act() ",0);
   $formulario -> boton("Cancelar","button\" onClick=\"javascript:history.go(-3)",0);
   $formulario -> cerrar();
 }//FIN FUNCION ACTUALIZAR
// *********************************************************************************

 function Actualizar()
 {
  $datos_usuario = $this -> usuario -> retornar();
  $usuario = $datos_usuario["cod_usuari"];

  $fec_actual = date("Y-m-d H:i:s");
   //valida el indicador
   if($_REQUEST[estado] == COD_ESTADO_ACTIVO)
     $estado = COD_ESTADO_ACTIVO;
   else
     $estado = COD_ESTADO_INACTI;

  $query = "UPDATE ".BASE_DATOS.".tab_genera_trayec
               SET nom_trayec = '$_REQUEST[nombre]',
                   cod_estado = '".$estado."',                   
                   usr_creaci = '".$usuario."',
                   fec_creaci = '".$fec_actual."'
             WHERE cod_trayec = '$_REQUEST[trayec]'
           ";
  $insercion = new Consulta($query, $this -> conexion,"BR");

  if($insercion = new Consulta("COMMIT", $this -> conexion))
    {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Actualizar Otro Trayecto</a></b>";

     $mensaje =  "El Trayecto <b>".$_REQUEST[nombre]."</b> Se Actualizo con Exito".$mensaje_sat.$link_a;
     $mens = new mensajes();
     $mens -> correcto("ACTUALIZAR TRAYECTOS",$mensaje);
    }
 }

}//FIN CLASE Proc_trayec
   $proceso = new Proc_trayec($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>