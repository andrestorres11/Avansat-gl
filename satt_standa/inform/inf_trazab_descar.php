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

class InfTrazabDescar
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cDespac,
					$cNull = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null) {
		if($_REQUEST['Ajax'] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
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

			default:
				self::formulario();
				break;
		}
	}

	private function getTipTransp(){

		$sql = "SELECT a.cod_tipdes, a.nom_tipdes
				  FROM ".BASE_DATOS.".tab_genera_tipdes a
				 where 1=1";

		$mConsult = new Consulta($sql, self::$cConexion );
		return $mConsult -> ret_matrix('i');

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
		$mTransp = self::$cDespac -> getTransp();
		$mClient = array_merge(self::$cNull, self::getClient());
		$tipTra = self::getTipTransp();

		if( sizeof($mTransp) != 1 ){
			$mTransp = array_merge(self::$cNull, $mTransp);
		}

		$temp = array(
					 	array("0","Finalizados"),
					  	array("1","Transito")
					  );

		$estado = array_merge(self::$cNull,$temp);
		$tipTra = array_merge(self::$cNull,$tipTra);

		IncludeJS( 'jquery.js' );
		IncludeJS( 'jquery.blockUI.js' );
		IncludeJS( 'inf_trazab_descar.js' );
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

		$mHtml->CloseTable('tr');

		#Acordion
		$mHtml->OpenDiv("class:accordion");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h3>");
			$mHtml->OpenDiv("id:secID");
				$mHtml->SetBody('<form name="form_InfTrazabDespac" id="form_InfTrazabDespacID" action="../'.DIR_APLICA_CENTRAL.'/lib/exportExcel.php" method="post">');
				$mHtml->OpenDiv("id:formID; class:Style2DIV");

					$mHtml->Table('tr');
						$mHtml->SetBody('<tr><th class="CellHead" colspan="4" style="text-align:left">Filtros Generales</th></tr>');

						$mHtml->Label( $mAs."Transportadoras: ", $mTD );
						$mHtml->Select2( $mTransp, array("name"=>"cod_transp", "id"=>"cod_transpID", "width"=>"20%", "class"=>"cellInfo1 multiSel") );


						$mHtml->Label( $mAs."Tipo de despacho: ", $mTD );
						$mHtml->Select2( $tipTra, array("name"=>"cod_tiptra", "id"=>"cod_tiptraID", "width"=>"50%", "class"=>"cellInfo1 multiSel", "end"=>true) );
					$mHtml->CloseTable('tr');

					$mHtml->Table('tr');
						$mHtml->Label( "Fecha Inicial: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"35%", "name"=>"fec_inicia", "id"=>"fec_iniciaID", "size"=>"10") );
						$mHtml->Label( "Fecha Final: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"25%", "name"=>"fec_finali", "id"=>"fec_finaliID", "size"=>"10") );

						$mHtml->Label( $mAs."Estado: ", $mTD );
						$mHtml->Select2( $estado, array("name"=>"ind_estado", "id"=>"ind_estadoID", "width"=>"25%", "class"=>"cellInfo1 multiSel") );

					$mHtml->CloseTable('tr');

					$mHtml->Table('tr');
						$mHtml->SetBody('<tr><th class="CellHead" colspan="4" style="text-align:left">Filtros Especificos</th></tr>');
					$mHtml->CloseTable('tr');
					$mHtml->Table('tr');
						$mHtml->Label( "Despacho: ",array("class"=>"cellInfo1", "width"=>"5%") );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"20%", "name"=>"num_despac", "id"=>"num_despacID", "size"=>"10", "minlength"=>"2", "maxlength"=>"12", "validate"=>"numero") );
						$mHtml->Label( "Viaje: ",array("class"=>"cellInfo1", "width"=>"5%") );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"20%", "name"=>"num_viajex", "id"=>"num_viajexID", "size"=>"10") );

						$mHtml->Label( "Placa: ",array("class"=>"cellInfo1", "width"=>"5%") );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"20%", "name"=>"num_placax", "id"=>"num_placaxID", "size"=>"10", "minlength"=>"6", "maxlength"=>"6", "validate"=>"placa") );
						$mHtml->Label( "Nº Factura: ",array("class"=>"cellInfo1", "width"=>"5%") );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"20%", "name"=>"num_factur", "id"=>"num_facturID", "size"=>"10") );
					$mHtml->CloseTable('tr');

						$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
						$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
						$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
						$mHtml->Hidden( array("name"=>"cod_ciuori", "id"=>"cod_ciuoriID") );
						$mHtml->Hidden( array("name"=>"cod_ciudes", "id"=>"cod_ciudesID") );
						$mHtml->Hidden( array("name"=>"cod_conduc", "id"=>"cod_conducID") );
						$mHtml->Hidden( array("name"=>"nameFile", "id"=>"nameFileID", "value"=>"Informe_TrazabilidadDespachos_".date('YmdHi') ) );
						$mHtml->Hidden( array("name"=>"OptionExcel", "id"=>"OptionExcelID", "value"=>"_REQUEST") );
						$mHtml->Hidden( array("name"=>"exportExcel", "id"=>"exportExcelID", "value"=>"") );
				$mHtml->CloseDiv();
				$mHtml->SetBody('</form>');
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		#Tabs
		$mHtml->OpenDiv("id:tabs");
			$mHtml->SetBody('<ul>');
				$mHtml->SetBody('<li><a id="liReport" href="#tabs-report" style="cursor:pointer" onclick="report(\'report\', \'tabs-report\')">REPORTE</a></li>');
			$mHtml->SetBody('</ul>');

			$mHtml->SetBody('<div id="tabs-report"></div>'); #DIV REPORTE
		$mHtml->CloseDiv();

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
	private function inform(){
		$flag = 0;
		if(!$_REQUEST['idTable']){
			$tabla = 'tabDespacFinali';
		}else{
			$tabla = $_REQUEST['idTable'];
		}

		$mIdxTab = $tabla;


		if(!$_REQUEST['indDetalle']){
			$flag = 1;

			$mData2 = self::getData("head");
			$mSize2 = sizeof($mData);

			$mTitl2 = array( "num_consec" => "#",
							"can_despac" => "# Despachos",
							"can_client" => "# clientes",
							"can_cumdes" => "# Cumplidos",
							"por_llegad" => "% Cumplidos",
							"can_inides" => "# Inicia Descargue",
							"por_inicio" => "% Inicia Descargue",
							"can_findes" => "# Fin Descargue",
							"por_finxxx" => "% Fin Descargue",
						  );

			$mData = self::getData();
			$mSize = sizeof($mData);

			$mTitl = array( "num_consec" => "#",
							"fec_despac" => "Fecha Despacho",
							"can_despac" => "# Despachos",
							"can_client" => "# clientes",
							"can_cumdes" => "# Cumplidos",
							"por_llegad" => "% Cumplidos",
							"can_inides" => "# Inicia Descargue",
							"por_inicio" => "% Inicia Descargue",
							"can_findes" => "# Fin Descargue",
							"por_finxxx" => "% Fin Descargue",
						  );
		}else{

			$mData = self::getData("detail");
			$mSize = sizeof($mData);

			$mTitl = array( "num_consec" => "#",
							"num_despac" => "Despacho",
							"num_desext" => "Viaje",
							"nom_tipdes" => "Tipo Despacho",
							"num_solici" => "# Solicitud",
							"num_docume" => "# Documento",
							"origen" => "Origen",
							"destino" => "Destino",
							"abr_tercer" => "Conductor",
							"cod_tercer" => "Documento Conductor",
							"nom_tiptra" => "Tipo Transporte",
							"nom_destin" => "Cliente",
							"fec_llecli" => "Fecha Llegada",
							"inicia_descargue" => "Inicia Descargue",
							"fin_descargue" => "# Fin Descargue",
							"diferencia" => "# Diferencia",
						  );
		}


		$mHtml = new Formlib(2);

		$mHtml->OpenDiv("id:formID; class:Style2DIV");

		if( $mSize < 1 ){
			$mHtml->Table('tr');
				$mHtml->Label( "No se encontraron Registros.", array("class"=>"cellInfo1", "align"=>"left") );
			$mHtml->CloseTable('tr');
		}else{

			$mHtml->SetBody('<center>
								<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" onclick="expTabExcelDespacFinali( \''.$mIdxTab.'\' );" style="cursor:pointer">
							 </center><br>');
			if($flag == 1){

				$mHtml->SetBody('<table id="'.$mIdxTab.'" width="80%" cellspacing="1" cellpadding="3" border="0" align="center"><tbody><tr>');

				foreach ($mTitl2 as $key => $tit) {
					$mHtml->Label( $tit, array("class"=>"CellHead") );
				}
				$mHtml->CloseRow('tr');

				$i=1;

				foreach ($mData2 as $row) {
					if( $i % 2 == 1 ){
						$mClass = 'cellInfo1';
					}else{
						$mClass = 'cellInfo2';
					}

					$row['num_consec'] = $i;

					foreach ($mTitl2 as $key => $tit) {
						$mHtml->Label( utf8_encode($row[$key]), array("class"=>"$mClass izquierda") );
					}
					$mHtml->CloseRow('tr');
					$i++;
				}

				$mHtml->SetBody('</table>');

			}

			$mHtml->SetBody("<label style='color: black'>Se encontraron $mSize registros.</label><br>");

			$mHtml->SetBody('<table id="'.$mIdxTab.'" width="100%" cellspacing="1" cellpadding="3" border="0" align="center"><tbody><tr>');

			foreach ($mTitl as $key => $tit) {
				$mHtml->Label( $tit, array("class"=>"CellHead") );
			}
			$mHtml->CloseRow('tr');

			$i=1;

			foreach ($mData as $row) {
				if( $i % 2 == 1 ){
					$mClass = 'cellInfo1';
				}else{
					$mClass = 'cellInfo2';
				}

				$row['num_consec'] = $i;
				$row['num_despac'] = '<a style="color:black" href="index.php?cod_servic=3302&window=central&despac='.$row['num_despac'].'&tie_ultnov=0&opcion=1">'.$row['num_despac'].'</a>';
				$row['can_despac'] = '<a style="color:black" href="#" onclick="detailData(\''.$row[fec_despac].'\')">'.$row['can_despac'].'</a>';


				foreach ($mTitl as $key => $tit) {
					$mHtml->Label( utf8_encode($row[$key]), array("class"=>"$mClass izquierda") );
				}
				$mHtml->CloseRow('tr');
				$i++;
			}

			$mHtml->SetBody('</table>');

		}

		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getClient
	 *  \brief: Trae los clientes
	 *  \author: Ing. Fabian Salinas
	 *  \date: 26/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:
	 *  \return: Matriz
	 */
	private function getClient(){
		$mSql = "SELECT e.cod_tercer,e.abr_tercer
				   FROM ".BASE_DATOS.".tab_despac_despac a
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige d
					 ON a.num_despac = d.num_despac
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e
					 ON a.cod_client = e.cod_tercer
			 INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu i
					 ON i.num_placax = d.num_placax
				  WHERE a.fec_salida IS NOT NULL
					AND a.fec_salida <= NOW()
					AND a.fec_llegad IS NOT NULL
					AND a.ind_anulad = 'R'
					AND a.ind_planru = 'S' ";

		if ($datos_usuario["cod_perfil"] == "") {
			//PARA EL FILTRO DE CONDUCTOR
			$filtro = new Aplica_Filtro_Usuari(self::$cCodAplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
			}
			//PARA EL FILTRO DE PROPIETARIO
			$filtro = new Aplica_Filtro_Usuari(self::$cCodAplica, COD_FILTRO_PROPIE, $datos_usuario["cod_usuari"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
			}
			//PARA EL FILTRO DE POSEEDOR
			$filtro = new Aplica_Filtro_Usuari(self::$cCodAplica, COD_FILTRO_POSEED, $datos_usuario["cod_usuari"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
			}
			//PARA EL FILTRO DE EMPRESA
			$filtro = new Aplica_Filtro_Usuari(self::$cCodAplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
			}
			//PARA EL FILTRO DEL CLIENTE
			$filtro = new Aplica_Filtro_Usuari(self::$cCodAplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
			}
			//PARA EL FILTRO DE LA AGENCIA
			$filtro = new Aplica_Filtro_Usuari(self::$cCodAplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
			}
		} else {
			//PARA EL FILTRO DE CONDUCTOR
			$filtro = new Aplica_Filtro_Perfil(self::$cCodAplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
			}
			//PARA EL FILTRO DE PROPIETARIO
			$filtro = new Aplica_Filtro_Perfil(self::$cCodAplica, COD_FILTRO_PROPIE, $datos_usuario["cod_perfil"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
			}
			//PARA EL FILTRO DE POSEEDOR
			$filtro = new Aplica_Filtro_Perfil(self::$cCodAplica, COD_FILTRO_POSEED, $datos_usuario["cod_perfil"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
			}
			//PARA EL FILTRO DE EMPRESA
			$filtro = new Aplica_Filtro_Perfil(self::$cCodAplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
			}
			//PARA EL FILTRO DEL CLIENTE
			$filtro = new Aplica_Filtro_Perfil(self::$cCodAplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
			}
			//PARA EL FILTRO DE LA AGENCIA
			$filtro = new Aplica_Filtro_Perfil(self::$cCodAplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
			if ($filtro->listar(self::$cConexion)) {
				$datos_filtro = $filtro->retornar();
				$mSql .= " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
			}
		}

		$mSql .= " GROUP BY 1 ORDER BY 2";

		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}

	/*! \fn: getData
	 *  \brief: Trae la data del informe
	 *  \author: Ing. Fabian Salinas
	 *  \date: 26/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:
	 *  \return:
	 */
	private function getData( $tipoInforme = NULL ){
		$condicion = "";

		if( $_REQUEST['ind_estado'] == "0" ){

			$condicion = "AND (a.fec_llegad IS NOT NULL OR a.fec_llegad != '0000-00-00 00:00:00')";

		}else if( $_REQUEST['ind_estado'] == "1" ){

			$condicion = "AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')";
		}


		if( $_REQUEST['fec_inicia'] && $_REQUEST['fec_finali'] ){

			$condicion .= " AND DATE(a.fec_despac) BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]'";
		}

		if ($_REQUEST['fec_filtro']) {

			$condicion .= " AND DATE(a.fec_despac) BETWEEN DATE('$_REQUEST[fec_filtro]') AND DATE('$_REQUEST[fec_filtro]')";
		}
		if($_REQUEST['cod_tiptra']){
			$condicion .= " AND e.cod_tipdes IN ( " . $_REQUEST['cod_tiptra'] . ")";
		}
		$numFactur = join(explode(",", $_REQUEST['num_factur']),"','");

		$condicion .= !$_REQUEST['num_factur'] ? "" : " AND c.num_docume IN ('$numFactur') ";
		$condicion .= !$_REQUEST['num_viajex'] ? "" : " AND d.num_desext LIKE '$_REQUEST[num_viajex]' ";
		$condicion .= !$_REQUEST['num_placax'] ? "" : " AND b.num_placax LIKE '$_REQUEST[num_placax]' ";
		$condicion .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = '$_REQUEST[num_despac]' ";

		$mSql = " SELECT y.fec_despac,
			    COUNT(DISTINCT y.num_despac) AS can_despac,
				SUM(can_client) AS can_client,
				SUM(can_cumdes) AS can_cumdes,
				SUM(can_inides) AS can_inides,
				SUM(can_findes) AS can_findes,
				ROUND(((SUM(can_cumdes) * 100)/SUM(can_client)), 2) AS por_llegad,
				ROUND(((SUM(can_inides) * 100)/SUM(can_client)), 2) AS por_inicio,
				ROUND(((SUM(can_findes) * 100)/SUM(can_client)), 2) AS por_finxxx
				FROM (
					SELECT x.num_despac, x.fec_despac,
					COUNT(DISTINCT x.nom_destin) AS can_client,
					SUM(x.fec_cumdes) AS can_cumdes,
					SUM(x.fec_inides) AS can_inides,
					SUM(x.fec_findes) AS can_findes
					FROM (
						SELECT a.num_despac, c.nom_destin,
								DATE(a.fec_despac) AS fec_despac,
								IF(c.fec_cumdes IS NULL, 0, 1) AS fec_cumdes,
								IF(c.fec_inides IS NULL, 0, 1) AS fec_inides,
								IF(c.fec_findes IS NULL, 0, 1) AS fec_findes
								FROM ".BASE_DATOS.".tab_despac_despac a
						  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
						   LEFT JOIN ".BASE_DATOS.".tab_despac_destin c ON a.num_despac = c.num_despac
						   LEFT JOIN ".BASE_DATOS.".tab_despac_sisext d ON a.num_despac = d.num_despac
					      INNER JOIN ".BASE_DATOS.".tab_genera_tipdes e ON a.cod_tipdes = e.cod_tipdes
						  INNER JOIN ".BASE_DATOS.".tab_tercer_tercer i ON b.cod_conduc = i.cod_tercer
							   WHERE a.fec_salida IS NOT NULL
								 AND a.fec_salida <= NOW()
								 AND a.ind_planru = 'S'
								 AND a.ind_anulad = 'R'
								 AND b.ind_activo = 'S'
								 AND b.cod_transp = '$_REQUEST[cod_transp]'
							     $condicion
							GROUP BY a.num_despac, c.nom_destin
						) x
					GROUP BY x.num_despac
					) y
				GROUP BY y.fec_despac";

		if ( $tipoInforme == "detail" ) {

			$mSql = "SELECT x.*
						FROM (
							SELECT a.num_despac, c.nom_destin,
									DATE(a.fec_despac) AS fec_despac,
									IF(c.fec_cumdes IS NULL, 0, 1) AS fec_cumdes,
									IF(c.fec_inides IS NULL, 0, 1) AS fec_inides,
									IF(c.fec_findes IS NULL, 0, 1) AS fec_findes,
									d.num_desext,
									e.nom_tipdes,
									f.num_solici,
									c.num_docume,
									g.nom_ciudad AS origen,
									h.nom_ciudad AS destino,
									i.abr_tercer,
									i.cod_tercer,
									j.nom_tiptra,
									c.fec_cumdes AS fec_llecli,
									c.fec_inides AS inicia_descargue,
									c.fec_findes AS fin_descargue,
									TIMEDIFF(TIME(c.fec_findes), TIME(c.fec_inides)) AS diferencia,
									c.nom_genera
									FROM ".BASE_DATOS.".tab_despac_despac a
							  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
							   LEFT JOIN ".BASE_DATOS.".tab_despac_destin c ON a.num_despac = c.num_despac
							   LEFT JOIN ".BASE_DATOS.".tab_despac_sisext d ON a.num_despac = d.num_despac
							  INNER JOIN ".BASE_DATOS.".tab_genera_tipdes e ON a.cod_tipdes = e.cod_tipdes
							   LEFT JOIN ".BASE_DATOS.".tab_despac_corona f ON d.num_desext = f.num_despac
							  INNER JOIN ".BASE_DATOS.".tab_genera_ciudad g ON a.cod_ciuori = g.cod_ciudad
							  INNER JOIN ".BASE_DATOS.".tab_genera_ciudad h ON a.cod_ciudes = h.cod_ciudad
							  INNER JOIN ".BASE_DATOS.".tab_tercer_tercer i ON b.cod_conduc = i.cod_tercer
							   LEFT JOIN ".BASE_DATOS.".tab_genera_tiptra j ON f.tip_transp = j.cod_tiptra
								   WHERE a.fec_salida IS NOT NULL
									 AND a.fec_salida <= NOW()
									 AND a.ind_planru = 'S'
									 AND a.ind_anulad = 'R'
									 AND b.ind_activo = 'S'
									 AND b.cod_transp = '$_REQUEST[cod_transp]'
								     $condicion
								GROUP BY a.num_despac, c.nom_destin
							) x
							GROUP BY x.num_despac
						 ";
		}

		else if( $tipoInforme == "head" ){

			$mSql = "SELECT SUM(w.can_despac) AS can_despac,
							SUM(w.can_client) AS can_client,
							SUM(w.can_cumdes) AS can_cumdes,
							SUM(w.can_inides) AS can_inides,
							SUM(w.can_findes) AS can_findes,

							ROUND(((SUM(w.can_cumdes) * 100)/SUM(w.can_client)), 2) AS por_llegad,
							ROUND(((SUM(w.can_inides) * 100)/SUM(w.can_client)), 2) AS por_inicio,
							ROUND(((SUM(w.can_findes) * 100)/SUM(w.can_client)), 2) AS por_finxxx
					 FROM (
						SELECT y.fec_despac,
						    COUNT(DISTINCT y.num_despac) AS can_despac,
							SUM(can_client) AS can_client,
							SUM(can_cumdes) AS can_cumdes,
							SUM(can_inides) AS can_inides,
							SUM(can_findes) AS can_findes
							FROM (
								SELECT x.num_despac, x.fec_despac,
								COUNT(DISTINCT x.nom_destin) AS can_client,
								SUM(x.fec_cumdes) AS can_cumdes,
								SUM(x.fec_inides) AS can_inides,
								SUM(x.fec_findes) AS can_findes
								FROM (
									SELECT a.num_despac, c.nom_destin,
											DATE(a.fec_despac) AS fec_despac,
											IF(c.fec_cumdes IS NULL, 0, 1) AS fec_cumdes,
											IF(c.fec_inides IS NULL, 0, 1) AS fec_inides,
											IF(c.fec_findes IS NULL, 0, 1) AS fec_findes
											FROM ".BASE_DATOS.".tab_despac_despac a
									  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
									   LEFT JOIN ".BASE_DATOS.".tab_despac_destin c ON a.num_despac = c.num_despac
									   LEFT JOIN ".BASE_DATOS.".tab_despac_sisext d ON a.num_despac = d.num_despac
							  		  INNER JOIN ".BASE_DATOS.".tab_genera_tipdes e ON a.cod_tipdes = e.cod_tipdes
									  INNER JOIN ".BASE_DATOS.".tab_tercer_tercer i ON b.cod_conduc = i.cod_tercer
										   WHERE a.fec_salida IS NOT NULL
											 AND a.fec_salida <= NOW()
											 AND a.ind_planru = 'S'
											 AND a.ind_anulad = 'R'
											 AND b.ind_activo = 'S'
											 AND b.cod_transp = '$_REQUEST[cod_transp]'
										     $condicion
										GROUP BY a.num_despac, c.nom_destin
									) x
								GROUP BY x.num_despac
								) y
							GROUP BY y.fec_despac
						) w	";

		}

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
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new InfTrazabDescar();
else
	$_INFORM = new InfTrazabDescar( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );


?>
