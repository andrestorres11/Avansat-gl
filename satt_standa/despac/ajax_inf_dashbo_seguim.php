<?php
/*! \file: ajax_despachos.php
 *  \brief: Archivo para las funciones Ajax relacionadas con despachos
 *  \author: 
 *  \author: 
 *  \version: 
 *  \date: 
 *  \bug: 
 *  \warning: 
 */

//

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
class AjaxDashBoard_Seguim
{
    var $conexion,
        $cod_aplica,
        $usuario;

    private static  $cHoy,$cTime = array( 'ind_desurb' => '30', 'ind_desnac' => '60' );

        function __construct($co = null, $us = null, $ca = null)
        {
			
            $this->conexion = $co;
            $this->usuario = $us;
            $this->cod_aplica = $ca;
            @include_once( "../lib/general/festivos.php" );
            @include( "../lib/ajax.inc" );
            @include_once( "../lib/general/constantes.inc" );
            $this -> conexion = $AjaxConnection;
            self::$cHoy = date("Y-m-d H:i:s");
            switch($_REQUEST[Option])
            {
                case "getGrafic1":
                    $this->getGrafic1();
                break;
                case "getGrafic2_3_4":
                    $this->getGrafic2_3_4();
                break;
                case "getGrafic5":
                    $this->getGrafic5();
                break;
                case "getGrafic6":
                    $this->getGrafic6();
                break;
                case "getGrafic7":
                    $this->getGrafic7();
                break;
                case "getGrafic8":
                    $this->getGrafic8();
                break;
                case "getGrafic9":
                    $this->getGrafic9();
                break;
                case "getGrafic10":
                    $this->getGrafic10();
                break;
                case "getGrafic11":
                    $this->getGrafic11();
                break;
				case "getGrafic12":
                    $this->getGrafic12();
                break;
				case "getGrafic13":
                    $this->getGrafic13();
                break;
            }

            //self::filtros();
        }

        private function getGrafic1(){
			
            $weekend = array('lunes','martes','miercoles','jueves','viernes','sabado','domingo');
            $names = array();
            $namesString = '';
            $dias = array();
            $diasString = '';
            $results=array();
            $results_=array();

			$Atipservic = $this->getTipServ();
			$mTipServic = '""';

            foreach($Atipservic as $servicio){
				$mTipServic .= $_REQUEST['tip_servic'.$servicio[0]] === true ? ',"'.$servicio[0].'"' : '';
			}
			$where_ = $mTipServic != '""' ? " AND cod_tipser IN (".$mTipServic.") " : "";

			$mSqlsub="select DISTINCT (
                SELECT cod_transp FROM tab_transp_tipser 
                WHERE cod_transp=b.cod_transp 
                $where_
                ORDER BY num_consec DESC 
                limit 1) as  cod_transp
            from tab_transp_tipser as b 
            where  b.cod_transp in (".$_REQUEST[cod_transp].") ";

            $consultasub  = new Consulta($mSqlsub, $this -> conexion);
            $despachos_validate = $consultasub -> ret_matriz();

            $cad_cod_transp='';
            foreach($despachos_validate as $value){
                $cad_cod_transp .='"'.$value['cod_transp'].'",';
            }
            $cad_cod_transp = substr($cad_cod_transp, 0, -1);
            
			
             $mSql="SELECT count(*) as sum,b.nom_tipdes as nombre,CASE WHEN DAYOFWEEK(a.fec_despac)=1 THEN 'domingo' WHEN DAYOFWEEK(a.fec_despac)=2 THEN 'lunes' WHEN DAYOFWEEK(a.fec_despac)=3 THEN 'martes' WHEN DAYOFWEEK(a.fec_despac)=4 THEN 'miercoles' WHEN DAYOFWEEK(a.fec_despac)=5 THEN 'jueves' WHEN DAYOFWEEK(a.fec_despac)=6 THEN 'viernes' WHEN DAYOFWEEK(a.fec_despac)=7 THEN 'sabado' END as dias
             FROM ".BASE_DATOS.".tab_despac_despac as a 
             inner join ".BASE_DATOS.".tab_genera_tipdes as b on a.cod_tipdes=b.cod_tipdes 
             inner join ".BASE_DATOS.".tab_despac_vehige as c on a.num_despac=c.num_despac
             where c.cod_transp in (".$cad_cod_transp.")
             and a.fec_despac BETWEEN DATE_ADD(NOW(), INTERVAL -7 DAY) AND NOW()
             GROUP by b.nom_tipdes,dias";
             
            $mConsult = new Consulta($mSql, $this -> conexion);
            $mResult = $mConsult->ret_matriz('a');
            foreach ($mResult as $key => $value)
            {
                $results[$value['nombre']][$value['dias']]=$value['sum'];
                array_push($names,$value['nombre']);
                $namesString.=$value['nombre'].',';
                array_push($dias,$value['dias']);
                
            }
            $names=array_unique($names);
            $dias=array_unique($dias);
            foreach($names as $value){
                foreach($weekend as $val){
                    if(!isset($results[$value][$val])){
                            $results[$value][$val]=0;
                    }
                }
            }
            foreach($names as $value){
                unset($arre);
                foreach($weekend as $val){
                    $arre[]=intval($results[$value][$val]);
                }
                $results_[$value]=$arre;
            }
            //$results['names']=$names;
            $results_['dias']=$weekend;
            
