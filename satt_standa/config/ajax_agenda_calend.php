<?php 

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

/*! \Class: ajaxCalendAgendamiento
*  \brief: Clase encargada de hacer la conexion para hacer la solicitud de SOAT
*  \author: Ing. Luis Manrique
*  \date: 22/10/2019
*  \param: $mConnection  -  Variable de clase que almacena la conexion de la Base de datosm biene desde el framework
*  \return array
*/

class ajaxCalendAgendamiento
{
  private $cod_usuari;

  var $conexion = NULL;   
  var $cod_aplica;
  /*! \fn: ajaxCalendAgendamiento
  *  \brief: constructor de php4 para la clase
  *  \author: Ing. Luis Manrique
  *  \date: 16/07/2015   
  *  \param: fConection  : Conexion de base de datos 
  *  \param: mParams     : Array con los datos a enviar 
  *  \return n/a
  */
  function ajaxCalendAgendamiento( $mConnection, $mData )
  {   
    include '../lib/ajax.inc';
    include '../lib/general/constantes.inc';
    $this -> conexion = $AjaxConnection;
    $this -> cod_usuari = $_SESSION['datos_usuario']['cod_usuari'];
    $this -> cod_aplica=COD_APLICACION;
    @include_once( '../'.DIR_APLICA_CENTRAL.'/lib/general/functions.inc' );
    switch( $_REQUEST["Option"] )
    { 
      case "creEditAgendPedi":
        $this -> creEditAgendPedi();
      break;
      case "creEditAgendPediCl":
        $this -> creEditAgendPediCl();
      break;
      case "viewAgenda":
        $this -> viewAgenda();
      break;
      case "updateAgenda":
        $this -> updateAgenda();
      break;
      case "listCodAgenda":
      	$this -> listCodAgenda();
      break;
      case "busquedaFranja":
      	$this -> busquedaFranja();
      break;
      case "busquedaHora":
      	$this -> busquedaHora();
      break;
      case "registraCitacion":
      	$this -> registraCitacion();
      break;
      case "EliminarFranja":
      	$this -> validarEliminacion();
      break;
    }
  }


