<?php
/*! \file: inf_noveda_empter.php
 *  \brief: consulta las novedades registradas por empresas terceras (perfil 719)
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 14/12/2015
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');
#date_default_timezone_get('America/Bogota');
setlocale(LC_ALL,"es_ES");

/*! \class: InfNovedadesEmpTer
 *  \brief: Clase para consultar las novedades registradas por empresas terceras (perfil 719)
 */
class InfNovedadesEmpTer
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cCallce,
					$cNull = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null)
	{
		if($_REQUEST['Ajax'] === 'on' )
		{
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion = $AjaxConnection;
		}else{
			self::$cConexion = $co;
			self::$cUsuario = $us;
			self::$cCodAplica = $ca;

			@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );

			IncludeJS( 'jquery.js' );
			IncludeJS( 'jquery.blockUI.js' );
			IncludeJS( 'inf_noveda_empter.js' );

			echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
			echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
		}

		switch($_REQUEST['Option'])
		{
			case 'generateReportG':
				self::generateReportG();
				break;

			case 'detailReport':
				self::detailReport();
				break;

			default:
				self::formulario();
				break;
		}
	}

	
	/*! \fn: formulario
	 *  \brief: Formulario
	 *  \author: Ing. Fabian Salinas
	 *  \date:  14/12/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function formulario()
	{
		
		$mTD = "cellInfo1";

		$mHtml = new Formlib(2);
		$mHtml->CloseTable('tr');

		#Acordion
		$mHtml->OpenDiv("id:accordionID");
			$mHtml->SetBody("<h3 style='padding:6px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>FILTROS</b></h3>");
			$mHtml->OpenDiv("id:secID");
				$mHtml->SetBody('<form id="form_NovedaEmpterID" name="form_NovedaEmpter" action="../'.DIR_APLICA_CENTRAL.'/lib/exportExcel.php" method="post">');
				$mHtml->OpenDiv("id:formID; class:Style2DIV");
					$mHtml->Table('tr');
						$mHtml->Label( "<b>Seleccione Par&aacute;metros de B&uacute;squeda</b>", array("class"=>"CellHead", "colspan"=>"6", "align"=>"left", "end"=>true) );

						$mHtml->Label( "Fecha Inicial: ", array("class"=>$mTD, "width"=>"16.6%") );
						$mHtml->Input( array("name"=>"fec_inicia", "id"=>"fec_iniciaID", "value"=>"", "size"=>"8", "class"=>$mTD, "width"=>"16.6%") );
						$mHtml->Label( "Fecha Final: ", array("class"=>$mTD, "width"=>"16.6%") );
						$mHtml->Input( array("name"=>"fec_finali", "id"=>"fec_finaliID", "value"=>"", "size"=>"8", "class"=>$mTD, "width"=>"16.6%") );
						$mHtml->Label( "No. Viaje: ", array("class"=>$mTD, "width"=>"16.6%") );
						$mHtml->Input( array("name"=>"num_viajex", "id"=>"num_viajexID", "value"=>"", "size"=>"15", "class"=>$mTD, "width"=>"16.6%", "end"=>true) );

						$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
						$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>$_REQUEST['window']) );
						$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
						$mHtml->Hidden( array("name"=>"nameFile", "id"=>"nameFileID", "value"=>"Informe_Empresas_Terceras_".date('Y-m-d') ) );
						$mHtml->Hidden( array("name"=>"OptionExcel", "id"=>"OptionExcelID", "value"=>"_SESSION") );
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
				$mHtml->SetBody('</form>');
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();

		#Tabs
		$mHtml->OpenDiv("id:tabs");
			$mHtml->SetBody('<ul>');
				$mHtml->SetBody('<li><a id="liGenera" href="#tabs-g" style="cursor:pointer">GENERAL</a></li>');
			$mHtml->SetBody('</ul>');

			$mHtml->SetBody('<div id="tabs-g"></div>'); #DIV GENERAL
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: generateReportG
	 *  \brief: Genera el reporte general
	 *  \author: Ing. Fabian Salinas
	 *  \date:  14/12/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function generateReportG()
	{
		$mTittle[0] = array('Novedades Registradas', 'Novedades Resueltas', 'Porcentaje', 'Novedades Pendientes', 'Porcentaje');
		$mTittle[1] = array('Fecha', 'Novedades Registradas', 'Novedades Resueltas', '%', 'Novedades Pendientes', '%');
		/*$mTittle[0] = array('Novedades Registradas', 'Novedades Resueltas', 'Porcentaje', 'Novedades Pendientes', 'Porcentaje', 'Otros', 'Porcentaje');
		$mTittle[1] = array('Fecha', 'Novedades Registradas', 'Novedades Resueltas', '%', 'Novedades Pendientes', '%', 'Otros', '%');*/
		
		if( $_REQUEST['fec_inicia'] && $_REQUEST['fec_finali'] )
		{ #Fechas según rango filtrado
			$mFecInicia = $_REQUEST['fec_inicia'];
			$mFecFinalx = $_REQUEST['fec_finali'];
			$mData = self::getData($mFecInicia, $mFecFinalx);
		}else{ #Fechas rin rango filtrado
			$mData = self::getData();
			$mFecInicia = explode(' ', $mData[0]);
			$mFecFinalx = explode(' ', $mData[1]);
			$mFecInicia = $mFecInicia[0];
			$mFecFinalx = $mFecFinalx[0];
		}

		$mHtml  = '';
		$mTd = '<td class="cellInfo onlyCell" align="center">-</td>';
		$mTotax = array(0, 0, 0, 0);
		$mDate = $mFecInicia;

		while( $mDate <= $mFecFinalx )
		{
			$mTotal = $mData[$mDate]['otrosx'] + $mData[$mDate]['pendie'] + $mData[$mDate]['ejecut'];
			$mTxt = strftime("%A, %d de %B del %Y", strtotime($mDate) );

			$mTotax[0] += $mTotal;
			$mTotax[1] += $mData[$mDate]['ejecut'];
			$mTotax[2] += $mData[$mDate]['pendie'];
			$mTotax[3] += $mData[$mDate]['otrosx'];

			$mHtml .= '<tr>';
				$mHtml .= '<td class="cellInfo onlyCell">'.ucwords($mTxt).'</td>';
				$mHtml .= $mTotal == 0 ? $mTd : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$mDate.'\', \''.$mDate.'\', \'g\' );" >'.$mTotal.'</td>';
				$mHtml .= $mData[$mDate]['ejecut'] == 0 ? $mTd : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$mDate.'\', \''.$mDate.'\', \'ejecut\' );" >'.$mData[$mDate]['ejecut'].'</td>';
				$mHtml .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mData[$mDate]['ejecut'] / $mTotal), 0) ).'%</td>';
				$mHtml .= $mData[$mDate]['pendie'] == 0 ? $mTd : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$mDate.'\', \''.$mDate.'\', \'pendie\' );" >'.$mData[$mDate]['pendie'].'</td>';
				$mHtml .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mData[$mDate]['pendie'] / $mTotal), 0) ).'%</td>';
				/*$mHtml .= $mData[$mDate]['otrosx'] == 0 ? $mTd : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$mDate.'\', \''.$mDate.'\', \'otrosx\' );" >'.$mData[$mDate]['otrosx'].'</td>';
				$mHtml .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mData[$mDate]['otrosx'] / $mTotal), 0) ).'%</td>';*/
			$mHtml .= '</tr>';

			$mDate = date ( 'Y-m-d', strtotime( '+1 day', strtotime($mDate) ) );
		}

		#<Fila de totales>
			$mHtml3  = $mTotax[0] == 0 ? $mTd : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$mFecInicia.'\', \''.$mFecFinalx.'\', \'g\' );" >'.$mTotax[0].'</td>';
			$mHtml3 .= $mTotax[1] == 0 ? $mTd : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$mFecInicia.'\', \''.$mFecFinalx.'\', \'ejecut\' );" >'.$mTotax[1].'</td>';
			$mHtml3 .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mTotax[1] / $mTotax[0]), 0) ).'%</td>';
			$mHtml3 .= $mTotax[2] == 0 ? $mTd : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$mFecInicia.'\', \''.$mFecFinalx.'\', \'pendie\' );" >'.$mTotax[2].'</td>';
			$mHtml3 .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mTotax[2] / $mTotax[0]), 0) ).'%</td>';
			/*$mHtml3 .= $mTotax[3] == 0 ? $mTd : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$mFecInicia.'\', \''.$mFecFinalx.'\', \'otrosx\' );" >'.$mTotax[3].'</td>';
			$mHtml3 .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mTotax[3] / $mTotax[0]), 0) ).'%</td>';*/
		#<Fila de totales>

		#<Table Detallado por Dias>
			$mHtml2  = '<table width="100%" align="center">';

			$mHtml2 .= '<tr>';
			$mHtml2 .= '<th class="CellHead" colspan="'.sizeof($mTittle[1]).'" >DETALLADO POR DIAS</th>';
			$mHtml2 .= '</tr>';

			$mHtml2 .= '<tr>';
			foreach ($mTittle[1] as $value)
				$mHtml2 .= '<th class="CellHead">'.$value.'</th>';
			$mHtml2 .= '</tr>';

			$mHtml2 .= $mHtml;

			$mHtml2 .= '<tr>';
			$mHtml2 .= '<td class="cellInfo onlyCell">Total</td>';
			$mHtml2 .= $mHtml3;
			$mHtml2 .= '</tr>';

			$mHtml2 .= '</table>';
		#</Table Detallado por Dias>

		#<Table General>
			$mHtml1  = '<table width="90%" align="center">';

			$mHtml1 .= '<tr>';
			$mHtml1 .= '<th class="CellHead" colspan="'.sizeof($mTittle[0]).'" >GENERAL OPERACION GENERADORES DE CARGA</th>';
			$mHtml1 .= '</tr>';

			$mHtml1 .= '<tr>';
			foreach ($mTittle[0] as $value)
				$mHtml1 .= '<th class="CellHead">'.$value.'</th>';
			$mHtml1 .= '</tr>';

			$mHtml1 .= '<tr>';
			$mHtml1 .= $mHtml3;
			$mHtml1 .= '</tr>';

			$mHtml1 .= '</table>';
		#</Table General>

		$mHtml  = $mHtml1;
		$mHtml .= '<br/>';
		$mHtml .= $mHtml2;

		echo $mHtml;
	}

	/*! \fn: getData
	 *  \brief: Trae la data para el informe general
	 *  \author: Ing. Fabian Salinas
	 *  \date:  14/12/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mFecInicia  Date  Fecha Inicial
	 *  \param: mFecFinalx  Date  Fecha Final
	 *  \return: Matriz
	 */
	private function getData( $mFecInicia = null, $mFecFinalx = null )
	{
		$mWhere  = !$_REQUEST['num_viajex'] ? "" : " AND b.num_desext LIKE '$_REQUEST[num_viajex]' ";
		$mWhere .= !$mFecInicia && !$mFecFinalx ? "" : " AND DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') BETWEEN '$mFecInicia' AND '$mFecFinalx' ";

		if( $_SESSION['datos_usuario']['cod_perfil'] == '719' ){
			$mUserInterf = self::getUserInterf( $_SESSION['datos_usuario']['cod_usuari'] );
			$mWhere .= " AND ( a.usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."' OR a.usr_creaci = '$mUserInterf' ) ";
		}else{
			$mWhere .= " AND c.cod_perfil = 719 ";
		}

		$mSql = "SELECT x.ind_ejecuc, x.fec_regist, SUM(x.cantidad) AS cantidad 
				   FROM (
							(
								 SELECT y.ind_ejecuc, y.ind_tablax, y.fec_regist, 
										COUNT(y.num_despac) AS cantidad 
								   FROM (	  SELECT d.ind_ejecuc, '1' AS ind_tablax, a.num_despac, 
													 DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') AS fec_regist 
												FROM ".BASE_DATOS.".tab_despac_noveda a 
										  INNER JOIN ".BASE_DATOS.".tab_despac_sisext b 
												  ON a.num_despac = b.num_despac 
										  INNER JOIN ".BASE_DATOS.".tab_protoc_asigna d 
												  ON a.num_despac = d.num_despac 
												 AND a.cod_contro = d.cod_contro 
												 AND a.cod_noveda = d.cod_noveda 
												/* AND a.fec_noveda = d.fec_noveda */
										  INNER JOIN ".BASE_DATOS.".tab_genera_usuari c 
												  ON a.cod_usrcre = c.cod_consec 
											   WHERE 1=1 $mWhere 
											GROUP BY d.num_despac, d.cod_contro, d.cod_noveda, d.fec_noveda
										) y 
							   GROUP BY y.fec_regist, y.ind_ejecuc 
							)
							UNION ALL
							(
								 SELECT z.ind_ejecuc, z.ind_tablax, z.fec_regist, 
										COUNT(z.num_despac) AS cantidad 
								   FROM (
											  SELECT d.ind_ejecuc, '0' AS ind_tablax, a.num_despac, 
													 DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') AS fec_regist 
												FROM ".BASE_DATOS.".tab_despac_contro a 
										  INNER JOIN ".BASE_DATOS.".tab_despac_sisext b 
												  ON a.num_despac = b.num_despac 
										  INNER JOIN ".BASE_DATOS.".tab_protoc_asigna d 
												  ON a.num_despac = d.num_despac 
												 AND a.cod_contro = d.cod_contro 
												 AND a.cod_noveda = d.cod_noveda 
												/* AND a.cod_consec = d.cod_consec */
										  INNER JOIN ".BASE_DATOS.".tab_genera_usuari c 
												  ON a.cod_usrcre = c.cod_consec 
											   WHERE 1=1 $mWhere 
											GROUP BY d.num_despac, d.cod_contro, d.cod_noveda, d.fec_noveda
										) z 
							   GROUP BY z.fec_regist, z.ind_ejecuc 
							)
						) x 
			   GROUP BY x.fec_regist, x.ind_ejecuc 
			   ORDER BY x.fec_regist 
				";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');
		$t = sizeof($mResult)-1;

		$mData = array( 0 => $mResult[0]['fec_regist'], 1 => $mResult[$t]['fec_regist'] );
		
		foreach ($mResult as $i)
		{
			switch ($i['ind_ejecuc']) {
				case '0':
					$a = 'pendie';
					break;

				case '1':
					$a = 'ejecut';
					break;
				
				default:
					$a = 'otrosx';
					break;
			}

			$mData[$i['fec_regist']][$a] = $i['cantidad'];
		}

		return $mData;
	}

	/*! \fn: getDataDetail
	 *  \brief: Trae la data para el informe detallado
	 *  \author: Ing. Fabian Salinas
	 *  \date: 14/12/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mFecInicia  Date  Fecha Inicial
	 *  \param: mFecFinalx  Date  Fecha Final
	 *  \return: Matriz
	 */
	private function getDataDetail( $mFecInicia = null, $mFecFinalx = null )
	{
		$mWhere  = !$_REQUEST['num_viajex'] ? "" : " AND b.num_desext LIKE '$_REQUEST[num_viajex]' ";
		$mWhere .= !$mFecInicia && !$mFecFinalx ? "" : " AND DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') BETWEEN '$mFecInicia' AND '$mFecFinalx' ";
		
		if( $_SESSION['datos_usuario']['cod_perfil'] == '719' ){
			$mUserInterf = self::getUserInterf( $_SESSION['datos_usuario']['cod_usuari'] );
			$mWhere .= " AND ( a.usr_creaci = '".$_SESSION['datos_usuario']['cod_usuari']."' OR a.usr_creaci = '$mUserInterf' ) ";
		}else{
			$mWhere .= " AND c.cod_perfil = 719 ";
		}

		switch ($_REQUEST['ind_report']) {
			case 'ejecut':
				$mWhere .= " AND d.ind_ejecuc = '1' ";
				break;
			case 'pendie':
				$mWhere .= " AND d.ind_ejecuc = '0' ";
				break;
			case 'otrosx':
				$mWhere .= " AND d.ind_ejecuc = '' ";
				break;
			default:
				$mWhere .= "";
				break;
		}

		$mSql = "(	  SELECT a.num_despac, b.num_desext, e.cod_manifi, 
							 e.fec_despac, f.nom_tipdes, g.nom_ciudad AS nom_ciuori, 
							 h.nom_ciudad AS nom_ciudes, i.cod_conduc, 
							 IF(i.nom_conduc IS NULL OR i.nom_conduc = '', m.abr_tercer, i.nom_conduc) AS nom_conduc, 
							 IF(e.con_telef1 IS NULL OR e.con_telef1 = '', m.num_telmov, e.con_telef1) AS con_telef1, 
							 b.num_pedido, i.num_placax, d.obs_noved2, 
							 j.nom_poseed, k.nom_produc, l.nom_noveda, 
							 a.des_noveda AS obs_noveda, d.fec_noveda AS fec_asigna, d.fec_noved2 AS fec_soluci, 
							 TIMESTAMPDIFF(MINUTE, d.fec_noved2, d.fec_noveda) AS num_difere, 
							 d.usr_asigna, d.usr_ejecut, '1' AS tab_origen 
						FROM ".BASE_DATOS.".tab_despac_noveda a 
				  INNER JOIN ".BASE_DATOS.".tab_despac_sisext b 
						  ON a.num_despac = b.num_despac 
				  INNER JOIN ".BASE_DATOS.".tab_protoc_asigna d 
						  ON a.num_despac = d.num_despac 
						 AND a.cod_contro = d.cod_contro 
						 AND a.cod_noveda = d.cod_noveda 
						/* AND a.fec_noveda = d.fec_noveda */
				  INNER JOIN ".BASE_DATOS.".tab_genera_usuari c 
						  ON a.cod_usrcre = c.cod_consec 
				  INNER JOIN ".BASE_DATOS.".tab_despac_despac e 
						  ON a.num_despac = e.num_despac 
				  INNER JOIN ".BASE_DATOS.".tab_genera_tipdes f 
						  ON e.cod_tipdes = f.cod_tipdes 
				  INNER JOIN ".BASE_DATOS.".tab_genera_ciudad g 
						  ON e.cod_paiori = g.cod_paisxx 
						 AND e.cod_depori = g.cod_depart 
						 AND e.cod_ciuori = g.cod_ciudad 
				  INNER JOIN ".BASE_DATOS.".tab_genera_ciudad h 
						  ON e.cod_paides = h.cod_paisxx 
						 AND e.cod_depdes = h.cod_depart 
						 AND e.cod_ciudes = h.cod_ciudad 
				  INNER JOIN ".BASE_DATOS.".tab_despac_vehige i 
						  ON a.num_despac = i.num_despac 
				  INNER JOIN ".BASE_DATOS.".tab_despac_corona j 
						  ON a.num_despac = j.num_dessat 
				  INNER JOIN ".BASE_DATOS.".tab_genera_produc k 
						  ON b.cod_mercan = k.cod_produc 
				  INNER JOIN ".BASE_DATOS.".tab_genera_noveda l 
						  ON a.cod_noveda = l.cod_noveda 
				  INNER JOIN ".BASE_DATOS.".tab_tercer_tercer m 
						  ON i.cod_conduc = m.cod_tercer
					   WHERE 1=1 $mWhere 
					GROUP BY d.num_despac, d.cod_contro, d.cod_noveda, d.fec_noveda 
				 )
				 UNION ALL 
				 (	  SELECT a.num_despac, b.num_desext, e.cod_manifi, 
							 e.fec_despac, f.nom_tipdes, g.nom_ciudad AS nom_ciuori, 
							 h.nom_ciudad AS nom_ciudes, i.cod_conduc, 
							 IF(i.nom_conduc IS NULL OR i.nom_conduc = '', m.abr_tercer, i.nom_conduc) AS nom_conduc, 
							 IF(e.con_telef1 IS NULL OR e.con_telef1 = '', m.num_telmov, e.con_telef1) AS con_telef1, 
							 b.num_pedido, i.num_placax, d.obs_noved2, 
							 j.nom_poseed, k.nom_produc, l.nom_noveda, 
							 a.obs_contro AS obs_noveda, d.fec_noveda AS fec_asigna, d.fec_noved2 AS fec_soluci, 
							 TIMESTAMPDIFF(MINUTE, d.fec_noved2, d.fec_noveda) AS num_difere, 
							 d.usr_asigna, d.usr_ejecut, '0' AS tab_origen 
						FROM ".BASE_DATOS.".tab_despac_contro a 
				  INNER JOIN ".BASE_DATOS.".tab_despac_sisext b 
						  ON a.num_despac = b.num_despac 
				  INNER JOIN ".BASE_DATOS.".tab_protoc_asigna d 
						  ON a.num_despac = d.num_despac 
						 AND a.cod_contro = d.cod_contro 
						 AND a.cod_noveda = d.cod_noveda 
						/* AND a.cod_consec = d.cod_consec */
				  INNER JOIN ".BASE_DATOS.".tab_genera_usuari c 
						  ON a.cod_usrcre = c.cod_consec 
				  INNER JOIN ".BASE_DATOS.".tab_despac_despac e 
						  ON a.num_despac = e.num_despac 
				  INNER JOIN ".BASE_DATOS.".tab_genera_tipdes f 
						  ON e.cod_tipdes = f.cod_tipdes 
				  INNER JOIN ".BASE_DATOS.".tab_genera_ciudad g 
						  ON e.cod_paiori = g.cod_paisxx 
						 AND e.cod_depori = g.cod_depart 
						 AND e.cod_ciuori = g.cod_ciudad 
				  INNER JOIN ".BASE_DATOS.".tab_genera_ciudad h 
						  ON e.cod_paides = h.cod_paisxx 
						 AND e.cod_depdes = h.cod_depart 
						 AND e.cod_ciudes = h.cod_ciudad 
				  INNER JOIN ".BASE_DATOS.".tab_despac_vehige i 
						  ON a.num_despac = i.num_despac 
				  INNER JOIN ".BASE_DATOS.".tab_despac_corona j 
						  ON a.num_despac = j.num_dessat 
				  INNER JOIN ".BASE_DATOS.".tab_genera_produc k 
						  ON b.cod_mercan = k.cod_produc 
				  INNER JOIN ".BASE_DATOS.".tab_genera_noveda l 
						  ON a.cod_noveda = l.cod_noveda 
				  INNER JOIN ".BASE_DATOS.".tab_tercer_tercer m 
						  ON i.cod_conduc = m.cod_tercer
					   WHERE 1=1 $mWhere 
					GROUP BY d.num_despac, d.cod_contro, d.cod_noveda, d.fec_noveda 
				 )
				";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('a');
	}

	/*! \fn: detailReport
	 *  \brief: Reporte detallado
	 *  \author: Ing. Fabian Salinas
	 *  \date:  14/12/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function detailReport()
	{
		$mTittle = array( 'num_despac' => 'Despacho SAT', 
						  'num_desext' => 'Viaje', 
						  'cod_manifi' => 'Manifiesto', 
						  'fec_despac' => 'Fecha Despacho', 
						  'nom_tipdes' => 'Tipo Despacho', 
						  'nom_ciuori' => 'Origen', 
						  'nom_ciudes' => 'Destino', 
						  'cod_conduc' => 'C.C. Conductor', 
						  'nom_conduc' => 'Nombre Conductor', 
						  'con_telef1' => 'Celular Conductor', 
						  'num_pedido' => 'Pedido', 
						  'num_placax' => 'Placa', 
						  'nom_poseed' => 'Poseedor', 
						  'nom_produc' => 'Mercancia / Negocio', 
						  'nom_noveda' => 'Novedad', 
						  'obs_noveda' => 'Observación Novedad', 
						  'fec_asigna' => 'Fecha Asignación', 
						  'fec_soluci' => 'Fecha Solución', 
						  'obs_noved2' => 'Observación Solución', 
						  'num_difere' => 'Diferencia', 
						  'usr_asigna' => 'Usuario Asignado', 
						  'usr_ejecut' => 'Usuario que Gestiona' 
						);

		$mData = self::getDataDetail( $_REQUEST['fec_inicia'], $_REQUEST['fec_finali'] );

		#Construye el HTML
		$mHtml = new Formlib(2);

		$mHtml->OpenDiv("id:formID; class:Style2DIV");
			$mHtml->Table('tr');
				$mHtml->Label( "Se encontraron ".sizeof($mData)." registros", array("class"=>"CellHead", "colspan"=>sizeof($mTittle), "align"=>"left", "end"=>true) ); #Encabezado

				foreach ($mTittle as $title) #Titulos
					$mHtml->Label( "<b>$title</b>", array("class"=>"CellHead", "align"=>"center") );
				$mHtml->CloseRow();

				#Registros
				foreach ($mData as $row){
					$mHtml->Row();
					foreach ($mTittle as $i => $title){
						$mHtml->Label( utf8_encode($row[$i]), array("class"=>"cellInfo onlyCell", "align"=>"left") );
					}
					$mHtml->CloseRow();
				}

			$mHtml->SetBody('</table>');
		$mHtml->CloseDiv();

		$_SESSION['exportExcel'] = $mHtml->MakeHtml();

		$mHtml = '<center>
					<img border="0" style="cursor:pointer" onclick="exportTableExcelNovEmpTer();" src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg">
				  </center>';

		echo $mHtml.$_SESSION['exportExcel'];
	}

    /*! \fn: getUserInterf
     *  \brief: Trae el usuario de interfaz relacionado al usuario actual
     *  \author: Ing. Fabian Salinas
     *  \date:  18/01/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mUser  String  Codigo del usuario actual
     *  \return: String
     */
    private function getUserInterf( $mUser = null ){
        $mSql = "SELECT a.usr_interf 
                   FROM ".BASE_DATOS.".tab_genera_usuari a 
                  WHERE a.cod_usuari = '$mUser' ";
        $mConsult = new Consulta($mSql, self::$cConexion );
        $mResult = $mConsult -> ret_matrix('i');
        return $mResult[0][0];
    }
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new InfNovedadesEmpTer();
else
	$_INFORM = new InfNovedadesEmpTer( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>