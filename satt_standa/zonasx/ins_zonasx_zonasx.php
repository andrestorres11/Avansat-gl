<?php

class Zonas
{
  var $conexion,
      $usuario,
      $cod_aplica;
  
  function __construct( $co, $us, $ca )
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }
  

  private function principal()
  {
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/min.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/jquery.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/es.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/time.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/Zonas.js' ></script>\n";
    
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>";
    
    $formulario = new Formulario ( "index.php", "post", "ZONAS", "formulario" );
		$formulario->oculto("Standa\" id=\"StandaID\"", DIR_APLICA_CENTRAL, 0);
    $formulario->oculto("filter\" id=\"filterID\"", COD_FILTRO_EMPTRA, 0);
    
		
    $mHtml =  '<center> <div id="mainFormID" class="StyleDIV">';
		$mHtml .= '</div></center>';
    
    echo $mHtml;
    $formulario -> cerrar();
    echo '<script>MainLoad();</script>';
    echo "<center><div id='PopUpID'></div></center>";
  }

}

$_PROC = new Zonas( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo  );

?>