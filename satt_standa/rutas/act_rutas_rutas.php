<?php
/*! \file: act_rutas_rutas.php
 *  \brief: Actualiza Rutas
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
 *  \brief: Actualiza Rutas
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

    IncludeJS("jquery.js");
    IncludeJS("rutas.js");
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";

    Proc_rutas::$cRutas = new rutas( $co, $us, $ca );
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;

    if(!isset($_REQUEST[opcion]))
      $this -> Buscar();
    else
    {
      switch($_REQUEST[opcion])
      {
        case "1":
          $this -> Resultado();
          break;
        case "2":
          $this -> Datos();
          break;
        case "3":
          $this -> Actualizar();
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
   *  \brief: Resultado de la busqueda de rutas
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
                     a.ind_estado, a.usr_creaci, a.fec_creaci, a.usr_modifi, a.fec_modifi
                FROM ".BASE_DATOS.".tab_genera_rutasx a 
           LEFT JOIN ".BASE_DATOS.".tab_genera_rutcon d 
                  ON a.cod_rutasx = d.cod_rutasx ";
    $indwhere = 0;

    if($_REQUEST[ruta] != "")
    {
      if($indwhere)
        $query .= " AND a.nom_rutasx LIKE '%".$_REQUEST[ruta]."%'";
      else{
        $query .= " WHERE a.nom_rutasx LIKE '%".$_REQUEST[ruta]."%'";
        $indwhere = 1;
      }
    }

    if($_REQUEST[cod_ciuori])
    {
      if($indwhere)
        $query .= " AND a.cod_ciuori = '".$_REQUEST[cod_ciuori]."'";
      else{
        $query .= " WHERE a.cod_ciuori = '".$_REQUEST[cod_ciuori]."'";
        $indwhere = 1;
      }
    }

    if($_REQUEST[cod_ciudes])
    {
      if($indwhere)
        $query .= " AND a.cod_ciudes = '".$_REQUEST[cod_ciudes]."'";
      else{
        $query .= " WHERE a.cod_ciudes = '".$_REQUEST[cod_ciudes]."'";
        $indwhere = 1;
      }
    }

    $query .= " GROUP BY 1 ORDER BY 2";
    $consec = new Consulta($query, $this -> conexion);
    $matriz = $consec -> ret_matriz();

    for($i=0;$i<sizeof($matriz);$i++)
      $matriz[$i][0]= "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&ruta=".$matriz[$i][0]."&opcion=2 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

    $formulario = new Formulario ("index.php","post","LISTADO DE RUTAS","form_item");

    $formulario -> nueva_tabla();
    $formulario -> linea("Se Encontrar&oacute;n un Total de ".sizeof($matriz)." Rutas.",0,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("C&oacute;digo",0,"t");
    $formulario -> linea("Nombre",0,"t");
    $formulario -> linea("Cant. P/C",0,"t");
    $formulario -> linea("Estado",0,"t");
    $formulario -> linea("Usuario Creador",0,"t");
    $formulario -> linea("Fecha Creacion",0,"t");
    $formulario -> linea("Usuario Modificador",0,"t");
    $formulario -> linea("Fecha Modificacion",1,"t");

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
      $formulario -> linea($estado,0,$estilo);
      $formulario -> linea($matriz[$i][4],0,$estilo);
      $formulario -> linea($matriz[$i][5],0,$estilo);
      $formulario -> linea($matriz[$i][6],0,$estilo);
      $formulario -> linea($matriz[$i][7],1,$estilo);
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

  /*! \fn: Datos
   *  \brief: Formulario de Actualizacion
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 06/08/2015
   *  \modified by: Ing. Fabian Salinas
   *  \param: 
   *  \return:
   */
  function Datos()
  {
    $datos_usuario = $this -> usuario -> retornar();
    $usuario=$datos_usuario["cod_usuari"];

    $inicio[0][0] = 0;
    $inicio[0][1] = '-';

    $query = " SELECT a.cod_rutasx, a.nom_rutasx, b.nom_ciudad, 
                      c.nom_ciudad, d.cod_contro, if(e.ind_virtua = '1',CONCAT(e.nom_contro,' (Virtual)'),e.nom_contro) AS nom_contro, 
                      d.val_duraci, e.nom_encarg, e.dir_contro, 
                      e.tel_contro, b.cod_ciudad, c.cod_ciudad, 
                      d.val_duraci, d.val_distan, d.ind_estado, 
                      a.ind_estado, if(ind_urbano = '".COD_ESTADO_ACTIVO."',' - (Urbano)','') AS ind_urbano 
                 FROM ".BASE_DATOS.".tab_genera_rutasx a 
           INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b 
                   ON a.cod_ciuori = b.cod_ciudad
           INNER JOIN ".BASE_DATOS.".tab_genera_ciudad c 
                   ON a.cod_ciudes = c.cod_ciudad 
            LEFT JOIN ".BASE_DATOS.".tab_genera_rutcon d 
                   ON a.cod_rutasx = d.cod_rutasx 
                  AND d.cod_contro != ".CONS_CODIGO_PCLLEG." 
            LEFT JOIN ".BASE_DATOS.".tab_genera_contro e 
                   ON d.cod_contro = e.cod_contro
                WHERE a.cod_rutasx = '$_REQUEST[ruta]'
             ORDER BY 7";
    $consec = new Consulta($query, $this -> conexion);
    $matriz = $consec -> ret_matriz();

    $nombre_des = explode("VIA ",$matriz[0][1]);

    if($nombre_des[1])
      $matriz[0][1] = $nombre_des[1];

    if(!$_REQUEST[nom])
      $_REQUEST[nom] = $matriz[0][1];

    if(isset($_REQUEST[cod_ciuori]))
      $origen_a=$_REQUEST[cod_ciuori];
    else
      $origen_a=$matriz[0][10];

    if(isset($_REQUEST[cod_ciudes]))
      $destino_a=$_REQUEST[cod_ciudes];
    else
      $destino_a=$matriz[0][11];

    if(isset($_REQUEST[asigna]) AND isset($_REQUEST[contro]) AND isset($_REQUEST[val]))
    {
      $asigna=$_REQUEST[asigna];
      $contro=$_REQUEST[cod_contro];
      $val   =$_REQUEST[val];
      $kil   =$_REQUEST[kil];
      $activo=$_REQUEST[activo];
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

    if(!$_REQUEST[cont])
    {
      $_REQUEST[cont] = sizeof($matriz);
      $_REQUEST[cinicial] = sizeof($matriz);
    }
    else if($_REQUEST[adican])
      $_REQUEST[cont] += $_REQUEST[adican];

    if(!$_REQUEST[cod_ciuori])
      $_REQUEST[cod_ciuori] = $origen_a;

    if(!$_REQUEST[cod_ciudes])
      $_REQUEST[cod_ciudes] = $destino_a;

    $mCiuOri = Proc_rutas::$cRutas -> getCiudades( $_REQUEST[cod_ciuori] );
    $mCiuDes = Proc_rutas::$cRutas -> getCiudades( $_REQUEST[cod_ciudes] );

    $query = "SELECT cod_rutasx
                FROM ".BASE_DATOS.".tab_despac_vehige
               WHERE cod_rutasx = '$_REQUEST[ruta]' ";
    $consulta = new Consulta($query,$this -> conexion);
    $existe_rut   = $consulta -> ret_arreglo();

    $formulario = new Formulario ("index.php","post","DETALLE DE LA RUTA","form_item");

    $formulario -> nueva_tabla();
    $formulario -> linea("Informaci&oacute;n B&aacute;sica de la Ruta",0,"t2");

    $formulario -> nueva_tabla();
    $formulario -> linea("C&oacute;digo",0,"","25%",'',"right");
    $formulario -> linea($matriz[0][0],1,"i");

    #Ciudad Origen
    if(!$existe_rut)
    {
      $formulario -> oculto("cod_ciuori\" id=\"cod_ciuoriID", $mCiuOri[0][cod_ciudad], 0);
      $formulario -> texto( "Origen:", "text", "origen\" id=\"origenID", 1, 40, 40, "", $mCiuOri[0][abr_ciudad], 0, 0, 0, 1 );
    }
    else
    {
      $formulario -> linea("Origen", 0,"","25%",1,"right");
      $formulario -> linea($mCiuOri[0][abr_ciudad], 1,"i","25%");
      $formulario -> oculto("cod_ciuori",$mCiuOri[0][cod_ciudad],0);
    }

    #Ciudad Destino
    if(!$existe_rut)
    {
      $formulario -> oculto("cod_ciudes\" id=\"cod_ciudesID", $mCiuDes[0][cod_ciudad], 0);
      $formulario -> texto( "Destino:", "text", "destino\" id=\"destinoID", 1, 40, 40, "", $mCiuDes[0][abr_ciudad], 0, 0, 0, 1 );
    }
    else
    {
      $formulario -> linea("Destino",0,"","",1,"right");
      $formulario -> linea($mCiuDes[0][abr_ciudad],1,"i");
      $formulario -> oculto("cod_ciudes",$mCiuDes[0][cod_ciudad],0);
    }

    if(!$_REQUEST[rutactiva])
      $_REQUEST[rutactiva] = $matriz[0][15];

    $formulario -> texto ("Via","text","nom",1,60,255,"",$_REQUEST[nom]);

    if($_REQUEST[rutactiva] == COD_ESTADO_ACTIVO)
      $formulario -> caja ("Activa","rutactiva",1,1,1);
    else
      $formulario -> caja ("Activa","rutactiva",1,0,1);

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
    $formulario -> linea("",0,"t");
    $formulario -> linea("",0,"t");
    $formulario -> linea("Activo",1,"t");

    for($i=0;$i<$_REQUEST[cont];$i++)
    {
      $disabledvar = "";

      if($contro[$i])
      {
        $query = "SELECT a.cod_contro
                    FROM ".BASE_DATOS.".tab_despac_seguim a
                   WHERE a.cod_contro = ".$contro[$i]." AND
                         a.cod_rutasx = ".$_REQUEST[ruta]." ";
        $consulta = new Consulta($query, $this -> conexion);
        $existcon = $consulta -> ret_matriz();

        if($existcon)
        {
          $disabledvar = "\" disabled ";
          $formulario -> oculto("asigna[$i]","1",0);
        }
      }

      if($contro[$i])
      {
        if($asigna[$i] == 1)
          $formulario -> caja("","asigna[$i]".$disabledvar, "1", 1,0);
        else if($i+1 == $_REQUEST[cont])
          $formulario -> caja("","asigna[$i]".$disabledvar, "1", 1,0);
        else
          $formulario -> caja("","asigna[$i]".$disabledvar, "1", 0,0);

        //Trae el puesto de control anterior
        $query = "SELECT cod_contro,if(ind_virtua = '1',CONCAT(nom_contro,' (Virtual)'),nom_contro),
                         if(ind_urbano = '".COD_ESTADO_ACTIVO."',' - (Urbano)','')
                    FROM ".BASE_DATOS.".tab_genera_contro
                   WHERE cod_contro = '".$contro[$i]."'";
        $consulta = new Consulta($query, $this -> conexion);
        $control = $consulta -> ret_matriz();

        $query = "SELECT a.cod_contro
                    FROM ".BASE_DATOS.".tab_genera_contro a
                   WHERE a.cod_contro = ".$contro[$i]." AND
                         a.ind_estado = '0' ";
        $consulta = new Consulta($query, $this -> conexion);
        $estacont = $consulta -> ret_matriz();

        $disabledact = "";

        if($estacont)
        {
          $disabledact = "\" disabled ";
          $formulario -> oculto("activo[$i]",$activo[$i],0);
        }

        $formulario -> oculto("cod_contro[$i]\" id=\"cod_contro".$i."ID", $control[0][0], 0);
        echo '<td class="celda_info">
                <input type="text" maxlength="40" value="'.$control[0][1].'" nameID="cod_contro'.$i.'ID" size="40" id="controID['.$i.']" name="contro['.$i.']" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto">
              </td>';
        $formulario -> texto("Duraci&oacute;n (Min)","text","val[$i]",0,5,4,"","$val[$i]");
        $formulario -> texto("Distancia (K/m)","text","kil[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,6,6,"","$kil[$i]");
        $formulario -> caja("","activo[$i]".$disabledact,"1",$activo[$i],1);
      }
      else
      {
        //Trae todos los puestos de control
        $formulario -> caja("","asigna[$i]", "1", 1,0);
        $formulario -> oculto("cod_contro[$i]\" id=\"cod_contro".$i."ID", "", 0);
        echo '<td class="celda_info">
                <input type="text" maxlength="40" value="" nameID="cod_contro'.$i.'ID" size="40" id="controID['.$i.']" name="contro['.$i.']" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto">
              </td>';
        $formulario -> texto("Duraci&oacute;n (Min)","text","val[$i]",0,5,4,"","");
        $formulario -> texto("Distancia (K/m)","text","kil[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,6,6,"","");
        $formulario -> caja("","activo[$i]".$disabledact,"1",1,1);
      }

    }//fin for

    $query = "SELECT a.cod_contro,if(a.ind_virtua = '1',CONCAT(a.nom_contro,' (Virtual)'),a.nom_contro),
                     b.val_duraci,b.val_distan
                FROM ".BASE_DATOS.".tab_genera_contro a LEFT JOIN
                     ".BASE_DATOS.".tab_genera_rutcon b ON
                     a.cod_contro = b.cod_contro AND
                     b.cod_rutasx = ".$_REQUEST[ruta]."
               WHERE a.cod_contro = ".CONS_CODIGO_PCLLEG." ";
    $consulta = new Consulta($query, $this -> conexion);
    $ultcon = $consulta -> ret_matriz();

    if(!$_REQUEST[timepcult])
      $_REQUEST[timepcult] = $ultcon[0][2];

    if(!$_REQUEST[kilulti])
      $_REQUEST[kilulti] = $ultcon[0][3];

    $formulario -> caja("","asipcult\" disabled ", "1", 1,0);
    $formulario -> oculto("codpcult\" id=\"codpcultID", $ultcon[0][0], 0);
    echo '<td class="celda_info">
            <input type="text" maxlength="40" value="'.$ultcon[0][1].'" size="40" id="codipcultID" name="codipcult" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto" disabled>
          </td>';
    $formulario -> texto("Duraci&oacute;n (Min)","text","timepcult\" id=\"timepcultID",0,5,4,"",$_REQUEST[timepcult]);
    $formulario -> texto("Distancia (K/m)","text","kilulti\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,6,6,"",$_REQUEST[kilulti]);
    $formulario -> caja("","activoulti\" disabled ","1",1,1);

    $formulario -> nueva_tabla();

    //Manejo de la Interfaz Aplicaciones SAT
    /*
      $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$this -> usuario_aplicacion,$this -> conexion);

      if($interfaz -> totalact > 0)
        $formulario -> linea("El Sistema Tiene Interfases Activas Debe Homologar este P/C",1,"t2");
    */
    $rutaint = $_REQUEST[rutaint];
    $homoloini = $_REQUEST[homoloini];
    $copia = $_REQUEST[copia];

    /*
      for($i = 0; $i < $interfaz -> totalact; $i++)
      {
        if($_REQUEST[cod_ciuori] && $_REQUEST[cod_ciudes])
        {
          $homolocon = $interfaz -> getHomoloTranspRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$_REQUEST[ruta]);

          if($homolocon["RUTAHomolo"] > 0 && !$_REQUEST[homoloini])
          {
            $rutaint[$i] = $homolocon["RUTAHomolo"];
            $homoloini[$i] = $homolocon["RUTAHomolo"];
          }

          $ruta_ws = $interfaz -> getListadRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$_REQUEST[cod_ciuori],$_REQUEST[cod_ciudes]);

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

            $formulario -> nueva_tabla();

            if(isset($rutaint[$i]) && $rutaint[$i] != "0")
            {
              $homolo = $interfaz -> getHomoloRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$rutaint[$i]);

              if($homolo["RUTAHomolo"] != -2 && $homoloini[$i] != $rutaint[$i])
              {
                echo "<div align = \"center\"><small><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\"><b>La Ruta  Seleccionada Ya Se Encuentra Homologada en la Interfaz ".$interfaz -> interfaz[$i]["nombre"]."</b>.</small></div>";
              }
              else
                $ruta = array_merge($ruta_a,$ruta);
            }

            $formulario -> lista("Ruta Con Interfaz ".$interfaz -> interfaz[$i]["nombre"].":", "rutaint[$i]\" onChange=\"form_item.submit()", $ruta, 0);

            if($copia[$i])
              $chek = "checked";
            else
              $chek = "";

            $formulario -> caja("Plan de Ruta (S/N)","copia[$i]\" onClick=\"form_item.submit()",1,$chek,1);

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
          }

          $formulario -> nueva_tabla();
          $formulario -> oculto("operad[$i]",$interfaz -> interfaz[$i]["operad"],0);
          $formulario -> oculto("homoloini[$i]",$homoloini[$i],0);
        }
        else
          $formulario -> linea("Debe Seleccionar Origen y Destino Para Realizar la Homologaci&oacute;n",1,"i");
      }
    */
    $formulario -> nueva_tabla();
    $formulario -> oculto("usuario","$usuario",0);
    $formulario -> oculto("cont","$_REQUEST[cont]",0);
    $formulario -> oculto("cinicial","$_REQUEST[cinicial]",0);
    $formulario -> oculto("opcion",$_REQUEST[opcion],0);
    $formulario -> oculto("standa\" id=\"standaID", DIR_APLICA_CENTRAL, 0);
    //$formulario -> oculto("maximo",$interfaz -> cant_interf,0);
    $formulario -> oculto("ruta",$_REQUEST[ruta],0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);

    $formulario -> botoni("Actualizar","aceptar_actuali()",0);
    $formulario -> texto("Adicionar Puesto(s) de Control","text","adican\" onChange=\"form_item.submit()",0,2,2,"","");

    if($_REQUEST[cont] > $_REQUEST[cinicial])
      $formulario -> botoni("Borrar","form_item.cont.value--; form_item.submit();",1);

    $formulario -> cerrar();
  }

  /*! \fn: Actualizar
   *  \brief: Actualiza la ruta
   *  \author: 
   *  \date: dia/mes/año
   *  \date modified: 06/08/2015
   *  \modified by: Ing. Fabian Salinas
   *  \param: 
   *  \return:
   */
  function Actualizar()
  {
    $fec_actual = date("Y-m-d H:i:s");

    //reasigna los valores

    $asigna=$_REQUEST[asigna];
    $contro=$_REQUEST[cod_contro];
    $val   =$_REQUEST[val];
    $kil   =$_REQUEST[kil];
    $activo=$_REQUEST[activo];

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

    $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);
    $ciuori = $objciud -> getSeleccCiudad($_REQUEST[cod_ciuori]);
    $ciudes = $objciud -> getSeleccCiudad($_REQUEST[cod_ciudes]);

    $_REQUEST[nom] = $ciuori[0][1]." - ".$ciudes[0][1]." VIA ".$_REQUEST[nom];

    if(!$_REQUEST[rutactiva])
      $_REQUEST[rutactiva] = COD_ESTADO_INACTI;
    else
      $_REQUEST[rutactiva] = COD_ESTADO_ACTIVO;

    //query que actualiza

    $query = " UPDATE ".BASE_DATOS.".tab_genera_rutasx
                  SET nom_rutasx = '".$_REQUEST[nom]."',
                      cod_paiori = ".$paidepori[0][0].",
                      cod_depori = ".$paidepori[0][1].",
                      cod_ciuori = ".$_REQUEST[cod_ciuori].",
                      cod_paides = ".$paidepdes[0][0].",
                      cod_depdes = ".$paidepdes[0][1].",
                      cod_ciudes = ".$_REQUEST[cod_ciudes].",
                      ind_estado = '".$_REQUEST[rutactiva]."',
                      usr_modifi = '$_REQUEST[usuario]',
                      fec_modifi = '$fec_actual'
                WHERE cod_rutasx = '$_REQUEST[ruta]'";
    $insercion = new Consulta($query, $this -> conexion,"BR");

    for($i = 0; $i < $_REQUEST[cont]; $i++)
    {
      if($asigna[$i] == 1 AND $contro[$i] != 0)
      {
        //verificar si existe el puesto de control

        $query ="SELECT cod_contro
                   FROM ".BASE_DATOS.".tab_genera_rutcon
                  WHERE cod_rutasx = '$_REQUEST[ruta]' AND
                        cod_contro = '".$contro[$i]."' ";
        $consulta = new  Consulta($query, $this -> conexion,"R");
        $pcontrol = $consulta -> ret_matriz();

        //si no existe lo inserta
        if ($contro[$i] != $pcontrol[0][0])
        {
          //query que inserta

          //query de insercion
          $query = "INSERT INTO ".BASE_DATOS.".tab_genera_rutcon
                    (cod_rutasx,cod_contro,val_duraci,val_distan,
                    usr_creaci,fec_creaci)
                    VALUES ('$_REQUEST[ruta]','$contro[$i]','$val[$i]','".$kil[$i]."',
                    '$_REQUEST[usuario]','$fec_actual') ";
          $insercion = new Consulta($query, $this -> conexion,"R");
        }

        //si existe lo actualiza

        if($contro[$i] == $pcontrol[0][0])
        {
          //query que Actualiza
          if(!$activo[$i])
            $activo[$i] = 0;

          $query = " UPDATE ".BASE_DATOS.".tab_genera_rutcon
                        SET val_duraci = '$val[$i]',
                            val_distan = '".$kil[$i]."',
                            ind_estado = '".$activo[$i]."',
                            usr_modifi = '$_REQUEST[usuario]',
                            fec_modifi = '$fec_actual'
                      WHERE cod_rutasx = '$_REQUEST[ruta]' AND
                            cod_contro = '".$contro[$i]."'";
          $actualizar = new Consulta($query, $this -> conexion,"R");
        }
      }

      //eliminar el puesto de control

      if($asigna[$i] != 1 AND $contro[$i] != 0)
      {
        //trae el nombre del puesto de control

        $query = "SELECT nom_contro FROM ".BASE_DATOS.".tab_genera_contro
                   WHERE cod_contro = ".$contro[$i];

        $consulta = new Consulta($query, $this -> conexion,"R");
        $nom_contro = $consulta -> ret_matriz();

        $query = "DELETE FROM ".BASE_DATOS.".tab_genera_rutcon
                  WHERE cod_rutasx = '$_REQUEST[ruta]' AND
                  cod_contro = '".$contro[$i]."' ";
        $delete = new Consulta($query, $this -> conexion,"R");
      }
    }

    //query que Actualiza

    $query = " UPDATE ".BASE_DATOS.".tab_genera_rutcon
                  SET val_duraci = '".$_REQUEST[timepcult]."',
                      val_distan = '".$_REQUEST[kilulti]."',
                      usr_modifi = '$_REQUEST[usuario]',
                      fec_modifi = '$fec_actual'
                WHERE cod_rutasx = '$_REQUEST[ruta]' AND
                      cod_contro = '".$_REQUEST[codpcult]."'";
    $actualizar = new Consulta($query, $this -> conexion,"R");

    $rutaint = $_REQUEST[rutaint];
    $operad = $_REQUEST[operad];
    $homoloini = $_REQUEST[homoloini];

    //Manejo de la Interfaz Aplicaciones SAT
    /*
      $interfaz = new Interfaz_SAT(BASE_DATOS,NIT_TRANSPOR,$this -> usuario_aplicacion,$this -> conexion);

      for($i = 0; $i < $interfaz -> totalact; $i++)
      {
        if($rutaint[$i] && $operad[$i] == $interfaz -> interfaz[$i]["operad"])
        {
          if($homoloini[$i] != $rutaint[$i])
          {
            $resultado_sat = $interfaz -> actHomoloRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$_REQUEST[ruta],$rutaint[$i]);

            if($resultado_sat["Confirmacion"] == "OK")
              $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">La Ruta Se Homologo en la Interfaz  <b>".$interfaz -> interfaz[$i]["nombre"].".</b><br>";
            else
              $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">Se Presento el Siguiente Error al Insertar la Homologacion : <b>".$resultado_sat["Confirmacion"]."</b><br>";
          }
        }
        else
        {
          if($homoloini[$i])
            $rutaint[$i] = $homoloini[$i];

          $resultado_sat = $interfaz -> eliHomoloRutasx($interfaz -> interfaz[$i]["operad"],$interfaz -> interfaz[$i]["usuari"],$interfaz -> interfaz[$i]["passwo"],$_REQUEST[ruta],$rutaint[$i]);

          if($resultado_sat["Confirmacion"] == "OK")
            $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/ok.gif\">Se Elimino la Homologacion de la Ruta en la Interfaz <b>".$interfaz -> interfaz[$i]["nombre"].".</b><br>";
          else
            $mensaje_sat .= "<img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">Se Presento el Siguiente Error al Eliminar la Homologacion : <b>".$resultado_sat["Confirmacion"]."</b><br>";
        }
      }
    */
    if($consulta = new Consulta ("COMMIT", $this -> conexion))
    {
      $link = "<b><a href=\"index.php?cod_servic=".$this -> servic."&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Actualizar Otra Ruta</a></b>";

      $mensaje = "La Ruta <b>".$_REQUEST[nom]."</b> se Actualizo con Exito<br>".$link;
      $mens = new mensajes();
      $mens -> correcto("ACTUALIZAR RUTAS",$mensaje);
    }
  }

}//FIN CLASE PROC_RUTAS

$proceso = new Proc_rutas($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
