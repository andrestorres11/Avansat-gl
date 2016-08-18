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

class infDespacFinali
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

		if( sizeof($mTransp) != 1 ){
			$mTransp = array_merge(self::$cNull, $mTransp);
		}


		IncludeJS( 'jquery.js' );
		IncludeJS( 'jquery.blockUI.js' );
		IncludeJS( 'inf_despac_finali.js' );
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
				$mHtml->SetBody('<form name="form_InfDespacFinali" id="form_InfDespacFinaliID" action="../'.DIR_APLICA_CENTRAL.'/lib/exportExcel.php" method="post">');
				$mHtml->OpenDiv("id:formID; class:Style2DIV");

					$mHtml->Table('tr');
						$mHtml->SetBody('<tr><th class="CellHead" colspan="4" style="text-align:left">Filtros Generales</th></tr>');

						$mHtml->Label( $mAs."Transportadoras: ", $mTD );
						$mHtml->Select2( $mTransp, array("name"=>"cod_transp", "id"=>"cod_transpID", "width"=>"30%", "class"=>"cellInfo1 multiSel") );
						$mHtml->Label( "Generador: ", $mTD );
						$mHtml->Select2( $mClient, array("name"=>"cod_client", "id"=>"cod_clientID", "width"=>"30%", "class"=>"cellInfo1 multiSel", "end"=>true) );

						$mHtml->Label( "Ciudad Origen: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"nom_ciuori", "id"=>"nom_ciuoriID", "size"=>"40") );
						$mHtml->Label( "Ciudad Destino: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"nom_ciudes", "id"=>"nom_ciudesID", "size"=>"40", "end"=>true) );

						$mHtml->Label( "Fecha Inicial: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"fec_inicia", "id"=>"fec_iniciaID", "size"=>"10") );
						$mHtml->Label( "Fecha Final: ", $mTD );
						$mHtml->Input( array("class"=>"cellInfo1", "width"=>"30%", "name"=>"fec_finali", "id"=>"fec_finaliID", "size"=>"10") );
					$mHtml->CloseTable('tr');

					$mHtml->Table('tr');
						$mHtml->SetBody('<tr><th class="CellHead" colspan="4" style="text-align:left">Filtros Especificos</th></tr>');

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
						$mHtml->Hidden( array("name"=>"nameFile", "id"=>"nameFileID", "value"=>"Informe_DespachoFinalizados_".date('YmdHi') ) );
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
		$mIdxTab = "tabDespacFinali";
		$mData = self::getData();
		$mSize = sizeof($mData);
		$mTitl = array( "num_consec" => "#",
						"num_despac" => "Despacho",
						"cod_manifi" => "Manifiesto",
						"num_viajex" => "Viaje",
						"nom_ciuori" => "Origen",
						"nom_ciudes" => "Destino",
						"nom_transp" => "Transportadora",
						"num_placax" => "Placa",
						"nom_conduc" => "Conductor",
						"num_celcon" => "Celular",
						"nom_client" => "Generador",
						"nom_novsit" => "Ult. Novedad (Sitio)", 
						"fec_novsit" => "Fecha Ult. Novedad (Sitio)", 
						"nom_novant" => "Ult. Novedad (Antes)", 
						"fec_novant" => "Fecha Ult. Novedad (Antes)", 
						"obs_llegad" => "Observacion Llegada", 
						"fec_llegad" => "Fecha Llegada", 
					  );

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
	private function getData(){
		$mSql = "SELECT f.abr_tercer AS nom_transp, 
						o.abr_tercer AS nom_client, 
						n.abr_tercer AS nom_asegur, /*Nombre aseguradora*/
						a.num_despac, a.cod_manifi, d.num_placax, 
						a.fec_llegad, a.obs_llegad, 
						IF(m.num_desext IS NULL,'N/A' ,m.num_desext ) as num_viajex, 
						IF(a.con_telmov IS NULL, e.num_telmov, a.con_telmov ) AS num_celcon, 
						IF(d.nom_conduc IS NOT NULL, CONCAT(d.cod_conduc, ' - ', d.nom_conduc), CONCAT(d.cod_conduc, ' - ', e.abr_tercer)) AS nom_conduc, 
						CONCAT(UPPER(g.nom_ciudad), ' (', LEFT(h.nom_depart, 4), ') - ', LEFT(i.nom_paisxx, 3)) AS nom_ciuori, 
						CONCAT(UPPER(j.nom_ciudad), ' (', LEFT(k.nom_depart, 4), ') - ', LEFT(l.nom_paisxx, 3)) AS nom_ciudes, 
						(	SELECT aa.fec_noveda 
							  FROM ".BASE_DATOS.".tab_despac_noveda aa 
							 WHERE aa.num_despac = a.num_despac 
						  ORDER BY aa.fec_creaci DESC 
							 LIMIT 1 
						) AS fec_novsit, 
						(	SELECT bb.fec_contro 
							  FROM ".BASE_DATOS.".tab_despac_contro bb 
							 WHERE bb.num_despac = a.num_despac 
						  ORDER BY bb.fec_creaci DESC 
							 LIMIT 1 
						) AS fec_novant, 
						(	  SELECT dd.nom_noveda 
								FROM ".BASE_DATOS.".tab_despac_noveda cc 
						  INNER JOIN ".BASE_DATOS.".tab_genera_noveda dd 
								  ON cc.cod_noveda = dd.cod_noveda 
							   WHERE cc.num_despac = a.num_despac 
							ORDER BY cc.fec_creaci DESC 
							   LIMIT 1 
						) AS nom_novsit, 
						(	  SELECT ff.nom_noveda 
								FROM ".BASE_DATOS.".tab_despac_contro ee 
						  INNER JOIN ".BASE_DATOS.".tab_genera_noveda ff 
								  ON ee.cod_noveda = ff.cod_noveda 
							   WHERE ee.num_despac = a.num_despac 
							ORDER BY ee.fec_creaci DESC 
							   LIMIT 1 
						) AS nom_novant 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige d 
					 ON a.num_despac = d.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e 
					 ON d.cod_conduc = e.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer f 
					 ON d.cod_transp = f.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad g 
					 ON a.cod_paiori = g.cod_paisxx 
					AND a.cod_depori = g.cod_depart 
					AND a.cod_ciuori = g.cod_ciudad 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart h 
					 ON a.cod_paiori = h.cod_paisxx 
					AND a.cod_depori = h.cod_depart 
			 INNER JOIN ".BASE_DATOS.".tab_genera_paises i 
					 ON a.cod_paiori = i.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad j 
					 ON a.cod_paides = j.cod_paisxx 
					AND a.cod_depdes = j.cod_depart 
					AND a.cod_ciudes = j.cod_ciudad 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart k 
					 ON a.cod_paides = k.cod_paisxx 
					AND a.cod_depdes = k.cod_depart 
			 INNER JOIN ".BASE_DATOS.".tab_genera_paises l 
					 ON a.cod_paides = l.cod_paisxx 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_sisext m 
					 ON a.num_despac = m.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer n 
					 ON n.cod_tercer = a.cod_asegur 
			  LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer o 
					 ON a.cod_client = o.cod_tercer 
				  WHERE a.fec_salida IS NOT NULL 
					AND a.ind_anulad = 'R' 
					AND a.ind_planru = 'S' 
					AND a.fec_llegad IS NOT NULL 
					AND d.cod_transp IN ($_REQUEST[cod_transp]) 
				";

		$mSql .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = '$_REQUEST[num_despac]' ";
		$mSql .= !$_REQUEST['cod_ciuori'] ? "" : " AND a.cod_ciuori IN ($_REQUEST[cod_ciuori]) ";
		$mSql .= !$_REQUEST['cod_ciudes'] ? "" : " AND a.cod_ciudes IN ($_REQUEST[cod_ciudes]) ";
		$mSql .= !$_REQUEST['cod_conduc'] ? "" : " AND d.cod_conduc IN ($_REQUEST[cod_conduc]) ";
		$mSql .= !$_REQUEST['cod_client'] ? "" : " AND a.cod_client IN ($_REQUEST[cod_client]) ";
		$mSql .= !$_REQUEST['num_viajex'] ? "" : " AND m.num_desext LIKE '$_REQUEST[num_viajex]' ";
		$mSql .= !$_REQUEST['num_placax'] ? "" : " AND d.num_placax LIKE '$_REQUEST[num_placax]' ";
		$mSql .= $_REQUEST['fec_inicia'] && $_REQUEST['fec_finali'] ? " AND DATE(a.fec_llegad) BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]' " : "";
		


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
		}

		$mSql .= " ORDER BY a.fec_llegad ASC ";

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
	$_INFORM = new infDespacFinali();
else
	$_INFORM = new infDespacFinali( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );


?>