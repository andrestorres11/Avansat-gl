<?php
/*! \class: cronAvansatTmsReportesUbicacion
*  \brief: Clase para envio de reportes pendientes a ANNUIT
*  \author: Ing. Nelson Liberato
*  \date: 2021-12-02
*  \return: null
*/
//ini_set('display_errors', true);
//error_reporting(E_ALL & ~E_NOTICE);

/*! Incluye script de constantes de conexion a la BD */
//echo getcwd();

@include_once(dirname(__DIR__)."/constantes.inc");   


// CONSULTA LAS NOVEDADES DE INTEGRADOR GPS REGISTRADAS EN EL GL INTEGRADOR PARA REENVIARLAS AL TMS (Empresrial, Pyme) DEL CLIENTE
 

class cronAvansatTmsReportesUbicacion
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

		if (sizeof(self::$cPendientes) > 0) {
			self::envioReportes();
			if (sizeof(self::$cReportes) > 0) {
				self::actualizarEnviados(); // actualiza como enviado los que si pasaron al TMS despues de acabar el foreach de los pendientes
			}
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
			$mQuery = "
								SELECT bb.num_despac, bb.num_placax, bb.cod_itiner, 
									   bb.cod_integr, DATE_FORMAT( bb.fec_itiner, '%Y-%d-%m %H:%i') as fec_itiner, bb.msg_itiner, 
									   bb.map_tokenx, bb.ind_envtms, dd.cod_server,
									   ee.nom_usuari, ee.clv_usuari, ee.nom_operad,
									   dd.url_webser,DATE_FORMAT( bb.fec_creaci, '%Y-%m-%d') as fec_creaci, aa.cod_manifi,
									   cc.cod_transp
								  FROM
								        ".BASE_DATOS.".tab_despac_despac aa
							 INNER JOIN ".BASE_DATOS.".tab_despac_vehige bb ON aa.num_despac = bb.num_despac
							 INNER JOIN ".BASE_DATOS.".tab_transp_tipser cc ON bb.cod_transp = cc.cod_transp AND cc.num_consec = (
							 																										SELECT MAX(xx.num_consec) AS num_consec
							 																										  FROM ".BASE_DATOS.".tab_transp_tipser xx
							 																										 WHERE xx.cod_transp = bb.cod_transp
							 																									 )
							 INNER JOIN ".BD_STANDA.".tab_genera_server  dd  ON cc.cod_server = dd.cod_server
							 INNER JOIN ".BASE_DATOS.".tab_interf_parame ee  ON cc.cod_transp = ee.cod_transp 
							 												AND ee.cod_operad = 53 /*Debe tener interfaz con TMS */
							 												AND ee.ind_estado = 1 /*Debe estar activa*/


							     WHERE
							     	bb.ind_envtms = 0
									AND bb.cod_itiner IS NOT NULL
							     	
							     	AND dd.cod_server IN (14,16,17,18,19,20,21)
							     	-- AND aa.num_despac IN (271)
							
							ORDER BY dd.cod_server

					  ";
			//echo "<pre>"; print_r( $mQuery );  echo "</pre>";	 
			self::$cPendientes = self::setExecuteQuery($mQuery, NULL,true);
			echo "<pre>"; print_r( self::$cPendientes );  echo "</pre>"; 
			die();
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
			echo "<pre>CANTIDAD DE REPORTES A ENVIAR: "; print_r(sizeof( self::$cPendientes ) ); echo "</pre>";
			$mMailData = [];
			foreach (self::$cPendientes AS $mIndex => $mReporte) 
			{ 

				// UPDATE `sate_solopl`.`tab_genera_noveda` SET `nom_noveda` = 'INT GPS - REPORTE DE UBICACIÓN' WHERE `tab_genera_noveda`.`cod_noveda` = 9183;
				$mParams =  [ 
							  	'nom_usuari'  => $mReporte['nom_usuari'],
								'pwd_clavex'  => $mReporte['clv_usuari'],
								'nom_aplica'  => $mReporte['nom_operad'],
								'cod_transp'  => $mReporte['cod_transp'],
								'cod_manifi'  => $mReporte['cod_manifi'],
								'num_placax'  => $mReporte['num_placax'],
								'cod_itiner'  => $mReporte['cod_itiner'],
								'msg_itiner'  => $mReporte['msg_itiner'],
								'fec_itiner'  => $mReporte['fec_itiner'],
								'map_tokenx'  => $mReporte['map_tokenx'],
							];
							//style='display:none';
				echo "<pre > <hr>".$mReporte['num_despac']." - ".$mReporte['fec_creaci']; print_r( $mParams); echo "</pre>";  //die();
				ini_set("soap.wsdl_cache_enabled", 0);
				//die('acaba proceso');
				$mSoap = new SoapClient( $mReporte['url_webser'], ['trace' => 1,'exception' => 1 ] );
				
				$mResponse = $mSoap -> __soapCall("setStatusItinerary", $mParams); // Se coloca la novedad en SITIO
				
				if( "1000" != $mResponse['cod_respon'] ){ 
					//throw new Exception( $mResponse['msg_respon'], $mCodResp[1] );

					$mMailData[$mIndex][$mReporte['nom_operad']] = ['params' => $mParams];
					$mMailData[$mIndex][$mReporte['nom_operad']] = ['resonse' => $mResponse];

					echo "<pre>Aplicacion: ".$mReporte['nom_operad']."  Despacho: ".$mReporte['num_despac']."- Url Ws: ".$mReporte['url_webser']." -> "; print_r( var_dump("Error: ".$mResponse['msg_respon']) ); echo "</pre>"; 
 

					// se valida el error para no volver a enviar el despacho, porque seguira generando error
					if(in_array($mResponse['msg_respon'], ["No se encuentra numero de despacho.", "Puesto de control no existente."])  ){
						self::$cReportes[$mIndex]['num_despac'] = $mReporte['num_despac'];
						self::$cReportes[$mIndex]['fec_creaci'] = $mReporte['fec_creaci'];
						self::$cReportes[$mIndex]['num_placax'] = $mReporte['num_placax'];
					}
				}
				else{
					self::$cReportes[$mIndex]['num_despac'] = $mReporte['num_despac'];
					self::$cReportes[$mIndex]['fec_creaci'] = $mReporte['fec_creaci'];
					self::$cReportes[$mIndex]['num_placax'] = $mReporte['num_placax'];
					
					echo "<pre>OK :  ".$mReporte['num_despac']." -> ".$mReporte['cod_manifi']."- ".$mReporte['url_webser']." -> "; print_r($mResponse['msg_respon'] ); echo "</pre>";  
				}

			}
	
		}
		catch (Exception $e) 
		{
			echo "<pre>Exception: ".$mReporte['cod_manifi']."- ".$mReporte['url_webser']." -> "; print_r( $e -> getMessage()); echo "</pre>";  
		}
	}
  

	/*! \fn: actualizarEnviados
	*  \brief: funcion que coloca la bandera de reporte enviado, si curl no genera error
	*  \author: Ing. Nelson Liberato
	*  \date: 2021-12-02
	*  \return: none
	*/
	private function actualizarEnviados( $NumReport = NULL )
	{
		try 
		{
			foreach (self::$cReportes as $mIndex => $mReporte) {
				$mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_vehige SET ind_envtms = 1 WHERE num_despac = ". $mReporte['num_despac']." AND  num_placax = '".$mReporte['num_placax']."' ";
				echo "<pre><hr>UPDATE "; print_r($mUpdate); echo "</pre>";  
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

	private function xml2array($contents, $get_attributes=1, $priority = 'tag') {
	    if(!$contents) return array();

	    if(!function_exists('xml_parser_create')) {
	        //print "'xml_parser_create()' function not found!";
	        return array();
	    }

	    //Get the XML parser of PHP - PHP must have this module for the parser to work
	    $parser = xml_parser_create('');
	    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	    xml_parse_into_struct($parser, trim($contents), $xml_values);
	    xml_parser_free($parser);

	    if(!$xml_values) return;//Hmm...

	    //Initializations
	    $xml_array = array();
	    $parents = array();
	    $opened_tags = array();
	    $arr = array();

	    $current = &$xml_array; //Refference

	    //Go through the tags.
	    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
	    foreach($xml_values as $data) {
	        unset($attributes,$value);//Remove existing values, or there will be trouble

	        //This command will extract these variables into the foreach scope
	        // tag(string), type(string), level(int), attributes(array).
	        extract($data);//We could use the array by itself, but this cooler.

	        $result = array();
	        $attributes_data = array();
	        
	        if(isset($value)) {
	            if($priority == 'tag') $result = $value;
	            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
	        }

	        //Set the attributes too.
	        if(isset($attributes) and $get_attributes) {
	            foreach($attributes as $attr => $val) {
	                if($priority == 'tag') $attributes_data[$attr] = $val;
	                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
	            }
	        }

	        //See tag status and do the needed.
	        if($type == "open") {//The starting of the tag '<tag>'
	            $parent[$level-1] = &$current;
	            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
	                $current[$tag] = $result;
	                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
	                $repeated_tag_index[$tag.'_'.$level] = 1;

	                $current = &$current[$tag];

	            } else { //There was another element with the same tag name

	                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
	                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
	                    $repeated_tag_index[$tag.'_'.$level]++;
	                } else {//This section will make the value an array if multiple tags with the same name appear together
	                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
	                    $repeated_tag_index[$tag.'_'.$level] = 2;
	                    
	                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
	                        unset($current[$tag.'_attr']);
	                    }

	                }
	                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
	                $current = &$current[$tag][$last_item_index];
	            }

	        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
	            //See if the key is already taken.
	            if(!isset($current[$tag])) { //New Key
	                $current[$tag] = $result;
	                $repeated_tag_index[$tag.'_'.$level] = 1;
	                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

	            } else { //If taken, put all things inside a list(array)
	                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

	                    // ...push the new element into that array.
	                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
	                    
	                    if($priority == 'tag' and $get_attributes and $attributes_data) {
	                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	                    }
	                    $repeated_tag_index[$tag.'_'.$level]++;

	                } else { //If it is not an array...
	                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
	                    $repeated_tag_index[$tag.'_'.$level] = 1;
	                    if($priority == 'tag' and $get_attributes) {
	                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	                            
	                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
	                            unset($current[$tag.'_attr']);
	                        }
	                        
	                        if($attributes_data) {
	                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	                        }
	                    }
	                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
	                }
	            }

	        } elseif($type == 'close') { //End of tag '</tag>'
	            $current = &$parent[$level-1];
	        }
	    }
	    
	    return($xml_array);
	}

}




$_CRON = new cronAvansatTmsReportesUbicacion();

?>