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

//FIN FUNCION INSERT_SEDE
}

//FIN CLASE
$proceso = new ListarusuariosMoviles($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
?>