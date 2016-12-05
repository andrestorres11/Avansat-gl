<?php 

class Cypher{

	function __construct(){

	}

	function cypher($text, $key){

		// Proceso de cifrado
		$iv    = 'abcdefghijklmnopqrstuvwxyz012345';
		$td = mcrypt_module_open('rijndael-256', '', 'ecb', '');
		mcrypt_generic_init($td, $key, $iv);
		$texto_cifrado = mcrypt_generic($td, $text);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		// Opcionalmente codificamos en base64
		$texto_cifrado = base64_encode($texto_cifrado);

		return $texto_cifrado;

	}

	function decipher($text, $key){

		// Opcionalmente descodificamos en base64
		$texto_cifrado = base64_decode($text);

		// Proceso de descifrado
		$td = mcrypt_module_open('rijndael-256', '', 'ecb', '');
		mcrypt_generic_init($td, $key, $iv);
		$texto = mdecrypt_generic($td, $texto_cifrado);
		$texto = trim($texto, "\0");

		return $texto;
	}

}
?>