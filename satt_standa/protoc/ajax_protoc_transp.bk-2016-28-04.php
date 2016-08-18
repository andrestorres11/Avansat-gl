<?php

class AjaxTranspProtoc {

    var $conexion;

    public function __construct() {
        $_AJAX = $_REQUEST;
        include_once('../lib/ajax.inc');
        include_once('../lib/general/constantes.inc');
        $this->conexion = $AjaxConnection;
        $this->$_AJAX['option']($_AJAX);
    }

    protected function verifyAgenci($_AJAX){
  
        $mQuery = "SELECT a.ind_notage 
                     FROM ".BASE_DATOS.".tab_transp_tipser a 
                    WHERE a.cod_transp = '".$_AJAX['cod_transp']."'
                      AND a.ind_notage = 1"   ; 

        $consulta = new Consulta($mQuery, $this->conexion);
        $mIndAgen = $consulta -> ret_matriz();
  
        return $mIndAgen[0][0];
    }

    protected function ShowFormContac($_AJAX) {
  
 
        $mIndAgen = $this -> verifyAgenci($_AJAX);

        $mQueryAgenci = "SELECT a.cod_agenci, a.nom_agenci 
                           FROM ".BASE_DATOS.".tab_genera_agenci a 
                     INNER JOIN ".BASE_DATOS.".tab_transp_agenci b
                             ON a.cod_agenci = b. cod_agenci
                          WHERE b.cod_transp = '".$_AJAX['cod_transp']."'" ;

        $consulta = new Consulta($mQueryAgenci, $this->conexion);
        $mAgencias = $consulta->ret_matrix("a");

        $temp = array('0' => array('cod_agenci' => '', 'nom_agenci' => '---' ) );
        $mAgencias = array_merge($temp, $mAgencias);

        $mHtml = "<div id='divPopup' class='cellInfo1' width='100%'>";
        $mHtml .= "<table id='tableData'>";
        $mHtml .= "<thead>";
        $mHtml .= "<tr>";
        $mHtml .= "<th><Label class='cellInfo1'>Contacto Principal</label></th>";
        $mHtml .= "<th><Label class='cellInfo1'>Celular</label></th>";
        $mHtml .= "<th><Label class='cellInfo1'>Cargo</label></th>";
        $mHtml .= "<th><Label class='cellInfo1'>E-mail</label></th>"; 

        if($mIndAgen)
        $mHtml .= "<th><Label class='cellInfo1'>Agencia</label></th>"; 

        $mHtml .= "</thead>";
        $mHtml .= "<tbody>";
        $mHtml .= "<tr>";
        $mHtml .= "<th><input type = 'text' id='pop_contacID' ></th>";
        $mHtml .= "<th><input type = 'text' id='pop_celtacID' ></th>";
        $mHtml .= "<th><input type = 'text' id='pop_cargoxID' ></th>";
        $mHtml .= "<th><input type = 'text' id='pop_emailxID' ></th>"; 

        if($mIndAgen){
        $mHtml .= "<th><select id='cod_agenci'>";

            foreach ($mAgencias as $row) {
                $mHtml .= "<option value='".$row['cod_agenci']."'>".$row['nom_agenci']."</option>";
            }

            $mHtml .= "</select></th>"; 
        }
        $mHtml .= "</tbody>";


        $mHtml .= "<tfoot>";
        $mHtml .= "</tfoot>";


        $mHtml .= "</table>";
        $mHtml .= "</div>";

        echo $mHtml;
    }

