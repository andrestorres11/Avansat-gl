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
    $validacion=$this->getValidacionPerfil();
    $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
    $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
    $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
    $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));
    $mHtml->Hidden(array( "name" => "cod_transp", "id" => "cod_transpID", 'value'=>($validacion==FALSE ? "" : $validacion."-")));
    $mHtml->Hidden(array( "name" => "resultado", "id" => "resultado", 'value'=>$_REQUEST['resultado']));
    $mHtml->Hidden(array( "name" => "opera", "id" => "opera", 'value'=>$_REQUEST['operacion']));
    $mHtml->Hidden(array( "name" => "filter", "id" => "filterID", 'value'=>COD_FILTRO_EMPTRA));
      # Construye accordion
      $mHtml->Row("td");
        $mHtml->OpenDiv("id:contentID; class:contentAccordion");
          # Accordion1
        if($validacion==FALSE)
        {
          $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
            $mHtml->SetBody("<h1 style='padding: 6px' ><b>INSERTAR DESPACHO</b></h1>");
            $mHtml->OpenDiv("id:sec1;");
              $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                $mHtml->Table("tr");
                    $mHtml->Label("Nit/Nombre Cliente:", "width:35%; :1;");
                    $mHtml->Input(array("name" => "trasp[nom_transp]", "id" => "nom_transpID", "width" => "35%"));
                    $mHtml->SetBody("<td><div id='boton'></div></td>");  
                $mHtml->CloseTable("tr");
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        }
        else
        {
          $mHtml->Hidden(array( "name" => "trasp[nom_transp]", "id" => "nom_transpID", 'value'=>$validacion."-"));
        }
          
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

  private function getValidacionPerfil(){

    if($_SESSION['datos_usuario']['cod_perfil']=="")
    {
      $filtro = new Aplica_Filtro_Usuari(COD_APLICACION ,COD_FILTRO_EMPTRA,$_SESSION['datos_usuario']['cod_usuari']);
    }
   else
    {
      $filtro = new Aplica_Filtro_Perfil(COD_APLICACION ,COD_FILTRO_EMPTRA,$_SESSION['datos_usuario']['cod_perfil']);
    }

    if($filtro -> listar($this -> conexion))
    {
      $datos_filtro = $filtro -> retornar();
      $respuesta=$datos_filtro['clv_filtro'];
    }
    else
    {
      $respuesta=FALSE;
    }
    return $respuesta;

  }

}

$centro = new InsertDespacho( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );


 ?>