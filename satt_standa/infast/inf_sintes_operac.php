<?php

ini_set('memory_limit', '2048M');

class SintesisOperacion
{
  var $conexion,
      $cod_aplica,
      $usuario;
  var $cNull = array( array('', '- Todos -') );
  
  function __construct($co, $us, $ca)
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    $this -> principal();
  }
  
  function principal()
  {
    switch( $_REQUEST['opcion'] )
    {
      default:
        $this -> Filters();
      break;
    }
  }
 
  function Filters()
  {
    
    if( $_REQUEST['fec_inicia'] == NULL || $_REQUEST['fec_inicia'] == '' )
    {
      $fec_actual = strtotime( '-7 day', strtotime( date('Y-m-d') ) );
      $_REQUEST['fec_inicia'] = date( 'Y-m-d', $fec_actual );
    }
    
    if( $_REQUEST['fec_finali'] == NULL || $_REQUEST['fec_finali'] == '' )
      $_REQUEST['fec_finali'] = date('Y-m-d');
     
    if( $_REQUEST['hor_inicia'] == NULL || $_REQUEST['hor_inicia'] == '' )
    {
      $hor_actual = strtotime( '-1 hour', strtotime( date('H:i:s') ) );
      $_REQUEST['hor_inicia'] = date( 'H:i:s', $hor_actual );
    }
     
    if( $_REQUEST['hor_finali'] == NULL || $_REQUEST['hor_finali'] == '' )
      $_REQUEST['hor_finali'] = date('H:i:s');
    
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
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/inf_sintes_operac.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    echo '<script>
        jQuery(function($) 
        {
          $(".ui-datepicker-week-col").css( "color", "#FFFFFF" );
          $( "#fec_finaliID, #fec_iniciaID" ).datepicker({
            changeMonth: true,
            changeYear: true
          });
          
          $( "#hor_iniciaID, #hor_finaliID" ).timepicker();
          
          $.mask.definitions["A"]="[12]";
          $.mask.definitions["M"]="[01]";
          $.mask.definitions["D"]="[0123]";
          
          $.mask.definitions["H"]="[012]";
          $.mask.definitions["N"]="[012345]";
          $.mask.definitions["n"]="[0123456789]";
          
          $( "#fec_iniciaID, #fec_finaliID" ).mask("Annn-Mn-Dn");
        });
        
        </script>';
    
    $mSelect = "SELECT cod_tipdes, nom_tipdes 
                  FROM ".BASE_DATOS.".tab_genera_tipdes 
                 GROUP BY 1 
                 ORDER BY 2";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_TIPDES = $consulta -> ret_matriz();

    $mSelect = "SELECT cod_produc, nom_produc 
                  FROM ".BASE_DATOS.".tab_genera_produc 
                 WHERE ind_estado = '1'
                 GROUP BY 1 
                 ORDER BY 2";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_PRODUC = $consulta -> ret_matriz();

    /************************* FORMULARIO *************************/
    $formulario = new Formulario ("index.php","post","Sintesis de Operacion","form\" id=\"formID");
    
    $formulario -> texto( "* Fecha Inicial:", "text", "fec_inicia\" readonly id=\"fec_iniciaID", 0, 10, 10, "", $_REQUEST['fec_inicia'] );
    $formulario -> texto( "* Fecha Final:", "text", "fec_finali\" readonly id=\"fec_finaliID", 1, 10, 10, "", $_REQUEST['fec_finali'] );

    $formulario -> lista ( "Tipo Despacho:", "cod_tipdes\" id=\"cod_tipdesID", array_merge( $this -> cNull, $_TIPDES ), 0 );
    $formulario -> lista ( "Mercancia:",     "cod_produc\" id=\"cod_producID", array_merge( $this -> cNull, $_PRODUC ), 1 );

    $formulario -> nueva_tabla();
    $formulario -> botoni("Generar","Validate();",0);
    $formulario -> nueva_tabla();
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("opcion\" id=\"opcionID",99,0);
    $formulario -> oculto("standa\" id=\"standaID",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);
    $formulario -> cerrar();     
    
    $mHtml  = '</tr><tr><td><center>';
    $mHtml .= '<div id="resultID" class="StyleDIV" align="center">';
    $mHtml .= '</div>';
    $mHtml .= '</center></td>';
    echo $mHtml;
    
    echo '<div id="PopUpID" style="display:none;max-height:500px;"></div>';
    echo '<div id="IndicaID" style="display:none;max-height:500px;"></div>';
    echo '<div id="RangosID" style="display:none;max-height:500px;"></div>';
    
    /*************************************************************/
  }
  
}

$_INDICA = new SintesisOperacion( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>