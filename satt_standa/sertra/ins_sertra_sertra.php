<?php
/* * **************************************************************************
  NOMBRE:   MODULO CONFIGURAR TIPO DE SERVICIO DE LA TRANSPORTADORA
  FUNCION:  CONFIGURAR TIPO SERVICIO TRANSP
  AUTOR: HUGO MALAGON
  FECHA CREACION : 20 OCTUBRE 2010
 * ************************************************************************** */

class Proc_alerta {

    var $conexion,
        $usuario; //una conexion ya establecida a la base de datos

    function __construct($co, $us, $ca) {
        $this->conexion = $co;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        $datos_usuario = $this->usuario->retornar();

        if (!isset($_REQUEST[opcion]))
            $this->FormularioBusqueda();
        else {
            switch ($_REQUEST[opcion]) {
                case "1":
                    $this->Formulario();
                    break;
                case "2":
                    $this->Insertar();
                    break;
                case "3":
                    $this->Resultado();
                    break;
            }
        }
    }

    function Resultado() {
		//Codigo del Tercero.
        $cod_tercer = explode("-", $_POST[busq_transp]);
        $cod_tercer = trim($cod_tercer[0]);

		//Nombre del Tercero.
        $nom_tercer = explode("-", $_POST[busq_transp]);
        $nom_tercer = trim($nom_tercer[1]);

        if ($nom_tercer == '')
            $nom_tercer = $cod_tercer;

        if (trim($_POST[busq_transp]) == "") {
			//Lista todas las transportadoras
            $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer,
					IF(c.num_consec IS NULL, 'NO', 'SI') AS ind_config
					FROM " . BASE_DATOS . ".tab_tercer_activi b,
					" . BASE_DATOS . ".tab_tercer_tercer a LEFT JOIN 
					" . BASE_DATOS . ".tab_transp_tipser c ON a.cod_tercer = c.cod_transp 
					WHERE a.cod_estado = '1' AND
					a.cod_tercer = b.cod_tercer AND
					b.cod_activi = " . COD_FILTRO_EMPTRA . " 
					GROUP BY 1
					ORDER BY 2 ASC ";
            $consec = new Consulta($query, $this->conexion);
            $matriz = $consec->ret_matriz();
        } else {
			//Lista las transportadoras que coincidan con el nit dado
            $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer,
					IF(c.num_consec IS NULL, 'NO', 'SI') AS ind_config
					FROM " . BASE_DATOS . ".tab_tercer_activi b,
					" . BASE_DATOS . ".tab_tercer_tercer a LEFT JOIN 
					" . BASE_DATOS . ".tab_transp_tipser c ON a.cod_tercer = c.cod_transp 
					WHERE a.cod_estado = '1' AND
					a.cod_tercer = b.cod_tercer AND
					a.cod_tercer LIKE '%" . $cod_tercer . "%' AND
					b.cod_activi = " . COD_FILTRO_EMPTRA . " 
					GROUP BY 1
					ORDER BY 2 ASC ";
            $consec = new Consulta($query, $this->conexion);
            $matriz = $consec->ret_matriz();
            if (!$matriz) {
				//Lista las transportadoras que coincidan con el nombre
                $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer,
							IF(c.num_consec IS NULL, 'NO', 'SI') AS ind_config
							FROM " . BASE_DATOS . ".tab_tercer_activi b,
							" . BASE_DATOS . ".tab_tercer_tercer a LEFT JOIN 
							" . BASE_DATOS . ".tab_transp_tipser c ON a.cod_tercer = c.cod_transp 
							WHERE a.cod_estado = '1' AND
							a.cod_tercer = b.cod_tercer AND
							a.nom_tercer LIKE '%" . $nom_tercer . "%' AND
							b.cod_activi = " . COD_FILTRO_EMPTRA . " 
							GROUP BY 1
							ORDER BY 2 ASC ";
                $consec = new Consulta($query, $this->conexion);
                $matriz = $consec->ret_matriz();
                if (!$matriz) {
					//Lista las transportadoras que coincidan con la abreviatura
                    $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer, UPPER(a.abr_tercer) AS abr_tercer,
							IF(c.num_consec IS NULL, 'NO', 'SI') AS ind_config
							FROM " . BASE_DATOS . ".tab_tercer_activi b,
							" . BASE_DATOS . ".tab_tercer_tercer a LEFT JOIN 
							" . BASE_DATOS . ".tab_transp_tipser c ON a.cod_tercer = c.cod_transp 
							WHERE a.cod_estado = '1' AND
							a.cod_tercer = b.cod_tercer AND
							a.abr_tercer LIKE '%" . $nom_tercer . "%' AND
							b.cod_activi = " . COD_FILTRO_EMPTRA . " 
							GROUP BY 1
							ORDER BY 2 ASC ";
                    $consec = new Consulta($query, $this->conexion);
                    $matriz = $consec->ret_matriz();
                }
            }
        }

        if (sizeof($matriz) == 1) {
			//Si retorna 1 solo resultado se redirecciona hacia la captura final
            $_REQUEST[cod_transp] = $matriz[0][0];
            $this->Formulario();
        } else {
            $datos_usuario = $this->usuario->retornar();
            $usuario = $datos_usuario["cod_usuari"];

            $formulario = new Formulario("index.php", "post", "Configurar Tipo de Servicio de Transportadoras", "form_item");
            $formulario->linea("Se Encontro un Total de " . sizeof($matriz) . " Transportadoras(s) para la b&uacute;squeda " . " \" " . $_POST[busq_transp] . " \" ", 0, "t2");
            $formulario->nueva_tabla();

            if (sizeof($matriz) > 0) {
                $formulario->linea("NIT", 0, "t");
                $formulario->linea("Nombre", 0, "t");
                $formulario->linea("Abreviatura", 0, "t");
                $formulario->linea("Configurada", 1, "t");

                for ($i = 0; $i < sizeof($matriz); $i++) {
                    $matriz[$i][0] = "<a href=\"index.php?cod_servic=$_REQUEST[cod_servic]&window=central&cod_transp=" . $matriz[$i][0] . "&opcion=1 \"target=\"centralFrame\">" . $matriz[$i][0] . "</a>";
                    $formulario->linea($matriz[$i][0], 0, "i");
                    $formulario->linea($matriz[$i][1], 0, "i");
                    $formulario->linea($matriz[$i][2], 0, "i");
                    $formulario->linea($matriz[$i][3], 1, "i");
                }
            }

            $formulario->nueva_tabla();
            $formulario->oculto("usuario", "$usuario", 0);
            $formulario->oculto("opcion", 1, 0);
            $formulario->oculto("valor", $valor, 0);
            $formulario->oculto("window", "central", 0);
            $formulario->oculto("cod_servic", $_REQUEST[cod_servic], 0);
            $formulario->botoni("Volver", "javascript:history.go(-1)", 0);

            $formulario->cerrar();
        }
    }

