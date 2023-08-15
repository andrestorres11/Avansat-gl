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
 	  	echo date("Y.m.d H:i:s");

 	  	//mail(
		//	 'nelson.liberato@grupooet.com, maribel.garcia@grupooet.com', 
		//	 'CRON ENVIO NOVEDAD GL INTEGRADOR A TMS EJECUTADO',
		//   	 'CRON DE REPLICA DE NOVEDAD DE GL INTEGRADOR A TMS CLIENTE SE EStS SATURANDO '.sizeof(self::$cPendientes)
		//   	);


		if (sizeof(self::$cPendientes) > 0) {
			// if( sizeof(self::$cPendientes) > 300){
			//	mail(
			//		 'nelson.liberato@grupooet.com, maribel.garcia@grupooet.com', 
			//		 'CRON GL INTEGRADIOR - TMS SATURADO',
			//	   	 'CRON DE REPLICA DE NOVEDAD DE GL INTEGRADOR A TMS CLIENTE SE EStS SATURANDO '.sizeof(self::$cPendientes)
			//	   	);
			//}
			self::envioReportes();
			/*
			if (sizeof(self::$cReportes) > 0) {
				self::actualizarEnviados(); // actualiza como enviado los que si pasaron al TMS despues de acabar el foreach de los pendientes
			}*/
		}
		else{
		 	echo '<b>NO HAY NADA PARA ENVIAR</b>';
		}
		echo date("Y.m.d H:i:s");
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
			$fecha_actual = DATE('Y-m-d');
			$mQuery = "SELECT a.cod_bitaco, a.cod_manifi, a.cod_transp, a.obs_noveda, a.fec_creaci FROM ".BASE_DATOS.".tab_bitaco_gendes a WHERE a.ind_enviad = 0";
			//echo "<pre>"; print_r( $mQuery );  echo "</pre>";	 
			self::$cPendientes = self::setExecuteQuery($mQuery, NULL,true);
			//echo "<pre>"; print_r( self::$cPendientes );  echo "</pre>"; 
			return self::$cPendientes;
		} 
		catch (Exception $e) 
		{
			echo date("Y.m.d H:i:s");
		}
	}

 

	/*! \fn: envioReportes
	*  \brief: funcion que hace el proceso de enviar los reportes de posicion
	*  \author: Ing. Nelson Liberato
	*  \date: 2021-12-02
	*  \return: bool
	*/
	private function envioReportes()
	{
		// code...
		try 
		{
			$registrosUnicos = self::obtenerRegistrosUnicos(self::$cPendientes);
			$registrosAgrupados = self::agruparRegistros($registrosUnicos);
			echo "<pre>CANTIDAD DE REPORTES A ENVIAR: "; print_r(sizeof( $registrosUnicos ) ); echo "</pre>";

			foreach ($registrosAgrupados AS $mIndex => $mReporte) 
			{ 
				self::sendEmail($mReporte, $mIndex);
			}
			self::actualizarEnviados();
		}
		catch (Exception $e) 
		{
			echo "<pre>Exception: ".$mReporte['cod_manifi']."- ".$mReporte['url_webser']." -> "; print_r( $e -> getMessage()); echo "</pre>";  
		}
	}


	private function sendEmail($mReporte, $cod_manifi){
			$sEmails = self::getEmailsNotifi($mReporte[0]['cod_transp']);
			$html_body = '<p>Estimado Cliente, </p>';
			$html_body .= '<p><strong>Centro Logístico Faro</strong> informa que su solicitud de despacho con el manifiesto número <strong>'.$cod_manifi.'</strong> se encuentra con las siguientes novedades:</p>';
			$html_body .= '<br>
							<table>
								<thead>
									<tr>
										<th>Observación</th>
										<th>Fecha</th>
									</tr>
								</thead>
								<tbody>';
			foreach($mReporte as $registro){
				$html_body .= '<tr>';
				$html_body .= '<td>'.$registro['obs_noveda'].'</td>';
				$html_body .= '<td>'.$registro['fec_creaci'].'</td>';
				$html_body .= '</tr>';
			}
			$html_body .= '		</tbody>
							</table>';

			$base_mensaje = $html_body;
			$mAsunto = utf8_encode("Novedad al crear el despacho - Manifiesto: ".$cod_manifi);
			$year=date('Y');
			$titulo = $mAsunto;
            $plantilla = "pla_notifi_gendes.html";
            $mResult = array();
            $mCabece  = 'MIME-Version: 1.0' . "\r\n";
            $mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $mCabece .= 'From: Novedades Centro Logistico FARO <supervisores@faro.com.co>' . "\r\n";
            $tmpl_file = '/var/www/html/ap/satt_standa/planti/'.$plantilla;
            $logo = "https://".$_SERVER['SERVER_NAME']."/ap/satt_faro/logos/LogoCLFaro.png";
            if(!file_exists($tmpl_file)) {
                throw new Exception("No existe la plantilla: ".$plantilla, 9999);              
            }
            $thefile = implode("", file( $tmpl_file ) );
            $thefile = addslashes($thefile);
            $thefile = "\$r_file=\"".$thefile."\";";
            eval( $thefile );
            $mHtmlxx = $r_file;
            mail($sEmails, $mAsunto, $mHtmlxx, $mCabece);
	}

	/*! \fn: obtenerRegistrosUnicos
	*  \brief: funcion que filtra por registros similares
	*  \author: Ing. Cristian Andres Torres
	*  \date: 2023-06-27
	*  \return: none
	*/
	private function obtenerRegistrosUnicos( $arreglo )
	{
		$registrosUnicos = array();
		$codigos = array();

		foreach ($arreglo as $registro) {
			$cod_manifi = $registro['cod_manifi'];
			$obs_noveda = $registro['obs_noveda'];

			$clave = $cod_manifi . '-' . $obs_noveda;

			if (!in_array($clave, $codigos)) {
				$codigos[] = $clave;
				$registrosUnicos[] = $registro;
			}
		}

		return $registrosUnicos;
	}


	private function agruparRegistros($registros) {
		$agrupados = array();
	
		foreach ($registros as $registro) {
			$cod_manifi = $registro['cod_manifi'];
			$obs_noveda = $registro['obs_noveda'];
			$cod_transp = $registro['cod_transp'];
			$fec_creaci = $registro['fec_creaci'];
	
			if (!isset($agrupados[$cod_manifi])) {
				$agrupados[$cod_manifi] = array();
			}
	
			$agrupados[$cod_manifi][] = array(
				'cod_transp' => $cod_transp,
				'obs_noveda' => $obs_noveda,
				'fec_creaci' => $fec_creaci
			);
		}
	
		return $agrupados;
	}


	/*! \fn: getEmailsNotifi
	*  \brief: Obtiene los emails para notificaciones
	*  \author: Ing. Cristian Andrés Torres
	*  \date: 02/12/2022   
	*  \return emails : String
	*/
	private function getEmailsNotifi($cod_transp){
		$emailsSend = array();
		//Consulta los correos para notificar a la empresa transportadora
		$mQueryEmpTra = "SELECT a.dir_emailx
					FROM " . BASE_DATOS. ".tab_genera_concor a
					WHERE a.num_remdes = '".$cod_transp."'
						AND a.ind_novapp = 1";
		$mEmaEmpTra = self::setExecuteQuery($mQueryEmpTra, NULL,true);
		foreach($mEmaEmpTra as $emails){
			$mEmails = explode(",", $emails['dir_emailx']);
			foreach($mEmails as $key => $value){
				array_push($emailsSend, $value);
			}
		}
		$emails = implode(', ', $emailsSend);
		return $emails;
	}
  

	/*! \fn: actualizarEnviados
	*  \brief: funcion que coloca la bandera de reporte enviado, si curl no genera error
	*  \author: Ing. Nelson Liberato
	*  \date: 2021-12-02
	*  \return: none
	*/
	private function actualizarEnviados()
	{
		try 
		{
			foreach (self::$cPendientes as $mIndex => $mReporte) {
				$sEmails = self::getEmailsNotifi($mReporte['cod_transp']);
				$mUpdate = "UPDATE ".BASE_DATOS.".tab_bitaco_gendes  SET ind_enviad = 1, fec_enviad = NOW(), cor_enviad = '".$sEmails."' WHERE cod_bitaco = ". $mReporte['cod_bitaco']." ";
				self::setExecuteQuery($mUpdate, NULL,false); 
			}

		} 
		catch (Exception $e) 
		{
			
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
		die("Muere Ejecución");
	}

}




$_CRON = new cronAvansatAlertasCEVA();

?>