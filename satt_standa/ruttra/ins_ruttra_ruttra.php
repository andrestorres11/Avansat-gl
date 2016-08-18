<?php
/*! \file: ins_ruttra_ruttra.php
 *  \brief: Asigna rutas a transportadoras
 *  \author: 
 *  \author: 
 *  \version: 1.0
 *  \date: dia/mes/año
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

/*! \class: Proc_rutas
 *  \brief: Asigna rutas a transportadoras
 */
class Proc_rutas
{
  private static $cRutas;
  var $conexion,
      $cod_aplica,
      $usuario;

  function __construct($co, $us, $ca)
  {
    @include_once("../".DIR_APLICA_CENTRAL."/lib/general/functions.inc");
    @include_once("../".DIR_APLICA_CENTRAL."/rutas/class_rutasx_rutasx.php");

    Proc_rutas::$cRutas = new rutas( $co, $us, $ca );
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;

    IncludeJS("jquery.js");
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";

    if(!isset($GLOBALS[opcion])){
      IncludeJS("rutas.js");
      $this -> Buscar();
    }
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

  /*! \fn: Buscar
   *  \brief: Formulario
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 10/08/2015
   *  \modified by: Ing. Fabian Salinas
   *  \param: 
   *  \return:
   */
  function Buscar()
  {
    $formulario = new Formulario ("index.php","post","RUTAS","form_list");

    $formulario -> nueva_tabla();
    $formulario -> linea("Especifique las Condiciones de B&uacute;squeda",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> oculto("cod_ciuori\" id=\"cod_ciuoriID", $_REQUEST[cod_ciuori], 0);
    $formulario -> oculto("cod_ciudes\" id=\"cod_ciudesID", $_REQUEST[cod_ciudes], 0);
    $formulario -> texto( "Origen:", "text", "origen\" id=\"origenID", 0, 40, 40, "", $_REQUEST[origen], 0, 0, 0, 1 );
    $formulario -> texto( "Destino:", "text", "destino\" id=\"destinoID", 1, 40, 40, "", $_REQUEST[destino], 0, 0, 0, 1 );

    $formulario -> nueva_tabla();
    $formulario -> linea("Buscar por Nombre de Ruta",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> texto ("Ruta","text","ruta\" id=\"rutaID",1,50,255, 0, $_REQUEST[ruta]);

    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar","form_list.submit()",0);

    $formulario -> oculto("window\" id=\"windowID", "central", 0);
    $formulario -> oculto("standa\" id=\"standaID", DIR_APLICA_CENTRAL, 0);
    $formulario -> oculto("opcion\" id=\"opcionID", 1, 0);
    $formulario -> oculto("cod_servic\" id=\"cod_servicID", $_REQUEST['cod_servic'], 0);
    $formulario -> cerrar();
  }

  /*! \fn: Resultado
   *  \brief: Resultado de la busqueda
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function Resultado()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    $query = "SELECT a.cod_rutasx,a.nom_rutasx,Count(d.cod_contro),a.ind_estado
                FROM ".BASE_DATOS.".tab_genera_rutasx a,
                     ".BASE_DATOS.".tab_genera_rutcon d
               WHERE a.cod_rutasx = d.cod_rutasx ";

    $indwhere = 0;

    if($GLOBALS[ruta] != "")
    {
      if($indwhere)
        $query .= " AND a.nom_rutasx LIKE '%".$GLOBALS[ruta]."%'";
      else
      {
        $query .= " AND a.nom_rutasx LIKE '%".$GLOBALS[ruta]."%'";
        $indwhere = 1;
      }
    }

    if($_REQUEST[cod_ciuori])
    {
      if($indwhere)
        $query .= " AND a.cod_ciuori = '".$_REQUEST[cod_ciuori]."'";
      else
      {
        $query .= " AND a.cod_ciuori = '".$_REQUEST[cod_ciuori]."'";
        $indwhere = 1;
      }
    }

    if($_REQUEST[cod_ciudes])
    {
      if($indwhere)
        $query .= " AND a.cod_ciudes = '".$_REQUEST[cod_ciudes]."'";
      else
      {
        $query .= " AND a.cod_ciudes = '".$_REQUEST[cod_ciudes]."'";
        $indwhere = 1;
      }
    }

    $query .= " GROUP BY 1 ORDER BY 2";

    $consec = new Consulta($query, $this -> conexion);
    $matriz = $consec -> ret_matriz();


    $formulario = new Formulario ("index.php","post","LISTADO DE RUTAS","form_item");

    $formulario -> nueva_tabla();
    $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Ruta(s).",0,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("C&oacute;digo",0,"t");
    $formulario -> linea("Nombre",0,"t");
    $formulario -> linea("Cant. P/C",0,"t");
    $formulario -> linea("Estado",1,"t");

    for($i=0;$i<sizeof($matriz);$i++)
    {
      if($matriz[$i][3] != COD_ESTADO_ACTIVO)
        $estilo = "ie";
      else
        $estilo = "i";

      if($matriz[$i][3] == COD_ESTADO_ACTIVO)
        $estado = "Activo";
      elseif($matriz[$i][3] == COD_ESTADO_INACTI)
        $estado = "Inactivo";

      $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&ruta=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

      $formulario -> linea($matriz[$i][0],0,$estilo);
      $formulario -> linea($matriz[$i][1],0,$estilo);
      $formulario -> linea($matriz[$i][2],0,$estilo);
      $formulario -> linea($estado,1,$estilo);
    }

    $formulario -> nueva_tabla();
    $formulario -> boton("Volver","button\" onClick=\"javascript:history.go(-1)",0);

    $formulario -> nueva_tabla();
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("opcion",2,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
    $formulario -> cerrar();
  }

  /*! \fn: Datos
   *  \brief: Formulario con datos a guardar
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function Datos()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    $inicio[0][0] = 0;
    $inicio[0][1] = "-";

    $query = "SELECT a.cod_rutasx,a.cod_ciuori,a.cod_ciudes,a.nom_rutasx
                FROM ".BASE_DATOS.".tab_genera_rutasx a
               WHERE a.cod_rutasx = ".$GLOBALS[ruta]."
            ORDER BY 2 ";
    $consec = new Consulta($query, $this -> conexion);
    $matriz = $consec -> ret_matriz();

    //transportadoras
    $query = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) AS abr_tercer
                FROM ".BASE_DATOS.".tab_tercer_tercer a,
                     ".BASE_DATOS.".tab_tercer_activi b 
               WHERE a.cod_tercer = b.cod_tercer 
                 AND b.cod_activi = ".COD_FILTRO_EMPTRA."
                 AND a.cod_tercer NOT IN( SELECT c.cod_transp FROM ".BASE_DATOS.".tab_genera_ruttra c WHERE c.cod_rutasx = ".$GLOBALS[ruta]." ) ";

    if($datos_usuario["cod_perfil"] == "")
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    else
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);

    if($filtro -> listar($this -> conexion))
    {
      $datos_filtro = $filtro -> retornar();
      $query .= " AND c.cod_transp = ".$datos_filtro[clv_filtro];
    }

    $query .= " GROUP BY 1 ORDER BY 2";

    $consulta = new Consulta($query, $this -> conexion);
    $transpors = $consulta -> ret_matriz();

    $transpors = array_merge($inicio,$transpors);

    //trae los puestos de control de la ruta
    $query = "SELECT e.nom_contro,d.val_duraci,e.nom_encarg,e.dir_contro,
                     e.tel_contro,d.cod_contro
                FROM ".BASE_DATOS.".tab_genera_rutasx a,
                     ".BASE_DATOS.".tab_genera_rutcon d,
                     ".BASE_DATOS.".tab_genera_contro e
               WHERE a.cod_rutasx = d.cod_rutasx AND
                     d.cod_contro = e.cod_contro AND
                     a.cod_rutasx = ".$GLOBALS[ruta]."
            ORDER BY 2 ";
    $consec = new Consulta($query, $this -> conexion);
    $matriz2 = $consec -> ret_matriz();

    $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
    $origen = $objciud -> getSeleccCiudad($matriz[0][1]);
    $destino = $objciud -> getSeleccCiudad($matriz[0][2]);

    IncludeJS("ruttra.js");
    $formulario = new Formulario ("index.php","post","ASIGNAR RUTA A TRANSPORTADORA","form_ins");

    $formulario -> nueva_tabla();
    $formulario -> linea("Informaci&oacute;n B&aacute;sica de la Ruta",0,"t2");

    $nombre_des = explode("VIA ",$matriz[0][3]);

    if($nombre_des[1])
      $matriz[0][3] = $nombre_des[1];

    $formulario -> nueva_tabla();
    $formulario -> linea("C&oacute;digo",0,"t");
    $formulario -> linea($matriz[0][0],1,"i");
    $formulario -> linea("Origen",0,"t");
    $formulario -> linea($origen[0][1],1,"i");
    $formulario -> linea("Destino",0,"t");
    $formulario -> linea($destino[0][1],1,"i");
    $formulario -> linea("Via",0,"t");
    $formulario -> linea($matriz[0][3],1,"i");

    $formulario -> nueva_tabla();
    $formulario -> linea("Selecci&oacute;n de la Transportadora",0,"t2");

    $formulario -> nueva_tabla();

    if($transpors)
    {
      $formulario -> lista("Transportadora:", "transpor", $transpors, 0);

      $formulario -> nueva_tabla();
      $formulario -> linea("Puestos de Control",1,"t2");

      $formulario -> nueva_tabla();
      $formulario -> linea("",0,"t");
      $formulario -> linea("",0,"t");
      $formulario -> linea("Nombre",0,"t","20%");
      $formulario -> linea("Min - Origen",0,"t","20%");
      $formulario -> linea("Encargado",0,"t","20%");
      $formulario -> linea("Direcci&oacute;n",0,"t","20%");
      $formulario -> linea("T&eacute;lefono",1,"t","20%");

      $asignar = 1;

      for($i=0;$i<sizeof($matriz2);$i++)
      {
        if($matriz2[$i][5] == CONS_CODIGO_PCLLEG)
        {
          $formulario -> caja("","asigna[$i]\" disabled ", "".$matriz2[$i][5]."", 1,0);
          $formulario -> oculto("asigna[$i]",$matriz2[$i][5],0);
        }
        else
          $formulario -> caja("","asigna[$i]",$matriz2[$i][5],1,0);

        $formulario -> linea($matriz2[$i][0],0,"i");
        $formulario -> linea($matriz2[$i][1],0,"i");
        $formulario -> linea($matriz2[$i][2],0,"i");
        $formulario -> linea($matriz2[$i][3],0,"i");
        $formulario -> linea($matriz2[$i][4],1,"i");
      }

      $formulario -> nueva_tabla();
      $formulario -> oculto("maximo","".sizeof($matriz2)."",0);
      $formulario -> oculto("opcion",$GLOBALS[opcion],0);
      $formulario -> oculto("ruta",$GLOBALS[ruta],0);
      $formulario -> oculto("window","central",0);
      $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
      $formulario -> boton("Asignar","button\" onClick=\"aceptar_ins2()",1);
    }
    else
      $formulario -> linea("Esta Ruta se Encuentra Asignada a Todas las Transportadoras Disponibles.",1,"e");

    $formulario -> cerrar();
  }

  /*! \fn: Insertar
   *  \brief: Guarda asignacion
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function Insertar()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario = $datos_usuario["cod_usuari"];

    $fec_actual = date("Y-m-d H:i:s");
    $asigna = $GLOBALS[asigna];

    $query = "SELECT a.abr_tercer
                FROM ".BASE_DATOS.".tab_tercer_tercer a
               WHERE a.cod_tercer = '".$GLOBALS[transpor]."' ";
    $consulta = new Consulta($query, $this -> conexion,"BR");
    $nomtra = $consulta -> ret_matriz();

    for($i = 0; $i < $GLOBALS[maximo]; $i++)
    {
      if($asigna[$i] != Null )
      {
        $query = "INSERT INTO ".BASE_DATOS.".tab_genera_ruttra
                          ( cod_rutasx,cod_contro,cod_transp,
                            usr_creaci,fec_creaci)
                   VALUES ( ".$GLOBALS[ruta].",".$asigna[$i].",'".$GLOBALS[transpor]."',
                            '".$usuario."','".$fec_actual."') ";
        $insercion = new Consulta($query, $this -> conexion,"R");
      }
    }

    if($insercion = new Consulta("COMMIT", $this -> conexion))
    {
      $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Asignar Otra Transportadora</a></b>";

      $mensaje =  "La Transportadora <b>".$nomtra[0][0]."</b> Se Asigno a la Ruta con Exito".$link_a;
      $mens = new mensajes();
      $mens -> correcto("ASIGNAR TRANSPORTADORA",$mensaje);
    }
  }

}//FIN CLASE PROC_RUTAS

$proceso = new Proc_rutas($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>