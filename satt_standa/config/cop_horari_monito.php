<?php

class CopHor {

    var $conexion,
            $cod_aplica,
            $usuario;

    function __construct($co, $us, $ca) {
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        $this->principal();
    }

    function principal() {

        switch ($_REQUEST[opcion]) {
            case "1":
                $this->Formulario();
                break;
            case "2":
                $this->registrar();
                break;
            default:
                $this->Formulario();
                break;
        }
    }

    function Formulario() {
        $inicio[0][0] = 0;
        $inicio[0][1] = '-';
        //
        $query = "SELECT cod_usuari,TRIM( UPPER( nom_usuari ) ) AS nom_usuari  
             FROM " . BASE_DATOS . ".tab_genera_usuari 
             WHERE ind_estado = '1' AND 
             cod_perfil IN(8,1,73,7,713) OR 
             cod_usuari LIKE '%eal%' OR 
             nom_usuari LIKE '%eal%' 
             ORDER BY 2 ASC ";
        //echo "<pre>$query</pre>";          
        $consulta = new Consulta($query, $this->conexion);
        $usuar = $consulta->ret_matriz();
        $usuari = array_merge($inicio, $usuar);
        if ($_REQUEST[usuari]) {
            $query = "SELECT cod_usuari,nom_usuari 
               FROM " . BASE_DATOS . ".tab_genera_usuari 
               WHERE cod_usuari = '" . $_REQUEST[usuari] . "'";
            $consulta = new Consulta($query, $this->conexion);
            $usuar = $consulta->ret_matriz();
            $usuari = array_merge($usuar, $usuari);
        }
        $query = "SELECT a.cod_usuari,TRIM( UPPER( a.nom_usuari ) ) AS nom_usuari
              FROM " . BASE_DATOS . ".tab_genera_usuari a,
                   " . BASE_DATOS . ".tab_monito_encabe b 
               WHERE a.cod_usuari = b.cod_usuari 
               GROUP BY 1
               ORDER BY 2";
        $consulta = new Consulta($query, $this->conexion);
        $usuar = $consulta->ret_matriz();
        $usua = array_merge($inicio, $usuar);
        if ($_REQUEST[usua]) {
            $query = "SELECT cod_usuari,nom_usuari 
               FROM " . BASE_DATOS . ".tab_genera_usuari 
               WHERE cod_usuari = '" . $_REQUEST[usua] . "'
               GROUP BY 1";
            $consulta = new Consulta($query, $this->conexion);
            $usuar = $consulta->ret_matriz();
            $usua = array_merge($usuar, $usua);
        }
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/monit.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
        echo '
    <script>
      jQuery(function($) { 
        
        $( "#feciniID,#fecfinID" ).datepicker();      
        $( "#horiniID,#horfinID" ).timepicker({
          timeFormat:"hh:mm",
          showSecond: false
        });
        
        $.mask.definitions["A"]="[12]";
        $.mask.definitions["M"]="[01]";
        $.mask.definitions["D"]="[0123]";
        
        $.mask.definitions["H"]="[012]";
        $.mask.definitions["N"]="[012345]";
        $.mask.definitions["n"]="[0123456789]";
        
        $( "#feciniID,#fecfinID" ).mask("Annn-Mn-Dn");
        $( "#horiniID,#horfinID" ).mask("Hn:Nn");
     })
     </script>';
        if ($_REQUEST['usuari'] && $_REQUEST['fecini'] && $_REQUEST['fecfin'])
            $aux = " onchange='form_ins.submit()'";
        $formulario = new Formulario("index.php", "post", "Copiar Horarios de Monitoreo", "form_ins\" id=\"formularioID");
        $formulario->nueva_tabla();
        $formulario->lista("Usuario", "usuari\" $aux id=\"usuariID", $usuari, 1);
        $formulario->texto("Fecha Inicio", "text", "fecini\" $aux id=\"feciniID", 0, 9, 12, "", "$_REQUEST[fecini]");
        $formulario->texto("Hora Inicio", "text", "horini\" $aux id=\"horiniID", 1, 9, 12, "", "$_REQUEST[horini]");
        $formulario->texto("Fecha Final", "text", "fecfin\" $aux id=\"fecfinID", 0, 9, 12, "", "$_REQUEST[fecfin]");
        $formulario->texto("Hora Final", "text", "horfin\" $aux id=\"horfinID", 1, 9, 12, "", "$_REQUEST[horfin]");
        $formulario->lista("Copiar de ", "usua\" $aux id=\"usuaID", $usua, 1);
        $formulario->nueva_tabla();
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_servic", $_REQUEST[cod_servic], 0);
        $formulario->oculto("opcion\" id=\"opcionID", 1, 0);

        $formulario->nueva_tabla();
        if ($_REQUEST['usuari'] && $_REQUEST['fecini'] && $_REQUEST['fecfin'] && $_REQUEST['usua']) {
            $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
            $formulario->nueva_tabla();
            $formulario->botoni("Cancelar", "aceptar_ins(2)", 1);



            $query = "SELECT 1
   				FROM " . BASE_DATOS . ".tab_monito_encabe a
   			   WHERE a.ind_estado = 1 AND
                 a.cod_usuari = '" . $_REQUEST['usuari'] . "' AND 
                 (fec_inicia <= '" . $_REQUEST['fecini'] . " " . $_REQUEST['horini'] . "' AND 
                  fec_finalx >= '" . $_REQUEST['fecini'] . " " . $_REQUEST['horini'] . "') 
                 ";
            $consulta = new Consulta($query, $this->conexion);
            $fec = $consulta->ret_matriz();
            $mensaje = '';
            if ($fec) {
                $mensaje = "Cruce de Horarios con la Fecha Inicial" . $link_a;
            }
            $query = "SELECT 1
   				FROM " . BASE_DATOS . ".tab_monito_encabe a
   			   WHERE a.ind_estado = 1 AND
                 a.cod_usuari = '" . $_REQUEST['usuari'] . "' AND 
                 (fec_inicia <= '" . $_REQUEST['fecfin'] . " " . $_REQUEST['horfin'] . "' AND 
                  fec_finalx >= '" . $_REQUEST['fecfin'] . " " . $_REQUEST['horfin'] . "') 
                 ";
            $consulta = new Consulta($query, $this->conexion);
            $fec = $consulta->ret_matriz();
            if ($fec) {
                $mensaje .= ".Cruce de Horarios con la Fecha Final" . $link_a;
            }
            if ($mensaje != '') {
                $mens = new mensajes();
                $mens->error("", $mensaje);
                die();
            }
            $sql = "SELECT MAX(d.cod_consec) 
          FROM " . BASE_DATOS . ".tab_monito_encabe d
          WHERE d.cod_usuari = '" . $_REQUEST['usua'] . "'";
            $consulta = new Consulta($sql, $this->conexion);
            $max = $consulta->ret_matriz();
            $max = $max[0][0];
            $sql = "SELECT b.cod_usuari, b.nom_usuari,
                   a.fec_inicia, a.fec_finalx,
                   a.obs_anulad, if(a.ind_estado ='1','Activo','Anulado'),
                   c.cod_tercer, d.abr_tercer, a.ind_limpio 
		        FROM " . BASE_DATOS . ".tab_monito_encabe a,
                 " . BASE_DATOS . ".tab_genera_usuari b,
                 " . BASE_DATOS . ".tab_monito_detall c,
                 " . BASE_DATOS . ".tab_tercer_tercer d
	          WHERE a.cod_usuari = b.cod_usuari AND
                  c.cod_consec = a.cod_consec AND
                  c.cod_tercer = d.cod_tercer AND
                  c.ind_estado = '1' AND
                  a.cod_consec = $max";
            $consulta = new Consulta($sql, $this->conexion);
            $horari = $consulta->ret_matriz();
            
            
            $formulario->linea("Limpio:", 1, "t2", "15%", 8);
        $formulario->radio("si","ind_limpio", "1",($horari[0]['ind_limpio'] == "1" ? 1 : 0), "0");
        $formulario->radio("no","ind_limpio", "0" , ($horari[0]['ind_limpio'] == "0" ? 1 : 0), "1");
            
            $ter = array();
            if ($horari) {
                for ($i = 0; sizeof($horari) >= $i + 1; $i++) {
                    $ter[$i] = "'" . $horari[$i][6] . "'";
                }


                $ter = implode(",", $ter);
                $query = "SELECT COUNT(a.num_despac)
  					  FROM " . BASE_DATOS . ".tab_despac_despac a,
                   " . BASE_DATOS . ".tab_despac_vehige b 
  					  WHERE a.fec_salida Is Not Null AND 
  							    a.fec_salida <= NOW() AND 
  							    a.fec_llegad Is Null AND 
  							    a.ind_anulad = 'R' AND 
  							    a.ind_planru = 'S' AND
                    a.num_despac = b.num_despac AND 
  							    b.cod_transp  IN($ter)";
                $consulta = new Consulta($query, $this->conexion);
                $despac = $consulta->ret_matriz();
                $x = $despac[0][0];
            } else
                $x = 0;
            $query = "SELECT  a.cod_tercer,a.abr_tercer, if(c.ind_estado ='1',1,0)
              FROM    " . BASE_DATOS . ".tab_tercer_activi b,
                      " . BASE_DATOS . ".tab_tercer_tercer a
                      LEFT JOIN " . BASE_DATOS . ".tab_monito_detall c ON c.cod_consec = '$max' AND c.cod_tercer = a.cod_tercer
              WHERE   a.cod_tercer = b.cod_tercer AND
                      b.cod_activi = " . COD_FILTRO_EMPTRA . " AND
                      a.cod_estado = '1'
              ORDER BY 2
              ";
            $consulta = new Consulta($query, $this->conexion);
            $transpor = $consulta->ret_matriz();
            $formulario->nueva_tabla();
            echo '<td class="celda_titulo2"><b>Numero de Despachos Seleccionados</b>
          <input type="text" maxlength="4" readonly="true" value="' . $x . '" size="3" style="background:none; border:none; color:#336600; font-weight:bold;" id="numID" >
          </td></tr>';
            $formulario->nueva_tabla();
            $formulario->oculto("transp\" id=\"transpID", sizeof($transpor), 0);
            $formulario->botoni("Aceptar", "aceptar_ins(3)", 0);

            echo '</tr></table><div style="height:400px; overflow:scroll">';

            echo '<table width="100%"  cellspacing="0" cellpadding="4" class="formulario" id="otherID"><tbody>';
            echo '<tr>';
            echo '<td colspan="3" class="celda_titulo2"><b>Tipo Seguimiento</b></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td align="left" class="celda_titulo">Cargue&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ind_cargue" value="1" id="ind_cargueID"/></td>';
            echo '<td align="left" class="celda_titulo">Transito&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ind_transi" checked="true" value="1" id="ind_transiID"/></td>';
            echo '<td align="left" class="celda_titulo">Descargue&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ind_descar" value="1" id="ind_descarID"/></td>';
            echo '</tr>';
            echo '</tbody></table>';
            echo '<table width="100%"  cellspacing="0" cellpadding="4" class="formulario" id="otherID"><tbody>';
            echo '<tr>';
            echo '<td colspan="6" class="celda_titulo2"><b>Tipo Despacho</b></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td align="left" class="celda_titulo">Urbano&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ind_desurb" checked="true" value="1" id="ind_desurbID"/></td>';
            echo '<td align="left" class="celda_titulo">Nacional&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ind_desnac" checked="true" value="1" id="ind_desnacID"/></td>';
            echo '<td align="left" class="celda_titulo">Importaci&oacute;n&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ind_desimp" checked="true" value="1" id="ind_desimpID"/></td>';
            echo '<td align="left" class="celda_titulo">Exportaci&oacute;n&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ind_desexp" checked="true" value="1" id="ind_desexpID"/></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td align="left" class="celda_titulo">&nbsp;</td>';
            echo '<td align="left" class="celda_titulo">XD Tipo 1&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ind_desxd1" checked="true" value="1" id="ind_desxd1ID"/></td>';
            echo '<td align="left" class="celda_titulo">XD Tipo 2&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ind_desxd2" checked="true" value="1" id="ind_desxd2ID"/></td>';
            echo '<td align="left" class="celda_titulo">&nbsp;</td>';
            echo '</tr>';
            echo '<tr><td>&nbsp;</td></tr>';
            echo '</tbody></table>';

            echo '<table width="100%"  cellspacing="0" cellpadding="4" class="formulario" id="algoID"><tbody>';
            echo '<tr><td align="left" class="celda_titulo2"><b>Transportadora</b></td>
    <td align="left" class="celda_titulo2"><b>N.Despachos</b></td>
    <td align="left" class="celda_titulo2"><b>S/N</b></td>
    <td align="left" class="celda_titulo2"><b>Transportadora</b></td>
    <td align="left" class="celda_titulo2"><b>N.Despachos</b></td>
    <td align="left" class="celda_titulo2"><b>S/N</b></td>';

            for ($i = 0; $i < sizeof($transpor); $i++) {
                echo '<tr><td align="left" class="celda_titulo"><b>' . $transpor[$i][1] . '</b></td>';
                $query = "SELECT COUNT(a.num_despac)
                FROM " . BASE_DATOS . ".tab_despac_despac a,
                     " . BASE_DATOS . ".tab_despac_vehige b 
                WHERE a.fec_salida Is Not Null AND 
                      a.fec_salida <= NOW() AND 
                      a.fec_llegad Is Null AND 
                      a.ind_anulad = 'R' AND 
                      a.ind_planru = 'S' AND
                      a.num_despac = b.num_despac AND 
                      b.cod_transp = '" . $transpor[$i][0] . "'";
                $consulta = new Consulta($query, $this->conexion);
                $despac = $consulta->ret_matriz();
                if (!$despac)
                    $despac = '0';
                else
                    $despac = $despac[0][0];
                if ($transpor[$i][2] == '1')
                    $aux1 = "checked=checked ";
                else
                    $aux1 = "";
                echo '<td align="left" id="despacID' . $i . '" class="celda_titulo">' . $despac . '</td> ';
                echo "<td align='left' class='celda_titulo'>
               <input type='checkbox' name='tercer$i' $aux1 onclick='sum(" . $i . ")' id='tercerID$i' value='" . $transpor[$i][0] . "'>
               </td>";
                $i++;
                echo '<td align="left" class="celda_titulo"><b>' . $transpor[$i][1] . '</b></td>';
                $query = "SELECT COUNT(a.num_despac)
                FROM " . BASE_DATOS . ".tab_despac_despac a,
                     " . BASE_DATOS . ".tab_despac_vehige b 
                WHERE a.fec_salida Is Not Null AND 
                      a.fec_salida <= NOW() AND 
                      a.fec_llegad Is Null AND 
                      a.ind_anulad = 'R' AND 
                      a.ind_planru = 'S' AND
                      a.num_despac = b.num_despac AND 
                      b.cod_transp = '" . $transpor[$i][0] . "'";
                $consulta = new Consulta($query, $this->conexion);
                $despac = $consulta->ret_matriz();
                if (!$despac)
                    $despac = '0';
                else
                    $despac = $despac[0][0];
                if ($transpor[$i][2] == '1')
                    $aux1 = "checked=checked ";
                else
                    $aux1 = "";
                echo '<td align="left" id="despacID' . $i . '" class="celda_titulo">' . $despac . '</td> ';
                echo "<td align='left' class='celda_titulo'>
               <input type='checkbox' $aux1 name='tercer$i' id='tercerID$i' value='" . $transpor[$i][0] . "'>
               </td></tr>";
            }

            echo "</table></div>";
            $formulario->nueva_tabla();
            $formulario->botoni("Aceptar", "aceptar_ins(3)", 0);
        } else
            $formulario->botoni("Aceptar", "aceptar_ins(1)", 0);

        $formulario->nueva_tabla();
        echo "<br><br><br><br><br><br><br><br>";
        $formulario->cerrar();
    }

