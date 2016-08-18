<?php

class AjaxConsolViajes
{
  var $conexion;
  
  public function __construct()
  {
    $_AJAX = $_REQUEST;
    include_once('../lib/ajax.inc');

    $this -> conexion = $AjaxConnection;
    $this -> $_AJAX['option']( $_AJAX );
  }

  public function IMPMAT( $parametros , $die = null )
  {

  	echo '<pre>';
  		print_r( $parametros );
  	echo '</pre>';

  	if( $die )
  	{
  		die();
  	}//Fin If die

  }//FIN FUNCION IMPMAT  

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
        
        .cellInfoOther
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #FFFFFF;
          padding: 2px;
          border: 1px solid #EEEEEE;
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
            background-color: rgb(240, 240, 240);
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 99%; 
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
  
  private function JqueryProperty()
  {
  
	echo "<script>
			  $(function() {
				$( \"button\" )
				  .button()
				  .click(function( event ) {
					event.preventDefault();
				  });
			  });			  
			  
		  </script>";		  
  
  }//Fin Funcion JqueryProperty
  
  protected function ConsolidarViajes( $_AJAX )
  {

    $this -> Style();
    $this -> JqueryProperty();
	
	$viajes = $this -> getConsolViajes( $_AJAX['despacho']	);

	//---------------------------------------------------------
	// Sacando el nit de la transportadora de los viajes
	//---------------------------------------------------------		  
	$nit_transp = $this -> getDatosDespac( $_AJAX['despacho'] );
	$nit_transp = $nit_transp['cod_transp'];
	//------------------------------------------------------
	// Se establecen los buttonset para las consolidaciones
	//------------------------------------------------------
	for( $i = 0 ; $i < sizeof( $viajes ) ; $i++ )
	{
		$class .= '$( ".radio'.$i.'" ).buttonset(); ';
	}//Fin for clases de los buttonset
		
	//------------------------------------------------------
	$html = '<script>
			  $(function() {
				'.$class.'
			  });
		  </script>';					  
		  
	echo $html;		  
	//------------------------------------------------------

	$html = '<form name="formConsolViajes" id="formConsolViajesId">
			 <div class="StyleDIV" id="FormDivID">
			 <table border="1" cellpadding="3" cellspacing="0" align="center" style="border:none">
			 
				<tr>
					<td colspan="8" class="CellHead">Se encontraron viajes disponibles para realizar consolidaci&oacute;n con la placa <b>'.$viajes[0]['num_placax'].'</b></td>
				</tr>
			 
				<tr>
					<td class="CellHead" align="center">Consecutivo</td>
					<td class="CellHead" align="center">N&uacute;mero Viaje Corona</td>
					<td class="CellHead" align="center">Tipo Despacho</td>
					<td class="CellHead" align="center">Nombre Conductor</td>
					<td class="CellHead" align="center">Ciudad Origen</td>
					<td class="CellHead" align="center">Ciudad Destino</td>
					<td class="CellHead" align="center">Fecha Corona</td>
					<td class="CellHead" align="center">Consolidaci&oacute;n</td>
				</tr>';
				
	for( $i = 0, $j = 0; $i < sizeof( $viajes ) ; $i++ )
	{
		//if($this -> verifyNoveda($viajes[$i]['num_despac']) == true){

			$check1 = $viajes[$i]['ind_consol'] == 1 ? 'checked="checked"' : NULL ;
			$check2 = $viajes[$i]['ind_consol'] == 0 ? 'checked="checked"' : NULL ;

		    $url = 'index.php?ind_consol=1&cod_servic=3302&window=central&despac='.$viajes[$i]['num_despac'].'&opcion=1';
			$link = '<a href="'.$url.'" target="_blank" style="color: 000000">'.$viajes[$i]['num_viacor'].'</a>';

			$html .= '<tr>
						<th class="cellInfo" align="center">'.($j+1).'</td>
						<td class="cellInfo" align="center">'.$link.'</td>
						<td class="cellInfo" align="center">'.$viajes[$i]['nom_tipdes'].'</td>
						<td class="cellInfo" align="center">'.$viajes[$i]['nom_conduc'].'</td>
						<td class="cellInfo" align="center">'.$viajes[$i]['ciu_origen'].'</td>
						<td class="cellInfo" align="center">'.$viajes[$i]['ciu_destin'].'</td>
						<td class="cellInfo" align="center">'.$viajes[$i]['fec_descor'].'</td>
						<td class="cellInfo" align="center">
						  <div class="radio'.$i.'">
							<input type="radio" id="radio'.$i.'1" name="radio'.$i.'" '.$check1.'><label for="radio'.$i.'1">Si</label>
							<input type="radio" id="radio'.$i.'2" name="radio'.$i.'" '.$check2.'><label for="radio'.$i.'2">No</label>
							<input type="hidden" name="viaje'.$i.'" id="viaje'.$i.'" value="'.$viajes[$i]['num_despac']	.'">
						  </div>
						</td>
					</tr>';	
		$j++;
				
		//}
	}//Fin For muestra los datos de los viajes



			$html .= '<tr style="border: hidden">';
				$html .= '<td colspan="7" style="border: hidden"></td>';					
			$html .= '</tr>';
		
			$html .= '<tr style="border: hidden">';
				$html .= '<td style="border: hidden" colspan="7" align="right">';		
					$html .= '<button onclick="validarConsolidacionViajes();">Consolidar Viajes</button>';			
					$html .= '<button onclick="cerrarDialog();">Cerrar</button>';			
				$html .= '</td>';
			$html .= '</tr>';	
	
		$html .= '</table>
				  <input type="hidden" name="numViajes" id="numViajesId" value="'.sizeof( $viajes ).'">
				  <input type="hidden" name="numPlacax" id="numPlacaxId" value="'.$viajes[0]['num_placax'].'">
				  <input type="hidden" name="nitTransp" id="nitTranspId" value="'.$nit_transp.'">';
				  
		$html .= '</div>';
		
	$html .= '</form>';
 
	echo $html;
      
  }

  private function getDatosDespac( $despacho )
  {  
		//-------------------------------------------------------------
		// Sacando el nit de la transportadora y la placa del vehiculo
		// para ese despacho
		//-------------------------------------------------------------
		$sql = "SELECT 
					a.cod_transp , a.num_placax
				FROM
					".BASE_DATOS.".tab_despac_vehige a
				WHERE 
					a.num_despac = '".$despacho."' ";

        $consulta = new Consulta( $sql , $this -> conexion );
        $dataDespac = $consulta->ret_matriz();
		$dataDespac = $dataDespac[0];
		//---------------------------------------------------------

		return $dataDespac;
  }//Fin Funcion getDatosDespac
  
  private function getConsolViajes( $despacho )
  {  
		//---------------------------------------------------------
		// Sacando la placa para comparar viajes similares
		//---------------------------------------------------------		  
		$inf_despac = $this -> getDatosDespac( $despacho );
		$placax = $inf_despac['num_placax']; 
  
		//---------------------------------------------------------
		// Verificando si existen mas viajes para consolidar
		//---------------------------------------------------------	
	  $sql = "SELECT 
						c.num_desext AS num_viacor ,
						a.num_despac AS num_despac ,
						d.nom_ciudad AS ciu_origen ,
						e.nom_ciudad AS ciu_destin ,
						b.fec_salsis AS fec_descor ,
						IF( a.ind_consol = NULL OR a.num_despac = '".$despacho."' , '1' , '0'  ) AS ind_consol,
						a.num_placax AS num_placax , 
						f.abr_tercer AS nom_conduc ,
						g.nom_tipdes AS nom_tipdes
				FROM
					    ".BASE_DATOS.".tab_despac_despac b,
					    ".BASE_DATOS.".tab_despac_sisext c,
					    ".BASE_DATOS.".tab_genera_ciudad d,
					    ".BASE_DATOS.".tab_genera_ciudad e, 
					    ".BASE_DATOS.".tab_tercer_tercer f, 
					    ".BASE_DATOS.".tab_genera_tipdes g,
					    ".BASE_DATOS.".tab_despac_vehige a 

			   WHERE 
					 a.num_despac = b.num_despac
					 AND c.num_despac = a.num_despac
					 AND b.cod_ciuori = d.cod_ciudad
					 AND b.cod_ciudes = e.cod_ciudad
					 AND a.cod_conduc = f.cod_tercer
					 AND b.cod_tipdes = g.cod_tipdes
					 AND b.fec_salida IS NOT NULL
					 AND b.fec_llegad IS NULL
					 AND b.ind_anulad = 'R' 
					 AND a.ind_activo = 'S'
					 AND b.ind_planru = 'S'
					 AND a.num_placax = '".$placax."' ";
					
        $consulta = new Consulta( $sql , $this -> conexion );
        $result = $consulta->ret_matriz();
		//---------------------------------------------------------  
		
		return $result;
		
  }//Fin Funcion getConsolViajes

  protected function verifyNoveda($despac){


	  	$mIndReturn = true;

	    $sql1 = "SELECT a.ind_defini 
	  	  			  FROM tab_despac_despac a
	  	  			 WHERE a.num_despac = '".$despac."'";

	    $consulta = new Consulta( $sql1 , $this -> conexion );
	    $result1 = $consulta->ret_matrix("a");
	 
	    if($result1[0][ind_defini] == 0){

		  	$contro = getControDespac($this -> conexion, $despac);

		  	$mUltPC = end($contro);
		  
		  	$sql = "SELECT a.cod_noveda 
		  	  			  FROM tab_despac_noveda a
		  	  			 WHERE a.num_despac = '".$despac."'
		  	  			   AND a.cod_contro = '".$mUltPC[cod_contro]."' 
		  			 ";

		    $consulta = new Consulta( $sql , $this -> conexion );
		    $result = $consulta->ret_matrix("a");
	 
		    if(isset($result[0][cod_noveda])){
		    	$mIndReturn = false;
		    }
		    
	    }
	    else{
	    	$mIndReturn = false;
	    }
	 
		return $mIndReturn;
  }// Fin funcion verifyNoveda
  
  protected function ConsolidarViajesAutomatico( $_AJAX )
  {
  	$this -> Style();
  	$this -> JqueryProperty();

	if( $this -> verifyFechasViajes($_AJAX['viajes']) )
	{
		//Sacando el origen de la ruta que se utilizara
		$origen = $this -> getConsolManualOriDes( $_AJAX['viajes'] , 'origen', '1' );
		//Sacando el destino de la ruta que se utilizara
		$destin = $this -> getConsolManualOriDes( $_AJAX['viajes'] , 'destino', '1' );
		//Sacando la ruta que se utilizara
		$rutasx = $this -> getRutasConsol( $origen[0] , $destin[0] , $_AJAX['nitTransp'] , 'a' );

		if( sizeof($rutasx) > 0  )
		{

			$_AJAX['tip_consol'] = 'automatica';
			$_AJAX['cod_ruta'] = $rutasx['cod_rutasx'];
			$this -> consolGeneral( $_AJAX );

		}
		else
		{
			$msg = 'Actualmente no hay rutas disponibles para este origen ("'.$this -> getNomCiudad( $origen[0] ).'") y destino ("'.$this -> getNomCiudad( $destin[0] ).'"), debe crearlas por la opcion '
				   .'Rutas y P. Control >> Insertar o asignarla a la transportadora por la opcion Rutas y P. Control >> Rutas y Transportadoras >> Asignar';
				   
			$html = '<form name="formConsolManual" id="formConsolManualId">
					 <div class="StyleDIV" id="FormDivID">
					 <table border="1" cellpadding="3" cellspacing="0" align="center" style="border:none">

						<tr>
							<td class="CellHead">'.$msg.'</td>
						</tr>';

					$html .= '<tr style="border: hidden">';
						$html .= '<td colspan="5" style="border: hidden"></td>';					
					$html .= '</tr>';
				
					$html .= '<tr style="border: hidden">';
						$html .= '<td style="border: hidden" colspan="5" align="right">';		
							$html .= '<button onclick="cerrarDialog();">Cerrar</button>';			
						$html .= '</td>';
					$html .= '</tr>';
				
				$html .= '</table>';
				$html .= '</div>';
				
			$html .= '</form>';
			
			echo $html;				   
				   
		}//FIN IF 


	}
	else
	{
		$this -> ConsolidarViajesManual( $_AJAX );
		die();
	}//Fin IF	
  
  }//Fin Funcion 	

  protected function countDestinDespac( $despacho )
  {
	$sql = "SELECT 
				count(num_despac) AS conteo 
			FROM  
				".BASE_DATOS.".tab_despac_destin 
			WHERE  
				num_despac IN ( ".$despacho." ) ";
  
	$consulta = new Consulta( $sql , $this -> conexion );
	$verifyDestin = $consulta->ret_vector();
	$verifyDestin = $verifyDestin[0];

	return $verifyDestin;
	
  }//Fin Funcion countDestinDespac
  
  protected function verifyFechasViajes( $despacho )
  {
	//Con este conteo se verifica si el despacho tiene o no destinatarios
	$verifyDestin = $this -> countDestinDespac( $despacho );
	
	//Con este condicional se verifica si el despacho
	//si tienes destinatarios , y sigue comprobando
	if( $verifyDestin > 0 )
	{
		//Esta consulta obteniene las fechas de los destinatarios con indicadores de validacion
		$sql = "SELECT 
					IF( fec_citdes != '0000-00-00' AND fec_citdes > '2011-01-01' , 1 , 0 ) AS fec_citdes , 
					IF( hor_citdes != '00:00:00' , 1 , 0 ) AS hor_citdes   
				FROM 
					tab_despac_destin
				WHERE 
					num_despac IN (".$despacho.") ";		
					
		$consulta = new Consulta( $sql , $this -> conexion );
		$verifyFecha = $consulta->ret_matriz();
		
		//Este for recorre cada una de las fechas y horas de las citas y las valida
		for( $i = 0 ; $i < sizeof( $verifyFecha ); $i++ )
		{
			if( $verifyFecha[$i]['fec_citdes'] <> 1 || $verifyFecha[$i]['hor_citdes'] <> 1 )
			{
				return false;
			}//Fin If 
		}//FIN FOR
		
		return true;
	}
	else
	{
		return false;
	}//Fin If 
  
  }//Fin Funcion verifyFechasViajes
  
  protected function ConsolidarViajesManual( $_AJAX )
  {
		//Estilos para los formularios
		$this -> Style();
		//Propiedades de jquery para los elementos del formulario
		$this -> JqueryProperty();
	  
		//Sacando el origen de la ruta que se utilizara
		$origen = $this -> getConsolManualOriDes( $_AJAX['viajes'] , 'origen' );
		//Sacando el destino de la ruta que se utilizara
		$destin = $this -> getConsolManualOriDes( $_AJAX['viajes'] , 'destino' );
		
		$mHtml = '<div class="StyleDIV" id="FormDivID">
					 <table width="100%" border="0" id="tablePrincipal" cellpadding="3" cellspacing="1" align="center" style="border:none">
						<tr>
							<td colspan="4" class="CellHead">Seleccione Origen y Destino del Nuevo Viaje</td>
						</tr>';
		
		$mSelectO = '<select name="cod_ciuori" id="cod_ciuoriID"><option value="">-Seleccione-</option>';
		foreach( $origen as $cod_ciuori => $nom_ciuori )
		  $mSelectO .= '<option value="'.$cod_ciuori.'">'.$nom_ciuori.'</option>';
		$mSelectO .= '</select>';
		
		$mSelectD = '<select name="cod_ciudes" id="cod_ciudesID"><option value="">-Seleccione-</option>';
		foreach( $destin as $cod_ciudes => $nom_ciudes )
		  $mSelectD .= '<option value="'.$cod_ciudes.'">'.$nom_ciudes.'</option>';
		$mSelectD .= '</select>';

		$mHtml .='<tr>';
		  $mHtml .='<td class="cellInfo1" align="center">ORIGEN:</td>';	
		  $mHtml .='<td class="cellInfo1" align="center">'.$mSelectO.'</td>';	
		  $mHtml .='<td class="cellInfo1" align="center">DESTINO:</td>';	
		  $mHtml .='<td class="cellInfo1" align="center">'.$mSelectD.'</td>';	
		$mHtml .='</tr>';
		
		$mHtml .='<tr>';
		  $mHtml .='<td class="cellInfo" colspan="4" align="center"><button onclick="findRoutes( \''.$_AJAX['nitTransp'].'\' );">Buscar Rutas</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="cerrarDialog();">Cerrar</button></td>';	
		$mHtml .='</tr>';
		
		$mHtml .='<input type="hidden" name="viajes" id="viajesID" value="'.$_AJAX['viajes'].'"/>';
		$mHtml .='<input type="hidden" name="placa" id="placaID" value="'.$_AJAX['placa'].'"/>';

		$mHtml .='</table>
				    <div id="rutasxID">
				    </div>
				  </div>';

		echo $mHtml;
	
  }

  protected function findRoutes( $mData )
  {
  	$mRutasx = $this -> getRutasConsol( $mData['cod_ciuori'], $mData['cod_ciudes'], $mData['nit_transp'] );
  	
  	$this -> JqueryProperty();

  	$mHtml  = '<table width="100%" cellspacing="1" cellpadding="0">';
  	if( sizeof( $mRutasx ) > 0 )
  	{
  	  $mHtml .= '<tr>';
  	  $mHtml .= '<td class="CellHead" align="center">Seleccione</td>';
  	  $mHtml .= '<td class="CellHead" align="center">Origen</td>';
  	  $mHtml .= '<td class="CellHead" align="center">Destino</td>';
  	  $mHtml .= '<td class="CellHead" align="center">Ruta</td>';
  	  $mHtml .= '</tr>';
  	  
  	  $counter = 0;
  	  foreach( $mRutasx as $row )
  	  {
  	  	$style = $counter % 2 == 0 ? 'cellInfo1' : 'cellInfo2';
  	  	$radio = '<input type="radio" name="codRutasx" value="'.$row['cod_rutasx'].'" />';
  	  	$mHtml .= '<tr>';
  	  	  $mHtml .= '<td class="'.$style.'" align="center">'.$radio.'</td>';
  	  	  $mHtml .= '<td class="'.$style.'" align="center">'.$row['ciu_origen'].'</td>';
  	  	  $mHtml .= '<td class="'.$style.'" align="center">'.$row['ciu_destin'].'</td>';
  	  	  $mHtml .= '<td class="'.$style.'" align="center">'.$row['nom_rutasx'].'</td>';
  	  	$mHtml .= '</tr>';	
  	  	$counter++;
  	  }
  	  $mHtml .= '<tr>';
  	  $mHtml .= '<td colspan="4" align="right">
				 <input type="hidden" name="viajesId" id="viajesId" value="'.$mData['viajes'].'" />
				 <input type="hidden" name="nitTranspId" id="nitTranspId" value="'.$mData['nit_transp'].'" />
				 <input type="hidden" name="placaId" id="placaId" value="'.$mData['placa'].'" />
  	  			 <button onclick="validarRutaConsolidacion();">Continuar</button><td>';
  	  $mHtml .= '</tr>';
  	}
  	else
  	{
      $msg = 'Actualmente no hay rutas disponibles para este origen ("'.$this -> getNomCiudad( $mData['cod_ciuori'] ) .'") y destino ("'.$this -> getNomCiudad( $mData['cod_ciudes'] ).'"), debe crearlas por la opcion '
				   .'Rutas y P. Control > Insertar o asignarla a la transportadora por la opcion Rutas y P. Control > Rutas y Transportadoras > Asignar';

  	  $mHtml .= '<tr>';
  	  $mHtml .= '<td class="CellHead">'.$msg.'</td>';
  	  $mHtml .= '</tr>';
  	}
  	
  	$mHtml .= '</table>';
  	
  	echo $mHtml;
  }
  
  protected function getConsolManualOriDes( $viajes , $tipo = NULL, $ind_unique = NULL )
  {
  
  	switch( $tipo )
  	{

  		case 'origen' :
  			$campo = 'cod_ciuori';
  			$order = 'ASC';
  		break;

  		default:
  			$campo = 'cod_ciudes';
  			$order = 'DESC';
  		break;

  	}

	$sql = "SELECT 
				" . $campo .
			" FROM
				".BASE_DATOS.".tab_despac_despac a
			WHERE
				a.num_despac IN (".$viajes.")
			ORDER BY a.fec_salsis ".$order." ";
	
	$consulta = new Consulta( $sql , $this -> conexion );
	$origen = $consulta->ret_matriz();
	$arr_return = array(); 
	
	if( $ind_unique == '1' )
	{
	  $arr_return[0] = $origen[0][$campo];
	}
	else
	{
	  foreach( $origen as $row )
	  {
	    $arr_return[$row[$campo]]  = $this -> getNomCiudad( $row[$campo] );
	  }	
	}

	return $arr_return;

  }

  protected function getNomCiudad( $cod_ciudad )
  {
    $mSelect = "SELECT b.cod_ciudad, CONCAT(b.abr_ciudad,' (',LEFT(d.abr_depart,4),') - ',LEFT(e.nom_paisxx,3))
                  FROM ".BASE_DATOS.".tab_genera_ciudad b, 
                       ".BASE_DATOS.".tab_genera_depart d,
                       ".BASE_DATOS.".tab_genera_paises e
                 WHERE b.cod_depart = d.cod_depart AND
                       b.cod_paisxx = d.cod_paisxx AND
                       d.cod_paisxx = e.cod_paisxx AND
                       b.ind_estado = '1' AND
                       b.cod_ciudad = '".$cod_ciudad."'
                 GROUP BY 1";
    
    $consulta = new Consulta( $mSelect, $this -> conexion );
    $_DESTIN = $consulta -> ret_matriz();
    return $_DESTIN[0][1];
  }
    
  //Funcion que traera la/s ruta/s para estos origenes y destinos
  //Type = Tipo de consolidacion , m = Manual , a = Automatica
  protected function getRutasConsol( $origen , $destin , $nitTransp , $type = 'm' )
  {
  
  	//Saber si es de tipo manual o automatico para devolver todos o un solo registro
  	$type = strtolower($type);

	$sql = "SELECT 
				a.cod_rutasx, a.nom_rutasx, b.nom_ciudad AS ciu_origen, 
				c.nom_ciudad AS ciu_destin 
			FROM   
				".BASE_DATOS.".tab_genera_rutasx a ,
				".BASE_DATOS.".tab_genera_ciudad b ,
				".BASE_DATOS.".tab_genera_ciudad c ,
				".BASE_DATOS.".tab_genera_ruttra d
			WHERE  
				a.cod_ciuori = b.cod_ciudad 
				AND a.cod_ciudes = c.cod_ciudad 
				AND a.cod_rutasx = d.cod_rutasx
				AND d.cod_transp = '".$nitTransp."'
				AND a.cod_ciuori = '".$origen."' 
				AND a.cod_ciudes = '".$destin."'
			GROUP BY d.cod_rutasx";

	$consulta = new Consulta( $sql , $this -> conexion );
	$rutasx = $consulta->ret_matriz();

	//Para saber si se deben devolver todas las rutas
	//o solamente la primera encontrada
	if( $type == 'a' )
	{
		$rutasx = $rutasx[0];
	}

	return $rutasx;
	
  }//FIN FUNCION getRutasConsol
  
  protected function getFechasDestin( $viajes )
  {
	$sql = "SELECT 
				a.num_despac, a.num_docume, a.num_docalt,
				a.nom_destin, a.cod_ciudad, a.dir_destin,
				a.num_destin, a.fec_citdes, a.hor_citdes,
				b.abr_tercer, c.nom_ciudad
			FROM 
				".BASE_DATOS.".tab_despac_destin a,
				".BASE_DATOS.".tab_tercer_tercer b,
				".BASE_DATOS.".tab_genera_ciudad c
			WHERE 
				(a.fec_citdes = '0000-00-00' 
				OR a.fec_citdes <= '2011-01-01'
				OR a.hor_citdes = '00:00:00')
				AND a.cod_genera = b.cod_tercer
				AND a.cod_ciudad = c.cod_ciudad
				AND a.num_despac IN (".$viajes.") ";		
				
	$consulta = new Consulta( $sql , $this -> conexion );
	$destinUpdate = $consulta->ret_matriz();
	
	return $destinUpdate;
  
  }//FIN FUNCION getFechasDestin
  
  private function getCiudad( $cod_ciudad = NULL )
  {

    $sql = "SELECT 
    			cod_ciudad, UPPER( nom_ciudad ) AS nom_ciudad
           	 FROM
           		".BASE_DATOS.".tab_genera_ciudad 
          	 WHERE 
          		ind_estado = '1'";

    if( $cod_ciudad != NULL )
    {
      $sql .= " AND cod_ciudad = ".$cod_ciudad;      
    }
    $sql .= " ORDER BY 2";
    
    $consulta = new Consulta( $sql , $this -> conexion );

	return $consulta -> ret_matriz();

  }//Fin Funcion getCiudad

  private function getGenera( $cod_transp, $cod_genera = NULL )
  {
    $query = "SELECT a.cod_tercer, UPPER(a.abr_tercer) AS nom_tercer
                FROM ".BASE_DATOS.".tab_tercer_tercer a,
                     ".BASE_DATOS.".tab_tercer_activi b,
                     ".BASE_DATOS.".tab_transp_tercer c
               WHERE a.cod_tercer = b.cod_tercer AND
                     a.cod_tercer = c.cod_tercer AND
                     c.cod_transp = '".$cod_transp."' AND
                     b.cod_activi = 1";
    if( $cod_genera != NULL )
    {
      $query .= " AND a.cod_tercer = '".$cod_genera."'";
    }
    $query .= " ORDER BY 2 ASC";
    
    $consulta = new Consulta( $query, $this -> conexion );
    return $consulta -> ret_matriz();
  }

  protected function GenerateSelect( $arr_select, $name, $key = NULL, $events = NULL, $disabled = NULL )
  {
    $mHtml  = '<select name="'.$name.'" id="'.$name.'ID" '.$events.' '.$disabled.'>';
    $mHtml .= '<option value="">- Seleccione -</option>';
    foreach( $arr_select as $row )
    {
      $selected = '';
      if( $row[0] == $key )
        $selected = 'selected="selected"';
      
      $mHtml .= '<option value="'.$row[0].'" '.$selected.'>'.utf8_encode( $row[1] ).'</option>';
    }
    $mHtml .= '</select>';
    return $mHtml;
  }

  protected function ShowDestin( $_AJAX, $mData = NULL )
  {
    
    if( $_AJAX['counter'] == '' )
    {
      $_AJAX['counter'] = 0;
    }
    
    $style = $_AJAX['counter'] % 2 == 0 ? 'cellInfo1' : 'cellInfo2' ;
    
    $ciudad = $this -> getCiudad();
    $genera = $this -> getGenera( $_AJAX['nitTransp'] );
    
    
    $mHtml  .= '
    <script>
      $(function() {
        
        $( ".date" ).datepicker({ minDate: new Date('.(date('Y')).','. (date('m')-1) .','.(date('d')).') });
        
        $( ".time" ).timepicker();
        
        $.mask.definitions["A"]="[12]";
        $.mask.definitions["M"]="[01]";
        $.mask.definitions["D"]="[0123]";

        $.mask.definitions["H"]="[012]";
        $.mask.definitions["N"]="[012345]";
        $.mask.definitions["n"]="[0123456789]";

        $( ".date" ).mask("Annn-Mn-Dn");
        $( ".time" ).mask("Hn:Nn:Nn");

      });
    </script>';
    
    $mHtml .= '<div id="datdes'.$_AJAX['counter'].'ID">';
      $mHtml .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
      $mHtml .= '<tr>';
        $mHtml .= '<td align="left" colspan="5" class="cellHead" width="10%">DESTINATARIO No. '. ( $_AJAX['counter'] + 1 );
      	$mHtml .= '&nbsp;&nbsp;&nbsp;<a style="color:#FFFFFF; text-decoration:none; cursor:pointer;" onclick="DropGrid(\''.$_AJAX['counter'].'\');">[Eliminar]</a>';
        $mHtml .= '</td>';   
      $mHtml .= '</tr>';
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead" width="10%">* No. FACTURA/ REMISION</td>';
        $mHtml .= '<td align="center" class="cellHead" width="10%">* DOC. ALTERNO</td>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">* DESTINATARIO</td>';
        
      $mHtml .= '</tr>';

      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" size="10" name="num_factur'.$_AJAX['counter'].'" id="num_factur'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" size="10" name="num_docalt'.$_AJAX['counter'].'" id="num_docalt'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" size="30" name="nom_destin'.$_AJAX['counter'].'" id="nom_destin'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '</tr>';
      
      $mHtml  .= '</table>';
      
      $mHtml  .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
       
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">* CIUDAD</td>';
        $mHtml .= '<td align="center" class="cellHead" width="30%">* DIRECCI&Oacute;N</td>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">* NUMERO CONTACTO</td>';
        $mHtml .= '<td align="center" class="cellHead" width="15%">* FECHA CITA DESCARGUE</td>';
        $mHtml .= '<td align="center" class="cellHead" width="15%">* HORA CITA DESCARGUE</td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="'.$style.'">'.$this -> GenerateSelect( $ciudad, 'cod_ciudad'.$_AJAX['counter'], $mData['cod_ciudad'], $readonly ).'</td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" size="40" name="dir_destin'.$_AJAX['counter'].'" value="'.$mData['dir_destin'].'" id="dir_destin'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" size="15" name="nom_contac'.$_AJAX['counter'].'" value="'.$mData['num_destin'].'" id="nom_contac'.$_AJAX['counter'].'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" name="fec_citdes'.$_AJAX['counter'].'" value="'.$mData['fec_citdes'].'" id="fec_citdes'.$_AJAX['counter'].'ID" class="date" size="20" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="'.$style.'"><input type="text" name="hor_citdes'.$_AJAX['counter'].'" value="'.$mData['hor_citdes'].'" id="hor_citcar'.$_AJAX['counter'].'ID" class="time" size="15" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
      $mHtml .= '</tr>';
      
      $mHtml .= '</table><br>';
      
    $mHtml .= '</div>';
    if( $_AJAX['ind_ajax'] == '1' )
    {
      echo $mHtml;
    }
    else
    {
      return $mHtml;
    }
  }

  protected function ingresaDestin( $_AJAX )
  {

	//Estilos para los formularios
	$this -> Style();
	//Propiedades de jquery para los elementos del formulario
	$this -> JqueryProperty();

    $mHtml = "<div class='StyleDIV' id='DestinID'>";
    
    $mHtml  .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
	    $mHtml .= '<tr>';
	      $mHtml .= '<td align="left" width="100%" class="label-info" colspan="10">Destinatarios asignados al Despacho. Para agregar otro haga click <a style="color:#285C00; text-decoration:none; cursor:pointer;" onclick="AddGrid();">aqu&iacute;</a><br>&nbsp;</td>';
	    $mHtml .= '</tr>';
    $mHtml  .= '</table>';

    $mHtml .= '<input type="hidden" id="counterID" value="'.sizeof( $_DESTIN ).'" />';
    $count = 0;

    $_AJAX['counter'] = 0 ;
    $mHtml  .= $this -> ShowDestin( $_AJAX );
    
    $mHtml .= "</div>";

    $mHtml  .= "<div class='StyleDIV'>";
    $mHtml  .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';

	$mHtml .= '<tr style="border: hidden">';
		$mHtml .= '<td style="border: hidden" align="right">';		
			$mHtml .= '<button onclick="InserDestin();">Insertar Destinario/s</button>';			
			$mHtml .= '<button onclick="cerrarDialog();">Cerrar</button>';			
		$mHtml .= '</td>';
	$mHtml .= '</tr>';   

	$mHtml .= '<input type="hidden" name="viajes" id="viajesId" value="'.$_AJAX['viajes'].'">
			   <input type="hidden" name="numPlacax" id="numPlacaxId" value="'.$_AJAX['placa'].'">
			   <input type="hidden" name="nitTransp" id="nitTranspId" value="'.$_AJAX['nitTransp'].'">
			   <input type="hidden" name="codRutasx" id="codRutasxId" value="'.$_AJAX['cod_ruta'].'">';

    $mHtml  .= '</table>';
   
    $mHtml .= "</div>";
   	
    echo $mHtml;

  }//Fin Funcion ingresaDestin  


  protected function validacionDestinos( $_AJAX )
  {
  
	//Saber si los despachos tienen o no destinatarios
	$verifyDestin = $this -> countDestinDespac( $_AJAX['viajes'] );

	$mSelect = "SELECT a.num_despac, a.obs_despac, b.num_desext 
				  FROM ".BASE_DATOS.".tab_despac_despac a,
				       ".BASE_DATOS.".tab_despac_sisext b
				 WHERE a.num_despac = b.num_despac 
				   AND a.num_despac IN(".$_AJAX['viajes'].")";
	
	$consulta = new Consulta( $mSelect, $this -> conexion );
    $_OBSDES = $consulta -> ret_matriz();

	$mHtml =  '<div class="StyleDIV" id="FormDivID">
				 <table width="100%" cellpadding="0" cellspacing="0" align="center">
				   <tr>
					 <td colspan="3" class="CellHead">Observaciones de los Viajes a Consolidar </td>
				   </tr>
				   <tr>
					 <td class="CellHead" align="center" width="4%">Despacho</td>
					 <td class="CellHead" align="center" width="4%">Viaje</td>
					 <td class="CellHead" align="center" width="91%">Observaciones</td>
				   </tr>
				   ';

	foreach( $_OBSDES as $row )
	{
	  $mHtml .= '<tr>';
	  $mHtml .= '<td align="center" class="cellInfoOther">'.$row['num_despac'].'</td>';
	  $mHtml .= '<td align="center" class="cellInfoOther">'.$row['num_desext'].'</td>';
	  $mHtml .= '<td align="center" class="cellInfoOther">'.$row['obs_despac'].'</td>';
	  $mHtml .= '</tr>';
	}
	
	$mHtml .= '</table>
			  </div><br>';
	echo $mHtml;
	//if( $verifyDestin > 0)
	if( $verifyDestin > 0)
	{
		$destinUpdate = $this -> getFechasDestin( $_AJAX['viajes'] );
		//------------------------------------------------------
		
		$this -> Style();
		$this -> JqueryProperty();
		
		echo '<script>
				$(function() {
					$( ".calendario" ).datepicker();
					$( ".tiempo" ).timepicker();
				});
			 </script>';
		
		//------------------------------------------------------		
		$html = '<form name="formConsolViajes" id="formConsolViajesId">
				 <div class="StyleDIV" id="FormDivID">

				 <table border="1" id="tablePrincipal" cellpadding="3" cellspacing="0" align="center" style="border:none">
				 
					<tr>
						<td colspan="7" class="CellHead">Debe ingresar fechas y horas validas para los siguientes destinatarios</td>
					</tr>
				 
					<tr>
						<td class="CellHead" align="center">Numero Despacho</td>
						<td class="CellHead" align="center">Destinatario</td>
						<td class="CellHead" align="center">Ciudad</td>
						<td class="CellHead" align="center">Fecha Descargue</td>
						<td class="CellHead" align="center">Hora Cita Descargue</td>
					</tr>';
						
		for( $i = 0 ; $i < sizeof( $destinUpdate ) ; $i++ )
		{
		
			$check1 = $viajes[$i]['ind_consol'] == 1 ? 'checked="checked"' : NULL ;
			$check2 = $viajes[$i]['ind_consol'] == 0 ? 'checked="checked"' : NULL ;

		
			$html .= '<tr>
						<td class="cellInfo" align="center">
															<input type="hidden" name="num_despac" id="num_despac'.$i.'Id" value="'.$destinUpdate[$i]['num_despac'].'">
															'.$destinUpdate[$i]['num_despac'].'
															</td>
						<td class="cellInfo" align="center">
															<input type="hidden" name="num_docume" id="num_docume'.$i.'Id" value="'.$destinUpdate[$i]['num_docume'].'">
															'.$destinUpdate[$i]['num_docume'].' - '.$destinUpdate[$i]['nom_destin'].'		
															</td>
						<td class="cellInfo" align="center">'.$destinUpdate[$i]['nom_ciudad'].'</td>
						<td class="cellInfo" align="center">
							<input type="text" 
								   class="calendario" 
								   name="fec_citdes'.$i.'"
								   id="fec_citdes'.$i.'ID" 
								   class="campo_texto" 
								   size="10" 
								   maxlength="10"
								   onfocus="this.className=\'campo_texto_on\'" 
								   onblur="this.className=\'campo_texto\'"
								   value="'.$destinUpdate[$i]['fec_citdes'].'" >
						</td>
						<td class="cellInfo" align="center">
							<input type="text" 
								   class="tiempo" 
								   name="hor_citdes'.$i.'" 
								   id="hor_citdes'.$i.'ID" 
								   class="campo_texto" 
								   size="8" 
								   maxlength="8"
								   onfocus="this.className=\'campo_texto_on\'" 
								   onblur="this.className=\'campo_texto\'"
								   value="'.$destinUpdate[$i]['hor_citdes'].'" >
						</td>
					</tr>';	
					
		}//Fin For muestra fechas y horas que se deben corregir

				$html .= '<tr style="border: hidden">';
					$html .= '<td colspan="7" style="border: hidden"></td>';					
				$html .= '</tr>';
			$html .= '</table>';
			
			$html .= '<div id="DestinID">';
			$html .= '</div>';
			
			$html .= '<table border="1" id="tableBotonera" cellpadding="3" cellspacing="0" align="center" style="border:none">';
				$html .= '<tr style="border: hidden">';
					$html .= '<td style="border: hidden" colspan="7" align="right">';		
						$html .= '<button onclick="newDestin();">Agregar Destinatario</button>';			
						$html .= '<button onclick="validarFechasDestin();">Validar Fechas</button>';			
						$html .= '<button onclick="cerrarDialog();">Cerrar</button>';			
					$html .= '</td>';
				$html .= '</tr>';	
		
			$html .= '</table>
					  <input type="hidden" name="codRutasx" id="codRutasxId" value="'.$_AJAX['cod_ruta'].'">
					  <input type="hidden" name="viajes" id="viajesId" value="'.$_AJAX['viajes'].'">
					  <input type="hidden" name="numPlacax" id="numPlacaxId" value="'.$_AJAX['placa'].'">
					  <input type="hidden" name="nitTransp" id="nitTranspId" value="'.$_AJAX['nitTransp'].'">
					  <input type="hidden" name="counter" id="counterID" value="'.sizeof( $destinUpdate ).'">
					  <input type="hidden" name="numDestin" id="numDestinId" value="'.sizeof( $destinUpdate ).'">';
					  
			$html .= '</div>';
			
		$html .= '</form>';

		echo $html;
	}
	else
	{
		$this -> ingresaDestin( $_AJAX );
	}//Fin If
  
  }//Fin Funcion validacionDestinos
  
  protected function updateFechasDestin( $destin )
  {

  	for( $i = 0; $i < sizeof($destin) ; $i++ )
  	{

  		$sql = "UPDATE
  					".BASE_DATOS.".tab_despac_destin
				SET
					fec_citdes = '".$destin[$i]['fec_citdes']."' ,
					hor_citdes = '".$destin[$i]['hor_citdes']."' 
				WHERE
					num_despac = '".$destin[$i]['num_despac']."'
					AND num_docume = '".$destin[$i]['num_docume']."' ";

		$update = new Consulta( $sql , $this -> conexion );

  	}//Fin For Actualizacion 

  }//Fin Funcion updateFechasDestin

  protected function saveFechasDestin( $_AJAX )
  {
  	$mArrayNuedes = array();
  	
  	if( $_AJAX['ind_nuedes'] == '1' )
  	{
  	  for( $i = 0, $j = 1; $i < (int)$_AJAX['cou_nuedes'] ; $i++ )
  	  {
  	  	$mArrayNuedes[$i]['num_factur'] = $_AJAX['num_factur'.$j];
  	  	$mArrayNuedes[$i]['num_docalt'] = $_AJAX['num_docalt'.$j];
  	  	$mArrayNuedes[$i]['nom_destin'] = $_AJAX['nom_destin'.$j];
  	  	$mArrayNuedes[$i]['cod_ciudad'] = $_AJAX['cod_ciudad'.$j];
  	  	$mArrayNuedes[$i]['dir_destin'] = $_AJAX['dir_destin'.$j];
  	  	$mArrayNuedes[$i]['nom_contac'] = $_AJAX['nom_contac'.$j];
  	  	$mArrayNuedes[$i]['fec_citdes'] = $_AJAX['fec_citdes'.$j];
  	  	$mArrayNuedes[$i]['hor_citdes'] = $_AJAX['hor_citdes'.$j];
  	  	$j++;
  	  }
  	}

	//Separando los destinatarios 
	$destin = explode('||', $_AJAX['data']);

	//Organizando los datos de los destinatarios en un array
	for( $i = 0 ; $i < sizeof($destin); $i++ )
	{
		$result = explode( '|' , $destin[$i] );

		$destinFechas[$i]['num_despac'] = $result[0];
		$destinFechas[$i]['num_docume'] = $result[1];
		$destinFechas[$i]['fec_citdes'] = $result[2];
		$destinFechas[$i]['hor_citdes'] = $result[3];
	}//FIN FOR recorrido datos destinatarios

	$this -> updateFechasDestin( $destinFechas );

	//Paso a realizar el ordenamiento de los viajes 
	$this -> consolGeneral( $_AJAX, $mArrayNuedes );

  }//Fin Funcion saveFechasDestin  

  //Esta funcion toma todos los parametros que se envian en el get
  //desde un ajax, agrupa los correspondientes a la informacion de 
  //los destinatarios en un array y despues los organiza por fecha
  protected function orderDestinManual( $_AJAX )
  {

  	//Se toman los parametros que vienen por get y se organizan en un array
  	for( $i = 0 ; $i <= $_AJAX['counter'] ; $i++ )
  	{
  		$destin[$i]['num_factur'] = $_AJAX['num_factur'.$i];
  		$destin[$i]['num_docalt'] = $_AJAX['num_docalt'.$i];
  		$destin[$i]['nom_destin'] = $_AJAX['nom_destin'.$i];
  		$destin[$i]['cod_ciudad'] = $_AJAX['cod_ciudad'.$i];
  		$destin[$i]['dir_destin'] = $_AJAX['dir_destin'.$i];
  		$destin[$i]['nom_contac'] = $_AJAX['nom_contac'.$i];
  		$destin[$i]['fec_citdes'] = $_AJAX['fec_citdes'.$i];
  		$destin[$i]['hor_citcar'] = $_AJAX['hor_citcar'.$i];
  	}//Fin For 

  	//Utilizando metodo burbuja para organizar el array
    for( $i = 0 ; $i <= $_AJAX['counter'] ; $i++ )
    {
        for( $j = 0 ; $j < $_AJAX['counter']-$i ; $j++ )
        {
                if( $destin[$j]['fec_citdes'] > $destin[$j+1]['fec_citdes'] )
                {
                	$k = $destin[ $j + 1 ]; 
                	$destin[$j+1] = $destin[ $j ]; 
                	$destin[$j] = $k;
                }//Fin if condicional 
                
        }//Fin For Interno
    }//Fin For de Ordenamiento

    $destin = array_reverse($destin);

    return $destin;

  }//Fin Funcion orderDestinManual

  protected function orderDestinAutomatico( $viajes )
  {

	$sql = "SELECT
				a.num_despac , a.num_docume , a.num_docalt , a.nom_destin ,
				a.cod_ciudad , a.dir_destin , a.num_destin , 
				a.fec_citdes , a.hor_citdes
			FROM
				".BASE_DATOS.".tab_despac_destin a, 
				".BASE_DATOS.".tab_despac_despac b 
			WHERE
				a.num_despac = b.num_despac
				AND a.num_despac IN (".$viajes.")
			ORDER BY CONCAT( a.fec_citdes, ' ' , a.hor_citdes ) DESC";
	
	$consulta = new Consulta( $sql , $this -> conexion );
	$destin = $consulta->ret_matriz();
	
	return $destin;	
  }//Fin Funcion orderDestinAutomatico

  //-------------------------------------------------------------------
  protected function getConsecNew()
  {
    $sql = "SELECT 
    				MAX( num_despac ) AS maximo
                FROM 
                	".BASE_DATOS.".tab_despac_despac ";
		
    $consec = new Consulta( $sql, $this -> conexion );
		$ultimo = $consec -> ret_matriz();
		
	$ultimo_consec = $ultimo[0][0];
	$nuevo_consec = $ultimo_consec + 1;

	return $nuevo_consec;

  }//Fin Funcion getConsecNew
  //-------------------------------------------------------------------

  protected function getInfoDespachos( $viajes )
  {

	$sql = "SELECT
				a.fec_despac , a.cod_tipdes , a.cod_paiori , 
				a.cod_depori , a.cod_ciuori , a.cod_paides , 
				a.cod_depdes , a.cod_ciudes , a.cod_agedes ,
				b.cod_agenci , b.cod_conduc , a.fec_salida ,
				a.obs_salida , a.num_despac
			FROM
				".BASE_DATOS.".tab_despac_despac a,
				".BASE_DATOS.".tab_despac_vehige b
			WHERE 
				a.num_despac = b.num_despac
				AND a.num_despac IN (".$viajes.") 
			ORDER BY a.fec_salsis ASC";

	//$this -> IMPMAT($sql);
	$consulta = new Consulta( $sql , $this -> conexion );
	$despachos = $consulta->ret_matriz();
	
	return $despachos;

  }//Fin Funcion getInfoDespachos

  protected function InsertDespac( $datos , $destin )
  {

  	//---------------------------------------------------------
  	//Informacion de los despachos que se consolidaran
  	//---------------------------------------------------------
  	$despachos = $this -> getInfoDespachos( $datos['viajes'] );
  	//---------------------------------------------------------
  	
  	//ultimo destinatario 
  	//---------------------------------------------------------
  	$ultimo = sizeof($despachos) - 1;
  	//---------------------------------------------------------

  	//Insertando el registro en la tabla tab_despac_despac
    $sql = "INSERT INTO ".BASE_DATOS.".tab_despac_despac
                          ( 
                            num_despac, fec_despac, cod_tipdes,
                            cod_paiori,	cod_depori,	cod_ciuori,
                            cod_paides,	cod_depdes,	cod_ciudes,	
                            cod_agedes, fec_salida, fec_ultnov,
                            obs_salida, usr_creaci, fec_creaci
                          ) 
                   VALUES 
                   		  ( 
                            ".$datos['consec'].", '".$despachos[0]['fec_despac']."', '".$despachos[0]['cod_tipdes']."', 
                            '".$despachos[0]['cod_paiori']."', '".$despachos[0]['cod_depori']."', '".$despachos[0]['cod_ciuori']."',
                            '".$despachos[$ultimo]['cod_paides']."', '".$despachos[$ultimo]['cod_depdes']."', '".$despachos[$ultimo]['cod_ciudes']."',
                            '".$despachos[0]['cod_agedes']."', '".$despachos[0]['fec_salida']."' , '".$despachos[0]['fec_ultnov']."',
                            '".$despachos[0]['obs_salida']." - Viaje consolidado' , '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
                          )"; 

	//$this -> IMPMAT( $sql );
	//@consul
    $consulta = new Consulta($sql, $this -> conexion, "BR");

	$sql = "INSERT INTO ".BASE_DATOS.".tab_despac_vehige
	                  (
	                    num_despac, cod_transp, cod_agenci, 
	                    cod_rutasx, cod_conduc, num_placax, 
	                    num_trayle, obs_medcom, ind_activo, 
	                    usr_creaci, fec_creaci
	                  )
	             VALUES 
	                  (
	                    '".$datos['consec']."', '".$datos['nitTransp']."', '".$despachos[0]['cod_agenci']."', 
	                    '".$datos['cod_rutaxx']."', '".$despachos[0]['cod_conduc']."', '".$datos['num_placax']."', 
	                    NULL, NULL, 'S',
	                    '".$_SESSION['datos_usuario']['cod_usuari']."', NOW() 
	                  )";

	//$this -> IMPMAT( $sql );
	//@consul
    $consulta = new Consulta( $sql, $this -> conexion, "R" );

    for( $k = 0; $k < sizeof($destin); $k++ )
    {
        $sql = "INSERT INTO ".BASE_DATOS.".tab_despac_destin
                              (
                                num_despac, num_docume, num_docalt, 
                                cod_genera, nom_destin, cod_ciudad, 
                                dir_destin, num_destin, fec_citdes, 
                                hor_citdes, usr_creaci, fec_creaci
                              )
                         VALUES
                              (
                                '".$datos['consec']."', '".$destin[$k]['num_docume']."','".$destin[$k]['num_docalt']."', 
                                '".$datos['nitTransp']."', '".$destin[$k]['nom_destin']."', ".$destin[$k]['cod_ciudad'].",
                                '".$destin[$k]['dir_destin']."', '".$destin[$k]['nom_contac']."', '".$destin[$k]['fec_citdes']."', 
                                '".$destin[$k]['hor_citdes']."','".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
                              )";

		//$this -> IMPMAT( $sql );
		//@consul
        $consulta = new Consulta( $sql, $this -> conexion, "R" );
    }      

    //Esta consulta hace que el despacho se muestre en la bandeja 
    $sql = "INSERT INTO ".BASE_DATOS.".tab_despac_sisext 
    			(
					num_despac , num_desext , num_solici ,
					num_consol , num_pedido , tip_vehicu ,
					cod_operad , tip_transp , cod_instal ,
					cod_mercan , fec_plalle , ind_cumcar ,
					fec_cumcar , nov_cumcar , obs_cumcar ,
					usr_cumcar
				)
			VALUES 
				(
					'".$datos['consec']."', 'VC', NULL , 
					NULL , NULL , NULL , 
					NULL , NULL , NULL , 
					NULL , NULL , NULL , 
					NULL , NULL , NULL , 
					NULL
				);";
	//$this -> IMPMAT( $sql );
	//@consul
    $consulta = new Consulta( $sql, $this -> conexion, "R" );

    for( $i = 1; $i < sizeof( $despachos ); $i++ )
  	{  	  
  	  $mSelect = "SELECT CONCAT( fec_citcar, '', hor_citcar ) AS fec_citcar
  	   			    FROM ".BASE_DATOS.".tab_despac_despac 
  	   			   WHERE num_despac = '".$despachos[$i]['num_despac']."' ";
  	  
  	  $consulta = new Consulta( $mSelect, $this -> conexion );
	  $sisext = $consulta->ret_matriz();

	  $mSelect = "SELECT MAX(num_consec) 
	  				FROM ".BASE_DATOS.".tab_consol_citcar 
	  			   WHERE num_despac = '".$datos['consec']."'"; 

	  $consulta = new Consulta( $mSelect, $this -> conexion );
	  $num_consec = $consulta->ret_matriz();
  	  
  	  $mInsert = "INSERT INTO ".BASE_DATOS.".tab_consol_citcar
  	   						( num_consec, num_despac, fec_citcar, 
  	   						  ind_cumcar, fec_cumcar, nov_cumcar, 
  	   						  obs_cumcar, usr_creaci, fec_creaci
  	   						)VALUES
							( '".( $num_consec[0][0] + 1 )."', '".$datos['consec']."', '".$sisext[0]['fec_citcar']."',
							  NULL, NULL, NULL, NULL, '".$_SESSION['datos_usuario']['cod_usuari']."', NOW()
							)";
  	  $consulta = new Consulta( $mInsert, $this -> conexion, "R" );
  	}


  }//Fin Funcion InsertDespac

  //Con esta funcion se anulan los despachos que se consolidaran
  protected function AnularDespac( $viajes , $consec )
  {

  	//---------------------------------------------------------
	$sql = "UPDATE 
				".BASE_DATOS.".tab_despac_vehige
              SET 
              	ind_activo = 'N',
	            obs_anulad = 'Anulacion del viaje por consolidacion de los despachos ".$viajes." en el despacho ".$consec."',
	            usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
	            fec_modifi = NOW()
              WHERE 
              	num_despac IN ( ".$viajes." ) ";
	//$this -> IMPMAT( $sql );
  	//@consul
	$consulta = new Consulta($sql, $this -> conexion,"R");
	//---------------------------------------------------------
	$sql = "UPDATE 
				".BASE_DATOS.".tab_despac_despac a , 
				".BASE_DATOS.".tab_despac_vehige b 
    		  SET  
    		  	a.ind_anulad = 'A',
	            a.usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."',
	            a.fec_modifi = NOW(),
	            b.ind_consol = '1'
	          WHERE 
	          	a.num_despac = b.num_despac
	            AND a.num_despac IN ( ".$viajes." ) ";
    //$this -> IMPMAT( $sql );
	//@consul
	//---------------------------------------------------------
	$consulta = new Consulta($sql, $this -> conexion,"R");
	//---------------------------------------------------------  	
	
	//return $result = $consulta ? true : false ;

  }//Fin Funcion AnularDespac

  protected function InsertConsolDespac( $viajes , $consec )
  {

  	//echo $viajes . ' -> ' . $consec;

  	//Separando los viajes y organizandolos en un array
  	$viajes = explode( "," , $viajes );

  	//Ingresando los despachos a consolidar como despachos hijos
  	//del despacho que se creo anteriormente con los nuevos datos
  	for( $i = 0 ; $i < sizeof( $viajes ) ; $i++ )
  	{
  		$sql = "INSERT INTO ".BASE_DATOS.".tab_consol_despac
  					(
  						cod_despad , cod_deshij, usr_creaci , 
  						fec_creaci
					)
				VALUES
					(
						'".$consec."' , '".$viajes[$i]."' , '".$_SESSION['datos_usuario']['cod_usuari']."',
						NOW()
					); ";

		//$this -> IMPMAT( $sql );
		$consulta = new Consulta($sql, $this -> conexion,"R");

  	}//Fin For Insert en la tabla tab_consol_despac
	
	//Finalizando la transaccionalidad para estas consultas
	$consulta = new Consulta("SELECT 1", $this -> conexion,"RC");

	return true;

  }//Fin Funcion InsertConsolDespac

  protected function getPueConRuta( $ruta )
  {
 
	$sql = "SELECT 
				a.*
			FROM 
				".BASE_DATOS.".tab_genera_rutcon a
		    WHERE
		     	a.cod_rutasx = ".$ruta." 
	     	ORDER BY a.val_duraci ASC ";

	$consulta = new Consulta( $sql , $this -> conexion );
	$puecon = $consulta -> ret_matriz();	     	

	return $puecon;

  }//FIN FUNCION

  protected function EnrutarDespac( $despacho , $ruta , $fecha )
  {

  	$puecon = $this -> getPueConRuta( $ruta );

        #$tieacu = 0;
        #$ultimo = $tieacu + $puecon[0]['val_duraci'];
        #$tiepla = $tieacu + $puecon[0]['val_duraci'];

	$fecpla = $fecha;		
        $fecpla = str_replace("/","-",$fecpla);	

	for( $i = 0 ; $i < sizeof($puecon) ; $i++ )
	{
		$sql = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
				       (
			       			num_despac , cod_rutasx , cod_contro , 
			       			fec_planea , fec_alarma , usr_creaci , 
			       			fec_creaci , usr_modifi , fec_modifi
		       		   )
           		VALUES 
           			   (
                                        ".$despacho.", '".$ruta."' , '".$puecon[$i]['cod_contro']."' ,
                                        DATE_ADD('".$fecpla."', INTERVAL ".$puecon[$i]['val_duraci']." MINUTE) , 
                                        DATE_ADD('".$fecpla."', INTERVAL ".$puecon[$i]['val_duraci']." MINUTE) ,
                                        '".$_SESSION['datos_usuario']['cod_usuari']."' , NOW() ,NULL,NULL
                 	   );";

		//$this -> IMPMAT( $sql );
		//@consul
		$insercion = new Consulta($sql, $this -> conexion,"R");
	}//Fin For

	$sql = "UPDATE 
				".BASE_DATOS.".tab_despac_despac a
			SET
				a.ind_planru = 'S'
			WHERE 
				a.num_despac = '".$despacho."'; ";

	$insercion = new Consulta($sql, $this -> conexion,"R");


  }//Fin Funcion EnrutarDespac


  protected function consolGeneral( $_AJAX, $mArrayDestin = NULL )
  {

  	//$this -> IMPMAT( $_AJAX , 1 );

    $this -> Style();
    $this -> JqueryProperty();

  	//-----------------------------------------------------------------
  	// aqui se traen los destinatarios que se ingresaran para el nuevo
  	// despacho y se organizan cronologicamente
  	//-----------------------------------------------------------------
  	if( strtolower( $_AJAX['tip_consol'] ) == 'manual' )
  	{
  		$destin = $this -> orderDestinManual( $_AJAX );
  	}
  	else
	{
  		$destin = $this -> orderDestinAutomatico( $_AJAX['viajes'] );
	}//Fin Condicional organizacion de destinatarios
	//-----------------------------------------------------------------

	$consec = $this -> getConsecNew();

	$datos['consec'] = $consec;
	$datos['nitTransp'] = $_AJAX['nitTransp'];
	$datos['cod_rutaxx'] = $_AJAX['cod_ruta'];
	$datos['num_placax'] = $_AJAX['placa'];
	$datos['viajes'] = $_AJAX['viajes'];

	//Insertar el despacho 
	$this -> InsertDespac( $datos , $destin );
	//Enrutando el despacho
	$despachos = $this -> getInfoDespachos( $datos['viajes'] );	
	$this -> EnrutarDespac( $consec , $_AJAX['cod_ruta'] , $despachos[0]['fec_salida'] );
	//Anular los despachos antiguos
	$result = $this -> AnularDespac( $_AJAX['viajes'] , $consec );
	//consolidando Viajes
	
	if( sizeof( $mArrayDestin ) > 0 )
	{
	  foreach( $mArrayDestin as $row )
	  {
	  	$mInsertF = "INSERT INTO ".BASE_DATOS.".tab_despac_destin
                    (
                      num_despac, num_docume, num_docalt, cod_genera,
                      nom_destin, cod_ciudad, dir_destin, num_destin, 
                      fec_citdes, hor_citdes, usr_creaci, fec_creaci,
                      ind_modifi 
                    )
                   VALUES
                   (
					 '".$consec."', '".$row['num_factur']."', '".$row['num_docalt']."', '860068121',
					 '".$row['nom_destin']."', '".$row['cod_ciudad']."', '".$row['dir_destin']."', '".$row['nom_contac']."', 
					 '".$row['fec_citdes']."', '".$row['hor_citdes']."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW(),
					 '1'
                   	)";
	  	$consulta = new Consulta($mInsertF, $this -> conexion,"R");
	  }
	}

	$result = $this -> InsertConsolDespac( $_AJAX['viajes'] , $consec );

	//Si todas las transacciones se ejecutaron de manera correcta entonces 
	//se debe informar al cliente el numero de consolidado y refrescar
	if( $result )
	{
		$msg = 'Los despachos ' . $_AJAX['viajes'] . ' han sido consolidados con el numero ' . $consec . '.'; 

		$html = '<form name="formConsolManual" id="formConsolManualId">
				 <div class="StyleDIV" id="FormDivID">
				 <table border="1" cellpadding="3" cellspacing="0" align="center" style="border:none">

					<tr>
						<td>'.$msg.'</td>
					</tr>';
			
				$html .= '<tr style="border: hidden">';
					$html .= '<td style="border: hidden" align="right">';		
						$html .= '<input type="hidden" name="viaje" id="viajeID" value="'.$consec.'">';			
						$html .= '<input type="hidden" name="urlServer" id="urlServerID" value="'.DIREC_APLICA.'">';			
						$html .= '<button onclick="refreshDespac();">Cerrar</button>';			
					$html .= '</td>';
				$html .= '</tr>';
			
			$html .= '</table>';
			$html .= '</div>';
			
		$html .= '</form>';
		
		echo $html;	

	}
	else
	{

		$msg = 'Se ha presentado un error inesperado, por favor comunicarse con el administrador del sistema.'; 

		$html = '<form name="formConsolManual" id="formConsolManualId">
				 <div class="StyleDIV" id="FormDivID">
				 <table border="1" cellpadding="3" cellspacing="0" align="center" style="border:none">

					<tr>
						<td>'.$msg.'</td>
					</tr>';
			
				$html .= '<tr style="border: hidden">';
					$html .= '<td style="border: hidden" align="right">';		
						$html .= '<button onclick="cerrarDialog();">Cerrar</button>';			
					$html .= '</td>';
				$html .= '</tr>';
			
			$html .= '</table>';
			$html .= '</div>';
			
		$html .= '</form>';
		
		echo $html;

	}//Fin Verificacion de transacciones exitosas

  }//Fin Funcion ordenamientoViajes

}//Fin Clase 

$proceso = new AjaxConsolViajes();

?>