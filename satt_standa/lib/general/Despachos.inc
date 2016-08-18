<?php
/****************************************************************************
NOMBRE:   Clase de despacho
FUNCION:  PROVEE LAS FUNCIONES NECESARIAS PARA EL MANEJO DE LA INFORMACION
          DE UN DESPACHO
FECHA DE MODIFICACION: 16 DE DICIEMBRE DE 2005
CREADO POR: LEONARDO ROMERO CASTRO
MODIFICADO POR: LEONARDO ROMERO CASTRO

****************************************************************************/


class Despachos {

      var $conexion,
          $despacho,
          $agencia,
          $origen,
          $destino,
          $transpor,
          $ult_reporte,
          $ruta;

      function __construct($con, $des)
      {
              $this ->   conexion = $con;
              $this ->   despacho = $des;

       $query="SELECT  MAX(e.fec_noveda)
               FROM ".BASE_DATOS.".tab_despac_vehige c,
                    ".BASE_DATOS.".tab_despac_seguim d,
                    ".BASE_DATOS.".tab_despac_noveda e
               WHERE c.num_despac = d.num_despac AND
                    c.num_despac = e.num_despac AND
                    c.num_despac = '".$this -> despacho."' ";
   $consulta = new Consulta($query, $this -> conexion);
   //fecha del ultimo reporte
   $ultrep = $consulta -> ret_arreglo();

   if(sizeof($ultrep))
   {
   $this -> ult_reporte = $ultrep[0];
   }
   else
   {
   $this -> ult_reporte = 0;
   }

           //inicializa los datos del despacho
          $query = "SELECT g.nom_agenci,c.cod_ciudad,d.cod_ciudad,e.cod_rutasx
                    FROM ".BASE_DATOS.".tab_despac_despac a,".BASE_DATOS.".tab_despac_vehige b,
                         ".BASE_DATOS.".tab_genera_ciudad c,".BASE_DATOS.".tab_genera_ciudad d,
                         ".BASE_DATOS.".tab_genera_rutasx e,".BASE_DATOS.".tab_genera_agenci g
                   WHERE a.num_despac = b.num_despac AND
                         a.cod_ciuori = c.cod_ciudad AND
                         a.cod_ciudes = d.cod_ciudad AND
                         b.cod_rutasx = e.cod_rutasx AND
                         b.cod_agenci = g.cod_agenci AND
                         a.num_despac = '".$this -> despacho."'
                ORDER BY 2";
          $consulta = new Consulta($query, $this -> conexion);
          $datos = $consulta -> ret_arreglo();
          $this ->  agencia = $datos[0];
          $this ->  origen  = $datos[1];
          $this -> destino  = $datos[2];
          $this -> ruta     = $datos[3];

           //datos del encabezado
          $query = "SELECT a.cod_manifi,f.cod_tercer,f.abr_tercer ,
                           h.cod_tercer,h.abr_tercer, d.nom_empres,
                           d.cod_emptra,i.cod_tercer, i.abr_tercer,
                           i.num_telmov
                          FROM ".BASE_DATOS.".tab_despac_despac a,".BASE_DATOS.".tab_genera_ciudad b,
                               ".BASE_DATOS.".tab_genera_ciudad c,".BASE_DATOS.".tab_emptra_config d,
                               ".BASE_DATOS.".tab_tercer_client e,".BASE_DATOS.".tab_tercer_tercer f,
                               ".BASE_DATOS.".tab_despac_vehige g,".BASE_DATOS.".tab_tercer_tercer h,
                               ".BASE_DATOS.".tab_tercer_tercer i,".BASE_DATOS.".tab_tercer_conduc j
                         WHERE g.cod_transp = d.cod_emptra AND
                               a.cod_client = e.cod_tercer AND
                               e.cod_tercer = f.cod_tercer AND
                               a.cod_asegra = d.cod_asegra AND
                               a.cod_asegra = h.cod_tercer AND
                               g.cod_conduc = i.cod_tercer AND
                               g.cod_conduc = j.cod_tercer AND
                               a.num_despac = g.num_despac AND
                               a.num_despac = '".$this -> despacho."' AND
                               a.cod_ciuori = b.cod_ciudad AND
                               a.cod_ciudes = c.cod_ciudad
                               ORDER BY 1,2";

          $consulta = new Consulta($query, $this -> conexion);
          $datos1   = $consulta -> ret_arreglo();

          $this -> transpor = $datos1[6];


      }

