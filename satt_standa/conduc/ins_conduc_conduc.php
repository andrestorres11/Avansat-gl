<?php
    /*ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);*/
   

     require "ajax_conduc_conduc.php";
 
class Ins_conduc_conduc {
   var $conexion, $usuario, $cod_aplica;
    private static $cFunciones, $cTransp;

    function __construct($co, $us, $ca) {
        include_once('../'.DIR_APLICA_CENTRAL.'/inform/class_despac_trans3.php');
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        self::$cFunciones = new conduc($co, $us, $ca);
        self::$cTransp = new Despac($co, $us, $ca);
        switch ($_REQUEST[opcion]) {
            case 1:
              $this->Formulario();
            break;

            case 2:
              $this->imprimir();
            break;

            default:
                $this->filtro();
                break;
        }
    }

    /*! \fn: imprimir
     *  \brief: funcion para imprimir los datos de un conductor
     *  \author: Ing. Alexander Correa
     *  \date: 17/09/2015
     *  \date modified: dia/mes/a�o
     *  \param: 
     *  \param: 
     *  \return 
     */
    function imprimir(){

        $datos = self::$cFunciones->getDatosConductor($_REQUEST['cod_conduc']);

        $mHtml = new FormLib(2);

        # incluye JS
        $mHtml->SetJs("min");
        $mHtml->SetJs("config");
        $mHtml->SetJs("functions");
        $mHtml->SetJs("ajax_conduc_conduc");
        $mHtml->SetJs("printArea");
        $mHtml->SetJs("bootstrap"); 
        $mHtml->SetJs("Jsgauge"); 
        $mHtml->SetJs("Excanvas"); 
        $mHtml->SetCss("bootstrap");
        $mHtml->SetJs("jquery");
        $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
        $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
        $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
        $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));
        
        echo $mHtml->MakeHtml(); 
        ?>
        <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/print.js"></script>
        <table id="imprimir" name="imprimir">
          <tr style="text-align:center;border: 2px solid  #8A8683;">
          <?php  if($_REQUEST['cod_transp'] != "900491068"){?>
            <td colspan="2" width="400px" style="border-right: 2px solid #8A8683;" ><img src="../<?= DIR_APLICA_CENTRAL ?>/imagenes/logo-sat-color.png"/></td>
            <?php }else{ ?>
            <td colspan="2" width="400px" style="border-right: 2px solid #8A8683;" ><img src="../<?= DIR_APLICA_CENTRAL ?>/imagenes/capital.jpg"/></td>
            <?php } ?>
            <td colspan="4" width="600px"><h2><?= $_REQUEST['trasp']['nom_transp']?></h2></td>
          </tr>
          <tr style="text-align:center; border: 2px solid  #8A8683;"><td colspan="6" width="1200px"><br><h2>HOJA DE VIDA DEL CONDUCTOR</h2><br></td></tr>
          <tr style="text-align:left; border: 2px solid  #8A8683;"><td colspan="6" width="1200px"><br><b>Fecha de Impresi�n:</b> <?= date("Y-m-d")?><br></td></tr>
          <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683;">
            <tabla>
              <tr style="border-left: 2px solid  #8A8683; border-right:2px solid  #8A8683;">
                <td  colspan="6" style="text-align:center"><b>INFORMACI�N GENERAL</b><br></td>
              </tr>
              <tr style="border-left: 2px solid  #8A8683; border-right:2px solid  #8A8683;">
                <td  colspan="6" style="text-align:center">&nbsp;</td>
              </tr>
              <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683;" colspan="6" width="1200px">
                <td width="200px" style="text-align:right"><b>Tipo de Documento:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?php if($datos->principal->cod_tipdoc == 'C'){ Echo "C�DULA DE CIUDADANIA";}else{echo "C.C EXTRANGER�A";} ?></td>
                <td width="200px" style="text-align:right"><b>N�mero de Documento:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $datos->principal->cod_tercer ?></td>
                <td width="200px" style="text-align:right">&nbsp;</td>
                <td width="200px" style="text-align:right">&nbsp;</td>
              </tr>
              <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683;" colspan="6" width="1200px">
                <td width="200px" style="text-align:right"><b>Nombres:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($datos->principal->nom_tercer)  ?></td>
                <td width="200px" style="text-align:right"><b>Primer Apellido:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($datos->principal->nom_apell1) ?></td>
                <td width="200px" style="text-align:right"><b>Segundo Apellido:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($datos->principal->nom_apell2) ?></td>
              </tr>
              <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683;" colspan="6" width="1200px">
                <td width="200px" style="text-align:right"><b>Sexo:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?php if($datos->principal->cod_tipsex == 1){ Echo "MASCULINO";}else{echo "FEMENINO";} ?></td>
                <td width="200px" style="text-align:right"><b>Factor RH:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $datos->principal->cod_grupsa ?></td>
                <td width="200px" style="text-align:right">&nbsp;</td>
                <td width="200px" style="text-align:right">&nbsp;</td>
              </tr>
              <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683;" colspan="6" width="1200px">
                <td width="200px" style="text-align:right"><b>Direcci�n de Residencia:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($datos->principal->dir_domici) ?></td>
                <td width="200px" style="text-align:right"><b>Ciudad de Resudencia:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $datos->principal->abr_ciudad ?></td>
                <td width="200px" style="text-align:right">&nbsp;</td>
                <td width="200px" style="text-align:right">&nbsp;</td>
              </tr>
              <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683;" colspan="6" width="1200px">
                <td width="200px" style="text-align:right"><b>Tel�fono 1:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $datos->principal->num_telef1  ?></td>
                <td width="200px" style="text-align:right"><b>Tel�fono 2:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $datos->principal->num_telef2 ?></td>
                <td width="200px" style="text-align:right"><b>Tel�fono M�vil:</b></td>
                <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $datos->principal->num_telmov ?></td>
              </tr>
            </tabla>
          </tr>
          <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-top: 2px solid  #8A8683;">
             <td colspan="6" style="text-align:center"><b>DATOS DE LA LICENCIA</b></td>
          </tr>
          <tr style="border-left: 2px solid  #8A8683; border-right:2px solid  #8A8683;" >
                <td  colspan="6" style="text-align:center">&nbsp;</td>
          </tr>
          <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-bottom: 2px solid  #8A8683;">
            <td width="200px" style="text-align:right"><b>N� de Licencia:</b></td>
            <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $datos->principal->num_licenc  ?></td>
            <td width="200px" style="text-align:right"><b>Categoria:</b></td>
            <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $datos->principal->num_catlic ?></td>
            <td width="200px" style="text-align:right"><b>Fecha de Vencimiento:</b></td>
            <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $datos->principal->fec_venlic ?></td>
          </tr>
          <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-top: 2px solid  #8A8683;">
            <td colspan="6" style="text-align:center"><b>DATOS DE SEGURIDAD SOCIAL</b></td>
          </tr>
          <tr style="border-left: 2px solid  #8A8683; border-right:2px solid  #8A8683;" >
            <td  colspan="6" style="text-align:center">&nbsp;</td>
          </tr>
          <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-bottom: 2px solid  #8A8683;">
            <td width="200px" style="text-align:right"><b>EPS:</b></td>
            <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($datos->principal->nom_epsxxx) ?></td>
            <td width="200px" style="text-align:right"><b>ARL:</b></td>
            <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($datos->principal->nom_arpxxx) ?></td>
            <td width="200px" style="text-align:right"><b>Fondo de Pensiones:</b></td>
            <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($datos->principal->nom_pensio) ?></td>
          </tr>
          <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-top: 2px solid  #8A8683;">
            <td colspan="6" style="text-align:center"><b>REFERENCIA PERSONAL</b></td>
          </tr>
          <tr style="border-left: 2px solid  #8A8683; border-right:2px solid  #8A8683;" >
                <td  colspan="6" style="text-align:center">&nbsp;</td>
              </tr>
         
          <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-bottom: 2px solid  #8A8683;">
            <td width="200px" style="text-align:right"><b>Nombres:</b></td>
            <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($datos->principal->nom_refper)  ?></td>
            <td width="200px" style="text-align:right"><b>Tel�fono:</b></td>
            <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $datos->principal->tel_refper ?></td>
            <td width="200px" style="text-align:right">&nbsp;</td>
            <td width="200px" style="text-align:left">&nbsp;</td>
          </tr>
          <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-top: 2px solid  #8A8683;">
            <td colspan="6" style="text-align:center"><b>REFERENCIAS LABORALES</b></td>
          </tr>
          <tr style="border-left: 2px solid  #8A8683; border-right:2px solid  #8A8683;" >
                <td  colspan="6" style="text-align:center">&nbsp;</td>
              </tr>
           
          <?php foreach ($datos->referencias as $key => $value) { ?>
             <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683;">
              <td width="200px" style="text-align:right"><b>Empresa:</b></td>
              <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($value->nom_empre) ?></td>
              <td width="200px" style="text-align:right"><b>Tel�fono:</b></td>
              <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $value->tel_empre ?></td>
              <td width="200px" style="text-align:right">&nbsp;</td>
              <td width="200px" style="text-align:left">&nbsp;</td>
            </tr>
            <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-bottom: 2px solid  #8A8683;">
              <td width="200px" style="text-align:right"><b>viajes:</b></td>
              <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= $value->num_viajes ?></td>
              <td width="200px" style="text-align:right"><b>Antig�edad:</b></td>
              <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($value->num_atigue) ?></td>
              <td width="200px" style="text-align:right"><b>Mercancia:</b></td>
              <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($value->nom_mercan) ?></td>
            </tr>
           <?php } ?>
            <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-top: 2px solid  #8A8683;">
              <td colspan="6" style="text-align:center"><b>OBSERVACIONES</b></td>
            </tr>
            <tr style="border-left: 2px solid  #8A8683; border-right:2px solid  #8A8683;" >
                <td  colspan="6" style="text-align:center">&nbsp;</td>
              </tr>
            
            <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-bottom: 2px solid  #8A8683;">
              <td width="200px" style="text-align:right"><b>Observaciones Generales:</b></td>
              <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($datos->principal->obs_tercer)  ?></td>
              <td width="200px" style="text-align:right"><b>Observaciones Hab/Inh:</b></td>
              <td width="200px" style="text-align:left">&nbsp;&nbsp;<?= strtoupper($datos->principal->obs_habinh) ?></td>
              <td width="200px" style="text-align:right">&nbsp;</td>
              <td width="200px" style="text-align:left">&nbsp;</td>
            </tr>
             <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-top: 2px solid  #8A8683;">
              <td colspan="6" style="text-align:center"><b>Foto del Conductor</b></td>
            </tr>
            <tr style="border-left: 2px solid  #8A8683; border-right:2px solid  #8A8683;" >
                <td  colspan="6" style="text-align:center">&nbsp;</td>
            </tr>
            <tr style=" border-left: 2px solid  #8A8683;border-right:2px solid  #8A8683; border-bottom: 2px solid  #8A8683;">
              <td colspan="2" style="text-align:center"><br><br><br><br><br>_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _<br>Firma y C�dula del Conductor</b></b></td>
              <td colspan="2" style="text-align:center"><?php if($datos->principal->dir_ultfot != "NULL" && $datos->principal->dir_ultfot != NULL && $datos->principal->dir_ultfot != ""){ ?><img  width="128px"; height="128px" src="<?= $datos->principal->dir_ultfot ?>"/> <?php }else { ?> <img src="imagenes/silueta.png"/> <?php } ?></td>
              <td colspan="2" style="text-align:center"><img src="../<?= DIR_APLICA_CENTRAL ?>/imagenes/huella.png"/><br><b>Huella</b></td>              
            </tr>
        </table>
        <div id="botones">
          <br><br>
          <table>
            <tr>
              <td width="600px"  style="text-align:center"><input id="imprimirBTN" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" type="button" onclick="ocultarBotones(); window.print(); mostrarBotones();" value="Imprimir"></td>
              <td width="600px"  style="text-align:center"><input id="cancelarID" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" type="button" onclick="closed()" value="Cancelar" ></td>
            </tr>
          </table>
          <br><br>
        </div>
        <div id="editor"></div>

        <?php

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
        
        //verifico si es un usuario de una transportadora o administrador
        $transp = self::$cTransp->getTransp();

        $total = count($transp);
        if( $total == 1 ){
          $mCodTransp = $transp[0][0];
          $mNomTransp = $transp[0][1];
        }

        /*if($_SESSION[datos_usuario][cod_usuari] == "TRANSMER"){
          echo "<pre>";
          print_r($transp);
          echo "</pre>";
        }*/

        $mHtml = new FormLib(2);

         # incluye JS
        $mHtml->SetJs("min");
        $mHtml->SetJs("config");
        $mHtml->SetJs("fecha");
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("functions");
        $mHtml -> SetBody(' <script src="../'.DIR_APLICA_CENTRAL.'/js/ajax_conduc_conduc.js"></script> ');
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
      
        $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
        $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
        $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
        $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));
        $mHtml->Hidden(array( "name" => "cod_transp", "id" => "cod_transpID", 'value'=>$mCodTransp));
        $mHtml->Hidden(array( "name" => "cod_conduc", "id" => "cod_conducID", 'value'=>''));
        $mHtml->Hidden(array( "name" => "nom_conduc", "id" => "nom_conducID", 'value'=>''));
        $mHtml->Hidden(array( "name" => "resultado", "id" => "resultado", 'value'=>$_REQUEST['resultado']));
        $mHtml->Hidden(array( "name" => "opera", "id" => "opera", 'value'=>$_REQUEST['operacion']));
        $mHtml->Hidden(array( "name" => "conductor", "id" => "conductor", 'value'=>$_REQUEST['conductor']));
        $mHtml->Hidden(array( "name" => "total", "id" => "total", 'value'=>$total));

          # Construye accordion
          $mHtml->Row("td");
            $mHtml->OpenDiv("id:contentID; class:contentAccordion");
            if( $total != 1 ){
              # Accordion1
              $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
                $mHtml->SetBody("<h1 style='padding:6px'><b>AGREGAR CONDUCTORES</b></h1>");
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
            }
              # Fin accordion1    
              # Accordion2
              $mHtml->OpenDiv("id:datos; class:accordion");
                $mHtml->SetBody("<h1 style='padding:6px'><b>LISTADO DE CONDUCTORES</b></h1>");
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
        
        $datos = self::$cFunciones->getDatosConductor($_REQUEST['cod_conduc'], $_REQUEST['cod_transp']);
        # Nuevo frame ---------------------------------------------------------------
        # Inicia clase del fromulario ----------------------------------------------------------------------------------
        $mHtml = new FormLib(2);
        
        # incluye JS
        $mHtml->SetJs("min");
        $mHtml->SetJs("config");
        $mHtml->SetJs("fecha");
        $mHtml->SetJs("jquery");
        $mHtml->SetJs("functions");
        $mHtml -> SetBody(' <script src="../'.DIR_APLICA_CENTRAL.'/js/ajax_conduc_conduc.js"></script> ');
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
        $mHtml->Form(array("action" => "../".DIR_APLICA_CENTRAL."/conduc/ajax_conduc_conduc.php",
            "method" => "post",
            "name" => "form_transpor",
            "header" => "Transportadoras",
            "enctype" => "multipart/form-data"));

      $query = "SELECT a.cod_paisxx, b.nom_paisxx
        FROM ".BASE_DATOS.".tab_tercer_tercer a
        LEFT JOIN ".BASE_DATOS.".tab_genera_paises b ON a.cod_paisxx = b.cod_paisxx 
          WHERE cod_tercer = '".$_REQUEST['cod_transp']."'
        LIMIT 1";
      $consulta = new Consulta($query, $this->conexion);
  
      if($datos->principal->cod_paisxx=='' || $datos->principal->nom_paisxx==NULL){
        $cod_paisxx = $consulta->ret_matriz("a")[0]['cod_paisxx'];
        $nom_paisxx = $consulta->ret_matriz("a")[0]['nom_paisxx'];
      }else{
        $cod_paisxx = $datos->principal->cod_paisxx;
        $nom_paisxx = $datos->principal->abr_ciudad;
      }

      $pai_config = self::$cFunciones->darPaisConfig();

      if($cod_paisxx==''){
        $cod_paisxx = ($pai_config['cod_paisxx'] != '' || $pai_config['cod_paisxx'] != NULL) ? $pai_config['cod_paisxx'] : '';
      }
      if($nom_paisxx==''){
        $nom_paisxx = ($pai_config['nom_paisxx'] != '' || $pai_config['nom_paisxx'] != NULL || $datos->principal->nom_paisxx == NULL || $datos->principal->nom_paisxx == '') ? strtoupper($pai_config['nom_paisxx']) : '';
      }



      #variables ocultas
      $mHtml->Hidden(array( "name" => "conduc[cod_ciudad]", "id" => "cod_ciudadID", "value"=>$datos->principal->cod_ciudad)); //el codigo de la ciudad
      $mHtml->Hidden(array( "name" => "conduc[cod_transp]", "id" => "cod_transpID", "value"=>$_REQUEST['cod_transp'])); //el codigo de la transportadora
      $mHtml->Hidden(array( "name" => "conduc[cod_paisxx]", "id" => "cod_paisxxID", "value"=>$cod_paisxx)); //el codigo de la transportadora
      $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
      $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
      $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
      $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));
      $mHtml->Hidden(array( "name" => "conduc[cantidad]", "id" => "cantidadID", 'value'=>count($datos->referencias)));
      $mHtml->Hidden(array( "name" => "conduc[control]", "id" => "controlID", 'value'=>count($datos->referencias))); //variable para saber si a�aden mas experiencias laborales
      $mHtml->Hidden(array( "name" => "abr_terer", "id" => "abr_terer", 'value'=>$datos->principal->abr_tercer));
      $mHtml->Hidden(array( "name" => "imagen", "id" => "imagen", 'value'=>"1"));
      $mHtml->Hidden(array( "name" => "Ajax", "id" => "Ajax", 'value'=>"on"));
      $mHtml->Hidden(array( "name" => "operacion", "id" => "operacion", 'value'=>""));
      $mHtml->Hidden(array( "name" => "url", "id" => "url", 'value'=>"../".NOM_URL_APLICA."/".$datos->principal->dir_ultfot));
      
      

      $inputs = self::inputsByCountry()[$cod_paisxx];

      $disabled = "";
      if($datos->principal->cod_tercer){
        $disabled = "'disabled'=>true";
      }
          # Construye accordion
          $mHtml->Row("td");
          $mHtml->OpenDiv("id:contentID; class:contentAccordion");
            # Accordion1
            $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
              $mHtml->SetBody("<h3 style='padding:6px;'><center>Datos Básicos</center></h3>");
            $mHtml->OpenDiv("id:sec1;");
              $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                $mHtml->Table("tr");

                    $mHtml->Label("Pais:", "width:25%; *:1;"); 
                    $mHtml->Input (array("name" => "pais", "validate" => "dir",  "obl" => "1", "id" => "paisID",  "minlength" => "3", "maxlength" => "100", "width" => "25%", "value"=> $nom_paisxx, "end" => true, "onclick"=>"limpiarInput(this)") );

                    $mHtml->Label("Tipo de Documento:", "width:25%; *:1;");
                    $mHtml->Select2 ($datos->tipoDocumento,  array("name" => "conduc[cod_tipdoc]", "validate" => "select",  "obl" => "1", "id" => "cod_tipdocID", "width" => "25%", "key"=> $datos->principal->cod_tipdoc) );
                    $mHtml->Label("Número de Documento:", "width:25%; :1;");
                    $mHtml->Input(array("type" => $inputs['validation']['type'], "name" => "conduc[cod_tercer]", "id" => "cod_tercerID", "onblur"=>"comprobar()", "width" => "10%", "obl" => "1", "minlength" => "5", "maxlength" => "13", "validate" => $inputs['validation']['validate'], "value" =>  $datos->principal->cod_tercer, $disabled, "end" => true));

                    $mHtml->Label("Nombres:", "width:25%; *:1;");
                    $mHtml->Input(array("type" => "alpha", "name" => "conduc[nom_tercer]", "validate" => "alpha", "obl" => "1", "minlength" => "4", "maxlength" => "50", "id" => "nom_tercerID", "width" => "25%", "value" => $datos->principal->nom_tercer));

                    $mHtml->Label(("Primer Apellido:"), "width:25%; *:1;");
                    $mHtml->Input(array("type" => "alpha", "name" => "conduc[nom_apell1]", "id" => "nom_apell1", "validate" => "alpha",  "obl" => "1", "minlength" => "4", "maxlength" => "20", "width" => "100px", "value" => $datos->principal->nom_apell1, "end" => true));

                    $mHtml->Label(("Segundo Apellido:"), "width:25%; :1;");
                    $mHtml->Input(array("type" => "alpha", "name" => "conduc[nom_apell2]", "validate" => "alpha", "minlength" => "4", "maxlength" => "20", "id" => "nom_apell2", "width" => "25%", "value" => $datos->principal->nom_apell2));
                    $mHtml->Label("Factor RH:", "width:25%; :1;");
                    $mHtml->Select2 ($datos->grupoSanguineo,  array("name" => "conduc[cod_grupsa]", "validate" => "select",  "id" => "cod_grupsaID", "width" => "25%", "key"=> $datos->principal->cod_grupsa, "end" => true) );
                    
                    $mHtml->Label(("Género:"), "width:25%; *:1;");
                    $mHtml->Select2 ($datos->genero,  array("name" => "conduc[cod_tipsex]", "validate" => "select",  "obl" => "1", "id" => "cod_tipsexID", "width" => "25%", "key"=> $datos->principal->cod_terreg) );
                    $mHtml->Label("Dirección:", "width:25%; *:1;");
                    $mHtml->Input(array("name" => "conduc[dir_domici]", "validate" => "dir",  "obl" => "1", "minlength" => "10", "maxlength" => "100", "id" => "dir_domici", "width" => "25%", "value" => $datos->principal->dir_domici, "end" => true));

                    $mHtml->Label("Ciudad:", "width:25%; *:1;"); 
                    $mHtml->Input (array("name" => "ciudad", "validate" => "dir",  "obl" => "1", "id" => "ciudadID",  "minlength" => "7", "maxlength" => "100", "width" => "25%", "value"=> $datos->principal->abr_ciudad) );
                    $mHtml->Label("Télefono 1:", "width:25%; *:1;");                   
                    $mHtml->Input(array("type" => "numeric", "name" => "conduc[num_telef1]", "id" => "num_telef1", "minlength" => "7", "maxlength" => "10", "width" => "25%", "value" => $datos->principal->num_telef1,  "end" => true));

                    $mHtml->Label("Télefono 2:", "width:25%; :1;");                   
                    $mHtml->Input(array("type" => "numeric", "name" => "conduc[num_telef2]", "id" => "num_telef2", "minlength" => "7", "maxlength" => "10", "width" => "25%", "value" => $datos->principal->num_telef2));
                    $mHtml->Label("Teléfono Móvil:", "width:25%; *:1;");                   
                    $mHtml->Input(array("type" => "numeric", "name" => "conduc[num_telmov]", "id" => "num_telmov", "minlength" => "10", "maxlength" => "10", "width" => "25%", "value" => $datos->principal->num_telmov,  "end" => true));
                    
                    $mHtml->Label("Operador:", "width:25%; :1;");
                    $mHtml->Select2 ($datos->operador,  array("name" => "conduc[cod_operad]", "validate" => "select",  "obl" => "1", "id" => "cod_operadID", "width" => "25%", "key"=> $datos->principal->cod_operad) );
                    $mHtml->Label("Foto:", "width:25%; :1;");
                    $mHtml->File(array("name" => "foto",  "id" => "foto", "validate"=>"file", "format"=>"jpg,jpeg,png", "width" => "25%", "end" => true) );
                    
                    $mHtml->Label("Calificación:", "width:25%; :1;");
                    $mHtml->Select2 ($datos->calificacion,  array("name" => "conduc[cod_califi]", "validate" => "select",  "id" => "cod_califiID", "width" => "25%", "key"=> $datos->principal->cod_califi) );
                $mHtml->CloseTable("tr");
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
          # Fin accordion1    
          # Accordion2
          $mHtml->OpenDiv("id:DatosSecundariosID; class:accordion");
          $mHtml->SetBody("<h3 style='padding:6px;'><center>Datos de la Licencia</center></h3>");
          $mHtml->OpenDiv("id:sec2");
            $mHtml->OpenDiv("id:form2; class:contentAccordionForm");
              $mHtml->Table("tr");
                $mHtml->Label(("Número de Licencia:"), "width:25%; :1;");
                  $mHtml->Input(array("type" => "text", "name" => "conduc[num_licenc]", "id" => "num_licencID","minlength" => "7", "maxlength" => "10", "width" => "25%", "value" => $datos->principal->num_licenc));
                  $mHtml->Label("Categoria:", "width:25%; :1;");
                  $mHtml->Select2 ($datos->categorias,  array("name" => "conduc[num_catlic]", "validate" => "select",  "id" => "num_catlicID", "width" => "25%", "key"=> $datos->principal->num_catlic, "end" => true) );
                  $mHtml->Label("Fecha de Vencimiento:", "width:25%; :1;");
                  $mHtml->Input(array("type" => "date", "obl" => "yes", "name" => "conduc[fec_venlic]", "id" => "fec_venlicID", "width" => "25%", "maxlength" => "10", "value" => $datos->principal->fec_venlic, "end" => true));
              $mHtml->CloseTable("tr");
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
          # Fin accordion2
          # Accordion3
          $mHtml->OpenDiv("id:DatosSecundariosID; class:accordion");
          $mHtml->SetBody("<h3 style='padding:6px;'><center>Seguridad Social</center></h3>");
            $mHtml->OpenDiv("id:sec3");
              $mHtml->OpenDiv("id:form3; class:contentAccordionForm");
                $mHtml->Table("tr");
                  $mHtml->Label("Nombre Entidad EPS:", "width:25%; :1;");
                  $mHtml->Input(array("type" => "text", "name" => "conduc[nom_epsxxx]", "id" => "nom_epsxxx","minlength" => "5", "maxlength" => "30", "width" => "25%", "value" => $datos->principal->nom_epsxxx));
                  $mHtml->Label("Nombre Entidad ARP:", "width:25%; :1;");
                  $mHtml->Input(array("type" => "text", "name" => "conduc[nom_arpxxx]", "id" => "nom_arpxxx","minlength" => "5", "maxlength" => "30", "width" => "25%", "value" => $datos->principal->nom_arpxxx, "end"=>true));
                  $mHtml->Label("Fondo de Pensiones:", "width:25%; :1;");
                  $mHtml->Input(array("type" => "text", "name" => "conduc[nom_pensio]", "id" => "nom_pensio","minlength" => "5", "maxlength" => "30", "width" => "25%", "value" => $datos->principal->nom_pensio));                  
                $mHtml->CloseTable("tr");
              $mHtml->CloseDiv();
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
          # fin Accordion 3
          #Acordeon 4
          $mHtml->OpenDiv("id:DatosSecundariosID; class:accordion");
          $mHtml->SetBody("<h3 style='padding:6px;'><center>Referencia Personal en Caso de Accidente</center></h3>");
          $mHtml->OpenDiv("id:sec4");
            $mHtml->OpenDiv("id:form4; class:contentAccordionForm");
              $mHtml->Table("tr");
                  $mHtml->Label("Nombre:", "width:25%; :1;");
                  $mHtml->Input(array("type" => "text", "obl" => "yes", "id"=>"nom_refper", "validate" => "dir", "name" => "conduc[nom_refper]", "width" => "25%", "maxlength" => "50", "value" => $datos->principal->nom_refper));
                  $mHtml->Label("Teléfono:", "width:25%; :1;");
                  $mHtml->Input(array("type" => "text", "obl" => "yes", "id"=>"tel_refper", "validate" => "numero", "name" => "conduc[tel_refper]", "width" => "25%", "minlength" => "7", "maxlength" => "10", "value" => $datos->principal->tel_refper, "end" => true));
              $mHtml->CloseTable("tr");
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
          #fin Acordeon 4
        #Acordeon 5
        $mHtml->OpenDiv("id:DatosSecundariosID; class:accordion");
        $mHtml->SetBody("<h3 style='padding:6px;'><center>Referencias Laborales</center></h3>");
          $mHtml->OpenDiv("id:sec5");
            $mHtml->OpenDiv("id:form5; class:contentAccordionForm");
              $mHtml->Table("tr");
                if(!$datos->referencias){
                      $mHtml->Label(" Empresa:", "width:25%; :1;");
                      $mHtml->Input(array("type" => "text", "type" => "alpha", "validate" => "alpha", "minlength" => "5", "maxlength" => "100", "name" => "empresa[0]", "id" => "empresa0", "width" => "25%", "value" => $datos->principal->nom_agenci, "end" => true));
                      $mHtml->Label("Teléfono:", "width:25%; :1;");
                      $mHtml->Input(array("type" => "numbre", "validate" => "numero", "minlength" => "7", "maxlength" => "10", "name" => "telefono[0]", "id" => "telefono0", "width" => "25%",  "value" => $datos->principal->abr_ciudaa));
                      $mHtml->Label("Viajes:", "width:25%; :1;");
                      $mHtml->Input(array("type" => "numbre","validate" => "numero",  "minlength" => "1", "maxlength" => "4", "name" => "viajes[0]", "id" => "viajes0", "width" => "25%", "value" => $datos->principal->dir_agenci, "end" => true));
                      $mHtml->Label("Antigüedad:", "width:25%; :1;");
                      $mHtml->Input(array("type" => "text", "validate" => "alpha",  "minlength" => "5", "maxlength" => "50", "name" => "antiguedad[0]", "id" => "antiguedad0", "width" => "25%",  "value" => $datos->principal->con_agenci));
                      $mHtml->Label(" Mercancia:", "width:25%; :1;");
                      $mHtml->Input(array("type" => "text", "validate" => "dir",  "minlength" => "7", "maxlength" => "50", "name" => "mercancia[0]", "id" => "mercancia0", "width" => "25%", "value" => $datos->principal->tel_agenci, "end" => true));
                }else{
                  foreach ($datos->referencias as $key => $value) {
                      $mHtml->Label(" Empresa:", "width:25%; :1;");
                      $mHtml->Input(array("type" => "text", "type" => "alpha", "validate" => "alpha", "minlength" => "5", "maxlength" => "100", "name" => "empresa[$key]", "id" => "empresa$key", "width" => "25%", "value" =>$value->nom_empre, "end" => true));
                      $mHtml->Label("Teléfono:", "width:25%; :1;");
                      $mHtml->Input(array("type" => "numbre", "validate" => "numero", "minlength" => "7", "maxlength" => "10", "name" => "telefono[$key]", "id" => "telefono$key", "width" => "25%",  "value" => $value->tel_empre));
                      $mHtml->Label("Viajes:", "width:25%; :1;");
                      $mHtml->Input(array("type" => "numbre","validate" => "numero",  "minlength" => "1", "maxlength" => "4", "name" => "viajes[$key]", "id" => "viajes$key", "width" => "25%", "value" =>$value->num_viajes, "end" => true));
                      $mHtml->Label("Antigüedad:", "width:25%; :1;");
                      $mHtml->Input(array("type" => "text", "validate" => "alpha",  "minlength" => "5", "maxlength" => "50", "name" => "antiguedad[$key]", "id" => "antiguedad$key", "width" => "25%",  "value" => $value->num_atigue));
                      $mHtml->Label(" Mercancia:", "width:25%; :1;");
                      $mHtml->Input(array("type" => "text", "validate" => "dir",  "minlength" => "7", "maxlength" => "50", "name" => "mercancia[$key]", "id" => "mercancia$key", "width" => "25%", "value" => $value->nom_mercan, "end" => true));
                      if($key <= count($datos->referencias)){
                        $mHtml->SetBody("<tr><td colspan='4'><hr></td></tr>");

                      }
                  }
                }
              $mHtml->CloseTable("tr");
            $mHtml->CloseDiv();             
            $mHtml->SetBody("<br><div style='text-align:center'><input type='button' name='otra' id='otraID' onclick='agregarExperiencia()' value='    Otra    '></div>");
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        #fin Acordeon 5
        #Acordeon 6
        $mHtml->OpenDiv("id:DatosSecundariosID; class:accordion");
        $mHtml->SetBody("<h3 style='padding:6px;'><center>Otras Actividades</center></h3>");
          $mHtml->OpenDiv("id:sec6");
            $mHtml->OpenDiv("id:form6; class:contentAccordionForm");
              $mHtml->Table("tr");
                $mHtml->Label(" Propietario:", "width:25%; :1;");
                $mHtml->CheckBox(array("name" => "conduc[cod_propie]", "id"=>"cod_propie", "checked"=>($datos->cod_propie == 1 ? true : false), "width" => "25%", "value" =>1));
                $mHtml->Label("Poseedor:", "width:25%; :1;");
                $mHtml->CheckBox(array("name" => "conduc[cod_tenedo]", "id"=>"cod_tenedo", "checked"=>($datos->cod_tenedo == 1 ? true : false), "width" => "25%", "value" =>1,"end"=>true));
                $mHtml->Label("Observaciones:", "width:25%; :1;");
                $mHtml->TextArea($datos->principal->obs_tercer, array("cols" => 100, "rows" => 8, "colspan" => "3", "name" => "conduc[obs_tercer]", "id" => "obs_tercer", "width" => "25%",  "end" => true));
              $mHtml->CloseTable("tr");
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        #fin Acordeon 6

        if($datos->principal->dir_ultfot != "NULL" && $datos->principal->dir_ultfot != "" && $datos->principal->dir_ultfot != null){
          #Acordeon 7 Para mostrar la foto del coductor
        $mHtml->OpenDiv("id:fotoID; class:accordion");
        $mHtml->SetBody("<h3 style='padding:6px;'><center>Foto del Conductor</center></h3>");
          $mHtml->OpenDiv("id:sec7");
            $mHtml->OpenDiv("id:form7; class:contentAccordionForm");
              $mHtml->Table("tr");
                $mHtml->SetBody("<div style='text-align:center; '><img style='cursor:pointer;' width='120px' onclick='imagen()' height='120px' src='../".NOM_URL_APLICA."/".$datos->principal->dir_ultfot."'/></img></div>");
              $mHtml->CloseTable("tr");
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        #fin Acordeon 7
        }

        $mHtml->OpenDiv("id:DatosSecundariosID;");
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
    }

//FIN FUNCION INSERT_SEDE

function inputsByCountry(){
  $inputs = array();
  
  $colombia = array('validation' => array('type' => 'numeric', 'validate' => 'numero'));
  $chile = array('validation' => array('type' => 'text', 'validate' => ''));

  $inputs[3] = $colombia;
  $inputs[11] = $chile;

  return $inputs;
}

}

//FIN CLASE
$proceso = new Ins_conduc_conduc($this -> conexion, $this -> usuario_aplicacion, $this -> codigo);
?>