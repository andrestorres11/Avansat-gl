<?php
/*! \file: inf_regist_ideale.php
 *  \brief: Archivo para el informe de "Indicador de Registros Ideales"
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 05/04/2016
 *  \bug: 
 *  \warning: 
 */

class InfRegistIdeales
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
		$mTipoDespac = self::$cDespac -> getTipoDespac();
		$mTransp = self::$cDespac -> getTransp();
		$mTD = array("class"=>"cellInfo1", "width"=>"25%");

		if( sizeof($mTransp) != 1 ){
			$mTransp = array_merge(self::$cNull, $mTransp);
			$mDisabl = false;
		}else
			$mDisabl = true;


		IncludeJS( 'jquery.js' );
		IncludeJS( 'jquery.blockUI.js' );
		IncludeJS( 'inf_regist_ideale.js' );
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
				$mHtml->SetBody('<form name="form_InfBitacoUpdDespac" id="form_InfRegistIdealeID" action="../'.DIR_APLICA_CENTRAL.'/lib/exportExcel.php" method="post">');
				$mHtml->OpenDiv("id:formID; class:Style2DIV");

					$mHtml->Table('tr');

						$mHtml->SetBody('<th class="CellHead" colspan="8" style="text-align:left">Tipo de Despacho</th></tr>');
						$i=0;
						foreach ($mTipoDespac as $row){
							if( $i==0 || ( $i % 4 == 0) )
								$mHtml->SetBody('<tr>');
							
							$mHtml->Label( $row[1], array("class"=>"cellInfo1", "width"=>"12.5%", "align"=>"right") );
							$mHtml->CheckBox( array("class"=>"cellInfo1", "width"=>"12.5%", "name"=>"cod_tipdes$row[0]", "value"=>$row[0]) );

							if( ($i+1) == sizeof($mTipoDespac) || ( ($i+1) % 4 == 0) )
								$mHtml->SetBody('</tr>');
							$i++;
						}
					$mHtml->SetBody('</table>');

					$mHtml->Table('tr');
						$mHtml->SetBody('<tr><th class="CellHead" colspan="4" style="text-align:left">Otros Filtros</th></tr>');

						$mHtml->Label( "&nbsp;", $mTD );
						$mHtml->Label( "Transportadoras: ", $mTD );
						$mHtml->Select2( $mTransp, array_merge($mTD, array("name"=>"cod_transp", "id"=>"cod_transpID", "class"=>"cellInfo1 multiSel")) );
						$mHtml->Label( "&nbsp;", array_merge($mTD, array("colspan"=>"4", "end"=>true)) );

						$mHtml->Label( "Fecha Inicial: ", $mTD );
						$mHtml->Input( array_merge($mTD, array("name"=>"fec_inicia", "id"=>"fec_iniciaID", "size"=>"10", "minlength"=>"10", "maxlength"=>"10", "obl"=>"1", "validate"=>"date")) );
						$mHtml->Label( "Fecha Final: ", $mTD );
						$mHtml->Input( array_merge($mTD, array("name"=>"fec_finali", "id"=>"fec_finaliID", "size"=>"10", "minlength"=>"10", "maxlength"=>"10", "obl"=>"1", "validate"=>"date")) );
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
				$mHtml->SetBody('<li><a id="liDespac" href="#tabs-empres" style="cursor:pointer" onclick="report(\'empres\', \'tabs-empres\')">EMPRESAS</a></li>');
				$mHtml->SetBody('<li><a id="liDespac" href="#tabs-diario" style="cursor:pointer" onclick="report(\'diario\', \'tabs-diario\')">DIARIO</a></li>');
			$mHtml->SetBody('</ul>');

			$mHtml->SetBody('<div id="tabs-empres"></div>'); #DIV EMPRESAS
			$mHtml->SetBody('<div id="tabs-diario"></div>'); #DIV DIARIO
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: informGeneral
	 *  \brief: Gestiona los informes generales
	 *  \author: Ing. Fabian Salinas
	 *  \date: 06/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:     
	 *  \return:
	 */
	private function informGeneral(){
		switch ($_REQUEST['ind_pestan']) {
			case 'diario':
				self::infDiario();
				break;

			case 'empres':
				self::infEmpres();
				break;
			
			default:
				echo "¡Informe no definido!...";
				break;
		}
	}

	/*! \fn: infDiario
	 *  \brief: Informe Diario
	 *  \author: Ing. Fabian Salinas
	 *  \date: 06/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:    
	 *  \return: 
	 */
	private function infDiario(){
		$mTittle = array("Fecha", "Despachos Generados", "No. Registros Ideales", 
						"No. Registros Ejecutados", "Diferencia en Registros", "% Cumplido", "% No Cumplido");
		$mSizeTi = sizeof($mTittle);
		$mData = self::getDataDiario();
		$mIdxTablex = "tabRegIdeDiario";

		$mHtml = new Formlib(2);

		$mHtml->OpenDiv("id:formID; class:Style2DIV");

			if( sizeof($mData) < 1 ){
				$mHtml->Table('tr');
					$mHtml->Label( "No se encontraron Registros.", array("class"=>"cellInfo1", "align"=>"left") );
				$mHtml->CloseTable('tr');
			}else{
				$mHtml->SetBody('<center>
									<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" onclick="expTabExcelRegIde( \''.$mIdxTablex.'\' );" style="cursor:pointer">
								 </center><br>');

				$mHtml->Table('tr');
					$mHtml->Label( "GENERAL", array("class"=>"CellHead", "colspan"=>($mSizeTi-1), "end"=>true) );

					for ($i=1; $i < $mSizeTi; $i++) { 
						$mHtml->Label( $mTittle[$i], array("class"=>"CellHead") );
					}

					$mHtml->CloseRow();

					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_1ID"></td>');
					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_2ID"></td>');
					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_3ID"></td>');
					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_4ID"></td>');
					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_5ID"></td>');
					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_6ID"></td>');

				$mHtml->CloseTable('tr');

				$mHtml->SetBody('<br>');

				$mHtml->setBody('<table id="'.$mIdxTablex.'" width="100%" cellspacing="0" cellpadding="3" border="0" align="center"><tbody><tr>');
					$mHtml->Label( "DIARIO", array("class"=>"CellHead", "colspan"=>$mSizeTi, "end"=>true) );

					for ($i=0; $i < $mSizeTi; $i++) { 
						$mHtml->Label( $mTittle[$i], array("class"=>"CellHead") );
					}
					$mHtml->CloseRow();

					$i=1;
					$mTotalx = array();
					foreach ($mData as $key => $val) {
						if( $i % 2 == 1 ){
							$mClass = 'cellInfo1';
						}else{
							$mClass = 'cellInfo2';
						}

						$mDifPor = self::difPorcen($val['reg_ideale'], $val['can_noveda']);

						$mTotalx['can_despac'] += $val['can_despac'];
						$mTotalx['reg_ideale'] += $val['reg_ideale'];
						$mTotalx['can_noveda'] += $val['can_noveda'];

						$mHtml->Label( $key, array("class"=>$mClass, "align"=>"left") );
						$mHtml->SetBody('<td align="right" class='.$mClass.'>
											<label class="pointer" onclick="infDetail(\'diario\', \''.$key.'\', \''.$key.'\', false);">'.$val['can_despac'].'</label>
										</td>');
						$mHtml->Label( $val['reg_ideale'], array("class"=>$mClass) );
						$mHtml->Label( $val['can_noveda'], array("class"=>$mClass) );
						$mHtml->Label( $mDifPor['difere'], array("class"=>$mClass) );
						$mHtml->Label( $mDifPor['cumple']."%", array("class"=>$mClass) );
						$mHtml->Label( $mDifPor['noCump']."%", array("class"=>$mClass, "end"=>true) );

						$i++;
					}

					#<Totales>
					$mDifPor = self::difPorcen($mTotalx['reg_ideale'], $mTotalx['can_noveda']);

					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;">TOTAL</td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_1ID">
									<label class="pointer" id="tag0TD" 
									onclick="infDetail(\'diario\', \''.$_REQUEST['fec_inicia'].'\', \''.$_REQUEST['fec_finali'].'\', false);">'
									.$mTotalx['can_despac'].'</label></td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_2ID">'.$mTotalx['reg_ideale'].'</td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_3ID">'.$mTotalx['can_noveda'].'</td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_4ID">'.$mDifPor['difere'].'</td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_5ID">'.$mDifPor['cumple'].'%</td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_6ID">'.$mDifPor['noCump'].'%</td>');
					#</Totales>

				$mHtml->CloseTable('tr');
			}

		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: infEmpres
	 *  \brief: Informe por Empresas
	 *  \author: Ing. Fabian Salinas
	 *  \date: 08/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:    
	 *  \return: 
	 */
	private function infEmpres(){
		$mTittle = array("Transportadora", "Despachos Generados", "No. Registros Ideales", 
						"No. Registros Ejecutados", "Diferencia en Registros", "% Cumplido", "% No Cumplido");
		$mSizeTi = sizeof($mTittle);
		$mData = self::getDataTransp();
		$mIdxTablex = "tabRegIdeEmpres";

		$mHtml = new Formlib(2);

		$mHtml->OpenDiv("id:formID; class:Style2DIV");

			if( sizeof($mData) < 1 ){
				$mHtml->Table('tr');
					$mHtml->Label( "No se encontraron Registros.", array("class"=>"cellInfo1", "align"=>"left") );
				$mHtml->CloseTable('tr');
			}else{
				$mHtml->SetBody('<center>
									<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" onclick="expTabExcelRegIde( \''.$mIdxTablex.'\' );" style="cursor:pointer">
								 </center><br>');

				$mHtml->Table('tr');
					$mHtml->Label( "GENERAL", array("class"=>"CellHead", "colspan"=>($mSizeTi-1), "end"=>true) );

					for ($i=1; $i < $mSizeTi; $i++) { 
						$mHtml->Label( $mTittle[$i], array("class"=>"CellHead") );
					}

					$mHtml->CloseRow();

					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_1ID"></td>');
					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_2ID"></td>');
					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_3ID"></td>');
					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_4ID"></td>');
					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_5ID"></td>');
					$mHtml->SetBody('<td align="right" class="cellInfo1" id="totalx_6ID"></td>');

				$mHtml->CloseTable('tr');

				$mHtml->SetBody('<br>');

				$mHtml->setBody('<table id="'.$mIdxTablex.'" width="100%" cellspacing="0" cellpadding="3" border="0" align="center"><tbody><tr>');
					$mHtml->Label( "POR TRANSPORTADORA", array("class"=>"CellHead", "colspan"=>$mSizeTi, "end"=>true) );

					for ($i=0; $i < $mSizeTi; $i++) { 
						$mHtml->Label( $mTittle[$i], array("class"=>"CellHead") );
					}
					$mHtml->CloseRow('td');

					$i=1;
					$mTotalx = array();
					foreach ($mData as $key => $val) {
						if( $i % 2 == 1 ){
							$mClass = 'cellInfo1';
						}else{
							$mClass = 'cellInfo2';
						}

						$mDifPor = self::difPorcen($val['reg_ideale'], $val['can_noveda']);

						$mTotalx['can_despac'] += $val['can_despac'];
						$mTotalx['reg_ideale'] += $val['reg_ideale'];
						$mTotalx['can_noveda'] += $val['can_noveda'];

						$mHtml->Label( $val['nom_transp'], array("class"=>$mClass, "align"=>"left") );
						$mHtml->SetBody('<td align="right" class='.$mClass.'><label class="pointer" 
											onclick="infDetail(\'transp\', \''.$_REQUEST['fec_inicia'].'\', \''.$_REQUEST['fec_finali'].'\', \''.$key.'\');">'
											.$val['can_despac'].'</label>
										</td>');
						$mHtml->Label( $val['reg_ideale'], array("class"=>$mClass) );
						$mHtml->Label( $val['can_noveda'], array("class"=>$mClass) );
						$mHtml->Label( $mDifPor['difere'], array("class"=>$mClass) );
						$mHtml->Label( $mDifPor['cumple']."%", array("class"=>$mClass) );
						$mHtml->Label( $mDifPor['noCump']."%", array("class"=>$mClass, "end"=>true) );

						$i++;
					}

					#<Totales>
					$mDifPor = self::difPorcen($mTotalx['reg_ideale'], $mTotalx['can_noveda']);

					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;">TOTAL</td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_1ID">
									<label class="pointer" id="tag0TD" 
									onclick="infDetail(\'transp\', \''.$_REQUEST['fec_inicia'].'\', \''.$_REQUEST['fec_finali'].'\', false);">'
									.$mTotalx['can_despac'].'</label></td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_2ID">'.$mTotalx['reg_ideale'].'</td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_3ID">'.$mTotalx['can_noveda'].'</td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_4ID">'.$mDifPor['difere'].'</td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_5ID">'.$mDifPor['cumple'].'%</td>');
					$mHtml->SetBody('<td align="right" style="background: #35650F; color: #FFF;" id="total_6ID">'.$mDifPor['noCump'].'%</td>');
					#</Totales>

				$mHtml->CloseTable('tr');
			}
			
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: difPorcen
	 *  \brief: Halla la diferencia entre dos valores y los porcetajes correspondientes
	 *  \author: Ing. Fabian Salinas
	 *  \date: 07/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mVal1  Integer  Valor 1
	 *  \param: mVal2  Integer  Valor 2
	 *  \return: Array
	 */
	private function difPorcen($mVal1, $mVal2){
		$mResult['difere'] = $mVal1 - $mVal2;
		if( $mVal1 > 0 && $mVal2 > 0 ){
			$mResult['cumple'] = ($mVal2 * 100) / $mVal1;
			$mResult['cumple'] = round($mResult['cumple']);
		}else{
			$mResult['cumple'] = 0;
		}

		$mResult['noCump'] = 100 - $mResult['cumple'];
		if( $mVal1 == 0 ){
			$mResult['noCump'] = 0;
		}elseif($mResult['noCump'] < 1){
			$mResult['noCump'] = 0;
		}

		return $mResult;
	}

	/*! \fn: getDataDiario
	 *  \brief: Arma la data para el informe Diario
	 *  \author: Ing. Fabian Salinas
	 *  \date: 06/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getDataDiario(){
		$mCampos = array("a.cod_transp", "a.nom_transp", "a.tie_nacion", "a.tie_urbano", "a.tie_traexp", "a.tie_traimp", "a.tie_tratr1", "a.tie_tratr2");
		$mTransp = getTransTipser(self::$cConexion, " AND a.cod_transp IN ( $_REQUEST[cod_transp] ) ", $mCampos);
		$mResult = array();

		foreach ($mTransp as $row) {
			$mData = self::getData($row, 'diari');

			foreach ($mData as $row) {
				$mCanNoveda = $row['can_noveda'] == '' ? 0 : $row['can_noveda'];

				$mResult[$row['fec_despa1']]['can_despac'] += $row['can_despac'];
				$mResult[$row['fec_despa1']]['reg_ideale'] += $row['reg_ideale'];
				$mResult[$row['fec_despa1']]['can_noveda'] += $mCanNoveda;
			}
		}

		return $mResult;
	}

	/*! \fn: getDataTransp
	 *  \brief: Arma al data por transportadora
	 *  \author: Ing. Fabian Salinas
	 *  \date: 08/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:    
	 *  \return: Matriz
	 */
	private function getDataTransp(){
		$mCampos = array("a.cod_transp", "a.nom_transp", "a.tie_nacion", "a.tie_urbano", "a.tie_traexp", "a.tie_traimp", "a.tie_tratr1", "a.tie_tratr2");
		$mTransp = getTransTipser(self::$cConexion, " AND a.cod_transp IN ( $_REQUEST[cod_transp] ) ", $mCampos);
		$mResult = array();

		foreach ($mTransp as $row) {
			$mResult[$row['cod_transp']]['nom_transp'] = $row['nom_transp'];

			$mData = self::getData($row, 'transp');

			foreach ($mData as $row) {
				$mCanNoveda = $row['can_noveda'] == '' ? 0 : $row['can_noveda'];

				$mResult[$row['cod_transp']]['can_despac'] += $row['can_despac'];
				$mResult[$row['cod_transp']]['reg_ideale'] += $row['reg_ideale'];
				$mResult[$row['cod_transp']]['can_noveda'] += $mCanNoveda;
			}
		}

		return $mResult;
	}

	/*! \fn: getData
	 *  \brief: Trae la data para los informes generales
	 *  \author: Ing. Fabian Salinas
	 *  \date: 07/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mTransp  Array    Data de la transportadora
	 *  \param: mTipInf  String   diari = agrupa por fecha; transp = agrupa por dia; detail = No agrupa, agrega campos
	 *  \return: Matriz
	 */
	private function getData($mTransp, $mTipInf){
		if( $mTipInf == 'detail' ){
			$mAux = ", a.cod_manifi, b.num_placax, 
					   c.num_despac AS num_viajex, 
					   IF(b.nom_conduc IS NULL OR b.nom_conduc = '', j.abr_tercer, b.nom_conduc) AS nom_conduc, 
					   CONCAT(UPPER(d.nom_ciudad), ' (', SUBSTRING(e.nom_depart, 1, 4), ') - ', SUBSTRING(f.nom_paisxx, 1, 3)) AS nom_ciuori, 
					   CONCAT(UPPER(g.nom_ciudad), ' (', SUBSTRING(h.nom_depart, 1, 4), ') - ', SUBSTRING(i.nom_paisxx, 1, 3)) AS nom_ciudes, 
					   '$mTransp[nom_transp]' AS nom_transp 
					";
		}

		$mSubSql1 = "( /* Trae la cantidad de PC de la ruta asignada al Despacho */
							SELECT COUNT(z.cod_contro) AS can_contro 
							  FROM ".BASE_DATOS.".tab_despac_seguim z 
							 WHERE z.num_despac = u.num_despac 
						  GROUP BY z.num_despac 
					 )";

		$mSubSql2 = "( /* Trae el tiempo total de la ruta del Despacho */
							SELECT MAX(y.val_duraci) AS val_duraci
							  FROM ".BASE_DATOS.".tab_despac_seguim x 
						INNER JOIN ".BASE_DATOS.".tab_genera_rutcon y 
								ON x.cod_rutasx = y.cod_rutasx 
							   AND x.cod_contro = y.cod_contro 
							 WHERE x.num_despac = u.num_despac 
					 )";

		$mSql = "SELECT u.*,
						/* IF => Opcion 1: Registros ideales = a cantidad de PC de la ruta del despacho
								 Opcion 2: Registros ideales = a tiempo total de la ruta del despacho dividido el 
						   									   tiempo de seguimiento parametrizado para esa 
						   									   transportadora y tipo de despacho */
						IF( u.tie_seguim = 0, $mSubSql1, ROUND($mSubSql2 / u.tie_seguim) ) AS reg_ideale, 
						( /* Cantidad Novedades en Sitio del Despacho */
							SELECT COUNT(v.cod_noveda) AS can_novsit 
							  FROM ".BASE_DATOS.".tab_despac_noveda v 
							 WHERE v.num_despac = u.num_despac 
							   AND v.cod_noveda != 4999 
						  GROUP BY v.num_despac 
						) AS can_novsit, 
						( /* Cantidad Novedades antes de Sitio del Despacho */
							SELECT COUNT(w.cod_noveda) AS can_novant 
							  FROM ".BASE_DATOS.".tab_despac_contro w 
							 WHERE w.num_despac = u.num_despac 
							   AND w.cod_noveda != 4999 
						  GROUP BY w.num_despac
						) AS can_novant 
				   FROM (
							  SELECT a.num_despac, a.cod_tipdes, b.cod_transp, 
									DATE_FORMAT(a.fec_despac, '%Y-%m-%d') AS fec_despa1, 
									/* CASE tiempo de seguimiento de la transportadora segun tipo de despacho */
									CASE a.cod_tipdes 
										WHEN 1 THEN ".($mTransp['tie_urbano'] == '' ? '0' : $mTransp['tie_urbano'])." 
										WHEN 2 THEN ".($mTransp['tie_nacion'] == '' ? '0' : $mTransp['tie_nacion'])." 
										WHEN 3 THEN ".($mTransp['tie_traimp'] == '' ? '0' : $mTransp['tie_traimp'])." 
										WHEN 4 THEN ".($mTransp['tie_traexp'] == '' ? '0' : $mTransp['tie_traexp'])." 
										WHEN 5 THEN ".($mTransp['tie_tratr1'] == '' ? '0' : $mTransp['tie_tratr1'])." 
										WHEN 6 THEN ".($mTransp['tie_tratr2'] == '' ? '0' : $mTransp['tie_tratr2'])." 
										ELSE 0 
									END AS tie_seguim 
									$mAux 
							   FROM ".BASE_DATOS.".tab_despac_despac a 
						 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
								 ON a.num_despac = b.num_despac 
						  LEFT JOIN ".BASE_DATOS.".tab_despac_corona c 
								 ON a.num_despac = c.num_dessat 
						 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
								 ON a.cod_ciuori = d.cod_ciudad 
						 INNER JOIN ".BASE_DATOS.".tab_genera_depart e 
								 ON a.cod_depori = e.cod_depart 
						 INNER JOIN ".BASE_DATOS.".tab_genera_paises f 
								 ON a.cod_paiori = f.cod_paisxx
						 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad g 
								 ON a.cod_ciudes = g.cod_ciudad 
						 INNER JOIN ".BASE_DATOS.".tab_genera_depart h 
								 ON a.cod_depdes = h.cod_depart 
						 INNER JOIN ".BASE_DATOS.".tab_genera_paises i 
								 ON a.cod_paiori = i.cod_paisxx 
						 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer j 
								 ON b.cod_conduc = j.cod_tercer 
							  WHERE a.fec_llegad IS NOT NULL 
								AND a.fec_llegad != '0000-00-00 00:00:00'
								AND a.ind_anulad = 'R' 
								AND DATE_FORMAT(a.fec_despac, '%Y-%m-%d') BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]'
								AND b.cod_transp = $mTransp[cod_transp] 
								".( !$_REQUEST['cod_tipdes'] ? "" : " AND a.cod_tipdes IN ( $_REQUEST[cod_tipdes] ) " )." 
						   GROUP BY a.num_despac 
						) u 
				";

		if( $mTipInf == 'detail' ){
			$mSql = "SELECT s.num_despac, s.cod_manifi, s.num_viajex, 
							s.nom_conduc, s.num_placax, s.nom_transp, 
							s.nom_ciuori, s.nom_ciudes, s.reg_ideale, 
							s.can_noveda, @x:=@x+1 AS num_consec, 
							(s.reg_ideale - s.can_noveda) AS can_difere 
					   FROM (SELECT @x:=0) r,
					   		(
								 SELECT t.*, 
										(IF(t.can_novsit IS NULL, 0, t.can_novsit) + IF(t.can_novant IS NULL, 0, t.can_novant)) AS can_noveda 
								   FROM (
											$mSql 
										) t 
							   GROUP BY t.num_despac 
					   		) s 
					"; 
		}else{
			$mSql = "SELECT t.fec_despa1, t.cod_transp, 
							COUNT(DISTINCT(t.num_despac)) AS can_despac, 
							SUM(t.reg_ideale) AS reg_ideale, 
							(SUM(t.can_novsit) + SUM(t.can_novant)) AS can_noveda 
					   FROM (
								$mSql
							) t 
				   GROUP BY ";

			if( $mTipInf == 'diari' ){
				$mSql .= "t.fec_despa1";
			}elseif( $mTipInf == 'transp' ){
				$mSql .= "t.cod_transp";
			}
		}

		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('a');
	}

	/*! \fn: infDetail
	 *  \brief: Pinta el informe Detallado
	 *  \author: Ing. Fabian Salinas
	 *  \date: 08/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: return
	 */
	private function infDetail(){
		$mTittle = array(	'num_consec' => "No.",
							'num_despac' => "No. de Despacho",
							'cod_manifi' => "No. de Manifiesto",
							'num_viajex' => "No. de Viaje",
							'nom_conduc' => "Conductor",
							'num_placax' => "Placa",
							'nom_transp' => "Transportadora",
							'nom_ciuori' => "Origen",
							'nom_ciudes' => "Destino",
							'reg_ideale' => "No. de Registros Ideales",
							'can_noveda' => "No. de Registros Ejecutados",
							'can_difere' => "Diferencia"
						);
		$mSizeTi = sizeof($mTittle);
		$mData = self::getDataDetail();
		$mIdxTablex = "tabRegIdeDetail";

		$mHtml = new Formlib(2);

		$mHtml->OpenDiv("id:formID; class:Style2DIV");

		if( sizeof($mData) < 1 ){
			$mHtml->Table('tr');
				$mHtml->Label( "No se encontraron Registros.", array("class"=>"cellInfo1") );
			$mHtml->CloseTable('tr');
		}else{
			$mHtml->SetBody('<center>
								<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" onclick="expTabExcelRegIde( \''.$mIdxTablex.'\' );" style="cursor:pointer">
							 </center><br>');

			$mHtml->setBody("<label style='color: black'>Se Encontraron ".sizeof($mData)." Registro(s).</label>");

			$mHtml->setBody('<table id="'.$mIdxTablex.'" width="100%" cellspacing="0" cellpadding="3" border="0" align="center"><tbody><tr>');
				$mHtml->Label( "DETALLADO", array("class"=>"CellHead", "colspan"=>($mSizeTi+1), "end"=>true) );

				foreach ($mTittle as $key => $tit) {
					$mHtml->Label( $tit, array("class"=>"CellHead") );
				}
				$mHtml->CloseRow('td');

				$i=1;
				foreach ($mData as $row) {
					if( $i % 2 == 1 ){
						$mClass = 'cellInfo1';
					}else{
						$mClass = 'cellInfo2';
					}

					foreach ($mTittle as $key => $tit) {
						switch ($key) {
							case 'num_consec':
								$mHtml->Label( $i, array("class"=>$mClass, "align"=>"left") );
								break;
							case 'num_despac':
								$mNumDespac = '<a style="color:#000000;" href="index.php?cod_servic=3302&window=central&despac='.$row[$key].'&tie_ultnov=0&opcion=1">'.$row[$key].'</a>';
								$mHtml->Label( $mNumDespac, array("class"=>$mClass, "align"=>"left") );
								break;
							default:
								$mHtml->Label( utf8_encode($row[$key]), array("class"=>$mClass, "align"=>"left") );
								break;
						}
					}
					$mHtml->CloseRow('td');

					$i++;
				}
			$mHtml->SetBody('</table>');
		}

		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getDataDetail
	 *  \brief: Trae la data para el informe Detallado
	 *  \author: Ing. Fabian Salinas
	 *  \date: 08/04/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:     
	 *  \return: Matriz
	 */
	private function getDataDetail(){
		$mCampos = array("a.cod_transp", "a.nom_transp", "a.tie_nacion", "a.tie_urbano", "a.tie_traexp", "a.tie_traimp", "a.tie_tratr1", "a.tie_tratr2");
		$mTransp = getTransTipser(self::$cConexion, " AND a.cod_transp IN ( $_REQUEST[cod_transp] ) ", $mCampos);
		$mResult = array();

		foreach ($mTransp as $row) {
			$mData = self::getData($row, 'detail');
			if( sizeof($mData) > 0 ){
				$mResult = array_merge($mData, $mResult);
			}
		}

		return $mResult;
	}
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new InfRegistIdeales();
else
	$_INFORM = new InfRegistIdeales( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>