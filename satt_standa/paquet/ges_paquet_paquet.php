<?php

class GesPaquet
{
  var $conexion,
      $usuario,
      $cod_aplica;
  var $cNull = array( array( NULL, '--' ) );

  public function __construct( $co, $us, $ca )
  {
    $this -> conexion = $co;
    $this -> usuario = $us;
    $this -> cod_aplica = $ca;
    GesPaquet::principal();
  }

 

  //Inicio Función Principal
  private function principal() 
  {
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/min.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/jquery.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/es.js' ></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/time.js' ></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
    echo "<script type='text/javascript' src='../".DIR_APLICA_CENTRAL."/js/ges_paquet_paquet.js' ></script>\n";

    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";

    $datos_usuario = $this -> usuario -> retornar();
    $usuario = $datos_usuario["cod_usuari"];

    switch ($_REQUEST['opcion']) 
    {
      case "1": GesPaquet::lista(); break;
      case "2": GesPaquet::consult(); break;
      case "3": GesPaquet::exportExcel(); break;
      default: 	GesPaquet::formulario(); break;
    }
  }
  //Fin Función Principal


    private function exportExcel()
  {
    session_start();
    $archivo = "informe_operaciones".date( "Y_m_d_H_i" ).".xls";
    header('Content-Type: application//ms-excel');
    header('Expires: 0');
    header('Content-Disposition: attachment; filename="'.$archivo.'"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    ob_clean();
    echo $HTML = $_SESSION['html'];
  }


  private function formulario()
  {
  	$mArrayCiuOri = GesPaquet::getCiuOri();
  	$mArrayCiuDes = GesPaquet::getCiuDes();

    $formulario = new Formulario ( "?", "post", "Paqueteo", "frm_bitaco\" id=\"frm_paquetID");
    
    $formulario -> texto( "Fecha Inicial:", "text", "fec_inicia\" readonly id=\"fec_iniciaID", 0, 10, 10, "", NULL );#date('Y-m-d')
    $formulario -> texto( "Fecha Final:", "text", "fec_finali\" readonly id=\"fec_finaliID", 1, 10, 10, "", NULL );#date('Y-m-d')
    $formulario -> texto( "Despacho SAT:", 	"text", "num_despac\" id=\"num_despacID", 0, 20, 20, "", NULL );
    $formulario -> texto( "Remesa DIC:",		"text", "dic_remesa\" id=\"dic_remesaID", 1, 20, 20, "", NULL );
    $formulario -> lista( "Ciudad Origen:",	"ciu_origen\" id=\"ciu_origenID", $mArrayCiuOri);
    $formulario -> lista( "Ciudad Destino:","ciu_destin\" id=\"ciu_destinID", $mArrayCiuDes);
    
    $formulario -> nueva_tabla();
    $formulario -> botoni("Buscar","VerifiData();",0);
    $formulario -> nueva_tabla();
    $formulario -> oculto("window\" id=\"windowID","central",0);
    $formulario -> oculto("opcion\" id=\"opcionID","1",0);
    $formulario -> oculto("Standa\" id=\"StandaID",DIR_APLICA_CENTRAL,0);
    $formulario -> oculto("cod_servic\" id=\"cod_servicID",$_REQUEST['cod_servic'],0);
    
    $formulario -> cerrar();
  }

  private function lista()
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

    $mArrayData 		= GesPaquet::getDespacPaquet();
    $mArrayTitData 	= array('N&uacute;mero del Despacho', 'C&oacute;digo de la transportadora', 'Transportadora', 'Remesa DIC', 'Remesa Tercero', 'Nombre del Destinatario', 'Ciudad Origen', 
    												'Ciudad Destino', 'Direcci&oacute;n del Destinatario', 'Observaci&oacute;n del Despacho','telefono destino', 'descripcion unidad', 'descripcion cantidad', 'valor asegurado', 'observacion despacho', 'descripcion estado', 'fecha finalizacion', 'hora finalizacion', 'id cliente', 'id zona', 'id canal ', 'nombre negocio', 'numero pedido', 'numero consol', 'soolicitud transporte', 'numero viaje', 'numero documento1', 'numero documento2', 'numero documento3', 'numero documento4', 'numero documento5', 'numero documento6', 'numero documento7', 'numero documento8', 'numero documento9', 'numero documento10', 'numero documento11', 'numero documento12', 'numero documento13', 'numero documento14', 'numero documento15', 'numero documento16', 'numero documento17', 'numero documento18', 'numero documento19', 'numero documento20', 'numero documento21', 'numero documento22', 'numero documento23', 'numero documento24', 'numero documento25', 'numero documento26', 'numero documento27', 'numero documento28', 'numero documento29' );
    $mColum = sizeof($mArrayTitData);
    
    $formulario = new Formulario ( "?", "post", "Despachos", "frm_\" id=\"frm_ID");
    $mHtml  = '<table width="100%" cellspacing="1" cellpadding="0">';

	  $mHtml .= '<tr>';
    for($i=0; $i<$mColum; $i++){
    	$mHtml .= '<th class="CellHead">'.$mArrayTitData[$i].'</th>';
    }
	  $mHtml .= '</tr>';
	  
	  $i=0;
        echo '<a href="index.php?cod_servic='.$_REQUEST['cod_servic'].'&window=central&opcion=3 "target="centralFrame"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/excel.jpg" border="0" aling="left"></a>';
    foreach ($mArrayData as $row) {

	    $mHtml .= '<tr>';

	    $mHtml .= 	'<td class="cellInfo"><a href="index.php?window=central&cod_servic='.$_POST["cod_servic"].'&opcion=2&num_despac='.$row[0].'" id="DLLink'.$i.'ID" class="DLLink">'.$row[0].'</a></td>';
	    $mHtml .=		'<td class="cellInfo">'.$row[1].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row[20].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row[2].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row[3].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row[6].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['ciuOrigen'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['ciuDestin'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row[7].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row[12].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['tel_destin'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['des_unidad'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['des_cantid'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['val_asegur'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['obs_despac'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['des_estado'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['fec_finali'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['hor_finali'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['idx_client'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['idx_zonaxx'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['idx_canalx'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['nom_negoci'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['nox_pedido'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['nox_consol'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['sol_transp'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['nox_viajex'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum1'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum2'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum3'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum4'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum5'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum6'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum7'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum8'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum9'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum10'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum11'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum12'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum13'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum14'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum15'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum16'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum17'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum18'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum19'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum20'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum21'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum22'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum23'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum24'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum25'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum26'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum27'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum28'].'</td>';
	    $mHtml .=		'<td class="cellInfo">'.$row['num_docum29'].'</td>';
	    $mHtml .= '</tr>';
	    $i++;
    }
    
    $mHtml .= '</table>';
    $_SESSION['html'] = $mHtml;
    echo $mHtml;
    $formulario -> cerrar();
  }

  private function consult()
  {
    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";

  	$mArrayData  = GesPaquet::getDespacPaquet($_REQUEST['num_despac']);
  	$mArrayData2 = GesPaquet::getNovedaPaquet($_REQUEST['num_despac']);
  	$mArrayTitData 	= array('N&uacute;mero del Despacho', 'C&oacute;digo de la transportadora', 'Transportadora', 'Remesa DIC', 'Remesa Tercero', 'Ciudad Origen', 'Ciudad Destino', 
  													'Nombre del Destinatario', 'Direcci&oacute;n del Destinatario', 'Tel&eacute;fono del Destinatario', 'Unidades', 'Kilos', 'Valor Asegurado de la Mercanc&iacute;a', 
  													'Observaci&oacute;n del Despacho', 'Estado del Despacho', 'Fecha Finalizaci&oacute;n Despacho', 'Hora Finalizaci&oacute;n Despacho', 'Usuario de Creaci&oacute;n del Despacho', 
  													'Fecha de Creaci&oacute;n', 'Usuario Modificaci&oacute;n', 'Fecha Modificaci&oacute;n' );
  	$mArrayTitData2 = array('Consecutivo', 'N&uacute;mero del Despacho', 'C&oacute;digo de la transportadora', 'Remesa DIC', 'Remesa Tercero', 'Novedad', 'Observaci&oacute;n',
  													'Fecha', 'Hora', 'Usuario de Creaci&oacute;n del Despacho', 'Fecha de Creaci&oacute;n del Despacho');
    $mColum  = sizeof($mArrayTitData);
    $mColum2 = sizeof($mArrayTitData2);
    
    $formulario = new Formulario ( "?", "post", "Novedades del Despacho", "frm_\" id=\"frm_ID");
  	$mHtml  = '<table width="100%" cellspacing="1" cellpadding="0">';

	  $mHtml .= '<tr>';
    for($i=0; $i<$mColum; $i++){
    	$mHtml .= '<th class="CellHead">'.$mArrayTitData[$i].'</th>';
    }
	  $mHtml .= '</tr>';
	  
	  $mHtml .= '<tr>';
    for ($i=0; $i<$mColum; $i++)
    {
	    if ($i != 4 && $i != 5 && $i != 1 && $i != 20 && $i != 21){
	    	$mHtml .= '<td class="cellInfo">'.$mArrayData[0][$i].'&nbsp;</td>';
	    }elseif($i == 1){
	    	$mHtml .= '<td class="cellInfo">'.$mArrayData[0][$i].'&nbsp;</td>';
	    	$mHtml .= '<td class="cellInfo">'.$mArrayData[0][20].'&nbsp;</td>';
	    }elseif($i == 4){
	    	$mHtml .= '<td class="cellInfo">'.$mArrayData[0][21].'&nbsp;</td>';
	    	$mHtml .= '<td class="cellInfo">'.$mArrayData[0][22].'&nbsp;</td>';
	    }
    }
	  $mHtml .= '</tr>';
    
    $mHtml .= '</table>';
    $mHtml .= '</br>';

  	$mHtml .= '<table width="100%" cellspacing="1" cellpadding="0">';

  	$mHtml .= 	'<tr>';
  	for ($i=0; $i<$mColum2; $i++) { 
  		$mHtml .= '<th class="CellHead">'.$mArrayTitData2[$i].'</th>';
  	}
  	$mHtml .= 	'</tr>';

  	foreach ($mArrayData2 as $row) {
  		$mHtml .= '<tr>';
  		for ($i=0; $i<$mColum2; $i++) { 
  			$mHtml .= '<td class="cellInfo">'.$row[$i].'&nbsp;</td>';
  		}
  		$mHtml .= '</tr>';
  	}

  	$mHtml .= '</table>';

    echo $mHtml;
		$formulario -> cerrar();

  }

  private function getDespacPaquet($num_despac)
  {
  	$mSql = "	SELECT a.*, b.abr_tercer, c.abr_ciudad AS ciuOrigen, d.abr_ciudad AS ciuDestin
  							FROM ".BASE_DATOS.".tab_despac_paquet a
  				INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b
  								ON a.cod_transp = b.cod_tercer
  				INNER JOIN ".BASE_DATOS.".tab_genera_ciudad c
  								ON a.ciu_origen = c.cod_ciudad
  				INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d
  								ON a.ciu_destin = d.cod_ciudad
  						 WHERE 1=1 ";
	  if (!$num_despac)
	  {
      if ($_REQUEST['num_despac'] || $_REQUEST['dic_remesa'] || $_REQUEST['ciu_origen'] || $_REQUEST['ciu_destin'])
      {
  	  	if ($_REQUEST['num_despac']){
  	  		$mSql .= " AND a.num_despac = '".$_REQUEST['num_despac']."'";
  	  	}
  	  	if ($_REQUEST['dic_remesa']){
  	  		$mSql .= " AND a.dic_remesa = '".$_REQUEST['dic_remesa']."'";
  	  	}
        if ($_REQUEST['ciu_origen']){
          $mSql .= " AND a.ciu_origen = '".$_REQUEST['ciu_origen']."'";
        }
        if ($_REQUEST['ciu_destin']){
          $mSql .= " AND a.ciu_destin = '".$_REQUEST['ciu_destin']."'";
        }
        $mSql .= " ORDER BY num_despac ASC ";
      }elseif ($_REQUEST['fec_inicia'] && $_REQUEST['fec_finali']){
        $mSql .= " AND a.fec_creaci BETWEEN '".$_REQUEST['fec_inicia']."' AND '".$_REQUEST['fec_finali']."'";
        $mSql .= " ORDER BY num_despac ASC ";
      }else{
        $mSql .= " ORDER BY num_despac DESC ";
        $mSql .= " LIMIT 100 ";
      }
	  }else{
	  	$mSql .= " AND a.num_despac = '".$num_despac."'";
	  }

  	$mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult  = $mConsult -> ret_matriz('i');
  }

  private function getNovedaPaquet($num_despac)
  {
  	$mSql = "	SELECT  a.cod_consec, a.num_despac, a.cod_transp,
                      a.dic_remesa, a.ter_remesa, c.nom_noveda,
                      a.obs_noveda, a.fec_noveda, a.hor_noveda,
                      a.usr_creaci, a.fec_creaci, 
                      CONCAT(fec_noveda, ' ', hor_noveda) AS fec_novedax
  							FROM ".BASE_DATOS.".tab_noveda_paquet a
          INNER JOIN ".BASE_DATOS.".tab_homolo_causas b
                  ON a.cod_noveda = b.cod_causaf
          INNER JOIN ".BASE_DATOS.".tab_genera_noveda c
                  ON b.cod_noveda = c.  cod_noveda
  						 WHERE a.num_despac = ".$num_despac."
  					ORDER BY fec_novedax ASC
  					";
		$mConsult = new Consulta( $mSql, $this -> conexion );
    return $mResult  = $mConsult -> ret_matriz('i');
  }

  private function getCiuOri()
  {
  	$mSql = "	SELECT a.ciu_origen, b.abr_ciudad
	  						FROM ".BASE_DATOS.".tab_despac_paquet a
	  			INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b
	  							ON a.ciu_origen = b.cod_ciudad
	  				GROUP BY a.ciu_origen
	  				ORDER BY b.abr_ciudad
  					";
  	$mConsult = new Consulta( $mSql, $this -> conexion );
    $mResult  = $mConsult -> ret_matriz('i');
    return array_merge( $this -> cNull, $mResult );
  }

  private function getCiuDes()
  {
  	$mSql = "	SELECT a.ciu_destin, b.abr_ciudad
  							FROM ".BASE_DATOS.".tab_despac_paquet a
  				INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b
  								ON a.ciu_destin = b.cod_ciudad
  					GROUP BY a.ciu_destin
  					ORDER BY b.abr_ciudad
  					";
  	$mConsult = new Consulta( $mSql, $this -> conexion );
    $mResult  = $mConsult -> ret_matriz('i');
    return array_merge( $this -> cNull, $mResult );
  }

}

$new= new GesPaquet( $this -> conexion, $this -> usuario_aplicacion, $this-> codigo );

?>