<?php

class ModuloSubcausas
{
  var $conexion,
      $usuario,
      $cod_aplica;
  public function __construct( $co, $us, $ca )
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }
  private function principal()
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
    
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.blockUI.js\"></script>\n";
    
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/asi_subcau_protoc.js' ></script>\n";
    
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";

    $formulario = new Formulario ( "index.php", "post", "ASIGNAR SUBCAUSAS A PROTOCOLOS", "formulario" );
		echo "<td>";
		$formulario->oculto("Standa\" id=\"StandaID\"", DIR_APLICA_CENTRAL, 0);
    $formulario->oculto("filter\" id=\"filterID\"", COD_FILTRO_EMPTRA, 0);
    $formulario->oculto("transp\" id=\"transpID\"", '', 0);
		echo "<td></tr>";
		echo "<tr>";
		echo "<br><div class='StyleDIV' id='mainDiv'>";
    echo "</div>";
		echo "</tr>";
		echo "</td>";
		$formulario -> cerrar();
    
    echo "<center><div id='resultID'></div></center>";
    echo "<center><div id='PopUpID'></div></center>";
    
    echo "<script>MainLoad();</script>";
  }
}

$centro = new ModuloSubcausas( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );


 ?>