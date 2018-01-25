<?php
/*! \class: ins_config_loginx
 *  \brief: Personalizar login de AVANSAT 
 *  \author: Andres Felipe Torres
 *  \date: 27/12/2017, 
 */
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

class ins_config_loginx
{
    var $conexion,
        $cod_aplica,
        $cNull = array( array('', '-----') ),
        $usuario;
    
    //Metodos
    function __construct( $co, $us, $ca )
    {
            $this -> conexion = $co;
            $this -> usuario = $us;
            $this -> cod_aplica = $ca;

            @include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );
            IncludeJS( 'jquery.js' );
            IncludeJS( 'jquery.multiselect.filter.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
            IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
            IncludeJS( 'time.js', '../'.DIR_APLICA_CENTRAL.'/js/' );
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>\n";
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";

            switch( $_REQUEST[opcion] )
            {
                case "1":
                    $this -> Insertar();
                break;
                
                case "2":
                    $this -> Datos();
                break;
                
                case "3":
                    $this -> Actualizar();
                    $this -> Buscar();
                break;
                
                default:
                    $this -> login();
                break;
            }

        
    }
    //********METODOS
    function principal()
    {   
        
        if ($_REQUEST[Ajax] === 'on') {
            $GLOBALS[opcion] = $_REQUEST[Option];
        }
        switch( $GLOBALS[opcion] )
        {
            case "1":
            echo "string";
                $this -> Insertar();
            break;
            
            case "2":
                $this -> Datos();
            break;
            
            case "3":
                $this -> Actualizar();
                $this -> Buscar();
            break;
            
            default:
                $this -> login();
            break;
        }
    }
    
