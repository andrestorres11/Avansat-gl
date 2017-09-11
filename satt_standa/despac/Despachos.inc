<?php
/*! \file: Despachos.inc
 *  \brief: Archivo principal para los despachos
 *  \author: 
 *  \author: 
 *  \version: 
 *  \date: dia/mes/año
 *  \bug: 
 *  \bug: 
 *  \warning: 
 */

/*! \class: Despachos
 *  \brief: Clase principal para los despachos
 */
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
    var $codPerfil = NULL;
    var $ArrayComp = NULL;
    var $ArrayTipTansp = array("NA", "FLOTA PROPIA", "TERCEROS", "EMPRESAS");
    var $cTipFactur = array(" ", " - (Por Despacho)", " - (Por Registro)");

    var $cNovedadALL = NULL;

    function __construct($servic, $opcion, $aplica, $conexion, $opcion_base = NULL)
    {
        @include_once('../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc');
        $this->cod_aplica = $aplica;
        $this->servic = $servic;
        $this->opcion = $opcion;
        $this->conexion = $conexion;
        $this->codPerfil = $_SESSION["datos_usuario"]["cod_perfil"];
        $this->opcion_base = $opcion_base;
        $this->paginador = new Paginador_listado($opcion_base, $servic, $conexion);
        $this->GeneralFunctions = new GeneralFunctions($this->conexion);
    }

    function getFirstElement( $arr_fechas, $ind_ordenx )
    {
      $mArray = explode(', ', $arr_fechas);

      if( $ind_ordenx == 'A' )
      {
        for($i = 0; $i < count( $mArray ) - 1; $i++)
        {
          for($j = 0; $j < count( $mArray ) - 1; $j++)
          {
            if(strtotime( $mArray[$j] ) > strtotime( $mArray[$j+1] ) ) 
            {
              $aux = $mArray[$j];
              $mArray[$j] = $mArray[$j+1];
              $mArray[$j+1] = $aux;
            }
          }  
        }
      }

      if( $ind_ordenx == 'A' )
      {
        for($i = 0; $i < count( $mArray ) - 1; $i++)
        {
          for($j = 0; $j < count( $mArray ) - 1; $j++)
          {
            if(strtotime( $mArray[$j] ) <= strtotime( $mArray[$j+1] ) ) 
            {
              $aux = $mArray[$j];
              $mArray[$j] = $mArray[$j+1];
              $mArray[$j+1] = $aux;
            }
          }  
        }
      }
      return $mArray[0];
    }

   function ListadoPrincipal($datos_usuario, $busque = 0, $dir_export = "", $des_retras = 0, $validmatriz = NULL, $GLOBALS_ADD = NULL, $finali = 0, $desfut = 0, $liscon = 0, $matcondesp = NULL, $opcurban = 0) {
        $this->ind_status = $GLOBALS_ADD[0]["valor"];

        if ($GLOBALS_ADD[9]["valor"] != '')
            $_REQUEST["bdespac"] = $GLOBALS_ADD[9]["valor"];

        $query = "SELECT a.ind_remdes, a.ind_desurb
  		     FROM " . BASE_DATOS . ".tab_config_parame a ";
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
              FROM " . BASE_DATOS . ".tab_despac_despac a
         LEFT JOIN " . BASE_DATOS . ".tab_despac_sisext k
                ON a.num_despac = k.num_despac,
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
                   a.ind_planru = 'S' ";

        if (!$finali) {
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

        if ($busque) {
            if ($_REQUEST["placa"])
                $query = $query . " AND d.num_placax = '" . $_REQUEST["placa"] . "'";
            if ($_REQUEST["manifi"])
                $query = $query . " AND a.cod_manifi = '" . $_REQUEST["manifi"] . "'";
            if ($_REQUEST["despac"])
                $query = $query . " AND a.num_despac = '" . $_REQUEST["despac"] . "'";
        }

        if ($validmatriz) {
            for ($i = 0; $i < sizeof($validmatriz); $i++)
                $query .= $validmatriz[$i]["linea"];
        }
        if ($datos_usuario["cod_perfil"] == "") {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }

            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        } else {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }

            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
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
				  FROM " . BASE_DATOS . ".tab_despac_despac a
           LEFT JOIN " . BASE_DATOS . ".tab_despac_sisext k
                  ON a.num_despac = k.num_despac,
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
		
        if (!$finali) {
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

        if ($busque) {
            if ($_REQUEST["placa"])
                $query = $query . " AND d.num_placax = '" . $_REQUEST["placa"] . "'";
            if ($_REQUEST["manifi"])
                $query = $query . " AND a.cod_manifi = '" . $_REQUEST["manifi"] . "'";
            if ($_REQUEST["despac"])
                $query = $query . " AND a.num_despac = '" . $_REQUEST["despac"] . "'";
        }

        if ($validmatriz) {
            for ($i = 0; $i < sizeof($validmatriz); $i++)
                $query .= $validmatriz[$i]["linea"];
        }

        if ($datos_usuario["cod_perfil"] == "") {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        } else {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
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
              FROM " . BASE_DATOS . ".tab_despac_despac a
         LEFT JOIN " . BASE_DATOS . ".tab_despac_sisext k
                ON a.num_despac = k.num_despac,
                   " . BASE_DATOS . ".tab_despac_seguim b,
                   " . BASE_DATOS . ".tab_despac_vehige d,
                   " . BASE_DATOS . ".tab_tercer_tercer e
             WHERE a.num_despac = d.num_despac AND
             	   	 a.num_despac = b.num_despac AND
                   d.cod_transp = e.cod_tercer AND
                   a.fec_salida Is Not Null AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S' ";
                   

        if (!$finali) {
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

        if ($busque) {
            if ($_REQUEST["placa"])
                $query = $query . " AND d.num_placax = '" . $_REQUEST["placa"] . "'";
            if ($_REQUEST["manifi"])
                $query = $query . " AND a.cod_manifi = '" . $_REQUEST["manifi"] . "'";
            if ($_REQUEST["despac"])
                $query = $query . " AND a.num_despac = '" . $_REQUEST["despac"] . "'";
        }

        if ($validmatriz) {
            for ($i = 0; $i < sizeof($validmatriz); $i++)
                $query .= $validmatriz[$i]["linea"];
        }

        if ($datos_usuario["cod_perfil"] == "") {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        } else {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
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
                    
        /* -------------------------------------------------------------------------------------------------------------------------------------- */
        $query = "SELECT d.num_placax,d.num_placax
              FROM " . BASE_DATOS . ".tab_despac_despac a
         LEFT JOIN " . BASE_DATOS . ".tab_despac_sisext k
                ON a.num_despac = k.num_despac,
                   " . BASE_DATOS . ".tab_despac_seguim b,
                   " . BASE_DATOS . ".tab_despac_vehige d,
                   " . BASE_DATOS . ".tab_tercer_tercer e
             WHERE a.num_despac = d.num_despac AND
             	   	 a.num_despac = b.num_despac AND
                   d.cod_transp = e.cod_tercer AND
                   a.fec_salida Is Not Null AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S' ";
                   
        if (!$finali) {
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

        if ($busque) {
            if ($_REQUEST["placa"])
                $query = $query . " AND d.num_placax = '" . $_REQUEST["placa"] . "'";
            if ($_REQUEST["manifi"])
                $query = $query . " AND a.cod_manifi = '" . $_REQUEST["manifi"] . "'";
            if ($_REQUEST["despac"])
                $query = $query . " AND a.num_despac = '" . $_REQUEST["despac"] . "'";
        }

        if ($validmatriz) {
            for ($i = 0; $i < sizeof($validmatriz); $i++)
                $query .= $validmatriz[$i]["linea"];
        }

        if ($datos_usuario["cod_perfil"] == "") {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        } else {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
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
        
        /* -------------------------------------------------------------------------------------------------------------------------------------- */

        

        $defini = array(1, "SI");
        //trae los despachos en ruta
        $query = "SELECT a.num_despac, a.cod_manifi, a.cod_ciuori,
	 								 a.cod_ciudes, f.abr_tercer, d.num_placax,
									 IF( d.nom_conduc IS NOT NULL, d.nom_conduc, e.abr_tercer) AS abr_tercer,
                   IF( a.con_telmov IS NULL, e.num_telmov, a.con_telmov ) AS num_telmov,
                   '', '', '',
									 '', '', '',
									 '', d.cod_transp, a.ind_defini,
									 DATE_FORMAT( a.fec_ultnov, '%Y-%m-%d %H:%i' ), n.abr_tercer AS aseguradora, 
                   IF( k.num_desext IS NULL,'N/A' ,k.num_desext ) as num_desext,
                   IF( k.num_solici IS NULL,'N/A' ,k.num_solici ) as num_solici
              FROM " . BASE_DATOS . ".tab_despac_seguim b,
                   " . BASE_DATOS . ".tab_despac_vehige d,
                   " . BASE_DATOS . ".tab_tercer_tercer e,
                   " . BASE_DATOS . ".tab_tercer_tercer f,
                   " . BASE_DATOS . ".tab_despac_despac a
         LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer n 
                ON n.cod_tercer = a.cod_asegur
         LEFT JOIN " . BASE_DATOS . ".tab_despac_sisext k
                ON a.num_despac = k.num_despac
             WHERE a.num_despac = d.num_despac AND
                   a.num_despac = b.num_despac AND
                   d.cod_conduc = e.cod_tercer AND
                   d.cod_transp = f.cod_tercer AND
                   a.fec_salida Is Not Null AND
                   a.ind_anulad = 'R' AND
                   a.ind_planru = 'S' ";

        if (strtolower($datos_usuario["cod_usuari"]) == 'alogistica')
            $query .= " AND d.cod_transp IN ( SELECT cod_tercer FROM " . BASE_DATOS . ".tab_config_alianz ) ";


        if (!$finali) {
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

        if ($busque) {
            if ($_REQUEST["placa"])
                $query = $query . " AND d.num_placax = '" . $_REQUEST["placa"] . "'";
            if ($_REQUEST["manifi"])
                $query = $query . " AND a.cod_manifi = '" . $_REQUEST["manifi"] . "'";
            if ($_REQUEST["despac"])
                $query = $query . " AND a.num_despac = '" . $_REQUEST["despac"] . "'";
        }


        if ($validmatriz) {
            for ($i = 0; $i < sizeof($validmatriz); $i++)
                $query .= $validmatriz[$i]["linea"];
        }

        if ($datos_usuario["cod_perfil"] == "") {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA
            $filtro = new Aplica_Filtro_Usuari($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_usuari"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        } else {
            //PARA EL FILTRO DE CONDUCTOR
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CONDUC, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_conduc = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE EMPRESA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_EMPTRA, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_transp = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE ASEGURADORA
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_ASEGUR, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_asegur = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DEL CLIENTE
            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_CLIENT, $datos_usuario["cod_perfil"]);
            if ($filtro->listar($this->conexion)) {
                $datos_filtro = $filtro->retornar();
                $query = $query . " AND a.cod_client = '$datos_filtro[clv_filtro]' ";
            }
            //PARA EL FILTRO DE LA AGENCIA

            $filtro = new Aplica_Filtro_Perfil($this->cod_aplica, COD_FILTRO_AGENCI, $datos_usuario["cod_perfil"]);
            echo "<div style='display:none' >Aplica=" . $this->cod_aplica . " CODFIltroagenci=" . COD_FILTRO_AGENCI . " perfil=" . $datos_usuario["cod_perfil"] . "</div>";
            if ($filtro->listar($this->conexion)) {

                $datos_filtro = $filtro->retornar();
                $query = $query . " AND d.cod_agenci = '$datos_filtro[clv_filtro]' ";
            }
        }

        if (!$finali)
            $query = $query . " GROUP BY 1 ORDER BY b.fec_alarma,1 ";
        else
            $query = $query . " GROUP BY 1 ORDER BY a.fec_llegad,1";

        $matriz = $this->paginador->ejecPaginador($_REQUEST["npagina"], $query);


        $query_exp = $this->paginador->query;

        $ind_porlleg = 0;
        $ind_enrutas = 0;

        if ($finali) {

            for ($i = 0; $i < sizeof($matriz); $i++) {
                if ($this->GeneralFunctions->ViewVisibilityInterf(array("transp" => $matriz[$i][15]))) {
                    if ($manredes == 1) {
                        $query = "SELECT a.num_docume
	 			  FROM " . BASE_DATOS . ".tab_despac_remdes a
                	 			 WHERE a.num_despac = " . $matriz[$i][0] . " ";

                        $consulta = new Consulta($query, $this->conexion);
                        $document_despac = $consulta->ret_matriz();

                        if ($document_despac) {
                            $remisiones_destin = $document_despac[0][0];

                            for ($j = 1; $j < sizeof($document_despac); $j++)
                                if ($document_despac[$j][0])
                                    $remisiones_destin .= ", " . $document_despac[$j][0];

                            if (!$remisiones_destin)
                                $remisiones_destin = "-";
                        } else
                            $remisiones_destin = "-";

                        $matriz[$i][12] = $remisiones_destin;

                        $query = "SELECT a.num_pedido
	 			  FROM " . BASE_DATOS . ".tab_despac_remdes a
                	 			 WHERE a.num_despac = " . $matriz[$i][0] . " ";

                        $consulta = new Consulta($query, $this->conexion);
                        $document_despac = $consulta->ret_matriz();

                        if ($document_despac) {
                            $pedidos_destin = $document_despac[0][0];

                            for ($j = 1; $j < sizeof($document_despac); $j++)
                                if ($document_despac[$j][0])
                                    $pedidos_destin .= ", " . $document_despac[$j][0];

                            if (!$pedidos_destin)
                                $pedidos_destin = "-";
                        } else
                            $pedidos_destin = "-";

                        $matriz[$i][13] = $pedidos_destin;
                    }

                    $porllegad[] = $matriz[$i];
                    $ind_porlleg++;
                }
            }
        }
        else {
            for ($i = 0; $i < sizeof($matriz); $i++) {

                if ($this->GeneralFunctions->ViewVisibilityInterf(array("transp" => $matriz[$i][15]))) {
                    if ($manredes == 1) {
                        $query = "SELECT a.num_docume
	 			  FROM " . BASE_DATOS . ".tab_despac_remdes a
                	 			 WHERE a.num_despac = " . $matriz[$i][0] . " ";

                        $consulta = new Consulta($query, $this->conexion);
                        $document_despac = $consulta->ret_matriz();

                        if ($document_despac) {
                            $remisiones_destin = $document_despac[0][0];

                            for ($j = 1; $j < sizeof($document_despac); $j++)
                                if ($document_despac[$j][0])
                                    $remisiones_destin .= ", " . $document_despac[$j][0];

                            if (!$remisiones_destin)
                                $remisiones_destin = "-";
                        } else
                            $remisiones_destin = "-";

                        $matriz[$i][12] = $remisiones_destin;

                        $query = "SELECT a.num_pedido
	 			  FROM " . BASE_DATOS . ".tab_despac_remdes a
                	 			 WHERE a.num_despac = " . $matriz[$i][0] . " ";

                        $consulta = new Consulta($query, $this->conexion);
                        $document_despac = $consulta->ret_matriz();

                        if ($document_despac) {
                            $pedidos_destin = $document_despac[0][0];

                            for ($j = 1; $j < sizeof($document_despac); $j++)
                                if ($document_despac[$j][0])
                                    $pedidos_destin .= ", " . $document_despac[$j][0];

                            if (!$pedidos_destin)
                                $pedidos_destin = "-";
                        } else
                            $pedidos_destin = "-";

                        $matriz[$i][13] = $pedidos_destin;
                    }

                    $query = "SELECT a.cod_rutasx
	             FROM " . BASE_DATOS . ".tab_despac_seguim a
	            WHERE a.num_despac = " . $matriz[$i][0] . "
        		          GROUP BY 1 ";

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

                    if ($ultimnov) {
                        $query = "SELECT a.ind_urbano
	 		      FROM " . BASE_DATOS . ".tab_genera_contro a
	 		     WHERE a.cod_contro = " . $ultimnov[0][0] . " AND
                	 		     	   a.ind_urbano = '1' ";

                        $consulta = new Consulta($query, $this->conexion);
                        $pcontrurb = $consulta->ret_matriz();
                    } else
                        $pcontrurb = NULL;


                    if ($manredes == 1 && $desurb == 1 && $pcontrurb)
                        $pcomparar = $ultimnov[0][0];
                    else
                        $pcomparar = $ultimopc[0][0];



                    if ($pcomparar == $ultimnov[0][0] || $ultimnov[0][0] == CONS_CODIGO_PCLLEG) {
                        $porllegad[$ind_porlleg] = $matriz[$i];
                        $ind_porlleg++;
                    } else {
                        if ($_REQUEST["defini"] == $matriz[$i]["ind_defini"] || $_REQUEST["defini"] == '') {
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
        else if (!$finali) {
            $query = "SELECT nom_alarma, cant_tiempo, cod_colorx
               FROM " . BASE_DATOS . ".tab_genera_alarma
                    ORDER BY 2 ";

            $consulta = new Consulta($query, $this->conexion);

            $alarmas = $consulta->ret_matriz();

            $query = "SELECT cant_tiempo,cod_colorx
           FROM " . BASE_DATOS . ".tab_genera_alarma ";
            $consulta = new Consulta($query, $this->conexion);
            $colores = $consulta->ret_matriz();

            echo "<script>setTimeout('document.location.reload(\"index.php?window=central&cod_servic=" . $this->servic . "\")',300000); </script>";

            $formulario = new Formulario("index.php", "post", "Despachos en Ruta", "form_item", "", "", "100%");
        } else
            $formulario = new Formulario("index.php", "post", "Despachos Finalizados", "form_item", "", "", "100%");


        if ($busque) {
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

        if ($dir_export != "") {
            $_SESSION['sql'] = $query_exp;
            //$query_exp = base64_encode($query_exp);
            $exp .= "url=" . NOM_URL_APLICA . "&db=" . BASE_DATOS . "&nom_opcion=" . $dir_export . "&des_retras=" . $des_retras . "&finali=" . $finali;

            $matlinkim[0][0] = "../" . DIR_APLICA_CENTRAL . "/imagenes/pdf.jpg";
            $matlinkim[0][1] = "onClick=\"top.window.open('../" . DIR_APLICA_CENTRAL . "/export/exp_genera_despac.php?" . $exp . "&manpdf=1')\"";

            $formulario->nueva_tabla();
            $formulario->imagen("Exportar", "../" . DIR_APLICA_CENTRAL . "/imagenes/excel.jpg", "Exportar", 30, 30, 0, "onClick=\"top.window.open('../" . DIR_APLICA_CENTRAL . "/export/exp_genera_despac.php?" . $exp . "')\"", 1, 0, $matlinkim);
        }


        if (!$des_retras && !$datos_usuario["bandeja"]) {
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
            else if (!$finali) {
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

        if (!$finali) {
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


            if ($manredes == 1) {
                $formulario->linea("Remisi&oacute;n", 0, "t");
                $formulario->linea("Pedido", 0, "t");
            }

            if (!$des_retras) {
                $formulario->lista_titulo("Origen", "origen\" onChange=\"form_item.submit()\"", $origen, 0);
                $formulario->lista_titulo("Destino", "destino\" onChange=\"form_item.submit()\"", $destino, 0);
                $formulario->lista_titulo("Transportadora", "transp\" onChange=\"form_item.submit()\"", $transpors, 0);
            } else {
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


            for ($i = 0; $i < sizeof($desenruta); $i++) {

                $query = "(SELECT e.ind_fuepla, a.cod_conult,a.cod_ultnov,a.fec_ultnov,a.usr_ultnov,
                b.obs_contro,c.nom_contro,d.nom_noveda,	b.fec_contro,
                b.cod_contro,e.nom_noveda,b.fec_creaci, c.cod_colorx
                          FROM  " . BASE_DATOS . ".tab_despac_despac    a   LEFT    JOIN
                                " . BASE_DATOS . ".tab_despac_contro    b   ON
                                a.num_despac    =   b.num_despac    AND
                                a.cod_consec    =   b.cod_consec    LEFT    JOIN
                                " . BASE_DATOS . ".tab_genera_noveda    e   ON
                                b.cod_noveda    =   e.cod_noveda,
                                " . BASE_DATOS . ".tab_genera_contro    c,
                                " . BASE_DATOS . ".tab_genera_noveda    d
                         WHERE  a.cod_conult    =   c.cod_contro    AND
                                a.cod_ultnov    =   d.cod_noveda    AND
                                a.num_despac    =   '" . $desenruta[$i][0] . "')
         UNION
       (SELECT	e.ind_fuepla, a.cod_conult,a.cod_ultnov,a.fec_ultnov,a.usr_ultnov,
                b.des_noveda,c.nom_contro,d.nom_noveda,b.fec_noveda,
                                b.cod_contro,e.nom_noveda,  b.fec_creaci, c.cod_colorx
                          FROM  " . BASE_DATOS . ".tab_despac_despac    a   LEFT    JOIN
                                " . BASE_DATOS . ".tab_despac_noveda    b   ON
                                a.num_despac    =   b.num_despac    
                                LEFT    JOIN " . BASE_DATOS . ".tab_genera_noveda   e   ON
                                b.cod_noveda    =   e.cod_noveda,
                                " . BASE_DATOS . ".tab_genera_contro    c,
                                " . BASE_DATOS . ".tab_genera_noveda    d
                         WHERE  a.cod_conult    =   c.cod_contro    AND
                                a.cod_ultnov    =   d.cod_noveda    AND
                                a.num_despac    =   '" . $desenruta[$i][0] . "')
                      ORDER BY  12  DESC    LIMIT   1";
                $consulta = new Consulta($query, $this->conexion);
                $fuer_plan = $consulta->ret_matriz();

                if ($_REQUEST['FUEPLA'] == '1' && $fuer_plan[0][0] != '1') {
                    continue;
                }

                //if( $fuer_plan[0][0]=='1' && $_REQUEST['alacla'] != 'F' )
                //{
                //continue;
                //}

                $valimp = 1;
                $indicad_mostrar_condic = 1;

                //validacion de alarmas nueva --jose
                $query = "SELECT     cod_tipdes " .
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

                if ($transpor) {
                    if ($transpor[0][2] == '1') {
                        $tiem = 'P';
                    } else {
                        if ($cod_tipdes[0][0] == '1') {
                            $tiem = $transpor[0][0];
                        } else {
                            $tiem = $transpor[0][1];
                        }
                    }
                } else {
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
                if ($despac[0][1] != '') {
                    $query = "SELECT tiem_duraci 
                 FROM " . BASE_DATOS . ".tab_despac_contro
                WHERE num_despac = '" . $desenruta[$i][0] . "' AND
                      fec_contro = '" . $despac[0][3] . "" . ":00'
            ORDER BY fec_creaci DESC";
                    $consulta = new Consulta($query, $this->conexion);
                    $tiem_diff = $consulta->ret_matriz();
                }

                if ($tiem_diff && $tiem_diff[0][0] != 0) {
                    $tiem = $tiem_diff[0][0];
                } else {
                    if ($despac[0][2] != '') {
                        $query = "SELECT tiem_duraci 
                   FROM " . BASE_DATOS . ".tab_despac_noveda
                  WHERE num_despac = '" . $desenruta[$i][0] . "' AND
                        fec_noveda = '" . $despac[0][3] . "" . ":00'
               ORDER BY fec_creaci DESC";
                        $consulta = new Consulta($query, $this->conexion);
                        $tiem_diff = $consulta->ret_matriz();
                    }
                    if ($tiem_diff && $tiem_diff[0][0] != 0) {
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
                if ($mFecManala) {
                    $query = "SELECT CAST( (TIME_TO_SEC( TIMEDIFF(NOW(), '" . $mFecManala . "')) / 60) AS UNSIGNED INTEGER )";
                    $consulta = new Consulta($query, $this->conexion);
                    $mTieDif = $consulta->ret_matriz();
                    $tiemdif = $mTieDif[0][0];
                }

                if (is_numeric($tiem)) {
                    if ($tiemdif == '') {
                        $query = "SELECT TIMEDIFF(  NOW(), '" . $despac[0][3] . "' ) ";
                        $consulta = new Consulta($query, $this->conexion);
                        $tiemdif = $consulta->ret_matriz();
                        $tiemdif = explode(":", $tiemdif[0][0]);
                        $tiemdif = ($tiemdif[0] * 60) + $tiemdif[1];
                        //valida para monitoreo mixto y un perfil especifico si ya deberia haber pasado por un puesto
                        $fisico = 0;
                    }

                    $tieret = "";
                    if ($tiemdif > $tiem) {
                        $indicad_mostrar_condic = 1;
                        $tieret = $tiemdif - $tiem;
                    }
                } else {
                    $query = "SELECT    a.cod_contro,a.fec_alarma " .
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

                if ($tieret > 0) {
                    $y = 1;

                    for ($z = 0; $z <= sizeof($colores); $z++) {
                        if ($z + 1 == $_REQUEST['alacla']) {
                            $color_alacla = $colores[$z][1];
                        }
                        if ($tieret <= $colores[$z][0]) {
                            $x = $z;
                            $y = 2;
                            break;
                        }
                    }

                    if ($y == 1) {
                        $color_a[0] = $colores[sizeof($colores) - 1][1];
                    } else {
                        $color_a[0] = $colores[$x][1];
                    }
                } else {
                    $color_a[0] = '#FFFFFF';


                    if ('S' == $_REQUEST['alacla']) {
                        $color_alacla = '#FFFFFF';
                    }
                }

                if ($color_a[0] != $color_alacla && $_REQUEST["alacla"]) {
                    //Si el despacho tiene un color distinto al del alacla no se imprime ese despacho
                    continue;
                } else {
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
          
            if ($desfut)
                $desenruta = $this->orderMultiDimensionalArray($desenruta, 'tiempo', 0);
          
            for ($i = 0; $i < sizeof($desenruta); $i++) {
                if ($datos_usuario["bandeja"]) {
                    if ($valimp && $color_a[0] == "FFFF66") {
                        if ($indicad_mostrar_condic) {
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

                            if ($manredes == 1) {
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
                } else if ($valimp) {
                    echo "<div style='display:none'>" . $desenruta[$i]["bandeja"] . "</div>";
                    $indicad_mostrar_condic = 1;

                    if ($indicad_mostrar_condic) {
                        //vlix85
                        $_EAL = TRUE;
                        //-----------------------------------------------------------------------------------------------------
                        if (in_array($_SESSION["datos_usuario"]['cod_perfil'], array(COD_PERFIL_ADMINIST, COD_PERFIL_SUPEFARO, COD_PERFIL_SUPERUSR, COD_PERFIL_SUPEGENE))) {
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
                        if ($_EAL && in_array($_SESSION["datos_usuario"]['cod_perfil'], array(COD_PERFIL_ADMINIST, COD_PERFIL_SUPEFARO, COD_PERFIL_SUPERUSR, COD_PERFIL_SUPEGENE)) && (int) $transpor[0][2] != 2) {
                            $formulario->linea($desenruta[$i][1], 0, "i", 0, 0, "center\" onclick=\"poppc($vlix1017);", "#7fff99; cursor:pointer");
                        } else {
                            $formulario->linea($desenruta[$i][1], 0, "i", 0, 0, "center", "#FFF");
                        }
                        //-------------------------------------------
                        //$formulario->linea($desenruta[$i]['aseguradora'], 0, "i");
                        //$formulario->linea(( (int) $llamadas1 + (int) $llamadas2), 0, "i");

                        if ($manredes == 1) {
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


        if ($datos_usuario["bandeja"]) {
            $formulario->nueva_tabla();
            $formulario->linea("Se Encontro un Total de " . $en_bandeja . " Despacho(s) con Alarma Amarilla", 0, "t2");
        }

        //if($ind_porlleg && !$des_retras && !$desfut && $_REQUEST['alacla']!='ecl')
        if ($ind_porlleg && !$des_retras && !$desfut) {

            $formulario->nueva_tabla();


            if (!$datos_usuario["bandeja"]) {

                if (!$finali) {
                    if ($GLOBALS_ADD[0]["campo"] == "alacla" && $GLOBALS_ADD[0]["valor"] != "L" && $GLOBALS_ADD[0]["valor"])
                        $totalultdes = 0;
                    else
                        $totalultdes = $ind_porlleg;

                    if ($manredes == 1 && $desurb == 1)
                        $formulario->linea("Se Encontro un Total de <span id='tot2ID'>" . $totalultdes . "</span> Despacho(s) Urbanos &oacute; Pendientes por Llegada", 0, "t2");
                    else
                        $formulario->linea("Se Encontro un Total de <span id='tot2ID'>" . $totalultdes . "</span> Despacho(s) Pendientes por Llegada", 0, "t2");
                } else
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

                if ($finali) {
                  $formulario->linea("No. Viaje", 0, "t");
                }
                
                if ($manredes == 1) {
                    $formulario->linea("Remisi&oacute;n", 0, "t");
                    $formulario->linea("Pedido", 0, "t");
                }

                if (!$des_retras && $finali) {
                    $formulario->lista_titulo("Origen", "origen\" onChange=\"form_item.submit()\"", $origen, 0);
                    $formulario->lista_titulo("Destino", "destino\" onChange=\"form_item.submit()\"", $destino, 0);
                    $formulario->lista_titulo("Transportadora", "transp\" onChange=\"form_item.submit()\"", $transpors, 0);
                    $formulario->lista_titulo("Placas", "bplaca\" onChange=\"form_item.submit()", $placax, 0); 
                } else {
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

            
            for ($i = 0; $i < sizeof($porllegad); $i++) {
                //Si le dieron clic a los despachos por fuera de plataforma, los despachos pendientes por dar llegada no son necesarios
                if ($_REQUEST['FUEPLA'] == '1') {
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

                if (!$liscon) {
                    $porllegad[$i][8] = $maximo[0][0];
                    $porllegad[$i][9] = $nom_contro[0];
                    $porllegad[$i][10] = $nom_noveda[0];
                } else
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

                if ($datos_usuario["bandeja"]) {
                    
                } else if ($indicad_mostrar_condic) {
                    //vlix85
                    $_EAL1 = TRUE;
                    //-----------------------------------------------------------------------------------------------------
                    if (in_array($_SESSION["datos_usuario"]['cod_perfil'], array(COD_PERFIL_ADMINIST, COD_PERFIL_SUPEFARO, COD_PERFIL_SUPERUSR, COD_PERFIL_SUPEGENE))) {
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
                    if ($_EAL1 && in_array($_SESSION["datos_usuario"]['cod_perfil'], array(COD_PERFIL_ADMINIST, COD_PERFIL_SUPEFARO, COD_PERFIL_SUPERUSR, COD_PERFIL_SUPEGENE)) && (int) $transpor[0][2] != 2) {
                        $formulario->linea($porllegad[$i][1], 0, "i", 0, 0, "center\" onclick=\"poppc($vlix1017);", "#7fff99; cursor: pointer;");
                    } else {
                        $formulario->linea($porllegad[$i][1], 0, "i", 0, 0, "center", "#FFF");
                    }
                    
                    if ($finali) {
                      $formulario->linea($porllegad[$i]['num_desext'], 0, $estilo_celda);
                    }
                    
                    //-------------------------------------------
                    //$formulario -> linea($porllegad[$i][1],0,$estilo_celda); OLD

                    if ($manredes == 1) {
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

        if (!$ind_porlleg && !$desfut) {
            $formulario->nueva_tabla();
            if (!$finali)
                $formulario->linea("No Se Encontrar&oacute;n Despachos Pendientes por dar Llegada", 0, "t2");
            else
                $formulario->linea("No Se Encontrar&oacute;n Despachos Finalizados", 0, "t2");
        }


        echo "<script language=\"JavaScript\"> document.getElementById('tot2ID').innerHTML = '" . $count . "';</script>\n";

        $this->paginador->getPaginas($formulario, "npagina", $varadici);

        $formulario->nueva_tabla();



        if ($GLOBALS_ADD) {

            for ($i = 0; $i < sizeof($GLOBALS_ADD); $i++)
                $formulario->oculto($GLOBALS_ADD[$i]["campo"], $GLOBALS_ADD[$i]["valor"], 0);
        }

        if ($GLOBALS_ADD["atras"]) {
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

        if ($_REQUEST["defini"] == 0 || $_REQUEST["defini"] == 1) {
            echo "<script language=\"JavaScript\">";
            echo "document.getElementById('definiID').value=" . $_REQUEST["defini"] . ";";
            echo "</script>";
        }
    }

    public function IMPMAT( $parametros , $die = null )
    {
      echo '<pre>';
        print_r( $parametros );
      echo '</pre>';

      if( $die )
      {
        die();
      }//Fin If die
    }//FIN FUNCION IMPMAT  

    //Esta funcion se utlizara para incluir los js y estilos en las funciones Encabezado y EncabezadoConsolidacion
    public function IncludeJsStyle()
    {
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
        IncludeJS( 'par_confir_pernoc.js' );
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/jquery.css' type='text/css'>";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/informes.css' type='text/css'>";
        echo "<link rel='stylesheet' href='../" . DIR_APLICA_CENTRAL . "/estilos/validator.css' type='text/css'>";
    }//Fin Funcion IncludeJsStyle

    public function verifyConsolHijo( $despac )
    {
        $sql = "SELECT 
                    1 
                FROM 
                    ".BASE_DATOS.".tab_consol_despac 
                WHERE 
                    cod_deshij = '".$despac."'; ";

        $consulta = new Consulta( $sql , $this -> conexion );
        $count = $consulta->ret_num_rows();

        return $result = $count > 0 ? true : false ;
    }

    function getDespacPadre( $despac )
    {
      $sql = "SELECT 
                    cod_despad 
                FROM 
                    ".BASE_DATOS.".tab_consol_despac 
                WHERE 
                    cod_deshij = '".$despac."'; ";

        $consulta = new Consulta( $sql , $this -> conexion );
        $count = $consulta->ret_matriz();

        return $count[0][0];   
    }

    //Funcion que verifica si el despacho actual es un despacho consolidado
    public function verifyConsol( $despac )
    {
        $sql = "SELECT 
                    1 
                FROM 
                    ".BASE_DATOS.".tab_consol_despac 
                WHERE 
                    cod_despad = '".$despac."'; ";

        $consulta = new Consulta( $sql , $this -> conexion );
        $count = $consulta->ret_num_rows();

        return $result = $count > 0 ? true : false ;
    }//Fin Funcion verifyConsol

    public function getViajesConsol( $despac )
    {
        $sql = "SELECT 
                    cod_deshij
                FROM 
                    ".BASE_DATOS.".tab_consol_despac 
                WHERE 
                    cod_despad = '".$despac."'; ";

        $consulta = new Consulta( $sql , $this -> conexion );
        $viajesConsol = $consulta->ret_matriz();

        for( $i = 0 ; $i < sizeof( $viajesConsol ) ; $i++ )
        {
            $viajes[] = $viajesConsol[$i]['cod_deshij'];
        }//FIN FOR

        //Uniendo todos los viajes a un solo parametroy haciendo el retorno de los mismos
        return $viajes = implode(',', $viajes);
    }

    //Esta funcion une la informacion de 2 o mas registros para mostrarlos en un solo campo
    public function ConcatenaInfo( $array )
    {
        //$this -> IMPMAT( $array );

        //Obteniendo el tamaño del array
        $size = sizeof( $array );

        //Obteniendo el tamaño de los arreglos internos
        $sizeInterno = ((sizeof($array[0])/2)-1);

        //-----------------------------------------------
        //Sacando los indices alfanumericos
        $indice = array_keys( (array)$array[0] );
        //$this -> IMPMAT( $indice );

        //guardando los indices alfanumericos
        for( $i = 0 ; $i < sizeof( $indice ) ; $i++  )
        {
            $i++;
            $indAlf[] = $indice[$i];
        }
        //-----------------------------------------------

        //Recorriendo el arreglo para organizarlo
        for( $i = 0 ; $i <= $sizeInterno; $i++ )
        {
            $array_temp = NULL;

            //Concatenando la informacion 
            for( $j = 0 ; $j < $size; $j++ )
            {
                if ( !in_array( $array[$j][$i] , (array)$result[$i] ) ) 
                {
                    $array_temp[] = $array[$j][$i];
                }//Validacion del array

                $result[$i] = implode(', ', $array_temp);
                $result[$indAlf[$i]] = $result[$i];
            }//Fin For

            //echo $result[$i],'<br>';

        }//Fin Funcion 

        return $result;
    }//Fin Funcion ConcatenaInfo

    public function getIndRemdes()
    {
        $query = "SELECT a.ind_remdes
                  FROM ".BASE_DATOS.".tab_config_parame a
                  WHERE a.ind_remdes = '1' ";
        $consulta = new Consulta($query, $this->conexion);
        return $manredes = $consulta->ret_matriz();
    }//Fin Funcion getIndRemdes

    public function getInfoEncab1( $despac , $indConsol )
    {
        $query = "SELECT 
                    a.num_despac, 
                    a.cod_manifi, 
                    IF( b.nom_conduc IS NOT NULL, b.nom_conduc, c.abr_tercer) AS abr_tercer, 
                    IF( b.cod_condu2 IS NOT NULL, b.cod_condu2, c.cod_tercer ) AS cod_tercer,
                    IF( a.con_telmov IS NULL OR a.con_telmov = '', c.num_telmov, a.con_telmov ),
                    IF( a.con_telef1 IS NULL OR a.con_telef1 = '', c.num_telef1, a.con_telef1),
                    ind_defini,
                    e.nom_operad, 
                    f.nom_califi, 
                    a.tie_contra, 
                    a.ind_tiemod, 
                    a.obs_tiemod, 
                    IF(a.fec_citcar Is Null,'SIN CONFIRMAR',DATE_FORMAT( CONCAT( a.fec_citcar, ' ', a.hor_citcar ),'%H:%i %d-%m-%Y')) as fec_citcar, 
                    m.nom_tipdes
                  FROM 
                    " . BASE_DATOS . ".tab_despac_despac a,
                    " . BASE_DATOS . ".tab_despac_vehige b,
                    " . BASE_DATOS . ".tab_genera_tipdes m,
                    " . BASE_DATOS . ".tab_tercer_tercer c,
                    ". BASE_DATOS .".tab_tercer_conduc d 
                    LEFT JOIN ".  BASE_DATOS .".tab_genera_califi f ON f.cod_califi = d.cod_califi
                    LEFT JOIN ".  BASE_DATOS .".tab_operad_operad e ON e.cod_operad = d.cod_operad
                  WHERE 
                    a.num_despac = b.num_despac AND
                    b.cod_conduc = c.cod_tercer AND
                    a.cod_tipdes = m.cod_tipdes AND
                    d.cod_tercer = c.cod_tercer AND
                    a.num_despac IN ( " . $despac . " ) ";
        $consulta = new Consulta($query, $this->conexion);
        $encab1 = $consulta->ret_matriz();

        if( $indConsol )
        {
            $encab1 = $this -> ConcatenaInfo( $encab1 );    
            $encab1[0] = $encab1;
        }//Fin If comprobacion consolidacion

        //$this -> IMPMAT( $encab1 );
        return $encab1;
    }//Fin Funcion getInfoEncab1

    public function getInfoEncab2( $despac , $indConsol )
    {
        $query = "SELECT 
                    b.num_placax , d.nom_marcax , e.nom_lineax , 
                    f.nom_colorx , g.nom_config , h.nom_carroc , 
                    c.ano_modelo
                  FROM 
                    " . BASE_DATOS . ".tab_despac_vehige b,
                    " . BASE_DATOS . ".tab_genera_marcas d,
                    " . BASE_DATOS . ".tab_vehige_lineas e,
                    " . BASE_DATOS . ".tab_vehige_colore f,
                    " . BASE_DATOS . ".tab_vehige_carroc h,
                    " . BASE_DATOS . ".tab_vehicu_vehicu c LEFT JOIN " . BASE_DATOS . ".tab_vehige_config g ON c.num_config = g.num_config 
                  WHERE 
                    b.num_placax = c.num_placax AND
                    c.cod_marcax = d.cod_marcax AND
                    c.cod_marcax = e.cod_marcax AND
                    c.cod_lineax = e.cod_lineax AND
                    c.cod_colorx = f.cod_colorx AND
                    c.cod_carroc = h.cod_carroc AND
                    b.num_despac IN (" . $despac . ") ";
        $consulta = new Consulta($query, $this->conexion);
        $encab2 = $consulta->ret_matriz();

        if( $indConsol )
        {
            $encab2 = $this -> ConcatenaInfo( $encab2 );    
            $encab2[0] = $encab2;
        }//Fin If comprobacion consolidacion

        //$this -> IMPMAT( $encab2 );
        return $encab2;
    }//Fin Funcion getInfoEncab2  

    public function getInfoEncab3( $despac , $indConsol )
    {
        $query = "SELECT 
                    g.nom_agenci , 
                    e.nom_rutasx , 
                    a.cod_ciuori , 
                    a.cod_ciudes , 
                    IF(a.fec_salida Is Null,'SIN CONFIRMAR',DATE_FORMAT(a.fec_salida,'%H:%i %d-%m-%Y')) ,
                    IF(b.fec_llegpl Is Null,'SIN CONFIRMAR',DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y')) ,
                    DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),
                    DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y') ,
                    a.gps_operad ,
                    IF(a.gps_usuari = '' OR a.gps_usuari IS NULL, k.nom_usrgps, a.gps_usuari) AS gps_usuari ,
                    IF(a.gps_paswor = '' OR a.gps_paswor IS NULL, k.clv_usrgps, a.gps_paswor) AS gps_paswor ,
                    DATE_FORMAT(a.fec_salsis,'%H:%i %d-%m-%Y'),
                    b.cod_transp , 
                    h.abr_tercer AS aseguradora , 
                    a.num_poliza , 
                    i.nom_operad AS nom_opegps ,
                    IF(a.gps_paswor = '' OR a.gps_paswor IS NULL, 2, 1) AS ind_paswor , 
                    j.abr_tercer AS generador , 
                    IF(b.fec_findes Is Null,DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y') , 
                    DATE_FORMAT(b.fec_findes ,'%H:%i %d-%m-%Y')) as fec_findes
                  FROM 
                    " . BASE_DATOS . ".tab_despac_vehige b,
                    " . BASE_DATOS . ".tab_genera_rutasx e,
                    " . BASE_DATOS . ".tab_genera_agenci g,
                    " . BASE_DATOS . ".tab_despac_despac a LEFT JOIN
                    " . BASE_DATOS . ".tab_despac_gpsxxx k ON a.num_despac = k.num_despac LEFT JOIN
                    " . BD_STANDA  . ".tab_genera_opegps i ON k.cod_opegps = i.cod_operad
                    LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer h ON a.cod_asegur = h.cod_tercer 
                    LEFT JOIN " . BASE_DATOS . ".tab_tercer_tercer j ON a.cod_client = j.cod_tercer 
                  WHERE 
                    a.num_despac = b.num_despac AND
                    b.cod_rutasx = e.cod_rutasx AND
                    b.cod_agenci = g.cod_agenci AND
                    a.num_despac IN (" . $despac . ") "; 

        //$this -> IMPMAT( $query );

        $consulta = new Consulta($query, $this->conexion);
        $encab3 = $consulta->ret_matriz();

        if( $indConsol )
        {
            $encab3 = $this -> ConcatenaInfo( $encab3 );    
            $encab3[0] = $encab3;
        }//Fin If comprobacion consolidacion

        //$this -> IMPMAT( $encab3 );
        return $encab3;
    }//Fin Funcion getInfoEncab3  

    public function getInfoEncab99( $despac , $indConsol )
    {
        $query0 = "SELECT 
                    c.abr_tercer , a.val_declar , a.nom_mercan , a.num_poliza
                   FROM 
                    " . BASE_DATOS . ".tab_despac_poliza a,
                    " . BASE_DATOS . ".tab_despac_despac b,
                    " . BASE_DATOS . ".tab_tercer_tercer c
                   WHERE 
                    a.num_despac = b.num_despac
                    AND a.cod_tomado = c.cod_tercer
                    AND a.num_despac IN (".$despac.") ";
        
        $consulta = new Consulta($query0, $this->conexion);
        $encab99 = $consulta->ret_matriz();

        //$this -> IMPMAT( $encab99 );

        if( $indConsol && $encab99 )
        {
            $encab99 = $this -> ConcatenaInfo( $encab99 );    
            $encab99[0] = $encab99;
        }//Fin If comprobacion consolidacion

        //$this -> IMPMAT( $encab99 );
        return $encab99;
    }//Fin Funcion getInfoEncab99

    /*! \fn: Encabezado
     *  \brief: Encabezado del despacho
     *  \author: Ing. Fabian Salinas
     *  \date: 03/09/2010
     *  \date modified: dia/mes/año
     *  \param: mNumDespac
     *  \param: mDataUsuar
     *  \param: mIndFinali
     *  \param: mNomExp
     *  \return:
     */
    function Encabezado($mNumDespac, $mDataUsuar, $mIndFinali = 0, $mRuta = NULL, $mReturn = false)
    {
        self::IncludeJsStyle();

        echo '<style>
                .ocultarTR{
                  display:none
                }
              </style>
             ';

        $mObs = array('NA', 'DESCONOCIDA');
        $mView = self::getView('jso_encabe');

        #<Verifica Viaje Consolidado>
            $mNumDesOri = $mNumDespac; #Guarda el Despacho Original
            $mIndConsol = self::verifyConsol( $mNumDespac );

            if( $mIndConsol )
            {
                $mNumConsol = $this -> getViajesConsol( $mNumDespac ); #Trayendo los despachos consolidados para este despacho
                $mNumDespac = $mNumConsol;
                $mNumConsol = explode(",", $mNumConsol);

                //Codigo que agrega un link a los numeros de consolidados
                for( $i = 0; $i < sizeof($mNumConsol); $i++ )
                    $mNumConsol[$i] = '<a href="index.php?cod_servic=3302&window=central&despac='.$mNumConsol[$i].'&opcion=1" target="_blank" style="color:green;">'.$mNumConsol[$i].'</a>';

                $mNumConsol = implode(", ", $mNumConsol);
                $mStrDespac = $mNumDesOri . ' (Consolidado '.$mNumConsol.')'; #String Despacho
                $mIndConsol = 1;
            }
            else
            {
                $mIndConhij = self::verifyConsolHijo( $mNumDespac );

                if( $mIndConhij )
                {
                    $mNumDesPad = self::getDespacPadre( $mNumDespac ); # Numero Despacho Padre
                    $mLink = '<a href="index.php?cod_servic=3302&window=central&despac='.$mNumDesPad.'&opcion=1" target="_blank">'.$mNumDesPad.'</a>';
                    $mStrDespac = $mNumDespac." Consolidado a: ".$mLink; #String Despacho
                } 
                else
                    $mStrDespac = $mNumDespac; #String Despacho

                $mIndConsol = 0;
            }
        #</Verifica Viaje Consolidado>

        #mData información de despacho
        $mData = self::getDataEncabezado( $mNumDesOri, $mNumDespac, $mIndConsol, $mObs );
        $mData['num_despac'] = $mStrDespac;

        #<Arma HTML>
            $mHtml = new Formlib(2);

            $mHtml->Table('tr');
                $mHtml->SetBody("<td>");
                    $mHtml->OpenDiv("id:contentID; class:contentAccordion ancho");

                        #Datos Despacho
                        $mHtml->SetBody( self::divDatosDespac( $mNumDesOri, $mData, $mView) );

                        #Grilla Cargue
                        if( $mView->gri_cargax->ind_visibl == 1 ){
                            $mGrillaCargue = self::divGrillaCargue( $mNumDespac, $mNumDesOri );
                            if( $mGrillaCargue != false )
                                $mHtml->SetBody( $mGrillaCargue );
                        }

                        #Remesas
                        if( $mView->gri_descar->ind_visibl == 1 ){
                            $mRemesas = self::divRemesas( $mNumDesOri );
                            if( $mRemesas != false )
                                $mHtml->SetBody( $mRemesas );
                        }

                        #Grilla Descargue
                        if( $mView->gri_descar->ind_visibl == 1 ){
                            $mGrillaDescar = self::divGrillaDescar( $mNumDesOri );
                            if( $mGrillaDescar != false )
                                $mHtml->SetBody( $mGrillaDescar );
                        }

                        #Mapa Localizacion novedades GPS
                        if( $mView->sec_mapaxx->ind_visibl == 1 ){
                            $mValidaMapa = self::validaDivMapas($mNumDespac);

                            if( $mValidaMapa['ind_estado'] == 1){
                                $mapasHtml = self::divMapas($mValidaMapa['data']);
                                $mHtml->SetBody( $mapasHtml );
                            }
                        }

                        #Bitacora de cambios
                        if( $mView->bit_cambio->ind_visibl == 1 ){
                            $mBitacoCambio = self::divBitacoCambio();
                            if( $mBitacoCambio != false )
                                $mHtml->SetBody( $mBitacoCambio );
                        }

                        #Fotos
                        if( $mView->sec_fotosx->ind_visibl == 1 ){
                            $mFotos = self::divFotos( $mNumDesOri );
                            if( $mFotos != false )
                                $mHtml->SetBody( $mFotos );
                        }

                        #Plan de Ruta
                        if( $mRuta )
                            $mHtml->SetBody( self::PlanDeRuta( $mNumDesOri, $mRuta['link'], $mRuta['finali'], $mRuta['opcurban'], $mDataUsuar, $mRuta['lleg'], $mRuta['tie_ultnov']) );

                    $mHtml->CloseDiv();
                $mHtml->CloseRow('td');

                $mHtml->Row('td');
                $mHtml->Hidden( array("name"=>"based", "id"=>"basedID", "value"=>BASE_DATOS) );
                $mHtml->Hidden( array("name"=>"dir_aplica", "id"=>"dir_aplicaID", "value"=>DIR_APLICA_CENTRAL) );
                $mHtml->Hidden( array("name"=>"num_despac", "id"=>"num_despacID", "value"=>$mNumDesOri) );
                $mHtml->Hidden( array("name"=>"cod_servic", "id"=>"cod_servicID", "value"=>$_REQUEST['cod_servic']) );
                $mHtml->SetBody('</td></tr>');

              $mHtml->Row('td');
                $mHtml->SetBody( self::scriptEncabezado() );  
              $mHtml->SetBody('</td>');
            $mHtml->CloseTable('tr');
        #</Arma HTML>

        if( $mReturn == true )
            return $mHtml->MakeHtml();
        else
            echo $mHtml->MakeHtml();
    }

    /*! \fn: sanear_string
     *  \brief: quita acentos del string enviado
     *  \author: Ing. Miguel Romero
     *    \date: 04/03/2015
     *    \date modified: dia/mes/aÃ±o
     *  \param: string  = string a sanear
     *  \return string sin acentos
     */
    private function sanear_string($string)
    {
        $string = (string)trim(utf8_encode($string));
     
        $string = str_replace(
            array('Ã¡', 'Ã ', 'Ã¤', 'Ã¢', 'Âª', 'Ã?', 'Ã€', 'Ã‚', 'Ã„'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
     
        $string = str_replace(
            array('Ã©', 'Ã¨', 'Ã«', 'Ãª', 'Ã‰', 'Ãˆ', 'ÃŠ', 'Ã‹'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );
     
        $string = str_replace(
            array('Ã­', 'Ã¬', 'Ã¯', 'Ã®', 'Ã?', 'ÃŒ', 'Ã?', 'ÃŽ'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
     
        $string = str_replace(
            array('Ã³', 'Ã²', 'Ã¶', 'Ã´', 'Ã“', 'Ã’', 'Ã–', 'Ã”'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
     
        $string = str_replace(
            array('Ãº', 'Ã¹', 'Ã¼', 'Ã»', 'Ãš', 'Ã™', 'Ã›', 'Ãœ'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
     
        $string = str_replace(
            array('Ã±', 'Ã‘', 'Ã§', 'Ã‡'),
            array('n', 'N', 'c', 'C',),
            $string
        );
     
        //Esta parte se encarga de eliminar cualquier caracter extraÃ±o
        $string = str_replace(
            array("Â·", "$", "%", "&", "/",
                 "(", ")", "?", "'", "Â¡",
                 "Â¿", "[", "^", "<code>", "]",
                 "+", "}", "{", "Â¨", "Â´",
                 ">", "< ", ";", ",", ":",
                 "."),
            '',
            $string
        );

        $string = str_replace( array('VÃ­a'), array('via'), $string);
     
        return $string;
    }

    /*! \fn: validaDivMapas
     *  \brief:  valida si el despacho tiene novedades de GPS 4999
     *  \author: Ing. Miguel Romero
     *    \date: 04/03/2016
     *    \date modified: dia/mes/aÃ±o
     *  \param: mNumDespac
     *  \return arreglo con las novedades y el estado de la novedad
     */
    
    private function validaDivMapas($mNumDespac){
        $novedades = self::getNovedad($mNumDespac , 'LEFT');
 
        $flag = 0;
        $latLon = array( ); 
        foreach ($novedades as $key => $value) {
  
            $value['obs_noveda'] = self::sanear_string( $value['obs_noveda'] );
            
            if($value['val_longit'] != '' && $value['val_latitu'] != ''){
                $latLon[] = array(
                        "val_latitu" => $value['val_latitu'],
                        "val_longit" => $value['val_longit'],
                        "fec_noveda" => $value['fec_noveda'],
                        "obs_noveda" => $value['obs_noveda']
                    );

                $flag = 1;
            }
        }

        return array('ind_estado' => $flag , 'data' => $latLon);
    }

    /*! \fn: divMapas
     *  \brief: funcion que retorna el html del div donde se va a pintar el mapa
     *  \author: Ing. Miguel Romero
     *    \date: 04/03/2016
     *    \date modified: dia/mes/aÃ±o
     *  \param: mNovedades 
     *  \return retorna el html del div donde se va a pintar el mapa
     */
    

    private function divMapas( $mNovedades ){
        $mHtml = new Formlib(2);
        $mHtml -> SetBody('    <style>
                              .ol-popup {
                                position: absolute;
                                background-color: white;
                                -webkit-filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
                                filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
                                padding: 15px;
                                border-radius: 10px;
                                border: 1px solid #cccccc;
                                bottom: 12px;
                                left: -50px;
                                min-width: 280px;
                              }
                              .ol-popup:after, .ol-popup:before {
                                top: 100%;
                                border: solid transparent;
                                content: " ";
                                height: 0;
                                width: 0;
                                position: absolute;
                                pointer-events: none;
                              }
                              .ol-popup:after {
                                border-top-color: white;
                                border-width: 10px;
                                left: 48px;
                                margin-left: -10px;
                              }
                              .ol-popup:before {
                                border-top-color: #cccccc;
                                border-width: 11px;
                                left: 48px;
                                margin-left: -11px;
                              }
                              .ol-popup-closer {
                                text-decoration: none;
                                position: absolute;
                                top: 2px;
                                right: 8px;
                              }
                              .ol-popup-closer:after {
                                content: "✖";
                                color:#000000
                              }
                            </style>');

                 //------ SOLO PARA ESTE CASO, CAMBIAR ---------
        
        echo "<script src=\"https://code.jquery.com/jquery-1.11.2.min.js\"></script>";
        #echo " <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css\">";
        #echo " <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js\"></script>";
        
        echo "<link rel=\"stylesheet\" href=\"../" . DIR_APLICA_CENTRAL . "/estilos/ol.css\" type=\"text/css\">";
        echo "<script src=\"../" . DIR_APLICA_CENTRAL . "/js/ol.js\"></script>";
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/mapaGoogle.js\"></script>\n"; 
        //echo '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?"></script>';


        //------------------ FIN ----------------------
        

        $mHtml->OpenDiv("id:mapaID; class:accordion");

            $mHtml->SetBody("<h3 style='padding:6px;'><center>VER RECORRIDO DEL VEHICULO</center></h3>");
            //----------------------

            $mHtml->OpenDiv("id:secID");
                //----------------------

                $mHtml->OpenDiv("id:form_mapaID; class:contentAccordionForm; style:'float: left;' ");

                    $mHtml->Hidden( array("name"=>"dat_gpsxxx".$i, "id"=>'dat_gpsxxx'.$i, "value"=> base64_encode(json_encode($mNovedades))) );

                $mHtml->CloseDiv();
                //----------------------

                //----------------------

                $mHtml->OpenDiv("id:popupContenedor; class:ol-popup");
                    $mHtml -> SetBody('<a href="#" id="popup-closer" class="ol-popup-closer"></a>');
                    $mHtml -> OpenDiv('id:popupContent');
                    $mHtml -> CloseDiv(); 
                $mHtml->CloseDiv();

                //----------------------

                //----------------------
                //$mHtml->OpenDiv("id:footer;");
                //    $mHtml->SetBody('&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors');
                //$mHtml->CloseDiv();
                //----------------------

            $mHtml->CloseDiv();
            //----------------------

        $mHtml->CloseDiv(); 

        return $mHtml->MakeHtml();
    }


    /*! \fn: divDatosDespac
     *  \brief: Crea los DIV de las secciones: "DATOS BASICOS DEL DESPACHO", "DATOS COMPLEMENTARIOS DEL DESPACHO", "FECHAS DEL DESPACHO"
     *  \author: Ing. Fabian Salinas
     *  \date: 14/09/2015
     *  \date modified: dia/mes/año
     *  \param: mNumDespac  Integer  Numero del Despacho
     *  \param: mData       Matriz   Información de despacho
     *  \return: HTML
     */
    private function divDatosDespac( $mNumDespac, $mData,  $mView )
    {
        #Datos Adicionales del despacho
        $mTittle['dat_adicio'] = array( "nom_operad" => "", 
                                        "nom_lineax" => "Linea", 
                                        "nom_califi" => "Calificaci&oacute;n", 
                                        "fec_llegad" => "Fecha Llegada", 
                                        "cod_ciuori" => "", 
                                        "cod_ciudes" => "", 
                                        "cod_transp" => "", 
                                        "ind_pasgps" => "", 
                                        "ind_tiemod" => "", 
                                        "aux_placax" => "" 
                                        );

        $mTittle[0] = array( "id" => "dat_basico", "tittle" => "DATOS BASICOS DEL DESPACHO");
        $mTittle[0]['data'] = array("nom_transp" => "Transportadora", 
                                    "nom_agenci" => "Agencia", 
                                    "num_despac" => "Despacho", 
                                    "nom_ciuori" => "Origen", 
                                    "cod_manifi" => "Manifiesto", 
                                    "nom_ciudes" => "Destino", 
                                    "num_desext" => "N&uacute;mero de Viaje", 
                                    "nom_rutasx" => "Ruta", 
                                    "num_solici" => "No. Solicitud", 
                                    "nom_produc" => "Mercancia", 
                                    "nom_conduc" => "Conductor", 
                                    "cod_conduc" => "C.C.", 
                                    "con_telmov" => "Celular", 
                                    "nom_tipdes" => "Modalidad", 
                                    "con_telef1" => "Telefono", 
                                    "tip_transp" => "Tipo Transportadora", 
                                    "num_placax" => "Placa", 
                                    "nom_produc" => "Mercancia", 
                                    "tip_vehicu" => "Tipo Vehiculo", 
                                    "can_noveda" => "No. Novedades" 
                                    );

        $mTittle[1] = array( "id" => "dat_comple", "tittle" => "DATOS COMPLEMENTARIOS DEL DESPACHO");
        $mTittle[1]['data'] = array("nom_colorx" => "Color", 
                                    "nom_poseed" => "Poseedor", 
                                    "ano_modelo" => "Modelo", 
                                    "nom_genera" => "Generador", 
                                    "nom_marcax" => "Marca", 
                                    "nom_opegps" => "Operador Gps", 
                                    "nom_carroc" => "Carroceria", 
                                    "gps_usuari" => "Usuario Gps", 
                                    "num_config" => "Configuraci&oacute;n", 
                                    "gps_paswor" => "Contrase&ntilde;a Gps", 
                                    "nom_asegur" => "Aseguradora", 
                                    "num_poliza" => "Num Poliza", 
                                    "tie_contra" => "Tiempo Modificado para el Despacho", 
                                    "obs_tiemod" => "Observaciones de Modificaci&oacute;n", 
                                    "num_desant" => "tit_desant"
                                    );

        $mTittle[2] = array( "id" => "dat_fechas", "tittle" => "FECHAS DEL DESPACHO");
        $mTittle[2]['data'] = array("fec_creaci" => "Hora/Fecha Sistema", 
                                    "fec_salida" => "Fecha Salida del Despacho", 
                                    "fec_despac" => "Fecha de Creaci&oacute;n", 
                                    "fec_citcar" => "Fecha Cita de Cargue", 
                                    "fec_llegpl" => "Fecha Planeada Llegada", 
                                    "fec_findes" => "Fecha Fin Descargue", 
                                    "fec_plalle" => "Fecha Llegada a Planta", 
                                    "fec_salpla" => "Fecha Salida de Planta" 
                                    );

        if( $mView->dat_telefo->ind_visibl != '1' )
        { #No tiene permisos de visualizacion de telefonos
            unset($mTittle[0]['data']['con_telmov']);
            unset($mTittle[0]['data']['con_telef1']);
        }

        $mHtml = new Formlib(2);

        for ($i=0; $i<3; $i++)
        {
            if( $mView->$mTittle[$i]['id']->ind_visibl == 1 )
            {#Pinta el DIV si el perfil tiene parametrizado su visualizacion
                $mHtml->OpenDiv("id:".$mTittle[$i][id]."ID; class:accordion");
                    $mHtml->SetBody("<h3 style='padding:6px;'><center>".$mTittle[$i]['tittle']."</center></h3>");
                    $mHtml->OpenDiv("id:sec".$i."ID");
                        $mHtml->OpenDiv("id:form".$i."ID; class:contentAccordionForm");
                            $mHtml->Table("tr");

                                $j=0;
                                foreach ($mTittle[$i]["data"] as $key => $tittle)
                                {
                                    $mBandera = true;

                                    switch ($key)
                                    {
                                        case 'con_telmov':
                                            $mTxt = '<a style="color:green;" href="sip:'.$mData[$key].'_'.$mData['aux_placax'].'_'.$mNumDespac.'@pbx.intrared.net" target="blank">'.$mData[$key].'</a>';
                                            $mTxt = array($tittle, $mData['nom_operad']." - ".$mTxt);
                                            break;

                                        case 'gps_paswor':
                                            $mTxt = $mData['ind_pasgps'] == 2 ? base64_decode($mData[$key]) : $mData[$key];
                                            $mTxt = array($tittle, $mTxt);
                                            break;

                                        case 'fec_llegpl':
                                            if( $mData['fec_llegad'] == $mObs[1] )
                                                $mTxt = array($tittle, $mData[$key]);
                                            else
                                                $mTxt = array($mTittle['dat_adicio']['fec_llegad'], $mData['fec_llegad']);
                                            break;

                                        case 'tie_contra':
                                            if( $mData[$key] == '' )
                                                $mBandera = false;
                                            else
                                                $mTxt = array($tittle, $mData[$key]);
                                            break;

                                        case 'obs_tiemod':
                                            if( $mData['tie_contra'] == '' )
                                                $mBandera = false;
                                            else
                                                $mTxt = array($tittle, $mData[$key]);
                                            break;

                                        case 'num_desant':
                                            if( $mData[$key] == '' )
                                                $mBandera = false;
                                            else
                                                $mTxt = array($mData[$tittle], $mData[$key]);
                                            break;
                                        
                                        default:
                                            $mTxt = array($tittle, utf8_encode($mData[$key]));
                                            break;
                                    }

                                    if( $mBandera == true ){
                                        if( $j != 0 && ($j % 2) == false ) $mHtml->Row();
                                        $mHtml->Label( $mTxt[0].":",  array("width"=>"25%", "align"=>"right", "class"=>"celda_titulo") );
                                        $mHtml->SetBody('<td width="25%" align="left" class="celda_info">'.$mTxt[1].'</td>');
                                        if( $j % 2 ) $mHtml->SetBody('</tr>');
                                        $j++;
                                    }
                                }

                            $mHtml->SetBody("</table>");
                        $mHtml->CloseDiv();
                    $mHtml->CloseDiv();
                $mHtml->CloseDiv();
            }
        }

        return $mHtml->MakeHtml();
    }

    /*! \fn: divGrillaCargue
     *  \brief: Crea DIV de la seccion: GRILLA CARGUE
     *  \author: Ing. Fabian Salinas
     *  \date: 14/09/2015
     *  \date modified: dia/mes/año
     *  \param: mNumDespac  Integer  Numero del Despacho
     *  \return: HTML
     */
    private function divGrillaCargue( $mNumDespac, $mNumDesOri )
    {
        $mDespac = explode(',', $mNumDespac);
        $mHtml = new Formlib(2);

        $mHtml->OpenDiv("id:cargueID; class:accordion");
            $mHtml->SetBody("<h3 style='padding:6px;'><center>GRILLA CARGUE</center></h3>");
            $mHtml->OpenDiv("id:secID");
                $mHtml->OpenDiv("id:form_cargueID; class:contentAccordionForm");
                
                    foreach ($mDespac as $despac) {
                        $mHtml->SetBody( self::GridCitaCargue($despac, $mNumDesOri) );
                    }
                        
                $mHtml->CloseDiv();
            $mHtml->CloseDiv();
        $mHtml->CloseDiv();

        return $mHtml->MakeHtml();
    }

    /*! \fn: divGrillaDescar
     *  \brief: Crea DIV de la seccion: GRILLA DESCARGUE
     *  \author: Ing. Fabian Salinas
     *  \date: 14/09/2015
     *  \date modified: dia/mes/año
     *  \param: mNumDespac  Integer  Numero del Despacho
     *  \return: HTML
     */
    private function divGrillaDescar( $mNumDespac )
    {
        $mArrayDestin = array();
        $mTotDestin = self::getDestin( $mNumDespac );

        if( sizeof($mTotDestin) > 0 )
        {
            $mTittleDestin[0] = array(  "nom_destin" => "Nombre Del Destinatario", "num_docalt" => "Remesa (Doc. Alterno)", 
                                        "abr_ciudad" => "Ciudad", "dir_destin" => "Direccion", "num_destin" => "No. Contacto", 
                                        "fec_citdes2" => "Fecha y Hora" 
                                     );
            $mTittleDestin[1] = array( "num_docume" => "No. Factura", "abr_tercer" => "Generador", "fec_findes" => "Fecha y Hora" );


            $mHtml = new Formlib(2);
            
            $mHtml->OpenDiv("id:descargueID; class:accordion");
                $mHtml->SetBody("<h3 style='padding:6px;'><center>GRILLA DESCARGUE</center></h3>");
                $mHtml->OpenDiv("id:secID");
                    $mHtml->OpenDiv("id:form_descargueID; class:contentAccordionForm");

                        $mEsp = '&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;';
                        for( $i=0; $i < sizeof($mTotDestin); $i++ )
                        {
                            $mDataDestin = self::getDestin( $mNumDespac, $mTotDestin[$i]['num_destin'] );

                            $mTxt = "CLIENTE: ".$mTotDestin[$i]['nom_destin']." {$mEsp} DIRECCION: ".$mTotDestin[$i]['dir_destin']." {$mEsp} FECHA Y HORA: ".$mTotDestin[$i]['fec_citdes2'];
                            $mHtml->OpenDiv("id:destin".$i."ID; class:accordion");
                                $mHtml->SetBody("<h3 style='padding:6px;'><center>".$mTxt."</center></h3>");
                                $mHtml->OpenDiv("id:secID");
                                    $mHtml->OpenDiv("id:form_destin".$i."ID; class:contentAccordionForm");

                                    #<Pinta la información del destinatario>
                                        $mHtml->Table("tr");
                                            #Imprime titulos
                                            foreach ($mTittleDestin[0] as $key => $tittle)
                                                $mHtml->Label( $tittle,  array("align"=>"left", "class"=>"celda_titulo") );
                                            $mHtml->CloseRow();

                                            #Imprime datos
                                            $mHtml->Row();
                                            foreach ($mTittleDestin[0] as $key => $tittle)
                                                $mHtml->Label( $mTotDestin[$i][$key],  array("align"=>"left", "class"=>"celda_info") );

                                        $mHtml->CloseTable("tr");
                                    #</Pinta la información del destinatario>

                                    #<Pinta la información de las facturas>
                                        $mHtml->Table("tr");
                                            #Imprime titulos
                                            foreach ($mTittleDestin[1] as $key => $tittle)
                                                $mHtml->Label( $tittle,  array("align"=>"left", "class"=>"celda_titulo") );
                                            $mHtml->CloseRow();

                                            #Imprime datos
                                            $mNumDocume = null;
                                            for( $j=0; $j<sizeof($mDataDestin); $j++ ){
                                                $mHtml->Row();
                                                foreach ($mTittleDestin[1] as $key => $tittle)
                                                    $mHtml->Label( $mDataDestin[$j][$key],  array("align"=>"left", "class"=>($mDataDestin[$j]['fec_findes']=="" && $mDataDestin[$j]['valida']=='0'?"celda_info":"celda_backgr")) );

                                                array_push($mArrayDestin, $mDataDestin[$i]['num_docume']);
                                                $mHtml->CloseRow();

                                                $mNumDocume[$j] = $mDataDestin[$j]['num_docume'];
                                            }
    
                                            $destinatarios = join("|",$mArrayDestin);

                                        $mHtml->CloseTable("tr");
                                    #</Pinta la información de las facturas>

                                        $mHtml->Table("tr");
                                            $mHtml->SetBody("<td>");
                                                $mHtml->SetBody( self::GridCitaDescargue( $mNumDespac, $mTotDestin[$i]['num_docume'], $i) );
                                            $mHtml->SetBody("</td>");
                                        $mHtml->CloseTable("tr");

                                        $mHtml->Hidden( array("name"=>"nom_destin".$i, "id"=>"nom_destin".$i, "value"=> base64_encode($mTotDestin[$i]['nom_destin'])));
                                        $mHtml->Hidden( array("name"=>"num_destin".$i, "id"=>"num_destin".$i, "value"=> $mTotDestin[$i]['num_destin']));
                                        $mHtml->Hidden( array("name"=>"num_docume".$i, "id"=>"num_docume".$i, "value"=> $destinatarios));
                                        $mHtml->Hidden( array("name"=>"otr_destin".$i, "id"=>"otr_destin".$i, "value"=>"") );
                                        $mHtml->Hidden( array("name"=>"ind_finali".$i, "id"=>"ind_finali".$i, "value"=>"") );                                        
                                        $mHtml->Hidden( array("name"=>"num_docum2".$i, "id"=>"num_docum2".$i, "value"=> base64_encode("'".implode("','",$mNumDocume)."'") ) );

                                    $mHtml->CloseDiv();
                                $mHtml->CloseDiv();
                            $mHtml->CloseDiv();
                        }

                    $mHtml->CloseDiv();
                $mHtml->CloseDiv();
            $mHtml->CloseDiv();
            $mHtml->SetBody("<style>
                                .celda_backgr{
                                    background-color: #E0F8E0;
                                    padding-left: 10px;
                                    padding-right: 10px;
                                }
                            </style>");

            self::GridScript();

            return $mHtml->MakeHtml();
        }else
            return false;
    }

    /*! \fn: divRemesas
     *  \brief: Crea DIV de la seccion: INFORMACION DE REMESAS
     *  \author: Ing. Fabian Salinas
     *  \date: 14/09/2015
     *  \date modified: dia/mes/año
     *  \param: mNumDespac  Integer  Numero del Despacho
     *  \return: HTML
     */
    private function divRemesas( $mNumDespac )
    {
        $mRemesas = self::getRemesasDespac( $mNumDespac );
        $mTittleRemesa = array("Remesa", "Fecha Est. Entrega", "Peso T/n", "Volumen", "Empaque", "Mercancia", "Cliente", "Remitente", "Destinatario");

        if ( sizeof($mRemesas) > 0 )
        {
            $mHtml = new Formlib(2);

            $mHtml->OpenDiv("id:remesasID; class:accordion");
                $mHtml->SetBody("<h3 style='padding:6px;'><center>INFORMACION DE REMESAS</center></h3>");
                $mHtml->OpenDiv("id:secID");
                    $mHtml->OpenDiv("id:form_remesasID; class:contentAccordionForm");
                        $mHtml->Table("tr");

                        foreach ($mTittleRemesa as $tittle)
                            $mHtml->Label( $tittle,  array("align"=>"center", "class"=>"celda_titulo") );

                        for($i=0; $i < sizeof($mRemesas); $i++){
                            $mHtml->Row();

                            foreach($mRemesas[$i] as $value)
                                $mHtml->Label( $value,  array("align"=>"center", "class"=>"celda_info") );

                            $mHtml->SetBody('</tr>');
                        }

                        $mHtml->CloseTable("tr");
                    $mHtml->CloseDiv();
                $mHtml->CloseDiv();
            $mHtml->CloseDiv();

            return $mHtml->MakeHtml();
        }else
            return false;
    }

    /*! \fn: divBitacoCambio
     *  \brief: Crea DIV de la seccion: BITACORA DE CAMBIOS REALIZADOS POR WEB SERVICES
     *  \author: Ing. Fabian Salinas
     *  \date: 14/09/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return: HTML
     */
    private function divBitacoCambio()
    {
        $mTxt = '';
        foreach ($this->ArrayComp as $key => $value)
            $mTxt .= '<b>'.$key.'</b>: '.$value.'.<pre style="display:inline">&#09;</pre> ';

        if($mTxt != '' )
        {
            $mHtml = new Formlib(2);

            $mHtml->OpenDiv("id:bitacoCambioID; class:accordion");
                $mHtml->SetBody("<h3 style='padding:6px;'><center>BITACORA DE CAMBIOS REALIZADOS POR WEB SERVICES</center></h3>");
                $mHtml->OpenDiv("id:secID");
                    $mHtml->OpenDiv("id:form_bitacoCambioID; class:contentAccordionForm");
                        $mHtml->Table("tr");
                            $mHtml->SetBody('<span style="color:#000;">'.$mTxt.'</span>');
                        $mHtml->CloseTable("tr");
                    $mHtml->CloseDiv();
                $mHtml->CloseDiv();
            $mHtml->CloseDiv();

            return $mHtml->MakeHtml();
        }else
            return false;
    }

    /*! \fn: divFotos
     *  \brief: Crea DIV de la seccion: FOTOS
     *  \author: Ing. Fabian Salinas
     *  \date: 14/09/2015
     *  \date modified: dia/mes/año
     *  \param: mNumDespac  Integer  Numero del Despacho
     *  \return: HTML
     */
    private function divFotos( $mNumDespac )
    {
        $mFotos = self::fotos( $mNumDespac );

        if( $mFotos != false )
        {
            $mHtml = new Formlib(2);

            $mHtml->OpenDiv("id:fotosID; class:accordion");
                $mHtml->SetBody("<h3 style='padding:6px;'><center>FOTOS</center></h3>");
                $mHtml->OpenDiv("id:secID");
                    $mHtml->OpenDiv("id:form_fotosID; class:contentAccordionForm");
                        $mHtml->SetBody( $mFotos );
                    $mHtml->CloseDiv();
                $mHtml->CloseDiv();
            $mHtml->CloseDiv();

            return $mHtml->MakeHtml();
        }else
            return false;
    }

    /*! \fn: divImgDespac
     *  \brief: Crea DIV de la seccion: IMAGENES DEL CUMPLIDO
     *  \author: Ing. Fabian Salinas
     *  \date: 14/09/2015
     *  \date modified: dia/mes/año
     *  \param: mNumDespac  Integer  Numero del Despacho
     *  \return: HTML
     */
    private function divImgDespac( $mNumDespac )
    {
      $mSelect = "SELECT num_docume, num_consec, url_imagex 
                FROM ".BASE_DATOS.".tab_cumpli_despac 
                 WHERE num_despac = '".$mNumDespac."' ";

      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_IMAGEN = $consulta->ret_matriz();
       
      if( sizeof( $_IMAGEN ) > 0 )
      {
        echo '<script>
                function showImage( num_docume )
                { 
                    $("<div><img  height=\'450\' width=\'450\' src=\'"+window.atob(num_docume)+"\'></div>").dialog({
                       width: 500,
                       height: 500,
                       modal: true,
                       draggable: false,
                       resizable: false
                    });
                }
              </script>';

        foreach( $_IMAGEN as $row )
        {
          $tmp_file = $row['url_imagex']; 
          $mSrc = $tmp_file;
          $mHtml2 .= '&nbsp;&nbsp;&nbsp;<img style="border:1px solid #35650F; cursor:pointer;" onclick="showImage(\''.$mSrc.'\');" src="'.base64_decode($tmp_file).'" width="100" height="100"/>';
        }

        $mHtml = new Formlib(2);

        $mHtml->OpenDiv("id:imgCumplidoID; class:accordion");
          $mHtml->SetBody("<h3 style='padding:6px;'><center>IMAGENES DEL CUMPLIDO</center></h3>");
          $mHtml->OpenDiv("id:secID");
            $mHtml->OpenDiv("id:form_imgCumplidoID; class:contentAccordionForm");
              $mHtml->Table("tr");
                $mHtml->SetBody("<td>".$mHtml2."</td>");
              $mHtml->CloseTable('tr');
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();

        return $mHtml->MakeHtml();
      }else
        return false;
    }

    /*! \fn: divObsGenerales
     *  \brief: Crea DIV de la seccion: OBSERVACIONES GENERALES
     *  \author: Ing. Fabian Salinas
     *  \date: 14/09/2015
     *  \date modified: dia/mes/año
     *  \param: mNumDespac  Integer  Numero del Despacho
     *  \param: _DESPAC     Matriz   Informacion del Despacho
     *  \return: HTML
     */
    private function divObsGenerales( $mNumDespac, $_DESPAC )
    {
      $mHtml = new Formlib(2);

      $mHtml->OpenDiv("id:obsGeneralesID; class:accordion");
        $mHtml->SetBody("<h3 style='padding:6px;'><center>OBSERVACIONES GENERALES</center></h3>");
        $mHtml->OpenDiv("id:secID");
          $mHtml->OpenDiv("id:form_obsGeneralesID; class:contentAccordionForm");
            $mHtml->Table("tr");

              $mHtml->Label( 'Observaciones', array("align"=>"left", "class"=>"celda_etiqueta") );
              $mHtml->Label( 'Medios de Comunicacion', array("align"=>"left", "class"=>"celda_etiqueta") );
              $mHtml->Label( 'Protecciones Especiales', array("align"=>"left", "class"=>"celda_etiqueta") );
              $mHtml->CloseRow();

              if( $this -> verifyConsol( $mNumDespac ) )
              {
                $mHijos = $this -> getViajesConsol( $mNumDespac );
                $mSelect = "SELECT num_despac, obs_despac FROM ".BASE_DATOS.".tab_despac_despac WHERE num_despac IN(".$mHijos.")"; 
                $consulta = new Consulta( $mSelect, $this -> conexion );
                $mObserv = '';
                $_DIFERE = $consulta->ret_matriz();
                foreach( $_DIFERE as $row )
                {
                  $mObserv .= $mObserv != '' ? '<br>'.$row['num_despac'].': '.$row['obs_despac'] : $row['num_despac'].': '.$row['obs_despac'];
                }
                $_DESPAC[0][obs_despac] = $mObserv;
              }
              else if ( !$_DESPAC[0][obs_despac] )
                $_DESPAC[0][obs_despac] = "- - -";

              if ( !$_DESPAC[0][obs_medcom] )
                $_DESPAC[0][obs_medcom] = "- - -";

              if ( !$_DESPAC[0][obs_proesp] )
                $_DESPAC[0][obs_proesp] = "- - -";

              $mHtml->Label( $_DESPAC[0][obs_despac], array("align"=>"left", "class"=>"cell") );
              $mHtml->Label( $_DESPAC[0][obs_medcom], array("align"=>"left", "class"=>"cell") );
              $mHtml->Label( $_DESPAC[0][obs_proesp], array("align"=>"left", "class"=>"cell") );

            $mHtml->CloseTable('tr');
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
      $mHtml->CloseDiv();

      return $mHtml->MakeHtml();
    }

    /*! \fn: getAlias
    *  \brief: trae el alias del usuario
    *  \author: Ing. Miguel Romero
    *    \date: 16/08/2016 
    *  \param: usuario
    *  \return valor que retorna
    */
    public function getAlias($usuario)
    {
     $mSelect = "SELECT IF(a.ali_usuari IS NOT NULL, a.ali_usuari, '".$usuario."') AS ali_usuari 
                   FROM ".BASE_DATOS.".tab_genera_usuari a
                  WHERE LOWER(a.cod_usuari) = LOWER('".$usuario."')";

      $consulta = new Consulta( $mSelect, $this -> conexion );
      $alias = $consulta->ret_matrix("a");

      return $alias[0][ali_usuari];
    }

    /*! \fn: divNotasContro
     *  \brief: Crea DIV de la seccion: Informacion de Notas Controlador
     *  \author: Ing. Fabian Salinas
     *  \date: 14/09/2015
     *  \date modified: dia/mes/año
     *  \param: mNumDespac  Integer  Numero del Despacho
     *  \param: _DESPAC     Matriz   Informacion del Despacho
     *  \param: _RUTASX     Matriz   Informacion de la ruta del despacho
     *  \param: mView       Integer  1 Visible Opcion de Auditar
     *  \param: mViewNotCon Array    Opciones de visualizacion de la seccion notas controlador
     *  \return: HTML
     */
    private function divNotasContro( $mNumDespac, $_DESPAC, $_RUTASX, $mView, $mViewNotCon )
    {
      $mViewUsu = self::getViewUsu();

      $mHtml = new Formlib(2);

      $mHtml->OpenDiv("id:planRutaID; class:accordion");
        $mHtml->SetBody("<h3 style='padding:6px;'><center>INFORMACION DE NOTAS CONTROLADOR</center></h3>");
        $mHtml->OpenDiv("id:secID");
          $mHtml->OpenDiv("id:form_planRutaID; class:contentAccordionForm");
            $mHtml->SetBody('<center><span style="color:#416F1E; cursor:pointer" onclick="todasNotas(1);" >[Ver Todas]</span>&nbsp;&nbsp;');
            $mHtml->SetBody('<span style="color:#416F1E; cursor:pointer" onclick="todasNotas(2);" >[Ocultar]</span>&nbsp;&nbsp;');

            if( $mView == 1 ){
              $mHtml->SetBody('<span style="color:#416F1E; cursor:pointer" onclick="formAuditarDespac();" >[Auditar]</span></center>');
            }

            $mHtml->Table("tr");

      if( $_RUTASX )
      {
        $mSalLinea = $_SESSION['datos_usuario']['cod_usuari'] == 'mgarcia' || $_SESSION['datos_usuario']['cod_usuari'] == 'soporte' ? 0 : 1;

        $mHtml->Label( 'Sitio de Seguimiento', array("align"=>"center", "class"=>"cellTitu", "width"=>"15%") );
        $mHtml->Label( 'Novedad', array("align"=>"center", "class"=>"cellTitu", "width"=>"15%") );
        $mHtml->Label( 'Observaci&oacute;n', array("align"=>"center", "class"=>"cellTitu", "width"=>"20%") );
        $mHtml->Label( 'Fecha Novedad', array("align"=>"center", "class"=>"cellTitu", "width"=>"10%") );
        $mHtml->Label( 'Fecha Sistema', array("align"=>"center", "class"=>"cellTitu", "width"=>"10%") );
        if ( $mViewNotCon->sub->usr_creaci == '1' ) {
            $mHtml->Label( 'Usuario', array("align"=>"center", "class"=>"cellTitu", "width"=>"10%") );
        }

        $viewAlias = self::getView('jso_plarut');
        $watchAlias = $viewAlias->inf_planru->sub->ali_usuari; 
        if( $watchAlias == 1 ){
            $mHtml->Label( 'Usuario', array("align"=>"center", "class"=>"cellTitu", "width"=>"10%") ); 
        }

        if( $mViewUsu->edi_noveda->ind_visibl == 1 ){
          $mHtml->Label( 'Accion', array("align"=>"center", "class"=>"cellTitu", "width"=>"10%") );
        }
        
        $mHtml->Label( 'Solucion Efectiva', array("align"=>"center", "class"=>"cellTitu", "width"=>"10%") );
        
        if( $viewAlias->ind_novnem->ind_visibl == 1 ){
          $mHtml->Label( 'Solucion a NEM', array("align"=>"center", "class"=>"cellTitu", "width"=>"10%") );
        }
        $mHtml->CloseRow();

      
        $mNovedades = self::getNovedad( $mNumDespac );
        $mCanNov = sizeof($mNovedades)-1;

        $j=0;
        for ($i = $mCanNov; $i >=0; $i--)
        {
          $mSolEfecti = "";

          if( $mViewUsu->edi_noveda->ind_visibl == 1 && ($mNovedades[$i][tab_origen] == 1 || $_RUTASX[$i][tab_origen] == 2) ){
            $mLinkUpd = '
              <spam style="color:#416F1E; cursor:pointer"
              onclick="ActionNovedad(\''.$mNumDespac.'\', \''.$mNovedades[$i][cod_contro].'\', \''.$mNovedades[$i][cod_noveda].'\', \''.$mNovedades[$i][fec_noveda].'\', \''.$mNovedades[$i][cod_consec].'\', \''.$mNovedades[$i][tab_origen].'\', \''.$mNovedades[$i][usr_creaci].'\', this,1)">
                [Editar]</spam>&nbsp;&nbsp;';

            if($_SESSION["datos_usuario"]["cod_usuari"] == 'mgarcia' || $_SESSION["datos_usuario"]["cod_usuari"] == 'soporte') {
              $mLinkUpd .= '
                <spam style="color:#416F1E; cursor:pointer"
                onclick="ActionNovedad(\''.$mNumDespac.'\', \''.$mNovedades[$i][cod_contro].'\', \''.$mNovedades[$i][cod_noveda].'\', \''.$mNovedades[$i][fec_noveda].'\', \''.$mNovedades[$i][cod_consec].'\', \''.$mNovedades[$i][tab_origen].'\', \''.$mNovedades[$i][usr_creaci].'\', this,2)">
                  [Eliminar]
                </spam>';
            }
          }

          if( $_RUTASX[$i][nov_especi] == 1 )
          {
            $mStyle = $j % 2 ? 'cellPar' : 'cellImpar';
            $j++;
          }else
            $mStyle = 'cell';

          if($_RUTASX[$i][cod_noveda] != 342)
          {
            $consecutivoProtocolo = $this -> verifyNoveda($_RUTASX[$i][cod_noveda], $mNumDespac, $_RUTASX[$i]['fec_noveda']);   

            if($consecutivoProtocolo[0][num_consec])
            {
              if($consecutivoProtocolo[0][ind_solnov] == '' || $consecutivoProtocolo[0][ind_solnov] == 0)
              {
                $mSolEfecti = "<input type='radio' name='ind_solefe".$i."' onclick='solucionEfectiva(".$i.", ".$_DESPAC[0][cod_transp].",".$mNovedades[$i][cod_noveda].",".$consecutivoProtocolo[0]['num_consec'].")'> SI <input type='radio' name='ind_solefe".$i."' onclick='solucionNoEfectiva(".$i.", ".$_DESPAC[0][cod_transp].",".$mNovedades[$i][cod_noveda].",".$consecutivoProtocolo[0]['num_consec'].")'> NO ";
              }
              elseif($consecutivoProtocolo[0][ind_solnov] == 1)
                $mSolEfecti = "<label>SI</label>";
            }
          }

          $mHtml->Row();
          $mHtml->SetBody( ($i == $mCanNov ? '<tr>' : '<tr class="ocultarTR">') );
            $mHtml->Label( '<b>'.htmlentities($mNovedades[$i][nom_sitiox], ENT_COMPAT, 'ISO-8859-1', true ).'</b>', array("align"=>"left", "class"=>$mStyle) );

            // if para las novedades actualizadas

            $mUpdated = self::checkNovedaUpd($mNovedades[$i],$mNumDespac); 
            if( $mUpdated[0]){
                $dataJson = base64_encode(json_encode($mUpdated));
                $link = "<a href='#' onclick='drawNovUpd(\"".$dataJson."\")'><u><font color='#000'>".htmlentities($mNovedades[$i][nom_noveda], ENT_COMPAT, 'ISO-8859-1', true )."</font></u></a>";
 
                $mHtml->Label( $link, array("align"=>"left", "class"=>$mStyle) );
            }else{ 
                $mHtml->Label( '<b>'.htmlentities($mNovedades[$i][nom_noveda], ENT_COMPAT, 'ISO-8859-1', true ).'</b>', array("align"=>"left", "class"=>$mStyle) );
            }

            $mHtml->Label( '<b>'.$mNovedades[$i][obs_noveda].'</b>', array("align"=>"left", "class"=>$mStyle) );
            $mHtml->Label( '<b>'.$mNovedades[$i][fec_noveda].'</b>', array("align"=>"right", "class"=>$mStyle) );
            $mHtml->Label( '<b>'.$mNovedades[$i][fec_creaci].'</b>', array("align"=>"right", "class"=>$mStyle) );

            if ( $mViewNotCon->sub->usr_creaci == '1' ) {
                $mHtml->Label( '<b>'.htmlentities($mNovedades[$i][usr_creaci], ENT_COMPAT, 'ISO-8859-1', true ).'</b>', array("align"=>"right", "class"=>$mStyle) );
            }
            if( $watchAlias == 1 ){ 
                $mHtml->Label( '<b>'.self::getAlias($mNovedades[$i][usr_creaci]).'</b>', array("align"=>"right", "class"=>$mStyle) ); 
            }

            if( $mViewUsu->edi_noveda->ind_visibl == 1 )
              $mHtml->Label( $mLinkUpd, array("align"=>"center", "class"=>$mStyle) );

            $mHtml->SetBody( '<td align="right" class="'.$mStyle.'" width="30%" ><b>'.$mSolEfecti.'</b></td>' );

            if( $viewAlias->ind_novnem->ind_visibl == 1 ){
              	#verifico si la novedad es movil para pintar el radio de solucion de novedades NEM
              	if(self::getNovedadNEM($mNovedades[$i]['cod_noveda'])[0])
              	{
	              $mHtml->Radio(array("value"=>"1","name" => "ind_novnem", "id" => "ind_novnemID", "class"=>$mStyle, "width" => "100%", "onclick"=>"ValidarProtoNoveda('".$mNovedades[$i]['cod_noveda']."', '".$_DESPAC[0][cod_transp]."')"));
            	}
            	else
            	{
            		$mHtml->Label( '', array("align"=>"right", "class"=>$mStyle) );
            	}
            }
          $mHtml->CloseRow();
        }
      }

            $mHtml->CloseTable('tr');
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
      $mHtml->CloseDiv();

      return $mHtml->MakeHtml();
    }

    /*! \fn: checkNovedaUpd
     *  \brief: revisa si la novedad fue modificada
     *  \author: Ing. Miguel Romero
     *    \date: 29/07/2016 
     *  \param: mData arreglo con los datos de la novedad
     *  \param: despacho a revisar
     *  \return valor que retorna
     */
    

    private function checkNovedaUpd($mData = NULL, $mNumDespac = NULL){

        $query = "SELECT a.cod_novold, a.obs_novold, a.obs_motivo, 
                         a.usr_creaci, a.fec_creaci, b.nom_noveda
                    FROM " . BASE_DATOS . ".tab_bitaco_actnov a
              INNER JOIN " . BASE_DATOS . ".tab_genera_noveda b
                      ON a.cod_novold = b.cod_noveda
                   WHERE a.num_despac = '".$mNumDespac."'
                     AND a.fec_noveda = '".$mData['fec_noveda']."'";

        $consulta = new Consulta( $query, $this -> conexion );
        $result = $consulta -> ret_matrix("a");

        return $result;

    }

    /*! \fn: divLlegada
     *  \brief: Crea DIV de la seccion: LLEGADA
     *  \author: Ing. Fabian Salinas
     *  \date: 14/09/2015
     *  \date modified: dia/mes/año
     *  \param: mIndConsol  Boolean  Indicador viaje consolidado
     *  \return: HTML
     */
    private function divLlegada( $mIndConsol = false )
    {
      $mSelect = "SELECT 1
                    FROM " . BASE_DATOS . ".tab_perfil_servic 
                   WHERE cod_perfil = '" . $_SESSION['datos_usuario']['cod_perfil'] . "'
                     AND cod_servic = '1335'";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $_PERMISO = $consulta -> ret_matriz();
      
      if( sizeof( $_PERMISO ) > 0 && !$mIndConsol )
      {
        $mHtml = new Formlib(2);

        $mHtml->OpenDiv("id:llegadaID; class:accordion");
          $mHtml->SetBody("<h3 style='padding:6px;'><center>LLEGADA</center></h3>");
          $mHtml->OpenDiv("id:secID");
            $mHtml->OpenDiv("id:form_llegadaID; class:contentAccordionForm");
              $mHtml->SetBody('<table width="40%" cellspacing="0" cellpadding="3" border="0" align="left"><tbody>');
                $mHtml->Row();
                #verico que tenga llegada el despacho
                $mDataLlegada = self::getDespLlegada($_REQUEST['despac']);
                if($mDataLlegada == NULL)
                {
                    $mHtml->Label( '<b>Observacion de LLegada :</b>', array("align"=>"left", "class"=>"celda_info", "width"=>"100px") );
                    $mHtml->TextArea( '', array("width"=>"15%", "align"=>"left", "class"=>"celda_info", "cols"=>"50", "rows"=>"2", "id"=>"obs_llegadID", "name"=>"obs_llegad") );
                    $mHtml->Button( array("value"=>"Llegada", "onclick"=>"guarllegad()", "class"=>"crmButton small save", "align"=>"left") );
                }
                else
                {
                    $mHtml->Label( '<b>Fecha de llegada :</b> '.$mDataLlegada[0]['fec_llegad'], array("align"=>"left", "class"=>"celda_etiqueta", "width"=>"33%", "colspan"=>"2") );
                    $mHtml->Label( '<b>Usuario Notificacion :</b> '.$mDataLlegada[0]['usr_modifi'], array("align"=>"left", "class"=>"celda_etiqueta", "width"=>"33%", "colspan"=>"2") );
                    $mHtml->Label( '<b>Fecha de registro :</b> '.$mDataLlegada[0]['fec_modifi'], array("align"=>"left", "class"=>"celda_etiqueta", "width"=>"33%", "colspan"=>"2", "end"=>TRUE) );
                    $mHtml->Label( '<b>Observacion de llegada :</b> '.$mDataLlegada[0]['obs_llegad'], array("align"=>"left", "class"=>"celda_etiqueta", "width"=>"100%", "colspan"=>"6") );
                    
                }
              $mHtml->CloseTable('tr');
            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();

        return $mHtml->MakeHtml();
      }else
        return false;
    }

    /*! \fn: divObsGenerales
     *  \brief: Crea DIV de la seccion: INFORMACION DEL PLAN DE RUTA
     *  \author: Ing. Fabian Salinas
     *  \date: 14/09/2015
     *  \date modified: dia/mes/año
     *  \param: _RUTASX     Matriz   Informacion de la ruta del despacho
     *  \param: mIndConsol  Boolean  Indicador viaje consolidado
     *  \param: mView       Array    Permisos de visibilidad en la seccion plan de ruta
     *  \return: HTML
     */
    private function divPlanRuta( $_RUTASX, $mIndConsol = false, $mView )
    {
      $mTittlePlanRut = array("Sitio de Seguimiento", "Hora/Fecha Programada", "Hora/Fecha Planeada", "Hora/Fecha Sistema", "Tiempo", "Novedad");


        $viewAlias = self::getView('jso_plarut');
        $watchAlias = $viewAlias->inf_planru->sub->ali_usuari;

      if($mView->sub->usr_creaci == 1) {
        $mTittlePlanRut[] = "Usuario";
      }
      if($watchAlias == 1) {
        $mTittlePlanRut[] = "Usuario";
      }

      $mHtml = new Formlib(2);

      $mHtml->OpenDiv("id:planRutaID; class:accordion");
        $mHtml->SetBody("<h3 style='padding:6px;'><center>INFORMACION DEL PLAN DE RUTA</center></h3>");
        $mHtml->OpenDiv("id:secID");
          $mHtml->OpenDiv("id:form_planRutaID; class:contentAccordionForm");
            $mHtml->Table("tr");

            if( !$mIndConsol && $_RUTASX )
            {
              foreach( $mTittlePlanRut as $tittle )
                $mHtml->Label( $tittle, array("align"=>"left", "class"=>"celda_titulo") );
              $mHtml->CloseRow();

              foreach ( $_RUTASX as $row ) 
              {
                $mHtml->Row();
                $mHtml->Label( $row['sit_seguim'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                $mHtml->Label( $row['fec_progra'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                $mHtml->Label( $row['fec_planea'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                $mHtml->Label( $row['fec_creaci'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                $mHtml->Label( $row['cal_tiempo'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                $mHtml->Label( $row['nom_noveda'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                if($mView->sub->usr_creaci == 1) {
                    $mHtml->Label( $row['cod_usuari'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                }
                if($watchAlias == 1) {
                    $mHtml->Label( self::getAlias($row['cod_usuari']), array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                }
                $mHtml->CloseRow();
              }
            }
            else
            {
              if(  $_SESSION['datos_usuario']['cod_perfil'] == 712 ||
                   $_SESSION['datos_usuario']['cod_perfil'] == 711 ||
                   $_SESSION['datos_usuario']['cod_perfil'] == 711 ||
                   $_SESSION['datos_usuario']['cod_perfil'] == 710 ||
                   $_SESSION['datos_usuario']['cod_perfil'] == 709 ||
                   $_SESSION['datos_usuario']['cod_perfil'] == 708 ||
                   $_SESSION['datos_usuario']['cod_perfil'] == 713 ||
                   $_SESSION['datos_usuario']['cod_perfil'] == 1 
                )
              {
                foreach( $mTittlePlanRut as $tittle )
                  $mHtml->Label( $tittle, array("align"=>"left", "class"=>"celda_titulo") );
                $mHtml->CloseRow();

                $row = end($_RUTASX);
                
                $mHtml->Row();
                $mHtml->Label( $row['sit_seguim'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                $mHtml->Label( $row['fec_progra'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                $mHtml->Label( $row['fec_planea'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                $mHtml->Label( $row['fec_creaci'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                $mHtml->Label( $row['cal_tiempo'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                $mHtml->Label( $row['nom_noveda'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                if($mView->sub->usr_creaci == 1) {
                    $mHtml->Label( $row['cod_usuari'], array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                }
                if($watchAlias == 1) {
                    $mHtml->Label( self::getAlias($row['cod_usuari']), array("align"=>"left", "class"=>"celda_info ".$row[bg]) );
                }
                $mHtml->CloseRow();
              }

              $msg = $mIndConsol === true ? "NOTA: El viaje ha sido consolidado, por tanto no tiene Plan de Ruta Activo" : "No Existe Informaci&oacute;n del Plan de Ruta";
              $mHtml->Row();
              $mHtml->Label( $msg, array("align"=>"left", "class"=>"celda_titulo2", "colspan"=>sizeof($mTittlePlanRut)) );
            }

            $mHtml->CloseTable('tr');
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();
      $mHtml->CloseDiv();

      return $mHtml->MakeHtml();
    }

    /*! \fn: divCrossDoking
     *  \brief: Crea el Div para Cross Doking
     *  \author: Ing. Fabian Salinas
     *  \date: 
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: 
     */
    private function divCrossDoking()
    {
      $mSql = "SELECT a.cod_tipdes 
                 FROM ".BASE_DATOS.".tab_despac_despac a 
                WHERE a.num_despac = '{$mNumDespac}' 
                  AND a.cod_tipdes IN (5,6) ";
      $mConsult = new Consulta( $mSql, $this -> conexion );
      $mCodTipdes = $mConsult -> ret_arreglo();

      if( sizeof($mCodTipdes) > 0 )
      {
        $mHtml = new Formlib(2);

        $mHtml->OpenDiv("id:crossDokingID; class:accordion");
          $mHtml->SetBody("<h3 style='padding:6px;'><center>TRAZABILIDAD CROSS DOKING</center></h3>");
          $mHtml->OpenDiv("id:secID");
            $mHtml->OpenDiv("id:form_crossDokingID; class:contentAccordionForm");

            $mHtml->CloseDiv();
          $mHtml->CloseDiv();
        $mHtml->CloseDiv();

        return $mHtml->MakeHtml();
      }else
        return false;
    }

    /*! \fn: scriptEncabezado
     *  \brief: script para el encabezado
     *  \author: Ing. Fabian Salinas
     *  \date: 07/09/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return:
     */
    private function scriptEncabezado()
    {
        return '<script>
                $(".accordion").accordion({
                    collapsible : true, 
                    heightStyle : "content",
                    icons: { "header" : "ui-icon-circle-arrow-e", "activeHeader": "ui-icon-circle-arrow-s" }
                }).click(function(){
                    $("body").removeAttr("class");
                });

                /*
                $("#dat_compleID h3").trigger( "click" );
                $("#cargueID h3").trigger( "click" );
                $("#remesasID h3").trigger( "click" );
                $("#bitacoCambioID h3").trigger( "click" );
                $("#planRutaID h3").trigger( "click" );
                $("#obsGeneralesID h3").trigger( "click" );
                $("#imgCumplidoID h3").trigger( "click" );
                */
                $("#fotosID h3").trigger( "click" );
                $("#crossDokingID h3").trigger( "click" );
                $("#descargueID h3").trigger( "click" );
                $("#llegadaID h3").trigger( "click" );

                function todasNotas( opt )
                {
                  try{
                    if( opt == 1 ){
                      $(".ocultarTR").fadeIn( 3000 );
                      $("#planRutaID #secID").css({"height":"auto"});
                    }else{
                      $(".ocultarTR").fadeOut( 1000 );
                      $("#planRutaID #secID").css({"height":"auto"});
                    }
                  }
                  catch(e)
                  {
                    console.log( "Error Function todasNotas: "+e.message+"\nLine: "+e.lineNumber );
                    return false;
                  }
                }

                $("#crossDokingID h3").click(function(){
                  //if( $("#cross_dokingID").val() == 0 ){
                    $("#cross_dokingID").val("1");
                    var divCross = $("#form_crossDokingID");

                    divCross.html(" Bien!... ");

                    var Standar   = $("#central").val();
                    var attributes  = "Ajax=on&Option=divCrossDoking&standa="+Standar;
                        attributes += "&window="+ $( "input[name$=\'window\']" ).val();
                        attributes += "&num_despac="+ $("#num_despacID").val();

                    $.ajax({
                      url: "../"+ Standar + "/lib/ClassCrossDoking.php",
                      type: "POST", 
                      data: attributes,
                      async: true,
                      beforeSend: function(){
                        divCross.html("<center><img src=\"../"+Standar+"/imagenes/ajax-loader.gif\"></center>");
                        $("#crossDokingID #secID").css("height","auto");
                      },
                      success: function(data){
                        divCross.html(data);
                        $("#crossDokingID #secID").css("height","auto");
                      }
                    });
                  //}
                });
                
                verifyConfirPernoc( 1, $("#num_despacID").val() );
                
              </script>';
    }

    /*! \fn: getDataEncabezado
     *  \brief: Trae toda la información necesaria para el encabezado
     *  \author: Ing. Fabian Salinas
     *  \date: 07/09/2015
     *  \date modified: dia/mes/año
     *  \param: mNumDesOri  Integer Despacho Original
     *  \param: mNumDespac  String  Despacho original o despachos consolidados separados por comas 
     *  \param: mIndConsol  Integer 1 = despacho consolidado;
     *  \return:
     */
    private function getDataEncabezado( $mNumDesOri, $mNumDespac, $mIndConsol, $mObs = array('0', '1') )
    {
        $mSql = "SELECT a.cod_manifi, l.nom_rutasx, b.cod_conduc, o.nom_operad, 
                        a.cod_ciuori, a.cod_ciudes, b.cod_transp, a.tie_contra, 
                        a.ind_tiemod, a.obs_tiemod, 
                        UPPER(j.nom_tercer) AS nom_transp, 
                        UPPER(t.nom_marcax) AS nom_marcax, 
                        UPPER(u.nom_lineax) AS nom_lineax, 
                        UPPER(v.nom_colorx) AS nom_colorx, 
                        UPPER(b.num_placax) AS num_placax, 
                        UPPER(b.num_placax) AS aux_placax, 
                        UPPER(w.nom_carroc) AS nom_carroc, 
                        UPPER(s.ano_modelo) AS ano_modelo, 
                        UPPER(aa.nom_tipdes) AS nom_tipdes, 
                        DATE_FORMAT(a.fec_despac, '%H:%i %d-%m-%Y') AS fec_despac, 
                        DATE_FORMAT(a.fec_creaci, '%H:%i %d-%m-%Y') AS fec_creaci, 
                        IF( r.tip_transp IS NULL OR r.tip_transp = '', 0, r.tip_transp ) AS tip_transp, 
                        IF( k.num_desext IS NULL OR k.num_desext = '', '$mObs[0]', k.num_desext ) AS num_desext, 
                        IF( p.nom_califi IS NULL OR p.nom_califi = '', '$mObs[0]', p.nom_califi ) AS nom_califi, 
                        IF( r.tip_vehicu IS NULL OR r.tip_vehicu = '', '$mObs[0]', r.tip_vehicu ) AS tip_vehicu, 
                        IF( s.num_config IS NULL OR s.num_config = '', '$mObs[0]', s.num_config ) AS num_config, 
                        IF( a.num_poliza IS NULL OR a.num_poliza = '', '$mObs[0]', a.num_poliza ) AS num_poliza, 
                        IF( a.con_telmov IS NULL OR a.con_telmov = '', m.num_telmov, a.con_telmov ) AS con_telmov, 
                        IF( i.nom_agenci IS NULL OR i.nom_agenci = '', '$mObs[0]', UPPER(i.nom_agenci) ) AS nom_agenci, 
                        IF( j.abr_tercer IS NULL OR j.abr_tercer = '', '$mObs[0]', UPPER(j.abr_tercer) ) AS nom_genera, 
                        IF( r.nom_poseed IS NULL OR r.nom_poseed = '', '$mObs[0]', UPPER(r.nom_poseed) ) AS nom_poseed, 
                        IF( z.abr_tercer IS NULL OR z.abr_tercer = '', '$mObs[0]', UPPER(z.abr_tercer) ) AS nom_asegur, 
                        IF( ab.nom_produc IS NULL OR ab.nom_produc = '', '$mObs[1]', UPPER(ab.nom_produc) ) AS nom_produc, 
                        IF( b.nom_conduc IS NULL OR b.nom_conduc = '', UPPER(m.abr_tercer), UPPER(b.nom_conduc) ) AS nom_conduc, 
                        IF( a.gps_paswor IS NULL OR a.gps_paswor = '', ( IF(x.clv_usrgps IS NULL OR x.clv_usrgps = '', 0, 2) ), 1 ) AS ind_pasgps, 
                        CONCAT( UPPER(c.abr_ciudad), ' (', LEFT(d.abr_depart,4), ') - ', LEFT(e.nom_paisxx,3), ' - ', c.cod_ciudad ) AS nom_ciuori, 
                        CONCAT( UPPER(f.abr_ciudad), ' (', LEFT(g.abr_depart,4), ') - ', LEFT(h.nom_paisxx,3), ' - ', f.cod_ciudad ) AS nom_ciudes, 
                        IF( a.fec_salida IS NULL OR a.fec_salida = '0000-00-00 00:00:00', '$mObs[1]', DATE_FORMAT(a.fec_salida, '%H:%i %d-%m-%Y') ) AS fec_salida, 
                        IF( r.fec_plalle IS NULL OR r.fec_plalle = '0000-00-00 00:00:00', '$mObs[1]', DATE_FORMAT(r.fec_plalle, '%H:%i %d-%m-%Y') ) AS fec_plalle, 
                        IF( r.fec_salida IS NULL OR r.fec_salida = '0000-00-00 00:00:00', '$mObs[1]', DATE_FORMAT(r.fec_salida, '%H:%i %d-%m-%Y') ) AS fec_salpla, 
                        IF( b.fec_llegpl IS NULL OR b.fec_llegpl = '0000-00-00 00:00:00', '$mObs[1]', DATE_FORMAT(b.fec_llegpl, '%H:%i %d-%m-%Y') ) AS fec_llegpl, 
                        IF( b.fec_findes IS NULL OR b.fec_findes = '0000-00-00 00:00:00', '$mObs[1]', DATE_FORMAT(b.fec_findes, '%H:%i %d-%m-%Y') ) AS fec_findes, 
                        IF( a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00', '$mObs[1]', DATE_FORMAT(a.fec_llegad, '%H:%i %d-%m-%Y') ) AS fec_llegad, 
                        IF( a.con_telef1 IS NULL OR a.con_telef1 = '', ( IF(m.num_telef1 IS NULL OR m.num_telef1 = '', '$mObs[0]', m.num_telef1) ), a.con_telef1 ) AS con_telef1, 
                        IF( a.gps_usuari IS NULL OR a.gps_usuari = '', ( IF(x.nom_usrgps IS NULL OR x.nom_usrgps = '', '$mObs[0]', x.nom_usrgps) ), a.gps_usuari ) AS gps_usuari, 
                        IF( a.gps_paswor IS NULL OR a.gps_paswor = '', ( IF(x.clv_usrgps IS NULL OR x.clv_usrgps = '', '$mObs[0]', x.clv_usrgps) ), a.gps_paswor ) AS gps_paswor, 
                        IF( a.fec_citcar IS NULL OR a.fec_citcar = '0000-00-00', '$mObs[1]', DATE_FORMAT( CONCAT( a.fec_citcar, ' ', a.hor_citcar ), '%H:%i %d-%m-%Y') ) AS fec_citcar, 
                        IF( a.gps_operad IS NULL OR a.gps_operad = '', ( IF(y.nom_operad IS NULL OR y.nom_operad = '', '$mObs[0]', UPPER(y.nom_operad)) ), UPPER(a.gps_operad) ) AS nom_opegps,
                        k.num_solici 
                   FROM ".BASE_DATOS.".tab_despac_despac a 
             INNER JOIN ".BASE_DATOS.".tab_despac_vehige b 
                     ON a.num_despac = b.num_despac 
             INNER JOIN ".BASE_DATOS.".tab_genera_ciudad c 
                     ON a.cod_paiori = c.cod_paisxx 
                    AND a.cod_depori = c.cod_depart 
                    AND a.cod_ciuori = c.cod_ciudad 
             INNER JOIN ".BASE_DATOS.".tab_genera_depart d 
                     ON a.cod_paiori = d.cod_paisxx 
                    AND a.cod_depori = d.cod_depart 
             INNER JOIN ".BASE_DATOS.".tab_genera_paises e 
                     ON a.cod_paiori = e.cod_paisxx 
             INNER JOIN ".BASE_DATOS.".tab_genera_ciudad f 
                     ON a.cod_paides = f.cod_paisxx 
                    AND a.cod_depdes = f.cod_depart 
                    AND a.cod_ciudes = f.cod_ciudad 
             INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
                     ON a.cod_paides = g.cod_paisxx 
                    AND a.cod_depdes = g.cod_depart 
             INNER JOIN ".BASE_DATOS.".tab_genera_paises h 
                     ON a.cod_paides = h.cod_paisxx 
              LEFT JOIN ".BASE_DATOS.".tab_genera_agenci i 
                     ON b.cod_agenci = i.cod_agenci 
              LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer j 
                     ON b.cod_transp = j.cod_tercer 
              LEFT JOIN ".BASE_DATOS.".tab_despac_sisext k 
                     ON a.num_despac = k.num_despac 
             INNER JOIN ".BASE_DATOS.".tab_genera_rutasx l 
                     ON b.cod_rutasx = l.cod_rutasx 
              LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer m 
                     ON b.cod_conduc = m.cod_tercer 
              LEFT JOIN ".BASE_DATOS.".tab_tercer_conduc n 
                     ON b.cod_conduc = n.cod_tercer 
              LEFT JOIN ".BASE_DATOS.".tab_operad_operad o 
                     ON n.cod_operad = o.cod_operad 
              LEFT JOIN ".BASE_DATOS.".tab_genera_califi p 
                     ON n.cod_califi = p.cod_califi 
              LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer q 
                     ON a.cod_client = q.cod_tercer 
              LEFT JOIN ".BASE_DATOS.".tab_despac_corona r 
                     ON a.num_despac = r.num_dessat 
             INNER JOIN ".BASE_DATOS.".tab_vehicu_vehicu s 
                     ON b.num_placax = s.num_placax 
             INNER JOIN ".BASE_DATOS.".tab_genera_marcas t 
                     ON s.cod_marcax = t.cod_marcax 
             INNER JOIN ".BASE_DATOS.".tab_vehige_lineas u 
                     ON s.cod_marcax = u.cod_marcax 
                    AND s.cod_lineax = u.cod_lineax 
             INNER JOIN ".BASE_DATOS.".tab_vehige_colore v 
                     ON s.cod_colorx = v.cod_colorx 
             INNER JOIN ".BASE_DATOS.".tab_vehige_carroc w 
                     ON s.cod_carroc = w.cod_carroc 
              LEFT JOIN ".BASE_DATOS.".tab_despac_gpsxxx x 
                     ON a.num_despac = x.num_despac 
              LEFT JOIN ".BD_STANDA .".tab_genera_opegps y 
                     ON x.cod_opegps = y.cod_operad 
              LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer z 
                     ON a.cod_asegur = z.cod_tercer 
             INNER JOIN ".BASE_DATOS.".tab_genera_tipdes aa 
                     ON a.cod_tipdes = aa.cod_tipdes 
              LEFT JOIN ".BASE_DATOS.".tab_genera_produc ab 
                     ON k.cod_mercan = ab.cod_produc 
                  WHERE a.num_despac = '{$mNumDesOri}' 
                ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        $mResult = $mConsult -> ret_matrix('a');
        $mResult = $mResult[0];

        #<Datos Despacho Consolidado>
            if( $mIndConsol == 1 )
            {
                $mArrayKey[0] = array('num_desext', 'cod_manifi', 'fec_despac', 'fec_citcar');
                $mArrayKey[1] = array('nom_poseed', 'tip_transp', 'tip_vehicu', 'nom_produc', 'fec_findes');
                $mArrayKey[2] = array('fec_creaci', 'fec_plalle');

                foreach ($mArrayKey[0] as $key)
                    $mResult[$key] = '';
                
                foreach ($mArrayKey[1] as $key)
                    $mResult[$key] = '';

                $mSql = "SELECT a.cod_manifi, b.num_desext, 
                                UPPER(c.nom_poseed) AS nom_poseed, 
                                DATE_FORMAT(a.fec_despac, '%H:%i %d-%m-%Y') AS fec_despac, 
                                a.fec_creaci, 
                                IF( c.tip_transp IS NULL OR c.tip_transp = '', 0, c.tip_transp ) AS tip_transp, 
                                IF( c.tip_vehicu IS NULL OR c.tip_vehicu = '', '$mObs[0]', c.tip_vehicu ) AS tip_vehicu, 
                                DATE_FORMAT( CONCAT( a.fec_citcar, ' ', a.hor_citcar ), '%H:%i %d-%m-%Y' ) AS fec_citcar, 
                                IF( d.nom_produc IS NULL OR d.nom_produc = '', '$mObs[1]', UPPER(d.nom_produc) ) AS nom_produc, 
                                IF( c.fec_plalle IS NULL OR c.fec_plalle = '0000-00-00 00:00:00', '$mObs[1]', c.fec_plalle ) AS fec_plalle, 
                                IF( e.fec_findes IS NULL OR e.fec_findes = '0000-00-00 00:00:00', '$mObs[1]', DATE_FORMAT(e.fec_findes, '%H:%i %d-%m-%Y') ) AS fec_findes 
                           FROM ".BASE_DATOS.".tab_despac_despac a 
                      LEFT JOIN ".BASE_DATOS.".tab_despac_sisext b 
                             ON a.num_despac = b.num_despac 
                      LEFT JOIN ".BASE_DATOS.".tab_despac_corona c 
                             ON a.num_despac = c.num_dessat 
                      LEFT JOIN ".BASE_DATOS.".tab_genera_produc d 
                             ON b.cod_mercan = d.cod_produc 
                     INNER JOIN ".BASE_DATOS.".tab_despac_vehige e 
                             ON a.num_despac = e.num_despac 
                          WHERE a.num_despac IN ( {$mNumDespac} ) 
                       ORDER BY a.fec_creaci ASC ";
                $mConsult = new Consulta( $mSql, $this -> conexion );
                $mData = $mConsult -> ret_matrix('a');

                foreach ($mArrayKey[0] as $key){
                    foreach ($mData as $row)
                        $mResult[$key] .= $mResult[$key] != '' ? ', '.$row[$key] : $row[$key];
                }

                foreach ($mArrayKey[1] as $key)
                    $mResult[$key] = $mData[0][$key];

                foreach ($mArrayKey[2] as $key){
                    for ($i=0; $i < sizeof($mData); $i++){
                        if( $i == 0 )
                            $mResult[$key] = $mData[$i][$key];
                        else{
                            if( $mResult[$key] > $mData[$i][$key] )
                                $mResult[$key] = $mData[$i][$key];
                        }
                    }

                    $mResult[$key] = date("H:i d-m-Y", strtotime($mResult[$key]));
                }
            }
        #</Datos Despacho Consolidado>

        $mResult['tip_transp'] = $this -> ArrayTipTansp[ $mResult['tip_transp'] ];

        #<Cantidad Novedades - Tipo de facturacion>
            $mCanNoveda = sizeof( getNovedadesDespac($this -> conexion, $mNumDesOri, '1') );
            $mTipFactur = getTransTipser($this -> conexion, " AND a.cod_transp = ".$mResult['cod_transp']);
            $mResult['can_noveda'] = $mCanNoveda." ".$this -> cTipFactur[ $mTipFactur[0]['tip_factur'] ];
        #</Cantidad Novedades - Tipo de facturacion>

        #<Despachos Anteriores>
            $mSql = " SELECT a.num_despac, b.ind_anulad, b.fec_llegad 
                        FROM ".BASE_DATOS.".tab_despac_sisext a
                  INNER JOIN ".BASE_DATOS.".tab_despac_despac b 
                          ON a.num_despac = b.num_despac 
                       WHERE a.num_despac != '{$mNumDesOri}' 
                         AND a.num_desext = (
                                                SELECT x.num_desext 
                                                  FROM ".BASE_DATOS.".tab_despac_sisext x 
                                                 WHERE x.num_despac = '{$mNumDesOri}'
                                                   AND x.num_desext NOT IN ('VC', '')
                                            ) ";
            $consulta = new Consulta($mSql, $this -> conexion);
            $mDespacAnterior = $consulta->ret_matrix('i');

            if($mDespacAnterior){
                $mTxt = '';
                foreach ($mDespacAnterior as $row) {
                    $mTxt = $mTxt == '' ? '' : ', ';
                    $mTxt = '<a class="classLink" href="index.php?cod_servic='.$this -> servic.'&window=central&despac='.$row[0].'&opcion=1" style="color:green;">'.$row[0].'</a>';
                }
                $mResult['tit_desant'] = ( $mDespacAnterior[0][1] == 'A' || $mDespacAnterior[0][2] != '' ) ? "Despacho Anterior" : "Despacho Actual" ;
                $mResult['num_desant'] = $mTxt; 
            }else{
                $mResult['tit_desant'] = '';
                $mResult['num_desant'] = '';
            }
        #</Despachos Anteriores>

        #<Verifica Cambios de datos según tab_bitaco_corona>
            if( $mIndConsol != 1 && $mResult['num_desext'] != $mObs[0] )
            {
                $mBitaco = self::getBitacoCorona( $mResult['num_desext'], $mObs );

                if( sizeof($mBitaco) > 0 )
                {#Recorre los datos a comparar
                    $mTittle = array(   "nom_ciuori" => "Origen", 
                                        "cod_manifi" => "Manifiesto", 
                                        "nom_ciudes" => "Destino", 
                                        "nom_rutasx" => "Ruta", 
                                        "nom_conduc" => "Conductor", 
                                        "cod_conduc" => "C.C.", 
                                        "fec_citcar" => "Fecha Cita de Cargue", 
                                        "fec_plalle" => "Fecha Llegada a Planta", 
                                        "con_telef1" => "Telefono", 
                                        "nom_poseed" => "Poseedor", 
                                        "nom_marcax" => "Marca", 
                                        "fec_llegad" => "Fecha Llegada", 
                                        "nom_colorx" => "Color", 
                                        "num_placax" => "Placa", 
                                        "nom_carroc" => "Carroceria", 
                                        "ano_modelo" => "Modelo", 
                                        "nom_opegps" => "Operador Gps", 
                                        "gps_usuari" => "Usuario Gps", 
                                        "gps_paswor" => "Contraseña Gps", 
                                        "nom_asegur" => "Aseguradora", 
                                        "num_poliza" => "Num Poliza", 
                                        "nom_produc" => "Mercancia", 
                                        "tip_transp" => "Tipo Transportadora" 
                                    );

                    foreach ($mTittle as $key => $tittle)
                        $mResult[$key] = self::verifiCambio( $mBitaco[$key], $mResult[$key], $tittle );
                }
            }
        #</Verifica Cambios de datos según tab_bitaco_corona>

        return $mResult;
    }

    /*! \fn: getRemesasDespac
     *  \brief: Trae las remesas del despacho
     *  \author: 
     *  \date: dia/mes/año
     *  \date modified: dia/mes/año
     *  \param: mNumDespac  Integer  Numero del despacho
     *  \return:
     */
    private function getRemesasDespac( $mNumDespac )
    {
        $mSql = "SELECT IF(cod_remesa IS NULL OR cod_remesa = '', '-', cod_remesa) AS cod_remesa,
                        IF(fec_estent IS NULL OR fec_estent = '', '-', fec_estent) AS fec_estent,
                        val_pesoxx,val_volume,des_empaqu,
                        des_mercan,abr_client,abr_remite,
                        abr_destin 
                   FROM ".BASE_DATOS.".tab_despac_remesa 
                  WHERE num_despac = '{$mNumDespac}' ";
        $mConsult = new Consulta($mSql, $this->conexion);
        return $mResult = $mConsult->ret_matrix('a');
    }

    /*! \fn: getDataDestin
     *  \brief: Trae la Data para los destinatarios
     *  \author: 
     *  \date: dia/mes/año
     *  \date modified: dia/mes/año
     *  \param: mNumDespac  Integer  Numero del despacho
     *  \param: mNomDestin  String   Nombre del destinatario
     *  \param: mIndGroupx  Boolean  Indicador de agrupacion
     *  \return: Matriz
     */
    private function getDestin( $mNumDespac, $mNomDestin = NULL, $mIndGroupx = true )
    {
        $mSql = "  SELECT a.nom_destin, c.abr_ciudad, a.dir_destin,
                          a.num_destin, CONCAT(a.fec_citdes,' ',a.hor_citdes) AS fec_citdes2,
                          a.num_docume, a.num_docalt, a.cod_genera, UPPER(b.abr_tercer) AS abr_tercer,
                          IF(a.fec_findes IS NULL,'',a.fec_findes) AS fec_findes, IF(e.num_consec IS NULL OR e.num_consec = '', 0, e.num_consec) AS valida  
                     FROM ".BASE_DATOS.".tab_despac_destin a
                LEFT JOIN ".BASE_DATOS.".tab_cumpli_despac e 
                       ON a.num_despac = e.num_despac 
                LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer b 
                       ON a.cod_genera = b.cod_tercer 
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad c 
                       ON a.cod_ciudad = c.cod_ciudad 
                    WHERE a.num_despac = '{$mNumDespac}' ";

        if( $mNomDestin == NULL && $mIndGroupx == true ){
            self::updateDestin($mNumDespac);
            $mSql .= " GROUP BY a.num_destin ";
        }
        elseif( $mNomDestin == NULL && $mIndGroupx == false ){
            $mSql .= "";
        }
        else{
            $mSql .= " AND a.num_destin LIKE '{$mNomDestin}' ";
        }

        $mSql .= " ORDER BY fec_citdes2, a.nom_destin, a.num_docume ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        return $mResult = $mConsult->ret_matrix('a');
    }

    /*! \fn: fotos
     *  \brief: Muestra las fotos registradas para el despacho
     *  \author: Ing. Fabian Salinas
     *  \date: 11/09/2015
     *  \date modified: dia/mes/año
     *  \param: mNumDespac  Integer  Numero del despacho
     *  \return: HTML
     */
    private function fotos( $mNumDespac )
    {
        $mSql = "SELECT a.bin_fotoxx, b.nom_contro, b.cod_contro, a.fec_creaci, a.num_consec, a.bin_fotox2
                  FROM ".BASE_DATOS.".tab_despac_images a,
                       ".BASE_DATOS.".tab_genera_contro b
                  WHERE a.cod_contro = b.cod_contro 
                    AND a.num_despac = '{$mNumDespac}' 
                    AND (a.bin_fotoxx != '' OR a.bin_fotox2 != '') ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        $mFotos = $mConsult->ret_matrix('a');
  
        if( sizeof($mFotos) < 1 )
          return false;

        $mHtml = '';
        $mHtml .= '<center>';
        $mHtml .= '<table with="100%">';

        $i=0;
        $j=1;
        while( $i < sizeof($mFotos) )
        {
            $mHtml .= '<tr><td colspan="4">&nbsp;</td></tr>';
            $mHtml .= '<tr>';
            $mHtml .=    '<th colspan="2" align="center" >'.($i+1).') '.$mFotos[$i]['nom_contro'].'</th>';
            $mHtml .=    '<th>&nbsp;</th>';
            $mHtml .= $mFotos[$j] ? '<th colspan="2" align="center" >'.($j+1).') '.$mFotos[$j]['nom_contro'].'</th>' : '<th>&nbsp;</th>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
            $mHtml .=    '<td>';
            $mHtml .=        '<center><b>Conductor</b></center>';
            $mHtml .=        '<img src="'.$mFotos[$i]['bin_fotoxx'].'" width="300" height="200" border="2"/>';
            $mHtml .=    '</td>';
            $mHtml .=    '<td>';
            $mHtml .=        '<center><b>Precinto</b></center>';
            $mHtml .=        '<img src="'.$mFotos[$i]['bin_fotox2'].'" width="300" height="200" border="2"/>';
            $mHtml .=    '</td>';
            $mHtml .=    '<td>&nbsp;</td>';
            $mHtml .=    '<td>';
            $mHtml .= $mFotos[$j] ? '<center><b>Conductor</b></center>' : '&nbsp;';
            $mHtml .= $mFotos[$j] ? '<img src="'.$mFotos[$j]['bin_fotoxx'].'" width="300" height="200" border="2"/>' : '&nbsp;';
            $mHtml .=    '</td>';
            $mHtml .=    '<td>';
            $mHtml .= $mFotos[$j] ? '<center><b>Precinto</b></center>' : '&nbsp;';
            $mHtml .= $mFotos[$j] ? '<img src="'.$mFotos[$j]['bin_fotox2'].'" width="300" height="200" border="2"/>' : '&nbsp;';
            $mHtml .=    '</td>';
            $mHtml .= '</tr>';
            $mHtml .= '<tr>';
            $mHtml .=    '<th colspan="2" align="center" >'.date($mFotos[$i]['fec_creaci']).'</th>';
            $mHtml .=    '<th>&nbsp;</th>';
            $mHtml .= $mFotos[$j] ? '<th colspan="2" align="center" >'.date($mFotos[$j]['fec_creaci']).'</th>' : '<th>&nbsp;</th>';
            $mHtml .= '</tr>';

            $i = $i+2;
            $j = $j+2;
        }

        $mHtml .= '</table>';
        $mHtml .= '</center>';

        return $mHtml;
    }
    
    /*! \fn: PlanDeRuta
     *  \brief: Plan de ruta
     *  \author: Ing. Fabian Salinas
     *  \date: 29/05/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \param: 
     *  \return: 
     */
    function PlanDeRuta( $despac, $link = 0, $finali = 0, $opcurban = 0, $datos_usuario = NULL, $lleg = NULL, $tie_ultnov = NULL )
    {
      #< 0. PRE >
        self::IncludeJsStyle();
        echo "<script language=\"JavaScript\" src=\"../" . DIR_APLICA_CENTRAL . "/js/PDF.js\"></script>\n";

        echo "<style>
                .D8DFF9{
                  background: #D8DFF9;
                }
                .D8DFEA{
                  background: #D8DFEA;
                }
                .cellTitu{
                  border-bottom:1px solid #999;
                  background-color:#F5F5F5;
                  background-image:url('../imagenes/backg_01.gif');
                  color:#333;
                  font-weight:bold;
                  padding:3px 10px;
                }
                .cellPar{
                  padding-left: 10px;
                  padding-right: 10px;
                  border-bottom: 1px solid #EFEFEF;
                  border-right: 1px solid #EFEFEF;
                  color:#0B3B17;
                  background:#E6E6E6;
                }
                .cellImpar{
                  padding-left: 10px;
                  padding-right: 10px;
                  border-bottom: 1px solid #EFEFEF;
                  border-right: 1px solid #EFEFEF;
                  color:#0B3B17;
                  background:#E0F8E0;
                }
                .cell{
                  padding-left: 10px;
                  padding-right: 10px;
                  border-bottom: 1px solid #EFEFEF;
                  border-right: 1px solid #EFEFEF;
                  background-color: #ffffff;
                }
                .celda_info2 {
                  background-color: #ffffff;
                  border-bottom: 1px solid #efefef;
                  border-right: 1px solid #efefef;
                  color: #000000;
                  padding-left: 10px;
                  padding-right: 10px;
                  white-space: nowrap;
                }
              </style>";

        $mView = self::getView('jso_plarut');
        /******* FILTROS INICIALES *******/
        $aux = "href";

        if ( $_SESSION[datos_usuario][cod_perfil] == "" )
        {
          $filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica, COD_FILTRO_CLIENT, $_SESSION[datos_usuario][cod_usuari] );
          if ($filtro -> listar( $this -> conexion ) )
          {
            $datos_filtro = $filtro -> retornar();
            $aux = "name";
          }
        }
        else
        {
          $filtro = new Aplica_Filtro_Perfil( $this -> cod_aplica, COD_FILTRO_CLIENT, $_SESSION[datos_usuario][cod_usuari] );
          if ( $filtro -> listar( $this -> conexion ) )
          {
            $datos_filtro = $filtro -> retornar();
            $aux = "name";
          }
        }
        /*********************************/

        $mSelect = "SELECT a.obs_despac, b.obs_medcom, b.obs_proesp,
                           a.obs_llegad, a.fec_salida, b.cod_rutasx, 
                           a.cod_tipdes, b.cod_transp, a.cod_tipdes 
                      FROM " . BASE_DATOS . ".tab_despac_despac a 
                INNER JOIN " . BASE_DATOS . ".tab_despac_vehige b 
                        ON a.num_despac = b.num_despac 
                     WHERE a.num_despac = " . $despac . " ";
        $consulta = new Consulta( $mSelect, $this -> conexion );
        $_DESPAC = $consulta -> ret_matrix('a'); 

        $mSelect = "SELECT cod_tipser
                      FROM " . BASE_DATOS . ".tab_transp_tipser  
                     WHERE cod_transp = '".$_DESPAC[0][cod_transp]."' 
                       AND num_consec = ( SELECT MAX(num_consec) 
                                            FROM ".BASE_DATOS.".tab_transp_tipser
                                           WHERE cod_transp = '".$_DESPAC[0][cod_transp]."'
                                        )";
        $consulta = new Consulta( $mSelect, $this -> conexion );
        $mTipSer = $consulta -> ret_arreglo();

        $indConsol = $this -> verifyConsolHijo( $despac );
        $_RUTASX = Despachos::getRutaDespac( $despac, $opcurban, $tie_ultnov, $mTipSer[0], $aux );
      #</ 0. PRE >

      $mHtml = new Formlib(2);

      #< 1: INFORMACION DEL PLAN DE RUTA >
      if( $mView->inf_planru->ind_visibl == 1 )
        $mHtml->SetBody( self::divPlanRuta( $_RUTASX, $indConsol, $mView->inf_planru) );

      #< 2: LLEGADA >
      if ( $lleg == 1 &&  $mView->inf_llegad->ind_visibl == 1 ){
        $mLlegada = self::divLlegada( $indConsol );
        if( $mLlegada != false )
          $mHtml->SetBody( $mLlegada );
      }

      #< 3: INFORMACION DE NOTAS DE CONTROLADOR >
      if( $mView->inf_notcon->ind_visibl == 1 )
        $mHtml->SetBody( self::divNotasContro( $despac, $_DESPAC, $_RUTASX, $mView->pop_califi->ind_visibl, $mView->inf_notcon) );

      #< 3.5: TRAZABILIDAD CROSS DOKING >
      if( $mView->inf_crodok->ind_visibl == 1 && in_array($_DESPAC[0]['cod_tipdes'],array(5,6)) ){
        $mCross = self::divCrossDoking();
        if( $mCross != false )
          $mHtml->SetBody( $mCross );
      }

      #< 4: OBSERVACIONES GENERALES >
      if( $mView->obs_genera->ind_visibl == 1 )
        $mHtml->SetBody( self::divObsGenerales( $despac, $_DESPAC) );

      #< 5: IMAGENES ADJUNTAS AL DESPACHO >
      if( $mView->img_adjunt->ind_visibl == 1 ){
        $mImgDespac = self::divImgDespac( $despac );
        if( $mImgDespac != false )
          $mHtml->SetBody( $mImgDespac );
      }

      $mHtml->Hidden( array("name"=>"central", "id"=>"central", "value"=> DIR_APLICA_CENTRAL));
      $mHtml->Hidden( array("name"=>"bd_aplica", "id"=>"bd_aplica", "value"=> BASE_DATOS));
      $mHtml->Hidden( array("name"=>"despac", "id"=>"despac", "value"=> $despac));
      $mHtml->Hidden( array("name"=>"cross_doking", "id"=>"cross_dokingID", "value"=> "0"));

      return $mHtml->MakeHtml();
    }

    /*! \fn: verifyNoveda
     *  \brief: verifica su una novedad de un despacho tiene protocolos asignados
     *  \author: Ing. Miguel Romero "mamafoka" 
     *  \date: 07/07/2015
     *  \date modified: dia/mes/año
     *  \param: noveda novedad a evaluar 
     *  \param: despac despacho el cual contiene la novedad anterior
     *  \return: Matriz
     */
    private function verifyNoveda ( $noveda, $despac, $fec_noveda)
    {
        $fec_noveda = explode(" ", $fec_noveda);

        $fec_noveda[1] = explode("-", $fec_noveda[1]);
        $fec_noveda[1] = $fec_noveda[1][2]."-".$fec_noveda[1][1]."-".$fec_noveda[1][0];
 

        $mSelect = "SELECT a.num_consec, a.ind_solnov
                      FROM ".BASE_DATOS.".tab_protoc_asigna a
                     WHERE a.num_despac = '".$despac."'
                       AND a.cod_noveda = '".$noveda."'
                       AND a.fec_noveda LIKE '".$fec_noveda[1]." ".$fec_noveda[0]."%'";

        $mConsult = new Consulta( $mSelect, $this -> conexion );
        $mReturn = $mConsult -> ret_matrix('a');
 
        return $mReturn;
    }

    /*! \fn: getRutaDespac
     *  \brief: Trae la ruta y las novedades del despacho 
     *  \author: Ing. Fabian Salinas
     *  \date: 29/05/2015
     *  \date modified: dia/mes/año
     *  \param: NumDespac, 
     *  \param: 
     *  \return: Matriz
     */
    private function getRutaDespac($mDespac, $opcurban = 0, $tie_ultnov = NULL, $mTipSer = NULL, $aux )
    {
      @include_once( "../".DIR_APLICA_CENTRAL."/lib/general/functions.inc" );
      $mResult = array();
      $mRutax = getControDespac( $this -> conexion, $mDespac ); #Script = /satt_standa/lib/general/functions.inc
      $mTrans = Despachos::getTranspDespac($mDespac);
      $mUltPC = end($mRutax);
      $mNoR = 'NO REPORT&Oacute;';

      #Ultimo PC con novedad
      $mUlPCNov = getNovedadesDespac( $this -> conexion, $mDespac, 2 ); #Script = /satt_standa/lib/general/functions.inc

      if( $mUlPCNov[cod_contro] != '' )
      {#Despacho con novedades

        $mBandera=0;
        $j=0;#Consecutivo

        #Recorre los puestos de control del plan de ruta
        for ($i=0; $i < sizeof($mRutax); $i++) 
        {
          $mNovEnSitio = Despachos::getNovEnSitio( $mDespac, $mRutax[$i][cod_contro] );#Novedades en Sitio
          $mNovAnSitio = Despachos::getNovAnSitio( $mDespac, $mRutax[$i][cod_contro] );#Novedades antes de Sitio

          if( $mBandera > 0 )
            $mBandera++;
          elseif( $mUlPCNov[cod_contro] == $mRutax[$i][cod_contro] )
            $mBandera++;

          if($mNovAnSitio)
          {#Con novedad antes de sitio
            foreach ($mNovAnSitio as $row)
            {
              $mTime[0] = $j==0 ? 0 : $mTime[1] ;
              $mTime[1] = $row[fec_creaci];
              $mTime[2] = Despachos::getTime($mTime[0], $mTime[1]);
              $mDate = date_format( ( date_create($row[fec_creaci]) ), 'H:i d-m-Y');
              $mResult[$j] = Despachos::arrayRuta( $row[nom_sitiox], '---', '---', $row[fec_contro], $mTime[2], $row[nom_noveda], $row[usr_creaci], 'D8DFEA', $mDate, $row[obs_noveda], '1', $row[nov_especi], $mRutax[$i][cod_contro], $row[cod_noveda], $row[tab_origen], $row[cod_consec] );
              $j++;
            }
          }

          if( $mBandera == 0 )
          {#PC es anterior al ultimo PC con novedad
            if($mNovEnSitio)
            {#Con Novedad En Sitio
              foreach ($mNovEnSitio as $row)
              {
                $mSitio = $mRutax[$i][ind_virtua] == '1' ? $mRutax[$i][nom_contro] : '<span style="color:#'.$mRutax[$i][cod_colorx].'">'.$mRutax[$i][nom_contro].'</span>';
                $mCol = $mRutax[$i][ind_virtua] == '1' ? 'D8DFEA' : 'D8DFF9';
                $mTime[0] = $j==0 ? 0 : $mTime[1] ;
                $mTime[1] = $row[fec_creaci];
                $mTime[2] = Despachos::getTime($mTime[0], $mTime[1]);
                $mDate = date_format( ( date_create($row[fec_creaci]) ), 'H:i d-m-Y');
                $mResult[$j] = Despachos::arrayRuta( $mSitio, $mRutax[$i][fec_progra], $mRutax[$i][fec_planea], $row[fec_noveda], $mTime[2], $row[nom_noveda], $row[usr_creaci], 'D8DFF9', $mDate, $row[obs_noveda], '1', $row[nov_especi], $mRutax[$i][cod_contro], $row[cod_noveda], $row[tab_origen], $row[cod_consec] );
                $j++;
              }
            }
            elseif($mRutax[$i][ind_virtua] == '0')
            {#Sin Novedad En Sitio
              $mSitio = '<span style="color:#FF564D">'.$mRutax[$i][nom_contro].'</span>';
              $mResult[$j] = Despachos::arrayRuta( $mSitio, $mRutax[$i][fec_progra], $mRutax[$i][fec_planea], $mNoR, $mNoR, $mNoR, $mNoR, 'D8DFEA', $mNoR, $mNoR, '0', $mRutax[$i][cod_contro], '', '', '' );
              $j++;
            }
          }
          elseif( $mBandera == 1 )
          {#PC es = al ultimo PC con novedad
            if($mNovEnSitio)
            {#Con Novedad En Sitio

              if( $mUltPC[cod_contro] == $mRutax[$i][cod_contro] )
                $mSitio = Despachos::linkNomSitio( $mDespac, $mRutax[$i][nom_contro], $mRutax[$i][cod_contro], $mRutax[$i][cod_colorx], $mTrans[cod_transp], $mRutax[$i][ind_virtua], $tie_ultnov, $opcurban, $aux, $mTipSer );
              else
                $mSitio = $mRutax[$i][ind_virtua] == '1' ? $mRutax[$i][nom_contro] : '<span style="color:#'.$mRutax[$i][cod_colorx].'">'.$mRutax[$i][nom_contro].'</span>';

              foreach ($mNovEnSitio as $row)
              {
                $mTime[0] = $j==0 ? 0 : $mTime[1] ;
                $mTime[1] = $row[fec_creaci];
                $mTime[2] = Despachos::getTime($mTime[0], $mTime[1]);
                $mDate = date_format( ( date_create($row[fec_creaci]) ), 'H:i d-m-Y');
                $mResult[$j] = Despachos::arrayRuta( $mSitio, $mRutax[$i][fec_progra], $mRutax[$i][fec_planea], $row[fec_noveda], $mTime[2], $row[nom_noveda], $row[usr_creaci], 'D8DFF9', $mDate, $row[obs_noveda], '1', $row[nov_especi], $mRutax[$i][cod_contro], $row[cod_noveda], $row[tab_origen], '' );
                $j++;
              }
            }
            else
            {#Sin Novedad En Sitio 
              $mSitio = Despachos::linkNomSitio( $mDespac, $mRutax[$i][nom_contro], $mRutax[$i][cod_contro], $mRutax[$i][cod_colorx], $mTrans[cod_transp], $mRutax[$i][ind_virtua], $tie_ultnov, $opcurban, $aux, $mTipSer );
              $mResult[$j] = Despachos::arrayRuta( $mSitio, $mRutax[$i][fec_progra], $mRutax[$i][fec_planea], '', '', '', '', '', '', '', '0', $mRutax[$i][cod_contro], '', '', '' );
              $j++;
            }
          }
          else
          {#PC es posterior al ultimo PC con novedad
            $mSitio = Despachos::linkNomSitio( $mDespac, $mRutax[$i][nom_contro], $mRutax[$i][cod_contro], $mRutax[$i][cod_colorx], $mTrans[cod_transp], $mRutax[$i][ind_virtua], $tie_ultnov, $opcurban, $aux, $mTipSer );
            $mResult[$j] = Despachos::arrayRuta( $mSitio, $mRutax[$i][fec_progra], $mRutax[$i][fec_planea], '', '', '', '', '', '', '', '0', $mRutax[$i][cod_contro], '', '', '' );
            $j++;
          }
        }
      }
      else
      {#Despacho sin novedades
        for ($i=0; $i < sizeof($mRutax); $i++) 
        {
          $mSitio = Despachos::linkNomSitio( $mDespac, $mRutax[$i][nom_contro], $mRutax[$i][cod_contro], $mRutax[$i][cod_colorx], $mTrans[cod_transp], $mRutax[$i][ind_virtua], $tie_ultnov, $opcurban, $aux, $mTipSer );
          $mResult[$i] = Despachos::arrayRuta( $mSitio, $mRutax[$i][fec_progra], $mRutax[$i][fec_planea], '', '', '', '', '', '', '', '0', $mRutax[$i][cod_contro], '', '', '' );
        }
      }

      return $mResult;
    }

    /*! \fn: arrayRuta
     *  \brief: Crea el array de la ruta
     *  \author: Ing. Fabian Salinas
     *  \date: 02/06/2015
     *  \date modified: dia/mes/año
     *  \param: sitio_seguimiento, fecha_programada, fecha_planeada, fecha_novedad, 
     *          tiempo, noveda, usuario, color background, fecha_creacion_novedad, 
     *          Observacion de la novedad, Ind_Notas Controlador, Ind_Novedad Especial,
     *          codigo PC, codigo Novedad, codigo consecutivo novedad
     *  \return: Posicion array
     */
    private function arrayRuta( $sit_seguim, $fec_progra, $fec_planea, $fec_noveda, $cal_tiempo, $nom_noveda, $cod_usuari, $bac_ground, $fec_creaci, $obs_noveda, $ind_notcon, $nov_especi, $cod_contro, $cod_noveda, $tab_origen, $cod_consec = NULL )
    {
      $mResult[sit_seguim] = $sit_seguim;
      $mResult[fec_progra] = $fec_progra;
      $mResult[fec_planea] = $fec_planea;
      $mResult[fec_noveda] = $fec_noveda;
      $mResult[cal_tiempo] = $cal_tiempo;
      $mResult[nom_noveda] = $nom_noveda;
      $mResult[cod_usuari] = $cod_usuari;
      $mResult[fec_creaci] = $fec_creaci;
      $mResult[obs_noveda] = $obs_noveda;
      $mResult[ind_notcon] = $ind_notcon;
      $mResult[nov_especi] = $nov_especi;
      $mResult[cod_contro] = $cod_contro;
      $mResult[cod_noveda] = $cod_noveda;
      $mResult[tab_origen] = $tab_origen;
      $mResult[cod_consec] = $cod_consec;

      $mResult[bg] = $bac_ground;
      return $mResult;
    }

    /*! \fn: getTime
     *  \brief: Trae la diferencia en minutos de dos fechas
     *  \author: Ing. Fabian Salinas
     *  \date: 02/06/2015
     *  \date modified: dia/mes/año
     *  \param: Fecha Inicial, Fecha Final
     *  \return: String
     */
    private function getTime($mFec1, $mFec2)
    {
      if($mFec1 != 0)
        return $mMin = round(( strtotime($mFec2) - strtotime($mFec1) )/60) .' Min(s)';
      else
        return '0 Min(s)';
    }

    /*! \fn: getNovAnSitio
     *  \brief: Novedades antes de sitio
     *  \author: Ing. Fabian Salinas
     *  \date: 01/06/2015
     *  \date modified: dia/mes/año
     *  \param: NumDespac, Puesto Control
     *  \return: Matriz Novedades
     */
    private function getNovAnSitio( $mNumDespac, $mCodContro )
    {
      $mSql = " (
                      SELECT DATE_FORMAT( a.fec_contro, '%H:%i %d-%m-%Y' ) AS fec_contro, 
                             UPPER(b.nom_noveda) AS nom_noveda, 
                             a.usr_creaci, a.fec_creaci, 
                             UPPER(c.nom_sitiox) AS nom_sitiox, 
                             a.obs_contro AS obs_noveda, 
                             b.nov_especi, a.cod_noveda, 
                             '1' AS tab_origen, a.cod_consec 
                        FROM ".BASE_DATOS.".tab_despac_contro a 
                  INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                          ON a.cod_noveda = b.cod_noveda 
                  INNER JOIN ".BASE_DATOS.".tab_despac_sitio c 
                          ON a.cod_sitiox = c.cod_sitiox
                       WHERE a.num_despac = '{$mNumDespac}' 
                         AND a.cod_contro = '{$mCodContro}' 
                )
                UNION
                (
                      SELECT DATE_FORMAT( a.fec_solici, '%H:%i %d-%m-%Y' ) AS fec_contro, 
                             c.nom_noveda,
                             a.usr_solici AS usr_creaci, 
                             a.fec_solici AS fec_creaci, 
                             UPPER(b.nom_contro) AS nom_sitiox, 
                             GROUP_CONCAT( d.tex_encabe ) AS obs_noveda, 
                             '0' AS nov_especi, a.cod_noveda, 
                             '3' AS tab_origen, d.cod_consec
                        FROM ".BASE_DATOS.".tab_recome_asigna a 
                  INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
                          ON a.cod_contro = b.cod_contro 
                  INNER JOIN ".BASE_DATOS.".tab_genera_noveda c 
                          ON a.cod_noveda = c.cod_noveda 
                  INNER JOIN ".BASE_DATOS.".tab_genera_recome d 
                          ON a.cod_recome = d.cod_consec 
                       WHERE a.num_despac = '{$mNumDespac}' 
                         AND a.cod_contro = '{$mCodContro}' 
                    GROUP BY a.cod_contro 
                )
                UNION
                (
                      SELECT DATE_FORMAT( a.fec_ejecut, '%H:%i %d-%m-%Y' ) AS fec_contro, 
                             'SOLUCION RECOMENDACION' AS nom_noveda, 
                             a.usr_ejecut AS usr_creaci,
                             a.fec_ejecut AS fec_creaci, 
                             UPPER(b.nom_contro) AS nom_sitiox, 
                             GROUP_CONCAT( CONCAT(d.tex_encabe, ': ', a.obs_ejecuc) ) AS obs_noveda, 
                             '0' AS nov_especi, a.cod_noveda, 
                             '4' AS tab_origen, d.cod_consec 
                        FROM ".BASE_DATOS.".tab_recome_asigna a 
                  INNER JOIN ".BASE_DATOS.".tab_genera_contro b 
                          ON a.cod_contro = b.cod_contro 
                  INNER JOIN ".BASE_DATOS.".tab_genera_noveda c 
                          ON a.cod_noveda = c.cod_noveda 
                  INNER JOIN ".BASE_DATOS.".tab_genera_recome d 
                          ON a.cod_recome = d.cod_consec 
                       WHERE a.num_despac = '{$mNumDespac}' 
                         AND a.cod_contro = '{$mCodContro}' 
                         AND a.obs_ejecuc IS NOT NULL 
                    GROUP BY a.cod_contro 
                )
              ORDER BY fec_creaci ASC 
              ";
      $mConsult = new Consulta( $mSql, $this -> conexion );
      return $mResult = $mConsult -> ret_matrix('a'); 
    }

    /*! \fn: getNovEnSitio
     *  \brief: Novedades en sitio
     *  \author: Ing. Fabian Salinas
     *  \date: 01/06/2015
     *  \date modified: dia/mes/año
     *  \param: NumDespac, Puesto Control
     *  \return: Matriz
     */
    private function getNovEnSitio( $mNumDespac, $mCodContro )
    {
      $mSql = " SELECT DATE_FORMAT( a.fec_noveda, '%H:%i %d-%m-%Y' ) AS fec_noveda, 
                       a.usr_creaci, a.fec_creaci, 
                       UPPER(b.nom_noveda) AS nom_noveda, 
                       a.des_noveda AS obs_noveda, b.nov_especi, 
                       a.cod_noveda, '2' AS tab_origen, 
                       '0' AS cod_consec
                  FROM ".BASE_DATOS.".tab_despac_noveda a 
            INNER JOIN ".BASE_DATOS.".tab_genera_noveda b 
                    ON a.cod_noveda = b.cod_noveda 
                 WHERE a.num_despac = '$mNumDespac' 
                   AND a.cod_contro = '$mCodContro' 
              ORDER BY a.fec_creaci ";
      $mConsult = new Consulta( $mSql, $this -> conexion );
      return $mResult = $mConsult -> ret_matrix('a'); 
    }

    /*! \fn: linkNomSitio
     *  \brief: Crea el link del sitio de seguimiento
     *  \author: Ing. Fabian Salinas
     *  \date: 01/06/2015
     *  \date modified: dia/mes/año
     *  \param: Num Despacho, Nombre PC, codigo PC, color, codigo transportadora, ind Virtual, 
     *  \param: tie_ultnov, opcurban, Auxiliar defini si lleva link o no, Tipo de servicio
     *  \return: html
     */
    private function linkNomSitio( $mNumDespac, $mNomSitio, $mCodContro, $mColor, $mCodTransp, $mIndVirtua, $tie_ultnov, $opcurban, $aux, $mTipSer = NULL )
    {
      if( $mColor == '' && $mIndVirtua == '1' )
        $mColor = '336600';
      elseif( $mColor == '' && $mIndVirtua == '0' )
        $mColor = '0026FF';
    
      $aux = ( strtolower( $_SESSION[datos_usuario][cod_usuari] ) == 'alogistica' || ( $mIndVirtua == 1 && $mTipSer == 1 && $_SESSION[datos_usuario][cod_perfil] == '7' ) ) ? "name" : $aux ;
      $mSpam  = '<span style="color:#'.$mColor.'">'.$mNomSitio.'</span>';
      $mLink  = '<a target="centralFrame" ';
      $mLink .= $aux.'="index.php?cod_servic='.$this -> servic.'&window=central&tie_ultnov='.$tie_ultnov;
      $mLink .= '&despac='.$mNumDespac.'&opcion=2&codpc='.$mCodContro.'&pc='.$mNomSitio;
      $mLink .= '&ind_virtua='.$mIndVirtua.'&cod_transp='.$mCodTransp.'&opcurban='.$opcurban.'" >';
      $mLink  = $mLink.$mSpam.'</a>';

      #Verifica que el Puesto de Control sea Padre o este Homologado como Hijo, para mostrar el PopUp con los detalles del Puesto de Control Padre
      $mDetallePc = Despachos::getVerifyPcPadre( $mCodContro );
      if( $mDetallePc != NULL && $mIndVirtua != '1' )
        $mLupa = '<span onclick="showDetallePc('.$mDetallePc[0].')"><img border="0" src="../satt_standa/imagenes/icon_buscar.png"></span>&nbsp;&nbsp;';
      else
        $mLupa = '';

      return $mResult = $mLupa.$mLink;
    }

    /*! \fn: getTranspDespac
     *  \brief: Trae la transportadora del despacho
     *  \author: Ing. Fabian Salinas
     *  \date: 01/06/2015
     *  \date modified: dia/mes/año
     *  \param: NumDespac
     *  \return: Array
     */
    private function getTranspDespac($mDespac)
    {
      $mSql = " SELECT a.cod_transp, b.abr_tercer 
                  FROM ".BASE_DATOS.".tab_despac_vehige a 
            INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b 
                    ON a.cod_transp = b.cod_tercer 
                 WHERE a.num_despac = '$mDespac' ";
      $mConsult = new Consulta( $mSql, $this -> conexion );
      $mResult = $mConsult -> ret_matrix('a'); 
      return $mResult[0];
    }
    
    function getDiffDate( $fec_inicia, $fec_finali ) 
    {
      $time_ini = strtotime( $fec_inicia );
      $time_fin = strtotime( $fec_finali );
      
      if ( $time_ini > $time_fin )
        return (int)abs( round( ( $time_ini - $time_fin ) / 60 ) );
      else
        return (int)abs( round( ( $time_fin - $time_ini ) / 60 ) );
    }
    
    /*! \fn: getVerifyPcPadre
     *  \brief: Verifica si el puesto de control es padre o si esta homologado
     *  \author: Ing. Fabian Salinas
     *	\date: 25/05/2015
     *	\date modified: dia/mes/año
     *  \param: codContro
     *  \param: 
     *  \return: Array
     */
    function getVerifyPcPadre($codContro)
    {
      $mSelect =  " SELECT a.cod_contro AS PcPadre 
                      FROM ".BASE_DATOS.".tab_genera_contro a 
                     WHERE a.cod_contro = '".$codContro."' 
                       AND a.ind_pcpadr = '1' ";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      $mPcPadre = $consulta -> ret_arreglo();

      if(!$mPcPadre)
      {
        $mSelect = "SELECT a.cod_contro AS PcHijox
                      FROM ".BASE_DATOS.".tab_homolo_pcxeal a 
                     WHERE a.cod_homolo = '".$codContro."' 
                     LIMIT 1 ";
        $consulta = new Consulta( $mSelect, $this -> conexion );
        return $mPcHijox = $consulta -> ret_arreglo();
      }else{
        return $mPcPadre;
      }
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
    
    /*! \fn: getNovedadesSitio
     *  \brief: Trae las novedades en sitio de un despacho en un PC
     *  \author: 
     *  \date: 
     *  \date modified: dd/mm/aaaa
     *  \param: num_despac  Integer  Numero de Despacho
     *  \param: cod_contro  Integer  Codigo PC
     *  \param: cod_rutasx  Integer  Codigo Ruta
     *  \return: Matriz
     */
    function getNovedadesSitio( $num_despac, $cod_contro, $cod_rutasx )
    {
      $mSelect = "SELECT UPPER( IF( b.ind_virtua = 1, CONCAT( b.nom_contro, ' (Virtual) ' ), b.nom_contro ) ) AS nom_sitiox,
                         DATE_FORMAT( a.fec_planea, '%H:%i %d-%m-%Y' ) AS fec_progra,
                         IF( c.fec_noveda IS NOT NULL, DATE_FORMAT( c.fec_noveda, '%H:%i %d-%m-%Y' ), DATE_FORMAT( a.fec_alarma, '%H:%i %d-%m-%Y' ) ) AS fec_planea,
                         DATE_FORMAT( c.fec_creaci, '%H:%i %d-%m-%Y' ) AS fec_noveda,
                         UPPER( e.nom_noveda ) AS nom_noveda, 
                         c.usr_creaci,
                         c.val_retras,
                         c.fec_creaci
                    FROM ".BASE_DATOS.".tab_genera_contro b, 
                         ".BASE_DATOS.".tab_despac_seguim a,
                         ".BASE_DATOS.".tab_despac_noveda c,
                         ".BASE_DATOS.".tab_genera_noveda e,
                         ".BASE_DATOS.".tab_despac_vehige f
                   WHERE a.cod_contro = b.cod_contro
                     AND a.num_despac = '" . $num_despac . "'
                     AND a.cod_contro = '" . $cod_contro . "' 
                     AND a.cod_rutasx = '" . $cod_rutasx . "'
                     AND a.num_despac = c.num_despac
                     AND a.cod_contro = c.cod_contro
                     AND a.cod_rutasx = c.cod_rutasx
                     AND c.cod_noveda = e.cod_noveda
                     AND a.num_despac = f.num_despac
                   ORDER BY c.fec_creaci";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      return $consulta -> ret_matriz();
    }
    
    /*! \fn: getNovedadesAntes
     *  \brief: Trae las novedades antes de sitio de un despacho en un PC
     *  \author: 
     *  \date: 
     *  \date modified: dd/mm/aaaa
     *  \param: num_despac  Integer  Numero de Despacho
     *  \param: cod_contro  Integer  Codigo PC
     *  \param: cod_rutasx  Integer  Codigo Ruta
     *  \return: Matriz
     */
    function getNovedadesAntes( $num_despac, $cod_contro, $cod_rutasx )
    {
      $mSelect = "SELECT IF( UPPER( IF( a.cod_noveda = ".CONS_NOVEDA_GPSXXX.", REPLACE(SUBSTRING(a.obs_contro, 11, INSTR(a.obs_contro, 'Velocidad')-11 ),',,',','), c.nom_sitiox ) ) IS NULL, d.nom_contro, UPPER( IF( a.cod_noveda = ".CONS_NOVEDA_GPSXXX.", REPLACE(SUBSTRING(a.obs_contro, 11, INSTR(a.obs_contro, 'Velocidad')-11 ),',,',','), c.nom_sitiox ) )) AS nom_sitiox,
                         '- - -' AS fec_progra, 
                         DATE_FORMAT( a.fec_contro, '%H:%i %d-%m-%Y' ) AS fec_planea, 
                         DATE_FORMAT( a.fec_creaci, '%H:%i %d-%m-%Y' ) AS fec_noveda,
                         UPPER( b.nom_noveda ) AS nom_noveda,
                         a.usr_creaci,
                         a.val_retras, 
                         a.fec_creaci
                    FROM " . BASE_DATOS . ".tab_despac_contro a,
                         " . BASE_DATOS . ".tab_genera_noveda b,
                         " . BASE_DATOS . ".tab_despac_sitio c,
                         " . BASE_DATOS . ".tab_genera_contro d
                   WHERE a.cod_noveda=b.cod_noveda AND 
                         a.cod_sitiox=c.cod_sitiox AND 
                         a.cod_contro = d.cod_contro AND
                         a.num_despac ='".$num_despac."' AND 
                         a.cod_contro = '" . $cod_contro . "' AND
                         a.cod_rutasx = '" . $cod_rutasx . "'
                   ORDER BY a.fec_creaci";
      $consulta = new Consulta( $mSelect, $this -> conexion );
      return $consulta -> ret_matriz();
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
								
									<img src="https://https://avansatgl.intrared.net/ap/satt_standa/imagenes/klogis/image001.jpg">
									
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
										<img src="https://https://avansatgl.intrared.net/ap/satt_standa/imagenes/klogis/image002.jpg">
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
            $query = "INSERT INTO " . $base_datos . ".tab_despac_noveda

                                 (num_despac, cod_rutasx,cod_contro,cod_noveda,
                                  tiem_duraci,fec_noveda,des_noveda,val_retras,cod_sitiox,
                                  usr_creaci,fec_creaci,usr_modifi,fec_modifi)
                          VALUES (" . $regist["despac"] . "," . $rutact[0][0] . "," . $regist["contro"] . ",
                                  " . $regist["noveda"] . "," . $regist["tieadi"] . ",
                                 '" . $regist["fecnov"] . "','" . addslashes($regist["observ"]) . "'," . $tiempretras[0][0] . ",
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
                            /********************* **************** ********************* ********************* **************** ********************* *********************/
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
                                        $url_webser = "http://www.colombiasoftware.net/base/webservice/ReportePuestoControlCS.php?wsdl";
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
                                        $respuesta = $oSoapClient -> __call( ' puestocontrol', $parametros );                                  
                                        
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
                            /********************* **************** ********************* ********************* **************** ********************* *********************/

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
                                   
                                    //echo "<pre>";print_r( $parametros ); echo "</pre>";
                                    
                                    $oSoapClient = new soapclient( $url_webser, array( 'encoding'=>'ISO-8859-1' ) );
                                    
                                    //Métodos disponibles en el WS
                                    
                                    $respuesta = $oSoapClient -> __call( 'reporte', $parametros );

                                    //echo "<pre>"; print_r( $respuesta ); echo "</pre>";
                                    
                                    
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
                                    $url_webser = "https://avansatgl.intrared.net/ap/interf/app/faro/wsdl/faro.wsdl";
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
                                    
                                    //MÃ©todos disponibles en el WS
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
							
								<img src="https://avansatgl.intrared.net/ap/satt_standa/imagenes/klogis/image001.jpg">
								
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
									<img src="https://https://avansatgl.intrared.net/ap/satt_standa/imagenes/klogis/image002.jpg">
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
                     '" . addslashes($regist["observ"]) . "'," . $regist["noveda"] . "," . $regist["tieadi"] . ",'" . $regist["fecnov"] . "',
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
                              
                              $this->setNovedadError($parametros, $regist, $novedaError, 'nc');
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

    /*! \fn: getListadoCiudades
     *  \brief: Trae el listado de ciudades
     *  \author: 
     *  \date: 
     *  \date modified: dd/mm/aaaa
     *  \param: 
     *  \return: Matriz
     */
    function getListadoCiudades()
    {
        $query = "SELECT a.cod_ciudad,CONCAT(UPPER(a.abr_ciudad),' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3),' - ',a.cod_ciudad)
  			  FROM " . BASE_DATOS . ".tab_genera_ciudad a,
  			       " . BASE_DATOS . ".tab_genera_depart b,
  			       " . BASE_DATOS . ".tab_genera_paises c
  			 WHERE a.cod_depart = b.cod_depart AND
  			       a.cod_paisxx = b.cod_paisxx AND
  			       b.cod_paisxx = c.cod_paisxx 
				   GROUP BY 1 ORDER BY 2 ";
        $consulta = new Consulta($query, $this->conexion);
        return $consulta->ret_matriz();
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
        $query = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3),' - ',a.cod_ciudad), UPPER( a.abr_ciudad )
                    FROM " . BASE_DATOS . ".tab_genera_ciudad a,
                         " . BASE_DATOS . ".tab_genera_depart b,
                         " . BASE_DATOS . ".tab_genera_paises c
                   WHERE a.cod_depart = b.cod_depart AND
                         a.cod_paisxx = b.cod_paisxx AND
                         b.cod_paisxx = c.cod_paisxx AND
                         a.cod_ciudad = '" . $ciudad . "' 
                GROUP BY 1 ORDER BY 2";

        $consulta = new Consulta($query, $this->conexion);
        return $ciudades = $consulta->ret_matriz();
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
    
    function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false)
    {
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

    function GridStyle()
    {
        echo "<style>
                .cellth-ltb{
                     background: #E7E7E7;
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .cellth-lb{
                     background: #E7E7E7;
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                }
                .cellth-b{
                     background: #E7E7E7;
                     border-bottom: 1px solid #999999; 
                }
                .cellth-tb{
                     background: #E7E7E7;
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-ltb{
                     border-left: 1px solid #999999; 
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-tb{
                     border-bottom: 1px solid #999999; 
                     border-top: 1px solid #999999;
                }
                .celltd-lb{
                     border-bottom: 1px solid #999999; 
                     border-left: 1px solid #999999;
                }
                .celltd-l{
                     border-left: 1px solid #999999;
                }
                .fontbold{
                    font-weight: bold;
                }
                .divGrilla{
                    margin: 0;
                    padding: 0;
                    border: none;
                    border-top: 1px solid #999999;
                    border-bottom: 1px solid #999999;
                }
              </style>";
    }

    function GridScript()
    {
        echo "<script>
                    $(document).ready(function(){
                        $( 'input.dates' ).datepicker();
                        $( 'input.hours' ).timepicker({
                            timeFormat: 'hh:mm',
                            showSecond: false
                        });
                        $.mask.definitions['A']='[12]';
                        $.mask.definitions['M']='[01]';
                        $.mask.definitions['D']='[0123]';
                        
                        $.mask.definitions['H']='[012]';
                        $.mask.definitions['N']='[012345]';
                        $.mask.definitions['n']='[0123456789]';
                        
                        $( 'input.dates' ).mask('Annn-Mn-Dn');
                        $( 'input.hours' ).mask('Hn:Nn');
                    });
              </script>";
    }

    function ShowGrilla( $mPerfil = NULL )
    {
        $mReturn = true;
        $mPerfhideGrilla = array(707,708,709,710,711,712);

        if(!in_array( $mPerfil, $mPerfhideGrilla ) ){
            $mReturn = false;
        }

        return $mReturn;
    }

    /*! \fn: getBitacoCorona
     *  \brief: Trae la información del despacho si existe en bitaco_corona
     *  \author: Ing. Fabian Salinas
     *  \date: 25/05/2015
     *  \date modified: 08/09/2015
     *  \modifi by: Ing. Fabian Salinas
     *  \param: NumDespac
     *  \return: Array con el primer registro del despacho
     */
    function getBitacoCorona( $num_despac, $mObs = array('0', '1') )
    {
      $mSql = " SELECT l.nom_rutasx, 
                       a.num_dessat, a.cod_ciuori, a.cod_manifi, 
                       a.cod_ciudes, a.cod_agedes, a.cod_rutasx, 
                       a.num_despac, a.cod_conduc, a.nom_conduc,
                       a.con_telmov, c.nom_lineax, a.cod_config,
                       a.gps_idxxxx, a.tip_transp, a.hor_citcar, 
                       UPPER(b.nom_marcax) AS nom_marcax, 
                       UPPER(d.nom_colorx) AS nom_colorx, 
                       UPPER(a.num_placax) AS num_placax, 
                       UPPER(e.nom_carroc) AS nom_carroc, 
                       UPPER(a.ano_modelo) AS ano_modelo, 
                       DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y') AS fec_creaci, 
                       IF( a.gps_usuari IS NULL OR a.gps_usuari = '', '$mObs[0]', a.gps_usuari ) AS gps_usuari, 
                       IF( a.gps_paswor IS NULL OR a.gps_paswor = '', '$mObs[0]', a.gps_paswor ) AS gps_paswor, 
                       IF( a.num_poliza IS NULL OR a.num_poliza = '', '$mObs[0]', a.num_poliza ) AS num_poliza, 
                       IF( a.nom_poseed IS NULL OR a.nom_poseed = '', '$mObs[0]', UPPER(a.nom_poseed) ) AS nom_poseed, 
                       IF( a.gps_operad IS NULL OR a.gps_operad = '', '$mObs[0]', UPPER(a.gps_operad) ) AS nom_opegps, 
                       IF( n.abr_tercer IS NULL OR n.abr_tercer = '', '$mObs[0]', UPPER(n.abr_tercer) ) AS nom_asegur, 
                       IF( o.nom_produc IS NULL OR o.nom_produc = '', '$mObs[1]', UPPER(o.nom_produc) ) AS nom_produc, 
                       CONCAT( UPPER(f.abr_ciudad), ' (', LEFT(g.abr_depart,4), ') - ', LEFT(h.nom_paisxx,3), ' - ', f.cod_ciudad ) AS nom_ciuori, 
                       CONCAT( UPPER(i.abr_ciudad), ' (', LEFT(j.abr_depart,4), ') - ', LEFT(k.nom_paisxx,3), ' - ', i.cod_ciudad ) AS nom_ciudes, 
                       IF( a.fec_plalle IS NULL OR a.fec_plalle = '0000-00-00 00:00:00', '$mObs[1]', DATE_FORMAT(a.fec_plalle, '%H:%i %d-%m-%Y') ) AS fec_plalle, 
                       IF( a.fec_llegad IS NULL OR a.fec_llegad = '0000-00-00 00:00:00', '$mObs[1]', DATE_FORMAT(a.fec_llegad, '%H:%i %d-%m-%Y') ) AS fec_llegad, 
                       IF( a.con_telef1 IS NULL OR a.con_telef1 = '', ( IF(m.num_telef1 IS NULL OR m.num_telef1 = '', '$mObs[0]', m.num_telef1) ), a.con_telef1 ) AS con_telef1, 
                       IF( a.fec_citcar IS NULL OR a.fec_citcar = '0000-00-00', '$mObs[1]', DATE_FORMAT( CONCAT( a.fec_citcar, ' ', a.hor_citcar ), '%H:%i %d-%m-%Y') ) AS fec_citcar 
                  FROM ".BASE_DATOS.".tab_bitaco_corona a
             LEFT JOIN ".BASE_DATOS.".tab_genera_marcas b
                    ON a.cod_marcax = b.cod_marcax
             LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas c
                    ON a.cod_lineax = c.cod_lineax AND
                       a.cod_marcax = c.cod_marcax
             LEFT JOIN ".BASE_DATOS.".tab_vehige_colore d
                    ON a.cod_colorx = d.cod_colorx
             LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc e
                    ON a.cod_carroc = e.cod_carroc 
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad f 
                    ON a.cod_ciuori = f.cod_ciudad 
            INNER JOIN ".BASE_DATOS.".tab_genera_depart g 
                    ON f.cod_depart = g.cod_depart 
                   AND f.cod_paisxx = g.cod_paisxx 
            INNER JOIN ".BASE_DATOS.".tab_genera_paises h 
                    ON f.cod_paisxx = h.cod_paisxx 
             LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad i 
                    ON a.cod_ciudes = i.cod_ciudad 
            INNER JOIN ".BASE_DATOS.".tab_genera_depart j 
                    ON i.cod_depart = j.cod_depart 
                   AND i.cod_paisxx = j.cod_paisxx 
            INNER JOIN ".BASE_DATOS.".tab_genera_paises k 
                    ON i.cod_paisxx = k.cod_paisxx 
             LEFT JOIN ".BASE_DATOS.".tab_genera_rutasx l 
                    ON a.cod_rutasx = l.cod_rutasx 
             LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer m 
                    ON a.cod_conduc = m.cod_tercer 
             LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer n 
                    ON a.cod_asegur = n.cod_tercer 
             LEFT JOIN ".BASE_DATOS.".tab_genera_produc o 
                    ON a.cod_mercan = o.cod_produc 
                 WHERE a.num_despac = '".$num_despac."' AND 
                       a.num_consec = '1'
              ";
      $mConsult = new Consulta($mSql, $this -> conexion);
      $mResult = $mConsult -> ret_matrix('a');

      if( sizeof($mResult) > 0 )
        $mResult[0]['tip_transp'] = $this -> ArrayTipTansp[ $mResult[0]['tip_transp'] ];

      return $mResult[0];
    }

    /*! \fn: verifiCambio
     *  \brief: Verifica si el dato tiene cambios de información; sin cambio retorna texto sin color de lo contrario retorna el texto con color rojo
     *  \author: Ing. Fabian Salinas
     *  \date: 25/05/2015
     *  \date modified: 08/09/2015
     *  \modifi by: Ing. Fabian Salinas
     *  \param: old     String  Dato a comparar de Bitacora
     *  \param: new     String  Dato actual a comparar
     *  \param: titu    String  Titulo del Campo
     *  \return: String
     */
    function verifiCambio($old, $new, $titu = NULL)
    {
        if($old != $new ){
            $this->ArrayComp[$titu] = $old;
            return '<span style="color:#8A0808;">'.$new.'</span>';
        }else
            return $new;
    }

    /*! \fn: verDiferencias
     *  \brief: Muestra el primer registro cuando hay diferencias de datos en el despacho
     *  \author: Ing. Fabian Salinas
     *  \date: 25/05/2015
     *  \date modified: dia/mes/año
     *  \param: 
     *  \return: html con los primeros datos.
     */
    private function verDiferencias()
    {
        $mHtml = '';
        foreach ($this->ArrayComp as $key => $value) 
        {
            switch ($key) 
            {
                case 'Origen':
                    $mHtml .= '<b>'.$key.'</b>: '.( Despachos::getCiudad($value) ).'.<pre style="display:inline">&#09;</pre> ';
                    break;
                case 'Destino':
                    $mHtml .= '<b>'.$key.'</b>: '.( Despachos::getCiudad($value) ).'.<pre style="display:inline">&#09;</pre> ';
                    break;
                case 'Ruta':
                    $mHtml .= '<b>'.$key.'</b>: '.( Despachos::getRutasx($value) ).'.<pre style="display:inline">&#09;</pre> ';
                    break;
                case 'Mercancia':
                    $mHtml .= '<b>'.$key.'</b>: '.( Despachos::getProduc($value) ).'.<pre style="display:inline">&#09;</pre> ';
                    break;
                case 'Tipo Transportadora':
                    $mHtml .= '<b>'.$key.'</b>: '.( $this->ArrayTipTansp[$value] ).'.<pre style="display:inline">&#09;</pre> ';
                    break;
                default:
                    $mHtml .= '<b>'.$key.'</b>: '.$value.'.<pre style="display:inline">&#09;</pre> ';
                    break;
            }
        }
        return $mHtml;
    }

    /*! \fn: getCiudad
     *  \brief: Trae el nombre de la ciudad concadenando departamento y pais
     *  \author: Ing. Fabian Salinas
     *  \date: 25/05/2015
     *  \date modified: dia/mes/año
     *  \param: codCiudad
     *  \return: NomCiudad
     */
    private function getCiudad($codCiudad)
    {
        $mSql = "SELECT CONCAT( '(', UPPER(a.abr_ciudad),' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3),' - ',a.cod_ciudad  ) AS nom_ciudad 
                   FROM ".BASE_DATOS.".tab_genera_ciudad a 
             INNER JOIN ".BASE_DATOS.".tab_genera_depart b 
                     ON a.cod_depart = b.cod_depart 
                    AND a.cod_paisxx = b.cod_paisxx 
             INNER JOIN ".BASE_DATOS.".tab_genera_paises c 
                     ON a.cod_paisxx = c.cod_paisxx 
                  WHERE a.cod_ciudad = '$codCiudad' ";
        $mConsult = new Consulta($mSql, $this -> conexion);
        $mResult = $mConsult -> ret_arreglo();
        return $mResult[0];
    }

    /*! \fn: getRutasx
     *  \brief: Trae l nombre de la ruta
     *  \author: Ing. Fabian Salinas
     *  \date: 25/05/2015
     *  \date modified: dia/mes/año
     *  \param: codRuta
     *  \return: NomRuta
     */
    private function getRutasx($codRutasx)
    {
        $mSql = "SELECT a.nom_rutasx 
                   FROM ".BASE_DATOS.".tab_genera_rutasx a 
                  WHERE a.cod_rutasx = '$codRutasx' ";
        $mConsult = new Consulta($mSql, $this -> conexion);
        $mResult = $mConsult -> ret_arreglo();
        return $mResult[0];
    }

    /*! \fn: getProduc
     *  \brief: trae el nombre del producto
     *  \author: Ing. Fabian Salinas
     *  \date: 25/05/2015
     *  \date modified: dia/mes/año
     *  \param: codProducto
     *  \return: NonProduc
     */
    private function getProduc($codProduc)
    {
        $mSql = "SELECT a.nom_produc 
                   FROM ".BASE_DATOS.".tab_genera_produc a 
                  WHERE a.cod_produc = '$codProduc' ";
        $mConsult = new Consulta($mSql, $this -> conexion);
        $mResult = $mConsult -> ret_arreglo();
        return $mResult[0];
    }

    /*! \fn: linkLlamada
     *  \brief: Crea el link para generar las llamadas y limpia el Número de celular
     *  \author: Ing. Fabian Salinas
     *  \date: 25/05/2015
     *  \date modified: dia/mes/año
     *  \param: NumCelular, Placa, NumDespac
     *  \return: link llamada con celular, placa, NumDespac
     */
    private function linkLlamada($mNumCel, $mPlacax, $mDespac)
    {
      $tofind1 = array( "(\.)", "(\,)", "(\ )", "(ñ)", "(Ñ)", "(\°)", "(\º)", "(&)", "(Â)", "(\()", "(\))", "(\/)", "(\´)", "(\¤)", "(\Ã)", "(\‘)", "(\ƒ)", "(\â)", "(\€)", "(\˜)", "(\¥)", "(Ò)", "(Í)", "(\É)", "(\Ãƒâ€šÃ‚Â)", "(\·)", "(\ª)", "(\-)", "(\+)", "(\Ó)", "(\é)", "(\;)", "(\¡)", "(\!)", "(\`)", "(\<)", "(\>)", "(\_)", "(\%20)", "(\–)", "(\“)", "(\…)", "(\’)", "(\")", "(\#)", "(\š)", "(\‚)", "(\:)", "(\”)", "(\–)", "(\•)", "(\])", "(\')", "(a)", "(b)", "(c)", "(d)", "(e)", "(f)", "(g)", "(h)", "(i)", "(j)", "(k)", "(l)", "(m)", "(n)", "(o)", "(p)", "(q)", "(r)", "(s)", "(t)", "(u)", "(v)", "(w)", "(x)", "(y)", "(z)", "(A)", "(B)", "(C)", "(D)", "(E)", "(F)", "(G)", "(H)", "(I)", "(J)", "(K)", "(L)", "(M)", "(N)", "(O)", "(P)", "(Q)", "(R)", "(S)", "(T)", "(U)", "(V)", "(W)", "(X)", "(Y)", "(Z)" );
      $replac1 = array( "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "" , "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "" );

      $mNumero = str_replace(" ", "", ( preg_replace($tofind1, $replac1, $mNumCel) ) );

      if( is_numeric($mNumero) && strlen($mNumero) == 10 ){
        return $mResult = ' - <a target="blank" href="sip:'.$mNumero.'_'.$mPlacax.'_'.$mDespac.'@pbx.intrared.net">'.$mNumero.'</a>';
      }
      else{
        echo "<script>
                $(document).ready(function(){
                  alert('Por Favor Corregir el N\u00FAmero de Celular del Conductor. Verifique que Sea N\u00FAmerico, Longitud Igual a 10');
                });
              </script>";
        return $mResult = ' - '.$mNumCel;
      }
    }

    /*! \fn: getView
     *  \brief: Trae los indicadores de secciones visibles por encargado (Perfil)
     *  \author: Ing. Fabian Salinas
     *  \date:  23/09/2015
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: mCatego   String   campo categoria a retornar
     *  \return: Object
     */
    public function getView( $mCatego )
    {
        $mSql = "SELECT a.jso_bandej, a.jso_encabe, a.jso_plarut 
                   FROM ".BASE_DATOS.".tab_genera_respon a 
             INNER JOIN ".BASE_DATOS.".tab_genera_perfil b 
                     ON a.cod_respon = b.cod_respon 
                  WHERE b.cod_perfil = '".$_SESSION['datos_usuario']['cod_perfil']."' ";
        $mConsult = new Consulta($mSql, $this -> conexion);
        $mData = $mConsult->ret_matrix('a');

        return json_decode($mData[0][$mCatego]);
    }

    /*! \fn: getViewUsu
     *  \brief: Trae los permisos adicionales por usuario logueado
     *  \author: Ing. Fabian Salinas
     *  \date:  24/02/2016
     *  \date modified: dd/mm/aaaa
     *  \modified by: 
     *  \param: 
     *  \return: Object/Boolean
     */
    public function getViewUsu(){
      $mSql = "SELECT a.jso_autori 
                 FROM ".BASE_DATOS.".tab_autori_usuari a 
           INNER JOIN ".BASE_DATOS.".tab_genera_usuari b 
                   ON a.cod_conusu = b.cod_consec 
                WHERE b.cod_usuari = '".$_SESSION['datos_usuario']['cod_usuari']."' ";
        $mConsult = new Consulta($mSql, $this -> conexion);
        $mData = $mConsult->ret_matrix('a');

        if( sizeof($mData) > 0 )
          return json_decode($mData[0]['jso_autori']);
        else
          return false;
    }

    /*! \fn: getRutasDespac
     *  \brief: Trae las rutas del despacho
     *  \author: Ing. Fabian Salinas
     *  \date: 11/06/2015
     *  \date modified: dia/mes/año
     *  \param: NumDespac
     *  \return: matriz
     */
    function getRutasDespac($mNumDespac)
    {
        $mSql = "SELECT a.cod_rutasx 
                   FROM ".BASE_DATOS.".tab_despac_seguim a 
                  WHERE a.num_despac = '{$mNumDespac}' 
               GROUP BY a.cod_rutasx 
               ORDER BY a.cod_rutasx ";
        $mConsult = new Consulta( $mSql, $this -> conexion );
        return $mConsult -> ret_matrix('i');
    }

    /*! \fn: GridCitaCargue
     *  \brief: Arma HTML contenido grilla de cargue
     *  \author: 
     *  \date: dd/mm/aaaa
     *  \date modified: 21/10/2015
     *  \modified by: Ing. Fabian Salinas
     *  \param: despac   Integer  Numero de despacho
     *  \param: desReal  Integer  Numero de despacho Original
     *  \return: HTML
     */
    function GridCitaCargue( $despac, $desReal )
    {
        $mSql = "SELECT a.ind_cumcar, 
                        DATE_FORMAT(a.fec_cumcar, '%Y-%m-%d') AS fec_cumcar,
                        DATE_FORMAT(a.fec_cumcar, '%H:%i:%s') AS hor_cumcar,
                        b.nom_noveda,
                        UPPER(a.obs_cumcar) AS obs_cumcar,
                        a.usr_cumcar
                   FROM ".BASE_DATOS.".tab_despac_sisext a,
                        ".BASE_DATOS.".tab_genera_noveda b
                  WHERE a.num_despac IN (".$despac.")
                    AND b.cod_noveda = a.nov_cumcar ";

        $consulta   = new Consulta($mSql, $this -> conexion);
        $_sisextx   = $consulta -> ret_matriz();
        $ind_cumcar = $_sisextx[0][ind_cumcar];
        #----------------------------------------------------------------------------------------
        
        $_HTML  = '';
        
        
        echo $this->GridStyle();

        #$_HTML .= "<td colspan='4' style='margin: 0; padding: 0;'>";
            $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0">';
            $_HTML .= '<tr>';
                $_HTML .= '<th class="cellth-ltb" style="width: 150px;">Cumpli&oacute; Cita de Cargue?</th>';
                $_HTML .= '<td class="cellth-tb"  style="width: 30px;" valign="middle" align="center"><label>Si</label></td>';

                if(is_null($ind_cumcar))
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center"><input type="radio" id="ind_cumpli" name="ind_cumpli" value="1" onclick="validateCumpliSeguimCargue2()"/></td>';
                elseif((int)$ind_cumcar==1)
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px; font-weight: bold;" valign="middle" align="center">X</td>';
                else
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center">&nbsp;</td>';

                $_HTML .= '<td class="cellth-tb"  style="width: 30px;" valign="middle" align="center"><label>No</label></td>';

                if(is_null($ind_cumcar))
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center"><input type="radio" id="ind_cumpli" name="ind_cumpli" value="0" onclick="validateCumpliSeguimCargue2()"/></td>';
                elseif((int)$ind_cumcar==0)
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px; font-weight: bold;" valign="middle" align="center">X</td>';
                else
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center">&nbsp;</td>';

                $_HTML .= '<td style="margin: 0; padding: 0;">';
                    $_HTML .= '<div width="100%" style="margin: 0; padding: 0; border: none; border-top: 1px solid #999999; border-bottom: 1px solid #999999;">';
                         $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0" id="frm_cumpli" style="opacity: '.(is_null($ind_cumcar)?0:1).';">';
                            $_HTML .= '<tr>';
                                $_HTML .= '<th class="cellth-lb">Fecha</th>';
                                $_HTML .= '<th class="cellth-lb">Hora</th>';
                                $_HTML .= '<th class="cellth-lb">Novedad</th>';
                                $_HTML .= '<th class="cellth-lb">Observaci&oacute;n</th>';
                                $_HTML .= is_null($ind_cumcar) ? '<th class="cellth-lb" colspan="2">Tiempo Extra</th>' : '';
                                $_HTML .= '<th class="cellth-lb">'.(is_null($ind_cumcar)?'&nbsp;':'Usuario').'</th>';
                            $_HTML .= '</tr>';
                            $_HTML .= '<tr>';

                                if(is_null($ind_cumcar))
                                    $_HTML .= '<td rowspan="2" class="celltd-l" style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo dates" size="10" id="fec_cumpli" name="fec_cumpli" value="'.date('Y-m-d').'"></td>';
                                else
                                    $_HTML .= '<td rowspan="2" class="celltd-l" style="width: 15%;" align="center">'.$_sisextx[0][fec_cumcar].'</td>';

                                if(is_null($ind_cumcar))
                                    $_HTML .= '<td rowspan="2" style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo hours" size="6" id="hor_cumpli" name="hor_cumpli" value="'.date('H:i').'"></td>';
                                else
                                    $_HTML .= '<td rowspan="2" style="width: 15%;" align="center">'.$_sisextx[0][hor_cumcar].'</td>';
                                
                                if(is_null($ind_cumcar))
                                    $_HTML .= '<td rowspan="2" align="center" style="width: 15%;"><select name="nov_cumpli" id="nov_cumpli" style="font-size: 11px; font-family: Arial,Helvetica,sans-serif; width: 99%;"><option value="">---</option></select></td>';
                                else
                                    $_HTML .= '<td rowspan="2" align="center" style="width: 15%;">'.$_sisextx[0][nom_noveda].'</td>';
                                
                                if(is_null($ind_cumcar))
                                    $_HTML .= '<td rowspan="2" align="center"><textarea name="obs_cumpli" id="obs_cumpli" onKeyUp="UpperText($(this) )" rows="2" style="width: 100%; font-size: 11px; font-family: Arial,Helvetica,sans-serif;"></textarea></td>';
                                else
                                    $_HTML .= '<td rowspan="2" align="center">'.strtoupper($_sisextx[0][obs_cumcar]).'</td>';

                                if(is_null($ind_cumcar))
                                {#Tiempo Extra
                                    $_HTML .= '<td align="center"> Si <input type="radio" id="ind_timextID" name="ind_timext" value="1" onClick="timeExtra(1);" /></td>';
                                    $_HTML .= '<td align="center"> No <input type="radio" id="ind_timextID" name="ind_timext" value="0" checked="true" onClick="timeExtra(0);" /></td>';
                                }

                                if(is_null($ind_cumcar))
                                    $_HTML .= '<td rowspan="2" style="width: 15%;" align="center"><input type="button" value="Registrar" onClick="SaveCumpli();" name="Protocolos" class="crmButton small save"></td>';
                                else
                                    $_HTML .= '<td rowspan="2" style="width: 15%;" align="center">'.$_sisextx[0][usr_cumcar].'</td>';  
 
                            $_HTML .= '</tr>';

                            if(is_null($ind_cumcar))
                            {#Tiempo Extra
                                $_HTML .= '<tr>';
                                    $_HTML .= '<td id="tdHourExtID" align="center"></td>';#Tiempo Extra
                                    $_HTML .= '<td id="tdDateExtID" align="center"></td>';#Tiempo Extra
                                $_HTML .= '</tr>';
                            }
                        $_HTML .= '</table>';
                    $_HTML .= '</div>';
                $_HTML .= '</td>';
            $_HTML .= '</tr>';
            $_HTML .= '</table>';
        #$_HTML .= "</td>";

        // nom_noveda
        $mSelect = "SELECT a.num_consec, a.num_despac, a.fec_citcar, 
                           a.ind_cumcar, a.fec_cumcar, a.nov_cumcar, 
                           a.obs_cumcar, a.usr_modifi, b.nom_noveda
                      FROM ".BASE_DATOS.".tab_consol_citcar a
                 LEFT JOIN ".BASE_DATOS.".tab_genera_noveda b
                        ON a.nov_cumcar = b.cod_noveda
                     WHERE num_despac IN (".$despac.") 
                  ORDER BY fec_citcar";
        
        $consulta   = new Consulta($mSelect, $this -> conexion);
        $_gridadi   = $consulta -> ret_matriz();

        if( sizeof( $_gridadi ) > 0 )
        {
          foreach( $_gridadi as $row )
          {
            $ind_cumcar  = $row['ind_cumcar'];
            $date_cumcar = explode( ' ', $row['fec_cumcar'] );

            #$_HTML  = "</tr><tr><td colspan='4' style='margin: 0; padding: 0;'>";
            $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0">';
            $_HTML .= '<tr>';
                $_HTML .= '<th class="cellth-ltb" style="width: 150px;">Cumpli&oacute; Cita de Cargue Programada para '.$row['fec_citcar'].' ?</th>';
                $_HTML .= '<td class="cellth-tb"  style="width: 30px;" valign="middle" align="center"><label>Si</label></td>';

                if(is_null($ind_cumcar))
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center"><input type="radio" id="ind_cumadi'.$row['num_consec'].'" name="ind_cumadi'.$row['num_consec'].'" value="1" /></td>';
                elseif((int)$ind_cumcar==1)
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px; font-weight: bold;" valign="middle" align="center">X</td>';
                else
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center">&nbsp;</td>';

                $_HTML .= '<td class="cellth-tb"  style="width: 30px;" valign="middle" align="center"><label>No</label></td>';

                if(is_null($ind_cumcar))
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center"><input type="radio" id="ind_cumadi'.$row['num_consec'].'" name="ind_cumadi'.$row['num_consec'].'" value="0" /></td>';
                elseif((int)$ind_cumcar==0)
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px; font-weight: bold;" valign="middle" align="center">X</td>';
                else
                    $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center">&nbsp;</td>';

                $_HTML .= '<td style="margin: 0; padding: 0;">';
                    $_HTML .= '<div width="100%" style="margin: 0; padding: 0; border: none; border-top: 1px solid #999999; border-bottom: 1px solid #999999;">';
                         $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0" id="frm_cumadi'.$row['num_consec'].'" style="opacity: '.(is_null($ind_cumcar)?0:1).';">';
                            $_HTML .= '<thead>';
                                $_HTML .= '<tr>';
                                    $_HTML .= '<th class="cellth-lb">Fecha</th>';
                                    $_HTML .= '<th class="cellth-lb">Hora</th>';
                                    $_HTML .= '<th class="cellth-lb">Novedad</th>';
                                    $_HTML .= '<th class="cellth-lb">Observaci&oacute;n</th>';
                                    $_HTML .= '<th class="cellth-lb">'.(is_null($ind_cumcar)?'&nbsp;':'Usuario').'</th>';
                                $_HTML .= '</tr>';
                            $_HTML .= '</thead>';
                            $_HTML .= '<tbody>';

                                if(is_null($ind_cumcar))
                                    $_HTML .= '<td class="celltd-l" style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo dates" size="10" id="fec_cumadi'.$row['num_consec'].'" name="fec_cumadi'.$row['num_consec'].'" value="'.date('Y-m-d').'"></td>';
                                else
                                    $_HTML .= '<td class="celltd-l" style="width: 15%;" align="center">'.$date_cumcar[0].'</td>';

                                if(is_null($ind_cumcar))
                                    $_HTML .= '<td style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo hours" size="6" id="hor_cumadi'.$row['num_consec'].'" name="hor_cumadi'.$row['num_consec'].'" value="'.date('H:i').'"></td>';
                                else
                                    $_HTML .= '<td style="width: 15%;" align="center">'.$date_cumcar[1].'</td>';
                                
                                if(is_null($ind_cumcar))
                                    $_HTML .= '<td align="center" style="width: 15%;"><select name="nov_cumadi'.$row['num_consec'].'" id="nov_cumadi'.$row['num_consec'].'" style="font-size: 11px; font-family: Arial,Helvetica,sans-serif; width: 99%;"><option value="">---</option></select></td>';
                                else
                                    $_HTML .= '<td align="center" style="width: 15%;">'.$row['nom_noveda'].'</td>';
                                
                                if(is_null($ind_cumcar))
                                    $_HTML .= '<td align="center"><textarea name="obs_cumadi'.$row['num_consec'].'" id="obs_cumadi'.$row['num_consec'].'" rows="2" style="width: 100%; font-size: 11px; font-family: Arial,Helvetica,sans-serif;"></textarea></td>';
                                else
                                    $_HTML .= '<td align="center">'.$row['obs_cumcar'].'</td>';
                                
                                if(is_null($ind_cumcar))
                                    $_HTML .= '<td style="width: 15%;" align="center"><input type="button" value="Registrar" onClick="SaveCumadi(\''.$row['num_consec'].'\');" name="Protocolos" class="crmButton small save"></td>';
                                else
                                    $_HTML .= '<td style="width: 15%;" align="center">'.$row['usr_modifi'].'</td>';  
 
                        $_HTML .= '</tbody></table>';
                    $_HTML .= '</div>';
                $_HTML .= '</td>';
            $_HTML .= '</tr>';
            $_HTML .= '</table>';
            #$_HTML .= "</td>";
          }
        }

        return utf8_encode($_HTML);
    }

    /*! \fn: GridCitaDescargue
     *  \brief: Arma HTML Grilla Descargue
     *  \author: 
     *  \date: 
     *  \date modified: dd/mm/aaaa
     *  \param: despac    Integer  Numero de despacho
     *  \param: document
     *  \param: index
     *  \return: HTML
     */
    function GridCitaDescargue($despac, $document, $index)
    {
        echo $this->GridStyle();

        $mSql = "SELECT a.ind_cumdes, 
                        DATE_FORMAT(a.fec_cumdes, '%Y-%m-%d') AS fec_cumcar,
                        DATE_FORMAT(a.fec_cumdes, '%H:%i:%s') AS hor_cumcar,
                        b.nom_noveda,
                        UPPER(a.obs_cumdes),
                        a.usr_cumdes
                   FROM ".BASE_DATOS.".tab_despac_destin a,
                        ".BASE_DATOS.".tab_genera_noveda b
                  WHERE a.num_despac = '{$despac}'
                    AND a.num_docume = '{$document}'
                    AND b.cod_noveda = a.nov_cumdes ";

        $consulta   = new Consulta($mSql, $this -> conexion);
        $_sisextx   = $consulta -> ret_matriz();
        $ind_cumcar = $_sisextx[0][ind_cumdes];

        $mSelect = "SELECT IF( fec_llecli <> '0000-00-00 00:00:00', fec_llecli, '' ) AS fec_llecli, 
                           fec_inides, 
                           fec_findes
                      FROM ".BASE_DATOS.".tab_despac_destin 
                     WHERE num_despac = '{$despac}'
                       AND num_docume = '{$document}'";
        
        $consulta = new Consulta( $mSelect, $this -> conexion );
        $fec_complem = $consulta -> ret_arreglo();


        #$_HTML = '<div id="gridContainer0">'; #Se muere por el ID algun HP JavaScript lo mata
        $_HTML = '<div>';
            $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0">';
                $_HTML .= '<tr>';
                    $_HTML .= '<th class="cellth-ltb" style="width: 150px;">Cumpli&oacute; Descargue?</th>';
                    $_HTML .= '<td class="cellth-tb"  style="width: 30px;" valign="middle" align="center"><label>Si</label></td>';

                    if(is_null($ind_cumcar))
                        $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center"><input type="radio" id="ind_cumdes'.$index.'" name="ind_cumdes'.$index.'" value="1" onclick="GetOtrosDestin(\'1\', \''.$index.'\')"/></td>';
                    elseif((int)$ind_cumcar==1)
                        $_HTML .= '<td class="celltd-tb"  style="width: 30px; font-weight: bold;" valign="middle" align="center">X</td>';
                    else
                        $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center">&nbsp;</td>';

                    $_HTML .= '<td class="cellth-tb"  style="width: 30px;" valign="middle" align="center"><label>No</label></td>';

                    if(is_null($ind_cumcar))
                        $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center"><input type="radio" id="ind_cumdes'.$index.'" name="ind_cumdes'.$index.'" value="0" onclick="GetOtrosDestin(\'0\', \''.$index.'\')"/></td>';
                    elseif((int)$ind_cumcar==0)
                        $_HTML .= '<td class="celltd-tb"  style="width: 30px; font-weight: bold;" valign="middle" align="center">X</td>';
                    else
                        $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center">&nbsp;</td>';

                    $_HTML .= '<td style="margin: 0; padding: 0;">';
                        $_HTML .= '<div width="100%" style="margin: 0; padding: 0; border: none; border-top: 1px solid #999999; border-bottom: 1px solid #999999;">';
                            $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0" id="frm_cumdes'.$index.'" style="opacity: '.(is_null($ind_cumcar)?0:1).';">';
                                $_HTML .= '<tr>';
                                    $_HTML .= '<th class="cellth-lb">Fecha</th>';
                                    $_HTML .= '<th class="cellth-lb">Hora</th>';
                                    $_HTML .= '<th class="cellth-lb">Novedad</th>';
                                    $_HTML .= '<th class="cellth-lb">Observaci&otilde;n</th>';
                                    $_HTML .= '<th class="cellth-lb">'.(is_null($ind_cumcar)?'&nbsp;':'Usuario').'</th>';
                                $_HTML .= '</tr>';
                                $_HTML .= '<tr>';

                                    if(is_null($ind_cumcar))
                                        $_HTML .= '<td class="celltd-l" style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo dates" size="10" id="fec_cumdes'.$index.'" name="fec_cumdes'.$index.'" value="'.date('Y-m-d').'"></td>';
                                    else
                                        $_HTML .= '<td class="celltd-l" style="width: 15%;" align="center">'.$_sisextx[0][fec_cumcar].'</td>';

                                    if(is_null($ind_cumcar))
                                        $_HTML .= '<td style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo hours" size="6" id="hor_cumdes'.$index.'" name="hor_cumdes'.$index.'" value="'.date('H:i').'"></td>';
                                    else
                                        $_HTML .= '<td style="width: 15%;" align="center">'.$_sisextx[0][hor_cumcar].'</td>';
                                    
                                    if(is_null($ind_cumcar))
                                        $_HTML .= '<td  align="center" style="width: 15%;"><select name="nov_cumdes'.$index.'" id="nov_cumdes'.$index.'" style="font-size: 11px; font-family: Arial,Helvetica,sans-serif; width: 99%;"><option value="">---</option></select></td>';
                                    else
                                        $_HTML .= '<td  align="center" style="width: 15%;">'.$_sisextx[0][nom_noveda].'</td>';
                                    
                                    if(is_null($ind_cumcar))
                                        $_HTML .= '<td align="center"><textarea name="obs_cumdes'.$index.'" id="obs_cumdes'.$index.'" rows="2" style="width: 100%; font-size: 11px; font-family: Arial,Helvetica,sans-serif;"></textarea></td>';
                                    else
                                        $_HTML .= '<td align="center">'.$_sisextx[0][obs_cumdes].'</td>';
                                    
                                    if(is_null($ind_cumcar))
                                        $_HTML .= '<td style="width: 15%;" align="center"><input type="button" value="Registrar" onClick="SaveCumdes('.$index.');" name="Protocolos" class="crmButton small save"></td>';
                                    else
                                        $_HTML .= '<td style="width: 15%;" align="center">'.$_sisextx[0][usr_cumdes].'</td>';

                                $_HTML .= '</tr>';
                            $_HTML .= '</table>';
                        $_HTML .= '</div>';
                    $_HTML .= '</td>';
                $_HTML .= '</tr>';
            $_HTML .= '</table>';
        
            if( sizeof( $_sisextx ) > 0 )
            {
                $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0">';
                    $_HTML .= '<tr>';

                        $select =  '<select name="tip_comple'.$index.'" id="tip_comple'.$index.'" style="font-size: 11px; font-family: Arial,Helvetica,sans-serif; width: 99%;"><option value="">- Seleccione -</option>';

                        $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0">';

                        if( $fec_complem['fec_inides'] == '' )
                            $select .= '<option value="fec_inides">Entrada a Descargar</option>';
                        else
                            $_HTML .= '<tr><th align="left" class="celltd-ltb" colspan="5" style="width: 150px;">ENTRADA A DESCARGAR:&nbsp;&nbsp;&nbsp;&nbsp;'.$fec_complem['fec_inides'].'</th></tr>';

                        if( $fec_complem['fec_findes'] == '' )
                            $select .= '<option value="fec_findes">Salida del Descargue</option>';
                        else
                            $_HTML .= '<tr><th align="left" class="celltd-ltb" colspan="5" style="width: 150px;">SALIDA DE DESCARGAR:&nbsp;&nbsp;&nbsp;&nbsp;'.$fec_complem['fec_findes'].'</th></tr>';

                        if( $fec_complem['fec_llecli'] == '' )
                            $select .= '<option value="fec_llecli">Cumplido del Viaje</option>';
                        else
                            $_HTML .= '<tr><th align="left" class="celltd-ltb" colspan="5" style="width: 150px;">CUMPLIDO AL DESTINATARIO:&nbsp;&nbsp;&nbsp;&nbsp;'.$fec_complem['fec_llecli'].'</th></tr>';

                        $select .= '</select>';

                        $_HTML .= '<tr>';
                            $_HTML .= '<th class="cellth-ltb" style="width: 150px;">Seleccion de Fechas Adicionales</th>';


                            $_HTML .= '<td class="celltd-ltb" style="width: 150px;" valign="middle" align="center">'.$select.'</td>';

                            $_HTML .= '<td class="celltd-ltb" style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo dates" size="10" id="fec_comple'.$index.'" name="fec_comple'.$index.'" value="'.date('Y-m-d').'"></td>';

                            $_HTML .= '<td class="celltd-ltb" style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo hours" size="6" id="hor_comple'.$index.'" name="hor_comple'.$index.'" value="'.date('H:i').'"></td>';

                            $_HTML .= '<td class="celltd-ltb" style="width: 15%;" align="center"><input type="button" value="Registrar" onClick="SaveFechaAdicio('.$index.');" name="Fechas" class="crmButton small save"></td>';
                  
                    $_HTML .= '</tr>';
                $_HTML .= '</table>';
            }
        
        $_HTML .= '</div>';

        return utf8_encode($_HTML);
    }

    /*! \fn: GridCitaDescargueClientes
     *  \brief: Arma HTML de los clientes para la Grilla Descargue
     *  \author: 
     *  \date: 
     *  \date modified: dd/mm/aaaa
     *  \param: despac    Integer  Numero de despacho
     *  \param: cliente
     *  \param: index
     *  \return: HTML
     */
    function GridCitaDescargueClientes($despac, $cliente, $index)
    {
        $mSql = "SELECT a.ind_cumpli, a.fec_citeje, a.hor_citeje, b.nom_noveda,
                        a.obs_noveda, a.usr_modifi
                   FROM ".BASE_DATOS.".tab_despac_inddes a,
                        ".BASE_DATOS.".tab_genera_noveda b 
                  WHERE a.num_despac = '{$despac}' 
                    AND a.cod_client = '{$cliente}'
                    AND a.cod_noveda = b.cod_noveda";
        $consulta   = new Consulta($mSql, $this -> conexion);
        $_sisextx   = $consulta -> ret_matriz();
        $ind_cumcar = $_sisextx[0][ind_cumpli];
  
        $mSelect = "SELECT IF( fec_llecli <> '0000-00-00 00:00:00', fec_llecli, '' ) AS fec_llecli, 
                           fec_inides, 
                           fec_findes
                      FROM ".BASE_DATOS.".tab_despac_destin 
                     WHERE num_despac = '{$despac}'";        
        $consulta = new Consulta( $mSelect, $this -> conexion );
        $fec_complem = $consulta -> ret_arreglo();

        echo "<tr><td colspan='8' style='margin: 0; padding: 0;'>";
            $_HTML  = '';
            $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0">';
            $_HTML .= '<tr>';
            $_HTML .= '<th class="cellth-ltb" style="width: 150px;">Cumpli&oacute; el Descargue del Cliente?</th>';
            $_HTML .= '<td class="cellth-tb"  style="width: 30px;" valign="middle" align="center"><label>Si</label></td>';

            if((int)$ind_cumcar==0)
                $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center"><input type="radio" id="ind_cliente_cumdes'.$index.'" name="ind_cliente_cumdes'.$index.'" value="1" /></td>';
            elseif((int)$ind_cumcar==1)
                $_HTML .= '<td class="celltd-tb"  style="width: 30px; font-weight: bold;" valign="middle" align="center">X</td>';
            else
                $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center">&nbsp;</td>';

            $_HTML .= '<td class="cellth-tb"  style="width: 30px;" valign="middle" align="center"><label>No</label></td>';

            if((int)$ind_cumcar==0)
                $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center"><input type="radio" id="ind_cliente_cumdes'.$index.'" name="ind_cliente_cumdes'.$index.'" value="2" /></td>';
            elseif((int)$ind_cumcar==2)
                $_HTML .= '<td class="celltd-tb"  style="width: 30px; font-weight: bold;" valign="middle" align="center">X</td>';
            else
                $_HTML .= '<td class="celltd-tb"  style="width: 30px;" valign="middle" align="center">&nbsp;</td>';

            $_HTML .= '<td style="margin: 0; padding: 0;">';
                $_HTML .= '<div width="100%" style="margin: 0; padding: 0; border: none; border-top: 1px solid #999999; border-bottom: 1px solid #999999;">';
                     $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0" id="frm_cliente_cumdes'.$index.'" style="opacity: '.((int)$ind_cumcar==0?0:1).';">';
                        $_HTML .= '<thead>';
                            $_HTML .= '<tr>';
                                $_HTML .= '<th class="cellth-lb">Fecha</th>';
                                $_HTML .= '<th class="cellth-lb">Hora</th>';
                                $_HTML .= '<th class="cellth-lb">Novedad</th>';
                                $_HTML .= '<th class="cellth-lb">Observación</th>';
                                $_HTML .= '<th class="cellth-lb">'.((int)$ind_cumcar==0?'&nbsp;':'Usuario').'</th>';
                            $_HTML .= '</tr>';
                        $_HTML .= '</thead>';
                        $_HTML .= '<tbody>';

                            if((int)($ind_cumcar)==0)
                                $_HTML .= '<td class="celltd-l" style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo dates" size="10" id="fec_cumdes_cliente'.$index.'" name="fec_cumdes_cliente'.$index.'" value=""></td>';
                            else
                                $_HTML .= '<td class="celltd-l" style="width: 15%;" align="center">'.$_sisextx[0][fec_citeje].'</td>';

                            if((int)($ind_cumcar)==0)
                                $_HTML .= '<td style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo hours" size="6" id="hor_cumdes_cliente'.$index.'" name="hor_cumdes_cliente'.$index.'" value=""></td>';
                            else
                                $_HTML .= '<td style="width: 15%;" align="center">'.$_sisextx[0][hor_citeje].'</td>';
                            
                            if((int)($ind_cumcar)==0)
                                $_HTML .= '<td  align="center" style="width: 15%;"><select name="nov_cumdes_cliente'.$index.'" id="nov_cumdes_cliente'.$index.'" style="font-size: 11px; font-family: Arial,Helvetica,sans-serif; width: 99%;"><option value="">---</option></select></td>';
                            else
                                $_HTML .= '<td  align="center" style="width: 15%;">'.$_sisextx[0][nom_noveda].'</td>';
                            
                            if((int)($ind_cumcar)==0)
                                $_HTML .= '<td align="center"><textarea name="obs_cumdes_cliente'.$index.'" id="obs_cumdes_cliente'.$index.'" rows="2" style="width: 100%; font-size: 11px; font-family: Arial,Helvetica,sans-serif;"></textarea></td>';
                            else
                                $_HTML .= '<td align="center">'.$_sisextx[0][obs_noveda].'</td>';
                            
                            if((int)($ind_cumcar)==0)
                                $_HTML .= '<td style="width: 15%;" align="center"><input type="button" value="Registrar" onClick="SaveCumdesCliente('.$index.','.$cliente.');" name="Protocolos" class="crmButton small save"></td>';
                            else
                                $_HTML .= '<td style="width: 15%;" align="center">'.$_sisextx[0][usr_modifi].'</td>';
                        
                        $_HTML .= '</tbody>';
                     $_HTML .= '</table>';
                $_HTML .= '</div>';
            $_HTML .= '</td>';
        $_HTML .= '</tr>';
        $_HTML .= '</table>';
        
        if( sizeof( $_sisextx ) > 0 )
        {
            $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0">';
            $_HTML .= '<tr>';

                $select =  '<select name="tip_comple'.$index.'" id="tip_comple'.$index.'" style="font-size: 11px; font-family: Arial,Helvetica,sans-serif; width: 99%;"><option value="">- Seleccione -</option>';

                  $_HTML .= '<table width="100%" border="0" cellspacing="0" cellspadding="0">';
                  
                  if( $fec_complem['fec_inides'] == '' )
                    $select .= '<option value="fec_inides">Entrada a Descargar</option>';
                  else
                    $_HTML .= '<tr><th align="left" class="celltd-ltb" colspan="5" style="width: 150px;">ENTRADA A DESCARGAR:&nbsp;&nbsp;&nbsp;&nbsp;'.$fec_complem['fec_inides'].'</th></tr>';

                  if( $fec_complem['fec_findes'] == '' )
                    $select .= '<option value="fec_findes">Salida del Descargue</option>';
                  else
                    $_HTML .= '<tr><th align="left" class="celltd-ltb" colspan="5" style="width: 150px;">SALIDA DE DESCARGAR:&nbsp;&nbsp;&nbsp;&nbsp;'.$fec_complem['fec_findes'].'</th></tr>';

                  if( $fec_complem['fec_llecli'] == '' )
                    $select .= '<option value="fec_llecli">Cumplido del Viaje</option>';
                  else
                    $_HTML .= '<tr><th align="left" class="celltd-ltb" colspan="5" style="width: 150px;">CUMPLIDO DEL VIAJE:&nbsp;&nbsp;&nbsp;&nbsp;'.$fec_complem['fec_llecli'].'</th></tr>';

                  $select .= '</select>';
                  
                  $_HTML .= '<tr>';
                    $_HTML .= '<th class="cellth-ltb" style="width: 150px;">Seleccion de Fechas Adicionales</th>';
                  
                  
                  $_HTML .= '<td class="celltd-ltb" style="width: 150px;" valign="middle" align="center">'.$select.'</td>';

                  $_HTML .= '<td class="celltd-ltb" style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo dates" size="10" id="fec_comple'.$index.'" name="fec_comple'.$index.'" value="'.date('Y-m-d').'"></td>';

                  $_HTML .= '<td class="celltd-ltb" style="width: 15%;" align="center"><input style="font-size: 11px; font-family: Arial,Helvetica,sans-serif;" type="text" class="campo hours" size="6" id="hor_comple'.$index.'" name="hor_comple'.$index.'" value="'.date('H:i').'"></td>';

                  $_HTML .= '<td class="celltd-ltb" style="width: 15%;" align="center"><input type="button" value="Registrar" onClick="SaveFechaAdicio('.$index.');" name="Fechas" class="crmButton small save"></td>';
              
                $_HTML .= '</tr>';
            $_HTML .= '</table>';
        }


        echo $_HTML;       
        echo "</td></tr>";
    }

    /*! \fn: updateDestin
     *  \brief: Actualiza los destinatarios de un despachos que nombre sea NULL o Sctring Vacio '' reemplazandolo por -
     *  \author: Ing. Fabian Salinas
     *  \date: 12/05/2016
     *  \date modified: dd/mm/aaaa
     *  \param: mNumDespac  Integer  Numero del despacho
     *  \return: 
     */
    private function updateDestin($mNumDespac){
        $mSql = "UPDATE ".BASE_DATOS.".tab_despac_destin 
                    SET nom_destin = '-' 
                  WHERE num_despac = '{$mNumDespac}' 
                    AND (nom_destin IS NULL OR nom_destin = '') ";
        new Consulta( $mSql, $this -> conexion );
    }

    /*! \fn: getNovedadNEM
     *  \brief: Verifico si la novedad es Movil
     *  \author: Edward Serrano
     *  \date: 16/05/2017
     *  \date modified: dd/mm/aaaa
     *  \param: mNumNovedad  Integer  Codigo de la novedad
     *  \return: 
     */
    private function getNovedadNEM($mNumNovedad){
        $mSql = "SELECT * FROM ".BASE_DATOS.".tab_genera_noveda a
        			INNER JOIN ".BASE_DATOS.".tab_despac_novnem b ON
        			a.cod_noveda = b.cod_noveda
                  WHERE a.nom_noveda LIKE '%movil%' AND
                  		b.ind_soluci = 0 AND 
                        a.cod_noveda = ".$mNumNovedad;
        $consulta = new Consulta( $mSql, $this -> conexion );
        return $consulta -> ret_arreglo();
    }

    /*! \fn: getDespLlegada
     *  \brief: Verifica que el despacho tenga la observacion en llegada
     *  \author: Edward Serrano
     *  \date: 12/05/2016
     *  \date modified: dd/mm/aaaa
     *  \param: mNumDespac  Integer  Numero del despacho
     *  \return: 
     */
    private function getDespLlegada($mNumDespac){
        $mSql = "SELECT a.fec_llegad, 
                        a.obs_llegad,
                        a.usr_modifi,
                        a.fec_modifi
                   FROM ".BASE_DATOS.".tab_despac_despac a
                  WHERE a.num_despac = '{$mNumDespac}' 
                  AND a.fec_llegad IS NOT NULL";

        $consulta   = new Consulta($mSql, $this -> conexion);
        $mResultLlegada = $consulta -> ret_matriz(); 
        #valido que tenga informacion la conuslta
        if(sizeof($mResultLlegada) > 0)
        {
            return $mResultLlegada;
        }
        else
        {
            return NULL;
        }

    }
}

?>