<?php
class InterfSolicitud {
	function setRuta(){
		echo "InterfSolicitud > setRuta > ";
		print_r($_POST);
	}
	function setSoliciSegimientoEspecial(){
		echo "InterfSolicitud > setSoliciSegimientoEspecial > ";
		print_r($_POST);
	}
	function setSoliciPQR(){
		echo "InterfSolicitud > setSoliciPQR > ";
		print_r($_POST);
	}
	function setSoliciOtros(){
		echo "InterfSolicitud > setSoliciOtros > ";
		print_r($_POST);
	}
}