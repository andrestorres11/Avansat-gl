<?php
/****************************************************************************

NOMBRE:   ins_carav_noveda
FUNCION:  Insertar Novedades para los despachos relacionados en una caravana
AUTOR:    LEONARDO ROMERO CASTRO
FECHA DE CREACION: 01 de Diciembre 2005
FECHA DE MODIFICACION: 05 de diciembre 2005
****************************************************************************/

class Proc_despac
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

    $this -> Listar();

  else

     {

      switch($_REQUEST[opcion])

       {

        case "1":

          $this -> Datos();

          break;

        case "2":

          $this -> Formulario();

          break;

        case "3":

          $this -> Insertar();

          break;

        default:

          $this -> Listar();

          break;

       }//FIN SWITCH

     }// FIN ELSE GLOBALS OPCION

 }//FIN FUNCION PRINCIPAL

 function Listar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $fec_actual = date("Y-m-d H:i:s");
   $usuario=$datos_usuario["cod_usuari"];

     $titori[0][0]=0;
     $titori[0][1]='Origen';
     $titdes[0][0]=0;
     $titdes[0][1]='Destino';
     $todas[0][0]=0;
     $todas[0][1]='Todas';
     $titcarava[0][0]=0;
     $titcarava[0][1]='Caravana';

     //traemos las caravanas existentes en ruta
     $query = "SELECT a.num_carava, a.num_carava
               FROM ".BASE_DATOS.".tab_despac_despac a
               WHERE a.num_carava != 0 AND
                     a.fec_salida IS NOT NULL AND
                     a.fec_llegad IS NULL AND
                     a.ind_anulad = 'R'";

     if ($_REQUEST[num_carava])
     $query .= " AND a.num_carava = '$_REQUEST[num_carava]' ";

     $query .= " GROUP BY 1 ORDER BY 1";
     $consulta = new Consulta($query, $this -> conexion);

     $carava = $consulta -> ret_matriz();

     if($_REQUEST[num_carava])

        $carava=array_merge($carava,$titcarava);

     //presenta todas las caravanas
     else
        $carava=array_merge($titcarava,$carava);


         //traemos la cuidad de origen
     $query = "SELECT b.cod_ciudad, b.nom_ciudad
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_genera_ciudad b
              WHERE a.cod_ciuori = b.cod_ciudad AND
                    a.fec_salida Is NOT Null AND
                    a.num_carava != 0 AND
                    a.fec_llegad Is Null AND
                    a.ind_anulad = 'R'";

     if($_REQUEST[origen])
           $query .= " AND b.cod_ciudad = '$_REQUEST[origen]'";

     if($_REQUEST[destino])
           $query .= " AND a.cod_ciudes = '$_REQUEST[destino]'";

       $query .= " GROUP BY 1,2 ORDER BY 2";

     $consulta = new Consulta($query, $this -> conexion);

     $origen = $consulta -> ret_matriz();

     //Prepara la presentacion del cuadro de lista
     //solo presenta la ciudad escogida y da la
     //posibilidad de cambiarla
     if($_REQUEST[origen])

        $origen=array_merge($origen,$todas);

     //presenta todas las ciudades con su respetivo titulo
     else
        $origen=array_merge($titori,$origen);

     $query = "SELECT b.cod_ciudad, b.nom_ciudad
               FROM ".BASE_DATOS.".tab_despac_despac a,
                   ".BASE_DATOS.".tab_genera_ciudad b
               WHERE a.cod_ciudes = b.cod_ciudad AND
                     a.num_carava != 0 AND
                     a.fec_salida Is NOT Null AND
                     a.fec_llegad Is Null AND
                     a.ind_anulad = 'R'";

     if($_REQUEST[destino])
        $query .= " AND b.cod_ciudad = '$_REQUEST[destino]'";

     if($_REQUEST[origen])
        $query .= " AND a.cod_ciuori = '$_REQUEST[origen]'";

     $query .= " GROUP BY 1,2 ORDER BY 2";

     $consulta = new Consulta($query, $this -> conexion);
     $destino = $consulta -> ret_matriz();

     //Prepara la presentacion del cuadro de lista
     //solo presenta la ciudad escogida y da la
     //posibilidad de cambiarla
     if($_REQUEST[destino])
        $destino=array_merge($destino,$todas);
     //presenta todas las ciudades con su respetivo titulo
     else
        $destino=array_merge($titdes,$destino);


  $query = "SELECT a.num_carava,COUNT(a.num_despac),f.abr_ciudad,g.abr_ciudad,a.num_despac
           FROM  ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_genera_ciudad f,
                 ".BASE_DATOS.".tab_genera_ciudad g
           WHERE a.cod_ciuori = f.cod_ciudad AND
                 a.cod_ciudes = g.cod_ciudad AND
                 a.fec_salida Is Not Null AND
                 a.fec_llegad Is Null AND
                 a.num_carava != 0
                 AND a.ind_anulad = 'R'";

     if ($_REQUEST[num_carava])
     $query .= " AND a.num_carava = '$_REQUEST[num_carava]' ";

     if($_REQUEST[destino])
        $query .= " AND g.cod_ciudad = '$_REQUEST[destino]'";

     if($_REQUEST[origen])
        $query .= " AND a.cod_ciuori = '$_REQUEST[origen]'";

     $query .= " GROUP BY 1 ORDER BY 1,4";


  $consulta = new Consulta($query, $this -> conexion);
  $matriz = $consulta -> ret_matriz();

  $query = "SELECT nom_alarma, cant_tiempo, cod_colorx
            FROM ".BASE_DATOS.".tab_genera_alarma
            ORDER BY 2 ";
  $consulta = new Consulta($query, $this -> conexion);
  $alarmas = $consulta -> ret_matriz();

   $formulario = new Formulario ("index.php","post","Despachos en ruta","form_item");

   $formulario -> linea("<b>Se encontraron ".sizeof($matriz)." registros</b>",0);

   $formulario -> nueva_tabla();

   for($i=0; $i < sizeof($alarmas); $i++ ){

   echo "<td style ='font-size: 8pt;color: #000000;background-color: ".$alarmas[$i][2]."'>".$alarmas[$i][0]." = ".$alarmas[$i][1]." Min</td>";

   }

   $formulario -> nueva_tabla();

   $formulario -> lista_titulo("Caravana", "num_carava\" onChange=\"form_item.submit()\"",$carava, 0);

   $formulario -> linea("Despachos", 0);

   $formulario -> lista_titulo("Origen", "origen\" onChange=\"form_item.submit()\"",$origen, 0);

   $formulario -> lista_titulo("Destino", "destino\" onChange=\"form_item.submit()\"",$destino, 1);


        for($i=0;$i<sizeof($matriz);$i++)
        {

             //trae la ultima fecha de la ultima novedad
             $query = "SELECT  MAX(e.fec_noveda)
                  FROM ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_despac_vehige c,
                       ".BASE_DATOS.".tab_despac_seguim d,
                       ".BASE_DATOS.".tab_despac_noveda e
                  WHERE c.num_despac = d.num_despac AND
                        c.num_despac = e.num_despac AND
                        a.num_despac = e.num_despac AND
                        a.num_despac = d.num_despac AND
                        a.num_carava = '".$matriz[$i][0]."' AND
                        c.num_despac = '".$matriz[$i][4]."' ";

            $consulta = new Consulta($query, $this -> conexion);
             $maximo = $consulta -> ret_matriz();

            if(sizeof($maximo)> 0){

            $query="SELECT  e.cod_contro
                    FROM ".BASE_DATOS.".tab_despac_vehige c,".BASE_DATOS.".tab_despac_seguim d,
                         ".BASE_DATOS.".tab_despac_noveda e
                    WHERE c.num_despac = d.num_despac AND
                          c.num_despac = e.num_despac AND
                          e.fec_noveda = '".$maximo[0][0]."' AND
                          c.num_despac = '".$matriz[$i][4]."'
                    GROUP BY 1";
            $consulta = new Consulta($query, $this -> conexion);
            $contro = $consulta -> ret_matriz();

            }

            $query = "SELECT c.fec_alarma
                      FROM tab_despac_despac a,tab_despac_seguim c LEFT JOIN
                           tab_despac_noveda AS b ON
                           c.cod_contro = b.cod_contro AND
                           c.num_despac = b.num_despac
                     WHERE a.num_despac = c.num_despac AND
                           a.num_despac = '".$matriz[$i][4]."' ";

            if(sizeof($contro) > 0){

            $query .=    " AND  b.cod_contro = '".$contro[0][0]."' ";

            }
             $query .= " ORDER BY 1 ";
             $consulta = new Consulta($query, $this -> conexion);
             $ajustada = $consulta -> ret_arreglo();

             //calcula el tiempo de retraso
             $query = "SELECT TIME_TO_SEC( TIMEDIFF(NOW(), '".$ajustada[0]."')) / 60";
             $tiempo = new Consulta($query, $this -> conexion);
             $tiemp_demora = $tiempo -> ret_arreglo();

             //trae el tiempo de alarma
             $query = "SELECT MAX(cant_tiempo)
                     FROM ".BASE_DATOS.".tab_genera_alarma
                     WHERE cant_tiempo < '".$tiemp_demora[0]."' ";
             $consulta = new Consulta($query, $this -> conexion);
             $tiemp_alarma = $consulta -> ret_arreglo();

             //trae el color de la alarma
              $query = "SELECT cod_colorx
                       FROM ".BASE_DATOS.".tab_genera_alarma
                       WHERE cant_tiempo = '".$tiemp_alarma[0]."'" ;
             $consulta = new Consulta ($query, $this -> conexion);
             $color_a  = $consulta -> ret_arreglo();

             $matriz[$i][0]= "<a href=\"index.php?cod_servic=".$_REQUEST[cod_servic]."&window=central&carava=".$matriz[$i][0]."&despac=".$matriz[$i][4]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
             if($i%2 == 0)
             {

               echo "<td style ='font-size: 8pt;color: #000000;background-color: ".$color_a[0]."'>".$matriz[$i][0]." </td>";

               echo "<td class=\"celda\">".$matriz[$i][1]." </td>";

               echo "<td class=\"celda\">".$matriz[$i][2]." </td>";

               echo "<td class=\"celda\">".$matriz[$i][3]." </td>";

               echo "</tr><tr>";

            }

            else

            {

               echo "<td style ='font-size: 8pt;color: #000000;background-color: ".$color_a[0]."'>".$matriz[$i][0]." </td>";

               echo "<td class=\"celda2\">".$matriz[$i][1]." </td>";

               echo "<td class=\"celda2\">".$matriz[$i][2]." </td>";

               echo "<td class=\"celda2\">".$matriz[$i][3]." </td>";

               echo "</tr><tr>";
            }
        }





   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("valor",$valor,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }//FIN FUNCION LISTAR

 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
   //MUESTRA EL ENCABEZADO DE LA CARAVANA
   $this -> encabezado("Novedades En Caravana");


  //datos del plan de ruta
  $query = "SELECT c.nom_contro,DATE_FORMAT(a.fec_planea,'%H:%i %d-%m-%Y'),
                   DATE_FORMAT(a.fec_alarma,'%H:%i %d-%m-%Y'),
                   d.nom_noveda,DATE_FORMAT(b.fec_modifi,'%H:%i %d-%m-%Y'),
                         b.des_noveda,a.fec_planea, b.fec_noveda, b.cod_contro, b.cod_noveda
            FROM ".BASE_DATOS.".tab_despac_seguim a,
                 ".BASE_DATOS.".tab_despac_despac e,
                 ".BASE_DATOS.".tab_genera_contro c
                 LEFT JOIN ".BASE_DATOS.".tab_despac_noveda b ON
                 a.num_despac = b.num_despac AND
                 a.cod_contro = b.cod_contro LEFT JOIN
                 ".BASE_DATOS.".tab_genera_noveda d ON
                 b.cod_noveda = d.cod_noveda
           WHERE a.cod_contro = c.cod_contro AND
                 a.num_despac = e.num_despac AND
                 e.num_carava = '".$_REQUEST[carava]."' AND
                 a.num_despac = '".$_REQUEST[despac]."' ";

    //inicio de los filtros asignados al usuario o perfil actual
    if(!$datos_usuario["cod_perfil"])
    {
       //PARA EL FILTRO DEL PUESTO DE CONTROL  SIN PERFIL
       $filtro= new Aplica_Filtro_Usuari($this -> cod_aplica, COD_FILTRO_CONTRO, $datos_usuario["cod_usuari"]);
       if($filtro -> listar($this -> conexion))
       {
         $datos_filtro = $filtro -> retornar();
         $query = $query . " AND a.cod_contro = '$datos_filtro[clv_filtro]' ";
       }

    }//fin if

    else

    {

      //PARA EL FILTRO DEL PUESTO DE CONTROL CON PERFIL

      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONTRO,$datos_usuario["cod_perfil"]);



      if($filtro -> listar($this -> conexion))

      {

        $datos_filtro = $filtro -> retornar();

        $query = $query . " AND a.cod_contro = '$datos_filtro[clv_filtro]' ";

      }

    }//fin else



    //final de los filtros asignados al usuario o perfil actual

  $query .= " GROUP BY e.num_carava,c.cod_contro,b.cod_noveda ORDER BY 7,8";

  $consulta = new Consulta($query, $this -> conexion);

  $matriz = $consulta -> ret_matriz();

   //para validar los Links de los despachos pendientes
   //trae la ultima fecha de la novedad
   $query="SELECT  MAX(e.fec_noveda)
           FROM ".BASE_DATOS.".tab_despac_vehige c,".BASE_DATOS.".tab_despac_seguim d,
                ".BASE_DATOS.".tab_despac_noveda e
           WHERE c.num_despac = d.num_despac AND
                 c.num_despac = e.num_despac AND
                 c.num_despac = '$_REQUEST[despac]' ";

   //fecha maxima de la novedad

   $consulta = new Consulta($query, $this -> conexion);

   $maximo = $consulta -> ret_matriz();

   $query="SELECT  a.cod_contro,b.nom_contro

              FROM ".BASE_DATOS.".tab_genera_rutcon a,".BASE_DATOS.".tab_genera_contro b,

                   ".BASE_DATOS.".tab_despac_vehige c,".BASE_DATOS.".tab_despac_seguim d

             WHERE a.cod_contro = b.cod_contro AND

                   a.cod_rutasx = c.cod_rutasx AND

                   c.num_despac = d.num_despac AND

                   a.cod_contro = d.cod_contro AND

                   d.fec_alarma >= '".$maximo[0][0]."' AND

                   c.num_despac = '$_REQUEST[despac]' ";

    //inicio de los filtros asignados al usuario o perfil actual

    if(!$datos_usuario["cod_perfil"])

    {

      //PARA EL FILTRO DEL PUESTO DE CONTROL  SIN PERFIL

     $filtro= new Aplica_Filtro_Usuari($this -> cod_aplica, COD_FILTRO_CONTRO, $datos_usuario["cod_usuari"]);

      if($filtro -> listar($this -> conexion))

      {

        $datos_filtro = $filtro -> retornar();

        $query = $query . " AND a.cod_contro = '$datos_filtro[clv_filtro]' ";

      }

    }//fin if

    else

    {

      //PARA EL FILTRO DEL PUESTO DE CONTROL CON PERFIL

      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONTRO,$datos_usuario["cod_perfil"]);

      if($filtro -> listar($this -> conexion))

      {

        $datos_filtro = $filtro -> retornar();

        $query = $query . " AND a.cod_contro = '$datos_filtro[clv_filtro]' ";

      }

    }//fin else

    //final de los filtros asignados al usuario o perfil actual

  $query .= " ORDER BY a.val_duraci ";

   $consulta = new Consulta($query, $this -> conexion);
   $matrizlink = $consulta -> ret_matriz();

   //datos de las observaciones

  $query = "SELECT a.obs_despac,b.obs_medcom,b.obs_proesp,a.obs_llegad

            FROM ".BASE_DATOS.".tab_despac_despac a,".BASE_DATOS.".tab_despac_vehige b

           WHERE a.num_despac = b.num_despac AND

                 a.num_despac = '$_REQUEST[despac]' ";

  $consulta = new Consulta($query, $this -> conexion);

  $observ = $consulta -> ret_matriz();



  if($observ[0][0] == "")

     $observ[0][0] = 'NINGUNA';

  if($observ[0][1] == "")

     $observ[0][1] = 'NINGUNO';

  if($observ[0][2] == "")

     $observ[0][2] = 'NINGUNA';

   //datos de los seguimientos

  $query = "SELECT a.cod_consec,a.obs_contro,DATE_FORMAT(a.fec_contro,'%H:%i %d-%m-%Y'),

                    a.usr_creaci

             FROM ".BASE_DATOS.".tab_despac_contro a

             WHERE a.num_despac = '$_REQUEST[despac]' ";

  $consulta = new Consulta($query, $this -> conexion);

  $seguim = $consulta -> ret_matriz();

  //imprime el formulario con la informacion del despacho
  $formulario = new Formulario ("index.php","post","Informacion del Despacho","form_item");

   if(sizeof($matriz) > 0)
   {
   $formulario -> linea("Puesto de control",0);
   $formulario -> linea("Hora/Fecha Programada",0);
   $formulario -> linea("Hora/Fecha control",0);
   $formulario -> linea("Novedad",0);
   $formulario -> linea("Hora/Fecha novedad",0);
   $formulario -> linea("Observaciones",1);

   for($i=0;$i<sizeof($matriz);$i++)
   {
     if($i%2 == 0)
     {
              for($j=0 ; $j<sizeof($matrizlink) ; $j++)
        {
            if($matriz[$i][0] == $matrizlink[$j][1])
               $matriz[$i][0]= "<a href=\"index.php?cod_servic=".$_REQUEST[cod_servic]."&window=central&carava=".$_REQUEST[carava]."&opcion=2&pc=".$matriz[$i][0]."&codpc=".$matrizlink[$j][0]." \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
            if($matriz[$i][0] != $matrizlink[$j][1])
                    if($matriz[$i][3] == NULL)
                       $matriz[$i][3] = "Sin Ejecutar";
                    if($matriz[$i][4] == NULL)
                     {
                      $matriz[$i][4] = "00:00:00 00-00-0000";

                      if($matriz[$i][5] == NULL )

                      $matriz[$i][5] = "No Reportado";

                      }
        }

      echo "<td class=\"celda2\">".$matriz[$i][0]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][1]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][2]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][3]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][4]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][5]."</td></tr><tr>";
     }//fin if

     else

     {

               for($j=0 ; $j<sizeof($matrizlink) ; $j++)


        {

            if($matriz[$i][0] == $matrizlink[$j][1])

               $matriz[$i][0]= "<a href=\"index.php?cod_servic=".$_REQUEST[cod_servic]."&window=central&carava=".$_REQUEST[carava]."&opcion=2&pc=".$matriz[$i][0]."&codpc=".$matrizlink[$j][0]." \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

            if($matriz[$i][0] != $matrizlink[$j][1])

                    if($matriz[$i][3] == NULL)

                       $matriz[$i][3] = "Sin Ejecutar";

                    if($matriz[$i][4] == NULL)

                     {

                      $matriz[$i][4] = "00:00:00 00-00-0000";

                      if($matriz[$i][5] == NULL )

                      $matriz[$i][5] = "No Reportado";

                       }

        }

        if($matriz[$i][8] != NULL)
        {
         $query = "SELECT   a.cod_contro, a.cod_noveda, DATE_FORMAT(a.fec_noveda, '%H:%i %d-%m-%Y')
                              FROM tab_despac_noveda a
                              WHERE num_despac = '$_REQUEST[despac]' AND
                                    cod_contro = '".$matriz[$i][8]."' AND
                                    cod_noveda = '".$matriz[$i][9]."'
                       ORDER BY a.fec_noveda ";
        $consulta = new Consulta($query, $this -> conexion);
        $nov_contro = $consulta -> ret_matriz();

        $matriz[$i][2]= $nov_contro[0][2];
         }

      echo "<td class=\"celda\">".$matriz[$i][0]."</td>";

      echo "<td class=\"celda\">".$matriz[$i][1]."</td>";

      echo "<td class=\"celda\">".$matriz[$i][2]."</td>";

      echo "<td class=\"celda\">".$matriz[$i][3]."</td>";

      echo "<td class=\"celda\">".$matriz[$i][4]."</td>";

      echo "<td class=\"celda\">".$matriz[$i][5]."</td></tr><tr>";

     }//fin else

   }//fin for

   }//fin if

  $formulario -> nueva_tabla();



  $formulario -> nueva_tabla();

   if(sizeof($observ) > 0)

   {

     $formulario -> linea("Observaciones generales",0);

     $formulario -> linea("Medios de comunicación",0);

     $formulario -> linea("Protecciones especiales",1);

     echo "<td class=\"celda2\">".$observ[0][0]."</td>";

     echo "<td class=\"celda2\">".$observ[0][1]."</td>";

     echo "<td class=\"celda2\">".$observ[0][2]."</td></tr><tr>";



   //presenta las observaicones de llegada si ya esta finalizado el despacho

     if($estado == 3)

     {

       $formulario -> nueva_tabla();

       $formulario -> linea("OBSERVACIONES DE LLEGADA",0);

       echo "<td class=\"celda2\">".$observ[0][3]."</td>";

     }

   }//fin if

  $formulario -> nueva_tabla();

   if(sizeof($seguim) > 0)

   {

     $formulario -> linea("NÚMERO",0);

     $formulario -> linea("OBSERVACIÓN",0);

     $formulario -> linea("FECHA",0);

     $formulario -> linea("USUARIO",1);

   for($i=0;$i<sizeof($seguim);$i++)

   {

     if($i%2 == 0)

     {

      echo "<td class=\"celda2\">".$seguim[$i][0]."</td>";

      echo "<td class=\"celda2\">".$seguim[$i][1]."</td>";

      echo "<td class=\"celda2\">".$seguim[$i][2]."</td>";

      echo "<td class=\"celda2\">".$seguim[$i][3]."</td></tr><tr>";

     }//fin if

     else

     {

      echo "<td class=\"celda\">".$seguim[$i][0]."</td>";

      echo "<td class=\"celda\">".$seguim[$i][1]."</td>";

      echo "<td class=\"celda\">".$seguim[$i][2]."</td>";

      echo "<td class=\"celda\">".$seguim[$i][3]."</td></tr><tr>";

     }//fin else

    }//fin for

   }//fin if

   $formulario -> nueva_tabla();

   $formulario -> oculto("usuario","$usuario",0);

   $formulario -> oculto("opcion",3,0);

   $formulario -> oculto("window","central",0);

   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);

   $formulario -> cerrar();

 }//FIN FUNCION DATOS

