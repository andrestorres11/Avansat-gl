<?php
/****************************************************************************
NOMBRE:   MODULO_DESPAC_INSNOV.PHP
FUNCION:  INSERTAR NOTAS DE CONTROLADOR VIRTUALES EN PUESTOS DE CONTROL
FECHA DE MODIFICACION: 6 DE DICIEMBRE
MODIFICADO POR: LEONARDO ROMERO CASTRO

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
  if(!isset($GLOBALS[opcion]))
    $this -> Listar();

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

     if ($GLOBALS[num_carava])
     $query .= " AND a.num_carava = '$GLOBALS[num_carava]' ";

     $query .= " GROUP BY 1 ORDER BY 1";
     $consulta = new Consulta($query, $this -> conexion);

     $carava = $consulta -> ret_matriz();

     if($GLOBALS[num_carava])

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

     if($GLOBALS[origen])
           $query .= " AND b.cod_ciudad = '$GLOBALS[origen]'";

     if($GLOBALS[destino])
           $query .= " AND a.cod_ciudes = '$GLOBALS[destino]'";

       $query .= " GROUP BY 1,2 ORDER BY 2";

     $consulta = new Consulta($query, $this -> conexion);

     $origen = $consulta -> ret_matriz();

     //Prepara la presentacion del cuadro de lista
     //solo presenta la ciudad escogida y da la
     //posibilidad de cambiarla
     if($GLOBALS[origen])

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

     if($GLOBALS[destino])
        $query .= " AND b.cod_ciudad = '$GLOBALS[destino]'";

     if($GLOBALS[origen])
        $query .= " AND a.cod_ciuori = '$GLOBALS[origen]'";

     $query .= " GROUP BY 1,2 ORDER BY 2";

     $consulta = new Consulta($query, $this -> conexion);
     $destino = $consulta -> ret_matriz();

     //Prepara la presentacion del cuadro de lista
     //solo presenta la ciudad escogida y da la
     //posibilidad de cambiarla
     if($GLOBALS[destino])
        $destino=array_merge($destino,$todas);
     //presenta todas las ciudades con su respetivo titulo
     else
        $destino=array_merge($titdes,$destino);


  $query = "SELECT a.num_carava,COUNT(a.num_despac),f.abr_ciudad,g.abr_ciudad,a.num_despac
           FROM  ".BASE_DATOS.".tab_despac_despac a,".BASE_DATOS.".tab_genera_ciudad f,
                 ".BASE_DATOS.".tab_genera_ciudad g
           WHERE a.cod_ciuori = f.cod_ciudad AND
                 a.cod_ciudes = g.cod_ciudad AND
                 a.fec_salida Is Not Null AND
                 a.fec_llegad Is Null AND
                 a.num_carava != 0 AND
                 a.ind_anulad = 'R'";

     if ($GLOBALS[num_carava])
     $query .= " AND a.num_carava = '$GLOBALS[num_carava]' ";

     if($GLOBALS[destino])
        $query .= " AND g.cod_ciudad = '$GLOBALS[destino]'";

     if($GLOBALS[origen])
        $query .= " AND a.cod_ciuori = '$GLOBALS[origen]'";

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

             $matriz[$i][0]= "<a href=\"index.php?cod_servic=".$GLOBALS[cod_servic]."&window=central&carava=".$matriz[$i][0]."&despac=".$matriz[$i][4]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";
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
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }//FIN FUNCION LISTAR

 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   //trae la fecha actual

   $fec_actual = date("d-m-Y");
   $hor_actual = date("H:i");

   //presenta por defecta la fecha actual
   if(!isset($GLOBALS[fecnov]))

      $GLOBALS[fecnov]=$fec_actual;

   if(!isset($GLOBALS[hornov]))

      $GLOBALS[hornov]=$hor_actual;

      //Encabezado del despacho
      $this -> encabezado();

   $inicio[0][0] = 0;
   $inicio[0][1] = '-';

   //lista las novedades
   $query = "SELECT cod_noveda,nom_noveda, ind_tiempo
               FROM ".BASE_DATOS.".tab_genera_noveda
               WHERE ind_tiempo = '1'
               ORDER BY 2";
   $consulta = new Consulta($query, $this -> conexion);
   $novedades = $consulta -> ret_matriz();

   $query = "SELECT cod_noveda,nom_noveda
               FROM ".BASE_DATOS.".tab_genera_noveda
               WHERE cod_noveda = '".$GLOBALS[novedad]."' AND
                     ind_tiempo = '1' ";
   $consulta = new Consulta($query, $this -> conexion);
   $novedades_a = $consulta -> ret_matriz();

   if($GLOBALS[novedad])
   $novedades = array_merge($novedades_a,$inicio,$novedades);
   else
   $novedades = array_merge($inicio,$novedades);

  //lista las novedades
  $query = "SELECT ind_tiempo
            FROM ".BASE_DATOS.".tab_genera_noveda
            WHERE cod_noveda = '".$GLOBALS[novedad]."'
           ORDER BY 1";

   $consulta = new Consulta($query, $this -> conexion);
   $ind_tiempo = $consulta -> ret_arreglo();


  $query = "SELECT c.cod_contro,c.nom_contro,b.des_noveda,a.fec_planea
            FROM ".BASE_DATOS.".tab_despac_seguim a,
                 ".BASE_DATOS.".tab_genera_contro c,
                 ".BASE_DATOS.".tab_despac_despac e
                 LEFT JOIN ".BASE_DATOS.".tab_despac_noveda b ON
                 a.num_despac = b.num_despac AND
                 a.cod_contro = b.cod_contro LEFT JOIN
                 ".BASE_DATOS.".tab_genera_noveda d ON
                 b.cod_noveda = d.cod_noveda
           WHERE a.cod_contro = c.cod_contro AND
                 a.num_despac = e.num_despac AND
                 e.num_carava = '$GLOBALS[carava]' AND
                 b.fec_noveda is null ";

    //final de los filtros asignados al usuario o perfil actual
  $query .= "GROUP BY e.num_carava,1 ORDER BY 3,4 ";
  $consulta = new Consulta($query, $this -> conexion);
  $puestos = $consulta -> ret_matriz();

  $query = "SELECT c.cod_contro,c.nom_contro,b.des_noveda,a.fec_planea
            FROM ".BASE_DATOS.".tab_despac_seguim a,
                 ".BASE_DATOS.".tab_genera_contro c
                 LEFT JOIN ".BASE_DATOS.".tab_despac_noveda b ON
                 a.num_despac = b.num_despac AND
                 a.cod_contro = b.cod_contro LEFT JOIN
                 ".BASE_DATOS.".tab_genera_noveda d ON
                 b.cod_noveda = d.cod_noveda
           WHERE a.cod_contro = c.cod_contro AND
                 c.cod_contro = '$GLOBALS[codpc]' AND
                 b.fec_noveda is null ";

  //final de los filtros asignados al usuario o perfil actual
  $query .= "GROUP BY 1 ORDER BY 3,4 ";
  $consulta = new Consulta($query, $this -> conexion);
  $puestos_a = $consulta -> ret_matriz();

  $query = "SELECT c.cod_contro,c.nom_contro,MAX(b.fec_noveda)
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_despac_noveda b,
                 ".BASE_DATOS.".tab_genera_contro c
           WHERE a.num_despac = b.num_despac AND
                 b.cod_contro = c.cod_contro AND
                 a.num_carava = '$GLOBALS[carava]' AND
                 b.fec_noveda is not null ";

    //final de los filtros asignados al usuario o perfil actual
  $query .= "GROUP BY 1 LIMIT 0,1 ";
  $consulta = new Consulta($query, $this -> conexion);
  $puesto_repor = $consulta -> ret_matriz();

  if($puesto_repor)
  $puestos = array_merge($puesto_repor,$puestos);

  if($GLOBALS[codpc])
    $puestos = array_merge($puestos_a,$inicio,$puestos);
  else
    $puestos = array_merge($inicio, $puestos);

  echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/noveda_contro.js\"></script>\n";

  $formulario = new Formulario ("index.php","post","","form_ins");
  $formulario -> lista("NOVEDAD","novedad\" onChange=\"form_ins.submit()\"", $novedades,0);
  if($ind_tiempo[0]){
  $formulario -> texto("TIEMPO DURACION ","text","tiem_duraci\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '0'){alert('La Cantidad No es Valida');this.value='';this.focus()}}else{this.value=''}\" id=\"duracion",1,3,3,"","");
  }
  $formulario -> nueva_tabla();
  $formulario -> lista("NOTA DESPUES DEL P/CONTROL ","codpc", $puestos,1);
  $formulario -> texto("OBSERVACIONES","textarea","obs",0,50,5,"","");
  $formulario -> nueva_tabla();
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("tercero","$tercero",0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
  $formulario -> oculto("opcion",1,0);
  if($ind_tiempo[0])
  $formulario -> oculto("tiem",1,0);
  else
  $formulario -> oculto("tiem",0,0);
  $formulario -> oculto("carava", $GLOBALS[carava],0);
  $formulario -> oculto("despac",$GLOBALS[despac],0);
  $formulario -> nueva_tabla();
  $formulario -> boton("Aceptar","button\" onClick=\"aceptar_ins()",0);
  $formulario -> boton("Borrar","reset",1);
  $formulario -> cerrar();

 }//fin funcion

 function Insertar()
 {
   $fec_actual = date("Y-m-d H:i:s");


   //Listado de Despachos vinculados a la caravana
  $query = "SELECT a.num_despac
            FROM ".BASE_DATOS.".tab_despac_despac a,".BASE_DATOS.".tab_tercer_tercer b,
                 ".BASE_DATOS.".tab_tercer_tercer c,".BASE_DATOS.".tab_despac_vehige d,
                 ".BASE_DATOS.".tab_tercer_tercer e,".BASE_DATOS.".tab_genera_ciudad f,
                 ".BASE_DATOS.".tab_genera_ciudad g
           WHERE a.num_despac = d.num_despac AND
                 a.cod_client = b.cod_tercer AND
                 d.cod_transp = c.cod_tercer AND
                 d.cod_conduc = e.cod_tercer AND
                 a.cod_ciuori = f.cod_ciudad AND
                 a.cod_ciudes = g.cod_ciudad AND
                 a.num_carava = '".$GLOBALS[carava]."' AND
                 a.fec_salida Is Not Null AND
                 a.fec_llegad Is Null
                 ORDER BY 1 ";
  $consulta = new Consulta($query, $this -> conexion);
  $despachos   = $consulta -> ret_matriz();

  for($i=0; $i < sizeof($despachos); $i++)
  {
   //trae el consecutivo de la tabla
   $query = "SELECT Max(cod_consec) AS maximo
               FROM ".BASE_DATOS.".tab_despac_contro
              WHERE num_despac = '".$despachos[$i][0]."'  ";

   $consec = new Consulta($query, $this -> conexion);
   $ultimo = $consec -> ret_matriz();
   $ultimo_consec = $ultimo[0][0];
   $nuevo_consec = $ultimo_consec+1;


  //inserta la novedad

  $query = "INSERT INTO ".BASE_DATOS.".tab_despac_contro
            VALUES ('".$despachos[$i][0]."', '$nuevo_consec','$GLOBALS[obs]', '$GLOBALS[novedad]','$GLOBALS[tiem_duraci]' ,'$fec_actual',
            '$GLOBALS[usuario]', '$fec_actual', '$GLOBALS[usuario]', '$fec_actual')";

  $insercion = new Consulta($query, $this -> conexion, "BR");

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
                //ins_noveda($bd_interf,nit_transp,$despac_sad,$pc_noveda, $noveda, $tiempo,$observ= "",$ind_interf=1)
               if($interfaz -> ins_notas($interf[$i][2],NIT_TRANSPOR,$despachos[$i][0],$GLOBALS[codpc],$GLOBALS[novedad],$fec_actual,$GLOBALS[tiem_duraci],$GLOBALS[obs]))
                   echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">Novedad registrada en ".$interf[$i][1]." <br>";
              else
              {
                   echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">La novedad no se Registro en ".$interf[$i][1]."<br>";
                   exit;
              }
           }

         }



  //actualiza los tiempos del despacho
  $this -> actualizar_tiempos($despachos[$i][0]);






  }//fin for despachos

    $mensaje = "<b>TRANSACCION EXITOSA<br>LA NOVEDAD FUE REPORTADA<b>";
    $mens = new mensajes();
    $mens -> correcto("Novedad Reportada", $mensaje);

 }//FIN FUNCION ACTUALIZAR


 function actualizar_tiempos($des)
 {
  $fec_actual = date("Y-m-d H:i:s");
  //actualiza la hora de generacion de la alarma con base a la fecha ingresada por el usuario

  //trae el puesto de control de la novedad
  $query = "SELECT a.cod_contro,c.val_duraci
              FROM ".BASE_DATOS.".tab_despac_seguim a,".BASE_DATOS.".tab_despac_vehige b,
                   ".BASE_DATOS.".tab_genera_rutcon c
             WHERE a.num_despac = b.num_despac AND
                   b.cod_rutasx = c.cod_rutasx AND
                   a.cod_contro = c.cod_contro AND
                   a.num_despac = '".$des."' AND
                   a.cod_contro = '$GLOBALS[codpc]' ";
 $consulta = new Consulta($query, $this -> conexion);
 $actual = $consulta -> ret_matriz();

 //trae los puestos de pernoctacion del despacho
 $query = "SELECT cod_contro,val_pernoc
              FROM ".BASE_DATOS.".tab_despac_pernoc
             WHERE num_despac = '".$des."'";
  $consulta = new Consulta($query, $this -> conexion);
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
                   a.num_despac = '".$des."' AND
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
                     usr_modifi = '$GLOBALS[usuario]',
                     fec_modifi = '$fec_actual'
               WHERE num_despac = '".$des."' AND
                     cod_contro = '$GLOBALS[codpc]'";

   $update = new Consulta($query, $this -> conexion, "R");

   //ACTUALIZA TODO EL PLAN DE RUTA

   for($i=0; $i < sizeof($contro); $i++)
   {
             if($contro[$i][2] > $actual[0][1])
             {

                    $query = "UPDATE  ".BASE_DATOS.".tab_despac_seguim
                              SET  fec_alarma = DATE_ADD('$fec_actual', INTERVAL ".$contro[$i][1]." MINUTE),
                                   usr_modifi = '$GLOBALS[usuario]',
                                   fec_modifi = '$fec_actual'
                             WHERE num_despac = '".$des."' AND
                                   cod_contro = '".$contro[$i][0]."'";
                 $insercion = new Consulta($query, $this -> conexion, "R");
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



   //actualiza la hora y fecha de llegada planeada s/n el ultimo puesto de control

   $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
             SET fec_llegpl = DATE_ADD('$fec_actual', INTERVAL ".$ultimo." MINUTE)
             WHERE num_despac = '".$des."' ";

   $insercion = new Consulta($query, $this -> conexion, "RC");

   return true;
 }


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
                 a.num_carava = '".$GLOBALS[carava]."' AND
                 a.fec_salida Is Not Null AND
                 a.fec_llegad Is Null
                 GROUP BY 1 ORDER BY 1 ";
  $consulta = new Consulta($query, $this -> conexion);
  $encabe   = $consulta -> ret_matriz();

  echo "<b>".$tit."</b>";

  $formulario = new Formulario ("index.php","post","","form");

  $formulario -> nueva_tabla();
  $formulario -> linea("DATOS DE LA CARAVANA NUMERO ".$GLOBALS[carava]." ",0);
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



// *********************************************************************************
}//FIN CLASE PROC_DESPAC

   $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>