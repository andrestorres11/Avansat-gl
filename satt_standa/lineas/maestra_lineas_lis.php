<?php

/****************************************************************************

NOMBRE:   MODULO_LINEAS_LIS.PHP
FUNCION:  LISTAR LINEAS DE VEHICULOS

****************************************************************************/

 /*ini_set("display_errors", true);
error_reporting(E_ALL ^ E_NOTICE);*/
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
        switch ($_REQUEST["opcion"]) {
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
        IncludeJS( 'ins_vehicu_lineas.js' );
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
        $headers = array("CODIGO LINEA", "LINEA", "CODIGO MARCA", "MARCA", "OPCION");

        $params = array(
            'option' => 'getLineas',
        );
        
        $pagination = new Pagination($this->conexion);

        $url = "../".DIR_APLICA_CENTRAL."/lineas/ajax_vehicu_lineas.php";
        $result = $pagination->view(1,$headers,$url,$params,'LINEAS',NULL,0,$options,6,1,25);

        if($vec_respon[0] == "1")
        {
            echo "<td>";
            ShowMessage("s", "Líneas", $vec_respon[1]);
            echo "</td>";
        }
        else if($vec_respon[0] == "2")
        {
            echo "<td>";
            ShowMessage("a", "Líneas", $vec_respon[1]);
            echo "</td>";
        }
        else if($vec_respon[0] == "3")
        {
            echo "<td>";
            ShowMessage("e", "Líneas", $vec_respon[1]);
            echo "</td>";
        }

        # Abre Form
        $mHtml->Form(array("action" => "index.php",
            "method" => "post",
            "name" => "form_lineas",
            "header" => "Lineas",
            "enctype" => "multipart/form-data"));

        $mHtml->Hidden(array( "name" => "cod_lineas", "id" => "cod_lineasID", 'value'=>0));
        $mHtml->Hidden(array( "name" => "cod_marcas", "id" => "cod_marcasID", 'value'=>0));
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

        

        $sql_valida = "SELECT a.cod_lineax, a.nom_lineax, a.cod_marcax, c.ind_estado, c.cod_mintra AS mintra_cliente 
            FROM ".BD_STANDA.".tab_genera_lineas a 
            LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas c ON a.cod_lineax = c.cod_mintra AND a.cod_marcax = c.cod_marcax 
            WHERE a.cod_lineax = '".$_REQUEST["cod_lineas"]."' AND a.cod_marcax = '".$_REQUEST["cod_marcas"]."' ";
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
            
            $query_1 = "UPDATE " . BASE_DATOS . ".tab_vehige_lineas SET ind_estado = IF(ind_estado = '1', '0', '1'), usr_modifi = '".$_REQUEST['usr_creaci']."',  fec_modifi = NOW() WHERE cod_mintra = '".$_REQUEST['cod_lineas']."'  AND cod_marmin = '".$_REQUEST["cod_marcas"]."'";
            $consul_1 = new Consulta($query_1, $this->conexion, "R");
        }else if($val_linea[0]["mintra_cliente"] != NULL && $val_linea[0]["ind_estado"] == "0"){
            $mensaje = "Activada";
            /* VAMOS A ACTUALIZAR OPERADOR */
            $query_1 = "UPDATE " . BASE_DATOS . ".tab_vehige_lineas SET ind_estado = IF(ind_estado = '1', '0', '1'), usr_modifi = '".$_REQUEST['usr_creaci']."',  fec_modifi = NOW() WHERE cod_mintra = '".$_REQUEST['cod_lineas']."' AND cod_marmin = '".$_REQUEST["cod_marcas"]."'";
            $consul_1 = new Consulta($query_1, $this->conexion, "R");
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

            $sql_linea_err_1 = "SELECT a.cod_lineax, a.cod_mintra, a.nom_lineax, a.cod_marcax 
                                FROM ".BASE_DATOS.".tab_vehige_lineas a 
                                WHERE a.cod_lineax = '".$_REQUEST["cod_lineas"]."' 
                                AND a.cod_marcax = '".$_REQUEST["cod_marcas"]."' 
                                AND a.cod_lineax != a.cod_mintra ";
            $con_linea_err_1 = new Consulta($sql_linea_err_1, $this->conexion);
            $err_linea_1 = $con_linea_err_1->ret_matriz();



            $sql_linea_err_2 = "SELECT a.cod_lineax, a.cod_mintra, a.nom_lineax, a.cod_marcax 
                                FROM ".BASE_DATOS.".tab_vehige_lineas a 
                                WHERE a.cod_lineax = '".$_REQUEST["cod_lineas"]."' 
                                AND a.cod_marcax = '".$_REQUEST["cod_marcas"]."' 
                                AND a.cod_marcax != a.cod_marmin ";

            
            $con_linea_err_2 = new Consulta($sql_linea_err_2, $this->conexion);
            $err_linea_2 = $con_linea_err_2->ret_matriz();

            if(sizeof($existe_marca) > 0 && sizeof($err_linea_1) == 0 && sizeof($err_linea_2) == 0){
                $query_1 = "INSERT INTO " . BASE_DATOS . ".tab_vehige_lineas ( cod_lineax, cod_marcax, cod_marmin, nom_lineax, cod_mintra, ind_estado, usr_creaci, fec_creaci, usr_modifi,  fec_modifi ) VALUES ( '".$_REQUEST['cod_lineas']."', '".$val_linea[0]['cod_marcax']."', '".$val_linea[0]["cod_marcax"]."', '".$val_linea[0]["nom_lineax"]."','".$_REQUEST['cod_lineas']."','1', '".$_REQUEST['usr_creaci']."', NOW(), NULL, NULL ) ";
                $consul_1 = new Consulta($query_1, $this->conexion, "R");
            }
        }

    

        if(sizeof($existe_marca) > 0){
            if (!mysql_errno())
            { 
                $end = new Consulta("COMMIT", $this->conexion);
                $vec_mensaj[1] .= "<br />La línea ".$val_linea[0]["nom_lineax"]." con código de ministerio ".$_REQUEST["cod_lineas"] ." de la marca ".$existe_marca[0][1]." ha sido ".$mensaje." con éxito.";
            }
            else
            {
                $end = new Consulta("ROLLBACK", $this->conexion);
                $vec_mensaj[0] = "3";
                $vec_mensaj[1] = "<br />Hubo un fallo de sistema. Por favor, vuelva a intentar.";
            }
        }else if(sizeof($err_linea_1) > 0){
            $vec_mensaj[0] = "3";
            $vec_mensaj[1] = "<br />La línea ".$val_linea[0]["nom_lineax"]." posee un error de codigos de la linea, donde los codigos de Avansat no coinciden con los Estandar, por favor contactar con soporte para solventar la novedad";
        }else if(sizeof($err_linea_2) > 0){
            $vec_mensaj[0] = "3";
            $vec_mensaj[1] = "<br />La línea ".$val_linea[0]["nom_lineax"]." posee un error de codigos de marcas de la linea, donde los codigos de Avansat no coinciden con los Estandar, por favor contactar con soporte para solventar la novedad";
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
