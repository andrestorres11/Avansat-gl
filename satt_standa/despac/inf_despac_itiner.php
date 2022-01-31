<?php  
/*! \file: inf_despac_finali.php
 *  \brief: Informe Despachos Finalizados (Informes > Operacion trafico > Finalizados)
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 2.0
 *  \date: 22/04/2016
 *  \bug: 
 *  \warning: 
 */

class infDespacErrorItinerarios
{
  private static  $cConexion,
          $cCodAplica,
          $cUsuario,
          $cDespac,
          $cNull = array( array('', '-----') );

  function __construct($co = null, $us = null, $ca = null) {
    if($_REQUEST['Ajax'] === 'on' ){
      @include_once( "../lib/ajax.inc" );
      @include_once( "../lib/general/constantes.inc" );
      @include_once('../lib/general/functions.inc');
      self::$cConexion  = $AjaxConnection;
      self::$cUsuario   = $_SESSION['datos_usuario'];
      self::$cCodAplica = $_SESSION['codigo'];
    }else{
      @include_once( '../'.DIR_APLICA_CENTRAL.'/inform/class_despac_trans3.php' );
      
      self::$cDespac = new Despac( $co, $us, $ca );
      self::$cConexion  = $co;
      self::$cUsuario   = $us;
      self::$cCodAplica = $ca;
    }

    @include_once( '../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc' );

    switch($_REQUEST['Option']){
      case 'inform':
        self::inform();
        break;

      case 'getCiudades':
        self::getCiudades();
        break;

      case 'getConduc':
        self::getConduc();
        break;
      
      case 'setForm':
        self::setReenvio();
        break;
      
      case 'getDestinatarios':
        self::getDestinatarios();
        break;
      
      case 'updateDestinatarios':
        self::updateDestinatarios();
        break;

      case 'exportExcel':
        self::exportExcel();
        break;

      default:
        self::formulario();
        break;
    }
  }

