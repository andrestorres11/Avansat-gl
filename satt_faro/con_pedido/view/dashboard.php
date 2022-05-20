<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

/* ! \file: index.php
 *  \brief: Clase que genera la interfaz principal de consulta
 *  \author: Ing. Luis Manrique
 *  \version: 1.0
 *  \date: 04/09/2020
 *  \warning:
 */
    class Dashboard
    {
        var $conexion  = NULL;
       
        function __construct()
        {
            //Archivos necesarios
            session_start();
            require_once "../../constantes.inc";
            require_once "../../../".DIR_APLICA_CENTRAL."/lib/general/constantes.inc";
            require_once "../../../".DIR_APLICA_CENTRAL."/lib/general/tabla_lib.inc";
            require_once "../../../".DIR_APLICA_CENTRAL."/lib/general/conexion_lib.inc";
            $this->conexion = new Conexion( HOST, USUARIO, CLAVE, BASE_DATOS, BD_STANDA );
            //echo ;
            //self::validateCaptcha($_REQUEST);
            
            self::validate();
        }

        protected function validate(){
            if(!isset($_SESSION['num_pedido']) AND !isset($_SESSION['num_linea'])){
                $error_message = "Vuelva a intentar la busqueda";
                header("Location: ".explode("?", $_SERVER['HTTP_REFERER'])[0]."?message_bus=".$error_message);
            }else{
                self::interfaz();
            }
        }
        /*! \fn: style
         *  \brief: Recopila los archivos necesarios de estilos
         *  \author: Ing. Luis Manrique
         *  \date: 04/09/2020
         *  \date modified: dd/mm/aaaa
         *  \return: HTML
         */
        protected function style(){
            $randon = rand(1111,9999);
            $style = ' <link rel="stylesheet" href="../assets/lib/bootstrap4/css/bootstrap.css">
            <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/dist/css/AdminLTE.min.css">
            <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/dist/css/skins/_all-skins.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/css/adminlte.min.css" integrity="sha256-tDEOZyJ9BuKWB+BOSc6dE4cI0uNznodJMx11eWZ7jJ4=" crossorigin="anonymous" />
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/css/alt/adminlte.plugins.min.css" integrity="sha256-K/rXKcrvSBsdB8WjaU78Ga+3bqjOZ0oyKQ2hpOb2OgU=" crossorigin="anonymous" />
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/css/alt/adminlte.extra-components.min.css" integrity="sha256-NQaR4VO2vLNDjoagWSYPEuUeqU5U7X1bdqJJiQsrmn0=" crossorigin="anonymous" />
            <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
            <link href="../assets/css/style.css?rand='.$randon.'" type="text/css" rel="stylesheet" />
            <!--<link rel="stylesheet" href="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/css/ol.css" type="text/css">-->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
            <link href="../assets/css/ol.css" type="text/css" rel="stylesheet" />
            <link href="../assets/css/timeline_style.css" type="text/css" rel="stylesheet" />
            <link href="../assets/lib/EasyZoom/css/pygments.css" type="text/css" rel="stylesheet" />
            <link href="../assets/lib/EasyZoom/css/easyzoom.css" type="text/css" rel="stylesheet" />';
            echo $style;
        }

        
        /*! \fn: style
         *  \brief: Recopila los archivos necesarios de JavaScript
         *  \author: Ing. Luis Manrique
         *  \date: 04/09/2020
         *  \date modified: dd/mm/aaaa
         *  \return: HTML
         */
        protected function script(){
            $script = '<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
            <script src="../assets/lib/bootstrap4/js/bootstrap.js"></script>
              <!-- AdminLTE App -->
            <script type="text/javascript" language="JavaScript" src="https://adminlte.io/themes/AdminLTE/dist/js/adminlte.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/js/adminlte.min.js" integrity="sha256-Utchz0cr9Hjt+G0gl1YbXb8P2mNugSxobc9AXUfreHc=" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/js/pages/dashboard3.min.js" integrity="sha256-bf6XNqDnwX4g6QZx934mr8BFaRNtjY2Vs88YsjZi9QY=" crossorigin="anonymous"></script>
            <!-- <script src="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/build/ol.js"></script>-->
            <script src="../assets/js/ol.js"></script>
            <script src="../assets/js/multiple.js"></script>
            <script src="../assets/lib/EasyZoom/dist/easyzoom.js"></script>
            <script src="../assets/lib/moment/moment.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
            <script src="../assets/js/dashboard.js"></script>';
            echo $script;
        }

        protected function darInformacionPedido($num_pedido,$num_linea){
          @include_once('../../../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc');
          $nit_transp = getInfoEmpresa($this->conexion)['cod_emptra'];
          $mSql = "SELECT b.nom_ciudad as 'ciu_origen',
                        a.fec_horaxx_pedido as 'fec_origen',
                        c.nom_remdes as 'nom_origen',
                        c.dir_remdes as 'dir_origen',
                        c.num_telefo as 'tel_origen',
                        c.num_remdes as 'doc_origen',
                        d.nom_ciudad as 'ciu_destin',
                        a.nombre_cliente as 'nom_destin',
                        e.dir_remdes as 'dir_destin',
                        e.num_telefo as 'num_destin',
                        e.num_remdes as 'doc_destin',
                        a.num_despac
                        FROM ".BASE_DATOS.".tab_genera_pedido a
                   INNER JOIN ".BASE_DATOS.".tab_genera_ciudad b ON
                                    a.mun_origen = b.cod_ciudad
                   INNER JOIN ".BASE_DATOS.".tab_genera_remdes c
                      ON c.cod_transp = '".$nit_transp."'
                   INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d ON
                   a.mun_enviox = d.cod_ciudad AND a.dep_enviox = d.cod_depart
                   INNER JOIN ".BASE_DATOS.".tab_genera_remdes e
                      ON e.cod_remdes = a.cliente
                    WHERE a.num_pedido = '".$num_pedido."' AND a.num_lineax = '".$num_linea."' ;";
          $mConsult = new Consulta($mSql, $this -> conexion);
          $mData = $mConsult->ret_arreglo();
          return $mData;
        }


      /*! \fn: getNovedad
     *  \brief: Trae las Novedades de un Despacho
     *  \author: Ing. Fabian Salinas
     *  \date: 
     *  \date modified: dd/mm/aaaa
     *  \param: mNumDespac   Integer   Numero de despacho
     *  \param: mIndGPS      String    Aplica Inner Join o Left Join
     *  \return: Matriz
     */
    private function getNovedad( $mNumDespac , $mIndGPS = 'INNER')
    {
      $mSql = " (      SELECT a.fec_contro AS fec_noveda, 
                              UPPER(b.nom_noveda) AS nom_noveda, 
                              a.usr_creaci, a.fec_creaci, 
                              UPPER( IF(c.nom_sitiox = NULL, d.nom_contro, c.nom_sitiox) ) AS nom_sitiox, 
                              a.obs_contro AS obs_noveda, 
                              b.nov_especi, a.cod_noveda, 
                              '1' AS tab_origen, a.cod_consec, 
                              a.cod_contro, '0' AS ind_ensiti, 
                              b.ind_fuepla, a.tiem_duraci, 
                              b.cod_etapax, b.ind_limpio, 
                              b.ind_manala, 
                              a.val_longit, a.val_latitu
                         FROM ".BASE_DATOS.".tab_despac_contro a 
                   INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                           ON a.cod_noveda = b.cod_noveda 
                   $mIndGPS JOIN ".BASE_DATOS.".tab_despac_sitio c 
                           ON a.cod_sitiox = c.cod_sitiox 
                   INNER JOIN ".BASE_DATOS.".tab_genera_contro d 
                           ON a.cod_contro = d.cod_contro 
                        WHERE a.num_despac = '{$mNumDespac}' 
                )
                UNION 
                (
                       SELECT a.fec_noveda, 
                              UPPER(b.nom_noveda) AS nom_noveda,
                              a.usr_creaci, 
                              a.fec_creaci,  
                              UPPER(c.nom_contro) AS nom_sitiox, 
                              a.des_noveda AS obs_noveda, 
                              b.nov_especi, a.cod_noveda, 
                              '2' AS tab_origen, '1' AS cod_consec, 
                              a.cod_contro, '1' AS ind_ensiti, 
                              b.ind_fuepla, a.tiem_duraci, 
                              b.cod_etapax, b.ind_limpio, 
                              b.ind_manala,
                              '' AS val_longit, '' AS val_latitu
                         FROM ".BASE_DATOS.".tab_despac_noveda a 
                   INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                           ON a.cod_noveda = b.cod_noveda 
                   INNER JOIN ".BASE_DATOS.".tab_genera_contro c 
                           ON a.cod_contro = c.cod_contro 
                        WHERE a.num_despac = '{$mNumDespac}' 
                )
                UNION
                (
                       SELECT a.fec_solici AS fec_noveda, 
                              c.nom_noveda,
                              a.usr_solici AS usr_creaci, 
                              a.fec_solici AS fec_creaci, 
                              UPPER(b.nom_contro) AS nom_sitiox, 
                              GROUP_CONCAT( d.tex_encabe ) AS obs_noveda, 
                              '0' AS nov_especi, a.cod_noveda, 
                              '3' AS tab_origen, d.cod_consec, 
                              a.cod_contro, '0' AS ind_ensiti, 
                              c.ind_fuepla, '' AS tiem_duraci, 
                              c.cod_etapax, '0' AS ind_limpio, 
                              c.ind_manala,
                              '' AS val_longit, '' AS val_latitu
                         FROM ".BASE_DATOS.".tab_recome_asigna a 
                   INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
                           ON a.cod_contro = b.cod_contro 
                   INNER JOIN ".BASE_DATOS.".tab_genera_noveda c 
                           ON a.cod_noveda = c.cod_noveda 
                   INNER JOIN ".BASE_DATOS.".tab_genera_recome d 
                           ON a.cod_recome = d.cod_consec 
                        WHERE a.num_despac = '{$mNumDespac}' 
                     GROUP BY a.cod_contro 
                )
                UNION
                (
                       SELECT a.fec_ejecut AS fec_noveda, 
                              'SOLUCION RECOMENDACION' AS nom_noveda, 
                              a.usr_ejecut AS usr_creaci,
                              a.fec_ejecut AS fec_creaci, 
                              UPPER(b.nom_contro) AS nom_sitiox, 
                              GROUP_CONCAT( CONCAT(d.tex_encabe, ': ', a.obs_ejecuc) ) AS obs_noveda, 
                              '0' AS nov_especi, a.cod_noveda, 
                              '4' AS tab_origen, d.cod_consec, 
                              a.cod_contro, '1' AS ind_ensiti, 
                              c.ind_fuepla, '' AS tiem_duraci, 
                              c.cod_etapax, '1' AS ind_limpio, 
                              c.ind_manala, 
                              '' AS val_longit, '' AS val_latitu
                         FROM ".BASE_DATOS.".tab_recome_asigna a 
                   INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
                           ON a.cod_contro = b.cod_contro 
                   INNER JOIN ".BASE_DATOS.".tab_genera_noveda c 
                           ON a.cod_noveda = c.cod_noveda 
                   INNER JOIN ".BASE_DATOS.".tab_genera_recome d 
                           ON a.cod_recome = d.cod_consec 
                        WHERE a.num_despac = '{$mNumDespac}' 
                          AND a.obs_ejecuc IS NOT NULL 
                     GROUP BY a.cod_contro 
                ) 
                ORDER BY fec_creaci ASC ";

      $mConsult = new Consulta( $mSql, $this -> conexion );
      return $mConsult -> ret_matrix('a');
    }


    /*! \fn: getRemDesDespac
     *  \brief: Trae los remietentes y destinatarios de un despacho
     *  \author: Ing. Edgar Felipe Clavijo Santoyo
     *  \date: 05/06/2019
     *  \date modified: dd/mm/aaaa
     *  \param: mNumDespac   Integer   Numero de despacho
     *  \return: Matriz
     */
    private function getRemDesDespac( $mNumDespac)
    {
      $mSql = " 
            (
                SELECT
                    c.cod_latitu AS `val_latitu`,
                    c.cod_longit AS `val_longit`,
                    CONCAT(b.fec_citcar, ' ', b.fec_citcar) AS `fec_noveda`,
                    c.nom_remdes AS `obs_noveda`,
                    'C' AS `tip_pointx`
                FROM
                    ".BASE_DATOS.".tab_despac_despac a
                    INNER JOIN ".BASE_DATOS.".tab_despac_corona b ON b.num_dessat = a.num_despac
                    INNER JOIN ".BASE_DATOS.".tab_genera_remdes c ON c.cod_remdes = b.nom_sitcar
                WHERE
                    a.num_despac = '" . $mNumDespac . "'
            )
            UNION
            (
                SELECT
                    c.cod_latitu AS `val_latitu`,
                    c.cod_longit AS `val_longit`,
                    CONCAT(b.fec_citdes, ' ', b.hor_citdes)AS `fec_noveda`,
                    c.nom_remdes AS `obs_noveda`,
                    IF(
                        b.fec_cumdes IS NOT NULL AND b.ind_cumdes IS NOT NULL,
                        'D2',
                        'D1'
                    ) AS `tip_pointx`
                FROM
                    ".BASE_DATOS.".tab_despac_despac a
                    INNER JOIN ".BASE_DATOS.".tab_despac_destin b ON b.num_despac = a.num_despac
                    INNER JOIN ".BASE_DATOS.".tab_genera_remdes c ON c.cod_remdes = b.cod_remdes
                WHERE
                    a.num_despac = '" . $mNumDespac . "'
                ORDER BY
                    CONCAT(b.fec_citdes, ' ', b.hor_citdes) ASC
            )
      ";

      $mConsult = new Consulta( $mSql, $this -> conexion );
      return $mConsult -> ret_matrix('a');
    }


     /*! \fn: getPuntFisDespac
     *  \brief: Trae Informacion de los puntos fisicos respecto al despacho
     *  \author: Ing. Luis Carlos Manrique Boada
     *  \date: 16/07/2019
     *  \date modified: dd/mm/aaaa
     *  \param: mNumDespac   Integer   Numero de despacho
     *  \return: Matriz
     */
    private function getPuntFisDespac( $mNumDespac)
    {
      $mSql = " 
      SELECT
            c.val_latitu AS val_longit,
            c.val_longit AS val_latitu,
            (b.fec_alarma) AS `fec_noveda`,
            c.nom_contro AS `obs_noveda`,
            'O' AS `tip_pointx`
        FROM
            ".BASE_DATOS.".tab_despac_despac a
            INNER JOIN ".BASE_DATOS.".tab_despac_seguim b ON b.num_despac = a.num_despac
            INNER JOIN ".BASE_DATOS.".tab_genera_contro c ON c.cod_contro = b.cod_contro
        WHERE
            c.ind_virtua = '0' AND
            a.num_despac = '" . $mNumDespac . "' 
      ";

      $mConsult = new Consulta( $mSql, $this -> conexion );
      return $mConsult -> ret_matrix('a');
    }

    /*! \fn: sanear_string
     *  \brief: quita acentos del string enviado
     *  \author: Ing. Miguel Romero
     *    \date: 04/03/2015
     *    \date modified: dia/mes/ano
     *  \param: string  = string a sanear
     *  \return string sin acentos
     */
    private function sanear_string($string)
    {
        $string = (string)trim(utf8_encode($string));
     
        $string = str_replace(
            array('ÃÂÃÂ¡', 'ÃÂÃÂ ', 'ÃÂÃÂ¤', 'ÃÂÃÂ¢', 'ÃÂÃÂª', 'ÃÂ?', 'ÃÂÃ¢ÂÂ¬', 'ÃÂÃ¢ÂÂ', 'ÃÂÃ¢ÂÂ'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
     
        $string = str_replace(
            array('ÃÂÃÂ©', 'ÃÂÃÂ¨', 'ÃÂÃÂ«', 'ÃÂÃÂª', 'ÃÂÃ¢ÂÂ°', 'ÃÂÃÂ', 'ÃÂÃÂ ', 'ÃÂÃ¢ÂÂ¹'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
     
        $string = str_replace(
            array('ÃÂÃÂ­', 'ÃÂÃÂ¬', 'ÃÂÃÂ¯', 'ÃÂÃÂ®', 'ÃÂ?', 'ÃÂÃÂ', 'ÃÂ?', 'ÃÂÃÂ½'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
     
        $string = str_replace(
            array('ÃÂÃÂ³', 'ÃÂÃÂ²', 'ÃÂÃÂ¶', 'ÃÂÃÂ´', 'ÃÂÃ¢ÂÂ', 'ÃÂÃ¢ÂÂ', 'ÃÂÃ¢ÂÂ', 'ÃÂÃ¢ÂÂ'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
     
        $string = str_replace(
            array('ÃÂÃÂº', 'ÃÂÃÂ¹', 'ÃÂÃÂ¼', 'ÃÂÃÂ»', 'ÃÂÃÂ¡', 'ÃÂÃ¢ÂÂ¢', 'ÃÂÃ¢ÂÂº', 'ÃÂÃÂ'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
     
        $string = str_replace(
            array('ÃÂÃÂ±', 'ÃÂÃ¢ÂÂ', 'ÃÂÃÂ§', 'ÃÂÃ¢ÂÂ¡'),
            array('n', 'N', 'c', 'C',),
            $string
        );
     
        //Esta parte se encarga de eliminar cualquier caracter extrano
        $string = str_replace(
            array("ÃÂ·", "$", "%", "&", "/",
                "(", ")", "?", "'", "ÃÂ¡",
                "ÃÂ¿", "[", "^", "<code>", "]",
                "+", "}", "{", "ÃÂ¨", "ÃÂ´"),
                '',
            $string
        );
        $string = str_replace( array('VÃÂÃÂ­a'), array('via'), $string);
     
        return $string;
    }

    

    /*! \fn: validaDivMapas
     *  \brief:  valida si el despacho tiene novedades de GPS 4999
     *  \author: Ing. Miguel Romero
     *    \date: 04/03/2016
     *    \date modified: dia/mes/ano
     *  \param: mNumDespac
     *  \return arreglo con las novedades y el estado de la novedad
     */
    
    private function validaDivMapas($mNumDespac){
        $novedades = self::getNovedad($mNumDespac , 'LEFT');
        $remdes = self::getRemDesDespac($mNumDespac);
        $puntFis = self::getPuntFisDespac($mNumDespac);

        $flag = 0;
        $latLon = array( ); 

        foreach ($novedades as $key => $value) {
  
            $value['obs_noveda'] = self::sanear_string( $value['obs_noveda'] );
            
            if($value['val_longit'] != '' && $value['val_latitu'] != ''){
                $latLon[] = array(
                        "val_latitu" => $value['val_latitu'],
                        "val_longit" => $value['val_longit'],
                        "fec_noveda" => $value['fec_noveda'],
                        "obs_noveda" => $value['obs_noveda'],
                        "tip_pointx" => "N"
                    );

                $flag = 1;
            }
        }

        foreach ($remdes as $key => $value) {
            if($value['val_longit'] != '' && $value['val_latitu'] != ''){

                //Go through data to clean it
                foreach($value as $key1 => $value1){
                    $value[$key1] = utf8_encode($value1);
                } 
                $latLon[] = $value;
            }
        }

        foreach ($puntFis as $key => $value) {
            if($value['val_longit'] != '' && $value['val_latitu'] != ''){

                //Go through data to clean it
                foreach($value as $key1 => $value1){
                    $value[$key1] = utf8_encode($value1);
                } 
                $latLon[] = $value;
            }
        }

        //echo "<pre>"; print_r($latLon); echo "</per>";

        return array('ind_estado' => $flag , 'data' => $latLon);
    }


    public function darNovedadRemisionTime($num_pedido,$num_linea){
        $mSql = "(
            SELECT 
                c.cod_destra, 
                c.nom_eclhoe AS nombre, 
                c.obs_hompro AS observacion, 
                c.fec_creaci AS fechaRegistro, 
                c.cod_noveda AS cod_estado 
            FROM 
                tab_despac_destin b 
                INNER JOIN tab_despac_tracki c ON b.num_despac = c.num_despac 
                INNER JOIN tab_despac_vehige d ON d.num_despac = b.num_despac 
                AND (b.ped_remisi = '".$num_pedido."-".$num_linea."') 
                AND c.nom_eclhoe IS NOT NULL 
                INNER JOIN tab_homolo_estnov e ON e.cod_noveda = c.cod_noveda 
            WHERE 
                d.ind_activo = 'S' 
            GROUP BY 
                fechaRegistro
        ) 
        UNION 
            (
                SELECT 
                    c.cod_destra, 
                    c.nom_eclihon AS nombre, 
                    c.obs_homnov AS observacion, 
                    c.fec_creaci AS fechaRegistro, 
                    c.cod_noveda AS cod_estado 
                FROM 
                    tab_despac_destin b 
                    INNER JOIN tab_despac_tracki c ON b.num_despac = c.num_despac 
                    INNER JOIN tab_despac_vehige d ON d.num_despac = b.num_despac 
                    AND (b.ped_remisi = '".$num_pedido."-".$num_linea."') 
                    AND c.nom_eclihon IS NOT NULL 
                    INNER JOIN tab_homolo_estnov e ON e.cod_noveda = c.cod_noveda 
                WHERE 
                    d.ind_activo = 'S' 
                GROUP BY 
                    fechaRegistro
            ) 
        ORDER BY 
            fechaRegistro ASC";

            $mSql = "(SELECT			  
                            c.cod_destra,
                            c.nom_eclhoe AS nombre, 
                            c.obs_hompro AS observacion,
                            c.fec_creaci AS fechaRegistro,
                            c.cod_noveda AS cod_estado 
                        FROM	tab_despac_destin b
                        INNER JOIN	tab_despac_tracki c
                            ON	b.num_despac = c.num_despac  
                        INNER JOIN    tab_despac_vehige d
                            ON  d.num_despac = b.num_despac  
                        AND (b.ped_remisi = '".$num_pedido."-".$num_linea."')
                        AND c.nom_eclhoe IS NOT NULL
                        WHERE    d.ind_activo = 'S'
                        GROUP BY 
                            fechaRegistro
                        )UNION(
                        SELECT		
                            c.cod_destra,
                            c.nom_eclihon AS nombre, 
                            c.obs_homnov AS observacion,
                            c.fec_creaci AS fechaRegistro,
                            c.cod_noveda AS cod_estado 
                        FROM	tab_despac_destin b
                        INNER JOIN	tab_despac_tracki c
                            ON	b.num_despac = c.num_despac  
                        INNER JOIN    tab_despac_vehige d
                            ON  d.num_despac = b.num_despac  
                        AND (b.ped_remisi = '".$num_pedido."-".$num_linea."')
                        AND c.nom_eclihon IS NOT NULL
                        WHERE    d.ind_activo = 'S'
                        GROUP BY 
                            fechaRegistro
                        )
                            ORDER BY fechaRegistro ASC
                        ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        $datos = $mConsult -> ret_matrix('a');
        
        $html='';
        foreach ($datos as $data){
            $fecha = date('Y-m-d', strtotime($data['fechaRegistro']));
            $dia = date('H:i:s', strtotime($data['fechaRegistro']));
            $estilos = self::darIconColorEstado($data['cod_estado']);
            $html.='<li>
                        <time class="cbp_tmtime style-span border rounded-left"><span>'.$fecha.'</span><span>'.$dia.'</span></time>
                        <div class="cbp_tmicon '.$estilos['color'].'"> <img src="../assets/img/icons/'.$estilos['icono'].'" width="20px"style="margin-top: 8px;"></img></div>
                            <div class="cbp_tmlabel">
                                <h2 class="title-estatus">'.$data['nombre'].'</h2>
                            <hr class="hr-space">
                            <p class="text-estatus">'.$data['observacion'].'
                            </p>
                        </div>
                    </li>
            ';
        }

        return utf8_decode($html);
    }

    
    protected function darIconColorEstado($cod_noveda){
        $color_bg = '';
        $icon = '';
        //Pre Cargue
        if($cod_noveda == 255 OR $cod_noveda == 9260){
            $color_bg = 'bg-green';
            $icon = 'delivery-package.png';
        }
        //En transito
        else if($cod_noveda == 213 OR $cod_noveda == 408 OR $cod_noveda == 9173){
            $color_bg = 'bg-info';
            $icon = 'delivery-truck.png';
        }
        //En planta
        else if($cod_noveda == 404 OR $cod_noveda == 9261){
            $color_bg = 'bg-blush';
            $icon = 'in-plant.png';
        }
        //En descargue
        else if($cod_noveda == 407){
            $color_bg = 'bg-orange';
            $icon = 'delivered-box-verification-symbol.png';
        }else{
            $color_bg = 'bg-info';
            $icon = 'commercial-delivery-symbol-of-a-list-on-clipboard-on-a-box-package.png';
        }

        $estilos = array(
            "color" => $color_bg,
            "icono" => $icon
        );

        return $estilos;   
    }

        /*! \fn: interfaz
         *  \brief: Genera la visual de la consulta del pedido
         *  \author: Ing. Luis Manrique
         *  \date: 04/09/2020
         *  \date modified: dd/mm/aaaa
         *  \return: HTML
         */
        protected function interfaz(){
            $num_pedido = $_SESSION['num_pedido'];
            $num_linea = $_SESSION['num_linea'];
            $info = self::darInformacionPedido($num_pedido,$num_linea);

            $rut_img = '../../imagenes/logo_'.NOM_URL_APLICA.'.jpg';

            //Se declara la varible de tiempos logisticos
            $tie_logist = null;
            //Encuesta Cerrada Temporal
            $habEncuesta = 'disabled';
            
            if($info['num_despac']!=0){
            $tie_logist = self::consultaTiemposLogisticos($info['num_despac']);

                if($tie_logist['fec_citdes']!=NULL AND $tie_logist['hor_citdes']!=NULL){
                    $fec_entrega = $tie_logist['fec_citdes']." - ".$tie_logist['hor_citdes'];  
                }

            $habEncu = self::validaHabilitiEncuesta($tie_logist);
            $habEncuesta = '';
            $ventEncuesta = '';
            if(!$habEncu){
                $habEncuesta = 'disabled';
                //$ventEncuesta = self::generateModalEncuestaSatisfaccion();
            }


            //Logica Boton tiempos logisticos
            $textTimes = 'Confirmar recibido del pedido';
            if($tie_logist['fec_cumdes']!='' OR $tie_logist['fec_cumdes']!=NULL){
                $estFecLlegada = true;
            }

            //Fecha de Entrada
            if($info['fec_ingdes']!='' OR $info['fec_ingdes']!=NULL){
                $estFecEntrada = true;
            }

            //Fecha de salida
            if($info['fec_saldes']!='' OR $info['fec_saldes']!=NULL){
                $estFecSalida = true;
            }

            if($estFecLlegada && $estFecEntrada && $estFecSalida){
                $textTimes = '<i class="zmdi zmdi-check-circle"></i> ¡Pedido Confirmado!';
            }


            }else{
                $fec_entrega = 'NO HA SIDO DESPACHADO';
                $textTimes = 'NO HA SIDO DESPACHADO';
            }

            $html = '<!doctype html>
            <head>
                <!-- Required meta tags -->
                <meta charset="utf8" />
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
            self:: style();

            $html.='<title>Consulta Pedido</title>
            </head>';

            $html.=' <body>
            <div class="container">
                <div class="row mt-2">
                    <div class="col-md-12 text-center">
                        <img src="'.$rut_img.'" height="100px;">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 text-center">
                        <h3 class="texth4">Pedido No. '.$num_pedido.'</h3>
                        <h3 class="texth4">linea No. '.$num_linea.'</h3>
                    </div>
                </div>';
            if(!isset($_SESSION['msj'])){
                $status = $_SESSION['sta'];
                $html.='<div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-'.$status.'" role="alert">
                                    '.$_SESSION['msj'].'
                                </div>
                            </div>
                        </div>
                ';
            }
            $html.='<div class="row mt-2">
                    <div class="col-md-6">
                        <div class="box box-solid box-primary">
                            <div class="box-header with-border text-center">
                                <h3 class="box-title">Remitente/Origen</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row mt-2">
                                    <div class="col-md-12"><h5 class="txtsmll">Ciudad de origen:</h5></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control form-control-sm" type="text" placeholder="Ciudad de origen" readonly value="'.utf8_encode($info['ciu_origen']).'">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12"><h5 class="txtsmll">Fecha de solicitud:</h5></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control form-control-sm" type="text" placeholder="Fecha de solicitud" readonly value="'.$info['fec_origen'].'">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12"><h5 class="txtsmll">Nombre de contacto:</h5></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control form-control-sm" type="text" placeholder="Nombre de contacto" readonly value="'.utf8_encode($info['nom_origen']).'">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12"><h5 class="txtsmll">Dirección:</h5></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control form-control-sm" type="text" placeholder="Direccion" readonly value="'.utf8_encode($info['dir_origen']).'">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6"><h5 class="txtsmll">Teléfono:</h5></div>
                                    <div class="col-md-6"><h5 class="txtsmll">Nit/CC:</h5></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input class="form-control form-control-sm" type="text" placeholder="Telefono" readonly value="Comuníquese con su asesor de confianza.">
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control form-control-sm" type="text" placeholder="Nit / CC" readonly value="'.$info['doc_origen'].'">
                                    </div>
                                </div>
                                
                            </div> 
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box box-solid box-primary">
                            <div class="box-header with-border text-center">
                                <h3 class="box-title">Destinatario/Destino</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </div>
                            </div>
                            <div class="box-body">
                            <div class="row mt-2">
                                    <div class="col-md-12"><h5 class="txtsmll">Ciudad de destino:</h5></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control form-control-sm" type="text" placeholder="Ciudad de destino" readonly value="'.utf8_encode($info['ciu_destin']).'">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12"><h5 class="txtsmll">Fecha estimada de entrega:</h5></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control form-control-sm" type="text" placeholder="Fecha de solicitud" readonly value="'.$fec_entrega.'">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12"><h5 class="txtsmll">Nombre de contacto:</h5></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control form-control-sm" type="text" placeholder="Nombre de contacto" readonly value="'.utf8_encode($info['nom_destin']).'">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12"><h5 class="txtsmll">Dirección:</h5></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input class="form-control form-control-sm" type="text" placeholder="Direccion" readonly value="'.utf8_encode($info['dir_destin']).'">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6"><h5 class="txtsmll">Teléfono:</h5></div>
                                    <div class="col-md-6"><h5 class="txtsmll">Nit/CC:</h5></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input class="form-control form-control-sm" type="text" placeholder="Telefono" readonly value="'.$info['num_destin'].'">
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control form-control-sm" type="text" placeholder="Nit / CC" readonly value="'.$info['doc_destin'].'">
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 text-center">
                        <button type="button" class="btn color-principal text-white" data-toggle="modal" data-target="#recibiProducForm">
                            '.$textTimes.'
                        </button>
                    </div>
                    <div class="col-md-6 text-center">
                        <button type="button" class="btn color-principal text-white" data-toggle="modal" data-target="#encuesSatisfaccionForm" '.$habEncuesta.'>
                            Encuesta de satisfacción
                        </button>
                    </div>
                </div>
                '.self::generateModalRecibidoPedido($tie_logist).'
            
                <input type="hidden" name="dat_gpsxxx" id="dat_gpsxxx" value="'.base64_encode(json_encode(self::validaDivMapas($info['num_despac'])['data'])).'" />

                <div class="row mt-3">
                    <div class="col-md-8">
                        <div class="box box-solid box-primary">
                            <div class="box-header with-border text-center">
                                <h3 class="box-title">Visualizar Recorrido de mi remisión</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </div>
                            </div>
                        <div class="box-body">
                            <div id="map" class="map">
                            </div>
                            <div id="popupContenedor" class="ol-popup">
                                <a href="#" id="popup-closer" class="ol-popup-closer"></a>
                                <div id="popupContent"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="box box-solid box-primary">
                        <div class="box-header with-border text-center">
                            <h3 class="box-title">Estado de mi remisión</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </div>
                        </div>
                    <div class="box-body overflow-auto" style="height:620px;">
                        <ul class="cbp_tmtimeline">
                            '.self::darNovedadRemisionTime($num_pedido,$num_linea).'
                        </ul>  
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid box-primary">
                        <div class="box-header with-border text-center">
                            <h3 class="box-title">Foto Cumplido</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                '.self::busquedaImgCumplido($info['num_despac']).'
                            </div>
                        </div><!--Cierra body-->
                    </div>                               
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <a href="../index.php" class="btn color-principal text-white">
                    <i class="zmdi zmdi-caret-left-circle"></i> Regresar
                </a>
            </div>
        </div>
    </div>';
    $_SESSION['msj']= "";
    $_SESSION['sta']= "";       
    self::script();                                    
            $html .=  '</body>
                  </html>';
            echo utf8_decode($html);
        }


        protected function consultaTiemposLogisticos($num_despac){
            $mSql = "SELECT 
                        a.fec_cumdes,
                        a.fec_ingdes,
                        a.fec_saldes,
                        a.fec_citdes,
                        a.hor_citdes
                    FROM ".BASE_DATOS.".tab_despac_destin a
                    WHERE a.num_despac = '".$num_despac."';";
            $mConsult = new Consulta($mSql, $this -> conexion);
            $mData = $mConsult->ret_arreglo();
            return $mData;
        }

        function busquedaImgCumplido($num_despac){
            $sql="SELECT a.url_imagex 
                FROM ".BASE_DATOS.".tab_cumpli_despac a 
                WHERE a.num_despac =  '$num_despac'";
            $queryPho = new Consulta($sql, $this -> conexion);
            $resulPho = $queryPho -> ret_matrix('a');
            $total = $queryPho -> ret_num_rows();
            $html='';
            if($total>0){
                $imageng = $this->base64_to_jpeg( $resulPho[0]['url_imagex'], "tmp.jpg" );
                $html.='<div class="col-md-8">
                            <div class="easyzoom easyzoom--overlay easyzoom--with-thumbnails" style="max-width:800px;overflow:hidden;">
                                <a href="'.$imageng.'">
                                <img src="'.$imageng.'" alt="" style="width:100%;height:auto"/>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                        ';

                $conteo_img=1;

                foreach($resulPho as $img){
                    $imagen = self::base64_to_jpeg( $img['url_imagex']);
                    if($conteo_img==1){
                        $html.='
                        <td>
                                <ul class="thumbnails">';
                    }
                    $html.= '<li class="mt-3">
                                <a href="'.$imagen.'" data-standard="'.$imagen.'">
                                <img src="'.$imagen.'" alt="" width="140px" heigth="100px"/>
                                </a>
                            </li>';

                    if($conteo_img>=4){
                        $html.='</ul>
                                </td>';
                    };
                    if($conteo_img<4){
                        $conteo_img++;
                    }else{
                        $conteo_img=1;
                    }
                }
                $html.='</div>';
                return utf8_decode($html);
            }
        }

        function base64_to_jpeg($base64_image_string) {
            define('UPLOAD_DIR', '../../../'.BASE_DATOS.'/infPedidos/');
	        $img = str_replace('data:image/png;base64,', '', $base64_image_string);
	        $img = str_replace(' ', '+', $img);
	        $data = base64_decode($img);
	        $file = UPLOAD_DIR . uniqid() . '.png';
            return $data;
        }

        function separarFecha($fecha){
            if($fecha!='' OR $fecha!=NULL){
                $fecha = date('Y-m-d', strtotime($fecha));
            }
            return $fecha;
        }

        function separarHora($fecha){
            if($fecha!='' OR $fecha!=NULL){
            $hora= date('H:i:s', strtotime($fecha));
            return $hora;
            }
            return $fecha;
            
        }


        function generateModalRecibidoPedido($info){
            $html='';
            if($info!=null){
            $attrFecLlegada = '';
            $attrFecEntrada = '';
            $attrFecSalida = '';
            $estFecLlegada = false;
            $estFecEntrada = false;
            $estFecSalida = false;

            $btn = '';
            $msj = '';
            //Fecha de llegada
            if($info['fec_cumdes']!='' OR $info['fec_cumdes']!=NULL){
                $attrFecLlegada='readonly disabled';
                $estFecLlegada = true;
            }

            //Fecha de Entrada
            if($info['fec_ingdes']!='' OR $info['fec_ingdes']!=NULL){
                $attrFecEntrada='readonly disabled';
                $estFecEntrada = true;
            }

            //Fecha de salida
            if($info['fec_saldes']!='' OR $info['fec_saldes']!=NULL){
                $attrFecSalida='readonly disabled';
                $estFecSalida = true;
            }
                
            if(($estFecLlegada) AND ($estFecEntrada) AND ($estFecSalida)){
                $btn = 'disabled';
                $msj = '<div class="row mt-3">
                            <div class="col-md-12 center-text">
                                <p style="color:#008f39">¡Ya se han diligenciado los tiempos logisticos!</p>
                            </div>
                        </div>';
            }

        
            $html=' <div class="modal" tabindex="-1" role="dialog" id="recibiProducForm">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header color-principal">
                                    <h5 class="modal-title col-11 text-center" style="color:#fff">Tiempos logisticos de descargue</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="post" action="envioData.php" id="timeLogisticsForm">
                                <div class="modal-body">
                                    <div class="row">
                                        <p>
                                            Antes de diligenciar la encuesta debera de diligenciar las siguiente fecha y hora de los tiempos del vehiculo:
                                            </p>
                                    </div>
                                    <div class="row">
                                        <div class="offset-md-2 col-md-4">
                                            <label for="example-time-input" class="col-form-label">Fecha de Llegada</label>
                                            <input class="form-control form-control-sm" type="date" value="'.self::separarFecha($info['fec_cumdes']).'" id="fec_cumdes" name="fec_cumdes" '.$attrFecLlegada.' required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="example-time-input" class="col-form-label">Hora de Llegada</label>
                                            <input class="form-control form-control-sm" type="time" value="'.self::separarHora($info['fec_cumdes']).'" id="hor_cumdes" name="hor_cumdes" '.$attrFecLlegada.' required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="offset-md-2 col-md-4">
                                            <label for="example-time-input" class="col-form-label">Fecha de entrada</label>
                                            <input class="form-control form-control-sm" type="date" value="'.self::separarFecha($info['fec_ingdes']).'" id="fec_ingdes" name="fec_ingdes" '.$attrFecEntrada.' required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="example-time-input" class="col-form-label">Hora de entrada</label>
                                            <input class="form-control form-control-sm" type="time" value="'.self::separarHora($info['fec_ingdes']).'" id="hor_ingdes" name="hor_ingdes" '.$attrFecEntrada.' required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="offset-md-2 col-md-4">
                                            <label for="example-time-input" class="col-form-label">Fecha de salida</label>
                                            <input class="form-control form-control-sm" type="date" value="'.self::separarFecha($info['fec_saldes']).'" id="fec_saldes" name="fec_saldes" '.$attrFecSalida.' required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="example-time-input" class="col-form-label">Hora de salida</label>
                                            <input class="form-control form-control-sm" type="time" value="'.self::separarHora($info['fec_saldes']).'" id="hor_saldes" name="hor_saldes" '.$attrFecSalida.' required>
                                        </div>
                                    </div>
                                    '.$msj.'
                                    <input type="hidden" name="option" value="1">
                                    <input type="hidden" name="num_pedido" value="'.$_SESSION['num_pedido'].'">
                                    <input type="hidden" name="num_linea" value="'.$_SESSION['num_linea'].'">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" '.$btn.' id="buttonTimeLogistics" disabled>Confirmar</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
            ';
        }
            return $html;
        }

        function validaHabilitiEncuesta($info){
            $estFecLlegada = false;
            $estFecEntrada = false;
            $estFecSalida = false;

            $resp=false;
            if($info['fec_cumdes']!='' OR $info['fec_cumdes']!=NULL){
                $estFecLlegada = true;
            }

            //Fecha de Entrada
            if($info['fec_ingdes']!='' OR $info['fec_ingdes']!=NULL){
                $estFecEntrada = true;
            }

            //Fecha de salida
            if($info['fec_saldes']!='' OR $info['fec_saldes']!=NULL){
                $estFecSalida = true;
            }

            if($estFecEntrada AND $estFecLlegada AND $estFecSalida){
                $resp=true;
            }

            return $resp;

        }

        function validaDiligeEncuesta($num_despac,$num_remisi,$num_pedido){
          $mSql = "SELECT COUNT(*) FROM tab_respue_encues WHERE num_despac = '".$num_despac."' AND num_docume='".$num_remisi."' AND ped_remisi='".$num_pedido."';";
          $mConsult = new Consulta($mSql, $this -> conexion);
          $mData = $mConsult->ret_arreglo()[0];
          if($mData>0){
              return true;
          }
          return false;
        }

        function dataDiligeEncuesta($num_despac,$num_remisi,$num_pedido){
            $mSql = "SELECT res_pregun1,res_pregun2,res_pregun3 FROM tab_respue_encues WHERE num_despac = '".$num_despac."' AND num_docume='".$num_remisi."' AND ped_remisi='".$num_pedido."';";
            $mConsult = new Consulta($mSql, $this -> conexion);
            $mData = $mConsult->ret_arreglo();
            return $mData;
        }


        function generateModalEncuestaSatisfaccion(){
            $attrbtn='';
            $msj='';
            $infoData='';

            //Respuestas
            $preg1Si='';
            $preg1No='';
            $preg2Si='';
            $preg2No='';

            $select1='';
            $select2='';
            $select3='';
            $select4='';

            $dili = '';
            if(self::validaDiligeEncuesta($_SESSION['num_despac'],$_SESSION['num_remisi'],$_SESSION['num_pedido'])){
                $attrbtn = 'disabled';
                $msj = '<div class="row mt-3">
                            <div class="col-md-12 center-text">
                                <p style="color:#008f39">¡Ya se han diligenciado la encuesta!</p>
                            </div>
                        </div>';
                $infoData = self::dataDiligeEncuesta($_SESSION['num_despac'],$_SESSION['num_remisi'],$_SESSION['num_pedido']);

                if($infoData['res_pregun1']==1){
                    $preg1Si = 'checked';
                }else{
                    $preg1No = 'checked';
                }

                if($infoData['res_pregun2']==1){
                    $preg2Si = 'checked';
                }else{
                    $preg2No = 'checked';
                }

                if($infoData['res_pregun3']==3){
                    $select1 = 'selected';
                }else if($infoData['res_pregun3']==2){
                    $select2 = 'selected';
                }else if($infoData['res_pregun3']==1){
                    $select3 = 'selected';
                }else{
                    $select4 = 'selected';
                }

                $dili = 'disabled';

            }
            $html=' <div class="modal" tabindex="-1" role="dialog" id="encuesSatisfaccionForm">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header color-principal">
                                    <h5 class="modal-title col-11 text-center" style="color:#fff">Encuesta de satisfacción</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="post" action="envioData.php">
                                <div class="modal-body">
                                    
                                    <div class="row">
                                        <div class="col-md-12 center-text">
                                            <p class="">¿Se cumplieron los tiempos de entrega del producto?</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 center-text">
                                            <label class="radio-inline mr-3">
                                                <input type="radio" name="preg1" value="1" '.$preg1Si.' '.$dili.'> Si
                                            </label>
                                            <label class="radio-inline mr-3">
                                                <input type="radio" name="preg1" value="0" '.$preg1No.' '.$dili.'> No
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 center-text">
                                            <p class="">¿El producto se entregó en condiciones adecuadas?</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 center-text">
                                            <label class="radio-inline mr-3">
                                                <input type="radio" name="preg2" value="1" '.$preg2Si.' '.$dili.'> Si
                                            </label>
                                            <label class="radio-inline mr-3">
                                                <input type="radio" name="preg2" value="0" '.$preg2No.' '.$dili.'> No
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 center-text">
                                            <p class="">¿Como fue la amabilidad del conductor durante la entrega?</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="offset-md-3 col-md-6 center-text">
                                            <select class="form-control" id="preg3" name="preg3" '.$dili.'>
                                                <option value="3" '.$select1.'>Excelente</option>
                                                <option value="2" '.$select2.'>Buena</option>
                                                <option value="1" '.$select3.'>Por mejorar</option>
                                                <option value="0" '.$select4.'>Mala</option>
                                            </select>
                                        </div>
                                    </div>
                                    '.$msj.'
                                    <input type="hidden" name="option" value="2">
                                    <input type="hidden" name="busqueda" value="'.$_SESSION['busqueda'].'">
                                    <input type="hidden" name="num_pedido" value="'.$_SESSION['num_pedido'].'">
                                    <input type="hidden" name="num_remisi" value="'.$_SESSION['num_remisi'].'">
                                    <input type="hidden" name="num_despac" value="'.$_SESSION['num_despac'].'">
                                    <input type="hidden" name="cod_remdes" value="'.$_SESSION['cod_remdes'].'">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" '.$attrbtn.'>Enviar</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
            ';
            return $html;
        }

    }  

    $ConsultarPedido = new Dashboard();

?>