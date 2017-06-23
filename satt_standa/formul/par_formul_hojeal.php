<?php
/*! \file: par_formul_hojeal.php
 *  \brief: Activacion y edicion de hojas de vida Eal
 *  \author: Edward Fabian Serrano
 *  \author: edward.serrano@intrared.net
 *  \version: 1.0
 *  \date: 02/06/2017
 *  \bug: 
 *  \warning: 
 */

/*! \class: hojaVidaEal
 *  \brief: Lista configuracion hojas de vida EAL
 */
class hojaVidaEal
{
	private static  $cConexion,
					$cCodAplica,
					$cUsuario,
                    $cTransp;
					
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
            case 'listaHojasVidaEAL':
            	self::listaHojasVidaEAL();
            	break;
            case 'FormNuevaHvEAL':
            	self::FormNuevaHvEAL();
            	break;
            case 'getOptionFormComp':
            	self::getOptionFormComp();
            	break;
            case 'getDrawFormul':
            	self::getDrawFormul();
            	break;
            case 'insertar':
            	self::insertar();
            	break;
            case 'tablaDatosBasicosEAL':
            	self::tablaDatosBasicosEAL();
            	break;
            case 'inactivarEal':
            	self::inactivarEal();
            	break;
            case 'download':
            	self::download();
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
            include_once('../'.DIR_APLICA_CENTRAL.'/inform/class_despac_trans3.php');
            $mHtml = new FormLib(2);

            self::$cTransp = new Despac(self::$cConexion, self::$cUsuario, self::$cCodAplica);

            $transp = self::$cTransp->getTransp();
            $total = count($transp);
            if( $total == 1 ){
              $mCodTransp = $transp[0][0];
              $mNomTransp = $transp[0][1];
            }
            
             # incluye JS
            $mHtml->SetJs("min");
            $mHtml->SetJs("config");
            $mHtml->SetJs("fecha");
            $mHtml->SetJs("jquery17");
            $mHtml->SetJs("jquery");
            $mHtml->SetJs("functions");
            $mHtml->SetJs("par_formul_hojeal");
            $mHtml->SetJs("new_ajax"); 
            $mHtml->SetJs("dinamic_list");
            $mHtml->SetCss("dinamic_list");
            $mHtml->SetJs("validator");
            $mHtml->SetJs("ol");
            $mHtml->SetBody("<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.qrcode-0.12.0.js\"></script>\n");
            $mHtml->SetBody("<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.qrcode-0.12.0.min.js\"></script>\n");
            //$mHtml->SetJs("jquery.qrcode-0.12.0");
            //$mHtml->SetJs("jquery.qrcode-0.12.0.min");
            $mHtml->SetCss("ol");
            $mHtml->SetCssJq("validator"); 
            $mHtml->CloseTable("tr");
            # incluye Css
            $mHtml->SetCssJq("jquery");
            $mHtml->Body(array("menubar" => "no"));
	//echo '<link rel="stylesheet" href="https://openlayers.org/en/v4.2.0/css/ol.css" type="text/css">';
    //echo '<script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>';
    //echo '<script src="https://openlayers.org/en/v4.2.0/build/ol.js"></script>';
            # Abre Form
            $mHtml->Form(array("action" => "index.php", "method" => "post", "name" => "form_search", "header" => "EAL", "enctype" => "multipart/form-data"));

            #variables ocultas
          
