<?php

/*    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);*/

	global $HTTP_POST_FILES;
	session_start();
	define ('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
	define ('ESTILO', $_SESSION['ESTILO']);
	define ('BASE_DATOS', $_SESSION['BASE_DATOS']);
	define ('BD_STANDA', $_REQUEST['dir_aplica']);
	define ('CENTRAL', $_REQUEST['dir_aplica']);
	setlocale(LC_TIME, "es_CO");
/*
	include( "../lib/general/conexion_lib.inc" );
	include( "../lib/general/form_lib.inc" );
	include( "../lib/general/tabla_lib.inc" );*/
	
@include( "../lib/ajax.inc" );
	include( "../lib/general/constantes.inc" );


	class AjaxDespachos{

		var $conexion = NULL;

		function __construct(){

			 $this -> conexion = new Conexion( "bd10.intrared.net", $_SESSION['USUARIO'], $_SESSION['CLAVE'], $_SESSION['BASE_DATOS']  );
			 $this -> $_REQUEST[option]( $_REQUEST );
		}

		private function Style()
		  {
		    echo '
		        <style>
		        .CellHead
		        {
		          font-family:Trebuchet MS, Verdana, Arial;
		          font-size:11px;
		          background-color: #35650F;
		          color:#FFFFFF;
		          padding: 4px;
		        }
		        
		        .CellHead99
		        {
		          font-family:Trebuchet MS, Verdana, Arial;
		          font-size:11px;
		          background-color: #35650F;
		          color:#FFFFFF;
		          padding: 4px;
		          text-align:left;
		        }
		        
		        .cellInfo1
		        {
		          font-family:Trebuchet MS, Verdana, Arial;
		          font-size:11px;
		          background-color: #EBF8E2;
		          padding: 2px;
		        }
		        
		        .cellInfo2
		        {
		          font-family:Trebuchet MS, Verdana, Arial;
		          font-size:11px;
		          background-color: #DEDFDE;
		          padding: 2px;
		        }
		        
		        .cellInfo
		        {
		          font-family:Trebuchet MS, Verdana, Arial;
		          font-size:11px;
		          background-color: #FFFFFF;
		          padding: 2px;
		        }
		        
		        tr.row:hover  td
		        {
		          background-color: #9ad9ae;
		        }

		        .onlyCell:hover
		        {
		          background-color: #9ad9ae;
		        }
		        
		        .StyleDIV
		          {
		            background-color: #FFFFFF;
		            border: 1px solid rgb(201, 201, 201); 
		            padding: 5px; width: 99%; 
		            min-height: 50px; 
		            border-radius: 5px 5px 5px 5px;
		          }
		        .Style2DIV
		          {
		            background-color: #FFFFFF;
		            border: 1px solid rgb(201, 201, 201); 
		            padding: 5px; width: 96%; 
		            min-height: 50px; 
		            border-radius: 5px 5px 5px 5px;
		          }
		          .TRform
		          {
		            padding-right:3px; 
		            padding-top:15px; 
		            font-family:Trebuchet MS, Verdana, Arial; 
		            font-size:12px;
		          }
		        </style>';
		  }

		function getNovedadesTotal(){
    	
    	  $this -> Style();

			$mHtml = "";
 
			$mResultCantTot = $this -> getTotalDespachos( $_REQUEST['fec_inicia'], $_REQUEST['fec_finali'] , $_REQUEST);
			$mDespac        = $this -> getDespachos( $_REQUEST['fec_inicia'], $_REQUEST['fec_finali'], $_REQUEST );
			$mDespacArray   = $this -> matrizToArray($mDespac); 
			$mNovedades     = $this -> getAllNoveda($this -> arrayToString($mDespacArray));
			$mLimpio        = $this -> getCantAllNovedaDespac($this -> arrayToString($mDespacArray), $this -> arrayToString( $mNovedades ));
	 

			$tNovedades   = sizeof( $mNovedades );
			$tNovedaLim   = $this -> matrizToArray( $mLimpio );
			$porNoveLim   = round((($mLimpio['cantLimpio'] *100)/$tNovedades),2);
			$porNoveNoLim   = round((($mLimpio['cantNoLimpio'] *100)/$tNovedades),2);
 
			$mHtml .= "<div id='container1' class='StyleDIV'>";
				$mHtml .= "<table>"; 
					$mHtml .= "<tr>";
						$mHtml .= "<td class='CellHead' colspan='8'><center>GENERAL</center></td>";
					$mHtml .= "</tr>"; 
					$mHtml .= "<tr>";
						$mHtml .= "<td class='CellHead' >Despachos Generados</td>";
						$mHtml .= "<td class='CellHead' >Total Novedades</td>";
						$mHtml .= "<td class='CellHead' >N. Novedades Limpio</td>";
						$mHtml .= "<td class='CellHead' >%</td>";
						$mHtml .= "<td class='CellHead' >N. Novedades No Limpio</td>";
						$mHtml .= "<td class='CellHead' >%</td>";
					$mHtml .= "</tr>";
					$mHtml .= "<tr center>"; 
						$mHtml .= "<td class='cellInfo2' onclick='detalleDespachosTotal();' style='cursor: pointer'>". $mResultCantTot ."</td>";
						$mHtml .= "<td class='cellInfo2' >". $tNovedades ."</td>";
						$mHtml .= "<td class='cellInfo2' >". $mLimpio['cantLimpio'] ."</td>";
						$mHtml .= "<td class='cellInfo2' >". $porNoveLim ."</td>";
						$mHtml .= "<td class='cellInfo2' >". $mLimpio['cantNoLimpio'] ."</td>";
						$mHtml .= "<td class='cellInfo2' >". $porNoveNoLim ."</td>"; 	
					$mHtml .= "</tr>"; 
				$mHtml .= "</table>"; 
  			$mHtml .= "<br>";

  
  			$mHtml .= "<br>"; 
				$mHtml .= "<table  width=\"100%\">"; 
				$mHtml .= "<tr>";
						$mHtml .= "<td class='CellHead' colspan='8'><center>DETALLADO POR DIAS</center></td>";
				$mHtml .= "</tr>"; 
				$mHtml .= "<tr>";
						$mHtml .= "<td class='CellHead' >Fecha</td>";
						$mHtml .= "<td class='CellHead' >Despachos Generados</td>";
						$mHtml .= "<td class='CellHead' >Total Novedades</td>";
						$mHtml .= "<td class='CellHead' >N. Novedades Limpio</td>";
						$mHtml .= "<td class='CellHead' >%</td>";
						$mHtml .= "<td class='CellHead' >N. Novedades No Limpio</td>";
						$mHtml .= "<td class='CellHead' >%</td>";
				$mHtml .= "</tr>";

			$fec_auxiliar = $_REQUEST['fec_inicia'];
			$i = 0;
			while ( $fec_auxiliar <= $_REQUEST['fec_finali']) {
  
					$mResultCantTot = $this -> getTotalDespachos( $fec_auxiliar, $fec_auxiliar, $_REQUEST );
					$mDespac        = $this -> getDespachos( $fec_auxiliar, $fec_auxiliar, $_REQUEST	 );
					$mDespacArray   = $this -> matrizToArray($mDespac); 
					$mNovedades     = $this -> getAllNoveda($this -> arrayToString($mDespacArray));
					$mLimpio        = $this -> getCantAllNovedaDespac($this -> arrayToString($mDespacArray), $this -> arrayToString( $mNovedades ));
			 

					$tNovedades     = sizeof( $mNovedades );
					$tNovedaLim     = $this -> matrizToArray( $mLimpio );
					$porNoveLim     = round((($mLimpio['cantLimpio'] *100)/$tNovedades),2);
					$porNoveNoLim   = round((($mLimpio['cantNoLimpio'] *100)/$tNovedades),2);
 
					if ($mResultCantTot != 0) {
						$mHtml .= "<tr center>"; 
							$mHtml .= '<input id="consec'.$i.'" type="hidden" value="'.$fec_auxiliar.'" name="consec'.$i.'">';
							$mHtml .= "<td class='cellInfo2' >". strftime ( '%A %d de %B del %Y', strtotime (  $fec_auxiliar ) ) . "</td>";
							$mHtml .= "<td class='cellInfo2' onclick='detalleDespachos(".$i.")' style='cursor: pointer'>". $mResultCantTot ."</td>";
							$mHtml .= "<td class='cellInfo2' >". $tNovedades ."</td>";
							$mHtml .= "<td class='cellInfo2' >". $mLimpio['cantLimpio'] ."</td>";
							$mHtml .= "<td class='cellInfo2' >". $porNoveLim ."</td>";
							$mHtml .= "<td class='cellInfo2' >". $mLimpio['cantNoLimpio'] ."</td>";
							$mHtml .= "<td class='cellInfo2' >". $porNoveNoLim ."</td>"; 	
						$mHtml .= "</tr>"; 
					}

				$fec_auxiliar = date ( 'Y-m-d' , strtotime ( '+1 day' , strtotime ( $fec_auxiliar ) ) );
 				$i++;
			}

				$mHtml .= "</table>"; 
 			$mHtml .= "</div>"; 
 
 			echo $mHtml;
		}

		function getNovedadesLimpio(){

			$this -> getNovedadesInform( '1', $_REQUEST);
		}	

		function getNovedadesNoLimpio(){

			$this -> getNovedadesInform( '0', $_REQUEST);
		}

		function getNovedadesInform( $ind_limpio, $_mData ){

    	  $this -> Style();

			$mHtml = "";
  			
  			$despachos = $this -> getDespachos( $_mData['fec_inicia'], $_mData['fec_finali'], $_mData );
  			$despachos = $this -> matrizToArray($despachos);
  			$despachos = $this -> arrayToString($despachos);

  			#Matriz Novedades Limpias
  			$mData = $this -> getNovedaXDespac($despachos, 'M', $ind_limpio );

  			#Total Novedades Limpias
  			$mSql = $this -> getNovedaXDespac($despachos, 'sql', $ind_limpio );
  			$mSql = "SELECT SUM(y.suma) AS total
  					   FROM ( {$mSql} ) y 
  				   GROUP BY y.ind_group ";
			$mConsul = new Consulta($mSql, $this -> conexion);
			$mTotalNov = $mConsul -> ret_arreglo();

			#Total Todas Novedades
  			$mSql = $this -> getNovedaXDespac($despachos, 'sql' );
  			$mSql = "SELECT SUM(y.suma) AS total
  					   FROM ( {$mSql} ) y 
  				   GROUP BY y.ind_group ";
			$mConsul = new Consulta($mSql, $this -> conexion);
			$mTotalTodas = $mConsul -> ret_arreglo();
 
			$mHtml .= "<div id='container1' class='StyleDIV'>";
				$mHtml .= "<table>"; 
					$mHtml .= "<tr>";
						$mHtml .= "<td class='CellHead' colspan='8'><center>GENERAL</center></td>";
					$mHtml .= "</tr>"; 
					$mHtml .= "<tr>";
						$mHtml .= "<td class='CellHead' >Total De Novedades</td>";
						$mHtml .= "<td class='CellHead' >% De Participacion</td>"; 
					$mHtml .= "</tr>";
					$mHtml .= "<tr center>"; 
						$mHtml .= "<td class='cellInfo2' >". $mTotalNov[0] ."</td>";
						$mHtml .= "<td class='cellInfo2' >".( round(($mTotalNov[0] * 100 / $mTotalTodas[0]),2) )."%</td>"; 
					$mHtml .= "</tr>"; 
				$mHtml .= "</table>"; 
  			$mHtml .= "<br>";

  
  			$mHtml .= "<br>"; 

			$mHtml .= "<table  width=\"100%\">"; 
			$mHtml .= "<tr>";
				$mHtml .= "<td class='CellHead' colspan='8'><center>DETALLADO POR DIAS</center></td>";
			$mHtml .= "</tr>"; 
			$mHtml .= "<tr>";
				$mHtml .= "<td class='CellHead' >Novedad</td>";
				$mHtml .= "<td class='CellHead' >Total De Novedades</td>";
				$mHtml .= "<td class='CellHead' >Porcentaje De Participacion</td>"; 	
			$mHtml .= "</tr>";
 
			$mData = SortMatrix($mData, "suma","DESC");
 

			$mSize = sizeof($mData);
			for ($i=0; $i < $mSize; $i++) { 
				$mHtml .= '<tr>';
					$mHtml .= '<td class="cellInfo2">'.$mData[$i][nom_noveda].'</td>';
					$mHtml .= '<td class="cellInfo2">'.$mData[$i][suma].'</td>';
					$mHtml .= '<td class="cellInfo2">'.( round(($mData[$i][suma] * 100 / $mTotalNov[0]),2) ).'%</td>';
				$mHtml .= '</tr>'; 

			}
     		$mHtml .= "</table>"; 
 			$mHtml .= "</div>"; 
 
 			echo $mHtml;
		}

		function getNovedaXDespac2( $despac, $noveda ){

			$mSql = "SELECT cantidad
					FROM (
								(
									 SELECT COUNT( a.cod_noveda ) AS cantidad
									   FROM ".BASE_DATOS.".tab_despac_noveda a
									  WHERE a.cod_noveda = " . $noveda . "  
									    AND a.num_despac IN ( " . $despac . ")
								)
								UNION 
								(
									 SELECT COUNT( a.cod_noveda ) AS cantidad
									   FROM ".BASE_DATOS.".tab_despac_contro a
									  WHERE a.cod_noveda = " . $noveda . "  
									    AND a.num_despac IN ( " . $despac . ")
								)
								UNION 
								(
									 SELECT COUNT( a.cod_noveda ) AS cantidad
									   FROM tab_recome_asigna a
									  WHERE a.num_despac IN ( " . $despac . " ) 
									    AND a.cod_noveda = " . $noveda . "  
									    AND a.ind_ejecuc = 0
								)
								UNION 
								(
									 SELECT COUNT( b.cod_noved2 ) AS cantidad
									   FROM tab_recome_asigna b
									  WHERE b.num_despac IN ( " . $despac . " ) 
									    AND b.cod_noved2 = " . $noveda . "   
									    AND b.ind_ejecuc = 1 
								)
							)x
					   ";
 
			$mConsul = new Consulta($mSql, $this -> conexion);
			$mResult = $mConsul -> ret_matriz();
 
			return $mResult;
		}


		function getNovedaXDespac( $despac, $mIndReturn, $mLimpio = NULL ){

			$mSql = "SELECT UPPER(x.nom_noveda) AS nom_noveda, SUM( x.cantidad ) AS suma, 
							x.ind_limpio, x.tab_origen, '1' AS ind_group
					   FROM (
								(
									SELECT a.cod_noveda, '1' AS tab_origen, 
									  COUNT(a.cod_noveda) AS cantidad, 
									  b.nom_noveda, b.ind_limpio
									 FROM " . BASE_DATOS . ".tab_despac_noveda a
					           INNER JOIN " . BASE_DATOS . ".tab_genera_noveda b 
					                   ON a.cod_noveda = b.cod_noveda
									WHERE a.num_despac IN ( {$despac} )
									 GROUP BY a.cod_noveda
								)
								UNION 
								(
									SELECT a.cod_noveda, '2' AS tab_origen, 
									  COUNT(a.cod_noveda) AS cantidad, 
									  b.nom_noveda, b.ind_limpio
									 FROM " . BASE_DATOS . ".tab_despac_contro a
					           INNER JOIN " . BASE_DATOS . ".tab_genera_noveda b 
					                   ON a.cod_noveda = b.cod_noveda
									WHERE a.num_despac IN ( {$despac} )
									 GROUP BY a.cod_noveda
								)
								UNION 
								(
									SELECT a.cod_noveda, '3' AS tab_origen, 
									  COUNT(a.cod_noveda) AS cantidad, 
									  b.nom_noveda, '0' AS ind_limpio
									 FROM " . BASE_DATOS . ".tab_recome_asigna a
					           INNER JOIN " . BASE_DATOS . ".tab_genera_noveda b 
					                   ON a.cod_noveda = b.cod_noveda
									WHERE a.num_despac IN ( {$despac} ) 
									 GROUP BY a.cod_noveda
								)
								UNION 
								(
									SELECT 'YY' AS cod_noveda, '4' AS tab_origen, 
									  COUNT(a.cod_noveda) AS cantidad, 
									  'SOLUCION RECOMENDACION' AS nom_noveda, 
									  '1' AS ind_limpio
									 FROM " . BASE_DATOS . ".tab_recome_asigna a
									WHERE a.num_despac IN ( {$despac} ) 
									  AND a.ind_ejecuc = 1 
									 GROUP BY a.cod_noveda
								)
						  ) x 
					WHERE 1=1 ";

			$mSql .= $mLimpio != NULL ? " AND x.ind_limpio = ".$mLimpio : "";

			$mSql .= " GROUP BY x.cod_noveda
					   ORDER BY x.nom_noveda

					   ";

 			if($mIndReturn == 'sql' )
 				return $mSql;
 			else{
				$mConsul = new Consulta($mSql, $this -> conexion);
				return $mResult = $mConsul -> ret_matriz();
 			}
		}

		function filterNoveda( $noveda, $ind_limpio ){

			$mSql = "SELECT a.cod_noveda 
					   FROM ".BASE_DATOS.".tab_genera_noveda a
					  WHERE a.cod_noveda IN ( " . $noveda . " ) 
					  	AND a.ind_limpio = '" . $ind_limpio . "'
				   GROUP BY a.nom_noveda";
 
			$mConsul = new Consulta($mSql, $this -> conexion);
			$mResult = $mConsul -> ret_matriz();
 
			return $mResult;
		}

		function getDetailDespac(){ 
 
			session_start();
			$mHtml = "";

			$despachos = $this -> getDespachos($_REQUEST['fec_inicia'],$_REQUEST['fec_finali'],$_REQUEST);  
			$despachos = $this -> matrizToArray($despachos);
            $mHtml .=  '<a onclick="exportExcel()"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" border="0"></a>';
    
				$mHtml .= "<center>";  
				$mHtml .= "<table id='tabDetail'>";  
				$mHtml .= "<tr>";
						$mHtml .= "<td class='CellHead' >#</td>";
						$mHtml .= "<td class='CellHead' >N. Despacho Satt</td>";
						$mHtml .= "<td class='CellHead' >Tipo de Despacho</td>";
						$mHtml .= "<td class='CellHead' >Placa</td>";
						$mHtml .= "<td class='CellHead' >Viaje</td>";
						$mHtml .= "<td class='CellHead' >N. Novedades Total</td>";
						$mHtml .= "<td class='CellHead' >N. Novedades Limpio</td>";
						$mHtml .= "<td class='CellHead' >%</td>";
						$mHtml .= "<td class='CellHead' >N. Novedades No Limpio</td>";
						$mHtml .= "<td class='CellHead' >%</td>";
				$mHtml .= "</tr>";
 
			for ($i=0; $i < sizeof($despachos); $i++) { 

				$mNovedadesDespac     = $this -> getAllNoveda($despachos[$i]);
				$mData = $this -> getDataDespac($despachos[$i]);
				$mNovedadesLimpio = $this -> getCantAllNovedaDespac($despachos[$i]);
 					$despac_padre = $mData[0]['num_despac'];
					$mHtml .= "<tr center>"; 
		                $padre = 'index.php?cod_servic=3302&window=central&despac='.$despac_padre.'&opcion=1';
          				$link = '<a href="'.$padre.'" target="_blank" style="color: blue">'.$despac_padre.'</a>';
						$mHtml .= "<td class='cellInfo2' >" . ($i + 1) . "</td>";
						$mHtml .= "<td class='cellInfo2' >" . $link . "</td>";
						$mHtml .= "<td class='cellInfo2' >" . $mData[0]['nom_tipdes'] . "</td>";
						$mHtml .= "<td class='cellInfo2' >" . $mData[0]['num_placax'] . "</td>";
						$mHtml .= "<td class='cellInfo2' >" . ( $mData[0]['viaje'] == '' ? 'CONSOLIDADO' : $mData[0]['viaje'] ) . "</td>";
						$mHtml .= "<td class='cellInfo2' >" . sizeof( $mNovedadesDespac ) . "</td>";
						$mHtml .= "<td class='cellInfo2' >" . $mNovedadesLimpio['cantLimpio'] . "</td>";
						$mHtml .= "<td class='cellInfo2' >" . round((($mNovedadesLimpio['cantLimpio'] *100)/sizeof( $mNovedadesDespac )),2) . "</td>";
						$mHtml .= "<td class='cellInfo2' >" . $mNovedadesLimpio['cantNoLimpio'] . "</td>"; 
						$mHtml .= "<td class='cellInfo2' >" . round((($mNovedadesLimpio['cantNoLimpio'] *100)/sizeof( $mNovedadesDespac )),2) . "</td>"; 
					$mHtml .= "</tr>"; 
			}
				$mHtml .= "</table>"; 
				$mHtml .= "</center>"; 
			$_SESSION[xls_InformViajes] = $mHtml; 
			echo $mHtml;
		}
 
 		function getNovedaXName( $noveda ){

			$mSql = "SELECT a.nom_noveda 
					   FROM ".BASE_DATOS.".tab_genera_noveda a
					  WHERE a.cod_noveda IN ( " . $noveda . " )  
				   GROUP BY a.nom_noveda";
 
			$mConsul = new Consulta($mSql, $this -> conexion);
			$mResult = $mConsul -> ret_matriz();
 
			return $mResult; 			
 		}

 		function getDataDespac( $despac ){
 
			$mSql = "SELECT a.num_despac, d.nom_tipdes, b.num_placax,
							c.num_despac AS viaje
						    FROM ".BASE_DATOS.".tab_despac_despac a
					  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b
					  		  ON a.num_despac = b.num_despac
					  LEFT JOIN tab_despac_corona c
					  		  ON a.num_despac = c.num_dessat
					  INNER JOIN tab_genera_tipdes d
					  		  ON a.cod_tipdes = d.cod_tipdes
					  	   WHERE a.num_despac = '".$despac."'";
 
			$mConsul = new Consulta($mSql, $this -> conexion);
			$mResult = $mConsul -> ret_matriz();
 
			return $mResult;
 		}

 
		function getDespachos( $fec_inicia, $fec_finali, $_mData){
  
			$mSql = "SELECT a.num_despac
						    FROM ".BASE_DATOS.".tab_despac_despac a
					  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b
					  		  ON a.num_despac = b.num_despac
					  LEFT JOIN ".BASE_DATOS.".tab_despac_corona c
					  		  ON a.num_despac = c.num_dessat
						    WHERE a.fec_creaci BETWEEN '". $fec_inicia ." 00:00:00' 
						      AND '".$fec_finali." 23:59:59'
					          AND b.cod_transp = '".$_mData['cod_transp']."' ";

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


 
			$mConsul = new Consulta($mSql, $this -> conexion);
			$mResult = $mConsul -> ret_matriz();

			return $mResult;
		}

		function getTotalDespachos( $fec_inicia, $fec_finali, $_mData ){
 		
 
		 	$mSql = "SELECT COUNT(a.num_despac) AS total
						    FROM ".BASE_DATOS.".tab_despac_despac a
					  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b
					  		  ON a.num_despac = b.num_despac
					  LEFT JOIN ".BASE_DATOS.".tab_despac_corona c
					  		  ON a.num_despac = c.num_dessat
						    WHERE a.fec_creaci BETWEEN '". $fec_inicia ." 00:00:00' 
						      AND '".$fec_finali." 23:59:59'
					          AND b.cod_transp = '".$_mData['cod_transp']."' ";

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

       
 
			$mConsul = new Consulta($mSql, $this -> conexion);
			$mResult = $mConsul -> ret_matriz();

				return $mResult[0][0];
		}

		function getCantAllNovedaDespac(  $despac, $noveda = NULL ){
			/******************************************************************/
			$mSql1 = " SELECT COUNT( a.cod_noveda ) AS total, b.ind_limpio
									   FROM tab_despac_contro a 
								 INNER JOIN tab_genera_noveda b
								 		 ON a.cod_noveda = b.cod_noveda	
								 	  WHERE a.num_despac IN (".$despac.")
								   GROUP BY b.ind_limpio";

			$mConsul = new Consulta($mSql1, $this -> conexion);
			$mTotal1 = $mConsul -> ret_matriz();
			/*******************************************************************/

			/*******************************************************************/
 			$mSql2 = " SELECT COUNT( a.cod_noveda ) AS total, b.ind_limpio
									   FROM tab_despac_noveda a 
								 INNER JOIN tab_genera_noveda b
								 		 ON a.cod_noveda = b.cod_noveda	
								 	  WHERE a.num_despac IN (".$despac.")
								   GROUP BY b.ind_limpio";

			$mConsul = new Consulta($mSql2, $this -> conexion);
			$mTotal2 = $mConsul -> ret_matriz();
			/*******************************************************************/

			/*******************************************************************/
 			$mSql3 = " SELECT COUNT( a.cod_noveda ) AS total, b.ind_limpio
									   FROM tab_recome_asigna a 
								 INNER JOIN tab_genera_noveda b
								 		 ON a.cod_noveda = b.cod_noveda	
								 	  WHERE a.num_despac IN (".$despac.")
								   GROUP BY b.ind_limpio";
			$mConsul = new Consulta($mSql3, $this -> conexion);
   		    $mTotal3 = $mConsul -> ret_matriz();			
 			/*******************************************************************/
 
 			$mResult = array(
 							"cantLimpio" => 0,
 							"cantNoLimpio"=> 0);
 			
 			for ($i=0; $i < 2; $i++) { 
 
 					if($mTotal1[$i]['ind_limpio'] == 1){
 						$mResult['cantLimpio'] += $mTotal1[$i]['total'];
 					}

 					if($mTotal2[$i]['ind_limpio'] == 1){
 						$mResult['cantLimpio'] += $mTotal2[$i]['total'];
 					}

 					if($mTotal3[$i]['ind_limpio'] == 1){
 						$mResult['cantLimpio'] += $mTotal3[$i]['total'];
 					}
 					if($mTotal1[$i]['ind_limpio'] == 0){
 						$mResult['cantNoLimpio'] += $mTotal1[$i]['total'];
 					}

 					if($mTotal2[$i]['ind_limpio'] == 0){
 						$mResult['cantNoLimpio'] += $mTotal2[$i]['total'];
 					}

 					if($mTotal3[$i]['ind_limpio'] == 0){
 						$mResult['cantNoLimpio'] += $mTotal3[$i]['total'];
 					}
 
 			}

			return $mResult;
		}

		function getNovedadesNC( $despac ){
 
			$mSql = "SELECT a.cod_noveda , a.num_despac
					   FROM ".BASE_DATOS.".tab_despac_contro a 
					  WHERE a.num_despac IN ( " .$despac. " )";

			$mConsul = new Consulta($mSql, $this -> conexion);
			$mResult = $mConsul -> ret_matriz();

			return $mResult;
		}

		function getNovedadesPC( $despac ){
 

			$mSql = "SELECT a.cod_noveda , a.num_despac
					   FROM ".BASE_DATOS.".tab_despac_noveda a 
					  WHERE a.num_despac IN ( " .$despac. " )";

			$mConsul = new Consulta($mSql, $this -> conexion);
			$mResult = $mConsul -> ret_matriz();

			return $mResult;
		}

		function getAllNoveda( $despac ){
 			 
			$mResult = $this -> matrizToArray( $this -> getNovedadesNC( $despac ) );
			$mResult = array_merge($mResult, $this -> matrizToArray( $this -> getNovedadesPC( $despac )));
			$mResult = array_merge($mResult, $this -> matrizToArray( $this -> getRecomendaciones( $despac )));
 			 
			return $mResult;
		}

		function getRecomendaciones( $despac ){
 
			$mSql = "SELECT a.cod_noveda  , a.num_despac
					   FROM tab_recome_asigna a
					  WHERE a.num_despac IN ( " .$despac. " ) 
					    AND a.ind_ejecuc = 0
					  UNION 
					 SELECT b.cod_noved2, b.num_despac
					   FROM tab_recome_asigna b
					  WHERE b.num_despac IN ( " .$despac. " ) 
					    AND b.ind_ejecuc = 1";

			$mConsul = new Consulta($mSql, $this -> conexion);
			$mResult = $mConsul -> ret_matriz();

			return $mResult;
		}

		function matrizToArray ( $matriz ){
			
			$result = array();

			for ($i=0; $i < sizeof($matriz); $i++) { 
				 array_push($result, $matriz[$i][0]);
			}

			return $result;
		}

		function arrayToString ( $arreglo ){

			$result;

			$result = " '".join("', '",$arreglo)."' ";
			
			return $result;
		}

		/*! \fn: getTransp
		*  \brief: Trae las transportadoras
		*  \author: Ing. Fabian Salinas
		*	\date: 17/06/2015
		*	\date modified: dia/mes/año
		*  \param: 
		*  \return:
		*/ 
		function getTransportadoras(){
 
			$mSql = "SELECT a.cod_tercer, UPPER(a.abr_tercer) AS nom_tercer 
						FROM ".BASE_DATOS.".tab_tercer_tercer a 
				  INNER JOIN ".BASE_DATOS.".tab_tercer_activi b 
						  ON a.cod_tercer = b.cod_tercer 
					   WHERE b.cod_activi = ".COD_FILTRO_EMPTRA." 
						 AND a.cod_estado = ".COD_ESTADO_ACTIVO."
						 AND a.abr_tercer LIKE '%".$_REQUEST['term']."%'";

			if ( $_SESSION['datos_usuario']['cod_perfil'] == NULL ) 
			{#PARA EL FILTRO DE EMPRESA

				$filtro = new Aplica_Filtro_Usuari( 1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_usuari] );

				if ( $filtro -> listar( $this -> conexion ) ) : 
					$datos_filtro = $filtro -> retornar();
					$mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
				endif;
			}
			else
			{#PARA EL FILTRO DE EMPRESA
			
				$filtro = new Aplica_Filtro_Perfil( 1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_perfil] );
				if ( $filtro -> listar( $this -> conexion ) ) : 
					$datos_filtro = $filtro -> retornar();
					$mSql .= " AND a.cod_tercer = '".$datos_filtro[clv_filtro]."' ";
				endif;
			}	
				$mSql .= " ORDER BY a.abr_tercer ASC ";


				$consulta  = new Consulta($mSql, $this -> conexion);
				$mTanspor  = $consulta -> ret_matriz();

				$transportadoras = array();
				for($i=0; $i<count( $mTanspor ); $i++){
					$transportadoras[] = array('value' => $mTanspor[$i][nom_tercer], 'label' => utf8_decode($mTanspor[$i][cod_tercer]."-".$mTanspor[$i][nom_tercer]) );
				}
				echo json_encode( $transportadoras );
 		}

		function getModalidades(){
 
			$mSql = "SELECT a.cod_tipdes, UPPER(a.nom_tipdes) AS nom_tipdes 
						FROM ".BASE_DATOS.".tab_genera_tipdes a   
					   WHERE a.nom_tipdes LIKE '%".$_REQUEST['term']."%'";

	 
				$consulta  = new Consulta($mSql, $this -> conexion);
				$mTanspor  = $consulta -> ret_matriz();

				$transportadoras = array();
				for($i=0; $i<count( $mTanspor ); $i++){
					$transportadoras[] = array('value' => $mTanspor[$i][nom_tipdes], 'label' => utf8_decode($mTanspor[$i][cod_tipdes]."-".$mTanspor[$i][nom_tipdes]) );
				}
				echo json_encode( $transportadoras );
 		}
		//Inicio Función exportExcel
		private function exportExcel()
		{
		    session_start();
		    $archivo = "informe_operaciones".date( "Y_m_d_H_i" ).".xls";
		    header('Content-Type: application/octetstream'); 
		    header('Content-Disposition: attachment; filename="'.$archivo.'"'); 
		    header('Pragma: public');
		    ob_clean();
		    echo $HTML = $_SESSION[xls_InformViajes];
		}
		//Fin Función exportExcel
	}

	$execute = new AjaxDespachos();