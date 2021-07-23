<?php
include ("/var/www/html/ap/generadores/satt_standa/lib/general/constantes.inc"); //Produccion
//include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/lib/general/constantes.inc"); //Dev

include (URL_ARCHIV_STANDA."/generadores/satt_faro/constantes.inc"); //Produccion
//include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_faro/constantes.inc"); //Dev

include (URL_ARCHIV_STANDA."/generadores/satt_standa/lib/general/conexion_lib.inc"); //Produccion
//include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/lib/general/conexion_lib.inc"); //Dev

include (URL_ARCHIV_STANDA."/generadores/satt_standa/lib/general/functions.inc"); //Produccion
//include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/lib/general/functions.inc"); //Dev

include (URL_ARCHIV_STANDA."/generadores/satt_standa/inform/class_despac_trans3.php"); //Produccion
//include ("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/inform/class_despac_trans3.php"); //Dev
/*ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);*/
class EmailSeguim
{
    public $conexion;
    private static $cHoy,
					$hora,
                    $cTipDespac = '""',
					$cTime = array( 'ind_desurb' => '30', 'ind_desnac' => '60' ),
                    $cTipDespacContro = '""'; #Tipo de Despachos asignados al controlador, Aplica para cTypeUser[tip_perfil] == 'CONTROL';
    function __construct()
	{
        $this->conexion = new Conexion(HOST,USUARIO, CLAVE, BASE_DATOS);
        self::$cHoy = date("Y-m-d H:i:s");
		self::$hora = date("H");
        $this->principal();
        
    }

    public function principal(){
        $transpors= self::getTransports();
        
        foreach ($transpors as $transport) {
                                
            $despachos= $this->getDespacTransi2($transport);
            $mData = self::calTimeAlarma( $despachos, $transport, 1 );


            
            $novedades= [];

            #Se trae ultima novedad por despacho
            for ($i=0; $i < count($despachos); $i++) { 

                $novedad = getNovedadesDespac($this->conexion, $despachos[$i]['num_despac'],2);
                
                if(!empty($novedad)){
                    $novedades[$i] = $novedad; 
                    $novedades[$i]['num_despac']=$despachos[$i]['num_despac'];
                    $novedades[$i]['nom_genera']=$despachos[$i]['nom_genera'];
                    $novedades[$i]['num_placax']=$despachos[$i]['num_placax'];
                    $novedades[$i]['ciu_origen']=$despachos[$i]['ciu_origen'];
                    $novedades[$i]['ciu_destin']=$despachos[$i]['ciu_destin'];
                }
            }

            #Se recorren las novedades y se agrupan por tipo de novedad
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
                    font-family: Arial;
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
                .color-letra {
                    color: #516CC6;
                }
                .content {

                    overflow: hidden;
                    position: relative;
                    padding: 25px;
                }
                .container{
                    overflow: hidden;
                    position: relative;
                    padding: 0px;
                    margin-botton: 1px;

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
                    padding-left:15px;
                    margin-right: 25px;
                }

                .end-bottom {
                    text-align: center;
                }
                .table {
                    border: 1px solid #6b6b6b;
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                    height: 30%;
                    }
                    
                td, th {
                    border: 1px solid #6b6b6b;
                    text-align: left;
                    padding: 8px;
                }
                .tr{
                    border: 1px solid #6b6b6b;
                    text-align: left;
                    padding: 8px;
                    background-color: #f9f9f9;
                    width: 100%;
                }
                .tr-title{
                    border: 1px solid #6b6b6b;
                    text-align: left;
                    padding: 8px;
                    background-color: #D2D3D4;
                    margin-bottom: auto;
                    width: 100%;
                    font-size: small;
                }
                .tr-sub-title{
                    border: 1px solid #6b6b6b;
                    text-align: left;
                    padding: 8px;
                    background-color: #E4E5E8;
                    margin-bottom: auto;
                    width: 100%;
                    margin-block: -1px;
                    font-size: small;
                }
                .img-grafi{
                    width: 35%;
                }
                </style>


                </head>

                <body>
                    <div class="divpadre">
                        <div class="header">
                            <div class="imagen-logo" style="min-width:25%;margin-top:9px;">
                                <img width="100%" src='.LOGOFARO.'>
                            </div>
                            <div class="text-encabeza color-letra" style="min-width:
                            75%;margin-top:9px;">
                                <h4>INFORME DE ESTADO DE SEGUIMIENTO</h4>
                            </div>   
                        </div>
                        <div class="content">
                            <h4>'.strtoupper($transport['abr_tercer']).'</h4>
                            <p>'.self::$cHoy.'</p>
                            <p>Centro logistico FARO informa que se recibe el estado de la plataforma del servicio del seguimiento de monitores activo contratado de <b>06:00 pm</b> a <b>06:00 am</b> de siguiente manera</p>
                        </div>
                        <div class="container" ">
                            <div class="item">
                                <table class="table">
                                    <tr class="tr-title">
                                        <th class="tr-title" colspan="7">ESTADO DE SEGUIMIENTO</th>
                                    </tr>
                                    <tr>
                                        <td style="width:100%; height: 30%;" colspan="7"> ';
                                        $labels=array(
                                            "SIN RETRASO",
                                            "00 A 30",
                                            "31 A 60",
                                            "61 A 90",
                                            "91 MAS",
                                            "PERNOTACION"
                                        );
                                        
                                        $data=array(
                                            $mData[sin_retras],
                                            $mData[con_00A30x],
                                            $mData[con_31A60x],
                                            $mData[con_61A90x],
                                            $mData[con_91Amas],
                                            $mData[est_pernoc]
                                        );
                                        $chartConfigArr = array(
                                            'type' => 'pie',
                                            'data' => array(
                                            'labels' => $labels,
                                            'datasets' => array(
                                                array(
                                                'label' => 'Cantidad Novedades',
                                                'data' => $data,
                                                'backgroundColor'=> [
                                                                    '#F6F2F1', 
                                                                    '#FFFF66', 
                                                                    '#FF9900', 
                                                                    '#FF0000', 
                                                                    '#CC33FF',
                                                                    '#951264'
                                                                ]
                                                )
                                                )
                                                
                                            )
                                            
                                        );
                                        $chartConfig = json_encode($chartConfigArr);
                                            $chartUrl = 'https://quickchart.io/chart?w=300&h=300&c=' . urlencode($chartConfig);
                                        $html .='
                                            <center> 
                                                <img class="img-grafi" src="'.$chartUrl.'">
                                            </center>
                                        </td>
                                    </tr>
                                    <tr class="tr-title">
                                        <th class="tr-title" colspan="7">NOVEDADES</th>
                                    </tr>';
                                
                                    foreach ($grupoNovedad as $key => $novedad) {
                                    $html .=
                                        '
                                            <tr>
                                                <th class="tr-sub-title" colspan="7">'.$novedad[0]['nom_noveda'].'</th>
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
                                                        <td>'.$novedadAgrupada['ciu_origen'].'</td>
                                                        <td>'.$novedadAgrupada['ciu_destin'].'</td>
                                                        <td>'.$novedadAgrupada['nom_genera'].'</td>
                                                        <td>'.$novedadAgrupada['fec_noveda'].'</td>
                                                        <td>'.$novedadAgrupada['nom_contro'].'</td>
                                                        <td>'.$novedadAgrupada['obs_noveda'].'</td>
                                                    </tr>'; 
                                        }
                                    
                                    }
                                    $html .=
                                    '
                                </table>
                            </div>
                        </div>
                        <div class="footer">
                            <img width="100%" src="'.FOOTERFARO.'">
                        </div>
                    </div>    
                </body>
                    
                </html>
            '; 


            echo($html);

            $query = "SELECT a.dir_emailx FROM ".BASE_DATOS.".tab_genera_concor a 
            WHERE a.num_remdes ='".$transport['cod_transp']."'
            OR a.num_remdes ='';";
            $consulta = new Consulta($query,$this->conexion);
            $correos = $consulta->ret_matriz('a'); 

            //require_once(URL_ARCHIV_STANDA."/generadores/satt_standa/planti/class.phpmailer.php"); //Produccion
            require_once("/var/www/html/ap/amartinez/FARO/sat-gl-2015/satt_standa/planti/class.phpmailer.php"); //Dev
            $mail = new PHPMailer();

            $mail->Host = "localhost";
            $mail->From = "supervisores@eltransporte.org";
            $mail->FromName = "ESTADO DE SEGUIMIENTO";
            $mail->Subject = "ESTADO DE SEGUIMIENTO";
            foreach($correos as $correo){
                $mail->AddAddress( $correo['dir_emailx'] );
            }
            //$mail->AddAddress('anfemardel@gmail.com');
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
                        
    }