            $mHtml->Hidden(array( "name" => "cod_tercer", "id" => "cod_tercerID", 'value'=>$mCodTransp));
            $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
            $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
            $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
            $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));
            $mHtml->Hidden(array( "name" => "cod_transp", "id" => "cod_transpID", 'value'=>$mCodTransp));
            $mHtml->Hidden(array( "name" => "conductor", "id" => "conductor", 'value'=>"")); 
            $mHtml->SetBody("<script async defer
                              src='https://maps.googleapis.com/maps/api/js?key=AIzaSyAbxIDRJVmZtpIubA75-DIpf2fbjx8KEck'>
                            </script>");

            # Construye accordion
            $mHtml->Row("td");
              $mHtml->OpenDiv("id:contentID; class:contentAccordion");
                # Accordion1
                /*$mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
                  $mHtml->SetBody("<h1 style='padding:6px'><b>Hojas de vida EAL</b></h1>");
                  $mHtml->OpenDiv("id:sec1;");
                    $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                      $mHtml->Table("tr");
                          $mHtml->Label("Transportadora:", "width:35%; :1;");
                          $mHtml->Input(array("name" => "trasp[nom_transp]", "id" => "nom_transpID", "value" => $mNomTransp, "width" => "35%"));
                          $mHtml->SetBody("<td><div id='boton'></div></td>");  
                      $mHtml->CloseTable("tr");
                    $mHtml->CloseDiv();
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();*/
                # Fin accordion1    
                # Accordion2
                $mHtml->OpenDiv("id:datos; class:accordion");
                  $mHtml->SetBody("<h1 style='padding:6px'><b>Lista de hojas de vida EAL</b></h1>");
                  $mHtml->OpenDiv("id:sec2");
                    $mHtml->OpenDiv("id:form3; class:contentAccordionForm");
                      
                    $mHtml->CloseDiv();
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();
              # Fin accordion2
              $mHtml->CloseDiv();
            $mHtml->CloseRow("td");
              # Cierra formulario
            $mHtml->CloseForm();
            # Cierra Body
            $mHtml->CloseBody();
            $mHtml->SetBody('<script> $("div[id=datos]").hide() </script>');

            # Muestra Html
            echo $mHtml->MakeHtml();
		}catch(Exception $e)
		{
			echo "Error funcion listar", $e->getMessage(), "\n";
		}
	}
    
    /*! \fn: listaHojasVidaEAL
     *  \brief: Lista los usuarios regitrados para movil
     *  \author: Edward Serrano
     *  \date: 26/05/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    public function listaHojasVidaEAL()
    {
    	try
    	{
	        $mQuery = "SELECT 	a.nom_contro, CONCAT(b.nom_funcio,' ',b.nom_apell1), b.num_telmov, 
	        					a.cod_contro, b.ind_estado  
							FROM ".BASE_DATOS.".tab_genera_contro a
							INNER JOIN ".BASE_DATOS.".tab_respon_funcio b ON b.cod_contro = a.cod_contro
							WHERE b.ind_repleg = 1
							GROUP BY (a.nom_contro)
							";  
	        
	        //----------------------------------------------------------------------------------------------------------------------------
	        $cList = new DinamicList( self::$cConexion, $mQuery , 1 );
	        $cList -> SetCreate("Agregar Hojas de vida EAL", "onclick:newHojaEAL('new')");
	        $cList -> SetHeader( "Esfera de asistencia logistica", "field:a.nom_contro" );
	        $cList -> SetHeader( "Nombre del representante", "field:a.cod_ciudad" );
	        $cList -> SetHeader( "Celular", "field:a.nom_encarg" );
	        $cList -> SetOption(utf8_decode("Opciones"),"field:cod_option; width:1%; onclikDisable:inactivarEal( 0, this ); onclikEnable:inactivarEal( 1, this ); onclikEdit:newHojaEAL('edit', this);" );
	        $cList -> SetHidden("cod_contro", "cod_contro" ); 
	        $cList -> SetClose( "no" );
	        $cList -> Display( self::$cConexion );
	        echo $cList -> GetHtml();
	        $_SESSION["DINAMIC_LIST"]   = $cList;
    	}catch(Exception $e)
		{
			echo "Error funcion listaHojasVidaEAL", $e->getMessage(), "\n";
		}
    }

    /*! \fn: FormNewHvEAL
     *  \brief: Formulario de nueva hoja de vida
     *  \author: Edward Serrano
     *  \date: 05/06/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    public function FormNuevaHvEAL()
    {
    	try
    	{
    		if($_REQUEST['accion'] == "edit")
    		{
    			$mInfoRL = self::getInfResponFuncion($_REQUEST['cod_contro'], "1");
    			$mDataInf = self::getInfResponFuncion($_REQUEST['cod_contro'], "0");
    			$mInfoTab = self::getInfTabs($_REQUEST['cod_contro']);
    			//Recorro los tab registrados
    			if(count($mInfoTab)>0)
    			{
    				$mactiveTabs = [];
    				foreach ($mInfoTab as $key => $value) 
    				{
    					$mactiveTabs[] = $value['cod_formul'];
    				}
    			}
    		}
	    	$mHtml = new FormLib(2);
	    	$mHtml->OpenDiv("id:PuntoEal; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>Crear Hoja de vida</center></h3>");
				$mHtml->OpenDiv("id:secNewNotifi");
					$mHtml->Table("tr");
						#Cuerpo de la notificacion
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Label( "*Seleccione EAL a crear:",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"1") );
							$mHtml->Select2 (array_merge(array(array('0' =>  '-','1' =>  '-')),self::getControlEal()),  array("name" => "cod_contro", "width" => "25%","colspan"=>"1", "obl"=>"obl", "key"=>($_REQUEST['cod_contro']?$_REQUEST['cod_contro']:""), "disabled" => ($_REQUEST['accion'] == "edit"?"disabled":""), "onchange"=>"cargarTablaEAL()") );
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			#informacion Representante Legal
			$mHtml->OpenDiv("id:RepreLegal; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>Representante Legal</center></h3>");
				$mHtml->SetBody("<div id='Sec1RepreLegal' class='contentAccordionForm' style='width:95%;'>");
					$mHtml->SetBody("<div id='Sec2RepreLegal' class='contentAccordionForm' style='width:60%;float: left;height:270px'>");
						#Espacio para el formulario
						$mHtml->SetBody("<div>");
							$mHtml->SetBody("<div>");
								$mHtml->Table("tr");
									$mHtml->Row();
										$mHtml->line("","i",0,7);
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "*Numero de Documento:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "num_documeRl", "id" => "num_documeRlID", "colspan"=>"2", "type"=>"numeric", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"10", "value"=>($mInfoRL[0]['num_docume']?$mInfoRL[0]['num_docume']:"")));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "*Primer Apellido:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "nom_apell1Rl", "id" => "nom_apell1RlID", "colspan"=>"2", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"15", "value"=>($mInfoRL[0]['nom_apell1']?$mInfoRL[0]['nom_apell1']:"")));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "Segundo Apellido:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "nom_apell2Rl", "id" => "nom_apell2RlID", "colspan"=>"2", "minlength"=>"3", "maxlength"=>"10", "value"=>($mInfoRL[0]['nom_apell2']?$mInfoRL[0]['nom_apell2']:"")));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "*Nombres:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "nom_replegRl", "id" => "nom_replegRlID", "colspan"=>"2", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"25", "value"=>($mInfoRL[0]['nom_funcio']?$mInfoRL[0]['nom_funcio']:"")));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "*N° de Celular",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "num_celulaRl", "id" => "num_celulaRlID", "colspan"=>"2", "type"=>"numeric", "obl"=>"obl", "maxlength"=>"10", "value"=>($mInfoRL[0]['num_telmov']?$mInfoRL[0]['num_telmov']:"")));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "N° de Telefono",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "num_telefoRl", "id" => "num_telefoRlID", "colspan"=>"2", "type"=>"numeric", "minlength"=>"7", "maxlength"=>"11", "value"=>($mInfoRL[0]['num_telef1']?$mInfoRL[0]['num_telef1']:"")));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "N° de Whatsapp",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "num_WhatsaRl", "id" => "num_WhatsaRlID", "colspan"=>"2", "type"=>"numeric", "maxlength"=>"10", "value"=>($mInfoRL[0]['num_whatsa']?$mInfoRL[0]['num_whatsa']:"")));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "*E-Mail",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "dir_emailxRl", "id" => "dir_emailxRlID", "colspan"=>"2", "obl"=>"obl", "minlength"=>"7", "maxlength"=>"20", "format"=>"mail", "value"=>($mInfoRL[0]['dir_emailx']?$mInfoRL[0]['dir_emailx']:"")));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "Roles",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"4") );
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "Servicio en la EAL",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"1") );
										$mHtml->CheckBox(array("name" => "ind_serealRl", "id" => "ind_serealRlID", "colspan"=>"1", "value"=>"1", "checked"=>($mInfoRL[0]['ind_sereal']=="1"?"checked":"")));
										$mHtml->Label( "Servicio de Asistencia",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"1") );
										$mHtml->CheckBox(array("name" => "ind_serasiRl", "id" => "ind_serasiRlID", "colspan"=>"1", "value"=>"1", "checked"=>($mInfoRL[0]['ind_serasi']=="1"?"checked":"")));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->line("","i",0,7);
									$mHtml->CloseRow();
								$mHtml->CloseTable('tr');
							$mHtml->SetBody("</div>");
						$mHtml->SetBody("</div>");
					$mHtml->SetBody("</div>");
					$mHtml->SetBody("<div id='Sec3RepreLegal' class='contentAccordionForm' style='width:35%;float: left;height:270px;'>");
						#Espacio para las imagenes
						$mHtml->SetBody("<div id='imgReprelegal' class='spaceImage' style='width: 250px; height: 200px;border: 3px solid #555;margin-left:70px;'>");
							if($_REQUEST['accion'] == "edit" && $mInfoRL[0]['url_fotoxx'] != "")
							{
								$mHtml->SetBody("<img src='".substr($mInfoRL[0]['url_fotoxx'], 3)."' height='180' width='250'>");
							}
						$mHtml->SetBody("</div>");
						$mHtml->Table("tr");
							$mHtml->Row();
								$mHtml->Label( "Foto del recurso:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"1") );
							$mHtml->CloseRow();
							$mHtml->Row();
								$mHtml->file(array("name" => "file_RepreLegal", "id" => "file_RepreLegalID", "onchange" => "ValidarImg(this);"));
							$mHtml->CloseRow();
						$mHtml->CloseTable('tr');
					$mHtml->SetBody("</div>");
				$mHtml->SetBody("</div>");
			$mHtml->CloseDiv();
			#Informacion Funcionarios
			$mHtml->OpenDiv("id:infoFunciona; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>Informacion Funcionarios</center></h3>");
				$mHtml->SetBody("<div id='Sec1infoFunciona'>");
					#Div que identifica formulario a clonar
					$mHtml->SetBody("<div class='formInfo'>");
					if(count($mDataInf)<1)
					{
						$mDataInf =	array(array("0"));
					}
					foreach ($mDataInf as $keyInf => $valueInf) 
					{
							#Div que identifica seccion a clonar y genera conteo
							$mHtml->SetBody("<div class='formcount ".($keyInf==0?"formPro":"")."'>");
								$mHtml->SetBody("<h4 align='center'>Funcionario</h4>");
								$mHtml->SetBody("<div id='Sec2infoFunciona$keyInf' class='contentAccordionForm' style='width:60%;float: left;height:270px'>");
									#Espacio para el formulario
									$mHtml->Table("tr");
										$mHtml->Row();
											$mHtml->line("","i",0,7);
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "*Numero de Documento:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "num_docume", "id" => "num_documeID-{$keyInf}", "colspan"=>"2", "type"=>"numeric", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"10", "value"=>($valueInf['num_docume']?$valueInf['num_docume']:"")));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "*Primer Apellido:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "nom_apell1", "id" => "nom_apell1ID-{$keyInf}", "colspan"=>"2", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"15", "value"=>($valueInf['nom_apell1']?$valueInf['nom_apell1']:"")));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "Segundo Apellido:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "nom_apell2", "id" => "nom_apell2ID-{$keyInf}", "colspan"=>"2", "minlength"=>"3", "maxlength"=>"10", "value"=>($valueInf['nom_apell2']?$valueInf['nom_apell2']:"")));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "*Nombres:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "nom_repleg", "id" => "nom_replegID-{$keyInf}", "colspan"=>"2", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"25", "value"=>($valueInf['nom_funcio']?$valueInf['nom_funcio']:"")));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "*N° de Celular",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "num_celula", "id" => "num_celulaID-{$keyInf}", "colspan"=>"2", "type"=>"numeric", "obl"=>"obl", "maxlength"=>"10", "value"=>($valueInf['num_telmov']?$valueInf['num_telmov']:"")));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "N° de Telefono",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "num_telefo", "id" => "num_telefoID-{$keyInf}", "colspan"=>"2", "type"=>"numeric", "minlength"=>"7", "maxlength"=>"11", "value"=>($valueInf['num_telef1']?$valueInf['num_telef1']:"")));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "N° de Whatsapp",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "num_Whatsa", "id" => "num_WhatsaID-{$keyInf}", "colspan"=>"2", "type"=>"numeric", "maxlength"=>"10", "value"=>($valueInf['num_Whatsa']?$valueInf['num_Whatsa']:"")));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "*E-Mail",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "dir_emailx", "id" => "dir_emailxID-{$keyInf}", "colspan"=>"2", "obl"=>"obl", "minlength"=>"7", "maxlength"=>"20", "value"=>($valueInf['dir_emailx']?$valueInf['dir_emailx']:"")));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "Roles",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"4") );
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "Servicio en la EAL",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"1") );
											$mHtml->CheckBox(array("name" => "ind_sereal", "id" => "ind_serealID-{$keyInf}", "colspan"=>"1", "value"=>"1", "checked"=>($valueInf['ind_sereal']=="1"?"checked":"")));
											$mHtml->Label( "Servicio de Asistencia",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"1") );
											$mHtml->CheckBox(array("name" => "ind_serasi", "id" => "ind_serasiID-{$keyInf}", "colspan"=>"1", "value"=>"1", "checked"=>($valueInf['ind_serasi']=="1"?"checked":"")));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->line("","i",0,7);
										$mHtml->CloseRow();
									$mHtml->CloseTable('tr');
								$mHtml->SetBody("</div>");
								$mHtml->SetBody("<div id='Sec3infoFunciona$keyInf' class='contentAccordionForm' style='width:35%;float: left;height:270px;'>");
									#Espacio para las imagenes
									$mHtml->SetBody("<div id='imginfoFunciona' class='spaceImage' style='width: 250px; height: 200px;border: 3px solid #555;margin-left:70px;'>");
										if($_REQUEST['accion'] == "edit" && $valueInf['url_fotoxx'] != "")
										{
											$mHtml->SetBody("<img src='".substr($valueInf['url_fotoxx'], 3)."' height='180' width='250'>");
										}
									$mHtml->SetBody("</div>");
									$mHtml->Table("tr");
										$mHtml->Row();
											$mHtml->Label( "Foto del recurso:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"1") );
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->file(array("name" => "file_infoFunciona{$keyInf}", "id" => "file_infoFuncionaID{$keyInf}", "onchange" => "ValidarImg(this);"));
										$mHtml->CloseRow();
									$mHtml->CloseTable('tr');
								$mHtml->SetBody("</div>");
							$mHtml->SetBody("</div>");
					}
					$mHtml->SetBody("</div>");
					$mHtml->SetBody("<div id='Sec4infoFunciona' class='contentAccordionForm' style='width:96%;float: left;height:10px'>");
						$mHtml->Table("tr");
							#Cuerpo de la notificacion
							$mHtml->Row();
								$mHtml->Button( array("value"=>"OTRO", "id"=>"btnOtroInfID","name"=>"btnOtroInf", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"center", "onclick"=>"addOtroInf()") );
							$mHtml->CloseRow();
						$mHtml->CloseTable('tr');
					$mHtml->SetBody("</div>");
				$mHtml->SetBody("</div>");
			$mHtml->CloseDiv();
			#Div Datos Basicos EAL
			$mHtml->OpenDiv("id:infBasicaEal; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>Datos Basicos de la EAL</center></h3>");
				$mHtml->OpenDiv("id:secinfBasicaEal");
					$mHtml->SetBody("<div id='Sec1infBasicaEal' class='contentAccordionForm' style='width:25%;float: left;height:500px'>");
						$mHtml->Table("tr");
							$mHtml->Row();
								$mHtml->Label( "Ubicacion QR Google Maps",  array("align"=>"center", "class"=>"celda_titulo") );
							$mHtml->CloseRow();
						$mHtml->CloseTable("tr");
						$mHtml->SetBody("<div id='Sec2QrEal' align='center' style='padding-top:20px;'>");
						$mHtml->SetBody("</div>");
						$mHtml->Table("tr");
							$mHtml->Row();
								$mHtml->Label( "Ubicacion QR Google Waze",  array("align"=>"center", "class"=>"celda_titulo") );
							$mHtml->CloseRow();
						$mHtml->CloseTable("tr");
						$mHtml->SetBody("<div id='Sec2WazeEal' align='center' style='padding-top:20px;'>");
						$mHtml->SetBody("</div>");
					$mHtml->SetBody("</div>");
					$mHtml->SetBody("<div id='Sec2infBasicaEal' class='contentAccordionForm' style='width:35%;float: left;height:500px;' align='center'>");
						$mHtml->Table("tr");
							$mHtml->Row();
								$mHtml->Label( "Ubicacion Mapa",  array("align"=>"center", "class"=>"celda_titulo") );
							$mHtml->CloseRow();
						$mHtml->CloseTable("tr");
						$mHtml->SetBody("<div id='Sec2MapEal' class='map' style='padding-top:0px;'>");
							$mHtml->SetBody("<div style='display: none;''>");
								$mHtml->SetBody("<div id='pinta' title='pinta'></div>");
							$mHtml->SetBody("</div>");
						$mHtml->SetBody("</div>");
						$mHtml->SetBody("<div id='infMapsID' style='color:black;padding-top:5px;'>");
						$mHtml->SetBody("</div>");
					$mHtml->SetBody("</div>");
					$mHtml->SetBody("<div id='Sec3infBasicaEal' class='contentAccordionForm' style='width:35%;float: left;height:500px'>");
					   $mHtml->Table("tr");
              $mHtml->Row();
                $mHtml->Label( "Cobertura de Asistencia",  array("align"=>"center", "class"=>"celda_titulo") );
              $mHtml->CloseRow();
            $mHtml->CloseTable("tr");
            $mHtml->SetBody("<div id='map' style='width:98%;height:400px;'></div>");
            $mHtml->Table("tr");
              $mHtml->Row();
                $mHtml->Label( "Cobertura Desde:",  array("align"=>"center", "class"=>"celda_titulo") );
                $mHtml->Input(array("name" => "num_cobdes", "id" => "num_cobdesID", "maxlength"=>"50", "value"=>""));
              $mHtml->CloseRow();
              $mHtml->Row();
                $mHtml->Label( "Cobertura Hasta:",  array("align"=>"center", "class"=>"celda_titulo") );
                $mHtml->Input(array("name" => "num_cobhas", "id" => "num_cobhasID", "maxlength"=>"50", "value"=>""));
              $mHtml->CloseRow();
            $mHtml->CloseTable("tr");
          $mHtml->SetBody("</div>");
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			#Div Informacion Complementaria
			$mDataInfComple = self::getFormulFormul();
			if(count($mDataInfComple)>0)
			{
				$mHtml->OpenDiv("id:infoComple; class:accordian");
					$mHtml->SetBody("<h3 style='padding:6px;'><center>Informacion Complementaria de la EAL</center></h3>");
					$mHtml->OpenDiv("id:secinfoComple");
						$mHtml->OpenDiv("id:openFormul");
							$mHtml->Table("tr");
								$mHtml->Row();
									$mHtml->Hidden(array( "name" => "tab_active", "id" => "tab_activeID", "value"=>(count($mactiveTabs)>0?implode(',', $mactiveTabs):"")));
								$mHtml->CloseRow();
								$mHtml->Row();
									$mHtml->line("","i",0,7);
								$mHtml->CloseRow();
								$mHtml->Row();
									$mHtml->Button( array("value"=>"FORMULARIOS", "id"=>"btnNForumlID","name"=>"btnNForuml", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"center","onclick"=>"getFormul()") );
								$mHtml->CloseRow();
								$mHtml->Row();
									$mHtml->line("","i",0,7);
								$mHtml->CloseRow();
							$mHtml->CloseTable('tr');
						$mHtml->CloseDiv();
						$mHtml->OpenDiv("id:tabs");
							if(count($mactiveTabs)>0)
							{
								$mHtml->SetBody("<ul id='ulTab'>");
									foreach ($mactiveTabs as $kTab => $vTab) 
									{
										$mHtml->SetBody("<li><a href='#tab".$vTab."' >".self::getFormulFormul($vTab)[0]['nom_formul']."<span class='ui-icon ui-icon-close' role='presentation' onclick='RemoveTabs(this);'>Remove Tab</span></a></li>");
									}
								$mHtml->SetBody("</ul>");
								$mHtml->SetBody(self::getDrawFormul(implode(',', $mactiveTabs),$_REQUEST['cod_contro']));
								$mHtml->SetBody("<script>$('#tabs').tabs();</script>");
							}
							else
							{
								$mHtml->SetBody("<ul id='ulTab'>");
								$mHtml->SetBody("</ul>");
							}
						$mHtml->CloseDiv();
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();
			}
			#Div botones
			$mHtml->OpenDiv("id:Botonera; class:accordian");
				$mHtml->SetBody("<h3 style='padding:6px;'><center>Crear Hoja de vida</center></h3>");
				$mHtml->OpenDiv("id:secBotones");
					$mHtml->Table("tr");
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->Button( array("value"=>($_REQUEST['accion'] == "edit"?"EDITAR":"REGISTRAR"), "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"right", "colspan"=>"2","onclick"=>"ConfirmAlmacerHvEal(".($_REQUEST['accion'] == "edit"?"'2'":"'1'").")") );
							$mHtml->Button( array("value"=>"VOLVER", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"left", "colspan"=>"5","onclick"=>"limpiarForm()") );
						$mHtml->CloseRow();
						$mHtml->Row();
							$mHtml->line("","i",0,7);
						$mHtml->CloseRow();
					$mHtml->CloseTable('tr');
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
			$mHtml->SetBody("<style>
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

                /*estilos google maps*/
                #right-panel {
                  font-family: 'Roboto','sans-serif';
                  line-height: 30px;
                  padding-left: 10px;
                }

                #right-panel select, #right-panel input {
                  font-size: 15px;
                }

                #right-panel select {
                  width: 100%;
                }

                #right-panel i {
                  font-size: 12px;
                }
                html, body {
                  height: 100%;
                  margin: 0;
                  padding: 0;
                }
                #map {
                  height: 100%;
                  float: left;
                  width: 63%;
                  height: 100%;
                }
                #right-panel {
                  float: right;
                  width: 34%;
                  height: 100%;
                }
                .panel {
                  height: 100%;
                  overflow: auto;
                }
							</style>");
			$mHtml->SetBody('<script>
		                        $( function() {
								    $( "#PuntoEal, #RepreLegal, #infoFunciona, #infoComple, #Botonera, #infBasicaEal" ).accordion({
								      	autoHeight: false,
									    collapsible: true
									    //,active: false
								    });
								} );
		                    </script>');
			# Muestra Html
	        echo $mHtml->MakeHtml();
	    }catch(Exception $e)
		{
			echo "Error funcion FormNewHvEAL", $e->getMessage(), "\n";
		}
    }

    /*! \fn: getOptionFormComp
     *  \brief: Obtiene el listados de formularios complementarios
     *  \author: Edward Serrano
     *  \date: 08/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    public function getOptionFormComp()
    {
    	try
    	{
	    	$mHtml = new Formlib(2);
	    	$mHtml->SetBody("<div id='OptionFormComp' class='tab-content'>");
	    		$mHtml->Table("tr");
	    			$mHtml->Label( "Fomularios Complementarios",  array("align"=>"center", "class"=>"celda_info", "colspan"=>"2") );
	    			$mHtml->CloseRow();
	    			$mHtml->Row();
	    			$mHtml->Label( "Formulario",  array("align"=>"center", "class"=>"celda_titulo") );
	    			$mHtml->Label( "Opcion",  array("align"=>"center", "class"=>"celda_titulo") );
	    			$mHtml->CloseRow();
	    			foreach (self::getFormulFormul($_REQUEST['tab_active']) as $key => $value) 
	    			{
	    				$mHtml->Row();
	    					$mHtml->Label( $value['nom_formul'],  array("align"=>"center", "class"=>"celda_info") );
	    					$mHtml->CheckBox(array("align"=>"center", "name" => "ind_Form".$value['cod_consec'], "value" => $value['cod_consec']));
	    				$mHtml->CloseRow();
	    			}
	    			$mHtml->Row();
						$mHtml->Button( array("value"=>"AÑADIR", "id"=>"btnAddForumlID","name"=>"btnAddForuml", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"center","onclick"=>"addForumlComp()") );
						$mHtml->Button( array("value"=>"CANCELAR", "id"=>"btnCanForumlID","name"=>"btnCanForuml", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"center","onclick"=>"closePopUp('popForumlID');$('#popForumlID').remove();") );
					$mHtml->CloseRow();
	    		$mHtml->CloseTable("tr");
	    	$mHtml->SetBody("</div>");
	    	echo $mHtml->MakeHtml();
	    }catch(Exception $e)
		{
			echo "Error funcion getOptionFormComp", $e->getMessage(), "\n";
		}
    }

    /*! \fn: getFormulFormul
     *  \brief: Obtiene los formularios regitrados
     *  \author: Edward Serrano
     *  \date: 08/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    private function getFormulFormul($cod_consec)
    {
    	try
    	{
	    	//$cod_consec son los formularios que ya estan selecionados y los escluye
	    	$sql = "SELECT a.cod_consec, a.nom_formul
					  FROM ".BASE_DATOS.".tab_formul_formul a
					  WHERE 1=1 
					  ".($cod_consec!=""?"AND a.cod_consec NOT IN ({$cod_consec})":"")."
				 ";
			$consult = new Consulta($sql, self::$cConexion );
			$result = $consult->ret_matrix('a');

			return $result;
    	}catch(Exception $e)
		{
			echo "Error funcion getFormulFormul", $e->getMessage(), "\n";
		}
    }

    /*! \fn: getFormulDetail
     *  \brief: Obtiene los detalles del formulario
     *  \author: Edward Serrano
     *  \date: 08/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    private function getFormulDetail($cod_formul)
    {
    	try
    	{
	    	$sql = "SELECT a.cod_consec, a.ind_obliga, a.cod_campox,
	    				   a.num_ordenx
					  FROM ".BASE_DATOS.".tab_formul_detail a
					  WHERE cod_formul= {$cod_formul}
					  ORDER BY a.num_ordenx
				 ";
			$consult = new Consulta($sql, self::$cConexion );
			$result = $consult->ret_matrix('a');

			return $result;
    	}catch(Exception $e)
		{
			echo "Error funcion getFormulDetail", $e->getMessage(), "\n";
		}
    }

    /*! \fn: getFormulDetail
     *  \brief: Obtiene los campos del formulario
     *  \author: Edward Serrano
     *  \date: 08/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    private function getFormulCampos($cod_consec)
    {
    	try
    	{
	    	$sql = "SELECT a.nom_campox, a.ind_tipoxx, a.val_htmlxx,
	    				   a.val_maximo, a.val_minimo
					  FROM ".BASE_DATOS.".tab_formul_campos a
					  WHERE ind_estado=1 AND cod_consec= {$cod_consec}

				 ";
			$consult = new Consulta($sql, self::$cConexion );
			$result = $consult->ret_matrix('a');

			return $result;
    	}catch(Exception $e)
		{
			echo "Error funcion getFormulCampos", $e->getMessage(), "\n";
		}
    }

    /*! \fn: getDrawFormul
     *  \brief: pinta los formularios registrados en tab_formul_formul
     *  \author: Edward Serrano
     *  \date: 08/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    public function getDrawFormul($cod_consec = NULL, $cod_contro = NULL)
    {
    	try
    	{
    		if($cod_consec != NULL)
    		{
	    		$mFormul = explode(",", $cod_consec);
	    		$mHtml = new Formlib(2, "yes",TRUE);
    		}
    		else
    		{
	    		$mFormul = explode(",", $_REQUEST['cod_consec']);
	    		$mHtml = new Formlib(2);
    		}
	    	foreach ($mFormul as $kForm => $vForm) 
	    	{
	    		$coutRow = 0; 
		    	$mHtml->OpenDiv("id:tab{$vForm}");
		    		$mHtml->Table("tr");
		    			$mHtml->Row();
		    			foreach (self::getFormulDetail($vForm) as $key => $value) 
		    			{
		    				if($coutRow==2)
		    				{
		    					$mHtml->CloseRow();
		    					$mHtml->Row();
		    					$coutRow = 0;
		    				}
		    				$mDatainfCampos = self::getFormulCampos($value['cod_campox']);
		    				$mHtml->SetBody("<td class='celda_titulo'><label>".$mDatainfCampos[0]['nom_campox']."</label></td><td class='celda_info' ".($value['ind_obliga']==1?"obl='obl'":"").">".self::drawCampos($mDatainfCampos[0],$vForm, $value['cod_campox'], $cod_contro)."</td>");
		    				$coutRow++;
		    			}
		    		$mHtml->CloseTable("tr");
		    	$mHtml->CloseDiv();
	    	}
	    	if($cod_consec != NULL)
    		{
		    	return $mHtml->MakeHtml();
    		}
    		else
    		{
		    	echo $mHtml->MakeHtml();
    		}
	    }catch(Exception $e)
		{
			echo "Error funcion getDrawFormul", $e->getMessage(), "\n";
		}
    }

    /*! \fn: getControlEal
     *  \brief: Obtiene los campos fisicos registrados
     *  \author: Edward Serrano
     *  \date: 08/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    private function getControlEal()
    {
    	try
    	{
	    	$sql = "SELECT a.cod_contro, CONCAT(a.cod_contro,' - ',a.nom_contro)
					  FROM ".BASE_DATOS.".tab_genera_contro a
					  WHERE ind_estado = 1 AND ind_virtua = 0
					   ORDER BY a.nom_contro 
				 ";
			$consult = new Consulta($sql, self::$cConexion );
			$result = $consult->ret_matrix('i');

			return $result;
		}catch(Exception $e)
		{
			echo "Error funcion getControlEal", $e->getMessage(), "\n";
		}
    }

    /*! \fn: insertar
     *  \brief: Insertar nueva Hoja de vida eal
     *  \author: Edward Serrano
     *  \date: 08/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    public function insertar()
    {
    	try
    	{	
    		//Array de eventos
    		$Estados = array();
    		$delImg = array();
    		//Ruta Servidor
    		$ruta = '../../'.BASE_DATOS.'/formdoc/';
    		//Informacion del representante legal
    		$mDataRL = json_decode($_REQUEST['RepreLegal']);
    		//inicio transacion
		    $consultaInicial = new Consulta( "SELECT 1 FROM DUAL", self::$cConexion,"BR");
		    //Nuevo registro
		    $mQueryP = "INSERT INTO ".BASE_DATOS.".tab_respon_funcio 
			    						(
			    								cod_consec, cod_contro, num_docume,
			    								nom_apell1, nom_apell2, nom_funcio,
			    								num_telmov, num_telef1, num_whatsa,
			    								dir_emailx, ind_sereal, ind_serasi,
			    								url_fotoxx, ind_estado, ind_repleg,
			    								usr_creaci, fec_creaci
			    						)
			    						VALUES ";
		    if($_REQUEST['accion']=="1")
		    {
			    //obtengo el maximo regitro
			    $maxRg = self::getMaxFuncio($_REQUEST['cod_contro']);
			    //Nuevo la imagen al repositorio
			    if($_FILES[$mDataRL->file])
			    {
	    			$Estados = self::moveImg($Estados, $mDataRL->file,$ruta);
			    }
			    $mQueryRL = "(".$maxRg.", '{$_REQUEST['cod_contro']}', '{$mDataRL->num_documeRl}',
			    					'{$mDataRL->nom_apell1Rl}', '{$mDataRL->nom_apell2Rl}', '{$mDataRL->nom_replegRl}',
			    					'{$mDataRL->num_celulaRl}', '{$mDataRL->num_telefoRl}', '{$mDataRL->num_WhatsaRl}',
			    					'{$mDataRL->dir_emailxRl}', '{$mDataRL->ind_serealRl}', '{$mDataRL->ind_serasiRl}',
			    					'".$Estados["uploadFiles"][$mDataRL->file]."', 1, 1, '".$_SESSION['datos_usuario']['cod_usuari']."',NOW())";
			   	$consultaFile = new Consulta($mQueryP.$mQueryRL, self::$cConexion, "R");
		   	}
		   	else
		   	{
		   		//obtengo los registros existentes
		   		$mInfoRL = self::getInfResponFuncion($_REQUEST['cod_contro'], "1");
		   		if($_FILES[$mDataRL->file])
			    {
	    			$Estados = self::moveImg($Estados, $mDataRL->file,$ruta);
	    			//array para eliminar las imagenes que son actualizadas
				    if($mInfoRL[0]['url_fotoxx'] != "")
				    {
				    	$delImg['uploadFiles'][] = $mInfoRL[0]['url_fotoxx'];
				    }
			    }
			    else
			    {
			    	if($mInfoRL[0]['url_fotoxx'] != "")
			    	{

			    		$Estados["uploadFiles"][$mDataRL->file] = $mInfoRL[0]['url_fotoxx'];
			    	}
			    }
		   		$mQueryRL = "UPDATE ".BASE_DATOS.".tab_respon_funcio SET
		   							num_docume = {$mDataRL->num_documeRl},
		   							nom_apell1 = '{$mDataRL->nom_apell1Rl}',
									nom_apell2 = '{$mDataRL->nom_apell2Rl}',
									nom_funcio = '{$mDataRL->nom_replegRl}',
									num_telmov = '{$mDataRL->num_celulaRl}',
									num_telef1 = '{$mDataRL->num_telefoRl}',
									num_whatsa = '{$mDataRL->num_WhatsaRl}',
									dir_emailx = '{$mDataRL->dir_emailxRl}',
									ind_sereal = '{$mDataRL->ind_serealRl}',
									ind_serasi = '{$mDataRL->ind_serasiRl}',
									url_fotoxx = '".$Estados["uploadFiles"][$mDataRL->file]."',
									usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
									fec_modifi = NOW()
							WHERE 	cod_consec = ".$mInfoRL[0]['cod_consec']." AND
									cod_contro = ".$_REQUEST['cod_contro']."
		   							";
		   		$consultaFile = new Consulta($mQueryRL, self::$cConexion, "R");
		   	}
		   	if($consultaFile)
		   	{
		   		$Estados["OK"]["consulta"][]="Se almaceno correctamente el representante legal";
		   	}
		   	else
		   	{
		   		$Estados["ERROR"]["consulta"][]="error en la consulta representante legal";
		   	}
		   	//Informacion de los funcionarios
		   	if($_REQUEST['infoFunciona'] != "{}")
		   	{
				//obtengo los registros existentes
		   		$mInfoRLOld = self::getInfResponFuncion($_REQUEST['cod_contro'], "0");
				$mDataFn = json_decode($_REQUEST['infoFunciona']);
				//Opcion de edicion, donde elimino los datos de los funcionarios para generar los registros nuevos
				if($_REQUEST['accion']=="2")
		    	{
		   			$mQueryDelFn = "DELETE FROM ".BASE_DATOS.".tab_respon_funcio WHERE cod_contro =".$_REQUEST['cod_contro']." AND ind_repleg=0";	
		   			$consultaDelFn = new Consulta($mQueryDelFn, self::$cConexion, "R");
		   			$maxRg = self::getMaxFuncio($_REQUEST['cod_contro'])-1;
		   			if($consultaDelFn)
		   			{
		   				$Estados["OK"]["consulta"][]="Se Limpio los datos de los funcionarios";
		   			}
		   			else
		   			{
		   				$Estados["ERROR"]["consulta"][]="error al limpir los datos de los funcionarios";
		   			}
		    	}
		    	$mQueryFn = array();
				foreach ($mDataFn as $keyFn => $valueFn) 
				{
				   	$maxRg++;
    				$miInfo = self::RecorrerArray($mInfoRLOld, 'num_docume',$valueFn->num_docume);
				   	if($_FILES[$valueFn->file])
					{
			    		$Estados = self::moveImg($Estados, $valueFn->file,$ruta);
			    		if($miInfo['url_fotoxx'] != "")
			    		{
		   					$delImg['uploadFiles'][] = $miInfo['url_fotoxx'];
		   				}
					}
					else
				    {
				    	if($miInfo['url_fotoxx'] != "")
				    	{
				    		$Estados["uploadFiles"][$valueFn->file] = $miInfo['url_fotoxx'];
				    	}
				    }
				   	$mQueryFn[] = "(".$maxRg.", '{$_REQUEST['cod_contro']}', '{$valueFn->num_docume}',
				    					'{$valueFn->nom_apell1}', '{$valueFn->nom_apell2}', '{$valueFn->nom_repleg}',
				    					'{$valueFn->num_celula}', '{$valueFn->num_telefo}', '{$valueFn->num_Whatsa}',
				    					'{$valueFn->dir_emailx}', '{$valueFn->ind_sereal}', '{$valueFn->ind_serasi}',
				    					'".$Estados["uploadFiles"][$valueFn->file]."', 1, 0, '".$_SESSION['datos_usuario']['cod_usuari']."',NOW())";
				}
				$consultaFn = new Consulta($mQueryP.implode(",", $mQueryFn), self::$cConexion, "R");
		    	if($consultaFn)
		   		{
		   			$Estados["OK"]["consulta"][]="Se almaceno correctamente los Funcionarios";
		   		}
		   		else
		   		{
		   			$Estados["ERROR"]["consulta"][]="error en la consulta Funcionarios";
		   		}
		   	}

		   	//informacion tab
		   	if($_REQUEST['infTab'] != "{}")
		   	{
		   		$mDataTab = json_decode($_REQUEST['infTab']);
		   		$mQueryTab = array();
		   		$mQuitax = array("formulcampo", "[", "]", "tab", "file" );
				$mColoca = array("", "", "", "", "");
		   		$mQueryP = "INSERT INTO ".BASE_DATOS.".tab_respon_frmeal 
		    							(cod_contro, cod_formul, cod_campos,
		    							 val_campos, rut_docume, usr_creaci, 
		    							 fec_creaci)
		    							 VALUES ";
		   		foreach ($mDataTab as $keyTab => $valueTab) 
		   		{
		   			$mIdForm = str_replace($mQuitax, $mColoca, $keyTab);
		   			//Opcion de edicion, donde elimino los datos de los tab para generar los registros nuevos
		   			foreach ($valueTab as $keyField => $valueField) 
		   			{
			   			$mIdCampo = str_replace($mQuitax, $mColoca, $keyField);
		   				if($keyField == "file")
		   				{
			   				$mIdCampo = str_replace($mQuitax, $mColoca, $valueField);
			   				$miInfo = self::getInfCampos($mIdCampo, $mIdForm, $_REQUEST['cod_contro'])[0];
						   	if($_FILES['formulcampo'.$mIdCampo])
							{
					    		$Estados = self::moveImg($Estados, 'formulcampo'.$mIdCampo,$ruta);
					    		if($miInfo['rut_docume'] != "")
					    		{
				   					$delImg['uploadFiles'][] = $miInfo['rut_docume'];
				   				}
						    	$valueField = $_FILES['formulcampo'.$mIdCampo]['name'];
							}
							else
						    {
						    	if($miInfo['rut_docume'] != "")
						    	{
						    		$Estados["uploadFiles"]['formulcampo'.$mIdCampo] = $miInfo['rut_docume'];
						    	}
						    }
		   				}
		   				$mQueryTab[] = "('{$_REQUEST['cod_contro']}', '{$mIdForm}', '".$mIdCampo."',
		   								'{$valueField}', '".$Estados["uploadFiles"]['formulcampo'.$mIdCampo]."','".$_SESSION['datos_usuario']['cod_usuari']."',
		   								 NOW())";
		   			}
		   			if($_REQUEST['accion']=="2")
			    	{
					   	//obtengo los registros existentes
			   			$mQueryDelTb = "DELETE FROM ".BASE_DATOS.".tab_respon_frmeal WHERE cod_contro =".$_REQUEST['cod_contro']." AND cod_formul=".$mIdForm;	
			   			$consultaDelTb = new Consulta($mQueryDelTb, self::$cConexion, "R");
			   			if($consultaDelTb)
			   			{
			   				$Estados["OK"]["consulta"][]="Se Limpio los datos de los Tab";
			   			}
			   			else
			   			{
			   				$Estados["ERROR"]["consulta"][]="error al limpir los datos de los Tab";
			   			}
			    	}
		   		}
		   		$consultaTab = new Consulta($mQueryP.implode(",", $mQueryTab), self::$cConexion, "R");
		   		if($consultaTab)
		   		{
		   			$Estados["OK"]["consulta"][]="Se almaceno correctamente los Tabs";
		   		}
		   		else
		   		{
		   			$Estados["ERROR"]["consulta"][]="error en la consulta Tabs";
		   		}
		   	}
		   	//Informacion a retornar
		   	if(!$Estados["ERROR"]){
				$Estados["resp"]="ok";
				$consultaFinal = new Consulta("COMMIT", self::$cConexion);
				//Elimino la imaganes si existe
		   		if(count($delImg)>0)
		   		{
		   			$delImg = self::removeImg($delImg,$ruta);
		   			echo "<pre>";print_r($delImg);echo "</pre>";
		   		}
			}else
			{
				$Estados["resp"]="error";
				$consultaFinal = new Consulta("ROLLBACK", self::$cConexion);
				$Estados=self::removeImg($Estados,$ruta);
			}
			//$consultaFinal = new Consulta("ROLLBACK", self::$cConexion);
    		echo json_encode($Estados);
    	}catch(Exception $e)
		{
			echo "Error funcion insertar", $e->getMessage(), "\n";
		}
    }

    /*! \fn: moveImg
    * \brief: Mueve las imagenes a su respectivo repositorio
    * \author: Edward Serrano
    * \date: 15/06/2017
    * \date modified: dia/mes/año
    * \param: 
    */
    private function moveImg($Estados, $nameImg, $ruta)
    {
    	try
    	{
    		$date = date("YmdHis");
    		#separo por punto el mombre del archivo
		    $type = explode(".", basename($_FILES[$nameImg]["name"] ));
		    #asigno nuevo combre al archivo con codificacion MD5
		    $newName = md5($_FILES[$nameImg]["name"].$date).rand(1,100).".".$type[1];
    		if ( move_uploaded_file($_FILES[$nameImg]['tmp_name'], $ruta.$newName) )
			{
				$Estados["OK"]["carga"] = "se cargo el archivo:".$_FILES[$nameImg]["name"];
				$Estados["uploadFiles"][$nameImg] = $ruta.$newName;
			}
			else
			{
				$Estados["ERROR"]["carga"] = "error cargar archivo:".$_FILES[$nameImg]["name"];
			}
			return $Estados;
    	}catch(Exception $e)
    	{
    		echo "Error funcion moveImg", $e->getMessage(), "\n";
    	}
    }

    /*! \fn: removeImg
    * \brief: elimina las imagenes en caso de que ocurra un error
    * \author: Edward Serrano
    * \date: 16/06/2017
    * \date modified: dia/mes/año
    * \param: 
    */
    private function removeImg($Estados, $ruta)
    {
    	try
    	{
    		foreach ($Estados["uploadFiles"] as $key => $value) 
    		{
    			echo "<pre>";print_r($value);echo "</pre>";
    			$DocEliminar = unlink($value);
				if($DocEliminar==TRUE)
				{
					$Estados["OK"]["remove"] = "se elimino el archivo:".$value;
				}
				else
				{
					$Estados["ERROR"]["remove"] = "error eliminar archivo:".$value;
				}
    		}
			return $Estados;
    	}catch(Exception $e)
    	{
    		echo "Error funcion moveImg", $e->getMessage(), "\n";
    	}
    }

    /*! \fn: getMaxFuncio
     *  \brief: Obtiene el maximo dato de la tabla tab_respon_funcio
     *  \author: Edward Serrano
     *  \date: 15/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    private function getMaxFuncio($cod_contro)
    {
    	try
    	{
	    	$sql = "SELECT IF(MAX(a.cod_consec) IS NULL ,1,MAX(a.cod_consec)+1)
					  FROM ".BASE_DATOS.".tab_respon_funcio a
					  WHERE a.cod_contro = {$cod_contro}
				 ";
			$consult = new Consulta($sql, self::$cConexion, "R");
			$result = $consult->ret_matrix('i');

			return $result[0][0];
		}catch(Exception $e)
		{
			echo "Error funcion getMaxFuncio", $e->getMessage(), "\n";
		}
    }

    /*! \fn: getInfResponFuncion
     *  \brief: Obtiene el maximo dato de la tabla tab_respon_funcio
     *  \author: Edward Serrano
     *  \date: 15/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    private function getInfResponFuncion($cod_contro, $ind_repleg, $num_docume = NULL)
    {
    	try
    	{
	    	$sql = "SELECT  cod_consec, cod_contro, num_docume,
							nom_apell1, nom_apell2, nom_funcio, 
							num_telmov, num_telef1, num_whatsa,
							dir_emailx, ind_sereal, ind_serasi,
							url_fotoxx, ind_estado, ind_repleg
					  FROM ".BASE_DATOS.".tab_respon_funcio a
					  WHERE a.cod_contro = {$cod_contro} AND 
					  		a.ind_repleg = {$ind_repleg}
				 			".($num_docume!=NULL?" AND a.num_docume = {$num_docume}":"");
			$consult = new Consulta($sql, self::$cConexion);
			$result = $consult->ret_matrix('a');

			return $result;
		}catch(Exception $e)
		{
			echo "Error funcion getInfResponFuncion", $e->getMessage(), "\n";
		}
    }

    /*! \fn: getInfTabs
     *  \brief: Obtiene los formularios dinamicos
     *  \author: Edward Serrano
     *  \date: 20/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    private function getInfTabs($cod_contro)
    {
    	try
    	{
	    	$sql = "SELECT  cod_formul
					  FROM ".BASE_DATOS.".tab_respon_frmeal a
					  WHERE a.cod_contro = {$cod_contro} 
					  GROUP BY cod_formul
				 ";
			$consult = new Consulta($sql, self::$cConexion);
			$result = $consult->ret_matrix('a');

			return $result;
		}catch(Exception $e)
		{
			echo "Error funcion getInfResponFuncion", $e->getMessage(), "\n";
		}
    }

    /*! \fn: drawCampos
     *  \brief: Prepara los campos a pintar
     *  \author: Edward Serrano
     *  \date: 20/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    private function drawCampos($html, $idForm, $cod_campox, $cod_contro = NULL)
    {
    	try
    	{
	    	if($cod_contro == NULL)
	    	{
	    		if($html['ind_tipoxx']=="file")
	    		{
	    			$patrones = Array("0"=>"[", "1"=>"]");
	    			$sustituc = Array("0"=>"" , "1"=>"-".$idForm);
	    			$mValue = str_replace($patrones, $sustituc, $html['val_htmlxx']);
	    			return $mValue;
	    		}
	    		else
	    		{
	    			return $html['val_htmlxx'];
	    		}
	    		
	    	}
	    	else
	    	{
	    		$mData = self::getInfCampos($cod_campox, $idForm, $cod_contro);
	    		switch ($html['ind_tipoxx']) {
	    			case 'text': case 'number': case 'date':
	    				$mValue = str_replace('dataAttr', 'value="'.$mData[0]['val_campos'].'"', $html['val_htmlxx']);
	    				break;
	    			
	    			case 'checkbox':
	    				$mValue = str_replace('dataAttr', ($mData[0]['val_campos']!=""?'checked':''), $html['val_htmlxx']);
	    				break;

	    			case 'radio':
	    				$patrones = Array("0"=>"/value=\"".$mData[0]['val_campos']."\"/");
	    				$sustituc = Array("0"=>"/value=\"".$mData[0]['val_campos']."\"/ checked");
	    				$mValue = preg_replace($patrones, $sustituc, $html['val_htmlxx']);
	    				break;

	    			case 'select':
	    				$patrones = Array("0"=>"/dataAttr>".$mData[0]['val_campos']."/");
	    				$sustituc = Array("0"=>"selected>".$mData[0]['val_campos']);
	    				$mValue = preg_replace($patrones, $sustituc, $html['val_htmlxx']);
	    				break;

	    			case 'file':
	    				$patrones = Array("0"=>"[", "1"=>"]");
		    			$sustituc = Array("0"=>"" , "1"=>"-".$idForm);
		    			$mValue = str_replace($patrones, $sustituc, $html['val_htmlxx']);
		    			if($mData[0]['rut_docume'] != "")
		    			{
		    				$mValue .="<p>".$mData[0]['val_campos']." <img width='15' height='15' value='Vizualizar' onclick='verArchivos(\"{$idForm}\", \"{$cod_campox}\", \"{$cod_contro}\")' src='../satt_standa/imagenes/ver.png'></p>"; 
		    			}
	    				break;
	    		}
	    		return $mValue;
	    	}
		}catch(Exception $e)
		{
			echo "Error funcion drawCampos", $e->getMessage(), "\n";
		}
    }

    /*! \fn: getInfCampos
     *  \brief: Obtiene los formularios dinamicos
     *  \author: Edward Serrano
     *  \date: 20/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    private function getInfCampos($cod_campox, $idForm, $cod_contro)
    {
    	try
    	{
	    	$sql = "SELECT  val_campos, rut_docume
					  FROM ".BASE_DATOS.".tab_respon_frmeal a
					  WHERE a.cod_campos = {$cod_campox} AND a.cod_formul={$idForm} AND a.cod_contro = {$cod_contro} 
				 ";
			$consult = new Consulta($sql, self::$cConexion, "R");
			$result = $consult->ret_matrix('a');
			return $result;
		}catch(Exception $e)
		{
			echo "Error funcion getInfCampos", $e->getMessage(), "\n";
		}
    }

    /*! \fn: RecorrerArray
     *  \brief: Recorro el array y devuelvo el valor padre
     *  \author: Edward Serrano
     *  \date: 20/06/2017
     *  \date modified: dia/mes/año
     *  \return Array
     */
    private function RecorrerArray($mArray, $campo, $valorC)
    {
    	try
    	{
	    	foreach ($mArray as $keyP => $valueP) 
	    	{
	    		if($valueP[$campo]==$valorC)
	    		{
	    			return $valueP; 
	    		}
	    	}
		}catch(Exception $e)
		{
			echo "Error funcion getInfCampos", $e->getMessage(), "\n";
		}
    }

    /*! \fn: tablaDatosBasicosEAL
     *  \brief: Obtengo las cordenadas de los puntos de control
     *  \author: Edward Serrano
     *  \date: 22/06/2017
     *  \date modified: dia/mes/año
     *  \return Json
     */
    public function tablaDatosBasicosEAL()
    {
    	try
    	{
    		$Estado = array();
	    	$sql = "SELECT  a.val_longit, a.val_latitu, a.url_google, a.url_wazexx, a.dir_contro
					  FROM ".BASE_DATOS.".tab_genera_contro a
					  WHERE a.cod_contro = ".$_REQUEST['cod_contro']." 
				 ";
			$consult = new Consulta($sql, self::$cConexion);
			$result = $consult->ret_matrix('a');
			if( $result[0]['val_longit'] != "" && $result[0]['val_latitu'] != "" )
			{
				$Estado['resp'] = "ok";
				$Estado['cord']['val_longit'] = $result[0]['val_longit'];
				$Estado['cord']['val_latitu'] = $result[0]['val_latitu'];
				$Estado['coqr']['url_google'] = $result[0]['url_google'];
				$Estado['coqr']['url_wazexx'] = $result[0]['url_wazexx'];
				$Estado['dirc']['dir_contro'] = utf8_encode($result[0]['dir_contro']);
			}
			else
			{
				$Estado['resp'] = "noCord";
			}

			echo json_encode($Estado) ;
		}catch(Exception $e)
		{
			echo "Error funcion tablaDatosBasicosEAL", $e->getMessage(), "\n";
		}
    }

    /*! \fn: inactivarEal
     *  \brief: Inactiva y activa las Eal
     *  \author: Edward Serrano
     *  \date: 22/06/2017
     *  \date modified: dia/mes/año
     *  \return Json
     */
    public function inactivarEal()
    {
    	try
    	{
    		$Estado = array();
	    	$mQuery = "UPDATE ".BASE_DATOS.".tab_respon_funcio SET
									ind_estado = '".$_REQUEST['accion']."',
									usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
									fec_modifi = NOW()
							WHERE 	cod_contro = ".$_REQUEST['cod_contro']."
		   							";
		   	$consult = new Consulta($mQuery, self::$cConexion);
			if( $consult )
			{
				$Estado['resp'] = "ok";
			}
			else
			{
				$Estado['resp'] = "Error";
			}

			echo json_encode($Estado) ;
		}catch(Exception $e)
		{
			echo "Error funcion inactivarEal", $e->getMessage(), "\n";
		}
    }
    
    /*! \fn: download
     *  \brief: Descarga el documento de los tabs
     *  \author: Edward Serrano
     *  \date: 22/06/2017
     *  \date modified: dia/mes/año
     *  \return Json
     */
    public function download()
    {
    	try
    	{
    		/*$Estado = array();
	    	$mQuery = "UPDATE ".BASE_DATOS.".tab_respon_funcio SET
									ind_estado = '".$_REQUEST['accion']."',
									usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
									fec_modifi = NOW()
							WHERE 	cod_contro = ".$_REQUEST['cod_contro']."
		   							";
		   	$consult = new Consulta($mQuery, self::$cConexion);
			if( $consult )
			{
				$Estado['resp'] = "ok";
			}
			else
			{
				$Estado['resp'] = "Error";
			}

			echo json_encode($Estado) ;*/
			$datos = (object) $_REQUEST;
			$Refdocument=self::getInfCampos($datos->cod_campo, $datos->form, $datos->cod_contro);
			$fileInfo = finfo_open(FILEINFO_MIME_TYPE);
			$detected_type = finfo_file( $fileInfo, substr($Refdocument[0]['rut_docume'], 3) );
			
			$zip = new ZipArchive();
 			$nameFileZip=explode(".", $Refdocument[0]['val_campos']);
			$filename = '../'.BASE_DATOS.'/formdoc/'.$nameFileZip[0].'.zip';
			 
			if($zip->open($filename,ZIPARCHIVE::CREATE)===true) {
			    $zip->addFile(substr($Refdocument[0]['rut_docume'], 3),$Refdocument[0]['val_campos']);
			    $zip->close();
			    if(file_exists($filename))
			    {
			    	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				    header("Content-type: application/zip");
				    header("Content-Transfer-Encoding: binary");
					header("Content-disposition: attachment; filename=".str_replace(" ","_",$nameFileZip[0]).".zip");
					ob_end_clean();
					readfile($filename);
					ob_end_flush();
					unlink($filename);	
			    }
			    else
			    {
			    	echo " el archivo no existe";
			    }
			}
			else 
			{
			    echo 'Error creando '.$filename;
			}
		}catch(Exception $e)
		{
			echo "Error funcion download", $e->getMessage(), "\n";
		}
    }
}
if($_REQUEST["Ajax"] === 'on' )
{
    $_hojaVidaEal = new hojaVidaEal();
}
else
{
    $_hojaVidaEal = new hojaVidaEal( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );
}

?>