<?php
/*! \file: ins_respon_respon.php
 *  \brief: Inserta, lista, actualiza Responsables
 *  \author: Ing. Fabian Salinas
 *  \author: fabian.salinas@intrared.net
 *  \version: 1.0
 *  \date: 21/09/2015
 *  \bug: 
 *  \warning: 
 */

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

header('Content-Type: text/html; charset=UTF-8');

/*! \class: respon
 *  \brief: Lista Responsables
 */
class respon
{
	private static  $cConexion,
					$cCodAplica,
					$cUsuario,
					$cNull = array( array('', '-----') );

	function __construct($co, $us, $ca)
	{
		@include_once( '../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc' );

		self::$cConexion = $co;
		self::$cUsuario = $us;
		self::$cCodAplica = $ca;
		
		IncludeJS( 'jquery.js' );
		IncludeJS( 'functions.js' );
		IncludeJS( 'ins_respon_respon.js' );

		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>\n";

		switch($_REQUEST[Option])
		{
			default:
				self::lista();
				break;
		}
	}

	/*! \fn: lista
	 *  \brief: Lista responsables
	 *  \author: Ing. Fabian Salinas
	 *	\date: 22/09/2015
	 *	\date modified: dia/mes/año
	 *  \param: 
	 *  \return:
	 */
	private function lista()
	{
		$mSql = "SELECT a.cod_respon, a.nom_respon, 
						IF( a.ind_activo = '1', 'Activo', 'Inactivo' ) AS ind_estado,
						a.ind_activo
				   FROM ".BASE_DATOS.".tab_genera_respon a ";

		$_SESSION["queryXLS"] = $mSql;

		if(!class_exists(DinamicList))
			include_once("../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc");

		$mList = new DinamicList(self::$cConexion, $mSql, "a.cod_respon" , "no", 'ASC');
		$mList->SetClose('no');
		$mList->SetCreate("Agregar", "onclick:editRespon(2)");
		$mList->SetHeader(utf8_decode("Código"), "field:a.cod_respon; width:25%;  ");
		$mList->SetHeader(utf8_decode("Nombre"), "field:a.nom_respon; width:25%;  ");
		$mList->SetHeader(utf8_decode("Estado"), "field:a.ind_estado; width:25%;  ");
		$mList->SetOption(utf8_decode("Opciones"),"field:ind_activo; width:1%; onclikDisable:editRespon( '0', this ); onclikEnable:editRespon( '1', this ); onclikEdit:editRespon( '99', this );" );
		$mList->SetHidden("cod_respon", "0");
		$mList->SetHidden("nom_respon", "1");

		$mList->Display(self::$cConexion);

		$_SESSION["DINAMIC_LIST"] = $mList;


		#HTML
		$mHtml = new Formlib(2);

		$mHtml->SetJs("dinamic_list");
		$mHtml->SetCss("dinamic_list");

		$mHtml->CloseTable('tr');
		$mHtml->Table("tr");
			$mHtml->SetBody("<td>");

				$mHtml->OpenDiv("id:contentID; class:contentAccordion");
					$mHtml->OpenDiv("id:responID; class:accordion");
						$mHtml->SetBody("<h3 style='padding:6px;'><center>RESPONSABLES</center></h3>");
						$mHtml->OpenDiv("id:secID");
							$mHtml->OpenDiv("id:sub_responID; class:contentAccordionForm");
								$mHtml->SetBody( $mList->GetHtml() );

								$mHtml->Hidden( array("name"=>"window", "id"=>"windowID", "value"=>"central") );
								$mHtml->Hidden( array("name"=>"standa", "id"=>"standaID", "value"=>DIR_APLICA_CENTRAL) );
								$mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
							$mHtml->CloseDiv();
						$mHtml->CloseDiv();
					$mHtml->CloseDiv();
				$mHtml->CloseDiv();

			$mHtml->SetBody('</td>');
		$mHtml->CloseTable('tr');

		echo $mHtml->MakeHtml();
	}
}

$_INFORM = new respon( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>