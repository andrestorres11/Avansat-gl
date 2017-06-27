<?php

ini_set('memory_limit', '1024M');
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

class CertifLaboral {

    var $conexion,
            $cod_aplica,
            $usuario;

    function __construct($co = NULL, $us = NULL, $ca = NULL) {
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        $this->principal();
    }

    function principal() {
        if ($_GET["Ajax"] == 'on' || $_POST["Ajax"] == 'on') {
            include("../lib/ajax.inc");
            $this->conexion = $AjaxConnection;
        }
        if (!$_POST) {
            $_POST = $_GET;
        }
        switch ($_POST[opcion]) {
            case "ValidaCedula":
                $this->ValidaCedula();
                break;
            case "searchTercer":
                $this->searchTercer();
                break;
            case "getCertif":
                $this->getCertif();
                break;
            case "ShowPdf":
                $this->ShowPdf();
                break;
            default:
                $this->Buscar();
                break;
        }
    }

    function ValidaCedula() {
        $_SESSION[mDataPdfCertif] = '';
        $query = "
            SELECT num_cedula, nom_usuari 
              FROM " . BASE_DATOS . ".tab_genera_usuari a 
             WHERE a.cod_usuari = '" . $_SESSION[datos_usuario][cod_usuari] . "' ";
        $consulta = new Consulta($query, $this->conexion);
        $mResult = $consulta->ret_matriz('a');
        if ($mResult[0][0] == '') {
            if ($_POST["Ajax"] == 'on') {
                echo "no";
            }
        } else {
            return $mResult;
        }
    }

    function Buscar() {
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/exp_certif_labora.js\"></script>\n";
        echo "<link type=\"text/css\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css\" rel=\"stylesheet\"></link>\n";
        $mData = $this->ValidaCedula();
        $formulario = new Formulario("index.php", "post", "EXPEDICION DE CERTIFICADOS LABORALES", "form_insert", "", "");
        $formulario->linea("Datos", 1, "t2");
        $mObliga = "<span style= \"color: green; font-size:15px;\">*</span> ";
        $formulario->nueva_tabla();
        $formulario->texto("$mObliga Identificación: ", "text", "cod_tercer\" size=\"15\" onkeypress=\"ClearNombre(event)\" readonly=\"readonly\" maxlength=\"12\" id=\"cod_tercerID", 0, 6, 6, "", $mData[0][0]); // return  Numeric(event)
        $formulario->texto("$mObliga Nombre: ", "text", "nom_tercer\" size=\"40\" maxlength=\"40\" readonly=\"readonly\" class=\"celda_info\" id=\"nom_tercerID", 1, 10, 11, "", $mData[0][1]);
        $formulario->texto("$mObliga Dirigido A: ", "text", "nom_dirigi\" size=\"40\" maxlength=\"40\" id=\"nom_dirigiID", 0, 6, 6, "", "");
        $formulario->caja("Ingreso:", "opcion[0]\" id=\"opcion[0]ID", "1", "1", 1);
        $formulario->caja("Salario:", "opcion[1]\" id=\"opcion[1]ID", "2", "0", 0);
        $formulario->caja("Caja De Compensación:", "opcion[2]\" id=\"opcion[2]ID", "3", "0", 1);
        $formulario->caja("Retiro:", "opcion[3]\" id=\"opcion[3]ID", "4", "0", 0);
        $formulario->caja("Logos:", "opcion[4]\" id=\"opcion[4]ID", "5", "1", 1);
        $formulario->nueva_tabla();
        $formulario->botoni("Aceptar", "Validar();", 1);
        $formulario->linea("Respuesta", 1, "t2", 0, 0, "center");
        $formulario->nueva_tabla();
        echo "<td>";
        echo "<div id='resultado' align='center'>";
        echo "</div>";
        echo "</td>";
        $formulario->nueva_tabla();
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("opcion", 1, 0);
        $formulario->oculto("cod_servic", $_REQUEST["cod_servic"], 0);
        echo "<input type='hidden' name='base' id='baseID' value='" . DIR_APLICA_CENTRAL . "'>";
        echo "<input type='hidden' name='user_id' id='user_idID' value='" . $this->usuario->cod_usuari . "'>";
        $formulario->cerrar();
    }