    protected function FillFormContac($_AJAX) {
 
        $mIndAgen = $this -> verifyAgenci($_AJAX);

        $mQueryAgenci = "SELECT a.cod_agenci, a.nom_agenci 
                           FROM ".BASE_DATOS.".tab_genera_agenci a 
                     INNER JOIN ".BASE_DATOS.".tab_transp_agenci b
                             ON a.cod_agenci = b. cod_agenci
                          WHERE b.cod_agenci = '".$_AJAX['cod_agenci']."'" ;

        $consulta = new Consulta($mQueryAgenci, $this->conexion);
        $mAgencias = $consulta->ret_matrix("a");
  
        $mHtml .= "<tr>";
        $mHtml .= "<th><input type = 'text' id='res_contacID' disabled size='25' value = '" . $_REQUEST['nom_contac'] . "'></th>";
        $mHtml .= "<th><input type = 'text' id='res_celtacID' disabled size='25' value = '" . $_REQUEST['num_telmov'] . "'></th>";
        $mHtml .= "<th><input type = 'text' id='res_cargoxID' disabled size='25' value = '" . $_REQUEST['nom_cargox'] . "'></th>";
        $mHtml .= "<th><input type = 'text' id='res_emailxID' disabled size='25' value = '" . $_REQUEST['dir_correo'] . "'></th>";
        
        if($mIndAgen){
            $mHtml .= "<th><input type = 'text' id='nom_agenciID' disabled size='25' value = '" . $mAgencias[0]['nom_agenci'] . "'></th>";
            $mHtml .= "<th><input type = 'hidden' id='cod_agenciID' disabled size='25' value = '" . $mAgencias[0]['cod_agenci'] . "'></th>";
        }

        $mHtml .= '<th><img src="../' . DIR_APLICA_CENTRAL . '/imagenes/error.gif" border="0" aling="left" onclick="deleterow(this);"></th>';
        $mHtml .= "</tr>";

        echo $mHtml;
    }

    protected function getNewRow($_AJAX) {

        $cod_noveda = $_AJAX['cod_noveda'];
  
        $AllProtocols = $this->getAllProtocols();
        $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="1">';
        $mHtml .= '<tr>';
        $mHtml .= '<td colspan="3" class="cellInfo1" >PROTOCOLOS</td>';
        $mHtml .= '</tr>';

        $mHtml .= '<tr>';
        $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="45%" align="center">' . $this->multipleSelect('all_protoc' . $cod_noveda, $AllProtocols, 'AsignaProtocolo(' . $cod_noveda . ');') . '</td>';
        $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="10%" align="center"><br><br><input type="button" onclick="AsignaProtocolo(' . $cod_noveda . ');" value=">>" /><br><br><input type="button" onclick="DerogaProtocolo(' . $cod_noveda . ');" value="<<"/></td>';
        $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="45%" align="center">' . $this->multipleSelect('asi_protoc-' . $cod_noveda, NULL, 'DerogaProtocolo(' . $cod_noveda . ');', "item") . '</td>';
        $mHtml .= '<td>';
        $mHtml .= '<Label style="color:white;">Notifica a contactos</label> ';
        $mHtml .= '<input type="radio" name="ver_emailx' . $cod_noveda . '" id="ver_email1ID" value="1" checked><Label  style="color:white;"> SI</label>';
        $mHtml .= '<input type="radio" name="ver_emailx' . $cod_noveda . '" id="ver_email2ID" value="0"><Label  style="color:white;"> NO</label>';
        $mHtml .= '</td>';
        $mHtml .= '</tr>';

        $mHtml .= '</table>';
        echo $mHtml;
    }

