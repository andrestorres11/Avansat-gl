<?php
/*! \class Lis_Acceso
 *  \brief INFORME DE CONTROL TIEMPO PRODUCTIVIDAD
 *  \author UNKNOWN
 *  \date    UNKNOWN
 *  \version 1.0
 *  \author David Rincón
 *  \author david.rincon@intrared.net
 *  \brief Nuevo Framework e inclusión de Datatables
 *  \version 2.0
 *  \date    20-06-2017
*/
//ini_set('display_errors', true);
//error_reporting(E_ALL && ~E_NOTICE);
class Lis_Acceso
{
    var $conexion,
        $usuario;//una conexion ya establecida a la base de datos

    /*! \fn: __construct
     *  \brief: Función Constructora
     *  \author: UNKNOWN, modified by Ing. David Rincón
     *    \date: UNKNOWN
     *    \date modified: 20/06/2017
     *  \param: $co (Objeto de conexión a motor de bases de datos y sus parámetros)
     *  \param: $us (Vector de información sobre usuario logueado)
     *  \param: $ca (Objeto que indica si el código aplica)
     *  \return NADA
     */
    function __construct($co, $us, $ca)
    {
        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
        <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/acceso.js"></script>
        <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/chosen/chosen.jquery.js"></script>
        <link rel="stylesheet" type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/js/chosen/chosen.css" />
        <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/sweetalert-dev.js"></script>
        <link rel="stylesheet" type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/sweetalert.css" />
        <link rel="stylesheet" type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/jquery.css" />
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/informes.css" rel="stylesheet" />
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/dinamic_list.css" rel="stylesheet" />
        <link type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/estilos/validator.css" rel="stylesheet" />
        <?php
        $this -> conexion = $co;
        $this -> usuario = $us;
        $this -> cod_aplica = $ca;
        $this -> cod_filtro = $cf;
        $this -> principal();
    }

    /*! \fn: principal
     *  \brief: Navegabilidad por la clase
     *  \author: UNKNOWN, modified by Ing. David Rincón
     *    \date: UNKNOWN
     *    \date modified: 20/06/2017
     *  \param: NINGUNO
     *  \return NADA
     */
    function principal()
    {
        if(!isset($_REQUEST["opcion"]))
        {
            $_REQUEST["opcion"] = "0";
        }

        switch($_REQUEST["opcion"])
        {
            case "1":
                $this -> Resultado();
            break;
            default:
                $this -> Resultado();
            break;
        }
    }

