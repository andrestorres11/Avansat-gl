<?php

ini_set('memory_limit', '1024M');
session_start();

class Proc_est_finali {

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
        if (!isset($_REQUEST[opcion]))
            $this->Listars();
        else {
            switch ($_REQUEST[opcion]) {
                case "0":
                    $this->Listars();
                    break;

                case "1":
                    $this->Listar();
                    break;

                case "2":
                    $this->Datos();
                    break;

                case "3":
                    $this->getConductores();
                    break;
            }
        }
    }

    function Listars() {
        $datos_usuario = $this->usuario->retornar();

        if (!$_REQUEST[cribus])
            $_REQUEST[cribus] = 1;

        $inicio[0][0] = 0;
        $inicio[0][1] = "-";

        $query = "SELECT a.cod_tercer, UPPER( a.nom_tercer ) AS nom_tercer
                FROM " . BASE_DATOS . ".tab_tercer_tercer a,
                " . BASE_DATOS . ".tab_tercer_activi b,
                " . BASE_DATOS . ".tab_despac_vehige c
                WHERE a.cod_estado = '1'
                AND a.cod_tercer = b.cod_tercer
                AND b.cod_activi = '1'
                AND a.cod_tercer = c.cod_transp ";

        if ($datos_usuario["cod_perfil"] == "") {
        //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND c.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
        } else {
        //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND c.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
        }

        $query .= " GROUP BY 1 ORDER BY 2 ASC";
        $consulta = new Consulta($query, $this->conexion);
        $transpors = $consulta->ret_matriz();

        $transpors = array_merge($inicio, $transpors);

        $query = "SELECT e.cod_tercer,e.abr_tercer
                FROM " . BASE_DATOS . ".tab_despac_despac a,
                " . BASE_DATOS . ".tab_despac_vehige d,
                " . BASE_DATOS . ".tab_tercer_tercer e,
                " . BASE_DATOS . ".tab_vehicu_vehicu i
                WHERE a.num_despac = d.num_despac AND
                a.cod_client = e.cod_tercer AND
                i.num_placax = d.num_placax AND
                a.fec_salida Is Not Null AND
                a.fec_salida <= NOW() AND
                a.fec_llegad IS NOT NULL AND
                a.ind_anulad = 'R' AND
                a.ind_planru = 'S' ";

        if ($datos_usuario["cod_perfil"] == "") {
        //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE PROPIETARIO
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_PROPIE, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE POSEEDOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_POSEED, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        } else {
        //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE PROPIETARIO
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_PROPIE, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND i.cod_propie = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE POSEEDOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_POSEED, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND i.cod_tenedo = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }

        $query = $query . " GROUP BY 1 ORDER BY 2";

        $consulta = new Consulta($query, $this->conexion);
        $cliente = $consulta->ret_matriz();

        $cliente = array_merge($inicio, $cliente);

        $feactual = date("Y-m-d");


        //------------------------------------
        echo "<link rel=\"stylesheet\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/dinamic_list.css\" type=\"text/css\">";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/dinamic_list.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/noveda.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/es.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/time.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mask.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/regnov.js\"></script>\n";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";

        //Definicion de Estilos.
        echo "<style>
                .celda_titulo2
                {
                    border-right:1px solid #AAA;
                    font-size:12px;
                    width:20%;
                }

                .celda_info
                {
                    width:20%;
                    text-align:center;
                }

                .campo
                {
                    border:1px solid #CCC;    
                    text-transform:uppercase;
                }

                .info
                {
                    border:0px;   
                    text-align:center;          
                }     

                .ui-autocomplete-loading 
                { 
                    background: white url('../" . DIR_APLICA_CENTRAL . "/estilos/images/ui-anim_basic_16x16.gif') right center no-repeat; 
                } 

                .ui-corner-all
                {
                    cursor:pointer;
                }

                /*.ui-autocomplete 
                {
                    max-height: 200px;
                    height: 200px;
                    overflow-y: auto;
                }*/
            </style>";

        $query = "SELECT a.cod_tercer,UPPER( a.nom_tercer ) AS nom_conduc, UPPER( a.nom_apell1 ) AS ape_conduc
                FROM " . BASE_DATOS . ".tab_tercer_tercer a 
                LEFT JOIN " . BASE_DATOS . ".tab_transp_tercer b ON a.cod_tercer = b.cod_tercer
                LEFT JOIN " . BASE_DATOS . ".tab_tercer_conduc c ON a.cod_tercer = c.cod_tercer";
        $consulta = new Consulta($query, $this->conexion);
        $conductor = $consulta->ret_matriz();

        echo '
            <script>
                jQuery(function($) {
                    $( "#busq_transp" ).autocomplete({
                        source: "../satt_standa/inform/inf_despac_finali.php?opcion=3",
                        minLength: 1, 
                        delay: 100
                    });
                });
            </script>';
        //----------------------------------
        $formulario = new Formulario("index.php", "post", "Despachos Finalizados", "form_fecha", "");

        $formulario->linea("Seleccione las Condiciones de Busqueda.", 1, "t2");

        $formulario->nueva_tabla();
        $formulario->lista("Transportadora", "transpor", $transpors, 1);
        $formulario->lista("Cliente", "client", $cliente, 1);
        $formulario->texto("Vehiculo", "text", "placin", 1, 6, 6, "", "");
        $formulario->texto("Despacho", "text", "despac", 1, 6, 11, "", "");
        $formulario->texto("No. Viaje", "text", "num_viajex", 1, 15, 11, "", "");
        $formulario->nueva_tabla();
        $formulario->linea("Seleccione Conductor.", 1, "t2");
        echo "<td></tr>";
        echo "<tr>";
        echo "<table width='50%' border='0' class='tablaList' align='left' cellspacing='0' cellpadding='0'>";
        echo "<tr>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='celda_titulo' style='padding:4px;' width='30%' colspan='2' align='right' >C&eacute;dula/Nombre </td>";
        echo "<td class='celda_titulo' style='padding:4px;' width='20%' colspan='2' align='left' >
                <input class='campo_texto' type='text' size='40' name='busq_transp' id='busq_transp' /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "</tr>";
        echo "</table></td>";
        $formulario->nueva_tabla();
        $formulario->linea("Seleccione el Rango de Fechas.", 1, "t2");

        $formulario->nueva_tabla();
        $formulario->caja("Filtrar Por Fechas (S/N)", "ind_fec", "1", "1", 1);
        $formulario->fecha_calendar("Fecha Inicial", "fecini", "form_fecha", $feactual, "yyyy/mm/dd", 0);
        $formulario->fecha_calendar("Fecha Final", "fecfin", "form_fecha", $feactual, "yyyy/mm/dd", 1);

        $formulario->oculto("opcion", 1, 0);
        $formulario->oculto("window", "central", 0);
        $formulario->oculto("cod_servic", $_REQUEST[cod_servic], 0);

        $formulario->nueva_tabla();
        $formulario->botoni("Aceptar", "form_fecha.submit()", 0);
        $formulario->cerrar();
    }

    function Listar() {
        $datos_usuario = $this->usuario->retornar();
        $fechaini = str_replace("/", "-", $_REQUEST[fecini]) . " 00:00:00";
        $fechafin = str_replace("/", "-", $_REQUEST[fecfin]) . " 23:59:59";

        if ($_REQUEST["busq_transp"]) {
            $busq_transp = explode('-', $_REQUEST["busq_transp"]);
            $_REQUEST["busq_transp"] = trim($busq_transp[0]);
        }
        if ($_REQUEST[ind_fec])
            $validmatriz[]["linea"] = " AND a.fec_llegad BETWEEN '" . $fechaini . "' AND '" . $fechafin . "'";
        if ($_REQUEST[transpor])
            $validmatriz[]["linea"] = " AND d.cod_transp = '" . $_REQUEST[transpor] . "'";
        if ($_REQUEST[client])
            $validmatriz[]["linea"] = " AND a.cod_client = '" . $_REQUEST[client] . "'";
        if ($_REQUEST[placin])
            $validmatriz[]["linea"] = " AND d.num_placax = '" . $_REQUEST[placin] . "'";

        if ($_REQUEST['num_viajex'] != '')
            $validmatriz[]["linea"] = " AND k.num_desext = '" . $_REQUEST['num_viajex'] . "'";

        if ($_REQUEST[ind_fec]) {
            $_REQUEST_ADD[0]["campo"] = "fecini";
            $_REQUEST_ADD[0]["valor"] = $_REQUEST[fecini];
            $_REQUEST_ADD[1]["campo"] = "fecfin";
            $_REQUEST_ADD[1]["valor"] = $_REQUEST[fecfin];
        }
        $_REQUEST_ADD[2]["campo"] = "transpor";
        $_REQUEST_ADD[2]["valor"] = $_REQUEST[transpor];
        $_REQUEST_ADD[3]["campo"] = "client";
        $_REQUEST_ADD[3]["valor"] = $_REQUEST[client];
        $_REQUEST_ADD[4]["campo"] = "rutasx";
        $_REQUEST_ADD[4]["valor"] = $_REQUEST[rutasx];
        $_REQUEST_ADD[5]["campo"] = "contro";
        $_REQUEST_ADD[5]["valor"] = $_REQUEST[contro];
        $_REQUEST_ADD[6]["campo"] = "placin";
        $_REQUEST_ADD[6]["valor"] = $_REQUEST[placin];
        $_REQUEST_ADD[7]["campo"] = "noveda";
        $_REQUEST_ADD[7]["valor"] = $_REQUEST[noveda];
        $_REQUEST_ADD[8]["campo"] = "cribus";
        $_REQUEST_ADD[8]["valor"] = $_REQUEST[cribus];
        $_REQUEST_ADD[9]["despac"] = "despac";
        $_REQUEST_ADD[9]["valor"] = $_REQUEST[despac];
        $condespe[0]["cribus"] = $_REQUEST[cribus];

        $listado_prin = new Despachos($_REQUEST[cod_servic], 2, $this->cod_aplica, $this->conexion, 1);
        $listado_prin->ListadoPrincipal($datos_usuario, 0, "Despachos Finalizados", 0, $validmatriz, $_REQUEST_ADD, 1, 0, 0, $condespe);
    }

    function getObsLLegada($num_despac) {
        $mSql = "SELECT obs_llegad, usr_modifi FROM " . BASE_DATOS . ".tab_despac_despac WHERE num_despac = " . $num_despac;
        $consulta = new Consulta($mSql, $this->conexion);
        $observ = $consulta->ret_matriz();
        return $observ[0];
    }

    function Datos() {
        $datos_usuario = $this->usuario->retornar();

        $mRuta = array("link" => 0, "finali" => 1, "opcurban" => 0, "lleg" => NULL, "tie_ultnov" => NULL); #Fabian
        $listado_prin = new Despachos($_REQUEST[cod_servic], 2, $this->cod_aplica, $this->conexion);

        $despac_llegada = $this->getObsLLegada($_REQUEST['despac']);


        $mHtml = new Formlib(2);

        $mHtml->Form(array("target" => "_self", "action" => "index.php", "method" => "post", "name" => "form_item"), true);
        $mHtml->SetBody($listado_prin->Encabezado($_REQUEST[despac], $datos_usuario, 1, $mRuta, true));

        $mHtml->OpenDiv("id:contentID; class:contentAccordion");

        $mHtml->OpenDiv("id:llegadaID; class:accordion");
        $mHtml->SetBody("<h3 style='padding:6px;'><center>INFORMACION LLEGADA</center></h3>");
        $mHtml->OpenDiv("id:secID");
        $mHtml->OpenDiv("id:form_llegadaID; class:contentAccordionForm");
        $mHtml->Table("tr");

        $mHtml->Label("Observaciones de LLegada", array("class" => "celda_titulo2", "align" => "left"));
        $mHtml->Label("Usuario LLegada", array("class" => "celda_titulo2", "align" => "left"));
        $mHtml->CloseRow();
        $mHtml->Row();
        $mHtml->Label($despac_llegada[0], array("class" => "celda_info", "align" => "left"));
        $mHtml->Label($despac_llegada[1], array("class" => "celda_info", "align" => "left"));
        $mHtml->CloseRow();
        $mHtml->Row('td');
        $mHtml->Hidden(array("name" => "despac", "id" => "despacID", "value" => $_REQUEST[despac]));
        $mHtml->Hidden(array("name" => "opcion", "id" => "opcionID", "value" => $_REQUEST[opcion]));
        $mHtml->Hidden(array("name" => "window", "id" => "windowID", "value" => "central"));
        $mHtml->Hidden(array("name" => "cod_servic", "id" => "cod_servicID", "value" => $_REQUEST[cod_servic]));
        $mHtml->SetBody('</td>');

        $mHtml->CloseTable("tr");
        $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        $mHtml->CloseDiv();

        $mHtml->CloseDiv();
        $mHtml->CloseForm();

        echo $mHtml->MakeHtml();
    }

    function getConductores() {
        global $HTTP_POST_FILES;
        $BASE = $_SESSION[BASE_DATOS];
        define('BASE_DATOS', $_SESSION['BASE_DATOS']);
        include( "../lib/general/conexion_lib.inc" );
        include( "../lib/general/form_lib.inc" );
        include( "../lib/general/tabla_lib.inc" );
        $conexion = new Conexion("bd10.intrared.net:3306", $_SESSION[USUARIO], $_SESSION[CLAVE], $BASE);
        $term = $_REQUEST['term'];

        $query = "SELECT a.cod_tercer,TRIM( UPPER( a.nom_tercer ) ) AS nom_conduc, 
                TRIM( UPPER( a.nom_apell1 ) ) AS ape_conduc, 
                TRIM( UPPER( a.nom_apell2 ) ) AS ape_conduc2
                FROM " . BASE_DATOS . ".tab_tercer_tercer a 
                LEFT JOIN " . BASE_DATOS . ".tab_transp_tercer b ON a.cod_tercer = b.cod_tercer
                LEFT JOIN " . BASE_DATOS . ".tab_tercer_conduc c ON a.cod_tercer = c.cod_tercer
                WHERE CONCAT(a.nom_tercer,' ',a.nom_apell1,' ',a.nom_apell2) LIKE '%" . $term . "%' OR
                CONCAT(a.nom_tercer,' ',a.nom_apell1) LIKE '%" . $term . "%' OR
                CONCAT(a.nom_apell1,' ',a.nom_apell2) LIKE '%" . $term . "%' OR 
                CONCAT(a.nom_tercer,' ',a.nom_apell2) LIKE '%" . $term . "%' OR
                CONCAT(a.nom_apell2,' ',a.nom_tercer) LIKE '%" . $term . "%' OR
                CONCAT(a.nom_apell1,' ',a.nom_apell2,' ',a.nom_tercer) LIKE '%" . $term . "%' OR
                a.cod_tercer LIKE '%" . $term . "%' OR 
                a.nom_apell2 LIKE '%" . $term . "%' OR 
                a.nom_apell1 LIKE '%" . $term . "%'
                GROUP BY a.nom_tercer
                LIMIT 0 , 10 ";
        $consulta = new Consulta($query, $conexion);
        $conductores = $consulta->ret_matriz();

        $data = array();
        for ($i = 0, $len = count($conductores); $i < $len; $i++) {
            $data [] = '{"label":"' . $conductores[$i][0] . " - " . $conductores[$i][1] . " " . $conductores[$i][2] . " " . $conductores[$i][3] . '","value":"' . $conductores[$i][0] . " - " . $conductores[$i][1] . " " . $conductores[$i][2] . " " . $conductores[$i][3] . '"}';
        }
        echo '[' . join(', ', $data) . ']';
    }
}

$proceso = new Proc_est_finali($_SESSION['conexion'], $_SESSION['usuario_aplicacion'], $_SESSION['codigo']);
?>