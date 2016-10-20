<?php
/* ! \file: ajax_perfil_perfil.php
 *  \brief: archivo con multiples funciones ajax
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 06/04/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/* ! \class: seguri
 *  \brief: Clase seguri que gestiona las diferentes peticiones ajax 
 */

class seguri {

    private static $cConexion,
            $cCodAplica,
            $cUsuario;
    public $cServic = array();

    function __construct($co = null, $us = null, $ca = null) {
        if ($_REQUEST[Ajax] === 'on') {
            @include_once( "../lib/ajax.inc" );
            @include_once( "../lib/general/constantes.inc" );
            self::$cConexion = $AjaxConnection;
        } else {
            self::$cConexion = $co;
            self::$cUsuario = $us;
            self::$cCodAplica = $ca;
        }

        if ($_REQUEST[Ajax] === 'on') {

            switch ($_REQUEST['Option']) {

                case 'gestionarDatos':
                    self::gestionarDatos();
                    break;

                case 'cambiarEstadoUsuario':
                    self::cambiarEstadoUsuario();
                    break;

                case 'getDatosFiltro':
                    self::getDatosFiltro($_POST['cod_perfil']);
                    break;

                case 'getOtherFilters':
                    self::getOtherFilters();
                    break;

                case 'RegisterDataUser':
                    self::RegisterDataUser();
                    break;

                case 'registrarSuspencion':
                    self::registrarSuspencion();
                    break;

                case 'listaUsuarios':
                    self::listaUsuarios();
                    break;

                case 'listaNovedades':
                    self::listaNovedades();
                    break;


                default:
                    header('Location: index.php?window=central&cod_servic=1366&menant=1366');
                    break;
            }
        }
    }

    /* ! \fn: listPerfiles
     *  \brief: trae la lista de los perfiles existentes en el sistema
     *  \author: Ing. Alexander Correa
     *  \date: 06/04/2016
     *  \date modified: dia/mes/año
     *  \param:   
     *  \return html con los datos
     */

