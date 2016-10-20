<?php

ini_set('memory_limit', '2048M');

class IndicadorEstadiaPlanta
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
  
  function getMercan()
  {
    $mSelect = "SELECT cod_produc, nom_produc 
                  FROM ".BASE_DATOS.".tab_genera_produc 
                 WHERE ind_estado = '1'
                 ORDER BY 2";
                 
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return array_merge( $this -> cNull, $consulta -> ret_matriz() );
  }
  
  function getCiudad()
  {
    $mSelect = "SELECT a.cod_ciudad, CONCAT(UPPER(a.abr_ciudad),' (',LEFT(b.abr_depart,4),')' )
                  FROM " . BASE_DATOS . ".tab_genera_ciudad a,
                       " . BASE_DATOS . ".tab_genera_depart b
                 WHERE a.cod_depart = b.cod_depart AND
                       a.cod_paisxx = b.cod_paisxx
                 GROUP BY 1 
                 ORDER BY 2";
                 
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return array_merge( $this -> cNull, $consulta -> ret_matriz() );
  }
  
  function getTipdes( $cod_tipdes = NULL )
  {
    $mSelect = "SELECT cod_tipdes, nom_tipdes 
                  FROM ".BASE_DATOS.".tab_genera_tipdes 
                 WHERE 1 = 1 ";
    
    if( $cod_tipdes != NULL )
      $mSelect .= " AND cod_tipdes = '".$cod_tipdes."' ";
    
    $mSelect .= " ORDER BY 2";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return array_merge( $this -> cNull, $consulta -> ret_matriz() );
  }
  
  function getTipveh()
  {
    $mSelect = "SELECT num_config, nom_config 
                  FROM ".BASE_DATOS.".tab_vehige_config 
                 WHERE ind_estado = '1'
                 ORDER BY 2";
                 
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return array_merge( $this -> cNull, $consulta -> ret_matriz() );
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
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/ind_estadi_planta.js\"></script>\n";
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
    /********************************/
    $_MERCAN = $this -> getMercan();
    $_CIUORI = $this -> getCiudad();
    $_TIPDES = $this -> getTipdes();
    $_TIPVEH = $this -> getTipveh();
    /********************************/
    
    /************************* FORMULARIO *************************/
    $formulario = new Formulario ("index.php","post","Indicador Estadia en Planta","form\" id=\"formID");
    
    $formulario -> texto( "* Fecha Inicial:", "text", "fec_inicia\" readonly id=\"fec_iniciaID", 0, 10, 10, "", $_REQUEST['fec_inicia'] );
    $formulario -> texto( "* Fecha Final:", "text", "fec_finali\" readonly id=\"fec_finaliID", 1, 10, 10, "", $_REQUEST['fec_finali'] );

    $formulario -> texto( "* Hora Inicial:", "text", "hor_inicia\" readonly id=\"hor_iniciaID", 0, 10, 10, "", $_REQUEST['hor_inicia'] );
    $formulario -> texto( "* Hora Final:",   "text", "hor_finali\" readonly id=\"hor_finaliID", 1, 10, 10, "", $_REQUEST['hor_finali'] );
    
    $formulario -> lista( "Producto:", "cod_mercan\" id=\"cod_mercanID", $_MERCAN, 0 );
    $formulario -> lista( "Origen:",   "cod_ciuori\" id=\"cod_ciuoriID", $_CIUORI, 1 );
    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Generar","Validate();",0);
    $formulario -> nueva_tabla();
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("opcion\" id=\"opcionID",99,0);
    $formulario -> oculto("standa\" id=\"standaID",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("cod_servic",$_REQUEST['cod_servic'],0);
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

$_INDICA = new IndicadorEstadiaPlanta( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>