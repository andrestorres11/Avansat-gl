<?php

error_reporting("E_ERROR");

include ("../lib/general/constantes.inc");
include ("../lib/general/conexion_lib.inc");
include ("../lib/general/form_lib.inc");
include ("../lib/general/paginador_lib.inc");
include ("../lib/general/pdf_lib.inc");
include ("../despac/Despachos.inc");
include("../../" . $_REQUEST['url'] . "/constantes.inc");

class Proc_exp_destra {

    var $conexion;

    function Proc_exp_destra() {
        $this->conexion = new Conexion(HOST, USUARIO, CLAVE, $_REQUEST["db"]);
        $this->Listar();
    }

    function Listar() {
        $objciud = new Despachos($_REQUEST[cod_servic], $_REQUEST[opcion], $this->aplica, $this->conexion);
        $fechoract = date("d-M-Y h:i a");

        $query = "SELECT a.ind_remdes
             FROM " . BASE_DATOS . ".tab_config_parame a
            WHERE a.ind_remdes = '1'
          ";

        $consulta = new Consulta($query, $this->conexion);
        $manredes = $consulta->ret_matriz();

        $query = "SELECT a.ind_desurb
             FROM " . BASE_DATOS . ".tab_config_parame a
            WHERE a.ind_desurb = '1'
          ";

        $consulta = new Consulta($query, $this->conexion);
        $desurb = $consulta->ret_matriz();

        $query = "SELECT a.ind_restra
            FROM " . BASE_DATOS . ".tab_config_parame a
           WHERE a.ind_restra = '1'
          ";

        $consulta = new Consulta($query, $this->conexion);
        $resptran = $consulta->ret_matriz();

        $archivo = $_REQUEST[nomarchive] . "_" . date("Y_m_d");

        $query = base64_decode($_REQUEST[query_exp]);
        $consulta = new Consulta($query, $this->conexion);
        $transpor = $consulta->ret_matriz();

        $query = "SELECT a.cod_alarma,a.nom_alarma,a.cod_colorx,a.cant_tiempo
             FROM " . BASE_DATOS . ".tab_genera_alarma a
                ORDER BY 4
          ";

        $consulta = new Consulta($query, $this->conexion);
        $alarmas = $consulta->ret_matriz();

        $totaldes = $totporll = $totsires = 0;

        for ($i = 0; $i < sizeof($transpor); $i++) {
            $transpor[$i][3] = $transpor[$i][4] = $transpor[$i][5] = 0;

            $query = "SELECT a.num_despac
                FROM " . BASE_DATOS . ".tab_despac_despac a,
                     " . BASE_DATOS . ".tab_despac_seguim b,
                     " . BASE_DATOS . ".tab_despac_vehige d,
                     " . BASE_DATOS . ".tab_vehicu_vehicu i
               WHERE a.num_despac = d.num_despac AND
                   a.num_despac = b.num_despac AND
                     i.num_placax = d.num_placax AND
                     a.fec_salida Is Not Null AND
                     a.fec_salida <= NOW() AND
                     a.fec_llegad Is Null AND
                     a.ind_anulad = 'R' AND
                     a.ind_planru = 'S' AND
                     d.cod_transp = '" . $transpor[$i][0] . "'
                     GROUP BY 1
       ";

            $consulta = new Consulta($query, $this->conexion);
            $despacho = $consulta->ret_matriz();

            for ($j = 0; $j < sizeof($despacho); $j++) {
                $transpor[$i][3] ++;
                $totaldes++;

                $query = "SELECT a.cod_rutasx
               FROM " . BASE_DATOS . ".tab_despac_seguim a
              WHERE a.num_despac = " . $despacho[$j][0] . "
              GROUP BY 1
            ";

                $consulta = new Consulta($query, $this->conexion);
                $totrutas = $consulta->ret_matriz();

                if (sizeof($totrutas) < 2)
                    $camporder = "fec_planea";
                else
                    $camporder = "fec_alarma";

                $query = "SELECT a.cod_contro
                 FROM " . BASE_DATOS . ".tab_despac_seguim a,
              " . BASE_DATOS . ".tab_despac_vehige c
                WHERE a.num_despac = c.num_despac AND
              c.num_despac = " . $despacho[$j][0] . " AND
                      a." . $camporder . " = (SELECT MAX(b." . $camporder . ")
                                            FROM " . BASE_DATOS . ".tab_despac_seguim b
                                           WHERE a.num_despac = b.num_despac
                                       )
              ";

                $consulta = new Consulta($query, $this->conexion);
                $ultimopc = $consulta->ret_matriz();

                $query = "SELECT a.cod_contro,a.fec_noveda
                 FROM " . BASE_DATOS . ".tab_despac_noveda a,
              " . BASE_DATOS . ".tab_despac_vehige c
                WHERE a.num_despac = c.num_despac AND
              c.num_despac = " . $despacho[$j][0] . " AND
                      a.fec_noveda = (SELECT MAX(b.fec_noveda)
                                        FROM " . BASE_DATOS . ".tab_despac_noveda b
                                       WHERE a.num_despac = b.num_despac
                                     )
              ";

                $consulta = new Consulta($query, $this->conexion);
                $ultimnov = $consulta->ret_matriz();

                if ($ultimnov) {
                    $query = "SELECT a.ind_urbano
            FROM " . BASE_DATOS . ".tab_genera_contro a
           WHERE a.cod_contro = " . $ultimnov[0][0] . " AND
               a.ind_urbano = '1'
         ";

                    $consulta = new Consulta($query, $this->conexion);
                    $pcontrurb = $consulta->ret_matriz();

                    $query = "SELECT b.fec_alarma
                  FROM " . BASE_DATOS . ".tab_despac_despac a,
                 " . BASE_DATOS . ".tab_despac_seguim b,
                 " . BASE_DATOS . ".tab_despac_vehige c
                 WHERE a.num_despac = c.num_despac AND
                 c.num_despac = b.num_despac AND
                       a.num_despac = " . $despacho[$j][0] . " AND
                       b.fec_alarma > '" . $ultimnov[0][1] . "'
                       ORDER BY 1 ";

                    $consulta = new Consulta($query, $this->conexion);
                    $pfechala = $consulta->ret_matriz();
                } else
                    $pcontrurb = NULL;

                if ($manredes && $desurb && $pcontrurb)
                    $pcomparar = $ultimnov[0][0];
                else
                    $pcomparar = $ultimopc[0][0];

                if ($pcomparar == $ultimnov[0][0]) {
                    $transpor[$i][5] ++;
                    $totporll++;
                } else {
                    if (!$ultimnov) {
                        $query = "SELECT MIN(a.fec_alarma)
                 FROM " . BASE_DATOS . ".tab_despac_seguim a
                WHERE a.num_despac = " . $despacho[$j][0] . "
              ";

                        $consulta = new Consulta($query, $this->conexion);
                        $fecalarm = $consulta->ret_matriz();

                        $tiempo_proxnov = $fecalarm[0][0];
                    } else
                        $tiempo_proxnov = $pfechala[0][0];

                    $query = "SELECT TIME_TO_SEC( TIMEDIFF(NOW(), '" . $tiempo_proxnov . "')) / 60";

                    $tiempo = new Consulta($query, $this->conexion);
                    $tiemp_demora = $tiempo->ret_arreglo();

                    $tiemp_alarma = NULL;

                    if ($tiemp_demora[0] >= 0) {
                        for ($l = 0, $totalalarm = sizeof($alarmas); $l < $totalalarm; $l++) {
                            if ($tiemp_demora[0] < $alarmas[0][3]) {
                                $transpor[$i][6] ++;
                                $totalarm[0] ++;
                                $tiemp_alarma = 1;
                                $l = sizeof($alarmas);
                            } else if ($tiemp_demora[0] > $alarmas[$l][3] && $tiemp_demora[0] < $alarmas[$l + 1][3]) {
                                $transpor[$i][7 + $l] ++;
                                $totalarm[$l] ++;
                                $tiemp_alarma = 1;
                                $l = sizeof($alarmas);
                            }
                        }

                        if (!$tiemp_alarma) {
                            if ($resptran) {
                                $query = "SELECT a.cant_tiempo
                   FROM " . BASE_DATOS . ".tab_genera_alarma a
                      ORDER BY 1
              ";

                                $consulta = new Consulta($query, $this->conexion);
                                $color_maximo = $consulta->ret_matriz();

                                $query = "SELECT a.cod_contro
                     FROM " . BASE_DATOS . ".tab_despac_contro a
                  WHERE a.cod_noveda = " . CONS_NOVEDA_CAMALA . " AND
                        a.num_despac = " . $despacho[$j][0] . "
                ";

                                $consulta = new Consulta($query, $this->conexion);
                                $existcambala = $consulta->ret_matriz();

                                if ($tiemp_demora[0] > $color_maximo[sizeof($color_maximo) - 1][0] && !$existcambala) {
                                    $transpor[$i][6 + (sizeof($alarmas) - 2)] ++;
                                    $totalarm[sizeof($alarmas) - 2] ++;
                                    $tiemp_alarma = 1;
                                    $l = sizeof($alarmas);
                                } else {
                                    $transpor[$i][6 + (sizeof($alarmas) - 1)] ++;
                                    $totalarm[sizeof($alarmas) - 1] ++;
                                    $tiemp_alarma = 1;
                                    $l = sizeof($alarmas);
                                }
                            } else {
                                $transpor[$i][6 + (sizeof($alarmas) - 1)] ++;
                                $totalarm[sizeof($alarmas) - 1] ++;
                            }
                        }
                    } else {
                        $transpor[$i][4] ++;
                        $totsires++;
                    }
                }
            }
        }

        $this->expListadoExcel($archivo, $fechoract, $totsires, $totporll, $totaldes, $totsires, $alarmas, $totalarm, $transpor, $objciud);
    }

