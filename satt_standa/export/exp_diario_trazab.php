<?php

include ("../lib/general/constantes.inc");
include ("../lib/general/conexion_lib.inc");
include ("../lib/general/form_lib.inc");
include ("../lib/general/paginador_lib.inc");
include("../../" . $_REQUEST['central'] . "/constantes.inc");

class exp_diario_trazab {

    var $conexion;

    function exp_diario_trazab() {
        $this->conexion = new Conexion(HOST, USUARIO, CLAVE, BASE_DATOS);
        $this->Listar();
    }

    function Listar() {
        $this->expListadoExcel();
    }

    function expListadoExcel() {
        session_start();
        $informe = $_SESSION[html];

        $archivo = "Informe_trazabilidad_diaria_" . date('Y_m_d') . ".xls";
        header('Content-Type: application/octetstream');
        header('Expires: 0');
        header('Content-Disposition: attachment; filename="' . $archivo . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        $html = "<style type='text/css' >";
        $html .= "  
          .cellHead
          {
            text-align: center;
            padding:3p 10px;
            border-top:1px solid #FFF;  
            border-left:1px solid #FFF; 
            border-right:1px solid #DDD;
            border-bottom:1px solid #DDD;
            font-weight: bold;
          }
          
          .cellInfo
          {
            padding:3p 10px;
            border-top:1px solid #FFF;  
            border-left:1px solid #FFF; 
            border-right:1px solid #DDD;
            border-bottom:1px solid #DDD;           
          }
          .celda_titulo 
          {
            background-color: #F5F5F5;
            border-bottom: 1px solid #999999;
            color: #333333;
            font-weight: bold;
            padding: 3px 10px;
            white-space: nowrap;
            width: 25%;
          }
          
          ";
        $html .= "</style>";

        $html .= "<table cellpadding='0' cellspacing='0' width='100%' border='0' >";

        $html .= "<tr>";

        $html .= "<td class=celda_titulo colspan=15 >Se Encontraron " . sizeof($informe) . " Manifiestos.</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td class=celda_titulo rowspan=2 >#</td>";
        $html .= "<td class=celda_titulo rowspan=2 >N° Documento</td>";
        $html .= "<td class=celda_titulo rowspan=2 >N° Transportadora</td>";
        $html .= "<td class=celda_titulo colspan=2 >Fecha y Hora de Salida</td>";
        $html .= "<td class=celda_titulo colspan=2 >Fecha y Hora de Cita Cargue</td>";
        $html .= "<td class=celda_titulo rowspan=2 >Origen</td>";
        $html .= "<td class=celda_titulo rowspan=2 >Destino</td>";
        $html .= "<td class=celda_titulo colspan=3 >Estimado de Llegada</td>";
        $html .= "<td class=celda_titulo rowspan=2 >Ubicación</td>";
        $html .= "<td class=celda_titulo rowspan=2 >Placa</td>";
        $html .= "<td class=celda_titulo rowspan=2 >Conductor</td>";
        $html .= "<td class=celda_titulo rowspan=2 >Observaciones</td>";
        $html .= "</tr>";
        $html .= "<tr>";
        $html .= "<td class=cellHead >Fecha</td>";
        $html .= "<td class=cellHead >Hora</td>";
        $html .= "<td class=cellHead >Fecha</td>";
        $html .= "<td class=cellHead >Hora</td>";
        $html .= "<td class=cellHead >Fecha</td>";
        $html .= "<td class=cellHead >Duraci&oacute;n</td>";
        $html .= "<td class=cellHead >Días</td>";
        $html .= "</tr>";
        @include_once( "../lib/general/functions.inc" );
        $i = 0;
        if ($informe)
            foreach ($informe as $row) {
                $i++;

                $fec_sal = $this->toFecha($row[1]);
                $fec_lle = $this->toFecha($row[4]);
                //valido que las fecha de cita de cargue no esta vacias o con formato 0000-00-00 00:00:00
                $ValirFecha = true;
                //valido fecha 0000-00-00
                if( $row[13] == Null || $row[13] == "0000-00-00" )
                {
                  $ValirFecha = false;
                }
                //valido hora 00:00:00
                if( $row[14] == Null || $row[14] == "00:00:00" )
                {
                  $ValirFecha = false;
                }
                //llamo la funcion toFecha
                if($ValirFecha == true)
                {
                  $fec_cit  = $this -> toFecha( $row[13]." ".$row[14] );
                }
                else
                {
                  $fec_cit[0] = "";
                  $fec_cit[1] = "";
                }
                $html .= "<tr>"; // celda_info
                $html .= "<td class=cellHead nowrap>$i</td>";                                      // Consecutivo
                $html .= "<td class=celda_info nowrap>$row[0]</td>";                               // Número despacho
                $html .= "<td class=celda_info nowrap>$row[10]</td>";                              // nombre Transportadora
                $html .= "<td class=celda_info nowrap>" . $fec_sal[0] . "</td>";                       // Fecha salida
                $html .= "<td class=celda_info nowrap>" . $fec_sal[1] . "</td>";                       // Hora salida
                $html .= "<td class=celda_info nowrap>".$fec_cit[0]."</td>";                       // Fecha cita cargue
                $html .= "<td class=celda_info nowrap>".$fec_cit[1]."</td>";  
                $html .= "<td class=celda_info nowrap>$row[2]</td>";                               // Origen
                $html .= "<td class=celda_info nowrap>$row[3]</td>";                               // Destino
                $html .= "<td class=celda_info nowrap>" . $fec_lle[0] . "</td>";                       // Fecha llegada 
                $html .= "<td class=celda_info nowrap>" . $row[8] . "</td>";                           // Duración Dias desde salida a llegada

                $bg_color = "";
                if ($row[9] <= 0)
                    $bg_color = " style='background-color:#EAF1DD' ";
                elseif ($row[9] == 1)
                    $bg_color = " style='background-color:#FAC090' ";
                else
                    $bg_color = " style='background-color:#FF3300' ";

                $html .= "<td class=celda_info $bg_color nowrap>" . $row[9] . "</td>";                 // Días desde Fecha salida      
                $html .= "<td class=celda_info nowrap>" . $this->getUbicacion($row[0]) . "</td>";  //Ubicacion.
                $html .= "<td class=celda_info nowrap>$row[6]</td>";                               // Placas  
                $html .= "<td class=celda_info nowrap>$row[7]</td>";                               // Conductor
                $html .= "<td class=celda_info nowrap>".getNovedadesDespac($this->conexion, $row[0], '2')['obs_noveda']."</td>";                               // Observacion
                $html .= "</tr>";
            }

        $html .= "<tr>";
        $html .= "<td class=celda_titulo colspan=13 >Se Encontraron " . sizeof($informe) . " Manifiestos.</td>";
        $html .= "</tr>";

        $html .= "</table>";

        echo $html;
    }

    function getUbicacion($num_despac) {
        $select = "( SELECT a.fec_contro, UPPER( b.nom_sitiox )
                     FROM " . BASE_DATOS . ".tab_despac_contro a,
                          " . BASE_DATOS . ".tab_despac_sitio b
                    WHERE a.cod_sitiox = b.cod_sitiox AND
                          a.num_despac = '$num_despac' )
                  UNION
                 ( SELECT a.fec_noveda, UPPER( b.nom_contro )
                     FROM " . BASE_DATOS . ".tab_despac_noveda a,
                          " . BASE_DATOS . ".tab_genera_contro b
                    WHERE a.cod_contro = b.cod_contro AND
                          a.num_despac = '$num_despac' )
                 ORDER BY 1 DESC ";
        //echo "<pre>"; print_r($select); echo "</pre>";
        $select = new Consulta($select, $this->conexion);
        $select = $select->ret_matriz("i");

        return $select[0][1];
    }

    function toFecha($date) {
        $fecha = explode("-", $date);
        $dia = $fecha[2];
        $mes = $fecha[1];
        $ano = $fecha[0];

        $dia = explode(" ", $dia);

        $hora = explode(" ", $date);
        $letra_mes = "";

        switch ($mes) {
            case "1": $letra_mes = "ENE";
                break;
            case "2": $letra_mes = "FEB";
                break;
            case "3": $letra_mes = "MAR";
                break;
            case "4": $letra_mes = "ABR";
                break;
            case "5": $letra_mes = "MAY";
                break;
            case "6": $letra_mes = "JUN";
                break;
            case "7": $letra_mes = "JUL";
                break;
            case "8": $letra_mes = "AGO";
                break;
            case "9": $letra_mes = "SEP";
                break;
            case "10": $letra_mes = "OCT";
                break;
            case "11": $letra_mes = "NOV";
                break;
            case "12": $letra_mes = "DIC";
                break;
        }

        $salida[0] = "$dia[0]-$letra_mes";
        $salida[1] = "$hora[1]";

        return $salida;
    }

}

$proceso = new exp_diario_trazab();
?>