//FUNCION FORMULARIO
 function Formulario()
 {

   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
   //trae la fecha actual
   $fec_actual = date("d-m-Y");
   $hor_actual = date("H:i:s");


   $query = "SELECT num_despac
             FROM ".BASE_DATOS.".tab_despac_despac
             WHERE num_carava = '".$_REQUEST[carava]."' ";

       $consulta = new Consulta($query , $this -> conexion );
       $despac = $consulta -> ret_matriz();

   if(!isset($_REQUEST[fecnov]))
      $_REQUEST[fecnov]=$fec_actual;
   if(!isset($_REQUEST[hornov]))
      $_REQUEST[hornov]=$hor_actual;


      //encabezado de la carvana
     $this -> encabezado("Caravana");

      $matriz[0][0] = $_REQUEST[codpc];
      $matriz[0][1] = $_REQUEST[pc];

   //lista las novedades
   $query = "SELECT cod_noveda,nom_noveda, ind_tiempo
               FROM ".BASE_DATOS.".tab_genera_noveda
               ORDER BY 2";
  $consulta = new Consulta($query, $this -> conexion);
  $novedades = $consulta -> ret_matriz();

   //lista las novedades
   $query = "SELECT cod_noveda,nom_noveda, ind_tiempo
               FROM ".BASE_DATOS.".tab_genera_noveda
               WHERE cod_noveda = '".$_REQUEST[novedad]."'
               ORDER BY 2";
  $consulta = new Consulta($query, $this -> conexion);
  $novedades_a = $consulta -> ret_matriz();

   //lista las novedades
   $query = "SELECT ind_tiempo
             FROM ".BASE_DATOS.".tab_genera_noveda
             WHERE cod_noveda = '".$_REQUEST[novedad]."'
             ORDER BY 1";

   $consulta = new Consulta($query, $this -> conexion);
   $ind_tiempo = $consulta -> ret_arreglo();

   $inicio[0][0] = 0;
   $inicio[0][1] = '-';

  if(empty($_REQUEST[novedad]))
   $novedades = array_merge($inicio,$novedades);
  else
   $novedades = array_merge($novedades_a,$inicio,$novedades);

   if(empty($_REQUEST[pc]))
           $matriz = array_merge($inicio,$matriz);

   $query="SELECT  MAX(e.fec_noveda)
           FROM ".BASE_DATOS.".tab_despac_vehige c,
                ".BASE_DATOS.".tab_despac_seguim d,
                ".BASE_DATOS.".tab_despac_noveda e
           WHERE c.num_despac = d.num_despac AND
                 c.num_despac = e.num_despac AND
                 c.num_despac = '$_REQUEST[despac]' ";
   $consulta = new Consulta($query, $this -> conexion);

   //fecha del ultimo reporte
   $ultrep = $consulta -> ret_matriz();

  echo "<script language=\"JavaScript\" src=\"../satb_standa/js/noveda.js\"></script>\n";
  $formulario = new Formulario ("index.php","post","","form_ins");

     $fecha1 = new Fecha();
     $formulario -> nueva_tabla();
     $formulario -> linea("FECHA Y HORA DE NOVEDAD",0);
     $formulario -> nueva_tabla();
     $fecha1 -> pedir_fecha("cano","cmes","cdia");
     $fecha1 -> pedir_hora("chora","cminuto");
     $formulario -> nueva_tabla();
     echo "<td class=\"etiqueta2\">PUESTO DE CONTROL </td>";
     echo "<td class=\"etiqueta\">".$_REQUEST[pc]." </td>";
  $formulario -> lista("NOVEDAD","novedad\" onChange=\"form_ins.submit()", $novedades,0);
  if($ind_tiempo[0]){
  $formulario -> texto("TIEMPO DURACION ","text","tiem_duraci\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '0'){alert('La Cantidad No es Valida');this.value='';this.focus()}}else{this.value=''}\" id=\"duracion",1,3,3,"","");
  }
  $formulario -> nueva_tabla();
  $formulario -> texto("OBSERVACIONES","textarea","obs",1,50,5,"","");
  $formulario -> nueva_tabla();
  $formulario -> linea ("FECHA DE LA NOVEDAD (dd-mm-yyyy)",0);
  $formulario -> linea ("".$_REQUEST[fecnov]."",1);
  $formulario -> linea ("HORA DE LA NOVEDAD (HH:mm)",0);
  $formulario -> linea ("<b>".$_REQUEST[hornov]."</b>",1);
  $formulario -> nueva_tabla();
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("tercero","$tercero",0);
  $formulario -> oculto("carava",$_REQUEST[carava],0);
  $formulario -> oculto("fecnov","$fec_actual",0);
  $formulario -> oculto("hornov","$hor_actual",0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
  $formulario -> oculto("opcion",2,0);
  $formulario -> oculto("pc",$_REQUEST[pc],0);
  $formulario -> oculto("codpc",$_REQUEST[codpc],0);
  $formulario -> oculto("ultrep",$ultrep[0][0],0);
  if($ind_tiempo[0])
  $formulario -> oculto("tiem",1,0);
  else
  $formulario -> oculto("tiem",0,0);
  $formulario -> oculto("despac","$_REQUEST[despac]",0);
  $formulario -> nueva_tabla();
  $formulario -> botoni("Aceptar","aceptar_inscarava()",0);
  $formulario -> botoni("Borrar","reset",1);
  $formulario -> cerrar();

 }//FIN FUNCION FORMULARIO

//FUNCION INSERTAR
 function Insertar()
 {
  $f_actual = date("d-m-Y");
  $fec_actual = date("Y-m-d H:i:s");
  $ultrep =   $_REQUEST[ultrep];
  $fecha=$_REQUEST[cano]."-".$_REQUEST[cmes]."-".$_REQUEST[cdia]." ".$_REQUEST[chora].":".$_REQUEST[cminuto].":00";
  $fech1=$fecha;

   //Listado de Despachos vinculados a la caravana
  $query = "SELECT a.num_despac
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_despac_vehige b,
                 ".BASE_DATOS.".tab_genera_ciudad f,
                 ".BASE_DATOS.".tab_genera_ciudad g
           WHERE a.num_despac = b.num_despac AND
                 a.cod_ciuori = f.cod_ciudad AND
                 a.cod_ciudes = g.cod_ciudad AND
                 a.num_carava = '".$_REQUEST[carava]."' AND
                 a.fec_salida Is Not Null AND
                 a.fec_llegad Is Null AND
                 b.ind_activo = 'S'
                 ORDER BY 1 ";
  $consulta = new Consulta($query, $this -> conexion);
  $despachos   = $consulta -> ret_matriz();


  for($j=0; $j < sizeof($despachos); $j++)
  {
    //verificar el tipo de novedad
    $query = "SELECT a.cod_noveda, a.num_despac
              FROM ".BASE_DATOS.".tab_despac_noveda a,
                   ".BASE_DATOS.".tab_despac_despac b
              WHERE a.cod_noveda = '$_REQUEST[novedad]' AND
                    a.num_despac = b.num_despac AND
                    num_carava = '$_REQUEST[carava]' AND
                    cod_contro = '$_REQUEST[codpc]' AND
                    b.num_despac = '".$despachos[$j][0]."' ";
    $consulta = new Consulta($query, $this -> conexion);
    $existe = $consulta -> ret_matriz();

    $exist[$j][0]  = $existe[0][0];
    $exist[$j][0]  = $existe[0][1];

    }


  if($fecha <= $ultrep){
     $mens_fec =  "La fecha de la novedad debe ser mayor a la fecha de la ultima novedad";
     echo "<img src=\"../satb_standa/imagenes/error.gif\">$mens_fec<hr>";
     $this -> Datos();
     exit;
  }

    if($fecha > $fec_actual){
     $mens_fec =  "La fecha de la novedad debe ser menor o igual a la fecha actual";
     echo "<img src=\"../satb_standa/imagenes/error.gif\">$mens_fec<hr>";
     $this -> Datos();
     exit;
  }

  if(sizeof($existe))
  {
     for($i=0; $i < sizeof($existe); $i++)
     {
          $mensaje = "La novedad ya fue insertada en el puesto de control $_REQUEST[pc] <br>
                      para el Despacho ".$exist[$i][0]." solamente puede insertar un tipo de novedad por puesto de control";
           echo "<img src=\"../satb_standa/imagenes/error.gif\">$mensaje<hr>";
     }
     $this -> Datos();
  }
  else
  {

   for($i=0; $i < sizeof($despachos); $i++)
   {
     //inserta los datos de la novedad con la fecha del sistema novedad
      $query = "INSERT INTO ".BASE_DATOS.".tab_despac_noveda
               (num_despac,cod_contro,cod_noveda,fec_noveda,des_noveda, tiem_duraci,usr_creaci,
                fec_creaci,usr_modifi,fec_modifi)
                VALUES ('".$despachos[$i][0]."', '$_REQUEST[codpc]',
                        '$_REQUEST[novedad]','$fech1','$_REQUEST[obs]',
                        '$_REQUEST[tiem_duraci]',
                        '$_REQUEST[usuario]', '$fec_actual',
                        '$_REQUEST[usuario]', '$fec_actual')";
     $insercion = new Consulta($query, $this -> conexion, "BR");

  //actualiza la hora de generacion de la alarma con base a la fecha ingresada por el usuario

   $query="UPDATE ".BASE_DATOS.".tab_despac_seguim

          SET fec_alarma = '$fech1'

          WHERE num_despac = '".$despachos[$i][0]."' AND

                cod_contro = '$_REQUEST[codpc]' ";


  $consulta = new Consulta($query, $this -> conexion, "R");



  //trae el puesto de control de la novedad

  $query = "SELECT a.cod_contro,c.val_duraci

              FROM ".BASE_DATOS.".tab_despac_seguim a,".BASE_DATOS.".tab_despac_vehige b,

                   ".BASE_DATOS.".tab_genera_rutcon c

             WHERE a.num_despac = b.num_despac AND

                   b.cod_rutasx = c.cod_rutasx AND

                   a.cod_contro = c.cod_contro AND

                   a.num_despac = '".$despachos[$i][0]."' AND

                   a.cod_contro = '$_REQUEST[codpc]' ";

 $consulta = new Consulta($query, $this -> conexion, "R");
 $actual = $consulta -> ret_matriz();



 //trae los puestos de pernoctacion del despacho
  $query = "SELECT cod_contro,val_pernoc
            FROM ".BASE_DATOS.".tab_despac_pernoc
            WHERE num_despac = '".$despachos[$i][0]."'";

  $consulta = new Consulta($query, $this -> conexion, "R");
  $pernoc = $consulta -> ret_matriz();



  //trae los puestos de control que no han sido reportados

  $query = "SELECT a.cod_contro,c.val_duraci,c.val_duraci

            FROM ".BASE_DATOS.".tab_despac_seguim a,

                 ".BASE_DATOS.".tab_despac_vehige b,

                 ".BASE_DATOS.".tab_genera_rutcon c LEFT JOIN

                 ".BASE_DATOS.".tab_despac_noveda AS d

                     ON a.num_despac = d.num_despac AND

                        a.cod_contro = d.cod_contro

             WHERE a.num_despac = b.num_despac AND

                   b.cod_rutasx = c.cod_rutasx AND

                   a.cod_contro = c.cod_contro AND

                   a.num_despac = '".$despachos[$i][0]."' AND

                   d.num_despac Is Null AND

                   d.cod_contro Is Null

              ORDER BY 2";



   $consulta = new Consulta($query, $this -> conexion,"R");

   $contro = $consulta -> ret_matriz();



   //trae el tiempo al puesto de control

   $tiempo=$actual[0][1];



   //los tiempos y puestos de control ha actualizar
   //los tiempos y puestos de control ha actualizar
   if($_REQUEST[tiem_duraci])
      for($i=0; $i < sizeof($contro); $i++)
      {
            $contro[$i][1] = $contro[$i][1]+$_REQUEST[tiem_duraci];
      }



   for($j=0;$j<sizeof($contro);$j++)

   {

    for($k=0;$k<sizeof($pernoc);$k++)

    {

     if($pernoc[$k][0] == $contro[$j][0])

     {

      $pernocta[$j]=$pernoc[$k][0];

      $tiempos[$j]=$pernoc[$k][1];

      $j=sizeof($pernoc)+1;

     }//fin if

     else

     {

      $pernocta[$j]=0;

      $tiempos[$j]=0;

     }//fin else

    }//fin for $k

   }//fin for $j



   //recalcular los tiempos



   for($j=0;$j<sizeof($contro);$j++)

   {

    $contro[$j][1]=$contro[$j][1]-$tiempo;

    if($pernocta[$j] == $contro[$j][0])

    {

     $contro[$j][1]=$contro[$j][1]+$carry[$j];

     $carry[$j+1]=$carry[$j]+$tiempos[$j];

    }//fin if

    else

    {

     $contro[$j][1]=$contro[$j][1]+$carry[$j];

     $carry[$j+1]=$carry[$j];

    }//fin else

   }//fin for



  //actualiza la hora del puesto de control donde se reporta la novedad



  $query = "UPDATE  ".BASE_DATOS.".tab_despac_seguim

                SET  fec_alarma = '$fech1',

                     usr_modifi = '$_REQUEST[usuario]',

                     fec_modifi = '$fec_actual'

               WHERE num_despac = '".$despachos[$i][0]."' AND

                     cod_contro = '$_REQUEST[codpc]'";



   $update = new Consulta($query, $this -> conexion, "R");



   //ACTUALIZA TODO EL PLAN DE RUTA



   for($j=0; $j < sizeof($contro); $j++)

   {
       if($contro[$i][2] > $actual[0][1])
       {

       $query = "UPDATE  ".BASE_DATOS.".tab_despac_seguim

                 SET  fec_alarma = DATE_ADD('$fech1', INTERVAL ".$contro[$j][1]." MINUTE),

                      usr_modifi = '$_REQUEST[usuario]',

                      fec_modifi = '$fec_actual'

                WHERE num_despac = '".$despachos[$i][0]."' AND

                      cod_contro = '".$contro[$j][0]."'";

    $insercion = new Consulta($query, $this -> conexion,"R");
      }
   }//fin for $i



   //trae el ultimo registro de la matriz s/n su tamaño



   $ult = sizeof($contro)-1;



   //trae el ultimo campo del resultado



   //garantiza que hay valor







   if(sizeof($contro) > 0)



      $ultimo = $contro[$ult][1];



   else



      $ultimo = 0;


         ///manejo de la interfaz
        $query = "SELECT cod_interf,nom_interf,nom_basedx,ind_interf
                     FROM ".BASE_DATOS.".tab_interf_parame
                     WHERE ind_interf = '1'";
         $consulta = new Consulta($query, $this -> conexion);
         $interf = $consulta -> ret_matriz();
         $activar = new Consulta($query, $this -> conexion, "R");

         for($i=0; $i<sizeof($interf); $i++)
         {
           //se crea el objeto para manejar terceros
           $interfaz = new Interfaz(BASE_DATOS,$this -> conexion, $this -> usuario_aplicacion);
           if($interf[$i][0])
           {
               //inserta la novedad
                //             ins_noveda($bd_interf,nit_transp,$despac_sad,$pc_noveda, $noveda, $tiempo,$observ= "",$ind_interf=1)
               if($interfaz -> ins_noveda_interf($interf[$i][2],NIT_TRANSPOR,$despachos[$i][0],$_REQUEST[codpc],$_REQUEST[novedad],$fech1,$_REQUEST[tiem_duraci],$_REQUEST[obs]))
                   echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">Novedad registrada en ".$interf[$i][1]." <br>";
              else
              {
                   echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">La novedad no se Registro en ".$interf[$i][1]."<br>";
                   exit;
              }
           }
         }





   //actualiza la hora y fecha de llegada planeada s/n el ultimo puesto de control



   $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige



             SET fec_llegpl = DATE_ADD('$fech1', INTERVAL ".$ultimo." MINUTE)



             WHERE num_despac = '".$despachos[$i][0]."' ";



   $insercion = new Consulta($query, $this -> conexion, "RC");



   }//fin for



      echo "<img src=\"../satb_standa/imagenes/ok.gif\"><b>transaccion exitosa<br>la novedad fue reportada en el puesto de control $_REQUEST[pc] <br>";



        $this -> Listar();

  }
}//FIN FUNCION LISTAR


function encabezado($tit= "Caravanas"){
   //Encabezado
  $query = "SELECT a.num_despac,a.cod_manifi,
                   c.abr_tercer,d.num_placax,
                   e.abr_tercer,e.num_telmov
            FROM ".BASE_DATOS.".tab_despac_despac a,".BASE_DATOS.".tab_tercer_tercer b,
                 ".BASE_DATOS.".tab_tercer_tercer c,".BASE_DATOS.".tab_despac_vehige d,
                 ".BASE_DATOS.".tab_tercer_tercer e,".BASE_DATOS.".tab_genera_ciudad f,
                 ".BASE_DATOS.".tab_genera_ciudad g
           WHERE a.num_despac = d.num_despac AND
                 d.cod_transp = c.cod_tercer AND
                 d.cod_conduc = e.cod_tercer AND
                 a.cod_ciuori = f.cod_ciudad AND
                 a.cod_ciudes = g.cod_ciudad AND
                 a.num_carava = '".$_REQUEST[carava]."' AND
                 a.fec_salida Is Not Null AND
                 a.fec_llegad Is Null
                 GROUP BY 1 ORDER BY 1 ";
  $consulta = new Consulta($query, $this -> conexion);
  $encabe   = $consulta -> ret_matriz();

  echo "<b>".$tit."</b>";

  $formulario = new Formulario ("index.php","post","","form");

  $formulario -> nueva_tabla();
  $formulario -> linea("DATOS DE LA CARAVANA NUMERO ".$_REQUEST[carava]." ",0);
  $formulario -> nueva_tabla();
   if(sizeof($encabe) > 0)
   {
   $formulario -> linea("Despacho",0);
   $formulario -> linea("Manifiesto",0);
   $formulario -> linea("Transportadora",0);
   $formulario -> linea("Placa",0);
   $formulario -> linea("Conductor",0);
   $formulario -> linea("Celular",1);

   for($i=0;$i<sizeof($encabe);$i++)

   {
       $despac[$i] = $encabe[$i][0];
     if($i%2 == 0)
     {
      echo "<td class=\"celda2\">".$encabe[$i][0]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][1]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][2]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][3]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][4]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][5]."</td></tr><tr>";
     }//fin if
     else
     {
      echo "<td class=\"celda\">".$encabe[$i][0]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][1]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][2]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][3]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][4]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][5]."</td></tr><tr>";
     }//fin else
   }//fin for
   }//fin if
   $formulario -> cerrar();
}






}//FIN CLASE PROC_DESPAC



   $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);



?>