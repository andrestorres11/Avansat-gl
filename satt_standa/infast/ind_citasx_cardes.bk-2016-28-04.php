<?php

ini_set('memory_limit', '2048M');

class IndicadorCitasCargueDescargue
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
  
  function Filters()
  {
    $this -> Style();
    
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
    
    /*******************************************************************************/
    $mSelect = "SELECT cod_tipdes, nom_tipdes FROM ".BASE_DATOS.".tab_genera_tipdes WHERE 1 = 1 ORDER BY 2";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_TIPDES = $consulta -> ret_matriz();
    
    $mSelect = "SELECT cod_produc, nom_produc FROM ".BASE_DATOS.".tab_genera_produc WHERE ind_estado = '1' ORDER BY 2";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_PRODUC = $consulta -> ret_matriz();

    $mSelect = "SELECT cod_clasex, nom_clasex FROM ".BASE_DATOS.".tab_vehige_clases WHERE ind_estado = '1' ORDER BY 2";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_CLASES = $consulta -> ret_matriz();
    
    $mSelect = "SELECT cod_zonaxx, nom_zonaxx FROM ".BASE_DATOS.".tab_genera_zonasx WHERE ind_estado = '1' ORDER BY 2";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_ZONASX = $consulta -> ret_matriz();
    
    $mSelect = "SELECT cod_canalx, nom_canalx FROM ".BASE_DATOS.".tab_genera_canalx WHERE ind_estado = '1' ORDER BY 2";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_CANALX = $consulta -> ret_matriz();

    $mSelect = "SELECT cod_tiptra, nom_tiptra FROM ".BASE_DATOS.".tab_genera_tiptra WHERE ind_estado = '1' ORDER BY 2";
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_TIPTRA = $consulta -> ret_matriz();
    /*******************************************************************************/

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
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/ind_citasx_cardes.js\"></script>\n";
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
          
          $( "#cod_ciuoriID, #cod_ciudesID" ).autocomplete({
            source: "../'.DIR_APLICA_CENTRAL.'/infast/ajax_citasx_cardes.php?option=getOrigen&standa='.DIR_APLICA_CENTRAL.'",
            minLength: 2, 
            delay: 100
        });
        });
        </script>';
    /************************* FORMULARIO *************************/
    $formulario = new Formulario ( "index.php","post","Cumplimiento Citas de Descargue","form\" id=\"formID" );
    echo "<td>";
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("opcion\" id=\"opcionID",99,0);
    $formulario -> oculto("standa\" id=\"standaID",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);
    echo "<td></tr>";
    echo "<tr>";
    echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0'>";
    
    echo "<tr>";    
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >* Fecha Inicial:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' ><input class='campo_texto' type='text' size='10' readonly name='fec_inicia' id='fec_iniciaID' value='".$_REQUEST['fec_inicia']."'/></td>";
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >* Fecha Final:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' ><input class='campo_texto' type='text' size='10' readonly name='fec_finali' id='fec_finaliID' value='".$_REQUEST['fec_finali']."'/></td>";
    echo "</tr>";

    echo "<tr>";    
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >* Hora Inicial:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' ><input class='campo_texto' type='text' size='10' readonly name='hor_inicia' id='hor_iniciaID' value='".$_REQUEST['hor_inicia']."'/></td>";
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >* Hora Final:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' ><input class='campo_texto' type='text' size='10' readonly name='hor_finali' id='hor_finaliID' value='".$_REQUEST['hor_finali']."'/></td>";
    echo "</tr>";

    echo "<tr>";    
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >Origen:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' ><input class='campo_texto' type='text' size='40' name='cod_ciuori' id='cod_ciuoriID' value='".$_REQUEST['cod_ciuori']."'/></td>";
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >Destino:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' ><input class='campo_texto' type='text' size='40' name='cod_ciudes' id='cod_ciudesID' value='".$_REQUEST['cod_ciudes']."'/></td>";
    echo "</tr>";
    
    echo "<tr>";    
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >Tipo Despacho:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' >".$this -> generateSelect( 'cod_tipdes', $_TIPDES )."</td>";
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >Negocio y/o Producto:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' >".$this -> generateSelect( 'cod_produc', $_PRODUC )."</td>";
    echo "</tr>";
    
    echo "<tr>";    
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >Viaje:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' ><input class='campo_texto' type='text' name='num_viajex' id='num_viajexID' size='10' maxlength='10'/></td>";
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >Pedido:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' ><input class='campo_texto' type='text' name='num_pedido' id='num_pedidoID' size='10' maxlength='10'/></td>";
    echo "</tr>";
    
    echo "<tr>";    
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >Zona:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' >".$this -> generateSelect( 'cod_zonaxx', $_ZONASX )."</td>";
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >Canal:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' >".$this -> generateSelect( 'cod_canalx', $_CANALX )."</td>";
    echo "</tr>";

    echo "<tr>";    
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >Tipo Transporte:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' >".$this -> generateSelect( 'cod_tiptra', $_TIPTRA )."</td>";
      echo "<td class='celda_titulo' style='padding:4px;' width='50%' align='right' >Nombre Poseedor:</td>";
      echo "<td class='celda_info' style='padding:4px;' width='50%' ><input class='campo_texto' type='text' name='nom_poseed' id='nom_poseedID' size='40' maxlength='100'/></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td style='padding-top:20px;' align='center' colspan='4' >
        <input class='crmButton small save' style='cursor:pointer;' type='button' value='Generar' onclick='Validate();'/></td>";
    echo "</tr>";


    echo "</table><center><div style='display:none;' id='downarrowID'><img onclick='showPrefilters();' style='cursor:pointer;' src='../".DIR_APLICA_CENTRAL."/imagenes/down-arrow.gif'/></div></center></td>";
    $formulario -> cerrar();     
    
    echo '<div id="resultID" style="display:none;" class="StyleDIV" align="center"></div>';
    echo '<div id="PopUpID"></div>';
    
    /*************************************************************/
  }
  
  private function generateSelect( $name, $array, $key = NULL, $events = '' )
  {
    $mSelect  = '<select style="font-family: Arial,Helvetica,sans-serif;font-size: 12px;" name="'.$name.'" id="'.$name.'ID" '.$events.'>';
    $mSelect .= '<option value="">- Seleccione -</option>';
    foreach( $array as $row )
    {
      $selected = '';
      if( $row[0] == $key )
        $selected = 'selected';
      
      $mSelect .= '<option value="'.$row[0].'" '.$selected.'>'.$row[1].'</option>';
}
    $mSelect .= '</select>';

    return $mSelect; 
  }
  
}

$_INDICA = new IndicadorCitasCargueDescargue( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>