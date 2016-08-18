<?php
/***************************************/
/* HOMOLOGACION DE CAUSAS DE SAFERBO ***/
/* ENERO 16 DE 2015                  ***/
/* ING. ANDRÉS FELIPE MALAVER        ***/
/***************************************/

class Homologar
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
  
  private function Style()
  {
    echo '<style>
          .StyleDIV
          {
            background-color: rgb(240, 240, 240);
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 100%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .Style2DIV
          {
            background-color: #FFFFFF;
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 96%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .TRform
          {
            padding-right:3px; 
            padding-top:15px; 
            font-family:Trebuchet MS, Verdana, Arial; 
            font-size:12px;
          }
          </style>';
  }
  
  private function principal()
  {
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/min.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/jquery.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/es.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/time.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/blockUI.jquery.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/hom_causas_saferb.js' ></script>\n";
    
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
    $this -> Style();
    $formulario = new Formulario ( "index.php", "post", "HOMOLOGAR CAUSAS SAFERBO", "formulario" );
	$formulario -> oculto("Standa\" id=\"StandaID\"", DIR_APLICA_CENTRAL, 0);
    $formulario -> oculto("filter\" id=\"filterID\"", COD_FILTRO_EMPTRA, 0);
    
		
    $mHtml =  '<center> <div id="mainFormID" class="StyleDIV">';
		$mHtml .= '</div></center>';
    
    echo $mHtml;
    $formulario -> cerrar();
    echo '<script>MainLoad();</script>';
    echo "<center><div id='PopUpID'></div></center>";
  }

}

$_PROC = new Homologar( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo  );

?>