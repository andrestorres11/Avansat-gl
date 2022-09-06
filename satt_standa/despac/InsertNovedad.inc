<?php

/* ! \file: InsertNovedad.inc
 *  \brief: Inserta las novedades de los despachos
 *  \author: 
 *  \version: 2.0
 *  \date: dd/mm/aaaa
 *  \date modified: 27/10/2015
 *  \modified by: Ing. Fabian Salinas
 *  \bug: 
 *  \warning: 
 */

ini_set("memory_limit", "1024M");

/* ! \class: InsertNovedad
 *  \brief: Guarda las novedades
 */

class InsertNovedad {

    var $cod_aplica = NULL,
            $servic = NULL,
            $opcion = NULL,
            $conexion = NULL,
            $opcion_base = NULL,
            $paginador = NULL,
            $GeneralFunctions = NULL,
            $cod_ciuori = NULL,
            $nom_ciuori = NULL,
            $cod_ciudes = NULL,
            $nom_ciudes = NULL,
            $nom_transp = NULL,
            $ind_status = NULL,
            $cod_transp = NULL;

    #cTipTrans  Array  Tipos de Transporte cod_tipdes = a la posicion del array
    private static $cTipTrans = array('DESCONOCIDO', 'FLOTA PROPIA', 'TERCEROS', 'EMPRESAS');

    #cLisTrans  Matriz  Lista de transportadoras para no quemarlas en el codigo, por cada comparación necesaria agregar una posicion con la descripción
    private static $cLisTrans = array('NoMailNovEsp' => array('900419270'),
        'Corona' => '860068121',
        'MailNotHtml' => array('830090031'),
    );

    private static $cTieAddSt = 15; #Tiempo adicional estandar para el recalculo de las alarmas

    private static $cCodNoved = array('0' => array('256'),
        '1' => array('255'),
        '2' => array('270', '271', '273', '274', '275', '276', '329', '296', '281', '279', '283', '269', '272'),
        '3' => array('242'),
        '4' => array('242', '262'),
        '5' => array('338'),
        '6' => array('326')
    );

    private static $cCodPerfi = array('0' => array('8', '713')
    );

    private static $cNow;

    function __construct($servic, $opcion, $aplica, $conexion = NULL, $opcion_base = NULL) {
        self::$cNow = date('Y-m-d H:i');
        $this->cod_aplica = $aplica;
        $this->servic = $servic;
        $this->opcion = $opcion;

        @include_once('../' . DIR_APLICA_CENTRAL . '/lib/general/functions.inc');

        if ($conexion != NULL)
            $this->conexion = $conexion;
        else {// VALIDACION PARA NUEVA OPCION SAT TRAFICO MOVIL v2.0
            include_once('../../' . DIR_APLICA_CENTRAL . '/lib/ajax.inc');
            $this->conexion = $AjaxConnection;
        }
        $this->opcion_base = $opcion_base;
    }

    /* ! \fn: GetNameProtoc
     *  \brief: 
     *  \author: 
     *  \date:  dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: cod_protoc
     *  \param: base_datos
     *  \return: 
     */

    function GetNameProtoc($cod_protoc, $base_datos) {
        $mSelect = "SELECT a.des_protoc 
                      FROM " . $base_datos . ".tab_genera_protoc a
                     WHERE a.cod_protoc = '" . $cod_protoc . "'";
        $consulta = new Consulta($mSelect, $this->conexion);
        $_PROTOC = $consulta->ret_matriz();

        return $_PROTOC[0][0];
    }

    /* ! \fn: getMailProtoc
     *  \brief: 
     *  \author: 
     *  \date:  dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: cod_protoc
     *  \param: num_despac
     *  \param: cod_noveda
     *  \param: base_datos
     *  \return: 
     */

    function getMailProtoc($cod_protoc, $num_despac, $cod_noveda, $base_datos) {

        #Cuando la novedad es no EFECTIVA debe buscar el protocolo de enviado , el anterior.
        $cod_noveda = $cod_noveda == '342' ? $_REQUEST["cod_noveda"] : $cod_noveda;


        #Obtiene los criterios ------------------------------------------------------------------
        $mSelect = "SELECT cod_criter, nom_criter 
                      FROM " . $base_datos . ".tab_config_modcom 
                  ORDER BY 1 ";
        $consulta = new Consulta($mSelect, $this->conexion);
        $aCriter = $consulta->ret_matriz();

        $mCriter = array();
        foreach ($aCriter as $row) {
            $mCriter[$row['nom_criter']] = $row['cod_criter'];
        }
        #-----------------------------------------------------------------------------------------
        # Datos del despacho (llaves) para buscar los correos que tengan esa configuracion -------
        $mSelect = "SELECT a.cod_ciuori, b.cod_mercan AS cod_produc, a.cod_ciudes,
                           a.cod_tipdes, b.cod_instal AS cod_zonaxx, c.cod_canalx,
                           b.tip_transp AS cod_tiptra, '" . $cod_noveda . "' AS cod_noveda, a.num_despac, 
                           -- a.nom_sitcar AS cod_deposi
                           -- d.des_sitcar AS cod_deposi
                           e.cod_deposi
                      FROM " . $base_datos . ".tab_despac_despac a 
                 LEFT JOIN " . $base_datos . ".tab_despac_sisext b 
                        ON a.num_despac = b.num_despac 
                 LEFT JOIN " . $base_datos . ".tab_despac_destin c 
                        ON a.num_despac = c.num_despac 
                 LEFT JOIN ". $base_datos .".tab_despac_corona d 
                        ON a.num_despac = d.num_dessat
                 LEFT JOIN ".$base_datos.".tab_genera_deposi e 
                        ON d.des_sitcar = e.nom_deposi
                     WHERE a.num_despac = '" . $num_despac . "' 
                     GROUP BY a.num_despac ";
        $consulta = new Consulta($mSelect, $this->conexion);
        $aDespac = $consulta->ret_matrix('a');

        # ------------------------------------------------------------------------------------------
        # Consecutivo canal ya que no viaja por el wsdl de astrans ---------------------------------
        $mSelect = "SELECT con_consec 
                      FROM " . $base_datos . ".tab_genera_canalx 
                     WHERE cod_canalx = '" . $aDespac[0]['cod_canalx'] . "' AND 
                           cod_produc = '" . $aDespac[0]['cod_produc'] . "' ";
        $consulta = new Consulta($mSelect, $this->conexion);
        $aCanalx = $consulta->ret_matrix('a');
        # -------------------------------------------------------------------------------------------

        $aDespac[0]['cod_canalx'] = $aCanalx[0]['con_consec'] != '' ? $aCanalx[0]['con_consec'] : '';

        $mDespac = array();
        foreach ($aDespac[0] as $nom_campox => $val_campox) {
            if ($val_campox != '')
                $mDespac[$nom_campox] = $val_campox;
        }

        $mCompar = array();
        foreach ($mCriter as $nom_criter => $val_criter) {
            if ($mDespac[$nom_criter] != '')
                $mCompar[$val_criter] = $mDespac[$nom_criter];
        }

        $mCiuori = $mCompar[1];
        $mMercan = $mCompar[2];
        $mCiudes = $mCompar[3];
        $mTipdes = $mCompar[4];
        $mZonaxx = $mCompar[5];
        $mCanalx = $mCompar[6];
        $mTiptra = $mCompar[7];
        $mDeposi = $mCompar[8];

        //OBTENER EL RESPONSABLE DEFINIDO SEGUN CRITERIOS DE BUSQUEDA
        $mSelect = "SELECT cod_usuari, cod_consec 
                      FROM " . $base_datos . ".tab_detail_modcom 
                     WHERE cod_noveda = '" . $cod_noveda . "'
                       AND ind_tipres = 'P' 
                       AND cod_criter = '4' 
                       AND val_criter = '" . $mTipdes . "' ";
        $consulta = new Consulta($mSelect, $this->conexion);
        $mRespons = $consulta->ret_matriz();

        $aDestin = array();
        foreach ($mRespons as $row) {
            $mSelect = "SELECT * 
                          FROM " . $base_datos . ".tab_detail_modcom 
                         WHERE cod_noveda = '" . $cod_noveda . "'
                           AND ind_tipres = 'P'
                           AND cod_usuari = '" . $row['cod_usuari'] . "'
                           AND cod_consec = '" . $row['cod_consec'] . "'";
            $consulta = new Consulta($mSelect, $this->conexion);
            $mDetalle = $consulta->ret_matriz();

            foreach ($mDetalle as $Detail)
                $aDestin[$Detail['cod_usuari']][$Detail['cod_criter']][] = $Detail['val_criter'];
        }

        if ($mTipdes != '4')
            $Order = array('7' => '7', '4' => '4', '2' => '2', '1' => '1', '5' => '5', '6' => '6', '8' => '8', '3' => '3');
        else
            $Order = array('7' => '7', '4' => '4', '2' => '2', '3' => '3', '1' => '1', '5' => '5', '6' => '6', '8' => '8');


        $mPrincipal = '';

        foreach ($Order as $Orden) {
            $rDestin = array();
            foreach ($aDestin as $Posible => $Responsable) {
                if (in_array($mCompar[$Orden], $Responsable[$Orden])) {
                    $rDestin[$Posible] = $Responsable;
                }
            }
            $aDestin = $rDestin;

            if (sizeof($aDestin) == 1) {
                foreach ($aDestin as $Main => $Por) {
                    $mPrincipal = $Main;
                }
            }
        }

        if ($mPrincipal == '')
            $mPrincipal = 'povedad.a';

        $mSelect = "SELECT usr_emailx
                      FROM " . $base_datos . ".tab_genera_usuari 
                     WHERE cod_usuari = '" . $mPrincipal . "' ";

        $consulta = new Consulta($mSelect, $this->conexion);
        $mUsuari = $consulta->ret_matriz();
        $mPrincipal = $mUsuari[0]['usr_emailx'];

        //OBTENER LAS COPIAS DEFINIDAS SEGUN CRITERIOS DE BUSQUEDA
        $mSelect = "SELECT cod_usuari, cod_consec 
                      FROM " . $base_datos . ".tab_detail_modcom 
                     WHERE cod_noveda = '" . $cod_noveda . "'
                       AND ind_tipres = 'S' 
                       AND cod_criter = '4' 
                       AND val_criter = '" . $mTipdes . "' ";
        $consulta = new Consulta($mSelect, $this->conexion);
        $mRespons = $consulta->ret_matriz();

        $aDestin = array();
        foreach ($mRespons as $row) {
            $mSelect = "SELECT * 
                          FROM " . $base_datos . ".tab_detail_modcom 
                         WHERE cod_noveda = '" . $cod_noveda . "'
                           AND ind_tipres = 'S'
                           AND cod_usuari = '" . $row['cod_usuari'] . "'
                           AND cod_consec = '" . $row['cod_consec'] . "'";
            $consulta = new Consulta($mSelect, $this->conexion);
            $mDetalle = $consulta->ret_matriz();

            foreach ($mDetalle as $Detail)
                $aDestin[$Detail['cod_usuari']][$Detail['cod_criter']][] = $Detail['val_criter'];
        }

        if ($mTipdes != '4')
            $Order = array('7' => '7', '4' => '4', '2' => '2', '1' => '1', '5' => '5', '6' => '6', '8' => '8', '3' => '3');
        else
            $Order = array('7' => '7', '4' => '4', '2' => '2', '3' => '3', '1' => '1', '5' => '5', '6' => '6', '8' => '8');

        $mSecundario = '';

        foreach ($Order as $Orden) {
            $rDestin = array();

            foreach ($aDestin as $Posible => $Responsable) {
                if (in_array($mCompar[$Orden], $Responsable[$Orden]))
                    $rDestin[$Posible] = $Responsable;
            }

            if (sizeof($rDestin) <= 0) {
                $final = $aDestin;
                break;
            }

            $aDestin = $rDestin;
        }

        foreach ($final as $Main => $Por) {
            $mSelect = "SELECT usr_emailx
                          FROM " . $base_datos . ".tab_genera_usuari 
                         WHERE cod_usuari = '" . $Main . "' ";
            $consulta = new Consulta($mSelect, $this->conexion);
            $mUsuari = $consulta->ret_matriz();

            $mSecundario .= $mSecundario != '' ? ', ' . $mUsuari[0]['usr_emailx'] : $mUsuari[0]['usr_emailx'];
        }


        /*         * *********************************************************************** */
        #Nelson Nuevo Matriz comunicacion ----------------------------------------------------
        $mMailTo = $this->getDataListCriter($aDespac[0], 'P', $base_datos);
        $mMailCC = $this->getDataListCriter($aDespac[0], 'S', $base_datos);
        # ------------------------------------------------------------------------------------
        # Valida que exista un correo o si no coloca opoveda por default --------------------- 
        $mSelect = "SELECT usr_emailx FROM " . $base_datos . ".tab_genera_usuari WHERE cod_usuari = 'povedad.a' ";
        $consulta = new Consulta($mSelect, $this->conexion);
        $mUsuari = $consulta->ret_matriz();


        $mMailTo = $mMailTo[0]["usr_emailx"] == '' ? $mUsuari[0]['usr_emailx'] : $mMailTo[0]["usr_emailx"];
        $mMailCC = $mMailCC[0]["usr_emailx"];
        #-------------------------------------------------------------------------------------
        /*if($_SESSION['datos_usuario']['cod_usuari'] == 'Adriana.poveda'){
            mail('nelson.liberato@eltransporte.org', 'PROTOCOLO', var_export($mPrincipal, true)."\n".var_export($mSecundario, true)."\n".var_export($mMailTo, true)."\n".var_export($mMailCC, true) );
        }*/

        $RESULTADO = array('ema_conpri' => $mPrincipal, 'ema_otrcon' => $mSecundario,
            'ema_conprX' => $mMailTo, 'ema_otrcoX' => $mMailCC);

        return $RESULTADO;
    }

    /* ! \fn: getMailImpact
     *  \brief: 
     *  \author: 
     *  \date:  dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: cod_transp  Integer  Codigo de la transportadora
     *  \param: num_despac  Integer  Numero del despacho
     *  \param: base_datos  String   Base de datos
     *  \return: 
     */

    function getMailImpact($cod_transp, $num_despac, $base_datos) {
        $mSelect = "SELECT a.cod_tipdes, a.cod_ciuori, a.cod_ciudes, b.cod_mercan 
                      FROM " . $base_datos . ".tab_despac_despac a 
                 LEFT JOIN " . $base_datos . ".tab_despac_sisext b 
                        ON a.num_despac = b.num_despac
                     WHERE a.num_despac = '" . $num_despac . "'";
        $consulta = new Consulta($mSelect, $this->conexion);
        $_data_despac = $consulta->ret_matriz();
        $DESPAC = $_data_despac[0];

        /* VALIDACION POR CAMPOS ******************************** */
        /* PRIMER NIVEL: TIPO DE DESPACHO *********************** */
        $mSelect = "SELECT b.ema_conpri, b.ema_otrcon
                      FROM " . $base_datos . ".tab_impact_matcom b
                     WHERE b.cod_transp = '" . $cod_transp . "'
                       AND b.cod_tipdes = '" . $DESPAC['cod_tipdes'] . "' ";
        $consulta = new Consulta($mSelect, $this->conexion);
        $to_return = $consulta->ret_matriz();

        if (sizeof($to_return) > 1) {/* SEGUNDO NIVEL: CIUDAD DE ORIGEN ********************** */
            $mSelect = "SELECT b.ema_conpri, b.ema_otrcon
                          FROM " . $base_datos . ".tab_impact_matcom b
                         WHERE b.cod_transp = '" . $cod_transp . "'
                           AND b.cod_tipdes = '" . $DESPAC['cod_tipdes'] . "'
                           AND b.cod_ciuori = '" . $DESPAC['cod_ciuori'] . "' ";
            $consulta = new Consulta($mSelect, $this->conexion);
            $to_return = $consulta->ret_matriz();

            if (sizeof($to_return) > 1) {/* TERCER NIVEL: CODIGO DEL PRODUCTO ********************** */
                $mSelect = "SELECT b.ema_conpri, b.ema_otrcon
                              FROM " . $base_datos . ".tab_impact_matcom b
                             WHERE b.cod_transp = '" . $cod_transp . "'
                               AND b.cod_tipdes = '" . $DESPAC['cod_tipdes'] . "'
                               AND b.cod_ciuori = '" . $DESPAC['cod_ciuori'] . "'
                               AND b.cod_produc = '" . $DESPAC['cod_mercan'] . "' ";
                $consulta = new Consulta($mSelect, $this->conexion);
                $to_return = $consulta->ret_matriz();

                if (sizeof($to_return) > 1) {/* CUARTO NIVEL: CIUDAD DE DESTINO ********************** */
                    $mSelect = "SELECT b.ema_conpri, b.ema_otrcon
                                  FROM " . $base_datos . ".tab_impact_matcom b
                                 WHERE b.cod_transp = '" . $cod_transp . "'
                                   AND b.cod_tipdes = '" . $DESPAC['cod_tipdes'] . "'
                                   AND b.cod_ciuori = '" . $DESPAC['cod_ciuori'] . "'
                                   AND b.cod_ciudes = '" . $DESPAC['cod_ciudes'] . "'
                                   AND b.cod_produc = '" . $DESPAC['cod_mercan'] . "'";
                    $consulta = new Consulta($mSelect, $this->conexion);
                    $to_return = $consulta->ret_matriz();
                }
            }
        }
        return $to_return[0];
    }

    /* ! \fn: getLastsPCRuta
     *  \brief: retorna los dos ultimos puestos de control de la ruta 
     *  \author: 
     *  \date:  dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mNumDespac  Integer  Numero del despacho
     *  \param: mBaseDatos  String   Base de datos
     *  \return: 
     */

    function getLastsPCRuta($mNumDespac, $mBaseDatos) {
        $mQuery = "SELECT a.cod_contro 
                     FROM " . $mBaseDatos . ".tab_despac_seguim a 
                    WHERE a.num_despac = '" . $mNumDespac . "'
                 ORDER BY a.fec_planea DESC LIMIT 2 ";
        $consulta = new Consulta($mQuery, $this->conexion);
        $mCodPC = $consulta->ret_matrix("i");
        return $mCodPC[1][0];
    }

    /* ! \fn: getDataListCriter
     *  \brief: 
     *  \author: 
     *  \date:  dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mData       Array    Data
     *  \param: mTipCorreo
     *  \param: base_datos  String   Base de datos
     *  \param: mPrint
     *  \return: 
     */

    function getDataListCriter($mData = NULL, $mTipCorreo = 'P', $base_datos, $mPrint = false) {
        $mListCr = 'SELECT y.usr_emailx AS usr_emailx, 
                           "' . $mData["num_despac"] . '" AS num_despac,
                           z.cod_usuari 
                     FROM (
                                 SELECT aa.cod_usuari , 
                                        aa.val_criter AS ValCriter1, aa.nom_criter AS NomCriter1, 
                                        bb.val_criter AS ValCriter2, bb.nom_criter AS NomCriter2, 
                                        cc.val_criter AS ValCriter3, cc.nom_criter AS NomCriter3,
                                        dd.val_criter AS ValCriter4, dd.nom_criter AS NomCriter4,
                                        ee.val_criter AS ValCriter5, ee.nom_criter AS NomCriter5,
                                        ff.val_criter AS ValCriter6, ff.nom_criter AS NomCriter6,
                                        gg.val_criter AS ValCriter7, gg.nom_criter AS NomCriter7,
                                        hh.val_criter AS ValCriter8, hh.nom_criter AS NomCriter8
                                   FROM (
                                            SELECT a.cod_usuari, a.cod_consec 
                                              FROM ' . $base_datos . '.tab_genera_modcom a 
                                             WHERE 1 = 1 GROUP BY a.cod_usuari
                                        ) xx
                             INNER JOIN (
                                            SELECT a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, b.nom_ciudad AS nom_criter
                                              FROM ' . $base_datos . '.tab_detail_modcom a ,
                                                   ' . $base_datos . '.tab_genera_ciudad b
                                             WHERE a.cod_criter = "1" AND  
                                                   a.val_criter = b.cod_ciudad AND 
                                                   a.cod_noveda = "' . $mData["cod_noveda"] . '" AND 
                                                   a.ind_tipres = "' . $mTipCorreo . '"   
                                        ) aa
                                     ON xx.cod_usuari = aa.cod_usuari ' . ($mData["cod_ciuori"] != '' ? ' AND  aa.val_criter = "' . $mData["cod_ciuori"] . '" /*Origen*/ ' : '') . '
                              LEFT JOIN (
                                            SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bc.nom_produc AS nom_criter
                                            FROM ' . $base_datos . '.tab_detail_modcom a, 
                                            ' . $base_datos . '.tab_genera_produc bc 
                                            WHERE a.cod_criter = "2" AND       
                                            a.val_criter = bc.cod_produc AND                                                                    
                                            a.cod_noveda = "' . $mData["cod_noveda"] . '" AND 
                                            a.ind_tipres = "' . $mTipCorreo . '"  
                                        ) bb
                                     ON xx.cod_usuari = bb.cod_usuari ' . ($mData["cod_produc"] != '' ? ' AND  bb.val_criter = "' . $mData["cod_produc"] . '" /*PRODUC*/ ' : '') . '
                              LEFT JOIN (
                                            SELECT a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bd.nom_ciudad AS nom_criter
                                              FROM ' . $base_datos . '.tab_detail_modcom a,
                                                   ' . $base_datos . '.tab_genera_ciudad bd
                                             WHERE a.cod_criter = "3" AND     
                                                   a.val_criter = bd.cod_ciudad AND 
                                                   a.cod_noveda = "' . $mData["cod_noveda"] . '" AND 
                                                   a.ind_tipres = "' . $mTipCorreo . '"  
                                        ) cc
                                     ON xx.cod_usuari = cc.cod_usuari ' . ( $mData["cod_ciudes"] != '' ? ' AND  cc.val_criter = "' . $mData["cod_ciudes"] . '" /*DESTIN*/ ' : '' ) . '
                              LEFT JOIN (
                                            SELECT a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, be.nom_tipdes AS nom_criter
                                              FROM ' . $base_datos . '.tab_detail_modcom a,
                                                   ' . $base_datos . '.tab_genera_tipdes be
                                             WHERE a.cod_criter = "4" AND  
                                                   a.val_criter = be.cod_tipdes AND 
                                                   a.cod_noveda = "' . $mData["cod_noveda"] . '" AND 
                                                   a.ind_tipres = "' . $mTipCorreo . '" 
                                        ) dd
                                     ON xx.cod_usuari = dd.cod_usuari ' . ($mData["cod_tipdes"] != '' ? ' AND  dd.val_criter = "' . $mData["cod_tipdes"] . '" /*TIPDES*/ ' : '') . '
                              LEFT JOIN (
                                            SELECT a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bf.nom_canalx AS nom_criter
                                              FROM ' . $base_datos . '.tab_detail_modcom a,
                                                   ' . $base_datos . '.tab_genera_canalx bf 
                                             WHERE a.cod_criter = "6" AND
                                                   a.val_criter = bf.con_consec AND 
                                                   a.cod_noveda = "' . $mData["cod_noveda"] . '" AND 
                                                   a.ind_tipres = "' . $mTipCorreo . '"  
                                        ) ff
                                    ON xx.cod_usuari = ff.cod_usuari ' . ( $mData["cod_canalx"] != '' ? ' AND  ff.val_criter = "' . $mData["cod_canalx"] . '" /*Zona*/ ' : '' ) . '
                              LEFT JOIN (
                                            SELECT a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bg.nom_zonaxx AS nom_criter
                                              FROM ' . $base_datos . '.tab_detail_modcom a,
                                                   ' . $base_datos . '.tab_genera_zonasx bg 
                                             WHERE a.cod_criter = "5" AND 
                                                   a.val_criter = bg.cod_zonaxx AND 
                                                   a.cod_noveda = "' . $mData["cod_noveda"] . '" AND 
                                                   a.ind_tipres = "' . $mTipCorreo . '"  
                                        ) ee
                                     ON xx.cod_usuari = ee.cod_usuari ' . ( $mData["cod_zonaxx"] != '' ? ' AND  ee.val_criter = "' . $mData["cod_zonaxx"] . '" /*Canal*/ ' : '' ) . '
                              LEFT JOIN (
                                            SELECT a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bh.nom_tiptra AS nom_criter
                                              FROM ' . $base_datos . '.tab_detail_modcom a,
                                                   ' . $base_datos . '.tab_genera_tiptra bh
                                             WHERE a.cod_criter = "7" AND  
                                                   a.val_criter = bh.cod_tiptra AND 
                                                   a.cod_noveda = "' . $mData["cod_noveda"] . '" AND 
                                                   a.ind_tipres = "' . $mTipCorreo . '" 
                                        ) gg
                                     ON xx.cod_usuari = gg.cod_usuari ' . ( $mData["cod_tiptra"] != '' ? ' AND  gg.val_criter = "' . $mData["cod_tiptra"] . '" /*Tiptra*/ ' : '' ) . '
                              LEFT JOIN (
                                            SELECT a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bi.nom_deposi AS nom_criter
                                              FROM ' . $base_datos . '.tab_detail_modcom a,
                                                   ' . $base_datos . '.tab_genera_deposi bi 
                                             WHERE a.cod_criter = "8" AND  
                                                   a.val_criter = bi.cod_deposi AND 
                                                   a.cod_noveda = "' . $mData["cod_noveda"] . '" AND 
                                                   a.ind_tipres = "' . $mTipCorreo . '"  
                                        ) hh
                                     ON xx.cod_usuari = hh.cod_usuari ' . ( $mData["cod_deposi"] != '' ? ' AND  hh.val_criter = "' . $mData["cod_deposi"] . '" /*DEPOSI*/ ' : '' ) . ' 
                               GROUP BY aa.cod_usuari 
                          ) z, 
                          ' . $base_datos . '.tab_genera_usuari y 
                    WHERE z.cod_usuari = y.cod_usuari
                      AND y.ind_estado = 1 ';


        if ($mData["cod_tipdes"] != '4')
            $Order = array('7' => '7', '4' => '4', '2' => '2', '1' => '1', '5' => '5', '6' => '6', '8' => '8', '3' => '3');
        else
            $Order = array('7' => '7', '4' => '4', '2' => '2', '3' => '3', '1' => '1', '5' => '5', '6' => '6', '8' => '8');

        /*
        foreach ($Order AS $key => $value) {
            switch ($key) {
                case '7':
                    $mListCr .= " \n AND z.ValCriter7 = '" . $mData["cod_tiptra"] . "' ";
                    //$consulta = new Consulta($mListCr, $this->conexion);
                    //$_Data[$key] = $consulta->ret_matrix('a');
                    break;
                case '4':
                    $mListCr .= " \n AND z.ValCriter7 = '" . $mData["cod_tiptra"] . "' ";
                    $mListCr .= " \n AND z.ValCriter4 = '" . $mData["cod_tipdes"] . "' ";
                    //$consulta = new Consulta($mListCr, $this->conexion);
                    //$_Data[$key] = $consulta->ret_matrix('a');
                    break;
                case '2':
                    $mListCr .= " \n AND z.ValCriter7 = '" . $mData["cod_tiptra"] . "' ";
                    $mListCr .= " \n AND z.ValCriter4 = '" . $mData["cod_tipdes"] . "' ";
                    $mListCr .= " \n AND z.ValCriter2 = '" . $mData["cod_produc"] . "' ";
                    //$consulta = new Consulta($mListCr, $this->conexion);
                    //$_Data[$key] = $consulta->ret_matrix('a');
                    break;
                case '1':
                    $mListCr .= " \n AND z.ValCriter7 = '" . $mData["cod_tiptra"] . "' ";
                    $mListCr .= " \n AND z.ValCriter4 = '" . $mData["cod_tipdes"] . "' ";
                    $mListCr .= " \n AND z.ValCriter2 = '" . $mData["cod_produc"] . "' ";
                    $mListCr .= " \n AND z.ValCriter1 = '" . $mData["cod_ciuori"] . "' ";
                    //$consulta = new Consulta($mListCr, $this->conexion);
                    //$_Data[$key] = $consulta->ret_matrix('a');
                    break;                   
                case '8':
                    $mListCr .= " \n AND z.ValCriter7 = '" . $mData["cod_tiptra"] . "' ";
                    $mListCr .= " \n AND z.ValCriter4 = '" . $mData["cod_tipdes"] . "' ";
                    $mListCr .= " \n AND z.ValCriter2 = '" . $mData["cod_produc"] . "' ";
                    $mListCr .= " \n AND z.ValCriter1 = '" . $mData["cod_ciuori"] . "' ";
                    $mListCr .= $mData["cod_deposi"] != '' && count($mData["cod_deposi"]) >= 2 ? " \n AND z.ValCriter8 = '" . $mData["cod_deposi"] . "' ": "";
                    //$consulta = new Consulta($mListCr, $this->conexion);
                    //$_Data[$key] = $consulta->ret_matrix('a');
                    break;                
                 
            }
        }
        */


        // nueva forma de filtrar saltando las prioridades de los filtros $Order ----------------------------
        $mListCr .= $mData["cod_tiptra"] != '' && count($mData["cod_tiptra"]) >= 1 ? " \n AND z.ValCriter7 = '" . $mData["cod_tiptra"] . "' " : "";
        $mListCr .= $mData["cod_tipdes"] != '' && count($mData["cod_tipdes"]) >= 1 ? " \n AND z.ValCriter4 = '" . $mData["cod_tipdes"] . "' " : "";
        $mListCr .= $mData["cod_produc"] != '' && count($mData["cod_produc"]) >= 1 ? " \n AND z.ValCriter2 = '" . $mData["cod_produc"] . "' " : "";
        $mListCr .= $mData["cod_ciuori"] != '' && count($mData["cod_ciuori"]) >= 2 ? " \n AND z.ValCriter1 = '" . $mData["cod_ciuori"] . "' " : "";
        $mListCr .= $mData["cod_deposi"] != '' && count($mData["cod_deposi"]) >= 2 ? " \n AND z.ValCriter8 = '" . $mData["cod_deposi"] . "' " : "";

        $mListCr .= " ORDER BY 1 ";

        $mQueryCanal = $mListCr . " \n AND z.ValCriter6 = '" . $mData["cod_canalx"] . "' ";

        if ($mPrint == true) {
            echo "<pre>Query:<br>";
            print_r($mListCr);
            echo "</pre>";
        }

        $consulta = new Consulta($mListCr, $this->conexion);
        $_Data = $consulta->ret_matrix('a');

        if ($mPrint == true) {
            echo "<pre>Original:<br>";
            print_r($_Data);
            echo "</pre>";
        }


        # consulta donde se reemplaza el usuario en la tabla                 
        for ($i = 0; $i < sizeof($_Data); $i++) {
            $mSelCor = "SELECT a.cod_reempl, a.cod_usuari, b.usr_emailx
                          FROM tab_restri_modcom a,
                               tab_genera_usuari b                             
                         WHERE a.cod_usuari = '" . $_Data[$i]['cod_usuari'] . "'
                           AND NOW() BETWEEN a.fec_inicia AND a.fec_finali
                           AND a.cod_reempl = b.cod_usuari";
            $consulta = new Consulta($mSelCor, $this->conexion);
            $aRemplaCorre = $consulta->ret_matrix('a');

            if ($_Data[$i]["cod_usuari"] == $aRemplaCorre[0]["cod_usuari"]) {
                $_Data[$i]["cod_usuari"] = $aRemplaCorre[0]["cod_reempl"];
                $_Data[$i]["usr_emailx"] = $aRemplaCorre[0]["usr_emailx"];
            }
        }

        # Crea lista de array de los correos y usuarios        
        for ($i = 0; $i < sizeof($_Data); $i++) {
            $mCor[] = $_Data[$i]["usr_emailx"];
            $mUsr[] = $_Data[$i]["cod_usuari"];
        }

        # Crea string separados por coma, correos y usuarios
        $mArrTotCor = join(',', $mCor);
        $mArrTotUsr = join(',', $mUsr);

        # Reasigna el array que retorna con los tres campos
        $_Data = null;
        $_Data[0]["usr_emailx"] = $mArrTotCor;
        $_Data[0]["num_despac"] = $mData["num_despac"];
        $_Data[0]["cod_usuari"] = $mArrTotUsr;

        if ($mPrint == true) {
            echo "<pre>Nuevo:<br>";
            print_r($_Data);
            echo "</pre>";
        }

        return $_Data;
    }