    function login()
    {           
        echo '<script type="text/javascript" src="../' . DIR_APLICA_CENTRAL . '/js/jquery.blockUI.js"></script>';
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/configColor.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/spectrum.js\"></script>\n";
        echo "<link rel='stylesheet' href=\"../".DIR_APLICA_CENTRAL."/estilos/spectrum.css\"/>";

        $config = $this->getConfig();

        $mHtml = new Formlib(2);
        # incluye JS
        $mHtml->SetJs("config");
        $mHtml->SetJs("fecha");
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("functions");
        $mHtml->SetJs("InsertProtocolo");
        $mHtml->SetJs("new_ajax"); 
        $mHtml->SetJs("dinamic_list");
        $mHtml->SetCss("dinamic_list");
        $mHtml->SetJs("validator");
        $mHtml->SetCssJq("validator"); 
        $mHtml->CloseTable("tr");
        # incluye Css
        $mHtml->SetCssJq("jquery");
        $mHtml->SetCssJq("informes");
        $mHtml->Body(array("menubar" => "no"));

        # Abre Form
        $mHtml->Form(array("action" => "index.php?cod_servic=" . $GLOBALS["cod_servic"] . "",
                            "method" => "post",
                            "name" => "form_log",
                            "header" => "login",
                            "enctype" => "multipart/form-data"));
        #variables ocultas
    
        $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>'satt_standa'));
        $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>"central"));
        $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>"1"));
        $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST["cod_servic"]));
        $mHtml->Hidden(array( "name" => "usuario", "id" => "cod_servicID", 'value'=>$_REQUEST["cod_servic"]));

        $mHtml->SetBody("<style>.fondo{
            background-color:#EBF8E2 !important;
            font-weight: bold;
        }</style>");

        # Construye accordion
          $mHtml->Row("td");
            $mHtml->OpenDiv("id:contentID; class:contentAccordion");
            # Accordion1
              $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
                $mHtml->SetBody("<h1 style='padding:6px'><b>Configuración Login</b></h1>");
                $mHtml->OpenDiv("id:sec1;");
                  $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                    $mHtml->Table("tr");
                            $mHtml->Row();
                                $mHtml->Line("logo", "t2", 1, 3, "left", "", "", "");
                            $mHtml->CloseRow();
                            $mHtml->Row();  
                                $mHtml->Label("Opcion 1: Seleccionar imagen", array("width"=>"30%", "background-color"=>"#EBF8E2 !important", "class"=>"fondo") );  
                                $mHtml->File( array("name"=>"fot_logoti", "class"=>"fondo", "id"=>"fot_logotiID", "width"=>"30%", "value"=>"") );
                                $mHtml->Label("Observaciones: Formato valido .png y .jpg con un tamaño de: (1600 pixeles x 775 pixeles).", array("align"=>"right","class"=>"fondo", "width"=>"30%"));
                            $mHtml->CloseRow();
                            $mHtml->Row();
                                $mHtml->SetBody("<tr><td  class='fondo' style='padding:4px;' align='center' colspan='4' ><input class='crmButton small save' style='cursor:pointer; width:100px;' type='button' value='Logo AVANSAT' onclick='resetlog_empres()'/></td></tr>");
                            $mHtml->CloseRow();
                            $mHtml->Row();
                                $mHtml->Line("Logo Fondo Pantalla", "t2", 0, 3, "left", "", "", "");
                            $mHtml->CloseRow();
                            $mHtml->Row();
                                $mHtml->Label("Opcion 1: Seleccionar imagen", "width:25%; class:fondo; ");  
                                $mHtml->File( array("name"=>"log_fonpan", "class"=>"fondo", "id"=>"log_fonpanID", "width"=>"50%", "value"=>"") );
                                $mHtml->Label("Observaciones: Formato valido .png y .jpg con un tamaño de: (1600 pixeles x 775 pixeles).", array("align"=>"right","class"=>"fondo", "width"=>"50%"));
                            $mHtml->CloseRow();
                            $mHtml->Row();
                                $mHtml->Label("Opcion 2: Seleccionar color", "width:25%; class:fondo;");
                                $mHtml->Input("name:col_botin1; width:25%; size:40; maxlength:100; class:fondo;");
                            $mHtml->CloseRow();
                            $mHtml->Row();
                                $mHtml->SetBody("<tr><td  class='fondo' style='padding:4px;' align='center' colspan='4' ><input class='crmButton small save' style='cursor:pointer; width:100px;' type='button' value='Fondo AVANSAT' onclick='reset_fonpan(),resetColor(1)'/></td></tr>");
                            $mHtml->CloseRow();
                            $mHtml->Row();
                                $mHtml->Line(" Boton Ingresar", "t2", 1, 3, "left", "", "", "");
                            $mHtml->CloseRow();
                            $mHtml->Row();
                                $mHtml->Label("Opcion 2: Seleccionar color", "width:25%; class:fondo;");
                                $mHtml->Input("name:col_botin2; width:25%; size:40; maxlength:100; class:fondo;");
                            $mHtml->CloseRow();
                            $mHtml->Row();
                                $mHtml->SetBody("<tr><td  class='fondo' style='padding:4px;' align='center' colspan='4' ><input class='crmButton small save' style='cursor:pointer; width:100px;' type='button' value='Color AVANSAT' onclick='resetColor()'/></td></tr>");
                            $mHtml->CloseRow();
                            $mHtml->Row();
                                $mHtml->Line("Nota De Aclaracion y Recomendaciones", "t2", 1, 3, "left", "", "", "");
                            $mHtml->CloseRow();
                            $mHtml->Row();
                            $mHtml->Label("Grupo OET SAS no responderá por la utilización de imágenes de terceros protegidas por los derechos de autor y/o por la propiedad intelectual utilizadas por el usuario. El usuario es el único responsable de las <br>imágenes utilizadas en el logotipo y el fondo de pantalla.", "width:25%; align:left; height:50%; font-size:15px; colspan:3 end:yes");//Seleccionar color 
                            $mHtml->CloseRow();
                            $mHtml->Row();
                            $mHtml->Label("Se recomienda borrar cookies y cache del sitio despues de insertar la configuración para que los cambios se visualicen en la plataforma.", "width:25%; align:left; height:50%; font-size:15px; colspan:3; align:justify ");//Seleccionar color 
                            $mHtml->CloseRow();
                            $mHtml->Row();
                                $mHtml->SetBody("<TD style='class:crmButton small save'></TD>");
                                $mHtml->StyleButton("value:Aceptar; class:crmButton small save; align:center; onclick:if(confirm('Esta de acuerdo a la Nota De Aclaracion informada en la parte inferior?')){submit()}; ");
                                $mHtml->StyleButton("value:Borrar; class:crmButton small save; align:center; onclick:reset(),resetColor(1),resetColor(2); end:1; ");
                            $mHtml->CloseRow();
                    $mHtml->CloseTable("tr");
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();
              $mHtml->CloseDiv();
            # Fin accordion1    
            $mHtml->CloseDiv();
          $mHtml->CloseRow("td");
            # Cierra formulario
          $mHtml->CloseForm();
          # Cierra Body
          $mHtml->CloseBody();

          # Muestra Html
          echo $mHtml->MakeHtml();  
    }

    function getConfig(){
                $sql = "SELECT cod_config,log_empres, log_fonpan, col_fonpan, col_botlog, usr_creaci, fec_creaci FROM ". BASE_DATOS.".tab_config_loginx 
                 WHERE 1=1";
        
        $consulta = new Consulta($sql, $this->conexion);
        $config = $consulta->ret_arreglo('a');

        return $config;
    }

    function Insertar(){

        $config = $this->getConfig();
        //se valida que vengan los campos y si no se llenan con la configuarción estandar.....

        $val = true;
        $mensaje = "";

        if($_FILES["fot_logoti"]['tmp_name'] == '') {
            $val = false;
            $mensaje .= "El logo quedo con la configuracion estandar. <br>";
            $fot_logoti = "../satt_standa/imagenes/logo-sat-color.png";
        }else{
            $fot_logoti = "".URL_APLICA."/imagenes/logo_".BASE_DATOS.".jpg";
        }
        if($_FILES["log_fonpan"]['tmp_name'] == '' && $_REQUEST['col_botin1'] == ''){
            $val = false;
            $mensaje .= "El fondo de pantalla quedo con la configuracion estandar. <br>";
            $log_fonpan = "satt_standa/imagenes/11.jpg";
        }
        if($_FILES["log_fonpan"]['tmp_name'] == ''){
            $log_fonpan = "../satt_standa/imagenes/11.jpg";
        }else{
            $log_fonpan = "".URL_APLICA."/imagenes/9.jpg";
        }
        if($_REQUEST['col_botin1'] == ''){
            $col_botin1 = "#285c00";
        }else{
            $col_botin1 = $_REQUEST['col_botin1'];
        }
        if($_REQUEST['col_botin2'] == ''){
            $val = false;
            $mensaje .= "El Boton ingresar quedo con la configuracion estandar. <br>";
            $col_botin2 = "#285c00";
        }else{
            $col_botin2 = $_REQUEST['col_botin2'];
        }

        if ($config == true) {
            $sql = "INSERT INTO ". BASE_DATOS .".tab_bitaco_loginx
                (log_empres, log_fonpan,col_fonpan,col_botlog,usr_creaci,fec_creaci,usr_modifi, fec_modifi)
                VALUES ('". $config['log_empres'] ."','".$config['log_fonpan']."','". $config['col_fonpan']."', '". $config['col_botlog']."', '". $config['usr_creaci'] ."', '". $config['fec_creaci'] ."', '".$_SESSION['datos_usuario']['cod_usuari']."',NOW())";

            $consulta = new Consulta($sql, $this->conexion, "R");
            
            $sql = "DELETE FROM tab_config_loginx WHERE cod_config = ".$config['cod_config']."";
            $consulta = new Consulta($sql, $this->conexion, "R");

        }

        $sql = "INSERT INTO ". BASE_DATOS .".tab_config_loginx
                            (log_empres, log_fonpan,col_fonpan,col_botlog,usr_creaci,fec_creaci) 
                     VALUES ('".$fot_logoti."', '". $log_fonpan ."', '". $col_botin1."', '". $col_botin2 ."', '". $_SESSION['datos_usuario']['cod_usuari'] ."', NOW())";

        $consulta = new Consulta($sql, $this->conexion, "RC");

        #----------------------------------------------------------------------------------------
        //se suben los logos al servidor a la carpeta del cliente.
        if ($_FILES["fot_logoti"]['tmp_name']!='') {
                move_uploaded_file($_FILES[fot_logoti][tmp_name], "" . URL_ARCHIV ."imagenes/logo_".BASE_DATOS.".jpg");
                $_REQUEST[fot_logoti] = "'imagenes/logo_".BASE_DATOS.".jpg'";
        }
        if ($_FILES["log_fonpan"]['tmp_name']!='') {
                move_uploaded_file($_FILES[log_fonpan][tmp_name], "" . URL_ARCHIV ."imagenes/9.jpg");
                $_REQUEST[log_fonpan] = "'imagenes/9.jpg'";
        }

        //se crea el archivo guia_login.php
        if(file_exists(URL_ARCHIV."/guia_login.inc")){
            unlink(URL_ARCHIV."/guia_login.inc");
            $mFile = fopen(URL_ARCHIV."/guia_login.inc", "a");
        }else{
            $mFile = fopen(URL_ARCHIV."/guia_login.inc", "a");
        }
            fwrite($mFile, "<?php\n");
            fwrite($mFile, "define(IMAGEN_LOGO,  '".$fot_logoti."');\n");  
            fwrite($mFile, "define(IMAGEN_FONDO, '".$log_fonpan."');\n");  
            fwrite($mFile, "define(COLOR_FONDO,  '".$col_botin1."');\n");  
            fwrite($mFile, "define(COLOR_BOTON,  '".$col_botin2."');\n");  
            fwrite($mFile, "?>");
            fclose($mFile);
        #----------------------------------------------------------------------------------------

        $cForm = new Formlib(2);

        if (!mysql_error()) {
            if ($val == false) {
                $cForm -> Table("tr");
                    ShowMessage('s', 'Transacción Exitosa!!', '<b>'.$mensaje.'</b>');
                $cForm -> CloseTable();
            }else{
                $cForm -> Table("tr");
                ShowMessage('s', 'Transacción Exitosa!!', "<b>La configuración ha sido registrada éxitosamente.</b>");
                $cForm -> CloseTable();
            }
        }else{
            $cForm -> Table("re");
            ShowMessage('e', 'Transacción Fallida!!', "<b>no fue posible insertar la configuración, comuniquese con el area de soporte.</b>");
            $cForm -> CloseTable();
        }
        
        $cForm -> Hidden( "name:window; value:central" );
        $cForm -> Hidden( "name:Action" );
        $cForm -> Hidden( "name:standar; value:".DIR_APLICA_CENTRAL );
        $cForm -> CloseForm();
        
        echo $cForm->MakeHtml();
    }

    
}  

if ($_REQUEST[Ajax] === 'on') {
    $proceso = new ins_config_loginx($conexion, $usuario_aplicacion, $codigo);
}else{
    $proceso = new ins_config_loginx($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
}

?>