<?php

class Proc_despac
{
 var $conexion,
     $usuario,
     $cod_aplica;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }
	
	function principal()
	{
		switch($_REQUEST[opcion])
		{
			case "buscar":
				$this -> Buscar();
			break;
			
			case "filtros":
				$this -> Filtros();
			break;
			case "1":
				$this -> Formulario1();
			break;
			
			case "2":
				$this -> Insertar();
			break;
			
			default:
				$this -> Formulario1();
			break;
		}
	}
	
	function Buscar()
	{
		ini_set('display_errors', true);  
		error_reporting(E_ALL & ~E_NOTICE); 

		session_start();
		include( "../lib/general/conexion_lib.inc" );
		
		$BASE = $_SESSION[BASE_DATOS];
		
		$this -> conexion = new Conexion( $_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE  );//cod_transp
				
		$select = "SELECT a.cod_tercer, a.abr_tercer, a.num_telmov, 
						  b.fec_venlic
				   FROM $BASE.tab_tercer_tercer a,
						$BASE.tab_tercer_conduc b,
						$BASE.tab_transp_tercer c
				   WHERE a.cod_tercer = b.cod_tercer AND
						 a.cod_tercer = c.cod_tercer AND
						 c.cod_transp = '$_POST[cod_transp]' AND
						 a.cod_estado = '1' ";
		
		if( $_POST[fil_cedula] )
			$select .= " AND a.cod_tercer LIKE '$_POST[fil_cedula]%' ";
		
		if( $_POST[fil_nombre] )
			$select .= " AND a.abr_tercer LIKE '%$_POST[fil_nombre]%' ";
		
		$select .= " ORDER BY 2 ";
		
		$consulta = new Consulta( $select, $this -> conexion );
		$conductores = $consulta -> ret_matriz();
		
		$html = "<table cellpadding='3' cellspacing='0' border='0' width='100%' >";
		$html .= "<tr>";
		$html .= "<th class='celda_titulo'>Cedula</th>";
		$html .= "<th class='celda_titulo'>Nombre</th>";
		$html .= "<th class='celda_titulo'>Celular</th>";
		$html .= "<th class='celda_titulo'>Vencimiento Licencia</th>";
		$html .= "</tr>";
		
		$i = 0;
		
		if( $conductores )
		{
			foreach( $conductores as $row )
			{
				$html .= "<tr>";
				$html .= "<td class='celda_info'><a href='#' onclick='Seleccionar( \"$i\" )' ><b id='cell".$i."_0' >$row[0]</b></a></td>";
				$html .= "<td class='celda_info' id='cell".$i."_1' >$row[1]</td>";
				$html .= "<td class='celda_info' id='cell".$i."_2' >$row[2]</td>";
				$html .= "<td class='celda_info' id='cell".$i."_3' >$row[3]</td>";
				$html .= "</tr>";
				$i++;
			}
		}
		else
		{
			$html .= "<tr>";
			$html .= "<td colspan='4' class='celda_info'><h2>No se Encontraron Resultados.</h2></td>";
			$html .= "</tr>";
		}
		
		$html .= "</table>";
		
		echo rawurlencode( $html );
	}
	
	function Filtros()
	{
		$html  =  "<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
		
		$html .=  "<tr>";
		$html .=  "<td class='celda_titulo2' style='padding:3px 10px'  colspan='2' >Filtros de Condutores</td>";
		$html .=  "<td class='celda_titulo2' style='padding:3px 10px; cursor:pointer'  colspan='2' align='right' onclick='ClosePopup()' >[ cerrar ]</td>";
		$html .=  "</tr>";
		
		$html .=  "<tr>";
		$html .=  "<td class='celda_titulo' style='padding:3px 10px' align='right' >Cedula:</td>";
		$html .=  "<td class='celda_info' style='padding:3px 10px' ><input class='campo_texto' id='fil_cedula' onblur='BuscarConductor( \"$_POST[cod_transp]\" )' ></td>";
		$html .=  "<td class='celda_titulo' style='padding:3px 10px' align='right' >Nombre:</td>";
		$html .=  "<td class='celda_info' style='padding:3px 10px' ><input class='campo_texto' id='fil_nombre' onblur='BuscarConductor( \"$_POST[cod_transp]\" )' ></td>";
		$html .=  "</tr>";
		
		$html .=  "<tr>";
		$html .=  "<td class='celda_titulo2' style='padding:3px 10px'  colspan='4' align='center' >Resultado</td>";
		$html .=  "</tr>";
		
		$html .=  "</table>";
		
		echo rawurlencode( $html );
	}
	

 function Formulario1()
 {
  $datos_usuario = $this -> usuario -> retornar();
   $usuario=$datos_usuario["cod_usuari"];

   $inicio[0][0]= 0;
   $inicio[0][1]= "-";

   $query = "SELECT a.ind_desurb
               FROM ".BASE_DATOS.".tab_config_parame a
  	      WHERE a.ind_desurb = '1'";

   $consulta = new Consulta($query, $this -> conexion);
   $desurb = $consulta -> ret_matriz();

   $query = "SELECT a.ind_remdes
  		      FROM ".BASE_DATOS.".tab_config_parame a
  		     WHERE a.ind_remdes = '1'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $manredes = $consulta -> ret_matriz();

  $query = "SELECT a.cod_tercer,a.abr_tercer
  		      FROM ".BASE_DATOS.".tab_tercer_tercer a,
                 ".BASE_DATOS.".tab_tercer_activi b
  		     WHERE a.cod_tercer = b.cod_tercer AND
                 b.cod_activi = 7
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $asegura = $consulta -> ret_matriz();
  $asegura = array_merge($inicio,$asegura);
  if($_REQUEST[asegur]){
    $query = "SELECT a.cod_tercer,a.abr_tercer
  		      FROM ".BASE_DATOS.".tab_tercer_tercer a,
                 ".BASE_DATOS.".tab_tercer_activi b
  		     WHERE a.cod_tercer = b.cod_tercer AND
                 b.cod_activi = 7 AND
                 a.cod_tercer ='".$_REQUEST[asegur]."'
  		   ";

  $consulta = new Consulta($query, $this -> conexion);
  $aseg = $consulta -> ret_matriz();
  $asegura = array_merge($aseg,$asegura);
    
  }
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/despac.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/puntos.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";    
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
		echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/LoadInsertDespac.js\"></script>\n";
    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
		
		
	echo '<div id="central_transparencyDIV" style="position: absolute; opacity: 0.2; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 2; visibility: hidden; border: 1px solid black; background: gray; "></div>';

    $formulario = new Formulario ("index.php","post","INSERTAR DESPACHO","form_insert\" id=\"form_insert","","");

   $formulario -> linea("Datos Basicos del Despacho",1,"t2");
   $formulario -> nueva_tabla();

   if($datos_usuario["cod_perfil"] == "")
    $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_usuari"]);
   else
    $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_EMPTRA,$datos_usuario["cod_perfil"]);

   if($filtro -> listar($this -> conexion))
   {
    $datos_filtro = $filtro -> retornar();
    $formulario -> oculto("transp",$datos_filtro[clv_filtro],0);
    $_REQUEST[transp] = $datos_filtro[clv_filtro];
	 $transpor[0]['cod_tercer'] = $datos_filtro[clv_filtro];
   }
   else
   {
    $query = "SELECT a.cod_tercer,a.abr_tercer
   				FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			     	 ".BASE_DATOS.".tab_tercer_activi b
   			   WHERE a.cod_tercer = b.cod_tercer AND
   			         b.cod_activi = ".COD_FILTRO_EMPTRA."
   			         ORDER BY 2
   			 ";

    $consulta = new Consulta($query, $this -> conexion);
    $transpor = $consulta -> ret_matriz();
    $transpor = array_merge($inicio,$transpor);

    if($_REQUEST[transp])
    {
     $query = "SELECT a.cod_tercer,a.abr_tercer, max(c.num_consec) num_consec, c.dup_manifi
   			     FROM ".BASE_DATOS.".tab_tercer_tercer a
   			     INNER JOIN ".BASE_DATOS.".tab_tercer_activi b ON b.cod_activi = ".COD_FILTRO_EMPTRA."
   			     INNER JOIN ".BASE_DATOS.".tab_transp_tipser c ON c.cod_transp = a.cod_tercer
   			    WHERE a.cod_tercer = b.cod_tercer AND
   			          a.cod_tercer = '".$_REQUEST[transp]."'
   			          ORDER BY 2
   			  	  ";

     $consulta = new Consulta($query, $this -> conexion);
     $transp_a = $consulta -> ret_matriz();
     $transpor = array_merge($transp_a,$transpor);

    
    }

    $formulario -> nueva_tabla();
    $formulario -> lista("Transportadora","transp\" id=\"transp\" onChange=\"form_insert.submit()",$transpor,0);
   }
		
		$formulario -> nueva_tabla();
    $formulario -> oculto("transpor\" id=\"transpID",$transpor[0][0],0);
    $formulario -> oculto("Standa\" id=\"StandaID\"", DIR_APLICA_CENTRAL, 0);
		$formulario -> oculto("enviar\" id=\"submitID\"", "1", 0);
		if( $transpor[0][0] = "891857878" )
			$formulario -> oculto( "ind_reqcam\" id=\"ind_reqcamID", 1, 0 );

   if($_REQUEST[transp])
   {
     //Combo Tipo despacho
     $query = "SELECT a.cod_tipdes, UPPER( a.nom_tipdes ) AS nom_tipdes
             FROM ".BASE_DATOS.".tab_genera_tipdes a 
             WHERE cod_tipdes IN ('1','2')
             ORDER BY 2 DESC";
    
    $consulta = new Consulta($query, $this -> conexion);
    $tipdes = $consulta -> ret_matriz();

    $tipdes = array_merge($inicio,$tipdes);
    
//    if($_REQUEST[ruta])
//    {
//      $query = "SELECT a.cod_tipdes, UPPER( b.nom_tipdes ) AS nom_tipdes
//             FROM ".BASE_DATOS.".tab_genera_rutasx a LEFT JOIN 
//                  ".BASE_DATOS.".tab_genera_tipdes b ON a.cod_tipdes = b.cod_tipdes
//            WHERE a.cod_rutasx = ".$_REQUEST[ruta]."
//          ";
//
//     $consulta = new Consulta($query, $this -> conexion);
//     $tipdes_a = $consulta -> ret_matriz();
//    }
    
    if($_REQUEST[cod_tipdes])
    {
     $query = "SELECT a.cod_tipdes, UPPER( a.nom_tipdes ) AS nom_tipdes
             FROM ".BASE_DATOS.".tab_genera_tipdes a
            WHERE a.cod_tipdes = ".$_REQUEST[cod_tipdes]."
          ";

     $consulta = new Consulta($query, $this -> conexion);
     $tipdes_a = $consulta -> ret_matriz();
     $tipdes = array_merge($tipdes_a,$tipdes);
    }
   
   	$formulario -> texto("Documento #/Despacho","text","manifi\" id=\"manifi\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",0,9,9,"",$_REQUEST[manifi]);
    //$formulario -> texto("Viaje VS-000000","text","viaje\" maxlength=\"12\" size=\"12\" id=\"viaje\" onkeypress=\"return NumericInput(event)",1,9,9,"",$_REQUEST["viaje"]);
    $formulario -> texto("Viaje (VS-000000)","text","viaje\" maxlength=\"12\" size=\"12\" id=\"viaje\" ",1,9,9,"",$_REQUEST["viaje"]);
    $query = "SELECT a.cod_tercer,a.abr_tercer
   			    FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			         ".BASE_DATOS.".tab_tercer_activi b,
   			         ".BASE_DATOS.".tab_transp_tercer c
   			   WHERE a.cod_tercer = b.cod_tercer AND
   			         a.cod_tercer = c.cod_tercer AND
   			         c.cod_transp = '".$_REQUEST[transp]."' AND
   			         b.cod_activi = ".COD_FILTRO_CLIENT."
                 ORDER BY 2 ASC
   			 ";

   	$consulta = new Consulta($query, $this -> conexion);
    $listgene = $consulta -> ret_matriz();

    $listgene = array_merge($inicio,$listgene);

    if($_REQUEST[generador])
    {
     $query = "SELECT a.cod_tercer,a.abr_tercer
   			     FROM ".BASE_DATOS.".tab_tercer_tercer a,
   			          ".BASE_DATOS.".tab_tercer_activi b,
   			          ".BASE_DATOS.".tab_transp_tercer c
   			    WHERE a.cod_tercer = b.cod_tercer AND
   			          a.cod_tercer = c.cod_tercer AND
   			          c.cod_transp = '".$_REQUEST[transp]."' AND
   			          b.cod_activi = ".COD_FILTRO_CLIENT." AND
   			          a.cod_tercer = '".$_REQUEST[generador]."'
                  ORDER BY 2 ASC
   			  ";

   	 $consulta = new Consulta($query, $this -> conexion);
     $listgene_a = $consulta -> ret_matriz();

     $listgene = array_merge($listgene_a,$listgene);
    }

   	$query = "SELECT ind_retdpa,val_minret,val_maxret
      			FROM ".BASE_DATOS.".tab_config_parame
      			";

    $consulta = new Consulta($query, $this -> conexion);
    $paramret = $consulta -> ret_matriz();

   	//Consulta el tipo de validaciÃ³n segun parametrizado del valor declarado.
    $query = "SELECT ind_valpol
                FROM ".BASE_DATOS.".tab_config_parame
             ";

    $consulta = new Consulta($query, $this -> conexion);
    $pardec = $consulta -> ret_vector();

    $query = "SELECT cod_perfil
                FROM ".BASE_DATOS.".tab_autori_perfil
               WHERE cod_perfil = '".$this -> usuario -> cod_perfil."' AND
              		 cod_autori = '1'";

    $consec = new Consulta($query, $this -> conexion);
    $autfec = $consec -> ret_arreglo();

    $query = "SELECT a.cod_agenci,a.nom_agenci
                FROM ".BASE_DATOS.".tab_genera_agenci a,
               		 ".BASE_DATOS.".tab_transp_agenci b
               WHERE a.cod_agenci = b.cod_agenci AND
               		 b.cod_transp = '".$_REQUEST[transp]."'
           	  ";

    if($datos_usuario["cod_perfil"] == "")
    {
     $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_usuari"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND a.cod_agenci = '$datos_filtro[clv_filtro]' ";
     }
    }
    else
    {
     $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_AGENCI,$datos_usuario["cod_perfil"]);
     if($filtro -> listar($this -> conexion))
     {
      $datos_filtro = $filtro -> retornar();
      $query = $query . " AND a.cod_agenci = '$datos_filtro[clv_filtro]' ";
     }
    }

    $query .= " ORDER BY 2";

    $consulta = new Consulta($query, $this -> conexion);
    $agencia = $consulta -> ret_matriz();

    $agencias = array_merge($inicio,$agencia);

    if($_REQUEST[agencia])
    {
     $query = "SELECT a.cod_agenci,a.nom_agenci
     		     FROM ".BASE_DATOS.".tab_genera_agenci a
     		    WHERE a.cod_agenci = ".$_REQUEST[agencia]."
     		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $agencia_a = $consulta -> ret_matriz();

     $agencias = array_merge($agencia_a,$agencia);
    }
    
    //Tipo despacho
     $query = "SELECT a.cod_tipdes,a.nom_tipdes
     		     FROM ".BASE_DATOS.".tab_genera_tipdes a 
     		     WHERE cod_tipdes IN ('1','2')
     		     ORDER BY 2 DESC";
    
    $consulta = new Consulta($query, $this -> conexion);
    $tipdes = $consulta -> ret_matriz();

    $tipdes = array_merge($inicio,$tipdes);
    
    if($_REQUEST[cod_tipdes])
    {
     $query = "SELECT a.cod_tipdes,a.nom_tipdes
     		     FROM ".BASE_DATOS.".tab_genera_tipdes a
     		    WHERE a.cod_tipdes = ".$_REQUEST[cod_tipdes]."
     		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $tipdes_a = $consulta -> ret_matriz();

     $tipdes = array_merge($tipdes_a,$tipdes);
    }

    $objciud = new Despachos($_REQUEST[cod_servic],$_REQUEST[opcion],$this -> aplica,$this -> conexion);

    //trae las ciudades de Origen
    $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
                FROM ".BASE_DATOS.".tab_genera_ciudad a,
		     		 ".BASE_DATOS.".tab_genera_rutasx b,
		     		 ".BASE_DATOS.".tab_genera_ruttra c,
		     		 ".BASE_DATOS.".tab_genera_depart d,
		     		 ".BASE_DATOS.".tab_genera_paises e
               WHERE a.cod_ciudad = b.cod_ciuori AND
		     		 b.cod_depori = d.cod_depart AND
		     		 b.cod_paiori = d.cod_paisxx AND
		     		 d.cod_paisxx = e.cod_paisxx AND
		     		 b.cod_rutasx = c.cod_rutasx AND
		     		 c.cod_transp = '".$_REQUEST[transp]."' AND
		     		 b.ind_estado = '".COD_ESTADO_ACTIVO."'
           	     	 GROUP BY 1 ORDER BY 2
           	  ";

    $consulta = new Consulta($query, $this -> conexion);
    $ciuoris = $consulta -> ret_matriz();

    $ciuoris = array_merge($inicio,$ciuoris);

    if($_REQUEST[ciuori])
    {
   	 $ciudad_a = $objciud -> getSeleccCiudad($_REQUEST[ciuori]);
   	 $ciuoris = array_merge($ciudad_a,$ciuoris);

     //trae las ciudades de destino
     $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
                 FROM ".BASE_DATOS.".tab_genera_ciudad a,
		     		  ".BASE_DATOS.".tab_genera_rutasx b,
		     		  ".BASE_DATOS.".tab_genera_ruttra c,
		     		  ".BASE_DATOS.".tab_genera_depart d,
		     		  ".BASE_DATOS.".tab_genera_paises e
                WHERE a.cod_ciudad = b.cod_ciudes AND
		     		  b.cod_depdes = d.cod_depart AND
		     		  b.cod_paides = d.cod_paisxx AND
		     		  d.cod_paisxx = e.cod_paisxx AND
                      b.cod_ciuori = ".$_REQUEST[ciuori]." AND
		     		  b.cod_rutasx = c.cod_rutasx AND
		     		  c.cod_transp = '".$_REQUEST[transp]."' AND
		     		  b.ind_estado = '".COD_ESTADO_ACTIVO."'
           	     	  GROUP BY 1 ORDER BY 2
           	  ";

     $consulta = new Consulta($query, $this -> conexion);
     $ciudess = $consulta -> ret_matriz();

     $ciudess = array_merge($inicio,$ciudess);

     if($_REQUEST[ciudes])
     {
      $ciudad_a = $objciud -> getSeleccCiudad($_REQUEST[ciudes]);
      $ciudess = array_merge($ciudad_a,$ciudess);

      //trae las rutas segun la ciudad de origen y la ciudad de destino
      $query = "SELECT a.cod_rutasx,a.nom_rutasx
                  FROM ".BASE_DATOS.".tab_genera_rutasx a,
                 	   ".BASE_DATOS.".tab_genera_ruttra b
                 WHERE a.cod_rutasx = b.cod_rutasx AND
                       b.cod_transp = '".$_REQUEST[transp]."' AND
                       a.cod_ciuori = ".$_REQUEST[ciuori]." AND
                       a.cod_ciudes = ".$_REQUEST[ciudes]." AND
		      		   a.ind_estado = '".COD_ESTADO_ACTIVO."'
                       GROUP BY 1 ORDER BY 2 ";

      $consulta = new Consulta($query, $this -> conexion);
      $rutas = $consulta -> ret_matriz();

      $rutas = array_merge($inicio,$rutas);

      if($_REQUEST[ruta])
      {
       $query = "SELECT cod_rutasx,nom_rutasx
                   FROM ".BASE_DATOS.".tab_genera_rutasx
                  WHERE cod_rutasx = ".$_REQUEST[ruta]."
           	       	    ORDER BY 2 ";

       $consulta = new Consulta($query, $this -> conexion);
       $ruta_a = $consulta -> ret_matriz();

       $rutas = array_merge($ruta_a,$rutas);
      }
     }
    }
	if($_REQUEST[placa] )
	{
    $query = "SELECT a.num_placax, b.nom_marcax, c.nom_lineax, d.nom_colorx,
                     e.nom_carroc, a.ano_modelo, a.num_config, a.cod_conduc,
                     a.cod_propie, a.cod_tenedo, e.cod_carroc 
            	  FROM ".BASE_DATOS.".tab_vehicu_vehicu a 
           LEFT JOIN ".BASE_DATOS.".tab_vehige_config f ON a.num_config = f.num_config,
                     ".BASE_DATOS.".tab_genera_marcas b,
                     ".BASE_DATOS.".tab_vehige_lineas c,
                     ".BASE_DATOS.".tab_vehige_colore d,
                     ".BASE_DATOS.".tab_vehige_carroc e,
                     ".BASE_DATOS.".tab_transp_vehicu i,
                     ".BASE_DATOS.".tab_transp_tercer j
               WHERE a.cod_marcax = b.cod_marcax 
                 AND a.cod_marcax = c.cod_marcax 
                 AND a.cod_lineax = c.cod_lineax 
                 AND a.cod_colorx = d.cod_colorx 
                 AND a.cod_carroc = e.cod_carroc 
                 AND a.num_placax = i.num_placax    
                 AND i.cod_transp = j.cod_transp               
                 AND a.num_placax = '".$_REQUEST[placa]."' 
                 AND a.ind_estado = '".COD_ESTADO_ACTIVO."' 
                 AND i.cod_transp = '".$_REQUEST[transp]."' 
            ";
     $consulta = new Consulta($query, $this -> conexion);
     $placas = $consulta -> ret_matriz();



     if($placas)
     {
       $query = "SELECT a.cod_tercer,a.abr_tercer
      		      FROM ".BASE_DATOS.".tab_tercer_tercer a,
      		      	   ".BASE_DATOS.".tab_tercer_activi b,
      		      	   ".BASE_DATOS.".tab_transp_tercer c
      		     WHERE a.cod_tercer = b.cod_tercer AND
      		      	   a.cod_tercer = c.cod_tercer AND
      		      	   a.cod_estado = '".COD_ESTADO_ACTIVO."' AND
      		      	   b.cod_activi = ".COD_FILTRO_PROPIE." AND
      		      	   c.cod_transp = '".$_REQUEST[transp]."' AND
      		      	   a.cod_tercer = '".$placas[0][8]."'
      		   ";

     
      $consulta = new Consulta($query, $this -> conexion);
      $propihab = $consulta -> ret_matriz();

      $menerrtercer = NULL;

      if(!$propihab)
      {
       $menerrtercer = "El Propietario Asignado al Vehiculo, no Se Encuentra Relacionado a la Transportadora &oacute; no se Encuentra Activo.</br>";
      }
		
		
		$query = "SELECT a.cod_tercer,a.abr_tercer
      		      FROM ".BASE_DATOS.".tab_tercer_tercer a,
      		      	   ".BASE_DATOS.".tab_tercer_activi b,
      		      	   ".BASE_DATOS.".tab_transp_tercer c
				  WHERE a.cod_tercer = b.cod_tercer AND
						a.cod_tercer = c.cod_tercer AND
						a.cod_estado = '".COD_ESTADO_ACTIVO."' AND
						b.cod_activi = '6' AND
						c.cod_transp = '".$_REQUEST[transp]."' AND
      		      	    a.cod_tercer = '".$placas[0][9]."' ";

      
      $consulta = new Consulta($query, $this -> conexion);
      $propihab = $consulta -> ret_matriz();

      if(!$propihab)
      {
		$menerrtercer .= "El Poseedor Asignado al Vehiculo, no Se Encuentra Relacionado a la Transportadora &oacute; no se Encuentra Activo.";
      }

      //trae los conductores
      $query = "SELECT a.cod_tercer, a.abr_tercer,a.num_telmov
                  FROM ".BASE_DATOS.".tab_tercer_tercer a,
                       ".BASE_DATOS.".tab_tercer_conduc b,
                       ".BASE_DATOS.".tab_transp_tercer c
                 WHERE a.cod_tercer = b.cod_tercer AND
                       a.cod_tercer = c.cod_tercer AND
                       c.cod_transp = '".$_REQUEST[transp]."' AND
                       a.cod_estado = ".COD_ESTADO_ACTIVO."
					   ORDER BY 2
			";

      

      $consulta = new Consulta($query, $this -> conexion);
      $conducs  = $consulta -> ret_matriz();

      $conducs = array_merge($inicio,$conducs);

      if($_REQUEST[conduc])
	  {
       $query = "SELECT a.cod_tercer,a.abr_tercer,a.num_telmov
                   FROM ".BASE_DATOS.".tab_tercer_tercer a,
                        ".BASE_DATOS.".tab_tercer_conduc b,
      		            ".BASE_DATOS.".tab_transp_tercer c
                  WHERE a.cod_tercer = c.cod_tercer AND
                        a.cod_tercer = b.cod_tercer AND
                        a.cod_estado = ".COD_ESTADO_ACTIVO." AND
                        a.cod_tercer = '".$_REQUEST[conduc]."' AND
                        c.cod_transp = '".$_REQUEST[transp]."'
                 ";

     
       $consulta = new Consulta($query, $this -> conexion);
       $conduc_e  = $consulta -> ret_matriz();

       $conducs = array_merge($conduc_e,$conducs);
	  }
	  else
	  {
	   $query = "SELECT a.cod_tercer,a.abr_tercer,a.num_telmov
      		       FROM ".BASE_DATOS.".tab_tercer_tercer a,
      		            ".BASE_DATOS.".tab_tercer_conduc b,
      		            ".BASE_DATOS.".tab_transp_tercer c
      		      WHERE a.cod_tercer = b.cod_tercer AND
      		            a.cod_tercer = c.cod_tercer AND
                        a.cod_estado = ".COD_ESTADO_ACTIVO." AND
      		            c.cod_transp = '".$_REQUEST[transp]."' AND
      		            a.cod_tercer = '".$placas[0][7]."'
      		    ";

       //echo "<hr />" . $query;

       $consulta = new Consulta($query, $this -> conexion);
       $conduc_e = $consulta -> ret_matriz();

       $conducs = array_merge($conduc_e,$conducs);
	  }
     }
   
else
     {
      if($_REQUEST[placa])
           {
           $mensaje_vehi = "El Vehiculo con Placas <b>".$_REQUEST[placa]."</b> No se Existe en el Sistema &oacute; no se Encuentra Activo.";
           unset($_REQUEST[placa]);
           } 
 }
}

    $formulario -> lista("Agencia", "agencia", $agencias, 0);

    $fec_manifi = date("Y/m/d");

    if($autfec)
    {
     if(!$_REQUEST[fecman])
      $_REQUEST[fecman]= $fec_manifi;

     $formulario -> fecha_calendar("Fecha(YYYY/MM/DD)","fecman","form_insert",$_REQUEST[fecman],"yyyy/mm/dd",1);
    }
    else
    {
     $fec = date("Y-m-d");
     $formulario -> linea("Fecha",0,"t","","","RIGHT");
     $formulario -> linea($fec_manifi,1,"i");
     $formulario -> oculto("fecman", $fec, 0);
    }

    $formulario -> lista("Origen", "ciuori\" onChange=\"form_insert.submit()", $ciuoris, 0);
    $formulario -> lista("Destino", "ciudes\" onChange=\"form_insert.submit()", $ciudess, 1);
    $formulario -> lista("Ruta", "ruta\" onChange=\"form_insert.submit()", $rutas, 0);
    $formulario -> lista("Tipo Despacho", "cod_tipdes", $tipdes, 1);
    $formulario -> lista("Generador", "generador",$listgene, 0);
    $formulario -> texto("No. Caravana","text","carava",1,4,4,"","$_REQUEST[carava]");//jorge
    $formulario -> texto ("Valor Declarado del Despacho","text","valdec\" onkeyup=\"puntos(this,this.value.charAt(this.value.length-1))",0,9,12,"","$_REQUEST[valdec]");
    $formulario -> texto ("Peso (Tn)","text","pesoxx\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}",1,5,5,"","$_REQUEST[pesoxx]");
    $formulario -> texto("Operador Gps","text","gps_operad",0,15,15,"",$_POST['gps_operad'],"","",0);
    $formulario -> texto("Usuario Gps","text","gps_usuari",1,15,15,"",$_POST['gps_usuari'],"","",0);
    $formulario -> texto("Contraseña Gps","text","gps_paswor",1,15,15,"",$_POST['gps_paswor'],"","",0);
    $formulario -> lista("Aseguradora","asegur",$asegura,1);
    $formulario -> texto ("No. Poliza","text","poliza",0,20,15,"","$_REQUEST[poliza]");
    $formulario -> oculto("duplicar\" id=\"duplicar",$transpor[0]['dup_manifi'],0);
  
    if($placas)
     $formulario -> oculto("regplaca",1,0);
    else
     $formulario -> oculto("regplaca",0,0);

    $formulario -> nueva_tabla();
    $formulario -> linea("Informaci&oacute;n del Veh&iacute;culo",1,"t2");

    if($mensaje_vehi)
    {
     $formulario -> nueva_tabla();
     $formulario -> linea($mensaje_vehi,1,"e");
    }
	
    $matpopup[0]["nomvar"] = "transport";
	  $matpopup[0]["valorx"] = $transpor[0]['cod_tercer'];
	  $matpopup[1]["nomvar"] = "indice";
	  $matpopup[1]["valorx"] = "placa";
	  $matpopup[2]["nomvar"] = "frm_remote";
	  $matpopup[2]["valorx"] = "frm_insert";
	//$matpopup[1]["valorx"] = "placa";


    $formulario -> nueva_tabla();
    $formulario -> texto ("Placa","text","placa\" id=\"num_placaxID\" onchange=\"if(this.value){form_insert.submit()}else{this.focus()}\" value=\"$_REQUEST[placa]");
    echo "<td><input type='button' name='search' id='search' onClick='PopupVehiculos()' value='Buscar'></td></tr><tr>";

    if($placas)
    {
     $conduc_a[0][0] = $placas[0][7];
     $conduc_a[0][1] = $placas[0][8];

     $formulario -> linea ("Marca",0,"t","","","RIGHT");
     $formulario -> linea ($placas[0][1],1,"i");
     $formulario -> linea ("Color",0,"t","","","RIGHT");
     $formulario -> linea ($placas[0][3],0,"i");
     $formulario -> linea ("Carroceria",0,"t","","","RIGHT");
     $formulario -> linea ($placas[0][4],1,"i");
     $formulario -> linea ("Modelo",0,"t","","","RIGHT");
     $formulario -> linea ($placas[0][5],0,"i");
     $formulario -> linea ("Configuraci&oacute;n",0,"t","","","RIGHT");
     $formulario -> linea ($placas[0][6],1,"i");

     $formulario -> nueva_tabla();
     $formulario -> linea ("Informaci&oacute;n del Conductor",1,"t2");

		
		
		/*$formulario -> nueva_tabla();
		$formulario -> lista("Conductor", "conduc\" onChange=\"form_insert.submit();", $conducs, 1);
		$formulario -> linea ("CC",0,"t","","","RIGHT");
		$formulario -> linea ($conduc_e[0][0],1,"i");
		$formulario -> linea ("Tel&eacute;fono Movil",0,"t","","","RIGHT");
		$formulario -> linea ($conduc_e[0][2],1,"i");*/
	 
		
		$formulario -> nueva_tabla();
		echo "<td class='celda_titulo' align='right' >* Conductor:</td>";
		echo "<td class='celda_info' align='left' >
				<input type='text' class='campo_texto' readonly name='cod_conduc' id='cod_conduc' value='$_POST[cod_conduc]'  >
        <input type='button' value='#' class='crmButton small save' style='width:23px' maxlength='15' size='15' onclick='CargarCondutores( \"$_REQUEST[transp]\" )' >
			  </td>";
		echo "<td class='celda_titulo' align='right' >* Nombre:</td>";
		echo "<td class='celda_info' align='left' >
			<input type='text' class='campo_texto' name='nom_conduc' id='nom_conduc' value='$_POST[nom_conduc]' maxlength='30' size='30' readonly style='border:0px;'  ></td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td class='celda_titulo' align='right' >* Celular 1:</td>";
		echo "<td class='celda_info' align='left' ><input type='text' class='campo_texto' value='$_POST[cel_conduc]'  name='cel_conduc' id='cel_conduc' maxlength='10' size='10' ></td>";
		echo "<td class='celda_titulo' align='right' >Celular 2:</td>";
		echo "<td class='celda_info' align='left' ><input type='text' class='campo_texto' value='$_POST[cel_condu2]' name='cel_condu2' id='cel_condu2' maxlength='10' size='10' ></td>"; 

     $query = "SELECT a.num_trayle,MAX(a.num_noveda)
     		     FROM ".BASE_DATOS.".tab_trayle_placas a,
		      		  ".BASE_DATOS.".tab_vehige_trayle b,
		      		  ".BASE_DATOS.".tab_transp_trayle c
     		    WHERE a.num_trayle = b.num_trayle AND
		      		  a.num_trayle = c.num_trayle AND
		      		  c.cod_transp = '".$_REQUEST[transp]."' AND
		      		  b.ind_estado = '".COD_ESTADO_ACTIVO."' AND
		      	 	  a.num_placax = '".$placas[0][0]."'
		      		  GROUP BY 1
     		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $trayler = $consulta -> ret_vector();

     $query = "SELECT a.num_trayle,a.num_trayle
     		     FROM ".BASE_DATOS.".tab_vehige_trayle a,
     		     	  ".BASE_DATOS.".tab_transp_trayle b
	        	WHERE a.ind_estado = '".COD_ESTADO_ACTIVO."' AND
		      		  a.num_trayle = b.num_trayle AND
		      		  b.cod_transp = '".$_REQUEST[transp]."'
     		      	  ORDER BY 1
     		  ";

     $consulta = new Consulta($query, $this -> conexion);
     $listatra = $consulta -> ret_matriz();

     $listatra = array_merge($inicio,$listatra);

     $formulario -> nueva_tabla();
     $formulario -> linea ("Informaci&oacute;n del Remolque",1,"t2");
     $formulario -> nueva_tabla();
     if($placas[0][6] == "2" || $placas[0][6] == "3" || $placas[0][6] == "4")
     {
      $formulario -> linea ("La Configuraci&oacute;n Actual del Vehiculo no Solicita Remolque",0,"i");
      $formulario -> oculto("l_trayle",null,0);
      $formulario -> oculto("soltrayle","0",0);
     }
     else if($trayler)
     {
       if(!$_REQUEST[l_trayle])
       {
        $mi_trayler[0][0] = $trayler[0];
        $mi_trayler[0][1] = $trayler[0];
       }
       else
       {
        $mi_trayler[0][0] = $_REQUEST[l_trayle];
        $mi_trayler[0][1] = $_REQUEST[l_trayle];
       }

       $listatra = array_merge($mi_trayler,$listatra);

       $formulario -> lista("Remolque:", "l_trayle", $listatra, 1);
       $formulario -> oculto("soltrayle","1",0);
     }
     else
     {
       if($_REQUEST[l_trayle])
       {
        $mi_trayler[0][0] = $_REQUEST[l_trayle];
        $mi_trayler[0][1] = $_REQUEST[l_trayle];

        $listatra = array_merge($mi_trayler,$listatra);
       }

       $formulario -> lista("Remolque:", "l_trayle", $listatra, 1);
       $formulario -> linea ("El Vehiculo Solicita una Asignaci&oacute;n De Remolque en su Informaci&oacute;n Base",1,"t2");
       $formulario -> oculto("soltrayle","1",0);
     }

     //vigencia final de Revision Mecanica num_tarpro
     $query = "SELECT a.fec_revmec
                 FROM ".BASE_DATOS.".tab_vehicu_vehicu a
                WHERE a.fec_revmec < NOW() AND
                      a.ind_estado = '".COD_ESTADO_ACTIVO."' AND
                      a.num_placax = '".$_REQUEST[placa]."'
               ";

     $consulta = new Consulta($query, $this -> conexion);
     $revmec = $consulta -> ret_matriz();

     //vigencia final de la licencia de conducion del conductor
     $query = "SELECT a.fec_venlic,b.abr_tercer
                 FROM ".BASE_DATOS.".tab_tercer_conduc a,
                 	  ".BASE_DATOS.".tab_tercer_tercer b
                WHERE a.cod_tercer= b.cod_tercer AND
                      a.cod_tercer = '$_POST[cod_conduc]' AND
                      a.fec_venlic < NOW()
              ";

     $consulta = new Consulta($query, $this -> conexion);
     $vlicondu = $consulta -> ret_matriz();
    }

    $formulario -> nueva_tabla();
    $formulario -> linea("Informaci&oacute;n Adicional",1,"t2");

    $formulario -> nueva_tabla();
    $formulario -> texto ("Medios de Comunicaci&oacute;n:","textarea","medcom",0,20,2,"","$_REQUEST[medcom]");
    $formulario -> texto ("Observaciones Generales:","textarea","obsgrl",1,20,2,"","$_REQUEST[obsgrl]");

    if($manredes)
    {
     $formulario -> nueva_tabla();
     $formulario -> linea("Selecci&oacute;n de Destinatarios",1,"t2");

     $formulario -> nueva_tabla();
     $formulario -> linea("",0,"t");
     $formulario -> linea("(S/N)",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Documento/C&oacute;digo",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Nombre",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Observaciones",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Ciudad",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Direcci&oacute;n",0,"t");

     if($desurb)
     {
      $formulario -> linea("",0,"t");
      $formulario -> linea("Telefono",0,"t");
      $formulario -> linea("",0,"t");
      $formulario -> linea("Longitud",0,"t");
      $formulario -> linea("",0,"t");
      $formulario -> linea("Latitud",0,"t");
     }

	 $formulario -> linea("",0,"t");
     $formulario -> linea("Peso (Tn)",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Valor (Unit)",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Valor Flete",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Remisi&oacute;n",0,"t");
     $formulario -> linea("",0,"t");
     $formulario -> linea("Pedido",1,"t");

	 $codrem = $_REQUEST[codrem];
     $docrem = $_REQUEST[docrem];
     $obsrem = $_REQUEST[obsrem];
     $nomrem = $_REQUEST[nomrem];
     $ciurem = $_REQUEST[ciurem];
     $dirrem = $_REQUEST[dirrem];
     $pesrem = $_REQUEST[pesrem];
     $remsel = $_REQUEST[remsel];
     $refrem = $_REQUEST[refrem];
     $pedrem = $_REQUEST[pedrem];
     $contel = $_REQUEST[contel];

     $conlon = $_REQUEST[conlon];
     $conlat = $_REQUEST[conlat];

     $tabfle = $_REQUEST[tabfle];
     $fleuni = $_REQUEST[fleuni];
     $codfle = $_REQUEST[codfle];

     if(!$_REQUEST[maxrem])
      $_REQUEST[maxrem] = 1;

     $ciudades = $objciud -> getListadoCiudades();
     $ciurem_t = array_merge($inicio,$ciudades);

	 $matpopup[0]["nomvar"] = "tipoxx";
	 $matpopup[0]["valorx"] = "2";
	 $matpopup[1]["nomvar"] = "transport";
	 $matpopup[1]["valorx"] = $transpor[0][0];
	 $matpopup[2]["nomvar"] = "indice";
	 $matpopup[2]["valorx"] = "rem";

     for($i = 0; $i < $_REQUEST[maxrem]; $i++)
     {
      if($ciurem[$i])
      {
       $ciurem_a = $objciud -> getSeleccCiudad($ciurem[$i]);
       $ciurem_s = array_merge($ciurem_a,$ciurem_t);
      }
      else
       $ciurem_s = $ciurem_t;

      $estado = 0;

      if($remsel[$i] || $i == $_REQUEST[maxrem] - 1)
       $estado = 1;

      eval("\$sasignado = \$_REQUEST[codrem".$i."];");

      if($sasignado != "n")
       $formulario -> oculto("codrem".$i,$sasignado,0);
      else
       $formulario -> oculto("codrem".$i,"n",0);
      $formulario -> caja("","remsel[$i]",1,$estado,0);
      $formulario -> texto ("","text","docrem[$i]\" id=\"docrem$i",0,10,10,"",$docrem[$i],"$i;5",3192,NULL,0,$matpopup);
      $formulario -> texto ("","text","nomrem[$i]\" id=\"nomrem$i",0,15,32,"",$nomrem[$i]);
      $formulario -> texto ("","text","obsrem[$i]\" id=\"obsrem$i",0,15,250,"",$obsrem[$i]);
      //$formulario -> lista ("","ciurem[$i]\" id=\"ciurem$i\" onChange=\"alert(document.getElementById(tabflerem$i).value)",$ciurem_s,0);     
      $formulario -> lista ("","ciurem[$i]\" id=\"ciurem$i\" onClick=\"validarflete(".$i.");",$ciurem_s,0);
      $formulario -> texto ("","text","dirrem[$i]\" id=\"dirrem$i",0,15,32,"",$dirrem[$i]);

      if($desurb)
      {
       $formulario -> texto("","text","contel[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" id=\"telrem$i",0,10,20,"",$contel[$i]);
	   $formulario -> texto("","text","conlon[$i]\" id=\"lonrem$i",0,10,30,"",$conlon[$i]);
       $formulario -> texto("","text","conlat[$i]\" id=\"latrem$i",0,10,30,"",$conlat[$i]);
      }

      $matpopup[3]["nomvar"] = "ciuoritab";
	  $matpopup[3]["valorx"] = $_REQUEST[ciuori];
	  $matpopup[4]["nomvar"] = "carroctab";
	  $matpopup[4]["valorx"] = $placas[0][10];
	  
	  $idelement[0]["nomvar"] = "ciurem$i";	  
	  $idelement[1]["nomvar"] = "pesrem$i";	  

      $formulario -> texto ("","text","pesrem[$i]\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onClick=\"validarflete(".$i.")\" id=\"pesrem$i",0,4,5,"",$pesrem[$i]);
      $formulario -> texto ("","text","fleuni[$i]\" id=\"fleunirem$i\" readonly",0,4,5,"",$fleuni[$i]);
      $formulario -> texto ("","text","tabfle[$i]\" id=\"tabflerem$i\" readonly ",0,10,30,"",$tabfle[$i],"$i;4",3193,NULL,0,$matpopup,$idelement);	  
      $formulario -> oculto("codfle[$i]\" id=\"codflerem$i",$codfle[$i],0);	  
      $formulario -> texto ("","text","refrem[$i]",0,5,10,"",$refrem[$i]);
      $formulario -> texto ("","text","pedrem[$i]",1,5,10,"",$pedrem[$i]);

     }

	 $formulario -> nueva_tabla();
     $formulario -> botoni("Otro","form_insert.maxrem.value++; form_insert.submit();",1);

     $formulario -> oculto("maxrem",$_REQUEST[maxrem],0);
    }

    $formulario -> nueva_tabla();

    if($desurb)
     $formulario -> oculto("desurb",1,0);
    else
     $formulario -> oculto("desurb",0,0);

   

    if(!$menerrtercer)
     $formulario -> botoni("Aceptar","aceptar(document.form_insert, 'Esta Seguro de Insertar el Despacho?', 2)",0);
    else
     $formulario -> linea($menerrtercer,1,"e");
   }
   
		$formulario -> oculto("mrevmec",sizeof($revmec),0);
		$formulario -> oculto("frevmec",$revmec[0][0],0);
		$formulario -> oculto("mvlicondu",sizeof($vlicondu),0);
		$formulario -> oculto("fvlicondu",$vlicondu[0][0],0);
		$formulario -> oculto("nconduc",$vlicondu[0][1],0);
		$formulario -> oculto("parvalol",$pardec[0],0);
		$formulario -> oculto("usuario","$usuario",0);
		$formulario -> oculto("manredes",$manredes,0);
		$formulario -> oculto("window","central",0);
		$formulario -> oculto("aplica_central\" id=\"aplica_central",DIR_APLICA_CENTRAL,0);
		
		//$formulario -> oculto( "url_archiv", "ins_despac_despac2.php", 0 );
		$formulario -> oculto("url_archiv\" id=\"url_archiv","ins_despac_despac.php",0);
		
		$formulario -> oculto("opcion",1,0);
		$formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
		$formulario -> cerrar();
   
		echo '<tr><td><div id="AplicationEndDIV"></div>              
              <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>              
              <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">
				
				<div id="filtros" >
				</div>
				
				<div id="result" >
				</div>
			  </div></td></tr>';
    echo "<center><div id='PopUpID'></div></center>";
			  
		//FINAL
	}
	
	function Insertar()
	{
		
		$fec_actual = date("Y-m-d H:i:s");
		$hor_actual = date("H:i:s");
		
		$_REQUEST[fecman]= $_REQUEST[fecman]." ".$hor_actual;
		if(!$_REQUEST[generador])
			$_REQUEST[generador] = "NULL";
		else
			$_REQUEST[generador] = "'".$_REQUEST[generador]."'";
		
		if(!$_REQUEST[l_trayle])
			$_REQUEST[l_trayle] = "null";
		else
			$_REQUEST[l_trayle] = "'".$_REQUEST[l_trayle]."'";
		
		if(!$_REQUEST[pesoxx])
			$_REQUEST[pesoxx] = 0;
      
    //jorge
    if(!$_REQUEST[carava])
			$_REQUEST[carava] = 0;
		
		$query = "SELECT Max(num_despac) AS maximo
				  FROM ".BASE_DATOS.".tab_despac_despac ";
		
		$consec = new Consulta($query, $this -> conexion);
		$ultimo = $consec -> ret_matriz();
		
		$ultimo_consec = $ultimo[0][0];
		$nuevo_consec = $ultimo_consec+1;
		
		$query = "SELECT a.cod_paisxx,a.cod_depart
			      FROM ".BASE_DATOS.".tab_genera_ciudad a
				  WHERE a.cod_ciudad = ".$_REQUEST[ciuori]." ";
		
		$consulta = new Consulta($query, $this -> conexion);
		$paidepori = $consulta -> ret_matriz();
		
		$query = "SELECT a.cod_paisxx,a.cod_depart
				  FROM ".BASE_DATOS.".tab_genera_ciudad a
				  WHERE a.cod_ciudad = ".$_REQUEST[ciudes]." ";

		$consulta = new Consulta($query, $this -> conexion);
		$paidepdes = $consulta -> ret_matriz();
		
		//Se obtiene la informacion del conductor
		$query = "SELECT a.num_telef1, a.num_telmov, a.dir_domici
				  FROM ".BASE_DATOS.".tab_tercer_tercer a
				  WHERE a.cod_tercer = ".$_REQUEST[cod_conduc]." ";

		$consulta = new Consulta($query, $this -> conexion);
		$conduc = $consulta -> ret_matriz();
		
		$_REQUEST[valdec] = str_replace('.','',$_REQUEST[valdec]);
		$_REQUEST[flete] = str_replace('.','',$_REQUEST[flete]);
		$_REQUEST[antici] = str_replace('.','',$_REQUEST[antici]);
		$_REQUEST[despac] = str_replace('.','',$_REQUEST[despac]);
		$_REQUEST["asegur"] = $_REQUEST["asegur"] ? "'".$_REQUEST["asegur"]."'" : 'NULL' ;
		//query de insercion de despachos Jorge num_carava
		$query = "INSERT INTO ".BASE_DATOS.".tab_despac_despac
					(
						num_despac, cod_manifi, fec_despac, 
						cod_client, cod_paiori, cod_depori,
						cod_ciuori, cod_paides, cod_depdes, 
						cod_ciudes, val_flecon, val_despac, 
						val_antici, val_retefu, nom_carpag, 
						nom_despag, cod_agedes, fec_pagoxx, 
						obs_despac, val_declara, usr_creaci,
						fec_creaci, val_pesoxx, con_telef1,
						con_telmov, con_domici, cod_tipdes,
            gps_operad, gps_usuari, gps_paswor,
            cod_asegur, num_poliza, num_carava
					)
					VALUES 
					(
						".$nuevo_consec.", '".$_REQUEST[manifi]."', '".$_REQUEST[fecman]."', 
						".$_REQUEST[generador].", ".$paidepori[0][0].", ".$paidepori[0][1].", 
						".$_REQUEST[ciuori].", ".$paidepdes[0][0].", ".$paidepdes[0][1].", 
						".$_REQUEST[ciudes].", NULL, NULL,
						NULL, NULL, NULL, 
						NULL, '".$_REQUEST[agencia]."', NULL, 
						'".$_REQUEST[obsgrl]."', '".$_REQUEST[valdec]."', '".$_REQUEST[usuario]."', 
						'$fec_actual', ".$_REQUEST[pesoxx].", '".$conduc[0][0]."',
						'".$conduc[0][1]."', '".$conduc[0][2]."', '".$_REQUEST['cod_tipdes']."',
            '".$_REQUEST[gps_operad]."', '".$_REQUEST[gps_usuari]."', '".$_REQUEST[gps_paswor]."',
            ".$_REQUEST['asegur'].", '".$_REQUEST['poliza']."', '".$_REQUEST['carava']."'
					)";
		
		$consulta = new Consulta($query, $this -> conexion, "BR");
		
		//query de insercion de despachos vehiculos
		$query = "INSERT INTO ".BASE_DATOS.".tab_despac_vehige
					(
						num_despac, cod_transp, cod_agenci, 
						cod_rutasx, cod_conduc, num_placax, 
						num_trayle, obs_medcom, ind_activo, 
						usr_creaci, fec_creaci
					)
					VALUES 
					(
						'$nuevo_consec', '$_REQUEST[transp]', '$_REQUEST[agencia]', 
						'$_REQUEST[ruta]', '$_REQUEST[cod_conduc]', '$_REQUEST[placa]', 
						".$_REQUEST[l_trayle].", '$_REQUEST[medcom]', 'R',
						'$_REQUEST[usuario]', '$fec_actual' 
					)";
		
		$consulta = new Consulta( $query, $this -> conexion, "R" );
		
		if( $_REQUEST[manredes] )
		{
			$docrem = $_REQUEST[docrem];
			$obsrem = $_REQUEST[obsrem];
			$nomrem = $_REQUEST[nomrem];
			$ciurem = $_REQUEST[ciurem];
			$dirrem = $_REQUEST[dirrem];
			$pesrem = $_REQUEST[pesrem];
			$remsel = $_REQUEST[remsel];
			$refrem = $_REQUEST[refrem];
			$pedrem = $_REQUEST[pedrem];
			$contel = $_REQUEST[contel];
			$conlon = $_REQUEST[conlon];
			$conlat = $_REQUEST[conlat];     
			$codfle = $_REQUEST[codfle];
			
			if( sizeof( $remsel ) )
			{
				for( $i = 0; $i < $_REQUEST[maxrem]; $i++ )
				{
					eval("\$existvalucod = \$_REQUEST[codrem".$i."];");
					
					if(!$existvalucod)
						$existvalucod = "n";
					
					$query = "SELECT MAX( a.cod_remdes )
							  FROM ".BASE_DATOS.".tab_genera_remdes a ";
					
					$consulta = new Consulta($query, $this -> conexion);
					$consecut = $consulta -> ret_matriz();
					$consecut[0][0]++;
					
					$query = "SELECT a.cod_paisxx,a.cod_depart
							  FROM ".BASE_DATOS.".tab_genera_ciudad a
							  WHERE a.cod_ciudad = ".$ciurem[$i]." ";
					
					$consulta = new Consulta($query, $this -> conexion);
					$paisdept = $consulta -> ret_matriz();
					
					if(!$obsrem[$i])	
						$obsrem[$i] = "NULL";
					else
						$obsrem[$i] = "'".$obsrem[$i]."'";
					
					if(!$refrem[$i])
						$refrem[$i] = "NULL";
					else
						$refrem[$i] = "'".$refrem[$i]."'";
					
					if(!$pedrem[$i])
						$pedrem[$i] = "NULL";
					else
						$pedrem[$i] = "'".$pedrem[$i]."'";
					
					if(!$pesrem[$i])
						$pesrem[$i] = "NULL";					
					else
						$pesrem[$i] = "'".$pesrem[$i]."'";
					
					if(!$dirrem[$i])
						$dirrem[$i] = "NULL";
					else
						$dirrem[$i] = "'".$dirrem[$i]."'";
					
					if($ciurem[$i])
					{
						$query = "SELECT cod_paisxx, cod_depart
								  FROM ".BASE_DATOS.".tab_genera_ciudad
								  WHERE cod_ciudad = ".$ciurem[$i]." ";
						
						$consulta = new Consulta($query, $this -> conexion);
						$ciudadrem = $consulta -> ret_matriz();
					}
					
					if($remsel[$i] && $existvalucod == "n")
					{
						$query = "INSERT INTO ".BASE_DATOS.".tab_genera_remdes
									(
										cod_remdes, num_remdes, nom_remdes, 
										obs_adicio, cod_transp, ind_remdes,
										ind_estado, usr_creaci, fec_creaci
									)
									VALUES
									(
										".$consecut[0][0].", '".$docrem[$i]."', '".$nomrem[$i]."', 
										".$obsrem[$i].", '".$_REQUEST[transp]."', '2', 
										'".COD_ESTADO_ACTIVO."', '".$_REQUEST[usuario]."', '".$fec_actual."' 
									)";
						
						$coddes = $consecut[0][0];
					}
					else if($remsel[$i] && $existvalucod != "n")
					{
						$query = "UPDATE ".BASE_DATOS.".tab_genera_remdes
								  SET num_remdes = '".$docrem[$i]."',
									  nom_remdes = '".$nomrem[$i]."',
									  obs_adicio = ".$obsrem[$i].",
									  usr_modifi = '".$_REQUEST[usuario]."',
									  fec_modifi = '".$fec_actual."'
								  WHERE cod_remdes = ".$existvalucod." ";
						
						$coddes = $existvalucod;
					}
					
					$consulta = new Consulta($query, $this -> conexion, "R");
					
					$query = "SELECT MAX(a.cod_contro)
							  FROM ".BASE_DATOS.".tab_genera_contro a
							  WHERE cod_contro != ".CONS_CODIGO_PCLLEG." ";
					
					$consulta = new Consulta($query, $this -> conexion);
					$consecut_con = $consulta -> ret_matriz();
					$consecut_con[0][0]++;
					
					if($_REQUEST[desurb] && ($remsel[$i] && $existvalucod == "n"))
					{
						$query = "INSERT INTO ".BASE_DATOS.".tab_genera_contro
									(
										cod_contro, nom_contro, cod_ciudad, 
										nom_encarg, dir_contro, tel_contro, 
										val_longit, val_latitu, ind_urbano,
										usr_creaci, fec_creaci
									)
									VALUES 
									(
										".$consecut_con[0][0].", '".$nomrem[$i]."', ".$ciurem[$i].", 
										'".$nomrem[$i]."', ".$dirrem[$i].", '".$contel[$i]."', 
										'".$conlon[$i]."', '".$conlat[$i]."', '".COD_ESTADO_ACTIVO."', 
										'".$_REQUEST[usuario]."', '".$fec_actual."' 
									)";
						
						$consulta = new Consulta($query, $this -> conexion,"R");
						
						$query = "INSERT INTO ".BASE_DATOS.".tab_destin_contro
									(
										cod_remdes, cod_contro 
									)
									VALUES 
									(
										".$coddes.", ".$consecut_con[0][0]." 
									)";
						
						$consulta = new Consulta($query, $this -> conexion,"R");
					}
					else
					{
						$query = "SELECT cod_contro
								  FROM ".BASE_DATOS.".tab_destin_contro
								  WHERE cod_remdes = ".$coddes." ";
						
						$consulta = new Consulta($query, $this -> conexion);
						$codcondes = $consulta -> ret_matriz();
						
						if($codcondes)
						{
							$query = "UPDATE ".BASE_DATOS.".tab_genera_contro
									  SET nom_contro = '".$nomrem[$i]."',
										  cod_ciudad = ".$ciurem[$i].",
										  nom_encarg = '".$nomrem[$i]."',
										  dir_contro = ".$dirrem[$i].",
										  tel_contro = '".$contel[$i]."',
										  val_longit = '".$conlon[$i]."',
										  val_latitu = '".$conlat[$i]."',
										  usr_modifi = '".$_REQUEST[usuario]."',
										  fec_modifi = '".$fec_actual."'
										  WHERE cod_contro = ".$codcondes[0][0]." ";
							
							$consulta = new Consulta($query, $this -> conexion,"R");
							
							$query = "INSERT INTO ".BASE_DATOS.".tab_destin_contro
										( cod_remdes, cod_contro )
									  VALUES 
										( ".$coddes.", ".$codcondes[0][0]." )";
							
							$consulta = new Consulta($query, $this -> conexion,"R");
						}
						else
						{
							$query = "INSERT INTO ".BASE_DATOS.".tab_genera_contro
										(
											cod_contro, nom_contro, cod_ciudad, 
											nom_encarg, dir_contro, tel_contro, 
											val_longit, val_latitu, ind_urbano, 
											usr_creaci, fec_creaci
										)
										VALUES 
										(
											".$consecut_con[0][0].", '".$nomrem[$i]."', ".$ciurem[$i].", 
											'".$nomrem[$i]."', ".$dirrem[$i].", '".$contel[$i]."', 
											'".$conlon[$i]."','".$conlat[$i]."', '".COD_ESTADO_ACTIVO."', 
											'".$_REQUEST[usuario]."','".$fec_actual."' 
										)";
							
							$consulta = new Consulta($query, $this -> conexion,"R");
							
							$query = "INSERT INTO ".BASE_DATOS.".tab_destin_contro
										(cod_remdes,cod_contro)
									  VALUES ( ".$coddes.", ".$consecut_con[0][0]." )";
							
							$consulta = new Consulta($query, $this -> conexion,"R");
						}
					}
				
					if( $remsel[$i] )
					{
						$query = "INSERT INTO ".BASE_DATOS.".tab_despac_remdes
									(
										num_despac, cod_remdes, num_docume, 
										num_pedido, val_pesoxx, cod_paisxx, 
										cod_depart, cod_ciudad, dir_destin, 
										obs_adicio, cod_tabfle 
									)
									VALUES
									(
										".$nuevo_consec.", ".$coddes.", ".$refrem[$i].", 
										".$pedrem[$i].", ".$pesrem[$i].", ".$paisdept[0][0].", 
										".$paisdept[0][1].", ".$ciurem[$i].", ".$dirrem[$i].", 
										".$obsrem[$i].", ".$codfle[$i]." 
									)";
						
						$consulta = new Consulta($query, $this -> conexion, "R");
					}
				}
			}
		}


    # Agrega los datos de viaje en caso de que exista
    if( $_REQUEST["viaje"] )
    {
      $mInsert = "INSERT INTO  ".BASE_DATOS.".tab_despac_viajex 
                  ( num_despac, num_placax, num_viajex, cod_transp, usr_creaci, fec_creaci ) 
                  VALUES 
                  ('{$nuevo_consec}', '{$_REQUEST[placa]}', '{$_POST[viaje]}', '{$_REQUEST[transp]}','{$_REQUEST['usuario']}', NOW() ) ";
      $consulta = new Consulta($mInsert, $this -> conexion, "R");
    }



		
		if($insercion = new Consulta("COMMIT", $this -> conexion))
		{
			$link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">Insertar Otro Despacho</a></b>";
			
			$mensaje =  "El Despacho # <b>".$nuevo_consec."</b> Se Inserto con Exito".$link_a;
			$mens = new mensajes();
			$mens -> correcto("INSERTAR DESPACHOS",$mensaje);
		}
	}
}

  $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>
