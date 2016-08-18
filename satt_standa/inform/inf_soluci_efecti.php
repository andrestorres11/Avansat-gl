<?php

header('Content-Type: text/html; charset=UTF-8');
ini_set('memory_limit', '2048M');

@include( "../lib/ajax.inc" );
class InformViajes
{
  var $conexion,
      $cod_aplica,
      $usuario;
  var $cNull = array( array('', '- Todos -') ); 

  function __construct($co, $us, $ca)
  {
		$this -> conexion = $co;
		$this -> usuario = $us;
		$this -> cod_aplica = $ca;
		echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/homolo.css' type='text/css'>";
		$this -> principal();
  }
  
  function principal()
  {

		switch($_REQUEST[opcion])
		{
		  case 99:
		    $this -> getInform();
		  break;

		  case 1:
		    $this -> exportExcel();
		  break;
		  
		  default:
		    $this -> Listar();
		  break;
		}
  }
  
  
  
  function getInform()
  {
		$formulario = new Formulario ("index.php","post","Informe de Soluciones Efectivas","form\" id=\"formID");
    		
    	$mHtml = ""; 
  
		$formulario -> nueva_tabla();
		$formulario -> oculto("cod_modali\" id=\"cod_modaliID","",0);
		$formulario -> oculto("window","central",0);
		$formulario -> oculto("opcion\" id=\"opcionID",99,0);
		$formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);
		$formulario -> oculto("standaID\" id=\"standaID",DIR_APLICA_CENTRAL,0); 

		$formulario -> oculto("cod_transp\" id=\"cod_transpID",$_REQUEST['cod_transp'],0);
		$formulario -> oculto("fec_inicia\" id=\"fec_iniciaID",$_REQUEST['fec_inicia'],0);  
		$formulario -> oculto("fec_finali\" id=\"fec_finaliID",$_REQUEST['fec_finali'],0);  
		$formulario -> oculto("num_dessat\" id=\"num_dessat",$_REQUEST['num_dessat'],0);  
		$formulario -> oculto("num_placa\" id=\"num_placa",$_REQUEST['num_placa'],0);  
		$formulario -> oculto("num_viajex\" id=\"num_viajex",$_REQUEST['num_viajex'],0);  
		$formulario -> oculto("cod_modali\" id=\"cod_modali",$_REQUEST['cod_modali'],0);  
  
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/inf_soluci_efecti.js\"></script>\n";

        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
  
    	$despac = $this -> getDespac($_REQUEST['fec_inicia'], $_REQUEST['fec_finali'],$_REQUEST);

    	$mString = "'".join(",",$despac)."'";

  		$mTotNovedades = $this -> getNovedaXDespac( "", $mFiltros,$_REQUEST['fec_inicia'], $_REQUEST['fec_finali']);
    	
    	$mFiltros = "AND (b.ind_solnov = 1 OR b.ind_solnov IS NULL) ";
		$mNovedaEfecti = $this -> getNovedaXDespac( "", $mFiltros,$_REQUEST['fec_inicia'], $_REQUEST['fec_finali']);    	

		$mFiltros = "AND b.ind_solnov = 0 ";
		$mNovedaNoEfecti = $this -> getNovedaXDespac( "", $mFiltros,$_REQUEST['fec_inicia'], $_REQUEST['fec_finali']);
    	
    	$mCantTot = 0;
    	$mCantEfc = 0;
    	$mCantNoe = 0;
 
    	foreach ($mTotNovedades as  $row) {

    		$mCantTot += $row['cantidad'];

    	}
    	foreach ($mNovedaEfecti as  $row) {

    		$mCantEfc += $row['cantidad'];

    	}
    	foreach ($mNovedaNoEfecti as  $row) {

    		$mCantNoe += $row['cantidad'];

    	}
  
    	$mHtml .= "</table>";  
    	$mHtml .= "<div id='contPadre' class='StyleDIV' style='background-color:#E3F6CE; width:80%'>";  
    	$mHtml .= "<div id='contTotal' class='StyleDIV' style='background-color:#31B404; width:90%'>";  
		$mHtml .= "<center style='color:#FFFFFF'><h3>RESULTADO SOLUCI&Oacute;N A NOVEDADES<h3></center>"; 
		$mHtml .= "</div>";     	

		$mHtml .= "<div id='contTotal' class='StyleDIV' style='width:90%; background-color:f0f0f0;'>";  
		
