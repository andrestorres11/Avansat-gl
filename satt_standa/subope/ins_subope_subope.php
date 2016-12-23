<?php 

/**
* 
*/
class InsSubopeSubope
{
	
	var $conexion;
	function __construct($conexion, $mData)
	{
		$this -> conexion = $conexion;

		switch ($mData['op']) {
			case 'value':
				# code...
				break;
			
			default:
				$this -> drawFrom($mData);
				break;
		}
		
	}

	function drawFrom($mData){ 

		$operaciones = $this -> getOperad();

		$subOperacion = $this -> getListSubOpe(); 
   
    	$mHtml = new FormLib(2);

		// incluye JS
	    $mHtml->SetJs("jquery");
	    $mHtml->SetJs("functions");
	    $mHtml->SetJs("subope");

	    // incluye Css
	    $mHtml->SetCssJq("jquery");
	    $mHtml->SetCssJq("dinamic_list");

	    $mHtml->Body(array("menubar" => "no"));

        // Abre Form
    	$mHtml->Form(array("action" => "index.php",
	        "method" => "post",
	        "name" => "form_search",
	        "header" => "Conductores",
	        "enctype" => "multipart/form-data"));

    	# Construye accordion
	      	$mHtml->Row("td");
	        	$mHtml->OpenDiv("id:contentID; class:contentAccordion");
	          		# Accordion1
	          		$mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
            			$mHtml->SetBody("<h1 style='padding: 6px' ><b>Ingresar Sub-operación</b></h1>");
	            		$mHtml->OpenDiv("id:sec1;");
	              			$mHtml->OpenDiv("id:form1; class:contentAccordionForm");

	                			$mHtml->Table("tr");
	                    		$mHtml->Label("Codigo Operación:", "width:35%; :1;");
	                    		$mHtml->Select($operaciones, array("name" => "cod_operac", "id" => "cod_operacID", "width" => "35%", "end" => "yes")); 

	                    		$mHtml->Label("Nombre Sub-operación:", "width:35%; :1;");
	                    		$mHtml->Input(array("name" => "nom_subope", "id" => "nom_subopeID", "width" => "35%", "end" => "yes"));
  								
	                    		$mHtml->Hidden(array("name"=>"standa", "value"=>DIR_APLICA_CENTRAL));
 								$mHtml->Button(array("name"=>"boton", "value"=>"Insertar", "onclick"=>"saveSubope()"));
	                	$mHtml->CloseTable("tr");
	              	$mHtml->CloseDiv(); 
            	$mHtml->CloseDiv(); 
            $mHtml->CloseDiv();
	        # Fin accordion1 

	        # Accordion2
	          		$mHtml->OpenDiv("id:DatosBasicosID; class:accordion");
            			$mHtml->SetBody("<h1 style='padding: 6px' ><b>Sub operaciones</b></h1>");
	            		$mHtml->OpenDiv("id:sec1;");

	              			$mHtml->OpenDiv("id:form1; class:contentAccordionForm");

            				$mHtml->SetBody($subOperacion);
  
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
 	

	    echo $mHtml->MakeHtml(); 

	}


	function getOperad(){

		$first = array("0" => "-", "1" => " --");

		$query = "SELECT cod_operac, nom_operac
				  FROM ".BASE_DATOS.".tab_callce_operac
				  WHERE 1=1";
		
		$consulta = new Consulta( $query, $this -> conexion );
		$select = $consulta -> ret_matrix("i");

		return array_merge($first, $select);

	}

	function getListSubOpe(){

		$query = "SELECT a.cod_subope, b.nom_operac, a.nom_subope,
						 IF(a.ind_estado = 1, 'Activo', 'Inactivo') AS 'a.ind_estado', a.ind_estado AS cod_option
					FROM ".BASE_DATOS.".tab_operad_subope a 
			  INNER JOIN ".BASE_DATOS.".tab_callce_operac b
			  	      ON a.cod_operac = b.cod_operac
			  	   WHERE 1=1";

        $_SESSION["queryXLS"] = $query;

        if(!class_exists(DinamicList)) {
          include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");                         
        }
        $list = new DinamicList( $this -> conexion, $query, "2" , "no", 'ASC');
        $list->SetClose('no'); 
        $list->SetHeader(utf8_decode("Consecutivo"), "field:a.cod_subope; width:1%;  ");
        $list->SetHeader(utf8_decode("Operacion"), "field:a.nom_operac; width:1%");
        $list->SetHeader(utf8_decode("Sub Operacion"), "field:a.nom_subope; width:1%");
        $list->SetHeader(utf8_decode("Estado"), "field:a.ind_estado; width:1%"); 
        $list->SetOption(utf8_decode("Opción"),"field:a.cod_option; width:1%; onclikDisable:disableSubope( this ); onclikEnable:enableSubope( this );" );
        $list->SetHidden("cod_subope", "0" );

        $list->Display($this -> conexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $Html = $list -> GetHtml();

        return $Html;
 
	}
}

$class = new InsSubopeSubope( $this -> conexion, $_REQUEST );

?>