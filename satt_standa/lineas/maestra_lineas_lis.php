<?php

/****************************************************************************

NOMBRE:   MODULO_LINEAS_LIS.PHP
FUNCION:  LISTAR LINEAS DE VEHICULOS

****************************************************************************/

ini_set("display_errors", true);
error_reporting(E_ALL ^ E_NOTICE);
/* !\class: Maestra_lineas_lis
 *  \brief: Módulo de listado y administrador de Lineas
 *  \author: Ing. Jesus Sanchez
 *  \date: 29/04/2024
 */

class Maestra_lineas_lis
{
    var $conexion = NULL,
        $usuario = NULL,
        $cod_aplica = NULL;
    var $cNull = array(array("", "--"));

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
                $this->Listar_Lineas(array(0, ""));
            break;
            case "5":
                $vec_respon = $this->ActivarLineas();
                $this->Listar_Lineas($vec_respon);
            break;
            default:
                $this->Listar_Lineas(array(0, ""));
            break;
        }
    }

    /* ! \fn: Listar_Lineas
     *  \brief: Muestra el listado de Lineas, a la vez de manejar un formulario de inserción/actualización
     *  \author: Ing. Jesus Sanchez
     *    \date: 29/04/2024
     *  \param1:$vec_respon (Vector con la respuesta de cualquier operación de modificación de registros)
     *  \return NADA, pero muestra el formulario central
     */
    function Listar_Lineas($vec_respon)
    {
        echo '<script language="javascript">
                $(document).ready(function(){
                    $("#tbl_lineasID thead th").each( function () {
                        var title = $(this).text();
                        if(title != "#") {
                            $(this).html( title + "<br /><input type=\'text\' placeholder=\'"+title+"\' />" );
                        }
                    } );
                    var table = $("#tbl_lineasID").DataTable({
                        paging: true,
                        bPaginate: true,
                        dom: "Bfrtip",
                        buttons: [
                            {
                                extend: "excelHtml5",
                                title: "Lista_Lineas_" + Date()
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

        ini_set("memory_limit", "1024M");
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/sweetalert.min.js\"></script>\n";
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/sweetalert.css\">\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/ins_vehicu_lineas.js\"></script>\n";

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
        
                
        $form = new Form("action:index.php?cod_servic=" . $_REQUEST["cod_servic"] . "; method:post; name:form_lineas;");
                
        if($vec_respon[0] == "1")
        {
            $form->Row("td");
            ShowMessage("s", "Líneas", $vec_respon[1]);
            $form->CloseRow("td");
        }
        else if($vec_respon[0] == "2")
        {
            $form->Row("td");
            ShowMessage("a", "Líneas", $vec_respon[1]);
            $form->CloseRow("td");
        }
        else if($vec_respon[0] == "3")
        {
            $form->Row("td");
            ShowMessage("e", "Líneas", $vec_respon[1]);
            $form->CloseRow("td");
        }

        $form->Hidden("name:cod_lineas; value:0; ");
        $form->Hidden("name:cod_marcas; value:0; ");
        $form->Hidden("name:option; value:2; ");
        $form->Hidden("name:window; value:central; ");
        $form->Hidden("name:cod_servic; value:".$_REQUEST['cod_servic']."; ");
        $form->Hidden("name:usr_creaci; value:" . $usuario . ";");
        $form->Hidden("name:standar; value:" . DIR_APLICA_CENTRAL);

        $form->Table();
        $form->Line("LINEAS", "t2", 0, 0, "left", "", "", "");
        $form->CloseTable();

        // $query = "SELECT a.cod_lineax, a.cod_marcax, a.cod_marmin, a.nom_lineax, a.cod_mintra, a.ind_estado FROM ".BASE_DATOS.".tab_vehige_lineas a";
        $query = "SELECT a.cod_mintra, a.nom_lineax, a.cod_marcax, b.nom_marcax, c.cod_mintra AS mintra_cliente, c.ind_estado
                            FROM ".BD_STANDA.".tab_genera_lineas a
                            INNER JOIN ".BD_STANDA.".tab_genera_marcas b ON a.cod_marcax = b.cod_marcax
                            LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas c ON a.cod_mintra = c.cod_mintra AND a.cod_marcax = c.cod_marcax";

        $consulta = new Consulta($query, $this->conexion);
        $matriz = $consulta->ret_matriz();

        $form->Row("td");
        echo "<br />";
        // echo "<div class='container' style='margin-left:inherit;'>";
        echo "<div class='container'>";
        echo "<table class='table table-hover' id='tbl_lineasID'>";
        echo "<thead>";
        echo "<tr class='celda_titulo'>";
        // echo "<th>CONSECUTIVO</th>";
        echo "<th>CODIGO LINEA</th>";
        echo "<th>LINEA</th>";
        echo "<th>CODIGO MARCA</th>";
        echo "<th>MARCA</th>";
        echo "<th>ACTIVAR / DESACTIVAR LINEA</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        $i = 0;
        if(sizeof($matriz) > 0)
        {
            $num_longit = sizeof($matriz);
            for($i = 0; $i < $num_longit; $i++)
            {
                $celdas = "celda";
                if($i%2 != 0)
                {
                    $celdas = "celda2";
                }

                if($matriz[$i]["mintra_cliente"] != NULL && $matriz[$i]["ind_estado"] == "1"){
                    $mensaje = "Desactivar";
                }else if($matriz[$i]["mintra_cliente"] != NULL && $matriz[$i]["ind_estado"] == "0"){
                    $mensaje = "Activar";
                }else{
                    $mensaje = "Insertar";
                }

                echo "<tr>";
                // echo "<td class='".$celdas."'>".utf8_decode($matriz[$i]["cod_lineax"])."</td>";
                echo "<td class='".$celdas."'>".utf8_decode($matriz[$i]["cod_mintra"])."</td>";
                echo "<td class='".$celdas."'>".utf8_decode($matriz[$i]["nom_lineax"])."</td>";
                echo "<td class='".$celdas."'>".utf8_decode($matriz[$i]["cod_marcax"])."</td>";
                echo "<td class='".$celdas."'>".utf8_decode(utf8_encode($matriz[$i]["nom_marcax"]))."</td>";

                if(strpos(utf8_decode(utf8_encode($matriz[$i]["nom_lineax"])),'"') !== false){ // Si encuentra una comilla doble, la cambia por `
                    $linea_cambiado = str_replace('"','`',utf8_decode(utf8_encode($matriz[$i]["nom_lineax"])));
                }else{
                    $linea_cambiado = utf8_decode(utf8_encode($matriz[$i]["nom_lineax"]));
                }

                echo "<td class='".$celdas."'><a href='#' onclick='javascript:ActivarLineas(\"".$matriz[$i]["cod_mintra"]."\", \"".$matriz[$i]["mintra_cliente"]."\", \"".$matriz[$i]["ind_estado"]."\", \"".$linea_cambiado."\", \"".$matriz[$i]["cod_marcax"]."\")'>".$mensaje."</a></td>";
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

    /* ! \fn: ActivarLineas
     *  \brief: Permite la activación manual de las lineas
     *  \author: Ing. Jesus Sanchez
     *    \date: 25/04/2019
     *  \param1:NINGUNO
     *  \return Un vector con el indicador de aviso (1:Success/2:Warning/3:Error), y la descripción del mensaje
     */
    function ActivarLineas() {
        $vec_mensaj = array();

        $vec_mensaj[0] = "1";
        $vec_mensaj[1] = "";

        $sql_valida = "SELECT a.cod_lineax, a.cod_mintra, a.nom_lineax, a.cod_marcax, c.ind_estado, c.cod_mintra AS mintra_cliente
        FROM ".BD_STANDA.".tab_genera_lineas a
              LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas c ON a.cod_mintra = c.cod_mintra AND a.cod_marcax = c.cod_marcax 
        WHERE a.cod_mintra = '".$_REQUEST["cod_lineas"]."' AND a.cod_marmin = '".$_REQUEST["cod_marcas"]."' ";
        $con_valida = new Consulta($sql_valida, $this->conexion);
        $val_linea = $con_valida->ret_matriz();

        $init = new Consulta("START TRANSACTION", $this->conexion);

        $sql_marca = "SELECT a.* FROM ".BASE_DATOS.".tab_genera_marcas a
                                WHERE a.cod_mintra = '".$_REQUEST["cod_marcas"]."'";
        $consul_marca = new Consulta($sql_marca, $this->conexion);
        $existe_marca = $consul_marca->ret_matriz();

        if($val_linea[0]["mintra_cliente"] != NULL && $val_linea[0]["ind_estado"] == "1"){
            $mensaje = "Desactivada";
            /* VAMOS A ACTUALIZAR OPERADOR */
            
            $query_1 = "UPDATE " . BASE_DATOS . ".tab_vehige_lineas
                        SET ind_estado = IF(ind_estado = '1', '0', '1'), 
                        usr_modifi = '".$_REQUEST['usr_creaci']."', 
                        fec_modifi = NOW()
                        WHERE cod_mintra = '".$_REQUEST['cod_lineas']."' 
                        AND cod_marmin = '".$_REQUEST["cod_marcas"]."'";
        }else if($val_linea[0]["mintra_cliente"] != NULL && $val_linea[0]["ind_estado"] == "0"){
            $mensaje = "Activada";
            /* VAMOS A ACTUALIZAR OPERADOR */
            $query_1 = "UPDATE " . BASE_DATOS . ".tab_vehige_lineas
                        SET ind_estado = IF(ind_estado = '1', '0', '1'),
                        usr_modifi = '".$_REQUEST['usr_creaci']."', 
                        fec_modifi = NOW()
                        WHERE cod_mintra = '".$_REQUEST['cod_lineas']."' 
                        AND cod_marmin = '".$_REQUEST["cod_marcas"]."'";
        }else{
            $mensaje = "Insertada";

            // $query_consec = "SELECT MAX(CAST(a.cod_lineax AS INT)) FROM " . BASE_DATOS . ".tab_vehige_lineas a";
        
            // $consulta_consec = new Consulta($query_consec, $this->conexion);
            // $matriz_consec = $consulta_consec->ret_matriz();

            // $consec = $matriz_consec[0][0] + 1;

            $anti_comilla = strpos($val_linea['nom_lineax'],"'");
            if($anti_comilla !== false){ // Si encuentra comilla simple, se reemplaza por ` para evitar novedades en SQL
                $val_linea['nom_lineax'] = str_replace("'","`",$val_linea['nom_lineax']);
            }

            $sql_linea_err_1 = "SELECT a.cod_lineax, a.cod_mintra, a.nom_lineax, a.cod_marcax FROM ".BASE_DATOS.".tab_vehige_lineas a
            WHERE a.cod_lineax = '".$_REQUEST["cod_lineas"]."' AND a.cod_marcax = '".$_REQUEST["cod_marcas"]."' AND a.cod_lineax != a.cod_mintra ";
            $con_linea_err_1 = new Consulta($sql_linea_err_1, $this->conexion);
            $err_linea_1 = $con_linea_err_1->ret_matriz();

            $sql_linea_err_2 = "SELECT a.cod_lineax, a.cod_mintra, a.nom_lineax, a.cod_marcax FROM ".BASE_DATOS.".tab_vehige_lineas a
            WHERE a.cod_lineax = '".$_REQUEST["cod_lineas"]."' AND a.cod_marcax = '".$_REQUEST["cod_marcas"]."' AND a.cod_marcax != a.cod_marmin ";
            $con_linea_err_2 = new Consulta($sql_linea_err_2, $this->conexion);
            $err_linea_2 = $con_linea_err_2->ret_matriz();

            if(sizeof($existe_marca) > 0 && sizeof($err_linea_1) == 0 && sizeof($err_linea_2) == 0){
                $query_1 = "INSERT INTO " . BASE_DATOS . ".tab_vehige_lineas
                        (
                            cod_lineax, cod_marcax, cod_marmin,
                            nom_lineax, cod_mintra, ind_estado,
                            usr_creaci, fec_creaci, usr_modifi, 
                            fec_modifi
                        ) VALUES 
                        (   
                            '".$_REQUEST['cod_lineas']."', '".$val_linea[0]['cod_marcax']."', '".$val_linea[0]["cod_marcax"]."',
                            '".$val_linea[0]["nom_lineax"]."','".$_REQUEST['cod_lineas']."','1',
                            '".$_REQUEST['usr_creaci']."', NOW(), NULL, 
                            NULL
                        )
                ";
            }
        }
        $consul_1 = new Consulta($query_1, $this->conexion, "R");

        if(sizeof($existe_marca) > 0){
            if (!mysql_errno())
            { 
                $end = new Consulta("COMMIT", $this->conexion);
                $vec_mensaj[1] .= "<br />La línea ".$val_linea[0]["nom_lineax"]." con código de ministerio ".$_REQUEST["cod_lineas"] ." de la marca ".$existe_marca[0][1]." ha sido ".$mensaje." con Éxito.";
            }
            else
            {
                $end = new Consulta("ROLLBACK", $this->conexion);
                $vec_mensaj[0] = "3";
                $vec_mensaj[1] = "<br />Hubo un fallo de sistema. Por favor, vuelva a intentar.";
            }
        }else if(sizeof($err_linea_1) > 0){
            $vec_mensaj[0] = "3";
            $vec_mensaj[1] = "<br />La línea ".$val_linea[0]["nom_lineax"]." posee un error de codigos de la linea, donde los codigos de Avansat no coinciden con los del RNDC, por favor contactar con soporte para solventar la novedad";
        }else if(sizeof($err_linea_2) > 0){
            $vec_mensaj[0] = "3";
            $vec_mensaj[1] = "<br />La línea ".$val_linea[0]["nom_lineax"]." posee un error de codigos de marcas de la linea, donde los codigos de Avansat no coinciden con los del RNDC, por favor contactar con soporte para solventar la novedad";
        }else{
            $vec_mensaj[0] = "3";
            $vec_mensaj[1] = "<br />La línea ".$val_linea[0]["nom_lineax"]." que desea insertar no tiene la marca insertada en su plataforma, por favor primero insertar la marca y posteriormente la línea";
        }

        unset($_REQUEST["cod_lineas"]);
        unset($_REQUEST["cod_marcas"]);
        unset($_REQUEST["nom_lineax"]);
        return $vec_mensaj;
    }
}

$servicio = new Maestra_lineas_lis($this->conexion, $this->usuario_aplicacion, $this->codigo);

?>
