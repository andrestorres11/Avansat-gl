<?php
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);
   

     require_once "ajax_trayle_trayle.php";
 
class Ins_trayle_trayle {
   var $conexion, $usuario, $cod_aplica;
    private static $cFunciones,  $cTransp;

    function __construct($co, $us, $ca) {
        include_once('../'.DIR_APLICA_CENTRAL.'/inform/class_despac_trans3.php');
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        self::$cFunciones = new trayle($co, $us, $ca);
        self::$cTransp = new Despac($co, $us, $ca);
        switch ($GLOBALS[opcion]) { 
            case 1:
              $this->Formulario();
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
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("functions");
        $mHtml->SetJs("validator");
        $mHtml->SetCssJq("validator");
        $mHtml->SetJs("new_ajax"); 
        $mHtml->SetJs("dinamic_list");
        $mHtml->SetCss("dinamic_list");  
 
        # incluye Css
        $mHtml->SetCssJq("jquery");
        $mHtml->Body(array("menubar" => "no")); 

        $mHtml -> SetBody(' <script src="../'.DIR_APLICA_CENTRAL.'/js/ajax_trayle_trayle.js"></script> ');

        # Abre Form
        $mHtml->Form(array("action" => "index.php",
            "method" => "post",
            "name" => "form_search",
            "header" => "Terceros" ));

      #variables ocultas
      
        $mHtml->Hidden(array( "name" => "cod_tercer", "id" => "cod_tercerID", 'value'=>$mCodTransp));
        $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
        $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
        $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
        $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));
        $mHtml->Hidden(array( "name" => "cod_transp", "id" => "cod_transpID", 'value'=>$mCodTransp));
        $mHtml->Hidden(array( "name" => "nom_tercer", "id" => "nom_tercerID", 'value'=>''));
        $mHtml->Hidden(array( "name" => "resultado", "id" => "resultado", 'value'=>$_REQUEST['resultado']));
        $mHtml->Hidden(array( "name" => "opera", "id" => "opera", 'value'=>$_REQUEST['operacion']));
        $mHtml->Hidden(array( "name" => "conductor", "id" => "conductor", 'value'=>$_REQUEST['conductor']));
        $mHtml->Hidden(array( "name" => "total", "id" => "total", 'value'=>$total));

          # Construye accordion
          $mHtml->Row("td");
            $mHtml->OpenDiv("id:contentID; class:contentAccordion");
              # Accordion1
              $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
                $mHtml->SetBody("<h2 class='fuente'><center>Remolques</center></h2>");
                $mHtml->OpenDiv("id:sec1;");
                  $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                    $mHtml->Table("tr");
                        $mHtml->Label("Transportadora:", "width:35%; *:1;");
                        $mHtml->Input(array("name" => "trasp[nom_transp]", "id" => "nom_transpID", "value" => $mNomTransp, "width" => "35%"));
                        $mHtml->SetBody("<td><div id='boton'></div></td>");  
                    $mHtml->CloseTable("tr");
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();
              $mHtml->CloseDiv();
              # Fin accordion1    
              # Accordion2
              $mHtml->OpenDiv("id:datos; class:accordion");
                $mHtml->SetBody("<h2 class='fuente'><center>Listado de Remolques</center></h2>");
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

        $datos = self::$cFunciones->getDatosTrayler($_REQUEST['cod_tercer']);

       /* echo "<pre>";
        print_r($datos->principal);
        die;*/
        
        # Nuevo frame ---------------------------------------------------------------
        # Inicia clase del fromulario ----------------------------------------------------------------------------------
        $mHtml = new FormLib(2);

        # incluye JS
        $mHtml->SetJs("min");
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("functions");
        $mHtml->SetJs("config");
        $mHtml->SetJs("fecha");
        $mHtml -> SetBody(' <script src="../'.DIR_APLICA_CENTRAL.'/js/ajax_trayle_trayle.js"></script> ');
        $mHtml->SetJs("validator");
        $mHtml->SetCssJq("validator");    
        # incluye Css
        $mHtml->SetCssJq("jquery");

        # coloca titulo
        $mHtml->SetTitle("Crear Transportadora");

        # Abre Body
        $mHtml->Body(array("menubar" => "no"));

        # Abre Form
        $mHtml->Form(array("action" => "../".DIR_APLICA_CENTRAL."/vehicu/ajax_trayle_trayle.php",
            "method" => "post",
            "name" => "form_transpor",
            "header" => "Transportadoras",
            "enctype" => "multipart/form-data"));

