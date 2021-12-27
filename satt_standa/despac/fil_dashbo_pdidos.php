<?php
    /*! \fn: FilterData
    *  \brief: Funcion que ejecuta los filtros y envia los datos para la generaci?n de los graficos
    *  \author: Luis Carlos Manrique Boada
    *    \date: 2019-08-02
    *  \param:  
    */

    class FilterData
    {

        //Create necessary variables
        static private $conexion = null;
        static private $cod_aplica = null;
        static private $usuario = null;
        static private $dates = array();
        

        /*! \fn: FilterData
        *  \brief: Funcion que controla los procesos de envio de evento hasta el cliente
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-02
        *  \param:  
        */
        function __construct($co = null, $us = null, $ca = null)
        {

            //Include Connection class
            @include_once( '../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc' );
            @include( "../lib/ajax.inc" );
            //Config display errors true
            ini_set('display_errors', true);
            error_reporting(E_ALL & ~E_NOTICE);

            //Assign values
            self::$conexion = $AjaxConnection;
            self::$usuario = $us;
            self::$cod_aplica = $ca;

            //Switch request options
            switch($_REQUEST[opcion])
            {
                case "1":
                    self::rem30DaysLastGraphic();

                    break;

                case "2":
                    self::topTenOrgFrecRec();

                    break;

                case "3":
                    self::topTenDesRemCum();

                    break;

                case "4":
                    self::topTenDesFreRecCiu();

                    break;

                case "5":
                    self::estRemGraphic();

                    break;

                case "6":
                    self::topTenDesFreRecEst();

                    break;

                case "7":
                    self::selClientes();

                    break;
                    
                case "8":
                    self::selNeg();

                    break;

                case "9":
                    self::selCan();

                    break;
                
                case "10":
                    self::selTipDes();

                    break;
                case "11":
                    self::selOrgDest();

                    break;
                
                case "12":
                    self::popUpRemPed();

                    break;

                case "13":
                    self::send();

                    break;

                case "14":
                    self::topConfiRec();
    
                     break;

                default:

                break;
            }
        }

        /*! \fn: filtersFields
        *  \brief: Funcion que controla los envios POST para la validaci?n de condici?n en las consultas
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-08
        *  \param:  
        */
        function filtersFields(){
            //Validate the fields sent by post

            if(!empty($_REQUEST['cliente'])){
                $query .= "
                AND e.cod_remdes IN (".$_REQUEST['cliente'].")";
            }

            if(!empty($_REQUEST['negocios'])){
                $query .= "
                AND j.cod_marcax IN (".$_REQUEST['negocios'].")";
            }

            if(!empty($_REQUEST['canal'])){
                $query .= "
                AND d.cod_canalx IN (".$_REQUEST['canal'].")";
            }

            if(!empty($_REQUEST['tipoOperacion'])){
                $query .= " 
                AND a.cod_tipdes = ".$_REQUEST['tipoOperacion'];
            }

            if(!empty($_REQUEST['origen'])){
                $query .= "
                AND a.cod_ciuori IN (".$_REQUEST['origen'].")";
            }

            if(!empty($_REQUEST['destino'])){
                $query .= "
                AND a.cod_ciudes IN (".$_REQUEST['destino'].")";
            }

            return $query;
        }

        /*! \fn: rem30DaysLastGraphic
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaci?n 
                   por Json para Graficar "Despachos realizados por tipo de Operaci?n"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-06
        *  \param:  
        */

        function rem30DaysLastGraphic(){
            //Mira el filtro si el perfil tiene asociada una transportadora
            $filtro = new Aplica_Filtro_Usuari( COD_APLICACION, COD_FILTRO_REMDES, $_SESSION['datos_usuario']['cod_usuari'] );
            $add_query="";
            if($filtro -> listar(self::$conexion)>0){
                $datos_filtro = $filtro -> dar_filtro_multiple(self::$conexion);
                $ultima = count($datos_filtro)-1;
                $add_query .= " AND a.cod_client IN (";
                foreach($datos_filtro as $key => $value){
                $add_query .= " ".$value['clv_filtro'];
                  if($key!=$ultima){
                    $add_query .= ", ";  
                  }
                }
                $add_query .= ") ";
              }

            //Create query "Despachos realizados por tipo de Operaci?n"
            $query = "
              SELECT    -- 'Remisiones' AS `nombre`,
                        c.nom_tipdes AS `nombre`,
                        DATE(a.fec_despac) AS `fecha`,
                        count(a.num_despac) AS `cantidad`
                FROM    ".BASE_DATOS.".tab_despac_despac a
          INNER JOIN    ".BASE_DATOS.".tab_despac_vehige b 
                  ON    a.num_despac = b.num_despac 
          INNER JOIN    ".BASE_DATOS.".tab_vehicu_vehicu j 
                  ON    b.num_placax = j.num_placax 
          INNER JOIN    ".BASE_DATOS.".tab_despac_destin d 
                  ON    a.num_despac = d.num_despac
          INNER JOIN    ".BASE_DATOS.".tab_genera_remdes e 
                  ON    d.cod_remdes = e.cod_remdes
          INNER JOIN    ".BASE_DATOS.".tab_genera_tipdes c 
                  ON    a.cod_tipdes = c.cod_tipdes
               WHERE    DATE(a.fec_despac) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE()
               $add_query
            ";

            $query .= self::filtersFields()."
            GROUP BY    DATE(a.fec_despac), c.cod_tipdes
            ORDER BY    2 ASC";

            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');
            $data = array();
            $json = array();

            //Create data structure
            foreach ($despachos as $key => $value)
            {
                //Validate existence
                if(!isset($data[$value["nombre"]]))
                {
                    $data[$value["nombre"]] = ["name" => $value["nombre"], "data" => self::$dates];
                }

                //Fill data
                $data[$value["nombre"]]["data"][$value["fecha"]] = [$value["fecha"], $value["cantidad"]];
            }

            //Clean data structure
            foreach ($data as $key => $value)
            {
                $tempData = array();
                $tempData["data"] = array();
                $tempData["name"] = $value["name"];

                foreach ($value["data"] as $key1 => $value1)
                {
                    array_push($tempData["data"], $value1);
                }

                array_push($json, $tempData);
            }

            echo json_encode(self::cleanArray($json));
        }

        /*! \fn: topTenOrgFrecRec
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaci?n 
                   por Json para Graficar "TOP 10 Origenes m?s frecuentes y recientes"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-06
        *  \param:  
        */

        function topTenOrgFrecRec(){
            
            //Condici?n sub consulta
            $condition = !empty($_REQUEST['cliente']) ? "AND e.cod_remdes IN (".$_REQUEST['cliente'].")" : "";
            
            //Select "TOP 10 Origenes m?s frecuentes y recientes"
            $query = "
              SELECT    'TOP 10 Origenes m?s frecuentes y recientes' AS `nombre`,
                        (f.nom_ciudad) AS `ciudad`,
                        count(f.nom_ciudad) AS `cantidad`
                FROM    ".BASE_DATOS.".tab_despac_despac a
          INNER JOIN    
                        (
                          SELECT    a.num_despac, 
                                    d.cod_remdes
                            FROM    ".BASE_DATOS.".tab_despac_despac a
                      INNER JOIN    ".BASE_DATOS.".tab_despac_destin d 
                              ON    a.num_despac = d.num_despac 
                                    AND DATE(a.fec_despac) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE()
                                    AND a.ind_anulad = 'R' 
                                    AND a.ind_planru = 'S'
                      INNER JOIN    ".BASE_DATOS.".tab_genera_remdes e 
                              ON    d.cod_remdes = e.cod_remdes ".$condition."
                        GROUP BY    a.num_despac
                        ) d
                  ON    a.num_despac = d.num_despac
          INNER JOIN    ".BASE_DATOS.".tab_genera_remdes e 
                  ON    d.cod_remdes = e.cod_remdes
          INNER JOIN    ".BASE_DATOS.".tab_genera_ciudad f 
                  ON    a.cod_ciuori = f.cod_ciudad 
          INNER JOIN    ".BASE_DATOS.".tab_despac_vehige b 
                  ON    a.num_despac = b.num_despac 
          INNER JOIN    ".BASE_DATOS.".tab_vehicu_vehicu j 
                  ON    b.num_placax = j.num_placax 
               WHERE    DATE(a.fec_despac) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE()
                        AND a.ind_anulad = 'R' AND a.ind_planru = 'S'
            ";

            $query .= self::filtersFields()."
            GROUP BY    f.nom_ciudad
            ORDER BY	3 DESC, 2
               LIMIT    10";
            
            //Execute query 
            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');
                           
            $data = array();
            $json = array();

            //Create data structure
            foreach ($despachos as $key => $value)
            {
                //Validate existence
                if(!isset($data[$value["nombre"]]))
                {
                    $data[$value["nombre"]] = ["name" => $value["nombre"], "data" => self::$dates];
                }

                //Fill data
                $data[$value["nombre"]]["data"][$value["ciudad"]] = [$value["cantidad"], $value["ciudad"]];
            }

            //Clean data structure
            foreach ($data as $key => $value)
            {
                $tempData = array();
                $tempData["data"] = array();

                foreach ($value["data"] as $key1 => $value1)
                {
                    array_push($tempData["data"], $value1);
                }

                array_push($json, $tempData);
            }
            echo json_encode(self::cleanArray($json));
        }

        /*! \fn: topTenDesRemCum
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaci?n 
                   por Json para Graficar "TOP 10 Destinos de Remisiones por Cumplir"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-06
        *  \param:  
        */

        function topTenDesRemCum(){

            $filtro = new Aplica_Filtro_Usuari( COD_APLICACION, COD_FILTRO_REMDES, $_SESSION['datos_usuario']['cod_usuari'] );
            $add_query="";
            if($filtro -> listar(self::$conexion)>0){
                $datos_filtro = $filtro -> dar_filtro_multiple(self::$conexion);
                $ultima = count($datos_filtro)-1;
                $add_query .= " AND a.cod_client IN (";
                foreach($datos_filtro as $key => $value){
                $add_query .= " ".$value['clv_filtro'];
                  if($key!=$ultima){
                    $add_query .= ", ";  
                  }
                }
                $add_query .= ") ";
              }

            //Select "TOP 10 Destinos de Remisiones por Cumplir"
            $query = "
              SELECT    'TOP 10 Destinos de Remisiones por Cumplir' AS `nombre`,
                        (c.nom_ciudad) AS `ciudad`,
                        count(a.num_despac) AS `cantidad`
                FROM    ".BASE_DATOS.".tab_despac_despac a
          INNER JOIN    ".BASE_DATOS.".tab_despac_destin d 
           		  ON	a.num_despac = d.num_despac AND a.cod_ciudes = d.cod_ciudad AND d.ind_cumdes IS NULL
          INNER JOIN    ".BASE_DATOS.".tab_genera_ciudad c 
                  ON	d.cod_ciudad = c.cod_ciudad
          INNER JOIN    ".BASE_DATOS.".tab_genera_remdes e 
                  ON    d.cod_remdes = e.cod_remdes   
          INNER JOIN    ".BASE_DATOS.".tab_despac_vehige b 
                  ON    a.num_despac = b.num_despac 
          INNER JOIN    ".BASE_DATOS.".tab_vehicu_vehicu j 
                  ON    b.num_placax = j.num_placax 
               WHERE    DATE(a.fec_despac) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE()
                        AND a.ind_anulad = 'R' AND a.ind_planru = 'S'
                        $add_query
            ";

            $query .= self::filtersFields()."
            GROUP BY    c.nom_ciudad 
            ORDER BY	3 DESC, 2
               LIMIT    10";
            //Execute query 
            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');

            $data = array();
            $json = array();

            //Create data structure
            foreach ($despachos as $key => $value)
            {
                //Validate existence
                if(!isset($data[$value["nombre"]]))
                {
                    $data[$value["nombre"]] = ["name" => $value["nombre"], "data" => self::$dates];
                }

                //Fill data
                $data[$value["nombre"]]["data"][$value["ciudad"]] = [$value["cantidad"], $value["ciudad"]];
            }

            //Clean data structure
            foreach ($data as $key => $value)
            {
                $tempData = array();
                $tempData["data"] = array();

                foreach ($value["data"] as $key1 => $value1)
                {
                    array_push($tempData["data"], $value1);
                }

                array_push($json, $tempData);
            }
            
            echo json_encode(self::cleanArray($json));
        }

        /*! \fn: topTenDesFreRecCiu
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaciï¿½n 
                   por Json para Graficar "TOP 10 Destinos mï¿½s frecuentes y recientes (Ciudad)"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-06
        *  \param:  
        */

        function topTenDesFreRecCiu(){

            $filtro = new Aplica_Filtro_Usuari( COD_APLICACION, COD_FILTRO_REMDES, $_SESSION['datos_usuario']['cod_usuari'] );
            $add_query="";
            if($filtro -> listar(self::$conexion)>0){
                $datos_filtro = $filtro -> dar_filtro_multiple(self::$conexion);
                $ultima = count($datos_filtro)-1;
                $add_query .= " AND a.cod_client IN (";
                foreach($datos_filtro as $key => $value){
                $add_query .= " ".$value['clv_filtro'];
                  if($key!=$ultima){
                    $add_query .= ", ";  
                  }
                }
                $add_query .= ") ";
              }

            //Select "TOP 10 Destinos mï¿½s frecuentes y recientes (Ciudad)"
            $query = "
              SELECT    'TOP 10 Destinos mï¿½s frecuentes y recientes (Ciudad)' AS `nombre`,
                        (f.nom_ciudad) AS `ciudad`,
                        count(a.num_despac) AS `cantidad`
                FROM    ".BASE_DATOS.".tab_despac_despac a
          INNER JOIN    ".BASE_DATOS.".tab_despac_destin d 
           		  ON	a.num_despac = d.num_despac AND a.cod_ciudes = d.cod_ciudad
          INNER JOIN    ".BASE_DATOS.".tab_genera_ciudad f 
                  ON    d.cod_ciudad = f.cod_ciudad
          INNER JOIN    ".BASE_DATOS.".tab_genera_remdes e 
                  ON    d.cod_remdes = e.cod_remdes        
          INNER JOIN    ".BASE_DATOS.".tab_despac_vehige b 
                  ON    a.num_despac = b.num_despac 
          INNER JOIN    ".BASE_DATOS.".tab_vehicu_vehicu j 
                  ON    b.num_placax = j.num_placax 
               WHERE    DATE(a.fec_despac) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE()
                        AND a.ind_anulad = 'R' AND a.ind_planru = 'S'
                        $add_query
            ";

            $query .= self::filtersFields()."
            GROUP BY    f.nom_ciudad 
            ORDER BY	3 DESC, 2
               LIMIT    10";
            //Execute query 
            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');
            
            $data = array();
            $json = array();

            //Create data structure
            foreach ($despachos as $key => $value)
            {
                //Validate existence
                if(!isset($data[$value["nombre"]]))
                {
                    $data[$value["nombre"]] = ["name" => $value["nombre"], "data" => self::$dates];
                }

                //Fill data
                $data[$value["nombre"]]["data"][$value["ciudad"]] = [$value["cantidad"], $value["ciudad"]];
            }

            //Clean data structure
            foreach ($data as $key => $value)
            {
                $tempData = array();
                $tempData["data"] = array();

                foreach ($value["data"] as $key1 => $value1)
                {
                    array_push($tempData["data"], $value1);
                }

                array_push($json, $tempData);
            }
            
            echo json_encode(self::cleanArray($json));
        }

        /*! \fn: estRemGraphic
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaci?n 
                   por Json para Graficar "Estado de mis remisiones"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-06
        *  \param:  
        */

        function estRemGraphic(){

            $filtro = new Aplica_Filtro_Usuari( COD_APLICACION, COD_FILTRO_REMDES, $_SESSION['datos_usuario']['cod_usuari'] );
            $add_query="";
            if($filtro -> listar(self::$conexion)>0){
                $datos_filtro = $filtro -> dar_filtro_multiple(self::$conexion);
                $ultima = count($datos_filtro)-1;
                $add_query .= " AND a.cod_client IN (";
                foreach($datos_filtro as $key => $value){
                $add_query .= " ".$value['clv_filtro'];
                  if($key!=$ultima){
                    $add_query .= ", ";  
                  }
                }
                $add_query .= ") ";
              }

            //Condiciï¿½n sub consulta
            $condition = !empty($_REQUEST['cliente']) ? "AND e.cod_remdes IN (".$_REQUEST['cliente'].")" : "";

            //Select "Estado de mis remisiones"
            $query = "
              SELECT    'Estado Remisiones' AS `nombre`,
                        IF( h.nom_etapax IS NULL , 'EN TRANSITO' , h.nom_etapax ) AS `estado`,
                        count(a.num_despac) AS `cantidad`
                FROM    ".BASE_DATOS.".tab_despac_despac a
          INNER JOIN    (
                          SELECT    a.num_despac, d.cod_remdes
                            FROM    ".BASE_DATOS.".tab_despac_despac a
                      INNER JOIN    ".BASE_DATOS.".tab_despac_destin d 
                              ON    a.num_despac = d.num_despac 
                                    AND a.ind_anulad = 'R' 
                                    AND a.ind_planru = 'S'
                      INNER JOIN    ".BASE_DATOS.".tab_genera_remdes e 
                              ON    d.cod_remdes = e.cod_remdes  ".$condition."
                      WHERE a.fec_llegad IS NULL
                        GROUP BY    a.num_despac
                        ) d
                  ON    a.num_despac = d.num_despac
          INNER JOIN    ".BASE_DATOS.".tab_despac_vehige b 
                  ON    a.num_despac = b.num_despac 
          INNER JOIN    ".BASE_DATOS.".tab_vehicu_vehicu j 
                  ON    b.num_placax = j.num_placax 
          INNER JOIN    ".BASE_DATOS.".tab_genera_remdes e 
                  ON    d.cod_remdes = e.cod_remdes   
           LEFT JOIN    ".BASE_DATOS.".tab_genera_noveda g
                  ON    a.cod_ultnov = g.cod_noveda AND a.ind_anulad = 'R' AND a.ind_planru = 'S'
           LEFT JOIN    ".BASE_DATOS.".tab_genera_etapax h
                  ON    g.cod_etapax = h.cod_etapax
               WHERE    a.ind_anulad = 'R' AND a.ind_planru = 'S'
                        AND a.fec_llegad IS NULL
                        $add_query
            ";

            $query .= self::filtersFields()."
            GROUP BY    h.nom_etapax
            ORDER BY    h.cod_etapax";
            //Execute query 
            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');

            $data = array();
            $json = array();

            //Create data structure
            foreach ($despachos as $key => $value)
            {
                //Validate existence
                if(!isset($data[$value["nombre"]]))
                {
                    $data[$value["nombre"]] = ["name" => $value["nombre"], "data" => self::$dates];
                }

                //Fill data
                $data[$value["nombre"]]["data"][$value["estado"]] = [$value["cantidad"], $value["estado"]];
            }

            //Clean data structure
            foreach ($data as $key => $value)
            {
                $tempData = array();
                $tempData["data"] = array();

                foreach ($value["data"] as $key1 => $value1)
                {
                    array_push($tempData["data"], $value1);
                }

                array_push($json, $tempData);
            }
            
            echo json_encode(self::cleanArray($json));
        }

        /*! \fn: topTenDesFreRecEst
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaci?n 
                   por Json para Graficar "TOP 10 Destinos por tipo de operaci?n"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-06
        *  \param:  
        */

        function topTenDesFreRecEst(){

            $filtro = new Aplica_Filtro_Usuari( COD_APLICACION, COD_FILTRO_REMDES, $_SESSION['datos_usuario']['cod_usuari'] );
            $add_query="";
            if($filtro -> listar(self::$conexion)>0){
                $datos_filtro = $filtro -> dar_filtro_multiple(self::$conexion);
                $ultima = count($datos_filtro)-1;
                $add_query .= " AND a.cod_client IN (";
                foreach($datos_filtro as $key => $value){
                $add_query .= " ".$value['clv_filtro'];
                  if($key!=$ultima){
                    $add_query .= ", ";  
                  }
                }
                $add_query .= ") ";
              }

           //Select "TOP 10 Destinos por tipo de operaci?n"
            $query = "
              SELECT    'TOP 10 Destinos por tipo de operaci?n' AS `nombre`,
                        (c.nom_tipdes) AS `estado`,
                        count(a.num_despac) AS `cantidad`
                FROM    ".BASE_DATOS.".tab_despac_despac a
          INNER JOIN    ".BASE_DATOS.".tab_despac_vehige b 
                  ON    a.num_despac = b.num_despac 
          INNER JOIN    ".BASE_DATOS.".tab_vehicu_vehicu j 
                  ON    b.num_placax = j.num_placax 
          INNER JOIN    ".BASE_DATOS.".tab_despac_destin d 
           		  ON	a.num_despac = d.num_despac
          INNER JOIN    ".BASE_DATOS.".tab_genera_tipdes c 
                  ON    a.cod_tipdes = c.cod_tipdes
          INNER JOIN    ".BASE_DATOS.".tab_genera_remdes e 
                  ON    d.cod_remdes = e.cod_remdes      
               WHERE    DATE(a.fec_despac) BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE()
                        AND a.ind_anulad = 'R' AND a.ind_planru = 'S'
                        $add_query
            ";

            $query .= self::filtersFields()."
            GROUP BY    c.nom_tipdes 
            ORDER BY	3 DESC, 2
               LIMIT    10";
            //Execute query 
            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');

            $data = array();
            $json = array();

            //Create data structure
            foreach ($despachos as $key => $value)
            {
                //Validate existence
                if(!isset($data[$value["nombre"]]))
                {
                    $data[$value["nombre"]] = ["name" => $value["nombre"], "data" => self::$dates];
                }

                //Fill data
                $data[$value["nombre"]]["data"][$value["estado"]] = [$value["cantidad"], $value["estado"]];
            }

            //Clean data structure
            foreach ($data as $key => $value)
            {
                $tempData = array();
                $tempData["data"] = array();

                foreach ($value["data"] as $key1 => $value1)
                {
                    array_push($tempData["data"], $value1);
                }

                array_push($json, $tempData);
            }
            
            echo json_encode(self::cleanArray($json));
        }

        /*! \fn: selClientes
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaci?n 
                   por Json para crea option del "Select Clientes"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-04
        *  \param:  
        */

        function selClientes(){
            $filtro = new Aplica_Filtro_Usuari( COD_APLICACION, COD_FILTRO_REMDES, $_SESSION['datos_usuario']['cod_usuari'] );
            
            $query="";
            if($filtro -> listar(self::$conexion)>0){
                $datos_filtro = $filtro -> dar_filtro_multiple(self::$conexion);
                $ultima = count($datos_filtro)-1;
                $query .= " AND a.num_remdes IN (";
                foreach($datos_filtro as $key => $value){
                $query .= " ".$value['clv_filtro'];
                  if($key!=$ultima){
                $query .= ", ";  
                  }
                }
                $query .= ") ";
              }
        

            try{
            $sql = "
              SELECT    a.cod_remdes, 
                        CONCAT(a.nom_remdes,' ',b.nom_ciudad,' ', a.dir_remdes) as nom_remdes
                FROM    ".BASE_DATOS.".tab_genera_remdes a 
           INNER JOIN   ".BASE_DATOS.".tab_genera_ciudad b
                  ON    a.cod_ciudad = b.cod_ciudad
               WHERE    a.ind_remdes = 2
               $query
            ORDER BY    nom_remdes ASC
            ";

            $query = new Consulta($sql, self::$conexion);
            $selClientes = $query -> ret_matrix('i');
            echo json_encode(self::cleanArray($selClientes));

            }catch(Exception $e){
                echo json_encode("Error en metodo getVehiclesForAuthorization: ".$e->getMessage());
            }
        }

        /*! \fn: selNeg
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaci?n 
                   por Json para crea option del "Select Negocios"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-04
        *  \param:  
        */

        function selNeg(){
            //Create query "Select Negocios"
            $sql = "
              SELECT    a.cod_produc, 
                        a.nom_produc 
                FROM    ".BASE_DATOS.".tab_genera_produc a 
               WHERE    a.ind_estado = 1
            ORDER BY    nom_produc ASC
            ";
            
            $query = new Consulta($sql, self::$conexion);
            $selNeg = $query -> ret_matrix('i');

            echo json_encode(self::cleanArray($selNeg));
        }


        /*! \fn: selCan
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaci?n 
                   por Json para crea option del "Select Canal"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-04
        *  \param:  
        */

        function selCan(){
            //Create query "Select Canal"
            $sql = "
              SELECT    a.con_consec, 
                        a.nom_canalx 
                FROM    ".BASE_DATOS.".tab_genera_canalx a 
               WHERE    a.ind_estado = 1
            ORDER BY    a.nom_canalx ASC
            ";
            
            $query = new Consulta($sql, self::$conexion);
            $selCan = $query -> ret_matrix('i');

            echo json_encode(self::cleanArray($selCan));
            
        }

        /*! \fn: selTipDes
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaci?n 
                   por Json para crea option del "Select Canal"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-04
        *  \param:  
        */

        function selTipDes(){
            //Create query "Select Tipo Operaci?n"
            $sql = "
              SELECT    a.cod_tipdes, 
                        a.nom_tipdes 
                FROM    ".BASE_DATOS.".tab_genera_tipdes a 
            ORDER BY    a.nom_tipdes ASC
            ";
            
            $query = new Consulta($sql, self::$conexion);
            $selTipDes = $query -> ret_matrix('i');

            echo json_encode(self::cleanArray($selTipDes));
        }

        /*! \fn: selOrgDest
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaci?n 
                   por Json para crea option del "Select Canal"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-04
        *  \param:  
        */
        
        function selOrgDest(){
            //Create query "Select Origen y Destino"
            $sql = "
                  SELECT    a.cod_ciudad, 
                            UPPER(CONCAT(a.nom_ciudad,'(',b.nom_depart,')')) AS nom_ciudad
                    FROM    ".BASE_DATOS.".tab_genera_ciudad a 
              INNER JOIN    ".BASE_DATOS.".tab_genera_depart b
                      ON    a.cod_depart = b.cod_depart AND
                            b.cod_paisxx = 3
                   WHERE    a.ind_estado = 1
                ORDER BY    a.nom_ciudad ASC
            ";
            
            $query = new Consulta($sql, self::$conexion);
            $selOrgDest = $query -> ret_matrix('i');

            echo json_encode(self::cleanArray($selOrgDest));
        }

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
                    $arrayReturn[utf8_encode($key)] = self::cleanArray($value);
                }else{
                    //Clean value
                    $arrayReturn[utf8_encode($key)] = $convert($value);
                }
            }
            //Return array
            return $arrayReturn;
        }


        /*! \fn: selTipDes
        *  \brief: Funcion que crea la interfaz de las Remesas / Pedidos basandose en el pedido 
                   digitado por el cliente
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-07
        *  \param:  N/A
        */
        function popUpRemPed(){

            $sql = "
                  SELECT
                            a.num_despac,
                            f.nom_ciudad AS ciudadO,
                            g.nom_ciudad AS ciudadD,
                            a.fec_despac AS fechaSolicitud,
                            b.fec_llecli AS fechaRecibido,
                            d.nom_remdes AS nombreContactoO,
                            e.nom_remdes AS nombreContactoD,
                            d.dir_remdes AS direccionO,
                            e.dir_remdes AS direccionD,
                            d.num_remdes AS nitO,
                            e.num_remdes AS nitD,
                            i.num_telef1 AS telO,
                            b.num_destin AS telD,
                            a.fec_salida AS fechaEnvio,
                            (CONCAT(b.fec_citdes, ' ', b.hor_citdes)) AS fecEstEntre,
                            b.fec_cumdes AS fecFinEntre
                    FROM	".BASE_DATOS.".tab_despac_despac a
              INNER JOIN	".BASE_DATOS.".tab_despac_destin b
                      ON	a.num_despac = b.num_despac AND 
                            (b.num_docume = '".$_REQUEST['remisionPedido']."' OR b.ped_remisi = '".$_REQUEST['remisionPedido']."')
              INNER JOIN	".BASE_DATOS.".tab_genera_remdes d
                      ON	a.nom_sitcar = d.cod_remdes
              INNER JOIN	".BASE_DATOS.".tab_genera_remdes e
                      ON	b.cod_remdes = e.cod_remdes
              INNER JOIN	".BASE_DATOS.".tab_genera_ciudad f
                      ON	d.cod_ciudad = f.cod_ciudad
              INNER JOIN	".BASE_DATOS.".tab_genera_ciudad g
                      ON	a.cod_ciudes = g.cod_ciudad
              INNER JOIN	".BASE_DATOS.".tab_despac_vehige h
                      ON	a.num_despac = h.num_despac
              INNER JOIN	".BASE_DATOS.".tab_tercer_tercer i
                      ON	h.cod_transp = i.cod_tercer
                   WHERE    h.ind_activo = 'S'
            ";
            
            $query = new Consulta($sql, self::$conexion);
            $resultado = $query -> ret_matrix('a');

            $sqlHist = "
                  (SELECT
                            c.nom_eclhoe AS nombre, 
                            c.obs_hompro AS observacion,
                            c.fec_creaci AS fechaRegistro
                    FROM	".BASE_DATOS.".tab_despac_destin b
              INNER JOIN	".BASE_DATOS.".tab_despac_tracki c
                      ON	b.num_despac = c.num_despac  
              INNER JOIN    ".BASE_DATOS.".tab_despac_vehige d
                            ON  d.num_despac = b.num_despac  
                            AND (b.num_docume = '".$_REQUEST['remisionPedido']."' OR b.ped_remisi = '".$_REQUEST['remisionPedido']."')
                            AND c.nom_eclhoe IS NOT NULL
                   WHERE    d.ind_activo = 'S'
                  )UNION(
                  SELECT
                            c.nom_eclihon AS nombre, 
                            c.obs_homnov AS observacion,
                            c.fec_creaci AS fechaRegistro
                    FROM	".BASE_DATOS.".tab_despac_destin b
              INNER JOIN	".BASE_DATOS.".tab_despac_tracki c
                      ON	b.num_despac = c.num_despac  
              INNER JOIN    ".BASE_DATOS.".tab_despac_vehige d
                            ON  d.num_despac = b.num_despac  
                            AND (b.num_docume = '".$_REQUEST['remisionPedido']."' OR b.ped_remisi = '".$_REQUEST['remisionPedido']."')
                            AND c.nom_eclihon IS NOT NULL
                   WHERE    d.ind_activo = 'S'
                    )
                    ORDER BY fechaRegistro ASC
            ";
            
            $queryHist = new Consulta($sqlHist, self::$conexion);
            $resulHist = $queryHist -> ret_matrix('a');
            
            $ultimoEstado = end($resulHist);


            if(count($resultado[0]) > 0){

            
                $contenedor = '
                <div class="dashBoardDialog dialog" id="contDialog">
                    <div>
                        <div class="closeWindow">
                            <i class="fa fa-times-circle"></i>
                        </div>
                        <div class="panel panel-default" id="conten-print">
                            <table class="table">
                                <tr>
                                    <td colspan="100">
                                        <div class="input-group" title="Ciudad Origen">
                                            <div class="values">
                                                <spam>
                                                	<img id="pruebaIMG" src="../'.CENTRAL.'/imagenes/fortecem_logo.png" alt="Logo" width="200"><h1>Pedido No. '.$_REQUEST['remisionPedido'].'</h1>
                                                </spam>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="2">Remitente/Origen</th>
                                    <th colspan="2">Destinatarios/Destino</th>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="input-group" title="Ciudad Origen">
                                            <div class="input-group-addon">
                                                <i class="fa fa-map-marker" style="font-size: 15pt;"></i>
                                                Ciudad Origen
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["ciudadO"].'
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <div class="input-group" title="Ciudad Destino">
                                            <div class="input-group-addon">
                                                <i class="fa fa-map-marker" style="font-size: 15pt;"></i>
                                                Ciudad Destino
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["ciudadD"].'
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="input-group" title="Fecha de Solicitud">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                                Fecha de Solicitud
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["fechaSolicitud"].'
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <div class="input-group" title="Fecha de Recibido">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                                Fecha de Recibido
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["fechaRecibido"].'
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="input-group" title="Nombre Contacto">
                                            <div class="input-group-addon">
                                                <i class="fa fa-user"></i>
                                                Nombre Contacto
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["nombreContactoO"].'
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <div class="input-group" title="Nombre Contacto">
                                            <div class="input-group-addon">
                                                <i class="fa fa-user"></i>
                                                Nombre Contacto
                                            </div>
                                            <div class="form-control" id="nomDes">'.$resultado[0]["nombreContactoD"].'</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="input-group" title="Direcci?n Origen">
                                            <div class="input-group-addon">
                                                <i class="fa fa-thumb-tack"></i>
                                                Direcci?n
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["direccionO"].'
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <div class="input-group" title="Direcci?n Destino">
                                            <div class="input-group-addon">
                                                <i class="fa fa-thumb-tack"></i>
                                                Direcci?n
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["direccionD"].'
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-group" title="Telefono Origen">
                                            <div class="input-group-addon">
                                                <i class="fa fa-phone"></i>
                                                Telefono
                                            </div>
                                            <div class="form-control">
                                            018000180113
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group" title="NIT/C.C. Origen">
                                            <div class="input-group-addon">
                                                <i class="fa fa-vcard"></i>
                                                NIT/C.C.
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["nitO"].'
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group" title="Telefono Destino">
                                            <div class="input-group-addon">
                                                <i class="fa fa-phone"></i>
                                                Telefono
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["telD"].'
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group" title="NIT/CC Destino">
                                            <div class="input-group-addon">
                                                <i class="fa fa-vcard"></i>
                                                NIT/CC
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["nitD"].'
                                            </div>
                                        </div>
                                    </td>
                                <tr>
                                    <th colspan="100">Fecha de mi Remisión/Origen</th>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="input-group" title="Fecha Envio">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                                Fecha Envio
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["fechaEnvio"].'
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <div class="input-group" title="Fecha estimada Entrega">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                                Fecha estimada Entrega
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["fecEstEntre"].'
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="input-group" title="Fecha Final Entrega">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                                Fecha Final Entrega
                                            </div>
                                            <div class="form-control">
                                                '.$resultado[0]["fecFinEntre"].'
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <div class="input-group" title="Ultimo Status">
                                            <div class="input-group-addon">
                                                <i class="fa fa-clock-o"></i>
                                                Ultimo Status
                                            </div>
                                            <div class="form-control">
                                                '.$ultimoEstado["nombre"].'
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="100">Estado de la Remisi?n</th>
                                </tr>
                                <tr>
                                    <td colspan="100">
                                        <table id="estadoRemesas" class="table">
                                            <tr>
                                                <td>
                                                    <div>
                                                        <h5><b>Fila</b></h5>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h5><b>Estado/Observaci?n</b></h5>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h5><b>Fecha Registro</b></h5>
                                                    </div>
                                                </td>
                                            <tr>
                                            ';
                            $cont = 1;
                            foreach ($resulHist as $key => $value) {
                                    
                                
                            $contenedor .= '<tr>
                                                <td>
                                                    <span class="fa-stack identification">
                                                        <span class="fa fa-circle-thin fa-stack-2x" style="font-size: 21pt;"></span>
                                                        <strong class="fa-stack-1x">
                                                            '.$cont.'
                                                        </strong>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <b>'.$value['nombre'].':</b> '.$value['observacion'].'
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        '.$value['fechaRegistro'].'
                                                    </div>
                                                </td>
                                            <tr>
                                            ';
                                $cont++;    
                            }
                            $contenedor .= '
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        
                        '.$this->busquedaImgCumplido($resultado[0]["num_despac"]).'

                        <div class="panel panel-default">
                            <table id="sendPrinEmail" class="table">
                                <tr>
                                    <th colspan="100">Si desesa enviar la trazabilidad de tu remesa registra tu correo aqu?</th>
                                </tr>
                                <tr>
                                    <td colspan="100">
                                        <div class="input-group" title="Correo">
                                            <div class="input-group-addon">
                                                <i class="fa fa-at"></i>
                                                Correo
                                            </div>
                                            <input type="email" id="email" name="email" class="form-control" required/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <button class="btn btn-primary pull-right" type="button" id="sendPedRem">
                                            <span class="glyphicon glyphicon-send"></span>Enviar
                                        </button>
                                    </td>
                                    <td colspan="2">
                                        <button class="btn btn-primary pull-left" type="button" id="printPedRem">
                                            <span class="glyphicon glyphicon-print"></span>Imprimir
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>';
            }else{
                $contenedor = 0;
            }
            echo $contenedor;
        }

        function busquedaImgCumplido($num_despac){
            $sql="SELECT a.url_imagex 
                FROM ".BASE_DATOS.".tab_cumpli_despac a 
                WHERE a.num_despac =  $num_despac";
            $queryPho = new Consulta($sql, self::$conexion);
            $resulPho = self::cleanArray($queryPho -> ret_matrix('a'));
            $total = $queryPho -> ret_num_rows();
            $html='';
            if($total>0){
                $imageng = $this->base64_to_jpeg( $resulPho[0]['url_imagex'], "tmp.jpg" );
                $html.='<div class="panel panel-default">
                <table id="sendPrinEmail" class="table">
                    <tr>
                        <th colspan="100">Registro fotografico de cumplidos</th>
                    </tr>
                    <tr>
                        <td>
                            <div class="easyzoom easyzoom--overlay easyzoom--with-thumbnails" style=" width:400px;height:400px;overflow:hidden;">
                                <a href="'.$imageng.'">
                                <img src="'.$imageng.'" alt="" style="width:100%;height:auto"/>
                                </a>
                            </div>
                        </td>';

                $conteo_img=1;

                foreach($resulPho as $img){
                    $imagen = $this->base64_to_jpeg( $img['url_imagex']);
                    if($conteo_img==1){
                        $html.='<td>
                                <ul class="thumbnails">';
                    }
                    $html.= '<li>
                                <a href="'.$imagen.'" data-standard="'.$imagen.'">
                                <img src="'.$imagen.'" alt="" width="100px" heigth="70px"/>
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
                $html.='</tr>';

                if($this->getimgFirmaCalificado($num_despac)!=""){
                $html.='<center><hr></center>
                        <tr>
                            <td colspan="100">
                                <center>
                                <h4>Firma</h4>
                                <ul class="thumbnails">
                                <li>
                                    <a href="'.$this->getimgFirmaCalificado($num_despac).'" data-standard="'.$this->getimgFirmaCalificado($num_despac).'">
                                    <img src="'.$this->getimgFirmaCalificado($num_despac).'" alt="" width="px" heigth="130px"/>
                                    </a>
                                </li>
                                </ul>
                                <h5>'.$this->getCalificadoPor($num_despac).'</h5>
                                </center>
                            </td>
                        </tr>';
                }    
                $html.='</table>
                      </div>';
            }

            return $html;
        }

        function base64_to_jpeg($base64_image_string) {
            define('UPLOAD_DIR', '../../'.BASE_DATOS.'/infPedidos/');
	        $img = str_replace('data:image/png;base64,', '', $base64_image_string);
	        $img = str_replace(' ', '+', $img);
	        $data = base64_decode($img);
	        $file = UPLOAD_DIR . uniqid() . '.png';
            return $data;
        }
     
        /*! \fn: selTipDes
        *  \brief: Funcion que recibe los POST, crea un archivo pdf con ima imagen enviada en Base64 
                    y transforma la informaci?n y envia el correo el PDF adjunto junto a un cuerpo creado
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-12
        *  \param:  N/A
        */

        function send(){
            //Declarar Variables
            $remisionPedido = $_REQUEST['remisionPedido'];
            $nomClient = $_REQUEST['nomDest'];
            $http = $_SERVER['HTTPS'] = "ON" ? "https://" : "http://";
            $serverName = $_SERVER['SERVER_NAME'];
            $carpLogo = BASE_DATOS;

            require_once('../../' . DIR_APLICA_CENTRAL . '/lib/general/fpdf17/fpdf.php') ;
            $pdf = new FPDF();
            $pic = 'data://text/plain;base64,'. $_REQUEST['imgdata'];
            $pdf->AddPage();
            $pdf->Image($pic, 10,15,-160,0,'png');
            $filename = 'Remision-Pedido '.$_REQUEST['remisionPedido'].'.pdf';
            $path = 'your path goes here';
            $file = $path . "/" . $filename;
            
            $thefile = implode("", file('../../'.DIR_APLICA_CENTRAL.'/despac/tra_estNov_cliEma.html'));
            $thefile = addslashes($thefile);
            $thefile = "\$r_file=\"" . $thefile . "\";";

            $Base64Img = base64_decode($_REQUEST['imgdata']);
            file_put_contents('../../'.BASE_DATOS.'/infPedidos/'.$_REQUEST['remisionPedido'].'.png', $Base64Img);
            eval($thefile);
            $message = $r_file;

            $filePath = chunk_split(base64_encode($pdf->Output("", "S") ) );

            $subject = 'SEGUIMIENTO DE SU PEDIDO NO '.$_REQUEST['remisionPedido'];
            $title = 'SEGUIMIENTO DE SU PEDIDO NO '.$_REQUEST['remisionPedido'];
            $encabezamsj = 'Estimado cliente: '.$nomClient;
            $bodymsj = 'El estado de su pedido No. <strong>'.$num_pedido.'</strong>';
            $html.='';
            $correos = explode (',',$mailto);
            envioEmailsTemplate($mConexion,COR_PLANTI_ESTPED,$subject,$title,$correos,$encabezamsj,$bodymsj,$html,$filePath);
            
        }

        /*! \fn: getCalificadoPor
    *  \brief: trae el nombre de quien califico el despacho
    *  \author: Ing. Cristian Andr?s Torres
    *    \date: 21/07/2020 
    *  \param: despacho
    *  \return la respuesta de la pregunta
    */
    public function getCalificadoPor($despacho)
    {
     $mSelect = "SELECT a.nom_clical
                   FROM ".BASE_DATOS.".tab_despac_despac a
                  WHERE a.num_despac='".$despacho."' LIMIT 1;";

      $consulta = new Consulta( $mSelect, self::$conexion );
      $respuesta = self::cleanArray($consulta->ret_matriz());
      return $respuesta[0]['nom_clical'];
    }

     /*! \fn: getimgFirmaCalificado
    *  \brief: trae el nombre de quien califico el despacho
    *  \author: Ing. Cristian Andres Torres
    *    \date: 21/07/2020 
    *  \param: despacho
    *  \return la respuesta de la pregunta
    */
    public function getimgFirmaCalificado($despacho)
    {
     $mSelect = "SELECT a.fir_client
                   FROM ".BASE_DATOS.".tab_despac_despac a
                  WHERE a.num_despac='".$despacho."' LIMIT 1;";

      $consulta = new Consulta( $mSelect, self::$conexion );
      $respuesta = self::cleanArray($consulta->ret_matriz());
      return $respuesta[0]['fir_client'];
    }



     /*! \fn: topConfiRec
        *  \brief: Funcion que recibe los POST, realiza la consulta  y transforma la informaciï¿½n 
                   por Json para Graficar "TOP 10 Configuracion de vehiculo mas usada en los ultimos 30 dias"
        *  \author: Luis Carlos Manrique Boada
        *  \date: 2019-08-06
        *  \param:  
        */

        function topConfiRec(){

            $filtro = new Aplica_Filtro_Usuari( COD_APLICACION, COD_FILTRO_REMDES, $_SESSION['datos_usuario']['cod_usuari'] );

            $add_query="";
            if($filtro -> listar(self::$conexion)>0){
                $datos_filtro = $filtro -> dar_filtro_multiple(self::$conexion);
                $ultima = count($datos_filtro)-1;
                $add_query .= " AND a.cod_client IN (";
                foreach($datos_filtro as $key => $value){
                $add_query .= " ".$value['clv_filtro'];
                  if($key!=$ultima){
                    $add_query .= ", ";  
                  }
                }
                $add_query .= ") ";
              }

            //Select "TOP 10 Destinos mï¿½s frecuentes y recientes (Ciudad)"
            $query = "
            SELECT 
           'Configuraciones Recurrentes' AS `nombre`,
            f.nom_config as 'configuraciones', 
            count(a.num_despac) as 'cantidad' 
        FROM 
            tab_despac_despac a 
            INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac 
            INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu c ON b.num_placax = c.num_placax 
            INNER JOIN ".BASE_DATOS.".tab_vehige_config f ON c.num_config = f.num_config
            INNER JOIN ".BASE_DATOS.".tab_despac_destin d ON a.num_despac = d.num_despac
            INNER JOIN ".BASE_DATOS.".tab_genera_remdes e ON d.cod_remdes = e.cod_remdes
            INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu j ON b.num_placax = j.num_placax
        WHERE 
            DATE(a.fec_despac) BETWEEN (
                CURDATE() - INTERVAL 30 DAY
            ) 
            AND CURDATE() $add_query
            ";

            $query .= self::filtersFields()."
            GROUP BY c.num_config
            ORDER BY 2 DESC
            LIMIT    10";
            //Execute query 
            $query = new Consulta($query, self::$conexion);
            $despachos = $query -> ret_matrix('a');
            
            $data = array();
            $json = array();

            //Create data structure
            foreach ($despachos as $key => $value)
            {
                //Validate existence
                if(!isset($data[$value["nombre"]]))
                {
                    $data[$value["nombre"]] = ["name" => $value["nombre"], "data" => self::$dates];
                }

                //Fill data
                $data[$value["nombre"]]["data"][$value["configuraciones"]] = [$value["cantidad"], $value["configuraciones"]];
            }

            //Clean data structure
            foreach ($data as $key => $value)
            {
                $tempData = array();
                $tempData["data"] = array();

                foreach ($value["data"] as $key1 => $value1)
                {
                    array_push($tempData["data"], $value1);
                }

                array_push($json, $tempData);
            }
            
            echo json_encode(self::cleanArray($json));
        }

    }

    new FilterData();
?>