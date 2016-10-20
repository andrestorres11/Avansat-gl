<?php

ini_set('memory_limit','128M');
session_start();

include ("../lib/general/constantes.inc");
include ("../lib/general/conexion_lib.inc");
include ("../lib/general/form_lib.inc");
include ("../lib/general/paginador_lib.inc");
include ("../lib/bd/seguridad/aplica_filtro_usuari_lib.inc");
include ("../lib/bd/seguridad/aplica_filtro_perfil_lib.inc");
include("../../".$_REQUEST['db']."/constantes.inc");


class ExportRecurs{
    
   var $conexion;
   
    function ExportRecurs(){ 
        $this -> conexion = new Conexion(HOST, USUARIO, CLAVE, $_REQUEST["db"]);
        switch( $_REQUEST[op] ){
            case 1:
                $_HTML = $this -> ExcelTerceros();
                $archivo = "Listado_de_Terceros_".date('YmdHis');
            break;
            case 2:
                $_HTML = $this -> ExcelVehiculos();
                $archivo = "Listado_de_Vehiculos_".date('YmdHis');
            break;
            case 3:
                $_HTML = $this -> ExcelConductores();
                $archivo = "Listado_de_Conductores_".date('YmdHis');
            break;
        }
        header('Content-Type: application/octetstream');
        header('Expires: 0');
        header('Content-Disposition: attachment; filename="'.$archivo.'.xls"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        echo $_HTML;
    }
    
    
    function ExcelTerceros(){

        
        
        $datos_usuario = $_SESSION[datos_usuario];
        $usuario = $datos_usuario["cod_usuari"];

        $query = "SELECT IF(a.cod_tipdoc != 'C', 'X', '') AS 'JURIDICO',
                         IF(a.cod_tipdoc  = 'C', 'X', '') AS 'NATURAL',
                         d.nom_tipdoc AS 'TIPO DOCUMENTO',
                         a.cod_tercer AS 'NO. DOCUMENTO',
                         a.num_verifi AS 'DIGITO VERIFICACION',
                         CONCAT_WS(' ', a.nom_tercer, a.nom_apell1, a.nom_apell2) AS 'NOMBRE', 
                         a.abr_tercer AS 'ABREVIATURA', 
                         e.nom_terreg AS 'REGIMEN',
                         f.nom_ciudad AS 'CIUDAD',
                         a.dir_domici AS 'DIRECCION',
                         a.num_telef1 AS 'TELEFONO 1',
                         a.num_telef2 AS 'TELEFONO 2',
                         a.num_telmov AS 'CELULAR',
                         a.num_faxxxx AS 'FAX',
                         TRIM(a.dir_urlweb) AS 'PAGINA WEB',
                         TRIM(a.dir_emailx) AS 'E-MAIL',
                         GROUP_CONCAT(DISTINCT g.cod_activi
				ORDER BY g.cod_activi ASC
				SEPARATOR ', ') AS 'ACTIVIDADES'
                    FROM ".BASE_DATOS.".tab_tercer_tercer a
                         INNER JOIN ".BASE_DATOS.".tab_transp_tercer b 
                                 ON a.cod_tercer = b.cod_tercer 
                         INNER JOIN ".BASE_DATOS.".tab_tercer_activi c
                                 ON a.cod_tercer = c.cod_tercer 
                         INNER JOIN ".BASE_DATOS.".tab_genera_tipdoc d
                                 ON d.cod_tipdoc = a.cod_tipdoc
                         INNER JOIN ".BASE_DATOS.".tab_genera_terreg e
                                 ON e.cod_terreg = a.cod_terreg
                         INNER JOIN ".BASE_DATOS.".tab_genera_ciudad f
                                 ON f.cod_ciudad = a.cod_ciudad
                         INNER JOIN ".BASE_DATOS.".tab_tercer_activi g
                                 ON g.cod_tercer = a.cod_tercer
                         
                   WHERE c.cod_activi <> ".COD_FILTRO_EMPTRA." AND
                         c.cod_activi <> ".COD_FILTRO_AGENCI." AND
                         c.cod_activi <> ".COD_FILTRO_CONDUC." ";

        if($datos_usuario["cod_perfil"] == ""){
            $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
            if($filtro -> listar($this -> conexion)){
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
            }
            $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
            if($filtro -> listar($this -> conexion)){
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
            }
            $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_usuari"]);
            if($filtro -> listar($this -> conexion)){
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
            }
        }else{
            $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
            if($filtro -> listar($this -> conexion)){
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
            }
            $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
            if($filtro -> listar($this -> conexion)){
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
            }
            $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CLIENT,$datos_usuario["cod_perfil"]);
            if($filtro -> listar($this -> conexion)){
                $datos_filtro = $filtro -> retornar();
                $query = $query . " AND a.cod_tercer = '$datos_filtro[clv_filtro]' ";
            }
        }

        if($_REQUEST[transp])
            $query .= " AND b.cod_transp = '".$_REQUEST[transp]."'";
        if($_REQUEST[activi])
            $query = $query." AND c.cod_activi = ".$_REQUEST[activi]."";
        if($_REQUEST[fil] == 1)
            $query = $query." AND a.cod_tercer = '".$_REQUEST[tercer]."'";
        else if($_REQUEST[fil] == 2)
            $query = $query." AND a.abr_tercer LIKE '%".$_REQUEST[tercer]."%'";
        else if($_REQUEST[fil] == 3)
            $query = $query." AND a.cod_estado = ".COD_ESTADO_ACTIVO."";
        else if($_REQUEST[fil] == 4)
            $query = $query." AND a.cod_estado = ".COD_ESTADO_INACTI."";
        else if($_REQUEST[fil] == 5)
            $query = $query." AND a.cod_estado = ".COD_ESTADO_PENDIE."";

        $query = $query." GROUP BY a.cod_tercer ORDER BY 6";

        $consec = new Consulta($query, $this -> conexion);
        $matriz = $consec -> ret_matriz('i');
        
        $styles = array(
            'center',
            'center',
            'center',
            'right',
            'center',
            'left',
            'left',
            'left',
            'left',
            'left',
            'right',
            'right',
            'right',
            'right',
            'left',
            'left'
        );
        
        $_HTML  = "<table border='0' cellspacing='0' cellpadding='0' width='4000'>";
        $_HTML .= "<thead>";
        $_HTML .= "<tr>";
            $_HTML .= "<th colspan='2'  style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222)'>TIPO TERCERO</th>";
            $_HTML .= "<th colspan='14' style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242)'>DATOS BASICO</th>";
            $_HTML .= "<th colspan='6'  style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222)'>ACTIVIDADES</th>";
        $_HTML .= "</tr>";
        $_HTML .= "<tr>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>JURIDICO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>NATURAL</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 150px;'>TIPO DOCUMENTO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px; '>NO. DOCUMENTO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 150px;'>DIGITO VERIFICACION</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 350px;'>NOMBRE</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 350px;'>ABREVIATURA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 150px;'>REGIMEN</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 150px;'>CIUDAD</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 2000px;'>DIRECCION</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>TELEFONO 1</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>TELEFONO 2</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>CELULAR</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>FAX</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 150px;'>PAGINA WEB</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>E-MAIL</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>ASEGURADORA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>EMPLEADO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>GENERADOR DE CARGA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>POSEEDOR</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>PROPIETARIO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>TOMADOR DE POLIZA</th>";
        $_HTML .= "</tr>";
        $_HTML .= "</thead>";
        $_HTML .= "<tbody>";
        
        foreach ($matriz as $key => $row){
            $_HTML .= "<tr>";
            foreach (array_keys($row) as $title){
                if($title<=1)
                    $style= "style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align: $styles[$title];'";
                if($title>1 && $title<=15)
                    $style= "style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); text-align: $styles[$title];'";
                
                if(is_numeric($title) && $title != 16){
                    $_HTML .= "<td $style >".trim($row[$title])."&nbsp;</td>";
                }
            }
            if(in_array(7, explode(', ', $row[16])))
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>X</td>";
            else
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>&nbsp;</td>";
            if(in_array(9, explode(', ', $row[16])))
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>X</td>";
            else
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>&nbsp;</td>";
            if(in_array(3, explode(', ', $row[16])))
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>X</td>";
            else
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>&nbsp;</td>";
            if(in_array(6, explode(', ', $row[16])))
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>X</td>";
            else
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>&nbsp;</td>";
            if(in_array(5, explode(', ', $row[16])))
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>X</td>";
            else
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>&nbsp;</td>";
            if(in_array(8, explode(', ', $row[16])))
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>X</td>";
            else
                $_HTML .= "<td style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align:center;'>&nbsp;</td>";
            $_HTML .= "</tr>";
        }
        $_HTML .= "</tbody>";
        $_HTML .= "</table>";
        return $_HTML;

    }
    
