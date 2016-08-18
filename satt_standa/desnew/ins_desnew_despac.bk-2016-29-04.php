<?php

class InsertDespacho
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
    /*echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
		
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/LoadInsertDespac.js' ></script>\n";
    
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";*/

    /*$formulario = new Formulario ( "index.php", "post", "INSERTAR DESPACHO", "formulario" );
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
				<input class='crmButton small save' style='cursor:pointer;' type='button' value='Generar' onclick='ValidateTransp();'/></td>";
		echo "</tr>";
		echo "</table></td>";
		$formulario -> cerrar();
    
    echo "<center><div id='resultID'></div></center>";
    echo "<center><div id='PopUpID'></div></center>";*/

    $mHtml = new FormLib(2);

     # incluye JS
    
    $mHtml->SetJs("time");
    $mHtml->SetJs("jquery");
    $mHtml->SetJs("functions");
    $mHtml->SetJs("LoadInsertDespac");
    $mHtml->SetJs("validator");
    $mHtml->SetCssJq("validator"); 
    $mHtml->CloseTable("tr");
    # incluye Css
    $mHtml->SetCssJq("jquery");
    $mHtml->Body(array("menubar" => "no"));

    # Abre Form
    $mHtml->Form(array("action" => "index.php",
        "method" => "post",
        "name" => "form_search",
        "header" => "Conductores",
        "enctype" => "multipart/form-data"));

  #variables ocultas
  
    $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
    $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
    $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
    $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));
    $mHtml->Hidden(array( "name" => "cod_transp", "id" => "cod_transpID", 'value'=>''));
    $mHtml->Hidden(array( "name" => "resultado", "id" => "resultado", 'value'=>$_REQUEST['resultado']));
    $mHtml->Hidden(array( "name" => "opera", "id" => "opera", 'value'=>$_REQUEST['operacion']));
    $mHtml->Hidden(array( "name" => "filter", "id" => "filterID", 'value'=>COD_FILTRO_EMPTRA));

      # Construye accordion
      $mHtml->Row("td");
        $mHtml->OpenDiv("id:contentID; class:contentAccordion");
          # Accordion1
          $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
            $mHtml->SetBody("<h2 class='fuente'><center>Insertar Despacho</center></h2>");
            $mHtml->OpenDiv("id:sec1;");
              $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                $mHtml->Table("tr");
                    $mHtml->Label("<h3>Nit/Nombre Cliente Faro:</h3>", "width:35%; :1;");
                    $mHtml->Input(array("name" => "trasp[nom_transp]", "id" => "nom_transpID", "width" => "35%"));
                    $mHtml->SetBody("<td><div id='boton'></div></td>");  
                $mHtml->CloseTable("tr");
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
          # Fin accordion1    
          
          $mHtml->SetBody("<div id='resultID'></div>");
          $mHtml->SetBody("<div id='PopUpID'></div>");
        $mHtml->CloseDiv();
      $mHtml->CloseRow("td");
      # Cierra formulario
    $mHtml->CloseForm();
    # Cierra Body
    $mHtml->CloseBody();

    # Muestra Html
    echo $mHtml->MakeHtml(); 
    
  }
  
  private function getTransp( $cod_transp )
  {
    $mSql = "SELECT cod_tercer, UPPER(abr_tercer) AS nom_tercer FROM ".BASE_DATOS.".tab_tercer_tercer WHERE cod_tercer = '".$cod_transp."' LIMIT 1";
    $consulta = new Consulta( $mSql, $this -> conexion );
    return $consulta -> ret_matriz();
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

$centro = new InsertDespacho( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );


 ?>