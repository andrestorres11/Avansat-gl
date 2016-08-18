<?php
class PorCumplir
{
    var $conexion;

    function __construct($conexion)
    {
    	$this->conexion = $conexion;
		
		switch($_POST[opcion])
		{
			case "1":
			{
				$this->listar();
			}
			break;
			case "2":
			{
				$this->imprimir();
			}
			break;
			case "3":
			{
				$this->eliminar();
			}
			break;
			default:
			{
				$this->filtro();
			}
			break;
		}
	}
	
	function filtro()
	{
		include( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );
		
		echo '<link rel="stylesheet" href="../satt_standa_15/estilos/dinamic_list.css" type="text/css">';
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/dinamic_list.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/concil.js\"></script>\n";
		
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "IMPRIMIR CUMPLIDOS", "formulario");
		
		$formulario -> nueva_tabla();
	    $formulario -> linea("Filtros:",1,"t2");
		
		$formulario -> nueva_tabla();
		$formulario -> texto ("Número de Transporte:","text","doc_despac",0,20,20,"",'',"","",NULL,0);
		$formulario -> texto ("Doc. Codigo:","text","cod_despac",1,20,20,"",'',"","",NULL,0);
		
		$formulario -> nueva_tabla();
	    $formulario -> linea("Seleccion Para el Rango de Fecha",1,"t2");
	
	    $feactual = date("Y-m-d");
		
		$formulario -> nueva_tabla();
		$formulario -> fecha_calendar("Fecha Inicial","fecini","formulario",'',"yyyy-mm-dd",0);
		$formulario -> fecha_calendar("Fecha Final","fecfin","formulario",'',"yyyy-mm-dd",1);
		
		$formulario -> nueva_tabla();
		$formulario -> boton("Aceptar","submit",0);
		
		$formulario -> nueva_tabla();
		$formulario -> oculto("num_despac",0,0);
  		$formulario -> oculto("window","central",0);
  		$formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
  		$formulario -> oculto("opcion",1,0);
  
