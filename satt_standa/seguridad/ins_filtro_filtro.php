<?php

/****************************************************************************

NOMBRE:   INS_FILTRO.INC

FUNCION:  INSERTAR FILTROS

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

  $datos_usuario = $this -> usuario -> retornar();

  $this -> principal();

 }

//********METODOS

 function principal()

 {

  if(!isset($_REQUEST[opcion]))

     $this -> Formulario();

  else

     {

      switch($_REQUEST[opcion])

       {

        case "1":

          $this -> Insertar();
          $this -> Formulario();   

          break;

       }//FIN SWITCH

     }// FIN ELSE GLOBALS OPCION

 }//FIN FUNCION PRINCIPAL

// *****************************************************

// FUNCION QUE PRESENTA EL FORMULARIO DE CAPTURA

// *****************************************************

 function Formulario()

 {

   echo "<script language=\"JavaScript\" src=\"js/filtro.js\"></script>\n";

   $formulario = new Formulario("index.php","post","<b></b>", "form_ins");

   $formulario -> texto("Nombre:","text","nombre", 1, 50,150,"","");

   $formulario -> texto("Código SQL:","textarea","query", 1,40,5,"","");

          $formulario -> nueva_tabla();

   $formulario -> oculto("opcion",1, 0);

   $formulario -> oculto("maximo","".sizeof($matriz)."", 0);

   $formulario -> oculto("cod_servic", $_REQUEST["cod_servic"], 0);

   $formulario -> oculto("window","central", 0);

   $formulario -> boton("Insertar","button\" onClick=\"aceptar_ins()",0);

   $formulario -> cerrar();

 }//FIN FUNCTION CAPTURA

// *****************************************************

//FUNCION INSERTAR

// *****************************************************

 function Insertar()

 {

   $fec_actual = date("Y-m-d H:i:s");

   $query = "SELECT Max(cod_filtro) AS maximo

               FROM ".CENTRAL.".tab_genera_filtro ";

   $consec = new Consulta($query, $this -> conexion);

   $ultimo = $consec -> ret_matriz();

   $ultimo_consec = $ultimo[0][0];

   $nuevo_consec = $ultimo_consec+1;

   //query de insercion

   $query = "INSERT INTO ".CENTRAL.".tab_genera_filtro

             VALUES ('$nuevo_consec','$_REQUEST[nombre]','$_REQUEST[query]',
                      '$_REQUEST[usuario]', '$fec_actual',
                      '$_REQUEST[usuario]', '$fec_actual') ";

   $consulta = new Consulta($query, $this -> conexion);



   echo "<br><br><b>TRANSACCION EXITOSA <br> EL FILTRO $_REQUEST[nombre] FUE INSERTADO</b>";

 }//FIN FUNCTION INSERTAR



}//FIN CLASE PROC_CIUDAD

     $proceso = new Proc_filtro($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

?>