  /*! \fn: creEditAgendPedi
  *  \brief: Metodo que consulta y genera la actualizaci�n o registro del agendamiento de un pedido
  *  \author: Ing. Luis Manrique
  *  \date: 20/12/2019    
  *  \return n/a
  */  
  function creEditAgendPedi(){

  	if(isset($_REQUEST['hor_apert_hor_cierre'])){
  		$horario = explode(" - ", $_REQUEST['hor_apert_hor_cierre']);
  		$_REQUEST['start'] = $horario[0];
      $_REQUEST['end'] = $horario[1];
      $_REQUEST['id'] = $_REQUEST['cod_pedido'];
  	}

      $respuesta = [];

      $mSql = "SELECT  a.fec_inicio,
                        a.fec_finalx 
                  FROM  ".BASE_DATOS.". tab_agenda_cliret a
                 WHERE  DATE(fec_inicio) = '".date('Y-m-d',strtotime($_REQUEST['start']))."' 
                        AND DATE(fec_finalx) = '".date('Y-m-d',strtotime($_REQUEST['end']))."'";

      $consulta = new Consulta( $mSql, $this -> conexion );
      $rangoCl = $consulta->ret_matriz();


      if(count($rangoCl) > 0){
        $ban = 0;
        $franja = [];
        foreach ($rangoCl as $key => $value) {
          if(strtotime($_REQUEST['start']) >= strtotime($value['fec_inicio']) AND strtotime($_REQUEST['end']) <= strtotime($value['fec_finalx'])) 
          {
            $ban = 1;
          }else{
            $franja[] = "(".$value['fec_inicio']." a ".$value['fec_finalx'].")";
          }
        }

        if($ban == 0){
          $respuesta["status"] = 3;
          $respuesta["title"] = "Oh!";
          $respuesta["text"] = "El horario seleccionado esta fuera del rango permitido el cual es de ".join(", ",$franja).".";
          $respuesta["type"] = "warning";

          echo json_encode($respuesta);
          die();
        }
      }else{
      	$respuesta["status"] = 3;
        $respuesta["title"] = "Oh!";
        $respuesta["text"] = "No se ha establecido rango de programacion para los pedidos de cliente retira";
        $respuesta["type"] = "warning";

        echo json_encode($respuesta);
        die();
      }

      $mSql = "SELECT  a.fec_inicio,
                        a.fec_finalx 
                  FROM  ".BASE_DATOS.". tab_agenda_pedido a
                 WHERE  DATE(fec_inicio) = '".date('Y-m-d',strtotime($_REQUEST['start']))."' 
                        AND DATE(fec_finalx) = '".date('Y-m-d',strtotime($_REQUEST['end']))."'";

      $consulta = new Consulta( $mSql, $this -> conexion );
      $agendado = $consulta->ret_matrix('a');

      if(count($agendado) > 0){
        foreach ($agendado as $ident => $data) {
          $estado = 0;
          if(strtotime($_REQUEST['start']) == strtotime($data['fec_inicio']) OR 
            strtotime($_REQUEST['end']) == strtotime($data['fec_finalx'])) {
            $estado = 1;
          }elseif (strtotime($_REQUEST['start']) < strtotime($data['fec_inicio']) AND 
            strtotime($_REQUEST['end']) > strtotime($data['fec_finalx'])) {
            $estado = 1;
          }elseif (strtotime($_REQUEST['start']) < strtotime($data['fec_inicio']) AND 
            (strtotime($_REQUEST['end']) > strtotime($data['fec_inicio']) AND strtotime($_REQUEST['end']) < strtotime($data['fec_finalx']))) {
            $estado = 1;
          }elseif ((strtotime($_REQUEST['start']) > strtotime($data['fec_inicio']) AND strtotime($_REQUEST['start']) < strtotime($data['fec_finalx'])) AND 
            (strtotime($_REQUEST['end']) > strtotime($data['fec_finalx']))) {
            $estado = 1;
          }

          if($estado == 1){
            $respuesta["status"] = 3;
            $respuesta["title"] = "Oh!";
            $respuesta["text"] = "El rango de fechas ya esta ocupado por otro pedido.";
            $respuesta["type"] = "warning";

            echo json_encode($respuesta);
            die();
          }
          
        }
      }
        
      $mSql = "SELECT a.cod_pedido
                FROM  ".BASE_DATOS.". tab_agenda_pedido a
               WHERE  a.cod_pedido = ".$_REQUEST['id'];

      $consulta = new Consulta( $mSql, $this -> conexion );
      $cod_pedido = $consulta->ret_matriz();
      
      if(count($cod_pedido) > 0){
        $mSql = "UPDATE ".BASE_DATOS.".tab_agenda_pedido 
                    SET fec_inicio = '".$_REQUEST['start']."',
                        fec_finalx = '".$_REQUEST['end']."',
                        cod_colorx = '".$_REQUEST['cod_colorx']."',
                        usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."', 
                        fec_modifi = NOW()
               WHERE  cod_pedido = ".$_REQUEST['id'];

        $consulta = new Consulta($mSql, $this -> conexion,"R");

        $respuesta["status"] = 2;
        $respuesta["title"] = "Agendamiento Actualizado!";
        $respuesta["text"] = "Este pedido ha sido actualizado exitosamente.";

      }else{
         $mSql = "INSERT INTO ".BASE_DATOS.".tab_agenda_pedido 
                               (cod_pedido, num_lineax,fec_inicio, fec_finalx, tip_pedido,
                               cod_colorx, usr_creaci, fec_creaci)
                       VALUES  (".$_REQUEST['id'].", '".$_REQUEST['num_lineax']."','".$_REQUEST['start']."', '".$_REQUEST['end']."', 1,
                                '".$_REQUEST['cod_colorx']."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW())";

         $consulta = new Consulta($mSql, $this -> conexion,"R");
         
         $sql = "SELECT a.cod_bahia
                    FROM ".BASE_DATOS.".tab_agenda_cliret a
                    WHERE a.fec_inicio >= '".$_REQUEST['start']."'
                      AND a.fec_finalx >= '".$_REQUEST['end']."'
                      LIMIT 1;";

         $consulta = new Consulta( $sql, $this -> conexion );
         $bahia = $consulta->ret_arreglo();
      
         $sql = "SELECT a.id
                  FROM ".BASE_DATOS.".tab_listad_planea a
                WHERE a.fec_planea = '".$_REQUEST['start']."';";

         $consulta = new Consulta( $sql, $this -> conexion );
         $planea = $consulta->ret_arreglo();

        if(empty($planea)){
          $mSql = "INSERT INTO ".BASE_DATOS.".tab_listad_planea 
                              (fec_planea, ind_estado, usr_creaci, fec_creaci)
                      VALUES  ('".$_REQUEST['start']."', 1, '".$_SESSION['datos_usuario']['cod_usuari']."', NOW())";

          $consulta = new Consulta($mSql, $this -> conexion,"R"); 

          $sql = "SELECT a.id
                  FROM ".BASE_DATOS.".tab_listad_planea a
                WHERE a.fec_planea = '".$_REQUEST['start']."';";

          $consulta = new Consulta( $sql, $this -> conexion );
          $planea = $consulta->ret_arreglo();

          $mSql = "INSERT INTO ".BASE_DATOS.".tab_agrupa_pedido 
                                (num_pedido, num_lineax, cod_planea, ind_estado, usr_creaci, fec_creaci)
                        VALUES  (".$_REQUEST['id'].",'".$_REQUEST['num_lineax']."' ,'".$planea['id']."', 0, 
                                '".$_SESSION['datos_usuario']['cod_usuari']."', NOW())";

        }else{
          $mSql = "INSERT INTO ".BASE_DATOS.".tab_agrupa_pedido 
                              (num_pedido, num_lineax, cod_planea, ind_estado, usr_creaci, fec_creaci)
                      VALUES  (".$_REQUEST['id'].",'".$_REQUEST['num_lineax']."' ,'".$planea['id']."', 0, 
                                '".$_SESSION['datos_usuario']['cod_usuari']."', NOW())";
        }

        $consulta = new Consulta($mSql, $this -> conexion,"R");

        $cargue = explode(' ', $_REQUEST['start']);

        $mSql = "UPDATE ".BASE_DATOS.".tab_genera_pedido 
                  SET ind_estado = 2,
                      usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."', 
                      fec_modifi = NOW(),
                      bahia_cargue = '".$bahia['cod_bahia']."',
                      fec_cargue = '".$cargue[0]."',
                      hor_cargue = '".$cargue[1]."',
                      aut_retira = '2'
            WHERE  num_pedido = ".$_REQUEST['id']." AND
                    num_lineax = ".$_REQUEST['num_lineax'];

        $consulta = new Consulta($mSql, $this -> conexion,"R");

         $respuesta["status"] = 1;
         $respuesta["title"] = "Agendamiento Creado!";
         $respuesta["text"] = "Este pedido ha sido generado exitosamente.";
      }

      $data = [
                "id" => $_REQUEST['id'],
                "title" => $_REQUEST['id'],
                "start" => $_REQUEST['start'],
                "end" => $_REQUEST['end'],
                "backgroundColor" => $_REQUEST['cod_colorx'],
                "borderColor" => $_REQUEST['cod_colorx'],
                "textColor" => "#ffffff"
              ];   

      $respuesta["type"] = "success";
      $respuesta["data"] = $data;

      echo json_encode($respuesta);
  }

  /*! \fn: creEditAgendPediCl
  *  \brief: Metodo que consulta y genera la actualizacion o registro del agendamiento de un pedido Cliente retira
  *  \author: Ing. Luis Manrique
  *  \date: 20/02/202                                                    
  *  \return n/a
  */  
  function creEditAgendPediCl(){

    //Se divide las fechas inicial y final
    if(isset($_REQUEST['hor_apert_hor_cierre-cl'])){
      $horario = explode(" - ", $_REQUEST['hor_apert_hor_cierre-cl']);
      $_REQUEST['start'] = strtotime($horario[0]);
      $_REQUEST['end'] = strtotime($horario[1]);
    }

    //Variable de respuesta
    $respuesta = [];
    $fec_inicia = date("Y-m-d", $_REQUEST['start']);
    $fec_finalx = date("Y-m-d", $_REQUEST['end']);
    $hora_inicia = date("H:i:s", $_REQUEST['start']);
    $hora_finalx = date("H:i:s", $_REQUEST['end']);
    //hora de la bahia
    $hor_inibah = $this->darHorlabBah($_REQUEST['cod_bahia'],$fec_inicia)[0]['hor_ingres'];
    $hor_finbah = $this->darHorlabBah($_REQUEST['cod_bahia'],$fec_finalx)[0]['hor_salida'];

    //validaciones del horario de la bahia
    $v3=$this->dentro_de_horario($hor_inibah,$hor_finbah,$hora_inicia);
    $v4=$this->dentro_de_horario($hor_inibah,$hor_finbah,$hora_finalx);

    if($v3 AND $v4){
    for($i=$_REQUEST['start']; $i<=$_REQUEST['end']; $i+=86400){
      //Consulta para validar si ya est� programado 
      $mSql = "INSERT INTO ".BASE_DATOS.".tab_agenda_cliret 
                              (fec_inicio, fec_finalx, cod_bahia,
                              usr_creaci, fec_creaci)
                      VALUES ('".date("Y-m-d", $i)." ".date("H:i:s", $_REQUEST['start'])."', '".date("Y-m-d", $i)." ".date("H:i:s", $_REQUEST['end'])."', ".$_REQUEST['cod_bahia'].",'".$_SESSION['datos_usuario']['cod_usuari']."', NOW())";
      $consulta = new Consulta($mSql, $this -> conexion,"R");    
    }
      $respuesta["status"] = 1;
      $respuesta["title"] = "Agendamiento generado!";
      $respuesta["text"] = "Este rango de fechas ha sido generado exitosamente.";
      $respuesta["type"] = "success";
    }else{
      $respuesta["status"] = 0;
      $respuesta["title"] = "Error";
      $respuesta["text"] = "El rango seleccionado se encuentra fuera del funcionamiento de la bahia";
      $respuesta["type"] = "error";
    }
    echo json_encode($respuesta);
  }

  private function infoUsuari( $cod_usuari = NULL )
  {
    
    $query = "SELECT a.cod_consec, a.cod_usuari, a.nom_usuari, a.cod_perfil
                FROM ".BASE_DATOS.".tab_genera_usuari a
                
              WHERE a.cod_usuari = '".$cod_usuari."'";
    
    
    $consulta = new Consulta( $query, $this->conexion );
    $mUsiari = $consulta->ret_matriz('a');
    return $mUsiari;
  
  }


    /*! \fn: viewAgenda
  *  \brief: Metodo que consulta y genera la visualizaci�n de los agendamientos
  *  \author: Ing. Luis Manrique
  *  \date: 23/12/2019    
  *  \return json
  */  
  function viewAgenda(){

      $filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica, COD_FILTRO_REMDES, $this -> cod_usuari );

      $info_usuari= $this->infoUsuari($_SESSION['datos_usuario']['cod_usuari']);
      $cond = "";
      // print_r($info_usuari[0]['cod_perfil']);
      // print_r(COD_PERFIL_ADMINIST);
      // print_r(COD_PERFIL_SUPEREAL);
      // die;
      if($info_usuari[0]['cod_perfil'] != COD_PERFIL_ADMINIST && $info_usuari[0]['cod_perfil'] != COD_PERFIL_SUPEFARO ){
        $cond = " WHERE a.cod_usuari= '".$_SESSION['datos_usuario']['cod_usuari']."' ";
      }

      $mSql="SELECT a.cod_protur,
                    a.cod_usuari,
                    CONCAT ('(',UPPER (a.cod_usuari), ', ', UPPER (c.cod_usuari) ,')') AS dat_client,
                    CONCAT(a.fec_inicia,' ', IF(a.hor_inicia IS NULL, b.hor_inicia,a.hor_inicia)) AS fec_inicia,
                    CONCAT(a.fec_finalx,' ', IF(a.hor_finalx IS NULL, b.hor_finalx,a.hor_finalx)) AS fec_finalx,
                    b.cod_colorx
              FROM  ".BASE_DATOS.". tab_progra_turnos a
              INNER JOIN  ".BASE_DATOS.". tab_config_horari b
                    ON  a.cod_horari = b.cod_horari
              INNER JOIN  ".BASE_DATOS.". tab_genera_usuari c
                    ON  a.cod_usuari = c.cod_usuari
                    ".$cond."

      ";

  
      $consulta = new Consulta( $mSql, $this -> conexion );
      $agendamientos = $consulta->ret_matrix('a');

      $agenda = [];
      $cont = 0;
      foreach ($agendamientos as $key => $value) {
        if($filtro -> listar($this -> conexion)>0){
          $datos_filtro = $filtro -> dar_filtro_multiple($this -> conexion);
          if($this->busquedaArray($datos_filtro,$value["cod_usuari"])){
              $agenda[$key]["title"] = $value["dat_client"];
              $agenda[$key]["backgroundColor"] = $value["cod_colorx"];
              $agenda[$key]["borderColor"] = $value["cod_colorx"];
          }else{
              $agenda[$key]["title"] = "AGENDADA ";
              $agenda[$key]["backgroundColor"] = "rgb(0, 100, 255)";
              $agenda[$key]["borderColor"] = "rgb(0, 100, 255)";
          }

          $agenda[$key]["cliente"] = $value["cod_usuari"];
          $agenda[$key]["id"] = $value["cod_protur"];
          $agenda[$key]["start"] = $value["fec_inicia"];
          $agenda[$key]["end"] = $value["fec_finalx"];
          $agenda[$key]["textColor"] = "#ffffff";
          $cont++;
         
        }else{
          $agenda[$key]["title"] = $value["dat_client"];
          $agenda[$key]["backgroundColor"] = $value["cod_colorx"];
          $agenda[$key]["cliente"] = $value["cod_usuari"];
          $agenda[$key]["id"] = $value["cod_protur"];
          $agenda[$key]["start"] = $value["fec_inicia"];
          $agenda[$key]["end"] = $value["fec_finalx"];
          $agenda[$key]["borderColor"] = $value["cod_colorx"];
          $agenda[$key]["textColor"] = "#ffffff";
          $cont++;
        }  
      }

      

      echo json_encode($agenda);
  }

