<?php



/****************************************************************************



NOMBRE:   INS_SERVIC.INC



FUNCION:  INSERTAR SERVICIOS



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



     $this -> Formulario1();



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



// *****************************************************



// FUNCION QUE PRESENTA EL FORMULARIO DE CAPTURA



// *****************************************************



 function Formulario1()



 {



            $usuario = $this -> usuario -> cod_usuari;



            //trae los servicios que no tienen ruta de archivo



            //estos pueden ser el padre del nuevo



      $query = "SELECT cod_servic, nom_servic

                  FROM ".CENTRAL.".tab_genera_servic

                 WHERE rut_archiv = ''

                    OR rut_archiv IS NULL

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



      $inicio[0][0] = 0;



      $inicio[0][1] = '-';



      $matriz = array_merge($inicio,$matriz);



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







      $formulario = new Formulario("index.php","post","<b>INSERTAR SERVICIOS</b>", "form_ins");


      $formulario -> nueva_tabla();


      $formulario -> texto("Codigo:","text","codigo", 1, 4,  4,"","");



      $formulario -> texto("Nombre:","text","nombre", 1,50,150,"","");



      $formulario -> texto("Descripcion:","text","descri", 1,50,255,"","");



      $formulario -> texto("Ruta Archivo:","text","ruta", 1,50,150,"","");



      $formulario -> texto("Ruta Jscript:","text","jscript", 1,50,150,"","");



      $formulario -> texto("Body Jscript:","text","bjscrip", 1,50,150,"","");



      $formulario -> lista("Servicio Padre:", "padre", $matriz, 1);



            $formulario -> nueva_tabla();


            $formulario -> linea("PERFILES",0, 0, "t");



            $formulario -> nueva_tabla();



            for($i=0;$i<sizeof($matriz1);$i++)



            {



              if($i%2 == 1)



           $formulario -> caja("".$matriz1[$i][1]."","perfiles[$i]", "".$matriz1[$i][0]."", 0,1);



        else



           $formulario -> caja("".$matriz1[$i][1]."","perfiles[$i]", "".$matriz1[$i][0]."", 0,0);



      }//fin for



            $formulario -> nueva_tabla();



            $formulario -> linea("USUARIOS",0, 0, "t");



            $formulario -> nueva_tabla();



            for($i=0;$i<sizeof($matriz2);$i++)



            {



              if($i%2 == 1)



           $formulario -> caja("".$matriz2[$i][1]."","usuarios[$i]", "".$matriz2[$i][0]."", 0,1);



        else



           $formulario -> caja("".$matriz2[$i][1]."","usuarios[$i]", "".$matriz2[$i][0]."", 0,0);



      }//fin for



      $formulario -> nueva_tabla();



      $formulario -> oculto("usuario","$usuario",0);



      $formulario -> oculto("opcion",0, 0);



      $formulario -> oculto("max_per","".sizeof($matriz1)."", 0);



      $formulario -> oculto("max_usu","".sizeof($matriz2)."", 0);



      $formulario -> oculto("cod_servic", $GLOBALS[cod_servic], 0);



      $formulario -> oculto("window","central", 0);



      $formulario -> boton("Insertar", "button\" onClick=\"aceptar_ins1()", 0);



      $formulario -> cerrar();



 }//FIN FUNCTION CAPTURA



// *****************************************************



//FUNCION INSERTAR



// *****************************************************



 function Insertar()



 {



   $fec_actual = date("Y-m-d H:i:s");



   //reasignacion de variables



   $perfiles = $GLOBALS[perfiles];



   $usuarios = $GLOBALS[usuarios];



   //mira si ya existe

   $query = "SELECT cod_servic

               FROM ".CENTRAL.".tab_genera_servic

              WHERE cod_servic = '$GLOBALS[codigo]' ";

   $consulta = new Consulta($query, $this -> conexion);

   if($matriz = $consulta -> ret_arreglo())

   {

       $query = "SELECT Max(cod_servic) AS maximo

                   FROM ".CENTRAL.".tab_genera_servic ";

       $consec = new Consulta($query, $this -> conexion);

       $ultimo = $consec -> ret_matriz();

       $ultimo_consec = $ultimo[0][0];

       $nuevo_consec = $ultimo_consec+1;

    }//fin if

    else

       $nuevo_consec = $GLOBALS[codigo];



   //query de insercion del servicio

   if(!$GLOBALS[descri])

       $GLOBALS[descri] = "NULL";

   else

       $GLOBALS[descri] = "'$GLOBALS[descri]'";



   if(!$GLOBALS[ruta])

       $GLOBALS[ruta] = "NULL";

   else

       $GLOBALS[ruta] = "'$GLOBALS[ruta]'";



   if(!$GLOBALS[jscript])

       $GLOBALS[jscript] = "NULL";

   else

       $GLOBALS[jscript] = "'$GLOBALS[jscript]'";



   if(!$GLOBALS[bjscrip])

       $GLOBALS[bjscrip] = "NULL";

   else

       $GLOBALS[bjscrip] = "'$GLOBALS[bjscrip]'";



   $query = "INSERT INTO ".CENTRAL.".tab_genera_servic

             VALUES ('$nuevo_consec','$GLOBALS[nombre]',$GLOBALS[descri],

                      $GLOBALS[ruta],$GLOBALS[jscript],$GLOBALS[bjscrip],

                      '".COD_APLICACION."','$GLOBALS[usuario]','$fec_actual',NULL,NULL)";

   $consulta = new Consulta($query, $this -> conexion);



   //query de insercion de la relacion con el servicio padre



   if($GLOBALS[padre])



   {



      $query = "INSERT INTO ".CENTRAL.".tab_servic_servic



                VALUES ('$GLOBALS[padre]','$nuevo_consec') ";



      $consulta = new Consulta($query, $this -> conexion);



    }//fin if



   //asignacion de servicios a los perfiles elegidos



   for($i=0;$i<$GLOBALS[max_per];$i++)



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



   for($i=0;$i<$GLOBALS[max_usu];$i++)



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



     echo "<br><br><b>TRANSACCION EXITOSA <br> EL SERVICIO $GLOBALS[nombre] FUE INSERTADO</b>";



 }//FIN FUNCTION INSERTAR







}//FIN CLASE PROC_SERVIC



     $proceso = new Proc_servic($this -> conexion, $this -> usuario_aplicacion,$this-> codigo);



?>