    protected function ShowMainList($_AJAX) {
        $_NOVEDA = $this->getNovedaProtoco($_AJAX['cod_transp']);

        $mIndAgen = $this -> verifyAgenci($_AJAX);

        $this->Style();
        echo '<script>
          $(function() {
            $( "#tabs" ).accordion({
              collapsible:true,
              active: false
            });
          });
          </script>';

        $mHtml = "";

        $mHtml .= "<hr>";

        $mHtml .= "</div>";

        $mHtml .= "<th><Label style='background:#009900; color:#ffffff; padding:2px 85% 2px 5px;' colspan='3'>Contactos Asignados  <a onclick='ShowFormContac();' style=' color:#ffffff;'>[agregar]</a></Label>";

        $mHtml .= "<div  class='cellInfo1' id='contacts_copy'>";

        $mHtml .= "<table id='tableData'>";
        $mHtml .= "<thead>";
        $mHtml .= "<tr>";
        $mHtml .= "<th><Label class='cellInfo1'>Contacto Principal</label></th>";
        $mHtml .= "<th><Label class='cellInfo1'>Celular</label></th>";
        $mHtml .= "<th><Label class='cellInfo1'>Cargo</label></th>";
        $mHtml .= "<th><Label class='cellInfo1'>E-mail</label></th>"; 

        
        if($mIndAgen)
        $mHtml .= "<th><Label class='cellInfo1'>Agencia</label></th>"; 

        $mHtml .= "</thead>";

        $mHtml .= "<tbody id='tableDataBody'>";

        if($mIndAgen)
        {

          $sqlContacts = "SELECT a.nom_contac, a.dir_correo, a.num_telmov, a.nom_cargox , b.nom_agenci, b.cod_agenci "
                  . "FROM ".BASE_DATOS.".tab_contac_protoc a " 
            . "INNER JOIN ".BASE_DATOS.".tab_genera_agenci b " 
                   . " ON a.cod_agenci = b.cod_agenci "
                 . "WHERE a.cod_transp = '" . $_AJAX['cod_transp'] . "'";
        }
        else
        {
          $sqlContacts = "SELECT a.nom_contac, a.dir_correo, a.num_telmov, a.nom_cargox "
                  . "FROM ".BASE_DATOS.".tab_contac_protoc a "  
                 . "WHERE a.cod_transp = '" . $_AJAX['cod_transp'] . "'";
        }
 
         

        $consulta = new Consulta($sqlContacts, $this->conexion, "BR");
        $sqlContacts = $consulta->ret_matriz();
  
        foreach ($sqlContacts as $value) {


            $mHtml .= "<tr>";
            $mHtml .= "<th><input type = 'text' id='res_contacID' disabled size='25' value = '" . $value['nom_contac'] . "'   '></th>";
            $mHtml .= "<th><input type = 'text' id='res_celtacID' disabled size='25' value = '" . $value['num_telmov'] . "'   '></th>";
            $mHtml .= "<th><input type = 'text' id='res_cargoxID' disabled size='25' value = '" . $value['nom_cargox'] . "'   '></th>";
            $mHtml .= "<th><input type = 'text' id='res_emailxID' disabled size='25' value = '" . $value['dir_correo'] . "'   '></th>";

            if($mIndAgen){
                $mHtml .= "<th><input type = 'text' id='nom_agenciID' disabled size='25' value = '" . $value['nom_agenci'] . "'></th>";
                $mHtml .= "<th><input type = 'hidden' id='cod_agenciID' disabled size='25' value = '" . $value['cod_agenci'] . "'></th>";
            }

            $mHtml .= '<th><img src="../' . DIR_APLICA_CENTRAL . '/imagenes/error.gif" border="0" aling="left" onclick="deleterow(this);"></th>';
            $mHtml .= "</tr>";
        }

        $mHtml .= "</tbody>";


        $mHtml .= "<tfoot>";
        $mHtml .= "</tfoot>";


        $mHtml .= "</table>";

        $mHtml .= "</div>";


        $mHtml .= '<label for="textID" style="font-size:12px; font-family:Trebuchet MS, Verdana, Arial;">Listado de Novedades con Protocolos asociados, asignadas a la Transportadora<br>Si desea agregar una nueva novedad, haga click <a onclick="NewNovedad();" href="#" style="color:#285C00; text-decoration:none; cursor:pointer;" >aqu&iacute;</a><br>&nbsp;</label>';


        $mHtml .= '<div id="tabs" width="100%">';

        foreach ($_NOVEDA as $cod_noveda => $des_noveda) {
            $mHtml .= '<div>&nbsp;<br>' . $cod_noveda . ' - ' . $des_noveda['nombre'] . '<img id="iconoBasura" src="../'.DIR_APLICA_CENTRAL.'/imagenes/rubbish.png" style="float:right;  padding-right: 10px;" onclick="deleteProtoc('.$_AJAX['cod_transp'].', '.$cod_noveda.')"><br>&nbsp;</div>';

            $_PROTOC = $des_noveda['protoc'];
            $ActiveProtocols = array();
            $_A_P = array();
            $i = 0;

            foreach ($_PROTOC as $cod_protoc => $nom_protoc) {
                $_A_P[] = $cod_protoc;
                $ActiveProtocols[$i][0] = $cod_protoc;
                $ActiveProtocols[$i][1] = $nom_protoc;
                $i++;
            }

            $mSqlIndEmail = "SELECT a.ind_notema "
                    . "FROM tab_noveda_protoc a "
                    . "WHERE a.cod_noveda = '" . $cod_noveda . "' "
                    . "AND a.cod_transp  = '" . $_AJAX['cod_transp'] . "' ";


            $consulta = new Consulta($mSqlIndEmail, $this->conexion, "BR");
            $IndEmail = $consulta->ret_matriz();

            $AllProtocols = $this->getAllProtocols($_A_P);

            $mHtml .= '<div id="tabs-' . $cod_noveda . '">';
            $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="1">';
            $mHtml .= '<tr>';
            $mHtml .= '<td colspan="3" class="cellInfo1" >PROTOCOLOS</td>';
            $mHtml .= '</tr>';

            $mHtml .= '<tr>';
            $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="45%" align="center">' . $this->multipleSelect('all_protoc' . $cod_noveda, $AllProtocols, 'AsignaProtocolo(' . $cod_noveda . ');') . '</td>';
            $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="10%" align="center"><br><br><input type="button" onclick="AsignaProtocolo(' . $cod_noveda . ');" value=">>" /><br><br><input type="button" onclick="DerogaProtocolo(' . $cod_noveda . ');" value="<<"/></td>';
            $mHtml .= '<td class="cellInfo2" style="padding-top:10px; padding-bottom:10px;" width="45%" align="center">' . $this->multipleSelect('asi_protoc-' . $cod_noveda, $ActiveProtocols, 'DerogaProtocolo(' . $cod_noveda . ');', "item") . '</td>';
            $mHtml .= '<td>';
            $mHtml .= '<Label  style="color:white;">Notifica a contactos</label>';
            $mHtml .= '<input type="radio" name="ver_emailx' . $cod_noveda . '" id="ver_email1ID" value="1" '.($IndEmail[0][0] == 1 ? "checked" : "").'><Label  style="color:white;"> SI</label>';
            $mHtml .= '<input type="radio" name="ver_emailx' . $cod_noveda . '" id="ver_email2ID" value="0" '.($IndEmail[0][0] == 0 ? "checked" : "").'><Label  style="color:white;"> NO</label>';
            $mHtml .= '</td>';
            $mHtml .= '</tr>';

            $mHtml .= '</table>';
            $mHtml .= '</div>';
        }
  
        $mHtml .= '</div>';

        $mHtml .= "<br><input class='crmButton small save' style='cursor:pointer;' type='button' value='Guardar' onclick='SaveAllProtocols();'/>";


        echo $mHtml;
    }

