<?php

/* ! \file: list_images_despac.php
 *  \brief: Muestra las imagenes del despacho
 *  \author: 
 *  \author: 
 *  \version: 2.0
 *  \date: dia/mes/año
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */
session_start();

/* ! \class: FleConcil
 *  \brief: Muestra las imagenes del despacho
 */

class FleConcil {

    var $conexion;

    function __construct($conexion) {
        $this->conexion = $conexion;

        switch ($_REQUEST[opcion]) {
            case "1":
                $this->Mostrar();
                break;

            case "2":
                $this->updProto();
                break;

            case "3":
                $this->insert();
                break;

            case "4":
                $this->delProto();
                break;

            default:
                $this->Prueba();
                break;
        }
    }

    /* ! \fn: Prueba
     *  \brief: 
     *  \author: 
     *  \date: dia/mes/año
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return:
     */

    function Prueba() {
        global $HTTP_POST_FILES;
        session_start();
        $BASE = $_SESSION[BASE_DATOS];
        define('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
        define('ESTILO', $_SESSION['ESTILO']);
        define('BASE_DATOS', $_SESSION['BASE_DATOS']);
        include( "../lib/general/conexion_lib.inc" );
        include( "../lib/general/form_lib.inc" );
        include( "../lib/general/tabla_lib.inc" );
        include ("../lib/mensajes_lib.inc");
        $this->conexion = new Conexion($_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE);

        $query = "SELECT a.bin_fotoxx, b.nom_contro, b.cod_contro, a.fec_creaci, a.num_consec, a.bin_fotox2
                  FROM " . BASE_DATOS . ".tab_despac_images a,
                       " . BASE_DATOS . ".tab_genera_contro b
                  WHERE a.cod_contro = b.cod_contro 
                    AND a.num_despac = '" . $_REQUEST['num_despac'] . "'
                    AND bin_fotoxx !=  ''";
        $consulta = new Consulta($query, $this->conexion);
        $mCount = $consulta->ret_matriz();

        $formulario = new Formulario("index.php", "post", "Fotos Despachos", "form\" id=\"formuID");
        $formulario->nueva_tabla();
        $formulario->botoni("Cerrar", "ClosePopup()", 1);

        $formulario->linea("Puestos de control", 0, "t");

        $mHtml = '';
        $mHtml .= '<table with="100%">';

        $i = 1;
        foreach ($mCount as $row) {
            $mHtml .= '<tr><td colspan="2">&nbsp;</td></tr>';
            $mHtml .= '<tr>';
            $mHtml .= '<th colspan="2" align="center" >' . $i . ') ' . $row['nom_contro'] . '</th>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
            $mHtml .= '<td>';
            $mHtml .= '<center><b>Conductor</b></center>';
            $mHtml .= '<img src="' . $row[bin_fotoxx] . '" width="300" height="200" border="2"/>';
            $mHtml .= '</td>';
            $mHtml .= '<td>';
            $mHtml .= '<center><b>Precinto</b></center>';
            $mHtml .= '<img src="' . $row[bin_fotox2] . '" width="300" height="200" border="2"/>';
            $mHtml .= '</td>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
            $mHtml .= '<th colspan="2" align="center" >' . date($row['fec_creaci']) . '</th>';
            $mHtml .= '</tr>';
            $i++;
        }

        $mHtml .= '</tr>';
        $mHtml .= '</table>';

        echo $mHtml;

        $formulario->nueva_tabla();
        $formulario->botoni("Cerrar", "ClosePopup()", 1);

        $formulario->cerrar();
    }

    /* ! \fn: Mostrar
     *  \brief: Muestra las imagenes del despacho
     *  \author: 
     *  \date: dia/mes/año
     *  \date modified: 25/05/2015
     *  \param: 
     *  \return:
     */

    function Mostrar() {
        global $HTTP_POST_FILES;
        session_start();
        $BASE = $_SESSION[BASE_DATOS];
        define('DIR_APLICA_CENTRAL', $_SESSION['DIR_APLICA_CENTRAL']);
        define('ESTILO', $_SESSION['ESTILO']);
        define('BASE_DATOS', $_SESSION['BASE_DATOS']);
        include( "../lib/general/conexion_lib.inc" );

        $this->conexion = new Conexion($_SESSION['HOST'], $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE); //cod_transp

        $query = "SELECT a.bin_fotoxx, b.nom_contro, a.bin_fotox2
                  FROM " . BASE_DATOS . ".tab_despac_images a,
                       " . BASE_DATOS . ".tab_genera_contro b
                  WHERE a.cod_contro = b.cod_contro AND
                        a.num_despac = '" . $_REQUEST['num_despac'] . "' AND
                        a.cod_contro = '" . $_REQUEST['cod_contro'] . "' AND 
                        a.num_consec = '" . $_REQUEST['num_consec'] . "';
                  
              ";
        $consulta = new Consulta($query, $this->conexion);
        $mImages = $consulta->ret_matriz();

        echo '<img src="' . $mImages[0]['bin_fotoxx'] . '" >';
    }

}

$service = new FleConcil($_SESSION['conexion']);
?> 