    function FormularioBusqueda() {
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";

        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";



        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/regnov.js\"></script>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";

		//Definicion de Estilos.
        echo "<style>
				.celda_titulo2{
					border-right:1px solid #AAA;
					font-size:12px;
					width:20%;
				}

				.celda_info{
					width:20%;
					text-align:center;
				}

				.campo{
					border:1px solid #CCC;
					text-transform:uppercase;
				}

				.info{
					border:0px;
					text-align:center;
				}			

				.ui-autocomplete-loading{ 
					background: white url('../" . DIR_APLICA_CENTRAL . "/estilos/images/ui-anim_basic_16x16.gif') right center no-repeat; 
				}	

				.ui-corner-all{
					cursor:pointer;
				}

				/*.ui-autocomplete{
					max-height: 200px;
					height: 200px;
					overflow-y: auto;
				}*/
			</style>";

        $query = "SELECT a.cod_tercer, UPPER( a.abr_tercer ) as abr_tercer
					FROM " . BASE_DATOS . ".tab_tercer_tercer a,
					" . BASE_DATOS . ".tab_tercer_activi b
					WHERE a.cod_tercer = b.cod_tercer AND
					b.cod_activi = " . COD_FILTRO_EMPTRA . "
					ORDER BY 2";
        $consulta = new Consulta($query, $this->conexion);
        $transpor = $consulta->ret_matriz();

        echo '
			<script>
				$(function() {
				var tranportadoras = 
				[';

        if ($transpor) {
            echo "\"Ninguna\"";
            foreach ($transpor as $row) {
                echo ", \"$row[cod_tercer] - $row[abr_tercer]\"";
            }
        };

        echo ']
				$( "#busq_transp" ).autocomplete({
					source: tranportadoras,
					delay: 100
				}).bind( "autocompleteclose", function(event, ui){$("#form_insID").submit();} );

				$( "#busq_transp" ).bind( "autocompletechange", function(event, ui){$("#form_insID").submit();} ); 
				});

			</script>';

        $formulario = new Formulario("index.php", "post", "TIPO DE SERVICIO", "formulario");
        echo "<td>";
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_servic", $_REQUEST[cod_servic], 0);
        $formulario->oculto("opcion", 3, 0);
        echo "<td></tr>";
        echo "<tr>";
        echo "<table width='100%' border='0' class='tablaList' align='center' cellspacing='0' cellpadding='0'>";
        echo "<tr>";
        echo "<td class='celda_titulo2' style='padding:4px;' width='100%' colspan='4' >B&uacute;squeda de Transportadora</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' align='right' >
				Nit / Nombre: </td>";
        echo "<td class='celda_titulo' style='padding:4px;' width='50%' colspan='2' >
				<input class='campo_texto' type='text' size='25' name='busq_transp' id='busq_transp' onblur='formulario.submit()' /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td  class='celda_etiqueta' style='padding:4px;' align='center' colspan='4' >
				<input class='crmButton small save' style='cursor:pointer;' type='button' value='Buscar' onclick='formulario.submit()'/></td>";
        echo "</tr>";
        echo "</table></td>";
        $formulario->cerrar();
    }

    function Formulario() {
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
        echo '
			<script>
				jQuery(function($) {
					$.mask.definitions["H"]="[01]";
					$.mask.definitions["N"]="[03]";
					$.mask.definitions["n"]="[0123456789]";
					$.mask.definitions["s"]="[0]";

					$( "#tie_trazabID" ).mask("Hn:Ns");
				});

				function ValidateTime( campo ){
					if( $(campo).val().split(":")[0] > 12 ){
						alert("Rango maximo 12:00 Horas");
						$(campo).val("");
						$(campo).focus();
					}else if( $(campo).val().split(":")[0] == 12 && $(campo).val().split(":")[1] > 0 ){
						alert("Rango maximo 12:00 Horas");
						$(campo).val("");
						$(campo).focus();
					}
				}
			</script>';

        $cod_transp = $_REQUEST['cod_transp'];
        $datos_usuario = $this->usuario->retornar();
        $usuario = $datos_usuario["cod_usuari"];

        $inicio[0][0] = 0;
        $inicio[0][1] = "-";

		//Servidores.
        $query = "SELECT cod_server, nom_server
				FROM " . CENTRAL . ".tab_genera_server
				WHERE ind_estado = '1' ";

        if ($_POST[cod_server])
            $query .= " AND cod_server = '$_POST[cod_server]' ";

        $query .= " ORDER BY 2 ";

        $consulta = new Consulta($query, $this->conexion);
        $server = $consulta->ret_matriz();

        if (!$_POST[cod_server])
            $server = array_merge($inicio, $server);
        else
            $server = array_merge($server, $inicio);

		//Trae el nombre de la transportadora
        $query = "SELECT UPPER(abr_tercer)
					FROM " . BASE_DATOS . ".tab_tercer_tercer
					WHERE cod_tercer = '" . $cod_transp . "' ";
        $consulta = new Consulta($query, $this->conexion);
        $nom_transp = $consulta->ret_matriz();

		//trae array con los tipos de servicio para el combobox
        $query = "SELECT cod_tipser, nom_tipser
				FROM " . BASE_DATOS . ".tab_genera_tipser
				WHERE ind_estado = '1' ";
        $consulta = new Consulta($query, $this->conexion);
        $tipser = $consulta->ret_matriz();
        $tipser = array_merge($inicio, $tipser);

        $tip_servic = array(0 => array(0 => "1", 1 => "Por Despacho"), 1 => array(0 => "2", 1 => "Por Registro"));
        $tip_servic = array_merge($inicio, $tip_servic);

		//Trae el ultimo tipo de servicio configurado para una transportadora
        $query = "SELECT MAX(num_consec) AS num_consec
					FROM " . BASE_DATOS . ".tab_transp_tipser a
					WHERE a.cod_transp = '" . $cod_transp . "'";
        $consult = new Consulta($query, $this->conexion);
        $matriz_consec = $consult->ret_matriz();
        $lastConsec = $matriz_consec ? $matriz_consec [0][0] : FALSE;

        if ($lastConsec) {
			//trae los datos de la ultima configuracion
            $query = "SELECT a.cod_tipser, a.tie_contro, a.ind_estado, 
					a.tie_conurb, a.ind_llegad, a.cod_server, 
					a.ind_notage, a.tip_factur, a.tie_carurb,
					a.tie_carnac, a.tie_carimp, a.tie_carexp,
					a.tie_desurb, a.tie_desnac, a.tie_desimp, 
					a.tie_desexp, a.tie_trazab, a.ind_excala, 
					a.ind_calcon, a.ind_segcar, a.ind_segtra, 
					a.ind_segdes, a.val_regist, a.tie_cartr1, 
					a.tie_cartr2, a.tie_destr1, a.tie_destr2, 
					a.ind_camrut, a.dup_manifi, a.ind_biomet, 
                    a.can_llaurb, a.can_llanac, a.can_llaimp, 
                    a.can_llaexp, a.can_llatr1, a.can_llatr2 
					FROM " . BASE_DATOS . ".tab_transp_tipser a
					WHERE a.cod_transp = '" . $cod_transp . "' AND 
					a.num_consec = '" . $lastConsec . "'";
            $consult = new Consulta($query, $this->conexion);
            $matriz = $consult->ret_matriz();

            if ($matriz) {
                $query = "SELECT cod_tipser, nom_tipser
						FROM " . BASE_DATOS . ".tab_genera_tipser
						WHERE ind_estado = '1' AND 
						cod_tipser = '" . $matriz[0][0] . "' ";
                $consulta = new Consulta($query, $this->conexion);
                $tipser_sel = $consulta->ret_matriz();
                $tipser = array_merge($tipser_sel, $tipser);

                if ($matriz[0][5]) {
                    $query = "SELECT cod_server, nom_server
							FROM " . CENTRAL . ".tab_genera_server
							WHERE ind_estado = '1'
							AND cod_server = '" . $matriz[0][5] . "'";
                    $consulta = new Consulta($query, $this->conexion);
                    $act_server = $consulta->ret_matriz();

                    $server = array_merge($act_server, $server);
                }

                if ($matriz[0][7] && $matriz[0][7] != '0') {
                    $act_servic[0][0] = $matriz[0][7];
                    $act_servic[0][1] = $matriz[0][7] == '1' ? 'Por Despacho' : 'Por Registro';

                    $tip_servic = array_merge($act_servic, $tip_servic);
                }
            }
        } else {
			//Configuracion Activa al insertar
            $matriz[0][2] = 1;
        }

        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/sertra.js\"></script>\n";

        $formulario = new Formulario("index.php", "post", "Configurar Tipo de Servicio de Transportadoras", "ins_sertra");

        $formulario->linea("Datos Básicos", 1, "t2");

        $formulario->nueva_tabla();
        $formulario->linea("Nombre Transportadora", 0, "t", NULL, NULL, 'right');
        $formulario->linea($nom_transp[0][0], 1, "i");
        $formulario->lista("Tipo Servicio ", "cod_tipser\" onChange=\"onChangeTipServic()", $tipser, 1);
        $formulario->lista("Servidor: ", "cod_server", $server, 1);

        $tie_visibility = $matriz[0][0] == '1' || $matriz[0][0] == '' ? ' style="display:none"' : '';

        $formulario->texto("Tiempo Para Despachos Nacionales(Min)", "text", "tie_contro\"  id=\"tie_controID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][1]);
        $formulario->texto("Tiempo Para Despachos Urbanos(Min)", "text", "tie_conurb\"  id=\"tie_conurbID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][3]);

        if ($matriz[0][4] == '1')
            $formulario->caja("LLegada Automatica:", "ind_llegad", "1", 1, 1);
        else
            $formulario->caja("LLegada Automatica:", "ind_llegad", "1", 0, 1);

        if ($matriz[0][2] == '1')
            $formulario->caja("Activa:", "ind_estado", "1", 1, 1);
        else
            $formulario->caja("Activa:", "ind_estado", "1", 0, 1);

        if ($matriz[0][6] == '1')
            $formulario->caja("Notificar Agencias:", "ind_notage", "1", 1, 1);
        else
            $formulario->caja("Notificar a correos por Agencia:", "ind_notage", "1", 0, 1);

        if ($matriz[0][19] == '1')
            $formulario->caja("Seguimiento Cargue:", "ind_segcar", "1", 1, 1);
        else
            $formulario->caja("Seguimiento Cargue:", "ind_segcar", "1", 0, 1);

        if ($matriz[0][20] == '1')
            $formulario->caja("Seguimiento Transito:", "ind_segtra", "1", 1, 1);
        else
            $formulario->caja("Seguimiento Transito:", "ind_segtra", "1", 0, 1);

        if ($matriz[0][21] == '1')
            $formulario->caja("Seguimiento Descargue:", "ind_segdes", "1", 1, 1);
        else
            $formulario->caja("Seguimiento Descargue:", "ind_segdes", "1", 0, 1);

        if ($matriz[0][ind_camrut] == '1')
            $formulario->caja("Cambio Plan de Ruta:", "ind_camrut", "1", 1, 1);
        else
            $formulario->caja("Cambio Plan de Ruta:", "ind_camrut", "1", 0, 1);

        /*         * ******************************************************************************************************************************************************************** */
        if ($matriz[0][16] != 0) {
            $mins = $matriz[0][16] / 60;
            $Mmm = explode(".", $mins);
            $mins = $Mmm[0];
            $segs = $matriz[0][16] % 60;
            if ($mins < 10)
                $mins = "0" . $mins;
            if ($segs == 0)
                $segs = "00";
            $matriz[0][16] = $mins . ":" . $segs;
        }

        $formulario->lista("Tipo de Servicio: ", "tip_factur", $tip_servic, 1);
        $formulario->texto("Valor Registro:", "text", "val_regist\" id=\"val_registID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][22]);
        $formulario->texto("Tiempo Trazabilidad Diaria:", "text", "tie_trazab\" id=\"tie_trazabID\" onchange=\"ValidateTime( this )", 1, 4, 5, "", $matriz[0][16]);
        $formulario->oculto("ori_trazab\" id=\"ori_trazabID", "", 0);


        if ($matriz[0][ind_calcon] == '1')
            $formulario->caja("Transmitir Despachos Finalizados y Calificaci&oacute;n de Conductores a la RIT:", "ind_calcon", "1", 1, 1);
        else
            $formulario->caja("Transmitir Despachos Finalizados y Calificaci&oacute;n de Conductores a la RIT:", "ind_calcon", "1", 0, 1);
		
		#nuevo campo para sabr si se permite o no duplicar un manifiesto
        if ($matriz[0][dup_manifi] == '1') {
            $val = 1;
        } else {
            $val = 0;
        }

        $formulario->caja("Permitir duplicar manifiesto:", "dup_manifi", "1", $val, 1);

        if ($matriz[0][ind_biomet] == '1') {
            $val = 1;
        } else {
            $val = 0;
        }
        $formulario->caja("Contratar Biometr&iacute;a:", "ind_biomet", "1", $val, 1);
        /*         * ******************************************************************************************************************************************************************** */

        /**    SECCION TIEMPOS PENDIENTES CARGUE ********************************************** */
        $formulario->nueva_tabla();
        $formulario->linea("Configuraci&oacute;n Tiempos Control de Cargue", 0, "h");
        
        $formulario->nueva_tabla();
        $formulario->linea("&nbsp;", 0, "h");
        $formulario->linea("Tiempos de Control", 0, "h");
        $formulario->linea("N&uacute;mero de LLamadas", 1, "h");
		//a.,a., a., a. 
        $formulario->texto("Despachos Urbanos(Min)", "text", "tie_carurb\" id=\"tie_carurbID\" onChange=\" BlurNumeric(this);", 0, 2, 3, "", $matriz[0][8]);
        echo '<td class="celda_info">
				<input type="text" maxlength="3" value="'.$matriz[0]['can_llaurb'].'" size="2" onchange=" BlurNumeric(this);" id="can_llaurbID" name="can_llaurb" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto">
			  </td></tr>';
        $formulario->texto("Despachos Nacionales(Min)", "text", "tie_carnac\" id=\"tie_carnacID\" onChange=\" BlurNumeric(this);", 0, 2, 3, "", $matriz[0][9]);
        echo '<td class="celda_info">
                <input type="text" maxlength="3" value="'.$matriz[0]['can_llanac'].'" size="2" onchange=" BlurNumeric(this);" id="can_llanacID" name="can_llanac" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto">
              </td></tr>';
        $formulario->texto("Despachos Importaci&oacute;n(Min)", "text", "tie_carimp\" id=\"tie_carimpID\" onChange=\" BlurNumeric(this);", 0, 2, 3, "", $matriz[0][10]);
        echo '<td class="celda_info">
                <input type="text" maxlength="3" value="'.$matriz[0]['can_llaimp'].'" size="2" onchange=" BlurNumeric(this);" id="can_llaimpID" name="can_llaimp" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto">
              </td></tr>';
        $formulario->texto("Despachos Exportaci&oacute;n(Min)", "text", "tie_carexp\" id=\"tie_carexpID\" onChange=\" BlurNumeric(this);", 0, 2, 3, "", $matriz[0][11]);
        echo '<td class="celda_info">
                <input type="text" maxlength="3" value="'.$matriz[0]['can_llaexp'].'" size="2" onchange=" BlurNumeric(this);" id="can_llaexpID" name="can_llaexp" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto">
              </td></tr>';
        $formulario->texto("Despachos Tramo 1(Min)", "text", "tie_cartr1\" id=\"tie_cartr1ID\" onChange=\" BlurNumeric(this);", 0, 2, 3, "", $matriz[0]['tie_cartr1']);
        echo '<td class="celda_info">
                <input type="text" maxlength="3" value="'.$matriz[0]['can_llatr1'].'" size="2" onchange=" BlurNumeric(this);" id="can_llatr1ID" name="can_llatr1" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto">
              </td></tr>';
        $formulario->texto("Despachos Tramo 2(Min)", "text", "tie_cartr2\" id=\"tie_cartr2ID\" onChange=\" BlurNumeric(this);", 0, 2, 3, "", $matriz[0]['tie_cartr2']);
        echo '<td class="celda_info">
                <input type="text" maxlength="3" value="'.$matriz[0]['can_llatr2'].'" size="2" onchange=" BlurNumeric(this);" id="can_llatr2ID" name="can_llatr2" onblur="this.className=\'campo_texto\'" onfocus="this.className=\'campo_texto_on\'" class="campo_texto">
              </td></tr>';

        /*         * ************************************************************************************ */

        /**    SECCION TIEMPOS PENDIENTES DESCARGUECARGUE ********************************************** */
        $formulario->nueva_tabla();
        $formulario->linea("Configuraci&oacute;n Tiempos Control de Descargue", 1, "h");
		//a.,a., a., a. 
        $formulario->nueva_tabla();
        $formulario->texto("Despachos Urbanos(Min)", "text", "tie_desurb\" id=\"tie_desurbID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][12]);
        $formulario->texto("Despachos Nacionales(Min)", "text", "tie_desnac\" id=\"tie_desnacID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][13]);
        $formulario->texto("Despachos Importaci&oacute;n(Min)", "text", "tie_desimp\" id=\"tie_desimpID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][14]);
        $formulario->texto("Despachos Exportaci&oacute;n(Min)", "text", "tie_desexp\" id=\"tie_desexpID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][15]);
        $formulario->texto("Despachos Tramo 1(Min)", "text", "tie_destr1\" id=\"tie_destr1ID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][tie_destr1]);
        $formulario->texto("Despachos Tramo 2(Min)", "text", "tie_destr2\" id=\"tie_destr2ID\" onChange=\" BlurNumeric(this);", 1, 2, 3, "", $matriz[0][tie_destr2]);

        /*         * ************************************************************************************ */

        $formulario->nueva_tabla();
        $formulario->oculto("usuario", "$usuario", 0);
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_transp", $cod_transp, 0);
        $formulario->oculto("opcion", 3, 0);
        $formulario->oculto("cod_servic", $_REQUEST["cod_servic"], 1);
        $formulario->botoni("Aceptar", "aceptar_insert() ", 0);
        $formulario->botoni("Volver", "javascript:history.go(-1)", 0);
        $formulario->cerrar();
    }

    function Insertar() {
        $cod_transp = $_REQUEST['cod_transp'];
        $datos_usuario = $this->usuario->retornar();
        $usuario = $datos_usuario["cod_usuari"];
		
		//trae el consecutivo de la tabla
        $query = "SELECT MAX(num_consec) AS num_consec
		FROM " . BASE_DATOS . ".tab_transp_tipser a
		WHERE a.cod_transp = '" . $cod_transp . "'";
        $consec = new Consulta($query, $this->conexion);
        $ultimo = $consec->ret_matriz();

        $ultimo_consec = $ultimo[0][0];
        $nuevo_consec = $ultimo_consec + 1;
        $_REQUEST[ind_estado] = $_REQUEST[ind_estado] == '1' ? '1' : '0';
        $_REQUEST[ind_llegad] = $_REQUEST[ind_llegad] == '1' ? '1' : '0';
        $_REQUEST[ind_notage] = $_REQUEST[ind_notage] == '1' ? '1' : '0';
        $_REQUEST[ind_calcon] = $_REQUEST[ind_calcon] == '1' ? '1' : '0';

        if ($_REQUEST['ori_trazab'] == 'NaN') {
            $_REQUEST['ori_trazab'] = 0;
        }
        $duplicado = $_REQUEST[dup_manifi];
        if (!$duplicado) {
            $duplicado = 0;
        }
		
		//query de insercion
        $query = "INSERT INTO " . BASE_DATOS . ".tab_transp_tipser
				(	num_consec, cod_transp, cod_tipser,
					tie_contro, ind_estado, ind_llegad, 
					fec_creaci, usr_creaci, tie_conurb,
					cod_server, ind_notage, tip_factur,
					tie_carurb, tie_carnac, tie_carimp,
					tie_carexp, tie_desurb, tie_desnac, 
					tie_desimp, tie_desexp, tie_trazab,
					ind_calcon, ind_segcar, ind_segtra, 
					ind_segdes, val_regist, tie_cartr1, 
					tie_cartr2, tie_destr1, tie_destr2, 
					ind_camrut, dup_manifi, ind_biomet, 
                    can_llaurb, can_llanac, can_llaimp, 
                    can_llaexp, can_llatr1, can_llatr2 
				) 
				VALUES ('$nuevo_consec','$cod_transp','$_REQUEST[cod_tipser]', 
						'$_REQUEST[tie_contro]', '$_REQUEST[ind_estado]', '$_REQUEST[ind_llegad]', 
						NOW(), '" . $usuario . "', '$_REQUEST[tie_conurb]', 
						'$_REQUEST[cod_server]', '$_REQUEST[ind_notage]','$_REQUEST[tip_factur]', 
						'" . $_REQUEST['tie_carurb'] . "', '" . $_REQUEST['tie_carnac'] . "', '" . $_REQUEST['tie_carimp'] . "', '" . $_REQUEST['tie_carexp'] . "',
						'" . $_REQUEST['tie_desurb'] . "', '" . $_REQUEST['tie_desnac'] . "', '" . $_REQUEST['tie_desimp'] . "', '" . $_REQUEST['tie_desexp'] . "', " . $_REQUEST['ori_trazab'] . ",
						'" . $_REQUEST[ind_calcon] . "', '" . $_REQUEST[ind_segcar] . "', '" . $_REQUEST[ind_segtra] . "', '" . $_REQUEST[ind_segdes] . "', '" . $_REQUEST[val_regist] . "', 
						'" . $_REQUEST[tie_cartr1] . "', '" . $_REQUEST[tie_cartr2] . "', '" . $_REQUEST[tie_destr1] . "', '" . $_REQUEST[tie_destr2] . "', '" . $_REQUEST[ind_camrut] . "', '" . $duplicado . "','" . $_REQUEST[ind_biomet] . "', 
                        '" . $_REQUEST[can_llaurb] . "', '" . $_REQUEST[can_llanac] . "', '" . $_REQUEST[can_llaimp] . "', 
                        '" . $_REQUEST[can_llaexp] . "', '" . $_REQUEST[can_llatr1] . "', '" . $_REQUEST[can_llatr2] . "' 
				) ";
        $consulta = new Consulta($query, $this->conexion, "BR");

        if ($insercion = new Consulta("COMMIT", $this->conexion)) {
            $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=" . $_REQUEST[cod_servic] . " \"target=\"centralFrame\">Configurar el Tipo de Servicio de Otra Transportadora</a></b>";

            $mensaje = "Se Inserto el Tipo de Servicio para la transportadora <b>" . $_REQUEST[cod_transp] . "</b> con Exito" . $link_a;
            $mens = new mensajes();
            $mens->correcto("INSERTAR TIPO SERVICIO", $mensaje);
        }
    }
}

//FIN CLASE Proc_alerta
$proceso = new Proc_alerta($this->conexion, $this->usuario_aplicacion, $this->codigo);
?>
