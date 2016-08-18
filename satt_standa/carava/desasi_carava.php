<?php
/****************************************************************************
NOMBRE:   desasi_carav_carav
FUNCION:  Desasigna el despachos relacionados en una caravana
AUTOR:    LEONARDO ROMERO CASTRO
FECHA DE CREACION: 27 de Marzo de 2006
FECHA DE MODIFICACION: 27 de Marzo de 2006
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
    $this -> Buscar();
  else
     {
      switch($GLOBALS[opcion])
       {
        case "1":
          $this -> Buscar();
          break;
        case "2":
         $this -> desasignar();
          break;
         case "3":
         $this -> Actualizar_carava();
         break;
        default:
        $this -> Buscar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL
// *****************************************************


 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];
     $titori[0][0]=0;
     $titori[0][1]='Origen';
     $titdes[0][0]=0;
     $titdes[0][1]='Destino';
     $todas[0][0]=0;
     $todas[0][1]='Todas';


     $fechaini = new fecha();
     $fechafin = new fecha();

     if($GLOBALS[fil]== 1)
         $val1 = 1;
     else
         $val1 = 0;

     if($GLOBALS[fil]== 2)
         $val2 = 1;
     else
         $val2 = 0;


     echo "<br><b>Busqueda de Despachos en Caravana</b><br>";
     $formulario = new Formulario ("index.php","post","<small>Seleccione el estado del despacho</small>","form_fecha", "");
     $formulario -> nueva_tabla();
     $formulario -> linea("Seleccione el estado de la caravana",0);
     $formulario -> radio("En Ruta","fil\" OnClick=\"form_fecha.submit()\" ",1,$val1,0);
     $formulario -> radio("Pendiente Por Salir","fil\" OnClick=\"form_fecha.submit()\"",2,$val2,1);
     $formulario -> nueva_tabla();

     if($GLOBALS[fil])
     {
          $query = "SELECT b.cod_ciudad, b.nom_ciudad
                 FROM ".BASE_DATOS.".tab_despac_despac a,
                      ".BASE_DATOS.".tab_genera_ciudad b
                WHERE a.cod_ciuori = b.cod_ciudad AND
                      a.ind_anulad = 'R' AND
                      a.num_carava != 0 ";

          if($GLOBALS[fil] == 1 )
          {
            $query .=" AND a.fec_salida is Not Null
                       AND a.fec_llegad Is Null ";
          }
          else
          {
            $query .="AND a.fec_llegad Is Null
                      AND a.fec_salida Is Null ";
          }

          if(isset($GLOBALS[origen]) AND $GLOBALS[origen] != 0)

             $query = $query." AND b.cod_ciudad = '$GLOBALS[origen]'";

          if(isset($GLOBALS[destino]) AND $GLOBALS[destino] != 0)

             $query = $query." AND a.cod_ciudes = '$GLOBALS[destino]'";

          $query = $query." GROUP BY 1,2 ORDER BY 2";

          $consulta = new Consulta($query, $this -> conexion);
          $origen = $consulta -> ret_matriz();

          if(isset($GLOBALS[origen]) AND $GLOBALS[origen] != 0)

             $origen=array_merge($origen,$todas);


          else

             $origen=array_merge($titori,$origen);


          $query = "SELECT b.cod_ciudad, b.nom_ciudad
                 FROM ".BASE_DATOS.".tab_despac_despac a,
                      ".BASE_DATOS.".tab_genera_ciudad b
                WHERE a.cod_ciudes = b.cod_ciudad AND
                      a.ind_anulad = 'R' AND
                      a.num_carava != 0 ";

          if($GLOBALS[fil] == 1 )
          {
            $query .=" AND a.fec_salida is Not Null
                       AND a.fec_llegad Is Null ";
          }
          else
          {
            $query .="AND a.fec_llegad Is Null
                      AND a.fec_salida Is Null ";
          }


          if(isset($GLOBALS[destino]) AND $GLOBALS[destino] != 0)

             $query = $query." AND b.cod_ciudad = '$GLOBALS[destino]'";

          if(isset($GLOBALS[origen]) AND $GLOBALS[origen] != 0)

             $query = $query." AND a.cod_ciuori = '$GLOBALS[origen]'";

          $query = $query." GROUP BY 1,2 ORDER BY 2";

          $consulta = new Consulta($query, $this -> conexion);
          $destino = $consulta -> ret_matriz();

          if(isset($GLOBALS[destino]) AND $GLOBALS[destino] != 0)

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
                      a.ind_anulad = 'R' ";

          if($GLOBALS[fil] == 1 )
          {
            $query .=" AND a.fec_salida is Not Null
                       AND a.fec_llegad Is Null ";
          }
          else
          {
            $query .="AND a.fec_llegad Is Null
                      AND a.fec_salida Is Null ";
          }


          if(isset($GLOBALS[destino]) AND $GLOBALS[destino] != 0)
             $query = $query." AND d.cod_ciudad = '$GLOBALS[destino]'";

          if(isset($GLOBALS[origen]) AND $GLOBALS[origen] != 0)
             $query = $query." AND a.cod_ciuori = '$GLOBALS[origen]'";

       $query = $query." GROUP BY 1 ORDER BY 1";

       $consulta = new Consulta($query, $this -> conexion);
       $matriz = $consulta -> ret_matriz();

       for($i=0;$i<sizeof($matriz);$i++)

             $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&ruta=".$matriz[$i][4]."&carava=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

        $formulario -> linea("<b>Se Encontraron ".sizeof($matriz)." Caravanas</b>",0);
        $formulario -> nueva_tabla();

        if(sizeof($matriz) > 0)
        {
        $formulario -> linea("Nro CARAVANA",0);
        $formulario -> linea("Despachos",0);
        $formulario -> lista_titulo("Origen", "origen\" onChange=\"form_fecha.submit()\"",$origen, 0);
        $formulario -> lista_titulo("Destino", "destino\" onChange=\"form_fecha.submit()\"",$destino, 1);
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
  }  //if Fil

     $formulario -> nueva_tabla();
     $formulario -> oculto("opcion",1,0);
     $formulario -> oculto("usuario","$datos_usuario[cod_usuari]",0);
     $formulario -> oculto("window","central",0);
     $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
     $formulario -> cerrar();
 }


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
                 a.num_carava = '".$GLOBALS[carava]."' AND
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
  $formulario -> linea("DATOS DE LA CARAVANA NUMERO ".$GLOBALS[carava]." ",0);
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
  $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
  $formulario -> oculto("opcion",3,0);
  $formulario -> oculto("ruta", $GLOBALS[ruta],0);
  $formulario -> oculto("carava", "$GLOBALS[carava]",0);
  $formulario -> nueva_tabla();
  $formulario -> boton("Aceptar","button\" onClick=\"if(confirm('Esta seguro que desea Desasignar los Vehiculos Seleccionados de la Caravana?')){form_desasi.submit()}",0);
  $formulario -> boton("Borrar","reset",1);
  $formulario -> cerrar();


 }//Fin desasignar


 function Actualizar_carava()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

      if($GLOBALS[desasign])
      {
      $tmp = $GLOBALS[desasign];
      $desa = array_values($tmp);
      }
      if($desa)
      {
              for($i=0; $i < sizeof($desa); $i++)
              {
                   $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                            SET num_carava = '0',
                                usr_modifi = '$usuario',
                                fec_modifi = NOW()
                            WHERE num_despac = '".$desa[$i]."'";
                  $consulta  = new Consulta($query,$this -> conexion);

                  $mensaje = "<b>El Despacho ".$desa[$i]." ha sido desasignado de la Caravana $GLOBALS[carava]</b>";
                  echo "<br><img src=\"../satb_standa/imagenes/ok.gif\">$mensaje<br>";
              }
      }

     $this -> Buscar();

 }//FIN FUNCION ACTUALIZAR



}//FIN CLASE PROC_DESPAC

   $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>