    function ExcelVehiculos(){
        
        $datos_usuario = $_SESSION[datos_usuario];
        $usuario = $datos_usuario["cod_usuari"];
        
        $query = "SELECT a.num_placax, 
                         b.nom_marcax, 
                         c.nom_lineax, 
                         a.ano_modelo, 
                         a.ano_repote,
                         d.nom_colorx, 
                         k.nom_tipveh,
                         e.nom_carroc, 
                         a.num_motorx,
                         a.num_seriex,
                         a.val_pesove,
                         a.val_capaci,
                         a.num_config,
                         a.nom_vincul,
                         a.fec_vigvin,
                         a.num_agases,
                         a.fec_revmec,
                         a.reg_nalcar,
                         a.num_tarpro,
                         a.num_tarope,
                         l.nom_califi,
                         a.num_poliza,
                         a.nom_asesoa,
                         a.fec_vigfin,
                         m.num_trayle,
                         g.abr_tercer,
                         n.abr_tercer,
                         h.abr_tercer
                    FROM ".BASE_DATOS.".tab_vehicu_vehicu a
                         LEFT JOIN ".BASE_DATOS.".tab_genera_marcas b 
                                ON a.cod_marcax = b.cod_marcax
                         LEFT JOIN ".BASE_DATOS.".tab_vehige_carroc e
                                ON a.cod_carroc = e.cod_carroc
                         LEFT JOIN ".BASE_DATOS.".tab_vehige_colore d 
                                ON a.cod_colorx = d.cod_colorx
                         LEFT JOIN ".BASE_DATOS.".tab_vehige_lineas c 
                                ON a.cod_marcax = c.cod_marcax AND a.cod_lineax = c.cod_lineax 
                         LEFT JOIN ".BASE_DATOS.".tab_vehige_config i 
                                ON a.num_config = i.num_config
                         LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer g 
                                ON a.cod_tenedo = g.cod_tercer
                         LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer h 
                                ON a.cod_conduc = h.cod_tercer 
                         LEFT JOIN ".BASE_DATOS.".tab_transp_vehicu j 
                                ON a.num_placax = j.num_placax
                         INNER JOIN ".BASE_DATOS.".tab_genera_tipveh k
                                ON a.cod_tipveh = k.cod_tipveh
                         LEFT JOIN ".BASE_DATOS.".tab_genera_califi l
                                ON a.cod_califi = l.cod_califi
                         LEFT JOIN (
                            SELECT MAX( num_noveda ), num_placax, num_trayle
                              FROM ".BASE_DATOS.".tab_trayle_placas
                             WHERE ind_actual = 'S'
                            GROUP BY num_placax
                         ) AS m
                               ON m.num_placax = a.num_placax
                         LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer n 
                                ON a.cod_propie = n.cod_tercer 
                   WHERE a.num_placax LIKE '%".$_REQUEST[placa]."%'";

        if($_REQUEST[transp])
            $query = $query." AND j.cod_transp = '".$_REQUEST[transp]."'";
        if($_REQUEST[por_fecha])
            $query = $query." AND a.fec_creaci  BETWEEN '".$_REQUEST[fecha1]."' AND '".$_REQUEST[fecha2]."'";
        if($_REQUEST[marcax])
            $query = $query." AND a.cod_marcax = '".$_REQUEST[marcax]."'";
        if($_REQUEST[carroc])
            $query = $query." AND a.cod_carroc = '".$_REQUEST[carroc]."'";
        if($_REQUEST[colorx])
            $query = $query." AND a.cod_colorx = '".$_REQUEST[colorx]."'";
        if($_REQUEST[config])
            $query = $query." AND a.num_config = '".$_REQUEST[config]."'";
        if($_REQUEST[por_modelo])
            $query = $query." AND a.ano_modelo >= ".$_REQUEST[mod1]." AND a.ano_modelo <= ".$_REQUEST[mod2];

        if($datos_usuario["cod_perfil"] == ""){
             //PARA EL FILTRO DE CONDUCTOR
             $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_usuari"]);
             if($filtro -> listar($this -> conexion))
             {
                     $datos_filtro = $filtro -> retornar();
                     $query = $query . " AND a.cod_conduc = '$datos_filtro[clv_filtro]' ";
             }
             //PARA EL FILTRO DE PROPIETARIO
             $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_usuari"]);
             if($filtro -> listar($this -> conexion))
             {
                     $datos_filtro = $filtro -> retornar();
                     $query = $query . " AND a.cod_propie = '$datos_filtro[clv_filtro]' ";
             }
             //PARA EL FILTRO DE POSEEDOR
             $filtro = new Aplica_Filtro_Usuari($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_usuari"]);
             if($filtro -> listar($this -> conexion))
             {
                     $datos_filtro = $filtro -> retornar();
                     $query = $query . " AND a.cod_tenedo = '$datos_filtro[clv_filtro]' ";
             }
        }
        else{
             //PARA EL FILTRO DE CONDUCTOR
             $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_CONDUC,$datos_usuario["cod_perfil"]);
             if($filtro -> listar($this -> conexion))
             {
                     $datos_filtro = $filtro -> retornar();
                     $query = $query . " AND a.cod_conduc = '$datos_filtro[clv_filtro]' ";
             }
             //PARA EL FILTRO DE PROPIETARIO
             $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_PROPIE,$datos_usuario["cod_perfil"]);
             if($filtro -> listar($this -> conexion))
             {
                     $datos_filtro = $filtro -> retornar();
                     $query = $query . " AND a.cod_propie = '$datos_filtro[clv_filtro]' ";
             }
             //PARA EL FILTRO DE POSEEDOR
             $filtro = new Aplica_Filtro_Perfil($this -> cod_aplica,COD_FILTRO_POSEED,$datos_usuario["cod_perfil"]);
             if($filtro -> listar($this -> conexion))
             {
                     $datos_filtro = $filtro -> retornar();
                     $query = $query . " AND a.cod_tenedo = '$datos_filtro[clv_filtro]' ";
             }
        }

