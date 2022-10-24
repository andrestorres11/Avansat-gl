<?php
//ini_set('display_errors', true);
//error_reporting(E_ALL & ~E_NOTICE);
class DashBoard_Seguim_Table2 {

    private static  $cHoy,$cTime = array( 'ind_desurb' => '30', 'ind_desnac' => '60' );

    function __construct() {
        @include_once( "../lib/general/festivos.php" );
        include('../lib/ajax.inc');
        @include_once( "../lib/general/constantes.inc" );
        $this -> conexion = $AjaxConnection;
        self::$cHoy = date("Y-m-d H:i:s");

        echo '
                <!-- Bootstrap -->
                <link href="../js/dashboard/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

                <!-- Font Awesome -->
                <link href="../js/dashboard/vendors/fontawesome/css/font-awesome.min.css" rel="stylesheet">
                
                <!-- Datatables -->
                <link href="../js/dashboard/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
                <link href="../js/dashboard/vendors/datatables.net-buttons/css/buttons.dataTables.min.css" rel="stylesheet">
                <link href="../js/dashboard/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
                <link href="../js/dashboard/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
                <link href="../js/dashboard/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

                <!-- Float Menu -->
                <link href="../js/dashboard/button.css" rel="stylesheet">
                <link href="../js/dashboard/floatMenu.css" rel="stylesheet">

                <!-- Custom Theme Style -->
                <link href="../js/dashboard/inf_dashbo_dashbo.css" rel="stylesheet">
                <link href="../estilos/informes.css" rel="stylesheet">
                <style>
                #tablaRegistros_wrapper{
                    width: fit-content;
                }
                </style>
            ';
            echo '

                <!-- jQuery -->
                <script src="../js/dashboard/vendors/jquery/dist/jquery.min.js"></script>
                
                <!-- jQuery FrameWork -->
                <script src="../js/functions.js"></script>
                <script src="../js/jquery.blockUI2019.js"></script>

                <!-- Bootstrap -->
                <script src="../js/dashboard/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

                <!-- bootstrap-progressbar -->
                <script src="../js/dashboard/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>

                <!-- Datatables -->
                <script src="../js/dashboard/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
                <script src="../js/dashboard/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
                <script src="../js/dashboard/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
                <script src="../js/dashboard/vendors/jszip/dist/jszip.min.js"></script>
                <script src="../js/dashboard/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
                
            ';
            echo "<script>
            $(document).ready(function () {
                $('#tablaRegistros').DataTable({
                    dom: 'Bflrtip',
                    buttons: [
                    ],
                    responsive: true,
                    'autoWidth': false,
                    'search': {
                            'regex': true,
                        'caseInsensitive': false,
                    },
                    'pageLength': 5,
                    'paging': true,
                    'info': true,
                    'filter': true,
                    'orderCellsTop': true,
                    'fixedHeader': true,
                    'language': {
                        'url': '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json'
                    },
                    'dom': \"<'row'<'col-sm-4'l><'col-ms-4'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>\",
                });
            });
            </script>";

            $Atipservic = $this->getTipServ();
			$mTipServic = '""';

            foreach($Atipservic as $servicio){
				$mTipServic .= $_REQUEST['tip_servic'.$servicio[0]] === true ? ',"'.$servicio[0].'"' : '';
			}
			$where_ .= $mTipServic != '""' ? " AND cod_tipser IN (".$mTipServic.") " : "";
            
