
<?php
/*! \class: cronAvansatTmsReportesUbicacion
*  \brief: Clase para envio de reportes pendientes a ANNUIT
*  \author: Ing. Nelson Liberato
*  \date: 2021-12-02
*  \return: null
*/
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

/*! Incluye script de constantes de conexion a la BD */
//echo getcwd();

@include_once(dirname(__DIR__)."/constantes.inc");   
@include_once(dirname(dirname(__DIR__))."/".DIR_APLICA_CENTRAL . "/lib/general/constantes.inc");   

// CONSULTA LAS NOVEDADES DE INTEGRADOR GPS REGISTRADAS EN EL GL INTEGRADOR PARA REENVIARLAS AL TMS (Empresrial, Pyme) DEL CLIENTE
 

class cronAvansatAlertasCEVA
{
	private static $cConn = NULL;
	private static $cPendientes = NULL;
	private static $cReportes = [];
	private static $cURL = 'https://apiq.solistica.com/gps/SupplierTransactions/ReceiveData/v1';
	private static $cUser = 'HUBGPS_OET';
	private static $cPass = 'DR4A^nRTs3sm';
	private static $cAuth = [];
	private static $cHTTP = [
								500 => 'Internal server error',
								401 => 'UnAuthorized',
							];


	/*! \fn: __construct
	*  \brief: constructor de la clase
	*  \author: Ing. Nelson Liberato
	*  \date: 2021-12-02
	*/
	function __construct()
	{
		self::setConection();
		self::getReportesPendientes();
		self::setCloseConection();

		 

	}

	/*! \fn: getReportesPendientes
	*  \brief: funcion encargada de consultar que reportes faltan enviar para la empresa: OPERACIONES NACIONALES DE MERCADEO LTDA  860350940
	*  \author: Ing. Nelson Liberato
	*  \date: 2021-12-02
	*  \return: array de reportes pendientes
	*/
	private function getReportesPendientes()
	{
		try 
		{ 
            $transportadoras_activas = [777777779,800021603,800049934,800064459,800089325,
                                        800090262,800109949,800120636,800129133,800133109,
                                        800138896,800157847,800166412,800170695,800177598,
                                        800199898,805023122,806014425,809000555,811001497,
                                        822006143,830038608,830040809,830075219,830076669,
                                        830099596,830109831,830134066,830141359,860053255,
                                        900029975,900033633,900056024,900068145,900074012,
                                        900089269,900116432,900134456,900159872,900179614,
                                        900213844,900218311,900220423,900265359,900301751,
                                        900306833,900307810,900424599,900431160,900472027,
                                        900499919,900507255,900587051,900587608,900635112,
                                        900646142,900714469,900720727,900732377,900738114,
                                        900745904,900746988,900748000,900775200,900805898,
                                        900836827,900936006,901003462,901039464,901121844,
                                        901140841,901167319,901196694,901211744,901237466,
                                        901289506,901297510,901340624,901353282,901389202,
                                        901408698,901413217,901541251,901575235];

            foreach ($transportadoras_activas as $cod_transp) {
                $query = "INSERT INTO `satt_intgps`.`tab_interf_parame` 
                            (`cod_operad`, `cod_transp`, `nom_operad`,
                            `nom_usuari`, `clv_usuari`, `int_config`,
                            `cod_intern`, `val_timtra`, `ind_intind`,
                            `ind_operad`, `ind_estado`, `url_webser`,
                            `cod_tokenx`, `ind_deseta`, `tie_report`
                            ) VALUES (
                                '53', '$cod_transp', '',
                                'InterfMasivFaroGl', 'InterfMasivoAvansatGLIntegradorGPS', '',
                            NULL, '0', '1',
                            '0', '1', NULL,
                            NULL, '0', '0'
                            )";
                echo "<pre>";
                echo $query;
                echo "</pre>";
                echo "<br><br>";
                self::setExecuteQuery($query, NULL,true);
            };  

		} 
		catch (Exception $e) 
		{
			echo date("Y.m.d H:i:s");
		}
	}


	/*******************************************************************************************************************************************/
	/*******************************   F U N C I O N   P A R A   C O N E X I O N   A   B D   C O N   P D O     *********************************/
	/*******************************************************************************************************************************************/

	private function setConection()
	{
		try { 

			self::$cConn  = new PDO('mysql:host=aglbd.intrared.net;dbname='.BASE_DATOS.';port=3306;charset=utf8', USUARIO, CLAVE, [PDO::ATTR_PERSISTENT => true] );

            self::$cConn ->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            self::$cConn ->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );


		} catch ( PDOException $e ) {
			self::showErrorCatch( $e );
		}
	}

	private function setCloseConection()
	{
		try {
			 self::$cConn = null;
		} catch ( PDOException $e ) {
			 
		}
	}

	private function setExecuteQuery( $mQuery = NULL, $mData = array(), $mReturn = false )
	{
		try 
		{
			$ReturnData = array();
			$data = self::$cConn -> query($mQuery, PDO::FETCH_ASSOC);


			 
			if(!$data){
				// self::getErrorPDO();
				throw new PDOException('Algo pasó', 1);			 	
			}
			if($mReturn === true)
			{
				foreach($data AS $mRow)
				{
				    $ReturnData[] = $mRow;
				}
			}
			return $ReturnData;
		} 
		catch (PDOException $e) 
		{ 

			//echo "<pre>"; print_r( $e ); echo "</pre>";
			self::showErrorCatch($e);
		}
	}

	private function showErrorCatch($data = NULL )
	{
		$html  = '<table width="100%">';
		 	$html .= '<tbody>';
			$html .= '<tr>';
				$html .= '<td><b>Error presentado</b></td>';
			$html .= '</tr>';
		 	
	
			//$html .= '<tr>';
			//	$html .= '<td>'.var_export($data, true).'</td>';
			//$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td>Mensaje:  <span style="color:red;">'.$data -> getMessage().'</span></td>';
			$html .= '</tr>';				$html .= '<tr>';
				$html .= '<td>Línea: '.$data -> getLine().'</td>';
			$html .= '</tr>';
			$html .= '</tr>';				
			//$html .= '<tr>';
			//	$html .= '<td>Argumentos: <pre>'.print_r($data -> getTrace(), true).'</pre></td>';
			//$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';

		echo $html;
		//die("Muere Ejecución");
	}

}




$_CRON = new cronAvansatAlertasCEVA();

?>