    /* ! \fn: InsertPuestoControlHumadea
     *  \brief: Inset Log para el consumo del webservice
     *  \author: 
     *  \date:  dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mParametros
     *  \param: mLog
     *  \return: 
     */

    function InsertPuestoControlHumadea($mParametros, $mLog, $mXML = NULL) {
        $mQueryInsertLog = "INSERT INTO " . $base_datos . ".tab_errorx_puecon 
                                    (
                                        cod_consec, empresa,  usuario, clave, 
                                        sentido, direccion, tipo_evento, kilometraje, 
                                        velocidad, codigo_puestocontrol,  novedad, longitud,
                                        latitud, hora,  fecha, placa, 
                                        manifiesto_codigo, num_despac, ind_estado, resultado, 
                                        fec_creaci, usr_creaci, xml_reques
                                    ) 
                                    VALUES
                                    (
                                        '', '$mParametros[empresa_codigocs]', '$mParametros[usuario]', '$mParametros[clave]', 
                                        '$mParametros[sentido]', '$mParametros[direccion]', '$mParametros[tipo_evento]', '$mParametros[kilometraje]', 
                                        '$mParametros[velocidad]', '$mParametros[codigo_puestocontrol]', '$mParametros[novedad]', '$mParametros[longitud]', 
                                        '$mParametros[latitud]', '$mParametros[hora]', '$mParametros[fecha]', '$mParametros[placa]', 
                                        '$mParametros[manifiesto_codigo]', '$mLog[mNumDes]', '$mLog[mEstado]', '" . str_replace("'", "", $mLog['mResult']) . "', 
                                        NOW() ,'$mLog[mUsuari]', '".htmlspecialchars( $mXML )."'
                                    ) ";
        $consulta = new Consulta($mQueryInsertLog, $this->conexion, 'R');
        return $consulta;
    }

    /* ! \fn: InsertNovedadHumadea
     *  \brief: Inset Log para el consumo del webservice
     *  \author: 
     *  \date:  dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mParametros
     *  \param: mLog
     *  \return: 
     */

    function InsertNovedadHumadea($mParametros, $mLog) {
        $mQueryInsertLog = "INSERT INTO " . $base_datos . ".tab_errorx_novsil
                                    (
                                        cod_consec, empresa, usuario, clave, 
                                        novedad, hora, fecha, placa, 
                                        manifiesto_codigo, num_despac, nom_sitiox, ind_estado,
                                        resultado, fec_creaci, usr_creaci
                                    ) 
                                    VALUES
                                    (
                                        '', '$mParametros[empresa]', '$mParametros[usuario]', '$mParametros[clave]', 
                                        '$mParametros[novedad]', '$mParametros[hora]', '$mParametros[fecha]', '$mParametros[placa]', 
                                        '$mParametros[manifiesto_codigo]', '$mLog[mNumDes]', '$mLog[mLugarr]', '$mLog[mEstado]', 
                                        '" . str_replace("'", "", $mLog['mResult']) . "', NOW(), '$mLog[mUsuari]'
                                    ) ";
        $consulta = new Consulta($mQueryInsertLog, $this->conexion, 'R');
        return $consulta;
    }

    /* ! \fn: getEmailAgecni
     *  \brief: 
     *  \author: 
     *  \date: dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: num_despac  Integer  Numero del despacho
     *  \return: 
     */

    function getEmailAgecni($num_despac) {
        $mQuery = 
       "  SELECT 
                    GROUP_CONCAT(CONCAT(c.dir_dispos,'@',b.dns_operad))   
            FROM    " . BASE_DATOS . ".tab_despac_despac a   
      INNER JOIN    " . BASE_DATOS . ".tab_despac_vehige e
              ON    a.num_despac = e.num_despac AND
                    a.num_despac = '" . $num_despac . "'
      INNER JOIN    " . BD_STANDA . ".tab_mensaj_operad b 
              ON    e.cod_transp = b.cod_transp AND 
                    b.ind_emailx = 1 AND 
                    b.nom_bdsata = '".BASE_DATOS."'
      INNER JOIN    " . BD_STANDA . ".tab_mensaj_dispos c
              ON    b.cod_operad = c.cod_operad 
           WHERE    c.ind_novala = 1 AND 
                    c.ind_estado = 1 ";
                    
        /*$mQuery = "SELECT b.dir_emailx 
                     FROM " . BASE_DATOS . ".tab_despac_despac a,
                          " . BASE_DATOS . ".tab_genera_agenci b
                     WHERE a.cod_agedes = b.cod_agenci AND
                           a.num_despac = '" . $num_despac . "' ";*/
        $consulta = new Consulta($mQuery, $this->conexion);
        $mEmailAgenci = $consulta->ret_matriz();

        return $mEmailAgenci[0][0];
    }

    /* ! \fn: setNovedadError
     *  \brief: 
     *  \author: 
     *  \date: dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: parametros
     *  \param: regist       Array   Data a registrar
     *  \param: novedaError
     *  \param: metodo
     *  \return: 
     */

    function setNovedadError($parametros, $regist, $novedaError, $metodo) {
        $novedaError['det_respon'] = str_replace("'", "", $novedaError['det_respon']);
        $novedaError['msg_respon'] = str_replace("'", "", $novedaError['msg_respon']);
        $query = "INSERT INTO " . BASE_DATOS . ".tab_errorx_noveda 
                        (
                            cod_transp, num_despac,  cod_rutasx,
                            cod_contro, nom_metodo,  cod_respon,
                            msg_respon, det_respon,  nom_usuari,
                            pwd_clavex, nom_aplica,  num_manifi,
                            num_placax, cod_novbas,  cod_conbas,
                            tim_duraci, fec_noveda,  des_noveda,
                            nom_contro, nom_sitiox,  cod_confar,
                            cod_novfar, nom_noveda,  ind_alarma,
                            ind_tiempo, nov_especi_, ind_manala,
                            usr_creaci, fec_creaci,  nom_server
                        )
                        VALUES
                        ( 
                            '" . $regist['nittra'] . "', '" . $regist['despac'] . "', '" . $regist['rutax'] . "',
                            '" . $regist["contro"] . "', '" . $metodo . "', '" . $novedaError['cod_respon'] . "',
                            '" . $novedaError['msg_respon'] . "', '" . addslashes($novedaError['det_respon']) . "', '" . $parametros['nom_usuari'] . "',
                            '" . $parametros['pwd_clavex'] . "', '" . $parametros['nom_aplica'] . "', '" . $parametros['num_manifi'] . "',
                            '" . $parametros['num_placax'] . "', '" . $parametros['cod_novbas'] . "', '" . $parametros['cod_conbas'] . "',
                            '" . $parametros['tim_duraci'] . "', '" . $parametros['fec_noveda'] . "', '" . addslashes($parametros['des_noveda']) . "',
                            '" . $parametros['nom_contro'] . "', '" . $parametros['nom_sitiox'] . "', '" . $parametros['cod_confar'] . "',
                            '" . $parametros['cod_novfar'] . "', '" . $parametros['nom_noveda'] . "', '" . $parametros['ind_alarma'] . "',
                            '" . $parametros['ind_tiempo'] . "', '" . $parametros['nov_especi_'] . "', '" . $parametros['ind_manala'] . "',
                            '" . $_SESSION['datos_usuario']['cod_usuari'] . "', NOW(), '" . $regist["server"] . "' 
                        )";
        $insercion = new Consulta($query, $this->conexion, "R");
    }

    /* ! \fn: getCiudad
     *  \brief: Trae el nombre de una ciudad
     *  \author: 
     *  \date: dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: cod_ciudad  Integer  Codigo de la ciudad
     *  \param: base_datos  String   Base de datos
     *  \param: ind_namexx
     *  \return: 
     */

