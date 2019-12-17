<?php
/*! \file: class_despac_trans3.php
 *  \brief: Archivo con las consultas de los Despachos en Cargue, Transito o Descargue 
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 16/06/2015
 *  \bug: 
 *  \warning1:  Para realizar el filtro por Despachos limpios o no limpios actualmente no se realiza con el ind_limpio de tab_despac_despac 
 *				ya que no esta siendo actualizado correctamente en InsertNovedad.inc 
 *				Para cuando este bug se corriga en InsertNovedad.inc Descomentariar lineas de las querys que traen los despachos.
 *				Buscar #warning1 para ubicar las lineas afectadas
 *  \warning2:	InsertNovedad.inc no recalcula bien el tiempo de alarma, para disfrazar este bug se opto por validar si la fecha de la
 *				ultima novedad es mayor a la fecha de alarma, si esto es verdadero se recalcula el tiempo de alarma según el tipo de 
 *				novedad; si la novedad solicita tiempo se recalcula con el tiempo dado, si no se recalcula con el tiempo del array de 
 *				clase $cTime.
 *				Buscar #warning2 para ubicar las lineas afectadas
 */

date_default_timezone_get('America/Bogota');

/*! \class: Despac
 *  \brief: Clase que realiza las consultas para retornar la información de los Despachos en Cargue, Transito o Descargue
 */