    public function listPerfiles() {
        $sql = "SELECT cod_perfil, nom_perfil, nom_respon, can_usuari, can_servic, cod_respon FROM " . BASE_DATOS . ".vis_usuari_perfil WHERE 1 ";
        $_SESSION["queryXLS"] = $sql;

        if (!class_exists(DinamicList)) {
            include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
        }

        $list = new DinamicList(self::$cConexion, $sql, "2", "no", 'ASC');
        $list->SetClose('no');
        $list->SetCreate("Crear Perfil", "onclick:formulario()");
        $list->SetHeader(utf8_decode("Código de Perfil"), "field:cod_perfil; width:1%;  ");
        $list->SetHeader("Perfil", "field:nom_perfil; width:1%");
        $list->SetHeader("Responsable", "field:nom_respon; width:1%");
        $list->SetHeader("Usuarios Asociados", "field:can_usuari; type:link; onclick:getUsersPerfil(this); width:1%");
        $list->SetHeader("Servcios Asociados", "field:can_servic; width:1%");
        $list->SetOption("Opciones", "field:cod_option; width:1%; onclikEdit:editarPerfil( this ); onclickCopy:copiarPerfil(this)");
        $list->SetHidden("cod_perfil", "0");
        $list->SetHidden("nom_perfil", "1");
        $list->SetHidden("cod_respon", "5");
        $list->Display(self::$cConexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $Html = $list->GetHtml();

        return $Html;
    }

    /* ! \fn: getServicNivel
     *  \brief: Trae los servicios por niveles
     *  \author: Ing. Fabian Salinas
     *  \date:  12/01/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: 
     *  \return: 
     */

    public function getServicNivel($cod_perfil = null) {
        if ($cod_perfil != null && $cod_perfil != "") {
            $and = " LEFT JOIN " . BASE_DATOS . ".tab_perfil_servic c ON a.cod_servic = c.cod_servic AND c.cod_perfil = $cod_perfil ";
            $and2 = " ,IF(c.cod_servic IS NULL, 0, 1) ind_select ";
        }
        $mSql = "SELECT a.cod_servic, a.nom_servic, '1' AS cod_nivelx $and2
           FROM " . CENTRAL . ".tab_genera_servic a 
       INNER JOIN " . BASE_DATOS . ".tab_perfil_servic b ON a.cod_servic = b.cod_servic 
         $and
          WHERE b.cod_perfil = '" . $_SESSION['datos_usuario']['cod_perfil'] . "'
          AND a.cod_servic NOT IN ( SELECT c.cod_serhij 
                        FROM " . CENTRAL . ".tab_servic_servic c 
                      ) 
         ORDER BY a.ind_ordenx, a.nom_servic ASC ";

        $mConsult = new Consulta($mSql, self::$cConexion);
        $mResult = $mConsult->ret_matrix('i');

        return $mResult;
        //self::getServicChildren( $mResult, 2 );
    }

    /* ! \fn: getServicChildren
     *  \brief: Trae los servicios hijos
     *  \author: Ing. Fabian Salinas
     *  \date: dd/mm/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mData   Array    Data Servic Padres
     *  \param: mNivel  Integer  Nivel del servicio
     *  \return: 
     */

    public function getServicChildren($mData = null, $mNivel, $cod_perfil = null) {
        if ($cod_perfil != null && $cod_perfil != "") {
            $and = " LEFT JOIN " . BASE_DATOS . ".tab_perfil_servic d ON a.cod_servic = d.cod_servic AND d.cod_perfil = $cod_perfil ";
            $and2 = ", IF(d.cod_servic IS NULL, 0, 1) ind_select ";
        }
        for ($i = 0; $i < sizeof($mData); $i++) {
            $this->cServic[] = $mData[$i];

            $mSql = "SELECT a.cod_servic, a.nom_servic, '$mNivel' AS cod_nivelx $and2
               FROM " . CENTRAL . ".tab_genera_servic a 
         INNER JOIN " . CENTRAL . ".tab_servic_servic b ON a.cod_servic = b.cod_serhij 
         INNER JOIN " . BASE_DATOS . ".tab_perfil_servic c ON a.cod_servic = c.cod_servic 
          $and
            WHERE b.cod_serpad = '" . $mData[$i][0] . "'               
            AND c.cod_perfil = '" . $_SESSION['datos_usuario']['cod_perfil'] . "' 

           ORDER BY a.ind_ordenx, a.nom_servic ASC ";

            $mConsult = new Consulta($mSql, self::$cConexion);
            $mResult = $mConsult->ret_matrix('i');

            self::getServicChildren($mResult, $mNivel + 1, $cod_perfil);
        }
    }

    /* ! \fn: getNewConsec
     *  \brief: consulta el ultimo conscutivo de la tabla de perfiles y devuelve el siguiente
     *  \author: Ing. Alexander Correa
     *  \date: 07/04/2016
     *  \date modified: dia/mes/año
     *  \param:   
     *  \return $cod_perfil => int => nuevo consecutivo para el siguiente registro
     */

    public function getNewConsec() {

        $sql = "SELECT MAX(cod_perfil)+1 new_consec FROM " . BASE_DATOS . ".tab_genera_perfil ";
        $mConsult = new Consulta($sql, self::$cConexion);
        $mResult = $mConsult->ret_matrix('a');

        return $mResult[0]['new_consec'];
    }

    /* ! \fn: gestionarDatos
     *  \brief: esta funcion redirije segun sea el caso creacion, edicion y copiado de perfil
     *  \author: Ing. Alexander Correa
     *  \date: 08/04/2016
     *  \date modified: dia/mes/año
     *  \param:   
     *  \return 
     */

    private function gestionarDatos() {
        $datos = (object) $_POST;

        if ($datos->ind != 2) {
            $res = $this->registrarPerfil($datos);
        } else {
            $res = $this->EditarPerfil($datos);
        }

        echo $res;
    }

    /* ! \fn: registrarPerfil
     *  \brief: registra un perfil en la base de datos con todos los servicios que se asocien
     *  \author: Ing. Alexander Correa
     *  \date: 08/04/2016
     *  \date modified: dia/mes/año
     *  \param: $datos   => objeto => informacion del perfil que se desea crear  
     *  \return booleano con el resultado de la operacion
     */

    private function registrarPerfil($datos) {
        $usr_creaci = $_SESSION['datos_usuario']['cod_usuari'];

        $sql = "INSERT INTO " . BASE_DATOS . ".tab_genera_perfil (cod_perfil, nom_perfil, cod_respon, usr_creaci, fec_creaci ) 
                                VALUES  ('$datos->cod_perfil', '$datos->nom_perfil', '$datos->cod_respon', '$usr_creaci', NOW())";

        if ($mConsult = new Consulta($sql, self::$cConexion, "BR")) {
            $sql = "INSERT INTO " . BASE_DATOS . ".tab_perfil_servic (cod_perfil, cod_servic) VALUES ";
            foreach ($datos->cod_servic as $key => $value) {
                $sql .= "($datos->cod_perfil,$value),";
            }
            $sql = trim($sql, ",") . ";";

            $mConsult = new Consulta($sql, self::$cConexion, "R");

            $novedades = explode(",", trim($datos->cod_noveda, ","));
            if ($datos->cod_noveda) {
                $sql = "INSERT INTO " . BASE_DATOS . ".tab_perfil_noveda (cod_perfil, cod_noveda) VALUES ";
                foreach ($novedades as $key => $novedad) {
                    $sql .= "('$datos->cod_perfil', '$novedad'),";
                }
                $sql = trim($sql, ",") . ";";
                $mConsult = new Consulta($sql, self::$cConexion, "RC");
            }
        }
        if ($mConsult) {
            return 1;
        } else {
            return 0;
        }
    }

    /* ! \fn: EditarPerfil
     *  \brief: Edita un perfil y los servicios que se asocien
     *  \author: Ing. Alexander Correa
     *  \date: 08/04/2016
     *  \date modified: dia/mes/año
     *  \param: $datos   => objeto => informacion del perfil que se desea editar
     *  \return return
     */

    private function EditarPerfil($datos) {
        $sql = "UPDATE " . BASE_DATOS . ".tab_genera_perfil 
                                            SET
                                    nom_perfil = '$datos->nom_perfil',
                                    cod_respon = '$datos->cod_respon',
                                    usr_modifi = '$usr_modifi', 
                                    fec_modifi = NOW()
                                    WHERE cod_perfil = $datos->cod_perfil ";

        if ($mConsult = new Consulta($sql, self::$cConexion, "BR")) {

            $sql = "DELETE FROM " . BASE_DATOS . ".tab_perfil_servic WHERE cod_perfil = $datos->cod_perfil ";

            if ($mConsult = new Consulta($sql, self::$cConexion, "R")) {

                $sql = "INSERT INTO " . BASE_DATOS . ".tab_perfil_servic (cod_perfil, cod_servic) VALUES ";

                foreach ($datos->cod_servic as $key => $value) {

                    $sql .= "($datos->cod_perfil,$value),";
                }
                $sql = trim($sql, ",") . ";";
                if ($datos->cod_noveda) {
                    $opt = "R";
                }else{
                    $opt = "RC";
                }
                $mConsult = new Consulta($sql, self::$cConexion, "R");
                
                $sql = "DELETE FROM " . BASE_DATOS . ".tab_perfil_noveda WHERE cod_perfil = $datos->cod_perfil";
                $mConsult = new Consulta($sql, self::$cConexion,$opt);
                
                if ($datos->cod_noveda) {
                    $novedades = explode(",", trim($datos->cod_noveda, ","));
                    $sql = "INSERT INTO " . BASE_DATOS . ".tab_perfil_noveda (cod_perfil, cod_noveda) VALUES ";
                    foreach ($novedades as $key => $novedad) {
                        $sql .= "('$datos->cod_perfil', '$novedad'),";
                    }
                    $sql = trim($sql, ",") . ";";
                    $mConsult = new Consulta($sql, self::$cConexion, "RC");
                }
            }
        }
        if ($mConsult) {
            return 1;
        } else {
            return 0;
        }
    }

    /* ! \fn: getResponsables
     *  \brief: trae los responsables para crear los perfiles
     *  \author: Ing. Alexander Correa
     *  \date: 08/04/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return arreglo con los responsables
     */

    public function getResponsables() {
        $sql = "SELECT cod_respon, nom_respon FROM " . BASE_DATOS . ".tab_genera_respon WHERE ind_activo = 1 ORDER BY nom_respon ASC ";
        $mConsult = new Consulta($sql, self::$cConexion);
        return $mConsult->ret_matrix('a');
    }

    /* ! \fn: listaUsuarios
     *  \brief: trae la lista de todos los usuarios del sistema
     *  \author: Ing. Alexander Correa
     *  \date: dia/mes/año
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return html con el listado ordenado
     */

    public function listaUsuarios() {

        $datos = (object) $_POST;
        if ($datos->cod_perfil) {
            $and = " AND a.cod_perfil = $datos->cod_perfil ";
        } else {
            $onclickEdit = "onclikEdit:editarUsuario( this )";
        }
        $sql = "SELECT a.cod_usuari, a.nom_usuari, b.nom_perfil, a.usr_emailx, IF(a.ind_estado = 1, 'ACTIVO', 'INACTIVO') estado, a.cod_consec, a.ind_estado
                      FROM " . BASE_DATOS . ".tab_genera_usuari a 
                INNER JOIN " . BASE_DATOS . ".tab_genera_perfil b ON a.cod_perfil = b.cod_perfil WHERE 1 $and";

        $_SESSION["queryXLS"] = $sql;

        if (!class_exists(DinamicList)) {
            include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
        }
        $estados[0] = array(0 => "", 1 => "Seleccione");
        $estados[1] = array(0 => "0", 1 => "INACTIVO");
        $estados[2] = array(0 => "1", 1 => "ACTIVO");

        $list = new DinamicList(self::$cConexion, $sql, "5", "no", 'ASC');
        $list->SetClose('no');
        if (!$datos->cod_perfil) {
            $list->SetCreate("Crear Usuario", "onclick:formulario()");
        }
        $list->SetHeader("Usuario", "field:a.cod_usuari; width:1%;  ");
        $list->SetHeader("Nombre", "field:a.nom_usuari; width:1%");
        $list->SetHeader("Perfil", "field:b.nom_perfil; width:1%");
        $list->SetHeader("E-Mail", "field:a.usr_emailx; width:1%");
        $list->SetHeader("Estado", "field:IF( a.ind_estado =1, 'ACTIVO', 'INACTIVO' ); width:1%");
        $list->SetOption("Opciones", "field:ind_estado; width:1%; onclikDisable:inactivarUsuario( this ); onclikEnable:activarUsuario( this ); $onclickEdit");
        $list->SetHidden("cod_usuari", "0");
        $list->SetHidden("nom_usuari", "1");
        $list->SetHidden("cod_consec", "5");
        $list->Display(self::$cConexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $Html = $list->GetHtml();

        if ($datos->cod_perfil) {
            $Html .= "<div class='col-md-12 text-right'>
            <button aria-disabled='false' role='button' class='ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only' onclick='closePopUp(\"popID\");' type='button'>
                <span class='ui-button-text'>Cerrar</span>
            </button></div>";
            $Html = "<div class='contenido'>" . $Html . "</div>";
            echo $Html;
        } else {
            return $Html;
        }
    }

    /* ! \fn: cambiarEstadoUsuario
     *  \brief: activa o inactiva un usuario en la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 11/04/2016
     *  \date modified: dia/mes/año
     *  \param:     
     *  \return 
     */

    private function cambiarEstadoUsuario() {
        $datos = (object) $_POST;
        $datos->usr_creaci = $_SESSION['datos_usuario']['cod_usuari'];
        // consulto el ultimo consecutivo del historial
        $sql = "SELECT MAX(cod_histor) + 1 cod_histor FROM " . BASE_DATOS . ".tab_usuari_histor ";

        $mConsult = new Consulta($sql, self::$cConexion);
        $max = $mConsult->ret_matrix("a");
        $cod_histor = $max[0]['cod_histor'];

        // consulto el usuario
        $sql = "SELECT cod_usuari FROM " . BASE_DATOS . ".tab_genera_usuari WHERE cod_consec = $datos->cod_consec";
        $mConsult = new Consulta($sql, self::$cConexion);
        $usu = $mConsult->ret_matrix("a");
        $cod_usuari = $usu[0]['cod_usuari'];

        $sql = "UPDATE " . BASE_DATOS . ".tab_genera_usuari SET ind_estado = $datos->ind_estado WHERE cod_consec = '$datos->cod_consec'";
        $mConsult = new Consulta($sql, self::$cConexion, "BR");
        $sql = "INSERT INTO " . BASE_DATOS . ".tab_usuari_histor 
                            (cod_histor, cod_usuari, obs_histor, ind_estado, usr_creaci, fec_creaci) 
                                                        VALUES 
                    ($cod_histor, '$cod_usuari', '$datos->obs_histor', $datos->ind_estado, '$datos->usr_creaci', NOW() )";
        $insercion = new Consulta($sql, self::$cConexion, "RC");
        if ($insercion = new Consulta("COMMIT", self::$cConexion)) {
            echo "1";
        } else {
            echo "2";
        }
    }

    /* ! \fn: getDataUsuario
     *  \brief: trae los datos del usuario almacenados en la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 11/04/2016
     *  \date modified: dia/mes/año
     *  \param: cod_consec => int => id del usuario    
     *  \return $datos objeto con la informacion de lucuario
     */

    public function getDataUsuario($cod_consec) {
        $sql = "SELECT a.* FROM " . BASE_DATOS . ".tab_genera_usuari a 
                        WHERE cod_consec = $cod_consec ";
        $mConsult = new Consulta($sql, self::$cConexion);
        $mResult = $mConsult->ret_matrix('a');

        $datos = (object) $mResult[0];

        return $datos;
    }

    /* ! \fn: getPerfiles
     *  \brief: trae todos los perfiles activos en el sistema
     *  \author: Ing. Alexander Correa
     *  \date: 11/04/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return arreglo con los perfiles
     */

    public function getPerfiles() {
        $sql = "SELECT cod_perfil, nom_perfil FROM " . BASE_DATOS . ".tab_genera_perfil ORDER BY nom_perfil ASC ";
        $mConsult = new Consulta($sql, self::$cConexion);
        return $mConsult->ret_matrix('a');
    }

    /* ! \fn: getGrupos
     *  \brief: trae los grupos de la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 11/04/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return 
     */

    public function getGrupos() {
        $sql = "SELECT cod_grupox, nom_grupox FROM " . BASE_DATOS . ".tab_callce_grupox ";
        $consulta = new Consulta($sql, self::$cConexion);
        return $consulta->ret_matrix("a");
    }

    /* ! \fn: getDatosFiltro
     *  \brief: muestra los diferentes filtros que aplique le perfil
     *  \author: Ing. Alexander Correa
     *  \date: 12/04/2016
     *  \date modified: dia/mes/año
     *  \param:     
     *  \return html con los select
     */

    public function getDatosFiltro($cod_perfil) {

        $filtros = self::getFiltros($cod_perfil);
        $div = "";
        if ($filtros) {
            foreach ($filtros as $key => $value) {

                if ($value['nom_filtro'] == 'Transportadora') {
                    $sql = "SELECT abr_tercer filtro FROM " . BASE_DATOS . ".tab_tercer_tercer WHERE cod_tercer = '" . $value['clv_filtro'] . "' ";
                } else if ($value['nom_filtro'] == 'Agencia') {
                    $sql = "SELECT nom_agenci filtro FROM " . BASE_DATOS . ".tab_genera_agenci WHERE cod_agenci = '" . $value['clv_filtro'] . "' ";
                }
                $consulta = new Consulta($sql, self::$cConexion);
                $dato = $consulta->ret_matrix("a");

                $div .= "<div class='col-md-6'>" . $value['nom_filtro'] . "</div><div class='col-md-6'>" . $dato[0]['filtro'] . "</div>";
            }
        } else {
            $div .= "<div class='col-md-12 text-center'><b>No hay Filtros asignados al perfil</b></div>";
        }
        echo $div;
    }

    /* ! \fn: getFiltros
     *  \brief: trae los filtros qeu tenga asignados un prfil
     *  \author: Ing. Alexander Correa
     *  \date: 12/04/2016
     *  \date modified: dia/mes/año
     *  \param:     
     *  \return arreglo con los filtros que aplican
     */

    private function getFiltros($cod_perfil) {
        if (!$cod_perfil) {
            $cod_perfil = 0;
        }
        $sql = "SELECT a.nom_filtro,b.clv_filtro
                    FROM " . CENTRAL . ".tab_genera_filtro a
                    INNER JOIN " . BASE_DATOS . ".tab_aplica_filtro_perfil b ON a.cod_filtro = b.cod_filtro
                    WHERE b.cod_perfil =  $cod_perfil 
                    ORDER BY a.cod_filtro ASC ";

        $consulta = new Consulta($sql, self::$cConexion);
        $filtros = $consulta->ret_matrix("a");

        return $filtros;
    }

    /* ! \fn: getOtherFilters
     *  \brief: muestra select con los filtros para configurar a un usuario
     *  \author: Ing. Alexander Correa
     *  \date: 12/04/2016
     *  \date modified: dia/mes/año
     *  \param: $cod_usuari = String => codigo del usuario para el cual aplicaran los filtros
     *  \return html con los filtros
     */

    public function getOtherFilters($cod_usuari) {
        $sql = "SELECT cod_filtro,nom_filtro,cod_queryx
                        FROM " . CENTRAL . ".tab_genera_filtro 
                        ORDER BY  cod_filtro ";
        $consulta = new Consulta($sql, self::$cConexion);
        $filtros = $consulta->ret_matrix("a");
        $fil_user = $this->getOtherFiltersUser($cod_usuari);

        $div = "";
        foreach ($filtros as $key => $value) {
            $consulta = new Consulta($value['cod_queryx'], self::$cConexion);
            $res = $consulta->ret_matrix("i");


            $div .= "<diiv class='col-md-12 ancho text-center'>
                <div class='col-md-6 text-right'>" . $value['nom_filtro'] . "</div>
                <div class='col-md-3'>
                    <select id='$value[cod_filtro]' name='$value[cod_filtro]' class='ancho'>
                        <option value=''>Seleccione una Opci&oacute;n</option>";
            foreach ($res as $k => $val) {
                $selected = "";

                if ($fil_user[$value['cod_filtro']] == $val[0]) {
                    $selected = "selected";
                }

                $div .= "<option $selected value='$val[0]'>" . $val[1] . "</option> ";
            }
            $div .="</select>
                </div>
                <div class='col-md-3'></div>
            </diiv>";
        }
        echo $div;
    }

    /* ! \fn: RegisterDataUser
     *  \brief: funcion para registrar los datos de usuario
     *  \author: Ing. Alexander Correa
     *  \date: 13/04/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return 1: operacion satisfactoria; 2: operacion fallida;
     */

    private function RegisterDataUser() {
        $datos = (object) $_POST; 
        if ($datos->ind == 1) {
            self::registerUser($datos);
        } else {
            self::editUser($datos);
        }
    }

    /* ! \fn: registerUser
     *  \brief: registra los datos de un usuario en la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 13/04/2016
     *  \date modified: dia/mes/año
     *  \param: $datos => object => objeto con los datos a registrar    
     *  \return boolean true: registro correcto, false: Error.
     */

    private function registerUser($datos) {
        $datos->usr_creaci = $_SESSION['datos_usuario']['cod_usuari'];
        $datos->clv_usuari = base64_encode($datos->clv_usuari);
        $datos->ind_estado = 1;
        $datos->ind_cambio = 1;
        $hoy = date("Y-m-d H:i:s");
        $datos->fec_cambio = strtotime('+' . $datos->num_diasxx . ' day', strtotime($hoy));
        $datos->fec_cambio = date('Y-m-d H:i:s', $datos->fec_cambio);
        $datos->fec_cambio = "'$datos->fec_cambio'";

        if (!$datos->cod_priori) {
            $datos->cod_priori = 1;
        }
        if (!$datos->cod_grupox) {
            $datos->cod_grupox = 'NULL';
        }
        if (!$datos->cod_contro) {
            $datos->cod_contro = 'NULL';
        }
        if (!$datos->usr_interf) {
            $datos->usr_interf = 'NULL';
        }
        $sql = "INSERT INTO " . BASE_DATOS . ".tab_genera_usuari 
                (cod_usuari, num_cedula, clv_usuari, nom_usuari, usr_emailx, ind_cambio,
                 num_diasxx, fec_cambio, cod_perfil, cod_grupox, cod_priori, cod_contro,
                 ind_estado, usr_interf, usr_creaci, fec_creaci, ali_usuari )
                VALUES
                ('$datos->cod_usuari', '$datos->num_cedula', '$datos->clv_usuari', '$datos->nom_usuari', '$datos->usr_emailx', $datos->ind_cambio,
                  $datos->num_diasxx,  $datos->fec_cambio,   '$datos->cod_perfil',  $datos->cod_grupox,   $datos->cod_priori,  $datos->cod_contro,
                  $datos->ind_estado , $datos->usr_interf, '$datos->usr_creaci', NOW(), '$datos->ali_usuari')";
        if ($consulta = new Consulta($sql, self::$cConexion)) {
            $fil = $datos->val_filtro;
            if ($datos->cod_filtro) {
                $sqlx = "INSERT INTO " . BASE_DATOS . ".tab_aplica_filtro_usuari  (cod_aplica, cod_filtro, cod_usuari, clv_filtro) VALUES ";
                foreach ($datos->cod_filtro as $key => $value) {
                    $sql = "SELECT cod_filtro FROM " . BASE_DATOS . ".tab_aplica_filtro_usuari WHERE cod_filtro = $value AND cod_perfil = $datos->cod_perfil";
                    $consulta = new Consulta($sql, self::$cConexion);
                    $filtro = $consulta->ret_matrix("a");
                    if (!$filtro) {
                        $sqlx.= " (1, $value, '$datos->cod_usuari', '$fil[$key]'),";
                    }
                }
                $sqlx = trim($sqlx, ",") . ";";
                $consulta = new Consulta($sqlx, self::$cConexion, "RC");
                if (!$insercion = new Consulta("COMMIT", self::$cConexion)) {
                    die("filtros"); //errro al registrar los filtros de usuario
                }
            }
            die("1"); //proceso correcto
        } else {
            die("usuario"); //error al registrar el usuario
        }
    }

    /* ! \fn: editUser
     *  \brief: Edita los datos de usuario
     *  \author: Ing. Alexander Correa
     *  \date: 14/04/2016
     *  \date modified: dia/mes/año
     *  \param: $datos => object => objeto con los datos     
     *  \return resultado de la operacion
     */

    private function editUser($datos) {

        if ($datos->clv_usuari) {
            $datos->clv_usuari = base64_encode($datos->clv_usuari);
            $set = ", clv_usuari = '$datos->clv_usuari'";
        }


        //pregunto por la cantidad de dias que tenia configurada el usuario, solo si cambia modifico la fecha de cambio de contraseña
        $sql = "SELECT num_diasxx FROM " . BASE_DATOS . ".tab_genera_usuari WHERE cod_consec = $datos->cod_consec";
        $consulta = new Consulta($sql, self::$cConexion);
        $dias = $consulta->ret_matrix("a");

        if ($dias[0]['num_diasxx'] != $datos->num_diasxx) {
            $hoy = date("Y-m-d H:i:s");
            $datos->fec_cambio = strtotime('+' . $datos->num_diasxx . ' day', strtotime($hoy));
            $datos->fec_cambio = date('Y-m-d H:i:s', $datos->fec_cambio);
            $set .= ", fec_cambio = '$datos->fec_cambio', num_diasxx = $datos->num_diasxx";
        }

        if ($datos->cod_priori) {
            $set .= ", cod_priori = $datos->cod_priori";
        }

        if ($datos->cod_grupox) {
            $set .= ",cod_grupox = $datos->cod_grupox";
        }

        $sql = "UPDATE " . BASE_DATOS . ".tab_genera_usuari 
                            SET 
                    num_cedula = '$datos->num_cedula',
                    nom_usuari = '$datos->nom_usuari',
                    usr_emailx = '$datos->usr_emailx', 
                    cod_perfil = '$datos->cod_perfil',
                    ali_usuari = '$datos->ali_usuari'
                                $set
                            WHERE 
                    cod_consec = $datos->cod_consec ";
        if ($mConsult = new Consulta($sql, self::$cConexion, "BR")) {
            $fil = $datos->val_filtro;

            $sql = "DELETE FROM " . BASE_DATOS . ".tab_aplica_filtro_usuari WHERE cod_usuari = '$datos->cod_usuari'";
            $consulta = new Consulta($sql, self::$cConexion, "RC");
            if ($datos->cod_filtro) {
                $sqlx = "INSERT INTO " . BASE_DATOS . ".tab_aplica_filtro_usuari  (cod_aplica, cod_filtro, cod_usuari, clv_filtro) VALUES ";
                foreach ($datos->cod_filtro as $key => $value) {
                    $sqlx.= " (1, $value, '$datos->cod_usuari', '$fil[$key]'),";
                }
                $sqlx = trim($sqlx, ",") . ";";


                $consulta = new Consulta($sqlx, self::$cConexion, "RC");
                if (!$insercion = new Consulta("COMMIT", self::$cConexion)) {
                    die("filtros"); //errro al registrar los filtros de usuario
                }
            }

            die("1"); //proceso correcto
        } else {
            die("usuario"); //error al editar el usuario
        }
    }

    /* ! \fn: getOtherFiltersUser
     *  \brief: trae los filtros asociados al usuario
     *  \author: Ing. Alexander Correa
     *  \date: 14/04/2016
     *  \date modified: dia/mes/año
     *  \param: $cod_usuari => String => Nombre de usuario    
     *  \return arreglo con la lista de filtros del usuario
     */

    private function getOtherFiltersUser($cod_usuari) {
        $sql = "SELECT cod_filtro, clv_filtro FROM " . BASE_DATOS . ".tab_aplica_filtro_usuari WHERE cod_usuari = '$cod_usuari'";
        $consulta = new Consulta($sql, self::$cConexion);
        $mData = $consulta->ret_matrix("a");

        $mResult = array();
        foreach ($mData as $key => $val) {
            $mResult[$val['cod_filtro']] = $val['clv_filtro'];
        }

        return $mResult;
    }

    /* ! \fn: listaEmpresasParametrizadas
     *  \brief: lista las empresas con sus parametrizaciones
     *  \author: Ing. Alexander Correa
     *  \date: 18/04/2016
     *  \date modified: dia/mes/año
     *  \param:     
     *  \return html con la lista de las empresas
     */

    public function listaEmpresasParametrizadas() {

        $sql = "SELECT a.cod_tercer, a.nom_tercer, c.nom_tipser, d.nom_server, if(a.cod_estado = 1, 'Activa', 'Inactiva') estado, a.dir_emailx,
                        a.cod_estado
                      FROM " . BASE_DATOS . ".tab_tercer_tercer a 
                INNER JOIN " . BASE_DATOS . ".tab_transp_tipser b ON b.cod_transp = a.cod_tercer  
                INNER JOIN " . BASE_DATOS . ".tab_genera_tipser c ON c.cod_tipser = b.cod_tipser 
                INNER JOIN " . BASE_DATOS . ".tab_genera_server d ON d.cod_server = b.cod_server 
                INNER JOIN " . BASE_DATOS . ".tab_tercer_activi e ON e.cod_tercer = a.cod_tercer 
                WHERE e.cod_activi = 1 
                GROUP BY a.cod_tercer";

        $_SESSION["queryXLS"] = $sql;

        if (!class_exists(DinamicList)) {
            include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
        }

        $list = new DinamicList(self::$cConexion, $sql, "5,1", "no", 'ASC');
        $list->SetClose('no');
        $list->SetHeader("Doc/NIT", "field:a.cod_tercer; width:1%;  ");
        $list->SetHeader("Empresa", "field:a.nom_tercer; width:1%");
        $list->SetHeader("Servicio Contratado", "field:b.nom_tipser; width:1%");
        $list->SetHeader("Servidor", "field:a.nom_server; width:1%");
        $list->SetHeader("Estado", "field:IF(a.cod_estado = 1, 'ACTIVO', 'INACTIVO') estado; width:1%");
        $list->SetHeader("E-mail", "field:dir_emailx; width:1%");
        $list->SetOption("Editar", "field:cod_estado; width:1%; onclikEdit:editarEmpresa( this )");
        $list->SetHidden("cod_tercer", "0");
        $list->SetHidden("nom_tercer", "1");
        $list->SetHidden("dir_emailx", "5");
        $list->Display(self::$cConexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $Html = $list->GetHtml();

        return $Html;
    }

    /* ! \fn: getRegistroSuspenciones
     *  \brief: trae el historico de suspenciones de una empresa
     *  \author: Ing. Alexander Correa
     *  \date: 20/04/2016
     *  \date modified: dia/mes/año
     *  \param: cod_transp => string => nit de la empresa a consultar    
     *  \return arreglo con los datos
     */

    public function getRegistroSuspenciones($cod_transp) {
        $sql = "SELECT fec_operac, usr_creaci, obs_bitaco FROM " . BASE_DATOS . ".tab_bitaco_suspen WHERE cod_transp = '$cod_transp' AND tip_bitaco = 0 OR tip_bitaco = 2 ";
        $mConsult = new Consulta($sql, self::$cConexion);
        return $mConsult->ret_matrix("a");
    }

    /* ! \fn: getRegistroActivaciones
     *  \brief: trae el historico de activaciones de una empresa
     *  \author: Ing. Alexander Correa
     *  \date: 20/04/2016
     *  \date modified: dia/mes/año
     *  \param: cod_transp => string => nit de la empresa a consultar    
     *  \return arreglo con los datos
     */

    public function getRegistroActivaciones($cod_transp) {
        $sql = "SELECT fec_operac, usr_creaci, obs_bitaco FROM " . BASE_DATOS . ".tab_bitaco_suspen WHERE cod_transp = '$cod_transp' AND tip_bitaco = 1";
        $mConsult = new Consulta($sql, self::$cConexion);
        return $mConsult->ret_matrix("a");
    }

    /* ! \fn: registrarSuspencion
     *  \brief: registra los datos de suspencion del servicio
     *  \author: Ing. Alexander Correa
     *  \date: 20/04/2016
     *  \date modified: dia/mes/año
     *  \param:     
     *  \return 
     */

    private function registrarSuspencion() {

        $datos = (object) $_POST;

        $datos->cod_tipser = $datos->sus_ealxxx + $datos->sus_monati;
        $usr_creaci = $_SESSION['datos_usuario']['cod_usuari'];
        $sql = "INSERT INTO " . BASE_DATOS . ".tab_bitaco_suspen (cod_transp, cod_tipser, tip_bitaco, obs_bitaco, fec_operac, usr_creaci) 
        VALUES 
        ('$datos->cod_tercer', $datos->cod_tipser , $datos->tip_bitaco, '$datos->obs_operac', '$datos->fec_operac $datos->hor_operac:00', '$usr_creaci');";
        $mConsult = new Consulta($sql, self::$cConexion, "BR");

        if ($datos->tip_operac == 1) {
            $sql = "UPDATE " . BASE_DATOS . ".tab_bitaco_suspen tip_bitaco = 2 WHERE tip_bitaco = 0 AND cod_transp = '$datos->cod_tercer' ";
            $consulta = new Consulta($sql, self::$cConexion, "RC");
        }

        $fec_inicio = $datos->fec_inicia;
        $fec_finali = $datos->fec_finali;
        $hor_inicio = $datos->hor_inicia;
        $hor_finali = $datos->hor_finali;

        $sql = "SELECT MAX(cod_bitsus) cod_bitsus FROM " . BASE_DATOS . ".tab_bitaco_suspen WHERE cod_transp = '$datos->cod_tercer' ";
        $mConsult = new Consulta($sql, self::$cConexion);
        $cod_bitsus = $mConsult->ret_matrix("a");
        $cod_bitsus = $cod_bitsus[0]['cod_bitsus'];

        if ($datos->dir_emailx) {
            $sql = "INSERT INTO " . BASE_DATOS . ".tab_bitaco_susema (cod_bitsus, dir_emailx, fec_inicio, fec_finali, usr_creaci) VALUES ";
            foreach ($datos->dir_emailx as $key => $value) {
                $sql .= "($cod_bitsus, '$value', '$fec_inicio[$key] $hor_inicio[$key]:00', '$fec_finali[$key] $hor_finali[$key]:00', '$usr_creaci' ),";
            }
            $sql = trim($sql, ",") . ";";
            $consulta = new Consulta($sql, self::$cConexion, "RC");
        }
        if ($commit = new Consulta("COMMIT", self::$cConexion)) {
            echo "1";
        } else {
            echo "2";
        }
    }

    /* ! \fn: listaNovedades
     *  \brief: muestra un html con la lista de novedades disponibles para asignar a un usuario
     *  \author: Ing. Alexander Correa
     *  \date: 28/06/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return 
     */

    private function listaNovedades() {
        $datos = (object) $_POST;
        $novedades = $this->getDataNovedades();
        $nov_perfi = $this->getNovPerrfil($datos->cod_perfil);
        ?>
        <div class="Style2Div">
            <div class="cellHead text-center"><b>Lista de Novedades Por Asignar</b></div>            
            <table cellpading="0" cellspacing="0" width="100%">
                <tr>
        <?php
        $j = 0;
        foreach ($novedades as $key => $novedad) {
            $check = "";
            if (in_array($novedad['cod_noveda'], $nov_perfi)) {
                $check = "checked";
            }
            if ($j % 2) {
                $class = "cellInfo2";
            } else {
                $class = "cellInfo1";
            }

            if ($key % 2) {
                $j++;
            } else {
                echo "</tr><tr>";
            }
            ?>                        
                        <td class=" <?= $class ?>"><?= $novedad['cod_noveda'] ?> - <?= utf8_encode($novedad['nom_noveda']) ?>:</td>   
                        <td class=" <?= $class ?>"><input type="checkbox" <?= $check ?> id="nov_<?= $key ?>" value="<?= $novedad['cod_noveda'] ?>"></td>                          
        <?php } ?>
                </tr>
            </table>
            <div class=" text-center">
                <input type="button" name="aceptar" value="Aceptar" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" onclick="setNovedades()"></input>
                <input type="button" name="aceptar" value="Todas" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" onclick="allOfThem()"></input>
                <input type="button" name="aceptar" value="Cancelar" class="crmButton small save ui-button ui-widget ui-state-default ui-corner-all" onclick="closePopUp()"></input>
            </div>
        </div>
        <?php
    }

    /* ! \fn: getDataNovedades
     *  \brief: trae la lista de novedades por asignar a un perfil
     *  \author: Ing. Alexander Correa
     *  \date: 28/06/2016
     *  \date modified: dia/mes/año
     *  \param:     
     *  \return array con las novedades
     */

    private function getDataNovedades() {
        $novedades = '156,150,157,131,154,203,205,204,206,153,160,166,158,169,175,162,163,176,141,194,140,151,230,132,133,75,116,146,155,248,267,202,192,266,245,164,171,161,159,165,
        167,173,174,215,186,191,299,304,307,309,311,316,315,240,226,220,184,223,224,195,237,263,231,189,260,239,214,229,216,232,236,233,219,222,238,221,217,218,228,227,225,235,178,180,177,182,197,183,181,152,137,172,247,234,91,199,198,209,149,148';
        $sql = "SELECT cod_noveda, nom_noveda FROM " . BASE_DATOS . ".tab_genera_noveda WHERE cod_noveda NOT IN ($novedades)";
        $mConsult = new Consulta($sql, self::$cConexion);
        return $mConsult->ret_matrix("a");
    }

    /* ! \fn: getNovPerrfil
     *  \brief: trae las novedades asignadas a un perfil
     *  \author: Ing. Alexander Correa
     *  \date: 28/06/2016
     *  \date modified: dia/mes/año
     *  \param: cod_perfil => integer => cdigo del perfil a consultar     
     *  \return array con las novedades del perfil
     */

    function getNovPerrfil($cod_perfil) {
        $sql = "SELECT cod_noveda FROM " . BASE_DATOS . ".tab_perfil_noveda WHERE cod_perfil = $cod_perfil";
        $mConsult = new Consulta($sql, self::$cConexion);
        $novedades = $mConsult->ret_matrix("a");
        foreach ($novedades as $key => $nov) {
            $newNov[] = $nov['cod_noveda'];
        }
        return $newNov;
    }

}

if ($_REQUEST[Ajax] === 'on')
    $_INFORM = new seguri();
?>
