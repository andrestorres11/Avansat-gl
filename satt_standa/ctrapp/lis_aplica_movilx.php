<?php
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);
   

     //require "ajax_trayle_trayle.php";
 
class ListarusuariosMoviles {
   
    private static $cFunciones, $cTransp, $conexion, $usuario, $cod_aplica;

    function __construct($co, $us, $ca) {
        self::$conexion = $co;
        self::$usuario = $us;
        self::$cod_aplica = $ca;
        switch ($_REQUEST["opcion"]) {
            case 1:
              self::Formulario();
            break;

            case 2:
              self::Transaccion();
            break;

            case 3:
              self::FormularioIns();
            break;

            case 4:
              self::ExportExcel();
            break;

            default:
                self::filtro();
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
        include_once('../'.DIR_APLICA_CENTRAL.'/inform/class_despac_trans3.php');
        $mHtml = new FormLib(2);
        self::$cTransp = new Despac(self::$conexion, self::$usuario, self::$cod_aplica);

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
        echo "<script language='JavaScript' src='../".DIR_APLICA_CENTRAL."/ctrapp/js/ins_aplica_movil.js'></script>";
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
        $mHtml->Form(array("action" => "index.php", "method" => "post", "name" => "form_search", "header" => "Conductores", "enctype" => "multipart/form-data"));

      	#variables ocultas
      
        $mHtml->Hidden(array( "name" => "cod_tercer", "id" => "cod_tercerID", 'value'=>$mCodTransp));
        $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
        $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
        $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
        $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));
        $mHtml->Hidden(array( "name" => "cod_transp", "id" => "cod_transpID", 'value'=>$mCodTransp));
        $mHtml->Hidden(array( "name" => "conductor", "id" => "conductor", 'value'=>"")); 
        $mHtml->Hidden(array( "name" => "total", "id" => "total", 'value'=>$total));

