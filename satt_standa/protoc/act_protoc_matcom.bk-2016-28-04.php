<?php

ini_set('memory_limit', '1024M');

class ActProtocMatcom
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

            .CellHead
            {
              font-family:Trebuchet MS, Verdana, Arial;
              font-size:13px;
              background-color: #35650F;
              color:#FFFFFF;
              padding: 4px;
            }
            
            .cellInfo1
            {
              font-family:Trebuchet MS, Verdana, Arial;
              font-size:11px;
              background-color: #EBF8E2;
              padding: 2px;
            }
            
            .cellInfo2
            {
              font-family:Trebuchet MS, Verdana, Arial;
              font-size:11px;
              background-color: #DEDFDE;
              padding: 2px;
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
  
  function Filters()
  {
    $this -> Style();
    
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
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/ActProtocMatcom.js\"></script>\n";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
        
    /************************* FORMULARIO *************************/
    $formulario = new Formulario ("index.php","post","Actualización de Correos","form\" id=\"formID");
    
    /*$formulario -> texto( "* Transportadora:", "text", "cod_transp\" id=\"cod_transpID", 0, 50, 60, "", $_REQUEST['cod_transp'] );*/
    $formulario -> texto( "Usuario SAT:",   "text", "nom_usuari\" id=\"nom_usuariID\" onchange=\"getData();", 1, 30,  30,  "", $_REQUEST['nom_usuari'] );
    
    $formulario -> texto( "* Correo Actual:", "text", "dir_coract\" id=\"dir_coractID", 1, 50, 50, "", $_REQUEST['dir_coract'] );
    $formulario -> texto( "* Nuevo Correo:", "text", "dir_cornue\" id=\"dir_cornueID", 1, 50, 50, "", $_REQUEST['dir_cornue'] );
    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar","Validate();",0);
    $formulario -> nueva_tabla();
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("opcion\" id=\"opcionID",99,0);
    $formulario -> oculto("standa\" id=\"StandaID",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);
    $formulario -> cerrar();     
    
    $mHtml  = '</tr><tr><td><center>';
    $mHtml .= '<div id="resultID" class="StyleDIV" align="center">';
    $mHtml .= '</div>';
    $mHtml .= '</center></td>';
    echo $mHtml;
    
    echo '<div id="PopUpID" style="display:none;max-height:500px;"></div>';
    /**************************************************************/
    
  }
}

$_MATCOM = new ActProtocMatcom( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>