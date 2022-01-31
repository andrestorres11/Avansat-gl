<?php
/****************************************************************************
Ing.Elkin Javier Beleño Correa
23-11-2005
Intrared.net
****************************************************************************/
class Act_clases_config
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

   echo "<script language=\"JavaScript\" src=\"clases.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","Bucar y Actualizar Clases","form_act");
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
   $formulario = new Formulario ("index.php","post","Actualizacion de Clases","form_item");
   $formulario -> nueva_tabla();
   echo "<td class=\"celda\">Codigo</td><td class=\"celda2\">".$matriz[0][0]."</td></tr><tr>";
   $formulario -> texto ("Nombre","text","nombre",1,50,100,"","".$matriz[0][1]."");
   $formulario -> nueva_tabla();

   if(!isset($_REQUEST[maximo]))
   {
      $query = "SELECT num_config
                   FROM ".BASE_DATOS.".tab_config_clasex
                   WHERE cod_clasex = '$_REQUEST[clasex]'";
      $consulta = new Consulta($query, $this -> conexion);
      $config_a = $consulta -> ret_matriz();
      $_REQUEST[maximo]=sizeof($config_a);
   }


      if(!$_REQUEST[maximo])
         $_REQUEST[maximo]=1;

   $query = "SELECT num_config,CONCAT(nom_config,' - ',num_config)
              FROM ".BASE_DATOS.".tab_vehige_config";
   $consulta = new Consulta($query, $this -> conexion);
   $config = $consulta -> ret_matriz();


        for($i=0;$i<$_REQUEST[maximo];$i++)
        {
          if($_REQUEST[config][$i])
             $config_a[$i][0] = $_REQUEST[config][$i];


          //lista la configuracion del vehiculo
          $query = "SELECT num_config,CONCAT(nom_config,' - ',num_config)
                     FROM ".BASE_DATOS.".tab_vehige_config
                       WHERE num_config = '".$config_a[$i][0]."'";
            $consulta = new Consulta($query, $this -> conexion);
            $config_n = $consulta -> ret_matriz();

            $config_n = array_merge($config_n, $inicio, $config);
            $formulario -> caja ("","asigna[$i]",1,1,0);
            $formulario -> lista("Configuracion:", "config[$i]", $config_n, 1);

        }//fin for

            $formulario -> nueva_tabla();
            $formulario -> botoni("Otra","form_item.submit()",0);
            $_REQUEST[maximo]=$_REQUEST[maximo]+1;

   $formulario -> nueva_tabla();
   $formulario -> texto ("Observaciones","textarea","obs",1,55,2,"","".$matriz[0][2]."");
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",2,0);
   $formulario -> oculto("clasex",$_REQUEST[clasex],0);
   $formulario -> oculto("maximo","$_REQUEST[maximo]",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
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


  //query de insercion de despacho
  $query = "UPDATE ".BASE_DATOS.".tab_genera_clases
               SET nom_clasex = '$_REQUEST[nombre]',
                   obs_clasex = '$_REQUEST[obs]'
             WHERE cod_clasex = '$_REQUEST[clasex]'";
  $insercion = new Consulta($query, $this -> conexion,"BR");

  $query = "DELETE FROM ".BASE_DATOS.".tab_config_clasex
             WHERE cod_clasex = '$_REQUEST[clasex]'";
  $eliminar = new Consulta($query, $this -> conexion,"R");

  $config = $_REQUEST[config];
  $asigna = $_REQUEST[asigna];

  for($i=0;$i<$_REQUEST[maximo];$i++)
      {
         if($config[$i] AND $asigna[$i])
         {
            //inserta el valor de la tarifa
            $query2 = "INSERT INTO ".BASE_DATOS.".tab_config_clasex
                           VALUES ('$_REQUEST[clasex]','$config[$i]')";
           $insercion2 = new Consulta($query2, $this -> conexion,"R");
         }
      }
  if(!mysql_error())
  {
     $consulta = new Consulta("COMMIT", $this -> conexion);
     echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>El Clase $_REQUEST[nombre]  ha sido Actualizada Correctamente</b><p></p>";
  }
  else
     $consulta = new Consulta("ROLLBACK", $this -> conexion);



 }//FIN FUNCION ACTUALIZAR
// *********************************************************************************
}//FIN CLASE PROC_CLASEX
   $proceso = new Act_clases_config($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>