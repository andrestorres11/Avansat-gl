<?php
require "ajax_emptra_agenci.php";

class Ins_emptra_agenci {

    var $conexion, $usuario, $cod_aplica;
    private static $cFunciones;

    function __construct($co, $us, $ca) {

        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        self::$cFunciones = new agenci($co, $us, $ca);
        

        switch ($_REQUEST[opcion]) {
            case 1:
                $this->Formulario();
                break;

            default:
                $this->filtro();
                break;
        }
    }
    
    
/*! \fn: getTransp
 *  \brief: Trae las transportadoras
 *  \author: Ing. Fabian Salinas
 * \date: 17/06/2015
 * \date modified: dia/mes/año
 *  \param: 
 *  \return:
 */
public function getTransp()
{
    $mSql = "SELECT a.cod_tercer, UPPER(a.abr_tercer) AS nom_tercer 
      FROM ".BASE_DATOS.".tab_tercer_tercer a 
    INNER JOIN ".BASE_DATOS.".tab_tercer_activi b 
    ON a.cod_tercer = b.cod_tercer 
    WHERE b.cod_activi = ".COD_FILTRO_EMPTRA." 
    AND a.cod_estado = ".COD_ESTADO_ACTIVO."
    ";
    if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) 
    {#PARA EL FILTRO DE EMPRESA
    $filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_usuari] );
    if ( $filtro -> listar(  $this->conexion ) ) : 
    $datos_filtro = $filtro -> retornar();
    $mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
    endif;
    }else{#PARA EL FILTRO DE EMPRESA
    $filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_perfil] );
    if ( $filtro -> listar(  $this->conexion ) ) : 
    $datos_filtro = $filtro -> retornar();
    $mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
    endif;
    }
    $mSql .= " ORDER BY a.abr_tercer ASC ";
    $consulta = new Consulta( $mSql,  $this->conexion );
    return $mResult = $consulta -> ret_matrix('i');
}

    /*! \fn: filtro
     *  \brief: funcion inicial para buscar una transportadora
     *  \author: Ing. Alexander Correa
     *  \date: 09/09/2015
     *  \date modified: dia/mes/año
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
        $mHtml->SetJs("validator");
        $mHtml->SetJs("ajax_emptra_agenci");
        $mHtml->SetJs("InsertProtocolo");
        $mHtml->SetJs("new_ajax"); 
        $mHtml->SetJs("dinamic_list"); 
        $mHtml->SetCss("dinamic_list");
        $mHtml->CloseTable("tr");
        # incluye Css
        $mHtml->SetCssJq("jquery");
        $mHtml->Body(array("menubar" => "no"));
       
         
        $cod_transp = $this->getTransp();
        if(sizeof($cod_transp) == 1 ) {
          $and = "WHERE cod_transp = '".$cod_transp[0][0]."'";
        } else {
            $and ="";
        }
       
        #creo el acordeon para el filtro
        #<DIV fitro>
          #abre formulario
          $mHtml->Form(array("action" => "index.php",
              "method" => "post",
              "name" => "form_search",
              "header" => "Agencias",
              "enctype" => "multipart/form-data"));
            $mHtml->Row("td");
              $mHtml->OpenDiv("id:tablaID; class:contentAccordion");
                $mHtml->OpenDiv("id:tabla; class:accordion");
                  $mHtml->SetBody("<h1 style='padding:6px' ><b>LISTADO DE AGENCIAS</b></h1>");
                    $mHtml->OpenDiv("id:sec2");
                      $mHtml->OpenDiv("id:form3; class:contentAccordionForm");
                         $mSql = "SELECT  a.cod_agenci, UPPER(a.nom_agenci), UPPER(f.abr_tercer),CONCAT( UPPER(b.abr_ciudad), '(', LEFT(c.nom_depart, 4), ') - ', LEFT(d.nom_paisxx, 3) ) abr_ciudad,
                                          UPPER(a.dir_agenci), 
                                         a.tel_agenci,   
                                         if(a.cod_estado = 1, 'Activa', 'Inactiva') cod_estado, a.cod_estado cod_option
                                        FROM ".BASE_DATOS.".tab_genera_agenci a 
                                           INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b ON b.cod_ciudad = a.cod_ciudad 
                                           INNER JOIN ".BASE_DATOS.".tab_genera_depart c ON b.cod_depart = b.cod_depart 
                                           INNER JOIN ".BASE_DATOS.".tab_genera_paises d ON b.cod_paisxx = c.cod_paisxx
                                           INNER JOIN ".BASE_DATOS.".tab_transp_agenci e ON e.cod_agenci = a.cod_agenci
                                           INNER JOIN ".BASE_DATOS.".tab_tercer_tercer f ON f.cod_tercer = e.cod_transp $and                                       
                                        GROUP BY a.cod_agenci";

                                         
                        $_SESSION["queryXLS"] = $mSql;

                        if(!class_exists(DinamicList)) {
                          include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");                         
                        }
                        $list = new DinamicList( $this->conexion, $mSql, "cod_estado" , "no", 'ASC');
                        
                        $list->SetClose('no');
                        $list->SetCreate("Crear agencia", "onclick:formulario()");
                        $list->SetHeader(utf8_decode("Código de la agencia"), "field:a.cod_agenci; width:1%;  ");
                        $list->SetHeader("Agencia", "field:a.nom_agenci; width:1%");
                        $list->SetHeader("Transportadora", "field:f.abr_tercer; width:1%");
                        $list->SetHeader("Ciudad", "field:CONCAT( UPPER(b.abr_ciudad), '(', LEFT(c.nom_depart, 4), ') - ', LEFT(d.nom_paisxx, 3) ) ; width:1%");
                        $list->SetHeader(utf8_decode("Dirección"), "field:a.dir_agenci; width:1%");
                        $list->SetHeader(utf8_decode("Teléfono"), "field:a.tel_agenci; width:1%");
                        $list->SetHeader("Estado", "field:if(a.cod_estado = 1, 'Activa', 'Inactiva')" );
                        $list->SetOption("Opciones","field:cod_option; width:1%; onclikDisable:editarAgencia( 2, this ); onclikEnable:editarAgencia( 1, this ); onclikEdit:editarAgencia( 99, this )" );
                        $list->SetHidden("cod_agenci", "0" );
                        $list->SetHidden("nom_agenci", "1" );
                       

                        $list->Display($this->conexion);
                        $_SESSION["DINAMIC_LIST"] = $list;

                        $Html = $list -> GetHtml();
                        $mHtml->SetBody($Html);
                        
                      $mHtml->CloseDiv();
                    $mHtml->CloseDiv();
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();            
        #$mHtml->CloseRow("td");
      #fin de acordeon con la lista de las tranportadoras
    #</div>
           $mHtml->Hidden(array( "name" => "agenci[cod_agenci]", "id" => "cod_agenciID"));
           $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
           $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
           $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
           $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));
           $mHtml->Hidden(array( "name" => "agenci['nom_agenci']", "id" => "nom_agenciID", 'value'=>''));
        
     # Cierra formulario
          $mHtml->CloseForm();
        # Cierra Body
        $mHtml->CloseBody();

        # Muestra Html
        echo $mHtml->MakeHtml();
    }

    function Formulario() {
        $datos = self::$cFunciones->getDatosAgencia($_REQUEST['agenci']['cod_agenci']);
        $oculto = true;

       if(!$datos->cod_agenci){
          $datos->cod_agenci = self::$cFunciones->getConsecutivo();  
       }else{
        $oculto = false;
       }

       

        # Nuevo frame ---------------------------------------------------------------
        # Inicia clase del fromulario ----------------------------------------------------------------------------------
        $mHtml = new FormLib(2);

        # incluye JS
        $mHtml->SetJs("min");
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("functions");
        $mHtml->SetJs("config");
        $mHtml->SetJs("fecha");
        $mHtml->SetJs("ajax_emptra_agenci");
        $mHtml->SetJs("validator");
        $mHtml->SetCssJq("validator");    
        # incluye Css
        $mHtml->SetCssJq("jquery");

        # coloca titulo
        $mHtml->SetTitle("Crear Agencia");

        # Abre Body
        $mHtml->Body(array("menubar" => "no"));

        # Abre Form
        $mHtml->Form(array("action" => "index.php",
            "method" => "post",
            "name" => "form_transpor",
            "header" => "Agencias",
            "enctype" => "multipart/form-data"));

      #variables ocultas
      $mHtml->Hidden(array( "name" => "agenci[cod_ciudad]", "id" => "cod_ciudadID", "value"=>$datos->cod_ciudad)); //el codigo de la ciudad de la agencia
      $mHtml->Hidden(array( "name" => "agenci[cod_tercer]", "id" => "cod_tercerID", "value"=>$datos->cod_tercer)); //el codigo de la transportadora de la agencia
      $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
      $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
      $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
      $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));

          # Construye accordion
          $mHtml->Row("td");
          $mHtml->OpenDiv("id:contentID; class:contentAccordion");
            # Accordion1
            $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
              $mHtml->SetBody("<h3 style='padding:6px;'><center>Información de la Agencia</center></h3>");
            $mHtml->OpenDiv("id:sec1;");
              $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                $mHtml->Table("tr");
                  $mHtml->Label("Transportadora:", "width:25%; *:1;");
                    $mHtml->Input(array("type" => "alpha", "name" => "transp", "id" => "nom_transpID", "width" => "25%", "maxlength" => "100", "minlength"=>"5", "validate" => "dir", "obl" => "1", "value" => $datos->abr_tercer));
                    $mHtml->Label(utf8_decode("Código:"), "width:25%; :1;");
                    $mHtml->Input(array("type" => "numeric", "name" => "agenci[cod_agenci]", "id" => "num_verifiID", "width" => "10%", "disabled"=>true, "value" =>  $datos->cod_agenci, "end" => true));

                    $mHtml->Label("Nombre De La Agencia:", "width:25%; *:1;");
                    $mHtml->Input(array("type" => "alpha", "name" => "agenci[nom_agenci]", "validate" => "alpha", "obl" => "1", "minlength" => "5", "maxlength" => "50", "id" => "nom_agenciID", "width" => "25%", "value" => $datos->nom_agenci));

                    $mHtml->Label(utf8_decode("Ciudad:"), "width:25%; *:1;");
                    $mHtml->Input(array("type" => "alpha", "name" => "ciudad", "id" => "ciudadID", "size"=>30, "validate" => "dir",  "obl" => "1", "minlength" => "5", "maxlength" => "100", "width" => "100px", "value" => $datos->abr_ciudad, "end" => true));

                    $mHtml->Label(utf8_decode("Dirección:"), "width:25%; *:1;");
                    $mHtml->Input(array("type" => "address", "name" => "agenci[dir_agenci]", "validate" => "dir", "minlength" => "5", "maxlength" => "100", "id" => "abr_tercer", "obl" => "1", "width" => "25%", "value" => $datos->dir_agenci));
                    
                    $mHtml->Label(utf8_decode("Teléfono:"), "width:25%; *:1;");
                    $mHtml->Input(array("type" => "number", "name" => "agenci[tel_agenci]", "validate" => "numero",  "obl" => "1", "minlength" => "7", "maxlength" => "10", "id" => "tel_agenci", "width" => "25%", "value" => $datos->tel_agenci, "end" => true));
                    $mHtml->Label("Fax:", "width:25%; :1;");
                    $mHtml->Input(array("type" => "numeric", "name" => "agenci[num_faxxxx]", "validate" => "numero",  "minlength" => "7", "maxlength" => "10", "id" => "num_faxxxx", "width" => "25%", "value" => $datos->num_faxxxx));

                    $mHtml->Label("Email:", "width:25%; :1;"); 
                    $mHtml->Input(array("type" => "number", "name" => "agenci[dir_emailx]", "validate" => "email",  "minlength" => "7", "maxlength" => "50", "id" => "dir_emailxID", "width" => "25%", "value" => $datos->dir_emailx, "end" => true));
                    $mHtml->Label("Contacto:", "width:25%; *:1;");
                    $mHtml->Input(array("type" => "text", "name" => "agenci[con_agenci]", "id" => "con_agenciID","minlength" => "8", "obl"=>"1", "maxlength" => "100", "width" => "25%", "value" => $datos->con_agenci));
                    if($oculto == false){
                      if($datos->cod_estado == 1){
                        $mHtml->Label("Estado:", "width:25%; :1;");
                        $mHtml->Input(array("type" => "text", "name" => "estado", "id" => "estado","minlength" => "8", "maxlength" => "100", "width" => "25%", "value" => "Activa", "disabled"=>true,));
                      }else if($datos->cod_estado == 0){
                        $mHtml->Label("Estado:", "width:25%; :1;");
                        $mHtml->Input(array("type" => "text", "name" => "estado", "id" => "estado","minlength" => "8", "maxlength" => "100", "width" => "25%", "value" => "Inactiva", "disabled"=>true,));
                      }
                    }
                $mHtml->CloseTable("tr");
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
          # Fin accordion1 

          $mHtml->OpenDiv("id:DatosSecundariosID;");
            $mHtml->Table("tr");
            if(!$datos->nom_agenci){
              $mHtml->StyleButton("name:send; id:registrarID; value:Registrar; onclick:confirmar('registrar'); align:center;  class:crmButton small save");
              $mHtml->StyleButton("name:clear; id:borrarID; value:Borrar; onclick:borrar(); align:center;  class:crmButton small save");
            }else{
              $mHtml->StyleButton("name:send; id:modificarID; value:Actualizar; onclick:confirmar('modificar'); align:center;  class:crmButton small save");
              if($datos->cod_estado == 1){
                $mHtml->StyleButton("name:inactive; id:inactivarID; value:Inactivar; onclick:confirmar('inactivar'); align:center;  class:crmButton small save");
              }else{
                $mHtml->StyleButton("name:inactive; id:inactivarID; value:Activar; onclick:confirmar('activar'); align:center;  class:crmButton small save");
              }
              $mHtml->StyleButton("name:clear; id:cancelarID; value:Cancelar; onclick:cancelar(); align:center;  class:crmButton small save");
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

$proceso = new Ins_emptra_agenci($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>