    function searchTercer() {
        $term = $_REQUEST['term'];
        $conditions = "a.num_cedula LIKE '" . $term . "%' OR a.nom_usuari LIKE '%" . $term . "%'";
        $query = "
            SELECT UPPER( CONCAT(IF( a.num_cedula IS NULL , '00000000', a.num_cedula ) ,' - ',a.nom_usuari) ) AS nom_tercer 
              FROM " . BASE_DATOS . ".tab_genera_usuari a 
             WHERE a.ind_estado = '1' AND 
                  ($conditions) LIMIT 10 ";
        $consulta = new Consulta($query, $this->conexion);
        $mResult = $consulta->ret_matriz('a');
        $data = array();
        for ($i = 0, $len = count($mResult); $i < $len; $i++) {
            $data [] = $mResult[$i]['nom_tercer'];
        }
        echo '["' . join('", "', $data) . '"]';
    }

    function getCertif() {
        require_once("../lib/nusoap095/nusoap.php");
        $_POST[opcion0] = $_POST[opcion0] != '' ? 'si' : NULL;
        $_POST[opcion1] = $_POST[opcion1] != '' ? 'si' : NULL;
        $_POST[opcion2] = $_POST[opcion2] != '' ? 'si' : NULL;
        $_POST[opcion3] = $_POST[opcion3] != '' ? 'si' : NULL;
        $_POST[opcion4] = $_POST[opcion4] != '' ? 'si' : NULL;
        $_POST[opcion5] = $_POST[opcion5] != '' ? 'si' : NULL;
        $parametros = array("nom_aplica" => "c_esfera",
            "nom_usuari" => "InterfSPG",
            "pwd_clavex" => "sp62013_wd",
            "nom_aplsol" => 'satt_faro',
            "cod_tercer" => $_POST[tercero],
            "cod_usrspg" => $_POST[user_id],
            "nom_dirigi" => $_POST[diridi],
            "ind_ingres" => $_POST[opcion0],
            "ind_salari" => $_POST[opcion1],
            "ind_cajcom" => $_POST[opcion2],
            "ind_retiro" => $_POST[opcion3],
            "ind_logosx" => $_POST[opcion4]
        );
        $oSoapClient = new nusoap_client("https://ut.intrared.net/ap/interf/app/consultor/wsdl/consultor.wsdl", true);
        $oSoapClient->soap_defencoding = 'ISO-8859-1';
        $mResult = $oSoapClient->call("getCertifLaboral", $parametros);
        if ($oSoapClient->fault) {
            echo "<h2>Respuesta Fault WSDL</h2>";
            echo "<pre>";
            print_r($oSoapClient->faultcode . ':' . $oSoapClient->faultdetail . ':' . $oSoapClient->faultstring);
            echo "</pre>";
        } else {
            $err = $oSoapClient->getError();
            if ($err) {
                echo "<h2>Respuesta Error WSDL</h2>";
                echo "<pre>";
                print_r($err);
                echo "</pre>";
            } else {
                if ($mResult['cod_respon'] == '1000') {
                    echo $mResult['cod_respon'];
                    $_SESSION['mDataPdfCertif'] = base64_decode($mResult['data']);
                } else {
                    echo $mResult['cod_respon'];
                }
            }
        }
    }

    function ShowPdf() {
        $archivo = "certificado_" . $_GET['tercero'] . "_" . date('Y_m_d') . ".pdf";
        header('Content-Type: application/octet-stream');
        header('Expires: 0');
        header('Content-Disposition: attachment; filename="' . $archivo . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        echo $_SESSION['mDataPdfCertif'];
    }

}

if ($_GET["Ajax"] == 'on' || $_POST['Ajax'] == 'on') {
    $proceso = new CertifLaboral();
} else {
    $proceso = new CertifLaboral($this->conexion, $this->usuario_aplicacion, $this->codigo);
}
