<?php

class Proc_despac {

    var $conexion,
            $usuario,
            $cod_aplica;

    function __construct($co, $us, $ca) {
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        $this->principal();
    }

    function principal() {
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/sweetalert2.all.8.11.8.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/regnov.js\"></script>\n";
        echo "<link type='text/css' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' rel='stylesheet'>\n";
        switch ($_REQUEST[opcion]) {
            case "datos":
                $this->ListarDatos();
                break;
            case "2":
                $this->Insertar();
                $_REQUEST[placa] = '';
                $this->Formulario();
                break;
            case "3":
                $this->Listar();
                break;
            default:
                $this->Formulario();
                break;
        }
    }

    function Formulario() {
        $datos_usuario = $this->usuario->retornar();
        $usuario = $datos_usuario["cod_usuari"];
        $fec_actual = date("d-m-Y");
        $hor_actual = date("H:i:s");
        $inicio[0][0] = 0;
        $inicio[0][1] = "-";
        switch ($_POST[fil]) {
            case '2': $B = " checked ";
                break;
            case '3': $C = " checked ";
                break;
            default: $A = " checked ";
                break;
        }
        if (!$_REQUEST['fecnov'])
            $_REQUEST['fecnov'] = $fec_actual;
        if (!$_REQUEST['hornov'])
            $_REQUEST['hornov'] = $hor_actual;
        if (!$_REQUEST['ind_despac'])
            $_REQUEST['ind_despac'] = 0;

        $_REQUEST['placa'] = strtoupper($_REQUEST['placa']);
        $formulario = new Formulario("index.php", "post", "REGISTRO DE NOVEDADES", "form_ins");

        echo "</table><div class='cellHead' style='padding: 1em 2.2em'><div class='Style2DIV'>
                    <table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0'>
                        <tr>
                            <td class='cellHead centrado' style='padding:4px;'  colspan='4' >
                                <b>Inserci&oacute;n R&aacute;pida de Novedades P/C</b>
                            </td>
                        </tr>
                        <tr>
                            <td class='contenido' style='padding:4px;' align='right' >
                                Placa Veh&iacute;culo: </td>
                            <td class='contenido' style='padding:4px;' >
                                <input class='campo_texto' type='text' maxlength='6' value='$_REQUEST[placa]' 
                                       size='6' name='placa' onChange='transporSusp(this.value, \"paca\")'/></td>
                            <td class='contenido' style='padding:4px;' align='right' >
                                Nro. Manifiesto: 
                            </td>
        
                            <td class='contenido' style='padding:4px;' >
                                <input class='campo_texto' type='text' value='$_REQUEST[cod_manifi]' name='cod_manifi' onChange='transporSusp(this.value, \"manifi\")' id='cod_manifiID' $disabled />
                            </td>
                        </tr>
                        <tr>
                            <td  class='contenido' style='padding:4px;' align='center' colspan='4' >
                                <input class='crmButton small save' style='cursor:pointer;' type='button' value='Buscar' onclick='busq_serv()'/>
                            </td>
                        </tr>
                    </table> 
                </div></div>";
        $formulario->oculto("window\" id=\"windowID", "central", 0);
        $formulario->oculto("cod_servic\" id=\"cod_servicID", $_REQUEST['cod_servic'], 0);
        $formulario->oculto("opcion", 1, 0);
        $formulario->oculto("ind_despac", $_REQUEST['ind_despac'], 0);

        $formulario->cerrar();
    }