        $query .= " GROUP BY 1 ORDER BY 1";

        $consulta = new Consulta($query, $this -> conexion);
        $matriz = $consulta -> ret_matriz('i');
        
        $_HTML  = "<table border='0' cellspacing='0' cellpadding='0' width='3500'>";
        $_HTML .= "<thead>";
        $_HTML .= "<tr>";
            $_HTML .= "<th colspan='21'  style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222)'>DATOS BASICOS</th>";
            $_HTML .= "<th colspan='3' style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242)'>SEGUROS</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222)'>SELECCION DE REMOLQUE</th>";
            $_HTML .= "<th colspan='3' style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242)'>DATOS PERSONAS</th>";
        $_HTML .= "</tr>";
        $_HTML .= "<tr>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>PLACA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 150px;'>MARCA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 150px;'>LINEA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 150px; '>MODELO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>REPOTENCIADO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 150px;'>COLOR</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 150px;'>TIPO DE VINCULACION</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 150px;'>CARROCERIA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 150px;'>No. MOTOR</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 150px;'>No. SERIE</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>PESO VACIO (TN)</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>CAPACIDAD (TN)</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>CONFIGURACION</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>VINCULADO A</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>FECHA DE VENCIMIENTO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>REVISION TECNOMECANICA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>FECHA DE VENCIMIENTO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>REG. NACIONAL DE CARGA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>LICENCIA DE TRANSITO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>TERJETA DE OPERACION</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>CALIFICACIÓN</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>SOAT</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>ASEGURADORA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>FECHA DE VENCIMIENTO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>REMOLQUE</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 350px;'>POSEEDOR</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 350px;'>PROPIETARIO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 350px;'>CONDUCTOR</th>";
        $_HTML .= "</tr>";
        $_HTML .= "</thead>";
        $_HTML .= "<tbody>";
        foreach ($matriz as $key => $row){
            $_HTML .= "<tr>";
            foreach (array_keys($row) as $title){
                if($title<=20 || $title==24)
                    $style= "style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align: $styles[$title];'";
                if($title>20 && $title<=23 || $title>24)
                    $style= "style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); text-align: $styles[$title];'";
                if(is_numeric($title)){
                    $_HTML .= "<td $style >".trim($row[$title])."&nbsp;</td>";
                }
            }
        }
        $_HTML .= "</tbody>";
        $_HTML .= "</table>";
        return $_HTML;
    }
    
    function ExcelConductores(){
        
        $query = "SELECT d.nom_tipdoc,
                         a.cod_tercer,
                         a.nom_tercer,
                         a.nom_apell1,
                         a.nom_apell2,
                         c.cod_grupsa,
                         IF(c.cod_tipsex = 1, 'MASCULINO', 'FEMENINO' ),
                         a.dir_domici,
                         l.nom_ciudad,
                         a.num_telef1,
                         a.num_telef2,
                         a.num_telmov,
                         e.nom_operad,
                         f.nom_califi,
                         c.num_licenc,
                         g.nom_catlic,
                         c.fec_venlic,
                         c.nom_epsxxx,
                         c.nom_arpxxx,
                         c.nom_pensio,
                         c.num_pasado,	
                         c.fec_venpas,
                         c.num_libtri,
                         c.fec_ventri,
                         c.nom_refper,
                         c.tel_refper,
                         k.nom_empre,
                         k.tel_empre,
                         k.num_viajes,
                         k.num_atigue,
                         k.nom_mercan,
                         i.abr_tercer,
                         a.abr_tercer
                    FROM ".BASE_DATOS.".tab_tercer_tercer a 
                        LEFT JOIN ".BASE_DATOS.".tab_transp_tercer b 
                               ON a.cod_tercer = b.cod_tercer
                        LEFT JOIN ".BASE_DATOS.".tab_genera_tipdoc d
                               ON d.cod_tipdoc = a.cod_tipdoc
                        LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad l
                                 ON l.cod_ciudad = a.cod_ciudad
                       INNER JOIN ".BASE_DATOS.".tab_tercer_conduc c 
                               ON a.cod_tercer = c.cod_tercer
                        LEFT JOIN ".BASE_DATOS.".tab_operad_operad e
                               ON c.cod_operad = e.cod_operad
                        LEFT JOIN ".BASE_DATOS.".tab_genera_califi f
                               ON c.cod_califi = f.cod_califi
                        LEFT JOIN ".BASE_DATOS.".tab_genera_catlic g
                               ON c.num_catlic = g.cod_catlic
                        LEFT JOIN ".BASE_DATOS.".tab_vehicu_vehicu h
                               ON h.cod_conduc = c.cod_tercer
                        LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer i
                               ON h.cod_tenedo = i.cod_tercer
                        LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer j
                               ON h.cod_propie = j.cod_tercer 
                        LEFT JOIN ".BASE_DATOS.".tab_conduc_refere k
                               ON k.cod_conduc = c.cod_tercer
                   WHERE 1 = 1  ";

        if($_REQUEST[transp])
            $query .= " AND b.cod_transp = '".$_REQUEST[transp]."'";
        if($_REQUEST[fil] == 1)
            $query = $query." AND a.cod_tercer = '".$_REQUEST[tercer]."'";
        else if($_REQUEST[fil] == 2)
            $query = $query." AND a.abr_tercer LIKE '%".$_REQUEST[tercer]."%'";
        else if($_REQUEST[fil] == 3)
            $query = $query." AND a.cod_estado = ".COD_ESTADO_ACTIVO."";
        else if($_REQUEST[fil] == 4)
            $query = $query." AND a.cod_estado = ".COD_ESTADO_INACTI."";
        else if($_REQUEST[fil] == 5)
            $query = $query." AND a.cod_estado = ".COD_ESTADO_PENDIE."";

        $query = $query." GROUP BY 2 ORDER BY 3";
       
        
        $consec = new Consulta($query, $this -> conexion);
        $matriz = $consec -> ret_matriz('i');
        
        $_HTML  = "<table border='0' cellspacing='0' cellpadding='0' width='4000'>";
        $_HTML .= "<thead>";
        $_HTML .= "<tr>";
            $_HTML .= "<th colspan='14'  style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222)'>DATOS BASICOS</th>";
            $_HTML .= "<th colspan='3' style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242)'>DATOS DE LICENCIA</th>";
            $_HTML .= "<th colspan='3' style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222)'>DATOS DE SEGURIDAD SOCIAL</th>";
            $_HTML .= "<th colspan='4' style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242)'>DATOS COMPLEMENTARIOS</th>";
            $_HTML .= "<th colspan='2' style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222)'>REFERENCIAS PERSONALES</th>";
            $_HTML .= "<th colspan='5' style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242)'>REFERENCIAS LABORALES</th>";
            $_HTML .= "<th colspan='3' style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222)'>OTRAS ACTIVIDADES</th>";
        $_HTML .= "</tr>";
        $_HTML .= "<tr>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 150px;'>TIPO DE DOCUMENTO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 150px;'>No. DOCUMENTO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 350px;'>NOMBRES</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 300px; '>APELLIDO 1</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 300px;'>APELLIDO 2</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>RH</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>GENERO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 550px;'>DIRECCION</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 150px;'>CIUDAD</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>TELEFONO 1</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>TELEFONO 2</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>CELULAR</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>OPERADOR</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>CALIFICACION</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>No. LICENCIA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>CATEGORIA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>FECHA DE VENCIMIENTO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>EPS</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>ARP</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>FONDO DE PENSIONES</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>PASADO JUDICIAL</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>FECHA DE VENCIMIENTO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>No. LIBRETA TRIPULACION</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>FECHA DE VENCIMIENTO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 250px;'>NOMBRE</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 100px;'>TELEFONO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>EMPRESA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>TELEFONO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>VIAJE</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>ANTIGUEDAD</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); width: 100px;'>MERCANCIA</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 350px;'>PROPIETARIO</th>";
            $_HTML .= "<th style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); width: 350px;'>POSEEDOR</th>";
        $_HTML .= "</tr>";
        $_HTML .= "</thead>";
        $_HTML .= "<tbody>";
        foreach ($matriz as $key => $row){
            $_HTML .= "<tr>";
            foreach (array_keys($row) as $title){
                if($title<=13 || ($title>16 && $title<=19) || ($title>23 && $title<=25) || $title>30 )
                    $style= "style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(235,241,222); text-align: $styles[$title];'";
                if($title>13 && $title<=16 || ($title>19 && $title<=23) || ($title>25 && $title<=30))
                    $style= "style='border: 0.5px solid #000; font-family: Arial; font-size: 12px; background: rgb(242,242,242); text-align: $styles[$title];'";
                if(is_numeric($title)){
                    $_HTML .= "<td $style >".trim($row[$title])."&nbsp;</td>";
                }
            }
        }
        $_HTML .= "</tbody>";
        $_HTML .= "</table>";
        return $_HTML;
        
    }
}

$ExportRecurs = new ExportRecurs();