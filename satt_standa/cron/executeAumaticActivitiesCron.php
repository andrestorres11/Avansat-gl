<?php

    $padre_superi = dirname(dirname(__DIR__)).'/';
    $padre_standa = dirname(__DIR__).'/';

    include ($padre_superi."satt_faro/constantes.inc");
    include ($padre_standa."lib/general/constantes.inc");
    include ($padre_standa."lib/general/conexion_lib.inc");
    include ($padre_standa."lib/general/functions.inc");

    $conexion = new Conexion(HOST,USUARIO, CLAVE, BASE_DATOS);

    //Extrae dia de la semana
    $hoy = date('w');
    $dias_semana = array('D', 'L', 'M', 'X', 'J', 'V', 'S');
    $dia = $dias_semana[$hoy];


    $sqltipserv = "SELECT gentipserv.`cod_tipser`, gentipserv.`nom_tipser` 
    FROM `tab_genera_tipser` gentipserv
    where gentipserv.`ind_estado`=1
    And gentipserv.`nom_tipser` LIKE '%MA%'";
    $conssql = new Consulta($sqltipserv, $conexion);
    $tipser = $conssql->ret_matriz('a');

    //Formatea array para validaciones
    $tip_servicio = array();
    foreach($tipser as $tip){
        array_push($tip_servicio, $tip['cod_tipser']);
    }


    $query = 'SELECT a.cod_tercer, a.abr_tercer,
                (SELECT c.cod_tipser FROM '.BASE_DATOS.'.tab_transp_tipser c
                    WHERE c.cod_transp = a.cod_tercer GROUP BY c.cod_transp ORDER BY c.num_consec DESC LIMIT 1) as "cod_tipser"
                FROM '.BASE_DATOS.'.tab_tercer_tercer a 
                INNER JOIN '.BASE_DATOS.'.tab_tercer_emptra b ON a.cod_tercer = b.cod_tercer
            WHERE a.cod_estado = 1';
    $transportadoras = new Consulta($query, $conexion);
    $transportadoras = $transportadoras->ret_matriz('a');
    foreach($transportadoras as $transportadora){
        if(in_array($transportadora['cod_tipser'], $tip_servicio)){
            $queryInt = 'SELECT * FROM tab_config_horlab WHERE cod_tercer LIKE "'.$transportadora['cod_tercer'].'" AND com_diasxx LIKE "%'.$dia.'%" AND hor_ingres > "00:00:00"';
            $horario = new Consulta($queryInt, $conexion);
            $horario = $horario->ret_matriz('a');
            if(count($horario)>0){
                $sqlinsprn = 'INSERT INTO '.BASE_DATOS.'.tab_genera_actdes (tit_activi, tip_actdes, des_activi, cod_perfil, ins_status, usr_creaci, fec_creaci, tip_user) 
                VALUES ("9","3","Asignación empresa de transporte '.$transportadora['abr_tercer'].'","8","1","cron",NOW(),"Automatico")';
                $consultainsprn = new Consulta($sqlinsprn, $conexion);
                $lastInsetID =  mysql_insert_id();
                
                $fec_inicia = date('Y-m-d');
                $hor_inicia = $horario[0]['hor_ingres'];
                $hor_finalx = date('H:i:s',strtotime('+15 minute', strtotime($hor_inicia)));
                $sqlinssec="INSERT INTO ".BASE_DATOS.".tab_actdes_frecue(cod_actdes, con_frecue, fec_inicia, hor_inicia, fec_finalx, hor_finalx, cod_period, usr_creaci, fec_creaci)
                VALUES ('$lastInsetID', 'L|M|X|J|V|S|D', '$fec_inicia', '$hor_inicia', '$fec_inicia', '$hor_finalx','2', 'cron', NOW()) ";
                $consultainssec = new Consulta($sqlinssec, $conexion);
            }
        }
    }