  /*! \fn: busquedaArray
  *  \brief: Metodo que consulta un valor en un array dado
  *  \author: Ing. Cristian Torres
  *  \date: 20/11/2020    
  *  \return boolean
  */  
  public function busquedaArray($array1,$valor){
    foreach($array1 as $arreglo){
      if($arreglo['clv_filtro']==$valor){
        return true;
      }
    }
    return false;
  }

  protected function listCodAgenda(){

    $filtro = new Aplica_Filtro_Usuari( $this -> cod_aplica, COD_FILTRO_REMDES, $this -> cod_usuari );
    $query="";
    
      if($filtro -> listar($this -> conexion)>0){
        $datos_filtro = $filtro -> dar_filtro_multiple($this -> conexion);
        $ultima = count($datos_filtro)-1;
        $query .= " AND a.cod_usuari IN (";
        foreach($datos_filtro as $key => $value){
          $query .= " ".$value['clv_filtro'];
          if($key!=$ultima){
            $query .= ", ";  
          }
        }
        $query .= ") ";
      }

    $mSql = "SELECT a.num_pedido,  
                    CONCAT ('(',UPPER (a.nit_cliente), ', ', UPPER (a.nombre_cliente) ,')') AS dat_client,
                    a.nombre_cliente,
                    b.cod_colorx,
                    a.num_lineax,
                    if(
                    	b.fec_inicio IS NOT NULL,
                    	CONCAT(SUBSTRING(b.fec_inicio, 1, 16),' - ',SUBSTRING(b.fec_finalx, 1, 16)), 
                    	''
                	) AS hor_apert_hor_cierre
              FROM ".BASE_DATOS.".tab_genera_pedido a
         LEFT JOIN ".BASE_DATOS.".tab_agenda_pedido b
                ON  a.num_pedido = b.cod_pedido
                AND a.num_lineax = b.num_lineax
             WHERE  a.d_tipo_pedido IN(10,16) AND CONCAT ( UPPER (a.num_pedido),'(',UPPER (a.nit_cliente), ', ', UPPER (a.nombre_cliente) ,')') LIKE '%". $_REQUEST['term'] ."%'
             ".$query."
             AND a.aut_retira=1
          ORDER BY  2";



	$consulta = new Consulta( $mSql, $this->conexion );
	$codPedido = $consulta->ret_matriz();

	$data = [];
    
	foreach ($codPedido as $key => $value) {
		$data[$key]['label'] = $value['num_pedido']." - ".$value['dat_client'];
		$data[$key]['value'] = $value['num_pedido'];
		$data[$key]['id'] = $value['num_pedido'];
		$data[$key]['nom'] = $value['nombre_cliente'];
		$data[$key]['fec'] = $value['hor_apert_hor_cierre'];
		$data[$key]['col'] = $value['cod_colorx'];
    $data[$key]['lin'] = $value['num_lineax'];
	}

	echo json_encode($data);
  }


