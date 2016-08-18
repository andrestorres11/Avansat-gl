<?php
/*! \file: ClassCrossDoking.php
 *  \brief: Realiza la logica para Cross Doking
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 29/09/2015
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

#header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');
#date_default_timezone_get('America/Bogota');
setlocale(LC_ALL,"es_ES");

/*! \class: ClassCrossDoking
 *  \brief: Realiza la logica para Cross Doking
 */
class ClassCrossDoking
{
	private static 	$cConexion,
					$cNull = array( array('', '-----') );

	function __construct($co = null)
	{
		@include_once( "general/constantes.inc" );
		if( $_REQUEST['Ajax'] === 'on' )
		{
			@include_once( "ajax.inc" );
			@include_once( "constantes.inc" );
			self::$cConexion = $AjaxConnection;

			switch($_REQUEST['Option'])
			{
				case 'generateReport':
					self::generateReport();
					break;

				case 'generateReport2':
					self::generateReport2();
					break;

				case 'generateReportG':
					self::generateReportG();
					break;

				case 'getCiudadTransp':
					self::getCiudadTransp();
					break;

				case 'divCrossDoking':
					self::divCrossDoking( $_REQUEST['num_despac'] );
					break;

				default:
					header('Location: index.php?window=central&cod_servic=1366&menant=1366');
					break;
			}
		}else
			self::$cConexion = $co;
	}

	/*! \fn: calTimeTramo1vsTramo2
	 *  \brief: Calcula el tiempo entre el viaje tramo 1 y el viaje tramo 2
	 *  \author: Ing. Fabian Salinas
	 *  \date: 29/09/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mDespacTramo1  Integer  Numero Despacho Tramo 1
	 *  \param: mDespacTramo2  Integer  Numero Despacho Tramo 2
	 *  \param: mIndAlarma     Boolean  true Retorna Array con 2 resultados, 1: tiempo transcurrido; 2: Tiempo de alarma
	 *  \return: 
	 */
	public function calTimeTramo1vsTramo2( $mDespacTramo1, $mDespacTramo2 = null, $mIndAlarma = false )
	{
		$mPlanRuta = getControDespac( self::$cConexion, $mDespacTramo1 ); #Plan de ruta del despacho tramo 1
		$mUltContr = end($mPlanRuta);
		$mUltContr = $mUltContr['cod_contro'];#Ultimo PC del despacho Tramo 1

		$mSql = "SELECT IF(b.fec_noveda IS NULL, a.fec_llegad, b.fec_noveda) AS date_time,
						IF(b.fec_noveda IS NULL, DATE(a.fec_llegad), DATE(b.fec_noveda)) AS date_tramo, 
						IF(b.fec_noveda IS NULL, TIME(a.fec_llegad), TIME(b.fec_noveda)) AS time_tramo, 
						a.cod_ciudes, c.cod_transp 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_noveda b 
					 ON a.num_despac = b.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige c 
					 ON a.num_despac = c.num_despac 
				  WHERE a.num_despac = '{$mDespacTramo1}' 
					AND (b.cod_contro = '{$mUltContr}' OR b.cod_contro IS NULL)
			   ORDER BY date_time ASC 
				  LIMIT 1 ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mTramo1 = $mConsult -> ret_matrix('a');

		if( $mDespacTramo2 == null )
			$mTramo2 = array( "date_time"=>date('Y-m-d H:i:s'), "date_tramo"=>date('Y-m-d'), "time_tramo"=>date('H:i:s') );
		else{
			$mSql = "SELECT IF(a.fec_salida IS NULL OR a.fec_salida = '' OR a.fec_salida = '0000-00-00 00:00:00', NOW(), a.fec_salida) AS date_time, 
							IF(a.fec_salida IS NULL OR a.fec_salida = '' OR a.fec_salida = '0000-00-00 00:00:00', DATE(NOW()), DATE(a.fec_salida) ) AS date_tramo, 
							IF(a.fec_salida IS NULL OR a.fec_salida = '' OR a.fec_salida = '0000-00-00 00:00:00', TIME(NOW()), TIME(a.fec_salida) ) AS time_tramo 
					   FROM ".BASE_DATOS.".tab_despac_corona a 
					  WHERE a.num_dessat = '{$mDespacTramo2}' ";
			$mConsult = new Consulta( $mSql, self::$cConexion );
			$mTramo2 = $mConsult -> ret_matrix('a');
			$mTramo2 = $mTramo2[0];
		}

		if( $mIndAlarma == true )
		{
			$mSql = "SELECT a.fec_noveda AS date_time, 
							DATE(a.fec_noveda) AS date_tramo, 
							TIME(a.fec_noveda) AS time_tramo 
					   FROM ".BASE_DATOS.".tab_despac_noveda a 
					  WHERE a.num_despac = '{$mDespacTramo1}' 
						AND a.cod_contro = '{$mUltContr}' 
						AND a.cod_noveda = '328' 
				   ORDER BY a.fec_noveda DESC 
					  LIMIT 1 ";
			$mConsult = new Consulta( $mSql, self::$cConexion );
			$mNovAlarma = $mConsult -> ret_matrix('a');

			if( sizeof($mNovAlarma) > 0 ){
				$mTramo1[1] = $mNovAlarma[0];
				$mTramo1[1]['cod_ciudes'] = $mTramo1[0]['cod_ciudes'];
				$mTramo1[1]['cod_transp'] = $mTramo1[0]['cod_transp'];
			}
		}

		$mResult = array();
		for ($i=0; $i < sizeof($mTramo1); $i++)
		{
			$mHorario = self::horarioCiudad( $mTramo1[$i]['cod_transp'], $mTramo1[$i]['cod_ciudes'] );
			$mFestivo = self::getFestTranspCiudad( $mTramo1[$i]['cod_transp'], $mTramo1[$i]['cod_ciudes'], $mTramo1[$i]['date_tramo'], $mTramo2['date_tramo'] );

			#Calcula Diferencia de tiempo habil 
			$mTime = 0;
			$mFec=$mTramo1[$i]['date_tramo'];
			while( $mFec<=$mTramo2['date_tramo'] )
			{
				if( $mFestivo[$mFec] != '1' )
				{#dia laboral
					$mDia = date('N', strtotime($mFec) );
					$mDia = self::formatDay($mDia);

					if( $mTramo1[$i]['date_tramo'] == $mTramo2['date_tramo'] )
					{#Fecha inicial = Fecha Final
						if( ($mTramo1[$i]['time_tramo'] < $mHorario[$mDia]['hor_ingres'] || $mTramo1[$i]['time_tramo'] > $mHorario[$mDia]['hor_salida']) && 
							($mTramo2['time_tramo'] < $mHorario[$mDia]['hor_ingres'] || $mTramo2['time_tramo'] > $mHorario[$mDia]['hor_salida']) )
						{#El rango de horas no esta dentro del horairo laboral
							$mDiff = 0;
						}else
						{
							if( $mTramo1[$i]['time_tramo'] < $mHorario[$mDia]['hor_ingres'] )
								$mFec1 = $mHorario[$mDia]['hor_ingres'];
							else
								$mFec1 = $mTramo1[$i]['time_tramo'];

							if( $mTramo2['time_tramo'] < $mHorario[$mDia]['hor_salida'] )
								$mFec2 = $mTramo2['time_tramo'];
							else
								$mFec2 = $mHorario[$mDia]['hor_salida'];

							$mDiff = diffHours($mFec1, $mFec2);
						}

					}
					elseif( $mFec == $mTramo1[$i]['date_tramo'] && $mHorario[$mDia]['hor_salida'] >= $mTramo1[$i]['time_tramo'] )
					{#mFec = Fecha inicial && Hora de salida >= Hora Inicial
						$mDiff = diffHours($mTramo1[$i]['time_tramo'], $mHorario[$mDia]['hor_salida']);
					}
					elseif( $mFec == $mTramo2['date_tramo'] && $mHorario[$mDia]['hor_salida'] >= $mTramo2['time_tramo'] && $mHorario[$mDia]['hor_ingres'] <= $mTramo2['time_tramo'] )
					{#mFec = Fecha Final && Hora Final entre horario laboral
						$mDiff = diffHours($mHorario[$mDia]['hor_ingres'], $mTramo2['time_tramo']);
					}
					elseif( $mFec == $mTramo2['date_tramo'] && $mHorario[$mDia]['hor_ingres'] > $mTramo2['time_tramo'] )
					{#mFec = Fecha Final && Hora de ingreso > Hora final
						$mDiff = 0;
					}
					else
						$mDiff = diffHours($mHorario[$mDia]['hor_ingres'], $mHorario[$mDia]['hor_salida']); #Horas Habiles

					$mTime+= $mDiff;
				}

				$mFec = date( 'Y-m-d', strtotime('+1 day', strtotime($mFec)) );
			}

			#Da formato a el resultado Horas Minutos
			$mResult[$i] = self::formatDayHour($mTime);
		}

		return $mResult;
	}

