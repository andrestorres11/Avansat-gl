<?php
/*! \file: class_rutasx_rutasx.php
 *  \brief: Script para realizar Consultas estandar para rutas
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 05/08/2015
 *  \bug: 
 *  \warning: 
 */

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

#header('Content-Type: text/html; charset=UTF-8');
#ini_set('memory_limit', '2048M');

#date_default_timezone_get('America/Bogota');

/*! \class: rutas
 *  \brief: Realizar Consultas estandar para rutas
 */
class rutas
{
	private static 	$cConexion,
					$cCodAplica,
					$cUsuario,
					$cNull = array( array('', '-----') );

	function __construct($co = null, $us = null, $ca = null)
	{
		if($_REQUEST[Ajax] === 'on' ){
			@include_once( "../lib/ajax.inc" );
			@include_once( "../lib/general/constantes.inc" );
			rutas::$cConexion = $AjaxConnection;
		}else{
			rutas::$cConexion = $co;
			rutas::$cUsuario = $us;
			rutas::$cCodAplica = $ca;
		}

		if($_REQUEST[Ajax] === 'on' )
		{
			switch($_REQUEST[Option])
			{
				case 'getCiudades':
					rutas::getCiudades();
					break;

				case 'getPC':
					rutas::getPC();
					break;

				case 'getRutas':
					rutas::getRutas();
					break;

				case 'getTransp':
					rutas::getTransp();
					break;

				default:
					header('Location: index.php?window=central&cod_servic=1366&menant=1366');
					break;
			}
		}
	}

	/*! \fn: getCiudades
	 *  \brief: Trae las ciudades
	 *  \author: Ing. Fabian Salinas
	 *	\date: 05/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: Matriz
	 */
	public function getCiudades( $mCodCiudad = NULL )
	{
		$mSql = "SELECT a.cod_ciudad, 
						CONCAT( UPPER(a.abr_ciudad), ' (', LEFT(b.nom_depart, 4), ') - ', LEFT(c.nom_paisxx, 3) ) AS abr_ciudad
				   FROM ".BASE_DATOS.".tab_genera_ciudad a 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart b 
					 ON a.cod_paisxx = b.cod_paisxx 
					AND a.cod_depart = b.cod_depart 
			 INNER JOIN ".BASE_DATOS.".tab_genera_paises c 
					 ON a.cod_paisxx = c.cod_paisxx 
				  WHERE a.ind_estado = 1 ";

		$mSql .= $_REQUEST[term] ? " AND a.abr_ciudad LIKE '%".$_REQUEST[term]."%' " : "";
		$mSql .= $mCodCiudad != NULL ? " AND a.cod_ciudad = '{$mCodCiudad}' " : "";

		$mSql .= " ORDER BY a.abr_ciudad ";

		$mConsult = new Consulta($mSql, rutas::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');

		if( $_REQUEST[term] )
		{
			$novedades = array();
			for($i=0; $i<sizeof( $mResult ); $i++){
				$mTxt = $mResult[$i][cod_ciudad]." - ".utf8_encode($mResult[$i][abr_ciudad]);
				$novedades[] = array('value' => utf8_encode($mResult[$i][abr_ciudad]), 'label' => $mTxt, 'id' => $mResult[$i][cod_ciudad] );
			}
			echo json_encode( $novedades );
		}
		else
			return $mResult;
	}

	/*! \fn: getPC
	 *  \brief: Trae los puestos de control
	 *  \author: Ing. Fabian Salinas
	 *	\date: 05/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: Matriz
	 */
	public function getPC()
	{
		$mSql = "  	(
						SELECT cod_contro, 
							   if( ind_urbano = '".COD_ESTADO_ACTIVO."', CONCAT(nom_contro,' (Urbano)'), nom_contro ) AS nom_contro 
						  FROM ".BASE_DATOS.".tab_genera_contro
						 WHERE cod_contro != ".CONS_CODIGO_PCLLEG." 
						   AND ind_estado = '1' 
						   AND ind_virtua = '0'
						   AND ind_pcpadr = '1' 
						   ". ($_REQUEST[term] ? " AND nom_contro LIKE '%".$_REQUEST[term]."%' " : "") ."
					  ORDER BY 2 
					)
					UNION 
					(
						SELECT cod_contro, 
							   if( ind_urbano = '".COD_ESTADO_ACTIVO."', CONCAT(nom_contro,' (Virtual) - (Urbano)'), CONCAT(nom_contro,' (Virtual)') ) AS nom_contro 
						  FROM ".BASE_DATOS.".tab_genera_contro
						 WHERE cod_contro != ".CONS_CODIGO_PCLLEG." 
						   AND ind_estado = '1' 
						   AND ind_virtua = '1'
						   AND ind_pcpadr = '1'
						   ". ($_REQUEST[term] ? " AND nom_contro LIKE '%".$_REQUEST[term]."%' " : "") ."
					  ORDER BY 2 
					) ";
		$mConsult = new Consulta($mSql, rutas::$cConexion);
		$mResult = $mConsult -> ret_matrix('a');

		if( $_REQUEST[term] )
		{
			$mPuestos = array();
			for($i=0; $i<sizeof( $mResult ); $i++){
				$mTxt = $mResult[$i][cod_contro]." - ".utf8_decode($mResult[$i][nom_contro]);
				$mPuestos[] = array('value' => utf8_decode($mResult[$i][nom_contro]), 'label' => $mTxt, 'id' => $mResult[$i][cod_contro] );
			}
			echo json_encode( $mPuestos );
		}
		else
			return $mResult;
	}

