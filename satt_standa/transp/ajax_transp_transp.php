<?php

/* ! \file: ajax_transp_transp.php
 *  \brief: archivo con multiples funciones ajax
 *  \author: Ing. Alexander Correa
 *  \author: alexander.correa@intrared.net
 *  \version: 1.0
 *  \date: 31/09/2015
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

setlocale(LC_ALL, "es_ES");

/* ! \class: trans
 *  \brief: Clase trasn que gestiona las diferentes peticiones ajax 
 */
class trans {

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
                case 'buscarTransportadora':
                    self::buscarTransportadora();
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

                case 'registrarSeguimAditional':
                    self::registrarSeguimAditional();
                    break;

                case 'getServicesAditionals':
                    self::getServicesAditionals();
                    break;

                case 'inactivarConfig':
                    self::inactivarConfig();
                    break;

                default:
                    header('Location: index.php?window=central&cod_servic=1366&menant=1366');
                    break;
            }
        }
    }

    /* ! \fn: buscarTransportadora
     *  \brief: funcion para buscar una trasnportadora en la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 31/09/2015
     *  \date modified: 
     *  \param: 
     *  \param: 
     *  \return json o matriz
     */

    private function buscarTransportadora() {
        $mSql = "SELECT a.cod_tercer, UPPER(a.abr_tercer) AS nom_tercer 
                      FROM " . BASE_DATOS . ".tab_tercer_tercer a 
                      INNER JOIN " . BASE_DATOS . ".tab_tercer_activi b ON a.cod_tercer = b.cod_tercer 
                            WHERE b.cod_activi = " . COD_FILTRO_EMPTRA; /* . "
          AND a.cod_estado = " . COD_ESTADO_ACTIVO . " "; */

        if ($_SESSION['datos_usuario']['cod_perfil'] == NULL) {#PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Usuari(1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_usuari]);
            if ($filtro->listar(self::$cConexion)) :
                $datos_filtro = $filtro->retornar();
                $mSql .= " AND a.cod_tercer = '" . $datos_filtro[clv_filtro] . "' ";
            endif;
        }else {#PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Perfil(1, COD_FILTRO_EMPTRA, $_SESSION[datos_usuario][cod_perfil]);
            if ($filtro->listar(self::$cConexion)) :
                $datos_filtro = $filtro->retornar();
                $mSql .= " AND a.cod_tercer = '" . $datos_filtro[clv_filtro] . "' ";
            endif;
        }

        $mSql .= $_REQUEST[term] ? " AND (a.abr_tercer LIKE '%" . $_REQUEST[term] . "%' OR a.cod_tercer LIKE '%" . $_REQUEST[term] . "%' )" : "";
        $mSql .= " ORDER BY a.abr_tercer ASC ";
        $consulta = new Consulta($mSql, self::$cConexion);
        $mResult = $consulta->ret_matrix('a');

        if ($_REQUEST[term]) {
            $mTranps = array();
            for ($i = 0; $i < sizeof($mResult); $i++) {
                $mTxt = $mResult[$i][cod_tercer] . " - " . utf8_decode($mResult[$i][nom_tercer]);
                $mTranps[] = array('value' => utf8_decode($mResult[$i][nom_tercer]), 'label' => $mTxt, 'id' => $mResult[$i][cod_tercer]);
            }
            echo json_encode($mTranps);
        } else
            return $mResult;
    }

    /* ! \fn: getCiudades
     *  \brief: Trae las ciudades
     *  \author: Ing. Fabian Salinas
     *  \date: 05/08/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return: Matriz
     */

    public function getCiudades($mCodCiudad = NULL) {
        $mSql = "SELECT a.cod_ciudad, 
                        CONCAT( UPPER(a.abr_ciudad), ' (', LEFT(b.nom_depart, 4), ') - ', LEFT(c.nom_paisxx, 3) ) AS abr_ciudad
                   FROM " . BASE_DATOS . ".tab_genera_ciudad a 
             INNER JOIN " . BASE_DATOS . ".tab_genera_depart b 
                     ON a.cod_paisxx = b.cod_paisxx 
                    AND a.cod_depart = b.cod_depart 
             INNER JOIN " . BASE_DATOS . ".tab_genera_paises c 
                     ON a.cod_paisxx = c.cod_paisxx 
                  WHERE 1=1 ";

        $mSql .= $_REQUEST[term] ? " AND a.abr_ciudad LIKE '%" . $_REQUEST[term] . "%' " : "";
        $mSql .= $mCodCiudad != NULL ? " AND a.cod_ciudad = '{$mCodCiudad}' " : "";

        $mSql .= " ORDER BY a.abr_ciudad ";

        $mConsult = new Consulta($mSql, self::$cConexion);
        $mResult = $mConsult->ret_matrix('a');

        if ($_REQUEST[term]) {
            $ciudades = array();
            for ($i = 0; $i < sizeof($mResult); $i++) {
                $mTxt = $mResult[$i][cod_ciudad] . " - " . utf8_decode($mResult[$i][abr_ciudad]);
                $ciudades[] = array('value' => utf8_decode($mResult[$i][abr_ciudad]), 'label' => $mTxt, 'id' => $mResult[$i][cod_ciudad]);
            }
            echo json_encode($ciudades);
        } else
            return $mResult;
    }

    /* ! \fn: funcion getTrasnportadoras()
     *  \brief: funcion que devuelve la lista completa de las transportadoras consignadas en la base de datos
     *  \author: Ing. Alexander Correa
     *  \date: 07/09/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return $datos -> objeto con la informacion de las transportadoras
     */

    public function getTrasnportadoras() {
        $query = "SELECT a.cod_tercer, a.abr_tercer, a.dir_domici, 
                         a.num_telef1, a.dir_emailx, a.cod_estado,
                         CONCAT( UPPER(b.abr_ciudad), '(', LEFT(c.nom_depart, 4), ') - ', LEFT(d.nom_paisxx, 3) ) abr_ciudad  
                         FROM " . BASE_DATOS . ".tab_tercer_tercer a 
                         INNER JOIN tab_genera_ciudad b ON b.cod_ciudad = a.cod_ciudad 
                         INNER JOIN tab_genera_depart c ON b.cod_depart = b.cod_depart 
                         INNER JOIN tab_genera_paises d ON b.cod_paisxx = c.cod_paisxx
                         INNER JOIN tab_tercer_activi e ON e.cod_tercer = a.cod_tercer
                         WHERE e.cod_activi = 1
                          GROUP BY a.cod_tercer ORDER BY abr_tercer ASC ";
        $consulta = new Consulta($query, self::$cConexion);
        $transpor = $consulta->ret_matrix("a");
    }

    /* ! \fn: funcion getDatosTrasnportadora()
     *  \brief: funcion que extrae los datos de una transportadora de la base de datos para su visualización o edición
     *  \author: Ing. Alexander Correa
     *  \date: 01/09/2015
     *  \date modified: dia/mes/año
     *  \param: $cod_transp -> id de la transportadora a consultar
     *  \param: 
     *  \return $datos -> objeto con la informacion de la transportadora
     */

    public function getDatosTrasnportadora($cod_transp) {
        if (!$cod_transp) {
            $cod_transp = '0';
        }
        $datos = new stdClass();

        #consulta los datos basicos de la transportadora
        $query = "SELECT a.cod_tercer,a.num_verifi,a.abr_tercer,a.nom_tercer,
                       b.cod_minins,a.cod_ciudad,a.dir_domici,a.num_telef1,
                       a.cod_terreg,a.obs_tercer,b.ind_cobnal,b.ind_cobint,
                       b.nro_habnal,b.fec_resnal,b.num_region,b.num_resolu,
                       b.ran_iniman,b.ran_finman,b.ind_gracon,b.ind_ceriso,
                       b.fec_ceriso,b.ind_cerbas,b.fec_cerbas,b.otr_certif,
                       a.dir_emailx,c.cod_modali, CONCAT( UPPER(d.abr_ciudad), '(', LEFT(e.nom_depart, 4), ') - ', LEFT(f.nom_paisxx, 3) ) abr_ciudad,
                       g.cod_terreg,g.nom_terreg, h.cod_minins,i.cod_agenci, i.nom_agenci,
                       i.con_agenci,i.dir_emailx,i.cod_ciudad cod_ciudaa, CONCAT( UPPER(k.abr_ciudad), '(', LEFT(l.nom_depart, 4), ') - ', LEFT(m.nom_paisxx, 3) ) abr_ciudaa,
                       i.dir_agenci,i.tel_agenci,i.num_faxxxx, a.cod_estado
                  FROM " . BASE_DATOS . ".tab_tercer_tercer a

                       INNER JOIN " . BASE_DATOS . ".tab_tercer_emptra b ON  a.cod_tercer = b.cod_tercer
                       LEFT JOIN " . BASE_DATOS . ".tab_emptra_modali c ON c.cod_emptra = a.cod_tercer
                       INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad d ON d.cod_ciudad = a.cod_ciudad
                       INNER JOIN " . BASE_DATOS . ".tab_genera_depart e ON e.cod_depart = d.cod_depart
                       INNER JOIN " . BASE_DATOS . ".tab_genera_paises f ON f.cod_paisxx = d.cod_paisxx
                       LEFT JOIN " . BASE_DATOS . ".tab_genera_terreg  g ON g.cod_terreg = a.cod_terreg
                       LEFT JOIN " . BASE_DATOS . ".tab_tercer_emptra h ON h.cod_tercer = a.cod_tercer
                       INNER JOIN " . BASE_DATOS . ".tab_transp_agenci j ON j.cod_transp = a.cod_tercer
                       INNER JOIN " . BASE_DATOS . ".tab_genera_agenci i ON i.cod_agenci = j.cod_agenci
                       INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad k ON k.cod_ciudad = i.cod_ciudad
                       INNER JOIN " . BASE_DATOS . ".tab_genera_depart l ON l.cod_depart = k.cod_depart
                       INNER JOIN " . BASE_DATOS . ".tab_genera_paises m ON m.cod_paisxx = k.cod_paisxx
                 WHERE 
                       a.cod_tercer = '" . $cod_transp . "'
               ";
        /* echo "<pre>";
          echo $query;die; */
        $consulta = new Consulta($query, self::$cConexion);
        $transpor = $consulta->ret_matrix("a");
        $datos->principal = (object) $transpor[0];
        $datos->principal->dir_domici = utf8_encode($datos->principal->dir_domici);

        #consulta la lista de regimenes para la lista
        $query = "SELECT cod_terreg,nom_terreg
                   FROM " . BASE_DATOS . ".tab_genera_terreg
               ORDER BY 2";
        $consulta = new Consulta($query, self::$cConexion);
        $regimen = $consulta->ret_matrix("i");
        $regimen = array_merge(self::$cNull, $regimen);

        // consulta la lista de modalidades para los check button
        $query = "SELECT cod_modali FROM " . BASE_DATOS . ".tab_emptra_modali WHERE cod_emptra = " . $cod_transp . " ORDER BY cod_modali ASC";
        $consulta = new Consulta($query, self::$cConexion);
        $modalidad = $consulta->ret_matrix("i");

        #agrega la lista de regimenes a la respuesta
        $datos->regimen = $regimen;
        #agrega la lista de modalidades a la respuesta
        $datos->modalidades = $modalidad;

        return $datos;
    }

    /*     * ****************************************************************************
     *  \fn: registrar                                                            *
     *  \brief: funcion para registros nuevos de transportadoras                  *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 31/08/2015                                                         *
     *  \date modified:                                                           *
     *  \param: operacion: string con la operacion a realizar.                    *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
     * **************************************************************************** */

    private function registrar() {

        # lleno los objetos nesesarios para la correcta insercion en la base de datos.
        $transp = (object) $_POST['transp']; //objeto para llenar la tabla tab_tercer_tercer
        $emptra = (object) $_POST['emptra']; //objeto para llenar la tabla tab_tercer_emptra
        $agencia = (object) $_POST['agencia']; //objeto para llenar la tabla tab_genera_agenci
        $modali = (object) $_POST['modali']; //para las modalidades de la transportadora

        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];

        #completo los objetos con datos dependientes de otros objetos
        $transp->fec_creaci = $fec_actual;
        $transp->usr_creaci = $usuario;
        $emptra->cod_tercer = $transp->cod_tercer;
        $emptra->fec_creaci = $fec_actual;
        $emptra->usr_creaci = $usuario;
        $agencia->dir_emailx = $transp->dir_emailx;
        $agencia->num_faxxxx = $transp->num_faxxxx;
        $agencia->usr_creaci = $usuario;
        $agencia->cod_tercer = $transp->cod_tercer;

        #consulta si ya existe algun registro en la tabla tab_tercer_tercer con el mismo nit
        $sql = "SELECT cod_tercer FROM " . BASE_DATOS . ".tab_tercer_tercer WHERE cod_tercer = '$transp->cod_tercer'";
        $consulta = new Consulta($sql, self::$cConexion);
        $tercero = $consulta->ret_matriz("a");
        if (!$tercero) {
            # consulta el departamento y pais de la transportadora basado en su ciudad
            $query = "SELECT cod_paisxx,cod_depart
                    FROM " . BASE_DATOS . ".tab_genera_ciudad
                   WHERE cod_ciudad = $transp->cod_ciudad ";


            $consulta = new Consulta($query, self::$cConexion);
            $ciudad = $consulta->ret_matriz("a");

            $transp->cod_paisxx = $ciudad[0][0];
            $transp->cod_depart = $ciudad[0][1];

            //insercion del principal
            $query = "INSERT INTO " . BASE_DATOS . ".tab_tercer_tercer(
              cod_tercer, num_verifi, cod_tipdoc,
              cod_terreg, nom_tercer, abr_tercer,
              dir_domici, num_telef1, cod_paisxx, 
              cod_depart, cod_ciudad, dir_emailx,
              num_faxxxx, obs_tercer, usr_creaci, 
              fec_creaci, cod_estado)
                  VALUES(
                    '$transp->cod_tercer','$transp->num_verifi','N',
                    '$transp->cod_terreg', '$transp->nom_tercer', '$transp->abr_tercer', 
                    '$transp->dir_domici', '$transp->num_telef1', '$transp->cod_paisxx',
                    '$transp->cod_depart', '$transp->cod_ciudad', '$transp->dir_emailx',
                    '$transp->num_faxxxx', '$transp->obs_tercer', '$transp->usr_creaci', 
                    '$transp->fec_creaci', '1')";


            $insercion = new Consulta($query, self::$cConexion, "BR");

            $query = "INSERT INTO " . BASE_DATOS . ".tab_tercer_activi
                        (cod_tercer,cod_activi)
                 VALUES ('$transp->cod_tercer'," . COD_FILTRO_PROPIE . ")
            ";

            $insercion = new Consulta($query, self::$cConexion, "R");

            $query = "INSERT INTO " . BASE_DATOS . ".tab_tercer_activi
                        (cod_tercer,cod_activi)
                 VALUES ('$transp->cod_tercer'," . COD_FILTRO_POSEED . ")
            ";

            $insercion = new Consulta($query, self::$cConexion, "R");

            //insercion de la actividad del tercero
            $query = "INSERT INTO " . BASE_DATOS . ".tab_tercer_activi
                VALUES ('$transp->cod_tercer'," . COD_FILTRO_EMPTRA . ")";
            $insercion = new Consulta($query, self::$cConexion, "R");

            //iso
            if (!$emptra->ind_ceriso) {
                $emptra->ind_ceriso = 'N';
            }
            //basc
            if (!$emptra->ind_cerbasc) {
                $emptra->ind_cerbasc = 'N';
            }
            //cobertura nacional
            if (!$emptra->ind_cobnal) {
                $emptra->ind_cobnal = 'N';
            }

            //cobertura internacional
            if (!$emptra->ind_cobint) {
                $emptra->ind_cobint = 'N';
            }

            //gran contribuyente
            if (!$emptra->ind_gracon) {
                $emptra->ind_gracon = 'N';
            }

            $query = "INSERT INTO " . BASE_DATOS . ".tab_tercer_emptra
                        (cod_tercer,cod_minins,num_resolu,fec_resolu,num_region,ran_iniman,
                         ran_finman,ind_gracon,ind_ceriso,fec_ceriso,ind_cerbas,fec_cerbas,
                         otr_certif,ind_cobnal,ind_cobint,nro_habnal,fec_resnal,nom_repleg,
                         usr_creaci,fec_creaci)
                        VALUES (
                        '$emptra->cod_tercer','$emptra->cod_minins','$emptra->num_resolu','$emptra->fec_resolu','$emptra->num_region','$emptra->ran_iniman',
                        '$emptra->ran_finman','$emptra->ind_gracon','$emptra->ind_ceriso','$emptra->fec_ceriso','$emptra->ind_cerbas','$emptra->fec_cerbas',
                        '$emptra->otr_certif','$emptra->ind_cobnal','$emptra->ind_cobint','$emptra->nro_habnal','$emptra->fec_resolu','$emptra->nom_repleg',
                        '$emptra->usr_creaci','$emptra->fec_creaci')";
            $insercion = new Consulta($query, self::$cConexion, "R");


            #conulta pais y dpto de la agencia por su ciudad
            $query = "SELECT cod_paisxx,cod_depart
                       FROM " . BASE_DATOS . ".tab_genera_ciudad
                      WHERE cod_ciudad = '$agencia->cod_ciudad'";
            $consulta = new Consulta($query, self::$cConexion);
            $ciused = $consulta->ret_matriz();
            $agencia->cod_paisxx = $ciused[0][0];
            $agencia->cod_depart = $ciused[0][1];

            //consecutivo de la sede
            $query = "SELECT MAX( CAST(cod_agenci AS UNSIGNED) ) AS maximo
                    FROM " . BASE_DATOS . ".tab_genera_agenci";
            $consec = new Consulta($query, self::$cConexion);

            $ultimo = $consec->ret_matriz();
            $ultimo_consec = $ultimo[0][0];
            $nuevo_consec = $ultimo_consec + 1;

            $agencia->cod_agenci = $nuevo_consec;
            //INSERTA LA SEDE

            $query = "INSERT INTO " . BASE_DATOS . ".tab_genera_agenci
                      (cod_agenci,nom_agenci,cod_paisxx,cod_depart,cod_ciudad,dir_agenci,
                       tel_agenci,con_agenci,dir_emailx,num_faxxxx,usr_creaci,fec_creaci)
                      VALUES (
                      '$agencia->cod_agenci','$agencia->nom_agenci','$agencia->cod_paisxx','$agencia->cod_depart','$agencia->cod_ciudad','$agencia->dir_agenci',
                      '$agencia->tel_agenci','$agencia->con_agenci','$agencia->dir_emailx','$agencia->num_faxxxx','$agencia->usr_creaci','$agencia->fec_creaci')";

            $insercion = new Consulta($query, self::$cConexion, "R");

            $query = "INSERT INTO " . BASE_DATOS . ".tab_transp_agenci
                   (cod_transp,cod_agenci)
              VALUES ('$agencia->cod_tercer','$agencia->cod_agenci')
            ";

            $insercion = new Consulta($query, self::$cConexion, "R");
            #ingresa las modalidades
            foreach ($modali as $key => $value) {
                $query = "INSERT INTO " . BASE_DATOS . ".tab_emptra_modali
                        VALUES ('$agencia->cod_tercer',$value)";
                $insercion = new Consulta($query, self::$cConexion, "R");
            }


            if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
                $link_a = "<br><b><a href=\"index.php?&window=central&cod_servic=" . $_REQUEST[cod_servic] . " \"target=\"centralFrame\"><font style='color:#3F7506'>Insertar Otra Transportadora</font></a></b>";

                $mensaje = "<font color='#000000'>Se Inserto la Transportadora <b>$transp->abr_tercer</b> Exitosamente.<br></font>" . $link_a;
                $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
                $mens = new mensajes();
                echo $mens->correcto2("INSERTAR TRANSPORTADORA", $mensaje);
            } else {
                $div = "<div style='text-align:center'>
                    <span><h2><font style='color:red'><b>Ocurri&oacute; un error en el registro</b></font>, por favor revisa la informaci&oacute;n e intenta nuevamente.<br>
                    Si el problema persiste por favor informa a soporte.</h2>
                     </span><br><br>
                  <input type='button' name='cerrar' id='closeID' value='Finalizar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/>
              </div>";
                echo $div;
            }
        } else {

            $sql = "SELECT cod_tercer FROM " . BASE_DATOS . ".tab_tercer_activi WHERE cod_tercer = '$transp->cod_tercer' AND cod_activi = " . COD_FILTRO_EMPTRA;
            $res = new Consulta($sql, self::$cConexion);
            $res = $res->ret_matrix("a");
            if (!$res) {
                $query = "INSERT INTO " . BASE_DATOS . ".tab_tercer_activi
                VALUES ('$transp->cod_tercer'," . COD_FILTRO_EMPTRA . ")";
                $insercion = new Consulta($query, self::$cConexion, "R");
            }

            $sql = "SELECT cod_agenci FROM " . BASE_DATOS . ".tab_transp_agenci WHERE cod_transp =  '$agencia->cod_tercer'";
            $ag = new Consulta($sql, self::$cConexion);
            $ag = $ag->ret_matrix("a");
            if (!$ag) {

                //iso
                if (!$emptra->ind_ceriso) {
                    $emptra->ind_ceriso = 'N';
                }
                //basc
                if (!$emptra->ind_cerbasc) {
                    $emptra->ind_cerbasc = 'N';
                }
                //cobertura nacional
                if (!$emptra->ind_cobnal) {
                    $emptra->ind_cobnal = 'N';
                }

                //cobertura internacional
                if (!$emptra->ind_cobint) {
                    $emptra->ind_cobint = 'N';
                }

                //gran contribuyente
                if (!$emptra->ind_gracon) {
                    $emptra->ind_gracon = 'N';
                }
                $sql = "SELECT cod_tercer FROM " . BASE_DATOS . ".tab_tercer_emptra WHERE cod_tercer = '$emptra->cod_tercer' ";
                $em = new Consulta($sql, self::$cConexion);
                $em = $em->ret_matrix("a");

                if (!$em) {
                    $query = "INSERT INTO " . BASE_DATOS . ".tab_tercer_emptra
                                  (cod_tercer,cod_minins,num_resolu,fec_resolu,num_region,ran_iniman,
                                   ran_finman,ind_gracon,ind_ceriso,fec_ceriso,ind_cerbas,fec_cerbas,
                                   otr_certif,ind_cobnal,ind_cobint,nro_habnal,fec_resnal,nom_repleg,
                                   usr_creaci,fec_creaci)
                                  VALUES (
                                  '$emptra->cod_tercer','$emptra->cod_minins','$emptra->num_resolu','$emptra->fec_resolu','$emptra->num_region','$emptra->ran_iniman',
                                  '$emptra->ran_finman','$emptra->ind_gracon','$emptra->ind_ceriso','$emptra->fec_ceriso','$emptra->ind_cerbas','$emptra->fec_cerbas',
                                  '$emptra->otr_certif','$emptra->ind_cobnal','$emptra->ind_cobint','$emptra->nro_habnal','$emptra->fec_resolu','$emptra->nom_repleg',
                                  '$emptra->usr_creaci','$emptra->fec_creaci')";
                    $insercion = new Consulta($query, self::$cConexion, "R");
                }
                //consecutivo de la sede
                $query = "SELECT MAX( CAST(cod_agenci AS UNSIGNED) ) AS maximo
                    FROM " . BASE_DATOS . ".tab_genera_agenci";
                $consec = new Consulta($query, self::$cConexion);

                $ultimo = $consec->ret_matriz();
                $ultimo_consec = $ultimo[0][0];
                $nuevo_consec = $ultimo_consec + 1;

                $agencia->cod_agenci = $nuevo_consec;
                #conulta pais y dpto de la agencia por su ciudad
                $query = "SELECT cod_paisxx,cod_depart
                       FROM " . BASE_DATOS . ".tab_genera_ciudad
                      WHERE cod_ciudad = '$agencia->cod_ciudad'";
                $consulta = new Consulta($query, self::$cConexion);
                $ciused = $consulta->ret_matriz();
                $agencia->cod_paisxx = $ciused[0][0];
                $agencia->cod_depart = $ciused[0][1];
                $query = "INSERT INTO " . BASE_DATOS . ".tab_genera_agenci
                          (cod_agenci,nom_agenci,cod_paisxx,cod_depart,cod_ciudad,dir_agenci,
                           tel_agenci,con_agenci,dir_emailx,num_faxxxx,usr_creaci,fec_creaci)
                          VALUES (
                          '$agencia->cod_agenci','$agencia->nom_agenci','$agencia->cod_paisxx','$agencia->cod_depart','$agencia->cod_ciudad','$agencia->dir_agenci',
                          '$agencia->tel_agenci','$agencia->con_agenci','$agencia->dir_emailx','$agencia->num_faxxxx','$agencia->usr_creaci','$agencia->fec_creaci')";

                $insercion = new Consulta($query, self::$cConexion, "R");

                $query = "INSERT INTO " . BASE_DATOS . ".tab_transp_agenci
                   (cod_transp,cod_agenci)
              VALUES ('$agencia->cod_tercer','$agencia->cod_agenci')
            ";

                $insercion = new Consulta($query, self::$cConexion, "R");
            }
            if ($ag) {
                $nuevo_consec = $ag[0]['cod_agenci'];
            }

            $this->modificar("Registró", $nuevo_consec);
        }
    }

    /*     * ****************************************************************************
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
        $transp = (object) $_POST['transp']; //objeto para la tabla tab_tercer_tercer
        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
        $transp->usr_modifi = $usuario;
        $transp->fec_modifi = $fec_actual;


        #activa la transportadora
        $query = "UPDATE " . BASE_DATOS . ".tab_tercer_tercer 
                        SET cod_estado = 1,
                            usr_modifi = '$transp->usr_modifi',
                            fec_modifi = '$transp->fec_modifi'
                            WHERE cod_tercer = '$transp->cod_tercer' ";
        $insercion = new Consulta($query, self::$cConexion, "R");

        #consulta las agencias de la transportadora
        $query = "SELECT cod_agenci FROM " . BASE_DATOS . ".tab_transp_agenci WHERE cod_transp = '$transp->cod_tercer'";
        $consulta = new Consulta($query, self::$cConexion);
        $agencias = $consulta->ret_matrix("a");

        #activa las agencias de la transportadora
        foreach ($agencias as $key => $value) {
            $query = "UPDATE " . BASE_DATOS . ".tab_genera_agenci SET cod_estado = '1' WHERE cod_agenci ='" . $value['cod_agenci'] . "'";
            $insercion = new Consulta($query, self::$cConexion, "R");
        }

        if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
            $mensaje = "Se activó la Transportadora " . $transp->abr_tercer . " exitosamente.";
            $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            $mens->correcto2("ACTIVAR TRANSPORTADORA", $mensaje);
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
        $transp = (object) $_POST['transp']; //objeto para la tabla tab_tercer_tercer
        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];
        $transp->usr_modifi = $usuario;
        $transp->fec_modifi = $fec_actual;

        #inactva la transportadora
        $query = "UPDATE " . BASE_DATOS . ".tab_tercer_tercer 
                        SET cod_estado = 0,
                            usr_modifi = '$transp->usr_modifi',
                            fec_modifi = '$transp->fec_modifi'
                            WHERE cod_tercer = '$transp->cod_tercer' ";
        $insercion = new Consulta($query, self::$cConexion, "R");

        #consulta las agencias de la transportadora
        $query = "SELECT cod_agenci FROM " . BASE_DATOS . ".tab_transp_agenci WHERE cod_transp = '$transp->cod_tercer'";
        $consulta = new Consulta($query, self::$cConexion);
        $agencias = $consulta->ret_matrix("a");

        #inactiva las agencias de la transportadora
        foreach ($agencias as $key => $value) {
            $query = "UPDATE " . BASE_DATOS . ".tab_genera_agenci SET cod_estado = '0' WHERE cod_agenci ='" . $value['cod_agenci'] . "'";
            $insercion = new Consulta($query, self::$cConexion, "R");
        }


        if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
            $mensaje = "Se inactivó la Transportadora " . $transp->abr_tercer . " exitosamente.";
            $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='closed()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            $mens->correcto2("INACTIVAR TRANSPORTADORA", $mensaje);
        }
    }

    /*     * ****************************************************************************
     *  \fn: modificar                                                            *
     *  \brief: función para modificar una transportadoras                        *
     *  \author: Ing. Alexander Correa                                            *
     *  \date: 07/09/2015                                                         *
     *  \date modified:                                                           *
     *  \param:                                                                   *
     *  \param:                                                                   *
     *  \return popUp con el resultado de la operacion                            *
     * **************************************************************************** */

    private function modificar($operacion = null, $cod_agenci = null) {

        if (!$operacion) {
            $operacion = "Modificó";
        }

        # lleno los objetos nesesarios para la correcta modificación en la base de datos.
        $transp = (object) $_POST['transp']; //objeto para llenar la tabla tab_tercer_tercer
        $emptra = (object) $_POST['emptra']; //objeto para llenar la tabla tab_tercer_emptra
        $agencia = (object) $_POST['agencia']; //objeto para llenar la tabla tab_genera_agenci
        $modali = (object) $_POST['modali']; //para las modalidades de la transportadora
        if ($cod_agenci) {
            $agencia->cod_agenci = $cod_agenci;
        }
        $fec_actual = date("Y-m-d H:i:s");
        $usuario = $_SESSION['datos_usuario']['cod_usuari'];

        #completo los objetos con datos dependientes de otros objetos
        $transp->fec_modifi = $fec_actual;
        $transp->usr_modifi = $usuario;
        $emptra->cod_tercer = $transp->cod_tercer;
        $emptra->fec_modifi = $fec_actual;
        $emptra->usr_modifi = $usuario;
        $agencia->dir_emailx = $transp->dir_emailx;
        $agencia->num_faxxxx = $transp->num_faxxxx;
        $agencia->usr_modifi = $usuario;
        $agencia->cod_tercer = $transp->cod_tercer;

        /* echo '<pre>';
          print_r($agencia);
          die; */
        # consulta el departamento y pais de la transportadora basado en su ciudad
        $query = "SELECT cod_paisxx,cod_depart
                  FROM " . BASE_DATOS . ".tab_genera_ciudad
                 WHERE cod_ciudad = $transp->cod_ciudad ";
        $consulta = new Consulta($query, self::$cConexion);
        $ciudad = $consulta->ret_matriz("a");

        $transp->cod_paisxx = $ciudad[0][0];
        $transp->cod_depart = $ciudad[0][1];

        #modifica la base de datos
        $query = "UPDATE " . BASE_DATOS . ".tab_tercer_tercer
                   SET cod_terreg = '$transp->cod_terreg',
                       nom_tercer = '$transp->nom_tercer',
                       abr_tercer = '$transp->abr_tercer',
                       dir_domici = '$transp->dir_domici',
                       num_telef1 = '$transp->num_telef1',
                       cod_paisxx = '$transp->cod_paisxx',
                       cod_depart = '$transp->cod_depart',
                       cod_ciudad = '$transp->cod_ciudad',
                       usr_modifi = '$transp->usr_modifi',
                       fec_modifi = '$transp->fec_modifi',
                       dir_emailx = '$transp->dir_emailx'
                 WHERE cod_tercer = '$transp->cod_tercer'";

        $insercion = new Consulta($query, self::$cConexion, "BR");
        //iso
        if (!$emptra->ind_ceriso) {
            $emptra->ind_ceriso = 'N';
        }
        //basc
        if (!$emptra->ind_cerbasc) {
            $emptra->ind_cerbasc = 'N';
        }
        //cobertura nacional
        if (!$emptra->ind_cobnal) {
            $emptra->ind_cobnal = 'N';
        }
        //cobertura internacional
        if (!$emptra->ind_cobint) {
            $emptra->ind_cobint = 'N';
        }
        //gran contribuyente
        if (!$emptra->ind_gracon) {
            $emptra->ind_gracon = 'N';
        }
        $query = "UPDATE " . BASE_DATOS . ".tab_tercer_emptra
                    SET cod_minins = '$emptra->cod_minins',
                        num_resolu = '$emptra->num_resolu',
                        fec_resolu = '$emptra->fec_resolu',
                        num_region = '$emptra->num_region',
                        ran_iniman = '$emptra->ran_iniman',
                        ran_finman = '$emptra->ran_finman',
                        ind_gracon = '$emptra->ind_gracon',
                        ind_ceriso = '$emptra->ind_ceriso',
                        fec_ceriso = '$emptra->fec_ceriso',
                        ind_cerbas = '$emptra->ind_cerbas',
                        fec_cerbas = '$emptra->fec_cerbas',
                        otr_certif = '$emptra->otr_certif',
                        ind_cobnal = '$emptra->ind_cobnal',
                        ind_cobint = '$emptra->ind_cobint',
                        nro_habnal = '$emptra->nro_habnal',
                        fec_resnal = '$emptra->fec_resnal',
                        nom_repleg = '$emptra->nom_repleg',
                        usr_modifi = '$emptra->usr_modifi',
                        fec_modifi = '$emptra->fec_modifi'
                  WHERE cod_tercer = '$emptra->cod_tercer'";

        $insercion = new Consulta($query, self::$cConexion, "R");

        #conulta pais y dpto de la agencia por su ciudad
        $query = "SELECT cod_paisxx,cod_depart
                     FROM " . BASE_DATOS . ".tab_genera_ciudad
                    WHERE cod_ciudad = '$agencia->cod_ciudad'";
        $consulta = new Consulta($query, self::$cConexion);
        $ciused = $consulta->ret_matriz();
        $agencia->cod_paisxx = $ciused[0][0];
        $agencia->cod_depart = $ciused[0][1];

        $query = "UPDATE " . BASE_DATOS . ".tab_genera_agenci
                    SET nom_agenci = '$agencia->nom_agenci',
                        cod_paisxx = '$agencia->cod_paisxx',
                        cod_depart = '$agencia->cod_depart',
                        cod_ciudad = '$agencia->cod_ciudad',
                        dir_agenci = '$agencia->dir_agenci',
                        tel_agenci = '$agencia->tel_agenci',
                        dir_emailx = '$agencia->dir_emailx',
                        num_faxxxx = '$agencia->num_faxxxx',
                        usr_modifi = '$agencia->usr_modifi',
                        fec_modifi = '$agencia->fec_modifi'
                    WHERE cod_agenci = $agencia->cod_agenci";

        $insercion = new Consulta($query, self::$cConexion, "R");

        #elimina las modalidades antiguas para ingresar las nuevas
        $query = "DELETE FROM " . BASE_DATOS . ".tab_emptra_modali WHERE cod_emptra = '$agencia->cod_tercer'";
        $insercion = new Consulta($query, self::$cConexion, "R");

        #ingresa las modalidades
        foreach ($modali as $key => $value) {
            $query = "INSERT INTO " . BASE_DATOS . ".tab_emptra_modali VALUES ('$agencia->cod_tercer',$value)";
            $insercion = new Consulta($query, self::$cConexion, "R");
        }

        if ($consulta = new Consulta("COMMIT", self::$cConexion)) {
            $mensaje = "Se $operacion la Transportadora " . $transp->abr_tercer . " exitosamente.";
            $mensaje .= "<br><br><input type='button' name='cerrar' id='closeID' value='cerrar' onclick='confirmado()' class='crmButton small save ui-button ui-widget ui-state-default ui-corner-all'/><br><br>";
            $mens = new mensajes();
            $mens->correcto2("ACTUALIZAR TRANSPORTADORA", $mensaje);
        }
    }

    /* ! \fn: registrarSeguimAditional
     *  \brief: registra un seguimiento adicional a una transportadora en la base de datos 
     *  \author: Ing. Alexander Correa
     *  \date:  08/07/016
     *  \date modified: dia/mes/año
     *  \param:   
     *  \return 
     */

    private function registrarSeguimAditional() {
        $datos = (object) $_POST;
        $datos->fec_inicia .= " " . $datos->hor_inicia . ":00";
        $datos->fec_finali .= " " . $datos->hor_finali . ":00";
        $usr_creaci = $_SESSION['datos_usuario']['cod_usuari'];
        #print_r($datos->cod_consec);die;
        if ($datos->cod_consec > 0) {
            $sql = "UPDATE " . BASE_DATOS . ".tab_seguim_adicio 
                       SET fec_inicia = '$datos->fec_inicia',
                           fec_finali = '$datos->fec_finali',
                           usr_modifi = '$usr_creaci'
                     WHERE cod_consec = $datos->cod_consec";
            $consulta = new Consulta($sql, self::$cConexion);
            if ($consulta) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            $sql = "SELECT cod_consec FROM " . BASE_DATOS . ".tab_seguim_adicio 
                 WHERE cod_transp = $datos->cod_transp 
                   AND ('$datos->fec_inicia' BETWEEN fec_inicia AND fec_finali 
                    OR  '$datos->fec_finali' BETWEEN fec_inicia AND fec_finali)
                   AND ind_estado = 1";
            $consulta = new Consulta($sql, self::$cConexion);
            $registro = $consulta->ret_matriz("a");
            if (!$registro) {
                $sql = "INSERT INTO " . BASE_DATOS . ".tab_seguim_adicio 
                                (cod_transp, fec_inicia, fec_finali, usr_creaci, fec_creaci)
                                VALUES 
                                ($datos->cod_transp, '$datos->fec_inicia', '$datos->fec_finali', '$usr_creaci', NOW())";
                $consulta = new Consulta($sql, self::$cConexion);
                if ($consulta) {
                    echo 1;
                } else {
                    echo 0;
                }
            } else {
                echo -1;
            }
        }
    }

    /* ! \fn: getServicesAditionals
     *  \brief: muestra a lista de configuraciones adicionales por empresa
     *  \author: Ing. Alexander Correa
     *  \date: 08/07/2016
     *  \date modified: dia/mes/año
     *  \param:     
     *  \return 
     */

    private function getServicesAditionals() {

        $mHtml = new FormLib(2);
        $mHtml->OpenDiv("id:tabla; class:accordion");
        $mHtml->SetBody("<h1 style='padding:6px;'><B>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Seguimiento Adicional</B></h1>");
        $mHtml->OpenDiv("id:sec;");
        $mHtml->OpenDiv("id:form3; class:contentAccordionForm");
        $mSql = "SELECT a.cod_transp, b.abr_tercer, a.fec_inicia, a.fec_finali, a.cod_consec, a.ind_estado
                   FROM " . BASE_DATOS . ".tab_seguim_adicio a 
             INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer b ON b.cod_tercer = a.cod_transp 
                  WHERE ind_estado = 1
                    AND fec_finali > NOW()";
        $_SESSION["queryXLS"] = $mSql;

        if (!class_exists(DinamicList)) {
            include_once("../" . DIR_APLICA_CENTRAL . "/lib/general/dinamic_list.inc");
        }
        $list = new DinamicList(self::$cConexion, $mSql, "1", "no", 'ASC');
        $list->SetClose('no');
        $list->SetHeader(utf8_decode("Código de la transportadora"), "field:a.cod_transp; width:1%;  ");
        $list->SetHeader("Transportadora", "field:b.abr_tercer; width:1%");
        $list->SetHeader("Fecha Inicial", "field:a.fec_inicia; width:1%");
        $list->SetHeader("Fecha Final", "field:a.fec_finali; width:1%");
        $list->SetOption("Opciones", "field:a.ind_estado; width:1%; onclikDisable:inactivarConfig( this );onclikEdit:editarConfiguracion( this )");
        $list->SetHidden("cod_consec", "4");
        $list->SetHidden("cod_transp", "0");
        $list->SetHidden("abr_tercer", "1");
        $list->SetHidden("fec_inicia", "2");
        $list->SetHidden("fec_finali", "3");
        $list->Display(self::$cConexion);
        $_SESSION["DINAMIC_LIST"] = $list;
        $Html = $list->GetHtml();
        $mHtml->SetBody($Html);
        $mHtml->CloseDiv();
        $mHtml->CloseDiv();
        $mHtml->CloseDiv();

        echo $mHtml->MakeHtml();
    }

    /* ! \fn: inactivarConfig
     *  \brief: inactiva una configuracioón registrada de una empresa
     *  \author: Ing. Alexander Correa
     *  \date: 11/07/2016
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return 
     */

    private function inactivarConfig() {
        $datos = (object) $_POST;
         
        $sql = "UPDATE " . BASE_DATOS . ".tab_seguim_adicio 
                   SET ind_estado = 0
                 WHERE cod_consec = $datos->cod_consec";
        $consulta = new Consulta($sql, self::$cConexion);
        if ($consulta) {
            echo 1;
        } else {
            echo 0;
        }
    }

}

if ($_REQUEST[Ajax] === 'on') {
    $_INFORM = new trans();
}
?>
