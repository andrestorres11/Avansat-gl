<?php

class Proc_rutas
{
 var $conexion,
 	 $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }

 function principal()
 {
  if(!isset($GLOBALS[opcion]))
    $this -> Buscar();
  else
  {
      switch($GLOBALS[opcion])
      {
        case "1":
          $this -> Resultado();
          break;
        case "2":
          $this -> Datos();
          break;
        case "3":
          $this -> Insertar();
          break;
      }
  }
 }

 function Buscar()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $formulario = new Formulario ("index.php","post","HOMOLACION ZONAS GPS","form_ins");

   $formulario -> nueva_tabla();
   $formulario -> linea("Digite el Nombre de la Ruta para Homologar los Puestos de Control",1,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea ("",0,"", "15%");
   $formulario -> texto ("Ruta","text","ruta",1,50,255,"","");

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> boton("Buscar","button\" onClick=\"form_ins.submit() ",0);
   $formulario -> cerrar();
 }//FIN FUNCION ACTUALIZAR

 function Resultado()
 {
  $datos_usuario = $this -> usuario -> retornar();
  $usuario=$datos_usuario["cod_usuari"];

  $query = "SELECT a.cod_rutasx,b.nom_rutasx,b.cod_ciuori,b.cod_ciudes,
                   e.nom_tercer,a.cod_transp
              FROM ".BASE_DATOS.".tab_genera_ruttra a,
                   ".BASE_DATOS.".tab_genera_rutasx b,
                   ".BASE_DATOS.".tab_tercer_tercer e,
                   ".BASE_DATOS.".tab_interf_gps f,
                   ".BASE_DATOS.".tab_zonaxx_gps g
             WHERE a.cod_rutasx = b.cod_rutasx AND
                   a.cod_transp = e.cod_tercer AND
                   a.cod_transp = f.cod_transp AND
                   f.ind_estado = '1' AND
                   f.cod_operad = g.cod_operad AND
                   f.cod_transp = g.cod_transp AND
                   b.nom_rutasx LIKE '%$GLOBALS[ruta]%'
                ";

  if($datos_usuario["cod_perfil"] == "")
    {
      //PARA EL FILTRO DE LA EMPRESA TRANSPORTADORA
      $filtro= new Aplica_Filtro_Usuari($this -> cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
    }
    else
    {
      //PARA EL FILTRO DE LA EMPRESA TRANSPORTADORA
      $filtro= new Aplica_Filtro_Perfil($this -> cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND a.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
    }

  $query .= " GROUP BY 1 ORDER BY 1";

  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);

   $formulario = new Formulario ("index.php","post","LISTADO DE RUTAS","form_item");
   $formulario -> nueva_tabla();
   $formulario -> linea("Se Encontrar&oacute;n un Total de ".sizeof($matriz)." Ruta(s)",0,"t2");
   $formulario -> nueva_tabla();

   if(sizeof($matriz) > 0)
   {
    $formulario -> linea("C&oacute;digo",0,"t");
    $formulario -> linea("Descripci&oacute;n",0,"t");
    $formulario -> linea("Origen",0,"t");
    $formulario -> linea("Destino",0,"t");
    $formulario -> linea("Transportadora",0,"t");
    $formulario -> linea("Cant. P/C",1,"t");

    for($i=0;$i<sizeof($matriz);$i++)
    {
     $query = "SELECT COUNT(a.cod_contro)
                 FROM ".BASE_DATOS.".tab_genera_ruttra a
                WHERE a.cod_transp = '".$matriz[$i][5]."' AND
                      a.cod_rutasx = '".$matriz[$i][0]."'
                      GROUP BY a.cod_transp,a.cod_rutasx
              ";

     $consulta = new Consulta($query, $this -> conexion);
     $canpcpru = $consulta -> ret_matriz();

     $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&ruta=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

     $ciudad_o = $objciud -> getSeleccCiudad($matriz[$i][2]);
     $ciudad_d = $objciud -> getSeleccCiudad($matriz[$i][3]);

     $formulario -> linea($matriz[$i][0],0,"i");
     $formulario -> linea($matriz[$i][1],0,"i");
     $formulario -> linea($ciudad_o[0][1],0,"i");
     $formulario -> linea($ciudad_d[0][1],0,"i");
     $formulario -> linea($matriz[$i][4],0,"i");
     $formulario -> linea($canpcpru[0][0],1,"i");
    }//fin for
   }//fin if
   else
   {
    $formulario -> linea("La B&uacute;squeda de Rutas Asignadas a la(s) Transportadora(s) no Arrojo Resultados Debido a Estos tres Posibles Casos:",1,"e");

    $formulario -> nueva_tabla();
    $formulario -> linea("1.) No Existen Operadores GPS Activos con la Interfaz.",1,"i");
    $formulario -> linea("2.) No Existen Listados de Zonas Correspondientes Para La(s) Transportadora(s).",1,"i");
    $formulario -> linea("3.) La Relaci&oacute;n de B&uacute;squeda Para \"".$GLOBALS[ruta]."\" no Coincide en el Sistema.",1,"i");
   }

   $formulario -> nueva_tabla();
   $formulario -> boton("Volver","button\" onClick=\"javascript:history.go(-1)",0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",1,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
   $formulario -> cerrar();
 }

 function Datos()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $inicio[0][0]=0;
   $inicio[0][1]='-';

   $query = "SELECT a.cod_rutasx,a.nom_rutasx,a.cod_ciuori,a.cod_ciudes,
                    a.cod_ciuori,a.cod_ciudes
               FROM ".BASE_DATOS.".tab_genera_rutasx a
              WHERE a.cod_rutasx = '".$GLOBALS[ruta]."'
                    ORDER BY 2";

   $consec = new Consulta($query, $this -> conexion);
   $matriz = $consec -> ret_matriz();

   $query = "SELECT a.cod_tercer,a.abr_tercer
              FROM ".BASE_DATOS.".tab_tercer_tercer a,
                   ".BASE_DATOS.".tab_genera_ruttra b,
                   ".BASE_DATOS.".tab_interf_gps c
             WHERE a.cod_tercer = b.cod_transp AND
                   b.cod_rutasx = ".$GLOBALS[ruta]." AND
                   a.cod_tercer = c.cod_transp AND
                   c.ind_estado = '1'
           ";

  if($datos_usuario["cod_perfil"] == "")
    {
      //PARA EL FILTRO DE LA EMPRESA TRANSPORTADORA
      $filtro= new Aplica_Filtro_Usuari($this -> cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
      }
    }
    else
    {
      //PARA EL FILTRO DE LA EMPRESA TRANSPORTADORA
      $filtro= new Aplica_Filtro_Perfil($this -> cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
      if($filtro -> listar($this -> conexion))
      {
        $datos_filtro = $filtro -> retornar();
        $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
      }
    }

  $query .= "GROUP BY 1";

  $consec = new Consulta($query, $this -> conexion);
  $transp = $consec -> ret_matriz();

  if($GLOBALS[transp] && $GLOBALS[transp] != "0")
  {
   $query = "SELECT a.cod_tercer,a.abr_tercer
               FROM ".BASE_DATOS.".tab_tercer_tercer a,
                    ".BASE_DATOS.".tab_genera_ruttra b
              WHERE a.cod_tercer = b.cod_transp AND
                    b.cod_rutasx = ".$GLOBALS[ruta]." AND
                    b.cod_transp = '".$GLOBALS[transp]."'
                    GROUP BY 1
            ";

   $consec = new Consulta($query, $this -> conexion);
   $transp_a = $consec -> ret_matriz();

   $transp = array_merge($transp_a,$inicio,$transp);
  }
  else
   $transp = array_merge($inicio,$transp);

  $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
  $ciudad_o = $objciud -> getSeleccCiudad($matriz[0][2]);
  $ciudad_d = $objciud -> getSeleccCiudad($matriz[0][3]);

   echo "<script language=\"JavaScript\" src=\"../satt_standa/js/ruttra.js\"></script>\n";

   $formulario = new Formulario ("index.php","post","HOMOLOGACION DE ZONAS","form_ins");

   $formulario -> nueva_tabla();
   $formulario -> linea("Datos de la Ruta",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("C&oacute;digo Ruta",0,"t");
   $formulario -> linea($matriz[0][0],1,"i");
   $formulario -> linea("Nombre Ruta",0,"t");
   $formulario -> linea($matriz[0][1],1,"i");
   $formulario -> linea("Origen Ruta",0,"t");
   $formulario -> linea($ciudad_o[0][1],1,"i");
   $formulario -> linea("Destino Ruta",0,"t");
   $formulario -> linea($ciudad_d[0][1],1,"i");

   $formulario -> lista("Transportadora", "transp\" onChange=\"form_ins.submit()", $transp, 1);

   $indbot = 1;

   if($GLOBALS[transp] && $GLOBALS[transp] != "0")
   {

    //trae los puestos de control de la ruta
    $query = "SELECT a.cod_contro,c.nom_contro,b.val_duraci
                FROM ".BASE_DATOS.".tab_genera_ruttra a,
                     ".BASE_DATOS.".tab_genera_rutcon b,
                     ".BASE_DATOS.".tab_genera_contro c
               WHERE a.cod_contro = c.cod_contro AND
                     a.cod_rutasx = b.cod_rutasx AND
                     c.cod_contro = b.cod_contro AND
                     a.cod_rutasx = '".$GLOBALS[ruta]."' AND
                     a.cod_transp = '".$GLOBALS[transp]."'
                     ORDER BY 3";

    $consec = new Consulta($query, $this -> conexion);
    $matriz2 = $consec -> ret_matriz();

    $query = "SELECT a.cod_tercer,a.nom_tercer
                FROM ".BASE_DATOS.".tab_tercer_tercer a,
                     ".BASE_DATOS.".tab_interf_parame b
               WHERE a.cod_tercer = b.cod_transp AND
                     b.ind_operad = '0' AND
		     		 b.ind_estado = '1' AND
                     b.cod_transp = '".$GLOBALS[transp]."'
             ";

    $consec = new Consulta($query, $this -> conexion);
    $intsat = $consec -> ret_matriz();

    if(!$intsat)
    {
    //Manejo de la Interfaz GPS
    $interf_gps = new Interfaz_GPS();
    $interf_gps -> Interfaz_GPS_envio($GLOBALS[transp],BASE_DATOS,$usuario,$this -> conexion);

    if($interf_gps -> cant_interf > 0)
    {
        $k = 0;
        for($i = 0; $i < $interf_gps -> cant_interf; $i++)
        {
          if($interf_gps -> SeleccionInterfaz($interf_gps -> cod_operad[$i][0]));
          $zonaxxl = $interf_gps -> getZonasxx($interf_gps -> cod_operad[$i][0],$GLOBALS[transp]);

          if($zonaxxl)
          {
            $formulario -> nueva_tabla();
            $formulario -> linea("OPERADORES GPS - HOMOLOGACION DE ZONAS :: ".$interf_gps -> nom_operad[$i][0]." ::",1,"t2");

            $formulario -> nueva_tabla();
            $formulario -> linea("C&oacute;digo",0,"t");
            $formulario -> linea("Nombre",0,"t");
            $formulario -> linea("Min - Origen",0,"t");
            $formulario -> linea("",0,"t");
            $formulario -> linea("Homologar >",1,"t");

            for($j = 0; $j < sizeof($matriz2); $j++)
            {
             $zona_homolo = $interf_gps -> getHomozonasxx($interf_gps -> cod_operad[$i][0],$GLOBALS[transp],$matriz2[$j][0]);

             if($zona_homolo)
              $zonapcon = array_merge($zona_homolo,$inicio,$zonaxxl);
             else
              $zonapcon = array_merge($inicio,$zonaxxl);

             if($matriz2[$j][0] == CONS_CODIGO_PCLLEG)
              $codpc = "-";
             else
              $codpc = $matriz2[$j][0];

             $formulario -> linea($codpc,0,"i");
             $formulario -> linea($matriz2[$j][1],0,"i");
             $formulario -> linea($matriz2[$j][2],0,"i");

             $formulario -> oculto("contro_cod[$k]",$matriz2[$j][0],0);
             $formulario -> oculto("contro_nom[$k]",$matriz2[$j][1],0);
             $formulario -> oculto("operador_gps[$k]",$interf_gps -> cod_operad[$i][0],0);
             $formulario -> oculto("nom_operador_gps[$k]",$interf_gps -> nom_operad[$i][0],0);
             $formulario -> lista("", "zona[$k]", $zonapcon, 1);
             $k++;
            }//fin for
          }
          else
          {
           $formulario -> nueva_tabla();
           $formulario -> linea("<center>No Existe un Listado de Zonas GPS en el Sistema Para Esta Transportadora</center>",1,"e");
           $indbot = 0;
          }
         }
    }
    else
    {
     $formulario -> nueva_tabla();
     $formulario -> linea("<center>No Existe Ninguna Interfaz Activa de la Transportadora con Operadores GPS en el Sistema.</center>",1,"e");
     $indbot = 0;
    }
   }
   else
   {
    $formulario -> nueva_tabla();
    $formulario -> linea("<center>La Transportadora Contiene Interfaz Aplicaciones SAT Activa. Las Homologaciones Solo Se Realizar&aacute;n Desde la Aplicaci&oacute;n del Cliente.</center>",1,"e");
    $indbot = 0;
   }
   }

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("maximo","".sizeof($matriz2)."",0);
   $formulario -> oculto("opcion",$GLOBALS[opcion],0);
   $formulario -> oculto("ruta",$GLOBALS[ruta],0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

   if($GLOBALS[transp] && $GLOBALS[transp] != "0" && $indbot)
   {
    $formulario -> boton("Aceptar","button\" onClick=\"if(confirm('Desea Actualizar la Homologacion de los Puestos de Control')){form_ins.opcion.value = 3; form_ins.submit();}",0);
    $formulario -> boton("Restaurar","reset",1);
   }
   $formulario -> cerrar();
 }

 function Insertar()
 {
  $fec_actual = date("Y-m-d H:i:s");

  $zona = array_merge($GLOBALS[zona]);
  $operador = array_merge($GLOBALS[operador_gps]);
  $nom_operador = array_merge($GLOBALS[nom_operador_gps]);
  $contro_cod = array_merge($GLOBALS[contro_cod]);
  $contro_nom = array_merge($GLOBALS[contro_nom]);

  $query = "SELECT a.abr_tercer
              FROM ".BASE_DATOS.".tab_tercer_tercer a
             WHERE a.cod_tercer = '".$GLOBALS[transp]."'
           ";

  $consec = new Consulta($query, $this -> conexion);
  $transpor = $consec -> ret_matriz();

  $insercion = new Consulta("START TRANSACTION", $this -> conexion);

  //Manejo de la Interfaz GPS
    /*$interf_gps = new Interfaz_GPS();
    $interf_gps -> Interfaz_GPS_conexion($this -> conexion,BASE_DATOS,$GLOBALS[usuario]);

    if($operador)
    {
        for($i = 0; $i < sizeof($contro_cod); $i++)
            {
         if($contro_cod[$i] != "0")
         {
          if($interf_gps -> actHomoloZonasxx($operador[$i],$GLOBALS[transp],$zona[$i],$contro_cod[$i]))
          {
            $mensaje_gps .= "<br><img src=\"../sadc_standa/imagenes/ok.gif\">El Puesto de Control <b>".$contro_nom[$i]."</b> Se Homologo en Interfaz GPS :: <b>".$nom_operador[$i]."</b> :: Para :: <b>".$transpor[0][0]."</b> ::";
          }
          else
          {
             $mensaje_gps .= "<br><img src=\"../sadc_standa/imagenes/advertencia.gif\">El Puesto de Control <b>".$contro_nom[$i]."</b> no se Homologo en Interfaz GPS :: <b>".$nom_operador[$i]."</b> :: Para :: <b>".$transpor[0][0]."</b> ::";
          }
         }
        }
    }
*/
  if(!$insercion = new Consulta("COMMIT", $this -> conexion));
  else
  {
          $mensaje =  "".$mensaje_gps."";
          $mens = new mensajes();
          $mens -> correcto("HOMOLOGACION ZONAS GPS", $mensaje);
  }
 }

}//FIN CLASE PROC_RUTAS

   $proceso = new Proc_rutas($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>