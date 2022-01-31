<?php
    /****************************************************************************
    NOMBRE:   AjaxActiviDesarr
    FUNCION:  Retorna todos los datos necesarios para construir la formularios e
              informacion de actividades a desarrollar
    FECHA DE MODIFICACION: 07/10/2021
    CREADO POR: Ing. Carlos Nieto
    MODIFICADO 
    ****************************************************************************/
    
    /* ini_set('error_reporting', E_ALL);
    ini_set("display_errors", 1); */
    

    class AjaxActiviDesarr
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
                    //crea los campos adicionales de formulario operativo
                    self::operativeForm();
                break;
                case "2":
                    //crea la tabla operativa
                    self::operativeTable();
                break;
                case "3":
                  //crea la tabla administrativa
                  self::administrativeTable();
                break;
                case "4":
                  //Obtiene la informacion pendiente por realizar
                  self::getPendingData();
                break;
                case "5":
                  //obtiene la informacion de tareas ya realizadas
                  self::getDoneData();
                break;
                case "6":
                  //llama el html del modal de historial
                  self::historyModal();
                break;
                case "7":
                  //obtiene la informacion del  historico
                  self::getHistoricalData();
                break;
                case "8":
                  //llama el formulario html de agregar historial
                  self::historyModalForm();
                break;
                case "9":
                  //inserta informacion de historial
                  self::historyInsertForm();
                break;
                case "10":
                  //html llama los campos select junto con su respectiva info 
                  self::dinamycSelects();
                break;
                case "11":
                  //segun la opcion de frecuencia que se elija llama el formulario html correspondiente
                  self::callFormOptionFrecuency();
                break;
                case "12":
                  //cuando la frecuencia no es de tipo 'no se repite' trae el los campos de fecha y hora completos
                  self::SelectFullDateTime();
                break;
                case "13":
                  //cuando la frecuencia es de tipo 'no se repite' trae el los campos de fecha y hora sin la hora fina
                  self::SelectShortDateTime();
                break;
                case "14":
                  //guarda los datos de la nueva actividad en base de datos
                  self::InsertNewActivity();
                break;
                case "15":
                  //select de titulo
                  self::selectTile();
                break;
            }
        }

      
      function operativeForm(){
        $getNovedaData = $this->getNovedaData();
        $getCompanyData = $this->getCompanyData();

            $html='
              <div class="row  mt-3">
                <div class="col-6">
                  <div class="form-floating">
                    <h6>Empresa de Transporte* :</h6>
                    <select class="custom-select" id="company" name="company">
                      <option value="0" selected>Seleccione...</option>
                      '.$getCompanyData.'
                    </select> 
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-floating">
                    <h6>Placa*(s) :</h6>
                    <input class="form-control form-control" type="text" placeholder="Escriba aqui...." id="palca" name="palca" required">
                  </div>
                </div>
                <div class="col-6 mt-3">
                  <h6>Seleccione la Novedad: </h6>        
                  <select class="custom-select" id="novedad" name="novedad">
                    <option value="0" selected>Seleccione...</option>
                    '.$getNovedaData.'
                  </select> 
                </div>
              </div>
          ';
          echo $html;
      }

      function operativeTable(){
        $html='<table class="table table-striped table-bordered table-sm" cellspacing="0" width="100%" id="tabla_inf_general">
                  <thead>
                      <tr>
                          <th colspan="15" style="background-color:#dff0d8; color: #000"><center id="text_general_fec">ACTIVIDADES PENDIENTES POR GESTIONAR DEL <center></th> 
                      </tr>
                      <tr>
                          <th>ID Actividad</th> 
                          <th>Tiempo</th>
                          <th>Nombre Actividad</th>
                          <th>Descripción Actividad</th>
                          <th>Hora de Ejecucion</th>
                          <th>Perfil(es)</th>
                          <th>Usuario(s)</th>
                          <th>Empresa de T</th>
                          <th>Placa</th>
                          <th>Novedad</th>
                          <th>Fecha y hora Inicial</th>
                          <th>Fecha y hora Final</th>
                          <th>Frecuencia</th>
                          <th>Fecha y Hora de Cumplimiento</th>
                          <th>Fecha a Ejecutar</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr id="resultado_info_general">
                      </tr>
                  </tbody>
              </table>
  
              <table class="table table-striped table-bordered table-sm" cellspacing="0" width="100%" id="tabla_inf_especifico">
                  <thead>
                      <tr>
                          <th colspan="15" style="background-color:#dff0d8; color: #000"><center id="text_general_eje">ACTIVIDADES EJECUTADAS DEL<center></th>
                      </tr>
                      <tr>
                      <th>ID Actividad</th> 
                      <th>Tiempo</th>
                      <th>Nombre Actividad</th>
                      <th>Descripción Actividad</th>
                      <th>Hora de Ejecucion</th>
                      <th>Perfil(es)</th>
                      <th>Usuario(s)</th>
                      <th>Empresa de T</th>
                      <th>Placa</th>
                      <th>Novedad</th>
                      <th>Fecha y hora Inicial</th>
                      <th>Fecha y hora Final</th>
                      <th>Frecuencia</th>
                      <th>Cumplimineto</th>
                      <th>Fecha y Hora de Cumplimiento</th>
                  </tr>
                  </thead>
                  <tbody id="resultado_info_especifico">
          
                  </tbody>
              </table>';

      echo $html;
      }

      function administrativeTable(){
        $html='
        <table class="table table-bordered table-sm" id="tabla_inf_general">
            <thead>
                <tr>
                    <th colspan="15" style="background-color:#dff0d8; color: #000"><center id="text_general_fec">ACTIVIDADES PENDIENTES POR GESTIONAR DEL <center></th> 
                </tr>
                <tr>
                    <th>ID Actividad</th> 
                    <th>Tiempo</th>
                    <th>Nombre Actividad</th>
                    <th>Descripción Actividad</th>
                    <th>Hora de Ejecucion</th>
                    <th>Perfil(es)</th>
                    <th>Usuario(s)</th>
                    <th>Fecha y hora Inicial</th>
                    <th>Fecha y hora Final</th>
                    <th>Frecuencia</th>
                    <th>Fecha y Hora de último Cumplimiento</th>
                    <th>Fecha a Ejecutar</th>
                </tr>
            </thead>
            <tbody>
                <tr id="resultado_info_general">
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered table-sm" id="tabla_inf_especifico">
            <thead>
                <tr>
                    <th colspan="15" style="background-color:#dff0d8; color: #000"  id="text_general_eje"><center>ACTIVIDADES EJECUTADAS DEL<center></th>
                </tr>
                <tr>
                <th>ID Actividad</th> 
                <th>Tiempo</th>
                <th>Nombre Actividad</th>
                <th>Descripción Actividad</th>
                <th>Hora de Ejecucion</th>
                <th>Perfil(es)</th>
                <th>Usuario(s)</th>
                <th>Fecha y hora Inicial</th>
                <th>Fecha y hora Final</th>
                <th>Frecuencia</th>
                <th>Cumplimineto</th>
                <th>Fecha y Hora de Cumplimiento</th>
            </tr>
            </thead>
            <tbody id="resultado_info_especifico">
    
            </tbody>
        </table>';
      
      echo $html;
      }

    function getPendingData(){
        //Create total query 
        $query = "SELECT a.cod_actdes, t.des_titulo as tit_activi, a.des_activi, p.nom_perfil, u.nom_usuari, a.tip_actdes, ter.abr_tercer, a.num_placax,
        f.fec_inicia, f.hor_inicia, f.fec_finalx, f.hor_finalx, prd.cod_period, prd.des_period , f.con_frecue, a.fec_ulteje, nov.nom_noveda
        FROM 
        ".BASE_DATOS.".tab_actdes_frecue as f
        LEFT JOIN tab_genera_actdes as a ON f.cod_actdes = a.cod_actdes
        LEFT JOIN tab_activi_titulo as t ON a.tit_activi = t.cod_titulo
        LEFT JOIN tab_genera_perfil as p ON a.cod_perfil = p.cod_perfil
        LEFT JOIN tab_genera_usuari as u ON a.cod_usuari = u.cod_consec
        LEFT JOIN tab_genera_period as prd ON f.cod_period = prd.cod_period
        LEFT JOIN tab_tercer_tercer as ter ON a.cod_emptra = ter.cod_tercer
        LEFT JOIN tab_genera_noveda as nov ON a.cod_noveda = nov.cod_noveda

        WHERE f.fec_finalx >= '".$_REQUEST["initDate"]."'";  

        if($_REQUEST["OperativeOrAdmin"] != "" ){
          $query .= " AND a.tip_actdes ='".$_REQUEST["OperativeOrAdmin"]."'";
        }
        //Generate consult
        $query = new Consulta($query, self::$conexion);
        $datos = $query -> ret_matrix('a');
        $filterData = self::validateData($datos);
        $json = json_encode($filterData);
        echo $json;
    }

    function getDoneData(){
      //Create total query 
      $query = "SELECT a.cod_actdes, t.des_titulo as tit_activi, a.des_activi, p.nom_perfil, u.nom_usuari, if(h.sta_ejecuc = 1, 'A tiempo', 'Atrasada') as sta_ejecuc, a.num_placax,  nov.nom_noveda,
      f.fec_inicia, f.hor_inicia, f.fec_finalx, f.hor_finalx, prd.cod_period,  prd.des_period, f.con_frecue, a.fec_ulteje, h.fec_ejecuc, a.tip_actdes, ter.abr_tercer, h.fec_dbejec
      FROM ".BASE_DATOS.".tab_actdes_histori as h
      LEFT JOIN tab_actdes_frecue as f ON h.cod_actdes = f.cod_actdes
      LEFT JOIN tab_genera_actdes as a ON f.cod_actdes = a.cod_actdes
      LEFT JOIN tab_activi_titulo as t ON a.tit_activi = t.cod_titulo
      LEFT JOIN tab_genera_perfil as p ON a.cod_perfil = p.cod_perfil
      LEFT JOIN tab_genera_usuari as u ON a.cod_usuari = u.cod_consec
      LEFT JOIN tab_genera_period as prd ON f.cod_period = prd.cod_period
      LEFT JOIN tab_tercer_tercer as ter ON a.cod_emptra = ter.cod_tercer
      LEFT JOIN tab_genera_noveda as nov ON a.cod_noveda = nov.cod_noveda

      WHERE h.fec_ejecuc >= '".$_REQUEST["initDate"]."' AND h.fec_ejecuc <= '".date( "Y-m-d", strtotime($_REQUEST["lastDate"]." +1 day" ) )."'";  
    
      if($_REQUEST["OperativeOrAdmin"] != "" ){
        $query .= " AND a.tip_actdes ='".$_REQUEST["OperativeOrAdmin"]."' ORDER BY h.fec_ejecuc ASC";
      }
      //Generate consult
      $query = new Consulta($query, self::$conexion);
      $datos = $query -> ret_matrix('a');
      $filterData = self::validateDataTime($datos);
      $json = json_encode($filterData);
      echo $json;
  }

    function sort_by_orden ($a, $b) {
      return $a['FechaAejecutar'] > $b['FechaAejecutar'];
    }

    /****************************************************************************
    NOMBRE:   validateData
    FUNCION:  Funcion mas compleja, segun el rango de fechas de la actividad valida la
              cantidad de veces que debe repetirse y a su vez valida si las fechas pasadas 
              dentro de ese rango estan en el rango de fechas filtradas por el usuario,
              tambien valida el tipo de frecuecia, dado que segun el tipo de frecuencia el 
              campo se mostrara determinadas veces. Tener en cuenta que lo que se muestra en 
              la tabla es un registo del historico que se repite con ciclos for por lo cual solo
              uno existe en bd y los demas son recorridos del ciclo, al igual que la fecha a ejecutar 
              es producto del ciclo.
    FECHA DE MODIFICACION: 07/10/2021
    CREADO POR: Ing. Carlos Nieto
    MODIFICADO 
    ****************************************************************************/

    function validateData($array){
      $date_start = strtotime($_REQUEST["initDate"]);
      $date_end = strtotime($_REQUEST["lastDate"]);
      $ArrayPush = [];

      foreach ($array as $key => $value) {
        $currentDaySt = strtotime(date("d-m-Y"));
        $lastExcutionSt = strtotime($value['fec_ulteje']);
        $initDatetimeBDSt = strtotime($value['fec_inicia'].' '.$value['hor_inicia']);
        $lastDatetimeBDSt = strtotime($value['fec_finalx'].' '.$value['hor_finalx']);

        for($i=strtotime($value['fec_inicia']); $i <= strtotime($value['fec_finalx']); $i += 86400){
          $dateRuteSt = strtotime(date("d-m-Y", $i));
          $dateRute = date("Y-m-d", $i);
          $dateRuteWeekNumberValue = date("N", $i);
          if(self::checkInRange(date("d-m-Y", $i)) && $dateRuteSt >= $currentDaySt)
          {
            if($lastExcutionSt != $dateRuteSt && $dateRuteSt > $lastExcutionSt)
            {
              if(self::validateFecuenci($value['cod_period'], $dateRute, $_REQUEST["initDate"], $_REQUEST["lastDate"], $value['con_frecue'] , $dateRuteWeekNumberValue)
              && $dateRuteSt >= $date_start && $dateRuteSt <= $date_end)
              {
                if($value['cod_period'] != '6')
                {
                $value['FechaAejecutar'] = $dateRute;
                $value['OverTime'] = self::getTimeDiff(date("Y-m-d ".$value['hor_inicia'], $i));
                array_push($ArrayPush, $value);
                }
              }
            }
          }
        }
        if($value['cod_period'] == '6')
        {
          $hourSteep = preg_replace('/[^0-9]/', '', $value['con_frecue']);
          $daysHour = self::validateDataHour($hourSteep, $initDatetimeBDSt, $lastDatetimeBDSt, strtotime($_REQUEST["lastDate"]." +1 day" ));
          foreach ($daysHour as  $days) {
            $dateRuteWeekNumberValue = date("N", strtotime($days));
            $value['FechaAejecutar'] = $days;
            $value['hor_inicia'] = date('H:i', strtotime($days));
            $value['OverTime'] = self::getTimeDiff($days);
            if(self::validateHistorical($value['cod_actdes'], $days) &&
            self::validateFecuenci(6, null, null, null, $value['con_frecue'] , $dateRuteWeekNumberValue))
            {
              array_push($ArrayPush, $value);
            }
          }
        }
      }
        usort($ArrayPush ,array($this, "sort_by_orden"));
        return $ArrayPush;
    }

    function validateHistorical($cod_acdes,$starTime){
      $query = "SELECT * FROM ".BASE_DATOS.".tab_actdes_histori WHERE cod_actdes = '".$cod_acdes."' 
      AND fec_dbejec = '".$starTime."'";  
      //Generate consult
      $query = new Consulta($query, self::$conexion);
      $datos = $query -> ret_matrix('a');
      if(count($datos) > 0)
      {
        $val = false;
      }else{
        $val = true;
      }
      return $val;
    }

    function validateDataHour($hourSteep,$intiHour,$LastHour,$lastRangeDate){
      $secs =  $hourSteep * 60 * 60;
      $daysHour = array();
      $initH = strtotime(date("H:i",$intiHour)); 
      $LastH =strtotime( date(" H:i",$LastHour));

      for($i=$intiHour; $i<=$LastHour; $i+=$secs){
        $hourRoute = strtotime(date("H:i", $i));
        $dateRoute = strtotime(date("Y-m-d H:i:s", $i));

        if($hourRoute >= $initH && $hourRoute <= $LastH)
        {
          if($dateRoute <= $lastRangeDate)
          {
            array_push( $daysHour, date("Y-m-d H:i:s", $i) );
          }
        } 
      }
      return $daysHour;
    }

    function validateDataTime($array){
      $ArrayPush = [];
      
      foreach ($array as $key => $value) {
        $exceutionDate = strtotime($value["cod_period"] == '6' ? $value["fec_dbejec"] : $value["fec_inicia"].' '. $value["hor_inicia"]);       
        $dateDone = strtotime($value["fec_ejecuc"]);
        if($dateDone > $exceutionDate)
        {
          $val = round(abs($dateDone - $exceutionDate) / 60,2);
        }else{
          $val = '-'.round(abs($dateDone - $exceutionDate) / 60,2);
        }
        $value['time'] = $val;
        array_push($ArrayPush, $value);
      }
        return $ArrayPush;
    }

    function getTimeDiff ($executionDate) {
      $to_time = strtotime(date('d M Y H:i:s'));
      $from_time = strtotime($executionDate);
      if($to_time > $from_time)
      {
         $val = round(abs($to_time - $from_time) / 60,2);
      }else{
        $val = 0;
      }
      return $val;
    }

    function checkInRange($date_now) {
      $date_start = strtotime($_REQUEST["initDate"]);
      $date_end = strtotime($_REQUEST["lastDate"]);
      $datePs = strtotime($date_now);

      if (($datePs >= $date_start) && ($datePs <= $date_end))
      {
        return true;
      }else{
        return false;
      }
    }

    function validateFecuenci($option, $dateFor, $date_start, $date_end, $codFrecienci, $numberWeekDay) {
      switch($option)
      {
          /* ** No se repite---->ok*/
          case 1:
              return true;
          break;
         /* ** Cada día--->ok*/
          case 2:
              return true;
          break;
           /* ** Cada semana, pasa cuando el numero del dia de la semana se encuentre en el codigo de frecuencia-->ok*/
           case 3:
              return self::checkWeekDay($codFrecienci, $numberWeekDay);
          break;
          //Cada mes--->OK
          case 4:
                return self::checkPerMoth($codFrecienci, $dateFor);
          break;
          //Anualmente --->ok
          case 5:
                return self::checkPerYear($codFrecienci, $dateFor);
          break;
          case 6:
            return self::checkWeekDay($codFrecienci, $numberWeekDay);
          break;
      }

    }

    function checkWeekDay($codFrecienci, $numberWeekDay) {
      switch($numberWeekDay)
      {
          case 1:
            $val = "L";
          break;
          case 2:
            $val = "M";
          break;
          case 3:
            $val = "X";
          break;
          case 4:
            $val = "J";
          break;
          case 5:
            $val = "V";
          break;
          case 6:
            $val = "S";
          break;
          case 7:
            $val = "D";
          break;
          default:
            $val = "F";
          break;
      }
      $data = explode("|", $codFrecienci);
      foreach ($data as $value) {
        if($value == $val)
        {
          return true;
          break;
        }
      } 
    }

    function checkPerMoth($codFrecienci, $dateFor) {     
      $data = explode("|", $codFrecienci);
      foreach ($data as $value) {
        if(is_numeric($value))
        {
          $number = self::switchSelect($value);
        }else{
          $day = self::switchSelect($value);
        }
      }        
        $date = strtotime($number.' '.$day.' of '.date("M-Y",strtotime($dateFor)));
        if(strtotime($dateFor) == $date)
        {
          return true;
        }        
    }

    function switchSelect($option)
    {
      switch($option)
      {
          case "1":
            $val = "first";
          break;
          case "2":
            $val = "second";
          break;
          case "3":
            $val = "third";
          break;
          case "4":
            $val = "fourth";
          break;
          case "L":
            $val = "monday";
          break;
          case "M":
            $val = "tuesday";
          break;
          case "X":
            $val = "wednesday";
          break;
          case "J":
            $val = "thursday";
          break;
          case "V":
            $val = "friday";
          break;
          case "S":
            $val = "saturday";
          break;
          case "D":
            $val = "sunday";
          break;
      }
      
      return $val;
    }

    function checkPerYear($codFrecienci, $dateFor) {    
      $data = explode("|", $codFrecienci);
      $date = date(date('Y', strtotime($dateFor)).'-'.$data[1].'-'.$data[0].'');

        if(strtotime($dateFor) == strtotime($date))
        {
          return true;
        }
    }

    private function historyModal(){
      $html='
      <div class="row  mt-3">
        <div class="col-6 mt-1">
          <div class="form-floating">
            <h6>Tipo de Actividad :</h6>
            <input class="form-control form-control-sm" type="text" id="ActivityType" value="" disabled>
          </div>
        </div> 
        <div class="col-6 mt-1">
          <div class="form-floating">
            <h6>Tipo de Tarea :</h6>
            <input class="form-control form-control-sm" type="text" id="ActivityWork" value="" disabled>
          </div>
        </div> 
        <div class="col-6 mt-1">
          <div class="form-floating">
            <h6>Perfil :</h6>
            <input class="form-control form-control-sm" type="text" id="profile" value="" disabled>
          </div>
        </div>   
        <div class="col-6 mt-1">
          <div class="form-floating">
            <h6>Fecha y hora inicial :</h6>
            <input class="form-control form-control-sm" type="datetime" id="initDate" value="" disabled>
          </div>
        </div>
        <div class="col-6 mt-1">
          <div class="form-floating">
            <h6>Fecha y hora final :</h6>
            <input class="form-control form-control-sm" type="datetime" id="LatsDate" value="" disabled>
          </div>
        </div>
        <div class="col-6 mt-1">
          <div class="form-floating">
            <h6>Usuario :</h6>
            <input class="form-control form-control-sm" type="text" id="user" value="" disabled>
          </div>
        </div>  
        <div class="col-6 mt-1">
          <div class="form-floating">
            <h6>Fecuencia :</h6>
            <input class="form-control form-control-sm" type="text" id="fecuency" value="" disabled>
          </div>
        </div> 
        <div class="col-6 mt-1">
          <div class="form-floating">
            <h6>Días :</h6>
            <input class="form-control form-control-sm" type="text" id="days" value="" disabled>
          </div>
        </div> 
        <div class="col-12 mt-1">
          <div class="form-floating">
            <h6>Descipción de la Actividad :</h6>
            <textarea class="form-control" id="description" rows="3" disabled></textarea>
          </div>
        </div>
        <div class="col-12 mt-3">
          <div class="form-floating">
          <table class="table table-striped table-bordered table-sm" cellspacing="0" width="100%" id="table_History">
                <thead>
                    <tr>
                        <th colspan="15" style="background-color:#dff0d8; color: #000"><center id="title_history">Historial de  Ejecución<center></th> 
                    </tr>
                    <tr>
                        <th>Fecha de ejecución</th> 
                        <th>Usuario</th>
                        <th>Porcentaje</th>
                        <th>Ejecución</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="resultado_Historical"></tr>
                </tbody>
            </table>
          </div>
        </div> 
        <div class="col-12" id="formHistorial"></div>
      </div>';

      echo $html;
    }

    function getHistoricalData(){
      //Create total query 
      $query = "SELECT a.cod_actdes, t.des_titulo as tit_activi, a.des_activi, p.nom_perfil, u.nom_usuari,if(h.sta_ejecuc = 1, 'A tiempo', 'Atrasada') as sta_ejecuc, if( a.tip_actdes = 1, 'Administrativa', 'Operativa') as tip_actdes,
      f.fec_inicia, f.hor_inicia, f.fec_finalx, f.hor_finalx, prd.cod_period, prd.des_period , f.con_frecue, a.fec_ulteje, h.fec_ejecuc, h.val_porcen, h.obs_ejecuc , h.usr_creaci
      FROM ".BASE_DATOS.".tab_actdes_histori as h
      LEFT JOIN tab_actdes_frecue as f ON h.cod_actdes = f.cod_actdes
      LEFT JOIN tab_genera_actdes as a ON f.cod_actdes = a.cod_actdes
      LEFT JOIN tab_activi_titulo as t ON a.tit_activi = t.cod_titulo
      LEFT JOIN tab_genera_perfil as p ON a.cod_perfil = p.cod_perfil
      LEFT JOIN tab_genera_usuari as u ON a.cod_usuari = u.cod_consec
      LEFT JOIN tab_genera_period as prd ON f.cod_period = prd.cod_period
      WHERE h.cod_actdes = '".$_REQUEST["cod_activ"]."'
      ORDER BY h.fec_ejecuc ASC";  
  
      //Generate consult
      $query = new Consulta($query, self::$conexion);
      $datos = $query -> ret_matrix('a');
      $json = json_encode($datos);
      echo $json;
    }

    private function historyModalForm(){
      $html='
      <div>
        <form id="NewObj" method="POST">
          <div class="col-6 mt-1">
            <select class="custom-select" id="progreso" name="progreso" required>
              <option value="0" selected>Seleccione Progreso...</option>
              <option value="5">5 %</option>
              <option value="10">10 %</option>
              <option value="15">15 %</option>
              <option value="20">20 %</option>
              <option value="25">25 %</option>
              <option value="30">30 %</option>
              <option value="35">35 %</option>
              <option value="40">40 %</option>
              <option value="45">45 %</option>
              <option value="50">50 %</option>
              <option value="55">55 %</option>
              <option value="55">55 %</option>
              <option value="65">65 %</option>
              <option value="70">70 %</option>
              <option value="75">75 %</option>
              <option value="80">80 %</option>
              <option value="85">85 %</option>
              <option value="90">90 %</option>
              <option value="95">95 %</option>
              <option value="100">100 %</option>
            </select>
          </div> 
          <div class="col-12 mt-1">
            <div class="form-floating">
              <h6>Observación :</h6>
              <textarea class="form-control" id="obsForm" rows="3" name="obsForm" required></textarea>
            </div>
          </div>
          <div class="col-12 mt-3 text-center">
            <div class="form-floating">
              <button type="button" class="btn btn-success" style="background-color:#509334" onclick="insertNewObj()">Insertar</button>
            </div>
          </div>   
        </form>
      </div>';

      echo $html;
    }

    private function historyInsertForm(){

      $status = self::evaluateTimeHour($_REQUEST['FechaAejecutar'], $_REQUEST['horaFin'], $_REQUEST['codPeriod']);
      
       $sql="INSERT INTO tab_actdes_histori(
        cod_actdes, obs_ejecuc, fec_ejecuc, fec_dbejec,
         val_porcen, sta_ejecuc, usr_creaci, fec_creaci 
        ) 
        VALUES(
          '".$_REQUEST['codActdes']."', '".$_REQUEST['NewObj'][1]['value']."', 
          '".date('Y-m-d H:i:s')."', '".$_REQUEST['FechaAejecutar']."', '".$_REQUEST['NewObj'][0]['value']."', '".$status."',
          '".$_SESSION['datos_usuario']['cod_usuari']."', '".date('Y-m-d H:i:s')."');";

        $sqlUpdate="UPDATE tab_genera_actdes SET fec_ulteje = '".date('Y-m-d H:i:s')."' 
        WHERE tab_genera_actdes.cod_actdes = '".$_REQUEST['codActdes']."'";   


      $query = new Consulta($sql, self::$conexion);
      $queryIpdate = new Consulta($sqlUpdate, self::$conexion);

      if($query){
        $info['status']=200; 
      }else{
        $info['status']=100; 
      } 
      echo json_encode($info);
    } 

    private function evaluateTimeHour($fec_ejecuc, $maxExecutionHour, $codPeriod){
      $valid = $codPeriod == '6' ? strtotime($fec_ejecuc." +25 minutes" ) : strtotime($fec_ejecuc.' '.$maxExecutionHour);

      if(strtotime(date('Y-m-d H:i:s')) > $valid)
      {
        return 2;
      }else{
        return 1;
      }
    }

    function getProfileData(){
      //Create total query 
      $query = "SELECT * FROM ".BASE_DATOS.".tab_genera_perfil
      ORDER BY tab_genera_perfil.nom_perfil ASC";  
  
      //Generate consult
      $query = new Consulta($query, self::$conexion);
      $data = $query -> ret_matrix('a');
      foreach($data as $servicio){
        $html.='<option value="'.$servicio['cod_perfil'].'">'.utf8_decode($servicio['nom_perfil']).'</option>';
      } 
      return $html;
    }

    function getUsersData(){
      //Create total query 
      $query = "SELECT * FROM ".BASE_DATOS.".tab_genera_usuari WHERE ind_estado = 1 ORDER BY nom_usuari ASC";  
  
      //Generate consult
      $query = new Consulta($query, self::$conexion);
      $data = $query -> ret_matrix('a');
      foreach($data as $servicio){
        $html.='<option value="'.$servicio['cod_consec'].'">'.utf8_decode($servicio['nom_usuari']).'</option>';
      } 
      return $html;
    }

    function getFrecuencyData(){
      //Create total query 
      $query = "SELECT * FROM ".BASE_DATOS.".tab_genera_period ORDER BY des_period ASC";  
  
      //Generate consult
      $query = new Consulta($query, self::$conexion);
      $data = $query -> ret_matrix('a');
      foreach($data as $servicio){
        $html.='<option value="'.$servicio['cod_period'].'">'.utf8_decode($servicio['des_period']).'</option>';
      } 
      return $html;
    }

    function getNovedaData(){
      //Create total query 
      $query = "SELECT * FROM ".BASE_DATOS.".tab_genera_noveda ORDER BY nom_noveda ASC
      ";  
  
      //Generate consult
      $query = new Consulta($query, self::$conexion);
      $data = $query -> ret_matrix('a');
      foreach($data as $servicio){
        $html.='<option value="'.$servicio['cod_noveda'].'">'.utf8_decode($servicio['nom_noveda']).'</option>';
      } 
      return $html;
    }

    function getCompanyData(){
      //Create total query 
      $query = "SELECT t.abr_tercer, e.cod_tercer FROM ".BASE_DATOS.".tab_tercer_emptra AS e
      LEFT JOIN tab_tercer_tercer AS t ON e.cod_tercer = t.cod_tercer
      WHERE t.cod_estado = 1
      ORDER BY t.abr_tercer  ASC";  
  
      //Generate consult
      $query = new Consulta($query, self::$conexion);
      $data = $query -> ret_matrix('a');
      foreach($data as $servicio){
        $html.='<option value="'.$servicio['cod_tercer'].'">'.utf8_decode($servicio['abr_tercer']).'</option>';
      } 
      return $html;
    }

    function selectTile(){
      $getTitleData = $this->getTitleData();

            $html='
              <div class="row  mt-3">
                <div class="col-12 mt-3">
                  <select class="custom-select" id="ActivityTitle" name="ActivityTitle">
                    <option value="0" selected>Seleccione...</option>
                    '.$getTitleData.'
                  </select> 
                </div>
              </div>
          ';
          echo $html;
    }

    function getTitleData(){
      //Create total query 
      $query = "SELECT * FROM ".BASE_DATOS.".tab_activi_titulo WHERE est_titulo = 1 ORDER BY des_titulo ASC";  
  
      //Generate consult
      $query = new Consulta($query, self::$conexion);
      $data = $query -> ret_matrix('a');
      foreach($data as $servicio){
        $html.='<option value="'.$servicio['cod_titulo'].'">'.utf8_decode($servicio['des_titulo']).'</option>';
      } 
      return $html;
    }

    function dinamycSelects(){
      $getProfileData = $this->getProfileData();
      $getUsersData = $this->getUsersData();
      $getFrecuencyData = $this->getFrecuencyData();

      $html='
      <div class="row">
        <div class="col-6 mt-3">       
          <h6>Seleccionar Perfil*(s) :</h6>        
          <select class="custom-select" id="profile" name="profile">
            <option value="0" selected>Seleccione...</option>
            '.$getProfileData.'
          </select>   
        </div>

        <div class="col-6 mt-3">       
          <h6>Selecionar Usuario (s): </h6>        
          <select class="custom-select" id="user" name="user">
            <option value="0" selected>Seleccione...</option>
            '.$getUsersData.'
          </select>   
        </div>

        <div class="col-6 mt-3">       
          <h6>Seleccione la Frecuencia(s) *: </h6>        
          <select class="custom-select" id="frecuencySelect" name="frecuencySelect" onchange="callFormOptionFrecuency(this)">
            <option value="0" selected>Seleccione...</option>
            '.$getFrecuencyData.'
          </select>   
        </div>

        <div class="col-6 mt-3" id="valueOption">
          
        </div>
      </div>';

      echo $html;
    }

    function callFormOptionFrecuency(){
      $this->switchSelectForm($_REQUEST["valueOption"]);
    }

    function SelectPerYear(){
      $html='<h6>Fecha de Ejecución *: </h6>        
      <input type="date" class="form-control" id="executionDate" name="executionDate">';
      echo $html;
    }

    function SelectPerHour(){
      $html='<h6>Dias :</h6>  
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="lunes" name="lunes">
        <label class="custom-control-label" for="lunes" style="padding-right:1em;">Lunes</label>
      </div>      
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="martes" name="martes">
        <label class="custom-control-label" for="martes" style="padding-right:1em;">Martes</label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="miercoles" name="miercoles">
        <label class="custom-control-label" for="miercoles">Miercoles</label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="jueves" name="jueves">
        <label class="custom-control-label" for="jueves">Jueves   </label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="viernes" name="viernes">
        <label class="custom-control-label" for="viernes" style="padding-right:0.4em;">Viernes</label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="sabados" name="sabados">
        <label class="custom-control-label" for="sabados">Sabados  </label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="domingos" name="domingos">
        <label class="custom-control-label" for="domingos">domingos  </label>
      </div>

      <h6>Seleccione Periodo de horas* :</h6>        
      <select class="custom-select" id="optionHour" name="optionHour">
        <option value="0" selected>Seleccione...</option>
        <option value="1">Cada hora</option>
        <option value="2">Cada dos horas</option>
        <option value="3">Cada tres horas</option>
        <option value="4">Cada cuatro horas</option>
        <option value="5">Cada cinco horas</option>
        <option value="6">Cada seis horas</option>
        <option value="7">Cada siete horas</option>
        <option value="8">Cada ocho horas</option>
        <option value="9">Cada  nueve horas</option>
        <option value="10">Cada diez horas</option>
        <option value="11">Cada once horas</option>
        <option value="12">Cada doce horas</option>
      </select>  
      ';
      echo $html;
    }

    function SelectEachDay(){
      $html='<h6>Dias :</h6>        
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="lunes" name="lunes"checked disabled>
        <label class="custom-control-label" for="lunes" style="padding-right:1em;">Lunes</label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="martes" name="martes" checked disabled>
        <label class="custom-control-label" for="martes" style="padding-right:1em;">Martes</label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="miercoles" name="miercoles" checked disabled>
        <label class="custom-control-label" for="miercoles">Miercoles</label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="jueves" name="jueves" checked disabled>
        <label class="custom-control-label" for="jueves">Jueves   </label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="viernes" name="viernes" checked disabled>
        <label class="custom-control-label" for="viernes" style="padding-right:0.4em;">Viernes</label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="sabados" name="sabados" checked disabled>
        <label class="custom-control-label" for="sabados">Sabados  </label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="domingos" name="domingos"checked disabled>
        <label class="custom-control-label" for="domingos">Domingos </label>
      </div>';
      echo $html;
    }

    function SelectPerMonth(){
      $html=' <h6>Seleccione Periodo* :</h6>        
      <select class="custom-select" id="optionMonth" name="optionMonth">
        <option value="0" selected>Seleccione...</option>
        <option value="L|1" selected>El primer lunes de cada mes</option>
        <option value="L|2" selected>El segundo lunes de cada mes</option>
        <option value="L|3" selected>El tercer lunes de cada mes</option>
        <option value="L|4" selected>El cuarto lunes de cada mes</option>
        <option value="M|1" selected>El primer martes de cada mes</option>
        <option value="M|2" selected>El segundo martes de cada mes</option>
        <option value="M|3" selected>El tercer martes de cada mes</option>
        <option value="M|4" selected>El cuarto martes de cada mes</option>
        <option value="X|1" selected>El primer Miercoles de cada mes</option>
        <option value="X|2" selected>El segundo Miercoles de cada mes</option>
        <option value="X|3" selected>El tercer Miercoles de cada mes</option>
        <option value="X|4" selected>El cuarto Miercoles de cada mes</option>
        <option value="J|1" selected>El primer Jueves de cada mes</option>
        <option value="J|2" selected>El segundo Jueves de cada mes</option>
        <option value="J|3" selected>El tercer Jueves de cada mes</option>
        <option value="J|4" selected>El cuarto Jueves de cada mes</option>
        <option value="V|1" selected>El primer Viernes de cada mes</option>
        <option value="V|2" selected>El segundo Viernes de cada mes</option>
        <option value="V|3" selected>El tercer Viernes de cada mes</option>
        <option value="V|4" selected>El cuarto Viernes de cada mes</option>
        <option value="S|1" selected>El primer Sabado de cada mes</option>
        <option value="S|2" selected>El segundo Sabado de cada mes</option>
        <option value="S|3" selected>El tercer Sabado de cada mes</option>
        <option value="S|4" selected>El cuarto Sabado de cada mes</option>
        <option value="D|1" selected>El primer Domingo de cada mes</option>
        <option value="D|2" selected>El segundo Domingo de cada mes</option>
        <option value="D|3" selected>El tercer Domingo de cada mes</option>
        <option value="D|4" selected>El cuarto Domingo de cada mes</option>
      </select>  ';
      echo $html;
    }

    function SelectPerWeek(){
      $html='<h6>Dias :</h6>  
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="lunes" name="lunes">
        <label class="custom-control-label" for="lunes" style="padding-right:1em;">Lunes</label>
      </div>      
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="martes" name="martes">
        <label class="custom-control-label" for="martes" style="padding-right:1em;">Martes</label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="miercoles" name="miercoles">
        <label class="custom-control-label" for="miercoles">Miercoles</label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="jueves" name="jueves">
        <label class="custom-control-label" for="jueves">Jueves   </label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="viernes" name="viernes">
        <label class="custom-control-label" for="viernes" style="padding-right:0.4em;">Viernes</label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="sabados" name="sabados">
        <label class="custom-control-label" for="sabados">Sabados  </label>
      </div>
      <div class="custom-control custom-checkbox custom-control-inline">
        <input type="checkbox" class="custom-control-input" id="domingos" name="domingos">
        <label class="custom-control-label" for="domingos">domingos  </label>
      </div>
      ';
      echo $html;
    }

    function SelectFullDateTime(){
      $html=' <div class="row">
                <div class="col-3 mt-3">       
                  <h6>Fecha Inicial *: </h6>        
                  <input type="date" class="form-control" id="InitDate" name="InitDate">  
                </div>
                <div class="col-3 mt-3">       
                  <h6>Hora Inicial *: </h6>        
                  <input type="time" class="form-control" id="InitHour" name="InitHour">  
                </div>

                <div class="col-3 mt-3">       
                  <h6>Fecha Final *: </h6>        
                  <input type="date" class="form-control" id="FinishDate" name="FinishDate">  
                </div>
                <div class="col-3 mt-3">       
                  <h6>Hora Final *: </h6>        
                  <input type="time" class="form-control" id="FinishHour" name="FinishHour">  
                </div>
              </div>';
      echo $html;
    }

    function SelectShortDateTime(){
      $html=' <div class="row">
                <div class="col-6 mt-3">       
                  <h6>Fecha Inicial *: </h6>        
                  <input type="date" class="form-control" id="InitDate" name="InitDate">  
                </div>
                <div class="col-3 mt-3">       
                  <h6>Hora Inicial *: </h6>        
                  <input type="time" class="form-control" id="InitHour" name="InitHour">  
                </div>
                <div class="col-3 mt-3">       
                  <h6>Hora Final *: </h6>        
                  <input type="time" class="form-control" id="FinishHour" name="FinishHour">  
                </div>
              </div>';
      echo $html;
    }

    function switchSelectForm($option)
    {
      switch($option)
      {
          case "6":
            $this->SelectPerHour();
          break;
          case "5":
            $this->SelectPerYear();
          break;
          case "2":
            $this->SelectEachDay();
          break;
          case "4":
            $this->SelectPerMonth();
          break;
          case "3":
            $this->SelectPerWeek();
          break;
          case "1":
            return;
          break;
      }
      
      return $val;
    }

    function InsertNewActivity() 
    {
      $evalData = $this->EvaluateDataInsertNewActivity($_REQUEST['NewformData']);
  
      $sql="INSERT INTO tab_genera_actdes (
        tip_actdes, tit_activi, des_activi, cod_perfil,";
      if(array_key_exists('user',$evalData))
      {
        $sql=$sql." cod_usuari,";
      }
      if(array_key_exists('company',$evalData))
      {
        $sql=$sql." cod_emptra,";
      }
      if(array_key_exists('palca',$evalData))
      {
        $sql=$sql." num_placax,";
      }
      if(array_key_exists('novedad',$evalData))
      {
        $sql=$sql." cod_noveda,";
      }

        $sql=$sql." ins_status, usr_creaci, fec_creaci
        ) 
      VALUES (
        '".$evalData['RadioOptions']."', '".$evalData['ActivityTitle']."', '".$evalData['description']."',  '".$evalData['profile']."',";
        if(array_key_exists('user',$evalData))
      {
        $sql=$sql."'".$evalData['user']."',";
      }
      if(array_key_exists('company',$evalData))
      {
        $sql=$sql."'".$evalData['company']."',";
      }
      if(array_key_exists('palca',$evalData))
      {
        $sql=$sql."'".$evalData['palca']."',";
      }
      if(array_key_exists('novedad',$evalData))
      {
        $sql=$sql."'".$evalData['novedad']."',";
      }

      $sql=$sql."'1', '".$_SESSION['datos_usuario']['cod_usuari']."', '".date('Y-m-d H:i:s')."');";
      
      $query = new Consulta($sql, self::$conexion);
      $lastInsetID =  mysql_insert_id();

      $sql2="INSERT INTO tab_actdes_frecue (
        cod_actdes,"; 
      
      if($evalData['frecuencySelect'] != '1')
      {
        $sql2=$sql2." con_frecue,";
      }
        
        $sql2=$sql2." fec_inicia, hor_inicia, fec_finalx,"; 

      /* if($evalData['frecuencySelect'] != '1')
      {
        $sql2=$sql2." fec_finalx,";
      }   */

        $sql2=$sql2." hor_finalx, cod_period, usr_creaci, fec_creaci)
         VALUES (
        '".$lastInsetID."',";

        if(array_key_exists('executionDate',$evalData))
        {
          $sql2=$sql2."'".$evalData['executionDate']."',";
        }
        if(array_key_exists('cod_frecuenci',$evalData))
        {
          $sql2=$sql2."'".$evalData['cod_frecuenci']."',";
        }
        if(array_key_exists('optionMonth',$evalData))
        {
          $sql2=$sql2."'".$evalData['optionMonth']."',";
        }
        
        $sql2=$sql2.="'".$evalData['InitDate']."', '".$evalData['InitHour']."',";

        if(array_key_exists('FinishDate',$evalData))
        {
          $sql2=$sql2."'".$evalData['FinishDate']."',";
        }else{
          $sql2=$sql2."'".$evalData['InitDate']."',";
        }
        
        $sql2=$sql2.="'".$evalData['FinishHour']."', '".$evalData['frecuencySelect']."', 
                     '".$_SESSION['datos_usuario']['cod_usuari']."', '".date('Y-m-d H:i:s')."');";
  
      $query2 = new Consulta($sql2, self::$conexion);

      if($query2){
        $info['status']=200; 
      }else{
        $info['status']=100; 
      } 
      echo json_encode($info);
    }

    function EvaluateDataInsertNewActivity($NewformData) 
    {
      $evalData = array();
      $weekToSave = array();
      $week = array("lunes", "martes", "miercoles", "jueves", "viernes", "sabados", "domingos");
      foreach ($NewformData as $key => $value) {
        if($value['value'] != '0')
        {
          if(in_array($value['name'], $week) || $value['name'] == 'optionHour')
          {
            $weekToSave[0] = is_numeric($value['value']) ? $weekToSave[0].$value['value'].'h|' : $weekToSave[0].$this->switchWeekDay($value['name']).'|';
            $evalData['cod_frecuenci'] = $weekToSave[0];
          }else{
            if($value['name'] == 'frecuencySelect' && $value['value'] == '2')
            {
              $evalData[$value['name']] = $value['value'];
              $evalData['cod_frecuenci'] = 'L|M|X|J|V|S|D';

            }else if($value['name'] == 'executionDate'){
              $evalData[$value['name']] = date('d|m', strtotime($value['value']));
            }else{
              $evalData[$value['name']] = $value['value'];
            }
          }
        }
      }
      return $evalData;

    }

    function switchWeekDay($option)
    {
      switch($option)
      {
          case "lunes":
            $val = 'L';
          break;
          case "martes":
            $val = 'M';
          break;
          case "miercoles":
            $val = 'X';
          break;
          case "jueves":
            $val = 'J';
          break;
          case "viernes":
            $val = 'V';
          break;
          case "sabados":
            $val = 'S';
          break;
          case "domingos":
            $val = 'D';
          break;
      }
      
      return $val;
    }    

    }

    new AjaxActiviDesarr();
    
?>