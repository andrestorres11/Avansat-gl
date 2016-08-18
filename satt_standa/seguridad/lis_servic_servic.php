<?php

/****************************************************************************
NOMBRE:   LIS_SERVIC.INC
FUNCION:  LISTAR SERVICIOS
****************************************************************************/

class Proc_servic
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

  if(!isset($GLOBALS[opcion]))

     $this -> Listar();

  else

     {

      switch($GLOBALS[opcion])

       {

        case "1":

          $this -> Datos();

          break;

       }//FIN SWITCH

     }// FIN ELSE GLOBALS OPCION

 }//FIN FUNCION PRINCIPAL

// *****************************************************

// FUNCION QUE PRESENTA EL FORMULARIO DE CAPTURA

// *****************************************************

 function Listar()

 {

            $query = "SELECT cod_servic, nom_servic

                  FROM ".CENTRAL.".tab_genera_servic

              ORDER BY 1";

      $consulta = new Consulta($query, $this -> conexion);

      $matriz = $consulta -> ret_matriz();



     for($i=0;$i<sizeof($matriz);$i++)

      {

        $bandera1 = 0;

        $hijo = $matriz[$i][0];

        $cont = 0;

        while($bandera1 < 1)

        {

                $query = "SELECT a.cod_serpad,b.nom_servic

                      FROM ".CENTRAL.".tab_servic_servic a,".CENTRAL.".tab_genera_servic b

                     WHERE a.cod_serpad = b.cod_servic AND

                           a.cod_serhij = '".$hijo."' ";

          $consulta = new Consulta($query, $this -> conexion);

          $matriz1 = $consulta -> ret_matriz();

          if(sizeof($matriz1) == 0)

             $bandera1 = 1;

          else

          {

             $nombres[$cont]=$matriz1[0][1];

             $cont = $cont + 1;

             $hijo = $matriz1[0][0];

          }//fin if

        }//fin while

        for($j=0;$j<$cont;$j++)

        {

         $matriz[$i][1] = $matriz[$i][1]." - ".$nombres[$j];

        }//fin for j

      }//fin for i



     for($i=0;$i<sizeof($matriz);$i++)

        $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&servic=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";



   $formulario = new Formulario ("index.php","post","RESULTADO DE LA CONSULTA","form_item");

   $formulario -> nueva_tabla();

   $formulario -> linea("SE ENCONTRARON ".sizeof($matriz)." REGISTROS",0,0,"t2");

   $formulario -> nueva_tabla();

   if(sizeof($matriz) > 0)

   {

   $formulario -> linea("C&Oacute;DIGO",0,0,"t");

   $formulario -> linea("SERVICIO",1,0,"t");

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

   $formulario -> boton("Volver <==","button\" onClick=\"javascript:history.go(-1)",0);

   $formulario -> nueva_tabla();

   $formulario -> oculto("usuario","$usuario",0);

   $formulario -> oculto("opcion",1,0);

   $formulario -> oculto("window","central",0);

   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

   $formulario -> cerrar();

 }//FIN FUNCTION CAPTURA

// *****************************************************

// FUNCION QUE PRESENTA EL FORMULARIO DE CAPTURA 2

// *****************************************************

 function Datos()

 {

            //trae los datos basicos del servicio

      $query = "SELECT a.cod_servic,a.nom_servic,a.rut_archiv,c.nom_servic

                  FROM ".CENTRAL.".tab_genera_servic a LEFT JOIN

                       ".CENTRAL.".tab_servic_servic b ON

                       a.cod_servic = b.cod_serhij LEFT JOIN

                       ".CENTRAL.".tab_genera_servic c ON

                       b.cod_serpad = c.cod_servic

                 WHERE a.cod_servic = '$GLOBALS[servic]'

              ORDER BY 1";

      $consulta = new Consulta($query, $this -> conexion);

      $matriz0 = $consulta -> ret_matriz();



            $formulario = new Formulario("index.php","post","<b>LISTAR SERVICIOS</b>", "form_ins");
            $formulario -> nueva_tabla();

      echo "<td class=\"celda\"><b>C�DIGO</b></td><td class=\"celda2\">".$matriz0[0][0]."</td></tr><tr>";

      echo "<td class=\"celda\"><b>NOMBRE</b></td><td class=\"celda2\">".$matriz0[0][1]."</td></tr><tr>";

      echo "<td class=\"celda\"><b>RUTA ARCHIVO</b></td><td class=\"celda2\">".$matriz0[0][2]."</td></tr><tr>";

      echo "<td class=\"celda\"><b>SERVICIO PADRE</b></td><td class=\"celda2\">".$matriz0[0][3]."</td></tr><tr>";

      $formulario -> nueva_tabla();

                   $formulario -> linea("&Aacute;rbol",0,0,"h");

                   $formulario -> nueva_tabla();

      //para imprimira el arbol

      $bandera1 = 0;

      $hijo = $matriz0[0][0];

      $cont = 0;

      while($bandera1 < 1)

      {

              $query = "SELECT a.cod_serpad,b.nom_servic

                    FROM ".CENTRAL.".tab_servic_servic a,".CENTRAL.".tab_genera_servic b

                   WHERE a.cod_serpad = b.cod_servic AND

                         a.cod_serhij = '".$hijo."' ";

        $consulta = new Consulta($query, $this -> conexion);

        $matriz1 = $consulta -> ret_matriz();

        if(sizeof($matriz1) == 0)

           $bandera1 = 1;

        else

        {

           echo "<td class=\"celda\">";

           for($i=0;$i<$cont;$i++)

               echo "&nbsp;&nbsp;&nbsp;";

           echo "".$matriz1[0][1]."</td></tr><tr>";

           $cont = $cont + 1;

           $hijo = $matriz1[0][0];

        }//fin if

      }//fin while



                   $formulario -> nueva_tabla();

      $formulario -> oculto("opcion",1, 0);

            $formulario -> oculto("cod_servic", $GLOBALS["cod_servic"], 0);

      $formulario -> oculto("window","central", 0);

      $formulario -> cerrar();

 }//FIN FUNCTION CAPTURA



}//FIN CLASE PROC_SERVIC

     $proceso = new Proc_servic($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

?>