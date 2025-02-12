<?php
// ini_set("display_errors", true);
// error_reporting(E_ALL ^ E_NOTICE);
/* !\class: Mod_Vehicu_Colore
 *  \brief: Módulo de listado y administrador de Colores
 *  \author: Ing. Jesus Sanchez
 *  \date: 29/04/2024
 */

class Mod_Vehicu_Colore
{
    var $conexion = NULL,
        $usuario = NULL,
        $cod_aplica = NULL;

    /* !\fn: __construct
     *  \brief: Función Constructora
     *  \author: Ing. Jesus Sanchez
     *    \date: 29/04/2024
     *  \param1:$conexion (objeto vectorial de conexión a bases de datos)
     *  \param2:$us (Objeto vectorial con datos del usuario)
     *  \param3:$ca (Cadena que indica si se aplica al código)
     *  \return NADA, pero redirige a la función de menú
     */
    function __construct($conexion, $us, $ca) {
        $this->conexion = $conexion;
        $this->usuario = $us;
        $this->cod_aplica = $ca;
        //$datos_usuario = $this -> usuario -> retornar();
        $this->Menu();
    }

    /* !\fn: Menu
     *  \brief: Navegabilidad por el módulo
     *  \author: Ing. Jesus Sanchez
     *    \date: 29/04/2024
     *  \param1:NINGUNO
     *  \return NADA, pero redirige a la función de menú
     */
    function Menu() {
        switch ($_REQUEST["option"]) {
            case "1":
                $this->Listar_Colores(array(0, ""));
            break;
            case "2":
                $vec_respon = $this->Insertar_Colores();
                $this->Listar_Colores($vec_respon);
            break;
            case "5":
                $vec_respon = $this->ActivarColores();
                $this->Listar_Colores($vec_respon);
            break;
            default:
                $this->Listar_Colores(array(0, ""));
            break;
        }
    }

