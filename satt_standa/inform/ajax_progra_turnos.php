<?php
    /****************************************************************************
    NOMBRE:   ajax_progra_turnos
    FUNCION:  Retorna todos los datos necesarios para cargar el formulario y los
              Daatatables
    FECHA DE MODIFICACION: 05/01/2022
    CREADO POR: Ing. Oscar Bocanegra
    Creado 
    ****************************************************************************/
    
   /*  ini_set('error_reporting', E_ALL);
    ini_set("display_errors", 1); */
    

    class ajax_progra_turnos
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
                    
                    self::dat_body_tbl_prgtur();
                    break;

                case "3":
                    self::datosnovedad();
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
            $sql = "SELECT a.cod_consec, a.cod_usuari, a.nom_usuari
            FROM ".BASE_DATOS.".tab_genera_usuari a
           WHERE a.ind_estado = '1'
           and a.`cod_perfil`=7
           or   a.ind_estado = '1'
           and a.`cod_perfil`=8
          ORDER BY a.nom_usuari ASC";
                
            $query = new Consulta($sql, self::$conexion);
            $datos = $query -> ret_num_rows();    
            if ($datos > 0) {
                
                $datos = $query -> ret_matrix();
                foreach($datos as $resultadodatos)
                {
                    
                    $resultselect = $resultselect ."<option value='".$resultadodatos['cod_consec']."'>".$resultadodatos['nom_usuari']."</option>";
                
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

    private function dat_body_tbl_prgtur(){

        $Cod_funcionario=$_POST['Cod_funcionario'];
        $fec_inicio=$_POST['fec_inicio'];
        $fec_finxxx=$_POST['fec_finxxx'];

        //Capturar los colores
        $sql="SELECT nom_horari,cod_colorx FROM tab_config_horari";
        $query = new Consulta($sql, self::$conexion);
        $datcoloreshex = $query -> ret_num_rows();
        if($datcoloreshex>0){
            $datcoloreshex = $query -> ret_matrix('a');
        }
        
       
        $newdat="";
        if (empty($$Cod_funcionario)){
            $newdat .= " u.cod_usuari = t.cod_usuari
            AND j.cod_horari= t.cod_horari
            And t.obs_protur = ''
            and t.fec_inicia BETWEEN '$fec_inicio' and '$fec_finxxx'
            or" ;
        }else{
            foreach($Cod_funcionario as $datcod_funcionario){
                $newdat .= " u.cod_usuari = t.cod_usuari
                AND j.cod_horari= t.cod_horari
                and u.cod_consec = $datcod_funcionario
                And t.obs_protur = ''
                and t.fec_inicia BETWEEN '$fec_inicio' and '$fec_finxxx'
                or" ;
            }
        }
        
        $newdat = rtrim($newdat, "or");
        
        $sql="SELECT '' as cont, u.cod_consec, u.nom_usuari, j.nom_horari , j.cod_colorx ,t.fec_inicia 
        FROM tab_progra_turnos as t, tab_genera_usuari as u, tab_config_horari as j
        WHERE $newdat ORDER BY u.nom_usuari ASC, t.fec_inicia asc";

        $query = new Consulta($sql, self::$conexion);
        $datos = $query -> ret_num_rows();
        if ($datos > 0) {
            $datos = $query -> ret_matrix('a');
           /*
            echo "<pre>";
                print_r($datos);
            echo "</pre>";
           */
            $arraypersonas=array(); 
            $arrayrowhead=array();
            foreach ($datos as $newdatdatos) {
                foreach($newdatdatos as $contdatos=>$valores){
                    if($contdatos=='cod_consec')
                        array_push($arraypersonas, $valores);
                    if($contdatos=='fec_inicia')
                    array_push($arrayrowhead, $valores); 
                }
            }
            
            $arraypersonas = array_unique($arraypersonas);
            $arrayrowhead = array_unique($arrayrowhead);
            /*
            echo "<pre>";
                print_r($arraypersonas);
            echo "</pre>";
            
            echo "<pre>";
                print_r($arrayrowhead);
            echo "</pre>";
            */
            //Recorrer x cada funcionario e ir llenando sus datos, aqui ya lleno 
                       
            sort($arrayrowhead);
            $arrayendbody=array();
            
            $arraytemp=array();
            
            $totnumarray=count($datos);

            $totnumarrayhead=count($arrayrowhead);
            foreach($arraypersonas as $datarraypersonas){
                $arrayrowheadtemp=array();
                $arrakeyafect=array();
                $arrayrowheadtemp=$arrayrowhead;
                foreach($datos as $keyprb => $valueprueba){
                    if($valueprueba['cod_consec']==$datarraypersonas){
                        foreach ($arrayrowheadtemp as $keyheaddatfin => $valueheadfin){
                           if($valueprueba['fec_inicia']==$valueheadfin){
                            $arrayrowheadtemp[$keyheaddatfin]=$valueprueba['nom_horari'];
                            $arrakeyafect[$keyheaddatfin]=$keyheaddatfin;
                           }
                        }
                        $resultado=[$valueprueba['cont'],$valueprueba['nom_usuari']];
                    }
                }
                
                foreach($arrayrowheadtemp as $keyarrayrowheadtemp=>$valarrayrowheadtemp){
                    if(array_key_exists($keyarrayrowheadtemp, $arrakeyafect)){}else{
                        $arrayrowheadtemp[$keyarrayrowheadtemp]=""; 
                    }
                }

                $resultado1 = array_merge($resultado, $arrayrowheadtemp);
                array_push($arrayendbody, $resultado1);
                $resultado=array();

            }
            /*
            echo "<pre>";
                print_r($arrayendbody);
            echo "</pre>";
            */
            $dias = array('', 'Lunes','Martes','Miercoles','Jueves','Viernes','Sabado', 'Domingo');

            $htmlhead ='';
            $htmlfila="";
            $htmlhead ='<thead><tr>';
            
            $htmlhead .='<th></th>';
            $htmlhead .='<th></th>';
            foreach($arrayrowhead as $dibfila1head){
                $diamostrar=$dias[date('N', strtotime($dibfila1head))];
                $htmlhead .="<th>$diamostrar</th>";
                
            }
            $htmlhead .='</tr><tr>';
            $htmlhead .='<th>No</th>';
            $htmlhead .='<th>Funcionario</th>';
            foreach($arrayrowhead as $dibarrayrowhead){
                $htmlhead .="<th>".$dibarrayrowhead."</th>";
            }

                
              $htmlhead .='</tr></thead>';
              $htmlhead .='<tbody>';

              foreach($arrayendbody as $dibarrayendbody){
                $htmlhead .="<tr>";
                  foreach($dibarrayendbody as $vrdibarrayendbody){
                      $caso=2;

                      foreach($datcoloreshex as $vardatoscolorshex){
                        if($vardatoscolorshex['nom_horari']==$vrdibarrayendbody){
                            if($vardatoscolorshex['cod_colorx']=="#000000"){
                                $colorfuente="#FCFCFC";
                            }else{
                                $colorfuente="#000000";
                            }
                           $coloraplicar= $vardatoscolorshex['cod_colorx'];
                           $caso=1;
                        break;
                       }    
                   }
                          
                   switch($caso){
                        case 1:
                            $htmlhead .="<td style='color: ".$colorfuente."; background-color: ".$coloraplicar.";'>".$vrdibarrayendbody."</td>";
                            break;
                        case 2:
                            $htmlhead .="<td>".$vrdibarrayendbody."</td>";
                            break;
                   }
                    
                  }
                $htmlhead .="</tr>";
                }
                
                $htmlhead .='</tbody>' ;              
            
              echo $htmlhead;
        }else{
            $htmlhead ='';
            $htmlfila="";
            $htmlhead ='<thead><tr>';
            $htmlhead .='<th>No hay datos</th>';
            $htmlhead .='</tr></thead>';
            $htmlhead .='<tbody>';
            $htmlhead .='</tr><td>No hay datos</td></tr>';
            $htmlhead .='</tbody>' ;              
            
            echo $htmlhead;
        }

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
    
    
    }

    new ajax_progra_turnos();

    /* $sel=new ajax_progra_turnos();
    $matrizuser=$sel->iniSelectDatosBD;
    */
?>