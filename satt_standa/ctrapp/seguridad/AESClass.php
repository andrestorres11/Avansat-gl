<?php 

class Cypher{

	function __construct(){

	}

	function cypher($text, $key){

		$Allowed_Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
		$Chars_Len = 63;
		$Salt_Length = 21;
		$salt = "";
		$Blowfish_Pre='$2y$12$';
		$Blowfish_End='$';

		for ($i = 0; $i < $Salt_Length; $i++) {
			$salt.= $Allowed_Chars[mt_rand(0, $Chars_Len) ];
		}

		$bcrypt_salt = $Blowfish_Pre . $salt . $Blowfish_End;

		return $bcrypt_salt;

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