	/*! \fn: getRutas
	 *  \brief: Consulta Rutas
	 *  \author: Ing. Fabian Salinas
	 *	\date: 05/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: Matriz
	 */
	public function getRutas()
	{
		$mSql = "SELECT a.cod_rutasx, a.nom_rutasx 
				   FROM ".BASE_DATOS.".tab_genera_rutasx a 
				". ($_REQUEST[term] ? " WHERE a.nom_rutasx LIKE '%".$_REQUEST[term]."%' " : "") ." 
			   ORDER BY a.nom_rutasx DESC ";
		$mConsult = new Consulta($mSql, rutas::$cConexion);
		$mResult = $mConsult -> ret_matrix('a');

		if( $_REQUEST[term] )
		{
			$mRutas = array();
			for($i=0; $i<sizeof( $mResult ); $i++){
				$mTxt = $mResult[$i][cod_rutasx]." - ".utf8_decode($mResult[$i][nom_rutasx]);
				$mRutas[] = array('value' => utf8_decode($mResult[$i][nom_rutasx]), 'label' => $mTxt, 'id' => $mResult[$i][cod_rutasx] );
			}
			echo json_encode( $mRutas );
		}
		else
			return $mResult;
	}

	/*! \fn: getUltiPC
	 *  \brief: Trae el PC de llegada según constantes
	 *  \author: Ing. Fabian Salinas
	 *	\date: 05/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: Array
	 */
	public function getUltiPC()
	{
		$mSql = " SELECT a.cod_contro, TRIM(if(ind_virtua = '1',CONCAT(nom_contro,' (Virtual)'),nom_contro)) AS nom_contro
					FROM ".BASE_DATOS.".tab_genera_contro a
				   WHERE a.cod_contro = ".CONS_CODIGO_PCLLEG;
		$mConsult = new Consulta($mSql, rutas::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');
		return $mResult[0];
	}

	/*! \fn: getNextCodRuta
	 *  \brief: Trae el siguiente Codigo de Ruta (Para crear una ruta)
	 *  \author: Ing. Fabian Salinas
	 *	\date: 05/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: Integer
	 */
	public function getNextCodRuta()
	{
		$mSql = " SELECT Max(cod_rutasx) AS maximo
					FROM ".BASE_DATOS.".tab_genera_rutasx";
		$mConsult = new Consulta($mSql, rutas::$cConexion );
		$mResult = $mConsult -> ret_arreglo();

		return $mResult = $mResult[0]+1;
	}

	/*! \fn: getPaisDepart
	 *  \brief: Trae los codigos de pais y Departamento por ciudad
	 *  \author: Ing. Fabian Salinas
	 *	\date: 05/08/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return: Matriz
	 */
	public function getPaisDepart( $mCodCiudad )
	{
		$mSql = " SELECT a.cod_paisxx, a.cod_depart
					FROM ".BASE_DATOS.".tab_genera_ciudad a
				   WHERE a.cod_ciudad = '{$mCodCiudad}' ";
		$mConsult = new Consulta($mSql, rutas::$cConexion );
		$mResult = $mConsult -> ret_matrix('a');
		return $mResult[0];
	}

	/*! \fn: getTransp
	 *  \brief: Trae las transportadoras
	 *  \author: Ing. Fabian Salinas
	 *	\date: 10/08/2015
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
			if ( $filtro -> listar( rutas::$cConexion ) ) : 
				$datos_filtro = $filtro -> retornar();
				$mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
			endif;
		}else{#PARA EL FILTRO DE EMPRESA
			$filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_perfil] );
			if ( $filtro -> listar( rutas::$cConexion ) ) : 
				$datos_filtro = $filtro -> retornar();
				$mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
			endif;
		}

		$mSql .= $_REQUEST[term] ? " AND a.abr_tercer LIKE '%".$_REQUEST[term]."%' " : "";
		$mSql .= " ORDER BY a.abr_tercer ASC ";
		$consulta = new Consulta( $mSql, rutas::$cConexion );
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
}

if($_REQUEST[Ajax] === 'on' )
	$_INFORM = new rutas();

?>