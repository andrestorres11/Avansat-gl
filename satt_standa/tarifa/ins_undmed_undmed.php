<?php
/*! \file: ins_undmed_undmed.php
 *  \brief: Administrar Unidades de medida
 *  \author: Edward Fabian Serrano
 *  \author: edward.serrano@intrared.net
 *  \version: 1.0
 *  \date: 12/09/2017
 *  \bug: 
 *  \warning: 
 */

/*! \class: unidadMedida
 *  \brief: Administrar tipo de transportadora
 */
class unidadMedida
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
            case 'NewUnidadMedida':
                self::NewUnidadMedida();
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
            IncludeJS( 'ins_undmed_undmed.js' );
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
            $mHtml->Form(array("action" => "index.php", "method" => "post", "name" => "form_search", "header" => "unidadMedida", "enctype" => "multipart/form-data"));

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
                  $mHtml->SetBody("<h1 style='padding:6px'><b>Lista Unidades de medida</b></h1>");
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
      $sql ="SELECT a.cod_empaqu, a.nom_empaqu, a.cod_minemp, IF(a.ind_activa=1,'ACTIVO','INACTIVO') AS ltr_estado, a.ind_activa AS ind_activa
           FROM ".BASE_DATOS.".tab_genera_empaqu a";
      $_SESSION["queryXLS"] = $sql;
      if (!class_exists(DinamicList)) 
      {
          include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
      }
      $list = new DinamicList(self::$cConexion, $sql, "1", "no", 'ASC');
      $list->SetClose('no');
      $list->SetCreate("Nueva Unidad de medida", "onclick:NewUnidadMedida( 0, null )");
      $list->SetHeader("Codigo", "field:a.cod_empaqu; width:1%;type:link; onclick:NewUnidadMedida( 1, $(this) )");
      $list->SetHeader("Nombre", "field:a.nom_empaqu; width:1%");
      $list->SetHeader("Codigo ministerio", "field:a.cod_minemp; width:1%");
      $list->SetHeader("Estado", "field:a.ltr_estado; width:1%");
      $list->SetOption("Opciones", "onclikEdit:NewUnidadMedida( 1, $(this) );onclikDisable:CambioEstado( $(this) );onclikEnable:CambioEstado( $(this) )");
      $list->SetHidden("cod_empaqu", "cod_empaqu");
      $list->SetHidden("ind_activa", "ind_activa");
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

  /*! \fn: NewUnidadMedida
     *  \brief: Pinta detalle de la informacion registrada
     *  \author: Edward Serrano
     *  \date:  12/07/2017
     *  \date modified: dia/mes/año
     */
    private function NewUnidadMedida()
    {
      try
      {
          #Informacion inicial
          $mDatos = self::getUnidadMedia($_REQUEST['cod_empaqu']);
          $tippEstado = array(
                              array('0' =>  '-','1' =>  '-'),
                              array('0' =>  '1','1' =>  'ACTIVO'),
                              array('0' =>  '0','1' =>  'INACTIVO')
                              );
          $mHtml = new FormLib(2);
            $mHtml->OpenDiv("id:DatosBasicosID; class:contentAccordionForm");
              $mHtml->Hidden(array( "name" => "accion", "id" => "accionID", 'value'=>$_REQUEST['accion']));
              $mHtml->Hidden(array( "name" => "cod_empaqu", "id" => "cod_empaquID", 'value'=>$_REQUEST['cod_empaqu']));
              $mHtml->Table("tr");
                $mHtml->Label( strtoupper("Registro"), array("align"=>"center", "width"=>"100%", "class"=>"CellHead", "colspan"=>"6", "end"=>"true") );
                $mHtml->CloseRow();
                $mHtml->Row();
                  $mHtml->Label( "*Nombre:",  array("align"=>"right", "class"=>"celda_titulo", "width"=>"20%", "obl"=>"obl") );
                  $mHtml->Input( array("name"=>"nom_empaqu", "id"=>"nom_empaquID", "width"=>"30%", "value"=>($mDatos[0]['nom_empaqu']) , "class"=>"cellInfo2") );
                  $mHtml->Label( "*Codigo ministerio:",  array("align"=>"right", "class"=>"celda_titulo", "width"=>"20%", "obl"=>"obl") );
                  $mHtml->Input( array("name"=>"cod_minemp", "id"=>"cod_minempID", "width"=>"30%", "value"=>($mDatos[0]['cod_minemp']) , "class"=>"cellInfo2", "maxlength"=>"99999") );
                  $mHtml->Label( "*Estado:",  array("align"=>"right", "class"=>"celda_titulo", "width"=>"20%", "obl"=>"obl") );
                  $mHtml->Select2 ($tippEstado,  array("name" => "ind_activa", "id" => "ind_activaID", "width" => "30%", "key"=>($mDatos[0]['ind_activa'])) );
                $mHtml->CloseRow();
                $mHtml->Row();
                  $mHtml->Button( array("value"=>"ACEPTAR", "colspan"=>"6", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all bigButton", "align"=>"center", "onclick"=>"insertar()") ); 
                $mHtml->CloseRow();
              $mHtml->CloseTable('tr');
            $mHtml->CloseDiv();
          # Muestra Html
          echo $mHtml->MakeHtml();
      } catch (Exception $e) {
        echo "error NewUnidadMedida :".$e;
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
            $sqlM = "SELECT IF(MAX(cod_empaqu) IS NULL, 1, MAX(cod_empaqu)+1) AS cod_empaqu FROM ".BASE_DATOS.".tab_genera_empaqu";
            $cod_empaqu = new Consulta( $sqlM, self::$cConexion);
            $cod_empaqu = $cod_empaqu -> ret_matrix('i');
            $cod_empaqu=$cod_empaqu[0][0];
            #Preparo la conuslta a ejecutar
            $sql ="INSERT INTO " . BASE_DATOS . ".tab_genera_empaqu 
                                          (cod_empaqu,  nom_empaqu,   cod_minemp,
                                           ind_activa,  usr_creaci,   fec_creaci
                                          )
                                  VALUES  
                                      ('".$cod_empaqu."', '".$_REQUEST['nom_empaqu']."', '".$_REQUEST['cod_minemp']."', 
                                      '".$_REQUEST['ind_activa']."',  NOW(), '".$_SESSION['datos_usuario']['cod_usuari']."')" ;
          }
          else if($_REQUEST['accion'] == "CambioEstado")
          {
            $sql ="UPDATE " . BASE_DATOS . ".tab_genera_empaqu 
                              SET
                              ind_activa = '".$_REQUEST['ind_activa']."',
                              usr_modifi = NOW(),
                              fec_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."'
                              WHERE cod_empaqu = '".$_REQUEST['cod_empaqu']."' 
                              " ;
          }
          else
          {
            $sql ="UPDATE " . BASE_DATOS . ".tab_genera_empaqu 
                              SET nom_empaqu = '".$_REQUEST['nom_empaqu']."',
                              cod_minemp = '".$_REQUEST['cod_minemp']."',
                              ind_activa = '".$_REQUEST['ind_activa']."',
                              usr_modifi = NOW(),
                              fec_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."'
                              WHERE cod_empaqu = '".$_REQUEST['cod_empaqu']."' 
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

    /*! \fn: getUnidadMedia
     *  \brief: obtiene la iformacion del tipo de servicio
     *  \author: Edward Serrano
     *  \date:  12/07/2017
     *  \date modified: dia/mes/año
     */
    private function getUnidadMedia($cod_empaqu)
    {
      try
      {
          $sql = "SELECT  cod_empaqu, nom_empaqu, cod_minemp,  
                          ind_activa 
                      FROM ".BASE_DATOS.".tab_genera_empaqu
                      WHERE cod_empaqu ='{$cod_empaqu}';";
          $result = new Consulta( $sql, self::$cConexion);
          $result = $result -> ret_matrix('a');
          return $result;
      } catch (Exception $e) {
        echo "error getUnidadMedia :".$e;
      }
    }
}
if($_REQUEST["Ajax"] === 'on' )
{
    $_unidadMedida = new unidadMedida();
}
else
{
    $_unidadMedida = new unidadMedida( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );
}

?>