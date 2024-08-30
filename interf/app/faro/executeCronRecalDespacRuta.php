<?php

/*! \file: executeCronRecalDespacRuta.php
 *  \brief: Recalcula el plan de ruta cuando el despacho tiene fecha salida del sistema o fecha salida de planta(Corona)
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 20/05/2015
 *  \bug: 
 *  \warning: 
 */
#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

ini_set('memory_limit', '4096M');
ini_set('max_execution_time', '1200');

include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );		//Constantes generales.
include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" );	//Funciones generales.
include_once( '/var/www/html/ap/interf/lib/Mail.class.php' );
include_once( "/var/www/html/ap/satt_faro/constantes.inc" );	#WEB 7
#include_once( "/var/www/html/ap/satt_faro/constantes.inc" );		#WEB 10

/*! \class: RecalculoRuta
 *  \brief: 
 */
class RecalculoRuta
{
	var $conexion, 
		$Except, 
		$usuario;

	function __construct( $us )
	{
		$this -> Except = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
		$this -> Except -> SetUser( 'GetDespacCorona' );
		$this -> Except -> SetParams( "Faro", "Cron para Consumir los Servicios Consulta de Corona" );
		$this -> conexion = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BASE_DATOS ), $this -> Except );
		echo "<pre>"; print_r( Hostxx ); echo "</pre>";
		echo "<pre>"; print_r( USUARIO ); echo "</pre>";
		echo "<pre>"; print_r( CLAVE ); echo "</pre>";
		echo "<pre>"; print_r( BASE_DATOS ); echo "</pre>";
		$this -> usuario = $us;
		RecalculoRuta::Principal();
	}

	/*! \fn: Principal
	 *  \brief: Trae todos los despachos que tengan fecha salida del sistema o fecha salida cargue(Corona)
	 *  		ind_recrut (Indicador recalculo ruta ejecutado ) 
	 *			ind_recrut = 0 (Sin actualizar plan de ruta segun fecha salida)
	 *			ind_recrut = 1 (Despacho de cualquier empresa incluida Corona actualizado por fecha salida del sistema )
	 *			ind_recrut = 2 (Despacho de Corona Actualizado por fecha de salida de planta )
	 *  \author: Ing. Fabian Salinas
	 *	\date: 20/05/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function Principal()
	{
		/*! \var: $mSql
		 *  \brief: Query 1: Trae despachos que tengan fecha salida del sistema con ind_recrut = 0
		 *  		Query 2: Trae despachos que tengan fehca salida de planta(Corona) con ind_recrut = 0
		 *  		Query 3: Trae despachos que tengan fehca salida de planta(Corona) con ind_recrut = 1
		 *  \warning: precausiones que puede generar la variable
		 */
		$mSql = " SELECT x.num_despac, x.fec_sistem, x.fec_planta 
					FROM (
							(
								 SELECT a.num_despac, a.fec_salsis AS fec_sistem, 
										'' AS fec_planta , b.cod_transp
								   FROM ".BASE_DATOS.".tab_despac_despac a 
							 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
									 ON a.num_despac = b.num_despac 
								  WHERE a.ind_anulad = 'R'
								  	AND b.ind_activo = 'S' 
								  	AND a.ind_planru = 'S' 
								  	AND b.ind_recrut = 0 
								  	AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
						  			AND a.fec_salsis IS NOT NULl 
						  			AND a.fec_salsis != '0000-00-00 00:00:00' 
							)
							UNION 
							(
								 SELECT a.num_despac, a.fec_salsis AS fec_sistem, 
										c.fec_salida AS fec_planta , b.cod_transp
								   FROM ".BASE_DATOS.".tab_despac_despac a 
							 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
									 ON a.num_despac = b.num_despac 
							 INNER JOIN ".BASE_DATOS.".tab_despac_corona c 
									 ON a.num_despac = c.num_dessat 
								  WHERE a.ind_anulad = 'R'
								  	AND b.ind_activo = 'S' 
								  	AND a.ind_planru = 'S' 
								  	AND b.ind_recrut = 0 
								  	AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
						  			AND c.fec_salida IS NOT NULl 
						  			AND c.fec_salida != '0000-00-00 00:00:00' 
							)
							UNION 
							(
								 SELECT a.num_despac, a.fec_salsis AS fec_sistem, 
										c.fec_salida AS fec_planta , b.cod_transp
								   FROM ".BASE_DATOS.".tab_despac_despac a 
							 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
									 ON a.num_despac = b.num_despac 
							 INNER JOIN ".BASE_DATOS.".tab_despac_corona c 
									 ON a.num_despac = c.num_dessat 
								  WHERE a.ind_anulad = 'R'
								  	AND b.ind_activo = 'S' 
								  	AND a.ind_planru = 'S' 
								  	AND b.ind_recrut = 1 
								  	AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
								  	AND c.fec_salida IS NOT NULl 
								  	AND c.fec_salida != '0000-00-00 00:00:00' 
							)
						) x
					GROUP BY x.num_despac LIMIT 100
				";
				echo "<pre>"; print_r( $mSql ); echo "</pre>";
		$this -> conexion -> ExecuteCons( $mSql );
		$mArrayDespac = $this -> conexion -> RetMatrix( 'a'  );
 
		
		echo "<pre>"; print_r( $mArrayDespac ); echo "</pre>";
		echo "<pre>";
		echo "Despachos por actualizar plan de ruta: ".sizeof($mArrayDespac);
		echo "</pre>";

		foreach ($mArrayDespac as $row ) {
			RecalculoRuta::Recalculo( $row );
		}

	}

	/*! \fn: Recalculo
     *  \brief: 
     *  \author: Ing. Fabian Salinas
     *  \date: 20/05/2015
     *  \date modified: dia/mes/año
     *  \param: mDespac
     *  \return: 
     */
	private function Recalculo( $mDespac )
	{
		echo "<pre>";
		echo "<hr/>";
		print_r($mDespac);
		
		$mSql = "SELECT a.cod_contro, a.cod_rutasx, a.fec_planea, 
						b.val_duraci
				   FROM ".BASE_DATOS.".tab_despac_seguim a 
			 INNER JOIN ".BASE_DATOS.".tab_genera_rutcon b 
					 ON a.cod_contro = b.cod_contro 
					AND a.cod_rutasx = b.cod_rutasx
					AND a.ind_estado != '2'
				  WHERE a.num_despac = '".$mDespac[num_despac]."' 
			   ORDER BY b.val_duraci ";
		$this -> conexion -> ExecuteCons( $mSql );
		$mData = $this -> conexion -> RetMatrix( 'a' );

		if( $mDespac[fec_planta] != NULL ){
			$mFecSalida = $mDespac[fec_planta];
			$mIndRecrut = '2';
		}else{
			$mFecSalida = $mDespac[fec_sistem];
			$mIndRecrut = '1';
		}

		/*! \var: $mFecSalida
		 *  \brief: Esta variable calcula la fecha programada del primer puesto de control 
		 *  		aumentando el tiempo con respecto al val_duraci del puesto de control
		 *  \warning: Si no suma bien el val_duraci puede encontrar diferencias en la fecha y hora
		 *  		  de salida de sistema vs fecha programada del primer puesto de control
		 *  		  esto genera actualizaciones del pland e ruta no necesarias
		 */
		$mMin = "+".$mData[0][val_duraci]." minute";
		$mFecSalida = strtotime( $mMin, strtotime ( $mFecSalida ) );
		$mFecSalida = date ( 'Y-m-d H:i:s', $mFecSalida );
		
		if( $mData[0][fec_planea] != $mFecSalida )
		{
			echo "</br>Fecha diferente</br>";
			$mValNoveda = RecalculoRuta::verifyNovedades( $mDespac[num_despac] );
			
			for( $i=0; $i<sizeof($mData); $i++ )
			{
				echo "</br>";
				print_r($mData[$i]);

				if($i == 0){
					$mFecNew = $mFecSalida;
				}else{
					$mMin = $mData[$i][val_duraci] - $mData[($i-1)][val_duraci];
					#$mMin = "+".$mMin." minute";
					#$mFecNew = strtotime( $mMin, strtotime ( $mFecNew ) );
					$mFecNew = date ( 'Y-m-d H:i:s', strtotime( "+".$mMin." minute" , strtotime ( $mFecNew ) ) );
				}

				if(!$mValNoveda){
					$mSql1 = "fec_alarma = '".$mFecNew."', ";
				}

				echo "</br>".$mSql = "UPDATE tab_despac_seguim 
										 SET fec_planea = '".$mFecNew."', 
											 ".$mSql1."
											 usr_modifi = '".$this -> usuario."', 
											 fec_modifi = NOW() 
									   WHERE num_despac = '".$mDespac[num_despac]."' 
										 AND cod_contro = '".$mData[$i][cod_contro]."'
										 AND cod_rutasx = '".$mData[$i][cod_rutasx]."' ";
				#$this -> conexion -> ExecuteCons( $mSql, "R" ) ;
			}

			echo "</br>".$mSql = "UPDATE tab_despac_vehige 
									 SET fec_llegpl = '".$mFecNew."', 
										 usr_modifi = '".$this -> usuario."', 
										 fec_modifi = NOW() 
								   WHERE num_despac = '".$mDespac[num_despac]."' ";
			#$this -> conexion -> ExecuteCons( $mSql, "R" ) ;

			RecalculoRuta::updIndRecrut( $mDespac[num_despac], $mIndRecrut );
			echo "</br>Cambio Indicador Actualizacion Ruta";
		}
		else
		{
			echo "</br>Fechas Iguales";
			RecalculoRuta::updIndRecrut( $mDespac[num_despac], $mIndRecrut );
			echo "</br>Cambio Indicador Actualizacion Ruta";
		}
		echo "</pre>";
	}

	/*! \fn: updIndRecrut
	 *  \brief: Actualiza el indicador ind_recrut
	 *  \author: Ing. Fabian Salinas
	 *	\date: 20/05/2015
	 *	\date modified: dia/mes/año
	 *  \param: NumDespac
	 *  \param: IndRecrut
	 *  \return:
	 */
	private function updIndRecrut($mNumDespac, $mIndRecrut)
	{
		echo "</br>".$mSql = "UPDATE ".BASE_DATOS.".tab_despac_vehige 
								 SET ind_recrut = '".$mIndRecrut."', 
								 	 usr_modifi = '".$this -> usuario."', 
								 	 fec_modifi = NOW()
							   WHERE num_despac = '".$mNumDespac."' ";
		#$this -> conexion -> ExecuteCons( $mSql, "R" ) ;
	}

	/*! \fn: verifyNovedades
	 *  \brief: Verifica si el despacho tiene novedades
	 *  \author: Ing. Fabian Salinas
	 *	\date: 20/05/2015
	 *	\date modified: dia/mes/año
	 *  \param: NumDespac
	 *  \return: True(Tiene Novedades) o False(No Tiene Novedades)
	 */
	private function verifyNovedades($mNumDespac)
	{
		$mSql = "	(
						 SELECT a.num_despac 
						   FROM ".BASE_DATOS.".tab_despac_noveda a 
						  WHERE a.num_despac = '".$mNumDespac."' 
					   GROUP BY a.num_despac 
					)
					UNION 
					(
						 SELECT a.num_despac 
						   FROM ".BASE_DATOS.".tab_despac_contro a 
						  WHERE a.num_despac = '".$mNumDespac."' 
					   GROUP BY a.num_despac 
					) ";
		$this -> conexion -> ExecuteCons( $mSql );
		$mResult = $this -> conexion -> RetMatrix( 'a' );

		if($mResult != NULL)
			return true;
		else
			return false;
	}

}

$_INFORM = new RecalculoRuta( "CronRecalDespacRuta" );

?>