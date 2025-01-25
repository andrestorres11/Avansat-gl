<?php
// ini_set("display_errors", true);
// error_reporting(E_ALL ^ E_NOTICE);
/* !\class: Mod_Vehicu_Marcas
 *  \brief: Módulo de listado y administrador de Marcas
 *  \author: Ing. Jesus Sanchez
 *  \date: 29/04/2024
 */

class Mod_Vehicu_Marcas
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
                $this->Listar_Marcas(array(0, ""));
            break;
            case "2":
                $vec_respon = $this->Insertar_Marcas();
                $this->Listar_Marcas($vec_respon);
            break;
            case "5":
                $vec_respon = $this->ActivarMarcas();
                $this->Listar_Marcas($vec_respon);
            break;
            default:
                $this->Listar_Marcas(array(0, ""));
            break;
        }
    }

    /* ! \fn: Listar_Marcas
     *  \brief: Muestra el listado de marcas, a la vez de manejar un formulario de inserción/actualización
     *  \author: Ing. Jesus Sanchez
     *    \date: 29/04/2024
     *  \param1:$vec_respon (Vector con la respuesta de cualquier operación de modificación de registros)
     *  \return NADA, pero muestra el formulario central
     */
    function Listar_Marcas($vec_respon)
    {
        echo '<script language="javascript">
                $(document).ready(function(){
                    $("#tbl_marcasID thead th").each( function () {
                        var title = $(this).text();
                        if(title != "#") {
                            $(this).html( title + "<br /><input type=\'text\' placeholder=\'"+title+"\' />" );
                        }
                    } );
                    var table = $("#tbl_marcasID").DataTable({
                        paging: true,
                        bPaginate: true,
                        dom: "Bfrtip",
                        buttons: [
                            {
                                extend: "excelHtml5",
                                title: "Lista_Marcas_" + Date()
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
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/ins_vehicu_marcas.js\"></script>\n";

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
        
                
        $form = new Form("action:index.php?cod_servic=" . $_REQUEST["cod_servic"] . "; method:post; name:form_marcas;");
                
        if($vec_respon[0] == "1")
        {
            $form->Row("td");
            ShowMessage("s", "Marcas", $vec_respon[1]);
            $form->CloseRow("td");
        }
        else if($vec_respon[0] == "2")
        {
            $form->Row("td");
            ShowMessage("a", "Marcas", $vec_respon[1]);
            $form->CloseRow("td");
        }
        else if($vec_respon[0] == "3")
        {
            $form->Row("td");
            ShowMessage("e", "Marcas", $vec_respon[1]);
            $form->CloseRow("td");
        }

        $form->Hidden("name:cod_marcas; value:0; ");
        $form->Hidden("name:option; value:2; ");
        $form->Hidden("name:window; value:central; ");
        $form->Hidden("name:cod_servic; value:".$_REQUEST['cod_servic']."; ");
        $form->Hidden("name:usr_creaci; value:" . $usuario . ";");
        $form->Hidden("name:standar; value:" . DIR_APLICA_CENTRAL);

        $form->Table();
        $form->Line("MARCAS", "t2", 0, 0, "left", "", "", "");
        $form->CloseTable();

        $query = "SELECT a.cod_marcax, a.nom_marcax, a.cod_mintra, b.ind_estado, b.cod_mintra AS mintra_cliente FROM ".BD_STANDA.".tab_genera_marcas a 
                        LEFT JOIN ".BASE_DATOS.".tab_genera_marcas b ON a.cod_mintra = b.cod_mintra";

        $consulta = new Consulta($query, $this->conexion);
        $matriz = $consulta->ret_matriz();

        $form->Row("td");
        echo "<br />";
        // echo "<div class='container' style='margin-left:inherit;'>";
        echo "<div class='container'>";
        echo "<table class='table table-hover' id='tbl_marcasID'>";
        echo "<thead>";
        echo "<tr class='celda_titulo'>";
        echo "<th>CONSECUTIVO</th>";
        echo "<th>CODIGO MARCA</th>";
        echo "<th>MARCA</th>";
        echo "<th>ACTIVAR / DESACTIVAR MARCA</th>";
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
                echo "<td class='".$celdas."'>".utf8_decode($matriz[$i]["cod_marcax"])."</td>";
                echo "<td class='".$celdas."'>".utf8_decode($matriz[$i]["cod_mintra"])."</td>";
                echo "<td class='".$celdas."'>".utf8_decode(utf8_encode($matriz[$i]["nom_marcax"]))."</td>";

                if(strpos(utf8_decode(utf8_encode($matriz[$i]["nom_marcax"])),'"') !== false){ // Si encuentra una comilla doble, la cambia por `
                    $marca_cambiado = str_replace('"','`',utf8_decode(utf8_encode($matriz[$i]["nom_marcax"])));
                }else{
                    $marca_cambiado = utf8_decode(utf8_encode($matriz[$i]["nom_marcax"]));
                }

                echo "<td class='".$celdas."'><a href='#' onclick='javascript:activarMarcas(\"".$matriz[$i]["cod_mintra"]."\", \"".$matriz[$i]["mintra_cliente"]."\", \"".$marca_cambiado."\", \"".$matriz[$i]["ind_estado"]."\")'>".$mensaje."</a></td>";
                echo "</tr>";
            }
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        echo "<br>";

        $form->CloseRow("td");

        $form->CloseForm();

        echo "<script language='javascript'>
                    AjaxLoader('none');
                    LockAplication('unlock');
                </script>";
    }

    /* ! \fn: ActivarMarcas
     *  \brief: Permite la activación manual de las marcas, si no esta presente en el cliente la inserta
     *  \author: Ing. Jesus Sanchez
     *    \date: 25/04/2019
     *  \param1:NINGUNO
     *  \return Un vector con el indicador de aviso (1:Success/2:Warning/3:Error), y la descripción del mensaje
     */
    function ActivarMarcas() {
        $vec_mensaj = array();

        $vec_mensaj[0] = "1";
        $vec_mensaj[1] = "";

        $sql_valida = "SELECT a.cod_marcax, a.nom_marcax, a.cod_mintra, b.ind_estado, b.cod_mintra AS mintra_cliente FROM ".BD_STANDA.".tab_genera_marcas a 
                        LEFT JOIN ".BASE_DATOS.".tab_genera_marcas b ON a.cod_mintra = b.cod_mintra WHERE a.cod_mintra = '".$_REQUEST['cod_marcas']."'";
             
        $con_valida = new Consulta($sql_valida, $this->conexion);
        $val_marcax = $con_valida->ret_matriz();
        
        $init = new Consulta("START TRANSACTION", $this->conexion);
        if($val_marcax[0]["mintra_cliente"] != NULL && $val_marcax[0]["ind_estado"] == "1"){
            $mensaje = "Desactivada";
            /* VAMOS A ACTUALIZAR OPERADOR */
            $query_1 = "UPDATE " . BASE_DATOS . ".tab_genera_marcas
                        SET ind_estado = IF(ind_estado = '1', '0', '1'), 
                        usr_modifi = '".$_REQUEST['usr_creaci']."', 
                        fec_modifi = NOW()
                        WHERE cod_mintra = '".$_REQUEST['cod_marcas']."' 
                    ";
        }else if($val_marcax[0]["mintra_cliente"] != NULL && $val_marcax[0]["ind_estado"] == "0"){
            $mensaje = "Activada";
            /* VAMOS A ACTUALIZAR OPERADOR */
            $query_1 = "UPDATE " . BASE_DATOS . ".tab_genera_marcas
                        SET ind_estado = IF(ind_estado = '1', '0', '1'),
                        usr_modifi = '".$_REQUEST['usr_creaci']."', 
                        fec_modifi = NOW()
                        WHERE cod_mintra = '".$_REQUEST['cod_marcas']."' 
                    ";
        }else{
            $mensaje = "Insertada";

            // Consecutivo de marcas 
            // $query_consec = "SELECT MAX(CAST(a.cod_marcax AS INT)) FROM " . BASE_DATOS . ".tab_genera_marcas a";
        
            // $consulta_consec = new Consulta($query_consec, $this->conexion);
            // $matriz_consec = $consulta_consec->ret_matriz();

            // $consec = $matriz_consec[0][0] + 1;

            $anti_comilla = strpos($val_marcax[0]['nom_marcax'],"'");
            if($anti_comilla !== false){ // Si encuentra comilla simple, se reemplaza por ` para evitar novedades en SQL
                $val_marcax[0]['nom_marcax'] = str_replace("'","`",$val_marcax[0]['nom_marcax']);
            }

            $query_1 = "INSERT INTO " . BASE_DATOS . ".tab_genera_marcas
                            (
                                cod_marcax, nom_marcax, cod_mintra, 
                                ind_estado, usr_creaci, fec_creaci, 
                                usr_modifi, fec_modifi
                            ) VALUES 
                            (   
                                '".$_REQUEST["cod_marcas"]."', '".$val_marcax[0]['nom_marcax']."', '".$_REQUEST["cod_marcas"]."',
                                '1', '".$_REQUEST['usr_creaci']."', NOW(),
                                NULL, NULL
                            )
                    ";
        }

        $consul_1 = new Consulta($query_1, $this->conexion, "R");

        
        if (!mysql_errno())
        {
            $end = new Consulta("COMMIT", $this->conexion);
            $vec_mensaj[1] .= "<br />La marca ".$val_marcax[0]["nom_marcax"]." con código de ministerio ".$_REQUEST["cod_marcas"] ." ha sido ".$mensaje." con Éxito.";
        }
        else
        {
            $end = new Consulta("ROLLBACK", $this->conexion);
            $vec_mensaj[0] = "3";
            $vec_mensaj[1] = "<br />Hubo un fallo de sistema. Por favor, vuelva a intentar.";
        }
        
        unset($_REQUEST["cod_marcas"]);
        unset($_REQUEST["nom_marcax"]);
        return $vec_mensaj;
    }
}

$servicio = new Mod_Vehicu_Marcas($this->conexion, $this->usuario_aplicacion, $this->codigo);

?>