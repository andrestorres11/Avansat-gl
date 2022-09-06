<?php

include ("../lib/general/constantes.inc");
include ("../lib/general/conexion_lib.inc");
include ("../lib/general/form_lib.inc");
include ("../lib/general/paginador_lib.inc");
include ("../lib/general/pdf_lib.inc");
include ("../lib/general/tabla_lib.inc");
include ("../despac/Despachos.inc");
include("../../" . $_REQUEST['url'] . "/constantes.inc");

class Proc_exp_tradoc {

    var $conexion;

    function Proc_exp_tradoc() {
        $this->conexion = new Conexion(HOST, USUARIO, CLAVE, $_REQUEST["db"]);
        $this->Listar();
    }

    function Listar() {
        $archivo = "Trazabilidad_de_Documentos_" . date("Y_m_d");

        $query = "SELECT a.num_despac,a.cod_manifi,k.nom_remdes,j.val_pesoxx,
                    j.num_docume,j.num_pedido,a.cod_ciuori,j.cod_ciudad,
                    f.abr_tercer,d.num_placax,e.abr_tercer,
                    DATE_FORMAT(a.fec_despac,'%H:%i %d-%m-%Y'),
                    DATE_FORMAT(a.fec_salida,'%H:%i %d-%m-%Y'),
                    DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y'),
                    a.ind_anulad,j.cod_remdes,
                    if(m.ind_tonela = '" . COD_ESTADO_ACTIVO . "',m.val_costos / m.can_tonela,m.val_costos),
                if(m.ind_tonela = '" . COD_ESTADO_ACTIVO . "',(m.val_costos / m.can_tonela) * j.val_pesoxx,m.val_costos),
                if(j.cod_tabfle IS NOT NULL,l.nom_trayec,'-'),
                if(m.ind_tonela != '" . COD_ESTADO_ACTIVO . "',' (Viaje)',''),
                a.cod_ciudes,j.val_bultox
               FROM " . BASE_DATOS . ".tab_despac_vehige d,
                    " . BASE_DATOS . ".tab_tercer_tercer e,
                    " . BASE_DATOS . ".tab_tercer_tercer f,
                    " . BASE_DATOS . ".tab_vehicu_vehicu i,
                    " . BASE_DATOS . ".tab_despac_despac a LEFT JOIN
                    " . BASE_DATOS . ".tab_despac_remdes j ON
                    a.num_despac = j.num_despac LEFT JOIN
                    " . BASE_DATOS . ".tab_genera_remdes k ON
                    j.cod_remdes = k.cod_remdes LEFT JOIN
                    " . BASE_DATOS . ".tab_tablax_fletes m ON
                    j.cod_tabfle = m.cod_consec LEFT JOIN
                    " . BASE_DATOS . ".tab_genera_trayec l ON
                    m.cod_trayec = l.cod_trayec
              WHERE a.num_despac = d.num_despac AND
                    d.cod_conduc = e.cod_tercer AND
                    d.cod_transp = f.cod_tercer AND
                    k.cod_remdes = j.cod_remdes AND
                    i.num_placax = d.num_placax
      ";

        if ($_REQUEST[eciuori])
            $query .= " AND a.cod_ciuori = " . $_REQUEST[eciuori] . "";
        if ($_REQUEST[eciudes])
            $query .= " AND a.cod_ciudes = " . $_REQUEST[eciudes] . "";
        if ($_REQUEST[econduc])
            $query .= " AND d.cod_conduc = '" . $_REQUEST[econduc] . "'";
        if ($_REQUEST[etransp])
            $query .= " AND d.cod_transp = '" . $_REQUEST[etransp] . "'";
        if ($_REQUEST[edespac])
            $query .= " AND a.num_despac = " . $_REQUEST[edespac] . "";
        if ($_REQUEST[edocume])
            $query .= " AND a.cod_manifi = '" . $_REQUEST[edocume] . "'";
        if ($_REQUEST[eremisi])
            $query .= " AND j.num_docume = '" . $_REQUEST[eremisi] . "'";
        if ($_REQUEST[epedido])
            $query .= " AND j.num_pedido = '" . $_REQUEST[epedido] . "'";
        if ($_REQUEST[eplacax])
            $query .= " AND d.num_placax = '" . $_REQUEST[eplacax] . "'";
        if ($_REQUEST[edestinat])
            $query .= " AND j.cod_remdes = '" . $_REQUEST[edestinat] . "'";

        if ($_REQUEST[transp])
            $query .= " AND d.cod_transp = '" . $_REQUEST[transp] . "'";
        if ($_REQUEST[despac])
            $query .= " AND a.num_despac = " . $_REQUEST[despac] . "";
        if ($_REQUEST[docume])
            $query .= " AND a.cod_manifi = '" . $_REQUEST[docume] . "'";
        if ($_REQUEST[remisi])
            $query .= " AND j.num_docume = '" . $_REQUEST[remisi] . "'";
        if ($_REQUEST[pedido])
            $query .= " AND j.num_pedido = '" . $_REQUEST[pedido] . "'";
        if ($_REQUEST[placax])
            $query .= " AND d.num_placax = '" . $_REQUEST[placax] . "'";
        if ($_REQUEST[conduc])
            $query .= " AND d.cod_conduc = '" . $_REQUEST[conduc] . "'";
        if ($_REQUEST[ciuori])
            $query .= " AND a.cod_ciuori = " . $_REQUEST[ciuori] . "";
        if ($_REQUEST[ciudes])
            $query .= " AND a.cod_ciudes = " . $_REQUEST[ciudes] . "";

        $query .= " AND a.fec_despac BETWEEN '" . $_REQUEST[fi] . "' AND '" . $_REQUEST[ff] . "'";
        $query .= " GROUP BY j.num_despac,j.cod_remdes ORDER BY 1,3,4";
        //echo $query;
        $consulta = new Consulta($query, $this->conexion);
        $despac = $consulta->ret_matriz();
        $this->expListadoExcel($archivo, $despac);
    }