    private function listContac() {
        
    }

    private function deleteProtoc(){


        $mDelete = "DELETE FROM " . BASE_DATOS . ".tab_noveda_protoc 
                          WHERE cod_transp = '" . $_REQUEST['cod_transp'] . "'
                            AND cod_noveda = '" . $_REQUEST['cod_noveda'] . "'";



        if ($consulta = new Consulta($mDelete, $this->conexion, "R")){

            $mHtml .= '<center><table width="100%" cellspacing="2px" cellpadding="0">';
            $mHtml .= '<tr>';
            $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >La Informaci&oacute;n ha sido Registrada Exitosamente.</i></td>';
            $mHtml .= '</tr>';
            $mHtml .= '</table></center>';
            echo $mHtml;;
        }
        else{

            $mHtml .= '<center><table width="100%" cellspacing="2px" cellpadding="0">'; 
            $mHtml .= '<tr>';
            $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #E31010;" >La Informaci&oacute;n No se ha sido Registrada Exitosamente.</i></td>';
            $mHtml .= '</tr>';
            $mHtml .= '</table></center>';
            echo $mHtml;
        }



    }

    protected function SaveAllProtocols($_AJAX) {
  
        $arr_nombre = explode("|", $_REQUEST['nom_contacs']);
        $arr_telmov = explode("|", $_REQUEST['num_telmovs']);
        $arr_cargos = explode("|", $_REQUEST['nom_cargosx']);
        $dir_correo = explode("|", $_REQUEST['dir_correos']);
        $ver_emailx = explode("|", $_REQUEST['ver_emailxx']);
        $nov_emailx = explode("|", $_REQUEST['nov_emailxx']);
        $cod_agenci = explode("|", $_REQUEST['cod_agenci']);
 
        for ($i = 0; $i < sizeof($nov_emailx); $i++) {
            $val = explode('¬', $nov_emailx[$i]);
            $mArrayInd[$i] = $val[0];
            $mArrayNov[$i] = $val[1];
        }

        $mSelect = "SELECT cod_transp, cod_noveda, cod_protoc 
               FROM " . BASE_DATOS . ".tab_noveda_protoc 
              WHERE cod_transp = '" . $_AJAX['cod_transp'] . "'";


        $consulta = new Consulta($mSelect, $this->conexion, "BR");
        $ant_protoc = $consulta->ret_matriz();

        if (sizeof($ant_protoc) > 0) {



            $mSelect = "SELECT MAX( num_consec ) AS num_consec 
                 FROM " . BASE_DATOS . ".tab_bitaco_novpro 
                WHERE cod_transp = '" . $_AJAX['cod_transp'] . "'";

            $consulta = new Consulta($mSelect, $this->conexion, "R");
            $ant_consec = $consulta->ret_matriz();

            $nue_consec = sizeof($ant_protoc) > 0 ? $ant_consec[0][0] + 1 : 1;

            $l = 0;
            foreach ($ant_protoc as $row) {
                $mInsert = "INSERT INTO " . BASE_DATOS . ".tab_bitaco_novpro
                              ( num_consec, cod_transp, cod_noveda, 
                                cod_protoc, ind_notema, usr_modifi,
                                fec_modifi )
                        VALUES( '" . $nue_consec . "', '" . $row['cod_transp'] . "', '" . $row['cod_noveda'] . "',
                                '" . $row['cod_protoc'] . "',  '" . $mArrayInd[$l] . "', '" . $_SESSION['datos_usuario']['cod_usuari'] . "', NOW() )";

                $consulta = new Consulta($mInsert, $this->conexion, "R");


                $l++;
            }
        }

        $mDelete = "DELETE FROM " . BASE_DATOS . ".tab_noveda_protoc 
                WHERE cod_transp = '" . $_AJAX['cod_transp'] . "'";

        $consulta = new Consulta($mDelete, $this->conexion, "R");

        $mDelete2 = "DELETE FROM " . BASE_DATOS . ".tab_contac_protoc 
                WHERE cod_transp = '" . $_AJAX['cod_transp'] . "'";

        $consulta = new Consulta($mDelete2, $this->conexion, "C");
  
        $k = 0;

        if (sizeof($_AJAX['noveda']) > 0) {
            foreach ($_AJAX['noveda'] as $cod_noveda => $protoco) {

                if (is_array($protoco)) {
                    foreach ($protoco as $cod_protoco) {
                        $mInsert = "INSERT INTO " . BASE_DATOS . ".tab_noveda_protoc
                                  ( cod_transp, cod_noveda, ind_notema,
                                    cod_protoc, usr_creaci, fec_creaci
                                    )
                            VALUES( '" . $_AJAX['cod_transp'] . "', '" . $cod_noveda . "','" . $mArrayInd[$k] . "',
                                    '" . $cod_protoco . "', '" . $_SESSION['datos_usuario']['cod_usuari'] . "', NOW()
                                   )";

                        $consulta = new Consulta($mInsert, $this->conexion, "R");


                        //proceso para la insersion en la tabla tab_contac_protoc
                        //que tiene los contactos a los que se les envia un correo para la identificacion 
                        //de los protocolos 


                        $k++;
                    }
                     
                }
            }
        }

        for ($i = 0; $i < $_AJAX['num_contact']; $i++) {


            $mInsertCorreo[] = array("cod_noveda" => $cod_noveda, "cod_protoc" => $cod_protoco, "cod_transp" => $_AJAX['cod_transp'],
                "nom_nombre" => $arr_nombre[$i], "dir_correo" => $dir_correo[$i], "num_telmov" => $arr_telmov[$i],
                "nom_cargos" => $arr_cargos[$i], "ind_tipcor" => "0", "cod_agenci" => $cod_agenci[$i]);
        }
 

        $counter = 1;
  
        foreach ($mInsertCorreo as $key) {
  
            $mInsertContac = "INSERT INTO " . BASE_DATOS . ". tab_contac_protoc
                                 (cod_consec, cod_noveda, cod_protoc, cod_transp,
                                  nom_contac, dir_correo, num_telmov,
                                  nom_cargox, ind_tipcor, cod_agenci,
                                  usr_creaci, fec_creaci
                                  ) VALUES 
                                  (
                                    '" . ($counter++) . "' , 
                                    '" . $key["cod_noveda"] . "',
                                    '" . $key["cod_protoc"] . "',
                                    '" . $key['cod_transp'] . "',
                                    '" . $key['nom_nombre'] . "' ,
                                    '" . $key['dir_correo'] . "' ,
                                    '" . $key['num_telmov'] . "' ,
                                    '" . $key['nom_cargos'] . "' ,
                                    '" . $key['ind_tipcor'] . "' ,                                     
                                    '" . $key['cod_agenci'] . "' ,                                     
                                    '" . $_SESSION['datos_usuario']['cod_usuari'] . "',
                                     NOW())";
  
            $consulta = new Consulta($mInsertContac, $this->conexion, "R");
        }
        if ($insercion = new Consulta("COMMIT", $this->conexion)) {
            $mHtml .= '<center><table width="100%" cellspacing="2px" cellpadding="0">';

            $mHtml .= '<tr>';
            $mHtml .= '<td colspan="4" style=" text-align:center;border-bottom:1px solid #000000; font-family:Trebuchet MS, Verdana, Arial; font-size:15px; font-weight:bold; padding-bottom:5px; color: #285C00;" >La Informaci&oacute;n ha sido Registrada Exitosamente.</i></td>';
            $mHtml .= '</tr>';
            $mHtml .= '</table></center>';
            echo $mHtml;
        }
    }