    function Listar() {
        if ($_REQUEST['ind_qrcode'] == 'yes') {
            include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/AES.inc");
            $key = 25;
            $_ENCRYPT = new AES_functions();
            $d44 = $_ENCRYPT->Decrypt(base64_decode($_REQUEST['cod_manifi']), $key); // MANIFIESTO DESENCRIPTADO
            $d88 = $_ENCRYPT->Decrypt(base64_decode($_REQUEST['placa']), $key); // PLACA DESENCRIPTADA
            $_REQUEST['cod_manifi'] = $d44;
            $_REQUEST['placa'] = $d88;
            $_SESSION['ind_qrcode'] = 'yes';
        }

        $query = "SELECT a.num_despac, a.cod_manifi, c.abr_tercer, d.num_placax, e.abr_tercer, f.nom_ciudad, g.nom_ciudad
                    FROM " . BASE_DATOS . ".tab_despac_despac a
              INNER JOIN " . BASE_DATOS . ".tab_despac_vehige d ON a.num_despac = d.num_despac
              INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer c ON d.cod_transp = c.cod_tercer
              INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer e ON d.cod_conduc = e.cod_tercer
              INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad f ON a.cod_ciuori = f.cod_ciudad
                    AND a.cod_depori = f.cod_depart AND a.cod_paiori = f.cod_paisxx
              INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad g ON a.cod_ciudes = g.cod_ciudad
                    AND a.cod_depdes = g.cod_depart AND a.cod_paides = g.cod_paisxx      
                   WHERE a.fec_salida Is Not Null 
                     AND a.fec_salida <= NOW() 
                     AND a.fec_llegad Is Null 
                     AND a.ind_anulad = 'R' 
                     AND a.ind_planru = 'S' 
                     AND (a.cod_conult != '9999' 
                            OR a.cod_conult !=(SELECT f.cod_contro FROM satt_faro.tab_despac_seguim f WHERE f.num_despac = a.num_despac AND f.cod_rutasx = d.cod_rutasx ORDER BY f.fec_planea DESC LIMIT 1)
                            OR a.cod_conult IS NULL)";

        if ($_REQUEST['ind_despac'] == 0) {
            if ($_REQUEST['cod_manifi'])
                $query .= " AND a.cod_manifi = '" . $_REQUEST['cod_manifi'] . "'";
            elseif ($_REQUEST['placa'])
                $query .= " AND d.num_placax = '" . $_REQUEST['placa'] . "'";
        }

        if ($_REQUEST['ind_despac'] == 2 && $_REQUEST['cod_manifi'])
            $query .= " AND a.cod_manifi = '" . $_REQUEST['cod_manifi'] . "'";

        if ($_REQUEST['ind_despac'] == 1 && $_REQUEST['placa']) {
            if ($_REQUEST['cod_manifi'])
                $query .= " AND a.cod_manifi = '" . $_REQUEST['cod_manifi'] . "'";
            elseif ($_REQUEST['placa'])
                $query .= " AND d.num_placax = '" . $_REQUEST['placa'] . "'";
        }

        $query .= " ORDER BY 1";

        $consulta = new Consulta($query, $this->conexion);
        $VEHICULO = $consulta->ret_matriz();

        if (sizeof($VEHICULO) == 1) {
            $_REQUEST[buffpal] = $VEHICULO[0][num_despac];
            $_REQUEST['placa'] = $VEHICULO[0]['num_placax'];
            $this->ListarDatos();
            die();
        }

        if (!$VEHICULO) {
            if ($_REQUEST['ind_despac'] == 0 && !$_REQUEST['placa'] && $_REQUEST['cod_manifi'])
                $_REQUEST['ind_despac'] = 2;

            if ($_REQUEST['ind_despac'] == 0 && $_REQUEST['placa'] && !$_REQUEST['cod_manifi']) {
                $_REQUEST['ind_despac'] = 1;
                $this->Formulario();
                echo "<script>
                        document.getElementById('cod_manifiID').focus();
                        alert('No se encontraron despachos relacionados con la placa " . $_REQUEST['placa'] . ". \\nPor favor digite un Manifiesto');
                    </script>";
                die();
            } else {
                $this->Formulario();
                echo "<script>
                        if( confirm('No se encontraron despachos relacionados. Desea continuar?') )
                        location.href='index.php?window=central&cod_servic=5001&num_placax=" . $_POST['placa'] . "';
                    </script>";
                die();
            }
        }

        $formulario = new Formulario("index.php", "post", "REGISTRO DE NOVEDADES", "form_ins");
        $formulario->nueva_tabla();
        $formulario->linea("Inserci&oacute;n R&aacute;pida de Novedades P/C. " . sizeof($VEHICULO) . " Despacho(s) en Relaci&oacute;n ", 1, "t2");

        $formulario->nueva_tabla();
        $formulario->linea("Despacho", 0, "t");
        $formulario->linea("Manifiesto", 0, "t");
        $formulario->linea("Origen", 0, "t");
        $formulario->linea("Destino", 0, "t");
        $formulario->linea("Transportadora", 0, "t");
        $formulario->linea("Vehiculo", 0, "t");
        $formulario->linea("Conductor", 1, "t");

        for ($i = 0; $i < sizeof($VEHICULO); $i++) {
            $VEHICULO[$i][0] = "<a href=\"index.php?
                                            cod_servic=" . $_REQUEST[cod_servic] . "&
                                            window=central&
                                            placa=" . $VEHICULO[$i][3] . "&
                                            opcion=datos&buffpal=" . $VEHICULO[$i][0] . " \" 
                                   target=\"centralFrame\" >" . $VEHICULO[$i][0] . "</a>";
            $formulario->linea($VEHICULO[$i][0], 0, "i");
            $formulario->linea($VEHICULO[$i][1], 0, "i"); //Manifiesto
            $formulario->linea($VEHICULO[$i][5], 0, "i"); //Ciudad Origen
            $formulario->linea($VEHICULO[$i][6], 0, "i"); //Ciudad Destino
            $formulario->linea($VEHICULO[$i][2], 0, "i");
            $formulario->linea($VEHICULO[$i][3], 0, "i");
            $formulario->linea($VEHICULO[$i][4], 1, "i");
        }

        $formulario->cerrar();
    }