    #Trae Transportadoras
    public function getTransports(){
		$hora=self::$hora;
        $transpor = "SELECT c.ind_segprc,c.ind_segcar, 
                        c.ind_segctr,c.ind_segtra, c.ind_segdes, 
                        c.cod_transp, c.num_consec, d.nom_tipser, 
                        UPPER(e.abr_tercer) AS nom_transp, c.tie_contro AS tie_nacion,
                        c.tie_conurb AS tie_urbano, c.tie_desurb, 
                        c.tie_desnac, c.tie_desimp, c.tie_desexp, 
                        c.tie_destr1, c.tie_destr2, c.cod_tipser, 
                        c.ind_conper, c.hor_pe1urb, c.hor_pe2urb, 
                        c.hor_pe1nac, c.hor_pe2nac, c.hor_pe1imp, 
                        c.hor_pe2imp, c.hor_pe1exp, c.hor_pe2exp, 
                        c.hor_pe1tr1, c.hor_pe2tr1, c.hor_pe1tr2, 
                        c.hor_pe2tr2, 
                        c.tgl_contro AS tgl_nacion, c.tgl_contro AS tgl_urbano,
                        c.tgl_prcnac AS tgl_nacprc, c.tgl_prcurb AS tgl_urbprc,
                        e.abr_tercer
                FROM ".BASE_DATOS.".tab_transp_tipser c 
            INNER JOIN ".BASE_DATOS.".tab_genera_tipser d 
                     ON c.cod_tipser = d.cod_tipser 
            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e 
                     ON c.cod_transp = e.cod_tercer 
            INNER JOIN (	  SELECT cod_transp , MAX(num_consec) AS num_consec 
                                 FROM ".BASE_DATOS.".tab_transp_tipser  
                             GROUP BY cod_transp 
                        ) f ON c.cod_transp = f.cod_transp AND c.num_consec = f.num_consec
            INNER JOIN ".BASE_DATOS.".tab_config_horlab g
                        ON g.cod_tercer = c.cod_transp 
                        AND g.hor_ingres !='00:00:00' 
                        AND g.hor_salida !='23:59:00' 
						AND ( DATE_FORMAT(g.hor_ingres,'%H') = '".$hora."' OR DATE_FORMAT(g.hor_salida,'%H') = '".$hora."')
                    GROUP BY c.cod_transp";

        $consulta = new Consulta($transpor, $this->conexion);
        $transpors = $consulta->ret_matriz('a'); 

        return $transpors;                
    }


