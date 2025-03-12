<?php

/**
 *	Archivo: sanitizeData.php
 * 
 *	Este archivo desinfecta los datos que llegan de la petici�n al servidor,
 *	para evitar cualquier tipo de inyecci�n de c�digo
 *
 *	@author   Ing. Andr�s Mantilla
 *	@version  1.0
 *	@since    2024-11-18
 */

#L�neas para ver errores y l�neas del Log
/*************************************************/
// ini_set('display_errors', 1);
// error_reporting(E_ERROR);
/*************************************************/

#Si se accede al archivo directamente, se muestra un mensaje en pantalla
if (__FILE__ === realpath($_SERVER['SCRIPT_FILENAME']))
{
	die("<h1>ACCESO NO AUTORIZADO!</h1>");
}

/**
 *	@method   sanitizeRequest
 *	@abstract Desinfecta la solicitud de las peticiones realizadas al servidor, por el medio que sea
 *			  Esta es la funci�n que se ejecuta, la instancia est� al final del archivo para evitar inconvenientes ��
 *
 *	@param    Ninguno
 *	@return   Nada
 *
 *	@author   Ing. Andr�s Mantilla
 *	@since    2024-11-18
 **/
function sanitizeRequest()
{
	try 
	{

		if (in_array($_REQUEST['cod_servic'], ['20240528', '600000'])) {
			return true;
		}

		#Datos GET
		/**********************************/
		$mData = [
			'method' => 'GET',
			'data' => &$_GET
		];
		sanitizeData($mData);
		/**********************************/

		#Datos POST
		/**********************************/
		$mData = [
			'method' => 'POST',
			'data' => &$_POST
		];
		sanitizeData($mData);
		/**********************************/
	} 
	catch(Exception $e) 
	{
		$msgError = "Se ha presentado un error en la funci�n \"".__FUNCTION__."\", generando lo siguiente:" . PHP_EOL . PHP_EOL;

		$msgError .= $e->getMessage();

		writeLogError($msgError);
	}
}

/**
 *	@method   sanitizeData
 *	@abstract Desinfecta los datos recibidos en la petici�n
 *	@param    Se manejan un par�metro:
 *				- $mData: Para los datos recibidos por GET, POST, REQUEST
 *	@return   Nada, es una variable por referencia
 *
 *	@author   Ing. Andr�s Mantilla
 *	@since    2024-11-18
 */
function sanitizeData($mData)
{

	try
	{
		#Se obtienen las reglas a aplicar para los datos
		$mRules = getRules();

		#Se recorre cada elemento (si existe)
		foreach ($mData['data'] as $mKey => $mValue)
		{
			#Se obtiene la totalidad de Reglas a Aplicar
			$mAllRules = [];

			#Se valida si hay reglas aplicadas para el elemento que se est� recorriendo
			if (isset($mRules[$mKey])) 
			{
				$mAllRules = $mRules[$mKey];
			}

			#Se obtienen todas las reglas aplicadas hasta el momento (si las hay)
			$mRulesNames = array_column($mAllRules, 'name');

			#Se recorren las reglas que aplican para todos los casos
			foreach ($mRules['all'] as $subIndex => $mRule)
			{

				#Se valida si ya existe una configuraci�n particular de la regla, y se omite la "global"
				if (array_search($mRule['name'], $mRulesNames) !== false)
				{
					continue;
				}
				#Si no existe la regla, se incluye en las Reglas a Aplicar
				else
				{
					$mAllRules[$subIndex] = $mRule;
				}
			}

			#Se recorre cada dato para aplicar las reglas correspondientes
			foreach ($mAllRules as $key => $mRule) 
			{
				#Se valida si la Regla est� activa (ya que puede no estarlo)
				if ($mRule['active'] == 'yes')
				{

					#Se verifica si se puede llamar la funci�n
					if (is_callable($mRule['function']))
					{
						#Se obtiene el nombre de la funci�n
						$mFunction = $mRule['function'];

						#Se verifica si es o no una funci�n especial (con par�metros)
						if (!$mRule['special'])
						{
							#Se ejecuta la funci�n
							$mValue = $mFunction($mValue);
						}

						#Funciones especiales (con par�metros)
						else
						{
							#Se obtiene la posici�n de donde debe ir el dato para ejecutar la funci�n
							$mFnIndex = array_search('data_value', $mRule['params']);

							#Se define el valor que tiene ese dato, en la posici�n correcta para ejecutar la funci�n
							$mRule['params'][$mFnIndex] = $mValue;

							#Se definen los par�metros para llamar la funci�n, ya con el dato real
							$mParams = $mRule['params'];

							#Se ejecuta la funci�n con sus par�metros correspondientes
							$mValue = call_user_func_array($mFunction, $mParams);
						}
					}
				}
			}

			#Se valida qu� m�todo se uso para la petici�n
			switch ($mData['method'])
			{
				#M�todo GEt
				case 'GET':
					$_GET[$mKey] = $mValue;
				break;

				#M�todo POST
				case 'POST':
					$_POST[$mKey] = $mValue;
				break;
			}

			#GLOBALS, por si se maneja de esta forma
			$GLOBALS[$mKey] = $mValue;
			#REQUEST
			$_REQUEST[$mKey] = $mValue;
		}
	}
	catch(Exception $e) 
	{
		$msgError = "Se ha presentado un error en la funci�n \"".__FUNCTION__."\", generando lo siguiente:" . PHP_EOL . PHP_EOL;

		$msgError .= $e->getMessage();

		writeLogError($msgError);
	}
}

