<?php
/*! \file: lis_ruttra_ruttra.php
 *  \brief: Lista rutas y las transportadoras asociadas a las rutas
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
 *  \brief: Lista rutas y las transportadoras asociadas a las rutas
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

    if(!isset($_REQUEST[opcion])){
      IncludeJS("rutas.js");
      $this -> Buscar();
    }
    else
    {
      switch($_REQUEST[opcion])
      {
        case "2":
          $this -> Resultado();
          break;
        case "3":
          $this -> Datos();
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
    $datos_usuario = $this -> usuario -> retornar();

    $formulario = new Formulario ("index.php","post","RUTAS ASIGNADAS","form_list");

    $formulario -> nueva_tabla();
    $formulario -> linea("Especifique las Condiciones de B&uacute;squeda",1,"t2");

    $formulario -> nueva_tabla();

    if($datos_usuario["cod_perfil"] == "")
      $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
    else
      $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);

    if($filtro -> listar($this -> conexion))
    {
      $datos_filtro = $filtro -> retornar();
      $formulario -> oculto("cod_transp\" id=\"cod_transpID",$datos_filtro[clv_filtro],0);
    }
    else
    {
      if( $_REQUEST[cod_transp] )
        $mTransp[0] = array("cod_tercer"=>$_REQUEST[cod_transp], "abr_tercer"=>$_REQUEST[transp]);
      else
      {
        $query = "SELECT a.cod_tercer,a.abr_tercer
                    FROM ".BASE_DATOS.".tab_tercer_tercer a,
                         ".BASE_DATOS.".tab_tercer_activi b
                   WHERE a.cod_tercer = b.cod_tercer AND
                         b.cod_activi = ".COD_FILTRO_EMPTRA." AND
                         a.cod_tercer = '".$_REQUEST[transp]."'
                ORDER BY 2 ";
        $consulta = new Consulta($query, $this -> conexion);
        $mTransp = $consulta -> ret_matrix('a');
      }

      $formulario -> oculto("cod_transp\" id=\"cod_transpID", $mTransp[0][cod_tercer], 0);
      $formulario -> texto( "Transportadora:", "text", "transp\" id=\"transpID", 1, 40, 40, "", $mTransp[0][abr_tercer], 0, 0, 0, 0 );
    }

    $formulario -> oculto("cod_ciuori\" id=\"cod_ciuoriID", $_REQUEST[cod_ciuori], 0);
    $formulario -> texto( "Origen:", "text", "origen\" id=\"origenID", 1, 40, 40, "", $_REQUEST[origen], 0, 0, 0, 0 );
    $formulario -> oculto("cod_ciudes\" id=\"cod_ciudesID", $_REQUEST[cod_ciudes], 0);
    $formulario -> texto( "Destino:", "text", "destino\" id=\"destinoID", 1, 40, 40, "", $_REQUEST[destino], 0, 0, 0, 0 );

    $formulario -> nueva_tabla();
    $formulario -> linea("Buscar por Nombre de Ruta",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> texto ("Ruta","text","ruta\" id=\"rutaID",1,50,255, 0, $_REQUEST[ruta]);

    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar","form_list.submit()",0);

    $formulario -> oculto("window\" id=\"windowID", "central", 0);
    $formulario -> oculto("standa\" id=\"standaID", DIR_APLICA_CENTRAL, 0);
    $formulario -> oculto("opcion\" id=\"opcionID", 2, 0);
    $formulario -> oculto("cod_servic\" id=\"cod_servicID", $_REQUEST['cod_servic'], 0);
    $formulario -> cerrar();
  }

  /*! \fn: Resultado
   *  \brief: Resultado de la busqueda
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified:
   *  \param: 
   *  \return:
   */
  function Resultado()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    $query = "SELECT a.cod_rutasx,a.nom_rutasx,a.ind_estado
                FROM ".BASE_DATOS.".tab_genera_rutasx a,
                     ".BASE_DATOS.".tab_genera_ruttra b
               WHERE a.cod_rutasx = b.cod_rutasx ";

    if($_REQUEST[cod_transp])
      $query .= " AND b.cod_transp = '".$_REQUEST[cod_transp]."'";
    if($_REQUEST[ruta])
      $query .= " AND a.nom_rutasx LIKE '%".$_REQUEST[ruta]."%'";
    if($_REQUEST[cod_ciuori])
      $query .= " AND a.cod_ciuori = ".$_REQUEST[cod_ciuori]."";
    if($_REQUEST[cod_ciudes])
      $query .= " AND a.cod_ciudes = ".$_REQUEST[cod_ciudes]."";

    $query .= " GROUP BY 1 ORDER BY 2";

    $consec = new Consulta($query, $this -> conexion);
    $matriz = $consec -> ret_matriz();

    $formulario = new Formulario ("index.php","post","LISTADO DE RUTAS","form_item");

    $formulario -> nueva_tabla();
    $formulario -> linea("Se Encontro un Total de ".sizeof($matriz)." Ruta(s).",0,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("C&oacute;digo",0,"t");
    $formulario -> linea("Nombre",0,"t");
    $formulario -> linea("Estado",1,"t");

    for($i = 0; $i < sizeof($matriz); $i++)
    {
      $estilo = $matriz[$i][2] != COD_ESTADO_ACTIVO ? "ie" : "i";
      $estado = $matriz[$i][2] == COD_ESTADO_ACTIVO ? "Activo" : "Inactivo";

      $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&ruta=".$matriz[$i][0]."&trpt=".$_REQUEST[cod_transp]."&opcion=3 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

      $formulario -> linea($matriz[$i][0],0,$estilo);
      $formulario -> linea($matriz[$i][1],0,$estilo);
      $formulario -> linea($estado,1,$estilo);
    }

    $formulario -> nueva_tabla();
    $formulario -> boton("Volver","button\" onClick=\"javascript:history.go(-1)",0);

    $formulario -> nueva_tabla();
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("opcion",2,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
    $formulario -> cerrar();
  }

 function Datos()
 {
   $query = "SELECT a.cod_rutasx,a.cod_ciuori,a.cod_ciudes,a.nom_rutasx
               FROM ".BASE_DATOS.".tab_genera_rutasx a
              WHERE a.cod_rutasx = ".$_REQUEST[ruta]."
                    ORDER BY 2
            ";

   $consulta = new Consulta($query, $this -> conexion);
   $matriz = $consulta -> ret_matriz();

   $query = "SELECT a.cod_tercer,a.abr_tercer
   			   FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			        ".BASE_DATOS.".tab_genera_ruttra b
   			  WHERE a.cod_tercer = b.cod_transp AND
   			        b.cod_rutasx = ".$_REQUEST[ruta]."
   			";

   if($_REQUEST[trpt])
    $query .= " AND b.cod_transp = '".$_REQUEST[trpt]."'";

   $query .= " GROUP BY 1 ORDER BY 1";

   $consulta = new Consulta($query, $this -> conexion);
   $transpors = $consulta -> ret_matriz();

   $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
   $origen = $objciud -> getSeleccCiudad($matriz[0][1]);
   $destino = $objciud -> getSeleccCiudad($matriz[0][2]);

   $formulario = new Formulario ("index.php","post","DETALLE DE LA RUTA","form_item");
   $formulario -> linea("Informaci&oacute;n de la Ruta ",0,"t2");

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
   $formulario -> linea("Transportadora(s) Relacionada(s)",0,"t2");

   $formulario -> nueva_tabla();
   $formulario -> linea("Transportadora",0,"t");
   $formulario -> linea("Can. P/C",1,"t");

   for($i = 0; $i < sizeof($transpors); $i++)
   {
   	$query = "SELECT a.cod_rutasx,a.cod_transp,COUNT(a.cod_contro)
   			    FROM ".BASE_DATOS.".tab_genera_ruttra a
   			   WHERE a.cod_rutasx = ".$_REQUEST[ruta]." AND
   			         a.cod_transp = '".$transpors[$i][0]."'
   			         GROUP BY 1,2
   			 ";

   	$consulta = new Consulta($query, $this -> conexion);
    $totcon = $consulta -> ret_matriz();

    $formulario -> linea($transpors[$i][1],0,"i");
    $formulario -> linea($totcon[0][2],1,"i");
   }
   $formulario -> nueva_tabla();
   $formulario -> boton("Volver","button\" onClick=\"javascript:history.go(-1)",0);

   $formulario -> nueva_tabla();
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",3,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }

}//FIN CLASE PROC_RUTAS

     $proceso = new Proc_rutas($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>