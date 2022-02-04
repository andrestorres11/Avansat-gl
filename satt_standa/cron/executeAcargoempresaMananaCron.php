<?php

switch ($_SERVER['SERVER_NAME']) {
    case 'dev.intrared.net':
        include ("/var/www/html/ap/obocanegra/gl/sat-gl-2015/satt_standa/lib/general/constantes.inc");
        include (URL_ARCHIV_STANDA."obocanegra/gl/sat-gl-2015/satt_faro/constantes.inc");
        include (URL_ARCHIV_STANDA."obocanegra/gl/sat-gl-2015/satt_standa/lib/general/conexion_lib.inc");
        include (URL_ARCHIV_STANDA."obocanegra/gl/sat-gl-2015/satt_standa/lib/general/functions.inc"); 
        break;
    case 'avansatgl.intrared.net':
        include ("/var/www/html/ap/satt_faro/constantes.inc");
        include ("/var/www/html/ap/satt_standa/lib/general/constantes.inc");
        include (URL_ARCHIV_STANDA."satt_standa/lib/general/conexion_lib.inc");
        include (URL_ARCHIV_STANDA."satt_standa/lib/general/functions.inc"); 
        break;
}
//Include Connection class

error_reporting(E_ALL);
ini_set('display_errors', '1');


$conexion = new Conexion(HOST,USUARIO, CLAVE, BASE_DATOS);
$codigonovedad=9996;
$query = "SELECT tercer.cod_tercer, despac.`num_despac`, tercer.abr_tercer as  nom_transp, tercer.num_telmov as tel_transp, emails.`dir_emailx`, despac.`cod_manifi`, vehic.`num_placax`,  conduct.abr_tercer AS nom_conduc, conduct.num_telmov as tel_conduc,
CONCAT(ciudori.abr_ciudad, ', ', UPPER(LEFT(dptoori.abr_depart, 5)), ' , ', UPPER(paisori.abr_paisxx)) AS ciu_origen,
CONCAT(ciudest.abr_ciudad, ', ', UPPER(LEFT(dptodes.abr_depart, 5)), ' , ', UPPER(paisdes.abr_paisxx)) AS ciu_destino,
novedad.nom_noveda, novedad.obs_preted, contro.obs_contro, despac.fec_ultnov

FROM `tab_despac_despac` despac

INNER JOIN tab_despac_vehige vehic 
 ON despac.num_despac = vehic.num_despac 
 
INNER JOIN tab_tercer_tercer tercer 
 ON vehic.cod_transp = tercer.cod_tercer AND tercer.cod_estado = 1

 INNER JOIN tab_despac_contro contro 
 ON contro.num_despac = despac.num_despac 
 AND contro.cod_noveda = $codigonovedad 

 INNER JOIN tab_genera_noveda novedad 
 ON novedad.cod_noveda = despac.`cod_ultnov`
 AND novedad.`cod_noveda` = $codigonovedad 

INNER JOIN `tab_genera_concor` emails
 ON tercer.cod_tercer = emails.num_remdes
 AND emails. `dir_emailx` != ''
 AND emails.`ind_acargo`=1

INNER JOIN tab_tercer_tercer conduct 
 ON vehic.cod_conduc = conduct.cod_tercer 

INNER JOIN tab_genera_ciudad ciudori 
 ON despac.cod_ciuori = ciudori.cod_ciudad 
 AND despac.cod_depori = ciudori.cod_depart 
 AND despac.cod_paiori = ciudori.cod_paisxx 
 
INNER JOIN tab_genera_depart dptoori 
 ON despac.cod_depori = dptoori.cod_depart 
 AND despac.cod_paiori = dptoori.cod_paisxx 
 
INNER JOIN tab_genera_paises paisori
 ON  despac.cod_paiori = paisori.cod_paisxx
 
INNER JOIN tab_genera_ciudad ciudest  
 ON despac.cod_ciudes = ciudest.cod_ciudad 
 AND despac.cod_depdes = ciudest.cod_depart 
 AND despac.cod_paides = ciudest.cod_paisxx 
 
INNER JOIN tab_genera_depart dptodes 
 ON despac.cod_depdes = dptodes.cod_depart 
 AND despac.cod_paides = dptodes.cod_paisxx 
 
INNER JOIN tab_genera_paises paisdes
 ON  despac.cod_paides = paisdes.cod_paisxx
 
 where (despac.fec_llegad IS NULL OR despac.fec_llegad = '0000-00-00 00:00:00')
 AND despac.`ind_anulad`='R' 
 AND despac.`cod_ultnov` = $codigonovedad ";


$consulta = new Consulta($query, $conexion);
$datos = $consulta->ret_matriz('a');

