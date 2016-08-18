<?php

// ini_set('memory_limit', '2048M');

class InformPendientesMobile
{
  var $conexion,
      $cod_aplica,
      $usuario;
  var $cNull = array( array('', '- Todos -') );
  
  function __construct( $co, $us, $ca )
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }
  
  function principal()
  {
    switch( $_REQUEST['option'] )
    {
      default:
        $this -> Filters();
      break;
    }
  }
  
 

  function Filters()
  {
   
    include_once( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );
    echo "<link rel=\"stylesheet\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css\" type=\"text/css\">";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/time.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.blockUI.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/inf_pendie_mobile.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>";

    /************************* FORMULARIO *************************/
    $formulario = new Formulario ("index.php","post","Pendientes Aplicativo Movil","form\" id=\"formID");
    
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("standa\" id=\"standaID",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);
    $formulario -> cerrar();     
    
    $mHtml  = '</tr><tr><td><center>';
    $mHtml .= '<div id="resultID" class="StyleDIV" align="center">';
    $mHtml .= '</div>';
    $mHtml .= '<div id="detailsID" align="center">';
    $mHtml .= '</div>';
    $mHtml .= '</center></td>';
    echo $mHtml;

    echo '<script>loadInform();</script>';
    /*************************************************************/
  }
}

$_INFORM = new InformPendientesMobile( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>