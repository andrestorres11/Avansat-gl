<?php
/*ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);*/
    class AjaxAsisteAsiscar
    {
        static private $conexion = null;
        static private $cod_aplica = null;
        static private $usuario = null;
        static private $dates = array();

        function __construct($co = null, $us = null, $ca = null)
        {
            @include( "../lib/ajax.inc" );
            include_once('../lib/general/constantes.inc');
            @include_once '../../' . BASE_DATOS . '/constantes.inc';

            //Assign values 
            self::$conexion = $AjaxConnection;
            self::$usuario = $us;
            self::$cod_aplica = $ca;

            $Where="where a.fec_creaci BETWEEN '".$_POST['datInit']."' and '".$_POST['datEnd']."'";
            if($_POST['optionTransp']!=''){
                $Where=$Where." AND cod_transp IN (".$_POST['optionTransp'].")";
            }
            if($_POST['optionProv']!=''){
                
                $Where=$Where." AND cod_provee IN (".$_POST['optionProv'].")"; 
            }
            if($_POST['optionRegio']!=''){
                
                $sql = "SELECT cod_tercer  FROM ".BASE_DATOS.".tab_tercer_tercer 
                WHERE cod_region IN (".$_POST['optionRegio'].")";
                $consulta = new Consulta($sql, self::$conexion);
                $respuesta = $consulta->ret_matriz("a");
                $cod_tercer='';
                foreach($respuesta_ind as $dato){
                    $cod_tercer=$cod_tercer.$dato['cod_tercer'].',';
                }
                $cod_tercer=substr($cod_tercer, 0, -1); // eliminar ultimo caracter
                if($cod_tercer!='')
                {
                    $Where=$Where." AND cod_transp IN (".$cod_tercer.")";
                }
            }
            if($_POST['optionTipSer']!='')
            {
                $Where=$Where." AND tip_solici IN (".$_POST['optionTipSer'].")";
            }

            $query="SELECT a.id, b.nom_asiste, concat(COALESCE(c.nom_tercer, ''), ' ', COALESCE(c.nom_apell1, ''), ' ', COALESCE(c.nom_apell2, '')) as name, 
            d.nom_region,a.nom_solici,a.cor_solici,a.cel_solici,a.ase_solici,a.num_poliza,
            concat(COALESCE(a.nom_transp, ''),' ', COALESCE(a.ap1_transp, ''),' ',COALESCE(a.ap2_transp, '')) as nameCond, 
	        a.ce1_transp, 
	        a.num_placax, 
            if(a.tip_solici = 3,(SELECT nom_ciudad FROM satt_faro.tab_genera_ciudad as e where e.cod_ciudad = a.ciu_origins),(SELECT nom_ciudad FROM satt_faro.tab_genera_ciudad as f where f.cod_ciudad = a.ciu_origen)) as ciudorig, 
	        if(a.tip_solici = 3, (SELECT nom_ciudad FROM satt_faro.tab_genera_ciudad as g where g.cod_ciudad = a.ciu_desin), (SELECT nom_ciudad FROM satt_faro.tab_genera_ciudad as h where h.cod_ciudad = a.ciu_destin)) as ciuddest,
            (SELECT SUM(val_servic) FROM satt_faro.tab_servic_solasi WHERE cod_solasi = a.id GROUP by cod_solasi) as valcli, 
	        a.val_cospro, 
	        if(j.cod_docume is null,(SELECT concat(COALESCE(i.nom_tercer, ''), ' ',COALESCE(i.nom_apell1, ''), ' ', COALESCE(i.nom_apell2, '')) FROM satt_faro.tab_tercer_activi as k INNER JOIN satt_faro.tab_tercer_tercer as i ON k.cod_tercer = i.cod_tercer WHERE i.cod_tercer = a.cod_provee limit 1),(concat(COALESCE(j.nom_contra, ''), ' ', COALESCE(j.pri_apelli, ''),' ',COALESCE(j.seg_apelli, '')))) as nomprovee,
            a.cod_provee, 
            a.fec_creaci, 
            a.fec_modifi 
            FROM 
	        satt_faro.tab_asiste_carret as a 
	        INNER JOIN satt_faro.tab_formul_asiste AS b ON a.tip_solici = b.id 
	        INNER JOIN satt_faro.tab_tercer_tercer AS c on a.cod_transp = c.cod_tercer 
	        lEFT JOIN satt_faro.tab_genera_region AS d on d.cod_region = c.cod_region 
	        lEFT JOIN satt_faro.tab_hojvid_ctxxxx AS j on j.cod_docume = a.cod_provee " .$Where;
            $consulta_ = new Consulta($query, self::$conexion);
            $respuesta_ = $consulta_->ret_matriz("a");
            $u=0;
            $data =[];
            foreach ($respuesta_ as $key => $value) {
                $u++;
                    $rteF=(floatval($value['val_cospro'])*0.01);
                    $rteF=round($rteF,2);
                    $rteI=((floatval($value['val_cospro'])*9.66)/1000);
                    $rteI=round($rteI,2);
                    $salRest=((floatval($value['val_cospro'])-$rteF)-$rteI);
                    $salRest=round($salRest,2);
                    $util=(floatval($value['valcli'])-floatval($value['val_cospro']));
                    $util=round($util,2);
                    $rent=(($util/floatval($value['valcli']))*100);
                    $rent=round($rent,2);
                    $val_cli=number_format($value['valcli']);
                    $val_cost=number_format($value['val_cospro']);
                    $val_rteF=number_format($rteF);
                    $val_rteI=number_format($rteI);
                    $val_Rest=number_format($salRest);
                    $val_util=number_format($util);
                    $data[] = [
                        "n"=>$u,
                        "id"=>$value['id'],
                        "nom_asiste"=>$value['nom_asiste'] ? $value['nom_asiste'] :'N/a',
                        "name"=>$value['name'] ? $value['name'] :'N/a',
                        "nom_region"=>$value['nom_region'] ? $value['nom_region']:'N/a',
                        "nom_solici"=>$value['nom_solici'] ? $value['nom_solici'] :'N/a',
                        "cor_solici"=>$value['cor_solici'] ? $value['cor_solici'] :'N/a',
                        "cel_solici"=>$value['cel_solici'] ? $value['cel_solici'] :'N/a',
                        "ase_solici"=>$value['ase_solici'] ? $value['ase_solici'] :'N/a',
                        "num_poliza"=>$value['num_poliza'] ? $value['num_poliza'] :'N/a',
                        "nameCond"=>$value['nameCond'] ? $value['nameCond'] :'N/a',
                        "ce1_transp"=>$value['ce1_transp'] ? $value['ce1_transp'] :'N/a', 
	                    "num_placax"=>$value['num_placax'] ? $value['num_placax'] :'N/a',
                        "ciudorig"=>$value['ciudorig'] ? $value['ciudorig'] :'N/a',
                        "ciuddest"=>$value['ciuddest'] ? $value['ciuddest'] :'N/a',
                        "valcli"=>$val_cli ? $val_cli :'N/a',
                        "val_cospro"=>$val_cost ? $val_cost :'N/a',
                        "nomprovee"=>$value['nomprovee'] ? $value['nomprovee']:'No aplica',
                        "cod_provee"=>$value['cod_provee'] ? $value['cod_provee'] :'No aplica',
                        "sol_antic"=>'0',
                        "rte_fnt"=>$val_rteF,
                        "rte_Ica"=>$val_rteI,
                        "salrest"=>$val_Rest,
                        "utili"=>$val_util,
                        "rent"=>$rent.'%',
                        "fec_creaci"=>$value['fec_creaci'] ? $value['fec_creaci'] :'No aplica',
                        "fec_modifi"=>$value['fec_modifi'] ? $value['fec_modifi'] :'No aplica',
                    ];
                
            }
            
            $return = array("data" => $data);
            echo json_encode(self::cleanArray($return));
        }

        /*! \fn: cleanArray
           *  \brief: Limpia los datos de cualquier caracter especial para corregir codificaciÃ³n
           *  \author: Ing. Luis Manrique
           *  \date: 03-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: $arrau => Arreglo que serÃ¡ analizado por la funciÃ³n
           *  \return: array
        */
        function cleanArray($array){

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
                    $arrayReturn[$convert($key)] = self::cleanArray($value);
                }else{
                    //Clean value
                    $arrayReturn[$convert($key)] = $convert($value);
                }
            }
            //Return array
            return $arrayReturn;
        }
    }
    new AjaxAsisteAsiscar();
?>