            $mSqlsub="select DISTINCT (
                SELECT cod_transp FROM ".BASE_DATOS.".tab_transp_tipser 
                WHERE cod_transp=b.cod_transp 
                $where_
                ORDER BY num_consec DESC 
                limit 1) as  cod_transp
            from ".BASE_DATOS.".tab_transp_tipser as b 
            where  b.cod_transp in (".$_REQUEST[cod_transp].") ";
            
            $consultasub  = new Consulta($mSqlsub, $this -> conexion);
            $despachos_validate = $consultasub -> ret_matriz();

            $cad_cod_transp='';
            foreach($despachos_validate as $value){
                $cad_cod_transp .='"'.$value['cod_transp'].'",';
            }
            $cad_cod_transp = substr($cad_cod_transp, 0, -1);
            
            $mSql="select a.num_despac from ".BASE_DATOS.".tab_despac_despac as a 
                INNER JOIN ".BASE_DATOS.".tab_despac_vehige as b on a.num_despac = b.num_despac
                where b.cod_transp in(".$cad_cod_transp.") and a.fec_salida is NOT null and a.fec_llegad is null and a.ind_planru = 'S' and a.ind_anulad = 'R' 
				";
            
            $consulta  = new Consulta($mSql, $this -> conexion);
            $despachos = $consulta -> ret_matriz();
            $html=''; $i=0;$despac='';$array_despac_transit=array();$despac_final='"",';
            foreach($despachos as $value){
                    $i++;
                    $despac .='"'.$value['num_despac'].'",';
                    array_push($array_despac_transit,$value['num_despac']);
            }
			$array_despac_transit=array_unique($array_despac_transit);
            $despac=substr($despac, 0, -1);
			
            $mSql2 = "SELECT DISTINCT(a.num_despac) as num_despac
                                FROM ".BASE_DATOS.".tab_despac_contro a
                                WHERE a.num_despac in ($despac)";
            $mSql3=" SELECT DISTINCT(b.num_despac) as num_despac
                                FROM ".BASE_DATOS.".tab_despac_noveda b 
                                WHERE b.num_despac in ($despac)";
            
            $consulta2  = new Consulta($mSql2, $this -> conexion);
            $despachos2 = $consulta2 -> ret_matriz();
            $array_despac=array();
            foreach($despachos2 as $value){
                array_push($array_despac,$value['num_despac']);
            }
            $consulta3  = new Consulta($mSql3, $this -> conexion);
            $despachos3 = $consulta3 -> ret_matriz();
            foreach($despachos3 as $value){
                array_push($array_despac,$value['num_despac']);
            }
            $array_despac=array_unique($array_despac);
			$l=0;
            foreach($array_despac_transit as $value){
                if(!in_array($value,$array_despac) && $l<=100){
                    $despac_final .=$value.',';
					$l++;
                }
            }
            $despac_final=substr($despac_final, 0, -1);
			
            $mSql4="select a.num_despac as num_despac, a.cod_manifi as cod_manifi, b.num_placax as num_placax, d.usr_creaci as usr_creaci,b.cod_transp,e.nom_tercer as nom_tercer
                from ".BASE_DATOS.".tab_despac_despac as a 
                INNER JOIN ".BASE_DATOS.".tab_despac_vehige as b on a.num_despac = b.num_despac
				INNER JOIN ".BASE_DATOS.".tab_tercer_tercer as e ON b.cod_transp = e.cod_tercer
                LEFT JOIN ".BASE_DATOS.".tab_despac_contro as d ON a.num_despac =d.num_despac
                where a.num_despac in($despac_final) and d.cod_contro !='9999'";
			
			$consulta4  = new Consulta($mSql4, $this -> conexion);
            $despachos4 = $consulta4 -> ret_matriz();
			$results=array();
            $i=0;$p=0;
            
            foreach($despachos4 as $value){
					
                    $mViewBa = $this->getView('jso_bandej');
                    $mTransp = $this->getTranspServic( $mIndEtapa, $value['cod_transp'] );
                    $mColor = array('', 'bgT1', 'bgT2', 'bgT3', 'bgT4');
                    $mNegTieesp = array(); #neg_tieesp
                    $mPosTieesp = array(); #pos_tieesp
                    $mNegTiempo = array(); #neg_tiempo
                    $mPosTiempo = array(); #pos_tiempo
                    $mNegFinrut = array(); #neg_finrut
                    $mPosFinrut = array(); #pos_finrut
                    $mNegAcargo = array(); #neg_acargo
                    $mPosAcargo = array(); #pos_acargo
                    
                    #array datos precarga
                    $con_paradi = array(); #para el dia
                    $con_paraco = array(); #para el corte
                    $con_anulad = array(); #anuladas
                    $con_planta = array(); #llegada en planta
                    $enx_planta = array(); #en planta de etapa de cargue
                    $con_porter = array(); #en porteria
                    $con_sinseg = array(); #sin seguimineto
                    $con_tranpl = array(); #transito a planta
                    $con_cnnlap = array(); #con novedad no llegada a planta
                    $con_cnlapx = array(); #con novedad llegada a planta
                    $con_acargo = array(); #A cargo de empresa
                    $mNameFunction = $mTransp[0]['ind_segcar'] == '1' && $mTransp[0]['ind_segdes'] == '1' ? 'getDespacTransi2' : 'getDespacTransi1';
                    $mDespac = $this->$mNameFunction( $mTransp[0], $value['num_despac'] );
                    $mDespac_ = $this->calTimeAlarma( $mDespac, $mTransp[0], 0, 'sinF', $mColor );

                    $mNegTieesp = $mDespac_['neg_tieesp'] ? array_merge($mNegTieesp, $mDespac_['neg_tieesp']) : $mNegTieesp;
                    $mPosTieesp = $mDespac_['pos_tieesp'] ? array_merge($mPosTieesp, $mDespac_['pos_tieesp']) : $mPosTieesp;
                    $mNegTiempo = $mDespac_['neg_tiempo'] ? array_merge($mNegTiempo, $mDespac_['neg_tiempo']) : $mNegTiempo;
                    $mPosTiempo = $mDespac_['pos_tiempo'] ? array_merge($mPosTiempo, $mDespac_['pos_tiempo']) : $mPosTiempo;
                    $mNegFinrut = $mDespac_['neg_finrut'] ? array_merge($mNegFinrut, $mDespac_['neg_finrut']) : $mNegFinrut;
                    $mPosFinrut = $mDespac_['pos_finrut'] ? array_merge($mPosFinrut, $mDespac_['pos_finrut']) : $mPosFinrut;
                    $mNegAcargo = $mDespac_['neg_acargo'] ? array_merge($mNegAcargo, $mDespac_['neg_acargo']) : $mNegAcargo;
                    $mPosAcargo = $mDespac_['pos_acargo'] ? array_merge($mPosAcargo, $mDespac_['pos_acargo']) : $mPosAcargo;

                    $mData =$this->orderMatrizDetail( $mNegTieesp, $mPosTieesp, $mNegTiempo, $mPosTiempo, $mNegFinrut, $mPosFinrut, $mNegAcargo, $mPosAcargo );
                    for ($j=0; $j < sizeof($mData['tiempo']); $j++) { 
                        if ($mData['tiempo'][$j]['nov_especi'] == '1' && $mData['tiempo'][$j]['ind_alarma'] == 'S' ) {
                            $mData['novesp'][$j] = $mData['tiempo'][$j];
                            unset($mData['tiempo'][$j]);
                        }else{
                            continue;
                        }
                    }
                    $mColor =  '#000000;';
                    $color='';
                    $timeGl=0;
					$text_novedad='';
					$mLink='<a href="../../'.BASE_DATOS.'/index.php?cod_servic=3302&window=central&despac='.$value['num_despac'].'&tie_ultnov=0&opcion=1"  target="centralFrameID">'.$value['num_despac'].'</a>';
                    foreach ($mData as $key=> $row)
			        {
                        foreach($row as $val)
                        {
							$text_novedad=strtolower($val['nom_ultnov']);
                            $timeGl=intval($val['tiempoGl'] !='' ? $val['tiempoGl']:0);
                            $color=$val['color'];
                            if($val['num_despac']!='' && $val['tiempo']){
                                $mLink = '<a href="../../'.BASE_DATOS.'/index.php?cod_servic=3302&window=central&despac='.$val['num_despac'].'&tie_ultnov='.$val['tiempo'].'&opcion=1" style="color:'.$mColor.'" target="centralFrameID">'.$val['num_despac'].'</a>';
                            }
                        }
                    }
					
                    $mTxt = substr($color, 3);
                    $mColor = $mTxt > 2 ? '#FFFFFF;' : '#000000;';
                    $pattern = "/a cargo empresa/i";
					if(!preg_match($pattern, $text_novedad))
					{
						array_push($results,array(
							'time'=>$timeGl,
							'link'=>($mLink !='' ? $mLink:$value['num_despac']),
							'color'=>$color,
							'cod_manifi'=>$value['cod_manifi'],
							'num_despac'=>$value['num_despac'],
							'num_placax'=>$value['num_placax'],
							'nom_tercer'=>$value['nom_tercer'],
							'usr_creaci'=>($value['usr_creaci'] !='' ? $value['usr_creaci']:'N/a'),
						));
					}
                }
				//var_dump($results);
				$uniques=array();
                $sorted = $this->array_orderby($results, 'time',SORT_DESC);
				
            echo '
            <table id="tablaRegistros" class="table table-striped table-bordered table-sm" style="width: 90vw;font-size:10px;">
                <thead>
                <tr>
                    <th>No.</th>
                    <th>No. Manifiestos</th>
                    <th>No. Despacho</th>
                    <th>Placa</th>
                    <th>Transp.</th>
                    <th>Asignado a.</th>
                </tr>
                </thead>
                <tbody>';
                foreach($sorted as $value_p){
					if(!in_array($uniques,$value_p['cod_manifi']) && !in_array($uniques,$value_p['num_despac']) && !in_array($uniques,$value_p['num_placax']) && !in_array($uniques,$value_p['nom_tercer']) && !in_array($uniques,$value_p['usr_creaci']))
					{
						$i++;
						echo "<tr>";
						echo "<td>".$i."</td>";
						echo "<td>".$value_p['cod_manifi']."</td>";
						echo "<td class='classCell bt ".$value_p['color']."'>".$value_p['link']."</td>";
						echo "<td>".$value_p['num_placax']."</td>";
						echo "<td>".$value_p['nom_tercer']."</td>";
						echo "<td>".$value_p['usr_creaci']."</td>";
						echo "</tr>";
						echo $html;
						array_push($uniques,$value_p['cod_manifi'],$value_p['num_despac'],$value_p['num_placax'],$value_p['nom_tercer'],$value_p['usr_creaci']);
					}
                }
            echo '</tbody>
            </table>';
    }


    private function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
                }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

	

    public function getView( $mCatego )
	{
		$mSql = "SELECT a.jso_bandej, a.jso_encabe, a.jso_plarut 
				   FROM ".BASE_DATOS.".tab_genera_respon a 
			 INNER JOIN ".BASE_DATOS.".tab_genera_perfil b 
					 ON a.cod_respon = b.cod_respon 
				  WHERE b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
		$mConsult = new Consulta($mSql, $this -> conexion);
		$mData = $mConsult->ret_matrix('a');

		return json_decode($mData[0][$mCatego]);
	}

    public function getTranspServic( $mTipEtapax = NULL, $mCodTransp = NULL, $mAddWherex = NULL )
	{
		$Atipservic = $this->getTipServ();
		$mTipServic = '""';

		foreach($Atipservic as $servicio){
			$mTipServic .= $_REQUEST['tip_servic'.$servicio[0]] === true ? ',"'.$servicio[0].'"' : '';
		}
		
		/*
		$mTipServic .= $_REQUEST[tip_servic1] == '1' ? ',"1"' : '';
		$mTipServic .= $_REQUEST[tip_servic2] == '1' ? ',"2"' : '';
		$mTipServic .= $_REQUEST[tip_servic3] == '1' ? ',"3"' : '';*/
		//$mLisTransp = $this->getTranspCargaControlador();

		//Tipo de servicio Horario de gesti?n
		$$mHorTipSer='';
		$arrayHorTipSer = array();
		$_REQUEST[tip_horlab1] == '1' ? $arrayHorTipSer[] = '1' : '';
		$_REQUEST[tip_horlab2] == '1' ? $arrayHorTipSer[] = '2' : '';
		$_REQUEST[tip_horlab3] == '1' ? $arrayHorTipSer[] = '3' : '';

		foreach($arrayHorTipSer as $index=>$servicio){
			$mHorTipSer .= $servicio;
			if( $index+1 < count($arrayHorTipSer)){
				$mHorTipSer .=', ';
			}
		}

		$mFilHorasx = $_REQUEST[fil_horasx] == '1' ? 'AND i.hor_ingres = "'.$_REQUEST['hor_inicia'].'" AND i.hor_ingres = "'.$_REQUEST['hor_finalx'].'"': '';

		$mSql = " SELECT a.*,
						 GROUP_CONCAT(h.cod_usuari ORDER BY h.cod_usuari ASC SEPARATOR ', ' ) AS usr_asigna
					FROM (

										SELECT c.ind_segprc,c.ind_segcar, 
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
											   c.tgl_prcnac AS tgl_nacprc, c.tgl_prcurb AS tgl_urbprc
										  FROM ".BASE_DATOS.".tab_transp_tipser c 
									INNER JOIN ".BASE_DATOS.".tab_genera_tipser d 
											ON c.cod_tipser = d.cod_tipser 
									INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e 
											ON c.cod_transp = e.cod_tercer 
									INNER JOIN (	  SELECT cod_transp , MAX(num_consec) AS num_consec 
														FROM ".BASE_DATOS.".tab_transp_tipser  
													GROUP BY cod_transp 
											   ) f ON c.cod_transp = f.cod_transp AND c.num_consec = f.num_consec
									  GROUP BY c.cod_transp
						 ) a 
			   LEFT JOIN ".BASE_DATOS.".vis_monito_encdet h
					  ON a.cod_transp = h.cod_transp
			   LEFT JOIN ".BASE_DATOS.".tab_config_horlab i 
			          ON a.cod_transp = i.cod_tercer
					WHERE 1=1 ";

		$mSql .= $mTipEtapax == NULL ? "" : " AND a.{$mTipEtapax} = 1 ";
		$mSql .= $mAddWherex == NULL ? "" : $mAddWherex;

		#Filtro por codigo de Transportadora
		
		$mSql .= " AND a.cod_transp IN ( {$mCodTransp} )";
		

		#Otros Filtros
		$mSql .= $mTipServic != '""' ? " AND a.cod_tipser IN (".$mTipServic.") " : "";

		$mSql .= $mHorTipSer != '' ? " AND i.tip_servic IN (".$mHorTipSer.") " : "";

		$mSql .= $mFilHorasx;
		
		$mSql .= " GROUP BY a.cod_transp ORDER BY h.cod_usuari, a.nom_transp ASC ";
		$mConsult = new Consulta( $mSql, $this -> conexion );

		return $mConsult -> ret_matrix('a');
	}

    public function getTipServ()
	{
		$mSql = "SELECT a.cod_tipser, a.nom_tipser
				   FROM ".BASE_DATOS.".tab_genera_tipser a 
				  WHERE a.ind_estado = '1'
					";
		$consulta = new Consulta( $mSql, $this -> conexion );
		return $mResult = $consulta -> ret_matrix('i');
	}
    private function getDespacTransi1( $mTransp , $num_despac)
	{
		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax,
						h.abr_tercer AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera,
						IF(
                           b.cod_itiner IS NOT NULL AND b.cod_itiner != 0,
                           b.cod_itiner,
                           IF(
                               m.nom_operad IS NULL OR m.nom_operad = '',
                               'No Requiere',
                               'Por Iniciar'
                            )
                        ) AS itinerario 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac
					AND a.fec_salida IS NOT NULL 
					AND a.fec_salida <= NOW() 
					AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00')
					AND a.ind_planru = 'S' 
					AND a.ind_anulad = 'R'
					AND b.ind_activo = 'S' 
					AND b.cod_transp = '".$mTransp[cod_transp]."'
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
			INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu l
					 ON b.num_placax = l.num_placax
			LEFT JOIN ".BD_STANDA.".tab_genera_opegps m
					 ON a.gps_operad = m.cod_operad
				  WHERE 1=1  and a.num_despac='$num_despac'";
		
			
		
		$mConsult = new Consulta( $mSql, $this -> conexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = $this->tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $y=0; $y<sizeof($mDespac); $y++ )
		{
			$mData = $this->getInfoDespac( $mDespac[$y], $mTransp, $mTipValida );

			#warning1
			
			$mResult[$j] = $mDespac[$y];
			$mResult[$j][can_noveda] = $mData[can_noveda];
			$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
			$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
			$mResult[$j][nov_especi] = $mData[nov_especi];
			$mResult[$j][ind_alarma] = $mData[ind_alarma];
			$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
			$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
			$mResult[$j][fec_planea] = $mData[fec_planea];
			$mResult[$j][fec_planGl] = $mData[fec_planGl];
			$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut]; #Aplica para empresas que solo tienen parametrizado seguimiento Transito
			$mResult[$j][coliti] = $this->validaAlarmaItiner($mDespac[$y]);
			$j++;
			
		}

		return $mResult;
	}


    public function getDespacTransi2( $mTransp, $num_despac )
	{
		$mDespacPrcCargue = $this->getDespacPrcCargue( $mTransp, 'list', true, $num_despac );
		$mDespacCarDes = $this->getDespacDescar( $mTransp, 'list2', true, $num_despac ); #Despachos en Etapas Cargue y Descargue
		$mDespacCarDes = trim($mDespacCarDes, ',');
		#Despachos en ruta  
		$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat
				INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu aa
						 ON yy.num_placax = aa.num_placax 
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND ( xx.fec_salida IS NOT NULL  )
						AND yy.cod_transp = '".$mTransp[cod_transp]."' 
                        AND xx.num_despac = '$num_despac'";
		$mSql .= ($mDespacPrcCargue != NULL || $mDespacPrcCargue != ''?" AND xx.num_despac NOT IN ( {$mDespacPrcCargue} ) ":"");
		
		$mConsult = new Consulta( $mSql, $this -> conexion );
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
		$mConsult = new Consulta( $mSql, $this -> conexion );
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

	

		$mConsult = new Consulta( $mSql, $this -> conexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = $this->tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();


		
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = $this->getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );
			#warning1
			
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
		
		return $mResult;
	}

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

    public function getInfoDespac( $mDespac, $mTransp, $mTipValida )
	{
		$mNovDespac = getNovedadesDespac( $this -> conexion, $mDespac[num_despac], 1 ); # Novedades del Despacho -- Script /lib/general/function.inc
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
		$mResult[sig_pcontr] = getNextPC( $this -> conexion, $mDespac[num_despac] );
		$mResult[pla_rutaxx] = getControDespac( $this -> conexion, $mDespac[num_despac] ); # Plan de Ruta del Despacho -- Script /lib/general/function.inc


		if( $mTipValida == 'tie_parame' )
		{
			if( $mDespac[tie_contra] != '' ){ #Tiempo parametrizado por Despacho
				$mTime = $mDespac[tie_contra];
				$mTimeGl = $mDespac[tie_contra];
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
				$mTimeGl = $mNovDespac[$mPosN][tiem_duraci];
				$mTimeprc = $mNovDespac[$mPosN][tiem_duraci];
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

    public function validaAlarmaItiner($mDespac){
		//Colores alarma de itinerario
		$resultado = 'bgIt1';
		if($mDespac[itinerario]=='No Requiere'){
			$resultado = 'bgIt1';
		}else{
			//Valida si esta reportando el operador gps (itinerario sin novedad)
			if($this->getReportOpeGPS($mDespac['num_despac'])){
				$resultado = 'bgIt2';
			}else{
				$resultado = 'bgIt4';
			}
			
			$mNovDespac = getNovedadesDespac( $this -> conexion, $mDespac['num_despac'], 1 );
			$mCantNoved = sizeof($mNovDespac); # Cantidad de Novedades del Despacho
			$mPosN = $mCantNoved-1; #Posicion ultima Novedad
			
			if($mNovDespac[$mPosN]['cod_noveda'] == '9273'){
				$resultado = 'bgIt3';
			}
		}
		return $resultado;
	}

	public function getReportOpeGPS($num_despac) {
		$queryReporte = "
                    SELECT b.cod_contro, c.*, d.nom_etapax
                        FROM ".BASE_DATOS.".tab_despac_despac a
                    LEFT JOIN ".BASE_DATOS.".tab_despac_contro b
                        ON a.num_despac = b.num_despac
                    LEFT JOIN ".BASE_DATOS.".tab_genera_noveda c
                        ON b.cod_noveda = c.cod_noveda
                    LEFT JOIN ".BASE_DATOS.".tab_genera_etapax d
                        ON c.cod_etapax = d.cod_etapax
                        WHERE a.num_despac = '" . $num_despac . "'
                          AND b.val_longit IS NOT NULL
                          AND b.val_latitu IS NOT NULL
                    ";
                
        //Execute query
        $queryReporte = new Consulta($queryReporte, $this -> conexion );
        $reporte = $queryReporte -> ret_matrix('a');
		$resp = false;

		//Assing "Etapa" value to total query
		if(count($reporte) > 0){
			return true;
		}
		return $resp;
	}

    public function getDespacCargue( $mTransp, $mTipReturn = NULL, $mSinFiltro = false ,$num_despac)
	{
		$mDespacPrcCargue = $this->getDespacPrcCargue( $mTransp, 'list', true ,$num_despac);

		$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat
				 INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu aa
				 		 ON yy.num_placax = aa.num_placax
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND ( xx.fec_salida IS NOT NULL   )
						AND yy.cod_transp = '".$mTransp[cod_transp]."' and xx.num_despac='".$num_despac."' ";

		$mSql .= ($mDespacPrcCargue != NULL || $mDespacPrcCargue != ''?" AND xx.num_despac NOT IN ( {$mDespacPrcCargue} ) ":"");
		$mConsult = new Consulta( $mSql, $this -> conexion );
		$mDespac = $mConsult -> ret_matrix('a');

		if( sizeof($mDespac) < 1 )
			return false;

		$mDespac = join( ',', GetColumnFromMatrix( $mDespac, 'num_despac' ) ); #Despachos en ruta Sin hora salida del sistema
		$mFiltro = $this->filtroNovedades($mDespac, '3,4,5,6');

		$mFiltro1 = ($mFiltro  != NULL || $mFiltro  != ''?" AND a.num_despac NOT IN ( {$mFiltro} ) ":"");

		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax, 
						UPPER(h.abr_tercer) AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera,
						IF(
                           b.cod_itiner IS NOT NULL AND b.cod_itiner != 0,
                           b.cod_itiner,
                           IF(
                               m.nom_operad IS NULL OR m.nom_operad = '',
                               'No Requiere',
                               'Por Iniciar'
                            )
                        ) AS itinerario
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					AND a.num_despac IN ( {$mDespac} )
					{$mFiltro1}
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
			  LEFT JOIN ".BD_STANDA.".tab_genera_opegps m
					 ON a.gps_operad = m.cod_operad
				  WHERE 1=1
				  ";
		
		$mConsult = new Consulta( $mSql, $this -> conexion );
		$mDespac = $mConsult -> ret_matrix('a');
		$mTipValida = $this->tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = $this->getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

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
						$mResult[$j][coliti] = $this->validaAlarmaItiner($mDespac[$i]);
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
					$mResult[$j][coliti] = $this->validaAlarmaItiner($mDespac[$i]);
					$j++;
				}
			} else {
				$mResult[$j] = $mDespac[$i];
				$mResult[$j][can_noveda] = $mData[can_noveda];
				$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
				$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
				$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
				$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
				$mResult[$j][fec_planea] = $mData[fec_planea];
				$mResult[$j][fec_planGl] = $mData[fec_planGl];
				$mResult[$j][coliti] = $this->validaAlarmaItiner($mDespac[$i]);
				$j++;
			}
		}
		
		if( $mTipReturn == 'array' )
			return GetColumnFromMatrix( $mResult, 'num_despac' );
		elseif( $mTipReturn == 'list' )
			return join(',', GetColumnFromMatrix($mResult, 'num_despac') );
		else
			return $mResult;
	}

    public function getDespacDescar( $mTransp, $mTipReturn = NULL, $mSinFiltro = false,$num_despac )
	{
		$mDespacPrcCargue = $this->getDespacPrcCargue( $mTransp, 'list', true ,$num_despac);
		$mDespacCargue = $this->getDespacCargue( $mTransp, 'list', true ,$num_despac);
		$mDespacCargue = ($mDespacCargue == NULL ? '0' : $mDespacCargue );

		#Despachos en ruta que ya finalizaron etapa Cargue
		$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu aa
						 ON yy.num_placax = aa.num_placax
						 WHERE 
						xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad = 'R'
						AND yy.ind_activo = 'S' 
						AND yy.cod_transp = '".$mTransp[cod_transp]."' and xx.num_despac='".$num_despac."'";
		$mSql .= ($mDespacPrcCargue != NULL || $mDespacPrcCargue != ''?" AND xx.num_despac NOT IN ( {$mDespacPrcCargue} ) ":"");
		$mSql .= ($mDespacCargue != NULL || $mDespacCargue != ''?" AND xx.num_despac NOT IN ( {$mDespacCargue} ) ":"");
		$mConsult = new Consulta( $mSql,  $this -> conexion );
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
		$mConsult = new Consulta( $mSql,  $this -> conexion );
		$mDespacDes = $mConsult -> ret_matrix('a');
		$mDespacDes = join( ',', GetColumnFromMatrix( $mDespacDes, 'num_despac' ) );		
		# Despachos para recorrer y verificar si estan en etapa Descargue Filtro 2
		$mSql = "	 SELECT a.num_despac, a.cod_tipdes 
					   FROM ".BASE_DATOS.".tab_despac_despac a 
					  WHERE a.num_despac IN ( {$mDespac} ) ";
		$mSql .= $mDespacDes != "" ? " AND a.num_despac NOT IN ( {$mDespacDes} ) " : "";
		$mConsult = new Consulta( $mSql,  $this -> conexion );
		$mDespac = $mConsult -> ret_matrix('i');

		$mDespacDes = $mDespacDes == "" ? '""' : $mDespacDes;


		#Recorre Despachos Para verificar Filtro 2
		foreach ($mDespac as $row)
		{
			$mNextPC = getNextPC(  $this -> conexion, $row[0] ); #Siguiente PC del Plan de ruta
			$mRutaDespac = getControDespac(  $this -> conexion, $row[0] ); # Ruta del Despacho
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
				$mTime = $this->getTimeDescargue( $mTransp, $row[1] );
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
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin, UPPER(k.abr_tercer) AS nom_genera,
						IF(
                           b.cod_itiner IS NOT NULL AND b.cod_itiner != 0,
                           b.cod_itiner,
                           IF(
                               m.nom_operad IS NULL OR m.nom_operad = '',
                               'No Requiere',
                               'Por Iniciar'
                            )
                        ) AS itinerario  
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
			LEFT JOIN ".BD_STANDA.".tab_genera_opegps m
					 ON a.gps_operad = m.cod_operad 
				  WHERE 1=1 ";

	
		$mConsult = new Consulta( $mSql, $this -> conexion );
		$mDespac = $mConsult -> ret_matrix('a');

		
		$mTipValida = $this->tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = $this->getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );

			#warning1
			
				$mResult[$j] = $mDespac[$i];
				$mResult[$j][can_noveda] = $mData[can_noveda];
				$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
				$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
				$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
				$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
				$mResult[$j][fec_planea] = $mData[fec_planea];
				$mResult[$j][fec_planGl] = $mData[fec_planGl];
				$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut];
				$mResult[$j][coliti] = $this->validaAlarmaItiner($mDespac[$i]);
				$j++;
			
		}
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

    public function getDespacPrcCargue( $mTransp, $mTipReturn = NULL, $mSinFiltro = false ,$num_despac)
	{
		$sal_inicio=date('Y-m-d')." 00:00:01";
		$sal_finxxx=date('Y-m-d')." 23:59:59"; 
		
		$hor_inicio="00:00:00";
		$hor_finxxx="23:59:59";
		

		$mSql = "	 SELECT xx.num_despac
					   FROM ".BASE_DATOS.".tab_despac_despac xx 
				 INNER JOIN ".BASE_DATOS.".tab_despac_vehige yy 
						 ON xx.num_despac = yy.num_despac
				  LEFT JOIN ".BASE_DATOS.".tab_despac_corona zz 
						 ON xx.num_despac = zz.num_dessat
				 INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu aa
						 ON yy.num_placax = aa.num_placax
					  WHERE xx.fec_salida IS NOT NULL 
						AND xx.fec_salida <= NOW() 
						AND (xx.fec_llegad IS NULL OR xx.fec_llegad = '0000-00-00 00:00:00')
						AND xx.ind_planru = 'S' 
						AND xx.ind_anulad in ('R', 'A')
						AND yy.ind_activo = 'S' 
						AND yy.cod_transp = '".$mTransp[cod_transp]."' 
                        AND xx.num_despac = '$num_despac'"; 
		
		$mConsult = new Consulta( $mSql, $this -> conexion );
		$mDespac = $mConsult -> ret_matrix('a');

		if( sizeof($mDespac) < 1 )
			return false;

		$mDespac = join( ',', GetColumnFromMatrix( $mDespac, 'num_despac' ) ); #Despachos en ruta Sin hora salida del sistema
		$mSql = "SELECT a.num_despac, a.cod_manifi, UPPER(b.num_placax) AS num_placax, 
						UPPER(h.abr_tercer) AS nom_conduc, h.num_telmov, a.fec_salida, 
						a.cod_tipdes, i.nom_tipdes, UPPER(c.abr_tercer) AS nom_transp, 
						IF(a.ind_defini = '0', 'NO', 'SI' ) AS ind_defini, a.tie_contra, 
						CONCAT(d.abr_ciudad, ' (', UPPER(LEFT(f.abr_depart, 4)), ')') AS ciu_origen, 
						CONCAT(e.abr_ciudad, ' (', UPPER(LEFT(g.abr_depart, 4)), ')') AS ciu_destin,
						l.cod_estado, a.ind_anulad, z.fec_plalle, a.fec_citcar, a.hor_citcar, k.num_solici, UPPER(o.abr_tercer) AS nom_genera,
						IF(
                           b.cod_itiner IS NOT NULL AND b.cod_itiner != 0,
                           b.cod_itiner,
                           IF(
                               m.nom_operad IS NULL OR m.nom_operad = '',
                               'No Requiere',
                               'Por Iniciar'
                            )
                        ) AS itinerario 
				   FROM ".BASE_DATOS.".tab_despac_despac a 
			 INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					 ON a.num_despac = b.num_despac 
					 AND a.num_despac IN ( {$mDespac} )
				AND a.num_despac NOT IN (  
						SELECT da.num_despac 
						  FROM ".BASE_DATOS.".tab_despac_noveda da 
					INNER JOIN ".BASE_DATOS.".tab_genera_noveda db 
							ON da.cod_noveda = db.cod_noveda 
						 WHERE da.num_despac IN ( {$mDespac} ) 
						   AND db.cod_etapax  IN ( 2,3,4,5 )
				)
				AND a.num_despac NOT IN (  
						SELECT ea.num_despac 
						  FROM ".BASE_DATOS.".tab_despac_contro ea 
					INNER JOIN ".BASE_DATOS.".tab_genera_noveda eb 
							ON ea.cod_noveda = eb.cod_noveda 
						 WHERE ea.num_despac IN ( {$mDespac} ) 
						   AND eb.cod_etapax  IN ( 2,3,4,5 )
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
			 INNER JOIN ".BASE_DATOS.".tab_despac_sisext k
			 		 ON a.num_despac = k.num_despac
			 LEFT JOIN ".BD_STANDA.".tab_genera_opegps m
					  ON a.gps_operad = m.cod_operad
			 LEFT JOIN ".BASE_DATOS.".tab_despac_corona z 
					 ON a.num_despac = z.num_dessat 
			  LEFT JOIN ( SELECT m.num_despac,n.num_consec,m.cod_estado
                            FROM ".BASE_DATOS.".tab_despac_estado m
                                INNER JOIN ( SELECT n.num_despac, MAX(n.num_consec) num_consec FROM tab_despac_estado n GROUP BY n.num_despac  ) n ON m.num_despac = n.num_despac
                                AND n.num_consec = m.num_consec
                                GROUP BY m.num_despac
                        ) l
                     ON a.num_despac = l.num_despac
              LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer o 
					 ON a.cod_client = o.cod_tercer  
				  WHERE k.ind_cumcar IS NULL AND k.fec_cumcar IS NULL AND
				  		a.fec_inicar IS NULL AND
				  		a.fec_fincar IS NULL AND				  		 
				  		a.fec_citcar >= DATE_SUB( '".$sal_inicio."', INTERVAl 5 DAY ) AND a.fec_citcar <= '{$sal_finxxx}'


				  		";
		
		//$mSql .=" GROUP BY a.num_despac";


		$mConsult = new Consulta( $mSql, $this -> conexion );
		$mDespac = $mConsult -> ret_matrix('a');

		$mTipValida = $this->tipValidaTiempo( $mTransp );

		# Verifica Novedades por despacho
		$j=0;
		$mResult = array();
		for( $i=0; $i<sizeof($mDespac); $i++ )
		{
			$mData = $this->getInfoDespac( $mDespac[$i], $mTransp, $mTipValida );
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
						$mResult[$j][fec_planea] = ($mData[fec_planea] == "1969-12-31 19:00:00"?$mDespac[$i][fec_salida]:$mData[fec_planea]);
						$mResult[$j][fec_plaprc] = ($mData[fec_plaprc] == "1969-12-31 19:00:00"?$mDespac[$i][fec_salida]:$mData[fec_plaprc]);
						$mResult[$j][coliti] = $this->validaAlarmaItiner($mDespac[$i]);
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
					$mResult[$j][fec_planea] = ($mData[fec_planea] == "1969-12-31 19:00:00"?$mDespac[$i][fec_salida]:$mData[fec_planea]);
					$mResult[$j][fec_plaprc] = ($mData[fec_plaprc] == "1969-12-31 19:00:00"?$mDespac[$i][fec_salida]:$mData[fec_plaprc]);
					$mResult[$j][coliti] = $this->validaAlarmaItiner($mDespac[$i]);
					$j++;
				}
			}else{
				$mResult[$j] = $mDespac[$i];
				$mResult[$j][can_noveda] = $mData[can_noveda];
				$mResult[$j][fec_ultnov] = $mData[fec_ultnov];
				$mResult[$j][ind_fuepla] = $mData[ind_fuepla];
				$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
				$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
				$mResult[$j][fec_planea] = ($mData[fec_planea] == "1969-12-31 19:00:00"?$mDespac[$i][fec_salida]:$mData[fec_planea]);
				$mResult[$j][fec_plaprc] = ($mData[fec_plaprc] == "1969-12-31 19:00:00"?$mDespac[$i][fec_salida]:$mData[fec_plaprc]);
				$mResult[$j][coliti] = $this->validaAlarmaItiner($mDespac[$i]);
				$j++;
			}
		}
		
		if( $mTipReturn == 'array' )
			return GetColumnFromMatrix( $mResult, 'num_despac' );
		elseif( $mTipReturn == 'list' )
			return join(',', GetColumnFromMatrix($mResult, 'num_despac') );	
		else
			return $mResult;
	}

    public function filtroNovedades($mDespac, $etapax){
		$mSql = "( 
			SELECT a.num_despac 
			  FROM ".BASE_DATOS.".tab_despac_noveda a 
		INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
				ON a.cod_noveda = b.cod_noveda 
			 WHERE a.num_despac IN ( {$mDespac} ) 
			   AND b.cod_etapax IN ( {$etapax} )
		  GROUP BY a.num_despac
		)
		UNION 
		( 
				SELECT c.num_despac 
				FROM ".BASE_DATOS.".tab_despac_contro c 
			INNER JOIN ".BASE_DATOS.".tab_genera_noveda d 
					ON c.cod_noveda = d.cod_noveda 
				WHERE c.num_despac IN ( {$mDespac} ) 
				AND d.cod_etapax IN ( {$etapax} )
		) ";
		$mConsult = new Consulta( $mSql,  $this -> conexion );
		$mDespacB = $mConsult -> ret_matrix('a');
		$mDespacB = join( ',', GetColumnFromMatrix( $mDespacB, 'num_despac' ) );
		return $mDespacB;
	}
    public function calTimeAlarma( $mDespac, $mTransp, $mIndCant = 0, $mFiltro = NULL, $mColor = NULL )
	{
		$mTipValida = $this->tipValidaTiempo( $mTransp );
	
		if( $mIndCant == 1 )
		{ #Define Cantidades seg?n estado
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

				if( $mDespac[$i][ind_fuepla] == '1' && $mDespac[$i][tiempo] < 0 ){
					$mPernoc = true;
				}
			}
			else{#Despacho Sin Novedades
				$mDespac[$i][tiempo] = getDiffTime( $mDespac[$i][fec_salida], self::$cHoy ); #Script /lib/general/function.inc
				
				$mDespac[$i][tiempoGl] = getDiffTime( $mDespac[$i][fec_salida], self::$cHoy ); #Script /lib/general/function.inc
			} 

			$mViewBa = $this->getView('jso_bandej');
			# Arma la matriz resultante 
			if( $mIndCant == 1 )
			{# Cantidades seg?n estado
				if($mDespac[$i][tiempoGl]){
					if( $mPernoc == true ){
						$mResult[est_pernoc]++;
						$mResult[enx_seguim]++;
					} #Pernoctacion
					elseif( $mDespac[$i][ind_finrut] == '1' ) #Por Llegada
						$mResult[fin_rutaxx]++;
					elseif( $mDespac[$i][ind_defini] == 'SI' ) #A Cargo Empresa
						$mResult[ind_acargo]++;
					elseif( $mDespac[$i][tiempoGl] < 0 ){ #Sin Retraso
						$mResult[sin_retras]++;
						$mResult[enx_seguim]++;
					}
					elseif( $mDespac[$i][tiempoGl] < 31 && $mDespac[$i][tiempoGl] >= 0 ){
						# 0 a 30
						$mResult[con_00A30x]++;
						$mResult[con_alarma]++;
						$mResult[enx_seguim]++;
					}
					elseif( $mDespac[$i][tiempoGl] < 61 && $mDespac[$i][tiempoGl] > 30 ) {
						# 31 a 60
						$mResult[con_31A60x]++;
						$mResult[con_alarma]++;
						$mResult[enx_seguim]++;
					}
					elseif( $mDespac[$i][tiempoGl] < 91 && $mDespac[$i][tiempoGl] > 60 ) {
						# 61 a 90
						$mResult[con_61A90x]++;
						$mResult[con_alarma]++;
						$mResult[enx_seguim]++;
					}
					elseif( $mDespac[$i][tiempoGl] > 90 ){
						# Mayor 90
						$mResult[con_91Amas]++;
						$mResult[con_alarma]++;
						$mResult[enx_seguim]++;
					} 
					else{
						continue;
					}
				}else{
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
			}
			else
			{# Colores e informaci?n del despacho seg?n estado

				if( $mFiltro == 'sinF' )
					$mBandera = true;
				elseif( $mPernoc != true && $mDespac[$i][ind_finrut] != '1' && $mDespac[$i][ind_defini] != 'SI' ) #Para los filtros por tiempos desde "Sin Retraso" hasta "Mayor 90"
					$mBandera = true;
				else
					$mBandera = false;

				if ($mViewBa->tie_alarma->ind_visibl==1) {
					#Arma Matriz resultante se?n fase
					if( ($mFiltro == 'est_pernoc' || $mFiltro == 'sinF' || $mFiltro == 'enx_seguim') && $mPernoc == true && $mDespac[$i][ind_defini] != 'SI' && $mDespac[$i][ind_finrut] != '1' )
					{ #Pernoctacion
						if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
							$mResult[neg_tieesp][$mNegTieesp] = $mDespac[$i];
							$mResult[neg_tieesp][$mNegTieesp][color] = $mColor[0];
							$mResult[neg_tieesp][$mNegTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[neg_tieesp][$mNegTieesp][fase] = 'est_pernoc';
							$mNegTieesp++;
						}else{
							$mResult[neg_tiempo][$mNegTiempo] = $mDespac[$i];
							$mResult[neg_tiempo][$mNegTiempo][color] = $mColor[0];
							$mResult[neg_tiempo][$mNegTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
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
							$mResult[neg_tieesp][$mNegTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[neg_tieesp][$mNegTieesp][fase] = 'sin_retras';
							$mNegTieesp++;
						}else{
							$mResult[neg_tiempo][$mNegTiempo] = $mDespac[$i];
							$mResult[neg_tiempo][$mNegTiempo][color] = $mColor[0];
							$mResult[neg_tiempo][$mNegTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[neg_tiempo][$mNegTiempo][fase] = 'sin_retras';
							$mNegTiempo++;
						}
					}
					elseif( ($mFiltro == 'con_00A30x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempoGl] < 31 && $mDespac[$i][tiempoGl] >= 0 && $mBandera == true )
					{ # 0 a 30
						if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
							$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
							$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[1];
							$mResult[pos_tieesp][$mPosTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_00A30x';
							$mPosTieesp++;
						}else{
							$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
							$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[1];
							$mResult[pos_tiempo][$mPosTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_00A30x';
							$mPosTiempo++;
						}
					}
					elseif( ($mFiltro == 'con_31A60x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempoGl] < 61 && $mDespac[$i][tiempoGl] > 30 && $mBandera == true )
					{ # 31 a 60
						if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
							$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
							$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[2];
							$mResult[pos_tieesp][$mPosTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_31A60x';
							$mPosTieesp++;
						}else{
							$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
							$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[2];
							$mResult[pos_tiempo][$mPosTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_31A60x';
							$mPosTiempo++;
						}
					}
					elseif( ($mFiltro == 'con_61A90x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempoGl] < 91 && $mDespac[$i][tiempoGl] > 60 && $mBandera == true )
					{ # 61 a 90
						if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
							$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
							$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[3];
							$mResult[pos_tieesp][$mPosTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_61A90x';
							$mPosTieesp++;
						}else{
							$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
							$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[3];
							$mResult[pos_tiempo][$mPosTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_61A90x';
							$mPosTiempo++;
						}
					}
					elseif( ($mFiltro == 'con_91Amas' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempoGl] > 90 && $mBandera == true )
					{ # Mayor 90
						if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
							$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
							$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[4];
							$mResult[pos_tieesp][$mPosTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_91Amas';
							$mPosTieesp++;
						}else{
							$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
							$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[4];
							$mResult[pos_tiempo][$mPosTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_91Amas';
							$mPosTiempo++;
						}
					}else{
						continue;
					}
				}else{
					#Arma Matriz resultante seï¿½n fase
					if( ($mFiltro == 'est_pernoc' || $mFiltro == 'sinF' || $mFiltro == 'enx_seguim') && $mPernoc == true && $mDespac[$i][ind_defini] != 'SI' && $mDespac[$i][ind_finrut] != '1' )
					{ #Pernoctacion
						if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
							$mResult[neg_tieesp][$mNegTieesp] = $mDespac[$i];
							$mResult[neg_tieesp][$mNegTieesp][color] = $mColor[0];
							$mResult[neg_tieesp][$mNegTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[neg_tieesp][$mNegTieesp][fase] = 'est_pernoc';
							$mNegTieesp++;
						}else{
							$mResult[neg_tiempo][$mNegTiempo] = $mDespac[$i];
							$mResult[neg_tiempo][$mNegTiempo][color] = $mColor[0];
							$mResult[neg_tiempo][$mNegTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
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
					elseif( ($mFiltro == 'sin_retras' || $mFiltro == 'sinF'|| $mFiltro == 'enx_seguim') && $mDespac[$i][tiempo] < 0 && $mBandera == true )
					{ #Sin Retraso
						if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
							$mResult[neg_tieesp][$mNegTieesp] = $mDespac[$i];
							$mResult[neg_tieesp][$mNegTieesp][color] = $mColor[0];
							$mResult[neg_tieesp][$mNegTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[neg_tieesp][$mNegTieesp][fase] = 'sin_retras';
							$mNegTieesp++;
						}else{
							$mResult[neg_tiempo][$mNegTiempo] = $mDespac[$i];
							$mResult[neg_tiempo][$mNegTiempo][color] = $mColor[0];
							$mResult[neg_tiempo][$mNegTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[neg_tiempo][$mNegTiempo][fase] = 'sin_retras';
							$mNegTiempo++;
						}
					}
					elseif( ($mFiltro == 'con_00A30x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempo] < 31 && $mDespac[$i][tiempo] >= 0 && $mBandera == true )
					{ # 0 a 30
						if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
							$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
							$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[1];
							$mResult[pos_tieesp][$mPosTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_00A30x';
							$mPosTieesp++;
						}else{
							$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
							$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[1];
							$mResult[pos_tiempo][$mPosTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_00A30x';
							$mPosTiempo++;
						}
					}
					elseif( ($mFiltro == 'con_31A60x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempo] < 61 && $mDespac[$i][tiempo] > 30 && $mBandera == true )
					{ # 31 a 60
						if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
							$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
							$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[2];
							$mResult[pos_tieesp][$mPosTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_31A60x';
							$mPosTieesp++;
						}else{
							$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
							$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[2];
							$mResult[pos_tiempo][$mPosTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_31A60x';
							$mPosTiempo++;
						}
					}
					elseif( ($mFiltro == 'con_61A90x' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempo] < 91 && $mDespac[$i][tiempo] > 60 && $mBandera == true )
					{ # 61 a 90
						if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
							$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
							$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[3];
							$mResult[pos_tieesp][$mPosTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_61A90x';
							$mPosTieesp++;
						}else{
							$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
							$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[3];
							$mResult[pos_tiempo][$mPosTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_61A90x';
							$mPosTiempo++;
						}
					}
					elseif( ($mFiltro == 'con_91Amas' || $mFiltro == 'sinF' || $mFiltro == 'con_alarma' || $mFiltro == 'enx_seguim') && $mDespac[$i][tiempo] > 90 && $mBandera == true )
					{ # Mayor 90
						if( $mDespac[$i][tie_contra] != '' ){ #Despacho con tiempo de seguimiento modificado
							$mResult[pos_tieesp][$mPosTieesp] = $mDespac[$i];
							$mResult[pos_tieesp][$mPosTieesp][color] = $mColor[4];
							$mResult[pos_tieesp][$mPosTieesp][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tieesp][$mPosTieesp][fase] = 'con_91Amas';
							$mPosTieesp++;
						}else{
							$mResult[pos_tiempo][$mPosTiempo] = $mDespac[$i];
							$mResult[pos_tiempo][$mPosTiempo][color] = $mColor[4];
							$mResult[pos_tiempo][$mPosTiempo][color2] = $this->despacRutaPlaca( $mTransp[cod_transp], $mDespac[$i][num_placax], $mDespac[$i][num_despac] );
							$mResult[pos_tiempo][$mPosTiempo][fase] = 'con_91Amas';
							$mPosTiempo++;
						}
					}else{
						continue;
					}
				}
			}
		}
		return $mResult;
	}

    private function orderMatrizDetail( $mNegTieesp, $mPosTieesp, $mNegTiempo, $mPosTiempo, $mNegFinrut, $mPosFinrut, $mNegAcargo, $mPosAcargo )
	{
		$mData = array();
		$mViewBa = $this->getView('jso_bandej');
		if($mViewBa->tie_alarma->ind_visibl==1){
			#Ordena Matriz Por tiempo
			$mNega = $mNegTieesp ? SortMatrix( $mNegTieesp, 'tiempoGl', 'ASC'  ) : array();
			$mPosi = $mPosTieesp ? SortMatrix( $mPosTieesp, 'tiempoGl', 'DESC' ) : array();
			$mData['tieesp'] = array_merge($mPosi, $mNega);

			$mNegTiempo = self::separateMatrix($mNegTiempo);
			$mPosTiempo = self::separateMatrix($mPosTiempo);

			$mNega = $mNegTiempo[0] ? SortMatrix( $mNegTiempo[0], 'tiempoGl', 'ASC'  ) : array();
			$mPosi = $mPosTiempo[0] ? SortMatrix( $mPosTiempo[0], 'tiempoGl', 'DESC' ) : array();
			$mData['tiemp0'] = array_merge($mPosi, $mNega);

			$mNega = $mNegTiempo[1] ? SortMatrix( $mNegTiempo[1], 'tiempoGl', 'ASC'  ) : array();
			$mPosi = $mPosTiempo[1] ? SortMatrix( $mPosTiempo[1], 'tiempoGl', 'DESC' ) : array();
			$mData['tiempo'] = array_merge($mPosi, $mNega);

			$mNega = $mNegFinrut ? SortMatrix( $mNegFinrut, 'tiempoGl', 'ASC'  ) : array();
			$mPosi = $mPosFinrut ? SortMatrix( $mPosFinrut, 'tiempoGl', 'DESC' ) : array();
			$mData['finrut'] = array_merge($mPosi, $mNega);

			$mNega = $mNegAcargo ? SortMatrix( $mNegAcargo, 'tiempoGl', 'ASC'  ) : array();
			$mPosi = $mPosAcargo ? SortMatrix( $mPosAcargo, 'tiempoGl', 'DESC' ) : array();
			$mData['acargo'] = array_merge($mPosi, $mNega);
		}else{
			#Ordena Matriz Por tiempo
			$mNega = $mNegTieesp ? SortMatrix( $mNegTieesp, 'tiempo', 'ASC'  ) : array();
			$mPosi = $mPosTieesp ? SortMatrix( $mPosTieesp, 'tiempo', 'DESC' ) : array();
			$mData['tieesp'] = array_merge($mPosi, $mNega);

			$mNegTiempo = self::separateMatrix($mNegTiempo);
			$mPosTiempo = self::separateMatrix($mPosTiempo);
			
			$mNega = $mNegTiempo[0] ? SortMatrix( $mNegTiempo[0], 'tiempo', 'ASC'  ) : array();
			$mPosi = $mPosTiempo[0] ? SortMatrix( $mPosTiempo[0], 'tiempo', 'DESC' ) : array();
			$mData['tiemp0'] = array_merge($mPosi, $mNega);

			$mNega = $mNegTiempo[1] ? SortMatrix( $mNegTiempo[1], 'tiempo', 'ASC'  ) : array();
			$mPosi = $mPosTiempo[1] ? SortMatrix( $mPosTiempo[1], 'tiempo', 'DESC' ) : array();
			$mData['tiempo'] = array_merge($mPosi, $mNega);

			$mNega = $mNegFinrut ? SortMatrix( $mNegFinrut, 'tiempo', 'ASC'  ) : array();
			$mPosi = $mPosFinrut ? SortMatrix( $mPosFinrut, 'tiempo', 'DESC' ) : array();
			$mData['finrut'] = array_merge($mPosi, $mNega);

			$mNega = $mNegAcargo ? SortMatrix( $mNegAcargo, 'tiempo', 'ASC'  ) : array();
			$mPosi = $mPosAcargo ? SortMatrix( $mPosAcargo, 'tiempo', 'DESC' ) : array();
			$mData['acargo'] = array_merge($mPosi, $mNega);
		}
		return $mData;
	}

    private function separateMatrix($mMatriz){
		$mResult = array(array(), array());

		if( $mMatriz ){
			foreach ($mMatriz as $row) {
				if( $row['can_noveda'] > 0 ){
					$mResult[1][] = $row;
				}else{
					$mResult[0][] = $row;
				}
			}
		}

		return $mResult;
	}

    private function despacRutaPlaca( $mCodTransp, $mNumPlacax, $mNumDespac )
	{
		$mColor = array('', '#FFFF66', '#FFC266');
		$mCantid=0;

		$mSql .= "SELECT a.num_despac 
					FROM ".BASE_DATOS.".tab_despac_despac a 
			  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
					  ON a.num_despac = b.num_despac 
				   WHERE a.fec_salida IS NOT NULL 
					 AND a.fec_salida <= NOW() 
					 AND (a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00') 
					 AND a.ind_planru = 'S' 
					 AND a.ind_anulad = 'R' 
					 AND b.ind_activo = 'S' 
					 AND b.num_placax LIKE '{$mNumPlacax}' 
					 AND b.cod_transp = '{$mCodTransp}'
					 AND a.num_despac != '{$mNumDespac}' 
					 AND a.ind_defini = '0' ";
		$mConsult = new Consulta( $mSql, $this -> conexion );
		$mDespac = $mConsult -> ret_matrix('i');

		if( sizeof($mDespac) > 0 )
		{
			foreach ($mDespac as $row)
			{
				$mUltPC = getControDespac( $this -> conexion, $row[0] );
				$mUltPC = end( $mUltPC );

				$mSql = "SELECT a.cod_noveda 
						   FROM ".BASE_DATOS.".tab_despac_noveda a 
						  WHERE a.num_despac = '{$row[0]}' 
							AND a.cod_contro = '{$mUltPC[cod_contro]}' ";
				$mConsult = new Consulta( $mSql, $this -> conexion );
				$mNovedad = $mConsult -> ret_matrix('i');

				if( sizeof($mNovedad) < 1 )
					$mCantid++;
			}
		}

		if( $mCantid == 0 )
			return $mColor[0];
		elseif( $mCantid == 1 )
			return $mColor[1];
		else
			return $mColor[2];
	}
}
$_INFORM = new DashBoard_Seguim_Table2();
?>