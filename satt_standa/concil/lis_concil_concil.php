<?php
class PorConciliar
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
				$this->accion();
			}
			break;
			case "3":
			{
				$this->registrar();
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
		
		echo "<link rel=\"stylesheet\" href=\"../".DIR_APLICA_CENTRAL."/estilos/dinamic_list.css\" type=\"text/css\">";
		//echo '<link rel="stylesheet" href="../satt_standa_15/estilos/dinamic_list.css" type="text/css">';
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/dinamic_list.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/concil.js\"></script>\n";
		
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "LISTAR CONCILIACIONES", "formulario");
		
		$formulario -> nueva_tabla();
	    $formulario -> linea("Filtros:",1,"t2");
		
		$formulario -> nueva_tabla();
		$formulario -> texto ("Número de Transporte:","text","doc_despac",0,20,20,"",'',"","",NULL,0);
		$formulario -> texto ("Doc. Codigo:","text","cod_despac",1,20,20,"",'',"","",NULL,0);
		$formulario -> radio("Conciliadas","fil",1,0,0);
	    $formulario -> radio("Por Conciliar","fil",2,0,1);
	    $formulario -> radio("Todos","fil",3,1,0);
		
		$formulario -> nueva_tabla();
	    $formulario -> linea("Selecci&oacute;n Para el Rango de Fecha",1,"t2");
	
	    $feactual = date("Y-m-d");
		
		$formulario -> nueva_tabla();
		$formulario -> fecha_calendar("Fecha Inicial","fecini","formulario",'',"yyyy-mm-dd",0);
		$formulario -> fecha_calendar("Fecha Final","fecfin","formulario",'',"yyyy-mm-dd",1);
		
		$formulario -> nueva_tabla();
		$formulario -> boton("Aceptar","submit",0);
		
		$formulario -> nueva_tabla();
		$formulario -> oculto("num_despac",0,0);
  		$formulario -> oculto("window","central",0);
  		$formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
  		$formulario -> oculto("opcion",1,0);
  
		$formulario -> cerrar();
	}
	
	function listar()
	{
		include( "../".DIR_APLICA_CENTRAL."/lib/general/dinamic_list.inc" );
		
		echo "<link rel=\"stylesheet\" href=\"../".DIR_APLICA_CENTRAL."/estilos/dinamic_list.css\" type=\"text/css\">";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/dinamic_list.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/concil.js\"></script>\n";
		
        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "LISTAR CONCILIACIONES", "formulario");
		
        $sql = 
            /* "SELECT a.cod_manifi,  UPPER(b.num_placax), d.nom_ciudad, 
                    e.nom_ciudad, UPPER(f.abr_tercer), 
                    if(a.fec_cumpli = '0000-00-00 00:00:00','Sin Cumplir',a.fec_cumpli), 
                    if(a.fec_concil = '0000-00-00 00:00:00','Sin Conciliar',a.fec_concil) AS fec_concil, 
                    a.fec_llegad, UPPER(c.abr_tercer), c.num_telmov 
               FROM ".BASE_DATOS.".tab_despac_despac a, 
                    ".BASE_DATOS.".tab_despac_vehige b, 
                    ".BASE_DATOS.".tab_tercer_tercer c, 
                    ".BASE_DATOS.".tab_genera_ciudad d, 
                    ".BASE_DATOS.".tab_genera_ciudad e, 
                    ".BASE_DATOS.".tab_tercer_tercer f, 
                    ".BASE_DATOS.".tab_despac_remdes g 
              WHERE a.num_despac = b.num_despac 
                AND b.cod_conduc = c.cod_tercer 
                AND a.cod_ciuori = d.cod_ciudad 
                AND a.cod_ciudes = e.cod_ciudad 
                AND b.cod_transp = f.cod_tercer 
                AND a.num_despac = g.num_despac
                AND a.fec_cumpli != '0000-00-00 00:00:00'          
                AND a.fec_llegad IS NOT NULL";   */ 
                
                /**/
                "SELECT a.cod_manifi, h.num_remdes, ". 
                       "UPPER(b.num_placax), d.nom_ciudad, e.nom_ciudad, ". 
                       "UPPER(f.abr_tercer), ". 
                       "if(a.fec_cumpli = '0000-00-00 00:00:00','Sin Cumplir',a.fec_cumpli), ". 
                       "if(a.fec_concil = '0000-00-00 00:00:00','Sin Conciliar',a.fec_concil) AS fec_concil, ".
                       "a.fec_llegad, UPPER(c.abr_tercer), c.num_telmov  ".
                  "FROM ".BASE_DATOS.".tab_despac_despac a, ". 
                       "".BASE_DATOS.".tab_despac_vehige b, ". 
                       "".BASE_DATOS.".tab_tercer_tercer c, ". 
                       "".BASE_DATOS.".tab_genera_ciudad d, ". 
                       "".BASE_DATOS.".tab_genera_ciudad e, ". 
                       "".BASE_DATOS.".tab_tercer_tercer f, ". 
                       "".BASE_DATOS.".tab_despac_remdes g , ". 
                       "".BASE_DATOS.".tab_genera_remdes h ". 
                 "WHERE a.num_despac = b.num_despac ". 
                   "AND b.cod_conduc = c.cod_tercer ". 
                   "AND a.cod_ciuori = d.cod_ciudad ". 
                   "AND a.cod_ciudes = e.cod_ciudad ". 
                   "AND b.cod_transp = f.cod_tercer ". 
                   "AND a.num_despac = g.num_despac ". 
                   "AND g.cod_remdes = h.cod_remdes ". 
                   "AND a.fec_cumpli != '0000-00-00 00:00:00' ". 
                   "AND a.fec_llegad IS NOT NULL  ";
                
                /**/      
					  
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
			$sql.= " AND a.cod_manifi  = '$_POST[doc_despac]'";
			$origen.= " AND a.cod_manifi  = '$_POST[doc_despac]'";
			$placa.= " AND a.cod_manifi  = '$_POST[doc_despac]'";
		}
		
		if($_POST[cod_despac])
		{
			 $sql.= " AND h.num_remdes = '$_POST[cod_despac]'";
			 //$origen.= " AND g.num_remdes = '$_POST[cod_despac]'";
			 //$placa.= " AND g.num_remdes = '$_POST[cod_despac]'";
		}
		if($_POST[fil]== '1')
		{
			$sql.= " AND a.fec_concil IS NOT NULL";
			$origen.= " AND a.fec_concil IS NOT NULL";
			$placa.= " AND a.fec_concil IS NOT NULL";
		}
		if($_POST[fil]== '2')
		{
			$sql.= " AND a.fec_concil IS NULL";
			$origen.= " AND a.fec_concil IS NULL";
			$placa.= " AND a.fec_concil IS NULL";
		}
		
		$fechaini = $_REQUEST[fecini]." 00:00:00";
		$fechafin = $_REQUEST[fecfin]." 23:59:59";
		
		if($_POST[fecini] && $_POST[fecfin])
		{
			$sql .= " AND a.fec_concil BETWEEN '".$fechaini."' AND '".$fechafin."'";
			$origen .= " AND a.fec_concil BETWEEN '".$fechaini."' AND '".$fechafin."'";
			$placa .= " AND a.fec_concil BETWEEN '".$fechaini."' AND '".$fechafin."'";
		}	
		
		$sql .=" GROUP BY 1,2 ";
					  
		$origen = new Consulta($origen, $this -> conexion);
   		$origen = array_merge( array(array("","--")), $origen -> ret_matriz() );		
					  
		$placa = new Consulta($placa, $this -> conexion);
   		$placa = array_merge( array(array("","--")), $placa -> ret_matriz() );

        $list = new DinamicList($this->conexion, $sql, 1 );
        $list->SetClose('no');
		
		/*a.cod_manifi, UPPER(b.num_placax), d.nom_ciudad, 
					   e.nom_ciudad, UPPER(f.abr_tercer), a.fec_despac, 
					   a.fec_llegad, UPPER(c.abr_tercer), c.num_telmov*/
		
        $list->SetHeader("Número de Transporte", "field:a.cod_manifi; type:link; onclick:sendDespacho()");
        $list->SetHeader("Doc.Codigo", "field:h.num_remdes");
        $list->SetHeader("Placa", "field:b.num_placax",$placa);
        $list->SetHeader("Origen", "field:d.cod_ciudad",$origen);
        $list->SetHeader("Destino", "field:e.nom_ciudad" );
        $list->SetHeader("Transportadora", "field:f.abr_tercer");
        $list->SetHeader("Fecha de Cumplido", "field:a.fec_cumpli");
        $list->SetHeader("Fecha de Conciliación", "field:a.fec_concil");
        $list->SetHeader("Fecha de Llegada", "field:a.fec_llegad");
        $list->SetHeader("Conductor", "field:c.abr_tercer");
        $list->SetHeader("Celular", "field:c.num_telmov");
        $list->Display($this->conexion);

        $_SESSION["DINAMIC_LIST"] = $list;
        echo "<td>";
        echo $list->GetHtml();
        echo "</td>";
		
		$formulario -> nueva_tabla();
		$formulario -> oculto("num_despac",0,0);
  		$formulario -> oculto("window","central",0);
  		$formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
  		$formulario -> oculto("opcion",2,0);
  
		$formulario -> cerrar();
    }

	function accion()
	{
	 echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/concil.js\"></script>\n";	
	
		$sql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax), d.nom_ciudad, 
					   e.nom_ciudad, UPPER(f.abr_tercer) as transport, a.fec_creaci, 
					   a.fec_llegad, UPPER(c.abr_tercer) as conductor, c.num_telmov, 
					   a.fot_cumpli AS foto
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

		//echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/concil.js\"></script>\n";
		
	
    $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "CONCILIACIONES", "formulario");		
		
     $cumpli = "SELECT d.num_remdes, b.num_docume, b.cod_remdes, 
                      a.fec_despac, b.val_pesoxx, c.nom_ciudad, 
                      a.num_despac, e.obs_noveda
                 FROM ".BASE_DATOS.".tab_despac_despac a,
                      ".BASE_DATOS.".tab_despac_remdes b,
                      ".BASE_DATOS.".tab_genera_ciudad c,
                      ".BASE_DATOS.".tab_genera_remdes d,
                      ".BASE_DATOS.".tab_noveda_concil e  
                WHERE a.num_despac = b.num_despac 
                  AND a.cod_manifi = '$_POST[num_despac]' 
                  AND b.cod_ciudad = c.cod_ciudad 
                  AND b.cod_remdes = d.cod_remdes 
                  AND e.num_despac = a.num_despac 
                  AND e.cod_manifi = a.cod_manifi 
                  AND e.cod_remdes = b.cod_remdes
                GROUP BY 1";
						 						 
		$cumpli = new Consulta($cumpli, $this -> conexion);
   		$cumpli = $cumpli -> ret_matriz(1);
		
		if(!$cumpli)
		{
			$link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Listar otros cumplidos</a></b>";
			$mensaje = "<font color='red' size='2'>El Despacho $_POST[num_despac] no tiene Cumplidos.</font>";
           	echo "<div align='center'><img src=\"../satt_standa/imagenes/error.gif\"><br><b>$mensaje</b><hr>$link_a</div>";
			die();
		}
		
		$formulario -> nueva_tabla();
  		$formulario -> linea("Datos Basicos. ",1,"t2");
		
		$formulario -> nueva_tabla();
		$formulario -> linea("Número de Transporte:",0,"t","15%");
		$formulario -> linea($despac[cod_manifi],0,"i","15%");
		$formulario -> linea("Conductor:",0,"t","15%");
		$formulario -> linea($despac[conductor],0,"i","15%");
		$formulario -> linea("Transportadora:",0,"t","15%");
		$formulario -> linea($despac[transport],0,"i","15%");
		$formulario -> oculto("num_despac",$despac[num_despac],0);
		$formulario -> oculto("cod_manifi",$despac[cod_manifi],0);
		
		$formulario -> nueva_tabla();
  		$formulario -> linea("Detalle Cumplidos. ",1,"t2");
		
		$formulario -> nueva_tabla();
		$formulario -> linea("Conciliación",0,"t","5%");
		$formulario -> linea("Documento Código",0,"t","10%");
		$formulario -> linea("Remision",0,"t","10%");
		$formulario -> linea("Fecha creación despacho",0,"t","15%");
		$formulario -> linea("Peso",0,"t","10%");
		$formulario -> linea("Destino",0,"t","10%");
		$formulario -> linea("Novedad",0,"t","15%");
		$formulario -> linea("Observacion",1,"t","25%");		
		
		$i = 1;
		
		$formulario -> oculto("siz_cumpli",sizeof($cumpli),0);
		
		foreach( $cumpli as $row )
		{
		    $sql = "SELECT f.cod_novcum, f.nom_novcum
											  	   
				   		 FROM ".BASE_DATOS.".tab_despac_despac a,
				   			  ".BASE_DATOS.".tab_despac_remdes b,
							  ".BASE_DATOS.".tab_genera_ciudad c,
							  ".BASE_DATOS.".tab_genera_remdes d,
							  ".BASE_DATOS.".tab_noveda_concil e,
							  ".BASE_DATOS.".tab_genera_novcum f  
						 
				   	    WHERE a.num_despac = b.num_despac AND
				   		 	  a.cod_manifi = '$_POST[num_despac]' AND
						 	  b.cod_ciudad = c.cod_ciudad AND
						 	  b.cod_remdes = d.cod_remdes AND
						 	  e.num_despac = a.num_despac AND
						 	  e.cod_manifi = a.cod_manifi AND
						 	  e.cod_remdes = b.cod_remdes AND
						 	  e.cod_novcum = f.cod_novcum AND
						 	  d.num_remdes = ".$row[num_remdes]."
					 GROUP BY 1";
						 						 
			$consulta = new Consulta($sql, $this -> conexion);
   			$noveda = $consulta -> ret_matriz();
   			
   			for($j = 0; $j < sizeof($noveda); $j++)
   				$novcum[$i] .= $noveda[$j][1].', ';
   					
			$formulario -> linea("*",0,"i","");
			$formulario -> linea(strtoupper($row[num_remdes]),0,"i","");
			$formulario -> linea(strtoupper($row[num_docume]),0,"i","");
			$formulario -> linea($row[fec_despac],0,"i","");
			$formulario -> linea($row[val_pesoxx],0,"i","");
			$formulario -> linea($row[nom_ciudad],0,"i","");
			$formulario -> linea($novcum[$i],0,"i","");
			$formulario -> linea($row[obs_noveda],1,"i","");
			$i++;
		}
		
		$formulario -> nueva_tabla();
		
		if($despac[foto])
			echo "<td></td><td align=\"center\">" .
					"<input type=\"button\" onClick=\"tabla('".$despac[foto]."', 'Cumplido No ".$despac[num_despac]."') \" name=\"Aceptar\" value=\"Cumplido\">" .
			 	 "</td>";
		if($cumpli)
    	$formulario -> boton("Aceptar","submit",0);

		
		$formulario -> nueva_tabla();
  		$formulario -> oculto("window","central",0);
  		$formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
  		$formulario -> oculto("opcion",0,0);
  
		$formulario -> cerrar();
	}
	
}
$service = new PorConciliar($this->conexion);
?>
