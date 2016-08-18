<?php

/****************************************************************************

NOMBRE:   MODULO_LINEAS_LIS.PHP

FUNCION:  LISTAR LINEAS DE VEHICULOS

****************************************************************************/

class Proc_lineas

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

        case "2":

          $this -> Resultado();

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



   echo "<script language=\"JavaScript\" src=\"js/lineas.js\"></script>\n";

   $formulario = new Formulario ("index.php","post","BUSCAR Y LISTAR LINEAS DE VEHICULOS","form_list");

   $formulario -> linea("INSERTE UN TEXTO PARA INICIAR LA BUSQUEDA",1);

   $formulario -> nueva_tabla();

   $formulario -> texto ("TEXTO","text","linea",1,50,255,"","");

   $formulario -> nueva_tabla();

   $formulario -> oculto("usuario","$usuario",0);

   $formulario -> oculto("opcion",2,0);

   $formulario -> oculto("valor",$valor,0);

   $formulario -> oculto("window","central",0);

   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

   $formulario -> boton("Buscar","button\" onClick=\"aceptar_lis() ",0);

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

  $query = "SELECT a.cod_lineax,a.nom_lineax,b.nom_marcax

            FROM ".BASE_DATOS.".tab_vehige_lineas a,".BASE_DATOS.".tab_genera_marcas b

           WHERE a.cod_marcax = b.cod_marcax AND

                 a.nom_lineax LIKE '%$GLOBALS[linea]%'

        ORDER BY 3,2";

  $consec = new Consulta($query, $this -> conexion);

  $matriz = $consec -> ret_matriz();



   $formulario = new Formulario ("index.php","post","RESULTADO DE LA CONSULTA","form_item");

   $formulario -> linea("<b>SE ENCONTRARON ".sizeof($matriz)." REGISTROS</b>",0);

   $formulario -> nueva_tabla();

   if(sizeof($matriz) > 0)

   {

   $formulario -> linea("CODIGO",0);

   $formulario -> linea("DESCRIPCION",0);

   $formulario -> linea("MARCA",1);

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

   $formulario -> boton("Volver <==","button\" onClick=\"javascript:history.go(-1)",0);

   $formulario -> nueva_tabla();

   $formulario -> oculto("usuario","$usuario",0);

   $formulario -> oculto("opcion",1,0);

   $formulario -> oculto("valor",$valor,0);

   $formulario -> oculto("window","central",0);

   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

   $formulario -> cerrar();

 }//FIN FUNCION ACTUALIZAR

// *********************************************************************************

// *********************************************************************************

}//FIN CLASE PROC_LINEAS

     $proceso = new Proc_lineas($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
