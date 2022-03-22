<?php
/*! \file: inf_formul_hojeal.php
 *  \brief: Reporte de hojas de vida Eal
 *  \author: Edward Fabian Serrano
 *  \author: edward.serrano@intrared.net
 *  \version: 1.0
 *  \date: 11/07/2017
 *  \bug: 
 *  \warning: 
 */

/*! \class: hojaVidaEal
 *  \brief: Lista configuracion hojas de vida EAL
 */
class repHojaVidaEal
{
	private static  $cConexion,
					        $cCodAplica,
					        $cUsuario;
					
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
            case 'getReporteGeneral':
                self::getReporteGeneral();
              break;
            case 'getDetailEal':
                self::getDetailEal();
              break;
            case 'exprtExcel':
                self::exprtExcel();
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
            IncludeJS( 'jquery17.js' );
            IncludeJS( 'jquery.js' );
              
            IncludeJS( 'functions.js' );
            IncludeJS( 'inf_formul_hojeal.js' );
            IncludeJS( 'dinamic_list.js' );
            IncludeJS( 'new_ajax.js' );
            IncludeJS( 'validator.js' );

            IncludeJS( 'jquery.multiselect.filter.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );
            IncludeJS( 'jquery.multiselect.min.js', '../'.DIR_APLICA_CENTRAL.'/js/multiselect/' );

            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css' type='text/css'>\n";
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/validator.css' type='text/css'>\n";

            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
            echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";

            $mHtml = new FormLib(2);

            $mHtml->Body(array("menubar" => "no"));

            # Abre Form
            $mHtml->Form(array("action" => "index.php", "method" => "post", "name" => "form_search", "header" => "EAL", "enctype" => "multipart/form-data"));

            #variables ocultas
          
            $mHtml->Hidden(array( "name" => "cod_tercer", "id" => "cod_tercerID", 'value'=>$mCodTransp));
            $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
            $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
            $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
            $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>$_REQUEST['opcion']));
            $mHtml->Hidden(array( "name" => "tExporExcel", "id" => "tExporExcelID")); 

            # Construye accordion
            $mHtml->Row("td");
              $mHtml->OpenDiv("id:contentID; class:contentAccordion");
                # Accordion1
                $mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
                  $mHtml->SetBody("<h1 style='padding:6px'><b>Informe General las EAL</b></h1>");
                  $mHtml->OpenDiv("id:sec1");
                    $mHtml->OpenDiv("id:form1; class:contentAccordionForm");
                      $mHtml->Table("tr");
                          $mHtml->Label("Esferas:", "width:15%; :1;");
                          $mHtml->Select2 (array_merge(array(array('0' =>  '-','1' =>  '-')),self::getListEal()),  array("name" => "eal[nom_esfera]", "id" => "nom_esferaID", "width" => "25%") );
                          //$mHtml->Button( array("value"=>"Reporte", "id"=>"btnNForumlID","name"=>"btnNForuml", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all bigButton", "align"=>"left","onclick"=>"getReporteGeneral()") ); 
                      $mHtml->CloseTable("tr");
                    $mHtml->CloseDiv();
                  $mHtml->CloseDiv();
                $mHtml->CloseDiv();
                # Fin accordion1
                # Accordion2
                $mHtml->OpenDiv("id:report; class:accordion");
                  $mHtml->OpenDiv("id:DivGeneralReport");
                    $mHtml->OpenDiv("id:tabs; class:contentAccordionForm");
                      $mTabsActive = "<ul><li><a href='#tabResult' onclick='getReporteGeneral()'>REPORTE</a></li>";
                      $mTabsActive .= "</ul>";
                      $mHtml->SetBody($mTabsActive);
                      $mHtml->OpenDiv("id:tabResult");
                      $mHtml->CloseDiv();
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
            $mHtml->SetBody('<script>
                              $(function() {
                                $("#tabs").tabs();
                              } );
                            </script>');
            $mHtml->SetBody('<script> $("div[id=datos]").hide() </script>');
            $mHtml->SetBody('<style> 
                                .celda_titulo {
                                  border-bottom: 1px solid white;
                                  /*border-bottom: 1px solid #35650F;*/
                                  background-color: #35650F;
                                  background-image: url("");
                                  color: white;
                                  font-weight: bold;
                                  width: 25%;
                                  padding: 3px 10px;
                                  /*white-space: nowrap;*/
                                }
                                .celda_etiqueta{
                                  border-left: 1px solid #cdcdcd;
                                  /*background-color: #EBF8E2;*/
                                }
                                .CellInfohref{
                                  cursor:pointer;
                                }
                                .celda_info{
                                  background-color:rgb(240, 240, 240);  
                                }
                                 </style>');

            # Muestra Html
            echo $mHtml->MakeHtml();
		}catch(Exception $e)
		{
			echo "Error funcion listar", $e->getMessage(), "\n";
		}
	}
    
    /*! \fn: getListEal
     *  \brief: Lista las hojas de vida registradas
     *  \author: Edward Serrano
     *  \date: 11/07/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    public function getListEal()
    {
      try
      {
          $mQuery = "SELECT a.cod_contro, CONCAT(a.cod_contro,' - ',a.nom_contro)  
              FROM ".BASE_DATOS.".tab_genera_contro a
              INNER JOIN ".BASE_DATOS.".tab_respon_funcio b ON b.cod_contro = a.cod_contro
              WHERE b.ind_repleg = 1
              GROUP BY (a.nom_contro)
              "; 
          $consult = new Consulta($mQuery, self::$cConexion );
          $result = $consult->ret_matrix('i');

          return $result; 
      }
      catch(Exception $e)
      {
        echo "Error funcion getListEal", $e->getMessage(), "\n";
      }
    }

    /*! \fn: getReporteGeneral
     *  \brief: Lista las hojas de vida registradas
     *  \author: Edward Serrano
     *  \date: 11/07/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    public function getReporteGeneral()
    {
      try
      {
          #limpio el $cod_contro
          $cod_contro = substr($_REQUEST['cod_contro'],1);
          $cod_contro = explode(",",$cod_contro);
          if($cod_contro[0] == "'-'")
          {
            unset($cod_contro[0]);
          }
          $cod_contro = implode(",",$cod_contro);
          #Informacion Basica
          $mdata = self::getDataEal( $cod_contro);echo "<pre>";
          $mConteo = self::getCountGenera($cod_contro, $mdata);

          $mHtml1 = new FormLib(2);
            #Contenido
                  if(sizeof($mdata)>0)
                  {
                    $mHtml1->Table("tr");
                      $mHtml1->Button( array("value"=>"EXCEL", "colspan"=>"5", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all bigButton", "align"=>"center","onclick"=>"exprtExcel('general')") ); 
                      $mHtml1->CloseRow();
                      $mHtml1->Row();
                      #Recorro los titulos a generar
                      $mTitleGener = array("ESFERA","N° FUNCIONARIOS");
                      foreach ($mConteo['formActuales'] as $key => $value) {
                        $mTitleGener[]=strtoupper(self::getDateForml($key));
                      }
                      foreach ($mTitleGener as $key => $value) {
                        $mHtml1->Label( $value,  array("align"=>"center", "class"=>"celda_titulo") );
                      }
                      $mHtml1->CloseRow();
                      #Totales
                      $mHtml1->Row();
                        $mHtml1->Label( "TOTALES",  array("align"=>"left", "class"=>"celda_titulo") );
                      foreach ($mConteo['totales'] as $key => $value) {
                        $mHtml1->Label( $mConteo['totales'][$key],  array("align"=>"center", "class"=>"celda_titulo", "onclick"=>($mConteo['totales'][$key]?"detailForm('".str_replace("'","",$cod_contro)."',".($key=="funcionarios"?0:$key).")":"")) );
                      }
                       $mHtml1->CloseRow();
                      #Recorro los datos
                      foreach ($mConteo['data'] as $key => $value) {
                        $mHtml1->Row();
                          $mHtml1->Label( $value['name'],  array("align"=>"left") );
                          $mHtml1->Label( $value['funcionario'],  array("align"=>"center", "onclick"=>"detailForm({$key},0)") );
                          #Recorro los formularios
                          foreach ($mConteo['formActuales'] as $key1 => $value1) {
                            $mHtml1->Label( ($value['formulario'][$key1]?$value['formulario'][$key1]:0),  array("align"=>"center", "onclick"=>($value['formulario'][$key1]?"detailForm({$key},{$key1})":"")) );
                          }
                        $mHtml1->CloseRow();
                      }
                      #Totales
                      $mHtml1->Row();
                        $mHtml1->Label( "TOTALES",  array("align"=>"left", "class"=>"celda_titulo") );
                      foreach ($mConteo['totales'] as $key => $value) {
                        $mHtml1->Label( $mConteo['totales'][$key],  array("align"=>"center", "class"=>"celda_titulo", "onclick"=>($mConteo['totales'][$key]?"detailForm('".str_replace("'","",$cod_contro)."',".($key=="funcionarios"?0:$key).")":"")) );
                      }
                       $mHtml1->CloseRow();
                    $mHtml1->CloseTable('tr');
                  }
                  else
                  {
                    $mHtml1->Table("tr");
                      $mHtml1->Label( "No Se encuantra informacion Relacionada",  array("align"=>"left", "class"=>"celda_titulo") );
                    $mHtml1->CloseTable('tr');
                  }
            # Muestra Html
            echo $_SESSION['inf_GhvEal'] = $mHtml1->MakeHtml();
      }
      catch(Exception $e)
      {
        echo "Error funcion getReporteGeneral", $e->getMessage(), "\n";
      }
    }

    /*! \fn: getDataEal
     *  \brief: obtengo la iformacion de las eal
     *  \author: Edward Serrano
     *  \date: 12/07/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    public function getDataEal($cod_contro, $agrupacion=NULL, $filtro=NULL)
    {
      try
      {
        $mQuery = "
                    SELECT    a.cod_contro, 
                              a.nom_contro,
                              b.num_docume, 
                              b.nom_funcio,
                              b.ind_repleg, 
                              c.cod_formul, 
                              c.nom_formul,          
                              d.cod_campos, 
                              d.nom_campox, 
                              d.val_campos, 
                              d.rut_docume
                      FROM
                      (  /* CONSULTA LAS EAL QUE TENGAN FORMULARIO EJECUTADO*/
                          SELECT e.cod_contro, a.cod_formul, b.nom_formul, c.nom_contro
                          FROM 
                          tab_respon_funcio e LEFT JOIN 
                          tab_respon_frmeal a ON e.cod_contro = a.cod_contro LEFT JOIN 
                          tab_formul_formul b ON a.cod_formul = b.cod_consec  LEFT JOIN
                          tab_genera_contro c ON e.cod_contro = a.cod_contro 
                          WHERE e.cod_contro IN ($cod_contro)
                          GROUP BY e.cod_contro, a.cod_formul

                      ) a
                      INNER JOIN /* CONSULTA DE FUNCIONARIOS A LA EAL*/
                      (
                          SELECT  f.cod_contro, f.cod_consec, f.num_docume, f.nom_apell1, f.nom_apell2, f.nom_funcio, f.num_telmov, 
                                  f.num_telef1, f.num_whatsa, f.dir_emailx, f.ind_sereal, f.ind_serasi, f.url_fotoxx, f.ind_estado, f.ind_repleg
                          FROM tab_respon_funcio f WHERE f.cod_contro IN ($cod_contro)   
                      ) b 
                      ON a.cod_contro = b.cod_contro
                      LEFT JOIN /* CONSULTA FORMULARIOS EJECUTADOS*/
                      (
                          SELECT a.cod_contro, a.cod_formul, b.nom_formul
                          FROM 
                          tab_respon_frmeal a INNER JOIN 
                          tab_formul_formul b ON a.cod_formul = b.cod_consec AND a.cod_contro IN ($cod_contro)   GROUP BY a.cod_contro, a.cod_formul
                      ) c 
                      ON a.cod_contro = c.cod_contro AND a.cod_formul = c.cod_formul
                      LEFT JOIN /* CONSULTA VALORES DE FORMULARIOS EJECUTADOS*/
                      (    
                          SELECT a.cod_contro, a.cod_campos, b.nom_campox, a.val_campos, a.rut_docume
                          FROM 
                          tab_respon_frmeal a INNER JOIN 
                          tab_formul_campos b ON a.cod_campos = b.cod_consec   AND a.cod_contro IN ($cod_contro) 
                      ) d 
                      ON a.cod_contro = d.cod_contro

                      WHERE a.cod_contro IN ($cod_contro)
                      ".($filtro==NULL?"":"AND ".$filtro)."
                      ".($agrupacion==NULL?"":"GROUP BY ".$agrupacion)."
                      ORDER BY a.cod_contro, b.nom_funcio
              "; 
          $consult = new Consulta($mQuery, self::$cConexion );
          $result = $consult->ret_matrix('a');
          return $result;
      }
      catch(Exception $e)
      {
        echo "Error funcion getListEal", $e->getMessage(), "\n";
      }
    }

    /*! \fn: getNameEal
     *  \brief: obtengo el nombre de la eal
     *  \author: Edward Serrano
     *  \date: 12/07/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    public function getNameEal($cod_contro)
    {
      try
      {
          $mQuery = "SELECT nom_contro
                        FROM ".BASE_DATOS.".tab_genera_contro 
                        WHERE cod_contro ={$cod_contro}  
                    "; 
          $consult = new Consulta($mQuery, self::$cConexion );
          $result = $consult->ret_matrix('a');
          return $result[0]['nom_contro'];
      }
      catch(Exception $e)
      {
        echo "Error funcion getNameEal", $e->getMessage(), "\n";
      }
    }

    /*! \fn: getDateForml
     *  \brief: obtengo informacion de los formularios
     *  \author: Edward Serrano
     *  \date: 12/07/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    public function getDateForml($cod_formul)
    {
      try
      {
          $mQuery = "SELECT nom_formul
                        FROM ".BASE_DATOS.".tab_formul_formul 
                        WHERE cod_consec = {$cod_formul}  
                    "; 
          $consult = new Consulta($mQuery, self::$cConexion );
          $result = $consult->ret_matrix('a');
          return $result[0]['nom_formul'];
      }
      catch(Exception $e)
      {
        echo "Error funcion getDateForml", $e->getMessage(), "\n";
      }
    }

     /*! \fn: getCamposForml
     *  \brief: obtengo informacion de los formularios
     *  \author: Edward Serrano
     *  \date: 12/07/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    public function getCamposForml($cod_contro=NULL, $cod_formul)
    {
      try
      {
          $mQuery = "SELECT a.cod_campox AS cod_campos, b.nom_campox
                          FROM 
                          tab_formul_detail a INNER JOIN 
                          tab_formul_campos b ON a.cod_campox = b.cod_consec
                      WHERE
                          a.cod_formul = {$cod_formul}

                    "; 
          $consult = new Consulta($mQuery, self::$cConexion );
          $result = $consult->ret_matrix('a');
          return $result;
      }
      catch(Exception $e)
      {
        echo "Error funcion getCamposForml", $e->getMessage(), "\n";
      }
    }

    /*! \fn: getDataFuncionarios
     *  \brief: obtengo informacion de los funcionarios
     *  \author: Edward Serrano
     *  \date: 12/07/2017
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    public function getDataFuncionarios($cod_contro)
    {
      try
      {
          $mQuery = "SELECT   num_docume, nom_apell1, nom_apell2,
                              nom_funcio, num_telmov, num_telef1,
                              num_whatsa, dir_emailx, IF(ind_sereal = 0, 'NO', 'SI') AS ind_sereal,
                              IF(ind_serasi = 0, 'NO', 'SI') AS ind_serasi, IF(ind_repleg = 0, 'Funcionario', 'Representante Legal') AS ind_repleg
                          FROM 
                          ".BASE_DATOS.".tab_respon_funcio
                      WHERE
                          cod_contro IN ($cod_contro)
                      ORDER BY ind_repleg DESC 
                    "; 
          $consult = new Consulta($mQuery, self::$cConexion );
          $result = $consult->ret_matrix('a');
          return $result;
      }
      catch(Exception $e)
      {
        echo "Error funcion getDataFuncionarios", $e->getMessage(), "\n";
      }
    }

    /*! \fn: getCountGenera
     *  \brief: Genera Conteo de la informacion general
     *  \author: Edward Serrano
     *  \date:  12/07/2017
     *  \date modified: dia/mes/año
     */
    private function getCountGenera($cod_contro, $mdata)
    {
      try
      {
        $infoGeneral = [];
        #recorro las eal para generar los conteos
        foreach (explode(",",$cod_contro) as $key => $value) 
        {
          #agrupo por cedula de funcionario
          $mDataFunc = self::getDataEal( $value, "b.num_docume,b.ind_repleg");
          $infoGeneral['data'][$value]['name'] = self::getNameEal($value);
          $infoGeneral['data'][$value]['funcionario'] = sizeof($mDataFunc);
          if($mDataFunc[0]['cod_formul'] != NULL)
          {
            #agrupo por formulario
            $mDataForm = self::getDataEal( $value, "c.cod_formul");
            foreach ($mDataForm as $kForm => $vForm) {
              $infoGeneral['data'][$value]['formulario'][$vForm['cod_formul']] = 1;
              $infoGeneral['formActuales'][$vForm['cod_formul']] = 1;
            }
          }
        }
        #obtengo los totales
        #foreach para totales de funcionarios
        foreach ($infoGeneral['data'] as $key => $value) {
          $infoGeneral['totales']['funcionarios'] = $infoGeneral['totales']['funcionarios'] + $value['funcionario'];
        }
        #foreach para totales de formularios
        foreach ($infoGeneral['formActuales'] as $key => $value) {
          foreach ($infoGeneral['data'] as $keyN => $valueN) {
            $infoGeneral['totales'][$key] = $infoGeneral['totales'][$key] + $valueN['formulario'][$key];
          }
        }
        return $infoGeneral;
      } catch (Exception $e) {
        echo "error getCountGenera :".$e;
      }
    }

    /*! \fn: getDetailEal
     *  \brief: Pinta detalle de la informacion registrada
     *  \author: Edward Serrano
     *  \date:  12/07/2017
     *  \date modified: dia/mes/año
     */
    private function getDetailEal()
    {
      try
      {
          #Informacion Basica
          if($_REQUEST['cod_formul']==0)
          {
            $mTitles = array(
                              "0" =>array("cod_campos"=>"num_docume", "nom_campox"=>"Documento"),
                              "1" =>array("cod_campos"=>"nom_apell1", "nom_campox"=>"Primer Apellido"),
                              "2" =>array("cod_campos"=>"nom_apell2", "nom_campox"=>"Segundo Apellido"),
                              "3" =>array("cod_campos"=>"nom_funcio", "nom_campox"=>"Nombre"),
                              "4" =>array("cod_campos"=>"num_telmov", "nom_campox"=>"Telefono Movil"),
                              "5" =>array("cod_campos"=>"num_telef1", "nom_campox"=>"Telefono"),
                              "6" =>array("cod_campos"=>"num_whatsa", "nom_campox"=>"Whatsaap"),
                              "7" =>array("cod_campos"=>"dir_emailx", "nom_campox"=>"Email"),
                              "8" =>array("cod_campos"=>"ind_sereal", "nom_campox"=>"Servicio Eal"),
                              "9" =>array("cod_campos"=>"ind_serasi", "nom_campox"=>"Servicio Asistencia"),
                              "10"=>array("cod_campos"=>"ind_repleg", "nom_campox"=>"Cargo")
                            );
          }
          else
          {
            $mTitles = self::getCamposForml( NULL, $_REQUEST['cod_formul']);
          }
          $mHtml = new FormLib(2);
              $mHtml->Table("tr");
              $mHtml->Button( array("value"=>"EXCEL", "colspan"=>"5", "class"=>"crmButton small save ui-button ui-widget ui-state-default ui-corner-all bigButton", "align"=>"center","onclick"=>"exprtExcel('detail')") ); 
                $mHtml->CloseRow();
                //titulos
                $mHtml->Row();
                  $mHtml->Label( "ESFERA",  array("align"=>"left", "class"=>"celda_titulo") );
                  foreach ($mTitles as $key => $value) {
                    $mHtml->Label( $value['nom_campox'],  array("align"=>"left", "class"=>"celda_titulo") );
                  }
                $mHtml->CloseRow();
                //Contenido
                foreach (explode(",", $_REQUEST['cod_contro']) as $kContro => $vContro) {
                  if($_REQUEST['cod_formul']==0)
                  {
                    $mdata = self::getDataFuncionarios( $vContro );
                    if(sizeof($mdata)>0)
                    {
                      foreach ($mdata as $key => $value) {
                        $mHtml->Row();
                        $mHtml->Label( self::getNameEal($vContro),  array("align"=>"left", "class"=>"celda_etiqueta") );
                        foreach ($mTitles as $keyT => $valueT) {
                          $mHtml->Label( $value[$valueT['cod_campos']],  array("align"=>"left", "class"=>"celda_etiqueta") );
                        }
                        $mHtml->CloseRow();
                      }
                    }
                  }
                  else
                  {
                    $mdata = self::getDataEal( $vContro, NULL, "c.cod_formul=".$_REQUEST['cod_formul']);
                    if(sizeof($mdata)>0)
                    {
                      $mHtml->Row();
                      $mHtml->Label( self::getNameEal($vContro),  array("align"=>"left", "class"=>"celda_etiqueta") );
                      foreach ($mTitles as $key => $value) {
                       $mCampo = self::procesarArray($mdata,'cod_campos',$value['cod_campos']);
                        $mHtml->Label( $mCampo['val_campos'],  array("align"=>"left", "class"=>"celda_etiqueta") );
                      }
                      $mHtml->CloseRow();
                    }
                  }
                }
              $mHtml->CloseTable('tr');
          # Muestra Html
          echo $_SESSION['inf_hvEal'] = $mHtml->MakeHtml();
      } catch (Exception $e) {
        echo "error getDetailEal :".$e;
      }
    }

    /*! \fn: procesarArray
     *  \brief: recorrer arrays
     *  \author: Edward Serrano
     *  \date:  12/07/2017
     *  \date modified: dia/mes/año
     */
    private function procesarArray($array=NULL, $param=NULL, $valor=NULL )
    {
      try
      {
        if($array!=NULL && $param!=NULL)
        {
          foreach ($array as $keyG => $valueG) {
            foreach ($valueG as $key => $value) {
              if($key==$param && $valueG[$param]==$valor){
                  return $valueG;
              }
            }
          } 
        }
        else
        {
          //echo "no hay datos";
          return NULL;
        }
      } catch (Exception $e) {
        echo "error procesarArray :".$e;
      }
    }

    /*! \fn: exprtExcel
     *  \brief: Exportar a excel
     *  \author: Edward Serrano
     *  \date:  12/07/2017
     *  \date modified: dia/mes/año
     */
    private function exprtExcel()
    {
      try
      {
        session_start();
        $date=date("Y_m_d_h_s");
        header('Content-type: application/vnd.ms-excel');
        header('Content-type: application/x-msexcel');
        header("Content-Disposition: attachment; filename=Reporte Hojas de vida".$date.".xls");
        header("Pragma: nopreg_replace-cache");
        header("Expires: 0");
        ob_clean();
        $buscar = array('border="0"','class="celda_etiqueta"','align="left"','valign=""','rowspan=""','colspan=""','width=""','height=""');
        $reempl = array('','','','','','','','');
        //ob_flush();
        //ob_end_flush();
        if($_REQUEST['tExporExcel']=="detail")
        {
          echo str_replace($buscar,$reempl,$_SESSION['inf_hvEal']);
        }
        else
        {
          echo str_replace($buscar,$reempl,$_SESSION['inf_GhvEal']);
        }
        die();
      } catch (Exception $e) {
        echo "error exprtExcel :".$e;
      }
    }
}
if($_REQUEST["Ajax"] === 'on' )
{
    $_hojaVidaEal = new repHojaVidaEal();
}
else
{
    $_hojaVidaEal = new repHojaVidaEal( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );
}

?>