<?php

class Informe
{
	var $conexion;
	
	function __construct( $conexion )
	{
		$this -> conexion = $conexion;
		
		switch( $_REQUEST[option] )
		{
			case "xls":
				$this -> XLS();
			break;
			default:
				$this -> Listar();
			break;
		}
	}
	
	function XLS()
	{
		session_start();
		ini_set('memory_limit', '128M');
		$archivo = "Informe".date("Y_m_d").".xls";
		header('Content-Type: application/octetstream');
		header('Expires: 0');
		header('Content-Disposition: attachment; filename="'.$archivo.'"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

		ob_start("ob_gzhandler");
		echo $HTML = $_SESSION[html_info];
		ob_end_flush();
	}
	
	function Listar()
	{
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
		
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
		
		echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
		
		echo '
		<style>
			.cellHead
			{
				padding:3px 10px;
				border-top:1px solid #FFF;
				border-left:1px solid #FFF;
				border-right:1px solid #DDD;
				border-bottom:1px solid #DDD;
			}
			
			.cellInfo
			{
				padding:3px 10px;
				border-top:1px solid #FFF;
				border-left:1px solid #FFF;
				border-right:1px solid #DDD;
				border-bottom:1px solid #DDD;
			}
		</style>
		<script>
		$(function() 
		{
				
			
			$.mask.definitions["A"]="[12]";
			$.mask.definitions["M"]="[01]";
		    $.mask.definitions["D"]="[0123]";
			
			$.mask.definitions["H"]="[012]";
		    $.mask.definitions["N"]="[012345]";
		    $.mask.definitions["n"]="[0123456789]";
			
			$( "#fec_incial" ).mask("Annn-Mn-Dn");
			$( "#fec_incial" ).datepicker();
			
			$( "#fec_finali" ).mask("Annn-Mn-Dn");
			$( "#fec_finali" ).datepicker();
			
		});
		</script>';
		
		$formulario = new Formulario ( "index.php", "post", "Informe de Reportes.", "formulario" );
		/*
		$formulario -> oculto( "window", "central" );
		$formulario -> oculto( "opcion", 2 );
		$formulario -> oculto( "cod_servic", $_REQUEST[cod_servic] );		
		*/
		$formulario -> oculto( "window", "central", 0 );
		$formulario -> oculto( "opcion", 2, 0 );
		$formulario -> oculto( "cod_servic", $_REQUEST[cod_servic], 0 );		
		$formulario -> linea( "-Filtros", 1, "t2" );//Subtitulo.		
		$formulario -> nueva_tabla();
		
		$transp = $this -> getTransports();
		
		$formulario -> lista ( "Transportadora:", "cod_transp", $transp, 1, 1 );
		$formulario -> nueva_tabla();
		$formulario -> texto ( "Fecha Inicio:", "text", "fec_incial\" id=\"fec_incial", 0, 10, 10, "", $_POST[fec_incial], "", "", NULL, 1 );
		$formulario -> texto ( "Fecha Final:", "text", "fec_finali\" id=\"fec_finali", 0, 10, 10, "", $_POST[fec_finali], "", "", NULL, 1 );	

		$formulario -> nueva_tabla();
		$formulario -> boton( "Aceptar", "button\" onClick=\"formulario.submit()", 0 );		
		
		$num = 0;
		
		if( $_POST[cod_transp] || ( $_POST[fec_incial] && $_POST[fec_finali] ) )
			$reportes = $this -> getReport();
		
		$num = sizeof( $reportes );
			
		$formulario -> nueva_tabla();
		$formulario -> linea( "- Informe :: Se encontraron $num reportes.", 1, "t2" );//Subtitulo.		
		
		
		
		echo "</table>";
		
		echo "<div align='center' style='padding:5px;'  >";
		echo "<b><a href='../".DIR_APLICA_CENTRAL."/report/inf_report_report.php?option=xls' >[ Excel ]</a></b>";
		echo "</div>";
		
		$html .= "<table cellpadding='0' cellspacing='0' border='1' width='100%' >";
		
		if( $reportes )
		{
			$html .= "<tr>";
			$html .= "<th class='celda_titulo2 cellHead' >Codigo.</th>";
			$html .= "<th class='celda_titulo2 cellHead' >Transportadora.</th>";
			$html .= "<th class='celda_titulo2 cellHead' >Puesto de Control.</th>";
			$html .= "<th class='celda_titulo2 cellHead' >Placa.</th>";
			$html .= "<th class='celda_titulo2 cellHead' >Fecha Reporte.</th>";
			$html .= "<th class='celda_titulo2 cellHead' >Novedad.</th>";
			$html .= "<th class='celda_titulo2 cellHead' >Observacion.</th>";
			$html .= "<th class='celda_titulo2 cellHead' >Usuario.</th>";
			$html .= "<th class='celda_titulo2 cellHead' >Fecha.</th>";
			$html .= "</tr>";
			
			foreach( $reportes  as $row )
			{
				$html .= "<tr>";
				$html .= "<th class='cellInfo' >$row[num_repnov]</td>";
				$html .= "<td class='cellInfo' >$row[abr_tercer]</td>";
				$html .= "<td class='cellInfo' >$row[nom_contro]</td>";
				$html .= "<td class='cellInfo' >$row[num_placax]</td>";
				$html .= "<td class='cellInfo' >$row[fec_repnov]</td>";
				$html .= "<td class='cellInfo' >$row[nom_noveda]</td>";
				$html .= "<td class='cellInfo' >$row[obs_repnov]</td>";
				$html .= "<td class='cellInfo' >$row[usr_creaci]</td>";
				$html .= "<td class='cellInfo' >$row[fec_creaci]</td>";
				$html .= "</tr>";
			}
		}
		
		$html .= "</table>";
		
		$_SESSION[html_info] = $html;
		
		echo $html;
		
		$formulario -> cerrar();
	}
	
	function getTransports( $datos_usuario = NULL)
	{
              
		$query = "SELECT a.cod_tercer, UPPER( TRIM( b.abr_tercer ) )
				  FROM ".BASE_DATOS.".tab_report_noveda a,
					   ".BASE_DATOS.".tab_tercer_tercer b
				  WHERE a.cod_tercer = b.cod_tercer ";
		if( strtolower( $_SESSION["datos_usuario"]["cod_usuari"] ) == 'alogistica' )
              {
                $query .= " AND b.cod_tercer IN ( SELECT cod_tercer FROM ".BASE_DATOS.".tab_config_alianz ) ";
              }
		$query .= " GROUP BY a.cod_tercer ORDER BY 2 ";
		
		$consult = new Consulta( $query, $this -> conexion );      
		$report = array_merge( array( array( "", "---" ) ), $consult -> ret_matriz( 'i' ) );
		
		return $report;
	}
	
	function getReport()
	{
		$query = "SELECT a.num_repnov, UPPER( b.abr_tercer ) as abr_tercer, c.nom_contro, 
						 a.num_placax, a.fec_repnov, d.nom_noveda,a.obs_repnov,a.usr_creaci,
						 a.fec_creaci
				  FROM ".BASE_DATOS.".tab_report_noveda a,
					   ".BASE_DATOS.".tab_tercer_tercer b,
					   ".BASE_DATOS.".tab_genera_contro c,
					   ".BASE_DATOS.".tab_genera_noveda d
				  WHERE a.cod_tercer = b.cod_tercer AND
						a.cod_contro = c.cod_contro AND
						a.cod_noveda = d.cod_noveda ";
				  
		if( $_POST[cod_transp] )
			$query .= " AND a.cod_tercer = '$_POST[cod_transp]' ";
		
		if( $_POST[fec_incial] && $_POST[fec_finali]  )
			$query .= " AND a.fec_creaci BETWEEN '$_POST[fec_incial] 00:00:00' AND '$_POST[fec_finali] 23:59:59' ";
		
              if( strtolower( $_SESSION["datos_usuario"]["cod_usuari"] ) == 'alogistica' )
              {
                $query .= " AND a.cod_tercer IN ( SELECT cod_tercer FROM ".BASE_DATOS.".tab_config_alianz ) ";
              }


		$query .= " ORDER BY 1 ";
		
		$consult = new Consulta( $query, $this -> conexion );      
		$report = $consult -> ret_matriz( 'a' );
		
		return $report;
	}
}

$pagina = new Informe( $this -> conexion );

?>