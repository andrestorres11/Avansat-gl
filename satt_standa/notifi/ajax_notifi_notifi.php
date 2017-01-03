<?php
//ini_set('display_errors', true);

class AjaxNotifiNotifi
{
  	private static  $cConexion,
	                $cCodAplica,
	                $cUsuario;
	public function __construct()
	{
		$_AJAX=$_REQUEST;
	    @include_once( "../lib/ajax.inc" );
	    @include_once( "../lib/general/constantes.inc" );
	    self::$cConexion = $AjaxConnection;

		switch($_AJAX['option'])
		{
		    case 'getFormGeneral':
		       	self::getFormGeneral();
		    break;

		    default:
		      	#header('Location: index.php?window=central&cod_servic=20151235&menant=20151235');
		    break;
		}
	}
	public function getFormGeneral()
	{
		$datos = (object) $_POST;
		$mHtml = new FormLib();
		#print_r($datos->fec_iniID);
		#echo "fecha inicial: ". $datos->fec_iniID ."; fecha fin: ". $datos->fec_finID;
		$mHtml->Table("tr");
			$mHtml->Label( "NOTIFICACIONES GENERADAS EN EL PERIDODO DEL ". $datos->fec_iniID. " AL ".$datos->fec_finID, array("colspan"=>"4", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
			$mHtml->CloseRow();
		$mHtml->CloseTable('tr');
		echo $mHtml->MakeHtml();
	}
}
$notifi = new AjaxNotifiNotifi( );
?>