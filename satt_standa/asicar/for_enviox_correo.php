<?php

class for_enviox_correo
{
 var $conexion,
     $cod_aplica,
     $usuario;

 function __construct($co = null, $us = null, $ca = null)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
 }

 public function correoFormulAsisCar()
 {
   $html='';
   
   return $html;
 }

 
}

$proceso = new for_enviox_correo($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