$query2 = "SELECT dir_emailx FROM ".BASE_DATOS.".tab_genera_concor WHERE num_remdes = ''";
$consulta2 = new Consulta($query2, $conexion);
$datosAddCc = $consulta2->ret_matriz('a');

$numdatos=count($datos);
if ($numdatos>0){


echo "<pre>";
print_r($datos);
echo "</pre>";

function crearexceltmp($datexcel, $addcc){
    
    echo "<pre>";
        print_r($datexcel);
    echo "</pre>";

    switch ($_SERVER['SERVER_NAME']) {
        case 'dev.intrared.net':
            $url_planti_html=URL_ARCHIV_STANDA."obocanegra/gl/sat-gl-2015/satt_standa/planti/pla_acargo_empresa.html";
            break;
        case 'avansatgl.intrared.net':
            $url_planti_html=URL_ARCHIV_STANDA."satt_standa/planti/pla_acargo_empresa.html";
            break;
    }

    //$url_planti_html=URL_ARCHIV_STANDA."obocanegra/gl/sat-gl-2015/satt_standa/planti/pla_acargo_empresa.html";

    $ano = date("Y");
    $dia_manana = date('Y-m-d',time());

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->
        getProperties()
            ->setCreator("Avansat GL")
            ->setLastModifiedBy("Avansat GL")
            ->setTitle("Informe A cargo de empresa")
            ->setSubject("A cargo de empresa");

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Despacho')
            ->setCellValue('B1', 'Manifiesto')
            ->setCellValue('C1', 'Placa')
            ->setCellValue('D1', 'Conductor')
            ->setCellValue('E1', 'Celular')
            ->setCellValue('F1', 'Origen')
            ->setCellValue('G1', 'Destino')
            ->setCellValue('H1', 'Ubicacion')
            ->setCellValue('I1', 'Novedad')
            ->setCellValue('J1', 'Observacion')
            ->setCellValue('K1', 'Fecha de Novedad');
    $objPHPExcel->getActiveSheet()->setTitle('A Cargo de empresa');
    $i=2;



    foreach($datexcel as $fila) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $fila['num_despac'])
                ->setCellValue('B'.$i, $fila['cod_manifi'])
                ->setCellValue('C'.$i, $fila['num_placax'])
                ->setCellValue('D'.$i, $fila['nom_conduc'])
                ->setCellValue('E'.$i, $fila['tel_conduc'])
                ->setCellValue('F'.$i, $fila['ciu_origen'])   
                ->setCellValue('G'.$i, $fila['ciu_destino'])
                ->setCellValue('H'.$i, utf8_encode($fila['nom_noveda']))
                ->setCellValue('I'.$i, utf8_encode($fila['nom_noveda']))
                ->setCellValue('J'.$i, utf8_encode($fila['obs_contro']))
                ->setCellValue('K'.$i, utf8_encode($fila['fec_ultnov']));
            $transport=$fila['nom_transp'];
            $paraemails=$fila['dir_emailx'];    
            $i++;
        }
    //estilos
    $styleArray = array(
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => 'FFFFFF'),
            'size'  => 12,
            'name'  => 'Verdana'
        ));
    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleArray);
    foreach(range('A','K') as $columnID){
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:K1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => '002ff6'
            )
        ));
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1:K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold( true );
    $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
    $objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);

    // Se modifican los encabezados del HTTP para indicar que se envia un archivo de Excel.
    //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //header('Content-Disposition: attachment;filename="pruebaReal.xlsx"');
    //header('Cache-Control: max-age=0');

    //Creacion del documento excel
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

    //$filePath = "/cron/tmpfile/"."archivoprbexcel.xlsx";
    $filePath = sys_get_temp_dir() . "/" . rand(0, getrandmax()) . rand(0, getrandmax()) . ".xlsx";
    //echo $filePath."<br>";
    $objWriter->save($filePath);
    //$objWriter->save('php://output');



//Cuerpo del mensaje

$bodymsj='


                <p class="text-title">Estimado Cliente:<strong> '. $transport.'</strong> </p>
            
                <p class="text-content">Cordial saludo, <strong>Centro Logistico Faro SAS</strong> le remite la siguiente lista de despachos que se encuentran a cargo de empresa pendientes por una gestión, para dar continuidad al seguimiento.</p>
                <br>

';


//$remitente="faroavansat@eltransporte.com";
$remitente="oscar.bocanegra@intrared.net";

//Asunto del mensaje
$subject = "A cargo de Empresa - ".$dia_manana ;
$title = "PLANEACIÓN DE PEDIDOS - ".$dia_manana ;
$encabezamsj = 'Señores: '.$transport;
$porcionesemail = explode(",", $paraemails);

$correos = array();

//Destinatarios al mensaje
if(count($porcionesemail)<=0){
    array_push($correos, $porcionesemail);
}else{
    foreach ($porcionesemail as $mailto) {
        array_push($correos, $mailto);
    }
}