    function registrar() {
        global $HTTP_POST_FILES;



        $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
        $error = 0;
        $query = "SELECT MAX(cod_consec) 
                 FROM " . BASE_DATOS . ".tab_monito_encabe";
        $max = new Consulta($query, $this->conexion, "BR");
        $max = $max->ret_matriz();
        $max = $max[0][0] + 1;

        $_REQUEST['ind_cargue'] = $_REQUEST['ind_cargue'] == '1' ? $_REQUEST['ind_cargue'] : '0';
        $_REQUEST['ind_transi'] = $_REQUEST['ind_transi'] == '1' ? $_REQUEST['ind_transi'] : '0';
        $_REQUEST['ind_descar'] = $_REQUEST['ind_descar'] == '1' ? $_REQUEST['ind_descar'] : '0';

        $_REQUEST['ind_desurb'] = $_REQUEST['ind_desurb'] == '1' ? $_REQUEST['ind_desurb'] : '0';
        $_REQUEST['ind_desnac'] = $_REQUEST['ind_desnac'] == '1' ? $_REQUEST['ind_desnac'] : '0';
        $_REQUEST['ind_desimp'] = $_REQUEST['ind_desimp'] == '1' ? $_REQUEST['ind_desimp'] : '0';
        $_REQUEST['ind_desexp'] = $_REQUEST['ind_desexp'] == '1' ? $_REQUEST['ind_desexp'] : '0';
        $_REQUEST['ind_desxd1'] = $_REQUEST['ind_desxd1'] == '1' ? $_REQUEST['ind_desxd1'] : '0';
        $_REQUEST['ind_desxd2'] = $_REQUEST['ind_desxd2'] == '1' ? $_REQUEST['ind_desxd2'] : '0';

        $encabe = "INSERT INTO " . BASE_DATOS . ".tab_monito_encabe
										( 
											cod_consec, cod_usuari, fec_inicia, 
											fec_finalx, usr_creaci, fec_creaci,
                      ind_cargue, ind_transi, ind_descar,
                      ind_desurb, ind_desnac, ind_desimp,
                      ind_desexp, ind_desxd1, ind_desxd2,
                      ind_limpio
										)
										VALUES
										( 
											'" . $max . "', '" . $_POST["usuari"] . "', '" . $_POST["fecini"] . " " . $_POST["horini"] . ":00', 
											'" . $_POST["fecfin"] . " " . $_POST["horfin"] . ":00', '$usuario',NOW(), 
                      '" . $_REQUEST['ind_cargue'] . "', '" . $_REQUEST['ind_transi'] . "', '" . $_REQUEST['ind_descar'] . "',
                      '" . $_REQUEST['ind_desurb'] . "', '" . $_REQUEST['ind_desnac'] . "', '" . $_REQUEST['ind_desimp'] . "',
                      '" . $_REQUEST['ind_desexp'] . "', '" . $_REQUEST['ind_desxd1'] . "', '" . $_REQUEST['ind_desxd2'] . "','" . $_REQUEST['ind_limpio'] . "'
										)";
        
        

        
        $insercion = new Consulta($encabe, $this->conexion, "BR");
        for ($i = 1; $i <= $_POST[transp]; $i++) {
            if ($_POST["tercer$i"]) {
                $detall = "INSERT INTO " . BASE_DATOS . ".tab_monito_detall
										( 
											cod_consec, cod_tercer, usr_creaci, 
                      fec_creaci 
										)
										VALUES
										( 
											'" . $max . "', '" . $_POST["tercer$i"] . "', '$usuario',
                      NOW()
										)";
                $insercion = new Consulta($detall, $this->conexion, "R");
            }
        }


        if ($insercion = new Consulta("COMMIT", $this->conexion)) {
            $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=" . $_REQUEST[cod_servic] . " \"target=\"centralFrame\">Insertar Otro Horario</a></b>";

            if ($msm)
                $mensaje = $msm;
            $mensaje .= "El Horario se Inserto con Exito" . $link_a;
            $mens = new mensajes();
            $mens->correcto("INSERTAR Horario", $mensaje);
        }
    }

}

//FIN CLASE PROC_DESPAC

$proceso = new CopHor($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>
