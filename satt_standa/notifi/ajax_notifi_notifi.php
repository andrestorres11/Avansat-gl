<?php
//ini_set('display_errors', true);

class AjaxNotifiNotifi
{
  	private static  $cConexion,
	                $cCodAplica,
	                $cUsuario;
	public function __construct($co = null, $us = null, $ca = null)
	{
		self::$cConexion = $co;
		self::$cUsuario = $us;
		self::$cCodAplica = $ca;
		$_AJAX = $_REQUEST;  
		print_r( $_AJAX['option']);
		/*switch($_AJAX['option'])
		{
		    case 'getFormGeneral':
		       	self::getFormGeneral();
		    break;

		    default:
		      	header('Location: index.php?window=central&cod_servic=1366&menant=1366');
		    break;
		}*/
	}
	/*function getFormGeneral()
	{
		echo "prueba";
	}*/
}
$notifi = new AjaxNotifiNotifi( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );
?>