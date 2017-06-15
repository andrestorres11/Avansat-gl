<?php
/*
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);*/

class AjaxDespacDestin {

    var $conexion;
    var $ind_estado = array();
    var $ind_estado_ = array();
    var $ind_destin;
    var $num_docume;

    public function __construct() {

        $this->ind_estado_[0][0] = '';
        $this->ind_estado_[0][1] = '--';
        $this->ind_estado[1][0] = 'PENDIENTE';
        $this->ind_estado[1][1] = 'PENDIENTE';
        $this->ind_estado[2][0] = 'ACTUALIZADO';
        $this->ind_estado[2][1] = 'ACTUALIZADO';
        $this->ind_destin = "";
        $this->num_docume = "";


        $_AJAX = $_REQUEST;
        include('../lib/ajax.inc');
        include_once( "../lib/general/dinamic_list.inc" );
        include_once('../lib/general/constantes.inc');

        $this->conexion = $AjaxConnection;
        $this->$_AJAX['option']($_AJAX);

    }

    protected function SetDestinatarios($_AJAX) {
 
     $mSelect = "SELECT num_docume, num_docalt, cod_genera,
                       nom_destin, cod_ciudad, dir_destin, 
                       num_destin, fec_citdes, hor_citdes,
                       fec_findes
                  FROM " . BASE_DATOS . ".tab_despac_destin 
                 WHERE num_despac = '" . $_AJAX['num_despac'] . "' 
                       GROUP BY nom_destin
                 ORDER BY fec_citdes ASC, hor_citdes ASC";

        $consulta = new Consulta($mSelect, $this->conexion);
        $_DESTIN = $consulta->ret_matriz();






        # Datos Destinatari del webservice -------------------------------------------------
        $mQuery = "SELECT a.num_docume, a.num_docalt, a.cod_genera,
                       a.nom_destin, a.cod_ciudad, a.dir_destin, 
                       a.num_destin, a.fec_citdes, a.hor_citdes
                  FROM " . BASE_DATOS . ".tab_despac_cordes a, 
                       " . BASE_DATOS . ".tab_despac_despac b 
                 WHERE a.num_despac = b.cod_manifi AND
                       b.num_despac = '" . $_AJAX['num_despac'] . "' 
                       GROUP BY a.nom_destin";

        $consulta = new Consulta($mQuery, $this->conexion);
        $_DESTINDATA = $consulta->ret_matriz();

        foreach ($_DESTINDATA as $fKey => $fData) {
            $mDatDestin[] = "Destinatario: " . ($fKey + 1) . "\nNum Documento: " . $fData["num_docume"] . ", Documento Alt: " . $fData["num_docalt"] . " Destino: " . $fData["nom_destin"] . "\n" .
                    "Direccion Destino: " . $fData["dir_destin"] . " Num Destinatario: " . $fData["num_destin"] . " Fecha: " . $fData["fec_citdes"] . " Hora: " . $fData["hor_citdes"];
        }

        $mSelect = "SELECT obs_despac
                  FROM " . BASE_DATOS . ".tab_despac_despac
                 WHERE num_despac = '" . $_AJAX['num_despac'] . "' ";

        $consulta = new Consulta($mSelect, $this->conexion);
        $_OBSERV = $consulta->ret_matriz();


        $mHtml = "<div class='StyleDIV' id='DestinID'>";

        $mHtml .= "<textarea name='obs' id='obsID' cols='100' rows='9' readonly>" . $_OBSERV[0]['obs_despac'] . "\n\n\n" . utf8_decode((join("\n", $mDatDestin))) . "</textarea><br>";

        $mHtml .= '<Label class="label-info" style="color:#000000;"> Cuenta con cita</Label><br>';
        
        $mSqlRadio  = "SELECT a.ind_citdes "
                . "FROM tab_despac_destin a "
                . "WHERE a.num_despac = '" . $_AJAX['num_despac'] . "' "
                . "LIMIT 1";
        
        
        
        $consulta = new Consulta($mSqlRadio, $this->conexion);
        $mDataRadio = $consulta->ret_matriz();
        

        
        $mHtml .= '<input type="radio" name="ind_citaxx" value="1" ' . ( $mDataRadio[0]['ind_citdes'] == 1 ? "checked" : "" ) . ' ><Label class="label-info" style="color:#000000;"> SI </Label>';
        $mHtml .= '<input type="radio" name="ind_citaxx" value="0" ' . ( $mDataRadio[0]['ind_citdes'] == 0 ? "checked" : "" ) . ' ><Label class="label-info" style="color:#000000;"> NO </Label>';

        $mHtml .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';

        $mHtml .= '<tr>';
        $mHtml .= '<td align="left" width="100%" class="label-info" colspan="10">Destinatarios asignados al Despacho. Para agregar otro haga click <a style="color:#285C00; text-decoration:none; cursor:pointer;" onclick="AddGrid();">aqu&iacute;</a><br>&nbsp;</td>';
        $mHtml .= '</tr>';

        $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">Seleccionar Clientes <a onclick="PopDestinPadres();">[SELECCIONAR]</a></td>';
        $mHtml .= '</tr>';

        $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellInfo1" width="20%">';
        $mHtml .= '</tr>';

        $mHtml .= '</table>';
        $mHtml .= '<input type="hidden" value="" name="clientes" id="clientes">';
        $mHtml .= '<input type="hidden" value="" name="fecha" id="fecha">';
        $mHtml .= '<input type="hidden" value="" name="hora" id="hora">';
        $mHtml .= '<input type="hidden" value="'.$_REQUEST['num_viajex'].'" name="num_viajex" id="num_viajex">'; 


        # normal
        $count = 0;

        foreach ($_DESTIN as $row) {
            $_AJAX['counter'] = $count;
            $mHtml .= $this->ShowDestinNew($_AJAX, $row);
            $numDocume[$count] = $row[0];
            $count++;
        }

        #descripcion
        $countb = $count;
        foreach ($_DESTINDATA as $rowx) {
            $bandera = 0;
            for ($i = 0; $i < $count; $i++) {
                $bandera = $rowx[0] == $numDocume[$i] ? $bandera + 1 : $bandera;
            }
            if ($bandera == '0') {
                $_AJAX['counter'] = $countb;
                $mHtml .= $this->ShowDestinNew($_AJAX, $rowx);
                $countb++;
            }
        }

        $_AJAX['counter'] = $countb;
        $mHtml .= $this->ShowDestinNew($_AJAX);

        $mHtml .= '<input type="hidden" id="counterID" value="' . $countb . '" />';
        $mHtml .= "</div>";

        $mHtml .= "<div class='StyleDIV'>";
        $mHtml .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';

        $mHtml .= '<tr>';
        $mHtml .= '<td align="left" width="100%" class="label-info" colspan="10">Destinatarios asignados al Despacho. Para agregar otro haga click <a style="color:#285C00; text-decoration:none; cursor:pointer;" onclick="AddGrid();">aqu&iacute;</a><br>&nbsp;</td>';
        $mHtml .= '</tr>';

        $mHtml .= '</table>';
        $mHtml .= '<br><center>';
        
         
        if ($_SESSION['datos_usuario']['cod_perfil'] != '712') {
             $mHtml .= '<input class="crmButton small save" type="button" id="InsertID" value="Aceptar" onclick="InserDestin();"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        

        $mHtml .= '<input class="crmButton small save" type="button" id="CancelID" value="Cancelar" onclick="$(\'#PopUpID\').dialog(\'close\');"/></center>';
        $mHtml .= "</div>";

        echo $mHtml;
    }

    protected function addClient(){
  
        $client = $_REQUEST['cod_client'];
 
        $query = "SELECT a.cod_client, a.nom_client
                        FROM tab_destin_client a
                        WHERE a.cod_client = '". $client ."' ";
 
        $consulta = new Consulta($query, $this->conexion);
        $clients = $consulta->ret_matriz();

        $html .= '<tr id="DLRowInfo'.$_REQUEST['current'].'" class="DLRow1" title="Fila 1, Registro 1.">' ;
        $html .= '<td id="DLCell'.$_REQUEST['current'].'-0" align="left"><input type="checkbox" checked value="'.$_REQUEST['current'].'"></td>' ;
        $html .= '<td id="DLCell'.$_REQUEST['current'].'-1" align="left">'.$clients[0]['cod_client'].'</td>' ;
        $html .= '<td id="DLCell'.$_REQUEST['current'].'-2" align="left">'.$clients[0]['nom_client'].'</td>' ;
        $html .= '<td id="DLCell'.$_REQUEST['current'].'-3" align="left"><input type="text" name="fecha" value=""></td>' ;
        $html .= '<td id="DLCell'.$_REQUEST['current'].'-4" align="left"><input type="text" name="hora" value=""></td>' ;
        $html .= '<td id="DLCell'.$_REQUEST['current'].'-5" align="left"><img src="../'.DIR_APLICA_CENTRAL.'/imagenes/error.gif" onclick="AjaxEliminar('.$_REQUEST['current'].');"></td>' ;
        $html .= '</tr>' ;

        echo $html;
        
    }

    protected function getCliente($mData){
 


        $notIn = join("','" ,explode(",", $mData['clientes']));
        $notIn2 = join("','" ,explode(",", $mData['nombres']));

        $query = "SELECT a.cod_client, a.nom_client
                    FROM tab_destin_client a
                    WHERE a.cod_client LIKE '%".$mData['term']."%' 
                    AND a.cod_client NOT IN ('".$notIn."')
                    OR a.nom_client LIKE '%".$mData['term']."%'
                    AND a.nom_client NOT IN ('".$notIn2."')";

/*        echo "<pre>";
        print_r($query);
        print_r($h);
        echo "</pre>";*/
        
  
        $consulta = new Consulta($query, $this->conexion);
        $mDataResult = $consulta->ret_matriz();

        $data = array();
        for ($i = 0, $len = count($mDataResult); $i < $len; $i++) {
            $data [] = '{"label":"' . utf8_encode($mDataResult[$i][0] . ' - ' . $mDataResult[$i][1]) . '","value":"' . utf8_encode($mDataResult[$i][0] . ' - ' . $mDataResult[$i][1]) . '"}';
        }
        echo '[' . join(', ', $data) . ']';
    }

    protected function loadPopupPadres(){

        $query = "SELECT a.cod_client, a.nom_client
                    FROM tab_destin_client a
                    WHERE 1=1 ";
 
        $consulta = new Consulta($query, $this->conexion);
        $mDataResult = $consulta->ret_matriz();
 
        $html = "";
        $html .= '<div id="divSearchID"  class="DLRow1" width="100%">
                    <script type="text/javascript">
                     completeClients();
                    </script>'; 
        $html .= '<center>';
        $html .= '<label><font color="black"><strong>Buscar Cliente: </strong></font></label>';
        $html .= '<input type="text" id="loadCliente">'; 
        $html .= "</center>";
        $html .= "</div>";
        $html .= '<table class="DLTable" width="100%" cellspacing="1" cellpadding="1" align="center" id="tabClientes">' ;
        $html .= '<tr id="DLRowHeader" class="DLRowHeader">' ;
        $html .= '<th width="" align="center" style="color:#ffffff">#</th>' ;
        $html .= '<th width="" align="center" style="color:#ffffff">Codigo</th>' ;
        $html .= '<th width="" align="center" style="color:#ffffff">Cliente</th>' ;
        $html .= '<th width="" align="center" style="color:#ffffff">Fecha</th>' ;
        $html .= '<th width="" align="center" style="color:#ffffff">Hora</th>' ;
        $html .= '<th width="" align="center" style="color:#ffffff"></th>' ;
        $html .= '</tr>' ;
        $con = 0;
        foreach ($mDataResult as $client) {


            $query = "SELECT a.cod_client, a.fec_citdes, a.hor_citdes, a.ind_cumpli
                        FROM tab_despac_inddes a
                        WHERE a.num_despac = '". $_REQUEST['num_despac'] ."' 
                        AND a.cod_client = '".$client[0]."'";

            $consulta = new Consulta($query, $this->conexion);
            $mDataResultClientes = $consulta->ret_matriz();

            if(isset($mDataResultClientes[0])){
                $html .= '<tr id="DLRowInfo'.$con.'" class="DLRow1" title="Fila 1, Registro 1.">' ;
                $html .= '<td id="DLCell'.$con.'-0" align="left"><input type="checkbox" '.($mDataResultClientes[0][3] == 0 ? "" : "disabled").' value="'.$con.'" '. (isset($mDataResultClientes[0]) ? "checked" : "") .'></td>' ;
                $html .= '<td id="DLCell'.$con.'-1" align="left">'.$client[0].'</td>' ;
                $html .= '<td id="DLCell'.$con.'-2" align="left">'.$client[1].'</td>' ;
                $html .= '<td id="DLCell'.$con.'-3" align="left"><input type="text" '.($mDataResultClientes[0][3] == 0 ? "" : "disabled").' name="fecha" value="'.$mDataResultClientes[0][1].'"></td>' ;
                $html .= '<td id="DLCell'.$con.'-4" align="left"><input type="text" '.($mDataResultClientes[0][3] == 0 ? "" : "disabled").' name="hora" value="'.$mDataResultClientes[0][2].'"></td>' ;
                $html .= '<td id="DLCell'.$con.'-5" align="left">'.($mDataResultClientes[0][3] == 0 ?'<img src="../'.DIR_APLICA_CENTRAL.'/imagenes/error.gif" onclick="AjaxEliminar('.$con.');">' : "").'</td>' ;
                $html .= '</tr>' ;
            }
            $con++;
        }

                 $html .= "</table>";

        echo $html;

    }

    private function VerifyDestin($num_despac, $num_docume) {
        $mSql = "SELECT 1
               FROM " . BASE_DATOS . ".tab_despac_destin 
              WHERE num_despac = '" . $num_despac . "' 
                AND num_docume ='" . $num_docume . "'";
        $consulta = new Consulta($mSql, $this->conexion);
        $VER = $consulta->ret_matriz();

        return sizeof($VER) > 0 ? true : false;
    }

    protected function AjaxEliminar($_AJAX){

        $mConsultRep = "SELECT cod_consec
             FROM tab_despac_inddes
             WHERE cod_client = '".$cliente[0]."'   ";

        $consulta = new Consulta($mConsultRep, $this->conexion);
        $data = $consulta->ret_matriz();

        if( !isset($data[0][cod_consec])) {
            $query = "DELETE   
                                FROM tab_despac_inddes
                                WHERE num_despac = '". $_REQUEST['num_despac'] ."' 
                                    AND cod_client = '".$_REQUEST['cod_destin']."' ";

            $consulta = new Consulta($query, $this->conexion);
        }
    }

    protected function InserDestin($_AJAX) {
 
        

        $mFlag = true;
 
        $consec = new Consulta("START TRANSACTION;", $this->conexion);

        $num_despac = $_AJAX['num_despac'];
        $num_viajex = $_AJAX['num_viajex'];
        $mArrayDocume = $_AJAX['num_factur'];
        $mArrayDocAlt = $_AJAX['doc_altern'];
        $data_cliens = explode("|",$_AJAX['clientes']);

        /*$mDelete = "DELETE FROM " . BASE_DATOS . ".tab_despac_destin
                      WHERE num_despac = '" . $num_despac . "'
                        AND ( fec_findes = '0000-00-00 00:00:00' 
                         OR fec_findes IS NULL )";
        //echo $mDelete."<hr>";                     
        $consulta = new Consulta($mDelete, $this->conexion);*/
 
        foreach ($data_cliens as $cliente) {
            $cod_cliens[] = explode("/", $cliente); 
        }
 
        $mQueryConsec = "SELECT MAX(a.cod_consec) as max_consec
                         FROM " . BASE_DATOS . ".tab_despac_inddes a
                         WHERE num_despac = '".$num_despac."'";

        $consulta = new Consulta($mQueryConsec, $this->conexion);
        $max = $consulta->ret_matriz();

        $q = 0;
 

        if($max[0][0] != null)
            $q = (int)$max[0][0];
 
        foreach ($cod_cliens as  $cliente) {

            $mConsultRep = "SELECT cod_consec
                             FROM tab_despac_inddes
                             WHERE cod_client = '".$cliente[0]."'
                             AND num_despac = '".$num_despac."'";

            $consulta = new Consulta($mConsultRep, $this->conexion);
            $data = $consulta->ret_matriz();
 
 
            if( !isset($data[0] ) && $cliente[0] != '') {
 
                $mInsertF = "INSERT INTO " . BASE_DATOS . ".tab_despac_inddes
                (
                  cod_consec, num_despac, num_viajex, cod_client,
                  fec_citdes, hor_citdes, ind_citdes, usr_creaci, 
                  fec_creaci
                )
               VALUES (" .$q. ", '" . $num_despac . "', '" . $num_viajex . "', '" . $cliente[0] . "',
                       '".$cliente[1]."','".$cliente[2]."', '" .$_AJAX['ind_citaxx']."' ,'" . $_SESSION['datos_usuario']['cod_usuari'] . "',
                        NOW() );";
  
                if (!$consulta = new Consulta($mInsertF, $this->conexion)) {
                    $mFlag = false;
                } 
            }
            $q++;
        }
 
            
        foreach ($_AJAX[mData] as $mDestin) {

            if ($mDestin[nom_destin] != '') {


                $cantFactur = (sizeof($mDestin)) - 6;
                for ($i = 0; $i < $cantFactur; $i++) {

                    $qSelect = "SELECT a.num_despac "
                            . "FROM " . BASE_DATOS . ".tab_despac_destin a "
                            . "WHERE "
                            . "a.num_despac = '" . $_AJAX[num_despac] . "' "
                            . "AND a.num_docume = '" . $mDestin[factur . $i][num_factura] . "' ";
                    $consulta = new Consulta($qSelect, $this->conexion);
                    $mResult = $consulta->ret_matriz();

                    if ($mResult == NULL || $mResult == '') {


                        $mInsertF = "REPLACE INTO " . BASE_DATOS . ".tab_despac_destin
                        (
                          num_despac, num_docume, num_docalt, cod_genera,
                          nom_destin, cod_ciudad, dir_destin, num_destin, 
                          fec_citdes, hor_citdes, usr_creaci, fec_creaci,
                          ind_modifi, ind_citdes
                        )
                       VALUES ('" . $_AJAX[num_despac] . "', '" . $mDestin[factur . $i][num_factura] . "', '" . $mDestin[factur . $i][doc_alterna] . "', '" . $mDestin[factur . $i][cod_genera] . "','" .
                                $mDestin[nom_destin] . "', '" . $mDestin[cod_ciudad] . "', '" . $mDestin[dir_destin] . "', '" . $mDestin[nom_contac] . "', '" . $mDestin[fec_citdes] . "' ,
                           '" . $mDestin[hor_citdes] . "', '" . $_SESSION['datos_usuario']['cod_usuari'] . "', NOW(), '1' ,'" . $_AJAX['ind_citaxx'] . "' );";
 

                    //$consulta = new Consulta($mInsertF, $this->conexion);
                        if (!$consulta = new Consulta($mInsertF, $this->conexion)) {
                            $mFlag = false;
                        }
                    } else {


                        $qUpdate = "UPDATE  " . BASE_DATOS . ".`tab_despac_destin` a "
                                . "SET "
                                . "a.num_docume = '" . $mDestin[factur . $i][num_factura] . "', "
                                . "a.num_docalt = '" . $mDestin[factur . $i][doc_alterna] . "' , "
                                . "a.cod_genera = '" . $mDestin[factur . $i][cod_genera] . "' , "
                                . "a.nom_destin = '" . $mDestin[nom_destin] . "' , "
                                . "a.cod_ciudad = '" . $mDestin[cod_ciudad] . "' , "
                                . "a.dir_destin = '" . $mDestin[dir_destin] . "' , "
                                . "a.num_destin = '" . $mDestin[nom_contac] . "' , "
                                . "a.fec_citdes = '" . $mDestin[fec_citdes] . "' , "
                                . "a.hor_citdes = '" . $mDestin[hor_citdes] . "' , "
                                . "a.ind_modifi = 1  , "
                                . "a.usr_modifi = '" . $_SESSION['datos_usuario']['cod_usuari'] . "' , "
                                . "a.fec_modifi = NOW()"
                                . "WHERE  a.`num_despac` = '" . $_AJAX[num_despac] . "' "
                                . "AND a.num_docume =  '" . $mDestin[factur . $i][num_factura] . "' ";

                        $consulta = new Consulta($qUpdate, $this->conexion);
                    }
                }
            }
        }


        if ($mFlag) {
            $consulta = new Consulta("COMMIT;", $this->conexion);
            $mHtml .= '<center><table width="100%" cellspacing="2px" cellpadding="0">';

            $mHtml .= '<tr>';
            $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >La Informaci&oacute;n ha Sido Insertada Exitosamente.</i></td>';
            $mHtml .= '</tr>';
            $mHtml .= '</table></center>';
            echo $mHtml;
        } else {
            $consulta = new Consulta("ROLLBACK;", $this->conexion);
            $mHtml .= '<center><table width="100%" cellspacing="2px" cellpadding="0">';

            $mHtml .= '<tr>';
            $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >Error de insercion.</i></td>';
            $mHtml .= '</tr>';
            $mHtml .= '</table></center>';
        }
    }

    private function getCiudad($cod_ciudad = NULL) {
        $mSql = "SELECT cod_ciudad, UPPER( nom_ciudad ) AS nom_ciudad
               FROM " . BASE_DATOS . ".tab_genera_ciudad 
              WHERE ind_estado = '1'";
        if ($cod_ciudad != NULL) {
            $mSql .= " AND cod_ciudad = " . $cod_ciudad;
        }
        $mSql .= " ORDER BY 2";
        $consulta = new Consulta($mSql, $this->conexion);
        return $consulta->ret_matriz();
    }

    private function getGenera($cod_transp, $cod_genera = NULL) {
        $query = "SELECT a.cod_tercer, UPPER(a.abr_tercer) AS nom_tercer
                FROM " . BASE_DATOS . ".tab_tercer_tercer a,
                     " . BASE_DATOS . ".tab_tercer_activi b,
                     " . BASE_DATOS . ".tab_transp_tercer c
               WHERE a.cod_tercer = b.cod_tercer AND
                     a.cod_tercer = c.cod_tercer AND
                     c.cod_transp = '" . $cod_transp . "' AND
                     b.cod_activi = " . COD_FILTRO_CLIENT . "";
        if ($cod_genera != NULL) {
            $query .= " AND a.cod_tercer = '" . $cod_genera . "'";
        }
        $query .= " ORDER BY 2 ASC";

        $consulta = new Consulta($query, $this->conexion);
        return $consulta->ret_matriz();
    }

    private function GenerateSelect($arr_select, $name, $key = NULL, $events = NULL, $disabled = NULL) {
        $mHtml = '<select name="' . $name . '" id="' . $name . 'ID" ' . $events . ' ' . $disabled . '>';
        $mHtml .= '<option value="">- Seleccione -</option>';
        foreach ($arr_select as $row) {
            $selected = '';
            if ($row[0] == $key)
                $selected = 'selected="selected"';

            $mHtml .= '<option value="' . $row[0] . '" ' . $selected . '>' . utf8_encode($row[1]) . '</option>';
        }
        $mHtml .= '</select>';
        return $mHtml;
    }

    protected function ShowDestinNew($_AJAX, $mData = NULL) {
        $readonly = '';
        if ($mData != NULL) {
            if ($mData['fec_findes'] != '0000-00-00 00:00:00' && $mData['fec_findes'] != '') {
                $readonly = ' disabled ';
            }
        }

        if ($_AJAX['counter'] == '') {
            $_AJAX['counter'] = 0;
        }

        $_AJAX['cod_transp'] = '860068121';

        $style = $_AJAX['counter'] % 2 == 0 ? 'cellInfo1' : 'cellInfo2';

        $ciudad = $this->getCiudad();
        $genera = $this->getGenera($_AJAX['cod_transp']);


        $mHtml .= '
    <script>
      $(function() {
        
        $( ".date" ).datepicker({ minDate: new Date(' . (date('Y')) . ',' . (date('m') - 1) . ',' . (date('d')) . ') });
        
        $( ".time" ).timepicker();
        
        $.mask.definitions["A"]="[12]";
        $.mask.definitions["M"]="[01]";
        $.mask.definitions["D"]="[0123]";

        $.mask.definitions["H"]="[012]";
        $.mask.definitions["N"]="[012345]";
        $.mask.definitions["n"]="[0123456789]";

        $( ".date" ).mask("Annn-Mn-Dn");
        $( ".time" ).mask("Hn:Nn:Nn");

      });
    </script>';

        $mHtml .= '<div id="datdes' . $_AJAX['counter'] . 'ID">';
        $mHtml .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
        $mHtml .= '<tr>';
        $mHtml .= '<td align="left" colspan="5" class="cellHead" width="10%">DESTINATARIO No. ' . ( $_AJAX['counter'] + 1 );
        /*if ($readonly == '') {
            $mHtml .= '&nbsp;&nbsp;&nbsp;<a style="color:#FFFFFF; text-decoration:none; cursor:pointer;" onclick="DropGrid(\'' . $_AJAX['counter'] . '\');">[Eliminar]</a>';
        }*/
        $mHtml .= '</td>';
        $mHtml .= '</tr>';

        $mHtml .= '</table>';

        $mHtml .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';

        $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead" width="15%">DESTINATARIO</td>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">* CIUDAD</td>';
        $mHtml .= '<td align="center" class="cellHead" width="30%">DIRECCI&Oacute;N</td>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">NUMERO CONTACTO</td>';
        $mHtml .= '<td align="center" class="cellHead" width="15%">* FECHA CITA DESCARGUE</td>';
        $mHtml .= '<td align="center" class="cellHead" width="15%">* HORA CITA DESCARGUE</td>';
        $mHtml .= '</tr>';

        $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="' . $style . '"><input type="text" ' . $readonly . ' size="20" name="nom_destin' . $_AJAX['counter'] . '" id="nom_destin' . $_AJAX['counter'] . 'ID" value="' . $mData['nom_destin'] . '" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="' . $style . '">' . $this->GenerateSelect($ciudad, 'cod_ciudad' . $_AJAX['counter'], $mData['cod_ciudad'], $readonly) . '</td>';
        $mHtml .= '<td align="center" class="' . $style . '"><input type="text" ' . $readonly . ' size="40" name="dir_destin' . $_AJAX['counter'] . '" value="' . $mData['dir_destin'] . '" id="dir_destin' . $_AJAX['counter'] . 'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="' . $style . '"><input type="text" ' . $readonly . ' size="15" name="nom_contac' . $_AJAX['counter'] . '" value="' . $mData['num_destin'] . '" id="nom_contac' . $_AJAX['counter'] . 'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="' . $style . '"><input type="text" ' . $readonly . ' name="fec_citdes' . $_AJAX['counter'] . '" value="' . $mData['fec_citdes'] . '" id="fec_citdes' . $_AJAX['counter'] . 'ID" class="date" size="20" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="' . $style . '"><input type="text" ' . $readonly . ' name="hor_citdes' . $_AJAX['counter'] . '" value="' . $mData['hor_citdes'] . '" id="hor_citcar' . $_AJAX['counter'] . 'ID" class="time" size="15" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '</tr>';


        $mHtml .= '<tr>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">GENERADOR</td>';
        $mHtml .= '<td align="center" class="cellHead" width="15%">N° factura de remision</td>';
        $mHtml .= '<td align="center" class="cellHead" width="20%">* Doc. Alterno</td>';
        $mHtml .= '<td align="center" class="cellHead" width="20%"> <a onclick="addrow(' . $_AJAX['counter'] . ');">[+]</a></td>';
        $mHtml .= '</tr>';
        ////AQUI VIENE UN MAMAFOKA SELECT PARA LA LISTA
        # Datos Destinatari del webservice -------------------------------------------------
        if ($mData != null) {


            $mQuery = "(SELECT a.num_docume, a.num_docalt
                  FROM " . BASE_DATOS . ".tab_despac_cordes a, 
                       " . BASE_DATOS . ".tab_despac_despac b 
                 WHERE a.num_despac = b.cod_manifi AND
                       b.num_despac = '" . $_AJAX['num_despac'] . "'
                      AND a.nom_destin = '" . $mData['nom_destin'] . "')
                      UNION(
                      SELECT a.num_docume, a.num_docalt
                  FROM " . BASE_DATOS . ".tab_despac_destin a, 
                       " . BASE_DATOS . ".tab_despac_despac b 
                 WHERE a.num_despac = b.num_despac AND
                       b.num_despac = '" . $_AJAX['num_despac'] . "'
                       AND a.nom_destin = '" . $mData['nom_destin'] . "'
                            )";



            $consulta = new Consulta($mQuery, $this->conexion);
            $mDestinFact = $consulta->ret_matriz();


            foreach ($mDestinFact as $key => $value) {
                $mHtml .= '<tr>';
                $mHtml .= '<td align="center" class="cellInfo1"><label id="cod_generaList" value="860068121">CORONA L&T</label></td>';
                $mHtml .= '<td align="center" class="cellInfo1"><input type="text" ' . $readonly . ' size="20" name="num_factura' . $_AJAX['counter'] . '" id="num_factura' . $_AJAX['counter'] . 'ID" value="' . $value["num_docume"] . '" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
                $mHtml .= '<td align="center" class="cellInfo1"><input type="text" ' . $readonly . ' size="20" name="doc_alterna' . $_AJAX['counter'] . '" id="doc_alterna' . $_AJAX['counter'] . 'ID" value="' . $value["num_docalt"] . '" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
                $mHtml .= '</tr>';
            }
        } else {
            $mHtml .= '<tr id="divCeldas' . $_AJAX['counter'] . '">';
            $mHtml .= '<td align="center" class="cellInfo1"><label id="cod_generaList" value="860068121">CORONA L&T</label></td>';
            $mHtml .= '<td align="center" class="cellInfo1"><input type="text" ' . $readonly . ' size="20" name="num_factura' . $_AJAX['counter'] . '" id="num_factura' . $_AJAX['counter'] . 'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
            $mHtml .= '<td align="center" class="cellInfo1"><input type="text" ' . $readonly . ' size="20" name="doc_alterna' . $_AJAX['counter'] . '" id="doc_alterna' . $_AJAX['counter'] . 'ID" onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
            $mHtml .= '</tr>';
        }


        $mHtml .= '</table><br>';

        $mHtml .= '<input type="hidden" id="hiddenIdGenera" value= "' . $mData['cod_genera'] . '">';
        $mHtml .= '<input type="hidden" id="hiddenIdTransp" value= "' . $_AJAX['cod_transp'] . '">';
        $mHtml .= '</div>';
        if ($_AJAX['ind_ajax'] == '1') {
            echo $mHtml;
        } else {
            return $mHtml;
        }
    }

