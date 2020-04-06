<?php

class ajaxRegistNoveda
{
  var $conexion;
  
  public function __construct()
  { 
    include('../lib/ajax.inc');

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST['opcion'])
        {
            case "nitEmpresa":
                self::nitEmpresa();
                break;
        }

  }
  
  private function nitEmpresa()
  {
    try {

        switch ($_REQUEST['tipo']) {
          case 'paca':
            $campo = 'b.num_placax';
            break;
          case 'manifi':
            $campo = 'a.cod_manifi';
            break;
        }

        $query = "SELECT b.cod_transp, 
                         if(c.nom_tercer = '', 
                            c.abr_tercer, 
                            c.nom_tercer
                          ) AS nom_tercer
                    FROM " . BASE_DATOS . ".tab_despac_despac a
              INNER JOIN " . BASE_DATOS . ".tab_despac_vehige b ON a.num_despac = b.num_despac
              INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer c ON b.cod_transp = c.cod_tercer
                   WHERE a.fec_salida Is Not Null 
                         AND a.fec_salida <= NOW() 
                         AND a.fec_llegad Is Null 
                         AND a.ind_anulad = 'R' 
                         AND a.ind_planru = 'S' 
                         AND (a.cod_conult != '9999' 
                                OR a.cod_conult !=(SELECT f.cod_contro FROM satt_faro.tab_despac_seguim f WHERE f.num_despac = a.num_despac AND f.cod_rutasx = b.cod_rutasx ORDER BY f.fec_planea DESC LIMIT 1)
                                OR a.cod_conult IS NULL)
                         AND $campo = '" . $_REQUEST['valor'] . "'
                     ORDER BY 1";
        $consulta = new Consulta($query, $this->conexion);
        echo json_encode($consulta->ret_matrix('a')[0]);

    } catch (Exception $e) {
        return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
    }
        

  }
}

$proceso = new ajaxRegistNoveda();
 ?>