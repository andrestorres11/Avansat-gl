<?php
/****************************************************************************
NOMBRE:   ins_carav_carav
FUNCION:  Insertar Salida para los despachos relacionados en una caravana
AUTOR:    LEONARDO ROMERO CASTRO
FECHA DE CREACION: 28 de Noviembre de 2005
FECHA DE MODIFICACION: 17 de Febrero de 2006
Modificaciones: Se adecuo el fomato para SAD TRAFICO V 1.5
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
          $this -> Formulario();
          break;
        case "2":
          $this -> Insertar();
          break;
        case "3":
         $this -> desasignar();
         break;
        default:
          $this -> Listar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL
// *****************************************************

//FUNCION LISTAR
// *****************************************************

 function Listar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

     $titori[0][0]=0;
     $titori[0][1]='Origen';
     $titdes[0][0]=0;
     $titdes[0][1]='Destino';
     $todas[0][0]=0;
     $todas[0][1]='Todas';

     $query = "SELECT b.cod_ciudad, b.nom_ciudad
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_genera_ciudad b
           WHERE a.cod_ciuori = b.cod_ciudad AND
                 a.fec_llegad Is Null AND
                 a.fec_salida Is Null AND
                 a.ind_planru = 'S' AND
                 a.ind_anulad = 'R' AND
                 a.num_carava != 0 ";

     if(isset($_REQUEST[origen]) AND $_REQUEST[origen] != 0)

        $query = $query." AND b.cod_ciudad = '$_REQUEST[origen]'";

     if(isset($_REQUEST[destino]) AND $_REQUEST[destino] != 0)

        $query = $query." AND a.cod_ciudes = '$_REQUEST[destino]'";

     $query = $query." GROUP BY 1,2 ORDER BY 2";

     $consulta = new Consulta($query, $this -> conexion);
     $origen = $consulta -> ret_matriz();

     if(isset($_REQUEST[origen]) AND $_REQUEST[origen] != 0)

        $origen=array_merge($origen,$todas);


     else

        $origen=array_merge($titori,$origen);


     $query = "SELECT b.cod_ciudad, b.nom_ciudad
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_genera_ciudad b
           WHERE a.cod_ciudes = b.cod_ciudad AND
                 a.fec_llegad Is Null AND
                 a.fec_salida Is Null AND
                 a.ind_planru = 'S' AND
                 a.ind_anulad = 'R' AND
                 a.num_carava != 0 ";

     if(isset($_REQUEST[destino]) AND $_REQUEST[destino] != 0)

        $query = $query." AND b.cod_ciudad = '$_REQUEST[destino]'";

     if(isset($_REQUEST[origen]) AND $_REQUEST[origen] != 0)

        $query = $query." AND a.cod_ciuori = '$_REQUEST[origen]'";

     $query = $query." GROUP BY 1,2 ORDER BY 2";

     $consulta = new Consulta($query, $this -> conexion);
     $destino = $consulta -> ret_matriz();

     if(isset($_REQUEST[destino]) AND $_REQUEST[destino] != 0)

        $destino=array_merge($destino,$todas);

     //presenta todas las ciudades con su respetivo titulo
     else

        $destino=array_merge($titdes,$destino);


  $query = "SELECT a.num_carava,COUNT(a.num_despac),c.abr_ciudad,d.abr_ciudad,
                   b.cod_rutasx
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_despac_vehige b,
                 ".BASE_DATOS.".tab_genera_ciudad c,
                 ".BASE_DATOS.".tab_genera_ciudad d
           WHERE a.num_despac = b.num_despac AND
                 a.cod_ciuori = c.cod_ciudad AND
                 a.cod_ciudes = d.cod_ciudad AND
                 a.num_carava != 0 AND
                 a.ind_anulad = 'R' AND
                 a.fec_salida Is Null AND
                 a.fec_llegad Is Null ";

     if(isset($_REQUEST[destino]) AND $_REQUEST[destino] != 0)
        $query = $query." AND d.cod_ciudad = '$_REQUEST[destino]'";

     if(isset($_REQUEST[origen]) AND $_REQUEST[origen] != 0)
        $query = $query." AND a.cod_ciuori = '$_REQUEST[origen]'";

  $query = $query." GROUP BY 1 ORDER BY 1";

  $consulta = new Consulta($query, $this -> conexion);
  $matriz = $consulta -> ret_matriz();

  for($i=0;$i<sizeof($matriz);$i++)

        $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&ruta=".$matriz[$i][4]."&carava=".$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

   $formulario = new Formulario ("index.php","post","","form_item");
   $formulario -> linea("<b>Se Encontraron ".sizeof($matriz)." Caravanas</b>",0);
   $formulario -> nueva_tabla();

   if(sizeof($matriz) > 0)
   {
   $formulario -> linea("Nro CARAVANA",0);
   $formulario -> linea("Despachos",0);
   $formulario -> lista_titulo("Origen", "origen\" onChange=\"form_item.submit()\"",$origen, 0);
   $formulario -> lista_titulo("Destino", "destino\" onChange=\"form_item.submit()\"",$destino, 1);
   for($i=0;$i<sizeof($matriz);$i++)

   {
     if($i%2 == 0)
     {
      echo "<td class=\"celda2\">".$matriz[$i][0]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][1]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][2]."</td>";
      echo "<td class=\"celda2\">".$matriz[$i][3]."</td></tr><tr>";
     }//fin if
     else
     {
      echo "<td class=\"celda\">".$matriz[$i][0]."</td>";
      echo "<td class=\"celda\">".$matriz[$i][1]."</td>";
      echo "<td class=\"celda\">".$matriz[$i][2]."</td>";
      echo "<td class=\"celda\">".$matriz[$i][3]."</td></tr><tr>";
     }//fin else
   }//fin for
   }//fin if

   $formulario -> nueva_tabla();
   $formulario -> botoni("Volver","history.go(1)",1);
   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",4,0);
   $formulario -> oculto("valor",$valor,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();

 }//FIN FUNCION LISTAR


// *****************************************************
//FUNCION FORMULARIO
// *****************************************************
 function Formulario()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
   //trae la fecha actual
   $fec_actual = date("d-m-Y");
   $hor_actual = date("H:i");
   //presenta por defecta la fecha actual
   if(!isset($_REQUEST[fecsal]))
      $_REQUEST[fecsal]=$fec_actual;
   if(!isset($_REQUEST[horsal]))
      $_REQUEST[horsal]=$hor_actual;

      if($_REQUEST[desasign])
      {
      $tmp = $_REQUEST[desasign];
      $desa = array_values($tmp);
      }
      if($desa)
      {
              for($i=0; $i < sizeof($desa); $i++)
              {
                  $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                            SET num_carava = '0'
                            WHERE num_despac = '".$desa[$i]."'";
                  $consulta  = new Consulta($query,$this -> conexion);
              }
      }

  $query = "SELECT a.num_despac,a.cod_manifi,
                   c.abr_tercer,d.num_placax,
                   e.abr_tercer,e.num_telmov
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_tercer_tercer c,".BASE_DATOS.".tab_despac_vehige d,
                 ".BASE_DATOS.".tab_tercer_tercer e,".BASE_DATOS.".tab_genera_ciudad f,
                 ".BASE_DATOS.".tab_genera_ciudad g
           WHERE a.num_despac = d.num_despac AND
                 d.cod_transp = c.cod_tercer AND
                 d.cod_conduc = e.cod_tercer AND
                 d.ind_activo = 'R' AND
                 a.cod_ciuori = f.cod_ciudad AND
                 a.cod_ciudes = g.cod_ciudad AND
                 a.num_carava = '".$_REQUEST[carava]."' AND
                 a.fec_salida Is Null AND
                 a.fec_llegad Is Null
                 ORDER BY 1 ";
  $consulta = new Consulta($query, $this -> conexion);
  $encabe   = $consulta -> ret_matriz();


  for($i=0; $i < sizeof($encabe);$i++)


  $ruta = 0;
  for($i=0; $i < sizeof($encabe);$i++)
  {
  //query que trae los despachos en ruta con la placa a salir
  $query = "SELECT a.num_despac
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_despac_vehige b
           WHERE a.num_despac = b.num_despac AND
                 a.fec_salida Is not Null AND
                 a.fec_llegad Is Null AND
                 b.ind_activo = 'S' AND
                 b.num_placax = '".$encabe[$i][3]."'
        ORDER BY 1";
  $consulta = new Consulta($query, $this -> conexion);
  $enruta = $consulta -> ret_matriz();

  if(sizeof($enruta) == 0)
  {
          $encabe[$i][6] = "NO";
  }
  else
  {
          $encabe[$i][6] = "SI";
          $plac_enruta = $plac_enruta+1;
  }

 }



  $query = "SELECT  a.cod_contro,b.nom_contro,a.val_duraci
              FROM ".BASE_DATOS.".tab_genera_rutcon a,".BASE_DATOS.".tab_genera_contro b,
                   ".BASE_DATOS.".tab_despac_vehige c,".BASE_DATOS.".tab_despac_seguim d
             WHERE a.cod_contro = b.cod_contro AND
                   a.cod_rutasx = c.cod_rutasx AND
                   c.num_despac = d.num_despac AND
                   a.cod_contro = d.cod_contro AND
                   a.cod_rutasx = '".$_REQUEST[ruta]."'
             GROUP BY a.cod_contro ORDER BY a.val_duraci";
   $consulta = new Consulta($query, $this -> conexion);

   $matriz = $consulta -> ret_matriz();
   //lista las novedades
   $query = "SELECT cod_noveda,nom_noveda
               FROM ".BASE_DATOS.".tab_genera_noveda
              WHERE ind_alarma = 'N'
           ORDER BY 2";
   $consulta = new Consulta($query, $this -> conexion);
   $novedad = $consulta -> ret_matriz();
   $novedad[0][0] ='-';$novedad[0][1] ='-';

   for ($i = 0; $i < sizeof($encabe); $i ++){

   //trae los sitios planeados de pernotacion
   $query = "SELECT cod_contro,cod_noveda,val_pernoc
               FROM ".BASE_DATOS.".tab_despac_pernoc
              WHERE num_despac = '".$encabe[$i][0]."'";
   $consulta = new Consulta($query, $this -> conexion);
   $pernot = $consulta -> ret_matriz();



   for($j=0;$j<sizeof($matriz);$j++)
   {
    for($k=0;$k<sizeof($pernot);$k++)
    {
     if($matriz[$j][0] == $pernot[$k][0])
     {
      $ant[$j]=$pernot[$k][1];
      $igual[$j]=1;
      $time[$j]=$pernot[$k][2];
      $i++;
     }//fin if
     else
     {
      $ant[$j]='-';
      $igual[$j]=0;
      $time[$j]=0;
     }//fin else
    }//fin for $j
   }//fin for $i

   }

  echo "<script language=\"JavaScript\" src=\"../satb_standa/js/fecha.js\"></script>\n";
  echo "<b>SALIDA DE CARAVANAS</b>";

  $formulario = new Formulario ("index.php","post","","form_ins");

  $formulario -> nueva_tabla();
  $formulario -> linea("DATOS DE LA CARAVANA NUMERO ".$_REQUEST[carava]." ",0);
  $formulario -> nueva_tabla();
   if(sizeof($encabe) > 0)
   {
   $formulario -> linea("",0);
   $formulario -> linea("S/N",0);
   $formulario -> linea("Despacho",0);
   $formulario -> linea("Manifiesto",0);
   $formulario -> linea("Transportadora",0);
   $formulario -> linea("Placa",0);
   $formulario -> linea("Conductor",0);
   $formulario -> linea("Vehiculo en Ruta",0);
   $formulario -> linea("Celular",1);

   for($i=0;$i<sizeof($encabe);$i++)

   {
       $despac[$i] = $encabe[$i][0];
     if($i%2 == 0)
     {
      $formulario -> caja("","des[$i]","$despac[$i]",1,0);
      echo "<td class=\"celda2\">".$encabe[$i][0]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][1]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][2]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][3]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][4]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][6]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][5]."</td></tr><tr>";
     }//fin if
     else
     {
      $formulario -> caja("","des[$i]","$despac[$i]",1,0);
      echo "<td class=\"celda\">".$encabe[$i][0]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][1]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][2]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][3]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][4]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][6]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][5]."</td></tr><tr>";
     }//fin else
   }//fin for
   }//fin if

  $formulario -> nueva_tabla();
  $formulario -> linea("Sitios de Pernoctacion",0);
  $formulario -> nueva_tabla();
  $formulario -> texto ("Fecha Salida (dd-mm-yyyy)","text","fecsal",0,10,10,"","$_REQUEST[fecsal]");
  $formulario -> texto ("Hora Salida (HH:mm)","text","horsal",0,5,5,"","$_REQUEST[horsal]");
  $formulario -> nueva_tabla();
  $formulario -> linea("",0);
  $formulario -> linea("S/N",0);
  $formulario -> linea("PUESTO DE CONTROL",0);
  $formulario -> linea("",0);
  $formulario -> linea("TIEMPO (MINUTOS)",0);
  $formulario -> linea("",0);
  $formulario -> linea("NOVEDADES",1);

  for($i=0; $i < sizeof($matriz); $i++)
  {

      $formulario -> caja("","pernoctar[$i]",1,1,0);
    echo "<td class=\"etiqueta\"><b>".$matriz[$i][1]." </b></td>";
    if($igual[$i]==1)
      $formulario -> texto ("","text","tiempo[$i]",0,15,15,"","".$time[$i]."");
    else
      $formulario -> texto ("","text","tiempo[$i]",0,15,15,"","");
    if($igual[$i]==1)
    {
     $query = "SELECT cod_noveda,nom_noveda FROM ".BASE_DATOS.".tab_genera_noveda WHERE cod_noveda = '".$ant[$i]."' ";
     $consulta = new Consulta($query, $this -> conexion);
     $nov_ant = $consulta -> ret_matriz();
     $anterior[0][0]=$nov_ant[0][0];
     $anterior[0][1]=$nov_ant[0][1];
     $novedad_a=array_merge($anterior,$novedad);
     $formulario -> lista("", "novedad[$i]", $novedad_a, 0);
    }//fin else
    else
    $formulario -> lista("", "novedad[$i]", $novedad, 0);
    echo "</tr><tr>";
  }//fin for

  $formulario -> nueva_tabla();
  $formulario -> texto ("OBSERVACIONES DE CARAVANA:","textarea","obscar",1,50,3,"","");
  $formulario -> nueva_tabla();
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("tercero","$tercero",0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
  $formulario -> oculto("opcion",3,0);
  for($i=0;$i<sizeof($despac);$i++)
  $formulario -> oculto("despac[$i]",$despac[$i],0);

  $formulario -> oculto("ruta", $_REQUEST[ruta],0);
  $formulario -> oculto("carava", "$_REQUEST[carava]",0);
  $formulario -> nueva_tabla();
  $formulario -> botoni("Aceptar","aceptar_ins()",0);
  if($plac_enruta > 0)
  $formulario -> botoni("desasignar_c","form_ins.submit()",0);
  $formulario -> botoni("Borrar","reset",1);
  $formulario -> cerrar();

 }//FIN FUNCION ACTUALIZAR


 function Insertar()

 {
  $fec_actual = date('Y-m-d H:m:s');
  $fecha=explode("-",$_REQUEST[fecsal]);

  $fecha=$fecha[2]."-".$fecha[1]."-".$fecha[0]." ".$_REQUEST[horsal].":00";

  $despa = array_values($_REQUEST[des]);

    $contros=$_REQUEST[contro];
    $tiempos=$_REQUEST[tiempo];
    $novedades=$_REQUEST[novedad];



  for ($i=0; $i < sizeof($despa) ; $i++ )
  {
  //query que trae la placa
   $query = "SELECT num_placax
            FROM ".BASE_DATOS.".tab_despac_vehige
           WHERE num_despac = '".$despa[$i]."' ";
  $consulta = new Consulta($query, $this -> conexion);
  $placa = $consulta -> ret_matriz();


  //query que trae los despachos en ruta con la placa a salir
  $query = "SELECT a.num_despac
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_despac_vehige b
           WHERE a.num_despac = b.num_despac AND
                 a.fec_salida Is not Null AND
                 a.fec_llegad Is Null AND
                 b.ind_activo = 'S' AND
                 b.num_placax = '".$placa[0][0]."'
        ORDER BY 1";
  $consulta = new Consulta($query, $this -> conexion);
  $enruta = $consulta -> ret_matriz();

  if(sizeof($enruta) == 0)
  {
    //actualiza la hora de salida del despacho
    $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
               SET fec_salida = '$fecha'
             WHERE num_despac = '".$despa[$i]."' ";
    $consulta = new Consulta($query, $this -> conexion, "R");

   $query="SELECT a.cod_contro,a.val_duraci,DATE_ADD('$fecha', INTERVAL a.val_duraci MINUTE)
              FROM ".BASE_DATOS.".tab_genera_rutcon a,".BASE_DATOS.".tab_despac_vehige b,
                   ".BASE_DATOS.".tab_despac_seguim c
             WHERE a.cod_rutasx = b.cod_rutasx AND
                   b.num_despac = c.num_despac AND
                   c.cod_contro = a.cod_contro AND
                   b.num_despac = '".$despa[$i]."'
          ORDER BY val_duraci";
    $consulta = new Consulta($query, $this -> conexion, "R");
    $matriz = $consulta -> ret_matriz();

    $puestos_sad = $matriz;

         ///manejo de la interfaz
        $query = "SELECT cod_operad,nom_operad,'',ind_estado
                     FROM ".BASE_DATOS.".tab_interf_parame
                     WHERE ind_estado = '1'";
         $consulta = new Consulta($query, $this -> conexion);
         $interf = $consulta -> ret_matriz();

              ///manejo de la interfaz
        $query = "SELECT cod_rutasx
                     FROM ".BASE_DATOS.".tab_despac_vehige
                     WHERE num_despac = '".$despa[$i]."'";
         $consulta = new Consulta($query, $this -> conexion);
         $rutaxx = $consulta -> ret_arreglo();


         $query = "SELECT fec_salipl
                   FROM ".BASE_DATOS.".tab_despac_vehige
                   WHERE num_despac = '".$despa[$i]."'";
         $consulta = new Consulta($query, $this -> conexion,"R");
         $fec_salpl = $consulta -> ret_arreglo();

         for($j=0; $j<sizeof($interf); $j++)

         {

           //se crea el objeto para manejar terceros

           $interfaz = new Interfaz(BASE_DATOS,$this -> conexion, $this -> usuario_aplicacion);

           if($interf[$j][0])

           {

               //inserta la Salida
                             //ins_salida($bd_interf,$cod_trans,$despac_sad,$rut_sad,$placa,$fec_salida,$puestos_sad,$fec_salpl)
               if($interfaz -> ins_salida($interf[$j][2],NIT_TRANSPOR,$despa[$i],$rutaxx[0],$placa[0][0],$fecha,$puestos_sad,$fec_salpl[0]))

                  echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">El Vechiculo con Placas <b>$_REQUEST[placa]</b> Salio Correctamente en ".$interf[$i][1]." <br>";

              else

              {

                   echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">El Vechiculo con Placas <b>$_REQUEST[placa]</b> esta en ruta reporte en ".$interf[$i][1]."<br>";

                   exit;

              }

           }

         }






    //actualiza el estado del despacho
    $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                SET  ind_activo='S'
               WHERE num_despac='".$despa[$i]."'";
    $activar = new Consulta($query, $this -> conexion, "R");

    //elimina todo los sitios de pernotacion
    $query = "DELETE FROM ".BASE_DATOS.".tab_despac_pernoc
                    WHERE num_despac = '".$despa[$i]."'";
    $eliminacion = new Consulta($query, $this -> conexion, "R");


     for($j=0;$j<sizeof($matriz);$j++)
     {

      if($tiempos[$j] != Null)
      {
       $resultado[$j]=$matriz[$j][1]+$carry[$j];
       $carry[$j+1]=$carry[$j]+$tiempos[$j];
       //inserta la novedad
       $query = "INSERT INTO ".BASE_DATOS.".tab_despac_pernoc
                 VALUES ('".$despa[$i]."', '".$matriz[$j][0]."','".$novedades[$i]."','".$tiempos[$i]."',
                 '$_REQUEST[usuario]', '$fec_actual', '$_REQUEST[usuario]', '$fec_actual')";
      $insercion = new Consulta($query, $this -> conexion, "R");

      }//fin if

      else

      {

       $resultado[$j]=$matriz[$j][1]+$carry[$j];
       $carry[$j+1]=$carry[$j];
       $novedades[$j]=0;
      }//fin else
     }//fin for

     for($j=0; $j < sizeof($matriz); $j++)
     {
       $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                   SET fec_alarma = DATE_ADD('$fecha', INTERVAL ".$resultado[$j]." MINUTE)
                 WHERE num_despac = '".$despa[$i]."' AND
                       cod_contro = '".$matriz[$j][0]."' ";
      $insercion = new Consulta($query, $this -> conexion, "R");
     }//fin for

     //trae el ultimo registro de la matriz s/n su tamaño

     $ult = sizeof($matriz)-1;
     $resultado[$ult];

     //trae el ultimo campo del resultado
     $ultimo = $resultado[$ult];

     //actualiza la hora y fecha de llegada planeada s/n el ultimo puesto de control

     $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
               SET fec_llegpl = DATE_ADD('$fecha', INTERVAL ".$ultimo." MINUTE)
               WHERE num_despac = '".$despa[$i]."' ";
     $insercion = new Consulta($query, $this -> conexion, "RC");


     $mensaje = "<b>TRANSACCION EXITOSA<br>LA SALIDA DE LA CARAVANA FUE REPORTADA<b>";
      echo "<img src=\"../satb_standa/imagenes/ok.gif\">$mensaje<br>";
     }
    else
    {
      echo "<img src=\"../satb_standa/imagenes/ok.gif\"><b>Vehículo con placas ".$placa[0][0]." se encuentra en ruta reporte su llegada<br>";

    }
  }//fin for

 }//FIN FUNCION


 function desasignar()
 {
  $query = "SELECT a.num_despac,a.cod_manifi,
                   c.abr_tercer,d.num_placax,
                   e.abr_tercer,e.num_telmov
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_tercer_tercer c,".BASE_DATOS.".tab_despac_vehige d,
                 ".BASE_DATOS.".tab_tercer_tercer e,".BASE_DATOS.".tab_genera_ciudad f,
                 ".BASE_DATOS.".tab_genera_ciudad g
           WHERE a.num_despac = d.num_despac AND
                 d.cod_transp = c.cod_tercer AND
                 d.cod_conduc = e.cod_tercer AND
                 d.ind_activo = 'R' AND
                 a.cod_ciuori = f.cod_ciudad AND
                 a.cod_ciudes = g.cod_ciudad AND
                 a.num_carava = '".$_REQUEST[carava]."' AND
                 a.fec_salida Is Null AND
                 a.fec_llegad Is Null
                 ORDER BY 1 ";
  $consulta = new Consulta($query, $this -> conexion);
  $encabe   = $consulta -> ret_matriz();

  $ruta = 0;
  for($i=0; $i < sizeof($encabe);$i++)
  {
  //query que trae los despachos en ruta con la placa a salir
  $query = "SELECT a.num_despac
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_despac_vehige b
           WHERE a.num_despac = b.num_despac AND
                 a.fec_salida Is not Null AND
                 a.fec_llegad Is Null AND
                 b.ind_activo = 'S' AND
                 b.num_placax = '".$encabe[$i][3]."'
        ORDER BY 1";
  $consulta = new Consulta($query, $this -> conexion);
  $enruta = $consulta -> ret_matriz();

  if(sizeof($enruta) == 0)
  {
          $encabe[$i][6] = "NO";
  }
  else
  {
          $encabe[$i][6] = "SI";
          $plac_enruta = $plac_enruta+1;
  }

 }
  echo "<script language=\"JavaScript\" src=\"js/fecha.js\"></script>\n";
  echo "<script language=\"JavaScript\" src=\"../satb_standa/js/salida.js\"></script>\n";
  echo "<b>SALIDA DE CARAVANAS</b>";

  $formulario = new Formulario ("index.php","post","Desasignar Despacho","form_desasi");
        print_r($despac);
  $formulario -> nueva_tabla();
  $formulario -> linea("DATOS DE LA CARAVANA NUMERO ".$_REQUEST[carava]." ",0);
  $formulario -> nueva_tabla();
   if(sizeof($encabe) > 0)
   {
   $formulario -> linea("",0);
   $formulario -> linea("S/N",0);
   $formulario -> linea("Despacho",0);
   $formulario -> linea("Manifiesto",0);
   $formulario -> linea("Transportadora",0);
   $formulario -> linea("Placa",0);
   $formulario -> linea("Conductor",0);
   $formulario -> linea("Vehiculo en Ruta",0);
   $formulario -> linea("Celular",1);

   for($i=0;$i<sizeof($encabe);$i++)

   {
       $despac[$i] = $encabe[$i][0];
     if($i%2 == 0)
     {
      if($encabe[$i][6] != "NO")
      $formulario -> caja("","desasign[$i]","$despac[$i]",1,0);
      else
      $formulario -> caja("","desasign[$i]","$despac[$i]",0,0);

      echo "<td class=\"celda2\">".$encabe[$i][0]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][1]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][2]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][3]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][4]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][6]."</td>";
      echo "<td class=\"celda2\">".$encabe[$i][5]."</td></tr><tr>";
     }//fin if
     else
     {
      if($encabe[$i][6] != "NO")
      $formulario -> caja("","desasign[$i]","$despac[$i] ",1,0);
      else
      $formulario -> caja("","desasign[$i]","$despac[$i] ",0,0);

      echo "<td class=\"celda\">".$encabe[$i][0]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][1]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][2]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][3]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][4]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][6]."</td>";
      echo "<td class=\"celda\">".$encabe[$i][5]."</td></tr><tr>";
     }//fin else
   }//fin for
   }//fin if

  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("tercero","$tercero",0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
  $formulario -> oculto("opcion",1,0);
  $formulario -> oculto("ruta", $_REQUEST[ruta],0);
  $formulario -> oculto("carava", "$_REQUEST[carava]",0);
  $formulario -> nueva_tabla();
  $formulario -> boton("Aceptar","button\" onClick=\"if(confirm('Esta seguro que desea Desasignar los Vehiculos Seleccionados de la Caravana?')){form_desasi.submit()}",0);
  $formulario -> boton("Borrar","reset",1);
  $formulario -> cerrar();


 }//Fin desasignar



}//FIN CLASE PROC_DESPAC

   $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>