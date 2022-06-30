<?php
    /****************************************************************************
    NOMBRE:   ajax_salida_despac
    FUNCION:  Retorna todos los datos necesarios para cargar el formulario y los
              Daatatables
    FECHA DE MODIFICACION: 04/05/2022
    CREADO POR: Ing. Oscar Bocanegra
    Creado 
    ****************************************************************************/
    
   /*  ini_set('error_reporting', E_ALL);
    ini_set("display_errors", 1); */
    

    class ajax_salida_despac
    {

        //Create necessary variables
        static private $conexion = null;
        static private $cod_aplica = null;
        static private $usuario = null;
        static private $dates = array();

        function __construct($co = null, $us = null, $ca = null)
        {

            //Include Connection class
            @include( "../lib/ajax.inc" );
            include_once('../lib/general/constantes.inc');

            //Assign values 
            self::$conexion = $AjaxConnection;
            self::$usuario = $us;
            self::$cod_aplica = $ca;

            //Switch request options
            switch($_REQUEST['opcion'])
            {
                case "1":
                    //verifica si hay datos en la transportadora
                    self::iniSelectDatosBD();
                    break;

                case "2":
                    self::iniSelectTipoServicio();
                    break; 

                case "3":
                    
                    self::dat_body_tbl_hora();
                    break;

                case "4":
                    self::datosnovedad();
                    break; 
                
                case "5":
                    self::dat_horas_vent_modal();
                    break;
                case "6":
                   
                    self::listtransp();
                    break;    
                
                
                    

                default:
                    echo "ninguna  opcion ";
                
            }
        }
/****************************************************************************
    NOMBRE:   iniSelectDatosBD
    FUNCION:  trae los datos para el multiselect los funcionarios
    FECHA DE MODIFICACION: 05/01/2022
    CREADO POR: Ing. Oscar Bocanegra
    Creado 
    ****************************************************************************/
    
        private function iniSelectDatosBD(){
            $sql = "SELECT transptipo.`cod_transp`, transp.`abr_tercer` 
            FROM ".BASE_DATOS.".`tab_transp_tipser` transptipo, ".BASE_DATOS.".`tab_tercer_tercer` transp 
            WHERE transptipo.`cod_transp`= transp.`cod_tercer`
            and transp.`cod_estado`=1
            group by transptipo.`cod_transp`
            ORDER BY `transp`.`abr_tercer` ASC";
                
            $query = new Consulta($sql, self::$conexion);
            $datos = $query -> ret_num_rows();    
            if ($datos > 0) {
                
                $datos = $query -> ret_matrix();
                foreach($datos as $resultadodatos)
                {
                    
                    $resultselect = $resultselect ."<option value='".$resultadodatos['cod_transp']."'>".$resultadodatos['abr_tercer']."</option>";
                
                }


                echo $resultselect;
            }else{
                echo '<option value="" selected>----</option>';
                //echo self::notDatos();
            }

            /*$datos = $query -> ret_matrix('a');
            $json = json_encode($datos);
            echo $json;
            */
        } 

        private function iniSelectTipoServicio(){
            $sql = "SELECT * FROM ".BASE_DATOS.".`tab_genera_tipser` WHERE `ind_estado`=1 ORDER BY `tab_genera_tipser`.`nom_tipser` ASC";
            
                
            $query = new Consulta($sql, self::$conexion);
            $datos = $query -> ret_num_rows();    
            if ($datos > 0) {
                
                $datos = $query -> ret_matrix();
                foreach($datos as $resultadodatos)
                {
                    
                    $resultselect = $resultselect ."<option value='".$resultadodatos['cod_tipser']."'>".$resultadodatos['nom_tipser']."</option>";
                
                }


                echo $resultselect;
            }else{
                echo '<option value="" selected>----</option>';
                //echo self::notDatos();
            }

            /*$datos = $query -> ret_matrix('a');
            $json = json_encode($datos);
            echo $json;
            */
        } 
        
 /****************************************************************************
    NOMBRE:   dat_body_tbl_prgtur
    FUNCION:  trae los datos encabezado para la tabla de programacion turnos en el datatable
    FECHA DE MODIFICACION: 06/01/2022
    CREADO POR: Ing. Oscar Bocanegra
    Creado 
    ****************************************************************************/ 

    private function dat_body_tbl_hora(){

        $Cod_transp=$_POST['Cod_transp'];
        $Cod_tiposerv=$_POST['Cod_tiposerv'];
        $fec_inicio=$_POST['fec_inicio'];
        $fec_finxxx=$_POST['fec_finxxx'];
        $hor_inicio=$_POST['hor_inicio'];
        $hor_finxxx=$_POST['hor_finxxx'];
        //$horaini='00:01';
        //$horafin='23:59';
       
       

        $fech1=$fec_inicio." ".$hor_inicio;
        $fech2=$fec_finxxx." ".$hor_finxxx;
        $newdat="";

        foreach($Cod_tiposerv as $newtiposerv){
            $conditiposerv .="( `cod_tipser`=".$newtiposerv." And `ind_estado`=1) or ";
        }
        $conditiposerv = rtrim($conditiposerv, " or ");
        $sqlonlytipserv="
                SELECT `cod_tipser`, `nom_tipser` FROM `tab_genera_tipser` WHERE ". $conditiposerv. " order by cod_tipser";

        $qonlytipserv = new Consulta($sqlonlytipserv, self::$conexion);    
        $datosonlytipserv = $qonlytipserv -> ret_matrix('a');

        
        //echo "tipos de Servicios ";
        //echo "<pre>";
        //print_r($datosonlytipserv);
        //echo "</pre> <br>";
        $condiini="
        SELECT a.`cod_transp`, max(a.`num_consec`) as nummayor,(Select c.cod_tipser from tab_transp_tipser c where a.`cod_transp`=c.`cod_transp` and c.`num_consec`= max(a.`num_consec`)) as tipo FROM `tab_transp_tipser` a, `tab_tercer_tercer` b 
        WHERE a.`cod_transp`=b.`cod_tercer`
        AND b.`cod_estado`=1
        
        group by a.`cod_transp`
        order by a.`cod_transp` ";
        
        $qcondiini = new Consulta($condiini, self::$conexion);    
        $datoscondiini = $qcondiini -> ret_matrix('a');

        //echo "Todas las Empresas activas consecutivo mayor y su tipo";
        //echo "<pre>";
        //print_r($datoscondiini);
        //echo "</pre>";

    $arrdatoscondiini=[];
        foreach($datoscondiini as $newdatoscondiini){
           foreach($datosonlytipserv as $newdatosonlytipserv){
               if($newdatoscondiini['tipo']==$newdatosonlytipserv['cod_tipser']){
                    array_push($arrdatoscondiini,["cod_tercer"=>$newdatoscondiini['cod_transp'],
            "cod_tipser"=>$newdatoscondiini['tipo'], "cod_tipser"=>$newdatoscondiini['tipo'], "nom_tipser"=>$newdatosonlytipserv['nom_tipser']]);
               }

           }        

        }
        //echo "Todas las Empresas Que Cumplen el Critrio de tipo de Servicio";
        

        if($Cod_transp==null){
            
            foreach($arrdatoscondiini as $newdatostipserv){
                
                        $newdat .= "desptercer.cod_tercer=". $newdatostipserv['cod_tercer'] . " 
                        and despac.fec_despac >= '". $fec_inicio."' 
                        and despac.fec_despac <= '". $fec_finxxx."' 
                        or " ;
                        

            }
            

        }else{
             

        //echo "<pre>";
        //    print_r($arrdatoscondiini);
        //echo "</pre><br>";

        //echo "-----------------------------";

        //echo "<br><pre>";
        //    print_r($Cod_transp);
        //echo "</pre><br>";

       
            $fechafinmanipulada=strtotime($fec_finxxx."+ 1 days");
            $fechafinmanipulada=date("Y-m-d",$fechafinmanipulada);
            foreach($Cod_transp as $datCod_transp){
                foreach($arrdatoscondiini as $newdatostipserv){
                    $validar=true;
                    if($newdatostipserv['cod_tercer']==$datCod_transp){
                        $newdat .= "desptercer.cod_tercer= ". $datCod_transp . " 
                        and despac.fec_despac >= '". $fec_inicio."' 
                        and despac.fec_despac <= '". $fechafinmanipulada."' 
                        or " ;
                    break ;    
                    }
                }
            }

        }

        
        $newdat = rtrim($newdat, "or ");
        
        $sql="

        SELECT desptercer.cod_tercer, desptercer.abr_tercer, DATE_FORMAT(despac.fec_despac, '%d-%m-%Y') AS fec_despac2

            FROM `tab_despac_despac` despac 
            INNER JOIN `tab_despac_vehige` despveh 
            on despac.num_despac= despveh.num_despac 
            INNER JOIN `tab_tercer_tercer` desptercer 
            on despveh.`cod_transp`= desptercer.cod_tercer 
            INNER JOIN `tab_transp_tipser` tipser
            on despveh.`cod_transp` = tipser.cod_transp
            INNER JOIN  `tab_genera_tipser` tipsergen 
            on tipser.cod_tipser= tipsergen.cod_tipser
            where $newdat
            
            group by desptercer.cod_tercer, DATE_FORMAT(despac.fec_despac, '%d-%m-%Y')
            ORDER BY despac.fec_despac ASC";
        // echo $sql;
            
        $query = new Consulta($sql, self::$conexion);
        $datos = $query -> ret_num_rows();
        
        
        
        if ($datos > 0) {
            $datos = $query -> ret_matrix('a');
            $datosnew=$datos;
           
            //echo "Todas las Empresas Que Cumplen el Critrio de tipo de Servicio";
            //echo "<pre>";
            //    print_r($datosnew);
            //echo "</pre>";
          

          $ini=$hor_inicio;
          $fin=$hor_finxxx;
          $conpivot="";
          
            for ($i=$ini;$i<=$fin;$i++){
                $conpivot .=" COUNT(CASE WHEN DATE_FORMAT(despac.fec_despac, '%H')=".$i." THEN DATE_FORMAT(despac.fec_despac, '%H') END) '".$i."' ,";
            }
            $conpivot = rtrim($conpivot, ",");

            $arraytransport=array(); 
            $arrayrowhead=array();
            $arrayidtbl=array();
            $vartabla=0;
            $vartbl=0;
            $fechdepur="";
            foreach ($datos as $newdatdatos) {
                if($fechdepur != $newdatdatos['fec_despac2']){

                    $tablasfila="<table style='width: 100%;'><tr style='background-color:#89d889'><td>";
                    $tablasfila .="Dia ".$newdatdatos['fec_despac2']. " ". $hor_inicio." a ". $hor_finxxx;
                    $tablasfila .="</td></tr></table><br>";
                    array_push($arrayrowhead, $tablasfila);
                    $fechdepur=$newdatdatos['fec_despac2'];
                    $newdat2="";
                    foreach($datosnew as $datCod_transp2){
                    $newdat2 .= "desptercer.cod_tercer=".$datCod_transp2['cod_tercer']."  
                                AND DATE_FORMAT(despac.fec_despac, '%d-%m-%Y') ='".$newdatdatos['fec_despac2']."' or " ;
                    }
                    $newdat2 = rtrim($newdat2, "or ");
                    $sql2="
                        SELECT despvehige.cod_transp, desptercer.abr_tercer,despac.fec_despac,$conpivot
                        FROM `tab_despac_despac` despac 
                        INNER JOIN `tab_despac_vehige` despvehige 
                        ON despac.num_despac = despvehige.num_despac 
                        INNER JOIN `tab_tercer_tercer` desptercer 
                        ON despvehige.cod_transp = desptercer.cod_tercer 
                        where  $newdat2
                        group by despvehige.cod_transp
                        ORDER BY `desptercer`.`abr_tercer` ASC, despac.fec_despac
                        ";
                    //echo $sql2;
                    $query2 = new Consulta($sql2, self::$conexion);
                    $datos2 = $query2 -> ret_matrix('a');
                    //echo "<pre>";
                    //    print_r($$datos2);
                    //echo "</pre>";

                    $vartbl++;

                    $initabla =' <table class="table table-striped table-bordered elliminartbl" id="table_data'.$vartbl.'" name="table_data'.$vartbl.'" >';
                    $fintabla='</table>';
                    $htmlhead ='<thead><tr>';
                    $htmlhead .='<th>No</th>';
                    $htmlhead .='<th>Nit</th>';
                    $htmlhead .='<th>Empresa</th>';
                    $htmlhead .='<th>Tipo de Servicio</th>';
                    for ($i=$ini;$i<=$fin;$i++){
                        $htmlhead .='<th>'.$i.'</th>';
                    }
                    $htmlhead .='<th>Total</th>';
                    $htmlhead .='</tr></thead>';
                    $inibody='<tbody>';
                    $cuerpobody="";
                    $contdor=1;
                    foreach($datos2 as $transp2){
                        $cuerpobody .='<tr>';
                        $cuerpobody .='<td>'. $contdor++.'</td>';
                        $cuerpobody .='<td>'.$transp2['cod_transp'].'</td>';
                        $cuerpobody .='<td>'.$transp2['abr_tercer'].'</td>';
                         $sql3="SELECT nomtipserv.`nom_tipser` 
                            FROM `tab_genera_tipser` nomtipserv, `tab_transp_tipser` tipserv, `tab_despac_vehige` transpvehi
                            where nomtipserv.`cod_tipser`= tipserv.`cod_tipser`
                            and transpvehi.cod_transp=tipserv.cod_transp
                            and transpvehi.cod_transp=".$transp2['cod_transp']."
                            ORDER BY `tipserv`.`num_consec`  DESC
                            limit 1";
                            $query3 = new Consulta($sql3, self::$conexion);
                            $datos3 = $query3 -> ret_matrix('a');
                            foreach($datos3 as $tiposervicio){
                                $tiposerv=$tiposervicio['nom_tipser']; 
                            }
                        $cuerpobody .='<td>'.$tiposerv.'</td>';
                        $total=0;
                        $transphor=$transp2['cod_transp'];
                        $transpfecha= $newdatdatos['fec_despac2'];
                        
                        for ($i=$ini;$i<=$fin;$i++){
                            if($transp2[$i]>0){
                                $varcompeto=$transphor.",'".$transpfecha."',".$i;
                                $cuerpobody .='<td onclick="dathortransp('.$varcompeto.')"><a href="#">'.$transp2[$i].'</a></td>';
                            }else{
                                $cuerpobody .='<td>'.$transp2[$i].'</td>';
                            }
                            
                            $total = $total + $transp2[$i];
                        }
                        $cuerpobody .='<td>'.$total.'</td>';
                        $cuerpobody .='</tr>';
                    }
                    $finbody='</tbody>'; 
                    
                    $tblcompleta=$initabla.$htmlhead.$inibody.$cuerpobody.$finbody.$fintabla ;
                    
                    //echo $tblcompleta,"$vartbl <br><br>";

                    array_push($arraytransport, $tblcompleta); 
                    $nametableid="table_data".$vartbl;
                    array_push($arrayidtbl, $nametableid);
                }
            }


            //echo "<pre>";
            //    print_r($arrayrowhead);
            //echo "</pre>";
            $arrayresult=["titulostbl"=>$arrayrowhead, "contentbl"=>$arraytransport, "idtable"=>$arrayidtbl];
            
            echo json_encode($arrayresult);
            //echo "<table><tr><td>aqui</td></tr></table>";
            
            
        }else{
            $htmlhead ='';
            $htmlfila="";
            $htmlhead ='<table><thead><tr>';
            $htmlhead .='<th>No hay datossss</th>';
            $htmlhead .='</tr></thead>';
            $htmlhead .='<tbody>';
            $htmlhead .='</tr><td>No hay datosss</td></tr>';
            $htmlhead .='</tbody></table>' ;              
            
            echo $htmlhead;
        }








    }





    private function dat_horas_vent_modal(){

        $vartransp=$_POST['vartransp'];
        $varfech=$_POST['varfech'];
        $varhor=$_POST['varhor'];


        



        $sql = "SELECT DATE_FORMAT(a.fec_despac, '%d-%m-%Y') fech, a.fec_despac, a.num_despac, a.cod_manifi,a.usr_creaci, 
        b.cod_conduc, IF( b.nom_conduc IS NOT NULL, b.nom_conduc, c.abr_tercer) AS abr_tercer,b.num_placax, UPPER( e.abr_tercer ) AS nom_transp, e.num_telmov AS telmov,
        CONCAT(f.abr_ciudad, ' (', UPPER(LEFT(j.abr_paisxx, 3)), ')') AS ciu_origen,
        CONCAT(f.abr_ciudad, ' (', UPPER(LEFT(k.abr_paisxx, 3)), ')') AS ciu_destin
        
        FROM `tab_despac_despac` a
        INNER JOIN  tab_despac_vehige b
        on b.num_despac = a.num_despac
        
        INNER JOIN tab_tercer_tercer c
        on c.cod_tercer = b.cod_transp
        
        INNER JOIN tab_tercer_conduc d
        on d.cod_tercer = b.cod_conduc
        
        INNER JOIN tab_tercer_tercer e
        on e.cod_tercer = d.cod_tercer
        
        
        INNER JOIN tab_genera_ciudad f 
        ON a.cod_ciuori = F.cod_ciudad 
        AND a.cod_depori = F.cod_depart 
        AND a.cod_paiori = F.cod_paisxx 
        
        INNER JOIN tab_genera_ciudad g
        ON a.cod_ciudes = g.cod_ciudad
        AND a.cod_depdes = g.cod_depart
        AND a.cod_paides = g.cod_paisxx
        
        INNER JOIN tab_genera_depart h 
        ON a.cod_depori = h.cod_depart 
        AND a.cod_paiori = h.cod_paisxx 
        
        INNER JOIN tab_genera_depart i 
        ON a.cod_depdes = i.cod_depart 
        AND a.cod_paides = i.cod_paisxx 
        
        INNER JOIN tab_genera_paises j 
        on a.cod_paiori = j.cod_paisxx 
        
        INNER JOIN tab_genera_paises k 
        on a.cod_paides = k.cod_paisxx 
        
       

        WHERE DATE_FORMAT(a.fec_despac, '%d-%m-%Y') >= '".$varfech."' AND
        DATE_FORMAT(a.fec_despac, '%d-%m-%Y') <= '".$varfech."' AND
        DATE_FORMAT(a.fec_despac, '%H') = '".$varhor."' AND
        C.cod_tercer=$vartransp";

$query = new Consulta($sql, self::$conexion);
$datos = $query -> ret_matrix('a');
$tblmodal=' <table class="table table-striped table-bordered" id="table_detalle" name="table_detalle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Despacho</th>
                        <th>Manifiesto</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Transportadora</th>
                        <th>Placa</th>
                        <th>No Cedula</th>
                        <th>Nombre Conductor</th>
                        <th>Celular</th>
                        <th>Generador</th>
                        <th>Fecha y Hora se</th>
                        <th>usuario</th>
                    </tr>
                </thead>
                <tbody>';
    

$j=1;
$vartransportadora="";
$filasobody="";
foreach ($datos as $datosvent) {

    $mysql4=" SELECT despac.`num_despac`, despac.`cod_client`, tercer.`abr_tercer` 
        FROM `tab_despac_despac` despac, `tab_tercer_tercer` tercer 
        WHERE despac.`cod_client`= tercer.`cod_tercer` 
        AND despac.`num_despac` =".$datosvent['num_despac'];
    $query4 = new Consulta($mysql4, self::$conexion);
    $datos4 = $query4 -> ret_num_rows();
    $generador="";    
    if ($datos4 > 0) {
        $datos4 = $query4 -> ret_matrix('a');
        foreach ($datos4 as $datos4vent) {
            $generador=$datos4vent['abr_tercer'];
        }
        
    }else{
        $generador="No aplica";
    }
    $filasobody .='<tr>';
    $filasobody .='<td>'.$j.'</td>';
    $filasobody .='<td>'.$datosvent['num_despac'].'</td>';
    $filasobody .='<td>'.$datosvent['cod_manifi'].'</td>';
    $filasobody .='<td>'.$datosvent['ciu_origen'].'</td>';
    $filasobody .='<td>'.$datosvent['ciu_destin'].'</td>';        
    $filasobody .='<td>'.$datosvent['abr_tercer'].'</td>';    
    $filasobody .='<td>'.$datosvent['num_placax'].'</td>'; 
    $filasobody .='<td>'.$datosvent['cod_conduc'].'</td>'; 
    $filasobody .='<td>'.$datosvent['nom_transp'].'</td>';        
    $filasobody .='<td>'.$datosvent['telmov'].'</td>'; 
    $filasobody .='<td>'.$generador.'</td>';        
    $filasobody .='<td>'.$datosvent['fec_despac'].'</td>';        
    $filasobody .='<td>'.$datosvent['usr_creaci'].'</td>';         
    
    $filasobody .='</tr>';
    $vartransportadora=$datosvent['abr_tercer'];
    $j++;
}

$tblmodal=$tblmodal.$filasobody.'</tbody></table>';


$arrayresultvent=["conttbody"=>$tblmodal, "numreg"=>$j-1, "transportadora"=>$vartransportadora];
echo json_encode($arrayresultvent);

}

        
/****************************************************************************
    NOMBRE:   datosnovedad
    FUNCION:  trae los datos para la tabla de tiempos en el datatable
    FECHA DE MODIFICACION: 05/01/2022
    CREADO POR: Ing. Oscar Bocanegra
    Creado 
    ****************************************************************************/ 
        private function datosnovedad(){
            
            $Cod_funcionario=$_POST['Cod_funcionario'];
            $fec_inicio=$_POST['fec_inicio'];
            $fec_finxxx=$_POST['fec_finxxx'];
            $newdat="";


            if (empty($$Cod_funcionario)){
                $newdat .= " tbluser.`cod_usuari`= tblmov.`cod_usuari` 
                            and tblnov.`cod_novtur`= tblmov.`cod_novtur`
                            And obs_protur != ''
                            and tblmov.`fec_inicia` BETWEEN '$fec_inicio' AND '$fec_finxxx'
                            or";

            }else{

                foreach($Cod_funcionario as $datcod_funcionario){
                    $newdat .= " tbluser.`cod_usuari`= tblmov.`cod_usuari` 
                                and tblnov.`cod_novtur`= tblmov.`cod_novtur`
                                And obs_protur != ''
                                and tblmov.`fec_inicia` BETWEEN '$fec_inicio' AND '$fec_finxxx'
                                and tbluser.`cod_consec`=$datcod_funcionario
                                or";


                    //$newdat .= " `cod_tercer` LIKE ".$datcod_transp." AND date(`fec_modifi`) BETWEEN '$fec_inicio' and '$fec_finxxx' or" ;
                }
            }
            
            $newdat = rtrim($newdat, "or");

            $sql="SELECT '' as contador, tbluser.`nom_usuari`, tblnov.`nom_novtur`, tblmov.`fec_inicia`, tblmov.`hor_inicia`, 
                tblmov.`hor_finalx`, tblmov.`obs_protur`, tblmov.`usr_creaci`,  DATE_FORMAT(tblmov.`fec_creaci`,'%d/%m/%Y %h:%i %p') as fecha 
                FROM `tab_progra_turnos` tblmov, `tab_genera_usuari` tbluser, `tab_genera_novtur` tblnov
                WHERE $newdat
                ORDER BY `tblmov`.`fec_inicia`  ASC";
                        
            $query = new Consulta($sql, self::$conexion);

            $datos = $query -> ret_num_rows();
            if ($datos > 0) {
                $datos = $query -> ret_matrix();
                //print_r($datos);
                echo json_encode(self::cleanArray($datos));
            }else{
                $datos=array("contador"=>"","nom_usuari"=>"Vacio","nom_novtur"=>"Vacio","fec_inicia"=>"Vacio","hor_inicia"=>"Vacio","hor_finalx"=>"Vacio"
                ,"obs_protur"=>"Vacio","usr_creaci"=>"Vacio","fec_creaci"=>"Vacio" );
                echo json_encode(self::cleanArray($datos));
                
                //echo self::notDatos();
            }
            
        }

        private function notDatos(){
            $htmlencabezado ='
                <div class="alert alert-danger" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>
                            No hay horarios ni tiempos que mostrar... 
                </div>
            
            ';
            
            return $htmlencabezado;
        
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
                    $arrayReturn[$convert($key)] = self::cleanArray($value);
                }else{
                    //Clean value
                    $arrayReturn[$convert($key)] = $convert($value);
                }
            }
            //Return array
            return $arrayReturn;
        }

    
    
        private function partewhere(){
            $cod_transp=$_POST['cod_transp'];
            $fec_inicio=$_POST['fec_inicio'];
            $fec_finxxx=$_POST['fec_finxxx'];
            $newdat="";
            foreach($cod_transp as $datcod_transp){
                $newdat .= " `cod_tercer` LIKE ".$datcod_transp." AND date(`fec_modifi`) BETWEEN '$fec_inicio' and '$fec_finxxx' or" ;
            }
            $newdat = rtrim($newdat, "or");
            return $newdat;
        }
    
        private function listtransp(){
            $Cod_tiposerv=$_POST['Cod_tiposerv'];

            $sql = "SELECT a.`cod_transp`, b.`abr_tercer`, max(a.`num_consec`) as nummayor,(Select c.cod_tipser from tab_transp_tipser c where a.`cod_transp`=c.`cod_transp` and c.`num_consec`= max(a.`num_consec`)) as tipo 
            FROM `tab_transp_tipser` a, `tab_tercer_tercer` b 
                    WHERE a.`cod_transp`=b.`cod_tercer`
                    AND b.`cod_estado`=1
                    group by a.`cod_transp`
                    order by b.`abr_tercer`";
                
            $query = new Consulta($sql, self::$conexion);
            $datos = $query -> ret_num_rows();   
            
            if ($datos > 0) {
                
                $datos = $query -> ret_matrix();
                $resultselectact="";
                foreach($datos as $resultadodatos)
                {
                    foreach($Cod_tiposerv as $newtiposerv){

                        if($resultadodatos['tipo']==$newtiposerv){
                           
                            $resultselectact = $resultselectact ."<option value='".$resultadodatos['cod_transp']."'>".$resultadodatos['abr_tercer']."</option>";
                        }

                    }
                    
                    
                
                }
            }


                echo $resultselectact;

            


        }

           
    
    }

    new ajax_salida_despac();

    /* $sel=new ajax_progra_turnos();
    $matrizuser=$sel->iniSelectDatosBD;
    */
?>