/**
 *	@method   getRules
 *	@abstract Se obtienen las reglas que aplicar�n para los campos recibidos
 *			  Se tiene un "all" que indica que aplica para todos los campos.
 *			  De igual forma, se pueden parametrizar campos espec�ficos, 
 *			  sobreponiendo tambi�n la regla general en caso de requerirlo
 *	@param    Ninguno
 *	@return   Matriz con las reglas a aplicar
 *
 *	@author   Ing. Andr�s Mantilla
 *	@since    2024-11-20
 */
function getRules() 
{
	try
	{
		$mRules = [

			#Reglas que aplican para todos los campos
			'all' => [
				['name' => 'removeSpaces', 'function' => 'trim', 'active' => true],
				['name' => 'removeTags', 'function' => 'strip_tags', 'active' => true],
				['name' => 'addSlashes', 'function' => 'addslashes', 'active' => true],
				['name' => 'encodeHtmlChars', 'function' => 'htmlspecialchars', 'active' => true],
				['name' => 'replaceSpecialChars', 'function' => 'str_replace', 'active' => true, 'special' => true, 
					'params' => [ ['#', '/*', '--', 'javascript'], '' , 'data_value' ]
				],
				['name' => 'dataLength', 'function' => 'substr', 'active' => false, 'special' => true, 
					'params' => ['data_value', '0', '10'] #Esta tomar�an solo 10 caracteres
				]
			]
		];

		#Ejemplo de c�mo se implementar�an para campos espec�ficos
		#############################################################################################################################################################
		/*
		$mRules['cod_tercer'] = [
			['name' => 'dataLength', 'function' => 'substr', 'active' => true, 'special' => true, 
				'params' => ['data_value', '0', '11'] #Se tiene un m�ximo de 11 caracteres
			]
		];
		*/
		#############################################################################################################################################################

		#Reglas para la Clave del GPS
		$mRules['clv_gpsxxx'] = [
			['name' => 'replaceSpecialChars', 'active' => false] #No quiero aplicar la regla de reemplazo de caracteres especiales para la clave del GPS
		];

		#Reglas para el Nombre de la Mercanc�a
		$mRules['nom_mercan'] = [
			['name' => 'replaceSpecialChars', 'active' => false] #No quiero aplicar la regla de reemplazo de caracteres especiales para el Nombre de la Mercanc�a
		];

		#Reglas para la Clave de Usuario
		$mRules['clave'] = [
			['name' => 'replaceSpecialChars', 'active' => false], #No quiero aplicar la regla de reemplazo de caracteres especiales para la Clave de Usuario
			['name' => 'addSlashes', 'active' => false]
		];

		return $mRules;
	}
	catch(Exception $e) 
	{
		$msgError = "Se ha presentado un error en la funci�n \"".__FUNCTION__."\", generando lo siguiente:" . PHP_EOL . PHP_EOL;

		$msgError .= $e->getMessage();

		writeLogError($msgError);
	}
}

#Se valida si no existe la funci�n, ya que est� definida desde la versi�n de PHP 5.5, y actualmente (Fecha de creado este archivo) es la versi�n 5.4 ��
if (!function_exists('array_column')) {
	function array_column(array $input, $columnKey, $indexKey = null) {
		$result = array();

		foreach ($input as $row) {
			if (isset($row[$columnKey])) {
				if ($indexKey !== null && isset($row[$indexKey])) {
					$result[$row[$indexKey]] = $row[$columnKey];
				} else {
					$result[] = $row[$columnKey];
				}
			}
		}

		return $result;
	}
}

/**
 *	@method   writeLogError
 *	@abstract Genera el log correspondiente en caso de que algo falle en la ejecuci�n del archivo
 *	@param    Se maneja un par�metro:
 *				- $msgError: Mensaje de error a almacenar en el Log
 *	@return   Nada
 *
 *	@author   Ing. Andr�s Mantilla
 *	@since    2024-12-09
 */
function writeLogError($msgError = "") {

	#Se define la ruta del archivo del Log
	$mLogFile = '/backup/restore/logSanitizeData.log';

	#Mensaje a almacenar en el archivo Log
	$msgLog = "";

	#Se define el tiempo de ejecuci�n
	$timestamp = date('Y-m-d H:i:s');

	#Se valida si existe el archivo del Log
	if (file_exists($mLogFile))
	{
		#Se valida si ya hay alg�n contenido en el archivo, para agregar un separador adicional
		if (filesize($mLogFile) > 0)
		{
			#Se deja un salto de l�nea
			$msgLog .= PHP_EOL;

			#Se deja una separaci�n por guiones
			for ($i=0; $i < 135; $i++)
			{
				$msgLog .= "-";
			}
			
			#Se deja un salto de l�nea
			$msgLog .= PHP_EOL;
		}

		#Se deja una separaci�n de almohadillas/numerales, indicando que inicia el Log
		for ($i=0; $i < 83; $i++)
		{
			$msgLog .= "#";
		}
		
		#Se define el contenido del Log
		/***************************************************************************/
		$msgLog .= PHP_EOL;
		$msgLog .= "Log generado en la fecha [ {$timestamp} ]" . PHP_EOL;
		$msgLog .= PHP_EOL;
		$msgLog .= $msgError;
		$msgLog .= PHP_EOL;
		/***************************************************************************/

		#Se deja una separaci�n de almohadillas/numerales, indicando que termina el Log
		for ($i=0; $i < 83; $i++)
		{
			$msgLog .= "#";
		}

		#Se agrega el mensaje del log en el archivo correspondiente
		file_put_contents($mLogFile, $msgLog, FILE_APPEND | LOCK_EX);
	}
}

#Se ejecuta la funci�n de Desinfecci�n
/*************************************************/
sanitizeRequest();
/*************************************************/
?>