    function ListarDatos() {
        echo "<style>
                .fotoList
                {
                    background-color:#f7f7f7;
                    text-align:center;
                    padding:10px;
                    margin:20px;
                }
              </style>";

	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.js\"></script>\n";
	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/es.js\"></script>\n";
	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/time.js\"></script>\n";
	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/mask.js\"></script>\n";
	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/functions.js\"></script>\n";
	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/par_confir_pernoc.js\"></script>\n";
	    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/jquery.blockUI.js\"></script>\n";

	    echo "<link rel='stylesheet' href='../".DIR_APLICA_CENTRAL."/estilos/jquery.css' type='text/css'>";
	    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>";
        echo '
        <script>
          jQuery(function($) { 
            
            $( "#date,#fecha" ).datepicker();      
            $( "#hora,#hor" ).timepicker();
            
            $.mask.definitions["A"]="[12]";
            $.mask.definitions["M"]="[01]";
            $.mask.definitions["D"]="[0123]";
            
            $.mask.definitions["H"]="[012]";
            $.mask.definitions["N"]="[012345]";
            $.mask.definitions["n"]="[0123456789]";
            
            $( "#date,#fecha" ).mask("Annn-Mn-Dn");
            $( "#hora" ).mask("Hn:Nn:Nn");
            $( "#hor" ).mask("Hn:Nn");

	        verifyConfirPernoc( 2, $("#num_despacID").val(), $("#cod_controID").val() );
          });
         </script>';

        $formulario = new Formulario("index.php", "post", "REGISTRO DE NOVEDADES", "form_ins\" id=\"form_insID");
        echo "<td>";
        $formulario->oculto("window\" id=\"windowID", "central", 0);
        $formulario->oculto("cod_servic\" id=\"cod_servicID", $_REQUEST[cod_servic], 0);
        $formulario->oculto("opcion", "datos", 0);
        echo "<td></tr>";

        if ($_REQUEST[placa]) {
            if ($_REQUEST[placa] != $_REQUEST[placa_a] && $_REQUEST[placa_a]) {
                $_REQUEST[contro] = NULL;
                $_REQUEST[novedad] = NULL;
                $_REQUEST[tiemp_adicis] = NULL;
                $_REQUEST[obs] = NULL;
                $_REQUEST[placa_a] = $_REQUEST[placa];
            }

            $query = "SELECT a.num_despac, a.cod_manifi, c.abr_tercer as nom_transp, 
                             d.abr_tercer as nom_conduc, e.abr_tercer as nom_client,
                             d.dir_ultfot as fot_conduc, f.nom_agenci, g.ano_modelo,
                             h.nom_marcax, i.nom_colorx, b.cod_conduc, c.cod_tercer,
                             k.nom_ciudad as 'ciu_origen', l.nom_ciudad as 'ciu_destin', m.nom_rutasx as 'nom_rutasx'
                        FROM " . BASE_DATOS . ".tab_despac_despac a
                  INNER JOIN " . BASE_DATOS . ".tab_despac_vehige b ON a.num_despac = b.num_despac 
                  INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer c ON b.cod_transp = c.cod_tercer
                  INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer d ON b.cod_conduc = d.cod_tercer 
                  INNER JOIN " . BASE_DATOS . ".tab_genera_agenci f ON a.cod_agedes = f.cod_agenci
                   LEFT JOIN " . BASE_DATOS . ".tab_vehicu_vehicu g ON b.num_placax = g.num_placax 
                  INNER JOIN " . BASE_DATOS . ".tab_genera_marcas h ON g.cod_marcax = h.cod_marcax                              
                   LEFT JOIN " . BASE_DATOS . ".tab_vehige_colore i ON i.cod_colorx = g.cod_colorx 
                   LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer e ON a.cod_client = e.cod_tercer
                  INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad k ON a.cod_ciuori = k.cod_ciudad
                   AND a.cod_depori = k.cod_depart AND a.cod_paiori = k.cod_paisxx
                  INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad l ON a.cod_ciudes = l.cod_ciudad
                   AND a.cod_depdes = l.cod_depart AND a.cod_paides = l.cod_paisxx
                  INNER JOIN " . BASE_DATOS . ".tab_genera_rutasx m ON b.cod_rutasx = m.cod_rutasx
                       WHERE a.fec_salida IS NOT NULL 
                         AND a.fec_salida <= NOW() 
                         AND a.fec_llegad IS NULL 
                         AND a.ind_anulad = 'R' 
                         AND a.ind_planru = 'S'
                         AND b.num_placax = '" . $_REQUEST[placa] . "' 
                         AND (a.cod_conult != '9999' 
                                OR a.cod_conult !=(SELECT f.cod_contro FROM satt_faro.tab_despac_seguim f WHERE f.num_despac = a.num_despac AND f.cod_rutasx = b.cod_rutasx ORDER BY f.fec_planea DESC LIMIT 1)
                                OR a.cod_conult IS NULL) ";

            if ($_REQUEST[buffpal])
                $query .= " AND a.num_despac = " . $_REQUEST[buffpal] . "";

            $consulta = new Consulta($query, $this->conexion);
            $VEHICULO = $consulta->ret_matriz();

            if (sizeof($VEHICULO) > 1 && !$_REQUEST[buffpal]) {
                $formulario->nueva_tabla();
                $this->Listar($_REQUEST[placa], $formulario);
            } else if ($VEHICULO) {
                $DATOS = $VEHICULO[0];
                $PLACA = strtoupper($_REQUEST[placa]);
                
                
                
                echo "<tr>";
                echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0'>";
                echo "<tr>";
                echo "<td class='celda_titulo2' style='padding:4px;' width='100%' colspan='4' >Informaci&oacute;n B&aacute;sica del Despacho</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td class='fotoList' style='padding:4px;' width='50%' colspan='2' align='center' >
                        <center><table cellspacing='20px'>
                            <tr>
                                <td> 
                                    <center><img src='fotcon/$DATOS[fot_conduc]' onerror='this.src=\"../" . DIR_APLICA_CENTRAL . "/imagenes/conduc.jpg\"; ' width=100 height=120 id='fot_conduc'><br><b>Foto Conductor</b></center>
                                </td>
                                <td> 
                                    <center><img src='../" . DIR_APLICA_CENTRAL . "/images/camara-de-fotos.png' width=100 height=100 onclick='showDialog(this);' id='fot_actual'><br><b>Tomar Foto</b></center>
                                </td>
                            </tr>
                        </table></center></td>";
                echo "<td class='fotoList' style='padding:4px;' width='50%' colspan='2' align='center' >
                        <img src='fotveh/foto1_$_REQUEST[placa].jpg' onerror='this.src=\"../" . DIR_APLICA_CENTRAL . "/imagenes/vehiculo.jpg\"; ' width=120 height=100 ><br><b>Foto Vehiculo</b></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Numero de Despacho: </td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[num_despac]</td>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Transportadora</td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[nom_transp]<input type='hidden' name='cod_tercer' value='$DATOS[cod_tercer]'></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Numero de Manifiesto: </td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[cod_manifi]</td>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Origen</td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[ciu_origen]<input type='hidden' name='nom_conduc' value='$DATOS[nom_conduc]'></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Destino: </td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[ciu_destin]</td>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Ruta</td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[nom_rutasx]<input type='hidden' name='nom_conduc' value='$DATOS[nom_conduc]'></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Cedula Conductor: </td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[cod_conduc]</td>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Conductor</td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[nom_conduc]<input type='hidden' name='nom_conduc' value='$DATOS[nom_conduc]'></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Placa: </td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'><b>$PLACA</b><input type='hidden' name='placa' value='$PLACA' ></td>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Cliente:</td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[nom_client]</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Agencia: </td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[nom_agenci]</td>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Modelo:</td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[ano_modelo]</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Marca: </td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[nom_marcax]</td>";
                echo "<td class='celda_titulo' style='padding:4px;' width='25%' align='right' >Color:</td>";
                echo "<td class='celda_info' style='padding:4px;' width='25%'>$DATOS[nom_colorx]</td>";
                echo "</tr>";
                echo "</table>";

                if ($_REQUEST[codpc])
                    $contro = $_REQUEST[codpc];
                else
                    $contro = $_REQUEST[contro];

                $query = "SELECT b.fec_planea
                            FROM " . BASE_DATOS . ".tab_despac_noveda a
                      INNER JOIN " . BASE_DATOS . ".tab_despac_seguim b ON a.num_despac = b.num_despac AND a.cod_contro = b.cod_contro
                      INNER JOIN " . BASE_DATOS . ".tab_despac_vehige d ON d.num_despac = b.num_despac AND d.cod_rutasx = b.cod_rutasx
                           WHERE d.num_despac = " . $VEHICULO[0][0] . " 
                             AND a.fec_noveda = ( SELECT MAX( c.fec_noveda )
                                                    FROM " . BASE_DATOS . ".tab_despac_noveda c
                                                   WHERE c.num_despac = a.num_despac 
                                                     AND c.cod_rutasx = a.cod_rutasx ) ";

                $consulta = new Consulta($query, $this->conexion);
                $maximo = $consulta->ret_matriz();

                //trae la ultima fecha de la nota de controlador
                $query = "SELECT b.fec_planea 
                            FROM " . BASE_DATOS . ".tab_despac_contro a
                      INNER JOIN " . BASE_DATOS . ".tab_despac_seguim b ON a.num_despac = b.num_despac AND a.cod_contro = b.cod_contro
                      INNER JOIN " . BASE_DATOS . ".tab_despac_vehige d ON d.num_despac = b.num_despac AND d.cod_rutasx = b.cod_rutasx
                           WHERE d.num_despac = " . $VEHICULO[0][0] . " 
                             AND a.fec_contro = ( SELECT MAX( c.fec_contro ) 
                                                    FROM " . BASE_DATOS . ".tab_despac_contro c
                                                   WHERE c.num_despac = a.num_despac 
                                                     AND c.cod_rutasx = a.cod_rutasx ) ";
                $consulta = new Consulta($query, $this->conexion);
                $maximo_c = $consulta->ret_matriz();

                if ($maximo[0][0] > $maximo_c[0][0])
                    $fecplanult = $maximo[0][0];
                else
                    $fecplanult = $maximo_c[0][0];

                // se mira si el perfil tiene un puesto de control designado
                $datos_usuario = $this->usuario->retornar();
                $cod_perfil = $datos_usuario['cod_perfil'];
                $usuario = $datos_usuario['nom_usuari'];
                $query = "SELECT clv_filtro FROM " . BASE_DATOS . ".tab_aplica_filtro_perfil
                           WHERE cod_filtro ='7' 
                             AND cod_perfil ='" . $cod_perfil . "'
                             AND cod_aplica='1'";
                $consulta = new Consulta($query, $this->conexion);
                $puesto = $consulta->ret_matriz();
                $puesto = $puesto[0][0];

                // -- Revisión de los PC por Filtro de Usuario --
                $cod_usuari = $datos_usuario['cod_usuari'];

                $mSelect = "SELECT clv_filtro FROM " . BASE_DATOS . ".tab_aplica_filtro_usuari WHERE cod_usuari = '" . $cod_usuari . "'";
                $consulta = new Consulta($mSelect, $this->conexion);
                $fil_usuari = $consulta->ret_matriz();

                if (sizeof($fil_usuari) > 0) {
                    $clv_filtro = $fil_usuari[0][0];

                    $mSelect = "SELECT cod_contro, cod_homolo FROM " . BASE_DATOS . ".tab_homolo_pcxeal
                                 WHERE cod_contro = '" . $clv_filtro . "' OR cod_homolo = '" . $clv_filtro . "'";
                    $consulta = new Consulta($mSelect, $this->conexion);
                    $homologa = $consulta->ret_matriz();
                    if (!$homologa) {
                        $in_homol[0] = $clv_filtro;
                    } else {
                        $in_homol = array();
                        foreach ($homologa as $row) {
                            if (in_array($row['cod_contro'], $in_homol) === FALSE)
                                $in_homol[] = $row['cod_contro'];

                            if (in_array($row['cod_homolo'], $in_homol) === FALSE)
                                $in_homol[] = $row['cod_homolo'];

                            $mSelect = "SELECT cod_contro, cod_homolo FROM " . BASE_DATOS . ".tab_homolo_pcxeal WHERE cod_contro = '" . $row['cod_contro'] . "'";
                            $consulta = new Consulta($mSelect, $this->conexion);
                            $homolog2 = $consulta->ret_matriz();

                            foreach ($homolog2 as $row2) {
                                if (in_array($row2['cod_homolo'], $in_homol) === FALSE)
                                    $in_homol[] = $row2['cod_homolo'];
                            }
                        }
                    }
                }


                // ----------------------------------------------

                $query = "SELECT b.cod_contro, IF( ( 
                                             SELECT z.nom_contro 
                                               FROM " . BASE_DATOS . ".tab_genera_contro z
                                         INNER JOIN " . BASE_DATOS . ".tab_homolo_ealxxx y ON z.cod_contro = y.cod_pcxfar
                                         INNER JOIN " . BASE_DATOS . ".tab_homolo_trafico x ON y.cod_pcxcli = x.cod_pcxbas AND y.cod_tercer = x.cod_transp
                                              WHERE x.cod_pcxfar = d.cod_contro
                                                AND x.cod_rutfar = d.cod_rutasx
                                                AND x.cod_transp = e.cod_transp
                                                LIMIT 1
                                             )IS NULL, b.nom_contro, 
                                           ( SELECT z.nom_contro 
                                               FROM " . BASE_DATOS . ".tab_genera_contro z
                                         INNER JOIN " . BASE_DATOS . ".tab_homolo_ealxxx y ON z.cod_contro = y.cod_pcxfar 
                                         INNER JOIN " . BASE_DATOS . ".tab_homolo_trafico x ON y.cod_pcxcli = x.cod_pcxbas AND y.cod_tercer = x.cod_transp
                                              WHERE x.cod_pcxfar = d.cod_contro
                                                AND x.cod_rutfar = d.cod_rutasx
                                                AND x.cod_transp = e.cod_transp  LIMIT 1) ),
                                 e.cod_rutasx
                            FROM " . BASE_DATOS . ".tab_genera_contro b  
                      INNER JOIN " . BASE_DATOS . ".tab_despac_seguim d ON b.cod_contro = d.cod_contro
                      INNER JOIN " . BASE_DATOS . ".tab_despac_vehige e ON e.num_despac = d.num_despac AND e.cod_rutasx = d.cod_rutasx
                           WHERE e.num_despac = " . $VEHICULO[0][0] . " ";

                if ($puesto)
                    $query .= " AND b.cod_contro = '" . $puesto . "'";

                if (count($in_homol) > 0)
                    $query .= " AND b.cod_contro IN( " . implode(",", $in_homol) . ")";

                $query .=" ORDER BY d.fec_planea ";
                $consulta = new Consulta($query, $this->conexion);
                $matrizlink = $consulta->ret_matriz();

                if (count($in_homol) > 0 && sizeof($matrizlink) <= 0) {
                    $mens = new mensajes();
                    $mensaje = "<b>Atenci&oacute;n:</b> El plan de Ruta no tiene Homologado y/o Asignado el Puesto de Control. <br>Por favor comuniquese con el CLFARO";
                    $mens->advert("REGISTRO DE NOVEDADES", $mensaje);
                    die();
                }

                $cod_rutasx = $matrizlink[0][2];
                $pcdefecto[0][0] = $matrizlink[0][0];
                $pcdefecto[0][1] = $matrizlink[0][1];

                $inicio[0][0] = 0;
                $inicio[0][1] = "-";

                if ($_REQUEST[contro])
                    $matrizlink = array_merge($inicio, $matrizlink);
                else
                    $matrizlink = array_merge($pcdefecto, $inicio, $matrizlink);

                if ($_REQUEST[contro]) {
                    $query = "SELECT a.cod_contro, a.nom_contro FROM " . BASE_DATOS . ".tab_genera_contro a WHERE a.cod_contro = " . $_REQUEST[contro];
                    $consulta = new Consulta($query, $this->conexion);
                    $contro_a = $consulta->ret_matriz();

                    $matrizlink = array_merge($contro_a, $matrizlink);
                }

                $formulario->nueva_tabla();
                $formulario->linea("Novedades", 1, "t2");

                $formulario->nueva_tabla();
                $formulario->linea("Sitio de Seguimiento", 0, "t");
                $formulario->linea("Fecha/Hora", 0, "t");
                $formulario->linea("Novedad", 0, "t");
                $formulario->linea("Tiempo Solicitado", 0, "t");
                $formulario->linea("Observaciones", 1, "t");

                //PUESTOS DE CONTROL
                $formulario->lista("", "contro", $matrizlink, 0);

                //lista la novedad seleccionada
                $query = "SELECT cod_noveda, UPPER(CONCAT(CONVERT(nom_noveda USING utf8),'',if(nov_especi='1','(NE)',''),if(ind_alarma='S','(GA)',''),if(ind_manala='1','(MA)',''),if(ind_tiempo='1','(ST)','') )), ind_tiempo,nov_especi
                          FROM " . BASE_DATOS . ".tab_genera_noveda 
                          WHERE cod_noveda = '" . $_REQUEST['novedad'] . "'  ";
                $consulta = new Consulta($query, $this->conexion);
                $novedad_a = $consulta->ret_matriz();

                if($_SESSION['datos_usuario']['cod_usuari'] == 'eclaltodeltrigo'){
                    $or = "OR a.cod_noveda = 102 ";
                }
                //lista las novedades
                $query = "SELECT a.cod_noveda, 
                                 UPPER(CONCAT(CONVERT(a.nom_noveda USING utf8),'',
                                 if(a.nov_especi='1','(NE)',''),if(a.ind_alarma='S','(GA)',''),
                                 if(a.ind_manala='1','(MA)',''),if(a.ind_tiempo='1','(ST)','') ))
                            FROM " . BASE_DATOS . ".tab_genera_noveda a 
                       LEFT JOIN " . BASE_DATOS . ".tab_perfil_noveda b ON a.cod_noveda = b.cod_noveda $or
                           WHERE a.cod_noveda != '9998'
                             AND a.ind_visibl = '1'";
                $query .= $_SESSION['datos_usuario']['cod_perfil'] == '' ? " AND a.ind_ealxxx = '1'" : " AND b.cod_perfil = '" . $_SESSION['datos_usuario']['cod_perfil'] . "' ";
                $query .= " GROUP BY a.cod_noveda ORDER BY 2";
                $consulta = new Consulta($query, $this->conexion);
                $novedades = $consulta->ret_matriz();

                $query = "SELECT a.cod_noveda,a.nom_noveda FROM " . BASE_DATOS . ".tab_genera_noveda a ";

                if ($_SESSION['datos_usuario']['cod_perfil'] == '')
                    $query .= " WHERE a.cod_noveda = 71 AND a.ind_ealxxx = '1'";
                else
                    $query .= " WHERE a.cod_noveda = 71";
                $consulta = new Consulta($query, $this->conexion);
                $novdefau = $consulta->ret_matriz();

                if ($_REQUEST[novedad])
                    $novedades = array_merge($novedad_a, $inicio, $novedades);
                else
                    $novedades = array_merge($novdefau, $inicio, $novedades);

                $query = "SELECT MAX( e.fec_noveda )
                           FROM " . BASE_DATOS . ".tab_despac_vehige c 
                     INNER JOIN " . BASE_DATOS . ".tab_despac_seguim d ON c.num_despac = d.num_despac
                     INNER JOIN " . BASE_DATOS . ".tab_despac_noveda e ON c.num_despac = e.num_despac
                          WHERE c.num_despac = " . $VEHICULO[0][0];
                $consulta = new Consulta($query, $this->conexion);

                //fecha del ultimo reporte
                $ultrep = $consulta->ret_matriz();


                if (!$_REQUEST[fecpronov])
                    $feactual = date("Y-m-d H:i");
                else
                    $feactual = $_REQUEST[fecpronov];

                $feactual = str_replace("/", "-", $feactual);
                $fecha = explode(' ', $feactual);
                //fecha Hora Calendario
                echo "<td class='celda_info' >";
                echo "<input type='text' class='campo' size='10' id='date' value = '" . date("Y-m-d", strtotime($feactual)) . "' name='date' >&nbsp;";
                echo "<input type='text' class='campo' size='10' id='hora' value = '" . date("H:i:s", strtotime($feactual)) . "' name='hora' >";
                echo "</td>";

                if (!$novedades) {
                    $formulario->linea("No Existen Novedades Homologadas Para Esta Transportadora", 0, "i");
                    $formulario->oculto("novedad", 0, 0);
                } else{                    
                    $formulario->lista("", "novedad\" id=\"novedadID\" onChange=\"validateNoveda()\"", $novedades, 0);
                }

                if ($novedad_a[0][2] == "1") {
                    $h = date('G');
                    $m = date('i');
                    if ($h <= 9)
                        $h = "0" . $h;
                    if ($m <= 9)
                        $m = "0" . $m;
                    echo "<td class='celda_info' >";
                    echo "<input type='text' class='campo' size='10' id='fecha' name='fecha' value='" . date('Y-m-d') . "'> ";
                    echo "<input type='text' class='campo' size='10' id='hor' name='hor' value='" . $h . ":" . $m . "'>";
                    echo "</td>";
                } else
                    $formulario->linea("", 0, "i");

                //Observaciones
                echo '<td class="celda_info"><textarea wrap="" rows="2" cols="50" id="obsID" name="obs" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto">' . $_REQUEST['obs'] . '</textarea></td>';

                $formulario->nueva_tabla();
                $formulario->linea("Informaci&oacute;n de Inserci&oacute;n", 0, "t2");

                $fec_actual = date("Y-m-d");
                $hor_actual = date("H:i:s");

                $formulario->nueva_tabla();
                $formulario->linea("Fecha de la Novedad (dd-mm-yyyy)", 0);
                $formulario->linea($fec_actual, 1, "i", "72%");
                $formulario->linea("Hora de la Novedad (HH:mm)", 0);
                $formulario->linea("<b>" . $hor_actual . "</b>", 1, "i");

                /*    TRAE LAS DOS ULTIMAS NOVEDADES PARA EL NÚMERO DE DESPACHO    */
                $mSql = " ( SELECT b.nom_contro, a.fec_noveda, c.nom_noveda,
                                   a.des_noveda, a.fec_creaci, a.usr_creaci
                              FROM " . BASE_DATOS . ".tab_despac_noveda a
                        INNER JOIN " . BASE_DATOS . ".tab_genera_contro b ON  a.cod_contro = b.cod_contro 
                        INNER JOIN " . BASE_DATOS . ".tab_genera_noveda c ON  a.cod_noveda = c.cod_noveda 
                             WHERE num_despac = '" . $DATOS[num_despac] . "' 
                          ) UNION(SELECT b.nom_sitiox, a.fec_contro, c.nom_noveda,
                                         a.obs_contro, a.fec_creaci, a.usr_creaci
                                    FROM " . BASE_DATOS . ".tab_despac_contro a
                              INNER JOIN " . BASE_DATOS . ".tab_despac_sitio  b ON a.cod_sitiox = b.cod_sitiox
                              INNER JOIN " . BASE_DATOS . ".tab_genera_noveda c ON a.cod_noveda = c.cod_noveda
                                   WHERE num_despac = '" . $DATOS[num_despac] . "' )                                
                         ORDER BY 5 DESC 
                            LIMIT 2 ";

