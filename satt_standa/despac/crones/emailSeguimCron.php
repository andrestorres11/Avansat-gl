<?php

//include ("/var/www/html/ap/generadores/satt_standa/lib/general/constantes.inc"); //Produccion
include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/lib/general/constantes.inc"); //Dev

//include (URL_ARCHIV_STANDA."/generadores/satt_faro/constantes.inc"); //Produccion
include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_faro/constantes.inc"); //Dev

//include (URL_ARCHIV_STANDA."/generadores/satt_standa/lib/general/conexion_lib.inc"); //Produccion
include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/lib/general/conexion_lib.inc"); //Dev

//include (URL_ARCHIV_STANDA."/generadores/satt_standa/lib/general/functions.inc"); //Produccion
include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/lib/general/functions.inc"); //Dev

//include (URL_ARCHIV_STANDA."/generadores/satt_standa/inform/class_despac_trans3.php"); //Produccion
include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/inform/class_despac_trans3.php"); //Dev

$ano = date("Y");



ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);
$conexion = new Conexion(HOST,USUARIO, CLAVE, BASE_DATOS);
#Trasnportadora


$transpor = "SELECT a.cod_tercer, b.tie_contro AS tie_nacion,
                        b.tie_conurb AS tie_urbano
                     FROM ".BASE_DATOS.".tab_config_horlab a
                     INNER JOIN ".BASE_DATOS.".tab_transp_tipser b
                        ON a.cod_tercer = b.cod_transp 
                        WHERE a.hor_ingres !='00:00:00' 
                        AND a.hor_salida !='23:59:00'
                        GROUP BY cod_tercer
                        LIMIT 1";

$consulta = new Consulta($transpor, $conexion);
$transpors = $consulta->ret_matriz('a'); 


