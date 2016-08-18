<?php

ini_set('display_errors', false);
session_start();

class AjaxParametrizar {

    var $conexion;

    public function __construct() {
        $_AJAX = $_REQUEST;
        include_once('../lib/bd/seguridad/aplica_filtro_perfil_lib.inc');
        include_once('../lib/bd/seguridad/aplica_filtro_usuari_lib.inc');
        include_once('../lib/ajax.inc');
        include_once('../lib/general/constantes.inc');
        $this->conexion = $AjaxConnection;
        $this->$_AJAX['option']($_AJAX);
    }

    private function Style() {
        echo '
        <style>
        .CellHead
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:13px;
          background-color: #35650F;
          color:#FFFFFF;
          padding: 4px;
        }
        
        .CellHead2
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:13px;
          background-color: #35650F;
          color:#FFFFFF;
          padding: 4px;
        }
        
        .cellInfo1
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #EBF8E2;
          padding: 2px;
        }
        
        .cellInfo2
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #DEDFDE;
          padding: 2px;
        }
        
        .cellInfo
        {
          font-family:Trebuchet MS, Verdana, Arial;
          font-size:11px;
          background-color: #FFFFFF;
          padding: 2px;
        }
        
        tr.row:hover  td
        {
          background-color: #9ad9ae;
        }
        
        .StyleDIV
          {
            background-color: rgb(240, 240, 240);
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 99%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .Style2DIV
          {
            background-color: rgb(255, 255, 255);
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 95%; 
            min-height: 50px; 
            border-radius: 5px 5px 5px 5px;
          }
          .TRform
          {
            padding-right:3px; 
            padding-top:15px; 
            font-family:Trebuchet MS, Verdana, Arial; 
            font-size:12px;
          }
        </style>';
    }

    protected function getProtoc($mData) {
        $mSql = "SELECT a.cod_protoc, UPPER( a.des_protoc ) as des_protoc
               FROM " . BASE_DATOS . ".tab_genera_protoc a
              WHERE a.des_protoc LIKE '%" . $mData['term'] . "%'
                AND a.ind_activo = '1'
           ORDER BY 2 
              LIMIT 10";

        $consulta = new Consulta($mSql, $this->conexion);
        $transpor = $consulta->ret_matriz();

        $data = array();
        for ($i = 0, $len = count($transpor); $i < $len; $i++) {
            $data [] = '{"label":"' . utf8_encode($transpor[$i][0] . ' - ' . $transpor[$i][1]) . '","value":"' . utf8_encode($transpor[$i][0] . ' - ' . $transpor[$i][1]) . '"}';
        }
        echo '[' . join(', ', $data) . ']';
    }

    protected function getEmptra($mData) {
        $mSql = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) as nom_tercer
               FROM " . BASE_DATOS . ".tab_tercer_tercer a,
                    " . BASE_DATOS . ".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer 
              AND   b.cod_activi = 1
              AND   (a.cod_tercer LIKE '%" . $mData['term'] . "%' OR a.nom_tercer LIKE '%" . $mData['term'] . "%' OR a.abr_tercer LIKE '%" . $mData['term'] . "%')
              ORDER BY 2 ";


        $consulta = new Consulta($mSql, $this->conexion);
        $transpor = $consulta->ret_matriz();

        $data = array();
        for ($i = 0, $len = count($transpor); $i < $len; $i++) {
            $data [] = '{"label":"' . utf8_encode($transpor[$i][0] . ' - ' . $transpor[$i][1]) . '","value":"' . utf8_encode($transpor[$i][0] . ' - ' . $transpor[$i][1]) . '"}';
        }
        echo '[' . join(', ', $data) . ']';
    }

    protected function ValidateProtoc($mData) {
        $mSql = "SELECT 1
               FROM " . BASE_DATOS . ".tab_genera_protoc a
              WHERE a.cod_protoc = '" . trim($mData['cod_protoc']) . "'";

        $consulta = new Consulta($mSql, $this->conexion);
        $transpor = $consulta->ret_matriz();
        if (sizeof($transpor) > 0)
            echo 'y';
        else
            echo 'n';
    }

    protected function ValidateEmptra($mData) {
        $mSql = "SELECT 1
               FROM " . BASE_DATOS . ".tab_tercer_tercer a
              WHERE a.cod_tercer = '" . trim($mData['cod_tercer']) . "'";

        $consulta = new Consulta($mSql, $this->conexion);
        $transpor = $consulta->ret_matriz();
        if (sizeof($transpor) > 0)
            echo 'y';
        else
            echo 'n';
    }

    protected function SetSubcausas($mData) {
        $mSelect = "SELECT cod_consec, tex_encabe 
                  FROM " . BASE_DATOS . ".tab_genera_subcau 
                 WHERE cod_consec IN(" . $mData['checks'] . ")";

        $consulta = new Consulta($mSelect, $this->conexion);
        $mSubcau = $consulta->ret_matriz();

        $mHtml = '
              <style>
                #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
                #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
                #sortable li span { position: absolute; margin-left: -1.3em; }
              </style>
              <script> 
                $( "#sortable" ).sortable();
                $( "#sortable" ).disableSelection();
                $("input[type=button]").button();
              </script>';

        $mHtml .= '<br><br><ul id="sortable" style="width:30%;">';
        foreach ($mSubcau as $row)
            $mHtml .= '<li class="ui-state-default" id="' . $row['cod_consec'] . '"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' . $row['cod_consec'] . ' - ' . $row['tex_encabe'] . '</li>';
        $mHtml .= '</ul>';

        $mHtml .= '<br><input type="button" value="Registrar" onclick="SendSubcausas();" />';

        echo $mHtml;
    }

    protected function SendSubcausas($mData) {

        $verifi = explode(',', $mData['cod_verify']);

        $consulta = new Consulta("SELECT 1", $this->conexion, "BR");


        $mDelete = "DELETE 
                      FROM " . BASE_DATOS . ".tab_asigna_subcau
                     WHERE cod_protoc = '" . (int) $mData['cod_protoc'] . "'";

        $consulta = new Consulta( $mDelete, $this -> conexion, "R" );

        $counter = 1;
        foreach (explode('|', $mData['orden']) as $row) {
            
            $mInsert = "INSERT INTO " . BASE_DATOS . ".tab_asigna_subcau
                            ( cod_protoc, cod_subcau, cod_ordenx,
                              cod_tercer, usr_creaci, fec_creaci
                     )VALUES( '" . (int) $mData['cod_protoc'] . "', '" . $row . "', '" . $counter . "',
                               '" . $mData['cod_transp'] . "','" . $_SESSION['datos_usuario']['cod_usuari'] . "', NOW() )";

            $consulta = new Consulta( $mInsert, $this -> conexion, "R" );

            $counter++;
        }

        $ind_realiz = $verifi[0] != 0 ? 1 : 0;
        $ind_gpsxxx = $verifi[1] != 0 ? 1 : 0;
        $ind_notcli = $verifi[2] != 0 ? 1 : 0;
        $ind_encveh = $verifi[3] != 0 ? 1 : 0;


        $mInsert = "DELETE FROM " . BASE_DATOS . ".tab_verifi_protoc 
                    WHERE cod_transp = '" . $mData['cod_transp'] . "' 
                      AND cod_protoc = '" . (int) $mData['cod_protoc'] . "'";

        $consulta = new Consulta($mInsert, $this->conexion, "R");


        $mInsert = "INSERT INTO " . BASE_DATOS . ".tab_verifi_protoc
                          ( cod_transp, cod_protoc, ind_realiz,
                            ind_gpsxxx, ind_notcli, ind_encveh,
                            usr_creaci, fec_creaci
                   )VALUES( '" . $mData['cod_transp'] . "','" . (int) $mData['cod_protoc'] . "', '" . $ind_realiz . "', '" . $ind_gpsxxx . "',
                             '" . $ind_notcli . "','" . $ind_encveh . "','" . $_SESSION['datos_usuario']['cod_usuari'] . "', NOW() )";

        $consulta = new Consulta($mInsert, $this->conexion, "R");



        if ($insercion = new Consulta("COMMIT", $this->conexion)) {
            echo "1000";
        } else {
            echo "9999";
        }
    }

    protected function MainLoad($mData) {

      echo ".";
        echo "<link rel=\"stylesheet\" href=\"../satt_standa/estilos/dinamic_list.css\" type=\"text/css\">";
        echo "<script language=\"JavaScript\" src=\"../satt_standa/js/dinamic_list.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../satt_standa/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../satt_standa/js/functions.js\"></script>\n";

        $this->Style();

        $mHtml = '<center>';

        $mHtml .= '<br><div >';
        $mHtml .= '<table width="100%" cellspacing="1" cellpadding="0">';

        $mHtml .= '<tr>';
        $mHtml .= '<td width="40%" style="padding-right:30px;" align="right" class="cellInfo">Digite empresa :</td>';
        $mHtml .= '<td ><input type="text" size="80" maxlength="100" name="nom_emptra" id="nom_emptraID" /></td>';
        $mHtml .= '</tr>';

        $mHtml .= '</table>';

        $mHtml .= '<div id="OrderID">';
        $mHtml .= '</div>';

        $mHtml .= '</div>';
        $mHtml .= '<table width="100%" cellspacing="2px" cellpadding="0">';
        $mHtml .= '<tr>';
        $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:18px; font-weight:bold; padding-bottom:5px; color: #285C00;" ><i>Selecci&oacute;n de Protocolo</i></td>';
        $mHtml .= '</tr>';
        $mHtml .= '</table>';

        $mHtml .= '<br><div class="Style2DIV">';
        $mHtml .= '<table width="100%" cellspacing="1" cellpadding="0">';

        $mHtml .= '<tr>';
        $mHtml .= '<td width="40%" style="padding-right:15px;" align="right">Digite Protocolo:</td>';
        $mHtml .= '<td width="60%"><input type="text" size="80" maxlength="100" name="nom_protoc" id="nom_protocID" /></td>';
        $mHtml .= '</tr>';

        $mHtml .= '</table>';

        $mHtml .= '<div id="OrderID">';
        $mHtml .= '</div>';

        $mHtml .= '</div>';

        $mHtml .= '<br><br><div id="DynamicID" style="display:none;"><span style="font-family:Trebuchet MS, Verdana, Arial; font-size:12px; font-weight:bold; padding-bottom:5px; color: #285C00;">SELECCIONE LAS SUBCAUSAS QUE DESEA AGREGAR AL PROTOCOLO</span>';

        $mSql = "SELECT a.cod_consec, b.nom_config, a.tex_encabe, 
                      IF( a.ind_requer = '1' ,'SI', 'NO'), a.des_texto
                 FROM " . BASE_DATOS . ".tab_genera_subcau a,
                      " . BASE_DATOS . ".tab_config_subcau b
                WHERE a.cod_tipoxx = b.num_consec ";

        $_SESSION["queryXLS"] = $mSql;
        $list = new DinamicList($this->conexion, $mSql, 1, "yes");
        $list->SetClose('no');
        $list->SetHeader("Consecutivo", "field:a.cod_consec; width:15%;");
        $list->SetHeader("Tipo", "field:b.nom_config; width:20%");
        $list->SetHeader("Encabezado", "field:a.tex_protoc; width:25%");
        $list->SetHeader("Requerido", "field:IF( a.ind_requer = '1' ,'SI', 'NO'); width:10%");
        $list->SetHeader("Descripcion", "field:a.des_texto; width:30%");

        $list->Display($this->conexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $mHtml .= $list->GetHtml();

        #$mHtml .= '<br><br><div id="DynamicaID" style="display:none;"><span style="font-family:Trebuchet MS, Verdana, Arial; font-size:12px; font-weight:bold; padding-bottom:5px; color: #285C00;">SELECCIONE LAS Verificaciones</span>';

        $mSql = "SELECT '1' AS cod_algi , 'Realizado' AS nom_algo 
               UNION 
               SELECT '2' AS cod_algi , 'Verificacion GPS' AS nom_algo 
               UNION 
               SELECT '3' AS cod_algi , 'Notificar Al Cliente' AS nom_algo 
               UNION 
               SELECT '4' AS cod_algi , 'Dejar vehiculo recomendado' AS nom_algo    ";

        $list = new DinamicList($this->conexion, $mSql, 1, "yes");
        $list->SetClose('no');
        $list->SetHeader("Codigo", "field:cod_algi;  ", false);
        $list->SetHeader("Tipo Veridficaicion", "field:nom_algo;  ");
        $list->setHidden("val_verifi", 0);


        $list->Display($this->conexion);

        $mHtml .= $list->GetHtml();

        $mHtml .= '<br><br><input type="button" value="Aceptar" onclick="SetSubcausas();" />';
        $mHtml .= '</div>';

        $mHtml .= '</center>';

        echo $mHtml;
    }

}

$proceso = new AjaxParametrizar();
?>