<?php
/*! \file: cop_rutas_rutas.php
 *  \brief: Copia Rutas
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
 *  \brief: Copia Rutas
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
        case "2":
          $this -> Resultado();
          break;
        case "3":
          IncludeJS("ina_rutax_rutax.js");
          $this -> Datos();
          break;
        case "4":
          $this -> Update();
          break;
      }
    }
  }

  /*! \fn: Buscar
   *  \brief: Formulario para buscar rutas
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
    $formulario -> radio("ACTIVAS","tipter",1,1,0);
    $formulario -> radio("INACTIVAS","tipter",2,0,1);

    $formulario -> oculto("cod_ciuori\" id=\"cod_ciuoriID", $_REQUEST[cod_ciuori], 0);
    $formulario -> oculto("cod_ciudes\" id=\"cod_ciudesID", $_REQUEST[cod_ciudes], 0);
    $formulario -> texto( "Origen:", "text", "origen\" id=\"origenID", 0, 40, 40, "", $_REQUEST[origen], 0, 0, 0, 0 );
    $formulario -> texto( "Destino:", "text", "destino\" id=\"destinoID", 1, 40, 40, "", $_REQUEST[destino], 0, 0, 0, 0 );

    $formulario -> nueva_tabla();
    $formulario -> linea("Buscar por Nombre de Ruta",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> texto ("Ruta","text","ruta\" id=\"rutaID",1,50,255, 0, $_REQUEST[ruta]);

    $formulario -> nueva_tabla();
    $formulario -> oculto("opcion",2,0);
    $formulario -> oculto("standa\" id=\"standaID", DIR_APLICA_CENTRAL, 0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
    $formulario -> botoni("Buscar","form_list.submit()",0);
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

    $query = "SELECT a.cod_rutasx,a.nom_rutasx,Count(d.cod_contro),
    a.ind_estado
    FROM ".BASE_DATOS.".tab_genera_rutasx a LEFT JOIN
    ".BASE_DATOS.".tab_genera_rutcon d ON
    a.cod_rutasx = d.cod_rutasx
    ";
    $indwhere = 0;

    if($GLOBALS[tipter])
    {
      if($GLOBALS[tipter] == COD_ESTADO_ACTIVO )
      {
        $query .= " WHERE a.ind_estado = ".COD_ESTADO_ACTIVO." ";
      }
      else
      {
        $query .= " WHERE a.ind_estado != ".COD_ESTADO_ACTIVO." ";
      }
    }

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

    //PARA EL FILTRO DE EMPRESA
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);
    if($filtro -> listar($this -> conexion))
    {
      $datos_filtro = $filtro -> retornar();

      if($indwhere)
        $query = $query . " AND a.cod_rutasx IN( SELECT cod_rutasx FROM ".BASE_DATOS.".tab_genera_ruttra WHERE cod_transp =  '$datos_filtro[clv_filtro]' GROUP BY cod_rutasx ) ";
      else
      {
        $query = $query . " AND a.cod_rutasx IN( SELECT cod_rutasx FROM ".BASE_DATOS.".tab_genera_ruttra WHERE cod_transp =  '$datos_filtro[clv_filtro]' GROUP BY cod_rutasx ) ";
        $indwhere = 1;
      }
    }
    $query .= " GROUP BY 1 ORDER BY 2";

    $consec = new Consulta($query, $this -> conexion);
    $matriz = $consec -> ret_matriz();

    for($i=0;$i<sizeof($matriz);$i++)
      $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&ruta=".$matriz[$i][0]."&opcion=3&tipter=$GLOBALS[tipter] \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

    $formulario = new Formulario ("index.php","post","LISTADO DE RUTAS","form_item");

    $formulario -> nueva_tabla();
    $formulario -> linea("Se Encontrar&oacute;n un Total de ".sizeof($matriz)." Rutas.",0,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("C&oacute;digo",0,"t");
    $formulario -> linea("Nombre",0,"t");
    $formulario -> linea("Cant. P/C",0,"t");
    $formulario -> linea("Estado",1,"t");

    for($i = 0; $i < sizeof($matriz); $i++)
    {
      if($matriz[$i][3] != COD_ESTADO_ACTIVO)
      {
        $estilo = "ie";
        $estado = "Inactivo";
      }
      else
      {
        $estilo = "i";
        $estado = "Activo";
      }
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
    $formulario -> oculto("valor",$valor,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
    $formulario -> cerrar();
  }

  function Datos()
  {
    $query = " SELECT a.cod_rutasx,a.nom_rutasx,a.cod_ciuori,a.cod_ciudes,d.cod_contro,
                      if(e.ind_virtua = '1',CONCAT(e.nom_contro,' (Virtual)'),e.nom_contro),
                      d.val_duraci,e.nom_encarg,e.dir_contro,e.tel_contro,
                      if(a.ind_estado = '1','Activa','Inactiva'),e.ind_estado,d.ind_estado,
                      if(ind_urbano = '".COD_ESTADO_ACTIVO."',' - (Urbano)','')
                 FROM ".BASE_DATOS.".tab_genera_rutasx a,
                      ".BASE_DATOS.".tab_genera_rutcon d,
                      ".BASE_DATOS.".tab_genera_contro e
                WHERE a.cod_rutasx = d.cod_rutasx AND
                      d.cod_contro = e.cod_contro AND
                      a.cod_rutasx = '$GLOBALS[ruta]'
             ORDER BY 7 ";
    $consec = new Consulta($query, $this -> conexion);
    $matriz = $consec -> ret_matriz();

    $usuario = $_SESSION[datos_usuario][cod_usuari];
    $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
    $origen = $objciud -> getSeleccCiudad($matriz[0][2]);
    $destino = $objciud -> getSeleccCiudad($matriz[0][3]);

    $formulario = new Formulario ("index.php","post","DETALLE DE LA RUTA","form_item");
    $formulario -> linea("Datos de la Ruta ",0,"t2");

    $formulario -> oculto("num_ruta",$matriz[0][0],0);
    $formulario -> oculto("nom_ruta",$matriz[0][1],0);
    $formulario -> oculto("tipter",$GLOBALS[tipter],0);

    $formulario -> nueva_tabla();
    $formulario -> linea("C&oacute;digo",0,"t");
    $formulario -> linea($matriz[0][0],0,"i");
    $formulario -> linea("Ruta",0,"t");
    $formulario -> linea($matriz[0][1],1,"i");
    $formulario -> linea("Origen",0,"t");
    $formulario -> linea($origen[0][1],0,"i");
    $formulario -> linea("Destino",0,"t");
    $formulario -> linea($destino[0][1],1,"i");
    $formulario -> linea("Estado",0,"t");
    $formulario -> linea($matriz[0][10],0,"i");

    $formulario -> linea("",0,"t");
    $formulario -> linea("",1,"i");


    $formulario -> nueva_tabla();
    $formulario -> linea("Puestos de Control",0,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("C&oacute;digo",0,"t");
    $formulario -> linea("Puestos de Control",0,"t");
    $formulario -> linea("Minutos al Origen",0,"t");
    $formulario -> linea("Encargado",0,"t");
    $formulario -> linea("Direcci&oacute;n",0,"t");
    $formulario -> linea("Tel&eacute;fono",0,"t");
    $formulario -> linea("Estado",1,"t");

    for($i = 0; $i < sizeof($matriz); $i++)
    {
      if($matriz[$i][11] != COD_ESTADO_ACTIVO || $matriz[$i][12] != COD_ESTADO_ACTIVO)
        $estilo = "ie";
      else
        $estilo = "i";

      $estado = "Activo";

      if($matriz[$i][11] == COD_ESTADO_INACTI || $matriz[$i][12] == COD_ESTADO_INACTI)
        $estado = "Inactivo";

      if($matriz[$i][4] == CONS_CODIGO_PCLLEG)
        $formulario -> linea("-",0,$estilo);
      else
        $formulario -> linea($matriz[$i][4],0,$estilo);

      $formulario -> linea($matriz[$i][5].$matriz[$i][13],0,$estilo);
      $formulario -> linea($matriz[$i][6],0,$estilo);
      $formulario -> linea($matriz[$i][7],0,$estilo);
      $formulario -> linea($matriz[$i][8],0,$estilo);
      $formulario -> linea($matriz[$i][9],0,$estilo);
      $formulario -> linea($estado,1,$estilo);
    }

    $formulario -> nueva_tabla();


    $formulario -> nueva_tabla();


    $formulario -> nueva_tabla();
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("opcion",4,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

    if($GLOBALS[tipter] == 1)
      $formulario -> boton("Inactivar","button\" onClick=\"Valida_Inactivar();",0);
    else
      $formulario -> boton("Activar","button\" onClick=\"Valida_Inactivar();",0);

    //$formulario -> boton("Inactivar","button\" onClick=\"Valida_Inactivar();",0);

    $formulario -> boton("Volver","button\" onClick=\"javascript:history.go(-2);",0);
    $formulario -> cerrar();
  }

  function Update()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    $cod_ruta = $_REQUEST[num_ruta];
    $nom_ruta = $_REQUEST[nom_ruta];
    $ind_activo = $_REQUEST[tipter];

    if($ind_activo == 1)
      $ind_activo = 0;
    else
      $ind_activo = 1;

    $mSql = "UPDATE ".BASE_DATOS.".tab_genera_rutasx
                SET ind_estado = '$ind_activo',
                    usr_modifi = '$usuario',
                    fec_modifi = NOW()
              WHERE cod_rutasx = '$cod_ruta'";
    $update = new Consulta($mSql, $this -> conexion,"BR");

    $msg = $ind_activo == 1 ? "Activó" : "Inactivó";
    $msg2 = $ind_activo == 1 ? "Activar" : "Inactivar";

    if($consulta = new Consulta ("COMMIT", $this -> conexion))
    {
      $link = "<b><a href=\"index.php?window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">".$msg2." Otra Ruta</a></b>";

      $mensaje = "La Ruta No. <b>". $cod_ruta.": ".$nom_ruta."</b> se ".$msg." con Éxito<br>".$link;
      $mens = new mensajes();
      $mens -> correcto($msg2." Rutas",$mensaje);
    }
  }

}//FIN CLASE PROC_RUTAS

$proceso = new Proc_rutas($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>