	/*! \fn: formatDayHour
	 *  \brief: Retorna Array con los dias y horas con base a Minutos
	 *  \author: Ing. Fabian Salinas
	 *  \date:  08/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mTime  Integer  Minutos
	 *  \return: Array
	 */
	private function formatDayHour( $mTime )
	{
		$mHours = round(($mTime/60), 2);
		$mHours = explode(".", $mHours);
		$mMins = strlen($mHours[1]) == 1 ? $mHours[1]."0" : $mHours[1];
		$mMins = round( ($mMins * 60 / 100), 0);
		return $mResult = array("hour"=>$mHours[0], "min"=>$mMins);
	}

	/*! \fn: getFacturDespac
	 *  \brief: Consulta las facturas asociadas al despacho
	 *  \author: Ing. Fabian Salinas
	 *  \date: 30/09/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mNumDespac  Integer  Numero del despacho
	 *  \return: Matriz
	 */
	public function getFacturDespac( $mNumDespac )
	{
		/*$mSql = "SELECT c.num_docume, b.nom_estado, d.num_desext, 
						c.num_destin, c.nom_destin, e.cod_pedrem 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_corona b 
					 ON a.num_despac = b.num_dessat 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_destin c 
					 ON a.num_despac = c.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_sisext d 
					 ON a.num_despac = d.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_cordes e 
					 ON d.num_desext = e.num_despac 
				  WHERE a.num_despac = '{$mNumDespac}' 
					AND c.num_docume IS NOT NULL 
					AND e.cod_pedrem != '' 
			   GROUP BY e.cod_pedrem ";*/

		$mSql = "SELECT a.num_docume, a.num_despac AS num_desext, 
						a.num_destin, a.nom_destin, a.cod_pedrem, 
						b.nom_estado, a.num_destin1 
				   FROM ".BASE_DATOS.".tab_despac_cordes a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_corona b 
					 ON a.num_despac = b.num_despac 
					AND a.num_docume IS NOT NULL 
					AND a.cod_pedrem != '' 
					AND a.cod_pedrem IS NOT NULL 
				  WHERE b.num_dessat = '{$mNumDespac}' 
				";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('a');
	}

	/*! \fn: getViajeFactur
	 *  \brief: Consulta el numero de viaje asociado al codigo pedido remision diferente al vieje del despacho parametro
	 *  \author: Ing. Fabian Salinas
	 *  \date: 30/09/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mNumDespac  Integer  Numero de Despacho
	 *  \param: mCodPedrem  Integer  Codigo pedido remision
	 *  \return: Array
	 */
	public function getViajeFactur( $mNumDespac, $mCodPedrem )
	{
		/*$mSql = "SELECT a.num_desext, a.num_despac, b.ind_xdunox, 
						c.cod_manifi, d.num_solici, b.num_destin1, 
						GROUP_CONCAT(b.num_docume SEPARATOR ' ' ) AS num_docume, 
						b.num_destin, b.nom_destin, c.fec_citcar, 
						DATE_FORMAT(c.fec_creaci, '%H:%i %d-%m-%Y') AS fec_creaci, 
						IF(c.fec_llegad = '0000-00-00 00:00:00', '', c.fec_llegad) AS fec_llegad, 
						IF(d.fec_salida = '0000-00-00 00:00:00', '', d.fec_salida) AS fec_salpla 
				   FROM ".BASE_DATOS.".tab_despac_sisext a 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_destin b 
					 ON a.num_despac = b.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_despac c 
					 ON a.num_despac = c.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_corona d 
					 ON a.num_despac = d.num_dessat 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige e 
					 ON a.num_despac = e.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_destin_client f 
					 ON b.nom_destin LIKE f.nom_client 
			 INNER JOIN ".BASE_DATOS.".tab_despac_cordes g 
					 ON a.num_desext = g.num_despac 
					AND g.cod_pedrem = '{$mCodPedrem}' 
				  WHERE a.num_despac != '{$mNumDespac}' 
					
					AND g.num_docume IN (		SELECT x.num_docume 
												  FROM ".BASE_DATOS.".tab_despac_cordes x 
											INNER JOIN ".BASE_DATOS.".tab_despac_corona y 
													ON x.num_despac = y.num_despac 
												 WHERE y.num_dessat = '{$mNumDespac}' 
												   AND x.cod_pedrem = '{$mCodPedrem}' 
											  GROUP BY x.num_docume 
										)
			   GROUP BY a.num_despac ";*/

		$mSql = "SELECT a.num_despac AS num_desext, b.num_dessat AS num_despac, 
						a.num_destin, a.nom_destin, c.fec_citcar, b.nom_estado, 
						d.ind_xdunox, b.cod_manifi, b.num_solici, a.num_destin1, 
						GROUP_CONCAT(a.num_docume SEPARATOR ' ' ) AS num_docume, 
						DATE_FORMAT(c.fec_creaci, '%H:%i %d-%m-%Y') AS fec_creaci, 
						IF(c.fec_llegad = '0000-00-00 00:00:00', '', c.fec_llegad) AS fec_llegad, 
						IF(b.fec_salida = '0000-00-00 00:00:00', '', b.fec_salida) AS fec_salpla 
				   FROM ".BASE_DATOS.".tab_despac_cordes a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_corona b 
					 ON a.num_despac = b.num_despac 
					AND a.cod_pedrem = '{$mCodPedrem}' 
					AND a.num_docume IN (		SELECT x.num_docume 
												  FROM ".BASE_DATOS.".tab_despac_cordes x 
											INNER JOIN ".BASE_DATOS.".tab_despac_corona y 
													ON x.num_despac = y.num_despac 
												 WHERE y.num_dessat = '{$mNumDespac}' 
												   AND x.cod_pedrem = '{$mCodPedrem}' 
											  GROUP BY x.num_docume 
										) 
			 INNER JOIN ".BASE_DATOS.".tab_despac_despac c 
					 ON b.num_dessat = c.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_destin d 
					 ON b.num_dessat = d.num_despac 
				  WHERE b.num_dessat != '{$mNumDespac}' 
				";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');

		if( $mResult[0]['num_despac'] == '' )
			$mResult = array();

		if( sizeof($mResult) < 1 )
		{
			$mSql = "SELECT a.num_docume, a.num_destin, a.nom_destin, a.num_destin1 
					   FROM ".BASE_DATOS.".tab_despac_cordes a 
					  WHERE a.num_despac = '{$mNumDespac}' 
						AND a.cod_pedrem = '{$mCodPedrem}' 
				   GROUP BY a.num_docume ";
			$mConsult = new Consulta( $mSql, self::$cConexion );
			$mResult = $mConsult -> ret_matrix('a');
		}
		
		return $mResult[0];
	}