  /*! \fn: form_mail
  *  \brief: Metodo que genera la funcionalidad de crear correos
  *  \author: Ing. Luis Manrique
  *  \date: 23/10/2019
  *  \return n/a
  */ 

  function form_mail($sPara, $sParaN, $sAsunto, $sCuerpo, $sDe, $sDeN){ 
    //Variables necesarias
    $bHayFicheros = 0; 
    $sCabeceraTexto = ""; 
    $sAdjuntos = "";
    $adjuntos = array();
    $sTexto = "";

    //Formatea el campo Files organizando los archivos correctamente
    if(!empty($_FILES['tar_propie']['name'])){
      foreach ($_FILES as $key => $value) {
        foreach ($value as $key1 => $value1) {
          foreach ($value1 as $key2 => $value2) {
            $adjuntos[$key][$key2][$key1]= $value2;
          }
        }
      }
    }

    if ($sDe)$sCabeceras = "From:".$sDeN."<".$sDe.">" . "\r\n";
    if ($sPara)$sCabeceras .= 'To: '.$sParaN.' <'.$sPara.'>' . "\r\n";
    else $sCabeceras = ""; 
    
    $sCabeceras .= 'MIME-Version: 2.0' . "\r\n";
    $sTexto = $sCuerpo;

    $sCabeceras .= "Content-type: multipart/mixed;"; 
    $sCabeceras .= "boundary=\"--_Separador-de-mensajes_--\"\n";
    
    $sCabeceraTexto = "----_Separador-de-mensajes_--\n";
    $sCabeceraTexto .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $sCabeceraTexto .= "Content-transfer-encoding: 7BIT\n";
    
    $sTexto = $sCabeceraTexto.$sTexto;
    if(!empty($_FILES['tar_propie']['name'])){
        foreach ($adjuntos as $key => $zAdjunto){
          foreach ($zAdjunto as $vAdjunto) {
            if ($bHayFicheros == 0){
              $bHayFicheros = 1;
            }
          
          if ($vAdjunto["size"] > 0){
            $sAdjuntos .= "\n\n----_Separador-de-mensajes_--\n";
            $sAdjuntos .= "Content-type: ".$vAdjunto["type"].";name=\"".$vAdjunto["name"]."\"\n";;
            $sAdjuntos .= "Content-Transfer-Encoding: BASE64\n";
            $sAdjuntos .= "Content-disposition: attachment;filename=\"".$vAdjunto["name"]."\"\n\n";
            
            $oFichero = fopen($vAdjunto["tmp_name"], 'r');
            $sContenido = fread($oFichero, filesize($vAdjunto["tmp_name"])); 
            $sAdjuntos .= chunk_split(base64_encode($sContenido));
            fclose($oFichero);
          }
        }
        
          if ($bHayFicheros)
            $sTexto .= $sAdjuntos."\n\n----_Separador-de-mensajes_----\n"; 
        }
    }
    return(mail($sPara, $sAsunto, $sTexto, $sCabeceras));
  }


