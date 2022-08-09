<?php
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);
class Proc_salida {
  var $conexion,
  $cod_aplica,
  $usuario;

  function __construct($co, $us, $ca) {
    $this->conexion   = $co;
    $this->usuario    = $us;
    $this->cod_aplica = $ca;
    @include_once('../'.DIR_APLICA_CENTRAL.'/lib/InterfADSTrayectJLT.inc');
    @include_once("../".DIR_APLICA_CENTRAL."/lib/general/functions.inc");
    $this->principal();
  }

  function principal() {
    if (!isset($_REQUEST[opcion])) {

      $this->Listar();
    } else {
      switch ($_REQUEST[opcion]) {
        case "0":
          $this->Listar();
          break;
        case "1":
          $this->Formulario();
          break;
        case "2":
          $this->Insertar();
          break;
      }
    }
  }

  function Listar() {

    $data_despac[0]['cod_transp'] = '900284054';
    $_REQUEST['placa'] = $data_despac[0]['num_placax'] = 'UPT949';
    $_REQUEST['despac'] = $data_despac[0]['num_despac'] = '4';
    $data_despac[0]['cod_manifi'] = '00005';
     // validacion de interfaz con integrador GPS
      $mIntegradorGPS = getValidaInterfaz($this->conexion, '53', $data_despac[0]['cod_transp'], true, 'data');
      if( sizeof($mIntegradorGPS) > 0 )
      {
        if ($mIntegradorGPS['ind_operad'] == '3') // SOLO REPORTES UBICACION SI TIENE IND_OPERAD = 3 --> HUB
        {   
            $mHubGPS = new InterfHubIntegradorGPS($this->conexion, ['cod_transp' => $data_despac[0]['cod_transp']] );

            // Proceso de generar itinerario a placa del manifiesto---------------------------------------------------------------------------
            $mDesGPS = $mHubGPS -> setTrakingStart([
                                                    'num_placax' => $data_despac[0]['num_placax'],
                                                    'num_despac' => $data_despac[0]['num_despac'],
                                                    'num_docume' => $data_despac[0]['cod_manifi'],
                                                    'fec_inicio' => date("Y-m-d H:i:s"),
                                                    'fec_finali' => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")."+ 5 day ")),
                                                    'ind_origen' => '3', // 3 = DESPACHO
                                                    ]);
            if($mDesGPS['code'] == '1000'){ 
                ShowMessage("s", "REGISTRO HUB GPS", $mDesGPS['message']);
            }
            else if($mDesGPS['code'] != '1000' && isset($mDesGPS) ){
                ShowMessage("e", "REGISTRO HUB GPS", $mDesGPS['message']);
            }
            // Fin proceso de generar itinerario HUB al despacho ---------------------------------------------------------------------------
        }
        else
        {
            $mInterfGps = new InterfGPS( $this->conexion ); 
            $mResp = $mInterfGps -> setPlacaIntegradorGPS( $_REQUEST['despac'], ['ind_transa' => 'I'] );  
            $mens = new mensajes();
            if($mResp['code_resp'] == '1000'){
              $mens -> correcto("Envio despacho: ".$_REQUEST['despac']." con placa: ".$_REQUEST['placa'],
                                "Este es un envio asincrono al integrador GPS<br><b>Respuesta:</b> ".$mResp['msg_resp']);
            } else {
              $mens -> error("Envio despacho: ".$_REQUEST['despac']." con placa: ".$_REQUEST['placa'],
                             "Este es un envio asincrono al integrador GPS<br><b>Respuesta:</b> ".$mResp['msg_resp']);
            }
            unset($mResp);
          }
      }

      die('dasdas');
    $datos_usuario = $this->usuario->retornar();

    $fechaini = $_REQUEST[fecini]." 00:00:00";
    $fechafin = $_REQUEST[fecfin]." 23:59:59";

    $titori[0][0] = 0;
    $titori[0][1] = "Origen";
    $titdes[0][0] = 0;
    $titdes[0][1] = "Destino";
    $todos[0][0]  = 0;
    $todos[0][1]  = "Todos";

    $query = "SELECT c.cod_ciudad,CONCAT(c.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_despac_vehige b,
                    ".BASE_DATOS.".tab_genera_ciudad c,
                    ".BASE_DATOS.".tab_genera_depart d,
                    ".BASE_DATOS.".tab_genera_paises e
              WHERE a.num_despac = b.num_despac AND
                    a.cod_ciuori = c.cod_ciudad AND
                    c.cod_depart = d.cod_depart AND
                    c.cod_paisxx = d.cod_paisxx AND
                    d.cod_paisxx = e.cod_paisxx AND
                    a.ind_anulad != 'A' AND
                    a.ind_planru = 'S' AND
                    a.fec_salida IS NULL
            ";

    if ($_REQUEST[ciuori]) {
      $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori];
    }

