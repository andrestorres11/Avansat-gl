<?php 
/*! \class: InsertarAutorizacion
 *  \brief: clase que manea la insercion de las autorizaciones moviles
 *
 * 
 */
  
class InsertarAutorizacion{

/*! \fn: __construct
 *  \brief: constructor de la clase
 *  \author: Ing. Miguel Romero
 *	\date: 01/01/2016
 *	\date modified: dia/mes/año
 *  \param: conexion = conexion a la BD
 *  \param: usuario 
 *  \param: cod_aplica = codigo del servicio 
 *  \return valor que retorna
 */
 
	function __construct($co, $us, $ca ){

		$this -> conexion = $co;
		$this -> usuario = $us;
		$this -> cod_aplica = $ca;
		$this -> formulario();

	}
/*! \fn: formulario
 *  \brief: pinta el formulario central de insercion
 *  \author: Ing. Miguel Romero
 *	\date: 01/01/2016
 *	\date modified: dia/mes/año  
 */

	function formulario(){
  
 

		$form = new Formulario();

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
							2 => array( 0 => '1', 1 => 'Administrador' )
							);

		$query = "SELECT a.cod_tipdoc, a.nom_tipdoc FROM ".BASE_DATOS.".tab_genera_tipdoc a WHERE 1 = 1";

		$consulta = new Consulta($query, $this -> conexion);
		$dat_tipdoc = $consulta -> ret_matriz("i");

		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
		echo "<script language='JavaScript' src='../".DIR_APLICA_CENTRAL."/js/jquery.js'></script>";
   		echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
		echo "<script language='JavaScript' src='../".DIR_APLICA_CENTRAL."/ctrapp/js/ins_aplica_movil.js'></script>";
		echo "<script language='JavaScript' src='../".DIR_APLICA_CENTRAL."/js/functions.js'></script>";
		echo "<script src='../" . DIR_APLICA_CENTRAL . "/js/sweetalert2.all.8.11.8.js'></script>";
		
		$form -> nueva_tabla( ); 
		$form -> linea("<center>Tipo de usuario</center>","1","t"); 	

		$form -> nueva_tabla( ); 

		// LOS "NULL" SON PARA ELIMINAR WARNINGS "Missing argument"

		$form -> lista("Tipo de usuario", "tip_person",$tip_person,"1",NULL); 

		$form -> nueva_tabla( );

		$form -> linea("<center>Datos Basicos del Usuario</center>","1","t","100%"); 

		$form -> nueva_tabla( );

		// LOS "NULL" SON PARA ELIMINAR WARNINGS "Missing argument"
 
		$form -> lista(" Tipo de Documento", "tip_docume",$dat_tipdoc, NULL); 	
		$form -> texto(" Numero de Documento", "text", "num_docume\" onkeypress=\"return NumericInput(event)","1", NULL,NULL,NULL,NULL,NULL); 	
		$form -> texto(" Nombres", "text\" readonly=\"readonly", "nom_usuari", NULL,NULL,NULL,NULL,NULL); 	
		$form -> texto(" Apellido 1", "text\" readonly=\"readonly", "nom_appel1","1", NULL,NULL,NULL,NULL,NULL); 	
		$form -> texto(" Apellido 2", "text\" readonly=\"readonly", "nom_appel2", NULL,NULL,NULL,NULL,NULL); 	 
		$form -> texto(" Direccion", "text\" readonly=\"readonly", "num_direcc","1",NULL,NULL,NULL,NULL); 	
		$form -> texto(" Telefono 1 ", "text\" readonly=\"readonly", "num_telef1", NULL,NULL,NULL,NULL,NULL); 	
		$form -> texto(" Telefono 2 ", "text\" readonly=\"readonly", "num_telef2","1",NULL,NULL,NULL,NULL); 	
		$form -> texto(" Celular ", "text\" readonly=\"readonly", "num_movilx", NULL,NULL,NULL,NULL,NULL); 	
		$form -> texto(" E-mail ", "text" , "nom_emailx","1",NULL,NULL,NULL,NULL); 	

		$form -> nueva_tabla( );

		$form -> linea("<center>Datos Aplicacion APP</center>","1","t","100%"); 

		$form -> nueva_tabla( );

		$form -> texto("* Usuario a Generar ", "text", "nom_usrapp", NULL,NULL,NULL,NULL,NULL); 	
		$form -> texto("* Serie ", "text\" readonly=\"readonly", "cod_seriex","1", NULL,NULL,NULL,NULL,NULL); 	
		$form -> lista("* Estado ", "cod_estado\" id=\"cod_estadoID",$est_appxxx, NULL); 	
		$form -> lista("* Tipo de Usuario ", "ind_admini\" id=\"ind_adminiID",$tip_usuari, NULL); 	
		$form -> oculto("standa", DIR_APLICA_CENTRAL, NULL); 	
		$form -> oculto("nit_transpor\" id=\"nit_transpor", NIT_TRANSPOR, NULL); 	

		$form -> nueva_tabla( );

		$form ->StyleButton("name:send;  id:guardarID; value:Guardar; onclick:guardar(); align:center; colspan:4;  class:crmButton small save");
		$form -> cerrar();
	}
}

$insertar = new InsertarAutorizacion( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );
	
?>