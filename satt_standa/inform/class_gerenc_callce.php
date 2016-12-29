<?php
/*! \file: class_gerenc_callce.php
 *  \brief: Archivo principal que general genera data para el informe gerencial del Call center
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 18/08/2015
 *  \bug: 
 *  \warning:
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');
#date_default_timezone_get('America/Bogota');
setlocale(LC_ALL,"es_ES");

/*! \class: CallCe
 *  \brief: Clase principal que general genera data para el informe gerencial del Call center
 */
class CallCe
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cNull = array( array('', '-----') );

	#Diccionario de estado de llamadas
	private static $cDicEst = array( 'CHANUNAVAIL' => 'Error del Sistema', 'CONGESTION' => 'Congesti&oacute;n', 'NOANSWER' => 'No Contesto', 'BUSY' => 'Ocupado', 'ANSWER' => 'Contesto', 'CANCEL' => 'Cancelado' );

	function __construct($co = null, $us = null, $ca = null)
	{
		if($_REQUEST[Ajax] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			self::$cConexion = $AjaxConnection;
		}else{
			self::$cConexion = $co;
			self::$cUsuario = $us;
			self::$cCodAplica = $ca;
		}

		if($_REQUEST[Ajax] === 'on' )
		{
			switch($_REQUEST[Option])
			{
				case 'generateReport':
					self::generateReport();
					break;

				case 'getTransp':
					self::getTransp();
					break;

				case 'detail':
					self::detail();
					break;

				default:
					header('Location: index.php?window=central&cod_servic=1366&menant=1366');
					break;
			}
		}
	}

	
	/*! \fn: generateReport
	 *  \brief: Genereador del reporte
	 *  \author: Ing. Fabian Salinas
	 *	\date: 19/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function generateReport(){
		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
		$mHtml  = '<div id=table'.$_REQUEST[ind_tipdes].'ID class="StyleDIV">';
		$mHtml .= self::informGeneral();
		$mHtml .= '</div>';

		echo $mHtml;
	}

	/*! \fn: detail
	 *  \brief: Imprime detalle de llamadas
	 *  \author: Ing. Fabian Salinas
	 *	\date: 21/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return;
	 */
	private function detail()
	{
		$mTittle = array('#', 'No. Despacho SATT', 'Viaje', 'Fecha Despacho', 'Tipo Despacho', 'Nombre Conductor', 'Placa', 'ID Llamada', 'Telefono', 'Duraci&oacute;n', 'Observaciones', 'Fecha/Hora Llamada', 'Conversaci&oacute;n');
		$mData = self::getData( $_REQUEST[fec_inicia], $_REQUEST[fec_finalx], $_REQUEST[est_llamad] );

		$mHtml .= '<table width="100%" align="center">';

		$mHtml .= '<tr>';
		$mHtml .= '<th class="CellHead" colspan="'.sizeof($mTittle).'" >Se Encontraron '.sizeof($mData).' Registros</th>';
		$mHtml .= '</tr>';

		$mHtml .= '<tr>';
		foreach ($mTittle as $value)
			$mHtml .= '<th class="CellHead">'.$value.'</th>';
		$mHtml .= '</tr>';

		$mEstate = array("BUSY"=> "Ocupado",
			      		 "NOANSWER"=> "No Contestado",
			      		 "NO ANSWER"=> "No Contestado",
			      		 "ANSWER"=> "Contestado",
			      		 "ANSWERED"=> "Contestado",
			      		 "CANCEL"=> "Cancelado",
			      		 "CONGESTION"=> "Congestionado",
			      		 "CHANUNAVAIL"=> "Canal no disponible",
			      		 "FAILED"=> "Falla de marcación",
			      		 ""=> "Otro"
			      		 );

		$n=1;
		foreach ($mData as $row)
		{
			$mHtml .= '<tr>';
			$mHtml .= '<td class="cellInfo onlyCell" align="center">'.$n.'</td>';
			$mHtml .= '<td class="cellInfo onlyCell"><a style="color:#000000;" href="index.php?cod_servic=3302&window=central&despac='.$row[num_despac].'&tie_ultnov=0&opcion=1">'.$row[num_despac].'</a></td>';
			$mHtml .= '<td class="cellInfo onlyCell">'.$row[num_desext].'</td>';
			$mHtml .= '<td class="cellInfo onlyCell">'.$row[fec_despac].'</td>';
			$mHtml .= '<td class="cellInfo onlyCell">'.$row[nom_tipdes].'</td>';
			$mHtml .= '<td class="cellInfo onlyCell">'.$row[nom_conduc].'</td>';
			$mHtml .= '<td class="cellInfo onlyCell">'.$row[num_placax].'</td>';
			$mHtml .= '<td class="cellInfo onlyCell">'.$row[idx_llamad].'</td>';
			$mHtml .= '<td class="cellInfo onlyCell">'.$row[num_telefo].'</td>';
			$mHtml .= '<td class="cellInfo onlyCell" align="center">'.$row[tie_duraci].'</td>';
			$mHtml .= '<td class="cellInfo onlyCell">'.self::$mEstate[ $row[nom_estado] ].'</td>';
			#$mHtml .= '<td class="cellInfo onlyCell">'.$row[nom_estado].'</td>';
			$mHtml .= '<td class="cellInfo onlyCell">'.$row[fec_creaci].'</td>';
			$mHtml .= '<td class="cellInfo onlyCell" align="center"><img border="0" onclick="PlayAudioCall( \''.$row[num_despac].'\', \''.$row[cod_consec].'\', \''.$_REQUEST[standa].'\' );" style="width: 20px; height:20px; curosr: pointer;" src="../'.DIR_APLICA_CENTRAL.'/imagenes/image_play.gif"></td>';
			$mHtml .= '</tr>';
			$n++;
		}

		$mHtml .= '</table>';

		$_SESSION[excelCallce] = $mHtml;

		#Imprime div con tabla informe
		$mImg = '<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" border="0">';
		echo '<div class="StyleDIV" align="center">';
		echo '<a href="index.php?cod_servic='.$_REQUEST[cod_servic].'&window=central&standa='.DIR_APLICA_CENTRAL.'&Option=exportExcel">'.$mImg.'</a>';
		echo $mHtml;
		echo '</div>';
	}

	/*! \fn: informGeneral
	 *  \brief: Crea HTML de las tablas generales
	 *  \author: Ing. Fabian Salinas
	 *	\date: 19/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: HTML
	 */
	private function informGeneral()
	{
		$mTittle[0] = array('Llamadas Generadas', 'Contestadas', 'Porcentaje', 'No Contestadas', 'Porcentaje', 'Otros', 'Porcentaje');
		$mTittle[1] = array('Fecha', 'Generadas', 'Contestadas', 'Porcentaje', 'No Contestadas', 'Porcentaje', 'Otros', 'Porcentaje');

		if( $_REQUEST[fec_inicia] && $_REQUEST[fec_finalx] )
		{ #Fechas según rango filtrado
			$mFecInicia = $_REQUEST[fec_inicia];
			$mFecFinalx = $_REQUEST[fec_finalx];
		}else{ #Fechas rin rango filtrado
			$mData = self::getData();
			$mFecInicia = explode(' ', $mData[0][fec_creaci]);
			$mFecFinalx = explode(' ', $mData[(sizeof($mData)-1)][fec_creaci] );
			$mFecInicia = $mFecInicia[0];
			$mFecFinalx = $mFecFinalx[0];
		}

		$mHtml  = '';
		#<Recorre las fechas>
			$mDate = $mFecInicia;
			while( $mDate <= $mFecFinalx )
			{
				$mFecIni = $mDate." 00:00:00";
				$mFecFin = $mDate." 23:59:59";
				$mCanEstado[0] = self::getData( $mFecIni, $mFecFin, 'ANSWER', true );
				$mCanEstado[1] = self::getData( $mFecIni, $mFecFin, 'NOANSWER', true );
				$mCanEstado[2] = self::getData( $mFecIni, $mFecFin, 'OTHER', true );
				$mTotalLlam = array_sum($mCanEstado);

				$mTotal[0] += $mTotalLlam;
				$mTotal[1] += $mCanEstado[0];
				$mTotal[2] += $mCanEstado[1];
				$mTotal[3] += $mCanEstado[2];

				$mHtml .= '<tr>';
				$mHtml .= '<td class="cellInfo onlyCell">'.strftime("%A, %d de %B del %Y", strtotime($mDate) ).'</td>';
				$mHtml .= $mTotalLlam == 0 ? '<td class="cellInfo onlyCell" align="center">-</td>' : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$_REQUEST[ind_tipdes].'\', \''.$mFecIni.'\', \''.$mFecFin.'\', \'NULL\' );" >'.$mTotalLlam.'</td>';
				$mHtml .= $mCanEstado[0] == 0 ? '<td class="cellInfo onlyCell" align="center">-</td>' : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$_REQUEST[ind_tipdes].'\', \''.$mFecIni.'\', \''.$mFecFin.'\', \'ANSWER\' );" >'.$mCanEstado[0].'</td>';
				$mHtml .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mCanEstado[0] / $mTotalLlam), 0) ).'%</td>';
				$mHtml .= $mCanEstado[1] == 0 ? '<td class="cellInfo onlyCell" align="center">-</td>' : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$_REQUEST[ind_tipdes].'\', \''.$mFecIni.'\', \''.$mFecFin.'\', \'NOANSWER\' );" >'.$mCanEstado[1].'</td>';
				$mHtml .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mCanEstado[1] / $mTotalLlam), 0) ).'%</td>';
				$mHtml .= $mCanEstado[2] == 0 ? '<td class="cellInfo onlyCell" align="center">-</td>' : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$_REQUEST[ind_tipdes].'\', \''.$mFecIni.'\', \''.$mFecFin.'\', \'OTHER\' );" >'.$mCanEstado[2].'</td>';
				$mHtml .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mCanEstado[2] / $mTotalLlam), 0) ).'%</td>';
				$mHtml .= '</tr>';

				$mDate = date ( 'Y-m-d', strtotime( '+1 day', strtotime($mDate) ) );
			}
		#</Recorre las fechas>

		$mFecIni = $mFecInicia." 00:00:00";
		$mFecFin = $mFecFinalx." 23:59:59";

		#<Fila de totales>
			$mHtml3  = $mTotal[0] == 0 ? '<td class="cellInfo onlyCell" align="center">-</td>' : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$_REQUEST[ind_tipdes].'\', \''.$mFecIni.'\', \''.$mFecFin.'\', \'NULL\' );" >'.$mTotal[0].'</td>';
			$mHtml3 .= $mTotal[1] == 0 ? '<td class="cellInfo onlyCell" align="center">-</td>' : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$_REQUEST[ind_tipdes].'\', \''.$mFecIni.'\', \''.$mFecFin.'\', \'ANSWER\' );" >'.$mTotal[1].'</td>';
			$mHtml3 .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mTotal[1] / $mTotal[0]), 0) ).'%</td>';
			$mHtml3 .= $mTotal[2] == 0 ? '<td class="cellInfo onlyCell" align="center">-</td>' : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$_REQUEST[ind_tipdes].'\', \''.$mFecIni.'\', \''.$mFecFin.'\', \'NOANSWER\' );" >'.$mTotal[2].'</td>';
			$mHtml3 .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mTotal[2] / $mTotal[0]), 0) ).'%</td>';
			$mHtml3 .= $mTotal[3] == 0 ? '<td class="cellInfo onlyCell" align="center">-</td>' : '<td class="cellInfo onlyCell" align="center" style="cursor: pointer" onclick="showDetail( \''.$_REQUEST[ind_tipdes].'\', \''.$mFecIni.'\', \''.$mFecFin.'\', \'OTHER\' );" >'.$mTotal[3].'</td>';
			$mHtml3 .= '<td class="cellInfo onlyCell" align="center">'.( number_format((100 * $mTotal[3] / $mTotal[0]), 0) ).'%</td>';
		#<Fila de totales>

		#<Table Detallado por Dias>
			$mHtml2  = '<table width="100%" align="center">';

			$mHtml2 .= '<tr>';
			$mHtml2 .= '<th class="CellHead" colspan="'.sizeof($mTittle[1]).'" >Detallado por D&iacute;as</th>';
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
			$mHtml1 .= '<th class="CellHead" colspan="'.sizeof($mTittle[0]).'" >General</th>';
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

		return utf8_encode($mHtml);
	}

	/*! \fn: getData
	 *  \brief: Trae la informacion según filtros
	 *  \author: Ing. Fabian Salinas
	 *	\date: 19/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: mFecInicia	Date	Fecha Inicial
	 *  \param: mFecFinalx	Date	Fecha Final
	 *  \param: mEstLlamad	String	Estado de la llamada
	 *  \param: mIndCantid	Boolean	Indicador = TRUE retorna cantidad
	 *  \return: Matriz o Integer
	 */
	private function getData( $mFecInicia = NULL, $mFecFinalx = NULL, $mEstLlamad = NULL, $mIndCantid = false )
	{

	  
		$mSql = "SELECT a.num_despac, a.cod_manifi, a.cod_tipdes, 
						a.fec_despac, c.num_placax, d.nom_tipdes, 
						b.cod_consec, b.idx_llamad, b.num_telefo, 
						b.tie_duraci, b.nom_estado, b.rut_audiox, 
						b.fec_creaci, e.abr_tercer AS nom_conduc, 
						x.num_desext, x.tip_transp 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_callnov b 
					 ON a.num_despac = b.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige c 
					 ON a.num_despac = c.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes d 
					 ON a.cod_tipdes = d.cod_tipdes 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e 
					 ON c.cod_conduc = e.cod_tercer 
					AND c.cod_transp = '{$_REQUEST[cod_transp]}' 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_sisext x 
					 ON a.num_despac = x.num_despac 
			  LEFT JOIN ".BASE_DATOS.".vis_despac_callou y
			  		 ON a.num_despac = y.num_despac 
				  WHERE 1=1  ";

		$mSql .= !$_REQUEST[num_despac] ? "" : " AND a.num_despac = '{$_REQUEST[num_despac]}' ";
		$mSql .= !$_REQUEST[num_manifi] ? "" : " AND a.cod_manifi = '{$_REQUEST[num_manifi]}' ";
		$mSql .= !$_REQUEST[num_placax] ? "" : " AND c.num_placax = '{$_REQUEST[num_placax]}' ";
		$mSql .= !$_REQUEST[num_viajex] ? "" : " AND x.num_desext = '{$_REQUEST[num_viajex]}' ";
		$mSql .= !$_REQUEST[cod_tiptra] ? "" : " AND x.tip_transp = '{$_REQUEST[cod_tiptra]}' ";
		$mSql .= !$_REQUEST[cod_tipdes] ? "" : " AND a.cod_tipdes = '{$_REQUEST[cod_tipdes]}' "; 
		$mSql .= $_REQUEST[ind_tipdes] == 0 ? "" : " AND a.cod_tipdes = '{$_REQUEST[ind_tipdes]}' ";

		if ($_REQUEST[cod_operad]) {
			$mSql .= "AND y.num_exten IN (
								           SELECT a.num_extenc
							                 FROM ".BASE_DATOS.".tab_callce_extenc a
							                WHERE a.cod_operac = '{$_REQUEST[cod_operad]}' 
							                )";
		}
		if( $mFecInicia == NULL && $mFecFinalx == NULL )
			$mSql .= !$_REQUEST[fec_inicia] && !$_REQUEST[fec_finalx] ? "" : " AND b.fec_creaci BETWEEN '{$_REQUEST[fec_inicia]}' AND '{$_REQUEST[fec_finalx]}' ";
		elseif( $mFecInicia != NULL && $mFecFinalx != NULL )
			$mSql .= " AND b.fec_creaci BETWEEN '{$mFecInicia}' AND '{$mFecFinalx}' ";

		$mSql .= !$_REQUEST[nom_estado] ? "" : " AND b.nom_estado LIKE '{$_REQUEST[nom_estado]}' ";

		if( $mEstLlamad == 'ANSWER' || $mEstLlamad == 'ANSWERED' )
			$mSql .= " AND b.nom_estado IN ('ANSWER', 'ANSWERED') ";
		elseif( $mEstLlamad == 'NOANSWER' || $mEstLlamad == 'NO ANSWER' )
			$mSql .= " AND b.nom_estado IN ('NOANSWER', 'NO ANSWER') ";
		elseif( $mEstLlamad == 'OTHER' )
			$mSql .= " AND b.nom_estado NOT IN ('ANSWER', 'ANSWERED', 'NOANSWER', 'NO ANSWER') ";

		$mSql .= " ORDER BY a.num_despac, b.fec_creaci ";
		 
		$mConsult = new Consulta($mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');

		if( $mIndCantid == true )
			return sizeof($mResult);
		else
			return $mResult;
	}

	/*! \fn: getEstadoLlamada
	 *  \brief: Trae los estados de llamada
	 *  \author: Ing. Fabian Salinas
	 *	\date: 18/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	public function getEstadoLlamada()
	{
		$mSelect = "SELECT a.nom_estado 
					  FROM ".BASE_DATOS.".tab_despac_callnov a 
				  GROUP BY a.nom_estado 
				  ORDER BY a.nom_estado DESC ";
		$consulta = new Consulta( $mSelect, self::$cConexion );
		$mArrayEstado = $consulta -> ret_matrix('i');

		$i=0;
		foreach ($mArrayEstado as $row )
		{
			$mValue = self::$cDicEst[$row[0]];
			$mResult[$i][0] = $row[0];
			$mResult[$i][1] = $mValue == NULL ? $row[0] : $mValue;
			$i++;
		}

		return $mResult;
	}

	/*! \fn: getTipoDespac
	 *  \brief: Trae los Tipos de Despacho
	 *  \author: Ing. Fabian Salinas
	 *	\date: 19/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: 
	 */
	public function getTipoDespac()
	{
		$mSql = " SELECT cod_tipdes, UPPER(nom_tipdes) 
					FROM ".BASE_DATOS.".tab_genera_tipdes 
				ORDER BY nom_tipdes ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('i');
	}

	/*! \fn: getTipoTransp
	 *  \brief: Trae los Tipos de Transporte
	 *  \author: Ing. Fabian Salinas
	 *	\date: 19/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: 
	 */
	public function getTipoTransp()
	{
		$mSql = " SELECT cod_tiptra, nom_tiptra 
					FROM ".BASE_DATOS.".tab_genera_tiptra 
				ORDER BY nom_tiptra ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('i');
	}

	/*! \fn: getTransp
	 *  \brief: Trae las transportadoras
	 *  \author: Ing. Fabian Salinas
	 *	\date: 19/08/2015
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
					AND a.cod_estado = ".COD_ESTADO_ACTIVO." ";

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

		$mSql .= $_REQUEST[term] ? " AND a.abr_tercer LIKE '%".$_REQUEST[term]."%' " : "";
		$mSql .= " ORDER BY a.abr_tercer ASC ";
		$consulta = new Consulta( $mSql, self::$cConexion );
		$mResult = $consulta -> ret_matrix('a');

		if( $_REQUEST[term] )
		{
			$mTranps = array();
			for($i=0; $i<sizeof( $mResult ); $i++){
				$mTxt = $mResult[$i][cod_tercer]." - ".utf8_decode($mResult[$i][nom_tercer]);
				$mTranps[] = array('value' => utf8_decode($mResult[$i][nom_tercer]), 'label' => $mTxt, 'id' => $mResult[$i][cod_tercer] );
			}
			echo json_encode( $mTranps );
		}
		else
			return $mResult;
	}

	/*! \fn: lista
	 *  \brief: Crea una lista desplegable para el formulario
	 *  \author: Ing. Fabian Salinas
	 *	\date: 18/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: mTitulo  String  Titulo del Campo
	 *  \param: mNomSel  String  Nombre del Select
	 *  \param: mMatriz  Matriz  Matriz con las opciones
	 *  \param: mClass  String  Nombre de la clase para el <td>
	 *  \param: mObliga  boolean  Si el campo es obligatorio agrega *
	 *  \return: HTML
	 */
	public function lista( $mTitulo, $mNomSel, $mMatriz, $mClass, $mObliga = 0 )
	{
		$mHtml = '<td class="'.$mClass.'" align="right">'.( $mObliga ? '* ' : '' ).$mTitulo.'</td>';

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

	/*! \fn: texto
	 *  \brief: Crea un input tipo texto para el formulario
	 *  \author: Ing. Fabian Salinas
	 *	\date: 18/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: mTitulo  String  Titulo del Campo
	 *  \param: mNomInp  String  Nombre del Input
	 *  \param: mValue   String  Valor del input
	 *  \param: mClass   String  Nombre de la clase para el <td>
	 *  \param: mObliga  boolean  Si el campo es obligatorio agrega *
	 *  \param: mMaxLen  Integer  Longitud maxima del input
	 *  \param: mColspa  Integer  Valor Colspan
	 *  \param: mDisabl  Boolean  true = disable
	 *  \return: HTML
	 */
	public function texto( $mTitulo, $mNomInp, $mValue, $mClass, $mObliga = 0, $mMaxLen = 15, $mColspa = 1, $mDisabl = false )
	{
		$mHtml  = '	<td class="'.$mClass.'" align="right" colspan="'.$mColspa.'" >'.( $mObliga ? '* ' : '' ).$mTitulo.'</td>';
		$mHtml .= '	<td class="'.$mClass.'" colspan="'.$mColspa.'" >';
		$mHtml .= '		<input type="text" maxlength="'.$mMaxLen.'" value="'.$mValue.'" size="'.$mMaxLen.'" id="'.$mNomInp.'ID" name="'.$mNomInp.'" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" '.( $mDisabl == true ? " disabled" : "" ).'>';
		$mHtml .= '	</td>';

		return $mHtml;
	}

	public function getTipOperad(){

		$mSql = " SELECT cod_operac, UPPER(nom_operac) 
					FROM ".BASE_DATOS.".tab_callce_operac 
				ORDER BY nom_operac ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('i');

	}
}

if($_REQUEST[Ajax] === 'on' )
	$_INFORM = new CallCe();

?>