    function expListadoExcel($archivo, $despac) {
        $archivo .= ".xls";

        header('Content-Type: application/octetstream');
        header('Expires: 0');
        header('Content-Disposition: attachment; filename="' . $archivo . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        $formulario = new Formulario("index.php", "post", "Trazabilidad de Documentos", "form_item");
        $formulario->linea("Se Encontro un Total de " . sizeof($despac) . " Documento(s).", 1, "t2");

        $formulario->nueva_tabla();
        $formulario->linea("Despacho", 0, "t");
        $formulario->linea("Documento/Despacho", 0, "t");
        $formulario->linea("Desposito/Destinatario", 0, "t");
        $formulario->linea("Remisi&oacute;n", 0, "t");
        $formulario->linea("Pedido", 0, "t");
        $formulario->linea("Origen", 0, "t");
        $formulario->linea("Destino", 0, "t");
        $formulario->linea("Peso (Tn)", 0, "t");
        $formulario->linea("Bultos", 0, "t");
        $formulario->linea("Valor (Unit)", 0, "t");
        $formulario->linea("Valor Flete", 0, "t");
        $formulario->linea("Trayecto", 0, "t");
        $formulario->linea("Transportadora", 0, "t");
        $formulario->linea("Placa", 0, "t");
        $formulario->linea("Conductor", 0, "t");
        $formulario->linea("Fecha/Despacho", 0, "t");
        $formulario->linea("Fecha/Salida", 0, "t");
        $formulario->linea("Fecha/Llegada", 0, "t");

        //Nuevas Filas.
        $formulario->linea("Hora/Fecha Control Lugar de Entrega", 0, "t");
        $formulario->linea("Hora/Fecha Novedad Lugar de Entrega", 0, "t");
        $formulario->linea("Hora/Fecha Control Destinatario", 0, "t");
        $formulario->linea("Hora/Fecha Novedad Destinatario", 0, "t");
        $formulario->linea("Fecha Cumplido Fisico", 0, "t");
        $formulario->linea("Novedad Cumplido", 0, "t");

        $formulario->linea("Estado", 1, "t");

        for ($i = 0; $i < sizeof($despac); $i++) {
            $query = "SELECT nom_ciudad
             FROM " . BASE_DATOS . ".tab_genera_ciudad
             WHERE cod_ciudad = " . $despac[$i][6] . "
            ";

            $consulta = new Consulta($query, $this->conexion);
            $ciudad_o = $consulta->ret_matriz();

            $query = "SELECT nom_ciudad
             FROM " . BASE_DATOS . ".tab_genera_ciudad
             WHERE cod_ciudad = " . $despac[$i][20] . "
            ";

            $consulta = new Consulta($query, $this->conexion);
            $ciudad_d = $consulta->ret_matriz();

            $fec_cumpli = " SELECT a.fec_cumpli
            FROM " . BASE_DATOS . ".tab_despac_despac a
          WHERE num_despac = '" . $despac[$i][0] . "'
          ORDER BY 1 DESC";

            $fec_cumpli = new Consulta($fec_cumpli, $this->conexion);
            $fec_cumpli = $fec_cumpli->ret_matriz();
            $fec_cumpli = $fec_cumpli[0][fec_cumpli];

            $fec_noveda = "SELECT e.fec_noveda,e.fec_creaci
          FROM
          " . BASE_DATOS . ".tab_despac_despac a,
          " . BASE_DATOS . ".tab_despac_remdes b,
          " . BASE_DATOS . ".tab_destin_contro c,
          " . BASE_DATOS . ".tab_despac_seguim d left join
          " . BASE_DATOS . ".tab_despac_noveda e on
          d.num_despac = e.num_despac and
          d.cod_rutasx = e.cod_rutasx and
          d.cod_contro = e.cod_contro
          WHERE
          a.num_despac = b.num_despac and
          b.cod_remdes = c.cod_remdes and
          a.num_despac = d.num_despac and
          c.cod_contro = d.cod_contro and
          a.num_despac = '" . $despac[$i][0] . "'
          GROUP BY a.num_despac "; // c.cod_contro = '".CONS_CODIGO_PCLLEG."' GROUP BY 1,2

            $fec_noveda = new Consulta($fec_noveda, $this->conexion);
            $fec_noveda = $fec_noveda->ret_matriz(1);
            $fec_noveda = $fec_noveda[0];

            $nov_entreg = "SELECT c.fec_noveda,c.fec_creaci
          FROM
          " . BASE_DATOS . ".tab_despac_seguim b,
          " . BASE_DATOS . ".tab_despac_noveda c
          WHERE
          b.num_despac = c.num_despac and
          c.cod_contro = '" . CONS_CODIGO_PCLLEG . "' and
          b.num_despac = '" . $despac[$i][0] . "'
          GROUP BY 1 ";

            $nov_entreg = new Consulta($nov_entreg, $this->conexion);
            $nov_entreg = $nov_entreg->ret_matriz(1);
            $nov_entreg = $nov_entreg[0];

            $mQuery = "SELECT a.nom_novcum
         FROM " . BASE_DATOS . ".tab_genera_novcum a,
            " . BASE_DATOS . ".tab_noveda_cumpli b
        WHERE a.cod_novcum = b.cod_novcum AND
            b.num_despac = '" . $despac[$i][0] . "'
        ";

            $mConsulta = new Consulta($mQuery, $this->conexion);
            $nov_cumpli = $mConsulta->ret_matriz();
            if ($nov_cumpli[0][0])
                $nov_cumpli = $nov_cumpli[0][0];
            else
                $nov_cumpli = "-";

            if ($despac[$i][14] == "R") {
                $estado = "Activo";
                $estilo = "i";
            } else if ($despac[$i][12] == "A") {
                $estado = "Anulado";
                $estilo = "ie";
            }

            $formulario->linea($despac[$i][0], 0, $estilo);
            $formulario->linea($despac[$i][1], 0, $estilo);
            $formulario->linea($despac[$i][2], 0, $estilo);
            $formulario->linea($despac[$i][4], 0, $estilo);
            $formulario->linea($despac[$i][5], 0, $estilo);
            $formulario->linea($ciudad_o[0][0], 0, $estilo);
            $formulario->linea($ciudad_d[0][0], 0, $estilo);
            $formulario->linea($despac[$i][3], 0, $estilo);
            $formulario->linea($despac[$i][21], 0, $estilo);
            $formulario->linea($despac[$i][16] . $despac[$i][19], 0, $estilo);
            $formulario->linea($despac[$i][17], 0, $estilo);
            $formulario->linea($despac[$i][18], 0, $estilo);
            $formulario->linea($despac[$i][8], 0, $estilo);
            $formulario->linea($despac[$i][9], 0, $estilo);
            $formulario->linea($despac[$i][10], 0, $estilo);
            $formulario->linea($despac[$i][11], 0, $estilo);
            $formulario->linea($despac[$i][12], 0, $estilo);
            $formulario->linea($despac[$i][13], 0, $estilo);
            //Nuevas columnas.

            $formulario->linea($nov_entreg[fec_noveda], 0, $estilo);
            $formulario->linea($nov_entreg[fec_creaci], 0, $estilo);
            $formulario->linea($fec_noveda[fec_noveda], 0, $estilo);
            $formulario->linea($fec_noveda[fec_creaci], 0, $estilo);
            $formulario->linea($fec_cumpli, 0, $estilo);
            $formulario->linea($nov_cumpli, 0, $estilo);
            $formulario->linea($estado, 1, $estilo);
        }
    }

}

$proceso = new Proc_exp_tradoc();
?>