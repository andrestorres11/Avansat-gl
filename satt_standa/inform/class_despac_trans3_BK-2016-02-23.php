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

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

#header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');
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
					$cTime = array( 'ind_desurb' => '30', 'ind_desnac' => '60' ); #warning2

	function __construct($co = null, $us = null, $ca = null)
	{
		if($_REQUEST[Ajax] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			Despac::$cConexion = $AjaxConnection;
		}else{
			Despac::$cConexion = $co;
			Despac::$cUsuario = $us;
			Despac::$cCodAplica = $ca;
		}

		Despac::style();
		Despac::$cHoy = date("Y-m-d H:i:s");
		Despac::$cTypeUser = Despac::typeUser();

		if( Despac::$cTypeUser[tip_perfil] == 'CONTROL' )
		{
			Despac::$cControlador = Despac::getInfoControlador( $_SESSION[datos_usuario][cod_usuari] );

			Despac::$cTipDespacContro .= Despac::$cControlador[ind_desurb] == 1 ? ',"1"' : '';
			Despac::$cTipDespacContro .= Despac::$cControlador[ind_desnac] == 1 ? ',"2"' : '';
			Despac::$cTipDespacContro .= Despac::$cControlador[ind_desimp] == 1 ? ',"3"' : '';
			Despac::$cTipDespacContro .= Despac::$cControlador[ind_desexp] == 1 ? ',"4"' : '';
			Despac::$cTipDespacContro .= Despac::$cControlador[ind_desxd1] == 1 ? ',"5"' : '';
			Despac::$cTipDespacContro .= Despac::$cControlador[ind_desxd2] == 1 ? ',"6"' : '';
			Despac::$cTipDespacContro = Despac::$cTipDespacContro == '"","1","2","3","4","5","6"' ? '""' : Despac::$cTipDespacContro; #Si tiene asignado todos los tipos de despacho no es necesario filtrar por tipo de despacho
		}

		$mTipDespac = Despac::getTipoDespac();
		foreach ($mTipDespac as $row){
			Despac::$cTipDespac .= $_REQUEST["tip_despac".$row[0]] == '1' ? ',"'.$row[0].'"' : '';
		}

		if($_REQUEST[Ajax] === 'on' )
		{
			switch($_REQUEST[Option])
			{
				case "infoGeneral":
					Despac::infoGeneral();
					break;

				case "infoCargue":
					Despac::infoCargue();
					break;

				case 'infoTransito':
					Despac::infoTransito();
					break;

				case 'infoDescargue':
					Despac::infoDescargue();
					break;

				case "detailBand":
					Despac::detailBand();
					break;

				case "detailSearch":
					Despac::detailSearch();
					break;

				default:
					header('Location: index.php?window=central&cod_servic=1366&menant=1366');
					break;
			}
		}
	}

	/*! \fn: style
	 *  \brief: Estilos
	 *  \author: Ing. Fabian Salinas
	 *	\date: 18/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function style()
	{
		echo '
			<style>
				.classTable{ font-family:Arial; font-size:11px; color:#444444; background:#eeeeee; }
				.classHead{ border-right: 1px solid #ffffff; border-bottom: 1px solid #ffffff; padding:5px 15px 5px 15px; color:#333333; }
				.classTotal{ border-right: 1px solid #eeeeee; border-bottom: 1px solid #eeeeee; padding:5px 15px 5px 15px; color:#333333; background:#ffffff; }
				.classCell{ border-right: 1px solid #ffffff; border-bottom: 1px solid #ffffff; padding:2px 5px 2px 5px; color:#444444; }
				.classList{ background:#eeeeee; font-family:Arial; font-size:11px; color:#444444; font-weight:bold; padding:0px 5px 0px 5px; }

				/* Cargue */
				.bgC1{ background:#F6CEEC; color:#000000; }
				.bgC2{ background:#F5A9F2; color:#000000; }
				.bgC3{ background:#F781F3; color:#FFFFFF; }
				.bgC4{ background:#FA58F4; color:#FFFFFF; }

				/* Transito */
				.bgT1{ background:#FFFF66; color:#000000; }
				.bgT2{ background:#FF9900; color:#000000; }
				.bgT3{ background:#FF0000; color:#FFFFFF; }
				.bgT4{ background:#CC33FF; color:#FFFFFF; }

				/* Descargue */
				.bgD1{ background:#BCF5A9; color:#000000; }
				.bgD2{ background:#01DF01; color:#000000; }
				.bgD3{ background:#088A08; color:#FFFFFF; }
				.bgD4{ background:#0B610B; color:#FFFFFF; }

				/* AZUL */
				.bgAZ{ background:#2E2EFE; color:#FFFFFF; }

				.cp{ cursor:pointer; }
				.classLink { background:#eeeeee; font-family:Arial; font-size:11px; color:#006600; font-weight:bold; }
				.classLinkTotal{ font-family:Arial; font-size:11px; color:#bb0000; font-weight:bold; text-decoration:none; }
				.classLinkTotal:hover{ font-family:Arial; font-size:11px; color:#111111; font-weight:bold; text-decoration:underline; }
				.classMenu{ border-left: 1px solid #ffffff; border-right: 1px solid #ffffff; background:#009900; cursor:pointer; width:7px; }
				.bt{ border-top: 1px solid #ffffff; }

				.celda_titulo {
					background-color: #f5f5f5;
					background-image: url("../'.DIR_APLICA_CENTRAL.'/estilos/Verde/imagenes/backg_01.gif");
					border-bottom: 1px solid #999999;
					color: #333333;
					font-weight: bold;
					padding: 3px 10px;
					white-space: nowrap;
					width: 16%;
				}

				.celda_info {
					background-color: #ffffff;
					border-bottom: 1px solid #efefef;
					border-right: 1px solid #efefef;
					color: #000000;
					padding-left: 10px;
					padding-right: 10px;
					white-space: nowrap;
					width: 16%;
				}

				.cellHead
                {
                  padding:5px 10px;
                  background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
                  background: -moz-linear-gradient(top, #009617, #00661b ); 
                  background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
                  background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
                  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#03ad39", endColorstr="#00660f",GradientType=0 );
                  color:#fff;
                  text-align:center;
                }
                
                .footer
                {
                  padding:5px 10px;
                  background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
                  background: -moz-linear-gradient(top, #009617, #00661b ); 
                  background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
                  background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
                  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#03ad39", endColorstr="#00660f",GradientType=0 );
                  color:#fff;
                  text-align:left;
                }

                .cellHead2
                {
                  padding:5px 10px;
                  background: #03ad39;
                  background: -webkit-gradient(linear, left top, left bottom, from( #03ad39 ), to( #00660f )); 
                  background: -moz-linear-gradient(top, #03ad39, #00660f ); 
                  background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
                  background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
                  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#03ad39", endColorstr="#00660f",GradientType=0 );
                  color:#fff;
                  text-align:right;
                }

                tr.row:hover  td
                {
                  background-color: #9ad9ae;
                }
                .cellInfo
                {
                  padding:5px 10px;
                  background-color:#fff;
                  border:1px solid #ccc;
                }

                .cellInfo2
                {
                  padding:5px 10px;
                  background-color:#9ad9ae;
                  border:1px solid #ccc;
                }

                .label
                {
                  font-size:12px;
                  font-weight:bold;
                }

                .select
                {
                  background-color:#fff;
                  border:1px solid #009617;
                }

                .boton
                {
                  background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
                  background: -moz-linear-gradient(top, #009617, #00661b ); 
                  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#009617", endColorstr="#00661b");
                  color:#fff;
                  border:1px solid #fff;
                  padding:3px 15px;
                  -webkit-border-radius: 5px;
                  -moz-border-radius: 5px;
                  border-radius: 5px;
                }

                .boton:hover
                {
                  background:#fff;
                  color:#00661b;
                  border:1px solid #00661b;
                  cursor:pointer;
                }
                
                .StyleDIV
                {
                  min-height: 300px; 
                }

                .Style2DIV {
                    background-color: rgb(240, 240, 240);
                    border: 1px solid rgb(201, 201, 201);
                    border-radius: 5px;
                    min-height: 50px;
                    padding: 5px;
                    width: 99%;
                }
                .cellInfo1 {
                    background-color: #ebf8e2;
                    font-family: Trebuchet MS,Verdana,Arial;
                    font-size: 11px;
                    padding: 2px;
                }
			</style> 
		';
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
		$mContent = $_REQUEST[ind_filact] == '1' || Despac::$cTypeUser[tip_perfil] == 'CLIENTE' ? Despac::printInformGeneral() : 'Por Favor Seleccione los Parametros de Busqueda Para Generar el Informe.';
		$mHtml  = '<div id=table1ID>';
		$mHtml .= $mContent;
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
		$mTittle[texto] = array('NO.', 'TIPO SERVICIO', 'EMPRESA', 'NO. DESPACHOS', 'SIN RETRASO', 'AVISO CONTROL CARGUE (0-30 MIN)', 'ALERTA CARGUE (31-60 MIN)', 'SIN CARGUE (61-90 MIN)', 'NOVEDAD EN CARGUE (91 MIN)', 'ESTADO PERNOCTACION', 'A CARGO EMPRESA', 'USUARIO ASIGNADO' );
		$mTittle[style] = array('', '', '', '', '', 'bgC1', 'bgC2', 'bgC3', 'bgC4', '', '');

		$mHtml  = '<div id=table2ID>';
		$mHtml .= Despac::printInform( $mIndEtapa, $mTittle );
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
		$mTittle[texto] = array('NO.', 'TIPO SERVICIO', 'EMPRESA', 'NO. DESPACHOS', 'SIN RETRASO', 'SEGUIMIENTO (0-30 MIN)', 'AVISO (31-60 MIN)', 'OPERATIVO (61-90 MIN)', 'A CARGO DE EMPRESA (91 MIN)', 'ESTADO PERNOCTACION', 'POR LLEGADA', 'A CARGO EMPRESA', 'USUARIO ASIGNADO' );
		$mTittle[style] = array('', '', '', '', '', 'bgT1', 'bgT2', 'bgT3', 'bgT4', '', '');

		$mHtml  = '<div id=table3ID>';
		$mHtml .= Despac::printInform( $mIndEtapa, $mTittle );
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
		$mTittle[texto] = array('NO.', 'TIPO SERVICIO', 'EMPRESA', 'NO. DESPACHOS', 'SIN RETRASO', 'PROXIMO A DESCARGUE (0-30 MIN)', 'EN DESCARGUE (31-60 MIN)', 'SIN DESCARGUE (61-90 MIN)', 'NOVEDAD EN DESCARGUE (91 MIN)', 'ESTADO PERNOCTACION', 'POR LLEGADA', 'A CARGO EMPRESA', 'USUARIO ASIGNADO' );
		$mTittle[style] = array('', '', '', '', '', 'bgD1', 'bgD2', 'bgD3', 'bgD4', '', '');

		$mHtml  = '<div id=table4ID>';
		$mHtml .= Despac::printInform( $mIndEtapa, $mTittle );
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
		$mTransp = Despac::getTranspServic( $mIndEtapa );
		$mLimitFor = Despac::$cTypeUser[tip_perfil] == 'OTRO' ? sizeof($mTittle[texto]) : sizeof($mTittle[texto])-1;
		$mHtml = '';
		$j=1;

		#Dibuja las Filas por Transportadora
		for($i=0; $i<sizeof($mTransp); $i++)
		{
			#Trae los Despachos Segun Etapa
			switch ($mIndEtapa){
				case 'ind_segcar':
					$mDespac = Despac::getDespacCargue( $mTransp[$i] );
					break;
				case 'ind_segtra':
					if( $mTransp[$i][ind_segcar] == '0' && $mTransp[$i][ind_segdes] == '0' )
						$mDespac = Despac::getDespacTransi1( $mTransp[$i] );
					else
						$mDespac = Despac::getDespacTransi2( $mTransp[$i] );
					break;
				case 'ind_segdes':
					$mDespac = Despac::getDespacDescar( $mTransp[$i] );
					break;
			}

			#Si la Transportadora tiene Despachos
			if( $mDespac != false )
			{
				$mData = Despac::calTimeAlarma( $mDespac, $mTransp[$i], 1 );
				
				$mHtml .= '<tr onclick="this.style.background=this.style.background==\'#CEF6CE\'?\'#eeeeee\':\'#CEF6CE\';">';
				$mHtml .= 	'<th class="classCell" nowrap="" align="left">'.$j.'</th>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mTransp[$i][nom_tipser].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$mTransp[$i][nom_transp].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer" >'.sizeof($mDespac).'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[sin_retras] == 0 ? '' : 'onclick="showDetailBand(\'sin_retras\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[sin_retras].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_00A30x] == 0 ? '' : 'onclick="showDetailBand(\'con_00A30x\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_00A30x].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_31A60x] == 0 ? '' : 'onclick="showDetailBand(\'con_31A60x\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_31A60x].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_61A90x] == 0 ? '' : 'onclick="showDetailBand(\'con_61A90x\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_61A90x].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_91Amas] == 0 ? '' : 'onclick="showDetailBand(\'con_91Amas\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_91Amas].'</td>';
				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[est_pernoc] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[est_pernoc].'</td>';

				if( $mIndEtapa != 'ind_segcar' )
					$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[fin_rutaxx] == 0 ? '' : 'onclick="showDetailBand(\'fin_rutaxx\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[fin_rutaxx].'</td>';

				$mHtml .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[ind_acargo] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \''.$mTransp[$i][cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[ind_acargo].'</td>';

				if( Despac::$cTypeUser[tip_perfil] == 'OTRO' )
					$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.( $mTransp[$i][usr_asigna] != '' ? $mTransp[$i][usr_asigna] : 'SIN ASIGNAR' ).'</td>';

				$mHtml .= '</tr>';

				$mTotal[0] += sizeof($mDespac);
				$mTotal[1] += $mData[sin_retras];
				$mTotal[2] += $mData[con_00A30x];
				$mTotal[3] += $mData[con_31A60x];
				$mTotal[4] += $mData[con_61A90x];
				$mTotal[5] += $mData[con_91Amas];
				$mTotal[6] += $mData[est_pernoc];
				$mTotal[8] += $mData[ind_acargo];

				if( $mIndEtapa != 'ind_segcar' )
					$mTotal[7] += $mData[fin_rutaxx];

				$j++;
			}
		}

		#Dibuja la Fila de los Totales
		$mHtml1  = '<tr>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="right" colspan="3">TOTALES:</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" onclick="showDetailBand(\'sinF\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;" >'.$mTotal[0].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[1] == 0 ? '' : 'onclick="showDetailBand(\'sin_retras\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[1].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[2] == 0 ? '' : 'onclick="showDetailBand(\'con_00A30x\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[2].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[3] == 0 ? '' : 'onclick="showDetailBand(\'con_31A60x\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[3].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[4] == 0 ? '' : 'onclick="showDetailBand(\'con_61A90x\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[4].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[5] == 0 ? '' : 'onclick="showDetailBand(\'con_91Amas\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[5].'</th>';
		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[6] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[6].'</th>';

		if( $mIndEtapa != 'ind_segcar' )
			$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[7] == 0 ? '' : 'onclick="showDetailBand(\'fin_rutaxx\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[7].'</th>';

		$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[8] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \'TODAS\');" style="cursor: pointer;"' ) .' >'.$mTotal[8].'</th>';

		if( Despac::$cTypeUser[tip_perfil] == 'OTRO' )
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
		$mConsult = new Consulta($mSql, Despac::$cConexion );
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
			if ( $filtro -> listar( Despac::$cConexion ) ) : 
				$datos_filtro = $filtro -> retornar();
				$mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
			endif;
		}else{#PARA EL FILTRO DE EMPRESA
			$filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_perfil] );
			if ( $filtro -> listar( Despac::$cConexion ) ) : 
				$datos_filtro = $filtro -> retornar();
				$mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
			endif;
		}
		$mSql .= " ORDER BY a.abr_tercer ASC ";
		$consulta = new Consulta( $mSql, Despac::$cConexion );
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
		$mConsult = new Consulta( $mSql, Despac::$cConexion );
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
		$mConsult = new Consulta( $mSql, Despac::$cConexion );
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
			$mTransp = Despac::getTransp();
			if( sizeof($mTransp) == '1' ){
				$mResult[tip_perfil] = 'CLIENTE'; #Perfil Cliente
				$mResult[cod_transp] = $mTransp[0][0];
			}else
				$mResult[tip_perfil] = 'OTRO'; 
		}

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
	private function getDespacCargue( $mTransp, $mTipReturn = NULL, $mSinFiltro = false )
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
						AND ( zz.fec_salida IS NULL OR zz.fec_salida = '0000-00-00 00:00:00' )
						AND yy.cod_transp = '".$mTransp[cod_transp]."' ";
		$mConsult = new Consulta( $mSql, Despac::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		if( sizeof($mDespac) < 1 )
			return false;

		$mDespac = join( ',', GetColumnFromMatrix( $mDespac, 'num_despac' ) ); #Despachos en ruta Sin hora salida del sistema

		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax, 
						UPPER(h.abr_tercer) AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin 
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
				  WHERE 1=1 ";

		if( $mSinFiltro == false )
		{
			#Filtros por Formulario
			#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
			$mSql .= Despac::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". Despac::$cTipDespac .") " : "";

			#Filtros por usuario
			$mSql .= Despac::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. Despac::$cTipDespacContro .') ' : '';	
		}

		$mConsult = new Consulta( $mSql, Despac::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = Despac::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = Despac::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

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
	private function getDespacDescar( $mTransp, $mTipReturn = NULL, $mSinFiltro = false )
	{
		$mDespacCargue = Despac::getDespacCargue( $mTransp, 'list', true );
		 
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
		$mConsult = new Consulta( $mSql, Despac::$cConexion );
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
		$mConsult = new Consulta( $mSql, Despac::$cConexion );
		$mDespacDes = $mConsult -> ret_matrix('a');
		$mDespacDes = join( ',', GetColumnFromMatrix( $mDespacDes, 'num_despac' ) );

		# Despachos para recorrer y verificar si estan en etapa Descargue Filtro 2
		$mSql = "	 SELECT a.num_despac, a.cod_tipdes 
					   FROM ".BASE_DATOS.".tab_despac_despac a 
					  WHERE a.num_despac IN ( {$mDespac} ) ";
		$mSql .= $mDespacDes != "" ? " AND a.num_despac NOT IN ( {$mDespacDes} ) " : "";
		$mConsult = new Consulta( $mSql, Despac::$cConexion );
		$mDespac = $mConsult -> ret_matrix('i');

		$mDespacDes = $mDespacDes == "" ? '""' : $mDespacDes;

		#Recorre Despachos Para verificar Filtro 2
		foreach ($mDespac as $row)
		{
			$mNextPC = getNextPC( Despac::$cConexion, $row[0] ); #Siguiente PC del Plan de ruta
			$mRutaDespac = getControDespac( Despac::$cConexion, $row[0] ); # Ruta del Despacho
			$mPosPC = (sizeof($mRutaDespac))-1; #Posicion Ultimo PC


			if(    ( $mNextPC[cod_contro] == $mRutaDespac[$mPosPC][cod_contro] ) #Siguiente PC igual al ultimo PC del plan de ruta
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
				$mTime = Despac::getTimeDescargue( $mTransp, $row[1] );
				$mTime = "-".$mTime." minute";
				$mDate = date ( 'Y-m-d H:i:s', ( strtotime( $mTime, strtotime ( $mNextPC[fec_planea] ) ) ) ); #Fecha Planeada para iniciar Seguimiento en Descargue

				if( $mDate <= Despac::$cHoy )
					$mDespacDes .= ','.$row[0];
			}
		}

		#Informacion de los despachos en Etapa Descargue
		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax,
						h.abr_tercer AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin 
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
				  WHERE 1=1 ";

		if( $mSinFiltro == false )
		{
			#Filtros por Formulario
			#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
			$mSql .= Despac::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". Despac::$cTipDespac .") " : "";

			#Filtros por usuario
			$mSql .= Despac::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. Despac::$cTipDespacContro .') ' : '';	
		}

		$mConsult = new Consulta( $mSql, Despac::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		
		$mTipValida = Despac::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = Despac::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

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
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin 
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
				  WHERE 1=1 ";

		#Filtros por Formulario
		#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
		$mSql .= Despac::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". Despac::$cTipDespac .") " : "";

		#Filtros por usuario
		$mSql .= Despac::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. Despac::$cTipDespacContro .') ' : '';	
		
		$mConsult = new Consulta( $mSql, Despac::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = Despac::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = Despac::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

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
	private function getDespacTransi2( $mTransp )
	{
		$mDespacCarDes = Despac::getDespacDescar( $mTransp, 'list2', true ); #Despachos en Etapas Cargue y Descargue

		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax,
						h.abr_tercer AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin 
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
					AND a.num_despac NOT IN ( {$mDespacCarDes} )
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
				  WHERE 1=1 ";

		#Filtros por Formulario
		#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
		$mSql .= Despac::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". Despac::$cTipDespac .") " : "";

		#Filtros por usuario
		$mSql .= Despac::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. Despac::$cTipDespacContro .') ' : '';	
		
		$mConsult = new Consulta( $mSql, Despac::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = Despac::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = Despac::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

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
		$mNovDespac = getNovedadesDespac( Despac::$cConexion, $mDespac[num_despac], 1 ); # Novedades del Despacho -- Script /lib/general/function.inc
		$mCantNoved = sizeof($mNovDespac); # Cantidad de Novedades del Despacho
		$mPosN = $mCantNoved-1; #Posicion ultima Novedad

		$mResult[can_noveda] = $mCantNoved;
		$mResult[ind_fuepla] = $mNovDespac[$mPosN][ind_fuepla];
		$mResult[ind_limpio] = $mNovDespac[$mPosN][ind_limpio];
		$mResult[fec_ultnov] = $mNovDespac[$mPosN][fec_crenov];
		$mResult[nom_ultnov] = $mNovDespac[$mPosN][nom_noveda] == '' ? '-' : $mNovDespac[$mPosN][nom_noveda];
		$mResult[nom_sitiox] = $mNovDespac[$mPosN][nom_sitiox] == '' ? '-' : $mNovDespac[$mPosN][nom_sitiox];
		$mResult[sig_pcontr] = getNextPC( Despac::$cConexion, $mDespac[num_despac] );
		$mResult[pla_rutaxx] = getControDespac( Despac::$cConexion, $mDespac[num_despac] ); # Plan de Ruta del Despacho -- Script /lib/general/function.inc

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
					$mTime = $mDespac[cod_tipdes] == '1' ? Despac::$cTime[ind_desurb] : Despac::$cTime[ind_desnac];
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
		$mConsult = new Consulta( $mSql, Despac::$cConexion );
		$mDespac = $mConsult -> ret_matrix('i');

		if( sizeof($mDespac) > 0 )
		{
			foreach ($mDespac as $row)
			{
				$mUltPC = getControDespac( Despac::$cConexion, $row[0] );
				$mUltPC = end( $mUltPC );

				$mSql = "SELECT a.cod_noveda 
						   FROM ".BASE_DATOS.".tab_despac_noveda a 
						  WHERE a.num_despac = '{$row[0]}' 
							AND a.cod_contro = '{$mUltPC[cod_contro]}' ";
				$mConsult = new Consulta( $mSql, Despac::$cConexion );
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

	/*! \fn: getTranspServic
	 *  \brief: Traer las transportadoras según tipo de servicio
	 *  \author: Ing. Fabian Salinas
	 *	\date: 19/06/2015
	 *	\date modified: dia/mes/año
	 *  \param: mTipEtapax  String   Tipo de Seguimiento ( ind_segcar, ind_segtra, ind_segdes )
	 *  \param: mCodTransp  Integer  Codigo de la transportadora 
	 *  \return: Matriz
	 */
	public function getTranspServic( $mTipEtapax = NULL, $mCodTransp = NULL )
	{
		$mTipServic = '""';
		$mTipServic .= $_REQUEST[tip_servic1] == '1' ? ',"1"' : '';
		$mTipServic .= $_REQUEST[tip_servic2] == '1' ? ',"2"' : '';
		$mTipServic .= $_REQUEST[tip_servic3] == '1' ? ',"3"' : '';

		$mSql = " SELECT a.cod_transp, a.nom_tipser, UPPER(a.abr_tercer) AS nom_transp, 
						 a.tie_nacion, a.tie_urbano, a.ind_segcar, a.ind_segtra, 
						 a.ind_segdes, a.tie_desurb, a.tie_desnac, a.tie_desimp, 
						 a.tie_desexp, a.tie_destr1, a.tie_destr2, a.cod_tipser, 
						 GROUP_CONCAT(h.cod_usuari ORDER BY h.cod_usuari ASC SEPARATOR ', ' ) AS usr_asigna
					FROM (
							SELECT b.cod_transp, b.nom_tipser, b.abr_tercer, 
								   b.ind_segcar, b.ind_segtra, b.ind_segdes, 
								   b.tie_nacion, b.tie_urbano, b.tie_desurb, 
								   b.tie_desnac, b.tie_desimp, b.tie_desexp, 
								   b.tie_destr1, b.tie_destr2, b.cod_tipser 
							FROM (
										SELECT c.ind_segcar, c.ind_segtra, c.ind_segdes, 
											   c.cod_transp, c.num_consec, d.nom_tipser, 
											   e.abr_tercer, c.tie_contro AS tie_nacion, 
											   c.tie_conurb AS tie_urbano, c.tie_desurb, 
											   c.tie_desnac, c.tie_desimp, c.tie_desexp, 
											   c.tie_destr1, c.tie_destr2, c.cod_tipser 
										  FROM ".BASE_DATOS.".tab_transp_tipser c 
									INNER JOIN ".BASE_DATOS.".tab_genera_tipser d 
											ON c.cod_tipser = d.cod_tipser 
									INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e 
											ON c.cod_transp = e.cod_tercer 
									  ORDER BY c.cod_transp, c.num_consec DESC
								 ) b
							GROUP BY b.cod_transp
						 ) a 
			   LEFT JOIN ".BASE_DATOS.".vis_monito_encdet h
					  ON a.cod_transp = h.cod_transp
					WHERE 1=1 ";

		$mSql .= $mTipEtapax != NULL ? " AND a.{$mTipEtapax} = 1 " : "";

		#Filtro por codigo de Transportadora
		if( Despac::$cTypeUser[tip_perfil] == 'CLIENTE' )
			$mSql .= " AND a.cod_transp = '". Despac::$cTypeUser[cod_transp] ."' ";
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
		if( Despac::$cTypeUser[tip_perfil] == 'CONTROL' || Despac::$cTypeUser[tip_perfil] == 'EAL' )
			$mSql .= " AND h.cod_usuari = '".$_SESSION[datos_usuario][cod_usuari]."' ";
		elseif( $mSinFiltro == true )
			$mSql .= " AND ( h.cod_transp IS NULL OR h.cod_usuari IN ({$_REQUEST[cod_usuari]}) )";
		else
			$mSql .= $_REQUEST[cod_usuari] ? " AND h.cod_usuari IN ( {$_REQUEST[cod_usuari]} ) " : "";

		#Otros Filtros
		$mSql .= $mTipServic != '""' ? " AND a.cod_tipser IN (".$mTipServic.") " : "";

		$mSql .= " GROUP BY a.cod_transp ORDER BY a.abr_tercer ASC ";

		$mConsult = new Consulta( $mSql, Despac::$cConexion );
		return $mResult = $mConsult -> ret_matrix('a');
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
	 *  \param: $mColor    Array  	Colores por Etapa
	 *  \return: Matriz
	 */
	private function calTimeAlarma( $mDespac, $mTransp, $mIndCant = 0, $mFiltro = NULL, $mColor = NULL )
	{
		$mTipValida = Despac::tipValidaTiempo( $mTransp );

		if( $mIndCant == 1 )
		{ #Define Cantidades según estado
			$mResult[fin_rutaxx] = 0;
			$mResult[ind_acargo] = 0;
			$mResult[est_pernoc] = 0;
			$mResult[sin_retras] = 0;
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
				$mDespac[$i][tiempo] = getDiffTime( $mDespac[$i][fec_planea], Despac::$cHoy ); #Script /lib/general/function.inc

				if( $mDespac[$i][ind_fuepla] == '1' && $mDespac[$i][tiempo] < 0 )
					$mPernoc = true;
			}
			else #Despacho Sin Novedades
				$mDespac[$i][tiempo] = getDiffTime( $mDespac[$i][fec_salida], Despac::$cHoy ); #Script /lib/general/function.inc


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
				elseif( $mDespac[$i][tiempo] < 31 && $mDespac[$i][tiempo] >= 0 ) # 0 a 30
					$mResult[con_00A30x]++;
				elseif( $mDespac[$i][tiempo] < 61 && $mDespac[$i][tiempo] > 30 ) # 31 a 60
					$mResult[con_31A60x]++;
				elseif( $mDespac[$i][tiempo] < 91 && $mDespac[$i][tiempo] > 60 ) # 61 a 90
					$mResult[con_61A90x]++;
				elseif( $mDespac[$i][tiempo] > 90 ) # Mayor 90
					$mResult[con_91Amas]++;
				else
					continue;
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
						$mResult[neg_tieesp][$mNegTieesp][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[neg_tieesp][$mNegTieesp][fase] = 'est_pernoc';
						$mNegTieesp++;
					}else{
						$mResult[neg_tiempo][$mNegTiempo] = $mDespac[$i];
						$mResult[neg_tiempo][$mNegTiempo][color] = $mColor[0];
						$mResult[neg_tiempo][$mNegTiempo][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
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
						$mResult[neg_tieesp][$mNegTieesp][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[neg_tieesp][$mNegTieesp][fase] = 'sin_retras';
						$mNegTieesp++;
					}else{
						$mResult[neg_tiempo][$mNegTiempo] = $mDespac[$i];
						$mResult[neg_tiempo][$mNegTiempo][color] = $mColor[0];
						$mResult[neg_tiempo][$mNegTiempo][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[neg_tiempo][$mNegTiempo][fase] = 'sin_retras';
						$mNegTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_00A30x' || $mFiltro == 'sinF') && $mDespac[$i][tiempo] < 31 && $mDespac[$i][tiempo] >= 0 && $mBandera == true )
				{ # 0 a 30
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[1];
						$mResult[pos_tieesp][$mPosTieesp][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_00A30x';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[1];
						$mResult[pos_tiempo][$mPosTiempo][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_00A30x';
						$mPosTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_31A60x' || $mFiltro == 'sinF') && $mDespac[$i][tiempo] < 61 && $mDespac[$i][tiempo] > 30 && $mBandera == true )
				{ # 31 a 60
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[2];
						$mResult[pos_tieesp][$mPosTieesp][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_31A60x';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[2];
						$mResult[pos_tiempo][$mPosTiempo][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_31A60x';
						$mPosTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_61A90x' || $mFiltro == 'sinF') && $mDespac[$i][tiempo] < 91 && $mDespac[$i][tiempo] > 60 && $mBandera == true )
				{ # 61 a 90
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[3];
						$mResult[pos_tieesp][$mPosTieesp][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_61A90x';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[3];
						$mResult[pos_tiempo][$mPosTiempo][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_61A90x';
						$mPosTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_91Amas' || $mFiltro == 'sinF') && $mDespac[$i][tiempo] > 90 && $mBandera == true )
				{ # Mayor 90
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[4];
						$mResult[pos_tieesp][$mPosTieesp][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_91Amas';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[4];
						$mResult[pos_tiempo][$mPosTiempo][color2] = Despac::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_91Amas';
						$mPosTiempo++;
					}
				}else
					continue;
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
		$mTittle = array('NO.', 'DESPACHO', 'TIEMPO', 'A C. EMPRESA', 'NO. TRANSPORTE', 'NOVEDADES', 'ORIGEN', 'DESTINO', 'TRANSPORTADORA', 'PLACA', 'CONDUCTOR', 'CELULAR', 'UBICACI&Oacute;N', 'FECHA SALIDA', 'ULTIMA NOVEDAD' );
		$mTransp = Despac::getTranspServic( $_REQUEST[ind_etapax], $_REQUEST[cod_transp] );

		#Según Etapa
		switch ( $_REQUEST[ind_etapax] )
		{
			case 'ind_segcar':
				$mColor = array('', 'bgC1', 'bgC2', 'bgC3', 'bgC4');
				$mNameFunction = 'getDespacCargue';
				break;

			case 'ind_segtra':
				$mColor = array('', 'bgT1', 'bgT2', 'bgT3', 'bgT4');
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

		#Trae Data por transportadoras
		for ($i=0; $i < sizeof($mTransp); $i++)
		{
			if( $_REQUEST[ind_etapax] == 'ind_segtra' )
				$mNameFunction = $mTransp[$i][ind_segcar] == '1' && $mTransp[$i][ind_segdes] == '1' ? 'getDespacTransi2' : 'getDespacTransi1';

			$mDespac = Despac::$mNameFunction( $mTransp[$i] );
			$mDespac = Despac::calTimeAlarma( $mDespac, $mTransp[$i], 0, $_REQUEST[ind_filtro], $mColor );

			$mNegTieesp = $mDespac[neg_tieesp] ? array_merge($mNegTieesp, $mDespac[neg_tieesp]) : $mNegTieesp;
			$mPosTieesp = $mDespac[pos_tieesp] ? array_merge($mPosTieesp, $mDespac[pos_tieesp]) : $mPosTieesp;
			$mNegTiempo = $mDespac[neg_tiempo] ? array_merge($mNegTiempo, $mDespac[neg_tiempo]) : $mNegTiempo;
			$mPosTiempo = $mDespac[pos_tiempo] ? array_merge($mPosTiempo, $mDespac[pos_tiempo]) : $mPosTiempo;
			$mNegFinrut = $mDespac[neg_finrut] ? array_merge($mNegFinrut, $mDespac[neg_finrut]) : $mNegFinrut;
			$mPosFinrut = $mDespac[pos_finrut] ? array_merge($mPosFinrut, $mDespac[pos_finrut]) : $mPosFinrut;
			$mNegAcargo = $mDespac[neg_acargo] ? array_merge($mNegAcargo, $mDespac[neg_acargo]) : $mNegAcargo;
			$mPosAcargo = $mDespac[pos_acargo] ? array_merge($mPosAcargo, $mDespac[pos_acargo]) : $mPosAcargo;
		}

		$mData = Despac::orderMatrizDetail( $mNegTieesp, $mPosTieesp, $mNegTiempo, $mPosTiempo, $mNegFinrut, $mPosFinrut, $mNegAcargo, $mPosAcargo );

		#Pinta tablas
		$mHtml  = '';
		$mHtml .= $mData[tieesp] ? Despac::printTabDetail( $mTittle, $mData[tieesp], sizeof($mData[tieesp]).' DESPACHOS CON TIEMPO MODIFICADO', '1' ) : '';
		$mHtml .= $mData[tiempo] ? Despac::printTabDetail( $mTittle, $mData[tiempo], sizeof($mData[tiempo]).' DESPACHOS EN SEGUIMIENTO', '1' ) : '';
		$mHtml .= $mData[acargo] ? Despac::printTabDetail( $mTittle, $mData[acargo], sizeof($mData[acargo]).' DESPACHOS A CARGO EMPRESA', '1' ) : '';
		$mHtml .= $mData[finrut] ? Despac::printTabDetail( $mTittle, $mData[finrut], sizeof($mData[finrut]).' DESPACHOS PENDIENTE LLEGADA', '1' ) : '';

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
		#Dibuja Cabecera tabla 
		$mHtml  = '<table class="classTable" width="100%" cellspacing="0" cellpadding="0" align="center">';

		$mHtml .= 	'<tr>';
		$mHtml .= 		'<th class="classHead" align="center" colspan="'.sizeof($mTittle).'" >'.$mSection.'</th>';
		$mHtml .= 	'</tr>';

		$mHtml .= 	'<tr>';
		foreach ($mTittle as $value){
			$mHtml .= '<th class="classHead" align="center">'.$value.'</th>';
		}
		$mHtml .= 	'</tr>';
		
		#Dibuja Data de la tabla
		$n=1;
		foreach ($mData as $row)
		{
			$mTxt = substr($row[color], 3);
			$mColor = $mTxt > 2 ? '#FFFFFF;' : '#000000;';
			$mLink = '<a href="index.php?cod_servic='.( $mOpcion == '1' ? '3302' : '1385').'&window=central&despac='.$row[num_despac].'&tie_ultnov='.$row[tiempo].'&opcion='.$mOpcion.'" style="color:'.$mColor.'">'.$row[num_despac].'</a>';
			$mHtml .= '<tr onclick="this.style.background=this.style.background==\'#CEF6CE\'?\'#eeeeee\':\'#CEF6CE\';">';
			$mHtml .= 	'<th class="classHead" nowrap="" align="left">'.$n.'</th>';
			$mHtml .= 	'<td class="classCell bt '.$row[color].'" nowrap="" align="left">'.$mLink.'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[tiempo].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[ind_defini].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[cod_manifi].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[can_noveda].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[ciu_origen].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[ciu_destin].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[nom_transp].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left" style="background: '.$row[color2].'" >'.$row[num_placax].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[nom_conduc].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[num_telmov].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[nom_sitiox].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[fec_salida].'</td>';
			$mHtml .= 	'<td class="classCell" nowrap="" align="left">'.$row[nom_ultnov].'</td>';
			$mHtml .= '</tr>';
			$n++;
		}

		$mHtml .= '</table>';
		$mHtml .= '<br/>';

		return $mHtml;
	}

	/*! \fn: orderMatrizDetail
	 *  \brief: Ordena la Matriz Resultante para el detalle
	 *  \author: Ing. Fabian Salinas
	 *	\date: 23/07/2015
	 *	\date modified: dia/mes/año
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
		$mData[tieesp] = array_merge($mPosi, $mNega);

		$mNega = $mNegTiempo ? SortMatrix( $mNegTiempo, 'tiempo', 'ASC'  ) : array();
		$mPosi = $mPosTiempo ? SortMatrix( $mPosTiempo, 'tiempo', 'DESC' ) : array();
		$mData[tiempo] = array_merge($mPosi, $mNega);

		$mNega = $mNegFinrut ? SortMatrix( $mNegFinrut, 'tiempo', 'ASC'  ) : array();
		$mPosi = $mPosFinrut ? SortMatrix( $mPosFinrut, 'tiempo', 'DESC' ) : array();
		$mData[finrut] = array_merge($mPosi, $mNega);

		$mNega = $mNegAcargo ? SortMatrix( $mNegAcargo, 'tiempo', 'ASC'  ) : array();
		$mPosi = $mPosAcargo ? SortMatrix( $mPosAcargo, 'tiempo', 'DESC' ) : array();
		$mData[acargo] = array_merge($mPosi, $mNega);

		return $mData;
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
		if( $_REQUEST[ind_entran] == '1' )
			echo Despac::search();

		if( $_REQUEST[ind_fintra] == '1' )
			echo Despac::search( true );
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
		$mTittle = array('NO.', 'DESPACHO', 'TIEMPO', 'A C. EMPRESA', 'NO. TRANSPORTE', 'NOVEDADES', 'ORIGEN', 'DESTINO', 'TRANSPORTADORA', 'PLACA', 'CONDUCTOR', 'CELULAR', 'UBICACI&Oacute;N', 'FECHA SALIDA', 'ULTIMA NOVEDAD' );
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
			$mHtml .= "</center>";

			$mResult = $mHtml;
		}
		elseif( $mIndConsol == true )
		{#Despachos consolidados
			$mNovedades = getNovedadesDespac( Despac::$cConexion , $mDespac[$i][num_despac], 1 ); #Novedades del despacho
			$n = sizeof($mNovedades);

			$mDespac[0][can_noveda] = $n;
			$mDespac[0][nom_ultnov] = $mNovedades[($n-1)][nom_noveda];
			$mDespac[0][nom_sitiox] = $mNovedades[($n-1)][nom_sitiox];
			$mDespac[0][tiempo] = '-';

			$mHtml = Despac::printTabDetail( $mTittle, $mDespac, sizeof($mDespac).' DESPACHOS CONSOLIDADOS', '1' );
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
				$mTransp = Despac::getTranspServic( NULL, $mDespac[$i][cod_transp] );
				$mTipValida = Despac::tipValidaTiempo( $mTransp[0] );
				$mData = Despac::getInfoDespac( $mDespac[$i], $mTransp[0], $mTipValida );

				$mDespac[$i][can_noveda] = $mData[can_noveda];
				$mDespac[$i][fec_ultnov] = $mData[fec_ultnov];
				$mDespac[$i][nom_ultnov] = $mData[nom_ultnov];
				$mDespac[$i][nom_sitiox] = $mData[nom_sitiox];
				$mDespac[$i][fec_planea] = $mData[fec_planea];
				$mDespac[$i][ind_finrut] = $mData[sig_pcontr][ind_finrut]; 

				$mDespacho[0] = $mDespac[$i];
				$mData = Despac::calTimeAlarma( $mDespacho, $mTransp, 0, 'sinF', $mColor );

				$mNegTieesp = $mData[neg_tieesp] ? array_merge($mNegTieesp, $mData[neg_tieesp]) : $mNegTieesp;
				$mPosTieesp = $mData[pos_tieesp] ? array_merge($mPosTieesp, $mData[pos_tieesp]) : $mPosTieesp;
				$mNegTiempo = $mData[neg_tiempo] ? array_merge($mNegTiempo, $mData[neg_tiempo]) : $mNegTiempo;
				$mPosTiempo = $mData[pos_tiempo] ? array_merge($mPosTiempo, $mData[pos_tiempo]) : $mPosTiempo;
				$mNegFinrut = $mData[neg_finrut] ? array_merge($mNegFinrut, $mData[neg_finrut]) : $mNegFinrut;
				$mPosFinrut = $mData[pos_finrut] ? array_merge($mPosFinrut, $mData[pos_finrut]) : $mPosFinrut;
				$mNegAcargo = $mData[neg_acargo] ? array_merge($mNegAcargo, $mData[neg_acargo]) : $mNegAcargo;
				$mPosAcargo = $mData[pos_acargo] ? array_merge($mPosAcargo, $mData[pos_acargo]) : $mPosAcargo;
			}

			$mData = Despac::orderMatrizDetail( $mNegTieesp, $mPosTieesp, $mNegTiempo, $mPosTiempo, $mNegFinrut, $mPosFinrut, $mNegAcargo, $mPosAcargo );

			#Pinta tablas
			$mHtml  = '';
			$mHtml .= $mData[tieesp] ? Despac::printTabDetail( $mTittle, $mData[tieesp], sizeof($mData[tieesp]).' DESPACHOS CON TIEMPO MODIFICADO', '1' ) : '';
			$mHtml .= $mData[tiempo] ? Despac::printTabDetail( $mTittle, $mData[tiempo], sizeof($mData[tiempo]).' DESPACHOS EN SEGUIMIENTO', '1' ) : '';
			$mHtml .= $mData[acargo] ? Despac::printTabDetail( $mTittle, $mData[acargo], sizeof($mData[acargo]).' DESPACHOS A CARGO EMPRESA', '1' ) : '';
			$mHtml .= $mData[finrut] ? Despac::printTabDetail( $mTittle, $mData[finrut], sizeof($mData[finrut]).' DESPACHOS PENDIENTE LLEGADA', '1' ) : '';
		}
		else
		{#Despachos Finalizados
			for ($i=0; $i < sizeof($mDespac); $i++)
			{
				$mNovedades = getNovedadesDespac( Despac::$cConexion , $mDespac[$i][num_despac], 1 ); #Novedades del despacho
				$n = sizeof($mNovedades);

				$mDespac[$i][can_noveda] = $n;
				$mDespac[$i][nom_ultnov] = $mNovedades[($n-1)][nom_noveda];
				$mDespac[$i][nom_sitiox] = $mNovedades[($n-1)][nom_sitiox];
				$mDespac[$i][tiempo] = '-';
			}

			$mHtml = Despac::printTabDetail( $mTittle, $mDespac, sizeof($mDespac).' DESPACHOS FINALIZADOS', '2' );
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

		$mConsult = new Consulta( $mSql, Despac::$cConexion );
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
					".( $mNumDespac != NULL ? "" : ($mIndFinrut == true ? " AND a.fec_llegad IS NOT NULL AND a.fec_llegad != '0000-00-00 00:00:00' " : " AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00') ") )."
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

		if( Despac::$cTypeUser[tip_perfil] == 'CLIENTE' ){
			$mSql .= " AND b.cod_transp = ".Despac::$cTypeUser[cod_transp];
		}

		if( $mNumDespac == NULL ){
			$mSql .= $_REQUEST[num_despac] ? " AND a.num_despac = '{$_REQUEST[num_despac]}' " : "";
			$mSql .= $_REQUEST[num_placax] ? " AND b.num_placax LIKE '{$_REQUEST[num_placax]}' " : "";
			$mSql .= $_REQUEST[num_celcon] ? " AND h.num_telmov = '{$_REQUEST[num_celcon]}' " : "";
			$mSql .= $_REQUEST[num_viajex] ? " AND x.num_desext LIKE '{$_REQUEST[num_viajex]}' " : "";
			$mSql .= $_REQUEST[num_solici] ? " AND x.num_solici = '{$_REQUEST[num_solici]}' " : "";
			$mSql .= $_REQUEST[num_pedido] ? " AND x.num_pedido = '{$_REQUEST[num_pedido]}' " : "";
			$mSql .= $_REQUEST[num_factur] ? " AND y.num_docume = '{$_REQUEST[num_factur]}' " : "";
		}else
			$mSql .= " AND a.num_despac = '{$mNumDespac}' ";

		$mSql .= " GROUP BY a.num_despac ";
		$mSql .= $mIndFinrut == false ? "" : " ORDER BY a.fec_llegad DESC ";
		$mSql .= $mIndFinrut == false ? "" : " LIMIT 10 ";

		$mConsult = new Consulta( $mSql, Despac::$cConexion );
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
		$mLimitFor = Despac::$cTypeUser[tip_perfil] == 'OTRO' ? sizeof($mTittle[texto]) : sizeof($mTittle[texto])-1;
		$mTransp = Despac::getTranspServic();
		$mReport = array();
		$mHtml1 = '';
		$n=1;

		for ($i=0; $i < sizeof($mTransp); $i++)
		{
			if( $mTransp[$i][ind_segcar] == 1 )
			{
				$mDespac = Despac::getDespacCargue( $mTransp[$i] );
				$mData = Despac::calTimeAlarma( $mDespac, $mTransp[$i], 1 );
				$mReport[$i][can_cargue] = sizeof($mDespac);
			}

			if( $mTransp[$i][ind_segtra] == 1 )
			{
				if( $mTransp[$i][ind_segcar] == 1  && $mTransp[$i][ind_segtra] == 1  && $mTransp[$i][ind_segdes] == 1  )
					$mDespac = Despac::getDespacTransi2( $mTransp[$i] ); 
				else
					$mDespac = Despac::getDespacTransi1( $mTransp[$i] );

				$mData = Despac::calTimeAlarma( $mDespac, $mTransp[$i], 1 );

				$mReport[$i][can_transi] = sizeof($mDespac);
			}

			if( $mTransp[$i][ind_segdes] == 1 )
			{
				$mDespac = Despac::getDespacDescar( $mTransp[$i] );
				$mData = Despac::calTimeAlarma( $mDespac, $mTransp[$i], 1 );
				$mReport[$i][can_descar] = sizeof($mDespac);
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

				if( Despac::$cTypeUser[tip_perfil] == 'OTRO' )
					$mHtml1 .= 	'<td class="classCell" nowrap="" align="center">'.	$mEnSegi .'</td>';

				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center">'.$mReport[$i][tot_despac].'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center">'.( $mReport[$i][can_cargue] ? $mReport[$i][can_cargue] : '-' ).'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center">'.( $mReport[$i][can_transi] ? $mReport[$i][can_transi] : '-' ).'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center">'.( $mReport[$i][can_descar] ? $mReport[$i][can_descar] : '-' ).'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center">'.( $mData[est_pernoc] ? $mData[est_pernoc] : '-' ).'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center">'.( $mData[fin_rutaxx] ? $mData[fin_rutaxx] : '-' ).'</td>';
				$mHtml1 .= 	'<td class="classCell" nowrap="" align="center">'.( $mData[ind_acargo] ? $mData[ind_acargo] : '-' ).'</td>';

				if( Despac::$cTypeUser[tip_perfil] == 'OTRO' )
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

		if( Despac::$cTypeUser[tip_perfil] == 'OTRO' )
			$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[7].'</th>';

		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[0].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[1].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[2].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[3].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[4].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[5].'</th>';
		$mHtml2 .= '<th class="classTotal" nowrap="" align="center">'.$mTotal[6].'</th>';

		if( Despac::$cTypeUser[tip_perfil] == 'OTRO' )
			$mHtml2 .= '<th class="classTotal" nowrap="" align="center">&nbsp;</th>';

		$mHtml2 .= '</tr>';


		#Dibuja la Tabla Completa
		$mHtml  = '<table class="classTable" width="100%" cellspacing="0" cellpadding="0" align="center">';
		$mHtml .= 	'<tr>';
		for ($i=0; $i < $mLimitFor; $i++) {
			$mHtml .= $i != 3 || ( $i == 3 && Despac::$cTypeUser[tip_perfil] == 'OTRO' ) ? '<th class="classHead bt '.$mTittle[style][$i].'" align="center">'.$mTittle[texto][$i].'</th>' : '';
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
		$mConsult = new Consulta($mSql, Despac::$cConexion);
		$mData = $mConsult->ret_matrix('a');

		return json_decode($mData[0][$mCatego]);
	}

}

if($_REQUEST[Ajax] === 'on' )
	$_INFORM = new Despac();

?>