foreach ($transpors as $transport) {
  
            
                           $despachos="SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax,
                                 h.abr_tercer AS nom_conduc, h.num_telmov, a.fec_salida, 
                                 a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
                                 IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
                                 CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
                                 CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera 
                              FROM satt_faro.tab_despac_despac a 
                        INNER JOIN satt_faro.tab_despac_vehige b 
                              ON a.num_despac = b.num_despac 
                              AND a.fec_salida IS NOT NULL 
                              AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
                              AND a.ind_planru = 'S' 
                              AND a.ind_anulad = 'R'
                              AND b.ind_activo = 'S' 
                              AND b.cod_transp = '830002183' 
                        AND a.num_despac NOT IN ( 0,1872435,1872439,1872479,1872481,1872482,1872490,1872492 ) 
                              AND a.num_despac IN ( 1872439,18723184,1872415,1872435,1872479,1872481,1872482,1872490,1872492 )
                        INNER JOIN satt_faro.tab_tercer_tercer c 
                              ON b.cod_transp = c.cod_tercer 
                        INNER JOIN satt_faro.tab_genera_ciudad d 
                              ON a.cod_ciuori = d.cod_ciudad 
                              AND a.cod_depori = d.cod_depart 
                              AND a.cod_paiori = d.cod_paisxx 
                        INNER JOIN satt_faro.tab_genera_ciudad e 
                              ON a.cod_ciudes = e.cod_ciudad 
                              AND a.cod_depdes = e.cod_depart 
                              AND a.cod_paides = e.cod_paisxx 
                        INNER JOIN satt_faro.tab_genera_depart f 
                              ON a.cod_depori = f.cod_depart 
                              AND a.cod_paiori = f.cod_paisxx 
                        INNER JOIN satt_faro.tab_genera_depart g 
                              ON a.cod_depdes = g.cod_depart 
                              AND a.cod_paides = g.cod_paisxx 
                        INNER JOIN satt_faro.tab_tercer_tercer h 
                              ON b.cod_conduc = h.cod_tercer 
                        INNER JOIN satt_faro.tab_genera_tipdes i 
                              ON a.cod_tipdes = i.cod_tipdes 
                        LEFT JOIN satt_faro.tab_despac_corona j
                              ON a.num_despac = j.num_dessat
                        LEFT JOIN satt_faro.tab_tercer_tercer k 
                              ON a.cod_client = k.cod_tercer
                           WHERE 1=1     
                           ";
              
               $consulta = new Consulta($despachos, $conexion);
               $despachos = $consulta->ret_matriz('a');
               echo"<pre>";
                  print_r($despachos);
               echo"</pre>";
   

   
   $novedades= [];

   #Se trae ultima novedad por despacho
   for ($i=0; $i < count($despachos); $i++) { 
      
      $novedad = getNovedadesDespac($conexion, $despachos[$i]['num_despac'],2);
      // echo"<pre>";
      //    print_r($novedad);
      // echo"</pre>";
      if(!empty($novedad)){
         $novedades[$i] = $novedad; 
         $novedades[$i]['num_despac']=$despachos[$i]['num_despac'];
         $novedades[$i]['abr_tercer']=$despachos[$i]['abr_tercer'];
         $novedades[$i]['num_placax']=$despachos[$i]['num_placax'];
         $novedades[$i]['nom_origen']=$despachos[$i]['nom_origen'];
         $novedades[$i]['nom_destin']=$despachos[$i]['nom_destin'];
      }
   }


   #Se recorren las novedades y se agrupan por tipo de novedad
   $grupoNovedad= [];
   
   foreach ($novedades as $k => &$novedad) {
      $grupoNovedad[$novedad['cod_noveda']][] = $novedad;

      $ultiReport =  $novedad['fec_crenov'];
      //$mResult[fec_planea] = date ( 'Y-m-d H:i:s', ( strtotime( $mTime, strtotime ( $ultiReport ) ) ) ); #Fecha Planeada para el Seguimiento
      
   }
   $html='
   <!DOCTYPE html>
         <html lang="en">

         <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Correo Notificación</title>
            <style>
               .divpadre {
                     width: 100%;
                     max-width: 920px;
                     min-width: 920px;
                     margin: 25px auto;
                     overflow: hidden;
                     padding-top: 0px;
                     padding-bottom: 0px;
                     margin-bottom: 0px;
                     /*border: 1px solid black;*/
               }
               
               .header {
                     background-color: #f9f9f9;
                     max-width: 100%;
                     min-width: 100%;
                     overflow: hidden;
                     position: relative;
                     display: flex;
                     flex-direction: row;
                     flex-wrap: nowrap;
                     justify-content: space-between;
                     align-items: center;
               }
               
               .content {
                     overflow: hidden;
                     position: relative;
                     padding: 25px;
               }
               .container{
                     display: flex;

               }

               .item{

               }
               
               /*.content-body {
                     border: 1px solid #696969;
               }*/
               
               .header-table {
                     display: flex;
                     flex-direction: row;
                     flex-wrap: nowrap;
                     justify-content: space-between;
                     align-items: center;
                     background-color: #696969;
                     color: #fff;
                     font-family: sans-serif;
                     padding: 10px;
               }
               
               .row-table {
                     display: flex;
                     font-family: sans-serif;
                     padding: 5px;
                     font-size: 14px;
               }
               
               .text-encabeza {
                     margin-left: 25px;
               }
               
               .text-encabeza h4 {
                     font: 170% sans-serif;
                     color: #000;
               }
               
               .imagen-logo {
                     margin-right: 25px;
               }
               
               .end-bottom {
                     text-align: center;
               }
            </style>
            
            
         </head>

         <body>

            
            <div class="divpadre">
               <div class="header">
                     <div class="imagen-logo" style="min-width:25%;margin-top:9px;">
                        <img width="180px" src="./Logo CL Faro.png">
                     </div>
                     <div class="text-encabeza" style="min-width:
                     75%;margin-top:9px;">
                        <h4>INFORME DE ESTADO DE SEGUIMIENTO</h4>
                     </div>   
               </div>
               <div class="content">
                     <h2>'.$transport['cod_tercer'].'</h2>
                     <p>'.date("d.m.y").'</p>
                     <p>Centro logistico FARO informa que se recibe el estado de la plataforma del servicio del seguimiento de monitores activo contratado de <b>06:00 pm</b> a <b>06:00 am</b> de siguiente manera</p>
               </div>
               <div class="container" style="width:100%;">
                     <div class="item">
                        <table style="width:30%; border: 1px solid black;">
                           <tr>
                                 <th>ESTADO DE SEGUIMIENTO</th>
                           </tr>
                           <tr>
                              <td>';
                              if(count($grupoNovedad) > 0){
                                 foreach($grupoNovedad as $key => $novedad){
                                    
                                    $labels[]=$novedad[0]['nom_noveda'];
                                    $data[]=count($novedad);
                                 }
                                    
                              }
                              
                              $data=array(0=>1,1=>2,2=>1,3=>8);
                              
                              echo "<pre>";
                                 print_r($data);
                              echo "</pre>";
                              
                                    
                              $chartConfigArr = array(
                                 'type' => 'pie',
                                 'data' => array(
                                   'labels' => $labels,
                                   'datasets' => array(
                                     array(
                                       'label' => 'Cantidad Novedades',
                                       'data' => $data,
                                       'backgroundColor'=> ['#FFFC33', '#9fb4f3', '#33FFE9', '#FF5833']
                                     )
                                    )
                                    
                                 )
                                 
                               );
                               $chartConfig = json_encode($chartConfigArr);
                                 $chartUrl = 'https://quickchart.io/chart?w=300&h=300&c=' . urlencode($chartConfig);
                              $html .='
                                 <img src="'.$chartUrl.'">
                              </td>
                           </tr>
                           </table>
                     </div>
                     <div class="item">
                        <h3>NOVEDADES</h3>';
                        foreach ($grupoNovedad as $key => $novedad) {
                        $html .=
                        '<table style="width:70%; border: 1px solid black;">
                           <tr>
                                 <th><h5>'.$novedad[0]['nom_noveda'].'</h5></th>
                           </tr>
                           
                           <tr>
                                 <th>Placa</th>
                                 <th>Origen</th>
                                 <th>Destino</th>
                                 <th>Condultor</th>
                                 <th>Fecha y  hora novedad</th>
                                 <th>Sitio de seguimiento</th>
                                 <th>Observacion</th>
                           </tr>';
                           foreach ($novedad as $key => $novedadAgrupada) {
                              $html .='<tr>
                              <td>'.$novedadAgrupada['num_placax'].'</td>
                              <td>'.$novedadAgrupada['nom_origen'].'</td>
                              <td>'.$novedadAgrupada['nom_destin'].'</td>
                              <td>'.$novedadAgrupada['abr_tercer'].'</td>
                              <td>'.$novedadAgrupada['fec_noveda'].'</td>
                              <td>'.$novedadAgrupada['nom_contro'].'</td>
                              <td>'.$novedadAgrupada['obs_noveda'].'</td>
                              </tr>'; 
                           }
                           $html .='
                        </table>';
                        
                        }
                        $html .=
                     '</div>
               </div>
               <div class="contentImg">

               </div>
            </div>
            <div class="Imagen">
               <img width="180px" src="./banner-torre+de+control-960w.jpg">
            </div>
         </body>
                    
         </html>
   '; 


   echo($html);
   //////////////////////////////////////////////////////////////////////
   $query = "SELECT a.dir_emailx FROM ".BASE_DATOS.".tab_genera_concor a 
   WHERE a.num_remdes =".$transport['cod_tercer']."';";

   $consulta = new Consulta($query, $conexion);
   $correos = $consulta->ret_matriz('a'); 

    //require_once(URL_ARCHIV_STANDA."/generadores/satt_standa/planti/class.phpmailer.php"); //Produccion
   require_once("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/planti/class.phpmailer.php"); //Dev
    $mail = new PHPMailer();

    $mail->Host = "localhost";
    $mail->From = "supervisores@eltransporte.org";
    $mail->FromName = "INFORME";
    $mail->Subject = "Informes";
   //  foreach($correos as $correo){
   //    $mail->AddAddress( $correo['nom_emailx'] );
   //  }

    $mail->AddAddress('anfemardel@gmail.com');
    $mail->Body = $html;
    $mail->IsHTML( true );
    $exito = $mail->Send();

  //Si el mensaje no ha podido ser enviado se realizaran 4 intentos mas como mucho 
  //para intentar enviar el mensaje, cada intento se hara 5 segundos despues 
  //del anterior, para ello se usa la funcion sleep	
  $intentos=1; 
  while ((!$exito) && ($intentos < 5)) {
	sleep(5);
     	//echo $mail->ErrorInfo;
     	$exito = $mail->Send();
     	$intentos=$intentos+1;	
   }
 
   if(!$exito)
   {
	   echo "<br/>".$mail->ErrorInfo;	
   }
   else
   {
	   echo "Mensaje enviado correctamente";
   } 
}

// $fecha_anterior = date("Y-m-d",strtotime(date("Y-m-d")."- 15 days"))." 23:59:59";
// $logo = URL_LOGOPNG;
// $imgFooter = URL_IMGFOOTER;
  
    
    
   //  //$tmpl_file = URL_ARCHIV_STANDA.'/generadores/satt_standa/planti/pla_notifi_alerim.html'; //PRODUCCION
   //  $tmpl_file = '/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/planti/pla_inform_novdes.html'; //DEV
   //  $thefile = implode("", file( $tmpl_file ) );
   //  $thefile = addslashes($thefile);
   //  $thefile = "\$r_file=\"".$thefile."\";";
   //  eval( $thefile );
   //  $mHtml = $r_file;


    

