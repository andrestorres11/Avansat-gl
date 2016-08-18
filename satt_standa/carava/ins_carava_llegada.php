<?php
/****************************************************************************

NOMBRE:   ins_carav_llegada
FUNCION:  Insertar Llegada para los despachos relacionados en una caravana
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

   $this -> encabezado("LLEGADA DE CARAVANAS");
  echo "<script language=\"javascript\" src=\"../".DIR_APLICA_CENTRAL."/js/llegada.js\" ></script>\n";
  $formulario = new Formulario ("index.php","post","","form_ins");
  $formulario -> linea ("FECHA DE LA NOVEDAD (dd-mm-yyyy)",0);
  $formulario -> linea ("<b>".$fec_actual."</b>",1);
  $formulario -> linea ("HORA DE LA NOVEDAD (HH:mm)",0);
  $formulario -> linea ("<b>".$hor_actual."</b>",1);
  $formulario -> nueva_tabla();
  $formulario -> texto ("OBSERVACIONES","textarea","obs",1,50,5,"","");
  $formulario -> nueva_tabla();
  $formulario -> oculto("usuario","$usuario",0);
  $formulario -> oculto("tercero","$tercero",0);
  $formulario -> oculto("feclle","$fec_actual",0);
  $formulario -> oculto("horlle","$hor_actual",0);
  $formulario -> oculto("window","central",0);
  $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
  $formulario -> oculto("opcion",2,0);
  $formulario -> oculto("carava","$GLOBALS[carava]",0);
  $formulario -> nueva_tabla();
  $formulario -> botoni("Aceptar","aceptar_ins_carava()",0);
  $formulario -> botoni("Borrar","reset",1);
  $formulario -> cerrar();

 }//FIN FUNCION

function encabezado($tit= "Caravanas"){
   //Encabezado
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
                 a.cod_ciuori = f.cod_ciudad AND
                 a.cod_ciudes = g.cod_ciudad AND
                 a.num_carava = '".$GLOBALS[carava]."' AND
                 a.fec_salida Is Not Null AND
                 a.fec_llegad Is Null
                 ORDER BY 1 ";
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

//FUNCION INSERTAR
// *****************************************************
 function Insertar()
 {

  $fec_actual = date("d-m-Y");
  $feclle=$fec_actual;
  $hor_actual = date("H:i");
  $horlle=$hor_actual;
  $fecha=explode("-",$feclle);
  $fecha=$fecha[2]."-".$fecha[1]."-".$fecha[0]." ".$horlle.":00";

  $query = "SELECT a.num_despac
            FROM ".BASE_DATOS.".tab_despac_despac a,
                 ".BASE_DATOS.".tab_despac_vehige b
           WHERE a.num_despac = b.num_despac AND
                 a.num_carava = '".$GLOBALS[carava]."' AND
                 b.ind_activo = 'S' AND
                 a.fec_salida Is Not Null AND
                 a.fec_llegad Is Null
                 ORDER BY 1 ";
  $consulta = new Consulta($query, $this -> conexion);
  $despachos   = $consulta -> ret_matriz();

  $query = "UPDATE ".BASE_DATOS.".tab_carava_despac
            SET fec_llegad = '$fecha',
                obs_llegad = '$GLOBALS[obs]'  ";
  $consulta = new Consulta($query, $this -> conexion, "BR");


  for($i = 0; $i < sizeof($despachos);$i++)
  {
   //actualiza la hora de llegada del despacho
    $query="UPDATE ".BASE_DATOS.".tab_despac_despac
               SET fec_llegad = '$fecha',
                   obs_llegad = '$GLOBALS[obs]'
             WHERE num_despac = '".$despachos[$i][0]."' ";
   $consulta = new Consulta($query, $this -> conexion, "R");

        ///manejo de la interfaz
        $query = "SELECT cod_interf,nom_interf,nom_basedx,ind_interf
                  FROM ".BASE_DATOS.".tab_interf_parame
                  WHERE ind_interf = '1'";
         $consulta = new Consulta($query, $this -> conexion);
         $interf = $consulta -> ret_matriz();
         $activar = new Consulta($query, $this -> conexion, "R");

         for($j=0; $j<sizeof($interf); $j++)
         {
           //se crea el objeto para manejar terceros

           $interfaz = new Interfaz(BASE_DATOS,$this -> conexion, $this -> usuario_aplicacion);
           if($interf[$j][0])
           {
               //inserta la novedad
                             //ins_llegada($bd_interf,$cod_trans,$despac_sad,$fec_lleg,$GLOBALSobs,$ind_interf=1)
               if($interfaz -> ins_llegada($interf[$i][2],NIT_TRANSPOR,$despachos[$i][0],$fecha,$GLOBALS[obs],1))
                   echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">La llegada del despacho fue reportada en ".$interf[$i][1]." <br>";
              else
              {
                   echo "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">La llegada no se Registro en ".$interf[$i][1]."<br>";
                   exit;
              }
           }
         }

    if(!mysql_error())
     $consulta = new Consulta("COMMIT", $this -> conexion);
  }

      echo "<br><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\"><b>transaccion exitosa la llegada de la Caravana fue Reportada<br>";
    $this -> Listar();
 }//FIN FUNCION

}//FIN CLASE PROC_DESPAC

   $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>