		$mHtml .= "<center style='color:#FFFFFF'>";
		
		$mHtml .= "<table>";
		$mHtml .= "<tr class='cellHead2' >";
		$mHtml .= "<td style='width:250px'><label style='color:#FFFFFF; width:22%'><center><h3>Despachos Generados</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:250px'><label style='color:#FFFFFF; width:22%'><center><h3>Total De Soluciones Generadas</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:250px'><label style='color:#FFFFFF; width:22%'><center><h3>Soluciones Efectivas</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:250px'><label style='color:#FFFFFF; width:22%'><center><h3>%</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:250px'><label style='color:#FFFFFF; width:22%'><center><h3>Soluciones No Efectivas</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:250px'><label style='color:#FFFFFF; width:22%'><center><h3>%</h3></center></label>";
		$mHtml .= "</td>";
		$mHtml .= "</tr>";		
		$mHtml .= "<tr class='cellInfo' >";
		$mHtml .= "<td onclick='detalleDespachos(\"total\")' style='cursor: pointer'><center> <h3>".sizeof($despac)."</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td ><center> <h3>".$mCantTot."</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td ><center> <h3>".$mCantEfc."</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td ><center> <h3>".round((($mCantEfc*100)/$mCantTot),2)."</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td ><center> <h3>".$mCantNoe."</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td ><center> <h3>".round((($mCantNoe*100)/$mCantTot),2)."</h3></center></label>";
		$mHtml .= "</td>";
		$mHtml .= "</tr>";

		$mHtml .= "</table>";
		$mhtml .= "</center>"; 
		$mHtml .= "</div>"; 

		$mHtml .= "<br>";  
		$mHtml .= "<br>"; 		
		$mHtml .= "<br>";  
		$mHtml .= "<br>";  

		$mHtml .= "<div id='contTotal' class='StyleDIV' style='background-color:#31B404; width:90%'>";  
		$mHtml .= "<center style='color:#FFFFFF'><h3>DETALLADO POR DIAS<h3></center>"; 
		$mHtml .= "</div>";     	

		$mHtml .= "<div id='contTotal' class='StyleDIV' style='width:90%; background-color:f0f0f0;'>";  
		
		$mHtml .= "<center style='color:#FFFFFF'>";
		
		$mHtml .= "<table>";
		$mHtml .= "<tr class='cellHead2' >";
		$mHtml .= "<td style='width:100px'><label style='color:#FFFFFF; width:22%'><center><h3>#</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:250px'><label style='color:#FFFFFF; width:15px%'><center><h3>Fecha</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:100px'><label style='color:#FFFFFF; width:15px%'><center><h3>Despachos Generados</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:100px'><label style='color:#FFFFFF; width:15px%'><center><h3>Total De Soluciones Generadas</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:100px'><label style='color:#FFFFFF; width:15px%'><center><h3>Soluciones Efectivas</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:100px'><label style='color:#FFFFFF; width:15px%'><center><h3>%</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:100px'><label style='color:#FFFFFF; width:15px%'><center><h3>Soluciones No Efectivas</h3></center></label>";
		$mHtml .= "</td>";		
		$mHtml .= "<td style='width:100px'><label style='color:#FFFFFF; width:15px%'><center><h3>%</h3></center></label>";
		$mHtml .= "</td>";
 
