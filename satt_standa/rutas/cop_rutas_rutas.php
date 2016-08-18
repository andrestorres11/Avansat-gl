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
      $usuario;

  function __construct($co, $us, $ca)
  {
    @include_once("../".DIR_APLICA_CENTRAL."/lib/general/functions.inc");
    @include_once("../".DIR_APLICA_CENTRAL."/rutas/class_rutasx_rutasx.php");

    Proc_rutas::$cRutas = new rutas( $co, $us, $ca );
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> cod_filtro = $cf;
    $datos_usuario = $this -> usuario -> retornar();
    
    IncludeJS("jquery.js");
    IncludeJS("rutas.js");
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";

    if(!isset($GLOBALS[opcion])){
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
          IncludeJS("ruta.js");
          $this -> Datos();
          break;
        case "3":
          $this -> Insertar();
          break;
      }
    }
  }

  /*! \fn: Buscar
   *  \brief: Formulario para buscar rutas
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
    $indwhere = 0;

    $query = "SELECT a.cod_rutasx,a.nom_rutasx,Count(d.cod_contro)
                FROM ".BASE_DATOS.".tab_genera_rutasx a 
           LEFT JOIN ".BASE_DATOS.".tab_genera_rutcon d 
                  ON a.cod_rutasx = d.cod_rutasx";

    if($GLOBALS[ruta] != "")
    {
      if($indwhere)
        $query .= " AND a.nom_rutasx LIKE '%".$GLOBALS[ruta]."%'";
      else
      {
        $query .= " WHERE a.nom_rutasx LIKE '%".$GLOBALS[ruta]."%'";
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
      $matriz[$i][0]= "<a href=\"index.php?cod_servic=$GLOBALS[cod_servic]&window=central&ruta=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

    $formulario = new Formulario ("index.php","post","LISTADO DE RUTAS","form_item");

    $formulario -> nueva_tabla();
    $formulario -> linea("Se Encontraron un Total de ".sizeof($matriz)." Rutas.",0,"t2");

    $formulario -> nueva_tabla();
    if(sizeof($matriz) > 0)
    {
      $formulario -> linea("C&oacute;digo",0,"t");
      $formulario -> linea("Nombre",0,"t");
      $formulario -> linea("Cant. P/C",1,"t");

      for($i=0;$i<sizeof($matriz);$i++)
      {
        $formulario -> linea($matriz[$i][0],0,"i");
        $formulario -> linea($matriz[$i][1],0,"i");
        $formulario -> linea($matriz[$i][2],1,"i");
      }//fin for
    }//fin if

    $formulario -> nueva_tabla();
    $formulario -> boton("Volver","button\" onClick=\"javascript:history.go(-1)",0);

    $formulario -> nueva_tabla();
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("opcion",2,0);
    $formulario -> oculto("valor",$valor,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);
    $formulario -> oculto("standa\" id=\"standaID", DIR_APLICA_CENTRAL, 0);
    $formulario -> cerrar();
  }

  /*! \fn: Datos
   *  \brief: Formulario para copiar ruta
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 10/08/2015
   *  \modified by: Ing. Fabian Salinas
   *  \param: 
   *  \return:
   */
  function Datos()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    $query = " SELECT a.cod_rutasx,a.nom_rutasx,a.cod_ciuori,a.cod_ciudes,d.cod_contro,
                      if(e.ind_virtua = '1',CONCAT(e.nom_contro,' (Virtual)'),e.nom_contro),
                      d.val_duraci,e.nom_encarg,e.dir_contro,e.tel_contro,
                      '','',d.val_duraci,d.val_distan,d.ind_estado,
                      a.ind_estado
                 FROM ".BASE_DATOS.".tab_genera_rutasx a,
                      ".BASE_DATOS.".tab_genera_rutcon d,
                      ".BASE_DATOS.".tab_genera_contro e
                WHERE a.cod_rutasx = d.cod_rutasx AND
                      d.cod_contro = e.cod_contro AND
                      a.cod_rutasx = '$GLOBALS[ruta]' AND
                      d.cod_contro != ".CONS_CODIGO_PCLLEG."
             ORDER BY 7";
    $consec = new Consulta($query, $this -> conexion);
    $matriz = $consec -> ret_matriz();

    if(!$GLOBALS[nom])
      $GLOBALS[nom] = "";

    #Ciudad Origen
    if( isset($_REQUEST[cod_ciuori]) )
      $mCiuOri[0] = array("cod_ciudad"=>$_REQUEST[cod_ciuori], "abr_ciudad"=>$_REQUEST[origen]);
    else
      $mCiuOri = Proc_rutas::$cRutas -> getCiudades( $matriz[0][cod_ciuori] );

    #Ciudad Destino
    if( isset($_REQUEST[cod_ciudes]) )
      $mCiuDes[0] = array("cod_ciudad"=>$_REQUEST[cod_ciudes], "abr_ciudad"=>$_REQUEST[destino]);
    else
      $mCiuDes = Proc_rutas::$cRutas -> getCiudades( $matriz[0][cod_ciudes] );

    if(isset($GLOBALS[asigna]) AND isset($GLOBALS[contro]) AND isset($GLOBALS[val]))
    {
      $asigna=$GLOBALS[asigna];
      $contro=$GLOBALS[cod_contro];
      $val   =$GLOBALS[val];
      $kil   =$GLOBALS[kil];
      $activo=$GLOBALS[activo];
    }//fin if
    else
    {
      for($i=0;$i<sizeof($matriz);$i++)
      {
        $asigna[$i]=1;
        $contro[$i]=$matriz[$i][4];
        $val[$i]   =$matriz[$i][12];
        $kil[$i]   =$matriz[$i][13];
        $activo[$i]=$matriz[$i][14];
      }//fin for
    }//fin else

    $mLimit = $GLOBALS[cont];
    if(!$GLOBALS[cont])
    {
      $GLOBALS[cont] = sizeof($matriz);
      $GLOBALS[cinicial] = sizeof($matriz);
    }else
      $GLOBALS[cont] = $_REQUEST[adican] + $GLOBALS[cont];

    $formulario = new Formulario ("index.php","post","DETALLE DE LA RUTA","form_item");

    $formulario -> nueva_tabla();
    $formulario -> linea("Informaci&oacute;n B&aacute;sica de la Ruta",0,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("C&oacute;digo Ruta Base",0,"t","25%",'',"right");
    $formulario -> linea($matriz[0][0],1,"i");

    #Ciudades
    $formulario -> oculto("cod_ciuori\" id=\"cod_ciuoriID", $mCiuOri[0][cod_ciudad], 0);
    $formulario -> texto( "Origen:", "text", "origen\" id=\"origenID", 1, 40, 40, "", $mCiuOri[0][abr_ciudad], 0, 0, 0, 1 );
    $formulario -> oculto("cod_ciudes\" id=\"cod_ciudesID", $mCiuDes[0][cod_ciudad], 0);
    $formulario -> texto( "Destino:", "text", "destino\" id=\"destinoID", 1, 40, 40, "", $mCiuDes[0][abr_ciudad], 0, 0, 0, 1 );

    $formulario -> texto ("Via","text","nom",1,60,255,"",$GLOBALS[nom]);

    $formulario -> nueva_tabla();
    $formulario -> linea("Puestos de Control",0,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("",0,"t");
    $formulario -> linea("S/N",0,"t");
    $formulario -> linea("",0,"t");

    $formulario -> linea("Puestos de Control",0,"t");
    $formulario -> linea("",0,"t");
    $formulario -> linea("Desde el Origen",0,"t");
    $formulario -> linea("",0,"t");
    $formulario -> linea("",1,"t");

    for($i=0;$i<$GLOBALS[cont];$i++)
    {
      if($asigna[$i] == 1)
        $formulario -> caja("","asigna[$i]", "1", 1,0);
      elseif($i >= $mLimit)
        $formulario -> caja("","asigna[$i]", "1", 1,0);
      else
        $formulario -> caja("","asigna[$i]", "1", 0,0);

      //Trae el puesto de control
      $query = " SELECT cod_contro,if(ind_virtua = '1',CONCAT(nom_contro,' (Virtual)'),nom_contro),
                        if(ind_urbano = '".COD_ESTADO_ACTIVO."',' - (Urbano)','')
                   FROM ".BASE_DATOS.".tab_genera_contro
                  WHERE cod_contro = '".$contro[$i]."'";
      $consulta = new Consulta($query, $this -> conexion);
      $control = $consulta -> ret_matriz();

      $formulario -> oculto("cod_contro[$i]\" id=\"cod_contro".$i."ID", $control[0][0], 0);
        echo '<td class="celda_info">
                <input type="text" maxlength="60" value="'.$control[0][1].'" nameID="cod_contro'.$i.'ID" size="60" id="controID['.$i.']" name="contro['.$i.']" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto">
              </td>';
      $formulario -> texto("Duraci&oacute;n (Min)","text","val[$i]",0,5,4,"","$val[$i]");
      $formulario -> texto("Distancia (K/m)","text","kil[$i]",1,6,6,"","$kil[$i]");
    }//fin for

    $query = " SELECT a.cod_contro, if(a.ind_virtua = '1',CONCAT(a.nom_contro,' (Virtual)'),a.nom_contro),
                      b.val_duraci,b.val_distan
                 FROM ".BASE_DATOS.".tab_genera_contro a 
            LEFT JOIN ".BASE_DATOS.".tab_genera_rutcon b 
                   ON a.cod_contro = b.cod_contro 
                  AND b.cod_rutasx = ".$GLOBALS[ruta]." 
                WHERE a.cod_contro = ".CONS_CODIGO_PCLLEG." ";

    $consulta = new Consulta($query, $this -> conexion);
    $ultcon = $consulta -> ret_matriz();

    if(!$GLOBALS[timepcult])
      $GLOBALS[timepcult] = $ultcon[0][2];

    if(!$GLOBALS[kilulti])
      $GLOBALS[kilulti] = $ultcon[0][3];

    $formulario -> caja("","asipcult\" disabled ", "1", 1,0);
    $formulario -> oculto("codpcult\" id=\"codpcultID", $ultcon[0][0], 0);
    echo '<td class="celda_info">
            <input type="text" maxlength="60" value="'.$ultcon[0][1].'" size="60" id="codipcultID" name="codipcult" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto" disabled>
          </td>';
    $formulario -> texto("Duraci&oacute;n (Min)","text","timepcult",0,5,4,"",$GLOBALS[timepcult]);
    $formulario -> texto("Distancia (K/m)","text","kilulti",1,6,6,"",$GLOBALS[kilulti]);

    $formulario -> nueva_tabla();

    //Manejo de la Interfaz Aplicaciones SAT
    /*
      $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$this -> usuario_aplicacion,$this -> conexion);

      $formulario -> nueva_tabla();

      if($interfaz -> totalact > 0)
      $formulario -> linea("El Sistema Tiene Interfases Activas Debe Homologar esta Ruta",1,"t2");
    */
    if($GLOBALS[copia])
      $copia = $GLOBALS[copia];

    if($GLOBALS[rutaint])
      $rutaint = $GLOBALS[rutaint];

    /*
      for($i = 0; $i < $interfaz -> totalact; $i++)
      {
      $formulario -> nueva_tabla();

      if($GLOBALS[origen] && $GLOBALS[destino])
      {
      $ruta_ws = $interfaz -> getListadRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$GLOBALS[origen],$GLOBALS[destino]);

      if($ruta_ws[0] == "-2")
      {
      $formulario -> linea("No Existen Rutas con el Operador ".$interfaz -> interfaz[$i]["nombre"]." Relacionados con el Origen y Destino Seleccionados.",0,"i");
      }
      else
      {
      for($j = 0; $j < sizeof($ruta_ws); $j++)
      {
      $ruta[$j][0] = $ruta_ws[$j]["rutasx"];
      $ruta[$j][1] = $ruta_ws[$j]["nombre"];

      if($rutaint[$i] == $ruta[$j][0])
      {
      $ruta_a[0][0] = $ruta[$j][0];
      $ruta_a[0][1] = $ruta[$j][1];
      }
      }

      $ruta = array_merge($inicio,$ruta);

      if(isset($rutaint[$i]) && $rutaint[$i] != "0")
      {
      $homolo = $interfaz -> getHomoloRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$rutaint[$i]);

      if($homolo["RUTAHomolo"] != -2)
      {
      echo "<div align = \"center\"><small><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\"><b>La Ruta Seleccionada Ya Se Encuentra Homologada en la Interfaz ".$interfaz -> interfaz[$i]["nombre"].".</small></div>";

      $ruta =array_merge($inicio,$ruta);
      }
      else
      $ruta = array_merge($ruta_a,$ruta);
      }

      $formulario -> lista("Rutas Con Interfaz ".$interfaz -> interfaz[$i]["nombre"].":", "rutaint[$i]\" onChange=\"form_item.submit() ", $ruta, 0);

      if($copia[$i])
      $chek = "checked";
      else
      $chek = "";

      $formulario -> caja("Plan de Ruta (S/N)","copia[$i]\" onClick=\"form_item.submit()",1,$chek,0);

      if($copia[$i])
      {
      $pcontro = $interfaz -> getPlanruRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$rutaint[$i]);

      $formulario -> nueva_tabla();
      $formulario -> linea("Nombre P/C",0,"h");
      $formulario -> linea("Minutos Origen",1,"h");

      for($j = 0; $j < sizeof($pcontro); $j++)
      {
      $formulario -> linea($pcontro[$j]["nombre"],0,"i");
      $formulario -> linea($pcontro[$j]["tiempo"],1,"i");
      }
      }

      $formulario -> nueva_tabla();
      $formulario -> oculto("operad[$i]",$interfaz -> interfaz[$i]["operad"],0);
      }
      }
      else
      $formulario -> linea("Debe Seleccionar Origen y Destino Para Realizar la Homologaci&oacute;n",1,"i");
      }
    */
    $formulario -> nueva_tabla();
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("cont","$GLOBALS[cont]",0);
    $formulario -> oculto("cinicial","$GLOBALS[cinicial]",0);
    $formulario -> oculto("opcion",$GLOBALS[opcion],0);
    $formulario -> oculto("standa\" id=\"standaID", DIR_APLICA_CENTRAL, 0);
    //$formulario -> oculto("maximo",$interfaz -> cant_interf,0);
    $formulario -> oculto("ruta",$GLOBALS[ruta],0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$GLOBALS[cod_servic],0);

    $formulario -> botoni("Insertar","aceptar_copia()",0);
    $formulario -> texto("Adicionar Puesto(s) de Control","text","adican\" onChange=\"form_item.submit()",0,2,2,"","");

    if($GLOBALS[cont] > $GLOBALS[cinicial])
      $formulario -> botoni("Borrar","form_item.cont.value--; form_item.submit();",1);

    $formulario -> cerrar();
  }

  /*! \fn: Insertar
   *  \brief: Insertar ruta copiada
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  function Insertar()
  {
    $fec_actual = date("Y-m-d H:i:s");

    //reasigna los valores

    $asigna=$GLOBALS[asigna];
    $contro=$_REQUEST[cod_contro];
    $val   =$GLOBALS[val];
    $kil   =$GLOBALS[kil];

    $query = "SELECT a.cod_paisxx,a.cod_depart
                FROM ".BASE_DATOS.".tab_genera_ciudad a
               WHERE a.cod_ciudad = ".$_REQUEST[cod_ciuori]." ";
    $consulta = new Consulta($query, $this -> conexion,"R");
    $paidepori = $consulta -> ret_matriz();

    $query = "SELECT a.cod_paisxx,a.cod_depart
                FROM ".BASE_DATOS.".tab_genera_ciudad a
               WHERE a.cod_ciudad = ".$_REQUEST[cod_ciudes]." ";
    $consulta = new Consulta($query, $this -> conexion,"R");
    $paidepdes = $consulta -> ret_matriz();

    $objciud = new Despachos($GLOBALS[cod_servic],$GLOBALS[opcion],$this -> aplica,$this -> conexion);
    $ciuori = $objciud -> getSeleccCiudad($_REQUEST[cod_ciuori]);
    $ciudes = $objciud -> getSeleccCiudad($_REQUEST[cod_ciudes]);

    $GLOBALS[nom] = $ciuori[0][1]." - ".$ciudes[0][1]." VIA ".$GLOBALS[nom];

    if(!$GLOBALS[rutactiva])
      $GLOBALS[rutactiva] = 0;

    $query = "SELECT Max(cod_rutasx) maximo
                FROM ".BASE_DATOS.".tab_genera_rutasx";
    $consec = new Consulta($query, $this -> conexion,"BR");
    $ultimo = $consec -> ret_matriz();

    $ultimo_consec = $ultimo[0][0];
    $nuevo_consec = $ultimo_consec+1;

    //query de insercion
    $query = "INSERT INTO ".BASE_DATOS.".tab_genera_rutasx
                      ( cod_rutasx, nom_rutasx, 
                        cod_paiori, cod_depori, cod_ciuori, 
                        cod_paides, cod_depdes, cod_ciudes, 
                        usr_creaci, fec_creaci )
               VALUES (  ".$nuevo_consec.", '".$GLOBALS[nom]."', 
                        ".$paidepori[0][0].", ".$paidepori[0][1].", ".$_REQUEST[cod_ciuori].", 
                        ".$paidepdes[0][0].", ".$paidepdes[0][1].",".$_REQUEST[cod_ciudes].", 
                        '$GLOBALS[usuario]','$fec_actual'
                      )";
    $consulta = new Consulta($query, $this -> conexion,"BR");

    for($i = 0; $i < $GLOBALS[cont]; $i++)
    {
      if($asigna[$i] == 1 AND $contro[$i] != 0)
      {
        //query de insercion
        $query = "INSERT INTO ".BASE_DATOS.".tab_genera_rutcon
                          ( cod_rutasx, cod_contro, val_duraci, 
                            val_distan, usr_creaci, fec_creaci )
                   VALUES ( '$nuevo_consec', '$contro[$i]', '$val[$i]',
                            '".$kil[$i]."', '$GLOBALS[usuario]', '$fec_actual'
                          ) ";
        $insercion = new Consulta($query, $this -> conexion,"R");
      }
    }

    //query de insercion
    $query = "INSERT INTO ".BASE_DATOS.".tab_genera_rutcon
                      ( cod_rutasx, cod_contro, val_duraci, 
                        val_distan, usr_creaci, fec_creaci )
               VALUES ( '$nuevo_consec', '".$GLOBALS[codpcult]."', '".$GLOBALS[timepcult]."', 
                        '".$GLOBALS[kilulti]."', '$GLOBALS[usuario]', '$fec_actual' 
                      )";
    $consulta = new Consulta($query, $this -> conexion,"R");

    $rutaint = $GLOBALS[rutaint];
    $operad = $GLOBALS[operad];

    //Manejo de la Interfaz Aplicaciones SAT
    /*   $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$usuario,$this -> conexion);

    for($i = 0; $i < $interfaz -> totalact; $i++)
    {
    if($rutaint[$i] && $operad[$i] == $interfaz -> interfaz[$i]["operad"])
    {
    $resultado_sat = $interfaz -> insHomoloRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$nuevo_consec,$rutaint[$i]);

    if($resultado_sat["Confirmacion"] == "OK")
    $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">La Ruta Se Homologo en la Interfaz  <b>".$interfaz -> interfaz[$i]["nombre"].".</b><br>";
    else
    $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">Se Presento el Siguiente Error al Insertar la Homologacion : <b>".$resultado_sat["Confirmacion"]."</b><br>";
    }
    }
    */
    if($consulta = new Consulta ("COMMIT", $this -> conexion))
    {
      $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$GLOBALS[cod_servic]." \"target=\"centralFrame\">Copiar Otra Ruta</a></b>";

      $mensaje = "La Ruta <b>".$GLOBALS[nom]."</b> se Inserto con Exito<br>".$link;
      $mens = new mensajes();
      $mens -> correcto("COPIAR RUTAS",$mensaje);
    }
  }

}//FIN CLASE PROC_RUTAS

$proceso = new Proc_rutas($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
