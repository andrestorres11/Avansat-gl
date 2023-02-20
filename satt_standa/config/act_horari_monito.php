<?php

class HoraMoni {

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
                $this->Filtro();
                break;
            case "2":
                $this->Listar();
                break;
            case "3":
                $this->Formulario();
                break;
            case "4":
                $this->registrar();
                break;
            default:
                $this->Filtro();
                break;
        }
    }

    function Filtro() {
        $inicio[0][0] = 0;
        $inicio[0][1] = '-';
        //codigo de ruta
        $query = "SELECT cod_usuari,TRIM( UPPER( nom_usuari ) ) AS nom_usuari FROM " . BASE_DATOS . ".tab_genera_usuari WHERE cod_perfil IN (7,713) AND ind_estado = '1' ORDER BY 2 ASC";
        $consulta = new Consulta($query, $this->conexion);
        $usuar = $consulta->ret_matriz();
        $usuari = array_merge($inicio, $usuar);
        $query = "SELECT a.cod_tercer,a.abr_tercer
        			FROM " . BASE_DATOS . ".tab_tercer_tercer a,
   		           	 " . BASE_DATOS . ".tab_tercer_activi b
   		        WHERE a.cod_tercer = b.cod_tercer AND
   		              b.cod_activi = " . COD_FILTRO_EMPTRA . "
   		        ORDER BY 2 ";
        $consulta = new Consulta($query, $this->conexion);
        $transpor = $consulta->ret_matriz();
        $transpor = array_merge($inicio, $transpor);
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
        $formulario = new Formulario("index.php", "post", "Listar Horarios de Monitoreo", "form_ins\" id=\"formularioID");
        $formulario->nueva_tabla();
        $formulario->lista("Usuario", "usuari\" id=\"usuariID", $usuari, 1);
        $formulario->lista("Transportadora", "transp\" id=\"transpID", $transpor, 1);
        $formulario->texto("Fecha Inicio", "text", "fecini\" id=\"feciniID", 0, 9, 12, "", "$_REQUEST[fecini]");
        $formulario->texto("Fecha Final", "text", "fecfin\" id=\"fecfinID", 0, 9, 12, "", "$_REQUEST[fecfin]");
        $formulario->nueva_tabla();
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_servic", $_REQUEST[cod_servic], 0);
        $formulario->oculto("opcion\" id=\"opcionID", 1, 0);

        $formulario->botoni("Buscar", "listar()", 0);

        $formulario->nueva_tabla();
        echo "<br><br><br><br><br><br><br><br>";
        $formulario->cerrar();
    }

    function Listar() {
        include( "../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc" );

        echo "<link rel=\"stylesheet\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css\" type=\"text/css\">";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/dinamic_list.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/monit.js\"></script>\n";

        $formulario = new Formulario("index.php\" enctype=\"multipart/form-data\"", "post", "Listar Horarios de Monitoreo", "formulario\" id=\"formularioID");
        $sql = "SELECT a.cod_consec, b.cod_usuari, b.nom_usuari,
                       a.fec_inicia, a.fec_finalx,
                       if(a.ind_estado ='1','Estado','Anulado'), 
		       if(a.ind_limpio ='1','Limpio','No limpio') AS ind_limpio 
   				        FROM " . BASE_DATOS . ".tab_monito_encabe a,
                       " . BASE_DATOS . ".tab_genera_usuari b,
                       " . BASE_DATOS . ".tab_monito_detall c
   			          WHERE a.cod_usuari = b.cod_usuari AND
                        c.cod_consec = a.cod_consec AND 
                        a.ind_estado = 1 ";
        if ($_REQUEST['usuari'])
            $sql .= " AND a.cod_usuari = '" . $_REQUEST['usuari'] . "' ";
        if ($_REQUEST['transp'])
            $sql .= " AND c.cod_tercer = '" . $_REQUEST['transp'] . "' ";
        if ($_REQUEST['fecini'] && $_REQUEST['fecfin'])
            $sql .= " AND ((a.fec_inicia >= '" . $_REQUEST['fecini'] . " 00:00:00'  AND a.fec_inicia <= '" . $_REQUEST['fecini'] . " 23:59:00') OR (a.fec_finalx >= '" . $_REQUEST['fecfin'] . " 00:00:00' AND a.fec_finalx <= '" . $_REQUEST['fecfin'] . " 23:59:00'))";
        $sql .=" GROUP BY 1";
        $_SESSION["queryXLS"] = $sql;
        $list = new DinamicList($this->conexion, $sql, 1);
        $list->SetClose('no');
        $list->SetHeader("Consecutivo", "field:a.cod_consec; type:link; onclick:ActHorari()");
        $list->SetHeader("Usuario", "field:a.cod_usuari; type:link; onclick:ActHorari()");
        $list->SetHeader("Nombre", "field:b.nom_usuari");
        $list->SetHeader("Fecha Inicial", "field:a.fec_inicia");
        $list->SetHeader("Fecha Final", "field:a.fec_finalx");
        $list->SetHeader("Estado", "field:a.ind_estado");
        $list->SetHeader("Limpio", "field:a.ind_limpio");

        $list->Display($this->conexion);

        $_SESSION["DINAMIC_LIST"] = $list;
        echo "<td>";
        echo $list->GetHtml();
        echo "</td>";

        $formulario->nueva_tabla();
        $formulario->oculto("url_archiv\" id=\"url_archivID\"", "lis_horari_monito.php", 0);
        $formulario->oculto("dir_aplica\" id=\"dir_aplicaID\"", DIR_APLICA_CENTRAL, 0);
        $formulario->oculto("cod_consec\" id=\"cod_consecID", 0, 0);
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_servic", $_REQUEST["cod_servic"], 0);
        $formulario->oculto("opcion\" id=\"opcionID", 2, 0);

        $formulario->cerrar();

        echo '<tr><td><div id="AplicationEndDIV"></div>
              <div id="RyuCalendarDIV" style="position: absolute; background: white; left: 0px; top: 0px; z-index: 3; display: none; border: 1px solid black; "></div>
              <div id="popupDIV" style="position: absolute; left: 0px; top: 0px; width: 300px; height: 300px; z-index: 3; visibility: hidden; overflow: auto; border: 5px solid #333333; background: white; ">

    		  <div id="filtros" >
    		  </div>

    		  <div id="result" >


    		  </div>
     		  </div><div id="alg"> <table></table></div></td></tr>';
        echo"";
    }

    function Formulario() {

        $sql = "SELECT 1
		        FROM " . BASE_DATOS . ".tab_monito_encabe 
	          WHERE cod_consec = '" . $_POST['cod_consec'] . "' AND
                  fec_finalx < NOW()";
        $consulta = new Consulta($sql, $this->conexion);
        $horari = $consulta->ret_matriz();

        if ($horari) {
            $mensaje .= "EL Horario No Puede Ser Actualizado Porque su Fecha de Final es Menor que la Fecha Actual";
            $mens = new mensajes();
            $mens->error("", $mensaje);
            die();
        }

        $sql = "SELECT b.cod_usuari, b.nom_usuari,
                   a.fec_inicia, a.fec_finalx,
                   a.obs_anulad, if(a.ind_estado ='1','Activo','Anulado'),
                   c.cod_tercer, d.abr_tercer,
                   a.ind_limpio
		        FROM " . BASE_DATOS . ".tab_monito_encabe a,
                 " . BASE_DATOS . ".tab_genera_usuari b,
                 " . BASE_DATOS . ".tab_monito_detall c,
                 " . BASE_DATOS . ".tab_tercer_tercer d
	          WHERE a.cod_usuari = b.cod_usuari AND
                  c.cod_consec = a.cod_consec AND
                  c.cod_tercer = d.cod_tercer AND
                  c.ind_estado = '1' AND
                  a.cod_consec = '" . $_POST['cod_consec'] . "'";
        $consulta = new Consulta($sql, $this->conexion);
        $horari = $consulta->ret_matriz();
        $ter = array();
        for ($i = 0; sizeof($horari) >= $i + 1; $i++) {
            $ter[$i] = "'" . $horari[$i][6] . "'";
        }

        $ter = implode(",", $ter);
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/monit.js\"></script>\n";
        $formulario = new Formulario("index.php", "post", "Actualizar Horarios de Monitoreo", "form_ins\" id=\"formularioID");
        $formulario->nueva_tabla();
        $formulario->linea("Usuario:", 0, "t", "15%");
        $formulario->linea($horari[0][0], 0, "i", "15%");
        $formulario->linea("Nombre:", 0, "t", "15%");
        $formulario->linea($horari[0][1], 1, "i", "15%");
        $formulario->linea("Fecha Inicial:", 0, "t", "15%");
        $formulario->linea($horari[0][2], 0, "i", "15%");
        $formulario->linea("Fecha Final:", 0, "t", "15%");
        $formulario->linea($horari[0][3], 1, "i", "15%");
        $formulario->linea("Limpio:", 1, "t2", "15%",8);
        $formulario->radio("si","ind_limpio", "1",($horari[0]['ind_limpio'] == "1" ? 1 : 0), "0");
        $formulario->radio("no","ind_limpio", "0" , ($horari[0]['ind_limpio'] == "0" ? 1 : 0), "1");
        echo "<td align='left' class='celda_titulo'>Anular
             </td>";
        echo '<td width="15%" align="left" class="celda_info"><input type="checkbox" id="anuladID" name="anula" value="1"></td>';
        echo "<td align='left' class='celda_titulo'>Observacion Anulado
             </td>";
        echo '<td width="15%" align="left" class="celda_info"><textarea cols="30" rows="3" id="obs_anuladID" name="obs_anulad" ></textarea></td></tr>';
        $x = 0;
        $formulario->nueva_tabla();
        $formulario->linea("Transportadora", 0, "t2", "15%");
        $formulario->linea("Anular", 0, "t2", "15%");
        $formulario->linea("Observacion de Anulado ", 1, "t2", "15%");
        for ($i = 0; $i <= sizeof($horari) - 1; $i++) {
            $query = "SELECT COUNT(a.num_despac)
  					  FROM " . BASE_DATOS . ".tab_despac_despac a,
                   " . BASE_DATOS . ".tab_despac_vehige b 
  					  WHERE a.fec_salida Is Not Null AND 
  							    a.fec_salida <= NOW() AND 
  							    a.fec_llegad Is Null AND 
  							    a.ind_anulad = 'R' AND 
  							    a.ind_planru = 'S' AND
                    a.num_despac = b.num_despac AND 
  							    b.cod_transp = '" . $horari[$i][6] . "'";
            $consulta = new Consulta($query, $this->conexion);
            $despac = $consulta->ret_matriz();
            $x+=$despac[0][0];
            $formulario->linea($horari[$i][7], 0, "i", "15%");
            echo '<td width="15%" align="left" class="celda_info"><input type="checkbox" id="anuladID' . $i . '" name="anulad' . $i . '" value="' . $horari[$i][6] . '"></td>';
            echo '<td width="15%" align="left" class="celda_info"><textarea cols="30" rows="2" id="obs_anuladID' . $i . '" name="obs_anulad' . $i . '" ></textarea></td></tr>';
        }
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_servic", $_REQUEST[cod_servic], 0);
        $formulario->oculto("opcion\" id=\"opcionID", 1, 0);
        $formulario->oculto("cod_consec\" id=\"cod_consecID", $_POST['cod_consec'], 0);
        $formulario->oculto("anul\" id=\"numanulID", sizeof($horari), 0);
        $formulario->nueva_tabla();
        $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
        $query = "SELECT a.cod_tercer,a.abr_tercer
   				FROM " . BASE_DATOS . ".tab_tercer_tercer a,
   			     	 " . BASE_DATOS . ".tab_tercer_activi b
   			   WHERE a.cod_tercer = b.cod_tercer AND
   			         b.cod_activi = " . COD_FILTRO_EMPTRA . " ";
        if ($horari)
            $query .="  AND a.cod_tercer NOT IN($ter)";

        $query .=" ORDER BY 2";
        $consulta = new Consulta($query, $this->conexion);
        $transpor = $consulta->ret_matriz();
        $formulario->nueva_tabla();
        echo '<td class="celda_titulo2"><b>Numero de Despachos Seleccionados</b>
          <input type="text" maxlength="2" readonly="true" value="' . $x . '" size="2" style="background:none; border:none; color:#336600; font-weight:bold;" id="numID" >
          </td></tr>';
        $formulario->nueva_tabla();
        $formulario->oculto("transp\" id=\"transpID", sizeof($transpor), 0);
        $formulario->botoni("Aceptar", "actualizar()", 0);
        echo '</tr></table><div style="height:300px; overflow:scroll"><table width="100%"  cellspacing="0" cellpadding="4" class="formulario" id="algoID">
    <tbody><tr>';
        echo '<td align="left" class="celda_titulo2"><b>Transportadora</b></td>
    <td align="left" class="celda_titulo2"><b>N.Despachos</b></td>
    <td align="left" class="celda_titulo2"><b>S/N</b></td>
    <td align="left" class="celda_titulo2"><b>Transportadora</b></td>
    <td align="left" class="celda_titulo2"><b>N.Despachos</b></td>
    <td align="left" class="celda_titulo2"><b>S/N</b></td>';

        for ($i = 0; $i <= sizeof($transpor); $i++) {
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
            echo '<td align="left" id="despacID' . $i . '" class="celda_titulo">' . $despac . '</td> ';
            echo "<td align='left' class='celda_titulo'>
             <input type='checkbox' name='tercer$i' onclick='sum(" . $i . ")' id='tercerID$i' value='" . $transpor[$i][0] . "'>
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
            echo '<td align="left" id="despacID' . $i . '" class="celda_titulo">' . $despac . '</td> ';
            echo "<td align='left' class='celda_titulo'>
             <input type='checkbox' name='tercer$i' id='tercerID$i' value='" . $transpor[$i][0] . "'>
             </td></tr>";
        }

        echo "</table></div>";
        $formulario->nueva_tabla();
        $formulario->botoni("Aceptar", "actualizar()", 0);

        $formulario->nueva_tabla();
        echo "<br><br><br><br><br><br><br><br>";
        $formulario->cerrar();
    }

    function registrar() {

        
        global $HTTP_POST_FILES;

        $usuario = $_SESSION["datos_usuario"]["cod_usuari"];
        $detall = "UPDATE " . BASE_DATOS . ".tab_monito_encabe SET
                       fec_modifi = NOW(),
                       usr_modifi = '$usuario',
                       ind_limpio = '".$_POST["ind_limpio"]."'
                       WHERE cod_consec = '" . $_POST["cod_consec"] . "'";
        

        
        $insercion = new Consulta($detall, $this->conexion, "BR");
        if ($_POST[anula]) {
            $detall = "UPDATE " . BASE_DATOS . ".tab_monito_encabe SET
  									   ind_estado = '0' ,
                       obs_anulad ='" . $_POST["obs_anulad"] . "' ,
                       fec_modifi = NOW(),
                       usr_modifi = '$usuario'
                       WHERE cod_consec = '" . $_POST["cod_consec"] . "'";
            $insercion = new Consulta($detall, $this->conexion, "R");
        } else {

            for ($i = 0; $i <= $_POST[anul] - 1; $i++) {
                if ($_POST["anulad$i"]) {
                    echo $_POST["anulad$i"];
                    $detall = "UPDATE " . BASE_DATOS . ".tab_monito_detall SET
  									   ind_estado = '0' ,
                       obs_anulad ='" . $_POST["obs_anulad$i"] . "' ,
                       fec_modifi = NOW(),
                       usr_modifi = '$usuario'
                       WHERE cod_consec = '" . $_POST["cod_consec"] . "' AND
                             cod_tercer = '" . $_POST["anulad$i"] . "'";
                    $insercion = new Consulta($detall, $this->conexion, "R");
                }
            }

            for ($i = 0; $i <= $_POST[transp] - 1; $i++) {
                if ($_POST["tercer$i"]) {
                    $sql = "SELECT 1
          		        FROM " . BASE_DATOS . ".tab_monito_detall
          	          WHERE cod_consec = '" . $_POST['cod_consec'] . "' AND
                            cod_tercer = '" . $_POST["tercer$i"] . "'";
                    $consulta = new Consulta($sql, $this->conexion);
                    $tercer = $consulta->ret_matriz();
                    if ($tercer)
                        $detall = "UPDATE " . BASE_DATOS . ".tab_monito_detall SET
  									   ind_estado = '1',
                       obs_anulad ='' ,
                       fec_modifi = NOW(),
                       usr_modifi = '$usuario'
                       WHERE cod_consec = '" . $_POST["cod_consec"] . "' AND
                             cod_tercer = '" . $_POST["tercer$i"] . "'";
                    else
                        $detall = "INSERT INTO " . BASE_DATOS . ".tab_monito_detall
  										( 
  											cod_consec, cod_tercer, usr_creaci, 
                        fec_creaci 
  										)
  										VALUES
  										( 
  											'" . $_POST["cod_consec"] . "', '" . $_POST["tercer$i"] . "', '$usuario',
                        NOW()
  										)";

                    $insercion = new Consulta($detall, $this->conexion, "R");
                }
            }
        }

        if ($insercion = new Consulta("COMMIT", $this->conexion)) {
            $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=" . $_REQUEST[cod_servic] . " \"target=\"centralFrame\">Actualizar Otro Horario</a></b>";

            if ($msm)
                $mensaje = $msm;
            $mensaje .= "El Horario se Actualizo con Exito" . $link_a;
            $mens = new mensajes();
            $mens->correcto("Actualizar Horario", $mensaje);
        }
    }

}

//FIN CLASE PROC_DESPAC

$proceso = new HoraMoni($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>