  /*! \fn: sendMail
  *  \brief: Envia la solicitud correspondiente mediante el correo
  *  \author: Ing. Luis Manrique
  *  \date: 23/10/2019    
  *  \return boolean
  */  
  function sendMail()
  {
    //Informaci�n de la empresa solicitante
    $mSql = "SELECT b.nom_perfil,
                    d.dir_emailx
              FROM  ".BASE_DATOS.".tab_genera_usuari a
        INNER JOIN  ".BASE_DATOS.".tab_genera_perfil b
                ON  a.cod_perfil = b.cod_perfil
        INNER JOIN  ".BASE_DATOS.".tab_aplica_filtro_perfil c
                ON  a.cod_perfil = c.cod_perfil
        INNER JOIN  ".BASE_DATOS.".tab_tercer_tercer d
                ON  c.clv_filtro = d.cod_tercer
             WHERE  a.cod_usuari = '".$_SESSION["datos_usuario"]["cod_usuari"]."'
                  ";
                  
    $consulta = new Consulta( $mSql, $this -> conexion );
    $datClient = $consulta->ret_matriz();

    //Valida si exite nombre o correo de la empresa
    if($datClient == '' || $datClient[0][0] == '' || $datClient[0][1] == ''){
      echo 2;
      die();
    }

    foreach ($datClient as $key => $value) {
      $nom_empres = $value[0];
      $dir_emassr = $value[1];
    }

    //Creando Variables automaticas
    foreach ($_REQUEST as $nombreCampo => $valorCampo) {
      ${$nombreCampo} = $valorCampo;
    }

    //Valiables enviadas por correo
    $http = $_SERVER['HTTPS'] = "ON" ? "https://" : "http://";
    $serverName = $_SERVER['HTTP_HOST'];
    $fecha = date("d/m/y h:i:s");
    $asunto = "NOTIFICACI�N SOLICITUD SEGURO SOAT";

    //Generar plantilla
    $thefile = implode("", file('sol_seguro_soatpl.html'));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"" . $thefile . "\";";

    eval($thefile);
    $sTexto = $r_file;

    //Cambiar aqui el email 
    if ($this -> form_mail("asistencias@faro.com.co", "Asistencia Logistica", $asunto, $sTexto, $dir_emassr, $nom_empres)) 
      echo 1;
    else
      echo 0;
  }



  /*! \fn: busquedaFranja
  *  \brief: Metodo que consulta y retorna el html con las franjas programadas en base al dia seleccionado
  *  \author: Ing. Cristian Torres
  *  \date: 22/04/2020                                                    
  *  \return n/a
  */  

  function busquedaFranja(){
    $fecha_actual = date("Y-m-d H:i:s");
    $fecha_busqueda=$_REQUEST['fechaBusqueda'];

    $fecha_inicia=$fecha_busqueda . " 00:00:00";
    $fecha_finalx=$fecha_busqueda . " 23:59:59";

    $html='';
    $mSql="SELECT 
            a.fec_inicio, 
            a.fec_finalx,
            CAST(a.fec_inicio as TIME) as 'hor_inicio',
            CAST(a.fec_finalx as TIME) as 'hor_finalx',
            a.cod_bahia
          FROM 
            tab_agenda_cliret a 
           WHERE 
            a.fec_inicio BETWEEN '$fecha_inicia' 
          AND '$fecha_finalx' 
          AND a.fec_finalx BETWEEN '$fecha_inicia' 
          AND '$fecha_finalx'
          AND a.fec_finalx > '$fecha_actual'
          ";
    $consulta = new Consulta( $mSql, $this->conexion );
    $registros = $consulta->ret_matriz(); 
    
    foreach($registros as $registro){
      $vista=$fecha_busqueda." (".$registro['hor_inicio']." - ".$registro['hor_finalx'].")";
      $value=$registro['fec_inicio']."_".$registro['fec_finalx']."_".$registro['cod_bahia'];
      $html.="<option value='".$value."'>".$vista."</option>";
    }

    $respuestas=[];
    $respuesta["valores"] = $html;
    echo json_encode($respuesta);

  }

    /*! \fn: busquedaHora
  *  \brief: Metodo que consulta y retorna la confirmacion de las horas disponibles del agendamiento.
  *  \author: Ing. Cristian Torres
  *  \date: 22/04/2020                                                    
  *  \return n/a
  */  

  function busquedaHora(){
    //Informacion enviada por ajax

    $cod_usuari= $_REQUEST['cod_usuari'];
    $horario = explode(" - ", $_REQUEST['hor_apert_hor_cierre-cl']);
    $cod_horari= $_REQUEST['cod_horari'];
    $cod_noveda= $_REQUEST['cod_noveda'];
    $observa= $_REQUEST['observatext'];
    
    
    $fec_inicia=date('Y-m-d',strtotime ($horario[0]));
    $fec_finalx=date('Y-m-d',strtotime ($horario[1]));

    $dataHorari=$this->getHorari($cod_horari);
    $hor_inihor=$dataHorari[0]['hor_inicia'];
    $hor_finhor=$dataHorari[0]['hor_finalx'];
    

    $respuesta['fec_inicio']=$fec_inicia;
    $respuesta['fec_final']=$fec_finalx;
    $respuesta['inicio']=$hor_inihor;
    $respuesta['final']=$hor_finhor;
    $respuesta['usuari']=$cod_usuari;
    /*$num_pedido= $_REQUEST['cod_pedido'];
    $num_lineax= $_REQUEST['num_lineax'];
    $hor_franja= $_REQUEST['FranjasID'];
    $horario = explode("_", $hor_franja);*/

    //Horas de la franja

    $respuestas=[];

    //Hora disponible para agendar
    //$hora_inicia=$this->retornaHoraFinal($fec_inicia,$fec_finalx);
    //Tiempo de cargue en minutos
    //$tiempo_cargue=$this->darTiempoCargue($bahia,$num_pedido,$num_lineax);
    //$hora_finalx = date ( 'H:i:s' , strtotime ( '+'.$tiempo_cargue.' minute' , strtotime ($hora_inicia)));

    //validaciones de la hora
    //$v1=$this->dentro_de_horario($hor_inicia,$hor_finalx,$hora_inicia);
    //$v2=$this->dentro_de_horario($hor_inicia,$hor_finalx,$hora_finalx);


    //hora de la bahia
    //$hor_inibah = $this->darHorlabBah($bahia,$fec_inicia)[0]['hor_ingres'];
    //$hor_finbah = $this->darHorlabBah($bahia,$fec_finalx)[0]['hor_salida'];

    //validaciones del horario de la bahia
    //$v3=$this->dentro_de_horario($hor_inibah,$hor_finbah,$hora_inicia);
    //$v4=$this->dentro_de_horario($hor_inibah,$hor_finbah,$hora_finalx);

    //$respuesta['estado']=2;
    
    //if($v3 AND $v4){
      //$respuesta['estado']=0;
      //$respuesta['comparacion_prueba']=$hora_finalx;
      //if($v1 AND $v2){
      
        
      //}
    //}

    echo json_encode($respuesta);
  }

