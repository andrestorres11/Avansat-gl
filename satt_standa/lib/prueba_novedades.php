<?php

include_once("general/conexion_lib.inc");
class PruebaNovedades
{
  var $cUser = 'nliberato';
  var $cPass = ',oMaXe,o';
  var $cStan = 'satt_standa';
  var $cClie = 'satt_faro';
  var $cURls = '';

  private static $cBD;
  
  //968028
  function __construct()
  {
  	$mHost = $_SERVER['HTTP_HOST'];
    $mHost = explode('.', $mHost);

    switch ($mHost[0]) {
        case 'web7':      self::$cBD = "bd7.intrared.net:3306";  break;
        case 'web13':     self::$cBD = "bd13.intrared.net:3306"; break;
        case 'avansatgl': self::$cBD = "aglbd.intrared.net";     break;
        default:          self::$cBD = "demo.intrared.net";      break;
    }

    switch($_POST["Opcion"])
	{
	  case "1": PruebaNovedades::Enviar1(); break;
	  default: PruebaNovedades::Formulario();  break;
	}
  }
  
  function Formulario()
  {
    $mHtml  = '<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">';
    $mHtml .= '<script src="//code.jquery.com/jquery-1.10.2.js"></script>
               <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>';
    $mHtml .= '<script>
	              function Enviar()
				  {
				    try {
					  var cod_transp = document.getElementById("cod_transpID");
					  var num_despac = document.getElementById("num_despacID");
					  var cod_noveda = document.getElementById("cod_novedaID");
					  var cod_contro = document.getElementById("cod_controID");
					  
					  if( cod_transp.value == "") {
					    alert("Digite Nit transportadora");
						return $("#cod_transpID").focus();
					  }
					  if( num_despac.value == "") {
					    alert("Digite Nit transportadora");
						return $("#num_despacID").focus();
					  }
					  if(cod_noveda.value == ""){
					    alert("Digite Codigo Novedad");
						return $("#cod_novedaID").focus();
					  }
					  if(cod_contro.value == ""){
					    alert("Digite Codigo Control");
						return $("#cod_controID").focus();
					  }
					  
					  $.ajax({
							type : "POST",
							url  : "prueba_novedades.php",
							data : "Opcion=1&cod_transp="+cod_transp.value+"&num_despac="+num_despac.value+"&cod_noveda="+cod_noveda.value+"&cod_contro="+cod_contro.value,
							beforeSend: function (){
							  $("#ResultID").html("<center>Enviando novedad</center>");
							},
							success: function( data ){
							  $("#ResultID").html(data);
							}
						});
					}
					catch(e){
					  alert("Error en Line: "+e.lineNumber);
					}
				  }
	           </script>';
    $mHtml .= '<table width="80%" align="center">';
		$mHtml .= '<tr>';
			$mHtml .= '<td colspan="12"><span><b>Novedades PC SATT -> SAT X</b></span></td>';
		$mHtml .= '</tr>';
		$mHtml .= '<tr>';
			$mHtml .= '<td>Numero Transportadora:</td>';
			$mHtml .= '<td><input type="text" id="cod_transpID" placeholder="Numero transportadora"   /></td>';
			$mHtml .= '<td>Numero Despacho:</td>';
			$mHtml .= '<td><input type="text" id="num_despacID" placeholder="Numero despacho"   /></td>';
		$mHtml .= '</tr>';
		$mHtml .= '<tr>';
			$mHtml .= '<td colspan="12">Datos de la novedad</td>';
		$mHtml .= '</tr>';
		$mHtml .= '<tr>';
			$mHtml .= '<td>Codigo Novedad:</td>';
			$mHtml .= '<td><input type="text" id="cod_novedaID" placeholder="Codigo Novedad"   /></td>';
			$mHtml .= '<td>Codigo  Control:</td>';
			$mHtml .= '<td><input type="text" id="cod_controID" placeholder="Codigo Control"   /></td>';
		$mHtml .= '</tr>';
		
		
		
		$mHtml .= '<tr>';
			$mHtml .= '<td colspan="12"><input type="button" value="Enviar" id="SendID" onclick="Enviar()" /></td>';
		$mHtml .= '</tr>';
    $mHtml .= '</table>';
    $mHtml .= '<table>';
	    $mHtml .= '<tr>';
			$mHtml .= '<td colspan="12"><div id="ResultID"></div></td>';
		$mHtml .= '</tr>';
    $mHtml .= '</table>';
	
	echo $mHtml;
	
	
	
  }
  
  
  function Enviar1()
  {
    ini_set( "soap.wsdl_cache_enabled", "0" );
    $this -> conexion = new Conexion(self::$cBD,$this -> cUser ,$this -> cPass, $this -> cStan);
    
	#Datos de conexion
	$query = "SELECT a.cod_operad, a.nom_operad, a.nom_usuari, a.clv_usuari, a.val_timtra  
				 FROM ".$this -> cClie.".tab_interf_parame a  
				WHERE a.ind_operad = '0' AND
				      a.ind_estado = '1' AND  
				      a.ind_intind = '1' AND 
					  a.cod_transp = '".$_POST["cod_transp"]."'";
	$consulta = new Consulta( $query, $this -> conexion );
	$mDatos = $consulta -> ret_matriz("i");	
	echo "<pre>"; print_r( $mDatos ); echo "</pre>";  
	
	
	
	#Consulta Servidor
	$mUrl = "SELECT a.url_webser	
			  FROM ".$this -> cStan.".tab_genera_server a,
				   ".$this -> cClie.".tab_transp_tipser b
			  WHERE a.cod_server = b.cod_server AND
					b.cod_transp = '".$_POST["cod_transp"]."' 
			  ORDER BY b.fec_creaci DESC ";

	$consulta = new Consulta( $mUrl, $this -> conexion );
	$url_webser = $consulta -> ret_matriz();
	$url_webser =  $url_webser[0][0];//URL DEL WSDL.
	
	echo "<pre>"; print_r($url_webser); echo "</pre>";  
	

	try
	{
		$oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

		$mResult = $oSoapClient -> __call( 'aplicaExists', array( "nom_aplica" => $mDatos[0]["nom_operad"] ) );
		$mResult = explode("; ", $mResult);
		$mCodResp = explode(":", $mResult[0]);
		$mMsgResp = explode(":", $mResult[1]);

		if ("1000" != $mCodResp[1])
		{
			$error_ = $mMsgResp[1];
			echo "<pre>"; print_r( "No se encontró la aplicacion: ".$mDatos[0]["nom_operad"].", en ".$url_webser ); echo "</pre>";  
			die();
		}
		
		PruebaNovedades::Enviar2( $mDatos, $url_webser );
        		
	}
	catch( SoapFault $e )
	{
		$error_ = $e -> getMessage();
	}    
  }
  
  
  function Enviar2( $mDatos , $url_webser)
  {
    $query = "SELECT a.cod_manifi, b.num_placax  
				FROM ".$this -> cClie.".tab_despac_despac a,  
				     ".$this -> cClie.".tab_despac_vehige b  
				WHERE a.num_despac = b.num_despac AND 
				      a.num_despac = '".$_POST["num_despac"]."' ";
	$consulta = new Consulta($query, $this->conexion);
	$mSalida = $consulta->ret_matriz();

	$mQuerySelNomNov = "SELECT nom_noveda, ind_alarma, ind_tiempo, nov_especi, ind_manala   
					      FROM ".$this -> cClie.".tab_genera_noveda  
						 WHERE cod_noveda = '".$_POST["cod_noveda"]."' ";

	$consulta = new Consulta($mQuerySelNomNov, $this->conexion);
	$mNomNov = $consulta->ret_matriz();

	$mQuerySelNomPc = "SELECT nom_contro " .
			"FROM " . $this -> cClie . ".tab_genera_contro " .
			"WHERE cod_contro = '" . $_POST["cod_contro"] . "' ";

	$consulta = new Consulta($mQuerySelNomPc, $this->conexion);
	$mNomPc = $consulta->ret_matriz();

	$mQuerySelPcxbas = "SELECT cod_pcxbas 
						  FROM ".$this -> cClie.".tab_homolo_trafico 
						 WHERE cod_transp = '".$_POST["cod_transp"]."' AND 
						       cod_pcxfar = '".$_POST["cod_contro"]."' AND 
							   cod_rutfar = '31128'
						  ";

	$consulta = new Consulta($mQuerySelPcxbas, $this->conexion);
	$mCodPcxbas = $consulta->ret_matriz();

	$parametros = array("nom_usuari" => $mDatos[0]["nom_usuari"],
						"pwd_clavex" => $mDatos[0]["clv_usuari"] ,
						"nom_aplica" => $mDatos[0]["nom_operad"],
						
						"num_manifi" => $mSalida[0]['cod_manifi'],
						"num_placax" => $mSalida[0]['num_placax'],
						"cod_novbas" => 0,
						"cod_conbas" => $mCodPcxbas[0][0],
						"tim_duraci" => $regist["tieadi"],
						"fec_noveda" => date('Y-m-d H:i', strtotime($regist["fecnov"])),
						"des_noveda" => $regist["observ"],
						"nom_contro" => $mNomPc[0][0],
						"nom_sitiox" => substr($regist["sitio"], 0, 50),
						"cod_confar" => NULL,
						'cod_novfar' => $regist['noveda'],
						'nom_noveda' => $mNomNov[0]['nom_noveda'],
						'ind_alarma' => $mNomNov[0]['ind_alarma'],
						'ind_tiempo' => $mNomNov[0]['ind_tiempo'],
						'nov_especi_' => $mNomNov[0]['nov_especi'],
						'ind_manala' => $mNomNov[0]['ind_manala']
					);//ARRAY
					
    echo "<pre>"; print_r($parametros); echo "</pre>";
	
	$oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
    $respuesta = $oSoapClient -> __call( 'setNovedadPC', $parametros );
	
    echo "<pre>"; print_r($respuesta); echo "</pre>";
  }
}

$mSetNovedad = new PruebaNovedades();
?>