    function expListadoExcel($archivo, $fechoract, $totsires, $totporll, $totaldes, $totsires, $alarmas, $totalarm, $transpor, $objciud) {
        $archivo .= ".xls";

        header('Content-Type: application/octetstream');
        header('Expires: 0');
        header('Content-Disposition: attachment; filename="' . $archivo . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        $formulario = new Formulario("index.php", "post", "Despachos en Transito", "form_despac");
        $formulario->linea("Fecha y Hora Reporte :: " . $fechoract, 1, "t2");

        $formulario->nueva_tabla();

        if (!$totsires)
            $totsires = "-";
        if (!$totporll)
            $totporll = "-";

        $formulario->linea("", 0, "t");
        $formulario->linea("", 0, "t");
        $formulario->linea("TOTALES", 0, "t", 0, 0, "right");
        $formulario->linea($totaldes, 0, "t", 0, 0, "center");
        $formulario->linea($totsires, 0, "t", 0, 0, "center");

        for ($i = 0; $i < sizeof($alarmas); $i++) {
            if (!$totalarm[$i])
                $totalarm[$i] = "-";
            $formulario->linea($totalarm[$i], 0, "t", 0, 0, "center");
        }

        $formulario->linea($totporll, 1, "t", 0, 0, "center");

        $formulario->linea("No.", 0, "t");
        $formulario->linea("Transportadora", 0, "t");
        $formulario->linea("Ciudad", 0, "t");
        $formulario->linea("No. Despachos", 0, "t");
        $formulario->linea("Sin Retraso", 0, "t");

        for ($i = 0; $i < sizeof($alarmas); $i++)
            echo "<td bgcolor=\"#" . $alarmas[$i][2] . "\">" . $alarmas[$i][1] . " - " . $alarmas[$i][3] . " Min</td>";

        $formulario->linea("Por Llegada", 1, "t");

        for ($i = 0; $i < sizeof($transpor); $i++) {
            $ciudad_a = $objciud->getSeleccCiudad($transpor[$i][2]);

            if (!$transpor[$i][4])
                $transpor[$i][4] = "-";
            if (!$transpor[$i][5])
                $transpor[$i][5] = "-";

            $formulario->linea(($i + 1), 0, "i");
            $formulario->linea($transpor[$i][1], 0, "i");
            $formulario->linea($ciudad_a[0][1], 0, "i");
            $formulario->linea($transpor[$i][3], 0, "i", 0, 0, "center");
            $formulario->linea($transpor[$i][4], 0, "i", 0, 0, "center");

            for ($j = 0; $j < sizeof($alarmas); $j++) {
                if (!$transpor[$i][6 + $j])
                    $transpor[$i][6 + $j] = "-";

                $formulario->linea($transpor[$i][6 + $j], 0, "i", 0, 0, "center");
            }

            $formulario->linea($transpor[$i][5], 1, "i", 0, 0, "center");
        }

        $formulario->cerrar();
    }

}

class PDF extends FPDF {

    function Header() {
        $this->SetFont('Arial', 'B', 8);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

}

$proceso = new Proc_exp_destra();
?>