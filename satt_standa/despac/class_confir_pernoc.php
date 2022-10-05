<?php 
/*! \file: ConfirPernoc
 *  \brief: Archivo para el manejo de la confirmacion de Pernoctacion
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 07/03/2016
 *  \bug: 
 *  \warning: 
 */

class ConfirPernoc
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cNull = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null) {
		if($_REQUEST['Ajax'] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion  = $AjaxConnection;
			self::$cUsuario   = $_SESSION['datos_usuario'];
			self::$cCodAplica = $_SESSION['codigo'];
		}else{
			self::$cConexion  = $co;
			self::$cUsuario   = $us;
			self::$cCodAplica = $ca;
		}

		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );

		switch($_REQUEST['Option']){
			case 'verifyConfirPernoc':
				self::verifyConfirPernoc();
				break;

			case 'formReportPernoc':
				self::formReportPernoc();
				break;

			case 'formNoReporPernoc':
				self::formNoReporPernoc();
				break;

			case 'savePernoc':
				self::savePernoc();
				break;

			default:
				die("�Acceso Denegado!");
				break;
		}
	}

	/*! \fn: verifyConfirPernoc
	 *  \brief: Verifica si es necesario Confirmar la Pernoctacion
	 *  \author: Ing. Fabian Salinas
	 *  \date: 07/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function verifyConfirPernoc(){
		$mResult = true;
		
		$mSql = "SELECT a.cod_tipdes, b.cod_transp, 
						DATE_FORMAT(NOW(), '%Y-%m-%d') AS fec_actual, 
						DATE_FORMAT(NOW(), '%H:%i:00') AS hor_actual 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
				  WHERE a.num_despac = '$_REQUEST[num_despac]' ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mData = $mConsult -> ret_matrix('a');
		$mData = $mData[0];

		
		switch ($mData['cod_tipdes']) {
			case 1: $x = "urb"; break;
			case 2: $x = "nac"; break;
			case 3: $x = "imp"; break;
			case 4: $x = "exp"; break;
			case 5: $x = "tr1"; break;
			case 6: $x = "tr2"; break;
			default: $mResult = false; break;
		}

		if( $mResult ){
			 
			$mTransp = getTransTipser( self::$cConexion, " AND a.cod_transp = '$mData[cod_transp]' ", array("ind_conper", "hor_pe1".$x, "hor_pe2".$x) );
			$mTransp = $mTransp[0];

			$mHorIni = $mTransp["hor_pe1".$x] != '' ? $mTransp["hor_pe1".$x] : '00:00';
			$mHorFin = $mTransp["hor_pe2".$x] != '' ? $mTransp["hor_pe2".$x] : '00:00';
			$mFecIni = date($mData['fec_actual']." ".$mHorIni.":00");
			$mFecFin = date($mData['fec_actual']." ".$mHorFin.":00");
			$mFecAct = date($mData['fec_actual']." ".$mData["hor_actual"]);

			if( $mFecFin <= $mFecIni )
				$mFecFin = date( "Y-m-d H:i:s", strtotime('+1 day', strtotime($mFecFin)) );
			$mIndVerify = self::getDespacPernoc( $_REQUEST['num_despac'], $mFecIni, $mFecFin );
 
			if( $mFecAct <= $mFecFin && $mFecAct >= $mFecIni && sizeof($mIndVerify) < 1 && $mTransp["ind_conper"] == 1 ){
				$mResult = self::formConfirPernoc();
			}else{
				$mResult = false;
			}
		}

		echo $mResult;
	}

	/*! \fn: formConfirPernoc
	 *  \brief: Formulario para realizar la confirmación de pernoctación
	 *  \author: Ing. Fabian Salinas
	 *  \date: 07/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: html
	 */
	private function formConfirPernoc(){
		$mHtml = new Formlib(2);

		$mHtml->Table('tr');
			$mHtml->Hidden( array("name"=>"ind_option", "id"=>"ind_optionID", "value"=>"0") );
			$mHtml->Hidden( array("name"=>"num_despac", "id"=>"num_despacID", "value"=>$_REQUEST['num_despac']) );
			$mHtml->Hidden( array("name"=>"cod_contrx", "id"=>"cod_contrxID", "value"=>$_REQUEST['cod_contro']) );
			$mHtml->Hidden( array("name"=>"ind_origen", "id"=>"ind_origenID", "value"=>$_REQUEST['ind_origen']) );
			$mHtml->Label("INFORMACION DE PERNOCTACION", array("class"=>"cellHead", "align"=>"center", "colspan"=>"6", "end"=>"true"));

			$mHtml->Label("Pernoctacion", array("class"=>"cellInfo1", "width"=>"16.5%"));
			$mHtml->Radio( array("name"=>"ind_pernoc", "id"=>"ind_pernocID", "value"=>"1", "class"=>"cellInfo1 izquierda", "width"=>"16.5%", "onclick"=>"formReporPernoc(1, this)") );
			$mHtml->Label("Posible Pernoctacion", array("class"=>"cellInfo1", "width"=>"16.5%"));
			$mHtml->Radio( array("name"=>"ind_pernoc", "id"=>"ind_pernocID", "value"=>"2", "class"=>"cellInfo1 izquierda", "width"=>"16.5%", "onclick"=>"formReporPernoc(2, this)") );
			$mHtml->Label("No Reporta Aun", array("class"=>"cellInfo1", "width"=>"16.5%"));
			$mHtml->Radio( array("name"=>"ind_pernoc", "id"=>"ind_pernocID", "value"=>"0", "class"=>"cellInfo1 izquierda", "width"=>"16.5%", "onclick"=>"formReporPernoc(3, this)") );
		$mHtml->CloseTable('tr');

		$mHtml->SetBody('<table id="tabConfirPernocID" align="center" width="100%" cellspacing="0" cellpadding="3" border="0"></table>');

		return $mHtml->MakeHtml();
	}

	/*! \fn: formNoReporPernoc
	 *  \brief: Formulario no reporta pernoctacion
	 *  \author: Ing. Fabian Salinas
	 *  \date: 08/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function formNoReporPernoc(){
		$mHtml = new Formlib(2);

		$mHtml->SetBody('<tr>');
		$mHtml->Label("* Justificacion: ", array("class"=>"cellInfo1", "width"=>"50%"));
		$mHtml->Input( array("name"=>"obs_justif", "id"=>"obs_justifID", "class"=>"cellInfo1", "width"=>"50%",
							 "obl"=>"1", "end"=>true) );

		$mHtml->Button( array("value"=>"&nbsp; Guardar &nbsp;", "colspan"=>"2", "align"=>"center", "class2"=>"cellInfo1", 
							  "class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only",
							  "onclick"=>"savePernoc()", "end"=>true) );

		echo $mHtml->MakeHtml();
	}

	/*! \fn: formReportPernoc
	 *  \brief: Formulario para reportar Pernoctacion o Posible Pernoctacion
	 *  \author: Ing. Fabian Salinas
	 *  \date: 09/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function formReportPernoc(){
		$mHtml = new Formlib(2);

		$mHtml->SetBody('<tr>');

		if( $_REQUEST['ind_option'] == '1' && $_REQUEST['cod_contrx'] == '' ){
			$mPueContro = self::getPCPendientes( $_REQUEST['num_despac'] );

			$mHtml->Label("* Puesto de control: ", array("class"=>"cellInfo1", "width"=>"50%"));
			$mHtml->Select2( array_merge(self::$cNull, $mPueContro), array("name"=>"cod_contro", "id"=>"cod_controID", "class"=>"cellInfo1", "width"=>"50%", "obl"=>"1", "end"=>true) );
		}

		$mHtml->Label("* Ubicación: ", array("class"=>"cellInfo1", "width"=>"50%"));
		$mHtml->Input( array("name"=>"obs_ubicac", "id"=>"obs_ubicacID", "class"=>"cellInfo1", "width"=>"50%", "obl"=>"1", "end"=>true) );
		$mHtml->Label("Parqueadero: ", array("class"=>"cellInfo1"));
		$mHtml->Input( array("name"=>"obs_parque", "id"=>"obs_parqueID", "class"=>"cellInfo1", "width"=>"50%", "end"=>true) );
		$mHtml->Label("Hotel: ", array("class"=>"cellInfo1"));
		$mHtml->Input( array("name"=>"obs_hotelx", "id"=>"obs_hotelxID", "class"=>"cellInfo1", "width"=>"50%", "end"=>true) );
		$mHtml->Label("* Fecha de inicio: ", array("class"=>"cellInfo1 fecha"));
		$mHtml->Input( array("name"=>"fec_inicio", "id"=>"fec_inicioID", "readonly"=>"true", "class"=>"cellInfo1", "width"=>"50%", "obl"=>"1", "end"=>true) );
		$mHtml->Label("* Hora de inicio: ", array("class"=>"cellInfo1 hora"));
		$mHtml->Input( array("name"=>"hor_inicio", "id"=>"hor_inicioID", "readonly"=>"true", "class"=>"cellInfo1", "width"=>"50%", "obl"=>"1", "end"=>true) );
		$mHtml->Label("* Fecha de reinicio: ", array("class"=>"cellInfo1 fecha"));
		$mHtml->Input( array("name"=>"fec_reinic", "id"=>"fec_reinicID", "readonly"=>"true", "class"=>"cellInfo1", "width"=>"50%", "obl"=>"1", "end"=>true) );
		$mHtml->Label("* Hora de reinicio: ", array("class"=>"cellInfo1 hora"));
		$mHtml->Input( array("name"=>"hor_reinic", "id"=>"hor_reinicID", "readonly"=>"true", "class"=>"cellInfo1", "width"=>"50%", "obl"=>"1", "end"=>true) );

		$mHtml->Button( array("value"=>"&nbsp; Guardar &nbsp;", "colspan"=>"2", "align"=>"center", "class2"=>"cellInfo1", 
							  "class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only",
							  "onclick"=>"savePernoc()", "end"=>true) );

		echo $mHtml->MakeHtml();
	}

	/*! \fn: savePernoc
	 *  \brief: Guarda la Confirmacion de Pernoctacion
	 *  \author: Ing. Fabian Salinas
	 *  \date: 09/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function savePernoc(){
		if( $_REQUEST['cod_contro'] == '' ){
			$mContro = getNextPC( self::$cConexion, $_REQUEST['num_despac'] );
			$_REQUEST['cod_contro'] = $mContro['cod_contro'];
		}

		if( $_REQUEST['ind_pernoc'] == 1 && $_REQUEST['ind_origen'] == 2 )
			$_REQUEST['ind_pernoc'] = 2;

		$mData = $_REQUEST;

		if( $_REQUEST['ind_pernoc'] != 0 ){
			$mData['fec_inicio'] = "'$_REQUEST[fec_inicio] $_REQUEST[hor_inicio]:00'";
			$mData['fec_reinic'] = "'$_REQUEST[fec_reinic] $_REQUEST[hor_reinic]:00'";
		}else{
			$mData['fec_inicio'] = "NULL";
			$mData['fec_reinic'] = "NULL";
		}

		self::insertDespacPerno2($mData);

		if( $_REQUEST['ind_option'] == 1 && $_REQUEST['ind_origen'] == '1' ){
			self::saveNoveda( $_REQUEST );
		}

		echo "1";
	}

	/*! \fn: insertDespacPerno2
	 *  \brief: Inserta en la tabla tab_despac_perno2
	 *  \author: Ing. Fabian Salinas
	 *  \date: 16/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mData  Array  Data a insertar
	 *  \return: 
	 */
	private function insertDespacPerno2( $mData ){
		$mSql = "INSERT INTO ".BASE_DATOS.".tab_despac_perno2 
					(num_despac, cod_contro, ind_pernoc, 
					 fec_inicio, fec_reinic, obs_ubicac, 
					 obs_parque, obs_hotelx, obs_justif, 
					 usr_creaci, fec_creaci
					)
				 VALUES 
					('$mData[num_despac]', '$mData[cod_contro]', '$mData[ind_pernoc]', 
					 $mData[fec_inicio], $mData[fec_reinic], '$mData[obs_ubicac]', 
					 '$mData[obs_parque]', '$mData[obs_hotelx]', '$mData[obs_justif]', 
					 '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
					) ";
		$mConsult = new Consulta($mSql, self::$cConexion );
	}

	/*! \fn: getDespacPernoc
	 *  \brief: Trae la gestion de pernoctacion de un despacho por rango de fecha
	 *  \author: Ing. Fabian Salinas
	 *  \date: 14/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mDespac  Integer  Numero del despaho
	 *  \param: mFecini  Date     Fecha Inicial
	 *  \param: mFecFin  Date     Fecha Final
	 *  \return: Matriz
	 */
	private function getDespacPernoc($mDespac, $mFecIni, $mFecFin){
		$mSql = "/* OPTIMIZADO cod_servic=".self::$cCodAplica."*/
				 SELECT a.cod_consec 
				   FROM ".BASE_DATOS.".tab_despac_perno2 a 
				  WHERE a.num_despac = '$mDespac' 
					AND DATE_FORMAT(a.fec_creaci, '%Y-%m-%d') BETWEEN '$mFecIni' AND '$mFecFin' ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('a');
	}

	/*! \fn: getPCPendientes
	 *  \brief: Trae los puestos de control pendientes de un despacho
	 *  \author: Ing. Fabian Salinas
	 *  \date: 14/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mNumDes  Integer  Numero del despacho
	 *  \return: Matriz
	 */
	private function getPCPendientes( $mNumDes ){
		$mSigContro = getNextPC( self::$cConexion, $mNumDes );

		$mSql = "SELECT a.cod_contro, b.nom_contro 
				   FROM ".BASE_DATOS.".tab_despac_seguim a 
			 INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
					 ON a.cod_contro = b.cod_contro 
			 INNER JOIN ".BASE_DATOS.".tab_genera_rutcon c 
					 ON a.cod_rutasx = c.cod_rutasx 
					AND a.cod_contro = c.cod_contro 
				  WHERE a.num_despac = '$mNumDes' 
					AND a.cod_rutasx = $mSigContro[cod_rutasx]
					AND c.val_duraci >= ( 
											SELECT x.val_duraci 
											  FROM ".BASE_DATOS.".tab_genera_rutcon x 
											 WHERE x.cod_contro = $mSigContro[cod_contro] 
											   AND x.cod_rutasx = $mSigContro[cod_rutasx] 
										)
			   ORDER BY c.val_duraci ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}

	/*! \fn: saveNoveda
	 *  \brief: Llama a InsertNovedad.inc para guardar la novedad antes de sitio
	 *  \author: Ing. Fabian Salinas
	 *  \date: 14/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \return: 
	 */
	private function saveNoveda(){

		@include_once( "../despac/InsertNovedad.inc" );
		$mInsNoveda = new InsertNovedad($_REQUEST['cod_servic'], 3, $_SESSION['codigo'], self::$cConexion);

		$mFecAct = date("Y-m-d H:i:s");
		$mData = array();
		$mDespac = self::getDataAddDespac( $_REQUEST['num_despac'], $_REQUEST['cod_contro'] );
		$mTieAdicio = abs( (strtotime($_REQUEST['fec_reinic']." ".$_REQUEST['hor_reinic'].":00") - strtotime($mFecAct)) ) / 60;
		$mTieAdicio = round($mTieAdicio);

		if( $mDespac['fec_ultnov'] != '' ){
			$mTieUltNov = abs( (strtotime($mFecAct) - strtotime($mDespac['fec_ultnov'])) ) / 60;
			$mTieUltNov = round($mTieUltNov);
		}else{
			$mTieUltNov = 0;
		}

		$mData['email'] = $_SESSION['datos_usuario']['usr_emailx'];
		$mData['virtua'] = $mDespac['ind_virtua'];
		$mData['tip_servic'] = $mDespac['cod_tipser'];
		$mData['celular'] = '';
		$mData['despac'] = $_REQUEST['num_despac'];
		$mData['contro'] = $_REQUEST['cod_contro'];
		$mData['noveda'] = 6;
		$mData['tieadi'] = $mTieAdicio;
		$mData['fecact'] = $mFecAct;
		$mData['fecnov'] = $mFecAct;
		$mData['usuari'] = $_SESSION['datos_usuario']['cod_usuari'];
		$mData['nittra'] = $mDespac['cod_transp'];
		$mData['indsit'] = '1';
		$mData['sitio'] = $mDespac['nom_contro'];
		$mData['tie_ultnov'] = $mTieUltNov;
		$mData['tiem'] = '0';
		$mData['rutax'] = $mDespac['cod_rutasx'];
		$mData['observ'] = "EL CONDUCTOR INFORMA QUE SE ENCUENTRA UBICADO EN: $_REQUEST[obs_ubicac]";
		$mData['observ'] .= $_REQUEST['obs_parque'] == "" ? "" : ", PARQUEADERO: $_REQUEST[obs_parque]";
		$mData['observ'] .= $_REQUEST['obs_hotelx'] == "" ? "" : ", HOTEL: $_REQUEST[obs_hotelx]";
		$mData['observ'] .= "; REINICIA RUTA: $_REQUEST[fec_reinic] $_REQUEST[hor_reinic]:00.";

		$mInsNoveda->InsertarNovedadNC( BASE_DATOS, $mData, 0 );
	}

	/*! \fn: getDataAddDespac
	 *  \brief: Trae la data adicional del despacho
	 *  \author: Ing. Fabian Salinas
	 *  \date: 15/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mNumDespac  Integer  Numero del Despacho
	 *  \return: Matriz
	 */
	private function getDataAddDespac( $mNumDespac, $mCodContro ){
		$mUltNoveda = getNovedadesDespac( self::$cConexion, $mNumDespac, 2 );

		$mSql = "SELECT a.cod_transp, b.cod_rutasx, c.nom_contro, 
						c.ind_virtua 
				   FROM ".BASE_DATOS.".tab_despac_vehige a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_seguim b 
					 ON a.num_despac = b.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_genera_contro c  
					 ON b.cod_contro = c.cod_contro 
				  WHERE a.num_despac = '$mNumDespac' 
					AND b.cod_contro = '$mCodContro' 
				";
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');

		$mResult = $mResult[0];
		$mResult['fec_ultnov'] = $mUltNoveda['fec_crenov'];

		$mTransp = getTransTipser( self::$cConexion, " AND a.cod_transp = $mResult[cod_transp] ", array('cod_tipser') );
		$mResult['cod_tipser'] = $mTransp[0]['cod_tipser'];

		return $mResult;
	}
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new ConfirPernoc();
else
	$_INFORM = new ConfirPernoc( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>