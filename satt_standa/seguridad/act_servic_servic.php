<?php

/****************************************************************************

NOMBRE:   ACT_SERVIC.INC

FUNCION:  ACTUALIZAR SERVICIOS

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

  $this -> principal();

 }

//********METODOS

 function principal()

 {

  if(!isset($_REQUEST[opcion]))

     $this -> Listar();

  else

     {

      switch($_REQUEST[opcion])

       {

        case "1":

          $this -> Datos();

          break;

        case "2":

          $this -> Actualizar();

          break;

       }//FIN SWITCH

     }// FIN ELSE GLOBALS OPCION

 }//FIN FUNCION PRINCIPAL

// *****************************************************

// FUNCION QUE PRESENTA EL FORMULARIO DE CAPTURA

// *****************************************************

 function Listar()

 {

            $usuario = $this -> usuario -> cod_usuari;

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

        $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&servic=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";



   $formulario = new Formulario ("index.php","post","RESULTADO DE LA CONSULTA","form_item");

   $formulario -> nueva_tabla();

   $formulario -> linea("<b>SE ENCONTRARON ".sizeof($matriz)." REGISTROS</b>",0,0,"t2");

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

   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);

   $formulario -> cerrar();

 }//FIN FUNCTION CAPTURA

// *****************************************************

// FUNCION QUE PRESENTA EL FORMULARIO DE CAPTURA 2

// *****************************************************

 function Datos()

 {

            $usuario = $this -> usuario -> cod_usuari;



            //trae los datos basicos del servicio

      $query = "SELECT a.cod_servic,a.nom_servic,a.rut_archiv,c.nom_servic,a.des_servic,

                                                a.rut_jscrip,a.bod_jscrip

                  FROM ".CENTRAL.".tab_genera_servic a LEFT JOIN

                       ".CENTRAL.".tab_servic_servic b ON

                       a.cod_servic = b.cod_serhij LEFT JOIN

                       ".CENTRAL.".tab_genera_servic c ON

                       b.cod_serpad = c.cod_servic

                 WHERE a.cod_servic = '$_REQUEST[servic]'

              ORDER BY 1";

      $consulta = new Consulta($query, $this -> conexion);

      $matriz0 = $consulta -> ret_matriz();



            //trae los servicios que no tienen ruta de archivo

            //estos pueden ser el padre del nuevo

      $query = "SELECT cod_servic, nom_servic

                  FROM ".CENTRAL.".tab_genera_servic

                 WHERE rut_archiv Is Null

              ORDER BY 2";

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



      //trae el nombre del servicio anterior

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

             //para guardar el nombre del padre

             $nombre_a[$cont]=$matriz1[0][1];

             //para guardar el codigo del padre

             $codigo_a[$cont]=$matriz1[0][0];

             $cont = $cont + 1;

             $hijo = $matriz1[0][0];

          }//fin if

        }//fin while

        //asigna los valores del servicio padre

        for($j=0;$j<$cont;$j++)

        {

         if($j == 0)

         {

            $matriz_a[0][1] = $matriz_a[0][1]."".$nombre_a[$j];

            $matriz_a[0][0] = $codigo_a[$j];

         }//fin if

         else

            $matriz_a[0][1] = $matriz_a[0][1]." - ".$nombre_a[$j];

        }//fin for j

      $inicio[0][0] = 0;
      $inicio[0][1] = '-';

	 if(!$matriz_a)
      $matriz = array_merge($inicio,$matriz);
     else
      $matriz = array_merge($matriz_a,$inicio,$matriz);


      //trae los perfiles

      $query = "SELECT cod_perfil, nom_perfil

                  FROM ".BASE_DATOS.".tab_genera_perfil

              ORDER BY 2";

      $consulta = new Consulta($query, $this -> conexion);

      $matriz1 = $consulta -> ret_matriz();

      //trae los usuarios

      $query = "SELECT cod_usuari, nom_usuari

                  FROM ".BASE_DATOS.".tab_genera_usuari

                 WHERE cod_perfil Is Null

              ORDER BY 2";

      $consulta = new Consulta($query, $this -> conexion);

      $matriz2 = $consulta -> ret_matriz();



      echo "<script language=\"JavaScript\" src=\"js/servic.js\"></script>\n";

            $formulario = new Formulario("index.php","post","<b>ACTUALIZAR SERVICIOS</b>", "form_ins");

            $formulario -> nueva_tabla();

      echo "<td class=\"celda\"><b>CÓDIGO</b></td><td class=\"celda2\">".$matriz0[0][0]."</td></tr><tr>";

      $formulario -> texto("Nombre:","text","nombre", 1,50,150,"",$matriz0[0][1]);

      $formulario -> texto("Descripcion:","text","descri", 1,50,255,"",$matriz0[0][4]);

      $formulario -> texto("Ruta Archivo:","text","ruta", 1,50,150,"",$matriz0[0][2]);

      $formulario -> texto("Ruta Jscript:","text","jscript", 1,50,150,"",$matriz0[0][5]);

      $formulario -> texto("Body Jscript:","text","bjscrip", 1,50,150,"",$matriz0[0][6]);

      $formulario -> lista("Servicio Padre:", "padre", $matriz, 1);

            $formulario -> nueva_tabla();

            $formulario -> linea("PERFILES",0,0,"t");

            $formulario -> nueva_tabla();

            for($i=0;$i<sizeof($matriz1);$i++)

            {

        //Pregunta si habia sido asignado

        $query = "SELECT cod_perfil

                    FROM ".BASE_DATOS.".tab_perfil_servic

                   WHERE cod_servic = '".$matriz0[0][0]."' AND

                         cod_perfil = '".$matriz1[$i][0]."' ";

        $consulta = new Consulta($query, $this -> conexion);

        $matriz3 = $consulta -> ret_matriz();



              if($i%2 == 1 AND sizeof($matriz3) == 1)

           $formulario -> caja("".$matriz1[$i][1]."","perfiles[$i]", "".$matriz1[$i][0]."", 1,1);

              else if($i%2 == 1 AND sizeof($matriz3) == 0)

           $formulario -> caja("".$matriz1[$i][1]."","perfiles[$i]", "".$matriz1[$i][0]."", 0,1);

              else if($i%2 == 0 AND sizeof($matriz3) == 1)

           $formulario -> caja("".$matriz1[$i][1]."","perfiles[$i]", "".$matriz1[$i][0]."", 1,0);

        else

           $formulario -> caja("".$matriz1[$i][1]."","perfiles[$i]", "".$matriz1[$i][0]."", 0,0);

      }//fin for

            $formulario -> nueva_tabla();

            $formulario -> linea("USUARIOS",0,0,"t");

            $formulario -> nueva_tabla();

            for($i=0;$i<sizeof($matriz2);$i++)

            {

        //Pregunta si habia sido asignado

        $query = "SELECT cod_usuari

                    FROM ".BASE_DATOS.".tab_servic_usuari

                   WHERE cod_servic = '".$matriz0[0][0]."' AND

                         cod_usuari = '".$matriz2[$i][0]."' ";

        $consulta = new Consulta($query, $this -> conexion);

        $matriz4 = $consulta -> ret_matriz();



              if($i%2 == 1 AND sizeof($matriz4) == 1)

           $formulario -> caja("".$matriz2[$i][1]."","usuarios[$i]", "".$matriz2[$i][0]."", 1,1);

              else if($i%2 == 1 AND sizeof($matriz4) == 0)

           $formulario -> caja("".$matriz2[$i][1]."","usuarios[$i]", "".$matriz2[$i][0]."", 0,1);

              else if($i%2 == 0 AND sizeof($matriz4) == 1)

           $formulario -> caja("".$matriz2[$i][1]."","usuarios[$i]", "".$matriz2[$i][0]."", 1,0);

        else

           $formulario -> caja("".$matriz2[$i][1]."","usuarios[$i]", "".$matriz2[$i][0]."", 0,0);

      }//fin for

            $formulario -> nueva_tabla();

      $formulario -> oculto("usuario","$usuario",0);

      $formulario -> oculto("opcion",0, 0);

      $formulario -> oculto("max_per","".sizeof($matriz1)."", 0);

      $formulario -> oculto("max_usu","".sizeof($matriz2)."", 0);

      $formulario -> oculto("codigo","".$matriz0[0][0]."", 0);

      $formulario -> oculto("cod_servic", $_REQUEST[cod_servic], 0);

      $formulario -> oculto("window","central", 0);

      $formulario -> boton("Actualizar", "button\" onClick=\"aceptar_ins2()", 0);

      $formulario -> cerrar();

 }//FIN FUNCTION CAPTURA

// *****************************************************

//FUNCION ACTUALIZAR

// *****************************************************

 function Actualizar()

 {

   $fec_actual = date("Y-m-d H:i:s");

   //PRIMERO SE BORRA TODAS LAS ASIGNACIONES DE ESTE SERVICIO

   //se eliminan las relaciones con los servicios

   //padres si estos no tienen mas hijos relacionados

   //PRIMERO CON LOS PERFILES

   $bandera1 = 0;

   $hijo = $_REQUEST[codigo];

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

    {

      //lista los perfiles

             $query = "SELECT cod_perfil

                  FROM ".BASE_DATOS.".tab_genera_perfil ";

      $consulta = new Consulta($query, $this -> conexion);

      $matriz2 = $consulta -> ret_matriz();

      //recorre el resultado de los perfiles

      for($i=0;$i<sizeof($matriz2);$i++)

      {

                 $query = "SELECT a.cod_serhij

                      FROM ".CENTRAL.".tab_servic_servic a,

                           ".BASE_DATOS.".tab_perfil_servic b

                     WHERE a.cod_serhij = b.cod_servic AND

                           b.cod_perfil = '".$matriz2[$i][0]."' AND

                           a.cod_serpad = '".$hijo."' ";

          $consulta = new Consulta($query, $this -> conexion);

          $matriz3 = $consulta -> ret_matriz();

        if(sizeof($matriz3) == 0)

        {

          $query = "DELETE FROM ".BASE_DATOS.".tab_perfil_servic

                          WHERE cod_servic = '".$hijo."' AND

                                cod_perfil = '".$matriz2[$i][0]."' ";

          $delete = new Consulta($query, $this -> conexion);

        }//fin if

      }//fin for

       $bandera1 = 1;

    }

    else

    {

      //lista los perfiles

             $query = "SELECT cod_perfil

                  FROM ".BASE_DATOS.".tab_genera_perfil ";

      $consulta = new Consulta($query, $this -> conexion);

      $matriz2 = $consulta -> ret_matriz();

      //recorre el resultado de los perfiles

      for($i=0;$i<sizeof($matriz2);$i++)

      {

                 $query = "SELECT a.cod_serhij

                      FROM ".CENTRAL.".tab_servic_servic a,

                           ".BASE_DATOS.".tab_perfil_servic b

                     WHERE a.cod_serhij = b.cod_servic AND

                           b.cod_perfil = '".$matriz2[$i][0]."' AND

                           a.cod_serpad = '".$matriz1[0][0]."' ";

          $consulta = new Consulta($query, $this -> conexion);

          $matriz3 = $consulta -> ret_matriz();

        if(sizeof($matriz3) == 1)

        {

          $query = "DELETE FROM ".BASE_DATOS.".tab_perfil_servic

                          WHERE cod_servic = '".$matriz1[0][0]."' AND

                                cod_perfil = '".$matriz2[$i][0]."' ";

          $delete = new Consulta($query, $this -> conexion);

        }//fin if

      }//fin for

      $hijo = $matriz1[0][0];

    }//fin else

   }//fin while



   //LUEGO CON LOS USUARIOS

   $bandera1 = 0;

   $hijo = $_REQUEST[codigo];

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

    {

      //lista los usuarios

             $query = "SELECT cod_usuari

                  FROM ".BASE_DATOS.".tab_genera_usuari ";

      $consulta = new Consulta($query, $this -> conexion);

      $matriz2 = $consulta -> ret_matriz();

      //recorre el resultado de los perfiles

      for($i=0;$i<sizeof($matriz2);$i++)

      {

                 $query = "SELECT a.cod_serhij

                      FROM ".CENTRAL.".tab_servic_servic a,

                           ".BASE_DATOS.".tab_servic_usuari b

                     WHERE a.cod_serhij = b.cod_servic AND

                           b.cod_usuari = '".$matriz2[$i][0]."' AND

                           a.cod_serpad = '".$hijo."' ";

          $consulta = new Consulta($query, $this -> conexion);

          $matriz3 = $consulta -> ret_matriz();

        if(sizeof($matriz3) == 0)

        {

          $query = "DELETE FROM ".BASE_DATOS.".tab_servic_usuari

                          WHERE cod_servic = '".$hijo."' AND

                                cod_usuari = '".$matriz2[$i][0]."' ";

          $delete = new Consulta($query, $this -> conexion);

        }//fin if

      }//fin for

       $bandera1 = 1;

    }

    else

    {

      //lista los usuarios

             $query = "SELECT cod_usuari

                  FROM ".BASE_DATOS.".tab_genera_usuari ";

      $consulta = new Consulta($query, $this -> conexion);

      $matriz2 = $consulta -> ret_matriz();

      //recorre el resultado de los perfiles

      for($i=0;$i<sizeof($matriz2);$i++)

      {

                 $query = "SELECT a.cod_serhij

                      FROM ".CENTRAL.".tab_servic_servic a,

                           ".BASE_DATOS.".tab_servic_usuari b

                     WHERE a.cod_serhij = b.cod_servic AND

                           b.cod_usuari = '".$matriz2[$i][0]."' AND

                           a.cod_serpad = '".$matriz1[0][0]."' ";

          $consulta = new Consulta($query, $this -> conexion);

          $matriz3 = $consulta -> ret_matriz();

        if(sizeof($matriz3) == 1)

        {

          $query = "DELETE FROM ".BASE_DATOS.".tab_servic_usuari

                          WHERE cod_servic = '".$matriz1[0][0]."' AND

                                cod_usuari = '".$matriz2[$i][0]."' ";

          $delete = new Consulta($query, $this -> conexion);

        }//fin if

      }//fin for

      $hijo = $matriz1[0][0];

    }//fin else

   }//fin while



   //borra la relacion con el padre

   $query = "DELETE FROM ".CENTRAL.".tab_servic_servic

                   WHERE cod_serhij = '$_REQUEST[codigo]' ";

   $delete = new Consulta($query, $this -> conexion);

   //borra la relacion con los perfiles

   $query = "DELETE FROM ".BASE_DATOS.".tab_perfil_servic

                   WHERE cod_servic = '$_REQUEST[codigo]' ";

   $delete = new Consulta($query, $this -> conexion);

   //borra la relacion con los usuarios

   $query = "DELETE FROM ".BASE_DATOS.".tab_servic_usuari

                   WHERE cod_servic = '$_REQUEST[codigo]' ";

   $delete = new Consulta($query, $this -> conexion);



   //reasignacion de variables

   $perfiles = $_REQUEST[perfiles];

   $usuarios = $_REQUEST[usuarios];

   $nuevo_consec = $_REQUEST[codigo];



   //query de insercion del servicio

   $query = "UPDATE ".CENTRAL.".tab_genera_servic

                SET nom_servic = '$_REQUEST[nombre]',

                    des_servic = '$_REQUEST[descri]',

                    rut_archiv = '$_REQUEST[ruta]',

                    rut_jscrip = '$_REQUEST[jscript]',

                    bod_jscrip = '$_REQUEST[bjscrip]',

                    usr_modifi = '$_REQUEST[usuario]',

                    fec_modifi = '$fec_actual'

              WHERE cod_servic = '$nuevo_consec' ";

   $consulta = new Consulta($query, $this -> conexion);

   //query de insercion de la relacion con el servicio padre

   if($_REQUEST[padre] != Null OR $_REQUEST[padre] != 0)

   {

      $query = "INSERT INTO ".CENTRAL.".tab_servic_servic

                VALUES ('$_REQUEST[padre]','$nuevo_consec') ";

      $consulta = new Consulta($query, $this -> conexion);

    }//fin if

   //asignacion de servicios a los perfiles elegidos

   for($i=0;$i<$_REQUEST[max_per];$i++)

   {

     if($perfiles[$i] != Null)

     {

       //query de insercion

       $query = "INSERT INTO ".BASE_DATOS.".tab_perfil_servic

                 VALUES ('$perfiles[$i]','$nuevo_consec') ";

       $consulta = new Consulta($query, $this -> conexion);



       $bandera1 = 0;

       $hijo = $nuevo_consec;

       $cont = 0;

       while($bandera1 < 1)

       {

                 $query = "SELECT a.cod_serpad,b.nom_servic

                      FROM ".CENTRAL.".tab_servic_servic a,

                           ".CENTRAL.".tab_genera_servic b

                     WHERE a.cod_serpad = b.cod_servic AND

                           a.cod_serhij = '".$hijo."' ";

          $consulta = new Consulta($query, $this -> conexion);

          $matriz1 = $consulta -> ret_matriz();

          if(sizeof($matriz1) == 0)

             $bandera1 = 1;

          else

          {

             $query = "SELECT cod_servic

                         FROM ".BASE_DATOS.".tab_perfil_servic

                        WHERE cod_servic = '".$matriz1[0][0]."' AND

                              cod_perfil = '".$perfiles[$i]."' ";

             $consulta = new Consulta($query, $this -> conexion);

             $matriz2 = $consulta -> ret_matriz();

             if(sizeof($matriz2) == 0)

             {

               //query de insercion

               $query = "INSERT INTO ".BASE_DATOS.".tab_perfil_servic

                         VALUES ('".$perfiles[$i]."','".$matriz1[0][0]."') ";

               $consulta = new Consulta($query, $this -> conexion);

             }//fin if

             $hijo = $matriz1[0][0];

          }//fin if

        }//fin while

     }//fin if

   }//fin for



   //asignacion de servicios a los usuarios elegidos

   for($i=0;$i<$_REQUEST[max_usu];$i++)

   {

     if($usuarios[$i] != Null)

     {

       //query de insercion

       $query = "INSERT INTO ".BASE_DATOS.".tab_servic_usuari

                 VALUES ('$nuevo_consec','$usuarios[$i]') ";

       $consulta = new Consulta($query, $this -> conexion);



       $bandera1 = 0;

       $hijo = $nuevo_consec;

       $cont = 0;

       while($bandera1 < 1)

       {

                 $query = "SELECT a.cod_serpad,b.nom_servic

                      FROM ".CENTRAL.".tab_servic_servic a,

                           ".CENTRAL.".tab_genera_servic b

                     WHERE a.cod_serpad = b.cod_servic AND

                           a.cod_serhij = '".$hijo."' ";

          $consulta = new Consulta($query, $this -> conexion);

          $matriz1 = $consulta -> ret_matriz();

          if(sizeof($matriz1) == 0)

             $bandera1 = 1;

          else

          {

             $query = "SELECT cod_servic

                         FROM ".BASE_DATOS.".tab_servic_usuari

                        WHERE cod_servic = '".$matriz1[0][0]."' AND

                              cod_usuari = '".$usuarios[$i]."' ";

             $consulta = new Consulta($query, $this -> conexion);

             $matriz2 = $consulta -> ret_matriz();

             if(sizeof($matriz2) == 0)

             {

               //query de insercion

               $query = "INSERT INTO ".BASE_DATOS.".tab_servic_usuari

                         VALUES ('".$matriz1[0][0]."','".$usuarios[$i]."') ";

               $consulta = new Consulta($query, $this -> conexion);

             }//fin if

             $hijo = $matriz1[0][0];

          }//fin if

        }//fin while

     }//fin if

   }//fin for



   if(isset($consulta))

     echo "<br><br><b>TRANSACCION EXITOSA <br> EL SERVICIO $_REQUEST[nombre] FUE ACTUALIZADO</b>";

 }//FIN FUNCTION ACTUALIZAR





}//FIN CLASE PROC_SERVIC

     $proceso = new Proc_servic($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);

?>