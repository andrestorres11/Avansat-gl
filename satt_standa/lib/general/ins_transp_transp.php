<?php
	ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);

    require "ajax_transp_transp.php";

class Ins_config_emptra {

    var $conexion, $usuario, $cod_aplica;
    private static $cFunciones;

    function __construct($co, $us, $ca) {
    	$mHtml = new FormLib(2);
    	#incluyo css y js para las validaciones
        $mHtml->SetJs("validator");
        $mHtml->SetCssJq("validator");
        echo $mHtml->MakeHtml();

        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        self::$cFunciones = new trans($co, $us, $ca);

        switch ($GLOBALS[opcion]) {
           	case 1;
           		$this->Formulario();
           	break;

           	case 2:
           		$this->listado();
          	 break;

            default;
                $this->filtro();
                break;
        }
    }
    /*! \fn: filtro
     *  \brief: funcion inicial para buscar una transportadora
     *  \author: Ing. Alexander Correa
     *	\date: 31/09/2015
     *	\date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    
    function filtro() {
    	
    	$datos_usuario = $this->usuario->retornar();
        $usuario = $datos_usuario["cod_usuari"];
        
        $inicio[0][0] = 0;
        $inicio[0][1] = "-";

        $mHtml = new FormLib(2);

        # incluye JS
        $mHtml->SetJs("min");
        $mHtml->SetJs("config");
        $mHtml->SetJs("fecha");
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("functions");
        $mHtml->SetJs("ajax_transp_transp");
        $mHtml->SetJs("InsertProtocolo");
        $mHtml->SetJs("dinamic_list");
        $mHtml->SetCss("dinamic_list");
        $mHtml->CloseTable("tr");
        # incluye Css
        $mHtml->SetCssJq("jquery");
        $mHtml->Body(array("menubar" => "no"));
       
       	 
        
        
        #creo el acordeon para el filtro
       	#<DIV fitro>
       		#abre formulario
	       	$mHtml->Form(array("action" => "index.php",
	            "method" => "post",
	            "name" => "form_search",
	            "header" => "Transportadoras",
	            "enctype" => "multipart/form-data"));
		        $mHtml->Row("td");
			        $mHtml->OpenDiv("id:contentID; class:contentAccordion");
				        $mHtml->OpenDiv("id:filtro; class:accordion");
				        	$mHtml->SetBody("<h3 style='padding:6px;'><center>Búsqueda de transportadoras</center></h3>");
				        		$mHtml->OpenDiv("id:sec2");
				        			$mHtml->OpenDiv("id:form2; class:contentAccordionForm");
				        				$mHtml->Table("tr");
								       		$mHtml->Label("Transportadora:", "width:35%; *:1;");
									        $mHtml->Input(array("name" => "trasp[nom_transp]", "id" => "nom_transpID", "width" => "35%"));
									       	$mHtml->SetBody("<td><div id='boton'></div></td>");
				        				$mHtml->CloseTable("tr");
				        			$mHtml->CloseDiv();
				        		$mHtml->CloseDiv();
				        	$mHtml->CloseDiv();
				        $mHtml->CloseDiv();
				    $mHtml->CloseDiv();
				//$mHtml->CloseRow("td");
			#acordeon con la lista de las transportadoras
				//$mHtml->Row("td");
			        $mHtml->OpenDiv("id:tablaID; class:contentAccordion");
				        $mHtml->OpenDiv("id:tabla; class:accordion");
				        	$mHtml->SetBody("<h3 style='padding:6px;'><center>Listado de transportadoras</center></h3>");
				        		$mHtml->OpenDiv("id:sec2");
				        			$mHtml->OpenDiv("id:form3; class:contentAccordionForm");
				        				$mHtml->Table("tr");
								       		$mSql = "SELECT a.cod_tercer, a.abr_tercer, a.dir_domici, 
								                         a.num_telef1, a.dir_emailx, a.cod_estado,
								                         CONCAT( UPPER(b.abr_ciudad), '(', LEFT(c.nom_depart, 4), ') - ', LEFT(d.nom_paisxx, 3) ) abr_ciudad  
								                         FROM ".BASE_DATOS.".tab_tercer_tercer a 
								                         INNER JOIN tab_genera_ciudad b ON b.cod_ciudad = a.cod_ciudad 
								                         INNER JOIN tab_genera_depart c ON b.cod_depart = b.cod_depart 
								                         INNER JOIN tab_genera_paises d ON b.cod_paisxx = c.cod_paisxx
								                         INNER JOIN tab_tercer_activi e ON e.cod_tercer = a.cod_tercer
								                         WHERE e.cod_activi = 1
								                          GROUP BY a.cod_tercer ORDER BY abr_tercer ASC";
									      $_SESSION["queryXLS"] = $mSql;
									      if(class_exists(DinamicList)) {
									      	$list = new DinamicList( $this -> conexion, $mSql, 1 );
									      }
									  	  else
									  	  {
									  	  	include_once("../".DIR_APLICA_CENTRAL."/satt_standa/lib/general/dinamic_list.inc");
									  	  	$list = new DinamicList( $this -> conexion, $mSql, 1 );
									  	  }
									  	   
									      /*
									      $list->SetClose('no');
									      $list->SetHeader("Código de la transportadora", "field:a.cod_tercer; width:1%; type:link; onclick:editarDistribuidora( $(this) )");
									      $list->SetHeader("Transportadora", "field:a.abr_tercer; width:1%");
									      $list->SetHeader("Ciudad", "field:a.abr_ciudad; width:1%");
									      $list->SetHeader("Dirección", "field:a.dir_domici; width:1%");
									      $list->SetHeader("Teléfono", "field:a.num_telef1; width:1%");
									      // $list->SetHeader("Estado", "field:IF( a.cod_estado = '1' ,'Activa<img src=\"../images/delete.png\" style=\"cursor:pointer\" onclick=\" confirmar(\"inactivar\")\" />&nbsp;<img src=\"../images/edit.png\" style=\"cursor:pointer\" onclick=\" editar(\"a.cod_tercer\")\" />', 'Inactiva<img src=\"../images/active.png\" style=\"cursor:pointer\" onclick=\" confirmar(\"activar\")\" />&nbsp;<img src=\"../images/edit.png\" style=\"cursor:pointer\" onclick=\" editar(\"a.cod_tercer\")\" />'); width:1%" );
									     
									      $list->SetHeader("Descripcion","field:a.des_texto; width:1%");

									      $list->Display($this->conexion);

									      $_SESSION["DINAMIC_LIST"] = $list;

									      $Html = $list -> GetHtml();

									 
									      $mHtml->SetBody($Html);
      									*/
				        				$mHtml->CloseTable("tr");
				        			$mHtml->CloseDiv();
				        		$mHtml->CloseDiv();
				        	$mHtml->CloseDiv();
				        $mHtml->CloseDiv();
				    $mHtml->CloseDiv();
				$mHtml->CloseRow("td");
			#fin de acordeon con la lista de las tranportadoras
		#</div>
					 $mHtml->Hidden(array( "name" => "transp[cod_tercer]", "id" => "cod_tercerID"));
					 $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
					 $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
					 $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
					 $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));
			 	
		 # Cierra formulario
        	$mHtml->CloseForm();
        # Cierra Body
        $mHtml->CloseBody();

        # Muestra Html
        echo $mHtml->MakeHtml();
    }

    function Formulario() {

        $datos = self::$cFunciones->getDatosTrasnportadora($_REQUEST['transp']['cod_tercer']);

        
        /*echo "<pre>";
        print_r($datos);die;*/

        # Nuevo frame ---------------------------------------------------------------
        # Inicia clase del fromulario ----------------------------------------------------------------------------------
        $mHtml = new FormLib(2);

        # incluye JS
        $mHtml->SetJs("min");
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("functions");
        $mHtml->SetJs("config");
        $mHtml->SetJs("fecha");
        $mHtml->SetJs("ajax_transp_transp");
		$mHtml->SetJs("validator");
        $mHtml->SetCssJq("validator");		
        # incluye Css
        $mHtml->SetCssJq("jquery");

        # coloca titulo
        $mHtml->SetTitle("Crear Transportadora");

        # Abre Body
        $mHtml->Body(array("menubar" => "no"));

        # Abre Form
        $mHtml->Form(array("action" => "index.php",
            "method" => "post",
            "name" => "form_transpor",
            "header" => "Transportadoras",
            "enctype" => "multipart/form-data"));

    	#variables ocultas
    	$mHtml->Hidden(array( "name" => "transp[cod_ciudad]", "id" => "cod_ciudadID", "value"=>$datos->principal->cod_ciudad)); //el codigo de la ciudad de la transportadora
    	$mHtml->Hidden(array( "name" => "agencia[cod_ciudad]", "id" => "cod_ciudaaID", "value"=>$datos->principal->cod_ciudaa)); //el codigo de la ciudad de la agencia
    	$mHtml->Hidden(array( "name" => "agencia[cod_agenci]", "id" => "cod_agenciID", "value"=>$datos->principal->cod_agenci)); //el codigo de la ciudad de la agencia
    	$mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
		$mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
		$mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
		$mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));

	        # Construye accordion
	        $mHtml->Row("td");
	        $mHtml->OpenDiv("id:contentID; class:contentAccordion");
	        	# Accordion1
	        	$mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
	        		$mHtml->SetBody("<h3 style='padding:6px;'><center>Información Básica de la Transportadora</center></h3>");
	   				$mHtml->OpenDiv("id:sec1;");
	    				$mHtml->OpenDiv("id:form1; class:contentAccordionForm");
	    					$mHtml->Table("tr");
	    						$mHtml->Label("Nit:", "width:25%; *:1;");
						        $mHtml->Input(array("type" => "numeric", "name" => "transp[cod_tercer]", "id" => "cod_tercerID", "width" => "25%", "maxlength" => "12", "validate" => "numero", "obl" => "1", "value" => $datos->principal->cod_tercer));
						        $mHtml->Label("DV:", "width:25%; :1;");
						        $mHtml->Input(array("type" => "numeric", "name" => "transp[num_verifi]", "id" => "num_verifiID", "width" => "10%", "maxlength" => "1", "validate" => "numero", "value" =>  $datos->principal->num_verifi, "end" => true));

						        $mHtml->Label("Abreviatura:", "width:25%; *:1;");
						        $mHtml->Input(array("type" => "alpha", "name" => "transp[abr_tercer]", "validate" => "alpha", "obl" => "1", "minlength" => "5", "maxlength" => "50", "id" => "abr_tercerID", "width" => "25%", "value" => $datos->principal->abr_tercer));

						        $mHtml->Label(utf8_decode("Nombre o Razón Social:"), "width:25%; *:1;");
						        $mHtml->Input(array("type" => "alpha", "name" => "transp[nom_tercer]", "id" => "nom_tercer", "size"=>30, "validate" => "alpha",  "obl" => "1", "minlength" => "5", "maxlength" => "100", "width" => "100px", "value" => $datos->principal->nom_tercer, "end" => true));

						        $mHtml->Label(utf8_decode("Código de Empresa:"), "width:25%; :1;");
						        $mHtml->Input(array("type" => "numeric", "name" => "emptra[cod_minins]", "validate" => "numero", "minlength" => "1", "maxlength" => "4", "id" => "abr_tercer", "width" => "25%", "value" => $datos->principal->cod_minins));
						        $mHtml->Label("Ciudad:", "width:25%; *:1;");
						        $mHtml->Input(array("type" => "text", "name" => "ciudad", "id" => "ciudadID", "validate" => "dir", "minlength" => "8", "maxlength" => "100",  "obl" => "1", "width" => "25%", "value" => $datos->principal->abr_ciudad, "end" => true));

						        $mHtml->Label(utf8_decode("Dirección:"), "width:25%; *:1;");
						        $mHtml->Input(array("type" => "address", "name" => "transp[dir_domici]", "validate" => "alpha",  "obl" => "1", "minlength" => "5", "maxlength" => "100", "id" => "dir_domici", "width" => "25%", "value" => $datos->principal->dir_domici));
						        $mHtml->Label("Telefono:", "width:25%; *:1;");
						        $mHtml->Input(array("type" => "numeric", "name" => "transp[num_telef1]", "validate" => "numero",  "obl" => "1", "minlength" => "7", "maxlength" => "10", "id" => "num_telef1", "width" => "25%", "value" => $datos->principal->num_telef1, "end" => true));

						        $mHtml->Label("Regimen:", "width:25%; *:1;"); 
						        $mHtml->Select2	($datos->regimen,  array("name" => "transp[cod_terreg]", "validate" => "select",  "obl" => "1", "id" => "cod_terregID", "width" => "25%", "key"=> $datos->principal->cod_terreg) );
						        $mHtml->Label("Estado:", "width:25%; :1;");
						        if($datos->principal->cod_estado == 1){						        	
					        		$mHtml->Input(array("type" => "text", "name" => "estado", "id" => "estado","minlength" => "8", "maxlength" => "100", "width" => "25%", "value" => 'Activa', 'disabled'=>true, "end" => true));
					    		}else{
					    			$mHtml->Input(array("type" => "text", "name" => "estado", "id" => "estado","minlength" => "8", "maxlength" => "100", "width" => "25%", "value" => 'Inactiva', 'disabled'=>true, "end" => true));
					    		}
						        $mHtml->Label("Observaciones:", "width:25%; :1;");
						        $mHtml->TextArea($mString, array("cols" => 100, "rows" => 8, "colspan" => "3", "name" => "transp[obs_tercer]", "id" => "obs_tercer", "width" => "25%", "value" => $datos->principal->obs_tercer, "end" => true));
	    					$mHtml->CloseTable("tr");
	    				$mHtml->CloseDiv();
	    			$mHtml->CloseDiv();
	   			$mHtml->CloseDiv();
	        # Fin accordion1		
	        # Accordion2
	        $mHtml->OpenDiv("id:DatosSecundariosID; class:accordion");
	    		$mHtml->SetBody("<h3 style='padding:6px;'><center>Modalidad</center></h3>");
	    		$mHtml->OpenDiv("id:sec2");
	    			$mHtml->OpenDiv("id:form2; class:contentAccordionForm");
	    				$mHtml->Table("tr");
	    					$mHtml->Label(utf8_decode("Distribución:"), "width:25%; :1;");
					        $mHtml->CheckBox(array("name" => "modali[cod_modali1]", "checked"=>($datos->modalidades[0][0] == 1 ? true : false), "width" => "25%", "value" =>1));
					        $mHtml->Label("Masivo:", "width:25%; :1;");
					        $mHtml->CheckBox(array("name" => "modali[cod_modali2]",  "checked"=>($datos->modalidades[1][0] == 2 ? true : false), "width" => "25%", "value" =>2, "end" => true));
					        $mHtml->Label("Semi-masivo:", "width:25%; :1;");
					        $mHtml->CheckBox(array("name" => "modali[cod_modali3]", "checked"=>($datos->modalidades[2][0] == 3 ? true : false), "width" => "25%", "value" => 3));
	    				$mHtml->CloseTable("tr");
	    			$mHtml->CloseDiv();
	    		$mHtml->CloseDiv();
	    	$mHtml->CloseDiv();
	        # Fin accordion2


	        # Accordion3
	        $mHtml->OpenDiv("id:DatosSecundariosID; class:accordion");
	        $mHtml->SetBody("<h3 style='padding:6px;'><center>Habilitaciones Legales</center></h3>");
	        	$mHtml->OpenDiv("id:sec2");
	        		$mHtml->OpenDiv("id:form2; class:contentAccordionForm");
	        			$mHtml->Table("tr");
					        $mHtml->Label("Cobertura Nacional:", "width:25%; :1;");
					        $mHtml->CheckBox(array("name" => "emptra[ind_cobnal]", "id" => "ind_cobnal", "width" => "25%", "value"=>"S", "checked"=>( $datos->principal->ind_cobnal== "S" ? true : false)));
					        $mHtml->Label("Cobertura Internacional:", "width:25%; :1;");
					        $mHtml->CheckBox(array("name" => "emptra[ind_cobint]", "id" => "ind_cobint", "width" => "25%", "value"=>"S", "checked"=>( $datos->principal->ind_cobint== "S" ? true : false), "end" => true));
							$mHtml->Label(utf8_decode("Nro Habilitación Nacional:"), "width:25%; :1;");
	        				$mHtml->Input(array("type" => "alpha", "type" => "numeric",  "validate" => "alpha", "minlength" => "1", "maxlength" => "30", "name" => "emptra[nro_habnal]", "id" => "nro_habnal", "width" => "25%", "value" => $datos->principal->nro_habnal));
					        $mHtml->Label(utf8_decode("Fecha Resolución (AAAA-MM-DD):"), "width:25%; :1;");
					        $mHtml->Input(array("type" => "date", "obl" => "yes", "name" => "emptra[fec_resnal]", "id" => "fec_resnal", "width" => "25%", "maxlength" => "10", "value" => $datos->principal->fec_resnal, "end" => true));
					        $mHtml->Label(utf8_decode("Código Regional:"), "width:25%; :1;");
					        $mHtml->Input(array("type" => "numeric", "obl" => "yes", "name" => "emptra[num_region]",  "validate" => "numero", "minlength" => "1", "maxlength" => "3", "id" => "num_region", "width" => "25%", "maxlength" => "10", "value" => $datos->principal->num_region, "end" => true));
					        $mHtml->Label(utf8_decode("Nro de Resolución:"), "width:25%; :1;");
					        $mHtml->Input(array("type" => "alpha", "obl" => "yes", "name" => "emptra[num_resolu]", "validate" => "alpha", "minlength" => "1", "maxlength" => "8", "id" => "num_resolu", "width" => "25%", "maxlength" => "10", "value" => $datos->principal->num_resolu));
					        $mHtml->Label(utf8_decode("Del (AAAA-MM-DD):"), "width:25%; :1;");
					        $mHtml->Input(array("type" => "date", "obl" => "yes", "name" => "emptra[fec_resolu]", "id" => "fec_resolu", "width" => "25%", "maxlength" => "10", "value" => $datos->principal->fec_resolu, "end" => true));
					        $mHtml->Label(utf8_decode("Rango Autorizado Manifiesto del:"), "width:25%; :1;");
					        $mHtml->Input(array("type" => "alpha", "obl" => "yes", "name" => "emptra[ran_iniman]", "id" => "ran_iniman", "width" => "25%", "validate" => "alpha", "minlength" => "1", "maxlength" => "7", "value" => $datos->principal->ran_iniman));
					        $mHtml->Label(utf8_decode("Hasta:"), "width:25%; :1;");
					        $mHtml->Input(array("type" => "alpha", "obl" => "yes", "name" => "emptra[ran_finman]", "id" => "ran_finman", "width" => "25%", "validate" => "alpha", "minlength" => "1", "maxlength" => "7", "value" => $datos->principal->ran_finman, "end" => true));
					        $mHtml->Label("Gran Contribuyente:", "width:25%; :1;");
					        $mHtml->CheckBox(array("name" => "emptra[ind_gracon]", "id" => "ind_gracon", "width" => "25%", "value"=>"S", "checked"=>( $datos->principal->ind_gracon== "S" ? true : false)));
	        			$mHtml->CloseTable("tr");
	        		$mHtml->CloseDiv();
	        	$mHtml->CloseDiv();
	        $mHtml->CloseDiv();
	        # fin Accordion 3

	        #Acordeon 4
	        $mHtml->OpenDiv("id:DatosSecundariosID; class:accordion");
	    		$mHtml->SetBody("<h3 style='padding:6px;'><center>Certificaciones</center></h3>");
	    		$mHtml->OpenDiv("id:sec2");
	    			$mHtml->OpenDiv("id:form2; class:contentAccordionForm");
	    				$mHtml->Table("tr");
	    					$mHtml->Label(utf8_decode("Certificación ISO:"), "width:25%; :1;");
					        $mHtml->CheckBox(array("name" => "emptra[ind_ceriso]", "checked"=>($datos->principal->ind_ceriso == "S" ? true : false), "id"=>"ind_cerisoID", "width" => "25%", "value" =>"S"));
					        $mHtml->Label("Vigencia(AAAA-MM-DD):", "width:25%; :1;");
					        $mHtml->Input(array("type" => "date", "obl" => "yes", "id"=>"fec_cerisoID", "validate" => "date", "name" => "emptra[fec_ceriso]", "width" => "25%", "maxlength" => "10", "value" => $datos->principal->fec_ceriso, "end" => true));
					        $mHtml->Label(utf8_decode("Certificación BASC:"), "width:25%; :1;");
					        $mHtml->CheckBox(array("name" => "emptra[ind_cerbas]", "id"=>"ind_cerbasID", "checked"=>($datos->principal->ind_cerbas == "S" ? true : false), "width" => "25%", "value" => "S"));
					        $mHtml->Label("Vigencia(AAAA-MM-DD):", "width:25%; :1;");
					        $mHtml->Input(array("type" => "date", "obl" => "yes", "name" => "emptra[fec_cerbas]", "id" => "fec_cerbasID", "width" => "25%", "maxlength" => "10", "value" => $datos->principal->fec_cerbas, "end" => true));
					        $mHtml->Label("Otras:", "width:25%; :1;");
					        $mHtml->Input(array("type" => "date", "obl" => "yes", "name" => "emptra[otr_certif]", "id" => "otr_certifID", "width" => "25%", "maxlength" => "10", "value" => $datos->principal->otr_certif, "end" => true));
	    				$mHtml->CloseTable("tr");
	    			$mHtml->CloseDiv();
	    		$mHtml->CloseDiv();
	    	$mHtml->CloseDiv();
	        #fin Acordeon 4

	        #Acordeon 5
	        $mHtml->OpenDiv("id:DatosSecundariosID; class:accordion");
	        $mHtml->SetBody("<h3 style='padding:6px;'><center>Informaci&oacute;n Agencia Principal</center></h3>");
	        	$mHtml->OpenDiv("id:sec2");
	        		$mHtml->OpenDiv("id:form2; class:contentAccordionForm");
	        			$mHtml->Table("tr");
					        $mHtml->Label(" Nombre:", "width:25%; *:1;");
	        				$mHtml->Input(array("type" => "text", "type" => "alpha", "validate" => "alpha", "obl" => "1", "minlength" => "5", "maxlength" => "100", "name" => "agencia[nom_agenci]", "id" => "nom_agenciID", "width" => "25%", "value" => $datos->principal->nom_agenci, "end" => true));
					        $mHtml->Label(utf8_decode("Ciudad:"), "width:25%; *:1;");
					        $mHtml->Input(array("type" => "text", "validate" => "dir", "obl" => "1", "minlength" => "5", "maxlength" => "50", "name" => "agencia[abr_ciudad]", "id" => "abr_ciudadID", "width" => "25%",  "value" => $datos->principal->abr_ciudaa));
					        $mHtml->Label(utf8_decode("Dirección:"), "width:25%; *:1;");
					        $mHtml->Input(array("type" => "alpha","validate" => "dir", "obl" => "1", "minlength" => "5", "maxlength" => "100", "name" => "agencia[dir_agenci]", "id" => "dir_agenci", "width" => "25%", "value" => $datos->principal->dir_agenci, "end" => true));
					        $mHtml->Label(utf8_decode("Contacto:"), "width:25%; *:1;");
					        $mHtml->Input(array("type" => "text", "validate" => "texto", "obl" => "1", "minlength" => "5", "maxlength" => "50", "name" => "agencia[con_agenci]", "id" => "con_agenci", "width" => "25%",  "value" => $datos->principal->con_agenci));
					        $mHtml->Label(utf8_decode(" Teléfono:"), "width:25%; *:1;");
					        $mHtml->Input(array("type" => "numeric", "validate" => "numero", "obl" => "1", "minlength" => "7", "maxlength" => "10", "name" => "agencia[tel_agenci]", "id" => "tel_agenci", "width" => "25%", "value" => $datos->principal->tel_agenci, "end" => true));
					        $mHtml->Label(utf8_decode("Fax:"), "width:25%; :1;");
					        $mHtml->Input(array("type" => "numeric", "validate" => "numero",  "minlength" => "7", "maxlength" => "10", "name" => "transp[num_faxxxx]", "id" => "num_faxxxx", "width" => "25%",  "value" => $datos->principal->num_faxxxx));
					        $mHtml->Label(utf8_decode("E-Mail:"), "width:25%; :1;");
					        $mHtml->Input(array("type" => "email", "validate" => "email",  "minlength" => "10", "maxlength" => "100", "name" => "transp[dir_emailx]", "id" => "dir_emailx", "width" => "25%", "value" => $datos->principal->dir_emailx, "end" => true));
					        $mHtml->CloseTable("tr");
	        		$mHtml->CloseDiv();
	        	$mHtml->CloseDiv();
	        $mHtml->CloseDiv();
	        #fin Acordeon 5

	        $mHtml->OpenDiv("id:DatosSecundariosID;");
	        	$mHtml->Table("tr");
	        	if(!$datos->principal->cod_tercer){
	        		$mHtml->StyleButton("name:send; id:registrarID; value:Registrar; onclick:registrar('registrar'); align:center;  class:crmButton small save");
	        		$mHtml->StyleButton("name:clear; id:borrarID; value:Borrar; onclick:borrar(); align:center;  class:crmButton small save");
	        	}else{
	        		$mHtml->StyleButton("name:send; id:modificarID; value:Actualizar; onclick:confirmar('modificar'); align:center;  class:crmButton small save");
	        		if($datos->principal->cod_estado == 1){
	        			$mHtml->StyleButton("name:inactive; id:inactivarID; value:Inactivar; onclick:confirmar('inactivar'); align:center;  class:crmButton small save");
	        		}else{
	        			$mHtml->StyleButton("name:inactive; id:inactivarID; value:Activar; onclick:confirmar('activar'); align:center;  class:crmButton small save");
	        		}
	        		$mHtml->StyleButton("name:clear; id:cancelarID; value:Cancelar; onclick:closed(); align:center;  class:crmButton small save");
	        	}			       
			        
	        	$mHtml->CloseTable("tr");
	        $mHtml->CloseDiv();

	        $mHtml->CloseDiv();
	        $mHtml->CloseRow("td");
        	# Cierra formulario
        $mHtml->CloseForm();
        # Cierra Body
        $mHtml->CloseBody();

        # Muestra Html
        echo $mHtml->MakeHtml();
    }

    /* ! \fn: listado
     *  \brief: Muestra una lista de las transportadoras existentes en la base de datos
     *  \author: Ing. Alexander Correa
     * 	\date: 07/09/2015
     * 	\date modified: dia/mes/año
     *  \param: 
     *  \return:
     */
    function listado() {
    	
    	$datos = self::$cFunciones->getTrasnportadoras();
        
    }

//FIN FUNCION INSERT_SEDE
}

//FIN CLASE
$proceso = new Ins_config_emptra($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>