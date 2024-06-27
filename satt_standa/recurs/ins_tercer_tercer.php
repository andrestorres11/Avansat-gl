<?php
    //ini_set('display_errors', true);
    //error_reporting(E_ALL & ~E_NOTICE);
   

     require_once "ajax_tercer_tercer.php";
 
class Ins_tercer_tercer {
   var $conexion, $usuario, $cod_aplica;
    private static $cFunciones, $cTransp;

    function __construct($co, $us, $ca) {
        include_once('../'.DIR_APLICA_CENTRAL.'/inform/class_despac_trans3.php');
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        self::$cFunciones = new tercer($co, $us, $ca);
        self::$cTransp = new Despac($co, $us, $ca);
        switch ($_REQUEST[opcion]) { 
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
     *  \date modified: dia/mes/a�o
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

        $mHtml -> SetBody(' <script src="../'.DIR_APLICA_CENTRAL.'/js/ajax_tercer_tercer.js?v=00999"></script> ');

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
        $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));
        $mHtml->Hidden(array( "name" => "cod_transp", "id" => "cod_transpID", 'value'=>$mCodTransp));
        $mHtml->Hidden(array( "name" => "cod_agenci", "id" => "cod_agenciID", 'value'=>''));
        $mHtml->Hidden(array( "name" => "nom_tercer", "id" => "nom_tercerID", 'value'=>''));
        $mHtml->Hidden(array( "name" => "total", "id" => "total", 'value'=>$total));

          # Construye accordion
          $mHtml->Row("td");
            $mHtml->OpenDiv("id:contentID; class:contentAccordion");
              # Accordion1
              $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
                $mHtml->SetBody("<h1 style='padding:6px'><b>INSERTAR TERCEROS</b></h1>");
                $mHtml->OpenDiv("id:sec1");
                  $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                    $mHtml->Table("tr");
                        $mHtml->Label("Transportadora:", "width:35%; *:1;");
                        $mHtml->Input(array("name" => "trasp[nom_transp]", "id" => "nom_transpID", "width" => "35%"));
                        $mHtml->SetBody("<td><div id='boton'></div></td>");  
                    $mHtml->CloseTable("tr");
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();
              $mHtml->CloseDiv();
              # Fin accordion1    
              # Accordion2
              $mHtml->OpenDiv("id:datos; class:accordion");
                $mHtml->SetBody("<h1 style='padding:6px' ><b>LISTAOD DE TEREROS</b></h1>");
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
        $datos = self::$cFunciones->getDatosTerero($_REQUEST['cod_agenci'], $_REQUEST['cod_tercer']);
        var_dump($datos->principal->cod_contro);
        
        # Nuevo frame ---------------------------------------------------------------
        # Inicia clase del fromulario ----------------------------------------------------------------------------------
        $mHtml = new FormLib(2);
         #echo "<pre>"; print_r($datos);  die;
        # incluye JS
        $mHtml->SetJs("min");
        $mHtml->SetJs("config");
        $mHtml->SetJs("fecha");
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("functions");
        $mHtml -> SetBody(' <script src="../'.DIR_APLICA_CENTRAL.'/js/ajax_tercer_tercer.js?v=00999"></script> ');
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
        $mHtml->Form(array("action" => "index.php",
            "method" => "post",
            "name" => "form_transpor",
            "header" => "Transportadoras",
            "enctype" => "multipart/form-data"));

      $query = "SELECT a.cod_paisxx, b.nom_paisxx
            FROM ".BASE_DATOS.".tab_tercer_tercer a
            LEFT JOIN ".BASE_DATOS.".tab_genera_paises b ON a.cod_paisxx = b.cod_paisxx 
              WHERE cod_tercer = '".$_REQUEST['cod_tercer']."'
            LIMIT 1";
      $consulta = new Consulta($query, $this->conexion);

      if($datos->principal->cod_paisxx=='' || $datos->principal->nom_paisxx==NULL){
        $cod_paisxx = $consulta->ret_matriz("a")[0]['cod_paisxx'];
        $nom_paisxx = $consulta->ret_matriz("a")[0]['nom_paisxx'];
      }else{
        $cod_paisxx = $datos->principal->cod_paisxx;
        $nom_paisxx = $datos->principal->nom_paisxx;
      }

      $pai_config = self::$cFunciones->darPaisConfig();