                $consulta = new Consulta($mSql, $this->conexion);
                $_ULTNOV = $consulta->ret_matriz();
                
                $sql = "SELECT a.cod_partic, a.fec_defini, a.cod_tipser, a.des_partic, a.usr_creaci, b.nom_tipser  
                FROM " . BASE_DATOS . ".tab_partic_tipser a
                    INNER JOIN " . BASE_DATOS . ".tab_genera_tipser b ON a.cod_tipser = b.cod_tipser
                    WHERE cod_transp = '$DATOS[cod_tercer]' AND a.cod_tipser =" . CON_TIPSER_OAL ."";
    
                    
    
                $consulta = new Consulta($sql, $this-> conexion);
                $particularidades = $consulta->ret_matrix("a");
                $formulario->nueva_tabla();
                $formulario->linea("Particularidades", 0, "t2");
                echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0' >";
                    echo "<tr>";
                        echo "<th width='10%' class='cellHead' >N�</th>";                        
                        echo "<th width='40%' class='cellHead'>Pareticularidad del servicio</th>";
                    echo"</tr>";
                    foreach ($particularidades as $row  => $value) {
                        $i = $row +1;  
                     echo "<tr>";
                         echo"<td align='center' width='10%' class='cellInfo' style='border: 1px #c3c3c3 solid'> $i </td>"; 
                         echo "<td align='center' width='40%' class='celda_info'  style='border: 1px #c3c3c3 solid'> $value[des_partic] </td>";                                        
                     echo "</tr>";                                
                    }
                            
