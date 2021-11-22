<?php

/* ! \file: ajax_tercer_tercer.php
 *  \brief: archivo con multiples funciones ajax
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 21/09/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

setlocale(LC_ALL, "es_ES");

/* ! \class: trans
 *  \brief: Clase trasn que gestiona las diferentes peticiones ajax  */

class tercer {

    private static $cConexion,
            $cCodAplica,
            $cUsuario,
            $cNull = array(array('', '-----'));

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
            switch ($_REQUEST[Option]) {
                case 'listaTerceros':
                    $cod_transp = $_REQUEST['cod_transp'];
                    self::listaTerceros($cod_transp);
                    break;
                case 'getCiudades':
                    self::getCiudades();
                    break;
                case "registrar":
                    self::registrar();
                    break;
                case "modificar":
                    self::modificar();
                    break;
                case 'eliminar':
                    self::eliminar();
                    break;
                case 'inactivar':
                    self::inactivar();
                    break;
                case 'activar':
                    self::activar();
                    break;
                case 'verificar':
                    self::verificar();
                    break;
                default:
                    header('Location: index.php?window=central&cod_servic=1366&menant=1366');
                    break;
            }
        }
    }

    /* *********************************************************************************
     *   \fn: listaTerceros                                                             *
     *  \brief: funcion para listar los terceros de una transportadoras                 *
     *  \author: Ing. Alexander Correa                                                  *
     *  \date:  4/09/2015                                                               *
     *  \date modified:                                                                 *
     *  \param: $cod_transp codigo de la Trasnportador                                  *     
     *  \param:                                                                         * 
     *  \return tabla con la lista de los terceros                                      *
     * ********************************************************************************* */
    private function listaTerceros($cod_transp) {
        $mSql = "SELECT a.cod_tercer,a.abr_tercer,g.nom_activi,
                        CONCAT( UPPER(d.abr_ciudad), '(', LEFT(e.nom_depart, 4), ') - ', LEFT(f.nom_paisxx, 3) ) abr_ciudad,
                        a.num_telmov,a.dir_domici,
                        IF(b.ind_estado = '1','Activo', 'Inactivo') cod_estado,
                        b.ind_estado cod_option
                   FROM " . BASE_DATOS . ".tab_tercer_tercer a
             INNER JOIN " . BASE_DATOS . ".tab_transp_tercer b ON a.cod_tercer = b.cod_tercer
             INNER JOIN " . BASE_DATOS . ".tab_tercer_activi c ON a.cod_tercer = c.cod_tercer
             INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad d ON d.cod_ciudad = a.cod_ciudad
             INNER JOIN " . BASE_DATOS . ".tab_genera_depart e ON e.cod_depart = d.cod_depart
             INNER JOIN " . BASE_DATOS . ".tab_genera_paises f ON f.cod_paisxx = d.cod_paisxx
             INNER JOIN " . BASE_DATOS . ".tab_genera_activi g ON g.cod_activi = c.cod_activi
                  WHERE c.cod_activi NOT IN (1,2,4) AND b.cod_transp = '$cod_transp'
               GROUP BY a.cod_tercer, c.cod_activi ";

        $_SESSION["queryXLS"] = $mSql;

        if (!class_exists(DinamicList)) {
            include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
        }

        $list = new DinamicList(self::$cConexion, $mSql, "2", "no", 'ASC');
        $list->SetClose('no');
        $list->SetCreate("Agregar Tercero", "onclick:formulario()");
        $list->SetHeader(utf8_decode("NIT/CEDULA"), "field:a.cod_tercer; width:1%;  ");
        $list->SetHeader(utf8_decode("TERCERO"), "field:a.abr_tercer; width:1%");
        $list->SetHeader(utf8_decode("ACTIVIDAD"), "field:g.nom_activi; width:1%");
        $list->SetHeader(utf8_decode("CIUDAD"), "field:d.abr_ciudad; width:1%");
        $list->SetHeader(utf8_decode("CELULAR"), "field:a.num_telmov");
        $list->SetHeader(utf8_decode("DIRECCIÓN"), "field:a.dir_domici");
        $list->SetHeader(utf8_decode("ESTADO"), "field:a.cod_estado");
        $list->SetOption(utf8_decode("OPCIONES"), "field:cod_option; width:1%; onclikDisable:editarTercero( 2, this ); onclikEnable:editarTercero( 1, this ); onclikEdit:editarTercero( 99, this );");
        $list->SetHidden("cod_agenci", "0");
        $list->SetHidden("abr_tercer", "1");

        $list->Display(self::$cConexion);

        $_SESSION["DINAMIC_LIST"] = $list;

        $Html = $list->GetHtml();

        echo $Html;
    }

    /*     * ***************************************************************************
     *  \fn: registrar                                                            *
     *  \brief: funcion para registros nuevos de terceros                         *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 15/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
     * **************************************************************************** */

    private function registrar() {
        #arma los objetos para cada una de las tablas necesarias
        $tercer = (object) $_POST['tercer']; #datos principales del tercero
        $activi = $_POST['activi']; #datos de las actividades del tercero
        #pregunto si ya hay algun tercero con el documento ingresado

        $query = "SELECT cod_tercer
            FROM " . BASE_DATOS . ".tab_tercer_tercer
            WHERE cod_tercer = '$tercer->cod_tercer'";
        $consulta = new Consulta($query, self::$cConexion, "BR");
        $indter = $consulta->ret_arreglo();

        if (!$indter) {
            if ($activi) {
                if ($tercer->cod_tipter == 2) {
                    $tercer->abr_tercer = $tercer->nom_apell1 . " " . $tercer->nom_tercer;
                } else {
                    $query = "SELECT cod_tipdoc
                        FROM " . BASE_DATOS . ".tab_genera_tipdoc
                        WHERE ind_person = 2 AND cod_paisxx = ".$tercer->cod_paisxx." LIMIT 1";
                    $consulta = new Consulta($query, self::$cConexion);
                    $tipoDocumento = $consulta->ret_matriz("a")[0]['cod_tipdoc'];
                    $tercer->cod_tipdoc = $tipoDocumento;
                }

                # consulta el departamento y pais del tercero basado en su ciudad
                $query = "SELECT cod_paisxx,cod_depart
                    FROM " . BASE_DATOS . ".tab_genera_ciudad
                   WHERE cod_ciudad = '$tercer->cod_ciudad' ";
                $consulta = new Consulta($query, self::$cConexion);

                $ciudad = $consulta->ret_matriz("a");

                $tercer->cod_paisxx = $ciudad[0][0];
                $tercer->cod_depart = $ciudad[0][1];
                #por defecto estado activo para el tercero

                $tercer->cod_estado = 1;

                $fec_actual = date("Y-m-d H:i:s");
                $usuario = $_SESSION['datos_usuario']['cod_usuari'];
                $tercer->fec_creaci = $fec_actual;
                $tercer->usr_creaci = $usuario;

                #incerta la inforacion princial del tercero
                $query = "INSERT INTO " . BASE_DATOS . ".tab_tercer_tercer(
                           cod_tercer,cod_tipdoc,nom_apell1,nom_apell2,nom_tercer,abr_tercer,
                           dir_domici,num_telef1,num_telef2,num_telmov,cod_paisxx,cod_depart,
                           cod_ciudad,cod_estado,obs_tercer,usr_creaci,fec_creaci,num_verifi,
                           dir_emailx,dir_urlweb,num_faxxxx)
                    VALUES( 
                            '$tercer->cod_tercer','$tercer->cod_tipdoc','$tercer->nom_apell1','$tercer->nom_apell2','$tercer->nom_tercer','$tercer->abr_tercer',
                            '$tercer->dir_domici','$tercer->num_telef1','$tercer->num_telef2','$tercer->num_telmov','$tercer->cod_paisxx','$tercer->cod_depart',
                            '$tercer->cod_ciudad','$tercer->cod_estado','$tercer->obs_tercer','$tercer->usr_creaci','$tercer->fec_creaci','$tercer->num_verifi',
                            '$tercer->dir_emailx','$tercer->dir_urlweb','$tercer->num_faxxx') ";
                $insercion = new Consulta($query, self::$cConexion, "BR");
                #inserta la relacion entre el tercero y la transportadora
                $query = "INSERT INTO " . BASE_DATOS . ".tab_transp_tercer 
                   (cod_transp,cod_tercer,usr_creaci,fec_creaci)
                      VALUES ('$tercer->cod_transp','$tercer->cod_tercer','$tercer->usr_creaci','$tercer->fec_creaci')";

                $insercion = new Consulta($query, self::$cConexion, "R");

                foreach ($activi as $key => $value) {
                    $query = "INSERT INTO " . BASE_DATOS . ".tab_tercer_activi
                            VALUES ('$tercer->cod_tercer','$value')";
                    $insercion = new Consulta($query, self::$cConexion, "R");
                }



                if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
                    $mensaje = "<font color='#000000'>Se Registró El Tercero: <b>$tercer->abr_tercer</b> Exitosamente.<br></font>";
                    $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
                    $mens = new mensajes();
                    echo $mens->correcto2("INSERTAR TERCERO", $mensaje);
                } else {
                    $mensaje = "<font color='#000000'>Ocurrió un Error inesperado al registrar el Tercero: <b>$tercer->abr_tercer</b><br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font>";
                    $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
                    $mens = new mensajes();
                    echo $mens->error2("INSERTAR TERCERO", $mensaje);
                }
            } else {
                $mensaje = "<font color='#000000'>Error al Registrar el tercero:  <b>$tercer->abr_tercer</b><br> Seleccione por lo menos una actividad<br></font>";
                $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
                $mens = new mensajes();
                echo $mens->error2("INSERTAR TERCERO", $mensaje);
            }
        } else {
            #reviso si ya esta relacionado con la transportadora el tercero si no inserto la relacion y finalmente llamo a la funcion actualizar
            $query = "SELECT cod_tercer
            FROM " . BASE_DATOS . ".tab_transp_tercer
            WHERE cod_tercer = '$tercer->cod_tercer' AND cod_transp = '$tercer->cod_transp' ";
            $consulta = new Consulta($query, self::$cConexion, "BR");
            $indter = $consulta->ret_arreglo();
            if (!$indter) {
                $query = "INSERT INTO " . BASE_DATOS . ".tab_transp_tercer 
                   (cod_transp,cod_tercer,usr_creaci,fec_creaci)
                      VALUES ('$tercer->cod_transp','$tercer->cod_tercer','$tercer->usr_creaci','$tercer->fec_creaci')";

                $insercion = new Consulta($query, self::$cConexion, "R");
            }
            $this->modificar("Registró");

            /* $mensaje = "<font color='#000000'>Error al Registrar el tercero:  <b>$tercer->abr_tercer</b><br> Ya existe otró con el número de documento o NIT: <b> $tercer->cod_tercer </b><br> verifique e intente nuevamente.<br></font>";
              $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
              $mens = new mensajes();
              echo $mens->error2("INSERTAR TERCERO", $mensaje); */
        }
    }

    /*     * ***************************************************************************
     *  \fn: activar                                                              *
     *  \brief: función para activar una transportadora                           *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 07/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
     * **************************************************************************** */

    private function activar() {
        $tercer = (object) $_POST['propie']; //objeto para la tabla tab_tercer_tercer
        $tercer->usr_modifi = $_SESSION['datos_usuario']['cod_usuari'];
        $tercer->fec_modifi = date("Y-m-d H:i:s");
        if (!$tercer->cod_tercer) {
            $tercer->cod_transp = $_POST['cod_tercer'];
            $tercer->cod_tercer = $_POST['cod_agenci'];
            $tercer->abr_tercer = $_POST['nom_tercer'];
        }

        $query = "UPDATE " . BASE_DATOS . ".tab_transp_tercer 
                        SET ind_estado = 1,
                            usr_modifi = '$tercer->usr_modifi',
                            fec_modifi = '$tercer->fec_modifi'
                      WHERE cod_tercer = '$tercer->cod_tercer'
                        AND cod_transp = '$tercer->cod_transp'";

        $insercion = new Consulta($query, self::$cConexion, "R");
        if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
            $mensaje = "Se activó el Tercero: " . $tercer->abr_tercer . " exitosamente.";
            $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            $mens->correcto2("ACTIVAR TERCERO", $mensaje);
        }
    }

    /*     * ****************************************************************************
     *  \fn: inactivar                                                              *
     *  \brief: función para inactivar una transportadora                           *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 07/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
     * **************************************************************************** */

    private function inactivar() {
        $tercer = (object) $_POST['propie']; //objeto para la tabla tab_tercer_tercer
        $tercer->usr_modifi = $_SESSION['datos_usuario']['cod_usuari'];
        $tercer->fec_modifi = date("Y-m-d H:i:s");
        if (!$tercer->cod_tercer) {
            $tercer->cod_transp = $_POST['cod_tercer'];
            $tercer->cod_tercer = $_POST['cod_agenci'];
            $tercer->abr_tercer = $_POST['nom_tercer'];
        }

        $query = "UPDATE " . BASE_DATOS . ".tab_transp_tercer 
                        SET ind_estado = 0,
                            usr_modifi = '$tercer->usr_modifi',
                            fec_modifi = '$tercer->fec_modifi'
                      WHERE cod_tercer = '$tercer->cod_tercer'
                        AND cod_transp = '$tercer->cod_transp'";

        $insercion = new Consulta($query, self::$cConexion, "R");
        if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
            $mensaje = "Se inactivó el Tercero: " . $tercer->abr_tercer . " exitosamente.";
            $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            $mens->correcto2("INACTIVAR TERCERO", $mensaje);
        }
    }

    /*     * ****************************************************************************
     *  \fn: modificar                                                            *
     *  \brief: función para modificar una conductores                            *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 23/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
     * **************************************************************************** */

    private function modificar($operacion = null) {
        if (!$operacion) {
            $operacion = "Modificó";
        }
        $tercer = (object) $_POST['tercer']; #datos principales del tercero
        $activi = $_POST['activi']; #datos de las actividades del tercero

        $query = "SELECT cod_tipdoc
        FROM " . BASE_DATOS . ".tab_genera_tipdoc
        WHERE ind_person = 2 AND cod_paisxx = ".$tercer->cod_paisxx." LIMIT 1";
        $consulta = new Consulta($query, self::$cConexion);
        $tipoDocumento = $consulta->ret_matriz("a")[0]['cod_tipdoc'];

        if ($tercer->cod_tipter == 2) {
            $tercer->abr_tercer = $tercer->nom_apell1 . " " . $tercer->nom_tercer;
        } else {
           
            $tercer->cod_tipdoc = $tipoDocumento;
        }
        if ($activi) {
            if (!$tercer->cod_tipdoc) {
                $tercer->cod_tipdoc = $tipoDocumento;
            }
            # consulta el departamento y pais del tercero basado en su ciudad
            $query = "SELECT cod_paisxx,cod_depart
                  FROM " . BASE_DATOS . ".tab_genera_ciudad
                 WHERE cod_ciudad = '$tercer->cod_ciudad' ";

            $consulta = new Consulta($query, self::$cConexion);

            $ciudad = $consulta->ret_matriz("a");

            #añade los codigos de departamento y ciudad
            $tercer->cod_paisxx = $ciudad[0][0];
            $tercer->cod_depart = $ciudad[0][1];

            #añade usuario y fecha de modificacion
            $tercer->fec_modifi = date("Y-m-d H:i:s");
            $tercer->usr_modifi = $_SESSION['datos_usuario']['cod_usuari'];

            #actualiza la informacion basica del tercero
            $query = "UPDATE " . BASE_DATOS . ".tab_tercer_tercer
                                      SET
                          cod_tipdoc = '$tercer->cod_tipdoc',
                          nom_apell1 = '$tercer->nom_apell1',
                          nom_apell2 = '$tercer->nom_apell2',
                          nom_tercer = '$tercer->nom_tercer',
                          abr_tercer = '$tercer->abr_tercer',
                          dir_domici = '$tercer->dir_domici',
                          num_telef1 = '$tercer->num_telef1',
                          num_telef2 = '$tercer->num_telef2',
                          num_telmov = '$tercer->num_telmov',
                          cod_paisxx = '$tercer->cod_paisxx',
                          cod_depart = '$tercer->cod_depart',
                          cod_ciudad = '$tercer->cod_ciudad',
                          obs_tercer = '$tercer->obs_tercer',
                          usr_modifi = '$tercer->usr_modifi',
                          fec_modifi = '$tercer->fec_modifi',
                          num_verifi = '$tercer->num_verifi',
                          dir_emailx = '$tercer->dir_emailx',
                          dir_urlweb = '$tercer->dir_urlweb',
                          num_faxxxx = '$tercer->num_faxxx'
                                      WHERE
                          cod_tercer = '$tercer->cod_tercer'";
            $insercion = new Consulta($query, self::$cConexion, "BR");

            #elimina las antiguas actividades del tercero 
            $query = "DELETE FROM " . BASE_DATOS . ".tab_tercer_activi
                     WHERE cod_tercer = '$tercer->cod_tercer'
                       AND cod_activi <> 1 ";
            $eliminacion = new Consulta($query, self::$cConexion, "BR");

            #inserta las nuevas actividades
            foreach ($activi as $key => $value) {
                $query = "INSERT INTO " . BASE_DATOS . ".tab_tercer_activi
                      VALUES ('$tercer->cod_tercer','$value')";
                $insercion = new Consulta($query, self::$cConexion, "R");
            }

            if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
                $mensaje = "<font color='#000000'>Se " . $operacion . " El Tercero: <b>$tercer->abr_tercer</b> Exitosamente.<br></font>";
                $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
                $mens = new mensajes();
                echo $mens->correcto2("MODIFICAR TERCERO", $mensaje);
            } else {
                $mensaje = "<font color='#000000'>Ocurrió un Error inesperado al modificar el Tercero: <b>$tercer->abr_tercer</b><br> verifique e intente nuevamente.<br>Si el error persiste informe a mesa de apoyo</font>";
                $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
                $mens = new mensajes();
                echo $mens->error2("MODIFICAR TERCERO", $mensaje);
            }
        } else {
            $mensaje = "<font color='#000000'>Error al modificar el Tercero: <b>$tercer->abr_tercer</b><br> Seleccione por lo menos una actividad</font>";
            $mensaje .= "<br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closePopUp()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            echo $mens->error2("MODIFICAR TERCERO", $mensaje);
        }
    }

    /*     * ****************************************************************************
     *  \fn: getDatosConductor                                                    *
     *  \brief: función que consulta los datos de un coductor                     *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 16/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return $data => objeto con los datos de la consulta                      *
     * **************************************************************************** */

    public function getDatosTerero($cod_tercer = '0', $cod_transp = NULL) {
        #objeto que contiene los datos a retornar 
        $datos = new stdClass();

        $tipoTercero [0][0] = "";
        $tipoTercero [0][1] = "Seleccione una Opción";
        $tipoTercero [1][0] = 1;
        $tipoTercero [1][1] = "Jurídica";
        $tipoTercero [2][0] = 2;
        $tipoTercero [2][1] = "Natural";
        $datos->tipoTercero = $tipoTercero;

        $query = "SELECT cod_paisxx
        FROM ".BASE_DATOS.".tab_tercer_tercer
          WHERE cod_tercer = '".$cod_transp."'
        LIMIT 1";
        $consulta = new Consulta($query, self::$cConexion);
        $cod_paisxx = $consulta->ret_matriz("a")[0]['cod_paisxx'];

        #añade los regimenes
        $query = "SELECT cod_terreg,nom_terreg
              FROM " . BASE_DATOS . ".tab_genera_terreg
               ORDER BY 2";
        $consulta = new Consulta($query, self::$cConexion);
        $regimen = $consulta->ret_matriz("a");
        $datos->regimen = $regimen;

        #añade los tipos de documento
        $query = "SELECT cod_tipdoc,nom_tipdoc
                 FROM " . BASE_DATOS . ".tab_genera_tipdoc
                 WHERE ind_person = 1 AND cod_paisxx = ".$cod_paisxx;;
        $consulta = new Consulta($query, self::$cConexion);
        $tipoDocumento = $consulta->ret_matriz("a");
        $datos->tipoDocumento = self::cleanArray($tipoDocumento);

        #añade las actividades
        $query = "SELECT a.cod_activi,a.nom_activi
               FROM " . BASE_DATOS . ".tab_genera_activi a
              WHERE a.cod_activi NOT IN (" . COD_FILTRO_EMPTRA . "," . COD_FILTRO_AGENCI . "," . COD_FILTRO_CONDUC . ")";
        $consulta = new Consulta($query, self::$cConexion);
        $actividades = $consulta->ret_matriz("a");
        foreach ($actividades as $key => $value) {
            $datos->actividades->$key = (object) $value;
        }

        #añade las calificaciones
        $query = "SELECT cod_califi,nom_califi
               FROM " . BASE_DATOS . ".tab_genera_califi
               ORDER BY 2";
        $consulta = new Consulta($query, self::$cConexion);
        $calificacion = $consulta->ret_matriz("a");
        $datos->calificacion = $calificacion;

        #añade las categorias de licencia
        $query = "SELECT cod_catlic,nom_catlic
                 FROM " . BASE_DATOS . ".tab_genera_catlic
             ORDER BY 2";
        $consulta = new Consulta($query, self::$cConexion);
        $categorias = $consulta->ret_matriz("a");

        $datos->categorias = $categorias;

        #consulta los datos del tercero
        $query = "SELECT a.cod_tercer, a.nom_tercer,a.abr_tercer,a.cod_ciudad,
                   a.dir_domici,a.num_telef1, a.dir_emailx,a.obs_tercer,a.cod_estado,
                   a.num_telmov,a.dir_urlweb, a.cod_tipdoc,a.nom_apell1,a.nom_apell2,
                   a.num_faxxxx,a.num_telef2, a.cod_terreg,a.num_verifi,a.cod_paisxx,
                   a.cod_depart,a.usr_creaci, a.fec_creaci,a.usr_modifi, a.fec_modifi,
                   CONCAT( UPPER(b.abr_ciudad), '(', LEFT(c.nom_depart, 4), ') - ', LEFT(d.nom_paisxx, 3) ) abr_ciudad,
                   a.num_faxxxx num_faxxx
              FROM " . BASE_DATOS . ".tab_tercer_tercer a
              INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad b ON b.cod_ciudad = a.cod_ciudad
              INNER JOIN " . BASE_DATOS . ".tab_genera_depart c ON c.cod_depart = b.cod_depart
              INNER JOIN " . BASE_DATOS . ".tab_genera_paises d ON d.cod_paisxx = b.cod_paisxx
                   WHERE a.cod_tercer = '$cod_tercer' ";

        $consulta = new Consulta($query, self::$cConexion);
        $conductor = $consulta->ret_matrix("a");

        #agrega el resultado al objeto de retorno
        $datos->principal = (object) $conductor[0];

        #por ultimo las actividades del tercero
        $query = "SELECT cod_activi FROM " . BASE_DATOS . ".tab_tercer_activi
                   WHERE cod_tercer = '$cod_tercer'";
        $insercion = new Consulta($query, self::$cConexion);
        $activities = $insercion->ret_matrix("a");

        foreach ($activities as $key => $value) {
            $activities[$key] = $value['cod_activi'];
        }
        #por ultimo consulta todas las referencias laborales del conductor
        $query = "SELECT nom_empre,tel_empre,num_viajes,num_atigue,nom_mercan FROM " . BASE_DATOS . ".tab_conduc_refere WHERE cod_conduc = '$cod_tercer'";
        $consulta = new Consulta($query, self::$cConexion);
        $referencias = $consulta->ret_matrix("a");


        foreach ($referencias as $key => $value) {
            $datos->referencias->$key = (object) $value;
        }

        $datos->activities = $activities;
        return $datos;
    }

    private function verificar() {
        $tercero = $_POST['tercero'];
        $query = "SELECT cod_tercer
            FROM " . BASE_DATOS . ".tab_tercer_tercer
            WHERE cod_tercer = '$tercero'";
        $consulta = new Consulta($query, self::$cConexion, "BR");
        $indter = $consulta->ret_arreglo();
        if (!$indter) {
            echo true;
        } else {
            $datos = $this->getDatosTerero($tercero);
            $datos = json_encode($datos);
            echo $datos;
        }
    }

    /*! \fn: cleanArray
           *  \brief: Limpia los datos de cualquier caracter especial para corregir codificaciÃ³n
           *  \author: Ing. Luis Manrique
           *  \date: 03-04-2020
           *  \date modified: dd/mm/aaaa
           *  \param: $arrau => Arreglo que serÃ¡ analizado por la funciÃ³n
           *  \return: array
        */
        function cleanArray($array){

            $arrayReturn = array();
  
            //Convert function
            $convert = function($value){
                if(is_string($value)){
                    return utf8_encode($value);
                }
                return $value;
            };
  
            //Go through data
            foreach ($array as $key => $value) {
                //Validate sub array
                if(is_array($value)){
                    //Clean sub array
                    $arrayReturn[$convert($key)] = self::cleanArray($value);
                }else{
                    //Clean value
                    $arrayReturn[$convert($key)] = $convert($value);
                }
            }
            //Return array
            return $arrayReturn;
        }

}

if ($_REQUEST[Ajax] === 'on')
    $_INFORM = new tercer();
?>