  /*! \fn: formulario
   *  \brief: Formulario para aplicar los filtros al informe
   *  \author: Ing. Fabian Salinas
   *  \date: 22/04/2016
   *  \date modified: dd/mm/aaaa
   *  \param:    
   *  \return: 
   */
  private function formulario(){
    $mTD = array("class"=>"cellInfo1", "width"=>"20%");
    $mAs = '<label style="color: red">* </label>';
    // $mTransp = self::$cDespac -> getTransp(); 

    // if( sizeof($mTransp) != 1 ){
    //   $mTransp = array_merge(self::$cNull, $mTransp);
    // }


    IncludeJS( 'jquery.js' );
    IncludeJS( 'jquery.blockUI.js' );
    IncludeJS( 'inf_despac_itiner.js' );
    IncludeJS( 'functions.js' );
    IncludeJS( 'validator.js' );
    IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
    IncludeJS( 'jquery.multiselect.filter.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
    

    $mHtml = new Formlib(2);
    
    $mHtml->SetCss("jquery");
    $mHtml->SetCss("informes");
    $mHtml->SetCss("validator");
    $mHtml->SetBody("<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n");
    $mHtml->SetBody("<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n");
    $mHtml->SetJs("new_ajax"); 
    $mHtml->SetJs( "dinamic_list");
    $mHtml->SetCss("dinamic_list");

    $mHtml->CloseTable('tr');

    #Acordion
    $mHtml->SetBody('<form name="form_infDespacErrorItinerarios" id="form_infDespacErrorItinerariosID"  method="post">');
    $mHtml->OpenDiv("class:accordion");
      $mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h3>");
      $mHtml->OpenDiv("id:secID");
        $mHtml->OpenDiv("id:formID; class:Style2DIV");

          $mHtml->Table('tr');
            $mHtml->SetBody('<tr><th class="CellHead" colspan="4" style="text-align:left">Filtros Generales</th></tr>');

            $mHtml->Label( "Fecha Inicial: ", $mTD );
            $mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"fec_inicia", "id"=>"fec_iniciaID", "size"=>"10", "value"=> date('Y-m-d',strtotime("-20 day") )  ) );
            $mHtml->Label( "Fecha Final: ", $mTD );
            $mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"fec_finali", "id"=>"fec_finaliID", "size"=>"10", "end" => "yes", "value"=> date('Y-m-d')) );
           
            $mHtml->Label( "Ciudad Origen: ", $mTD );
            $mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"nom_ciuori", "id"=>"nom_ciuoriID", "size"=>"40") );
            $mHtml->Label( "Ciudad Destino: ", $mTD );
            $mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"nom_ciudes", "id"=>"nom_ciudesID", "size"=>"40", "end"=>true) );

         

            $mHtml->Label( "Despacho: ", $mTD );
            $mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"num_despac", "id"=>"num_despacID", "size"=>"10", "minlength"=>"2", "maxlength"=>"12", "validate"=>"numero") );
            $mHtml->Label( "Viaje: ", $mTD );
            $mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"num_viajex", "id"=>"num_viajexID", "size"=>"10", "end"=>true) );

            $mHtml->Label( "Placa: ", $mTD );
            $mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"num_placax", "id"=>"num_placaxID", "size"=>"10", "minlength"=>"6", "maxlength"=>"6", "validate"=>"placa") );
            $mHtml->Label( "Conductor: ", $mTD );
            $mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"nom_conduc", "id"=>"nom_conducID", "size"=>"40", "end"=>true) );

          $mHtml->CloseTable('tr');

            $mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
            $mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
            $mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
            $mHtml->Hidden( array("name"=>"cod_ciuori", "id"=>"cod_ciuoriID") );
            $mHtml->Hidden( array("name"=>"cod_ciudes", "id"=>"cod_ciudesID") );
            $mHtml->Hidden( array("name"=>"cod_conduc", "id"=>"cod_conducID") );  
            $mHtml->Hidden( array("name"=>"Option", "id"=>"OptionID") );  
        $mHtml->CloseDiv();
      $mHtml->CloseDiv();
    $mHtml->CloseDiv();

    #Tabs
    $mHtml->OpenDiv("id:tabs");
      $mHtml->SetBody('<ul>');
        $mHtml->SetBody('<li><a id="liReport" href="#tabs-report" style="cursor:pointer" onclick="report(\'report\', \'tabs-report\')">REPORTE</a></li>');
        $mHtml->SetBody('<li><a id="liSGPS" href="#tabs-SGPS" style="cursor:pointer" onclick="report(\'SGPS\', \'tabs-SGPS\')">SIN GPS</a></li>');
      $mHtml->SetBody('</ul>');

      $mHtml->SetBody('<div id="tabs-report" style="overflow: scroll;"></div>'); #DIV REPORTE
      $mHtml->SetBody('<div id="tabs-SGPS" style="overflow: scroll;"></div>'); #DIV SGPS
    $mHtml->CloseDiv();
        $mHtml->SetBody('</form>');

    echo $mHtml->MakeHtml();
  }

  /*! \fn: inform
   *  \brief: Genera el informe de Finalizados
   *  \author: Ing. Fabian Salinas
   *  \date: 25/04/2016
   *  \date modified: dd/mm/aaaa
   *  \param:    
   *  \return: 
   */
  private function inform()
  {
    $mIdxTab = "tabDespacFinali";
    $mSql = self::getData();
     

    $mHtml = new Formlib(2);
    $mHtml->SetJs( "dinamic_list");
    $mHtml->SetCss("dinamic_list");

    $mHtml->OpenDiv(["id" => $_REQUEST['ind_pestan'], "class" => "formID", "style" => "overflow: scroll;"]);
    
     $_SESSION["queryXLS"] = $mSql;

      if(!class_exists(DinamicList)) {
        include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");                         
      }
      $list = new DinamicList(self::$cConexion, $mSql, "2" , "yes", 'DESC');
      $list->SetExcel("Excel", "onclick:exportExcel('Option=exportExcel')");
      $list->SetClose('no');
      $list->SetHeader("Viaje",          "onclick:c.num_despac");
      $list->SetHeader("Despacho",       "field:a.num_despac; width:1%;  ");
      $list->SetHeader("Manifiesto",     "field:a.cod_manifi; width:1%");
      $list->SetHeader("Placa",          "field:b.num_placax; width:1%");
      $list->SetHeader("GPS",            "field:g.nom_operad" );
      $list->SetHeader("Fecha despacho", "field:a.fec_despac" );
      $list->SetHeader("Fecha salida",   "field:a.fec_salida" );
      $list->SetHeader("Conductor",      "field:IF( d.abr_tercer IS NOT NULL, d.abr_tercer , CONCAT( d.nom_tercer, ' ', d.nom_apell1, ' ', d.nom_apell2 ) )" );
      $list->SetHeader("Origen",         "field:e.nom_ciudad" );
      $list->SetHeader("Destino",        "field:f.nom_ciudad" );
      $list->SetHeader("Error",          "field:b.msg_itiner" );
      $list->SetOption("Opciones",       "onclikEdit:showDesinatarios( this );" );

      $list->Display(self::$cConexion);

      $_SESSION["DINAMIC_LIST"] = $list;      
      $mHtml->SetBody( $list -> GetHtml() );
      $mHtml->CloseDiv();

      $mHtml->OpenDiv("id:formResend; class:Style2DIV");
      $mHtml->Table('tr');
        $mHtml->Button( array("value"=>"Reenviar", "id"=>"Reenviar","name"=>"Reenviar", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"center", "onclick"=>"reenvio()") );
      $mHtml->CloseTable('tr');
      $mHtml->CloseDiv();

      echo $mHtml->MakeHtml();
  }
 
  /*! \fn: getData
   *  \brief: Trae la data del informe
   *  \author: Ing. Fabian Salinas
   *  \date: 26/04/2016
   *  \date modified: dd/mm/aaaa
   *  \param:    
   *  \return: 
   */
  private function getData(){
    $transp = getTranspPerfil(self::$cConexion,$_SESSION[datos_usuario][cod_perfil]);
    $filtransp = "";
    

    switch ($_REQUEST['ind_pestan']) {
      case 'report':
        $gps = " AND a.gps_operad IS NOT NULL";
        break;
      
      case 'SGPS':
        $gps = " AND a.gps_operad IS NULL";
        break;
    }

    $mSql = "   SELECT
                      c.num_despac AS num_viajex, 
                      a.num_despac,
                      a.cod_manifi,
                      b.num_placax,
                      g.nom_operad,
                      a.fec_despac,
                      a.fec_salida,
                      IF( d.abr_tercer IS NOT NULL, d.abr_tercer , CONCAT( d.nom_tercer, ' ', d.nom_apell1, ' ', d.nom_apell2 ) ) AS nom_conduc,
                      e.nom_ciudad AS nom_ciuori,
                      f.nom_ciudad AS nom_ciudes,
                      b.msg_itiner
              FROM 
                    ".BASE_DATOS.".tab_despac_despac a INNER JOIN 
                    ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac AND a.fec_salida IS NOT NULL AND a.fec_llegad IS NULL AND a.ind_planru = 'S' AND a.ind_anulad = 'R' AND b.ind_activo = 'S' INNER JOIN 
                    ".BASE_DATOS.".tab_despac_corona c ON a.num_despac = c.num_dessat INNER JOIN 
                    ".BASE_DATOS.".tab_tercer_tercer d ON b.cod_conduc = d.cod_tercer INNER JOIN 
                    ".BASE_DATOS.".tab_genera_ciudad e ON a.cod_ciuori = e.cod_ciudad INNER JOIN 
                    ".BASE_DATOS.".tab_genera_ciudad f ON a.cod_ciudes = f.cod_ciudad LEFT JOIN
                    ".BD_STANDA.".tab_genera_opegps g ON a.gps_operad = g.cod_operad
              WHERE
                    1 = 1
                AND ( b.cod_itiner IS NULL OR b.cod_itiner = '' )
                $gps
                /* AND ( b.msg_itiner IS NOT NULL OR b.msg_itiner != '' ) */

                ";

        $mSql .= $_REQUEST['fec_inicia'] && $_REQUEST['fec_finali'] ? " AND DATE(a.fec_despac) BETWEEN '".$_REQUEST['fec_inicia']."' AND '".$_REQUEST['fec_finali']."' " : "";
        $mSql .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = '".$_REQUEST['num_despac']."' ";
        $mSql .= !$_REQUEST['num_viajex'] ? "" : " AND c.num_despac LIKE '".$_REQUEST['num_viajex']."' ";
        $mSql .= !$_REQUEST['num_placax'] ? "" : " AND b.num_placax LIKE '".$_REQUEST['num_placax']."' ";
        $mSql .= !$_REQUEST['cod_ciuori'] ? "" : " AND a.cod_ciuori LIKE '".$_REQUEST['cod_ciuori']."' ";
        $mSql .= !$_REQUEST['cod_ciudes'] ? "" : " AND a.cod_ciudes LIKE '".$_REQUEST['cod_ciudes']."' ";
        $mSql .= !$_REQUEST['cod_conduc'] ? "" : " AND b.cod_conduc LIKE '".$_REQUEST['cod_conduc']."' ";

        if($_REQUEST['Option'] == 'setForm'){
          $mSql .= !$_REQUEST['DLSelectedRows'] ? " AND a.num_despac IN ( '' ) " : " AND a.num_despac IN ( ".str_replace('::', ',', $_REQUEST['DLSelectedRows'] )." ) ";
        }

        if ($datos_usuario["cod_perfil"] == "")
        {
          //PARA EL FILTRO DE CONDUCTOR
          $filtro = new Aplica_Filtro_Usuari(self::$cCodAplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
          if ($filtro->listar(self::$cConexion)) {
            $datos_filtro = $filtro->retornar();
            $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
          }
          //PARA EL FILTRO DE EMPRESA
          $filtro = new Aplica_Filtro_Usuari(self::$cCodAplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
          if ($filtro->listar(self::$cConexion)) {
            $datos_filtro = $filtro->retornar();
            $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
          }
          //PARA EL FILTRO DE ASEGURADORA
          $filtro = new Aplica_Filtro_Usuari(self::$cCodAplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_usuari"]);
          if ($filtro->listar(self::$cConexion)) {
            $datos_filtro = $filtro->retornar();
            $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
          }
          //PARA EL FILTRO DEL CLIENTE
          $filtro = new Aplica_Filtro_Usuari(self::$cCodAplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
          if ($filtro->listar(self::$cConexion)) {
            $datos_filtro = $filtro->retornar();
            $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
          }
          //PARA EL FILTRO DE LA AGENCIA
          $filtro = new Aplica_Filtro_Usuari(self::$cCodAplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
          if ($filtro->listar(self::$cConexion)) {
            $datos_filtro = $filtro->retornar();
            $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
          }
        }else{
          //PARA EL FILTRO DE CONDUCTOR
          $filtro = new Aplica_Filtro_Perfil(self::$cCodAplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
          if ($filtro->listar(self::$cConexion)) {
            $datos_filtro = $filtro->retornar();
            $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
          }
          //PARA EL FILTRO DE EMPRESA
          $filtro = new Aplica_Filtro_Perfil(self::$cCodAplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
          if ($filtro->listar(self::$cConexion)) {
            $datos_filtro = $filtro->retornar();
            $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
          }
          //PARA EL FILTRO DE ASEGURADORA
          $filtro = new Aplica_Filtro_Perfil(self::$cCodAplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_perfil"]);
          if ($filtro->listar(self::$cConexion)) {
            $datos_filtro = $filtro->retornar();
            $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
          }
          //PARA EL FILTRO DEL CLIENTE
          $filtro = new Aplica_Filtro_Perfil(self::$cCodAplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
          if ($filtro->listar(self::$cConexion)) {
            $datos_filtro = $filtro->retornar();
            $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
          }
          //PARA EL FILTRO DE LA AGENCIA

          $filtro = new Aplica_Filtro_Perfil(self::$cCodAplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
          if ($filtro->listar(self::$cConexion)) {
            $datos_filtro = $filtro->retornar();
            $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
          }

          if(!empty($transp)){
            $query = $query . " AND b.cod_transp = '".$transp['cod_tercer']."' ";
          }

          
        }


        // echo "<pre>"; print_r($mSql); echo "</pre>"; die();

        return $mSql;
        $mConsult = new Consulta($mSql, self::$cConexion );
        return $mConsult -> ret_matrix('a');
  }

  /*! \fn: getCiudades
   *  \brief: Trae las ciudades
   *  \author: Ing. Fabian Salinas
   *  \date: 26/04/2016
   *  \date modified: dd/mm/aaaa
   *  \param:     
   *  \return: Matriz
   */
  private function getCiudades(){
    $mSql = "SELECT a.cod_ciudad AS codex,
            CONCAT(UPPER(a.nom_ciudad), ' (', LEFT(b.nom_depart, 4), ') - ', LEFT(c.nom_paisxx, 3)) AS label, 
            CONCAT(UPPER(a.nom_ciudad), ' (', LEFT(b.nom_depart, 4), ') - ', LEFT(c.nom_paisxx, 3)) AS value 
           FROM ".BASE_DATOS.".tab_genera_ciudad a 
       INNER JOIN ".BASE_DATOS.".tab_genera_depart b 
           ON a.cod_paisxx = b.cod_paisxx 
          AND a.cod_depart = b.cod_depart 
       INNER JOIN ".BASE_DATOS.".tab_genera_paises c 
           ON a.cod_paisxx = c.cod_paisxx 
          WHERE 1=1 
      ".( !$_REQUEST['term'] ? "" : " AND (a.abr_ciudad LIKE '%$_REQUEST[term]%' OR a.cod_ciudad LIKE '$_REQUEST[term]%') " )."
         ORDER BY a.nom_ciudad ASC ";
    $mSql .= !$_REQUEST['term'] ? "" : " LIMIT 30 ";

    $mConsult = new Consulta($mSql, self::$cConexion );
    $mResult = $mConsult -> ret_matrix('a');

    if( $_REQUEST['term'] ){
      echo json_encode($mResult);
    }else{
      return $mResult;
    }
  }

  /*! \fn: getDestinatarios
   *  \brief: Obtiene destinatarios dependiendo de un n�mero de despacho
   *  \author: Ing. Edgar Felipe Clavijo Santoyo
   *  \date: 06/06/2019
   *  \date modified: dd/mm/aaaa
   *  \param:     
   *  \return: Matriz
   */
  private function getDestinatarios(){

    $result = array();

    //Get "Destinatarios" and "Remitentes"
    $query = "
      SELECT
        b.cod_latitu AS `Latitud`,
        b.cod_longit AS `Longitud`,
        b.dir_remdes AS `Direccion`,
        c.nom_ciudad AS `Ciudad`,
        b.nom_remdes AS `Nombre`,
        b.cod_remdes AS `Codigo`,
        a.num_despac AS `Despacho`
      FROM
        ".BASE_DATOS.".tab_despac_destin a
        INNER JOIN ".BASE_DATOS.".tab_genera_remdes b ON a.cod_remdes = b.cod_remdes
        INNER JOIN ".BASE_DATOS.".tab_genera_ciudad c ON a.cod_ciudad = c.cod_ciudad
      WHERE
        a.num_despac = '" . $_REQUEST["num_despac"] . "'
      GROUP BY
        a.cod_remdes
    ";

    $query = new Consulta($query, self::$cConexion );
    $result["destin"] = $query -> ret_matrix('a');

    //Get "GPS"
    $query = "
      SELECT
        n.gps_operad AS `Operador GPS`,
        n.gps_usuari AS `Usuario GPS`,
        n.gps_paswor AS `Contrasena GPS`,
        n.gps_idxxxx AS `Id GPS`
      FROM
        ".BASE_DATOS.".tab_despac_corona n
      WHERE
        n.num_dessat = '" . $_REQUEST["num_despac"] . "'
    ";

    $query = new Consulta($query, self::$cConexion );
    $result["GPS"] = $query -> ret_matrix('a');

    //Get codes GPS to list
    $query = "
      SELECT
        a.cod_operad,
        a.nom_operad
      FROM
        ".BD_STANDA.".tab_genera_opegps a
      WHERE
        ind_intgps = '1'
      GROUP BY
        a.cod_operad
      ORDER BY
        a.nom_operad
    ";

    $query = new Consulta($query, self::$cConexion );
    $result["cod_operad"] = $query -> ret_matrix('a');


    //Clean data
    $result["cod_operad"] = self::cleanArray($result["cod_operad"]);
    $result["destin"] = self::cleanArray($result["destin"]);
    $result["GPS"] = self::cleanArray($result["GPS"]);

    echo json_encode($result);
  }

  /*! \fn: updateDestinatarios
   *  \brief: Actualiza destinatarios dependiendo de un n�mero de despacho
   *  \author: Ing. Edgar Felipe Clavijo Santoyo
   *  \date: 06/06/2019
   *  \date modified: dd/mm/aaaa
   *  \param:     
   *  \return: Matriz
   */
  private function updateDestinatarios(){

    foreach ($_REQUEST["cod_remdes"] as $key => $value) {
      $query = "
        UPDATE
          ".BASE_DATOS.".tab_genera_remdes a
          INNER JOIN ".BASE_DATOS.".tab_despac_destin b ON a.cod_remdes = b.cod_remdes
        SET
          a.dir_remdes = '" . $_REQUEST["dir_remdes"][$key] . "',
          a.cod_longit = '" . $_REQUEST["cod_longit"][$key] . "',
          a.cod_latitu = '" . $_REQUEST["cod_latitu"][$key] . "',
          b.dir_destin = '" . $_REQUEST["dir_remdes"][$key] . "'
        WHERE
          a.cod_remdes = '" . $value . "'
      ";
      
      $query = new Consulta($query, self::$cConexion );
    }

    $query = "
      UPDATE
        ".BASE_DATOS.".tab_despac_corona a
        INNER JOIN ".BASE_DATOS.".tab_despac_despac b ON a.num_dessat = b.num_despac
      SET
        a.gps_operad = '" . $_REQUEST["gps_operad"] . "',
        a.gps_idxxxx = '" . $_REQUEST["gps_idxxxx"] . "',
        a.gps_usuari = '" . $_REQUEST["gps_usuari"] . "',
        a.gps_paswor = '" . $_REQUEST["gps_paswor"] . "',
        b.gps_idxxxx = '" . $_REQUEST["gps_idxxxx"] . "',
        b.gps_usuari = '" . $_REQUEST["gps_usuari"] . "',
        b.gps_paswor = '" . $_REQUEST["gps_paswor"] . "',
        b.gps_operad = '" . $_REQUEST["gps_operad"] . "'
      WHERE
        a.num_dessat= '" . $_REQUEST["num_despac"] . "'
    ";
    
    $query = new Consulta($query, self::$cConexion );

    echo "Correct";
  }

  /*! \fn: getConduc
   *  \brief: Trae los conductores activos
   *  \author: Ing. Fabian Salinas
   *  \date: 29/04/2016
   *  \date modified: dd/mm/aaaa
   *  \param: 
   *  \return: Matriz
   */
  private function getConduc(){
    $mSql = "SELECT a.cod_tercer AS codex, 
            UPPER(a.abr_tercer) AS value, 
            CONCAT(a.cod_tercer, ' - ', UPPER(a.abr_tercer)) AS label 
           FROM ".BASE_DATOS.".tab_tercer_tercer a 
       INNER JOIN ".BASE_DATOS.".tab_tercer_activi b 
           ON a.cod_tercer = b.cod_tercer 
          WHERE b.cod_activi = ".COD_FILTRO_CONDUC." 
          AND a.cod_estado = ".COD_ESTADO_ACTIVO." 
      ".( !$_REQUEST['term'] ? "" : " AND (a.abr_tercer LIKE '%$_REQUEST[term]%' OR a.cod_tercer LIKE '$_REQUEST[term]%') " )."
         ORDER BY a.abr_tercer ASC ";
    $mSql .= !$_REQUEST['term'] ? "" : " LIMIT 30 ";

    $mConsult = new Consulta($mSql, self::$cConexion );
    $mResult = $mConsult -> ret_matrix('a');

    $mResult = array_map(function($val){return array_map(function($val1){return utf8_encode($val1); },$val);},$mResult);

    if( $_REQUEST['term'] ){
      echo json_encode($mResult);
    }else{
      return $mResult;
    }
  }

  /*! \fn: setReenvio
   *  \brief: Reenvia el despacho a integrador GPS nuevamente
   *  \author: Ing. Nelson Liberato
   *  \date: 2019-05-22
   *  \date modified: dd/mm/aaaa
   *  \param: 
   *  \return: Matriz
   */
  private function setReenvio()
  {
    // revalido los despacho, depronto me meten unos despachos nada que ver, inyeccion
    $mSql =  self::getData();    
    $mConsult = new Consulta($mSql, self::$cConexion );
    $mDespachos = $mConsult -> ret_matrix('a');
     
    include("../".DIR_APLICA_CENTRAL."/lib/InterfGPS.inc");
    $mInterfGps = new InterfGPS( self::$cConexion ); 

    IncludeJS( 'inf_despac_itiner.js' );
    $mHtml = new Formlib(2);
    $mHtml->SetBody('<form name="form_infDespacErrorItinerarios" id="form_infDespacErrorItinerariosID"  method="post">');

    $mHtml->OpenDiv("id:gridReenvio; class:Style2DIV");
       $mHtml->Table('tr');
        $mens = new mensajes();      
        if( sizeof($mDespachos) > 0 )    
        {
          foreach ($mDespachos AS $mIndex => $mDespacho) 
          {
            $mResp = $mInterfGps -> setPlacaIntegradorGPS( $mDespacho['num_despac'], ['ind_transa' => 'I'] );
            //code_resp, msg_resp
            if($mResp['code_resp'] == '1000')
            {
              $mens -> correcto("Reenvio despacho: ".$mDespacho['num_despac'].", Placa:".$mDespacho['num_placax'],'Este es un envio asincrono al integrador GPS<br><b>Respuesta:</b> '.$mResp['msg_resp']);
            }
            else
            {
              $mens -> error("Reenvio despacho: ".$mDespacho['num_despac'].", Placa:".$mDespacho['num_placax'],'Este es un envio asincrono al integrador GPS<br><b>Respuesta:</b> '.$mResp['msg_resp']);
            }

            unset($mResp);
          }
        }
        else
        {
          $mens -> error("Reenvio Despachos", "No hay despachos seleccionados a enviar, intentelo de nuevo!!!");
        }
        

      $mHtml->CloseTable('tr');
    $mHtml->CloseDiv();

    $mHtml->OpenDiv("id:buttonGoBack; class:Style2DIV");
      $mHtml->Table('tr');
        $mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
        $mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>"central") );
        $mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
        $mHtml->Hidden( array("name"=>"Option", "id"=>"OptionID") );  
        $mHtml->Button( array("value"=>"Volver", "id"=>"VolverID","name"=>"Volver", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"center", "onclick"=>"goBack()") );
      $mHtml->CloseTable('tr');
    $mHtml->CloseDiv();
    $mHtml->SetBody('</form>');

    echo $mHtml->MakeHtml();

  }

  /*! \fn: exportExcel
   *  \brief: Exporta la tabla a excel
   *  \author: Ing. Luis Manrique
   *  \date: 20/08/2019
   *  \date modified: dd/mm/aaaa
   *  \param:     
   *  \return: Matriz
   */
  private function exportExcel(){

    $mSql = $_SESSION["queryXLS"];    
    $mConsult = new Consulta($mSql, self::$cConexion );
    $mDespachos = $mConsult -> ret_matrix('a');

    $table = "";
  
    $table .='<table id="exportData"><tr>';
      $table .='<th>Viaje</th>';
      $table .='<th>Despacho</th>';
      $table .='<th>Manifiesto</th>';
      $table .='<th>Placa</th>';
      $table .='<th>Fecha despacho</th>';
      $table .='<th>Fecha salida</th>';
      $table .='<th>Conductor</th>';
      $table .='<th>Origen</th>';
      $table .='<th>Destino</th>';
      $table .='<th>Error</th>';
    $table .='</tr>';
    foreach ($mDespachos as $key => $value) {
      $table .='<tr>';
        $table .="<td>".$value['num_viajex']."</td>";
        $table .="<td>".$value['num_despac']."</td>";
        $table .="<td>".$value['cod_manifi']."</td>";
        $table .="<td>".$value['num_placax']."</td>"; 
        $table .="<td>".$value['fec_despac']."</td>";
        $table .="<td>".$value['fec_salida']."</td>"; 
        $table .="<td>".$value['nom_conduc']."</td>"; 
        $table .="<td>".$value['nom_ciuori']."</td>"; 
        $table .="<td>".$value['nom_ciudes']."</td>"; 
        $table .="<td>".$value['msg_itiner']."</td>"; 
      $table .='</tr>';
    }
    
    $table .= "</table>";

    header('Content-type: application/vnd.ms-excel');
    header('Content-type: application/x-msexcel');
    header("Content-Disposition: attachment; filename=Reporte Hojas de vida.xls");
    header("Pragma: nopreg_replace-cache");
    header("Expires: 0");
    echo $table;
  }

  function cleanArray($array){

    $arrayReturn = array();

    //Go through data
    foreach ($array as $key => $value) {
        
        //Validate sub array
        if(is_array($value)){
            
            //Clean sub array
            $arrayReturn[utf8_encode($key)] = self::cleanArray($value);

        }else{
            
            //Clean value
            $arrayReturn[utf8_encode($key)] = utf8_encode($value);

        }

    }

    //Return array
    return $arrayReturn;

}

}

if($_REQUEST['Ajax'] === 'on' )
  $_INFORM = new infDespacErrorItinerarios();
else
  $_INFORM = new infDespacErrorItinerarios( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );


?>