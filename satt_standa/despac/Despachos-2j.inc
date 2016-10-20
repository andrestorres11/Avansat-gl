<?php
ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);
//ini_set("memory_limit", "1024M");
class Despachos
{
    var $cod_aplica = NULL;
    var $servic = NULL;
    var $opcion = NULL;
    var $conexion = NULL;
    var $opcion_base = NULL;
    var $paginador = NULL;
    var $GeneralFunctions = NULL;
    var $cod_ciuori = NULL;
    var $nom_ciuori = NULL;
    var $cod_ciudes = NULL;
    var $nom_ciudes = NULL;
    var $nom_transp = NULL;
    var $ind_status = NULL;

    function __construct($servic, $opcion, $aplica, $conexion, $opcion_base = NULL)
    {
        $this->cod_aplica = $aplica;
        $this->servic = $servic;
        $this->opcion = $opcion;
        $this->conexion = $conexion;
        $this->opcion_base = $opcion_base;
        $this->paginador = new Paginador_listado($opcion_base, $servic, $conexion);
        $this->GeneralFunctions = new GeneralFunctions($this->conexion);
    }

    function ListadoPrincipal($datos_usuario, $busque = 0, $dir_export = "", $des_retras = 0, $validmatriz = NULL, $GLOBALS_ADD = NULL, $finali = 0, $desfut = 0, $liscon = 0, $matcondesp = NULL, $opcurban = 0)
    {
        $this->ind_status = $GLOBALS_ADD[0]["valor"];
        switch ($this->ind_status)
        {
            case 'S':
                ;
                break;
            case 'L':
                ;
                break;

            default:
                ;
                break;
        }

        if ($GLOBALS_ADD[9]["valor"] != '')
            $_REQUEST["bdespac"] = $GLOBALS_ADD[9]["valor"];

        $query = "SELECT a.ind_remdes, a.ind_desurb
  		     FROM " . BASE_DATOS . ".tab_config_parame a
  		   ";

        $consulta = new Consulta($query, $this->conexion);
        $manredes = $consulta->ret_arreglo();
        $desurb = $manredes[1];
        $manredes = $manredes[0];


        $titori[0][0] = 0;
        $titori[0][1] = "Origen";
        $titdes[0][0] = 0;
        $titpla[0][0] = 0;
        $titpla[0][1] = "Placa";
        $titdes[0][1] = "Destino";
        $titra[0][0] = 0;
        $titra[0][1] = "Transportadora";
        $tipla[0][0] = 0;
        $tipla[0][1] = "Placa";

        $todas[0][0] = 0;
        $todas[0][1] = "Todas";

        $this->nom_transp = $titra[0][1];

        if ($matcondesp[0]["cribus"] == 1 || !$matcondesp)
            $nomtabcribus = "noveda";
        else if ($matcondesp[0]["cribus"] == 2)
            $nomtabcribus = "contro";

        $query = "SELECT f.cod_ciudad, CONCAT(f.abr_ciudad,' (',LEFT(j.abr_depart,4),') ')
              FROM " . BASE_DATOS . ".tab_despac_despac a,
                   " . BASE_DATOS . ".tab_despac_seguim b,
                   " . BASE_DATOS . ".tab_despac_vehige d,
                   " . BASE_DATOS . ".tab_genera_ciudad f,
                   " . BASE_DATOS . ".tab_genera_depart j 
             WHERE a.num_despac = d.num_despac AND
             	   	 a.num_despac = b.num_despac AND
                   a.cod_ciuori = f.cod_ciudad AND
                   f.cod_depart = j.cod_depart AND
                   f.cod_paisxx = j.cod_paisxx AND
                   a.fec_salida Is Not Null AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S'
	   ";

        /* if($opcurban)
          $query .= " AND m.ind_urbano = '1'"; */

        if (!$finali)
        {
            if (!$desfut)
                $query .= " AND a.fec_salida <= NOW()";
            else
                $query .= " AND a.fec_salida > NOW()";
        }

        if ($_REQUEST["bplaca"])
            $query = $query . " AND d.num_placax = '" . $_REQUEST["bplaca"] . "'";
        if ($_REQUEST["bmanifi"])
            $query = $query . " AND a.cod_manifi = '" . $_REQUEST["bmanifi"] . "'";
        if ($_REQUEST["bdespac"])
            $query = $query . " AND a.num_despac = " . $_REQUEST["bdespac"] . "";

        if (!$finali)
            $query .= " AND a.fec_llegad Is Null";
        else
            $query .= " AND a.fec_llegad Is Not Null";

        if ($_REQUEST["destino"])
            $query .= " AND a.cod_ciudes = " . $_REQUEST["destino"] . "";
        if ($_REQUEST["origen"])
            $query .= " AND a.cod_ciuori = " . $_REQUEST["origen"] . "";
        if ($_REQUEST["transp"])
            $query .= " AND d.cod_transp = " . $_REQUEST["transp"] . "";

        if ($busque)
        {
            if ($_REQUEST["placa"])
                $query = $query . " AND d.num_placax = '" . $_REQUEST["placa"] . "'";
            if ($_REQUEST["manifi"])
                $query = $query . " AND a.cod_manifi = '" . $_REQUEST["manifi"] . "'";
            if ($_REQUEST["despac"])
                $query = $query . " AND a.num_despac = '" . $_REQUEST["despac"] . "'";
        }

        if ($validmatriz)
        {
            for ($i = 0; $i < sizeof($validmatriz); $i++)
                $query .= $validmatriz[$i]["linea"];
        }
        if ($datos_usuario["cod_perfil"] == "")
        {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }

            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }
        else
        {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }

            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }

        $query = $query . " GROUP BY 1 ORDER BY 2";
        $consulta = new Consulta($query, $this->conexion);
        $origen = $consulta->ret_matriz();

        if ($_REQUEST["origen"])
            $origen = array_merge($origen, $todas);
        else
            $origen = array_merge($titori, $origen);

        $query = "SELECT f.cod_ciudad, CONCAT( f.abr_ciudad,' (',LEFT(j.abr_depart,4),') ' )
				  FROM " . BASE_DATOS . ".tab_despac_despac a,
					   " . BASE_DATOS . ".tab_despac_seguim b,
					   " . BASE_DATOS . ".tab_despac_vehige d,
					   " . BASE_DATOS . ".tab_genera_ciudad f,
					   " . BASE_DATOS . ".tab_genera_depart j
				  WHERE a.num_despac = d.num_despac AND
						a.num_despac = b.num_despac AND
						a.cod_ciudes = f.cod_ciudad AND
						f.cod_depart = j.cod_depart AND
						f.cod_paisxx = j.cod_paisxx AND
						a.fec_salida Is Not Null AND
						a.ind_anulad = 'R' AND
						a.ind_planru = 'S' ";
		
		if (!$finali)
        {
            if (!$desfut)
                $query .= " AND a.fec_salida <= NOW()";
            else
                $query .= " AND a.fec_salida > NOW()";
        }

        if ($_REQUEST["bplaca"])
            $query = $query . " AND d.num_placax = '" . $_REQUEST["bplaca"] . "'";
        if ($_REQUEST["bmanifi"])
            $query = $query . " AND a.cod_manifi = '" . $_REQUEST["bmanifi"] . "'";
        if ($_REQUEST["bdespac"])
            $query = $query . " AND a.num_despac = " . $_REQUEST["bdespac"] . "";

        if (!$finali)
            $query .= " AND a.fec_llegad Is Null";
        else
            $query .= " AND a.fec_llegad Is Not Null";

        if ($_REQUEST["destino"])
            $query .= " AND a.cod_ciudes = " . $_REQUEST["destino"] . "";
        if ($_REQUEST["origen"])
            $query .= " AND a.cod_ciuori = " . $_REQUEST["origen"] . "";
        if ($_REQUEST["transp"])
            $query .= " AND d.cod_transp = " . $_REQUEST["transp"] . "";

        if ($busque)
        {
            if ($_REQUEST["placa"])
                $query = $query . " AND d.num_placax = '" . $_REQUEST["placa"] . "'";
            if ($_REQUEST["manifi"])
                $query = $query . " AND a.cod_manifi = '" . $_REQUEST["manifi"] . "'";
            if ($_REQUEST["despac"])
                $query = $query . " AND a.num_despac = '" . $_REQUEST["despac"] . "'";
        }

        if ($validmatriz)
        {
            for ($i = 0; $i < sizeof($validmatriz); $i++)
                $query .= $validmatriz[$i]["linea"];
        }

        if ($datos_usuario["cod_perfil"] == "")
        {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }
        else
        {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }

        $query = $query . " GROUP BY 1 ORDER BY 2";

        $consulta = new Consulta($query, $this->conexion);
        $destino = $consulta->ret_matriz();

        if ($_REQUEST["destino"])
            $destino = array_merge($destino, $todas);
        else
            $destino = array_merge($titdes, $destino);

        $query = "SELECT e.cod_tercer,e.abr_tercer
              FROM " . BASE_DATOS . ".tab_despac_despac a,
                   " . BASE_DATOS . ".tab_despac_seguim b,
                   " . BASE_DATOS . ".tab_despac_vehige d,
                   " . BASE_DATOS . ".tab_tercer_tercer e
             WHERE a.num_despac = d.num_despac AND
             	   	 a.num_despac = b.num_despac AND
                   d.cod_transp = e.cod_tercer AND
                   a.fec_salida Is Not Null AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S' 
                   
	   ";


        if (!$finali)
        {
            if (!$desfut)
                $query .= " AND a.fec_salida <= NOW()";
            else
                $query .= " AND a.fec_salida > NOW()";
        }

        if ($_REQUEST["bplaca"])
            $query = $query . " AND d.num_placax = '" . $_REQUEST["bplaca"] . "'";
        if ($_REQUEST["bmanifi"])
            $query = $query . " AND a.cod_manifi = '" . $_REQUEST["bmanifi"] . "'";
        if ($_REQUEST["bdespac"])
            $query = $query . " AND a.num_despac = " . $_REQUEST["bdespac"] . "";

        if (!$finali)
            $query .= " AND a.fec_llegad Is Null";
        else
            $query .= " AND a.fec_llegad Is Not Null";

        if ($_REQUEST["destino"])
            $query .= " AND a.cod_ciudes = " . $_REQUEST["destino"] . "";
        if ($_REQUEST["origen"])
            $query .= " AND a.cod_ciuori = " . $_REQUEST["origen"] . "";
        if ($_REQUEST["transp"])
            $query .= " AND d.cod_transp = " . $_REQUEST["transp"] . "";

        if ($busque)
        {
            if ($_REQUEST["placa"])
                $query = $query . " AND d.num_placax = '" . $_REQUEST["placa"] . "'";
            if ($_REQUEST["manifi"])
                $query = $query . " AND a.cod_manifi = '" . $_REQUEST["manifi"] . "'";
            if ($_REQUEST["despac"])
                $query = $query . " AND a.num_despac = '" . $_REQUEST["despac"] . "'";
        }

        if ($validmatriz)
        {
            for ($i = 0; $i < sizeof($validmatriz); $i++)
                $query .= $validmatriz[$i]["linea"];
        }

        if ($datos_usuario["cod_perfil"] == "")
        {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }
        else
        {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }

        $query = $query . " GROUP BY 1 ORDER BY 2";

        $consulta = new Consulta($query, $this->conexion);
        $transpors = $consulta->ret_matriz();

        if ($_REQUEST["transp"])
            $transpors = array_merge($transpors, $todas);
        else
            $transpors = array_merge($titra, $transpors);
                    
        /*--------------------------------------------------------------------------------------------------------------------------------------*/
        
        $query = "SELECT d.num_placax,d.num_placax
              FROM " . BASE_DATOS . ".tab_despac_despac a,
                   " . BASE_DATOS . ".tab_despac_seguim b,
                   " . BASE_DATOS . ".tab_despac_vehige d,
                   " . BASE_DATOS . ".tab_tercer_tercer e
             WHERE a.num_despac = d.num_despac AND
             	   	 a.num_despac = b.num_despac AND
                   d.cod_transp = e.cod_tercer AND
                   a.fec_salida Is Not Null AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S' 
                   
	   ";


        if (!$finali)
        {
            if (!$desfut)
                $query .= " AND a.fec_salida <= NOW()";
            else
                $query .= " AND a.fec_salida > NOW()";
        }

        if ($_REQUEST["bplaca"])
            $query = $query . " AND d.num_placax = '" . $_REQUEST["bplaca"] . "'";
        if ($_REQUEST["bmanifi"])
            $query = $query . " AND a.cod_manifi = '" . $_REQUEST["bmanifi"] . "'";
        if ($_REQUEST["bdespac"])
            $query = $query . " AND a.num_despac = " . $_REQUEST["bdespac"] . "";

        if (!$finali)
            $query .= " AND a.fec_llegad Is Null";
        else
            $query .= " AND a.fec_llegad Is Not Null";

        if ($_REQUEST["destino"])
            $query .= " AND a.cod_ciudes = " . $_REQUEST["destino"] . "";
        if ($_REQUEST["origen"])
            $query .= " AND a.cod_ciuori = " . $_REQUEST["origen"] . "";
        if ($_REQUEST["transp"])
            $query .= " AND d.cod_transp = " . $_REQUEST["transp"] . "";

        if ($busque)
        {
            if ($_REQUEST["placa"])
                $query = $query . " AND d.num_placax = '" . $_REQUEST["placa"] . "'";
            if ($_REQUEST["manifi"])
                $query = $query . " AND a.cod_manifi = '" . $_REQUEST["manifi"] . "'";
            if ($_REQUEST["despac"])
                $query = $query . " AND a.num_despac = '" . $_REQUEST["despac"] . "'";
        }

        if ($validmatriz)
        {
            for ($i = 0; $i < sizeof($validmatriz); $i++)
                $query .= $validmatriz[$i]["linea"];
        }

        if ($datos_usuario["cod_perfil"] == "")
        {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }
        else
        {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }

        $query = $query . " GROUP BY 1 ORDER BY 2";

        $consulta = new Consulta($query, $this->conexion);
        $placax = $consulta->ret_matriz();
        
        if ($_REQUEST["bplaca"])
            $placax = array_merge($placax, $todas);
        else
            $placax = array_merge($tipla, $placax);
        
        /*--------------------------------------------------------------------------------------------------------------------------------------*/

        

        $defini = array(1, "SI");
        //trae los despachos en ruta
        $query = "SELECT a.num_despac, a.cod_manifi, a.cod_ciuori,
	 								 a.cod_ciudes, f.abr_tercer, d.num_placax,
									 IF( d.nom_conduc IS NOT NULL, d.nom_conduc, e.abr_tercer) AS abr_tercer,
                   IF( a.con_telmov IS NULL, e.num_telmov, a.con_telmov ) AS num_telmov,
                   '', '', '',
									 '', '', '',
									 '', d.cod_transp, a.ind_defini,
									 DATE_FORMAT( a.fec_ultnov, '%Y-%m-%d %H:%i' ), n.abr_tercer AS aseguradora
              FROM " . BASE_DATOS . ".tab_despac_seguim b,
                   " . BASE_DATOS . ".tab_despac_vehige d,
                   " . BASE_DATOS . ".tab_tercer_tercer e,
                   " . BASE_DATOS . ".tab_tercer_tercer f,
                   " . BASE_DATOS . ".tab_despac_despac a
                    LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer n 
                   ON n.cod_tercer = a.cod_asegur
             WHERE a.num_despac = d.num_despac AND
                   a.num_despac = b.num_despac AND
                   d.cod_conduc = e.cod_tercer AND
                   d.cod_transp = f.cod_tercer AND
                   a.fec_salida Is Not Null AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S' 
	";
        if (strtolower($datos_usuario["cod_usuari"]) == 'alogistica')
            $query .= " AND d.cod_transp IN ( SELECT cod_tercer FROM " . BASE_DATOS . ".tab_config_alianz ) ";



        if (!$finali)
        {
            if (!$desfut)
                $query .= " AND a.fec_salida <= NOW()";
            else
                $query .= " AND a.fec_salida > NOW()";
        }

        if ($_REQUEST["bplaca"])
            $query = $query . " AND d.num_placax = '" . $_REQUEST["bplaca"] . "'";
        if ($_REQUEST["bmanifi"])
            $query = $query . " AND a.cod_manifi = '" . $_REQUEST["bmanifi"] . "'";
        if ($_REQUEST["bdespac"])
            $query = $query . " AND a.num_despac = " . $_REQUEST["bdespac"] . "";
        if ($_REQUEST["busq_transp"])
            $query = $query . " AND d.cod_conduc = '" . $_REQUEST["busq_transp"] . "'";
            

        if (!$finali)
            $query .= " AND a.fec_llegad Is Null";
        else
            $query .= " AND a.fec_llegad Is Not Null";

        if ($_REQUEST["destino"])
            $query .= " AND a.cod_ciudes = " . $_REQUEST["destino"] . "";
        if ($_REQUEST["origen"])
            $query .= " AND a.cod_ciuori = " . $_REQUEST["origen"] . "";
        if ($_REQUEST["transp"])
            $query .= " AND d.cod_transp = " . $_REQUEST["transp"] . "";

        if ($busque)
        {
            if ($_REQUEST["placa"])
                $query = $query . " AND d.num_placax = '" . $_REQUEST["placa"] . "'";
            if ($_REQUEST["manifi"])
                $query = $query . " AND a.cod_manifi = '" . $_REQUEST["manifi"] . "'";
            if ($_REQUEST["despac"])
                $query = $query . " AND a.num_despac = '" . $_REQUEST["despac"] . "'";
            
        }


        if ($validmatriz)
        {
            for ($i = 0; $i < sizeof($validmatriz); $i++)
                $query .= $validmatriz[$i]["linea"];
        }

        if ($datos_usuario["cod_perfil"] == "")
        {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }
        else
        {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion))
            {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA

            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            echo "<div style='display:none' >Aplica=" . $this->cod_aplica . " CODFIltroagenci=" . COD_FILTRO_AGENCI . " perfil=" . $datos_usuario["cod_perfil"] . "</div>";
            if ($filtro->listar($this->conexion))
            {

                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }

        if (!$finali)
            $query = $query . " GROUP BY 1 ORDER BY b.fec_alarma,1 ";
        else
            $query = $query . " GROUP BY 1 ORDER BY a.fec_llegad,1";
        //echo $query;
        $matriz = $this->paginador->ejecPaginador($_REQUEST["npagina"], $query);


        $query_exp = $this->paginador->query;

        $ind_porlleg = 0;
        $ind_enrutas = 0;

        if ($finali)
        {

            for ($i = 0; $i < sizeof($matriz); $i++)
            {
                if ($this->GeneralFunctions->ViewVisibilityInterf(array("transp" => $matriz[$i][15])))
                {
                    if ($manredes == 1)
                    {
                        $query = "SELECT a.num_docume
	 			  FROM " . BASE_DATOS . ".tab_despac_remdes a
	 			 WHERE a.num_despac = " . $matriz[$i][0] . "
	 		   ";

                        $consulta = new Consulta($query, $this->conexion);
                        $document_despac = $consulta->ret_matriz();

                        if ($document_despac)
                        {
                            $remisiones_destin = $document_despac[0][0];

                            for ($j = 1; $j < sizeof($document_despac); $j++)
                                if ($document_despac[$j][0])
                                    $remisiones_destin .= ", " . $document_despac[$j][0];

                            if (!$remisiones_destin)
                                $remisiones_destin = "-";
                        }
                        else
                            $remisiones_destin = "-";

                        $matriz[$i][12] = $remisiones_destin;

                        $query = "SELECT a.num_pedido
	 			  FROM " . BASE_DATOS . ".tab_despac_remdes a
	 			 WHERE a.num_despac = " . $matriz[$i][0] . "
	 		   ";

                        $consulta = new Consulta($query, $this->conexion);
                        $document_despac = $consulta->ret_matriz();

                        if ($document_despac)
                        {
                            $pedidos_destin = $document_despac[0][0];

                            for ($j = 1; $j < sizeof($document_despac); $j++)
                                if ($document_despac[$j][0])
                                    $pedidos_destin .= ", " . $document_despac[$j][0];

                            if (!$pedidos_destin)
                                $pedidos_destin = "-";
                        }
                        else
                            $pedidos_destin = "-";

                        $matriz[$i][13] = $pedidos_destin;
                    }

                    $porllegad[] = $matriz[$i];
                    $ind_porlleg++;
                }
            }
        }
        else
        {
            for ($i = 0; $i < sizeof($matriz); $i++)
            {

                if ($this->GeneralFunctions->ViewVisibilityInterf(array("transp" => $matriz[$i][15])))
                {
                    if ($manredes == 1)
                    {
                        $query = "SELECT a.num_docume
	 			  FROM " . BASE_DATOS . ".tab_despac_remdes a
	 			 WHERE a.num_despac = " . $matriz[$i][0] . "
	 		   ";

                        $consulta = new Consulta($query, $this->conexion);
                        $document_despac = $consulta->ret_matriz();

                        if ($document_despac)
                        {
                            $remisiones_destin = $document_despac[0][0];

                            for ($j = 1; $j < sizeof($document_despac); $j++)
                                if ($document_despac[$j][0])
                                    $remisiones_destin .= ", " . $document_despac[$j][0];

                            if (!$remisiones_destin)
                                $remisiones_destin = "-";
                        }
                        else
                            $remisiones_destin = "-";

                        $matriz[$i][12] = $remisiones_destin;

                        $query = "SELECT a.num_pedido
	 			  FROM " . BASE_DATOS . ".tab_despac_remdes a
	 			 WHERE a.num_despac = " . $matriz[$i][0] . "
	 		   ";

                        $consulta = new Consulta($query, $this->conexion);
                        $document_despac = $consulta->ret_matriz();

                        if ($document_despac)
                        {
                            $pedidos_destin = $document_despac[0][0];

                            for ($j = 1; $j < sizeof($document_despac); $j++)
                                if ($document_despac[$j][0])
                                    $pedidos_destin .= ", " . $document_despac[$j][0];

                            if (!$pedidos_destin)
                                $pedidos_destin = "-";
                        }
                        else
                            $pedidos_destin = "-";

                        $matriz[$i][13] = $pedidos_destin;
                    }

                    $query = "SELECT a.cod_rutasx
	             FROM " . BASE_DATOS . ".tab_despac_seguim a
	            WHERE a.num_despac = " . $matriz[$i][0] . "
		       		  GROUP BY 1
	         ";

                    $consulta = new Consulta($query, $this->conexion);
                    $totrutas = $consulta->ret_matriz();

                    if (sizeof($totrutas) < 2)
                        $camporder = "fec_planea";
                    else
                        $camporder = "fec_alarma";

                    $query = "SELECT a.cod_contro 
  						  FROM " . BASE_DATOS . ".tab_despac_seguim a, 
  						  	   " . BASE_DATOS . ".tab_despac_vehige c,
                     (SELECT MAX(b." . $camporder . ") as fec 
  								    FROM " . BASE_DATOS . ".tab_despac_seguim b 
  								    WHERE b.num_despac = " . $matriz[$i][0] . " ) as  d 
  						  WHERE a.num_despac = c.num_despac AND 
  						  		  c.num_despac = " . $matriz[$i][0] . " AND 
  								    a." . $camporder . " = d.fec ";
                    $consulta = new Consulta($query, $this->conexion);

                    $ultimopc = $consulta->ret_matriz();

                    $query = "SELECT a.cod_contro, a.fec_noveda, d.fec_planea 
						  FROM " . BASE_DATOS . ".tab_despac_noveda a, 
							     " . BASE_DATOS . ".tab_despac_seguim d 
						  WHERE a.num_despac = d.num_despac AND 
								    a.cod_rutasx = d.cod_rutasx AND 
								    a.cod_contro = d.cod_contro AND 
								    a.num_despac = " . $matriz[$i][0] . " AND 
								    a.fec_noveda = '" . $matriz[$i][17] . ":00" . "'";

                    $consulta = new Consulta($query, $this->conexion);

                    $ultimnov = $consulta->ret_matriz();

                    if ($ultimnov)
                    {
                        $query = "SELECT a.ind_urbano
	 		      FROM " . BASE_DATOS . ".tab_genera_contro a
	 		     WHERE a.cod_contro = " . $ultimnov[0][0] . " AND
	 		     	   a.ind_urbano = '1'
	 		  ";

                        $consulta = new Consulta($query, $this->conexion);
                        $pcontrurb = $consulta->ret_matriz();
                    }
                    else
                        $pcontrurb = NULL;


                    if ($manredes == 1 && $desurb == 1 && $pcontrurb)
                        $pcomparar = $ultimnov[0][0];
                    else
                        $pcomparar = $ultimopc[0][0];



                    if ($pcomparar == $ultimnov[0][0] || $ultimnov[0][0] == CONS_CODIGO_PCLLEG)
                    {
                        $porllegad[$ind_porlleg] = $matriz[$i];
                        $ind_porlleg++;
                    }
                    else
                    {
                        if ($_REQUEST["defini"] == $matriz[$i]["ind_defini"] || $_REQUEST["defini"] == '')
                        {
                            $desenruta[$ind_enrutas] = $matriz[$i];
                            $ind_enrutas++;
                        }
                    }
                }
            }
        }
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/min.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/jquery.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/poppc.js\"></script>\n";
        echo "<link href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' rel='stylesheet' type='text/css'>";
        if ($desfut)
            $formulario = new Formulario("index.php", "post", "Despachos Salida Futura", "form_item", "", "", "100%");

        else if (!$finali)
        {

            $query = "SELECT nom_alarma, cant_tiempo, cod_colorx

               FROM " . BASE_DATOS . ".tab_genera_alarma

                    ORDER BY 2 ";



            $consulta = new Consulta($query, $this->conexion);

            $alarmas = $consulta->ret_matriz();

            $query = "SELECT cant_tiempo,cod_colorx
           FROM " . BASE_DATOS . ".tab_genera_alarma";
            $consulta = new Consulta($query, $this->conexion);
            $colores = $consulta->ret_matriz();

            echo "<script>setTimeout('document.location.reload(\"index.php?window=central&cod_servic=" . $this->servic . "\")',300000); </script>";



            $formulario = new Formulario("index.php", "post", "Despachos en Ruta", "form_item", "", "", "100%");
        }

        else
            $formulario = new Formulario("index.php", "post", "Despachos Finalizados", "form_item", "", "", "100%");


        if ($busque)
        {
            $formulario->nueva_tabla();
            $formulario->linea("Filtros de Busqueda", 1, "t2");

            $formulario->nueva_tabla();
            $formulario->texto("Placa", "text", "placa", 0, 6, 6, "", $_REQUEST["placa"]);
            $formulario->texto("Manifiesto", "text", "manifi", 0, 7, 7, "", $_REQUEST["manifi"]);
            $formulario->texto("Despacho", "text", "despac", 1, 6, 6, "", $_REQUEST["despac"]);

            $formulario->nueva_tabla();
            $formulario->boton("Buscar", "button\" onClick=\"if(form_item.placa.value || form_item.manifi.value || form_item.despac.value){form.submit()}else{alert('Digite parametro de Busqueda')}", 0);
            $formulario->boton("Todos", "button\" onClick=\"form_item.placa.value=''; form_item.manifi.value=''; form_item.despac.value=''; form.submit()", 0);
        }

        if ($dir_export != "")
        {
            $_SESSION['sql'] = $query_exp;
            //$query_exp = base64_encode($query_exp);
            $exp .= "url=" . NOM_URL_APLICA . "&db=" . BASE_DATOS . "&nom_opcion=" . $dir_export . "&des_retras=" . $des_retras . "&finali=" . $finali;

            $matlinkim[0][0] = "../" . DIR_APLICA_CENTRAL . "/imagenes/pdf.jpg";
            $matlinkim[0][1] = "onClick=\"top.window.open('../" . DIR_APLICA_CENTRAL . "/export/exp_genera_despac.php?" . $exp . "&manpdf=1')\"";

            $formulario->nueva_tabla();
            $formulario->imagen("Exportar", "../" . DIR_APLICA_CENTRAL . "/imagenes/excel.jpg", "Exportar", 30, 30, 0, "onClick=\"top.window.open('../" . DIR_APLICA_CENTRAL . "/export/exp_genera_despac.php?" . $exp . "')\"", 1, 0, $matlinkim);
        }


        if (!$des_retras && !$datos_usuario["bandeja"])
        {
            $formulario->nueva_tabla();


            $query = "SELECT  COUNT(a.num_despac)
                    FROM    " . BASE_DATOS . ".tab_despac_despac a,
                            " . BASE_DATOS . ".tab_despac_vehige b
                    WHERE a.num_despac = b.num_despac AND
                       a.fec_salida is not NULL AND
                       a.fec_llegad is NULL AND
                       b.ind_activo = 'S' ";

            $consulta = new Consulta($query, $this->conexion);
            $maxdespac = $consulta->ret_matriz();

            $maxdespac = $maxdespac[0][0];


            if ($desfut)
                $formulario->linea("Se Encontro un Total de " . $ind_enrutas . " Despacho(s) con Salida Futuro", 0, "t2");
            else if (!$finali)
            {
                if ($GLOBALS_ADD[0]["campo"] == "alacla" && $GLOBALS_ADD[0]["valor"] == "L")
                    $formulario->linea("* Se Encontro un Total de 0 Despacho(s) en Ruta", 0, "t2");
                else if ($GLOBALS_ADD[1]["campo"] == "totregif" && $GLOBALS_ADD[1]["valor"])
                    echo '<td align="left" id="totalID" class="celda_titulo2"><b>Se Encontro un Total de ' . sizeof($desenruta) . ' Despacho(s) en Ruta</b></td>';
                //$formulario -> linea("Se Encontro un Total de ".$GLOBALS_ADD[1]["valor"]." Despacho(s) en Ruta",0,"t2");
                //$formulario -> linea("Se Encontro un Total de ".$ind_enrutas." Despacho(s) en Ruta",0,"t2");          
                //$formulario -> linea("Se Encontro un Total de ".sizeof( $desenruta)." Despacho(s) en Ruta",0,"t2");
                else
                    echo '<td align="left" id="totalID" class="celda_titulo2"><b>Se Encontro un Total de ' . sizeof($desenruta) . ' Despacho(s) en Ruta</b></td>';
                //$formulario -> linea("Se Encontro un Total de ".sizeof( $desenruta )." Despacho(s) en Ruta",0,"t2");
            }
        }

        $formulario->nueva_tabla();

        $en_bandeja = 0;

        if (!$finali)
        {
            for ($i = 0; $i < sizeof($alarmas); $i++)
                echo "<td style ='font-size: 8pt;color: #000000;background-color: " . $alarmas[$i][2] . "'>" . $alarmas[$i][0] . " = " . $alarmas[$i][1] . " Min</td>";

            $formulario->nueva_tabla();
            $formulario->texto("Despacho", "text", "bdespac\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()", 0, 6, 6, "", $_REQUEST["bdespac"], "", "", 1);
            $formulario->linea("Tiempo", 0, "t");
            echo '<td align="center" class="celda_titulo"><b>A cargo Empresa <br></b>';
            echo '<select onchange="form_item.submit()"  id="definiID" onChange="form_item.submit()" name="defini" class="form_01">
				
				<option value=>-</option>
				<option value=1>SI</option>
				<option value=0>NO</option>
			</select>';
            echo "</td>";
            $formulario->texto("Manifiesto", "text", "bmanifi\" onKeyUp=\"if(!isNaN(this.value)){if(this.value == '-'){alert('La Cantidad No es Valida');this.value=''}}else{this.value=''}\" onChange=\"form_item.submit()", 0, 7, 7, "", $_REQUEST["bmanifi"], "", "", 1);
            //$formulario->linea("Aseguradora", 0, "t");
            //$formulario->linea("Novedades", 0, "t");

            if ($manredes == 1)
            {
                $formulario->linea("Remisi&oacute;n", 0, "t");
                $formulario->linea("Pedido", 0, "t");
            }

            if (!$des_retras)
            {
                $formulario->lista_titulo("Origen", "origen\" onChange=\"form_item.submit()\"", $origen, 0);
                $formulario->lista_titulo("Destino", "destino\" onChange=\"form_item.submit()\"", $destino, 0);
                $formulario->lista_titulo("Transportadora", "transp\" onChange=\"form_item.submit()\"", $transpors, 0);
            }
            else
            {
                $formulario->linea("Origen", 0, "t");
                $formulario->linea("Destino", 0, "t");
                $formulario->linea("Transportadora", 0, "t");
            }

            $formulario->texto("Placa", "text", "bplaca\" onChange=\"form_item.submit()", 0, 6, 6, "", $_REQUEST["bplaca"], "", "", 1);
            $formulario->linea("Conductor", 0, "t");
            $formulario->linea("Celular", 0, "t");


            $formulario->linea("Fecha Novedad", 0, "t");
            $formulario->linea("Ultimo P/C", 0, "t");
            $formulario->linea("Novedad", 0, "t");

            $formulario->linea("Generador", 0, "t");
            $formulario->linea("Ultima Nota Control", 0, "t");
            $formulario->linea("Fecha N/C", 1, "t");
            $ind_enrutas = 0;
//--------------------------------------------------------------------------      


            for ($i = 0; $i < sizeof($desenruta); $i++)
            {

                $query = "(SELECT e.ind_fuepla, a.cod_conult,a.cod_ultnov,a.fec_ultnov,a.usr_ultnov,
                b.obs_contro,c.nom_contro,d.nom_noveda,	b.fec_contro,
                b.cod_contro,e.nom_noveda,b.fec_creaci, c.cod_colorx
          FROM	" . BASE_DATOS . ".tab_despac_despac	a	LEFT	JOIN
                " . BASE_DATOS . ".tab_despac_contro	b	ON
                a.num_despac	=	b.num_despac	AND
                a.cod_consec	=	b.cod_consec	LEFT	JOIN
                " . BASE_DATOS . ".tab_genera_noveda	e	ON
                b.cod_noveda	=	e.cod_noveda,
                " . BASE_DATOS . ".tab_genera_contro	c,
                " . BASE_DATOS . ".tab_genera_noveda	d
         WHERE	a.cod_conult	=	c.cod_contro	AND
                a.cod_ultnov	=	d.cod_noveda	AND
                a.num_despac	=	'" . $desenruta[$i][0] . "')
         UNION
       (SELECT	e.ind_fuepla, a.cod_conult,a.cod_ultnov,a.fec_ultnov,a.usr_ultnov,
                b.des_noveda,c.nom_contro,d.nom_noveda,b.fec_noveda,
                b.cod_contro,e.nom_noveda,	b.fec_creaci, c.cod_colorx
          FROM	" . BASE_DATOS . ".tab_despac_despac	a	LEFT	JOIN
                " . BASE_DATOS . ".tab_despac_noveda	b	ON
                a.num_despac	=	b.num_despac	
                LEFT	JOIN " . BASE_DATOS . ".tab_genera_noveda	e	ON
                b.cod_noveda	=	e.cod_noveda,
                " . BASE_DATOS . ".tab_genera_contro	c,
                " . BASE_DATOS . ".tab_genera_noveda	d
         WHERE	a.cod_conult	=	c.cod_contro	AND
                a.cod_ultnov	=	d.cod_noveda	AND
                a.num_despac	=	'" . $desenruta[$i][0] . "')
      ORDER	BY	12	DESC	LIMIT	1";
                $consulta = new Consulta($query, $this->conexion);
                $fuer_plan = $consulta->ret_matriz();

                if ($_REQUEST['FUEPLA'] == '1' && $fuer_plan[0][0] != '1')
                {
                    continue;
                }

                //if( $fuer_plan[0][0]=='1' && $_REQUEST['alacla'] != 'F' )
                //{
                //continue;
                //}

                $valimp = 1;
                $indicad_mostrar_condic = 1;

                //validacion de alarmas nueva --jose
                $query = "SELECT 	 cod_tipdes " .
                        "FROM " . BASE_DATOS . ".tab_despac_despac " .
                        "WHERE num_despac='" . $desenruta[$i][0] . "'";
                $consulta = new Consulta($query, $this->conexion);
                $cod_tipdes = $consulta->ret_matriz();

                $query = "SELECT a.tie_conurb, a.tie_contro, a.cod_tipser 
                FROM " . BASE_DATOS . ".tab_transp_tipser a,
			 				 			 (SELECT MAX(num_consec) AS num_consec 
                        FROM " . BASE_DATOS . ".tab_transp_tipser
                       WHERE cod_transp='" . $desenruta[$i][15] . "') b
               WHERE a.cod_transp='" . $desenruta[$i][15] . "' AND 
                 		 a.num_consec= b.num_consec ";
                $consulta = new Consulta($query, $this->conexion);
                $transpor = $consulta->ret_matriz();

                if ($transpor)
                {
                    if ($transpor[0][2] == '1')
                    {
                        $tiem = 'P';
                    }
                    else
                    {
                        if ($cod_tipdes[0][0] == '1')
                        {
                            $tiem = $transpor[0][0];
                        }
                        else
                        {
                            $tiem = $transpor[0][1];
                        }
                    }
                }
                else
                {
                    if ($cod_tipdes[0][0] == '1')
                        $tiem = 25;
                    else
                        $tiem = 80;
                }

                $query = "SELECT a.usr_ultnov, a.cod_conult, a.cod_ultnov, " .
                        "IF( a.fec_ultnov IS NULL, DATE_FORMAT( a.fec_salida, '%Y-%m-%d %H:%i' ), DATE_FORMAT( a.fec_ultnov, '%Y-%m-%d %H:%i' )  ), " .
                        "a.fec_salida, a.fec_manala ,if(a.ind_defini = '1','SI','NO') as defini " .
                        "FROM " . BASE_DATOS . ".tab_despac_despac a " .
                        "WHERE a.num_despac='" . $desenruta[$i][0] . "'";
                $consulta = new Consulta($query, $this->conexion);
                $despac = $consulta->ret_matriz();

                //busca la novedad 
                $tiem_diff = '';
                $query = "SELECT nom_noveda, ind_fuepla
                FROM " . BASE_DATOS . ".tab_genera_noveda 
               WHERE cod_noveda = '" . $despac[0][2] . "'";
                $consulta = new Consulta($query, $this->conexion);
                $noveda = $consulta->ret_matriz();



                $query = "SELECT nom_contro, cod_colorx
                FROM " . BASE_DATOS . ".tab_genera_contro  
               WHERE cod_contro = '" . $despac[0][1] . "'";
                $consulta = new Consulta($query, $this->conexion);
                $contro_actual = $consulta->ret_arreglo();

                $noveda[0][1] = $contro_actual[0];

                //agreega el tiempo de duracion de la pernoctacion
                if ($despac[0][1] != '')
                {
                    $query = "SELECT tiem_duraci 
                 FROM " . BASE_DATOS . ".tab_despac_contro
                WHERE num_despac = '" . $desenruta[$i][0] . "' AND
                      fec_contro = '" . $despac[0][3] . "" . ":00'
            ORDER BY fec_creaci DESC";
                    $consulta = new Consulta($query, $this->conexion);
                    $tiem_diff = $consulta->ret_matriz();
                }

                if ($tiem_diff && $tiem_diff[0][0] != 0)
                {
                    $tiem = $tiem_diff[0][0];
                }
                else
                {
                    if ($despac[0][2] != '')
                    {
                        $query = "SELECT tiem_duraci 
                   FROM " . BASE_DATOS . ".tab_despac_noveda
                  WHERE num_despac = '" . $desenruta[$i][0] . "' AND
                        fec_noveda = '" . $despac[0][3] . "" . ":00'
               ORDER BY fec_creaci DESC";
                        $consulta = new Consulta($query, $this->conexion);
                        $tiem_diff = $consulta->ret_matriz();
                    }
                    if ($tiem_diff && $tiem_diff[0][0] != 0)
                    {
                        $tiem = $tiem_diff[0][0];
                    }
                }

                if ($despac[0][2] == '' && $despac[0][1] == '' || ( $despac[0]['fec_manala'] == $despac[0]["fec_salida"] ))
                    $tiem = '0';

                //Se hace la validacion para alarmas acumulables
                $query = "SELECT fec_manala 
                FROM " . BASE_DATOS . ".tab_despac_despac 
               WHERE num_despac = '" . $desenruta[$i][0] . "'";
                $consulta = new Consulta($query, $this->conexion);
                $mFecManala = $consulta->ret_matriz();

                $mFecManala = $mFecManala[0][0];
                $tiemdif = '';
                if ($mFecManala)
                {
                    $query = "SELECT CAST( (TIME_TO_SEC( TIMEDIFF(NOW(), '" . $mFecManala . "')) / 60) AS UNSIGNED INTEGER )";
                    $consulta = new Consulta($query, $this->conexion);
                    $mTieDif = $consulta->ret_matriz();
                    $tiemdif = $mTieDif[0][0];
                }

                if (is_numeric($tiem))
                {
                    if ($tiemdif == '')
                    {
                        $query = "SELECT TIMEDIFF(  NOW(), '" . $despac[0][3] . "' ) ";
                        $consulta = new Consulta($query, $this->conexion);
                        $tiemdif = $consulta->ret_matriz();
                        $tiemdif = explode(":", $tiemdif[0][0]);
                        $tiemdif = ($tiemdif[0] * 60) + $tiemdif[1];
                        //valida para monitoreo mixto y un perfil especifico si ya deberia haber pasado por un puesto
                        $fisico = 0;
                    }

                    $tieret = "";
                    if ($tiemdif > $tiem)
                    {
                        $indicad_mostrar_condic = 1;
                        $tieret = $tiemdif - $tiem;
                    }
                }
                else
                {
                    $query = "SELECT 	a.cod_contro,a.fec_alarma " .
                            "FROM " . BASE_DATOS . ".tab_despac_seguim a " .
                            "WHERE a.num_despac='" . $desenruta[$i][0] . "'
                       AND a.ind_estado = 1 
                       AND a.cod_contro NOT IN (SELECT cod_contro FROM " . BASE_DATOS . ".tab_despac_noveda b 
                       WHERE b.num_despac='" . $desenruta[$i][0] . "' AND a.num_despac = b.num_despac
                       AND a.cod_rutasx = b.cod_rutasx)
              ORDER BY a.fec_planea ";
                    $consulta = new Consulta($query, $this->conexion);
                    $sitios = $consulta->ret_matriz();

                    $fecha = date('Y-m-d H:i:s');
                    if ($fecha < $sitios[0][1])
                        $query = "SELECT TIMEDIFF(  '" . $sitios[0][1] . "',NOW()   ) ";
                    else
                        $query = "SELECT TIMEDIFF(  NOW(),'" . $sitios[0][1] . "'   ) ";

                    $consulta = new Consulta($query, $this->conexion);
                    $tieret = $tiemdif;
                    $tiemdif = $consulta->ret_matriz();
                    $tiemdif = explode(":", $tiemdif[0][0]);
                    $tiemdif = ($tiemdif[0] * 60) + $tiemdif[1];
                    $tieret = "";
                    $indicad_mostrar_condic = 1;
                    $tieret = $tiemdif - $tieret;
                    if ($fecha < $sitios[0][1])
                        $tieret = $tieret * (-1);
                }

                if ($tieret > 0)
                {
                    $y = 1;

                    for ($z = 0; $z <= sizeof($colores); $z++)
                    {
                        if ($z + 1 == $_REQUEST['alacla'])
                        {
                            $color_alacla = $colores[$z][1];
                        }
                        if ($tieret <= $colores[$z][0])
                        {
                            $x = $z;
                            $y = 2;
                            break;
                        }
                    }

                    if ($y == 1)
                    {
                        $color_a[0] = $colores[sizeof($colores) - 1][1];
                    }
                    else
                    {
                        $color_a[0] = $colores[$x][1];
                    }
                }
                else
                {
                    $color_a[0] = '#FFFFFF';




                    if ('S' == $_REQUEST['alacla'])
                    {
                        $color_alacla = '#FFFFFF';
                    }
                }

                if ($color_a[0] != $color_alacla && $_REQUEST["alacla"])
                {
                    //Si el despacho tiene un color distinto al del alacla no se imprime ese despacho
                    continue;
                }
                else
                {
                    //Se cuentan cuantos despachos hay en ruta para ese alacla
                    $total_desenruta++;
                }

                if ($tieret != "")
                    $desenruta[$i]["tiempo"] = $tieret;
                else
                if ($tiemdif != "")
                    $desenruta[$i]["tiempo"] = $tiemdif - $tiem;

                //Generador del Manifiesto.
                $generador = "SELECT a.abr_tercer
				      		  FROM " . BASE_DATOS . ".tab_tercer_tercer a,
						        	   " . BASE_DATOS . ".tab_despac_despac b
						       WHERE a.cod_tercer = b.cod_client AND
							           b.num_despac = '" . $desenruta[$i][0] . "' ";
                $consulta = new Consulta($generador, $this->conexion);
                $generador = $consulta->ret_matriz();

                $generador = $generador [0][0];

                $nota = "SELECT LEFT( a.obs_contro, 50 ), a.fec_contro
		 				  FROM " . BASE_DATOS . ".tab_despac_contro a
						  WHERE a.num_despac = '" . $desenruta[$i][0] . "' 
				  ORDER BY a.cod_consec DESC ";
                $consulta = new Consulta($nota, $this->conexion);
                $nota = $consulta->ret_matriz();

                $nota = $nota[0];

                $llamadas1 = "SELECT COUNT( 1 )
								  FROM " . BASE_DATOS . ".tab_despac_contro a
								  WHERE a.num_despac = '" . $desenruta[$i][0] . "'";

                $consulta = new Consulta($llamadas1, $this->conexion);
                $llamadas1 = $consulta->ret_matriz();
                $llamadas1 = $llamadas1[0][0];

                $llamadas2 = "SELECT COUNT( 1 )
								  FROM " . BASE_DATOS . ".tab_despac_noveda a
								  WHERE a.num_despac = '" . $desenruta[$i][0] . "'";

                $consulta = new Consulta($llamadas2, $this->conexion);
                $llamadas2 = $consulta->ret_matriz();
                $llamadas2 = $llamadas2[0][0];

            }
          
          if( $desfut )
            $desenruta = $this -> orderMultiDimensionalArray( $desenruta, 'tiempo', 0);
          
          /*echo "<pre>";
          print_r($desenruta);
          echo "</pre>";
          die();*/
            for ($i = 0; $i < sizeof($desenruta); $i++)
            {
                if ($datos_usuario["bandeja"])
                {
                    if ($valimp && $color_a[0] == "FFFF66")
                    {
                        if ($indicad_mostrar_condic)
                        {
                            $ciudad_o = $this->getSeleccCiudad($desenruta[$i][2], 1);
                            $ciudad_d = $this->getSeleccCiudad($desenruta[$i][3], 1);

                            $num_despac = $desenruta[$i][0];

                            $desenruta[$i][0] = "<a href=\"index.php?cod_servic=" . $this->servic . "&window=central&despac=" . $desenruta[$i][0] . "&opcion=" . $this->opcion . " \"target=\"centralFrame\">" . $desenruta[$i][0] . "</a>";

                            $formulario->linea($desenruta[$i][0], 0, "i", 0, 0, "left", $color_a[0]);

                            $formulario->linea($desenruta[$i]["tiempo"], 0, "i");

                            $formulario->linea($despac[0]["defini"], 0, "i");
                            $formulario->linea($desenruta[$i][1], 0, "i");
                            $formulario->linea($desenruta[$i]['aseguradora'], 0, "i");



                            $formulario->linea(( (int) $llamadas1 + (int) $llamadas2), 0, "i");

                            if ($manredes == 1)
                            {
                                $formulario->linea($desenruta[$i][12], 0, "i");
                                $formulario->linea($desenruta[$i][13], 0, "i");
                            }

                            $formulario->linea($ciudad_o[0][1], 0, "i");
                            $formulario->linea($ciudad_d[0][1], 0, "i");
                            $formulario->linea($desenruta[$i][4], 0, "i");
                            $formulario->linea($desenruta[$i][5], 0, "i");
                            $formulario->linea($desenruta[$i][6], 0, "i");
                            $formulario->linea($desenruta[$i][7], 0, "i");
                            $formulario->linea($despac[0][3], 0, "i");
                            $formulario->linea($noveda[0][1], 0, "i");
                            $formulario->linea($noveda[0][0], 0, "i");

                            $formulario->linea($generador, 0, "i");
                            echo "<td class='celda_info' style='width:100px' >$nota[0]&nbsp;</td>";
                            $formulario->linea($nota[1], 1, "i");

                            $en_bandeja++;
                        }
                    }
                }
                else if ($valimp)
                {
                    echo "<div style='display:none'>" . $desenruta[$i]["bandeja"] . "</div>";
                    $indicad_mostrar_condic = 1;

                    if ($indicad_mostrar_condic)
                    {
                        //vlix85
                        $_EAL = TRUE;
                        //-----------------------------------------------------------------------------------------------------
                        if (in_array($_SESSION["datos_usuario"]['cod_perfil'], array(COD_PERFIL_ADMINIST, COD_PERFIL_SUPEFARO, COD_PERFIL_SUPERUSR, COD_PERFIL_SUPEGENE)))
                        {
                            $_EAL = $this->GET_EAL($desenruta[$i][0]);
                            if (!$_EAL && (isset($_REQUEST["EAL"]) && (int) $_REQUEST["EAL"] == 1) && (int) $transpor[0][2] != 2)
                                continue;
                        }
                        //-----------------------------------------------------------------------------------------------------


                        $ind_enrutas++;
                        $vlix1017 = $desenruta[$i][0];
                        $ciudad_o = $this->getSeleccCiudad($desenruta[$i][2], 1);
                        $ciudad_d = $this->getSeleccCiudad($desenruta[$i][3], 1);
                        $desenruta[$i][0] = "<a href=\"index.php?cod_servic=" . $this->servic . "&window=central&tie_ultnov=" . $desenruta[$i]["tiempo"] . "&despac=" . $desenruta[$i][0] . "&opcion=" . $this->opcion . " \"target=\"centralFrame\">" . $desenruta[$i][0] . "</a>";
                        //Jorge 120404
                        $formulario->linea($desenruta[$i][0], 0, "i", 0, 0, "left", $color_a[0]);
                        $formulario->linea($desenruta[$i]["tiempo"], 0, "i");
                        $formulario->linea($despac[0]["defini"], 0, "i");

                        //vlix85
                        //-------------------------------------------
                        if ($_EAL && in_array($_SESSION["datos_usuario"]['cod_perfil'], array(COD_PERFIL_ADMINIST, COD_PERFIL_SUPEFARO, COD_PERFIL_SUPERUSR, COD_PERFIL_SUPEGENE)) && (int) $transpor[0][2] != 2)
                        {
                            $formulario->linea($desenruta[$i][1], 0, "i", 0, 0, "center\" onclick=\"poppc($vlix1017);", "#7fff99; cursor:pointer");
                        }
                        else
                        {
                            $formulario->linea($desenruta[$i][1], 0, "i", 0, 0, "center", "#FFF");
                        }
                        //-------------------------------------------

                        //$formulario->linea($desenruta[$i]['aseguradora'], 0, "i");

                        //$formulario->linea(( (int) $llamadas1 + (int) $llamadas2), 0, "i");

                        if ($manredes == 1)
                        {
                            $formulario->linea($desenruta[$i][12], 0, "i");
                            $formulario->linea($desenruta[$i][13], 0, "i");
                        }

                        $formulario->linea($ciudad_o[0][1], 0, "i");
                        $formulario->linea($ciudad_d[0][1], 0, "i");
                        $formulario->linea($desenruta[$i][4], 0, "i");
                        $formulario->linea($desenruta[$i][5], 0, "i");
                        $formulario->linea($desenruta[$i][6], 0, "i");
                        $formulario->linea($desenruta[$i][7], 0, "i");


                        $formulario->linea($despac[0][3], 0, "i");
                        $formulario->linea($noveda[0][1], 0, "i");
                        $formulario->linea($noveda[0][0], 0, "i");

                        $formulario->linea($generador, 0, "i");

                        echo "<td class='celda_info' style='width:100px' >$nota[0]&nbsp;</td>";
                        $formulario->linea($nota[1], 1, "i");
                    }
                }
            }

//--------------------------------------------------------------------------     
            $formulario->oculto("cod_cond\" id=\"totID", $ind_enrutas, 0);
            echo "<script language=\"JavaScript\"> document.getElementById('totalID').innerHTML = 'Se Encontro un Total de '+ document.getElementById('totID').value +' Despacho(s) en Ruta';</script>\n";
        }

        //------------------------------------------------------------------
        echo "<div id='pc' title='Puestos de Control sin Novedad' style='background: #FFF; padding: 0; margin: 0;'>&nbsp;</div>";
        //------------------------------------------------------------------


        if ($datos_usuario["bandeja"])
        {
            $formulario->nueva_tabla();
            $formulario->linea("Se Encontro un Total de " . $en_bandeja . " Despacho(s) con Alarma Amarilla", 0, "t2");
        }

        //if($ind_porlleg && !$des_retras && !$desfut && $_REQUEST['alacla']!='ecl')
        if ($ind_porlleg && !$des_retras && !$desfut)
        {

            $formulario->nueva_tabla();


            if (!$datos_usuario["bandeja"])
            {

                if (!$finali)
                {
                    if ($GLOBALS_ADD[0]["campo"] == "alacla" && $GLOBALS_ADD[0]["valor"] != "L" && $GLOBALS_ADD[0]["valor"])
                        $totalultdes = 0;
                    else
                        $totalultdes = $ind_porlleg;

                    if ($manredes == 1 && $desurb == 1)
                        $formulario->linea("Se Encontro un Total de <span id='tot2ID'>" . $totalultdes . "</span> Despacho(s) Urbanos &oacute; Pendientes por Llegada", 0, "t2");
                    else
                        $formulario->linea("Se Encontro un Total de <span id='tot2ID'>" . $totalultdes . "</span> Despacho(s) Pendientes por Llegada", 0, "t2");
                }

                else
                    $formulario->linea("Se Encontro un Total de <span id='tot2ID'>" . $this->paginador->totalr . "</span>  Despacho(s) Finalizado(s)", 0, "t2");

                $varadici[$i]["nomvar"] = "origen";
                $varadici[$i]["valvar"] = $_REQUEST["origen"];
                $i++;
                $varadici[$i]["nomvar"] = "destino";
                $varadici[$i]["valvar"] = $_REQUEST["destino"];
                $i++;
                $varadici[$i]["nomvar"] = "despac";
                $varadici[$i]["valvar"] = $_REQUEST["despac"];
                $i++;
                $varadici[$i]["nomvar"] = "manifi";
                $varadici[$i]["valvar"] = $_REQUEST["manifi"];
                $i++;
                $varadici[$i]["nomvar"] = "placa";
                $varadici[$i]["valvar"] = $_REQUEST["placa"];
                $i++;
                $varadici[$i]["nomvar"] = "transp";
                $varadici[$i]["valvar"] = $_REQUEST["transp"];

                $this->paginador->getPaginas($formulario, "npagina", $varadici);



                $formulario->nueva_tabla();
                $formulario->linea("Despacho", 0, "t");
                $formulario->linea("Manifiesto", 0, "t");

                if ($manredes == 1)
                {
                    $formulario->linea("Remisi&oacute;n", 0, "t");
                    $formulario->linea("Pedido", 0, "t");
                }

                if (!$des_retras && $finali)
                {
                    $formulario->lista_titulo("Origen", "origen\" onChange=\"form_item.submit()\"", $origen, 0);
                    $formulario->lista_titulo("Destino", "destino\" onChange=\"form_item.submit()\"", $destino, 0);
                    $formulario->lista_titulo("Transportadora", "transp\" onChange=\"form_item.submit()\"", $transpors, 0);
                    $formulario->lista_titulo("Placas", "bplaca\" onChange=\"form_item.submit()", $placax, 0); 
                }
                else
                {
                    $formulario->linea("Origen", 0, "t");
                    $formulario->linea("Destino", 0, "t");
                    $formulario->linea("Transportadora", 0, "t");
                    $formulario->linea("Placa", 0, "t");
                }

                //$formulario->linea("Placa", 0, "t");
                $formulario->linea("Conductor", 0, "t");
                $formulario->linea("Celular", 0, "t");

                $formulario->linea("Fecha Novedad", 0, "t");
                //$formulario->linea("Ultimo P/C", 0, "t");
                $formulario->linea("Novedad", 0, "t");

                $formulario->linea("Generador", 0, "t");
                $formulario->linea("Ultima Nota Control", 0, "t");
                $formulario->linea("Fecha N/C", 1, "t");
            }


            for ($i = 0; $i < sizeof($porllegad); $i++)
            {
                //Si le dieron clic a los despachos por fuera de plataforma, los despachos pendientes por dar llegada no son necesarios
                if ($_REQUEST['FUEPLA'] == '1')
                {
                    continue;
                }
                $query = "SELECT a.ind_urbano
       		      FROM " . BASE_DATOS . ".tab_genera_contro a,
       		           " . BASE_DATOS . ".tab_despac_seguim b
       		     WHERE a.cod_contro = b.cod_contro AND
       		           a.ind_urbano = '1' AND
       		           b.num_despac = " . $porllegad[$i][0] . "
       		    ";

                $consulta = new Consulta($query, $this->conexion);
                $despurban = $consulta->ret_matriz();

                //trae la ultima fecha de la ultima novedad


                $query = "SELECT c.nom_contro,c.cod_contro
                  FROM " . BASE_DATOS . ".tab_despac_noveda a,
                       " . BASE_DATOS . ".tab_genera_contro c
                 WHERE a.num_despac = '" . $porllegad[$i][0] . "' AND
                       a.cod_contro = c.cod_contro AND
                       a.fec_noveda = '" . $maximo[0][0] . "'
                       GROUP BY a.num_despac ";
                $nom_contro = new Consulta($query, $this->conexion);
                $nom_contro = $nom_contro->ret_arreglo();

                $query = "SELECT b.nom_noveda, b.cod_noveda, a.fec_ultnov
                  FROM " . BASE_DATOS . ".tab_despac_despac a,
                       " . BASE_DATOS . ".tab_genera_noveda b
                 WHERE a.num_despac = '" . $porllegad[$i][0] . "' AND
                       a.cod_ultnov = b.cod_noveda
                       GROUP BY a.num_despac ";
                $nom_noveda = new Consulta($query, $this->conexion);
                $nom_noveda = $nom_noveda->ret_arreglo();
                $maximo[0][0] = $nom_noveda[2];

                //Generador del Manifiesto.
                $generador = "SELECT a.abr_tercer
									  FROM " . BASE_DATOS . ".tab_tercer_tercer a,
										   " . BASE_DATOS . ".tab_despac_despac b
									  WHERE a.cod_tercer = b.cod_client AND
											b.num_despac = '" . $porllegad[$i][0] . "' ";

                $consulta = new Consulta($generador, $this->conexion);
                $generador = $consulta->ret_matriz();
                $generador = $generador [0][0];

                $nota = "SELECT LEFT( a.obs_contro, 50 ), a.fec_contro
						  FROM " . BASE_DATOS . ".tab_despac_contro a
						  WHERE a.num_despac = '" . $porllegad[$i][0] . "' 
						  ORDER BY a.cod_consec DESC ";

                $consulta = new Consulta($nota, $this->conexion);
                $nota = $consulta->ret_matriz();
                $nota = $nota[0];

                //Trae la Ultima Observacion de nota de controlador
                $query = "SELECT c.obs_contro
                 FROM " . BASE_DATOS . ".tab_despac_contro c,
											" . BASE_DATOS . ".tab_despac_despac a
                WHERE a.num_despac = " . $porllegad[$i][0] . " AND
											a.num_despac = c.num_despac AND
                      c.cod_consec = a.cod_consec
                      GROUP BY 1
                  ";
                $consulta = new Consulta($query, $this->conexion);
                $ultnotco = $consulta->ret_matriz();

                if (!$liscon)
                {
                    $porllegad[$i][8] = $maximo[0][0];
                    $porllegad[$i][9] = $nom_contro[0];
                    $porllegad[$i][10] = $nom_noveda[0];
                }
                else
                    $porllegad[$i][8] = $ultnotco[0][0];


                $indicad_mostrar_condic = 0;

                if ($GLOBALS_ADD[0]["campo"] == "alacla" && $GLOBALS_ADD[0]["valor"] == "L")
                    $indicad_mostrar_condic = 1;
                else if ($GLOBALS_ADD[0]["campo"] == "alacla" && !$GLOBALS_ADD[0]["valor"])
                    $indicad_mostrar_condic = 1;
                else if ($finali || $desfut || !$GLOBALS_ADD)
                    $indicad_mostrar_condic = 1;

                $ciudad_o = $this->getSeleccCiudad($porllegad[$i][2]);
                $ciudad_d = $this->getSeleccCiudad($porllegad[$i][3]);

                $estilo_celda = "i";

                if ($despurban)
                    $estilo_celda = "iu";

                $indicad_mostrar_condic = 1;

                if ($datos_usuario["bandeja"])
                {
                    
                }
                else if ($indicad_mostrar_condic)
                {
                    //vlix85
                    $_EAL1 = TRUE;
                    //-----------------------------------------------------------------------------------------------------
                    if (in_array($_SESSION["datos_usuario"]['cod_perfil'], array(COD_PERFIL_ADMINIST, COD_PERFIL_SUPEFARO, COD_PERFIL_SUPERUSR, COD_PERFIL_SUPEGENE)))
                    {
                        $_EAL1 = $this->GET_EAL($porllegad[$i][0]);
                        if (!$_EAL1 && (isset($_REQUEST["EAL"]) && (int) $_REQUEST["EAL"] == 1) && (int) $transpor[0][2] != 2)
                            continue;
                    }
                    $count++;
                    //----------------------------------------------------------------------------------------------------- 
                    $vlix1017 = $porllegad[$i][0];
                    $porllegad[$i][0] = "<a href=\"index.php?cod_servic=" . $this->servic . "&window=central&despac=" . $porllegad[$i][0] . "&opcion=" . $this->opcion . " \"target=\"centralFrame\">" . $porllegad[$i][0] . "</a>";
                    $formulario->linea($porllegad[$i][0], 0, $estilo_celda);
                    //vlix85
                    //-------------------------------------------
                    if ($_EAL1 && in_array($_SESSION["datos_usuario"]['cod_perfil'], array(COD_PERFIL_ADMINIST, COD_PERFIL_SUPEFARO, COD_PERFIL_SUPERUSR, COD_PERFIL_SUPEGENE)) && (int) $transpor[0][2] != 2)
                    {
                        $formulario->linea($porllegad[$i][1], 0, "i", 0, 0, "center\" onclick=\"poppc($vlix1017);", "#7fff99; cursor: pointer;");
                    }
                    else
                    {
                        $formulario->linea($porllegad[$i][1], 0, "i", 0, 0, "center", "#FFF");
                    }
                    //-------------------------------------------
                    //$formulario -> linea($porllegad[$i][1],0,$estilo_celda); OLD

                    if ($manredes == 1)
                    {
                        $formulario->linea($porllegad[$i][12], 0, $estilo_celda);
                        $formulario->linea($porllegad[$i][13], 0, $estilo_celda);
                    }

                    $formulario->linea($ciudad_o[0][1], 0, $estilo_celda);
                    $formulario->linea($ciudad_d[0][1], 0, $estilo_celda);
                    $formulario->linea($porllegad[$i][4], 0, $estilo_celda);
                    $formulario->linea($porllegad[$i][5], 0, $estilo_celda);
                    $formulario->linea($porllegad[$i][6], 0, $estilo_celda);
                    $formulario->linea($porllegad[$i][7], 0, $estilo_celda);


                    $formulario->linea($porllegad[$i][8], 0, $estilo_celda);
                    //$formulario->linea($porllegad[$i][9], 0, $estilo_celda);
                    $formulario->linea($porllegad[$i][10], 0, $estilo_celda);

                    $formulario->linea($generador, 0, $estilo_celda);

                    $style_td = "celda_info";
                    if ($estilo_celda == 'iu')
                        $style_td = "celda_urban_relleno";

                    echo "<td class='$style_td' style='width:100px' >$nota[0]&nbsp;</td>";
                    $formulario->linea($nota[1], 1, $estilo_celda);
                }
            }
        }

        if (!$ind_porlleg && !$desfut)
        {
            $formulario->nueva_tabla();
            if (!$finali)
                $formulario->linea("No Se Encontrar&oacute;n Despachos Pendientes por dar Llegada", 0, "t2");
            else
                $formulario->linea("No Se Encontrar&oacute;n Despachos Finalizados", 0, "t2");
        }


        echo "<script language=\"JavaScript\"> document.getElementById('tot2ID').innerHTML = '" . $count . "';</script>\n";

        $this->paginador->getPaginas($formulario, "npagina", $varadici);

        $formulario->nueva_tabla();



        if ($GLOBALS_ADD)
        {

            for ($i = 0; $i < sizeof($GLOBALS_ADD); $i++)
                $formulario->oculto($GLOBALS_ADD[$i]["campo"], $GLOBALS_ADD[$i]["valor"], 0);
        }

        if ($GLOBALS_ADD["atras"])
        {
            $formulario->botoni("Atras", "javascript:history.go(-1)", 0);
            $formulario->oculto("atras", "si", 0);
        }


        $formulario->oculto("usuario", "$usuario", 0);

        $formulario->oculto("valor", $valor, 0);

        $formulario->oculto("window", "central", 0);

        $formulario->oculto("cod_servic", $this->servic, 0);
        $formulario->oculto("central\" id=\"central", DIR_APLICA_CENTRAL, 0);
        if ($this->opcion_base)
            $formulario->oculto("opcion", $this->opcion_base, 0);



        $formulario->cerrar();

        if ($_REQUEST["defini"] == 0 || $_REQUEST["defini"] == 1)
        {
            echo "<script language=\"JavaScript\">";
            echo "document.getElementById('definiID').value=" . $_REQUEST["defini"] . ";";
            echo "</script>";
        }
    }

    function Encabezado($despac, $formulario, $datos_usuario, $finali = 0, $nomexp = NULL)
    {
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/new_ajax.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/functions.js\"></script>\n";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/proto.js\"></script>\n";
        $query = "SELECT a.ind_remdes
  		      FROM " . BASE_DATOS . ".tab_config_parame a
  		     WHERE a.ind_remdes = '1'
  		   ";

        $consulta = new Consulta($query, $this->conexion);
        $manredes = $consulta->ret_matriz();

        $query = "SELECT a.num_despac, a.cod_manifi, 
              IF( b.nom_conduc IS NOT NULL, b.nom_conduc, c.abr_tercer) AS abr_tercer, IF( b.cod_condu2 IS NOT NULL, b.cod_condu2, c.cod_tercer ) AS cod_tercer,
              IF( a.con_telmov IS NULL OR a.con_telmov = '', c.num_telmov, a.con_telmov ),
              IF( a.con_telef1 IS NULL OR a.con_telef1 = '', c.num_telef1, a.con_telef1),ind_defini,
              e.nom_operad, f.nom_califi, a.tie_contra, a.ind_tiemod, a.obs_tiemod, 
              if(a.fec_citcar Is Null,'SIN CONFIRMAR',DATE_FORMAT( CONCAT( a.fec_citcar, ' ', a.hor_citcar ),'%H:%i %d-%m-%Y')) as fec_citcar
            FROM " . BASE_DATOS . ".tab_despac_despac a,
                 " . BASE_DATOS . ".tab_despac_vehige b,
                 " . BASE_DATOS . ".tab_tercer_tercer c,
                 ". BASE_DATOS .".tab_tercer_conduc d 
				 LEFT JOIN ".  BASE_DATOS .".tab_genera_califi f ON f.cod_califi = d.cod_califi
                 LEFT JOIN ".  BASE_DATOS .".tab_operad_operad e ON e.cod_operad = d.cod_operad
            WHERE a.num_despac = b.num_despac AND
                    b.cod_conduc = c.cod_tercer AND
                    d.cod_tercer = c.cod_tercer AND
                    a.num_despac = " . $despac . "
            ";
        $consulta = new Consulta($query, $this->conexion);
        $encab1 = $consulta->ret_matriz();

        $query = "SELECT b.num_placax,d.nom_marcax,e.nom_lineax,f.nom_colorx,
		   		   g.nom_config,h.nom_carroc,c.ano_modelo
              FROM " . BASE_DATOS . ".tab_despac_vehige b,
                   " . BASE_DATOS . ".tab_genera_marcas d,
		   		   " . BASE_DATOS . ".tab_vehige_lineas e,
                   " . BASE_DATOS . ".tab_vehige_colore f,
                   " . BASE_DATOS . ".tab_vehige_carroc h,
                   " . BASE_DATOS . ".tab_vehicu_vehicu c LEFT JOIN " . BASE_DATOS . ".tab_vehige_config g ON c.num_config = g.num_config 
             WHERE b.num_placax = c.num_placax AND
                   c.cod_marcax = d.cod_marcax AND
                   c.cod_marcax = e.cod_marcax AND
                   c.cod_lineax = e.cod_lineax AND
                   c.cod_colorx = f.cod_colorx AND
                   c.cod_carroc = h.cod_carroc AND
                   b.num_despac = " . $despac . "
	  ";

        $consulta = new Consulta($query, $this->conexion);
        $encab2 = $consulta->ret_matriz();

        $query = "SELECT g.nom_agenci,e.nom_rutasx,a.cod_ciuori,a.cod_ciudes,
                   if(a.fec_salida Is Null,'SIN CONFIRMAR',DATE_FORMAT(a.fec_salida,'%H:%i %d-%m-%Y')),
                   if(b.fec_llegpl Is Null,'SIN CONFIRMAR',DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y')),
		   		   DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y'),
                   a.gps_operad,
                   IF(a.gps_usuari = '' OR a.gps_usuari IS NULL, k.nom_usrgps, a.gps_usuari) AS gps_usuari,
                   IF(a.gps_paswor = '' OR a.gps_paswor IS NULL, k.clv_usrgps, a.gps_paswor) AS gps_paswor,
                   DATE_FORMAT(a.fec_salsis,'%H:%i %d-%m-%Y'),
                   b.cod_transp, h.abr_tercer AS aseguradora, a.num_poliza, i.nom_operad AS nom_opegps,
                   IF(a.gps_paswor = '' OR a.gps_paswor IS NULL, 2, 1) AS ind_paswor,  j.abr_tercer AS generador, 
                   if(b.fec_findes Is Null,DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y'),DATE_FORMAT(b.fec_findes ,'%H:%i %d-%m-%Y')) as fec_findes
              FROM " . BASE_DATOS . ".tab_despac_vehige b,
                   " . BASE_DATOS . ".tab_genera_rutasx e,
                   " . BASE_DATOS . ".tab_genera_agenci g,
                   " . BASE_DATOS . ".tab_despac_despac a LEFT JOIN
                   " . BASE_DATOS . ".tab_despac_gpsxxx k ON a.num_despac = k.num_despac LEFT JOIN
                   " . BD_STANDA  . ".tab_genera_opegps i ON k.cod_opegps = i.cod_operad
                   LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer h ON a.cod_asegur = h.cod_tercer 
                   LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer j ON a.cod_client = j.cod_tercer 
             WHERE a.num_despac = b.num_despac AND
                   b.cod_rutasx = e.cod_rutasx AND
                   b.cod_agenci = g.cod_agenci AND
                   a.num_despac = " . $despac . "
           "; 

        $consulta = new Consulta($query, $this->conexion);
        $encab3 = $consulta->ret_matriz();
		
		
		//---------------------------------------------------------------------------
		 $query0 = "SELECT c.abr_tercer, a.val_declar, a.nom_mercan, a.num_poliza
					 FROM " . BASE_DATOS . ".tab_despac_poliza a,
					      " . BASE_DATOS . ".tab_despac_despac b,
						  " . BASE_DATOS . ".tab_tercer_tercer c
				    WHERE a.num_despac = b.num_despac
					  AND a.cod_tomado = c.cod_tercer
					  AND a.num_despac = '".$despac."' ";
		
		$consulta = new Consulta($query0, $this->conexion);
        $encab99 = $consulta->ret_matriz();
		//---------------------------------------------------------------------------

        $origen = $this->getSeleccCiudad($encab3[0][2], 1);
        $destin = $this->getSeleccCiudad($encab3[0][3], 1);
        //transportadora
        $query = "SELECT nom_tercer, b.cod_tercer
  		      FROM " . BASE_DATOS . ".tab_despac_vehige a,
                 " . BASE_DATOS . ".tab_tercer_tercer b
  		     WHERE a.cod_transp = b.cod_tercer
                 AND a.num_despac = " . $despac . "";

        $consulta = new Consulta($query, $this->conexion);
        $transp = $consulta->ret_matriz();

        //numero de Novedades
        $sql = "(SELECT b.num_despac
           FROM  " . BASE_DATOS . ".tab_despac_contro b
           WHERE b.num_despac = '$despac') 
           UNION ALL
           (SELECT b.num_despac
           FROM " . BASE_DATOS . ".tab_despac_noveda b
           WHERE b.num_despac = '$despac')
           ORDER BY 1";
        $consulta = new Consulta($sql, $this->conexion);
        $novedades = $consulta->ret_matriz();
        $query = "SELECT a.ind_fincal
  		      FROM " . BASE_DATOS . ".tab_config_parame a
  		     WHERE a.ind_fincal = '1'
  		   ";

        $consulta = new Consulta($query, $this->conexion);
        $viscalti = $consulta->ret_matriz();

        if ($viscalti && $finali)
        {
            $query = "SELECT MAX(a.fec_planea)
   		       FROM " . BASE_DATOS . ".tab_despac_seguim a
   		      WHERE a.num_despac = " . $despac . "
   		    ";

            $consulta = new Consulta($query, $this->conexion);
            $fllegpla = $consulta->ret_matriz();

            $query = "SELECT SUM(a.val_pernoc)
   		       FROM " . BASE_DATOS . ".tab_despac_pernoc a
   		      WHERE a.num_despac = " . $despac . "
   		    ";

            $consulta = new Consulta($query, $this->conexion);
            $tiemprog = $consulta->ret_matriz();

            $query = "SELECT (TIME_TO_SEC(TIMEDIFF(b.fec_salipl,a.fec_salida))/60),
   		            (TIME_TO_SEC(TIMEDIFF('" . $fllegpla[0][0] . "',a.fec_llegad))/60),
   		            (TIME_TO_SEC(TIMEDIFF(a.fec_llegad,a.fec_salida))/60)
   		       FROM " . BASE_DATOS . ".tab_despac_despac a,
   		            " . BASE_DATOS . ".tab_despac_vehige b
   		      WHERE a.num_despac = b.num_despac AND
   		            a.num_despac = " . $despac . "
   		    ";

            $consulta = new Consulta($query, $this->conexion);
            $calminti = $consulta->ret_matriz();
        }

        if ($nomexp)
        {
            $exp .= "url=" . NOM_URL_APLICA . "&db=" . BASE_DATOS . "&nomexp=" . $nomexp . "&finali=" . $finali . "&despac=" . $despac . "&tipexp=2&cod_aplica=" . $this->cod_aplica . "&cod_usuari=" . $datos_usuario["cod_usuari"] . "&cod_perfil=" . $datos_usuario["cod_perfil"] . "";

            $matlinkim[0][0] = "../" . DIR_APLICA_CENTRAL . "/imagenes/pdf.jpg";
            $matlinkim[0][1] = "onClick=\"top.window.open('../" . DIR_APLICA_CENTRAL . "/export/exp_genera_despac.php?" . $exp . "&manpdf=1')\"";

            $formulario->nueva_tabla();
            $formulario->imagen("Exportar", "../" . DIR_APLICA_CENTRAL . "/imagenes/excel.jpg", "Exportar", 30, 30, 0, "onClick=\"top.window.open('../" . DIR_APLICA_CENTRAL . "/export/exp_genera_despac.php?" . $exp . "')\"", 0, 0, $matlinkim);
        }

        $formulario->nueva_tabla();
        $formulario->linea("Informaci&oacute;n Principal", 1, "t2");

        $formulario->nueva_tabla();
        $formulario->linea("Despacho", 0, "t");
        $formulario->linea($encab1[0][0]?$encab1[0][0]:'NA', 0, "i");
        $formulario->linea("Origen", 0, "t");
        $formulario->linea($origen[0][1]?$origen[0][1]:'NA', 1, "i");
        $formulario->linea("Manifiesto", 0, "t");
        $formulario->linea($encab1[0][1]?$encab1[0][1]:'NA', 0, "i");
        $formulario->linea("Destino", 0, "t");
        $formulario->linea($destin[0][1]?$destin[0][1]:'NA', 1, "i");
        $formulario->linea("Agencia", 0, "t");
        $formulario->linea($encab3[0][0]?$encab3[0][0]:'NA', 0, "i");
        $formulario->linea("Transportadora", 0, "t");
        $formulario->linea($transp[0][0]?$transp[0][0]:'NA', 1, "i");
        $formulario->linea("Ruta", 0, "t");
        $formulario->linea($encab3[0][1]?$encab3[0][1]:'NA', 1, "i");
        $formulario->linea("Conductor", 0, "t");
        $formulario->linea($encab1[0][2]?$encab1[0][2]:'NA', 0, "i");
        $formulario->linea("Hora/Fecha Sistema", 0, "t");
        $formulario->linea($encab3[0][11]?$encab3[0][11]:'NA', 1, "i");
		//************************************************
		$califi = (!$encab1[0][8]) ? "NA" : $encab1[0][8] ;
		$formulario->linea("Calificación", 0, "t");
        $formulario->linea($califi, 0, "i");
        $formulario->linea("Generador", 0, "t");
        $formulario->linea($encab3[0]['generador']?$encab3[0]['generador']:'NA', 1, "i");
		//************************************************
        $formulario->linea("Fecha Salida", 0, "t");
        $formulario->linea($encab3[0][4]?$encab3[0][4]:'NA', 0, "i");
        $formulario->linea("C.C.", 0, "t");
        $formulario->linea($encab1[0][3]?$encab1[0][3]:'NA', 1, "i");

        //----------------------------------
        $this->cod_ciuori = $origen[0][0];
        $this->nom_ciuori = $origen[0][1];
        $this->cod_ciudes = $destin[0][0];
        $this->nom_ciudes = $destin[0][1];
        //----------------------------------

        if (!$finali)
        {
            $formulario->linea("Fecha Planeada Llegada", 0, "t");
            $formulario->linea($encab3[0][5]?$encab3[0][5]:'NA', 0, "i");
        }
        else
        {
            $formulario->linea("Fecha Llegada", 0, "t");
            $formulario->linea($encab3[0][7]?$encab3[0][7]:'NA', 0, "i");
        }
        $formulario->linea("Celular", 0, "t");
        $formulario->linea($encab1[0][7]." - ".$encab1[0][4], 1, "i");
        
        /*  NUEVAS FECHAS ESPECIFICAS PARA CORONA  */
        $formulario->linea("Fecha Real Llegada", 0, "t");
        $formulario->linea($encab3[0][18]?$encab3[0][18]:'NA', 0, "i");
        $formulario->linea("Fecha Cita de Cargue", 0, "t");
        $formulario->linea($encab1[0][12]?$encab1[0][12]:'DESCONOCIDA', 1, "i");
        /*******************************************/
        
        $formulario->linea("Fecha Creaci&oacute;n", 0, "t");
        $formulario->linea($encab3[0][6]?$encab3[0][6]:'NA', 0, "i");
        $formulario->linea("Telefono", 0, "t");
        $formulario->linea($encab1[0][5]?$encab1[0][5]:'NA', 1, "i");
        $formulario->linea("Placa", 0, "t");
        $formulario->linea($encab2[0][0]?$encab2[0][0]:'NA', 0, "i");
        $formulario->linea("Marca", 0, "t");
        $formulario->linea($encab2[0][1]?$encab2[0][1]:'NA', 1, "i");
        $formulario->linea("Configuraci&oacute;n", 0, "t");
        $formulario->linea($encab2[0][4]?$encab2[0][4]:'NA', 0, "i");
        $formulario->linea("Linea", 0, "t");
        $formulario->linea($encab2[0][2]?$encab2[0][2]:'NA', 1, "i");
        $formulario->linea("Carroceria", 0, "t");
        $formulario->linea($encab2[0][5]?$encab2[0][5]:'NA', 0, "i");
        $formulario->linea("Color", 0, "t");
        $formulario->linea($encab2[0][3]?$encab2[0][3]:'NA', 1, "i");
        $formulario->linea("Modelo", 0, "t");
        $formulario->linea($encab2[0][6]?$encab2[0][6]:'NA', 0, "i");
        $formulario->linea("Operador Gps", 0, "t");
        
        if( trim($encab3[0][8]) != '' && $encab3[0][8] != NULL )
          $formulario->linea($encab3[0][8]?$encab3[0][8]:'NA', 1, "i");
        else
          $formulario->linea($encab3[0]['nom_opegps']?$encab3[0]['nom_opegps']:'NA', 1, "i");
          
          
        /*************************************************************************************************************************************************/    
        $subquery = "SELECT MAX(num_consec)
                    FROM ".BASE_DATOS.".tab_transp_tipser
                   WHERE ind_estado = '1' AND
                      cod_transp = '".$transp[0][1]."' ";
        
        $query = "SELECT tip_factur
                    FROM ".BASE_DATOS.".tab_transp_tipser
                   WHERE ind_estado = '1' AND
                      num_consec = (".$subquery.") AND
                      cod_transp = '".$transp[0][1]."' ";
        

        $consul = new Consulta($query, $this -> conexion);
        $consul = $consul -> ret_matriz();
        $tipfactur = $consul[0][0];
        if( $tipfactur == '0')
          $tipfactur = " ";
        elseif( $tipfactur == '1')
          $tipfactur = " - (Por Despacho)";
        elseif( $tipfactur == '2')
          $tipfactur = " - (Por Registro)";
        
    /*************************************************************************************************************************************************/
    
        $formulario->linea("Usuario", 0, "t");        
        $formulario->linea($encab3[0][9]?$encab3[0][9]:'NA', 0, "i");
        
        $formulario->linea("Contraseña Gps", 0, "t");
        if($encab3[0][ind_paswor]==2)
            $formulario->linea(base64_decode($encab3[0][10])?base64_decode($encab3[0][10]):'NA', 1, "i");
        else
            $formulario->linea(($encab3[0][10]?$encab3[0][10]:'NA'), 1, "i");
        
        $formulario->linea("No. Novedades", 0, "t");
        $formulario->linea(sizeof($novedades)."".$tipfactur, 0, "i");
        $formulario->linea("Aseguradora", 0, "t");
        $formulario->linea($encab3[0]["aseguradora"]?$encab3[0]["aseguradora"]:'NA', 1, "i");
        $formulario->linea("Num Poliza", 0, "t");
		
		if(count($encab99)>0){
			$encab3[0]["num_poliza"] = $encab99[0]["num_poliza"];
		}
		$formulario->linea($encab3[0]["num_poliza"]?$encab3[0]["num_poliza"]:'NA', 0, "i");
		
		if(count($encab99)>0){
		
			$formulario->linea("Tomador de la Poliza", 0, "t");
			$formulario->linea($encab99[0]["abr_tercer"]?$encab99[0]["abr_tercer"]:'NA', 1, "i");
			
			$formulario->linea("Valor Declarado", 0, "t");
			$formulario->linea($encab99[0]["val_declar"]?$encab99[0]["val_declar"]:'NA', 0, "i");
			
			$formulario->linea("Mercancia", 0, "t");
			$formulario->linea($encab99[0]["nom_mercan"]?$encab99[0]["nom_mercan"]:'NA', 1, "i");
			
		}
		
		if( $encab1[0][10] )
		{
			
			$formulario->linea("", 1, "i");
			$formulario->linea("Tiempo Modificado para el Despacho:", 0, "t");
			$formulario->linea($encab1[0][9]." mins.", 0, "i");
			
			$formulario->linea("Observaciones de Modificación:", 0, "t");
			$formulario->linea($encab1[0][11]?$encab1[0][11]:'NA', 1, "i");
		
		}
		
	
		
		
        $formulario->botoni("Protocolos", "protocolos(" . $encab3[0]['cod_transp'] . ",'" . DIR_APLICA_CENTRAL . "')", 0);
        
        $query = "SELECT a.bin_fotoxx, b.nom_contro, b.cod_contro
              FROM ".BASE_DATOS.".tab_despac_images a,
                   ".BASE_DATOS.".tab_genera_contro b
              WHERE a.cod_contro = b.cod_contro AND
                    a.num_despac = '".$encab1[0][0]."'";
        
        $consul = new Consulta($query, $this -> conexion);
        $mShowButton = $consul -> ret_matriz();
        $mFlag = false;
        for( $a = 0; $a < sizeof($mShowButton); $a++)
        {
          if($mShowButton[$a]['bin_fotoxx'] != NULL || $mShowButton[$a]['bin_fotoxx'] != '' )
          $mFlag = true;
        }
                        
                        
        if($mFlag == true)               
        $formulario->botoni("Fotos", "ImagenesDespac(" . $encab1[0][0] . ",'" . DIR_APLICA_CENTRAL . "')", 0);
        
        if ($viscalti && $finali)
        {
            for ($i = 0; $i < 3; $i++)
            {
                if ($calminti[0][$i] < 0)
                {
                    $estilo[$i] = "e";
                    $calminti[0][$i] *= -1;
                }
                else
                    $estilo[$i] = "a";
            }

            $formulario->linea("Diferencia Salida P/E", 0, "t");
            $formulario->linea($this->getCantTiempo($calminti[0][0]), 0, $estilo[0]);
            $formulario->linea("Diferencia Llegada P/E", 0, "t");
            $formulario->linea($this->getCantTiempo($calminti[0][1]), 1, $estilo[1]);
            $formulario->linea("Tiempo Recorrido", 0, "t");
            $formulario->linea($this->getCantTiempo($calminti[0][2]), 0, $estilo[2]);
            $formulario->linea("Tiempo Novedades Programadas", 0, "t");
            $formulario->linea($this->getCantTiempo($tiemprog[0][0]), 1, "i");
        }

        if ($manredes)
        {
            $query = "SELECT a.num_remdes,a.nom_remdes,a.obs_adicio,b.cod_ciudad,
   			         b.dir_destin,b.val_pesoxx,b.num_docume,b.num_pedido,
   			         if(c.ind_tonela = '" . COD_ESTADO_ACTIVO . "',c.val_costos / c.can_tonela,c.val_costos),
   			         if(c.ind_tonela = '" . COD_ESTADO_ACTIVO . "',(c.val_costos / c.can_tonela) * b.val_pesoxx,c.val_costos),
   			         if(b.cod_tabfle IS NOT NULL,d.nom_trayec,'-'),
   			         if(c.ind_tonela != '" . COD_ESTADO_ACTIVO . "',' (Viaje)',''), b.val_bultox
   			    FROM " . BASE_DATOS . ".tab_genera_remdes a,
   			         " . BASE_DATOS . ".tab_despac_remdes b LEFT JOIN
   			         " . BASE_DATOS . ".tab_tablax_fletes c ON
   			         b.cod_tabfle = c.cod_consec LEFT JOIN
   			         " . BASE_DATOS . ".tab_genera_trayec d ON
   			         c.cod_trayec = d.cod_trayec
   			   WHERE a.cod_remdes = b.cod_remdes AND
   			         a.ind_remdes = '2' AND
   			         b.num_despac = " . $_REQUEST["despac"] . "
   			 ";

            $consulta = new Consulta($query, $this->conexion);
            $liremdes = $consulta->ret_matriz();

            $formulario->nueva_tabla();
            $formulario->linea("Selecci&oacute;n de Destinatarios", 1, "t2");

            $formulario->nueva_tabla();
            $formulario->linea("Documento/C&oacute;digo", 0, "t");
            $formulario->linea("Nombre", 0, "t");
            $formulario->linea("Observaciones", 0, "t");
            $formulario->linea("Ciudad", 0, "t");
            $formulario->linea("Direcci&oacute;n", 0, "t");
            $formulario->linea("Bultos", 0, "t");
            $formulario->linea("Peso (Tn)", 0, "t");
            $formulario->linea("Valor (Unit)", 0, "t");
            $formulario->linea("Valor Flete", 0, "t");
            $formulario->linea("Trayecto", 0, "t");
            $formulario->linea("Remisi&oacute;n", 0, "t");
            $formulario->linea("Pedido", 1, "t");

            if ($liremdes)
            {
                for ($i = 0; $i < sizeof($liremdes); $i++)
                {
                    $ciudestin = $this->getSeleccCiudad($liremdes[$i][3]);

                    $formulario->linea($liremdes[$i][0], 0, "i");
                    $formulario->linea($liremdes[$i][1], 0, "i");
                    $formulario->linea($liremdes[$i][2], 0, "i");
                    $formulario->linea($ciudestin[0][1], 0, "i");
                    $formulario->linea($liremdes[$i][4], 0, "i");
                    $formulario->linea($liremdes[$i][12], 0, "i");
                    $formulario->linea($liremdes[$i][5], 0, "i");
                    $formulario->linea($liremdes[$i][8] . $liremdes[$i][11], 0, "i");
                    $formulario->linea($liremdes[$i][9], 0, "i");
                    $formulario->linea($liremdes[$i][10], 0, "i");
                    $formulario->linea($liremdes[$i][6], 0, "i");
                    $formulario->linea($liremdes[$i][7], 1, "i");
                }
            }
            else
            {
                $formulario->nueva_tabla();
                $formulario->linea("No se Encontrar&oacute;n Destinatarios Relacionados al Despacho", 1, "e");
            }
        }
/*------------------------------------------------------------NUEVA OPCION DE LISTAR LAS REMESAS------------------------------------------------------------------------*/
		$query = "SELECT num_despac,cod_remesa,fec_estent,
						 val_pesoxx,val_volume,des_empaqu,
						 des_mercan,abr_client,abr_remite,
						 abr_destin
				FROM ".BASE_DATOS.".tab_despac_remesa 
				WHERE num_despac = '".$_REQUEST["despac"]."'";
		$consulta = new Consulta($query, $this->conexion);
        $remesas = $consulta->ret_matriz();
		
		if ( $remesas )
        {
			$formulario->nueva_tabla();
			$formulario->linea("Informaci&oacute;n de Remesas", 1, "t2");

			$formulario->nueva_tabla();
			$formulario->linea("Remesa", 0, "t");
			$formulario->linea("Fecha Est. Entrega", 0, "t");
			$formulario->linea("Peso T/n", 0, "t");
			$formulario->linea("Volumen", 0, "t");
			$formulario->linea("Empaque", 0, "t");
			$formulario->linea("Mercancia", 0, "t");
			$formulario->linea("Cliente", 0, "t");
			$formulario->linea("Remitente", 0, "t");
			$formulario->linea("Destinatario", 1, "t");
			
			

			for ( $i = 0; $i < sizeof( $remesas ); $i++ )
			{
			
				$fec_lledes = $remesas[$i][fec_estent];

				
				$bg = "";                    
				if( !$fec_lledes || $fec_lledes == "0000-00-00 00:00:00" )
					$bg = "  style='background-color:#CC99CC; padding:3px 10px; ' ";
				
				if (!$remesas[$i][2])
					$remesas[$i][2] = "-";

				if (!$remesas[$i][1])
					$remesas[$i][1] = "-";

				
				$fec_estent = $remesas[$i]['fec_estent'];

				$formulario->linea($remesas[$i][1], 0, "i");
				$formulario->linea($remesas[$i][2], 0, "i");
				$formulario->linea($remesas[$i][3], 0, "i");
				$formulario->linea($remesas[$i][4], 0, "i");
				$formulario->linea($remesas[$i][5], 0, "i");
				$formulario->linea($remesas[$i][6], 0, "i");
				$formulario->linea($remesas[$i][7], 0, "i");
				$formulario->linea($remesas[$i][8], 0, "i");
				$formulario->linea($remesas[$i][9], 1, "i");
			}
        }
		/*------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
		
        $query = "(SELECT a.nom_noveda, b.obs_contro as obs, fec_contro as fec, a.ind_notsup
           FROM " . BASE_DATOS . ".tab_genera_noveda a,
                " . BASE_DATOS . ".tab_despac_contro b
           WHERE a.cod_noveda=b.cod_noveda AND 
                 b.num_despac = '" . $encab1[0][0] . "' AND 
                 a.nov_especi ='1')
           UNION 
           (SELECT a.nom_noveda, b.des_noveda as obs, fec_noveda as fec,  a.ind_notsup
           FROM " . BASE_DATOS . ".tab_genera_noveda a,
                " . BASE_DATOS . ".tab_despac_noveda b
           WHERE a.cod_noveda=b.cod_noveda AND 
                 b.num_despac = '" . $encab1[0][0] . "' AND 
                 a.nov_especi ='1')
					 ORDER BY fec";
        $consulta = new Consulta($query, $this->conexion);
        $novespec = $consulta->ret_matriz();
        $formulario->nueva_tabla();
        $formulario->linea("Novedades Especiales", 1, "t2");
        $formulario->nueva_tabla();
        $formulario->linea("Tipo de Novedad", 0, "t");
        $formulario->linea("Detalle", 0, "t");
        $formulario->linea("Fecha y Hora del Sistema  ", 0, "t");
        for ($i = 0; $i < sizeof($novespec); $i++)
        {
            if ($i % 2)
            {
                $estilo = "#DDFFBB;";
            }
            else
            {
                $estilo = "#FFFFFF";
            }
            $formulario->nueva_tabla();
            if( $novespec[$i][3] == '1' )
              $color_notsup = 'color:#0101DF;';
            else
              $color_notsup = 'color:#000000;';
            $formulario->linea($novespec[$i][0], 0, "i", "16%\" style=\"$color_notsup background:$estilo;");
            echo "<td align='left' class='celda_info' style='$color_notsup background:$estilo'><b>";
            $limit = 50;
            $x = 0;
            for ($s = 0; $s < strlen($novespec[$i][1]); $s++)
            {
                echo $novespec[$i][1][$s];
                $x++;
                if ($x == $limit)
                {
                    echo "<br>";
                    $x = 0;
                }
            }
            echo "</td>";

            $formulario->linea($novespec[$i][2], 0, "i", "16%\" style=\"$color_notsup background:$estilo;");
        }
        //busqueda de novedades especiales
    }

    function PlanDeRuta($despac, $formulario, $link = 0, $finali = 0, $opcurban = 0, $datos_usuario = NULL, $lleg = NULL, $tie_ultnov = NULL)//Jorge 120404
    {
        /* if($datos_usuario)
          {
          $query = "SELECT a.cod_perfil
          FROM ".BASE_DATOS.".tab_perfil_servic a
          WHERE a.cod_perfil = ".$datos_usuario["cod_perfil"]." AND
          a.cod_servic = 1330
          ";

          $consulta = new Consulta($query, $this -> conexion);
          $pernotco = $consulta -> ret_matriz();
          } */

        //datos de las observaciones
        $query = "SELECT a.obs_despac,b.obs_medcom,b.obs_proesp,a.obs_llegad, a.fec_salida 

              FROM " . BASE_DATOS . ".tab_despac_despac a,

		   " . BASE_DATOS . ".tab_despac_vehige b

             WHERE a.num_despac = b.num_despac AND

                   a.num_despac = " . $despac . "

	  ";



        $consulta = new Consulta($query, $this->conexion);

        $observ = $consulta->ret_matriz();



        //datos de los seguimientos

        $query = "(SELECT b.nom_contro,c.nom_noveda,a.tiem_duraci,a.obs_contro as des,
		         DATE_FORMAT(a.fec_contro,'%H:%i %d-%m-%Y') as fec,a.usr_creaci,a.val_retras,d.nom_sitiox as nom_sitiox,
             a.fec_contro
            FROM  " . BASE_DATOS . ".tab_genera_contro b,
		              " . BASE_DATOS . ".tab_genera_noveda c, 
                  " . BASE_DATOS . ".tab_despac_contro a LEFT JOIN " . BASE_DATOS . ".tab_despac_sitio d ON a.cod_sitiox = d.cod_sitiox  
             WHERE a.cod_contro = b.cod_contro AND
		               a.cod_noveda = c.cod_noveda AND
                   a.num_despac = " . $despac . " AND 
                   a.obs_contro != '')
             UNION
            (SELECT b.nom_contro,c.nom_noveda,a.tiem_duraci,a.des_noveda as des,
		         DATE_FORMAT(a.fec_noveda,'%H:%i %d-%m-%Y') as fec,a.usr_creaci,a.val_retras,b.nom_contro as nom_sitiox,
             a.fec_noveda
            FROM  " . BASE_DATOS . ".tab_genera_contro b,
		              " . BASE_DATOS . ".tab_genera_noveda c,
                  " . BASE_DATOS . ".tab_despac_noveda a 
             WHERE a.cod_contro = b.cod_contro AND
		               a.cod_noveda = c.cod_noveda AND
                   a.num_despac = " . $despac . " AND 
                   a.des_noveda != '')
            ORDER BY 9 DESC
          ";


        $consulta = new Consulta($query, $this->conexion);

        $seguim = $consulta->ret_matriz();



        $query = "SELECT a.cod_rutasx

	      FROM " . BASE_DATOS . ".tab_despac_seguim a

	     WHERE a.num_despac = " . $despac . "

		   GROUP BY 1

	   ";



        $consulta = new Consulta($query, $this->conexion);

        $totrutas = $consulta->ret_matriz();



        if (sizeof($totrutas) < 2)
            $camporder = "fec_planea";

        else
            $camporder = "fec_alarma";



        //datos del plan de ruta
        /*
        $query = "SELECT IF( ( 
                           SELECT z.nom_contro 
                             FROM " . BASE_DATOS . ".tab_genera_contro z,
                                  " . BASE_DATOS . ".tab_homolo_ealxxx y,
                                  " . BASE_DATOS . ".tab_homolo_trafico x
                            WHERE z.cod_contro = y.cod_pcxfar 
                              AND y.cod_pcxcli = x.cod_pcxbas
                              AND y.cod_tercer = x.cod_transp
                              AND x.cod_pcxfar = a.cod_contro
                              AND x.cod_rutfar = a.cod_rutasx
                              AND x.cod_transp = e.cod_transp
                            LIMIT 1
                           ) IS NULL, IF( c.ind_virtua = '1',CONCAT( c.nom_contro, ' (Virtual)' ), c.nom_contro ), 
                         ( SELECT z.nom_contro 
                             FROM " . BASE_DATOS . ".tab_genera_contro z,
                                  " . BASE_DATOS . ".tab_homolo_ealxxx y,
                                  " . BASE_DATOS . ".tab_homolo_trafico x
                            WHERE z.cod_contro = y.cod_pcxfar 
                              AND y.cod_pcxcli = x.cod_pcxbas
                              AND y.cod_tercer = x.cod_transp
                              AND x.cod_pcxfar = a.cod_contro
                              AND x.cod_rutfar = a.cod_rutasx
                              AND x.cod_transp = e.cod_transp
                            LIMIT 1
                          ) ),
						 DATE_FORMAT( a.fec_planea, '%H:%i %d-%m-%Y' ),
						 if(b.fec_noveda IS NOT NULL,DATE_FORMAT(b.fec_noveda,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_alarma,'%H:%i %d-%m-%Y')),
						 d.nom_noveda,DATE_FORMAT(b.fec_creaci,'%H:%i %d-%m-%Y'),
						 b.des_noveda,a.fec_planea,a.cod_contro, ";
        if (sizeof($totrutas) < 2)
            $query .= "a.fec_planea,";
        else
            $query .= "if(b.fec_noveda IS NOT NULL,b.fec_noveda,a." . $camporder . "),";
        $query .= "b.usr_creaci,a.fec_alarma,'indlink',c.ind_urbano,b.val_retras,
             b.fec_noveda as fec,c.ind_virtua,e.cod_transp,a.ind_estado,
             DATE_FORMAT( a.fec_planea, '%H:%i' )as hora, a.cod_rutasx, c.cod_colorx
              FROM " . BASE_DATOS . ".tab_despac_vehige e,
                   " . BASE_DATOS . ".tab_genera_contro c,
                   " . BASE_DATOS . ".tab_despac_seguim a LEFT JOIN
                   " . BASE_DATOS . ".tab_despac_noveda b ON
                   a.num_despac = b.num_despac AND
                   a.cod_contro = b.cod_contro LEFT JOIN
                   " . BASE_DATOS . ".tab_genera_noveda d ON
                   b.cod_noveda = d.cod_noveda
             WHERE a.cod_contro = c.cod_contro AND
                   a.num_despac = e.num_despac AND
                   e.num_despac = " . $despac . " AND
                   c.cod_contro = a.cod_contro AND 
                   a.num_despac = e.num_despac";


        if ($opcurban)
            $query .= " AND c.ind_urbano = '1'";

        $query .= " ORDER BY a.fec_creaci, 7, b.fec_creaci";

        if (sizeof($totrutas) < 2)
            $query .= ",11,15";
        $consulta = new Consulta($query, $this->conexion);
        $matriz = $consulta->ret_matriz();*/
        
        // if( $despac == '778350' )
        // {
            $query = "(
              SELECT UPPER( d.nom_sitiox ), DATE_FORMAT( a.fec_planea, '%H:%i %d-%m-%Y' ), 
                     IF( c.fec_contro IS NOT NULL, DATE_FORMAT( c.fec_contro, '%H:%i %d-%m-%Y' ), DATE_FORMAT( a.fec_alarma, '%H:%i %d-%m-%Y' ) ),
              NULL, NULL, NULL, a.fec_planea, a.cod_contro, a.fec_planea, NULL, a.fec_alarma,
              'indlink', b.ind_urbano, NULL, NULL AS fec, b.ind_virtua, f.cod_transp, a.ind_estado, DATE_FORMAT( a.fec_planea, '%H:%i' )as hora, a.cod_rutasx, NULL,
              c.fec_creaci
              FROM ".BASE_DATOS.".tab_genera_contro b, 
              ".BASE_DATOS.".tab_despac_seguim a,
              ".BASE_DATOS.".tab_despac_contro c LEFT JOIN 
              ".BASE_DATOS.".tab_despac_sitio d ON c.cod_sitiox = d.cod_sitiox,
              ".BASE_DATOS.".tab_genera_noveda e,
              ".BASE_DATOS.".tab_despac_vehige f
              WHERE a.cod_contro = b.cod_contro
              AND a.num_despac = '" . $despac . "'
              AND a.ind_estado = '0'
              AND a.num_despac = c.num_despac
              AND a.cod_contro = c.cod_contro
              AND a.cod_rutasx = c.cod_rutasx
              AND c.cod_noveda = e.cod_noveda
              AND a.num_despac = f.num_despac
              )
              UNION 
              (
              SELECT UPPER( IF( b.ind_virtua = 1, CONCAT( b.nom_contro, ' (Virtual) ' ), b.nom_contro ) ),  DATE_FORMAT( a.fec_planea, '%H:%i %d-%m-%Y' ),
              IF( c.fec_noveda IS NOT NULL, DATE_FORMAT( c.fec_noveda, '%H:%i %d-%m-%Y' ), DATE_FORMAT( a.fec_alarma, '%H:%i %d-%m-%Y' ) ),
              UPPER( e.nom_noveda ), DATE_FORMAT( c.fec_creaci, '%H:%i %d-%m-%Y' ), c.des_noveda, a.fec_planea, a.cod_contro, a.fec_planea, c.usr_creaci, a.fec_alarma,
              'indlink', b.ind_urbano, c.val_retras, c.fec_noveda AS fec, b.ind_virtua, f.cod_transp, a.ind_estado, DATE_FORMAT( a.fec_planea, '%H:%i' )as hora, a.cod_rutasx, b.cod_colorx,
              c.fec_creaci
              FROM ".BASE_DATOS.".tab_genera_contro b, 
              ".BASE_DATOS.".tab_despac_seguim a,
              ".BASE_DATOS.".tab_despac_noveda c,
              ".BASE_DATOS.".tab_genera_noveda e,
              ".BASE_DATOS.".tab_despac_vehige f
              WHERE a.cod_contro = b.cod_contro
              AND a.num_despac = '" . $despac . "'
              AND a.ind_estado = '0'
              AND a.num_despac = c.num_despac
              AND a.cod_contro = c.cod_contro
              AND a.cod_rutasx = c.cod_rutasx
              AND c.cod_noveda = e.cod_noveda
              AND a.num_despac = f.num_despac
              )
              ORDER BY 22 ASC";
            
            $consulta = new Consulta($query, $this->conexion);
            $_MATRIZ1 = $consulta->ret_matriz();
            
            $query = "/*(
              SELECT UPPER( d.nom_sitiox ),  DATE_FORMAT( a.fec_planea, '%H:%i %d-%m-%Y' ),
              IF( c.fec_contro IS NOT NULL, DATE_FORMAT( c.fec_contro, '%H:%i %d-%m-%Y' ), DATE_FORMAT( a.fec_alarma, '%H:%i %d-%m-%Y' ) ),
              UPPER( e.nom_noveda ),DATE_FORMAT( c.fec_creaci, '%H:%i %d-%m-%Y' ), c.obs_contro, a.fec_planea, a.cod_contro, a.fec_planea, c.usr_creaci, a.fec_alarma,
              'indlink', b.ind_urbano, c.val_retras, c.fec_contro AS fec, b.ind_virtua, f.cod_transp, a.ind_estado, DATE_FORMAT( a.fec_planea, '%H:%i' )as hora, a.cod_rutasx, b.cod_colorx,
              c.fec_creaci
              FROM ".BASE_DATOS.".tab_genera_contro b, 
                   ".BASE_DATOS.".tab_despac_seguim a
              LEFT JOIN ".BASE_DATOS.".tab_despac_contro c ON 
                  a.num_despac = c.num_despac
              AND a.cod_contro = c.cod_contro
              AND a.cod_rutasx = c.cod_rutasx
              LEFT JOIN ".BASE_DATOS.".tab_genera_noveda e ON
                  c.cod_noveda = e.cod_noveda
              LEFT JOIN ".BASE_DATOS.".tab_despac_vehige f ON
                  a.num_despac = f.num_despac,
                  ".BASE_DATOS.".tab_despac_sitio d 
              WHERE a.cod_contro = b.cod_contro
              AND a.num_despac = '" . $despac . "'
              AND a.ind_estado = '1'
              AND c.cod_sitiox = d.cod_sitiox
              )
              UNION 
              (*/
              SELECT UPPER( IF( b.ind_virtua = 1, CONCAT( b.nom_contro, ' (Virtual) ' ), b.nom_contro ) ),  DATE_FORMAT( a.fec_planea, '%H:%i %d-%m-%Y' ),
              IF( c.fec_noveda IS NOT NULL, DATE_FORMAT( c.fec_noveda, '%H:%i %d-%m-%Y' ), DATE_FORMAT( a.fec_alarma, '%H:%i %d-%m-%Y' ) ),
              UPPER( e.nom_noveda ), DATE_FORMAT( c.fec_creaci, '%H:%i %d-%m-%Y' ), c.des_noveda, a.fec_planea, a.cod_contro, a.fec_planea, c.usr_creaci, a.fec_alarma,
              'indlink', b.ind_urbano, c.val_retras, c.fec_noveda AS fec, b.ind_virtua, f.cod_transp, a.ind_estado, DATE_FORMAT( a.fec_planea, '%H:%i' )as hora, a.cod_rutasx, b.cod_colorx,
              c.fec_creaci
              FROM ".BASE_DATOS.".tab_genera_contro b, 
                   ".BASE_DATOS.".tab_despac_seguim a
              LEFT JOIN ".BASE_DATOS.".tab_despac_noveda c ON 
                  a.num_despac = c.num_despac
                  AND a.cod_contro = c.cod_contro
                  AND a.cod_rutasx = c.cod_rutasx
              LEFT JOIN ".BASE_DATOS.".tab_genera_noveda e ON
                 c.cod_noveda = e.cod_noveda
              LEFT JOIN ".BASE_DATOS.".tab_despac_vehige f ON
                 a.num_despac = f.num_despac
              WHERE a.cod_contro = b.cod_contro
              AND a.num_despac = '" . $despac . "'
              AND a.ind_estado = '1'
              /*)*/
              ORDER BY 7 ASC, 22 ASC";
            
            $consulta = new Consulta($query, $this->conexion);
            $_MATRIZ2 = $consulta->ret_matriz();
            
            // $_matriz3 = array();
            
            // $CODCONTRO = $_MATRIZ2[0]['cod_contro'];
            // for( $k = 0; $k < sizeof( $_MATRIZ2 ); $k++ )
            // {
              // $row = $_MATRIZ2[$k];
              // if( $CODCONTRO == $row['cod_contro'] )
              // {
                // $_matriz3[$k] = $row;
              // }
            // }
            
            // if( sizeof( $_matriz3 ) > 1 ) 
            // {
              // foreach($_matriz3 as $index => $row )
              // {
                // if( $row['fec_creaci'] == '' || $row['fec_creaci'] == NULL )
                // {
                  // unset( $_MATRIZ2[$index] );
                // }
              // }            
            // }
            
            
            // if( $despac == '778395' )
            // {
               // $aux0 = $_MATRIZ2[0];
               // $aux1 = $_MATRIZ2[1];
               // $aux2 = $_MATRIZ2[2];
               
               // $_MATRIZ2[0] = $aux1;
               // $_MATRIZ2[1] = $aux2;
               // $_MATRIZ2[3] = $aux0;
               
            // }
            
            $matriz = array_merge($_MATRIZ1, $_MATRIZ2);
            
            // if($despac == '778395')
            // {
              // echo "<pre>";
              // print_r( $matriz );
              // echo "</pre>";
            // }
        // }


        $query = "SELECT cod_tipser " .
                "FROM " . BASE_DATOS . ".tab_transp_tipser " .
                "WHERE cod_transp='" . $matriz[0]["cod_transp"] . "' AND 
                  num_consec= (SELECT MAX(num_consec) FROM " . BASE_DATOS . ".tab_transp_tipser
 							                 WHERE cod_transp='" . $matriz[0]["cod_transp"] . "')";
        $consulta = new Consulta($query, $this->conexion);
        $tipser = $consulta->ret_matriz();



        //para validar los Links de los despachos pendientes
        //trae la ultima fecha de la novedad

        $query = "SELECT b." . $camporder . "

              FROM " . BASE_DATOS . ".tab_despac_noveda a,

                   " . BASE_DATOS . ".tab_despac_seguim b,

                   " . BASE_DATOS . ".tab_despac_vehige d

             WHERE d.num_despac = '" . $_REQUEST["despac"] . "' AND

                   d.num_despac = b.num_despac AND

                   a.num_despac = b.num_despac AND

                   a.cod_contro = b.cod_contro AND

                   a.fec_noveda = (SELECT MAX(c.fec_noveda)

                                     FROM " . BASE_DATOS . ".tab_despac_noveda c

                                    WHERE c.num_despac = a.num_despac

                                  )

             ";



        $consulta = new Consulta($query, $this->conexion);

        $maximo = $consulta->ret_matriz();



        //trae la ultima fecha de la nota de controlador

        $query = "SELECT b." . $camporder . "

              FROM " . BASE_DATOS . ".tab_despac_contro a,

                   " . BASE_DATOS . ".tab_despac_seguim b,

                   " . BASE_DATOS . ".tab_despac_vehige d

             WHERE d.num_despac = '" . $_REQUEST["despac"] . "' AND

                   d.num_despac = b.num_despac AND

                   a.num_despac = b.num_despac AND

                   a.cod_contro = b.cod_contro AND

                   a.fec_contro = (SELECT MAX(c.fec_contro)

                                     FROM " . BASE_DATOS . ".tab_despac_contro c

                                    WHERE c.num_despac = a.num_despac

                                  )

          ";


          
          
          
          
          
          
          
          

        $consulta = new Consulta($query, $this->conexion);

        $maximo_c = $consulta->ret_matriz();



        if ($maximo[0][0] > $maximo_c[0][0])
            $fecplanult = $maximo[0][0];

        else
            $fecplanult = $maximo_c[0][0];



        $query = "SELECT b.cod_contro,b.nom_contro, d.cod_rutasx
              FROM " . BASE_DATOS . ".tab_genera_contro b,
                   " . BASE_DATOS . ".tab_despac_seguim d,
                   " . BASE_DATOS . ".tab_despac_vehige e
             WHERE b.cod_contro = d.cod_contro AND
                   e.num_despac = d.num_despac AND
                   e.num_despac = " . $despac . " AND
                   d.ind_estado =1
                   
             ";
        $query = $query . " ORDER BY d.fec_planea ";
        $consulta = new Consulta($query, $this->conexion);
        $matrizlink = $consulta->ret_matriz();
        
        // if( $despac == '778350' )
        // {
          // echo "<pre>";
          // print_r($matrizlink);
          // echo "</pre>";
        // }

        $query = "SELECT a.ind_fincal
  		      FROM " . BASE_DATOS . ".tab_config_parame a
  		     WHERE a.ind_fincal = '1'
  		   ";
        $consulta = new Consulta($query, $this->conexion);
        $viscalti = $consulta->ret_matriz();
        $sit = "";
        
        //Nelson
        if (sizeof($matriz) > 0)
        {
            $formulario->nueva_tabla();
            $formulario->linea("Informaci&oacute;n del Plan de Ruta", 1, "t2");

            $formulario->nueva_tabla();
            $formulario->linea("Sitio de Seguimiento", 0, "t", "10%");
            $formulario->linea("Hora/Fecha Programada", 0, "t", "10%");

            if ($viscalti && $finali)
                $formulario->linea("", 0, "t");

            $formulario->linea("Hora/Fecha Control", 0, "t", "10%");
            $formulario->linea("Tiempo", 0, "t", "10%");
            $formulario->linea("Novedad", 0, "t", "40%");
            $formulario->linea("Hora/Fecha Sistema", 0, "t", "10%");
            $formulario->linea("Usuario", 1, "t", "10%");
            $salida = $observ[0][4];
            $tiemdif = 0;
            $ini = 1;
            for ($i = 0; $i < sizeof($matriz); $i++)
            {
                $asignado_variurb = 0;
                $nomost = 0;
                $x = 0;

                for ($j = $i; $j < sizeof($matriz); $j++)
                {
                    if ($matriz[$i][7] == $matriz[$j][7])
                    {
                        $matriz[$j][11] = 1;

                        for ($k = $j - 1; $k > 0; $k--)
                            if ($matriz[$j][7] == $matriz[$k][7])
                                $matriz[$k][11] = 0;
                    }
                }
            }
            $salida = $observ[0][4];
            $tiemdif = 0;
            $ini = 1;
            for ($i = 0; $i < sizeof($matriz); $i++)
            {
                $asignado_variurb = 0;
                $nomost = 0;
                $x = 0;
                $aux = "href";
                if ($datos_usuario["cod_perfil"] == "")
                {
                    $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
                    if ($filtro->listar($this->conexion))
                    {
                        $datos_filtro = $filtro->retornar();
                        $aux = "name";
                    }
                }
                else
                {
                    $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
                    if ($filtro->listar($this->conexion))
                    {
                        $datos_filtro = $filtro->retornar();
                        $aux = "name";
                    }
                }
                if (strtolower($datos_usuario["cod_usuari"]) == 'alogistica' || ($matriz[$i]['ind_virtua'] == 1 && $tipser[0][0] == 1 && $datos_usuario["cod_perfil"] == '7'))
                {
                    $aux = "name";
                }
                
                 if($matriz[$i][20] != NULL)
                  $RowColor = $matriz[$i][20];
                else
                  $RowColor = "";
                  
                for ($j = 0; $j < sizeof($matrizlink); $j++)
                {
                    if (!$opcurban)
                    {
                        if ($matriz[$i][7] == $matrizlink[$j][0] && $matriz[$i]['cod_rutasx'] == $matrizlink[$j][2] && $link && $matriz[$i][11] && $x == 0)
                        {
                            $x = 1;
                            //Jorge 120404
                            $matriz[$i][0] = "<a $aux=\"index.php?&cod_servic=" . $this->servic . "&window=central&tie_ultnov=" . $tie_ultnov . "
														&despac=" . $despac . "&opcion=" . $this->opcion . "&codpc=" . $matrizlink[$j][0] . "&pc=" . $matriz[$i][0] . "&ind_virtua=" . $matriz[$i]["ind_virtua"] . 
                            "&cod_transp=" . $matriz[$i]["cod_transp"] . " &opcurban=" . $opcurban . " \"target=\"centralFrame\" style=\"color:#".$RowColor."\">" . $matriz[$i][0] . "</a>";
                        }
                    }
                    else if (!$asignado_variurb)
                    {//Jorge 120404
                        $matriz[$i][0] = "<a $aux=\"index.php?&cod_servic=1325&window=central&tie_ultnov=" . $tie_ultnov . "
							&despac=" . $despac . "&opcion=2&codpc=" . $matriz[$i][7] . "
							&pc=" . $matriz[$i][0] . " &ind_virtua=" . $matriz[$i]["ind_virtua"] . " &cod_transp=" . $matriz[$i]["cod_transp"] . " &opcurban=" . $opcurban . " \"target=\"centralFrame\"  style=\"color:#".$RowColor."\">" . $matriz[$i][0] . "</a>";
                        $asignado_variurb = 1;
                    }



                    if ($matriz[$i][7] != $matrizlink[$j][0])
                        if ($matriz[$i][3] == NULL)
                        {
                            $matriz[$i][2] = "";
                            $nomost = 1;
                        }

                    if ($matriz[$i][4] == NULL)
                    {
                        $matriz[$i][4] = "00:00:00 00-00-0000";

                        if ($matriz[$i][5] == NULL)
                            $matriz[$i][5] = "No Reportado";
                    }
                }

                if ($viscalti && $finali)
                {
                    $query = "SELECT (TIME_TO_SEC(TIMEDIFF('" . $matriz[$i][1] . "','" . $matriz[$i][2] . "'))/60)
   		    ";

                    $consulta = new Consulta($query, $this->conexion);
                    $calminti = $consulta->ret_matriz();

                    $anter = $poste = "";

                    if ($calminti[0][0] < 0)
                    {
                        $calminti[0][0] *= -1;
                        $estilo = "e";
                        $anter = "<< ";
                    }
                    else
                    {
                        $estilo = "a";
                        $poste = " >>";
                    }

                    if ($nomost)
                        $difftempo = "";
                    else
                        $difftempo = $anter . $this->getCantTiempo($calminti[0][0]) . $poste;
                }

                if($matriz[$i][12] == "1")
                  $celda_estilo = "iu";
                else
                  $celda_estilo = "i";
                
                
               

                $query = "SELECT a.cod_colorx,a.cant_tiempo
  	 		     FROM " . BASE_DATOS . ".tab_genera_alarma a
  	 		    	  ORDER BY 2
  	 		  ";

                $consulta = new Consulta($query, $this->conexion);
                $timecolo = $consulta->ret_matriz();

                $alarma_color = NULL;

                if ($matriz[$i][13])
                {
                    if ($matriz[$i][13] > 0)
                    {
                        for ($j = 0; $j < sizeof($timecolo); $j++)
                        {
                            if ($matriz[$i][13] <= $timecolo[$j][1])
                            {
                                $alarma_color = $timecolo[$j][0];
                                $j = sizeof($timecolo);
                            }
                        }

                        if (!$alarma_color)
                            $alarma_color = $timecolo[sizeof($timecolo) - 1][0];
                    }
                }

                if (!$alarma_color)
                    $alarma_color = "FFFFFF";
                    
                if($matriz[$i][20] != NULL)
                  $RowColor = $matriz[$i][20];
                else
                  $RowColor2 = "D8DFEA";
                  
                if ($sit != $matriz[$i][7])
                {
                    //notas de control con indicativo de antes
                    $query = "SELECT IF( UPPER( IF( a.cod_noveda = ".CONS_NOVEDA_GPSXXX.", REPLACE(SUBSTRING(a.obs_contro, 11, INSTR(a.obs_contro, 'Velocidad')-11 ),',,',','), c.nom_sitiox ) ) IS NULL, d.nom_contro, UPPER( IF( a.cod_noveda = ".CONS_NOVEDA_GPSXXX.", REPLACE(SUBSTRING(a.obs_contro, 11, INSTR(a.obs_contro, 'Velocidad')-11 ),',,',','), c.nom_sitiox ) )) AS nom_sitiox,
                    DATE_FORMAT(a.fec_contro,'%H:%i %d-%m-%Y'),b.nom_noveda,
                    DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),a.usr_creaci,a.val_retras,a.fec_creaci as fec
              FROM " . BASE_DATOS . ".tab_despac_contro a,
                   " . BASE_DATOS . ".tab_genera_noveda b,
                   " . BASE_DATOS . ".tab_despac_sitio c,
                   " . BASE_DATOS . ".tab_genera_contro d
              WHERE a.cod_noveda=b.cod_noveda AND 
                    a.cod_sitiox=c.cod_sitiox AND 
                    a.cod_contro = d.cod_contro AND
                    a.num_despac ='$despac' AND 
                    a.cod_contro ='" . $matriz[$i][7] . "' AND
                    a.cod_rutasx = '" . $matriz[$i]['cod_rutasx'] . "'
              ORDER BY a.fec_creaci";
                    $consulta = new Consulta($query, $this->conexion);
                    $sitios = $consulta->ret_matriz();
                    //echo "<pre>"; print_r($sitios); echo "</pre>";
                    $sit = $matriz[$i][7];
                    //Nelson
                    for ($z = 0; $z < sizeof($sitios); $z++)
                    {
                        if ($ini == 1)
                        {
                            $query = "SELECT TIMEDIFF(  '" . $sitios[$z]["fec"] . "' ,'$salida' ) ";
                            $ini = 0;
                        }
                        else
                        {
                            $query = "SELECT TIMEDIFF( '" . $sitios[$z]["fec"] . "','$salida'  ) ";
                        }
                        $consulta = new Consulta($query, $this->conexion);
                        $tiemdif = $consulta->ret_matriz();
                        $tiemdif = explode(":", $tiemdif[0][0]);

                        if ($sitios[$z]["fec"] != "")
                            $salida = $sitios[$z]["fec"];
                            
                      
                        $tiemdif = $tiemdif[0] * 60 + $tiemdif[1];
                        $formulario->linea($sitios[$z][0], 0, $celda_estilo, "10%\" style=\"background:#D8DFEA;",null,null,0);//$RowColor2
                        $formulario->linea("", 0, $celda_estilo, "10%\" style=\"background:#D8DFEA;",null,null,0);
                        if ($viscalti && $finali)
                            $formulario->linea("", 0, $celda_estilo, "10%\" style=\"background:#D8DFEA;",null,null,0);
                        $formulario->linea($sitios[$z][1], 0, $celda_estilo, "10%\" style=\"background:#D8DFEA;", 0,null,0);
                        $formulario->linea(number_format($tiemdif) . "Min(s)", 0, $celda_estilo, "10%\" style=\"background:#D8DFEA;",null,null,0);
                        $formulario->linea($sitios[$z][2], 0, $celda_estilo, "40%\" style=\"background:#D8DFEA;",null,null,0);
                        $formulario->linea($sitios[$z][3], 0, $celda_estilo, "10%\" style=\"background:#D8DFEA;",null,null,0);
                        $formulario->linea($sitios[$z][4], 1, $celda_estilo, "10%\" style=\"background:#D8DFEA;",null,null,0);
                    }
                }
                if ($matriz[$i][3] != "NULL")
                {
                    if ($ini == 1)
                    {
                        $query = "SELECT TIMEDIFF(  '" . $matriz[$i]["fec"] . "','$salida' ) ";
                        $ini = 0;
                    }
                    else
                    {
                        $query = "SELECT TIMEDIFF( '" . $matriz[$i]["fec"] . "','$salida' ) ";
                    }
                    $consulta = new Consulta($query, $this->conexion);
                    $tiemdif = $consulta->ret_matriz();
                    $tiemdif = explode(":", $tiemdif[0][0]);
                    if ($matriz[$i]["fec"] != "")
                        $salida = $matriz[$i]["fec"];
                    $tiemdif = $tiemdif[0] * 60 + $tiemdif[1];
                    $tiemdif = number_format($tiemdif) . "Min(s)";
                }
                else
                {
                    $tiemdif = "";
                }
                //echo "Tiempos -> ".$tiemdif."<br>";
                //Color Nelson
                if($matriz[$i][3] == NULL )
                {
                  if($matriz[$i][20] == NULL)
                     $RowColor = 'FFFFFF';
                  else
                     $RowColor = $matriz[$i][20];
                }
                else 
                {
                  $estilo = "10%\" style=\"background:#D8DFEA;";
                  //$RowColor = $RowColor == 'i' ? 'D8DFEA' : $RowColor;
                }
                    
                   
                
                if ($matriz[$i][3] == NULL)
                {
                    $matriz[$i][4] = "";
                    $matriz[$i][9] = "";
                    $tiemdif = "";
                    $estilo = "10%";
                }
                if (($matriz[$i][3] != NULL || strlen($matriz[$i][0]) > 80) && (strlen($matriz[$i][0]) > 80 && $matriz[$i]["ind_estado"] != 0 || $matriz[$i][3] != NULL))
                {
                    //if(strlen($matriz[$i][0])>80  && $matriz[$i][ind_estado]!=0 || $matriz[$i][3]!=NULL){
                    $formulario->linea("<span style='color:#".$RowColor."'>".$matriz[$i][0]."</span>", 0, $celda_estilo, $estilo,null,null,0);//$RowColor
                    $formulario->linea($matriz[$i][1], 0, $celda_estilo, $estilo,null,null,0);
                    if ($viscalti && $finali)
                        $formulario->linea($difftempo, 0, $estilo, "10%", 0, $estilo,null,null,0);
                    $formulario->linea($matriz[$i][2], 0, $celda_estilo, $estilo,null,null,0);
                    $formulario->linea($tiemdif, 0, $celda_estilo, $estilo, 0,null,0);
                    $formulario->linea($matriz[$i][3], 0, $celda_estilo, $estilo,null,null,0);
                    $formulario->linea($matriz[$i][4], 0, $celda_estilo, $estilo,null,null,0);
                    $formulario->linea($matriz[$i][9], 1, $celda_estilo, $estilo,null,null,0);
                    //}
                }
            }//fin for
            
            // if( $despac == '778395' )
            // {
              // echo "<pre>"; print_r($matriz); echo "</pre>"; 
            // }
                          
        }//fin if
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/PDF.js\"></script>\n";
        $formulario->oculto("central\" id=\"central", DIR_APLICA_CENTRAL, 0);
        $formulario->oculto("bd_aplica\" id=\"bd_aplica", BASE_DATOS, 0);
        $formulario->oculto("despac\" id=\"despac", $despac, 0);
        

        if (!$finali && $pernotco)
            $link_notcon = " ==> <a href=\"index.php?cod_servic=1330&window=central&despac=" . $despac . "&opcion=1 \"target=\"centralFrame\">Nueva Nota de Controlador</a>";

        if ($lleg == 1)
        {
            $query = "SELECT 1
             FROM " . BASE_DATOS . ".tab_perfil_servic 
             WHERE cod_perfil = '" . $datos_usuario["cod_perfil"] . "'
                   AND cod_servic ='1335'
             ";
            $consulta = new Consulta($query, $this->conexion);
            $permi = $consulta->ret_matriz();
            if ($permi)
            {
                $formulario->nueva_tabla();
                $formulario->linea("Llegada:", 1, "t2");
                $formulario->nueva_tabla();
                echo '<td align="left" width="100px" class="celda_titulo2">
                 <b>Observacion de LLegada :</b></td>
               <td width="15%" align="left" class="celda_info">
                 <textarea cols="50" rows="2" id="obs_llegadID" name="obs_llegad" ></textarea>
               </td>';
                $formulario->botoni("Llegada", "guarllegad()", 1);
            }
        }


        $formulario->nueva_tabla();
        $formulario->linea("Informaci&oacute;n de Notas de Controlador", 1, "t2");

        if (sizeof($seguim) > 0)
        {
            $formulario->nueva_tabla();
            $formulario->linea("Sitio de Seguimiento", 0, "t");
            $formulario->linea("Novedad", 0, "t");
            $formulario->linea("Observaci&oacute;n", 0, "t");
            $formulario->linea("Fecha", 0, "t");
            $formulario->linea("Usuario", 1, "t");

            for ($i = 0; $i < sizeof($seguim); $i++)
            {
                if (!$seguim[$i][2])
                    $seguim[$i][2] = "-";

                $alarma_color = NULL;

                if ($seguim[$i][6])
                {
                    if ($seguim[$i][6] > 0)
                    {
                        for ($j = 0; $j < sizeof($timecolo); $j++)
                        {
                            if ($seguim[$i][6] <= $timecolo[$j][1])
                            {
                                $alarma_color = $timecolo[$j][0];
                                $j = sizeof($timecolo);
                            }
                        }

                        if (!$alarma_color)
                            $alarma_color = $timecolo[sizeof($timecolo) - 1][0];
                    }
                }

                if (!$alarma_color)
                    $alarma_color = "FFFFFF";
                if (!$seguim[$i]["nom_sitiox"])
                {
                    $formulario->linea($seguim[$i][0], 0, "i", "16%");
                }
                else
                {
                    $formulario->linea($seguim[$i]["nom_sitiox"], 0, "i", "16%");
                }
                $formulario->linea($seguim[$i][1], 0, "i", "10%");
                //echo "<td style ='font-size: 8pt;color: #000000;background-color: ".$alarma_color."'>".number_format($seguim[$i][6])." Min(s)</td>";
                //$formulario -> linea($seguim[$i][2],0,"i","16%");
                echo "<td align='left' class='celda_info'><b>";
                $limit = 50;
                $x = 0;
                for ($s = 0; $s < strlen($seguim[$i][3]); $s++)
                {
                    echo $seguim[$i][3][$s];
                    $x++;
                    if ($x == $limit)
                    {
                        echo "<br>";
                        $x = 0;
                    }
                }
                echo "</td>";
                $formulario->linea($seguim[$i][4], 0, "i", "16%");
                $formulario->linea($seguim[$i][5], 1, "i", "16%");
            }//fin for
        }//fin if
        /*--------------------------------------------OPENSTREETMAPS-----------------------------------------------------------------*/
  $query = "SELECT b.nom_contro,c.nom_noveda,a.tiem_duraci,a.obs_contro as des,
		         DATE_FORMAT(a.fec_contro,'%H:%i %d-%m-%Y') as fec,a.usr_creaci,a.val_retras,d.nom_sitiox as nom_sitiox,
             a.fec_contro, a.val_latitu, a.val_longit, a.cod_noveda
            FROM  ".BASE_DATOS.".tab_genera_contro b,
		              ".BASE_DATOS.".tab_genera_noveda c, 
                  ".BASE_DATOS.".tab_despac_contro a LEFT JOIN ".BASE_DATOS.".tab_despac_sitio d ON a.cod_sitiox = d.cod_sitiox  
             WHERE a.cod_contro = b.cod_contro AND
		               a.cod_noveda = c.cod_noveda AND
                   a.num_despac = ".$despac." AND 
                   a.obs_contro != '' 
          GROUP BY 4
          ORDER BY 9 DESC";
    
    $id_gpsxxx = 0;
   $consulta = new Consulta($query, $this -> conexion);
    $seguim = $consulta -> ret_matriz();
    
    for ($i = 0; $i <= count($seguim); $i++)
            {
              if( $seguim[$i]['cod_noveda'] == CONS_NOVEDA_GPSXXX ){
                 $ind_gpsxxx = $seguim[$i]['cod_noveda'];
               }
            }
            
          if($ind_gpsxxx == CONS_NOVEDA_GPSXXX){
            if($_REQUEST[mos_mapa]== 1 && $_REQUEST[detalles]== 1){    
              echo '<table align="center" cellspacing="0" cellpadding="0" width="100%" style="background-color:#FFF" >';
              echo '<tr>';
              echo '<td align="center" width="100%">';
              echo '<div id="map" style="text-align:center; width:800px; height:500px ;margin-left:auto; margin-right:auto;"></div>';
              echo '</td>';
              echo '</tr>';
              echo '</table>';
              echo '<table align="center" cellspacing="0" cellpadding="0" width="100%" style="background-color:#FFF" >';
              echo '<tr>';
              echo '<td align="center" width="100%">';
              echo '<table align="center">';
              $formulario->boton("Sin Detalle", "button\" onClick=\"detalles.value='0';  form.submit()", 0);
              $formulario->boton("Ocultar", "button\" onClick=\"mos_mapa.value='0'; form.submit()", 0);
              echo '<td>';
              echo '<tr>'; 
            }elseif($_REQUEST[mos_mapa]== 1 && $_REQUEST[detalles]== 0){
              echo '<table align="center" cellspacing="0" cellpadding="0" width="100%" style="background-color:#FFF" >';
              echo '<tr>';
              echo '<td align="center" width="100%">';
              echo '<div id="map" style="text-align:center; width:800px; height:500px ;margin-left:auto; margin-right:auto;"></div>';
              echo '</td>';
              echo '</tr>';
              echo '</table>';
              echo '<table align="center" cellspacing="0" cellpadding="0" width="100%" style="background-color:#FFF" >';
              echo '<tr>';
              echo '<td align="center" width="100%">';
              echo '<table align="center">';
              $formulario->boton("Detalle", "button\" onClick=\"detalles.value='1';  form.submit()", 0);
              $formulario->boton("Ocultar", "button\" onClick=\"mos_mapa.value='0'; form.submit()", 0);
              echo '<td>';
              echo '<tr>';
            }else{
              echo '<table align="center" cellspacing="0" cellpadding="0" width="100%" style="background-color:#FFF" >';
              echo '<tr>';
              echo '<td align="center" width="100%">';
              echo '<table align="center">';
              echo '<td>';
              echo '<tr>';  
              $formulario->boton("Mapa", "button\" onClick=\"mos_mapa.value='1'; form.submit()", 0);
              echo '</td>';
              echo '</tr>';
              echo '</table>'; 
            }
          }
          $formulario->oculto("detalles\" id=\"detalles",$_REQUEST[detalles],0);
          $formulario->oculto("mos_mapa\" id=\"mos_mapa",$_REQUEST[mos_mapa],0);
          $num_notgps = 0;
          for($i=0;$i<count($seguim);$i++)
          {
            if( $seguim[$i]['cod_noveda'] == CONS_NOVEDA_GPSXXX )
            {
              $formulario -> oculto( "val_latitu".$num_notgps."\" id=\"val_latitu".$num_notgps."ID", $seguim[$i]['val_latitu'], 0 );
              $formulario -> oculto( "val_longit".$num_notgps."\" id=\"val_longit".$num_notgps."ID", $seguim[$i]['val_longit'], 0 );
              $formulario -> oculto( "obs_contro".$num_notgps."\" id=\"obs_contro".$num_notgps."ID", $seguim[$i]['des'], 0 );
              $num_notgps++;
            }
          }
          $formulario -> oculto( "size_notgps\" id=\"size_notgpsID", $num_notgps, 0 );
        
        /*------------------------------------------------------------------------------------------------------------------------------*/
        $formulario->nueva_tabla();



        $formulario->linea("Observaciones Generales", 0, "t2");

        $formulario->linea("Medios de Comunicacion", 0, "t2");

        $formulario->linea("Protecciones Especiales", 1, "t2");



        if (!$observ[0][0])
            $observ[0][0] = "-";

        if (!$observ[0][1])
            $observ[0][1] = "-";

        if (!$observ[0][2])
            $observ[0][2] = "-";



        $formulario->linea($observ[0][0], 0, "i");

        $formulario->linea($observ[0][1], 0, "i");

        $formulario->linea($observ[0][2], 1, "i");
    }

    //$regist representa los valores del reporte
    //$ominter representa desde donde se envia el reporte 0-> Aplicac, 1-> WAP, 2-> Interf SAT, 3-> interf GPS
    function InsertarNovedadPC($base_datos, $regist, $ominter)
    {
        $fTiempEnvia = $regist["tieadi"];

        $query = "SELECT a.cod_noveda, a.num_despac, b.nom_contro, c.nom_noveda
            FROM " . $base_datos . ".tab_despac_noveda a,
                 " . $base_datos . ".tab_genera_contro b,
                 " . $base_datos . ".tab_genera_noveda c,
                 " . $base_datos . ".tab_despac_vehige d,
                 " . $base_datos . ".tab_despac_seguim e
            WHERE e.num_despac = d.num_despac AND
                  e.cod_contro = " . $regist["contro"] . " AND
                  a.num_despac = e.num_despac AND
                  a.cod_contro = e.cod_contro AND
                  a.cod_noveda = " . $regist["noveda"] . " AND
                  b.cod_contro = a.cod_contro AND
                  c.cod_noveda = a.cod_noveda AND
                  d.num_despac = " . $regist["despac"] . " AND 
                  a.fec_noveda = '" . $regist["fecnov"] . "'
           ";

        $consulta = new Consulta($query, $this->conexion);
        $existe = $consulta->ret_matriz();

        $query = "SELECT a.fec_salida
  		      FROM " . BASE_DATOS . ".tab_despac_despac a
  		     WHERE a.num_despac = " . $regist["despac"] . "
  		   ";

        $consulta = new Consulta($query, $this->conexion);
        $fecsalid = $consulta->ret_matriz();

        if ($regist["fecnov"] <= $fecsalid[0][0])
        {
            $RESPON[0]["indica"] = 0;
            $RESPON[0]["mensaj"] = "La Fecha de la Novedad Debe ser Mayor a la Fecha de Salida Asignada al Despacho.";
        }
        else if ($regist["fecnov"] <= $regist["ultrep"])
        {
            $RESPON[0]["indica"] = 0;
            $RESPON[0]["mensaj"] = "La Fecha de la Novedad Debe ser Mayor a la Fecha de la Ultima Novedad.";
        }
        /* else if($regist["fecnov"] > $regist["fecact"])
          {
          $RESPON[0]["indica"] = 0;
          $RESPON[0]["mensaj"] = "La Fecha de la Novedad Debe ser Menor o Igual a la Fecha Actual.";
          } */
        else if (sizeof($existe) > 0)
        {
            $RESPON[0]["indica"] = 0;
            $RESPON[0]["mensaj"] = "La Novedad ya se Encuentra Registrada en el Puesto de Control Seleccionado con la misma fecha.";
        }
        else
        {
            if (!$regist["tieadi"])
                $regist["tieadi"] = 0;

            $query = "SELECT a.cod_rutasx,a.cod_transp
                 FROM " . $base_datos . ".tab_despac_vehige a
                WHERE a.num_despac = '" . $regist["despac"] . "'
              ";

            $consulta = new Consulta($query, $this->conexion);
            $rutact = $consulta->ret_matriz();
            $rutact[0][0] = $regist["rutax"];
            $query = "SELECT (TIME_TO_SEC(TIMEDIFF('" . $regist["fecact"] . "', a.fec_alarma))/60)
     		     FROM " . $base_datos . ".tab_despac_seguim a
     		    WHERE a.num_despac = " . $regist["despac"] . " AND
     		          a.cod_rutasx = " . $rutact[0][0] . " AND
     		          a.cod_contro = " . $regist["contro"] . "
     		  ";

            $consulta = new Consulta($query, $this->conexion);
            $tiempretras = $consulta->ret_matriz();

            if ($tiempretras[0][0] > 0)
                $tiempretras[0][0] = floor($tiempretras[0][0]);
            else
                $tiempretras[0][0] = 0;

            $regist["nittra"] = $rutact[0][1];
            //captura el cel de la observacion para actualizar el despacho y el tercero
            if ((int) $regist["celular"] != '')
                $cel = (int) $regist["celular"];
            if ($cel)
            {
                $query = "SELECT con_telmov " .
                        "FROM " . $base_datos . ".tab_despac_despac 
                   WHERE num_despac = '" . $regist["despac"] . "'";
                $consulta = new Consulta($query, $this->conexion);
                $num = $consulta->ret_matriz();
                $query = "UPDATE " . $base_datos . ".tab_despac_despac 
                   SET con_telmov = '$cel' 
                   WHERE num_despac = '" . $regist["despac"] . "'";
                $update = new Consulta($query, $this->conexion, "R");
                $regist["observ"] = $regist["observ"] . ".Celular Nuevo: $cel, Celular Anterior " . $num[0][0];
            }
            //valida si el nombre del sitio existe
            $query = "SELECT cod_sitiox " .
                    "FROM " . $base_datos . ".tab_despac_sitio " .
                    "WHERE nom_sitiox = '" . $regist["sitio"] . "'";
            $consulta = new Consulta($query, $this->conexion);
            $sitio = $consulta->ret_matriz();
            if (!$sitio)
            {
                $query = "SELECT MAX(cod_sitiox) " .
                        "FROM " . $base_datos . ".tab_despac_sitio ";
                $consulta = new Consulta($query, $this->conexion);
                $sitio = $consulta->ret_matriz();
                $maxsit = $sitio[0][0] + 1;
                $query = "INSERT INTO " . $base_datos . ".tab_despac_sitio" .
                        "(cod_sitiox,nom_sitiox)VALUES($maxsit,'" . $regist["sitio"] . "') ";
                $insercion = new Consulta($query, $this->conexion, "R");
            }
            else
            {
                $maxsit = $sitio[0][0];
            }
            //Se hacen ajustes para las alarmas cuando la novedad mantiene alarma
            $query = "SELECT fec_manala,fec_ultnov, fec_salida,ind_defini " .
                    "FROM " . $base_datos . ".tab_despac_despac
               WHERE num_despac = '" . $regist["despac"] . "' ";
            $consulta = new Consulta($query, $this->conexion);
            $fec_manala = $consulta->ret_matriz();
            $ind_defini = $fec_manala[0][3];
            $fec_ultnov = "'" . $fec_manala[0][1] . "'";
            $fec_salida = "'" . $fec_manala[0][2] . "'";
            $fec_manala = "'" . $fec_manala[0][0] . "'";
            $query = "SELECT ind_manala, ind_alarma, nom_noveda, nov_especi, ind_notsup " .
                    "FROM " . $base_datos . ".tab_genera_noveda
               WHERE cod_noveda = '" . $regist["noveda"] . "' ";
            $consulta = new Consulta($query, $this->conexion);
            $ind_manala = $consulta->ret_matriz();
            $alarma = $ind_manala[0][1];
            $especi = $ind_manala[0][3];
            $nove = $ind_manala[0][2];
            //$ind_agenci = $ind_manala[0][5]; // Nelson 2013-02-05
            $ind_notsup = $ind_manala[0][4]; //Jorge 2012-11-08
            $ind_manala = $ind_manala[0][0];
            
 
            $query = "(SELECT tiem_duraci,a.fec_contro
            FROM  " . BASE_DATOS . ".tab_despac_contro a
             WHERE a.num_despac = " . $regist["despac"] . " )
             UNION
            (SELECT tiem_duraci,a.fec_noveda
            FROM  " . BASE_DATOS . ".tab_despac_noveda a 
             WHERE a.num_despac = " . $regist["despac"] . " )
            ORDER BY 2 DESC
            LIMIT 1
          ";
            $consulta = new Consulta($query, $this->conexion);
            $valretras = $consulta->ret_matriz();
            $valretras = $valretras[0][0];

            if ($ind_manala == '0')
            {
                $fec_manala = "NULL";
            }
            else
            {
                if ($fec_manala == "''")
                {
                    if ($fec_ultnov == '' || $fec_ultnov == "''")
                    {
                        $fec_manala = $fec_salida;
                    }
                    else
                    {
                        $fec_manala = $fec_ultnov;
                    }
                }
                if ($valretras == '')
                    $valretras = 0;
                $regist["tieadi"] = $valretras;
            }
            if ($regist["noveda"] == CONS_NOVEDA_ACAFAR)
                $regist["observ"] .= ".Vuelve el Vehiculo a Monitoreo Faro. ";
            
            
            if ($alarma == 'S' || $especi == 1 || $ind_notsup == 1 )//|| $ind_agenci == '1'
            {
                $query = "SELECT a.cod_tercer,a.abr_tercer ,b.num_placax, c.con_telmov, d.abr_tercer AS nom_conduc, c.cod_manifi,
                                (SELECT UPPER(nom_ciudad) FROM ".$base_datos.".tab_genera_ciudad WHERE cod_ciudad = c.cod_ciuori) AS nom_ciuori,
                                (SELECT UPPER(nom_ciudad) FROM ".$base_datos.".tab_genera_ciudad WHERE cod_ciudad = c.cod_ciudes) AS nom_ciudes                               
                  FROM " . $base_datos . ".tab_tercer_tercer a,
  										 " . $base_datos . ".tab_despac_vehige b,
  										 " . $base_datos . ".tab_despac_despac c,
  										 " . $base_datos . ".tab_tercer_tercer d
                	WHERE b.num_despac = '" . $regist["despac"] . "' 
                    AND b.num_despac = c.num_despac
                    AND b.cod_conduc = d.cod_tercer
  									AND a.cod_tercer = b.cod_transp";
                $empre = new Consulta($query, $this->conexion);
                $empre = $empre->ret_matriz();
                $query = "SELECT CONCAT(c.dir_dispos,'@',b.dns_operad) AS email
              		FROM " . BD_STANDA . ".tab_mensaj_operad b,                                                                                    
              			   " . BD_STANDA . ".tab_mensaj_dispos c
              		WHERE c.cod_operad = b.cod_operad
              		      AND c.cod_transp = b.cod_transp
              	        AND c.cod_operad = b.cod_operad
              	        AND c.ind_novala = 1
              	        AND c.ind_estado = 1
              	        AND b.cod_transp = '" . $empre[0][0] . "'";
                $consulta = new Consulta($query, $this->conexion);
                $correos = $consulta->ret_matriz();
                $dir = '';
                foreach ($correos AS $correo)
                {
                    $dir .= $correo[0] . ",";
                }
                $NotifiAgenci = "SELECT ind_notage
                               FROM tab_transp_tipser
                              WHERE cod_transp = '".$empre[0][0]."' AND
                                    num_consec = (SELECT MAX(num_consec) FROM tab_transp_tipser WHERE cod_transp = '".$empre[0][0]."')";
                $consulta = new Consulta($NotifiAgenci, $this->conexion);
                $ind_notage = $consulta->ret_matriz();
                //CORREO NOTIFICA SUPERVISOR. Jorge 2012-11-08
                if ($ind_notsup == 1)
                {
                    
                    $info .="EMPRESA: " . $empre[0][1] . " \n";
                    $info .="DESPACHO: " . $regist["despac"] . " \n";
                    $info .="MANIFIESTO: " . $empre[0]['cod_manifi'] . " \n";
                    $info .="PLACA DEL VEHICULO: " . $empre[0][2] . " \n";
                    $info .="CONDUCTOR: " . $empre[0]['nom_conduc'] . " \n";
                    $info .="TELEFONO CONDUCTOR: " . $empre[0]['con_telmov'] . " \n";
                    $info .="SITIO DE SEGUIMIENTO: " . $regist["sitio"] . " \n";
                    $info .="FECHA DE LA NOVEDAD: " . $regist["fecnov"] . " \n";
                    $info .="NOVEDAD: " . $nove . " \n";
                    $info .="USUARIO: " . $regist["usuari"] . " \n";
                    $info .="OBSERVACION:  \n";
                    $info .=" " . $regist["observ"] . " \n";

                    $asunto = "NOTIFICACION SUPERVISOR";

                    mail(MAIL_SUPERVISORES, $asunto, $info, 'From: supervisores@eltransporte.org');
                    //mail('jorge.preciado@intrared.net,leidy.acosta@intrared.net', $asunto, $info, 'From: supervisores@eltransporte.org');
                }
                
                // Notificaciones especiales
                if ($especi == 1)
                {
                    $NIT_TRANSPORTADORA = $empre[0][0];
                    $info .="EMPRESA: " . $empre[0][1] . " \n";
                    $info .="DESPACHO: " . $regist["despac"] . " \n";
                    $info .="No MANIFIESTO: " . $empre[0]['cod_manifi'] . " \n";
                    $info .="ORIGEN: " . $empre[0]['nom_ciuori'] . " \n";
                    $info .="DESTINO: " . $empre[0]['nom_ciudes'] . " \n";
                    $info .="PLACA DEL VEHICULO: " . $empre[0][2] . " \n";
                    $info .="CONDUCTOR: " . $empre[0]['nom_conduc'] . " \n";
                    $info .="TELEFONO CONDUCTOR: " . $empre[0]['con_telmov'] . " \n";
                    $info .="SITIO DE SEGUIMIENTO: " . $regist["sitio"] . " \n";
                    $info .="FECHA DE LA NOVEDAD: " . $regist["fecnov"] . " \n";
                    $info .="Novedad: " . $nove . " \n";
                    if( $regist["noveda"] == '70' )
                    {
                        $tieultima = "Tiempo Ultima Novedad: " . $regist["tie_ultnov"] . " Minutos \n";//Jorge 120404
                        $info .= $tieultima;
                    }
                    $info .="USUARIO: " . $regist["usuari"] . " \n";
                    $info .="OBSERVACION:  \n";
                    $info .=" " . $regist["observ"] . " \n";
                    $asunto = "NOVEDAD ESPECIAL";
                    $mEmailAgenci = $this -> getEmailAgecni ($regist["despac"]);
                        
                    if($ind_notage[0][0] == 1)
                    {
                      $mEmailAgenci = $this -> getEmailAgecni ($regist["despac"]);
                      if($mEmailAgenci)
                          $dir = $mEmailAgenci;                  
                    }   
  
                    if ( $NIT_TRANSPORTADORA != "900419270" ) 
                      mail("$dir", $asunto, $info, 'From: supervisores@eltransporte.org ');
						          
            
                    $no_correos = array('9996','9995','111',
                                      '68','50','53','70',
                                      '74','126','52','127',
                                      '96','97','1','28','14',
                                      '27','60','13','62','64',
                                      '49','12','41','9','4',
                                      '124','22','117','99');
             
                    if ( $NIT_TRANSPORTADORA == "900419270" && !in_array( $regist["noveda"], $no_correos) )//SI ES "K Logistics" OJO REEMPLAZAR EN EL IF -> 900419270.
                    {
                        // subject
                        $titulo = 'NOVEDAD EN RUTA';
						
						$cod_ruta = $rutact[0][0];
						
						$select = "SELECT CONCAT(b.nom_ciudad,' - ', c.nom_ciudad), a.nom_rutasx, a.cod_ciuori, a.cod_ciudes  
							   FROM " . $base_datos . ".tab_genera_rutasx a,
							        " . $base_datos . ".tab_genera_ciudad b,
							        " . $base_datos . ".tab_genera_ciudad c
							   WHERE a.cod_rutasx = '$cod_ruta' 
                                 AND a.cod_paiori = b.cod_paisxx 
                                 AND a.cod_depori = b.cod_depart 
                                 AND a.cod_ciuori = b.cod_ciudad
                                 AND a.cod_paides = c.cod_paisxx 
                                 AND a.cod_depdes = c.cod_depart 
                                 AND a.cod_ciudes = c.cod_ciudad                                 
                               ";
						
						$consulta = new Consulta( $select, $this->conexion);
						$rutaXXX = $consulta -> ret_matriz();
						$rutaXXX = $rutaXXX[0][0];
						
						$rutaXXX = str_replace( "Col", "" , $rutaXXX );
						
						
						$select = "SELECT b.nom_carroc, b.cod_carroc 
								   FROM ".$base_datos.".tab_vehicu_vehicu a,
										".$base_datos.".tab_vehige_carroc b
								   WHERE a.num_placax = '".$empre[0][2]."' AND
										 b.cod_carroc = a.cod_carroc ";
						
						$consulta = new Consulta( $select, $this->conexion);
						$vehicul = $consulta -> ret_matriz();
						//$vehicul = $vehicul[0][0];

                        $via = explode( "VIA", $rutaXXX );
                        
                        $date = explode( " ", $regist["fecnov"] );					
						$fech_nov = str_replace( "-", "/", $date[0] ) ;
						$hora_nov = $date[1];
						
						$fech_nov = explode( "/", $fech_nov );
						$fech_nov = $fech_nov[2]."/".$fech_nov[1]."/".$fech_nov[0];						

                        // message
                        $mensaje = '
								<div id="mensaje" style="color:#7F7F7F; border:0px; padding:0px; width:700px; text-align:justify;">
								
									<img src="https://ap.intrared.net:444/ap/satt_standa/imagenes/klogis/image001.jpg">
									
									<div style="color:#7F7F7F; font-family: Arial, sans-serif; font-size: 12pt;">
										<div style="padding:20px" >
											<strong>Fecha </strong> ' . $fech_nov . '<br/>
											<strong>Hora Novedad </strong> ' . $hora_nov . '<br/><br/>
											
											<strong>Conductor </strong> ' .ucwords( strtolower( $empre[0]['nom_conduc'] ) ) . '<br/>
											'.( (int)$vehicul[0][1]==0 ? '' : '<strong>Veh&iacute;culo </strong> '.ucwords( strtolower($vehicul[0][0])).'<br/>' ).'
											<strong>Tel&eacute;fono </strong> ' . ucwords( strtolower($empre[0]['con_telmov'])) . '<br/>
											<strong>Placa </strong> ' . strtoupper($empre[0][2]) . '<br/><br/>
											
											<strong>Ruta </strong> ' . ucwords( strtolower($rutaXXX)) . '<br/>';
                      if( $via[1] )
                        $mensaje .= '<strong>Via </strong> ' . ucwords( strtolower($via[1])) . '<br/>';
                      $mensaje.= '<strong>Despacho No </strong> ' . ucwords( strtolower($regist["despac"])) . '<br/>
											<strong>Puesto de Control </strong> ' . ucwords( strtolower($regist["sitio"])) . '<br/><br/>
											
											<strong>NOVEDAD: </strong> ' . strtolower( $nove ) . '<br/><br/>
                                            
										</div>
										<img src="https://ap.intrared.net:444/ap/satt_standa/imagenes/klogis/image002.jpg">
									</div>
									
								</div>';

                        // Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
                        $cabeceras = 'MIME-Version: 1.0' . "\r\n";
                        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                        // Cabeceras adicionales
                        $cabeceras .= 'From: no-reply@intrared.net' . "\r\n";
						
						            $dir .= ", miguel.chavez@klogistics.com.co";
                        // Mail it
                        //mail($dir, $titulo, $mensaje, $cabeceras);
                        //mail('jorge.preciado@intrared.net,leidy.acosta@intrared.net', $titulo, $mensaje, $cabeceras);
                    }
                }

                $regist["observ"] .= " . Se Envio Correos a : " . $dir.' '.$tieultima;
            }

            //inserta los datos de la novedad
            $query = "INSERT INTO " . $base_datos . ".tab_despac_noveda(num_despac,
                cod_rutasx,cod_contro,cod_noveda,
                tiem_duraci,fec_noveda,des_noveda,val_retras,cod_sitiox,
                usr_creaci,fec_creaci,usr_modifi,fec_modifi)
            VALUES (" . $regist["despac"] . "," . $rutact[0][0] . "," . $regist["contro"] . ",
                    " . $regist["noveda"] . "," . $regist["tieadi"] . ",
                    '" . $regist["fecnov"] . "','" . $regist["observ"] . "'," . $tiempretras[0][0] . ",
                    '$maxsit','" . $regist["usuari"] . "', '" . $regist["fecact"] . "',
                    '" . $regist["usuari"] . "', '" . $regist["fecact"] . "')";

            $insercion = new Consulta($query, $this->conexion, "R");


            //validacion de despachos de llegado automatico

            $query = "SELECT a.ind_llegad
            		FROM " . $base_datos . ".tab_transp_tipser a,
                     " . $base_datos . ".tab_despac_vehige c                                                                                  
            		WHERE a.cod_transp = c.cod_transp AND
                      c.num_despac = " . $regist["despac"] . " AND
                      a.ind_estado = '1' AND
                      a.fec_creaci =(SELECT MAX(b.fec_creaci) FROM " . $base_datos . ".tab_transp_tipser b
                                     WHERE b.cod_transp = c.cod_transp)";
            $ind_llegad = new Consulta($query, $this->conexion);
            $ind_llegad = $ind_llegad->ret_matriz();

            if ($ind_llegad[0][0] == '1' && $ind_defini != '1')
            {
                $query = "SELECT cod_contro
            		 FROM " . $base_datos . ".tab_despac_seguim 
            		 WHERE num_despac ='" . $regist["despac"] . "'
                       AND ind_estado = '1'
                 ORDER BY fec_planea ";
                $sigcontro = new Consulta($query, $this->conexion);
                $sigcontro = $sigcontro->ret_matriz();
                if ($sigcontro[1][0] == CONS_CODIGO_PCLLEG || $sigcontro[1][0] == '')
                {
                    $query = "UPDATE " . $base_datos . ".tab_despac_despac 
                   SET fec_llegad = NOW(),
                       obs_llegad = 'Llegada Automatica por el Sistema.USUARIO:" . $regist["usuari"] . "',
                       usr_modifi = '" . $regist["usuari"] . "',
                       fec_modifi = NOW() 
                   WHERE num_despac = '" . $regist["despac"] . "'";
                    $update = new Consulta($query, $this->conexion, "R");
                }
            }

            $query = "UPDATE " . $base_datos . ".tab_despac_despac 
             SET fec_ultnov = '" . $regist["fecnov"] . "',
                 fec_manala = " . $fec_manala . ",
                 cod_conult = '" . $regist["contro"] . "',
                 cod_ultnov = '" . $regist["noveda"] . "',";
            if ($regist["noveda"] == CONS_NOVEDA_ACAEMP)
                $query .=" ind_defini = '1', ";
            if ($regist["noveda"] == CONS_NOVEDA_ACAFAR)
                $query .=" ind_defini = '0', ";
            $query .=" usr_ultnov = '" . $regist["usuari"] . "'
             WHERE num_despac = '" . $regist["despac"] . "'";
            $update = new Consulta($query, $this->conexion, "R");
            //manda el correo ala empresa y al usuario en las novedades de acargo empresa y acargo faro
            if ($regist["noveda"] == CONS_NOVEDA_ACAEMP || $regist["noveda"] == CONS_NOVEDA_ACAFAR)
            {
                $query = "SELECT b.dir_emailx
  		      FROM " . BASE_DATOS . ".tab_despac_vehige a,
                 " . BASE_DATOS . ".tab_tercer_tercer b
  		     WHERE a.cod_transp = b.cod_tercer
                 AND a.num_despac = " . $regist["despac"] . "";
                $consulta = new Consulta($query, $this->conexion);
                $email = $consulta->ret_matriz();
                $email = $email[0][0];
                $dir_email = $regist["email"];
                if ($email != "")
                    $dir_email .= ",$email";
                $query = "SELECT b.num_placax 
                FROM " . $base_datos . ".tab_despac_vehige b
              	WHERE b.num_despac = " . $regist["despac"] . "
            ";
                $datos = new Consulta($query, $this->conexion);
                $datos = $datos->ret_matriz();
                $query = "SELECT a.abr_tercer 
                FROM " . $base_datos . ".tab_tercer_tercer a,
										 " . $base_datos . ".tab_despac_vehige b
              	WHERE b.num_despac = '" . $regist["despac"] . "' 
											AND a.cod_tercer = b.cod_transp";
                $empre = new Consulta($query, $this->conexion);
                $empre = $empre->ret_matriz();
                $info .="EMPRESA: " . $empre[0][0] . " \n";
                $info .="DESPACHO: " . $regist["despac"] . " \n";
                $info .="PLACA DEL VEHICULO: " . $datos[0][0] . " \n";
                $info .="PUESTO DE CONTROL: " . $lugar[0][0] . " \n";
                $info .="SITIO DE SEGUIMIENTO: " . $regist["sitio"] . " \n";
                $info .="FECHA DE LA NOVEDAD: " . $regist["fecnov"] . " \n";
                $info .="USUARIO: " . $regist["usuari"] . " \n";
                $info .="OBSERVACION:  \n";
                $info .=" " . $regist["observ"] . " \n";
                if ($regist["noveda"] == CONS_NOVEDA_ACAEMP)
                    $asunto = "NOVEDAD A Cargo de la Empresa";
                else
                    $asunto = "NOVEDAD A Cargo de Faro";

                //$dir_email .= ', engmiguelgarcia@gmail.com'; //COMENTARIAR THIS
                mail("$dir_email", $asunto, $info, 'From: supervisores@eltransporte.org');
            }
            $query = "SELECT cod_contro
            FROM " . $base_datos . ".tab_despac_seguim
            WHERE num_despac = '" . $regist["despac"] . "' AND
                  cod_rutasx = '" . $rutact[0][0] . "'
            ORDER by fec_planea";
            $consulta = new Consulta($query, $this->conexion);
            $seguim = $consulta->ret_matriz();
            for ($i = 0; $i < sizeof($seguim); $i++)
            {
                if ($regist["contro"] == $seguim[$i][0])
                {
                    break;
                }
                else
                {
                    $query = "UPDATE " . $base_datos . ".tab_despac_seguim
                SET ind_estado = '0'
              WHERE num_despac = '" . $regist["despac"] . "' AND
                    cod_rutasx = '" . $rutact[0][0] . "' AND
                    cod_contro = '" . $seguim[$i][0] . "' ";
                    $consulta = new Consulta($query, $this->conexion, "R");
                }
            }
            if ($regist["noveda"] == '9998')
            {
                $query = "UPDATE " . $base_datos . ".tab_despac_seguim
                  SET ind_estado = '0'
                WHERE num_despac = '" . $regist["despac"] . "' AND
                      cod_rutasx = '" . $rutact[0][0] . "'  ";
                $consulta = new Consulta($query, $this->conexion, "R");
            }
            $query = "SELECT a.fec_planea
		 FROM " . $base_datos . ".tab_despac_seguim a
		WHERE a.num_despac = " . $regist["despac"] . " AND
		      a.cod_rutasx = " . $rutact[0][0] . " AND
		      a.cod_contro = " . $regist["contro"] . "
	      ";

            $consulta = new Consulta($query, $this->conexion);
            $planru_c = $consulta->ret_matriz();


            $query = "UPDATE " . $base_datos . ".tab_despac_seguim
		  SET fec_alarma = '" . $regist["fecnov"] . "',
		      usr_modifi = '" . $regist["usuari"] . "',
		      fec_modifi = '" . $regist["fecact"] . "'
		WHERE num_despac = " . $regist["despac"] . " AND
		      cod_rutasx = " . $rutact[0][0] . " AND
		      cod_contro = " . $regist["contro"] . "
	      ";

            $insercion = new Consulta($query, $this->conexion, "R");

            //trae el plan de ruta a actualizar
            $query = "SELECT a.cod_contro,(TIME_TO_SEC(TIMEDIFF(a.fec_planea,'" . $planru_c[0][0] . "'))/60)
		 FROM " . $base_datos . ".tab_despac_seguim a
		WHERE a.num_despac = " . $regist["despac"] . " AND
		      a.cod_rutasx = " . $rutact[0][0] . " AND
		      a.fec_planea > '" . $planru_c[0][0] . "'
		      ORDER BY a.fec_planea
	      ";

            $consulta = new Consulta($query, $this->conexion);
            $planru_p = $consulta->ret_matriz();

            $tiemacu = 0;

            for ($i = 0; $i < sizeof($planru_p); $i++)
            {
                $tiemacu = $planru_p[$i][1];

                $query = "UPDATE " . $base_datos . ".tab_despac_seguim
		   SET fec_alarma = DATE_ADD('" . $regist["fecnov"] . "', INTERVAL " . $tiemacu . " MINUTE),
		       usr_modifi = '" . $regist["usuari"] . "',
		       fec_modifi = '" . $regist["fecact"] . "'
		 WHERE num_despac = " . $regist["despac"] . " AND
		       cod_rutasx = " . $rutact[0][0] . " AND
		       cod_contro = " . $planru_p[$i][0] . "
	       ";

                $insercion = new Consulta($query, $this->conexion, "R");
            }

            $query = "UPDATE " . $base_datos . ".tab_despac_vehige
                  SET fec_llegpl = DATE_ADD('" . $regist["fecnov"] . "', INTERVAL " . $tiemacu . " MINUTE),
                      usr_modifi = '" . $regist["usuari"] . "',
                      fec_modifi = '" . $regist["fecact"] . "'
                WHERE num_despac = " . $regist["despac"] . "
              ";

            $insercion = new Consulta($query, $this->conexion, "R");

            if ($ominter != 2)
            {
                $printmenerr = 0;

                //Manejo de la Interfaz Aplicaciones SAT
                $interfaz = new Interfaz_SAT($base_datos, $regist["nittra"], $regist["usuari"], $this->conexion);

                if ($ominter != 2)
                {
                    if ($interfaz->totalact > 0)
                    {
                        $a = 0;
                        for ($i = 0; $i < $interfaz->totalact; $i++)
                        {
                            if (10 == $interfaz->interfaz[$i]["operad"])
                            {
                                $query = "SELECT  a.num_placax, a.fec_salipl, b.cod_manifi " .
                                        "FROM " . $base_datos . ".tab_despac_vehige a, " .
                                        "" . $base_datos . ".tab_despac_despac b " .
                                        "WHERE a.num_despac = '" . $regist["despac"] . "' " .
                                        "AND a.num_despac = b.num_despac ";

                                $consulta = new Consulta($query, $this->conexion);
                                $mSalida = $consulta->ret_matriz();

                                $mQuerySelCodEmp = "SELECT cod_alianz " .
                                        "FROM " . $base_datos . ".tab_config_alianz " .
                                        "WHERE cod_tercer = '" . $regist["nittra"] . "' " .
                                        "AND ind_estado = '1' ";

                                $consulta = new Consulta($mQuerySelCodEmp, $this->conexion);
                                $mCodEmptra = $consulta->ret_matriz();


/*
                                //Ruta Web Service Alianza e-com.
                                $oSoapClient = new soapclient('http://190.144.2.98:9090/alianza/site_priv/webservice_oet/servidor2.php?wsdl', true);
                                $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                $mFecha[1] = date('H:i', strtotime($regist["fecnov"]));

                                //Parametros Web Service.
                                $parametros = array("usuario" => $interfaz->interfaz[$i]["usuari"], "password" => $interfaz->interfaz[$i]["passwo"],
                                    "placa" => $mSalida[0][0], "manifiesto" => $mSalida[0][2],
                                    "id_puestocontrol" => $regist["contro"], "id_novedad" => $regist["noveda"], "fecha" => $mFecha[0],
                                    "hora" => $mFecha[1], "observacion" => 'Interfaz - ' . $regist["observ"], "cod_empresa" => $mCodEmptra[0][0]);

                                //Consumo Web Service.
                                $respuesta = $oSoapClient->call("getDespacho", $parametros);
*/


                                /********************* TRATAMIENTO SOAP *********************/
                                ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                                try
                                {

                                    //Ruta Web Service Alianza e-com.
                                    $url_webser = "http://190.144.2.98:9090/alianza/site_priv/webservice_oet/servidor2.php?wsdl";

                                    $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                    $mFecha[1] = date('H:i', strtotime($regist["fecnov"]));

                                    $parametros = array("usuario" => $interfaz->interfaz[$i]["usuari"],
                                                        "password" => $interfaz->interfaz[$i]["passwo"],
                                                        "placa" => $mSalida[0][0],
                                                        "manifiesto" => $mSalida[0][2],
                                                        "id_puestocontrol" => $regist["contro"],
                                                        "id_novedad" => $regist["noveda"],
                                                        "fecha" => $mFecha[0],
                                                        "hora" => $mFecha[1],
                                                        "observacion" => 'Interfaz - ' . $regist["observ"],
                                                        "cod_empresa" => $mCodEmptra[0][0]
                                                        );

                                    $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

                                    //Métodos disponibles en el WS
                                    $respuesta = $oSoapClient -> __call( 'getDespacho', $parametros );
                                }
                                catch( SoapFault $e )
                                {
                                    $error_ = $e -> getMessage();

                                    if ($e -> faultcode != '2' && $e -> faultcode != '1' && $e -> faultstring != 'NINGUNO')
                                    {
                                        $mMessage = "******** Encabezado ******** \n";
                                        $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                                        $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                        $mMessage .= "Codigo alianza: " . $mCodEmptra[0][0] . " \n";
                                        $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                        $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                        $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " \n";
                                        $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                        $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                                        $mMessage .= "******** Detalle ******** \n";
                                        $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                                        $mMessage .= "Actor del error: " . $e -> faultactor . " \n";
                                        $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";
                                        $mMessage .= "Observaciones: " . $e -> detail . " \n";
                                        
                                        //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
                                        mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Web service Alianza", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                    }      
                                }
                                /********************* **************** *********************/



                            }

                            if (12 == $interfaz->interfaz[$i]["operad"])
                            {
                                //Colocar
                                $query = "SELECT  a.num_placax, a.fec_salipl, b.cod_manifi " .
                                        "FROM " . $base_datos . ".tab_despac_vehige a, " .
                                        "" . $base_datos . ".tab_despac_despac b " .
                                        "WHERE a.num_despac = '" . $regist["despac"] . "' " .
                                        "AND a.num_despac = b.num_despac ";

                                $consulta = new Consulta($query, $this->conexion);
                                $mSalida = $consulta->ret_matriz();

                                $mQuerySelCodEmp = "SELECT cod_alianz " .
                                        "FROM " . $base_datos . ".tab_config_alianz " .
                                        "WHERE cod_tercer = '" . $regist["nittra"] . "' " .
                                        "AND ind_estado = '1' ";

                                $consulta = new Consulta($mQuerySelCodEmp, $this->conexion);
                                $mCodEmptra = $consulta->ret_matriz();




/*
                                //Ruta Web Service Colocar e-com.
                                $oSoapClient = new soapclient('http://198.171.209.91/ecom/transweb/modulos/webservice_colocar/servidor.php?wsdl', true);
                                $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                $mFecha[1] = date('H:i', strtotime($regist["fecnov"]));

                                //Parametros Web Service.
                                $parametros = array("usuario" => $interfaz->interfaz[$i]["usuari"], "password" => $interfaz->interfaz[$i]["passwo"],
                                    "placa" => $mSalida[0][0], "manifiesto" => $mSalida[0][2],
                                    "id_puestocontrol" => $regist["contro"], "id_novedad" => $regist["noveda"], "fecha" => $mFecha[0],
                                    "hora" => $mFecha[1], "observacion" => 'Interfaz - ' . $regist["observ"], "cod_empresa" => $regist["nittra"]);

                                //Consumo Web Service.
                                $respuesta = $oSoapClient->call("getDespacho", $parametros);
*/


                                /********************* TRATAMIENTO SOAP *********************/
                                ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                                try
                                {
                                    //Ruta Web Service Colocar e-com.
                                    $url_webser = "http://198.171.209.91/ecom/transweb/modulos/webservice_colocar/servidor.php?wsdl";
                                    $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                    $mFecha[1] = date('H:i', strtotime($regist["fecnov"]));

                                    $parametros = array("usuario" => $interfaz->interfaz[$i]["usuari"],
                                                        "password" => $interfaz->interfaz[$i]["passwo"],
                                                        "placa" => $mSalida[0][0], "manifiesto" => $mSalida[0][2],
                                                        "id_puestocontrol" => $regist["contro"],
                                                        "id_novedad" => $regist["noveda"],
                                                        "fecha" => $mFecha[0],
                                                        "hora" => $mFecha[1],
                                                        "observacion" => 'Interfaz - ' . $regist["observ"],
                                                        "cod_empresa" => $regist["nittra"]
                                                        );

                                    $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                    
                                    //Métodos disponibles en el WS
                                    $respuesta = $oSoapClient -> __call( 'getDespacho', $parametros );
                                }
                                catch( SoapFault $e )
                                {
                                    $error_ = $e -> getMessage();

                                    if ($e -> faultcode && $e -> faultcode != '2' && $e -> faultcode != '1' && $e -> faultstring != 'NINGUNO')
                                    {
                                        $mMessage = "******** Encabezado ******** \n";
                                        $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                                        $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                        $mMessage .= "Codigo ecom: " . $mCodEmptra[0][0] . " \n";
                                        $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                        $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                        $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " \n";
                                        $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                        $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                                        $mMessage .= "******** Detalle ******** \n";
                                        $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                                        $mMessage .= "Actor del error: " . $e -> faultactor . " \n";
                                        $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";
                                        $mMessage .= "Observaciones: " . $e -> detail . " \n";

                                        //COMENTARIAR THIS
                                        mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Web service Faro - EcomColocar", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                    }
                                }
                                /********************* **************** *********************/




                            }
                            
                            // NELSON
/********************* **************** ********************* ********************* **************** ********************* ********************* **************** *********************/
                            if (51 == $interfaz->interfaz[$i]["operad"])
                            {
                                 $mQuerySelNomPc = "SELECT LOWER( nom_contro ) AS nom_contro " .
                                        "FROM " . $base_datos . ".tab_genera_contro " .
                                        "WHERE cod_contro = '" . $regist["contro"] . "' ";

                                $consulta = new Consulta($mQuerySelNomPc, $this->conexion);
                                $mNomPc = $consulta->ret_matriz();
                                
                                if( strpos(strtolower($regist["sitio"]), "eal") !== FALSE || strpos(strtolower($regist["sitio"]), "ecl") !== FALSE ||
                                    strpos($mNomPc[0][0], "eal") !== FALSE || strpos($mNomPc[0][0], "ecl") !== FALSE )
                                {
                                    $error_ = NULL;
                                    $query = "SELECT  a.num_placax, a.fec_salipl, b.cod_manifi " .
                                            "FROM " . $base_datos . ".tab_despac_vehige a, " .
                                            "" . $base_datos . ".tab_despac_despac b " .
                                            "WHERE a.num_despac = '" . $regist["despac"] . "' " .
                                            "AND a.num_despac = b.num_despac ";

                                    $consulta = new Consulta($query, $this->conexion);
                                    $mSalida = $consulta->ret_matriz();

                                    //Consulta de Detalle de Novedad
                                    $query = "SELECT  a.nom_noveda
                                            FROM " . $base_datos . ".tab_genera_noveda a 
                                            WHERE a.cod_noveda = " . $regist["noveda"];

                                    $consulta = new Consulta($query, $this->conexion);
                                    $mNoveda = $consulta->ret_matriz();

                                    //Consulta del puesto Padre de la homologacion
                                    $query = "SELECT  a.cod_contro
                                                FROM ".$base_datos.".tab_homolo_pcxeal a 
                                               WHERE a.cod_homolo = '".$regist["contro"]."'
                                                 OR a.cod_contro = '".$regist["contro"]."'";

                                    $consulta = new Consulta($query, $this->conexion);
                                    $mControPadre = $consulta->ret_matriz();
                                    if( !$mControPadre )
                                      $mControPadre[0][0] = $regist["contro"];

                                    $mQuerySelNomPc = "SELECT nom_contro " .
                                            "FROM " . $base_datos . ".tab_genera_contro " .
                                            "WHERE cod_contro = '" . $mControPadre[0][0] . "' ";

                                    $consulta = new Consulta($mQuerySelNomPc, $this->conexion);
                                    $mNomPc = $consulta->ret_matriz();

                                    //------- Obtenemos la latitud y longitud del puesto ---------
                                    $query = "SELECT  a.val_latitu, a.val_longit
                                                FROM ".$base_datos.".tab_genera_contro a 
                                               WHERE a.cod_contro = '".$mControPadre[0][0] ."' ";

                                    $consulta = new Consulta($query, $this->conexion);
                                    $mGeo = $consulta->ret_matriz();
                                    //--------------------------------------

                                    /********************* TRATAMIENTO SOAP *********************/
                                    ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                                    try
                                    {
                                        //Ruta Web Service Colocar e-com.
                                        $mEstado = '1';
                                        $url_webser = "http://www.colombiasoftware.net/base/webservice/reportepuestocontrol.php?wsdl";
                                        $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                        $mFecha[1] = date('H:i:s', strtotime($regist["fecnov"]));
                                        //echo "<pre>";print_r($interfaz);echo "</pre>";
                                        $parametros = array();
                                        $parametros["empresa"]=$interfaz->interfaz[$i]["nombre"];
                                        $parametros["usuario"]=$interfaz->interfaz[$i]["usuari"];
                                        $parametros["clave"]=$interfaz->interfaz[$i]["passwo"];
                                        $parametros["sentido"]="0";
                                        $parametros["direccion"]=$mNomPc[0][0];
                                        $parametros["tipo_evento"] ="0";
                                        $parametros["kilometraje"] ="0";
                                        $parametros["velocidad"] ="0";
                                        $parametros["codigo_puestocontrol"] =$mControPadre[0][0];
                                        
                                        //Se envia la novedad sin los codigos internos faro de usuarios que registran la novedad
                                        $pos = strpos($regist["observ"], ">");

                                        if ($pos !== false)
                                        {
                                          $regist["observ"] = substr( $regist["observ"], $pos + 1 );
                                        }
                                        
                                        
                                        $parametros["novedad"] = $mNoveda[0]["nom_noveda"].' '.$regist["observ"];
                                        $parametros["longitud"]=$mGeo[0]['val_longit'];
                                        $parametros["latitud"] =$mGeo[0]['val_latitu'];
                                        $parametros["hora"] =$mFecha[1];
                                        $parametros["fecha"] =$mFecha[0];
                                        $parametros["placa"] =$mSalida[0][0];
                                        $parametros["manifiesto_codigo"] =$mSalida[0][2];
                                      
                                        $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                 
                                        //Métodos disponibles en el WS
                                        /*echo "InsertarNovedadPC <br> puestocontrol_humadea <pre>";
                                        print_r($parametros);
                                        echo "</pre>";*/
                                        $respuesta = $oSoapClient -> __call( 'puestocontrol_humadea', $parametros );                                  
                                        
                                        if ( $respuesta != 'OK REPORTO' )
                                        {
                                            $error_ = $respuesta;
                                        }
                                    }
                                    catch( SoapFault $e )
                                    {
                                        $error_ = $e -> getMessage();
                                    }
                                    
                                    if( $error_ != NULL )
                                    {
                                      $mEstado = '0';
                                      $mMessage = "******** Encabezado ******** \n";
                                      $mMessage .= "Fecha y hora Sistema: " . date("Y-m-d H:i") . " \n";
                                      $mMessage .= "Fecha y hora Novedad: " . $mFecha[0].' '.$mFecha[1] . " \n";
                                      $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                      $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                      $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                      $mMessage .= "Codigo puesto de control del despacho: " . $regist["contro"] . " \n";
                                      $mMessage .= "Codigo puesto de control padre: " . $mControPadre[0][0] . " \n";
                                      $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                      $mMessage .= "Nombre novedad: " . $mNoveda[0]["nom_noveda"] . " \n";
                                      $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                                      $mMessage .= "******** Detalle ******** \n";
                                      $mMessage .= "Mensaje de error: " . $error_  . " \n";
                                      
                                      mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Error Web service Humadea - Colombiasoftware", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                      //mail("nelson.liberato@intrared.net", "Error Web service Humadea - Colombiasoftware", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                    }
                                     //Insert de Bitácora 
                                     $mLog = array();
                                     $mLog['mEstado'] = $mEstado;
                                     $mLog['mResult'] = $error_;
                                     $mLog['mUsuari'] = $regist["usuari"];
                                     $mLog['mNumDes'] = $regist["despac"];
                                    
                                     $this -> InsertPuestoControlHumadea( $parametros , $mLog );
                               }
                               else//Joger 
                               {
                               
                                    $error_ = NULL;
                                    $query = "SELECT  a.num_placax, a.fec_salipl, b.cod_manifi " .
                                            "FROM " . $base_datos . ".tab_despac_vehige a, " .
                                            "" . $base_datos . ".tab_despac_despac b " .
                                            "WHERE a.num_despac = '" . $regist["despac"] . "' " .
                                            "AND a.num_despac = b.num_despac ";

                                    $consulta = new Consulta($query, $this->conexion);
                                    $mSalida = $consulta->ret_matriz();

                                    //Consulta de Detalle de Novedad
                                    $query = "SELECT  a.nom_noveda
                                            FROM " . $base_datos . ".tab_genera_noveda a 
                                            WHERE a.cod_noveda = " . $regist["noveda"];

                                    $consulta = new Consulta($query, $this->conexion);
                                    $mNoveda = $consulta->ret_matriz();

                                    //Consulta del puesto Padre de la homologacion
                                    $query = "SELECT  a.cod_contro
                                                FROM ".$base_datos.".tab_homolo_pcxeal a 
                                               WHERE a.cod_homolo = '".$regist["contro"]."'
                                                 OR a.cod_contro = '".$regist["contro"]."'";

                                    $consulta = new Consulta($query, $this->conexion);
                                    $mControPadre = $consulta->ret_matriz();


                                    /********************* TRATAMIENTO SOAP *********************/
                                    ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                                    try
                                    {   
                                         $mEstado = '1';
                                        //Ruta Web Service Colocar e-com.
                                        $url_webser = "http://www.colombiasoftware.net/base/webservice/reportepuestocontrol.php?wsdl";
                                        $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                        $mFecha[1] = date('H:i:s', strtotime($regist["fecnov"]));
                                        //echo "<pre>";print_r($interfaz);echo "</pre>";
                                        $parametros = array();
                                        $parametros["empresa"]=$interfaz->interfaz[$i]["nombre"];
                                        $parametros["usuario"]=$interfaz->interfaz[$i]["usuari"];
                                        $parametros["clave"]=$interfaz->interfaz[$i]["passwo"];
                                        
                                        //Se envia la novedad sin los codigos internos faro de usuarios que registran la novedad
                                        $pos = strpos($regist["observ"], ">");

                                        if ($pos !== false)
                                        {
                                          $regist["observ"] = substr( $regist["observ"], $pos + 1 );
                                        }

                                        
                                        
                                        $parametros["novedad"] = $mNoveda[0]["nom_noveda"].' '.$regist["observ"];
                                        $parametros["hora"] =$mFecha[1];
                                        $parametros["fecha"] =$mFecha[0];
                                        $parametros["placa"] =$mSalida[0][0];
                                        $parametros["manifiesto_codigo"] =$mSalida[0][2];
                                        $parametros["lugar"] = $regist["sitio"];  

                                        $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                        
                                        //Métodos disponibles en el WS
                                        /*echo "InsertarNovedadPC <br> novedades_humadea <pre>";
                                        print_r($parametros);
                                        echo "</pre>";*/
                                        $respuesta = $oSoapClient -> __call( 'novedades_humadea', $parametros );

                                        //$respuesta = 'OK REPORTO';
                                        if ( $respuesta != 'OK REPORTO.' )
                                        {
                                            $error_ = $respuesta;
                                        }
                                      }
                                    catch( SoapFault $e )
                                    {
                                        $error_ = $e -> getMessage();
                                    }
                                    
                                    if(  $error_ != NULL )
                                    {
                                        $mEstado = '0';
                                        $mMessage = "******** Encabezado ******** \n";
                                        $mMessage .= "Fecha y hora Sistema: " . date("Y-m-d H:i") . " \n";
                                        $mMessage .= "Fecha y hora Novedad: " . $mFecha[0].' '.$mFecha[1] . " \n";
                                        $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                        $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                        $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                        $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                        $mMessage .= "Nombre novedad: " . $mNoveda[0]["nom_noveda"] . " \n";
                                        $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                                        $mMessage .= "******** Detalle ******** \n";
                                        $mMessage .= "Mensaje de error: " . $error_ . " \n";
                                        //echo $mMessage;
                                        mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Error Web service Colombia Software - Colombiasoftware Puestos virtuales", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                        //mail("hugo.malagon@intrared.net", "Error Web service Humadea - Colombiasoftware", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                    }
                                     //Insert de Bitácora 
                                     $mLog = array();
                                     $mLog['mEstado'] = $mEstado;
                                     $mLog['mResult'] = $respuesta;
                                     $mLog['mUsuari'] = $regist["usuari"];
                                     $mLog['mNumDes'] = $regist["despac"];
                                     
                                     $this -> InsertNovedadHumadea( $parametros , $mLog );
                                    
                                    /********************* **************** *********************/
                               }
                            }
/********************* **************** ********************* ********************* **************** ********************* ********************* **************** *********************/

                            if(57 == $interfaz->interfaz[$i]["operad"]) //GMT Onlinetools   // Prueba Nit 800081833  NIT ORIGINAL ->  8301019592
                            {
                              $query = "SELECT a.cod_manifi, b.num_placax, a.cod_tipdes " .
                                    "FROM " . $base_datos . ".tab_despac_despac a, " .
                                    "" . $base_datos . ".tab_despac_vehige b " .
                                    "WHERE a.num_despac = b.num_despac " .
                                    "AND a.num_despac = '" . $regist["despac"] . "' ";
                              $consulta = new Consulta($query, $this->conexion);
                              $mSalida = $consulta->ret_matriz();
                              
                              //Consulta del puesto Padre de la homologacion
                              $query = "SELECT  a.cod_contro
                                          FROM ".$base_datos.".tab_homolo_pcxeal a 
                                         WHERE a.cod_homolo = '".$regist["contro"]."'
                                           OR a.cod_contro = '".$regist["contro"]."'";
                              $consulta = new Consulta($query, $this->conexion);
                              $mControPadre = $consulta->ret_matriz();
                              if( !$mControPadre )
                                $mControPadre[0][0] = $regist["contro"];
                                          
                              // Consulta Nombre PC
                              $query = "SELECT  a.nom_contro, IF(a.ind_virtua = 1 ,NULL,a.cod_contro) AS cod_puecon
                                          FROM ".$base_datos.".tab_genera_contro a 
                                         WHERE a.cod_contro = '".$mControPadre[0][0]."'";
                              $consulta = new Consulta($query, $this->conexion);
                              $mNomContro = $consulta->ret_matriz();
                              
                              // Consulta el Nombre de la Novedad registrada
                              $query = "SELECT  a.nom_noveda
                                          FROM ".$base_datos.".tab_genera_noveda a 
                                         WHERE a.cod_noveda = '".$regist["noveda"]."'";
                              $consulta = new Consulta($query, $this->conexion);
                              $mNomNoveda = $consulta->ret_matriz();
                       
                              //Interfaz Transportes GMT Onlinetools   // Prueba Nit 800081833  NIT ORIGINAL ->  8301019592
                              ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                              try
                              {
                                $url_webser = "http://200.93.188.109/gmt/seguimiento/webservice/WebserviceReturnNovedad.php?wsdl";                                  

                                //Parametros Web Service.   
                                $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                $mFecha[1] = date('H-i-s', strtotime($regist["fecnov"]));                                
                                
                                $parametros = array();
                                $parametros["usuario"]=$interfaz->interfaz[$i]["usuari"];
                                $parametros["clave"]=$interfaz->interfaz[$i]["passwo"];
                                $parametros["planilla"] = $regist["despac"];
                                $parametros["placa"] =$mSalida[0][1];
                                $parametros["ind_naturaleza"] = (int)$mSalida[0][2];
                                $parametros["fecha_reporte"]= $mFecha[0];
                                $parametros["hora_reporte"] = $mFecha[1];
                                $parametros["cod_puesto"] = (int)$mNomContro[0][1];
                                $parametros["nom_punto"] = $mNomContro[0][0];
                                $parametros["cod_novedad"] = (int)$regist["noveda"];
                                $parametros["novedad"] =$mNomNoveda[0][0];
                                $parametros["observacion"] = $regist["observ"];
                                //echo "<pre>"; print_r( $parametros ); echo "</pre>";
                                $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                //Métodos disponibles en el WS
                                $respuesta = $oSoapClient -> __call( 'set_Novedad', $parametros );
                                echo "<pre>"; print_r( $respuesta ); echo "</pre>";
                              }
                              catch( SoapFault $e )
                              {
                                  $error_ = $e -> getMessage();

                                  if ($e -> faultcode && $e -> faultcode != '2' && $e -> faultcode != '1' && $e -> faultstring != 'NINGUNO')
                                  {
                                      $mMessage = "******** Encabezado ******** \n";
                                      $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                                      $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                      $mMessage .= "Codigo alianza: " . $mCodEmptra[0][0] . " \n";
                                      $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                      $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                      $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " \n";
                                      $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                      $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                                      $mMessage .= "******** Detalle ******** \n";
                                      $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                                      $mMessage .= "Actor del error: " . $e -> faultactor . " \n";
                                      $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";
                                      $mMessage .= "Observaciones: " . $e -> detail . " \n";

                                      //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
                                      mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Web service Faro - OnlineTools GMT", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                  }

                              }                           
                            }
                            if (11 == $interfaz->interfaz[$i]["operad"])
                            {
                                $query = "SELECT  a.num_placax, a.fec_salipl, b.cod_manifi " .
                                        "FROM " . $base_datos . ".tab_despac_vehige a, " .
                                        "" . $base_datos . ".tab_despac_despac b " .
                                        "WHERE a.num_despac = '" . $regist["despac"] . "' " .
                                        "AND a.num_despac = b.num_despac ";

                                $consulta = new Consulta($query, $this->conexion);
                                $mSalida = $consulta->ret_matriz();

                                $mQuerySelCodEmp = "SELECT cod_alianz " .
                                        "FROM " . $base_datos . ".tab_config_alianz " .
                                        "WHERE cod_tercer = '" . $regist["nittra"] . "' " .
                                        "AND ind_estado = '1' ";

                                $consulta = new Consulta($mQuerySelCodEmp, $this->conexion);
                                $mCodEmptra = $consulta->ret_matriz();





/*
                                //Ruta Web Service Coounidas e-com.
                                $oSoapClient = new soapclient('http://198.171.209.91/ecom/transweb/modulos/webservice_counida/servidor.php?wsdl', true);
                                $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                $mFecha[1] = date('H:i', strtotime($regist["fecnov"]));

                                //Parametros Web Service.
                                $parametros = array("usuario" => $interfaz->interfaz[$i]["usuari"], "password" => $interfaz->interfaz[$i]["passwo"],
                                    "placa" => $mSalida[0][0], "manifiesto" => $mSalida[0][2],
                                    "id_puestocontrol" => $regist["contro"], "id_novedad" => $regist["noveda"], "fecha" => $mFecha[0],
                                    "hora" => $mFecha[1], "observacion" => 'Interfaz - ' . $regist["observ"], "cod_empresa" => $regist["nittra"]);

                                //Consumo Web Service.
                                $respuesta = $oSoapClient->call("getDespacho", $parametros);

*/

                                /********************* TRATAMIENTO SOAP *********************/
                                ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                                try
                                {
                                    //Ruta Web Service Coounidas e-com.
                                    $url_webser = "http://198.171.209.91/ecom/transweb/modulos/webservice_counida/servidor.php?wsdl";
                                    $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                    $mFecha[1] = date('H:i', strtotime($regist["fecnov"]));

                                    //Parametros Web Service.
                                    $parametros = array("usuario" => $interfaz->interfaz[$i]["usuari"],
                                                        "password" => $interfaz->interfaz[$i]["passwo"],
                                                        "placa" => $mSalida[0][0],
                                                        "manifiesto" => $mSalida[0][2],
                                                        "id_puestocontrol" => $regist["contro"],
                                                        "id_novedad" => $regist["noveda"],
                                                        "fecha" => $mFecha[0],
                                                        "hora" => $mFecha[1],
                                                        "observacion" => 'Interfaz - ' . $regist["observ"],
                                                        "cod_empresa" => $regist["nittra"]
                                                        );

                                    $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                    
                                    //Métodos disponibles en el WS
                                    $respuesta = $oSoapClient -> __call( 'getDespacho', $parametros );
                                }
                                catch( SoapFault $e )
                                {
                                    $error_ = $e -> getMessage();

                                    if ($e -> faultcode && $e -> faultcode != '2' && $e -> faultcode != '1' && $e -> faultstring != 'NINGUNO')
                                    {
                                        $mMessage = "******** Encabezado ******** \n";
                                        $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                                        $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                        $mMessage .= "Codigo alianza: " . $mCodEmptra[0][0] . " \n";
                                        $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                        $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                        $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " \n";
                                        $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                        $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                                        $mMessage .= "******** Detalle ******** \n";
                                        $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                                        $mMessage .= "Actor del error: " . $e -> faultactor . " \n";
                                        $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";
                                        $mMessage .= "Observaciones: " . $e -> detail . " \n";

                                        //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
                                        mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Web service Faro - EcomCoounidas", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                    }

                                }
                                /********************* **************** *********************/



                            }						
							
							$nit_empres = $regist["nittra"];//NIT EMPRESA.
							
                            if ( 50 == $interfaz -> interfaz[$i]["operad"] )//SI ESTA ACTIVA LA INTERFAZ "FARO".
                            {
                              $error_ = NULL;
								//CONSULTAR URL WSDL.
								$query = "SELECT a.url_webser	
										  FROM ".BD_STANDA.".tab_genera_server a,
											   ".$base_datos.".tab_transp_tipser b
										  WHERE a.cod_server = b.cod_server AND
												b.cod_transp = '$nit_empres' 
										  ORDER BY b.fec_creaci DESC ";

                                $consulta = new Consulta( $query, $this -> conexion );
                                $url_webser = $consulta -> ret_matriz();
								$url_webser =  $url_webser[0][0];//URL DEL WSDL.
								
								                            //$oSoapClient = new soapclient( $url_webser, true );//CLIENTE WEBSERVICES.
                                $regist['server'] = $url_webser;
                                //$caido = $oSoapClient->getError();
                                //print_r( $caido );//MOSTRAR ERRORES.


                                ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                                try
                                {
                                    //echo $url_webser;
                                    $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

                                    //Métodos disponibles en el WS
                                    $mResult = $oSoapClient -> __call( 'aplicaExists', array( "nom_aplica" => $interfaz -> interfaz[$i]["nombre"] ) );
                                    $mResult = explode("; ", $mResult);
                                    $mCodResp = explode(":", $mResult[0]);
                                    $mMsgResp = explode(":", $mResult[1]);

                                    if ("1000" != $mCodResp[1])
                                    {
                                        $error_ = $mMsgResp[1];
                                    }                                   
                                }
                                catch( SoapFault $e )
                                {
                                    $error_ = $e -> getMessage();
                                }

                                /*
                                $oSoapClient = new soapclient( $url_webser, true );//CLIENTE WEBSERVICES.
								$regist['server'] = $url_webser;
								$caido = $oSoapClient->getError();
								
								//print_r( $caido );//MOSTRAR ERRORES.
								
								$oSoapClient->soap_defencoding = 'ISO-8859-1';
								$mResult = $oSoapClient -> call( "aplicaExists", 
											array( "nom_aplica" => $interfaz -> interfaz[$i]["nombre"] ) );
										
								//print_r( $mResult );
								
								
								if ($oSoapClient->fault)
								{
									//Notifica Fallos
									$error_ = $oSoapClient->faultcode . ':' . $oSoapClient->faultdetail . ':' . $oSoapClient->faultstring;
								}
								else
								{
									$err = $oSoapClient->getError();
									if ($err)
									{
										// Notifica errores
										$error_ = $err;
									}
									else
									{
										//Procesa el resultado del WS
										$mResult = explode("; ", $respuesta);
										$mCodResp = explode(":", $mResult[0]);
										$mMsgResp = explode(":", $mResult[1]);

										if ("1000" != $mCodResp[1])
										{
											$error_ = $mMsgResp[1];
										}
										
									}
								}
								*/
								
								
								
								$query = "SELECT a.cod_manifi, b.num_placax " .
                                        "FROM " . $base_datos . ".tab_despac_despac a, " .
                                        "" . $base_datos . ".tab_despac_vehige b " .
                                        "WHERE a.num_despac = b.num_despac " .
                                        "AND a.num_despac = '" . $regist["despac"] . "' ";
                                $consulta = new Consulta($query, $this->conexion);
                                $mSalida = $consulta->ret_matriz();

                                $mQuerySelNomNov = "SELECT nom_noveda, ind_alarma, ind_tiempo, nov_especi, ind_manala  " .
                                        "FROM " . $base_datos . ".tab_genera_noveda " .
                                        "WHERE cod_noveda = '" . $regist["noveda"] . "' ";

                                $consulta = new Consulta($mQuerySelNomNov, $this->conexion);
                                $mNomNov = $consulta->ret_matriz();

                                $mQuerySelNomPc = "SELECT nom_contro " .
                                        "FROM " . $base_datos . ".tab_genera_contro " .
                                        "WHERE cod_contro = '" . $regist["contro"] . "' ";

                                $consulta = new Consulta($mQuerySelNomPc, $this->conexion);
                                $mNomPc = $consulta->ret_matriz();

                                $mQuerySelPcxbas = "SELECT cod_pcxbas 
                                   FROM " . $base_datos . ".tab_homolo_trafico 
                                  WHERE cod_transp = '" . $regist["nittra"] . "'
                                    AND cod_pcxfar = '" . $regist["contro"] . "'
                                    AND cod_rutfar = '" . $regist["rutax"] . "'
                                  ";

                                $consulta = new Consulta($mQuerySelPcxbas, $this->conexion);
                                $mCodPcxbas = $consulta->ret_matriz();

                                $parametros = array("nom_usuari" => $interfaz->interfaz[$i]["usuari"],
                                    "pwd_clavex" => $interfaz->interfaz[$i]["passwo"],
                                    "nom_aplica" => $interfaz->interfaz[$i]["nombre"],
                                    "num_manifi" => $mSalida[0]['cod_manifi'],
                                    "num_placax" => $mSalida[0]['num_placax'],
                                    "cod_novbas" => 0,
                                    "cod_conbas" => $mCodPcxbas[0][0],
                                    "tim_duraci" => $regist["tieadi"],
                                    "fec_noveda" => date('Y-m-d H:i', strtotime($regist["fecnov"])),
                                    "des_noveda" => $regist["observ"],
                                    "nom_contro" => $mNomPc[0][0],
                                    "nom_sitiox" => substr($regist["sitio"], 0, 50),
                                    "cod_confar" => NULL,
                                    'cod_novfar' => $regist['noveda'],
                                    'nom_noveda' => $mNomNov[0]['nom_noveda'],
                                    'ind_alarma' => $mNomNov[0]['ind_alarma'],
                                    'ind_tiempo' => $mNomNov[0]['ind_tiempo'],
                                    'nov_especi_' => $mNomNov[0]['nov_especi'],
                                    'ind_manala' => $mNomNov[0]['ind_manala']
                                );//ARRAY
								
								
                                if (!$error_)
                                {
                                
                                    for( $index1 = 0; $index1 < count( $mCodPcxbas ); $index1++ )
                                    {
                                      $parametros['cod_conbas'] = $mCodPcxbas[$index1][0];
                                      //Consumo Web Service.
                                      try
                                      {
                                          $respuesta = $oSoapClient -> __call( 'setNovedadPC', $parametros );

                                          $mResult = explode("; ", $respuesta);
                                          $mCodResp = explode(":", $mResult[0]);
                                          $mMsgResp = explode(":", $mResult[1]);

                                          if ("1000" != $mCodResp[1])
                                          {
                                              $error_ = $mMsgResp[1];
                                          }
                                          else
                                          {
                                              $error_ = NULL;
                                              break;
                                          }
                                      }
                                      catch( SoapFault $e )
                                      {
                                          $error_ = $e -> getMessage();
                                      }
                                    }
                                    if ($error_ != NULL)
                                    {
                                        echo "<div align='center' style='color:#000000; padding:5px; font-size:14px; background-color:#FFAFAF; border:2px solid #000000;'><b>Error en Webservice - Insertar novedad en " . $interfaz->interfaz[$i]["nombre"] . ':' . $error_ . "</b></div>";
                                        $mMessage = "******** Encabezado ******** \n";
                                        $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                                        $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                        $mMessage .= "Aplicacion: " . $interfaz->interfaz[$i]["nombre"] . " \n";
                                        $mMessage .= "Numero de despacho SAT: " . $regist["despac"] . " \n";
                                        $mMessage .= "Placa del vehiculo: " . $mSalida[0][1] . " \n";
                                        $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " \n";
                                        $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                        $mMessage .= "Nombre novedad: " . $mNomNov[0]['nom_noveda'] . " \n";
                                        $mMessage .= "******** Detalle ******** \n";
                                        $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                                        $mMessage .= "Mesaje de error: " . $error_ . " \n";

                                        $novedaError['cod_respon'] = $e -> faultcode;
                                        $novedaError['msg_respon'] = $error_;
                                        $novedaError['det_respon'] = $mMessage;
                                        //Se registran errores de la interfaz en la BD
                                        $this->setNovedadError($parametros, $regist, $novedaError, 'pc');

                                        //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
                                        mail("hugo.malagon@intrared.net", "Web service TRAFICO - SAT - NOVEDAD PC", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                        //mail( "supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Web service TRAFICO - SAT", $mMessage,'From: soporte.ingenieros@intrared.net' );
                                    }
                                }
                                else
                                {
                                  $novedaError['cod_respon'] = '';
                                  $novedaError['msg_respon'] = '';
                                  $novedaError['det_respon'] = $error_;
                                  
                                  $this->setNovedadError($parametros, $regist, $novedaError, 'pc');
                                  echo "<div align='center' style='color:#000000; padding:5px; font-size:14px; background-color:#FFAFAF; border:2px solid #000000;'><b>Error en Webservice - Insertar novedad en " . $interfaz->interfaz[$i]["nombre"] . ':' . $error_ . "</b></div>";
                                }
                            }
                            if (56 == $interfaz->interfaz[$i]["operad"])//transportadora referencia =  800081833 || Trasportadora FW 
                            {

                                $query = "SELECT  a.num_placax, a.fec_salipl, b.cod_manifi " .
                                        "FROM " . $base_datos . ".tab_despac_vehige a, " .
                                        "" . $base_datos . ".tab_despac_despac b " .
                                        "WHERE a.num_despac = '" . $regist["despac"] . "' " .
                                        "AND a.num_despac = b.num_despac ";

                                $consulta = new Consulta($query, $this->conexion);
                                $mSalida = $consulta->ret_matriz();

                                //Consulta de Detalle de Novedad
                                $query = "SELECT  a.cod_fwxxxx
                                        FROM " . $base_datos . ".tab_noveda_tranfw a 
                                        WHERE a.cod_faroxx = " . $regist["noveda"];

                                $consulta = new Consulta($query, $this->conexion);
                                $mNoveda = $consulta->ret_matriz();
                                $mNoveda[0][0] = $mNoveda[0][0] == NULL ? '01' : $mNoveda[0][0];
                                

                                //Consulta del puesto Padre de la homologacion
                                $query = "SELECT  a.cod_contro
                                            FROM ".$base_datos.".tab_homolo_pcxeal a 
                                           WHERE a.cod_homolo = '".$regist["contro"]."'
                                             OR a.cod_contro = '".$regist["contro"]."'";
                                
                                $consulta = new Consulta($query, $this->conexion);
                                $mControPadre = $consulta->ret_matriz();
                                
                                if( !$mControPadre )
                                  $mControPadre[0][0] = $regist["contro"];

                                 //Consulta Sitio antes, en despac_sitio                                
                                 $query = "SELECT CONCAT( b.cod_sitiox, ' - ', b.nom_sitiox ), a.cod_rutasx ".
                                             "FROM ".$base_datos.".tab_despac_contro a, ".$base_datos.".tab_despac_sitio b ".
                                             "WHERE ".
                                             "a.cod_sitiox = b.cod_sitiox ".
                                             "AND a.cod_consec = (SELECT MAX( cod_consec ) FROM ".$base_datos.".tab_despac_contro WHERE num_despac = '".$regist["despac"]."' )".
                                             "AND a.cod_contro = '".$mControPadre[0][0]."'";
                                //$consulta = new Consulta($query, $this->conexion);
                                //$mControSitiox = $consulta->ret_matriz();
                                
                                // -------------------------------**** Calcula los kilómetros desde el último puesto de control reportado y antes del sitio
                                // Trae la ultima hora de PC Reportada
                                
                                $query = "SELECT MAX(DATE_FORMAT(fec_noveda,'%H:%i')) FROM ".$base_datos.".tab_despac_noveda WHERE num_despac = '".$regist["despac"]."'";
                                $consulta = new Consulta($query, $this->conexion);
                                $mLastTimeNovePC = $consulta->ret_matriz();
                                
                                
                                // Trae los datos del puesto de control Tiempo y distancia
                                
                                $query = "SELECT val_duraci, val_distan FROM ".$base_datos.".tab_genera_rutcon WHERE cod_rutasx = '".$mControSitiox[0][1]."' AND cod_contro  = '".$mControPadre[0][0]."'";
                                $consulta = new Consulta($query, $this->conexion);
                                $mDataLastPC = $consulta->ret_matriz();         
                                
                                // Calcula la diferencia en tiempo desde la ultima novedad en PC y el NC                                
                                
                                $query ="SELECT TIMEDIFF( '".date('H:i', strtotime($regist["fecnov"]))."', '".$mLastTimeNovePC[0][0]."' )";                               
                                $consulta = new Consulta($query, $this->conexion);
                                $mDiffPC_NC = $consulta->ret_matriz();
                                
                                
                                // CAlcula la distancia que le queda desde el ultimo contros (Antes) haste el siguiente PC                                
                                $mTimer = explode(":",$mDiffPC_NC[0][0] );                                
                                $mHours = $mTimer[0];
                                $mMinut = $mTimer[1];
                                
                                $mFullTime = ($mHours * 60) + ($mMinut); 
                                
                                $mPasoOne = ($mFullTime) * ($mDataLastPC[0][1]);
                                //$mFinalDinstace = ( $mPasoOne) / ($mDataLastPC[0][0]) ;
                                //********************* TRATAMIENTO SOAP *********************
                                ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                                try
                                {
                                    //Ruta Web Service Colocar e-com.
                                    $url_webser = "http://50.28.44.212/~forward/server/seguimiento.php?wsdl";
                                    $mFecha[0] = date('Y-m-d H:i', strtotime($regist["fecnov"]));
                                    
                                    $parametros = array();
                                    $parametros["usuario"]=$interfaz->interfaz[$i]["usuari"];
                                    $parametros["clave"]=$interfaz->interfaz[$i]["passwo"];
                                    $parametros["fecha"] = $mFecha[0];
                                    $parametros["punto_control"] =substr( $regist["sitio"], 0, 30 );
                                    $parametros["distancia_pc"] = $mFinalDinstace;
                                    $parametros["motivo"]= $mNoveda[0][0];
                                    $parametros["referencia"] = $mControSitiox[0][0];
                                    $parametros["placa"] =$mSalida[0][0];
                                   
                                    echo "<pre>";print_r( $parametros ); echo "</pre>";
                                    
                                    $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                    
                                    //Métodos disponibles en el WS
                                    
                                    $respuesta = $oSoapClient -> __call( 'reporte', $parametros );

                                    echo "<pre>"; print_r( $respuesta ); echo "</pre>";
                                    
                                    
                                    if ( $respuesta != 'Ok' )
                                    {
                                        $mMessage = "******** Encabezado ******** \n";
                                        $mMessage .= "Fecha y hora Sistema: " . date("Y-m-d H:i") . " \n";
                                        $mMessage .= "Fecha y hora Novedad: " . $mFecha[0].' '.$mFecha[1] . " \n";
                                        $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                        $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                        $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                        $mMessage .= "Codigo puesto de control del despacho: " . $regist["contro"] . " \n";
                                        $mMessage .= "Codigo puesto de control padre: " . $mControPadre[0][0] . " \n";
                                        $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                        $mMessage .= "Nombre novedad: " . $mNoveda[0]["nom_noveda"] . " \n";
                                        $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                                        $mMessage .= "******** Detalle ******** \n";
                                        $mMessage .= "Mensaje de error: " . $respuesta . " \n";
                                        //echo $mMessage;
                                        mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Error Web service Trans FW - SISTEMA ARGO", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                        //mail("hugo.malagon@intrared.net", "Error Web service Humadea - Colombiasoftware", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                    }
                                  }
                                catch( SoapFault $e )
                                {
                                    $error_ = $e -> getMessage();
                                    echo "<pre>";print_r( $error_ ); echo "</pre>";
                                }
                                //********************* **************** ********************
                            }

                            if (25 == $interfaz->interfaz[$i]["operad"])
                            {
                                $query = "SELECT  a.num_placax, a.fec_salipl, b.cod_manifi " .
                                        "FROM " . $base_datos . ".tab_despac_vehige a, " .
                                        "" . $base_datos . ".tab_despac_despac b " .
                                        "WHERE a.num_despac = '" . $regist["despac"] . "' " .
                                        "AND a.num_despac = b.num_despac ";

                                $consulta = new Consulta($query, $this->conexion);
                                $mSalida = $consulta->ret_matriz();

                                $mQuerySelNomPc = "SELECT nom_contro " .
                                        "FROM " . $base_datos . ".tab_genera_contro " .
                                        "WHERE cod_contro = '" . $regist["contro"] . "' ";

                                $consulta = new Consulta($mQuerySelNomPc, $this->conexion);
                                $mNomPc = $consulta->ret_matriz();



/*
                                //Ruta Web Service Faro.
                                $oSoapClient = new soapclient('https://server.intrared.net:444/ap/interf/app/faro/wsdl/faro.wsdl', true);

                                //Parametros Web Service.
                                $parametros = array("nom_usuari" => $interfaz->interfaz[$i]["usuari"],
                                    "pwd_clavex" => $interfaz->interfaz[$i]["passwo"],
                                    "cod_manifi" => $mSalida[0][2],
                                    "fec_noveda" => date('Y-m-d', strtotime($regist["fecnov"])),
                                    "hor_noveda" => date('H:i:s', strtotime($regist["fecnov"])),
                                    "nom_contro" => $mNomPc[0][0],
                                    "des_observ" => $regist["observ"],
                                    "nom_llavex" => "59a68t7j95s4t96dS2g9A",
                                    "fec_pronov" => date('Y-m-d', strtotime($regist["fecnov"])),
                                    "hor_pronov" => date('H:i:s', strtotime($regist["fecnov"])));

                                //Consumo Web Service Faro Metodo que consume WS de Intracarga desde PHP 5.
                                $result = $oSoapClient->call("setPCIntracarga", $parametros);
                                $result2 = explode("; ", $result);
                                $mCodResp = explode(":", $result2[0]);
                                $mMsgResp = explode(":", $result2[1]);
*/




                                /********************* TRATAMIENTO SOAP *********************/
                                ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                                try
                                {
                                    //Ruta Web Service Faro.
                                    $url_webser = "https://ap.intrared.net:444/ap/interf/app/faro/wsdl/faro.wsdl";
                                    $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                    $mFecha[1] = date('H:i', strtotime($regist["fecnov"]));

                                    $parametros = array("nom_usuari" => $interfaz->interfaz[$i]["usuari"],
                                        "pwd_clavex" => $interfaz->interfaz[$i]["passwo"],
                                        "cod_manifi" => $mSalida[0][2],
                                        "fec_noveda" => date('Y-m-d', strtotime($regist["fecnov"])),
                                        "hor_noveda" => date('H:i:s', strtotime($regist["fecnov"])),
                                        "nom_contro" => $mNomPc[0][0],
                                        "des_observ" => $regist["observ"],
                                        "nom_llavex" => "59a68t7j95s4t96dS2g9A",
                                        "fec_pronov" => date('Y-m-d', strtotime($regist["fecnov"])),
                                        "hor_pronov" => date('H:i:s', strtotime($regist["fecnov"])),
                                        "num_placax" => $mSalida[0][0]);

                                    $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                    
                                    //Métodos disponibles en el WS
                                    $respuesta = $oSoapClient -> __call( 'setPCIntracarga', $parametros );
                                    echo "<pre>";
                                    print_r($respuesta);
                                    echo "</pre>";
                                }
                                catch( SoapFault $e )
                                {
                                    $error_ = $e -> getMessage();
                                  
                                    //if ("1000" != trim($mCodResp[1]))
                                    if ("1000" != $e -> faultcode)
                                    {
                                        $mMessage = "******** Encabezado ******** \n";
                                        $mMessage .= "Fecha y hora de la novedad: " . date("Y-m-d H:i", strtotime($regist["fecnov"])) . " \n";
                                        $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                        $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                        $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                        $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " \n";
                                        $mMessage .= "Nombre puesto de control: " . $mNomPc[0][0] . " \n";
                                        $mMessage .= "Observacion.: " . $regist["observ"] . " \n";
                                        $mMessage .= "******** Detalle ******** \n";
                                        $mMessage .= "Cod Error: " . $e -> faultcode . " \n";
                                        $mMessage .= "Error: " . $e -> faultstring . " \n";
                                        $mMessage .= "Error Detallado: " . $result . " \n";

                                        //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
                                        mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Web service Intracarga", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                    }

                                }
                                /********************* **************** *********************/



                            }

                            /*if (10 != $interfaz->interfaz[$i]["operad"] && 12 != $interfaz->interfaz[$i]["operad"] && 11 != $interfaz->interfaz[$i]["operad"] && 50 != $interfaz->interfaz[$i]["operad"] && 25 != $interfaz->interfaz[$i]["operad"])
                            {
                                $homolodespac = $interfaz->getHomoloDespac($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["despac"]);

                                if ($homolodespac["DespacHomolo"] > 0)
                                {
                                    $homolocontro = $interfaz->getHomoloContro($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["contro"]);

                                    if ($homolocontro["PCHomolo"] > 0)
                                    {
                                        $homolonoveda = $interfaz->getHomoloTranspNoveda($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["noveda"]);

                                        if ($homolonoveda["NovedadHomolo"] <= 0)
                                            ;
                                        {
                                            $mensaje_sat[$a] = "La Novedad No se Encuentra Homologada en la Interfaz " . $interfaz->interfaz[$i]["nombre"] . ".";
                                            $printmenerr = 1;
                                            $a++;
                                        }
                                    }
                                    else
                                    {
                                        $mensaje_sat[$a] = "El Puesto de Control No se Encuentra Homologado en la Interfaz " . $interfaz->interfaz[$i]["nombre"] . ".";
                                        $printmenerr = 1;
                                        $a++;
                                    }
                                }
                                else
                                {
                                    $mensaje_sat[$a] = "El Despacho No se Encuentra Homologado en la Interfaz " . $interfaz->interfaz[$i]["nombre"] . ".";
                                    $printmenerr = 1;
                                    $a++;
                                }
                            }
                            
                            */





                        }
                    }

                    if ($printmenerr)
                    {
                        for ($i = 0; $i < $a; $i++)
                        {
                            $RESPON[$i]["indica"] = 0;
                            $RESPON[$i]["mensaj"] = $mensaje_sat[$i];
                        }

                        return $RESPON;
                    }
                }

                if ($interfaz->totalact > 0)
                {
                    $a = 0;
                    $printmenerr = 0;

                    /*for ($i = 0; $i < $interfaz->totalact; $i++)
                    {
                        $homolodespac = $interfaz->getHomoloDespac($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["despac"]);

                        if ($homolodespac["DespacHomolo"] > 0)
                        {
                            $homolocontro = $interfaz->getHomoloContro($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["contro"]);

                            if ($homolocontro["PCHomolo"] > 0)
                                ;
                            {
                                $homolonoveda = $interfaz->getHomoloTranspNoveda($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["noveda"]);

                                if ($homolonoveda["NovedadHomolo"] > 0)
                                {
                                    $noveda_ws["despac"] = $regist["despac"];
                                    $noveda_ws["contro"] = $regist["contro"];
                                    $noveda_ws["noveda"] = $regist["noveda"];
                                    $noveda_ws["duraci"] = $regist["tieadi"];
                                    $noveda_ws["fechax"] = $regist["fecnov"];
                                    $noveda_ws["observ"] = $regist["observ"];

                                    $resultado_ws = $interfaz->insNovedaPC($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $noveda_ws);

                                    if ($resultado_ws["Confirmacion"] == "OK")
                                    {
                                        $mensaje_sat[$a][0] = 1;
                                        $mensaje_sat[$a][1] = "La Novedad Fue Registrada Exitosamente en la Interfaz " . $interfaz->interfaz[$i]["nombre"] . "";
                                    }
                                    else
                                    {
                                        $mensaje_sat[$a][0] = 0;
                                        $mensaje_sat[$a][1] = "Se Presento un Error al Insertar la Novedad en la Interfaz " . $interfaz->interfaz[$i]["nombre"] . " :: " . $resultado_ws["Confirmacion"];
                                        $printmenerr++;
                                    }
                                    $a++;
                                }
                            }
                        }
                    }
                    */
                }
            }

            $query = "SELECT a.num_placax,b.cod_manifi
              FROM " . $base_datos . ".tab_despac_vehige a,
              	   " . $base_datos . ".tab_despac_despac b
             WHERE a.num_despac = " . $regist["despac"] . " AND
             	   a.num_despac = b.num_despac
           ";

            $consulta = new Consulta($query, $this->conexion);
            $placax = $consulta->ret_vector();

            if ($ominter != 3)
            {

                //Manejo Interfaz GPS

                /* $interf_gps = new Interfaz_GPS();
                  $interf_gps -> Interfaz_GPS_envio($regist["nittra"],$base_datos,$regist["usuari"],$this -> conexion);

                  for($i = 0; $i  < $interf_gps -> cant_interf; $i++)
                  {
                  if($interf_gps -> getVehiculo($placax[0],$interf_gps -> cod_operad[$i][0],$regist["nittra"]))
                  {
                  $idgps = $interf_gps -> getIdGPS($placax[0],$interf_gps -> cod_operad[$i][0],$regist["nittra"]);

                  if($interf_gps -> setAcTimeRepor($interf_gps -> cod_operad[$i][0],$regist["nittra"],$placax[0],$idgps,$regist["despac"],$regist["fecnov"],$interf_gps -> val_timtra[$i][0]));
                  }
                  } */
            }

            $porapl["nittra"] = $regist["nittra"];
            $porapl["bdsata"] = $base_datos;

            $query = "SELECT a.nom_contro
  			  FROM " . $base_datos . ".tab_genera_contro a
  			 WHERE a.cod_contro = " . $regist["contro"] . "
  		   ";

            $consulta = new Consulta($query, $this->conexion);
            $nomcontr = $consulta->ret_vector();

            $query = "SELECT a.nom_noveda
  			  FROM " . $base_datos . ".tab_genera_noveda a
  			 WHERE a.cod_noveda = " . $regist["noveda"] . " AND
  			 	   a.ind_alarma = 'S'
  		   ";

            $consulta = new Consulta($query, $this->conexion);
            $nomnoved = $consulta->ret_vector();

            if ($nomnoved)
            {
                $despac_men["manifi"] = $placax[1];
                $despac_men["contro"] = $nomcontr[0];
                $despac_men["noveda"] = $nomnoved[0];
                $despac_men["placa"] = $placax[0];
                $despac_men["fecnov"] = $regist["fecnov"];
                $despac_men["observ"] = $regist["observ"];
                $despac_men["tipnov"] = "Novedad Puesto de Control";

                $MensajReport = new EnvMensaj();
                $MensajReport->EnvMensajCron($this->conexion, $porapl);
                $MensajReport->EjecPrecesNove($despac_men);
            }

            $RESPON[0]["indica"] = 1;
            $RESPON[0]["mensaj"] = "La Novedad se Registro Exitosamente en el Sistema";

            for ($i = 0; $i < $a; $i++)
            {
                $RESPON[$i + 1]["indica"] = $mensaje_sat[$i][0];
                $RESPON[$i + 1]["mensaj"] = $mensaje_sat[$i][1];
            }
        }

        return $RESPON;
    }

    //$regist representa los valores del reporte
    //$ominter representa desde donde se envia el reporte 0-> Aplicac, 1-> WAP, 2-> Interf SAT, 3-> interf GPS
    function InsertarNovedadNC($base_datos, $regist, $ominter)
    {
    
        $fTiempEnvia = $regist["tieadi"];
        //trae el consecutivo de la tabla
        $query = "SELECT Max(a.cod_consec) AS maximo
               FROM " . $base_datos . ".tab_despac_contro a,  
                    " . $base_datos . ".tab_despac_vehige b
              WHERE a.num_despac = b.num_despac AND
                    b.num_despac = " . $regist["despac"] . "
            ";

        $consec = new Consulta($query, $this->conexion);
        $ultimo = $consec->ret_matriz();

        $query = "SELECT if(a.ind_virtua = '1',CONCAT(a.nom_contro,' (Virtual)'),a.nom_contro)
               FROM " . $base_datos . ".tab_genera_contro a
              WHERE a.cod_contro = " . $regist["contro"] . "";

        $consulta = new Consulta($query, $this->conexion);
        $pcontro = $consulta->ret_matriz();

        $query = "SELECT a.nom_noveda
               FROM " . $base_datos . ".tab_genera_noveda a
              WHERE a.cod_noveda = " . $regist["noveda"] . "
            ";

        $consulta = new Consulta($query, $this->conexion);
        $nnoveda = $consulta->ret_matriz();

        $ultimo_consec = $ultimo[0][0];
        $nuevo_consec = $ultimo_consec + 1;

        $query = "SELECT a.cod_rutasx, a.cod_transp
               FROM " . $base_datos . ".tab_despac_vehige a
              WHERE a.num_despac = '" . $regist["despac"] . "'
            ";

        $consulta = new Consulta($query, $this->conexion);
        $rutact = $consulta->ret_matriz();
        $transp = $rutact[0][1];
        $rutact[0][0] = $regist["rutax"];
        if (!$regist["tieadi"])
            $regist["tieadi"] = 0;

        $tiemp_adicis_interf = $regist["tieadi"];
        //captura el cel de la observacion para actualizar el despacho y el tercero
        if ((int) $regist["celular"] != '')
            $cel = (int) $regist["celular"];
        if ($cel)
        {
            $query = "SELECT con_telmov " .
                    "FROM " . $base_datos . ".tab_despac_despac 
                   WHERE num_despac = '" . $regist["despac"] . "'";
            $consulta = new Consulta($query, $this->conexion);
            $num = $consulta->ret_matriz();
            $query = "UPDATE " . $base_datos . ".tab_despac_despac 
                   SET con_telmov = '$cel' 
                   WHERE num_despac = '" . $regist["despac"] . "'";
            $update = new Consulta($query, $this->conexion, "R");
            $regist["observ"] = $regist["observ"] . ".Celular Nuevo: $cel, Celular Anterior " . $num[0][0];
        }
        $query = "SELECT (TIME_TO_SEC(TIMEDIFF('" . $regist["fecact"] . "', a.fec_alarma))/60)
               FROM " . $base_datos . ".tab_despac_seguim a
              WHERE a.num_despac = " . $regist["despac"] . " AND
                    a.cod_rutasx = " . $rutact[0][0] . " AND
                    a.cod_contro = " . $regist["contro"] . " ";

        $consulta = new Consulta($query, $this->conexion);
        $tiempretras = $consulta->ret_matriz();

        if ($tiempretras[0][0] > 0)
            $tiempretras[0][0] = floor($tiempretras[0][0]);
        else
            $tiempretras[0][0] = 0;

        if ($regist["indsit"] != '1')
            $regist["indsit"] = 0;


        //valida si el nombre del sitio existe
        $query = "SELECT cod_sitiox " .
                "FROM " . $base_datos . ".tab_despac_sitio " .
                "WHERE nom_sitiox = '" . $regist["sitio"] . "'";
        $consulta = new Consulta($query, $this->conexion);
        $sitio = $consulta->ret_matriz();
        if (!$sitio)
        {
            $query = "SELECT MAX(cod_sitiox) " .
                    "FROM " . $base_datos . ".tab_despac_sitio ";
            $consulta = new Consulta($query, $this->conexion);
            $sitio = $consulta->ret_matriz();
            $maxsit = $sitio[0][0] + 1;
            $query = "INSERT INTO " . $base_datos . ".tab_despac_sitio" .
                    "(cod_sitiox,nom_sitiox)VALUES('$maxsit','" . $regist["sitio"] . "') ";
            $insercion = new Consulta($query, $this->conexion, "R");
        }
        else
        {
            $maxsit = $sitio[0][0];
        }

        //Se hacen ajustes para las alarmas cuando la novedad mantiene alarma
        $query = "SELECT fec_manala,fec_ultnov, fec_salida " .
                "FROM " . $base_datos . ".tab_despac_despac
               WHERE num_despac = '" . $regist["despac"] . "' ";
        $consulta = new Consulta($query, $this->conexion);
        $fec_manala = $consulta->ret_matriz();
        $fec_ultnov = "'" . $fec_manala[0][1] . "'";
        $fec_salida = "'" . $fec_manala[0][2] . "'";
        $fec_manala = "'" . $fec_manala[0][0] . "'";
        $query = "SELECT ind_manala, ind_alarma, nom_noveda, nov_especi, ind_notsup " .
                "FROM " . $base_datos . ".tab_genera_noveda
               WHERE cod_noveda = '" . $regist["noveda"] . "' ";
        $consulta = new Consulta($query, $this->conexion);
        $ind_manala = $consulta->ret_matriz();
        $alarma = $ind_manala[0][1]."<br>";
        $especi = $ind_manala[0][3];
        $nove = $ind_manala[0][2];
        $ind_notsup = $ind_manala[0][4]; //Jorge 2012-11-08
        $ind_manala = $ind_manala[0][0];
        
        $query = "(SELECT tiem_duraci,a.fec_contro
            FROM  " . BASE_DATOS . ".tab_despac_contro a
             WHERE a.num_despac = " . $regist["despac"] . " )
             UNION
            (SELECT tiem_duraci,a.fec_noveda
            FROM  " . BASE_DATOS . ".tab_despac_noveda a 
             WHERE a.num_despac = " . $regist["despac"] . " )
            ORDER BY 2 DESC
            LIMIT 1
          ";
        $consulta = new Consulta($query, $this->conexion);
        $valretras = $consulta->ret_matriz();
        $valretras = $valretras[0][0];

        if ($ind_manala == '0')
        {
            $fec_manala = "NULL";
        }
        else
        {
            if ($fec_manala == "''")
            {
                if ($fec_ultnov == '' || $fec_ultnov == "''")
                {
                    $fec_manala = $fec_salida;
                }
                else
                {
                    $fec_manala = $fec_ultnov;
                }
            }
            if ($valretras == '')
                $valretras = 0;
            $regist["tieadi"] = $valretras;
        }

        if ($regist["noveda"] == CONS_NOVEDA_ACAFAR)
            $regist["observ"] .= ".Vuelve el Vehiculo a Monitoreo Faro. ";
        
        
        if ($alarma == 'S' || $especi == 1 || $ind_notsup == 1 ) 
        {
            $query = "SELECT a.cod_tercer,a.abr_tercer ,b.num_placax, c.con_telmov, d.abr_tercer AS nom_conduc, c.cod_manifi, 
                            (SELECT UPPER(nom_ciudad) FROM ".$base_datos.".tab_genera_ciudad WHERE cod_ciudad = c.cod_ciuori) AS nom_ciuori,
                            (SELECT UPPER(nom_ciudad) FROM ".$base_datos.".tab_genera_ciudad WHERE cod_ciudad = c.cod_ciudes) AS nom_ciudes
                  FROM " . $base_datos . ".tab_tercer_tercer a,
  										 " . $base_datos . ".tab_despac_vehige b,
  										 " . $base_datos . ".tab_despac_despac c,
  										 " . $base_datos . ".tab_tercer_tercer d
                	WHERE b.num_despac = '" . $regist["despac"] . "' 
                    AND b.num_despac = c.num_despac
                    AND b.cod_conduc = d.cod_tercer
  									AND a.cod_tercer = b.cod_transp";
                   
                    
            $empre = new Consulta($query, $this->conexion);
            
            $empre = $empre->ret_matriz();
            $query = "SELECT CONCAT(c.dir_dispos,'@',b.dns_operad) AS email
            		FROM " . BD_STANDA . ".tab_mensaj_operad b,                                                                                    
            			   " . BD_STANDA . ".tab_mensaj_dispos c
            		WHERE c.cod_operad = b.cod_operad
            		      AND c.cod_transp = b.cod_transp
            	        AND c.cod_operad = b.cod_operad
            	        AND c.ind_novala = 1
            	        AND c.ind_estado = 1
            	        AND b.cod_transp = '" . $empre[0][0] . "'";
            $consulta = new Consulta($query, $this->conexion);
            $correos = $consulta->ret_matriz();
            $dir = '';
            foreach ($correos AS $correo)
            {
                $dir .= $correo[0] . ",";
            }
            $NotifiAgenci = "SELECT ind_notage
                               FROM tab_transp_tipser
                              WHERE cod_transp = '".$empre[0][0]."' AND
                                    num_consec = (SELECT MAX(num_consec) FROM tab_transp_tipser WHERE cod_transp = '".$empre[0][0]."')";
            $consulta = new Consulta($NotifiAgenci, $this->conexion);
            $ind_notage = $consulta->ret_matriz();
            
            //CORREO NOTIFICA SUPERVISOR. Jorge 2012-11-08
            if ($ind_notsup == 1)
            {
                $NIT_TRANSPORTADORA = $empre[0][0];

                $info .="EMPRESA: " . $empre[0][1] . " \n";
                $info .="DESPACHO: " . $regist["despac"] . " \n";
                $info .="MANIFIESTO: " . $empre[0]['cod_manifi'] . " \n";
                $info .="PLACA DEL VEHICULO: " . $empre[0][2] . " \n";
                $info .="CONDUCTOR: " . $empre[0]['nom_conduc'] . " \n";
                $info .="TELEFONO CONDUCTOR: " . $empre[0]['con_telmov'] . " \n";
                $info .="SITIO DE SEGUIMIENTO: " . $regist["sitio"] . " \n";
                $info .="FECHA DE LA NOVEDAD: " . $regist["fecnov"] . " \n";
                $info .="NOVEDAD: " . $nove . " \n";
                $info .="USUARIO: " . $regist["usuari"] . " \n";
                $info .="OBSERVACION:  \n";
                $info .=" " . $regist["observ"] . " \n";

                $asunto = "NOTIFICACION SUPERVISOR";

                mail(MAIL_SUPERVISORES, $asunto, $info, 'From: supervisores@eltransporte.org');
            }
            //CORREO NOVEDAD ESPECIAL.
            if ($especi == 1)
            {
                $NIT_TRANSPORTADORA = $empre[0][0];

                $info .="EMPRESA: " . $empre[0][1] . " \n";
                $info .="DESPACHO: " . $regist["despac"] . " \n";
                $info .="No MANIFIESTO: " . $empre[0]['cod_manifi'] . " \n";
                $info .="ORIGEN: " . $empre[0]['nom_ciuori'] . " \n";
                $info .="DESTINO: " . $empre[0]['nom_ciudes'] . " \n";
                $info .="PLACA DEL VEHICULO: " . $empre[0][2] . " \n";
                $info .="CONDUCTOR: " . $empre[0]['nom_conduc'] . " \n";
                $info .="TELEFONO CONDUCTOR: " . $empre[0]['con_telmov'] . " \n";
                $info .="SITIO DE SEGUIMIENTO: " . $regist["sitio"] . " \n";
                $info .="FECHA DE LA NOVEDAD: " . $regist["fecnov"] . " \n";
                $info .="Novedad: " . $nove . " \n";
                if( $regist["noveda"] == '70' )
                {
                    $tieultima = "Tiempo Ultima Novedad: " . $regist["tie_ultnov"] . " Minutos \n";//Jorge 120404
                    $info .= $tieultima;
                }
                $info .="USUARIO: " . $regist["usuari"] . " \n";
                $info .="OBSERVACION:  \n";
                $info .=" " . $regist["observ"] . " \n";

                $asunto = "NOVEDAD ESPECIAL";
                $mEmailAgenci = $this -> getEmailAgecni ($regist["despac"]);
                
                if($ind_notage[0][0] == 1)
                {
                  $mEmailAgenci = $this -> getEmailAgecni ($regist["despac"]);
                  if($mEmailAgenci)
                      $dir = $mEmailAgenci;                  
                }
                if ( $NIT_TRANSPORTADORA != "900419270" ) 
                  mail("$dir", $asunto, $info, 'From: supervisores@eltransporte.org'); 
          
                $no_correos = array('9996','9995','111',
                                      '68','50','53','70',
                                      '74','126','52','127',
                                      '96','97','1','28','14',
                                      '27','60','13','62','64',
                                      '49','12','41','9','4',
                                      '124','22','117','99');
                if ($NIT_TRANSPORTADORA == "900419270" && !in_array( $regist["noveda"], $no_correos) )//SI ES "K Logistics" OJO REEMPLAZAR EN EL IF -> 900419270.
                {
                    // subject
                    $titulo = 'NOVEDAD EN RUTA';
					
					$cod_ruta = $rutact[0][0];
					
					$select = "SELECT CONCAT(b.nom_ciudad,' - ', c.nom_ciudad), a.nom_rutasx, a.cod_ciuori, a.cod_ciudes  
							   FROM " . $base_datos . ".tab_genera_rutasx a,
							        " . $base_datos . ".tab_genera_ciudad b,
							        " . $base_datos . ".tab_genera_ciudad c
							   WHERE a.cod_rutasx = '$cod_ruta' 
                                 AND a.cod_paiori = b.cod_paisxx 
                                 AND a.cod_depori = b.cod_depart 
                                 AND a.cod_ciuori = b.cod_ciudad
                                 AND a.cod_paides = c.cod_paisxx 
                                 AND a.cod_depdes = c.cod_depart 
                                 AND a.cod_ciudes = c.cod_ciudad                                 
                               ";
					
					$consulta = new Consulta( $select, $this->conexion);
					$rutaXXX = $consulta -> ret_matriz();
					$rutaXXX = $rutaXXX[0][0];
					
					$rutaXXX = str_replace( "Col", "" , $rutaXXX );
					
					$select = "SELECT b.nom_carroc, b.cod_carroc 
								   FROM ".$base_datos.".tab_vehicu_vehicu a,
										".$base_datos.".tab_vehige_carroc b
								   WHERE a.num_placax = '".$empre[0][2]."' AND
										 b.cod_carroc = a.cod_carroc ";
						
					$consulta = new Consulta( $select, $this->conexion);
					$vehicul = $consulta -> ret_matriz();
					//$vehicul = $vehicul[0][0];
					
          $via = explode( "VIA", $rutaXXX );
                    
					$date = explode( " ", $regist["fecnov"] );					
					$fech_nov = str_replace( "-", "/", $date[0] ) ;
					$hora_nov = $date[1];
					
					$fech_nov = explode( "/", $fech_nov );
					$fech_nov = $fech_nov[2]."/".$fech_nov[1]."/".$fech_nov[0];						

                    // message
                    $mensaje = '
							<div id="mensaje" style="color:#7F7F7F; border:0px; padding:0px; width:700px; text-align:justify;">
							
								<img src="https://ap.intrared.net:444/ap/satt_standa/imagenes/klogis/image001.jpg">
								
								<div style="color:#7F7F7F; font-family: Arial, sans-serif; font-size: 12pt;">
									<div style="padding:20px" >
											<strong>Fecha </strong> ' . $fech_nov . '<br/>
											<strong>Hora Novedad </strong> ' . $hora_nov . '<br/><br/>
											
											<strong>Conductor </strong> ' .ucwords( strtolower( $empre[0]['nom_conduc'] ) ) . '<br/>
											'.( (int)$vehicul[0][1]==0 ? '' : '<strong>Veh&iacute;culo </strong> '.ucwords( strtolower($vehicul[0][0])).'<br/>' ).'
											<strong>Tel&eacute;fono </strong> ' . ucwords( strtolower($empre[0]['con_telmov'])) . '<br/>
											<strong>Placa </strong> ' . strtoupper($empre[0][2]) . '<br/><br/>											
											<strong>Ruta </strong> ' . ucwords( strtolower($rutaXXX)) . '<br/>';
                      if( $via[1] )
                        $mensaje .= '<strong>Via </strong> ' . ucwords( strtolower($via[1])) . '<br/>';
                      
                      $mensaje.= '<strong>Despacho No </strong> ' . ucwords( strtolower($regist["despac"])) . '<br/><strong>Puesto de Control </strong> ' . ucwords( strtolower($regist["sitio"])) . '<br/><br/>
											
											<strong>NOVEDAD: </strong> ' . strtolower( $nove ) . '<br/><br/>
									</div>
									<img src="https://ap.intrared.net:444/ap/satt_standa/imagenes/klogis/image002.jpg">
								</div>
								
							</div>';

                    // Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
                    $cabeceras = 'MIME-Version: 1.0' . "\r\n";
                    $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                    // Cabeceras adicionales
                    $cabeceras .= 'From: no-reply@intrared.net' . "\r\n";
					
                    $dir .= ", miguel.chavez@klogistics.com.co";
                    // Mail it
                    //mail($dir, $titulo, $mensaje, $cabeceras);
                    //mail('jorge.preciado@intrared.net,leidy.acosta@intrared.net', $titulo, $mensaje, $cabeceras);
                }
            }
            

            $regist["observ"] .= " . Se Envio Correos a : " . $dir.' '.$tieultima;
        }


        //inserta la novedad
        $query = "INSERT INTO " . $base_datos . ".tab_despac_contro
              ( num_despac,cod_rutasx,cod_contro,cod_consec,
                obs_contro,cod_noveda,tiem_duraci,fec_contro,
                val_retras,cod_sitiox,ind_sitiox,usr_creaci,fec_creaci,usr_modifi,
                fec_modifi )
             VALUES ( " . $regist["despac"] . "," . $rutact[0][0] . "," . $regist["contro"] . "," . $nuevo_consec . ",
                     '" . $regist["observ"] . "'," . $regist["noveda"] . "," . $regist["tieadi"] . ",'" . $regist["fecnov"] . "',
                      " . $tiempretras[0][0] . ",'" . $maxsit . "','" . $regist["indsit"] . "','" . $regist["usuari"] . "','" . $regist["fecact"] . "','" . $regist["usuari"] . "',
                     '" . $regist["fecact"] . "') ON DUPLICATE KEY UPDATE fec_creaci = DATE_ADD( VALUES(fec_creaci), INTERVAL 1 SECOND )";
         
         
         $insercion = new Consulta($query, $this->conexion, "R");
        

        $query = "UPDATE " . $base_datos . ".tab_despac_despac 
             SET fec_ultnov = '" . $regist["fecnov"] . "',
                 fec_manala = " . $fec_manala . ",
                 usr_ultnov = '" . $regist["usuari"] . "',
                 cod_conult = '" . $regist["contro"] . "',";
        if ($regist["noveda"] == CONS_NOVEDA_ACAEMP)
            $query .=" ind_defini = '1', ";
        if ($regist["noveda"] == CONS_NOVEDA_ACAFAR)
            $query .=" ind_defini = '0', ";
        $query.=" cod_consec = " . $nuevo_consec . "
             WHERE num_despac = '" . $regist["despac"] . "'";
        $update = new Consulta($query, $this->conexion, "R");
      
        
        //manda el correo ala empresa y al usuario en las novedades de acargo empresa y acargo faro
        if ($regist["noveda"] == CONS_NOVEDA_ACAEMP || $regist["noveda"] == CONS_NOVEDA_ACAFAR)
        {
            $query = "SELECT b.dir_emailx
  		      FROM " . BASE_DATOS . ".tab_despac_vehige a,
                 " . BASE_DATOS . ".tab_tercer_tercer b
  		     WHERE a.cod_transp = b.cod_tercer
                 AND a.num_despac = " . $regist["despac"] . "";
            $consulta = new Consulta($query, $this->conexion);
            $email = $consulta->ret_matriz();
            $email = $email[0][0];
            $dir_email = $regist["email"];
            if ($email != "")
                $dir_email .= ",$email";
            $query = "SELECT b.num_placax 
                FROM " . $base_datos . ".tab_despac_vehige b
              	WHERE b.num_despac = " . $regist["despac"] . "
            ";
            $datos = new Consulta($query, $this->conexion);
            $datos = $datos->ret_matriz();
            $query = "SELECT a.abr_tercer 
                FROM " . $base_datos . ".tab_tercer_tercer a,
										 " . $base_datos . ".tab_despac_vehige b
              	WHERE b.num_despac = '" . $regist["despac"] . "' 
											AND a.cod_tercer = b.cod_transp";
            $empre = new Consulta($query, $this->conexion);
            $empre = $empre->ret_matriz();
            $info .="EMPRESA: " . $empre[0][0] . " \n";
            $info .="DESPACHO: " . $regist["despac"] . " \n";
            $info .="PLACA DEL VEHICULO: " . $datos[0][0] . " \n";
            $info .="SITIO DE SEGUIMIENTO: " . $regist["sitio"] . " \n";
            $info .="FECHA DE LA NOVEDAD: " . $regist["fecnov"] . " \n";
            $info .="USUARIO: " . $regist["usuari"] . " \n";
            $info .="OBSERVACION:  \n";
            $info .=" " . $regist["observ"] . " \n";
            if ($regist["noveda"] == CONS_NOVEDA_ACAEMP)
                $asunto = "NOVEDAD A Cargo de la Empresa";
            else
                $asunto = "NOVEDAD A Cargo de Faro";

            //$dir_email .= ", engmiguelgarcia@gmail.com"; //COMENTARIAR THIS
            mail("$dir_email", $asunto, $info, 'From: supervisores@eltransporte.org');
        }

        $query = "SELECT cod_contro
            FROM " . $base_datos . ".tab_despac_seguim
            WHERE num_despac = '" . $regist["despac"] . "' AND
                  cod_rutasx = '" . $rutact[0][0] . "'
            ORDER by fec_planea";
        $consulta = new Consulta($query, $this->conexion);
        $seguim = $consulta->ret_matriz();
        for ($i = 0; $i < sizeof($seguim); $i++)
        {
            if ($regist["contro"] == $seguim[$i][0])
            {
                break;
            }
            else
            {
                $query = "UPDATE " . $base_datos . ".tab_despac_seguim
                SET ind_estado = '0'
              WHERE num_despac = '" . $regist["despac"] . "' AND
                    cod_rutasx = '" . $rutact[0][0] . "' AND
                    cod_contro = '" . $seguim[$i][0] . "' ";
                $consulta = new Consulta($query, $this->conexion, "R");
            }
        }


        $tieAdi2 = (int) $regist["tieadi"] + 30;
        $query = "SELECT a.fec_planea
		 FROM " . $base_datos . ".tab_despac_seguim a
		WHERE a.num_despac = " . $regist["despac"] . " AND
		      a.cod_rutasx = " . $rutact[0][0] . " AND
		      a.cod_contro = " . $regist["contro"] . "
	      ";
        $consulta = new Consulta($query, $this->conexion);
        $planru_c = $consulta->ret_matriz();

        if ($ind_manala == '0')
        {
            $query = "UPDATE " . $base_datos . ".tab_despac_seguim
		  SET fec_alarma = '" . $regist["fecnov"] . "',
		      usr_modifi = '" . $regist["usuari"] . "',
		      fec_modifi = '" . $regist["fecact"] . "'
		WHERE num_despac = " . $regist["despac"] . " AND
		      cod_rutasx = " . $rutact[0][0] . " AND
		      cod_contro = " . $regist["contro"] . "
	      ";
            $insercion = new Consulta($query, $this->conexion, "R");
        }
        //trae el plan de ruta a actualizar
        $query = "SELECT a.cod_contro,(TIME_TO_SEC(TIMEDIFF(a.fec_planea,'" . $planru_c[0][0] . "'))/60)
		 FROM " . $base_datos . ".tab_despac_seguim a
		WHERE a.num_despac = " . $regist["despac"] . " AND
		      a.cod_rutasx = " . $rutact[0][0] . " AND
		      a.fec_planea > '" . $planru_c[0][0] . "'
		      ORDER BY a.fec_planea
	      ";
        $consulta = new Consulta($query, $this->conexion);
        $planru_p = $consulta->ret_matriz();

        $tiemacu = 0;
        if ($ind_manala == '0')
            for ($i = 0; $i < sizeof($planru_p); $i++)
            {
                $tiemacu = $planru_p[$i][1] + $tieAdi2;

                $query = "UPDATE " . $base_datos . ".tab_despac_seguim
		   SET fec_alarma = DATE_ADD('" . $regist["fecnov"] . "', INTERVAL " . $tiemacu . " MINUTE),
		       usr_modifi = '" . $regist["usuari"] . "',
		       fec_modifi = '" . $regist["fecact"] . "'
		 WHERE num_despac = " . $regist["despac"] . " AND
		       cod_rutasx = " . $rutact[0][0] . " AND
		       cod_contro = " . $planru_p[$i][0] . "
	       ";
                $insercion = new Consulta($query, $this->conexion, "R");
            }

        if ($regist["noveda"] == '9998')
        {
            $query = "UPDATE " . $base_datos . ".tab_despac_seguim
                  SET ind_estado = '0'
                WHERE num_despac = '" . $regist["despac"] . "' AND
                      cod_rutasx = '" . $rutact[0][0] . "'  ";
            $consulta = new Consulta($query, $this->conexion, "R");
        }

        $query = "UPDATE " . $base_datos . ".tab_despac_vehige
                SET fec_llegpl = DATE_ADD('" . $planrut[sizeof($planrut) - 1][1] . "', INTERVAL " . $tiemacu . " MINUTE),
                    usr_modifi = '" . $regist["usuari"] . "',
                    fec_modifi = '" . $regist["fecact"] . "'
              WHERE num_despac = " . $regist["despac"] . " ";

        $insercion = new Consulta($query, $this->conexion, "R");

        $query = "SELECT MAX(e.fec_noveda),e.cod_contro
             FROM " . $base_datos . ".tab_despac_vehige c,
                  " . $base_datos . ".tab_despac_seguim d,
                  " . $base_datos . ".tab_despac_noveda e
            WHERE d.num_despac = c.num_despac AND
                  d.cod_rutasx = c.cod_rutasx AND
                  e.num_despac = d.num_despac AND
                  e.cod_rutasx = d.cod_rutasx AND
                  c.num_despac = '" . $regist["despac"] . "'
            GROUP BY e.cod_contro 
            ORDER BY e.fec_noveda DESC ";

        $consulta = new Consulta($query, $this->conexion);
        $pc_noveda = $consulta->ret_arreglo();

        $printmenerr = 0;

        if ($ominter != 2)
        {
            //Manejo de la Interfaz Aplicaciones SAT
            $interfaz = new Interfaz_SAT($base_datos, $regist["nittra"], $regist["usuari"], $this->conexion);

            if ($ominter != 2)
            {

                if ($interfaz->totalact > 0)
                {
                    $a = 0;
                    for ($i = 0; $i < $interfaz->totalact; $i++)
                    {
						$nit_empres = $regist["nittra"];
						
                        if (50 == $interfaz->interfaz[$i]["operad"])
                        {
							            $error_ = NULL; 
							//mail( "engmiguelgarcia@gmail.com", "ok", BD_STANDA ); //COMENTARIAR THIS
							
							//CONSULTAR URL WSDL.
							$query = "SELECT a.url_webser	
									  FROM ".BD_STANDA.".tab_genera_server a,
										   ".$base_datos.".tab_transp_tipser b
									  WHERE a.cod_server = b.cod_server AND
											b.cod_transp = '$nit_empres' 
									  ORDER BY b.fec_creaci DESC ";

							$consulta = new Consulta( $query, $this -> conexion );
							$url_webser = $consulta -> ret_matriz();
							$url_webser =  $url_webser[0][0];//URL DEL WSDL.
							
							//$oSoapClient = new soapclient( $url_webser, true );//CLIENTE WEBSERVICES.
							$regist['server'] = $url_webser;
							//$caido = $oSoapClient->getError();
							//print_r( $caido );//MOSTRAR ERRORES.
							

                            ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                            try
                            {
                                $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );

                                //Métodos disponibles en el WS
                                $mResult = $oSoapClient -> __call( 'aplicaExists', array( "nom_aplica" => $interfaz -> interfaz[$i]["nombre"] ) );

                                $mResult = explode("; ", $mResult);
                                $mCodResp = explode(":", $mResult[0]);
                                $mMsgResp = explode(":", $mResult[1]);

                                if ("1000" != $mCodResp[1])
                                {
                                    $error_ = $mMsgResp[1];
                                }                                   
                            }
                            catch( SoapFault $e )
                            {
                                $error_ = $e -> getMessage();
                            }


							//$oSoapClient->soap_defencoding = 'ISO-8859-1';
							//$mResult = $oSoapClient -> call( "aplicaExists", 
							//			array( "nom_aplica" => $interfaz -> interfaz[$i]["nombre"] ) );
									
							//print_r( $mResult );
							
							
							/*if ($oSoapClient->fault)
							{
								//Notifica Fallos
								$error_ = $oSoapClient->faultcode . ':' . $oSoapClient->faultdetail . ':' . $oSoapClient->faultstring;
							}
							else
							{
								$err = $oSoapClient->getError();
								if ($err)
								{
									// Notifica errores
									$error_ = $err;
								}
								else
								{
									//Procesa el resultado del WS
									$mResult = explode("; ", $mResult);
									$mCodResp = explode(":", $mResult[0]);
									$mMsgResp = explode(":", $mResult[1]);

									if ("1000" != $mCodResp[1])
									{
										$error_ = $mMsgResp[1];
									}									
								}
							}*/
								
                            $query = "SELECT a.cod_manifi, b.num_placax " .
                                    "FROM " . $base_datos . ".tab_despac_despac a, " .
                                    "" . $base_datos . ".tab_despac_vehige b " .
                                    "WHERE a.num_despac = b.num_despac " .
                                    "AND a.num_despac = '" . $regist["despac"] . "' ";
                            $consulta = new Consulta($query, $this->conexion);
                            $mSalida = $consulta->ret_matriz();

                            $mQuerySelNomNov = "SELECT nom_noveda, ind_alarma, ind_tiempo, nov_especi, ind_manala  " .
                                    "FROM " . $base_datos . ".tab_genera_noveda " .
                                    "WHERE cod_noveda = '" . $regist["noveda"] . "' ";

                            $consulta = new Consulta($mQuerySelNomNov, $this->conexion);
                            $mNomNov = $consulta->ret_matriz();

                            $mQuerySelNomPc = "SELECT nom_contro " .
                                    "FROM " . $base_datos . ".tab_genera_contro " .
                                    "WHERE cod_contro = '" . $regist["contro"] . "' ";

                            $consulta = new Consulta($mQuerySelNomPc, $this->conexion);
                            $mNomPc = $consulta->ret_matriz();

                            $mQuerySelPcxbas = "SELECT cod_pcxbas 
                                   FROM " . $base_datos . ".tab_homolo_trafico 
                                  WHERE cod_transp = '" . $regist["nittra"] . "'
                                    AND cod_pcxfar = '" . $regist["contro"] . "'
                                    AND cod_rutfar = '" . $regist["rutax"] . "'
                                  ";

                            $consulta = new Consulta($mQuerySelPcxbas, $this->conexion);
                            $mCodPcxbas = $consulta->ret_matriz();

                            $parametros = array("nom_usuari" => $interfaz->interfaz[$i]["usuari"],
                                "pwd_clavex" => $interfaz->interfaz[$i]["passwo"],
                                "nom_aplica" => $interfaz->interfaz[$i]["nombre"],
                                "num_manifi" => $mSalida[0]['cod_manifi'],
                                "num_placax" => $mSalida[0]['num_placax'],
                                "cod_novbas" => 0,
                                "cod_conbas" => $mCodPcxbas[0][0],
                                "tim_duraci" => $regist["tieadi"],
                                "fec_noveda" => date('Y-m-d H:i', strtotime($regist["fecnov"])),
                                "des_noveda" => $regist["observ"],
                                "nom_contro" => $mNomPc[0][0],
                                "nom_sitiox" => substr($regist["sitio"], 0, 50),
                                "cod_confar" => NULL,
                                'cod_novfar' => $regist['noveda'],
                                'nom_noveda' => $mNomNov[0]['nom_noveda'],
                                'ind_alarma' => $mNomNov[0]['ind_alarma'],
                                'ind_tiempo' => $mNomNov[0]['ind_tiempo'],
                                'nov_especi_' => $mNomNov[0]['nov_especi'],
                                'ind_manala' => $mNomNov[0]['ind_manala']
                            );
							
                            if (!$error_)
                            {
                                for( $index1 = 0; $index1 < count( $mCodPcxbas ); $index1++ )
                                {
                                  $parametros['cod_conbas'] = $mCodPcxbas[$index1][0];
                                  //Consumo Web Service.
                                  /********************* TRATAMIENTO SOAP *********************/
                                  try
                                  {
                                      //Métodos disponibles en el WS
                                      $respuesta = $oSoapClient -> __call( 'setNovedadNC', $parametros );

                                      $mResult = explode("; ", $respuesta);
                                      $mCodResp = explode(":", $mResult[0]);
                                      $mMsgResp = explode(":", $mResult[1]);

                                      if ("1000" != $mCodResp[1])
                                      {
                                          $error_ = $mMsgResp[1];
                                      }
                                      else
                                      {
                                          $error_ = NULL;
                                          break;
                                      }
                                  }
                                  catch( SoapFault $e )
                                  {
                                      $error_ = $e -> getMessage();
                                   }
                                }
                                if ($error_ != NULL)
                                {
                                    echo "<div align='center' style='color:#000000; padding:5px; font-size:14px; background-color:#FFAFAF; border:2px solid #000000;'><b>Error en Webservice - Insertar novedad en " . $interfaz->interfaz[$i]["nombre"] . ':' . $error_ . "</b></div>";
                                    $mMessage = "******** Encabezado ******** \n";
                                    $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                                    $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                    $mMessage .= "Aplicacion: " . $interfaz->interfaz[$i]["nombre"] . " \n";
                                    $mMessage .= "Numero de despacho SAT: " . $regist["despac"] . " \n";
                                    $mMessage .= "Placa del vehiculo: " . $mSalida[0][1] . " \n";
                                    $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " \n";
                                    $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                    $mMessage .= "Nombre novedad: " . $mNomNov[0]['nom_noveda'] . " \n";
                                    $mMessage .= "******** Detalle ******** \n";
                                    $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                                    $mMessage .= "Mesaje de error: " . $error_ . " \n";

                                    $novedaError['cod_respon'] = $e -> faultcode;
                                    $novedaError['msg_respon'] = $error_;
                                    $novedaError['det_respon'] = $mMessage;
                                    //Se registran errores de la interfaz en la BD
                                    $this->setNovedadError($parametros, $regist, $novedaError, 'nc');

                                    //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
                                    mail("hugo.malagon@intrared.net", "Web service TRAFICO - SAT - NOVEDAD NC", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                    //mail( "supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Web service TRAFICO - SAT", $mMessage,'From: soporte.ingenieros@intrared.net' );
                                }
                                /********************* **************** *********************/
                            }
                            else
                            {
                              $novedaError['cod_respon'] = '';
                              $novedaError['msg_respon'] = '';
                              $novedaError['det_respon'] = $error_;
                              
                              $this->setNovedadError($parametros, $regist, $novedaError, 'pc');
                              echo "<div align='center' style='color:#000000; padding:5px; font-size:14px; background-color:#FFAFAF; border:2px solid #000000;'><b>Error en Webservice - Insertar novedad en " . $interfaz->interfaz[$i]["nombre"] . ':' . $error_ . "</b></div>";
                            }
                        }
						
                            // NELSON, modificado por Hugo Novedades para puestos virtuales
                            if (51 == $interfaz->interfaz[$i]["operad"])
                            {
                                $error_ = NULL;
                                $query = "SELECT  a.num_placax, a.fec_salipl, b.cod_manifi " .
                                        "FROM " . $base_datos . ".tab_despac_vehige a, " .
                                        "" . $base_datos . ".tab_despac_despac b " .
                                        "WHERE a.num_despac = '" . $regist["despac"] . "' " .
                                        "AND a.num_despac = b.num_despac ";

                                $consulta = new Consulta($query, $this->conexion);
                                $mSalida = $consulta->ret_matriz();

                                //Consulta de Detalle de Novedad
                                $query = "SELECT  a.nom_noveda
                                        FROM " . $base_datos . ".tab_genera_noveda a 
                                        WHERE a.cod_noveda = " . $regist["noveda"];

                                $consulta = new Consulta($query, $this->conexion);
                                $mNoveda = $consulta->ret_matriz();

                                //Consulta del puesto Padre de la homologacion
                                $query = "SELECT  a.cod_contro
                                            FROM ".$base_datos.".tab_homolo_pcxeal a 
                                           WHERE a.cod_homolo = '".$regist["contro"]."'
                                             OR a.cod_contro = '".$regist["contro"]."'";

                                $consulta = new Consulta($query, $this->conexion);
                                $mControPadre = $consulta->ret_matriz();


                                /********************* TRATAMIENTO SOAP *********************/
                                ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                                try
                                {   
                                     $mEstado = '1';
                                    //Ruta Web Service Colocar e-com.
                                    $url_webser = "http://www.colombiasoftware.net/base/webservice/reportepuestocontrol.php?wsdl";
                                    $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                    $mFecha[1] = date('H:i:s', strtotime($regist["fecnov"]));
                                    //echo "<pre>";print_r($interfaz);echo "</pre>";
                                    $parametros = array();
                                    $parametros["empresa"]=$interfaz->interfaz[$i]["nombre"];
                                    $parametros["usuario"]=$interfaz->interfaz[$i]["usuari"];
                                    $parametros["clave"]=$interfaz->interfaz[$i]["passwo"];
                                    
                                    //Se envia la novedad sin los codigos internos faro de usuarios que registran la novedad
                                    $pos = strpos($regist["observ"], ">");

                                    if ($pos !== false)
                                    {
                                      $regist["observ"] = substr( $regist["observ"], $pos + 1 );
                                    }

                                    
                                    
                                    $parametros["novedad"] = $mNoveda[0]["nom_noveda"].' '.$regist["observ"];
                                    $parametros["hora"] =$mFecha[1];
                                    $parametros["fecha"] =$mFecha[0];
                                    $parametros["placa"] =$mSalida[0][0];
                                    $parametros["manifiesto_codigo"] =$mSalida[0][2];
                                    $parametros["lugar"] = $regist["sitio"];  
                    
                                    $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                    
                                    //Métodos disponibles en el WS
                                    /*echo "InsertarNovedadNC <br> novedades_humadea <pre>";
                                    print_r($parametros);
                                    echo "</pre>";*/
                                    $respuesta = $oSoapClient -> __call( 'novedades_humadea', $parametros );

                                    //$respuesta = 'OK REPORTO';
                                    if ( $respuesta != 'OK REPORTO.' )
                                    {
                                        $error_ = $respuesta;
                                    }
                                  }
                                catch( SoapFault $e )
                                {
                                    $error_ = $e -> getMessage();
                                }
                                
                                if(  $error_ != NULL )
                                {
                                    $mEstado = '0';
                                    $mMessage = "******** Encabezado ******** \n";
                                    $mMessage .= "Fecha y hora Sistema: " . date("Y-m-d H:i") . " \n";
                                    $mMessage .= "Fecha y hora Novedad: " . $mFecha[0].' '.$mFecha[1] . " \n";
                                    $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                    $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                    $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                    $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                    $mMessage .= "Nombre novedad: " . $mNoveda[0]["nom_noveda"] . " \n";
                                    $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                                    $mMessage .= "******** Detalle ******** \n";
                                    $mMessage .= "Mensaje de error: " . $error_ . " \n";
                                    //echo $mMessage;
                                    mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Error Web service Colombia Software - Colombiasoftware Puestos virtuales", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                    //mail("hugo.malagon@intrared.net", "Error Web service Humadea - Colombiasoftware", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                }
                                 //Insert de Bitácora 
                                 $mLog = array();
                                 $mLog['mEstado'] = $mEstado;
                                 $mLog['mResult'] = $respuesta;
                                 $mLog['mUsuari'] = $regist["usuari"];
                                 $mLog['mNumDes'] = $regist["despac"];
                                 
                                 $this -> InsertNovedadHumadea( $parametros , $mLog );
                                
                                /********************* **************** *********************/
                            }
 
                            if (56 == $interfaz->interfaz[$i]["operad"])//transportadora referencia =  800081833 || Trasportadora FW 
                            {
                                $query = "SELECT  a.num_placax, a.fec_salipl, b.cod_manifi " .
                                        "FROM " . $base_datos . ".tab_despac_vehige a, " .
                                        "" . $base_datos . ".tab_despac_despac b " .
                                        "WHERE a.num_despac = '" . $regist["despac"] . "' " .
                                        "AND a.num_despac = b.num_despac ";

                                $consulta = new Consulta($query, $this->conexion);
                                $mSalida = $consulta->ret_matriz();

                                //Consulta de Detalle de Novedad
                                $query = "SELECT  a.cod_fwxxxx
                                        FROM " . $base_datos . ".tab_noveda_tranfw a 
                                        WHERE a.cod_faroxx = " . $regist["noveda"];

                                $consulta = new Consulta($query, $this->conexion);
                                $mNoveda = $consulta->ret_matriz();
                                $mNoveda[0][0] = $mNoveda[0][0] == NULL ? '01' : $mNoveda[0][0];
                                

                                //Consulta del puesto Padre de la homologacion
                                $query = "SELECT  a.cod_contro
                                            FROM ".$base_datos.".tab_homolo_pcxeal a 
                                           WHERE a.cod_homolo = '".$regist["contro"]."'
                                             OR a.cod_contro = '".$regist["contro"]."'";
                                
                                $consulta = new Consulta($query, $this->conexion);
                                $mControPadre = $consulta->ret_matriz();
                                
                                if( !$mControPadre )
                                  $mControPadre[0][0] = $regist["contro"];

                                 //Consulta Sitio antes, en despac_sitio                                
                                 $query = "SELECT CONCAT( b.cod_sitiox, ' - ', b.nom_sitiox ), a.cod_rutasx ".
                                             "FROM ".$base_datos.".tab_despac_contro a, ".$base_datos.".tab_despac_sitio b ".
                                             "WHERE ".
                                             "a.cod_sitiox = b.cod_sitiox ".
                                             "AND a.cod_consec = (SELECT MAX( cod_consec ) FROM ".$base_datos.".tab_despac_contro WHERE num_despac = '".$regist["despac"]."' )".
                                             "AND a.cod_contro = '".$mControPadre[0][0]."'";
                                //$consulta = new Consulta($query, $this->conexion);
                                //$mControSitiox = $consulta->ret_matriz();
                                
                                // -------------------------------**** Calcula los kilómetros desde el último puesto de control reportado y antes del sitio
                                // Trae la ultima hora de PC Reportada
                                
                                $query = "SELECT MAX(DATE_FORMAT(fec_noveda,'%H:%i')) FROM ".$base_datos.".tab_despac_noveda WHERE num_despac = '".$regist["despac"]."'";
                                $consulta = new Consulta($query, $this->conexion);
                                $mLastTimeNovePC = $consulta->ret_matriz();
                                
                                
                                // Trae los datos del puesto de control Tiempo y distancia
                                
                                $query = "SELECT val_duraci, val_distan FROM ".$base_datos.".tab_genera_rutcon WHERE cod_rutasx = '".$mControSitiox[0][1]."' AND cod_contro  = '".$mControPadre[0][0]."'";
                                $consulta = new Consulta($query, $this->conexion);
                                $mDataLastPC = $consulta->ret_matriz();         
                                
                                // Calcula la diferencia en tiempo desde la ultima novedad en PC y el NC                                
                                
                                $query ="SELECT TIMEDIFF( '".date('H:i', strtotime($regist["fecnov"]))."', '".$mLastTimeNovePC[0][0]."' )";                               
                                $consulta = new Consulta($query, $this->conexion);
                                $mDiffPC_NC = $consulta->ret_matriz();
                                
                                
                                // CAlcula la distancia que le queda desde el ultimo contros (Antes) haste el siguiente PC                                
                                $mTimer = explode(":",$mDiffPC_NC[0][0] );                                
                                $mHours = $mTimer[0];
                                $mMinut = $mTimer[1];
                                
                                $mFullTime = ($mHours * 60) + ($mMinut); 
                                
                                $mPasoOne = ($mFullTime) * ($mDataLastPC[0][1]);
                                //$mFinalDinstace = ( $mPasoOne) / ($mDataLastPC[0][0]) ;
                                //********************* TRATAMIENTO SOAP *********************
                                ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                                try
                                {
                                    //Ruta Web Service Colocar e-com.
                                    $url_webser = "http://50.28.44.212/~forward/server/seguimiento.php?wsdl";
                                    $mFecha[0] = date('Y-m-d H:i', strtotime($regist["fecnov"]));
                                    
                                    $parametros = array();
                                    $parametros["usuario"]=$interfaz->interfaz[$i]["usuari"];
                                    $parametros["clave"]=$interfaz->interfaz[$i]["passwo"];
                                    $parametros["fecha"] = $mFecha[0];
                                    $parametros["punto_control"] =substr( $regist["sitio"], 0, 30 );
                                    $parametros["distancia_pc"] = $mFinalDinstace;
                                    $parametros["motivo"]= $mNoveda[0][0];
                                    $parametros["referencia"] = $mControSitiox[0][0];
                                    $parametros["placa"] =$mSalida[0][0];
                                   
                                    echo "<pre>";print_r( $parametros ); echo "</pre>";
                                    $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                    
                                    //Métodos disponibles en el WS
                                    $respuesta = $oSoapClient -> __call( 'reporte', $parametros );

                                    echo "<pre>"; print_r( $respuesta ); echo "</pre>";
                                    
                                    if ( $respuesta != 'Ok' )
                                    {
                                        $mMessage = "******** Encabezado ******** \n";
                                        $mMessage .= "Fecha y hora Sistema: " . date("Y-m-d H:i") . " \n";
                                        $mMessage .= "Fecha y hora Novedad: " . $mFecha[0].' '.$mFecha[1] . " \n";
                                        $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                        $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                        $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                        $mMessage .= "Codigo puesto de control del despacho: " . $regist["contro"] . " \n";
                                        $mMessage .= "Codigo puesto de control padre: " . $mControPadre[0][0] . " \n";
                                        $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                        $mMessage .= "Nombre novedad: " . $mNoveda[0]["nom_noveda"] . " \n";
                                        $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                                        $mMessage .= "******** Detalle ******** \n";
                                        $mMessage .= "Mensaje de error: " . $respuesta . " \n";
                                        //echo $mMessage;
                                        mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Error Web service Trans FW - SISTEMA ARGO", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                        //mail("hugo.malagon@intrared.net", "Error Web service Humadea - Colombiasoftware", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                    }
                                  }
                                catch( SoapFault $e )
                                {
                                  
                                    $error_ = $e -> getMessage();
                                    echo "<pre>";
                                    print_r( $error_ );
                                    echo "</pre>";
                                }
                                //********************* **************** ********************
                            }
                            
                            if(57 == $interfaz->interfaz[$i]["operad"])
                            {
                              $query = "SELECT a.cod_manifi, b.num_placax, a.cod_tipdes " .
                                    "FROM " . $base_datos . ".tab_despac_despac a, " .
                                    "" . $base_datos . ".tab_despac_vehige b " .
                                    "WHERE a.num_despac = b.num_despac " .
                                    "AND a.num_despac = '" . $regist["despac"] . "' ";
                              $consulta = new Consulta($query, $this->conexion);
                              $mSalida = $consulta->ret_matriz();
                              
                              //Consulta del puesto Padre de la homologacion
                              $query = "SELECT  a.cod_contro
                                          FROM ".$base_datos.".tab_homolo_pcxeal a 
                                         WHERE a.cod_homolo = '".$regist["contro"]."'
                                           OR a.cod_contro = '".$regist["contro"]."'";
                              $consulta = new Consulta($query, $this->conexion);
                              $mControPadre = $consulta->ret_matriz();
                              if( !$mControPadre )
                                $mControPadre[0][0] = $regist["contro"];
                                          
                              // Consulta Nombre PC
                              $query = "SELECT  a.nom_contro, IF(a.ind_virtua = 1, NULL,'a.cod_contro' ) AS cod_puecon
                                          FROM ".$base_datos.".tab_genera_contro a 
                                         WHERE a.cod_contro = '".$mControPadre[0][0]."'";
                              $consulta = new Consulta($query, $this->conexion);
                              $mNomContro = $consulta->ret_matriz();
                              
                              // Consulta el Nombre de la Novedad registrada
                              $query = "SELECT  a.nom_noveda
                                          FROM ".$base_datos.".tab_genera_noveda a 
                                         WHERE a.cod_noveda = '".$regist["noveda"]."'";
                              $consulta = new Consulta($query, $this->conexion);
                              $mNomNoveda = $consulta->ret_matriz();
                       
                              //Interfaz Transportes GMT Onlinetools   // Prueba Nit 800081833  NIT ORIGINAL ->  8301019592
                              ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                              try
                              {
                                $url_webser = "http://200.93.188.109/gmt/seguimiento/webservice/WebserviceReturnNovedad.php?wsdl";                                  

                                //Parametros Web Service.   
                                $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                $mFecha[1] = date('H:i:s', strtotime($regist["fecnov"]));                                
                                
                                $parametros = array();
                                    $parametros["usuario"]=$interfaz->interfaz[$i]["usuari"];
                                    $parametros["clave"]=$interfaz->interfaz[$i]["passwo"];
                                    $parametros["planilla"] = $regist["despac"];
                                    $parametros["placa"] =$mSalida[0][1];
                                    $parametros["ind_naturaleza"] = (int)$mSalida[0][2];
                                    $parametros["fecha_reporte"]= $mFecha[0];
                                    $parametros["hora_reporte"] = $mFecha[1];
                                    $parametros["cod_puesto"] = (int)$mNomContro[0][1];
                                    $parametros["nom_punto"] = $mNomContro[0][0];
                                    $parametros["cod_novedad"] = (int)$regist["noveda"];
                                    $parametros["novedad"] =$mNomNoveda[0][0];
                                    $parametros["observacion"] = $regist["observ"];
                                //echo "<pre>"; print_r( $parametros ); echo "</pre>";
                                
                                $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                //Métodos disponibles en el WS
                                $respuesta = $oSoapClient -> __call( 'set_Novedad', $parametros );
                                echo "<pre>"; print_r( $respuesta ); echo "</pre>";
                              }
                              catch( SoapFault $e )
                              {
                                  $error_ = $e -> getMessage();

                                  if ($e -> faultcode && $e -> faultcode != '2' && $e -> faultcode != '1' && $e -> faultstring != 'NINGUNO')
                                  {
                                      $mMessage = "******** Encabezado ******** \n";
                                      $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                                      $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                      $mMessage .= "Codigo alianza: " . $mCodEmptra[0][0] . " \n";
                                      $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                      $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                      $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " \n";
                                      $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                      $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                                      $mMessage .= "******** Detalle ******** \n";
                                      $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                                      $mMessage .= "Actor del error: " . $e -> faultactor . " \n";
                                      $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";
                                      $mMessage .= "Observaciones: " . $e -> detail . " \n";

                                      //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
                                      mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Web service Faro - OnlineTools GMT", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                  }

                              }                                    
                            }

                        if (11 == $interfaz->interfaz[$i]["operad"])
                        {
                            //Interfaz con coounidas proveedor ECOM
                            $query = "SELECT  a.num_placax, a.fec_salipl, b.cod_manifi " .
                                    "FROM " . $base_datos . ".tab_despac_vehige a, " .
                                    "" . $base_datos . ".tab_despac_despac b " .
                                    "WHERE a.num_despac = '" . $regist["despac"] . "' " .
                                    "AND a.num_despac = b.num_despac ";

                            $consulta = new Consulta($query, $this->conexion);
                            $mSalida = $consulta->ret_matriz();

                            $mQuerySelCodEmp = "SELECT cod_alianz " .
                                    "FROM " . $base_datos . ".tab_config_alianz " .
                                    "WHERE cod_tercer = '" . $regist["nittra"] . "' " .
                                    "AND ind_estado = '1' ";

                            $consulta = new Consulta($mQuerySelCodEmp, $this->conexion);
                            $mCodEmptra = $consulta->ret_matriz();




/*
                            //Ruta Web Service Coounidas e-com.
                            $oSoapClient = new soapclient('http://198.171.209.91/ecom/transweb/modulos/webservice_counida/servidor.php?wsdl', true);
                            $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                            $mFecha[1] = date('H:i', strtotime($regist["fecnov"]));

                            //Parametros Web Service.
                            $parametros = array("usuario" => $interfaz->interfaz[$i]["usuari"], "password" => $interfaz->interfaz[$i]["passwo"],
                                "placa" => $mSalida[0][0], "manifiesto" => '55588899',
                                "id_puestocontrol" => $regist["contro"], "id_novedad" => $regist["noveda"], "fecha" => $mFecha[0],
                                "hora" => $mFecha[1], "observacion" => 'Interfaz - ' . $regist["observ"], "cod_empresa" => $regist["nittra"]);

                            //Consumo Web Service.
                            $respuesta = $oSoapClient->call("getDespacho", $parametros);
*/
                            
                            /********************* TRATAMIENTO SOAP *********************/
                            ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache
                            try
                            {
                                //Ruta Web Service Faro.
                                $url_webser = "http://198.171.209.91/ecom/transweb/modulos/webservice_counida/servidor.php?wsdl";
                                $mFecha[0] = date('Y-m-d', strtotime($regist["fecnov"]));
                                $mFecha[1] = date('H:i', strtotime($regist["fecnov"]));

                                //Parametros Web Service.
                                $parametros = array("usuario" => $interfaz->interfaz[$i]["usuari"],
                                                    "password" => $interfaz->interfaz[$i]["passwo"],
                                                    "placa" => $mSalida[0][0],
                                                    "manifiesto" => '55588899',
                                                    "id_puestocontrol" => $regist["contro"],
                                                    "id_novedad" => $regist["noveda"],
                                                    "fecha" => $mFecha[0],
                                                    "hora" => $mFecha[1],
                                                    "observacion" => 'Interfaz - ' . $regist["observ"],
                                                    "cod_empresa" => $regist["nittra"]
                                                    );

                                $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                
                                //Métodos disponibles en el WS
                                $respuesta = $oSoapClient -> __call( 'getDespacho', $parametros );
                            }
                            catch( SoapFault $e )
                            {
                                $error_ = $e -> getMessage();

                                if ($e -> faultcode && $e -> faultcode != '2' && $e -> faultcode != '1' && $e -> faultstring != 'NINGUNO')
                                {
                                    $mMessage = "******** Encabezado ******** \n";
                                    $mMessage .= "Fecha y hora: " . date("Y-m-d H:i") . " \n";
                                    $mMessage .= "Empresa de transporte: " . $regist["nittra"] . " \n";
                                    $mMessage .= "Codigo alianza: " . $mCodEmptra[0][0] . " \n";
                                    $mMessage .= "Numero de manifiesto: " . $mSalida[0][2] . " \n";
                                    $mMessage .= "Placa del vehiculo: " . $mSalida[0][0] . " \n";
                                    $mMessage .= "Codigo puesto de control: " . $regist["contro"] . " \n";
                                    $mMessage .= "Codigo novedad: " . $regist["noveda"] . " \n";
                                    $mMessage .= "Observación enviada: " . 'Interfaz - ' . $regist["observ"] . " \n";
                                    $mMessage .= "******** Detalle ******** \n";
                                    $mMessage .= "Codigo de error: " . $e -> faultcode . " \n";
                                    $mMessage .= "Actor del error: " . $e -> faultactor . " \n";
                                    $mMessage .= "Mesaje de error: " . $e -> faultstring . " \n";
                                    $mMessage .= "Observaciones: " . $e -> detail . " \n";

                                    //COMENTARIAR THIS -> engmiguelgarcia@gmail.com
                                    mail("supervisores@eltransporte.org, soporte.ingenieros@intrared.net", "Web service Faro - EcomCoounidas", $mMessage, 'From: soporte.ingenieros@intrared.net');
                                }

                            }
                            /********************* **************** *********************/



                        }

                        /*if (10 != $interfaz->interfaz[$i]["operad"] && 12 != $interfaz->interfaz[$i]["operad"] && 11 != $interfaz->interfaz[$i]["operad"] && 50 != $interfaz->interfaz[$i]["operad"])
                        {
                            $homolodespac = $interfaz->getHomoloDespac($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["despac"]);

                            if ($homolodespac["DespacHomolo"] > 0)
                            {
                                $homolocontro = $interfaz->getHomoloContro($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["contro"]);

                                if ($homolocontro["PCHomolo"] > 0)
                                {
                                    $homolonoveda = $interfaz->getHomoloTranspNoveda($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["noveda"]);

                                    if ($homolonoveda["NovedadHomolo"] <= 0)
                                        ;
                                    {
                                        $mensaje_sat[$a] = "La Novedad No se Encuentra Homologada en la Interfaz " . $interfaz->interfaz[$i]["nombre"];
                                        $printmenerr = 1;
                                        $a++;
                                    }
                                }
                                else
                                {
                                    $mensaje_sat[$a] = "El Puesto de Control No se Encuentra Homologado en la Interfaz " . $interfaz->interfaz[$i]["nombre"];
                                    $printmenerr = 1;
                                    $a++;
                                }
                            }
                            else
                            {
                                $mensaje_sat[$a] = "El Despacho No se Encuentra Homologado en la Interfaz " . $interfaz->interfaz[$i]["nombre"];
                                $printmenerr = 1;
                                $a++;
                            }
                        }
                        */


                    }
                }

                if ($printmenerr)
                {
                    for ($i = 0; $i < $a; $i++)
                    {
                        $RESPON[$i]["indica"] = 0;
                        $RESPON[$i]["mensaj"] = $mensaje_sat[$i];
                    }
                    return $RESPON;
                }
            }

            if ($interfaz->totalact > 0)
            {
                $a = 0;
                /*for ($i = 0; $i < $interfaz->totalact; $i++)
                {
                    $homolodespac = $interfaz->getHomoloDespac($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["despac"]);

                    if ($homolodespac["DespacHomolo"] > 0)
                    {
                        $homolocontro = $interfaz->getHomoloContro($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["contro"]);

                        if ($homolocontro["PCHomolo"] > 0)
                            ;
                        {
                            $homolonoveda = $interfaz->getHomoloTranspNoveda($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $regist["noveda"]);

                            if ($homolonoveda["NovedadHomolo"] > 0)
                            {
                                $noveda_ws["despac"] = $regist["despac"];
                                $noveda_ws["contro"] = $regist["contro"];
                                $noveda_ws["noveda"] = $regist["noveda"];
                                $noveda_ws["duraci"] = $tiemp_adicis_interf;
                                $noveda_ws["fechax"] = $regist["fecact"];
                                $noveda_ws["observ"] = $regist["observ"];

                                $resultado_ws = $interfaz->insNovedaNC($interfaz->interfaz[$i]["operad"], $interfaz->interfaz[$i]["usuari"], $interfaz->interfaz[$i]["passwo"], $noveda_ws);

                                if ($resultado_ws["Confirmacion"] == "OK")
                                {
                                    $mensaje_sat[$a][0] = 1;
                                    $mensaje_sat[$a][1] = "La Novedad Fue Registrada Exitosamente en la Interfaz " . $interfaz->interfaz[$i]["nombre"];
                                }
                                else
                                {
                                    $mensaje_sat[$a][0] = 0;
                                    $mensaje_sat[$a][1] .= "Se Presento un Error al Insertar la Novedad en la Interfaz " . $interfaz->interfaz[$i]["nombre"] . " :: " . $resultado_ws["Confirmacion"];
                                }
                                $a++;
                            }
                        }
                    }
                }
*/




            }
        }
        $query = "SELECT a.num_placax,b.cod_manifi
               FROM " . $base_datos . ".tab_despac_vehige a,
                    " . $base_datos . ".tab_despac_despac b
              WHERE a.num_despac = '" . $regist["despac"] . "' AND
                    a.num_despac = b.num_despac ";

        $consulta = new Consulta($query, $this->conexion);
        $placax = $consulta->ret_vector();

        //Manejo Interfaz GPS
        /* if($ominter != 3)
          {
          $interf_gps = new Interfaz_GPS();
          $interf_gps -> Interfaz_GPS_envio($regist["nittra"],$base_datos,$this -> usuari,$this -> conexion);

          for($i = 0; $i  < $interf_gps -> cant_interf; $i++)
          {
          if($interf_gps -> getVehiculo($placax[0],$interf_gps -> cod_operad[$i][0],$regist["nittra"]))
          {
          $idgps = $interf_gps -> getIdGPS($placax[0],$interf_gps -> cod_operad[$i][0],$regist["nittra"]);

          if($interf_gps -> setAcTimeRepor($interf_gps -> cod_operad[$i][0],$regist["nittra"],$placax[0],$idgps,$regist["despac"],$regist["fecact"],$interf_gps -> val_timtra[$i][0]));
          }
          }
          }
         */
        $porapl["nittra"] = $regist["nittra"];
        $porapl["bdsata"] = $base_datos;

        $query = "SELECT a.nom_contro
               FROM " . $base_datos . ".tab_genera_contro a
              WHERE a.cod_contro = " . $regist["contro"] . " ";

        $consulta = new Consulta($query, $this->conexion);
        $nomcontr = $consulta->ret_vector();

        $query = "SELECT a.nom_noveda
               FROM " . $base_datos . ".tab_genera_noveda a
              WHERE a.cod_noveda = " . $regist["noveda"] . " AND
                    a.ind_alarma = 'S' ";

        $consulta = new Consulta($query, $this->conexion);
        $nomnoved = $consulta->ret_vector();

        if ($nomnoved)
        {
            //------------------------------------------
            $despac_men["client"] = $despac["client"];
            $despac_men["remisi"] = $despac["remisi"];
            $despac_men["pedido"] = $despac["pedido"];
            $despac_men["origen"] = $this->nom_ciuori;
            $despac_men["destin"] = $this->nom_ciudes;
            $despac_men["transp"] = $this->nom_transp;
            //------------------------------------------
            $despac_men["manifi"] = $placax[1];
            $despac_men["contro"] = $nomcontr[0];
            $despac_men["noveda"] = $nomnoved[0];
            $despac_men["placa"] = $placax[0];
            $despac_men["fecnov"] = $regist["fecact"];
            $despac_men["observ"] = $regist["observ"];
            $despac_men["tipnov"] = "Nota de Controlador";

            $MensajReport = new EnvMensaj();
            $MensajReport->EnvMensajCron($this->conexion, $porapl);
            $MensajReport->EjecPrecesNove($despac_men);
        }

        $RESPON[0]["indica"] = 1;
        $RESPON[0]["mensaj"] = "La Novedad se Registro Exitosamente en el Sistema";

        for ($i = 0; $i < $a; $i++)
        {
            $RESPON[$i + 1]["indica"] = $mensaje_sat[$i][0];
            $RESPON[$i + 1]["mensaj"] = $mensaje_sat[$i][1];
        }

        return $RESPON;
    }

        function getListadoCiudades()
    {
        $query = "SELECT a.cod_ciudad,CONCAT(UPPER(a.abr_ciudad),' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3),' - ',a.cod_ciudad)
  			  FROM " . BASE_DATOS . ".tab_genera_ciudad a,
  			       " . BASE_DATOS . ".tab_genera_depart b,
  			       " . BASE_DATOS . ".tab_genera_paises c
  			 WHERE a.cod_depart = b.cod_depart AND
  			       a.cod_paisxx = b.cod_paisxx AND
  			       b.cod_paisxx = c.cod_paisxx 

				   GROUP BY 1 ORDER BY 2
  		   ";

        $consulta = new Consulta($query, $this->conexion);
        $ciudades = $consulta->ret_matriz();

        return $ciudades;
    }
     
    function getEmailAgecni($num_despac)
    {
      $mQuery = "SELECT b.dir_emailx 
                 FROM ".BASE_DATOS.".tab_despac_despac a,
                      ".BASE_DATOS.".tab_genera_agenci b
                 WHERE a.cod_agedes = b.cod_agenci AND
                       a.num_despac = '".$num_despac."'";
      $consulta = new Consulta($mQuery, $this->conexion);
      $mEmailAgenci = $consulta->ret_matriz();

      return $mEmailAgenci[0][0];
                       
    }

    function getSeleccCiudad($ciudad, $despac = NULL)
    {
		$query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3),' - ',a.cod_ciudad)
  			  FROM " . BASE_DATOS . ".tab_genera_ciudad a,
  			       " . BASE_DATOS . ".tab_genera_depart b,
  			       " . BASE_DATOS . ".tab_genera_paises c
  			 WHERE a.cod_depart = b.cod_depart AND
  			       a.cod_paisxx = b.cod_paisxx AND
  			       b.cod_paisxx = c.cod_paisxx AND
               a.cod_ciudad = '" . $ciudad . "'";
        /*if ($despac == NULL)
            $query .=" AND a.ind_estado = '1' ";*/

        $query .=" GROUP BY 1 ORDER BY 2";

        $consulta = new Consulta($query, $this->conexion);
        $ciudades = $consulta->ret_matriz();

        return $ciudades;
    }

    function getCantTiempo($minutos)
    {
        if ($minutos >= 0 && $minutos < 60)
            $resultado = round($minutos, 1) . " Min(s).";
        else if ($minutos > 59 && $minutos < 1440)
            $resultado = round($minutos / 60, 1) . " Hor(s).";
        else if ($minutos > 1439 && $minutos < 10080)
            $resultado = round($minutos / 1440, 1) . " Dia(s).";
        else if ($minutos > 10079 && $minutos < 43200)
            $resultado = round($minutos / 10080, 1) . " Sem(s).";
        else if ($minutos > 43199)
            $resultado = round($minutos / 43200, 1) . " Mes(s).";

        return $resultado;
    }

    //vlix85  
    function GET_EAL($num_despac)
    {


        $mQuery = "SELECT a.num_despac, a.cod_contro
                 FROM " . BASE_DATOS . ".tab_despac_seguim a, " . BASE_DATOS . ".tab_genera_contro b
                WHERE a.cod_contro = b.cod_contro
                  AND b.ind_virtua =0
                  AND a.ind_estado =0
                  AND a.num_despac = '" . $num_despac . "'
                  AND (
                    a.num_despac, a.cod_contro
                  ) NOT
                  IN (
                    SELECT num_despac, cod_contro
                    FROM " . BASE_DATOS . ".tab_despac_noveda
                  )";
        $consulta = new Consulta($mQuery, $this->conexion);
        $consulta = $consulta->ret_matriz();

        return count($consulta) > 0 ? TRUE : FALSE;
    }

    //Almacena los errores de la Interfaz al Insertar novedades en los SATB y SATE desde SATT
    function setNovedadError($parametros, $regist, $novedaError, $metodo)
    {
        $novedaError['det_respon'] = str_replace("'", "", $novedaError['det_respon']);
        $novedaError['msg_respon'] = str_replace("'", "", $novedaError['msg_respon']);
        $query = "INSERT INTO " . BASE_DATOS . ".tab_errorx_noveda 
              ( cod_transp, num_despac,  cod_rutasx,
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
              ( '" . $regist['nittra'] . "', '" . $regist['despac'] . "', '" . $regist['rutax'] . "',
                '" . $regist["contro"] . "', '" . $metodo . "', '" . $novedaError['cod_respon'] . "',
                '" . $novedaError['msg_respon'] . "', '" . $novedaError['det_respon'] . "', '" . $parametros['nom_usuari'] . "',
                '" . $parametros['pwd_clavex'] . "', '" . $parametros['nom_aplica'] . "', '" . $parametros['num_manifi'] . "',
                '" . $parametros['num_placax'] . "', '" . $parametros['cod_novbas'] . "', '" . $parametros['cod_conbas'] . "',
                '" . $parametros['tim_duraci'] . "', '" . $parametros['fec_noveda'] . "', '" . $parametros['des_noveda'] . "',
                '" . $parametros['nom_contro'] . "', '" . $parametros['nom_sitiox'] . "', '" . $parametros['cod_confar'] . "',
                '" . $parametros['cod_novfar'] . "', '" . $parametros['nom_noveda'] . "', '" . $parametros['ind_alarma'] . "',
                '" . $parametros['ind_tiempo'] . "', '" . $parametros['nov_especi_'] . "', '" . $parametros['ind_manala'] . "',
                '" . $_SESSION['datos_usuario']['cod_usuari'] . "', NOW(), '" . $regist["server"] . "' )";
        $insercion = new Consulta($query, $this->conexion, "R");
    }
    
    function InsertPuestoControlHumadea( $mParametros , $mLog)
    {
      // Inset Log para el consumo del webservice
     $mQueryInsertLog = "INSERT INTO ".$base_datos.".tab_errorx_puecon 
      (
        cod_consec, empresa,  usuario, clave, sentido, direccion,
        tipo_evento, kilometraje, velocidad, codigo_puestocontrol,  novedad, longitud,
        latitud, hora,  fecha, placa, manifiesto_codigo, num_despac, ind_estado,
        resultado, fec_creaci, usr_creaci
      ) 
      VALUES
      (
        '', '$mParametros[empresa]','$mParametros[usuario]',   '$mParametros[clave]',    '$mParametros[sentido]',             '$mParametros[direccion]',
        '$mParametros[tipo_evento]','$mParametros[kilometraje]','$mParametros[velocidad]','$mParametros[codigo_puestocontrol]','$mParametros[novedad]','$mParametros[longitud]',
        '$mParametros[latitud]',    '$mParametros[hora]',       '$mParametros[fecha]',    '$mParametros[placa]',               '$mParametros[manifiesto_codigo]', '$mLog[mNumDes]' ,'$mLog[mEstado]',
        '".str_replace("'", "", $mLog['mResult'])."', NOW() ,'$mLog[mUsuari]'
      )";      
      $consulta = new Consulta($mQueryInsertLog, $this->conexion, 'R');
      return $consulta;
    }    
    
    function InsertNovedadHumadea( $mParametros , $mLog)
    {
      // Inset Log para el consumo del webservice
     $mQueryInsertLog = "INSERT INTO ".$base_datos.".tab_errorx_novsil
      (
        cod_consec, empresa, usuario, clave, novedad,
        hora, fecha, placa, manifiesto_codigo, num_despac, ind_estado,
        resultado, fec_creaci, usr_creaci
      ) 
      VALUES
      (
        '', '$mParametros[empresa]','$mParametros[usuario]',   '$mParametros[clave]', '$mParametros[novedad]', '$mParametros[hora]',  
        '$mParametros[fecha]',    '$mParametros[placa]', '$mParametros[manifiesto_codigo]', '$mLog[mNumDes]','$mLog[mEstado]',
        '".str_replace("'", "", $mLog['mResult'])."', NOW() ,'$mLog[mUsuari]'
      )
      ";     
      $consulta = new Consulta($mQueryInsertLog, $this->conexion, 'R');
      return $consulta;
    }
    
    function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) {
    $position = array();
    $newRow = array();
    foreach ($toOrderArray as $key => $row) {
            $position[$key]  = $row[$field];
            $newRow[$key] = $row;
    }
    if ($inverse) {
        arsort($position);
    }
    else {
        asort($position);
    }
    $returnArray = array();
    foreach ($position as $key => $pos) {     
        $returnArray[] = $newRow[$key];
    }
    return $returnArray;
}

}

?>
