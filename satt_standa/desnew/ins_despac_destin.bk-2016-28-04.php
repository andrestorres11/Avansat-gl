<?php

class AsignaDestinatarios
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
  
  private function Style()
  {
    echo '<style>
          .StyleDIV
          {
            background-color: rgb(240, 240, 240);
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 99%; 
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
          .label-tr
      {
        padding-right:3px;
        padding-top:8px; 
        font-family:Trebuchet MS, Verdana, Arial; 
        font-size:12px;
        border-bottom: 1px solid #CDCDCD;
        border-top: 1px solid #FFFFFF;
      }
      
      .label-info
      {
        padding-right:3px;
        padding-top:8px; 
        font-family:Trebuchet MS, Verdana, Arial; 
        font-size:12px;
      }
      
      .label-tr2
      {
        padding-right:3px;
        padding-top:8px; 
        font-family:Trebuchet MS, Verdana, Arial; 
        font-size:10px;
        border-bottom: 1px solid #CDCDCD;
        border-top: 1px solid #FFFFFF;
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
          </style>';
  }
  
  private function principal()
  { 
    if( $_REQUEST['fec_inicia'] == NULL || $_REQUEST['fec_inicia'] == '' )
    {
      $fec_actual = strtotime( '-30 day', strtotime( date('Y-m-d') ) );
      $_REQUEST['fec_inicia'] = date( 'Y-m-d', $fec_actual );
    }
    
    if( $_REQUEST['fec_finali'] == NULL || $_REQUEST['fec_finali'] == '' )
      $_REQUEST['fec_finali'] = date('Y-m-d');
      
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/min.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/jquery.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/es.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/time.js' ></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/LoadAsignaDestin.js' ></script>\n";
    
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
    $this -> Style();
    
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
    $formulario = new Formulario ( "index.php", "post", "DESTINATARIOS", "formulario" );
    
    $formulario -> texto( "* Fecha Inicial:", "text", "fec_inicia\" readonly id=\"fec_iniciaID", 0, 10, 10, "", $_REQUEST['fec_inicia'] );
    $formulario -> texto( "* Fecha Final:", "text", "fec_finali\" readonly id=\"fec_finaliID", 1, 10, 10, "", $_REQUEST['fec_finali'] );
    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar","mainList();",0);
    $formulario -> nueva_tabla();
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("opcion\" id=\"opcionID",99,0);
    $formulario -> oculto("Standa\" id=\"StandaID",DIR_APLICA_CENTRAL,0);
    $formulario->oculto("num_despac\" id=\"num_despacID\"", "", 0);
    $formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);
    $formulario -> cerrar();     
    
		$mHtml  = '</tr><tr><td>';
    $mHtml .= '<div id="ResultInsertID"></div>';
    $mHtml .= '<center><div id="mainListID" class="StyleDIV">';
    $mHtml .= '</div>';
    $mHtml .= '<div id="PopUpID"></div></center></td>';
    echo $mHtml;
    
    echo '<script>mainList();</script>';
    
  }
  
}

$centro = new AsignaDestinatarios( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );


 ?>