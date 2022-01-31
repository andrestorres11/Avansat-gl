<?php

ini_set('display_errors', false);
error_reporting(E_ALL & ~E_NOTICE);
ini_set("memory_limit", "1024M");

class Protocolos {

    private $cConecion = NULL;

    function __construct($mConnec) {
         

        if ($mConnec != NULL) {
            $this->cConecion = $mConnec;
        } else {
            @include_once('../../' . DIR_APLICA_CENTRAL . '/lib/ajax.inc');
            $this->cConecion = $AjaxConnection;
        }
    }

    private function qIndicaEmail($noveda, $transp) {

        $mSql = "SELECT a.ind_notema
                 FROM " . BASE_DATOS . ".tab_noveda_protoc a 
                 WHERE cod_noveda = '" . $noveda . "' 	
                 AND a.cod_transp = '" . $transp . "'";


        $consulta = new Consulta($mSql, $this->cConecion);
        $mResult = $consulta->ret_matriz();

        if ($mResult[0][0] == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getIndicaEmail($noveda, $transp) {
        return $this->qIndicaEmail($noveda, $transp);
    }

    private function qGetManifi($despac) {
        $mSql = "SELECT a.num_despac, a.cod_manifi, 
                           IF( b.nom_conduc IS NOT NULL, b.nom_conduc, c.abr_tercer) AS abr_tercer, c.cod_tercer,
                           IF( a.con_telmov IS NULL OR a.con_telmov = '', c.num_telmov, a.con_telmov ) AS telmov,
                           IF( a.con_telef1 IS NULL OR a.con_telef1 = '', c.num_telef1, a.con_telef1),ind_defini,
                               e.nom_operad, f.nom_califi, a.tie_contra, a.ind_tiemod, a.obs_tiemod, b.num_placax,
                               b.cod_transp, UPPER( z.abr_tercer ) AS nom_transp,
                               x.nom_tipdes, m.tip_vehicu
                          FROM " . BASE_DATOS . ".tab_despac_despac a 
                     LEFT JOIN " . BASE_DATOS . ".tab_despac_corona m 
                            ON a.num_despac = m.num_dessat,
                               " . BASE_DATOS . ".tab_despac_vehige b,
                               " . BASE_DATOS . ".tab_genera_tipdes x,
                               " . BASE_DATOS . ".tab_tercer_tercer c,
                               " . BASE_DATOS . ".tab_tercer_tercer z,
                               " . BASE_DATOS . ".tab_tercer_conduc d 
                     LEFT JOIN " . BASE_DATOS . ".tab_genera_califi f ON f.cod_califi = d.cod_califi
                     LEFT JOIN " . BASE_DATOS . ".tab_operad_operad e ON e.cod_operad = d.cod_operad
                         WHERE a.num_despac = b.num_despac AND
                               b.cod_transp = z.cod_tercer AND
                               a.cod_tipdes = x.cod_tipdes AND
                               b.cod_conduc = c.cod_tercer AND
                               d.cod_tercer = c.cod_tercer AND
                               a.num_despac = '" . $despac . "'";
        echo $mSql;
        $consulta = new Consulta($mSql, $this->cConecion);
        $mResult = $consulta->ret_matriz();

        return $mResult;
    }
    
    public function getManifi($despac) {
        return $this -> qGetManifi($despac);
    }
}

?>