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
    switch ($_REQUEST['opcion']) {
      case 3:
          $this->ExportExcel();
        break;
      
      default:
          $this -> principal();
        break;
    }
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
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>";
    
    
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
    $formulario -> oculto("cod_servic",$_REQUEST['cod_servic'],0);
    $formulario -> cerrar();     
    
		$mHtml  = '</tr><tr><td>';
    $mHtml .= '<div id="ResultInsertID"></div>';
    $mHtml .= '<center><div id="mainListID" class="StyleDIV">';
    $mHtml .= '</div>';
    $mHtml .= '<div id="PopUpID"></div></center></td>';
    echo $mHtml;
    
    echo '<script>mainList();</script>';
    
  }
  /*! \fn: ExportExcel
  * \brief: Exportar a excel consuta
  * \author: Edward Serrano
  * \date: 31/03/2017
  * \date modified: dia/mes/año
  * \param: paramatro
  * \return valor que retorna
  */
  private function ExportExcel()
  {
    session_start();
    $date=date("Y_m_d_h_s");
    $consulta = new Consulta($_SESSION["queryXLS"],  $this->conexion);
    $mData = $consulta -> ret_matriz( "i" );
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Destinatarios".$date.".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    ob_clean();
    echo "<table>";
    echo "  <tr>";
    echo "     <th>Despacho</th>";
    echo "     <th>No. Viaje</th>";
    echo "     <th>Estado</th>";
    echo "     <th>Placa</th>";
    echo "     <th>Manifiesto</th>";
    echo "     <th>Fecha</th>";
    echo "     <th>Tipo Despacho</th>";
    echo "     <th>Origen</th>";
    echo "     <th>Destino</th>";
    echo "     <th>Cant. Clientes</th>";
    echo "  </tr>";
    foreach ($mData as $key => $value) 
    {
      echo "  <tr>";
      echo "     <td>".$value["num_despac"]."</td>";
      echo "     <td>".$value["num_desext"]."</td>";
      echo "     <td>".$value["ind_modifi"]."</td>";
      echo "     <td>".$value["num_placax"]."</td>";
      echo "     <td>".$value["cod_manifi"]."</td>";
      echo "     <td>".$value["fec_despac"]."</td>";
      echo "     <td>".$value["nom_tipdes"]."</td>";
      echo "     <td>".$value["nom_ciuori"]."</td>";
      echo "     <td>".$value["nom_ciudes"]."</td>";
      echo "     <td>".$value["num_client"]."</td>";
      echo "  </tr>";
    }
  }
  
}

$centro = new AsignaDestinatarios( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );


 ?>