      if($cod_paisxx==''){
        $cod_paisxx = ($pai_config['cod_paisxx'] != '' || $pai_config['cod_paisxx'] != NULL) ? $pai_config['cod_paisxx'] : '';
      }
      if($nom_paisxx==''){
        $nom_paisxx = ($pai_config['nom_paisxx'] != '' || $pai_config['nom_paisxx'] != NULL || $datos->principal->nom_paisxx == NULL || $datos->principal->nom_paisxx == '') ? strtoupper($pai_config['nom_paisxx']) : '';
      }

      #variables ocultas
      $mHtml->Hidden(array( "name" => "tercer[cod_ciudad]", "id" => "cod_ciudadID", "value"=>$datos->principal->cod_ciudad)); //el codigo de la ciudad
      $mHtml->Hidden(array( "name" => "tercer[cod_transp]", "id" => "cod_transpID", "value"=>$_REQUEST['cod_tercer'])); //el codigo de la transportadora
      $mHtml->Hidden(array( "name" => "tercer[cod_paisxx]", "id" => "cod_paisxxID", "value"=>$cod_paisxx)); //el codigo de la transportadora
      $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
      $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
      $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
      $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));
      $mHtml->Hidden(array( "name" => "abr_tercer", "id" => "abr_tercer", 'value'=>$datos->principal->abr_tercer));
      $mHtml->Hidden(array( "name" => "", "id" => "cod_tipdoc", 'value'=>$datos->principal->cod_tipdoc));
      $disables = "";

      $query = "SELECT cod_tipdoc
        FROM ".BASE_DATOS.".tab_genera_tipdoc
          WHERE ind_person = 1";
      $consulta = new Consulta($query, $this->conexion);
      $tipdocs = $consulta->ret_matriz("a");

      
      if($datos->principal->cod_tipdoc == "N"){
          $persona = 1;
      }else{
          $persona = 2;
      }
      
      if($datos->principal->cod_tercer){
        $disabled = "'disabled'=>true";
      }

      $sql = "SELECT a.cod_contro, a.nom_contro FROM tab_genera_contro a 
              WHERE a.ind_estado =  '1' AND cod_tpcont = 4 ";
      $consulta = new Consulta($sql, $this->conexion);
      $tipContro = $consulta->ret_matriz("a");

      $inicio[0][0]= "0";
      $inicio[0][1]= "-";
      $tipContro = array_merge($inicio,$tipContro);
      
      /*echo "<pre>";
      echo $disabled;
      print_r($datos->principal->cod_tercer);die;*/
          # Construye accordion
          $mHtml->Row("td");
          $mHtml->OpenDiv("id:contentID; class:contentAccordion");
            # Accordion1
            $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
              $mHtml->SetBody("<h3 style='padding:6px;'><center>Tipo de Tercero</center></h3>");
              $mHtml->OpenDiv("id:sec1;");
                $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                  $mHtml->Table("tr");
                    $mHtml->Label((""), "width:18%; :1;");
                    $mHtml->Select2 ($datos->tipoTercero,  array("name" => "tercer[cod_tipter]", "validate" => "select", "onchange"=>"verificar()", "obl" => "1", "id" => "cod_tipterID", "width" => "25%", "key"=> $persona) );
                  $mHtml->CloseTable("tr");
                $mHtml->CloseDiv();
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          # Fin accordion1

           $mHtml->OpenDiv("id:pintar");
           $mHtml->CloseDiv();
          # Accordion2

            $mHtml->OpenDiv("id:juridico");
              $mHtml->OpenDiv("id:juridicaID; class:accordion");
                $mHtml->SetBody("<h3 style='padding:6px;'><center>Persona Jurídica</center></h3>");
                $mHtml->OpenDiv("id:sec2");
                  $mHtml->OpenDiv("id:form2; class:contentAccordionForm");
                    $mHtml->Table("tr");
                      $mHtml->Label("Pais:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "pais", "validate"=>"dir", "id" => "paisID", "width" => "25%", "minlength"=>"7", "maxlength" => "40", "value" => $nom_paisxx, "end" => true, "onclick"=>"limpiarInput(this)" ));
                      $mHtml->Label(("Número de :"), "width:25%; *:1; id:tip_docempID");
                      $mHtml->Input (array("name" => "tercer[cod_tercer]", "validate" => "numero",  "id" => "cod_tercerID",  "obl"=> "1", "minlength"=>"9", $disabled, "maxlength"=>"10", "onblur"=>"comprobar()", "width" => "25%", "value"=> $datos->principal->cod_tercer) );
                      $mHtml->Label("Digito de Verificación:", "width:25%; *:1;");
                      $mHtml->Input (array("name" => "tercer[num_verifi]", "validate" => "numero",  "id" => "num_verifiID", "size"=>1, "obl"=> "1", "minlength"=>"1","maxlength"=>"1", "width" => "25%", "value"=> $datos->principal->num_verifi, "end" => true) );
                      $mHtml->Label("Nombre:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "tercer[nom_tercer]", "validate"=>"alpha", "id" => "nom_tercerID", "width" => "25%","minlength"=>"10", "maxlength" => "50", "value" => $datos->principal->nom_tercer));
                      $mHtml->Label("Abreviatura:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "tercer[abr_tercer]", "validate"=>"alpha", "id" => "abr_tercerID", "width" => "25%", "minlength"=>"10", "maxlength" => "100", "value" => $datos->principal->abr_tercer, "end" => true));
                      $mHtml->Label("Regimen:", "width:25%; *:1;");
                      $mHtml->Select2 ($datos->regimen,  array("name" => "tercer[cod_terreg]", "validate" => "select", "obl" => "1", "id" => "cod_terregID", "width" => "25%", "key"=> $datos->principal->cod_terreg) );
                      $mHtml->Label("Ciudad:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "ciudad", "validate"=>"dir", "id" => "ciudadID", "width" => "25%", "minlength"=>"7", "maxlength" => "40", "value" => $datos->principal->abr_ciudad, "end" => true));
                      $mHtml->Label("Dirección:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "tercer[dir_domici]", "validate"=>"dir", "id" => "dir_domiciID", "width" => "25%", "minlength"=>"7", "maxlength" => "50", "value" => $datos->principal->dir_domici));
                      $mHtml->Label("Teléfono 1:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "tercer[num_telef1]", "validate"=>"numero", "id" => "num_telef1ID", "width" => "25%", "minlength"=>"7", "maxlength" => "10", "value" => $datos->principal->num_telef1, "end" => true));
                      $mHtml->Label("Teléfono 2:", "width:25%; :1;");
                      $mHtml->Input(array("name" => "tercer[num_telef2]", "id" => "num_telef2ID", "validate"=>"numero", "width" => "25%", "minlength"=>"7", "maxlength" => "10", "value" => $datos->principal->num_telef2));
                      $mHtml->Label("Celular:", "width:25%; :1;");
                      $mHtml->Input(array("name" => "tercer[num_telmov]", "id" => "num_telmovID", "validate"=>"numero", "width" => "25%", "minlength"=>"7", "maxlength" => "10", "value" => $datos->principal->num_telmov, "end" => true));
                      $mHtml->Label("Fax:", "width:25%; :1;");
                      $mHtml->Input(array("name" => "tercer[num_faxxx]", "id" => "num_faxxxID", "validate"=>"numero", "width" => "25%", "minlength"=>"7", "maxlength" => "10", "value" => $datos->principal->num_faxxx));
                      $mHtml->Label("Página WEB:", "width:25%; :1;");
                      $mHtml->Input(array("name" => "tercer[dir_urlweb]", "id" => "dir_urlwebID", "width" => "25%", "minlength"=>"7", "maxlength" => "100", "value" => $datos->principal->dir_urlweb, "end" => true));
                      $mHtml->Label("E-Mail:", "width:25%; :1;");
                      $mHtml->Input(array("name" => "tercer[dir_emailx]", "id" => "dir_emailxID", "width" => "25%", "minlength"=>"7", "maxlength" => "100", "validate"=>"email", "value" => $datos->principal->dir_emailx, "end" => true));
                    $mHtml->CloseTable("tr");
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();
              $mHtml->CloseDiv();
              $mHtml->OpenDiv("id:actividadesID; class:accordion");
                $mHtml->SetBody("<h3 style='padding:6px;'><center>Actividades</center></h3>");
                $mHtml->OpenDiv("id:sec3");
                  $mHtml->OpenDiv("id:form3; class:contentAccordionForm");
                    $mHtml->Table("tr");
                    foreach ($datos->actividades as $key => $value) {

                      $checked = in_array($value->cod_activi, $datos->activities) ? true : false;
                    
                      if($key%2==1){
                        $mHtml->Label(strtoupper("$value->nom_activi:"), "width:25%; :1;");
                        $mHtml->CheckBox(array("name" => "activi[$key]\" id='activi".$key."'", "checked"=>$checked, "width" => "25%", "value" =>$value->cod_activi,"onclick" => "validateProvAsist();","end"=>true));
                      }else{
                        $mHtml->Label(strtoupper("$value->nom_activi:"), "width:25%; :1;");
                        $mHtml->CheckBox(array("name" => "activi[$key]\" id='activi".$key."'", "checked"=>$checked, "width" => "25%", "value" =>$value->cod_activi, "onclick" => "validateProvAsist();"));
                      }
                    }
                    $mHtml->Label("Observaciones:", "width:25%; :1;");
                    $mHtml->TextArea( $datos->principal->obs_tercer, array("cols" => 100, "rows" => 8, "colspan" => "3", "name" => "tercer[obs_tercer]", "id" => "obs_tercer", "validate"=>"dir", "minlength"=>"3", "maxlength"=>"200", "width" => "25%",  "end" => true));  

                    $mHtml->Label(("PUESTO CONTROL:"), "width:25%; :1; id:pueCon;");
                    $mHtml->Select2 ($tipContro,  array("name" => "tercer[cod_contro]", "validate" => "select",  "obl" => "1", "id" => "cod_controID", "width" => "100%","colspan" => "3", "key"=> $datos->principal->cod_contro) );
                    $mHtml->CloseTable("tr");
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
            # Fin accordion2
            # Accordion3
            $mHtml->OpenDiv("id:natural");
              $mHtml->OpenDiv("id:naturalID; class:accordion");
                $mHtml->SetBody("<h3 style='padding:6px;'><center>Persona Natural</center></h3>");
                $mHtml->OpenDiv("id:sec3");
                  $mHtml->OpenDiv("id:form3; class:contentAccordionForm");
                    $mHtml->Table("tr");
                      $mHtml->Label("Pais:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "pais", "validate"=>"dir", "id" => "paisID", "width" => "25%", "minlength"=>"7", "maxlength" => "40", "value" => $nom_paisxx, "end" => true,  "onclick"=>"limpiarInput(this)"));
                      $mHtml->Label(("Tipo de Doumento:"), "width:25%; *:1;");
                      $mHtml->Select2 ($datos->tipoDocumento,  array("name" => "tercer[cod_tipdoc]", "validate" => "select",  "obl" => "1", "id" => "cod_tipdocID", "width" => "25%", "val"=> $datos->principal->cod_tipdoc) );
                      $mHtml->Label(("Número de Documento:"), "width:25%; *:1;");
                      $mHtml->Input (array("name" => "tercer[cod_tercer]", "validate" => "numero",  "id" => "cod_tercerID",  "obl"=> "1", "minlength"=>"5", $disabled, "maxlength"=>"10", "onblur"=>"comprobar()", "width" => "25%", "value"=> $datos->principal->cod_tercer, "end"=>true) );
                      $mHtml->Label("Nombres:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "tercer[nom_tercer]", "validate"=>"alpha", "id" => "nom_tercerID", "width" => "25%", "minlength"=>"4", "maxlength" => "30", "value" => $datos->principal->nom_tercer));
                      $mHtml->Label("Apellido 1:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "tercer[nom_apell1]", "validate"=>"alpha", "id" => "nom_apell1ID", "width" => "25%", "minlength"=>"4", "maxlength" => "20", "value" => $datos->principal->nom_apell1, "end" => true));
                      $mHtml->Label("Apellido 2:", "width:25%; :1;");
                      $mHtml->Input(array("name" => "tercer[nom_apell2]", "validate"=>"alpha", "id" => "nom_apell2ID", "width" => "25%", "minlength"=>"4", "maxlength" => "20", "value" => $datos->principal->nom_apell2));
                      $mHtml->Label("Ciudad:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "ciudad", "validate"=>"dir", "id" => "ciudadID", "width" => "25%", "minlength"=>"7", "maxlength" => "40", "value" => $datos->principal->abr_ciudad, "end" => true));
                      $mHtml->Label("Dirección:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "tercer[dir_domici]", "validate"=>"dir", "id" => "dir_domiciID", "width" => "25%", "minlength"=>"7", "maxlength" => "50", "value" => $datos->principal->dir_domici));
                      $mHtml->Label("Teléfono 1:", "width:25%; *:1;");
                      $mHtml->Input(array("obl" => "1", "name" => "tercer[num_telef1]", "validate"=>"numero", "id" => "num_telef1ID", "width" => "25%", "minlength"=>"7", "maxlength" => "10", "value" => $datos->principal->num_telef1, "end" => true));
                      $mHtml->Label("Teléfono 2:", "width:25%; :1;");
                      $mHtml->Input(array("name" => "tercer[num_telef2]", "id" => "num_telef2ID", "validate"=>"numero", "width" => "25%", "minlength"=>"7", "maxlength" => "10", "value" => $datos->principal->num_telef2));
                      $mHtml->Label("Celular:", "width:25%; :1;");
                      $mHtml->Input(array("name" => "tercer[num_telmov]", "id" => "num_telmovID", "validate"=>"numero", "width" => "25%", "minlength"=>"7", "maxlength" => "10", "value" => $datos->principal->num_telmov, "end" => true));
                      $mHtml->Label("Fax:", "width:25%; :1;");
                      $mHtml->Input(array("name" => "tercer[num_faxxx]", "id" => "num_faxxxID", "validate"=>"numero", "width" => "25%", "minlength"=>"7", "maxlength" => "10", "value" => $datos->principal->num_faxxx));
                      $mHtml->Label("Página WEB:", "width:25%; :1;");
                      $mHtml->Input(array("name" => "tercer[dir_urlweb]", "id" => "dir_urlwebID", "width" => "25%", "minlength"=>"7", "maxlength" => "100", "value" => $datos->principal->dir_urlweb, "end" => true));
                      $mHtml->Label("E-Mail:", "width:25%; :1;");
                      $mHtml->Input(array("name" => "tercer[dir_emailx]", "id" => "dir_emailxID", "width" => "25%", "minlength"=>"7", "maxlength" => "100", "validate"=>"email", "value" => $datos->principal->dir_emailx, "end" => true));
                    $mHtml->CloseTable("tr");
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();
              $mHtml->CloseDiv();
              $mHtml->OpenDiv("id:actividadesID; class:accordion");
                $mHtml->SetBody("<h3 style='padding:6px;'><center>Actividades</center></h3>");
                $mHtml->OpenDiv("id:sec3");
                  $mHtml->OpenDiv("id:form3; class:contentAccordionForm");
                    $mHtml->Table("tr");
                    foreach ($datos->actividades as $key => $value) {

                      $checked = in_array($value->cod_activi, $datos->activities) ? true : false;

                      if($key%2==1){
                        $mHtml->Label(strtoupper("$value->nom_activi:"), "width:25%; :1;");
                        $mHtml->CheckBox(array("name" => "activi[$key]\" id='activi".$key."'", "checked"=>$checked, "width" => "25%", "value" =>$value->cod_activi,"onclick" => "validateProvAsist();","end"=>true));
                      }else{
                        $mHtml->Label(strtoupper("$value->nom_activi:"), "width:25%; :1;");
                        $mHtml->CheckBox(array("name" => "activi[$key]\" id='activi".$key."'", "id"=>"activi$key", "checked"=>$checked, "width" => "25%", "value" =>$value->cod_activi, "onclick" => "validateProvAsist();"));
                      }
                    }
                    $mHtml->Label("Observaciones:", "width:25%; :1;");
                    $mHtml->TextArea( $datos->principal->obs_tercer, array("cols" => 100, "rows" => 8, "colspan" => "3", "name" => "tercer[obs_tercer]", "id" => "obs_tercer", "validate"=>"dir", "minlength"=>"3", "maxlength"=>"200", "width" => "25%", "end" => true));                              
                    
                    $mHtml->Label(("PUESTO CONTROL:"), "width:25%; :1; id:pueCon;");
                    $mHtml->Select2 ($tipContro,  array("name" => "tercer[cod_contro]", "validate" => "select",  "obl" => "1", "id" => "cod_controID", "width" => "100%","colspan" => "3",  "key"=> $datos->principal->cod_contro) );
                    
                    $mHtml->CloseTable("tr");
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
          # fin Accordion 3
        $mHtml->OpenDiv("id:botones");
          $mHtml->Table("tr");
          if(!$datos->principal->cod_tercer){
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
      echo '<script> setTimeout(() => {
                  validateProvAsist();
            }, "2000"); </script>';
    }

//FIN FUNCION INSERT_SEDE


function inputsByCountry(){
  $inputs = array();
  
  $colombia = array('tipdoc' => array('name' => 'Nit', 'value' => 'N'));
  $chile = array('tipdoc' => array('name' => 'Rut', 'value' => 'RT'));

  $inputs[3] = $colombia;
  $inputs[11] = $chile;

  return $inputs;
}

}

//FIN CLASE
$proceso = new Ins_tercer_tercer($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
?>