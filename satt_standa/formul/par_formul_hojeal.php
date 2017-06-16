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
            $mHtml->SetCssJq("validator"); 
            $mHtml->CloseTable("tr");
            # incluye Css
            $mHtml->SetCssJq("jquery");
            $mHtml->Body(array("menubar" => "no"));

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
							FROM tab_genera_contro a
							INNER JOIN tab_respon_funcio b ON b.cod_contro = a.cod_contro
							WHERE b.ind_repleg = 1
							GROUP BY (a.nom_contro)
							";  
	        
	        //----------------------------------------------------------------------------------------------------------------------------
	        $cList = new DinamicList( self::$cConexion, $mQuery , 1 );
	        $cList -> SetCreate("Agregar Hojas de vida EAL", "onclick:newHojaEAL('new')");
	        $cList -> SetHeader( "Esfera de asistencia logistica", "field:a.nom_contro" );
	        $cList -> SetHeader( "Nombre del representante", "field:a.cod_ciudad" );
	        $cList -> SetHeader( "Celular", "field:a.nom_encarg" );
	        $cList -> SetOption(utf8_decode("Opciones"),"field:cod_option; width:1%; onclikDisable:editarUsuarioMovil( 2, this ); onclikEnable:editarUsuarioMovil( 1, this ); onclikEdit:newHojaEAL('edit', this);" );
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
    			echo "<pre>";print_r($mInfoRL);echo "</pre>";
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
							$mHtml->Select2 (array_merge(array(array('0' =>  '-','1' =>  '-')),self::getControlEal()),  array("name" => "cod_contro", "width" => "25%","colspan"=>"1", "obl"=>"obl", "key"=>($_REQUEST['cod_contro']?"22406":"")) );
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
										$mHtml->Input(array("name" => "num_documeRl", "id" => "num_documeRlID", "value" => "", "colspan"=>"2", "type"=>"numeric", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"10"));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "*Primer Apellido:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "nom_apell1Rl", "id" => "nom_apell1RlID", "value" => "", "colspan"=>"2", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"15"));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "Segundo Apellido:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "nom_apell2Rl", "id" => "nom_apell2RlID", "value" => "", "colspan"=>"2", "minlength"=>"3", "maxlength"=>"10"));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "*Nombres:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "nom_replegRl", "id" => "nom_replegRlID", "value" => "", "colspan"=>"2", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"25"));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "*N° de Celular",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "num_celulaRl", "id" => "num_celulaRlID", "value" => "", "colspan"=>"2", "type"=>"numeric", "obl"=>"obl", "maxlength"=>"10"));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "N° de Telefono",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "num_telefoRl", "id" => "num_telefoRlID", "value" => "", "colspan"=>"2", "type"=>"numeric", "minlength"=>"7", "maxlength"=>"11"));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "N° de Whatsapp",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "num_WhatsaRl", "id" => "num_WhatsaRlID", "value" => "", "colspan"=>"2", "type"=>"numeric", "maxlength"=>"10"));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "*E-Mail",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
										$mHtml->Input(array("name" => "dir_emailxRl", "id" => "dir_emailxRlID", "value" => "", "colspan"=>"2", "obl"=>"obl", "minlength"=>"7", "maxlength"=>"20", "format"=>"mail"));
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "Roles",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"4") );
									$mHtml->CloseRow();
									$mHtml->Row();
										$mHtml->Label( "Servicio en la EAL",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"1") );
										$mHtml->CheckBox(array("name" => "ind_serealRl", "id" => "ind_serealRlID", "value" => "", "colspan"=>"1", "value"=>"1"));
										$mHtml->Label( "Servicio de Asistencia",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"1") );
										$mHtml->CheckBox(array("name" => "ind_serasiRl", "id" => "ind_serasiRlID", "value" => "", "colspan"=>"1", "value"=>"1"));
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
					//$mDataInf = array(array("0"),array("1"));
					$mDataInf = array(array("0"));
					/*if()
					{

					}*/
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
											$mHtml->Input(array("name" => "num_docume", "id" => "num_documeID-{$keyInf}", "value" => "", "colspan"=>"2", "type"=>"numeric", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"10"));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "*Primer Apellido:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "nom_apell1", "id" => "nom_apell1ID-{$keyInf}", "value" => "", "colspan"=>"2", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"15"));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "Segundo Apellido:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "nom_apell2", "id" => "nom_apell2ID-{$keyInf}", "value" => "", "colspan"=>"2", "minlength"=>"3", "maxlength"=>"10"));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "*Nombres:",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "nom_repleg", "id" => "nom_replegID-{$keyInf}", "value" => "", "colspan"=>"2", "obl"=>"obl", "minlength"=>"3", "maxlength"=>"25"));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "*N° de Celular",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "num_celula", "id" => "num_celulaID-{$keyInf}", "value" => "", "colspan"=>"2", "type"=>"numeric", "obl"=>"obl", "maxlength"=>"10"));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "N° de Telefono",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "num_telefo", "id" => "num_telefoID-{$keyInf}", "value" => "", "colspan"=>"2", "type"=>"numeric", "minlength"=>"7", "maxlength"=>"11"));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "N° de Whatsapp",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "num_Whatsa", "id" => "num_WhatsaID-{$keyInf}", "value" => "", "colspan"=>"2", "type"=>"numeric", "maxlength"=>"10"));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "*E-Mail",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"2") );
											$mHtml->Input(array("name" => "dir_emailx", "id" => "dir_emailxID-{$keyInf}", "value" => "", "colspan"=>"2", "obl"=>"obl", "minlength"=>"7", "maxlength"=>"20"));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "Roles",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"4") );
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->Label( "Servicio en la EAL",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"1") );
											$mHtml->CheckBox(array("name" => "ind_sereal", "id" => "ind_serealID-{$keyInf}", "value" => "", "colspan"=>"1", "value"=>"1"));
											$mHtml->Label( "Servicio de Asistencia",  array("align"=>"center", "class"=>"celda_titulo", "colspan"=>"1") );
											$mHtml->CheckBox(array("name" => "ind_serasi", "id" => "ind_serasiID-{$keyInf}", "value" => "", "colspan"=>"1", "value"=>"1"));
										$mHtml->CloseRow();
										$mHtml->Row();
											$mHtml->line("","i",0,7);
										$mHtml->CloseRow();
									$mHtml->CloseTable('tr');
								$mHtml->SetBody("</div>");
								$mHtml->SetBody("<div id='Sec3infoFunciona$keyInf' class='contentAccordionForm' style='width:35%;float: left;height:270px;'>");
									#Espacio para las imagenes
									$mHtml->SetBody("<div id='imginfoFunciona' class='spaceImage' style='width: 250px; height: 200px;border: 3px solid #555;margin-left:70px;'>");
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
					$mHtml->SetBody("<div id='Sec1infBasicaEal' class='contentAccordionForm' style='width:63%;float: left;height:270px'>");
						$mHtml->Table("tr");
							$mHtml->Row();
								$mHtml->Label( "Ubicacion QR",  array("align"=>"center", "class"=>"celda_titulo") );
								$mHtml->Label( "Ubicacion Mapa",  array("align"=>"center", "class"=>"celda_titulo") );
							$mHtml->CloseRow();
							$mHtml->Row();

							$mHtml->CloseRow();
						$mHtml->CloseTable("tr");
					$mHtml->SetBody("</div>");
					$mHtml->SetBody("<div id='Sec2infBasicaEal' class='contentAccordionForm' style='width:32%;float: left;height:270px'>");
						$mHtml->Table("tr");
							$mHtml->Row();
								$mHtml->Label( "Cobertura de asistencia",  array("align"=>"center", "class"=>"celda_titulo") );
							$mHtml->CloseRow();
							$mHtml->Row();

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
									$mHtml->Hidden(array( "name" => "tab_active", "id" => "tab_activeID", "value"=>""));
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
							$mHtml->SetBody("<ul id='ulTab'>");
							/*$mDivCont = "";
							foreach ($mDataInfComple as $key => $value) 
							{
								$mHtml->SetBody("<li><a href='#tab".$value['cod_consec']."' >".$value['nom_formul']."</a></li>");
								$mDivCont .= "<div id='tab".$value['cod_consec']."'>".self::getDrawFormul($value['cod_consec'])."</div>";
							}*/
							$mHtml->SetBody("</ul>");
							//$mHtml->SetBody($mDivCont);
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
							$mHtml->Button( array("value"=>"REGISTRAR", "id"=>"btnNenviarID","name"=>"btnNenviar", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all", "align"=>"right", "colspan"=>"2","onclick"=>"ConfirmAlmacerHvEal()") );
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
    public function getDrawFormul()
    {
    	try
    	{
	    	$mFormul = explode(",", $_REQUEST['cod_consec']);
	    	$mHtml = new Formlib(2);
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
		    				$mHtml->SetBody("<td class='celda_titulo'><label>".$mDatainfCampos[0]['nom_campox']."</label></td><td class='celda_info' ".($value['ind_obliga']==1?"obl='obl'":"").">".$mDatainfCampos[0]['val_htmlxx']."</td>");
		    				$coutRow++;
		    			}
		    		$mHtml->CloseTable("tr");
		    	$mHtml->CloseDiv();
	    	}
	    	echo $mHtml->MakeHtml();
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
					  WHERE ind_estado = 1 AND ind_virtua = 0 ORDER BY a.nom_contro 
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
    		//Ruta Servidor
    		$ruta = '../../'.BASE_DATOS.'/formdoc/';
    		//Informacion del representante legal
    		$mDataRL = json_decode($_REQUEST['RepreLegal']);
    		//inicio transacion
		    $consultaInicial = new Consulta( "SELECT 1 FROM DUAL", self::$cConexion,"BR");
		    //obtengo el maximo regitro
		    $maxRg = self::getMaxFuncio($_REQUEST['cod_contro']);
		    //Nuevo la imagen al repositorio
		    if($_FILES[$mDataRL->file])
		    {
    			$Estados = self::moveImg($Estados, $mDataRL->file,$ruta);
		    }
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
		    $mQueryRL = "(".$maxRg.", '{$_REQUEST['cod_contro']}', '{$mDataRL->num_documeRl}',
		    					'{$mDataRL->nom_apell1Rl}', '{$mDataRL->nom_apell2Rl}', '{$mDataRL->nom_replegRl}',
		    					'{$mDataRL->num_celulaRl}', '{$mDataRL->num_telefoRl}', '{$mDataRL->num_WhatsaRl}',
		    					'{$mDataRL->dir_emailxRl}', '{$mDataRL->ind_serealRl}', '{$mDataRL->ind_serasiRl}',
		    					'".$Estados["uploadFiles"][$mDataRL->file]."', 1, 1, '".$_SESSION['datos_usuario']['cod_usuari']."',NOW())";
		   	$consultaFile = new Consulta($mQueryP.$mQueryRL, self::$cConexion, "R");
		   	//Informacion de los funcionarios
		   	if($_REQUEST['infoFunciona'] != "{}")
		   	{
				$mDataFn = json_decode($_REQUEST['infoFunciona']);
			   	$mQueryFn = array();
			   	foreach ($mDataFn as $keyFn => $valueFn) 
			   	{
			   		$maxRg++;
			   		if($_FILES[$valueFn->file])
				    {
		    			$Estados = self::moveImg($Estados, $valueFn->file,$ruta);
				    }
			   		$mQueryFn[] = "(".$maxRg.", '{$_REQUEST['cod_contro']}', '{$valueFn->num_docume}',
			    					'{$valueFn->nom_apell1}', '{$valueFn->nom_apell2}', '{$valueFn->nom_repleg}',
			    					'{$valueFn->num_celula}', '{$valueFn->num_telefo}', '{$valueFn->num_Whatsa}',
			    					'{$valueFn->dir_emailx}', '{$valueFn->ind_sereal}', '{$valueFn->ind_serasi}',
			    					'".$Estados["uploadFiles"][$valueFn->file]."', 1, 0, '".$_SESSION['datos_usuario']['cod_usuari']."',NOW())";
			   	}
				$consultaFn = new Consulta($mQueryP.implode(",", $mQueryFn), self::$cConexion, "R");
		   	}
		   	//informacion tab
		   	if($_REQUEST['infTab'] != "{}")
		   	{
		   		$mDataTab = json_decode($_REQUEST['infTab']);
		   		$mQueryTab = array();
		   		$mQuitax = array("formulcampo", "[", "]", "tab" );
				$mColoca = array("", "", "", "");
		   		$mQueryP = "INSERT INTO ".BASE_DATOS.".tab_respon_frmeal 
		    							(cod_contro, cod_formul, cod_campos,
		    							 val_campos, usr_creaci, fec_creaci)
		    							 VALUES ";
		   		foreach ($mDataTab as $keyTab => $valueTab) 
		   		{
		   			$mIdForm = str_replace($mQuitax, $mColoca, $keyTab);
		   			foreach ($valueTab as $keyField => $valueField) 
		   			{
		   				$mQueryTab[] = "('{$_REQUEST['cod_contro']}', '{$mIdForm}', '".str_replace($mQuitax, $mColoca, $keyField)."',
		   								'{$valueField}', '".$_SESSION['datos_usuario']['cod_usuari']."',NOW())";
		   			}
		   		}
		   		$consultaTab = new Consulta($mQueryP.implode(",", $mQueryTab), self::$cConexion, "R");
		   	}
    //$consultaFinal = new Consulta("ROLLBACK", self::$cConexion);
		   	//Informacion a retornar
		   	if(!$Estados["ERROR"]){
				$Estados["OK"]="OK";
				$consultaFinal = new Consulta("COMMIT", self::$cConexion);
			}else
			{
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
		    $newName = md5($_FILES[$nameImg]["name"].$date).".".$type[1];
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
    private function getInfResponFuncion($cod_contro, $ind_repleg)
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
				 ";
			$consult = new Consulta($sql, self::$cConexion);
			$result = $consult->ret_matrix('a');

			return $result;
		}catch(Exception $e)
		{
			echo "Error funcion getInfResponFuncion", $e->getMessage(), "\n";
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