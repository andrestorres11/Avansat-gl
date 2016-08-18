<?php 
/*! \file: inf_noveda_actual.php
 *  \brief: Consultar Novedades Actualizadas 
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 26/02/2016
 *  \bug: 
 *  \warning: 
 */

class InfNovUpd
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
			case 'informDetail':
				self::informDetail();
				break;

			case 'informGeneral':
				self::informGeneral();
				break;

			default:
				self::formulario();
				break;
		}
	}

	/*! \fn: formulario
	 *  \brief: Formulario con los filtros para general el informe
	 *  \author: Ing. Fabian Salinas
	 *  \date: 26/02/2016
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
		IncludeJS( 'inf_noveda_actual.js' );
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
				$mHtml->SetBody('<form name="form_InfBitacoUpdDespac" id="form_InfBitacoUpdDespacID" action="../'.DIR_APLICA_CENTRAL.'/lib/exportExcel.php" method="post">');
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

					$mHtml->Table('tr');
						$mHtml->SetBody('<tr><th class="CellHead" colspan="6" style="text-align:left">Filtros Especificos</th></tr>');

						$mHtml->Label( "Despacho: ", array("class"=>"cellInfo1", "width"=>"16.6%") );
						$mHtml->Input( array("name"=>"num_despac", "id"=>"num_despacID", "class"=>"cellInfo1", "width"=>"16.6%", "size"=>"10", "minlength"=>"6", "maxlength"=>"10", "validate"=>"numero") );
						$mHtml->Label( "Placa: ", array("class"=>"cellInfo1", "width"=>"16.6%") );
						$mHtml->Input( array("name"=>"num_placax", "id"=>"num_placaxID", "class"=>"cellInfo1", "width"=>"16.6%", "size"=>"6", "validate"=>"placa") );
						$mHtml->Label( "Pedido: ", array("class"=>"cellInfo1", "width"=>"16.6%") );
						$mHtml->Input( array("name"=>"num_pedido", "id"=>"num_pedidoID", "class"=>"cellInfo1", "width"=>"16.6%", "size"=>"10", "maxlength"=>"15", "end"=>true) );
					$mHtml->CloseTable('tr');

						$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
						$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
						$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
						$mHtml->Hidden( array("name"=>"nameFile", "id"=>"nameFileID", "value"=>"Informe_NovedadesActualizadas_".date('YmdHi') ) );
						$mHtml->Hidden( array("name"=>"OptionExcel", "id"=>"OptionExcelID", "value"=>"_REQUEST") );
						$mHtml->Hidden( array("name"=>"exportExcel", "id"=>"exportExcelID", "value"=>"") );
				$mHtml->CloseDiv();
				$mHtml->SetBody('</form>');
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		#Tabs
		$mHtml->OpenDiv("id:tabs");
			$mHtml->SetBody('<ul>');
				$mHtml->SetBody('<li><a id="liDespac" href="#tabs-inform" style="cursor:pointer" onclick="report(\'inform\', \'tabs-inform\')">INFORME</a></li>');
			$mHtml->SetBody('</ul>');

			$mHtml->SetBody('<div id="tabs-inform"></div>'); #DIV INFORME
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: informGeneral
	 *  \brief: Pinta el informe general
	 *  \author: Ing. Fabian Salinas
	 *  \date: 29/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param:     
	 *  \return: 
	 */
	private function informGeneral(){
		$mData = self::getDataG();
		$mTd1 = 'align="right" style="background: #00660F; color: #FFF;"';

		$mHtml = new Formlib(2);

		$mHtml->Table('tr');
			$mHtml->Label( "INFORME DIARIO", array("class"=>"CellHead", "colspan"=>"3", "end"=>true) );

			$mHtml->Label( "Fecha", array("class"=>"CellHead") );
			$mHtml->Label( "Despachos con Novedades Modificadas", array("class"=>"CellHead") );
			$mHtml->Label( "Novedades Modificadas", array("class"=>"CellHead", "end"=>true) );

			$mTotalx = array('can_despac'=>0, 'can_noveda'=>0);
			foreach ($mData as $key => $row) {
				$mTotalx['can_despac'] += $row['can_despac'];
				$mTotalx['can_noveda'] += $row['can_noveda'];

				$mHtml->Label( $row['fec_cambio'], array("class"=>"cellInfo1", "align"=>"left") );
				$mHtml->SetBody('<td align="right" class="cellInfo1">'.$row['can_despac'].'</td>');
				$mHtml->SetBody('<td align="right" class="cellInfo1 pointer" onclick="informDetail(\''.$row['fec_cambio'].'\', \''.$row['fec_cambio'].'\')">'.$row['can_noveda'].'</td></tr>');
			}

			$mHtml->SetBody('<td align="right" style="background: #00660F; color: #FFF;">TOTAL</td>');
			$mHtml->SetBody('<td '.$mTd1.'>'.$mTotalx['can_despac'].'</td>');
			$mHtml->SetBody('<td '.$mTd1.' class="pointer" onclick="informDetail(\''.$_REQUEST['fec_inicia'].'\', \''.$_REQUEST['fec_finali'].'\')">'.$mTotalx['can_noveda'].'</td>');
		$mHtml->CloseTable('tr');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getDataG
	 *  \brief: Trae la Data del informe general
	 *  \author: Ing. Fabian Salinas
	 *  \date: 29/02/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getDataG(){
		$mSql = "SELECT DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') AS fec_cambio, 
						COUNT(DISTINCT a.num_despac) AS can_despac, 
						COUNT(a.num_despac) AS can_noveda 
				   FROM ".BASE_DATOS.".tab_bitaco_actnov a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_despac b 
					 ON a.num_despac = b.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige c 
					 ON a.num_despac = c.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_corona d 
					 ON a.num_despac = d.num_dessat 
				  WHERE DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]' ";
		$mSql .= !$_REQUEST['cod_tipdes'] ? "" : " AND b.cod_tipdes IN ($_REQUEST[cod_tipdes]) ";
		$mSql .= !$_REQUEST['cod_transp'] ? "" : " AND c.cod_transp IN ($_REQUEST[cod_transp]) ";
		$mSql .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = '$_REQUEST[num_despac]' ";
		$mSql .= !$_REQUEST['num_placax'] ? "" : " AND c.num_placax LIKE '$_REQUEST[num_placax]' ";
		$mSql .= !$_REQUEST['num_pedido'] ? "" : " AND d.num_pedido LIKE '$_REQUEST[num_pedido]' ";
		$mSql .= " GROUP BY fec_cambio";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('a');
	}

	/*! \fn: informDetail
	 *  \brief: Pinta el informe detallado
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function informDetail(){
		$mTitle = array("num_consec"=>"#",
						"num_despac"=>"Despacho",
						"cod_manifi"=>"Manifiesto",
						"num_viajex"=>"Viaje",
						"num_placax"=>"Placa",
						"nom_transp"=>"Transportadora",
						"nom_novold"=>"Novedad Anterior",
						"obs_novold"=>"Observacion Novedad Anterior",
						"nom_novnew"=>"Novedad Actual",
						"obs_novnew"=>"Observacion Novedad Actual",
						"obs_motivo"=>"Motivo",
						"usr_creaci"=>"Usuario que Modifica",
						"fec_creaci"=>"Fecha Modificación",
					   );

		$mIdxTablex = "tabNovUpd";
		$mData = self::getDataDetail();


		$mHtml = new Formlib(2);

		$mHtml->SetBody('<center>
							<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" onclick="expTabExcelNovupd( \''.$mIdxTablex.'\' );" style="cursor:pointer">
						 </center><br>');

		$mHtml->setBody("Se Encontraron ".sizeof($mData)." Registros");

		$mHtml->Table('tr');
		$mHtml->setBody('<table id="'.$mIdxTablex.'" width="100%" cellspacing="0" cellpadding="3" border="0" align="center"><tbody><tr>');

				foreach ($mTitle as $key => $tit) {
					$mHtml->Label( $tit, array("class"=>"CellHead") );
				}
			$mHtml->CloseRow('td');

			foreach ($mData as $row) {
				foreach ($mTitle as $key => $tit)
					$mHtml->Label($row[$key], array("class"=>"cellInfo1") );

				$mHtml->CloseRow('td');
			}

		$mHtml->SetBody('</table>');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getDataDetail
	 *  \brief: Trae la data para el informe detallado
	 *  \author: Ing. Fabian Salinas
	 *  \date: 01/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getDataDetail(){
		$mSql = "SELECT @rownum := @rownum + 1 AS num_consec, 
						a.obs_novold, a.obs_novnew, a.obs_motivo, 
						a.usr_creaci, a.fec_creaci, a.num_despac, 
						b.cod_manifi, c.num_placax, 
						d.num_despac AS num_viajex, e.abr_tercer AS nom_transp, 
						f.nom_noveda AS nom_novold, g.nom_noveda AS nom_novnew 
				   FROM ( SELECT @rownum :=0 ) z, 
						".BASE_DATOS.".tab_bitaco_actnov a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_despac b 
					 ON a.num_despac = b.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige c 
					 ON a.num_despac = c.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_corona d 
					 ON a.num_despac = d.num_dessat 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e 
					 ON c.cod_transp = e.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_noveda f 
					 ON a.cod_novold = f.cod_noveda 
			 INNER JOIN ".BASE_DATOS.".tab_genera_noveda g 
					 ON a.cod_novnew = g.cod_noveda 
				  WHERE DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') BETWEEN '$_REQUEST[fec_inicia]' AND '$_REQUEST[fec_finali]' ";
		$mSql .= !$_REQUEST['cod_tipdes'] ? "" : " AND b.cod_tipdes IN ($_REQUEST[cod_tipdes]) ";
		$mSql .= !$_REQUEST['cod_transp'] ? "" : " AND c.cod_transp IN ($_REQUEST[cod_transp]) ";
		$mSql .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = '$_REQUEST[num_despac]' ";
		$mSql .= !$_REQUEST['num_placax'] ? "" : " AND c.num_placax LIKE '$_REQUEST[num_placax]' ";
		$mSql .= !$_REQUEST['num_pedido'] ? "" : " AND d.num_pedido LIKE '$_REQUEST[num_pedido]' ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('a');
	}
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new InfNovUpd();
else
	$_INFORM = new InfNovUpd( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>