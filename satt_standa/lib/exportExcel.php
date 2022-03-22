<?php

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

class ExportExcel
{
	var $cBuscar = NULL;
	var $cCambia = NULL;
	function __construct()
	{
		$this -> cBuscar = array('width=""', 'height=""');
		$this -> cCambia = array('', '');

		switch ($_REQUEST['OptionExcel']) {
			case '_REQUEST':
				self::exportExcel1( $_REQUEST['exportExcel'] );
				break;

			case '_SESSION':
				session_start();
				self::exportExcel1( $_SESSION['exportExcel'] );
				break;
			
			default:
				header('Location: index.php?window=central&cod_servic=1366&menant=1366');
				break;
		}
	}

	private function exportExcel1( $mHtml = null )
	{
		#header('Content-Type: application/vnd.ms-excel'); // This should work for IE & Opera 
		header('Content-type: application/x-msexcel'); // This should work for the rest 
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("content-disposition: attachment; filename=".$_REQUEST['nameFile'].".xls");
		 
		echo $mExcel = str_replace($this -> cBuscar, $this -> cCambia, utf8_decode($mHtml) );
	}

	function __destruct(){
	}
}

$_EXPORT = new ExportExcel();

?>