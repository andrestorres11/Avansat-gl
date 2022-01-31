<?php
class TarTransp
{
    var $conexion;

    function __construct($co, $us, $ca)
 		{
			  $this -> conexion = $co;
			  $this -> usuario = $us;
			  $this -> cod_aplica = $ca;
        switch($_POST[opcion])
        {
            case "1":
            {
              $this->formulario();
            }
            break;
            case "2":
				      $this ->insert();
            break;
            default:
            {
                $this->formulario();
            }
            break;
        }
    }

		function moneyToDouble($valor=NULL){
   		 return  str_replace( ',', NULL, $valor );
  	}


    function formulario()
    {
        $tipos[0][0] = "";
        $tipos[0][1] = "-";
				$tipos[1][0] = 'N';
        $tipos[1][1] = "Novedades";
				$tipos[2][0] = 'D';
        $tipos[2][1] = "Despachos";
				$inicio[0][0] = "";
        $inicio[0][1] = "-";
				
				
				$query= "SELECT a.cod_tercer, a.abr_tercer
           			 FROM ".BASE_DATOS.".tab_tercer_tercer a,
								 			".BASE_DATOS.".tab_tercer_activi b
					 			 WHERE a.cod_tercer = b.cod_tercer  AND
								 			 b.cod_activi =1 ";
  			$consulta = new Consulta($query, $this -> conexion);
				$transp = $consulta -> ret_matriz();
				$transp = array_merge($inicio, $transp);
				
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/tarifa.js\"></script>\n";
				echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
				echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
				echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
				echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
				echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
				echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
				echo '
					<script>
						jQuery(function($) { 
						$( "#feciniID,#fecfinID" ).datepicker();      
						$.mask.definitions["A"]="[12]";
						$.mask.definitions["M"]="[01]";
						$.mask.definitions["D"]="[0123]";
						$.mask.definitions["n"]="[0123456789]";
						$( "#feciniID,#fecfinID" ).mask("Annn-Mn-Dn");
		    	})</script>';
				$formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "INSERTAR TARIFA POR TRANSPORTADORA", "form_insert\" id=\"formularioID");
			
