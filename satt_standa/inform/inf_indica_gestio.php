<?php

/****************************************************************************
  NOMBRE:   MODULO INFORME DE NOVEDADES DE LA TRANSPORTADORA
  FUNCION:  INFORME DE NOVEDADES DE LA TRANSP
  AUTOR: JORGE PRECIADO
  FECHA CREACION : 17 FEBRERO 2012
 ****************************************************************************/

class IndicadoresGestion
{

    var $conexion;

    function __construct( $conexion )
    {
        $this -> conexion = $conexion;
		
        ini_set("memory_limit", "128M");
		
        switch ( $_REQUEST['opcion'] )
        {
            case "1":
											$this -> Informe();
											break;
														
            case "2":
											$this -> inf_despac_servic( $_REQUEST["servicio"] );
											break;
														
						default:
											$this -> filtro();
											break;
        }
    }
	
	//-------------------------------------------------------
  //@funct.  :  inf_despac_servic()
  //@author  :  MIGUEL A. GARCIA  [ MIK ]
  //@brief   :  METODO PARA MOSTRAR EL INFORME DE LOS DESPACHOS 
  //            DE ACUERDO AL TIPO DE SERVICIO
	//-------------------------------------------------------
	function inf_despac_servic( $servicio = NULL )
	{
		echo 	" <style>
							.cellHead
							{
								padding:3px 10px;
								background-color:#257038;
								color:#fff;
								font-weight:bold;
								text-align:center;
							}

							.cellSubHead
							{
								padding:3px 10px;
								background-color:#04B431;
								color:#fff;
								font-weight:bold;
								text-align:center;
							}
					
							.cellInfo
							{
								padding:3px 10px;
								background-color:#fff;
								color:#000;
								text-align:center;
								border:1px solid #ddd;
							}
						</style>
					";

		/*
		//---------------------------------------------
    include( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );
    echo '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/estilos/dinamic_list.css" type="text/css">';
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/dinamic_list.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
		//---------------------------------------------
		*/


		$formulario = new Formulario( "index.php\" enctype=\"multipart/form-data\"", "post", "Indicadores de Gestión", "formulario\" id=\"formularioID" );

		//---------------------------------------------
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/inf_indica_gestio.js\"></script>\n";
		//---------------------------------------------

		//---------------------------------------------
		//VEHICULOS TOTALES MONITOREADOS POR SERVICIO
		//$vehicu_tomose = $this -> getVehiculosMonitoreados( $_REQUEST['fec_inicio'], $_REQUEST['fec_finalx'], $servicio );
		//---------------------------------------------

    $colspan = "20";

		echo "<table width='100%' >";
		echo "<tr>";
		echo "<td class=cellHead colspan=" . $colspan . ">:: VEHICULOS MONITOREADOS :: TIPO DE SERVICIO [ " . $this -> getDataServicio( $servicio ) . " ] :: </td>";
		//echo "<td class=cellHead colspan=" . $colspan . ">:: VEHICULOS MONITOREADOS [ " . $vehicu_tomose . " ] :: TIPO DE SERVICIO [ " . $this -> getDataServicio( $servicio ) . " ] :: </td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class=cellHead colspan=" . $colspan . ">Desde ".$_REQUEST['fec_inicio']." Hasta ".$_REQUEST['fec_finalx']."</td>";
		echo "</tr>";

		//---------------------------------------------
		//DATOS DE LOS DESPACHOS ENCONTRADOS DE LA TRANSPORTADORA POR SERVICIO
		$despac_transp = $this -> getTransp_VehiculosMonitoreados( $_REQUEST['fec_inicio'], $_REQUEST['fec_finalx'], $servicio );
		//---------------------------------------------

		$contad = 1;
		foreach( $despac_transp AS $itemxx )
		{
			//---------------------------------------------
			//DATOS DE LOS DESPACHOS ENCONTRADOS DE LA TRANSPORTADORA POR SERVICIO
			$despac_dadese = $this -> getData_VehiculosMonitoreados( $_REQUEST['fec_inicio'], $_REQUEST['fec_finalx'], $servicio, $itemxx["cod_transp"] );
			//---------------------------------------------

      echo 	"	<tr>
      					<td class=cellHead colspan=" . $colspan . ">" . $itemxx["abr_tercer"] . " ( " . $itemxx["cod_transp"] . " )</td>
    					</tr>
    				";

			echo " 	<tr>
								<td class=cellSubHead>NRO.</td>
								<td class=cellSubHead>DESPACHO</td>
								<td class=cellSubHead>RUTA</td>
								<td class=cellSubHead>PLACA</td>
								<td class=cellSubHead>CONDUCTOR</td>
							</tr>
						";

			//$contad = 1;
			foreach( $despac_dadese AS $despac )
			{
    		$despac["num_despac"] = "<a href=\"index.php?cod_servic=1295&window=central&despac=".$despac["num_despac"]."&opcion=3 \"target=\"centralFrame\">".$despac["num_despac"]."</a>";

        echo 	"	<tr>
        					<td class=cellInfo>" . $contad++ . "</td>
        					<td class=cellInfo>" . $despac["num_despac"] . "</td>

        					<td class=cellInfo>" . $this -> getDataRutaxx( $despac["cod_rutasx"] ) . "</td>
        					<td class=cellInfo>" . $despac["num_placax"] . "</td>
        					<td class=cellInfo>" . $this -> getDataConduc( $despac["cod_conduc"] ) . "</td>
      					</tr>
      				";
			}

		}

		echo "</table>
					<br /><br />";

		$formulario -> nueva_tabla();
		$btn_volver = "
										<center>
											<a href=\"index.php?cod_servic=" . $_REQUEST["cod_servic"] . "&window=central&servicio=" . $_REQUEST["servicio"] . " \"target=\"centralFrame\"> 
												<div class='crmButton small save'>Volver</div>
											</a>
										</center>
									";

		echo $btn_volver;
		
		$formulario -> cerrar();

	}
	//-------------------------------------------------------

