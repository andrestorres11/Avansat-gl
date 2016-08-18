<?php
/****************************************************************************
Ing.Elkin Javier Beleño Correa
01-12-2005
Intrared.net
****************************************************************************/
class Act_config_vehicu
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
  if(!isset($GLOBALS[opcion]))
    $this -> Buscar();
  else
     {
      switch($GLOBALS[opcion])
       {
        case "1":
          $this -> Resultado();
          break;
        case "2":
          $this -> Datos();
          break;
        case "3":
          $this -> Actualizar();
          $this -> Buscar();
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


   $formulario = new Formulario ("index.php","post","Bucar y Actualizar Configuración de Vehiculos","form_act");
   $formulario -> linea("Ingrese un Texto para iniciar la busqueda",1);
   $formulario -> nueva_tabla();
   $formulario -> texto ("Texto","text","config",1,50,255,"","");
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

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
  $query = "SELECT   num_config,nom_config,if(ind_estado,'Activa','Inactiva')
            FROM     ".BASE_DATOS.".tab_vehige_config
           WHERE num_config LIKE '%$GLOBALS[config]%'
        ORDER BY 2";
  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();
  for($i=0;$i<sizeof($matriz);$i++)
        $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&config=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   $formulario = new Formulario ("index.php","post","Resultado de la Consulta","form_item");
   $formulario -> linea("<b>Se Encontraron ".sizeof($matriz)." Registros</b>",0);
   $formulario -> nueva_tabla();
   if(sizeof($matriz) > 0)
   {
   $formulario -> linea("Codigo",0);
   $formulario -> linea("Descripción",0);
   $formulario -> linea("Estado",1);
   for($i=0;$i<sizeof($matriz);$i++)
   {
     if($i%2 == 0)
     {
      echo "<td class=\"celda2\">".$matriz[$i][0]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][1]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][2]."</td></tr><tr>";

     }//fin if
     else
     {
      echo "<td class=\"celda\">".$matriz[$i][0]."</td>";
      echo "<td class=\"celda\">".$matriz[$i][1]."</td>";
      echo "<td class=\"celda\">".$matriz[$i][2]."</td></tr><tr>";

     }//fin else
   }//fin for
   }//fin if
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);


   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
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
   $query = "SELECT   num_config,nom_config,if(ind_estado,'Activa','Inactiva'),fot_config,
                      val_pesmax,ind_estado
            FROM     ".BASE_DATOS.".tab_vehige_config
           WHERE num_config = '$GLOBALS[config]'";
  $consulta = new Consulta($query, $this -> conexion);
  $matriz = $consulta -> ret_matriz();


  $inicio[0][0]='-';
  $inicio[0][1]='-';

   echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/conveh.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","Actualizacion de Configuraciones","form_item");
   $formulario -> nueva_tabla();

   $formulario -> nueva_tabla();
   $formulario -> linea("Figura",1);
   $formulario -> nueva_tabla();
   echo "<div align=\"center\"><p><img src=\"../".DIR_APLICA_CENTRAL."/conveh/fotos/".$matriz[0][3]."\" border=\"0\">";
   $formulario -> nueva_tabla();
   $formulario -> linea("Datos Basicos",1);
   $formulario -> nueva_tabla();
   echo "<td class=\"celda\"><b>Designación</b></td><td class=\"celda2\">".$matriz[0][0]."</td></tr><tr>";
   echo "<td class=\"celda\"><b>Nombre</td></b><td class=\"celda2\">".$matriz[0][1]."</td></tr><tr>";
   $formulario -> nueva_tabla();
   $formulario -> caja ("<b>Activar (S/N)</b>:","estado","1",$matriz[0][5],0);
   $formulario -> texto ("<b>Peso Tn</b>","text","peso",1,6,6,"","".$matriz[0][4]."");
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",3,0);
   $formulario -> oculto("config",$GLOBALS[config],0);
   $formulario -> oculto("nombre",$matriz[0][0],0);

   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> botoni("Actualizar","aceptar_act()",0);
   $formulario -> cerrar();
 }//FIN FUNCION ACTUALIZAR
// *********************************************************************************

// *****************************************************
//FUNCION ACTUALIZAR
// *****************************************************
 function Actualizar()
 {
  $fec_actual = date("Y-m-d H:i:s");
  //ultimo despacho

  if(!$GLOBALS[estado])
   $GLOBALS[estado]=0;

  //query de insercion de despacho
   $query = "UPDATE ".BASE_DATOS.".tab_vehige_config
               SET ind_estado = '$GLOBALS[estado]',
                   val_pesmax = '$GLOBALS[peso]',
                   usr_modifi = '$GLOBALS[usuario]',
                   fec_modifi = '$fec_actual'
             WHERE num_config = '$GLOBALS[config]'";
  $insercion = new Consulta($query, $this -> conexion);


    echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>La Configuración $GLOBALS[nombre] ha sido Actualizada Correctamente</b><p></p>";



 }//FIN FUNCION ACTUALIZAR
// *********************************************************************************
}//FIN CLASE PROC_CLASEX
   $proceso = new Act_config_vehicu($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>