class Despac
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cHoy,
					$cTypeUser,
					$cControlador,
					$cTipDespac = '""',
					$cTipDespacContro = '""', #Tipo de Despachos asignados al controlador, Aplica para cTypeUser[tip_perfil] == 'CONTROL'
					$cNull = array( array('', '-----') ), 
					$cTime = array( 'ind_desurb' => '30', 'ind_desnac' => '60' ),
					$cSession; #warning2

	function __construct($co = null, $us = null, $ca = null)
	{

		@include_once( "../lib/general/festivos.php" );
		if($_REQUEST[Ajax] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion = $AjaxConnection;
		}else{
			self::$cConexion = $co;
			self::$cUsuario = $us;
			self::$cCodAplica = $ca;
		}
		self::$cSession = $_SESSION["datos_usuario"];
		self::$cHoy = date("Y-m-d H:i:s");
		self::$cTypeUser = self::typeUser();

		if( self::$cTypeUser[tip_perfil] == 'CONTROL' )
		{
			self::$cControlador = self::getInfoControlador( $_SESSION[datos_usuario][cod_usuari] );

			self::$cTipDespacContro .= self::$cControlador[ind_desurb] == 1 ? ',"1"' : '';
			self::$cTipDespacContro .= self::$cControlador[ind_desnac] == 1 ? ',"2"' : '';
			self::$cTipDespacContro .= self::$cControlador[ind_desimp] == 1 ? ',"3"' : '';
			self::$cTipDespacContro .= self::$cControlador[ind_desexp] == 1 ? ',"4"' : '';
			self::$cTipDespacContro .= self::$cControlador[ind_desxd1] == 1 ? ',"5"' : '';
			self::$cTipDespacContro .= self::$cControlador[ind_desxd2] == 1 ? ',"6"' : '';
			self::$cTipDespacContro = self::$cTipDespacContro == '"","1","2","3","4","5","6"' ? '""' : self::$cTipDespacContro; #Si tiene asignado todos los tipos de despacho no es necesario filtrar por tipo de despacho
		}

		$mTipDespac = self::getTipoDespac();
		foreach ($mTipDespac as $row){
			self::$cTipDespac .= $_REQUEST["tip_despac".$row[0]] == '1' ? ',"'.$row[0].'"' : '';
		}

		if($_REQUEST['Ajax'] === 'on' )
		{
			switch($_REQUEST['Option'])
			{
				case "infoGeneral":
					self::infoGeneral();
					break;
	
				case "infoPreCargue":
					self::infoPreCargue();
					break;

				case "infoCargue":
					self::infoCargue();
					break;

				case 'infoTransito':
					self::infoTransito();
					break;

				case 'infoControl':
					self::infoControl();
					break;

				case 'infoDescargue':
					self::infoDescargue();
					break;

				case "detailBand":
					self::detailBand();
					break;

				case "detailSearch":
					self::detailSearch();
					break;

				case "infoPernoctacion":
					self::infoPernoctacion();
					break;

				case "getLisCiudadOrigne":
					self::getLisCiudadOrigne();
					break;

				case "getFormValidaProNove":
					self::getFormValidaProNove();
					break;

				case "getNovedadAutocomple":
					self::getNovedadAutocomple();
					break;
					
				case "formNovedadGestion":
					self::formNovedadGestion();
					break;

				case 'getConteoNem':
					self::getConteoNem($_REQUEST['etapa'], $_REQUEST['transp']);
					break;

				case 'AlmcenarSolucionNem':
					self::AlmcenarSolucionNem();
					break;

				case 'getListDespac':
					self::getListDespac();
					break;

				default:
					header('Location: index.php?window=central&cod_servic=1366&menant=1366');
					break;
			}
		}
	}

	/*! \fn: infoGeneral
	 *  \brief: Informe General
	 *  \author: Ing. Fabian Salinas
	 *	\date: 15/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function infoGeneral()
	{
		$mContent = $_REQUEST['ind_filact'] == '1' || self::$cTypeUser['tip_perfil'] == 'CLIENTE' ? self::printInformGeneral() : 'Por Favor Seleccione los Parametros de Busqueda Para Generar el Informe.';
		$mHtml  = '<div id=table1ID>';
		$mHtml .= $mContent;
		$mHtml .= '</div>';

		echo $mHtml;
	}

	/*! \fn: infoPreCargue
	 *  \brief: Informe Etapa Cargue
	 *  \author: Edward Serrano
	 *	\date: 07/03/2017
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function infoPreCargue()
	{
		$mIndEtapa = 'ind_segprc';
		$mTittle['texto'] = array('NO.', 'TIPO SERVICIO', 'EMPRESA', 'NO. DESPACHOS', 'PROGRAMACION', 'REGISTRO', 'ESTADOS', 'PENDIENTES', 'PARA EL CORTE', 'ANULADOS', 'EN PLANTA', 'PORTERIA', 'SIN COMUNICACION', 'TRANSITO A PLANTA', 'CON NOVEDAD NO LLEGADA A PLANTA', 'CON NOVEDAD LLEGADA A PLANTA', 'A CARGO EMPRESA');
		$mTittle['style'] = array('', '', '', '', 'bgPC1', 'bgPC2', 'bgPC1', '', '', '', '');
		$mStyleCel = array(
							"COL" => array('1', '1', '1', '1', '2', '2', '6', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1'),
					 		"ROW" => array('2', '2', '2', '2', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1'),
					 		"BR"  => array('0', '0', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0')
					 	  );
		$mHtml .= '<div id=table2ID>';
		$mHtml .= self::printInformPrc( $mIndEtapa, $mTittle, $mStyleCel );
		$mHtml .= '</div>';

		echo $mHtml;
	}

	/*! \fn: infoCargue
	 *  \brief: Informe Etapa Cargue
	 *  \author: Ing. Fabian Salinas
	 *	\date: 18/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function infoCargue()
	{
		$mIndEtapa = 'ind_segcar';
		$mTittle['texto'] = array('NO.', 'TIPO SERVICIO', 'EMPRESA', 'NO. DESPACHOS', 'SIN RETRASO', 'AVISO CONTROL CARGUE (0-30 MIN)', 'ALERTA CARGUE (31-60 MIN)', 'SIN CARGUE (61-90 MIN)', 'NOVEDAD EN CARGUE (91 MIN)', 'ESTADO PERNOCTACION', 'A CARGO EMPRESA', 'USUARIO ASIGNADO' );
		$mTittle['style'] = array('', '', '', '', '', 'bgC1', 'bgC2', 'bgC3', 'bgC4', '', '');

		$mHtml  = '<div id=table2ID>';
		$mHtml .= self::printInform( $mIndEtapa, $mTittle );
		$mHtml .= '</div>';

		echo $mHtml;
	}

	/*! \fn: infoTransito
	 *  \brief: Informe Etapa Transito
	 *  \author: Ing. Fabian Salinas
	 *	\date: 26/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function infoTransito()
	{
		$mIndEtapa = 'ind_segtra';
		$mTittle['texto'] = array('NO.', 'TIPO SERVICIO', 'HORARIO DE SEGUIMIENTO', 'EMPRESA', 'NO. DESPACHOS', 'SIN RETRASO', 'CON ALARMA', 'AMARILLO (SEGUIMIENTO) (0-30 MIN)', 'ALARMA NARANJA (31-60 MIN)', 'ALARMA ROJA (61-90 MIN)', 'ALARMA VIOLETA (91 MIN) hasta solución', 'ESTADO PERNOCTACION', 'POR LLEGADA', 'A CARGO EMPRESA', 'USUARIO ASIGNADO' );
		$mTittle['style'] = array('', '', '', '', '', '', '', 'bgT1', 'bgT2', 'bgT3', 'bgT4', '', '');

		$mHtml  = '<div id=table3ID>';
		$mHtml .= self::printInform( $mIndEtapa, $mTittle );
		$mHtml .= '</div>';

		echo $mHtml;
	}

	/*! \fn: infoControl
	 *  \brief: Informe Etapa Operación Total
	 *  \author: Ing. Fabian Salinas
	 *	\date: 26/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function infoControl()
	{
		$mIndEtapa = 'ind_segctr';
		$mTittle['texto'] = array('NO.', 'TIPO SERVICIO', 'HORARIO DE SEGUIMIENTO', 'EMPRESA', 'EN SEGUIMIENTO', 'SIN RETRASO', 'CON ALARMA', 'AMARILLO (SEGUIMIENTO) (0-30 MIN)', 'ALARMA NARANJA (31-60 MIN)', 'ALARMA ROJA (61-90 MIN)', 'ALARMA VIOLETA (91 MIN) hasta solución', 'ESTADO PERNOCTACION', 'POR LLEGADA', 'A CARGO EMPRESA', 'USUARIO ASIGNADO' );
		$mTittle['style'] = array('', '', '', '', '', '', '', 'bgT1', 'bgT2', 'bgT3', 'bgT4', '', '');

		$mHtml  = '<div id="table3ID">';
		$mHtml .= self::printInformContr( $mIndEtapa, $mTittle );
		$mHtml .= '</div>';

		echo $mHtml;
	}

	/*! \fn: infoDescargue
	 *  \brief: Informe Etapa Transito
	 *  \author: Ing. Fabian Salinas
	 *	\date: 26/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function infoDescargue()
	{
		$mIndEtapa = 'ind_segdes';
		$mTittle['texto'] = array('NO.', 'TIPO SERVICIO', 'EMPRESA', 'NO. DESPACHOS', 'SIN RETRASO', 'PROXIMO A DESCARGUE (0-30 MIN)', 'EN DESCARGUE (31-60 MIN)', 'SIN DESCARGUE (61-90 MIN)', 'NOVEDAD EN DESCARGUE (91 MIN)', 'ESTADO PERNOCTACION', 'POR LLEGADA', 'A CARGO EMPRESA', 'USUARIO ASIGNADO' );
		$mTittle['style'] = array('', '', '', '', '', 'bgD1', 'bgD2', 'bgD3', 'bgD4', '', '');

		$mHtml  = '<div id=table4ID>';
		$mHtml .= self::printInform( $mIndEtapa, $mTittle );
		$mHtml .= '</div>';

		echo $mHtml;
	}

	/*! \fn: infoPernoctacion
	 *  \brief: Informe C. Pernoctacion
	 *  \author: Ing. Fabian Salinas
	 *  \date: 03/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function infoPernoctacion(){
		$mTittle['texto'] = array('NO.', 'TIPO SERVICIO', 'EMPRESA', 'NO. DESPACHOS', 'USUARIO ASIGNADO' );
		$mTittle['style'] = array('', '', '', '', '');

		$mHtml  = '<div id=table5ID>';
		$mHtml .= self::printInformPernoc( $mTittle );
		$mHtml .= '</div>';

		echo $mHtml;
	}

	/*! \fn: printInform
	 *  \brief: Imprime la table segun etapa
	 *  \author: Ing. Fabian Salinas
	 *	\date: 07/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: mIndEtapa  String  Etapa 
	 *  \param: mTittle  Matriz  Titulos y Colores
	 *  \return:
	 */
	private function printInform( $mIndEtapa, $mTittle )
	{
		$mTransp = self::getTranspServic( $mIndEtapa );
		echo "<pre style='display:none'>"; print_r($mTransp ); echo "</pre>";
		$mLimitFor = self::$cTypeUser[tip_perfil] == 'OTRO' ? sizeof($mTittle[texto]) : sizeof($mTittle[texto])-1;
		$mHtml = '';
		$j=1;
		$mCodTransp = "";

		/*echo "<pre>";
		print_r($mTransp);
		echo "<pre>";*/

		#Dibuja las Filas por Transportadora
		for($i=0; $i<sizeof($mTransp); $i++)
		{
			#Trae los Despachos Segun Etapa
			switch ($mIndEtapa){
				case 'ind_segprc':
					$mDespac = self::getDespacPrcCargue( $mTransp[$i] );
					break;
				case 'ind_segcar':
					$mDespac = self::getDespacCargue( $mTransp[$i] );
					break;
				case 'ind_segtra':
					if( $mTransp[$i][ind_segcar] == '0' && $mTransp[$i][ind_segdes] == '0' )
						$mDespac = self::getDespacTransi1( $mTransp[$i] );
					else
						$mDespac = self::getDespacTransi2( $mTransp[$i] );
					break;
				case 'ind_segdes':
					$mDespac = self::getDespacDescar( $mTransp[$i] );
					break;
			}

			//Capturar las transportadoras
			$mCodTransp .= $mCodTransp != '' ? ','.$mTransp[$i][cod_transp] : $mTransp[$i][cod_transp];

			#Si la Transportadora tiene Despachos
			if( $mDespac != false )
			{	
				$mTransp[$i][hor_seguim] = self::setHorSeguim($mTransp[$i][cod_transp]);
				$mData = self::calTimeAlarma( $mDespac, $mTransp[$i], 1 );
				
				$mHtml .= '<tr onclick="this.style.background=this.style.background==\'#CEF6CE\'?\'#eeeeee\':\'#CEF6CE\';">';
				$mHtml .= 	'<th class="classCell" nowrap="" align="left">'.$j.'</th>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mTransp[$i][nom_tipser].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mTransp[$i][hor_seguim].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mTransp[$i][nom_transp].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer" >'.sizeof($mDespac).'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[sin_retras] == 0 ? '' : 'onclick="showDetailBand(\'sin_retras\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[sin_retras].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_alarma] == 0 ? '' : 'onclick="showDetailBand(\'con_alarma\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_alarma].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_00A30x] == 0 ? '' : 'onclick="showDetailBand(\'con_00A30x\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_00A30x].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_31A60x] == 0 ? '' : 'onclick="showDetailBand(\'con_31A60x\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_31A60x].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_61A90x] == 0 ? '' : 'onclick="showDetailBand(\'con_61A90x\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_61A90x].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_91Amas] == 0 ? '' : 'onclick="showDetailBand(\'con_91Amas\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_91Amas].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[est_pernoc] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[est_pernoc].'</td>';

				if( $mIndEtapa != 'ind_segcar' )
					$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[fin_rutaxx] == 0 ? '' : 'onclick="showDetailBand(\'fin_rutaxx\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[fin_rutaxx].'</td>';

				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[ind_acargo] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[ind_acargo].'</td>';

				if( self::$cTypeUser[tip_perfil] == 'OTRO' )
					$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.( $mTransp[$i][usr_asigna] != '' ? $mTransp[$i][usr_asigna] : 'SIN ASIGNAR' ).'</td>';

				$mHtml .= '</tr>';

				$mTotal[0] += sizeof($mDespac);
				$mTotal[1] += $mData[sin_retras];
				$mTotal[2] += $mData[con_alarma];
				$mTotal[3] += $mData[con_00A30x];
				$mTotal[4] += $mData[con_31A60x];
				$mTotal[5] += $mData[con_61A90x];
				$mTotal[6] += $mData[con_91Amas];
				$mTotal[7] += $mData[est_pernoc];
				$mTotal[9] += $mData[ind_acargo];

				if( $mIndEtapa != 'ind_segcar' )
					$mTotal[8] += $mData[fin_rutaxx];

				$j++;
			}
		}

		#Dibuja la Fila de los Totales
		$mHtml1  = '<tr>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="right" colspan="4">TOTALES:</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \''.$mIndEtapa.'\', \''.$mCodTransp.'\');" style="cursor: pointer;" >'.$mTotal[0].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[1] == 0 ? '' : 'onclick="showDetailBand(\'sin_retras\', \''.$mIndEtapa.'\', \''.$mCodTransp.'\');" style="cursor: pointer;"' ) .' >'.$mTotal[1].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[2] == 0 ? '' : 'onclick="showDetailBand(\'con_alarma\', \''.$mIndEtapa.'\', \''.$mCodTransp.'\');" style="cursor: pointer;"' ) .' >'.$mTotal[2].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[3] == 0 ? '' : 'onclick="showDetailBand(\'con_00A30x\', \''.$mIndEtapa.'\', \''.$mCodTransp.'\');" style="cursor: pointer;"' ) .' >'.$mTotal[3].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[4] == 0 ? '' : 'onclick="showDetailBand(\'con_31A60x\', \''.$mIndEtapa.'\', \''.$mCodTransp.'\');" style="cursor: pointer;"' ) .' >'.$mTotal[4].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[5] == 0 ? '' : 'onclick="showDetailBand(\'con_61A90x\', \''.$mIndEtapa.'\', \''.$mCodTransp.'\');" style="cursor: pointer;"' ) .' >'.$mTotal[5].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[6] == 0 ? '' : 'onclick="showDetailBand(\'con_91Amas\', \''.$mIndEtapa.'\', \''.$mCodTransp.'\');" style="cursor: pointer;"' ) .' >'.$mTotal[6].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[7] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \''.$mCodTransp.'\');" style="cursor: pointer;"' ) .' >'.$mTotal[7].'</th>';

		if( $mIndEtapa != 'ind_segcar' )
			$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[8] == 0 ? '' : 'onclick="showDetailBand(\'fin_rutaxx\', \''.$mIndEtapa.'\', \''.$mCodTransp.'\');" style="cursor: pointer;"' ) .' >'.$mTotal[8].'</th>';

		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[9] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \''.$mCodTransp.'\');" style="cursor: pointer;"' ) .' >'.$mTotal[9].'</th>';

		if( self::$cTypeUser[tip_perfil] == 'OTRO' )
			$mHtml1 .= '<th class="classTotal" nowrap="" align="center">&nbsp;</th>';

		$mHtml1 .= '</tr>';
		
		#Dibuja la Tabla Completa
		$mHtml2  = '<table class="classTable" width="100%" cellspacing="0" cellpadding="0" align="center">';
		$mHtml2 .= 	'<tr>';
		for ($i=0; $i < $mLimitFor; $i++){
			$mHtml2 .= '<th class="classHead bt '.$mTittle[style][$i].'" align="center">'.$mTittle[texto][$i].'</th>';
		}
		$mHtml2 .= 	'</tr>';

		$mHtml2 .= $mHtml1;
		$mHtml2 .= $mHtml;
		$mHtml2 .= $mHtml1;

		$mHtml2 .= '</table>';

		return utf8_decode($mHtml2);
	}

	/*! \fn: printInformContr
	 *  \brief: Imprime la table etapa de control
	 *  \author: Ing. Luis Manrique
	 *	\date: 09/12/2019
	 *	\date modified: dia/mes/año
	 *  \param: mIndEtapa  String  Etapa 
	 *  \param: mTittle  Matriz  Titulos y Colores
	 *  \return:
	 */
	private function printInformContr( $mIndEtapa, $mTittle )
	{
		$mTransp = self::getTranspServic( $mIndEtapa );
		echo "<pre style='display:none'>"; print_r($mTransp ); echo "</pre>";
		$mLimitFor = self::$cTypeUser[tip_perfil] == 'OTRO' ? sizeof($mTittle[texto]) : sizeof($mTittle[texto])-1;
		$mHtml = '';
		$j=1;

		/*echo "<pre>";
		print_r($mTransp);
		echo "<pre>";*/

		//Variables necesarias
		$mUsrAsignaAnt = "";
		$mCodTransp = "";
		$mCodTranspUS = "";

		#Dibuja las Filas por Transportadora
		for($i=0; $i<sizeof($mTransp); $i++)
		{

			#Trae los Despachos Segun Etapa
			$mDespac = self::getDespacControl( $mTransp[$i] );
			$mUsrAsigna = $mTransp[$i][usr_asigna] != '' ? $mTransp[$i][usr_asigna] : 'SIN ASIGNAR';
			$mUsrAsignaAnt = $mUsrAsignaAnt != '' ? $mUsrAsignaAnt : $mUsrAsigna ;
			$mCodTransp .= $mCodTransp != '' ? ','.$mTransp[$i][cod_transp] : $mTransp[$i][cod_transp];

			#Si la Transportadora tiene Despachos
			if( $mDespac != false )
			{	
				$mTransp[$i][hor_seguim] = self::setHorSeguim($mTransp[$i][cod_transp]);
				$mData = self::calTimeAlarma( $mDespac, $mTransp[$i], 1 );
				//Reemplaza valores para reutilizarce como clase y generar evento acordeon
				$mClassEv = str_replace(",", "_",str_replace(".", "_", str_replace(" ", "_", $mUsrAsignaAnt)));


					//Si el usuario es diferente genera la columna de total por usuario
					if($mUsrAsignaAnt != $mUsrAsigna){

						$mHtml .= '<tr>';
						$mHtml .= '<th class="classTotal ui-state-default" colspan="2"style="cursor: pointer; font-weight: bold" align="left" onclick="acordion(\''.$mClassEv.'\')">* '.$mUsrAsignaAnt.'</th>';
						$mHtml .= '<th class="classTotal ui-state-default" colspan="2"style="cursor: pointer; font-weight: bold" align="right" onclick="acordion(\''.$mClassEv.'\')">TOTALES:</th>';
						$mHtml .= '<th class="classTotal" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;" >'.$mTotalUS[0].'</th>';
						$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[1] == 0 ? '' : 'onclick="showDetailBand(\'sin_retras\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[1].'</th>';
						$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[2] == 0 ? '' : 'onclick="showDetailBand(\'con_alarma\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[2].'</th>';
						$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[3] == 0 ? '' : 'onclick="showDetailBand(\'con_00A30x\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[3].'</th>';
						$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[4] == 0 ? '' : 'onclick="showDetailBand(\'con_31A60x\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[4].'</th>';
						$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[5] == 0 ? '' : 'onclick="showDetailBand(\'con_61A90x\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[5].'</th>';
						$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[6] == 0 ? '' : 'onclick="showDetailBand(\'con_91Amas\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[6].'</th>';
						$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[7] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[7].'</th>';

						if( $mIndEtapa != 'ind_segcar' )
							$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[8] == 0 ? '' : 'onclick="showDetailBand(\'fin_rutaxx\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[8].'</th>';

						$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[9] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[9].'</th>';

						if( self::$cTypeUser[tip_perfil] == 'OTRO' )
							$mHtml .= '<th class="classTotal" nowrap="" align="center">&nbsp;</th>';
						$mHtml .= '</tr>';

						//Reinicia las variables para el contreo por el usuario
						$mTotalUS[0] = 0;
						$mTotalUS[1] = 0;
						$mTotalUS[2] = 0;
						$mTotalUS[3] = 0;
						$mTotalUS[4] = 0;
						$mTotalUS[5] = 0;
						$mTotalUS[6] = 0;
						$mTotalUS[7] = 0;
						$mTotalUS[9] = 0;
						$mTotalUS[8] = 0;

						$mCodTranspUS = "";

						$j=1;
					}


					//Reemplaza valores para reutilizarce como clase y generar evento acordeon
					$mClassEv = str_replace(",", "_",str_replace(".", "_", str_replace(" ", "_", $mUsrAsigna)));

					$mHtml .= '<tr class="'.$mClassEv.'" onclick="this.style.background=this.style.background==\'#CEF6CE\'?\'#eeeeee\':\'#CEF6CE\';"><div>';
					$mHtml .= 	'<th class="classCell" nowrap="" align="left">'.$j.'</th>';
					$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mTransp[$i][nom_tipser].'</td>';
					$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mTransp[$i][hor_seguim].'</td>';
					$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mTransp[$i][nom_transp].'</td>';
					$mHtml .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer" >'.sizeof($mDespac).'</td>';
					$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[sin_retras] == 0 ? '' : 'onclick="showDetailBand(\'sin_retras\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[sin_retras].'</td>';
					$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_alarma] == 0 ? '' : 'onclick="showDetailBand(\'con_alarma\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_alarma].'</td>';
					$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_00A30x] == 0 ? '' : 'onclick="showDetailBand(\'con_00A30x\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_00A30x].'</td>';
					$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_31A60x] == 0 ? '' : 'onclick="showDetailBand(\'con_31A60x\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_31A60x].'</td>';
					$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_61A90x] == 0 ? '' : 'onclick="showDetailBand(\'con_61A90x\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_61A90x].'</td>';
					$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_91Amas] == 0 ? '' : 'onclick="showDetailBand(\'con_91Amas\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_91Amas].'</td>';
					$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[est_pernoc] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[est_pernoc].'</td>';

					if( $mIndEtapa != 'ind_segcar' )
						$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[fin_rutaxx] == 0 ? '' : 'onclick="showDetailBand(\'fin_rutaxx\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[fin_rutaxx].'</td>';

					$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[ind_acargo] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[ind_acargo].'</td>';

					if( self::$cTypeUser[tip_perfil] == 'OTRO' )
						$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.( $mTransp[$i][usr_asigna] != '' ? $mTransp[$i][usr_asigna] : 'SIN ASIGNAR' ).'</td>';

					$mHtml .= '</tr>';

					//Totates globales
					$mTotal[0] += sizeof($mDespac);
					$mTotal[1] += $mData[sin_retras];
					$mTotal[2] += $mData[con_alarma];
					$mTotal[3] += $mData[con_00A30x];
					$mTotal[4] += $mData[con_31A60x];
					$mTotal[5] += $mData[con_61A90x];
					$mTotal[6] += $mData[con_91Amas];
					$mTotal[7] += $mData[est_pernoc];
					$mTotal[9] += $mData[ind_acargo];

					//Totales por usuario
					$mTotalUS[0] += sizeof($mDespac);
					$mTotalUS[1] += $mData[sin_retras];
					$mTotalUS[2] += $mData[con_alarma];
					$mTotalUS[3] += $mData[con_00A30x];
					$mTotalUS[4] += $mData[con_31A60x];
					$mTotalUS[5] += $mData[con_61A90x];
					$mTotalUS[6] += $mData[con_91Amas];
					$mTotalUS[7] += $mData[est_pernoc];
					$mTotalUS[9] += $mData[ind_acargo];

					if( $mIndEtapa != 'ind_segcar' ){
						$mTotal[8] += $mData[fin_rutaxx];
						$mTotalUS[8] += $mData[fin_rutaxx];
					}

					//Iguala las variables para la siguente comparación.
					$mUsrAsignaAnt = $mUsrAsigna;

					//Captura los cod de transportadora para los subtotales
					$mCodTranspUS .= $mCodTranspUS != '' ? ','.$mTransp[$i][cod_transp] : $mTransp[$i][cod_transp];


				$j++;
			}
		}


		//Genera Ultima fila de subtotales
		$mHtml .= '<tr>';
		$mHtml .= '<th class="classTotal ui-state-default" colspan="2"style="cursor: pointer; font-weight: bold" align="left" onclick="acordion(\''.$mClassEv.'\')">* '.$mUsrAsigna.'</th>';
		$mHtml .= '<th class="classTotal ui-state-default" colspan="2"style="cursor: pointer; font-weight: bold" align="right" onclick="acordion(\''.$mClassEv.'\')">TOTALES:</th>';
		$mHtml .= '<th class="classTotal" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;" >'.$mTotalUS[0].'</th>';
		$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[1] == 0 ? '' : 'onclick="showDetailBand(\'sin_retras\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[1].'</th>';
		$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[2] == 0 ? '' : 'onclick="showDetailBand(\'con_alarma\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[2].'</th>';
		$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[3] == 0 ? '' : 'onclick="showDetailBand(\'con_00A30x\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[3].'</th>';
		$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[4] == 0 ? '' : 'onclick="showDetailBand(\'con_31A60x\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[4].'</th>';
		$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[5] == 0 ? '' : 'onclick="showDetailBand(\'con_61A90x\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[5].'</th>';
		$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[6] == 0 ? '' : 'onclick="showDetailBand(\'con_91Amas\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[6].'</th>';
		$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[7] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[7].'</th>';

		if( $mIndEtapa != 'ind_segcar' )
			$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[8] == 0 ? '' : 'onclick="showDetailBand(\'fin_rutaxx\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[8].'</th>';

		$mHtml .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalUS[9] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer;"' ) .' >'.$mTotalUS[9].'</th>';

		if( self::$cTypeUser[tip_perfil] == 'OTRO' )
			$mHtml .= '<th class="classTotal" nowrap="" align="center">&nbsp;</th>';
		$mHtml .= '</tr>';

		#Dibuja la Fila de los Totales
		$mHtml1  = '<tr>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="right" colspan="4">TOTALES:</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;" >'.$mTotal[0].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[1] == 0 ? '' : 'onclick="showDetailBand(\'sin_retras\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotal[1].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[2] == 0 ? '' : 'onclick="showDetailBand(\'con_alarma\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotal[2].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[3] == 0 ? '' : 'onclick="showDetailBand(\'con_00A30x\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotal[3].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[4] == 0 ? '' : 'onclick="showDetailBand(\'con_31A60x\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotal[4].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[5] == 0 ? '' : 'onclick="showDetailBand(\'con_61A90x\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotal[5].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[6] == 0 ? '' : 'onclick="showDetailBand(\'con_91Amas\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotal[6].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[7] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotal[7].'</th>';

		if( $mIndEtapa != 'ind_segcar' )
			$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[8] == 0 ? '' : 'onclick="showDetailBand(\'fin_rutaxx\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotal[8].'</th>';

		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[9] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotal[9].'</th>';

		if( self::$cTypeUser[tip_perfil] == 'OTRO' )
			$mHtml1 .= '<th class="classTotal" nowrap="" align="center">&nbsp;</th>';

		$mHtml1 .= '</tr>';
		
		#Dibuja la Tabla Completa
		$mHtml2  = '<table class="classTable" width="100%" cellspacing="0" cellpadding="0" align="center">';
		$mHtml2 .= 	'<tr>';
		for ($i=0; $i < $mLimitFor; $i++){
			$mHtml2 .= '<th class="classHead bt '.$mTittle[style][$i].'" align="center">'.$mTittle[texto][$i].'</th>';
		}
		$mHtml2 .= 	'</tr>';

		$mHtml2 .= $mHtml1;
		$mHtml2 .= $mHtml;
		$mHtml2 .= $mHtml1;

		$mHtml2 .= '</table>';

		return utf8_decode($mHtml2);
	}

	/*! \fn: getTipoDespac
	 *  \brief: Trae los Tipos de Despacho
	 *  \author: Ing. Fabian Salinas
	 *	\date: 16/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: 
	 */
	public function getTipoDespac()
	{
		$mSql = " SELECT cod_tipdes, nom_tipdes 
					FROM ".BASE_DATOS.".tab_genera_tipdes 
				ORDER BY cod_tipdes ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('i');
	}

	/*! \fn: getTransp
	 *  \brief: Trae las transportadoras
	 *  \author: Ing. Fabian Salinas
	 *	\date: 17/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function getTransp()
	{
		$mSql = "SELECT a.cod_tercer, UPPER(a.abr_tercer) AS nom_tercer 
				   FROM ".BASE_DATOS.".tab_tercer_tercer a 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_activi b 
					 ON a.cod_tercer = b.cod_tercer 
				  WHERE b.cod_activi = ".COD_FILTRO_EMPTRA." 
					AND a.cod_estado = ".COD_ESTADO_ACTIVO."
					";
		if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) 
		{#PARA EL FILTRO DE EMPRESA
			$filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_usuari] );
			if ( $filtro -> listar( self::$cConexion ) ) : 
				$datos_filtro = $filtro -> retornar();
				$mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
			endif;
		}else{#PARA EL FILTRO DE EMPRESA
			$filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_perfil] );
			if ( $filtro -> listar( self::$cConexion ) ) : 
				$datos_filtro = $filtro -> retornar();
				$mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
			endif;
		}
		$mSql .= " ORDER BY a.abr_tercer ASC ";
		$consulta = new Consulta( $mSql, self::$cConexion );
		return $mResult = $consulta -> ret_matrix('i');
	}

	/*! \fn: getUserAsig
	 *  \brief: Trae los usuarios asignados a turno
	 *  \author: Ing. Fabian Salinas
	 *	\date: 16/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: 
	 */
	public function getUserAsig()
	{
		$mSql = " SELECT a.cod_usuari, a.cod_usuari AS usuario 
					FROM ".BASE_DATOS.".tab_monito_encabe a 
			  INNER JOIN ".BASE_DATOS.".tab_monito_detall b 
			  		  ON a.cod_consec = b.cod_consec 
			  INNER JOIN ".BASE_DATOS.".tab_genera_usuari c 
					  ON a.cod_usuari = c.cod_usuari 
				   WHERE a.ind_estado = '1' 
					 AND b.ind_estado = '1'  
					 AND a.fec_inicia <= NOW() 
					 AND a.fec_finalx >= NOW() 
					 AND c.cod_perfil NOT IN ( ".CONS_PERFIL." ) 
				GROUP BY 1 
				ORDER BY 1 ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('i');
	}

	/*! \fn: getInfoControlador
	 *  \brief: Trae la informacion parametrizada para el turno del controlador
	 *  \author: Ing. Fabian Salinas
	 *	\date: 13/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: mCodUsuari  String  Codigo del usuario
	 *  \return: Array 
	 */
	public function getInfoControlador( $mCodUsuari )
	{
		$mSql = " SELECT a.ind_cargue, a.ind_transi, a.ind_descar, 
						 a.ind_desurb, a.ind_desnac, a.ind_desimp, 
						 a.ind_desexp, a.ind_desxd1, a.ind_desxd2, 
						 a.ind_limpio 
					FROM ".BASE_DATOS.".tab_monito_encabe a 
			  INNER JOIN ".BASE_DATOS.".tab_monito_detall b 
			  		  ON a.cod_consec = b.cod_consec 
			  INNER JOIN ".BASE_DATOS.".tab_genera_usuari c 
					  ON a.cod_usuari = c.cod_usuari 
				   WHERE a.cod_usuari = '{$mCodUsuari}' 
					 AND a.ind_estado = '1' 
					 AND b.ind_estado = '1'  
					 AND a.fec_inicia <= NOW() 
					 AND a.fec_finalx >= NOW() 
					 AND c.cod_perfil NOT IN ( ".CONS_PERFIL." ) 
				GROUP BY 1 
				ORDER BY 1 ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');

		return $mResult[0];
	}

	/*! \fn: typeUser
	 *  \brief: Retorna el tipo de perfil del usuario
	 *  \author: Ing. Fabian Salinas
	 *	\date: 09/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: array
	 */
	public function typeUser()
	{
		$mPerfil = $_SESSION[datos_usuario][cod_perfil];
		$mResult = array();

		if( $mPerfil == '7' || $mPerfil == '713' )
			$mResult[tip_perfil] = 'CONTROL'; #Perfil Controlador
		elseif( $mPerfil == '70' || $mPerfil == '80' || $mPerfil == '669' )
			$mResult[tip_perfil] = 'EAL'; #Perfil EAL
		else{
			$mTransp = self::getTransp();
			if( sizeof($mTransp) == '1' ){
				$mResult[tip_perfil] = 'CLIENTE'; #Perfil Cliente
				$mResult[cod_transp] = $mTransp[0][0];
			}else
				$mResult[tip_perfil] = 'OTRO'; 
		}

		return $mResult;
	}

	/*! \fn: getDespacPrcCargue
	 *  \brief: Trae los despachos en Etapa Cargue
	 *  \author: Edward Serrano
	 *	\date: 07/03/2017
	 *	\date modified: dia/mes/año
	 *  \param: $mTransp  Array  Informacion transportadora
	 *  \param: $mTipReturn  String   array = Retorna array con número de los despachos; list = Retorna lista con número de los despachos;
	 *	\param: mSinFiltro  Boolean  true = No filtra por datos que llegas del formulario $_REQUEST
	 *  \return: Matriz, Array o String (Segun parametro mTipReturn)
	 */
	public function getDespacPrcCargue( $mTransp, $mTipReturn = NULL, $mSinFiltro = false )
	{
		$sal_inicio=date('Y-m-d')." 00:00:01";
		$sal_finxxx=date('Y-m-d')." 23:59:59"; 
		$hor_inicio;
		$hor_finxxx;
		if ( $_REQUEST['hor_inicio'] && $_REQUEST['hor_finxxx'] ) 
		{
			$hor_inicio=$_REQUEST['hor_inicio'];
			$hor_finxxx=$_REQUEST['hor_finxxx'];
		}
		else
		{
			$hor_inicio="00:00:00";
			$hor_finxxx="23:59:59";
		}

		$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat 
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad in ('R', 'A')
						AND yy.ind_activo = 'S' 
						AND yy.cod_transp = '".$mTransp[cod_transp]."' "; 
		
		$mSql .= ($_REQUEST['cod_client'] != NULL || $_REQUEST['cod_client'] != ''?" AND xx.cod_client IN (".str_replace(array('"",','"'),array('',''),$_REQUEST['cod_client']).") ":"");
		
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		if( sizeof($mDespac) < 1 )
			return false;

		$mDespac = join( ',', GetColumnFromMatrix( $mDespac, 'num_despac' ) ); #Despachos en ruta Sin hora salida del sistema

		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax, 
						UPPER(h.abr_tercer) AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin,
						l.cod_estado, a.ind_anulad, z.fec_plalle, a.fec_citcar, a.hor_citcar, k.num_solici, UPPER(o.abr_tercer) AS nom_genera
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					 AND a.num_despac IN ( {$mDespac} ) 
					 AND a.num_despac NOT IN (  
													SELECT da.num_despac 
													  FROM ".BASE_DATOS.".tab_despac_noveda da 
												INNER JOIN ".BASE_DATOS.".tab_genera_noveda db 
														ON da.cod_noveda = db.cod_noveda 
													 WHERE da.num_despac IN ( {$mDespac} ) 
													   AND db.cod_etapax  IN ( 3,4,5 )
											)
					AND a.num_despac NOT IN (  
													SELECT ea.num_despac 
													  FROM ".BASE_DATOS.".tab_despac_contro ea 
												INNER JOIN ".BASE_DATOS.".tab_genera_noveda eb 
														ON ea.cod_noveda = eb.cod_noveda 
													 WHERE ea.num_despac IN ( {$mDespac} ) 
													   AND eb.cod_etapax  IN (  3,4,5 )
											) 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
					 ON b.cod_transp = c.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
					AND a.cod_depori = d.cod_depart 
					AND a.cod_paiori = d.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
					 ON a.cod_ciudes = e.cod_ciudad 
					AND a.cod_depdes = e.cod_depart 
					AND a.cod_paides = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
					 ON a.cod_depori = f.cod_depart 
					AND a.cod_paiori = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
					 ON a.cod_depdes = g.cod_depart 
					AND a.cod_paides = g.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
					 ON b.cod_conduc = h.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
					 ON a.cod_tipdes = i.cod_tipdes
			 INNER JOIN ".BASE_DATOS.".tab_despac_sisext k
			 		 ON a.num_despac = k.num_despac
			 LEFT JOIN ".BASE_DATOS.".tab_despac_corona z 
					 ON a.num_despac = z.num_dessat 
			  LEFT JOIN ( SELECT m.num_despac,n.num_consec,m.cod_estado
                            FROM ".BASE_DATOS.".tab_despac_estado m
                                INNER JOIN ( SELECT n.num_despac, MAX(n.num_consec) num_consec FROM tab_despac_estado n GROUP BY n.num_despac  ) n ON m.num_despac = n.num_despac
                                AND n.num_consec = m.num_consec
                                GROUP BY m.num_despac
                        ) l
                     ON a.num_despac = l.num_despac
              LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer o 
					 ON a.cod_client = o.cod_tercer  
				  WHERE k.ind_cumcar IS NULL AND k.fec_cumcar IS NULL AND
				  		a.fec_inicar IS NULL AND
				  		a.fec_fincar IS NULL AND				  		 
				  		a.fec_citcar >= DATE_SUB( '".$sal_inicio."', INTERVAl 5 DAY ) AND a.fec_citcar <= '{$sal_finxxx}' 


				  		";

		if( $mSinFiltro == false )
		{
			#Filtros por Formulario
			#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
			$mSql .= self::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". self::$cTipDespac .") " : "";

			#Filtros por usuario
			$mSql .= self::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. self::$cTipDespacContro .') ' : '';	
		}
		
		if($_REQUEST['pun_cargue'])
		{
			$mSql .=" AND a.cod_ciuori IN ( ". $_REQUEST['pun_cargue'] .") /*dd*/ ";
		} 

		if($_REQUEST['tip_produc'])
		{
			$mSql .=" AND z.cod_mercan IN (". $_REQUEST['tip_produc'] .") ";
		}
		//$mSql .=" GROUP BY a.num_despac";

 		 
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = self::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = self::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

			#Verifica que el siguiente PC sea el Primero o Segundo ( Parametro Despachos Etapa Cargue )
			if( $mData[sig_pcontr][cod_contro] == $mData[pla_rutaxx][0][cod_contro] || $mData[sig_pcontr][cod_contro] == $mData[pla_rutaxx][1][cod_contro] )
			{
				#warning1
				if( ($_REQUEST[ind_limpio] === '1' || $_REQUEST[ind_limpio] === '0') && $mSinFiltro == false )
				{
					if(		( $_REQUEST[ind_limpio] === '1' && ($mData[ind_limpio] === '1' || $mData[ind_limpio] == '') ) #Despachos Limpios
						||	( $_REQUEST[ind_limpio] === '0' && $mData[ind_limpio] === '0' ) #Despachos no Limpios 
					)
					{
						$mResult[$j] = $mDespac[$i];
						$mResult[$j][can_noveda] = $mData[can_noveda];
						$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
						$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
						$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
						$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
						$mResult[$j][fec_planea] = ($mData[fec_planea] == "1969-12-31 19:00:00"?$mDespac[$i][fec_salida]:$mData[fec_planea]);
						$j++;
					}
				}
				else
				{
					$mResult[$j] = $mDespac[$i];
					$mResult[$j][can_noveda] = $mData[can_noveda];
					$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
					$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
					$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
					$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
					$mResult[$j][fec_planea] = ($mData[fec_planea] == "1969-12-31 19:00:00"?$mDespac[$i][fec_salida]:$mData[fec_planea]);
					$j++;
				}
			}
		}
		
		if( $mTipReturn == 'array' )
			return GetColumnFromMatrix( $mResult, 'num_despac' );
		elseif( $mTipReturn == 'list' )
			return join(',', GetColumnFromMatrix($mResult, 'num_despac') );	
		else
			return $mResult;
	}

	/*! \fn: getDespacCargue
	 *  \brief: Trae los despachos en Etapa Cargue
	 *  \author: Ing. Fabian Salinas
	 *	\date: 18/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: $mTransp  Array  Informacion transportadora
	 *  \param: $mTipReturn  String   array = Retorna array con número de los despachos; list = Retorna lista con número de los despachos;
	 *	\param: mSinFiltro  Boolean  true = No filtra por datos que llegas del formulario $_REQUEST
	 *  \return: Matriz, Array o String (Segun parametro mTipReturn)
	 */
	public function getDespacCargue( $mTransp, $mTipReturn = NULL, $mSinFiltro = false )
	{
		$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat 
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND ( xx.fec_salida IS NOT NULL   )
						AND yy.cod_transp = '".$mTransp[cod_transp]."' ";

		$mSql .= ($_REQUEST['cod_client'] != NULL || $_REQUEST['cod_client'] != ''?" AND xx.cod_client IN (".str_replace(array('"",','"'),array('',''),$_REQUEST['cod_client']).") ":"");

						echo "<pre style='display:none'>"; print_r($mSql); echo "</pre>";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		if( sizeof($mDespac) < 1 )
			return false;

		$mDespac = join( ',', GetColumnFromMatrix( $mDespac, 'num_despac' ) ); #Despachos en ruta Sin hora salida del sistema

		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax, 
						UPPER(h.abr_tercer) AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					AND a.num_despac IN ( {$mDespac} )
					AND a.num_despac NOT IN ( /* Despachos con novedades etapa Transito y Descargue en Sitio */
													SELECT da.num_despac 
													  FROM ".BASE_DATOS.".tab_despac_noveda da 
												INNER JOIN ".BASE_DATOS.".tab_genera_noveda db 
														ON da.cod_noveda = db.cod_noveda 
													 WHERE da.num_despac IN ( {$mDespac} ) 
													   AND db.cod_etapax NOT IN ( 0, 1, 2 )
											)
					AND a.num_despac NOT IN ( /* Despachos con novedades etapa Transito y Descargue antes de Sitio */
													SELECT ea.num_despac 
													  FROM ".BASE_DATOS.".tab_despac_contro ea 
												INNER JOIN ".BASE_DATOS.".tab_genera_noveda eb 
														ON ea.cod_noveda = eb.cod_noveda 
													 WHERE ea.num_despac IN ( {$mDespac} ) 
													   AND eb.cod_etapax NOT IN ( 0, 1, 2 )
											)
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
					 ON b.cod_transp = c.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
					AND a.cod_depori = d.cod_depart 
					AND a.cod_paiori = d.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
					 ON a.cod_ciudes = e.cod_ciudad 
					AND a.cod_depdes = e.cod_depart 
					AND a.cod_paides = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
					 ON a.cod_depori = f.cod_depart 
					AND a.cod_paiori = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
					 ON a.cod_depdes = g.cod_depart 
					AND a.cod_paides = g.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
					 ON b.cod_conduc = h.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
					 ON a.cod_tipdes = i.cod_tipdes 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_corona z 
					 ON a.num_despac = z.num_dessat 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_sisext y
			  		 ON a.num_despac = y.num_despac
			  LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k 
					 ON a.cod_client = k.cod_tercer
				  WHERE 1=1  AND y.ind_cumcar IS NOT NULL AND y.fec_cumcar IS NOT NULL
				  ";

		if( ($_REQUEST["Option"] == "infoPreCargue" || $_REQUEST["Option"]  == 'detailBand' ) && $_REQUEST["pun_cargue"] != '')
		{
		 
			$mSql .=" AND a.cod_ciuori IN (". $_REQUEST['pun_cargue'] .") /*cargue*/";		 
		}

		if( $mSinFiltro == false )
		{
			#Filtros por Formulario
			#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
			$mSql .= self::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". self::$cTipDespac .") " : "";

			#Filtros por usuario
			$mSql .= self::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. self::$cTipDespacContro .') ' : '';	
		}
		echo "<pre style='display:none'>"; print_r($mSql); echo "</pre>";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = self::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = self::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

			#Verifica que el siguiente PC sea el Primero o Segundo ( Parametro Despachos Etapa Cargue )
			if( $mData[sig_pcontr][cod_contro] == $mData[pla_rutaxx][0][cod_contro] || $mData[sig_pcontr][cod_contro] == $mData[pla_rutaxx][1][cod_contro] )
			{
				#warning1
				if( ($_REQUEST[ind_limpio] === '1' || $_REQUEST[ind_limpio] === '0') && $mSinFiltro == false )
				{
					if(		( $_REQUEST[ind_limpio] === '1' && ($mData[ind_limpio] === '1' || $mData[ind_limpio] == '') ) #Despachos Limpios
						||	( $_REQUEST[ind_limpio] === '0' && $mData[ind_limpio] === '0' ) #Despachos no Limpios 
					)
					{
						$mResult[$j] = $mDespac[$i];
						$mResult[$j][can_noveda] = $mData[can_noveda];
						$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
						$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
						$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
						$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
						$mResult[$j][fec_planea] = $mData[fec_planea];
						$j++;
					}
				}
				else
				{
					$mResult[$j] = $mDespac[$i];
					$mResult[$j][can_noveda] = $mData[can_noveda];
					$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
					$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
					$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
					$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
					$mResult[$j][fec_planea] = $mData[fec_planea];
					$j++;
				}
			}
		}
		
		if( $mTipReturn == 'array' )
			return GetColumnFromMatrix( $mResult, 'num_despac' );
		elseif( $mTipReturn == 'list' )
			return join(',', GetColumnFromMatrix($mResult, 'num_despac') );
		else
			return $mResult;
	}

	/*! \fn: getDespacDescar
	 *  \brief: Trae los despachos en Etapa Descargue
	 *  \author: Ing. Fabian Salinas
	 *	\date: 06/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: $mTransp  Array  Informacion transportadora
	 *  \param: $mTipReturn  String   array = Retorna array con número de los despachos; list = Retorna lista con número de los despachos; list2 = Lista de Despachos Pertenecientes a etapas Cargue y Descargue;
	 *	\param: mSinFiltro  Boolean  true = No filtra por datos que llegas del formulario $_REQUEST
	 *  \return: Matriz, Array o String (Segun parametro mTipReturn)
	 */
	public function getDespacDescar( $mTransp, $mTipReturn = NULL, $mSinFiltro = false )
	{
		$mDespacCargue = self::getDespacCargue( $mTransp, 'list', true );
		 
		$mDespacCargue = ($mDespacCargue == NULL ? '0' : $mDespacCargue );

		#Despachos en ruta que ya finalizaron etapa Cargue
		$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac 
						AND xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND yy.cod_transp = '".$mTransp[cod_transp]."' 
						AND xx.num_despac NOT IN ( {$mDespacCargue} ) ";
		$mSql .= ($_REQUEST['cod_client'] != NULL || $_REQUEST['cod_client'] != ''?" AND xx.cod_client IN (".str_replace(array('"",','"'),array('',''),$_REQUEST['cod_client']).") ":"");

		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		if( sizeof($mDespac) < 1 )
			return false;

		$mDespac = join( ',', GetColumnFromMatrix( $mDespac, 'num_despac' ) );

		#Despachos en Etapa Descargue Filtro 1
		$mSql = "( /* Despachos con novedades etapa Descargue en Sitio */
						SELECT a.num_despac 
						  FROM ".BASE_DATOS.".tab_despac_noveda a 
					INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
							ON a.cod_noveda = b.cod_noveda 
						 WHERE a.num_despac IN ( {$mDespac} ) 
						   AND b.cod_etapax IN ( 4, 5 )
					  GROUP BY a.num_despac
				)
				UNION 
				( /* Despachos con novedades etapa Descargue antes de Sitio */
						SELECT c.num_despac 
						  FROM ".BASE_DATOS.".tab_despac_contro c 
					INNER JOIN ".BASE_DATOS.".tab_genera_noveda d 
							ON c.cod_noveda = d.cod_noveda 
						 WHERE c.num_despac IN ( {$mDespac} ) 
						   AND d.cod_etapax IN ( 4, 5 )
				) ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespacDes = $mConsult -> ret_matrix('a');
		$mDespacDes = join( ',', GetColumnFromMatrix( $mDespacDes, 'num_despac' ) );

		# Despachos para recorrer y verificar si estan en etapa Descargue Filtro 2
		$mSql = "	 SELECT a.num_despac, a.cod_tipdes 
					   FROM ".BASE_DATOS.".tab_despac_despac a 
					  WHERE a.num_despac IN ( {$mDespac} ) ";
		$mSql .= $mDespacDes != "" ? " AND a.num_despac NOT IN ( {$mDespacDes} ) " : "";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('i');

		$mDespacDes = $mDespacDes == "" ? '""' : $mDespacDes;

		#Recorre Despachos Para verificar Filtro 2
		foreach ($mDespac as $row)
		{
			$mNextPC = getNextPC( self::$cConexion, $row[0] ); #Siguiente PC del Plan de ruta
			$mRutaDespac = getControDespac( self::$cConexion, $row[0] ); # Ruta del Despacho
			$mPosPC = (sizeof($mRutaDespac))-1; #Posicion Ultimo PC


			if(	( $mNextPC[cod_contro] == $mRutaDespac[$mPosPC][cod_contro] ) #Siguiente PC igual al ultimo PC del plan de ruta
				|| ( $mNextPC[cod_contro] == $mRutaDespac[($mPosPC-1)][cod_contro] && $mNextPC[ind_ensiti] == '1' ) #Siguiente PC igual al penultimo PC del plan de ruta con Novedades en Sitio
			  )
			{
			  	$mDespacDes .= ','.$row[0];
				$i++;
			}
			elseif(	( $mNextPC[cod_contro] == $mRutaDespac[($mPosPC-1)][cod_contro] && $mNextPC[ind_ensiti] == '0' ) #Siguiente PC igual al penultimo PC del plan de ruta con Novedades antes de Sitio
					 || ( $mNextPC[cod_contro] == $mRutaDespac[($mPosPC-2)][cod_contro] && $mNextPC[ind_ensiti] == '1' ) #Siguiente PC igual al antepenultimo PC del plan de ruta con Novedades en Sitio
				  )
			{
				$mTime = self::getTimeDescargue( $mTransp, $row[1] );
				$mTime = "-".$mTime." minute";
				$mDate = date ( 'Y-m-d H:i:s', ( strtotime( $mTime, strtotime ( $mNextPC[fec_planea] ) ) ) ); #Fecha Planeada para iniciar Seguimiento en Descargue

				if( $mDate <= self::$cHoy )
					$mDespacDes .= ','.$row[0];
			}
		}

		$mDespacDes = trim($mDespacDes, ',');

		#Informacion de los despachos en Etapa Descargue
		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax,
						h.abr_tercer AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					AND a.num_despac IN ( {$mDespacDes} )
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
					 ON b.cod_transp = c.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
					AND a.cod_depori = d.cod_depart 
					AND a.cod_paiori = d.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
					 ON a.cod_ciudes = e.cod_ciudad 
					AND a.cod_depdes = e.cod_depart 
					AND a.cod_paides = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
					 ON a.cod_depori = f.cod_depart 
					AND a.cod_paiori = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
					 ON a.cod_depdes = g.cod_depart 
					AND a.cod_paides = g.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
					 ON b.cod_conduc = h.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
					 ON a.cod_tipdes = i.cod_tipdes
			 LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k 
					 ON a.cod_client = k.cod_tercer 
				  WHERE 1=1 ";

		if( $mSinFiltro == false )
		{
			#Filtros por Formulario
			#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
			$mSql .= self::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". self::$cTipDespac .") " : "";

			#Filtros por usuario
			$mSql .= self::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. self::$cTipDespacContro .') ' : '';	
		}

		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		
		$mTipValida = self::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = self::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

			#warning1
			if( ($_REQUEST[ind_limpio] === '1' || $_REQUEST[ind_limpio] === '0') && $mSinFiltro == false )
			{
				if(		( $_REQUEST[ind_limpio] === '1' && ($mData[ind_limpio] === '1' || $mData[ind_limpio] == '') ) #Despachos Limpios
					||	( $_REQUEST[ind_limpio] === '0' && $mData[ind_limpio] === '0' ) #Despachos no Limpios 
				)
				{
					$mResult[$j] = $mDespac[$i];
					$mResult[$j][can_noveda] = $mData[can_noveda];
					$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
					$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
					$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
					$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
					$mResult[$j][fec_planea] = $mData[fec_planea];
					$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut];
					$j++;
				}
			}
			else
			{
				$mResult[$j] = $mDespac[$i];
				$mResult[$j][can_noveda] = $mData[can_noveda];
				$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
				$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
				$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
				$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
				$mResult[$j][fec_planea] = $mData[fec_planea];
				$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut];
				$j++;
			}
		}
		
		# Resultados de la funcion
		if( $mTipReturn == 'array' )
			return GetColumnFromMatrix( $mDespac, 'num_despac' );
		elseif( $mTipReturn == 'list' )
			return join(',', GetColumnFromMatrix($mDespac, 'num_despac') );
		elseif( $mTipReturn == 'list2' )
			return $mResult = $mDespacCargue.','.( join(',', GetColumnFromMatrix($mDespac, 'num_despac') )  );
		else
			return $mResult;
	}

	/*! \fn: getDespacTransi1
	 *  \brief: Trae los despachos para las empresas que solo tienen parametrizado ind_transi
	 *  \author: Ing. Fabian Salinas
	 *	\date: 26/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: mTransp  Array  Informacion transportadora
	 *  \return: Matriz
	 */
	private function getDespacTransi1( $mTransp )
	{
		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax,
						h.abr_tercer AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					AND a.fec_salida IS NOT NULL 
					AND a.fec_salida <= NOW() 
					AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
					AND a.ind_planru = 'S' 
					AND a.ind_anulad = 'R'
					AND b.ind_activo = 'S' 
					AND b.cod_transp = '".$mTransp[cod_transp]."'
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
					 ON b.cod_transp = c.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
					AND a.cod_depori = d.cod_depart 
					AND a.cod_paiori = d.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
					 ON a.cod_ciudes = e.cod_ciudad 
					AND a.cod_depdes = e.cod_depart 
					AND a.cod_paides = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
					 ON a.cod_depori = f.cod_depart 
					AND a.cod_paiori = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
					 ON a.cod_depdes = g.cod_depart 
					AND a.cod_paides = g.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
					 ON b.cod_conduc = h.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
					 ON a.cod_tipdes = i.cod_tipdes
			 LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k 
					 ON a.cod_client = k.cod_tercer 
				  WHERE 1=1 ";
		$mSql .= ($_REQUEST['cod_client'] != NULL || $_REQUEST['cod_client'] != ''?" AND a.cod_client IN (".str_replace(array('"",','"'),array('',''),$_REQUEST['cod_client']).") ":"");
		#Filtros por Formulario
		#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
		$mSql .= self::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". self::$cTipDespac .") " : "";

		#Filtros por usuario
		$mSql .= self::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. self::$cTipDespacContro .') ' : '';	
		
		echo "<pre style='display:none;' id='andres2'>"; print_r($mSql); echo "</pre>";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = self::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = self::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

			#warning1
			if( $_REQUEST[ind_limpio] === '1' || $_REQUEST[ind_limpio] === '0' )
			{
				if(		( $_REQUEST[ind_limpio] === '1' && ($mData[ind_limpio] === '1' || $mData[ind_limpio] == '') ) #Despachos Limpios
					||	( $_REQUEST[ind_limpio] === '0' && $mData[ind_limpio] === '0' ) #Despachos no Limpios 
				)
				{
					$mResult[$j] = $mDespac[$i];
					$mResult[$j][can_noveda] = $mData[can_noveda];
					$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
					$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
					$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
					$mResult[$j][nov_especi] = $mData[nov_especi];
					$mResult[$j][ind_alarma] = $mData[ind_alarma];
					$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
					$mResult[$j][fec_planea] = $mData[fec_planea];
					$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut]; #Aplica para empresas que solo tienen parametrizado seguimiento Transito
					$j++;
				}
			}
			else
			{
				$mResult[$j] = $mDespac[$i];
				$mResult[$j][can_noveda] = $mData[can_noveda];
				$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
				$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
				$mResult[$j][nov_especi] = $mData[nov_especi];
				$mResult[$j][ind_alarma] = $mData[ind_alarma];
				$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
				$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
				$mResult[$j][fec_planea] = $mData[fec_planea];
				$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut]; #Aplica para empresas que solo tienen parametrizado seguimiento Transito
				$j++;
			}
		}

		return $mResult;
	}

	/*! \fn: getDespacTransi2
	 *  \brief: Trae los despachos para las empresas que tienen parametrizado Cargue, Transito y Descargue
	 *  \author: Ing. Fabian Salinas
	 *	\date: 07/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: mTransp  Array  Informacion transportadora
	 *  \return: Matriz
	 */
	public function getDespacTransi2( $mTransp )
	{
		$mDespacCarDes = self::getDespacDescar( $mTransp, 'list2', true ); #Despachos en Etapas Cargue y Descargue
		$mDespacCarDes = trim($mDespacCarDes, ',');

		#Despachos en ruta  
		$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat 
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND ( xx.fec_salida IS NOT NULL  )
						AND yy.cod_transp = '".$mTransp[cod_transp]."' ";
		$mSql .= ($_REQUEST['cod_client'] != NULL || $_REQUEST['cod_client'] != ''?" AND xx.cod_client IN (".str_replace(array('"",','"'),array('',''),$_REQUEST['cod_client']).") ":"");

		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		if( sizeof($mDespac) < 1 )
			return false;

		$mDespac = join( ',', GetColumnFromMatrix( $mDespac, 'num_despac' ) );

		#Despachos en Etapa Transito Filtro 3
		$mSql = "( /* Despachos con novedades etapa Descargue en Sitio */
						SELECT a.num_despac 
						  FROM ".BASE_DATOS.".tab_despac_noveda a 
					INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
							ON a.cod_noveda = b.cod_noveda 
						 WHERE a.num_despac IN ( {$mDespac} ) 
						   AND b.cod_etapax IN ( 3 )
					  GROUP BY a.num_despac
				)
				UNION 
				( /* Despachos con novedades etapa Descargue antes de Sitio */
						SELECT c.num_despac 
						  FROM ".BASE_DATOS.".tab_despac_contro c 
					INNER JOIN ".BASE_DATOS.".tab_genera_noveda d 
							ON c.cod_noveda = d.cod_noveda 
						 WHERE c.num_despac IN ( {$mDespac} ) 
						   AND d.cod_etapax IN ( 3 )
				) ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		echo "<pre style='display:none;' id='mDespacTrasiandres'>"; print_r($mSql); echo "</pre>";
		$mDespacTrasi = $mConsult -> ret_matrix('a');
		$mDespacTrasi = join( ',', GetColumnFromMatrix( $mDespacTrasi, 'num_despac' ) );
		$mDespacTrasi = $mDespacTrasi ? $mDespacTrasi : '0';



		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax,
						h.abr_tercer AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					AND a.fec_salida IS NOT NULL 
					AND a.fec_salida <= NOW() 
					AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
					AND a.ind_planru = 'S' 
					AND a.ind_anulad = 'R'
					AND b.ind_activo = 'S' 
					AND b.cod_transp = '".$mTransp['cod_transp']."' 
			".( $mDespacCarDes == '' ? "" : " AND a.num_despac NOT IN ( {$mDespacCarDes} ) " )."
					AND a.num_despac IN ( {$mDespacTrasi} )
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
					 ON b.cod_transp = c.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
					AND a.cod_depori = d.cod_depart 
					AND a.cod_paiori = d.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
					 ON a.cod_ciudes = e.cod_ciudad 
					AND a.cod_depdes = e.cod_depart 
					AND a.cod_paides = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
					 ON a.cod_depori = f.cod_depart 
					AND a.cod_paiori = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
					 ON a.cod_depdes = g.cod_depart 
					AND a.cod_paides = g.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
					 ON b.cod_conduc = h.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
					 ON a.cod_tipdes = i.cod_tipdes 
			 LEFT JOIN ".BASE_DATOS.".tab_despac_corona j
			 	 	 ON a.num_despac = j.num_dessat
			 LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k 
					 ON a.cod_client = k.cod_tercer
				  WHERE 1=1     ";

		#Filtros por Formulario
		#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
		$mSql .= self::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". self::$cTipDespac .") " : "";

		#Filtros por usuario
		$mSql .= self::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. self::$cTipDespacContro .') ' : '';	
		

		echo "<pre style='display:none;' id='Transito2'>"; print_r($mSql); echo "</pre>";

		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = self::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = self::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

			#warning1
			if( $_REQUEST[ind_limpio] === '1' || $_REQUEST[ind_limpio] === '0' )
			{
				if(		( $_REQUEST[ind_limpio] === '1' && ($mData[ind_limpio] === '1' || $mData[ind_limpio] == '') ) #Despachos Limpios
					||	( $_REQUEST[ind_limpio] === '0' && $mData[ind_limpio] === '0' ) #Despachos no Limpios 
				)
				{
					$mResult[$j] = $mDespac[$i];
					$mResult[$j][can_noveda] = $mData[can_noveda];
					$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
					$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
					$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
					$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
					$mResult[$j][fec_planea] = $mData[fec_planea];
					$j++;
				}
			}
			else
			{
				$mResult[$j] = $mDespac[$i];
				$mResult[$j][can_noveda] = $mData[can_noveda];
				$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
				$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
				$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
				$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
				$mResult[$j][fec_planea] = $mData[fec_planea];
				$j++;
			}
		}

		return $mResult;
	}

	/*! \fn: getDespacControl
	 *  \brief: Trae los despachos para las empresas que solo tienen parametrizado ind_segctrl
	 *  \author: Ing. Luis Manrique
	 *	\date: 09/12/2019
	 *	\date modified: dia/mes/año
	 *  \param: mTransp  Array  Informacion transportadora
	 *  \return: Matriz
	 */
	private function getDespacControl( $mTransp )
	{
		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax,
						h.abr_tercer AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					AND a.fec_salida IS NOT NULL 
					AND a.fec_salida <= NOW() 
					AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
					AND a.ind_planru = 'S' 
					AND a.ind_anulad = 'R'
					AND b.ind_activo = 'S' 
					AND b.cod_transp = '".$mTransp[cod_transp]."'
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
					 ON b.cod_transp = c.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
					AND a.cod_depori = d.cod_depart 
					AND a.cod_paiori = d.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
					 ON a.cod_ciudes = e.cod_ciudad 
					AND a.cod_depdes = e.cod_depart 
					AND a.cod_paides = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
					 ON a.cod_depori = f.cod_depart 
					AND a.cod_paiori = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
					 ON a.cod_depdes = g.cod_depart 
					AND a.cod_paides = g.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
					 ON b.cod_conduc = h.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
					 ON a.cod_tipdes = i.cod_tipdes
			 LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k 
					 ON a.cod_client = k.cod_tercer 
				  WHERE 1=1 ";
		$mSql .= ($_REQUEST['cod_client'] != NULL || $_REQUEST['cod_client'] != ''?" AND a.cod_client IN (".str_replace(array('"",','"'),array('',''),$_REQUEST['cod_client']).") ":"");
		#Filtros por Formulario
		#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
		$mSql .= self::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". self::$cTipDespac .") " : "";

		#Filtros por usuario
		$mSql .= self::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. self::$cTipDespacContro .') ' : '';	
		
		echo "<pre style='display:none;' id='andres2'>"; print_r($mSql); echo "</pre>";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = self::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = self::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

			#warning1
			if( $_REQUEST[ind_limpio] === '1' || $_REQUEST[ind_limpio] === '0' )
			{
				if(		( $_REQUEST[ind_limpio] === '1' && ($mData[ind_limpio] === '1' || $mData[ind_limpio] == '') ) #Despachos Limpios
					||	( $_REQUEST[ind_limpio] === '0' && $mData[ind_limpio] === '0' ) #Despachos no Limpios 
				)
				{
					$mResult[$j] = $mDespac[$i];
					$mResult[$j][can_noveda] = $mData[can_noveda];
					$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
					$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
					$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
					$mResult[$j][nov_especi] = $mData[nov_especi];
					$mResult[$j][ind_alarma] = $mData[ind_alarma];
					$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
					$mResult[$j][fec_planea] = $mData[fec_planea];
					$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut]; #Aplica para empresas que solo tienen parametrizado seguimiento Transito
					$j++;
				}
			}
			else
			{
				$mResult[$j] = $mDespac[$i];
				$mResult[$j][can_noveda] = $mData[can_noveda];
				$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
				$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
				$mResult[$j][nov_especi] = $mData[nov_especi];
				$mResult[$j][ind_alarma] = $mData[ind_alarma];
				$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
				$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
				$mResult[$j][fec_planea] = $mData[fec_planea];
				$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut]; #Aplica para empresas que solo tienen parametrizado seguimiento Transito
				$j++;
			}
		}

		return $mResult;
	}

	/*! \fn: getInfoDespac
	 *  \brief: Trae informacion adicional del despacho
	 *  \author: Ing. Fabian Salinas
	 *	\date: 02/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: mDespac  Array  Data del Despacho
	 *  \param: mTransp  Array  Informacion transportadora
	 *  \param: mTipValida  String  Tipo de validacion
	 *  \return: Array
	 */
	private function getInfoDespac( $mDespac, $mTransp, $mTipValida )
	{
		$mNovDespac = getNovedadesDespac( self::$cConexion, $mDespac[num_despac], 1 ); # Novedades del Despacho -- Script /lib/general/function.inc
		$mCantNoved = sizeof($mNovDespac); # Cantidad de Novedades del Despacho
		$mPosN = $mCantNoved-1; #Posicion ultima Novedad

		$mResult[can_noveda] = $mCantNoved;
		$mResult[ind_fuepla] = $mNovDespac[$mPosN][ind_fuepla];
		$mResult[ind_limpio] = $mNovDespac[$mPosN][ind_limpio];
		$mResult[fec_ultnov] = $mNovDespac[$mPosN][fec_crenov];
		$mResult[nov_especi] = $mNovDespac[$mPosN][nov_especi];
		$mResult[ind_alarma] = $mNovDespac[$mPosN][ind_alarma];
		$mResult[nom_ultnov] = $mNovDespac[$mPosN][nom_noveda] == '' ? '-' : $mNovDespac[$mPosN][nom_noveda];
		$mResult[nom_sitiox] = $mNovDespac[$mPosN][nom_sitiox] == '' ? '-' : $mNovDespac[$mPosN][nom_sitiox];
		$mResult[sig_pcontr] = getNextPC( self::$cConexion, $mDespac[num_despac] );
		$mResult[pla_rutaxx] = getControDespac( self::$cConexion, $mDespac[num_despac] ); # Plan de Ruta del Despacho -- Script /lib/general/function.inc

		if( $mTipValida == 'tie_parame' )
		{
			if( $mDespac[tie_contra] != '' ) #Tiempo parametrizado por Despacho
				$mTime = $mDespac[tie_contra];
			elseif( $mDespac[cod_tipdes] == '1' ) #Despacho Urbano
				$mTime = $mTransp[tie_urbano];
			else #Otros Tipos de despacho se toma el tiempo de Despachos Nacionales
				$mTime = $mTransp[tie_nacion];
			
			$mFecUltReport = $mDespac[fec_salida];
			#Verifica la ultima novedad que no mantienen alarma
			for ($i=$mPosN; $i >= 0; $i--)
			{
				if( $mNovDespac[$i][ind_manala] == '0' ){
					$mFecUltReport = $mNovDespac[$i][fec_crenov];
					$mTime = $mNovDespac[$i][tiem_duraci] > 0 ? $mNovDespac[$i][tiem_duraci] : $mTime;
					break;
				}elseif( $i == 0 ){#Si el despacho no tiene ninguna novedad que no mantenga alarma toma fecha salida del sistema
					$mFecUltReport = $mDespac[fec_salida];
					$mTime = 0;
				}
			}

			$mTime = "+".$mTime." minute";
			$mResult[fec_planea] = date ( 'Y-m-d H:i:s', ( strtotime( $mTime, strtotime ( $mFecUltReport ) ) ) ); #Fecha Planeada para el Seguimiento
		}
		else
		{ #warning2
			if( $mNovDespac[$mPosN][tiem_duraci] != '0' )
			{ #Si la ultima novedad solicita tiempo  	
				$mTime = $mNovDespac[$mPosN][tiem_duraci];
				$mTime = "+".$mTime." minute";
				$mResult[fec_planea] = date ( 'Y-m-d H:i:s', ( strtotime( $mTime, strtotime ( $mResult[fec_ultnov] ) ) ) );
			}
			elseif( 	$mResult[fec_ultnov] < $mResult[sig_pcontr][fec_progra] # Fecha de la ultima novedad menor a la fecha planeada del siguiente PC
				|| 	($mResult[pla_rutaxx][(sizeof($mResult[pla_rutaxx])-1)][cod_contro] == $mNovDespac[$mPosN][cod_contro] && $mNovDespac[$mPosN][ind_ensiti] == '1') # Ultimo PC con novedad en sitio es el ultimo PC del plan de ruta
			  ){
			  	$mResult[fec_planea] = $mResult[sig_pcontr][fec_progra]; #Fecha planeada del siguinete PC
			}else{
			  	if( $mNovDespac[$mPosN][ind_manala] == '0' )
				{ #Si la ultima novedad no mantiene alarma
					$mTime = $mDespac[cod_tipdes] == '1' ? self::$cTime[ind_desurb] : self::$cTime[ind_desnac];
					$mTime = "+".$mTime." minute";
					$mResult[fec_planea] = date ( 'Y-m-d H:i:s', ( strtotime( $mTime, strtotime ( $mResult[fec_ultnov] ) ) ) );
				}
				else
					$mResult[fec_planea] = $mResult[sig_pcontr][fec_progra]; #Fecha planeada del siguinete PC
			}
		}
		return $mResult;
	}

	/*! \fn: despacRutaPlaca
	 *  \brief: Verifica despachos en ruta por placa, a cargo faro y sin novedades en ultimo PC
	 *  \author: Ing. Fabian Salinas
	 *	\date: 29/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: mNumPlacax  String  Placa
	 *  \param: mNumDespac  String  Numero del despacho 
	 *  \return: String
	 */
	private function despacRutaPlaca( $mCodTransp, $mNumPlacax, $mNumDespac )
	{
		$mColor = array('', '#FFFF66', '#FFC266');
		$mCantid=0;

		$mSql .= "SELECT a.num_despac 
					FROM ".BASE_DATOS.".tab_despac_despac a 
			  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					  ON a.num_despac = b.num_despac 
				   WHERE a.fec_salida IS NOT NULL 
					 AND a.fec_salida <= NOW() 
					 AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00') 
					 AND a.ind_planru = 'S' 
					 AND a.ind_anulad = 'R' 
					 AND b.ind_activo = 'S' 
					 AND b.num_placax LIKE '{$mNumPlacax}' 
					 AND b.cod_transp = '{$mCodTransp}'
					 AND a.num_despac != '{$mNumDespac}' 
					 AND a.ind_defini = '0' ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('i');

		if( sizeof($mDespac) > 0 )
		{
			foreach ($mDespac as $row)
			{
				$mUltPC = getControDespac( self::$cConexion, $row[0] );
				$mUltPC = end( $mUltPC );

				$mSql = "SELECT a.cod_noveda 
						   FROM ".BASE_DATOS.".tab_despac_noveda a 
						  WHERE a.num_despac = '{$row[0]}' 
							AND a.cod_contro = '{$mUltPC[cod_contro]}' ";
				$mConsult = new Consulta( $mSql, self::$cConexion );
				$mNovedad = $mConsult -> ret_matrix('i');

				if( sizeof($mNovedad) < 1 )
					$mCantid++;
			}
		}

		if( $mCantid == 0 )
			return $mColor[0];
		elseif( $mCantid == 1 )
			return $mColor[1];
		else
			return $mColor[2];
	}

	/*! \fn: getTranspCargaControlador
	 *  \brief: trae las transportadoras asignadas como carga laboral de un controlador o eal
	 *  \author: Ing. Fabian Salinas
	 *  \date: 21/09/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: string
	 */
	private function getTranspCargaControlador() {
		if( self::$cTypeUser[tip_perfil] == 'CONTROL' || self::$cTypeUser[tip_perfil] == 'EAL' ) {
			$mSql = "SELECT GROUP_CONCAT(a.cod_transp SEPARATOR ',') AS lis_transp
					   FROM satt_faro.vis_monito_encdet a
					  WHERE a.cod_usuari = '".$_SESSION[datos_usuario][cod_usuari]."' ";
			$mConsult = new Consulta( $mSql, self::$cConexion );
			$mResult = $mConsult -> ret_arreglo();
			return $mResult[0];
		} else {
			return null;
		}
	}

	/*! \fn: getTranspServic
	 *  \brief: Traer las transportadoras según tipo de servicio
	 *  \author: Ing. Fabian Salinas
	 *	\date: 19/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: mTipEtapax  String   Tipo de Seguimiento ( ind_segcar, ind_segtra, ind_segdes )
	 *  \param: mCodTransp  Integer  Codigo de la transportadora 
	 *  \param: mAddWherex  String   Where adicional
	 *  \return: Matriz
	 */
	public function getTranspServic( $mTipEtapax = NULL, $mCodTransp = NULL, $mAddWherex = NULL )
	{
		$mTipServic = '""';
		$mTipServic .= $_REQUEST[tip_servic1] == '1' ? ',"1"' : '';
		$mTipServic .= $_REQUEST[tip_servic2] == '1' ? ',"2"' : '';
		$mTipServic .= $_REQUEST[tip_servic3] == '1' ? ',"3"' : '';
		$mLisTransp = $this->getTranspCargaControlador();

		$mSql = " SELECT a.*,
						 GROUP_CONCAT(h.cod_usuari ORDER BY h.cod_usuari ASC SEPARATOR ', ' ) AS usr_asigna
					FROM (
										SELECT c.ind_segprc,c.ind_segcar, 
										       c.ind_segctr,c.ind_segtra, c.ind_segdes, 
											   c.cod_transp, c.num_consec, d.nom_tipser, 
											   UPPER(e.abr_tercer) AS nom_transp, c.tie_contro AS tie_nacion, 
											   c.tie_conurb AS tie_urbano, c.tie_desurb, 
											   c.tie_desnac, c.tie_desimp, c.tie_desexp, 
											   c.tie_destr1, c.tie_destr2, c.cod_tipser, 
											   c.ind_conper, c.hor_pe1urb, c.hor_pe2urb, 
											   c.hor_pe1nac, c.hor_pe2nac, c.hor_pe1imp, 
											   c.hor_pe2imp, c.hor_pe1exp, c.hor_pe2exp, 
											   c.hor_pe1tr1, c.hor_pe2tr1, c.hor_pe1tr2, 
											   c.hor_pe2tr2 
										  FROM ".BASE_DATOS.".tab_transp_tipser c 
									INNER JOIN ".BASE_DATOS.".tab_genera_tipser d 
											ON c.cod_tipser = d.cod_tipser 
									INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e 
											ON c.cod_transp = e.cod_tercer 
									INNER JOIN (	  SELECT cod_transp , MAX(num_consec) AS num_consec 
														FROM ".BASE_DATOS.".tab_transp_tipser  
													GROUP BY cod_transp 
											   ) f ON c.cod_transp = f.cod_transp AND c.num_consec = f.num_consec
									  GROUP BY c.cod_transp
						 ) a 
			   LEFT JOIN ".BASE_DATOS.".vis_monito_encdet h
					  ON a.cod_transp = h.cod_transp
					WHERE 1=1 ";

		$mSql .= $mTipEtapax == NULL ? "" : " AND a.{$mTipEtapax} = 1 ";
		$mSql .= $mAddWherex == NULL ? "" : $mAddWherex;

		#Filtro por codigo de Transportadora
		if( self::$cTypeUser[tip_perfil] == 'CLIENTE' )
			$mSql .= " AND a.cod_transp = '". self::$cTypeUser[cod_transp] ."' ";
		else{
			if( $mCodTransp != NULL )
				$mSql .= $mCodTransp != 'TODAS' ? " AND a.cod_transp IN ( {$mCodTransp} ) " : "";
			else
				$mSql .= $_REQUEST[cod_transp] ? " AND a.cod_transp IN ( {$_REQUEST[cod_transp]} ) " : "";
		}

		$mCodUsuari = explode(',', $_REQUEST[cod_usuari]);
		$mSinFiltro = false;
		foreach ($mCodUsuari as $key => $value) {
			if( $value == '"SIN"' ){
				$mSinFiltro = true;
				break;
			}
		}

		#Filtro Por Usuario Asignado
		if( self::$cTypeUser[tip_perfil] == 'CONTROL' || self::$cTypeUser[tip_perfil] == 'EAL' ){
			$mSql .= " AND h.cod_usuari = '".$_SESSION[datos_usuario][cod_usuari]."' ";
			$mSql .= $mLisTransp != '' && $mLisTransp != null ? " AND a.cod_transp IN ( $mLisTransp ) " : " AND a.cod_transp IN ( '' ) ";
		} elseif( $mSinFiltro == true )
			$mSql .= " AND ( h.cod_transp IS NULL OR h.cod_usuari IN ({$_REQUEST[cod_usuari]}) )";
		else
			$mSql .= $_REQUEST[cod_usuari] ? " AND h.cod_usuari IN ( {$_REQUEST[cod_usuari]} ) " : "";

		#Otros Filtros
		$mSql .= $mTipServic != '""' ? " AND a.cod_tipser IN (".$mTipServic.") " : "";

		$mSql .= " GROUP BY a.cod_transp ORDER BY h.cod_usuari, a.nom_transp ASC ";

		$mConsult = new Consulta( $mSql, self::$cConexion );
		return $mConsult -> ret_matrix('a');
	}

	/*! \fn: tipValidaTiempo
	 *  \brief: Verifica el tipo de validacion que aplica por transportadora
	 *  \author: Ing. Fabian Salinas
	 *	\date: 22/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: Data Transportadora
	 *  \return: 
	 */
	private function tipValidaTiempo( $mTransp )
	{
		$mBandera = $mTransp[tie_nacion] == 0 && $mTransp[tie_urbano] == 0 ? 0 : 1;

		if( ($mTransp[nom_tipser] == 'MA' && $mBandera == 1) || ($mTransp[nom_tipser] == 'EAL/MA' && $mBandera == 1) )
			$mResult = 'tie_parame';
		else
			$mResult = 'fec_alarma';

		return $mResult;
	}

	/*! \fn: getTimeDescargue
	 *  \brief: Retorna el tiempo parametrizado para iniciar seguimiento etapa Descargue
	 *  \author: Ing. Fabian Salinas
	 *	\date: 07/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: mTransp  Array  Informacion de la Transportadora
	 *  \param: mCodTipdes  String  Codigo Tipo de Despacho   
	 *  \return: Integer
	 */
	private function getTimeDescargue( $mTransp, $mCodTipdes )
	{
		switch ($mCodTipdes) {
			case '1':
				$mResult = $mTransp[tie_desurb];
				break;
			
			case '2':
				$mResult = $mTransp[tie_desnac];
				break;
			
			case '3':
				$mResult = $mTransp[tie_desimp];
				break;
			
			case '4':
				$mResult = $mTransp[tie_desexp];
				break;
			
			case '5':
				$mResult = $mTransp[tie_destr1];
				break;
			
			case '6':
				$mResult = $mTransp[tie_destr2];
				break;
			
			default:
				$mResult = $mTransp[tie_desnac];
				break;
		}
		return $mResult;
	}

	/*! \fn: calTimeAlarma
	 *  \brief: Calcula el tiempo por fecha de alarma
	 *  \author: Ing. Fabian Salinas
	 *	\date: 22/06/2016
	 *	\date modified: dia/mes/año
	 *  \param: $mDespac   Matriz	Datos Despachos
	 *  \param: $mTransp   Array  	Informacion de la transportadora
	 *  \param: $mIndCant  Integer  0:Retorna Despachos con Tiempos; 1:Retorna Cantidades
	 *  \param: $mFiltro   String  	Filtro para el detallado por color, sinF = Todos
	 *  \param: $mColor	Array  	Colores por Etapa
	 *  \return: Matriz
	 */
	private function calTimeAlarma( $mDespac, $mTransp, $mIndCant = 0, $mFiltro = NULL, $mColor = NULL )
	{
		$mTipValida = self::tipValidaTiempo( $mTransp );

		if( $mIndCant == 1 )
		{ #Define Cantidades según estado
			$mResult[fin_rutaxx] = 0;
			$mResult[ind_acargo] = 0;
			$mResult[est_pernoc] = 0;
			$mResult[sin_retras] = 0;
			$mResult[con_alarma] = 0;
			$mResult[con_00A30x] = 0;
			$mResult[con_31A60x] = 0;
			$mResult[con_61A90x] = 0;
			$mResult[con_91Amas] = 0;
		}else
		{ #Variables de Posicion
			$mNegTiempo = 0; #neg_tiempo
			$mPosTiempo = 0; #pos_tiempo
			$mNegFinrut = 0; #neg_finrut
			$mPosFinrut = 0; #pos_finrut
			$mNegAcargo = 0; #neg_acargo
			$mPosAcargo = 0; #pos_acargo
			$mNegTieesp = 0; #neg_tieesp
			$mPosTieesp = 0; #pos_tieesp
		}
		
		for ($i=0; $i < sizeof($mDespac); $i++)
		{
			$mPernoc = false; #Bandera para despachos estado pernoctacion
			$mFecPerno = '';

			if( $mDespac[$i][can_noveda] > 0 )
			{#Despacho con Novedades
				$mDespac[$i][tiempo] = getDiffTime( $mDespac[$i][fec_planea], self::$cHoy ); #Script /lib/general/function.inc

				if( $mDespac[$i][ind_fuepla] == '1' && $mDespac[$i][tiempo] < 0 )
					$mPernoc = true;
			}
			else #Despacho Sin Novedades
				$mDespac[$i][tiempo] = getDiffTime( $mDespac[$i][fec_salida], self::$cHoy ); #Script /lib/general/function.inc


			# Arma la matriz resultante 
			if( $mIndCant == 1 )
			{# Cantidades según estado
				if( $mPernoc == true ) #Pernoctacion
					$mResult[est_pernoc]++;
				elseif( $mDespac[$i][ind_finrut] == '1' ) #Por Llegada
					$mResult[fin_rutaxx]++;
				elseif( $mDespac[$i][ind_defini] == 'SI' ) #A Cargo Empresa
					$mResult[ind_acargo]++;
				elseif( $mDespac[$i][tiempo] < 0 ) #Sin Retraso
					$mResult[sin_retras]++;
				elseif( $mDespac[$i][tiempo] < 31 && $mDespac[$i][tiempo] >= 0 ){
					 # 0 a 30
					$mResult[con_00A30x]++;
					$mResult[con_alarma]++;
				}
				elseif( $mDespac[$i][tiempo] < 61 && $mDespac[$i][tiempo] > 30 ) {
					# 31 a 60
					$mResult[con_31A60x]++;
					$mResult[con_alarma]++;
				}
				elseif( $mDespac[$i][tiempo] < 91 && $mDespac[$i][tiempo] > 60 ) {
					# 61 a 90
					$mResult[con_61A90x]++;
					$mResult[con_alarma]++;
				}
				elseif( $mDespac[$i][tiempo] > 90 ){
					# Mayor 90
					$mResult[con_91Amas]++;
					$mResult[con_alarma]++;
				} 
				else{
					continue;
				}
			}
			else
			{# Colores e información del despacho según estado

				if( $mFiltro == 'sinF' )
					$mBandera = true;
				elseif( $mPernoc != true && $mDespac[$i][ind_finrut] != '1' && $mDespac[$i][ind_defini] != 'SI' ) #Para los filtros por tiempos desde "Sin Retraso" hasta "Mayor 90"
					$mBandera = true;
				else
					$mBandera = false;

				#Arma Matriz resultante seún fase
				if( ($mFiltro == 'est_pernoc' || $mFiltro == 'sinF') && $mPernoc == true && $mDespac[$i][ind_defini] != 'SI' && $mDespac[$i][ind_finrut] != '1' )
				{ #Pernoctacion
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[neg_tieesp][$mNegTieesp] = $mDespac[$i];
						$mResult[neg_tieesp][$mNegTieesp][color] = $mColor[0];
						$mResult[neg_tieesp][$mNegTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[neg_tieesp][$mNegTieesp][fase] = 'est_pernoc';
						$mNegTieesp++;
					}else{
						$mResult[neg_tiempo][$mNegTiempo] = $mDespac[$i];
						$mResult[neg_tiempo][$mNegTiempo][color] = $mColor[0];
						$mResult[neg_tiempo][$mNegTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[neg_tiempo][$mNegTiempo][fase] = 'est_pernoc';
						$mNegTiempo++;
					}
				}
				elseif( ($mFiltro == 'fin_rutaxx' || $mFiltro == 'sinF') && $mDespac[$i][ind_finrut] == '1' && $mDespac[$i][ind_defini] != 'SI' )
				{ #Por Llegada
					if( $mDespac[$i][tiempo] < 0 ){
						$mResult[neg_finrut][$mNegFinrut] = $mDespac[$i];
						$mResult[neg_finrut][$mNegFinrut][color] = $mColor[0];
						$mResult[neg_finrut][$mNegFinrut][fase] = 'fin_rutaxx';
						$mNegFinrut++;
					}else{
						$mResult[pos_finrut][$mPosFinrut] = $mDespac[$i];
						$mResult[pos_finrut][$mPosFinrut][color] = $mColor[0];
						$mResult[pos_finrut][$mPosFinrut][fase] = 'fin_rutaxx';
						$mPosFinrut++;
					}
				}
				elseif( ($mFiltro == 'ind_acargo' || $mFiltro == 'sinF') && $mDespac[$i][ind_defini] == 'SI' )
				{ #A Cargo Empresa
					if( $mDespac[$i][tiempo] < 0 ){
						$mResult[neg_acargo][$mNegAcargo] = $mDespac[$i];
						$mResult[neg_acargo][$mNegAcargo][color] = $mColor[0];
						$mResult[neg_acargo][$mNegAcargo][fase] = 'ind_acargo';
						$mNegAcargo++;
					}else{
						$mResult[pos_acargo][$mPosAcargo] = $mDespac[$i];
						$mResult[pos_acargo][$mPosAcargo][color] = $mColor[0];
						$mResult[pos_acargo][$mPosAcargo][fase] = 'ind_acargo';
						$mPosAcargo++;
					}
				}
				elseif( ($mFiltro == 'sin_retras' || $mFiltro == 'sinF') && $mDespac[$i][tiempo] < 0 && $mBandera == true )
				{ #Sin Retraso
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[neg_tieesp][$mNegTieesp] = $mDespac[$i];
						$mResult[neg_tieesp][$mNegTieesp][color] = $mColor[0];
						$mResult[neg_tieesp][$mNegTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[neg_tieesp][$mNegTieesp][fase] = 'sin_retras';
						$mNegTieesp++;
					}else{
						$mResult[neg_tiempo][$mNegTiempo] = $mDespac[$i];
						$mResult[neg_tiempo][$mNegTiempo][color] = $mColor[0];
						$mResult[neg_tiempo][$mNegTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[neg_tiempo][$mNegTiempo][fase] = 'sin_retras';
						$mNegTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_00A30x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma') && $mDespac[$i][tiempo] < 31 && $mDespac[$i][tiempo] >= 0 && $mBandera == true )
				{ # 0 a 30
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[1];
						$mResult[pos_tieesp][$mPosTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_00A30x';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[1];
						$mResult[pos_tiempo][$mPosTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_00A30x';
						$mPosTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_31A60x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma') && $mDespac[$i][tiempo] < 61 && $mDespac[$i][tiempo] > 30 && $mBandera == true )
				{ # 31 a 60
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[2];
						$mResult[pos_tieesp][$mPosTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_31A60x';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[2];
						$mResult[pos_tiempo][$mPosTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_31A60x';
						$mPosTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_61A90x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma') && $mDespac[$i][tiempo] < 91 && $mDespac[$i][tiempo] > 60 && $mBandera == true )
				{ # 61 a 90
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[3];
						$mResult[pos_tieesp][$mPosTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_61A90x';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[3];
						$mResult[pos_tiempo][$mPosTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_61A90x';
						$mPosTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_91Amas' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma') && $mDespac[$i][tiempo] > 90 && $mBandera == true )
				{ # Mayor 90
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[4];
						$mResult[pos_tieesp][$mPosTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_91Amas';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[4];
						$mResult[pos_tiempo][$mPosTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_91Amas';
						$mPosTiempo++;
					}
				}else{
					continue;
				}
			}
		}
		return $mResult;
	}

	/*! \fn: detailBand
	 *  \brief: Detalle de la bandeja, lista los despachos
	 *  \author: Ing. Fabian Salinas
	 *	\date: 25/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function detailBand()
	{
		$mTittle = array('NO.', 'NEM', 'DESPACHO', 'TIEMPO', 'A C. EMPRESA', 'NO. TRANSPORTE', 'NOVEDADES', 'ORIGEN', 'DESTINO', 'TRANSPORTADORA', 'GENERADOR', 'PLACA', 'CONDUCTOR', 'CELULAR', 'UBICACI&Oacute;N', 'FECHA SALIDA', 'ULTIMA NOVEDAD' );

		$mTittle2 = array('NO.', 'NEM', 'DESPACHO', 'NO. SOLICITUD', 'NO. TRANSPORTE', 'TIEMPO SEGUIMIENTO', 'TIEMPO CITA DE CARGUE', 'NO. NOVEDADES', 'PLACA', 'ORIGEN', 'ESTADO', 'ULTIMA NOVEDAD', 'OBSERVACION', 'FECHA Y HORA NOVEDAD' );

		$mTransp = self::getTranspServic( $_REQUEST['ind_etapax'], $_REQUEST['cod_transp'] );

		#Según Etapa
		switch ( $_REQUEST['ind_etapax'] )
		{
			case 'ind_segprc':
				$mColor = array('', 'bgPC1', 'bgPC2', 'bgPC3', 'bgPC4', "bgPC5");
				$mNameFunction = 'getDespacPrcCargue';
				break;

			case 'ind_segcar':
				$mColor = array('', 'bgC1', 'bgC2', 'bgC3', 'bgC4');
				$mNameFunction = 'getDespacCargue';
				break;

			case 'ind_segtra':
				$mColor = array('', 'bgT1', 'bgT2', 'bgT3', 'bgT4');
				break;

			case 'ind_segctr':
				$mColor = array('', 'bgT1', 'bgT2', 'bgT3', 'bgT4');
				$mNameFunction = 'getDespacControl';
				break;

			case 'ind_segdes':
				$mColor = array('', 'bgD1', 'bgD2', 'bgD3', 'bgD4');
				$mNameFunction = 'getDespacDescar';
				break;
			
			default:
				$mColor = array('');
				break;
		}

		$mNegTieesp = array(); #neg_tieesp
		$mPosTieesp = array(); #pos_tieesp
		$mNegTiempo = array(); #neg_tiempo
		$mPosTiempo = array(); #pos_tiempo
		$mNegFinrut = array(); #neg_finrut
		$mPosFinrut = array(); #pos_finrut
		$mNegAcargo = array(); #neg_acargo
		$mPosAcargo = array(); #pos_acargo
		
		#array datos precarga
		$con_paradi = array(); #para el dia
		$con_paraco = array(); #para el corte
		$con_anulad = array(); #anuladas
		$con_planta = array(); #llegada en planta
		$enx_planta = array(); #en planta de etapa de cargue
		$con_porter = array(); #en porteria
		$con_sinseg = array(); #sin seguimineto
		$con_tranpl = array(); #transito a planta
		$con_cnnlap = array(); #con novedad no llegada a planta
		$con_cnlapx = array(); #con novedad llegada a planta
		$con_acargo = array(); #A cargo de empresa

		#Trae Data por transportadoras
		for ($i=0; $i < sizeof($mTransp); $i++)
		{
			if( $_REQUEST['ind_etapax'] == 'ind_segtra' )
				$mNameFunction = $mTransp[$i]['ind_segcar'] == '1' && $mTransp[$i]['ind_segdes'] == '1' ? 'getDespacTransi2' : 'getDespacTransi1';
 
			if($_REQUEST['ind_etapax'] == '')
			{
				$mNameFunction = $mTransp[$i]['ind_segcar'] == '1' && $mTransp[$i]['ind_segdes'] == '1' ? 'getDespacTransi2' : 'getDespacTransi1';
				$mDespac1 = array(); 
				$mDespac2 = array();
				$mDespac3 = array();
				$mDespac1 = self::getDespacCargue( $mTransp[$i] );
				$mDespac2 = self::$mNameFunction( $mTransp[$i] );
				$mDespac3 = self::getDespacDescar( $mTransp[$i] );
				$mDespac  = array_merge($mDespac1,$mDespac2,$mDespac3);
			}
			else
			{	
				$mDespac = self::$mNameFunction( $mTransp[$i] );

			}
		
			if($_REQUEST['ind_etapax']=='ind_segprc'){

				$mDespac = self::getTotalPrecargue( $mDespac, $mTransp[$i], 0, $_REQUEST['ind_filtro'], $mColor );
				 
				$con_paradi = $mDespac['con_paradi'] ? array_merge($con_paradi, $mDespac['con_paradi']) : $con_paradi;
				$con_paraco = $mDespac['con_paraco'] ? array_merge($con_paraco, $mDespac['con_paraco']) : $con_paraco;
				$con_anulad = $mDespac['con_anulad'] ? array_merge($con_anulad, $mDespac['con_anulad']) : $con_anulad;
				$con_planta = $mDespac['con_planta'] ? array_merge($con_planta, $mDespac['con_planta']) : $con_planta;
				$enx_planta = $mDespac['enx_planta'] ? array_merge($enx_planta, self::getDespacCargue( $mTransp[$i] ) ) : $enx_planta;
				$con_porter = $mDespac['con_porter'] ? array_merge($con_porter, $mDespac['con_porter']) : $con_porter;
				$con_sinseg = $mDespac['con_sinseg'] ? array_merge($con_sinseg, $mDespac['con_sinseg']) : $con_sinseg;
				$con_tranpl = $mDespac['con_tranpl'] ? array_merge($con_tranpl, $mDespac['con_tranpl']) : $con_tranpl;
				$con_cnnlap = $mDespac['con_cnnlap'] ? array_merge($con_cnnlap, $mDespac['con_cnnlap']) : $con_cnnlap;
				$con_cnlapx = $mDespac['con_cnlapx'] ? array_merge($con_cnlapx, $mDespac['con_cnlapx']) : $con_cnlapx;
				$con_acargo = $mDespac['con_acargo'] ? array_merge($con_acargo, $mDespac['con_acargo']) : $con_acargo;
			}
			else
			{

				$mDespac = self::calTimeAlarma( $mDespac, $mTransp[$i], 0, $_REQUEST['ind_filtro'], $mColor );

				$mNegTieesp = $mDespac['neg_tieesp'] ? array_merge($mNegTieesp, $mDespac['neg_tieesp']) : $mNegTieesp;
				$mPosTieesp = $mDespac['pos_tieesp'] ? array_merge($mPosTieesp, $mDespac['pos_tieesp']) : $mPosTieesp;
				$mNegTiempo = $mDespac['neg_tiempo'] ? array_merge($mNegTiempo, $mDespac['neg_tiempo']) : $mNegTiempo;
				$mPosTiempo = $mDespac['pos_tiempo'] ? array_merge($mPosTiempo, $mDespac['pos_tiempo']) : $mPosTiempo;
				$mNegFinrut = $mDespac['neg_finrut'] ? array_merge($mNegFinrut, $mDespac['neg_finrut']) : $mNegFinrut;
				$mPosFinrut = $mDespac['pos_finrut'] ? array_merge($mPosFinrut, $mDespac['pos_finrut']) : $mPosFinrut;
				$mNegAcargo = $mDespac['neg_acargo'] ? array_merge($mNegAcargo, $mDespac['neg_acargo']) : $mNegAcargo;
				$mPosAcargo = $mDespac['pos_acargo'] ? array_merge($mPosAcargo, $mDespac['pos_acargo']) : $mPosAcargo;
			}
			
		}


		if($_REQUEST['ind_etapax']=='ind_segprc'){
			#Pinta tablas
			//$mData = self::orderMatrizDetailPrc( $con_paradi, $con_paraco, $con_anulad, $con_planta, $con_porter, $con_sinseg, $con_tranpl, $con_cnnlap, $con_cnlapx );
			//$mComparadi = self::orderMatrizDetailPrc($con_paradi, 'ASC');
		 
			$mHtml  = '';
			$mHtml .= $con_paradi ? self::printTabDetail( $mTittle2, self::orderMatrizDetailPrc($con_paradi), sizeof($con_paradi).'DESPACHOS PENDIENTES', '1' ) : '';
			$mHtml .= $con_paraco ? self::printTabDetail( $mTittle2, self::orderMatrizDetailPrc($con_paraco), sizeof($con_paraco).'DESPACHOS PARA EL CORTE', '1' ) : '';
			$mHtml .= $con_anulad ? self::printTabDetail( $mTittle2, self::orderMatrizDetailPrc($con_anulad), sizeof($con_anulad).'DESPACHOS ANULADOS', '1' ) : '';
			$mHtml .= $enx_planta ? self::printTabDetail( $mTittle2, self::orderMatrizDetailPrc($enx_planta), sizeof($enx_planta).'DESPACHOS EN PLANTA', '1' ) : '';
			//$mHtml .= $con_planta ? self::printTabDetail( $mTittle2, self::orderMatrizDetailPrc($con_planta), sizeof($con_planta).'DESPACHOS EN PLANTA N', '1' ) : '';
			$mHtml .= $con_porter ? self::printTabDetail( $mTittle2, self::orderMatrizDetailPrc($con_porter), sizeof($con_porter).'DESPACHOS EN PORTERIA', '1' ) : '';
			$mHtml .= $con_sinseg ? self::printTabDetail( $mTittle2, self::orderMatrizDetailPrc($con_sinseg), sizeof($con_sinseg).'DESPACHOS SIN COMUNICACION', '1' ) : '';
			$mHtml .= $con_tranpl ? self::printTabDetail( $mTittle2, self::orderMatrizDetailPrc($con_tranpl), sizeof($con_tranpl).'DESPACHOS EN TRANSITO A PLANTA', '1' ) : '';
			$mHtml .= $con_cnnlap ? self::printTabDetail( $mTittle2, self::orderMatrizDetailPrc($con_cnnlap), sizeof($con_cnnlap).'DESPACHOS CON NOVEDAD NO LLEGADA A PLANTA', '1' ) : '';
			$mHtml .= $con_cnlapx ? self::printTabDetail( $mTittle2, self::orderMatrizDetailPrc($con_cnlapx), sizeof($con_cnlapx).'DESPACHOS CON NOVEDAD LLEGADA A PLANTA', '1' ) : '';
			$mHtml .= $con_acargo ? self::printTabDetail( $mTittle2, self::orderMatrizDetailPrc($con_acargo), sizeof($con_acargo).'DESPACHOS A CARGO EMPRESA', '1' ) : '';


		}
		else{

			$mData = self::orderMatrizDetail( $mNegTieesp, $mPosTieesp, $mNegTiempo, $mPosTiempo, $mNegFinrut, $mPosFinrut, $mNegAcargo, $mPosAcargo );
			#Pinta tablas
			for ($i=0; $i < sizeof($mData['tiempo']); $i++) { 
				if ($mData['tiempo'][$i]['nov_especi'] == '1' && $mData['tiempo'][$i]['ind_alarma'] == 'S' ) {
					$mData['novesp'][$i] = $mData['tiempo'][$i];
					unset($mData['tiempo'][$i]);
				}else{
					continue;
				}
			}

			$mHtml  = '';
			$mHtml .= $mData['tieesp'] ? self::printTabDetail( $mTittle, $mData['tieesp'], sizeof($mData['tieesp']).' DESPACHOS CON TIEMPO MODIFICADO', '1' ) : '';
			$mHtml .= $mData['tiemp0'] ? self::printTabDetail( $mTittle, $mData['tiemp0'], sizeof($mData['tiemp0']).' DESPACHOS EN SEGUIMIENTO SIN NOVEDADES', '1' ) : '';
			$mHtml .= $mData['novesp'] ? self::printTabDetail( $mTittle, $mData['novesp'], sizeof($mData['novesp']).' VEHICULOS CON NOVEDADES ESPECIALES (MA)', '1' ) : '';
			$mHtml .= $mData['tiempo'] ? self::printTabDetail( $mTittle, $mData['tiempo'], sizeof($mData['tiempo']).' DESPACHOS EN SEGUIMIENTO CON NOVEDADES', '1' ) : '';
			$mHtml .= $mData['acargo'] ? self::printTabDetail( $mTittle, $mData['acargo'], sizeof($mData['acargo']).' DESPACHOS A CARGO EMPRESA', '1' ) : '';
			$mHtml .= $mData['finrut'] ? self::printTabDetail( $mTittle, $mData['finrut'], sizeof($mData['finrut']).' DESPACHOS PENDIENTE LLEGADA', '1' ) : '';
			
		}

		echo $mHtml;
	}

	/*! \fn: printTabDetail
	 *  \brief: Pinta la tabla del detalle de la bandeja
	 *  \author: Ing. Fabian Salinas
	 *	\date: 23/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: mTittle  Array  Titulos para la cabecera de la tabla
	 *  \param: mData   Matriz  Data para el contenido de la tabla
	 *  \param: mOpcion	Integer Opcion para el link del despacho
	 *  \return: html
	 */
	private function printTabDetail( $mTittle, $mData, $mSection, $mOpcion )
	{
		#verifico los permisos para las novedades nem registradas por responsable
		$mViewPr = self::getView('jso_plarut');
		#Dibuja Cabecera tabla 
		$mHtml  = '<table class="classTable" width="100%" cellspacing="0" cellpadding="0" align="center">';

		$mHtml .= 	'<tr>';
		$mHtml .= 		'<th class="classHead" align="center" colspan="'.sizeof($mTittle).'" >'.$mSection.'<span type="button" onclick="exportExcel()"> [EXCEL]   </span></th>';
		$mHtml .= 	'</tr>';

		$mHtml .= 	'<tr>';
		foreach ($mTittle as $value){
			$mHtml .= '<th class="classHead" align="center">'.$value.'</th>';
			//if($value=="NO."){$mHtml .= '<th style="display:none;" align="center">N? SOLICITUD</th>';}
		}
		$mHtml .= 	'</tr>';

		#Dibuja Data de la tabla
		$n=1;
		if($_REQUEST['ind_etapax']=='ind_segprc'){
			foreach ($mData as $row)
			{	
				$mNonEstado;
				switch ($row[cod_estado]) {
					case '1':
						$mNonEstado = 'PORTERIA';
						break;
					case '2':
						$mNonEstado = 'SIN COMUNICACION';
						break;
					case '3':
						$mNonEstado = 'TRANSITO A PLANTA';
						break;
					case '4':
						$mNonEstado = 'CON NOVEDAD NO LLEGA A PLANTA';
						break;
					case '5':
						$mNonEstado = 'CON NOVEDAD LLEGA A PLANTA';
						break;
					default:
       					$mNonEstado = '-';
       					break;
				}
				
				//$mTxt = substr($row[color], 3);
				$mNovedades = getNovedadesDespac( self::$cConexion , $row[num_despac], 1 ); #Novedades del despacho

				$gif = "";
				if(isset($row['num_despac']))
				{
					if(self::getNovedadNem($row['num_despac'])[0]['ind_soluci']=="0" && $mViewPr->ind_novnem->ind_visibl == 1)
					{
						$gif = "<img src='../".CENTRAL."/imagenes/Alert.gif' width='15px' height='15px'>";
					}
				}

				$mColor =  '#000000;';
				if($row["ind_anulad"]=="A" || $_REQUEST["ind_filtro"] == 'enx_planta'){
					$mLink =$row[num_despac];
				}
				else
				{
					$mLink = '<a href="index.php?cod_servic=3302&window=central&despac='.$row[num_despac].'&tie_ultnov='.$row[tiempS].'&opcion=1&etapa=prc" style="color:'.$mColor.'">'.$row[num_despac].'</a>';
				}
				$mHtml .= '<tr onclick="this.style.background=this.style.background==\'#CEF6CE\'?\'#eeeeee\':\'#CEF6CE\';">';
				$mHtml .= 	'<th class="classHead" nowrap="" align="left">'.$n.'</th>';
				$mHtml .= 	'<th class="classHead" nowrap="" align="left">'.$gif.'</th>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mLink.'</td>';
				$mHtml .= 	'<td class="classHead" nowrap="" align="left">'.$row[num_solici].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[cod_manifi].'</td>';

				if($row["ind_anulad"] == "A") {
					$mHtml .= 	'<td class="classCell   nowrap="" align="left"  >N/A</td>';
				}
				else{
					$mHtml .= 	'<td class="classCell '.$row[color2].' nowrap="" align="left" style="color:'.$mColor.'">'. $row[tiempS].'</td>';
				}

				$mHtml .= 	'<td class="classCell '.$row[color].' nowrap="" align="left" style="color:'.$mColor.'">'.$row[tiempo].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[can_noveda].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[num_placax].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[ciu_origen].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mNonEstado.'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[nom_ultnov].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left" width="300px">'.$mNovedades[sizeof($mNovedades) - 1]["obs_noveda"].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mNovedades[sizeof($mNovedades) - 1]["fec_crenov"].'</td>';
				$mHtml .= '</tr>';
				$n++;
			}
		}
		else
		{
			foreach ($mData as $row)
			{
				$gif = "";
				if(isset($row['num_despac']))
				{
					if(self::getNovedadNem($row['num_despac'])[0]['ind_soluci']=="0" && $mViewPr->ind_novnem->ind_visibl == 1)
					{
						$gif = "<img src='../".CENTRAL."/imagenes/Alert.gif' width='15px' height='15px'>";
					}
				}
				$mTxt = substr($row[color], 3);
				$mColor = $mTxt > 2 ? '#FFFFFF;' : '#000000;';
				$mLink = '<a href="index.php?cod_servic=3302&window=central&despac='.$row[num_despac].'&tie_ultnov='.$row[tiempo].'&opcion=1" style="color:'.$mColor.'">'.$row[num_despac].'</a>';
				$mHtml .= '<tr onclick="this.style.background=this.style.background==\'#CEF6CE\'?\'#eeeeee\':\'#CEF6CE\';">';
				$mHtml .= 	'<th class="classHead" nowrap="" align="left">'.$n.'</th>';
				$mHtml .= 	'<th class="classHead" nowrap="" align="left">'.$gif.'</th>';
				$mHtml .= 	'<td class="classCell bt '.$row[color].'" nowrap="" align="left">'.$mLink.'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[tiempo].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[ind_defini].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[cod_manifi].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[can_noveda].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[ciu_origen].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[ciu_destin].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[nom_transp].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[nom_genera].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left" style="background: '.$row[color2].'" >'.$row[num_placax].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[nom_conduc].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[num_telmov].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[nom_sitiox].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[fec_salida].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[nom_ultnov].'</td>';
				$mHtml .= '</tr>';
				$n++;
			}
		}
		

		$mHtml .= '</table>';
		$mHtml .= '<br/>';
		$_SESSION['precargue']['detallado'] = $mHtml;
		return $mHtml;
	}

	/*! \fn: orderMatrizDetail
	 *  \brief: Ordena la Matriz Resultante para el detalle
	 *  \author: Ing. Fabian Salinas
	 *	\date: 23/07/2015
	 *	\date modified: 17/05/2016
	 *  \modified by: Ing. Fabian Salinas
	 *  \param: mNegTieesp  matriz  Despachos con Tiempo Modificado
	 *  \param: mPosTieesp  matriz  Despachos con Tiempo Modificado
	 *  \param: mNegTiempo  matriz  Despachos en Seguimiento tiempo Negativo
	 *  \param: mPosTiempo  matriz  Despachos en Seguimiento tiempo Positivo
	 *  \param: mNegFinrut  matriz  Despachos Pendiente Llegada tiempo Negativo
	 *  \param: mPosFinrut  matriz  Despachos Pendiente Llegada tiempo Positivo
	 *  \param: mNegAcargo  matriz  Despachos a Cargo Empresa tiempo Negativo
	 *  \param: mPosAcargo  matriz  Despachos a Cargo Empresa tiempo Positivo
	 *  \return: Matriz
	 */
	private function orderMatrizDetail( $mNegTieesp, $mPosTieesp, $mNegTiempo, $mPosTiempo, $mNegFinrut, $mPosFinrut, $mNegAcargo, $mPosAcargo )
	{
		$mData = array();
		#Ordena Matriz Por tiempo
		$mNega = $mNegTieesp ? SortMatrix( $mNegTieesp, 'tiempo', 'ASC'  ) : array();
		$mPosi = $mPosTieesp ? SortMatrix( $mPosTieesp, 'tiempo', 'DESC' ) : array();
		$mData['tieesp'] = array_merge($mPosi, $mNega);

		$mNegTiempo = self::separateMatrix($mNegTiempo);
		$mPosTiempo = self::separateMatrix($mPosTiempo);

		$mNega = $mNegTiempo[0] ? SortMatrix( $mNegTiempo[0], 'tiempo', 'ASC'  ) : array();
		$mPosi = $mPosTiempo[0] ? SortMatrix( $mPosTiempo[0], 'tiempo', 'DESC' ) : array();
		$mData['tiemp0'] = array_merge($mPosi, $mNega);

		$mNega = $mNegTiempo[1] ? SortMatrix( $mNegTiempo[1], 'tiempo', 'ASC'  ) : array();
		$mPosi = $mPosTiempo[1] ? SortMatrix( $mPosTiempo[1], 'tiempo', 'DESC' ) : array();
		$mData['tiempo'] = array_merge($mPosi, $mNega);

		$mNega = $mNegFinrut ? SortMatrix( $mNegFinrut, 'tiempo', 'ASC'  ) : array();
		$mPosi = $mPosFinrut ? SortMatrix( $mPosFinrut, 'tiempo', 'DESC' ) : array();
		$mData['finrut'] = array_merge($mPosi, $mNega);

		$mNega = $mNegAcargo ? SortMatrix( $mNegAcargo, 'tiempo', 'ASC'  ) : array();
		$mPosi = $mPosAcargo ? SortMatrix( $mPosAcargo, 'tiempo', 'DESC' ) : array();
		$mData['acargo'] = array_merge($mPosi, $mNega);

		return $mData;
	}
	
	/*! \fn: orderMatrizDetailPrc
	 *  \brief: Ordena la Matriz Resultante para el detalle
	 *  \author: Ing. Fabian Salinas
	 *	\date: 23/07/2015
	 *	\date modified: 17/05/2016
	 *  \modified by: Ing. Fabian Salinas
	 *  \param: mNegTieesp  matriz  Despachos con Tiempo Modificado
	 *  \param: mPosTieesp  matriz  Despachos con Tiempo Modificado
	 *  \param: mNegTiempo  matriz  Despachos en Seguimiento tiempo Negativo
	 *  \param: mPosTiempo  matriz  Despachos en Seguimiento tiempo Positivo
	 *  \param: mNegFinrut  matriz  Despachos Pendiente Llegada tiempo Negativo
	 *  \param: mPosFinrut  matriz  Despachos Pendiente Llegada tiempo Positivo
	 *  \param: mNegAcargo  matriz  Despachos a Cargo Empresa tiempo Negativo
	 *  \param: mPosAcargo  matriz  Despachos a Cargo Empresa tiempo Positivo
	 *  \return: Matriz
	 */
	private function orderMatrizDetailPrc( $con_paradi )
	{ 
		$mData = array();
		#Ordena Matriz Por tiempo 
		$con_paradiPos = array();
		$con_paradiNeg = array();
		$mPosi = array();
		$mNega = array();

		foreach ($con_paradi AS $key => $value) {
			if($value["tiempo"] >= 0) {
			 $con_paradiPos[] = $value;

			}
			else{
			 $con_paradiNeg[] = $value;
			}
		}

		$mPosi = $con_paradiPos ? SortMatrix( $con_paradiPos, 'tiempo', "DESC" ) : array();  
		$mNega = $con_paradiNeg ? SortMatrix( $con_paradiNeg, 'tiempo', "ASC" ) : array();  
	 	
 
	 	$mReturn = array_merge($mPosi,$mNega );

 
		return $mReturn;
	}

	/*! \fn: detailSearch
	 *  \brief: Imprime resultados de la busqueda según despacho en transito o finalizados
	 *  \author: Ing. Fabian Salinas
	 *	\date: 08/09/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function detailSearch()
	{
		if( $_REQUEST[ind_entran] == '1' ){
			echo self::search();
		}

		if( $_REQUEST[ind_fintra] == '1' ){
			echo self::search( true );
		}
	}

	/*! \fn: search
	 *  \brief: Muestra el detallado de los despachos que conincidan con los parametros de busqueda
	 *  \author: Ing. Fabian Salinas
	 *	\date: 10/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: mIndFinrut  Boolean  True = Indicador despacho finalizado
	 *  \return:
	 */
	private function search( $mIndFinrut = false )
	{
		$mTittle = array('NO.', 'NEM', 'DESPACHO', 'TIEMPO', 'A C. EMPRESA', 'NO. TRANSPORTE', 'NOVEDADES', 'ORIGEN', 'DESTINO', 'TRANSPORTADORA', 'PLACA', 'CONDUCTOR', 'CELULAR', 'UBICACI&Oacute;N', 'FECHA SALIDA', 'ULTIMA NOVEDAD' );
		$mColor = array('', 'bgT1', 'bgT2', 'bgT3', 'bgT4');
		$mIndConsol = false;

		$mDespac = self::getDataSearch( $mIndFinrut );

		if( sizeof($mDespac) < 1 && $_REQUEST['num_viajex'] )
		{#Verifica si el viaje fue consolidado
			$mNumDespac = self::getDespacConsol( $_REQUEST['num_viajex'] );

			if( sizeof($mNumDespac) > 0 ){
				$mDespac = self::getDataSearch( $mIndFinrut, $mNumDespac );
				$mIndConsol = true;
			}
		}

		if( sizeof($mDespac) < 1 )
		{#No se encontraron resultados
			$mHtml = "<center>";
			$mHtml .= "<br/>No se encontr&oacute; ning&uacute;n Despacho ".( $mIndFinrut == true ? "finalizado" : "en tr&aacute;nsito" )." con el par&aacute;metro de b&uacute;squeda: </br>";
			$mHtml .= $_REQUEST[num_despac] ? " N&uacute;mero del Despacho = {$_REQUEST[num_despac]} " : "";
			$mHtml .= $_REQUEST[num_placax] ? " Placa = {$_REQUEST[num_placax]} " : "";
			$mHtml .= $_REQUEST[num_celcon] ? " Celular del Conductor = {$_REQUEST[num_celcon]} " : "";
			$mHtml .= $_REQUEST[num_viajex] ? " N&uacute;mero de Viaje = {$_REQUEST[num_viajex]} " : "";
			$mHtml .= $_REQUEST[num_solici] ? " N&uacute;mero de Solicitud = {$_REQUEST[num_solici]} " : "";
			$mHtml .= $_REQUEST[num_pedido] ? " N&uacute;mero de Pedido = {$_REQUEST[num_pedido]} " : "";
			$mHtml .= $_REQUEST[num_factur] ? " N&uacute;mero de Factura = {$_REQUEST[num_factur]} " : "";
			$mHtml .= $_REQUEST[cod_manifi] ? " N&uacute;mero de Manifiesto = {$_REQUEST[cod_manifi]} " : "";
			$mHtml .= $_REQUEST[cod_tercer] ? " C.C. del Conductor = {$_REQUEST[cod_tercer]} " : "";
			$mHtml .= "</center>";

			$mResult = $mHtml;
		}
		elseif( $mIndConsol == true )
		{#Despachos consolidados
			$mNovedades = getNovedadesDespac( self::$cConexion , $mDespac[$i][num_despac], 1 ); #Novedades del despacho
			$n = sizeof($mNovedades);

			$mDespac[0][can_noveda] = $n;
			$mDespac[0][nom_ultnov] = $mNovedades[($n-1)][nom_noveda];
			$mDespac[0][nom_sitiox] = $mNovedades[($n-1)][nom_sitiox];
			$mDespac[0][tiempo] = '-';

			$mHtml = self::printTabDetail( $mTittle, $mDespac, sizeof($mDespac).' DESPACHOS CONSOLIDADOS', '1' );
		}
		elseif( $mIndFinrut == false )
		{#Despachos en transito
			$mNegTieesp = array(); #neg_tieesp
			$mPosTieesp = array(); #pos_tieesp
			$mNegTiempo = array(); #neg_tiempo
			$mPosTiempo = array(); #pos_tiempo
			$mNegFinrut = array(); #neg_finrut
			$mPosFinrut = array(); #pos_finrut
			$mNegAcargo = array(); #neg_acargo
			$mPosAcargo = array(); #pos_acargo

			# Verifica Novedades por despacho
			for( $i=0; $i<sizeof($mDespac); $i++ )
			{
				$mTransp = self::getTranspServic( NULL, $mDespac[$i][cod_transp] );
				$mTipValida = self::tipValidaTiempo( $mTransp[0] );
				$mData = self::getInfoDespac( $mDespac[$i], $mTransp[0], $mTipValida );

				$mDespac[$i][can_noveda] = $mData[can_noveda];
				$mDespac[$i][fec_ultnov] = $mData[fec_ultnov];
				$mDespac[$i][nom_ultnov] = $mData[nom_ultnov];
				$mDespac[$i][nom_sitiox] = $mData[nom_sitiox];
				$mDespac[$i][fec_planea] = $mData[fec_planea];
				$mDespac[$i][ind_finrut] = $mData[sig_pcontr][ind_finrut]; 

				$mDespacho[0] = $mDespac[$i];
				$mData = self::calTimeAlarma( $mDespacho, $mTransp, 0, 'sinF', $mColor );

				$mNegTieesp = $mData[neg_tieesp] ? array_merge($mNegTieesp, $mData[neg_tieesp]) : $mNegTieesp;
				$mPosTieesp = $mData[pos_tieesp] ? array_merge($mPosTieesp, $mData[pos_tieesp]) : $mPosTieesp;
				$mNegTiempo = $mData[neg_tiempo] ? array_merge($mNegTiempo, $mData[neg_tiempo]) : $mNegTiempo;
				$mPosTiempo = $mData[pos_tiempo] ? array_merge($mPosTiempo, $mData[pos_tiempo]) : $mPosTiempo;
				$mNegFinrut = $mData[neg_finrut] ? array_merge($mNegFinrut, $mData[neg_finrut]) : $mNegFinrut;
				$mPosFinrut = $mData[pos_finrut] ? array_merge($mPosFinrut, $mData[pos_finrut]) : $mPosFinrut;
				$mNegAcargo = $mData[neg_acargo] ? array_merge($mNegAcargo, $mData[neg_acargo]) : $mNegAcargo;
				$mPosAcargo = $mData[pos_acargo] ? array_merge($mPosAcargo, $mData[pos_acargo]) : $mPosAcargo;
			}

			$mData = self::orderMatrizDetail( $mNegTieesp, $mPosTieesp, $mNegTiempo, $mPosTiempo, $mNegFinrut, $mPosFinrut, $mNegAcargo, $mPosAcargo );

			#Pinta tablas
			$mHtml  = '';
			$mHtml .= $mData['tieesp'] ? self::printTabDetail( $mTittle, $mData['tieesp'], sizeof($mData['tieesp']).' DESPACHOS CON TIEMPO MODIFICADO', '1' ) : '';
			$mHtml .= $mData['tiemp0'] ? self::printTabDetail( $mTittle, $mData['tiemp0'], sizeof($mData['tiemp0']).' DESPACHOS EN SEGUIMIENTO SIN NOVEDADES', '1' ) : '';
			$mHtml .= $mData['tiempo'] ? self::printTabDetail( $mTittle, $mData['tiempo'], sizeof($mData['tiempo']).' DESPACHOS EN SEGUIMIENTO CON NOVEDADES', '1' ) : '';
			$mHtml .= $mData['acargo'] ? self::printTabDetail( $mTittle, $mData['acargo'], sizeof($mData['acargo']).' DESPACHOS A CARGO EMPRESA', '1' ) : '';
			$mHtml .= $mData['finrut'] ? self::printTabDetail( $mTittle, $mData['finrut'], sizeof($mData['finrut']).' DESPACHOS PENDIENTE LLEGADA', '1' ) : '';
		}
		else
		{#Despachos Finalizados
			for ($i=0; $i < sizeof($mDespac); $i++)
			{
				$mNovedades = getNovedadesDespac( self::$cConexion , $mDespac[$i][num_despac], 1 ); #Novedades del despacho
				$n = sizeof($mNovedades);

				$mDespac[$i][can_noveda] = $n;
				$mDespac[$i][nom_ultnov] = $mNovedades[($n-1)][nom_noveda];
				$mDespac[$i][nom_sitiox] = $mNovedades[($n-1)][nom_sitiox];
				$mDespac[$i][nom_sitiox] = $mNovedades[($n-1)][obs_node];
				$mDespac[$i][tiempo] = '-';
			}

			$mHtml = self::printTabDetail( $mTittle, $mDespac, sizeof($mDespac).' DESPACHOS FINALIZADOS', '2' );
		}

		return utf8_encode($mHtml);
	}

	/*! \fn: getDespacConsol
	 *  \brief: Trae el despacho para para viajes consolidados
	 *  \author: Ing. Fabian Salinas
	 *	\date: 08/09/2015
	 *	\date modified: dia/mes/año
	 *  \param: mNumViajex  String   Numero de Viaje
	 *  \return: Integer
	 */
	private function getDespacConsol( $mNumViajex )
	{
		$mSql = "SELECT a.cod_despad 
				   FROM ".BASE_DATOS.".tab_consol_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_sisext b 
					 ON a.cod_deshij = b.num_despac 
				  WHERE b.num_desext LIKE '{$mNumViajex}' ";

		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mResult = $mConsult -> ret_arreglo();

		return $mResult[0];
	}

	/*! \fn: getDataSearch
	 *  \brief: Trae la información del despacho para opción busqueda especifica
	 *  \author: Ing. Fabian Salinas
	 *	\date: 08/09/2015
	 *	\date modified: dia/mes/año
	 *  \param: mIndFinrut  Boolean  True = Indicador despacho finalizado
	 *  \param: mNumDespac  Integer  Numero de despacho
	 *  \return: Matriz
	 */
	private function getDataSearch( $mIndFinrut = false, $mNumDespac = NULL )
	{
		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax, 
						UPPER(h.abr_tercer) AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, b.cod_transp, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					AND a.fec_salida IS NOT NULL 
					AND a.fec_salida <= NOW()  
					-- ".( $mNumDespac != NULL ? "" : ($mIndFinrut == true ? " AND a.fec_llegad IS NOT NULL AND a.fec_llegad != '0000-00-00 00:00:00' " : " AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00') ") )."
					".( $mIndFinrut == true ? " AND a.fec_llegad IS NOT NULL AND a.fec_llegad != '0000-00-00 00:00:00' " : " AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00') ")."
					".( $mNumDespac == NULL ? " AND a.ind_anulad = 'R' AND b.ind_activo = 'S' " : "" )."
					AND a.ind_planru = 'S' 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
					 ON b.cod_transp = c.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
					AND a.cod_depori = d.cod_depart 
					AND a.cod_paiori = d.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
					 ON a.cod_ciudes = e.cod_ciudad 
					AND a.cod_depdes = e.cod_depart 
					AND a.cod_paides = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
					 ON a.cod_depori = f.cod_depart 
					AND a.cod_paiori = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
					 ON a.cod_depdes = g.cod_depart 
					AND a.cod_paides = g.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
					 ON b.cod_conduc = h.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
					 ON a.cod_tipdes = i.cod_tipdes 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_sisext x 
					 ON a.num_despac = x.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_destin y 
					 ON a.num_despac = y.num_despac 
				  WHERE 1=1 ";

		if( self::$cTypeUser[tip_perfil] == 'CLIENTE' ){
			$mSql .= " AND b.cod_transp = ".self::$cTypeUser[cod_transp];
		}

		if( $mNumDespac == NULL ){
			$mSql .= $_REQUEST[num_despac] ? " AND a.num_despac = '{$_REQUEST[num_despac]}' " : "";
			$mSql .= $_REQUEST[num_placax] ? " AND b.num_placax LIKE '{$_REQUEST[num_placax]}' " : "";
			$mSql .= $_REQUEST[num_celcon] ? " AND h.num_telmov = '{$_REQUEST[num_celcon]}' " : "";
			$mSql .= $_REQUEST[num_viajex] ? " AND x.num_desext LIKE '{$_REQUEST[num_viajex]}' " : "";
			$mSql .= $_REQUEST[num_solici] ? " AND x.num_solici = '{$_REQUEST[num_solici]}' " : "";
			$mSql .= $_REQUEST[num_pedido] ? " AND x.num_pedido = '{$_REQUEST[num_pedido]}' " : "";
			$mSql .= $_REQUEST[num_factur] ? " AND y.num_docume = '{$_REQUEST[num_factur]}' " : "";
			$mSql .= $_REQUEST[cod_manifi] ? " AND a.cod_manifi = '{$_REQUEST[cod_manifi]}' " : "";
			$mSql .= $_REQUEST[cod_tercer] ? " AND h.cod_tercer = '{$_REQUEST[cod_tercer]}' " : "";
		}else
			$mSql .= " AND a.num_despac = '{$mNumDespac}' ";

		$mSql .= " GROUP BY a.num_despac ";
		$mSql .= $mIndFinrut == false ? "" : " ORDER BY a.fec_llegad DESC ";
		$mSql .= $mIndFinrut == false ? "" : " LIMIT 10 ";

		echo "<pre id='TorresAndres' $mNumDespac $mIndFinrut style='display:none' >"; print_r($mSql); echo "</pre>";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('a');
	}

	/*! \fn: printInformGeneral
	 *  \brief: Informe General
	 *  \author: Ing. Fabian Salinas
	 *	\date: 13/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function printInformGeneral()
	{
		$mTittle[texto] = array('NO.', 'TIPO SERVICIO', 'EMPRESA', 'EN SEGUIMIENTO', 'NO. DESPACHOS', 'CARGUE', 'TRANSITO', 'DESCARGUE', 'ESTADO PERNOCTACION', 'POR LLEGADA', 'A CARGO EMPRESA', 'USUARIO ASIGNADO' );
		$mTittle[style] = array('', '', '', 'bgAZ', '', 'bgC3', 'bgT1', 'bgD3', '', '', '', '');
		$mLimitFor = self::$cTypeUser[tip_perfil] == 'OTRO' ? sizeof($mTittle[texto]) : sizeof($mTittle[texto])-1;
		$mTransp = self::getTranspServic();
		$mReport = array();
		$mHtml1 = '';
		$n=1;

		for ($i=0; $i < sizeof($mTransp); $i++)
		{
			if( $mTransp[$i][ind_segcar] == 1 )
			{
				$mDespac = self::getDespacCargue( $mTransp[$i] );
				$mData = self::calTimeAlarma( $mDespac, $mTransp[$i], 1 );
				$mReport[$i][can_cargue] = ($mDespac == false ? 0 : sizeof($mDespac));
			}

			if( $mTransp[$i][ind_segtra] == 1 )
			{
				if( $mTransp[$i][ind_segcar] == 1  && $mTransp[$i][ind_segtra] == 1  && $mTransp[$i][ind_segdes] == 1  )
					$mDespac = self::getDespacTransi2( $mTransp[$i] ); 
				else
					$mDespac = self::getDespacTransi1( $mTransp[$i] );

				$mData = self::calTimeAlarma( $mDespac, $mTransp[$i], 1 );

				$mReport[$i][can_transi] = ($mDespac == false ? 0 : sizeof($mDespac));
			}

			if( $mTransp[$i][ind_segdes] == 1 )
			{
				$mDespac = self::getDespacDescar( $mTransp[$i] );
				$mData = self::calTimeAlarma( $mDespac, $mTransp[$i], 1 );
				$mReport[$i][can_descar] = ($mDespac == false ? 0 : sizeof($mDespac));
			}

			$mReport[$i][tot_despac] = $mReport[$i][can_cargue] + $mReport[$i][can_transi] + $mReport[$i][can_descar];

			if( $mReport[$i][tot_despac] > 0 )
			{
				$mEnSegi = $mReport[$i][tot_despac] - $mData[fin_rutaxx];

				#Dibuja cuerpo de la Tabla
				$mHtml1 .= '<tr onclick="this.style.background=this.style.background==\'#CEF6CE\'?\'#eeeeee\':\'#CEF6CE\';">';
				$mHtml1 .= 	'<th class="classCell" nowrap="" align="left">'.  $n.'</th>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="left">'.  $mTransp[$i][nom_tipser].'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="left">'.  $mTransp[$i][nom_transp].'</td>';

				if( self::$cTypeUser[tip_perfil] == 'OTRO' )
					$mHtml1 .= 	'<td class="classCell" nowrap="" align="center">'.	$mEnSegi .'</td>';

				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \'\', \''.$mTransp[$i]['cod_transp'].'\');">'.$mReport[$i][tot_despac].'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \'ind_segcar\', \''.$mTransp[$i]['cod_transp'].'\');">'.( $mReport[$i][can_cargue] ? $mReport[$i][can_cargue] : '-' ).'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \'ind_segtra\', \''.$mTransp[$i]['cod_transp'].'\');">'.( $mReport[$i][can_transi] ? $mReport[$i][can_transi] : '-' ).'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \'ind_segdes\', \''.$mTransp[$i]['cod_transp'].'\');">'.( $mReport[$i][can_descar] ? $mReport[$i][can_descar] : '-' ).'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'est_pernoc\', \'\', \''.$mTransp[$i]['cod_transp'].'\');">'.( $mData[est_pernoc] ? $mData[est_pernoc] : '-' ).'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'fin_rutaxx\', \'\', \''.$mTransp[$i]['cod_transp'].'\');">'.( $mData[fin_rutaxx] ? $mData[fin_rutaxx] : '-' ).'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'ind_acargo\', \'\', \''.$mTransp[$i]['cod_transp'].'\');">'.( $mData[ind_acargo] ? $mData[ind_acargo] : '-' ).'</td>';

				if( self::$cTypeUser[tip_perfil] == 'OTRO' )
					$mHtml1 .= 	'<td class="classCell" nowrap="" align="left">'.  ( $mTransp[$i][usr_asigna] != '' ? $mTransp[$i][usr_asigna] : 'SIN ASIGNAR' ).'</td>';
				
				$mHtml1 .= '</tr>';

				$n++;

				$mTotal[0] += $mReport[$i][tot_despac];
				$mTotal[1] += $mReport[$i][can_cargue];
				$mTotal[2] += $mReport[$i][can_transi];
				$mTotal[3] += $mReport[$i][can_descar];
				$mTotal[4] += $mData[est_pernoc];
				$mTotal[5] += $mData[fin_rutaxx];
				$mTotal[6] += $mData[ind_acargo];
				$mTotal[7] += $mEnSegi;
			}
		}

		#Dibuja la Fila de los Totales
		$mHtml2  = '<tr>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="right" colspan="3">TOTALES:</th>';

		if( self::$cTypeUser[tip_perfil] == 'OTRO' )
			$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[7].'</th>';

		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[0].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[1].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[2].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[3].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[4].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[5].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[6].'</th>';

		if( self::$cTypeUser[tip_perfil] == 'OTRO' )
			$mHtml2 .= '<th class="classTotal" nowrap="" align="center">&nbsp;</th>';

		$mHtml2 .= '</tr>';


		#Dibuja la Tabla Completa
		$mHtml  = '<table class="classTable" width="100%" cellspacing="0" cellpadding="0" align="center">';
		$mHtml .= 	'<tr>';
		for ($i=0; $i < $mLimitFor; $i++) {
			$mHtml .= $i != 3 || ( $i == 3 && self::$cTypeUser[tip_perfil] == 'OTRO' ) ? '<th class="classHead bt '.$mTittle[style][$i].'" align="center">'.$mTittle[texto][$i].'</th>' : '';
		}
		$mHtml .= 	'</tr>';

		$mHtml .= $mHtml2;
		$mHtml .= $mHtml1;
		$mHtml .= $mHtml2;

		$mHtml .= '</table>';

		return utf8_encode($mHtml);
	}

	/*! \fn: lista
	 *  \brief: Crea una lista desplegable para el formulario
	 *  \author: Ing. Fabian Salinas
	 *	\date: 14/07/2015
	 *	\date modified: dia/mes/año
	 *  \param: mTitulo  String  Titulo del Campo
	 *  \param: mNomSel  String  Nombre del Select
	 *  \param: mMatriz  Matriz  Matriz con las opciones
	 *  \param: mClass  String  Nombre de la clase para el <td>
	 *  \param: mObliga  boolean  Si el campo es obligatorio agrega *
	 *  \return: 
	 */
	public function lista( $mTitulo, $mNomSel, $mMatriz, $mClass, $mObliga = 0 )
	{
		$mHtml = '<td class="'.$mClass.'" align="right">'.( $mObliga ? '*' : '' ).$mTitulo.'</td>';

		$mHtml .= '<td class="'.$mClass.'">'; 
		$mHtml .= '<select name="'.$mNomSel.'" id="'.$mNomSel.'ID" onKeypress=buscar_op(this)>';

		$n = sizeof($mMatriz);
		for($i = 0; $i < $n; $i++){
			$mHtml .= '<option value="'.$mMatriz[$i][0].'">'.$mMatriz[$i][1].'</option>';
		}

		$mHtml .= '</select>';
		$mHtml .= '</td>';

		return $mHtml;
	}

	/*! \fn: getView
	 *  \brief: Trae los indicadores de secciones visibles por encargado (Perfil)
	 *  \author: Ing. Fabian Salinas
	 *  \date:  23/09/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mCatego   String   campo categoria a retornar
	 *  \return: Object
	 */
	public function getView( $mCatego )
	{
		$mSql = "SELECT a.jso_bandej, a.jso_encabe, a.jso_plarut 
				   FROM ".BASE_DATOS.".tab_genera_respon a 
			 INNER JOIN ".BASE_DATOS.".tab_genera_perfil b 
					 ON a.cod_respon = b.cod_respon 
				  WHERE b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
		$mConsult = new Consulta($mSql, self::$cConexion);
		$mData = $mConsult->ret_matrix('a');

		return json_decode($mData[0][$mCatego]);
	}

	/*! \fn: separateMatrix
	 *  \brief: Separa la la matriz [0]=> Sin Novedades, [1]=> Con Novedades
	 *  \author: Ing. Fabian Salinas
	 *  \date: 17/05/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mMatriz  Matriz  Matriz a separar
	 *  \return: Matriz
	 */
	private function separateMatrix($mMatriz){
		$mResult = array(array(), array());

		if( $mMatriz ){
			foreach ($mMatriz as $row) {
				if( $row['can_noveda'] > 0 ){
					$mResult[1][] = $row;
				}else{
					$mResult[0][] = $row;
				}
			}
		}

		return $mResult;
	}

	/*! \fn: printInformPernoc
	 *  \brief: Imprime el informe C. Pernoctacion
	 *  \author: Ing. Fabian Salinas
	 *  \date: 03/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function printInformPernoc( $mTittle ){
		$mData = self::getDataPernoc();
		$mLimitFor = self::$cTypeUser['tip_perfil'] == 'OTRO' ? sizeof($mTittle['texto']) : sizeof($mTittle['texto'])-1;

		#Dibuja la Tabla Completa
		$mHtml  = '<table class="classTable" width="100%" cellspacing="0" cellpadding="0" align="center">';
		$mHtml .= 	'<tr>';
		for ($i=0; $i < $mLimitFor; $i++){
			$mHtml .= '<th class="classHead bt '.$mTittle['style'][$i].'" align="center">'.$mTittle['texto'][$i].'</th>';
		}
		$mHtml .= 	'</tr>';

		$j=1;
		foreach ($mData as $row) {
			$mHtml .= '<tr onclick="this.style.background=this.style.background==\'#CEF6CE\'?\'#eeeeee\':\'#CEF6CE\';">';
				$mHtml .= 	'<th class="classCell" nowrap="" align="left">'.$j.'</th>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row['nom_tipser'].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row['nom_transp'].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row['num_cantid'].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.($row['usr_asigna'] != '' ? $row['usr_asigna'] : 'SIN ASIGNAR').'</td>';
			$mHtml .= '<tr>';

			$j++;
		}

		echo $mHtml;
	}

	/*! \fn: getDataPernoc
	 *  \brief: Trae la data para la pestaña C. PERNOCTACION
	 *  \author: Ing. Fabian Salinas
	 *  \date: 18/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	private function getDataPernoc(){
		$mTransp = self::getTranspServic(NULL, NULL, " AND a.ind_conper = 1 ");

		#Filtros por Formulario
		#$mWhere .= $_REQUEST[ind_limpio] ? " AND x.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
		$mWhere .= self::$cTipDespac != '""' ? " AND x.cod_tipdes IN (". self::$cTipDespac .") " : "";

		#Filtros por usuario
		$mWhere .= self::$cTipDespacContro != '""' ? 'AND x.cod_tipdes IN ('. self::$cTipDespacContro .') ' : '';	

		$i=0;
		foreach ($mTransp as $row) {
			$mSql = "
					 SELECT b.* 
					   FROM ( SELECT DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i') AS fec_actual ) a, 
							(
								 SELECT v.*, 
										CONCAT(DATE_FORMAT(NOW(), '%Y-%m-%d'), ' ', v.hor_perini) AS fec_perini, 
										CONCAT(DATE_FORMAT(NOW(), '%Y-%m-%d'), ' ', v.hor_perfin) AS fec_perfin 
								   FROM (
											 SELECT x.num_despac, x.cod_tipdes, y.cod_transp, 
													x.cod_ciuori, x.cod_depori, x.cod_paiori, 
													x.cod_ciudes, x.cod_depdes, x.cod_paides, 
													y.cod_conduc, z.cod_consec AS cod_pernoc, 
													DATE_FORMAT(z.fec_creaci, '%Y-%m-%d %H:%i') AS fec_pernoc, 
													CASE x.cod_tipdes 
														WHEN 1 THEN '$row[hor_pe1urb]'
														WHEN 2 THEN '$row[hor_pe1nac]'
														WHEN 3 THEN '$row[hor_pe1imp]'
														WHEN 4 THEN '$row[hor_pe1exp]'
														WHEN 5 THEN '$row[hor_pe1tr1]'
														WHEN 6 THEN '$row[hor_pe1tr2]'
													END AS hor_perini,
													CASE x.cod_tipdes 
														WHEN 1 THEN '$row[hor_pe2urb]'
														WHEN 2 THEN '$row[hor_pe2nac]'
														WHEN 3 THEN '$row[hor_pe2imp]'
														WHEN 4 THEN '$row[hor_pe2exp]'
														WHEN 5 THEN '$row[hor_pe2tr1]'
														WHEN 6 THEN '$row[hor_pe2tr2]'
													END AS hor_perfin 
											   FROM ".BASE_DATOS.".tab_despac_despac x 
										 INNER JOIN ".BASE_DATOS.".tab_despac_vehige y 
												 ON x.num_despac = y.num_despac 
										  LEFT JOIN ".BASE_DATOS.".tab_despac_perno2 z 
												 ON x.num_despac = z.num_despac 
											  WHERE x.fec_salida IS NOT NULL 
												AND x.fec_salida <= NOW() 
												AND (x.fec_llegad IS NULL OR x.fec_llegad = '0000-00-00 00:00:00')
												AND x.ind_planru = 'S' 
												AND x.ind_anulad = 'R'
												AND y.ind_activo = 'S' 
												AND y.cod_transp = '$row[cod_transp]' 
												AND x.cod_tipdes IN (1,2,3,4,5,6) 
												$mWhere
										   ORDER BY x.num_despac ASC, z.fec_creaci DESC
										) v 
								  WHERE v.hor_perini != '' 
									AND v.hor_perfin != '' 
									AND v.hor_perini IS NOT NULL 
									AND v.hor_perfin IS NOT NULL 
							   GROUP BY v.num_despac 
							) b 
				 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
						 ON b.cod_ciuori = d.cod_ciudad 
						AND b.cod_depori = d.cod_depart 
						AND b.cod_paiori = d.cod_paisxx 
				 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
						 ON b.cod_ciudes = e.cod_ciudad 
						AND b.cod_depdes = e.cod_depart 
						AND b.cod_paides = e.cod_paisxx 
				 INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
						 ON b.cod_depori = f.cod_depart 
						AND b.cod_paiori = f.cod_paisxx 
				 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
						 ON b.cod_depdes = g.cod_depart 
						AND b.cod_paides = g.cod_paisxx 
				 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
						 ON b.cod_conduc = h.cod_tercer 
					  WHERE a.fec_actual BETWEEN b.fec_perini AND b.fec_perfin 
						AND (b.fec_pernoc IS NULL OR b.fec_pernoc NOT BETWEEN b.fec_perini AND b.fec_perfin ) 
				   GROUP BY b.num_despac ";

			$mSql = "SELECT xx.cod_transp, COUNT(xx.cod_transp) AS num_cantid 
					   FROM ( $mSql ) xx 
				   GROUP BY xx.cod_transp ";
			$mConsult = new Consulta($mSql, self::$cConexion);
			$mData = $mConsult->ret_matrix('a');

			$mResult[$i] = $mData[0];
			$mResult[$i]['nom_transp'] = $row['nom_transp'];
			$mResult[$i]['nom_tipser'] = $row['nom_tipser'];
			$mResult[$i]['usr_asigna'] = $row['usr_asigna'];

			$i++;
		}

		return $mResult;
	}

	/*! \fn: getLisCiudadOrigne
	 *  \brief: ciudad de origen
	 *  \author: Edward Serrano
	 *  \date: 10/03/2017
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	public function getLisCiudadOrigne( ){
		$mData=(object) $_REQUEST;
		$mTransp = self::getTranspServic( $mData->mIndEtapa );
		$mResultPrc = self::getDespacPrcCargue($mTransp[0]);
		$mDespachos;
		if(sizeof($mResultPrc)>0){
			foreach ($mResultPrc as $keyDespacho => $valueDespacho) {
				$mDespachos.=$valueDespacho['num_despac'].",";
			}
			$mDespachos=substr($mDespachos ,0 , -1);
		}
		$mSelect = "SELECT a.cod_ciuori, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3)) AS nom_ciudad
                       FROM 
                       ".BASE_DATOS.".tab_despac_despac a,
                       ".BASE_DATOS.".tab_genera_ciudad b, 
                       ".BASE_DATOS.".tab_genera_depart d,
                       ".BASE_DATOS.".tab_genera_paises e
                   WHERE a.cod_ciuori = b.cod_ciudad AND
                       b.cod_depart = d.cod_depart AND
                       b.cod_paisxx = d.cod_paisxx AND
                       d.cod_paisxx = e.cod_paisxx AND
                       b.ind_estado = '1' AND
                       a.num_despac in ({$mDespachos})
                   GROUP BY 1 ORDER BY 2";
        $consulta = new Consulta( $mSelect, self::$cConexion);
	    $mCidOrig = $consulta -> ret_matriz('i');
	    $mResult;
	    foreach ($mCidOrig as $keyCiudad => $valueCiudad) {
	    	$mResult.="<option value='".$valueCiudad['cod_ciuori']."'>".$valueCiudad['nom_ciudad']."</option>";
	    }
	    echo $mResult;
	}

	/*! \fn: getLisProductos
	 *  \brief: productos
	 *  \author: Edward Serrano
	 *  \date: 10/03/2017
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: 
	 */
	public function getLisProductos( ){
		$mSelect = "SELECT cod_produc, nom_produc FROM ".BASE_DATOS.".tab_genera_produc WHERE ind_estado = '1' ORDER BY 2";
        $consulta = new Consulta( $mSelect, self::$cConexion);
	    $mProductos = $consulta -> ret_matriz('i');
	    return $mProductos;
	}

	/*! \fn: printInformPrc
	 *  \brief: Imprime la table para la etapa precargue
	 *  \author: Edward Serrano
	 *	\date: 10/03/2017
	 *	\date modified: dia/mes/año
	 *  \param: mIndEtapa  String  Etapa 
	 *  \param: mTittle  Matriz  Titulos y Colores
	 *  \param: mStyleCel  Matriz  filas y colunmas
	 *  \return:
	 */
	private function printInformPrc( $mIndEtapa, $mTittle, $mStyleCel )
	{
		$mTransp = self::getTranspServic( $mIndEtapa );
		$mLimitFor =  sizeof($mTittle[texto]);
		$mHtml = '';
		$j=1;

		#Dibuja las Filas por Transportadora
		for($i=0; $i<sizeof($mTransp); $i++)
		{
			#Trae los Despachos Segun Etapa
			switch ($mIndEtapa){
				case 'ind_segprc':
					$mDespac = self::getDespacPrcCargue( $mTransp[$i] );
					break;
			}
			#Si la Transportadora tiene Despachos
			if( $mDespac != false )
			{
				$mData = self::getTotalPrecargue( $mDespac, $mTransp[$i], 1 );
				$mData["enx_planta"] = sizeof(self::getDespacCargue( $mTransp[$i] ) );
			 
				$mHtml .= '<tr onclick="this.style.background=this.style.background==\'#CEF6CE\'?\'#eeeeee\':\'#CEF6CE\';">';
				$mHtml .= 	'<th class="classCell" nowrap="" align="left">'.$j.'</th>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mTransp[$i][nom_tipser].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mTransp[$i][nom_transp].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer" >'.sizeof($mDespac).'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_paradi] == 0 ? '' : 'onclick="showDetailBand(\'con_paradi\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_paradi].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_paraco] == 0 ? '' : 'onclick="showDetailBand(\'con_paraco\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_paraco].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_anulad] == 0 ? '' : 'onclick="showDetailBand(\'con_anulad\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_anulad].'</td>';
				
				//En planta nuevo, es todo lo que está en pestaña de cargue
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[enx_planta] == 0 ? '' : 'onclick="showDetailBand(\'enx_planta\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[enx_planta].'</td>';
				

				// Con planta pasa a ser oporteria, osea Porteria tiene las condiciones de planta
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_planta] == 0 ? '' : 'onclick="showDetailBand(\'con_planta\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_planta].'</td>';

				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_sinseg] == 0 ? '' : 'onclick="showDetailBand(\'con_sinseg\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_sinseg].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_tranpl] == 0 ? '' : 'onclick="showDetailBand(\'con_tranpl\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_tranpl].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_cnnlap] == 0 ? '' : 'onclick="showDetailBand(\'con_cnnlap\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_cnnlap].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_cnlapx] == 0 ? '' : 'onclick="showDetailBand(\'con_cnlapx\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_cnlapx].'</td>';
				#Nuevo campo a cargo de empresa
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_acargo] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_acargo].'</td>';

				$mHtml .= '</tr>';

				$mTotal[0] += sizeof($mDespac);
				$mTotal[1] += $mData[con_paradi];
				$mTotal[2] += $mData[con_paraco];
				$mTotal[3] += $mData[con_anulad];
				$mTotal[4] += $mData[enx_planta];
				$mTotal[5] += $mData[con_porter];
				$mTotal[6] += $mData[con_sinseg];
				$mTotal[7] += $mData[con_tranpl];
				$mTotal[8] += $mData[con_cnnlap];
				$mTotal[9] += $mData[con_cnlapx];
				$mTotal[10] += $mData[con_acargo];

				$j++;
			}
		}
		#Dibuja la Fila de los Totales
		$mHtml1  = '<tr>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="right" colspan="3">TOTALES:</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;" >'.$mTotal[0].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[1] == 0 ? '' : 'onclick="showDetailBand(\'con_paradi\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[1].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[2] == 0 ? '' : 'onclick="showDetailBand(\'con_paraco\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[2].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[3] == 0 ? '' : 'onclick="showDetailBand(\'con_anulad\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[3].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[4] == 0 ? '' : 'onclick="showDetailBand(\'con_planta\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[4].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[5] == 0 ? '' : 'onclick="showDetailBand(\'con_porter\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[5].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[6] == 0 ? '' : 'onclick="showDetailBand(\'con_sinseg\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[6].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[7] == 0 ? '' : 'onclick="showDetailBand(\'con_tranpl\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[7].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[8] == 0 ? '' : 'onclick="showDetailBand(\'con_cnnlap\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[8].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[9] == 0 ? '' : 'onclick="showDetailBand(\'con_cnlapx\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[9].'</th>';
		#Nuevo campo a cargo de empresa
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[10] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[10].'</th>';
		$mHtml1 .= '</tr>';

		#Dibuja la Tabla Completa
		$mHtml2  = '<table class="classTable" width="100%" cellspacing="0" cellpadding="0" align="center">';
		$mHtml2 .= 	'<tr>';
		for ($i=0; $i < $mLimitFor; $i++){
			if( $mStyleCel[BR][$i]==1 ){
						
					$mHtml2 .= '<th class="classHead bt '.$mTittle[style][$i].'" align="center" rowspan="'.($mStyleCel[ROW][$i]).'" colspan="'.($mStyleCel[COL][$i]).'">'.$mTittle[texto][$i].'</th>';
				$mHtml2 .= 	'</tr>';
				$mHtml2 .= 	'<tr>';
			}
			else
			{
				$mHtml2 .= '<th class="classHead bt '.$mTittle[style][$i].'" align="center" rowspan="'.($mStyleCel[ROW][$i]).'" colspan="'.($mStyleCel[COL][$i]).'">'.$mTittle[texto][$i].'</th>';
			}
		}
		$mHtml2 .= 	'<tr>';
		$mHtml2 .= $mHtml1;
		$mHtml2 .= $mHtml;
		$mHtml2 .= $mHtml1;

		$mHtml2 .= '</table>';
		$_SESSION['precargue']['general'] = $mHtml2;
		return utf8_decode($mHtml2);
	}

	/*! \fn: getTotalPrecargue
	 *  \brief: Calcula el tiempo por fecha de alarma para precargue
	 *  \author: Edward Serrano
	 *	\date: 12/03/2017
	 *	\date modified: dia/mes/año
	 *  \param: $mDespac   Matriz	Datos Despachos
	 *  \param: $mTransp   Array  	Informacion de la transportadora
	 *  \param: $mIndCant  Integer  0:Retorna Despachos con Tiempos; 1:Retorna Cantidades
	 *  \param: $mFiltro   String  	Filtro para el detallado por color, sinF = Todos
	 *  \param: $mColor	Array  	Colores por Etapa
	 *  \return: Matriz
	 */
	private function getTotalPrecargue( $mDespac, $mTransp, $mIndCant = 0, $mFiltro = NULL, $mColor = NULL )
	{
		$mTipValida = self::tipValidaTiempo( $mTransp );
		$fec_sisact = date("Y-m-d");
		$fec_sisHoraIni = date("Y-m-d")." ".($_REQUEST['hor_inicio']?$_REQUEST['hor_inicio']:" 00:00:01");
		$fec_sisHoraFin = date("Y-m-d")." ".($_REQUEST['hor_finxxx']?$_REQUEST['hor_finxxx']:" 23:59:59");
		if( $mIndCant == 1 )
		{ #Define Cantidades según estado
			$mResult["con_paradi"] = 0;//para el dia
			$mResult["con_paraco"] = 0;//para el corte
			$mResult["con_anulad"] = 0;//anulados
			$mResult["con_planta"] = 0;//en planta
			$mResult["con_porter"] = 0;//porteria
			$mResult["con_sinseg"] = 0;//SIN COMUNICACION
			$mResult["con_tranpl"] = 0;//transito a planta
			$mResult["con_cnnlap"] = 0;//con novedad no llegada a planta
			$mResult["con_cnlapx"] = 0;//con novedad llegada a planta
			$mResult["con_acargo"] = 0;//con novedad llegada a planta
		}
		else
		{
			$con_paradi = 0;
			$con_paraco = 0;
			$con_anulad = 0;
			$con_planta = 0;
			$con_porter = 0;
			$con_sinseg = 0;
			$con_tranpl = 0;
			$con_cnnlap = 0;
			$con_cnlapx = 0;
			$con_acargo = 0;
		}
		for ($i=0; $i < sizeof($mDespac); $i++)
		{
			if( $mDespac[$i]["fec_planea"] )
			{	#Despacho con Novedades
				$mDespac[$i]["tiempS"] = getDiffTime( $mDespac[$i]["fec_planea"], self::$cHoy ); #Script /lib/general/function.inc
			}
			if($mDespac[$i]["fec_citcar"])
			{	#Despacho Sin Novedades
				$mDespac[$i]["tiempo"] = getDiffTime( $mDespac[$i]["fec_citcar"]." ".$mDespac[$i]['hor_citcar'], self::$cHoy ); #Script /lib/general/function.inc
			} 

			if( $mIndCant == 1 )
			{ 
				#Valida si el deaspacho esta acargo de la empresa
				if($mDespac[$i]["ind_defini"] == 'SI' && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_acargo"]++;
					continue;
				}
				//if( strtotime($mDespac[$i]['fec_citcar']) >= strtotime(date("d-m-Y ",time()))  )
				 
				if( $mDespac[$i]['fec_citcar'] <= $fec_sisact && $mDespac[$i]["ind_anulad"] != "A" ) // Hora actual
				{
					//$mResult["con_paradi"]++;
				}
				if( strtotime(date( "Y-m-d H:i:s", strtotime($mDespac[$i]['fec_citcar']." ".$mDespac[$i]['hor_citcar'] ) )) >=  strtotime(date( "Y-m-d H:i:s", strtotime( $fec_sisHoraIni ) )) && strtotime(date( "Y-m-d H:i:s", strtotime($mDespac[$i]['fec_citcar']." ".$mDespac[$i]['hor_citcar'] ) )) <=  strtotime(date( "Y-m-d H:i:s", strtotime( $fec_sisHoraFin ) )) && $mDespac[$i]["ind_anulad"] != "A" ) // del día actual
				{
					$mResult["con_paraco"]++;
				}
				if($mDespac[$i]["ind_anulad"] == "A")
				{
					$mResult["con_anulad"]++;
				}
				elseif($mDespac[$i]["fec_plalle"]!="" && $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00")
				{
					$mResult["con_planta"]++;
				}else{
					switch ($mDespac[$i]['cod_estado']) {
						case '1':
							$mResult["con_porter"]++;
							break;
						case '2':
							$mResult["con_sinseg"]++;
							break;
						case '3':
							$mResult["con_tranpl"]++;
							break;
						case '4':
							$mResult["con_cnnlap"]++;
							break;
						case '5':
							$mResult["con_cnlapx"]++;
							break;
						default:
							$mResult["con_paradi"]++;
							break;
					}
				}
			}
			else
			{	
 

				$color;//color tiempo para cita de cargue
				$color2;//color tiempo para seguimiento
				if($mDespac[$i]["tiempo"] < -30 ){
					$color = $mColor[0];
				}
				elseif($mDespac[$i]["tiempo"] < 0 && $mDespac[$i]["tiempo"] >= -30){
					$color = $mColor[4];
				}
				elseif ($mDespac[$i]["tiempo"] < 31 && $mDespac[$i]["tiempo"] >= 0) {
					$color = $mColor[5];
				}
				elseif ($mDespac[$i]["tiempo"] < 61 && $mDespac[$i]["tiempo"] > 30) {
					$color = $mColor[3];
				}
				elseif ($mDespac[$i]["tiempo"] < 91 && $mDespac[$i]["tiempo"] > 60) {
					$color = $mColor[3];
				}
				elseif ($mDespac[$i]["tiempo"] > 90) {
					$color = $mColor[3];
				}
				#sequimineto
				if($mDespac[$i]["tiempS"] < -30 ){
					$color2 = $mColor[0];
				}
				elseif($mDespac[$i]["tiempS"] < 0 && $mDespac[$i]["tiempS"] >= -30){
					$color2 = $mColor[4];
				}
				elseif ($mDespac[$i]["tiempS"] < 31 && $mDespac[$i]["tiempS"] >= 0) {
					$color2 = $mColor[5];
				}
				elseif ($mDespac[$i]["tiempS"] < 61 && $mDespac[$i]["tiempS"] > 30) {
					$color2 = $mColor[3];
				}
				elseif ($mDespac[$i]["tiempS"] < 91 && $mDespac[$i]["tiempS"] > 60) {
					$color2 = $mColor[3];
				}
				elseif ($mDespac[$i]["tiempS"] > 90) {
					$color2 = $mColor[3];
				}
				#Valida si el despacho esta a cargo de la empresa
				if(($mFiltro == 'ind_acargo' || $mFiltro == 'sinF') && $mDespac[$i]["ind_defini"] == 'SI' && $mDespac[$i]["ind_anulad"] != "A" )
				{
					$mResult["con_acargo"][$con_acargo] = $mDespac[$i];
					$mResult["con_acargo"][$con_acargo]["color"] = $color;
					$mResult["con_acargo"][$con_acargo]["color2"] = $color2;
					$con_acargo++;
					continue;
				}
				/*if(($mFiltro == "con_paradi" || $mFiltro == 'sinF') && $mDespac[$i]['fec_citcar'] <= $fec_sisact && $mDespac[$i]["ind_anulad"] != "A" )
				{
					$mResult["con_paradi"][$con_paradi] = $mDespac[$i];
					$mResult["con_paradi"][$con_paradi]["color"] = $color;
					$mResult["con_paradi"][$con_paradi]["color2"] = $color2;
					$con_paradi++;
				}*/
				if(($mFiltro == "con_paraco" || $mFiltro == 'sinF') && strtotime(date( "Y-m-d H:i:s", strtotime($mDespac[$i]['fec_citcar']." ".$mDespac[$i]['hor_citcar'] ) )) >=  strtotime(date( "Y-m-d H:i:s", strtotime( $fec_sisHoraIni ) )) && strtotime(date( "Y-m-d H:i:s", strtotime($mDespac[$i]['fec_citcar']." ".$mDespac[$i]['hor_citcar'] ) )) <=  strtotime(date( "Y-m-d H:i:s", strtotime( $fec_sisHoraFin ) )) && $mDespac[$i]["ind_anulad"] != "A" )
				{
					$mResult["con_paraco"][$con_paraco] = $mDespac[$i];
					$mResult["con_paraco"][$con_paraco]["color"] = $color;
					$mResult["con_paraco"][$con_paraco]["color2"] = $color2;
					$con_paraco++;
				}
				if(($mFiltro == "con_anulad" || $mFiltro == 'sinF') && $mDespac[$i]["ind_anulad"] == "A" )
				{
					$mResult["con_anulad"][$con_anulad] = $mDespac[$i];
					$mResult["con_anulad"][$con_anulad]["color"] = $color;
					$mResult["con_anulad"][$con_anulad]["color2"] = $color2;
					$con_anulad++;
				}
				if(($mFiltro == "con_planta" || $mFiltro == 'sinF') && $mDespac[$i]["fec_plalle"]!="" && $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00" && $mDespac[$i]["ind_anulad"] != "A" )
				{
					$mResult["con_planta"][$con_planta] = $mDespac[$i];
					$mResult["con_planta"][$con_planta]["color"] = $color;
					$mResult["con_planta"][$con_planta]["color2"] = $color2;
					$con_planta++;
				}

				 
				/*
					 
					switch ($mDespac[$i]['cod_estado']) { 
							case '1':
								if( ($mFiltro == "con_porter" || $mFiltro == 'sinF') && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A" )
								{
									$mResult["con_porter"] = self::setContaEstado($mFiltro ,$mDespac[$i], $mResult,$color, $color2,$con_porter++ );
								}
							break;
							case '2':
								if( ($mFiltro == "con_sinseg" || $mFiltro == 'sinF') && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A" )
								{
									$mResult["con_sinseg"] = self::setContaEstado($mFiltro,$mDespac[$i], $mResult,$color, $color2,$con_sinseg++ );
								}
							break;
							case '3':
								if( ($mFiltro == "con_tranpl" || $mFiltro == 'sinF' )&& ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A" )
								{
									$mResult["con_tranpl"] = self::setContaEstado($mFiltro,$mDespac[$i], $mResult,$color, $color2,$con_tranpl++ );
									
								}
							break;
							case '4':
								if( ($mFiltro == "con_cnnlap" || $mFiltro == 'sinF' )&& ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A" )
								{
									$mResult["con_cnnlap"] = self::setContaEstado($mFiltro,$mDespac[$i], $mResult,$color, $color2,$con_cnnlap++ );
								}
							break;
							case '5':
								if( ($mFiltro == "con_cnlapx" || $mFiltro == 'sinF' )&& ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A" )
								{
									$mResult["con_cnlapx"] = self::setContaEstado($mFiltro,$mDespac[$i], $mResult,$color, $color2,$con_cnlapx++ );
								}
							break;					
							default:
								$mResult["con_paradi"] = self::setContaEstado("con_paradi" ,$mDespac[$i], $mResult,$color, $color2,$con_paradi++ );
							break;
					}
				 */
				
				if(($mFiltro == "con_porter" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == "1" && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A")
				{
					$mResult["con_porter"][$con_porter] = $mDespac[$i];
					$mResult["con_porter"][$con_porter]["color"] = $color;
					$mResult["con_porter"][$con_porter]["color2"] = $color2;
					$con_porter++;
				}
				if(($mFiltro == "con_sinseg" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == "2" && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_sinseg"][$con_sinseg] = $mDespac[$i];
					$mResult["con_sinseg"][$con_sinseg]["color"] = $color;
					$mResult["con_sinseg"][$con_sinseg]["color2"] = $color2;
					$con_sinseg++;
				}
				if(($mFiltro == "con_tranpl" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == "3" && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_tranpl"][$con_tranpl] = $mDespac[$i];
					$mResult["con_tranpl"][$con_tranpl]["color"] = $color;
					$mResult["con_tranpl"][$con_tranpl]["color2"] = $color2;
					$con_tranpl++;
				}
				if(($mFiltro == "con_cnnlap" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == "4" && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_cnnlap"][$con_cnnlap] = $mDespac[$i];
					$mResult["con_cnnlap"][$con_cnnlap]["color"] = $color;
					$mResult["con_cnnlap"][$con_cnnlap]["color2"] = $color2;
					$con_cnnlap++;
				}
				if(($mFiltro == "con_cnlapx" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == "5" && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_cnlapx"][$con_cnlapx] = $mDespac[$i];
					$mResult["con_cnlapx"][$con_cnlapx]["color"] = $color;
					$mResult["con_cnlapx"][$con_cnlapx]["color2"] = $color2;
					$con_cnlapx++;
				}

				if(($mFiltro == "con_paradi" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == NULL && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_paradi"][$con_paradi] = $mDespac[$i];
					$mResult["con_paradi"][$con_paradi]["color"] = $color;
					$mResult["con_paradi"][$con_paradi]["color2"] = $color2;
					$con_paradi++;
				}
				
			
			}
			
		}
 
		return $mResult;
	}


	/*! \fn: setContaEstado
	 *  \brief: Calcula el tiempo por fecha de alarma para precargue
	 *  \author: Edward Serrano
	 *	\date: 12/03/2017
	 *	\date modified: dia/mes/año
	 *  \param: $mDespac   Matriz	Datos Despachos
	 *  \param: $mTransp   Array  	Informacion de la transportadora
	 *  \param: $mIndCant  Integer  0:Retorna Despachos con Tiempos; 1:Retorna Cantidades
	 *  \param: $mFiltro   String  	Filtro para el detallado por color, sinF = Todos
	 *  \param: $mColor	Array  	Colores por Etapa
	 *  \return: Matriz
	 */
	private function setContaEstado($mFiltro , $mDespac, $mResult, $color, $color2, $mContador )
	{
		switch ($mFiltro) {
			case 'con_porter':				
					$mResult["con_porter"][$mContador] = $mDespac ;
					$mResult["con_porter"][$mContador]["color"] = $color;
					$mResult["con_porter"][$mContador]["color2"] = $color2;
					 
			break;
			case 'con_sinseg': 
					$mResult["con_sinseg"][$mContador] = $mDespac;
					$mResult["con_sinseg"][$mContador]["color"] = $color;
					$mResult["con_sinseg"][$mContador]["color2"] = $color2;
					 
			break;
			case 'con_tranpl':
					$mResult["con_tranpl"][$mContador] = $mDespac;
					$mResult["con_tranpl"][$mContador]["color"] = $color;
					$mResult["con_tranpl"][$mContador]["color2"] = $color2;
					 
			break;
			case 'con_cnnlap':
					$mResult["con_cnnlap"][$mContador] = $mDespac;
					$mResult["con_cnnlap"][$mContador]["color"] = $color;
					$mResult["con_cnnlap"][$mContador]["color2"] = $color2;
					 
			break;
			case 'con_cnlapx':
					$mResult["con_cnlapx"][$mContador] = $mDespac;
					$mResult["con_cnlapx"][$mContador]["color"] = $color;
					$mResult["con_cnlapx"][$mContador]["color2"] = $color2;
					 
			break;
			default:
					$mResult["con_paradi"][$mContador] = $mDespac;
					$mResult["con_paradi"][$mContador]["color"] = $color;
					$mResult["con_paradi"][$mContador]["color2"] = $color2;
					 
			break;

		}
		return $mResult;
	}

	/*! \fn: getFormValidaProNove
	 *  \brief: Genera formulario de validacion de novedades NEM
	 *  \author: Edward Serrano
	 *	\date: 11/05/2017
	 *	\date modified: dia/mes/año
	 *  \return: 
	 */
	public function getFormValidaProNove()
	{
		try
		{
			#array de titulos tabla novedades a gestionar
			#'nombre'=>'tamano o dimemcion en width'												
			$mTitulosNG = array('Seleccione' =>'10%' ,
								'#' =>'10%' ,
								'Ubicacion' =>'20%' ,
								'Novedad' =>'40%' ,
								'Fecha y hora novedad' =>'20%' ,
			 );
			$mHtml = new Formlib(2);
			$mHtml->Hidden(array( "name" => "cod_novedaSol", "id"=>"cod_novedaSolID", "value"=>""));
			$mHtml->Hidden(array( "name" => "ind_soltie", "id"=>"ind_soltieID", "value"=>"0"));
			$mHtml->Hidden(array( "name" => "cod_transp", "id"=>"cod_transpID", "value"=>($_REQUEST['cod_transp']!=""?$_REQUEST['cod_transp']:self::getTranspUsuari()[0][0])));
			$mHtml->OpenDiv("id:solNovedadesNem");
				$mHtml->OpenDiv("id:getNoveda");
					$mHtml->Table("tr",array("class"=>"displayDIV2"));
						$mHtml->Label( "SELECCIONE LA NOVEDAD A REGISTRAR", array("align"=>"center", "width"=>"100%", "class"=>"CellHead", "colspan"=>"2") );
						$mHtml->CloseRow();		
						$mHtml->Row();
							$mHtml->Label( "* NOVEDAD :",  array("align"=>"right", "class"=>"celda_titulo", "width" => "50%") );
		                	$mHtml->Input(array("value"=>"","name" => "sol_mNoveda", "id" => "sol_mNovedaID", "width" => "50%", "onkeyup"=>"getNovedadAutocomple()"));
						$mHtml->CloseRow();
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
				$mHtml->OpenDiv("id:NovedaGestion");
					$mHtml->Table("tr",array("class"=>"displayDIV2"));
						$mHtml->Label( "NOVEDADES A GESTIONAR", array("align"=>"center", "width"=>"100%", "class"=>"CellHead", "colspan"=>(sizeof($mTitulosNG))) );
						$mHtml->CloseRow();		
						$mHtml->Row();
							#recorro los titulos a pintar
							foreach ($mTitulosNG as $kTituloNG => $vTituloNG) 
							{
								$mHtml->Label( $kTituloNG,  array("align"=>"right", "class"=>"celda_titulo", "width" => $vTituloNG) );
							}
						$mHtml->CloseRow();
							#Recorro las novedades que se encuentan sin solucion
							$mNovedadesMen = self::getNovedadNem($_REQUEST['despac']);
							if(sizeof($mNovedadesMen)>0)
							{
								foreach ($mNovedadesMen as $kNovedaNEM => $vNovedaNEM) 
								{	$mHtml->Row();
									$valueNem = $vNovedaNEM['num_consec']."|".$vNovedaNEM['num_despac']."|".$vNovedaNEM['cod_noveda'];
										$mHtml->CheckBox(array('name' => 'nov_nem'.$kNovedaNEM+1, 'value' => $valueNem, "class"=>"celda_info", "width" => "10%" ));
										$mHtml->Label( $kNovedaNEM+1,  array("align"=>"left", "class"=>"celda_info", "width" => "10%") );
										$mHtml->Label( self::getNomSitio($vNovedaNEM['cod_sitiox'])[0]['nom_sitiox'],  array("align"=>"left", "class"=>"celda_info", "width" => "20%") );
										$mHtml->Label( $vNovedaNEM['nom_noveda'],  array("align"=>"left", "class"=>"celda_info", "width" => "40%") );
										$mHtml->Label( $vNovedaNEM['fec_noveda'],  array("align"=>"left", "class"=>"celda_info", "width" => "20%") );
									$mHtml->CloseRow();
								}
							}
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
				$mHtml->OpenDiv("id:TitulosNoveProto");
					$mHtml->Table("tr");	
						$mHtml->Label( "ASIGNACION DE NOVEDAD", array("align"=>"center", "width"=>"100%", "class"=>"CellHead") );
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
				$mHtml->OpenDiv("id:NovedaProtocolo");
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			echo $mHtml->MakeHtml();
		}
		catch(Excpetion $e)
		{
			echo "Error en la funcion getFormValidaProNove: ", $e->getMessage(), "\n";
		}
	}
	/*! \fn: getNovedadAutocomple
	* \brief: Trae las novedades actuales
	* \author: Edward Serrano
	* \date: 17/04/2017
	* \date modified: dia/mes/año
	* \param: paramatro
	* \return valor que retorna
	*/
	public function getNovedadAutocomple()
	{
		try
		{
			#Busco el tipo de destino y la transporadora asociada
			$mSql = "SELECT a.cod_tipdes, b.cod_transp
	                   FROM ".BASE_DATOS.".tab_despac_despac a 
	             INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
							 ON a.num_despac = b.num_despac
	                  WHERE a.num_despac = '".$_REQUEST["num_despac"]."' ";
	        $mConsult = new Consulta($mSql, self::$cConexion);
	        $mData = $mConsult->ret_matrix('a');
	        #Busco el servicio asociado a la tranportadora
	        $query = "SELECT tie_conurb, tie_contro, cod_tipser " .
                "FROM " . BASE_DATOS . ".tab_transp_tipser " .
                "WHERE cod_transp='".$mData[0]["cod_transp"]."' AND 
			                num_consec= (SELECT MAX(num_consec) FROM " . BASE_DATOS . ".tab_transp_tipser
			                						 WHERE cod_transp='" . $mData[0]["cod_transp"] . "') ";
	        $consulta = new Consulta($query, self::$cConexion);
	        $transpor = $consulta->ret_matriz();
	        #Busco las novedades existentes que no esten inactivas
			$query = " SELECT a.cod_noveda, UPPER( CONCAT( CONVERT( a.nom_noveda USING utf8), 
						  '', if (a.nov_especi = '1', '(NE)', '' ), 
						  if( a.ind_alarma = 'S', '(GA)', '' ), 
						  if( a.ind_manala = '1', '(MA)', '' ),
						  if( a.ind_tiempo = '1', '(ST)', '' ) )) AS label , 
						  a.ind_tiempo
				   FROM " . BASE_DATOS . ".tab_genera_noveda a
				   INNER JOIN ".BASE_DATOS.".tab_perfil_noveda b
				   ON a.cod_noveda = b.cod_noveda
				   WHERE 1 = 1 AND a.ind_visibl = '1' ";
				   
	        $query .=" AND a.cod_noveda NOT IN (6,69) AND b.cod_perfil=".$_SESSION["datos_usuario"]["cod_perfil"];
	        if ($_SESSION["datos_usuario"]["cod_perfil"] != COD_PERFIL_SUPERUSR && $_SESSION["datos_usuario"]["cod_perfil"] != COD_PERFIL_ADMINIST && $_SESSION["datos_usuario"]["cod_perfil"] != COD_PERFIL_SUPEFARO)
			{
				if( $_SESSION["datos_usuario"]["cod_perfil"]  != '689' && $_SESSION["datos_usuario"]["cod_perfil"]  != '77' )
		            $query .=" AND a.cod_noveda !='" . CONS_NOVEDA_ACAEMP . "' ";
			}
		    if ($transpor[0][2] == '1')
		    {
		        $query .=" AND a.cod_noveda !='" . CONS_NOVEDA_ACAFAR . "' ";
		    }
		        
		    $query .=" AND a.nom_noveda LIKE '%".$_REQUEST['term']."%' OR a.cod_noveda LIKE '%".$_REQUEST['term']."%' GROUP BY (a.cod_noveda) ORDER BY 2 ASC";
		    $consulta = new Consulta($query, self::$cConexion);
		    $mResult = $consulta->ret_matriz();

		    if( $_REQUEST['term'] )
		    {
		        $mNovedades = array();
		    	for($i=0; $i<sizeof( $mResult ); $i++){
		            $mTxt = $mResult[$i]['cod_noveda']." - ".utf8_decode($mResult[$i]['label']);
		            $mNovedades[] = array('value' => utf8_decode($mResult[$i]['label']), 'label' => $mTxt, 'id' => $mResult[$i]['cod_noveda'] );
		        }
		        echo json_encode( $mNovedades );
		    }
		    else
		    {
		        return $mResult;
		    }
	        
	        
		}
		catch(Exception $e)
		{
			echo "Error en la funcion getNovedad: ", $e->getMessage();
		}
	}

	/*! \fn: getInfNovedad
	* \brief: Trae las  informacion basica de las novedade
	* \author: Edward Serrano
	* \date: 12/05/2017
	* \date modified: dia/mes/año
	* \param: paramatro
	* \return valor que retorna
	*/
	private function getInfNovedad($cod_noveda)
	{
		try
		{
			$query = " SELECT a.cod_noveda, UPPER( CONCAT( CONVERT( a.nom_noveda USING utf8), 
						  '', if (a.nov_especi = '1', 'NE/', '' ), 
						  if( a.ind_alarma = 'S', 'GA/', '' ), 
						  if( a.ind_manala = '1', 'MA/', '' ),
						  if( a.ind_tiempo = '1', 'ST/', '' ) )) AS label , 
						  a.ind_tiempo
				   FROM " . BASE_DATOS . ".tab_genera_noveda a
				   INNER JOIN ".BASE_DATOS.".tab_perfil_noveda b
				   ON a.cod_noveda = b.cod_noveda
				   WHERE 1 = 1 AND a.ind_visibl = '1' AND a.cod_noveda=".$cod_noveda." GROUP BY (a.cod_noveda) ORDER BY 2 ASC";

			$consulta = new Consulta($query, self::$cConexion);
		    return $mResult = $consulta->ret_matriz();
		}
		catch(Exception $e)
		{
			echo "Error en la funcion getNovedad: ", $e->getMessage();
		}
	}

	/*! \fn: getProtoNovedad
	* \brief: consulta si la noveda tiene protocolo
	* \author: Edward Serrano
	* \date: 12/05/2017
	* \date modified: dia/mes/año
	* \param: paramatro
	* \return valor que retorna
	*/
	private function getProtoNovedad($cod_noveda)
	{
		try
		{
			$query = " SELECT a.des_protoc, a.tex_protoc
				   FROM " . BASE_DATOS . ".tab_genera_protoc a
				   INNER JOIN ".BASE_DATOS.".tab_noveda_protoc b
				   ON a.cod_protoc = b.cod_protoc
				   WHERE 1 = 1 AND a.ind_activo = '1' AND b.cod_noveda=".$cod_noveda;//." AND a.cod_respon=".self::getRepon()[0]['cod_respon'];

			$consulta = new Consulta($query, self::$cConexion);
		    return $mResult = $consulta->ret_matriz();
		}
		catch(Exception $e)
		{
			echo "Error en la funcion getNovedad: ", $e->getMessage();
		}
	}
	
	/*! \fn: formNovedadGestion
	* \brief: pinta complemento del formulario para la gestio de la nocedad
	* \author: Edward Serrano
	* \date: 12/05/2017
	* \date modified: dia/mes/año
	* \param: paramatro
	* \return valor que retorna
	*/
	private function formNovedadGestion()
	{
		try
		{
			$mHtml = new Formlib(2);
			#protocolos de las novedades
			$mProtocolosNove = self::getProtoNovedad($_REQUEST['cod_noveda']); 
			$mHtml->OpenDiv("id:formGestionNovedad");
				$mHtml->OpenDiv("id:contProtocolos");
		        $mHtml->CloseDiv();
		        $mHtml->OpenDiv("id:contNovedaNem");
		        	$mHtml->Table("tr");	
						$mHtml->Label( "DATOS COMPLEMENTARIOS", array("align"=>"center", "width"=>"100%", "class"=>"CellHead") );
					$mHtml->CloseTable('tr');
					$mHtml->Table("tr",array("class"=>"displayDIV2"));
			            	//$mHtml->SetBody('<tr style="display:none;" class="NovedadPerno">');
							$mHtml->Label("* Fecha: ", array("class"=>"cellInfo1 fecha"));
							$mHtml->Input( array("name"=>"fec", "id"=>"fecID", "readonly"=>"true", "class"=>"cellInfo1", "width"=>"50%", "value"=>(Date("Y-m-d"))) );
							//$mHtml->SetBody('</tr>');
						$mHtml->CloseRow();
			            $mHtml->Row();
			            	$mHtml->Label("* Hora: ", array("class"=>"cellInfo1 hora"));
							$mHtml->Input( array("name"=>"hor", "id"=>"horID", "readonly"=>"true", "class"=>"cellInfo1", "width"=>"50%", "value"=>(Date("H:i"))) );
			            $mHtml->CloseRow();
			            $mHtml->Row();
			            	$mHtml->Label("* Puesto de control: ", array("class"=>"cellInfo1", "width"=>"50%"));
							$mHtml->Select2( array_merge(self::$cNull, self::getPCPendientes($_REQUEST['num_despac'])), array("name"=>"cod_contro", "id"=>"cod_controID", "class"=>"cellInfo1", "width"=>"50%", "obl"=>"1") );
			            $mHtml->CloseRow();
			            $mHtml->Row();
			            	$mHtml->Label("* Antes/Sitio: ", array("class"=>"cellInfo1", "width"=>"50%"));
							$mHtml->Select2( array(array("A","Antes"),array("S","Sitio")), array("name"=>"ind_valsit", "id"=>"ind_valsitID", "class"=>"cellInfo1", "width"=>"50%", "obl"=>"1") );
			            $mHtml->CloseRow();
			            if($_REQUEST['ind_soltie']==1)
			            {
				            $mHtml->Row();
								$mHtml->Label("* Fecha de novedad: ", array("class"=>"cellInfo1 fecha"));
								$mHtml->Input( array("name"=>"fec_noveda", "id"=>"fec_novedaID", "readonly"=>"true", "class"=>"cellInfo1", "width"=>"50%") );
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->Label("* Hora de novedad: ", array("class"=>"cellInfo1 fecha"));
								$mHtml->Input( array("name"=>"hor_noveda", "id"=>"hor_novedaID", "readonly"=>"true", "class"=>"cellInfo1", "width"=>"50%") );
							$mHtml->CloseRow();
			            }
			            $mHtml->Row();
			            	$mHtml->Label( "OBSERVACION: ",  array("align"=>"right", "class"=>"cellInfo1", "width" => "10%", "colspan"=>"1") );
			            $mHtml->SetBody("<td class='cellInfo1'>
			            					<textarea name='obs' id='obsID' onkeyup='UpperText( $(this) )' cols='20' Rows='4'></textarea>
			            					<div style='font-family:Arial,Helvetica,sans-serif; font-size: 11px;' id='counter'></div>
			            				</td>");
			            $mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Button( array("value"=>"Solucionar", "id"=>"btnContNoveID","name"=>"btnContNove", "class"=>"crmButton small save", "align"=>"center", "colspan"=>"2","onclick"=>"SolucionNovNem()") );
						$mHtml->CloseRow();
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			echo $mHtml->MakeHtml();
		}
		catch(Exception $e)
		{
			echo "Error en la funcion getNovedad: ", $e->getMessage();
		}
	}

	/*! \fn: getRepon
	 *  \brief: Busca el codigo de responsable asigando
	 *  \author: Edward Serrano
	 *  \date:  12/05/2017
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Array
	 */
	private function getRepon()
	{
		$mSql = "SELECT a.cod_respon 
				   FROM ".BASE_DATOS.".tab_genera_respon a 
			 INNER JOIN ".BASE_DATOS.".tab_genera_perfil b 
					 ON a.cod_respon = b.cod_respon 
				  WHERE b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
		$mConsult = new Consulta($mSql, self::$cConexion);
		return $mData = $mConsult->ret_matrix('a');
	}

	/*! \fn: getNovedadNem
	 *  \brief: Busca la novedad registrada en la tabla tab_despac_novnem
	 *  \author: Edward Serrano
	 *  \date:  12/05/2017
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Array
	 */
	private function getNovedadNem($num_despac, $etapa=NULL)
	{
		$mSql = "SELECT 
						a.num_consec,	a.num_despac,	a.cod_noveda,
						a.ind_soluci,	a.fec_soluci,	a.usr_soluci,
						a.nov_soluci,	a.obs_soluci,	a.ind_realiz,
						a.usr_creaci,	a.fec_creaci, 	a.cod_contro,
						a.cod_sitiox, 	b.cod_etapax,	b.nom_noveda,
						c.fec_contro AS fec_noveda
					FROM ".BASE_DATOS.".tab_despac_novnem a 
						INNER JOIN ".BASE_DATOS.".tab_genera_noveda b
						ON a.cod_noveda = b.cod_noveda
						LEFT JOIN ".BASE_DATOS.".tab_despac_contro c
						ON a.cod_noveda = c.cod_noveda AND a.num_despac IN (".$num_despac.")
				  WHERE a.num_despac IN (".$num_despac.") AND a.ind_soluci=0 ";

				  if($etapa != NULL)
				  {
				  		$mSql.= " AND b.cod_etapax IN ( ".$etapa." ) GROUP BY (a.num_despac)"; 	
				  }
				  else
				  {
				  		$mSql.= " GROUP BY (a.num_consec)"; 	
				  }

		$mConsult = new Consulta($mSql, self::$cConexion);
		return $mData = $mConsult->ret_matrix('a');
	}

	/*! \fn: getNomSitio
	 *  \brief: Busca el nombre del sitio
	 *  \author: Edward Serrano
	 *  \date:  19/05/2017
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Array
	 */
	private function getNomSitio($cod_sitiox)
	{
		$mSql = "SELECT a.nom_sitiox,	a.cod_sitiox
					FROM ".BASE_DATOS.".tab_despac_sitio a 
				  WHERE a.cod_sitiox = ".$cod_sitiox." ";

		$mConsult = new Consulta($mSql, self::$cConexion);
		return $mData = $mConsult->ret_matrix('a');
	}

	/*! \fn: getConteoNem
	 *  \brief: Cantidad de novedades NEM (Novedades Enviadas Movil)
	 *  \author: Edward Serrano
	 *  \date:  10/05/2017
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Array
	 */
	public function getConteoNem($etapa, $mTransp)
	{
		try
		{	$mViewPr = self::getView('jso_plarut');
			$mResult = NULL;
			if($mViewPr->ind_novnem->ind_visibl == 1)
			{
				#identifico la transportadora
				#Consulto el tipo de usuario
				if(self::getTranspCargaControlador() != NULL )
				{
					$mTransp = self::getTranspCargaControlador();
				}
				else if(!is_array($mTransp))
				{	
					#convierto la cadena en array
					$mTransp = explode(',', $mTransp);
					#limpio los campos del array vacio
					$mTransp = array_filter($mTransp,'strlen');
					#elimino la posicion 0 de array la caul llega vacia
					unset($mTransp[0]);
					#Combierto el array en cadena
					$mTransp = join( ',', $mTransp);
				}
				else if(sizeof($mTransp)>1)
				{
					$mTransp = join( ',', GetColumnFromMatrix( $mTransp, '0' ) );
				}
				else
				{
					$mTransp = $mTransp[0][0];
				}

				#Actulizacion 25-10-2017 para perfiles diferentes a supervisores, controladores y administradores 'Soporte'
				#1=administrador, 7 = Controlador de trafico C.L. FARO, 8=Supervisor C L. FARO
				if(self::$cSession["cod_perfil"] != 1 && self::$cSession["cod_perfil"] != 7 && self::$cSession["cod_perfil"] != 8)
				{
					$mSql = "SELECT clv_filtro AS cod_transp
					   				FROM ".BASE_DATOS.".tab_aplica_filtro_perfil 
					   			WHERE cod_perfil= ".self::$cSession["cod_perfil"];
					$mConsult = new Consulta( $mSql, self::$cConexion );
					$mTransp = $mConsult -> ret_matrix('a');
					$mTransp = $mTransp[0]['cod_transp'];
				}
				#Filtro por las estas existentes
				$mDespac = NULL;
				switch ($etapa) {
					case '0':
						#General
						break;

					case '1':
						#Precargue
						$mDespac = join( ',', GetColumnFromMatrix( self::getDespacEtapaPrecar($mTransp), 'num_despac' ) );
						break;

					case '2':
						#Cargue
						$mDespac = join( ',', GetColumnFromMatrix( self::getDespacEtapaCargue($mTransp), 'num_despac' ) );
						break;

					case '3':
						#Transito
					
						$mDespac = join( ',', GetColumnFromMatrix( self::getDespacEtapaTransi($mTransp), 'num_despac' ) );
						break;
					
					case '4,5':
						#Descargue
						$mDespac = join( ',', GetColumnFromMatrix( self::getDespacEtapaDescar($mTransp), 'num_despac' ) );
						break;
					case '7':
						#Descargue
						$mDespac = join( ',', GetColumnFromMatrix( self::getDespacEtapaContro($mTransp), 'num_despac' ) );
						break;
					
				}

				if($mDespac != NULL)
				{
					$cantidad = self::getNovedadNem($mDespac, $etapa);
					$mResult = (sizeof($cantidad)>0?sizeof($cantidad):0);
				}
				#Cuando se realiza el llamado desde ajax se realiza un echo para ver la informacion
				#de lo contrario se retorna la cadena para ser concatenada.
			}
			if(isset($_REQUEST['Ajax']))
			{
				ob_clean();
				echo $mResult;
			}
			else
			{
				return $mResult;
			}
		}
		catch(Exception $e)
		{
			echo "Error en la funcion getNovedad: ", $e->getMessage();
		}
			
	}

	/*! \fn: getDespacEtapaCargue
	 *  \brief: Busca los despacho que se encuentran en la etapa de cargue
	 *  \author: Edward Serrano
	 *  \date:  15/05/2017
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Array
	 */
	private function getDespacEtapaCargue($mTrans)
	{
		try
		{
			$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat 
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND ( xx.fec_salida IS NOT NULL   )
						".(strlen($mTrans)>0?"AND yy.cod_transp IN (".$mTrans.")":"");

			$mConsult = new Consulta( $mSql, self::$cConexion );
			return $mDespac = $mConsult -> ret_matrix('a');
		}
		catch(Exception $e)
		{
			echo "Error en la funcion getNovedad: ", $e->getMessage();
		}
	}

	/*! \fn: getDespacEtapaCargue
	 *  \brief: Busca los despacho que se encuentran en la etapa de precargue
	 *  \author: Edward Serrano
	 *  \date:  15/05/2017
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Array
	 */
	private function getDespacEtapaPrecar($mTrans)
	{
		try
		{
			$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat 
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad in ('R', 'A')
						AND yy.ind_activo = 'S' 
						".(strlen($mTrans)>0?"AND yy.cod_transp IN (".$mTrans.")":"");
			$mConsult = new Consulta( $mSql, self::$cConexion );
			return $mDespac = $mConsult -> ret_matrix('a');
		}
		catch(Exception $e)
		{
			echo "Error en la funcion getNovedad: ", $e->getMessage();
		}
	}

	/*! \fn: getDespacEtapaDescar
	 *  \brief: Busca los despacho que se encuentran en la etapa de descargue
	 *  \author: Edward Serrano
	 *  \date:  15/05/2017
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Array
	 */
	private function getDespacEtapaDescar($mTrans)
	{
		try
		{
			$mDespacCargue = self::getDespacCargue( $mTrans, 'list', true );

			$mDespacCargue = ($mDespacCargue == NULL ? '0' : $mDespacCargue );
		
			#Despachos en ruta que ya finalizaron etapa Cargue
			$mSql = "	 SELECT xx.num_despac
						   FROM ".BASE_DATOS.".tab_despac_despac xx 
					 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
							 ON xx.num_despac = yy.num_despac 
							AND xx.fec_salida IS NOT NULL 
							AND xx.fec_salida <= NOW() 
							AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
							AND xx.ind_planru = 'S' 
							AND xx.ind_anulad = 'R'
							AND yy.ind_activo = 'S' 
							".(strlen($mTrans)>0?"AND yy.cod_transp IN (".$mTrans.")":"")."
							AND xx.num_despac NOT IN ( {$mDespacCargue} ) ";
			$mConsult = new Consulta( $mSql, self::$cConexion );
			return $mDespac = $mConsult -> ret_matrix('a');
		}
		catch(Exception $e)
		{
			echo "Error en la funcion getNovedad: ", $e->getMessage();
		}
	}

	/*! \fn: getDespacEtapaTransi
	 *  \brief: Busca los despacho que se encuentran en la etapa de transito
	 *  \author: Edward Serrano
	 *  \date:  15/05/2017
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Array
	 */
	private function getDespacEtapaTransi($mTrans)
	{
		try
		{
			$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat 
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND ( xx.fec_salida IS NOT NULL  )
						".(strlen($mTrans)>0?"AND yy.cod_transp IN (".$mTrans.")":"");
			$mConsult = new Consulta( $mSql, self::$cConexion );
			return $mDespac = $mConsult -> ret_matrix('a');
		}
		catch(Exception $e)
		{
			echo "Error en la funcion getNovedad: ", $e->getMessage();
		}
	}

	/*! \fn: getDespacEtapaContro
	 *  \brief: Busca los despacho que se encuentran en la etapa de Control Operación
	 *  \author: Luis Manrique
	 *  \date:  9/12/2019
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Array
	 */
	private function getDespacEtapaContro($mTrans)
	{
		try
		{
			$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat 
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND ( xx.fec_salida IS NOT NULL  )
						".(strlen($mTrans)>0?"AND yy.cod_transp IN (".$mTrans.")":"");
			$mConsult = new Consulta( $mSql, self::$cConexion );
			return $mDespac = $mConsult -> ret_matrix('a');
		}
		catch(Exception $e)
		{
			echo "Error en la funcion getNovedad: ", $e->getMessage();
		}
	}

	/*! \fn: getPCPendientes
	 *  \brief: Trae los puestos de control pendientes de un despacho
	 *  \author: Ing. Fabian Salinas
	 *  \date: 14/03/2016
	 *  \date modified: dd/mm/aaaa
	 *  \param: mNumDes  Integer  Numero del despacho
	 *  \return: Matriz
	 */
	private function getPCPendientes( $mNumDes, $contro= NULL ){
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
					".($contro != NULL ? "AND a.cod_contro = '".$contro."' " : "" )."
			   ORDER BY c.val_duraci ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mConsult -> ret_matrix('i');
	}
	
	/*! \fn: AlmcenarSolucionNem
	 *  \brief: Almacena las solucion de las novedades moviles (NEM)
	 *  \author: Edward Serrano
	 *  \date:  18/05/2017
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Array
	 */
	private function AlmcenarSolucionNem()
	{
		try
		{
			//indicador solucion
			$_REQUEST['ind_activo_'] = 'S';

			//Genero la nueva novedad
     	 	@include_once( "../despac/InsertNovedad.inc" );
			$mInsNoveda = new InsertNovedad($_REQUEST['cod_servic'], 3, $_SESSION['codigo'], self::$cConexion);

			$mFecAct = date("Y-m-d H:i:s");
			$mData = array();
			$mDespac = self::getDataAddDespac( $_REQUEST['num_despac'], $_REQUEST['cod_contro'] );
			$validacion = true;

			if(isset($_REQUEST['fec_noveda']) && isset($_REQUEST['hor_noveda']))
			{
				$mTieAdicio = self::calcularMinAdi($_REQUEST['fec_noveda']." ".$_REQUEST['hor_noveda'].":00", $mFecAct);
			}
			else
			{
				$mTieAdicio = 0;
			}

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
			$mData['fecact'] = $mFecAct;
			$mData['fecnov'] = $mFecAct;
			$mData['usuari'] = $_SESSION['datos_usuario']['cod_usuari'];
			$mData['nittra'] = $mDespac['cod_transp'];
			$mData['indsit'] = '1';
			$mData['sitio'] = $mDespac['nom_contro'];
			$mData['tie_ultnov'] = $mTieUltNov;
			$mData['tiem'] = '0';
			$mData['rutax'] = $mDespac['cod_rutasx'];
			 $mData['noveda'] = $_REQUEST["cod_noveda"];
			$mData['tieadi'] = $mTieAdicio;
			$mData['observ'] = $_REQUEST["obs"];
			//opcional para el envio del correo en novedades especiales
			$mData['rutpla'] = '../../' . DIR_APLICA_CENTRAL . '/planti/pla_noveda_especi.html';
			#verifico si la noveda genera correo y si genera lleno el $_REQUEST['ind_protoc'] = yes 
			self::validateNovedad($mData['nittra'], $mData['noveda']);
			
			switch ($_REQUEST["ind_valsit"]) 
			{
				case 'S':
					$RESPON = $mInsNoveda->InsertarNovedadPC( BASE_DATOS, $mData, 0 );
					break;
				default:
					$RESPON = $mInsNoveda->InsertarNovedadNC( BASE_DATOS, $mData, 0 );
					break;
			}
			
			if($RESPON[0]['indica'] == "1")
			{
				//actualizo las novedades a solucionar cambiando el indicador a 1
				$mNovedadNem = json_decode($_REQUEST['nov_soluci']);
				# inicia transaccion
	     	 	$mConsultUpd = new Consulta("SELECT 1 ;", self::$cConexion, "BR"); // Inicia 
	     	 	foreach ($mNovedadNem as $ksNem => $vsNem) 
	     	 	{
	     	 		//la posicion 0 = num_consec, 1 = num_despac, 2 = cod_noveda
	     	 		$mDataSNem = explode("|", $vsNem);
	     	 		$mQueryud = " UPDATE ". BASE_DATOS .".tab_despac_novnem 
	     	 							SET ind_soluci = 1, 
		     	 							fec_soluci = NOW(),
		     	 							usr_soluci = '".$_SESSION['datos_usuario']['cod_usuari']."', 
		     	 							nov_soluci = '".$_REQUEST["cod_noveda"]."', 
		     	 							obs_soluci = '".$_REQUEST["obs"]."',
		     	 							cod_solctr = '".$_REQUEST['cod_contro']."', 
		     	 							cod_solstx = '".$mDespac['nom_contro']."', 
		     	 							usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."', 
		     	 							fec_modifi = NOW()
	     	 							WHERE num_consec = '".$mDataSNem[0]."' AND num_despac = '".$mDataSNem[1]."' AND cod_noveda = '".$mDataSNem[2]."'";
	          		$mConsultUpd = new Consulta($mQueryud, self::$cConexion, "BR");
	     	 	}

				$mConsultUpd = new Consulta("SELECT 1;", self::$cConexion, "RC"); // Si no se  va nada por la excepcion hace commit de todo
			    if($mConsultUpd)
			    {
			        echo "ok";
			    }
			}
			else
			{
				#error al generar la novedad
				echo "Error1";
			}

		}
		catch(Exception $e)
		{
			echo "Error en la funcion getNovedad: ", $e->getMessage();
		}
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

	/*! \fn: calcularMinAdi
	 *  \brief: 
	 *  \author: 
	 *  \date: 
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: Matriz
	 */
	private function calcularMinAdi($fechaNoveda, $fechaActual)
	{
		try
		{
			$mTieAdicio = abs( (strtotime($fechaNoveda) - strtotime($fechaActual)) ) / 60;
			$mTieAdicio = round($mTieAdicio);
			return $mTieAdicio;
		}
		catch (Exception $e)
		{
			echo "Error en la funcion calcularMinAdi: ", $e->getMessage();
		}
	}

	/*! \fn: getTranspUsuari
	 *  \brief: 
	 *  \author: 
	 *  \date: 
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getTranspUsuari()
	{
		try
		{
			$mSql = "SELECT a.clv_filtro
						FROM ".BASE_DATOS.".tab_aplica_filtro_perfil a
						WHERE a.cod_perfil = ".$_SESSION['datos_usuario']['cod_perfil']." 
					";
			$mConsult = new Consulta( $mSql, self::$cConexion );
			return $mConsult -> ret_matrix('a');
		}
		catch (Exception $e)
		{
			echo "Error en la funcion getTranspUsuari: ", $e->getMessage();
		}
	}

	/*! \fn: validateNovedad
	 *  \brief: 
	 *  \author: Edward Serrano
	 *  \date: 09-08-2017
	 *  \date modified: dd/mm/aaaa
	 *  \param: 
	 *  \return:
	 */
	private function validateNovedad($mCodTransp = null, $mCodNoveda = null)
	{
		try
		{
			$mSql = "SELECT a.ind_notema
                   FROM " . BASE_DATOS . ".tab_noveda_protoc a 
                  WHERE cod_noveda = '$mCodNoveda' 
                    AND a.cod_transp = '$mCodTransp' ";

	        $mConsult = new Consulta($mSql, self::$cConexion );
	        $mResultMail = $mConsult -> ret_matrix('i');

	        if ( $mResultMail[0][0] == 1)
	        {
	        	$_REQUEST['ind_protoc'] = 'yes';
	        }
		}
		catch (Exception $e)
		{
			echo "Error en la funcion getTranspUsuari: ", $e->getMessage();
		}
	}
	
	/*! \fn: getGenerador
	 *  \brief: Trae los generadores de carga que se encuenten en ruta
	 *  \author: Edward Serrano
	 *	\date: 02/11/2017
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function getGenerador()
	{
		$mSql = "SELECT a.cod_client, UPPER(c.abr_tercer) AS nom_tercer 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac
					AND a.fec_salida IS NOT NULL 
					AND a.fec_salida <= NOW()
					AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
					AND a.ind_anulad = 'R' AND b.ind_activo = 'S'
					AND a.ind_planru = 'S' 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
					 ON a.cod_client = c.cod_tercer 
				  
					";
		if( self::$cTypeUser[tip_perfil] == 'CLIENTE' ){
			$mSql .= " WHERE b.cod_transp = ".self::$cTypeUser[cod_transp];
		}	
		$mSql .= " GROUP BY a.cod_client ORDER BY c.abr_tercer ASC ";
		$consulta = new Consulta( $mSql, self::$cConexion );
		return $mResult = $consulta -> ret_matrix('i');
	}

	/*! \fn: getListDespac
	 *  \brief: Obtiene la lista de despachos generada
	 *  \author: Edward Serrano
	 *	\date: 28/11/2017
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function getListDespac()
	{
		#Query tomada del script despachos.inc y la caul pinta el despacho anterior
		/*$mSql = " SELECT a.num_despac, b.obs_llegad, b.fec_llegad 
                        FROM ".BASE_DATOS.".tab_despac_sisext a
                  INNER JOIN ".BASE_DATOS.".tab_despac_despac b 
                          ON a.num_despac = b.num_despac 
                       WHERE a.num_despac != '{$_REQUEST[cod_despac]}'
                         AND b.cod_manifi = '{$_REQUEST[cod_manifi]}' 
                         AND a.num_desext = (
                                                SELECT x.num_desext 
                                                  FROM ".BASE_DATOS.".tab_despac_sisext x 
                                                 WHERE x.num_despac = '{$_REQUEST[cod_despac]}'
                                                   AND x.num_desext NOT IN ('VC', '')
                                            ) ";*/
        $mSql = " SELECT a.num_dessat, b.obs_llegad, b.fec_llegad FROM tab_bitaco_corona a
                  INNER JOIN ".BASE_DATOS.".tab_despac_despac b
                  		  ON a.num_dessat = b.num_despac 
                  WHERE a.num_dessat != '{$_REQUEST[cod_despac]}'
                    AND a.num_despac = '{$_REQUEST[cod_manifi]}'
                    GROUP BY a.num_dessat ";                               
		$consulta = new Consulta( $mSql, self::$cConexion );
		$mResult = $consulta -> ret_matrix('i');
		$mHtml = new Formlib(2);
		$mHtml->Table("tr");
			$mHtml->Label( "DESPACHOS", array("colspan"=>"3", "align"=>"center", "width"=>"100%", "class"=>"CellHead", "color"=>"#FFFFFF") );
			$mHtml->CloseRow();
			$mHtml->Row();
				//$mHtml->Label( "Despacho", array("colspan"=>"1", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
				//$mHtml->Label( "Observacion", array("colspan"=>"1", "align"=>"center", "width"=>"50%", "class"=>"CellHead") );
				//$mHtml->Label( "Fecha", array("colspan"=>"1", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
			$mHtml->CloseRow();
			foreach ($mResult as $key => $value) {

				$mHtml->Row();
					$mHtml->Label( '<a class="classLink" href="index.php?cod_servic=3302&window=central&despac='.$value[0].'&opcion=1" style="background-color:#FFFFFF; color:green;">'.$value[0].'</a>', array("align"=>"center", "width"=>"25%", "class"=>"cellInfo") );
					//$mHtml->Label( $value[1], array("align"=>"left", "width"=>"50%", "class"=>"cellInfo2") );
					//$mHtml->Label( $value[2], array("align"=>"left", "width"=>"25%", "class"=>"cellInfo2") );
				$mHtml->CloseRow();
			}
		$mHtml->CloseTable('tr');
		echo $mHtml->MakeHtml();
	}


	/*! \fn: setHorSeguim
	 *  \brief: Obtiene el horario del dia de la transportadora
	 *  \author: Ing. Luis Manrique
	 *	\date: 05/03/2019
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: string
	 */
	public function setHorSeguim($mTransp)
	{
		//Consulta que retorna los horarios de seguimiento a la empresa
        $mSql = " SELECT 	a.com_diasxx, 
        					a.hor_ingres,
        					a.hor_salida
        			FROM 	".BASE_DATOS.".tab_config_horlab a 
                   WHERE 	a.cod_tercer = '{$mTransp}'";                               
		$consulta = new Consulta( $mSql, self::$cConexion );
		$mResult = $consulta -> ret_matrix();

		//Variables necesarias
		$dayTxNow = date('D', strtotime(time()));
		$yeaNow = date("Y"); 
		$monNow = date("m");
		$dayNow = date("d");
		$arrayDiasNec = [];
		$arrayDiasFes = [];
		$arrayValida = [];
		$horaMostrar = "";
		
		//Si retornn horarios, recorre el resultado
		if (count($mResult) > 0){
			foreach ($mResult as $key => $value) {
				//Genera division de los dias
				$day =  explode("|",$value["com_diasxx"]);
				//Recorre los dias
				foreach ($day as $day ) {
					//Valida los festivos en Colombia
					$festivo = new Festivos($yeaNow);
					//Valida si es festivo el dia actual y si el dia que retorna es F
					if($day == "F" && $festivo->esFestivo($dayNow,$monNow) == true){
						$arrayDiasFes[] = $value;
					//Valida si hay horarios con el dia actual
					}else if(self::setDiasSem($day) == $dayTxNow){
						$arrayDiasNec[] = $value;
					}
				}
			}

			//Da prioridad al dia como festivo
			if(count($arrayDiasFes) > 0){
				$arrayValida = $arrayDiasFes;
			}else{
				$arrayValida = $arrayDiasNec;
			}

			//Recorre el nuevo arreglo de los dias que aplican
			foreach ($arrayValida as $key => $value) {
				//Si retornn horarios, valida que solo sea un registro
				if (count($arrayValida) == 1){
					$horaMostrar = $value['hor_ingres']."-".$value['hor_salida'];
				}else{
					//Valida si la llave es = a cero para identificar los horarios nocturnos
					if($key == 0){
						$horaMostrar = $value['hor_ingres'];
					}else{
						$horaMostrar .= "-".$value['hor_salida'];
					}
				}
			}
		}
		return $horaMostrar;
	}

	/*! \fn: setDiasSem
	 *  \brief: Obtiene el horario Homologado en ingles
	 *  \author: Ing. Luis Manrique
	 *	\date: 05/03/2019
	 *	\date modified: dia/mes/ano
	 *  \param: 
	 *  \return: string
	 */
	public function setDiasSem($dia) {
		$diasEsp= array ("L","M","X","J","V","S","D");
		$diasEng= array ("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
		$texto = str_replace($diasEsp, $diasEng ,$dia);
		return $texto;
	}

}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new Despac();

?>