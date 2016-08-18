<?php
/*! \file: par_recome_recome.php
 *  \brief: Crea, Lista, Actualiza y Elimina Recomendaciones
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 25/05/2015
 *  \bug: 
 *  \warning: 
 */

/*! \class: Recomend
 *  \brief: Clase principal de Recomendaciones
 */
class Recomend
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

  
  
  /*! \fn: principal
   *  \brief: funcion principal para las recomendaciones
   *  \author: Ing. Fabian Salinas
   *  \date: 25/05/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return:
   */
  private function principal()
  {
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
    
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
    
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.blockUI.js\"></script>\n";
    
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/par_recome_recome.js' ></script>\n";
    
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";

    $formulario = new Formulario ( "index.php", "post", "PARAMETRIZAR RECOMENDACIONES", "formulario" );
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

$centro = new Recomend( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );

?>