                echo"</table>";
                if ($_ULTNOV && count($_ULTNOV) > 0) {
                    echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/homolo.css' type='text/css'>\n";
                    $formulario->nueva_tabla();
                    $formulario->linea("&Uacute;litmas Novedades del Despacho", 0, "t2");
                    echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0' >";

                    echo "<tr>";
                    echo "<td class='cellHead' width='20%' >Puesto Control</td>";
                    echo "<td class='cellHead' width='10%' >Fecha/Hora Novedad</td>";
                    echo "<td class='cellHead' width='15%' >Novedad</td>";
                    echo "<td class='cellHead' width='35%' >Observaci&oacute;n</td>";
                    echo "<td class='cellHead' width='10%' >Fecha/Hora Sistema</td>";
                    echo "<td class='cellHead' width='10%' >Usuario</td>";
                    echo "</tr>";
                    foreach ($_ULTNOV as $row) {
                        echo "<tr class='row'>";
                        echo "<td class='cellInfo' width='20%' >" . $row['nom_contro'] . "</td>";
                        echo "<td class='cellInfo' width='10%' >" . $row['fec_noveda'] . "</td>";
                        echo "<td class='cellInfo' width='15%' >" . $row['nom_noveda'] . "</td>";
                        echo "<td class='cellInfo' width='35%' >" . $row['des_noveda'] . "</td>";
                        echo "<td class='cellInfo' width='10%' >" . $row['fec_creaci'] . "</td>";
                        echo "<td class='cellInfo' width='10%' >" . $row['usr_creaci'] . "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else
                    $formulario->linea("&Uacute;litmas Novedades del Despacho", 0, "t2");

                echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0' >";
                echo "<tr>";
                echo "<td class='cellHead' width='100%' >El Despacho no Posee Novedades Anteriores </td>";
                echo "</tr>";
                
                $formulario->nueva_tabla();

                if ($novedad_a[0][2] == "1") {
                    $formulario->oculto("soltie", 1, 0);
                } else {
                    $formulario->oculto("soltie", 0, 0);
                }
                $formulario->oculto("nov_especi\" id=\"nov_especiID\" ", $novedad_a[0][nov_especi], 0);
                $formulario->oculto("usuario", "$usuario", 0);
                $formulario->oculto("tercero", "$tercero", 0);
                $formulario->oculto("fecnov", "$fec_actual", 0);
                $formulario->oculto("cod_rutasx", "$cod_rutasx", 0);
                $formulario->oculto("hornov", "$hor_actual", 0);
                $formulario->oculto("buffpal", $_REQUEST[buffpal], 0);
                $formulario->oculto("despac\" id=\"num_despacID", $VEHICULO[0][0], 0);
                $formulario->oculto("central\" id=\"central", DIR_APLICA_CENTRAL, 0);
                $formulario->oculto("ultrep", $ultrep[0][0], 0);
                $formulario->oculto("standa",DIR_APLICA_CENTRAL , 0);
                $formulario->oculto("cod_conduc",$DATOS[cod_conduc] , 0);
                $formulario->oculto("cod_contro\" id=\"cod_controID", self::getContro($VEHICULO[0][0]), 0);
                $formulario->oculto("bin_fotcon","" , 0);
                $formulario->oculto("dateSystem\" id=\"dateSystemID\" ", date('Y-m-d H:i:s'), 0);
                
                $formulario->nueva_tabla();
                $formulario->boton("Aceptar", "button\" onClick=\"aceptar_ins()", 0);
                $formulario->boton("Borrar", "reset", 1);
                echo "<br/>";
                $formulario->nueva_tabla();
                echo "<br/>";
                echo "<br/>";
                echo "<br/>";
                echo "<br/>";
                echo "<br/>";
            } else {
                $query = "SELECT a.num_placax FROM " . BASE_DATOS . ".tab_vehicu_vehicu a WHERE a.num_placax = '" . $_REQUEST[placa] . "' ";
                $consulta = new Consulta($query, $this->conexion);
                $vehi_exi = $consulta->ret_matriz();

                $formulario->nueva_tabla();

                if ($vehi_exi)
                    $formulario->linea("El Vehiculo con Placas " . $_REQUEST[placa] . " no se Encuentra en Ruta.", "e");
                else
                    $formulario->linea("El Vehiculo con Placas " . $_REQUEST[placa] . " no se Encuentra Registrado en el Sistema.", "e");
            }
        }