      function encabezado1()
       {
           //datos del encabezado
          $query = "SELECT a.num_despac,a.cod_manifi,f.abr_tercer, h.abr_tercer, d.nom_empres,
                               i.abr_tercer, i.cod_tercer, i.num_telmov
                          FROM ".BASE_DATOS.".tab_despac_despac a,".BASE_DATOS.".tab_genera_ciudad b,
                               ".BASE_DATOS.".tab_genera_ciudad c,".BASE_DATOS.".tab_emptra_config d,
                               ".BASE_DATOS.".tab_tercer_client e,".BASE_DATOS.".tab_tercer_tercer f,
                               ".BASE_DATOS.".tab_despac_vehige g,".BASE_DATOS.".tab_tercer_tercer h,
                               ".BASE_DATOS.".tab_tercer_tercer i,".BASE_DATOS.".tab_tercer_conduc j
                         WHERE g.cod_transp = d.cod_emptra AND
                               a.cod_client = e.cod_tercer AND
                               e.cod_tercer = f.cod_tercer AND
                               a.cod_asegra = d.cod_asegra AND
                               a.cod_asegra = h.cod_tercer AND
                               g.cod_conduc = i.cod_tercer AND
                               g.cod_conduc = j.cod_tercer AND
                               a.num_despac = g.num_despac AND
                               a.num_despac = '$GLOBALS[despac]' AND
                               a.cod_ciuori = b.cod_ciudad AND
                               a.cod_ciudes = c.cod_ciudad
                               ORDER BY 1,2";

          $consulta = new Consulta($query, $this -> conexion);
          $matriz1[1] = $consulta -> ret_arreglo();
          $matriz1[0] = array("<b>Despacho</b>","<b>Manifiesto</b>","<b>Cliente</b>","<b>Aseguradora</b>",
                             "<b>Transportadora</b>","<b>Conductor</b>","<b>C.C.</b>","<b>Celular</b>");
          //inicio de los filtros asignados al usuario o perfil actual
            if($datos_usuario["cod_perfil"] == "")
            {
              //PARA EL FILTRO DEL PUESTO DE CONTROL  SIN PERFIL
              $filtro= new Aplica_Filtro_Usuari($this -> cod_aplica, COD_FILTRO_CONTRO, $datos_usuario["cod_usuari"]);
              if($filtro -> listar($this -> conexion))
              {
                 $matriz1[1][2]='';
                 $matriz1[1][3]='';
              }
            }//fin if

            else
            {
              //PARA EL FILTRO DEL PUESTO DE CONTROL CON PERFIL
              $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONTRO,$datos_usuario["cod_perfil"]);
              if($filtro -> listar($this -> conexion))
              {
                 $matriz1[1][2]='';
                 $matriz1[1][3]='';
              }
            }//fin else

            //final de los filtros asignados al usuario o perfil actual
            //datos del encabezado
          $query = "SELECT b.num_placax,d.nom_marcax,e.nom_lineax,f.nom_colorx,g.nom_config,
                           h.nom_carroc,c.ano_modelo
                    FROM ".BASE_DATOS.".tab_despac_vehige b,".BASE_DATOS.".tab_vehicu_vehicu c,
                         ".BASE_DATOS.".tab_genera_marcas d,".BASE_DATOS.".tab_vehige_lineas e,
                         ".BASE_DATOS.".tab_vehige_colore f,".BASE_DATOS.".tab_vehige_config g,
                         ".BASE_DATOS.".tab_vehige_carroc h
                   WHERE b.num_placax = c.num_placax AND
                         c.cod_marcax = d.cod_marcax AND
                         c.cod_marcax = e.cod_marcax AND
                         c.cod_lineax = e.cod_lineax AND
                         c.cod_colorx = f.cod_colorx AND
                         c.num_config = g.num_config AND
                         c.cod_carroc = h.cod_carroc AND
                         b.num_despac = '$GLOBALS[despac]'
                 ORDER BY 2";

          $consulta = new Consulta($query, $this -> conexion);
          $matriz2[1] = $consulta -> ret_arreglo();
          $matriz2[0] = array("<b>Placa</b>","<b>Marca</b>","<b>Linea</b>","<b>Color</b>",
                             "<b>Configuracion</b>","<b>Carroceria</b>","<b>Modelo</b>");