    /*! \fn: getDespacTransi2
	 *  \brief: Trae los despachos para las empresas que tienen parametrizado Cargue, Transito y Descargue
	 *  \author: Ing. Fabian Salinas
	 *	\date: 07/07/2015
	 *	\date modified: dia/mes/a�o
	 *  \param: mTransp  Array  Informacion transportadora
	 *  \return: Matriz
	 */
	public function getDespacTransi2( $mTransp )
	{
		$mDespacCarDes = self::getDespacDescar( $mTransp, 'list2', true ); #Despachos en Etapas Cargue y Descargue
		$mDespacCarDes = trim($mDespacCarDes, ',');

		#Despachos en ruta  
		$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat 
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND ( xx.fec_salida IS NOT NULL  )
						AND yy.cod_transp = '".$mTransp[cod_transp]."' ";
		//$mSql .= ($_REQUEST['cod_client'] != NULL || $_REQUEST['cod_client'] != ''?" AND xx.cod_client IN (".str_replace(array('"",','"'),array('',''),$_REQUEST['cod_client']).") ":"");

		$mConsult = new Consulta( $mSql, $this->conexion );
		$mDespac = $mConsult -> ret_matrix('a');

		if( sizeof($mDespac) < 1 )
			return false;

		$mDespac = join( ',', GetColumnFromMatrix( $mDespac, 'num_despac' ) );

		#Despachos en Etapa Transito Filtro 3
		$mSql = "( /* Despachos con novedades etapa Descargue en Sitio */
						SELECT a.num_despac 
						  FROM ".BASE_DATOS.".tab_despac_noveda a 
					INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
							ON a.cod_noveda = b.cod_noveda 
						 WHERE a.num_despac IN ( {$mDespac} ) 
						   AND b.cod_etapax IN ( 3 )
					  GROUP BY a.num_despac
				)
				UNION 
				( /* Despachos con novedades etapa Descargue antes de Sitio */
						SELECT c.num_despac 
						  FROM ".BASE_DATOS.".tab_despac_contro c 
					INNER JOIN ".BASE_DATOS.".tab_genera_noveda d 
							ON c.cod_noveda = d.cod_noveda 
						 WHERE c.num_despac IN ( {$mDespac} ) 
						   AND d.cod_etapax IN ( 3 )
				) ";
		$mConsult = new Consulta( $mSql, $this->conexion );
		//echo "<pre style='display:none;' id='mDespacTrasiandres'>"; print_r($mSql); echo "</pre>";
		$mDespacTrasi = $mConsult -> ret_matrix('a');
		$mDespacTrasi = join( ',', GetColumnFromMatrix( $mDespacTrasi, 'num_despac' ) );
		$mDespacTrasi = $mDespacTrasi ? $mDespacTrasi : '0';



		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax,
						h.abr_tercer AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					AND a.fec_salida IS NOT NULL 
					AND a.fec_salida <= NOW() 
					AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
					AND a.ind_planru = 'S' 
					AND a.ind_anulad = 'R'
					AND b.ind_activo = 'S' 
					AND b.cod_transp = '".$mTransp['cod_transp']."' 
			".( $mDespacCarDes == '' ? "" : " AND a.num_despac NOT IN ( {$mDespacCarDes} ) " )."
					AND a.num_despac IN ( {$mDespacTrasi} )
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
					 ON b.cod_transp = c.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
					AND a.cod_depori = d.cod_depart 
					AND a.cod_paiori = d.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
					 ON a.cod_ciudes = e.cod_ciudad 
					AND a.cod_depdes = e.cod_depart 
					AND a.cod_paides = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
					 ON a.cod_depori = f.cod_depart 
					AND a.cod_paiori = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
					 ON a.cod_depdes = g.cod_depart 
					AND a.cod_paides = g.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
					 ON b.cod_conduc = h.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
					 ON a.cod_tipdes = i.cod_tipdes 
			 LEFT JOIN ".BASE_DATOS.".tab_despac_corona j
			 	 	 ON a.num_despac = j.num_dessat
			 LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k 
					 ON a.cod_client = k.cod_tercer
				  WHERE 1=1     ";