      #variables ocultas
      $mHtml->Hidden(array( "name" => "trayle[cod_tercer]", "id" => "cod_agenciID", "value"=>$_REQUEST['cod_tercer'])); //el codigo de la transportadora para el trayler
      $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
      $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
      $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
      $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));
      $mHtml->Hidden(array( "name" => "imagen", "id" => "imagen", 'value'=>"1"));
      $mHtml->Hidden(array( "name" => "Ajax", "id" => "Ajax", 'value'=>"on"));
      $mHtml->Hidden(array( "name" => "operacion", "id" => "operacion", 'value'=>""));
      $mHtml->Hidden(array( "name" => "dueno", "id" => "dueno", 'value'=>$datos->principal->nom_propie));
      $mHtml->Hidden(array( "name" => "url", "id" => "url", 'value'=>"../".NOM_URL_APLICA."/".$datos->principal->dir_fottra));

      # Construye accordion
      $mHtml->Row("td");
      $mHtml->OpenDiv("id:contentID; class:contentAccordion");
        # Accordion1
        $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
        $mHtml->SetBody("<h3 style='padding:6px;'><center>Información Básica</center></h3>");
        $mHtml->OpenDiv("id:sec1;");
          $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
            $mHtml->Table("tr");
              $mHtml->Label("Número de Remolque:", "width:25%; *:1;");
               if($datos->principal->num_trayle){
                $mHtml->Input(array( "name" => "trayle[num_trayle]", "id" => "num_trayleID", "width" => "25%", "maxlength" => "6", "minlength" => "6",  "validate" => "alpha", "obl" => "1", "value" => $datos->principal->num_trayle, "disabled" => true));
              }else{
                $mHtml->Input(array( "name" => "trayle[num_trayle]", "id" => "num_trayleID", "width" => "25%", "maxlength" => "6", "minlength" => "6", "onblur"=>"comprobar()",  "validate" => "alpha", "obl" => "1", "value" =>""));
              }
              $mHtml->Label("Marca:", "width:25%; *:1;");
              $mHtml->Select2 ($datos->marcas,  array("name" => "trayle[cod_marcax]", "validate" => "select",  "obl" => "1", "id" => "cod_marcaxID", "width" => "25%", "key"=> $datos->principal->cod_marcax, "end"=>true) );
              
              $mHtml->Label("Modelo:", "width:25%; :1;");
              $mHtml->Input(array("type" => "numeric", "name" => "trayle[ano_modelo]", "validate" => "numero",  "minlength" => "4", "maxlength" => "4", "id" => "ano_modeloID", "width" => "25%", "value" => $datos->principal->ano_modelo));
              $mHtml->Label("Configuración:", "width:25%; :1;");
              $mHtml->Select2 ($datos->configuraciones,  array("name" => "trayle[cod_config]", "validate" => "select", "id" => "cod_configID", "width" => "25%", "key"=> $datos->principal->cod_config, "end"=>true) );
              
              $mHtml->Label("Peso Vacio (TN):", "width:25%; *:1;");
              $mHtml->Input(array("name" => "trayle[tra_pesoxx]", "validate" => "numero", "obl" => "1", "minlength" => "1",   "maxlength" => "4", "id" => "tra_pesoxxID", "width" => "25%", "value" => $datos->principal->tra_pesoxx));
              $mHtml->Label("Capacidad (TN):", "width:25%; *:1;");
              $mHtml->Input(array("name" => "trayle[tra_capaci]", "id" => "tra_capaciID", "validate" => "numero", "minlength" => "1", "maxlength" => "4",  "obl" => "1", "width" => "25%", "value" => $datos->principal->tra_capaci, "end" => true));

              $mHtml->Label("Ancho (M):", "width:25%; :1;");
              $mHtml->Input(array( "name" => "trayle[tra_anchox]", "validate" => "numero",  "minlength" => "1", "maxlength" => "4", "id" => "tra_anchoxID", "width" => "25%", "value" => $datos->principal->tra_anchox));
              $mHtml->Label("Alto (M):", "width:25%; :1;");
              $mHtml->Input(array("type" => "numeric", "name" => "trayle[tra_altoxx]", "validate" => "numero",  "minlength" => "1", "maxlength" => "4", "id" => "tra_altoxxID", "width" => "25%", "value" => $datos->principal->tra_altoxx, "end" => true));

              $mHtml->Label("largo (M):", "width:25%; :1;"); 
              $mHtml->Input(array("type" => "numeric", "name" => "trayle[tra_largox]", "validate" => "numero",  "minlength" => "1", "maxlength" => "4", "id" => "tra_largoxID", "width" => "25%", "value" => $datos->principal->tra_largox));
              $mHtml->Label("Volumen Posterior:", "width:25%; :1;"); 
              $mHtml->Input(array("name" => "trayle[tra_volpos]", "validate" => "alpha",  "minlength" => "1", "maxlength" => "50", "id" => "tra_volposID", "width" => "25%", "value" => $datos->principal->tra_volpos, "end" => true));

              $mHtml->Label("Tipo de Tramite:", "width:25%; :1;"); 
              $mHtml->Input(array("name" => "trayle[tip_tramit]", "validate" => "dir",  "minlength" => "4", "maxlength" => "10", "id" => "tip_tramitID", "width" => "25%", "value" => $datos->principal->tip_tramit));
              $mHtml->Label("Carroceria:", "width:25%; :1;"); 
              $mHtml->Select2 ($datos->carrocerias,  array("name" => "trayle[cod_carroc]", "validate" => "select", "id" => "cod_carrocID", "width" => "25%", "key"=> $datos->principal->cod_carroc, "end"=>true) );
              
              $mHtml->Label("Serie Chasis:", "width:25%; :1;"); 
              $mHtml->Input(array("name" => "trayle[ser_chasis]", "validate" => "alpha",  "minlength" => "5", "maxlength" => "20", "id" => "ser_chasisID", "width" => "25%", "value" => $datos->principal->ser_chasis));
              $mHtml->Label("Propietario:", "width:25%; :1;"); 
              $mHtml->Input(array("name" => "trayle[nom_propie]", "validate" => "alpha",  "minlength" => "3", "maxlength" => "50", "id" => "nom_propieID", "width" => "25%", "value" => $datos->principal->nom_propie, "end" => true));
              
              $mHtml->Label("Color:", "width:25%; :1;"); 
              $mHtml->Select2 ($datos->colores,  array("name" => "trayle[cod_colore]", "validate" => "select", "id" => "cod_coloreID", "width" => "25%", "key"=> $datos->principal->cod_colore) );
              $mHtml->Label("Foto Trayler:", "width:25%; :1;");
              $mHtml->File(array("name" => "foto",  "id" => "foto", "validate"=>"file", "format"=>"jpg,jpeg,png", "width" => "25%", "end" => true) );
                
            $mHtml->CloseTable("tr");
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
      $mHtml->CloseDiv();
      # Fin accordion1 
      if($datos->principal->dir_fottra != "NULL" && $datos->principal->dir_fottra != "" && $datos->principal->dir_fottra != null){
          #Acordeon 7 Para mostrar la foto del trayler
        $mHtml->OpenDiv("id:fotoID; class:accordion");
        $mHtml->SetBody("<h3 style='padding:6px;'><center>Foto del Trayler</center></h3>");
          $mHtml->OpenDiv("id:sec7");
            $mHtml->OpenDiv("id:form7; class:contentAccordionForm");
              $mHtml->Table("tr");
                $mHtml->SetBody("<div style='text-align:center; '><img style='cursor:pointer;' width='120px' onclick='imagen()' height='120px' src='../".NOM_URL_APLICA."/".$datos->principal->dir_fottra."'/></img></div>");
              $mHtml->CloseTable("tr");
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        #fin Acordeon 7
        }
      $mHtml->OpenDiv("id:DatosSecundariosID;");
        $mHtml->Table("tr");
        if(!$datos->principal->num_trayle){
          $mHtml->StyleButton("name:send; id:registrarID; value:Registrar; onclick:registrar('registrar'); align:center;  class:crmButton small save");
          $mHtml->StyleButton("name:clear; id:borrarID; value:Borrar; onclick:borrar(); align:center;  class:crmButton small save");
        }else{
          $mHtml->StyleButton("name:send; id:modificarID; value:Actualizar; onclick:confirmar('modificar'); align:center;  class:crmButton small save");
         
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
//FIN FUNCION INSERT_SEDE
}

//FIN CLASE
$proceso = new Ins_trayle_trayle($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
?>