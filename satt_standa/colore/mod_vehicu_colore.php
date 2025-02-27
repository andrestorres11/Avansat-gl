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
        switch ($_REQUEST["opcion"]) {
            case "1":
                $this->Listar_Colores(array(0, ""));
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
        $datos_usuario = $this->usuario->retornar();
        $usuario = $datos_usuario["cod_usuari"];

        @include_once('../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc');
        IncludeJS( 'new_ajax.js' );
        IncludeJS( 'functions.js' );
        IncludeJS( 'proto.js' );
        IncludeJS( 'min.js' );
        IncludeJS( 'jquery.js' );
        IncludeJS( 'es.js' );
        IncludeJS( 'time.js' );
        IncludeJS( 'mask.js' );
        IncludeJS( 'jquery.blockUI.js' );
        IncludeJS( 'validator.js' );
        IncludeJS( 'par_califi_califi.js' );
        IncludeJS( '/dashboard/vendors/sweetAlert/sweetalert2.all.min.js' );
        IncludeJS( 'ins_vehicu_colore.js' );
        IncludeJS( 'par_confir_pernoc.js' );
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/validator.css' type='text/css'>";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/js/dashboard/vendors/sweetAlert/sweetalert2.min.css' type='text/css'>";
        echo "<style>
        td > label {
            word-break: break-all;
        }</style>";
        
        $mHtml = new FormLib(2);

        

        include_once("../".DIR_APLICA_CENTRAL."/lib/pagination/init.inc");
        $headers = array("CONSECUTIVO", "CODIGO COLOR", "COLOR",  "OPCION");

        $datos_usuario = $this->usuario->retornar();
        $usuario = $datos_usuario["cod_usuari"];

        $params = array(
            'option' => 'getColores',
        );
        
        $pagination = new Pagination($this->conexion);

        $url = "../".DIR_APLICA_CENTRAL."/colores/ajax_vehicu_colore.php";
        $result = $pagination->view(1,$headers,$url,$params,'COLORES',NULL,0,$options,4,1,25);

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

         # Abre Form
         $mHtml->Form(array("action" => "index.php",
         "method" => "post",
         "name" => "form_colores",
         "header" => "Colores",
         "enctype" => "multipart/form-data"));

        $mHtml->Hidden(array( "name" => "cod_colore", "id" => "cod_coloreID", 'value'=>0));
        // $mHtml->Hidden(array( "name" => "cod_marcas", "id" => "cod_marcasID", 'value'=>0));
        $mHtml->Hidden(array( "name" => "opcion", "id" => "opcionID", 'value'=>2));
        $mHtml->Hidden(array( "name" => "window", "id" => "windowID", 'value'=>'central'));
        $mHtml->Hidden(array( "name" => "cod_servic", "id" => "cod_servicID", 'value'=> $_REQUEST['cod_servic']));
        $mHtml->Hidden(array( "name" => "usr_creaci", "id" => "usr_creaciID", 'value'=> $usuario));
        $mHtml->Hidden(array( "name" => "standar", "id" => "standarID", 'value'=> DIR_APLICA_CENTRAL));

        $mHtml->Row("td");
            $mHtml->OpenDiv("id:contentID; class:contentAccordion");
                $mHtml->SetBody($result);  
            $mHtml->CloseDiv();
        $mHtml->CloseRow("td");

        # Cierra formulario
        $mHtml->CloseForm();
        # Muestra Html
        echo $mHtml->MakeHtml();  

        // $form->oculto("name:cod_colore; value:0; ");
        // $form->oculto("name:option; value:2; ");
        // $form->oculto("name:window; value:central; ");
        // $form->oculto("name:cod_servic; value:".$_REQUEST['cod_servic']."; ");
        // $form->oculto("name:usr_creaci; value:" . $usuario . ";");
        // $form->oculto("name:standar; value:" . DIR_APLICA_CENTRAL);

        // $form->nueva_tabla();
        // $form->linea("COLORES", "t2", 0, 0, "left", "", "", "");
        // $form->cerrar_tabla();

        // $query = "SELECT a.cod_colorx, a.nom_colorx, b.ind_estado, b.cod_mintra AS mintra_cliente FROM ".BD_STANDA.".tab_vehige_colore a LEFT JOIN ".BASE_DATOS.".tab_vehige_colore b ON a.cod_colorx = b.cod_mintra";

        // $consulta = new Consulta($query, $this->conexion);
        // $matriz = $consulta->ret_matriz();

        // echo "<td>";
        // echo "<br />";
        // // echo "<div class='container' style='margin-left:inherit;'>";
        // echo "<div class='container'>";
        // echo "<table class='table table-hover' id='tbl_coloreID'>";
        // echo "<thead>";
        // echo "<tr class='celda_titulo'>";
        // echo "<th>CONSECUTIVO</th>";
        // echo "<th>CODIGO COLOR</th>";
        // echo "<th>COLOR</th>";
        // echo "<th>ACTIVAR / DESACTIVAR COLOR</th>";
        // // echo "<th>ELIMINAR COLOR</th>";
        // echo "</tr>";
        // echo "</thead>";
        // echo "<tbody>";
        // $i = 0;

        // $mensaje = "";
        // if(sizeof($matriz) > 0)
        // {
        //     $num_longit = sizeof($matriz);
        //     for($i = 0; $i < $num_longit; $i++)
        //     {
        //         if($matriz[$i]["mintra_cliente"] != NULL && $matriz[$i]["ind_estado"] == "1"){
        //             $mensaje = "Desactivar";
        //         }else if($matriz[$i]["mintra_cliente"] != NULL && $matriz[$i]["ind_estado"] == "0"){
        //             $mensaje = "Activar";
        //         }else{
        //             $mensaje = "Insertar";
        //         }

        //         $celdas = "celda";
        //         if($i%2 != 0)
        //         {
        //             $celdas = "celda2";
        //         }
        //         echo "<tr>";
        //         echo "<td class='".$celdas."'>".utf8_decode($matriz[$i]["cod_colorx"])."</td>";
        //         echo "<td class='".$celdas."'>".utf8_decode($matriz[$i]["cod_colorx"])."</td>";
        //         echo "<td class='".$celdas."'>".utf8_decode(utf8_encode($matriz[$i]["nom_colorx"]))."</td>";

        //         if(strpos(utf8_decode(utf8_encode($matriz[$i]["nom_colorx"])),'"') !== false){ // Si encuentra una comilla doble, la cambia por `
        //             $color_cambiado = str_replace('"','`',utf8_decode(utf8_encode($matriz[$i]["nom_colorx"])));
        //         }else{
        //             $color_cambiado = utf8_decode(utf8_encode($matriz[$i]["nom_colorx"]));
        //         }

        //         echo "<td class='".$celdas."'><a href='#' onclick='javascript:activarColores(\"".$matriz[$i]["cod_colorx"]."\", \"".$matriz[$i]["mintra_cliente"]."\", \"".$color_cambiado."\", \"".$matriz[$i]["ind_estado"]."\")'>".$mensaje."</a></td>";
        //         echo "</tr>";
        //     }
        // }

        // echo "</tbody>";
        // echo "</table>";
        // echo "</div>";

        // echo "<br>";

        // echo "</td>";
        // $form->cerrar();
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

        $sql_valida = "SELECT a.cod_colorx, a.nom_colorx, b.ind_estado, b.cod_mintra AS mintra_cliente FROM ".BD_STANDA.".tab_vehige_colore a LEFT JOIN ".BASE_DATOS.".tab_vehige_colore b ON a.cod_colorx = b.cod_mintra WHERE a.cod_colorx = '".$_REQUEST['cod_colore']."'";

        $con_valida = new Consulta($sql_valida, $this->conexion);
        $val_colorx = $con_valida->ret_matriz();


        $init = new Consulta("START TRANSACTION", $this->conexion);

        if($val_colorx[0]["mintra_cliente"] != NULL && $val_colorx[0]["ind_estado"] == "1"){
            $mensaje = "Desactivado";
            /* VAMOS A ACTUALIZAR OPERADOR */
            $query_1 = "UPDATE " . BASE_DATOS . ".tab_vehige_colore SET ind_estado = IF(ind_estado = '1', '0', '1'), usr_modifi = '".$_REQUEST['usr_creaci']."',  fec_modifi = NOW() WHERE cod_mintra = '".$_REQUEST['cod_colore']."' ";
            $consul_1 = new Consulta($query_1, $this->conexion, "R");
        }else if($val_colorx[0]["mintra_cliente"] != NULL && $val_colorx[0]["ind_estado"] == "0"){
            $mensaje = "Activado";
            /* VAMOS A ACTUALIZAR OPERADOR */
            $query_1 = "UPDATE " . BASE_DATOS . ".tab_vehige_colore SET ind_estado = IF(ind_estado = '1', '0', '1'),  usr_modifi = '".$_REQUEST['usr_creaci']."',   fec_modifi = NOW() WHERE cod_mintra = '".$_REQUEST['cod_colore']."' ";
            $consul_1 = new Consulta($query_1, $this->conexion, "R");
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
            
            $query_1 = "INSERT INTO " . BASE_DATOS . ".tab_vehige_colore ( cod_colorx, nom_colorx, cod_mintra, ind_estado, usr_creaci, fec_creaci, usr_modifi, fec_modifi ) VALUES ( '".$_REQUEST["cod_colore"]."', '".$val_colorx[0]['nom_colorx']."', '".$_REQUEST["cod_colore"]."', '1', '".$_REQUEST['usr_creaci']."', NOW(), NULL, NULL) ";
            $consul_1 = new Consulta($query_1, $this->conexion, "R");
        }
    

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