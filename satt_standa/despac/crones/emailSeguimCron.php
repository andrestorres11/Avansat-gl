<?php
//include ("/var/www/html/ap/generadores/satt_standa/lib/general/constantes.inc"); //Produccion
include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/lib/general/constantes.inc"); //Dev

//include (URL_ARCHIV_STANDA."/generadores/satt_faro/constantes.inc"); //Produccion
include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_faro/constantes.inc"); //Dev

//include (URL_ARCHIV_STANDA."/generadores/satt_standa/lib/general/conexion_lib.inc"); //Produccion
include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/lib/general/conexion_lib.inc"); //Dev

//include (URL_ARCHIV_STANDA."/generadores/satt_standa/lib/general/functions.inc"); //Produccion
include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/lib/general/functions.inc"); //Dev

$ano = date("Y");

/*ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);*/
$conexion = new Conexion(HOST,USUARIO, CLAVE, BASE_DATOS);

$transpor = "SELECT cod_tercer FROM ".BASE_DATOS.".tab_config_horlab
                        WHERE hor_ingres !='00:00:00' 
                        AND hor_salida !='23:59:00'
                        GROUP BY cod_tercer";

$consulta = new Consulta($transpor, $conexion);
$transpors = $consulta->ret_matriz('a'); 
foreach ($transpors as $transport) {
   
   $despachos = "SELECT a.num_despac, l.abr_tercer, d.num_placax, 
            CONCAT(f.abr_ciudad,' (',LEFT(j.abr_depart,4),') ') as nom_origen,
            CONCAT(m.abr_ciudad,' (',LEFT(n.abr_depart,4),') ') as nom_destin
            FROM " . BASE_DATOS . ".tab_despac_despac a
            LEFT JOIN " . BASE_DATOS . ".tab_despac_sisext k
            ON a.num_despac = k.num_despac,
               " . BASE_DATOS . ".tab_despac_seguim b,
               " . BASE_DATOS . ".tab_despac_vehige d,
               " . BASE_DATOS . ".tab_genera_ciudad f,
               " . BASE_DATOS . ".tab_genera_depart j,
               " . BASE_DATOS . ".tab_tercer_tercer l,
               " . BASE_DATOS . ".tab_genera_ciudad m,
               " . BASE_DATOS . ".tab_genera_depart n
            WHERE a.num_despac = d.num_despac AND
                     a.num_despac = b.num_despac AND
               a.cod_ciuori = f.cod_ciudad AND
               f.cod_depart = j.cod_depart AND
               f.cod_paisxx = j.cod_paisxx AND

               a.cod_ciudes = m.cod_ciudad AND
               m.cod_depart = n.cod_depart AND
               m.cod_paisxx = n.cod_paisxx AND

               a.fec_salida Is Not Null AND
               a.ind_anulad = 'R' AND
               a.ind_planru = 'S' 
               AND a.fec_llegad Is Null
               AND d.cod_transp =".$transport['cod_tercer']."
               AND d.cod_conduc = l.cod_tercer
               GROUP BY a.num_despac
               LIMIT 10
               ";
   $consulta = new Consulta($despachos, $conexion);
   $despachos = $consulta->ret_matriz('a');
   $novedades= [];

   for ($i=0; $i < count($despachos); $i++) { 
      
      $novedad = getNovedadesDespac($conexion, $despachos[$i]['num_despac'],2);
      if(!empty($novedad)){
         $novedades[$i] = $novedad; 
         $novedades[$i]['num_despac']=$despachos[$i]['num_despac'];
         $novedades[$i]['abr_tercer']=$despachos[$i]['abr_tercer'];
         $novedades[$i]['num_placax']=$despachos[$i]['num_placax'];
         $novedades[$i]['nom_origen']=$despachos[$i]['nom_origen'];
         $novedades[$i]['nom_destin']=$despachos[$i]['nom_destin'];
      }
   }

   $grupoNovedad= [];
   
   foreach ($novedades as $k => &$novedad) {
      $grupoNovedad[$novedad['cod_noveda']][] = $novedad;

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
                  
                              $chartConfigArr = array(
                                 'type' => 'pie',
                                 'data' => array(
                                   'labels' => $labels,
                                   'datasets' => array(
                                     array(
                                       'label' => 'Cantidad Novedades',
                                       'data' => $data,
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
    foreach($correos as $correo){
      $mail->AddAddress( $correo['nom_emailx'] );
    }
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


    

