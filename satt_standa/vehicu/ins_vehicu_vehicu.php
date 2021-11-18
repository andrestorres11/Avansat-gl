<?php
/*
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);*/
   

     require "ajax_trayle_trayle.php";
 
class Ins_vehicu_vehicu {
   var $conexion, $usuario, $cod_aplica;
    private static $cFunciones, $cTransp;

    function __construct($co, $us, $ca) {
        include_once('../'.DIR_APLICA_CENTRAL.'/inform/class_despac_trans3.php');
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        self::$cFunciones = new trayle($co, $us, $ca);
        self::$cTransp = new Despac($co, $us, $ca);
        switch ($_REQUEST[opcion]) {
            case 1:
              $this->Formulario();
            break;

            case 2:
              $this->imprimir();
            break;

            case 3:
              $this->ExportExcel();
            break;

            default:
                $this->filtro();
            break;
        }
    }

      /*! \fn: filtro
     *  \brief: funcion inicial para buscar una transportadora
     *  \author: Ing. Alexander Correa
     *  \date: 31/09/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    
    function filtro() {

        # Nuevo frame ---------------------------------------------------------------
        # Inicia clase del fromulario ----------------------------------------------------------------------------------
        $mHtml = new FormLib(2);

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
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("functions");
        $mHtml -> SetBody(' <script src="../'.DIR_APLICA_CENTRAL.'/js/ajax_vehicu_vehicu.js"></script> ');
        $mHtml->SetJs("InsertProtocolo");
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
        $mHtml->Form(array("action" => "index.php",
            "method" => "post",
            "name" => "form_search",
            "header" => "Conductores",
            "enctype" => "multipart/form-data"));

      #variables ocultas
      
        $mHtml->Hidden(array( "name" => "cod_tercer", "id" => "cod_tercerID", 'value'=>$mCodTransp));
        $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
        $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
        $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
        $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));
        $mHtml->Hidden(array( "name" => "cod_transp", "id" => "cod_transpID", 'value'=>$mCodTransp));
        $mHtml->Hidden(array( "name" => "placa", "id" => "placa", 'value'=>$_REQUEST['placa']));
        $mHtml->Hidden(array( "name" => "resultado", "id" => "resultado", 'value'=>$_REQUEST["resultado"]));
        $mHtml->Hidden(array( "name" => "opera", "id" => "opera", 'value'=>$_REQUEST['operacion']));
        $mHtml->Hidden(array( "name" => "total", "id" => "total", 'value'=>$total));

      # Construye accordion
        $mHtml->Row("td");
          $mHtml->OpenDiv("id:contentID; class:contentAccordion");
          # Accordion1
          	$mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
	            $mHtml->SetBody("<h1 style='padding:6px'><b>Agregar Vehiculos</b></h1>");
	            $mHtml->OpenDiv("id:sec1;");
	              $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
	                $mHtml->Table("tr");
	                    $mHtml->Label("Transportadora:", "width:35%; :1;");
	                    $mHtml->Input(array("name" => "trasp[nom_transp]", "id" => "nom_transpID", "value" => $mNomTransp, "width" => "35%"));
	                    $mHtml->SetBody("<td><div id='boton'></div></td>");  
	                $mHtml->CloseTable("tr");
	              $mHtml->CloseDiv();
	            $mHtml->CloseDiv();
          	$mHtml->CloseDiv();
          # Fin accordion1    
          # Accordion2
	          $mHtml->OpenDiv("id:datos; class:accordion");
	            $mHtml->SetBody("<h1 style='padding:6px'><b>Listado de Vehiculos</b></h1>");
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

        # Muestra Html
        echo $mHtml->MakeHtml();  
        
    }

    function Formulario() {
        $datos = self::$cFunciones->getDatosVehiculo($_REQUEST['placa']);
        
        # Nuevo frame ---------------------------------------------------------------
        # Inicia clase del fromulario ----------------------------------------------------------------------------------
        $mHtml = new FormLib(2);

        # incluye JS
        $mHtml->SetJs("min");
        $mHtml->SetJs("config");
        $mHtml->SetJs("fecha");
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("functions");
        $mHtml->SetBody(' <script src="../'.DIR_APLICA_CENTRAL.'/js/blockUI.jquery.js"></script> ');
        $mHtml->SetBody(' <script src="../'.DIR_APLICA_CENTRAL.'/js/ajax_vehicu_vehicu.js"></script> ');
        $mHtml->SetJs("InsertProtocolo");
        $mHtml->SetJs("new_ajax"); 
        $mHtml->SetJs("dinamic_list");
        $mHtml->SetJs("validator");
        $mHtml->SetCssJq("validator");
        $mHtml->SetCss("dinamic_list");
        $mHtml->CloseTable("tr");
        # incluye Css
        $mHtml->SetCssJq("jquery");
        # Abre Form
        $mHtml->Form(array("action" => "../".DIR_APLICA_CENTRAL."/vehicu/ajax_trayle_trayle.php",
            "method" => "post",
            "name" => "form_vehicu",
            "header" => "Transportadoras",
            "enctype" => "multipart/form-data"));

        $query = "SELECT cod_paisxx
            FROM ".BASE_DATOS.".tab_tercer_tercer
              WHERE cod_tercer = '".$_REQUEST['cod_tercer']."'
            LIMIT 1";
        $consulta = new Consulta($query, $this->conexion);
        $cod_paisxx = $consulta->ret_matriz("a")[0]['cod_paisxx'];
      #variables ocultas
      $mHtml->Hidden(array( "name" => "vehicu[cod_ciudad]", "id" => "cod_ciudadID", "value"=>$datos->principal->cod_ciudad)); //el codigo de la ciudad
      $mHtml->Hidden(array( "name" => "vehicu[cod_transp]", "id" => "cod_transpID", "value"=>$_REQUEST['cod_tercer'])); //el codigo de la transportadora
      $mHtml->Hidden(array( "name" => "vehicu[cod_paisxx]", "id" => "cod_paisxxID", "value"=>$cod_paisxx)); //el codigo de la transportadora
      $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
      $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
      $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
      $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));
      $mHtml->Hidden(array( "name" => "vehicu[cantidad]", "id" => "cantidadID", 'value'=>count($datos->referencias)));
      $mHtml->Hidden(array( "name" => "vehicu[control]", "id" => "controlID", 'value'=>count($datos->referencias))); //variable para saber si añaden mas experiencias laborales
      $mHtml->Hidden(array( "name" => "abr_terer", "id" => "abr_terer", 'value'=>$datos->principal->abr_tercer));
      $mHtml->Hidden(array( "name" => "imagen", "id" => "imagen", 'value'=>"1"));
      $mHtml->Hidden(array( "name" => "Ajax", "id" => "Ajax", 'value'=>"on"));
      $mHtml->Hidden(array( "name" => "operacion", "id" => "operacion", 'value'=>""));
      $mHtml->Hidden(array( "name" => "url", "id" => "url", 'value'=>"../".NOM_URL_APLICA."/".$datos->principal->dir_ultfot));
      
      $disabled = false;
      if($datos->principal->num_placax){
        $disabled = true;
      }
          # Construye accordion
          $mHtml->Row("td");
          $mHtml->OpenDiv("id:contentID; class:contentAccordion");
            # Accordion1
            $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
              $mHtml->SetBody("<h3 style='padding:6px;'><center>Datos B&aacute;sicos</center></h3>");
            $mHtml->OpenDiv("id:sec1;");
              $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                $mHtml->Table("tr");
                  $mHtml->Label("Placa:", "width:25%; *:1;");
                  $mHtml->Input(array("name" => "vehicu[num_placax]", "id" => "num_placaxID", "onblur"=>"comprobar()", "width" => "10%", "obl" => "1", "minlength" => "6", "maxlength" => "6", "validate" => "placa", "size"=>6, "value" =>  $datos->principal->num_placax, "readonly"=>$disabled));
                  $mHtml->Label("Marca:", "width:25%; *:1;");
                  $mHtml->Select2 ($datos->marcas,  array("name" => "vehicu[cod_marcax]", "validate" => "select", "obl"=>"1", "id" => "cod_marcaxID", "width" => "25%", "key"=> $datos->principal->cod_marcax, "end" => true) );
                  
                  $mHtml->Label("Linea:", "width:25%; *:1;");
                  $mHtml->Select2 ($datos->lineas,  array("name" => "vehicu[cod_lineax]", "validate" => "select", "obl"=>"1", "id" => "cod_lineaxID", "width" => "25%", "key"=> $datos->principal->cod_lineax) );
                  $mHtml->Label(("Modelo:"), "width:25%; *:1;");
                  $mHtml->Input(array("type" => "numeric", "name" => "vehicu[ano_modelo]", "size"=>"4", "id" => "ano_modeloID", "validate" => "numero",  "obl" => "1", "minlength" => "4", "maxlength" => "4", "width" => "100px", "value" => $datos->principal->ano_modelo, "end" => true));

                  $mHtml->Label(("Repotenciado A:"), "width:25%; :1;");
                  $mHtml->Input(array("type" => "numeric", "name" => "vehicu[ano_repote]", "size"=>"6", "validate" => "numero", "minlength" => "4", "maxlength" => "4", "id" => "ano_repoteID", "width" => "25%", "value" => ($datos->principal->ano_repote == 0 ? "":  $datos->principal->ano_repote)));
                  $mHtml->Label("Color:", "width:25%; :1;");
                  $mHtml->Select2 ($datos->colores,  array("name" => "vehicu[cod_colorx]", "validate" => "select",  "id" => "cod_colorxID", "width" => "25%", "key"=> $datos->principal->cod_colorx, "end" => true) );
                  
                  $mHtml->Label(("Tipo de Vinculaci&oacute;n:"), "width:25%; :1;");
                  $mHtml->Select2 ($datos->vinculaciones,  array("name" => "vehicu[cod_tipveh]", "validate" => "select", "id" => "cod_tipvehID", "width" => "25%", "key"=> $datos->principal->cod_tipveh) );
                  $mHtml->Label(("Tipo de Carroceria:"), "width:25%; :1;");
                  $mHtml->Select2 ($datos->carrocerias,  array("name" => "vehicu[cod_carroc]", "validate" => "select", "id" => "cod_carrocID", "width" => "25%", "key"=> $datos->principal->cod_carroc,  "end" => true) );
                  
                  $mHtml->Label("N&uacute;mero de Motor:", "width:25%; *:1;"); 
                  $mHtml->Input (array("name" => "vehicu[num_motorx]", "validate" => "dir",  "obl" => "1", "id" => "num_motorxID",  "minlength" => "6", "maxlength" => "20", "width" => "25%", "value"=> $datos->principal->num_motorx) );
                  $mHtml->Label("N&uacute;mero de Serie:", "width:25%; *:1;");
                  $mHtml->Input (array("name" => "vehicu[num_seriex]", "validate" => "alpha",  "obl" => "1", "id" => "num_seriexID",  "minlength" => "7", "maxlength" => "20", "width" => "25%", "value"=> $datos->principal->num_seriex, "end"=> true) );
                  
				          $mHtml->Label("Peso Vacio (TN):", "width:25%; *:1;"); 
                  $mHtml->Input (array("name" => "vehicu[val_pesove]", "validate" => "numero", "size"=>"4", "obl" => "1", "id" => "val_pesoveID",  "minlength" => "1", "maxlength" => "4", "width" => "25%", "value"=> $datos->principal->val_pesove) );
                  $mHtml->Label("Capacidad (TN):", "width:25%; *:1;");
                  $mHtml->Input (array("name" => "vehicu[val_capaci]", "validate" => "numero", "size"=>"4", "obl" => "1", "id" => "val_capaciID",  "minlength" => "1", "maxlength" => "4", "width" => "25%", "value"=> $datos->principal->val_capaci,  "end" => true) );

                  $mHtml->Label("Configuraci&oacute;n:", "width:25%; *:1;");
                  $mHtml->Select2 ($datos->configuraciones,  array("name" => "vehicu[num_config]", "validate" => "select",  "obl" => "1", "id" => "num_configID", "width" => "25%", "key"=> $datos->principal->num_config,  "end" => true) );
                  
                  $mHtml->Label("Vinculado a:", "width:25%; :1;"); 
                  $mHtml->Input (array("name" => "vehicu[nom_vincul]", "validate" => "alpha", "id" => "nom_vinculID",  "minlength" => "7", "maxlength" => "100", "width" => "25%", "value"=> $datos->principal->nom_vincul) );
                  $mHtml->Label("Fecha de Vencimiento:", "width:25%; :1;");                   
                  $mHtml->Input(array("type" => "date", "name" => "vehicu[fec_vigvin]", "size"=>"10", "id" => "fec_vigvinID", "minlength" => "7", "maxlength" => "10", "width" => "25%", "value" => $datos->principal->fec_vigvin,  "end" => true));
				
				          $mHtml->Label("Revisi&oacute;n Tecno Mecanica:", "width:25%; *:1;"); 
                  $mHtml->Input (array("name" => "vehicu[num_agases]", "validate" => "numero", "obl"=> "1", "id" => "num_agasesID",  "minlength" => "7", "maxlength" => "10", "width" => "25%", "value"=> $datos->principal->num_agases) );
                  $mHtml->Label("Fecha de Vencimiento:", "width:25%; *:1;");                   
                  $mHtml->Input(array("type" => "date", "name" => "vehicu[fec_vengas]", "size"=>"10", "obl"=> "1", "id" => "fec_vengasID", "validate"=>"date", "minlength" => "10", "maxlength" => "10", "width" => "25%", "value" => $datos->principal->fec_vengas,  "end" => true));
				
				          $mHtml->Label("Licencia de Tr&aacute;nsito:", "width:25%; *:1;"); 
                  $mHtml->Input (array("name" => "vehicu[num_tarpro]", "validate" => "numero", "obl"=> "1", "id" => "num_tarproID",  "minlength" => "8", "maxlength" => "12", "width" => "25%", "value"=> $datos->principal->num_tarpro) );
                  $mHtml->Label("Calificaci&oacute;n:", "width:25%; *:1;");
                  $mHtml->Select2 ($datos->calificaciones,  array("name" => "vehicu[cod_califi]", "obl"=>"1", "validate" => "select",  "id" => "cod_califiID", "width" => "25%", "key"=> $datos->principal->cod_califi, "end" => true) );
                   
                $mHtml->CloseTable("tr");
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
          # Fin accordion1

          # Accordion2
          $mHtml->OpenDiv("id:GPSinfoID; class:accordion");
          $mHtml->SetBody("<h3 style='padding:6px;'><center>Informaci�n GPS</center></h3>");
          $mHtml->OpenDiv("id:sec8");
            $mHtml->OpenDiv("id:form8; class:contentAccordionForm");
              $mHtml->Table("tr");

                $mHtml->Label(("Operador GPS:"), "width:25%; *:1;"); 
                $mHtml->Select2 ($datos->opegps,  array("name" => "vehicu[cod_opegps]", "validate" => "select", "id" => "cod_opegpsID", "width" => "25%", "key"=> $datos->principal->cod_opegps, 'onchange' => 'validaIdGPS(this)') );
                
                $mHtml->Label("Usuario:", "width:25%; *:1;"); 
                $mHtml->Input (array("name" => "vehicu[usr_gpsxxx]", "validate" => "alpha",  "id" => "usr_gpsxxxID",  "minlength" => "3", "maxlength" => "50", "width" => "25%", "value"=> $datos->principal->usr_gpsxxx,"end" => true) );
                
                $mHtml->Label("Clave:", "width:25%; *:1;"); 
                $mHtml->Input (array("name" => "vehicu[clv_gpsxxx]", "size"=>"10", "id" => "clv_gpsxxxID",  "width" => "25%", "value"=> $datos->principal->clv_gpsxxx) ); 

                $mHtml->Label("ID:", "width:25%; *:1;"); 
                $mHtml->Input (array("name" => "vehicu[idx_gpsxxx]", "validate" => "alpha","minlength" => "1", "maxlength" => "15", "size"=>"10", "id" => "idx_gpsxxxID",  "width" => "25%", "value"=> $datos->principal->idx_gpsxxx, "end" => true) );
              
              $mHtml->CloseTable("tr");
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        #Fin accordion2

          # Accordion2
          $mHtml->OpenDiv("id:SegurosID; class:accordion");
          $mHtml->SetBody("<h3 style='padding:6px;'><center>Seguros</center></h3>");
          $mHtml->OpenDiv("id:sec2");
            $mHtml->OpenDiv("id:form2; class:contentAccordionForm");
              $mHtml->Table("tr");
                $mHtml->Label(("SOAT:"), "width:25%; *:1;"); 
                  $mHtml->Input (array("name" => "vehicu[num_poliza]", "validate" => "numero", "obl"=> "1", "id" => "num_polizaID",  "minlength" => "3", "maxlength" => "20", "width" => "25%", "value"=> $datos->principal->num_poliza) );
                $mHtml->Label("Aseguradora:", "width:25%; *:1;"); 
                  $mHtml->Input (array("name" => "vehicu[nom_asesoa]", "validate" => "alpha", "obl"=> "1", "id" => "nom_asesoaID",  "minlength" => "3", "maxlength" => "20", "width" => "25%", "value"=> $datos->principal->nom_asesoa) );
                $mHtml->Label("Fecha de Vencimiento:", "width:25%; *:1;"); 
                  $mHtml->Input (array("type" => "date", "name" => "vehicu[fec_vigfin]", "validate" => "date", "obl"=> "1", "size"=>"10", "id" => "fec_vigfinID",  "minlength" => "10", "maxlength" => "10", "width" => "25%", "value"=> $datos->principal->fec_vigfin, "end" => true) );
              $mHtml->CloseTable("tr");
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
          # Fin accordion2
          # Accordion3
          $mHtml->OpenDiv("id:remolquesID; class:accordion");
          $mHtml->SetBody("<h3 style='padding:6px;'><center>Selecci&oacute;n del Remolque</center></h3>");
            $mHtml->OpenDiv("id:sec3");
              $mHtml->OpenDiv("id:form3; class:contentAccordionForm");
                $mHtml->Table("tr");
                  $mHtml->Label("Remolque:", "width:25%; :1;");
                  $mHtml->Select2 ($datos->remolques,  array("name" => "vehicu[num_trayle]", "validate" => "select",  "id" => "num_trayleID", "width" => "25%", "key"=> $datos->principal->num_trayle, "end" => true) );
                $mHtml->CloseTable("tr");
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
          # fin Accordion 3
          #Acordeon 4
          $mHtml->OpenDiv("id:personasID; class:accordion");
          $mHtml->SetBody("<h3 style='padding:6px;'><center>Datos Personas</center></h3>");
          $mHtml->OpenDiv("id:sec4");
            $mHtml->OpenDiv("id:form4; class:contentAccordionForm");
              $mHtml->Table("tr");
                  $mHtml->Label("Poseedor:", "width:25%; *:1;");
                  $mHtml->Input(array("type" => "text", "obl" => "1", "readonly" =>true, "id"=>"cod_tenedo", "validate" => "alpha", "name" => "vehicu[cod_tenedo]", "width" => "25%","minlength" => "6", "maxlength" => "10", "value" => $datos->principal->cod_tenedo));
                  $mHtml->Input(array("type" => "text", "readonly" => true, "id"=>"nom_poseed",  "name" => "vehicu[nom_poseed]", "width" => "25%", "maxlength" => "50", "value" => $datos->principal->nom_poseed));
                  $mHtml->SetBody("<td><img src='../".DIR_APLICA_CENTRAL."/imagenes/search.png' width='16px' height='16px' onclick='getTercerTransp(6)' style='cursor:pointer'/></td></tr>");

                  $mHtml->Label("Propietario:", "width:25%; *:1;");
                  $mHtml->Input(array("type" => "text", "obl" => "1", "readonly" =>true, "id"=>"cod_propie", "validate" => "alpha", "name" => "vehicu[cod_propie]", "width" => "25%","minlength" => "6", "maxlength" => "10", "value" => $datos->principal->cod_propie));
                  $mHtml->Input(array("type" => "text",  "readonly" => true, "id"=>"nom_propie",  "name" => "vehicu[nom_propie]", "width" => "25%", "maxlength" => "50", "value" => $datos->principal->nom_propie));
                  $mHtml->SetBody("<td><img src='../".DIR_APLICA_CENTRAL."/imagenes/search.png' width='16px' height='16px' onclick='getTercerTransp(5)' style='cursor:pointer'/></td></tr>");

                  $mHtml->Label("Conductor:", "width:25%; *:1;");
                  $mHtml->Input(array("type" => "text", "obl" => "1", "readonly" =>true, "id"=>"cod_conduc", "validate" => "alpha", "name" => "vehicu[cod_conduc]", "width" => "25%","minlength" => "6", "maxlength" => "10", "value" => $datos->principal->cod_conduc));
                  $mHtml->Input(array("type" => "text",  "readonly" => true, "id"=>"nom_conduc", "name" => "vehicu[nom_conduc]", "width" => "25%", "maxlength" => "50", "value" => $datos->principal->nom_conduc));
                  $mHtml->SetBody("<td><img src='../".DIR_APLICA_CENTRAL."/imagenes/search.png' width='16px' height='16px' onclick='getTercerTransp(4)' style='cursor:pointer'/></td></tr>");
              $mHtml->CloseTable("tr");
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
          #fin Acordeon 4
        #Acordeon 5
        $mHtml->OpenDiv("id:DatosSecundariosID; class:accordion");
        $mHtml->SetBody("<h3 style='padding:6px;'><center>Fotos del Veh&iacute;culo</center></h3>");
          $mHtml->OpenDiv("id:sec5");
            $mHtml->OpenDiv("id:form5; class:contentAccordionForm");
              $mHtml->Table("tr");
                $mHtml->Label("Foto Frente:", "width:25%; :1;");
                $mHtml->File(array("name" => "fotoFrente",  "id" => "fotoFrente", "validate"=>"file", "format"=>"jpg,jpeg,png", "width" => "25%") );
                $mHtml->Label("Foto izquierda:", "width:25%; :1;");
                $mHtml->File(array("name" => "fotoIzquierda",  "id" => "fotoIzquierda", "validate"=>"file", "format"=>"jpg,jpeg,png", "width" => "25%", "end" => true) );
                $mHtml->Label("Foto Derecha:", "width:25%; :1;");
                $mHtml->File(array("name" => "fotoDerecha",  "id" => "fotoDerecha", "validate"=>"file", "format"=>"jpg,jpeg,png", "width" => "25%") );
                $mHtml->Label("Foto Posterior:", "width:25%; :1;");
                $mHtml->File(array("name" => "fotoPosterior",  "id" => "fotoPosterior", "validate"=>"file", "format"=>"jpg,jpeg,png", "width" => "25%", "end" => true) );
              $mHtml->CloseTable("tr");
            $mHtml->CloseDiv(); 
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        #fin Acordeon 5
       

        if($datos->principal->num_placax){
            #Acordeon 7 Para mostrar la foto del coductor
            $class = ""; #para voltear la imagen laterial si no viene.
            #valida la url de las imagenes, si no muestra una por default;
            if($datos->principal->dir_fotfre != "NULL" && $datos->principal->dir_fotfre != "" && $datos->principal->dir_fotfre != null){
              $src1 = '../'.NOM_URL_APLICA.'/'.URL_VEHICU.$datos->principal->dir_fotfre;
            }else{
              $src1 = "../".DIR_APLICA_CENTRAL."/imagenes/frontal.png";
            }
            if($datos->principal->dir_fotizq != "NULL" && $datos->principal->dir_fotizq != "" && $datos->principal->dir_fotizq != null){
              $src2 = '../'.NOM_URL_APLICA.'/'.URL_VEHICU.$datos->principal->dir_fotizq;
            }else{
              $src2 = "../".DIR_APLICA_CENTRAL."/imagenes/lateral.png";
            }
            if(($datos->principal->dir_fotder != "NULL" && $datos->principal->dir_fotder != "" && $datos->principal->dir_fotder != null)){
              $src3 = '../'.NOM_URL_APLICA.'/'.URL_VEHICU.$datos->principal->dir_fotder;
            }else{
              $class = "class='rotar'";
              $src3 = "../".DIR_APLICA_CENTRAL."/imagenes/lateral.png";
            }
            if($datos->principal->dir_fotpos != "NULL" && $datos->principal->dir_fotpos != "" && $datos->principal->dir_fotpos != null){
              $src4 = '../'.NOM_URL_APLICA.'/'.URL_VEHICU.$datos->principal->dir_fotpos;
            }else{
              $src4 = "../".DIR_APLICA_CENTRAL."/imagenes/posterior.png";
            }
         
          $mHtml->OpenDiv("id:fotoID; class:accordion");
          $mHtml->SetBody("<h3 style='padding:6px;'><center>Fotos del Veh&iacute;culo</center></h3>");
            $mHtml->OpenDiv("id:sec7");
              $mHtml->OpenDiv("id:form7; class:contentAccordionForm");
                $mHtml->Table("tr");
                  $mHtml->SetBody("<td><div style='text-align:center; '><label>Foto Frontal</label><br><img style='cursor:pointer;' width='120px' onclick='imagen(\"".$src1."\")' height='120px' src='$src1'/></img></div></td>");
                  $mHtml->SetBody("<td><div style='text-align:center; '><label>Foto Izquierda</label><br><img style='cursor:pointer;' $class width='120px' onclick='imagen(\"".$src2."\")' height='120px' src='$src2'/></img></div></td>");
                  $mHtml->SetBody("<td><div style='text-align:center; '><label>Foto Derecha</label><br><img style='cursor:pointer;'  width='120px' onclick='imagen(\"".$src3."\")' height='120px' src='$src3'/></img></div></td>");
                  $mHtml->SetBody("<td><div style='text-align:center; '><label>Foto Posterior</label><br><img style='cursor:pointer;' width='120px' onclick='imagen(\"".$src4."\")' height='120px' src='$src4'/></img></div></td>");
                $mHtml->CloseTable("tr");
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
          #fin Acordeon 7
         }

        $mHtml->OpenDiv("id:DatosSecundariosID;");
          $mHtml->Table("tr");
          if(!$datos->principal->num_placax){
            $mHtml->StyleButton("name:send; id:registrarID; value:Registrar; onclick:confirmar('registrarVehiculo'); align:center;  class:crmButton small save");
            $mHtml->StyleButton("name:clear; id:borrarID; value:Borrar; onclick:borrar(); align:center;  class:crmButton small save");
          }else{
            $mHtml->StyleButton("name:send; id:modificarID; value:Actualizar; onclick:confirmar('modificarVehiculo'); align:center;  class:crmButton small save");
            
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
      header("Content-Disposition: attachment; filename=Vehiculos".$date.".xls");
      header("Pragma: no-cache");
      header("Expires: 0");
      ob_clean();
      echo "<table>";
      echo "  <tr>";
      echo "     <th>Placa</th>";
      echo "     <th>Poseedor</th>";
      echo "     <th>Telefono</th>";
      echo "     <th>Celular</th>";
      echo "     <th>Marca</th>";
      echo "     <th>Linea</th>";
      echo "     <th>Color</th>";
      echo "     <th>Carroceria</th>";
      echo "     <th>Modelo</th>";
      echo "     <th>Estado</th>";
      echo "  </tr>";
      foreach ($mData as $key => $value) 
      {
        echo "  <tr>";
        echo "     <td>".$value["num_placax"]."</td>";
        echo "     <td>".$value["abr_tercer"]."</td>";
        echo "     <td>".$value["num_telef1"]."</td>";
        echo "     <td>".$value["num_telmov"]."</td>";
        echo "     <td>".$value["nom_marcax"]."</td>";
        echo "     <td>".$value["nom_lineax"]."</td>";
        echo "     <td>".$value["nom_colorx"]."</td>";
        echo "     <td>".$value["nom_carroc"]."</td>";
        echo "     <td>".$value["ano_modelo"]."</td>";
        echo "     <td>".$value["cod_estado"]."</td>";
        echo "  </tr>";
      }

    } 

//FIN FUNCION INSERT_SEDE
}

//FIN CLASE
$proceso = new Ins_vehicu_vehicu($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
?>