    /*! \fn: Resultado
     *  \brief: Muestra la tabla de resultados con su correspondiente botón en Excel para Importación
     *  \author: UNKNOWN, modified by Ing. David Rincón
     *    \date: UNKNOWN
     *    \date modified: 20/06/2017
     *  \param: NINGUNO
     *  \return NADA
     */
    function Resultado()
    {
        $datos_usuario = $this -> usuario -> retornar();
        $usuario=$datos_usuario["cod_usuari"];
        $fec_actual = date("Y-m-d");
        ?>
        <script>
            $(document).ready(function(){
                $( "#fec_iniciaID" ).datepicker({dateFormat: 'yy-mm-dd'}).prop("autocomplete", "off");
                $( "#fec_finalxID" ).datepicker({dateFormat: 'yy-mm-dd'}).prop("autocomplete", "off");
                $('#cod_usuariID').chosen();
                $('#cod_usuariID_chosen').width(400);
            });
        </script>
        <form action="index.php?cod_servic=<?= $_REQUEST['cod_servic'] ?>" method="post" name="form_lista" id="form_listaID">
        <table class="col-md-12 CellHead" width="100%"><tr><td style="color:#FFFFFF;">Ingresos de Usuarios: Ingrese El Rango De Fechas Deseado y/o Usuario</td></tr></table>
        <br />

        <table class="col-md-12 ancho text-center">
        <tr>
        <td class="col-md-2 text-right">Fecha Inicial:</td>
        <td class="col-md-2"><input type="text" class="text-center" name="fec_inicia" id="fec_iniciaID" maxlength="10" size="10" value="<?= $_REQUEST['fec_inicia'] ?>" /></td>
        <td class="col-md-2 text-right">Fecha Final:</td>
        <td class="col-md-2"><input type="text" class="text-center" name="fec_finalx" id="fec_finalxID" maxlength="10" size="10" value="<?= $_REQUEST['fec_finalx'] ?>" /></td>
        <td class="col-md-2 text-right">Usuario:</td>
        <td class="col-md-2"><select name="cod_usuari" id="cod_usuariID">
        <?php
        $mat_usuari = $this->obtenerListadoUsuarios();
        foreach ($mat_usuari as $vec_usuari) {
            ?>
            <option value="<?= $vec_usuari[0] ?>" <?php echo ($vec_usuari[0] == $_REQUEST["cod_usuari"]."" ? 'selected' : ''); ?> ><?= $vec_usuari[1] ?></option>
            <?php
        }
        ?>
        </select></td>
        </tr>

        <tr>
        <td class="col-md-12 ancho text-center" colspan="6">
        <input type="hidden" name="opcion" id="opcionID" value="1" />
        <input type="hidden" name="window" id="windowID" value="central" />
        <input type="hidden" name="usuario" id="usuarioID" value="<?= $datos_usuario["cod_usuari"] ?>" />
        <input type="hidden" name="cod_servic" id="cod_servicID" value="<?= $_REQUEST["cod_servic"] ?>" />
        <input type="button" name="accept" id="acceptID" value="Aceptar" onclick="validarFiltrosInformeAcceso()" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" />
        </td>
        </tr>
        </table>
        <?php
        /* SI ESTAMOS ACCEDIENDO POR SEGUNDA O ENÉSIMA VEZ, SE DEBE MOSTRAR EL RESULTADO DE LA CONSULTA */
        if($_REQUEST["opcion"] == "1") {
        ?>
            <link rel="stylesheet" type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/js/bootstrap3/css/bootstrap.min.css" />
            <link rel="stylesheet" type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/css/jquery.dataTables.min.css" />
            <link rel="stylesheet" type="text/css" href="../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/css/buttons.dataTables.min.css" />
            <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/bootstrap3/js/bootstrap.min.js"></script>
            <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js_export/jquery.dataTables.min.js"></script>
            <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js_export/dataTables.buttons.min.js"></script>
            <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js_export/jszip.min.js"></script>
            <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js_export/pdfmake.min.js"></script>
            <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js_export/vfs_fonts.js"></script>
            <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js_export/buttons.flash.min.js"></script>
            <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js_export/buttons.html5.min.js"></script>
            <script type="text/javascript" src="../<?= DIR_APLICA_CENTRAL ?>/js/DataTables/js_export/buttons.print.min.js"></script>
            <script language="javascript">
                $(document).ready(function()
                {
                    $("#tbl_despac01ID thead th").each( function () {
                        var title = $(this).text();
                        $(this).html( title + "<br /><input type='text' placeholder='"+title+"' />" );
                    } );

                    // DataTable
                    var table01 = $("#tbl_despac01ID").DataTable({
                        lengthMenu: [[100, 200, 500, 1000, -1], [100, 200, 500, 1000, "All"]],
                        dom: "lpiBfrt",
                        buttons: [
                            {
                                extend: "excelHtml5",
                                footer: true,
                                title: "Lis_Acceso_" + Date()
                            }
                        ]
                    });

                    // Apply the search
                    table01.columns().every( function () {
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
            </script>
        <?php

            $datos_usuario = $this -> usuario -> retornar();
            $usuario=$datos_usuario["cod_usuari"];

            $fecha1 = $_REQUEST["fec_inicia"]." 00:00:00";
            $fecha2 = $_REQUEST["fec_finalx"]." 23:59:59";
            //arreglos iniciales o finales para la presentacion

            //lista total
            $query = "SELECT a.cod_usuari, 
                            IFNULL(b.nom_perfil, 'Sin Perfil') as nom_perfil, 
                            a.fec_ingres, 
                            a.url_acceso 
                        FROM ".BASE_DATOS.".tab_bitaco_acceso a 
                            LEFT JOIN ".BASE_DATOS.".tab_genera_perfil b 
                                ON a.cod_perfil = b.cod_perfil 
                        WHERE (1=1) 
                    ";
            if($_REQUEST["fec_inicia"] != "" && $_REQUEST["fec_finalx"] != ""){
                $query .= " 
                            AND a.fec_ingres BETWEEN '".$fecha1."' AND '".$fecha2."' ";
            }
            if($_REQUEST["cod_usuari"] != ""){
                $query .= " 
                            AND a.cod_usuari = '".$_REQUEST["cod_usuari"]."' ";
            }
            $query .= "
                        ORDER BY 1 ";

            $consulta = new Consulta($query, $this -> conexion);
            $matriz = $consulta -> ret_matriz();

            //formulario para imprimir el resultado
            ?>
            <br />
            <table class='table table-hover' id='tbl_despac01ID' align='left' width='100%'>
            <thead>
            <tr class='DLRowHeader'>
            <th>Nombre Usuario</th>
            <th>Perfil</th>
            <th>Fecha y Hora de Ingreso</th>
            <th>IP de Ingreso</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for($i=0; $i < sizeof($matriz); $i++)
            {
                if($i%2 == 0) {
                    $celda = "DLRow2";
                } else {
                    $celda = "DLRow1";
                }
                ?>
                <tr>
                <td class="<?= $celda ?>"><?= $matriz[$i]["cod_usuari"] ?></td>
                <td class="<?= $celda ?>"><?= $matriz[$i]["nom_perfil"] ?></td>
                <td class="<?= $celda ?>"><?= $matriz[$i]["fec_ingres"] ?></td>
                <td class="<?= $celda ?>"><?= $matriz[$i]["url_acceso"] ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
            </table>
            <?php
        }
        ?>
        </form>
        <?php
    }

    /*! \fn: obtenerListadoUsuarios
     *  \brief: Muestra los usuarios que tienen acceso al AVANSAT
     *  \author: Ing. David Rincón
     *    \date: 14/02/2020
     *  \param: NINGUNO
     *  \return Una natriz con códigos y nombres de los usuarios
     */
    function obtenerListadoUsuarios(){
        $inicio = array(array("", "--"));
        $query = "
            SELECT a.cod_usuari, CONCAT(a.nom_usuari, ' ( ', a.cod_usuari, ' )') as nom_usuari 
            FROM ".BASE_DATOS.".tab_genera_usuari a 
            ORDER BY a.nom_usuari
        ";
        $consulta = new Consulta ($query,$this->conexion);
        $usuarios = $consulta->ret_matriz("i");
        $matriz = array_merge($inicio, $usuarios);

        return $matriz;
    }
}

$proceso = new Lis_Acceso($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);
?>