		$fec_auxiliar = $_REQUEST['fec_inicia'];
			$i = 0;
			while ( $fec_auxiliar <= $_REQUEST['fec_finali']) {

				$despac = $this -> getDespac( $fec_auxiliar, $fec_auxiliar, $_REQUEST);

				$mTotNovedadesDia = $this -> getNovedaXDespac( "", "", $fec_auxiliar, $fec_auxiliar);

				$mFiltros = "AND (b.ind_solnov = 1 OR b.ind_solnov IS NULL) ";
				$mNovedaEfectiDia = $this -> getNovedaXDespac( "", $mFiltros, $fec_auxiliar, $fec_auxiliar);    	

				$mFiltros = "AND b.ind_solnov = 0 ";
				$mNovedaNoEfectiDia = $this -> getNovedaXDespac( "", $mFiltros, $fec_auxiliar, $fec_auxiliar);

				$mCantTotDia = 0;
				$mCantEfcDia = 0;
				$mCantNoeDia = 0;

		    	foreach ($mTotNovedadesDia as  $row) {

		    		$mCantTotDia += $row['cantidad'];

		    	}
		    	foreach ($mNovedaEfectiDia as  $row) {

		    		$mCantEfcDia += $row['cantidad'];

		    	}
		    	foreach ($mNovedaNoEfectiDia as  $row) {

		    		$mCantNoeDia += $row['cantidad'];

		    	}

		    	if($mCantTotDia != 0 || $mCantEfcDia != 0 || $mCantNoeDia != 0 || sizeof($despac) > 0){

					$dateNow = explode(" ",strftime ( '%A %d de %B del %Y', strtotime (  $fec_auxiliar )));
 
					$dateNow[0] =  ucwords($dateNow[0]); 
					$dateNow[3] =  ucwords($dateNow[3]); 
 
					$dateNow = join(" ",$dateNow);
 

	 				$mHtml .= '<input id="consec'.$i.'" type="hidden" value="'.$fec_auxiliar.'" name="consec'.$i.'">';
					$mHtml .= "</tr>";		
					$mHtml .= "<tr class='cellInfo' >";
					$mHtml .= "<td ><center> <h3>".$i."</h3></center></label>";
					$mHtml .= "</td>";		
					$mHtml .= "<td class='cellInfo' ><h3>". $dateNow . "</h3></td>";
					$mHtml .= "<td ><center onclick='detalleDespachos(".$i.")'  style='cursor: pointer'> <h3>".sizeof($despac)."</h3></center></label>";
					$mHtml .= "</td>";		
					$mHtml .= "<td ><center> <h3>".$mCantTotDia."</h3></center></label>";
					$mHtml .= "</td>";		
					$mHtml .= "<td ><center> <h3>".$mCantEfcDia."</h3></center></label>";
					$mHtml .= "</td>";		
					$mHtml .= "<td ><center> <h3>".round((($mCantEfcDia*100)/$mCantTotDia),2)."</h3></center></label>";
					$mHtml .= "</td>";		
					$mHtml .= "<td ><center> <h3>".$mCantNoeDia."</h3></center></label>";
					$mHtml .= "</td>";		
					$mHtml .= "<td ><center> <h3>".round((($mCantNoeDia*100)/$mCantTotDia),2)."</h3></center></label>";
					$mHtml .= "</td>";
					$mHtml .= "</tr>";


				$i++;
		    	}
				$fec_auxiliar = date ( 'Y-m-d' , strtotime ( '+1 day' , strtotime ( $fec_auxiliar ) ) );

			}	   
		$mHtml .= "</table>";
		$mhtml .= "</center>"; 
		$mHtml .= "</div>"; 
    	  
    	$mHtml .= "</div>";   
    	$mHtml .= "</table>"; 