    if ($_REQUEST[ciudes]) {
      $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes];
    }

    if ($_REQUEST[manifi]) {
      $query .= " AND a.cod_manifi = '".$_REQUEST[manifi]."'";
    }

    if ($_REQUEST[numdes]) {
      $query .= " AND a.num_despac = '".$_REQUEST[numdes]."'";
    }

    if ($_REQUEST[vehicu]) {
      $query .= " AND b.num_placax = '".$_REQUEST[vehicu]."'";
    }

    if ($_REQUEST[trayle]) {
      $query .= " AND b.num_trayle = '".$_REQUEST[trayle]."'";
    }

    if ($datos_usuario["cod_perfil"] == "") {
      $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND a.cod_client = '$datos_filtro[clv_filtro]' ";
      }
    } else {
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND a.cod_client = '$datos_filtro[clv_filtro]' ";
      }
    }

    $query .= " GROUP BY 1 ORDER BY 2";
    $consec   = new Consulta($query, $this->conexion);
    $origenes = $consec->ret_matriz();

    if ($_REQUEST[ciuori]) {
      $origenes = array_merge($origenes, $todos);
    } else {

      $origenes = array_merge($titori, $origenes);
    }

    $query = "SELECT c.cod_ciudad,CONCAT(c.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_despac_vehige b,
                    ".BASE_DATOS.".tab_genera_ciudad c,
                    ".BASE_DATOS.".tab_genera_depart d,
                    ".BASE_DATOS.".tab_genera_paises e
              WHERE a.num_despac = b.num_despac AND
                    a.cod_ciudes = c.cod_ciudad AND
                    c.cod_depart = d.cod_depart AND
                    c.cod_paisxx = d.cod_paisxx AND
                    d.cod_paisxx = e.cod_paisxx AND
                    a.ind_anulad != 'A' AND
                    a.ind_planru = 'S' AND
                    a.fec_salida IS NULL
            ";

    if ($_REQUEST[ciuori]) {
      $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori];
    }

    if ($_REQUEST[ciudes]) {
      $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes];
    }

    if ($_REQUEST[manifi]) {
      $query .= " AND a.cod_manifi = '".$_REQUEST[manifi]."'";
    }

    if ($_REQUEST[numdes]) {
      $query .= " AND a.num_despac = '".$_REQUEST[numdes]."'";
    }

    if ($_REQUEST[vehicu]) {
      $query .= " AND b.num_placax = '".$_REQUEST[vehicu]."'";
    }

    if ($_REQUEST[trayle]) {
      $query .= " AND b.num_trayle = '".$_REQUEST[trayle]."'";
    }

    if ($datos_usuario["cod_perfil"] == "") {
      $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND a.cod_client = '$datos_filtro[clv_filtro]' ";
      }
    } else {
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND a.cod_client = '$datos_filtro[clv_filtro]' ";
      }
    }

    $query .= " GROUP BY 1 ORDER BY 2";
    $consec   = new Consulta($query, $this->conexion);
    $destinos = $consec->ret_matriz();

    if ($_REQUEST[ciudes]) {
      $destinos = array_merge($destinos, $todos);
    } else {

      $destinos = array_merge($titdes, $destinos);
    }

    $query = "SELECT a.num_despac,a.cod_manifi,a.ind_anulad,a.cod_ciuori,
                    a.cod_ciudes,c.abr_tercer,b.num_placax,b.num_trayle,
                    d.abr_tercer, a.usr_creaci, a.fec_creaci,
                  IF(a.fec_salida = '' OR a.fec_salida IS NULL, 'SIN REGISTRAR', a.fec_salida ) as 'fec_salida'
               FROM ".BASE_DATOS.".tab_despac_despac a,
                    ".BASE_DATOS.".tab_despac_vehige b,
                    ".BASE_DATOS.".tab_tercer_tercer c,
                    ".BASE_DATOS.".tab_tercer_tercer d
              WHERE a.num_despac = b.num_despac AND
                    b.cod_transp = c.cod_tercer AND
                    b.cod_conduc = d.cod_tercer AND
                    a.ind_anulad != 'A' AND
                    a.ind_planru = 'S' AND
                    (a.fec_salida IS NULL OR a.fec_salida > NOW())
            ";

    if ($_REQUEST[ciuori]) {
      $query .= " AND a.cod_ciuori = ".$_REQUEST[ciuori];
    }

    if ($_REQUEST[ciudes]) {
      $query .= " AND a.cod_ciudes = ".$_REQUEST[ciudes];
    }

    if ($_REQUEST[manifi]) {
      $query .= " AND a.cod_manifi = '".$_REQUEST[manifi]."'";
    }

    if ($_REQUEST[numdes]) {
      $query .= " AND a.num_despac = '".$_REQUEST[numdes]."'";
    }

    if ($_REQUEST[vehicu]) {
      $query .= " AND b.num_placax = '".$_REQUEST[vehicu]."'";
    }

    if ($_REQUEST[trayle]) {
      $query .= " AND b.num_trayle = '".$_REQUEST[trayle]."'";
    }

    if ($datos_usuario["cod_perfil"] == "") {
      $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND a.cod_client = '$datos_filtro[clv_filtro]' ";
      }
    } else {
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_transp = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND b.cod_agenci = '$datos_filtro[clv_filtro]' ";
      }
      $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
      if ($filtro->listar($this->conexion)) {
        $datos_filtro = $filtro->retornar();
        $query        = $query." AND a.cod_client = '$datos_filtro[clv_filtro]' ";
      }
    }

    $query .= " GROUP BY 1 ORDER BY 2";
    $consec = new Consulta($query, $this->conexion);
    $matriz = $consec->ret_matriz();

    $formulario = new Formulario("index.php", "post", "LISTADO DE DESPACHOS", "form_item");

    $formulario->linea("Se Encontro un Total de ".sizeof($matriz)." Despacho(s).", 0, "t2");

    $formulario->nueva_tabla();
    $formulario->texto("Despacho", "text", "numdes\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()", 0, 6, 6, "", $_REQUEST[numdes], "", "", 1);
    $formulario->texto("Documento/Despacho", "text", "manifi\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()", 0, 7, 7, "", $_REQUEST[manifi], "", "", 1);
    $formulario->linea("Estado", 0, "t");
    $formulario->lista_titulo("", "ciuori\" onChange=\"form_item.submit()", $origenes, 0);
    $formulario->lista_titulo("", "ciudes\" onChange=\"form_item.submit()", $destinos, 0);
    $formulario->linea("Transportadora", 0, "t");
    $formulario->texto("Vehiculo", "text", "vehicu\" onChange=\"form_item.submit()", 0, 6, 6, "", $_REQUEST[vehicu], "", "", 1);
    $formulario->texto("Remolque", "text", "trayle\" onChange=\"form_item.submit()", 0, 6, 6, "", $_REQUEST[trayle], "", "", 1);
    $formulario->linea("Conductor", 0, "t");
    $formulario->linea("Fecha de Salida", 0, "t");
    $formulario->linea("Usuario Creador", 0, "t");
    $formulario->linea("Fecha Creación", 1, "t");
    $formulario->oculto("opcion", 0, 0);
    $formulario->oculto("window", "central", 0);
    $formulario->oculto("cod_servic", $_REQUEST[cod_servic], 0);
    for ($i = 0; $i < sizeof($matriz); $i++) {
      if ($matriz[$i][2] == "A") {
        $estilo = "ie";
      } else {

        $estilo = "i";
      }

      if ($matriz[$i][2] == "R") {
        $estado = "Activo";
      } else if ($matriz[$i][2] == "A") {
        $estado = "Anulado";
      }

      $objciud  = new Despachos($_REQUEST[cod_servic], $_REQUEST[opcion], $this->aplica, $this->conexion);
      $ciudad_o = $objciud->getSeleccCiudad($matriz[$i][3]);
      $ciudad_d = $objciud->getSeleccCiudad($matriz[$i][4]);

      $matriz[$i][0] = "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&despac=" .$matriz[$i][0]."&opcion=1 \"target=\"centralFrame\">".$matriz[$i][0]."</a>";

      $formulario->linea($matriz[$i][0], 0, $estilo);
      $formulario->linea($matriz[$i][1], 0, $estilo);
      $formulario->linea($estado, 0, $estilo);
      $formulario->linea($ciudad_o[0][1], 0, $estilo);
      $formulario->linea($ciudad_d[0][1], 0, $estilo);
      $formulario->linea($matriz[$i][5], 0, $estilo);
      $formulario->linea($matriz[$i][6], 0, $estilo);
      $formulario->linea($matriz[$i][7], 0, $estilo);
      $formulario->linea($matriz[$i][8], 0, $estilo);
      $formulario->linea($matriz[$i][11], 0, $estilo);
      $formulario->linea($matriz[$i][9], 0, $estilo);
      $formulario->linea($matriz[$i][10], 1, $estilo);

    }

    $formulario->nueva_tabla();
    $formulario->oculto("b_ciuori", $_REQUEST[b_ciuori], 0);
    $formulario->oculto("b_ciudes", $_REQUEST[b_ciudes], 0);
    $formulario->oculto("transp", $_REQUEST[transp], 0);
    $formulario->oculto("fil", $_REQUEST[fil], 0);
    $formulario->oculto("fecini", $_REQUEST[fecini], 0);
    $formulario->oculto("fecfin", $_REQUEST[fecfin], 0);

    $formulario->cerrar();
  }//FIN FUNCION

  function Formulario() {
    $datos_usuario = $this->usuario->retornar();
    $usuario       = $datos_usuario["cod_usuari"];

    $inicio[0][0] = 0;
    $inicio[0][1] = "-";

    $query = "SELECT a.ind_remdes
            FROM ".BASE_DATOS.".tab_config_parame a
           WHERE a.ind_remdes = '1'
         ";

    $consulta = new Consulta($query, $this->conexion);
    $manredes = $consulta->ret_matriz();

    $query = "SELECT a.ind_desurb
             FROM ".BASE_DATOS.".tab_config_parame a
            WHERE a.ind_desurb = '1'
          ";

    $consulta = new Consulta($query, $this->conexion);
    $desurb   = $consulta->ret_matriz();

    $query = "SELECT a.cod_rutasx,a.fec_salipl,a.cod_transp
         FROM ".BASE_DATOS.".tab_despac_vehige a
        WHERE a.num_despac = ".$_REQUEST[despac]."
      ";

    $consulta = new Consulta($query, $this->conexion);
    $rutasx   = $consulta->ret_matriz();

    $query = "SELECT a.cod_contro,a.nom_contro,b.fec_planea,
        if(a.ind_virtua = '0','Fisico','Virtual')
         FROM ".BASE_DATOS.".tab_genera_contro a,
        ".BASE_DATOS.".tab_despac_seguim b
        WHERE a.cod_contro = b.cod_contro AND
        b.num_despac = ".$_REQUEST[despac]." AND
        b.cod_rutasx = ".$rutasx[0][0]."
        ORDER BY b.fec_planea
      ";

    $consulta = new Consulta($query, $this->conexion);
    $pcontr   = $consulta->ret_matriz();

    $query = "SELECT a.cod_noveda,a.nom_noveda
         FROM ".BASE_DATOS.".tab_genera_noveda a
        WHERE a.ind_alarma = 'N' AND
        a.ind_tiempo = '1'
      ";

    $consulta = new Consulta($query, $this->conexion);
    $noveda   = $consulta->ret_matriz();

    $noveda = array_merge($inicio, $noveda);

    $query = "SELECT cod_perfil
               FROM ".BASE_DATOS.".tab_autori_perfil
              WHERE cod_perfil = '".$this->usuario->cod_perfil."' AND
            cod_autori = '2'";

    $consulta  = new Consulta($query, $this->conexion);
    $parfecsal = $consulta->ret_matriz();

    if (!$_REQUEST[fecprosal]) {
      $feactual = $rutasx[0][1];
    } else {

      $feactual = $_REQUEST[fecprosal];
    }

    $feactual = str_replace("/", "-", $feactual);

    $query = "SELECT if('".$feactual."' > NOW(),'1','0')";

    $consulta = new Consulta($query, $this->conexion);
    $valfec   = $consulta->ret_matriz();

    #Inicio formulario
    $formulario = new Formulario("index.php", "post", "SALIDA DE DESPACHOS", "form_ins");

    $formulario->oculto("usuario", "$usuario", 0);
    $formulario->oculto("despac", $_REQUEST[despac], 0);
    $formulario->oculto("rutasx", $rutasx[0][0], 0);
    $formulario->oculto("opcion", $_REQUEST[opcion], 0);
    $formulario->oculto("window", "central", 0);
    $formulario->oculto("cod_servic", $_REQUEST[cod_servic], 0);
    $formulario->oculto("pernoctar_ind", 1, 0);
    $formulario->oculto("transpor", NIT_TRANSPOR, 0);

    $listado_prin = new Despachos($_REQUEST[cod_servic], 2, $this->aplica, $this->conexion);
    $listado_prin->Encabezado($_REQUEST[despac], $datos_usuario);

    //expedir certificado jlt
    if($this->getInterfParame( '70', $rutasx[0][cod_transp])){
        echo "<table width='100%'>
                <tr width='100%'>
                    <td width='20%' class='celda_etiqueta'>Expedir Certificado de Trayectos</td>
                    <td class='celda'> <input name='ind_trayec' colspan='2' width='100px' align='left'  value='on' type='checkbox'></td>
                </tr>
              </table>";
        //$formulario -> nueva_tabla();

        //$formulario -> caja('Expedir Certificado de Trayectos','ind_trayec\' colspan=\'2\' width=\'100px\' align=\'left\' ','on','0','0');
        //$formulario -> linea("                  ",1,"h");
   }

    $formulario->nueva_tabla();
    $formulario->linea("Fecha Programada de Salida", 1, "h");

    if (!$parfecsal && $valfec[0][0] == "1") {
      $feactual = date("Y-m-d H:i");
      $formulario->nueva_tabla();
      $formulario->linea("<div align = \"center\"><img src=\"../".DIR_APLICA_CENTRAL."/imagenes/advertencia.gif\">No Puede Asignar una Fecha Posterior a la Actual Para la Salida de Vehiculos. Se Asignar&aacute; La Fecha y Hora Actual</div>", 1, "i");
    }

    $feccal = $feactual;

    $formulario->nueva_tabla();
    $formulario->fecha_calendar("Fecha/Hora", "fecprosal", "form_ins", $feactual, "yyyy/mm/dd hh:ii", 1, 1);

    $formulario->nueva_tabla();
    $formulario->linea("Tiempos de Pernoctaci&oacute;n", 1, "h");

    $formulario->nueva_tabla();
    $formulario->linea("", 0, "t");
    $formulario->linea("", 0, "t");
    $formulario->linea("C&oacute;digo", 0, "t");
    $formulario->linea("Nombre", 0, "t");
    $formulario->linea("Puesto", 0, "t");
    $formulario->linea("", 0, "t");
    $formulario->linea("Novedad", 0, "t");
    $formulario->linea("", 0, "t");
    $formulario->linea("Tiempo Estimado", 0, "t");
    $formulario->linea("Fecha y Hora Planeada", 1, "t");

    $tiemacu = 0;
    $pcnovel = $_REQUEST[pcnove];
    $pctimel = $_REQUEST[pctime];

    for ($i = 0; $i < sizeof($pcontr); $i++) {
      $query = "SELECT a.cod_noveda,a.nom_noveda,b.val_pernoc
        FROM ".BASE_DATOS.".tab_genera_noveda a,
       ".BASE_DATOS.".tab_despac_pernoc b
       WHERE a.cod_noveda = b.cod_noveda AND
       b.num_despac = ".$_REQUEST[despac]." AND
       b.cod_contro = ".$pcontr[$i][0]." AND
       b.cod_rutasx = ".$rutasx[0][0]."
     ";

      $consulta = new Consulta($query, $this->conexion);
      $pernoc   = $consulta->ret_matriz();

      $query = "SELECT a.val_duraci
        FROM ".BASE_DATOS.".tab_genera_rutcon a,
       ".BASE_DATOS.".tab_despac_vehige b
       WHERE a.cod_rutasx = b.cod_rutasx AND
       a.cod_contro = ".$pcontr[$i][0]." AND
       b.num_despac = ".$_REQUEST[despac]."
     ";

      $consulta = new Consulta($query, $this->conexion);
      $duraci   = $consulta->ret_matriz();

      if ($manredes && $desurb) {
        $query = "SELECT a.cod_contro
           FROM ".BASE_DATOS.".tab_destin_contro a,
                ".BASE_DATOS.".tab_despac_remdes b
          WHERE a.cod_remdes = b.cod_remdes AND
                a.cod_contro = ".$pcontr[$i][0]." AND
                b.num_despac = ".$_REQUEST[despac]."
        ";

        $consulta  = new Consulta($query, $this->conexion);
        $aplicaurb = $consulta->ret_matriz();
      } else {

        $aplicaurb = NULL;
      }

      if ($_REQUEST[pcnove] && $pcontr[$i][0] != CONS_CODIGO_PCLLEG && !$aplicaurb) {
        $pernoc[0][2] = $pctimel[$i];

        $query = "SELECT a.cod_noveda,a.nom_noveda
         FROM ".BASE_DATOS.".tab_genera_noveda a
        WHERE a.cod_noveda = ".$pcnovel[$i]."
     ";

        $consulta  = new Consulta($query, $this->conexion);
        $novselant = $consulta->ret_matriz();

        $nuenov = array_merge($novselant, $noveda);
      } else {
        $nuenov = array_merge($pernoc, $noveda);
      }

      $tiempcum = $tiemacu+$duraci[0][0];

      $query = $query = "SELECT DATE_ADD('".$feccal."', INTERVAL ".$tiempcum." MINUTE)
        ";

      $consulta = new Consulta($query, $this->conexion);
      $timemost = $consulta->ret_matriz();

      $tiemacu += $pernoc[0][2];

      if ($aplicaurb) {
        $matriz_urbanos[] = $pcontr[$i];
      } else {
        $formulario->caja("", "pcontro[$i]\" disabled ", $pcontr[$i][0], 1, 0);

        if ($pcontr[$i][0] == CONS_CODIGO_PCLLEG) {
          $formulario->linea("-", 0, "i");
          $formulario->linea($pcontr[$i][1], 0, "i");
          $formulario->linea($pcontr[$i][3], 0, "i");
          $formulario->linea("", 0, "t");
          $formulario->linea("-", 0, "i");
          $formulario->linea("", 0, "t");
          $formulario->linea("-", 0, "i");
        } else {
          $formulario->linea($pcontr[$i][0], 0, "i");
          $formulario->linea($pcontr[$i][1], 0, "i");
          $formulario->linea($pcontr[$i][3], 0, "i");
          $formulario->lista("", "pcnove[$i]", $nuenov, 0);
          $formulario->texto("", "text", "pctime[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_ins.submit()", 0, 10, 4, "", $pernoc[0][2]);
        }

        $formulario->linea($timemost[0][0], 1, "i");
        $formulario->oculto("pcontr[$i]", $pcontr[$i][0], 0);

        $ultimo_tiempla = $pcontr[$i][2];
        $ultimo_minutos = $tiempcum;
      }
    }

    if ($matriz_urbanos) {
      $formulario->nueva_tabla();
      $formulario->linea("Puestos de Control Urbanos - Destinatarios", 0, "t2");

      $formulario->nueva_tabla();
      $formulario->linea("", 0, "t");
      $formulario->linea("", 0, "t");
      $formulario->linea("C&oacute;digo", 0, "t");
      $formulario->linea("Nombre", 0, "t");
      $formulario->linea("Puesto", 0, "t");
      $formulario->linea("Fecha y Hora Planeada", 1, "t");

      for ($i = 0; $i < sizeof($matriz_urbanos); $i++) {
        $query = "SELECT (TIME_TO_SEC(TIMEDIFF('".$matriz_urbanos[$i][2]."', '".$ultimo_tiempla."'))/60)
          ";

        $consulta = new Consulta($query, $this->conexion);
        $tiempcum = $consulta->ret_matriz();
        $tiempcum[0][0] += $ultimo_minutos;

        $query = "SELECT DATE_ADD('".$feccal."', INTERVAL ".$tiempcum[0][0]." MINUTE)
        ";

        $consulta = new Consulta($query, $this->conexion);
        $timemost = $consulta->ret_matriz();

        $formulario->caja("", "pcontro_urban[$i]\" disabled ", $matriz_urbanos[$i][0], 1, 0);
        $formulario->linea($matriz_urbanos[$i][0], 0, "i");
        $formulario->linea($matriz_urbanos[$i][1], 0, "i");
        $formulario->linea($matriz_urbanos[$i][3], 0, "i");
        $formulario->linea($timemost[0][0], 1, "i");
        $formulario->oculto("pcontr_urban[$i]", $matriz_urbanos[$i][0], 0);
        $formulario->oculto("fecpla_urban[$i]", $timemost[0][0], 0);
      }
    }

    $formulario->nueva_tabla();
    $formulario->linea("", 1, "h");
    $formulario->boton("Insertar", "button\" onClick=\"aceptar_ins()", 1);
    $formulario->cerrar();

    //Para la carga del Popup
    echo '<div id="AplicationEndDIV"></div>
        <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
          <div id="result" ></div>
        </div>
        ';
  }

  function Insertar() {

    $datos_usuario = $this->usuario->retornar();
    $usuario       = $datos_usuario["cod_usuari"];

    $fec_actual = date("Y-m-d H:i:s");
    $fecpla     = $_REQUEST[fecprosal];

    $fecpla = str_replace("/", "-", $fecpla);

    $formulario = new Formulario("index.php", "post", "Salida de Despachos", "form_item");

    $query = "SELECT a.*, b.*
                FROM ".BASE_DATOS.".tab_despac_despac a
          INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
               WHERE a.num_despac = '".$_REQUEST[despac]."'";

    $consulta    = new Consulta($query, $this->conexion);
    $data_despac = $consulta->ret_matriz();

    //Validacion de despacho en ruta para Megacarga
    if ($data_despac[0][0] == '830100365') {
      $query = "SELECT 1 ".
      "FROM ".BASE_DATOS.".tab_despac_despac a, ".
      "".BASE_DATOS.".tab_despac_vehige b ".
      "WHERE a.num_despac = b.num_despac ".
      "AND b.num_placax = '".$data_despac[0][1]."' ".
      "AND a.fec_salida IS NOT NULL ".
      "AND a.ind_anulad = 'R' ".
      "AND a.fec_llegad IS NULL ";

      $consulta = new Consulta($query, $this->conexion);
      $enruta   = $consulta->ret_matriz();

      if (count($enruta) > 0) {
        $mensaje = "El Vechiculo con Placas ".$data_despac[0][1]." se Encuentra en Ruta Actualmente, Reporte Primero su Llegada";
        $mens    = new mensajes();
        $mens->advert("SALIDA DE DESPACHOS", $mensaje);
        die();
      }
    }

    $pcontro = $_REQUEST[pcontr];
    $pctime  = $_REQUEST[pctime];
    $pcnove  = $_REQUEST[pcnove];

    $pcontr_urban = $_REQUEST[pcontr_urban];
    $fecpla_urban = $_REQUEST[fecpla_urban];

    for ($i = 0; $i < sizeof($pcontro); $i++) {
      $pc_interfSATT[$i][0] = $pcontro[$i];
      $pc_interfSATT[$i][1] = $pctime[$i];

      if ($pcontro[$i] == CONS_CODIGO_PCLLEG) {
        $pc_interfSATT[$i][2] = "0";
      } else {

        $pc_interfSATT[$i][2] = $pcnove[$i];
      }
    }

    $query = "SELECT a.num_placax
         FROM ".BASE_DATOS.".tab_despac_vehige a
        WHERE a.num_despac = ".$_REQUEST[despac]."
      ";

    $consulta = new Consulta($query, $this->conexion);
    $placax   = $consulta->ret_matriz();

    $_REQUEST[placa] = $placax[0][0];

    $query = "DELETE FROM ".BASE_DATOS.".tab_despac_seguim
       WHERE num_despac = ".$_REQUEST[despac]." AND
       cod_rutasx = ".$_REQUEST[rutasx]."
      ";

    $insercion = new Consulta($query, $this->conexion, "BR");

    //La Eliminacion de los registros de pernoctacion relacionados a este plan de ruta
    //se eliminan en cascada.

    $tieacu = 0;

    for ($i = 0; $i <= sizeof($pcontro); $i++) {
      if ($pcontro[$i]) {

        $query = "SELECT a.val_duraci
        FROM ".BASE_DATOS.".tab_genera_rutcon a
       WHERE a.cod_rutasx = ".$_REQUEST[rutasx]." AND
       a.cod_contro = ".$pcontro[$i]."
     ";

        $consulta = new Consulta($query, $this->conexion);
        $tieori   = $consulta->ret_matriz();

        $ultimo = $tieacu+$tieori[0][0];

        $tiepla = $tieacu+$tieori[0][0];

        $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
             (num_despac,cod_rutasx,cod_contro,fec_planea,
        fec_alarma,usr_creaci,fec_creaci,usr_modifi,
        fec_modifi)
                    VALUES (".$_REQUEST[despac].",'".$_REQUEST[rutasx]."','".$pcontro[$i]."',
                        DATE_ADD('$fecpla', INTERVAL " .$tiepla." MINUTE),
                        DATE_ADD('$fecpla', INTERVAL " .$tiepla." MINUTE),
                        '".$_REQUEST[usuario]."','$fec_actual',NULL,NULL)";

        $insercion = new Consulta($query, $this->conexion, "R");

        if ($pcnove[$i] != "0" && $pcontro[$i] != CONS_CODIGO_PCLLEG) {
          $query = "INSERT INTO ".BASE_DATOS.".tab_despac_pernoc
              (num_despac,cod_rutasx,cod_contro,cod_noveda,
         val_pernoc,usr_creaci,fec_creaci,usr_modifi,
         fec_modifi)
       VALUES (".$_REQUEST[despac].",".$_REQUEST[rutasx].",".$pcontro[$i].",
         ".$pcnove[$i].",".$pctime[$i].",'".$_REQUEST[usuario]."',
         '".$fec_actual."',NULL,NULL)
       ";

          $insercion = new Consulta($query, $this->conexion, "R");
        }

        if ($pcnove[$i] != "0") {
          $tieacu += $pctime[$i];
        }
      }
    }

    for ($i = 0; $i < sizeof($pcontr_urban); $i++) {
      $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
                   (num_despac,cod_rutasx,cod_contro,fec_planea,
              fec_alarma,usr_creaci,fec_creaci,usr_modifi,
              fec_modifi)
                    VALUES (".$_REQUEST[despac].",'".$_REQUEST[rutasx]."','".$pcontr_urban[$i]."',
                        '".$fecpla_urban[$i]."',
                        '".$fecpla_urban[$i]."',
                        '".$_REQUEST[usuario]."','$fec_actual',NULL,NULL)";

      $insercion = new Consulta($query, $this->conexion, "R");
    }

    $query = "SELECT DATE_ADD('$fecpla', INTERVAL " .$ultimo." MINUTE)
      ";

    $consulta = new Consulta($query, $this->conexion);
    $timlle   = $consulta->ret_matriz();

    $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
    SET fec_salipl = '".$fecpla."',
        ind_activo = 'S',
        fec_llegpl = '".$timlle[0][0]."',
        usr_modifi = '".$_REQUEST[usuario]."',
        fec_modifi = '".$fec_actual."'
        WHERE num_despac = '".$_REQUEST[despac]."'
      ";

    $insercion = new Consulta($query, $this->conexion, "R");

    $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
    SET fec_salida= '".$fecpla."',
        fec_salsis= NOW(),
        usr_modifi = '".$_REQUEST[usuario]."',
        fec_modifi = '".$fec_actual."',
        fec_ultnov = '".$fecpla."'
        WHERE num_despac = '".$_REQUEST[despac]."'
      ";

    $insercion = new Consulta($query, $this->conexion, "R");

    $query = "SELECT a.cod_transp,a.num_placax,a.cod_rutasx
        FROM ".BASE_DATOS.".tab_despac_vehige a
       WHERE a.num_despac = ".$_REQUEST[despac]."
     ";

    $consulta = new Consulta($query, $this->conexion);
    $transpor = $consulta->ret_matriz();

    /*******
     *
     *  Se crea el despacho en Destino Seguro
     *
     * *****/
    $query = "SELECT b.nom_usuari, b.clv_usuari, a.cod_transp
          FROM ".BASE_DATOS.".tab_despac_vehige a,
               ".BASE_DATOS.".tab_interf_parame b
         WHERE a.num_despac = '".$_REQUEST[despac]."'
               AND a.cod_transp = b.cod_transp
               AND b.cod_operad = '35' AND b.ind_estado = 1 ";

    $consulta = new Consulta($query, $this->conexion);
    $datos_ds = $consulta->ret_matriz();

    if ($datos_ds) {
      include ("kd_xmlrpc.php");

      define("XMLRPC_DEBUG", true);
      define("SITEDS", "www.destinoseguro.net");
      define("LOCATIONDS", "/WS/server.php");

      $query = "SELECT a.cod_manifi, a.cod_ciuori, a.cod_ciudes,
                       b.num_placax, c.cod_marcax, c.cod_carroc,
                       c.cod_colorx, c.ano_modelo, b.cod_conduc,
                       d.nom_tercer, d.nom_apell1,
                       d.dir_domici, d.num_telef1, d.num_telmov, IF(a.cod_client IS NULL, '0',a.cod_client) as cod_client,
                       IF(a.cod_client IS NULL,'',e.abr_tercer) as abr_tercer, a.obs_despac
          FROM ".BASE_DATOS.".tab_despac_vehige b,
               ".BASE_DATOS.".tab_vehicu_vehicu c,
               ".BASE_DATOS.".tab_tercer_tercer d,
               ".BASE_DATOS.".tab_despac_despac a LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer e ON a.cod_client = e.cod_tercer
         WHERE a.num_despac = '".$_REQUEST[despac]."'
           AND a.num_despac = b.num_despac
           AND b.num_placax = c.num_placax
           AND b.cod_conduc = d.cod_tercer ";

      $consulta = new Consulta($query, $this->conexion);
      $despacho = $consulta->ret_matriz();

      $datosDespac['usuario']     = $datos_ds[0][0];
      $datosDespac['clave']       = $datos_ds[0][1];
      $datosDespac['fecha']       = date("Y-m-d", strtotime($fecpla));
      $datosDespac['hora']        = date("H:i:s", strtotime($fecpla));
      $datosDespac['nittra']      = $datos_ds[0][2];
      $datosDespac['manifiesto']  = $despacho[0][0];
      $datosDespac['ruta']        = $despacho[0][1]."-".$despacho[0][2];
      $datosDespac['placa']       = $despacho[0][3];
      $datosDespac['c_marca']     = $despacho[0][4];
      $datosDespac['c_carroc']    = $despacho[0][5];
      $datosDespac['c_color']     = $despacho[0][6];
      $datosDespac['tipo_v']      = '2';
      $datosDespac['modelo']      = $despacho[0][7];
      $datosDespac['cedula']      = $despacho[0][8];
      $datosDespac['nombres']     = $despacho[0][9];
      $datosDespac['apellidos']   = $despacho[0][10];
      $datosDespac['direccion']   = $despacho[0][11];
      $datosDespac['telefono']    = $despacho[0][12];
      $datosDespac['celular']     = $despacho[0][13];
      $datosDespac['nitgen']      = $despacho[0][14];
      $datosDespac['nomgen']      = $despacho[0][15];
      $datosDespac['c_sucur']     = '1';
      $datosDespac['observacion'] = $despacho[0][16];

      /* XMLRPC_prepare works on an array and converts it to XML-RPC parameters */
      list($success, $response) = XMLRPC_request
      (SITEDS,
        LOCATIONDS,
        'wsds.InsertarDespacho',
        array(XMLRPC_prepare($datosDespac),
          'HarryFsXMLRPCClient')
      );
      $mReturn = explode("-", $response['faultString']);

      if (0 == $mReturn[0]) {
        $mMessage = "******** Encabezado ******** \n";
        $mMessage .= "Fecha y hora: ".date("Y-m-d H:i")." \n";
        $mMessage .= "Empresa de transporte: ".$datosDespac['nittra']." \n";
        $mMessage .= "Numero de manifiesto: ".$datosDespac['manifiesto']." \n";
        $mMessage .= "Placa del vehiculo: ".$datosDespac['placa']." \n";
        $mMessage .= "Conductor del vehiculo: ".$datosDespac['cedula']." \n";
        $mMessage .= "Ruta: ".$datosDespac['ruta']." \n";
        $mMessage .= "******** Detalle ******** \n";
        $mMessage .= "Codigo de error: ".$mReturn[1]." \n";
        $mMessage .= "Mesaje de error: ".$mReturn[2]." \n";
        mail("soporte.ingenieros@intrared.net", "Web service Trafico-Destino seguro", $mMessage, 'From: soporte.ingenieros@intrared.net');
      }
      //print_r($response);
    }

    $query = "SELECT a.cod_transp
            FROM ".BASE_DATOS.".tab_despac_vehige a
           WHERE a.num_despac = ".$_REQUEST[despac]."
         ";

    $consulta = new Consulta($query, $this->conexion);
    $transdes = $consulta->ret_matriz();

     
    if ($consulta = new Consulta("COMMIT", $this->conexion)) 
    {
      //se solicita la por ws la expedicion
        if($_REQUEST['ind_trayec'] == 'on' ){
            $poliza = new IntefADSTrayectJLT($this->conexion,$data_despac[0][cod_transp]);
            $registro = $poliza->setPoliza($_REQUEST['despac'], $_REQUEST['cod_manifi']);       
            if($registro["cod_respon"]=='1000'){
                $mensaje_jlt="<br>Se Expide la Poliza de Trayectos con Exito, Certificado de Seguro Numero<b>".$registro[num_certif]."</b>";
            }else{
                $mensaje_jlt="<br>Error Al Expidir la Poliza De Trayectos";
            }
        }

      // $consultaNit = "SELECT a.clv_filtro FROM ".BASE_DATOS.".tab_aplica_filtro_perfil a WHERE a.cod_perfil = ".$_SESSION['datos_usuario']['cod_perfil']." ";
      // $nit = new Consulta($consultaNit, $this->conexion);
      // $nit = $nit->ret_matriz();
      // $nit = $nit[0]['clv_filtro'];

      if ($this->getInterfParame('85', $data_despac[0]['cod_transp']) == true)
      {
        require_once URL_ARCHIV_STANDA."/interf/app/APIClienteApp/controlador/DespachoControlador.php";
        $controlador = new DespachoControlador();
        $response    = $controlador->registrar($this->conexion, $_REQUEST[despac], $data_despac[0]['cod_transp']);
        $mensaje     = $response->msg_respon;

        $mens = new mensajes();
        if ($response->cod_respon == 1000) {

          $mens->correcto("REGISTRO MOVIL", $mensaje);

        } else {
          $mens->advert("REGISTRO MOVIL", $mensaje);
        }
      }
      $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Insertar Otra Salida</a></b>";

      $mensaje = "El Vehiculo <b>".$_REQUEST['placa']."</b> Asignado al Despacho # <b>".$_REQUEST['despac']."</b> Salio Exitosamente.".$mensaje_sat.$mensaje_gps.$mensaje_jlt;
      $mens    = new mensajes();
      $mens->correcto("SALIDA DE DESPACHOS", $mensaje);

      // validacion de interfaz con integrador GPS
      $mIntegradorGPS = getValidaInterfaz($this->conexion, '53', $data_despac[0]['cod_transp'], true, 'data');
      if( sizeof($mIntegradorGPS) > 0 && $regist["ind_seggps"] == '1')
      {
        if ($mIntegradorGPS['ind_operad'] == '3') // SOLO REPORTES UBICACION SI TIENE IND_OPERAD = 3 --> HUB
        { 
            $mHubGPS = new InterfHubIntegradorGPS($this->conexion, ['cod_transp' => $data_despac[0]['cod_transp']] );

            // Proceso de generar itinerario a placa del manifiesto---------------------------------------------------------------------------
            $mDesGPS = $mHubGPS -> setTrakingStart([
                                                    'num_placax' => $data_despac[0]['num_placax'],
                                                    'num_despac' => $data_despac[0]['num_despac'],
                                                    'num_docume' => $data_despac[0]['cod_manifi'],
                                                    'fec_inicio' => date("Y-m-d H:i:s"),
                                                    'fec_finali' => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")."+ 5 day ")),
                                                    'ind_origen' => '3', // 3 = DESPACHO
                                                    ]);
            if($mDesGPS['code'] == '1000'){ 
                ShowMessage("s", "REGISTRO HUB GPS", $mDesGPS['message']);
            }
            else if($mDesGPS['code'] != '1000' && isset($mDesGPS) ){
                ShowMessage("e", "REGISTRO HUB GPS", $mDesGPS['message']);
            }
            // Fin proceso de generar itinerario HUB al despacho ---------------------------------------------------------------------------
        }
        else
        {
            $mInterfGps = new InterfGPS( $this->conexion ); 
            $mResp = $mInterfGps -> setPlacaIntegradorGPS( $_REQUEST['despac'], ['ind_transa' => 'I'] );  
            $mens = new mensajes();
            if($mResp['code_resp'] == '1000'){
              $mens -> correcto("Envio despacho: ".$_REQUEST['despac']." con placa: ".$_REQUEST['placa'],
                                "Este es un envio asincrono al integrador GPS<br><b>Respuesta:</b> ".$mResp['msg_resp']);
            } else {
              $mens -> error("Envio despacho: ".$_REQUEST['despac']." con placa: ".$_REQUEST['placa'],
                             "Este es un envio asincrono al integrador GPS<br><b>Respuesta:</b> ".$mResp['msg_resp']);
            }
            unset($mResp);
          }
      }


    }
     

    $formulario->cerrar();
  }

  //---------------------------------------------
  /*! \fn: getInterfParame
   *  \brief:Verificar la interfaz con destino seguro esta activa
   *  \author: Nelson Liberato
   *  \date: 21/12/2015
   *  \date modified: 21/12/2015
   *  \return BOOL
   */
  function getInterfParame($mCodInterf = NULL, $nit = NULL) {
    $mSql = "SELECT ind_estado
                   FROM ".BASE_DATOS.".tab_interf_parame a
                  WHERE a.cod_operad = '".$mCodInterf."'
                    AND a.cod_transp = '".$nit."'";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = $mMatriz->ret_matriz("a");
    return $mMatriz[0]['ind_estado'] == '1'?true:false;
  }

}//FIN CLASE PROC_DESCARGUE
$proceso = new Proc_salida($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>
