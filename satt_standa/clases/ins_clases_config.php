<?php
/****************************************************************************
Ing.Elkin Javier Beleño Correa
23-11-2005
Intrared.net
****************************************************************************/
class Ins_clases_config
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
//********METODOS DE LA CLASE Ins_clases_config*************
 function principal()
 {
  if(!isset($GLOBALS[opcion]))
     $this -> Formulario();
  else
     {
      switch($GLOBALS[opcion])
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
// *****************************************************
// FUNCION QUE PRESENTA EL FORMULARIO DE CAPTURA
// *****************************************************
 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $inicio[0][0]=0;
   $inicio[0][1]='-';


   echo "<script language=\"JavaScript\" src=\"clases.js\"></script>\n";
   $formulario = new Formulario ("index.php","post","<b>Ingreso de Clases</b>","form_insert");
   $formulario -> linea("Datos Basicos de la Clase",1);
   $formulario -> nueva_tabla();
   $formulario -> texto ("Nombre o Descripcion de la Clase:","text","nom",1,30,100,"",$GLOBALS[nom]);
   $formulario -> nueva_tabla();
   $formulario -> linea("Asignacion de Configuraciones",1);
   $formulario -> nueva_tabla();

   $config=$GLOBALS[config];

     if(!isset($GLOBALS[maximo]))
     $GLOBALS[maximo]=1;

     //    echo "maximo".$GLOBALS[maximo];
        ///////////////
        for($i=0;$i<$GLOBALS[maximo];$i++)
        {
         if($config[$i])
         {

          //lista la configuracion del vehiculo
            $query = "SELECT num_config,CONCAT(nom_config,' - ',num_config)
                       FROM ".BASE_DATOS.".tab_vehige_config
                       WHERE num_config = '$config[$i]'";
            $consulta = new Consulta($query, $this -> conexion);
            $config1 = $consulta -> ret_matriz();

            $formulario -> lista("Configuracion:", "config[$i]", $config1, 1);
         }      //fin if
         else
         {
           $query = "SELECT num_config,CONCAT(nom_config,' - ',num_config)
                       FROM ".BASE_DATOS.".tab_vehige_config  ORDER BY 1";
            $consulta = new Consulta($query, $this -> conexion);
            $config1 = $consulta -> ret_matriz();
            $config1 = array_merge($inicio,$config1);

            $formulario -> lista("Configuracion:", "config[$i]", $config1, 1);

         }//fin else
        }//fin for

            $formulario -> nueva_tabla();
            $formulario -> botoni("Otra","form_insert.submit()",0);
            $GLOBALS[maximo]=$GLOBALS[maximo]+1;


   $formulario -> nueva_tabla();
   $formulario -> texto ("Observaciones","textarea","obs",1,55,2,"","");
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("maximo","$GLOBALS[maximo]",0);
   $formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
   $formulario -> botoni("Aceptar","aceptar_insert()",0);
   $formulario -> botoni("Borrar","form_insert.reset()",0);
   $formulario -> cerrar();
 }//FIN FUNCTION CAPTURA
// *****************************************************
//FUNCION INSERTAR
// *****************************************************
 function Insertar()
 {
   $fec_actual = date("Y-m-d H:i:s");
   //trae el consecutivo de la tabla
   $query = "SELECT Max(cod_clasex) AS maximo
               FROM ".BASE_DATOS.".tab_genera_clases ";
   $consec = new Consulta($query, $this -> conexion);
   $ultimo = $consec -> ret_matriz();
   $ultimo_consec = $ultimo[0][0];
   $nuevo_consec = $ultimo_consec+1;


   //query de insercion
   $query = "INSERT INTO ".BASE_DATOS.".tab_genera_clases
             VALUES ('$nuevo_consec','$GLOBALS[nom]','$GLOBALS[obs]',
             '$GLOBALS[usuario]','$fec_actual','$GLOBALS[usuario]','$fec_actual') ";
   $consulta = new Consulta($query, $this -> conexion,"BR");

   $config=$GLOBALS[config];


   for($i=0;$i<$GLOBALS[maximo];$i++)
      {
         if($config[$i])
         {
            //inserta el valor de la tarifa
            $query2 = "INSERT INTO ".BASE_DATOS.".tab_config_clasex
                           VALUES ('$nuevo_consec','$config[$i]')";
           $insercion2 = new Consulta($query2, $this -> conexion,"R");
         }
      }
  if(!mysql_error())
  {
     $consulta = new Consulta("COMMIT", $this -> conexion);
     echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>El Clase $GLOBALS[nom]  ha sido Ingresada Correctamente</b><p></p>";
  }
  else
     $consulta = new Consulta("ROLLBACK", $this -> conexion);

 }//FIN FUNCTION INSERTAR

}//FIN CLASE Ins_clases_config
     $proceso = new Ins_clases_config($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);
?>