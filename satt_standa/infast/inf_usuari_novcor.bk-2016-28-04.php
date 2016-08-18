<?php

ini_set('memory_limit', '2048M');

class InfDespacUsuari
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
      case 'Inform':
        $this -> getInform();
      	break;
      
      default:
        $this -> Filters();
      	break;
    }
  }
  
  function Style()
  {
    echo "	<style>
            .cellHead
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
              background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
              color:#fff;
              text-align:center;
            }
            
            .footer
            {
              padding:5px 10px;
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
              background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
              color:#fff;
              text-align:left;
            }

            .cellHead2
            {
              padding:5px 10px;
              background: #03ad39;
              background: -webkit-gradient(linear, left top, left bottom, from( #03ad39 ), to( #00660f )); 
              background: -moz-linear-gradient(top, #03ad39, #00660f ); 
              background-image: -ms-linear-gradient(top, #00660f 0%,#00660f 100%); 
              background-image: linear-gradient(to bottom, #00660f 0%,#00660f 100%); 
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#03ad39', endColorstr='#00660f',GradientType=0 );
              color:#fff;
              text-align:right;
            }

            tr.row:hover  td
            {
              background-color: #9ad9ae;
            }
            .cellInfo
            {
              padding:5px 10px;
              background-color:#fff;
              border:1px solid #ccc;
            }

            .cellInfo2
            {
              padding:5px 10px;
              background-color:#9ad9ae;
              border:1px solid #ccc;
            }

            .label
            {
              font-size:12px;
              font-weight:bold;
            }

            .select
            {
              background-color:#fff;
              border:1px solid #009617;
            }

            .boton
            {
              background: -webkit-gradient(linear, left top, left bottom, from( #009617 ), to( #00661b )); 
              background: -moz-linear-gradient(top, #009617, #00661b ); 
              filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#009617', endColorstr='#00661b');
              color:#fff;
              border:1px solid #fff;
              padding:3px 15px;
              -webkit-border-radius: 5px;
              -moz-border-radius: 5px;
              border-radius: 5px;
            }

            .boton:hover
            {
              background:#fff;
              color:#00661b;
              border:1px solid #00661b;
              cursor:pointer;
            }
            
            .StyleDIV
            {
              min-height: 300px; 
            }
    </style>";
  }
  
  function getUsuario( $cod_usuari = NULL )
  {
    $mSelect = "SELECT cod_usuari, UPPER( nom_usuari ) AS nom_usuari 
                  FROM ".BASE_DATOS.".tab_genera_usuari 
                 WHERE usr_emailx LIKE '%corona%'
                 GROUP BY 1 
                 ORDER BY 2";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    return $consulta -> ret_matriz();
    
  }
  
  function Filters()
  {
    $this -> Style();
    $_TIPINF = array
              (
                array('D', 'Diario'  ),
                array('S', 'Semanal' ),
                array('M', 'Mensual' )
              );
    
    $_USUARI = $this -> getUsuario();
    
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
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/inf_usuari_novcor.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";

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
        
    /************************* FORMULARIO *************************/
    $formulario = new Formulario ("index.php","post","Usuario Asignado a Novedad","form\" id=\"formID");
    
    $formulario -> texto( "* Fecha Inicial:", "text", "fec_inicia\" readonly id=\"fec_iniciaID", 0, 10, 10, "", $_REQUEST['fec_inicia'] );
    $formulario -> texto( "* Fecha Final:", "text", "fec_finali\" readonly id=\"fec_finaliID", 1, 10, 10, "", $_REQUEST['fec_finali'] );

    $formulario -> texto( "* Hora Inicial:", "text", "hor_inicia\" readonly id=\"hor_iniciaID", 0, 10, 10, "", $_REQUEST['hor_inicia'] );
    $formulario -> texto( "* Hora Final:",   "text", "hor_finali\" readonly id=\"hor_finaliID", 1, 10, 10, "", $_REQUEST['hor_finali'] );

    $formulario -> lista ("Usuario:","cod_usuari\" id=\"cod_usuariID",array_merge($this -> cNull, $_USUARI ),0 );
    $formulario -> lista ("* Tipo Informe:","cod_tipinf\" id=\"cod_tipinfID",array_merge($this -> cNull, $_TIPINF ),1 );
    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar","Validate();",0);
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
    
    /*************************************************************/
  }
  
}

$_INFORM = new InfDespacUsuari( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>