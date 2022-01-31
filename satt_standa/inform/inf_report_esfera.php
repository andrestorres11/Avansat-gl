<?php
/*! \file: inf_report_esfera.php
 *  \brief: Archivo para el informe de "Informes > Gestion operacion > Ind. de Registros Ideales "
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 2.0
 *  \date: 18/04/2016
 *  \bug: 
 *  \warning: 
 */

class InfReportEspera
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
			@include_once( '../' . DIR_APLICA_CENTRAL . '/inform/class_despac_trans3.php' );

			self::$cDespac = new Despac( $co, $us, $ca );
			self::$cConexion  = $co;
			self::$cUsuario   = $us;
			self::$cCodAplica = $ca;
		}

		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );

		switch($_REQUEST['Option']){
			case 'informGeneral':
				self::informGeneral();
				break;

			case 'infDetail':
				self::infDetail();
				break;

			default:
				self::formulario();
				break;
		}
	}

	/*! \fn: formulario
	 *  \brief: Formulario para aplicar los filtros del informe
	 *  \author: Ing. Fabian Salinas
	 *  \date: 05/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function formulario(){
		$mTransp = self::$cDespac -> getTransp();
		$mContro = self::getEsferas();
		$mTD = array("class"=>"cellInfo1", "width"=>"25%");

		if( sizeof($mTransp) != 1 ){
			$mTransp = array_merge(self::$cNull, $mTransp);
			$mDisabl = false;
		}else{
			$mDisabl = true;
		}


		IncludeJS( 'jquery.js' );
		IncludeJS( 'jquery.blockUI.js' );
		IncludeJS( 'inf_report_esfera.js' );
		IncludeJS( 'functions.js' );
		IncludeJS( 'validator.js' );
		IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
		IncludeJS( 'jquery.multiselect.filter.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
		
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";

		$mHtml = new Formlib(2);
		
		$mHtml->SetCss("jquery");
		$mHtml->SetCss("informes");
		$mHtml->SetCss("validator");

		$mHtml->CloseTable('tr');

		#Acordion
		$mHtml->OpenDiv("class:accordion");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h3>");
			$mHtml->OpenDiv("id:secID");
				$mHtml->SetBody('<form name="form_InfReportEspera" id="form_InfReportEsperaID" action="../'.DIR_APLICA_CENTRAL.'/lib/exportExcel.php" method="post">');
				$mHtml->OpenDiv("id:formID; class:Style2DIV");

					$mHtml->Table('tr');
						$mHtml->SetBody('<tr><th class="CellHead" colspan="4" style="text-align:left">Filtros</th></tr>');

						$mHtml->Label( "Transportadoras: ", $mTD );
						$mHtml->Select2( $mTransp, array_merge($mTD, array("name"=>"cod_transp", "id"=>"cod_transpID", "class"=>"cellInfo1 multiSel")) );
						$mHtml->Label( "Esfera: ", $mTD );
						$mHtml->Select2( array_merge(self::$cNull, $mContro), array_merge($mTD, array("name"=>"cod_contro", "id"=>"cod_controID", "class"=>"cellInfo1 multiSel", "end"=>true)) );

						$mHtml->Label( "Fecha Inicial: ", $mTD );
						$mHtml->Input( array_merge($mTD, array("name"=>"fec_inicia", "id"=>"fec_iniciaID", "size"=>"10", "minlength"=>"10", "maxlength"=>"10", "obl"=>"1", "validate"=>"date", "readonly"=>"true")) );
						$mHtml->Label( "Fecha Final: ", $mTD );
						$mHtml->Input( array_merge($mTD, array("name"=>"fec_finali", "id"=>"fec_finaliID", "size"=>"10", "minlength"=>"10", "maxlength"=>"10", "obl"=>"1", "validate"=>"date", "readonly"=>"true")) );
					$mHtml->CloseTable('tr');

						$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
						$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
						$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
						$mHtml->Hidden( array("name"=>"nameFile", "id"=>"nameFileID", "value"=>"Informe_RegistrosIdeales_".date('YmdHi') ) );
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

	/*! \fn: getEsferas
	 *  \brief: Trae los puestos de control fisicos padres
	 *  \author: Ing. Fabian Salinas
	 *  \date: 18/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mCodContro  String  Codigo de PC
	 *  \return: Matriz
	 */
	private function getEsferas( $mCodContro = null ){
		$mSql = "SELECT a.cod_contro, UPPER(a.nom_contro) AS nom_contro 
				   FROM ".BASE_DATOS.".tab_genera_contro a 
				  WHERE a.ind_pcpadr = 1 
					AND a.ind_virtua = 0 
					AND a.nom_contro NOT LIKE 'DEST%' ";
		$mSql .= !$mCodContro ? "" : " AND a.cod_contro IN ($mCodContro) ";
		$mSql .= " ORDER BY a.nom_contro ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}

	/*! \fn: informGeneral
	 *  \brief: Pinta el informe general
	 *  \author: Ing. Fabian Salinas
	 *  \date: 18/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:
	 *  \return: 
	 */
	private function informGeneral(){
		$mIdxTab = "tabReportEsferaGeneral";
		$mContro = self::getEsferas( $_REQUEST['cod_contro'] );
		$mSizeTi = sizeof($mContro);
		$mContrx = self::getListPC($mContro);
		$mData = self::getDataTransEsfera($mContrx);
		$mCanTra = sizeof($mData);


		$mHtml = new Formlib(2);

		$mHtml->OpenDiv("id:formID; class:Style2DIV");

			if( $mCanTra < 1 ){
				$mHtml->Table('tr');
					$mHtml->Label( "No se encontraron Registros.", array("class"=>"cellInfo1", "align"=>"left") );
				$mHtml->CloseTable('tr');
			}else{
				$mHtml->SetBody('<center>
									<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" onclick="expTabExcelReportEsfera( \''.$mIdxTab.'\' );" style="cursor:pointer">
								 </center><br>');

				#<tabla general>
				$mHtml->Table('tr');
					$mHtml->Label( "No. TRANSPORTADORAS REPORTANDO", array("class"=>"CellHead") );
					$mHtml->Label( "No. DESPACHOS GENERADOS", array("class"=>"CellHead") );
					$mHtml->Label( "No. REPORTES", array("class"=>"CellHead", "end"=>true) );

					$mHtml->SetBody('<td align="center" class="cellInfo1"><label>'.$mCanTra.'</label></td>');
					$mHtml->SetBody('<td align="center" class="cellInfo1" id="totalx_1ID"><label>-</td>');
					$mHtml->SetBody('<td align="center" class="cellInfo1" id="totalx_2ID"><label>-</td>');
				$mHtml->CloseTable('tr');
				#</tabla general>

				$mHtml->SetBody('<br>');

				#<tabla TRANSPORTADORAS vs ESFERAS>
				$mHtml->SetBody('<table id="'.$mIdxTab.'" width="100%" cellspacing="1" cellpadding="3" border="0" align="center"><tbody><tr>');
					#<titulos>
					$mHtml->Label( "No.", array("class"=>"CellHead", "rowspan"=>"2") );
					$mHtml->Label( "TRANSPORTADORA", array("class"=>"CellHead", "rowspan"=>"2") );
					$mHtml->Label( "No. DESPACHOS", array("class"=>"CellHead", "rowspan"=>"2") );
					$mHtml->Label( "ESFERAS", array("class"=>"CellHead", "colspan"=>$mSizeTi) );
					$mHtml->Label( "TOTAL", array("class"=>"CellHead", "rowspan"=>"2", "end"=>true) );

					foreach ($mContro as $row) {
						$mHtml->Label( utf8_encode($row[1]), array("class"=>"CellHead") );
					}
					$mHtml->CloseRow('tr');
					#</titulos>

					#<contenido>
					$i=1;
					$mTotalx = array();
					foreach ($mData as $key => $val) {
						if( $i % 2 == 1 ){
							$mClass = 'cellInfo1 derecha';
						}else{
							$mClass = 'cellInfo2 derecha';
						}

						$mHtml->Label( $i, array("class"=>$mClass." izquierda") );
						$mHtml->Label( $val['nom_transp'], array("class"=>$mClass." izquierda") );
						$mHtml->Label( $val['can_despac'], array("class"=>$mClass) );

						$y = 0;
						$j = 1;
						foreach ($mContro as $row) {
							$x = $val[$row[0]];
							$y += $x;
							$mTotalx[$j] += $x;

							$mHtml->SetBody( self::buildTD($x, $key, $row[0], $mClass) );

							$j++;
						}

						$mHtml->SetBody( self::buildTD($y, $key, '0', $mClass).'</tr>' );

						$mTotalx[-1] += $val['can_despac'];
						$mTotalx[0] += $y;
						$i++;
					}
					#</contenido>

					#<totales>
					$mHtml->Label( "TOTAL", array("class"=>"CellHead derecha", "colspan"=>"2") );

					$mHtml->SetBody('<td class="CellHead derecha" id="total_1TD">
										<label id="total_1ID">'.$mTotalx[-1].'</label>
									</td>');

					for ($j=1; $j <= sizeof($mContro); $j++) {
						$mHtml->SetBody( self::buildTD($mTotalx[$j], '0', $mContro[($j-1)][0], 'CellHead derecha') );
					}
					
					$mHtml->SetBody( self::buildTD($mTotalx[0], '0', '0', 'CellHead derecha', 'total_2') );
					#</totales>

				$mHtml->CloseTable('tr');
				#</tabla TRANSPORTADORAS vs ESFERAS>
			}

		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getDataTransEsfera
	 *  \brief: Trae los tados para el informe de transportadoras vs esferas
	 *  \author: Ing. Fabian Salinas
	 *  \date: 19/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mContro  String  Lista de PC 
	 *  \return: Matriz
	 */
	private function getDataTransEsfera($mContro){
		$mResult = array();
		$mData = self::getData( $mContro, 'esfera' );

		foreach ($mData as $row) {
			if( !$mResult[$row['cod_transp']] ){
				$mResult[$row['cod_transp']]['nom_transp'] = $row['nom_transp']; #Nombre de la transportadora
			}
			$mResult[$row['cod_transp']][$row['cod_contro']] = $row['can_noveda']; #Cantidad de novedades registradas para la transportadora en el PC
		}

		$mData = self::getData( $mContro, 'transp' );
		foreach ($mData as $row) {
			$mResult[$row['cod_transp']]['can_despac'] = $row['can_despac']; #Cantidad de despachos
		}

		return $mResult;
	}

	/*! \fn: getData
	 *  \brief: Trae las novedades en sitio y los PC donde se generaron
	 *  \author: Ing. Fabian Salinas
	 *  \date: 19/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getData( $mContro = null, $mReport = null ){
		$mSql = "SELECT @x:=@x+1 AS num_consec, 
						a.num_despac, c.cod_transp, a.fec_noveda, 
						c.num_placax, a.usr_creaci, a.cod_verpcx,
						n.nom_noveda, 
						UPPER(f.abr_tercer) AS nom_transp, 
						UPPER(e.nom_contro) AS nom_contro,
						IF(d.cod_contro IS NOT NULL, d.cod_contro, a.cod_contro) AS cod_contro, 
						IF(c.nom_conduc IS NOT NULL, c.nom_conduc, m.abr_tercer) AS nom_conduc, 
						CONCAT(UPPER(g.nom_ciudad), ' (', LEFT(h.nom_depart, 4), ') - ', LEFT(i.nom_paisxx, 3)) AS nom_ciuori, 
						CONCAT(UPPER(j.nom_ciudad), ' (', LEFT(k.nom_depart, 4), ') - ', LEFT(l.nom_paisxx, 3)) AS nom_ciudes 
				   FROM (SELECT @x:=0) z,
						".BASE_DATOS.".tab_despac_noveda a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_despac b 
					 ON a.num_despac = b.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige c 
					 ON b.num_despac = c.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_homolo_pcxeal d 
					 ON a.cod_contro = d.cod_homolo 
			 INNER JOIN ".BASE_DATOS.".tab_genera_contro e 
					 ON a.cod_contro = e.cod_contro 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer f 
					 ON c.cod_transp = f.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad g 
					 ON b.cod_paiori = g.cod_paisxx 
					AND b.cod_depori = g.cod_depart 
					AND b.cod_ciuori = g.cod_ciudad 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart h 
					 ON b.cod_paiori = h.cod_paisxx 
					AND b.cod_depori = h.cod_depart 
			 INNER JOIN ".BASE_DATOS.".tab_genera_paises i 
					 ON b.cod_paiori = i.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad j 
					 ON b.cod_paides = j.cod_paisxx 
					AND b.cod_depdes = j.cod_depart 
					AND b.cod_ciudes = j.cod_ciudad 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart k 
					 ON b.cod_paides = k.cod_paisxx 
					AND b.cod_depdes = k.cod_depart 
			 INNER JOIN ".BASE_DATOS.".tab_genera_paises l 
					 ON b.cod_paides = l.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer m 
					 ON c.cod_conduc = m.cod_tercer
			 INNER JOIN ".BASE_DATOS.".tab_genera_noveda n
			 		ON a.cod_noveda = n.cod_noveda
				  WHERE DATE(a.fec_creaci) BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]' 
					AND c.cod_transp IN ($_REQUEST[cod_transp])
					AND a.cod_contro IN ($mContro) 
				";

		switch ($mReport) {
			case 'esfera':
				$mSql = "SELECT x.cod_transp, x.cod_contro, x.nom_transp, 
								COUNT(x.num_despac) AS can_noveda 
						   FROM (
									$mSql
								) x 
					   GROUP BY x.cod_transp, x.cod_contro 
					   ORDER BY x.nom_transp ASC ";
				break;

			case 'transp':
				$mSql = "SELECT x.cod_transp, 
								COUNT(DISTINCT x.num_despac) AS can_despac 
						   FROM (
									$mSql
								) x 
					   GROUP BY x.cod_transp ";
				break;
		}

		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('a');
	}

	/*! \fn: getPCHijos
	 *  \brief: Trae los PC Hijos
	 *  \author: Ing. Fabian Salinas
	 *  \date: 19/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mContro  String  PC Padres
	 *  \return: Array
	 */
	private function getPCHijos($mContro){
		$mSql = "SELECT a.cod_homolo 
				   FROM ".BASE_DATOS.".tab_homolo_pcxeal a 
				  WHERE a.cod_contro IN ($mContro) ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('i');
		return GetUniqueCol(0, $mResult);
	}

	/*! \fn: getListPC
	 *  \brief: Trae la lista de PC incluidos los hijos
	 *  \author: Ing. Fabian Salinas
	 *  \date: 20/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mContro  Matriz  Datos de los PC padres
	 *  \return: String
	 */
	private function getListPC($mContro){
		$mContro = GetUniqueCol(0, $mContro);
		$mContrx = self::getPCHijos( implode(',' ,$mContro) );

		if( sizeof($mContrx) > 0 ){
			$mContrx = array_merge($mContro, $mContrx);
			return implode(',' ,$mContrx);
		}else{
			return $mContro[0];
		}
	}

	/*! \fn: buildTD
	 *  \brief: Construlle el td para el informe
	 *  \author: Ing. Fabian Salinas
	 *  \date: 20/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mNameTd  String  Nombre del TD
	 *  \return: HTML
	 */
	private function buildTD($mValuex, $mTransp, $mContro, $mClassx, $mNameTd = "tag"){
		if( $mValuex > 0 ){
			return '<td class="'.$mClassx.'" id="'.$mNameTd.'TD">
						<label id="'.$mNameTd.'ID" class="pointer" onclick="infDetail('.$mTransp.', '.$mContro.');">'.$mValuex.'</label>
					</td>';
		}else{
			return '<td class="'.$mClassx.'" id="'.$mNameTd.'TD">
						<label id="'.$mNameTd.'ID">&nbsp;&nbsp;-</label>
					</td>';
		}
	}

	/*! \fn: infDetail
	 *  \brief: Pinta el informe detallado
	 *  \author: Ing. Fabian Salinas
	 *  \date: 20/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:     
	 *  \return: 
	 */
	private function infDetail(){
		$mIdxTab = "tabReportEsferaDetail";
		$mContro = self::getEsferas( $_REQUEST['cod_contro'] );
		$mContrx = self::getListPC($mContro);
		$mData = self::getData( $mContrx, 'detail' );
		$mSize = sizeof($mData);

		$mTittle = array("num_consec"=>"No.",
						 "num_despac"=>"Despacho",
						 "nom_ciuori"=>"Origen",
						 "nom_ciudes"=>"Destino",
						 "num_placax"=>"Placa",
						 "nom_conduc"=>"Conductor",
						 "nom_noveda"=>"Novedad",
						 "fec_noveda"=>"Fecha Novedad",
						 "nom_transp"=>"Transportadora",
						 "usr_creaci"=>"Usuario",
						 "cod_verpcx"=>"Codigo de Confirmacion"
						);


		$mHtml = new Formlib(2);

		$mHtml->OpenDiv("id:formID; class:Style2DIV");
			$mHtml->SetBody('<center>
								<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" onclick="expTabExcelReportEsfera( \''.$mIdxTab.'\' );" style="cursor:pointer">
							 </center><br>');

			$mHtml->SetBody("<label style='color: black'>Se encontraron $mSize registros.</label><br>");

			$mHtml->SetBody('<table id="'.$mIdxTab.'" width="100%" cellspacing="0" cellpadding="3" border="0" align="center"><tbody><tr>');

				foreach ($mTittle as $key => $tit) {
					$mHtml->Label( $tit, array("class"=>"CellHead") );
				}
				$mHtml->CloseRow('tr');

				$i=1;
				foreach ($mData as $row) {
					if( $i % 2 == 1 ){
						$mClass = 'cellInfo1 izquierda';
					}else{
						$mClass = 'cellInfo2 izquierda';
					}

					$row['num_despac'] = '<a style="color:#000000;" href="index.php?cod_servic=3302&window=central&despac='.$row['num_despac'].'&tie_ultnov=0&opcion=1">'.$row['num_despac'].'</a>';

					foreach ($mTittle as $key => $tit) {
						$mHtml->Label( utf8_encode($row[$key]), array("class"=>$mClass) );
					}
					$mHtml->CloseRow('tr');
					$i++;
				}

			$mHtml->CloseTable('tr');
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new InfReportEspera();
else
	$_INFORM = new InfReportEspera( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>