           //datos del encabezado
          $query = "SELECT g.nom_agenci,c.abr_ciudad,d.abr_ciudad,e.nom_rutasx,
                           if(a.fec_salida Is Null,'SIN CONFIRMAR',DATE_FORMAT(a.fec_salida,'%H:%i %d-%m-%Y')),
                           if(a.fec_llegad Is Null,'SIN CONFIRMAR',DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y'))
                    FROM ".BASE_DATOS.".tab_despac_despac a,".BASE_DATOS.".tab_despac_vehige b,
                         ".BASE_DATOS.".tab_genera_ciudad c,".BASE_DATOS.".tab_genera_ciudad d,
                         ".BASE_DATOS.".tab_genera_rutasx e,".BASE_DATOS.".tab_genera_agenci g
                   WHERE a.num_despac = b.num_despac AND
                         a.cod_ciuori = c.cod_ciudad AND
                         a.cod_ciudes = d.cod_ciudad AND
                         b.cod_rutasx = e.cod_rutasx AND
                         b.cod_agenci = g.cod_agenci AND
                         a.num_despac = '$GLOBALS[despac]'
                ORDER BY 2";

          $consulta = new Consulta($query, $this -> conexion);
          $matriz3[1] = $consulta -> ret_arreglo();

          //si hay dos contenedores los presenta en
          if($matriz3[1][7] != Null)
             $matriz3[1][3]=$matriz3[1][3]." - ".$matriz3[1][7];

          $matriz3[0] = array("<b>Agencia</b>","<b>Origen</b>","<b>Destino</b>","<b>Ruta</b>",
                              "<b>Fecha Salida</b>","<b>Fecha Llegada</b>");


          //imprime el formulario con la informacion del despacho

          $formulario = new Formulario ("index.php","post","INFORMACIó DEL DESPACHO","form_item");
          echo "<table bgcolor=\"#F7F7F7\" width=\"100%\"><tr><td valign=\"top\">";
          $tabla = new Tabla_arreglo($matriz1);
          echo "</td><td valign=\"top\">";
          $tabla = new Tabla_arreglo($matriz3);
          echo "</td><td valign=\"top\">";
          $tabla = new Tabla_arreglo($matriz2);
          echo "</td></tr></table>";
          $formulario -> cerrar();
        }


function encabezado(){

   $query = "SELECT a.num_despac,a.cod_manifi,f.abr_tercer,

                       i.abr_tercer, i.cod_tercer, i.num_telmov,i.num_telef1

                  FROM ".BASE_DATOS.".tab_despac_despac a,".BASE_DATOS.".tab_tercer_tercer f,

                       ".BASE_DATOS.".tab_despac_vehige g,".BASE_DATOS.".tab_tercer_tercer h,

                       ".BASE_DATOS.".tab_tercer_tercer i

                 WHERE a.num_despac = g.num_despac AND

                       g.cod_transp = f.cod_tercer AND

                       g.cod_conduc = i.cod_tercer AND

                       a.num_despac = '$GLOBALS[despac]'

                       ORDER BY 1,2";



  $consulta = new Consulta($query, $this -> conexion);

  $matriz1[1] = $consulta -> ret_arreglo();

  $matriz1[0] = array("<b>Despacho</b>","<b>Manifiesto</b>",

                     "<b>Transportadora</b>","<b>Conductor</b>","<b>C.C.</b>","<b>Celular</b>","<b>Telefono</b>");

  //datos del encabezado
    //final de los filtros asignados al usuario o perfil actual
    //datos del encabezado
  $query = "SELECT b.num_placax,d.nom_marcax,e.nom_lineax,f.nom_colorx,g.nom_config,
                   h.nom_carroc,c.ano_modelo
            FROM ".BASE_DATOS.".tab_despac_vehige b,".BASE_DATOS.".tab_vehicu_vehicu c,
                 ".BASE_DATOS.".tab_genera_marcas d,".BASE_DATOS.".tab_vehige_lineas e,
                 ".BASE_DATOS.".tab_vehige_colore f,".BASE_DATOS.".tab_vehige_config g,
                 ".BASE_DATOS.".tab_vehige_carroc h
           WHERE b.num_placax = c.num_placax AND
                 c.cod_marcax = d.cod_marcax AND
                 c.cod_marcax = e.cod_marcax AND
                 c.cod_lineax = e.cod_lineax AND
                 c.cod_colorx = f.cod_colorx AND
                 c.num_config = g.num_config AND
                 c.cod_carroc = h.cod_carroc AND
                 b.num_despac = '$GLOBALS[despac]'
         ORDER BY 2";

  $consulta = new Consulta($query, $this -> conexion);
  $matriz2[1] = $consulta -> ret_arreglo();
  $matriz2[0] = array("<b>Placa</b>","<b>Marca</b>","<b>Linea</b>","<b>Color</b>",
                     "<b>Configuracion</b>","<b>Carroceria</b>","<b>Modelo</b>");

   //datos del encabezado
  $query = "SELECT g.nom_agenci,c.abr_ciudad,d.abr_ciudad,e.nom_rutasx,
                   if(a.fec_salida Is Null,'SIN CONFIRMAR',DATE_FORMAT(a.fec_salida,'%H:%i %d-%m-%Y')),
                   if(a.fec_llegad Is Null,'SIN CONFIRMAR',DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y'))
            FROM ".BASE_DATOS.".tab_despac_despac a,".BASE_DATOS.".tab_despac_vehige b,
                 ".BASE_DATOS.".tab_genera_ciudad c,".BASE_DATOS.".tab_genera_ciudad d,
                 ".BASE_DATOS.".tab_genera_rutasx e,".BASE_DATOS.".tab_genera_agenci g
           WHERE a.num_despac = b.num_despac AND
                 a.cod_ciuori = c.cod_ciudad AND
                 a.cod_ciudes = d.cod_ciudad AND
                 b.cod_rutasx = e.cod_rutasx AND
                 b.cod_agenci = g.cod_agenci AND
                 a.num_despac = '$GLOBALS[despac]'
        ORDER BY 2";

  $consulta = new Consulta($query, $this -> conexion);
  $matriz3[1] = $consulta -> ret_arreglo();

  //si hay dos contenedores los presenta en
  if($matriz3[1][7] != Null)
     $matriz3[1][3]=$matriz3[1][3]." - ".$matriz3[1][7];

  $matriz3[0] = array("<b>Agencia</b>","<b>Origen</b>","<b>Destino</b>","<b>Ruta</b>",
                      "<b>Fecha Salida</b>","<b>Fecha Llegada</b>");




  $formulario = new Formulario ("index.php","post","INFORMACIó DEL DESPACHO","form_item");

  echo "<table bgcolor=\"#F7F7F7\" width=\"100%\"><tr><td valign=\"top\">";

  $tabla = new Tabla_arreglo($matriz1);

  echo "</td><td valign=\"top\">";

  $tabla = new Tabla_arreglo($matriz3);

  echo "</td><td valign=\"top\">";

  $tabla = new Tabla_arreglo($matriz2);

  echo "</td></tr></table>";
  $query = "SELECT a.cod_remesa,a.val_pesoxx,a.val_volume,b.nom_empaqu,c.abr_mercan,
                   d.abr_tercer,a.nom_remite,a.nom_destin

                  FROM ".BASE_DATOS.".tab_despac_remesa a,".BASE_DATOS.".tab_genera_empaqu b,
                       ".BASE_DATOS.".tab_genera_mercan c,".BASE_DATOS.".tab_tercer_tercer d

                 WHERE  a.cod_empaqu = b.cod_empaqu AND
                        a.cod_mercan = c.cod_mercan AND
                        a.cod_client = d.cod_tercer AND
                        a.num_despac = '$GLOBALS[despac]'
                       ORDER BY 1";



  $consulta = new Consulta($query, $this -> conexion);
  $remesas = $consulta -> ret_matriz();

  $etiquetas[0] = array("<b>Remesa</b>","<b>Peso Tn</b>","<b>Volumen</b>","<b>Empaque</b>","<b>Mercancia</b>","<b>Cliente</b>","<b>Remitente</b>","<b>Destinatario</b>");

  $tabla = new Tabla_matriz(array_merge($etiquetas, $remesas));



  $formulario -> cerrar();

 }//fin funcion



         function actualizar_tiempos($pc)
          {
            $fec_actual = date("Y-m-d H:i:s");
         //actualiza la hora de generacion de la alarma con base a la fecha ingresada por el usuario
         $query="UPDATE ".BASE_DATOS.".tab_despac_seguim
                 SET fec_alarma = '$fec_actual'
                 WHERE num_despac = '".$this -> despacho."' AND
                       cod_contro = '".$pc."' ";

         $consulta = new Consulta($query, $this -> conexion, "R");

         //trae el puesto de control de la novedad
         $query = "SELECT a.cod_contro,c.val_duraci
                     FROM   ".BASE_DATOS.".tab_despac_seguim a,
                            ".BASE_DATOS.".tab_despac_vehige b,
                            ".BASE_DATOS.".tab_genera_rutcon c
                    WHERE a.num_despac = b.num_despac AND
                          b.cod_rutasx = c.cod_rutasx AND
                          a.cod_contro = c.cod_contro AND
                          a.num_despac = '".$this -> despacho."' AND
                          a.cod_contro = '".$pc."' ";
        $consulta = new Consulta($query, $this -> conexion);
        $actual = $consulta -> ret_matriz();


        //trae los puestos de pernoctacion del despacho
        $query = "SELECT cod_contro,val_pernoc
                     FROM ".BASE_DATOS.".tab_despac_pernoc
                    WHERE num_despac = '".$this -> despacho."'";
         $consulta = new Consulta($query, $this -> conexion);
         $pernoc = $consulta -> ret_matriz();

         //trae los puestos de control que no han sido reportados
         $query = "SELECT a.cod_contro,c.val_duraci
                   FROM ".BASE_DATOS.".tab_despac_seguim a,
                        ".BASE_DATOS.".tab_despac_vehige b,
                        ".BASE_DATOS.".tab_genera_rutcon c LEFT JOIN
                        ".BASE_DATOS.".tab_despac_noveda AS d
                            ON a.num_despac = d.num_despac AND
                               a.cod_contro = d.cod_contro
                    WHERE a.num_despac = b.num_despac AND
                          b.cod_rutasx = c.cod_rutasx AND
                          a.cod_contro = c.cod_contro AND
                          a.num_despac = '".$this -> despacho."' AND
                          d.num_despac Is Null AND
                          d.cod_contro Is Null
                     ORDER BY 2";

          $consulta = new Consulta($query, $this -> conexion);
          $contro = $consulta -> ret_matriz();

          //trae el tiempo al puesto de control
          $tiempo=$actual[0][1];

          if($GLOBALS[tiem_duraci])
             for($i=0; $i < sizeof($contro); $i++)
             {
                   $contro[$i][1] = $contro[$i][1]+$GLOBALS[tiem_duraci];
             }


          //los tiempos y puestos de control ha actualizar
          for($i=0;$i<sizeof($contro);$i++)
          {
           for($j=0;$j<sizeof($pernoc);$j++)
           {
            if($pernoc[$j][0] == $contro[$i][0])
            {
             $pernocta[$i]=$pernoc[$j][0];
             $tiempos[$i]=$pernoc[$j][1];
             $j=sizeof($pernoc)+1;
            }//fin if
            else
            {
             $pernocta[$i]=0;
             $tiempos[$i]=0;
            }//fin else
           }//fin for $j
          }//fin for $i

          //Recalcular los tiempos
          for($i=0;$i<sizeof($contro);$i++)
          {
           $contro[$i][1]=$contro[$i][1]-$tiempo;
           if($pernocta[$i] == $contro[$i][0])
           {
            $contro[$i][1]=$contro[$i][1]+$carry[$i];
            $carry[$i+1]  =$carry[$i]+$tiempos[$i];
           }//fin if
           else
           {
            $contro[$i][1]=$contro[$i][1]+$carry[$i];
            $carry[$i+1]  =$carry[$i];
           }//fin else
          }//fin for

         //actualiza la hora del puesto de control donde se reporta la novedad
         $query = "UPDATE  ".BASE_DATOS.".tab_despac_seguim
                       SET  fec_alarma = '$fec_actual',
                            usr_modifi = 'lromero',
                            fec_modifi = '$fec_actual'
                      WHERE num_despac = '".$this -> despacho."' AND
                            cod_contro = '".$pc."'";

          $update = new Consulta($query, $this -> conexion, "R");

          //ACTUALIZA TODO EL PLAN DE RUTA

          for($i=0; $i < sizeof($contro); $i++)
          {

              $query = "UPDATE  ".BASE_DATOS.".tab_despac_seguim
                        SET  fec_alarma = DATE_ADD('$fec_actual', INTERVAL ".$contro[$i][1]." MINUTE),
                             usr_modifi = '$GLOBALS[usuario]',
                             fec_modifi = '$fec_actual'
                       WHERE num_despac = '".$this -> despacho."' AND
                             cod_contro = '".$contro[$i][0]."'";
           $insercion = new Consulta($query, $this -> conexion, "R");

          }//fin for $i

          //trae el ultimo registro de la matriz s/n su tamaó

          $ult = sizeof($contro)-1;

          //trae el ultimo campo del resultado

          //garantiza que hay valor



          if(sizeof($contro) > 0)

             $ultimo = $contro[$ult][1];

          else

             $ultimo = 0;



          //actualiza la hora y fecha de llegada planeada s/n el ultimo puesto de control
          $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                    SET fec_llegpl = DATE_ADD('$fec_actual', INTERVAL ".$ultimo." MINUTE)
                    WHERE num_despac = '".$this -> despacho."' ";

          $insercion = new Consulta($query, $this -> conexion, "RC");
          return true;
       }//fin funcion



       function cambiarRuta($pc ,$rutNueva, $tiempos_noveda, $pc_noveda, $pcontrol, $nov_pernoct, $tiempo_pernoct){

        $fec_actual = date("Y-m-d H:i:s");
        $cod_noveda = '17';

          //trae los puestos de control de la nueva ruta
       $query="SELECT  cod_contro,val_duraci
                FROM ".BASE_DATOS.".tab_genera_rutcon
                WHERE cod_rutasx = '".$rutNueva."'
                ORDER BY val_duraci";
        $consulta = new Consulta($query, $this -> conexion, "BR");
        $pcon = $consulta -> ret_matriz();

        //Elimina los puestos de control donde no se han reportado
       $query=" DELETE FROM ".BASE_DATOS.".tab_despac_seguim
                WHERE num_despac = '".$this -> despacho."' AND
                      cod_rutasx = '".$this -> ruta."' AND
                      fec_alarma  > '".$this -> ult_reporte."'";
        $eliminar = new Consulta($query, $this -> conexion, "R");

        //inserta la novedad de cambio de ruta en el ultimo puesto de control
        if($this -> ult_reporte != 0)
        $this -> ins_noveda($pc_noveda,"17","0");

        $saltar = 1;
        for($i = 0; $i < sizeof($pcon); $i++)
        {
               if($pcon[$i][0] == $pc)
               $saltar = 0;
                   if(!$saltar)
                   {
                       if($tiempo_pernoct[$i] != Null)
                       {

                        $pcon[$i][1];
                        $pcon[$i-1][1];
                        $tiempo1[$i] = $pcon[$i][1] - $pcon[$i-1][1];
                        $tiempo1[0] = $tiempo + $tiempo_pernoct[$i];

                        $this -> ins_pernoc($pcon[$i][0], $nov_pernoct[$i], $tiempo_pernoct[$i]);
                       }
                       else
                       {
                        $tiempo1[$i] = $pcon[$i][1] - $pcon[$i-1][1];
                        $tiempo1[0]  = $tiempo;
                       }
                   }
         }

         //algoritmo de insercion de nuevos tiempos
        for($i=0; $i < sizeof($pcon); $i++)
        {
               if($pcon[$i][0] == $pc)
               $saltar = 0;
                   if(!$saltar)
                   {
                     $query="SELECT DATE_ADD('".$fec_actual."', INTERVAL ".$tiempo1[$i]." MINUTE)";
                     $consulta = new Consulta($query, $this -> conexion, "R");
                     $tmp = $consulta -> ret_arreglo();

                     $fec_actual = $tmp[0];

                     $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
                               VALUES ('".$this -> despacho ."','".$pcon[$i][0]."','".$rutNueva."',
                                       '".$fec_actual."','".$fec_actual."','$GLOBALS[usuario]','$fec_actual','$GLOBALS[usuario]','$fec_actual')";
                    $insercion = new Consulta($query, $this -> conexion,"R");
                    }
        }//fin for

          $query="SELECT DATE_ADD('".$fec_actual."', INTERVAL ".$tiempo1[$i-1]." MINUTE)";
          $consulta = new Consulta ($query,  $this -> conexion);
          $fec_llegpla = $consulta -> ret_arreglo();

   //actualiza la hora y fecha de llegada
   $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
               SET fec_llegpl = '".$fec_llegpla[0]."',
                   cod_rutasx = '".$rutNueva."',
                   usr_modifi = '$GLOBALS[usuario]',
                   fec_modifi = NOW()
             WHERE num_despac = '".$this -> despacho."' ";
      $update = new Consulta($query, $this -> conexion,"R");

   $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
             SET ind_camrut = '".$rutNueva."'
             WHERE num_despac = '".$this -> despacho."' ";
   $consulta = new Consulta($query , $this -> conexion, "R");

   if(!mysql_error())
      return true;
   else
      return false;

  }//fin funcion

  function ins_noveda($pc_noveda, $noveda, $tiempo)
  {
   $fec_actual = date("Y-m-d H:i:s");

    //verificar el tipo de novedad
    $query = "SELECT cod_noveda, num_despac
              FROM ".BASE_DATOS.".tab_despac_noveda
              WHERE cod_noveda = '".$noveda."' AND
                    num_despac = '".$this -> despacho."' AND
                    cod_contro = '".$pc_noveda."'";
  $consulta = new Consulta($query, $this -> conexion, "R");

  $existe = $consulta -> ret_matriz();

  if($fec_actual <= $this -> ult_report )
  {
     echo "Fecha actual menor a ultimo reporte";
     return false;
  }
  if(sizeof($existe) > 0)
  {
   echo "ya existe la novedad";
   return false;
  }
  else
  {

     $query = "INSERT INTO ".BASE_DATOS.".tab_despac_noveda
               (num_despac,cod_contro,cod_noveda,fec_noveda,des_noveda,tiem_duraci,usr_creaci,
                fec_creaci,usr_modifi,fec_modifi)
                VALUES ('".$this -> despacho."', '".$pc_noveda."',
                        '".$noveda."','".$fec_actual."','".$observ."','".$tiempo."',
                        '$GLOBALS[usuario]', '$fec_actual',
                        '$GLOBALS[usuario]', '$fec_actual')";

  $insercion = new Consulta($query, $this -> conexion, "R");
    return true;
  }
  }//fin funcion

  function getPlanRuta()
  {

 $query = "SELECT cod_contro, cod_rutasx FROM ".BASE_DATOS.".tab_despac_seguim
            WHERE num_despac = '".$this -> despacho."'
            GROUP BY  cod_contro
            ORDER BY fec_alarma";

  $consulta = new Consulta($query, $this -> conexion);
  $rutas    = $consulta -> ret_matriz();

   $formulario = new Formulario ("index.php","post","","f");
   $formulario -> linea("Puesto de control",0);
   $formulario -> linea("Hora/Fecha Programada",0);
   $formulario -> linea("Hora/Fecha control",0);
   $formulario -> linea("Novedad",0);
   $formulario -> linea("Hora/Fecha novedad",0);
   $formulario -> linea("Observaciones",1);



  for($i=0; $i < sizeof($rutas); $i++)
  {
  //datos del plan de ruta
  $query = "SELECT c.nom_contro,DATE_FORMAT(a.fec_planea,'%H:%i %d-%m-%Y'),
                  DATE_FORMAT(a.fec_alarma,'%H:%i %d-%m-%Y'),
                   d.nom_noveda,DATE_FORMAT(b.fec_modifi,'%H:%i %d-%m-%Y'),
                   b.des_noveda,a.fec_planea
            FROM ".BASE_DATOS.".tab_despac_seguim a,
                 ".BASE_DATOS.".tab_genera_contro c
                 LEFT JOIN ".BASE_DATOS.".tab_despac_noveda b ON
                 a.num_despac = b.num_despac AND
                 a.cod_contro = b.cod_contro LEFT JOIN
                 ".BASE_DATOS.".tab_genera_noveda d ON
                 b.cod_noveda = d.cod_noveda
           WHERE a.cod_contro = c.cod_contro AND
                 a.cod_contro = '".$rutas[$i][0]."' AND
                 a.cod_rutasx = '".$rutas[$i][1]."' AND
                 a.num_despac = '".$this -> despacho."' ";
  $query = $query." ORDER BY 7 ";
  $consulta = new Consulta($query, $this -> conexion);
  $matriz = $consulta -> ret_matriz();


   $query="SELECT  a.cod_contro,b.nom_contro
              FROM ".BASE_DATOS.".tab_genera_rutcon a,".BASE_DATOS.".tab_genera_contro b,
                   ".BASE_DATOS.".tab_despac_vehige c,".BASE_DATOS.".tab_despac_seguim d
             WHERE a.cod_contro = b.cod_contro AND
                   a.cod_rutasx = c.cod_rutasx AND
                   c.num_despac = d.num_despac AND
                   a.cod_contro = d.cod_contro AND
                   d.fec_alarma >= '".$this -> ult_reporte."' AND
                   c.num_despac = '".$this -> despacho."' ";
   $query = $query." ORDER BY a.val_duraci ";
   $consulta = new Consulta($query, $this -> conexion);
   $matrizlink = $consulta -> ret_matriz();

   for($j=0;$j<sizeof($matriz);$j++)
   {
     if($j%2 == 0)
     {
              for($k=0 ; $k<sizeof($matrizlink) ; $k++)
        {
            if($matriz[$j][0] == $matrizlink[$k][1])
               $matriz[$j][0]= "<a href=\"index.php?cod_servic=".$GLOBALS[cod_servic]."&window=central&despac=".$this -> despacho."&opcion=2&pc=".$matriz[$j][0]."&codpc=".$matrizlink[$k][0]." \"target=\"centralFrame\">".$matriz[$j][0]."</a>";
            if($matriz[$j][0] != $matrizlink[$j][1])
                    if($matriz[$j][3] == NULL)
                       $matriz[$j][3] = "Sin Ejecutar";
                    if($matriz[$j][4] == NULL)
                     {
                       $matriz[$j][4] = "00:00:00 00-00-0000";

                      if($matriz[$j][5] == NULL )
                      $matriz[$j][5] = "No Reportado";

                      }

        }



      echo "<td class=\"celda2\">".$matriz[$j][0]."</td>";
      echo "<td class=\"celda2\">".$matriz[$j][1]."</td>";
      echo "<td class=\"celda2\">".$matriz[$j][2]."</td>";
      echo "<td class=\"celda2\">".$matriz[$j][3]."</td>";
      echo "<td class=\"celda2\">".$matriz[$j][4]."</td>";
      echo "<td class=\"celda2\">".$matriz[$j][5]."</td></tr><tr>";
     }//fin if
     else
     {
               for($k=0 ; $k<sizeof($matrizlink) ; $k++)
        {
            if($matriz[$j][0] == $matrizlink[$k][1])
               $matriz[$j][0]= "<a href=\"index.php?cod_servic=".$GLOBALS[cod_servic]."&window=central&despac=".$this -> despacho."&opcion=2&pc=".$matriz[$j][0]."&codpc=".$matrizlink[$k][0]." \"target=\"centralFrame\">".$matriz[$j][0]."</a>";
            if($matriz[$j][0] != $matrizlink[$j][1])
                    if($matriz[$j][3] == NULL)
                     $matriz[$j][3] = "Sin Ejecutar";
                    if($matriz[$j][4] == NULL)
                     {
                      $matriz[$j][4] = "00:00:00 00-00-0000";
                      if($matriz[$j][5] == NULL )
                      $matriz[$j][5] = "No Reportado";
                       }
      }
      echo "<td class=\"celda\">".$matriz[$j][0]."</td>";
      echo "<td class=\"celda\">".$matriz[$j][1]."</td>";
      echo "<td class=\"celda\">".$matriz[$j][2]."</td>";
      echo "<td class=\"celda\">".$matriz[$j][3]."</td>";
      echo "<td class=\"celda\">".$matriz[$j][4]."</td>";
      echo "<td class=\"celda\">".$matriz[$j][5]."</td></tr><tr>";
     }//fin else
   }//fin for

   }//fin for

   $formulario -> cerrar();
}//fin funcion


   //muestra el plan de ruta modificado
   //parametros
   // $rutNueva
  function setPlanRuta($formulario, $rutNueva="0", $tiempo="0", $pc = "0")
  {

           if(!$rutNueva)
               echo $rutNueva = $this -> ruta;

       $fec_actual = date("Y-m-d H:i:s");
       //trae los puestos de control de la nueva ruta
       $query="SELECT  cod_contro,val_duraci
                FROM ".BASE_DATOS.".tab_genera_rutcon
                WHERE cod_rutasx = '".$rutNueva."'
                ORDER BY val_duraci";
        $consulta = new Consulta($query, $this -> conexion, "BR");
        $pcon = $consulta -> ret_matriz();

  $query = "SELECT b.cod_contro, b.nom_contro,a.val_duraci
             FROM ".BASE_DATOS.".tab_genera_rutcon a,
                  ".BASE_DATOS.".tab_genera_contro b,
                  ".BASE_DATOS.".tab_genera_rutasx  c
            WHERE a.cod_contro = b.cod_contro AND
                  a.cod_rutasx = c.cod_rutasx AND
                  a.cod_rutasx = '".$rutNueva."' ";
  $query = $query." ORDER BY 3 ";

  $consulta = new Consulta($query, $this -> conexion, "R");
  $pcon = $consulta -> ret_matriz();

        $saltar = 1;
        for($i = 0; $i < sizeof($pcon); $i++)
        {
               if($pcon[$i][0] == $pc)
               $saltar = 0;
                   if(!$saltar)
                   {
                         $tiempo1[$i] = $pcon[$i][2] - $pcon[$i-1][2];
                         $tiempo1[0] = $tiempo;
                   }
         }



         $saltar = 1;
         //algoritmo de insercion de nuevos tiempos
        for($i=0; $i < sizeof($pcon); $i++)
        {
               if($pcon[$i][0] == $pc)
               $saltar = 0;
                   if(!$saltar)
                   {

                             $query="SELECT DATE_ADD('".$fec_actual."', INTERVAL ".$tiempo1[$i]." MINUTE)";
                             $consulta = new Consulta($query, $this -> conexion, "R");
                             $tmp = $consulta -> ret_arreglo();

                             $tiempo_plan[$i] = $tmp[0];
                   }


        }//fin for

                        $formulario -> nueva_tabla();
                        $formulario -> linea("",0);
                        $formulario -> linea("",0);
                        $formulario -> linea("Puesto de Control",0);
                        $formulario -> linea("",0);
                        $formulario -> linea("Novedad",0);
                        $formulario -> linea("",0);
                        $formulario -> linea("Tiempo Estimado",0);
                        $formulario -> linea("Fecha y Hora Planeada",1);

            $saltar = 1;
            for($i=0;$i<sizeof($pcon);$i++)
            {
               if($pcon[$i][0] == $pc)
               $saltar = 0;
                   if(!$saltar)
                   {
                          $formulario -> caja ("","pcontrol[$i]",$pcon[$i][0],1,0);
                          echo "<td class=\"celda\">".$pcon[$i][1]." </td>";
                          $formulario -> lista("", "novedad[$i]", $this -> GetNovedades(),0);
                          $formulario -> texto ("","text","tiempo[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '0'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''} ",0,3,5,"","");
                          echo "<td class=\"celda2\">". $tiempo_plan[$i]." </td>";
                          echo "</tr><tr>";
                   }
            }


  }//fin funcion


  function GetNovedades($ind_tiempo = '1',$ind_alarma = 'N')
  {
   $inicio[0][0] = 0;
   $inicio[0][1] = '-';

     //novedades
     $query="SELECT  cod_noveda,nom_noveda
             FROM ".BASE_DATOS.".tab_genera_noveda
             WHERE ind_alarma = '".$ind_alarma."' AND
                   ind_tiempo = '".$ind_tiempo."'
             ORDER BY 2";
       $consulta = new Consulta($query, $this -> conexion);
       $novedad = $consulta -> ret_matriz();

       $novedad=array_merge($inicio,$novedad);
     return $novedad;
  }//fin funcion


  function ins_pernoc($pcontro, $cod_noveda, $duraci )
  {
   $fec_actual = date("Y-m-d H:i:s");
           echo "<hr>";
     ECHO $query2 = "INSERT INTO ".BASE_DATOS.".tab_despac_pernoc
               VALUES ('".$this -> despacho."', '".$pcontro."','".$cod_noveda."',
                       '".$duraci."','".$GLOBALS[usuario]."','$fec_actual',
                       '".$GLOBALS[usuario]."','$fec_actual')";
           echo "<hr>";
    $insercion2 = new Consulta($query2, $this -> conexion,"R");

  }

}//fin clase


?>