//CC copia de emails
//array_push($correos, "arnulfo.castaneda@eltransporte.org");
foreach($addcc as $addccemail){
$emailcc=$addccemail['dir_emailx'];
}

$porcionesemail2 = explode(",", $emailcc);
if(count($porcionesemail2)<=0){
    array_push($correos, $porcionesemail2);
}else{
    foreach ($porcionesemail2 as $mailcc) {
    array_push($correos, $mailcc);
    }
}


$html='';
//print_r($correos);
 

//asigno un archivo adjunto al mensaje
//$mail->AddAttachment($objWriter);

//Cuerpo del mensaje



//envioEmailsTemplate($conexion,COR_PLANTI_NOTPED,$subject,$title,$correos,$encabezamsj,$bodymsj,$html,$filePath);


envioEmailsTemplate($conexion,$url_planti_html,$subject,$title,$correos,$encabezamsj,$bodymsj,$html,$filePath);
/*
$enviomail =
if(!$enviomail)
   {
	   echo "<br/>".$mail->ErrorInfo;	
   }
   else
   {
	   echo "Mensaje enviado correctamente";
   } 
   */
}

switch ($_SERVER['SERVER_NAME']) {
    case 'dev.intrared.net':
        require_once URL_ARCHIV_STANDA."obocanegra/gl/sat-gl-2015/satt_standa/js/lib/plugins/PHPExcel-1.8/Classes/PHPExcel.php";
        break;
    case 'avansatgl.intrared.net':
        require_once URL_ARCHIV_STANDA."satt_standa/js/lib/plugins/PHPExcel-1.8/Classes/PHPExcel.php";
        break;
}

//require_once URL_ARCHIV_STANDA."obocanegra/gl/sat-gl-2015/satt_standa/js/lib/plugins/PHPExcel-1.8/Classes/PHPExcel.php";
$codtransptemp=null;
$exportexcel=array();
$conarray=0;
for($m=0;$m<$numdatos;$m++){
    
    if($codtransptemp==null){
        $codtransptemp=$datos[$m]['nom_transp'];
    }  
     
    if($codtransptemp==$datos[$m]['nom_transp']){
        $codtransptadora=$datos[$m]['nom_transp'];
        $remitentestmp=$datos[$m]['dir_emailx'];   
        array_push($exportexcel, array('num_despac'=>$datos[$m]['num_despac'],
        'cod_manifi'=>$datos[$m]['cod_manifi'],
        'num_placax'=>$datos[$m]['num_placax'],
        'nom_conduc'=>$datos[$m]['nom_conduc'],
        'tel_conduc'=>$datos[$m]['tel_conduc'],
        'ciu_origen'=>$datos[$m]['ciu_origen'],
        'ciu_destino'=>$datos[$m]['ciu_destino'],
        'nom_noveda'=>$datos[$m]['nom_noveda'],
        'obs_preted'=>$datos[$m]['obs_preted'],
        'obs_contro'=>$datos[$m]['obs_contro'],
        'fec_ultnov'=>$datos[$m]['fec_ultnov'],
        'nom_transp'=>$datos[$m]['nom_transp'],
        'dir_emailx'=>$datos[$m]['dir_emailx'])
    );
        $conarray++;
    }else{
    //enviar a excel y mail
    crearexceltmp($exportexcel, $datosAddCc);
    $exportexcel=array();
    $conarray=0;
    sleep(5);

    $codtransptadora=$datos[$m]['nom_transp'];
    $remitentestmp=$datos[$m]['dir_emailx'];   
    array_push($exportexcel, array('num_despac'=>$datos[$m]['num_despac'],
        'cod_manifi'=>$datos[$m]['cod_manifi'],
        'num_placax'=>$datos[$m]['num_placax'],
        'nom_conduc'=>$datos[$m]['nom_conduc'],
        'tel_conduc'=>$datos[$m]['tel_conduc'],
        'ciu_origen'=>$datos[$m]['ciu_origen'],
        'ciu_destino'=>$datos[$m]['ciu_destino'],
        'nom_noveda'=>$datos[$m]['nom_noveda'],
        'obs_preted'=>$datos[$m]['obs_preted'],
        'obs_contro'=>$datos[$m]['obs_contro'],
        'fec_ultnov'=>$datos[$m]['fec_ultnov'],
        'nom_transp'=>$datos[$m]['nom_transp'],
        'dir_emailx'=>$datos[$m]['dir_emailx'])
    );
}
if($m==($numdatos-1)){
    crearexceltmp($exportexcel, $datosAddCc);
    $exportexcel=array();
    $conarray=0;
    sleep(5);
}
        
    
}








} else{
    echo "No hay Registro que Enviar";
    }

 
  