		$formulario -> cerrar();
	}
	
	function listar()
	{	
			
		include( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );
		
		echo '<link rel="stylesheet" href="../'.DIR_APLICA_CENTRAL.'/estilos/dinamic_list.css" type="text/css">';
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/dinamic_list.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/concil.js\"></script>\n";
		
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "IMPRIMIR CUMPLIDOS", "formulario");
		
        $sql = "SELECT a.cod_manifi, d.nom_ciudad, 
					   e.nom_ciudad, a.fec_despac, UPPER(b.num_placax)  
				FROM ".BASE_DATOS.".tab_despac_despac a, 
					 ".BASE_DATOS.".tab_despac_vehige b, 
					 ".BASE_DATOS.".tab_tercer_tercer c,
					 ".BASE_DATOS.".tab_genera_ciudad d,
					 ".BASE_DATOS.".tab_genera_ciudad e, 
					 ".BASE_DATOS.".tab_tercer_tercer f
				WHERE a.num_despac = b.num_despac AND
					  b.cod_conduc = c.cod_tercer AND
					  a.cod_ciuori = d.cod_ciudad AND
					  a.cod_ciudes = e.cod_ciudad AND
					  b.cod_transp = f.cod_tercer AND
					  a.fec_llegad IS NOT NULL AND
					  a.fec_cumpli != '0000-00-00 00:00:00' ";
					  
		$origen = "SELECT d.cod_ciudad, d.nom_ciudad 
					FROM ".BASE_DATOS.".tab_despac_despac a, 
						 ".BASE_DATOS.".tab_despac_vehige b, 
						 ".BASE_DATOS.".tab_tercer_tercer c,
						 ".BASE_DATOS.".tab_genera_ciudad d     
					WHERE a.num_despac = b.num_despac AND
						  b.cod_conduc = c.cod_tercer AND
						  a.cod_ciuori = d.cod_ciudad AND
						  a.fec_llegad IS NOT NULL
					GROUP BY 1,2 ";
					
		$placa = "SELECT b.num_placax, UPPER(b.num_placax) 
					FROM ".BASE_DATOS.".tab_despac_despac a, 
						 ".BASE_DATOS.".tab_despac_vehige b 
					WHERE a.num_despac = b.num_despac AND
						a.fec_llegad IS NOT NULL
					GROUP BY 1,2 ";
					  
		if($_POST[doc_despac])
		{
			$sql.= " AND a.num_despac = '$_POST[doc_despac]'";
			$origen.= " AND a.num_despac = '$_POST[doc_despac]'";
			$placa.= " AND a.num_despac = '$_POST[doc_despac]'";
		}
		
		if($_POST[cod_despac])
		{
			$sql.= " AND a.cod_manifi = '$_POST[cod_despac]'";
			$origen.= " AND a.cod_manifi = '$_POST[cod_despac]'";
			$placa.= " AND a.cod_manifi = '$_POST[cod_despac]'";
		}
		
		$fechaini = $GLOBALS[fecini]." 00:00:00";
		$fechafin = $GLOBALS[fecfin]." 23:59:59";
		
		if($_POST[fecini] && $_POST[fecfin])
		{
			$sql .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";
			$origen .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";
			$placa .= " AND a.fec_despac BETWEEN '".$fechaini."' AND '".$fechafin."'";
		}	
					  
		$origen = new Consulta($origen, $this -> conexion);
   		$origen = array_merge( array(array("","--")), $origen -> ret_matriz() );		
					  
		$placa = new Consulta($placa, $this -> conexion);
   		$placa = array_merge( array(array("","--")), $placa -> ret_matriz() );
		
		$sql .=" GROUP BY 1,2 ";
		
        $list = new DinamicList($this->conexion, $sql, 1 );
        $list->SetClose('no');
		
        $list->SetHeader("Número de Transporte", "field:a.cod_manifi; type:link; onclick:sendDespacho()");
        $list->SetHeader("Origen", "field:d.cod_ciudad",$origen);
        $list->SetHeader("Destino", "field:e.nom_ciudad" );
		$list->SetHeader("Fecha de Creacion", "field:a.fec_creaci");
		$list->SetHeader("Placa", "field:b.num_placax",$placa);

        $list->Display($this->conexion);

        $_SESSION["DINAMIC_LIST"] = $list;
		echo "<td>";
        echo $list->GetHtml();
		echo "</td>";
		
		$formulario -> nueva_tabla();
		$formulario -> oculto("num_despac",0,0);
  		$formulario -> oculto("window","central",0);
  		$formulario -> oculto("cod_servic",$GLOBALS["cod_servic"],0);
  		$formulario -> oculto("opcion",2,0);
  
		$formulario -> cerrar();
    }

	function Imprimir()
	{		
		$sql = "SELECT  UPPER(f.abr_tercer) as transport, UPPER(c.abr_tercer) as conductor,
						c.cod_tercer as ced_conduc, c.num_telmov, a.num_despac, a.cod_manifi, 
						UPPER(b.num_placax) as num_placax, d.nom_ciudad as origen , a.obs_despac, 
					   e.nom_ciudad, a.fec_creaci,a.val_pesoxx,a.fec_llegad   
				FROM ".BASE_DATOS.".tab_despac_despac a, 
					 ".BASE_DATOS.".tab_despac_vehige b, 
					 ".BASE_DATOS.".tab_tercer_tercer c,
					 ".BASE_DATOS.".tab_genera_ciudad d,
					 ".BASE_DATOS.".tab_genera_ciudad e, 
					 ".BASE_DATOS.".tab_tercer_tercer f 
				WHERE a.num_despac = b.num_despac AND
					  b.cod_conduc = c.cod_tercer AND
					  a.cod_ciuori = d.cod_ciudad AND
					  a.cod_ciudes = e.cod_ciudad AND
					  b.cod_transp = f.cod_tercer AND
					  a.fec_llegad IS NOT NULL AND 
					  a.cod_manifi = '$_POST[num_despac]'";
					  
		$despac = new Consulta($sql, $this -> conexion);
   		$despac = $despac -> ret_matriz(1);		
		$despac = $despac[0];
		
		$query = "SELECT a.num_placax,g.abr_tercer,g.num_telef1,h.abr_tercer,
		      		h.num_telmov,b.nom_marcax,c.nom_lineax,d.nom_colorx,
                    e.nom_carroc,a.ano_modelo,a.ind_estado,a.fec_creaci,
                    j.cod_transp 
               FROM ".BASE_DATOS.".tab_vehicu_vehicu a,
		      		".BASE_DATOS.".tab_genera_marcas b,
                    ".BASE_DATOS.".tab_vehige_carroc e,
		      		".BASE_DATOS.".tab_vehige_colore d,
		      		".BASE_DATOS.".tab_vehige_lineas c,
		      		".BASE_DATOS.".tab_vehige_config i,
                    ".BASE_DATOS.".tab_tercer_tercer g,
                    ".BASE_DATOS.".tab_tercer_tercer h,
                    ".BASE_DATOS.".tab_transp_vehicu j
           	  WHERE a.cod_marcax = b.cod_marcax AND
		      		a.num_config = i.num_config AND
		      	    a.cod_marcax = c.cod_marcax AND
		      		a.cod_lineax = c.cod_lineax AND
		      		a.cod_colorx = d.cod_colorx AND
                    a.cod_carroc = e.cod_carroc AND
                    a.cod_tenedo = g.cod_tercer AND					
                    a.cod_conduc = h.cod_tercer AND
                    a.num_placax = j.num_placax AND
                    a.num_placax = '".$despac[num_placax]."'";//
		
						 						 
		$vehicu = new Consulta($query, $this -> conexion);
   		$vehicu = $vehicu -> ret_matriz(1);
		$vehicu = $vehicu[0];
		
		$cumpli = "SELECT d.num_remdes, b.num_docume, g.nom_remdes, 
						  b.val_pesoxx, '', c.nom_ciudad, 
						  a.num_despac, e.obs_noveda, a.fec_despac 
						    
				   FROM	".BASE_DATOS.".tab_despac_despac a,
				   		".BASE_DATOS.".tab_despac_remdes b,
						".BASE_DATOS.".tab_genera_ciudad c,
						".BASE_DATOS.".tab_genera_remdes d,
						".BASE_DATOS.".tab_noveda_cumpli e,
						".BASE_DATOS.".tab_genera_novcum f,
						".BASE_DATOS.".tab_genera_remdes g 
						 
				   WHERE a.num_despac = b.num_despac AND
				   		 a.cod_manifi = '$_POST[num_despac]' AND
						 b.cod_ciudad = c.cod_ciudad AND
						 b.cod_remdes = d.cod_remdes AND
						 e.num_despac = a.num_despac AND
						 e.cod_manifi = a.cod_manifi AND
						 e.cod_remdes = b.cod_remdes AND
						 e.cod_novcum = f.cod_novcum AND
						 b.cod_remdes = g.cod_remdes
				   GROUP BY 1 LIMIT 7";
					
		$cumpli = new Consulta($cumpli, $this -> conexion);
   		$remision = $cumpli -> ret_matriz(2);
   		
   		for($i = 0; $i < sizeof($remision); $i++)
   		{
   			$sql = "SELECT f.cod_novcum, f.nom_novcum
											  	   
				   		 FROM ".BASE_DATOS.".tab_despac_despac a,
				   			  ".BASE_DATOS.".tab_despac_remdes b,
							  ".BASE_DATOS.".tab_genera_ciudad c,
							  ".BASE_DATOS.".tab_genera_remdes d,
							  ".BASE_DATOS.".tab_noveda_cumpli e,
							  ".BASE_DATOS.".tab_genera_novcum f  
						 
				   	    WHERE a.num_despac = b.num_despac AND
				   		 	  a.cod_manifi = '$_POST[num_despac]' AND
						 	  b.cod_ciudad = c.cod_ciudad AND
						 	  b.cod_remdes = d.cod_remdes AND
						 	  e.num_despac = a.num_despac AND
						 	  e.cod_manifi = a.cod_manifi AND
						 	  e.cod_remdes = b.cod_remdes AND
						 	  e.cod_novcum = f.cod_novcum AND
						 	  d.num_remdes = ".$remision[$i][0]."
					 GROUP BY 1";
						 						 
			$consulta = new Consulta($sql, $this -> conexion);
   			$noveda = $consulta -> ret_matriz();
   			
   			for($j = 0; $j < sizeof($noveda); $j++)
   				$remision[$i][4] .= $noveda[$j][1].', ';
   		}
   		
		$remision1 = $remision[0];
		$remision2 = $remision[1];
		$remision3 = $remision[2];
		$remision4 = $remision[3];
		$remision5 = $remision[4];
		$remision6 = $remision[5];
		$remision7 = $remision[6];
		
		
		$fecha = date("Y-m-d");
		$origen = $despac[origen];
		$usuario = $_SESSION["datos_usuario"]["cod_usuari"];
		
		$d1 = $despac[transport];
		$d2 = $despac[conductor];
		$d3 = $despac[ced_conduc];
		$d4 = $despac[num_telmov];
		$d5 = $despac[num_placax];
		$d6 = $vehicu[nom_marcax];
		$d7 = $vehicu[ano_modelo];
		
		$d9 = $despac[cod_manifi];
		$d10 = $despac[val_pesoxx];
		$d11 = $despac[obs_despac];
		
		// LLAMADO AL ARCHIVO HTML DEL FORMATO DE CODNUCTORES
		$tmpl_file = "../".DIR_APLICA_CENTRAL."/cumpli/format.html";
		$thefile = implode("", file($tmpl_file));
		$thefile = addslashes($thefile);
		$thefile = "\$r_file=\"".$thefile."\";";
		eval($thefile);
		
		print $r_file;
		
		echo "<form name=\"form\" method=\"post\" action=\"index.php\">";
		echo "<br><br>"
				."<table border=\"0\" width=\"100%\">"
				."<tr>"
				."<td align=\"center\">"
				."<input type=\"hidden\" name=\"cod_servic\" value=\"$GLOBALS[cod_servic]\">"
				."<input type=\"hidden\" name=\"window\" value=\"central\">"
				."<input type=\"button\" onClick=\"form.Imprimir.style.visibility='hidden';form.Volver.style.visibility='hidden';print();form.Imprimir.style.visibility='visible';form.Volver.style.visibility='visible';\" name=\"Imprimir\" value=\"Imprimir\">"
				."</td>"
				."<td align=\"center\">"
				."<input type=\"reset\" name=\"Volver\" value=\"Volver\" onClick=\"javascript:history.go(-1);\">"
				."</td>"
				."</tr>"
				."</table>";
		echo "</form>";
}
	
}
$service = new PorCumplir($this->conexion);
?>