    protected function addRow() {

        $style = "cellInfo2";
        $readonly = ' enabled ';
        $genera = $this->getGenera($_REQUEST['cod_transp']);

        $mHtml = '<tr>';
        $mHtml .= '<td align="center" class="' . $style . '"><label id="cod_generaList" value="860068121">CORONA L&T</label></td>';
        /*$mHtml .= '<td align="center" class="' . $style . '">' . $this->GenerateSelect($genera, 'cod_generaList' . $_REQUEST['counter'], $_REQUEST['cod_genera'], $readonly) . '</td>';
        */$mHtml .= '<td align="center" class="' . $style . '"><input type="text" ' . $readonly . ' size="20" name="num_factura' . $_REQUEST['counter'] . '" id="num_factura' . $_REQUEST['counter'] . 'ID"  onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';
        $mHtml .= '<td align="center" class="' . $style . '"><input type="text" ' . $readonly . ' size="20" name="doc_alterna' . $_REQUEST['counter'] . '" id="doc_alterna' . $_REQUEST['counter'] . 'ID"  onfocus="this.className=\'campo_texto_on\'" onblur="this.className=\'campo_texto\'" /></td>';

        $mHtml .= '</tr>';

        echo $mHtml;
    }

    protected function mainList($_AJAX) {
        echo "<link rel=\"stylesheet\" href=\"../" . $_AJAX['Standa'] . "/estilos/dinamic_list.css\" type=\"text/css\">";
        echo "<script language=\"JavaScript\" src=\"../" . $_AJAX['Standa'] . "/js/dinamic_list.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . $_AJAX['Standa'] . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . $_AJAX['Standa'] . "/js/functions.js\"></script>\n";

        if ($_SESSION['datos_usuario']['cod_perfil'] == '712') {
            $mSql = "SELECT * 
                   FROM (
                    SELECT a.num_despac, IF( z.num_desext IS NOT NULL , z.num_desext,  'N/A' ) as num_desext , b.num_placax, a.cod_manifi, a.fec_despac, c.nom_tipdes, d.nom_ciudad AS nom_ciuori, e.nom_ciudad AS nom_ciudes
                      FROM satt_faro.tab_despac_despac a
                 LEFT JOIN satt_faro.tab_despac_sisext z ON a.num_despac = z.num_despac
                 LEFT JOIN satt_faro.tab_despac_destin f ON a.num_despac = f.num_despac, satt_faro.tab_despac_vehige b, satt_faro.tab_genera_tipdes c, satt_faro.tab_genera_ciudad d, satt_faro.tab_genera_ciudad e
                     WHERE a.cod_tipdes = c.cod_tipdes
                       AND a.cod_ciuori = d.cod_ciudad
                       AND a.cod_ciudes = e.cod_ciudad
                       AND a.num_despac = b.num_despac
                       AND b.cod_transp =  '860068121'
                       AND a.fec_llegad IS NULL 
                       AND a.ind_anulad !=  'A'
                       AND a.fec_despac BETWEEN  '" . $_AJAX['fec_inicia'] . " 00:00:00' AND '" . $_AJAX['fec_finali'] . " 23:59:59'
                      ORDER BY 1 , f.ind_modifi DESC
                      ) AS w WHERE 1 = 1
              GROUP BY w.num_despac";

            $_SESSION["queryXLS"] = $mSql;
            $list = new DinamicList($this->conexion, $mSql, 1);
            $list->SetClose('no');
            $list->SetHeader("Despacho", "field:num_despac; type:link; onclick:SetDestinatarios( $(this) )");
            $list->SetHeader("No. Viaje", "field:num_desext"); 
            $list->SetHeader("Placa", "field:num_placax");
            $list->SetHeader("Manifiesto", "field:cod_manifi");
            $list->SetHeader("Fecha", "field:fec_despac");
            $list->SetHeader("Tipo Despacho", "field:nom_tipdes");
            $list->SetHeader("Origen", "field:nom_ciuori");
            $list->SetHeader("Destino", "field:nom_ciudes");

        } else {

            $mSql = "SELECT * 
                   FROM (
                    SELECT a.num_despac, IF( z.num_desext IS NOT NULL , z.num_desext,  'N/A' ) as num_desext , IF( f.ind_modifi =  '1', 'ACTUALIZADO', 'PENDIENTE' ) as ind_modifi, b.num_placax, a.cod_manifi, a.fec_despac, c.nom_tipdes, d.nom_ciudad AS nom_ciuori, e.nom_ciudad AS nom_ciudes
                      FROM satt_faro.tab_despac_despac a
                 LEFT JOIN satt_faro.tab_despac_sisext z ON a.num_despac = z.num_despac
                 LEFT JOIN satt_faro.tab_despac_destin f ON a.num_despac = f.num_despac, satt_faro.tab_despac_vehige b, satt_faro.tab_genera_tipdes c, satt_faro.tab_genera_ciudad d, satt_faro.tab_genera_ciudad e
                     WHERE a.cod_tipdes = c.cod_tipdes
                       AND a.cod_ciuori = d.cod_ciudad
                       AND a.cod_ciudes = e.cod_ciudad
                       AND a.num_despac = b.num_despac
                       AND b.cod_transp =  '860068121'
                       AND a.fec_llegad IS NULL 
                       AND a.ind_anulad !=  'A'
                       AND a.fec_despac BETWEEN  '" . $_AJAX['fec_inicia'] . " 00:00:00' AND '" . $_AJAX['fec_finali'] . " 23:59:59'
                      ORDER BY 1 , f.ind_modifi DESC
                      ) AS w WHERE 1 = 1
                  GROUP BY w.num_despac";

            $_SESSION["queryXLS"] = $mSql;
            $list = new DinamicList($this->conexion, $mSql, 1);
            $list->SetClose('no');
            $list->SetHeader("Despacho", "field:num_despac; type:link; onclick:SetDestinatarios( $(this) )");
            $list->SetHeader("No. Viaje", "field:num_desext");
            $list->SetHeader("Estado", "field:ind_modifi; width:1%", array_merge($this->ind_estado_, $this->ind_estado));
            $list->SetHeader("Placa", "field:num_placax");
            $list->SetHeader("Manifiesto", "field:cod_manifi");
            $list->SetHeader("Fecha", "field:fec_despac");
            $list->SetHeader("Tipo Despacho", "field:nom_tipdes");
            $list->SetHeader("Origen", "field:nom_ciuori");
            $list->SetHeader("Destino", "field:nom_ciudes");
        }
         
 



        $list->Display($this->conexion);

        $_SESSION["DINAMIC_LIST"] = $list;
        echo "<td>";
        echo $list->GetHtml();
        echo "</td>";
    }

}

$proceso = new AjaxDespacDestin();
?>