		#Filtros por Formulario
		#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
		$mSql .= self::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". self::$cTipDespac .") " : "";

		#Filtros por usuario
		$mSql .= self::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. self::$cTipDespacContro .') ' : '';	
		

		//echo "<pre style='display:none;' id='Transito2'>"; print_r($mSql); echo "</pre>";

		$mConsult = new Consulta( $mSql, $this->conexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = self::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = self::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );
            $_REQUEST[ind_limpio]=1;
			#warning1
			if( $_REQUEST[ind_limpio] === '1' || $_REQUEST[ind_limpio] === '0' )
			{
				if(		( $_REQUEST[ind_limpio] === '1' && ($mData[ind_limpio] === '1' || $mData[ind_limpio] == '') ) #Despachos Limpios
					||	( $_REQUEST[ind_limpio] === '0' && $mData[ind_limpio] === '0' ) #Despachos no Limpios 
				)
				{
					$mResult[$j] = $mDespac[$i];
					$mResult[$j][can_noveda] = $mData[can_noveda];
					$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
					$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
					$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
					$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
					$mResult[$j][fec_planea] = $mData[fec_planea];
					$mResult[$j][fec_planGl] = $mData[fec_planGl];
					$j++;
				}
			}
			else
			{
				$mResult[$j] = $mDespac[$i];
				$mResult[$j][can_noveda] = $mData[can_noveda];
				$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
				$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
				$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
				$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
				$mResult[$j][fec_planea] = $mData[fec_planea];
				$mResult[$j][fec_planGl] = $mData[fec_planGl];
				$j++;
			}
		}
		
		return $mResult;
	}
	/*! \fn: getDespacDescar
	 *  \brief: Trae los despachos en Etapa Descargue
	 *  \author: Ing. Fabian Salinas
	 *	\date: 06/07/2015
	 *	\date modified: dia/mes/a�o
	 *  \param: $mTransp  Array  Informacion transportadora
	 *  \param: $mTipReturn  String   array = Retorna array con n�mero de los despachos; list = Retorna lista con n�mero de los despachos; list2 = Lista de Despachos Pertenecientes a etapas Cargue y Descargue;
	 *	\param: mSinFiltro  Boolean  true = No filtra por datos que llegas del formulario $_REQUEST
	 *  \return: Matriz, Array o String (Segun parametro mTipReturn)
	 */
	public function getDespacDescar( $mTransp, $mTipReturn = NULL, $mSinFiltro = false )
	{
        
		$mDespacCargue = self::getDespacCargue( $mTransp, 'list', true );
        
		$mDespacCargue = ($mDespacCargue == NULL ? '0' : $mDespacCargue );

		#Despachos en ruta que ya finalizaron etapa Cargue
		$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac 
						AND xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND yy.cod_transp = '".$mTransp[cod_transp]."' 
						AND xx.num_despac NOT IN ( {$mDespacCargue} ) ";
		//$mSql .= ($_REQUEST['cod_client'] != NULL || $_REQUEST['cod_client'] != ''?" AND xx.cod_client IN (".str_replace(array('"",','"'),array('',''),$_REQUEST['cod_client']).") ":"");

		$mConsult = new Consulta( $mSql, $this->conexion );
		$mDespac = $mConsult -> ret_matrix('a');
        
		if( sizeof($mDespac) < 1 )
			return false;

		$mDespac = join( ',', GetColumnFromMatrix( $mDespac, 'num_despac' ) );

		#Despachos en Etapa Descargue Filtro 1
		$mSql = "( /* Despachos con novedades etapa Descargue en Sitio */
						SELECT a.num_despac 
						  FROM ".BASE_DATOS.".tab_despac_noveda a 
					INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
							ON a.cod_noveda = b.cod_noveda 
						 WHERE a.num_despac IN ( {$mDespac} ) 
						   AND b.cod_etapax IN ( 4, 5 )
					  GROUP BY a.num_despac
				)
				UNION 
				( /* Despachos con novedades etapa Descargue antes de Sitio */
						SELECT c.num_despac 
						  FROM ".BASE_DATOS.".tab_despac_contro c 
					INNER JOIN ".BASE_DATOS.".tab_genera_noveda d 
							ON c.cod_noveda = d.cod_noveda 
						 WHERE c.num_despac IN ( {$mDespac} ) 
						   AND d.cod_etapax IN ( 4, 5 )
				) ";
		$mConsult = new Consulta( $mSql, $this->conexion );
		$mDespacDes = $mConsult -> ret_matrix('a');
		$mDespacDes = join( ',', GetColumnFromMatrix( $mDespacDes, 'num_despac' ) );

		# Despachos para recorrer y verificar si estan en etapa Descargue Filtro 2
		$mSql = "	 SELECT a.num_despac, a.cod_tipdes 
					   FROM ".BASE_DATOS.".tab_despac_despac a 
					  WHERE a.num_despac IN ( {$mDespac} ) ";
		$mSql .= $mDespacDes != "" ? " AND a.num_despac NOT IN ( {$mDespacDes} ) " : "";
		$mConsult = new Consulta( $mSql, $this->conexion );
		$mDespac = $mConsult -> ret_matrix('i');

        
		$mDespacDes = $mDespacDes == "" ? '""' : $mDespacDes;

		#Recorre Despachos Para verificar Filtro 2
		foreach ($mDespac as $row)
		{
			$mNextPC = getNextPC( $this->conexion, $row[0] ); #Siguiente PC del Plan de ruta
			$mRutaDespac = getControDespac( $this->conexion, $row[0] ); # Ruta del Despacho
			$mPosPC = (sizeof($mRutaDespac))-1; #Posicion Ultimo PC


			if(	( $mNextPC[cod_contro] == $mRutaDespac[$mPosPC][cod_contro] ) #Siguiente PC igual al ultimo PC del plan de ruta
				|| ( $mNextPC[cod_contro] == $mRutaDespac[($mPosPC-1)][cod_contro] && $mNextPC[ind_ensiti] == '1' ) #Siguiente PC igual al penultimo PC del plan de ruta con Novedades en Sitio
			  )
			{
			  	$mDespacDes .= ','.$row[0];
				$i++;
			}
			elseif(	( $mNextPC[cod_contro] == $mRutaDespac[($mPosPC-1)][cod_contro] && $mNextPC[ind_ensiti] == '0' ) #Siguiente PC igual al penultimo PC del plan de ruta con Novedades antes de Sitio
					 || ( $mNextPC[cod_contro] == $mRutaDespac[($mPosPC-2)][cod_contro] && $mNextPC[ind_ensiti] == '1' ) #Siguiente PC igual al antepenultimo PC del plan de ruta con Novedades en Sitio
				  )
			{
				$mTime = self::getTimeDescargue( $mTransp, $row[1] );
				$mTime = "-".$mTime." minute";
				$mDate = date ( 'Y-m-d H:i:s', ( strtotime( $mTime, strtotime ( $mNextPC[fec_planea] ) ) ) ); #Fecha Planeada para iniciar Seguimiento en Descargue

				if( $mDate <= self::$cHoy )
					$mDespacDes .= ','.$row[0];
			}
		}

		$mDespacDes = trim($mDespacDes, ',');

		#Informacion de los despachos en Etapa Descargue
		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax,
						h.abr_tercer AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					AND a.num_despac IN ( {$mDespacDes} )
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
					 ON b.cod_transp = c.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
					AND a.cod_depori = d.cod_depart 
					AND a.cod_paiori = d.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
					 ON a.cod_ciudes = e.cod_ciudad 
					AND a.cod_depdes = e.cod_depart 
					AND a.cod_paides = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
					 ON a.cod_depori = f.cod_depart 
					AND a.cod_paiori = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
					 ON a.cod_depdes = g.cod_depart 
					AND a.cod_paides = g.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
					 ON b.cod_conduc = h.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
					 ON a.cod_tipdes = i.cod_tipdes
			 LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k 
					 ON a.cod_client = k.cod_tercer 
				  WHERE 1=1 ";

		if( $mSinFiltro == false )
		{
			#Filtros por Formulario
			#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
			$mSql .= self::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". self::$cTipDespac .") " : "";

			#Filtros por usuario
			$mSql .= self::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. self::$cTipDespacContro .') ' : '';	
		}

		$mConsult = new Consulta( $mSql, $this->conexion );
		$mDespac = $mConsult -> ret_matrix('a');
        
		$mTipValida = self::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		//for( $i=0; $i<sizeof($mDespac); $i++ )
		//{
			//$mData = self::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

			#warning1
			// if( ($_REQUEST[ind_limpio] === '1' || $_REQUEST[ind_limpio] === '0') && $mSinFiltro == false )
			// {
			// 	if(		( $_REQUEST[ind_limpio] === '1' && ($mData[ind_limpio] === '1' || $mData[ind_limpio] == '') ) #Despachos Limpios
			// 		||	( $_REQUEST[ind_limpio] === '0' && $mData[ind_limpio] === '0' ) #Despachos no Limpios 
			// 	)
			// 	{
			// 		$mResult[$j] = $mDespac[$i];
			// 		$mResult[$j][can_noveda] = $mData[can_noveda];
			// 		$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
			// 		$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
			// 		$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
			// 		$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
			// 		$mResult[$j][fec_planea] = $mData[fec_planea];
			// 		$mResult[$j][fec_planGl] = $mData[fec_planGl];
			// 		$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut];
			// 		$j++;
			// 	}
			// }
			// else
			// {
			// 	$mResult[$j] = $mDespac[$i];
			// 	$mResult[$j][can_noveda] = $mData[can_noveda];
			// 	$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
			// 	$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
			// 	$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
			// 	$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
			// 	$mResult[$j][fec_planea] = $mData[fec_planea];
			// 	$mResult[$j][fec_planGl] = $mData[fec_planGl];
			// 	$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut];
			// 	$j++;
			// }
		//}
        
		# Resultados de la funcion
		if( $mTipReturn == 'array' )
			return GetColumnFromMatrix( $mDespac, 'num_despac' );
		elseif( $mTipReturn == 'list' )
			return join(',', GetColumnFromMatrix($mDespac, 'num_despac') );
		elseif( $mTipReturn == 'list2' )
			return $mResult = $mDespacCargue.','.( join(',', GetColumnFromMatrix($mDespac, 'num_despac') )  );
		else
			return $mResult;

                
	}

    /*! \fn: getDespacCargue
	 *  \brief: Trae los despachos en Etapa Cargue
	 *  \author: Ing. Fabian Salinas
	 *	\date: 18/06/2015
	 *	\date modified: dia/mes/a�o
	 *  \param: $mTransp  Array  Informacion transportadora
	 *  \param: $mTipReturn  String   array = Retorna array con n�mero de los despachos; list = Retorna lista con n�mero de los despachos;
	 *	\param: mSinFiltro  Boolean  true = No filtra por datos que llegas del formulario $_REQUEST
	 *  \return: Matriz, Array o String (Segun parametro mTipReturn)
	 */
	public function getDespacCargue( $mTransp, $mTipReturn = NULL, $mSinFiltro = false )
	{
       
		$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat 
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND ( xx.fec_salida IS NOT NULL   )
						AND yy.cod_transp = '".$mTransp[cod_transp]."' ";

		//$mSql .= ($_REQUEST['cod_client'] != NULL || $_REQUEST['cod_client'] != ''?" AND xx.cod_client IN (".str_replace(array('"",','"'),array('',''),$_REQUEST['cod_client']).") ":"");

				//echo "<pre style='display:none'>"; print_r($mSql); echo "</pre>";
		$mConsult = new Consulta( $mSql, $this->conexion );
		$mDespac = $mConsult -> ret_matrix('a');
        
		if( sizeof($mDespac) < 1 )
			return false;

		$mDespac = join( ',', GetColumnFromMatrix( $mDespac, 'num_despac' ) ); #Despachos en ruta Sin hora salida del sistema

		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax, 
						UPPER(h.abr_tercer) AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					AND a.num_despac IN ( {$mDespac} )
					AND a.num_despac NOT IN ( /* Despachos con novedades etapa Transito y Descargue en Sitio */
													SELECT da.num_despac 
													  FROM ".BASE_DATOS.".tab_despac_noveda da 
												INNER JOIN ".BASE_DATOS.".tab_genera_noveda db 
														ON da.cod_noveda = db.cod_noveda 
													 WHERE da.num_despac IN ( {$mDespac} ) 
													   AND db.cod_etapax NOT IN ( 0, 1, 2 )
											)
					AND a.num_despac NOT IN ( /* Despachos con novedades etapa Transito y Descargue antes de Sitio */
													SELECT ea.num_despac 
													  FROM ".BASE_DATOS.".tab_despac_contro ea 
												INNER JOIN ".BASE_DATOS.".tab_genera_noveda eb 
														ON ea.cod_noveda = eb.cod_noveda 
													 WHERE ea.num_despac IN ( {$mDespac} ) 
													   AND eb.cod_etapax NOT IN ( 0, 1, 2 )
											)
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c 
					 ON b.cod_transp = c.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d 
					 ON a.cod_ciuori = d.cod_ciudad 
					AND a.cod_depori = d.cod_depart 
					AND a.cod_paiori = d.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e 
					 ON a.cod_ciudes = e.cod_ciudad 
					AND a.cod_depdes = e.cod_depart 
					AND a.cod_paides = e.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart f 
					 ON a.cod_depori = f.cod_depart 
					AND a.cod_paiori = f.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
					 ON a.cod_depdes = g.cod_depart 
					AND a.cod_paides = g.cod_paisxx 
			 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer h 
					 ON b.cod_conduc = h.cod_tercer 
			 INNER JOIN ".BASE_DATOS.".tab_genera_tipdes i 
					 ON a.cod_tipdes = i.cod_tipdes 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_corona z 
					 ON a.num_despac = z.num_dessat 
			  LEFT JOIN ".BASE_DATOS.".tab_despac_sisext y
			  		 ON a.num_despac = y.num_despac
			  LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer k 
					 ON a.cod_client = k.cod_tercer
				  WHERE 1=1  AND y.ind_cumcar IS NOT NULL AND y.fec_cumcar IS NOT NULL
				  ";

		// if( ($_REQUEST["Option"] == "infoPreCargue" || $_REQUEST["Option"]  == 'detailBand' ) && $_REQUEST["pun_cargue"] != '')
		// {
		 
		// 	$mSql .=" AND a.cod_ciuori IN (". $_REQUEST['pun_cargue'] .") /*cargue*/";		 
		// }

		// if( $mSinFiltro == false )
		// {
		// 	#Filtros por Formulario
		// 	#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
		// 	$mSql .= self::$cTipDespac != '""' ? " AND a.cod_tipdes IN (". self::$cTipDespac .") " : "";

		// 	#Filtros por usuario
		// 	$mSql .= self::$cTipDespacContro != '""' ? 'AND a.cod_tipdes IN ('. self::$cTipDespacContro .') ' : '';	
		// }
		//echo "<pre style='display:none'>"; print_r($mSql); echo "</pre>";
		$mConsult = new Consulta( $mSql, $this->conexion );
		$mDespac = $mConsult -> ret_matrix('a');
        
		$mTipValida = self::tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = self::getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

			#Verifica que el siguiente PC sea el Primero o Segundo ( Parametro Despachos Etapa Cargue )
			if( $mData[sig_pcontr][cod_contro] == $mData[pla_rutaxx][0][cod_contro] || $mData[sig_pcontr][cod_contro] == $mData[pla_rutaxx][1][cod_contro] )
			{
				#warning1
				if( ($_REQUEST[ind_limpio] === '1' || $_REQUEST[ind_limpio] === '0') && $mSinFiltro == false )
				{
					if(		( $_REQUEST[ind_limpio] === '1' && ($mData[ind_limpio] === '1' || $mData[ind_limpio] == '') ) #Despachos Limpios
						||	( $_REQUEST[ind_limpio] === '0' && $mData[ind_limpio] === '0' ) #Despachos no Limpios 
					)
					{
						$mResult[$j] = $mDespac[$i];
						$mResult[$j][can_noveda] = $mData[can_noveda];
						$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
						$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
						$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
						$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
						$mResult[$j][fec_planea] = $mData[fec_planea];
						$mResult[$j][fec_planGl] = $mData[fec_planGl];
						$j++;
					}
				}
				else
				{
					$mResult[$j] = $mDespac[$i];
					$mResult[$j][can_noveda] = $mData[can_noveda];
					$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
					$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
					$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
					$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
					$mResult[$j][fec_planea] = $mData[fec_planea];
					$mResult[$j][fec_planGl] = $mData[fec_planGl];
					$j++;
				}
			}
		}

		if( $mTipReturn == 'array' )
			return GetColumnFromMatrix( $mResult, 'num_despac' );
		elseif( $mTipReturn == 'list' )
			return join(',', GetColumnFromMatrix($mResult, 'num_despac') );
		else
			return $mResult;
	}

    /*! \fn: tipValidaTiempo
	 *  \brief: Verifica el tipo de validacion que aplica por transportadora
	 *  \author: Ing. Fabian Salinas
	 *	\date: 22/06/2015
	 *	\date modified: dia/mes/a�o
	 *  \param: Data Transportadora
	 *  \return: 
	 */
	private function tipValidaTiempo( $mTransp )
	{
		$mBandera = $mTransp[tie_nacion] == 0 && $mTransp[tie_urbano] == 0 ? 0 : 1;

		if( ($mTransp[nom_tipser] == 'MA' && $mBandera == 1) || ($mTransp[nom_tipser] == 'EAL/MA' && $mBandera == 1) || ($mTransp[nom_tipser] == 'OAL/MA' && $mBandera == 1) )
		{
			$mResult = 'tie_parame';
		}
		else{
			$mResult = 'fec_alarma';
		}
			

		return $mResult;
	}

    /*! \fn: calTimeAlarma
	 *  \brief: Calcula el tiempo por fecha de alarma
	 *  \author: Ing. Fabian Salinas
	 *	\date: 22/06/2016
	 *	\date modified: dia/mes/a�o
	 *  \param: $mDespac   Matriz	Datos Despachos
	 *  \param: $mTransp   Array  	Informacion de la transportadora
	 *  \param: $mIndCant  Integer  0:Retorna Despachos con Tiempos; 1:Retorna Cantidades
	 *  \param: $mFiltro   String  	Filtro para el detallado por color, sinF = Todos
	 *  \param: $mColor	Array  	Colores por Etapa
	 *  \return: Matriz
	 */
	private function calTimeAlarma( $mDespac, $mTransp, $mIndCant = 0, $mFiltro = NULL, $mColor = NULL )
	{
		$mTipValida = self::tipValidaTiempo( $mTransp );

		if( $mIndCant == 1 )
		{ #Define Cantidades seg�n estado
			$mResult[fin_rutaxx] = 0;
			$mResult[ind_acargo] = 0;
			$mResult[est_pernoc] = 0;
			$mResult[sin_retras] = 0;
			$mResult[con_alarma] = 0;
			$mResult[con_00A30x] = 0;
			$mResult[con_31A60x] = 0;
			$mResult[con_61A90x] = 0;
			$mResult[con_91Amas] = 0;
			$mResult[enx_seguim] = 0;
		}else
		{ #Variables de Posicion
			$mNegTiempo = 0; #neg_tiempo
			$mPosTiempo = 0; #pos_tiempo
			$mNegFinrut = 0; #neg_finrut
			$mPosFinrut = 0; #pos_finrut
			$mNegAcargo = 0; #neg_acargo
			$mPosAcargo = 0; #pos_acargo
			$mNegTieesp = 0; #neg_tieesp
			$mPosTieesp = 0; #pos_tieesp
		}
		
		for ($i=0; $i < sizeof($mDespac); $i++)
		{
			$mPernoc = false; #Bandera para despachos estado pernoctacion
			$mFecPerno = '';

			if( $mDespac[$i][can_noveda] > 0 )
			{#Despacho con Novedades

				$mDespac[$i][tiempo] = getDiffTime( $mDespac[$i][fec_planea], self::$cHoy ); #Script /lib/general/function.inc
				
				$mDespac[$i][tiempoGl] = getDiffTime( $mDespac[$i][fec_planGl], self::$cHoy ); #Script /lib/general/function.inc

				if( $mDespac[$i][ind_fuepla] == '1' && $mDespac[$i][tiempo] < 0 )
					$mPernoc = true;
			}
			else #Despacho Sin Novedades
				$mDespac[$i][tiempo] = getDiffTime( $mDespac[$i][fec_salida], self::$cHoy ); #Script /lib/general/function.inc
				$mDespac[$i][tiempoGl] = getDiffTime( $mDespac[$i][fec_planGl], self::$cHoy ); #Script /lib/general/function.inc

			# Arma la matriz resultante 
			if( $mIndCant == 1 )
			{# Cantidades seg�n estado
				
					if( $mPernoc == true ){
						$mResult[est_pernoc]++;
						$mResult[enx_seguim]++;
					} #Pernoctacion
					elseif( $mDespac[$i][ind_finrut] == '1' ) #Por Llegada
						$mResult[fin_rutaxx]++;
					elseif( $mDespac[$i][ind_defini] == 'SI' ) #A Cargo Empresa
						$mResult[ind_acargo]++;
					elseif( $mDespac[$i][tiempo] < 0 ){ #Sin Retraso
						$mResult[sin_retras]++;
						$mResult[enx_seguim]++;
					}
					elseif( $mDespac[$i][tiempo] < 31 && $mDespac[$i][tiempo] >= 0 ){
						 # 0 a 30
						$mResult[con_00A30x]++;
						$mResult[con_alarma]++;
						$mResult[enx_seguim]++;
					}
					elseif( $mDespac[$i][tiempo] < 61 && $mDespac[$i][tiempo] > 30 ) {
						# 31 a 60
						$mResult[con_31A60x]++;
						$mResult[con_alarma]++;
						$mResult[enx_seguim]++;
					}
					elseif( $mDespac[$i][tiempo] < 91 && $mDespac[$i][tiempo] > 60 ) {
						# 61 a 90
						$mResult[con_61A90x]++;
						$mResult[con_alarma]++;
						$mResult[enx_seguim]++;
					}
					elseif( $mDespac[$i][tiempo] > 90 ){
						# Mayor 90
						$mResult[con_91Amas]++;
						$mResult[con_alarma]++;
						$mResult[enx_seguim]++;
					} 
					else{
						continue;
					}
				
			}
			else
			{# Colores e informaci�n del despacho seg�n estado

				if( $mFiltro == 'sinF' )
					$mBandera = true;
				elseif( $mPernoc != true && $mDespac[$i][ind_finrut] != '1' && $mDespac[$i][ind_defini] != 'SI' ) #Para los filtros por tiempos desde "Sin Retraso" hasta "Mayor 90"
					$mBandera = true;
				else
					$mBandera = false;

				#Arma Matriz resultante se�n fase
				if( ($mFiltro == 'est_pernoc' || $mFiltro == 'sinF' || $mFiltro == 'enx_seguim') && $mPernoc == true && $mDespac[$i][ind_defini] != 'SI' && $mDespac[$i][ind_finrut] != '1' )
				{ #Pernoctacion
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[neg_tieesp][$mNegTieesp] = $mDespac[$i];
						$mResult[neg_tieesp][$mNegTieesp][color] = $mColor[0];
						$mResult[neg_tieesp][$mNegTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[neg_tieesp][$mNegTieesp][fase] = 'est_pernoc';
						$mNegTieesp++;
					}else{
						$mResult[neg_tiempo][$mNegTiempo] = $mDespac[$i];
						$mResult[neg_tiempo][$mNegTiempo][color] = $mColor[0];
						$mResult[neg_tiempo][$mNegTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[neg_tiempo][$mNegTiempo][fase] = 'est_pernoc';
						$mNegTiempo++;
					}
				}
				elseif( ($mFiltro == 'fin_rutaxx' || $mFiltro == 'sinF') && $mDespac[$i][ind_finrut] == '1' && $mDespac[$i][ind_defini] != 'SI' )
				{ #Por Llegada
					if( $mDespac[$i][tiempo] < 0 ){
						$mResult[neg_finrut][$mNegFinrut] = $mDespac[$i];
						$mResult[neg_finrut][$mNegFinrut][color] = $mColor[0];
						$mResult[neg_finrut][$mNegFinrut][fase] = 'fin_rutaxx';
						$mNegFinrut++;
					}else{
						$mResult[pos_finrut][$mPosFinrut] = $mDespac[$i];
						$mResult[pos_finrut][$mPosFinrut][color] = $mColor[0];
						$mResult[pos_finrut][$mPosFinrut][fase] = 'fin_rutaxx';
						$mPosFinrut++;
					}
				}
				elseif( ($mFiltro == 'ind_acargo' || $mFiltro == 'sinF') && $mDespac[$i][ind_defini] == 'SI' )
				{ #A Cargo Empresa
					if( $mDespac[$i][tiempo] < 0 ){
						$mResult[neg_acargo][$mNegAcargo] = $mDespac[$i];
						$mResult[neg_acargo][$mNegAcargo][color] = $mColor[0];
						$mResult[neg_acargo][$mNegAcargo][fase] = 'ind_acargo';
						$mNegAcargo++;
					}else{
						$mResult[pos_acargo][$mPosAcargo] = $mDespac[$i];
						$mResult[pos_acargo][$mPosAcargo][color] = $mColor[0];
						$mResult[pos_acargo][$mPosAcargo][fase] = 'ind_acargo';
						$mPosAcargo++;
					}
				}
				elseif( ($mFiltro == 'sin_retras' || $mFiltro == 'sinF'|| $mFiltro == 'enx_seguim') && $mDespac[$i][tiempoGl] < 0 && $mBandera == true )
				{ #Sin Retraso
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[neg_tieesp][$mNegTieesp] = $mDespac[$i];
						$mResult[neg_tieesp][$mNegTieesp][color] = $mColor[0];
						$mResult[neg_tieesp][$mNegTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[neg_tieesp][$mNegTieesp][fase] = 'sin_retras';
						$mNegTieesp++;
					}else{
						$mResult[neg_tiempo][$mNegTiempo] = $mDespac[$i];
						$mResult[neg_tiempo][$mNegTiempo][color] = $mColor[0];
						$mResult[neg_tiempo][$mNegTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[neg_tiempo][$mNegTiempo][fase] = 'sin_retras';
						$mNegTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_00A30x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempoGl] < 31 && $mDespac[$i][tiempoGl] >= 0 && $mBandera == true )
				{ # 0 a 30
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[1];
						$mResult[pos_tieesp][$mPosTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_00A30x';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[1];
						$mResult[pos_tiempo][$mPosTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_00A30x';
						$mPosTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_31A60x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempoGl] < 61 && $mDespac[$i][tiempoGl] > 30 && $mBandera == true )
				{ # 31 a 60
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[2];
						$mResult[pos_tieesp][$mPosTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_31A60x';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[2];
						$mResult[pos_tiempo][$mPosTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_31A60x';
						$mPosTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_61A90x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempoGl] < 91 && $mDespac[$i][tiempoGl] > 60 && $mBandera == true )
				{ # 61 a 90
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[3];
						$mResult[pos_tieesp][$mPosTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_61A90x';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[3];
						$mResult[pos_tiempo][$mPosTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_61A90x';
						$mPosTiempo++;
					}
				}
				elseif( ($mFiltro == 'con_91Amas' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempoGl] > 90 && $mBandera == true )
				{ # Mayor 90
					if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
						$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
						$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[4];
						$mResult[pos_tieesp][$mPosTieesp][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_91Amas';
						$mPosTieesp++;
					}else{
						$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
						$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[4];
						$mResult[pos_tiempo][$mPosTiempo][color2] = self::despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
						$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_91Amas';
						$mPosTiempo++;
					}
				}else{
					continue;
				}
			}
		}
		return $mResult;
	}
    /*! \fn: getInfoDespac
	 *  \brief: Trae informacion adicional del despacho
	 *  \author: Ing. Fabian Salinas
	 *	\date: 02/06/2015
	 *	\date modified: dia/mes/a�o
	 *  \param: mDespac  Array  Data del Despacho
	 *  \param: mTransp  Array  Informacion transportadora
	 *  \param: mTipValida  String  Tipo de validacion
	 *  \return: Array
	 */
	private function getInfoDespac( $mDespac, $mTransp, $mTipValida )
	{
		$mNovDespac = getNovedadesDespac( $this->conexion, $mDespac[num_despac], 1 ); # Novedades del Despacho -- Script /lib/general/function.inc
		$mCantNoved = sizeof($mNovDespac); # Cantidad de Novedades del Despacho
		$mPosN = $mCantNoved-1; #Posicion ultima Novedad

		$mResult[can_noveda] = $mCantNoved;
		$mResult[ind_fuepla] = $mNovDespac[$mPosN][ind_fuepla];
		$mResult[ind_limpio] = $mNovDespac[$mPosN][ind_limpio];
		$mResult[fec_ultnov] = $mNovDespac[$mPosN][fec_crenov];
		$mResult[nov_especi] = $mNovDespac[$mPosN][nov_especi];
		$mResult[ind_alarma] = $mNovDespac[$mPosN][ind_alarma];
		$mResult[nom_ultnov] = $mNovDespac[$mPosN][nom_noveda] == '' ? '-' : $mNovDespac[$mPosN][nom_noveda];
		$mResult[nom_sitiox] = $mNovDespac[$mPosN][nom_sitiox] == '' ? '-' : $mNovDespac[$mPosN][nom_sitiox];
		$mResult[sig_pcontr] = getNextPC( $this->conexion, $mDespac[num_despac] );
		$mResult[pla_rutaxx] = getControDespac( $this->conexion, $mDespac[num_despac] ); # Plan de Ruta del Despacho -- Script /lib/general/function.inc


		if( $mTipValida == 'tie_parame' )
		{
			if( $mDespac[tie_contra] != '' ){ #Tiempo parametrizado por Despacho
				$mTime = $mDespac[tie_contra];
			}
			elseif( $mDespac[cod_tipdes] == '1' )#Despacho Urbano
				{
					$mTime = $mTransp[tie_urbano];
					$mTimeGl = $mTransp[tgl_urbano];
					$mTimeprc = $mTransp[tgl_urbprc];
				}
			else #Otros Tipos de despacho se toma el tiempo de Despachos Nacionales
				{
					$mTime = $mTransp[tie_nacion];
					$mTimeGl = $mTransp[tgl_nacion];
					$mTimeprc = $mTransp[tgl_nacprc];
				}
			$mFecUltReport = $mDespac[fec_salida];
			#Verifica la ultima novedad que no mantienen alarma
			for ($i=$mPosN; $i >= 0; $i--)
			{
				if( $mNovDespac[$i][ind_manala] == '0' ){
					$mFecUltReport = $mNovDespac[$i][fec_crenov];
					$mTime = $mNovDespac[$i][tiem_duraci] > 0 ? $mNovDespac[$i][tiem_duraci] : $mTime;
					$mTimeGl = $mNovDespac[$i][tiem_duraci] > 0 ? $mNovDespac[$i][tiem_duraci] : $mTimeGl;
					
					$mTimeprc = $mNovDespac[$i][tiem_duraci] > 0 ? $mNovDespac[$i][tiem_duraci] : $mTimeprc;
					break;
				}elseif( $i == 0 ){#Si el despacho no tiene ninguna novedad que no mantenga alarma toma fecha salida del sistema
					$mFecUltReport = $mDespac[fec_salida];
					$mTime = 0;
					$mTimeGl = 0;
					$mTimeprc = 0;
				}
			}
			
			$mTime = "+".$mTime." minute";
			$mTimeGl = "+".$mTimeGl." minute";
			$mTimeprc = "+".$mTimeprc." minute";

			$mResult[fec_plaprc] = date ( 'Y-m-d H:i:s', ( strtotime( $mTimeprc, strtotime ( $mFecUltReport ) ) ) ); #Fecha Planeada para el Seguimiento
			$mResult[fec_planGl] = date ( 'Y-m-d H:i:s', ( strtotime( $mTimeGl, strtotime ( $mFecUltReport ) ) ) ); #Fecha Planeada para el Seguimiento
			$mResult[fec_planea] = date ( 'Y-m-d H:i:s', ( strtotime( $mTime, strtotime ( $mFecUltReport ) ) ) ); #Fecha Planeada para el Seguimiento
		}
		else
		{ #warning2
			if( $mNovDespac[$mPosN][tiem_duraci] != '0' )
			{ #Si la ultima novedad solicita tiempo  	
				$mTime = $mNovDespac[$mPosN][tiem_duraci];
				$mTime = "+".$mTime." minute";
				$mTimeGl = "+".$mTimeGl." minute";
				$mTimeprc = "+".$mTimeprc." minute";
				$mResult[fec_planGl] = date ( 'Y-m-d H:i:s', ( strtotime( $mTimeGl, strtotime ( $mResult[fec_ultnov] ) ) ) );
				$mResult[fec_planea] = date ( 'Y-m-d H:i:s', ( strtotime( $mTime, strtotime ( $mResult[fec_ultnov] ) ) ) );
				
				$mResult[fec_plaprc] = date ( 'Y-m-d H:i:s', ( strtotime( $mTimeprc, strtotime ( $mResult[fec_ultnov] ) ) ) );
			}
			elseif( $mResult[fec_ultnov] < $mResult[sig_pcontr][fec_progra] # Fecha de la ultima novedad menor a la fecha planeada del siguiente PC
				|| 	($mResult[pla_rutaxx][(sizeof($mResult[pla_rutaxx])-1)][cod_contro] == $mNovDespac[$mPosN][cod_contro] && $mNovDespac[$mPosN][ind_ensiti] == '1') # Ultimo PC con novedad en sitio es el ultimo PC del plan de ruta
			  )
			{
			  	$mResult[fec_planea] = $mResult[sig_pcontr][fec_progra]; #Fecha planeada del siguinete PC
				$mResult[fec_planGl] = $mResult[sig_pcontr][fec_progra];
				$mResult[fec_plaprc] = $mResult[sig_pcontr][fec_progra];
			}else{
			  	if( $mNovDespac[$mPosN][ind_manala] == '0' )
				{ #Si la ultima novedad no mantiene alarma
					$mTime = $mDespac[cod_tipdes] == '1' ? self::$cTime[ind_desurb] : self::$cTime[ind_desnac];
					$mTimeGl = $mDespac[cod_tipdes] == '1' ? self::$cTime[ind_desurb] : self::$cTime[ind_desnac];
					$mTime = "+".$mTime." minute";
					$mTimeGl = "+".$mTimeGl." minute";
					$mTimeprc = "+".$mTimeprc." minute";
					$mResult[fec_planGl] = date ( 'Y-m-d H:i:s', ( strtotime( $mTimeGl, strtotime ( $mResult[fec_ultnov] ) ) ) );
					$mResult[fec_planea] = date ( 'Y-m-d H:i:s', ( strtotime( $mTime, strtotime ( $mResult[fec_ultnov] ) ) ) );
					$mResult[fec_plaprc] = date ( 'Y-m-d H:i:s', ( strtotime( $mTimeprc, strtotime ( $mResult[fec_ultnov] ) ) ) );
				}
				else
					$mResult[fec_planGl] = $mResult[sig_pcontr][fec_progra]; #Fecha planeada del siguinete PC
					$mResult[fec_planea] = $mResult[sig_pcontr][fec_progra]; #Fecha planeada del siguinete PC
					$mResult[fec_plaprc] = $mResult[sig_pcontr][fec_progra]; #Fecha planeada del siguinete PC
			}
		}
		
		return $mResult;
	}
    /*! \fn: getTimeDescargue
	 *  \brief: Retorna el tiempo parametrizado para iniciar seguimiento etapa Descargue
	 *  \author: Ing. Fabian Salinas
	 *	\date: 07/07/2015
	 *	\date modified: dia/mes/a�o
	 *  \param: mTransp  Array  Informacion de la Transportadora
	 *  \param: mCodTipdes  String  Codigo Tipo de Despacho   
	 *  \return: Integer
	 */
	private function getTimeDescargue( $mTransp, $mCodTipdes )
	{
		switch ($mCodTipdes) {
			case '1':
				$mResult = $mTransp[tie_desurb];
				break;
			
			case '2':
				$mResult = $mTransp[tie_desnac];
				break;
			
			case '3':
				$mResult = $mTransp[tie_desimp];
				break;
			
			case '4':
				$mResult = $mTransp[tie_desexp];
				break;
			
			case '5':
				$mResult = $mTransp[tie_destr1];
				break;
			
			case '6':
				$mResult = $mTransp[tie_destr2];
				break;
			
			default:
				$mResult = $mTransp[tie_desnac];
				break;
		}
		return $mResult;
	}

    
}
$proceso= new EmailSeguim();

?>