  private function getHorari( $cod_horari )
    {
      
      $query = "SELECT a.cod_horari, a.nom_horari, hor_inicia, hor_finalx
                  FROM ".BASE_DATOS.".tab_config_horari a
                WHERE a.cod_horari =" .$cod_horari."";
      
      $consulta = new Consulta( $query, $this->conexion );
      $mHorari = $consulta->ret_matriz('a');
      return $mHorari;
    
    }

  function darHorlabBah($cod_bahia,$fecha){
    $dia = $this->darDiaSemana($fecha);
    $sql = "SELECT hor_ingres, hor_salida
                  FROM ".BASE_DATOS.".tab_config_horbah a
                WHERE a.cod_bahia = '".$cod_bahia."'
                AND a.com_diasxx LIKE '%".$dia."%'";

    $consulta = new Consulta( $sql, $this->conexion );
    $registros = $consulta->ret_matriz();
    return $registros;
  }

  function darDiaSemana($fecha) {
    $dias = array('D', 'L', 'M', 'X', 'J', 'V', 'S');
    $fecha = $dias[date('w', strtotime($fecha))];
    return $fecha;
  }

     /*! \fn: registraCitacion
  *  \brief: Metodo que registra las hora confirmada del agendamiento a la base de datos
  *  \author: Ing. Cristian Torres
  *  \date: 22/04/2020                                                    
  *  \return n/a
  */  
  function registraCitacion(){

    $cod_usuari= $_REQUEST['cod_usuari'];
    $horario = explode(" - ", $_REQUEST['hor_apert_hor_cierre-cl']);
    $cod_horari= $_REQUEST['cod_horari'];
    $cod_noveda= $_REQUEST['cod_noveda'];
    
    
    $observa= $_REQUEST['observatext'];
    $dataHorari=$this->getHorari($cod_horari);
    $hor_inihor=$dataHorari[0]['hor_inicia'];
    $hor_finhor=$dataHorari[0]['hor_finalx'];

    $fec_inicia=date('Y-m-d',strtotime ($horario[0]));
    $fec_finalx=date('Y-m-d',strtotime ($horario[1]));

    $fec_aumenta = $fec_inicia;

    while ($fec_aumenta <= $fec_finalx) {
      $this->registraCita($cod_usuari,$fec_aumenta, $fec_aumenta, $hor_inihor,$hor_finhor,$cod_horari,$cod_noveda,$observa);
      $fec_aumenta=date("Y-m-d",strtotime($fec_aumenta."+ 1 days"));
    }
    
    $respuesta['estado']=1;
    echo json_encode($respuesta);

  
    
  }
    /*$num_pedido= $_REQUEST['usuariID'];
    echo($num_pedido);
    $num_lineax= $_REQUEST['num_lineax'];
    $hor_franja= $_REQUEST['FranjasID'];
    $horario = explode("_", $hor_franja);
    */
    //Horas de la franja
    /*$fec_inicia=$horario[0];
    $hor_inicia=date('H:i:s',strtotime ($fec_inicia));
    $fec_finalx=$horario[1];
    $hor_finalx=date('H:i:s',strtotime ($fec_finalx));
    $fec_dia=date('Y-m-d',strtotime($horario[0]));
    $bahia=$horario[2];
    */
    //$respuestas=[];
    /*$hora_inicia=$this->retornaHoraFinal($fec_inicia,$fec_finalx);
    $tiempo_cargue=$this->darTiempoCargue($bahia,$num_pedido,$num_lineax);
    $hora_finalx = date ( 'H:i:s' , strtotime ( '+'.$tiempo_cargue.' minute' , strtotime ($hora_inicia)));
    */
    //validaciones de la hora
    /*$v1=$this->dentro_de_horario($hor_inicia,$hor_finalx,$hora_inicia);
    $v2=$this->dentro_de_horario($hor_inicia,$hor_finalx,$hora_finalx);
*/
    
    //hora de la bahia
    /*$hor_inibah = $this->darHorlabBah($bahia,$fec_inicia)[0]['hor_ingres'];
    $hor_finbah = $this->darHorlabBah($bahia,$fec_finalx)[0]['hor_salida'];
*/
    //validaciones del horario de la bahia
    /*$v3=$this->dentro_de_horario($hor_inibah,$hor_finbah,$hora_inicia);
    $v4=$this->dentro_de_horario($hor_inibah,$hor_finbah,$hora_finalx);
*/
    //$respuesta['estado']=2;
    //if($v3 AND $v4){
      //$respuesta['estado']=0;
      //if($v1 AND $v2){
      
      //Validacion disponiblidad de vehiculo.
      /*if($this->validarDisponibilidadVehiculo($num_pedido,$num_lineax,$fec_dia)){
        $this->registraCita($fec_dia, $hora_inicia, $hora_finalx,$num_pedido,$num_lineax,$bahia);
        $respuesta['estado']=1;
      }else{
        $respuesta['estado']=5;
      }
      */
        
      //}
    //}
    //echo json_encode($respuesta);*/
  //}

