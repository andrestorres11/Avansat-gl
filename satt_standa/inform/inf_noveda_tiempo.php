<?php
/****************************************************************************
NOMBRE:   MODULO INFORME DE NOVEDADES DE LA TRANSPORTADORA
FUNCION:  INFORME DE NOVEDADES DE LA TRANSP
AUTOR: JORGE PRECIADO
FECHA CREACION : 17 FEBRERO 2012
****************************************************************************/
session_start();

class InformeNovedadesTiempos
{
	var  $conexion;
	
	function __construct( $conexion )
	{
		$this -> conexion = $conexion;
		
		ini_set("memory_limit", "128M");
		
		switch( $_POST[opcion] )
		{
			case  2:
				$this->MostrarResul();
			break;
			
			case "3":
				$this->infoNovEmp();
			break;
			
			default:
				$this->filtro();
			break;
		}
	}
	
	function filtro()
	{    
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_nov_emp.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/regnov.js\"></script>\n";
		echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
		
		
    echo '
      <script>
        jQuery(function($) 
		{ 
			$( "#fec_iniciaID,#fec_finalID" ).datepicker(); 
		});
      </script>';
	  
		//SQL Para el autocompletar
		$query = "SELECT a.cod_usuari, UPPER( a.cod_usuari ) AS nom_usuari
				  FROM ".BASE_DATOS.".tab_genera_usuari a
				  WHERE a.ind_estado = 1";
				  
		if( $_POST[cod_usuari] )
        $query .= " AND a.cod_usuari = '$_POST[cod_usuari]' ";
		
		$query .= " ORDER BY 2 ";

		$consulta = new Consulta( $query, $this -> conexion );
		//$usuarios = array_merge( array( array( "", "---" ) ), $consulta -> ret_matriz() );
		if( !$_POST[cod_usuari] )
		$usuarios = array_merge( array( array( "", "---" ) ), $consulta -> ret_matriz() );
	  else
		$usuarios = array_merge( $consulta -> ret_matriz(), array( array( "", "---" ) ) );
		
		//SQL Para el autocompletar
		$query = "SELECT a.cod_tercer, TRIM( UPPER( a.abr_tercer ) ) as abr_tercer
                  FROM ".BASE_DATOS.".tab_tercer_tercer a,
                       ".BASE_DATOS.".tab_tercer_activi b
                 WHERE a.cod_tercer = b.cod_tercer AND
                       b.cod_activi = ".COD_FILTRO_EMPTRA." ";
		
		if( $_POST[cod_transp] )
            $query .= " AND a.cod_tercer = '$_POST[cod_transp]' ";
		
        $query .= " ORDER BY 2";
		//echo "<pre>$query</pre>";die();
      $consulta = new Consulta( $query, $this -> conexion );
	  if( !$_POST[cod_transp] )
		$transpor = array_merge( array( array( "", "---" ) ), $consulta -> ret_matriz() );
	  else
		$transpor = array_merge( $consulta -> ret_matriz(), array( array( "", "---" ) ) );
		
		echo "	<style>
				.cellHead
				{
					padding:5px 10px;
					font-size:11px;
					background: -webkit-gradient(linear, left top, left bottom, from( #32984B ), to( #257038 )); 
					background: -moz-linear-gradient(top, #32984B, #257038 ); 
					filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#32984B', endColorstr='#257038');
					color:#fff;
					font-weight:bold;
					text-align:center;
					
				}
				.cellInfo
				{
					padding:5px 10px;
					font-size:11px;
					color:#257038;
					border:1px solid #ccc;
				}
				</style>";
				
		if( !$_POST[fec_inicia] ) $_POST[fec_inicia] = date( "Y-m-d" );
		if( !$_POST[fec_final] ) $_POST[fec_final] = date( "Y-m-d" );
	  
		$formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", 
										"Informe de Novedades por Tiempos", "formulario\" id=\"formularioID");

		$formulario -> nueva_tabla();
		$formulario -> texto ("Fecha Inicial","text","fec_inicia\" id=\"fec_iniciaID",0,7,7,"", $_POST[fec_inicia] );
		$formulario -> texto ("Fecha Final","text","fec_final\" id=\"fec_finalID",1,7,7,"",$_POST[fec_final] );
		$formulario -> lista ("Transportadora: ", "cod_transp", $transpor, 0 );
		$formulario -> lista ("Usuario: ", "cod_usuari", $usuarios, 1 );

		$formulario -> nueva_tabla();
		$formulario -> botoni( "Buscar", "formulario.submit()", 0 );

		/*- Nombre de Empresa 
		- Numero Despacho 
		- Minutos (Diferencia entre los minutos a los que se suponia debian llamarlo y la fecha real de la llamada) 
		- Fecha de la novedad tardía */
			
		if( $_POST[fec_inicia] && $_POST[fec_final] && $_POST[cod_transp] )
		{			
			$despachos = $this -> getDespachos();

			if( $despachos )
			{
				$a = 0;
				foreach( $despachos as $despacho )
				{	
					//echo "<pre><br>$despacho[4]</pre>";
					$novedades = $this -> getNovedades( $despacho[1] );

					if( $novedades )
					{	
						if($a == 0)
						{
							echo "<table border=0 width='100%'>";
							echo "<tr>";
							echo "<td class='cellHead' colspan=6 >Se Encontraron ".sizeof( $despachos )." Despachos</td>";
							echo "</tr>";	
							$a = 1;
						}
						echo "<td class='cellHead' colspan=6 style='text-align:left' ><b>Empresa:</b> $despacho[0]. <b>Nro. Despacho:</b> $despacho[1]. Tipo: $despacho[4]. </td>";
						echo "<tr>";
						echo "</tr>";
						$tie_lim = $despacho[3];
						echo "<tr>";
						echo "<td class='cellHead' >Puesto de Control</td>";
						echo "<td class='cellHead' >Novedad</td>";
						echo "<td class='cellHead' >Tiempo</td>";
						echo "<td class='cellHead' >Fecha Novedad</td>";
						echo "<td class='cellHead' >Más de $tie_lim Minutos</td>";
						echo "<td class='cellHead' >Usuario</td>";
						echo "</tr>";
						
						$fec_salida = $despacho[2];
						
						
						echo "<tr>";
						echo "<td class='cellInfo' ><b>FECHA SALIDA</b></td>";
						echo "<td class='cellInfo' >Salida</td>";
						echo "<td class='cellInfo' >N/A</td>";
						echo "<td class='cellInfo' >$fec_salida</td>";
						echo "<td class='cellInfo' align=right><b>0</b></td>";
						echo "<td class='cellInfo' align=right><b>---</b></td>";
						echo "</tr>";
						
						$fec_anteri = $fec_salida;
						$tiempo = $this -> getDiferencia( $novedades[0][4], $fec_anteri );
						
						$diferencia = $tiempo;
						
						if( $diferencia < 0 ) $diferencia = 0;
						
						$color = $this -> getColor( $diferencia );
						
						echo "<tr>";
						echo "<td class='cellInfo' ><b>".$novedades[0][0]."</b></td>";
						echo "<td class='cellInfo' >".$novedades[0][2]." - ".$novedades[0][8]."</td>";
						echo "<td class='cellInfo' >".$novedades[0][3]."</td>";
						echo "<td class='cellInfo' >".$novedades[0][4]."</td>";
						echo "<td class='cellInfo' align=right style='background-color:#$color' ><b>$diferencia</b> mins</td>";
						echo "<td class='cellInfo' align=right><b>".$novedades[0][7]."</b></td>";
						echo "</tr>";
						
						$tip_noveda = $this -> getTipoNovedad($novedades[0][2]);
						$ind_tiempoini = $tip_noveda[0][1];
						$ind_manalaini = $tip_noveda[0][2];
						
						if( $ind_manalaini == 0 )
						{
							$fec_anteri = $novedades[0][4];
						}
						$aux = 0;	
						for( $i = 1; $i < sizeof($novedades); $i++)
						{
							if( $ind_manalaini == 1 )
							{
								$tiempo = $this -> getDiferencia( $novedades[$i][4], $fec_anteri );
								
								$diferencia = $tiempo ;
							}
							else
							{
								$tiempo = $this -> getDiferencia( $novedades[$i][4], $fec_anteri );
								
								$diferencia = $tiempo - $tie_lim;
							}	
							
							if($aux == 1)
							{
								$diferencia = ( $diferencia + $tie_lim ) - $time;
							}
							
							if( $diferencia < 0 ) $diferencia = 0;
							
							$color = $this -> getColor( $diferencia );
							
							echo "<tr>";
							echo "<td class='cellInfo' ><b>".$novedades[$i][0]."</b></td>";
							echo "<td class='cellInfo' >".$novedades[$i][2]." - ".$novedades[$i][8]."</td>";
							echo "<td class='cellInfo' >".$novedades[$i][3]."</td>";
							echo "<td class='cellInfo' >".$novedades[$i][4]."</td>";
							echo "<td class='cellInfo' align=right style='background-color:#$color' ><b>$diferencia</b> mins</td>";
							echo "<td class='cellInfo' align=right><b>".$novedades[0][7]."</b></td>";
							echo "</tr>";
							
							$tip_noveda = $this -> getTipoNovedad($novedades[$i][2]);
							$ind_tiempo = $tip_noveda[0][1];
							$ind_manala = $tip_noveda[0][2];
							
							if($ind_tiempo == 1)
							{
								$aux = 1;
								$time = $novedades[$i][3];
							}
							else
							{
								$aux = 0;
							}
							
							if( $ind_manala == 0 )
							{
								$fec_anteri = $novedades[$i][4];
								$ind_manalaini = 0;
							}
						}
					}
					if($despacho[5]){
						echo "<tr>";
						echo "<td class='cellInfo' ><b>FECHA LLEGADA</b></td>";
						echo "<td class='cellInfo' >Llegada</td>";
						echo "<td class='cellInfo' >N/A</td>";
						echo "<td class='cellInfo' >$despacho[5]</td>";
						echo "<td class='cellInfo' align=right><b>0</b></td>";
						echo "<td class='cellInfo' align=right><b>$despacho[6]</b></td>";
						echo "</tr>";
					}
				}
				if( !$novedades )
				{
					echo "<tr>";
					echo "<td class='cellHead' colspan=6 >No se Encontraron Registros con los Criterios de Búsqueda</td>";
					echo "</tr>";
				}
				
			}
			else
			{
				echo "<tr>";
				echo "<td class='cellHead' colspan=6 >No se Encontraron Despachos Para la Transportadora Asignada</td>";
				echo "</tr>";
			}
			echo "</table>";
		}

		$formulario -> oculto("window","central",0);
		$formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
		$formulario -> oculto("opcion\" id=\"opcionID",1,0);
		$formulario -> cerrar();

	}
	
	function getColor( $tiempo )
	{
		$select = "SELECT MIN(a.cod_alarma),a.cod_colorx
				   FROM ".BASE_DATOS.".tab_genera_alarma a
				   WHERE a.cant_tiempo >= $tiempo";
						 
		
		$select = new Consulta( $select, $this -> conexion );
		$select = $select -> ret_matriz('i');	
		
		if( $tiempo > 120 )
			return "CC33FF";
			
		if( $tiempo <= 0 )
			return "FFFFFF";
		
		return $select[0][1];
	}
	
	function getDiferencia( $fechaa, $fechab )
	{
		$select = "SELECT time_to_sec( TIMEDIFF( '$fechaa', '$fechab' ) ) / 60 ";	
		$select = new Consulta( $select, $this -> conexion );
		$select = $select -> ret_matriz('i');		
		return (int)$select[0][0];
	}
	
	function getDespachos()
	{
		$select = "SELECT c.abr_tercer, a.num_despac, b.fec_salida, IF( b.cod_tipdes = '2', f.tie_contro, f.tie_conurb ) AS tie_contro, IF( b.cod_tipdes = '2', 'NACIONAL', 'URBANO' ) AS obs_contro, b.fec_llegad, UPPER(b.usr_modifi) as usr_llegad
				   FROM ".BASE_DATOS.".tab_despac_noveda a,
						".BASE_DATOS.".tab_despac_despac b,
						".BASE_DATOS.".tab_tercer_tercer c,
						".BASE_DATOS.".tab_despac_seguim d,
						".BASE_DATOS.".tab_despac_vehige e,
						".BASE_DATOS.".tab_transp_tipser f
				   WHERE a.num_despac = b.num_despac AND
						 e.cod_transp = c.cod_tercer AND
						 a.num_despac = d.num_despac AND
						 a.cod_contro = d.cod_contro AND
						 a.cod_rutasx = d.cod_rutasx AND
						 a.num_despac = e.num_despac AND
						 e.cod_transp = f.cod_transp AND
						 f.cod_tipser = 3 AND
						 DATE( b.fec_salida ) BETWEEN '$_POST[fec_inicia]' AND '$_POST[fec_final]' ";
						 
		if( $_POST[cod_transp] )
			$select .= " AND e.cod_transp = '$_POST[cod_transp]' "; 
	
		$select = new Consulta( $select, $this -> conexion );
		$select = $select -> ret_matriz('a');
		
		return $select;
	}
	
	function getNovedades( $num_despac )
	{
		
		
		$select = " SELECT b.nom_contro, a.obs_contro, a.cod_noveda, 
							a.tiem_duraci, a.fec_contro, a.cod_sitiox, 
							a.fec_creaci,UPPER(a.usr_creaci) as usr_creaci,
							UPPER(CONCAT(CONVERT(c.nom_noveda USING utf8),'',if(c.nov_especi='1','(NE)',''),if(c.ind_alarma='S','(GA)',''),if(c.ind_manala='1','(MA)',''),if(c.ind_tiempo='1','(ST)','') )) AS nom_noveda
					FROM    ".BASE_DATOS.".tab_despac_contro a,
						    ".BASE_DATOS.".tab_genera_contro b,
							".BASE_DATOS.".tab_genera_noveda c
					WHERE   a.cod_contro = b.cod_contro AND 
							a.cod_noveda = c.cod_noveda AND 
							a.num_despac = '$num_despac'";			 
		
		if( $_POST[cod_usuari] )
			$select .= " AND a.usr_creaci = '$_POST[cod_usuari]'  "; 
		
		$select .= " UNION ";
		
		$select .= " SELECT b.nom_contro, a.des_noveda, a.cod_noveda, 
							a.tiem_duraci, a.fec_noveda, a.cod_sitiox, 
							a.fec_creaci,UPPER(a.usr_creaci) as usr_creaci,
							UPPER(CONCAT(CONVERT(c.nom_noveda USING utf8),'',if(c.nov_especi='1','(NE)',''),if(c.ind_alarma='S','(GA)',''),if(c.ind_manala='1','(MA)',''),if(c.ind_tiempo='1','(ST)','') )) AS nom_noveda
					FROM    ".BASE_DATOS.".tab_despac_noveda a,
							".BASE_DATOS.".tab_genera_contro b,
							".BASE_DATOS.".tab_genera_noveda c
					WHERE   a.cod_contro = b.cod_contro AND 
							a.cod_noveda = c.cod_noveda AND 
							a.num_despac = '$num_despac' ";
						 
		if( $_POST[cod_usuari] )
			$select .= " AND a.usr_creaci = '$_POST[cod_usuari]'  ";
			
		$select .= " ORDER BY 5 ASC ";

		$select = new Consulta( $select, $this -> conexion );
		$select = $select -> ret_matriz('a');
		
		return $select;
	}
	
	function getTipoNovedad( $novedad ) 
	{
		$select = "SELECT cod_noveda, ind_tiempo, ind_manala 
				   FROM   ".BASE_DATOS.".tab_genera_noveda 
				   WHERE  cod_noveda = '$novedad'";
		
		$select = new Consulta( $select, $this -> conexion );
		$select = $select -> ret_matriz('a');
		
		return $select;
	}
}
$service= new  InformeNovedadesTiempos($_SESSION['conexion']);
?>