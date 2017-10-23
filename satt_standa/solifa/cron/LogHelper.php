<?php
/*! \file: LogHelper.php
 *  \brief: clase para controlar en la aplicacion los mensajes y su formato
 *  \author: INTRARED <fesus.rocuts@intrared.net>
 *  \version: 1.0
 *  \date:    29/09/2016
 *  \bug: ""
 *  \warning: en la funcion log, solo debe estar descomentareada en modo de desarrollo
 *  \warning: la funcion error es la unica que puede trabajar en producción que trate las excepciones
 */

 /*! \class: LogHelper
  *  \brief: clase para controlar en la aplicacion los mensajes y su formato
  */
class LogHelper
{
	/*! \fn: __construct
	 *  \brief: que inicializa y lanza por defecto fn log con el mensaje
	 */
	public function __construct($msg)
	{
		try{
			//metodo por defecto para mostrar mensajes
			self::log($msg);
		}catch(Exception $e){
			echo $e;
		}
	}
	/*! \fn: log
	 *  \brief: mensajes para modo desarrollo, visualiza los mensajes que quiere ver en entorno de desarrollo
	 */
	public static function log($msg)
	{
		try{
			if(is_array($msg) || is_object($msg)){
				print_r($msg);
			}else {
				echo $msg;
				echo self::getSeparator();
			}
		}catch(Exception $e){
			echo $e;
		}
	}
	/*! \fn: info
	 *  \brief: mensajes para modo desarrollo similar a log
	 */
	public static function info($msg)
	{
		try{
			/*if(is_array($msg) || is_object($msg)){
				print_r($msg);
			}else {
				echo $msg;
				echo self::getSeparator();
			}*/
		}catch(Exception $e){
			echo $e;
		}
	}
	/*! \fn: error
	 *  \brief: mensajes para modo produccion, solo se visualiza los errores se deben tratar por el administrador
	 */
	public static function error($msg)
	{
		try{
			if(is_array($msg) || is_object($msg)){
				echo self::getSeparator();
				echo "-----------------------------------------------";
				echo self::getSeparator();
				print_r($msg);
				echo self::getSeparator();
				echo "-----------------------------------------------";
				echo self::getSeparator();
			}else {
				echo self::getSeparator();
				echo "-----------------------------------------------";
				echo self::getSeparator();
				echo $msg;
				echo self::getSeparator();
				echo "-----------------------------------------------";
				echo self::getSeparator();
			}
		}catch(Exception $e){
			echo $e;
		}
	}
	public static function getSeparator(){
		if(!isset($_SERVER["HTTP_HOST"])) 
			return "\n\r";
		return "<br>";
	}
}

?>
