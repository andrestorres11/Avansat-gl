<?php
	/*ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);*/

    require "ajax_extenc_extenc.php";

class Ins_operac_operac {

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
        self::$cFunciones = new extenc($co, $us, $ca);
        switch ($_REQUEST[opcion]) {
            default:
                $this->filtro();
                break;
        }
    }
    /*! \fn: filtro
     *  \brief: funcion inicial para listar registrar extenciones
     *  \author: Ing. Alexander Correa
     *	\date: 04/12/2015
     *	\date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    
    function filtro() {
    	
        $operaciones = self::$cFunciones->getTipoDeOperacion();
        $grupos = self::$cFunciones->getGrupos();
        $mHtml = new FormLib(2);

        # incluye JS
        $mHtml->SetJs("min");
        $mHtml->SetJs("config");
        $mHtml->SetJs("fecha");
        $mHtml->SetJs("jquery");
        $mHtml -> SetBody(' <script src="../'.DIR_APLICA_CENTRAL.'/js/blockUI.jquery.js"></script> ');
        $mHtml->SetJs("functions");
        $mHtml->SetJs("ins_extenc_extenc");
        $mHtml->SetJs("InsertProtocolo");
        $mHtml->SetJs("new_ajax"); 
        $mHtml->SetJs("dinamic_list"); 
        $mHtml->SetCss("dinamic_list");
        $mHtml->SetCss("boostrap");
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
	            "header" => "Insertar Configuración",
	            "enctype" => "multipart/form-data"));
		        $mHtml->Row("td");
					 $mHtml->Hidden(array( "name" => "standa", "id" => "standaID", 'value'=>DIR_APLICA_CENTRAL));
					 $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
					 $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=>$_REQUEST['cod_servic']));
					 $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>''));
					 $mHtml->Hidden(array( "name" => "cod", "id" => "codID", 'value'=>''));
					 $mHtml->Hidden(array( "name" => "operac", "id" => "operacID", 'value'=>''));
			#Cierra formulario

        	$mHtml->CloseForm();
	        	$mHtml->OpenDiv("id:tabla1; class:accordion ancho");
	        	$mHtml->SetBody("<h1 style='padding:6px;'><B>AGREGAR OPERACI&Oacute;N</B></h1>");
					$mHtml->OpenDiv("id:sec1");
						$mHtml->OpenDiv("id:form3; class:contentAccordionForm");
							$mHtml->SetBody("<div class='col-md-12' style='text-align:center'>
												<div class='col-md-6' style='color:black'>Operación <font style='color:red'>*</font>:</div>
												<div class='col-md-6'><input class='campo_texto' validate='alpha' obl='1' minlength='5' maxlength='50'  type='text' name='nom_operac' style='width:100%' id='nom_operacID'></div>
												
											 </div>
												");
							
	                        $mHtml->SetBody("<td><div class='col-md-12'>&nbsp;</div><div id='boton' style='text-align:center'><input type='button' id='registrar' class='ui-button ui-widget ui-state-default ui-corner-all' value='Registrar' onClick='registrarOperacion()'></div></td>");  
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();	

			$mHtml->OpenDiv("id:tabla; class:accordion ancho");
				$mHtml->SetBody("<h1 style='padding:6px;'><B>LISTADO DE OPERACIONES</B></h1>");
				$mHtml->OpenDiv("id:sec2");
					$mHtml->OpenDiv("id:form3; class:contentAccordionForm");
						 $mSql = "SELECT a.cod_operac,a.nom_operac,
				                         IF(a.ind_estado = '1','Activa', 'Inactiva') AS cod_estado,
				                         a.ind_estado AS cod_option
				                         FROM ".BASE_DATOS.".tab_callce_operac a ";

				                         
					      $_SESSION["queryXLS"] = $mSql;

					      if(!class_exists(DinamicList)) {
					      	include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");									  	  	
					  	  }
					  	  $list = new DinamicList( $this->conexion, $mSql, "3" , "no", 'ASC');
					      
					      $list->SetClose('no');
					      $list->SetHeader("Consecutivo", "field: a.cod_operac; width:1%;  ");
					      $list->SetHeader(utf8_decode("Operación"), "field:c.nom_operac; width:1%");
					      $list->SetHeader("Estado", "field:if(a.cod_estado = 1, 'Activa', 'Inactiva')" );
					      $list->SetOption("Opciones","field:cod_option; width:1%; onclikDisable:editarOperacion( 2, this ); onclikEnable:editarOperacion( 1, this );" );
					      $list->SetHidden("cod_operac", "0" );
					      $list->SetHidden("nom_operac", "1" );
					      $list->Display($this->conexion);

					      $_SESSION["DINAMIC_LIST"] = $list;

					      $Html = $list -> GetHtml();

					 
					      $mHtml->SetBody($Html);
				$mHtml->CloseDiv();
			$mHtml->CloseDiv();
		$mHtml->CloseDiv();	
        # Cierra Body
        $mHtml->CloseBody();

        # Muestra Html
        echo $mHtml->MakeHtml();
    }

    


}

//FIN CLASE
$proceso = new Ins_operac_operac($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>