      /*! \fn: registraCita
  *  \brief: Recibe los datos y hace la insercion y registro en las tablas de la base de datos con la informacion de la cita del agendamiento
  *  \author: Ing. Cristian Torres
  *  \date: 22/04/2020                                                    
  *  \return n/a
  */  
  function registraCita($cod_usuari,$fec_inicia, $fec_finalx, $hor_inihor,$hor_finhor,$cod_horari,$cod_noveda,$observa){

    $sql="INSERT INTO ".BASE_DATOS.".tab_progra_turnos 
            (cod_usuari, cod_horari,cod_novtur, fec_inicia, fec_finalx,
            hor_inicia, hor_finalx, obs_protur, usr_creaci, fec_creaci)
          VALUES  ('".$cod_usuari."', '".$cod_horari."','".$cod_noveda."', '".$fec_inicia."', '".$fec_finalx."',
          '".$hor_inihor."', '".$hor_finhor."', '".$observa."', '".$_SESSION['datos_usuario']['cod_usuari']."', NOW())";
    new Consulta( $sql, $this->conexion, "R" );
    
    //$num_planea=$this->darPlaneacion($fecha);

    //Revisa el status actual de la lista de planeacion devuelve true si esta activa y false si esta inactiva
    //$status=$this->verStatusPlaneacion($num_planea);

    //En caso del status ser false, reactiva la planeacion cambiando su status a 1 en la tabla
    /*if(!$status){
        $this->activarPlaneacion($num_planea);
    }*/

    /*$mSql = "INSERT INTO ".BASE_DATOS.".tab_agrupa_pedido 
                                (num_pedido, num_lineax, cod_planea, ind_estado, usr_creaci, fec_creaci)
                        VALUES  (".$num_pedido.",'".$num_lineax."' ,'".$num_planea."', 0, 
                                '".$_SESSION['datos_usuario']['cod_usuari']."', NOW())";
    new Consulta( $mSql, $this->conexion, "R" );

    $mSql = "UPDATE ".BASE_DATOS.".tab_genera_pedido 
                  SET ind_estado = 2,
                      usr_modifi = '".$_SESSION['datos_usuario']['cod_usuari']."', 
                      fec_modifi = NOW(),
                      bahia_cargue = '".$bahia."',
                      fec_cargue = '".$fecha."',
                      hor_cargue = '".$hora_inicial."',
                      hor_fcargu = '".$hora_finalx."',
                      aut_retira = '2'
            WHERE  num_pedido = '".$num_pedido."' AND
                    num_lineax = ".$num_lineax;
    new Consulta($mSql, $this -> conexion,"R");
    */
  }

    /*! \fn: darPlaneacion
  *  \brief: registra o consulta el codigo de la planeacion en base a la fecha que se le da a la funcion como parametro
  *  \author: Ing. Cristian Torres
  *  \date: 22/04/2020                                                    
  *  \return codigo de planeacion
  */  

  function darPlaneacion($fecha){
    $sql = "SELECT count(a.id) as 'cantidad'
                  FROM ".BASE_DATOS.".tab_listad_planea a
                WHERE a.fec_planea = '".$fecha."';";

    $consulta = new Consulta( $sql, $this->conexion );
    $registros = $consulta->ret_matriz();
    $id_planea='';
    if($registros[0]['cantidad']==0){

      $mSql = "INSERT INTO ".BASE_DATOS.".tab_listad_planea 
                              (fec_planea, ind_estado, usr_creaci, fec_creaci)
                      VALUES  ('".$fecha."', 1, '".$_SESSION['datos_usuario']['cod_usuari']."', NOW())";
      new Consulta($mSql, $this -> conexion,"R");

      $sql = "SELECT a.id FROM ".BASE_DATOS.".tab_listad_planea a
                WHERE a.fec_planea = '".$fecha."';";
      $consulta = new Consulta( $sql, $this->conexion );

      $registros = $consulta->ret_matriz();
      $id_planea=$registros[0]['id'];

    }else{
      $mSql = "SELECT a.id FROM ".BASE_DATOS.".tab_listad_planea a
                WHERE a.fec_planea = '".$fecha."';";
      $consulta = new Consulta( $mSql, $this->conexion );
      $registros = $consulta->ret_matriz();
      $id_planea=$registros[0]['id'];
    }

    return $id_planea;
  }

  /*! \fn: verStatusPlaneacion
  *  \brief: Consulta el status actual de la planeacion devuelve true si esta activa = 1 o false si esta inactiva = 0
  *  \author: Ing. Cristian Torres
  *  \date: 28/07/2020                                                    
  *  \return boolean
  */ 
  function verStatusPlaneacion($cod_planea){
    $sql="SELECT a.ind_estado FROM ".BASE_DATOS.".tab_listad_planea a WHERE a.id = $cod_planea";
    $consulta = new Consulta($sql, $this->conexion);
    $consulta = $consulta->ret_matriz();
    $status = $consulta[0][0];
    if($status){
        return true;
    }else{
        return false;
    }
  }

  /*! \fn: activarPlaneacion
  *  \brief: Cambia el status de la planeacion a 1
  *  \author: Ing. Cristian Torres
  *  \date: 28/07/2020                                                    
  *  \return boolean
  */ 
  function activarPlaneacion($cod_planea){
    $sql="UPDATE ".BASE_DATOS.".tab_listad_planea SET ind_estado = '1' WHERE id = $cod_planea";
    $consulta = new Consulta($sql, $this->conexion);
    if($consulta){
        return true;
    }
    return false;
  }


    /*! \fn: retornaHoraFinal
  *  \brief: Consulta en el registro de agendamiento la hora en que estara disponible el sitio de carga para el base a esa hora calcular el agedamiento.
  *  \author: Ing. Cristian Torres
  *  \date: 22/04/2020                                                    
  *  \return hora libre
  */ 