	/*! \fn: getHorarioCiudad
	 *  \brief: Trae el horario configurado para la transportadora y centro de distribucion
	 *  \author: Ing. Fabian Salinas
	 *  \date: 30/09/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mCodTransp  Integer  Codigo de la transportadora
	 *  \param: mCodCiudad  Integer  Codigo de la ciudad del centro de distribucion
	 *  \return: Matriz
	 */
	private function getHorarioCiudad( $mCodTransp, $mCodCiudad )
	{
		$mSql = "SELECT a.com_diasxx, a.hor_ingres, a.hor_salida 
				   FROM ".BASE_DATOS.".tab_config_horlab a 
				  WHERE a.cod_tercer = '{$mCodTransp}' 
					AND a.cod_ciudad = '{$mCodCiudad}' ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('a');
	}

	/*! \fn: horarioCiudad
	 *  \brief: Crea la matriz por dias del horario configurado para la transportadora y centro de distribucion 
	 *  \author: Ing. Fabian Salinas
	 *  \date: 30/09/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mCodTransp  Integer  Codigo de la transportadora
	 *  \param: mCodCiudad  Integer  Codigo de la ciudad del centro de distribucion
	 *  \return: Matriz
	 */
	private function horarioCiudad( $mCodTransp, $mCodCiudad )
	{
		$mHorarios = self::getHorarioCiudad( $mCodTransp, $mCodCiudad );

		$mHorario = array();
		foreach ($mHorarios as $row){
			$mDias = explode('|', $row['com_diasxx']);

			foreach ($mDias as $key => $dia)
				$mHorario[$dia] = array( "hor_ingres"=>$row['hor_ingres'], "hor_salida"=>$row['hor_salida'] );
		}

		return $mHorario;
	}

	/*! \fn: getFestTranspCiudad
	 *  \brief: Trae los dias festivos parametrizados para la transportadora y centro de distribucion 
	 *  \author: Ing. Fabian Salinas
	 *  \date: 30/09/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mCodTransp  Integer  Codigo de la transportadora
	 *  \param: mCodCiudad  Integer  Codigo de la ciudad del centro de distribucion
	 *  \param: mFecInicia  Date     Fecha Inicial
	 *  \param: mFecFinali  Date     Fecha Final
	 *  \return: Matriz
	 */
	private function getFestTranspCiudad( $mCodTransp, $mCodCiudad = '1', $mFecInicia = null, $mFecFinali = null )
	{
		$mSql = "SELECT a.fec_festiv 
				   FROM ".BASE_DATOS.".tab_config_festiv a 
				  WHERE a.cod_tercer = '{$mCodTransp}' 
					AND a.cod_ciudad = '{$mCodCiudad}' 
				".($mFecInicia == null ? "" : " AND a.fec_festiv >= '{$mFecInicia}' " )."
				".($mFecFinali == null ? "" : " AND a.fec_festiv <= '{$mFecFinali}' " )."
				";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mMatriz = $mConsult -> ret_matrix('i');

		$mResult = array();
		foreach ($mMatriz as $row)
			$mResult[$row[0]] = '1';
		
		return $mResult;
	}

	/*! \fn: formatDay
	 *  \brief: cambia el numero del dia en la semana 1 al 7 a su equivalente en letra inicial
	 *  \author: Ing. Fabian Salinas
	 *  \date:  30/09/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mNumDia  Integer  Numero del dia en la semana
	 *  \return: String
	 */
	private function formatDay( $mNumDia )
	{
		switch ($mNumDia){
			case '1':
				$mDay = 'L';
				break;
			case '2':
				$mDay = 'M';
				break;
			case '3':
				$mDay = 'X';
				break;
			case '4':
				$mDay = 'J';
				break;
			case '5':
				$mDay = 'V';
				break;
			case '6':
				$mDay = 'S';
				break;
			case '7':
				$mDay = 'D';
				break;
		}
		return $mDay;
	}

