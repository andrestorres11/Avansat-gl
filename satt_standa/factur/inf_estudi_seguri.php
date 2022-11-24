<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
class InfEstudiSeguri 
{
    var $conexion,
        $cod_aplica,
        $usuario;
    var $cNull = array(array('', '- Todos -'));
    var $cNullt = array();

    function __construct($co, $us, $ca) {
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        switch ($_POST[option]) {
            case 99:
                $this->filtro();
                $this->getInform();
                break;
            default:
                $this->filtro();
                break;
        }
    }

    function filtro()
    {
        if ($_REQUEST['fec_inicia'] == NULL || $_REQUEST['fec_inicia'] == '') {
            $fec_actual = strtotime('-7 day', strtotime(date('Y-m-d')));
            $_REQUEST['fec_inicia'] = date('Y-m-d', $fec_actual);
        }

        if ($_REQUEST['fec_finali'] == NULL || $_REQUEST['fec_finali'] == ''){
            $_REQUEST['fec_finali'] = date('Y-m-d');
        }
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/inf_estudi_seguri.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.blockUI.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/multiselect/jquery.multiselect.filter.min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/multiselect/jquery.multiselect.min.js\"></script>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/bootstrap.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/multiselect/jquery.multiselect.filter.css' type='text/css'>\n";
        echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
        echo "<style> .small, small {
            font-size: 114% !important;
        }</style>";
        $_TRANSP = $this->getTransport();

        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Informe Estudio Seguridad", "formulario\" id=\"formularioID");
            $formulario -> nueva_tabla();
            $formulario->lista("Transportadora", "cod_transp\" id=\"cod_transpID", array_merge($this->cNull, $_TRANSP), 0);
            $formulario -> texto ("No. solicitud","text","cod_solici\" id=\"cod_soliciID",1,15,15,"","");
            $formulario->texto("Fecha Inicial:", "text", "fec_inicia\"  id=\"fec_iniciaID", 0, 10, 10, "", $_REQUEST['fec_inicia']);
            $formulario->texto("Fecha Final:", "text", "fec_finali\"  id=\"fec_finaliID", 1, 10, 10, "", $_REQUEST['fec_finali']);

            $formulario -> nueva_tabla();
            echo "<BR>";
            $formulario -> botoni("Buscar","listar()",0);
            $formulario -> nueva_tabla();
            $formulario -> oculto("window","central",0);
            $formulario -> oculto("standa\" id=\"standaID",DIR_APLICA_CENTRAL,0);
            $formulario -> oculto("cod_servic",$_REQUEST["cod_servic"],0);
            $formulario -> oculto("option\" id=\"optionID",'',0);
            $formulario -> oculto("cod_transp_\" id=\"cod_transpID_",'',0);
            echo "<hr style=\"border: 1px solid black;\">";
            $formulario -> cerrar();
    }

    function getInform(){
        
        echo '<table width="100%" cellpadding="4" cellspacing="0" class="formulario">';
        echo '<tr>
                <td>
                <iframe src="../satt_standa/factur/inf_estudi_seguri_table.php?cod_transp='.$_POST['cod_transp_'].'&cod_solici='.$_POST['cod_solici'].'&fec_inicia='.$_POST['fec_inicia'].'&fec_finali='.$_POST['fec_finali'].'" id="iframe1"    style="max-width:100%;overflow-y:hidden;overflow-x:scroll;margin-left: 9px;" scrolling="si" onmouseover="this.style.height=(this.contentDocument.body.scrollHeight+20) +\'px\';this.style.width=(this.contentDocument.body.scrollWidth) +\'px\';" frameborder="0"  ></iframe>
                </td>
            </tr>';
        echo '</table>';
    }

    private function getTransport(){
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
        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult = $mConsult->ret_matriz('a');
        return $mResult;
    }

    
}
$service = new InfEstudiSeguri($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>