  function retornaHoraFinal($fec_inicia,$fec_finalx){
    $hor_actual = date("H:i:s");
    $sql="SELECT 
            MAX(
              CAST(a.fec_finalx as TIME)
            ) as 'hor_librex',
            CAST('$fec_inicia' as TIME) as 'hor_inicix'
          FROM 
            tab_agenda_pedido a 
          WHERE 
            a.fec_inicio BETWEEN '$fec_inicia' 
          AND '$fec_finalx' 
          AND a.fec_finalx BETWEEN '$fec_inicia' 
          AND '$fec_finalx' 
          AND a.tip_pedido = 1 
          ORDER BY 
            a.fec_inicio ASC";
    $consulta = new Consulta( $sql, $this->conexion );
    $registros = $consulta->ret_matriz();

    if($registros[0]['hor_librex'] == NULL || $registros[0]['hor_librex'] == ''){
      return $this->compararHorayFechaActual($fec_inicia,$registros[0]['hor_inicix']);
    }
    return $this->compararHorayFechaActual($fec_inicia,$registros[0]['hor_librex']);
  }

  function compararHorayFechaActual($fec_inicio,$hor_inicio){
    $fec_inicio=date('Y-m-d',strtotime ($fec_inicio));
    $fec_hoy = date("Y-m-d");
    if($fec_hoy==$fec_inicio){
      $hor_actual = date("H:i:s");
      $hor_inicio = date("H:i:s",strtotime ($hor_inicio));
      if($hor_inicio<$hor_actual){
        return date( 'H:i:s' , strtotime ( '+1 minute' , strtotime ($hor_actual)));
      }
    }
   return $hor_inicio;
  }

     /*! \fn: darTiempoCargue
  *  \brief: Consulta el tiempo de cargue en base al vehiculo asignado al pedido
  *  \author: Ing. Cristian Torres
  *  \date: 22/04/2020                                                    
  *  \return tiempo de cargue
  */

  function darTiempoCargue($bahia,$num_pedido,$num_linea){
    $sql="SELECT 
    d.ind_tiecar 
  FROM 
    tab_genera_pedido a, 
    tab_vehicu_vehicu b, 
    tab_combin_config c, 
    tab_tiecar_vehicu d 
  WHERE 
    a.placa = b.num_placax 
    AND b.num_config = c.nom_config 
    AND c.id = d.cod_tipveh 
    AND a.cod_articu = d.cod_produc
    AND a.num_pedido = '$num_pedido' 
    AND a.num_lineax = '$num_linea'";

    if($bahia!='' || $bahia!=NULL){
      $sql.="AND d.cod_bahiax = '$bahia'";
    }
    
    $consulta = new Consulta( $sql, $this->conexion );
    $registros = $consulta->ret_matriz();

    if($registros[0]['ind_tiecar'] == NULL || $registros[0]['ind_tiecar']==''){
      return 30;
    }
    return $registros[0]['ind_tiecar'];
  }

     /*! \fn: dentro_de_horario
  *  \brief: Retorna un boolean en base al analisis si una hora dada esta entre una franja de dos horas
  *  \author: Ing. Cristian Torres
  *  \date: 22/04/2020                                                    
  *  \return boolean
  */
  function dentro_de_horario($hms_inicio, $hms_fin, $hms_referencia=NULL){ // v2011-06-21
    if( is_null($hms_referencia) ){
        $hms_referencia = date('G:i:s');
    }

    list($h, $m, $s) = array_pad(preg_split('/[^\d]+/', $hms_inicio), 3, 0);
    $s_inicio = 3600*$h + 60*$m + $s;

    list($h, $m, $s) = array_pad(preg_split('/[^\d]+/', $hms_fin), 3, 0);
    $s_fin = 3600*$h + 60*$m + $s;

    list($h, $m, $s) = array_pad(preg_split('/[^\d]+/', $hms_referencia), 3, 0);
    $s_referencia = 3600*$h + 60*$m + $s;

    if($s_inicio<=$s_fin){
        return $s_referencia>=$s_inicio && $s_referencia<=$s_fin;
    }else{
        return $s_referencia>=$s_inicio || $s_referencia<=$s_fin;
    }
  }


  function validarEliminacion(){
    $respuesta=[];
    $respuesta['status']=false;
    $cod_bahia = $_REQUEST['cod_bahia'];
    $fec_inicio = $_REQUEST['hor_inicio'];
    $fec_finalx = $_REQUEST['hor_finalx'];
    $sql="SELECT COUNT(*) FROM ".BASE_DATOS.".tab_agenda_pedido 
    WHERE (fec_inicio BETWEEN '$fec_inicio' AND '$fec_finalx') 
    AND (fec_finalx BETWEEN '$fec_inicio' AND '$fec_finalx') 
    AND tip_pedido = 1";
    $consulta = new Consulta( $sql, $this->conexion );
    $registros = $consulta->ret_matriz()[0][0];
    if($registros<1){
      $sql="DELETE FROM ".BASE_DATOS.".tab_agenda_cliret WHERE fec_inicio = '$fec_inicio' AND fec_finalx = '$fec_finalx' AND cod_bahia='$cod_bahia'";
      $consulta = new Consulta( $sql, $this->conexion );
      if($consulta){
        $respuesta['status']=true;
      }
    }
    echo json_encode($respuesta);
  }

  function validarDisponibilidadVehiculo($num_pedido,$num_linea,$fec_cargue){
    $sql="SELECT placa,num_despac FROM ".BASE_DATOS.".tab_genera_pedido WHERE num_pedido = '$num_pedido' AND num_lineax = '$num_linea';";
    $consulta = new Consulta($sql, $this -> conexion);
    $informacion = $consulta->ret_matrix('a')[0];
    $sql="SELECT COUNT(*) as 'total' FROM ".BASE_DATOS.".tab_genera_pedido WHERE placa = '".$informacion['placa']."' AND fec_cargue='".$fec_cargue."';";
    $consulta = new Consulta($sql, $this -> conexion);
    $total = $consulta->ret_matrix('a')[0];
    $total = $total['total'];
    //El vehiculo no esta agendado para ese dia.
    if($total<1){
      return true;
    }
     //El Vehiculo esta agendado pero ademas ya ha sido despachado puede agendarse.
    if(($total>=1) AND ($informacion['num_despac']!=0)){
      return true;
    }
    return false;
  }


}
    $solicitud = new ajaxCalendAgendamiento( NULL, $_REQUEST,$_SESSION['usuario_aplicacion']->cod_usuari,  NULL );
?>