	function Informe()
	{
		echo "	<style>
					.cellHead
					{
						padding:3px 10px;
						background-color:#257038;
						color:#fff;
						font-weight:bold;
						text-align:center;
					}
					
					.cellInfo
					{
						padding:3px 10px;
						background-color:#fff;
						color:#000;
						font-weight:bold;
						text-align:center;
						border:1px solid #ddd;
					}

					#exportExcel{
	    				color: #ffffff;
	    				cursor: pointer;
	    			}

	    			#exportExcel:hover{
	    				color: #cbffd7;
	    			}
				</style>";
		
		$sql = "SELECT a.cod_tipser, a.nom_tipser
				FROM ".BASE_DATOS.".tab_genera_tipser a 
				WHERE a.ind_estado = '1' ";
				
		$consulta = new Consulta($sql, $this->conexion);
        $servicios = $consulta->ret_matriz('i');
		
		$formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", 
									 "Indicadores de Gestión", 
									 "formulario\" id=\"formularioID");
		
		//---------------------------------------------
    echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/inf_indica_gestio.js\"></script>\n";
    echo '<script type="text/javascript" language="JavaScript" src="../'. DIR_APLICA_CENTRAL .'/js/exportExcel/FileSaver.js"></script>';
    echo '<script type="text/javascript" language="JavaScript" src="../'. DIR_APLICA_CENTRAL .'/js/exportExcel/xlsx.full.min.js"></script>';
		//---------------------------------------------
    	echo "<table id='tableHead' width='100%'>";
			echo "<tr>";
				echo "<td class=cellHead>";
					echo "Desde ".$_REQUEST['fec_inicio']." Hasta ".$_REQUEST['fec_finalx'];
					echo " <a id='exportExcel' onclick='exporExcel( \"tableVehiNov\", \"exportExcel\", \"Vehiculos_Monitoreados_Novedades\" )'>[ Excel ]</a>";
				echo "</td>";
			echo "</tr>";
		echo "</table>";
		echo "<table id='tableVehiNov' width='100%' >";
    
		$c1 = $this -> getRutasCreadas( $_REQUEST['fec_inicio'], $_REQUEST['fec_finalx'] );
		$c2 = $this -> getVehiculosMonitoreados( $_REQUEST['fec_inicio'], $_REQUEST['fec_finalx'] );
		$c3 = $this -> getNovedades( $_REQUEST['fec_inicio'], $_REQUEST['fec_finalx'] );
    
			echo "<tr>";
			echo "<td class=cellHead rowspan=2 >RUTAS CREADAS</td>";
			echo "<td class=cellHead colspan='".sizeof( $servicios )."'>TOTAL VEH&Iacute;CULOS MONITOREADOS: ". $c2 ."</td>";
			echo "<td class=cellHead colspan='".sizeof( $servicios )."'>TOTAL NOVEDADES: ". $c3 ."</td>";

		echo "</tr>";
		
		echo "<tr>";
		if( $servicios ) 
		foreach( $servicios as $servicio )
		{
			echo "<td class=cellHead >".$servicio[1]."</td>";
		} 
		foreach( $servicios as $servicio )
		{
			echo "<td class=cellHead >".$servicio[1]."</td>";
		}
		echo "</tr>";

		
		echo "<tr>";
		echo "<td class=cellInfo >".$c1."</td>";
    
	    if( $servicios )
	    {
	      foreach( $servicios as $servicio )
	      {
	        echo "<td class=cellInfo ><a href=\"#\" onclick=\"ChangeDespacServicio(" . $servicio[0] . ")\">".$this -> getVehiculosMonitoreados( $_REQUEST['fec_inicio'], $_REQUEST['fec_finalx'], $servicio[0] )."</a></td>";
	      }
	      foreach( $servicios as $servicio )
	      {
	        echo "<td class=cellInfo >".$this -> getNovedades( $_REQUEST['fec_inicio'], $_REQUEST['fec_finalx'], $servicio[0] )."</td>";
	      }
	    }
		echo "</tr>";
		
		echo "</table>";
		
		$formulario->oculto("window", "central", 0);
		$formulario->oculto("cod_servic", $_REQUEST["cod_servic"], 0);
		$formulario->oculto("opcion", 1, 0);
		
		//---------------------------------------------
		$formulario->oculto("servicio", "", 0);
		$formulario->oculto("fec_inicio", $_REQUEST["fec_inicio"], 0);
		$formulario->oculto("fec_finalx", $_REQUEST["fec_finalx"], 0);
		//---------------------------------------------
		
		$formulario->cerrar();
	}
	
	function getRutasCreadas( $fec_inicio, $fec_finalx )
	{
		$sql = "SELECT COUNT( a.cod_rutasx ) AS num_rutasx
				    FROM ".BASE_DATOS.".tab_genera_rutasx a 
				    WHERE DATE( a.fec_creaci ) BETWEEN '".$fec_inicio."' AND '".$fec_finalx."' ";
				
		$consulta = new Consulta($sql, $this->conexion);
        $rutas = $consulta->ret_matriz('a');
		
		return number_format( $rutas[0]['num_rutasx'], 0 );
	}
	


	//-------------------------------------------------------
	//@funct.  :  getDataRutaxx()
	//@author  :  MIGUEL A. GARCIA [ MIK ]
	//@brief   :  METODO PARA OBTENER EL DATO DEL TIPO DE SERVICIO
	//-------------------------------------------------------
	function getDataServicio( $cod_tipser = NULL )
	{
    $sql = 	" SELECT 	a.nom_tipser
           		FROM  	".BASE_DATOS.".tab_genera_tipser a 
           		WHERE 	a.cod_tipser = '" . $cod_tipser . "'
            ";

		$consulta = new Consulta( $sql, $this -> conexion );
    $dataxx = $consulta -> ret_matriz('a');
		
		return $dataxx[0][0];
	}
	//-------------------------------------------------------

	//-------------------------------------------------------
	//@funct.  :  getDataRutaxx()
	//@author  :  MIGUEL A. GARCIA [ MIK ]
	//@brief   :  METODO PARA OBTENER EL DATO DE LA RUTA
	//-------------------------------------------------------
	function getDataRutaxx( $cod_rutasx = NULL )
	{
    $sql = 	" SELECT 	a.nom_rutasx
           		FROM  	".BASE_DATOS.".tab_genera_rutasx a 
           		WHERE 	a.cod_rutasx = '" . $cod_rutasx . "'
            ";

		$consulta = new Consulta( $sql, $this -> conexion );
    $dataxx = $consulta -> ret_matriz('a');
		
		return $dataxx[0][0];
	}
	//-------------------------------------------------------

	//-------------------------------------------------------
	//@funct.  :  getDataConduc()
	//@author  :  MIGUEL A. GARCIA [ MIK ]
	//@brief   :  METODO PARA OBTENER EL DATO DEL CONDUCTOR
	//-------------------------------------------------------
	function getDataConduc( $cod_tercer = NULL )
	{
    $sql = 	" SELECT 	a.abr_tercer
           		FROM  	".BASE_DATOS.".tab_tercer_tercer a 
           		WHERE 	a.cod_tercer = '" . $cod_tercer . "'
            ";

		$consulta = new Consulta( $sql, $this -> conexion );
    $dataxx = $consulta -> ret_matriz('a');
		
		return $dataxx[0][0];
	}
	//-------------------------------------------------------

	//-------------------------------------------------------
	//@funct.  :  getVehiculosMonitoreados()
	//@author  :  MIGUEL A. GARCIA [ MIK ]
	//@brief   :  METODO PARA OBTENER LOS DATOS DE LOS DESPACHOS
	//-------------------------------------------------------
	function getData_VehiculosMonitoreados( $fec_inicio, $fec_finalx, $cod_tipser = NULL, $cod_tercer = NULL )
	{
    $sql = " SELECT a.*
           FROM ".BASE_DATOS.".tab_despac_vehige a, 
                ".BASE_DATOS.".tab_transp_tipser b
           WHERE a.cod_transp = b.cod_transp AND
                 b.num_consec = ( SELECT MAX( z.num_consec ) 
                                  FROM ".BASE_DATOS.".tab_transp_tipser z
                                  WHERE z.cod_transp = b.cod_transp )";
    if( $cod_tercer != NULL )
      $sql .= " AND a.cod_transp =  '". $cod_tercer ."'";

    if( $cod_tipser != NULL )
      $sql .= " AND b.cod_tipser =  '". $cod_tipser ."'";

    $sql .= " AND DATE( a.fec_creaci ) BETWEEN  '". $fec_inicio ."' AND  '". $fec_finalx ."'";

		$consulta = new Consulta( $sql, $this -> conexion );
    $dataxx = $consulta -> ret_matriz('a');
		
		return $dataxx;
	}
	//-------------------------------------------------------
	
	//-------------------------------------------------------
	//@funct.  :  getTransp_VehiculosMonitoreados()
	//@author  :  MIGUEL A. GARCIA [ MIK ]
	//@brief   :  METODO PARA OBTENER LOS DATOS DE LAS TRANSPORTADORAS
	//-------------------------------------------------------
	function getTransp_VehiculosMonitoreados( $fec_inicio, $fec_finalx, $cod_tipser = NULL )
	{
	    $sql = " SELECT a.cod_transp, c.abr_tercer
	           FROM ".BASE_DATOS.".tab_despac_vehige a, 
	                ".BASE_DATOS.".tab_transp_tipser b,
	                ".BASE_DATOS.".tab_tercer_tercer c
	           WHERE a.cod_transp = b.cod_transp AND
	                 b.num_consec = ( SELECT MAX( z.num_consec ) 
	                                  FROM ".BASE_DATOS.".tab_transp_tipser z
	                                  WHERE z.cod_transp = b.cod_transp 
	                                )
							AND  a.cod_transp = c.cod_tercer 
							AND  b.ind_estado = 1

							";
	    if( $cod_tipser != NULL )
	      $sql .= " AND b.cod_tipser =  '". $cod_tipser ."'";

	    $sql .= " AND DATE( a.fec_creaci ) BETWEEN  '". $fec_inicio ."' AND  '". $fec_finalx ."'";
	    
	    $sql .= " GROUP BY 1";

			$consulta = new Consulta( $sql, $this -> conexion );
	    $dataxx = $consulta -> ret_matriz('a');
			
			return $dataxx;
	}
	//-------------------------------------------------------
	

	function getVehiculosMonitoreados( $fec_inicio, $fec_finalx, $cod_tipser=NULL )
	{
	    $sql = " SELECT COUNT(a.num_despac) AS num_despac
	           FROM ".BASE_DATOS.".tab_despac_vehige a, 
	                ".BASE_DATOS.".tab_transp_tipser b
	           WHERE a.cod_transp = b.cod_transp AND
	                 b.num_consec = ( SELECT MAX( z.num_consec ) 
	                                  FROM ".BASE_DATOS.".tab_transp_tipser z
	                                  WHERE z.cod_transp = b.cod_transp )";
	    if( $cod_tipser != NULL )
	      $sql .= " AND b.cod_tipser =  '". $cod_tipser ."'";

	    $sql .= " AND DATE( a.fec_creaci ) BETWEEN  '". $fec_inicio ."' AND  '". $fec_finalx ."'";

		$consulta = new Consulta($sql, $this->conexion);
	    $vehicu = $consulta->ret_matriz('a');
		
		return number_format( $vehicu[0]['num_despac'], 0 );
	}
	
	function getNovedades( $fec_inicio, $fec_finalx,$cod_tipser = NULL )
	{
    $suma = 0;
    /*$sql = "SELECT COUNT(*) FROM
           (
              SELECT num_despac, usr_creaci
              FROM ".BASE_DATOS.".tab_despac_contro
              WHERE DATE(fec_creaci) BETWEEN '". $fec_inicio ."' AND '". $fec_finalx ."' AND 
                    usr_creaci NOT LIKE '%Interf%'
            ) AS a,            
            ( 
              SELECT a.cod_transp 
              FROM ".BASE_DATOS.".tab_transp_tipser a 
              WHERE a.num_consec = ( SELECT MAX( z.num_consec ) 
                                              FROM ".BASE_DATOS.".tab_transp_tipser z
                                                WHERE z.cod_transp = a.cod_transp )";
    if( $cod_tipser != NULL )
     $sql .= " AND a.cod_tipser = '". $cod_tipser ."'";
    
    
    $sql .=" ) AS x,
            ".BASE_DATOS.".tab_despac_vehige b 
             
             WHERE  x.cod_transp = b.cod_transp AND
                    a.num_despac = b.num_despac";
    $sql .= " UNION ";
    
    $sql .= " SELECT COUNT(*) FROM
           (
              SELECT num_despac, usr_creaci
              FROM ".BASE_DATOS.".tab_despac_noveda
              WHERE DATE(fec_creaci) BETWEEN '". $fec_inicio ."' AND '". $fec_finalx ."' AND 
                    usr_creaci NOT LIKE '%Interf%'
            ) AS a,            
            ( 
              SELECT a.cod_transp 
              FROM ".BASE_DATOS.".tab_transp_tipser a 
              WHERE a.num_consec = ( SELECT MAX( z.num_consec ) 
                                              FROM ".BASE_DATOS.".tab_transp_tipser z
                                                WHERE z.cod_transp = a.cod_transp )";
    if( $cod_tipser != NULL )
     $sql .= " AND a.cod_tipser = '". $cod_tipser ."'";
   

    $sql .=" ) AS x,
            ".BASE_DATOS.".tab_despac_vehige b 
             
             WHERE  x.cod_transp = b.cod_transp AND
                    a.num_despac = b.num_despac";*/

    //echo "<br>-------> ".$sql;		
			// $sql .= " AND a.cod_tipser = '". $cod_tipser ."'";
 
		$sql = "(
				  SELECT
							COUNT(c.num_despac) AS num_despac
					FROM 
					(
						SELECT
								a.cod_transp, b.num_consec
						FROM
								".BASE_DATOS.".tab_transp_tipser a INNER JOIN 
								(
									SELECT 
											cod_transp, MAX(num_consec) AS num_consec
									  FROM 
											".BASE_DATOS.".tab_transp_tipser a  
									  WHERE 1 = 1
									  		".( $cod_tipser != NULL ? ' AND a.cod_tipser = "'.$cod_tipser.'" ' : ''  )."
									GROUP BY cod_transp
								) b ON a.cod_transp = b.cod_transp AND a.num_consec = b.num_consec
						WHERE
								a.ind_estado = 1
								".( $cod_tipser != NULL ? ' AND a.cod_tipser = "'.$cod_tipser.'" ' : ''  )."
					) a 
					INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.cod_transp = b.cod_transp
					INNER JOIN ".BASE_DATOS.".tab_despac_contro c ON b.num_despac = c.num_despac 
															 AND DATE(c.fec_creaci) BETWEEN '". $fec_inicio ."' AND '". $fec_finalx ."'
															 AND c.usr_creaci NOT LIKE '%Interf%'
					WHERE 1 = 1
				)
				UNION 
				(
				  SELECT
							COUNT(c.num_despac) AS num_despac
					FROM 
					(
						SELECT
								a.cod_transp, b.num_consec
						FROM
								".BASE_DATOS.".tab_transp_tipser a INNER JOIN 
								(
									SELECT 
											cod_transp, MAX(num_consec) AS num_consec
									FROM 
											".BASE_DATOS.".tab_transp_tipser a  
									WHERE
											1 = 1
											".( $cod_tipser != NULL ? ' AND a.cod_tipser = "'.$cod_tipser.'" ' : ''  )."
									GROUP BY cod_transp
								) b ON a.cod_transp = b.cod_transp AND a.num_consec = b.num_consec
						WHERE
								a.ind_estado = 1
								".( $cod_tipser != NULL ? ' AND a.cod_tipser = "'.$cod_tipser.'" ' : ''  )."
					) a 
					INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.cod_transp = b.cod_transp
					INNER JOIN ".BASE_DATOS.".tab_despac_noveda c ON b.num_despac = c.num_despac 
															 AND DATE(c.fec_creaci) BETWEEN '". $fec_inicio ."' AND '". $fec_finalx ."'
															 AND c.usr_creaci NOT LIKE '%Interf%'
					WHERE 1 = 1
				)";
		$consulta = new Consulta( $sql, $this->conexion);
    	$novedades = $consulta->ret_matriz('a');
		
	    /*echo "<pre>"; 
	    print_r($novedades);
	    echo "</pre>"; */
		$suma = $novedades[0][0] + $novedades[1][0];
		
		return number_format( $suma, 0 );		
	}

    function filtro()
    {
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/inf_nov_emp.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/regnov.js\"></script>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
        //Scrip del calendario
		echo '
		<script>
			jQuery(function($) 
			{             
				$( "#fec_inicio" ).datepicker();
				$( "#fec_finalx" ).datepicker();            
			});
		</script>';


        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", 
									 "Indicadores de Gestión", 
									 "formulario\" id=\"formularioID");

        $formulario->nueva_tabla();
        $formulario->texto(	"Fecha Inicial: ", 	"text", "fec_inicio\" id=\"fec_inicio", 0, 10, 10, "", date( "Y-m-d" ) );
        $formulario->texto(	"Fecha Final: ", 	"text", "fec_finalx\" id=\"fec_finalx", 1, 10, 10, "", date( "Y-m-d" ) );

        $formulario->nueva_tabla();
        $formulario->botoni("Buscar", "formulario.submit()", 0);
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_servic", $_REQUEST["cod_servic"], 0);
        $formulario->oculto("opcion\" id=\"opcionID", 1, 0);
        $formulario->cerrar();
    }
}

$service = new IndicadoresGestion($this->conexion);
?>