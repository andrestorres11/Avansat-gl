<?php
/*! \file: eli_rutas_rutas.php
 *  \brief: Elimina Rutas
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
 *  \brief: Elimina Rutas
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
    IncludeJS("rutas.js");
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";

    if(!isset($_REQUEST[opcion]))
      $this -> Buscar();
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
        case "4":
          $this -> Eliminar();
          break;
      }
    }
  }

  /*! \fn: Buscar
   *  \brief: Formulario
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 05/08/2015
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
    $formulario -> texto( "Origen:", "text", "origen\" id=\"origenID", 0, 40, 40, "", $_REQUEST[origen], 0, 0, 0, 0 );
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

 function Resultado()
 {
   $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $query = "SELECT a.cod_rutasx,a.nom_rutasx,Count(d.cod_contro),
		    		a.ind_estado
               FROM ".BASE_DATOS.".tab_genera_rutasx a LEFT JOIN
                    ".BASE_DATOS.".tab_genera_rutcon d ON
                    a.cod_rutasx = d.cod_rutasx";
   $indwhere = 0;

   if($_REQUEST[ruta] != "")
   {
    if($indwhere)
     $query .= " AND a.nom_rutasx LIKE '%".$_REQUEST[ruta]."%'";

    else
    {
     $query .= " WHERE a.nom_rutasx LIKE '%".$_REQUEST[ruta]."%'";
     $indwhere = 1;
    }
   }

   if($_REQUEST[cod_ciuori])
   {
    if($indwhere)
     $query .= " AND a.cod_ciuori = '".$_REQUEST[cod_ciuori]."'";
    else
    {
     $query .= " WHERE a.cod_ciuori = '".$_REQUEST[cod_ciuori]."'";
     $indwhere = 1;
    }
   }

   if($_REQUEST[cod_ciudes])
   {
    if($indwhere)
     $query .= " AND a.cod_ciudes = '".$_REQUEST[cod_ciudes]."'";
    else
    {
     $query .= " WHERE a.cod_ciudes = '".$_REQUEST[cod_ciudes]."'";
     $indwhere = 1;
    }
   }

  $query .= " GROUP BY 1 ORDER BY 2";
  $consec = new Consulta($query, $this -> conexion);
  $matriz = $consec -> ret_matriz();

  for($i=0;$i<sizeof($matriz);$i++)
      $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&ruta=".$matriz[$i][0]."&opcion=3 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

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
     $estilo = "ie";
    else
     $estilo = "i";

    if($matriz[$i][3] == COD_ESTADO_ACTIVO)
     $estado = "Activo";
    else if($matriz[$i][3] == COD_ESTADO_INACTI)
     $estado = "Inactivo";

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
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
   $formulario -> cerrar();
 }

 function Datos()
 {
   $query = "SELECT a.cod_rutasx,a.nom_rutasx,a.cod_ciuori,a.cod_ciudes,d.cod_contro,
                    if(e.ind_virtua = '1',CONCAT(e.nom_contro,' (Virtual)'),e.nom_contro),
		   		    d.val_duraci,e.nom_encarg,e.dir_contro,e.tel_contro,
		   		    if(a.ind_estado = '1','Activa','Inactiva'),e.ind_estado,d.ind_estado,
        		    if(ind_urbano = '".COD_ESTADO_ACTIVO."',' - (Urbano)','')
               FROM ".BASE_DATOS.".tab_genera_rutasx a,
              	    ".BASE_DATOS.".tab_genera_rutcon d,
                    ".BASE_DATOS.".tab_genera_contro e
              WHERE a.cod_rutasx = d.cod_rutasx AND
                    d.cod_contro = e.cod_contro AND
                    a.cod_rutasx = '$_REQUEST[ruta]'
        		    ORDER BY 7
            ";

   $consec = new Consulta($query, $this -> conexion);
   $matriz = $consec -> ret_matriz();

   $query = "SELECT a.cod_rutasx
   		       FROM ".BASE_DATOS.".tab_genera_ruttra a
   		      WHERE a.cod_rutasx = ".$_REQUEST[ruta]."
   		    ";

   $consec = new Consulta($query, $this -> conexion);
   $existt = $consec -> ret_matriz();

   $query = "SELECT a.cod_rutasx
   		       FROM ".BASE_DATOS.".tab_despac_vehige a
   		      WHERE a.cod_rutasx = ".$_REQUEST[ruta]."
   		    ";

   $consec = new Consulta($query, $this -> conexion);
   $existd = $consec -> ret_matriz();

   if(!$existd)
   {
    $query = "SELECT a.cod_rutasx
   		        FROM ".BASE_DATOS.".tab_despac_seguim a
   		       WHERE a.cod_rutasx = ".$_REQUEST[ruta]."
   		     ";

    $consec = new Consulta($query, $this -> conexion);
    $existd = $consec -> ret_matriz();
   }

   $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
   $origen = $objciud -> getSeleccCiudad($matriz[0][2]);
   $destino = $objciud -> getSeleccCiudad($matriz[0][3]);

   $formulario = new Formulario ("index.php","post","DETALLE DE LA RUTA","form_item");
   $formulario -> linea("Datos de la Ruta ",0,"t2");

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

   //Manejo de la Interfaz Aplicaciones SAT
/*   $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$this -> usuario_aplicacion,$this -> conexion);

   if($interfaz -> totalact > 0)
    $formulario -> linea("El Sistema Tiene Interfases Activas Debe Homologar este P/C",1,"t2");

   for($i = 0; $i < $interfaz -> totalact; $i++)
   {
    $homolocon = $interfaz -> getHomoloTranspRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$_REQUEST[ruta]);

    if($homolocon["RUTAHomolo"] > 0)
    {
     $query = "SELECT a.cod_ciuori,a.cod_ciudes
		 FROM ".BASE_DATOS.".tab_genera_rutasx a
		WHERE a.cod_rutasx = ".$_REQUEST[ruta]."
	      ";

     $consec = new Consulta($query, $this -> conexion);
     $orides = $consec -> ret_matriz();

     $ruta_ws = $interfaz -> getListadRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$orides[0][0],$orides[0][1]);

     for($j = 0; $j < sizeof($ruta_ws); $j++)
     {
      if($homolocon["RUTAHomolo"] == $ruta_ws[$j]["rutasx"])
       $nomruthom[$i] = $ruta_ws[$j]["nombre"];
     }

     $formulario -> linea("La Ruta se Encuentra Homologada en la Interfaz ".$interfaz -> interfaz[$i]["nombre"]." :: ".$nomruthom[$i].".",1,"i");
    }
    else
     $formulario -> linea("La Ruta no se Encuentra Homologada en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].".",1,"i");
   }
*/
   $formulario -> nueva_tabla();
   $formulario -> oculto("ruta",$_REQUEST[ruta],0);
   $formulario -> oculto("usuario","$usuario",0);
   $formulario -> oculto("opcion",4,0);
   $formulario -> oculto("window","central",0);
   $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);

   if(!$existt && !$existd)
    $formulario -> botoni("Eliminar","if(confirm('Esta Seguro de Eliminar Esta Ruta?')){form_item.submit()}",1);
   else
    $formulario -> linea("Imposible Eliminar la Ruta, Esta se Encuentra Relacionada a Una Transportadora &oacute; a un Despacho.",1,"e");
   $formulario -> cerrar();
 }

 function Eliminar()
 {
  $query = "DELETE FROM ".BASE_DATOS.".tab_genera_rutasx
  		          WHERE cod_rutasx = ".$_REQUEST[ruta]."
  		   ";

  $consulta = new Consulta($query, $this -> conexion,"BR");

  if($consulta = new Consulta ("COMMIT", $this -> conexion))
  {
   $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Eliminar Otra Ruta</a></b>";

   $mensaje = "La Ruta se Elimino con Exito<br>".$link;
   $mens = new mensajes();
   $mens -> correcto("ELIMINAR RUTAS",$mensaje);
  }
 }

}//FIN CLASE PROC_RUTAS

     $proceso = new Proc_rutas($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>