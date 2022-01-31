<?php
/*! \file: ins_parame_noveda.php
 *  \brief: Insertar, Editar y  desactivar parametros de novedades
 *  \author: Edward Fabian Serrano
 *  \author: edward.serrano@intrared.net
 *  \version: 1.0
 *  \date: 04/04/2017
 *  \bug: 
 *  \warning: 
 */

/*! \class: parameNoveda
 *  \brief: Lista configuracion parametros de novedades
 */
class parameNoveda
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
    

     switch ($_REQUEST['opcion']) {
        case 'getFormSoltie':
              self::getFormSoltie();     
          break;

        case 'getFormIndi':
              self::getFormIndi();
          break;

        case 'almacenarNovedad':
              self::almacenarNovedad();
          break;

        case 'inactivarNovedad':
              self::inactivarNovedad();
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

			echo self::GridStyle();
			$mTabs = array(
										'ind_tiempo' => 'SOLICITA TIEMPO',
										'ind_manala' => 'MANTINENE ALARMA',
										'ind_otrpar' => 'OTRAS PARAMETRIZACIONES'
										//'ind_gpsxxx' => 'GPS'
										 );
			$mHtml = new FormLib(2);
       # incluye JS
      $mHtml->SetJs("min");
      $mHtml->SetJs("config");
      $mHtml->SetJs("fecha");
      $mHtml->SetJs("jquery17");
      $mHtml->SetJs("jquery");
      $mHtml->SetJs("functions");
      $mHtml->SetJs("ins_parame_noveda");
      $mHtml->SetJs("new_ajax"); 
      $mHtml->SetJs("dinamic_list");
      $mHtml->SetCss("dinamic_list");
      $mHtml->SetJs("validator");
      $mHtml->SetJs("time");
      $mHtml->SetCssJq("validator");
      $mHtml->SetCssJq("jquery");

      #variables ocultas
      $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
      $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
      $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
      $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));
      $mHtml->Hidden(array( "name" => "accion", "id" => "accionID", 'value'=>($_REQUEST['accion']==""?"1":$_REQUEST['accion'])));

      $mHtml->Table("tr");
        # Construye accordion
        $mHtml->Row("td");
          $mHtml->OpenDiv("id:contentID; class:contentAccordion");
          # Accordion1
            $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
              $mHtml->SetBody("<h1 style='padding:6px'><b>Novedades</b></h1>");
              $mHtml->OpenDiv("id:sec1");
                $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
           				$mHtml->Table("tr");
                    $mHtml->Label( strtoupper("Parametrizacion Novedades"), array("colspan"=>"12", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
                    $mHtml->Row();
                      $mHtml->Label( strtoupper("Filtro de perfiles:"), array("colspan"=>"7", "align"=>"right", "width"=>"25%", "class"=>"cellInfo2") );
                      	$mHtml->Select2 (self::getPerfiles(),  array("name" => "perfiles", "width" => "25%") );
                    $mHtml->CloseRow();
                  $mHtml->CloseTable("tr");
                $mHtml->CloseDiv();
                $mHtml->OpenDiv("id:tabs");
  							#tabs
  							$mHtml->SetBody("<ul>");
  							foreach ($mTabs as $kTab => $vTab) {
  								$mHtml->SetBody("<li id='".$kTab."' class='ind_tab'><a href='#resultDiv' onclick='openTabs(\"$kTab\")'>".$vTab."</a></li>");
  							}
  							$mHtml->SetBody("</ul>");
                $mHtml->OpenDiv("id:resultDiv");
                    
                $mHtml->CloseDiv();
  							#cierra tabs
  							$mHtml->CloseDiv();
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          # Fin accordion1    
          $mHtml->CloseDiv();
        $mHtml->CloseRow("td");
        # Cierra Body
        $mHtml->CloseBody();
      $mHtml->CloseTable("tr");
      # Muestra Html
      $mHtml->SetBody('<script>
                      $(function() {
                        $("#tabs").tabs();
                      } );
                    </script>');
      echo $mHtml->MakeHtml();
		}catch(Exception $e)
		{
			echo "Error funcion listar", $e->getMessage(), "\n";
		}
	}

	/*! \fn: getPerfiles
	* \brief: perfiles
	* \author: Edward Serrano
	* \date: 04/04-2017/
	* \date modified: dia/mes/año
	* \param: paramatro
	* \return valor que retorna
	*/
	private function getPerfiles()
	{
			try
			{
				$query = "SELECT a.cod_perfil, a.nom_perfil
				  FROM " . BASE_DATOS . ".tab_genera_perfil  a
				  WHERE a.ind_estado = 1 ORDER BY 2";

    		$consulta = new Consulta($query, self::$cConexion);
    		return $matriz = $consulta -> ret_matriz( "a" );
			}
      catch(Exception $e)
			{
				echo "Error funcion listar", $e->getMessage(), "\n";
			}
	}

  /*! \fn: getFormSoltie
  * \brief: Formulario de tabs
  * \author: Edward Serrano
  * \date: 05/04/2017
  * \date modified: dia/mes/año
  * \param: paramatro
  * \return valor que retorna
  */
  function getFormSoltie()
  {
    try
    {
      $mHtml = new FormLib(2);
      $mHtml->OpenDiv("id:sec3; class:contentAccordionForm");
        $mHtml->Table("tr");
          $mHtml->Label( strtoupper("Parametrizacion Especial a Aplicar"), array("align"=>"center", "width"=>"25%", "class"=>"CellHead", "colspan"=>"6", "end"=>"true") );
          $mHtml->CloseRow();
          $mHtml->Row();
            $mHtml->CheckBox(array("name"=>"ind_apsees", "id"=>"ind_apsees", "class"=>"cellInfo2", "align"=>"right", "checked"=>null));
            $mHtml->Label( strtoupper("Aplica Para Seguimiento Especial"), array("align"=>"right", "class"=>"cellInfo2" ) );
            $mHtml->CheckBox(array("name"=>"ind_tisees", "id"=>"ind_tisees", "class"=>"cellInfo2", "align"=>"right", "checked"=>null, "onclick"=>"activarMinuto(1)"));
            $mHtml->Label( strtoupper("Tiempo de Seguimiento Especial Automatico"), array("align"=>"right", "class"=>"cellInfo2" ) );
            $mHtml->Input( array("name"=>"num_minuto", "id"=>"num_minuto", "align"=>"right", "class"=>"cellInfo2", "type"=>"numeric", "maxlength"=>"4"));
            $mHtml->Label( strtoupper("Tiempo en Minutos"), array("align"=>"left", "class"=>"cellInfo2" ) );
          $mHtml->CloseRow();
          $mHtml->Row();
            $mHtml->Label( strtoupper("Perfiles"), array("align"=>"center", "width"=>"25%", "class"=>"CellHead", "colspan"=>"12", "end"=>"true") );
          $mHtml->CloseRow();
          $mHtml->Row();
            $mHtml->Label( "SELECCIONAR TODAS:", array("align"=>"left", "class"=>"cellInfo2" ) );
            $mHtml->CheckBox(array("name"=>"SeleccionM", "id"=>"SeleccionM", "class"=>"cellInfo2", "align"=>"left", "onchange"=>"checkAll()"));
        $mHtml->CloseTable("tr");
        $mHtml->OpenDiv("id:secNovedadesP");
          $mHtml->Table("tr");
            $saltoln=0;$n=0;
            foreach (self::getNovedPerfil() as $key => $value) 
            {
              $novpar = self::getNovedadParame($value["cod_noveda"], $_REQUEST["perfil"]);
              if($_REQUEST["tab"] != "ind_otrpar")
              {
                if($value[$_REQUEST["tab"]]==1)
                {
                  $saltoln++;
                  $mHtml->CheckBox(array("name"=>$value["cod_noveda"], "id"=>$value["cod_noveda"], "class"=>"cellInfo2", "align"=>"right", "value"=>$value["cod_noveda"]));
                  $mHtml->Label($value["cod_noveda"]." - ".$value["nom_noveda"] , array("align"=>"left", "class"=>"cellInfo2" ) );
                  
                  $mHtml->Label(($novpar[0]?"Editar":" ") , array("align"=>"right", "class"=>"cellInfo2", "id" => "labelEditar", "onclick" => ($novpar[0]?"opciones(1, '". $value["cod_noveda"] ."')":"") ) );
                  $mHtml->Label(($novpar[0]?"Eliminar":" "), array("align"=>"right", "class"=>"cellInfo2", "id" => "labelEliminar","onclick" =>  ($novpar[0]?"opciones(2, '". $value["cod_noveda"] ."')":"") ) );
                  
                  if($saltoln==2){
                    $mHtml->CloseRow();
                    $mHtml->Row();
                    $saltoln=0;
                  }
                }
              }
              else
              {
                if($value["ind_manala"]==0 && $value["ind_tiempo"]==0)
                {
                  $saltoln++;
                  $mHtml->CheckBox(array("name"=>$value["cod_noveda"], "id"=>$value["cod_noveda"], "class"=>"cellInfo2", "align"=>"right", "value"=>$value["cod_noveda"]));
                  $mHtml->Label($value["cod_noveda"]." - ".$value["nom_noveda"] , array("align"=>"left", "class"=>"cellInfo2" ) );
                  $mHtml->Label(($novpar[0]?"Editar":" ")  , array("align"=>"right", "class"=>"cellInfo2", "id" => "labelEditar","onclick" => ($novpar[0]?"opciones(1, '". $value["cod_noveda"] ."')":"") ) );
                  $mHtml->Label(($novpar[0]?"Eliminar":" ") , array("align"=>"right", "class"=>"cellInfo2", "id" => "labelEliminar","onclick" => ($novpar[0]?"opciones(2, '". $value["cod_noveda"] ."')":"") ) );
                  if($saltoln==2){
                    $mHtml->CloseRow();
                    $mHtml->Row();
                    $saltoln=0;
                  }
                }
              }
            }
          $mHtml->CloseTable("tr");
        $mHtml->CloseDiv();
        $mHtml->OpenDiv("id:secBotoneraP");
          $mHtml->Table("tr");
            $mHtml->Button( array("value"=>"GUARDAR", "id"=>"btnGuardar","name"=>"btnGuardar", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"right", "onclick"=>"almacenarNovedad(1)") );
            $mHtml->Button( array("value"=>"CANCELAR", "id"=>"btnCancelar","name"=>"btnCancelar", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"right", "onclick"=> "cerrarTab()") );
            $mHtml->CloseRow();
          $mHtml->CloseTable('tr');
        $mHtml->CloseDiv();
      $mHtml->CloseDiv();
      echo $mHtml->MakeHtml();
    }
    catch(Exception $e)
    {
      echo "Error funcion getFormSoltie", $e->getMessage(),"\n";
    }
  }

  /*! \fn: getNovedPerfil
  * \brief: retorna las novedades existentes
  * \author: Edward Serrano
  * \date: 06/04/2017
  * \date modified: dia/mes/año
  * \param: paramatro
  * \return valor que retorna
  */

  function getNovedPerfil()
  {
    try
    {
      $query ="SELECT a.cod_perfil, a.cod_noveda, UPPER(b.nom_noveda) AS nom_noveda,
                      b.ind_manala, b.ind_tiempo 
                      FROM ". BASE_DATOS .".tab_perfil_noveda a
                      INNER JOIN ". BASE_DATOS .".tab_genera_noveda b
                        ON a.cod_noveda = b.cod_noveda
                      WHERE a.cod_noveda NOT IN (9996, 9995) AND cod_perfil=".$_REQUEST['perfil'];
      $consulta = new Consulta($query, self::$cConexion);
      return $consulta -> ret_matriz("a");
    }
    catch(Exception $e)
    {
      echo "Error funcion getNovedPerfil", $e->getMessage(), "\n";
    }
  }

  /*! \fn: getInfoNovedad
  * \brief: descripcion
  * \author: Edward Serrano
  * \date: //
  * \date modified: dia/mes/año
  * \param: paramatro
  * \return valor que retorna
  */
  function getInfoNovedad()
  {
    try
    {
      $query = "SELECT UPPER(a.nom_noveda) AS nom_noveda, a.cod_etapax AS cod_etapax, a.obs_preted AS obs_preted,    
                       a.ind_alarma AS ind_alarma, a.ind_tiempo AS ind_tiempo, a.nov_especi AS nov_especi,   
                       a.ind_manala AS ind_manala, a.ind_fuepla AS ind_fuepla, a.ind_insveh AS ind_insveh,      
                       a.ind_agenci AS ind_agenci, a.cod_operad AS cod_operad, a.cod_homolo AS cod_homolo,   
                       a.ind_visibl AS ind_visibl, a.ind_limpio AS ind_limpio
                       FROM ". BASE_DATOS .".tab_genera_noveda a 
                       WHERE a.cod_noveda=".$_REQUEST["cod_noveda"];
      $consulta = new Consulta($query, self::$cConexion);
      return $consulta->ret_matriz("a");
    }
    catch(Exception $e)
    {
      echo "Error funcion getInfoNovedad", $e->getMessage(), "\n";
    }
  }
  
  /*! \fn: getFormIndi
  * \brief: devuelve formulario individual para parametirzacion
  * \author: Edward Serrano
  * \date: 07/04/2017
  * \date modified: dia/mes/año
  * \param: 
  * \return
  */
  function getFormIndi()
  {
    try
    {
      $mDataNoveda = self::getInfoNovedad()[0];
      $mEtapas = self::getEtapa();
      $novpar = self::getNovedadParame($_REQUEST['cod_noveda'], $_REQUEST['cod_perfil'])[0];
      $mCompor = array(
                       'nov_especi' => 'Novedad Especial',
                       'ind_manala' => 'Mantiene Alarma',
                       'ind_fuepla' => 'Fuera de Plataforma',
                       'ind_tiempo' => 'Solicita Tiempos',
                       'ind_ealxxx' => 'Visible Esferas',
                       'ind_visibl' => 'Visibilidad (S/N)',
                       'ind_notsup' => 'Notifica Supervisor',
                       'ind_limpio' => 'Limpio'
                        );
      
      $mHtml = new FormLib(2);
      $mHtml->Hidden(array( "name" => "cod_novedaIn", "id" => "cod_novedaIn", 'value'=>$_REQUEST["cod_noveda"]));
      $mHtml->OpenDiv("id:secNI; class:contentAccordionForm");
        $mHtml->Table("tr");  
          $mHtml->Label( $mDataNoveda["nom_noveda"], array("colspan"=>"4", "align"=>"center", "width"=>"25%", "class"=>"CellHead") );
          $mHtml->CloseRow();
          $mHtml->Row();
            $mHtml->Label( "ETAPAS DE SEGUIMIENTO", array( "colspan"=>"4", "align"=>"left", "width"=>"25%", "class"=>"CellHead", "end" => "true") );
            $saltoLn = 0;
            foreach ($mEtapas as $NomEtapa => $valorEtapa) 
            {
              $saltoLn++;
              $mHtml->Radio(array("colspan"=>"1", "value"=>$valorEtapa[0], "name" => "cod_etapax", "id" => "cod_etapax", "class"=>"cellInfo2", "width" => "8%", "readonly"=>"readonly", "disabled"=>"disabled", "checked"=>($mDataNoveda["cod_etapax"] == $valorEtapa[0]?"checked":null)));
              $mHtml->Label( $valorEtapa[1], array("colspan"=>"1", "align"=>"left", "width"=>"8%", "class"=>"cellInfo2") );
              if($saltoLn>=2)
              {
                $mHtml->CloseRow();
                $mHtml->Row();
                $saltoLn=0;
              }
            }
          $mHtml->CloseRow();
        $mHtml->CloseTable("tr");
        $mHtml->Table("tr");
          $mHtml->Label( "COMPORTAMIENTO", array( "align"=>"left", "width"=>"25%", "class"=>"CellHead",  "colspan" =>"8","end" => "true") );
          $saltoLn = 0;
          foreach ($mCompor as $idCompor => $valCompor) 
          {
            $saltoLn++;
            $mHtml->CheckBox(array("name" => $idCompor, "id"=>$idCompor, "width" => "25%", "colspan" => "1", "class"=>"cellInfo2", "align"=>"right", "value"=>"1", "readonly"=>"readonly", "disabled"=>"disabled", "checked"=>($mDataNoveda[$idCompor] == 1 || $mDataNoveda[$idCompor] == "S"?"checked":null) ));
            $mHtml->Label( strtoupper($valCompor), array("align"=>"left", "width"=>"25%", "class"=>"cellInfo2", "colspan" =>"3" ) );
            if($saltoLn>=2)
            {
              $mHtml->CloseRow();
              $mHtml->Row();
              $saltoLn=0;
            }
          }
        $mHtml->CloseRow();
        $mHtml->CloseTable("tr");
          $mHtml->Table("tr");
            $mHtml->Label( strtoupper("Parametrizacion Especial a Aplicar"), array("align"=>"center", "width"=>"25%", "class"=>"CellHead", "colspan"=>"6", "end"=>"true") );
            $mHtml->CloseRow();
            $mHtml->Row();
              $mHtml->CheckBox(array("name"=>"ind_apseesIn", "id"=>"ind_apseesIn", "class"=>"cellInfo2", "align"=>"right", "checked"=>($novpar["ind_apsees"]==1?"checked":null)));
              $mHtml->Label( strtoupper("Aplica Para Seguimiento Especial"), array("align"=>"right", "class"=>"cellInfo2" ) );
              $mHtml->CheckBox(array("name"=>"ind_tiseesIn", "id"=>"ind_tiseesIn", "class"=>"cellInfo2", "align"=>"right", "checked"=>($novpar["ind_tisees"]==1?"checked":null), "onclick"=>"activarMinuto(2)"));
              $mHtml->Label( strtoupper("Tiempo de Seguimiento Especial Automatico"), array("align"=>"right", "class"=>"cellInfo2" ) );
              $mHtml->Input( array("name"=>"num_minutoIn", "id"=>"num_minutoIn", "class"=>"cellInfo2", "align"=>"right", "type"=>"numeric", "maxlength"=>"4", "value"=>($novpar["num_minuto"]!=0?$novpar["num_minuto"]:"") ));
              $mHtml->Label( strtoupper("Tiempo en Minutos"), array("align"=>"left", "class"=>"cellInfo2" ) );
            $mHtml->CloseRow();
        $mHtml->CloseTable("tr");
        $mHtml->Table("tr");
          $mHtml->Button( array("value"=>"GUARDAR", "id"=>"btnGuardar","name"=>"btnGuardar", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"right", "onclick"=>"almacenarNovedad(2)") );
          $mHtml->Button( array("value"=>"CANCELAR", "id"=>"btnCerrarPop","name"=>"btnCerrarPop", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"right", "onclick"=> "cerrarPopup()") );
          $mHtml->CloseRow();
        $mHtml->CloseTable('tr');
      $mHtml->CloseDiv();
      echo $mHtml->MakeHtml();
    }
    catch(Exception $e)
    {
      echo "Error funcion getFormIndi", $e->getMessage(), "\n";
    }
  }

  /*! \fn: getEtapa
   *  \brief: Trae las Etapas de un despacho
   *  \author: Ing. Fabian Salinas
   *  \date: 26/06/2015
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: 
   */
  private function getEtapa()
  {
    try
    {
      $mSql = "SELECT a.cod_queryx 
                 FROM ". BD_STANDA .".tab_genera_filtro a 
                WHERE a.nom_filtro = 'Etapa' ";
      $mConsult = new Consulta($mSql, self::$cConexion);
      $mSql = $mConsult -> ret_arreglo();

      $mConsult = new Consulta($mSql[0], self::$cConexion);
      return $mResult = $mConsult -> ret_matrix('i');
    }
    catch(Exception $e)
    {
      echo "Error Funcion getEtapa", $e->getMessage(),"\n";
    }
  }

  /*! \fn: almacenarNovedad
  * \brief: almacena las novedades enviadas
  * \author: Edward Serrano
  * \date: //
  * \date modified: dia/mes/año
  * \param: paramatro
  * \return valor que retorna
  */
  function almacenarNovedad()
  {
    try
    {
      #elimino el ultimo caracter de la cadena que es una coma
      $valCadena =substr($_REQUEST["cod_noveda"], -1);
      if($valCadena==",")
      {
        $aCod_nodeda = explode(",", substr($_REQUEST["cod_noveda"], 0, -1));
      }
      else
      {
        $aCod_nodeda[] = $_REQUEST["cod_noveda"];
      }
      #delimoto por comas la cadena de novedades
      $mNuevasNov = array();
      $mActualNov = array();

      # inicia transaccion
      $mConsultUpd = new Consulta("SELECT 1 ;", self::$cConexion, "BR"); // Inicia 
      #si no tiene datos se insertan todas las novedades como nuevas
      foreach ($aCod_nodeda as $keyNov => $valueNov) 
      {
        #consulto la novedades registradas en la tabla tab_genera_novpar
        $novpar = self::getNovedadParame($valueNov, $_REQUEST["cod_perfil"]);
        if(sizeof($novpar)>0)
        {
          $mQueryud = " UPDATE ". BASE_DATOS .".tab_genera_novpar SET ind_apsees='".$_REQUEST["ind_apsees"]."', ind_tisees='".$_REQUEST["ind_tisees"]."', num_minuto = '".$_REQUEST["num_minuto"]."', usr_modifi='".self::$cUsuario["cod_usuari"]."', fec_modifi=NOW() WHERE cod_perfil=".$_REQUEST["cod_perfil"]." AND cod_noveda=".$valueNov;
          $mConsultUpd = new Consulta($mQueryud, self::$cConexion, "BR");
        }
        else
        {
          $mNuevasNov[] = "({$_REQUEST['cod_perfil']}, {$valueNov}, {$_REQUEST['ind_apsees']}, {$_REQUEST['ind_tisees']}, {$_REQUEST['num_minuto']}, 1, NOW(), '".self::$cUsuario["cod_usuari"]."' )";
          
        }
      }
      if(sizeof($mNuevasNov)>0)
      {
        $mQueryin = "INSERT INTO ". BASE_DATOS .".tab_genera_novpar ( cod_perfil, cod_noveda, ind_apsees, ind_tisees, num_minuto, ind_estado, fec_creaci, usr_creaci) VALUES ".join(",",$mNuevasNov);
        $mConsultIns = new Consulta($mQueryin, self::$cConexion, "BR" ); // Mantener        
        
      }
      
      $mConsultUpd = new Consulta("SELECT 1;", self::$cConexion, "RC"); // Si no se  va nada por la excepcion hace commit de todo
      if($mConsultUpd)
      {
        echo "ok";
      }
    }
    catch(Exception $e)
    {
      echo "Error funcion almacenarNovedad: ", $e->getMessage(),"\n";
       
    }
  }

  /*! \fn: getNovedadParame
   *  \brief: Trae las novedades almacenadas en la tabla tab_genera_novpar
   *  \author: Edward Serrano
   *  \date: 07/04/2017
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: 
   */
  private function getNovedadParame($cod_noveda, $cod_perfil = NULL)
  {
    try
    {
      $query = "SELECT a.cod_perfil, a.cod_noveda, a.ind_apsees,
                       a.ind_tisees, a.num_minuto, a.ind_estado
                       FROM ". BASE_DATOS .".tab_genera_novpar a 
                       WHERE a.cod_noveda = ".$cod_noveda.($cod_perfil!=""?" AND a.cod_perfil = ".$cod_perfil:"");
      $consulta = new Consulta($query, self::$cConexion);
      return $consulta->ret_matriz("a");
    }
    catch(Exception $e)
    {
      echo "Error Funcion getEtapa", $e->getMessage(),"\n";
    }
  }

   /*! \fn: inactivarNovedad
   *  \brief: inactiva los parmetros de novedades
   *  \author: Edward Serrano
   *  \date: 07/04/2017
   *  \date modified: dia/mes/año
   *  \param: 
   *  \return: 
   */
  function inactivarNovedad()
  {
    try
    {
      if($_REQUEST["cod_noveda"]!="" && $_REQUEST["cod_perfil"]!="")
      {

        //$mQuery = "UPDATE ". BASE_DATOS .".tab_genera_novpar SET ind_estado=0,usr_modifi='".self::$cUsuario["cod_usuari"]."',fec_modifi=NOW() WHERE cod_perfil=".$_REQUEST["cod_perfil"]." AND cod_noveda=".$_REQUEST["cod_noveda"];
        $mQuery = "DELETE FROM ". BASE_DATOS .".tab_genera_novpar WHERE cod_perfil=".$_REQUEST["cod_perfil"]." AND cod_noveda=".$_REQUEST["cod_noveda"];
        $mConsult = new Consulta($mQuery, self::$cConexion, "RC");
        if($mConsult)
        {
          echo "ok";
        }
      }
    }
    catch(Exception $e)
    {
      echo "Error Funcion inactivarNovedad", $e->getMessage(),"\n";
    }
  }

	/*! \fn: GridStyle 
  * \brief: estilos actualizados
  * \author: Edward Serrano
  * \date: 31/03/2017
  * \date modified: dia/mes/año
  * \param: paramatro
  * \return valor que retorna
  */
  private function GridStyle()
  {
    	try{
        echo "<style>
                .cellth-ltb{
                     background: #E7E7E7;
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .cellth-lb{
                     background: #E7E7E7;
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                }
                .cellth-b{
                     background: #E7E7E7;
                     border-bottom: 1px solid #999999; 
                }
                .cellth-tb{
                     background: #E7E7E7;
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-ltb{
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-tb{
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-lb{
                     border-bottom: 1px solid #999999; 
                     border-left: 1px solid #999999;
                }
                .celltd-l{
                     border-left: 1px solid #999999;
                }
                .fontbold{
                    font-weight: bold;
                }
                .divGrilla{
                    margin: 0;
                    padding: 0;
                    border: none;
                    border-top: 1px solid #999999;
                    border-bottom: 1px solid #999999;
                }

                .CellHead {
                    background-color: #35650f;
                    color: #ffffff;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding: 4px;
                }
                .cellInfo1 {
                    background-color: #ebf8e2;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding: 2px;
                    height: 10px;
                }
                /*.campo_texto {
                    background-color: #ffffff;
                    border: 1px solid #bababa;
                    color: #000000;
                    font-family: Times New Roman;
                    font-size: 11px;
                    padding-left: 5px;
                }*/
                .crmButton {
                  width:25%;
                  height: 20px;
                }
                
              .error{
                  background-color: #45930b;
                  border-radius: 4px 4px 4px 4px;
                  color: white;
                  font-weight: bold;
                  margin-left: 6px;
                  margin-top: 3px;
                  padding: 3px 6px;
                  position: absolute;
              }
              .error:before{
                  border-color: transparent #45930b transparent transparent;
                  border-style: solid;
                  border-width: 3px 4px;
                  content: '';
                  display: block;
                  height: 0;
                  left: -16px;
                  position: absolute;
                  top: 4px;
                  width: 0;
              }
              .campo_texto, .campo_texto_on{
                border: 1px solid #DBE1EB;
                font-size: 10px;
                font-family: Arial, Verdana;
                padding-left: 5px;
                padding-right: 5px;
                padding-top: 5px;
                padding-bottom: 5px;
                border-radius: 4px;
                -moz-border-radius: 4px;
                -webkit-border-radius: 4px;
                -o-border-radius: 4px;
                background: #FFFFFF;
                background: linear-gradient(left, #FFFFFF, #F7F9FA);
                background: -moz-linear-gradient(left, #FFFFFF, #F7F9FA);
                background: -webkit-linear-gradient(left, #FFFFFF, #F7F9FA);
                background: -o-linear-gradient(left, #FFFFFF, #F7F9FA);
                color: #2E3133;
              }
              .CellInfohref{
                cursor:pointer;
                background-color: #ebf8e2;
                font-family: Times New Roman;
                font-size: 11px;
                padding: 2px;
                height: 10px;
              }
              #labelEditar, #labelEliminar:hover{
                cursor: hand
              }
              </style>";
    	
      }
      catch(Exception $e)
			{
				echo "Error funcion listar", $e->getMessage(), "\n";
			}
    }
}
if($_REQUEST["Ajax"] === 'on' )
{
  $_parameNoveda = new parameNoveda();
}
else
{
  $_parameNoveda = new parameNoveda( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );
}

?>