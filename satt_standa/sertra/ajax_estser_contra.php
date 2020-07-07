<?php

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

class ajax_estser_contra
{
  var $AjaxConnection;
  
  public function __construct()
  {
    include('../lib/ajax.inc');
    include_once('../lib/general/constantes.inc');

    $this -> conexion = $AjaxConnection;

    switch ($_REQUEST['option']) {
      case 'setRegistros':
        $this->setRegistros();
        break;
      default:
        $this->setRegistros();
        break;
    }

  }

  //---------------------------------------------
  /*! \fn: setRegistros
  *  \brief:Retorna los registros para el dataTable
  *  \author: Ing. Luis Manrique
  *  \date: 27/01/2020
  *  \date modified: 21/12/2015
  *  \return BOOL
  */
  private function setRegistros() {
    $mSql = " SELECT  a.cod_tercer,
                      IF(a.nom_tercer = '' OR a.nom_tercer IS NULL,
                          a.abr_tercer,
                          a.nom_tercer
                      ) AS nom_tercer,
                      CONCAT(b.fec_iniser,' ',b.hor_iniser) AS fec_iniser,
                      CONCAT(b.fec_finser,' ',b.hor_finser) AS fec_finser,
                      CONCAT('$ ',b.val_regist) AS val_regist,
                      IF(b.val_despac = '' OR b.val_despac IS NULL,
                          '$ 0',
                          CONCAT('$ ',b.val_despac)
                      ) AS val_despac,
                      b.fec_valreg,
                      c.nom_server,
                      c.nom_aplica,
                      IF(b.ind_solpol = 1,
                          'SI',
                          'NO'
                      )AS ind_solpol,
                      IF(b.ind_solpol = 1,
                          d.nom_tercer,
                          'Ninguno'
                      )AS nom_asegur,
                      b.num_poliza,
                      e.nom_tipser,
                      IF(a.cod_estado = 1,
                          'SI',
                          'NO'
                      )AS cod_estado,
                      IF(b.ind_notage = 1,
                          'SI',
                          'NO'
                      )AS ind_notage,
                      IF(b.ind_camrut = 1,
                          'SI',
                          'NO'
                      )AS ind_camrut,
                      IF(b.ind_llegad = 1,
                          'SI',
                          'NO'
                      )AS ind_llegad,
                      IF(b.ind_biomet = 1,
                          'SI',
                          'NO'
                      )AS ind_biomet,
                      f.nom_grupox,
                      b.tie_trazab,
                      b.cod_priori,
                      g.nom_operac,
                      GROUP_CONCAT(DISTINCT(i.nom_contro)) AS nom_contro
                FROM  ".BASE_DATOS.".tab_tercer_tercer a
          INNER JOIN  ".BASE_DATOS.".tab_transp_tipser b
                  ON  a.cod_tercer = b.cod_transp
                      AND a.cod_estado = 1
          INNER JOIN  ".BASE_DATOS.".tab_genera_server c
                  ON  b.cod_server = c.cod_server
                      AND c.ind_estado = 1
          INNER JOIN  ".BASE_DATOS.".tab_tercer_tercer d
                  ON  b.cod_asegur = d.cod_tercer
                      AND d.cod_estado = 1
          INNER JOIN  ".BASE_DATOS.".tab_genera_tipser e 
                  ON  b.cod_tipser = e.cod_tipser
                      AND e.ind_estado = 1
          INNER JOIN  ".BASE_DATOS.".tab_callce_grupox f
                  ON  b.cod_grupox = f.cod_grupox
          INNER JOIN  ".BASE_DATOS.".tab_callce_operac g
                  ON  b.cod_operac = g.cod_operac
                      AND g.ind_estado = 1
           LEFT JOIN  ".BASE_DATOS.".tab_homolo_ealxxx h
                  ON  a.cod_tercer = h.cod_tercer
                      AND h.ind_estado = 1
           LEFT JOIN  ".BASE_DATOS.".tab_genera_contro i
                  ON  h.cod_pcxfar = i.cod_contro
                      AND i.ind_estado
            GROUP BY  a.cod_tercer";
    $mMatriz = new Consulta($mSql, $this->conexion);
    $mMatriz = $mMatriz->ret_matrix("a");

    $data = [];

    foreach ($mMatriz as $key => $datos) {
    	foreach ($datos as $campo => $valor) {
    		$data[$key][] = $valor;	
    	}
    }

    $return = array("data" => $data);
    echo json_encode($return);
  }
}
$proceso = new ajax_estser_contra();
?>