        $formulario -> nueva_tabla();
        $formulario -> lista( "Tipo de Cobro:","tip_tarifa\" id=\"tip_tarifaID\" onChange=\"valTipo()",$tipos,0);
				$formulario -> lista( "Transportadora:","cod_tercer\" id=\"cod_tercerID\" onChange=\"valTipo()",$transp,1);
				if($_REQUEST['tip_tarifa']=='D'){
					$query= "SELECT cod_tarifa, val_minimo, fec_inifac,
													fec_finfac
             			 FROM ".BASE_DATOS.".tab_transp_tarifa
						 			 WHERE cod_tercer = '".$_REQUEST['cod_tercer']."' AND
									 			 ind_estado = 1 AND
												 tip_tarifa = 'D'";
    			$consulta = new Consulta($query, $this -> conexion);
					$val_minim = $consulta -> ret_matriz();
					if(!$val_minim){
						$query= "SELECT cod_tarifa, val_minimo, fec_inifac,
														fec_finfac
	             			 FROM ".BASE_DATOS.".tab_genera_tarifa
							 			 WHERE ind_estado =1 AND
													 tip_tarifa = 'D'";
	    			$consulta = new Consulta($query, $this -> conexion);
						$val_minim = $consulta -> ret_matriz();
						$query= "SELECT val_tarifa
	             			 FROM ".BASE_DATOS.".tab_detall_tarifa
							 			 WHERE cod_tarifa= '".$val_minim[0][0]."' ";
	    			$consulta = new Consulta($query, $this -> conexion);
						$can_minim = $consulta -> ret_matriz();
					}else{
						$query= "SELECT val_tarifa
	             			 FROM ".BASE_DATOS.".tab_transp_tardell
							 			 WHERE cod_tarifa= '".$val_minim[0][0]."' ";
	    			$consulta = new Consulta($query, $this -> conexion);
						$can_minim = $consulta -> ret_matriz();
					}
					$formulario -> nueva_tabla();
					$formulario -> texto ("Valor Minimo","text","val_minim\" onchange=\"BlurNumeric(this);\" onkeypress=\"return EvalMoney( event, this,',' );\" id=\"val_minimID",0,9,11,"", $val_minim[0][1]);
					$formulario -> texto ("Valor Despacho","text","val_tarifa\" onchange=\"BlurNumeric(this);\" onkeypress=\"return EvalMoney( event, this,',' );\" id=\"val_tarifaID",1,9,11,"",$can_minim[0][0] );
					$formulario -> texto ("Fecha Inicial de Facturacion","text","fec_inifac\" id=\"feciniID",0,9,9,"", $val_minim[0][2]);
					$formulario -> texto ("Fecha Final de Facturacion","text","fec_finfac\" id=\"fecfinID",0,9,9,"",$val_minim[0][3]);
				}
				if($_REQUEST['tip_tarifa']=='N'){
					$query= "SELECT cod_tarifa, val_minimo, fec_inifac,
														fec_finfac
             			 FROM ".BASE_DATOS.".tab_transp_tarifa
						 			 WHERE cod_tercer = '".$_REQUEST['cod_tercer']."' AND
									 			 ind_estado =1 AND
												 tip_tarifa = 'N'";
    			$consulta = new Consulta($query, $this -> conexion);
					$val_minim = $consulta -> ret_matriz();
					if(!$val_minim){
						$query= "SELECT cod_tarifa, val_minimo, fec_inifac,
														fec_finfac
	             			 FROM ".BASE_DATOS.".tab_genera_tarifa
							 			 WHERE ind_estado = 1 AND
													 tip_tarifa = 'N'";
	    			$consulta = new Consulta($query, $this -> conexion);
						$val_minim = $consulta -> ret_matriz();
						$query= "SELECT can_minimo, can_maximo, val_tarifa
	             			 FROM  ".BASE_DATOS.".tab_detall_tarifa
							 			 WHERE cod_tarifa= '".$val_minim[0][0]."'";
	    			$consulta = new Consulta($query, $this -> conexion);
						$detall = $consulta -> ret_matriz();
					}else{
						$query= "SELECT can_minimo, can_maximo, val_tarifa
	             			 FROM  ".BASE_DATOS.".tab_transp_tardell
							 			 WHERE cod_tarifa= '".$val_minim[0][0]."'";
	    			$consulta = new Consulta($query, $this -> conexion);
						$detall = $consulta -> ret_matriz();
					}
					$formulario -> nueva_tabla();
					$formulario -> texto ("Valor Minimo","text","val_minim\" onchange=\"BlurNumeric(this);\" onkeypress=\"return EvalMoney( event, this,',' );\" id=\"val_minimID",1,9,11,"", number_format($val_minim[0][1]));
					$formulario -> texto ("Fecha Inicial de Facturacion","text","fec_inifac\" id=\"feciniID",0,9,9,"", $val_minim[0][2]);
					$formulario -> texto ("Fecha Final de Facturacion","text","fec_finfac\" id=\"fecfinID",0,9,9,"",$val_minim[0][3]);
					$formulario -> nueva_tabla();
					
					echo '<td align="center" colspan="0" style="padding:4px;" class="celda_etiqueta">
									Cantidad Minima</td>';
					echo '<td align="center" colspan="0" style="padding:4px;" class="celda_etiqueta">
									Cantidad Maxima</td>';
					echo '<td align="center" colspan="0" style="padding:4px;" class="celda_etiqueta">
									Valor
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';								
					echo "</tr>";
					$j=0;
					$formulario -> nueva_tabla();
					echo '<tr ><td width="100%"><div id="AplicationEndDIV" width="100%"></div>
              <div id="RyuCalendarDIV" width="100%"></div>
              <div id="popupDIV" width="100%">

    		  <div id="filtros" width="100%">
					
    		  </div><div id="tarifasID" width="100%">';
					if($detall){
						for( $i=0; $i < sizeof( $detall ); $i++ )
						{	
							echo 	"<table id='tarifas".$i."ID' width='100%'>";
							//echo "<td class='celda_etiqueta' style='padding:4px;'   colspan='0' > </td>";		
							echo "<td align='center'   class='celda_info' style='padding:4px;'   colspan='0' >
											<input class='campo_texto' readonly='true' value=".$detall[$i][0]." onBlur=\"BlurNumeric(this);\" type='text' size='5' maxlength='5' 
							 				name='can_minimo[]'    /></td>";
							echo "<td align='center' class='celda_info'  style='padding:4px;'   colspan='0' >
											<input class='campo_texto' readonly='true' value=".$detall[$i][1]." onBlur=\"BlurNumeric(this);\" type='text' size='5' maxlength='5' 
							 				name='can_maximo[]'   /></td>";
							echo "<td align='center' class='celda_info'  style='padding:4px;'   colspan='0' >
											<input class='campo_texto' readonly='true' type='text' value=".number_format($detall[$i][2])." onBlur=\"BlurNumeric(this);\" onkeypress=\"return EvalMoney( event, this,',' );\" size='10' maxlength='10' 
							 				name='val_tarifa[]'   /></td>";								
							echo "</tr></table>";
							
						}
						$j= sizeof( $detall )-1;
					}
					else{
						
						echo 	"<table id='tarifas0ID' width='100%'>";
						//echo "<td class='celda_etiqueta' style='padding:4px;'   colspan='0' > </td>";		
						echo "<td align='center'  class='celda_info' style='padding:4px;'   colspan='0' >
										<input class='campo_texto' onBlur=\"BlurNumeric(this);\" type='text' size='5' maxlength='5' 
						 				name='can_minimo[]'    /></td>";
						echo "<td align='center' class='celda_info'  style='padding:4px;'   colspan='0' >
										<input class='campo_texto' onBlur=\"BlurNumeric(this);\" type='text' size='5' maxlength='5' 
						 				name='can_maximo[]'   /></td>";
						echo "<td align='center' class='celda_info'  style='padding:4px;'   colspan='0' >
										<input class='campo_texto' type='text' onBlur=\"BlurNumeric(this);\" onkeypress=\"return EvalMoney( event, this,',' );\" size='10' maxlength='10' 
						 				name='val_tarifa[]'   /></td>";								
						echo "</tr></table>";
					}
					echo "</div></div><div id='alg'> <table></table></div></td></tr>";
					echo "<table>
									<tr>
									  <td>
											<input type='button' value='Agregar' name='Agregar' onclick='makeTable()' class='crmButton small save'>
										</td>
										<td>
											<input type='button' value='Eliminar' name='Eliminar' onclick='deleteTable()' class='crmButton small save'>
										</td>
									</tr>";
				}
				$formulario -> nueva_tabla();
				$formulario -> boton("Aceptar","button\" onClick=\"valTarifa()",0);
        $formulario -> oculto("url_archiv\" id=\"url_archivID\"","ins_client_emailx.php",0);
		    $formulario -> oculto("dir_aplica\" id=\"dir_aplicaID\"",DIR_APLICA_CENTRAL,0);
				$formulario -> oculto("maxtarifa\" id=\"maxtarifaID",$j,0);
        $formulario -> oculto("num_serie",0,0);
        $formulario -> oculto("window","central",0);
        $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
        $formulario -> oculto("opcion\" id=\"opcionID",1,0);
        $formulario -> cerrar();
        echo '
					<script>
			 			document.getElementById("tip_tarifaID").value="'.$_REQUEST["tip_tarifa"].'";
						document.getElementById("cod_tercerID").value="'.$_REQUEST["cod_tercer"].'";	
       		</script>';
    }

  
	

    function insert()
	  {
	  	if($_POST['tip_tarifa']=='D'){
	  		$datos_usuario = $this -> usuario -> retornar();
	  		$query =  "UPDATE ".BASE_DATOS.".tab_transp_tarifa
                   	SET	ind_estado = 0,
												fec_modifi = NOW(),
												usr_modifi = '".$datos_usuario['cod_usuari']."'
									 WHERE cod_tercer = '".$_REQUEST['cod_tercer']."'";
	      $insercion = new Consulta($query, $this -> conexion,"BR");
				$query= "SELECT MAX(cod_tarifa)
             		 FROM ".BASE_DATOS.".tab_transp_tarifa";
    		$consulta = new Consulta($query, $this -> conexion);
				$max = $consulta -> ret_matriz();
				$max = $max[0][0]+1;
				$query =  "INSERT INTO ".BASE_DATOS.".tab_transp_tarifa(
	                      cod_tarifa, cod_tercer, tip_tarifa,
												val_minimo, ind_estado, usr_creaci,
												fec_creaci, fec_inifac, fec_finfac)
	                 VALUES('".$max."', '".$_REQUEST['cod_tercer']."', 'D', 
									 				'".$this -> moneyToDouble($_POST['val_minim'])."', 1,'".$datos_usuario['cod_usuari']."',
													NOW(), '".$_REQUEST['fec_inifac']."', '".$_REQUEST['fec_finfac']."')";
	      $insercion = new Consulta($query, $this -> conexion,"R");
				$query= "SELECT MAX(cod_consec)
             		 FROM ".BASE_DATOS.".tab_transp_tardell";
    		$consulta = new Consulta($query, $this -> conexion);
				$consec = $consulta -> ret_matriz();
				$consec = $consec[0][0]+1;
				$query =  "INSERT INTO ".BASE_DATOS.".tab_transp_tardell(
	                      cod_tarifa, cod_consec, val_tarifa)
	                 VALUES('".$max."', '".$consec."', '".$this -> moneyToDouble($_POST['val_tarifa'])."')";
	      $insercion = new Consulta($query, $this -> conexion,"R");
				if($insercion = new Consulta("COMMIT", $this -> conexion)){
		     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">INSERTAR OTRA TARIFA POR TRANSPORTADORA</a></b>";
		
		     $mensaje =  " Se Inserto con Exito".$link_a;
		     $mens = new mensajes();
		     $mens -> correcto("INSERTAR TARIFA POR TRANSPORTADORA",$mensaje);
				}
	  	}
			if($_POST['tip_tarifa']=='N'){
				$can_minimo = $_POST['can_minimo'];
				$can_maximo = $_POST['can_maximo'];
				$val_tarifa = $_POST['val_tarifa'];
	  		$datos_usuario = $this -> usuario -> retornar();
	  		$query =  "UPDATE ".BASE_DATOS.".tab_transp_tarifa
                   	SET	ind_estado = 0,
												fec_modifi = NOW(),
												usr_modifi = '".$datos_usuario['cod_usuari']."'
									 WHERE cod_tercer = '".$_REQUEST['cod_tercer']."'";
	      $insercion = new Consulta($query, $this -> conexion,"BR");
				$query= "SELECT MAX(cod_tarifa)
             FROM ".BASE_DATOS.".tab_transp_tarifa";
    		$consulta = new Consulta($query, $this -> conexion);
				$max = $consulta -> ret_matriz();
				$max = $max[0][0]+1;
				$query =  "INSERT INTO ".BASE_DATOS.".tab_transp_tarifa(
	                      cod_tarifa, cod_tercer, tip_tarifa, 
												val_minimo, ind_estado, usr_creaci,
												fec_creaci, fec_inifac, fec_finfac)
	                 VALUES('".$max."', '".$_REQUEST['cod_tercer']."', 'N',
									 			  '".$this -> moneyToDouble($_POST['val_minim'])."', 1,'".$datos_usuario['cod_usuari']."',
													NOW(), '".$_REQUEST['fec_inifac']."', '".$_REQUEST['fec_finfac']."')";
	      $insercion = new Consulta($query, $this -> conexion,"R");
				$query= "SELECT MAX(cod_consec)
             		 FROM ".BASE_DATOS.".tab_transp_tardell";
    		$consulta = new Consulta($query, $this -> conexion);
				$consec = $consulta -> ret_matriz();
				$consec = $consec[0][0];
				for( $i = 0; $i < count( $can_minimo ); $i++ ){
					$consec ++;
					$query =  "INSERT INTO ".BASE_DATOS.".tab_transp_tardell(
	                      cod_tarifa, cod_consec, can_minimo, 
												can_maximo, val_tarifa)
	                 	 VALUES('".$max."', '".$consec."', '".$can_minimo[$i]."',
										 				'".$can_maximo[$i]."','".$this -> moneyToDouble($val_tarifa[$i])."')";
	      	$insercion = new Consulta($query, $this -> conexion,"R");
				}
				
				if($insercion = new Consulta("COMMIT", $this -> conexion)){
		     $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=".$_REQUEST[cod_servic]." \"target=\"centralFrame\">INSERTAR OTRA TARIFA POR TRANSPORTADORA</a></b>";
		
		     $mensaje =  " Se Inserto con Exito".$link_a;
		     $mens = new mensajes();
		     $mens -> correcto("INSERTAR TARIFA POR TRANSPORTADORA",$mensaje);
				}
	  	}
	  }



}
$service = new TarTransp($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?> 