    /* ! \fn: Listar_Colores
     *  \brief: Muestra el listado de Colores, a la vez de manejar un formulario de inserción/actualización
     *  \author: Ing. Jesus Sanchez
     *    \date: 29/04/2024
     *  \param1:$vec_respon (Vector con la respuesta de cualquier operación de modificación de registros)
     *  \return NADA, pero pinta el formulario central
     */
    function Listar_Colores($vec_respon)
    {
        echo '<script language="javascript">
                $(document).ready(function(){
                    $("#tbl_coloreID thead th").each( function () {
                        var title = $(this).text();
                        if(title != "#") {
                            $(this).html( title + "<br /><input type=\'text\' placeholder=\'"+title+"\' />" );
                        }
                    } );
                    var table = $("#tbl_coloreID").DataTable({
                        paging: true,
                        bPaginate: true,
                        dom: "Bfrtip",
                        buttons: [
                            {
                                extend: "excelHtml5",
                                title: "Lista_Colores_" + Date()
                            }
                        ], 
                        "order": [[ 2, "asc" ]]
                    });
                    table.columns().every( function () {
                        var that = this;
                        $( "input", this.header() ).on( "keyup change", function () {
                            if ( that.search() !== this.value ) {
                                that
                                    .search( this.value )
                                    .draw();
                            }
                        } );
                    } );
                });
                </script>';

        $datos_usuario = $this->usuario->retornar();
        $usuario = $datos_usuario["cod_usuari"];

        ini_set("memory_limit", "128M");
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/sweetalert.min.js\"></script>\n";
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/sweetalert.css\">\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/ins_vehicu_colore.js\"></script>\n";

        /*! \brief: Se implementará DATATABLES para generar los resultados en pantalla y en excel
         */
        echo '<link rel="stylesheet" type="text/css" href="../'.DIR_APLICA_CENTRAL.'/css/jquery.dataTables.min.css">';
        echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>';
        echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/jquery.dataTables.min.js"></script>';
        echo '<link rel="stylesheet" type="text/css" href="../'.DIR_APLICA_CENTRAL.'/estilos/bootstrap/3.3.7/css/bootstrap.min.css">';
        echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/estilos/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
        /*! \brief: Vamos a llamar las liberías encargadas de la exportación a Excel
         */
        echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/js_export/buttons.flash.min.js"></script>';
        echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/js_export/buttons.html5.min.js"></script>';
        echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/js_export/buttons.print.min.js"></script>';
        echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/js_export/dataTables.buttons.min.js"></script>';
        echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/js_export/jszip.min.js"></script>';
        echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/js_export/pdfmake.min.js"></script>';
        echo '<script type="text/javascript" src="../'.DIR_APLICA_CENTRAL.'/js/js_export/vfs_fonts.js"></script>';
        echo '<link rel="stylesheet" type="text/css" href="../'.DIR_APLICA_CENTRAL.'/css/css_export/buttons.dataTables.min.css">';

        echo "<script language='javascript'>
                    LockAplication('lock');
                    AjaxLoader('block');
                </script>";

        $form = new Formulario("action:index.php?cod_servic=" . $_REQUEST["cod_servic"] . "; method:post; name:form_colore;");

        if($vec_respon[0] == "1")
        {
            echo "<td>";
            ShowMessage("s", "Colores", $vec_respon[1]);
            echo "</td>";
        }
        else if($vec_respon[0] == "2")
        {
            echo "<td>";
            ShowMessage("a", "Colores", $vec_respon[1]);
            echo "</td>";
        }
        else if($vec_respon[0] == "3")
        {
            echo "<td>";
            ShowMessage("e", "Colores", $vec_respon[1]);
            echo "</td>";
        }

        $query_consec = "SELECT MAX(a.cod_colorx) FROM ".BASE_DATOS.".tab_vehige_colore a";
        
        $consulta_consec = new Consulta($query_consec, $this->conexion);
        $matriz_consec = $consulta_consec->ret_matriz();

        $consec = $matriz_consec[0][0] + 1;

        $form->oculto("name:cod_colore; value:0; ");
        $form->oculto("name:option; value:2; ");
        $form->oculto("name:window; value:central; ");
        $form->oculto("name:cod_servic; value:".$_REQUEST['cod_servic']."; ");
        $form->oculto("name:usr_creaci; value:" . $usuario . ";");
        $form->oculto("name:standar; value:" . DIR_APLICA_CENTRAL);

        $form->nueva_tabla();
        $form->linea("COLORES", "t2", 0, 0, "left", "", "", "");
        $form->cerrar_tabla();

        $query = "SELECT a.cod_colorx, a.nom_colorx, b.ind_estado, b.cod_mintra AS mintra_cliente FROM ".BD_STANDA.".tab_vehige_colore a 
                            LEFT JOIN ".BASE_DATOS.".tab_vehige_colore b ON a.cod_mintra = b.cod_mintra";

        $consulta = new Consulta($query, $this->conexion);
        $matriz = $consulta->ret_matriz();

        echo "<td>";
        echo "<br />";
        // echo "<div class='container' style='margin-left:inherit;'>";
        echo "<div class='container'>";
        echo "<table class='table table-hover' id='tbl_coloreID'>";
        echo "<thead>";
        echo "<tr class='celda_titulo'>";
        echo "<th>CONSECUTIVO</th>";
        echo "<th>CODIGO COLOR</th>";
        echo "<th>COLOR</th>";
        echo "<th>ACTIVAR / DESACTIVAR COLOR</th>";
        // echo "<th>ELIMINAR COLOR</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        $i = 0;

        $mensaje = "";
        if(sizeof($matriz) > 0)
        {
            $num_longit = sizeof($matriz);
            for($i = 0; $i < $num_longit; $i++)
            {
                if($matriz[$i]["mintra_cliente"] != NULL && $matriz[$i]["ind_estado"] == "1"){
                    $mensaje = "Desactivar";
                }else if($matriz[$i]["mintra_cliente"] != NULL && $matriz[$i]["ind_estado"] == "0"){
                    $mensaje = "Activar";
                }else{
                    $mensaje = "Insertar";
                }

                $celdas = "celda";
                if($i%2 != 0)
                {
                    $celdas = "celda2";
                }
                echo "<tr>";
                echo "<td class='".$celdas."'>".utf8_decode($matriz[$i]["cod_colorx"])."</td>";
                echo "<td class='".$celdas."'>".utf8_decode($matriz[$i]["cod_colorx"])."</td>";
                echo "<td class='".$celdas."'>".utf8_decode(utf8_encode($matriz[$i]["nom_colorx"]))."</td>";

                if(strpos(utf8_decode(utf8_encode($matriz[$i]["nom_colorx"])),'"') !== false){ // Si encuentra una comilla doble, la cambia por `
                    $color_cambiado = str_replace('"','`',utf8_decode(utf8_encode($matriz[$i]["nom_colorx"])));
                }else{
                    $color_cambiado = utf8_decode(utf8_encode($matriz[$i]["nom_colorx"]));
                }

                echo "<td class='".$celdas."'><a href='#' onclick='javascript:activarColores(\"".$matriz[$i]["cod_colorx"]."\", \"".$matriz[$i]["mintra_cliente"]."\", \"".$color_cambiado."\", \"".$matriz[$i]["ind_estado"]."\")'>".$mensaje."</a></td>";
                echo "</tr>";
            }
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        echo "<br>";

        echo "</td>";
        $form->cerrar();

        echo "<script language='javascript'>
                    AjaxLoader('none');
                    LockAplication('unlock');
                </script>";
    }

    /* ! \fn: ActivarColores
     *  \brief: Permite la activación manual de los colores, si no esta presente en el cliente la inserta
     *  \author: Ing. Jesus Sanchez
     *    \date: 25/04/2019
     *  \param1:NINGUNO
     *  \return Un vector con el indicador de aviso (1:Success/2:Warning/3:Error), y la descripción del mensaje
     */
    function ActivarColores() {
        $vec_mensaj = array();

        $vec_mensaj[0] = "1";
        $vec_mensaj[1] = "";

        $sql_valida = "SELECT a.cod_colorx, a.nom_colorx, b.ind_estado, b.cod_mintra AS mintra_cliente FROM ".BD_STANDA.".tab_vehige_colore a 
                            LEFT JOIN ".BASE_DATOS.".tab_vehige_colore b ON a.cod_colorx = b.cod_mintra WHERE a.cod_colorx = '".$_REQUEST['cod_colore']."'";

        $con_valida = new Consulta($sql_valida, $this->conexion);
        $val_colorx = $con_valida->ret_matriz();


        $init = new Consulta("START TRANSACTION", $this->conexion);

        if($val_colorx[0]["mintra_cliente"] != NULL && $val_colorx[0]["ind_estado"] == "1"){
            $mensaje = "Desactivado";
            /* VAMOS A ACTUALIZAR OPERADOR */
            $query_1 = "UPDATE " . BASE_DATOS . ".tab_vehige_colore
                    SET ind_estado = IF(ind_estado = '1', '0', '1'), 
                    usr_modifi = '".$_REQUEST['usr_creaci']."', 
                    fec_modifi = NOW()
                    WHERE cod_mintra = '".$_REQUEST['cod_colore']."' 
                ";
        }else if($val_colorx[0]["mintra_cliente"] != NULL && $val_colorx[0]["ind_estado"] == "0"){
            $mensaje = "Activado";
            /* VAMOS A ACTUALIZAR OPERADOR */
            $query_1 = "UPDATE " . BASE_DATOS . ".tab_vehige_colore
                    SET ind_estado = IF(ind_estado = '1', '0', '1'),
                    usr_modifi = '".$_REQUEST['usr_creaci']."', 
                    fec_modifi = NOW()
                    WHERE cod_mintra = '".$_REQUEST['cod_colore']."' 
                ";
        }else{
            $mensaje = "Insertado";

            // $query_consec = "SELECT MAX(CAST(a.cod_colorx AS INT)) FROM " . BASE_DATOS . ".tab_vehige_colore a";
        
            // $consulta_consec = new Consulta($query_consec, $this->conexion);
            // $matriz_consec = $consulta_consec->ret_matriz();

            // $consec = $matriz_consec[0][0] + 1;

            $anti_comilla = strpos($val_colorx[0]['nom_colorx'],"'");
            if($anti_comilla !== false){ // Si encuentra comilla simple, se reemplaza por ` para evitar novedades en SQL
                $val_colorx[0]['nom_colorx'] = str_replace("'","`",$val_colorx[0]['nom_colorx']);
            }
            
            $query_1 = "INSERT INTO " . BASE_DATOS . ".tab_vehige_colore
                            (
                                cod_colorx, nom_colorx, cod_mintra, 
                                ind_estado, usr_creaci, fec_creaci, 
                                usr_modifi, fec_modifi
                            ) VALUES 
                            (
                                '".$_REQUEST["cod_colore"]."', '".$val_colorx[0]['nom_colorx']."', '".$_REQUEST["cod_colore"]."',
                                '1', '".$_REQUEST['usr_creaci']."', NOW(),
                                NULL, NULL)
                    ";
        }
    
        $consul_1 = new Consulta($query_1, $this->conexion, "R");

        if (!mysql_errno())
        {
            $end = new Consulta("COMMIT", $this->conexion);
            $vec_mensaj[1] .= "<br />El color ".$val_colorx[0]["nom_colorx"]." con código de ministerio ".$_REQUEST["cod_colore"] ." ha sido ".$mensaje." con Éxito.";
        }
        else
        {
            $end = new Consulta("ROLLBACK", $this->conexion);
            $vec_mensaj[0] = "3";
            $vec_mensaj[1] = "<br />Hubo un fallo de sistema. Por favor, vuelva a intentar.";
        }

        unset($_REQUEST["cod_colore"]);
        unset($_REQUEST["nom_colorx"]);
        return $vec_mensaj;
    }
}

$servicio = new Mod_Vehicu_Colore($this->conexion, $this->usuario_aplicacion, $this->codigo);

?>