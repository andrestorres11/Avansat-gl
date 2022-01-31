<?php
/*! \file: ins_tipvin_tipvin.php
 *  \brief: Administrar tipo de vinculacion
 *  \author: Edward Fabian Serrano
 *  \author: edward.serrano@intrared.net
 *  \version: 1.0
 *  \date: 12/09/2017
 *  \bug: 
 *  \warning: 
 */

/*! \class: tipoVinculacion
 *  \brief: Administrar tipo de transportadora
 */
class tipoVinculacion
{
	private static  $cConexion,
					        $cCodAplica,
					        $cUsuario;
					
	function __construct($co = null, $us = null, $ca = null)
	{
		if($_REQUEST['Ajax']=="on")
        {
            include_once( "../lib/ajax.inc" );
            self::$cConexion = $AjaxConnection;
            self::$cUsuario = $_SESSION["datos_usuario"]; 
        }
        else
        {
            self::$cConexion = $co;
            self::$cUsuario = $_SESSION["datos_usuario"];
            self::$cCodAplica = $ca;
        }
        @include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/constantes.inc' );
        @include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );
        
        switch ($_REQUEST['opcion']) {
            case 'getDinamiList':
                self::getDinamiList();
              break;
            case 'insertar':
                self::insertar();
              break;
            case 'NewTipVinculacion':
                self::NewTipVinculacion();
              break;
            default:  
                self::listar(); 
            break;
        }
	}

	/*! \fn: listar
	* \brief: Lista las novedades registradas con el perfil asociado
	* \author: Edward Serrano
	* \date: //
	* \date modified: dia/mes/año
	* \param: paramatro
	* \return valor que retorna
	*/
	private function listar()
	{
		try
		{
            IncludeJS( 'jquery17.js' );
            IncludeJS( 'jquery.js' );
              
            IncludeJS( 'functions.js' );
            IncludeJS( 'ins_tipvin_tipvin.js' );
            IncludeJS( 'dinamic_list.js' );
            IncludeJS( 'new_ajax.js' );
            IncludeJS( 'validator.js' );
            IncludeJS( 'sweetalert-dev.js' );

            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css' type='text/css'>\n";
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/validator.css' type='text/css'>\n";

            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/sweetalert.css' type='text/css'>\n";

            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
           

            $mHtml = new FormLib(2);

            $mHtml->Body(array("menubar" => "no"));

            # Abre Form
            $mHtml->Form(array("action" => "index.php", "method" => "post", "name" => "form_search", "header" => "EAL", "enctype" => "multipart/form-data"));

            #variables ocultas
          
            $mHtml->Hidden(array( "name" => "cod_tercer", "id" => "cod_tercerID", 'value'=>$mCodTransp));
            $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
            $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
            $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
            $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));

            # Construye accordion
            $mHtml->Row("td");
              $mHtml->OpenDiv("id:contentID; class:contentAccordion");
                # Accordion1
                $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
                  $mHtml->SetBody("<h1 style='padding:6px'><b>Lista de Tipos de Vinculacion</b></h1>");
                  $mHtml->OpenDiv("id:sec1");
                    $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                      $mHtml->SetBody( self::getDinamiList() );                 
                    $mHtml->CloseDiv();
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();
                # Fin accordion1
              $mHtml->CloseDiv();
            $mHtml->CloseRow("td");
              # Cierra formulario
            $mHtml->CloseForm();
            $mHtml->SetBody("<style>
                              .CellHead 
                              {
                                  background-color: #35650f;
                                  color: #ffffff;
                                  font-family: Times New Roman;
                                  font-size: 11px;
                                  padding: 4px;
                              }
                              .celda_info
                              {
                                background-color: #efefef;
                              }
                              #sec1
                              {
                                height: auto;
                              }
                            </style>");
            # Cierra Body
            $mHtml->CloseBody();
            # Muestra Html
            echo $mHtml->MakeHtml();
		}catch(Exception $e)
		{
			echo "Error funcion listar", $e->getMessage(), "\n";
		}
	}

  /*! \fn: getDinamiList
   *  \brief: dinamic list de los registros
   *  \author: Edward Serrano
   *  \date:  18/01/2017
   *  \date modified: dia/mes/año
   */
  public function getDinamiList()
  { 
    try
    {
      $sql ="SELECT a.cod_tipveh, a.nom_tipveh, IF(a.ind_estado=1,'ACTIVO','INACTIVO') AS ltr_estado, a.ind_estado AS ind_estado 
           FROM ".BASE_DATOS.".tab_genera_tipveh a";
      $_SESSION["queryXLS"] = $sql;
      if (!class_exists(DinamicList)) 
      {
          include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
      }
      $list = new DinamicList(self::$cConexion, $sql, "1", "no", 'ASC');
      $list->SetClose('no');
      $list->SetCreate("Nuevo Tipo de Vinculacion", "onclick:NewTipVinculacion( 0, null )");
      $list->SetHeader("Codigo", "field:a.cod_tipveh; width:1%;type:link; onclick:NewTipVinculacion( 1, $(this) )");
      $list->SetHeader("Tipo de Transporte", "field:a.nom_tipveh; width:1%");
      $list->SetHeader("Estado", "field:a.ltr_estado; width:1%");
      $list->SetOption("Opciones", "onclikEdit:NewTipVinculacion( 1, $(this) );onclikDisable:CambioEstado( $(this) );onclikEnable:CambioEstado( $(this) )");
      $list->SetHidden("cod_tipveh", "cod_tipveh");
      $list->SetHidden("ind_estado", "ind_estado");
      $list->Display(self::$cConexion);

      $_SESSION["DINAMIC_LIST"] = $list;

      $Html = $list->GetHtml();

      if($_REQUEST["Ajax"] === 'on' )
      {
        echo $Html;
      }
      else
      {
        return $Html;
      }
    } catch (Exception $e) {
      echo "error getDinamiList :".$e;
    }
  }

  /*! \fn: NewTipVinculacion
     *  \brief: Pinta detalle de la informacion registrada
     *  \author: Edward Serrano
     *  \date:  12/07/2017
     *  \date modified: dia/mes/año
     */
    private function NewTipVinculacion()
    {
      try
      {
          #Informacion inicial
          $mDatos = self::getTipVinculacion($_REQUEST['cod_tipveh']);
          $tippEstado = array(
                              array('0' =>  '-','1' =>  '-'),
                              array('0' =>  '1','1' =>  'ACTIVO'),
                              array('0' =>  '0','1' =>  'INACTIVO')
                              );
          $mHtml = new FormLib(2);
            $mHtml->OpenDiv("id:DatosBasicosID; class:contentAccordionForm");
              $mHtml->Hidden(array( "name" => "accion", "id" => "accionID", 'value'=>$_REQUEST['accion']));
              $mHtml->Hidden(array( "name" => "cod_tipveh", "id" => "cod_tipvehID", 'value'=>$_REQUEST['cod_tipveh']));
              $mHtml->Table("tr");
                $mHtml->Label( strtoupper("Registro"), array("align"=>"center", "width"=>"100%", "class"=>"CellHead", "colspan"=>"4", "end"=>"true") );
                $mHtml->CloseRow();
                $mHtml->Row();
                  $mHtml->Label( "*Tipo de Vinculacion:",  array("align"=>"right", "class"=>"celda_titulo", "width"=>"20%", "obl"=>"obl") );
                  $mHtml->Input( array("name"=>"nom_tipveh", "id"=>"nom_tipvehID", "width"=>"30%", "value"=>($mDatos[0]['nom_tipveh']) , "class"=>"cellInfo2") );
                  $mHtml->Label( "*Estado:",  array("align"=>"right", "class"=>"celda_titulo", "width"=>"20%", "obl"=>"obl") );
                  $mHtml->Select2 ($tippEstado,  array("name" => "ind_estado", "id" => "ind_estadoID", "width" => "30%", "key"=>($mDatos[0]['ind_estado'])) );
                $mHtml->CloseRow();
                $mHtml->Row();
                  $mHtml->Button( array("value"=>"ACEPTAR", "colspan"=>"4", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all bigButton", "align"=>"center", "onclick"=>"insertar()") ); 
                $mHtml->CloseRow();
              $mHtml->CloseTable('tr');
            $mHtml->CloseDiv();
          # Muestra Html
          echo $mHtml->MakeHtml();
      } catch (Exception $e) {
        echo "error NewTipVinculacion :".$e;
      }
    }

    /*! \fn: insertar
     *  \brief: Pinta detalle de la informacion registrada
     *  \author: Edward Serrano
     *  \date:  12/07/2017
     *  \date modified: dia/mes/año
     */
    private function insertar()
    {
      try
      {
          if($_REQUEST['accion'] == "almacenar")
          {
            #obtengo el valora maximo de la tabla
            $sqlM = "SELECT IF(MAX(cod_tipveh) IS NULL, 1, MAX(cod_tipveh)+1) AS cod_tipveh FROM ".BASE_DATOS.".tab_genera_tipveh";
            $cod_tipveh = new Consulta( $sqlM, self::$cConexion);
            $cod_tipveh = $cod_tipveh -> ret_matrix('i');
            $cod_tipveh=$cod_tipveh[0][0];
            #Preparo la conuslta a ejecutar
            $sql ="INSERT INTO " . BASE_DATOS . ".tab_genera_tipveh 
                                          (cod_tipveh,  nom_tipveh,   ind_estado,
                                           usr_creaci,  fec_creaci
                                          )
                                  VALUES  
                                      ('".$cod_tipveh."', '".$_REQUEST['nom_tipveh']."', '".$_REQUEST['ind_estado']."', 
                                      NOW(), '".$_SESSION['datos_usuario']['cod_usuari']."')" ;
          }
          else if($_REQUEST['accion'] == "CambioEstado")
          {
            $sql ="UPDATE " . BASE_DATOS . ".tab_genera_tipveh 
                              SET
                              ind_estado = '".$_REQUEST['ind_estado']."',
                              usr_modifi = NOW(),
                              fec_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."'
                              WHERE cod_tipveh = '".$_REQUEST['cod_tipveh']."' 
                              " ;
          }
          else
          {
            $sql ="UPDATE " . BASE_DATOS . ".tab_genera_tipveh 
                              SET nom_tipveh = '".$_REQUEST['nom_tipveh']."',
                              ind_estado = '".$_REQUEST['ind_estado']."',
                              usr_modifi = NOW(),
                              fec_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."'
                              WHERE cod_tipveh = '".$_REQUEST['cod_tipveh']."' 
                              " ;
          }
          $consulta = new Consulta($sql, self::$cConexion, "BR");
          if($consulta)
          {
            $consultaFinal = new Consulta("COMMIT", self::$cConexion);
            echo "OK";
          }
          else
          {
            $consultaFinal = new Consulta("ROLLBACK", self::$cConexion);
            echo "ERROR";
          }
      } catch (Exception $e) {
        echo "error insertar :".$e;
      }
    }

    /*! \fn: getTipVinculacion
     *  \brief: obtiene la iformacion del tipo de servicio
     *  \author: Edward Serrano
     *  \date:  12/07/2017
     *  \date modified: dia/mes/año
     */
    private function getTipVinculacion($cod_tipveh)
    {
      try
      {
          if($cod_tipveh == "")
          {
            return NULL;
          }
          $sql = "SELECT cod_tipveh, nom_tipveh, ind_estado 
                      FROM ".BASE_DATOS.".tab_genera_tipveh
                      WHERE cod_tipveh ='{$cod_tipveh}';";
          $result = new Consulta( $sql, self::$cConexion);
          $result = $result -> ret_matrix('a');
          return $result;
      } catch (Exception $e) {
        echo "error getTipVinculacion :".$e;
      }
    }
}
if($_REQUEST["Ajax"] === 'on' )
{
    $tipoVinculacion = new tipoVinculacion();
}
else
{
    $tipoVinculacion = new tipoVinculacion( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );
}

?>