        if ($_REQUEST[placa_a])
            $formulario->oculto("placa_a", $_REQUEST[placa_a], 0);
        else
            $formulario->oculto("placa_a", $_REQUEST[placa], 0);

        $formulario->cerrar();
    }

    function Insertar() {
        $fec_actual = date("Y-m-d H:i:s");
        $query = "SELECT TIMEDIFF( '" . $_REQUEST['fecha'] . " " . $_REQUEST['hor'] . "', NOW() ) ";
        $consulta = new Consulta($query, $this->conexion);
        $TIME_DIFF = $consulta->ret_matriz();
        $TIME_DIFF = explode(":", $TIME_DIFF[0][0]);
        $tiemp_adicis = $TIME_DIFF[0] * 60 + $TIME_DIFF[1];
        $_REQUEST[fecpronov] = str_replace("/", "-", $_REQUEST[fecpronov]);

        $regist["despac"] = $_REQUEST[despac];
        $regist["contro"] = $_REQUEST[contro];
        $regist["noveda"] = $_REQUEST[novedad];
        $regist["tieadi"] = $tiemp_adicis;
        $regist["observ"] = $_REQUEST[obs];
        $regist["fecnov"] = $_REQUEST['date'] . " " . $_REQUEST['hora'];
        $regist["fecact"] = $fec_actual;
        $regist["ultrep"] = $_REQUEST[ultrep];
        $regist["rutax"] = $_REQUEST[cod_rutasx];
        $regist["usuari"] = $_SESSION['datos_usuario']['cod_usuari'];
        $regist["bin_fotcon"] = $_REQUEST["bin_fotcon"];
        $regist["cod_conduc"] = $_REQUEST["cod_conduc"];

        if ($_SESSION['ind_qrcode'] == 'yes') {
            $regist["observ"] .= '. Insertado por Código QR.';
            unset($_SESSION['ind_qrcode']);
        }

        $consulta = new Consulta("SELECT NOW()", $this->conexion, "BR");
        
        include_once("../" . DIR_APLICA_CENTRAL . "/despac/InsertNovedad.inc");
        $transac_nov = new InsertNovedad($_REQUEST[cod_servic], $_REQUEST[opcion], $this->cod_aplica, $this->conexion);
 
        $RESPON = $transac_nov->InsertarNovedadPC(BASE_DATOS, $regist, 0);
        $transac_nov->loadImage($regist["despac"], $regist["bin_fotcon"],$_REQUEST["contro"]);

        if ($RESPON[0]["indica"]) {
            $consulta = new Consulta("COMMIT", $this->conexion);
            $mensaje = $RESPON[0]["mensaj"];                    
                    if($_REQUEST[novedad] == 102 && $_REQUEST['cod_tercer'] == 900503325 && 
                        ($_SESSION["satt_movil"]["cod_usuari"] == 'eclaltodeltrigo' || $_SESSION["satt_movil"]["cod_usuari"] == 'ealgranada' || $_SESSION["satt_movil"]["cod_usuari"] == 'GRANADA1'  ) )
                    {
                        $regist['placa'] = $_REQUEST['placa'];
                        $regist['nom_conduc'] = $_REQUEST['nom_conduc'];
                        $this->sendEmail($regist, $_REQUEST['cod_tercer']);
                    }

            for ($i = 1; $i < sizeof($RESPON); $i++) {
                if ($RESPON[$i]["indica"]){
                    $mensaje .= "<br><img src=\"../" . DIR_APLICA_CENTRAL . "/imagenes/ok.gif\">" . $RESPON[$i]["mensaj"];
                } else {
                    $mensaje .= "<br><img src=\"../" . DIR_APLICA_CENTRAL . "/imagenes/error.gif\">" . $RESPON[$i]["mensaj"];
                }
            }

            $mens = new mensajes();
            $mens->correcto("REGISTRO DE NOVEDADES", $mensaje);
        } else {
            $mensaje = $RESPON[0]["mensaj"];
            $mens = new mensajes();
            $mens->advert("REGISTRO DE NOVEDADES", $mensaje);
        }
    }
    
    /*! \fn: getContro
     *  \brief: Trae el PC para la novedad
     *  \author: Ing. Fabian Salinas
     *  \date: 15/03/2016
     *  \date modified: dd/mm/aaaa
     *  \param: mNumDespac  Integer  Numero Del Despacho
     *  \return: Matriz
     */
    private function getContro( $mNumDespac ){
    	$mSql = "SELECT a.cod_contro 
				   FROM ".BASE_DATOS.".tab_genera_usuari a 
				  WHERE a.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."' ";

		$mSql = "SELECT x.cod_contro 
				   FROM ".BASE_DATOS.".tab_despac_seguim x 
			 INNER JOIN (
							 (
								 SELECT b.cod_homolo AS cod_contro 
								   FROM ".BASE_DATOS.".tab_homolo_pcxeal b 
								  WHERE b.cod_contro IN ( $mSql ) 
							 ) UNION (
							 	$mSql 
							 )
  						) y 
					 ON x.cod_contro = y.cod_contro 
				  WHERE x.num_despac = '$mNumDespac' 
				";
		$mConsult = new Consulta($mSql, $this -> conexion);
		$mResult = $mConsult -> ret_matrix('i');

		return $mResult[0][0];
    }

    /* ! \fn: sendEmail
     *  \brief: envia un email al cliente
     *  \author: Ing. Alexander Correa
     *  \date: 27/06/2016
     *  \date modified: dia/mes/año
     *  \param: $datos => array => datos necesarios para enviar el email   
     *  \param: $cod_tercer => string => para sacar la direccion de email de la transportadora   
     *  \return
     */
    private function sendEmail ($datos, $cod_tercer) {
        $sql = "SELECT dir_emailx FROM ".BASE_DATOS.".tab_tercer_tercer WHERE cod_tercer = $cod_tercer";
        $consulta = new Consulta($sql, $this->conexion);
        $email = $consulta->ret_matrix('a');
        $email = $email[0]['dir_emailx']; 
        $cabeceras = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $cabeceras .= 'From: '.MAIL_SUPERVISORES;

        
        $html = "<table width='100%' cellpadding='0' cellspacing='1'>
                    <tr>
                        <td colspan='2' style='background-color:#35650f;color:#ffffff;font-family:Times New Roman;font-size:14px;padding:4px;text-align: center;'>
                            <b>Reporte EAL Alto del Trigo</b>
                        </td>
                    </tr>
                    <tr>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Despacho:</td>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>$datos[despac]</td>
                    </tr>
                    <tr>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Puesto de Control:</td>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>EAL ALTO DEL TRIGO</td>
                    </tr>
                    <tr>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Placa:</td>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>$datos[placa]</td>
                    </tr>
                    <tr>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Conductor:</td>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>$datos[nom_conduc]</td>
                    </tr>
                    <tr>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Novedad:</td>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>OK EAL SIN NOVEDAD</td>
                    </tr>                   
                    <tr>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Fecha de Novedad:</td>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>$datos[fecnov]</td>
                    </tr>
                    <tr>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Fecha Actual:</td>
                        <td style='background-color: #ebf8e2;font-family: Times New Roman;font-size: 11px;padding: 2px;'>$datos[fecact]</td>
                    </tr>                    
                    <tr>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>Observaci&oacute;n:</td>
                        <td style='background-color: #dedfde;font-family: Times New Roman;font-size: 11px;padding: 2px;'>$datos[observ]</td>
                    </tr>                   
                </table>";  
        if(HOST_WEB == HOST_WEB_PRO){
            return mail($email, 'Reporte Novedad EAL Alto del Trigo',$html, $cabeceras);
        }else{
            return mail("leidy.acosta@intrared.net", 'Reporte Novedad EAL Alto del Trigo',$html, $cabeceras);
        }      
     }

 
}

$proceso = new Proc_despac($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>