<?php

class TablaImpactos
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
  
  private function principal()
  {
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/min.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/jquery.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/es.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/time.js' ></script>\n";
    
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/jquery.blockUI.js' ></script>\n";
    
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/colorpicker.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/eye.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/utils.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/layout.js?ver=1.0.2' ></script>\n";
    
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/LoadImpactos.js' ></script>\n";
    
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
    
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/colorpicker.css' type='text/css'>";
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/layout.css' type='text/css'>";

    $formulario = new Formulario ( "index.php", "post", "INSERTAR DESPACHO", "formulario" );
		echo "<td>";
		$formulario->oculto("Standa\" id=\"StandaID\"", DIR_APLICA_CENTRAL, 0);
    $formulario->oculto("filter\" id=\"filterID\"", COD_FILTRO_EMPTRA, 0);
    $formulario->oculto("transp\" id=\"transpID\"", '', 0);
		echo "<td></tr>";
		echo "<tr>";
		echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0'>";
		echo "<tr>";
		echo "<td class='celda_titulo2' style='padding:4px;' width='100%' colspan='4' >Datos B&aacute;sicos de Despacho</td>";
		echo "</tr>";
		echo "<tr>";
    $readonly = ''; 
    $filter = $this -> VerifyTranspor();
    if( sizeof( $filter ) > 0 )
    { 
      $TRANSP = $this -> getTransp( $filter['clv_filtro'] );
      $readonly = 'readonly="readonly" value="'.$TRANSP[0][0].' - '.$TRANSP[0][1].'"';
    }
    
		echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' align='right' >
				Nit / Nombre Cliente FARO: </td>";
		echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' >
				<input class='campo_texto' type='text' size='35' name='cod_transp' id='cod_transpID' ".$readonly."/></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td  class='celda_etiqueta' style='padding:4px;' align='center' colspan='4' >
				<input class='crmButton small save' style='cursor:pointer;' type='button' value='Buscar' onclick='ValidateTransp();'/></td>";
		echo "</tr>";
		echo "</table></td>";
		$formulario -> cerrar();
    
    echo "<center><br><div id='resultID' style='background-color: rgb(240, 240, 240);border: 1px solid rgb(201, 201, 201); padding: 5px; width: 98%; min-height: 50px; border-radius: 5px 5px 5px 5px;'></div></center>";
    echo "<center><div id='PopUpID'></div></center>";
    // echo "<script>MainForm();</script>";
  }
  
  private function VerifyTranspor()
  {
    if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) {
      //--------------------------
      //@PARA EL FILTRO DE EMPRESA
      //--------------------------
      $filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_usuari'] );
      if ( $filtro -> listar( $this -> conexion ) ) : 
      $datos_filtro = $filtro -> retornar();
      endif;
    }
    else { 
      //--------------------------
      //@PARA EL FILTRO DE EMPRESA
      //--------------------------
      $filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION['datos_usuario']['cod_perfil'] );
      if ( $filtro -> listar( $this -> conexion ) ) : 
      $datos_filtro = $filtro -> retornar();
      endif;
    }
    return $datos_filtro;
  }
  
}

$centro = new TablaImpactos( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );


 ?>