	/*! \fn: getDespacXD1
	 *  \brief: Trae los despachos XDTramo1 pendientes por verificar alarma
	 *  \author: Ing. Fabian Salinas
	 *  \date:  02/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	public function getDespacXD1()
	{
		$mSql = "SELECT a.num_despac, b.num_docume, 
						c.nom_estado, d.num_desext, 
						e.cod_pedrem, 
						x.num_desext AS XD2_num_desext, 
						x.num_despac AS XD2_num_despac, 
						x.ind_xdunox AS XD2_ind_xdunox,
						x.fec_creaci AS XD2_fec_creaci, 
						x.fec_llegad AS XD2_fec_llegad, 
						x.fec_salpla AS XD2_fec_salpla 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_destin b 
					 ON a.num_despac = b.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_corona c 
					 ON a.num_despac = c.num_dessat 
			 INNER JOIN ".BASE_DATOS.".tab_despac_sisext d 
					 ON a.num_despac = d.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_cordes e 
					 ON d.num_desext = e.num_despac 
			  LEFT JOIN (
							 SELECT a.num_desext, a.num_despac, b.ind_xdunox, 
									b.num_docume, e.cod_pedrem, 
									DATE_FORMAT(c.fec_creaci, '%H:%i %d-%m-%Y') AS fec_creaci, 
									IF(c.fec_llegad = '0000-00-00 00:00:00', '', c.fec_llegad) AS fec_llegad, 
									IF(d.fec_salida = '0000-00-00 00:00:00', '', d.fec_salida) AS fec_salpla 
							   FROM ".BASE_DATOS.".tab_despac_sisext a 
						  LEFT JOIN ".BASE_DATOS.".tab_despac_destin b 
								 ON a.num_despac = b.num_despac 
						 INNER JOIN ".BASE_DATOS.".tab_despac_despac c 
								 ON a.num_despac = c.num_despac 
						 INNER JOIN ".BASE_DATOS.".tab_despac_corona d 
								 ON a.num_despac = d.num_dessat 
						 INNER JOIN ".BASE_DATOS.".tab_despac_cordes e 
								 ON a.num_desext = e.num_despac 
							  WHERE c.ind_anulad = 'R' 
								AND c.cod_tipdes = '6' 
						   GROUP BY e.cod_pedrem 
						) x 
					 ON e.cod_pedrem = x.cod_pedrem 
				  WHERE a.cod_tipdes = '5' 
					AND b.ind_xdunox = '0' 
					AND a.ind_anulad = 'R' 
					AND (a.fec_llegad IS NOT NULL OR a.fec_llegad != '0000-00-00 00:00:00' ) 
					AND b.num_docume IS NOT NULL 
					AND b.num_docume != '' 
			   GROUP BY a.num_despac, e.cod_pedrem 
				";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('a');
	}

	/*! \fn: getDespacXD2
	 *  \brief: Trae la lista de despachos XDTramo2 pendientes por actualizar ind_xdunox que tengan fecha de salida de planta o llegada
	 *  \author: Ing. Fabian Salinas
	 *  \date: 05/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: String
	 */
	public function getDespacXD2()
	{
		$mSql = "SELECT GROUP_CONCAT(a.num_despac SEPARATOR ',' ) AS lis_despac 
			   FROM ".BASE_DATOS.".tab_despac_despac a 
		 INNER JOIN ".BASE_DATOS.".tab_despac_corona b 
				 ON a.num_despac = b.num_dessat 
		 INNER JOIN ".BASE_DATOS.".tab_despac_destin c 
				 ON a.num_despac = c.num_despac 
			  WHERE a.cod_tipdes = '6' 
				AND c.ind_xdunox = '0' 
				AND ( (a.fec_llegad IS NOT NULL AND a.fec_llegad != '0000-00-00 00:00:00') OR (b.fec_salida IS NOT NULL AND b.fec_salida != '0000-00-00 00:00:00') ) ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');
		return $mResult[0]['lis_despac'];
	}

