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

     $this -> Formulario();

  else

     {

      switch($GLOBALS[opcion])

       {

        case "1":

          $this -> Insertar();

          break;

       }//FIN SWITCH

     }// FIN ELSE GLOBALS OPCION

 }//FIN FUNCION PRINCIPAL



 function Formulario()

 {

      $inicio[0][0] = 0;

      $inicio[0][1] = '-';

      //trae los Clientes

      $query = "SELECT a.cod_tercer, a.abr_tercer

                FROM ".BASE_DATOS.".tab_tercer_tercer a,

                       ".BASE_DATOS.".tab_tercer_activi b

                 WHERE a.cod_tercer = b.cod_tercer AND

                       b.cod_activi = ".COD_FILTRO_CLIENT."

              ORDER BY 2";

      $consulta = new Consulta($query, $this -> conexion);

      $clientes = $consulta -> ret_matriz();

      $clientes = array_merge($inicio,$clientes);

      //trae los conductores

            $query = "SELECT a.cod_tercer, a.abr_tercer

                  FROM ".BASE_DATOS.".tab_tercer_tercer a,

                       ".BASE_DATOS.".tab_tercer_activi b

                 WHERE a.cod_tercer = b.cod_tercer AND

                       b.cod_activi = ".COD_FILTRO_CONDUC."

              ORDER BY 2";

      $consulta = new Consulta($query, $this -> conexion);

      $conducs = $consulta -> ret_matriz();

      $conducs = array_merge($inicio,$conducs);

      //trae los puestos de control

            $query = "SELECT cod_contro,nom_contro

                  FROM ".BASE_DATOS.".tab_genera_contro

              ORDER BY 2";

      $consulta = new Consulta($query, $this -> conexion);

      $contros = $consulta -> ret_matriz();

      $contros = array_merge($inicio,$contros);





      echo "<script language=\"JavaScript\" src=\"../sadc_standa/js/usrwap.js\"></script>\n";

      $formulario = new Formulario("index.php","post","INSERTAR USUARIO WAP", "form_ins");

      $formulario -> texto("Usuario:","text","usuari", 0, 15,  20,"","");

      $formulario -> texto("Clave:","password","clave1", 0, 15,  20,"","");

      $formulario -> texto("Confirmar Clave:","password","clave2", 1, 15,  20,"","");

      $formulario -> nueva_tabla();

      $formulario -> radio("Sin Filtro:", "filtro", "0", 1, 0);

      $formulario -> radio("Cliente:", "filtro", "1", 0, 0);

      $formulario -> radio("Puesto de Control:", "filtro", "2", 0, 0);

      $formulario -> radio("Conductor:", "filtro", "3", 0, 0);

      $formulario -> nueva_tabla();

      $formulario -> lista("Cliente:", "cliente", $clientes, 0);

      $formulario -> lista("Puesto de Control:", "contro", $contros, 1);

      $formulario -> lista("Conductor:", "conduc", $conducs, 0);

      $formulario -> nueva_tabla();

      $formulario -> oculto("usuario","$usuario",0);

      $formulario -> oculto("opcion",0, 0);

      $formulario -> oculto("maximo","".sizeof($matriz)."", 0);

      $formulario -> oculto("cod_servic", $GLOBALS[cod_servic], 0);

      $formulario -> oculto("window","central", 0);

      $formulario -> botoni("Insertar", "onClick=aceptar_ins()", 0);

      $formulario -> cerrar();

 }//FIN FUNCTION CAPTURA

// *****************************************************

//FUNCION INSERTAR

// *****************************************************

 function Insertar()

 {

   $fec_actual = date("Y-m-d H:i:s");

   if($GLOBALS[filtro] == 1)

     $id_filtro = $GLOBALS[cliente];

   else if($GLOBALS[filtro] == 2)

     $id_filtro = $GLOBALS[contro];

   else if($GLOBALS[filtro] == 3)

     $id_filtro = $GLOBALS[conduc];

   else

     $id_filtro = 0;

   //reasignacion de variables

   $clave = base64_encode($GLOBALS[clave1]);

   //query de insercion

   $query = "INSERT INTO ".BASE_DATOS.".tab_usuari_wapxxx

             VALUES ('$GLOBALS[usuari]','$clave','$GLOBALS[filtro]','$id_filtro') ";

   $consulta = new Consulta($query, $this -> conexion,"BR");



  if($insercion = new Consulta("COMMIT", $this -> conexion))
  {
     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Insertar Otro Usuario</a></b>";

     $mensaje =  "El Usuario <b>".$GLOBALS[usuari]."</b> Se Inserto con Exito".$link_a;
     $mens = new mensajes();
     $mens -> correcto("INSERTAR USUARIOS WAP",$mensaje);
  }

 }
}

$proceso = new Proc_usuari($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

?>