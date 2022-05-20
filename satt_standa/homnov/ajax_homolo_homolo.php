<?php
/* ! \file: ajax_homolo_homolo.php
 *  \brief: Se crea documento que contiene multiples funciones para el funcionamiento de los estados Homologados por el cliente
 *  \author: Ing. Luis Marique
 *  \version: 1.0
 *  \date: 23/07/2019
 *  \bug: 
 *  \bug: 
 *  \warning:
 */

class ajax_homolo_homolo {

    private static $cConexion,
    $cCodAplica,
    $cUsuario;

    function __construct($co = null, $us = null, $ca = null) {
        if ($_REQUEST[Ajax] === 'on' || $_POST[Ajax] === 'on') {
            @include_once( "../lib/ajax.inc" );
            @include_once( "../lib/general/constantes.inc" );
            @include_once( "../lib/general/functions.inc" );

            self::$cConexion = $AjaxConnection;
            self::$cUsuario = $_SESSION['datos_usuario'];
            self::$cCodAplica = $_SESSION['codigo'];
        } else {
            self::$cConexion = $co;
            self::$cUsuario = $us;
            self::$cCodAplica = $ca;
        }

        if ($_REQUEST[Ajax] === 'on') {
            $opcion = $_REQUEST[Option];

            switch ($opcion) {
                case "getDataList":
                    $this->getDataList();
                    break;
                case "registrar":
                    $this->registrar();
                    break;
                case 'actualizarCod':
                    $this->actualizarCod();
                    break;
                case 'actualizarRegistro':
                    $this->actualizarRegistro();
                    break;
                default:
                    header('Location: ../../' . BASE_DATOS . '/index.php?window=central&cod_servic=557&menant=557');
                    break;
            }
        }
    }

    /* ! \fn: getDataList
     *  \brief: funcion para cargar los datos de Estados registrados
     *  \author: Ing. Luis Manrique
     *  \date: 23/07/2019
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return 
     */
    private function getDataList() {
        $datos = (object) $_POST;

        $mHtml = new FormLib(2);
        $mHtml->Form(array("action" => "index.php", "method" => "post", "name" => "form_search", "header" => "TRANSPORTADORAS", "enctype" => "multipart/form-data"));
        $mHtml->Row("td");

        $mHtml->OpenDiv("id:tabla; class:accordion");
        $mHtml->SetBody("<h1 style='padding:6px'><B>Datos Consignados</B></h1>");
        $mHtml->OpenDiv("id:sec2");
        $mHtml->OpenDiv("id:form3; class:contentAccordionForm");

        $mSql = "SELECT 
                        a.cod_estcli, 
                        a.nom_estcli, 
                        IF(
                            a.ind_estcli = '1',
                            'Activo',
                            'Inactivo'
                        ) AS ind_estcli
                  FROM
                        ". BASE_DATOS .".tab_genest_tracki a";
        
        $_SESSION["queryXLS"] = $mSql;

        if (!class_exists(DinamicList)) {
            include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
        }
        $list = new DinamicList(self::$cConexion, $mSql, "1", "no", 'ASC');
        $list->SetClose('no');
        $list->SetHeader("Codigo", "field:a.cod_estcli; type:link; onclick:linkCodEstado(this); width:1%;");
        $list->SetHeader("Nombre Estado", "field:a.nom_estcli; width:1%;");
        $list->SetHeader("Estado", "field:ind_estcli; width:1%;", [0 => ['',''], ['0','Inactivo'],['1', 'Activo']]);
        $list->SetHidden("cod_consec", "0");
        $list->Display(self::$cConexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $Html = $list->GetHtml();
        $mHtml->SetBody($Html);
        $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        $mHtml->CloseForm();
        echo $mHtml->MakeHtml();
    }

    /* ! \fn: registrar
     *  \brief: funcion para registrar un estado HOmologado
     *  \author: Ing. Luis Manrique
     *  \date: 23/07/2019
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return boolen
     */
    private function registrar() {
        $datos = (object) $_POST;
        $datos->usr_creaci = $_SESSION["datos_usuario"]["cod_usuari"];
        $sql = "INSERT INTO " . BASE_DATOS . ".tab_genest_tracki( nom_estcli, ind_estcli, usr_creaci, fec_creaci)VALUES('$datos->nom_estcli', '$datos->ind_estcli', '$datos->usr_creaci', NOW())";
        
        $consulta = new Consulta($sql, self::$cConexion, 'RC');

        if (count($consulta) > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }


    /*! \fn: actualizarCod
     *  \brief: Trae el Estado por Codigo
     *  \author: Ing. Luis Manrique
     *  \date: 22/07/2019
     *  \date modified: dd/mm/aaaa
     *  \return: string
     */
    private function actualizarCod() {
        $sql = "SELECT  a.cod_estcli,
                	    a.nom_estcli, 
                        a.ind_estcli 
                  FROM ".BASE_DATOS.".tab_genest_tracki a  
                 WHERE a.cod_estcli = ".$_REQUEST['cod_estcli'];
        $consulta = new Consulta($sql, self::$cConexion);
        $result = $consulta->ret_matrix('a');
        
        echo json_encode($result[0]);
    }

    /*! \fn: actualizarCod
     *  \brief: Trae el Estado por Codigo
     *  \author: Ing. Luis Manrique
     *  \date: 22/07/2019
     *  \date modified: dd/mm/aaaa
     *  \return: string
     */

    private function actualizarRegistro() {
        $datos = (object) $_POST;
        $datos->usr_modif = $_SESSION["datos_usuario"]["cod_usuari"];
        $sql = "UPDATE " 
                        . BASE_DATOS . ".tab_genest_tracki 
                   SET  nom_estcli = '$datos->nom_estcli', 
                        ind_estcli = '$datos->ind_estcli', 
                        usr_modifi  = '$datos->usr_modif', 
                        fec_modifi  = NOW()
                 WHERE  cod_estcli = $datos->cod_estcli";
        
        $consulta = new Consulta($sql, self::$cConexion, 'RC');

        if (count($consulta) > 0) {
            echo 1;
        } else {
            echo 0;
        }
    }
}

if ($_REQUEST[Ajax] === 'on') {
    $_INFORM = new ajax_homolo_homolo();
}
?>