    protected function NewNovedad($_AJAX) {
        // echo "<pre>";
        // print_r(  $_AJAX  );
        // echo "</pre>";
        // echo "-> ".getcwd();
        // $this -> Style();
        echo '<script>$( "#novedaID" ).autocomplete({
            source: "../' . $_AJAX['standa'] . '/protoc/ajax_protoc_transp.php?option=getNoveda",
            minLength: 1, 
            delay: 100
          });</script>';

        $mHtml .= '<div class="StyleDIV" id="FormNovedaID">';
        $mHtml .= '<table width="100%" border="0" cellpadding="0" cellspacing="1">';

        $mHtml .= '<tr>';
        $mHtml .= '<td class="TRform" align="right">Novedad:&nbsp;&nbsp;</td>';
        $mHtml .= '<td class="TRform" align="left"><input type="text" id="novedaID" name="noveda" size="50"/></td>';


        $mHtml .= '</tr>';

        $mHtml .= '</table>';
        $mHtml .= '</div>';

        echo $mHtml;
    }

    private function getAllProtocols($notinarray = NULL) {
        // echo "<pre>";
        // print_r( $notinarray );
        // echo "</pre>";
        $mSelect = "SELECT cod_protoc, des_protoc 
                  FROM " . BASE_DATOS . ".tab_genera_protoc 
                 WHERE ind_activo = '1' ";

        if ($notinarray != NULL)
            $mSelect .= " AND cod_protoc NOT IN( " . implode(',', $notinarray) . " ) ";

        $mSelect .= " ORDER BY 2";
        $consulta = new Consulta($mSelect, $this->conexion);
        return $consulta->ret_matriz();
    }

    private function multipleSelect($name, $protoc = NULL, $dbclick = NULL, $class = NULL) {
        $mHtml = '';
        $mHtml .= '<select style="width:75%;" name="' . $name . '" id="' . $name . '-ID" size="8" multiple';
        if ($dbclick) {
            $mHtml .= ' ondblclick="' . $dbclick . '"';
        }
        if ($class) {
            $mHtml .= ' class="' . $class . '"';
        }
        $mHtml .= ' >';

        if ($protoc != NULL) {
            foreach ($protoc as $row) {
                $mHtml .= '<option value="' . $row[0] . '">' . utf8_decode($row[1]) . '</option>';
            }
        }
        $mHtml .= '</select>';
        return $mHtml;
    }

    private function getNovedaProtoco($cod_transp) {
        $_PROTOC = array();
        $mSelect = " SELECT c.cod_protoc, c.des_protoc, b.cod_noveda, 
                        b.nom_noveda, a.cod_transp
                   FROM " . BASE_DATOS . ".tab_noveda_protoc a, 
                        " . BASE_DATOS . ".tab_genera_noveda b, 
                        " . BASE_DATOS . ".tab_genera_protoc c
                  WHERE a.cod_noveda = b.cod_noveda 
                    AND a.cod_protoc = c.cod_protoc 
                    AND c.ind_activo = '1'
                    AND a.cod_transp = '" . $cod_transp . "'";

        $consulta = new Consulta($mSelect, $this->conexion);
        $_NOVEDA = $consulta->ret_matriz();

        foreach ($_NOVEDA as $noveda) {
            $_PROTOC[$noveda['cod_noveda']]['nombre'] = $noveda['nom_noveda'];
            $_PROTOC[$noveda['cod_noveda']]['protoc'][$noveda['cod_protoc']] = $noveda['des_protoc'];
        }

        return $_PROTOC;
    }

    protected function ValidateTransp($_AJAX) {
        $mSql = "SELECT 1
               FROM " . BASE_DATOS . ".tab_tercer_tercer a,
                    " . BASE_DATOS . ".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = " . $_AJAX['filter'] . " AND
                    a.cod_tercer = '" . $_AJAX['cod_transp'] . "'";

        $consulta = new Consulta($mSql, $this->conexion);
        $transpor = $consulta->ret_matriz();
        if (sizeof($transpor) > 0)
            echo 'y';
        else
            echo 'n';
    }

    private function Style() {
        echo '
        <style>
        .ui-tabs-vertical { width: 75%; }
        .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
        .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
        .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
        .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
        .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 75%;}
        #tabs li .ui-icon-close { float: right; margin: 0.4em 0.2em 0 0; cursor: pointer; }
        .CellHead
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
            background-color: #FFFFFF;
            border: 1px solid rgb(201, 201, 201); 
            padding: 5px; width: 96%; 
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

    protected function getNoveda($_AJAX) {

        $mSelect = "SELECT cod_noveda,  CONCAT( CONVERT( nom_noveda USING utf8), 
						  '', if (nov_especi = '1', '(NE)', '' ), 
						  if( ind_alarma = 'S', '(GA)', '' ), 
						  if( ind_manala = '1', '(MA)', '' ),
						  if( ind_tiempo = '1', '(ST)', '' ) ) , 
						  ind_tiempo
				   FROM " . BASE_DATOS . ".tab_genera_noveda 
				   WHERE ind_visibl = '1' AND
                 ( CONCAT( CONVERT( nom_noveda USING utf8), 
                   '', if (nov_especi = '1', '(NE)', '' ), 
                   if( ind_alarma = 'S', '(GA)', '' ), 
                   if( ind_manala = '1', '(MA)', '' ),
                   if( ind_tiempo = '1', '(ST)', '' ) ) LIKE '%" . $_AJAX['term'] . "%' OR cod_noveda LIKE '%" . $_AJAX['term'] . "%') ";

        if ($_SESSION['datos_usuario']['cod_perfil'] != COD_PERFIL_SUPERUSR && $_SESSION['datos_usuario']['cod_perfil'] != COD_PERFIL_ADMINIST && $_SESSION['datos_usuario']['cod_perfil'] != COD_PERFIL_SUPEFARO)
            $mSelect .=" AND cod_noveda !='" . CONS_NOVEDA_ACAEMP . "' ";

        $mSelect .=" ORDER BY 2 ASC LIMIT 10";

        $consulta = new Consulta($mSelect, $this->conexion);
        $noveda = $consulta->ret_matriz();

        $data = array();
        for ($i = 0, $len = count($noveda); $i < $len; $i++) {
            $data [] = '{"label":"' . $noveda[$i][0] . ' - ' . utf8_encode($noveda[$i][1]) . '","value":"' . $noveda[$i][0] . ' - ' . utf8_encode($noveda[$i][1]) . '"}';
        }
        echo '[' . join(', ', $data) . ']';
    }

    protected function getTransp($_AJAX) {
        $mSql = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
               FROM " . BASE_DATOS . ".tab_tercer_tercer a,
                    " . BASE_DATOS . ".tab_tercer_activi b
              WHERE a.cod_tercer = b.cod_tercer AND
                    b.cod_activi = " . $_AJAX['filter'] . " AND
                    CONCAT( a.cod_tercer ,' - ', UPPER( a.abr_tercer ) ) LIKE '%" . $_AJAX['term'] . "%'
           ORDER BY 2";

        $consulta = new Consulta($mSql, $this->conexion);
        $transpor = $consulta->ret_matriz();

        $data = array();
        for ($i = 0, $len = count($transpor); $i < $len; $i++) {
            $data [] = '{"label":"' . $transpor[$i][0] . ' - ' . utf8_encode($transpor[$i][1]) . '","value":"' . $transpor[$i][0] . ' - ' . utf8_encode($transpor[$i][1]) . '"}';
        }
        echo '[' . join(', ', $data) . ']';
    }

}

$proceso = new AjaxTranspProtoc();
?>