      	# Construye accordion
        $mHtml->Row("td");
          $mHtml->OpenDiv("id:contentID; class:contentAccordion");
            if( $total != 1 )
            {
          	# Accordion1
            	$mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
  	            $mHtml->SetBody("<h1 style='padding:6px'><b>Agregar Usuario Movil</b></h1>");
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
            }    
          	# Accordion2
	          $mHtml->OpenDiv("id:datos; class:accordion");
	            $mHtml->SetBody("<h1 style='padding:6px'><b>Listado de Usuarios Moviles</b></h1>");
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
        
    }

     /*! \fn: Formulario
     *  \brief: Formulario para re establecer y actualizar la informacion de los usuarios moviles 
     *  \author: Edward Serrano
     *  \date: 26/05/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    function Formulario() {
        
          # Nuevo frame ---------------------------------------------------------------
          # Inicia clase del fromulario ----------------------------------------------------------------------------------
          $mHtml = new FormLib(2);

          # incluye JS
          $mHtml->SetJs("min");
          
          $mHtml->SetJs("fecha");
          $mHtml->SetJs("jquery");
          $mHtml->SetJs("functions");
          echo "<script language='JavaScript' src='../".DIR_APLICA_CENTRAL."/ctrapp/js/ins_aplica_movil.js'></script>";          
          $mHtml->SetJs("new_ajax"); 
          $mHtml->SetJs("dinamic_list");
          $mHtml->SetCss("dinamic_list");
          
          $mHtml->CloseTable("tr");
          # incluye Css
          $mHtml->SetCssJq("jquery");
          # Abre Form
          $mHtml->Form(array("action" => "index.php", "method" => "post", "name" => "form_vehicu", "header" => "Transportadoras", "enctype" => "multipart/form-data"));

      
          $mHtml->Hidden(array( "name" => "standa",     "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
          $mHtml->Hidden(array( "name" => "window",     "id" => "windowID", 'value'=>'central'));
          $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
          $mHtml->Hidden(array( "name" => "opcion",     "id" => "opcionID", 'value'=>'2'));     
          $mHtml->Hidden(array( "name" => "action",     "id" => "action", 'value'=>"none"));
          $mHtml->Hidden(array( "name" => "Ajax",       "id" => "Ajax", 'value'=>"on"));
          
          $mData = self::getDataUsuario();
          # Construye accordion
          $mHtml->Row("td");
          $mHtml->OpenDiv("id:contentID; class:contentAccordion");
            # Accordion1
            $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
              $mHtml->SetBody("<h3 style='padding:6px;'><center>Datos usuario APP</center></h3>");
            $mHtml->OpenDiv("id:sec1;");
              $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                $mHtml->Table("tr");
              
                  $mHtml->Label("Usuario APP:", "width:25%;"); 
                  $mHtml->Info (array("name" => "usuarioapp[cod_usuari]", "size"=>"50", "width" => "25%", "value"=>  $mData["cod_usuari"]) );
                  $mHtml->Label("Conductor:", "width:5%;");
                  $mHtml->Info (array("name" => "usuarioapp[nom_tercer]", "size"=>"50", "width" => "45%", "value"=>  $mData["nom_tercer"], "end"=> true) );
                  
				          $mHtml->Label("Correo:", "width:25%; *:1;"); 
                  //$mHtml->Input (array("name" => "usuarioapp[nom_emailx]", "size"=>"30", "id" => "nom_emailxID",  "minlength" => "1", "maxlength" => "60", "width" => "25%", "value"=> $mData["dir_emailx"]) );
                  $mHtml->SetBody('<td><input type="text" name="usuarioapp[dir_emailx]" id="dir_emailxID" value="'.($mData["dir_emailx"]!=""?$mData["dir_emailx"]:"").'" /> </td>');
                  $mHtml->Label("Doc.Conductor", "width:5%;");
                  $mHtml->Info (array("name" => "usuarioapp[cod_tercer]", "size"=>"50", "width" => "45%", "value"=>  $mData["cod_tercer"], "end"=> true) );
                  $mHtml->StyleButton("name:send;  id:modificarID; value:Actualizar; onclick:Guardar('save'); align:center; colspan:2;  class:crmButton small save");
                  $mHtml->StyleButton("name:reset; id:resetusr;   value:Re establecer; onclick:Guardar('reset'); align:center; colspan:1; class:crmButton small save");

                  $mHtml->StyleButton("name:clear; id:cancelarID; value:Cancelar; onclick:Guardar('forward'); align:center; colspan:1; class:crmButton small save");

                  $mHtml->Hidden(array( "name" => "cod_transp", "id" => "cod_transpID", 'value'=>$mData["cod_transp"]));
                  $mHtml->Hidden(array( "name" => "cod_tercer", "id" => "cod_tercerID", 'value'=>$mData["cod_tercer"]));
                  $mHtml->Hidden(array( "name" => "cod_usuari", "id" => "cod_usuariID", 'value'=>$mData["cod_usuari"]));
                  $mHtml->Hidden(array( "name" => "ind_activo", "id" => "ind_activoID", 'value'=>$mData["ind_estado"]));

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

	/*! \fn: getDataUsuario
     *  \brief: Obtinene la informacion del usuario movil
     *  \author: Edward Serrano
     *  \date: 26/05/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    private function getDataUsuario()
    {
        $mQuery = "SELECT   a.cod_transp, a.cod_tercer, c.cod_usuari, b.nom_tercer, b.nom_apell1, b.nom_apell2, b.dir_emailx, c.clv_usuari, IF( b.fec_creaci IS NULL OR a.fec_creaci = '', 'N/A', b.fec_creaci) AS fec_creaci, c.cod_tercer AS cod_pendie, c.ind_activo AS ind_estado
                   FROM  
                          ".BASE_DATOS.".tab_transp_tercer a INNER JOIN 
                          ".BASE_DATOS.".tab_tercer_tercer b ON a.cod_tercer = b.cod_tercer INNER JOIN
                          ".BASE_DATOS.".tab_usuari_movilx c ON b.cod_tercer = c.cod_tercer
                  WHERE
                          a.ind_estado = 1 AND
                          c.cod_tercer = '".$_REQUEST["conductor"]."'  LIMIT 1 ";  

        $nit = new Consulta($mQuery, self::$conexion);
        $nit = $nit -> ret_matriz("a");
        return $nit[0];
    }

    /*! \fn: Transaccion
     *  \brief: Realiza la actualizacio del usuario movil
     *  \author: Edward Serrano
     *  \date: 26/05/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    public function Transaccion()
    {
      switch ($_REQUEST['action']) {
        case 'save':
          if($_REQUEST['cod_tercer'] != "")
          {
            if(self::ActualizarUsuario($_REQUEST)=="ok")
            {
              $mensaje = "Se actualizo correctamente el usuario <b>".$_REQUEST['cod_usuari']."</b><br><br><button class='crmButton small save' onclick='$(this).parents().get( 6 ).remove();'>Cerrar</button> ";
              $mens = new mensajes();
              $mens->correcto("Actulizacion Usuario Movil", $mensaje);
              $_REQUEST["opcion"] = "1";
              $_REQUEST["conductor"] = $_REQUEST["cod_tercer"];
              unset($_REQUEST["action"]);
              unset($_REQUEST["usuarioapp"]);
              self::Formulario();
            }
            else
            {
              $mensaje = "No se pudo actualizar el usuario <b>".$_REQUEST['cod_usuari']."</b><br><br><button class='crmButton small save' onclick='window.location.href =\"index.php?window=central&cod_servic=\"+$(\"#cod_servicID\").val()+\"&menant=\"+$(\"#cod_servicID\").val()'>Cerrar</button> ";
              $mens = new mensajes();
              $mens->error("", $mensaje);
            }
          }
          else
          {
            $mensaje = "No se pudo actualizar el usuario <b>".$_REQUEST['cod_usuari']."</b><br><br><button class='crmButton small save' onclick='window.location.href =\"index.php?window=central&cod_servic=\"+$(\"#cod_servicID\").val()+\"&menant=\"+$(\"#cod_servicID\").val()'>Cerrar</button> ";
              $mens = new mensajes();
              $mens->error("", $mensaje);
          }
          break;

        default:
          echo "Sin opcion";
          break;
      }
    }

    /*! \fn: ActualizarUsuario
    * \brief: Actuliza informacion del usuario
    * \author: Edward Serrano
    * \date: 22/05/2017
    * \date modified: dia/mes/año
    * \param: paramatro
    * \return valor que retorna
    */
    public function ActualizarUsuario()
    {
      try
      {

        $mSql = "UPDATE ".BASE_DATOS.".tab_tercer_tercer 
                        SET 
                              dir_emailx = '".$_REQUEST['usuarioapp']['dir_emailx']."',
                              usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
                                fec_modifi = NOW()
                        WHERE cod_tercer = '".$_REQUEST['cod_tercer']."'";
        $consulta = new Consulta($mSql, self::$conexion, "BR");
        if($consulta)
        {
          $consultaFinal = new Consulta("COMMIT", self::$conexion);
          return "ok";
        }
        else
        {
          $consultaFinal = new Consulta("ROLLBACK", self::$conexion);
          return "error";
        }
      }
      catch(Exception $e)
      {
        echo "Error en la funcion ActualizarUsuario:",  $e->getMessage(), "\n";
      }
    }

     /*! \fn: FormularioIns
     *  \brief: Formulario para crear usuario movil 
     *  \author: Edward Serrano
     *  \date: 26/05/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    function FormularioIns() {
        
          # Nuevo frame ---------------------------------------------------------------
          # Inicia clase del fromulario ----------------------------------------------------------------------------------
          $mHtml = new FormLib(2);

          # Datos varios
          $tip_person = array(
              0 => array( 0 => NULL, 1 => '--' ),
              1 => array( 0 => 'N', 1 => 'Natural' ),
              2 => array( 0 => 'J', 1 => 'Juridica' )
              );    
          $est_appxxx = array(
                    0 => array( 0 => NULL, 1 => '--' ),
                    1 => array( 0 => '1', 1 => 'Activo' ),
                    2 => array( 0 => '0', 1 => 'Inactivo' )
                    );    
          $tip_usuari = array(
                    0 => array( 0 => NULL, 1 => '--' ),
                    1 => array( 0 => '0', 1 => 'Conductor' ),
                    2 => array( 0 => '1', 1 => 'Administrador' ),
                    3 => array( 0 => '2', 1 => 'Inspecciones' ),
                    3 => array( 0 => '3', 1 => 'asistencia en carretera' )
                    );

          $query = "SELECT a.cod_tipdoc, a.nom_tipdoc FROM ".BASE_DATOS.".tab_genera_tipdoc a WHERE 1 = 1";

          $consulta = new Consulta($query, self::$conexion);
          $dat_tipdoc = $consulta -> ret_matriz("i");

          # incluye JS
          $mHtml->SetJs("min");
          
          $mHtml->SetJs("fecha");
          $mHtml->SetJs("jquery");
          $mHtml->SetJs("functions");
          echo "<script language='JavaScript' src='../".DIR_APLICA_CENTRAL."/ctrapp/js/ins_aplica_movil.js'></script>";          
          $mHtml->SetJs("new_ajax"); 
          $mHtml->SetJs("dinamic_list");
          $mHtml->SetCss("dinamic_list");
          
          $mHtml->CloseTable("tr");
          # incluye Css
          $mHtml->SetCssJq("jquery");
          # Abre Form
          $mHtml->Form(array("action" => "index.php", "method" => "post", "name" => "form_vehicu", "header" => "Transportadoras", "enctype" => "multipart/form-data"));

      
          $mHtml->Hidden(array( "name" => "standa",     "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
          $mHtml->Hidden(array( "name" => "window",     "id" => "windowID", 'value'=>'central'));
          $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
          $mHtml->Hidden(array( "name" => "opcion",     "id" => "opcionID", 'value'=>'3'));     
          $mHtml->Hidden(array( "name" => "action",     "id" => "action", 'value'=>"none"));
          $mHtml->Hidden(array( "name" => "Ajax",       "id" => "Ajax", 'value'=>"on"));
          
          $mData = self::getDataUsuario();
          # Construye accordion
          $mHtml->Row("td");
          $mHtml->OpenDiv("id:contentID; class:contentAccordion");
            # Accordion1
            $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
              $mHtml->SetBody("<h3 style='padding:6px;'><center>Nuevo Usuario Movil</center></h3>");
            $mHtml->OpenDiv("id:sec1;");
              $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                $mHtml->Table("tr");
                  $mHtml->Row();
                    // $mHtml->Label("Tipo de usuario:", array("width" => "50%", "colspan"=>"4"));
                    // $mHtml->Select2 ($tip_person,  array("name" => "tip_person", "width" => "50%","colspan"=>"4") );
                    $mHtml->Label("* Tipo de Usuario", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Select2 ($tip_usuari,  array("name" => "ind_admini", "id" => "ind_adminiID", "width" => "50%","colspan"=>"4") );
                  $mHtml->CloseRow();
                  $mHtml->Row();
                    $mHtml->Label( "Datos Basicos del Usuario",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"8") );
                  $mHtml->CloseRow();
                  $mHtml->Row();
                    $mHtml->Label("Tipo de Documento", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Select2 ($dat_tipdoc,  array("name" => "tip_docume", "width" => "50%","colspan"=>"2") );
                    $mHtml->Label("Numero de Documento", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Input(array("name" => "num_docume", "id" => "num_documeID", "width" => "25%", "colspan"=>"2", "onkeypress" => "return NumericInput(event)"));
                  $mHtml->CloseRow();
                  $mHtml->Row();
                    $mHtml->Label("Nombres", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Input(array("name" => "nom_usuari", "id" => "nom_usuariID", "width" => "25%", "colspan"=>"2", "readonly"=>"readonly", "disabled"=>"disabled"));
                    $mHtml->Label("Apellido 1", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Input(array("name" => "nom_appel1", "id" => "nom_appel1ID", "width" => "25%", "colspan"=>"2", "readonly"=>"readonly", "disabled"=>"disabled"));
                  $mHtml->CloseRow();
                  $mHtml->Row();
                    $mHtml->Label("Apellido 2", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Input(array("name" => "nom_appel2", "id" => "nom_appel2ID", "width" => "25%", "colspan"=>"2", "readonly"=>"readonly", "disabled"=>"disabled"));
                    $mHtml->Label("Direccion", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Input(array("name" => "num_direcc", "id" => "num_direccID", "width" => "25%", "colspan"=>"2", "readonly"=>"readonly", "disabled"=>"disabled"));
                  $mHtml->CloseRow();
                  $mHtml->Row();
                    $mHtml->Label("Telefono 1", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Input(array("name" => "num_telef1", "id" => "num_telef1ID", "width" => "25%", "colspan"=>"2", "readonly"=>"readonly", "disabled"=>"disabled"));
                    $mHtml->Label("Telefono 2", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Input(array("name" => "num_telef2", "id" => "num_telef2ID", "width" => "25%", "colspan"=>"2", "readonly"=>"readonly", "disabled"=>"disabled"));
                  $mHtml->CloseRow();
                  $mHtml->Row();
                    $mHtml->Label("Celular", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Input(array("name" => "num_movilx", "id" => "num_movilxID", "width" => "25%", "colspan"=>"2", "readonly"=>"readonly", "disabled"=>"disabled"));
                    $mHtml->Label("* E-mail", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Input(array("name" => "nom_emailx", "id" => "nom_emailxID", "width" => "25%", "colspan"=>"2"));
                  $mHtml->CloseRow();
                  $mHtml->Row();
                    $mHtml->Label( "Datos Aplicacion APP",  array("align"=>"center", "class"=>"celda_titulo","colspan"=>"8") );
                  $mHtml->CloseRow();
                  $mHtml->Row();
                    $mHtml->Label("* Usuario a Generar", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Input(array("name" => "nom_usrapp", "id" => "nom_usrappID", "width" => "25%", "colspan"=>"2"));
                    $mHtml->Label("* Serie", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Input(array("name" => "cod_seriex", "id" => "cod_seriexID", "width" => "25%", "colspan"=>"2", "readonly"=>"readonly", "disabled"=>"disabled"));
                  $mHtml->CloseRow();
                  $mHtml->Row();
                    $mHtml->Label("* Estado", array("width" => "25%", "colspan"=>"2"));
                    $mHtml->Select2 ($est_appxxx,  array("name" => "cod_estado", "id" => "cod_estadoID", "width" => "50%","colspan"=>"2") );
                  $mHtml->CloseRow();
                  $mHtml->Row();
                    $mHtml->StyleButton("name:send;  id:guardarID; value:Guardar; onclick:guardar(); align:center; colspan:4;  class:crmButton small save");
                    $mHtml->StyleButton("name:clear; id:cancelarID; value:Cancelar; onclick:Guardar('forward'); align:center; colspan:4; class:crmButton small save");
                  $mHtml->CloseRow();                  
                  $mHtml->Hidden(array( "name" => "nit_transpor", "id" => "nit_transporID", 'value'=>NIT_TRANSPOR));
                  $mHtml->Hidden(array( "name" => "cod_transp", "id" => "cod_transpID", 'value'=>$_REQUEST["cod_tercer"]));
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

    /*! \fn: ExportExcel
    * \brief: Exportar a excel consuta
    * \author: Edward Serrano
    * \date: 29/08/2017
    * \date modified: dia/mes/año
    * \param: paramatro
    * \return valor que retorna
    */
    private function ExportExcel()
    {
      session_start();
      $date=date("Y_m_d_h_s");
      $consulta = new Consulta($_SESSION["queryXLS"],  self::$conexion);
      $mData = $consulta -> ret_matriz( "i" );
      header('Content-type: application/vnd.ms-excel');
      header("Content-Disposition: attachment; filename=Usuarios_Moviles_".$date.".xls");
      header("Pragma: no-cache");
      header("Expires: 0");

      ob_clean();
      echo "<table>";
      echo "  <tr>";
      echo "     <th>Transportadora</th>";
      echo "     <th>Doc.Conductor</th>";
      echo "     <th>Usuario APP</th>";
      echo "     <th>Nombre conductor</th>";
      echo "     <th>Primer apellido</th>";
      echo "     <th>Segundo apellido</th>";
      echo "     <th>Correo</th>";
      echo "     <th>Contraseña</th>";
      echo "  </tr>";
      foreach ($mData as $key => $value) 
      {
        echo "  <tr>";
        echo "     <td>".$value["cod_transp"]."</td>";
        echo "     <td>".$value["cod_tercer"]."</td>";
        echo "     <td>".$value["cod_usuari"]."</td>";
        echo "     <td>".$value["nom_tercer"]."</td>";
        echo "     <td>".$value["nom_apell1"]."</td>";
        echo "     <td>".$value["nom_apell2"]."</td>";
        echo "     <td>".$value["dir_emailx"]."</td>";
        echo "     <td>".base64_decode($value["clv_usuari"])."</td>";
        echo "  </tr>";
      }
    } 

//FIN FUNCION INSERT_SEDE
}

//FIN CLASE
$proceso = new ListarusuariosMoviles($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
?>