            echo json_encode($this->cleanArray($results_));
        }

    private function getGrafic2_3_4()
    {
        $mIndEtapa = 'ind_segprc';
        $mTransp = $this->getTranspServic( $mIndEtapa );
        $j=1;
        for($i=0; $i<sizeof($mTransp); $i++)
		{
            $mDespac = $this->getDespacPrcCargue( $mTransp[$i] );
            #Si la Transportadora tiene Despachos
            if( $mDespac != false )
            {
                $mData = $this->getTotalPrecargue( $mDespac, $mTransp[$i], 1 );
                $mData["enx_planta"] = sizeof($this->getDespacCargue( $mTransp[$i] ) );
                $horario = $this->setHorSeguim($mTransp[$i][cod_transp]);
                $mTransp[$i][hor_seguim] = $horario['horaMostrar'];
                $mTransp[$i][tip_servic] = $horario['tipServic'];
                
                $mTotal[2] += $mData[con_paradi];
                $mTotal[1] += $mData[con_paraco];
                $mTotal[3] += $mData[con_anulad];
				$mTotal[4] += $mData[enx_planta];
                $mTotal[5] += $mData[con_porter];
                $mTotal[6] += $mData[con_sinseg];
                $mTotal[7] += $mData[con_tranpl];
                $mTotal[8] += $mData[con_cnnlap];
                $mTotal[9] += $mData[con_cnlapx];
                $mTotal[10] += $mData[con_acargo];
                $j++;
            }
            else{
                $mTotal[1] += 0;
                $mTotal[2] += 0;
                $mTotal[3] += 0;
				$mTotal[4] += 0;
                $mTotal[5] += 0;
                $mTotal[6] += 0;
                $mTotal[7] += 0;
                $mTotal[8] += 0;
                $mTotal[9] += 0;
                $mTotal[10] += 0;
            }
        }
		for($i=1;$i<11;$i++){
			if(!isset($mTotal[$i])){
				$mTotal[$i]=0;
			}
		}
		
        $mTotal['data1']=array($mTotal[1],$mTotal[2]);
        $mTotal['data2']=array($mTotal[3],$mTotal[4]);
        $mTotal['data3']=array($mTotal[5],$mTotal[6],$mTotal[7],$mTotal[8],$mTotal[9],$mTotal[10]);
        
        echo json_encode($mTotal);
		
    }

    private function getGrafic5()
    {
        $mIndEtapa = 'ind_segcar';
        $mTransp = $this->getTranspServic( $mIndEtapa );
        for($i=0; $i<sizeof($mTransp); $i++)
		{
            $mDespac = $this->getDespacCargue( $mTransp[$i] );
            $mCodTransp .= $mCodTransp != '' ? ','.$mTransp[$i][cod_transp] : $mTransp[$i][cod_transp];
            if( $mDespac != false )
			{
                $horario = $this->setHorSeguim($mTransp[$i][cod_transp]);
				$mTransp[$i][hor_seguim] = $horario['horaMostrar'];
				$mTransp[$i][tip_servic] = $horario['tipServic'];
				$mData = $this->calTimeAlarma( $mDespac, $mTransp[$i], 1 );
                $mTotal[1] +=$mData[con_00A30x];
                $mTotal[2] +=$mData[con_31A60x];
                $mTotal[3] +=$mData[con_61A90x];
                $mTotal[4] +=$mData[con_91Amas];
            }
            else{
                $mTotal[1] +=0;
                $mTotal[2] +=0;
                $mTotal[3] +=0;
                $mTotal[4] +=0;
            }
        }
		for($i=1;$i<5;$i++){
			if(!isset($mTotal[$i])){
				$mTotal[$i]=0;
			}
		}
        $mTotal['data1']=array($mTotal[1],$mTotal[2],$mTotal[3],$mTotal[4]);
        echo json_encode($mTotal);
    }
    
    private function getGrafic6()
    {
        $mIndEtapa = 'ind_segtra';
        $mTransp = $this->getTranspServic( $mIndEtapa );
        for($i=0; $i<sizeof($mTransp); $i++)
		{
            if( $mTransp[$i][ind_segcar] == '0' && $mTransp[$i][ind_segdes] == '0' ){
                $mDespac = $this->getDespacTransi1( $mTransp[$i] );
            }
            else{
                $mDespac = $this->getDespacTransi2( $mTransp[$i] );
            }
            $mCodTransp .= $mCodTransp != '' ? ','.$mTransp[$i][cod_transp] : $mTransp[$i][cod_transp];

            if( $mDespac != false )
			{	
				$horario = $this->setHorSeguim($mTransp[$i][cod_transp]);
				$mTransp[$i][hor_seguim] = $horario['horaMostrar'];
				$mTransp[$i][tip_servic] = $horario['tipServic'];
				$mData = $this->calTimeAlarma( $mDespac, $mTransp[$i], 1 );
				
				$mTotal[1] += $mData[con_00A30x];
				$mTotal[2] += $mData[con_31A60x];
				$mTotal[3] += $mData[con_61A90x];
				$mTotal[4] += $mData[con_91Amas];

				$j++;
			}
            else{
                $mTotal[1] +=0;
                $mTotal[2] +=0;
                $mTotal[3] +=0;
                $mTotal[4] +=0;
            }
        }
		for($i=1;$i<5;$i++){
			if(!isset($mTotal[$i])){
				$mTotal[$i]=0;
			}
		}
        $mTotal['data1']=array($mTotal[1],$mTotal[2],$mTotal[3],$mTotal[4]);
        echo json_encode($mTotal);

    }

    private function getGrafic7()
    {
        $mIndEtapa = 'ind_segdes';
        $mTransp = $this->getTranspServic( $mIndEtapa );
        #Dibuja las Filas por Transportadora
		for($i=0; $i<sizeof($mTransp); $i++)
		{
            $mDespac = $this->getDespacDescar( $mTransp[$i] );
            $mCodTransp .= $mCodTransp != '' ? ','.$mTransp[$i][cod_transp] : $mTransp[$i][cod_transp];

            if( $mDespac != false )
			{	
				$horario = $this->setHorSeguim($mTransp[$i][cod_transp]);
				$mTransp[$i][hor_seguim] = $horario['horaMostrar'];
				$mTransp[$i][tip_servic] = $horario['tipServic'];
				$mData = $this->calTimeAlarma( $mDespac, $mTransp[$i], 1 );
				$mTotal[1] += $mData[con_00A30x];
				$mTotal[2] += $mData[con_31A60x];
				$mTotal[3] += $mData[con_61A90x];
				$mTotal[4] += $mData[con_91Amas];

				$j++;
			}
            else{
                $mTotal[1] +=0;
                $mTotal[2] +=0;
                $mTotal[3] +=0;
                $mTotal[4] +=0;
            }
        }
		for($i=1;$i<5;$i++){
			if(!isset($mTotal[$i])){
				$mTotal[$i]=0;
			}
		}
        $mTotal['data1']=array($mTotal[1],$mTotal[2],$mTotal[3],$mTotal[4]);
        echo json_encode($mTotal);

    }

    private function getGrafic8()
    {
		$Atipservic = $this->getTipServ();
			$mTipServic = '""';

            foreach($Atipservic as $servicio){
				$mTipServic .= $_REQUEST['tip_servic'.$servicio[0]] === true ? ',"'.$servicio[0].'"' : '';
			}
			$where_ = $mTipServic != '""' ? " AND cod_tipser IN (".$mTipServic.") " : "";
            
    
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
		
        $mSql="select count(b.nom_noveda) as sum,b.nom_noveda as nov 
         from ".BASE_DATOS.".tab_despac_despac as a 
         inner join ".BASE_DATOS.".tab_genera_noveda as b on a.cod_ultnov=b.cod_noveda 
         inner join ".BASE_DATOS.".tab_despac_vehige as c on a.num_despac=c.num_despac 
         where c.cod_transp in (".$cad_cod_transp.") 
         and a.fec_salida is NOT null 
         and b.cod_noveda in(63, 70, 52, 63,9,331,28,40,49,384,9996) 
         and a.fec_llegad is null and a.ind_planru='s' 
         and a.ind_anulad='r' 
         group by b.nom_noveda";
        $mConsult = new Consulta($mSql, $this -> conexion);
        $mResult = $mConsult->ret_matriz('a');
        $results['names']=array();
        $results['data']=array();
        foreach ($mResult as $key => $value)
        {
            array_push($results['names'],$value['nov']);
            array_push($results['data'],intval($value['sum']));
        }
		if(empty($results['names'])){
			array_push($results['names'],'');
		}
		if(empty($results['data'])){
			array_push($results['data'],0);
		}
        echo json_encode($this->cleanArray($results));
    }

    private function getGrafic9()
    {
		$Atipservic = $this->getTipServ();
			$mTipServic = '""';

            foreach($Atipservic as $servicio){
				$mTipServic .= $_REQUEST['tip_servic'.$servicio[0]] === true ? ',"'.$servicio[0].'"' : '';
			}
			$where_ = $mTipServic != '""' ? " AND cod_tipser IN (".$mTipServic.") " : "";
			
			$mSqlsub="select DISTINCT (
                SELECT cod_transp FROM tab_transp_tipser 
                WHERE cod_transp=b.cod_transp 
                $where_
                ORDER BY num_consec DESC 
                limit 1) as  cod_transp
            from tab_transp_tipser as b 
            where  b.cod_transp in (".$_REQUEST[cod_transp].") ";

            $consultasub  = new Consulta($mSqlsub, $this -> conexion);
            $despachos_validate = $consultasub -> ret_matriz();

            $cad_cod_transp='';
            foreach($despachos_validate as $value){
                $cad_cod_transp .='"'.$value['cod_transp'].'",';
            }
            $cad_cod_transp = substr($cad_cod_transp, 0, -1);

        $mSql="select count(*) as sum,if(a.gps_operad is null,'no',if(aa.nom_operad is null,if(ab.nom_operad is null,'no','si'),'si')) as estado 
        from tab_despac_despac as a 
        inner join ".BASE_DATOS.".tab_despac_vehige as c on a.num_despac=c.num_despac 
        LEFT OUTER JOIN ".BASE_DATOS.".tab_genera_opegps as aa on a.gps_operad=aa.nom_operad 
        LEFT OUTER JOIN satt_standa.tab_genera_opegps as ab on a.gps_operad=ab.nom_operad 
        where c.cod_transp in (".$cad_cod_transp.")
        and a.fec_salida is NOT null and a.fec_llegad is null and a.ind_planru='s' and a.ind_anulad='r' 
        GROUP by estado";

        $mConsult = new Consulta($mSql, $this -> conexion);
        $mResult = $mConsult->ret_matriz('a');
        $results['names']=array();
        $results['data']=array();
        foreach ($mResult as $key => $value)
        {
            array_push($results['names'],$value['estado']);
            array_push($results['data'],intval($value['sum']));
        }
		if(empty($results['names'])){
			array_push($results['names'],'');
		}
		if(empty($results['data'])){
			array_push($results['data'],0);
		}
        echo json_encode($this->cleanArray($results));
    }

    private function getGrafic10()
    {
        $Results=array();
        $mResult=explode(',',$_REQUEST[cod_transp]);

        foreach($mResult as $value){
			if(strlen($value)>2){
				$mSql2="select if(cod_operad is null,'no','si') as estado from ".BASE_DATOS.".tab_interf_parame where cod_transp='".$value."' and cod_operad =53";
				$mConsult2 = new Consulta($mSql2, $this -> conexion);
				$mResult2 = $mConsult2->ret_matriz('a');
				if(empty($mResult2)){
					array_push($Results,'no');
				}
				foreach($mResult2 as $val){
					array_push($Results,$val['estado']);
				}
			}
        }
        $i=0;$j=0;
        foreach($Results as $value){
            if($value=='si'){
                $i++;
            }else{
                $j++;
            }
        }
    
        $mTotal['data']=array($i,$j);
        echo json_encode($this->cleanArray($mTotal));
    }

    private function getGrafic11()
    {
        $mSql="select count(*) as sum,if(cod_itiner is null,'Sin itinerario','Con itinerario') as estado 
        from ".BASE_DATOS.".tab_despac_despac as a 
        inner join ".BASE_DATOS.".tab_despac_vehige as b on a.num_despac=b.num_despac 
        where b.cod_transp in (".$_REQUEST[cod_transp].")  and
        a.fec_salida is NOT null 
        and a.fec_llegad is null 
        and a.ind_planru='s' 
        and a.ind_anulad='r' 
        GROUP by estado";
        $mConsult = new Consulta($mSql, $this -> conexion);
        $mResult = $mConsult->ret_matriz('a');
        $results['names']=array();
        $results['data']=array();
        foreach ($mResult as $key => $value)
        {
            array_push($results['names'],$value['estado']);
            array_push($results['data'],intval($value['sum']));
        }
		if(empty($results['names'])){
			array_push($results['names'],'');
		}
		if(empty($results['data'])){
			array_push($results['data'],0);
		}
        echo json_encode($this->cleanArray($results));
    }

	private function getGrafic12()
	{
		$sql =" SELECT LOWER(cod_usuari) cod_usuari, CONCAT( UPPER(nom_usuari ), ' - ', LOWER(cod_usuari) ) AS nom_usuari
                  FROM ".BASE_DATOS.".tab_genera_usuari
                 WHERE ind_estado = '1' 
                   AND ( cod_perfil IN(7,8,73,713,833) AND cod_usuari NOT LIKE '%eal%' OR nom_usuari NOT LIKE '%eal%' OR cod_usuari LIKE '%ecl%' OR nom_usuari LIKE '%ecl%' OR nom_usuari LIKE '%oal%' OR  cod_usuari LIKE '%oal%' ) 
                ORDER BY 2";
        $consulta = new Consulta($sql, $this -> conexion);
        $usuarios=$consulta->ret_matrix("a");
        $usuarios_='';
        foreach ($usuarios as $key => $value) {
            $usuarios_=$usuarios_.'"'.$value['cod_usuari'].'",';
         } 

		$usuarios_=substr($usuarios_, 0, -1);
		 
		$hor_finali = date("H").':59';
		$fec_inicia = date("Y-m-d")." ".date("H").":00";
		$fec_finali = date("Y-m-d")." ".date("H").":59";
		$intervalo = " 1 day"; // para los dias

		$and = ", DATE_FORMAT(a.fec_creaci, '%Y-%m-%d %H') AS fec1";
        $fec1 = ", x.fec1";
        $group2 = " GROUP BY x.usr_creaci $fec1 ";

		$search = explode(",","?,?,?,?,?,?,?,?,?,?,?,?,ï¿½,ï¿½,ï¿½,ï¿½,ï¿½,ï¿½,?ï¿½,?ï¿½,?ï¿½,?ï¿½,?ï¿½,?ï¿½,??,? ,??,? ,??,???,?? ,ï¿½,?");
		$replace = explode(",","?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,\",\",?,&uuml;");
		$usuarios_= str_replace($search, $replace, $usuarios_);
		$u = " AND a.usr_creaci IN ($usuarios_)";

		$datoSelect = "x.num_despac";
        $datoGroup = "x.usr_creaci"; 
        $datoLabel = "x.usr_creaci";
        $datoCodig = "x.cod_perfil";

		$sql = "SELECT COUNT(DISTINCT($datoSelect)) AS can_despac, COUNT($datoSelect) AS can_regist, 
                       $datoLabel AS usr_creaci, $datoCodig AS cod_perfil $fec1
                  FROM (
                            (
                                    SELECT a.cod_noveda, d.nom_noveda, a.num_despac, a.fec_creaci, a.usr_creaci, b.cod_perfil  $and
                                      FROM ".BASE_DATOS.".tab_despac_contro a
                                INNER JOIN ".BASE_DATOS.".tab_genera_usuari b ON b.cod_usuari = a.usr_creaci
                                INNER JOIN ".BASE_DATOS.".tab_genera_perfil c ON c.cod_perfil = b.cod_perfil
                                INNER JOIN ".BASE_DATOS.".tab_genera_noveda d ON a.cod_noveda = d.cod_noveda
                                     WHERE a.fec_creaci >= '$fec_inicia' AND  a.fec_creaci <= '$fec_finali'  $u
                                           
                            )
                            UNION ALL
                            (
                                    SELECT a.cod_noveda, d.nom_noveda, a.num_despac, a.fec_creaci, a.usr_creaci, b.cod_perfil $and
                                      FROM ".BASE_DATOS.".tab_despac_noveda a 
                                INNER JOIN ".BASE_DATOS.".tab_genera_usuari b ON b.cod_usuari = a.usr_creaci 
                                INNER JOIN ".BASE_DATOS.".tab_genera_perfil c ON c.cod_perfil = b.cod_perfil
                                INNER JOIN ".BASE_DATOS.".tab_genera_noveda d ON a.cod_noveda = d.cod_noveda
                                     WHERE a.fec_creaci >= '$fec_inicia' AND  a.fec_creaci <= '$fec_finali'  $u
                                           
                            )
                       ) x 
              GROUP BY $datoGroup $fec1 
              ORDER BY $datoGroup $fec1";
        $consulta = new Consulta($sql, $this -> conexion);
        $results=$consulta->ret_matrix("a");
		$results_['names']=array();
		$results_['data']=array();
		foreach($results as $value){
			array_push($results_['names'],$value['usr_creaci']);
			array_push($results_['data'],$value['can_despac']);
		}
		if(empty($results_['names'])){
			array_push($results_['names'],'');
		}
		if(empty($results_['data'])){
			array_push($results_['data'],0);
		}
		echo json_encode($this->cleanArray($results_));
	}

	private function getGrafic13(){
			
		$mIndEtapa = 'ind_segctr';
		$mTransp = $this->getTranspServic( $mIndEtapa );

		$mUsrAsignaAnt = "";
		$mCodTransp = "";
		$mCodTranspUS = "";
		$titulos = 1;
		$asignaciones = $this->mergeUser($mTransp);
		$result=array();
		$result['users']=array();
		$result['data1']=array();
		$result['data2']=array();
		$result['data3']=array();
		$result['data4']=array();
		foreach($asignaciones as $key=>$usuario){
			$mTotalUS = $this->calculaTotal($usuario);
			//$mCodTranspUS = $this->darTransportadoras($usuario);
			array_push($result['users'],$key);
			array_push($result['data1'],$mTotalUS[3]);
			array_push($result['data2'],$mTotalUS[4]);
			array_push($result['data3'],$mTotalUS[5]);
			array_push($result['data4'],$mTotalUS[6]);
		}
		echo json_encode($this->cleanArray($result));
		
	}

	private function calculaTotal($asignaciones_usuario){
		$array = array();
		foreach($asignaciones_usuario as $emptra){
			$mDespac = $this->getDespacControl( $emptra );
			$mData = $this->calTimeAlarma( $mDespac, $emptra['cod_transp'], 1 );	
			$array[0] += $mData[enx_seguim] + $mData[ind_acargo];
			$array[1] += $mData[sin_retras];
			$array[2] += $mData[con_alarma];
			$array[3] += $mData[con_00A30x];
			$array[4] += $mData[con_31A60x];
			$array[5] += $mData[con_61A90x];
			$array[6] += $mData[con_91Amas];
			$array[7] += $mData[est_pernoc];
			$array[9] += $mData[ind_acargo];
		}
		return $array;
	}

	private function getDespacControl( $mTransp )
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
			 LEFT JOIN ".BD_STANDA.".tab_genera_opegps m
					 ON a.gps_operad = m.cod_operad
				  WHERE 1=1 ";
		$mSql .= ($_REQUEST['cod_client'] != NULL || $_REQUEST['cod_client'] != ''?" AND a.cod_client IN (".str_replace(array('"",','"'),array('',''),$_REQUEST['cod_client']).") ":"");
		#Filtros por Formulario
		#$mSql .= $_REQUEST[ind_limpio] ? " AND a.ind_limpio = '{$_REQUEST[ind_limpio]}' " : ""; #warning1
		
		
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
				$mResult[$j][nov_especi] = $mData[nov_especi];
				$mResult[$j][ind_alarma] = $mData[ind_alarma];
				$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
				$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
				$mResult[$j][fec_planea] = $mData[fec_planea];
				$mResult[$j][fec_planGl] = $mData[fec_planGl];
				$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut]; #Aplica para empresas que solo tienen parametrizado seguimiento Transito
				$mResult[$j][coliti] = $this->validaAlarmaItiner($mDespac[$i]);
				$j++;
			
		}

		return $mResult;
	}


	private function printInformContr( $mIndEtapa, $mTittle )
	{
		$mTransp = self::getTranspServic( $mIndEtapa );
		echo "<pre style='display:none'>"; print_r($mTransp ); echo "</pre>";
		$mLimitFor = self::$cTypeUser[tip_perfil] == 'OTRO' ? sizeof($mTittle[texto]) : sizeof($mTittle[texto])-1;
		$mHtml = '';
		//Variables necesarias
		$mUsrAsignaAnt = "";
		$mCodTransp = "";
		$mCodTranspUS = "";
		$titulos = 1;
		
		$asignaciones = $this->mergeUser($mTransp);
		
		$mTotalGen = $this->calculaTotal($mTransp);
		$cod_transportadoras = $this->darTransportadoras($mTransp);

		$arm_totalGen  = '<tr class="GridviewScrollItem" >';
		$arm_totalGen .= '<th class="classTotal" nowrap="" align="right" colspan="5">GRAN TOTAL:</th>';
		$arm_totalGen .= '<th class="classTotal" nowrap="" align="center" onclick="showDetailBand(\'enx_seguim\', \''.$mIndEtapa.'\', \''.$cod_transportadoras .'\');" style="cursor: pointer;" >'.$mTotalGen[0].'</th>';
		$arm_totalGen .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalGen[1] == 0 ? '' : 'onclick="showDetailBand(\'sin_retras\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotalGen[1].'</th>';
		$arm_totalGen .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalGen[2] == 0 ? '' : 'onclick="showDetailBand(\'con_alarma\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotalGen[2].'</th>';
		$arm_totalGen .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalGen[3] == 0 ? '' : 'onclick="showDetailBand(\'con_00A30x\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotalGen[3].'</th>';
		$arm_totalGen .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalGen[4] == 0 ? '' : 'onclick="showDetailBand(\'con_31A60x\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotalGen[4].'</th>';
		$arm_totalGen .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalGen[5] == 0 ? '' : 'onclick="showDetailBand(\'con_61A90x\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotalGen[5].'</th>';
		$arm_totalGen .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalGen[6] == 0 ? '' : 'onclick="showDetailBand(\'con_91Amas\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotalGen[6].'</th>';
		$arm_totalGen .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalGen[7] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotalGen[7].'</th>';

		if( $mIndEtapa != 'ind_segcar' ){
			/*
			$mHtml1 .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotal[8] == 0 ? '' : 'onclick="showDetailBand(\'fin_rutaxx\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotalGen[8].'</th>';
			*/
		}	
		$arm_totalGen .= '<th class="classTotal" nowrap="" align="center" '. ( $mTotalGen[9] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \''.$mCodTransp .'\');" style="cursor: pointer;"' ) .' >'.$mTotalGen[9].'</th>';
		if( self::$cTypeUser[tip_perfil] == 'OTRO' ){
		$arm_totalGen .= '<th class="classTotal" nowrap="" align="center">&nbsp;</th>';
		}	
		$arm_totalGen .= '</tr>';
		$armado = $arm_totalGen;
		foreach($asignaciones as $key=>$usuario){
			$mTotalUS = $this->calculaTotal($usuario);
			$mCodTranspUS = $this->darTransportadoras($usuario);
			$mClassEv = str_replace(",", "_",str_replace(".", "_", str_replace(" ", "_", $key)));
			$armado .= '<tr class="GridviewScrollItem" >';
			$armado .= '<th class="classTotal ui-state-default" colspan="4" style="cursor: pointer; font-weight: bold;" align="left" onclick="acordion(\''.$mClassEv.'\')">* '.strtoupper($key).'</th>';
			$armado .= '<th class="classTotal ui-state-default"   align="right">TOTAL:</th>';
			$armado .= '<th class="classTotal ui-state-default"  	align="center" onclick="showDetailBand(\'enx_seguim\', \''.$mIndEtapa.'\', \''.$mCodTranspUS .'\');" style="cursor: pointer; font-weight: bold;" >'.$mTotalUS[0].'</th>';
			$armado .= '<th class="classTotal ui-state-default" 	align="center" '. ( $mTotalUS[1] == 0 ? '' : 'onclick="showDetailBand(\'sin_retras\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer; font-weight: bold;"' ) .' >'.$mTotalUS[1].'</th>';
			$armado .= '<th class="classTotal ui-state-default"  	align="center" '. ( $mTotalUS[2] == 0 ? '' : 'onclick="showDetailBand(\'con_alarma\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer; font-weight: bold;"' ) .' >'.$mTotalUS[2].'</th>';
			$armado .= '<th class="classTotal ui-state-default" 	align="center" '. ( $mTotalUS[3] == 0 ? '' : 'onclick="showDetailBand(\'con_00A30x\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer; font-weight: bold;"' ) .' >'.$mTotalUS[3].'</th>';
			$armado .= '<th class="classTotal ui-state-default" 	align="center" '. ( $mTotalUS[4] == 0 ? '' : 'onclick="showDetailBand(\'con_31A60x\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer; font-weight: bold;"' ) .' >'.$mTotalUS[4].'</th>';
			$armado .= '<th class="classTotal ui-state-default"  	align="center" '. ( $mTotalUS[5] == 0 ? '' : 'onclick="showDetailBand(\'con_61A90x\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer; font-weight: bold;"' ) .' >'.$mTotalUS[5].'</th>';
			$armado .= '<th class="classTotal ui-state-default" 	align="center" '. ( $mTotalUS[6] == 0 ? '' : 'onclick="showDetailBand(\'con_91Amas\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer; font-weight: bold;"' ) .' >'.$mTotalUS[6].'</th>';
			$armado .= '<th class="classTotal ui-state-default"  	align="center" '. ( $mTotalUS[7] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer; font-weight: bold;"' ) .' >'.$mTotalUS[7].'</th>';
			$armado .= '<th class="classTotal ui-state-default"  	align="center" '. ( $mTotalUS[9] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \''.$mCodTranspUS.'\');" style="cursor: pointer; font-weight: bold;"' ) .' >'.$mTotalUS[9].'</th>';
			$armado .= '<th class="classTotal ui-state-default"  	align="center" style="font-weight: bold;"></th>';
			$armado .= '</tr>';
			$j=1;
			foreach($usuario as $keyy=>$emptra){
				$mDespac = self::getDespacControl( $emptra );
				//Extrae los datos relacionados al tipo de servicio y horario.
				$horario = self::setHorSeguim($emptra['cod_transp']);
				$hor_seguim = $horario['horaMostrar'];
				$tip_servic = $horario['tipServic'];
				$mData = self::calTimeAlarma( $mDespac, $emptra['cod_transp'], 1 );
				if($mDespac != false){
					$armado .= '<tr class="GridviewScrollItem rowData '.$mClassEv.'" onclick="this.style.background=this.style.background==\'#CEF6CE\'?\'#eeeeee\':\'#CEF6CE\';"><div>';
					$armado .= 	'<th class="classCell" nowrap="" align="left">'.$j.'</th>';
					$armado .= 	'<td class="classCell" nowrap="" align="left">'.$emptra[nom_tipser].'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="left">'.$tip_servic.'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="left">'.$hor_seguim.'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="left">'.$emptra[nom_transp].'</td>';
						/* Suma muestra la suma de los despachos en seguimiento + a cargo */
					$armado .= 	'<td class="classCell" nowrap="" align="center" onclick="showDetailBand(\'enx_seguim\', \''.$mIndEtapa.'\', \''.$emptra[cod_transp].'\');" style="cursor: pointer" >'.($mData[enx_seguim] + $mData[ind_acargo]).'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[sin_retras] == 0 ? '' : 'onclick="showDetailBand(\'sin_retras\', \''.$mIndEtapa.'\', \''.$emptra[cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[sin_retras].'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_alarma] == 0 ? '' : 'onclick="showDetailBand(\'con_alarma\', \''.$mIndEtapa.'\', \''.$emptra[cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_alarma].'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_00A30x] == 0 ? '' : 'onclick="showDetailBand(\'con_00A30x\', \''.$mIndEtapa.'\', \''.$emptra[cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_00A30x].'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_31A60x] == 0 ? '' : 'onclick="showDetailBand(\'con_31A60x\', \''.$mIndEtapa.'\', \''.$emptra[cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_31A60x].'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_61A90x] == 0 ? '' : 'onclick="showDetailBand(\'con_61A90x\', \''.$mIndEtapa.'\', \''.$emptra[cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_61A90x].'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[con_91Amas] == 0 ? '' : 'onclick="showDetailBand(\'con_91Amas\', \''.$mIndEtapa.'\', \''.$emptra[cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[con_91Amas].'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[est_pernoc] == 0 ? '' : 'onclick="showDetailBand(\'est_pernoc\', \''.$mIndEtapa.'\', \''.$emptra[cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[est_pernoc].'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="center" '. ( $mData[ind_acargo] == 0 ? '' : 'onclick="showDetailBand(\'ind_acargo\', \''.$mIndEtapa.'\', \''.$emptra[cod_transp].'\');" style="cursor: pointer"' ) .' >'.$mData[ind_acargo].'</td>';
					$armado .= 	'<td class="classCell" nowrap="" align="center">'. $key.'</td>';
					$armado .= 	'</tr>';
					$j++;
				}
			}		
		}
		$armado.=$arm_totalGen;
		#Dibuja la Tabla Completa
		$mHtml2  = '<table class="classTable" id="GridView11" width="100%" cellspacing="0" cellpadding="0" align="center">';
		$mHtml2 .= 	'<tr class="GridviewScrollHeader">';
		for ($i=0; $i < $mLimitFor; $i++){
			$mHtml2 .= '<th class="classHead bt '.$mTittle[style][$i].'" align="center">'.$mTittle[texto][$i].'</th>';
		}
		$mHtml2 .= 	'</tr>';

		$mHtml2 .= $mHtml1;
		$mHtml2 .= $armado;
		$mHtml2 .= $mHtml1;

		$mHtml2 .= '</table>';

		return utf8_decode($mHtml2);
	}

	private function mergeUser($transp){
		$array = array();
		foreach($transp as $key=>$value){
			$position = $value['usr_asigna'] != NULL ? $value['usr_asigna']: 'SIN ASIGNAR';
			if($position != 'SIN ASIGNAR')
			{
				$usuarios = explode(", ", $position);
				//Array sin usuarios repetidos
				$usuarios_unic = array_unique($usuarios);
				$ind_varios = false;
				if(count($usuarios_unic)>1){
					$key_users = '';
					foreach($usuarios_unic as $key=>$usuario){
						$key_users .= $usuario;
						if(($key+1) < count($usuarios_unic)){
							$key_users.=', ';
						}
					}
					$array[$key_users][] = $value;
				}else{
					$array[$usuarios[0]][] = $value;
				}
			}
		}
		
		return $array;
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
		$mLisTransp = $this->getTranspCargaControlador();

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
		
		$mSql .= $_REQUEST[cod_transp] ? " AND a.cod_transp IN ( {$_REQUEST[cod_transp]} ) " : "";
		

		$mCodUsuari = explode(',', $_REQUEST[cod_usuari]);
		$mSinFiltro = false;
		foreach ($mCodUsuari as $key => $value) {
			if( $value == '"SIN"' ){
				$mSinFiltro = true;
				break;
			}
		}

		#Filtro Por Usuario Asignado
		if( $mSinFiltro == true )
			$mSql .= " AND ( h.cod_transp IS NULL OR h.cod_usuari IN ({$_REQUEST[cod_usuari]}) )";
		else
			$mSql .= $_REQUEST[cod_usuari] ? " AND h.cod_usuari IN ( {$_REQUEST[cod_usuari]} ) " : "";

		#Otros Filtros
		$mSql .= $mTipServic != '""' ? " AND a.cod_tipser IN (".$mTipServic.") " : "";

		$mSql .= $mHorTipSer != '' ? " AND i.tip_servic IN (".$mHorTipSer.") " : "";

		$mSql .= $mFilHorasx;
		
		$mSql .= " GROUP BY a.cod_transp ORDER BY h.cod_usuari, a.nom_transp ASC ";
		$mConsult = new Consulta( $mSql, $this -> conexion );

		return $mConsult -> ret_matrix('a');
	}

    

    private function getTransport()
    {
            $mSql=" SELECT 
                        b.cod_tercer, 
                        b.nom_tercer 
                    FROM 
                        ".BASE_DATOS.".tab_tercer_emptra a 
                    INNER JOIN tab_tercer_tercer b ON a.cod_tercer = b.cod_tercer 
                    AND b.cod_estado = 1
                    ORDER BY b.nom_tercer ASC
                    ;
            ";
            $mConsult = new Consulta($mSql, $this -> conexion);
            $mResult = $mConsult->ret_matriz('a');
            $select='';
            foreach($mResult as $value){
                $select.='<option value="'.$value['cod_tercer'].'">'.$value['nom_tercer'].'</option>';
            }
            echo $select;
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

    function cleanArray($array)
    {
            $arrayReturn = array();
            //Convert function
            $convert = function($value){
                if(is_string($value)){
                    return utf8_encode($value);
                }
                return $value;
            };
            //Go through data
            foreach ($array as $key => $value) {
                //Validate sub array
                if(is_array($value)){
                    //Clean sub array
                    $arrayReturn[utf8_encode($key)] = self::cleanArray($value);
                }else{
                    //Clean value
                    $arrayReturn[utf8_encode($key)] = $convert($value);
                }
            }
            //Return array
            return $arrayReturn;
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

    private function getTranspCargaControlador() {
		return null;
	}

    public function getDespacPrcCargue( $mTransp, $mTipReturn = NULL, $mSinFiltro = false )
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
						AND yy.cod_transp = '".$mTransp[cod_transp]."' "; 
		
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

    private function getTotalPrecargue( $mDespac, $mTransp, $mIndCant = 0, $mFiltro = NULL, $mColor = NULL )
	{

		$mTipValida = $this->tipValidaTiempo( $mTransp );
		$fec_sisact = date("Y-m-d");
		$fec_sisHoraIni = date("Y-m-d")." ".($_REQUEST['hor_inicio']?$_REQUEST['hor_inicio']:" 00:00:01");
		$fec_sisHoraFin = date("Y-m-d")." ".($_REQUEST['hor_finxxx']?$_REQUEST['hor_finxxx']:" 23:59:59");
		if( $mIndCant == 1 )
		{ #Define Cantidades seg?n estado
			$mResult["con_paradi"] = 0;//para el dia
			$mResult["con_paraco"] = 0;//para el corte
			$mResult["con_anulad"] = 0;//anulados
			$mResult["con_planta"] = 0;//en planta
			$mResult["con_porter"] = 0;//porteria
			$mResult["con_sinseg"] = 0;//SIN COMUNICACION
			$mResult["con_tranpl"] = 0;//transito a planta
			$mResult["con_cnnlap"] = 0;//con novedad no llegada a planta
			$mResult["con_cnlapx"] = 0;//con novedad llegada a planta
			$mResult["con_acargo"] = 0;//con novedad llegada a planta
		}
		else
		{
			$con_paradi = 0;
			$con_paraco = 0;
			$con_anulad = 0;
			$con_planta = 0;
			$con_porter = 0;
			$con_sinseg = 0;
			$con_tranpl = 0;
			$con_cnnlap = 0;
			$con_cnlapx = 0;
			$con_acargo = 0;
		}
		for ($i=0; $i < sizeof($mDespac); $i++)
		{
			if( $mDespac[$i]["fec_planea"] )
			{	#Despacho con Novedades
				$mDespac[$i]["tiempS"] = getDiffTime( $mDespac[$i]["fec_planea"], self::$cHoy ); #Script /lib/general/function.inc
				$mDespac[$i]["tiempSGl"] = getDiffTime( $mDespac[$i]["fec_plaprc"], self::$cHoy ); #Script /lib/general/function.inc
			}
			if($mDespac[$i]["fec_citcar"])
			{	#Despacho Sin Novedades
				$mDespac[$i]["tiempo"] = getDiffTime( $mDespac[$i]["fec_citcar"]." ".$mDespac[$i]['hor_citcar'], self::$cHoy ); #Script /lib/general/function.inc
			} 

			if( $mIndCant == 1 )
			{ 
				#Valida si el deaspacho esta acargo de la empresa
				if($mDespac[$i]["ind_defini"] == 'SI' && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_acargo"]++;
					continue;
				}
				//if( strtotime($mDespac[$i]['fec_citcar']) >= strtotime(date("d-m-Y ",time()))  )
				 
				if( $mDespac[$i]['fec_citcar'] <= $fec_sisact && $mDespac[$i]["ind_anulad"] != "A" ) // Hora actual
				{
					//$mResult["con_paradi"]++;
				}
				if( strtotime(date( "Y-m-d H:i:s", strtotime($mDespac[$i]['fec_citcar']." ".$mDespac[$i]['hor_citcar'] ) )) >=  strtotime(date( "Y-m-d H:i:s", strtotime( $fec_sisHoraIni ) )) && strtotime(date( "Y-m-d H:i:s", strtotime($mDespac[$i]['fec_citcar']." ".$mDespac[$i]['hor_citcar'] ) )) <=  strtotime(date( "Y-m-d H:i:s", strtotime( $fec_sisHoraFin ) )) && $mDespac[$i]["ind_anulad"] != "A" ) // del d?a actual
				{
					$mResult["con_paraco"]++;
				}
				if($mDespac[$i]["ind_anulad"] == "A")
				{
					$mResult["con_anulad"]++;
				}
				elseif($mDespac[$i]["fec_plalle"]!="" && $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00")
				{
					$mResult["con_planta"]++;
				}else{
					switch ($mDespac[$i]['cod_estado']) {
						case '1':
							$mResult["con_porter"]++;
							break;
						case '2':
							$mResult["con_sinseg"]++;
							break;
						case '3':
							$mResult["con_tranpl"]++;
							break;
						case '4':
							$mResult["con_cnnlap"]++;
							break;
						case '5':
							$mResult["con_cnlapx"]++;
							break;
						default:
							$mResult["con_paradi"]++;
							break;
					}
				}
			}
			else
			{	
 

				$color;//color tiempo para cita de cargue
				$color2;//color tiempo para seguimiento
				if($mDespac[$i]["tiempo"] < -30 ){
					$color = $mColor[0];
				}
				elseif($mDespac[$i]["tiempo"] < 0 && $mDespac[$i]["tiempo"] >= -30){
					$color = $mColor[4];
				}
				elseif ($mDespac[$i]["tiempo"] < 31 && $mDespac[$i]["tiempo"] >= 0) {
					$color = $mColor[5];
				}
				elseif ($mDespac[$i]["tiempo"] < 61 && $mDespac[$i]["tiempo"] > 30) {
					$color = $mColor[3];
				}
				elseif ($mDespac[$i]["tiempo"] < 91 && $mDespac[$i]["tiempo"] > 60) {
					$color = $mColor[3];
				}
				elseif ($mDespac[$i]["tiempo"] > 90) {
					$color = $mColor[3];
				}
				#sequimineto
				if($mDespac[$i]["tiempS"] < -30 ){
					$color2 = $mColor[0];
				}
				elseif($mDespac[$i]["tiempS"] < 0 && $mDespac[$i]["tiempS"] >= -30){
					$color2 = $mColor[4];
				}
				elseif ($mDespac[$i]["tiempS"] < 31 && $mDespac[$i]["tiempS"] >= 0) {
					$color2 = $mColor[5];
				}
				elseif ($mDespac[$i]["tiempS"] < 61 && $mDespac[$i]["tiempS"] > 30) {
					$color2 = $mColor[3];
				}
				elseif ($mDespac[$i]["tiempS"] < 91 && $mDespac[$i]["tiempS"] > 60) {
					$color2 = $mColor[3];
				}
				elseif ($mDespac[$i]["tiempS"] > 90) {
					$color2 = $mColor[3];
				}

				if($mDespac[$i]["tiempSGl"] < -30 ){
					$color2 = $mColor[0];
				}
				elseif($mDespac[$i]["tiempSGl"] < 0 && $mDespac[$i]["tiempSGl"] >= -30){
					$color2 = $mColor[4];
				}
				elseif ($mDespac[$i]["tiempSGl"] < 31 && $mDespac[$i]["tiempSGl"] >= 0) {
					$color2 = $mColor[5];
				}
				elseif ($mDespac[$i]["tiempSGl"] < 61 && $mDespac[$i]["tiempSGl"] > 30) {
					$color2 = $mColor[3];
				}
				elseif ($mDespac[$i]["tiempSGl"] < 91 && $mDespac[$i]["tiempSGl"] > 60) {
					$color2 = $mColor[3];
				}
				elseif ($mDespac[$i]["tiempSGl"] > 90) {
					$color2 = $mColor[3];
				}
				#Valida si el despacho esta a cargo de la empresa
				if(($mFiltro == 'ind_acargo' || $mFiltro == 'sinF') && $mDespac[$i]["ind_defini"] == 'SI' && $mDespac[$i]["ind_anulad"] != "A" )
				{
					$mResult["con_acargo"][$con_acargo] = $mDespac[$i];
					$mResult["con_acargo"][$con_acargo]["color"] = $color;
					$mResult["con_acargo"][$con_acargo]["color2"] = $color2;
					$con_acargo++;
					continue;
				}
				/*if(($mFiltro == "con_paradi" || $mFiltro == 'sinF') && $mDespac[$i]['fec_citcar'] <= $fec_sisact && $mDespac[$i]["ind_anulad"] != "A" )
				{
					$mResult["con_paradi"][$con_paradi] = $mDespac[$i];
					$mResult["con_paradi"][$con_paradi]["color"] = $color;
					$mResult["con_paradi"][$con_paradi]["color2"] = $color2;
					$con_paradi++;
				}*/
				if(($mFiltro == "con_paraco" || $mFiltro == 'sinF') && strtotime(date( "Y-m-d H:i:s", strtotime($mDespac[$i]['fec_citcar']." ".$mDespac[$i]['hor_citcar'] ) )) >=  strtotime(date( "Y-m-d H:i:s", strtotime( $fec_sisHoraIni ) )) && strtotime(date( "Y-m-d H:i:s", strtotime($mDespac[$i]['fec_citcar']." ".$mDespac[$i]['hor_citcar'] ) )) <=  strtotime(date( "Y-m-d H:i:s", strtotime( $fec_sisHoraFin ) )) && $mDespac[$i]["ind_anulad"] != "A" )
				{
					$mResult["con_paraco"][$con_paraco] = $mDespac[$i];
					$mResult["con_paraco"][$con_paraco]["color"] = $color;
					$mResult["con_paraco"][$con_paraco]["color2"] = $color2;
					$con_paraco++;
				}
				if(($mFiltro == "con_anulad" || $mFiltro == 'sinF') && $mDespac[$i]["ind_anulad"] == "A" )
				{
					$mResult["con_anulad"][$con_anulad] = $mDespac[$i];
					$mResult["con_anulad"][$con_anulad]["color"] = $color;
					$mResult["con_anulad"][$con_anulad]["color2"] = $color2;
					$con_anulad++;
				}
				if(($mFiltro == "con_planta" || $mFiltro == 'sinF') && $mDespac[$i]["fec_plalle"]!="" && $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00" && $mDespac[$i]["ind_anulad"] != "A" )
				{
					$mResult["con_planta"][$con_planta] = $mDespac[$i];
					$mResult["con_planta"][$con_planta]["color"] = $color;
					$mResult["con_planta"][$con_planta]["color2"] = $color2;
					$con_planta++;
				}

				 
				/*
					 
					switch ($mDespac[$i]['cod_estado']) { 
							case '1':
								if( ($mFiltro == "con_porter" || $mFiltro == 'sinF') && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A" )
								{
									$mResult["con_porter"] = self::setContaEstado($mFiltro ,$mDespac[$i], $mResult,$color, $color2,$con_porter++ );
								}
							break;
							case '2':
								if( ($mFiltro == "con_sinseg" || $mFiltro == 'sinF') && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A" )
								{
									$mResult["con_sinseg"] = self::setContaEstado($mFiltro,$mDespac[$i], $mResult,$color, $color2,$con_sinseg++ );
								}
							break;
							case '3':
								if( ($mFiltro == "con_tranpl" || $mFiltro == 'sinF' )&& ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A" )
								{
									$mResult["con_tranpl"] = self::setContaEstado($mFiltro,$mDespac[$i], $mResult,$color, $color2,$con_tranpl++ );
									
								}
							break;
							case '4':
								if( ($mFiltro == "con_cnnlap" || $mFiltro == 'sinF' )&& ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A" )
								{
									$mResult["con_cnnlap"] = self::setContaEstado($mFiltro,$mDespac[$i], $mResult,$color, $color2,$con_cnnlap++ );
								}
							break;
							case '5':
								if( ($mFiltro == "con_cnlapx" || $mFiltro == 'sinF' )&& ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A" )
								{
									$mResult["con_cnlapx"] = self::setContaEstado($mFiltro,$mDespac[$i], $mResult,$color, $color2,$con_cnlapx++ );
								}
							break;					
							default:
								$mResult["con_paradi"] = self::setContaEstado("con_paradi" ,$mDespac[$i], $mResult,$color, $color2,$con_paradi++ );
							break;
					}
				 */
				
				if(($mFiltro == "con_porter" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == "1" && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A")
				{
					$mResult["con_porter"][$con_porter] = $mDespac[$i];
					$mResult["con_porter"][$con_porter]["color"] = $color;
					$mResult["con_porter"][$con_porter]["color2"] = $color2;
					$con_porter++;
				}
				if(($mFiltro == "con_sinseg" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == "2" && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_sinseg"][$con_sinseg] = $mDespac[$i];
					$mResult["con_sinseg"][$con_sinseg]["color"] = $color;
					$mResult["con_sinseg"][$con_sinseg]["color2"] = $color2;
					$con_sinseg++;
				}
				if(($mFiltro == "con_tranpl" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == "3" && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_tranpl"][$con_tranpl] = $mDespac[$i];
					$mResult["con_tranpl"][$con_tranpl]["color"] = $color;
					$mResult["con_tranpl"][$con_tranpl]["color2"] = $color2;
					$con_tranpl++;
				}
				if(($mFiltro == "con_cnnlap" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == "4" && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_cnnlap"][$con_cnnlap] = $mDespac[$i];
					$mResult["con_cnnlap"][$con_cnnlap]["color"] = $color;
					$mResult["con_cnnlap"][$con_cnnlap]["color2"] = $color2;
					$con_cnnlap++;
				}
				if(($mFiltro == "con_cnlapx" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == "5" && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_cnlapx"][$con_cnlapx] = $mDespac[$i];
					$mResult["con_cnlapx"][$con_cnlapx]["color"] = $color;
					$mResult["con_cnlapx"][$con_cnlapx]["color2"] = $color2;
					$con_cnlapx++;
				}

				if(($mFiltro == "con_paradi" || $mFiltro == 'sinF') && $mDespac[$i]['cod_estado'] == NULL && ($mDespac[$i]["fec_plalle"]!="" || $mDespac[$i]["fec_plalle"]!="0000-00-00 00:00:00") && $mDespac[$i]["ind_anulad"] != "A") 
				{
					$mResult["con_paradi"][$con_paradi] = $mDespac[$i];
					$mResult["con_paradi"][$con_paradi]["color"] = $color;
					$mResult["con_paradi"][$con_paradi]["color2"] = $color2;
					$con_paradi++;
				}
				
			
			}
			
		}
 
		return $mResult;
	}

    public function getDespacCargue( $mTransp, $mTipReturn = NULL, $mSinFiltro = false )
	{
		$mDespacPrcCargue = $this->getDespacPrcCargue( $mTransp, 'list', true );

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
						AND yy.cod_transp = '".$mTransp[cod_transp]."' ";

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
        $queryReporte = new Consulta($queryReporte, $this -> conexion);
        $reporte = $queryReporte -> ret_matrix('a');
		$resp = false;

		//Assing "Etapa" value to total query
		if(count($reporte) > 0){
			return true;
		}
		return $resp;
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

    public function setHorSeguim($mTransp)
	{
		//Consulta que retorna los horarios de seguimiento a la empresa
        $mSql = " SELECT 	a.com_diasxx, 
        					a.hor_ingres,
        					a.hor_salida,
							a.tip_servic
        			FROM 	".BASE_DATOS.".tab_config_horlab a 
                   WHERE 	a.cod_tercer = '{$mTransp}'";                               
		$consulta = new Consulta( $mSql, $this -> conexion );
		$mResult = $consulta -> ret_matrix();

		//Variables necesarias
		$dayTxNow = date('D', strtotime(time()));
		$yeaNow = date("Y"); 
		$monNow = date("m");
		$dayNow = date("d");
		$arrayDiasNec = [];
		$arrayDiasFes = [];
		$arrayValida = [];
		$datoMostrar = [];
		
		//Si retornn horarios, recorre el resultado
		if (count($mResult) > 0){
			foreach ($mResult as $key => $value) {
				//Genera division de los dias
				$day =  explode("|",$value["com_diasxx"]);
				//Recorre los dias
				foreach ($day as $day ) {
					//Valida los festivos en Colombia
					$festivo = new Festivos($yeaNow);
					//Valida si es festivo el dia actual y si el dia que retorna es F
					if($day == "F" && $festivo->esFestivo($dayNow,$monNow) == true){
						$arrayDiasFes[] = $value;
					//Valida si hay horarios con el dia actual
					}else if($this->setDiasSem($day) == $dayTxNow){
						$arrayDiasNec[] = $value;
					}
				}
			}

			//Da prioridad al dia como festivo
			if(count($arrayDiasFes) > 0){
				$arrayValida = $arrayDiasFes;
			}else{
				$arrayValida = $arrayDiasNec;
			}

			//Recorre el nuevo arreglo de los dias que aplican
			foreach ($arrayValida as $key => $value) {
				//Si retornn horarios, valida que solo sea un registro
				if (count($arrayValida) == 1){
					$datoMostrar['horaMostrar'] = $value['hor_ingres']."-".$value['hor_salida'];
					$datoMostrar['tipServic'] = $this->getServicHorLab($value['tip_servic']);
				}else{
					//Valida si la llave es = a cero para identificar los horarios nocturnos
					if($key == 0){
						$datoMostrar['horaMostrar'] = $value['hor_ingres'];
						$datoMostrar['tipServic'] = $this->getServicHorLab($value['tip_servic']);
					}else{
						$datoMostrar['horaMostrar'] .= "-".$value['hor_salida'];
					}
				}
			}
		}
		return $datoMostrar;
	}

    public function setDiasSem($dia) {
		$diasEsp= array ("L","M","X","J","V","S","D");
		$diasEng= array ("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
		$texto = str_replace($diasEsp, $diasEng ,$dia);
		return $texto;
	}

    public function getServicHorLab($tip_servic){
		switch ($tip_servic){
			case '1':
			return '24/7';
			break;
			case '2':
				return '12/7';
				break;
			case '3':
				return 'Fin de semana';
				break;
			default:
				return 'NA - Servicio';
				break;
		}
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
					#Arma Matriz resultante se�n fase
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

    private function getDespacTransi1( $mTransp )
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
			$mResult[$j][nov_especi] = $mData[nov_especi];
			$mResult[$j][ind_alarma] = $mData[ind_alarma];
			$mResult[$j][nom_ultnov] = $mData[nom_ultnov];
			$mResult[$j][nom_sitiox] = $mData[nom_sitiox];
			$mResult[$j][fec_planea] = $mData[fec_planea];
			$mResult[$j][fec_planGl] = $mData[fec_planGl];
			$mResult[$j][ind_finrut] = $mData[sig_pcontr][ind_finrut]; #Aplica para empresas que solo tienen parametrizado seguimiento Transito
			$mResult[$j][coliti] = $this->validaAlarmaItiner($mDespac[$i]);
			$j++;
			
		}

		return $mResult;
	}

    public function getDespacTransi2( $mTransp )
	{
		$mDespacPrcCargue = $this->getDespacPrcCargue( $mTransp, 'list', true );
		$mDespacCarDes = $this->getDespacDescar( $mTransp, 'list2', true ); #Despachos en Etapas Cargue y Descargue
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
						AND yy.cod_transp = '".$mTransp[cod_transp]."' ";
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

    public function getDespacDescar( $mTransp, $mTipReturn = NULL, $mSinFiltro = false )
	{
		$mDespacPrcCargue = $this->getDespacPrcCargue( $mTransp, 'list', true );
		$mDespacCargue = $this->getDespacCargue( $mTransp, 'list', true );
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
						AND yy.cod_transp = '".$mTransp[cod_transp]."'";
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
new AjaxDashBoard_Seguim($_SESSION['conexion']);