	/*! \fn: generateReport
	 *  \brief: Pinta Reporte Cross Doking
	 *  \author: Ing. Fabian Salinas
	 *  \date:  07/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function generateReport()
	{
		#<Titulares>
			$mTittle[0] = array("num_despac" => "N&uacute;mero Despacho",
								"cod_manifi" => "N&uacute;mero Manifiesto",
								"fec_despac" => "Fecha Despacho",
								"num_viajex" => "N&uacute;mero Viaje",
								"num_solici" => "Solicitud",
								"num_pedido" => "Pedido",
								"nom_produc" => "Producto",
								"val_pesoxx" => "Peso",
								"fec_llegad" => "Fecha Recibo en CLO",
								"nom_ciuori" => "Ciudad Origen",
								"nom_ciudes" => "Ciudad Destino"
							   );
			$mTittle[1] = array("num_despac" => "N&uacute;mero Despacho",
								"cod_manifi" => "N&uacute;mero Manifiesto",
								"num_desext" => "N&uacute;mero Viaje",
								"num_solici" => "Solicitud",
								"cod_pedrem" => "Cod. Pedido Remesa",
								"num_docume" => "Factura",
								"num_destin1" => "Codigo Cliente",
								"nom_destin" => "Cliente",
								"fec_citcar" => "Fecha Cita Cargue",
								"fec_salpla" => "Fecha Salida de Planta"
							   );
			$mTittle[2] = array("time_estad" => "Tiempo Estadia en CLO (Habil)",
								"time_entre" => "Tiempo Entrega Total (Calendario)"
							   );
		#</Titulares>

		$mData = self::getDataXD1();
		$mIdxTablex = "tableExcelCrossID";

		echo "<style>
				.bg1 { background-color: #EBF8E2; }
				.bg2 { background-color: #DEDFDE; }
			  </style>";

		echo '<center><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" border="0" onclick="exportTableExcel( \''.$mIdxTablex.'\' );" style="cursor:pointer"></center>';

		$mHtml = new Formlib(2);

		$mHtml->OpenDiv();
		$mHtml->SetBody('<table id="'.$mIdxTablex.'" width="100%" align="center" cellspacing="1" cellpadding="3" border="0">');
			$mHtml->Row();
				$mHtml->Label( "XD Tramo 1", array("class"=>"CellHead", "colspan"=>sizeof($mTittle[0]), "align"=>"center") );
				$mHtml->Label( "XD Tramo 2", array("class"=>"CellHead", "colspan"=>sizeof($mTittle[1]), "align"=>"center") );
				$mHtml->Label( "Tiempos", 	 array("class"=>"CellHead", "colspan"=>sizeof($mTittle[2]), "align"=>"center") );
			$mHtml->CloseRow();
			$mHtml->Row();
				for ($i=0; $i < sizeof($mTittle); $i++){#Imprime Subtitulos
					foreach ($mTittle[$i] as $key => $tittle)
						$mHtml->Label( $tittle, array("class"=>"CellHead", "align"=>"center") );
				}
			$mHtml->CloseRow();

			$x=0;
			foreach ($mData as $row)
			{
				$mCodPedrem = explode("|", $row['cod_pedrem']);
				$mCodPedrem = array_unique($mCodPedrem);
				foreach ($mCodPedrem as $value)
					$mPedrem[] = $value;
				
				$mSize = sizeof($mPedrem);
				$mBg = $x % 2 == 0 ? "bg1" : "bg2";

				$mHtml->Row();
					foreach ($mTittle[0] as $key => $tittle) #Datos XD1
						$mHtml->Label( htmlentities($row[$key]), array("class"=>"cellInfo onlyCell $mBg", "align"=>"left", "rowspan"=>$mSize) );

					for ($j=0; $j < $mSize; $j++)
					{#Recorre las Facturas
						#<Tratamiento de datos>
							$mXD2 = self::getViajeFactur( $row['num_despac'], $mPedrem[$j] );
							$mXD2['cod_pedrem'] = $mPedrem[$j];
							$mFec = $mXD2['fec_salpla'] != "" ? $mXD2['fec_salpla'] : date('Y-m-d H:i:s');
							$mTime = self::calTimeTramo1vsTramo2( $row['num_despac'], $mXD2['num_despac'] );
							$mTime['time_estad'] = $mTime[0]['hour']." Horas &nbsp;".$mTime[0]['min']." Minutos";
							$mDiff = ceil((strtotime($mFec) - strtotime($row['fec_despac'])) / 60);
							$mDiff = self::formatDayHour( $mDiff );
							$mTime['time_entre'] = $mDiff['hour']." Horas &nbsp;".$mDiff['min']." Minutos";
						#</Tratamiento de datos>

						#Imprime los datos
						if( $j!=0 )
							$mHtml->Row();

						foreach ($mTittle[1] as $key => $tittle) #Datos XD2
							$mHtml->Label( $mXD2[$key], array("class"=>"cellInfo onlyCell $mBg", "align"=>"left") );

						foreach ($mTittle[2] as $key => $tittle) #Tiempos
							$mHtml->Label( $mTime[$key], array("class"=>"cellInfo onlyCell $mBg", "align"=>"left") );

						if( $j!= $mSize-1 )
							$mHtml->CloseRow();
					}
				$mHtml->CloseRow();

				$x++;
			}

		$mHtml->SetBody('</table>');
		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: generateReport
	 *  \brief: Pinta Reporte Viajes que no pertenecen a Cross Doking
	 *  \author: Ing. Fabian Salinas
	 *  \date:  07/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function generateReport2()
	{
		#<Titulares>
			$mTittle[0] = array("num_despac" => "N&uacute;mero Despacho",
								"cod_manifi" => "N&uacute;mero Manifiesto",
								"fec_despac" => "Fecha Despacho",
								"num_viajex" => "N&uacute;mero Viaje",
								"nom_tipdes" => "Modalidad",
								"num_solici" => "Solicitud",
								"num_pedido" => "Pedido",
								"nom_produc" => "Producto",
								"val_pesoxx" => "Peso",
								"nom_ciuori" => "Ciudad Origen",
								"nom_ciudes" => "Ciudad Destino",
								"fec_citcar" => "Fecha Cita Cargue",
								"fec_salpla" => "Fecha Salida de Planta",
								"time_entre" => "Tiempo Entrega Total (Calendario)"
							   );
			$mTittle[1] = array("num_docume"  => "Facturas",
								"num_destin1" => "Codigo Cliente",
								"nom_destin"  => "Cliente"
							   );
		#</Titulares>

		$mData = self::getDataDespac( $_REQUEST['ind_pestan'] );
		$mCant = sizeof($mData);
		$mIdxTablex = "tableExcel2".$_REQUEST['ind_pestan']."ID";

		$mScript = "<style>
				.bg1 { background-color: #EBF8E2; }
				.bg2 { background-color: #DEDFDE; }
			</style>";


		$mHtml = new Formlib(2);
		
		$mHtml->SetBody($mScript);
		$mHtml->SetBody('<center><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" border="0" onclick="exportTableExcel( \''.$mIdxTablex.'\' );" style="cursor:pointer"></center><br/>');

		if( $mCant == 1 )
			$mHtml->SetBody('Se encontro 1 registro.<br/>');
		else
			$mHtml->SetBody('Se encontraron '.$mCant.' registros.<br/>');

		$mHtml->OpenDiv();

			$mHtml->SetBody('<table id="'.$mIdxTablex.'" width="100%" align="center" cellspacing="1" cellpadding="3" border="0">');
				$mHtml->Row();
					for ($i=0; $i < sizeof($mTittle); $i++){#Imprime Subtitulos
						foreach ($mTittle[$i] as $key => $tittle)
							$mHtml->Label( $tittle, array("class"=>"CellHead", "align"=>"center") );
					}
				$mHtml->CloseRow();

				$x=0;
				foreach ($mData as $row)
				{
					#<Tratamiento de datos>
						$mFactur = self::getFacturDespac( $row['num_despac'] );
						$mSize = sizeof($mFactur);
						$mBg = $x % 2 == 0 ? "bg1" : "bg2";

						$mFec = $row['fec_salpla'] != "" ? $row['fec_salpla'] : date('Y-m-d H:i:s');
						$mDiff = ceil((strtotime($mFec) - strtotime($row['fec_despac'])) / 60);
						$mDiff = self::formatDayHour( $mDiff );
						$row['time_entre'] = $mDiff['hour']." Horas  ".$mDiff['min']." Minutos";
					#</Tratamiento de datos>

					$mHtml->Row();
						foreach ($mTittle[0] as $key => $tittle) #Datos XD1
							$mHtml->Label( htmlentities($row[$key]), array("class"=>"cellInfo onlyCell $mBg", "align"=>"left", "rowspan"=>$mSize) );
							
						if( $mSize < 1 ){
							foreach ($mTittle[1] as $key => $tittle) #Datos Facturas
								$mHtml->Label( "&nbsp;", array("class"=>"cellInfo onlyCell $mBg", "align"=>"left") );
						}else
						{
							for ($j=0; $j < $mSize; $j++)
							{#Recorre las Facturas
								$mFactur[$j]['num_docume'] = str_replace(",", "", $mFactur[$j]['num_docume']);
								$mFactur[$j]['num_docume'] = str_replace(" ", "", $mFactur[$j]['num_docume']);

								if( $j!=0 )
									$mHtml->Row();

								foreach ($mTittle[1] as $key => $tittle) #Datos Facturas
									$mHtml->Label( $mFactur[$j][$key], array("class"=>"cellInfo onlyCell $mBg", "align"=>"left") );

								if( $j!= $mSize-1 )
									$mHtml->CloseRow();
							}
						}

					$mHtml->CloseRow();

					$x++;
				}

			$mHtml->SetBody('</table>');

		$mHtml->CloseDiv();

		echo $mHtml->MakeHtml();
	}

	/*! \fn: generateReportG
	 *  \brief: Pinta Reporte General Viajes difirentes a Cross Doking
	 *  \author: Ing. Fabian Salinas
	 *  \date:  23/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: 
	 */
	private function generateReportG()
	{
		$mStyle = "<style>
				.bg1 { background-color: #EBF8E2; }
				.bg2 { background-color: #DEDFDE; }
			</style>";

		if( $_REQUEST['fec_inicia'] && $_REQUEST['fec_finalx'] )
		{ #Fechas según rango filtrado
			$mFecInicia = $_REQUEST['fec_inicia'];
			$mFecFinalx = $_REQUEST['fec_finalx'];
		}else{ #Fechas sin rango filtrado
			$mData = self::getDataGeneral();
			$mFecInicia = explode(' ', $mData[0]['fec_despac']);
			$mFecFinalx = explode(' ', $mData[(sizeof($mData)-1)]['fec_despac'] );
			$mFecInicia = $mFecInicia[0];
			$mFecFinalx = $mFecFinalx[0];
		}

		$mIdxTablex = "tableExcelID";

		$mHtml = new Formlib(2);

		$mHtml->SetBody($mStyle);
		$mHtml->SetBody('<center><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" border="0" onclick="exportTableExcel( \''.$mIdxTablex.'\' );" style="cursor:pointer"></center>');

		$mHtml->OpenDiv();

		#<Tabla Totales>
		$mHtml->SetBody('<table width="70%" align="center" cellspacing="1" cellpadding="3" border="0">');
			#Titulos Tabla Totales
			$mHtml->Row();
				$mHtml->Label( "Total Despachos Generados", array("class"=>"CellHead", "align"=>"center") );
				$mHtml->Label( "Total Clientes por Despaho", array("class"=>"CellHead", "align"=>"center", "end"=>true) );

			#Contenido Tabla Totales
				$mHtml->SetBody( '<td align="center" class="cellInfo onlyCell bg1" id="totalDespac1" style="cursor: pointer" onclick="showDetail( \''.$mFecInicia.'\', \''.$mFecFinalx.'\' )">&nbsp;</td>' );
				$mHtml->Label("&nbsp;", array("class"=>"cellInfo onlyCell bg1", "align"=>"center", "id"=>"totalClient1", "end"=>true) );

			$mHtml->Row();
				$mHtml->SetBody('<td>&nbsp;</td>');
		$mHtml->CloseTable('tr');
		#</Tabla Totales>
		
		#<Tabla Fechas>
		$mHtml->SetBody('<table id="'.$mIdxTablex.'" width="100%" align="center" cellspacing="1" cellpadding="3" border="0">');
			#Titulos Tabla Fechas
				$mHtml->Label( "D&iacute;as", array("class"=>"CellHead", "align"=>"center") );
				$mHtml->Label( "Despachos Generados", array("class"=>"CellHead", "align"=>"center") );
				$mHtml->Label( "Clientes por Despacho", array("class"=>"CellHead", "align"=>"center", "end"=>true) );

			#<Recorre las fechas>
				$i=0;
				$mDate = $mFecInicia;
				while( $mDate <= $mFecFinalx )
				{
					#<Tratamiento de Datos>
						$mBg = $i % 2 == 0 ? "bg1" : "bg2";
						$mFecIni = $mDate." 00:00:00";
						$mFecFin = $mDate." 23:59:59";
						$mData = self::getDataGeneral( $mFecIni, $mFecFin );

						$mTotalDespac += $mData['can_despac'];
						$mTotalClient += $mData['can_client'];
					#</Tratamiento de Datos>

					#<Imprime Cantidades>
						$mHtml->Label( htmlentities(strftime("%A, %d de %B del %Y", strtotime($mDate))), array("class"=>"cellInfo onlyCell $mBg", "align"=>"center") );

						if( $mData['can_despac'] == 0 )
							$mHtml->Label( "-", array("class"=>"cellInfo onlyCell $mBg", "align"=>"center") );
						else
							$mHtml->SetBody( '<td class="cellInfo onlyCell '.$mBg.'" align="center" style="cursor: pointer" onclick="showDetail( \''.$mFecIni.'\', \''.$mFecFin.'\' )">'.$mData['can_despac'].'</td>' );

						$mHtml->Label( ($mData['can_client'] == 0 ? "-" : $mData['can_client']), array("class"=>"cellInfo onlyCell $mBg", "align"=>"center", "end"=>true) );
					#</Imprime Cantidades>

					$i++;
					$mDate = date ( 'Y-m-d', strtotime( '+1 day', strtotime($mDate) ) );
				}
			#</Recorre las fechas>
			
			$mHtml->Label( "Total", array("class"=>"CellHead", "align"=>"center") );
			$mHtml->SetBody( '<td align="center" class="CellHead" id="totalDespac" style="cursor: pointer" onclick="showDetail( \''.$mFecInicia.'\', \''.$mFecFinalx.'\' )">'.$mTotalDespac.'</td>' );
			$mHtml->Label( $mTotalClient, array("class"=>"CellHead", "align"=>"center", "id"=>"totalClient", "end"=>true) );

		$mHtml->SetBody('</table>');
		#</Tabla Fechas>

		$mHtml->SetBody('<center><span onclick="clearDiv();" style="color:#ffffff; cursor:pointer">[Limpiar]</span><br/></center>');

		$mHtml->OpenDiv('id:divTableID');
		$mHtml->CloseDiv();

		$mHtml->CloseDiv();
		$mHtml->SetBody('<script>printTotal();</script>');

		echo $mHtml->MakeHtml();
	}

	/*! \fn: getCiudadTransp
	 *  \brief: Trae las ciudades asociadas como origen a una transportadora
	 *  \author: Ing. Fabian Salinas
	 *  \date: 07/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mCodTransp  Integer  Codigo Transportadora
	 *  \return: Matriz
	 */
	private function getCiudadTransp( $mCodTransp = NULL )
	{
		$mSql = "SELECT a.cod_ciudad, 
						CONCAT( UPPER(b.abr_ciudad), ' (', LEFT(c.nom_depart, 4), ') - ', LEFT(d.nom_paisxx, 3) ) AS nom_ciudad
				   FROM ".BASE_DATOS.".tab_transp_origen a 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b 
					 ON a.cod_ciudad = b.cod_ciudad 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart c 
					 ON b.cod_depart = c.cod_depart 
					AND b.cod_paisxx = c.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_paises d 
					 ON b.cod_paisxx = d.cod_paisxx 
				  WHERE a.cod_transp = '".( $_REQUEST['Ajax'] == 'on' ? $_REQUEST['cod_transp'] : $mCodTransp )."' 
			   ORDER BY b.abr_ciudad ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');

		if( $_REQUEST['Ajax'] == 'on' ){
			$mHtml  = "<select class='form_01' name='cod_ciudad' id='cod_ciudadID'>";
			$mHtml .= "<option value=''>---</option>";

			foreach ($mResult as $row)
				$mHtml .= "<option value='".$row['cod_ciudad']."'>".$row['nom_ciudad']."</option>";

			$mHtml .= "</select>";

			echo $mHtml;
		}
		else
			return $mResult;
	}

	/*! \fn: getDataXD1
	 *  \brief: Trae la Data XDTramo1 para el informe
	 *  \author: Ing. Fabian Salinas
	 *  \date:  07/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getDataXD1()
	{
		$mSql = "SELECT a.num_despac, a.cod_manifi, a.fec_llegad, 
						c.num_solici, c.num_pedido, a.fec_despac, 
						a.val_pesoxx, c.num_despac AS num_viajex, 
						l.nom_produc, 
						GROUP_CONCAT(m.cod_pedrem SEPARATOR '|') AS cod_pedrem, 
						GROUP_CONCAT(b.num_docume SEPARATOR '|') AS num_docume, 
						CONCAT( UPPER(d.abr_ciudad), ' (', LEFT(e.nom_depart, 4), ') - ', LEFT(f.nom_paisxx, 3) ) AS nom_ciuori, 
						CONCAT( UPPER(g.abr_ciudad), ' (', LEFT(h.nom_depart, 4), ') - ', LEFT(i.nom_paisxx, 3) ) AS nom_ciudes 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_destin b 
					 ON a.num_despac = b.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_corona c 
					 ON a.num_despac = c.num_dessat 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart e 
					 ON d.cod_depart = e.cod_depart 
					AND d.cod_paisxx = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_paises f 
					 ON d.cod_paisxx = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad g 
					 ON a.cod_ciudes = g.cod_ciudad 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart h 
					 ON g.cod_depart = h.cod_depart 
					AND g.cod_paisxx = h.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_paises i 
					 ON g.cod_paisxx = i.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige j 
					 ON a.num_despac = j.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_sisext k 
					 ON a.num_despac = k.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_genera_produc l 
					 ON k.cod_mercan = l.cod_produc 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_cordes m 
					 ON c.num_despac = m.num_despac 
				  WHERE a.cod_tipdes = '5' 
					AND a.ind_anulad = 'R' 
					AND b.num_docume IS NOT NULL 
					AND b.num_docume != '' 
					AND j.cod_transp = '".$_REQUEST['cod_transp']."'
				";
		$mSql .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = '".$_REQUEST['num_despac']."' ";
		$mSql .= !$_REQUEST['num_manifi'] ? "" : " AND a.cod_manifi = '".$_REQUEST['num_manifi']."' ";
		$mSql .= !$_REQUEST['num_viajex'] ? "" : " AND c.num_despac = '".$_REQUEST['num_viajex']."' ";
		$mSql .= !$_REQUEST['num_placax'] ? "" : " AND j.num_placax = '".$_REQUEST['num_placax']."' ";
		$mSql .= !$_REQUEST['cod_ciudad'] ? "" : " AND a.cod_ciudes = '".$_REQUEST['cod_ciudad']."' ";
		$mSql .= !$_REQUEST['cod_tiptra'] ? "" : " AND k.tip_transp = '".$_REQUEST['cod_tiptra']."' ";
		$mSql .= !$_REQUEST['cod_produc'] ? "" : " AND k.cod_mercan IN (".$_REQUEST['cod_produc'].") ";
		$mSql .= (!$_REQUEST['fec_inicia'] AND !$_REQUEST['fec_finali']) ? "" : " AND DATE(a.fec_despac) BETWEEN '".$_REQUEST['fec_inicia']."' AND '".$_REQUEST['fec_finali']."' ";

		$mSql .= " GROUP BY a.num_despac ORDER BY a.fec_despac ASC ";

		$mConsult = new Consulta( $mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('a');
	}

	/*! \fn: getDataDespac
	 *  \brief: Trae la data de los despacho segun tipo de despacho
	 *  \author: Ing. Fabian Salinas
	 *  \date:  16/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	private function getDataDespac( $mCodTipdes = null )
	{
		$mSql = "SELECT a.num_despac, a.cod_manifi, a.fec_llegad, 
						c.num_solici, c.num_pedido, a.fec_despac, 
						a.val_pesoxx, c.num_despac AS num_viajex, 
						l.nom_produc, a.fec_citcar, m.nom_tipdes, 
						IF(c.fec_salida = '0000-00-00 00:00:00', '', c.fec_salida) AS fec_salpla, 
						CONCAT( UPPER(d.abr_ciudad), ' (', LEFT(e.nom_depart, 4), ') - ', LEFT(f.nom_paisxx, 3) ) AS nom_ciuori, 
						CONCAT( UPPER(g.abr_ciudad), ' (', LEFT(h.nom_depart, 4), ') - ', LEFT(i.nom_paisxx, 3) ) AS nom_ciudes 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_corona c 
					 ON a.num_despac = c.num_dessat 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart e 
					 ON d.cod_depart = e.cod_depart 
					AND d.cod_paisxx = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_paises f 
					 ON d.cod_paisxx = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad g 
					 ON a.cod_ciudes = g.cod_ciudad 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart h 
					 ON g.cod_depart = h.cod_depart 
					AND g.cod_paisxx = h.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_paises i 
					 ON g.cod_paisxx = i.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige j 
					 ON a.num_despac = j.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_sisext k 
					 ON a.num_despac = k.num_despac 
			  LEFT JOIN ".BASE_DATOS.".tab_genera_produc l 
					 ON k.cod_mercan = l.cod_produc 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes m 
					 ON a.cod_tipdes = m.cod_tipdes 
				  WHERE a.ind_anulad = 'R' 
					AND j.cod_transp = '".$_REQUEST['cod_transp']."' ";

		$mSql .= !$mCodTipdes ? " AND a.cod_tipdes NOT IN (5, 6) " : " AND a.cod_tipdes = '".$mCodTipdes."' ";
		$mSql .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = '".$_REQUEST['num_despac']."' ";
		$mSql .= !$_REQUEST['num_manifi'] ? "" : " AND a.cod_manifi = '".$_REQUEST['num_manifi']."' ";
		$mSql .= !$_REQUEST['num_viajex'] ? "" : " AND c.num_despac = '".$_REQUEST['num_viajex']."' ";
		$mSql .= !$_REQUEST['num_placax'] ? "" : " AND j.num_placax = '".$_REQUEST['num_placax']."' ";
		$mSql .= !$_REQUEST['cod_tiptra'] ? "" : " AND k.tip_transp = '".$_REQUEST['cod_tiptra']."' ";
		$mSql .= !$_REQUEST['cod_produc'] ? "" : " AND k.cod_mercan IN (".$_REQUEST['cod_produc'].") ";
		$mSql .= (!$_REQUEST['fec_inicia'] AND !$_REQUEST['fec_finali']) ? "" : " AND DATE(a.fec_despac) BETWEEN '".$_REQUEST['fec_inicia']."' AND '".$_REQUEST['fec_finali']."' ";

		$mSql .= " GROUP BY a.num_despac ORDER BY a.fec_despac ASC ";

		$mConsult = new Consulta( $mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('a');
	}

	/*! \fn: getDataGeneral
	 *  \brief: Trae la data del informe general
	 *  \author: Ing. Fabian Salinas
	 *  \date: 26/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mFecInicia  Date     Fecha Inicial
	 *  \param: mFecFinali  Date     Fecha Final
	 *  \return: Matriz
	 */
	private function getDataGeneral( $mFecInicia = null, $mFecFinali = null )
	{
		$mSql = "SELECT a.num_despac, a.fec_despac 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_corona c 
					 ON a.num_despac = c.num_dessat 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige j 
					 ON a.num_despac = j.num_despac 
			 INNER JOIN ".BASE_DATOS.".tab_despac_sisext k 
					 ON a.num_despac = k.num_despac 
				  WHERE a.ind_anulad = 'R' 
					AND a.cod_tipdes NOT IN (5, 6) 
					AND j.cod_transp = '".$_REQUEST['cod_transp']."' ";

		$mSql .= !$_REQUEST['num_despac'] ? "" : " AND a.num_despac = '".$_REQUEST['num_despac']."' ";
		$mSql .= !$_REQUEST['num_manifi'] ? "" : " AND a.cod_manifi = '".$_REQUEST['num_manifi']."' ";
		$mSql .= !$_REQUEST['num_viajex'] ? "" : " AND c.num_despac = '".$_REQUEST['num_viajex']."' ";
		$mSql .= !$_REQUEST['num_placax'] ? "" : " AND j.num_placax = '".$_REQUEST['num_placax']."' ";
		$mSql .= !$_REQUEST['cod_tiptra'] ? "" : " AND k.tip_transp = '".$_REQUEST['cod_tiptra']."' ";
		$mSql .= !$_REQUEST['cod_produc'] ? "" : " AND k.cod_mercan IN (".$_REQUEST['cod_produc'].") ";

		if( $mFecInicia == null && $mFecFinali == null )
			$mSql .= (!$_REQUEST['fec_inicia'] AND !$_REQUEST['fec_finali']) ? "" : " AND DATE(a.fec_despac) BETWEEN '".$_REQUEST['fec_inicia']."' AND '".$_REQUEST['fec_finali']."' ";
		else
			$mSql .= " AND DATE(a.fec_despac) BETWEEN '".$mFecInicia."' AND '".$mFecFinali."' ";
		$mSql .= " GROUP BY a.num_despac ORDER BY a.fec_despac ASC ";

		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mDespac = $mConsult -> ret_matrix('a');

		if( $mFecInicia == null && $mFecFinali == null )
			return $mDespac;

		$mResult['can_despac'] = sizeof($mDespac);
		$mResult['can_client'] = 0;

		foreach ($mDespac as $row)
		{
			$mSql = "SELECT a.nom_destin 
					   FROM ".BASE_DATOS.".tab_despac_destin a 
					  WHERE a.num_despac = '".$row['num_despac']."' 
				   GROUP BY a.nom_destin ";
			$mConsult = new Consulta( $mSql, self::$cConexion );
			$mData = $mConsult -> ret_matrix('a');
			$mResult['can_client'] += sizeof($mData);
		}

		return $mResult;
	}

	/*! \fn: getProduc
	 *  \brief: Trae el listado de productos
	 *  \author: Ing. Fabian Salinas
	 *  \date: 15/10/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: 
	 *  \return: Matriz
	 */
	public function getProduc()
	{
		$mSql = "SELECT a.cod_produc, a.nom_produc 
				   FROM ".BASE_DATOS.".tab_genera_produc a 
				  WHERE a.ind_estado = '1' ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('i');
	}

	/*! \fn: getTipoDespac
	 *  \brief: Trae los Tipos de Despacho Diferentes a Tramo1 y Tramo2
	 *  \author: Ing. Fabian Salinas
	 *	\date: 15/10/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: 
	 */
	public function getTipoDespac()
	{
		$mSql = " SELECT cod_tipdes, UPPER(nom_tipdes) 
					FROM ".BASE_DATOS.".tab_genera_tipdes 
				   WHERE cod_tipdes NOT IN ( 5,6 ) 
				ORDER BY nom_tipdes ASC ";
		$mConsult = new Consulta($mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('i');
	}

    /*! \fn: divCrossDoking
     *  \brief: Pinta contenido DIV de Cross Doking en el encabezado
     *  \author: Ing. Fabian Salinas
     *  \date: 24/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mNumDespac  Integer  Numero del despacho
     *  \return: 
     */
    private function divCrossDoking( $mNumDespac )
    {
    	$mSql = " SELECT a.cod_tipdes 
		            FROM ".BASE_DATOS.".tab_despac_despac a 
		           WHERE a.num_despac = '{$mNumDespac}' 
		        	 AND a.cod_tipdes IN (5,6) ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		$mCodTipdes = $mConsult -> ret_arreglo();

		if( sizeof($mCodTipdes) > 0 )
		{
	        $mData = self::getPedidoRemisi( $mNumDespac );
	        $mSize = sizeof($mData);
	        $mTittle = array( "Cod. Pedido Remisi&oacute;n", "No. de Viaje", "No. Factura", "Fecha de Creaci&oacute;n", "Estado", "Tiempo Estad&iacute;a H&aacute;bil" );

	        $mHtml = new Formlib(2);

			$mHtml->Table('tr');

			#Imprime titulares de la tabla
			foreach ($mTittle as $key => $tittle)
				$mHtml->Label( $tittle, array("align"=>"center", "class"=>"cellTitu", "width"=>"20%") );
			$mHtml->CloseRow();

			for( $i=0; $i<$mSize; $i++ )
			{
				$mViajes = self::getViajeFactur( $mNumDespac, $mData[$i]['cod_pedrem'] );

				if( sizeof($mViajes) > 0 )
				{
					if( $mCodTipdes[0] == '5' ){
						$mTramo1 = $mNumDespac;
						$mTramo2 = $mViajes['num_despac'];
					}else{
						$mTramo1 = $mViajes['num_despac'];
						$mTramo2 = $mNumDespac;
					}

					$mTime = self::calTimeTramo1vsTramo2( $mTramo1, $mTramo2 );
					$mTxt = $mTime[0]['hour']." Horas &nbsp;".$mTime[0]['min']." Minutos";
					
					$mViaje = '<a style="color:#000000;" href="index.php?cod_servic=1385&window=central&despac='.$mViajes['num_despac'].'&opcion=2">'.$mViajes['num_desext'].'</a>';
				}

				#Imprime Registros
				$mHtml->Row();
				$mHtml->Label( $mData[$i]['cod_pedrem'], array("align"=>"left", "class"=>"cell", "width"=>"20%") );
				$mHtml->Label( $mViaje, array("align"=>"left", "class"=>"cell", "width"=>"20%") );
				$mHtml->Label( $mViajes['num_docume'],   array("align"=>"left", "class"=>"cell", "width"=>"20%") );
				$mHtml->Label( $mViajes['fec_creaci'],   array("align"=>"left", "class"=>"cell", "width"=>"20%") );
				$mHtml->Label( $mViajes['fec_creaci'], array("align"=>"left", "class"=>"cell", "width"=>"20%") );
				$mHtml->Label( $mTxt, array("align"=>"right", "class"=>"cell", "width"=>"20%") );

				if( $i != ($mSize-1) )
					$mHtml->CloseRow();
			}

			$mHtml->CloseTable('tr');

	        echo $mHtml->MakeHtml();
	    }
	}

	/*! \fn: getPedidoRemisi
	 *  \brief: Trae codigo pedido remision de un despacho
	 *  \author: Ing. Fabian Salinas
	 *  \date: 25/11/2015
	 *  \date modified: dd/mm/aaaa
	 *  \modified by: 
	 *  \param: mNumDespac  Integer  Numero del despacho
	 *  \return: Matriz
	 */
	private function getPedidoRemisi( $mNumDespac = null )
	{
		$mSql = "SELECT a.cod_pedrem 
				   FROM ".BASE_DATOS.".tab_despac_cordes a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_corona b 
					 ON a.num_despac = b.num_despac 
				  WHERE b.num_dessat = '$mNumDespac'
			   GROUP BY a.cod_pedrem ";
		$mConsult = new Consulta( $mSql, self::$cConexion );
		return $mResult = $mConsult -> ret_matrix('a');
	}
}

if($_REQUEST['Ajax'] === 'on' )
	$_INFORM = new ClassCrossDoking();

?>