		echo $mHtml;
  }

  function Listar(){

		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/inf_soluci_efecti.js\"></script>\n";

		echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";

			$formulario = new Formulario ("index.php","post","Informe de Operaciones","form\" id=\"formID");

			$formulario -> texto( "Fecha Inicial:", "text", "fec_inicia\" readonly id=\"fec_iniciaID", 0, 15, 15, "" );
			$formulario -> texto( "Fecha Final:", "text", "fec_finali\" readonly id=\"fec_finaliID", 1, 40, 15, "" ); 
			$formulario -> texto( "No. Despacho( SATT ):", "text", "num_dessat\" id=\"num_dessatID", 0, 15, 15, "" ); 
			$formulario -> texto( "Transportadora:", "text", "Transportadora\"  id=\"TransportadoraID", 1, 40, 100, "","" );
			$formulario -> texto( "Placa:", "text", "num_placa\" id=\"num_placaID", 0, 15, 15, "", "" );
			$formulario -> texto( "Modalidad:", "text", "nom_modali\" id=\"nom_modaliID", 1, 40, 15, "", "" ); 
			$formulario -> texto( "No. Viaje:", "text", "num_viajex\" id=\"num_viajexID", 0, 15, 15, "", "" );



			$formulario -> nueva_tabla();
			$formulario -> botoni("Buscar\" id=\"btnPrincipal","send();",0);

			$formulario -> nueva_tabla();
			$formulario -> oculto("cod_transp\" id=\"cod_transpID","",0);
			$formulario -> oculto("cod_modali\" id=\"cod_modaliID","",0);
			$formulario -> oculto("window","central",0);
			$formulario -> oculto("opcion\" id=\"opcionID",99,0);
			$formulario -> oculto("cod_servic",$GLOBALS['cod_servic'],0);
			$formulario -> oculto("standaID\" id=\"standaID",DIR_APLICA_CENTRAL,0); 

  }

  function getData(){

  }

  function getDespac($inicia, $finali, $_mData){

  		$mSql = "SELECT a.num_despac 
  				   FROM tab_despac_despac a 
  		     INNER JOIN tab_despac_vehige b 
  		     		 ON a.num_despac = b.num_despac
  				  WHERE a.fec_creaci >= '".$inicia." 00:00:00'
  				    AND a.fec_creaci <= '".$finali." 23:59:59'
  				    AND b.cod_transp = '".$_mData['cod_transp']."'";

			if($_mData['num_dessat'] != ''){
				$mSql .= "AND a.num_despac = '" . $_mData['num_dessat']  . "'";
			}
			if($_mData['num_placa'] != ''){
				$mSql .= "AND b.num_placax = '" . $_mData['num_placa']  . "'";
			}
			if($_mData['cod_modali'] != ''){
				$mSql .= "AND a.cod_tipdes = '" . $_mData['cod_modali']  . "'";
			}
			
			if($_mData['num_viajex'] != ''){
				$mSql .= "AND c.num_despac = '" . $_mData['num_viajex']  . "'";
			}

        $consulta = new Consulta($mSql, $this->conexion);
        $mResult = $consulta->ret_matriz();

    
        return $this -> matrizToArray($mResult );
         
  }

	function matrizToArray ( $matriz ){
		
		$result = array();

		for ($i=0; $i < sizeof($matriz); $i++) { 
			 array_push($result, $matriz[$i][0]);
		}

		return $result;
	}

	function getNovedaXDespac( $mIndReturn, $mFiltros = NULL , $inicia, $finali){
  
		$mSql = "SELECT b.cod_noveda, c.nom_noveda, COUNT(b.cod_noveda) AS cantidad, a.fec_creaci 
				   				  FROM ".BASE_DATOS.".tab_despac_despac a 
				   			INNER JOIN ".BASE_DATOS.".tab_protoc_asigna b 
				   					ON a.num_despac = b.num_despac 
				   			INNER JOIN ".BASE_DATOS.".tab_despac_vehige z
				   					ON a.num_despac = z.num_despac
								   AND a.fec_creaci >= '".$inicia." 00:00:00'
								   AND a.fec_creaci <= '".$finali." 23:59:59'
				   			INNER JOIN ".BASE_DATOS.".tab_genera_noveda c 
				   					ON b.cod_noveda = c.cod_noveda 
				   			 LEFT JOIN ".BASE_DATOS.".tab_despac_corona d
					  		        ON a.num_despac = d.num_dessat
				   				 WHERE 1=1 ";

			if($_REQUEST['num_dessat'] != ''){
				$mSql .= "AND a.num_despac = '" . $_REQUEST['num_dessat']  . "'";
			}
			if($_REQUEST['num_placa'] != ''){
				$mSql .= "AND z.num_placax = '" . $_REQUEST['num_placa']  . "'";
			}
			if($_REQUEST['cod_modali'] != ''){
				$mSql .= "AND a.cod_tipdes = '" . $_REQUEST['cod_modali']  . "'";
			}
			
			if($_REQUEST['num_viajex'] != ''){
				$mSql .= "AND d.num_despac = '" . $_REQUEST['num_viajex']  . "'";
			}

		
		$mSql .= ($mFiltros == NULL || $mFiltros == "") ? "" : " ".$mFiltros." ";

		$mSql .= "GROUP BY b.cod_noveda ";

			if($mIndReturn == 'sql' )
				return $mSql;
			else{
			$mConsul = new Consulta($mSql, $this -> conexion);
			return $mResult = $mConsul -> ret_matrix("a");	
			}
	}


  //Inicio Función exportExcel
  private function exportExcel()
  {

		session_start();
		$archivo = "informe_operaciones".date( "Y_m_d_H_i" ).".xls";
		header('Content-Type: application/octetstream');
		header('Expires: 0');
		header('Content-Disposition: attachment; filename="'.$archivo.'"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		ob_clean();
		echo $HTML = $_SESSION[xls_InformViajes];
  }
  //Fin Función exportExcel
}

$_INFORM = new InformViajes( $this -> conexion, $this -> usuario_aplicacion, $this -> codigo );

?>