    function getCiudad($cod_ciudad, $base_datos, $ind_namexx = FALSE) {
        $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3),' - ',a.cod_ciudad), UPPER( a.abr_ciudad )
                    FROM " . BASE_DATOS . ".tab_genera_ciudad a,
                         " . BASE_DATOS . ".tab_genera_depart b,
                         " . BASE_DATOS . ".tab_genera_paises c
                   WHERE a.cod_depart = b.cod_depart AND
                         a.cod_paisxx = b.cod_paisxx AND
                         b.cod_paisxx = c.cod_paisxx AND
                         a.cod_ciudad = '" . $cod_ciudad . "' 
                GROUP BY 1 ORDER BY 2 ";
        $consulta = new Consulta($query, $this->conexion);
        $ciudad = $consulta->ret_matriz();

        if ($ind_namexx === TRUE)
            return $ciudad[0][1];
        else
            return $ciudad[0];
    }

    /* ! \fn: getVerifiConso
     *  \brief: 
     *  \author: 
     *  \date: dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: num_despac  Integer  Numero del despacho
     *  \return: 
     */

    function getVerifiConso($num_despac) {
        $mSql = " SELECT a.cod_deshij
                    FROM " . BASE_DATOS . ".tab_consol_despac a 
                   WHERE a.cod_despad = '$num_despac' 
                   LIMIT 0, 1 ";
        $mConsulta = new Consulta($mSql, $this->conexion);
        $mCodDeshij = $mConsulta->ret_arreglo();

        if ($mCodDeshij == NULL)
            return $num_despac;
        else
            return $mCodDeshij[0];
    }

    /* ! \fn: getDataDespac
     *  \brief: 
     *  \author: 
     *  \date: dd/mm/aaaa
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: num_despac  Integer  Numero del despacho
     *  \param: base_datos  String   Base de datos
     *  \param: despaOrigi  Integer  
     *  \return: 
     */

    function getDataDespac($num_despac, $base_datos, $despaOrigi) {
        $mReturn = array();

        $query = "SELECT a.num_despac, a.cod_manifi, 
                         IF( b.nom_conduc IS NOT NULL, b.nom_conduc, c.abr_tercer) AS abr_tercer, c.cod_tercer,
                         IF( a.con_telmov IS NULL OR a.con_telmov = '', c.num_telmov, a.con_telmov ) AS telmov,
                         IF( a.con_telef1 IS NULL OR a.con_telef1 = '', c.num_telef1, a.con_telef1),ind_defini,
                         e.nom_operad, f.nom_califi, a.tie_contra, a.ind_tiemod, a.obs_tiemod, b.num_placax,
                         b.cod_transp, UPPER( z.abr_tercer ) AS nom_transp,
                         x.nom_tipdes, m.tip_vehicu
                    FROM " . $base_datos . ".tab_despac_despac a 
               LEFT JOIN " . $base_datos . ".tab_despac_corona m 
                      ON a.num_despac = m.num_dessat,
                         " . $base_datos . ".tab_despac_vehige b,
                         " . $base_datos . ".tab_genera_tipdes x,
                         " . $base_datos . ".tab_tercer_tercer c,
                         " . $base_datos . ".tab_tercer_tercer z,
                         " . $base_datos . ".tab_tercer_conduc d 
               LEFT JOIN " . $base_datos . ".tab_genera_califi f ON f.cod_califi = d.cod_califi
               LEFT JOIN " . $base_datos . ".tab_operad_operad e ON e.cod_operad = d.cod_operad
                   WHERE a.num_despac = b.num_despac AND
                         b.cod_transp = z.cod_tercer AND
                         a.cod_tipdes = x.cod_tipdes AND
                         b.cod_conduc = c.cod_tercer AND
                         d.cod_tercer = c.cod_tercer AND
                         a.num_despac = '" . $num_despac . "' ";
        $consulta = new Consulta($query, $this->conexion);
        $_DATACON = $consulta->ret_matriz();

        $query = "SELECT e.nom_rutasx,a.cod_ciuori,a.cod_ciudes,
                         if(b.fec_llegpl Is Null,'SIN CONFIRMAR',DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y')),
                         DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y')
                    FROM " . $base_datos . ".tab_despac_vehige b,
                         " . $base_datos . ".tab_genera_rutasx e,
                         " . $base_datos . ".tab_genera_agenci g,
                         " . $base_datos . ".tab_despac_despac a  
                   WHERE a.num_despac = b.num_despac AND
                         b.cod_rutasx = e.cod_rutasx AND
                         b.cod_agenci = g.cod_agenci AND
                         a.num_despac = '" . $num_despac . "' ";
        $consulta = new Consulta($query, $this->conexion);
        $_DATARUT = $consulta->ret_matriz();

        $_FACTUR = "";
        $_CLIENT = "";

        $mSelect = "SELECT num_despac, num_docume, nom_destin
                      FROM " . $base_datos . ".tab_despac_destin
                     WHERE num_despac = '" . $despaOrigi . "' 
                  ORDER BY 1";
        $consulta = new Consulta($mSelect, $this->conexion);
        $facturas = $consulta->ret_matriz();

        if (sizeof($facturas) > 0) {
            foreach ($facturas as $fct) {
                $_FACTUR .= $_FACTUR != '' ? ',' . $fct['num_docume'] : $fct['num_docume'];
                $_CLIENT .= $_CLIENT != '' ? ',' . $fct['nom_destin'] : $fct['nom_destin'];
            }
        } else {
            $_FACTUR = "N/A";
            $_CLIENT = "N/A";
        }

        $query = "SELECT a.num_despac, a.num_desext, a.num_pedido, 
                         a.num_solici, a.num_consol, a.tip_transp, 
                         b.nom_produc, c.nom_poseed
                    FROM " . $base_datos . ".tab_despac_sisext a 
               LEFT JOIN " . $base_datos . ".tab_genera_produc b 
                      ON b.cod_produc = a.cod_mercan
               LEFT JOIN " . $base_datos . ".tab_despac_corona c
                      ON a.num_despac = c.num_dessat
                   WHERE a.num_despac = '" . $num_despac . "' ";
        $consulta = new Consulta($query, $this->conexion);
        $num_desext = $consulta->ret_matriz();

        if (in_array($num_desext[0]['tip_transp'], array('1', '2', '3')))
            $x = $num_desext[0]['tip_transp'];
        else
            $x = 0;

        $tip_transp = self::$cTipTrans[$x];

        /*         * ****************************************************************************** */
        $mReturn['num_despac'] = $_DATACON[0]['num_despac'];
        $mReturn['nom_tipdes'] = $_DATACON[0]['nom_tipdes'];
        $mReturn['cod_conduc'] = $_DATACON[0]['cod_tercer'];
        $mReturn['nom_conduc'] = $_DATACON[0]['abr_tercer'];
        $mReturn['cel_conduc'] = $_DATACON[0]['telmov'];
        $mReturn['tip_vehicu'] = $_DATACON[0]['tip_vehicu'];
        $mReturn['nom_empres'] = $_DATACON[0]['nom_transp'];
        $mReturn['num_placax'] = $_DATACON[0]['num_placax'];
        $mReturn['nom_rutaxx'] = $_DATARUT[0]['nom_rutasx'];
        $mReturn['nom_origen'] = $this->getCiudad($_DATARUT[0]['cod_ciuori'], $base_datos, TRUE);
        $mReturn['nom_destin'] = $this->getCiudad($_DATARUT[0]['cod_ciudes'], $base_datos, TRUE);
        $mReturn['cod_factur'] = $_FACTUR;
        $mReturn['nom_client'] = $_CLIENT;
        $mReturn['num_viajex'] = $num_desext[0]['num_desext'] != '' ? $num_desext[0]['num_desext'] : 'N/A';
        $mReturn['nom_mercan'] = $num_desext[0]['nom_produc'] != '' ? $num_desext[0]['nom_produc'] : 'N/A';
        $mReturn['num_pedido'] = $num_desext[0]['num_pedido'] != '' ? $num_desext[0]['num_pedido'] : 'N/A';
        $mReturn['num_solici'] = $num_desext[0]['num_solici'] != '' ? $num_desext[0]['num_solici'] : 'N/A';
        $mReturn['nom_poseed'] = $num_desext[0]['nom_poseed'] != '' ? $num_desext[0]['nom_poseed'] : 'N/A';
        $mReturn['nom_tiptra'] = $tip_transp;
        /*         * ****************************************************************************** */
        return $mReturn;
    }

    /* ! \fn: getNumViajexEmptra
     *  \brief: 
     *  \author: 
     *  \date:  dd/mm/aaaa
     *  \date modified: 
     *  \modified by: 
     *  \param: mDataDespac  Array  Data del despacho
     *  \return: 
     */

    function getNumViajexEmptra($mDataDespac = NULL) {
        try {
            # Valida relacion despacho-despacho viaje (empresas terceras con GL)
            $mIndViaje = "SELECT num_viajex, num_placax FROM " . BASE_DATOS . ".tab_despac_viajex WHERE num_despac = '{$mDataDespac[despac]}' ";
            $mConsult = new Consulta($mIndViaje, $this->conexion);
            $mResult = $mConsult->ret_matrix('a');



            // valida si es un viaje corona
            $mFindDessat = " SELECT num_dessat FROM " . BASE_DATOS . ".tab_despac_corona  WHERE num_despac = '" . $mResult[0]["num_viajex"] . "' ";
            $mConsult = new Consulta($mFindDessat, $this->conexion);
            $mExistVJ = $mConsult->ret_matrix('a');
            if (sizeof($mExistVJ) > 0) {
                $fNumDespac = $mExistVJ[0]["num_dessat"];
            } else {

                // valida si es un viaje diferente a corona, caso FAGOVER, empresas terceras que le reportan a fagover desde Gl o desde externo, corona usa tablas propias
                // para este caso busca un despacho que tenga ese "Viaje" -> manifiesto, con esa placa, para replicar la novedad
                $mFindDessat = "SELECT a.num_despac AS num_dessat
                                  FROM 
                                        ".BASE_DATOS.".tab_despac_despac a INNER JOIN 
                                        ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac AND 
                                                                              a.fec_salida IS NOT NULL AND 
                                                                              a.fec_salida <= NOW() AND
                                                                              a.ind_planru = 'S' AND 
                                                                              a.ind_anulad = 'R' AND 
                                                                              b.ind_activo = 'S' AND
                                                                              a.cod_manifi = '" . trim($mResult[0]["num_viajex"]) . "' AND 
                                                                              b.num_placax = '" . trim($mResult[0]["num_placax"]) . "'
                                 WHERE 1 = 1 LIMIT 1 ";
                $mConsult = new Consulta($mFindDessat, $this->conexion);
                $mExistVJ = $mConsult->ret_matrix('a');
                if (sizeof($mExistVJ) > 0) {
                    if($mDataDespac['despac'] == $mExistVJ[0]["num_dessat"] )
                    {
                        throw new Exception("El cliente no se debe reportar a si mismo, No se encuentra numero de Viaje " . $mResult[0]["num_viajex"], "1999");
                    }else{
                        $fNumDespac = $mExistVJ[0]["num_dessat"];
                    }
                        
                }
                else{
                    throw new Exception("No se encuentra numero de Viaje " . $mResult[0]["num_viajex"], "1999");
                    break;
                }
            }

            // Busca los datos complementarios del despacho, para poder hacer la replicacion de la novedad, validando que el despacho encontrado por el manifiesto esté en ruta y activo
            $mQuery = 'SELECT a.num_despac, 
                         b.cod_transp, 
                         a.cod_manifi,
                         b.num_placax,
                         b.cod_rutasx
                    FROM ' . BASE_DATOS . '.tab_despac_despac a 
                         INNER JOIN ' . BASE_DATOS . '.tab_despac_vehige b ON a.num_despac = b.num_despac
                         INNER JOIN ' . BASE_DATOS . '.tab_tercer_tercer c ON b.cod_transp = c.cod_tercer                   
                   WHERE a.fec_salida IS NOT NULL 
                     AND a.fec_salida <= NOW() 
                    /* Nelson AND (a.fec_llegad IS NULL OR a.fec_llegad = "0000-00-00 00:00:00") */
                     AND a.ind_planru = "S" 
                     AND a.ind_anulad = "R"
                     AND b.ind_activo = "S"  
                     AND a.num_despac = "' . $fNumDespac . '" ';


            $mConsult = new Consulta($mQuery, $this->conexion);
            $fResultNumDespac = $mConsult->ret_matrix('a');
            if (sizeof($fResultNumDespac) > 0) {
                $fNumDespac = $fResultNumDespac[0];
            } else {
                throw new Exception("No se encuentra numero de despacho del Viaje.", "1999");
                break;
            }

            return array('cod_respon' => '1000', 'msg_respon' => $fNumDespac);
        } catch (Exception $e) {
            return array('cod_respon' => '6001', 'msg_respon' => $e->getMessage());
        }
    }

    /* ! \fn: InsertarNovedadPC
     *  \brief: Guarda las novedades en sitio
     *  \author: 
     *  \date:  dd/mm/aaaa
     *  \date modified: 27/10/2015
     *  \modified by: Ing. Fabian Salinas
     *  \param: base_datos  String   Base de datos
     *  \param: regist      Array    Data a registrar
     *  \param: ominter
     *  \return: 
     */

    function InsertarNovedadPC($base_datos, $regist, $ominter) {
        $mIndEnSiti = true;
        if (in_array($regist['noveda'], self::$cCodNoved['4']) && BASE_DATOS=='satt_faro') { #Solucion a novedad Corona
            return self::InsertarNovedadNC($base_datos, $regist, $ominter);
        } else {
            $mIndSalida = self::verifyNovSalida($regist['despac'], $regist['fecnov']); #Indicador fecha Novedad antes de salida del despacho
            $mUltNoveda = getNovedadesDespac($this->conexion, $regist['despac'], '2'); #Data ultima novedad
            $mIndExists = self::verifyNovExists($regist, $mIndEnSiti); #Indicador Novedad del despacho existe
            $RESPON = array();
            $_ERROR = array();
            if ($mIndSalida != false) {
                $RESPON[0]['indica'] = 0;
                $RESPON[0]['mensaj'] = $mIndSalida;
            } elseif (( $regist['fecnov'] <= $mUltNoveda['fec_noveda'] ) && ($regist["ind_grides"] != "1" )) {
                $RESPON[0]['indica'] = 0;
                $RESPON[0]['mensaj'] = "La Fecha de la Novedad Debe ser Mayor a la Fecha de la Ultima Novedad.";
            } elseif ($mIndExists != false) {
                $RESPON[0]['indica'] = 0;
                $RESPON[0]['mensaj'] = $mIndExists;
            } else {
                $mDatDespac = self::getDataBasicDespac($regist['despac']); #Data basica del despacho
                $mDatUsuari = self::getDataUsuari($regist['usuari']); #Datos del usuario
                #<Depura el array $regist>
                $regist['rutax'] = $mDatDespac['cod_rutasx']; #Codigo de la ruta del despacho actual (Necesaria si cambio de ruta)
                $regist['nittra'] = $mDatDespac['cod_transp']; #Nit de la transportadora
                $this->cod_transp = $mDatDespac['cod_transp'];

                if (!$regist['tieadi']) {
                    $regist['tieadi'] = 0;
                }
                $regist['observ'] .= self::updateCelConduc($regist, $mDatDespac['con_telmov']); #Actualiza el numero de celular del conductor

                if ($regist['noveda'] == CONS_NOVEDA_ACAFAR) {
                    $regist['observ'] .= ". Vuelve el Vehiculo a Monitoreo Faro. ";
                }
                #</Depura el array $regist>
                $mCodSitiox = self::getSitio($regist['sitio']); #Codigo del Sitio
                $mDifTieAla = self::getDiffTimeFecnoveFecalar($regist); #Diferencia en minutos fecha de la novedad VS fecha de la alarma
                $mDatNoveda = self::getNoveda($regist['noveda'], $regist['nittra']); #Data de la Novedad tab_genera_noveda
                $mDatTransp = getTransTipser($this->conexion, " AND a.cod_transp = " . $mDatDespac['cod_transp']); # Parametros de la transportadora
                $mDatTransp = $mDatTransp[0];
                $mIndProtoc = self::indProtoc($regist['nittra'], $regist['noveda']);
                //Valida si la parametrizacion de la novedad tiene tiempo parametrizado
                if(BASE_DATOS != 'satt_faro'){
                    if($mDatNoveda['num_tiempo'] > 0){
                        $regist['tieadi'] = $mDatNoveda['num_tiempo'];
                    }
                }
                
                #<Envio de Correos>                
                if ($mDatNoveda['ind_notsup'] == 1 && $mIndProtoc == false) { #CORREO NOTIFICA SUPERVISOR.
                    if (!self::SendMailSupervisores($regist, $mDatDespac, $mDatNoveda)) {
                        $_ERROR[] = 'Error envio correo Notificacion Supervisores.';
                    }
                }
                if ($mDatNoveda['nov_especi'] == 1 && (!in_array($mDatDespac['cod_transp'], self::$cLisTrans['NoMailNovEsp'])) && $regist['noveda'] != CONS_NOVEDA_ACAEMP && $regist['noveda'] != CONS_NOVEDA_ACAFAR && $mIndProtoc == false
                ) { #Novedad Especial diferente a (a cargo faro, a cargo empresa), Notificacion correo
                    $mResult = self::SendMailNovedadEspeci($regist, $mDatDespac, $mDatNoveda);
                    if (!$mResult) {
                        $_ERROR[] = 'Error envio correo Novedad Especial.';
                    }
                    $regist['observ'] .= $mResult;
                }
                if ($regist['noveda'] == CONS_NOVEDA_ACAEMP || $regist['noveda'] == CONS_NOVEDA_ACAFAR) { #Envia correo a la empresa y al usuario en las novedades de acargo empresa y acargo faro
                    $mResult = self::sendMailACargo($regist, $mDatDespac, $mDatNoveda);
                    if (!$mResult) {
                        $_ERROR[] = 'Error envio correo A cargo Empersa o A cargo Faro.';
                    }
                    $regist['observ'] .= $mResult;

                    
                }
                #</Envio de Correos>
                if ($_REQUEST['ind_solRec'] == '1') { #Guarda el indicador de solucion de la recomendacion
                    $mResult = self::updateSoluciRecome($regist);
                    if (!$mResult[0]) {
                        $_ERROR[] = $mResult[1];
                    }
                }
                #Inserta la novedad
                $mInsNoveda = self::insertNovedad($regist, $mDifTieAla, $mCodSitiox, $mIndEnSiti, $mDatUsuari);
                if (!$mInsNoveda[0]) {
                    $_ERROR[] = $mInsNoveda[1];
                }
                if ($mDatTransp['ind_llegad'] == 1 && $mDatDespac['ind_defini'] != '1') { #Despachos de llegado automatico
                    $mResult = self::updateLlegadAuto($regist);
                    if (!$mResult[0]) {
                        $_ERROR[] = $mResult[1];
                    }
                }
                if ($mDatNoveda['ind_manala'] == '0') { #Actualiza fec_alarma en tab_despac_seguim
                    $mTieAcumul = self::updateFecAlarma($regist, $mDatNoveda, $mDatTransp, $mIndEnSiti);

                    if ($mTieAcumul > 0) {
                        self::updateFinDespac($regist, $mTieAcumul);
                    }
                }
                #SOLUCION DE NOTIFICACIONES DEL APLICATIVO MOVIL
                if (in_array($_SESSION['datos_usuario']['cod_perfil'], self::$cCodPerfi[0])) {
                    self::updateSoluciMovil();
                }
                #<Envio de Correos 2>
                self::sendMailCitaEntrega($regist, $mDatDespac, $mDatNoveda, $mTieAcumul[1]); #Retraso fecha cita entrega
                if ($mIndProtoc == true) {
                    self::validacionProtocolos($regist, $mDatDespac, $mDatNoveda, $mIndEnSiti); #VALIDACION DE PROTOCOLOS
                }
                if (in_array($regist['noveda'], self::$cCodNoved['3'])) {
                    self::soluciNovedad($regist, $mDatDespac, $mDatNoveda); #Solucion a novedad
                }
                #</Envio de Correos 2>
                #Interfaces
                if (HOST_WEB == HOST_WEB_PRO) {
                    self::soaPs($regist, $ominter, $mDatDespac, $mDatNoveda, $mIndEnSiti);
                }
                #Envio correo Novedad genera alarma
                if ($mDatNoveda['ind_alarma'] == 'S' OR $mDatNoveda['ind_alarma'] == 1) {
                    self::sendMailNovGeneraAlarma($regist, $mDatDespac, $mDatNoveda, $mIndEnSiti);
                }
                #Guarda el codigo de novedad en tab_despac_despac cod_ultnov
                $mUpdUltNov = self::updateUltNovedad($regist, $mFecManAla);
                if (!$mUpdUltNov[0]) {
                    $_ERROR[] = $mUpdUltNov[1];
                }
                
                $RESPON[0]['indica'] = 1;
                $RESPON[0]['mensaj'] = "La Novedad se Registro Exitosamente en el Sistema";
            }
            /*$mViaje = self::getNumViajexEmptra($regist);
            if ($mViaje["cod_respon"] == '1000') {

                //$mNextPC = getNextPC($this->conexion, $mViaje["msg_respon"]["num_despac"]); // Busca PC habilitado del despacho a replicar la novedad         
                $regist["despac"] = $mViaje["msg_respon"]["num_despac"];
                $regist["nittra"] = $mViaje["msg_respon"]["cod_transp"];
                $regist["rutax"] = $mViaje["msg_respon"]["cod_rutasx"];
                $regist["contro"] = $mNextPC["cod_contro"];

                //$mRecursive = self::InsertarNovedadNC($base_datos, $regist, $ominter);
            }*/
            return $RESPON;
        }
    }

    /*! \fn: loadImage
    *  \brief: carga imagenes en la bd
    *  \author: Ing. Miguel Romero
    *    \date: 14/09/2016 
    *  \param: binFoto = binario de la foto 
    *  \return valor que retorna
    */
    
    
    public function loadImage($numDespac, $binFoto, $pueContro)
    {

        $max =  "SELECT MAX(num_consec) AS consecutivo
                   FROM ".BASE_DATOS.".tab_despac_images
                WHERE num_despac = '".$numDespac."'";
 
        $mConsult = new Consulta($max, $this->conexion);
        $consecutivo = $mConsult->ret_matrix('a');

        $consecutivo = ($consecutivo[0]['consecutivo']+1);
 
        $sql = "INSERT INTO ".BASE_DATOS.".tab_despac_images (
                            num_despac ,
                            cod_contro ,
                            num_consec ,
                            bin_fotoxx ,
                            usr_creaci ,
                            fec_creaci
                            )
                            VALUES (
                            '".$numDespac."',  
                            '".$pueContro."',  
                            ".$consecutivo.",  
                            '".$binFoto."',
                            '".$_SESSION['datos_usuario']['cod_usuari']."',  
                            NOW());"; 

        $mConsult = new Consulta($sql, $this->conexion, "R"); 
 
    }

    /* ! \fn: InsertarNovedadNC
     *  \brief: Guarda las novedades antes de sitio
     *  \author: 
     *  \date:  dd/mm/aaaa
     *  \date modified: 27/10/2015
     *  \modified by: Ing. Fabian Salinas
     *  \param: base_datos  String   Base de datos
     *  \param: regist      Array    Data a registrar
     *  \param: ominter
     *  \return: 
     */

    function InsertarNovedadNC($base_datos, $regist, $ominter) {
        $mIndEnSiti = false;

        if (in_array($regist['noveda'], self::$cCodNoved['4']) && BASE_DATOS=='satt_faro') { #Solucion a novedad Corona
            $mNextPc = getNextPC($this->conexion, $regist['despac']);

            $regist['contro'] = $mNextPc['cod_contro'];
            $regist['sitio'] = 'NO REGISTRA';
        }

        if (in_array($regist['noveda'], self::$cCodNoved['5']) && BASE_DATOS=='satt_faro') { #Guarda las recomendaciones por despacho
            self::insertRecome($regist);
            $RESPON[0]["indica"] = 1;
            $RESPON[0]["mensaj"] = "La recomendacion se Registro Exitosamente en el Sistema";
        } else {
            $mIndSalida = self::verifyNovSalida($regist['despac'], $regist['fecnov']); #Indicador fecha Novedad antes de salida del despacho
            $mUltNoveda = getNovedadesDespac($this->conexion, $regist['despac'], '2'); #Data ultima novedad
            $mIndExists = self::verifyNovExists($regist, $mIndEnSiti); #Indicador Novedad del despacho existe

            $RESPON = array();
            $_ERROR = array();

            if ($mIndSalida != false) {
                $RESPON[0]['indica'] = 0;
                $RESPON[0]['mensaj'] = $mIndSalida;
            }                                                           #ID: 201924
            elseif (($regist['fecnov'] <= $mUltNoveda['fec_noveda'] ) && ($regist["ind_grides"] != "1" )) {
                $RESPON[0]['indica'] = 0;
                $RESPON[0]['mensaj'] = "La Fecha de la Novedad Debe ser Mayor a la Fecha de la Ultima Novedad.";
            } 
            elseif ($mIndExists != false) {
                $RESPON[0]['indica'] = 0;
                $RESPON[0]['mensaj'] = $mIndExists;
            } else {
                $mDatDespac = self::getDataBasicDespac($regist['despac']); #Data basica del despacho
                $mDatUsuari = self::getDataUsuari($regist['usuari']); #Datos del usuario
                #<Depura el array $regist>
                $regist['rutax'] = $mDatDespac['cod_rutasx']; #Codigo de la ruta del despacho actual (Necesaria si cambio de ruta)
                $regist['nittra'] = $mDatDespac['cod_transp']; #Nit de la transportadora
                $this->cod_transp = $mDatDespac['cod_transp'];

                if (!$regist['tieadi'])
                    $regist['tieadi'] = 0;

                if ($regist['indsit'] != '1')
                    $regist['indsit'] = 0;

                $regist['observ'] .= self::updateCelConduc($regist, $mDatDespac['con_telmov']); #Actualiza el numero de celular del conductor

                if ($regist['noveda'] == CONS_NOVEDA_ACAFAR)
                    $regist['observ'] .= ". Vuelve el Vehiculo a Monitoreo Faro. ";
                #</Depura el array $regist>

                $mCodSitiox = self::getSitio($regist['sitio']); #Codigo del Sitio
                $mDifTieAla = self::getDiffTimeFecnoveFecalar($regist); #Diferencia en minutos fecha de la novedad VS fecha de la alarma
                $mDatNoveda = self::getNoveda($regist['noveda'], $regist['nittra']); #Data de la Novedad tab_genera_noveda
                $mDatTransp = getTransTipser($this->conexion, " AND a.cod_transp = " . $mDatDespac['cod_transp']); # Parametros de la transportadora
                $mDatTransp = $mDatTransp[0];
                $mIndProtoc = self::indProtoc($regist['nittra'], $regist['noveda']);
                //Valida si la parametrizacion de la novedad tiene tiempo parametrizado
                if(BASE_DATOS != 'satt_faro'){
                    if($mDatNoveda['num_tiempo'] > 0){
                        $regist['tieadi'] = $mDatNoveda['num_tiempo'];
                    }
                }
                #<Envio de Correos>
                if ($mDatNoveda['ind_notsup'] == 1 && $mIndProtoc == false) { #CORREO NOTIFICA SUPERVISOR.
                    if (!self::SendMailSupervisores($regist, $mDatDespac, $mDatNoveda))
                        $_ERROR[] = 'Error envio correo Notificacion Supervisores.';
                }

                if ($mDatNoveda['nov_especi'] == 1 && (!in_array($mDatDespac['cod_transp'], self::$cLisTrans['NoMailNovEsp'])) && $regist['noveda'] != CONS_NOVEDA_ACAEMP && $regist['noveda'] != CONS_NOVEDA_ACAFAR && $mIndProtoc == false
                ) { #Novedad Especial diferente a (a cargo faro, a cargo empresa), Notificacion correo
                    $mResult = self::SendMailNovedadEspeci($regist, $mDatDespac, $mDatNoveda);
                    if (!$mResult[0])
                        $_ERROR[] = 'Error envio correo Novedad Especial.';

                    $regist['observ'] .= $mResult;
                }

                if ($regist['noveda'] == CONS_NOVEDA_ACAEMP || $regist['noveda'] == CONS_NOVEDA_ACAFAR) { #Envia correo a la empresa y al usuario en las novedades de acargo empresa y acargo faro
                    $mResult = self::sendMailACargo($regist, $mDatDespac, $mDatNoveda);
                    if (!$mResult[0])
                        $_ERROR[] = 'Error envio correo A cargo Empersa o A cargo Faro.';

                    $regist['observ'] .= $mResult;
                }   
                #</Envio de Correos>
                #Inserta la novedad
                $mInsNoveda = self::insertNovedad($regist, $mDifTieAla, $mCodSitiox, $mIndEnSiti, $mDatUsuari);
                if (!$mInsNoveda[0])
                    $_ERROR[] = $mInsNoveda[1];
                else
                    $mNumConsec = $mInsNoveda[1];#Consecutivo de la novedad registrada en tab_despac_contro

                if ($mDatNoveda['ind_manala'] == '0') { #Actualiza fec_alarma en tab_despac_seguim
                    $mTieAcumul = self::updateFecAlarma($regist, $mDatNoveda, $mDatTransp, $mIndEnSiti);

                    if ($mTieAcumul > 0)
                        self::updateFinDespac($regist, $mTieAcumul);
                }

                #SOLUCION DE NOTIFICACIONES DEL APLICATIVO MOVIL
                if (in_array($_SESSION['datos_usuario']['cod_perfil'], self::$cCodPerfi[0]))
                    self::updateSoluciMovil();

                #<Envio de Correos 2>
                if ($mIndProtoc == true)
                    self::validacionProtocolos($regist, $mDatDespac, $mDatNoveda, $mIndEnSiti, $mNumConsec, $mCodSitiox);#VALIDACION DE PROTOCOLOS

                if (in_array($regist['noveda'], self::$cCodNoved['3']))
                    self::soluciNovedad($regist, $mDatDespac, $mDatNoveda);#Solucion a novedad
                #</Envio de Correos 2>
                #Interfaces
                if (HOST_WEB == HOST_WEB_PRO) {
                    self::soaPs($regist, $ominter, $mDatDespac, $mDatNoveda, $mIndEnSiti);
                }

                #Envio correo Novedad genera alarma
                if ($mDatNoveda['ind_alarma'] == 'S' OR $mDatNoveda['ind_alarma'] == 1)
                    self::sendMailNovGeneraAlarma($regist, $mDatDespac, $mDatNoveda, $mIndEnSiti);

                #Guarda el codigo de novedad en tab_despac_despac cod_ultnov
                $mUpdUltNov = self::updateUltNovedad($regist, $mFecManAla);
                if (!$mUpdUltNov[0])
                    $_ERROR[] = $mUpdUltNov[1];

                $RESPON[0]['indica'] = 1;
                $RESPON[0]['mensaj'] = "La Novedad se Registro Exitosamente en el Sistema";
            }
        }

        #Inicia recursivo para replicar novedad a corona
        /*$mViaje = self::getNumViajexEmptra($regist);
        if ($mViaje["cod_respon"] == '1000') {

            //$mNextPC = getNextPC($this->conexion, $mViaje["msg_respon"]["num_despac"]); // Busca PC habilitado del despacho a replicar la novedad         
            $regist["despac"] = $mViaje["msg_respon"]["num_despac"];
            $regist["nittra"] = $mViaje["msg_respon"]["cod_transp"];
            $regist["rutax"] = $mViaje["msg_respon"]["cod_rutasx"];
            $regist["contro"] = $mNextPC["cod_contro"];


            //$mRecursive = self::InsertarNovedadNC($base_datos, $regist, $ominter);
        }*/

        return $RESPON;
    }

    /* ! \fn: getDataBasicDespac
     *  \brief: Trae la Data basica del despacho
     *  \author: Ing. Fabian Salinas
     *  \date:  27/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mNumDespac  Integer  Numero del Despacho
     *  \return: Integer
     */

    private function getDataBasicDespac($mNumDespac) {
        $mSql = "SELECT a.cod_rutasx, a.cod_transp,
                        IF( b.con_telmov IS NULL OR b.con_telmov = '', d.num_telmov, b.con_telmov ) AS con_telmov, 
                        b.cod_tipdes, a.num_placax, b.cod_manifi, 
                        b.ind_defini, a.fec_salipl, c.dir_emailx, 
                        c.abr_tercer AS nom_transp, 
                        d.abr_tercer AS nom_conduc, 
                        UPPER(e.nom_ciudad) AS nom_ciuori, 
                        UPPER(f.nom_ciudad) AS nom_ciudes, 
                        IF(b.cod_tipdes = '2', '1', '0' ) AS ind_tipdes,
                        h.nom_config 
                   FROM " . BASE_DATOS . ".tab_despac_vehige a 
             INNER JOIN " . BASE_DATOS . ".tab_despac_despac b ON a.num_despac = b.num_despac 
             INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer c ON a.cod_transp = c.cod_tercer 
             INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer d ON a.cod_conduc = d.cod_tercer 
             INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad e ON b.cod_ciuori = e.cod_ciudad 
             INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad f ON b.cod_ciudes = f.cod_ciudad
             INNER JOIN " . BASE_DATOS . ".tab_vehicu_vehicu g ON a.num_placax = g.num_placax
             LEFT  JOIN " . BASE_DATOS . ".tab_vehige_config h ON g.num_config = h.num_config 
                  WHERE a.num_despac = '$mNumDespac' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult = $mConsult->ret_matrix('a');

        $mResult[0]['mai_transp'] = self::getMailTransp($mResult[0]['cod_transp']);

        return $mResult[0];
    }

    /* ! \fn: getDataUsuari
     *  \brief: Trae la data del Usuario
     *  \author: Ing. Fabian Salinas
     *  \date:  01/01/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mCodUsuari  String  Codigo del usuario
     *  \return: Array
     */

    private function getDataUsuari($mCodUsuari = null) {
        $mSql = "SELECT a.cod_consec, a.cod_usuari, a.num_cedula, 
                        a.nom_usuari, a.usr_emailx, a.cod_perfil, 
                        a.cod_contro, a.cod_inicio 
                   FROM " . BASE_DATOS . ".tab_genera_usuari a 
                  WHERE a.cod_usuari = '$mCodUsuari' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult = $mConsult->ret_matrix('a');

        return $mResult[0];
    }

    /* ! \fn: verifyNovExists
     *  \brief: Verifica si la novedad ya existe
     *  \author: Ing. Fabian Salinas
     *  \date:  27/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mData       Matriz   Data de la novedad a guardar
     *  \param: mIndEnSiti  Boolean  true = En SITIO; false = Antes de Sitio
     *  \return: String o Boolean 
     */

    private function verifyNovExists($mData, $mIndEnSiti = false) {
        if ($mIndEnSiti == true) {
            $mSql = " SELECT a.cod_noveda, a.num_despac 
                        FROM " . BASE_DATOS . ".tab_despac_noveda a 
                       WHERE a.cod_contro = '" . $mData['contro'] . "' 
                         AND a.cod_noveda = '" . $mData['noveda'] . "' 
                         AND a.num_despac = '" . $mData['despac'] . "' 
                         AND a.fec_noveda = '" . $mData['fecnov'] . "' ";
        } else {
            $mSql = "SELECT a.cod_noveda, a.num_despac 
                       FROM " . BASE_DATOS . ".tab_despac_contro a 
                      WHERE a.cod_contro = '" . $mData['contro'] . "' 
                        AND a.cod_noveda = '" . $mData['noveda'] . "' 
                        AND a.num_despac = '" . $mData['despac'] . "' 
                        AND a.fec_contro = '" . $mData['fecnov'] . "' ";
        }

        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult = $mConsult->ret_matrix('a');

        if (sizeof($mResult) > 0)
            return 'La Novedad ya se Encuentra Registrada en el Puesto de Control Seleccionado con la misma fecha.'; 
        else
            return false;
    }

    /* ! \fn: verifyNovSalida
     *  \brief: Verifica si la fecha de la Novedad es menor a la fecha de salida del despacho
     *  \author: Ing. Fabian Salinas
     *  \date:  27/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mNumDespac
     *  \return: String o Boolean 
     */

    private function verifyNovSalida($mNumDespac, $mFecNoveda) {
        $mSql = "SELECT a.fec_salida 
                   FROM " . BASE_DATOS . ".tab_despac_despac a
                  WHERE a.num_despac = '$mNumDespac' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult = $mConsult->ret_arreglo();

        if ($mFecNoveda <= $mResult[0])
            return 'La Fecha de la Novedad Debe ser Mayor a la Fecha de Salida Asignada al Despacho.';
        else
            return false;
    }

    /* ! \fn: getDiffTimeFecnoveFecalar
     *  \brief: Trae en la diferencia en minutos de la fecha de la novedad a guardar con fecha de alarma
     *  \author: Ing. Fabian Salinas
     *  \date: 27/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mData  Matriz  Data de la novedad a guardar
     *  \return: Integer
     */

    private function getDiffTimeFecnoveFecalar($mData) {
        $mSql = "SELECT (TIME_TO_SEC(TIMEDIFF('" . $mData["fecact"] . "', a.fec_alarma))/60) AS dif_tiempo 
                   FROM " . BASE_DATOS . ".tab_despac_seguim a 
                  WHERE a.num_despac = '" . $mData["despac"] . "' 
                    AND a.cod_rutasx = '" . $mData['rutax'] . "' 
                    AND a.cod_contro = '" . $mData["contro"] . "' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult = $mConsult->ret_arreglo();

        if ($mResult[0] > 0)
            $mResult = floor($mResult[0]);
        else
            $mResult = 0;

        return $mResult;
    }

    /* ! \fn: updateCelConduc
     *  \brief: Actualiza el numero de celular del conductor
     *  \author: Ing. Fabian Salinas
     *  \date: 27/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mData  Matriz  Data de la novedad a guardar
     *  \param: mConTelmov  String  Numero celular conductor
     *  \return: String
     */

    private function updateCelConduc($mData, $mConTelmov) {
        if ((int) $mData["celular"] != '')
            $cel = (int) $mData["celular"];

        if ($cel) {
            $mSql = "UPDATE " . BASE_DATOS . ".tab_despac_despac 
                        SET con_telmov = '$cel' 
                      WHERE num_despac = '" . $mData["despac"] . "'";
            $update = new Consulta($mSql, $this->conexion, "R");

            return ".Celular Nuevo: $cel, Celular Anterior " . $mConTelmov;
        } else
            return "";
    }

    /* ! \fn: getSitio
     *  \brief: valida si el nombre del sitio existe, si no existe lo inserta
     *  \author: Ing. Fabian Salinas
     *  \date: 27/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mNomSitiox  String  Nombre del Sitio
     *  \return: Integer
     */

    private function getSitio($mNomSitiox) {
        //valida si el nombre del sitio existe
        $mSql = " SELECT cod_sitiox 
                    FROM " . BASE_DATOS . ".tab_despac_sitio 
                   WHERE nom_sitiox LIKE '$mNomSitiox' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mSitio = $mConsult->ret_matrix('i');

        if (!$mSitio) {
            $mSql = "SELECT MAX(cod_sitiox) 
                       FROM " . BASE_DATOS . ".tab_despac_sitio ";
            $mConsult = new Consulta($mSql, $this->conexion);
            $mSitio = $mConsult->ret_matrix('i');
            $mCodSitiox = $mSitio[0][0] + 1;

            $mSql = "INSERT INTO " . BASE_DATOS . ".tab_despac_sitio
                            ( cod_sitiox, nom_sitiox )
                     VALUES ( $mCodSitiox, '$mNomSitiox' ) ";
            $mConsult = new Consulta($mSql, $this->conexion, "R");
        } else
            $mCodSitiox = $mSitio[0][0];

        return $mCodSitiox;
    }

    /* ! \fn: getNoveda
     *  \brief: Trae la Data de la Novedad
     *  \author: Ing. Fabian Salinas
     *  \date:  27/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mCodNoveda  Integer  Codigo de la Novedad
     *  \param: mCodTransp  Integer  Codigo de la empresa transportadora
     *  \return: Array
     */

    private function getNoveda($mCodNoveda, $mCodTransp = NULL) {
        if(BASE_DATOS=='satt_faro'){
            $mSql = "SELECT * FROM " . BASE_DATOS . ".tab_genera_noveda WHERE cod_noveda = '$mCodNoveda' ";
        }else{
            $mSql = " SELECT a.nom_noveda,
                             
                             IFNULL(b.ind_notsup, 0) as 'ind_notsup',
                             IFNULL(b.ind_novesp, 0) as 'nov_especi',
                             IFNULL(b.ind_manale, 0) as 'ind_manala',
                             IFNULL(b.ind_manale, 0) as 'ind_alarma',
                             0 as 'ind_insveh',
                             IFNULL(b.ind_soltie, 0) as 'ind_tiempo',
                             IFNULL(b.num_tiempo, 0) as 'num_tiempo'
                        FROM " . BASE_DATOS . ".tab_genera_noveda a 
                        LEFT JOIN " . BASE_DATOS . ".tab_parame_novseg b ON
                        a.cod_noveda = b.cod_noveda AND
                        b.cod_transp = '".$mCodTransp."' AND
                        b.ind_status = 1 
                      WHERE a.cod_noveda = '" . $mCodNoveda . "'";
        }
        
        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult = $mConsult->ret_matrix('a');

        return $mResult[0];
    }

    /* ! \fn: getMailTransp
     *  \brief: Trae los eMails por transportadora
     *  \author: Ing. Fabian Salinas
     *  \date:  28/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mCodTransp
     *  \return: String
     */

    private function getMailTransp($mCodTransp) {
        $mSql = " SELECT GROUP_CONCAT( DISTINCT (CONCAT(c.dir_dispos, '@', b.dns_operad)) ORDER BY 1 ASC SEPARATOR ',' ) AS email 
                    FROM satt_standa.tab_mensaj_operad b 
              INNER JOIN satt_standa.tab_mensaj_dispos c 
                      ON c.cod_operad = b.cod_operad 
                     AND c.cod_transp = b.cod_transp 
                     AND c.cod_operad = b.cod_operad 
                   WHERE c.ind_novala = 1 
                     AND c.ind_estado = 1 
                     AND b.cod_transp = '$mCodTransp' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult = $mConsult->ret_matrix('a');
        return $mResult[0]['email'];
    }

    /* ! \fn: SendMailSupervisores
     *  \brief: Envia Correo a supervisores
     *  \author: Ing. Fabian Salinas
     *  \date: 28/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array  Datos de la novedad a registrar
     *  \param: mDatDespac  Array  Datos basicos del despacho
     *  \param: mDatNoveda  Array  Datos basicos de la noveadad
     *  \return: Boolean
     */

    private function SendMailSupervisores($mDatRegist = null, $mDatDespac = null, $mDatNoveda = null) {

        $datos->nom_noveda = $mDatNoveda['nom_noveda'];
        $datos->num_placax = $mDatDespac['num_placax'];
        $datos->num_despac = $mDatRegist['despac'];
        $datos->nom_transp = $mDatDespac['nom_transp'];
        $datos->cod_manifi = $mDatDespac['cod_manifi'];
        $datos->nom_ciuori = $mDatDespac['nom_ciuori'];
        $datos->nom_ciudes = $mDatDespac['nom_ciudes'];
        $datos->nom_conduc = $mDatDespac['nom_conduc'];
        $datos->con_telmov = $mDatDespac['con_telmov'];
        $datos->fec_noveda = $mDatRegist['fecact'];
        $datos->nom_sitiox = $mDatRegist['sitio'];
        $datos->obs_noveda = $mDatRegist['observ'];
        $datos->ema_asigna = $mDatDespac['mai_transp'];
        $datos->usr_creaci = $mDatRegist['usuari'];
        $datos->asunto = "NOTIFICACION SUPERVISOR";
        $datos->mai_destin = MAIL_SUPERVISORES;
        $datos->rutpla = $mDatRegist['rutpla'];

        return self::sendMail($datos);
    }

    /* ! \fn: SendMailNovedadEspeci
     *  \brief: Envia correo por novedad es especial
     *  \author: Ing. Fabian Salinas
     *  \date: 28/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array  Datos de la novedad a registrar
     *  \param: mDatDespac  Array  Datos basicos del despacho
     *  \param: mDatNoveda  Array  Datos basicos de la noveadad
     *  \return: Array
     */

    private function SendMailNovedadEspeci($mDatRegist = null, $mDatDespac = null, $mDatNoveda = null) {
        #<Carga la variable mMail>
        $mSql = "SELECT ind_notage
                   FROM " . BASE_DATOS . ".tab_transp_tipser
                  WHERE cod_transp = '" . $mDatDespac['cod_transp'] . "' 
                    AND num_consec = (  SELECT MAX(num_consec) 
                                          FROM " . BASE_DATOS . ".tab_transp_tipser 
                                         WHERE cod_transp = '" . $mDatDespac['cod_transp'] . "'
                                     ) ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mIndNotage = $mConsult->ret_arreglo();

        $mEmailAgenci = self::getEmailAgecni($mDatRegist['despac']);

        if ($mIndNotage[0] == 1 && $mEmailAgenci)
            $mMail = $mEmailAgenci;
        else
            $mMail = $mDatDespac['mai_transp'];
        #</Carga la variable mMail>

        $datos->nom_noveda = $mDatNoveda['nom_noveda'];
        $datos->num_placax = $mDatDespac['num_placax'];
        $datos->num_despac = $mDatRegist['despac'];
        $datos->nom_transp = $mDatDespac['nom_transp'];
        $datos->cod_manifi = $mDatDespac['cod_manifi'];
        $datos->nom_ciuori = $mDatDespac['nom_ciuori'];
        $datos->nom_ciudes = $mDatDespac['nom_ciudes'];
        $datos->nom_conduc = $mDatDespac['nom_conduc'];
        $datos->con_telmov = $mDatDespac['con_telmov'];
        $datos->fec_noveda = $mDatRegist['fecact'];
        $datos->nom_sitiox = $mDatRegist['sitio'];
        $datos->obs_noveda = $mDatRegist['observ'];
        $datos->ema_asigna = $mDatDespac['mai_transp'];
        $datos->usr_creaci = $mDatRegist['usuari'];
        $datos->asunto = "NOVEDAD ESPECIAL";
        $datos->mai_destin = $mMail;
        $datos->div_namexx = HOST_WEB."_faro_01";
        $datos->rutpla = $mDatRegist['rutpla'];
        $datos->nom_config = $mDatDespac['nom_config'];

        if (self::sendMail($datos)){
            return ". Se envió Correos a: ".$mMail . ". Tiempo Ultima Novedad: ".$mDatRegist['tie_ultnov']." Minutos <br>";
        }else{
            return false;
        }
    }

    /* ! \fn: sendMailACargo
     *  \brief: Envia correo A cargo Empresa o A cargo Faro
     *  \author: Ing. Fabian Salinas
     *  \date: 29/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array  Datos de la novedad a registrar
     *  \param: mDatDespac  Array  Datos basicos del despacho
     *  \return: Array
     */

    private function sendMailACargo($mDatRegist = null, $mDatDespac = null, $mDatNoveda = null) {
        $mPueContro = self::getPuestoContro($mDatRegist['contro']);

        if ($mDatRegist['noveda'] == CONS_NOVEDA_ACAEMP) {
            $asunto = "NOVEDAD A Cargo de la Empresa";
            $mMail = $mDatDespac['mai_transp'];
        } else {
            $asunto = "NOVEDAD A Cargo de Faro";
            $mMail = MAIL_SUPERVISORES;
        }

        $datos->nom_noveda = $mDatNoveda['nom_noveda'];
        $datos->num_placax = $mDatDespac['num_placax'];
        $datos->num_despac = $mDatRegist['despac'];
        $datos->nom_transp = $mDatDespac['nom_transp'];
        $datos->cod_manifi = $mDatDespac['cod_manifi'];
        $datos->nom_ciuori = $mDatDespac['nom_ciuori'];
        $datos->nom_ciudes = $mDatDespac['nom_ciudes'];
        $datos->nom_conduc = $mDatDespac['nom_conduc'];
        $datos->con_telmov = $mDatDespac['con_telmov'];
        $datos->fec_noveda = $mDatRegist['fecact'];
        $datos->nom_sitiox = $mDatRegist['sitio'];
        $datos->obs_noveda = $mDatRegist['observ'];
        $datos->ema_asigna = $mDatDespac['mai_transp'];
        $datos->usr_creaci = $mDatRegist['usuari'];
        $datos->asunto = $asunto;
        $datos->mai_destin = $mMail;
        $datos->div_namexx = HOST_WEB."_faro_01";
        $datos->rutpla = $mDatRegist['rutpla'];

        if (self::sendMail($datos)){
            return ". Se envio Correos a: ".$mMail;
        }else{
            return false;
        }
    }

    /* ! \fn: updateSoluciRecome
     *  \brief: Actualiza la recomendación cuando se soluciona
     *  \author: Ing. Fabian Salinas
     *  \date: 29/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array  Datos de la novedad a registrar
     *  \return: Array
     */

    private function updateSoluciRecome($mDatRegist = null) {
        $mSql = "SELECT a.num_condes FROM " . BASE_DATOS . ".tab_recome_asigna a 
                  WHERE a.num_despac = '" . $mDatRegist['despac'] . "' 
                    AND a.cod_contro = '" . $mDatRegist['contro'] . "' 
                    AND a.ind_ejecuc = 0 LIMIT 1 ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mValidRecome = $mConsult->ret_arreglo();

        if ($mValidRecome != NULL) {
            $mSql = "UPDATE " . BASE_DATOS . ".tab_recome_asigna 
                        SET cod_noved2 = '" . $mDatRegist['noveda'] . "', 
                            ind_ejecuc = '1', 
                            usr_ejecut = '" . $mDatRegist['usuari'] . "', 
                            fec_ejecut = NOW(), 
                            usr_modifi = '" . $mDatRegist['usuari'] . "', 
                            fec_modifi = NOW() 
                      WHERE num_despac = '" . $mDatRegist['despac'] . "' 
                        AND cod_contro = '" . $mDatRegist['contro'] . "' ";
            $mConsult = new Consulta($mSql, $this->conexion, "R");

            if ($mConsult->id_resultado == 1)
                $mResult[0] = true;
            else {
                $mResult[0] = false;
                $mResult[1] = "Error actualizando solucion a recomendacion";
            }
        } else {
            $mResult[0] = false;
            $mResult[1] = "No hay Recomendaciones por solucionar en este puesto de control para este despacho";
        }

        return $mResult;
    }
    
    /* ! \fn: insertNovedad
     *  \brief: Inserta los datos de la novedad
     *  \author: Ing. Fabian Salinas
     *  \date: 29/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \param: mDifTieAla  String   Diferencia tiempo de alarma
     *  \param: mCodSitiox  String   Codigo del sitio de seguimiento
     *  \param: mIndEnSiti  Boolean  true = En SITIO; false = Antes de Sitio
     *  \param: mDatUsuari  Array    Datos del usuario
     *  \return: Array
     */

    private function insertNovedad($mDatRegist = null, $mDifTieAla = '0', $mCodSitiox = '94134', $mIndEnSiti = false, $mDatUsuari = null) {
        if ($mIndEnSiti == true) {
            $mSql = " INSERT INTO " . BASE_DATOS . ".tab_despac_noveda 
                                (
                                    num_despac, cod_rutasx,  cod_contro, 
                                    cod_noveda, tiem_duraci, fec_noveda, 
                                    des_noveda, val_retras,  cod_sitiox, 
                                    cod_usrcre, usr_creaci,  fec_creaci 
                                )
                                VALUES 
                                ( 
                                    '" . $mDatRegist['despac'] . "', '" . $mDatRegist['rutax'] . "',  '" . $mDatRegist['contro'] . "', 
                                    '" . $mDatRegist['noveda'] . "', '" . $mDatRegist['tieadi'] . "', '" . $mDatRegist['fecnov'] . "', 
                                    '" . addslashes($mDatRegist['observ']) . "', '" . $mDifTieAla . "', '" . $mCodSitiox . "', 
                                    '" . $mDatUsuari['cod_consec'] . "', '" . $mDatRegist['usuari'] . "', '" . $mDatRegist['fecact'] . "'
                                )";
        } else {
            $mSql = " SELECT MAX(cod_consec) AS maximo 
                        FROM " . BASE_DATOS . ".tab_despac_contro 
                       WHERE num_despac = '" . $mDatRegist['despac'] . "' ";
            $mConsult = new Consulta($mSql, $this->conexion);
            $mMax = $mConsult->ret_arreglo();
            $mMax = $mMax[0] + 1;


            // Parche porque en algunos casos cod_rutasx de despac_vehige es diferente a cod_rutasx despac_seguim y al registrar la novedad, la llave compuesta no es la misma por el cod_rutasx
            // Se actualiza la ruta de despac_vehige a despac_seguim
            $query = "UPDATE  
                             ".BASE_DATOS.".tab_despac_seguim a 
                  INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
                         SET a.cod_rutasx = IF( b.cod_rutasx != a.cod_rutasx, b.cod_rutasx, a.cod_rutasx )
                       WHERE a.num_despac = '" . $mDatRegist['despac'] . "' AND a.cod_rutasx != b.cod_rutasx ";
            $consulta = new Consulta($query, $this->conexion);
      


            $mSql = " INSERT INTO " . BASE_DATOS . ".tab_despac_contro 
                                (
                                    num_despac,  cod_rutasx, cod_contro, 
                                    cod_consec,  obs_contro, cod_noveda, 
                                    tiem_duraci, fec_contro, val_retras, 
                                    cod_sitiox,  ind_sitiox, cod_usrcre, 
                                    usr_creaci,  fec_creaci 
                                )
                                VALUES 
                                (
                                    '" . $mDatRegist['despac'] . "',  '" . $mDatRegist['rutax'] . "', '" . $mDatRegist['contro'] . "', 
                                    '" . $mMax . "',     '" . addslashes($mDatRegist['observ']) . "', '" . $mDatRegist['noveda'] . "', 
                                    '" . $mDatRegist['tieadi'] . "', '" . $mDatRegist['fecnov'] . "', '" . $mDifTieAla . "', 
                                    '" . $mCodSitiox . "',           '" . $mDatRegist['indsit'] . "', '" . $mDatUsuari['cod_consec'] . "', 
                                    '" . $mDatRegist['usuari'] . "', '" . $mDatRegist['fecact'] . "'
                                )";
        }

        $mConsult = new Consulta($mSql, $this->conexion, "R");

        #Actualizo en la tabla tab_despac_seguin el indicador a 0 cuando es una novedad en sitio
        if($mIndEnSiti == true)
        {
            $query = "UPDATE " . BASE_DATOS . ".tab_despac_seguim
                SET ind_estado = '0'
              WHERE num_despac = '" . $mDatRegist['despac'] . "' AND
                    cod_rutasx = '" . $mDatRegist['rutax'] . "' AND
                    cod_contro = '" . $mDatRegist['contro'] . "' ";
            $consulta = new Consulta($query, $this->conexion, "R");
        }

        if ($mConsult->id_resultado == 1) {
            $mResult[0] = true;
            $mResult[1] = $mMax;
        } else {
            $mResult[0] = false;
            $mResult[1] = "Error Inserta los datos de la novedad en " . ( $mIndEnSiti == true ? "tab_despac_noveda." : "tab_despac_contro." );
        }

         //Inserta historial de Novedades a homologar
         setTrackingDespac($this->conexion, $mDatRegist['despac'], null , 2, $mDatRegist['noveda']);

        return $mResult;
    }

    /* ! \fn: updateLlegadAuto
     *  \brief: Actualiza el despacho para dar llegada automatica
     *  \author: Ing. Fabian Salinas
     *  \date:  29/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \return: Array
     */

    private function updateLlegadAuto($mDatRegist = null) {
        $mNextPC = getNextPC($this->conexion, $mDatRegist['despac']);

        if ($mNextPC['cod_contro'] == CONS_CODIGO_PCLLEG) {
            $mSql = "UPDATE " . BASE_DATOS . ".tab_despac_despac 
                        SET fec_llegad = NOW(),
                            obs_llegad = 'Llegada Automatica por el Sistema. USUARIO:" . $mDatRegist['usuari'] . "',
                            usr_modifi = '" . $mDatRegist['usuari'] . "',
                            fec_modifi = NOW() 
                      WHERE num_despac = '" . $mDatRegist['despac'] . "' ";
            $mConsult = new Consulta($mSql, $this->conexion, "R");

            if ($mConsult->id_resultado == 1)
                $mResult[0] = true;
            else {
                $mResult[0] = false;
                $mResult[1] = "Error Actualizacion llegada Automatica del Despacho. Query";
            }
        } elseif (!$mNextPC) {
            $mResult[0] = false;
            $mResult[1] = "Error Actualizacion llegada Automatica del Despacho. No se encontro Siguiente PC.";
        } else
            $mResult[0] = true;

        return $mResult;
    }

    /* ! \fn: updateUltNovedad
     *  \brief: Actualizacion Guarda el codigo de la ultima novedad en tab_despac_despac cod_ultnov
     *  \author: Ing. Fabian Salinas
     *  \date: 29/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \param: mFecManAla  DateTime  Fecha mantiene alarma
     *  \return: Array
     */

    private function updateUltNovedad($mDatRegist = null, $mFecManAla = null) {
        $mSql = "UPDATE " . BASE_DATOS . ".tab_despac_despac 
                    SET fec_ultnov = '" . $mDatRegist['fecnov'] . "',
                        fec_manala = '" . $mFecManAla . "',
                        cod_conult = '" . $mDatRegist['contro'] . "',
                        cod_ultnov = '" . $mDatRegist['noveda'] . "', 
                        " . ($mDatRegist['noveda'] == CONS_NOVEDA_ACAEMP ? " ind_defini = '1', " : "" ) . "
                        " . ($mDatRegist['noveda'] == CONS_NOVEDA_ACAFAR ? " ind_defini = '0', " : "" ) . "
                        usr_ultnov = '" . $mDatRegist['usuari'] . "' 
                  WHERE num_despac = '" . $mDatRegist['despac'] . "' ";
        $mConsult = new Consulta($mSql, $this->conexion, "RC");

        if ($mConsult->id_resultado == 1)
            $mResult[0] = true;
        else {
            $mResult[0] = false;
            $mResult[1] = "Error Actualizacion Guarda el codigo de la ultima novedad en tab_despac_despac cod_ultnov";
        }

        return $mResult;
    }

    /* ! \fn: getPuestoContro
     *  \brief: Trae la Data del puesto de control
     *  \author: Ing. Fabian Salinas
     *  \date: 29/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mCodContro  Integer  Codigo del puesto de control
     *  \return: Array
     */

    private function getPuestoContro($mCodContro = null) {
        $mSql = " SELECT a.nom_contro 
                    FROM " . BASE_DATOS . ".tab_genera_contro a 
                   WHERE a.cod_contro = '$mCodContro' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult = $mConsult->ret_matrix('a');
        return $mResult[0];
    }

    /* ! \fn: updateFecAlarma
     *  \brief: Calcula y actualiza la fecha de alarma
     *  \author: Ing. Fabian Salinas
     *  \date:  04/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \param: mDatNoveda  Array    Datos basicos de la noveadad
     *  \param: mIndEnSiti  Boolean  true = En SITIO; false = Antes de Sitio
     *  \param: mDatTransp  Array    Datos de la transportadora
     *  \return: 
     */

    private function updateFecAlarma($mDatRegist = null, $mDatNoveda = null, $mDatTransp = null, $mIndEnSiti = false) {
        $mRutPendie = self::getRutaPendiente($mDatRegist['despac'], $mDatRegist['contro'], $mIndEnSiti);
        $mDatDespac = self::getDataBasicDespac($mDatRegist['despac']); #Data basica del despacho
        if (sizeof($mRutPendie) < 1 && $mIndEnSiti == true)
            return '0';
        else {
            if ($mIndEnSiti == false && sizeof($mRutPendie) > 0)
                $mCodContrs = $mDatRegist['contro'] . "," . ( join(",", $mRutPendie) );
            elseif ($mIndEnSiti == false)
                $mCodContrs = $mDatRegist['contro'];
            else
                $mCodContrs = join(',', $mRutPendie);

            $mSql = "UPDATE " . BASE_DATOS . ".tab_despac_seguim 
                        SET fec_planea = NOW() 
                      WHERE num_despac = '" . $mDatRegist['despac'] . "' 
                        AND ind_estado != '2' 
                        AND cod_contro IN ( $mCodContrs ) ";
            $mConsult = new Consulta($mSql, $this->conexion, "R");

            #<Calcula variable mTiempo>
            if ($mDatRegist['tieadi'] > 0) {
                $mTiempo = $mDatRegist['tieadi'];
            } elseif (in_array($mDatRegist['noveda'], self::$cCodNoved['0'])) {
                $mTiempo = 150;
            } elseif (in_array($mDatRegist['noveda'], self::$cCodNoved['1'])) {
                if ($mDatDespac['cod_tipdes'] == 4)
                    $mTiempo = 220;
                else
                    $mTiempo = 340;
            }elseif (in_array($mDatRegist['noveda'], self::$cCodNoved['2'])) {
                if ($mDatDespac['cod_tipdes'] == 4)
                    $mTiempo = 220;
                else
                    $mTiempo = 340;
            }elseif (in_array($mDatRegist['noveda'], self::$cCodNoved['6'])) {
                $mTiempo = 210;
            } else {
                $mTiempo = 0;
            }

            #<Calculo 1>
            if ($mIndEnSiti == true && $mTiempo == 0 && ($mDatTransp['cod_tipser'] == 1 || ($mDatTransp['cod_tipser'] == 3 && $mDatTransp['tie_nacion'] == 0 && $mDatTransp['tie_urbano'] == 0))
            ) { #En sitio && sin tiempo adicional && (Empresas con tipo de servicio EAL || MA/EAL sin tiempo parametrizado)
                $mValDuraci = self::getValDuraci($mDatRegist['rutax'], $mDatRegist['contro']);
                $mTiempo = $mValDuraci['val_duraci'];
            } elseif ($mIndEnSiti == false && $mTiempo == 0 && ($mDatTransp['cod_tipser'] == 1 || ($mDatTransp['cod_tipser'] == 3 && $mDatTransp['tie_nacion'] == 0 && $mDatTransp['tie_urbano'] == 0))
            ) { #Antes de sitio && sin tiempo adicional && (Empresas con tipo de servicio EAL || MA/EAL sin tiempo parametrizado)
                $mTiempo = self::$cTieAddSt;
            } elseif ($mDatDespac['cod_tipdes'] == 1 && $mTiempo == 0) { #Empresas con tipo de servicio MA o MA/EAL con tiempo parametrizado (Despacho Urbano)
                $mTiempo = $mDatTransp['tie_urbano'];
            } elseif ($mTiempo == 0) { #Empresas con tipo de servicio MA o MA/EAL con tiempo parametrizado (Despacho Diferente a Urbano)
                $mTiempo = $mDatTransp['tie_nacion'];
            }
            #</Calculo 1>

            if ($mIndEnSiti == false) #Se actualiza el PC en el que se registra la novedad si la novedad es antes de sitio
                self::updateDespacSeguim($mDatRegist['despac'], $mDatRegist['contro'], $mDatRegist['usuari'], $mDatRegist['fecnov'], $mTiempo);

            #<Calculo 2>
            for ($i = 0; $i < sizeof($mRutPendie); $i++) {
                $mValDuraci = self::getValDuraci($mDatRegist['rutax'], $mRutPendie[$i]);

                if ($mIndEnSiti == true && $i == 0)#Si la novedad es en Sitio, Tiempo para actualizar el siguiente PC al que se registra la novedad, calculado en <Calculo 1>
                    $mTiempo = $mTiempo;
                else
                    $mTiempo += $mValDuraci['val_duraci'];

                self::updateDespacSeguim($mDatRegist['despac'], $mValDuraci['cod_contro'], $mDatRegist['usuari'], $mDatRegist['fecnov'], $mTiempo);
            }
            #</Calculo 2>
            #</Calcula variable mTiempo>

            return $mTiempo;
        }
    }

    /* ! \fn: getRutaPendiente
     *  \brief: Trae puestos de control pendientes por reportar novedad
     *  \author: Ing. Fabian Salinas
     *  \date:  30/10/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mNumDespac  Integer  Numero del Despacho
     *  \param: mCodContro  Integer  Codigo del puesto de control
     *  \return: Matriz
     */

    private function getRutaPendiente($mNumDespac = null, $mCodContro = null) {
        $mRutSeguim = getControDespac($this->conexion, $mNumDespac);

        $mBandera = false;
        $mResult = array();
        foreach ($mRutSeguim as $row) {
            if ($mBandera == true)
                $mResult[] = $row['cod_contro'];

            if ($row['cod_contro'] == $mCodContro)
                $mBandera = true;
        }

        return $mResult;
    }

    /* ! \fn: getValDuraci
     *  \brief: Trae el tiempo de duracion entre PC y el anterior
     *  \author: Ing. Fabian Salinas
     *  \date:  03/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mCodRutasx  Integer  Codigo de la ruta
     *  \param: mCodContro  Integer  Codigo del PC
     *  \return: Integer
     */

    private function getValDuraci($mCodRutasx = null, $mCodContro = null) {
        $mSql = " SELECT b.cod_contro, b.val_duraci
                    FROM " . BASE_DATOS . ".tab_genera_rutcon b 
                   WHERE b.cod_rutasx = '$mCodRutasx' 
                     AND b.val_duraci <= (
                                              SELECT a.val_duraci 
                                                FROM " . BASE_DATOS . ".tab_genera_rutcon a 
                                               WHERE a.cod_rutasx = '$mCodRutasx' 
                                                 AND a.cod_contro = '$mCodContro' 
                                                 AND a.ind_estado = '1' 
                                         )
                ORDER BY b.val_duraci DESC 
                   LIMIT 2 ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mValDuraci = $mConsult->ret_matrix('a');

        $mResult['val_duraci'] = $mValDuraci[0]['val_duraci'] - $mValDuraci[1]['val_duraci'];
        $mResult['cod_contro'] = $mValDuraci[0]['cod_contro'];

        return $mResult;
    }

    /* ! \fn: updateDespacSeguim
     *  \brief: Query que actualiza tab_despac_seguim por puesto de control, adiciona tiempo a fec_alarma
     *  \author: Ing. Fabian Salinas
     *  \date: 03/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mNumDespac  Integer  Numero del despacho
     *  \param: mCodContro  Integer  Codigo del puesto de control
     *  \param: mNomUsuari  String   Nombre del usuario
     *  \param: mFecNoveda  DateTime Fecha y hora de la novedad
     *  \param: mAddMinute  Integer  Minutos a adicionar
     *  \return: 
     */

    private function updateDespacSeguim($mNumDespac, $mCodContro, $mNomUsuari, $mFecNoveda, $mAddMinute) {
        $mSql = " UPDATE " . BASE_DATOS . ".tab_despac_seguim 
                     SET fec_planea = DATE_ADD('" . self::$cNow . "', INTERVAL '$mAddMinute' MINUTE), 
                         usr_modifi = '$mNomUsuari', 
                         fec_modifi = NOW() 
                   WHERE num_despac = '$mNumDespac' 
                     AND ind_estado != '2' 
                     AND cod_contro = '$mCodContro' ";
        $mConsult = new Consulta($mSql, $this->conexion, "R");
    }

    /* ! \fn: updateFinDespac
     *  \brief: Actualiza el campo fec_findes de tab_despac_vehige
     *  \author: Ing. Fabian Salinas
     *  \date:  09/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \param: mTieAcumul  Integer  Tiempo acumulado
     *  \return: 
     */

    private function updateFinDespac($mDatRegist = null, $mTieAcumul = null) {
        $mSql = " UPDATE " . BASE_DATOS . ".tab_despac_vehige
                     SET fec_findes = DATE_ADD('" . self::$cNow . "', INTERVAL " . $mTieAcumul . " MINUTE),
                         usr_modifi = '" . $mDatRegist['usuari'] . "',
                         fec_modifi = '" . $mDatRegist['fecact'] . "'
                   WHERE num_despac = " . $mDatRegist['despac'] . " ";
        $mConsult = new Consulta($mSql, $this->conexion, "R");
    }

    /* ! \fn: sendMailCitaEntrega
     *  \brief: Envia correo Cita entrega
     *  \author: Ing. Fabian Salinas
     *  \date: 09/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \param: mDatDespac  Array    Datos basicos del despacho
     *  \param: mDatNoveda  Array    Datos basicos de la noveadad
     *  \param: mTieAcumul  Integer  Tiempo acumulado
     *  \return: 
     */

    private function sendMailCitaEntrega($mDatRegist = null, $mDatDespac = null, $mDatNoveda = null, $mTieAcumul = null) {
        $mSql = "SELECT CONCAT(fec_citdes, ' ', hor_citdes) AS date_citdes 
                   FROM " . BASE_DATOS . ".tab_despac_destin 
                  WHERE num_despac = '" . $mDatRegist['despac'] . "' 
                    AND fec_citdes != '0000-00-00'
                    AND fec_citdes IS NOT NULL 
                    AND hor_citdes != '00:00:00'
                    AND hor_citdes IS NOT NULL 
               ORDER BY 1
                  LIMIT 1";
        $mConsult = new Consulta($mSql, $this->conexion);
        $dat_citdes = $mConsult->ret_matrix('a');

        #<IF 0>
        if (sizeof($dat_citdes) > 0) {
            $time_inicio = strtotime($mDatRegist['fecnov']);
            $time_fin = strtotime($dat_citdes[0]['date_citdes']);

            $dif_minuto = round(( $time_inicio - $time_fin ) / 60);
        }
        #</IF 0>
        #<IF 1>
        if (sizeof($dat_citdes) > 0 && (int) $dif_minuto > 0) {
            $nue_llegpl = date("Y-m-d H:i", strtotime($mDatRegist['fecnov']) + ($mTieAcumul * 60));

            $query = "SELECT a.num_despac, a.cod_manifi, 
                           IF( b.nom_conduc IS NOT NULL, b.nom_conduc, c.abr_tercer) AS abr_tercer, c.cod_tercer,
                           IF( a.con_telmov IS NULL OR a.con_telmov = '', c.num_telmov, a.con_telmov ) AS telmov,
                           IF( a.con_telef1 IS NULL OR a.con_telef1 = '', c.num_telef1, a.con_telef1),ind_defini,
                               e.nom_operad, f.nom_califi, a.tie_contra, a.ind_tiemod, a.obs_tiemod, b.num_placax,
                               b.cod_transp, UPPER( z.abr_tercer ) AS nom_transp,
                               x.nom_tipdes, m.tip_vehicu
                          FROM " . BASE_DATOS . ".tab_despac_despac a 
                     LEFT JOIN " . BASE_DATOS . ".tab_despac_corona m 
                            ON a.num_despac = m.num_dessat,
                               " . BASE_DATOS . ".tab_despac_vehige b,
                               " . BASE_DATOS . ".tab_genera_tipdes x,
                               " . BASE_DATOS . ".tab_tercer_tercer c,
                               " . BASE_DATOS . ".tab_tercer_tercer z,
                               " . BASE_DATOS . ".tab_tercer_conduc d 
                     LEFT JOIN " . BASE_DATOS . ".tab_genera_califi f ON f.cod_califi = d.cod_califi
                     LEFT JOIN " . BASE_DATOS . ".tab_operad_operad e ON e.cod_operad = d.cod_operad
                         WHERE a.num_despac = b.num_despac AND
                               b.cod_transp = z.cod_tercer AND
                               a.cod_tipdes = x.cod_tipdes AND
                               b.cod_conduc = c.cod_tercer AND
                               d.cod_tercer = c.cod_tercer AND
                               a.num_despac = '" . $mDatRegist['despac'] . "' ";
            $consulta = new Consulta($query, $this->conexion);
            $_DATACON = $consulta->ret_matriz();

            $query = "SELECT e.nom_rutasx,a.cod_ciuori,a.cod_ciudes,
                            if(b.fec_llegpl Is Null,'SIN CONFIRMAR',DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y')),
                   DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y')
                          FROM " . BASE_DATOS . ".tab_despac_vehige b,
                               " . BASE_DATOS . ".tab_genera_rutasx e,
                               " . BASE_DATOS . ".tab_genera_agenci g,
                               " . BASE_DATOS . ".tab_despac_despac a  
                         WHERE a.num_despac = b.num_despac AND
                               b.cod_rutasx = e.cod_rutasx AND
                               b.cod_agenci = g.cod_agenci AND
                               a.num_despac = '" . $mDatRegist['despac'] . "' ";
            $consulta = new Consulta($query, $this->conexion);
            $_DATARUT = $consulta->ret_matriz();

            $_FACTUR = "";
            $_CLIENT = "";
            $mSelect = "SELECT num_despac, num_docume, nom_destin
                            FROM " . BASE_DATOS . ".tab_despac_destin
                           WHERE num_despac = '" . $mDatRegist['despac'] . "' 
                           ORDER BY 1";
            $consulta = new Consulta($mSelect, $this->conexion);
            $facturas = $consulta->ret_matriz();

            if (sizeof($facturas) > 0) {
                foreach ($facturas as $fct) {
                    $_FACTUR .= $_FACTUR != '' ? ',' . $fct['num_docume'] : $fct['num_docume'];
                    $_CLIENT .= $_CLIENT != '' ? ',' . $fct['nom_destin'] : $fct['nom_destin'];
                }
            } else {
                $_FACTUR = "N/A";
                $_CLIENT = "N/A";
            }

            $mSelect = "SELECT cod_impact, des_impact, cod_colorx, 
                                 min_ranini, min_ranfin
                            FROM " . BASE_DATOS . ".tab_genera_impact 
                           WHERE cod_transp = '" . $_DATACON[0]['cod_transp'] . "'";
            $consult = new Consulta($mSelect, $this->conexion);
            $rangos = $consult->ret_matrix('a');
            $size_rangos = sizeof($rangos);

            if ($size_rangos) {
                $ran_ini = $rangos[0]['min_ranini'];
                $ran_fin = $rangos[$size_rangos - 1]['min_ranfin'];
                if ($dif_minuto < $ran_ini) {
                    $color = $rangos[0]['cod_colorx'];
                    $namex = $rangos[0]['des_impact'];
                } elseif ($dif_minuto > $ran_fin) {
                    $color = $rangos[$size_rangos - 1]['cod_colorx'];
                    $namex = $rangos[$size_rangos - 1]['des_impact'];
                } else {
                    foreach ($rangos as $row) {
                        if ($dif_minuto <= $row['min_ranfin'] && $dif_minuto >= $row['min_ranini']) {
                            $color = $row['cod_colorx'];
                            $namex = $row['des_impact'];
                        }
                    }
                }
            } else {
                $color = 'FFFFFF';
                $namex = 'NO ASIGNADO';
            }

            $query = "SELECT a.num_despac, a.num_desext, a.num_pedido, 
                               a.num_solici, a.num_consol, a.tip_transp, 
                               b.nom_produc, c.nom_poseed
                          FROM " . BASE_DATOS . ".tab_despac_sisext a 
                     LEFT JOIN " . BASE_DATOS . ".tab_genera_produc b 
                            ON b.cod_produc = a.cod_mercan
                     LEFT JOIN " . BASE_DATOS . ".tab_despac_corona c
                            ON a.num_despac = c.num_dessat
                 WHERE a.num_despac = '" . $mDatRegist['despac'] . "' ";
            $consulta = new Consulta($query, $this->conexion);
            $num_desext = $consulta->ret_matriz();

            $num_consol = $num_desext[0]['num_consol'] != '' ? $num_desext[0]['num_consol'] : 'N/A';
            $nom_produc = $num_desext[0]['nom_produc'] != '' ? $num_desext[0]['nom_produc'] : 'N/A';
            $num_viajex = $num_desext[0]['num_desext'] != '' ? $num_desext[0]['num_desext'] : 'N/A';
            $num_pedido = $num_desext[0]['num_pedido'] != '' ? $num_desext[0]['num_pedido'] : 'N/A';
            $num_solici = $num_desext[0]['num_solici'] != '' ? $num_desext[0]['num_solici'] : 'N/A';

            $info = "<html>";
            $info .= "<head>";
            $info .= "<style>
                        .CellHead
                        {
                          font-family:Trebuchet MS, Verdana, Arial;
                          font-size:20px;
                          background-color: #35650F;
                          color:#FFFFFF;
                          padding: 4px;
                        }
                  
                        .cellInfo1
                        {
                          font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;
                        }
                    
                        .cellInfo2
                        {
                          font-family:Trebuchet MS, Verdana, Arial;
                          font-size:11px;
                          background-color: #DEDFDE;
                          padding: 2px;
                        }
                        </style>";
            $info .= "</head>";
            $info .= "<body>";
            $info .= "<table width='50%' cellpadding='0' cellspacing='0'>";

            /* NUEVO FORMATO ID:151900 */
            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:20px; background-color: #35650F; color:#FFFFFF; padding: 4px;' align='center'><img src='https://".HOST_WEB.".intrared.net/ap/satt_faro/imagenes/logo.gif' height='40' width='90' style='background-color:#FFFFFF;'></td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:20px; background-color: #35650F; color:#FFFFFF; padding: 4px;' align='center'>REPROGRAMACION CITA DE ENTREGA " . $num_viajex . " - " . $_DATACON[0]['num_placax'] . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td colspan='2' style='background-color: #" . $color . "; border-right:1px solid #35650F; border-left:1px solid #35650F;'>&nbsp;</td>";
            $info .= "</tr>";


            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Fecha Programada Cita de Entrega:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $dat_citdes[0]['date_citdes'] . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Nueva Fecha Estimada LLegada:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $nue_llegpl . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Cliente:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $_CLIENT . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Negocio:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $nom_produc . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >No. de Viaje:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $num_viajex . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Placa:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $_DATACON[0]['num_placax'] . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >No Solicitud:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $num_solici . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Factura(s):&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $_FACTUR . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Pedido - Consolidado:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $num_pedido . " - " . $num_consol . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Operaci&oacute;n:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $_DATACON[0]['nom_tipdes'] . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Observaciones:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $mDatRegist['observ'] . "</td>";
            $info .= "</tr>";

            $mSelect = "SELECT nom_noveda FROM " . BASE_DATOS . ".tab_genera_noveda WHERE cod_noveda = '" . $mDatRegist['noveda'] . "'";
            $consulta = new Consulta($mSelect, $this->conexion);
            $nom_noveda = $consulta->ret_matriz();

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Causa:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $nom_noveda[0][0] . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Ruta:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $_DATARUT[0]['nom_rutasx'] . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Conductor:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $_DATACON[0]['cod_tercer'] . " - " . $_DATACON[0]['abr_tercer'] . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Despacho No.:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $mDatRegist['despac'] . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Empresa:&nbsp;&nbsp;&nbsp;</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $_DATACON[0]['nom_transp'] . "</td>";
            $info .= "</tr>";

            $info .= "<tr>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Alerta:</td>";
            $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $namex . "</td>";
            $info .= "</tr>";

            if ($mDatNoveda['ind_insveh'] == '1' && $mDatDespac['cod_tipdes'] == '4') {
                $info .= "<tr>";
                $info .= "<td colspan='2' style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='center' >Se debe realizar Inspecci&oacute;n Antinarc&oacute;ticos</td>";
                $info .= "</tr>";
            }

            $info .= "<tr>";
            $info .= "<td colspan='2' style='background-color: #" . $color . "; border-bottom:1px solid #35650F; border-right:1px solid #35650F; border-left:1px solid #35650F;'>&nbsp;</td>";
            $info .= "</tr>";

            $info .= "</table>";

            $info .= "</body>";
            $info .= "</html>";

            $asunto = "REPROGRAMACION CITA DE ENTREGA " . $num_viajex . " - " . $_DATACON[0]['num_placax'];

            $cabeceras = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $cabeceras .= 'From: ' . MAIL_SUPERVISORES . " \r\n";

            $_EMA = $this->getMailImpact($_DATACON[0]['cod_transp'], $mDatRegist['despac'], BASE_DATOS);
            $_LastPC = $this->getLastsPCRuta($mDatRegist['despac'], BASE_DATOS); #Antepenultimo puesto de control
            if (HOST_WEB == HOST_WEB_PRO) {
                if (count($_EMA) > 0 && $mDatRegist['contro'] != '9999' && $mDatRegist['contro'] != $_LastPC) {
                    mail($_EMA['ema_conpri'], $asunto, '<div name="'.HOST_WEB.'_faro_04">' . $info . '</div>', $cabeceras . 'Cc: ' . $_EMA['ema_otrcon'] . "\r\n");
                }

                if ($mDatRegist['contro'] != '9999') {
                    mail(MAIL_SUPERVISORES, $asunto, '<div name="'.HOST_WEB.'_faro_05">' . $info . '</div>' . "<br><br>ENVIADO A: " . $_EMA['ema_conpri'] . "<br>COPIADO A: " . $_EMA['ema_otrcon'], $cabeceras);
                }
            }
        }
        #</IF 1>
    }

    /* ! \fn: updateSoluciMovil
     *  \brief: SOLUCION DE NOTIFICACIONES DEL APLICATIVO MOVIL
     *  \author: Ing. Fabian Salinas
     *  \date: 09/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \param: mDatNoveda  Array    Datos basicos de la noveadad
     *  \return: 
     */

    private function updateSoluciMovil($mDatRegist = null, $mDatNoveda = null) {
        $mNot = $mDatRegist['noveda'] == '255' || stripos($mDatNoveda['nom_noveda'], 'NICC') !== FALSE ? "" : "NOT";

        $mSql = "SELECT 1 FROM " . BASE_DATOS . ".tab_notify_mobile WHERE num_despac = '" . $mDatRegist['despac'] . "' AND cod_noveda $mNot IN (186, 188)";
        $mConsult = new Consulta($mSql, $this->conexion);
        $not_mobile = $mConsult->ret_matriz();

        if (sizeof($not_mobile) > 0) {
            $mSql = " UPDATE " . BASE_DATOS . ".tab_notify_mobile 
                         SET ind_soluci = '1', 
                             usr_modifi = '" . $_SESSION['datos_usuario']['cod_usuari'] . "', 
                             fec_modifi = NOW()
                       WHERE num_despac = '" . $mDatRegist['despac'] . "'" . $mCondition;
            $mConsult = new Consulta($mSql, $this->conexion, "R");
        }
    }

    /* ! \fn: indProtoc
     *  \brief: Verifica si la novedad genera protocolo
     *  \author: Ing. Fabian Salinas
     *  \date:  30/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mCodTransp  Integer  Codigo de la transportadora
     *  \param: mCodNoveda  Integer  Codigo de la novedad
     *  \return: Boolean
     */

    private function indProtoc($mCodTransp = null, $mCodNoveda = null) {

        $mCodNoveda = $mCodNoveda == '342' ? $_REQUEST["cod_noveda"] : $mCodNoveda;
        $mSql = "SELECT a.ind_notema
                   FROM " . BASE_DATOS . ".tab_noveda_protoc a 
                  WHERE cod_noveda = '$mCodNoveda' 
                    AND a.cod_transp = '$mCodTransp' ";

        $mConsult = new Consulta($mSql, $this->conexion);
        $mResultMail = $mConsult->ret_matrix('i');

        if ($_REQUEST['ind_protoc'] == 'yes' && $mResultMail[0][0] == 1)
            return true;
        else
            return false;
    }

    /* ! \fn: validacionProtocolos
     *  \brief: Valida protocolos y envia correos
     *  \author: Ing. Fabian Salinas
     *  \date: 09/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \param: mDatDespac  Array    Datos basicos del despacho
     *  \param: mDatNoveda  Array    Datos basicos de la noveadad
     *  \param: mIndEnSiti  Boolean  true = En SITIO; false = Antes de Sitio
     *  \param: mNumConsec  Integer  Numero consecutivo tab_despac_contro
     *  \param: mCodSitiox  Integer  Codigo sitio
     *  \return: 
     */

    private function validacionProtocolos($mDatRegist = null, $mDatDespac = null, $mDatNoveda = null, $mIndEnSiti = false, $mNumConsec = null, $mCodSitiox = null) {
        $mCodDespac = $this->getVerifiConso($mDatRegist['despac']);
        $mDataDespac = $this->getDataDespac($mCodDespac, BASE_DATOS, $mDatRegist['despac']);

        $mSql = "SELECT a.num_despac, a.cod_manifi, 
                        IF( b.nom_conduc IS NOT NULL, b.nom_conduc, c.abr_tercer) AS abr_tercer, c.cod_tercer,
                        IF( a.con_telmov IS NULL OR a.con_telmov = '', c.num_telmov, a.con_telmov ) AS telmov,
                        IF( a.con_telef1 IS NULL OR a.con_telef1 = '', c.num_telef1, a.con_telef1),ind_defini,
                        e.nom_operad, f.nom_califi, a.tie_contra, a.ind_tiemod, a.obs_tiemod, b.num_placax,
                        b.cod_transp, UPPER( z.abr_tercer ) AS nom_transp,
                        x.nom_tipdes, m.tip_vehicu
                   FROM " . BASE_DATOS . ".tab_despac_despac a 
              LEFT JOIN " . BASE_DATOS . ".tab_despac_corona m 
                     ON a.num_despac = m.num_dessat,
                        " . BASE_DATOS . ".tab_despac_vehige b,
                        " . BASE_DATOS . ".tab_genera_tipdes x,
                        " . BASE_DATOS . ".tab_tercer_tercer c,
                        " . BASE_DATOS . ".tab_tercer_tercer z,
                        " . BASE_DATOS . ".tab_tercer_conduc d 
              LEFT JOIN " . BASE_DATOS . ".tab_genera_califi f ON f.cod_califi = d.cod_califi
              LEFT JOIN " . BASE_DATOS . ".tab_operad_operad e ON e.cod_operad = d.cod_operad
                  WHERE a.num_despac = b.num_despac AND
                        b.cod_transp = z.cod_tercer AND
                        a.cod_tipdes = x.cod_tipdes AND
                        b.cod_conduc = c.cod_tercer AND
                        d.cod_tercer = c.cod_tercer AND
                        a.num_despac = '" . $mDatRegist['despac'] . "'";
        $mConsult = new Consulta($mSql, $this->conexion);
        $_DATACON = $mConsult->ret_matriz();

        $mSql = "SELECT e.nom_rutasx,a.cod_ciuori,a.cod_ciudes,
                        if(b.fec_llegpl Is Null,'SIN CONFIRMAR',DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y')),
                        DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y')
                   FROM " . BASE_DATOS . ".tab_despac_vehige b,
                        " . BASE_DATOS . ".tab_genera_rutasx e,
                        " . BASE_DATOS . ".tab_genera_agenci g,
                        " . BASE_DATOS . ".tab_despac_despac a  
                  WHERE a.num_despac = b.num_despac AND
                        b.cod_rutasx = e.cod_rutasx AND
                        b.cod_agenci = g.cod_agenci AND
                        a.num_despac = '" . $mDatRegist['despac'] . "'";
        $mConsult = new Consulta($mSql, $this->conexion);
        $_DATARUT = $mConsult->ret_matriz();

        $mSql = "SELECT num_despac, num_docume, nom_destin
                        FROM " . BASE_DATOS . ".tab_despac_destin
                       WHERE num_despac = '" . $mDatRegist['despac'] . "' 
                       ORDER BY 1";
        $mConsult = new Consulta($mSql, $this->conexion);
        $facturas = $mConsult->ret_matriz();

        $_FACTUR = "";
        $_CLIENT = "";
        if (sizeof($facturas) > 0) {
            foreach ($facturas as $fct) {
                $_FACTUR .= $_FACTUR != '' ? ',' . $fct['num_docume'] : $fct['num_docume'];
                $_CLIENT .= $_CLIENT != '' ? ',' . $fct['nom_destin'] : $fct['nom_destin'];
            }
        } else {
            $_FACTUR = "N/A";
            $_CLIENT = "N/A";
        }

        $mSql = "SELECT a.num_despac, a.num_desext, a.num_pedido, 
                        a.num_solici, a.num_consol, a.tip_transp, 
                        b.nom_produc, c.nom_poseed
                   FROM " . BASE_DATOS . ".tab_despac_sisext a 
              LEFT JOIN " . BASE_DATOS . ".tab_genera_produc b 
                     ON b.cod_produc = a.cod_mercan
              LEFT JOIN " . BASE_DATOS . ".tab_despac_corona c
                     ON a.num_despac = c.num_dessat
                  WHERE a.num_despac = '" . $mDatRegist['despac'] . "' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $num_desext = $mConsult->ret_matriz();

        if ($num_desext[0]['tip_transp'] == '1')
            $tip_transp = 'FLOTA PROPIA';
        elseif ($num_desext[0]['tip_transp'] == '2')
            $tip_transp = 'TERCEROS';
        elseif ($num_desext[0]['tip_transp'] == '3')
            $tip_transp = 'EMPRESAS';
        else
            $tip_transp = 'DESCONOCIDO';

        $num_consol = $num_desext[0]['num_consol'] != '' ? $num_desext[0]['num_consol'] : 'N/A';
        $nom_produc = $num_desext[0]['nom_produc'] != '' ? $num_desext[0]['nom_produc'] : 'N/A';
        $num_viajex = $num_desext[0]['num_desext'] != '' ? $num_desext[0]['num_desext'] : 'N/A';
        $num_pedido = $num_desext[0]['num_pedido'] != '' ? $num_desext[0]['num_pedido'] : 'N/A';
        $num_solici = $num_desext[0]['num_solici'] != '' ? $num_desext[0]['num_solici'] : 'N/A';
        $nom_poseed = $num_desext[0]['nom_poseed'] != '' ? $num_desext[0]['nom_poseed'] : 'N/A';
        $tip_vehicu = $_DATACON[0]['tip_vehicu'] != '' ? $_DATACON[0]['tip_vehicu'] : 'N/A';


        $info = "<html>";
        $info .= "<head>";
        $info .= "<style>
                    .CellHead
                    {
                      font-family:Trebuchet MS, Verdana, Arial; font-size:20px; background-color: #35650F; color:#FFFFFF; padding: 4px;
                    }
              
                    .cellInfo1
                    {
                      font-family:Trebuchet MS, Verdana, Arial;
                      font-size:11px;
                      background-color: #EBF8E2;
                      padding: 2px;
                    }
                
                    .cellInfo2
                    {
                      font-family:Trebuchet MS, Verdana, Arial;
                      font-size:11px;
                      background-color: #DEDFDE;
                      padding: 2px;
                    }
                    </style>";
        $info .= "</head>";
        $info .= "<body>";
        $info .= "<table width='50%' cellpadding='0' cellspacing='0'>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:20px; background-color: #35650F; color:#FFFFFF; padding: 4px;' align='center'><img src='https://ap.intrared.net:444/ap/satt_faro/imagenes/logo.gif' height='40' width='90' style='background-color:#FFFFFF;'></td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:20px; background-color: #35650F; color:#FFFFFF; padding: 4px;' align='center'>PROTOCOLO</td>";
        $info .= "</tr>";


        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Fecha y Hora:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . date('Y-m-d H:i:s') . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right'>Despacho No.:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $mDatRegist['despac'] . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >No. de Viaje:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $num_viajex . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Empresa:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $_DATACON[0]['nom_transp'] . "</td>";
        $info .= "</tr>";


        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Tipo Transportadora:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $tip_transp . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Tipo Despacho:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $_DATACON[0]['nom_tipdes'] . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Mercancia:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $nom_produc . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >No. de Pedido:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $num_pedido . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >No. de Solicitud:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $num_solici . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >No. de Consolidado:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $num_consol . "</td>";
        $info .= "</tr>";


        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Factura(s):&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $_FACTUR . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Cliente(s):&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $_CLIENT . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Ruta:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $_DATARUT[0]['nom_rutasx'] . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Placa:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $_DATACON[0]['num_placax'] . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Tipo Vehiculo:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $tip_vehicu . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Conductor:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $_DATACON[0]['cod_tercer'] . " - " . $_DATACON[0]['abr_tercer'] . "</td>";
        $info .= "</tr>";

        $info .= "<tr>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Celular:&nbsp;&nbsp;&nbsp;</td>";
        $info .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $_DATACON[0]['telmov'] . "</td>";
        $info .= "</tr>";

        #<FOR 3.1>
        for ($m = 0; $m < $_REQUEST['tot_protoc_']; $m++) {
            if ($mIndEnSiti == true) {
                $mInsert = "INSERT INTO " . BASE_DATOS . ".tab_despac_pronov
                                    ( num_despac, cod_contro, cod_rutasx, 
                                      cod_noveda, fec_noveda, cod_protoc,
                                      ind_aproba, obs_protoc, usr_creaci,
                                      fec_creaci )
                             VALUES ( '" . $mDatRegist['despac'] . "', '" . $mDatRegist['contro'] . "', '" . $mDatRegist['rutax'] . "',
                                      '" . $mDatRegist['noveda'] . "', '" . $mDatRegist['fecnov'] . "', '" . $_REQUEST['protoc' . $m . '_'] . "',
                                      '" . $_REQUEST['ind_activo_'] . "', '" . $_REQUEST['obs_protoc' . $m . '_'] . "', '" . $_SESSION['datos_usuario']['cod_usuari'] . "',
                                      '" . $mDatRegist['fecact'] . "' ) ";
            } else {
                $mInsert = "INSERT INTO " . BASE_DATOS . ".tab_despac_procon
                                    ( num_despac, cod_contro, cod_rutasx, 
                                      cod_consec, cod_noveda, fec_contro,
                                      cod_protoc, ind_aproba, obs_protoc, 
                                      usr_creaci, 
                                      fec_creaci )
                             VALUES ( '" . $mDatRegist['despac'] . "', '" . $mDatRegist['contro'] . "', '" . $mDatRegist['rutax'] . "', 
                                      '" . $mNumConsec . "',           '" . $mDatRegist["noveda"] . "', '" . $mDatRegist["fecnov"] . "', 
                                      '" . $_REQUEST['protoc' . $m . '_'] . "', '" . $_REQUEST['ind_activo_'] . "', '" . $_REQUEST['obs_protoc' . $m . '_'] . "', 
                                      '" . $_SESSION['datos_usuario']['cod_usuari'] . "',
                                      '" . $mDatRegist["fecact"] . "' ) ON DUPLICATE KEY UPDATE fec_creaci = DATE_ADD( VALUES(fec_creaci), INTERVAL 1 SECOND )";
            }
            $insercion = new Consulta($mInsert, $this->conexion, "R");

            #<IF 3.1.1>
            if ($_REQUEST['ind_activo_'] == 'S') {
                $message = $info;

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Protocolo:&nbsp;&nbsp;&nbsp;</td>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $this->GetNameProtoc($_REQUEST['protoc' . $m . '_'], BASE_DATOS) . "</td>";
                $message .= "</tr>";

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Nombre Poseedor:&nbsp;&nbsp;&nbsp;</td>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $nom_poseed . "</td>";
                $message .= "</tr>";

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Sitio:&nbsp;&nbsp;&nbsp;</td>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $mDatRegist['sitio'] . "</td>";
                $message .= "</tr>";

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Observaci&oacute;n:&nbsp;&nbsp;&nbsp;</td>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $_REQUEST['obs_protoc' . $m . '_'] . "</td>";
                $message .= "</tr>";

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' colspan='4' >El Protocolo fue Asignado al Cliente.</td>";
                $message .= "</tr>";

                if ($mDatNoveda['ind_insveh'] == '1' && $mDatDespac['cod_tipdes'] == '4') {
                    $message .= "<tr>";
                    $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' colspan='4' >Se debe realizar Inspecci&oacute;n Antinarc&oacute;ticos</td>";
                    $message .= "</tr>";
                }

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Asignado Por:&nbsp;&nbsp;&nbsp;</td>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $_SESSION['datos_usuario']['cod_usuari'] . "</td>";
                $message .= "</tr>";

                // $asunto = "PROTOCOLO ASIGNADO";
            } else {
                $message = $info;

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Protocolo:&nbsp;&nbsp;&nbsp;</td>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $this->GetNameProtoc($_REQUEST['protoc' . $m . '_'], BASE_DATOS) . "</td>";
                $message .= "</tr>";

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Nombre Poseedor:&nbsp;&nbsp;&nbsp;</td>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $nom_poseed . "</td>";
                $message .= "</tr>";

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='right' >Sitio:&nbsp;&nbsp;&nbsp;</td>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='left' >" . $mDatRegist['sitio'] . "</td>";
                $message .= "</tr>";

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' >Observaci&oacute;n:&nbsp;&nbsp;&nbsp;</td>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' >" . $_REQUEST['obs_protoc' . $m . '_'] . "</td>";
                $message .= "</tr>";

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #EBF8E2; padding: 2px;' align='center' colspan='4' style='border-left:1px solid #35650F; border-right:1px solid #35650F;'><b>EL PROTOCOLO FUE EJECUTADO.</b></td>";
                $message .= "</tr>";

                if ($mDatNoveda['ind_insveh'] == '1' && $mDatDespac['cod_tipdes'] == '4') {
                    $message .= "<tr>";
                    $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' colspan='4' >Se debe realizar Inspecci&oacute;n Antinarc&oacute;ticos</td>";
                    $message .= "</tr>";
                }

                $message .= "<tr>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='right' style=' border-bottom:1px solid #35650F; border-left:1px solid #35650F;'>Ejecutado Por:&nbsp;&nbsp;&nbsp;</td>";
                $message .= "<td style='font-family:Trebuchet MS, Verdana, Arial; font-size:11px; background-color: #DEDFDE; padding: 2px;' align='left' style='border-bottom:1px solid #35650F; border-right:1px solid #35650F;'>" . $_SESSION['datos_usuario']['cod_usuari'] . "</td>";
                $message .= "</tr>";

                // $asunto = "PROTOCOLO EJECUTADO";
            }
            #</IF 3.1.1>

            $message .= "</table>";
            $message .= "</body>";
            $message .= "</html>";

            /* NUEVO ASUNTO *************************************** */
            $mSelect = "SELECT nom_noveda FROM " . BASE_DATOS . ".tab_genera_noveda WHERE cod_noveda = '" . $mDatRegist['noveda'] . "'";
            $consulta = new Consulta($mSelect, $this->conexion);
            $nom_noveda = $consulta->ret_matriz();
            $asunto = "Viaje - " . $num_viajex . " - " . $nom_noveda[0][0] . " – Placa " . $_DATACON[0]['num_placax'];
            /*             * **************************************************** */


            $cabeceras = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $cabeceras .= 'From: ' . MAIL_SUPERVISORES . "\r\n";


            $_CENOPE = $this->getMailProtoc($_REQUEST['protoc' . $m . '_'], $mDatRegist['despac'], $mDatRegist['noveda'], BASE_DATOS);

            /* NUEVO ASUNTO *************************************** */
            $mSelect = "SELECT nom_noveda 
                          FROM " . BASE_DATOS . ".tab_genera_noveda 
                         WHERE cod_noveda = '" . $mDatRegist['noveda'] . "'";
            $consulta = new Consulta($mSelect, $this->conexion);
            $nom_noveda = $consulta->ret_matriz();

            $mDataDespac['nom_noveda'] = $nom_noveda[0][0];
            $mAsunto = "Viaje - " . $mDataDespac['num_viajex'] . " - " . $mDataDespac['nom_noveda'] . " – Placa " . $mDataDespac['num_placax'];
            /*             * **************************************************** */

            $mSelect = "SELECT nom_usuari 
                          FROM " . BASE_DATOS . ".tab_genera_usuari 
                         WHERE usr_emailx = '" . $_CENOPE['ema_conprX'] . "' ";
            $consulta = new Consulta($mSelect, $this->conexion);
            $nom_asigna = $consulta->ret_matriz();

            /* DATOS COMPLEMENTARIOS ******************************** */
            $mDataDespac['not_especi'] = 'NINGUNA';
            if ($mDatNoveda['ind_insveh'] == '1' && $mDatDespac['cod_tipdes'] == '4') {
                $mDataDespac['not_especi'] = 'SE DEBE REALIZAR INSPECCI&Oacute;N ANTINARC&Oacute;TICOS';
            }
            $mDataDespac['nom_sitiox'] = $mDatRegist['sitio'];
            $mDataDespac['obs_noveda'] = $mDatRegist['observ'];
            $mDataDespac['fec_noveda'] = $mDatRegist['fecnov'];
            $mDataDespac['nom_conasi'] = $_SESSION['datos_usuario']['nom_usuari'];
            $mDataDespac['usr_conasi'] = $_SESSION['datos_usuario']['cod_usuari'];
            $mDataDespac['ema_asigna'] = $_CENOPE['ema_conprX'];
            $mDataDespac['nom_asigna'] = $nom_asigna[0]['nom_usuari'];
            /*             * ****************************************************** */

            #<IF 3.2>
            /*if($_SESSION['datos_usuario']['cod_usuari'] == 'Adriana.poveda'){
                mail('nelson.liberato@eltransporte.org', 'CENOPE 1', var_export( $_CENOPE, true));
            }*/
                
            if (sizeof($_CENOPE) > 0) {
                $comp = "<br>ENVIADO A: " . ( $_CENOPE['ema_conprX'] != '' ? $_CENOPE['ema_conprX'] : 'N/A' ) . "<br>COPIADO A: " . ( $_CENOPE['ema_otrcoX'] != '' ? $_CENOPE['ema_otrcoX'] : 'N/A' ) . " ";

                if ($mIndEnSiti == true) {
                    $mUpdate = "UPDATE " . BASE_DATOS . ".tab_despac_noveda
                                 SET des_noveda = CONCAT(des_noveda,'. ', '" . $comp . "' ) 
                               WHERE num_despac = '" . $mDatRegist['despac'] . "'
                                 AND cod_contro = '" . $mDatRegist['contro'] . "'
                                 AND cod_rutasx = '" . $mDatRegist['rutax'] . "' 
                                 AND cod_noveda = '" . $mDatRegist['noveda'] . "'
                                 AND fec_noveda = '" . $mDatRegist['fecnov'] . "' ";
                } else {
                    $mUpdate = "UPDATE " . BASE_DATOS . ".tab_despac_contro
                             SET obs_contro = CONCAT(obs_contro,'. ', '" . $comp . "' ) 
                           WHERE num_despac = '" . $mDatRegist["despac"] . "'
                             AND cod_contro = '" . $mDatRegist["contro"] . "'
                             AND cod_rutasx = '" . $mDatRegist['rutax'] . "'
                             AND cod_consec = '" . $mNumConsec . "'
                             AND cod_noveda = '" . $mDatRegist["noveda"] . "'
                             AND fec_contro = '" . $mDatRegist["fecnov"] . "' ";
                }
                $insercion = new Consulta($mUpdate, $this->conexion, "R");

                if ($mDatRegist['noveda'] != '242') {
                    $ema_usu = explode(',', $_CENOPE['ema_conprX']);

                    $mSelect = "SELECT cod_usuari
                              FROM " . BASE_DATOS . ".tab_genera_usuari 
                             WHERE usr_emailx = '" . trim($ema_usu[0]) . "' ";
                    $consulta = new Consulta($mSelect, $this->conexion);
                    $mAsigna = $consulta->ret_matriz();

                    $mSelect = "SELECT MAX(num_consec) 
                              FROM " . BASE_DATOS . ".tab_protoc_asigna 
                             WHERE num_despac = '" . $mDatRegist['despac'] . "' ";
                    $consulta = new Consulta($mSelect, $this->conexion);
                    $mConsec = $consulta->ret_matriz();

                    /*if($_SESSION['datos_usuario']['cod_usuari'] == 'Adriana.poveda'){
                        mail('nelson.liberato@eltransporte.org', 'CENOPE 2', "mAsigna: ".var_export( $mAsigna, true)."\n_REQUEST: ".var_export($_REQUEST, true) );
                    }*/

                    if (sizeof($mAsigna) > 0 && $_REQUEST['ind_activo_'] == 'S') {
                        if ($mIndEnSiti == true) {
                            $mInsert = "INSERT INTO " . BASE_DATOS . ".tab_protoc_asigna
                                            ( num_consec, num_despac, cod_contro, 
                                              cod_rutasx, cod_noveda, fec_noveda, 
                                              obs_noveda, des_htmlxx, usr_asigna,
                                              ind_ejecuc
                                            )
                                      VALUES( '" . ( $mConsec[0][0] + 1 ) . "', '" . $mDatRegist['despac'] . "', '" . $mDatRegist['contro'] . "',
                                              '" . $mDatRegist['rutax'] . "', '" . $mDatRegist['noveda'] . "', '" . $mDatRegist['fecnov'] . "',
                                              '" . addslashes($mDatRegist['observ']) . "', '" . addslashes($message) . "', '" . $mAsigna[0][0] . "',
                                              '0'
                                            )";
                        } else {
                            $mInsert = "INSERT INTO " . BASE_DATOS . ".tab_protoc_asigna
                                            ( num_consec, num_despac, cod_contro, 
                                              cod_rutasx, cod_consec, cod_noveda, 
                                              cod_sitiox, fec_noveda, obs_noveda, 
                                              des_htmlxx, usr_asigna, ind_ejecuc
                                            )
                                      VALUES( '" . ( $mConsec[0][0] + 1 ) . "', '" . $mDatRegist["despac"] . "', '" . $mDatRegist["contro"] . "',
                                              '" . $mDatRegist['rutax'] . "', '" . $mNumConsec . "', '" . $mDatRegist["noveda"] . "', 
                                              '" . $mCodSitiox . "', '" . $mDatRegist["fecnov"] . "','" . addslashes($mDatRegist["observ"]) . "', 
                                              '" . addslashes($message) . "', '" . $mAsigna[0][0] . "',
                                              '0'
                                            )";
                        }

                        /*if($_SESSION['datos_usuario']['cod_usuari'] == 'Adriana.poveda'){
                            mail('nelson.liberato@eltransporte.org', 'CENOPE 3', $mInsert );
                        }*/

                        $insercion = new Consulta($mInsert, $this->conexion, "R");
                    }
                }


                /* inicio correos segun las empresas protocolos**************************************** */
                $plantilla = "pla_notifi_corona.html";

                $mResult = array();

                $mCabece = 'MIME-Version: 1.0' . "\r\n";
                $mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                $mCabece .= 'From: Centro Logistico FARO <no-reply@eltransporte.org>' . "\r\n";


                if ($mDatRegist['nittra'] != self::$cLisTrans['Corona']) {
                    $plantilla = "pla_notifi_cliente.html";

                    $mCabece = 'MIME-Version: 1.0' . "\r\n";
                    $mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                    $mCabece .= 'From: Centro Logistico FARO ' . "\r\n";

                    $mSql = "SELECT ind_notage
                                 FROM tab_transp_tipser
                                WHERE cod_transp = '" . $mDatRegist['nittra'] . "' AND
                                    num_consec = (SELECT MAX(num_consec) FROM tab_transp_tipser WHERE cod_transp = '" . $mDatRegist['nittra'] . "')";
                    $consulta = new Consulta($mSql, $this->conexion);
                    $ind_notage = $consulta->ret_matriz();

                    if ($ind_notage[0][0] == 1) {
                        $mSql = "SELECT a.cod_agenci 
                                   FROM " . BASE_DATOS . ".tab_despac_vehige a
                                  WHERE a.num_despac = '" . $mDatRegist['despac'] . "' ";
                        $consulta = new Consulta($mSql, $this->conexion, "R");
                        $cod_agenci = $consulta->ret_matriz();
                        $cod_agenci = $cod_agenci[0]['cod_agenci'];

                        $mSql = "SELECT a.dir_correo 
                                       FROM " . BASE_DATOS . ".tab_contac_protoc a 
                                      WHERE a.cod_transp = '" . $mDatRegist['nittra'] . "'
                                        AND a.cod_agenci = '" . $cod_agenci . "'";
                        $consulta = new Consulta($mSql, $this->conexion, "R");
                        $mResult = $consulta->ret_matrix("i");
                    } else {
                        $mSql = "SELECT a.dir_correo 
                                 FROM " . BASE_DATOS . ".tab_contac_protoc a 
                                WHERE a.cod_transp = '" . $mDatRegist['nittra'] . "' ";
                        $consulta = new Consulta($mSql, $this->conexion, "R");
                        $mResult = $consulta->ret_matrix("i");
                    }

                    $mCorreos = array();

                    foreach ($mResult as $row) {
                        $mCorreos = array_merge($mCorreos, $row);
                    }

                    $_CENOPE['ema_conprX'] = join(", ", $mCorreos);
                    $_CENOPE['ema_otrcoX'] = join(", ", $mCorreos);
                }
                /* fin correos segun las empresas protocolos******************************************* */


                /* VARIABLES ******************************************** */
                $num_despac = $mDatRegist['despac'];
                $nom_tipdes = $mDataDespac['nom_tipdes'];
                $cod_conduc = $mDataDespac['cod_conduc'];
                $nom_conduc = $mDataDespac['nom_conduc'];
                $cel_conduc = $mDataDespac['cel_conduc'];
                $tip_vehicu = $mDataDespac['tip_vehicu'];
                $nom_empres = $mDataDespac['nom_empres'];
                $num_placax = $mDataDespac['num_placax'];
                $nom_rutaxx = $mDataDespac['nom_rutaxx'];
                $nom_origen = $mDataDespac['nom_origen'];
                $nom_destin = $mDataDespac['nom_destin'];
                $cod_factur = $mDataDespac['cod_factur'];
                $nom_client = $mDataDespac['nom_client'];
                $num_viajex = $mDataDespac['num_viajex'];
                $nom_mercan = $mDataDespac['nom_mercan'];
                $num_pedido = $mDataDespac['num_pedido'];
                $num_solici = $mDataDespac['num_solici'];
                $nom_poseed = $mDataDespac['nom_poseed'];
                $nom_tiptra = $mDataDespac['nom_tiptra'];
                $not_especi = $mDataDespac['not_especi'];
                $nom_noveda = $mDataDespac['nom_noveda'];
                $nom_sitiox = $mDataDespac['nom_sitiox'];
                $obs_noveda = $mDataDespac['obs_noveda'];
                $fec_noveda = $mDataDespac['fec_noveda'];
                $nom_conasi = $mDataDespac['nom_conasi'];
                $usr_conasi = $mDataDespac['usr_conasi'];
                $ema_asigna = $mDataDespac['ema_asigna'];
                $nom_asigna = $mDataDespac['nom_asigna'];
                /* FIN VARIABLES****************************************** */

                /* INICIO ENVIO DE CORREOS************************************** */
                $temporal = getcwd();
                # Habilitar en produccion
                if ($temporal == '/var/www/html/ap/satt_standa/despac')
                    $tmpl_file = '../planti/' . $plantilla;
                else
                    $tmpl_file = '../' . DIR_APLICA_CENTRAL . '/planti/' . $plantilla;

                if(!file_exists($tmpl_file)){
                    $tmpl_file = '/var/www/html/ap/'.DIR_APLICA_CENTRAL.'/planti/' . $plantilla;
                }
                $thefile = implode("", file($tmpl_file));
                $thefile = addslashes($thefile);
                $thefile = "\$r_file=\"" . $thefile . "\";";
                eval($thefile);
                $mHtmlxx = $r_file;
                #if (HOST_WEB == HOST_WEB_PRO) {
                    mail($_CENOPE['ema_conprX'], $mAsunto, '<div name="'.HOST_WEB.'_faro_06">' . $mHtmlxx . '</div>', $mCabece . 'Cc: ' . $_CENOPE['ema_otrcoX'] . "\r\n");
                    mail(MAIL_SUPERVISORES.", maribel.garcia@eltransporte.org", $mAsunto . " - Validacion Correo PARA ", '<div name="'.HOST_WEB.'_faro_07">' . $mHtmlxx . '</div>' . "<br><br>ENVIADO A: " . $_CENOPE['ema_conprX'] . "<br>COPIADO A: " . $_CENOPE['ema_otrcoX'], $mCabece);
                #}
            }
            #</IF 3.2>
        }
        #</FOR 3.1>
    }

    /* ! \fn: soluciNovedad
     *  \brief: Solucion a novedad
     *  \author: Ing. Fabian Salinas
     *  \date: 09/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \param: mDatDespac  Array    Datos basicos del despacho
     *  \param: mDatNoveda  Array    Datos basicos de la noveadad
     *  \return: 
     */

    private function soluciNovedad($mDatRegist = null, $mDatDespac = null, $mDatNoveda = null) {
        $usuari = $_SESSION['datos_usuario']['cod_usuari'];

        #<FOREACH 1>
        foreach (explode('/-/', $_REQUEST['ind_resolu']) as $row) {
            $data = explode('|', $row);

            $mUpdate = "UPDATE " . BASE_DATOS . ".tab_protoc_asigna
                           SET ind_ejecuc = '1', 
                               cod_contr2 = '" . $mDatRegist['contro'] . "', 
                               cod_rutas2 = '" . $mDatRegist['rutax'] . "', 
                               cod_noved2 = '" . $mDatRegist['noveda'] . "', 
                               fec_noved2 = '" . $mDatRegist['fecnov'] . "', 
                               obs_noved2 = '" . addslashes($mDatRegist['observ']) . "',
                               usr_ejecut = '" . $usuari . "'
                         WHERE num_despac = '" . $mDatRegist['despac'] . "'
                           /* AND usr_ejecut = '" . $usuari . "' */
                           AND num_consec = '" . $data[0] . "'
                           AND cod_contro = '" . $data[1] . "'
                           AND cod_noveda = '" . $data[2] . "'
                           ";
            $insercion = new Consulta($mUpdate, $this->conexion, "R");
        }
        #</FOREACH 1>

        $mSelect = "SELECT usr_emailx
                      FROM " . BASE_DATOS . ".tab_despac_noveda a,
                           " . BASE_DATOS . ".tab_genera_usuari b                         
                     WHERE a.usr_creaci  = b.cod_usuari 
                       AND a.num_despac = '" . $mDatRegist['despac'] . "' 
                       AND b.cod_perfil = '719' 
                UNION 
                    SELECT usr_emailx
                      FROM " . BASE_DATOS . ".tab_despac_contro a,
                           " . BASE_DATOS . ".tab_genera_usuari b                         
                     WHERE a.usr_creaci  = b.cod_usuari 
                       AND a.num_despac = '" . $mDatRegist['despac'] . "' 
                       AND b.cod_perfil = '719'
                  GROUP BY 1";
        $consulta = new Consulta($mSelect, $this->conexion);
        $MAIL_NOT = $consulta->ret_matriz();

        #<IF 2>
        if (sizeof($MAIL_NOT) > 0) {
            $query = "SELECT a.num_despac, a.cod_manifi, 
                         IF( b.nom_conduc IS NOT NULL, b.nom_conduc, c.abr_tercer) AS abr_tercer, c.cod_tercer,
                         IF( a.con_telmov IS NULL OR a.con_telmov = '', c.num_telmov, a.con_telmov ) AS telmov,
                         IF( a.con_telef1 IS NULL OR a.con_telef1 = '', c.num_telef1, a.con_telef1),ind_defini,
                             e.nom_operad, f.nom_califi, a.tie_contra, a.ind_tiemod, a.obs_tiemod, b.num_placax,
                             b.cod_transp, UPPER( z.abr_tercer ) AS nom_transp,
                             x.nom_tipdes, m.tip_vehicu
                        FROM " . BASE_DATOS . ".tab_despac_despac a 
                   LEFT JOIN " . BASE_DATOS . ".tab_despac_corona m 
                          ON a.num_despac = m.num_dessat,
                             " . BASE_DATOS . ".tab_despac_vehige b,
                             " . BASE_DATOS . ".tab_genera_tipdes x,
                             " . BASE_DATOS . ".tab_tercer_tercer c,
                             " . BASE_DATOS . ".tab_tercer_tercer z,
                             " . BASE_DATOS . ".tab_tercer_conduc d 
                   LEFT JOIN " . BASE_DATOS . ".tab_genera_califi f ON f.cod_califi = d.cod_califi
                   LEFT JOIN " . BASE_DATOS . ".tab_operad_operad e ON e.cod_operad = d.cod_operad
                       WHERE a.num_despac = b.num_despac AND
                             b.cod_transp = z.cod_tercer AND
                             a.cod_tipdes = x.cod_tipdes AND
                             b.cod_conduc = c.cod_tercer AND
                             d.cod_tercer = c.cod_tercer AND
                             a.num_despac = '" . $mDatRegist['despac'] . "' ";
            $consulta = new Consulta($query, $this->conexion);
            $_DATACON = $consulta->ret_matriz();

            $query = "SELECT e.nom_rutasx,a.cod_ciuori,a.cod_ciudes,
                             if(b.fec_llegpl Is Null,'SIN CONFIRMAR',DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y')),
                             DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y')
                        FROM " . BASE_DATOS . ".tab_despac_vehige b,
                             " . BASE_DATOS . ".tab_genera_rutasx e,
                             " . BASE_DATOS . ".tab_genera_agenci g,
                             " . BASE_DATOS . ".tab_despac_despac a  
                       WHERE a.num_despac = b.num_despac AND
                             b.cod_rutasx = e.cod_rutasx AND
                             b.cod_agenci = g.cod_agenci AND
                             a.num_despac = '" . $mDatRegist['despac'] . "' ";
            $consulta = new Consulta($query, $this->conexion);
            $_DATARUT = $consulta->ret_matriz();

            $_FACTUR = "";
            $_CLIENT = "";
            $mSelect = "SELECT num_despac, num_docume, nom_destin
                          FROM " . BASE_DATOS . ".tab_despac_destin
                         WHERE num_despac = '" . $mDatRegist['despac'] . "' 
                         ORDER BY 1";
            $consulta = new Consulta($mSelect, $this->conexion);
            $facturas = $consulta->ret_matriz();

            if (sizeof($facturas) > 0) {
                foreach ($facturas as $fct) {
                    $_FACTUR .= $_FACTUR != '' ? ',' . $fct['num_docume'] : $fct['num_docume'];
                    $_CLIENT .= $_CLIENT != '' ? ',' . $fct['nom_destin'] : $fct['nom_destin'];
                }
            } else {
                $_FACTUR = "N/A";
                $_CLIENT = "N/A";
            }

            $query = "SELECT a.num_despac, a.num_desext, a.num_pedido, 
                             a.num_solici, a.num_consol, a.tip_transp, 
                             b.nom_produc, c.nom_poseed
                        FROM " . BASE_DATOS . ".tab_despac_sisext a 
                   LEFT JOIN " . BASE_DATOS . ".tab_genera_produc b 
                          ON b.cod_produc = a.cod_mercan
                   LEFT JOIN " . BASE_DATOS . ".tab_despac_corona c
                          ON a.num_despac = c.num_dessat
               WHERE a.num_despac = '" . $mDatRegist['despac'] . "' ";
            $consulta = new Consulta($query, $this->conexion);
            $num_desext = $consulta->ret_matriz();

            if ($num_desext[0]['tip_transp'] == '1')
                $tip_transp = 'FLOTA PROPIA';
            elseif ($num_desext[0]['tip_transp'] == '2')
                $tip_transp = 'TERCEROS';
            elseif ($num_desext[0]['tip_transp'] == '3')
                $tip_transp = 'EMPRESAS';
            else
                $tip_transp = 'DESCONOCIDO';

            $num_consol = $num_desext[0]['num_consol'] != '' ? $num_desext[0]['num_consol'] : 'N/A';
            $nom_produc = $num_desext[0]['nom_produc'] != '' ? $num_desext[0]['nom_produc'] : 'N/A';
            $num_viajex = $num_desext[0]['num_desext'] != '' ? $num_desext[0]['num_desext'] : 'N/A';
            $num_pedido = $num_desext[0]['num_pedido'] != '' ? $num_desext[0]['num_pedido'] : 'N/A';
            $num_solici = $num_desext[0]['num_solici'] != '' ? $num_desext[0]['num_solici'] : 'N/A';
            $nom_poseed = $num_desext[0]['nom_poseed'] != '' ? $num_desext[0]['nom_poseed'] : 'N/A';
            $tip_vehicu = $_DATACON[0]['tip_vehicu'] != '' ? $_DATACON[0]['tip_vehicu'] : 'N/A';
            $fec_actual = date('Y-m-d H:i:s');
            $nom_transp = $_DATACON[0]['nom_transp'];
            $nom_tipdes = $_DATACON[0]['nom_tipdes'];
            $nom_rutasx = $_DATARUT[0]['nom_rutasx'];
            $num_placax = $_DATACON[0]['num_placax'];
            $dat_conduc = $_DATACON[0]['cod_tercer'] . ' - ' . $_DATACON[0]['abr_tercer'];
            $cel_conduc = $_DATACON[0]['telmov'];
            $nom_usuari = $_SESSION['datos_usuario']['cod_usuari'];


            if ($mDatNoveda['ind_insveh'] == '1' && $mDatDespac['cod_tipdes'] == '4') {
                $mHtml = "<tr>";
                $mHtml .= "<td class='cellInfo2' align='left' colspan='4' >Se debe realizar Inspecci&oacute;n Antinarc&oacute;ticos</td>";
                $mHtml .= "</tr>";
            } else
                $mHtml = "";

            /* NUEVO ASUNTO *************************************** */
            $mSelect = "SELECT nom_noveda FROM " . BASE_DATOS . ".tab_genera_noveda WHERE cod_noveda = '" . $mDatRegist['noveda'] . "'";
            $consulta = new Consulta($mSelect, $this->conexion);
            $nom_noveda = $consulta->ret_matriz();
            $mAsunto = "Viaje - " . $num_viajex . " - " . $nom_noveda[0][0] . " – Placa " . $_DATACON[0]['num_placax'];
            /*             * **************************************************** */


            $mCabece = 'MIME-Version: 1.0' . "\r\n";
            $mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $mCabece .= 'From: Centro Logistico FARO <no-reply@eltransporte.org>' . "\r\n";
            #$mCabece .= 'From: '.MAIL_SUPERVISORES."\r\n";

            $plantilla = 'pla_notifi_solnov.html';


            /* INICIO ENVIO DE CORREOS************************************** */
            $temporal = getcwd();
            # Habilitar en produccion
            if ($temporal == '/var/www/html/ap/satt_standa/despac') {
                $tmpl_file = '../planti/' . $plantilla;
                $hola = "1";
            } else {
                $tmpl_file = '../' . DIR_APLICA_CENTRAL . '/planti/' . $plantilla;
                $hola = "2";
            }

            $thefile = implode("", file($tmpl_file));
            $thefile = addslashes($thefile);
            $thefile = "\$r_file=\"" . $thefile . "\";";
            eval($thefile);
            $mHtmlxx = $r_file;
            if (HOST_WEB == HOST_WEB_PRO) {
                mail(MAIL_SUPERVISORES, $mAsunto, '<div name="'.HOST_WEB.'_faro_00">' . $mHtmlxx . '</div>', $mCabece);

                foreach ($MAIL_NOT as $row) {
                    mail($row['usr_emailx'], $mAsunto, '<div name="'.HOST_WEB.'_faro_08">' . $mHtmlxx . '</div>', $mCabece);
                }
            }
        }
        #</IF 2>
    }

    /* ! \fn: soaPs
     *  \brief: Administrador soap y correos 
     *  \author: Ing. Fabian Salinas
     *  \date:  11/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: regist   Array    Datos de la novedad a registrar
     *  \param: ominter  Integer  
     *  \return: 
     */

    private function soaPs($regist = null, $ominter = null, $mDatDespac = null, $mDatNoveda = null, $mIndEnSiti = false) {
        $mCabeceSoporte = 'MIME-Version: 1.0' . "\r\n";
        $mCabeceSoporte .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $mCabeceSoporte .= 'From: ' . MAIL_SOPORTE;

        #<IF 5>
        if ($ominter != 2 && $regist['wsdl'] != "NO") {
            //Manejo de la Interfaz Aplicaciones SAT
            $interfaz = new Interfaz_SAT(BASE_DATOS, $regist['nittra'], $regist['usuari'], $this->conexion);

            #<IF 5.1.1>
            if ($interfaz->totalact > 0) {
                if ($mIndEnSiti == true)
                    $mOption = array('setNovedadPC', 'novedadPC', 'pc', 'PC');
                else
                    $mOption = array('setNovedadNC', 'novedadNC', 'nc', 'NC');

                $nit_empres = $regist['nittra']; //NIT EMPRESA.
                $mData = self::getDataSoap($regist);

                #<FOR 5.1.1.1>
                for ($i = 0; $i < $interfaz->totalact; $i++) {
                    #<IF 5.1.1.1.1>
                    if (10 == $interfaz->interfaz[$i]['operad'] && $mIndEnSiti == true) {
                        /*                         * ******************* TRATAMIENTO SOAP ******************** */
                        ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
                        try {
                            //Ruta Web Service Alianza e-com.
                            $url_webser = "http://190.144.2.98:9090/alianza/site_priv/webservice_oet/servidor2.php?wsdl";

                            $mFecha[0] = date('Y-m-d', strtotime($regist['fecnov']));
                            $mFecha[1] = date('H:i', strtotime($regist['fecnov']));

                            $parametros = array("usuario" => $interfaz->interfaz[$i]['usuari'],
                                "password" => $interfaz->interfaz[$i]['passwo'],
                                "placa" => $mDatDespac['num_placax'],
                                "manifiesto" => $mDatDespac['cod_manifi'],
                                "id_puestocontrol" => $regist['contro'],
                                "id_novedad" => $regist['noveda'],
                                "fecha" => $mFecha[0],
                                "hora" => $mFecha[1],
                                "observacion" => 'Interfaz - ' . $regist['observ'],
                                "cod_empresa" => $mData['cod_alianz']
                            );

                            $oSoapClient = new soapclient($url_webser, array('encoding' => 'ISO-8859-1'));

                            //Métodos disponibles en el WS
                            $respuesta = $oSoapClient->__call('getDespacho', $parametros);
                        } catch (SoapFault $e) {
                            $error_ = $e->getMessage();

                            if ($e->faultcode != '2' && $e->faultcode != '1' && $e->faultstring != 'NINGUNO') {
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist['nittra'] . " <br>";
                                $mMessage .= "Codigo alianza: " . $mData['cod_alianz'] . " <br>";
                                $mMessage .= "Numero de manifiesto: " . $mDatDespac['cod_manifi'] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDatDespac['num_placax'] . " <br>";
                                $mMessage .= "Codigo puesto de control: " . $regist['contro'] . " <br>";
                                $mMessage .= "Codigo novedad: " . $regist['noveda'] . " <br>";
                                $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist['observ'] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Codigo de error: " . $e->faultcode . " <br>";
                                $mMessage .= "Actor del error: " . $e->faultactor . " <br>";
                                $mMessage .= "Mesaje de error: " . $e->faultstring . " <br>";
                                $mMessage .= "Observaciones: " . $e->detail . " <br>";

                                mail(MAIL_SUPERVISORES . ", " . MAIL_SOPORTE, "Web service Alianza", '<div name="'.HOST_WEB.'_faro_09">' . $mMessage . '</div>', $mCabeceSoporte);
                            }
                        }
                    }
                    #</IF 5.1.1.1.1>
                    #<IF 5.1.1.1.2>
                    if (12 == $interfaz->interfaz[$i]['operad'] && $mIndEnSiti == true) {//Colocar
                        $respuesta = $oSoapClient->call("getDespacho", $parametros);

                        /*                         * ****************** TRATAMIENTO SOAP ******************** */
                        ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
                        try {
                            //Ruta Web Service Colocar e-com.
                            $url_webser = "http://198.171.209.91/ecom/transweb/modulos/webservice_colocar/servidor.php?wsdl";
                            $mFecha[0] = date('Y-m-d', strtotime($regist['fecnov']));
                            $mFecha[1] = date('H:i', strtotime($regist['fecnov']));

                            $parametros = array("usuario" => $interfaz->interfaz[$i]['usuari'],
                                "password" => $interfaz->interfaz[$i]['passwo'],
                                "placa" => $mDatDespac['num_placax'],
                                "manifiesto" => $mDatDespac['cod_manifi'],
                                "id_puestocontrol" => $regist['contro'],
                                "id_novedad" => $regist['noveda'],
                                "fecha" => $mFecha[0],
                                "hora" => $mFecha[1],
                                "observacion" => 'Interfaz - ' . $regist['observ'],
                                "cod_empresa" => $regist['nittra']
                            );

                            $oSoapClient = new soapclient($url_webser, array('encoding' => 'ISO-8859-1'));

                            //Métodos disponibles en el WS
                            $respuesta = $oSoapClient->__call('getDespacho', $parametros);
                        } catch (SoapFault $e) {
                            $error_ = $e->getMessage();

                            if ($e->faultcode && $e->faultcode != '2' && $e->faultcode != '1' && $e->faultstring != 'NINGUNO') {
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist['nittra'] . " <br>";
                                $mMessage .= "Codigo ecom: " . $mData['cod_alianz'] . " <br>";
                                $mMessage .= "Numero de manifiesto: " . $mDatDespac['cod_manifi'] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDatDespac['num_placax'] . " <br>";
                                $mMessage .= "Codigo puesto de control: " . $regist['contro'] . " <br>";
                                $mMessage .= "Codigo novedad: " . $regist['noveda'] . " <br>";
                                $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist['observ'] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Codigo de error: " . $e->faultcode . " <br>";
                                $mMessage .= "Actor del error: " . $e->faultactor . " <br>";
                                $mMessage .= "Mesaje de error: " . $e->faultstring . " <br>";
                                $mMessage .= "Observaciones: " . $e->detail . " <br>";

                                mail(MAIL_SUPERVISORES . ", " . MAIL_SOPORTE, "Web service Faro - EcomColocar", '<div name="'.HOST_WEB.'_faro_10">' . $mMessage . '</div>', $mCabeceSoporte);
                            }
                        }
                    }
                    #</IF 5.1.1.1.2>
                    #< IF 5.1.1.1.3>
                    if (51 == $interfaz->interfaz[$i]['operad']) {
                        $error_ = NULL;
                        $url_webser = 'http://www.colombiasoftware.net/base/webservice/ReportePuestoControlCS.php?wsdl';

                         if ($regist["nittra"] == "830104929") {
                            $cod_transp = "067";
                        }else{
                            $cod_transp = $regist["nittra"];
                        }

                        // parche 313726 para colombiasoftware, hay una URL de pruebas y otra de prod, la de prod es igual para todos y cada cliente tiene un codigo  
                        $cod_transp = $interfaz->interfaz[$i]['codigo']     != '' ? $interfaz->interfaz[$i]['codigo'] : $cod_transp; // cod de la transp, no es el nit, debe estar en BD
                        $url_webser    = $interfaz->interfaz[$i]['diwsdl']  != '' ? $interfaz->interfaz[$i]['diwsdl'] : $url_webser; // URL ws, debe estar en BD

                        #<IF 5.1.1.1.3.1>
                        if (strpos(strtolower($regist['sitio']), "eal") !== FALSE || 
                            strpos(strtolower($regist['sitio']), "oal") !== FALSE || 
                            strpos(strtolower($regist['sitio']), "ecl") !== FALSE ||
                            strpos($mData['nom_contro'], "eal") !== FALSE || 
                            strpos($mData['nom_contro'], "oal") !== FALSE || 
                            strpos($mData['nom_contro'], "ecl") !== FALSE
                           ) {
                            $mControPadre = self::getPCPadre($regist);

                            /*                             * ****************** TRATAMIENTO SOAP ******************** */
                            ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
                            try {
                                //Ruta Web Service Colocar e-com.
                                $mEstado = '1';
                                //$url_webser = "http://www.colombiasoftware.net/base/webservice/ReportePuestoControlCS.php?wsdl";
                                $mFecha[0] = date('Y-m-d', strtotime($regist['fecnov']));
                                $mFecha[1] = date('H:i:s', strtotime($regist['fecnov']));

                                $parametros = array();
                                $parametros['empresa_codigocs'] = $cod_transp;
                                $parametros['usuario'] = $interfaz->interfaz[$i]['usuari'];
                                $parametros['clave'] = $interfaz->interfaz[$i]['passwo'];
                                $parametros['sentido'] = "0";
                                $parametros['direccion'] = $mControPadre['nom_contro'];
                                $parametros['tipo_evento'] = "0";
                                $parametros['kilometraje'] = "0";
                                $parametros['velocidad'] = "0";
                                // $parametros['codigo_puestocontrol'] = $mControPadre['cod_contro'];
                                $parametros['codigopuestocontrol'] = $mControPadre['cod_contro'];

                                //Se envia la novedad sin los codigos internos faro de usuarios que registran la novedad
                                $pos = strpos($regist['observ'], ">");

                                if ($pos !== false) {
                                    $regist['observ'] = substr($regist['observ'], $pos + 1);
                                }

                                $parametros['novedad'] = $mDatNoveda['nom_noveda'] . ' ' . $regist['observ'];
                                $parametros['longitud'] = $mControPadre['val_longit'];
                                $parametros['latitud'] = $mControPadre['val_latitu'];
                                $parametros['hora'] = $mFecha[1];
                                $parametros['fecha'] = $mFecha[0];
                                $parametros['placa'] = trim($mDatDespac['num_placax']);
                                // $parametros['manifiesto_codigo'] = $mDatDespac['cod_manifi'];

                                $oSoapClient = new SoapClient($url_webser, array('encoding' => 'ISO-8859-1'));

                                //Métodos disponibles en el WS
                                $respuesta = $oSoapClient-> __soapCall( 'puestocontrol', $parametros );

                                if ($respuesta != 'OK REPORTO.') {
                                    $error_ = $respuesta;
                                }
                            } catch (SoapFault $e) {
                                $error_ = $e->getMessage();
                            }

                            if ($error_ != NULL) {
                                $mEstado = '0';
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora Sistema: " . date("Y-m-d H:i") . " <br>";
                                $mMessage .= "Fecha y hora Novedad: " . $mFecha[0] . ' ' . $mFecha[1] . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist['nittra'] . " <br>";
                                $mMessage .= "Numero de manifiesto: " . $mDatDespac['cod_manifi'] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDatDespac['num_placax'] . " <br>";
                                $mMessage .= "Codigo puesto de control del despacho: " . $regist['contro'] . " <br>";
                                $mMessage .= "Codigo puesto de control padre: " . $mControPadre['cod_contro'] . " <br>";
                                $mMessage .= "Codigo novedad: " . $regist['noveda'] . " <br>";
                                $mMessage .= "Nombre novedad: " . $mDatNoveda['nom_noveda'] . " <br>";
                                $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist['observ'] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Mensaje de error: " . $error_ . " <br>";

                                mail(MAIL_SOPORTE, 
                                	"Error Web service Humadea - Colombiasoftware", 
                                	'<div name="'.HOST_WEB.'_faro_11">' . $mMessage."\n". var_export($parametros, true ) . '</div>', $mCabeceSoporte);
                            }
                            //Insert de Bitácora 
                            $mLog = array();
                            $mLog['mEstado'] = $mEstado;
                            $mLog['mResult'] = $error_;
                            $mLog['mUsuari'] = $regist['usuari'];
                            $mLog['mNumDes'] = $regist['despac'];

                            $this->InsertPuestoControlHumadea($parametros, $mLog, $oSoapClient -> __getLastRequest() );
                        } else {
                            /*                             * ****************** TRATAMIENTO SOAP ******************** */
                            ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
                            try {
                                $mEstado = '1';
                                //Ruta Web Service Colocar e-com.
                                //$url_webser = "http://www.colombiasoftware.net/base/webservice/ReportePuestoControlCS.php?wsdl";
                                $mFecha[0] = date('Y-m-d', strtotime($regist['fecnov']));
                                $mFecha[1] = date('H:i:s', strtotime($regist['fecnov']));

                                $parametros = array();
                                $parametros['empresa_codigocs'] = $cod_transp;
                                $parametros['usuario'] = $interfaz->interfaz[$i]['usuari'];
                                $parametros['clave'] = $interfaz->interfaz[$i]['passwo'];

                                //Se envia la novedad sin los codigos internos faro de usuarios que registran la novedad
                                $pos = strpos($regist['observ'], ">");

                                if ($pos !== false) {
                                    $regist['observ'] = substr($regist['observ'], $pos + 1);
                                }

                                $parametros['novedad'] = $mDatNoveda['nom_noveda'] . ' ' . $regist['observ'];
                                $parametros['hora'] = $mFecha[1];
                                $parametros['fecha'] = $mFecha[0];
                                $parametros['placa'] = trim($mDatDespac['num_placax']);
                                $parametros['manifiesto_codigo'] = $mDatDespac['cod_manifi'];
                                $parametros['lugar'] = $regist['sitio'];

                                $oSoapClient = new SoapClient($url_webser, array('encoding' => 'ISO-8859-1'));


                                //Métodos disponibles en el WS
                                $respuesta = $oSoapClient->__soapCall('novedades', $parametros);

                                if ($respuesta != 'OK REPORTO.') {
                                    $error_ = $respuesta;
                                }
                            } catch (SoapFault $e) {
                                $error_ = $e->getMessage();
                            }

                            if ($error_ != NULL) {
                                $mEstado = '0';
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora Sistema: " . date("Y-m-d H:i") . " <br>";
                                $mMessage .= "Fecha y hora Novedad: " . $mFecha[0] . ' ' . $mFecha[1] . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist['nittra'] . " <br>";
                                $mMessage .= "Numero de manifiesto: " . $mDatDespac['cod_manifi'] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDatDespac['num_placax'] . " <br>";
                                $mMessage .= "Codigo novedad: " . $regist['noveda'] . " <br>";
                                $mMessage .= "Nombre novedad: " . $mDatNoveda['nom_noveda'] . " <br>";
                                $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist['observ'] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Mensaje de error: " . $error_ . " <br>";

                                mail(MAIL_SUPERVISORES . ", " . MAIL_SOPORTE, "Error Web service Colombia Software - Colombiasoftware Puestos virtuales", '<div name="'.HOST_WEB.'_faro_12">' . $mMessage . '</div>', $mCabeceSoporte);
                            }
                            //Insert de Bitácora 
                            $mLog = array();
                            $mLog['mEstado'] = $mEstado;
                            $mLog['mResult'] = $respuesta;
                            $mLog['mUsuari'] = $regist['usuari'];
                            $mLog['mNumDes'] = $regist['despac'];
                            $mLog['mLugarr'] = $regist['sitio'];

                            $this->InsertNovedadHumadea($parametros, $mLog);
                        }
                        #</IF 5.1.1.1.3.1>
                    }
                    #</IF 5.1.1.1.3>
                    #<IF 5.1.1.1.4>
                    if (57 == $interfaz->interfaz[$i]['operad']) { //GMT Onlinetools   // Prueba Nit 800081833  NIT ORIGINAL ->  8301019592
                        $mControPadre = self::getPCPadre($regist);

                        //Interfaz Transportes GMT Onlinetools   // Prueba Nit 800081833  NIT ORIGINAL ->  8301019592
                        ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
                        try {
                            $url_webser = $interfaz->interfaz[$i]['diwsdl'];

                            //Parametros Web Service.   
                            $mFecha[0] = date('Y-m-d', strtotime($regist['fecnov']));
                            $mFecha[1] = date('H:i:s', strtotime($regist['fecnov']));

                            $parametros = array();
                            $parametros['usuario'] = $interfaz->interfaz[$i]['usuari'];
                            $parametros['clave'] = $interfaz->interfaz[$i]['passwo'];
                            $parametros['planilla'] = $mDatDespac['cod_manifi'];
                            $parametros['placa'] = $mDatDespac['num_placax'];
                            $parametros['ind_naturaleza'] = (int) $mDatDespac['ind_tipdes'];
                            $parametros['fecha_reporte'] = $mFecha[0];
                            $parametros['hora_reporte'] = $mFecha[1];
                            $parametros['cod_puesto'] = (int) $mControPadre['cod_puecon'];
                            $parametros['nom_punto'] = $mControPadre['nom_contro'];
                            $parametros['cod_novedad'] = (int) $regist['noveda'];
                            $parametros['novedad'] = $mDatNoveda['nom_noveda'];
                            $parametros['observacion'] = $regist['observ'];

                            $oSoapClient = new soapclient($url_webser, array('encoding' => 'ISO-8859-1'));
                            //Métodos disponibles en el WS
                            $respuesta = $oSoapClient->__call('set_Novedad', $parametros);
                        } catch (SoapFault $e) {
                            $error_ = $e->getMessage();

                            if ($e->faultcode && $e->faultcode != '2' && $e->faultcode != '1' && $e->faultstring != 'NINGUNO') {
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist['nittra'] . " <br>";
                                $mMessage .= "Codigo alianza: " . $mData['cod_alianz'] . " <br>";
                                $mMessage .= "Numero de manifiesto: " . $mDatDespac['cod_manifi'] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDatDespac['num_placax'] . " <br>";
                                $mMessage .= "Codigo puesto de control: " . $regist['contro'] . " <br>";
                                $mMessage .= "Codigo novedad: " . $regist['noveda'] . " <br>";
                                $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist['observ'] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Codigo de error: " . $e->faultcode . " <br>";
                                $mMessage .= "Actor del error: " . $e->faultactor . " <br>";
                                $mMessage .= "Mesaje de error: " . $e->faultstring . " <br>";
                                $mMessage .= "Observaciones: " . $e->detail . " <br>";

                                mail(MAIL_SUPERVISORES . ", " . MAIL_SOPORTE, "Web service Faro - OnlineTools GMT", '<div name="'.HOST_WEB.'_faro_13">' . $mMessage . '</div>', $mCabeceSoporte);
                            }
                        }
                    }
                    #</IF 5.1.1.1.4>
                    #<IF 5.1.1.1.5>
                    if (11 == $interfaz->interfaz[$i]['operad']) {//Interfaz con coounidas proveedor ECOM

                        /*                         * ****************** TRATAMIENTO SOAP ******************** */
                        ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
                        try {
                            //Ruta Web Service Coounidas e-com.
                            $url_webser = "http://198.171.209.91/ecom/transweb/modulos/webservice_counida/servidor.php?wsdl";
                            $mFecha[0] = date('Y-m-d', strtotime($regist['fecnov']));
                            $mFecha[1] = date('H:i', strtotime($regist['fecnov']));

                            //Parametros Web Service.
                            $parametros = array("usuario" => $interfaz->interfaz[$i]['usuari'],
                                "password" => $interfaz->interfaz[$i]['passwo'],
                                "placa" => $mDatDespac['num_placax'],
                                "manifiesto" => $mDatDespac['cod_manifi'],
                                "id_puestocontrol" => $regist['contro'],
                                "id_novedad" => $regist['noveda'],
                                "fecha" => $mFecha[0],
                                "hora" => $mFecha[1],
                                "observacion" => 'Interfaz - ' . $regist['observ'],
                                "cod_empresa" => $regist['nittra']
                            );

                            $oSoapClient = new soapclient($url_webser, array('encoding' => 'ISO-8859-1'));

                            //Métodos disponibles en el WS
                            $respuesta = $oSoapClient->__call('getDespacho', $parametros);
                        } catch (SoapFault $e) {
                            $error_ = $e->getMessage();

                            if ($e->faultcode && $e->faultcode != '2' && $e->faultcode != '1' && $e->faultstring != 'NINGUNO') {
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist['nittra'] . " <br>";
                                $mMessage .= "Codigo alianza: " . $mData['cod_alianz'] . " <br>";
                                $mMessage .= "Numero de manifiesto: " . $mDatDespac['cod_manifi'] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDatDespac['num_placax'] . " <br>";
                                $mMessage .= "Codigo puesto de control: " . $regist['contro'] . " <br>";
                                $mMessage .= "Codigo novedad: " . $regist['noveda'] . " <br>";
                                $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist['observ'] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Codigo de error: " . $e->faultcode . " <br>";
                                $mMessage .= "Actor del error: " . $e->faultactor . " <br>";
                                $mMessage .= "Mesaje de error: " . $e->faultstring . " <br>";
                                $mMessage .= "Observaciones: " . $e->detail . " <br>";

                                mail(MAIL_SUPERVISORES . ", " . MAIL_SOPORTE, "Web service Faro - EcomCoounidas", '<div name="'.HOST_WEB.'_faro_14">' . $mMessage . '</div>', $mCabeceSoporte);
                            }
                        }
                    }
                    #</IF 5.1.1.1.5>
                    #<IF 5.1.1.1.6>
                    if (50 == $interfaz->interfaz[$i]['operad']) {//SI ESTA ACTIVA LA INTERFAZ "FARO".
                        $error_ = NULL;

                        ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
                        try {
                            $oSoapClient = new soapclient($mData['url_webser'], array('encoding' => 'ISO-8859-1'));

                            //Métodos disponibles en el WS
                            $mResult = $oSoapClient->__call('aplicaExists', array("nom_aplica" => $interfaz->interfaz[$i]['nombre']));
                            $mResult = explode("; ", $mResult);
                            $mCodResp = explode(":", $mResult[0]);
                            $mMsgResp = explode(":", $mResult[1]);

                            if ("1000" != $mCodResp[1]) {
                                $error_ = $mMsgResp[1];
                            }
                        } catch (SoapFault $e) {
                            $error_ = $e->getMessage();
                        }

                        $parametros = array("nom_usuari" => $interfaz->interfaz[$i]['usuari'],
                            "pwd_clavex" => $interfaz->interfaz[$i]['passwo'],
                            "nom_aplica" => $interfaz->interfaz[$i]['nombre'],
                            "num_manifi" => $mDatDespac['cod_manifi'],
                            "num_placax" => $mDatDespac['num_placax'],
                            "cod_novbas" => 0,
                            "cod_conbas" => $mData['cod_pcxbas'][0][0],
                            "tim_duraci" => $regist['tieadi'],
                            "fec_noveda" => date('Y-m-d H:i', strtotime($regist['fecnov'])),
                            "des_noveda" => $regist['observ'],
                            "nom_contro" => $mData['nom_contro'],
                            "nom_sitiox" => substr($regist['sitio'], 0, 50),
                            "cod_confar" => NULL,
                            'cod_novfar' => $regist['noveda'],
                            'nom_noveda' => $mDatNoveda['nom_noveda'],
                            'ind_alarma' => $mDatNoveda['ind_alarma'],
                            'ind_tiempo' => $mDatNoveda['ind_tiempo'],
                            'nov_especi_' => $mDatNoveda['nov_especi'],
                            'ind_manala' => $mDatNoveda['ind_manala'],
                            'bin_fotcon' => $regist['bin_fotcon']
                        );

                        if (!$error_) {
                            for ($index1 = 0; $index1 < count($mData['cod_pcxbas']); $index1++) {
                                $parametros['cod_conbas'] = $mData['cod_pcxbas'][$index1][0];
                                //Consumo Web Service.
                                /*                                 * ****************** TRATAMIENTO SOAP ******************** */
                                try {
                                    //M�todos disponibles en el WS
                                    $respuesta = $oSoapClient->__call($mOption[0], $parametros);
                                    $mResult = explode("; ", $respuesta);
                                    $mCodResp = explode(":", $mResult[0]);
                                    $mMsgResp = explode(":", $mResult[1]);

                                    if ("1000" != $mCodResp[1]) {
                                        $error_ = $mMsgResp[1];
                                    } else {
                                        $error_ = NULL;
                                        //Hace la actualizaci�n del indicador estado de novedad enviada a tms
                                        self::updateEstadoEnviadoTms($regist, $mIndEnSiti);
                                        break;
                                    }
                                } catch (SoapFault $e) {
                                    $error_ = $e->getMessage();
                                }
                            }



                            if ($error_ != NULL) {
                                echo '<meta name="viewport" content="width=device-width, initial-scale=1">
                                    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
                                    <style>
                                        
                                        .alert{
                                            text-align: center;
                                            background-color: #f44336;
                                            color: white;
                                            margin: auto;
                                            width: 80%;
                                            border-radius: 20px;
                                            border: 5px #f44336 solid;
                                        }
                                        .contenedorAlert{
                                            border-radius: 10px;
                                            padding: 10px;
                                            background-color: #f44336;
                                        }
                                        
                                        .contenedorWarning{
                                            font-size:20px;
                                            padding: 10px;
                                            background-color: #ffffff;
                                            color: #000000;
                                            border-radius: 0px 0px 20px 20px;
                                        }
                                        
                                        .warning{
                                            margin: 0 auto;
                                            font-size:100px;
                                            color: white;
                                        }

                                        @keyframes anim1 {  
                                            from { transform: scaleX(1) rotate(0deg) ;-o-transform: scaleX(1) rotate(0deg) ; }
                                            to { transform: scaleX(-1) rotate(0deg);  -o-transform: scaleX(-1) rotate(0deg);}
                                         }
                                         @-webkit-keyframes anim1 {  
                                            from { -webkit-transform: scaleX(1) rotate(-15deg) ; }
                                            to { -webkit-transform: scaleX(-1) rotate(15deg);  }
                                         }

                                         .contenedorAlert i{
                                            animation: anim1 2s ease-in-out alternate infinite;
                                            -webkit-animation: anim1 2s ease-in-out alternate infinite;
                                       }
                                    </style>
                                    <div class="alert">
                                        <div class="contenedorAlert">
                                            <i class="material-icons warning" >warning</i><br>
                                            <strong>Error!</strong> 
                                        </div>
                                        <div class="contenedorWarning">
                                            Error en Webservice - Insertar ' . $mOption[1] . ' en ' . $interfaz->interfaz[$i]['nombre'] . ':' . $error_ . '</b>
                                        </div>
                                    </div>';
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist['nittra'] . " <br>";
                                $mMessage .= "Aplicacion: " . $interfaz->interfaz[$i]['nombre'] . " <br>";
                                $mMessage .= "Numero de despacho SAT: " . $regist['despac'] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDatDespac['num_placax'] . " <br>";
                                $mMessage .= "Codigo puesto de control: " . $regist['contro'] . " <br>";
                                $mMessage .= "Codigo novedad: " . $regist['noveda'] . " <br>";
                                $mMessage .= "Nombre novedad: " . $mDatNoveda['nom_noveda'] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Codigo de error: " . $e->faultcode . " <br>";
                                $mMessage .= "Mesaje de error: " . $error_ . " <br>";

                                $novedaError['cod_respon'] = $e->faultcode;
                                $novedaError['msg_respon'] = $error_;
                                $novedaError['det_respon'] = $mMessage;
                                //Se registran errores de la interfaz en la BD
                                $this->setNovedadError($parametros, $regist, $novedaError, $mOption[2]);
                            }
                        } else {
                            $novedaError['cod_respon'] = '';
                            $novedaError['msg_respon'] = '';
                            $novedaError['det_respon'] = $error_;

                            $this->setNovedadError($parametros, $regist, $novedaError, $mOption[2]);
                            echo '<meta name="viewport" content="width=device-width, initial-scale=1">
                                    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
                                    <style>
                                        
                                        .alert{
                                            text-align: center;
                                            background-color: #f44336;
                                            color: white;
                                            margin: auto;
                                            width: 80%;
                                            border-radius: 20px;
                                            border: 5px #f44336 solid;
                                        }
                                        .contenedorAlert{
                                            border-radius: 10px;
                                            padding: 10px;
                                            background-color: #f44336;
                                        }
                                        
                                        .contenedorWarning{
                                            font-size:20px;
                                            padding: 10px;
                                            background-color: #ffffff;
                                            color: #000000;
                                            border-radius: 0px 0px 20px 20px;
                                        }
                                        
                                        .warning{
                                            margin: 0 auto;
                                            font-size:100px;
                                            color: white;
                                        }

                                        @keyframes anim1 {  
                                            from { transform: scaleX(1) rotate(0deg) ;-o-transform: scaleX(1) rotate(0deg) ; }
                                            to { transform: scaleX(-1) rotate(0deg);  -o-transform: scaleX(-1) rotate(0deg);}
                                         }
                                         @-webkit-keyframes anim1 {  
                                            from { -webkit-transform: scaleX(1) rotate(-15deg) ; }
                                            to { -webkit-transform: scaleX(-1) rotate(15deg);  }
                                         }

                                         .contenedorAlert i{
                                            animation: anim1 2s ease-in-out alternate infinite;
                                            -webkit-animation: anim1 2s ease-in-out alternate infinite;
                                       }
                                    </style>
                                    <div class="alert">
                                        <div class="contenedorAlert">
                                            <i class="material-icons warning" >warning</i><br>
                                            <strong>Error!</strong> 
                                        </div>
                                        <div class="contenedorWarning">
                                            Error en Webservice - Insertar novedad en ' . $interfaz->interfaz[$i]['nombre'] . ':' . $error_ . '</b>
                                        </div>
                                    </div>';
                        }
                    }
                    #</IF 5.1.1.1.6> 
                    #<IF 5.1.1.1.66>
                    if (52 == $interfaz->interfaz[$i]['operad'] ) //INTERFAZ CON CORONA NUEVA URL corona.intrared.net
                    {
                        $error_ = NULL;
                        $mCodeException = '';

                        try 
                        {
                            ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
                            $oSoapClient = new soapclient('https://corona.intrared.net/ap/interf/app/externo/externo.php?wsdl', array('trace' => true, 'exception' => true, 'encoding' => 'ISO-8859-1'));

                            $mIndViaje = "SELECT num_viajex, num_placax FROM " . BASE_DATOS . ".tab_despac_viajex WHERE num_despac = '".$regist['despac']."' ";
                            $mConsult = new Consulta($mIndViaje, $this->conexion);
                            $mViajexx = $mConsult->ret_matrix('a');

                            $parametros = [ 
                                            "nom_usuari" => $interfaz->interfaz[$i]['usuari'],
                                            "pwd_clavex" => $interfaz->interfaz[$i]['passwo'],
                                            "cod_transp" => '860068121',
                                            "num_manifi" => ( $mViajexx[0]['num_viajex'] != '' ? $mViajexx[0]['num_viajex'] : $mDatDespac['cod_manifi'] ) ,
                                            "num_placax" => ( $mViajexx[0]['num_placax'] != '' ? $mViajexx[0]['num_placax'] : $mDatDespac['num_placax'] ) ,
                                            "cod_noveda" => $regist['noveda'],
                                            "cod_contro" => $regist['contro'],
                                            "tim_duraci" => $regist['tieadi'],
                                            "fec_noveda" => date('Y-m-d H:i', strtotime($regist['fecnov'])),
                                            "des_noveda" => $regist['observ'],
                                            "nom_noveda" => $mDatNoveda['nom_noveda'],
                                            "nom_contro" => $mData['nom_contro'],
                                            "nom_sitiox" => substr($regist['sitio'], 0, 50),
                                            "num_viajex" => NULL,
                                            "fot_noveda" => NULL,
                                            "cod_remdes" => NULL,
                                            "tim_sigpun" => NULL,
                                            "tim_ultpun" => NULL
                                          ];

                            if (!$error_) 
                            {
                                //Validacion de novedad OAL o tercera de corona
                                if($mViajexx[0]['num_viajex'] != '' ){
                                    $respuesta = $oSoapClient->__soapCall('setNovedadNC', $parametros); // si la novedad es de una tercera de corona
                                }else{
                                    $respuesta = $oSoapClient->__soapCall('setNovedadPC', $parametros); // Si la novedad es de una esfera
                                }

                                $mResult  = explode("; ", $respuesta);
                                $mCodResp = explode(":", $mResult[0]);
                                $mMsgResp = explode(":", $mResult[1]);

                                if ("1000" != $mCodResp[1]) 
                                {
                                    $error_ = $mMsgResp[1];
                                    $mCodeException = $mCodResp[1];
                                } 
                                else 
                                {
                                    $error_ = NULL;
                                    break;
                                }
                            }
                        } 
                        catch (Exception $e) 
                        {
                            $error_ = $e->getMessage();
                            $mCodeException = $e->getCode();
                        }

                        if ($error_ != NULL) 
                        {
                            echo "<div align='center' style='color:#000000; padding:5px; font-size:14px; background-color:#FFAFAF; border:2px solid #000000;'><b>Error en Webservice - Insertar " . $mOption[1] . " en " . $interfaz->interfaz[$i]['nombre'] . ':' . $error_ . "</b></div>";
                            $mMessage = "******** Encabezado ******** <br>";
                            $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " <br>";
                            $mMessage .= "Empresa de transporte: " . $regist['nittra'] . " <br>";
                            $mMessage .= "Aplicacion: " . $interfaz->interfaz[$i]['nombre'] . " <br>";
                            $mMessage .= "Numero de despacho SAT: " . $regist['despac'] . " <br>";
                            $mMessage .= "Placa del vehiculo: " . $mDatDespac['num_placax'] . " <br>";
                            $mMessage .= "Codigo puesto de control: " . $regist['contro'] . " <br>";
                            $mMessage .= "Codigo novedad: " . $regist['noveda'] . " <br>";
                            $mMessage .= "Nombre novedad: " . $mDatNoveda['nom_noveda'] . " <br>";
                            $mMessage .= "******** Detalle ******** <br>";
                            $mMessage .= "Codigo de error: " . $mCodeException . " <br>";
                            $mMessage .= "Mesaje de error: " . $error_ ." XML ".@$oSoapClient -> __getLastRequest() . " <br>";

                            $novedaError['cod_respon'] = $mCodeException;
                            $novedaError['msg_respon'] = $error_." XML ".@$oSoapClient -> __getLastRequest();
                            $novedaError['det_respon'] = $mMessage;
                            //Se registran errores de la interfaz en la BD
                            $this->setNovedadError($parametros, $regist, $novedaError, $mOption[2]);
                        }

                    }
                    #</IF 5.1.1.1.66>
                    #<IF 5.1.1.1.7>
                    if (56 == $interfaz->interfaz[$i]['operad']) {//transportadora referencia =  800081833 || Trasportadora FW 
                        $mControPadre = self::getPCPadre($regist);

                        //********************* TRATAMIENTO SOAP *********************
                        ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
                        try {
                            //Ruta Web Service Colocar e-com.
                            $url_webser = "http://50.28.44.212/~forward/server/seguimiento.php?wsdl";
                            $mFecha[0] = date('Y-m-d H:i', strtotime($regist['fecnov']));

                            $parametros = array();
                            $parametros['usuario'] = $interfaz->interfaz[$i]['usuari'];
                            $parametros['clave'] = $interfaz->interfaz[$i]['passwo'];
                            $parametros['fecha'] = $mFecha[0];
                            $parametros['punto_control'] = substr($regist['sitio'], 0, 30);
                            $parametros['distancia_pc'] = $regist['observ'];
                            $parametros['motivo'] = $mData['cod_fwxxxx'];
                            $parametros['referencia'] = $mDataDespac['cod_manifi'];
                            $parametros['placa'] = $mDataDespac['num_placax'];

                            $oSoapClient = new soapclient($url_webser, array('encoding' => 'ISO-8859-1'));

                            //Métodos disponibles en el WS
                            $respuesta = $oSoapClient->__call('reporte', $parametros);

                            if ($respuesta != 'Ok') {
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora Sistema: " . date("Y-m-d H:i") . " <br>";
                                $mMessage .= "Fecha y hora Novedad: " . $mFecha[0] . ' ' . $mFecha[1] . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist['nittra'] . " <br>";
                                $mMessage .= "Numero de manifiesto: " . $mDataDespac['cod_manifi'] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDataDespac['num_placax'] . " <br>";
                                $mMessage .= "Codigo puesto de control del despacho: " . $regist['contro'] . " <br>";
                                $mMessage .= "Codigo puesto de control padre: " . $mControPadre['cod_contro'] . " <br>";
                                $mMessage .= "Codigo novedad: " . $regist['noveda'] . " <br>";
                                $mMessage .= "Nombre novedad: " . $mNoveda[0]['nom_noveda'] . " <br>";
                                $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist['observ'] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Mensaje de error: " . $respuesta . " <br>";

                                mail(MAIL_SUPERVISORES . ", " . MAIL_SOPORTE, "Error Web service Trans FW - SISTEMA ARGO", '<div name="'.HOST_WEB.'_faro_15">' . $mMessage . '</div>', $mCabeceSoporte);
                            }
                        } catch (SoapFault $e) {
                            $error_ = $e->getMessage();
                            echo "<pre>";
                            print_r($error_);
                            echo "</pre>";
                        }
                    }
                    #</IF 5.1.1.1.7>
                    #<IF 5.1.1.1.8>
                    if (25 == $interfaz->interfaz[$i]['operad'] && $mIndEnSiti == true) {
                        /*                         * ****************** TRATAMIENTO SOAP ******************** */
                        ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
                        try {
                            //Ruta Web Service Faro.
                            $url_webser = "https://".HOST_WEB.".intrared.net/ap/interf/app/faro/wsdl/faro.wsdl";
                            $mFecha[0] = date('Y-m-d', strtotime($regist['fecnov']));
                            $mFecha[1] = date('H:i', strtotime($regist['fecnov']));

                            $parametros = array("nom_usuari" => $interfaz->interfaz[$i]['usuari'],
                                "pwd_clavex" => $interfaz->interfaz[$i]['passwo'],
                                "cod_manifi" => $mDatDespac['cod_manifi'],
                                "fec_noveda" => date('Y-m-d', strtotime($regist['fecnov'])),
                                "hor_noveda" => date('H:i:s', strtotime($regist['fecnov'])),
                                "nom_contro" => $mData['nom_contro'],
                                "des_observ" => $regist['observ'],
                                "nom_llavex" => "59a68t7j95s4t96dS2g9A",
                                "fec_pronov" => date('Y-m-d', strtotime($regist['fecnov'])),
                                "hor_pronov" => date('H:i:s', strtotime($regist['fecnov'])),
                                "num_placax" => $mDatDespac['num_placax']
                            );

                            $oSoapClient = new soapclient($url_webser, array('encoding' => 'ISO-8859-1'));

                            //Métodos disponibles en el WS
                            $respuesta = $oSoapClient->__call('setPCIntracarga', $parametros);

                            //mail("maribel.garcia@eltransporte.org, nelson.liberato@intrared.net ", "Web service Intracarga Fault", '<div name="'.HOST_WEB.'_faro_16">' . var_export($respuesta, true) . '</div>', $mCabeceSoporte);
                        } catch (SoapFault $e) {
                            $error_ = $e->getMessage();

                            if ("1000" != $e->faultcode) {
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora de la novedad: " . date("Y-m-d H:i", strtotime($regist['fecnov'])) . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist['nittra'] . " <br>";
                                $mMessage .= "Numero de manifiesto: " . $mDatDespac['cod_manifi'] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDatDespac['num_placax'] . " <br>";
                                $mMessage .= "Codigo puesto de control: " . $regist['contro'] . " <br>";
                                $mMessage .= "Nombre puesto de control: " . $mData['nom_contro'] . " <br>";
                                $mMessage .= "Observacion.: " . $regist['observ'] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Cod Error: " . $e->faultcode . " <br>";
                                $mMessage .= "Error: " . $e->faultstring . " <br>";
                                $mMessage .= "Error Detallado: " . $result . " <br>";

                                mail(MAIL_SUPERVISORES . ", " . MAIL_SOPORTE, "Web service Intracarga Fault", '<div name="'.HOST_WEB.'_faro_17">' . $mMessage . '</div>', $mCabeceSoporte
                                );
                            }
                        }
                    }
                    #</IF 5.1.1.1.8>
                    #<IF 5.1.1.1.9>
                    if (58 == $interfaz->interfaz[$i]['operad'] && $mIndEnSiti == true) { //Interfaz con MCT NIT - 830004861 -> En Sitio
                        $mControPadre = self::getPCPadre($regist);

                        try {
                            # Imagenes de las fotos de un despacho
                            $mQuery = 'SELECT bin_fotoxx, bin_fotox2 FROM ' . BASE_DATOS . '.tab_despac_images WHERE num_despac = "' . $regist['despac'] . '"  ';
                            $consulta = new Consulta($mQuery, $this->conexion);
                            $mFotoDespac = $consulta->ret_matriz();

                            # Trama de datos a enviar a MCT
                            $mDataMCT = array(
                                'manifiesto_codigo' => urlencode($mDatDespac['cod_manifi']),
                                'ptoc_codigo' => urlencode($mControPadre['cod_contro']),
                                'ptoc_nombre' => urlencode($mControPadre['nom_contro']),
                                'ptoc_fecha' => urlencode(date("Y-m-d H:i", strtotime($regist['fecnov']))),
                                'ptoc_observacion' => urlencode(($regist['observ'] == '' ? 'Registro GL: ' . $mControPadre['cod_contro'] . ' - ' . $mControPadre['nom_contro'] : $regist['observ'])),
                                'ptoc_imagenconductor' => urlencode("{$mFotoDespac[0]['bin_fotoxx']}"),
                                'ptoc_imagenvehiculo' => urlencode("{$mFotoDespac[0]['bin_fotox2']}"),
                                'ptoc_imagenprecinto' => urlencode(NULL)
                            );

                            $mAditional['num_placax'] = $regist['placa'];
                            $mAditional['tip_noveda'] = "PC";
                            $mAditional['url_webser'] = $interfaz->interfaz[$i]['diwsdl'];
                            # Incluye la clase encargada de la interfaz         
                            include_once("../" . DIR_APLICA_CENTRAL . "/lib/InterfMct.inc");
                            $mMctX = new InterfMct($this->conexion, $mDataMCT, $mAditional);
                            $mReturn = $mMctX->getResponMct();

                            # Si la interfaz de MCT retorna error en validacion de datos
                            if ($mReturn['error'] == true) {
                                throw new Exception($mReturn['message'], 1);
                            }
                        } catch (Exception $e) {
                            $error_ = $e->getMessage();
                            if ("1000" != $e->faultcode) {
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora de la novedad: " . date("Y-m-d H:i", strtotime($regist['fecnov'])) . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist['nittra'] . " <br>";
                                $mMessage .= "Numero de manifiesto: " . $mDatDespac['cod_manifi'] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDatDespac['num_placax'] . " <br>";
                                $mMessage .= "Codigo puesto de control: " . $regist['contro'] . " <br>";
                                $mMessage .= "Nombre puesto de control: " . $mControPadre['nom_contro'] . " <br>";
                                $mMessage .= "Observacion.: " . $regist['observ'] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Cod Error: " . $e->faultcode . " <br>";
                                $mMessage .= "Error: " . $e->faultstring . " <br>";
                                $mMessage .= "Error Detallado: " . $result . " <br>";

                                mail(MAIL_SUPERVISORES . ", " . MAIL_SOPORTE, "Web service MCT Error Catch", '<div name="'.HOST_WEB.'_faro_18">' . $mMessage . '</div>', $mCabeceSoporte);
                            }
                        }
                    }
                    #</IF 5.1.1.1.9>
                    #<IF 5.1.1.1.10>
                    if (58 == $interfaz->interfaz[$i]["operad"] && $mIndEnSiti == false) { //Interfaz con MCT NIT - 830004861 -> Antes de Sitio
                        try {
                            # Trama de datos a enviar a MCT
                            $mDataMCT = array(
                                'manifiesto_codigo' => urlencode($mDatDespac["cod_manifi"]),
                                'nov_fecha' => urlencode(date("Y-m-d H:i", strtotime($regist["fecnov"]))),
                                'nov_observacion' => urlencode(($regist["observ"] == '' ? $regist['sitio'] . ' - Novedad PC From OET - ' . $mData['nom_contro'] : $regist['sitio'] . ' - ' . $regist["observ"]))
                            );

                            $mAditional["num_placax"] = $regist["placa"];
                            $mAditional["tip_noveda"] = "NC";
                            $mAditional['url_webser'] = $interfaz->interfaz[$i]['diwsdl'];
                            # Incluye la clase encargada de la interfaz         
                            include_once("../" . DIR_APLICA_CENTRAL . "/lib/InterfMct.inc");
                            $mMct = new InterfMct($this->conexion, $mDataMCT, $mAditional);
                            $mReturn = $mMct->getResponMct();

                            # Si la interfaz de MCT retorna error en validacion de datos
                            if ($mReturn["error"] == true) {
                                throw new Exception($mReturn["message"], 1);
                            }
                        } catch (Exception $e) {
                            $error_ = $e->getMessage();
                            if ("1000" != $e->faultcode) {
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora de la novedad: " . date("Y-m-d H:i", strtotime($regist["fecnov"])) . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " <br>";
                                $mMessage .= "Numero de manifiesto: " . $mDatDespac["cod_manifi"] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDatDespac["num_placax"] . " <br>";
                                $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " <br>";
                                $mMessage .= "Nombre puesto de control: " . $mData['nom_contro'] . " <br>";
                                $mMessage .= "Observacion.: " . $regist["observ"] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Cod Error: " . $e->faultcode . " <br>";
                                $mMessage .= "Error: " . $e->faultstring . " <br>";
                                $mMessage .= "Error Detallado: " . $result . " <br>";

                                mail(MAIL_SUPERVISORES . ", " . MAIL_SOPORTE, "Web service MCT Notas controlador Error Catch", '<div name="'.HOST_WEB.'_faro_19">' . $mMessage . '</div>', $mCabeceSoporte);
                            }
                        }
                    }
                    #</IF 5.1.1.1.10>
                    #</IF 5.1.1.1.11>
                    if (26 == $interfaz->interfaz[$i]["operad"] ) { //Interfaz con Carga Antioquia
                        try {

                                # Consulta del puesto Padre de la homologacion
                                $query = "SELECT  a.cod_contro
                                          FROM ".BASE_DATOS.".tab_homolo_pcxeal a 
                                         WHERE ( a.cod_homolo = '".$regist["contro"]."' OR a.cod_contro =  '".$regist["contro"]."' ) ";
                                $mControPadre = new Consulta($query, $this->conexion);
                                $mControPadre = $mControPadre->ret_matriz('a');


                                # Consulta PC, puesto control si no encontró nada homologado arriba -----------------
                                if( sizeof($mControPadre ) <= 0 )
                                {
                                    $mQuerySelNomPc = "SELECT cod_contro  
                                                   FROM ".BASE_DATOS.".tab_genera_contro  
                                                  WHERE cod_contro = '".$regist["contro"]."' ";

                                    $mControPadre = new Consulta($mQuerySelNomPc, $this->conexion);
                                    $mControPadre = $mControPadre->ret_matriz('a');
                                }

                                // Xml String porque se manda en POST como API, no es un WSDL (SOAP)
                                $mTextXML = '<?xml version="1.0" encoding="utf-8"?>
                                                <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://carga.local/">
                                                    <SOAP-ENV:Header>
                                                        <ns1:Credenciales>
                                                            <ns1:Username>'.$interfaz->interfaz[$i]['usuari'].'</ns1:Username>
                                                            <ns1:Password>'.$interfaz->interfaz[$i]['passwo'].'</ns1:Password>
                                                        </ns1:Credenciales>
                                                    </SOAP-ENV:Header>
                                                    <SOAP-ENV:Body>
                                                        <ns1:IngresarReporte>
                                                            <ns1:reporte>
                                                                <ns1:Manifiesto>'.$mDatDespac['cod_manifi'].'</ns1:Manifiesto>
                                                                <ns1:Placa>'.( substr($mDatDespac["num_placax"], 0,3)."-".substr($mDatDespac["num_placax"],3, 6) ).'</ns1:Placa>
                                                                <ns1:CodigoPuestoControlOET>'.$mControPadre[0]['cod_contro'].'</ns1:CodigoPuestoControlOET>
                                                                <ns1:FechaNovedad>'.$regist["fecnov"].'</ns1:FechaNovedad>
                                                                <ns1:Observacion>'.strip_tags($regist["observ"]).'</ns1:Observacion>
                                                                <ns1:Lugar>'.$regist['sitio'].'</ns1:Lugar>
                                                                <ns1:Sitio>'.($mIndEnSiti == false ? '0' : '1' ).'</ns1:Sitio>
                                                            </ns1:reporte>
                                                        </ns1:IngresarReporte>
                                                    </SOAP-ENV:Body>
                                                </SOAP-ENV:Envelope>';
                                
                                if(  $interfaz->interfaz[$i]['timtra'] == 1 ){
                                    $mFile = fopen(getcwd()."/logs/Carga_antioquia_STANDA_".$regist["nittra"]."_".date("Y_m_d").".txt", 'a+');
                                    fwrite($mFile, "------------------------ DATE ".date("Y-m-d H:i:s")." ------------------------ \n");
                                    fwrite($mFile, "Type      : NORMAL XML REQUEST \n"); 
                                    fwrite($mFile, "_POST     : ".json_encode($_POST)."  \n");
                                    fwrite($mFile, "datInterf : ".json_encode($interfaz->interfaz[$i])."  \n");
                                    fwrite($mFile, "Request   : ".$mTextXML."  \n");
                                    fclose($mFile);
                                }


                                $s = curl_init();
                                curl_setopt($s,CURLOPT_URL, $interfaz->interfaz[$i]['diwsdl']);
                                curl_setopt($s,CURLOPT_TIMEOUT,"14"); 
                                curl_setopt($s,CURLOPT_HTTPHEADER,array('Content-Type: text/xml')); 
                                curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
                                curl_setopt($s,CURLOPT_POST,true);
                                curl_setopt($s,CURLOPT_POSTFIELDS,$mTextXML);
                                // curl_setopt($s, CURLOPT_SSL_VERIFYHOST, 0);
                                // curl_setopt($s, CURLOPT_SSL_VERIFYPEER, 0);
                                $mResponse   = curl_exec($s);
                                $mHttpStatus = curl_getinfo($s,CURLINFO_HTTP_CODE);
                                curl_close($s);


                                // Si en la tabla interf_parame para ese cliente se tiene el parametro: val_timtra en 1, genera un log en satt_faro/logs/
                                if(  $interfaz->interfaz[$i]['timtra'] == 1 ){
                                    $mFile = fopen(getcwd()."/logs/Carga_antioquia_STANDA_".$regist["nittra"]."_".date("Y_m_d").".txt", 'a+');
                                    fwrite($mFile, "------------------------ DATE ".date("Y-m-d H:i:s")." ------------------------ \n");
                                    fwrite($mFile, "Type        : NORMAL XML RESPONSE  \n");
                                    fwrite($mFile, "Http Status : ".$mHttpStatus."  \n");
                                    fwrite($mFile, "Response    : ".$mResponse."  \n");
                                    fclose($mFile);
                                }

                                // Quita el namespace de las variables porque el simpleXML no lo puede leer, debe quedar un XML sencillo
                                $mResponse = str_replace(array('soap:'), array(''), $mResponse);
                                // Se consbierte el XML de respuesta en objeto para poderlo usar
                                $xmlObject = new SimpleXMLElement( $mResponse, 0, false);

                                // cuando se ejecuta una excepcion del WS pero en el externo, lo tomo para que entre al fault de nosotros
                                if($xmlObject -> Body -> Fault)
                                {
                                    throw new Exception($xmlObject -> Body -> Fault -> faultstring, 9999);
                                }

                                // se valida el codigo de retorno si la novedad fue registrada o no en el externo, -1 = error registrando la novedad en el externo y mando a catch
                                if($xmlObject -> Body -> IngresarReporteResponse -> IngresarReporteResult -> Codigo == '-1')
                                {
                                    throw new Exception($xmlObject -> Body -> IngresarReporteResponse -> IngresarReporteResult -> Descripcion, $xmlObject -> Body -> IngresarReporteResponse -> IngresarReporteResult -> Codigo);
                                }
                                /*
                                // Si en la tabla interf_parame para ese cliente se tiene el parametro: val_timtra en 1, genera un log en satt_faro/logs/
                                if(  $interfaz->interfaz[$i]['timtra'] == 1 ){
                                    $mFile = fopen(getcwd()."/logs/".$regist["nittra"]."_".date("Y_m_d").".txt", 'a+');
                                    fwrite($mFile, "------------------------ DATE ".date("Y-m-d H:i:s")." ------------------------ \n");
                                    fwrite($mFile, "Type    : NORMAL  \n");
                                    fwrite($mFile, "Request : ".$mTextXML."  \n");
                                    fwrite($mFile, "Response: ".$mResponse." \n\n");
                                    fclose($mFile);
                                }
                                */
                          
                        } catch (Exception $e) {

                            echo "<div align='center' style='color:#000000; padding:5px; font-size:14px; background-color:#FFAFAF; border:2px solid #000000;'>
                                    <b>Error en API - Insertar novedad en " . $interfaz->interfaz[$i]['nombre'] . ':' . $e->getMessage(). "
                                    </b>
                                  </div>";
                            if(  $interfaz->interfaz[$i]['timtra'] == 1 ){
                                    $mFile = fopen(getcwd()."/logs/Carga_antioquia_STANDA_".$regist["nittra"]."_".date("Y_m_d").".txt", 'a+');
                                    fwrite($mFile, "------------------------ DATE ".date("Y-m-d H:i:s")." ------------------------ \n");
                                    fwrite($mFile, "Type     : throw new Exception  \n");
                                    fwrite($mFile, "Request  : ".$mTextXML."  \n");
                                    fwrite($mFile, "Response : ".$mResponse." \n\n");
                                    fwrite($mFile, "Exception: ".var_dump($e)." \n\n");
                                    fclose($mFile);
                                }


                             
                                $mMessage = "******** Encabezado ******** <br>";
                                $mMessage .= "Fecha y hora de la novedad: " . date("Y-m-d H:i", strtotime($regist["fecnov"])) . " <br>";
                                $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " <br>";
                                $mMessage .= "Numero de manifiesto: " . $mDatDespac["cod_manifi"] . " <br>";
                                $mMessage .= "Placa del vehiculo: " . $mDatDespac["num_placax"] . " <br>";
                                $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " <br>";
                                $mMessage .= "Nombre puesto de control: " . $mData['nom_contro'] . " <br>";
                                $mMessage .= "Observacion.: " . $regist["observ"] . " <br>";
                                $mMessage .= "******** Detalle ******** <br>";
                                $mMessage .= "Cod Error: " . $e->getCode() . " <br>";
                                $mMessage .= "Error: " . $e->getMessage() . " <br>";
                                $mMessage .= "Error Detallado: " . $mResponse . " <br>";

                                mail(MAIL_SUPERVISORES . ", " . MAIL_SOPORTE, "Web service Carga Antioquia Notas controlador Error Catch", '<div name="'.HOST_WEB.'_faro_19">' . $mMessage . '</div>', $mCabeceSoporte);
                             
                        }
                    }
                    #</IF 5.1.1.1.11>

                }
                #</FOR 5.1.1.1>
            }
            #</IF 5.1.1>
        }
        #</IF 5>
    }

    /* ! \fn: sendMailNovGeneraAlarma
     *  \brief: Envia correo Novedades generan alarma
     *  \author: Ing. Fabian Salinas
     *  \date:  12/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \param: mDatDespac  Array    Datos basicos del despacho
     *  \param: mDatNoveda  Array    Datos basicos de la noveadad
     *  \return: 
     */

    private function sendMailNovGeneraAlarma($mDatRegist = null, $mDatDespac = null, $mDatNoveda = null, $mIndEnSiti = false) {
        $porapl['nittra'] = $mDatRegist['nittra'];
        $porapl['bdsata'] = BASE_DATOS;

        $despac_men['manifi'] = $mDatDespac['cod_manifi'];
        $despac_men['noveda'] = $mDatNoveda['nom_noveda'];
        $despac_men['placa'] = $mDatDespac['num_placax'];
        $despac_men['observ'] = $mDatRegist['observ'];
        $despac_men['contro'] = $mDatRegist['sitio'];
        $despac_men['fecnov'] = $mDatRegist['fecnov'];

        if ($mIndEnSiti == true) {
            $despac_men['tipnov'] = "Novedad Puesto de Control";
        } else {
            $despac_men['client'] = ''; #$despac['client'];
            $despac_men['remisi'] = ''; #$despac['remisi'];
            $despac_men['pedido'] = ''; #$despac['pedido'];
            $despac_men['origen'] = $mDatDespac['nom_ciuori'];
            $despac_men['destin'] = $mDatDespac['nom_ciudes'];
            $despac_men['transp'] = $mDatDespac['nom_transp'];
            $despac_men['tipnov'] = 'Nota de Controlador';
        }

        $MensajReport = new EnvMensaj();
        $MensajReport->EnvMensajCron($this->conexion, $porapl);
        $MensajReport->EjecPrecesNove($despac_men);
    }

    /* ! \fn: insertRecome
     *  \brief: Guarda las recomencaciones
     *  \author: Ing. Fabian Salinas
     *  \date: 13/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \return: 
     */

    private function insertRecome($mDatRegist = null) {
        $mSql = "SELECT a.num_condes 
                   FROM " . BASE_DATOS . ".tab_recome_asigna a 
                  WHERE a.num_despac = '$_REQUEST[num_despac]' 
               ORDER BY a.num_condes DESC 
                  LIMIT 1 ";
        $mConsulta = new Consulta($mSql, $this->conexion);
        $mConsRecome = $mConsulta->ret_arreglo();

        $mConsec = !$mConsRecome ? 0 : $mConsRecome[0];

        $mNumRecome = explode("|", $_REQUEST[num_recome]);

        foreach ($mNumRecome as $codRecome) {
            $mConsec++;
            $mSql = " INSERT INTO " . BASE_DATOS . ".tab_recome_asigna 
                            (num_condes, num_despac, cod_contro, 
                            cod_noveda, cod_rutasx, cod_recome, 
                            usr_solici, fec_solici ) 
                        VALUES 
                            ('$mConsec', '$_REQUEST[num_despac]', '$_REQUEST[cod_contro]', 
                            '$_REQUEST[novedad]', '$_REQUEST[rutax]', '$codRecome', 
                            '$mDatRegist[usuari]', NOW() ) ";
            $mInsertRecome = new Consulta($mSql, $this->conexion, "R");
        }
    }

    /* ! \fn: getDataSoap
     *  \brief: Trae datos adicionales para SOAP
     *  \author: Ing. Fabian Salinas
     *  \date:  19/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \return: Matriz
     */

    private function getDataSoap($mDatRegist = null) {
        //CONSULTAR URL WSDL.
        $mSql = " SELECT a.url_webser   
                    FROM satt_standa.tab_genera_server a 
              INNER JOIN " . BASE_DATOS . ".tab_transp_tipser b 
                      ON a.cod_server = b.cod_server 
                   WHERE b.cod_transp = '" . $mDatRegist['nittra'] . "' 
                ORDER BY b.fec_creaci DESC ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $url_webser = $mConsult->ret_matrix('i');
        $mResult['url_webser'] = $url_webser[0][0];

        #Nombre del puesto de control
        $mSql = "SELECT LOWER(nom_contro) AS nom_contro 
                   FROM " . BASE_DATOS . ".tab_genera_contro 
                  WHERE cod_contro = '" . $mDatRegist['contro'] . "' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $nom_contro = $mConsult->ret_matrix('i');
        $mResult['nom_contro'] = $nom_contro[0][0];

        $mSql = "SELECT cod_pcxbas 
                   FROM " . BASE_DATOS . ".tab_homolo_trafico 
                  WHERE cod_transp = '" . $mDatRegist['nittra'] . "'
                    AND cod_pcxfar = '" . $mDatRegist['contro'] . "'
                    AND cod_rutfar = '" . $mDatRegist['rutax'] . "' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult['cod_pcxbas'] = $mConsult->ret_matrix('i');

        $mSql = "SELECT a.cod_fwxxxx
                   FROM " . BASE_DATOS . ".tab_noveda_tranfw a 
                  WHERE a.cod_faroxx = " . $mDatRegist['noveda'];
        $mConsult = new Consulta($mSql, $this->conexion);
        $cod_fwxxxx = $mConsult->ret_matrix('i');
        $mResult['cod_fwxxxx'] = $cod_fwxxxx[0][0] == NULL ? '01' : $cod_fwxxxx[0][0];

        $mSql = " SELECT a.cod_alianz 
                    FROM " . BASE_DATOS . ".tab_config_alianz a 
                   WHERE cod_tercer = '" . $mDatRegist['nittra'] . "' 
                     AND ind_estado = '1' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $cod_alianz = $mConsult->ret_matrix('i');
        $mResult['cod_alianz'] = $cod_alianz[0][0];

        return $mResult;
    }

    /* ! \fn: getPCPadre
     *  \brief: Trae la informacion del PC Padre
     *  \author: Ing. Fabian Salinas
     *  \date:  19/11/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mDatRegist  Array    Datos de la novedad a registrar
     *  \return: Array
     */

    private function getPCPadre($mDatRegist = null) {
        $mSql = " SELECT a.cod_contro
                    FROM " . BASE_DATOS . ".tab_homolo_pcxeal a 
                   WHERE a.cod_homolo = '" . $mDatRegist['contro'] . "'
                      OR a.cod_contro = '" . $mDatRegist['contro'] . "' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mControPadre = $mConsult->ret_matrix('i');

        if (!$mControPadre)
            $mControPadre[0][0] = $mDatRegist['contro'];

        $mSql = " SELECT a.val_latitu, a.val_longit, a.nom_contro, 
                         a.cod_contro, 
                         IF(a.ind_virtua = 1, NULL, a.cod_contro) AS cod_puecon 
                    FROM " . BASE_DATOS . ".tab_genera_contro a 
                   WHERE a.cod_contro = '" . $mControPadre[0][0] . "' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        $mResult = $mConsult->ret_matrix('a');

        return $mResult[0];
    }

    /* ! \fn: sendMail
     *  \brief: funcion para enviar un email con una plantilla standar
     *  \author: Ing. Alexander Correa
     *  \date: 21/04/2016
     *  \date modified: dia/mes/año
     *  \param: $datos  => object => objeto con la informacion necesaria para enviar el Correo   
     *  \return booleano con el resuntado de la operacion
     */

    private function sendMail($datos) {
        if( $this->cod_transp  ) {
            if( in_array($this->cod_transp, self::$cLisTrans['MailNotHtml']) ) {
                return $this->sendMailNotHtml($datos);
            }
        }
        
        /*$cabeceras = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $cabeceras .= 'From: ' . MAIL_SUPERVISORES;*/
        $from = MAIL_SUPERVISORES;

        if($datos->rutpla != "")
        {
            $tmpl_file = $datos->rutpla;
        }
        else
        {
            $tmpl_file = '../' . DIR_APLICA_CENTRAL . '/planti/pla_noveda_especi.html';
        }

        $date = date("Y");
        $thefile = implode("", file($tmpl_file));
        $thefile = addslashes($thefile);
        $thefile = "\$r_file=\"" . $thefile . "\";";
        eval($thefile);
        $mHtmlxx = $r_file;

        return $this->mail($datos->mai_destin,  $datos->asunto . " - " . $datos->num_placax, '<html><head><title>AVANSAT GL</title></head><body><div name="' . $datos->div_namexx . '">' . $mHtmlxx . '</div></body></html>', $from);
    }

    /*! \fn: sendMailNotHtml
     *  \brief: Envia el correo sin formato html
     *  \author: Ing. Fabian Salinas
     *  \date: 16/08/2016
     *  \date modified: dd/mm/aaaa
     *  \param: datos
     *  \return: boolean
     */
    private function sendMailNotHtml($datos) {
        /*$cabeceras = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/plain; charset=UTF-8"' . "\r\n";
        $cabeceras .= 'From: ' . MAIL_SUPERVISORES . "\r\n";*/

        $from = MAIL_SUPERVISORES;

        $msj  = "Novedad: $datos->nom_noveda\r\n";
        $msj .= "Placa: $datos->num_placax\r\n";
        $msj .= "\r\nDatos Básicos del Despacho\r\n";
        $msj .= "No. Despacho AVANSAT GL: $datos->num_despac\r\n";
        $msj .= "Empresa: $datos->nom_transp\r\n";
        $msj .= "Número de Manifiesto: $datos->cod_manifi\r\n";
        $msj .= "Origen: $datos->nom_ciuori\r\n";
        $msj .= "Destino: $datos->nom_ciudes\r\n";
        $msj .= "\r\nDatos Del Conductor del Vehículo\r\n";
        $msj .= "Conductor: $datos->nom_conduc\r\n";
        $msj .= "Celular: $datos->con_telmov\r\n";
        $msj .= "\r\nDatos Novedad\r\n";
        $msj .= "Fecha y Hora: $datos->fec_noveda\r\n";
        $msj .= "Ubicación: $datos->nom_sitiox\r\n";
        $msj .= "Observación: $datos->obs_noveda\r\n";
        $msj .= "Asignado a: $datos->ema_asigna\r\n";
        $msj .= "Asignado por: $datos->usr_creaci\r\n";

        return $this->mail($datos->mai_destin,  $datos->asunto . " - " . $datos->num_placax, $msj, $from);
    }

    /*! \fn: mail
     *  \brief: Envia el correo al destinatario segun el ambiente
     *  \author: Ing. Fabian Salinas
     *  \date: 16/08/2016
     *  \date modified: dd/mm/aaaa
     *  \param: destin
     *  \return: boolean
     */
    private function mail($destin, $asunto, $msj, $from) {
        @require_once('../' . DIR_APLICA_CENTRAL . '/planti/class.phpmailer.php');

        $mail = new PHPMailer();
        $mail->Host = "localhost";
        $mail->From = $from;
        $mail->FromName = "Centro Logistico FARO";
        $mail->Subject = $asunto;
        $mail->Body = $msj;
        $mail->IsHTML( true );

        if (HOST_WEB == HOST_WEB_PRO) {
            $mail->AddAddress( $destin );
            //return mail($destin, $asunto, $msj, $cabeceras);
        } else {
            $mail->AddAddress( "luis.manrique@eltransporte.org, maribel.garcia@eltransporte.org" );
            //return mail("luis.manrique@eltransporte.org", $asunto, $msj, $cabeceras);
        }

        if($mail->Send()){
          return true;
        }else{
          echo 'Error: ' . $mail->ErrorInfo;
          return false;
        }
    }

     /*! \fn: updateEstadoEnviadoTms
     *  \brief: actualiza el estado de la novedad enviada a tms
     *  \author: Ing. Cristian Andr�s Torres
     *  \date: 03/08/2022
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return:
     */
    public function updateEstadoEnviadoTms($mData, $mIndEnSiti = false) {
        $tabla = 'tab_despac_contro';
        if($mIndEnSiti){
            $tabla = 'tab_despac_noveda';
        }
        $mSql = "UPDATE 
                        ".BASE_DATOS.".".$tabla."
                SET 
                    ind_enviad = 1 
                WHERE 
                    num_despac = '".$mData['despac']."' 
                    AND cod_contro = '".$mData['contro']."'
                    AND cod_rutasx = '".$mData['rutax']."' 
                    AND cod_noveda = '".$mData['noveda']."' 
                    AND fec_noveda = '".$mData['fecnov']."'";
        new Consulta($mSql, $this->conexion);     
    }

     /*! \fn: reenviaNovedadesAvansat
     *  \brief: reenvia las novedades de un cliente gl a avansat
     *  \author: Ing. Cristian Andr�s Torres
     *  \date: 03/08/2022
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: boolean
     */
    public function reenviaNovedadesAvansat($num_despac) {
        try 
		{ 
            $cReportes = [];
            $cRespuest = [];
            $mQuery = "
                                (
                                    SELECT
                                            'pc' AS metodo, cc.num_consec, cc.cod_transp, cc.cod_server, dd.nom_server, dd.url_webser, ee.nom_operad, ee.nom_usuari, ee.clv_usuari,
                                            a.num_despac, aa.cod_manifi, bb.num_placax,
                                            a.cod_contro, a.fec_noveda, a.des_noveda, a.tiem_duraci, a.cod_sitiox, c.nom_sitiox, a.cod_noveda, f.nom_noveda, f.ind_alarma,f.ind_tiempo, f.nov_especi,f.ind_manala,  
                                            '' AS val_longit, '' AS val_latitu, 'tab_despac_noveda' AS origen, a.fec_creaci
                                    FROM
                                            ".BASE_DATOS.".tab_despac_despac aa
                                INNER JOIN ".BASE_DATOS.".tab_despac_vehige bb ON aa.num_despac = bb.num_despac
                                INNER JOIN ".BASE_DATOS.".tab_transp_tipser cc ON bb.cod_transp = cc.cod_transp AND cc.num_consec = (
                                                                                                                                        SELECT MAX(xx.num_consec) AS num_consec
                                                                                                                                        FROM ".BASE_DATOS.".tab_transp_tipser xx
                                                                                                                                        WHERE xx.cod_transp = bb.cod_transp
                                                                                                                                    )
                                INNER JOIN ".BD_STANDA.".tab_genera_server  dd  ON cc.cod_server = dd.cod_server
                                INNER JOIN ".BASE_DATOS.".tab_interf_parame ee  ON cc.cod_transp = ee.cod_transp 
                                                                                AND ee.cod_operad = 50 /*Debe tener interfaz con TMS */
                                                                                AND ee.ind_estado = 1 /*Debe estar activa*/


                                INNER JOIN	".BASE_DATOS.".tab_despac_noveda a  ON bb.num_despac =  a.num_despac
                                LEFT JOIN ".BASE_DATOS.".tab_errorx_noveda b  ON a.num_despac !=  b.num_despac 
                                                                            AND a.cod_contro !=  b.cod_contro
                                LEFT JOIN ".BASE_DATOS.".tab_despac_sitio  c  ON a.cod_sitiox  =  c.cod_sitiox
                                LEFT JOIN ".BASE_DATOS.".tab_genera_noveda f ON a.cod_noveda  =  f.cod_noveda

                                    WHERE a.num_despac = '".$num_despac."'
                                        AND	a.ind_enviad = 0
                                        AND aa.ind_anulad = 'R'
                                        AND aa.ind_planru LIKE 'S'
                                        AND aa.fec_salida IS NOT NULL
                                        AND aa.fec_llegad IS NULL  
                                        AND dd.cod_server IN (14,16,17,18,19,20,21)
                                        AND DATE(a.fec_creaci) = DATE(NOW())
                                )
                                UNION ALL
                                (
                                    SELECT
                                            'nc' AS metodo, cc.num_consec, cc.cod_transp, cc.cod_server, dd.nom_server, dd.url_webser, ee.nom_operad, ee.nom_usuari, ee.clv_usuari,
                                            a.num_despac, aa.cod_manifi, bb.num_placax, 
                                            a.cod_contro, a.fec_contro AS fec_noveda, a.obs_contro AS des_noveda, a.tiem_duraci, a.cod_sitiox, c.nom_sitiox, a.cod_noveda, f.nom_noveda, f.ind_alarma,f.ind_tiempo, f.nov_especi,f.ind_manala, 
                                            a.val_longit, a.val_latitu, 'tab_despac_contro' AS origen, a.fec_creaci
                                    FROM
                                            ".BASE_DATOS.".tab_despac_despac aa
                                INNER JOIN ".BASE_DATOS.".tab_despac_vehige bb ON aa.num_despac = bb.num_despac
                                INNER JOIN ".BASE_DATOS.".tab_transp_tipser cc ON bb.cod_transp = cc.cod_transp AND cc.num_consec = (
                                                                                                                                        SELECT MAX(xx.num_consec) AS num_consec
                                                                                                                                        FROM ".BASE_DATOS.".tab_transp_tipser xx
                                                                                                                                        WHERE xx.cod_transp = bb.cod_transp
                                                                                                                                    )
                                INNER JOIN ".BD_STANDA.".tab_genera_server  dd  ON cc.cod_server = dd.cod_server
                                INNER JOIN ".BASE_DATOS.".tab_interf_parame ee  ON cc.cod_transp = ee.cod_transp 
                                                                                AND ee.cod_operad = 50 /*Debe tener interfaz con TMS */
                                                                                AND ee.ind_estado = 1 /*Debe estar activa*/


                                INNER JOIN ".BASE_DATOS.".tab_despac_contro a  ON bb.num_despac =  a.num_despac
                                LEFT JOIN ".BASE_DATOS.".tab_errorx_noveda b  ON a.num_despac != b.num_despac 
                                                                            AND a.cod_contro != b.cod_contro
                                LEFT JOIN ".BASE_DATOS.".tab_despac_sitio  c  ON a.cod_sitiox = c.cod_sitiox
                                LEFT JOIN ".BASE_DATOS.".tab_genera_noveda f ON a.cod_noveda  =  f.cod_noveda

                                    WHERE
                                            a.num_despac = '".$num_despac."'
                                        AND	a.ind_enviad = 0
                                        AND aa.ind_anulad = 'R'
                                        AND aa.ind_planru LIKE 'S'
                                        AND aa.fec_salida IS NOT NULL
                                        AND aa.fec_llegad IS NULL  
                                        AND dd.cod_server IN (14,16,17,18,19,20,21)
                                        AND DATE(a.fec_creaci) = DATE(NOW())
                                        GROUP BY aa.num_despac, a.fec_creaci
                                )
                                ORDER BY cod_server
                        ";
            $mConsult = new Consulta($mQuery, $this->conexion);
            $cNovPendientes = $mConsult->ret_matrix('a');
            $errores = '';
            if(sizeof($cNovPendientes) > 0){
                foreach ($cNovPendientes  AS $mIndex => $mReporte) 
			    {
                    $mDesNoveda = '';
                    if( $mReporte['val_latitu']  != '' && $mReporte['val_longit']  != '')
                    {
                        $mDesNoveda  =  ' ^{"latitud":"'.$mReporte['val_latitu'].'", "longitud":"'.$mReporte['val_longit'].'"}';
                    }
                    else
                    {
                        $mDesNoveda  = 'Coordenadas: SIN COORDENADAS ';
                    }
                    $mParams =  [ 
                      'nom_usuari'  => $mReporte['nom_usuari'],
                      'pwd_clavex'  => $mReporte['clv_usuari'],
                      'nom_aplica'  => $mReporte['nom_operad'],
                      'num_manifi'  => $mReporte['cod_manifi'],
                      'num_placax'  => $mReporte['num_placax'],
                      'cod_novbas'  => $mReporte['cod_noveda'] >= 9000 ? $mReporte['cod_noveda'] : '0',//$mReporte['cod_noveda'], //9183,
                      'cod_conbas'  => 0,
                      'tim_duraci'  => $mReporte['tiem_duraci'], 
                      'fec_noveda'  => $mReporte['fec_noveda'],
                      'des_noveda'  => $mReporte['des_noveda']." ".$mDesNoveda,
                      'nom_contro'  => NULL,
                      'nom_sitiox'  => $mReporte['nom_sitiox'],
                      'cod_confar'  => $mReporte['cod_contro'],
                      'cod_novfar'  => $mReporte['cod_noveda'] >= 9000 ? NULL : $mReporte['cod_noveda'], 
                      'nom_noveda'  => $mReporte['cod_noveda'] >= 9000 ? NULL : $mReporte['nom_noveda'], 
                      'ind_alarma'  => $mReporte['cod_noveda'] >= 9000 ? NULL : $mReporte['ind_alarma'],
                      'ind_tiempo'  => $mReporte['cod_noveda'] >= 9000 ? NULL : $mReporte['ind_tiempo'],
                      'nov_especi_' => $mReporte['cod_noveda'] >= 9000 ? NULL : $mReporte['nov_especi'],
                      'ind_manala'  => $mReporte['cod_noveda'] >= 9000 ? NULL : $mReporte['ind_manala'],
                      'bin_fotcon'  => NULL,
                      'bin_fotpre'  => NULL,
                      'cod_remdes'  => NULL,
                      'tim_sigpun'  => NULL,
                      'tim_ultpun'  => NULL,
                      'kms_vehicu'  => $mReporte['val_veloci'],
                      'man_ordcar'  => NULL
                  ];

                    ini_set("soap.wsdl_cache_enabled", 0);
                    //die('acaba proceso');
                    $mSoap = new SoapClient( $mReporte['url_webser'], ['trace' => 1,'exception' => 1 ] );
                    $mResponse = $mSoap -> __soapCall( 'setNovedadNC', $mParams );

                    $mResult  = explode( "; ", $mResponse );
                    $mCodResp = explode( ":", $mResult[0] );  
                    $mMsgResp = explode( ":", $mResult[1] );
                    if( "1000" != $mCodResp[1] ){
                        $errores .= "<p>Despacho: ".$mReporte['num_despac']." - Manifiesto: ".$mReporte['cod_manifi']." - Ws: ".$mReporte['url_webser']." --Error::".$mMsgResp[1]."-- </p>";
                        $msj = 'El despacho esta presentando inconveniente al reenviar las novedades';
				    }else{
                        $msj = 'Se realizo el reenvio de novedades de manera exitosa';
                    }

                    //Actualiza el registro de la novedad indicando que ya fue enviado
                    $mUpdate = "UPDATE ".BASE_DATOS.".".$mReporte['origen']." SET ind_enviad = 1 WHERE num_despac = ". $mReporte['num_despac']." AND  fec_creaci = '".$mReporte['fec_creaci']."' ";
                    $transaccion = 'R';
                    if($mIndex==0){
                        $transaccion = 'BR';
                    }
                    if(count($cNovPendientes)-1 == $mIndex){
                        $transaccion = 'RC';
                    }
                    new Consulta($mUpdate, $this->conexion, $transaccion);
                }
                
                $cRespuest['info'] = $errores;
                $cRespuest['noveda'] = $cReportes;
                $cRespuest['status'] = true;
                $cRespuest['msj'] = $msj;
                $cRespuest['title'] = '�Exito!';
                $cRespuest['type'] = 'success';
                
            }else{
                $cRespuest['status'] = true;
                $cRespuest['msj'] = 'No se han encontrado novedades por enviar.';
                $cRespuest['title'] = 'Alerta';
                $cRespuest['type'] = 'warning';
            }
                return $cRespuest;
        } catch (Exception $e) {
                $cRespuest['status'] = false;
                $cRespuest['msj'] = 'Error en el m�todo reenviaNovedadesAvansat. Por favor contacte a mesa de apoyo.';
                $cRespuest['title'] = 'Error';
                $cRespuest['type'] = 'error';
                return $cRespuest;
        }                      
    }

}

?>