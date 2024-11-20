<?php
 /************************************************************************
  * @file despac.php                                                     *
  * @brief Servidor de Servicios (WebService server).                    *
  * @version 0.1                                                         *
  * @date 19 de Marzo de 2010                                          *
  * @author Carlos A. Mock-kow M.                                        *
  * @bug usa funciones que desapareceran en php6                         *
  ************************************************************************/

  //turn off the wsdl cache
  ini_set( "soap.wsdl_cache_enabled", "0" );

  //include_once( "/var/www/html/ap/interf/config/Config.kons.php" );       //Constantes generales.
  include_once( "/var/www/html/ap/interf/app/faro/Config.kons.php" );     //Constantes propias.
  include_once( "/var/www/html/ap/interf/lib/funtions/General.fnc.php" ); //Funciones generales.  
  include_once( "/var/www/html/ap/interf/app/faro/protoc.class.inc" );     //Constantes propias.
  include_once( "/var/www/html/ap/interf/lib/tracki.class.php");

  function setSoliciFile($fil_archiv){
    try{
      $t=array("code"=>"","error"=>"","status"=>false,"path"=>"");
      //$faro_file_path=DirSolici;//"/var/www/html/ap/satt_faro/files/solici/";
      $fn="";
      if(!empty($fil_archiv) && !empty($fil_archiv->tip_format) && sizeof($fil_archiv->bin_archiv) > 0){
        $fe=!empty($fil_archiv->tip_format) ? '.'.$fil_archiv->tip_format : $fil_archiv->tip_format;
        //$fn=$faro_file_path.md5(rand(1000,9999).time().$fe).$fe;
        $fn=md5(rand(1000,9999).time().$fe).$fe;
        $t["path"]=$fn;
        if(file_put_contents(DirSolici.$fn, base64_decode($fil_archiv->bin_archiv))){
          $t["status"]=true;
        }else{
          $t["code"]=1005;
          $t["error"]="Error de escritura";
        }
      }
      return json_decode(json_encode($t));
    }catch(Exception $e){
      return json_decode(json_encode(array("code"=>$e->getCode(),"error"=>$e->getMessage(),"status"=>false,"path"=>"")));
    }
  }

  function getSoliciDatsol($conn, $objSoliciDatosx){
    try{
      $sql =  "select * from ".BASE_DATOS.".tab_solici_datosx ".
              "where ".
              "cod_transp=$objSoliciDatosx->cod_transp and ".
              "nom_aplica=$objSoliciDatosx->nom_aplica and ".
              "cod_usrsol=$objSoliciDatosx->cod_usrsol and ".
              "nom_usrsol=$objSoliciDatosx->nom_usrsol";
      $conn -> ExecuteCons( $sql );
      //$nr=$conn -> RetNumRows();
      return $conn -> RetMatrix( "a" );
    }catch(Exception $e){

    }
  }
  function getNewCodSolici($conn){
    try{
      $sql =  "select max(cod_solici) as cod_solici from ".BASE_DATOS.".tab_solici_datosx having cod_solici is not null ";
      $conn -> ExecuteCons( $sql );
      $data = $conn -> RetMatrix( "a" );
      return sizeof($data)>0 ? $data[0]["cod_solici"]+1 : 1;
    }catch(Exception $e){
      return 0;
    }
  }
  function getNewNumSolici($conn){
    try{
      $sql =  "select max(num_solici) as num_solici from ".BASE_DATOS.".tab_solici_solici having num_solici is not null ";
      $conn -> ExecuteCons( $sql );
      $data = $conn -> RetMatrix( "a" );
      return sizeof($data)>0 ? $data[0]["num_solici"]+1 : 1;
    }catch(Exception $e){
      return 0;
    }
  }
  function validateRutSol($arr_rutsol){
    try{
      $errors=array();
      if(empty($arr_rutsol->cod_ciuori)){
        array_push($errors, "Ciudad origen requerida");
      }
      if(empty($arr_rutsol->cod_ciudes)){
        array_push($errors, "Ciudad destino requerida");
      }
      /*if(empty($arr_rutsol->nom_viaxxx)){
        array_push($errors, "Nombre de v&iacute;a requerido");
      }*/
      return $errors;
    }catch(Exception $e){
      return array($e->getMessage());
    }
  }
  function validateDatSol($arr_datsol){
    try{
      $errors=array();
      if(empty($arr_datsol->cod_transp)){
        array_push($errors, "NIT es requerido");
      }
      if(empty($arr_datsol->nom_aplica)){
        array_push($errors, "Nombre aplicaci&oacute;n es requerido");
      }
      if(empty($arr_datsol->cod_solici)){
        array_push($errors, "Codigo usuario o codigo de avansat es requerido");
      }
      if(empty($arr_datsol->nom_solici)){
        array_push($errors, "Nombre del solicitante es requerido");
      }
      if(!empty($arr_datsol->mai_solici)){
        if(!filter_var($arr_datsol->mai_solici, FILTER_VALIDATE_EMAIL)){
          array_push($errors, "Email no v&aacute;lido");
        }
      }else{
        array_push($errors, "Email es requerido");
      }
      if(!empty($arr_datsol->fij_solici)){
        settype($arr_datsol->fij_solici,"integer");
        if(strlen($arr_datsol->fij_solici)!=7){
          array_push($errors, "No. fijo debe contener 7 caracteres num&eacute;ricos unicamente. ");
        }
      }
      if(!empty($arr_datsol->cel_solici)){
        settype($arr_datsol->cel_solici,"integer");
        if(strlen($arr_datsol->cel_solici)!=10){
          array_push($errors, "No. de celular debe contener 10 caracteres num&eacute;ricos unicamente.");
        }
      }else{
        array_push($errors, "No. de celular es requerido");
      }
      return $errors;
    }catch(Exception $e){
      return array($e->getMessage());
    }
  }

  function setSoliciDatsol($conn, $objSoliciDatosx, $update){
    try{
      if($update){
        $sql =  "update ".BASE_DATOS.".tab_solici_datosx ".
                "set dir_usrmai=$objSoliciDatosx->dir_usrmai, ".
                "num_usrfij=$objSoliciDatosx->num_usrfij, ".
                "num_usrcel=$objSoliciDatosx->num_usrcel, ".
                "usr_modifi=$objSoliciDatosx->usr_modifi, ".
                "fec_modifi=$objSoliciDatosx->fec_modifi ".
                "where cod_solici=$objSoliciDatosx->cod_solici";
      }else{
        $sql =  "insert into ".BASE_DATOS.".tab_solici_datosx ".
                "(cod_solici, cod_transp,nom_aplica,url_aplica,cod_usrsol,nom_usrsol,dir_usrmai,num_usrfij,num_usrcel,usr_creaci,fec_creaci) ".
                "values (".
                "$objSoliciDatosx->cod_solici,$objSoliciDatosx->cod_transp,$objSoliciDatosx->nom_aplica,$objSoliciDatosx->url_aplica,$objSoliciDatosx->cod_usrsol,$objSoliciDatosx->nom_usrsol,$objSoliciDatosx->dir_usrmai,$objSoliciDatosx->num_usrfij,$objSoliciDatosx->num_usrcel,$objSoliciDatosx->usr_creaci,$objSoliciDatosx->fec_creaci" .
                ")";
      }
      return $conn -> ExecuteCons( $sql, "R" );
    }catch(Exception $e){
      return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
    }
  }
  function getEmptySoliciDatosx($conn){
    return json_decode(json_encode(array(
      "cod_solici"=>getNewCodSolici($conn),
      "cod_transp"=>"NULL",
      "nom_aplica"=>"NULL",
      "url_aplica"=>"NULL",
      "cod_usrsol"=>"NULL",
      "nom_usrsol"=>"NULL",
      "dir_usrmai"=>"NULL",
      "num_usrfij"=>"NULL",
      "num_usrcel"=>"NULL",
      "usr_creaci"=>"NULL",
      "fec_creaci"=>"NULL",
      "usr_modifi"=>"NULL",
      "fec_modifi"=>"NULL"
    )));
  }
  function getEmptySoliciSolici($conn){
    return json_decode(json_encode(array(
      "num_solici"=>getNewNumSolici($conn),
      "cod_solici"=>"NULL",
      "cod_estado"=>"NULL",
      "cod_tipsol"=>"NULL",
      "cod_subtip"=>"NULL",
      "cod_ciuori"=>"NULL",
      "cod_ciudes"=>"NULL",
      "nom_viaxxx"=>"NULL",
      "fec_iniseg"=>"NULL",
      "fec_finseg"=>"NULL",
      "lis_placas"=>"NULL",
      "nom_asunto"=>"NULL",
      "dir_archiv"=>"NULL",
      "obs_solici"=>"NULL",
      "usr_creaci"=>"NULL",
      "fec_creaci"=>"NULL",
      "usr_modifi"=>"NULL",
      "fec_modifi"=>"NULL"
    )));
  }
  function setSoliciSolici($conn, $objSoliciSolici, $update){
    mail("andres.torres@intrared.net", "solifaa", var_export($objSoliciSolici, true) );
    if($update){
        $sql =  "update ".BASE_DATOS.".tab_solici_solici ".
                "set cod_estado=$objSoliciSolici->cod_estado, ".
                "usr_modifi=$objSoliciSolici->usr_modifi, ".
                "fec_modifi=$objSoliciSolici->fec_modifi  ".
                "where num_solici=$objSoliciSolici->num_solici";
      }else{
        $sql =  "insert into ".BASE_DATOS.".tab_solici_solici ".
                "(num_solici,cod_solici,cod_estado,cod_tipsol,cod_subtip,cod_ciuori,cod_ciudes,nom_viaxxx,fec_iniseg,fec_finseg,lis_placas,nom_asunto,dir_archiv,obs_solici,usr_creaci,fec_creaci) ".
                "values (".
                "$objSoliciSolici->num_solici,$objSoliciSolici->cod_solici,$objSoliciSolici->cod_estado,$objSoliciSolici->cod_tipsol,$objSoliciSolici->cod_subtip,$objSoliciSolici->cod_ciuori,$objSoliciSolici->cod_ciudes,$objSoliciSolici->nom_viaxxx,$objSoliciSolici->fec_iniseg,$objSoliciSolici->fec_finseg,$objSoliciSolici->lis_placas,$objSoliciSolici->nom_asunto,$objSoliciSolici->dir_archiv,$objSoliciSolici->obs_solici,$objSoliciSolici->usr_creaci,$objSoliciSolici->fec_creaci".
                ")";
      }
      mail("andres.torres@eltransporte.org", "solifaaa", var_export($sql, true));
      return $conn -> ExecuteCons( $sql, "R" );
      
  }

  function getTransSolici($num_solici = null ,$fConsult = null,$proceso = null)
  {
      try{  

        $joins = "";
        $campos = "";

        if ($proceso == '1') {
          $campos = ", d.nom_ciudad AS origen, e.nom_ciudad AS destino, a.nom_viaxxx ";
          $joins = "INNER JOIN ".BASE_DATOS.".tab_genera_ciudad d ON d.cod_ciudad = a.cod_ciuori
                  INNER JOIN ".BASE_DATOS.".tab_genera_ciudad e ON e.cod_ciudad = a.cod_ciudes";
        }elseif($proceso == '2'){
          $campos = ", f.nom_subtip ";
          $joins = "INNER JOIN ".BASE_DATOS.".tab_solici_subtip f ON f.cod_tipsol = a.cod_tipsol AND f.cod_subtip = a.cod_subtip";
        }
        $sql =  "SELECT c.abr_tercer, b.dir_usrmai, a.obs_solici, a.nom_asunto, h.obs_config $campos FROM ".BASE_DATOS.".tab_solici_solici a ".
                  "INNER JOIN ".BASE_DATOS.".tab_solici_datosx b ON a.cod_solici = b.cod_solici ".
                  "INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON b.cod_transp = c.cod_tercer ".
                  "INNER JOIN ".BASE_DATOS.".tab_solici_config h ON h.cod_tipsol = a.cod_tipsol AND h.cod_subtip = a.cod_subtip ".
                  "$joins WHERE ".
            "a.num_solici=$num_solici";
      
        $fConsult -> ExecuteCons( $sql );
        return $consulta = $fConsult -> RetMatrix( 'a' );
      }
      catch(Exception $e)
      {
        return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
      }
    }

  function setSoliciRuta($arr_datsol,$arr_rutsol){
    try{
      /**************** valida general si es usuario *********************/
      $fNomUsuari = htmlspecialchars($arr_datsol->cod_usuari,ENT_QUOTES);
      $fPwdClavex = $arr_datsol->pwd_clavex;
      $fCodTranps = htmlspecialchars($arr_datsol->cod_transp,ENT_QUOTES);

      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "setSoliciRuta" );
      $fReturn = NULL;

      include_once( AplKon );
      $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
      //mail("andres.torres@eltransporte.org", "solifa", $fNomUsuari."\n".$fPwdClavex."\n".var_export($a, true));
      if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
      {
        throw new Exception( "El usuario no existe.", "1002" );
      }
      /**************** valida general si es usuario *********************/


      /**************** validacion arreglos que llegan *********************/
      $arrValDS=validateDatSol($arr_datsol);
      $arrValRS=validateRutSol($arr_rutsol);
      $errors=array_merge($arrValDS,$arrValRS);
      if(sizeof($errors)>0){
        throw new Exception( implode(", ",$errors), "6001" );
      }
      /**************** validacion arreglos que llegan *********************/

      /**************** usuario tab_solici_datosx *********************/
      $objSoliciDatosx = getEmptySoliciDatosx($fConsult);
      if($objSoliciDatosx->cod_solici==0){
        throw new Exception( "No se pudo obtener un c&oacute;digo de solicitud v&aacute;lido", "3001" );
      }
      $objSoliciDatosx->cod_transp=!empty($arr_datsol->cod_transp) ? "'".htmlspecialchars($arr_datsol->cod_transp,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->nom_aplica=!empty($arr_datsol->nom_aplica) ? "'".htmlspecialchars($arr_datsol->nom_aplica,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->url_aplica=!empty($arr_datsol->url_aplica) ? "'".htmlspecialchars($arr_datsol->url_aplica,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->cod_usrsol=!empty($arr_datsol->cod_solici) ? "'".htmlspecialchars($arr_datsol->cod_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->nom_usrsol=!empty($arr_datsol->nom_solici) ? "'".htmlspecialchars($arr_datsol->nom_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->dir_usrmai=!empty($arr_datsol->mai_solici) ? "'".htmlspecialchars($arr_datsol->mai_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->num_usrfij=!empty($arr_datsol->fij_solici) ? htmlspecialchars($arr_datsol->fij_solici,ENT_QUOTES)         : "NULL";
      $objSoliciDatosx->num_usrcel=!empty($arr_datsol->cel_solici) ? htmlspecialchars($arr_datsol->cel_solici,ENT_QUOTES)         : "NULL";
      $objSoliciDatosx->usr_creaci="'ws'";
      $objSoliciDatosx->fec_creaci="now()";
      $objSoliciDatosx->usr_modifi="'ws'";
      $objSoliciDatosx->fec_modifi="now()";
      $recoverSoliciDatosx=getSoliciDatsol($fConsult, $objSoliciDatosx);
      $update=false;
      if(is_array($recoverSoliciDatosx) && sizeof($recoverSoliciDatosx)>0){
        settype($recoverSoliciDatosx[0]["cod_solici"], "integer");
        $objSoliciDatosx->cod_solici=$recoverSoliciDatosx[0]["cod_solici"];
        $update=true;
      }
      if(!setSoliciDatsol($fConsult, $objSoliciDatosx, $update)){
        throw new Exception( "Informaci&oacute;n incompleta o no v&aacute;lida (setSoliciDatsol).", "6001" );
      }
      /**************** usuario tab_solici_datosx *********************/


      /**************** registro de solicitud *********************/
      $objSoliciSolici=getEmptySoliciSolici($fConsult);
      if($objSoliciSolici->num_solici==0){
        throw new Exception( "No se pudo obtener un n&uacute;mero de solicitud v&aacute;lido", "3001" );
      }
      $objSoliciSolici->cod_solici=$objSoliciDatosx->cod_solici;
      $objSoliciSolici->cod_estado=1;
      $objSoliciSolici->cod_tipsol=1;
      $objSoliciSolici->cod_subtip=0;//se debe ajustar este valor para que quede por defecto
      $objSoliciSolici->cod_ciuori="'".htmlspecialchars($arr_rutsol->cod_ciuori,ENT_QUOTES)."'";
      $objSoliciSolici->cod_ciudes="'".htmlspecialchars($arr_rutsol->cod_ciudes,ENT_QUOTES)."'";
      $objSoliciSolici->nom_viaxxx="'".htmlspecialchars($arr_rutsol->nom_viaxxx,ENT_QUOTES)."'";
      $objSoliciSolici->usr_creaci="'ws'";
      $objSoliciSolici->fec_creaci="now()";
      $objSoliciSolici->usr_modifi="'ws'";
      $objSoliciSolici->fec_modifi="now()";

      //update en falso, ya que siempre se debe crear un registro, en este punto no hya actualizacion
      $update=false;
      if(!setSoliciSolici($fConsult, $objSoliciSolici, $update)){
        throw new Exception( "Informaci&oacute;n incompleta o no v&aacute;lida (setSoliciSolici).", "6001" );
      }
      /**************** registro de solicitud *********************/


      /**************** respuesta a solicitud *********************/
      $msg = "code_resp:1000; msg_resp: Su solicitud Numero ".$objSoliciSolici->num_solici." de creacion de ruta fue enviada con Exito, Datos: \n";
      $msg.= "Origen: ".htmlspecialchars($arr_rutsol->cod_ciuori,ENT_QUOTES).", \n";
      $msg.= "Destino: ".htmlspecialchars($arr_rutsol->cod_ciudes,ENT_QUOTES).", \n";
      $msg.= "Via: ".htmlspecialchars($arr_rutsol->nom_viaxxx,ENT_QUOTES).", \n";
      $msg.= "cod_solici: ".$objSoliciSolici->num_solici.", \n";
      $msg.= "ANS: ".getTransSolici($objSoliciSolici->num_solici, $fConsult, "1")[0]['obs_config']." \n";
      
      $dataMail = (object) array(
                            'nom_solici'  =>  'Creacion Ruta',
                            'nom_cliente'  =>  getTransSolici($objSoliciSolici->num_solici, $fConsult)[0]['abr_tercer'],
                            'date'  =>  date("Y-m-d H:i:s"),
                            'num_solici'  =>  $objSoliciSolici->num_solici,
                            'cod_usuari'  =>  $objSoliciDatosx->nom_usrsol,
              'year'  =>  date("Y"),
              'asunto' => "Creacion Ruta: ".getTransSolici($objSoliciSolici->num_solici, $fConsult,"1")[0]['origen']." - ".getTransSolici($objSoliciSolici->num_solici, $fConsult, "1")[0]['destino']." Via ".getTransSolici($objSoliciSolici->num_solici, $fConsult)[0]['nom_viaxxx'],
                            'cod_estado'  =>  "Abierta",
                            'obs_solici'  =>  "Solicitud De Creacion Ruta: ".getTransSolici($objSoliciSolici->num_solici, $fConsult,"1")[0]['origen']." - ".getTransSolici($objSoliciSolici->num_solici, $fConsult, "1")[0]['destino']." Via ".getTransSolici($objSoliciSolici->num_solici, $fConsult)[0]['nom_viaxxx'],
                            'mailTo'  =>  getTransSolici($objSoliciSolici->num_solici, $fConsult)[0]['dir_usrmai'].",".$objSoliciDatosx->dir_usrmai.",".FarMai.",supervisores@eltransporte.org",
                              );

      sendMailSolifa($dataMail);
      
      /**************** respuesta a solicitud *********************/
      return $msg;

    }catch(Exception $e){
      return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
    }
  }

  function sendMailSolifa($data = NULL)
  {
      try
      { 
        $mCabece = 'MIME-Version: 1.0' . "\r\n";
        $mCabece .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $mCabece .= 'From: Sol. Asistencia Logistica <webmaster@grupooet.com>' . "\r\n";
        $tmpl_file = '/var/www/html/ap/satt_standa/planti/pla_solifa_abierta.html';
        $thefile = implode("", file($tmpl_file));
        $thefile = addslashes($thefile);
        $thefile = "\$r_file=\"" . $thefile . "\";";
        eval($thefile);
        $mHtmlxx = $r_file;
        if($_SERVER['HTTP_HOST'] == 'dev.intrared.net:8083')
        {
            $mailToS = "maribel.garcia@eltransporte.org";
        }
        else
        {
            $mailToS = $data->mailTo;
        }
          mail( $mailToS, "sol. ".$data->asunto, '<div name="_faro_07">' . $mHtmlxx . '</div>', $mCabece );

         
      
      }
      catch(Exception $e)
      {
        return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
      }
  }


  function setSoliciSegimientoEspecial($arr_datsol,$ind_segesp,$fec_iniseg,$fec_finseg,$lis_placax,$obs_solici){
    try{
      $obs_solici = base64_decode($obs_solici);
      /**************** valida general si es usuario *********************/
      $fNomUsuari = htmlspecialchars($arr_datsol->cod_usuari,ENT_QUOTES);
      $fPwdClavex = $arr_datsol->pwd_clavex;
      $fCodTranps = htmlspecialchars($arr_datsol->cod_transp,ENT_QUOTES);

      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "setSoliciRuta" );
      $fReturn = NULL;

      include_once( AplKon );
      $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

      if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
      {
        throw new Exception( "El usuario no existe.", "1002" );
      }
      /**************** valida general si es usuario *********************/


      /**************** validacion arreglos que llegan *********************/
      $arrValDS=validateDatSol($arr_datsol);
      //$arrValRS=validateRutSol($arr_rutsol);
      //$errors=array_merge($arrValDS,$arrValRS);
      $errors=array_merge($arrValDS);
      if(sizeof($errors)>0){
        throw new Exception( implode(", ",$errors), "6001" );
      }
      /**************** validacion arreglos que llegan *********************/

      /**************** usuario tab_solici_datosx *********************/
      $objSoliciDatosx = getEmptySoliciDatosx($fConsult);
      if($objSoliciDatosx->cod_solici==0){
        throw new Exception( "No se pudo obtener un c&oacute;digo de solicitud v&aacute;lido", "3001" );
      }
      $objSoliciDatosx->cod_transp=!empty($arr_datsol->cod_transp) ? "'".htmlspecialchars($arr_datsol->cod_transp,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->nom_aplica=!empty($arr_datsol->nom_aplica) ? "'".htmlspecialchars($arr_datsol->nom_aplica,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->url_aplica=!empty($arr_datsol->url_aplica) ? "'".htmlspecialchars($arr_datsol->url_aplica,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->cod_usrsol=!empty($arr_datsol->cod_solici) ? "'".htmlspecialchars($arr_datsol->cod_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->nom_usrsol=!empty($arr_datsol->nom_solici) ? "'".htmlspecialchars($arr_datsol->nom_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->dir_usrmai=!empty($arr_datsol->mai_solici) ? "'".htmlspecialchars($arr_datsol->mai_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->num_usrfij=!empty($arr_datsol->fij_solici) ? htmlspecialchars($arr_datsol->fij_solici,ENT_QUOTES)         : "NULL";
      $objSoliciDatosx->num_usrcel=!empty($arr_datsol->cel_solici) ? htmlspecialchars($arr_datsol->cel_solici,ENT_QUOTES)         : "NULL";
      $objSoliciDatosx->usr_creaci="'ws'";
      $objSoliciDatosx->fec_creaci="now()";
      $objSoliciDatosx->usr_modifi="'ws'";
      $objSoliciDatosx->fec_modifi="now()";
      $recoverSoliciDatosx=getSoliciDatsol($fConsult, $objSoliciDatosx);
      $update=false;
      if(is_array($recoverSoliciDatosx) && sizeof($recoverSoliciDatosx)>0){
        settype($recoverSoliciDatosx[0]["cod_solici"], "integer");
        $objSoliciDatosx->cod_solici=$recoverSoliciDatosx[0]["cod_solici"];
        $update=true;
      }
      if(!setSoliciDatsol($fConsult, $objSoliciDatosx, $update)){
        throw new Exception( "Informaci&oacute;n incompleta o no v&aacute;lida (setSoliciDatsol).", "6001" );
      }
      /**************** usuario tab_solici_datosx *********************/



      /**************** registro de solicitud *********************/
      $objSoliciSolici=getEmptySoliciSolici($fConsult);
      if($objSoliciSolici->num_solici==0){
        throw new Exception( "No se pudo obtener un n&uacute;mero de solicitud v&aacute;lido", "3001" );
      }
      $objSoliciSolici->cod_solici=$objSoliciDatosx->cod_solici;
      $objSoliciSolici->cod_estado=1;
      $objSoliciSolici->cod_tipsol=2;
      $objSoliciSolici->cod_subtip=htmlspecialchars($ind_segesp,ENT_QUOTES);//falta validar aqui
      $objSoliciSolici->fec_iniseg="'".htmlspecialchars($fec_iniseg,ENT_QUOTES)."'";//falta validar aqui
      $objSoliciSolici->fec_finseg="'".htmlspecialchars($fec_finseg,ENT_QUOTES)."'";//falta validar aqui
      $objSoliciSolici->lis_placas="'".htmlspecialchars($lis_placax,ENT_QUOTES)."'";//falta validar aqui
      $objSoliciSolici->obs_solici="'".htmlspecialchars($obs_solici,ENT_QUOTES)."'";//falta validar aqui
      $objSoliciSolici->usr_creaci="'ws'";
      $objSoliciSolici->fec_creaci="now()";
      $objSoliciSolici->usr_modifi="'ws'";
      $objSoliciSolici->fec_modifi="now()";

      //update en falso, ya que siempre se debe crear un registro, en este punto no hya actualizacion
      $update=false;
      if(!setSoliciSolici($fConsult, $objSoliciSolici, $update)){
        throw new Exception( "Informaci&oacute;n incompleta o no v&aacute;lida (setSoliciSolici).", "6001" );
      }
      /**************** registro de solicitud *********************/


      /**************** respuesta a solicitud *********************/
      $msg = '{"code_resp":"1000", "msg_resp":"Su solicitud de seguimiento especial fue enviado con Exito, Datos: ",';
      $msg.= '"Sub Tipo": "'.htmlspecialchars($ind_segesp,ENT_QUOTES).'",';
      $msg.= '"Fecha Inicio": "'.htmlspecialchars($fec_iniseg,ENT_QUOTES).'",';
      $msg.= '"Fecha Fin": "'.htmlspecialchars($fec_finseg,ENT_QUOTES).'",';
      $msg.= '"Placa(s)": "'.htmlspecialchars($lis_placax,ENT_QUOTES).'",';
      $msg.= '"Observaci&oacute;n": "'.htmlspecialchars($obs_solici,ENT_QUOTES).'",';
      $msg.= '"cod_solici": "'.$objSoliciSolici->num_solici.'"}';
      return $msg;
      /**************** respuesta a solicitud *********************/

    }catch(Exception $e){
      return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
    }
  }
  function setSoliciPQR($arr_datsol, $ind_pqrsxx, $nom_pqrsxx, $obs_pqrsxx, $fil_archiv){
    try{
      $nom_pqrsxx = base64_decode($nom_pqrsxx);
      $obs_pqrsxx = base64_decode($obs_pqrsxx);
      /**************** valida general si es usuario *********************/
      $fNomUsuari = htmlspecialchars($arr_datsol->cod_usuari,ENT_QUOTES);
      $fPwdClavex = $arr_datsol->pwd_clavex;
      $fCodTranps = htmlspecialchars($arr_datsol->cod_transp,ENT_QUOTES);

      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "setSoliciRuta" );
      $fReturn = NULL;

      include_once( AplKon );
      $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

      if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
      {
        throw new Exception( "El usuario no existe.", "1002" );
      }
      /**************** valida general si es usuario *********************/


      /**************** validacion arreglos que llegan *********************/
      $arrValDS=validateDatSol($arr_datsol);
      //$arrValRS=validateRutSol($arr_rutsol);
      //$errors=array_merge($arrValDS,$arrValRS);
      $errors=array_merge($arrValDS);
      if(sizeof($errors)>0){
        throw new Exception( implode(", ",$errors), "6001" );
      }
      /**************** validacion arreglos que llegan *********************/

      /**************** usuario tab_solici_datosx *********************/
      $objSoliciDatosx = getEmptySoliciDatosx($fConsult);
      if($objSoliciDatosx->cod_solici==0){
        throw new Exception( "No se pudo obtener un c&oacute;digo de solicitud v&aacute;lido", "3001" );
      }
      $objSoliciDatosx->cod_transp=!empty($arr_datsol->cod_transp) ? "'".htmlspecialchars($arr_datsol->cod_transp,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->nom_aplica=!empty($arr_datsol->nom_aplica) ? "'".htmlspecialchars($arr_datsol->nom_aplica,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->url_aplica=!empty($arr_datsol->url_aplica) ? "'".htmlspecialchars($arr_datsol->url_aplica,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->cod_usrsol=!empty($arr_datsol->cod_solici) ? "'".htmlspecialchars($arr_datsol->cod_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->nom_usrsol=!empty($arr_datsol->nom_solici) ? "'".htmlspecialchars($arr_datsol->nom_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->dir_usrmai=!empty($arr_datsol->mai_solici) ? "'".htmlspecialchars($arr_datsol->mai_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->num_usrfij=!empty($arr_datsol->fij_solici) ? htmlspecialchars($arr_datsol->fij_solici,ENT_QUOTES)         : "NULL";
      $objSoliciDatosx->num_usrcel=!empty($arr_datsol->cel_solici) ? htmlspecialchars($arr_datsol->cel_solici,ENT_QUOTES)         : "NULL";
      $objSoliciDatosx->usr_creaci="'ws'";
      $objSoliciDatosx->fec_creaci="now()";
      $objSoliciDatosx->usr_modifi="'ws'";
      $objSoliciDatosx->fec_modifi="now()";
      $recoverSoliciDatosx=getSoliciDatsol($fConsult, $objSoliciDatosx);
      $update=false;
      if(is_array($recoverSoliciDatosx) && sizeof($recoverSoliciDatosx)>0){
        settype($recoverSoliciDatosx[0]["cod_solici"], "integer");
        $objSoliciDatosx->cod_solici=$recoverSoliciDatosx[0]["cod_solici"];
        $update=true;
      }
      if(!setSoliciDatsol($fConsult, $objSoliciDatosx, $update)){
        throw new Exception( "Informaci&oacute;n incompleta o no v&aacute;lida (setSoliciDatsol).", "6001" );
      }
      /**************** usuario tab_solici_datosx *********************/



      /**************** registro de solicitud *********************/
      $objSoliciSolici=getEmptySoliciSolici($fConsult);
      if($objSoliciSolici->num_solici==0){
        throw new Exception( "No se pudo obtener un n&uacute;mero de solicitud v&aacute;lido", "3001" );
      }
      $objSoliciSolici->cod_solici=$objSoliciDatosx->cod_solici;
      $objSoliciSolici->cod_estado=1;
      $objSoliciSolici->cod_tipsol=3;
      $objSoliciSolici->cod_subtip=htmlspecialchars($ind_pqrsxx,ENT_QUOTES);//falta validar aqui
      $objSoliciSolici->nom_asunto="'".htmlspecialchars($nom_pqrsxx,ENT_QUOTES)."'";//falta validar aqui
      $objSoliciSolici->obs_solici="'".htmlspecialchars($obs_pqrsxx,ENT_QUOTES)."'";//falta validar aqui
      $objSoliciSolici->usr_creaci="'ws'";
      $objSoliciSolici->fec_creaci="now()";
      $objSoliciSolici->usr_modifi="'ws'";
      $objSoliciSolici->fec_modifi="now()";

      $ssf=setSoliciFile($fil_archiv);
      if(!empty($ssf->code))
        throw new Exception( $ssf->error, $ssf->code );
      if($ssf->status==true && empty($ssf->code))
        $objSoliciSolici->dir_archiv="'$ssf->path'";


      //update en falso, ya que siempre se debe crear un registro, en este punto no hya actualizacion
      $update=false;
      if(!setSoliciSolici($fConsult, $objSoliciSolici, $update)){
        throw new Exception( "Informaci&oacute;n incompleta o no v&aacute;lida (setSoliciSolici).", "6001" );
      }
      /**************** registro de solicitud *********************/


      /**************** respuesta a solicitud *********************/
      $msg = "code_resp: 1000; msg_resp:Su solicitud PQR fue enviado con exito Datos";
      $msg.= "Tipo: ".htmlspecialchars($ind_pqrsxx,ENT_QUOTES).", ";
      $msg.= "Asunto: ".htmlspecialchars($nom_pqrsxx,ENT_QUOTES).",";
      $msg.= "Observaci&oacute;n: ".htmlspecialchars($obs_pqrsxx,ENT_QUOTES).",";
      $msg.= "Archivo: ".$ssf->path.", ";
      $msg.= "cod_solici: ".$objSoliciSolici->num_solici.". ";
      return $msg;
      /**************** respuesta a solicitud *********************/

    }catch(Exception $e){
      return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
    }
  }

  function setSoliciOtros($arr_datsol, $nom_otroxx, $obs_otroxx, $fil_archiv){
    try{
      $nom_otroxx = base64_decode($nom_otroxx);
      $obs_otroxx = base64_decode($obs_otroxx);
      /**************** valida general si es usuario *********************/
      $fNomUsuari = htmlspecialchars($arr_datsol->cod_usuari,ENT_QUOTES);
      $fPwdClavex = $arr_datsol->pwd_clavex;
      $fCodTranps = htmlspecialchars($arr_datsol->cod_transp,ENT_QUOTES);

      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "setSoliciRuta" );
      $fReturn = NULL;

      include_once( AplKon );
      $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

      if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
      {
        throw new Exception( "El usuario no existe.", "1002" );
      }
      /**************** valida general si es usuario *********************/


      /**************** validacion arreglos que llegan *********************/
      $arrValDS=validateDatSol($arr_datsol);
      //$arrValRS=validateRutSol($arr_rutsol);
      //$errors=array_merge($arrValDS,$arrValRS);
      $errors=array_merge($arrValDS);
      if(sizeof($errors)>0){
        throw new Exception( implode(", ",$errors), "6001" );
      }
      /**************** validacion arreglos que llegan *********************/

      /**************** usuario tab_solici_datosx *********************/
      $objSoliciDatosx = getEmptySoliciDatosx($fConsult);
      if($objSoliciDatosx->cod_solici==0){
        throw new Exception( "No se pudo obtener un c&oacute;digo de solicitud v&aacute;lido", "3001" );
      }
      $objSoliciDatosx->cod_transp=!empty($arr_datsol->cod_transp) ? "'".htmlspecialchars($arr_datsol->cod_transp,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->nom_aplica=!empty($arr_datsol->nom_aplica) ? "'".htmlspecialchars($arr_datsol->nom_aplica,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->url_aplica=!empty($arr_datsol->url_aplica) ? "'".htmlspecialchars($arr_datsol->url_aplica,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->cod_usrsol=!empty($arr_datsol->cod_solici) ? "'".htmlspecialchars($arr_datsol->cod_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->nom_usrsol=!empty($arr_datsol->nom_solici) ? "'".htmlspecialchars($arr_datsol->nom_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->dir_usrmai=!empty($arr_datsol->mai_solici) ? "'".htmlspecialchars($arr_datsol->mai_solici,ENT_QUOTES)."'" : "NULL";
      $objSoliciDatosx->num_usrfij=!empty($arr_datsol->fij_solici) ? htmlspecialchars($arr_datsol->fij_solici,ENT_QUOTES)         : "NULL";
      $objSoliciDatosx->num_usrcel=!empty($arr_datsol->cel_solici) ? htmlspecialchars($arr_datsol->cel_solici,ENT_QUOTES)         : "NULL";
      $objSoliciDatosx->usr_creaci="'ws'";
      $objSoliciDatosx->fec_creaci="now()";
      $objSoliciDatosx->usr_modifi="'ws'";
      $objSoliciDatosx->fec_modifi="now()";
      $recoverSoliciDatosx=getSoliciDatsol($fConsult, $objSoliciDatosx);
      $update=false;
      if(is_array($recoverSoliciDatosx) && sizeof($recoverSoliciDatosx)>0){
        settype($recoverSoliciDatosx[0]["cod_solici"], "integer");
        $objSoliciDatosx->cod_solici=$recoverSoliciDatosx[0]["cod_solici"];
        $update=true;
      }
      if(!setSoliciDatsol($fConsult, $objSoliciDatosx, $update)){
        throw new Exception( "Informaci&oacute;n incompleta o no v&aacute;lida (setSoliciDatsol).", "6001" );
      }
      /**************** usuario tab_solici_datosx *********************/



      /**************** registro de solicitud *********************/
      $objSoliciSolici=getEmptySoliciSolici($fConsult);
      if($objSoliciSolici->num_solici==0){
        throw new Exception( "No se pudo obtener un n&uacute;mero de solicitud v&aacute;lido", "3001" );
      }
      $objSoliciSolici->cod_solici=$objSoliciDatosx->cod_solici;
      $objSoliciSolici->cod_estado=1;
      $objSoliciSolici->cod_tipsol=4;
      $objSoliciSolici->cod_subtip=0;//falta validar aqui
      $objSoliciSolici->nom_asunto="'".htmlspecialchars($nom_otroxx,ENT_QUOTES)."'";//falta validar aqui
      $objSoliciSolici->obs_solici="'".htmlspecialchars($obs_otroxx,ENT_QUOTES)."'";//falta validar aqui
      $objSoliciSolici->usr_creaci="'ws'";
      $objSoliciSolici->fec_creaci="now()";
      $objSoliciSolici->usr_modifi="'ws'";
      $objSoliciSolici->fec_modifi="now()";

      $ssf=setSoliciFile($fil_archiv);
      if(!empty($ssf->code))
        throw new Exception( $ssf->error, $ssf->code );
      if($ssf->status==true && empty($ssf->code))
        $objSoliciSolici->dir_archiv="'$ssf->path'";

      //update en falso, ya que siempre se debe crear un registro, en este punto no hya actualizacion
      $update=false;
      if(!setSoliciSolici($fConsult, $objSoliciSolici, $update)){
        throw new Exception( "Informaci&oacute;n incompleta o no v&aacute;lida (setSoliciSolici).", "6001" );
      }
      /**************** registro de solicitud *********************/


      /**************** respuesta a solicitud *********************/
      $msg = '{"code_resp":"1000", "msg_resp":"Su solicitud Otros fue enviado con Exito, Datos:",';
      $msg.= '"Asunto": "'.htmlspecialchars($nom_otroxx,ENT_QUOTES).'",';
      $msg.= '"Observaci&oacute;n": "'.htmlspecialchars($obs_otroxx,ENT_QUOTES).'",';
      $msg.= '"Archivo": "'.$ssf->path.'",';
      $msg.= '"cod_solici": "'.$objSoliciSolici->num_solici.'"}';
      return $msg;
      /**************** respuesta a solicitud *********************/

    }catch(Exception $e){
      return "code_resp:".$e->getCode()."; msg_resp:".$e->getMessage();
    }
}


 /************************************************************************
  * Funcion Inserta despacho                                             *
  * @fn setSeguim                                                        *
  * @brief Inserta un despacho en Sat trafico.                           *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodManifi: string Numero del manifiesto.                    *
  * @param $fDatFechax: date   Fecha del despacho.                       *
  * @param $fCodCiuori: string Codigo dane de la ciudad origen.          *
  * @param $fCodCiudes: string Codigo dane de la ciudad destino.         *
  * @param $fCodPlacax: string Matricula del vehiculo.                   *
  * @param $fNumModelo: int    Modelo del vehiculo.                      *
  * @param $fCodMarcax: string Codigo de la marca del vehiculo.          *
  * @param $fCodColorx: string Codigo del color del vehiculo.            *
  * @param $fCodConduc: string Documento del conductor del vehiculo.     *
  * @param $fNomConduc: string Nombre del conductor del vehiculo.        *
  * @param $fCiuConduc: string Codigo dane de la ciudad del conductor.   *
  * @param $fTelConduc: string Numero de telefono del conductor.         *
  * @param $fMovConduc: string Numero de telefono movil del conductor.   *
  * @param $fObsComent: string Obsevaciones.                             *
  * @param $fCodRutaxx: string Codigo de la ruta Faro.                   *
  * @param $fIndNaturb: bool   Ind nacional urbano.                      *
  * @param $fNumConfig: str¡ng Configuracion del vehiculo.               *
  * @param $fCodCarroc: string Codigo de la carroceria del vehiculo.     *
  * @param $fNumChasis: int    Numero del chasis del vehiculo.           *
  * @param $fNumMotorx: str¡ng Serial del motor vehiculo.                *
  * @param $fNumSoatxx: str¡ng Seguro obligatorio.                       *
  * @param $fDatVigsoa: date   Fecha vigencia Soat.                      *
  * @param $fNomCiasoa: string Nombre compania de seguro Soat.           *
  * @param $fNumTarpro: str¡ng Numero tarjeta de proipedad.              *
  * @param $fNumTrayle: str¡ng Matricula remolque del vehiculo.          *
  * @param $fCatLicenc: string Numero categoria licencia del conductor.  *
  * @param $fDirConduc: string Direccion del conductor del vehiculo.     *
  * @param $fCodPoseed: string Documento del poseedor del vehiculo.      *
  * @param $fNomPoseed: string Nombre del poseedor del vehiculo.         *
  * @param $fCiuPoseed: string Codigo dane de la ciudad del poseedor.    *
  * @param $fDirPoseed: string Direccion del poseedor.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/
 function setSeguim( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL,
                      $fDatFechax = NULL, $fCodCiuori = NULL, $fCodCiudes = NULL, $fCodPlacax = NULL, 
                      $fNumModelo = NULL, $fCodMarcax = NULL, $fCodLineax = NULL, $fCodColorx = NULL, 
                      $fCodConduc = NULL, $fNomConduc = NULL, $fCiuConduc = NULL, $fTelConduc = NULL, 
                      $fMovConduc = NULL, $fObsComent = NULL, $fCodRutaxx = NULL, $fNomRutaxx = NULL, 
                      $fIndNaturb = 1,    $fNumConfig = "3S3",$fCodCarroc = 0,    $fNumChasis = 1111, 
                      $fNumMotorx = 1111, $fNumSoatxx = 1111, $fDatVigsoa = NULL, $fNomCiasoa = "NA", 
                      $fNumTarpro = 1111, $fNumTrayle = NULL, $fCatLicenc = 1,    $fDirConduc = "NA", 
                      $fCodPoseed = NULL, $fNomPoseed = NULL, $fCiuPoseed = NULL, $fDirPoseed = "NA",
                      $mCodAgedes = NULL, $mCodContrs = NULL, $mCodAgenci = NULL, $fCodoperad = NULL,
                      $mCodGpsxxx = NULL, $mCodRemesa = NULL, $fBinHuella = NULL, $fNumViajex = NULL, 
                      $fDatgps2xx = NULL, $fNomAplica = NULL, $fFotConduc = NULL, $fFotVehicu = NULL,
                      $fNumIntent = 0  )
  {
 
    # Parche - Cuando la empresa sea Intracarga se deben colocar NULL los campos
    if( $fCodTranps == '802017639' )
    {
      $mCodAgedes = NULL;
      $mCodContrs = NULL;
      $mCodAgenci = NULL;
      $fCodoperad = NULL;
      $mCodGpsxxx = NULL;
      $mCodRemesa = NULL;
      $fBinHuella = NULL;
      $fNumViajex = NULL;
    }

    // if ($fCodTranps == '805027046'){
    //   mail("andres.torres@eltransporte.org", "gl-remesa", var_export($mCodRemesa, true));
    // }

    $fCodPoseed = str_replace("-", "", $fCodPoseed);
    $fCodPoseed = str_replace(" ", "", $fCodPoseed);
    $fMessagesGps = array();
    $fMessagesGps2 = array();
    $fMessagesRem =  array();    
    $fMessagesSeguim = array();
    $fMessagesFotoCondutor = array();
    $fMessageVehicu = array();
	
    //Se realiza ajuste para la Interfaz de Transportes FW ellos envian la fecha en formatos raros por lo que se acuerda en reunion 21-01-2014 con Argos 
    //tomar la fecha en la que nos envien el despacho como la fecha de salida sin importar lo que ellos nos envien
    if( $fCodTranps == '900243606' )
    {
      $fDatFechax = date( "Y-m-d H:i:s" );
    }
	
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, 
                      "cod_manifi" => $fCodManifi, "dat_fechax" => $fDatFechax, "cod_ciuori" => $fCodCiuori, 
                      "cod_ciudes" => $fCodCiudes, "cod_placax" => $fCodPlacax, "num_modelo" => $fNumModelo, 
                      "cod_marcax" => $fCodMarcax, "cod_lineax" => $fCodLineax, "cod_colorx" => $fCodColorx, 
                      "cod_conduc" => $fCodConduc, "nom_conduc" => $fNomConduc, "ciu_conduc" => $fCiuConduc, 
                      "tel_conduc" => $fTelConduc, "mov_conduc" => $fMovConduc, "obs_coment" => $fObsComent, 
                      "cod_rutaxx" => $fCodRutaxx, "nom_rutaxx" => $fNomRutaxx, "ind_naturb" => $fIndNaturb, 
                      "num_config" => $fNumConfig, "cod_carroc" => $fCodCarroc, "num_chasis" => $fNumChasis, 
                      "num_motorx" => $fNumMotorx, "num_soatxx" => $fNumSoatxx, "dat_vigsoa" => $fDatVigsoa, 
                      "nom_ciasoa" => $fNomCiasoa, "num_tarpro" => $fNumTarpro, "num_trayle" => $fNumTrayle, 
                      "cat_licenc" => $fCatLicenc, "dir_conduc" => $fDirConduc, "cod_poseed" => $fCodPoseed, 
                      "nom_poseed" => $fNomPoseed, "ciu_poseed" => $fCiuPoseed, "dir_poseed" => $fDirPoseed,
                      "cod_agedes" => $mCodAgedes, "cod_operad" => $fCodoperad );

    if( $mCodContrs !== NULL && count( $mCodContrs ) > 0  )
    {
      $i = 0;
      foreach ( $mCodContrs as $mCodContr) 
      {
        $fArrContrs[$i]['cod_contro'] = $mCodContr -> cod_contro;
        $fArrContrs[$i]['val_duraci'] = $mCodContr -> val_duraci;
        $fArrContrs[$i]['ind_virtua'] = $mCodContr -> ind_virtua;
        $i++;
      }
    }
    else
    {
      $fArrContrs = NULL;
    }
    
    //----------------------------------------------------------------------
    if( $mCodAgenci !== NULL && count( $mCodAgenci ) > 0  )
    {
      
      $i = 0;
      foreach ( $mCodAgenci as $mCodAgen) 
      {
        $fInputs['cod_agenci'] = $mCodAgen -> cod_agenci;
        $fInputs['nom_agenci'] = $mCodAgen -> nom_agenci;
        $fInputs['cod_ciudad'] = $mCodAgen -> cod_ciudad;
        $fInputs['dir_agenci'] = $mCodAgen -> dir_agenci;
        $fInputs['tel_agenci'] = $mCodAgen -> tel_agenci;
        $fInputs['con_agenci'] = $mCodAgen -> con_agenci;
        $fInputs['dir_emailx'] = $mCodAgen -> dir_emailx;
        $fInputs['num_faxxxx'] = $mCodAgen -> num_faxxxx;
         $i++;
      }
      
    }
    else
    {
      $mCodAgenci = NULL;
    }
    
    
    //------------------------- Datos Compejos del GPS -----------------------------
    if( $mCodGpsxxx !== NULL && count( $mCodGpsxxx ) > 0  )
    { 
    
      $i = 0;
      $fMessageGps = [];
      foreach ( $mCodGpsxxx as $mCodGps) 
      {
        $mCodGps -> nom_usrgps = substr($mCodGps -> nom_usrgps, 0, 63);
        //mail("nelson.liberato@eltransporte.org", "dasdasdasdas", $mCodGps -> cod_opegps."|".$mCodGps -> nom_usrgps."|".$mCodGps -> clv_usrgps."|".$mCodGps -> idx_gpsxxx);
        $fInputsGps = array (
                      'cod_opegps' => $mCodGps -> cod_opegps,
                      'nom_usrgps' => $mCodGps -> nom_usrgps,
                      'clv_usrgps' => $mCodGps -> clv_usrgps,
                      'idx_gpsxxx' => $mCodGps -> idx_gpsxxx  
                      );
        
        $fValidatorGps = new Validator( $fInputsGps, "gps_valida.txt" );
        $fMessagesGps[] = $fValidatorGps -> GetMessages();
        //mail("nelson.liberato@eltransporte.org", "GetMessages",  var_export($fMessagesGps, true ) );
        
        $fMessageGps[] = $fMessagesGps[0]; // Crea array con los c�digos de validaci�n de cada array que entra
       
        $i++;
      }


    }
    else
    {
      $fInputsGps = NULL;
    }
    
    //------------------------- Datos Compejos del GPS2 -----------------------------
    if( $fDatgps2xx !== NULL && count( $fDatgps2xx ) > 0  )
    { 
    
      $i = 0;
      $fMessageGps2 = [];
      foreach ( $fDatgps2xx as $mCodGps2) 
      {
        $mCodGps2 -> nom_usrgps = substr($mCodGps -> nom_usrgps, 0,  63);
        $fInputsGps2 = array (
                      'nom_operad' => $mCodGps2 -> nom_operad,
                      'nom_usrgps' => $mCodGps2 -> nom_usrgps,
                      'clv_usrgps' => $mCodGps2 -> clv_usrgps,
                      'idx_gpsxxx' => $mCodGps2 -> idx_gpsxxx,
                      'gps_urlxxx' => $mCodGps2 -> gps_urlxxx  
                      );
        
        $fValidatorGps2 = new Validator( $fInputsGps2, "gps_valida2.txt" );
        $fMessagesGps2[] = $fValidatorGps2 -> GetMessages();        
        $fMessageGps2[] = $fMessagesGps2[0]; // Crea array con los c�digos de validaci�n de cada array que entra
        $i++;
      }
    }
    else
    {
      $fInputsGps2 = NULL;
    }
    // -------------------------------------------------------------------------------

        //Valida Datos Remesas
        if( $mCodRemesa !== NULL && count( $mCodRemesa ) > 0  )
        { 
        
          $i = 0;      
          foreach ( $mCodRemesa as $mCodRem) 
          {
            
            $fInputsRem = array (
                          'cod_remesa' => $mCodRem -> cod_remesa,
                          'pes_cargax' => $mCodRem -> pes_cargax,
                          'vol_cargax' => $mCodRem -> vol_cargax,
                          'nom_empaqu' => $mCodRem -> nom_empaqu,  
                          'abr_mercan' => $mCodRem -> abr_mercan,
                          'abr_tercer' => $mCodRem -> abr_tercer,
                          'nom_remite' => $mCodRem -> nom_remite,
                          'nom_destin' => $mCodRem -> nom_destin,
                          'fec_estent' => $mCodRem -> fec_estent,
                          'fec_lledes' => $mCodRem -> fec_lledes,
                          'fec_saldes' => $mCodRem -> fec_saldes,
                          'fec_ldesti' => $mCodRem -> fec_ldesti,  
                          'dir_emailx' => $mCodRem -> dir_emailx,  
                          'cod_client' => $mCodRem -> cod_client  
                          );
            
            $fValidatorRem = new Validator( $fInputsRem, "rem_valida.txt" );
            $fMessagesRem[] = $fValidatorRem -> GetMessages();
            
            $fMessageRem[] = $fMessagesRem[$i]["code"]; // Crea array con los c�digos de validaci�n de cada array que entra
          
            $i++;
          }
        }
        else
        {
          $fInputsRem = NULL;
        }

        //------------------------- DATOS DE FOTOS CONDUCTOR -----------------------------
        if( $fFotConduc !== NULL && count( $fFotConduc ) > 0  )
        { 
          $fInputDatosFotoConductor = array (
                                              'fot_namexx' => $fFotConduc -> bin_fotcon -> fot_namexx,
                                              'fot_typexx' => $fFotConduc -> bin_fotcon -> fot_typexx,
                                              'fot_sizexx' => $fFotConduc -> bin_fotcon -> fot_sizexx,
                                              'fot_binary' => $fFotConduc -> bin_fotcon -> fot_binary,
                                              );
          
          $fValidatorFotoVehiculo = new Validator( $fInputDatosFotoConductor, "fotoRecurso_valida.txt" );
          $fMessagesFotoCondutor[]  = $fValidatorFotoVehiculo -> GetMessages();         
        }
        else
        {
          $fMessagesFotoCondutor = NULL;
        }
        // -------------------------------------------------------------------------------
    
        //------------------------- DATOS DE FOTOS VEHICU -----------------------------
        if( $fFotVehicu !== NULL && count( $fFotVehicu ) > 0  )
        { 
        
          $i = 0;
          $fMessageVehicu = [];
          foreach ( $fFotVehicu as $mDataFoto) 
          {
            $fInputDescricionFotoVehicu = array (
                                                'fot_namexx' => $mDataFoto -> fot_namexx,
                                                'fot_typexx' => $mDataFoto -> fot_typexx,
                                                'fot_sizexx' => $mDataFoto -> fot_sizexx,
                                                'fot_binary' => $mDataFoto -> fot_binary,
                                                );
            
            $fValidatorFotoVehiculo = new Validator( $fInputDescricionFotoVehicu, "fotoRecurso_valida.txt" );
            $fMessageValidadorFotoVehiculo[] = $fValidatorFotoVehiculo -> GetMessages();        
            $fMessageVehicu[] = $fMessageValidadorFotoVehiculo[0]; // Crea array con los c�digos de validaci�n de cada array que entra
            $i++;
          }
        }
        else
        {
          $fMessageVehicu = NULL;
        }
    
      // -------------------------------------------------------------------------------
   
   
    
    $fValidator = new Validator( $fInputs, "seguim_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    
    
 
    if("1000" === $fMessages["code"])
    {      
      $mCodAgenc = array();
      $mCodAgenc[cod_agenci] = $fInputs['cod_agenci'];
      $mCodAgenc[nom_agenci] = $fInputs['nom_agenci'];
      $mCodAgenc[cod_ciudad] = $fInputs['cod_ciudad'];
      $mCodAgenc[dir_agenci] = $fInputs['dir_agenci'];
      $mCodAgenc[tel_agenci] = $fInputs['tel_agenci'];
      $mCodAgenc[con_agenci] = $fInputs['con_agenci'];
      $mCodAgenc[dir_emailx] = $fInputs['dir_emailx'];
      $mCodAgenc[num_faxxxx] = $fInputs['num_faxxxx'];      
    }   
    
    $fMessagesSeguim[0] = $fMessages["code"]; // Mensage de validacion seguim_valida.txt  
    
    $fMessagess[] = $fMessages;
    

 
    $fMessagesFinally = array_merge( $fMessagess, $fMessagesGps, $fMessagesRem , $fMessagesGps2, $fMessagesFotoCondutor, $fMessageVehicu);  // Union de los codigos de respuesta  
 
   

    //return var_export([$fMessagesFotoCondutor, $fMessageVehicu, $fMessagesFinally], true);      

    unset( $fInputs, $fValidator );
     
    $flagCode = 0;
    foreach ($fMessagesFinally AS $mCode)  //
    {
      if($mCode[code] !== '1000')
         $flagCode = 1;      
    }
    
    if( $flagCode === 0  )//AND count($mNumMessageGps) ===  1
    {
      try
      { 
        //include_once( AplKon );
        
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setSeguim" );





         if( $fNomAplica != NULL) // SI NO ES FARO , CARGA EL CLIENTE SATT_TQALA, EJEMPLO
        {
          if(!include_once('/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc') )
          {
            throw new Exception( "Aplicacion GL ".$fNomAplica."  en sat trafico no encontrada", "1999" );
          }
        }
        else if(file_exists( AplKon ) ){
          include_once( AplKon );
        }
        else
        {
          throw new Exception( "Aplicacion FARO en sat trafico no encontrada", "1999" );
          break;
        }

        $fReturn = NULL;
        $fSalidAut = TRUE;
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        $fSeguim = new Seguim( $fExcept );
        // parche para generar llegada a una placa que ya esta en ruta con otro manifiesto, para cargaantioquia, para dejar solo un manifiesto activo a la placa
        // ID 301858 carga antioquia
        // ID 354683 tevsa
        if(in_array($fCodTranps, ['890935085', '900138913']))
        {
          $mData = $fSeguim->placaEnRutaDespacho( $fCodTranps, $fCodPlacax);
          if(is_array($mData)) //si el metodo retorna un array es porque hay datos y toca darle llegada a esa placa con ese manifi
          {
            foreach ($mData AS $IndexLlegada => $mDataDespac) 
            {
              setLlegada($fNomUsuari, $fPwdClavex, $fCodTranps, $mDataDespac['cod_manifi'], date('Y-m-d H:i'), 
                                  'Llegada automatica porque hay otro manifiesto nuevo para la misma placa, Man nuevo '.$fCodManifi, $fCodPlacax,
                                  NULL, NULL, NULL, NULL  );
            }
          }

          unset($mData);
          unset($mDataDespac);
        }


        
        $fLocation = getLocation(  $mCodAgenc[cod_ciudad], $fExcept );
        
        if( $fLocation["CodPaisxx"] !== '' && $fLocation["CodDepart"] !== '' )
        {
          $mCodAgenc[cod_paisxx] = $fLocation["CodPaisxx"];
          $mCodAgenc[cod_depart] = $fLocation["CodDepart"];
          unset( $fLocation );
          
        }
        else             
          throw new Exception( "La ciudad de la agencia no se encuentra registrada: ".$mCodAgenc[cod_ciudad], '6001' );
        
        

        //if($mCodAgenc[dir_emailx] === '')
        //   throw new Exception( "La agencia no tiene direccion Email.", "6001" );
          
        //mail("nelson.liberato@intrared.net", "Cagadas de InterfConalca Produccion", $fNomUsuari." --- ".$fPwdClavex);
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          $fCodActivi = array( 4, 6 );

          //Verifica que se ingresa un poseedor y que este no sea el mismo conductor.
          if( NULL !== $fCodPoseed )
          {
            if( $fCodPoseed != $fCodConduc )
            {
              $fLocation = getLocation( $fCiuPoseed, $fExcept );
              if( FALSE !== $fLocation )
              {
                $fCodPaisxx = $fLocation["CodPaisxx"];
                $fCodDeptox = $fLocation["CodDepart"];
                $fCodCiudad = $fLocation["CodCiudad"];
                unset( $fLocation );
              }
              else
                throw new Exception( "La ciudad del poseedor no se encuentra registrada: ".$fCiuPoseed, '6001' );

              $fTercer -> setTercer( $fCodPoseed, $fNomPoseed, $fNomPoseed, $fDirPoseed, 
                                     $fCodPaisxx, $fCodDeptox, $fCodCiudad, $fNomUsuari,
                                     6, NULL, NULL, NULL, 1, "C", 1, TRUE, $fCodTranps,NULL );

              $fCodActivi = "4";
            }
          }
          else
            $fCodPoseed = $fCodConduc;

          $fLocation = getLocation( $fCiuConduc, $fExcept );
          if( FALSE !== $fLocation )
          {
            $fCodPaisxx = $fLocation["CodPaisxx"];
            $fCodDeptox = $fLocation["CodDepart"];
            $fCodCiudad = $fLocation["CodCiudad"];
            unset( $fLocation );
          }
          else 
            throw new Exception( "La ciudad del conductor no se encuentra registrada: ".$fCiuConduc, '6001' );          	
          
          
          // Operadora celular
         
          if( $fCodoperad == NULL || $fCodoperad =='' )
            $fCodoperad = '10';         
         
          if(  $fTercer -> OperadExist( $fCodoperad ) !== TRUE)
            throw new Exception( "La Operadora telefonica no existe.", "6001" );
         
            $fTercer -> setTercer( $fCodConduc, $fNomConduc, $fNomConduc, $fDirConduc, 
                                 $fCodPaisxx, $fCodDeptox, $fCodCiudad, $fNomUsuari,
                                 $fCodActivi, $fTelConduc, $fMovConduc, $fCatLicenc, 
                                 1, "C", 1, TRUE,NULL, $fCodoperad, $fFotConduc );
                             
            // --------------------------- Nelson Liberato 2017-05-30 -------------------------------------------------
            // Proceso para 1. Relacionar el tercero con trasportadora. 2. Crear usuario de App al conductor de corona
            $mQuery = 'SELECT 1 FROM  '.BASE_DATOS.'.tab_transp_tercer WHERE cod_transp = "'.$fCodTranps.'" AND cod_tercer = "'.$fCodConduc.'" ';
            $fConsult -> ExecuteCons( $mQuery, "R" );
            $mExist = $fConsult -> RetMatrix( "a" );
            if( sizeof($mExist) <= 0 )
            {
              $mDateNow = date( "Y-m-d H:i:s" );
              $mInsertTransTercer = 'INSERT INTO '.BASE_DATOS.'.tab_transp_tercer 
                                      (cod_transp, cod_tercer, ind_estado, usr_creaci, fec_creaci ) 
                                      VALUES 
                                      ("'.$fCodTranps.'", "'.$fCodConduc.'", "1", "'.$fNomUsuari.'","'.$mDateNow.'" ) ';
                if ( $fConsult -> ExecuteCons( $mInsertTransTercer, "R" ) === TRUE )
                {

                    $mQueryTercerMovil = 'SELECT 1 FROM '.BASE_DATOS.'.tab_usuari_movilx WHERE cod_tercer = "'.$fCodConduc.'"  ';
                    $fConsult -> ExecuteCons( $mQueryTercerMovil, "R" );
                    $mExist2 = $fConsult -> RetMatrix( "a" );
                    if (  sizeof($mExist2) <= 0 )
                    {

                        // 2. Generacion del usuario para la APP del conductor
                        if ($fCodTranps == '800105213') {
                          mail("andres.torres@eltransporte.org", "USUARIO MOVIL", var_export($mExist2, true));
                        }
                        
                        // Api de cliente para enviar a central
                        include "/var/www/html/ap/interf/app/APIClienteApp/controlador/UsuarioControlador.php";
                        // Libreria de cifrado del satt_standa
                        include("/var/www/html/ap/satt_standa/ctrapp/seguridad/AESClass.php"); 

                        // Patron de reemplazos de cadena de texto
                        $patron = array("(\¬)", "(\.)", "(\,)", "(\ )", "(ñ)", "(Ñ)", "(\°)", "(\º)", "(&)", "(Â)", "(\()", "(\))", "(\/)", "(\´)", "(\¤)", "(\Ã)", "(\‘)", "(\ƒ)", "(\â)", "(\€)", "(\˜)", "(\¥)", "(Ò)", "(Í)", "(\É)", "(\Ãƒâ€šÃ‚Â)", "(\·)", "(\ª)", "(\-)", "(\+)", "(\Ó)", "(\ü)", "(\Ü)", "(\é)", "(\;)", "(\¡)", "(\!)", "(\`)", "(\<)", "(\>)", "(\_)", "(\#)", "(\ö)", "(\À)", "(\¿)", "(\Ã±)", "(\±)", "(\*)", "(Ú)", "(\%)", "(\|)", "(\ò)", "(\Ì)", "(\:)", "(\Á)", "(\×)", "(\@)", "(\ )", "(\Ù)", "(\á)", "(\–)", "(\")", "(\È)", "(\])", "(\')", "(\í)", "(\Ç)","(\Nš)","(\‚)", "(\ó)", "(\ )", "(\ )", "(\ï½)", "(\?)" );
                        $reemplazo = array("", "", "", "", "n", "N", "", "", "Y", "", "", "", "", "", "", "", "", "", "", "", "", "", "O", "I", "E", "", "", "a", "", "", "O","U","U", "e", "", "", "", "", "", "", "", "", "", "A", "", "", "", "", "", "", "", "", "I", "", "A", "", "", "", "U", "a", "", "", "E", "", "", "i", "", "N","", "", "", "", "" , "", ""  ); 


                        $cliente = new UsuarioControlador();
                        $aes = new Cypher();

                        $pri_clave = "";
                        for ($i=0; $i<6; $i++){
                            $pri_clave .=  dechex(rand(0,15));
                        }
                      
                        $pri_clave = base64_encode($pri_clave);
                        $decodedPass = base64_decode($pri_clave);
                        $mCodHashxx = preg_replace( $patron, $reemplazo, $aes -> cypher($fCodConduc , $mDateNow ) ) ;                    

                        $mCreateUserAPP = "INSERT INTO ".BASE_DATOS.".tab_usuari_movilx 
                          ( cod_tercer ,cod_usuari ,clv_usuari ,cod_hashxx ,ind_activo , ind_admini ,usr_creaci ,fec_creaci )
                          VALUES 
                          ('".$fCodConduc."',  '".$fCodConduc."',  '".$pri_clave."',  '".$mCodHashxx."',  '1',  '0', '".$fNomUsuari."', '".$mDateNow."');";

                          if ( $fConsult -> ExecuteCons( $mCreateUserAPP, "R" ) === TRUE )
                          { 
                            $data = array(
                                    "cod_tercer" => $fCodConduc,
                                    "cod_usuari" => $fCodConduc,
                                    "clv_usuari" => $pri_clave,
                                    "cod_hashxx" => $mCodHashxx,
                                    "ind_activo" => "1",
                                    "nit_transp" => $fCodTranps,
                                    "nom_databa" => BASE_DATOS,
                                    "usr_creaci" => $fNomUsuari,
                                    "source" => "SAT",
                                    "ind_admini" => "0"
                                  );  
                            $respuesta = $cliente -> registrar($data); 
                            
                            $opfl = fopen("/var/www/html/ap/interf/app/faro/logs/USuarioMovil".date("Ymd").".txt", "a+");
                            fwrite($opfl, "---------------------------".date("Y-m-d H:i:s")."-------------------------------\n");
                            fwrite($opfl, "Accion           : Generar Usuario movil");
                            fwrite($opfl, "Conductor ID     : ".$fCodConduc."\n");
                            fwrite($opfl, "Clave     : ".$pri_clave."\n");
                            fwrite($opfl, "Response API APP : ".json_encode($respuesta)."\n"); 
                            fclose($opfl);
                            

                          }
                    }
                }
            }


          $fVehicu = new Vehicu( $fExcept );

          $fCodMarcax = $fVehicu -> getMarca( $fCodMarcax );//Valida que la marca exista o manda por defecto kw.

          $fCodLineax = $fVehicu -> getLinea( $fCodMarcax, $fCodLineax );//Valida que la linea exista o asigna la ultima linea de la marca.

          $fCodColorx = $fVehicu -> getCodColor( $fCodColorx );//Valida el color del vehiculo o retorna el color por defecto.
          
          $fCodCarroc = $fVehicu -> getCodCarroc( $fCodCarroc );//Valida la carroceria del vehiculo o retorna la carroceria por defecto.

          $fQueryInsVehicu = $fVehicu -> setVehicuSatt( $fCodPlacax, $fCodMarcax, $fCodLineax, $fCodColorx, $fNumModelo, $fCodPoseed, $fCodConduc, $fNomUsuari, 
                                                    $fNumMotorx, $fNumChasis, $fNumSoatxx, $fDatVigsoa, $fNomCiasoa, $fNumConfig, $fNumTarpro, $fCodCarroc, 1,
                                                    $fFotVehicu );


          
          if ($fSeguim -> despacFinalizado( $fCodManifi, $fCodTranps, $fCodPlacax )) 
            throw new Exception( "Numero de manifiesto se encuentra Finalizado en la plataforrma. $fCodManifi $fCodTranps $fCodPlacax", "6001" );
          
          if ($fSeguim -> despacExists( $fCodManifi, $fCodTranps, $fCodPlacax )) 
            throw new Exception( "Numero de manifiesto repetido.", "6001" );
 
         
          //return  $fSeguim -> despacExists( $fCodManifi, $fCodTranps, $fCodPlacax);
          
          $fLocation = getLocation( $fCodCiuori, $fExcept );
          if( FALSE !== $fLocation )
          {
            $fCodPaiori = $fLocation["CodPaisxx"];
            $fCodDepori = $fLocation["CodDepart"];
            $fCodCiuori = $fLocation["CodCiudad"];
            unset( $fLocation );
          }
          else
            throw new Exception( "La ciudad de origen no se encuentra registrada: ".$fCodCiuori, '6001' );

          $fLocation = getLocation( $fCodCiudes, $fExcept );
          if( FALSE !== $fLocation )
          {
            $fCodPaides = $fLocation["CodPaisxx"];
            $fCodDepdes = $fLocation["CodDepart"];
            $fCodCiudes = $fLocation["CodCiudad"];
            unset( $fLocation );
          }
          else
            throw new Exception( "La ciudad de destino no se encuentra registrada: ".$fCodCiudes, '6001' );

          $fDespac = new DespacSat( $fExcept );
          
          if( NULL === $fCodRutaxx || '0' === $fCodRutaxx )
          {
            $fSalidAut = FALSE;
          }
          else
          {
          }
          $fCodRutaxx = $fDespac -> getRuta( trim( $fCodRutaxx ), trim( $fCodCiuori ), trim( $fCodCiudes ), trim( $fNomRutaxx ), trim( $fCodTranps ) );//Retorna el codigo de ruta exacto que cumpla con los parametros de entrada.
          
          if( FALSE === $fCodRutaxx )
          {
            $fCodRutaxx = $fSeguim ->  getRoute( trim( $fCodCiuori ), trim( $fCodCiudes ) );//Retorna la primera ruta que se encuentre para el origen y destino dado.
            if( FALSE === $fCodRutaxx )
            {
              $fSeguim -> notifyRoutInterf( $fCodCiuori, $fCodCiudes, $fCodTranps);
              throw new Exception( "No se encuentra ruta disponible para origen ".$fCodCiuori." y destino ".$fCodCiudes.".", "6001" );
              break;
            }
            else
            {
              $fSalidAut = TRUE;
            }
          }
          else
          {
            $fSalidAut = TRUE;
          }
          
          // Inicia la transaccion --------------------------------------------------------------------------------------------------------------------------------

          if($mCodAgenc["cod_agenci"] != '' ||$mCodAgenc["cod_agenci"] != NULL  ) {
            $fLastCodAgenci = $fSeguim -> SetDatAgenc($mCodAgenc ,$fNomUsuari ,$fCodTranps, TRUE ); // Agencia
          }
          

          if( $fLastCodAgenci == NULL || $fLastCodAgenci== 0 || $fLastCodAgenci== '' )
            $fLastCodAgenci = 1;

          if( $mCodRemesa !== NULL && count( $mCodRemesa ) > 0  ){
            foreach( $mCodRemesa as $fRow )
            {
              $codClient = $fRow -> cod_client; //se agrega parche para enviar el cliente (generador) si llega la remesa.
            }
          }
     
          $fConsult -> StartTrans();
          $fIndNaturb = $fCodTranps == '860068121' ?  $fIndNaturb  : 2  ;
          $fQueryInsDespac = $fSeguim -> setDespac( $fNumDespac, $fCodManifi, $fDatFechax, $fCodPaiori, $fCodDepori, 
                                                    $fCodCiuori, $fCodPaides, $fCodDepdes, $fCodCiudes, $fNomUsuari,
                                                    $fIndNaturb, $fLastCodAgenci, 0, 0, "N", "R", FALSE, $fObsComent, $fMovConduc, $fDatgps2xx, $fInputsGps, $codClient);
          if ( $fConsult -> ExecuteCons( $fQueryInsDespac, "R" ) === FALSE ) {
            throw new Exception( "Error en Insert.", "3001" );    
          }
          $fNumDespac = $fSeguim -> getNextNumDespac(); 

          //$fQueryDespacTramo = $fSeguim -> setDespacTramo($fNumDespac ,$fCodTranps, $fCodRutaxx, $fCodCiuori, $fCodCiudes, $fNomUsuari, FALSE ); // Despacho Tramo


          // insert en despac_sisext para funcionamiento con la aoo y las etapas de precargue y cargue, trazabilidad integral
          $fQueryInsDespacSisext = $fSeguim -> setDespacSisext( $fNumDespac, $fCodManifi, FALSE);

          $fQueryInsDesVehi = $fSeguim -> setDesVehi( $fNumDespac, $fCodTranps, $fCodRutaxx, $fCodConduc, 
                                                    $fCodPlacax, $fObsComent, $fNomUsuari, $fLastCodAgenci );

          if( $mCodRemesa !== NULL && count( $mCodRemesa ) > 0  ){
          $fQueryInsDesRem = $fSeguim -> setDesRem( $fNumDespac, $mCodRemesa, $fNomUsuari);
          }                                           
            

          // Volcado de datos de remesas a estructura de destinatarios (Especie de homologacion) 2017 12 07 ID:261848
          $fArrDestin = array();
          $fQueryInsDestin = array();
          $fNitFajobe = '800232356';
          $fArrGenera = array(); // variable para almacenar los cod_genera de las remesas
          if( $mCodRemesa !== NULL && count( $mCodRemesa ) > 0 )
          {     
            foreach ( $mCodRemesa as $mRemesa) 
            {
              // parche ya que los NIT que llegan por este campo, no existen en tercer_tercer y por referencia de llave, generar error de insert en tab_despac_destin, se deja el tercero 9999999=no registra
              //$mRemesa -> cod_client = $mRemesa -> cod_client == $fNitFajobe ? $mRemesa -> cod_client : '9999999';
              
              $fArrDestin[] = array (
                            'num_docume' => $mRemesa -> cod_remesa,
                            'num_docalt' => $mRemesa -> cod_remesa,
                            'cod_genera' => $mRemesa -> cod_client,
                            'nom_genera' => utf8_encode($mRemesa -> abr_tercer),
                            'nom_destin' => utf8_encode($mRemesa -> nom_destin),  
                            'cod_ciudad' => $mRemesa -> ciu_destin, // Nuevo
                            'dir_destin' => utf8_encode($mRemesa -> dir_destin), // Nuevo
                            'num_destin' => $mRemesa -> num_destin,
                            'fec_citdes' => date("Y-m-d", strtotime($mRemesa -> fec_estent) ),
                            'hor_citdes' => date("H:i:s", strtotime($mRemesa -> fec_estent) ),
                            'cod_transp' => $fCodTranps
                            );
              $fArrGenera[] = $mRemesa -> cod_client;
            }
            // genera en despac_destin las remesas que ingresa al despacho, para la app
            $fQueryInsDestin = $fSeguim -> setDesDestin( $fNumDespac, json_decode(json_encode($fArrDestin)), $fNomUsuari);

            $fFecCitcar = date("Y-m-d", strtotime( $fDatFechax ) );
            $fHorCitcar = date("H:i:s", strtotime( $fDatFechax ) );
            $fNomSitcar = utf8_encode($fArrDestin[0]['abr_tercer']);
          }

          $fQueryCorona = $fSeguim -> setDespacCorona( $fNumDespac , $fCodManifi    , $fDatFechax, $fCodPaiori, $fCodDepori, 
                                                      $fCodCiuori , $fCodPaides    , $fCodDepdes, $fCodCiudes, $fNomUsuari, 
                                                      $fIndNaturb , $fLastCodAgenci,           0,           0,         "R",  
                                                      "R"         , FALSE          , $fObsComent, $fMovConduc,
                                                      $fIndNaturb , $fFecCitcar    , $fHorCitcar, $fNomSitcar, 
                                                      0    , 0 , 0, 0 , NULL , 
                                                      NULL , 0 , 0,     NULL
                                                    );

                                                               
          //$fConsult -> StartTrans();

          //if ( $fConsult -> ExecuteCons( $fQueryInsDespac, "R" ) === FALSE ) {
          //  throw new Exception( "Error en Insert.", "3001" );    
          //}

          if( NULL !== $fQueryInsVehicu ) {
            $fConsult -> ExecuteCons( $fQueryInsVehicu, "R" );
          }
                   

          if ( $fConsult -> ExecuteCons( $fQueryInsDespacSisext, "R" ) === FALSE ) {
            throw new Exception( "Error en Insert. Despacho Sistema Externo. ".$fQueryInsDespacSisext, "3001" );
          }
            
          if ( $fConsult -> ExecuteCons( $fQueryInsDesVehi, "R" ) === FALSE ) {
            throw new Exception( "Error en Insert.", "3001" );
          }
          
          if( $mCodGpsxxx !== NULL && count( $mCodGpsxxx ) > 0  ) 
          {
          	$fQueryInsGps = $fSeguim -> SetGps($fNumDespac, $mCodGpsxxx, $fNomUsuari);  //Gps
            if($fQueryInsGps === FALSE) {
              throw new Exception( "Error en Insert.", "3001" );
            }

          }
                    
          
          /*for($t = 0; $t<sizeof($fQueryDespacTramo); $t++)
          {          
            if($fConsult -> ExecuteCons( $fQueryDespacTramo[$t], "R" ) === FALSE )  // Insercion despacho tramo
              throw new Exception( "Error en Insert. despactramo", "3008" );
          }*/
          
          if( $fQueryInsDesRem != NULL && count( $fQueryInsDesRem ) > 0 )
          {
            foreach( $fQueryInsDesRem as $fQuery )
            {
              if ( $fConsult -> ExecuteCons( $fQuery, "R" ) === FALSE )
                throw new Exception( "Error en Insert.", "3001" );
            }
            
          }

          if( $fBinHuella != NULL )
          {
            $mSetHuella = $fSeguim -> setHuellaConductor(  $fCodConduc , $fBinHuella );
            if ( $fConsult -> ExecuteCons( $mSetHuella, "R" ) === FALSE )
                throw new Exception( "Error en Insert Huella Biometrico.", "3001" );
          }          
           
          # Si el cliente con SAT envia el despacho y tienen numero de viaje de corona se hace la relacion para replicar las novedades, NO QUITAR EL IF
          if( $fNumViajex != NULL )
          {
            $mSetViajeTercer = $fSeguim -> setViajexTercer( $fNumDespac, $fNumViajex, $fCodTranps, $fNomUsuari  );
            if ( $fConsult -> ExecuteCons( $mSetViajeTercer, "R" ) === FALSE )
                throw new Exception( "Error en Relacion Viaje corona con despacho cliente (Empresa tercera de corona).", "3001" );
          }

          // Insercion de datos de remesa como destinatario para que la app funcione 2017 12 07 ID:261848
          if( $fQueryInsDestin != NULL && count( $fQueryInsDestin ) > 0 )
          {
             
            foreach( $fQueryInsDestin as $fQuery1 ) // insert de genera_remdes
            {
              if ( $fConsult -> ExecuteCons( $fQuery1['fQueryRemdes'], "R" ) === FALSE )
                throw new Exception( "Error en Insert RemDes.", "5555" );
            }

            foreach( $fQueryInsDestin as $fQuery2 ) // insert a despac_destin
            {
              if ( $fConsult -> ExecuteCons( $fQuery2['mQueryDestin'], "R" ) === FALSE )
                throw new Exception( "Error en Insert DespacDestin. ", "6666" );
            }
            
          }          

          // Insercion en despac_corona
          if( $fQueryCorona != NULL )
          {
              if ( $fConsult -> ExecuteCons( $fQueryCorona, "R" ) === FALSE ) {
                throw new Exception( "Error en Insert DespacCorona.".$fQueryCorona, "5558" );
              }
          }



          $fConsult -> Commit();


          // validación para enviar despacho a la central para la app, pero para fajober
          // $fNitFajobe = '860017005';
          // if( in_array( $fNitFajobe, $fArrGenera ) ) {
          //   setDespachoAPP($fNitFajobe,$fNumDespac,$fConsult );
          // }else {
          // }
          
          setDespachoAPP($fCodTranps, $fNumDespac, $fConsult);

          if( $fSalidAut )
          {
            //Intracarga solo tiene contratados puestos fisicos
            #$fIndVirtua = $fCodTranps == '802017639' ? FALSE : TRUE;
            $fIndVirtua =   TRUE;
            
            $mResult = $fSeguim -> setDesSegu( $fNumDespac, $fCodRutaxx, $fDatFechax, $fDatFechax, $fNomUsuari, $fIndVirtua, TRUE, $fArrContrs, $fCodPlacax, $fCodTranps ) ;
            
            if( $mResult === 'SI')
              $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho, Y se da salida automatica.".$fCodRutaxx."-".$fNumDespac ;
            else if( $mResult === 'NO')
              $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho, queda pendiente darle salida.";
            else  
              $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho pero no se da plan de ruta, ya que no se encontraron puestos de control.";
          }
          else
            $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho pero no se da plan de ruta.";
            
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          

      }
      catch( Exception $e )
      {
        
        if( "3001" == $e -> getCode() )
        {
          //mail( "hugo.malagon@intrared.net", "Pruebas", 'lanza excepcion='.$fNumDespac.' Intent='.$fNumIntent.' Manifi='.$fCodManifi.' Transportadora='.$fCodTranps,'From: soporte.ingenieros@intrared.net' );
          if( $fNumIntent < 3 )
          {
            $fNumIntent++;
           
            $fReturn = setSeguim( $fNomUsuari, $fPwdClavex, $fCodTranps, $fCodManifi,
                                  $fDatFechax, $fCodCiuori, $fCodCiudes, $fCodPlacax, 
                                  $fNumModelo, $fCodMarcax, $fCodLineax, $fCodColorx, 
                                  $fCodConduc, $fNomConduc, $fCiuConduc, $fTelConduc, 
                                  $fMovConduc, $fObsComent, $fCodRutaxx, $fNomRutaxx, 
                                  $fIndNaturb, $fNumConfig, $fCodCarroc, $fNumChasis, 
                                  $fNumMotorx, $fNumSoatxx, $fDatVigsoa, $fNomCiasoa, 
                                  $fNumTarpro, $fNumTrayle, $fCatLicenc, $fDirConduc, 
                                  $fCodPoseed, $fNomPoseed, $fCiuPoseed, $fDirPoseed,
                                  $mCodAgedes, $mCodContrs, $mCodAgenci, $fCodoperad, 
                                  $mCodGpsxxx, $mCodRemesa, $fBinHuella, $fNumViajex, $fDatgps2xx, $fNomAplica, 
                                  $fFotConduc, $fFotVehicu, $fNumIntent );


          }
          else {

            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado. Line ".$e -> getLine();
            $fExcept -> CatchError( $e -> getCode(), "CATCHING: ".$e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
          }
          
        }
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }
      return $fReturn;
    }
    else
    {
       $fMessageFinally = $fMessagesFinally;  
       return var_export([$flagCode, $fMessageFinally], true);      
      //       if( $fCodPlacax == 'UPQ075' )
      //         return $fMessageFinally;
       $Messages = [];
      if( $flagCode === 1 )
      {
        //Separa los errores de codigo 6001
        foreach ($fMessageFinally AS $code) {
           if($code[code] === '6001')
              $Result[] =   $code;           
        }
        //Separa el �rea del mensaje del error
        for ($i = 0; $i<sizeof($Result); $i++) {
          for($j = 0; $j < sizeof($Result) ; $j++ ) {
            $Messages[] = $Result[$i][message][$j];  
          } 
        }
        //Concadena los errores retornados de la validaci�n
        foreach ($Messages AS $fRow) {
          if($fRow["Col"] != '')
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]." ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
        
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
     
    
    }
  }


  /***********************************************************************
  * Funcion Inserta despacho con datos minimos                           *
  * @fn setSeguimPC                                                      *
  * @brief Inserta un despacho y le da salida automatica.                *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTranps    : int    Nit Transportadora.                   *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fDatFechax    : string Fecha de la salida YYYY-MM-DD HH:MM.  *
  * @param $fCodPlacax    : string Matricula del vehiculo.               *
  * @param $fCodRutaxx    : int    Codigo de la ruta faro.               *
  * @param $fObsComent    : string Observaciones.                        *
  * @param $fCodCiuori    : string Codigo ciudad origen.                 *
  * @param $fCodCiudes    : string Codigo ciudad destino.                *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setSeguimPC( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, 
                        $fDatFechax = NULL, $fCodPlacax = NULL, $fCodRutaxx = NULL, $fObsComent = NULL, 
                        $fCodCiuori = NULL, $fCodCiudes = NULL, $fConTelmov = NULL, $fNomConduc = NULL )
  {
    $fCodCiuori = trim( $fCodCiuori ) == '' ? NULL : $fCodCiuori; 
    $fCodCiudes = trim( $fCodCiudes ) == '' ? NULL : $fCodCiudes;
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "dat_fechax" => $fDatFechax, "cod_placax" => $fCodPlacax, "cod_rutaxx" => $fCodRutaxx, "obs_coment" => $fObsComent, 
                      "cod_ciuori" => $fCodCiuori, "cod_ciudes" => $fCodCiudes, "con_telmov" => $fConTelmov, "nom_conduc" => $fNomConduc );
    $fValidator = new Validator( $fInputs, "seguimpc_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    //unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setSeguimPC" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          

          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          $fVehicu = new Vehicu( $fExcept );
          if( !$fVehicu -> vehicuExists( $fCodPlacax ) )
          {
            $fQueryInsVehicu = $fVehicu -> setVehicu( $fCodPlacax, "KW", 999, 0, 2010, 1001, 1001, $fNomUsuari );
          }
          else
            $fQueryInsVehicu = NULL;

          $fSeguim = new Seguim( $fExcept );

          if( $fSeguim -> placaEnRuta( $fCodTranps, $fCodPlacax ) )
          {
            //Si intentan crear un despacho para una placa que esta en ruta, se le da llegada a los despachos que estan en ruta y se crea el despacho con la nueva informacion
            $fUpdLlegada = "UPDATE ".BASE_DATOS.".tab_despac_despac a, ".BASE_DATOS.".tab_despac_vehige b ".
                                 "SET a.fec_llegad = NOW(), ".
                                     "a.obs_llegad = 'Se ingreso otro despacho con esa placa, se le da llegada automatica' ".
                               "WHERE a.num_despac = b.num_despac ".
                                 "AND b.num_placax = '".$fCodPlacax."' ".
                                 "AND b.cod_transp = '".$fCodTranps."' ".
                                 "AND a.fec_salida IS NOT NULL ".
                                 "AND a.ind_anulad = 'R' ".
                                 "AND a.fec_llegad IS NULL".
                                 "";
            $fConsult -> ExecuteCons( $fUpdLlegada, "R" );
          }
          elseif( $fSeguim -> despacExists( $fCodManifi, $fCodTranps, $fCodPlacax ) )
          {
            throw new Exception( "Numero de manifiesto repetido.", "6001" );
          }

          if( $fSeguim -> rutaExists( $fCodRutaxx ) )
          {
            $fRoutCities = $fSeguim ->  getRoutCities( $fCodRutaxx );

            if( NULL == $fCodCiuori && NULL == $fCodCiudes )
            {
              $fCodPaiori = $fRoutCities["cod_paiori"];
              $fCodDepori = $fRoutCities["cod_depori"];
              $fCodCiuori = $fRoutCities["cod_ciuori"];

              $fCodPaides = $fRoutCities["cod_paides"];
              $fCodDepdes = $fRoutCities["cod_depdes"];
              $fCodCiudes = $fRoutCities["cod_ciudes"];
            }
            elseif( $fCodCiuori == $fRoutCities["cod_ciuori"] && $fCodCiudes == $fRoutCities["cod_ciudes"] )
            {
              $fCodPaiori = $fRoutCities["cod_paiori"];
              $fCodDepori = $fRoutCities["cod_depori"];

              $fCodPaides = $fRoutCities["cod_paides"];
              $fCodDepdes = $fRoutCities["cod_depdes"];
            }
            else
              throw new Exception( "La ciudad de origen y destino no corresponden con la ruta seleccionada.", "6001" );
          }
          elseif( NULL !== $fCodCiuori && NULL !== $fCodCiudes )
          {
            $fCodRutaxx = $fSeguim ->  getRoute( $fCodCiuori, $fCodCiudes, $fCodTranps );
            if( FALSE !== $fCodRutaxx )
            {
              $fLocation = getLocation( $fCodCiuori, $fExcept );
              if( FALSE !== $fLocation )
              {
                $fCodPaiori = $fLocation["CodPaisxx"];
                $fCodDepori = $fLocation["CodDepart"];
                $fCodCiuori = $fLocation["CodCiudad"];
                unset( $fLocation );
              }
              else
                throw new Exception( "La ciudad de origen no se encuentra registrada: ".$fCodCiuori, '6001' );

              $fLocation = getLocation( $fCodCiudes, $fExcept );
              if( FALSE !== $fLocation )
              {
                $fCodPaides = $fLocation["CodPaisxx"];
                $fCodDepdes = $fLocation["CodDepart"];
                $fCodCiudes = $fLocation["CodCiudad"];
                unset( $fLocation );
              }
              else
                throw new Exception( "La ciudad de destino no se encuentra registrada: ".$fCodCiudes, '6001' );
            }
            else
            {
              $fSeguim -> notifyRout( $fCodCiuori, $fCodCiudes, $fCodTranps, "satt_faro" );
              throw new Exception( "No se encuentra ruta disponible para origen ".$fCodCiuori." y destino ".$fCodCiudes.".", "6001" );
            }

          }
          else
            throw new Exception( "No se envio codigo de ruta y/o ciudad de origen y destino.", "6001" );

          $fNumDespac = $fSeguim -> getNextNumDespac();

          $fQueryInsDespac = $fSeguim -> setDespac( $fNumDespac, $fCodManifi, $fDatFechax, $fCodPaiori, $fCodDepori, 
                                                    $fCodCiuori, $fCodPaides, $fCodDepdes, $fCodCiudes, $fNomUsuari );


          $fQueryInsDesVehi = $fSeguim -> setDesVehi( $fNumDespac, $fCodTranps, $fCodRutaxx, 1001, 
                                                      $fCodPlacax, $fObsComent, $fNomUsuari );
          $fConsult -> StartTrans();

          if( NULL != $fQueryInsVehicu )
            $fConsult -> ExecuteCons( $fQueryInsVehicu, "R" );

          if ( $fConsult -> ExecuteCons( $fQueryInsDespac, "R" ) === FALSE )
          throw new Exception( "Error en Insert Despac.", "3001" );
            
          if ( $fConsult -> ExecuteCons( $fQueryInsDesVehi, "R" ) === FALSE )
          throw new Exception( "Error en Insert Despacho Vehiculo.", "3001" );
                      
          if( $fConTelmov != NULL )
          {
            //Se actualiza el numero de movil en el despacho para que le puedan realizar MA
            $fQueryUpDespac = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                                 "SET con_telmov = '".$fConTelmov."' ".
                               "WHERE num_despac = '".$fNumDespac."' ";

            $fConsult -> ExecuteCons( $fQueryUpDespac, "R" );
          }
          if( $fNomConduc != NULL )
          {
            //Se actualiza el nombre del conductor en el despacho para que le puedan realizar MA
            $fQueryUpDespacV = "UPDATE ".BASE_DATOS.".tab_despac_vehige ".
                                 "SET nom_conduc = '".$fNomConduc."' ".
                               "WHERE num_despac = '".$fNumDespac."' ";

            $fConsult -> ExecuteCons( $fQueryUpDespacV, "R" );
          }

          $fConsult -> Commit();
          
          //coounidas tiene contratado monitoreo activo
          $fIndVirtua = $fCodTranps == '900014965' ? TRUE : FALSE;
          if( FALSE !== $fSeguim -> setDesSegu( $fNumDespac, $fCodRutaxx, $fDatFechax, $fDatFechax, $fNomUsuari, $fIndVirtua, TRUE, $mCodContrs, $fCodPlacax, 
                              $fCodTranps ) )

            $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho, Y se da salida automatica.";
          else
          {
            $fSeguim -> notificarRutaSinPcs( $fCodCiuori, $fCodCiudes, $fCodTranps, "satt_faro" );
            $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho pero no se da plan de ruta, ya que no se encontraron puestos de control.";
          }

        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );

      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          //mail( "hugo.malagon@intrared.net", "Error Supervisar", $fReturn."\n".http_build_query( $fInputs, '', "\n" ),'From: soporte.ingenieros@intrared.net' );
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
       //mail( "hugo.malagon@intrared.net", "Error Supervisar", "code_resp:6001; msg_resp:".$fMessage."\n".http_build_query( $fInputs, '', "\n" ),'From: soporte.ingenieros@intrared.net' );
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
      {
        //mail( "hugo.malagon@intrared.net", "Error Supervisar", "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"]."\n".http_build_query( $fInputs, '', "\n" ),'From: soporte.ingenieros@intrared.net' );
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
      }
    }
  }

  /***********************************************************************
  * Funcion Inserta despacho con datos minimos                           *
  * @fn setSeguimFTP                                                     *
  * @brief Inserta un despacho y le da salida automatica.                *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTranps    : int    Nit Transportadora.                   *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fDatFechax    : string Fecha de la salida YYYY-MM-DD HH:MM.  *
  * @param $fCodPlacax    : string Matricula del vehiculo.               *
  * @param $fCodRutaxx    : int    Codigo de la ruta faro.               *
  * @param $fObsComent    : string Observaciones.                        *
  * @param $fCodCiuori    : string Codigo ciudad origen.                 *
  * @param $fCodCiudes    : string Codigo ciudad destino.                *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setSeguimFTP( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, 
                        $fDatFechax = NULL, $fCodPlacax = NULL, $fCodRutaxx = NULL, $fObsComent = NULL, 
                        $fCodCiuori = NULL, $fCodCiudes = NULL, $fConTelmov = NULL, $fNomConduc = NULL,
                        $fCodCondu2 = NULL, $fCodClien2 = NULL, $fNomClient = NULL, $fEmaClient = NULL )
  {
    $fCodCiuori = trim( $fCodCiuori ) == '' ? NULL : $fCodCiuori; 
    $fCodCiudes = trim( $fCodCiudes ) == '' ? NULL : $fCodCiudes;
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "dat_fechax" => $fDatFechax, "cod_placax" => $fCodPlacax, "cod_rutaxx" => $fCodRutaxx, "obs_coment" => $fObsComent, 
                      "cod_ciuori" => $fCodCiuori, "cod_ciudes" => $fCodCiudes, "con_telmov" => $fConTelmov, "nom_conduc" => $fNomConduc );
    $fValidator = new Validator( $fInputs, "seguimpc_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    //unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setSeguimPC" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          

          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          $fVehicu = new Vehicu( $fExcept );
          if( !$fVehicu -> vehicuExists( $fCodPlacax ) )
          {
            $fQueryInsVehicu = $fVehicu -> setVehicu( $fCodPlacax, "KW", 999, 0, 2010, 1001, 1001, $fNomUsuari );
          }
          else
            $fQueryInsVehicu = NULL;

          $fSeguim = new Seguim( $fExcept );

          if( $fSeguim -> despacExists( $fCodManifi, $fCodTranps, $fCodPlacax ) )
          {
            return "code_resp:6001; msg_resp:Numero de manifiesto repetido.";
          }
          
          $fQuerySelFinali = "SELECT 1 
                               FROM ".BASE_DATOS.".tab_despac_despac a, 
                                    ".BASE_DATOS.".tab_despac_vehige b 
                              WHERE a.num_despac = b.num_despac 
                                AND a.cod_manifi = '".$fCodManifi."' 
                                AND b.cod_transp = '".$fCodTranps."' 
                                AND b.num_placax = '".$fCodPlacax."' 
                                AND a.ind_anulad = 'R' 
                                AND b.ind_activo = 'S' 
                                AND a.fec_llegad IS NOT NULL 
                                AND a.fec_salida IS NOT NULL";
                          
          $fConsult -> ExecuteCons( $fQuerySelFinali );
          $finali = $fConsult -> RetMatrix( 'a' );
          
          if( count( $finali ) > 0 )
          {
            return "code_resp:6001; msg_resp:Numero de manifiesto finalizado.";
          }

          if( $fSeguim -> rutaExists( $fCodRutaxx ) )
          {
            $fRoutCities = $fSeguim ->  getRoutCities( $fCodRutaxx );

            if( NULL == $fCodCiuori && NULL == $fCodCiudes )
            {
              $fCodPaiori = $fRoutCities["cod_paiori"];
              $fCodDepori = $fRoutCities["cod_depori"];
              $fCodCiuori = $fRoutCities["cod_ciuori"];

              $fCodPaides = $fRoutCities["cod_paides"];
              $fCodDepdes = $fRoutCities["cod_depdes"];
              $fCodCiudes = $fRoutCities["cod_ciudes"];
            }
            elseif( $fCodCiuori == $fRoutCities["cod_ciuori"] && $fCodCiudes == $fRoutCities["cod_ciudes"] )
            {
              $fCodPaiori = $fRoutCities["cod_paiori"];
              $fCodDepori = $fRoutCities["cod_depori"];

              $fCodPaides = $fRoutCities["cod_paides"];
              $fCodDepdes = $fRoutCities["cod_depdes"];
            }
            else
              throw new Exception( "La ciudad de origen y destino no corresponden con la ruta seleccionada.", "6001" );
          }
          elseif( NULL !== $fCodCiuori && NULL !== $fCodCiudes )
          {
			  
            $fCodRutaxx = $fSeguim ->  getRouteFTP( $fCodCiuori, $fCodCiudes, $fCodTranps );
            if( FALSE !== $fCodRutaxx )
            {
              $fLocation = getLocation( $fCodCiuori, $fExcept );
              if( FALSE !== $fLocation )
              {
                $fCodPaiori = $fLocation["CodPaisxx"];
                $fCodDepori = $fLocation["CodDepart"];
                $fCodCiuori = $fLocation["CodCiudad"];
                unset( $fLocation );
              }
              else
                throw new Exception( "La ciudad de origen no se encuentra registrada: ".$fCodCiuori, '6001' );

              $fLocation = getLocation( $fCodCiudes, $fExcept );
              if( FALSE !== $fLocation )
              {
                $fCodPaides = $fLocation["CodPaisxx"];
                $fCodDepdes = $fLocation["CodDepart"];
                $fCodCiudes = $fLocation["CodCiudad"];
                unset( $fLocation );
              }
              else
                throw new Exception( "La ciudad de destino no se encuentra registrada: ".$fCodCiudes, '6001' );
            }
            else
            {
              $mNomRutaxx = $fSeguim -> notifyRoutInterf3( $fCodCiuori, $fCodCiudes, $fCodTranps );
              throw new Exception( "No se encuentra ruta disponible para origen ".$fCodCiuori." y destino ".$fCodCiudes.". ".$mNomRutaxx, "6001" );
            }

          }
          else
            throw new Exception( "No se envio codigo de ruta y/o ciudad de origen y destino.", "6001" );

          $fConsult -> StartTrans();

          $fQueryInsDespac = $fSeguim -> setDespac( $fNumDespac, $fCodManifi, $fDatFechax, $fCodPaiori, $fCodDepori, 
                                                    $fCodCiuori, $fCodPaides, $fCodDepdes, $fCodCiudes, $fNomUsuari );
          if ( $fConsult -> ExecuteCons( $fQueryInsDespac, "R" ) === FALSE ) {
            throw new Exception( "Error Creando el despacho.", "3001" );
          }

          $fNumDespac = $fSeguim -> getNextNumDespac();

          // ajuste para validar que el conductor exista en la BD, si existe se relaciona el conductor a la transportadora si no existe el conductor se deja el tercer por defecto 1001
          $fQueryConduc = "SELECT 1 FROM ".BASE_DATOS.".tab_tercer_conduc a WHERE a.cod_tercer = '".$fCodCondu2."' ";                
          $fConsult -> ExecuteCons( $fQueryConduc );
          $conduc = $fConsult -> RetMatrix( 'a' );
          if( count( $conduc ) > 0 )
          {
            $fQueryInsert = "INSERT IGNORE INTO ".BASE_DATOS.".tab_transp_tercer ( cod_transp, cod_tercer, ind_estado, fec_creaci, usr_creaci ) VALUES ('".$fCodTranps."','".$fCodCondu2."','1', NOW(), 'FTP Syscom' ) ";
            $fConsult -> ExecuteCons( $fQueryInsert, 'R' );
          } else {
            $fCodCondu2 = '1001';
          }

          $fQueryInsDesVehi = $fSeguim -> setDesVehi( $fNumDespac, $fCodTranps, $fCodRutaxx, $fCodCondu2, 
                                                      $fCodPlacax, $fObsComent, $fNomUsuari );

          if( NULL != $fQueryInsVehicu )
            $fConsult -> ExecuteCons( $fQueryInsVehicu, "R" );

          if ( $fConsult -> ExecuteCons( $fQueryInsDespac, "R" ) === FALSE )
          throw new Exception( "Error en Insert. Despacho 2", "3001" );
            
          if ( $fConsult -> ExecuteCons( $fQueryInsDesVehi, "R" ) === FALSE )
          throw new Exception( "Error en Insert. Despacho Vehiculo 2", "3001" );
                      
          
          $fConTelmov = $fConTelmov == '' || $fConTelmov == NULL ? 'NULL' : "'".$fConTelmov."'";
          $fEmaClient = $fEmaClient == '' || $fEmaClient == NULL ? 'NULL' : "'".$fEmaClient."'";
          $fCodClien2 = $fCodClien2 == '' || $fCodClien2 == NULL ? 'NULL' : "'".$fCodClien2."'";
          $fNomClient = $fNomClient == '' || $fNomClient == NULL ? 'NULL' : "'".$fNomClient."'";
          //Se actualiza el numero de movil en el despacho para que le puedan realizar MA
          $fQueryUpDespac = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                               "SET con_telmov = ".$fConTelmov.", ".
                               "    ema_client = ".$fEmaClient.", ".
                               "    cod_client2 = ".$fCodClien2.", ".
                               "    nom_client = ".$fNomClient." ".
                             "WHERE num_despac = '".$fNumDespac."' ";

          $fConsult -> ExecuteCons( $fQueryUpDespac, "R" );
          
          
          $fNomConduc = $fNomConduc == '' || $fNomConduc == NULL ? 'NULL' : "'".$fNomConduc."'";
          $fCodCondu2 = $fCodCondu2 == '' || $fCodCondu2 == NULL ? 'NULL' : "'".$fCodCondu2."'";
          //Se actualiza el nombre del conductor en el despacho para que le puedan realizar MA
          $fQueryUpDespacV = "UPDATE ".BASE_DATOS.".tab_despac_vehige ".
                               "SET nom_conduc = ".$fNomConduc.", ".
                               "    cod_condu2 = ".$fCodCondu2." ".
                             "WHERE num_despac = '".$fNumDespac."' ";

          $fConsult -> ExecuteCons( $fQueryUpDespacV, "R" );
         

          $fConsult -> Commit();
          
          //Dependiendo del servicio contratado de la transportadora se asignan los puestos de control
          $mResultTipser = $fSeguim -> getTipser( $fCodTranps, 1 );
          if( $mResultTipser["cod_tipser"] == 2 || $mResultTipser["cod_tipser"] == 3 )
            $fIndVirtua = TRUE;
          elseif( $mResultTipser["cod_tipser"] == 1 ) 
            $fIndVirtua = FALSE;

          if( FALSE !== $fSeguim -> setDesSeguFTP( $fNumDespac, $fCodRutaxx, $fDatFechax, $fDatFechax, $fNomUsuari, $fIndVirtua, TRUE, $mCodContrs, $fCodPlacax, 
                              $fCodTranps ) )

            $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho, Y se da salida automatica.";
          else
          {
            $fSeguim -> notificarRutaSinPcs( $fCodCiuori, $fCodCiudes, $fCodTranps, "satt_faro" );
            $fReturn = "code_resp:1000; msg_resp:Se inserto el despacho pero no se da plan de ruta, ya que no se encontraron puestos de control para la ruta ".$fCodRutaxx;
          }

        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );

      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
      {
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
      }
    }
  }

  /***********************************************************************
  * Funcion Inserta una llegada a un despacho                            *
  * @fn setLlegada                                                       *
  * @brief Inserta una llegada a un despacho.                            *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTranps    : int    Nit Transportadora.                   *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fFecLlegad    : string Fecha de la llegada YYYY-MM-DD HH:MM. *
  * @param $fObsLlegad    : string Observacion de la llegada.            *
  * @param $fNumPlacax    : string Placa.                                *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
 
  function setLlegada( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, $fFecLlegad = NULL, $fObsLlegad = NULL, $fNumPlacax = NULL, 
                       $NomProcess = NULL, $fNumDespac = NULL, $fNumManifi = NULL, $fNomAplica=NULL)
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" =>  $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "fec_llegad" => $fFecLlegad, "obs_llegad" => $fObsLlegad, "num_placax" => $fNumPlacax );

    $fValidator = new Validator( $fInputs, "llegada_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setLlegada" );
        $fReturn = NULL;

        if( $fNomAplica != NULL) // SI NO ES FARO , CARGA EL CLIENTE SATT_TQALA, EJEMPLO
        {
          if(!include_once('/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc') )
          {
            throw new Exception( "Aplicacion GL ".$fNomAplica."  en sat trafico no encontrada", "1999" );
          }
        }
        else if(file_exists( AplKon ) ){
          include_once( AplKon );
        }
        else
        {
          throw new Exception( "Aplicacion FARO en sat trafico no encontrada", "1999" );
          break;
        }

        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          $fQuerySelNumDes = "SELECT a.num_despac ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ";
        
          //parche debido a que en central los despachos 
          //tienen el nit de fortecem y en gl(generadores) si tienen el de la transportadora correspondiente
          if ( $fCodTransp != '900657956'){
            $fQueryNumDespac .= " AND b.cod_transp = '".trim( $fCodTransp )."' ";
          }
          
          if( $fNumPlacax !== NULL )
          {
            $fQuerySelNumDes .= " AND b.num_placax = '".$fNumPlacax."' ";
          }
          $fQuerySelNumDes .=   "AND a.ind_anulad = 'R' ".
                                "AND a.fec_llegad IS NULL ".
                                "AND a.fec_salida IS NOT NULL ".
                                "";

          $fConsult -> ExecuteCons( $fQuerySelNumDes );

          if( 0 != $fConsult -> RetNumRows() )
          {
            //Si el manifiesto recibido esta en ruta para esa transportadora se hace el update
            $fNumDespac = $fConsult -> RetMatrix( "a" );

            $fQueryUpdLleg = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                                "SET fec_llegad = '".$fFecLlegad."', ".
                                    "obs_llegad = 'WS - ".$fObsLlegad."', ".
                                    "usr_modifi = '".$fNomUsuari."', ".
                                    "fec_modifi = NOW() ".
                              "WHERE num_despac = '".$fNumDespac[0]["num_despac"]."' ";

            if( $fConsult -> ExecuteCons( $fQueryUpdLleg, "BRC" ) ){
              $fReturn = "code_resp:1000; msg_resp:Se dio llegada con exito en Sat Trafico";
              setFinalizaDespachoAPP("800232356", $fNumDespac[0]["num_despac"], $fConsult );
            }
            else
              $fReturn = "code_resp:1999; msg_resp:No se pudo dar llegada en Sat Trafico";
          }
          else
          {
            $fQuerySelFinali = "SELECT 1 ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ";
            if( $fNumPlacax !== NULL )
            {
              $fQuerySelFinali .= " AND b.num_placax = '".$fNumPlacax."' ";
            }
            $fQuerySelFinali .= "AND a.ind_anulad = 'R' ".
                                "AND a.fec_llegad IS NOT NULL ".
                                "AND a.fec_salida IS NOT NULL ";

            $fConsult -> ExecuteCons( $fQuerySelFinali );
            
            if( 0 != $fConsult -> RetNumRows() ){
              $fReturn = "code_resp:1999; msg_resp:El despacho con manifiesto ".$fCodManifi." se encuentra finalizado en Sat Trafico"; 
            }
            else
              $fReturn = "code_resp:1999; msg_resp:No se encontro un despacho para el manifiesto ".$fCodManifi." en Sat Trafico";
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );

      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

  /***********************************************************************
  * Funcion Anula un despacho                                            *
  * @fn setAnulad                                                         *
  * @brief Anula a un despacho.                                          *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTranps    : int    Nit Transportadora.                   *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fNumPlacax    : string Placa.                                *
  * @param $fFecAnulad    : string Fecha de la llegada YYYY-MM-DD HH:MM. *
  * @param $fObsanulad    : string Observacion de la llegada.            *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setAnulad( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, $fNumPlacax = NULL, $fFecAnulad = NULL, $fObsAnulad = NULL, $fNomAplica = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" =>  $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "num_placax" => $fNumPlacax, "fec_anulad" => $fFecAnulad, "obs_anulad" => $fObsAnulad  );

    $fValidator = new Validator( $fInputs, "anulad_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setAnulad" );
        $fReturn = NULL;

        $fNomAplica = $fNomAplica == 'Webservice - Anular Despacho' ? NULL : $fNomAplica;

        if( $fNomAplica != NULL) // SI NO ES FARO , CARGA EL CLIENTE SATT_TQALA, EJEMPLO
        {
          if(!include_once('/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc') )
          {
            throw new Exception( "Aplicacion GL ".$fNomAplica."  en sat trafico no encontrada", "1999" );
          }
        }
        else if(file_exists( AplKon ) ){
          include_once( AplKon );
        }
        else
        {
          throw new Exception( "Aplicacion FARO en sat trafico no encontrada", "1999" );
          break;
        }


        
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          $fQuerySelNumDes = "SELECT a.num_despac ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ".
                                "AND b.num_placax = '".$fNumPlacax."' ".
                                "AND a.ind_anulad != 'A' ".
                                "AND a.fec_llegad IS NULL";

          $fConsult -> ExecuteCons( $fQuerySelNumDes );
          
          
          if( 0 != $fConsult -> RetNumRows() )
          {
            //Si el manifiesto recibido esta en ruta para esa transportadora se hace el update
            $fNumDespac = $fConsult -> RetMatrix( "a" );
            $fConsult -> StartTrans();
            
            $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                         SET ind_activo = 'N',
                             obs_anulad = '".$fObsAnulad."',
                             usr_modifi = '".$fNomUsuari."',
                             fec_modifi = NOW()
                       WHERE num_despac = '".$fNumDespac[0]["num_despac"]."'";

            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );
          
            $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                         SET  ind_anulad = 'A',
                              usr_modifi = '".$fNomUsuari."',
                              fec_modifi = NOW()
                        WHERE num_despac = '".$fNumDespac[0]["num_despac"]."'";
          
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );
            
            $fConsult -> Commit();
            $fReturn = "code_resp:1000; msg_resp:Se anulo con exito en Sat Trafico";
            setFinalizaDespachoAPP("800232356", $fNumDespac[0]["num_despac"], $fConsult );
          }
          else
          {
            $fQuerySelFinali = "SELECT 1 ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ".
                                "AND b.num_placax = '".$fNumPlacax."' ".
                                "AND a.ind_anulad = 'R' ".
                                "AND a.fec_llegad IS NOT NULL ".
                                "AND a.fec_salida IS NOT NULL ";

            $fConsult -> ExecuteCons( $fQuerySelFinali );
            
            if( 0 != $fConsult -> RetNumRows() )
              $fReturn = "code_resp:1999; msg_resp:El despacho con manifiesto ".$fCodManifi." se encuentra finalizado en Sat Trafico";
            else
              $fReturn = "code_resp:1999; msg_resp:No se encontro un despacho para el manifiesto ".$fCodManifi." en Sat Trafico";
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

  /***********************************************************************
  * Funcion Reversa la salida de un despacho                             *
  * @fn setRevSalida                                                     *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTranps    : int    Nit Transportadora.                   *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fNumPlacax    : string Placa.                                *
  * @param $fFecAnulad    : string Fecha de la llegada YYYY-MM-DD HH:MM. *
  * @param $fObsanulad    : string Observacion de la llegada.            *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setRevSalida( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, $fNumPlacax = NULL, $fFecAnulad = NULL, $fObsAnulad = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" =>  $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "num_placax" => $fNumPlacax, "fec_anulad" => $fFecAnulad, "obs_anulad" => $fObsAnulad  );

    $fValidator = new Validator( $fInputs, "anulad_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setRevSalida" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

            $fQuerySelNumDes = "SELECT a.num_despac, a.fec_salida ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ".
                                "AND b.num_placax = '".$fNumPlacax."' ".
                                "AND a.ind_anulad != 'A' ".
                                "AND a.fec_salida IS NOT NULL ".
                                "AND a.fec_llegad IS NULL ".
                                "AND ( SELECT COUNT( y.cod_contro ) FROM ".BASE_DATOS.".tab_despac_noveda y WHERE y.num_despac = a.num_despac ) = 0 ".
                                "AND ( SELECT COUNT( z.cod_contro ) FROM ".BASE_DATOS.".tab_despac_contro z WHERE z.num_despac = a.num_despac ) = 0 ";
          $fConsult -> ExecuteCons( $fQuerySelNumDes );

          if( 0 != $fConsult -> RetNumRows() )
          {
            //Si el manifiesto recibido esta en ruta para esa transportadora se hace el update
            $fNumDespac = $fConsult -> RetMatrix( "a" );
            $fConsult -> StartTrans();
            
            $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                         SET ind_activo = 'R',
                             obs_anulad = '".$fObsAnulad.'.Fecha Salida: '.$fNumDespac[0]["fec_salida"]."\n',
                             usr_modifi = '".$fNomUsuari."',
                             fec_modifi = NOW()
                       WHERE num_despac = ".$fNumDespac[0]["num_despac"]."";
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );

            $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                         SET fec_salida = NULL,
                             ind_anulad = 'A',
                             usr_modifi = '".$fNomUsuari."',
                             fec_modifi = NOW()
                       WHERE num_despac = ".$fNumDespac[0]["num_despac"]."";

            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );
   
            $fConsult -> Commit();
            $fReturn = "code_resp:1000; msg_resp:Se reverso la salida con exito en Sat Trafico";
            setFinalizaDespachoAPP("800232356", $fNumDespac[0]["num_despac"], $fConsult );
          }
          else
          {
            $fReturn = "code_resp:1999; msg_resp:No se encontro un despacho que cumpla con los requisitos para reversar salida ( que este en ruta y sin novedades ) en Sat Trafico";
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

  /*************************************************************************
  * Funcion Reversa la llegada de un despacho                              *
  * @fn setRevLlegada                                                      *
  * @param $fNomUsuari    : string Usuario.                                *
  * @param $fPwdClavex    : string Clave.                                  *
  * @param $fCodTranps    : int    Nit Transportadora.                     *
  * @param $fCodManifi    : string Numero del manifiesto.                  *
  * @param $fNumPlacax    : string Placa.                                  *
  * @param $fFecAnulad    : string Fecha de la reversion YYYY-MM-DD HH:MM. *
  * @param $fObsanulad    : string Observacion de la reversion.            *
  * @return string mensaje de respuesta.                                   *
  **************************************************************************/
  function setRevLlegada( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, $fNumPlacax = NULL, $fFecAnulad = NULL, $fObsAnulad = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" =>  $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "num_placax" => $fNumPlacax, "fec_anulad" => $fFecAnulad, "obs_anulad" => $fObsAnulad  );

    $fValidator = new Validator( $fInputs, "anulad_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setRevLlegada" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

            $fSeguim = new Seguim( $fExcept );
            if( $fSeguim -> despacExists( $fCodManifi, $fCodTranps, $fNumPlacax ) )
              throw new Exception( "Fallo la reversion de la llegada en SAT Trafico. Ya se encuentra en ruta un despacho con manifiesto ".$fCodManifi." y placa ".$fNumPlacax.".", "6001" );
            
            $fQuerySelNumDes = "SELECT a.num_despac ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ".
                                "AND b.num_placax = '".$fNumPlacax."' ".
                                "AND a.ind_anulad != 'A' ".
                                "AND a.fec_llegad IS NOT NULL ".
                           "ORDER BY a.fec_creaci DESC";
          $fConsult -> ExecuteCons( $fQuerySelNumDes );

          if( 0 != $fConsult -> RetNumRows() )
          {
            //Si el manifiesto recibido esta finalizado para esa transportadora se hace el update
            $fNumDespac = $fConsult -> RetMatrix( "a" );
            $fConsult -> StartTrans();
            
            $query = "UPDATE ".BASE_DATOS.".tab_despac_despac
                 SET fec_llegad = NULL,
                     usr_modifi = '".$fNomUsuari."',
                     fec_modifi = NOW()
               WHERE num_despac = '".$fNumDespac[0]["num_despac"]."'";
            
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );

   
            $fConsult -> Commit();
            $fReturn = "code_resp:1000; msg_resp:Se reverso la llegada con exito en Sat Trafico";
            setDespachoAPP("800232356", $fNumDespac[0]["num_despac"], $fConsult );
          }
          else
          {
            $fReturn = "code_resp:1999; msg_resp:No se encontro un despacho finalizado para reversar llegada en Sat Trafico";
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }


  /***********************************************************************
  * Funcion para finalizar un despacho en la APP de avansat en central   *
  * @fn setDespacPdf                                                     *
  * @brief Verifica si una ruta existe.                                  *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodManifi: string Codigo de la ruta Faro.                   *
  * @param $fCodPlacax: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/  
  function setDespachoAPP($fNitTransp = '', $fNumDespac = '', $fConsult )  
  {
    try {
          // busca si hay registros de fNumDespac con ese fNitFajobe en tabla de destinatarios
          //$mSql = "SELECT num_despac, num_docume  FROM ".BASE_DATOS.".tab_despac_vehige a WHERE a.num_despac = '".$fNumDespac."' AND a.cod_transp = '".$fNitTransp."' "; 
          $mSql = "SELECT num_despac  FROM ".BASE_DATOS.".tab_despac_vehige a WHERE a.num_despac = '".$fNumDespac."' AND a.cod_transp = '".$fNitTransp."' "; 
          $fConsult -> ExecuteCons( $mSql );
          $mMatriz = $fConsult -> RetMatrix( 'a' ); 


          // si hay resultado de registros, se procede a finalizar el despacho en la central
          if(sizeof($mMatriz) > 0)
          {
            // Consulta si la empresa (del nit) tiene activa interfaz con Movil para despachos
            $mSql = "SELECT ind_estado FROM ".BASE_DATOS.".tab_interf_parame a WHERE a.cod_operad = '85' AND a.cod_transp = '".$fNitTransp."' AND ind_estado = 1 "; 
            $fConsult -> ExecuteCons( $mSql );
            $mMatriz = $fConsult -> RetMatrix( 'a' ); 
            
            if ( $mMatriz[0]['ind_estado']) 
            {
              // Incluye la api para enviar el despacho a la central
              require_once "/var/www/html/ap/interf/app/APIClienteApp/controlador/DespachoControlador.php";
              $controlador = new DespachoControlador();
              $response    = $controlador->registrar($fConsult, $fNumDespac, $fNitTransp);    
              $opfl = fopen("/var/www/html/ap/interf/app/faro/logs/Despachos_moviles".date("Ymd").".txt", "a+");
              fwrite($opfl, "---------------------------".date("Y-m-d H:i:s")."-------------------------------\n");
              fwrite($opfl, "Accion           : Generar despacho en APP");
              fwrite($opfl, "Despacho         : ".$fNumDespac."\n");
              fwrite($opfl, "Manifiesto       : ".$fCodManifi."\n");
              fwrite($opfl, "Placa            : ".$fCodPlacax."\n");
              fwrite($opfl, "Conductor ID     : ".$fCodConduc."\n");
              fwrite($opfl, "Conductor Nom    : ".$fNomConduc."\n");
              fwrite($opfl, "Response API APP : ".json_encode($response)."\n"); 
              fclose($opfl);

            }
          }

    } catch (Exception $e) {
      
    }
  }  

  /***********************************************************************
  * Funcion para finalizar un despacho en la APP de avansat en central   *
  * @fn setDespacPdf                                                     *
  * @brief Verifica si una ruta existe.                                  *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodManifi: string Codigo de la ruta Faro.                   *
  * @param $fCodPlacax: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/  
  function setFinalizaDespachoAPP($fNitFajobe = '', $fNumDespac = '', $fConsult )  
  {
    try {
          // busca si hay registros de fNumDespac con ese fNitFajobe en tabla de destinatarios
          $mSql = "SELECT num_despac, num_docume  FROM ".BASE_DATOS.".tab_despac_destin a WHERE a.num_despac = '".$fNumDespac."' AND a.cod_genera = '".$fNitFajobe."' "; 
          $fConsult -> ExecuteCons( $mSql );
          $mMatriz = $fConsult -> RetMatrix( 'a' ); 

          // si hay resultado de registros, se procede a finalizar el despacho en la central
          if(sizeof($mMatriz) > 0)
          {
            // Consulta si la empresa (del nit) tiene activa interfaz con Movil para despachos
            $mSql = "SELECT ind_estado FROM ".BASE_DATOS.".tab_interf_parame a WHERE a.cod_operad = '85' AND a.cod_transp = '".$fNitFajobe."' AND ind_estado = 1 "; 
            $fConsult -> ExecuteCons( $mSql );
            $mMatriz = $fConsult -> RetMatrix( 'a' ); 
            if ( $mMatriz[0]['ind_estado']) 
            {
              // Incluye la api para enviar el despacho a la central
              require_once "/var/www/html/ap/interf/app/APIClienteApp/controlador/DespachoControlador.php";
              $controlador = new DespachoControlador();
              $response    = $controlador->finalizar($fConsult, $fNumDespac, $fNitFajobe);    
              $opfl = fopen("/var/www/html/ap/interf/app/faro/logs/Despacho_Fajober_".date("Ymd").".txt", "a+");
              fwrite($opfl, "---------------------------".date("Y-m-d H:i:s")."-------------------------------\n");
              fwrite($opfl, "Accion           : Finalizar despacho en APP");
              fwrite($opfl, "Despacho         : ".$fNumDespac."\n");
              fwrite($opfl, "Manifiesto       : ".$fCodManifi."\n");
              fwrite($opfl, "Placa            : ".$fCodPlacax."\n");
              fwrite($opfl, "Conductor ID     : ".$fCodConduc."\n");
              fwrite($opfl, "Conductor Nom    : ".$fNomConduc."\n");
              fwrite($opfl, "Response API APP : ".json_encode($response)."\n"); 
              fclose($opfl);

            }
          }
    } catch (Exception $e) {
      
    }
  }
  // PDF 
  /***********************************************************************
  * Funcion Retorna Url para descargar archivo PDF                       *
  * @fn setDespacPdf                                                     *
  * @brief Verifica si una ruta existe.                                  *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodManifi: string Codigo de la ruta Faro.                   *
  * @param $fCodPlacax: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/  
  function setDespacPdf ($fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodManifi = NULL, $fCodPlacax = NULL)
  {
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, "cod_manifi" => $fCodManifi,"cod_placax" => $fCodPlacax );
   
    $fValidator = new Validator( $fInputs, "seguim_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    
    if("1000" === $fMessages["code"])
    {
     try
      {
        include_once( AplKon );        
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setDespacPdf" );      

        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );      
        
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fSeguim = new Seguim( $fExcept );
          $CodPerfilUsuari = getCodPerfil($fNomUsuari, $fPwdClavex, $fExcept ); // /var/www/html/ap/interf/lib/funtions/General.fnc,php
         
          $fQueryTramoPdf = $fSeguim -> setDataPdf( $fCodTranps, $fCodManifi , $fCodPlacax, $CodPerfilUsuari );
         //return $fQueryTramoPdf;
          if($fQueryTramoPdf == NULL)
          { 
            return FALSE;
          }
          else
          {
            $fDataPdf = $fSeguim -> setMakePdf( $fQueryTramoPdf ); 

            return $fDataPdf ; //file_get_contents( 
             
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );        
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

 /************************************************************************
  * Funcion Verifica ruta                                                *
  * @fn routExists                                                       *
  * @brief Verifica si una ruta existe.                                  *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodCiuori: string Codigo dane de la ciudad origen.          *
  * @param $fCodCiudes: string Codigo dane de la ciudad destino.         *
  * @param $fCodRutaxx: string Codigo de la ruta Faro.                   *
  * @param $fNomRutaxx: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/
  function routExists( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodCiuori = NULL, $fCodCiudes = NULL, $fCodRutaxx, $fNomRutaxx = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_ciuori" => $fCodCiuori, 
                      "cod_ciudes" => $fCodCiudes, "cod_rutaxx" => $fCodRutaxx, "nom_rutaxx" => $fNomRutaxx );

    $fValidator = new Validator( $fInputs, "routexists_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "routExists" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fLocation = getLocation( $fCodCiuori, $fExcept );
          if( FALSE === $fLocation )
            throw new Exception( "Ciudad de origen no existente.", "6001" );

          $fLocation = getLocation( $fCodCiudes, $fExcept );
          if( FALSE === $fLocation )
            throw new Exception( "Ciudad de destino no existente.", "6001" );

          $fDespac = new DespacSat( $fExcept );

          $fCodRutaxx = $fDespac -> getRuta( trim( $fCodRutaxx ), trim( $fCodCiuori ), trim( $fCodCiudes ), trim( $fNomRutaxx ) );//Retorna el codigo de ruta exacto que cumpla con los parametros de entrada.
          if( FALSE === $fCodRutaxx )
            $fReturn = "code_resp:1999; msg_resp:La ruta proporcionada no existente.";
          else
            $fReturn = "code_resp:1000; msg_resp:La ruta existe en el sistema.";
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

 /************************************************************************
  * Funcion Inserta ruta                                                 *
  * @fn setRout                                                          *
  * @brief Inserta una nueva ruta.                                       *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodCiuori: string Codigo dane de la ciudad origen.          *
  * @param $fCodCiudes: string Codigo dane de la ciudad destino.         *
  * @param $fCodRutaxx: string Codigo de la ruta Faro.                   *
  * @param $fNomRutaxx: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/
  function setRout( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodCiuori = NULL, $fCodCiudes = NULL, $fNomRutaxx = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_ciuori" => $fCodCiuori, 
                      "cod_ciudes" => $fCodCiudes, "nom_rutaxx" => $fNomRutaxx );

    $fValidator = new Validator( $fInputs, "routexists_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setRout" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fLocation = getLocation( $fCodCiuori, $fExcept );
          if( FALSE !== $fLocation )
          {
            $fCodPaiori = $fLocation["CodPaisxx"];
            $fCodDepori = $fLocation["CodDepart"];
            $fCodCiuori = $fLocation["CodCiudad"];
            unset( $fLocation );
          }
          else
            throw new Exception( "Ciudad de origen no existente.", "6001" );

          $fLocation = getLocation( $fCodCiudes, $fExcept );
          if( FALSE !== $fLocation )
          {
            $fCodPaides = $fLocation["CodPaisxx"];
            $fCodDepdes = $fLocation["CodDepart"];
            $fCodCiudes = $fLocation["CodCiudad"];
            unset( $fLocation );
          }
          else
            throw new Exception( "Ciudad de destino no existente.", "6001" );

          $fQuerySelMAxRoute = "SELECT MAX( cod_rutasx ) AS cod_rutasx ".
                                 "FROM ".BASE_DATOS.".tab_genera_rutasx ";

          $fConsult -> ExecuteCons( $fQuerySelMAxRoute );
          $fCodRutasx = $fConsult -> RetMatrix( "a" );
          $fCodRutasx[0]["cod_rutasx"]++;

          $fQueryInsRoute = "INSERT INTO ".BASE_DATOS.".tab_genera_rutasx ".
                              "( cod_rutasx, nom_rutasx, cod_paiori, cod_depori, ".
                                "cod_ciuori, cod_paides, cod_depdes, cod_ciudes, ".
                                "ind_estado, usr_creaci, fec_creaci ) ".
                            "VALUES ( '".$fCodRutasx[0]["cod_rutasx"]."', '".$fNomRutaxx."', ".
                                     "'".$fCodPaiori."', '".$fCodDepori."', '".$fCodCiuori."', ".
                                     "'".$fCodPaides."', '".$fCodDepdes."', '".$fCodCiudes."', ".
                                     "'1', '".$fNomUsuari."', NOW() ) ";

          if( $fConsult -> ExecuteCons( $fQueryInsRoute, "BRC" ) )
            $fReturn = "code_resp:1000; msg_resp:".$fCodRutasx[0]["cod_rutasx"];
          else
            $fReturn = "code_resp:1999; msg_resp:La ruta no pudo ser creada.";

        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

 /************************************************************************
  * Funcion Inserta ruta                                                 *
  * @fn setRout                                                          *
  * @brief Inserta una nueva ruta.                                       *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  * @param $fCodTranps: string Nit de la transportadora.                 *
  * @param $fCodCiuori: string Codigo dane de la ciudad origen.          *
  * @param $fCodCiudes: string Codigo dane de la ciudad destino.         *
  * @param $fCodRutaxx: string Codigo de la ruta Faro.                   *
  * @param $fNomRutaxx: string Nombre de la ruta Faro.                   *
  * @return string respuesta del webservice.                             *
  ************************************************************************/
  function setPc( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, $fCodRutaxx = NULL, 
                  $fCodContro = NULL, $fNomContro = NULL, $fValDuraci = NULL, $fValDistan = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_rutaxx" => $fCodRutaxx, 
                      "cod_contro" => $fCodContro, "nom_contro" => $fNomContro, "val_duraci" => $fValDuraci, 
                      "val_distan" => $fValDistan, "cod_tranps" => $fCodTranps );

    $fValidator = new Validator( $fInputs, "puesto_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setPC" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fDespac = new DespacSat( $fExcept );
          $fCodContro = $fDespac -> getPcontro( trim( $fCodContro ), trim( $fNomContro ) );

          if( FALSE !== $fCodContro )
          {
            $fQuerySelContro = "SELECT cod_contro ".
                                 "FROM ".BASE_DATOS.".tab_tercer_contro ".
                                "WHERE cod_contro = '".$fCodContro."' ".
                                  "AND cod_tercer = '".trim( $fCodTranps )."' ";

            $fConsult -> ExecuteCons( $fQuerySelContro );
            $fContro = $fConsult -> RetMatrix( "a" );

            if( 0 !== count( $fContro ) )
            {
              $fQueryInsRoute = "INSERT INTO ".BASE_DATOS.".tab_genera_rutcon ".
                                  "( cod_rutasx, cod_contro, val_duraci, val_distan, ind_estado, ".
                                    "usr_creaci, fec_creaci ) ".
                                "VALUES ( '".$fCodRutaxx."', '".$fCodContro."', '".$fValDuraci."', '".$fValDistan."', ".
                                         "'1', '".$fNomUsuari."', NOW() ) ";

              if( $fConsult -> ExecuteCons( $fQueryInsRoute, "BRC" ) )
                $fReturn = "code_resp:1000; msg_resp:Puesto de control asignado con exito.";
              else
                $fReturn = "code_resp:1999; msg_resp:Puesto de control no asignado.";
            }
            else
              $fReturn = "code_resp:6001; msg_resp:El puesto de control no esta contratado por la empresa.";
          }
          else
            $fReturn = "code_resp:6001; msg_resp:El puesto de control no existe.";
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
  
  /***********************************************************************
  * Funcion Inserta una novedad en Intracarga                            *
  * @fn setPCIntracarga                                                  *
  * @brief Inserta una novedad en Intracarga.                            *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodManifi    : string Numero del manifiesto.                *
  * @param $fFecNoveda    : string Fecha de la llegada YYYY-MM-DD.       *
  * @param $fHorNoveda    : string Hora de la llegada HH:MM:SS.          *
  * @param $fNomContro    : string Nombre del Puesto de Control.         *
  * @param $fDesObserv    : string Observacion de la Novedad.            *
  * @param $fNomLlavex    : string Llave para autenticacion.             *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setPCIntracarga( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodManifi = NULL, 
                            $fFecNoveda = NULL, $fHorNoveda = NULL, $fNomContro = NULL, 
                            $fDesObserv = NULL, $fNomLlavex = NULL, $fFecPronov = NULL, 
                            $fHorPronov = NULL, $fNumPlacax = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_manifi" => $fCodManifi, 
                      "fec_noveda" => $fFecNoveda, "hor_noveda" => $fHorNoveda, "nom_contro" => $fNomContro,
                      "des_observ" => $fDesObserv, "nom_llavex" => $fNomLlavex, "fec_pronov" => $fFecPronov,
                      "hor_pronov" => $fHorPronov, "num_placax" => $fNumPlacax);

    $fValidator = new Validator( $fInputs, "pcintracarga_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setPCIntracarga" );
        $fReturn = NULL;

        if( $fNomLlavex == '59a68t7j95s4t96dS2g9A' )
        {
          ini_set( "soap.wsdl_cache_enabled", "0" );
          $oSoapClient = new soapclient( 'http://www.intracarga.com.co/actualizartrafico/service1.asmx?wsdl', array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );
      
          $parametros = array( "ReportaPuestoControlProx" => array(  "Usuario" => $fNomUsuari, 
                                                                 "Clave" => $fPwdClavex, 
                                                                 "Manifiesto" => $fCodManifi, 
                                                                 "Fecha" => $fFecNoveda, 
                                                                 "Hora" => $fHorNoveda, 
                                                                 "Puesto_control" => $fNomContro, 
                                                                 "Observaciones" => $fDesObserv,
                                                                 "FechaProximoPC" => $fFecPronov,
                                                                 "HoraProximoPC" => $fHorPronov,
                                                                 "strPlacaVehiculo" => $fNumPlacax ) );
                                                                 
          $result = $oSoapClient -> __call( "ReportaPuestoControlProx", $parametros );    
          
          $mCodResp = explode( ":", $result -> ReportaPuestoControlProxResult );
          
          if( "000125" != $mCodResp[0] )
            $fReturn = "code_resp:1999; msg_resp:Ocurrio error en el metodo ReportaPuestoControlProx de Intracarga - ".$result -> ReportaPuestoControlProxResult;
          else
            $fReturn = "code_resp:1000; msg_resp:Se reporto la novedad a Intracarga con exito";
        }
        else
          throw new Exception( "Autenticacion fallida.", "1002" );

      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), ' ', $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

  /***********************************************************************
  * Funcion Obtiene los Puestos de Control Contratados                   *
  * @fn getPCsContratados                                                *
  * @brief Obtiene los Puestos de Control Contratados                    *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTransp    : string Nit Transportadora.                   *
  * @param $fCodTipdes    : string Codigo tipo de despacho.              *
  * @param $fCodRutaxx    : string Codigo de la ruta.                    *
  * @param $fTieHolgur    : int    Tiempo de holgura.                    *
  * @return string or Array mensaje de respuesta.                        *
  ************************************************************************/
  function getPCsContratados( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fCodTipdes = NULL, $fCodRutaxx = NULL, $fTieHolgur = 20 )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTransp, 
                  "cod_tipdes" => $fCodTipdes, "cod_rutaxx" => $fCodRutaxx, "tie_holgur" => $fTieHolgur );

    $fValidator = new Validator( $fInputs, "pccontratados_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );
    
    //Se verifica si pasa las validaciones
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        //Se configura la clase Error
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getPCsContratados" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        //Se verifica Si se autentica
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTransp ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
            
          if( 1 != $fCodTipdes && 2 != $fCodTipdes )
            throw new Exception( "El tipo de Despacho debe ser 1 (Urbano), 2 (Nacional).", "6001" );
            
          $fSeguim = new Seguim( $fExcept );
          
          if( $fTieHolgur == '' || $fTieHolgur == NULL )
          {
            $fTieHolgur = 20;
          }

          $fPCs = $fSeguim -> getPCFromTipser( $fCodTransp, $fCodTipdes, $fCodRutaxx, $fTieHolgur );
          if( FALSE === $fPCs )
          {
            throw new Exception( "No se encuentran configuraciones activas para la transportadora", "6001" );
            break;
          }
          else
          {
            if( $fPCs['tie_contro'] <= $fTieHolgur && $fPCs['cod_tipser'] != '1' )
              throw new Exception( "El tiempo de seguimiento es menor o igual al tiempo de holgura", "6001" );
            
            if( count( $fPCs['pcs'] ) == 0 )
              throw new Exception( "No se encontraron puestos de control para el servicio contratado por la transportadora en la ruta. Comuniquese con un operador al ".DatFar, "6001" );
          
            $fReturn['arr_pcscon'] = $fPCs['pcs'];
            $fReturn['tie_contro'] = $fPCs['tie_contro'];
            $fReturn['cod_tipser'] = $fPCs['cod_tipser'];
            $fReturn['cod_rutaxx'] = $fPCs['cod_rutaxx'];
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se retornaron puestos satisfactoriamente';
            return $fReturn;
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTransp, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }
  
  /***********************************************************************
  * Funcion Obtiene todas las rutas activas segun origen y destino             *
  * @fn getRutas                                                               *
  * @param $fNomUsuari    : string Usuario.                                    *
  * @param $fPwdClavex    : string Clave.                                      *
  * @param $fCodCiuori    : string Codigo ciudad de origen.                    *
  * @param $fCodCiudes    : string Codigo ciudad de destino.                   *
  * @param $fNotRutasx    : string string de rutas para excluir del resultado. *
  * @return string or Array mensaje de respuesta.                              *
  *****************************************************************************/
  function getRutas( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodCiuori = NULL, $fCodCiudes = NULL, 
                     $fNotRutasx = NULL, $fCodTransp = NULL, $fCodTipdes = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_ciuori" => $fCodCiuori, 
                      "cod_ciudes" => $fCodCiudes, "cod_transp" => $fCodTransp, "cod_tipdes" => $fCodTipdes );

    $fValidator = new Validator( $fInputs, "getrutas_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );
    
    //Se verifica si pasa las validaciones
    if( "1000" === $fMessages["code"] )
    {
      try
      {
		    //Se configura la clase Error
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getRutas" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

		    //Se verifica Si se autentica
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTransp ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
            
          if( 1 != $fCodTipdes && 2 != $fCodTipdes )
            throw new Exception( "El tipo de Despacho debe ser 1 (Urbano), 2 (Nacional).", "6001" );
            
          $fSeguim = new Seguim( $fExcept );
          $fRutasx = $fSeguim -> getRutas( $fCodCiuori, $fCodCiudes, $fNotRutasx, $fCodTransp, $fCodTipdes );
          if( !is_array( $fRutasx ) )
          {
            $fReturn['cod_respon'] = '6001';
            $fReturn['msg_respon'] = $fRutasx;
            return $fReturn;
          }
          else
          {
            $fReturn['arr_rutasx'] = $fRutasx['rutas'];
            $fReturn['cod_tipser'] = $fRutasx['tipser']['cod_tipser'];
            $fReturn['tie_contro'] = $fRutasx['tipser']['tie_contro'];
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se retornaron rutas satisfactoriamente';
            return $fReturn;
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos. USUARIO=".$fNomUsuari."CLAVE=". $fPwdClavex, "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTransp, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }
  
  /***********************************************************************
  * Funcion Obtiene el tipo de servicio y el tiempo contratado           *
  * @fn getTipser                                                        *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @param $fCodTransp    : string Codigo transportadora.                *
  * @param $fCodTipdes    : string Codigo Tipo despacho.                 *
  * @return string mensaje de respuesta.                                 *
  ***********************************************************************/
  function getTipser( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fCodTipdes = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_transp" => $fCodTransp, 
                      "cod_tipdes" => $fCodTipdes );

    $fValidator = new Validator( $fInputs, "gettipser_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );
    
    //Se verifica si pasa las validaciones
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        //Se configura la clase Error
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getTipser" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        //Se verifica Si se autentica
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTransp ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
            
          if( 1 != $fCodTipdes && 2 != $fCodTipdes )
            throw new Exception( "El tipo de Despacho debe ser 1 (Urbano), 2 (Nacional).", "6001" );
            
          $fSeguim = new Seguim( $fExcept );
          $fTipser = $fSeguim -> getTipser( $fCodTransp, $fCodTipdes );
          if( $fTipser == FALSE )
          {
            throw new Exception( "No existen configuraciones activas en Sat Trafico para la transportadora", "6001" );
            break;
          }
          else
          {
            $fReturn['cod_tipser'] = $fTipser['cod_tipser'];
            $fReturn['tie_contro'] = $fTipser['tie_contro'];
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se retorno el tipo de servicio satisfactoriamente';
            return $fReturn;
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTransp, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }

  /***********************************************************************
  * Funcion Obtiene todas las novedades                                  *
  * @fn getRutas                                                         *
  * @param $fNomUsuari    : string Usuario.                              *
  * @param $fPwdClavex    : string Clave.                                *
  * @return string or Array mensaje de respuesta.                        *
  ************************************************************************/
  function getNovedades( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex );

    $fValidator = new Validator( $fInputs, "getnovedades_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );
    
    //Se verifica si pasa las validaciones
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        //Se configura la clase Error
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getNovedades" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        //Se verifica Si se autentica
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fSeguim = new Seguim( $fExcept );
          
          $fNovedades = $fSeguim -> getNovedades( NULL, NULL, $fCodTransp);
          if( FALSE === $fNovedades )
          {
            throw new Exception( "No se encuentran novedades en Sat Trafico", "6001" );
            break;
          }
          else
          {
            $fReturn['arr_noveda'] = $fNovedades;
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se retornaron novedades satisfactoriamente';
            return $fReturn;
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTransp, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }
  
    
  /***********************************************************************
  * Funcion Inserta una novedad GPS en una aplicacion SAT.               *
  * @fn setNovedadGPS.                                                   *
  * @brief Inserta una novedad GPS en una aplicacion SAT.                *
  * @param $fNomAplica: string Nombre aplicacion.                        *
  * @param $fNumDespac: integer Numero del despacho.                     *
  * @param $fCodNoveda: integer Codigo de la novedad.                    *
  * @param $fFecNoveda: string Fecha de la novedad.                      *
  * @param $fDesNoveda: string Descripcion de la novedad.                *
  * @param $fValLongit: double Valor de la longitud.                     *
  * @param $fValLatitu: double Valor de la latitud.                      *
  * @param $fNomLlavex: string Llave de autenticacion.                   *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setNovedadGPS( $fCodTransp = NULL, $fNumDespac = NULL, $fCodNoveda = NULL, $fFecNoveda = NULL, 
                          $fDesNoveda = NULL, $fValLongit = NULL, $fValLatitu = NULL, $fNomLlavex = NULL,
                          $fCodOperad = NULL, $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodManifi = NULL,
                          $fNumPlacax)
  {

    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( 'InterfGPS' );
    $fExcept -> SetParams( "Faro", "setNovedadGPS" );

    $fDatValida = array( "nom_aplica" => $fNomAplica, "num_despac" => $fNumDespac, "cod_noveda" => $fCodNoveda, 
                         "fec_noveda" => $fFecNoveda, "des_noveda" => $fDesNoveda, "val_longit" => $fValLongit,
                         "val_latitu" => $fValLatitu, "nom_llavex" => $fNomLlavex, "cod_operad" => $fCodOperad, 
                         "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_manifi" => $fCodManifi,
                         "num_placax" => $fNumPlacax); 

    
    $fValidator = new Validator( $fDatValida, "novedadgps_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fDatValida, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
       //if( $fNomAplica == 'satt_faro' )
          include_once( AplKon );
        /*else
        {
          throw new Exception( "Aplicacion no encontrada o invalida", "1999" );
          break;
        }*/
        
        
        // ------------------------------------------   Validacion Cuando se resibe la llave o usr y pwd -----------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------------------
        $mFlag = false;
        if(($fNomLlavex == NULL && $fNomUsuari == NULL && $fPwdClavex == NULL) || ($fNomLlavex != NULL && $fNomUsuari != NULL && $fPwdClavex != NULL) )       
          throw new Exception( "Debe enviar solo la LLAVE &oacute; USUARIO y CLAVE para continuar.", "1002" );        
        else if($fNomLlavex != NULL && (($fNomUsuari != NULL && $fPwdClavex == NULL) || ($fNomUsuari == NULL && $fPwdClavex != NULL)))  
          throw new Exception( "Debe enviar solo la llave.", "1002" );       
        else if($fNomLlavex != NULL && ($fNomUsuari == NULL && $fPwdClavex == NULL))
        {
          if( $fNomLlavex != '3c09f78c210a18b686ae2540b0d12358' )         
            throw new Exception( "No esta autorizado para usar este metodo, Envie la llave correcta.", "1002" );          
          else 
            $mFlag = true;        
        }
        else if($fNomLlavex == NULL && ($fNomUsuari != NULL && $fPwdClavex == NULL) ||($fNomUsuari == NULL && $fPwdClavex != NULL) )    
          throw new Exception( "Debe enviar El Usuario y Clave.", "1002" );     
        else if($fNomLlavex == NULL && ($fNomUsuari != NULL && $fPwdClavex != NULL))
        {
          if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )                   
            $mFlag = true;
          else
            throw new Exception( "No esta autorizado para usar este metodo, Clave y/o usuario incorrectos.", "1002" );
        }
        // -----------------------------------------------------  Fin Validación Usuarios y Llave  ---------------------------------------------------------
        // -------------------------------------------------------------------------------------------------------------------------------------------------
        
        if($mFlag === TRUE)
        {   
            
            $fDespac = new DespacSat( $fExcept );
            $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
            
          
            if( $fCodOperad != NULL && $fNomUsuari != NULL && $fPwdClavex != NULL )
            {
              // Validacion del operador
              $fCodOperadExist = $fDespac -> getOperadorGps( $fCodOperad );
              if( $fCodOperadExist === FALSE )
              {
                throw new Exception( "El Operador ".$fCodOperad." no existe.", "6001" );
                break;
              }
              
              //Validación del numero del codigo de  manifiesto y placa
              if ( $fCodManifi == NULL && $fNumPlacax == NULL  ) {             
                throw new Exception( "Debe enviar el Numero de Plantilla o Numero de Manifiesto Y la matricula del Vehiculo.", "6001" ); break;}        
             
              if ( $fCodManifi == NULL ) {             
                throw new Exception( "Debe enviar el Numero de Plantilla o Numero de Manifiesto.", "6001" ); break;}              
              
              if ( $fNumPlacax == NULL ) {
                throw new Exception( "Debe enviar la Matricula del Vehiculo.", "6001" ); break; }            
              
              //Busca el despacho Con placa, manifiesto y trasportadora
              $fQuerySelNumDes = "SELECT a.num_despac 
                               FROM ".BASE_DATOS.".tab_despac_despac a, 
                                    ".BASE_DATOS.".tab_despac_vehige b 
                              WHERE a.num_despac = b.num_despac 
                                AND a.cod_manifi = '".$fCodManifi."' 
                                AND b.cod_transp = '".$fCodTransp."' 
                                AND b.num_placax = '".$fNumPlacax."' 
                                AND a.ind_anulad != 'A' 
                                AND a.fec_llegad IS NULL";

              $fConsult -> ExecuteCons( $fQuerySelNumDes );
              if( 0 != $fConsult -> RetNumRows() ) {
                $mRetunDespac = $fConsult -> RetMatrix( "a" );
                $fNumDespac = $mRetunDespac[0]["num_despac"];                
              }
              else 
              {              
                throw new Exception( "No se encontro un despacho para el Manifiesto No: ".$fCodManifi.", con placa: ".$fNumPlacax." en Sat Trafico.", "6001" ); break; 
              }              
            }
            
            $fFecNoveda = NULL == $fFecNoveda ? date( "Y-m-d H:i" ) : $fFecNoveda;            
            $fFecActual = date( "Y-m-d H:i" );         
            
            //Se define la cantidad de minutos de holgura con respecto a la hora de otros servidores
            $fMinuteHolgur = 10;
            
            $fFecNovedaTime = strtotime( $fFecNoveda );
            $fFecActualTime = strtotime( $fFecActual );
            
            $fDifMinute = abs( ( $fFecActualTime - $fFecNovedaTime ) / 60);
            
            if( ( $fFecActualTime < $fFecNovedaTime ) && ( $fDifMinute > $fMinuteHolgur ) )
            {
              throw new Exception( "Fecha de la novedad ".$fFecNoveda." mayor que la fecha actual ".$fFecActual. " por ".$fDifMinute." minutos", "6001" );
              break;
            }
            else if( $fDifMinute <= $fMinuteHolgur )
            {
              $fFecNoveda = $fFecActual;
            }

            
            if( !$fDespac -> despacInRout( $fNumDespac ) )
            {
              throw new Exception( "El despacho ".$fNumDespac." no se encuentra en ruta, o no esta registrado.", "6001" );
              break;
            }

            $fDesp = $fDespac -> getDespac( $fNumDespac, $fCodTransp );

            $fNoveda = $fDespac -> getNovedadGps( $fCodNoveda, $fNomNoveda, $fCodOperad , $fNomLlavex);
            if( FALSE === $fNoveda )
            {
              if($fCodNoveda != '4999' && $fCodOperad != null)
                throw new Exception( "Novedad ". $fCodNoveda.' Con operador '.$fCodOperad ." no existente.", "6001" );
              else
                throw new Exception( "Novedad ". $fCodNoveda.' '.$fNomNoveda ." no existente.", "6001" );
              
              break;
            }
            

            //Se inserta el sitio reporte GPS
            if($fCodNoveda != '4999' && $fCodOperad != null)
            {
              $fNomSitiox = 'REPORTE PROTEKTO';
              $fNomUsuari = 'InterfPROTEKTO';
            }
            else
            {
              $fNomSitiox = 'REPORTE GPS';
              $fNomUsuari = 'InterfGPS';
            }

            $fQuerySelCodSitiox = "SELECT cod_sitiox ".
                                  "FROM ".BASE_DATOS.".tab_despac_sitio ".
                                 "WHERE nom_sitiox = '".$fNomSitiox."' ";
                                 
            $fConsult -> ExecuteCons( $fQuerySelCodSitiox );

            if( 0 != $fConsult -> RetNumRows() )
            {
              $fCodSitiox = $fConsult -> RetMatrix( "a" );
              $fCodSitiox = $fCodSitiox[0]['cod_sitiox'];
            }
            else
            {
              $fQuerySelMaxSitio = "SELECT MAX( cod_sitiox ) + 1 AS cod_sitiox ".
                                       "FROM ".BASE_DATOS.".tab_despac_sitio";
    
              $fConsult -> ExecuteCons( $fQuerySelMaxSitio );
              $fMaxSitiox = $fConsult -> RetMatrix( "a" );
              $fMaxSitiox = $fMaxSitiox[0]['cod_sitiox'];
              if( $fMaxSitiox == 0 )
                $fMaxSitiox = 1;
              
               $fQueryInsSitio = "INSERT INTO ".BASE_DATOS.".tab_despac_sitio ".
                                            "( cod_sitiox, nom_sitiox ) ".
                                     "VALUES ( '".$fMaxSitiox."', '".$fNomSitiox."' ) ";
    
              $resultInsSitio = $fConsult -> ExecuteCons( $fQueryInsSitio, "BRC" );
              if( $resultInsSitio )
              $fCodSitiox = $fMaxSitiox;
            }
          
          
            $fCodContro = $fDespac -> getPcontroFromDespac( $fCodContro, $fNomContro, $fNumDespac );

            $fQuerySelFecLastCont = "SELECT MAX( d.fec_contro ) AS fec_contro ".
                                      "FROM ".BASE_DATOS.".tab_despac_contro d ".
                                     "WHERE  d.num_despac = '".$fNumDespac."' ";

            $fConsult -> ExecuteCons( $fQuerySelFecLastCont );
            $fFecLastCont = $fConsult -> RetMatrix( "a" );

            $fQuerySelFecLastNov = "SELECT MAX( d.fec_noveda ) AS fec_contro ".
                                     "FROM ".BASE_DATOS.".tab_despac_noveda d ".
                                    "WHERE  d.num_despac = '".$fNumDespac."' ";

            $fConsult -> ExecuteCons( $fQuerySelFecLastNov );
            $fFecLastNov = $fConsult -> RetMatrix( "a" );

            if( NULL == $fFecLastCont[0]["fec_contro"] && NULL == $fFecLastNov[0]["fec_contro"] )
            {
              $fQueryCodContro = "SELECT a.cod_contro ".
                                   "FROM ".BASE_DATOS.".tab_despac_contro a ".
                                  "WHERE a.num_despac = '".$fNumDespac."' ";

              $fConsult -> ExecuteCons( $fQueryCodContro );  
            }
            elseif( $fFecLastCont[0]["fec_contro"] >= $fFecLastNov[0]["fec_contro"] )
            {
              $fQueryCodContro = "SELECT a.cod_contro ".
                                   "FROM ".BASE_DATOS.".tab_despac_contro a ".
                                  "WHERE a.num_despac = '".$fNumDespac."' ".
                                    "AND a.fec_contro = '".$fFecLastCont[0]["fec_contro"]."' ";

              $fConsult -> ExecuteCons( $fQueryCodContro );
            }
            else
            {
              $fQueryCodContro = "SELECT c.val_duraci ".
                                   "FROM ".BASE_DATOS.".tab_despac_noveda a, ".
                                        "".BASE_DATOS.".tab_despac_vehige b, ".
                                        "".BASE_DATOS.".tab_genera_rutcon c ".
                                  "WHERE a.num_despac = b.num_despac ".
                                    "AND a.cod_contro = c.cod_contro ".
                                    "AND b.cod_rutasx = c.cod_rutasx ".
                                    "AND a.num_despac = '".$fNumDespac."' ".
                                    "AND a.fec_noveda = '".$fFecLastNov[0]["fec_contro"]."' ";

              $fConsult -> ExecuteCons( $fQueryCodContro );
              $fResultCodContro = $fConsult -> RetMatrix( "a" );

              $fQueryCodContro = "SELECT a.cod_contro ".
                                   "FROM ".BASE_DATOS.".tab_despac_seguim a, ".
                                        "".BASE_DATOS.".tab_despac_vehige b, ".
                                        "".BASE_DATOS.".tab_genera_rutcon c ".
                                 "WHERE a.num_despac = b.num_despac ".
                                   "AND a.cod_contro = c.cod_contro ".
                                   "AND b.cod_rutasx = c.cod_rutasx ".
                                   "AND a.num_despac = '".$fNumDespac."' ".
                                   "AND c.val_duraci > '".$fResultCodContro[0]["val_duraci"]."' ORDER BY c.val_duraci ASC ";
    
              $fConsult -> ExecuteCons( $fQueryCodContro );
            }

            if( 0 == $fConsult -> RetNumRows() )
            {
              $fQueryCodContro = "SELECT a.cod_contro, c.val_duraci ".
                                   "FROM ".BASE_DATOS.".tab_despac_seguim a, ".
                                        "".BASE_DATOS.".tab_despac_vehige b, ".
                                        "".BASE_DATOS.".tab_genera_rutcon c ".
                                  "WHERE a.num_despac = b.num_despac ".
                                    "AND a.cod_contro = c.cod_contro ".
                                    "AND b.cod_rutasx = c.cod_rutasx ".
                                    "AND a.num_despac = '".$fNumDespac."' ".
                                    "AND a.fec_planea = ( SELECT MIN( d.fec_planea ) ".
                                                           "FROM ".BASE_DATOS.".tab_despac_seguim  d ".
                                                          "WHERE d.num_despac = '".$fNumDespac."' ) ";

              $fConsult -> ExecuteCons( $fQueryCodContro );
            }

            $fResultCodContro = $fConsult -> RetMatrix( "a" );
            $fCodContro = $fResultCodContro[0]["cod_contro"];

            //Se obtiene el ultimo consecutivo del despacho
            $query = "SELECT MAX( a.cod_consec ) AS cod_consec
                       FROM ".BASE_DATOS.".tab_despac_contro a,
                            ".BASE_DATOS.".tab_despac_vehige b
                      WHERE a.num_despac = b.num_despac AND
                            a.cod_rutasx = b.cod_rutasx AND
                            b.num_despac = ".$fNumDespac."";
        
            $consec = $fConsult -> ExecuteCons( $query );
            $ultimo = $fConsult -> RetMatrix( 'a' );
        
            $ultimo = $ultimo[0]['cod_consec'];
            $fConse = $ultimo + 1;
            
            $fSitioF = "cod_sitiox,";
            $fSitioV = "'".$fCodSitiox."',";
            $temp = $fDesNoveda;

            $exchange = explode("^", $temp);
            
            if(count($exchange) > 1){
							$latlon = json_decode($exchange[1]);
              $fValLatitu = $latlon -> latitud;
              $fValLongit = $latlon -> longitud;        
              $fDesNoveda = $exchange[0];
            }

            $fQueryInsNoved = "INSERT INTO ".BASE_DATOS.".tab_despac_contro ".
                                "( num_despac,  cod_rutasx, cod_contro, cod_consec, ".
                                  "obs_contro,  val_longit, val_latitu, cod_noveda, ".
                                  "tiem_duraci, fec_contro, ".$fSitioF." usr_creaci, ".
                                  "fec_creaci ) ".
                              "VALUES ( '".$fNumDespac."', '".$fDesp["cod_rutasx"]."', '".$fCodContro."', '".$fConse."', ".
                                       "'".$fDesNoveda."', '".$fValLongit."', '".$fValLatitu."', '".$fNoveda["cod_noveda"]."', ".
                                       "'0', '".$fFecNoveda."', ".$fSitioV." '".$fNomUsuari."', ".
                                       "NOW() ) ";

            $mUpdateSet[] = "cod_consec = '".$fConse."'";
            $mUpdateSet[] = "usr_modifi = '".$fNomUsuari."'";
            $mUpdateSet[] = "fec_modifi = NOW()";
            

            //Sat basico tiene nuevo modulo de Seguimiento
            $mUpdateSet[] = "fec_manala = NULL";
            $mUpdateSet[] = "fec_ultnov = '".$fFecNoveda."'";
          
            $fQueryDespacDes = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                                   "SET ";
            $fQueryDespacDes .= implode( ', ', $mUpdateSet );
            $fQueryDespacDes .= " WHERE num_despac = '".$fNumDespac."' ";

            $resultInsNoveda = $fConsult -> ExecuteCons( $fQueryInsNoved, "BR" );
            
            if( $resultInsNoveda && $fConsult -> ExecuteCons( $fQueryDespacDes, "RC" ) )
              $fReturn = "code_resp:1000; msg_resp:Se inserto la novedad Pc de forma satisfactoria.";
            else
              $fReturn = "code_resp:1999; msg_resp:No se inserto la novedad Pc.";
        }
        
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
  
   /***********************************************************************
  * Funcion Inserta una novedad PC en una aplicacion SAT.                *
  * @fn setNovedadPC.                                                    *
  * @brief Inserta una novedad PC en una aplicacion SAT.                 *
  * @param $fNomUsuari: string Usuario.                                  *
  * @param $fPwdClavex: string Clave.                                    *
  * @param $fCodTransp: string Codigo de la transportadora.              *
  * @param $fNumManifi: string Numero del manifiesto                     *
  * @param $fNumPlacax: string Numero de la placa.                       *
  * @param $fCodNoveda: string Codigo de la novedad.                     *
  * @param $fCodContro: string Codigo puest control.                     *
  * @param $fTimDuraci: string Tiempo duracion.                          *
  * @param $fFecNoveda: string Fecha de la novedad.                      *
  * @param $fDesNoveda: string Descripcion de la novedad.                *
  * @param $fNomNoveda: string Nombre novedad.                           *
  * @param $fNomContro: string Nombre puesto de control.                 *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setNovedadPC( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fNumManifi = NULL,
                         $fNumPlacax = NULL, $fCodNoveda = NULL, $fCodContro = NULL, $fTimDuraci = NULL, 
                         $fFecNoveda = NULL, $fDesNoveda = "", $fNomNoveda = NULL, $fNomContro = NULL,
                         $fNomSitiox = NULL, $fNumViajex = NULL, $fFotNoveda = NULL, $fCodRemdes = NULL,
                         $fTimSegpun = NULL, $fTimUltpun = NULL, $fNomAplica = NULL )
  {
    // Se amplía la memoria ya que generar error en el cliente, desde el TMS

    ini_set('memory_limit', '5096M');
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setNovedadPC" );

    $fDatValida = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_transp" => $fCodTransp, 
                         "num_manifi" => $fNumManifi, "num_placax" => $fNumPlacax, "cod_contro" => $fCodContro, 
                         "cod_noveda" => $fCodNoveda, "fec_noveda" => $fFecNoveda, "des_noveda" => $fDesNoveda, 
                         "tim_duraci" => $fTimDuraci, "nom_contro" => $fNomContro, "nom_noveda" => $fNomNoveda,
                         "nom_sitiox" => $fNomSitiox );

    $fValidator = new Validator( $fDatValida, "novedad_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    $fDatValidax= $fDatValida;
    unset( $fDatValida, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if(  $fNomAplica != NULL  &&  strpos($fNomAplica, '_' ) != false ) // valida si llega nom_aplica y si tiene _ en el texto  
        {
          if(!include_once('/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc') )
          {
            throw new Exception( "Aplicacion GL ".$fNomAplica."  en sat trafico no encontrada", "1999" );
          }elseif ($fNomAplica == 'satt_dingps') {
            include_once('/var/www/html/ap/integradorgps/'.$fNomAplica.'/constantes.inc');
          }
        }
        else if(file_exists( AplKon ) ){
          include_once( AplKon );
        }
        else
        {
          throw new Exception( "Aplicacion FARO en sat trafico no encontrada PC", "1999" );
          break;
        }

        $fFecNoveda = NULL == $fFecNoveda ? date( "Y-m-d H:i" ) : $fFecNoveda;
        

        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }

        //parche cuando la novedad viene desde las app ya que el manifiesto no siempre es el vs :(
        if($fNomUsuari == 'InterfCorona')
        {
            $mValida = "SELECT cod_manifi FROM   ".BASE_DATOS.".tab_despac_corona WHERE  num_despac = '".$fNumManifi."'";
            $fConsult -> ExecuteCons( $mValida );
            if( 0 != $fConsult -> RetNumRows() )
            {
              $fManiViaje = $fConsult -> RetMatrix( "a" );
              $fNumManifi = $fManiViaje[0]["cod_manifi"];   
            }
        }

        //Se consulta el numero de despacho
        $fQueryNumDespac = "SELECT a.num_despac ".
                             "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                  "".BASE_DATOS.".tab_despac_vehige b ".
                            "WHERE a.num_despac = b.num_despac ".
                              "AND a.cod_manifi = '".trim( $fNumManifi )."' ".
                              "AND b.num_placax = '".trim( $fNumPlacax )."' ".
                              "AND a.fec_salida IS NOT NULL ".
                              "AND a.fec_llegad IS NULL ".
                              "AND b.ind_activo = 'S' ";

        //parche debido a que en central los despachos 
        //tienen el nit de fortecem y en gl(generadores) si tienen el de la transportadora correspondiente
        if ( $fCodTransp != '900657956'){
          $fQueryNumDespac .= " AND b.cod_transp = '".trim( $fCodTransp )."' ";
        }

        $fConsult -> ExecuteCons( $fQueryNumDespac );
        
        if( 0 != $fConsult -> RetNumRows() )
        {
          $fResultNumDespac = $fConsult -> RetMatrix( "a" );
          $fNumDespac = $fResultNumDespac[0]["num_despac"];   
        }
        else
        {
            
            //$mFile = fopen("/var/www/html/ap/interf/app/faro/logs/NovedadesError".date("Ymd").".txt", "a+");
            //fwrite($mFile, "------------------     setNovedadPC ".date("Y m d H:i:s")."  ------------------------\n");
            //fwrite($mFile, "Data Input PC:\n");
            //fwrite($mFile, var_export($fDatValidax, true)."\n");
            //fwrite($mFile, "fQueryNumDespac: \n");
            //fwrite($mFile, $fQueryNumDespac." \n");  
            //fclose($mFile);


          throw new Exception( "No se encuentra numero de despacho PC. Transp: ".$fCodTransp." Manifi: ".$fNumManifi." Placa: ".$fNumPlacax, "1999" );
          break;
        }

        $fDespac = new DespacSat( $fExcept );

        if( !$fDespac -> despacInRout( $fNumDespac ) )
        {
          throw new Exception( "El despacho ".$fNumDespac." no se encuentra en ruta, o no esta registrado.", "6001" );
          break;
        }

        $fDesp = $fDespac -> getDespac( $fNumDespac, $fCodTransp );

        $fNoveda = $fDespac -> getNovedad( $fCodNoveda, $fNomNoveda );
        if( FALSE === $fNoveda )
        {
          throw new Exception( "Novedad ". $fNomNoveda ." no existente.", "6001" );
          break;
        }
        
        //valida si el nombre del sitio existe
         $query = "SELECT cod_sitiox ".
                    "FROM ".BASE_DATOS.".tab_despac_sitio ".
                   "WHERE nom_sitiox = '".$fNomSitiox."'";
         $consulta = $fConsult -> ExecuteCons( $query );
         $sitio = $fConsult -> RetMatrix( );
         
         //$fConsult -> ExecuteCons( "SELECT 1", "BR" );
         $fConsult -> StartTrans();
         
         if( !$sitio )
         {
           $query = "SELECT MAX( cod_sitiox ) ".
                      "FROM ".BASE_DATOS.".tab_despac_sitio ";
           $consulta = $fConsult -> ExecuteCons( $query );
           $sitio = $fConsult -> RetMatrix( );
           
           $maxsit = $sitio[0][0] + 1;
           $query = "INSERT INTO ".BASE_DATOS.".tab_despac_sitio".
                   "( cod_sitiox, nom_sitiox ) VALUES ( $maxsit, '".$fNomSitiox."' ) ";
           if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
             throw new Exception( "Error en Insert.", "3001" );
         }
         else
         {
           $maxsit = $sitio[0][0];
         }
         //Se hacen ajustes para las alarmas cuando la novedad mantiene alarma
         $tieadi = $fTimDuraci;
        $query = "SELECT fec_manala, fec_ultnov, fec_salida ".
                   "FROM ".BASE_DATOS.".tab_despac_despac
                   WHERE num_despac = '".$fNumDespac."' ";
        $consulta = $fConsult -> ExecuteCons( $query );
        $fec_manala = $fConsult -> RetMatrix( );
        $fec_ultnov = "'".$fec_manala[0][1]."'";
        $fec_salida = "'".$fec_manala[0][2]."'";
        $fec_manala = "'".$fec_manala[0][0]."'";
     
        $query = "SELECT ind_manala, ind_notsup, nom_noveda ".
        //$query = "SELECT ind_manala".
                   " FROM ".BASE_DATOS.".tab_genera_noveda
                   WHERE cod_noveda = '".$fCodNoveda."' ";
        $consulta = $fConsult -> ExecuteCons( $query );
        $ind_manala = $fConsult -> RetMatrix( );
        
        $ind_notsup = $ind_manala[0][1]; //Jorge 120769
        $nove = $ind_manala[0][2]; 
        $ind_manala = $ind_manala[0][0];
     
        $query = "( SELECT tiem_duraci, fec_contro
                      FROM  ".BASE_DATOS.".tab_despac_contro
                     WHERE num_despac = ".$fNumDespac." )
                   UNION
                  ( SELECT tiem_duraci, fec_noveda
                      FROM  ".BASE_DATOS.".tab_despac_noveda 
                     WHERE num_despac = ".$fNumDespac." )
                  ORDER BY 2 DESC
                     LIMIT 1 ";
                    
        $consulta = $fConsult -> ExecuteCons( $query );
        $valretras = $fConsult -> RetMatrix( );
        $valretras = $valretras[0][0];
     
        if( $ind_manala == '0' )
        {
          $fec_manala = "NULL"; 
        }
        else
        {
          if( $fec_manala == "''" )
          {
            if( $fec_ultnov == '' ||  $fec_ultnov == "''" )
              $fec_manala = $fec_salida;
            else
              $fec_manala = $fec_ultnov; 
          }
          if( $valretras == "" )
            $valretras = 0;
          $tieadi = $valretras;
        }
        if( $fec_manala == '' )
          $fec_manala = "NULL";
        if( $fTimDuraci == '' )
          $tieadi = 0;
        
        if ($ind_notsup == 1)//Jorge 120769
        {
            $query = "SELECT a.cod_tercer,a.abr_tercer ,b.num_placax, c.con_telmov, d.abr_tercer AS nom_conduc, c.cod_manifi
                                  FROM " . BASE_DATOS . ".tab_tercer_tercer a,
                                              " . BASE_DATOS . ".tab_despac_vehige b,
                                              " . BASE_DATOS . ".tab_despac_despac c,
                                              " . BASE_DATOS . ".tab_tercer_tercer d
                              WHERE b.num_despac = '" . $fNumDespac . "' 
                                    AND b.num_despac = c.num_despac
                                    AND b.cod_conduc = d.cod_tercer
                                    AND a.cod_tercer = b.cod_transp";
            $empre = $fConsult -> ExecuteCons( $query );
            $empre = $fConsult -> RetMatrix( );
            
            $info .="EMPRESA: " . $empre[0][1] . " \n";
            $info .="DESPACHO: " . $fNumDespac . " \n";
            $info .="MANIFIESTO: " . $empre[0]['cod_manifi'] . " \n";
            $info .="PLACA DEL VEHICULO: " . $empre[0][2] . " \n";
            $info .="CONDUCTOR: " . $empre[0]['nom_conduc'] . " \n";
            $info .="TELEFONO CONDUCTOR: " . $empre[0]['con_telmov'] . " \n";
            $info .="SITIO DE SEGUIMIENTO: " . $fNomSitiox . " \n";
            $info .="FECHA DE LA NOVEDAD: " . $fFecNoveda . " \n";
            $info .="NOVEDAD: " . $nove . " \n";
            $info .="USUARIO: " . $fNomUsuari . " \n";
            $info .="OBSERVACION:  \n";
            $info .=" " . $fDesNoveda . " \n";

            $asunto = "NOTIFICACION SUPERVISOR";

            //mail(MAIL_SUPERVISORES, $asunto, $info, 'From: faroavansat@eltransporte.com');
            mail(FarMai, $asunto, $info, 'From: faroavansat@eltransporte.com');
        }
        
        //Manejo de consecutivo de verificacion
        $query = "SELECT cod_verpcx
                    FROM ".BASE_DATOS.".tab_config_parame 
                   WHERE cod_aplica = 1 ";
        $consulta =  $fConsult -> ExecuteCons( $query );
        $fCodVerifi = $fConsult -> RetMatrix( );
        $fCodVerifi = (int)$fCodVerifi[0]['cod_verpcx'] + 1;

        # Upate Consec Verificacion OC 
        /*
        $query2 = "UPDATE ".BASE_DATOS.".tab_config_parame
                     SET cod_verpcx = '".$fCodVerifi."' 
                   WHERE cod_aplica = 1 ";                     
        if( $fConsult -> ExecuteCons( $query2, "R" ) === FALSE )
          throw new Exception( "Error en Update.", "3001" ); */

                # Parche si la novedad es para corona 
        $temp = $fDesNoveda;
        $fields = "";
        $values = "";
        $exchange = explode("^", $temp);
        
        if(count($exchange) > 1){
          $latlon = json_decode($exchange[1]);
          $fValLatitu = $latlon -> latitud;
          $fValLongit = $latlon -> longitud;        
          $fDesNoveda = $exchange[0];
          $fields = " val_latitu,val_longit, ";
          $values = " '$fValLatitu','$fValLongit' , ";
        }

        $mRemdes = [];
        if( ( $fCodRemdes != '' || $fCodRemdes != NULL ) && ( in_array($fNoveda["cod_noveda"] , ['9261', '9173'] ) ) )
        {
          $mRemdes = $fDespac -> getRemdes($fCodRemdes);
          $fDesNoveda .= "Evento en cargue: ".$mRemdes['nom_remdes']." - Dir: ".$mRemdes['dir_remdes']." - Ciu: ".$mRemdes["nom_ciudad"];
        }
        
        if($fCodContro == '')
        {
          # Trae el puesto habilitado y lo cambia por el que entra, el que nos envian
          $mCodControHab = getNextPC($fNumDespac, $fExcept);
          $fCodContro = $mCodControHab["cod_contro"];
          // $fNomSitiox = $fNomSitiox == NULL ? $mCodControHab["nom_contro"] : $fNomSitiox;      
        }


        // valida el codigo PC de la OAL si la novedad es 9266 Paso por OAL, ya que en el plan de ruta puede que no contenga el PC padre sino un PC hijo, Homologado
        if($fNoveda['cod_noveda'] == '9266XXX')
        {
          $fQueryPCOALHOMOLO = "SELECT 
                                        a.num_despac, a.cod_manifi, b.num_placax, c.cod_contro AS cod_pcplan, d.cod_contro AS pcx_padrex, 
                                        IF( c.cod_contro = d.cod_contro, c.cod_contro, IF( d.cod_homolo IS NULL , c.cod_contro ,  d.cod_homolo ) ) AS pcx_hijoxx
                                  FROM
                                        ".BASE_DATOS.".tab_despac_despac a
                            INNER JOIN  ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac AND a.num_despac = '".$fNumDespac."'
                            INNER JOIN  ".BASE_DATOS.".tab_despac_seguim c ON b.num_despac = c.num_despac AND b.cod_rutasx = c.cod_rutasx
                             LEFT JOIN  ".BASE_DATOS.".tab_homolo_pcxeal d ON ( c.cod_contro = d.cod_homolo OR c.cod_contro = d.cod_contro )
                                 WHERE
                                        1 = 1
                                   AND  a.num_despac = '".$fNumDespac."' 
                                   AND  ( d.cod_contro = '".$fCodContro."'  OR d.cod_homolo = '".$fCodContro."' OR c.cod_contro = '".$fCodContro."' ) ";
          if( $fConsult -> ExecuteCons( $fQueryPCOALHOMOLO, "R" ) === FALSE ){
            throw new Exception( "Error en Insert.", "3001" );
          }

          // valida si encuentra algo , para colocar el PC homologado que están en el plan de ruta para poder registrar la novedad en sitio con el PC Hijo
          $mSeguim = $fConsult -> RetMatrix( 'a' );        
          if( $mSeguim[0]['pcx_hijoxx'] != '' )
          {
            $fCodContro = $mSeguim[0]['pcx_hijoxx'];
          }else{
            throw new Exception( "La OAL con codigo ".$fCodContro." NO hace parte del plan de ruta para el despacho o no tiene un homologado o el integrador reporta la oal que no es para el despacho ".$fNumDespac, "8001" );
          }
        }


        // ------------------------------------------------------------------------------------------------------------------------------------
        // parche para no correr tiempo cuando el despacho tiene como ultima novedad la pernoctación ------------------------------------------
          $mQuery = 'SELECT 
                        -- Cuenta los incios y los fin de pernoc y valida si  los incios son mayores a lof finaliza, es porque está pernoctando, si son iguales es porque ya no está pernoctando
                        IF( (SUM( IF( a.cod_noveda IN (6, 245, 258, 9008) , 1 , 0 ) ) ) != SUM( IF( a.cod_noveda IN (211, 9025) , 1 , 0 ) ), 0 , 1 ) AS ind_modifi_alarma
                      FROM
                      (
                        -- Se obitene las novedades registradas en antes y en sitio de las pernoctaciones. (inicio y fin)
                        ( SELECT tiem_duraci, fec_contro, cod_contro, cod_noveda
                            FROM  '.BASE_DATOS.'.tab_despac_contro
                           WHERE num_despac = "'.$fNumDespac.'" AND
                                 cod_noveda IN ( 6, 245, 258, 9008, 211, 9025 )
                        )
                         UNION
                        ( SELECT tiem_duraci, fec_noveda, cod_contro, cod_noveda
                            FROM  '.BASE_DATOS.'.tab_despac_noveda 
                           WHERE num_despac = "'.$fNumDespac.'" AND
                                 cod_noveda IN ( 6, 245, 258, 9008, 211, 9025 )
                        )
                      ) a ';

          $consulta = $fConsult -> ExecuteCons( $mQuery );
          $CorrerAlarma = $fConsult -> RetMatrix( 'a' ); 

          $mUltimoRegistro = getNextPC( $fNumDespac, $fExcept );
          if($CorrerAlarma[0]['ind_modifi_alarma'] == '0' || $mUltimoRegistro['ind_fuepla'] == '1')
          {
            $tieadi = '0';
          }
        // ------------------------------------------------------------------------------------------------------------------------------------

        $fQueryInsNoved = "INSERT INTO ".BASE_DATOS.".tab_despac_noveda ".
                              "( num_despac, cod_rutasx,  cod_contro, cod_noveda, fec_noveda, ".
                                "des_noveda, tiem_duraci, cod_sitiox, usr_creaci, fec_creaci, cod_verpcx ) ".
                            "VALUES ( '".$fNumDespac."', '".$fDesp["cod_rutasx"]."', '".$fCodContro."', ".
                                     "'".$fNoveda["cod_noveda"]."', '".$fFecNoveda."', 'INTERFAZ - ".str_replace("'", "", $fDesNoveda)."', ".
                                     "'".$tieadi."', '".$maxsit."', '".$fNomUsuari."', NOW(), '".$fCodVerifi."' )";
         if( $fConsult -> ExecuteCons( $fQueryInsNoved, "R" ) === FALSE )
           throw new Exception( "Error en Insert.", "3001" );
        


        # Upate Consec Verificacion OC         
        $query2 = "UPDATE ".BASE_DATOS.".tab_config_parame
                     SET cod_verpcx = '".$fCodVerifi."' 
                   WHERE cod_aplica = 1 ";                     
         // if( $fConsult -> ExecuteCons( $query2, "R" ) === FALSE )
         //   throw new Exception( "Error en Update.", "3001" ); 

        
        $condAcargoFaro = $fNoveda["cod_noveda"] == NovAcarFar ? " ind_defini = '0', " : '';
        #$condAcargoFaro = $fNoveda["cod_noveda"] == NovAcarFar ? " ind_defini = '0', " : "  ind_defini = '1', ";
        
        $fQueryDespacDes = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                              "SET cod_conult = ".$fCodContro.", ".
                                  "cod_ultnov = ".$fNoveda["cod_noveda"].", ".
                                  "fec_ultnov = '".$fFecNoveda."', ".
                                  "fec_manala = ".$fec_manala.", ".
                                  $condAcargoFaro.
                                  "usr_ultnov = '".$fNomUsuari."' ".
                            "WHERE num_despac = '".$fNumDespac."' ";

        if( $fConsult -> ExecuteCons( $fQueryDespacDes, "R" ) === FALSE )
          throw new Exception( "Error en Insert.", "3001" );
        
        $query= "SELECT cod_contro
                   FROM ".BASE_DATOS.".tab_despac_seguim
                  WHERE num_despac = '".$fNumDespac."' 
                    AND cod_rutasx = '".$fDesp["cod_rutasx"]."'
               ORDER BY fec_planea";
        $consulta = $fConsult -> ExecuteCons( $query );
        $seguim = $fConsult -> RetMatrix( );        
        for( $i = 0; $i < sizeof( $seguim ); $i++ )
        {
          $msg .= "\n\n".$fCodContro.'='.$seguim[$i][0];
          if( $fCodContro == $seguim[$i][0] )
          {
            break;
          }
          else
          {
            $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                         SET ind_estado = '0'
                       WHERE num_despac = '".$fNumDespac."' 
                         AND cod_rutasx = '".$fDesp["cod_rutasx"]."' 
                         AND cod_contro = '".$seguim[$i][0]."' ";
                         
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );
          }
        }
        if( $fNoveda["cod_noveda"] == '9998' )
        {
          $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                       SET ind_estado = '0'
                     WHERE num_despac = '".$fNumDespac."' AND
                           cod_rutasx = '".$fDesp["cod_rutasx"]."'  ";
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );
          $query = "SELECT b.cod_rutasx
                    FROM ".BASE_DATOS.".tab_despac_seguim b
                   WHERE num_despac = '".$fNumDespac."' AND
                         fec_creaci =( SELECT MAX( fec_creaci ) FROM ".BASE_DATOS.".tab_despac_seguim WHERE num_despac = '".$fNumDespac."' LIMIT 1 )";
          $consulta =  $fConsult -> ExecuteCons( $query );
          $cod_rutasx = $fConsult -> RetMatrix( );
          $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                       SET cod_rutasx = ".$cod_rutasx[0][0].",
                           usr_modifi = '".$fNomUsuari."',
                           fec_modifi = NOW()
                     WHERE num_despac = ".$fNumDespac."";
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );
        }
        
                //se ajusta fec_planea y fec_alarma
        $query = "SELECT a.fec_planea
                    FROM ".BASE_DATOS.".tab_despac_seguim a
                   WHERE a.num_despac = ".$fNumDespac."
                     AND a.cod_rutasx = ".$fDesp["cod_rutasx"]."
                     AND a.cod_contro = ".$fCodContro."";
        $consulta =  $fConsult -> ExecuteCons( $query );
        $planru_c = $fConsult -> RetMatrix( );

        $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                     SET fec_alarma = '".$fFecNoveda."',
                         usr_modifi = '".$fNomUsuari."',
                         fec_modifi = NOW()
                   WHERE num_despac = ".$fNumDespac."
                     AND cod_rutasx = ".$fDesp["cod_rutasx"]."
                     AND cod_contro = ".$fCodContro."";

        if ( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );

        //trae el plan de ruta a actualizar
        $query = "SELECT a.cod_contro,( TIME_TO_SEC( TIMEDIFF( a.fec_planea, '".$planru_c[0][0]."' ) ) / 60 )
                    FROM ".BASE_DATOS.".tab_despac_seguim a
                   WHERE a.num_despac = ".$fNumDespac."
                     AND a.cod_rutasx = ".$fDesp["cod_rutasx"]."
                     AND a.fec_planea > '".$planru_c[0][0]."'
                ORDER BY a.fec_planea";

        $consulta =  $fConsult -> ExecuteCons( $query );
        $planru_p = $fConsult -> RetMatrix( );

        $tiemacu = 0;
     
        for( $i = 0; $i < sizeof( $planru_p ); $i++ )
        {
          $tiemacu = $planru_p[$i][1] + $tieadi;

          $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                       SET fec_alarma = DATE_ADD('".$fFecNoveda."', INTERVAL ".$tiemacu." MINUTE),
                           usr_modifi = '".$fNomUsuari."',
                           fec_modifi = now()
                     WHERE num_despac = ".$fNumDespac."
                       AND cod_rutasx = ".$fDesp["cod_rutasx"]."
                       AND cod_contro = ".$planru_p[$i][0]."";

          if ( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert.", "3001" );
        }

        //mail("nelson.liberato@intrared.net", "Mari jode V2", $fCodNoveda);
        # fecha: 2015-08-06
        # userx: nelson.liberato
        # Proceso para notificar las novedades que ingresan de los PC a las empresas terceras
        # Para este caso solo va a servir para la interfaz con MCT, codigo operadora Interf 58
        # Bug: se debe crear una libreria que se encargue del envio ws a los otros proveedores (silogtran, onlinetool, destino seguro etc)
        #      se puede guiar con el script ../satt_standa/despac/InsertNovedad.inc
        # si presenta error comentarear todo el if
        $mVerifyInterf = 'SELECT b.cod_operad, b.cod_transp, b.nom_usuari, b.clv_usuari  
                            FROM '.BASE_DATOS.'.tab_despac_vehige a, '.BASE_DATOS.'.tab_interf_parame b
                            WHERE a.cod_transp = b.cod_transp AND
                                  b.cod_operad = "58" AND
                                  b.ind_estado = "1" AND
                                  a.num_despac = "'.$fNumDespac.'" ';
        $mVerifyInterf =  $fConsult -> ExecuteCons( $mVerifyInterf );
        $mVerifyInterf =  $fConsult -> RetMatrix( "a");
        if(sizeof($mVerifyInterf) > 0)
        {
            $mQueryMct = "SELECT a.num_placax,b.cod_manifi 
                FROM ".BASE_DATOS.".tab_despac_vehige a,
                     ".BASE_DATOS.".tab_despac_despac b
               WHERE a.num_despac = " . $fNumDespac . " AND
                     a.num_despac = b.num_despac ";
        
            $mMct =  $fConsult -> ExecuteCons( $mQueryMct );
            $mMct =  $fConsult -> RetMatrix( "a" );

            # Consulta del puesto Padre de la homologacion
            $query = "SELECT  a.cod_contro
                        FROM ".BASE_DATOS.".tab_homolo_pcxeal a 
                       WHERE a.cod_homolo = '".$fCodContro."'  
                         OR a.cod_contro =  '".$fCodContro."' ";
            
            $mControPadre = $fConsult -> ExecuteCons($query);
            $mControPadre = $fConsult -> RetMatrix("a");

            # Consulta nombre, puesto control -----------------
            $mQuerySelNomPc = "SELECT nom_contro  
                                 FROM ".BASE_DATOS.".tab_genera_contro  
                                WHERE cod_contro = '".$mControPadre[0]["cod_contro"]."' ";

            $consulta = $fConsult -> ExecuteCons($mQuerySelNomPc);
            $mNomPc = $fConsult->RetMatrix("a");

            # Imagenes de las fotos de un despacho
            $mQuery = 'SELECT bin_fotoxx, bin_fotox2 FROM '.BASE_DATOS.'.tab_despac_images WHERE num_despac = "'.$fNumDespac.'" AND cod_contro = "'.$fCodContro.'"  ';
            $consulta = $fConsult -> ExecuteCons($mQuery);
            $mFotoDespac = $fConsult->RetMatrix("a");

            # Agrega datos adicionales en array para enviar en caso de notificacion de error
            $mAditional["num_placax"] = $mMct[0]["num_placax"];
            $mAditional["tip_noveda"] = "PC";

            # Trama de datos a enviar a MCT
            $mDataMCT = array(
                'manifiesto_codigo'    =>  urlencode( $mMct[0]["cod_manifi"] ),            
                'ptoc_codigo'          =>  urlencode( $mControPadre[0]["cod_contro"] ),
                'ptoc_nombre'          =>  urlencode( ($mNomPc[0]["nom_contro"] == '' ? 'Children PC WS: '.$mControPadre[0]["cod_contro"] : $mNomPc[0]["nom_contro"] ) ),
                'ptoc_fecha'           =>  urlencode( date("Y-m-d H:i", strtotime( $fFecNoveda ) ) ),
                'ptoc_observacion'     =>  urlencode( ($fDesNoveda == '' ? 'Registro WS: '.$mControPadre[0]["cod_contro"].' - '.$mNomPc[0][0] : 'Registro WS: '.$fDesNoveda ) ),
                'ptoc_imagenconductor' =>  urlencode( "{$mFotoDespac[0]['bin_fotoxx']}" ),
                'ptoc_imagenvehiculo'  =>  urlencode( "{$mFotoDespac[0]['bin_fotox2']}" ),
                'ptoc_imagenprecinto'  =>  urlencode( NULL )
            );      
            # Incluye la clase encargada de la interfaz toca importarla de satt_standa/lib/ del standa del framework de faro        
            include_once("/var/www/html/ap/satt_standa/lib/InterfMct.inc");
            $mMct = new InterfMct($fConsult, $mDataMCT, $mAditional);
            $mReturn = $mMct -> getResponMct();
        }


        //mail("nelson.liberato@intrared.net", "Mari jode V2", $fCodNoveda);
        # fecha: 2015-08-06
        # userx: nelson.liberato
        # Proceso para notificar las novedades que ingresan de los PC a las empresas terceras
        # Para este caso solo va a servir para la interfaz con MCT, codigo operadora Interf 58
        # Bug: se debe crear una libreria que se encargue del envio ws a los otros proveedores (silogtran, onlinetool, destino seguro etc)
        #      se puede guiar con el script ../satt_standa/despac/InsertNovedad.inc
        # si presenta error comentarear todo el if
        $mVerifyInterfCarga = 'SELECT b.cod_operad, b.cod_transp, b.nom_usuari, b.clv_usuari  
                            FROM '.BASE_DATOS.'.tab_despac_vehige a, '.BASE_DATOS.'.tab_interf_parame b
                            WHERE a.cod_transp = b.cod_transp AND
                                  b.cod_operad = "26" AND
                                  b.ind_estado = "1" AND
                                  a.num_despac = "'.$fNumDespac.'" ';
        $mVerifyInterfCarga =  $fConsult -> ExecuteCons( $mVerifyInterfCarga );
        $mVerifyInterfCarga =  $fConsult -> RetMatrix( "a");
        if(sizeof($mVerifyInterfCarga) > 0)
        { //Interfaz con Carga Antioquia
        
          $mQueryCarga = "SELECT a.num_placax,b.cod_manifi 
                FROM ".BASE_DATOS.".tab_despac_vehige a,
                     ".BASE_DATOS.".tab_despac_despac b
               WHERE a.num_despac = " . $fNumDespac . " AND
                     a.num_despac = b.num_despac ";
          $mCarga =  $fConsult -> ExecuteCons( $mQueryCarga );
          $mCarga =  $fConsult -> RetMatrix( "a" );

          # Consulta del puesto Padre de la homologacion
          $query = "SELECT  a.cod_contro
                      FROM ".BASE_DATOS.".tab_homolo_pcxeal a 
                     WHERE ( a.cod_homolo = '".$fCodContro."' OR a.cod_contro =  '".$fCodContro."' ) ";
          $mControPadre = $fConsult -> ExecuteCons($query);
          $mControPadre = $fConsult -> RetMatrix("a");

          # Consulta nombre, puesto control -----------------
          $mQuerySelNomPc = "SELECT nom_contro  
                               FROM ".BASE_DATOS.".tab_genera_contro  
                              WHERE cod_contro = '".$mControPadre[0]["cod_contro"]."' ";
          $consulta = $fConsult -> ExecuteCons($mQuerySelNomPc);
          $mNomPc = $fConsult->RetMatrix("a");

          // Xml String porque se manda en POST como API, no es un WSDL (SOAP)
          $mTextXML = '<?xml version="1.0" encoding="utf-8"?>
                          <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://carga.local/">
                              <SOAP-ENV:Header>
                                  <ns1:Credenciales>
                                      <ns1:Username>'.$mVerifyInterfCarga[0]['nom_usuari'].'</ns1:Username>
                                      <ns1:Password>'.$mVerifyInterfCarga[0]['clv_usuari'].'</ns1:Password>
                                  </ns1:Credenciales>
                              </SOAP-ENV:Header>
                              <SOAP-ENV:Body>
                                  <ns1:IngresarReporte>
                                      <ns1:reporte>
                                          <ns1:Manifiesto>'.$mCarga[0]['cod_manifi'].'</ns1:Manifiesto>
                                          <ns1:Placa>'.( substr($mCarga[0]["num_placax"], 0,3)."-".substr($mCarga[0]["num_placax"],3, 6) ).'</ns1:Placa>
                                          <ns1:CodigoPuestoControlOET>'.$mControPadre[0]["cod_contro"].'</ns1:CodigoPuestoControlOET>
                                          <ns1:FechaNovedad>'.date("Y-m-d H:i", strtotime( $fFecNoveda ) ).'</ns1:FechaNovedad>
                                          <ns1:Observacion>'.($fDesNoveda == '' ? 'Registro WS: '.$mControPadre[0]["cod_contro"].' - '.$mNomPc[0][0] : 'Registro WS: '.$fDesNoveda ).'</ns1:Observacion>
                                          <ns1:Lugar>'.$mNomPc[0]['nom_contro'].'</ns1:Lugar>
                                          <ns1:Sitio>1</ns1:Sitio>
                                      </ns1:reporte>
                                  </ns1:IngresarReporte>
                              </SOAP-ENV:Body>
                          </SOAP-ENV:Envelope>';
          
          $s = curl_init();
          curl_setopt($s,CURLOPT_URL, $interfaz->interfaz[$i]['diwsdl']);
          curl_setopt($s,CURLOPT_TIMEOUT,"4"); 
          curl_setopt($s,CURLOPT_HTTPHEADER,array('Content-Type: text/xml')); 
          curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
          curl_setopt($s,CURLOPT_POST,true);
          curl_setopt($s,CURLOPT_POSTFIELDS,$mTextXML);
          $mResponse   = curl_exec($s);
          $mHttpStatus = curl_getinfo($s,CURLINFO_HTTP_CODE);
          curl_close($s);

          // Quita el namespace de las variables porque el simpleXML no lo puede leer, debe quedar un XML sencillo
          $mResponse = str_replace(array('soap:'), array(''), $mResponse);
          // Se consbierte el XML de respuesta en objeto para poderlo usar
          $xmlObject = new SimpleXMLElement( $mResponse, 0, false);

          // cuando se ejecuta una excepcion del WS pero en el externo, lo tomo para que entre al fault de nosotros
          if($xmlObject -> Body -> Fault || $xmlObject -> Body -> IngresarReporteResponse -> IngresarReporteResult -> Codigo == '-1')
          {
          
              $mFile = fopen("/var/www/html/ap/satt_faro/logs/CARGASAS_".date("Y_m_d").".txt", 'a+');
              fwrite($mFile, "------------------------ DATE ".date("Y-m-d H:i:s")." ------------------------ \n");
              fwrite($mFile, "Type    : throw new Exception  \n");
              fwrite($mFile, "Request : ".$mTextXML."  \n");
              fwrite($mFile, "Response: ".$mResponse." \n\n");
              fclose($mFile);
          }
        }

        # Retorna string de mensaje satisfactorio
        $fConsult -> Commit();

         // -----------------------------------------------------------------------------------------------------------------------------
          // Validacion de cumplido de cita de cargue si $fCodNoveda = 9261 => llegada a cargue, 9263 => llegada a descargue
          // -----------------------------------------------------------------------------------------------------------------------------

          if(in_array($fNoveda["cod_noveda"], ['9261']) )
          {

            // Valdiacion de que la novedad no estya registrada
            $mValNoveda = "SELECT num_despac, fec_creaci 
                        FROM ".BASE_DATOS.".tab_despac_noveda
                       WHERE num_despac = '".$fNumDespac."'
                         AND cod_noveda = '".$fNoveda['cod_noveda']."' ";

            $consulta  = $fConsult -> ExecuteCons( $mValNoveda );
            $mExistNov = $fConsult -> RetMatrix( );


            $mExistNov[0]['num_despac'] = '';
            // Si la novedad es la de llega a cargue ejecuta cumplido en cargue y no estya registrada
            if( $fNoveda["cod_noveda"] == '9261' && $mExistNov[0]['num_despac'] == '' )
            {

              $mIndCumpli = "SELECT IF(NOW() < (
                                                  SELECT 
                                                          DATE_FORMAT( CONCAT( fec_citcar ,' ', hor_citcar ), '%Y-%m-%d %H:%i:%s' )
                                                    FROM  ".BASE_DATOS.".tab_despac_despac
                                                   WHERE  num_despac = '".$fNumDespac."' 
                                               ),'1','0'
                                      ) AS ind_cumpli";
              $fConsult -> ExecuteCons( $mIndCumpli, "R" );
              $mIndCumpli = $fConsult -> RetMatrix('a');
              $mIndCumpli = $mIndCumpli[0]["ind_cumpli"];

              // Ejecuta cumplido indicador al cargue
              $fDesNovcum = 'Cumplido cargue de integrador en '.$fNomSitiox;
              $fCodNoveda = '255';
              $mUpdCumpli = "UPDATE ".BASE_DATOS.".tab_despac_sisext a
                         INNER JOIN ".BASE_DATOS.".tab_despac_despac b ON a.num_despac = b.num_despac
                                SET a.ind_cumcar = '".$mIndCumpli."' ,
                                    a.fec_cumcar = '".$fFecNoveda."',
                                    a.nov_cumcar = '".$fCodNoveda."',
                                    a.obs_cumcar = '".$fDesNovcum."',

                                    b.fec_ingcar = '".$fFecNoveda."',
                                    b.nov_ingcar = '".$fNoveda['cod_noveda']."',
                                    b.obs_ingcar = '".str_replace("'", "", $fDesNoveda)."',
                                    b.ind_cumcar = '".$mIndCumpli."',
                                    b.obs_cumcar = '".$fDesNovcum."'
                              WHERE 
                                    a.num_despac = '".$fNumDespac."'  ";


              if ( $fConsult -> ExecuteCons( $mUpdCumpli, "R" ) === FALSE ) {
                throw new Exception( "Error registrando cumplido en cargue.", "7001" );
              }
            
              // Coloca novedad de cumplido en cargue en antes de sitio
              setNovedadNC( $fNomUsuari, $fPwdClavex, $fCodTransp, $fNumManifi,
                            $fNumPlacax, $fCodNoveda, NULL, $fTimDuraci, 
                            $fFecNoveda, $fDesNovcum, $fNomNoveda, $fNomContro,
                            $fNomSitiox, $fNumViajex, $fFotNoveda, $fCodRemdes);
            }
          }


        # Replicacion de novedad a Crona como NC 2016-07-25 nelson

        # Valida NUmero de viaje -----------------------------------------------------------------------------------------------------------------
        if($fNumViajex != NULL){         
          $mValidaViaje = ValidaNumViajeExist($fCodTransp, $fNumViajex, $fNumManifi, $fNumPlacax, $fConsult);   
          $fConsult -> CloseConection( $fNomUsuari." - Despacho: ".$fNumDespac." - Codigo verificación: ".$fCodVerifi." - Desde setNovedadPC" );       
          if($mValidaViaje[cod_respon] == '1000') {
              $mNoveCorona =  setNovedadNC( $fNomUsuari, $fPwdClavex, $mValidaViaje["msg_respon"]["cod_transp"], $mValidaViaje["msg_respon"]["cod_manifi"],
                                            $mValidaViaje["msg_respon"]["num_placax"], $fCodNoveda, $fCodContro, $fTimDuraci, 
                                            $fFecNoveda, $fDesNoveda , $fNomNoveda, $fNomContro,
                                            $fNomSitiox, NULL );
              
               
          }
        }else{
          $fConsult -> CloseConection( $fNomUsuari." - Despacho: ".$fNumDespac." - Codigo verificación: ".$fCodVerifi." - Desde setNovedadPC" );
        }

        $mMsgTrackin = '';
        
        if ($fNomAplica == 'satt_fortec'){
          $mtrackin = new TrackingDespac ();
          $mInsert = $mtrackin -> setTrackingDespac($fNumDespac, null , 2, $fNoveda["cod_noveda"], $fNomUsuari);

          if ($mInsert) {
            $mMsgTrackin = "; ind_trackin: Inserto la homologado de novedad exitosamente";
          }
        }

        $fReturn = "code_resp:1000; msg_resp:Se inserto la novedad PC de forma satisfactoria; cod_verifi: ".$fCodVerifi.$mMsgTrackin;

      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
   /***********************************************************************
  * Funcion Inserta los puestos para cambio de ruta en tab_despac_seguim. *
  * @fn setCambioRuta.                                                    *
  * @param $fNomUsuari: string Usuario.                                   *
  * @param $fPwdClavex: string Clave.                                     *
  * @param $fCodTransp: string Codigo de la transportadora.               *
  * @param $fNumManifi: string Numero del manifiesto                      *
  * @param $fNumPlacax: string Numero de la placa.                        *
  * @param $fCodRutasx: string Codigo de la novedad.                      *
  * @param $fTimDuraci: string Codigo de la novedad.                      *
  * @param $fCodPcbase: string Codigo puestos de empalme.                 *
  * @param $fCodContrs: string Codigo puestos de control.                 *
  * @return string mensaje de respuesta.                                  *
  ************************************************************************/
  function setCambioRuta( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fNumManifi = NULL,
                          $fNumPlacax = NULL, $fCodRutasx = NULL, $fTimDuraci = NULL, $fCodPcbase = NULL,
                          $fCodContrs = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setCambioRuta" );

    $fDatValida = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_transp" => $fCodTransp, 
                         "num_manifi" => $fNumManifi, "num_placax" => $fNumPlacax, "cod_rutasx" => $fCodRutasx, 
                         "tim_duraci" => $fTimDuraci, "cod_pcbase" => $fCodPcbase, "cod_contrs" => $fCodContrs );
    $fValidator = new Validator( $fDatValida, "cambioruta_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fDatValida, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }

        //Se consulta el numero de despacho
        $fQueryNumDespac = "SELECT a.num_despac ".
                             "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                  "".BASE_DATOS.".tab_despac_vehige b ".
                            "WHERE a.num_despac = b.num_despac ".
                              "AND a.cod_manifi = '".trim( $fNumManifi )."' ".
                              "AND b.cod_transp = '".trim( $fCodTransp )."' ".
                              "AND b.num_placax = '".trim( $fNumPlacax )."' ".
                              "AND a.fec_salida IS NOT NULL ".
                              "AND a.fec_llegad IS NULL ".
                              "AND b.ind_activo = 'S' ";
        $fConsult -> ExecuteCons( $fQueryNumDespac );

        if( 0 != $fConsult -> RetNumRows() )
        {
          $fResultNumDespac = $fConsult -> RetMatrix( "a" );
          $fNumDespac = $fResultNumDespac[0]["num_despac"];   
        }
        else
        {
          throw new Exception( "No se encuentra numero de despacho.", "1999" );
          break;
        }

        $fDespac = new DespacSat( $fExcept );

        if( !$fDespac -> despacInRout( $fNumDespac ) )
        {
          throw new Exception( "El despacho ".$fNumDespac." no se encuentra en ruta, o no esta registrado.", "6001" );
          break;
        }

        $fec_cambru = date("Y-m-d H:i:s");
        $fDesp = $fDespac -> getDespac( $fNumDespac, $fCodTransp );

        $fConsult -> StartTrans();
        
        $query = "SELECT b.cod_contro,b.cod_rutasx
                    FROM ".BASE_DATOS.".tab_despac_vehige a,
                         ".BASE_DATOS.".tab_despac_seguim b
                   WHERE a.num_despac = '".$fNumDespac."' 
                     AND b.num_despac = a.num_despac 
                     AND b.cod_rutasx = a.cod_rutasx";
        $consulta = $fConsult -> ExecuteCons( $query, "R" );
        $antplaru = $fConsult -> RetMatrix();
      
        $query = "SELECT a.val_duraci
                    FROM ".BASE_DATOS.".tab_genera_rutcon a
                   WHERE a.cod_rutasx = '".$fCodRutasx."' AND
                         a.cod_contro = '".$fCodPcbase."'";
        $consulta = $fConsult -> ExecuteCons( $query );
        $pcduracibase = $fConsult -> RetMatrix();
        $tiemacu = 0;
  
        $fCodContrs = explode( ',', $fCodContrs);
  
        for( $i = 0; $i < sizeof( $fCodContrs ); $i++ )
        {
          $query = "SELECT a.val_duraci
                      FROM ".BASE_DATOS.".tab_genera_rutcon a
                     WHERE a.cod_rutasx = '".$fCodRutasx."' AND
                           a.cod_contro = '".$fCodContrs[$i]."'";
          $consulta = $fConsult -> ExecuteCons( $query );
          $pcduraci = $fConsult -> RetMatrix();
          if( $fCodPcbase == $fCodContrs[$i] )
          {
            $tiempcum = $fTimDuraci;
          }
          else
          {
            $tiempcum = $tiempcum + ( $pcduraci[0][0] - $pcduracibase[0][0] ) + $fTimDuraci;
          }
          $query = "SELECT DATE_ADD( '".$fec_cambru."', INTERVAL ".$tiempcum." MINUTE )";
          $consulta = $fConsult -> ExecuteCons( $query );
          $timemost = $fConsult -> RetMatrix();
          $query = "INSERT INTO ".BASE_DATOS.".tab_despac_seguim
                    ( num_despac, cod_contro, cod_rutasx, fec_planea,
                      fec_alarma, ind_estado, usr_creaci, fec_creaci )
             VALUES (".$fNumDespac.", ".$fCodContrs[$i].", ".$fCodRutasx.",
                    '".$timemost[0][0]."', '".$timemost[0][0]."', '1', '".$fNomUsuari."',
                    '".$fec_cambru."' )";
        if ( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
          throw new Exception( "Error en Insert.", "3001" );
        }
     
        $fConsult -> Commit();
        $fReturn = "code_resp:1000; msg_resp:Se inserto el cambio de ruta satisfactoriamente.";
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }
      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
   /***********************************************************************
  * Funcion Inserta una novedad NC en una aplicacion SAT.                *
  * @fn setNovedadNC.                                                    *
  * @brief Inserta una novedad NC en una aplicacion SAT.                 *
  * @param $fNomUsuari: string Usuario.                                  *
  * @param $fPwdClavex: string Clave.                                    *
  * @param $fCodTransp: string Codigo de la transportadora.              *
  * @param $fNumManifi: string Numero del manifiesto                     *
  * @param $fNumPlacax: string Numero de la placa.                       *
  * @param $fCodNoveda: string Codigo de la novedad.                     *
  * @param $fCodContro: string Codigo puest control.                     *
  * @param $fTimDuraci: string Tiempo duracion.                          *
  * @param $fFecNoveda: string Fecha de la novedad.                      *
  * @param $fDesNoveda: string Descripcion de la novedad.                *
  * @param $fNomNoveda: string Nombre novedad.                           *
  * @param $fNomContro: string Nombre puesto de control.                 *
  * @return string mensaje de respuesta.                                 *
  ************************************************************************/
  function setNovedadNC( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fNumManifi = NULL,
                         $fNumPlacax = NULL, $fCodNoveda = NULL, $fCodContro = NULL, $fTimDuraci = NULL, 
                         $fFecNoveda = NULL, $fDesNoveda = "", $fNomNoveda = NULL, $fNomContro = NULL,
                         $fNomSitiox = NULL, $fNumViajex = NULL, $fFotNoveda = NULL, $fCodRemdes = NULL,
                         $fTimSegpun = NULL, $fTimUltpun = NULL, $fKmsVehicu = NULL, $fNomAplica = NULL)
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setNovedadNC" );

    $fDatValida = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_transp" => $fCodTransp, 
                         "num_manifi" => $fNumManifi, "num_placax" => $fNumPlacax, "cod_contro" => $fCodContro, 
                         "cod_noveda" => $fCodNoveda, "fec_noveda" => $fFecNoveda, "des_noveda" => $fDesNoveda, 
                         "tim_duraci" => $fTimDuraci, "nom_contro" => $fNomContro, "nom_noveda" => $fNomNoveda,
                         "nom_sitiox" => $fNomSitiox );

    $fValidator = new Validator( $fDatValida, "novedad_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    $fDatValidax = $fDatValida;
    unset( $fDatValida, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if ($fCodTransp == '800105213') {
          mail("andres.torres@eltransporte.org", "correo prueba constantes", var_export("sadasd", true));
        }
        if( $fNomAplica != NULL  &&  strpos($fNomAplica, '_' ) != false ) // valida si llega nom_aplica y si tiene _ en el texto
        {
          if(file_exists('/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc') )
          {
            include_once('/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc');
          }elseif (file_exists('/var/www/html/ap/integradorgps/'.$fNomAplica.'/constantes.inc') ) {
            include_once('/var/www/html/ap/integradorgps/'.$fNomAplica.'/constantes.inc');
          }elseif (file_exists( AplKon )) {
            include_once( AplKon );
          }else{
            throw new Exception( "Aplicacion GL ".$fNomAplica."  en sat trafico no encontrada", "1999" );
          }
        }else {
          throw new Exception( "Aplicacion FARO en sat trafico no encontrada NC", "1999" );
          break;
        }

        // colocamos la misma novedad si la transportadora es SKY = 900714469
        $fCodNoveda = $fCodTransp == '900714469' ? '213' : $fCodNoveda;

        $fFecNoveda = NULL == $fFecNoveda ? date( "Y-m-d H:i" ) : $fFecNoveda;
        
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
        if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }

        $fResultPosee = NULL;
        # Consulta si la transportadora es una tercera de corona, usa true o false
        # si true= puede guardar novedades sin importar que el despacho tenga llegada(Finalizado)
        $fQueryPoseedCoro = " SELECT cod_poseed FROM ".BASE_DATOS.".tab_despac_corona WHERE cod_poseed = '".$fCodTransp."' GROUP BY cod_poseed ";
        $fConsult -> ExecuteCons( $fQueryPoseedCoro );        
        $fResultPosee = $fConsult -> RetArray();  
        # Se filtra segun si el tercero le transporta a corona; busca el despacho

        $mFinaly = $fResultPosee["cod_poseed"] != NULL ? true : false; 


        //parche cuando la novedad viene desde las app ya que el manifiesto no siempre es el vs :(
        if($fNomUsuari == 'InterfCorona')
        {
            $mValida = "SELECT cod_manifi FROM   ".BASE_DATOS.".tab_despac_corona WHERE  num_despac = '".$fNumManifi."'";
            $fConsult -> ExecuteCons( $mValida );
            if( 0 != $fConsult -> RetNumRows() )
            {
              $fManiViaje = $fConsult -> RetMatrix( "a" );
              $fNumManifi = $fManiViaje[0]["cod_manifi"];   
            }
        }

       
        //Se consulta el numero de despacho
        $fQueryNumDespac = "SELECT a.num_despac ".
                             "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                  "".BASE_DATOS.".tab_despac_vehige b ".
                            "WHERE a.num_despac = b.num_despac ".
                              "AND a.cod_manifi = '".trim( $fNumManifi )."' ".
                              "AND b.num_placax = '".trim( $fNumPlacax )."' ".
                              "AND a.fec_salida IS NOT NULL ".
                              "AND a.fec_llegad IS NULL ";
        
        //parche debido a que en central los despachos 
        //tienen el nit de fortecem y en gl(generadores) si tienen el de la transportadora correspondiente
        if ( $fCodTransp != '900657956'){
          $fQueryNumDespac .= " AND b.cod_transp = '".trim( $fCodTransp )."' ";
        }
        $fQueryNumDespac .=   ($mFinaly == true ? " " : "AND a.fec_llegad IS NULL "); # si true trae el despacho sin importar si esta finalizado
        $fQueryNumDespac .=   "AND b.ind_activo = 'S' ";

        $fConsult -> ExecuteCons( $fQueryNumDespac );
        if( 0 != $fConsult -> RetNumRows() )
        {
          $fResultNumDespac = $fConsult -> RetMatrix( "a" );
          $fNumDespac = $fResultNumDespac[0]["num_despac"];
        }
        else
        {
          //Se consulta el numero de despacho Consolidador
          $fQueryNumDespacConsol = "SELECT a.cod_despad 
                                      FROM ".BASE_DATOS.".tab_consol_despac a 
                                INNER JOIN ".BASE_DATOS.".tab_despac_despac b 
                                        ON a.cod_deshij = b.num_despac 
                                INNER JOIN ".BASE_DATOS.".tab_despac_vehige c 
                                        ON b.num_despac = c.num_despac 
                                     WHERE b.cod_manifi = '".trim( $fNumManifi )."' 
                                       AND c.cod_transp = '".trim( $fCodTransp )."'
                                       AND c.num_placax = '".trim( $fNumPlacax )."'
                                       AND b.fec_salida IS NOT NULL
                                       AND b.fec_llegad IS NULL
                                       #AND c.ind_activo = 'S' ";
          $fQueryNumDespacConsol .=   ($mFinaly == true ? " " : " AND b.fec_llegad IS NULL "); # si true trae el despacho sin importar si esta finalizado

          
          $fConsult -> ExecuteCons( $fQueryNumDespacConsol );
          if( 0 != $fConsult -> RetNumRows() )
          {
            $fResultNumDespac = $fConsult -> RetMatrix( "a" );
            # Coloca el despacho padre - Consolidado, en caso que sea un consolidado
            $fNumDespac = $fResultNumDespac[0]["cod_despad"];
          }else{
            # Si no encuentra un consolidado y no encuentra el despacho vigente debe acabar la ejecucion con el catch

            //$mFile = fopen("/var/www/html/ap/interf/app/faro/logs/NovedadesError".date("Ymd").".txt", "a+");
            //fwrite($mFile, "------------------     setNovedadNC  ".date("Y m d H:i:s").": Message-Catch: No se encuentra numero de despacho NC. ------------------------\n");
            //fwrite($mFile, "Data Input NC:\n");
            //fwrite($mFile, var_export($fDatValidax, true)."\n");
            //fwrite($mFile, "fQueryNumDespac: \n");
            //fwrite($mFile, $fQueryNumDespac." \n");  
            //fwrite($mFile, "fQueryNumDespacConsol: \n");
            //fwrite($mFile, $fQueryNumDespacConsol." \n");  
            //fclose($mFile);

            throw new Exception( "No se encuentra numero de despacho NC.", "1999" );
            break;
          }
        }

        // borrar protocolo
        /*
        if( $fNumManifi == 'VS-742750' &&  $fCodTransp == '860068121' && $fNumPlacax == 'XVP517' )
        {
          $mProtoc = new Protocol ($fConsult  , $fNomUsuari, $fCodTransp, $fNumDespac , $fNumManifi, $fNumPlacax, 
                     $fCodNoveda, $fCodContro, $fTimDuraci, $fFecNoveda, $fDesNoveda, $fNomNoveda, 
                     $fNomContro, $fNomSitiox, $fNumViajex);
          $mRespon = $mProtoc -> getResponse();
          mail('nelson.liberato@eltransporte.org', 'Protocolo WS faro', var_export($mRespon, true) );
          die();  
        }
        */


        $fDespac = new DespacSat( $fExcept );

        # Se parchea el if para que valide cuando la empresa no sea tercera de corona
 
        if( !$fDespac -> despacInRout( $fNumDespac ) && $mFinaly == false )
        {
          throw new Exception( "El despacho ".$fNumDespac." no se encuentra en ruta, o no esta registrado NC.", "6001" );
          break;
        }

        $fDesp = $fDespac -> getDespac( $fNumDespac, $fCodTransp ); 
        $fNoveda = $fDespac -> getNovedad( $fCodNoveda, $fNomNoveda );
        if( FALSE === $fNoveda )
        {
          throw new Exception( "Novedad ". $fNomNoveda ." no existente.", "6001" );
          break;
        }
        
        # Se parcha este pedaso ya que cuando se trate de una empresa tercera de corona debe reportar antes del puesto habilitado
        # por el moemnto solo es para mct pero se debe consultar las empresas terceras de corona para que entre al if y cambie el codigo PC
        if( $fCodTransp == '830004861' || $fCodTransp == '860068121' || $fCodTransp == '900437294' )
        {
          $fCodContro = NULL;
          # Trae el puesto habilitado y lo cambia por el que entra, el que nos envian
          $mCodControHab = getNextPC($fNumDespac, $fExcept);
          $fCodContro = $mCodControHab["cod_contro"];
          $fNomSitiox = $fNomSitiox == NULL ? $mCodControHab["nom_contro"] : $fNomSitiox;          
        } 


        if( ( $fCodContro === '' || $fCodContro === NULL ) && ( $fNomContro === '' || $fNomContro === NULL ) )
        {
         //Se obtiene el ultimo cod_contro
          $fQueryCodContro = "(SELECT cod_contro, fec_contro, 'nc' FROM ".BASE_DATOS.".tab_despac_contro WHERE num_despac = '".$fNumDespac."')
                                UNION
                              (SELECT cod_contro, fec_noveda, 'pc' FROM ".BASE_DATOS.".tab_despac_noveda WHERE num_despac = '".$fNumDespac."')
                              ORDER BY 2 DESC";

          $fConsult -> ExecuteCons( $fQueryCodContro );
          
          if( 0 == $fConsult -> RetNumRows() )
          {
           //Si no hay novedades se busca el primer puesto del plan de ruta
            $fQueryCodContro = "SELECT a.cod_contro ".
                                 "FROM ".BASE_DATOS.".tab_despac_seguim a ".
                                "WHERE a.num_despac = '".$fNumDespac."' ".
                                  "AND a.fec_planea = ( SELECT MIN( d.fec_planea ) ".
                                                         "FROM ".BASE_DATOS.".tab_despac_seguim  d ".
                                                        "WHERE d.num_despac = '".$fNumDespac."' ) ";

            $fConsult -> ExecuteCons( $fQueryCodContro );
          }
          
          $fResultCodContro = $fConsult -> RetMatrix( "a" );
          $fCodContro = $fResultCodContro[0]["cod_contro"];


          if($fCodContro == '')
          {
            # Trae el puesto habilitado y lo cambia por el que entra, el que nos envian
            $mCodControHab = getNextPC($fNumDespac, $fExcept);
            $fCodContro = $mCodControHab["cod_contro"];
            $fNomSitiox = $fNomSitiox == NULL ? $mCodControHab["nom_contro"] : $fNomSitiox;    
          }

        }
        elseif( $fCodContro == '0' )
        {
          $fCodContro = $fDespac -> getPcontroFromDespac( $fCodContro, $fNomContro, $fNumDespac );
          if( FALSE === $fCodContro )
          {
            throw new Exception( "Puesto de control ".$fNomContro." no existente.", "6001" );
            break;
          }
        } 
  
        // ultimo recurso para buscar el PC siguiente activo
        if($fCodContro == '')
        {
          # Trae el puesto habilitado y lo cambia por el que entra, el que nos envian
          $mCodControHab = getNextPC($fNumDespac, $fExcept);
          $fCodContro = $mCodControHab["cod_contro"];
          $fNomSitiox = $fNomSitiox == NULL ? $mCodControHab["nom_contro"] : $fNomSitiox;      
        }

        $fQueryCodConsec = "SELECT MAX( cod_consec ) AS cod_consec ".
                             "FROM ".BASE_DATOS.".tab_despac_contro ".
                            "WHERE num_despac = '".$fNumDespac."' ".
                              "AND cod_rutasx = '".$fDesp["cod_rutasx"]."' ";

        $fConsult -> ExecuteCons( $fQueryCodConsec );
        $fResultCodConsec = $fConsult -> RetMatrix( "a" );

        if( 0 != $fConsult -> RetNumRows() )
          $fConse = $fResultCodConsec[0]["cod_consec"]+1;
        else  
          $fConse = 1;

        //valida si el nombre del sitio existe
        $query = "SELECT cod_sitiox ".
                   "FROM ".BASE_DATOS.".tab_despac_sitio ".
                  "WHERE nom_sitiox = '".$fNomSitiox."'";
        $consulta = $fConsult -> ExecuteCons( $query );
        $sitio = $fConsult -> RetMatrix( );
       
        $fConsult -> StartTrans();
       
        if( !$sitio )
        {
          $query = "SELECT MAX( cod_sitiox ) ".
                     "FROM ".BASE_DATOS.".tab_despac_sitio ";
          $consulta = $fConsult -> ExecuteCons( $query );
          $sitio = $fConsult -> RetMatrix( );
         
          $maxsit = $sitio[0][0] + 1;
          $query = "INSERT INTO ".BASE_DATOS.".tab_despac_sitio".
                  "( cod_sitiox, nom_sitiox ) VALUES ( $maxsit, '".$fNomSitiox."' ) ";
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert 1.", "3001" );
        }
        else
        {
          $maxsit = $sitio[0][0];
        }
         
        //Se hacen ajustes para las alarmas cuando la novedad mantiene alarma
        $tieadi = $fTimDuraci;
        $query = "SELECT fec_manala, fec_ultnov, fec_salida ".
                   "FROM ".BASE_DATOS.".tab_despac_despac
                   WHERE num_despac = '".$fNumDespac."' ";
        $consulta = $fConsult -> ExecuteCons( $query );
        $fec_manala = $fConsult -> RetMatrix( );
        $fec_ultnov = "'".$fec_manala[0][1]."'";
        $fec_salida = "'".$fec_manala[0][2]."'";
        $fec_manala = "'".$fec_manala[0][0]."'";
     
        $query = "SELECT ind_manala, ind_notsup, nom_noveda ".
        //$query = "SELECT ind_manala".
                   "FROM ".BASE_DATOS.".tab_genera_noveda
                   WHERE cod_noveda = '".$fCodNoveda."' ";
        $consulta = $fConsult -> ExecuteCons( $query );
        $ind_manala = $fConsult -> RetMatrix( );
        $ind_notsup = $ind_manala[0][1];//Jorge 120769
        $nove = $ind_manala[0][2];
        $ind_manala = $ind_manala[0][0];
        
     
        $query = "( SELECT tiem_duraci, fec_contro
                      FROM  ".BASE_DATOS.".tab_despac_contro
                     WHERE num_despac = ".$fNumDespac." )
                   UNION
                  ( SELECT tiem_duraci, fec_noveda
                      FROM  ".BASE_DATOS.".tab_despac_noveda 
                     WHERE num_despac = ".$fNumDespac." )
                  ORDER BY 2 DESC
                     LIMIT 1 ";
                    
        $consulta = $fConsult -> ExecuteCons( $query );
        $valretras = $fConsult -> RetMatrix( );
        $valretras = $valretras[0][0];
     
        if( $ind_manala == '0' )
        {
          $fec_manala = "NULL"; 
        }
        else
        {
          if( $fec_manala == "''" )
          {
            if( $fec_ultnov == '' ||  $fec_ultnov == "''" )
              $fec_manala = $fec_salida;
            else
              $fec_manala = $fec_ultnov; 
          }
          if( $valretras == "" )
            $valretras = 0;
          $tieadi = $valretras;
        }
        if( $fec_manala == '' )
          $fec_manala = "NULL";
        if( $fTimDuraci == '' )
          $tieadi = 0;
          
        if ($ind_notsup == 1)//Jorge 120769
        {
            $query = "SELECT a.cod_tercer,a.abr_tercer ,b.num_placax, c.con_telmov, d.abr_tercer AS nom_conduc, c.cod_manifi
                                  FROM " . BASE_DATOS . ".tab_tercer_tercer a,
                                              " . BASE_DATOS . ".tab_despac_vehige b,
                                              " . BASE_DATOS . ".tab_despac_despac c,
                                              " . BASE_DATOS . ".tab_tercer_tercer d
                              WHERE b.num_despac = '" . $fNumDespac . "' 
                                    AND b.num_despac = c.num_despac
                                    AND b.cod_conduc = d.cod_tercer
                                    AND a.cod_tercer = b.cod_transp";
            $empre = $fConsult -> ExecuteCons( $query );
            $empre = $fConsult -> RetMatrix( );
            
            $info .="EMPRESA: " . $empre[0][1] . " \n";
            $info .="DESPACHO: " . $fNumDespac . " \n";
            $info .="MANIFIESTO: " . $empre[0]['cod_manifi'] . " \n";
            $info .="PLACA DEL VEHICULO: " . $empre[0][2] . " \n";
            $info .="CONDUCTOR: " . $empre[0]['nom_conduc'] . " \n";
            $info .="TELEFONO CONDUCTOR: " . $empre[0]['con_telmov'] . " \n";
            $info .="SITIO DE SEGUIMIENTO: " . $fNomSitiox . " \n";
            $info .="FECHA DE LA NOVEDAD: " . $fFecNoveda . " \n";
            $info .="NOVEDAD: " . $nove . " \n";
            $info .="USUARIO: " . $fNomUsuari . " \n";
            $info .="OBSERVACION:  \n";
            $info .=" " . $fDesNoveda . " \n";

            $asunto = "NOTIFICACION SUPERVISOR";

            //mail(MAIL_SUPERVISORES, $asunto, $info, 'From: faroavansat@eltransporte.com');
            mail(FarMai, $asunto, $info, 'From: faroavansat@eltransporte.com');
        }

        // Suma minutos que tenga la novedad por defecto 
        if($fNoveda["ind_tieaut"] == '1' && (int)$fNoveda["tie_minaut"] > (int)'0' )
        {
          $tieadi += (int)$fNoveda["tie_minaut"];
        }
   
        # Parche si la novedad es para corona 
				$temp = $fDesNoveda;
				$fields = "";
				$values = "";
        $exchange = explode("^", $temp);
        
        if(count($exchange) > 1){
					$latlon = json_decode($exchange[1]);
          $fValLatitu = $latlon -> latitud  == '' || $latlon -> latitud  == 0 || $latlon -> latitud  == "0" ? "" : $latlon -> latitud ;
          $fValLongit = $latlon -> longitud == '' || $latlon -> longitud == 0 || $latlon -> longitud == "0" ? "" : $latlon -> longitud;        
          $fDesNoveda = $exchange[0];
					$fields = " val_latitu,val_longit, ";
					$values = " '$fValLatitu','$fValLongit' , ";
        }

        /* concatena texto de tiempo generado */

        $fDesNoveda .= $tieadi > 0 ? '. Tiempo generado: '.$tieadi.' Minutos ' : '';

        /* Obtiene el codigo numerico del usuario que usa la interfaz*/
        $mCodUsrcre = "SELECT cod_consec, cod_usuari FROM  ".BASE_DATOS.".tab_genera_usuari WHERE cod_usuari = '".$fNomUsuari."'  ";
        $mCodUsrcre = $fConsult -> ExecuteCons( $mCodUsrcre );
        $mCodUsrcre = $fConsult -> RetMatrix( 'a' );
        $mCodUsrcre = $mCodUsrcre[0]['cod_consec'] == '' ? '668' : $mCodUsrcre[0]['cod_consec'];

     
        $mRemdes = [];
        if( ( $fCodRemdes != '' || $fCodRemdes != NULL ) && in_array( $fNoveda["cod_noveda"], ['9265','9263','9264','9271' ] )  )
        {
          $mRemdes = $fDespac ->  getRemdes($fCodRemdes);
          $fDesNoveda .= "Evento en descargue: ".$mRemdes['nom_remdes']." - Dir: ".$mRemdes['dir_remdes']." - Ciu: ".$mRemdes["nom_ciudad"];
        }
          
        // Ajuste de parche, solo debe ejecutarce si es de APP que viene la novedad, de lo contrario  no se debe ejecutar el parche, ID 358655
        // Si el usuario y nit transportadora que entra tiene interfaz y viene de app, se ejecuta el parche
        $mQueryUser = 'SELECT cod_operad, cod_transp, nom_usuari, clv_usuari
                         FROM  '.BASE_DATOS.'.tab_interf_parame 
                        WHERE 1 = 1
                          AND cod_transp = "'.$fCodTransp.'" 
                          AND cod_operad = "85"
                          AND ind_estado = 1
                          AND nom_usuari = "'.$fNomUsuari.'" ';
        $consulta = $fConsult -> ExecuteCons( $mQueryUser );
        $mUsuariApp = $fConsult -> RetMatrix( 'a' ); 
        if(sizeof($mUsuariApp) > 0) // si encuntra el usuario y nit es porque la novedad biene desde la app
        {
          // ---------------------------------------------------------------------------------------------------------------------------------------------------------------
          // parche para no correr tiempo cuando el despacho tiene como ultima novedad la pernoctación APLICA PARA NOVEDAD DE APP ------------------------------------------
          $mQuery = 'SELECT 
                        -- Cuenta los incios y los fin de pernoc y valida si  los incios son mayores a lof finaliza, es porque está pernoctando, si son iguales es porque ya no está pernoctando
                        IF( (SUM( IF( a.cod_noveda IN (6, 245, 258, 9008) , 1 , 0 ) ) ) != SUM( IF( a.cod_noveda IN (211, 9025) , 1 , 0 ) ), 0 , 1 ) AS ind_modifi_alarma
                      FROM
                      (
                        -- Se obitene las novedades registradas en antes y en sitio de las pernoctaciones. (inicio y fin)
                        ( SELECT tiem_duraci, fec_contro, cod_contro, cod_noveda
                            FROM  '.BASE_DATOS.'.tab_despac_contro
                           WHERE num_despac = "'.$fNumDespac.'" AND
                                 cod_noveda IN ( 6, 245, 258, 9008, 211, 9025 )
                        )
                         UNION
                        ( SELECT tiem_duraci, fec_noveda, cod_contro, cod_noveda
                            FROM  '.BASE_DATOS.'.tab_despac_noveda 
                           WHERE num_despac = "'.$fNumDespac.'" AND
                                 cod_noveda IN ( 6, 245, 258, 9008, 211, 9025 )
                        )

                      ) a ';

          $consulta = $fConsult -> ExecuteCons( $mQuery );
          $CorrerAlarma = $fConsult -> RetMatrix( 'a' ); 

          $mUltimoRegistro = getNextPC( $fNumDespac, $fExcept );
          if($CorrerAlarma[0]['ind_modifi_alarma'] == '0' || $mUltimoRegistro['ind_fuepla'] == '1')
          {
            $tieadi = '0';
          }
        }

        $fQueryInsNoved = "INSERT INTO ".BASE_DATOS.".tab_despac_contro ".
                            "( num_despac , cod_rutasx , cod_contro , cod_consec , ".
                              "obs_contro , cod_noveda , tiem_duraci , fec_contro , ".
                              "cod_sitiox, usr_creaci ,$fields fec_creaci ) ".
                          "VALUES ( '".$fNumDespac."', '".$fDesp["cod_rutasx"]."', ".
                                   "'".$fCodContro."', '".$fConse."', ".
                                   "'INTERFAZ - ".str_replace("'", "", $fDesNoveda)." ', '".$fNoveda["cod_noveda"]."', ".
                                   "'".$tieadi."', '".$fFecNoveda."', ".
                                   "'".$maxsit."', '".$fNomUsuari."',$values NOW() ) ON DUPLICATE KEY UPDATE fec_creaci = DATE_ADD( VALUES(fec_creaci), INTERVAL 1 SECOND ) ";
        if( $fConsult -> ExecuteCons( $fQueryInsNoved, "R" ) === FALSE ) {
          throw new Exception( "Error en Insert 2.".$fCodTransp." Pre: ".$fQueryInsNoved, "3001" );
        }

        

        #$condAcargoFaro = $fNoveda["cod_noveda"] == NovAcarFar ? " ind_defini = '0', " : ''; 


        switch ($fNoveda["cod_noveda"]) 
        {
             case '9996':
               $condAcargoFaro = " ind_defini = '1', ";
               break;
             case '9995':
               $condAcargoFaro = " ind_defini = '0', ";
               break;
             default:
               $condAcargoFaro = "   ";
               break;
        }


        
        $fQueryDespacDes = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                              "SET cod_consec = '".$fConse."', ".
                                  "cod_conult = '".$fCodContro."', ".
                                  "usr_ultnov = '".$fNomUsuari."', ".
                                  "fec_ultnov = '".$fFecNoveda."', ".
                                  "cod_ultnov = '".$fNoveda["cod_noveda"]."', ".
                                  "fec_manala = ".$fec_manala.", ".
                                  "usr_modifi = '".$fNomUsuari."', ".
                                  $condAcargoFaro.
                                  "fec_modifi = NOW() ".
                            "WHERE num_despac = '".$fNumDespac."' ";

   

        if( $fConsult -> ExecuteCons( $fQueryDespacDes, "R" ) === FALSE )
          throw new Exception( "Error en Insert 3.", "3001" );
        
        $query= "SELECT cod_contro
                   FROM ".BASE_DATOS.".tab_despac_seguim
                  WHERE num_despac = '".$fNumDespac."' 
                    AND cod_rutasx = '".$fDesp["cod_rutasx"]."'
               ORDER BY fec_planea";
        $consulta = $fConsult -> ExecuteCons( $query );
        $seguim = $fConsult -> RetMatrix( );          
        for( $i = 0; $i < sizeof( $seguim ); $i++ )
        {
          if( $fCodContro == $seguim[$i][0] )
          {
            break;
          }
          else
          {
            $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                         SET ind_estado = '0'
                       WHERE num_despac = '".$fNumDespac."' 
                         AND cod_rutasx = '".$fDesp["cod_rutasx"]."' 
                         AND cod_contro = '".$seguim[$i][0]."' ";
                         
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert 4.", "3001" );
          }
        }
        
        if( $fNoveda["cod_noveda"] == '9998' )
        {
          $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                       SET ind_estado = '0'
                     WHERE num_despac = '".$fNumDespac."' AND
                           cod_rutasx = '".$fDesp["cod_rutasx"]."'  ";
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert 5.", "3001" );
            
          $query = "SELECT b.cod_rutasx
                    FROM ".BASE_DATOS.".tab_despac_seguim b
                   WHERE num_despac = '".$fNumDespac."' AND
                         fec_creaci =( SELECT MAX( fec_creaci ) FROM ".BASE_DATOS.".tab_despac_seguim WHERE num_despac = '".$fNumDespac."' LIMIT 1 )";
          $consulta =  $fConsult -> ExecuteCons( $query );
          $cod_rutasx = $fConsult -> RetMatrix( );
          $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                       SET cod_rutasx = ".$cod_rutasx[0][0].",
                           usr_modifi = '".$fNomUsuari."',
                           fec_modifi = NOW()
                     WHERE num_despac = ".$fNumDespac."";
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Insert 6.", "3001" );
        }
        
       $tieAdi2 = 30 + (int)$tieadi;
        
        //Se recalcula la fecha planeada
//        $query = "SELECT DATE_ADD( fec_planea, INTERVAL ".$tieAdi2." MINUTE )
//                    FROM ".BASE_DATOS.".tab_despac_seguim 
//                   WHERE num_despac = '".$fNumDespac."' 
//                     AND cod_contro = '".$fCodContro."'
//                     AND cod_rutasx = '".$fDesp["cod_rutasx"]."'";
//        $consulta = $fConsult -> ExecuteCons( $query );
//        $fec_planea = $fConsult -> RetMatrix( );
//        
//        $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim 
//                     SET fec_planea = '".$fec_planea[0][0]."'
//                   WHERE num_despac = '".$fNumDespac."'
//                     AND cod_contro = '".$fCodContro."'
//                     AND cod_rutasx = '".$fDesp["cod_rutasx"]."'";
//        if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
//              throw new Exception( "Error en Insert.", "3001" );
 
        $query = "SELECT b.cod_contro, DATE_ADD( b.fec_alarma, INTERVAL ".$tieAdi2." MINUTE )
                   FROM ".BASE_DATOS.".tab_despac_seguim b
                  WHERE b.num_despac = ".$fNumDespac." 
                    AND b.fec_planea >= ( SELECT a.fec_planea
                                            FROM ".BASE_DATOS.".tab_despac_seguim a
                                           WHERE a.num_despac = b.num_despac 
                                             AND a.cod_contro = ".$fCodContro." 
                                             AND a.cod_rutasx = '".$fDesp["cod_rutasx"]."')
               GROUP BY 1 
               ORDER BY 2";
       
        $consulta = $fConsult -> ExecuteCons( $query );
        $planrut = $fConsult -> RetMatrix( );
       
        $tiemacu = $tieadi;
        $actact = 0;
        if( $ind_manala == '0' )
          for($i = 0; $i < sizeof( $planrut ); $i++)
          {
            $query = "UPDATE ".BASE_DATOS.".tab_despac_seguim
                        SET fec_alarma = '".$planrut[$i][1]."',
                            usr_modifi = '".$fNomUsuari."',
                            fec_modifi = NOW()
                      WHERE num_despac = '".$fNumDespac."' 
                        AND cod_rutasx = '".$fDesp["cod_rutasx"]."' 
                        AND cod_contro = '".$planrut[$i][0]."' ";
    
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
                throw new Exception( "Error en Insert 7.", "3001" );
          }

            /////////////////////////////Registro de fotos//////////////////////////////


           if( $fFotNoveda != NULL ){

              $fotos = base64_decode($fFotNoveda);
              $fotos = json_decode($fotos);
              $pc = getNextPC($fNumDespac, $fExcept);
              if($fotos -> ind_fotoxx == 'cumplidos'){ 

                foreach ($fotos as $llave => $foto) {
                  foreach ($foto as $key => $value) {

                    $query = "SELECT MAX( a.num_consec ) as num_consec
                                FROM ".BASE_DATOS.".tab_cumpli_despac a
                               WHERE a.num_despac = '" . $fNumDespac . "'
                                 AND a.num_docume = '" . $llave . "'";
      
                    $consec = $fConsult -> ExecuteCons( $query );  
                    $consec = $fConsult -> RetMatrix( );                  
                    $consec = ((int)$consec[0]['num_consec']) + 1;
 
       
                    $insert = "INSERT INTO  ".BASE_DATOS.".tab_cumpli_despac
                                (
                                    num_despac, num_docume, num_consec,
                                    url_imagex, usr_creaci, fec_creaci
                                )
                                VALUES 
                                (
                                    '" . $fNumDespac . "', '" . $llave . "', '" . $consec . "',
                                    '" . $value . "', '" . $fNomUsuari . "',  NOW()
                                )";

                    //mail("miguel.romero@intrared.net", "if ok", $insert);
                    if( $fConsult -> ExecuteCons( $insert ) === FALSE )
                      throw new Exception( "Error en Insert 8.", "3001" );
                  }


                   $mUpdateCumpliCli = "UPDATE ".BASE_DATOS.".tab_despac_destin
                                    SET fec_llecli = NOW()
                                  WHERE num_despac = '".$fNumDespac."' AND num_docume = '".$llave."'";
                    //$fConsult -> ExecuteCons( $mUpdateCumpliCli );

                } 
              }else{

                foreach ($fotos as $key => $foto) {

                  $query = "SELECT MAX( a.num_consec ) as num_consec
                              FROM ".BASE_DATOS.".tab_despac_images a
                             WHERE a.num_despac = '".$fNumDespac."'
                               AND a.cod_contro = '".$pc['cod_contro']."'";
    
                  $consec = $fConsult -> ExecuteCons( $query );  
                  $consec = $fConsult -> RetMatrix( );                  
                  $consec = ((int)$consec[0]['num_consec']) + 1;
     
                  $insert = "INSERT INTO  ".BASE_DATOS.".tab_despac_images 
                              (
                                  num_despac , cod_contro , num_consec, usr_creaci ,
                                  fec_creaci ,bin_fotox2
                              )
                              VALUES 
                              (
                                  '".$fNumDespac."',  '".$pc['cod_contro']."', '".$consec."',  '" . $fNomUsuari . "',  
                                  NOW(), '".$foto."'
                              )";   
                  if( $fConsult -> ExecuteCons( $insert ) === FALSE )
                    throw new Exception( "Error en Insert 8.1.", "3001" );
                } 
              }
          } 
   
          /////////////////////////////Registro de fotos//////////////////////////////

 


        $fConsult -> Commit();

        // Si la novedad es la de llegada al descargue, puede ser m de una llegada por n descargues (Destinatarios)
        if($fNoveda["cod_noveda"] == '9263')
        { 
          
          $mCumpliDes = setFecAditio( $fNomUsuari , $fPwdClavex , $fNumDespac , 'fec_cumdes', $fFecNoveda , NULL, $fCodRemdes, $fNomAplica );

          if($mCumpliDes['cod_respon'] == '1000')
          {
             
              $fDesNoveda = 'Cumplido descargue de Integrador en '.$fNomSitiox." ".$mRemdes['nom_remdes']." - Dir: ".$mRemdes['dir_remdes']." - Ciu: ".$mRemdes["nom_ciudad"];
              setNovedadNC( $fNomUsuari, $fPwdClavex, $fCodTransp, $fNumManifi,
                         $fNumPlacax, '256', NULL, $fTimDuraci, 
                         $fFecNoveda, $fDesNoveda , $fNomNoveda, $fNomContro,
                         $fNomSitiox, $fNumViajex, $fFotNoveda, $fCodRemdes,
                         $fTimSegpun, $fTimUltpun );
          }
        }

        # Proceso de matriz de comunicacion por novedad y trasnportadora
        # valida si el codigo de la novedad genera protoco, si esi ejecuta protocolos
        $mProtoc = new Protocol ($fConsult  , $fNomUsuari, $fCodTransp, $fNumDespac , $fNumManifi, $fNumPlacax, 
                                 $fCodNoveda, $fCodContro, $fTimDuraci, $fFecNoveda, $fDesNoveda, $fNomNoveda, 
                                 $fNomContro, $fNomSitiox, $fNumViajex);
        $mRespon = $mProtoc -> getResponse();

        if($mRespon["cod_respon"] == '1000' ) {
          $mMsgProtoc = "; ind_protoc:". $mRespon["msg_respon"];
        }

        $mMsgTrackin = '';

        if ($fNomAplica == 'satt_fortec'){
          
          //Ejecuta clase que registra la homologación de la novedad y la inserta para enviar correo con el estado del despacho
          $mtrackin = new TrackingDespac();
          $mInsert = $mtrackin -> setTrackingDespac($fNumDespac, null , 2, $fNoveda["cod_noveda"], $fNomUsuari);
          
          if ($mInsert) {
            $mMsgTrackin = "; ind_trackin: Inserto la homologado de novedad exitosamente";
          }

        }

        $fReturn = "code_resp:1000; msg_resp:Se inserto la novedad NC de forma satisfactoria.".$mMsgProtoc.$mMsgTrackin;

        # Valida NUmero de viaje -----------------------------------------------------------------------------------------------------------------
        if($fNumViajex != NULL){
         
          $mValidaViaje = ValidaNumViajeExist($fCodTransp, $fNumViajex, $fNumManifi, $fNumPlacax, $fConsult);          
          if($mValidaViaje[cod_respon] == '1000') {
              $mNoveCorona =  setNovedadNC( $fNomUsuari, $fPwdClavex, $mValidaViaje["msg_respon"]["cod_transp"], $mValidaViaje["msg_respon"]["cod_manifi"],
                                            $mValidaViaje["msg_respon"]["num_placax"], $fCodNoveda, $fCodContro, $fTimDuraci, 
                                            $fFecNoveda, $fDesNoveda , $fNomNoveda, $fNomContro,
                                            $fNomSitiox, NULL );
              
               
          }
        }


      }
      catch( Exception $e )
      {
         
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.".$e -> getMessage();
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }

  /*! \fn: ValidaNumViajeExist
  *  \brief: Inserta novedad al despacho propio y despacho de corona en caso de exista el viaje
  *  \author: Ing. Nelson Liberato
  *  \date: 1/06/2015
  *  \date modified: 01/06/2015
  *  \param: $Conductor
  *  \param: $CodEmp
  *  \param: $USER
  *  \return array
  */
  function ValidaNumViajeExist( $fCodTransp, $fNumViajex, $fNumManifi, $fNumPlacax, $fConsult)
  {
    try
    {

      $mFindDessat = " SELECT num_dessat FROM ".BASE_DATOS.".tab_despac_corona  WHERE num_despac = '".$fNumViajex."' ";
      $fConsult -> ExecuteCons( $mFindDessat );
      if( 0 != $fConsult -> RetNumRows() )
      {
        $mFindDessat = $fConsult -> RetMatrix( "a" );
        $fNumDespac = $mFindDessat[0]["num_dessat"];   
      }
      else
      {
        throw new Exception( "No se encuentra numero de Viaje ".$fNumViajex.".".$mFindDessat, "1999" );
        break;
      }


      $mQuery = 'SELECT a.num_despac, 
                     b.cod_transp, 
                     a.cod_manifi,
                     b.num_placax 
                FROM ' . BASE_DATOS . '.tab_despac_despac a 
                     INNER JOIN ' . BASE_DATOS . '.tab_despac_vehige b ON a.num_despac = b.num_despac
                     INNER JOIN ' . BASE_DATOS . '.tab_tercer_tercer c ON b.cod_transp = c.cod_tercer                   
               WHERE a.fec_salida IS NOT NULL 
                 AND a.fec_salida <= NOW() 
                /* Nelson AND (a.fec_llegad IS NULL OR a.fec_llegad = "0000-00-00 00:00:00") */
                 AND a.ind_planru = "S" 
                 AND a.ind_anulad = "R"
                 AND b.ind_activo = "S"  
                 AND a.num_despac = "' . $fNumDespac . '" ';
      
      $fConsult -> ExecuteCons( $mQuery );

      if( 0 != $fConsult -> RetNumRows() )
      {
        $fResultNumDespac = $fConsult -> RetMatrix( "a" );
        $fNumDespac = $fResultNumDespac[0];   
      }
      else
      {
        throw new Exception( "No se encuentra numero de despacho del Viaje.", "1999" );
        break;
      }

      return array('cod_respon' => '1000', 'msg_respon' => $fNumDespac );  
    }
    catch(Exception $e)
    {
      return array('cod_respon' => '6001', 'msg_respon' => $e->getMessage() );
    }
  }
 
  
  /**************************************************************************
  * Funcion Copia la Ruta en Sat Trafico.                                   *
  * @fn setRutaFaro.                                                        *
  * @brief Inserta una novedad NC en una aplicacion SAT.                    *
  * @param $fCodTranps: string Nit transportadora.                          *
  * @param $fCodRutasx: string Codigo Ruta.                                 *
  * @param $fNomRutasx: string Nombre Ruta.                                 *
  * @param $fCodPaiori: string Codigo Pais Origen.                          *
  * @param $fCodDepori: string Codigo Departamento Origen.                  *
  * @param $fCodCiuori: string Codigo Ciudad Origen.                        *
  * @param $fCodPaides: string Codigo Pais Destino.                         *
  * @param $fCodDepdes: string Codigo Departamento Destino.                 *
  * @param $fCodCiudes: string Codigo Ciudad Destino.                       *
  * @param $fArrPcontr[cod_contro]: string Codigo del puesto de control.    *
  * @param $fArrPcontr[nom_contro]: string Nombre del puesto de control.    *
  * @param $fArrPcontr[cod_ciudad]: string Codigo de la ciudad del puesto.  *
  * @param $fArrPcontr[dir_contro]: string Direccion del puesto de control. *
  * @param $fArrPcontr[ind_virtua]: string Indicador virtual.               *
  * @param $fArrPcontr[ind_estado]: string Indicador Estado.                *
  * @param $fArrPcontr[ind_urbano]: string Indicador Urbano.                *
  * @param $fArrPcontr[val_duraci]: string Duracion en minutos.             *
  * @param $fArrPcontr[val_distan]: string Distancia en metros.             *
  * @return string mensaje de respuesta o array de rutas y puestos.         *
  ***************************************************************************/
  function setRutaFaro( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, 
                        $fCodRutasx = NULL, $fNomRutasx = NULL, $fCodPaiori = NULL,
                        $fCodDepori = NULL, $fCodCiuori = NULL, $fCodPaides = NULL,
                        $fCodDepdes = NULL, $fCodCiudes = NULL, $fObjPcontr = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setRutaFaro" );
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, 
                      "cod_rutasx" => $fCodRutasx, "nom_rutasx" => $fNomRutasx, "cod_paiori" => $fCodPaiori,
                      "cod_depori" => $fCodDepori, "cod_ciuori" => $fCodCiuori, "cod_paides" => $fCodPaides,
                      "cod_depdes" => $fCodDepdes, "cod_ciudes" => $fCodCiudes );
    
    $fValidator = new Validator( $fInputs, "setrutafaro_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    $fReturn = FALSE;
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );
  
          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
  
          //Se convierte de objeto a arreglo
          $i = 0;
          $fArrPcontr = array();
          
          foreach( $fObjPcontr as $fPc )
          {
            $fArrPcontr[$i]['cod_contro'] = $fPc -> cod_contro;
            $fArrPcontr[$i]['nom_contro'] = $fPc -> nom_contro;
            $fArrPcontr[$i]['cod_ciudad'] = $fPc -> cod_ciudad;
            $fArrPcontr[$i]['dir_contro'] = $fPc -> dir_contro;
            $fArrPcontr[$i]['ind_virtua'] = $fPc -> ind_virtua;
            $fArrPcontr[$i]['ind_estado'] = $fPc -> ind_estado;
            $fArrPcontr[$i]['ind_urbano'] = $fPc -> ind_urbano;
            $fArrPcontr[$i]['val_duraci'] = $fPc -> val_duraci;
            $fArrPcontr[$i]['val_distan'] = $fPc -> val_distan;

            $i++;
          }
          
          $InterfTrafico = new InterfTrafico( $fConsult, $fExcept );
          $InterfTrafico -> setNomUsuar( $fNomUsuari );
          $fCodRutfar = $InterfTrafico -> getRutaHomolo( $fCodTranps, $fCodRutasx, $fNomRutasx, 
                                                         $fCodPaiori, $fCodDepori, $fCodCiuori, 
                                                         $fCodPaides, $fCodDepdes, $fCodCiudes, 
                                                         $fArrPcontr );
          
          $fHomoloData = $InterfTrafico -> getHomoloData( $fCodTranps, $fCodRutfar );
          
          if( NULL !== $fHomoloData )
          {
            $fReturn['arr_homolo'] = $fHomoloData;
            $fReturn['cod_rutbas'] = $fHomoloData[0]['cod_rutbas'];
            $fReturn['cod_rutfar'] = $fHomoloData[0]['cod_rutfar'];
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se copio la ruta satisfactoriamente';
          }
          else
            throw new Exception( "Fallo el copiado de rutas. No hay datos en la homologacion.", "1999" );
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }
      return $fReturn;
    }
    else
    {
      $fMessage = NULL;
      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }

     /**************************************************************************
  * Funcion Actualiza el telefono celular en la informacion de los despachos *
  * @fn setTelConduc                                                         *
  * @brief Se actualizan despachos en ruta asignando el nuevo celular.       *
  * @param $fNomUsuari    : string Usuario.                                  *
  * @param $fPwdClavex    : string Clave.                                    *
  * @param $fCodTranps    : int    Nit Transportadora.                       *
  * @param $fCodConduc    : int    Cedula conductor.                         *
  * @param $fTelConduc    : string Telefono celular.                         *
  * @return string mensaje de respuesta.                                     *
  ****************************************************************************/
  function setTelConduc( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, 
                         $fCodConduc = NULL, $fTelConduc = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" =>  $fCodTranps, 
                      "cod_conduc" => $fCodConduc, "tel_conduc" => $fTelConduc );

    $fValidator = new Validator( $fInputs, "settelconduc_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setTelConduc" );
        $fReturn = NULL;

        include_once( AplKon );
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          //Se consulta el numero de telefono actual
          $fQueryTelChanged = "SELECT a.cod_manifi, b.num_placax, a.con_telmov, a.num_despac
                                 FROM ".BASE_DATOS.".tab_despac_despac a,
                                      ".BASE_DATOS.".tab_despac_vehige b
                           WHERE a.num_despac = b.num_despac
                             AND b.cod_transp = '".$fCodTranps."'
                             AND a.fec_llegad IS NULL
                             AND a.ind_anulad = 'R'
                             AND b.ind_activo = 'S'
                             AND a.fec_salida IS NOT NULL
                             AND a.ind_planru = 'S'
                             AND b.cod_conduc = '".$fCodConduc."'
                             AND a.con_telmov <> '".$fTelConduc."'";

          $fConsult -> ExecuteCons( $fQueryTelChanged );
          $fManifis = $fConsult -> RetMatrix( "a" );
          if( 0 != $fConsult -> RetNumRows() )
          {
            //Si el numero que esta guardado es diferente al que estan enviando se hace el update
            $fQueryUpd = "UPDATE ".BASE_DATOS.".tab_despac_despac a,
                                 ".BASE_DATOS.".tab_despac_vehige b
                             SET a.con_telmov = '".$fTelConduc."',
                                 a.fec_modifi = NOW()
                           WHERE a.num_despac = b.num_despac
                             AND b.cod_transp = '".$fCodTranps."'
                             AND a.fec_llegad IS NULL
                             AND a.ind_anulad = 'R'
                             AND b.ind_activo = 'S'
                             AND a.fec_salida IS NOT NULL
                             AND a.ind_planru = 'S'
                             AND b.cod_conduc = '".$fCodConduc."'
                             AND a.con_telmov <> '".$fTelConduc."'";
            
            if( $fConsult -> ExecuteCons( $fQueryUpd, "BRC" ) )
              $fReturn = "code_resp:1000; msg_resp:Se actualizo el celular con exito";
            else
              $fReturn = "code_resp:1999; msg_resp:No se pudo actualizar el celular del conductor en SAT Trafico";
            
            
            
            for( $i = 0, $total = count( $fManifis ); $i < $total; $i++ )
            {
              //Se ingresa la novedad cambio de celular
              $query = "SELECT a.cod_contro, b.nom_contro
                          FROM ".BASE_DATOS.".tab_despac_seguim a,
                               ".BASE_DATOS.".tab_genera_contro b
                         WHERE a.cod_contro = b.cod_contro
                           AND a.ind_estado = '1'
                           AND a.num_despac = '".$fManifis[$i]['num_despac']."'
                      ORDER BY a.fec_planea ASC
                         LIMIT 1";
          
              $fCodContro = $fConsult -> ExecuteCons( $query );
              $fCodContro = $fConsult -> RetMatrix( 'a' );
          
              $resultNoveda = setNovedadNC( $fNomUsuari, $fPwdClavex, $fCodTranps, $fManifis[$i]['cod_manifi'],
                                     $fManifis[$i]['num_placax'], CamCel, $fCodContro[0]['cod_contro'], 0, 
                                     date("Y-m-d H:i"), 
                                     "Se cambio el celular del conductor en la plataforma del cliente. Antes: ".$fManifis[$i]['con_telmov'].". Despues: ".$fTelConduc,
                                     NULL, NULL, $fCodContro[0]['nom_contro'] );
                                     
              //Procesa el resultado del WS
              $mResult  = explode( "; ", $resultNoveda );
              $mCodResp = explode( ":", $mResult[0] );
              $mMsgResp = explode( ":", $mResult[1] );
        
              if( "1000" != $mCodResp[1] )
              {
                //Notifica Errores retornados por el WS
                throw new Exception( $mMsgResp[1], "1999" );
              }
            }
          }
          else
          {
            $fReturn = "code_resp:1000; msg_resp:No es necesario cambiar el celular";
          }


        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );

      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
  
    
  /*****************************************************************************
  * Funcion Copia la informacion de la homologacion en trafico.                *
  * @fn setHomoloData.                                                         *
  * @brief Inserta las equivalencias entre puestos y rutas de trafico y basico *
  * @param $fNomUsuari: string Usuario.                                        *
  * @param $fPwdClavex: string Clave.                                          *
  * @param $fCodTranps: string Nit transportadora.                             *
  * @param $fCodRutfar: string Codigo Ruta Faro.                               *
  * @param $fCodRutbas: string codigo Ruta Basico.                             *
  * @param $fObjHomolo[cod_pcxfar]: int Codigo Puesto Trafico.                 *
  * @param $fObjHomolo[cod_pcxbas]: int Codigo Puesto Basico.                  *
  * @return string mensaje de respuesta o array de rutas y puestos.            *
  *****************************************************************************/
  function setHomoloData( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, 
                          $fCodRutfar = NULL, $fCodRutbas = NULL, $fObjHomolo = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setHomoloData" );
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps,
                      "cod_rutfar" => $fCodRutfar, "cod_rutbas" => $fCodRutbas );
    
    $fValidator = new Validator( $fInputs, "sethomolodata_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    $fReturn = FALSE;
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );
  
          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
  
          //Se convierte de objeto a arreglo
          $i = 0;
          $fArrPcontr = array();
          
          foreach( $fObjHomolo as $fRow )
          {
            $fArrHomolo[$i]['cod_pcxfar'] = $fRow -> cod_pcxfar;
            $fArrHomolo[$i]['cod_pcxbas'] = $fRow -> cod_pcxbas;
            $fArrHomolo[$i]['ind_estado'] = $fRow -> ind_estado;

            $i++;
          }
          
          if( count( $fArrHomolo ) == 0 )
            throw new Exception( "Se debe enviar minimo un puesto de control para insertar la homologacion.", "6001" );
            
          $fConsult -> StartTrans();
          
          $query = "DELETE FROM ".BASE_DATOS.".tab_homolo_trafico
                     WHERE cod_rutbas = '".$fCodRutbas."'
                       AND cod_rutfar = '".$fCodRutfar."'
                       AND cod_transp = '".$fCodTranps."'";
          
          if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
            throw new Exception( "Error en Delete.", "3001" );
      
          $query = "INSERT INTO ".BASE_DATOS.".tab_homolo_trafico
                    (
                      cod_transp, cod_rutfar, cod_rutbas, 
                      cod_pcxfar, cod_pcxbas, ind_estado, 
                      usr_creaci, fec_creaci
                    )VALUES";

          for( $i = 0, $total = count( $fArrHomolo ); $i < $total; $i++ )
          {
             $queryConsult = "SELECT 1 
                               FROM ".BASE_DATOS.".tab_genera_rutcon
                              WHERE cod_rutasx = '".$fCodRutfar."'
                                AND cod_contro = '".$fArrHomolo[$i]['cod_pcxfar']."'";
                         
            $fConsult -> ExecuteCons( $queryConsult );

            if( 0 != $fConsult -> RetNumRows() )
            {
              $query .= "( '".$fCodTranps."', '".$fCodRutfar."', '".$fCodRutbas."',
                         '".$fArrHomolo[$i]['cod_pcxfar']."', '".$fArrHomolo[$i]['cod_pcxbas']."', '".$fArrHomolo[$i]['ind_estado']."',
                         '".$fNomUsuari."', NOW()
                       ),";
             }
           }
           //Se elimina la ultima coma
           $query = substr( $query, 0, strlen( $query ) - 1 );
      
           if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
             throw new Exception( "Error en Insert.", "3001" );
          
           $fConsult -> Commit();
            
           $fReturn = "code_resp:1000; msg_resp:Se Insertaron los datos de homologacion satisfactoriamente.";
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }
      return $fReturn;
    }
    else
    {
      $fMessage = NULL;
      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
    function getHomoloData( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTranps = NULL, 
                            $fCodRutasx = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "getHomoloData" );
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" => $fCodTranps, 
                      "cod_rutasx" => $fCodRutasx );
    
    $fValidator = new Validator( $fInputs, "gethomolodata_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    $fReturn = FALSE;
    if( "1000" === $fMessages["code"] )
    {
      try
      {
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );
  
          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );
  
          $InterfTrafico = new InterfTrafico( $fConsult, $fExcept );
          $InterfTrafico -> setNomUsuar( $fNomUsuari );
          $fHomoloData = $InterfTrafico -> getHomoloData2( $fCodTranps, $fCodRutasx );
          
          if( NULL !== $fHomoloData )
          {
            $fReturn['arr_homolo'] = $fHomoloData;
            $fReturn['cod_rutbas'] = $fHomoloData[0]['cod_rutbas'];
            $fReturn['cod_rutfar'] = $fHomoloData[0]['cod_rutfar'];
            $fReturn['cod_respon'] = '1000';
            $fReturn['msg_respon'] = 'Se retorno la informacion de '.count($fHomoloData).' puestos satisfactoriamente';
          }
          else
            throw new Exception( "No hay datos en la homologacion.", "1999" );
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos web10.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = 'Se ha presentado un error el cual ya fue notificado';
        }
        else
        {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fNomAplica, $fNumDespac );
        }
      }
      return $fReturn;
    }
    else
    {
      $fMessage = NULL;
      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        $fReturn['cod_respon'] = '6001';
        $fReturn['msg_respon'] = $fMessage;
        return $fReturn;
      }
      else
      {
        $fReturn['cod_respon'] = $fMessages["code"];
        $fReturn['msg_respon'] = $fMessages["message"];
        return $fReturn;
      }
    }
  }
  
  /**********************************************************************
  * Funcion Dar salida al despacho                                       *
  * @fn setSalida                                                        *
  * @brief Da salida a un despacho ya ingresado anteriormente.           *
  * @param $fNomUsuari: string Nombre de usuario.                        *
  * @param $fPwdClavex: string Clave de usuario.                         *
  ************************************************************************/
  function setSalida( $fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fCodManifi = NULL, $fNumPlacax  = NULL, $fFecSalida = NULL, $fObsSalida = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fNomUsuari );
    $fExcept -> SetParams( "Faro", "setSalida" );
    
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_transp" => $fCodTransp, 
                      "cod_manifi" => $fCodManifi,  "num_placax" => $fNumPlacax, "fec_salida" => $fFecSalida,
                      "obs_salida" => $fObsSalida);

    $fValidator = new Validator( $fInputs, "salida_valida.txt" );
    $fMessages = $fValidator -> GetMessages();
    


    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      { 
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }

        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
        
        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {

        $queryPlacaEnRuta = "SELECT a.num_despac 
                      FROM ".BASE_DATOS.".tab_despac_vehige a,
                           ".BASE_DATOS.".tab_despac_despac b 
                     WHERE a.num_placax = '".$fNumPlacax."'
                       AND b.cod_manifi = '".$fCodManifi."'
                       AND a.cod_transp = '".$fCodTransp."' 
                       AND a.num_despac = b.num_despac 
                       AND a.ind_activo = 'S' 
                       AND b.ind_anulad = 'R' 
                       AND b.fec_salida IS NOT NULL 
                       AND b.fec_llegad IS NULL";
                       
          $fConsult -> ExecuteCons( $queryPlacaEnRuta );
          
          if( $fConsult -> RetNumRows() >= 1 )
          {
            //La placa ya se encuentra en ruta
            $fReturn = "code_resp:1999; msg_resp:El Vechiculo Placas ".$fNumPlacax." se Encuentra en Ruta Actualmente, Reporte Primero su Llegada.";
          }
          else
          { 
            $mQueryDespacSalida = "SELECT a.num_despac ".
                                     "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                          "".BASE_DATOS.".tab_despac_vehige b ".
                                    "WHERE a.num_despac = b.num_despac ".
                                      "AND a.cod_manifi = '".$fCodManifi."' ".
                                      "AND b.cod_transp = '".$fCodTransp."' ".
                                      "AND b.num_placax = '".$fNumPlacax."' ".
                                      "AND a.fec_salida IS NULL ".
                                      "AND a.ind_anulad = 'R' ".
                                      "AND a.fec_llegad IS NULL ";
                                 
            $fConsult -> ExecuteCons( $mQueryDespacSalida );
            
            if( 0 != $fConsult -> RetNumRows() )
            {
              $fNumDespac = $fConsult -> RetMatrix( "a" );
              $fConsult -> StartTrans();
                
              $fQueryUpdSalida = "UPDATE ".BASE_DATOS.".tab_despac_despac ".
                                    "SET fec_salida = '".$fFecSalida."', ".
                                        "fec_salsis = NOW(), ".
                                        "fec_ultnov = '".$fFecSalida."', ".
                                        "obs_salida = '".$fObsSalida."', ".
                                        "usr_modifi = '".$fNomUsuari."', ".
                                        "ind_planru = 'S', ".
                                        "fec_modifi = NOW() ".
                                  "WHERE num_despac = '".$fNumDespac[0]["num_despac"]."' ";
              
              if( $fConsult -> ExecuteCons( $fQueryUpdSalida, "R" ) === FALSE )
                throw new Exception( "Error en Update tab_despac_despac.", "3001" );
              
              $fQueryUpdDesVeh = "UPDATE ".BASE_DATOS.".tab_despac_vehige ".
                                      "SET ind_activo = 'S', ".
                                          "usr_modifi = '".$fNomUsuari."', ".
                                          "fec_modifi = NOW() ".
                                    "WHERE num_despac = '".$fNumDespac[0]["num_despac"]."' ";
                                
              if( $fConsult -> ExecuteCons( $fQueryUpdDesVeh, "R" ) === FALSE )
                throw new Exception( "Error en Update tab_despac_vehige.", "3001" );
                
              $fConsult -> Commit();
                  
              $fReturn = "code_resp:1000; msg_resp:Se dio salida con exito";
            }
            else
            {
              $fReturn = "code_resp:1000; msg_resp:No se encontro un despacho para el manifiesto ".$fCodManifi." con placa ".$fNumPlacax;
            }
              
              
         
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }
 
  function RegistrarCall( $fUsrCallxx = NULL, $fClvCallxx = NULL, $fTokCallxx = NULL, $fNumDespac = NULL, $fNumPlacax = NULL, $fNumTelefo = NULL,
                          $fTieDuraci = NULL, $fIdxLlamad = NULL, $fNomEstado = NULL, $fRutAudiox = NULL )
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fUsrCallxx );
    $fExcept -> SetParams( "Faro", "RegistrarCall" );
 
    $mDatenow = date("Y-m-d H:i:s");
    $mDatenow = date("Y-m-d H:i:s");

    $fMessages["code"] = '1000';

    if( "1000" === $fMessages["code"] )
    {
      try
      { 
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          #break;
        }

        if($fTokCallxx != '*K4czOUZxtt{Y|ND5c=q'){
          #Token No coincide
          return array("cod_respon" => "2001", "msg_respon" => "EL Token no coincide para el despacho: ".$fNumDespac.". Fecha: ".$mDatenow." ", "dat_respon" => NULL );
        }

        if($fNumDespac == '0' || $fNumPlacax == '0')
        {
          return array("cod_respon" => "1000", "msg_respon" => "No se registra porque no hay despacho ni placa", "dat_respon" => " OK " );
        }
        # Genera conexion a BD ---------------------------------------------------------------------------------------------------------
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        # array datos del registro de la llamada -----------------------------------------------------------------------------------------------
        $mData = array( "num_despac" => $fNumDespac,"cod_consec" => $fCodConsec ,"cod_transp" => $fCodTransp,"num_placax" => $fNumPlacax ,
                        "num_telefo" => $fNumTelefo,"tie_duraci" => $fTieDuraci ,"idx_llamad" => $fIdxLlamad,"nom_estado" => $fNomEstado ,
                        "rut_audiox" => $fRutAudiox,"usr_creaci" => $fUsrCallxx ,"fec_creaci" => $mDatenow  
                      ) ;
        
      
        if( !getAutentica( $fUsrCallxx, $fClvCallxx, $fExcept ) ) {
          $mData = array_merge( $mData,  array("sql_queryx" => "No Query", "sql_errorx" => "Clave y/o usuario incorrectos" ) );
          $mLog = LogCallcenter($mData, $fConsult);
          throw new Exception("Clave y/o usuario incorrectos.\n Data:\n ".var_export( $mData,true)."\n Query: ".$mLog, 1002); 
        }       

          $fQuery = "SELECT a.cod_transp 
                       FROM ".BASE_DATOS.".tab_despac_vehige a 
                      WHERE a.num_despac = '$fNumDespac'
                      ";
          $fConsult -> ExecuteCons( $fQuery );
          $fResult = $fConsult -> RetMatrix('a');
          $fCodTransp = $fResult[0]['cod_transp'];

          if(!$fCodTransp){
            #No se encontro Transportadora 
            $mData = array_merge( $mData,  array("sql_queryx" => "No Query", "sql_errorx" => "No se encontro transportadora para el despacho: ".$fNumDespac.". \nDatos:".var_export( $mData,true)." \n Fecha: ".$mDatenow  ) );
            LogCallcenter($mData, $fConsult);        
            throw new Exception("No se encontro transportadora para el despacho: ".$fNumDespac.". \nDatos:".var_export( $mData,true)." \n Fecha: ".$mDatenow , 2002);     
          }



          $fQuery = "SELECT cod_consec 
                       FROM ".BASE_DATOS.".tab_despac_callnov 
                      WHERE num_despac = '$fNumDespac' 
                        AND cod_transp = '$fCodTransp' 
                   ORDER BY cod_consec DESC 
                      LIMIT 0, 1
                    ";
          $fConsult -> ExecuteCons( $fQuery ); 
          $fResult = $fConsult -> RetMatrix('a');
          $fCodConsec = $fResult[0]['cod_consec'] == NULL ? '1' : $fResult[0]['cod_consec'] + 1 ;

          $fQuery = "INSERT INTO ".BASE_DATOS.".tab_despac_callnov 
                     (num_despac, cod_consec, cod_transp, 
                      num_placax, num_telefo, tie_duraci, 
                      idx_llamad, nom_estado, rut_audiox, 
                      usr_creaci, fec_creaci) 
                     VALUES 
                     ('$fNumDespac', '$fCodConsec', '$fCodTransp', 
                      '$fNumPlacax', '$fNumTelefo', '$fTieDuraci', 
                      '$fIdxLlamad', '$fNomEstado', '$fRutAudiox', 
                      '$fUsrCallxx', now() )
                    ";

          $mInsert = $fConsult -> ExecuteCons( $fQuery );
          if( !$mInsert ) {
            $mData = array_merge( $mData,  array("sql_queryx" => $fQuery, "sql_errorx" => mysql_error() ) );
            LogCallcenter($mData, $fConsult);
            throw new Exception($fQuery."\n ".var_export( $mInsert, true )." Fecha: ".$mDatenow." Mysql: ".mysql_error() , "2004");            
          }
          else
            $mFinal = array("cod_respon" => "1000", "msg_respon" => "Se registro de manera exitosa", "dat_respon" => " OK " );

          return $mFinal;
          
          
      
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );

          //mail("maribel.garcia@eltransporte.org, nelson.liberato@intrared.net", "Error Registro Llamada Faro -  Despacho: ".$fNumDespac, $e -> getMessage());
          $fReturn = array("cod_respon" => "2004", "msg_respon" => $e -> getMessage(), "dat_respon" => NULL);
            
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }


 

  function RegistrarCallIn( $fUsrCallxx = NULL, $fClvCallxx = NULL, $fTokCallxx = NULL, $fNumTelefo = NULL,
                            $fTieDuraci = NULL, $fIdxLlamad = NULL, $fNomEstado = NULL, $fRutAudiox = NULL,
                            $fCodExtenc = NULL, $fIdxServic = NULL)
  {
    $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
    $fExcept -> SetUser( $fUsrCallxx );
    $fExcept -> SetParams( "Faro", "RegistrarCallIn" );
 
    $mDatenow = date("Y-m-d H:i:s");

    $fMessages["code"] = '1000';

    if( "1000" === $fMessages["code"] )
    {
      try
      { 
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          #break;
        }

        if($fTokCallxx != '*K4czOUZxtt{Y|ND5c=q'){
          #Token No coincide
          return array("cod_respon" => "2001", "msg_respon" => "EL Token no coincide para el despacho: ".$fNumDespac.". Fecha: ".$mDatenow." ", "dat_respon" => NULL );
        }

        # Genera conexion a BD ---------------------------------------------------------------------------------------------------------
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        # array datos del registro de la llamada -----------------------------------------------------------------------------------------------
        $mData = array( "cod_consec" => $fCodConsec,"num_telefo" => $fNumTelefo ,"tie_duraci" => $fTieDuraci,
                        "idx_llamad" => $fIdxLlamad,"nom_estado" => $fNomEstado ,"rut_audiox" => $fRutAudiox,
                        "cod_extenc" => $fCodExtenc,"idx_servic" => $fIdxServic ,"usr_creaci" => $fUsrCallxx,"fec_creaci" => $mDatenow  
                      ) ;
        
      
        if( !getAutentica( $fUsrCallxx, $fClvCallxx, $fExcept ) ) {
          $mData = array_merge( $mData,  array("sql_queryx" => "No Query", "sql_errorx" => "Clave y/o usuario incorrectos", "ind_llamad" =>"2" ) );
          $mLog = LogCallcenter($mData, $fConsult);
          throw new Exception("Clave y/o usuario incorrectos.\n Data:\n ".var_export( $mData,true)."\n Query: ".$mLog, 1002); 
        }       

         


          $fQuery = "SELECT cod_consec 
                       FROM ".BASE_DATOS.".tab_despac_callin 
                      WHERE num_telefo = '{$fNumTelefo}'                         
                   ORDER BY cod_consec DESC 
                      LIMIT 0, 1
                    ";
          $fConsult -> ExecuteCons( $fQuery ); 
          $fResult = $fConsult -> RetMatrix('a');
          $fCodConsec = $fResult[0]['cod_consec'] == NULL ? '1' : $fResult[0]['cod_consec'] + 1 ;

          $fQuery = "INSERT INTO ".BASE_DATOS.".tab_despac_callin 
                     (cod_consec, num_telefo, tie_duraci, 
                      idx_llamad, nom_estado, rut_audiox, 
                      cod_extenc, idx_servic, 
                      usr_creaci, fec_creaci) 
                     VALUES 
                     ('{$fCodConsec}', '{$fNumTelefo}', '{$fTieDuraci}', 
                      '{$fIdxLlamad}', '{$fNomEstado}', '{$fRutAudiox}', 
                      '{$fCodExtenc}', '{$fIdxServic}', 
                      '{$fUsrCallxx}', NOW() )
                    ";

          $mInsert = $fConsult -> ExecuteCons( $fQuery );
          if( !$mInsert ) {
            $mData = array_merge( $mData,  array("sql_queryx" => $fQuery, "sql_errorx" => mysql_error(), "ind_llamad" =>"2" ) );
            LogCallcenter($mData, $fConsult);
            throw new Exception($fQuery."\n ".var_export( $mInsert, true )." Fecha: ".$mDatenow." Mysql: ".mysql_error() , "2004");            
          }
          else
            $mFinal = array("cod_respon" => "1000", "msg_respon" => "Se registro de manera exitosa", "dat_respon" => " OK " );

          return $mFinal;
          
          
      
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else
        {
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );

          //mail("maribel.garcia@eltransporte.org, nelson.liberato@intrared.net", "Error Registro Llamada Faro -  Despacho: ".$fNumDespac, $e -> getMessage());
          $fReturn = array("cod_respon" => "2004", "msg_respon" => $e -> getMessage(), "dat_respon" => NULL);
            
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }
        return "code_resp:6001; msg_resp:".$fMessage;
      }
      else
        return "code_resp:".$fMessages["code"]."; msg_resp:".$fMessages["message"];
    }
  }


  function LogCallcenter($fDataCallcenter = NULL, $fConsult = NULL, $fActionLog = 'Add')
  {
       
        $mLog = 'INSERT INTO '.BASE_DATOS.'.tab_error_calnov 
                 (num_despac, cod_consec, cod_transp, 
                  num_placax, num_telefo, tie_duraci, 
                  idx_llamad, nom_estado, rut_audiox, 
                  fec_creaci, sql_queryx, sql_errorx) 
                 VALUES 
                 (
                  "'.$fDataCallcenter["num_despac"].'", "'.$fDataCallcenter["cod_consec"].'", "'.$fDataCallcenter["cod_transp"].'", 
                  "'.$fDataCallcenter["num_placax"].'", "'.$fDataCallcenter["num_telefo"].'", "'.$fDataCallcenter["tie_duraci"].'", 
                  "'.$fDataCallcenter["idx_llamad"].'", "'.$fDataCallcenter["nom_estado"].'", "'.$fDataCallcenter["rut_audiox"].'", 
                  "'.$fDataCallcenter["fec_creaci"].'", "'.$fDataCallcenter["sql_queryx"].'", "'.$fDataCallcenter["sql_errorx"].'"
                  )';
                  
                  $mFile = fopen("/var/www/html/ap/interf/app/faro/logs/call_".date("Ymd").".txt", "a+");
                  fwrite( $mFile, $mLog."\n");
                  fwrite( $mFile, var_export($fDataCallcenter, true)."\n");
                  fwrite( $mFile, "----------------------");
                  fclose($mFile);




      $fConsult -> ExecuteCons( $mLog );
      
  }

    /*! \fn: getDataDestin
    *  \brief: Trae la Data para los destinatarios
    *  \author: 
    *  \date: dia/mes/ano
    *  \date modified: dia/mes/ano
    *  \param: mNumDespac  Integer  Numero del despacho
    *  \param: mNomDestin  String   Nombre del destinatario
    *  \par am: mIndGroupx  Boolean  Indicador de agrupacion
    *  \return: Matriz
    */
    function getDestin( $mUser = NULL, $mPass = NULL, $mNumDespac = NULL, $fNomAplica = NULL)
    {

      try {
        //mail("andres.torres@eltransporte.org", "getDestin1", var_export(array("fNomAplica"=>$fNomAplica), true) );
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $mUser );
        $fExcept -> SetParams( "Faro", "getDestin" );

        if( $fNomAplica != NULL && $fNomAplica != 'satt_faro') // SI NO ES FARO , CARGA EL CLIENTE SATT_TQALA, EJEMPLO
        {
          if(!include_once('/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc') )
          {
            throw new Exception( "Aplicacion GL ".$fNomAplica."  en sat trafico no encontrada", "1999" );
          }
        }
        else if(file_exists( AplKon ) ){
          include_once( AplKon );
        }
        else
        {
          throw new Exception( "Aplicacion FARO en sat trafico no encontrada", "1999" );
          break;
        }

        if( !getAutentica( $mUser, $mPass, $fExcept ) )
        {
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }

        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        $mSql = "  SELECT a.nom_destin, c.abr_ciudad, a.dir_destin,
                          a.num_destin, CONCAT(a.fec_citdes,' ',a.hor_citdes) AS fec_citdes2,
                          GROUP_CONCAT(a.num_docume) AS num_docume, a.num_docalt,
                          a.cod_genera, UPPER(b.abr_tercer) AS abr_tercer,
                          IF(
                                NOW() < DATE_ADD(CONCAT(a.fec_citdes,' ',a.hor_citdes),INTERVAL 15 MINUTE) AND
                                NOW() > DATE_ADD(CONCAT(a.fec_citdes,' ',a.hor_citdes),INTERVAL -15 MINUTE),
                                1,0
                            ) AS ind_descar,
                          IF( a.fec_llecli IS NULL OR a.fec_llecli = '0000-00-00 00:00:00' , 1, 0 ) AS ind_entrad,
                          IF( a.fec_inides IS NULL OR a.fec_inides = '0000-00-00 00:00:00' , 1, 0) AS ind_inides,
                          IF( a.fec_findes IS NULL OR a.fec_findes = '0000-00-00 00:00:00' , 1, 0) AS ind_findes
                     FROM ".BASE_DATOS.".tab_despac_destin a
                LEFT JOIN ".BASE_DATOS.".tab_tercer_tercer b
                       ON a.cod_genera = b.cod_tercer
                LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad c
                       ON a.cod_ciudad = c.cod_ciudad
                    WHERE a.num_despac = '{$mNumDespac}'
                  AND a.num_docume not in(SELECT g.num_docume FROM ".BASE_DATOS.".tab_cumpli_despac g WHERE g.num_despac = a.num_despac )
                GROUP BY a.num_destin /* a.num_docume */
                 ORDER BY fec_citdes2, a.nom_destin, a.num_docume ";
         
        $fConsult -> ExecuteCons( $mSql );
        $fReturn = array( "dat_respon" => $fConsult -> RetMatrix('a'));
        //mail("andres.torres@eltransporte.org", "getDestin1", var_export(array("mSql"=>$mSql), true) );

        if (sizeof($fReturn) < 1 ) {
          throw new Exception( "No se encuentran los clientes del despacho", "1999" );
        }



      } catch (Exception $e) {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fReturn['dat_respon'] = array();


      }
          return $fReturn;
    }
    
    
    /*! \fn: setFecAditio
    *  \brief: Trae la Data para los destinatarios
    *  \author: 
    *  \date: dia/mes/no
    *  \date modified: dia/mes/no
    *  \param: mNumDespac  Integer  Numero del despacho
    *  \param: mNomDestin  String   Nombre del destinatario
    *  \par am: mIndGroupx  Boolean  Indicador de agrupacion
    *  \return: Matriz
    */   
    function setFecAditio( $mUser = NULL, $mPass = NULL, $mNumDespac = NULL, $mTipFecha = NULL, $mNumFecha = NULL, $mNumFactur = NULL, $mCodRemdes = NULL, $fNomAplica = NULL)
    {
      try {

        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $mUser );
        $fExcept -> SetParams( "Faro", "setFecAditio" );

        /*if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }*/

        if( $fNomAplica != NULL && $fNomAplica != 'satt_faro') // SI NO ES FARO , CARGA EL CLIENTE SATT_TQALA, EJEMPLO
        {
          if(!include_once('/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc') )
          {
            throw new Exception( "Aplicacion GL ".$fNomAplica."  en sat trafico no encontrada", "1999" );
          }
        }
        else if(file_exists( AplKon ) ){
          include_once( AplKon );
        }
        else
        {
          throw new Exception( "Aplicacion FARO en sat trafico no encontrada", "1999" );
          break;
        }

        if( !getAutentica( $mUser, $mPass, $fExcept ) )
        {

          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }

        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
        $filter = "";

       //$mTipFecha = 'fec_cumdes';
        // mail("andres.torres@eltransporte.org", "fechas", var_export($mTipFecha, true));
        //$mTipFecha = 'fec_cumdes'; 
        // Si son eventos de inicio cargue y fin cargue
        if( in_array($mTipFecha,  array('fec_inicar','fec_fincar') ) )
        {
          // fec_inicar, fec_fincar
            if($mTipFecha == 'fec_inicar')
            {
              $mCodnoVeda = '404';
            }

            if($mTipFecha == 'fec_fincar')
            {
              $mCodnoVeda = '405';
            }

            if ($fNomAplica != 'satt_faro') {
              $mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_despac
                         SET   ".$mTipFecha." = '".$mNumFecha."',
                               ".( $mTipFecha == 'fec_inicar' ? " fec_ingcar = '".$mNumFecha."' , nov_ingcar = '".$mCodnoVeda."' , usr_ingcar = '".$mUser."' , obs_ingcar = 'REPORTA ENTRADA A PLANTA ".date("d/m/Y")." A LAS ".date("H:m:s")."' ," : " "  )."
                               ".( $mTipFecha == "fec_fincar" ? " fec_salcar = '".$mNumFecha."' , nov_salcar = '".$mCodnoVeda."' , usr_salcar = '".$mUser."' , obs_salcar = 'REPORTA SALIDA DE PLANTA ".date("d/m/Y")." A LAS ".date("H:m:s")."' ," : " "  )."
                               usr_modifi = '".$mUser."'
                         WHERE num_despac = '".$mNumDespac."'";
            }else{
              
              $mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_despac
                           SET   ".$mTipFecha." = '".$mNumFecha."',
                                 ".( $mTipFecha == 'fec_inicar' ? " fec_inicar = '".$mNumFecha."' ," : " "  )."
                                 ".( $mTipFecha == "fec_fincar" ? " fec_fincar = '".$mNumFecha."' ," : " "  )."
                                 usr_modifi = '".$mUser."'
                           WHERE num_despac = '".$mNumDespac."'";
              // mail("andres.torres@eltransporte.org", "Prueba despacho ".$mNumDespac." Actualización fechas", var_export($mUpdate, true));

            }

            //mail("luis.manrique@eltransporte.org", "Prueba despacho ".$mNumDespac." Actualización fechas", $mUpdate);
        }
        // Si son eventos de inicio descargue y fin descargue
        elseif( in_array($mTipFecha,  array('fec_inides','fec_findes') ) )
        {
            // fec_inides, fec_findes
            if($mTipFecha == 'fec_inides')
            {
              $mCodnoVeda = '355';
            }

            if($mTipFecha == 'fec_findes')
            {
              $mCodnoVeda = '356';
            }

            if ($fNomAplica != 'satt_faro') {
              $mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_destin
                           SET   ".$mTipFecha." = '".$mNumFecha."',     
                                 ".($mTipFecha == 'fec_inides' ?  " fec_ingdes = '".$mNumFecha."', nov_ingdes = '".$mCodnoVeda."', usr_ingdes = '".$mUser."', obs_ingdes = 'REPORTA ENTRADA A CLIENTE ".date("d/m/Y")." A LAS ".date("H:m:s")."' ," : " " )."
                                 ".($mTipFecha == 'fec_findes' ?  " fec_saldes = '".$mNumFecha."', nov_saldes = '".$mCodnoVeda."', usr_saldes = '".$mUser."', obs_saldes = 'REPORTA SALIDA DE CLIENTE ".date("d/m/Y")." A LAS ".date("H:m:s")."' ," : " " )."
                                 usr_modifi = '".$mUser."'
                           WHERE num_despac = '".$mNumDespac."'
                           AND num_docume IN ( ".$mNumFactur." )";
            }else{
              $mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_destin
                           SET   ".$mTipFecha." = '".$mNumFecha."',     
                                 ".($mTipFecha == 'fec_inides' ?  " fec_inides = '".$mNumFecha."' ," : " " )."
                                 ".($mTipFecha == 'fec_findes' ?  " fec_findes = '".$mNumFecha."' ," : " " )."
                                 usr_modifi = '".$mUser."'
                           WHERE num_despac = '".$mNumDespac."'
                           AND num_docume IN ( ".$mNumFactur." )";
            }
            
        }
        else
        {
            $mNumFactur = $mNumFactur == '' || $mNumFactur == NULL ? '"0"' : $mNumFactur;
            $mIndCumpli = "SELECT IF(NOW() < ( SELECT DATE_FORMAT( CONCAT( fec_citdes,' ', hor_citdes ) , '%Y-%m-%d %H:%i:%s' )
                           FROM  ".BASE_DATOS.".tab_despac_destin
                           WHERE num_despac = '".$mNumDespac."' AND ( num_docume IN ( ".$mNumFactur." ) OR cod_remdes = '".$mCodRemdes."' ) LIMIT 1 ),'S','N') AS ind_cumpli";
            $fConsult -> ExecuteCons( $mIndCumpli, "R" );
            $mIndCumpli = $fConsult -> RetMatrix('a');
            $mIndCumpli = $mIndCumpli[0]["ind_cumpli"];

            // mari crea la 9053 para reemplazar 208
            $mCodnoVeda = $mIndCumpli == 'S' ? '256' : '9053';

            // Parche si llega cod_remdes, que es llegada a descargue desde integrador GPS, reutilizo funcion para no hacer de nuevo la logica
            // if( $mCodRemdes != '' ||آ $mCodRemdes != NULL )
            // {
            //   $mCodnoVeda = '255';
            // } 

            $mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_destin
                           SET
                                ind_cumdes = '".($mIndCumpli == 'S' ? '1' : '0')."',
                                nov_cumdes = '".$mCodnoVeda."',
                                fec_cumdes = NOW(),
                                obs_cumdes = 'Reporta llegada cliente ".($mCodRemdes != NULL || $mCodRemdes != '' ?  "Integrador GPS" : "" )."',
                                fec_modifi = NOW(),
                                fec_llecli = NOW(),
                                usr_cumdes = '".$mUser."',
                                not_mobile = '1'
                        WHERE 1 = 1 AND num_despac = '".$mNumDespac."' AND ( num_docume IN ( ".$mNumFactur." ) OR cod_remdes = '".$mCodRemdes."' ) ";
        }
 
        // $mFilex = fopen('/var/www/html/ap/interf/app/externo/logs/setFecAditio.log', "a+");
        // fwrite($mFilex, "------------------------------------".date("Y-m-d H:i:s")."-------------------------------------------------------------\n");
        // fwrite($mFilex, "setFecAditio Inputs: \n".var_export(  ['mUser' => $mUser , 'mPass' => $mPass , 'mNumDespac' => $mNumDespac , 'mTipFecha' => $mTipFecha , 'mNumFecha' => $mNumFecha , 'mNumFactur' => $mNumFactur , 'mCodRemdes' => $mCodRemdes ] , true)."\n");
        // fwrite($mFilex, "setFecAditio Query: \n".$mUpdate."\n");
        // fclose($mFilex);

        if( $fConsult -> ExecuteCons( $mUpdate, "R" ) != FALSE )
        {
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
          $fReturn['dat_respon'] = '';

          // Envio de punto visitado al integrador, es cuando desde la APP dan salida de cargue/descargue unicamente
          if( in_array($mTipFecha,  array('fec_fincar','fec_findes') ) )
          {
            $mDataDespac = "SELECT a.num_despac, a.cod_manifi, b.cod_transp, b.num_placax, b.cod_integr, c.nom_sitcar
                             FROM ".BASE_DATOS.".tab_despac_despac a 
                       INNER JOIN ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac
                       INNER JOIN ".BASE_DATOS.".tab_despac_corona c ON a.num_despac = c.num_dessat
                           WHERE a.num_despac = '".$mNumDespac."'  ";
            $fConsult -> ExecuteCons( $mDataDespac, "R" );
            $mDataDespac = $fConsult -> RetMatrix('a');
            $mDataDespac = $mDataDespac[0];

            if( $mDataDespac['cod_integr']  != '' )
            {

              // en el cargue no tenemos el cod_remdes pero en el descargue si
              $mCodRemdes = $mCodRemdes == '' || $mCodRemdes == NULL ? $mDataDespac['nom_sitcar'] : $mCodRemdes;
              // Envio al metodo para que lo envie a la central
              $mVisited = setPuntoCumplidoIntegrador( [ 
                                                        0 => [  
                                                                'CustomCode' => $mCodRemdes, 
                                                                'ItineraryID' => $mDataDespac['cod_integr'] 
                                                             ] 
                                                      ], 
                                                      [ 
                                                        'cod_transp' => $mDataDespac['cod_transp'],  
                                                        'num_placax' => $mDataDespac['num_placax'],  
                                                        'cod_manifi' => $mDataDespac['cod_manifi'],  
                                                        'num_despac' => $mDataDespac['num_despac'],
                                                        'cod_integr' => $mDataDespac['cod_integr'] 
                                                      ]   
                                                    );
              // respuesta de central/integrador
              $fReturn['msg_respon'] .= $mVisited['msg_resp'];
            }
          }

          $fReturn['msg_respon'] .= ".".$mCodnoVeda;
          $fReturn['dat_respon'] = $mCodnoVeda;

        }

        if (sizeof($fReturn) < 1 ) {
          throw new Exception( "Ha ocurrido un error en la transaccion".$mUpdate, "1999" );
        }


      } catch (Exception $e) {

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(), $e -> getLine(), $mTipFecha, $fNumDespac );

          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fReturn['dat_respon'] = array();


      }
          return $fReturn;
    }
    
     /*! \fn: setPuntoCumplidoIntegrador
    *  \brief: funcion para enviar el cumplido de punto a integrador GPS
    *  \author: Ing. Nelson Liberato
    *  \date: 2019-10-04
    *  \param:   $mDatPoint array con el codigo cod_remdes como CustomCode e cod_integr como ItineraryID 
    *  \return:  array
    */
    function setPuntoCumplidoIntegrador( $mDatPoint = NULL, $mDataDespac  = NULL)
    {
      try 
      {
          $sendData = array(
              "cod_tokenx" => "0zV;Q;%5=zL6TG",
              "nit_transp" => $mDataDespac['cod_transp'],
              "ifx_server" => 'CORONA', 
              "nom_aplica" => BASE_DATOS, 
              "usr_gpsxxx" => "InterfGpsIntegr", 
              "pwd_gpsxxx" => "0zV;Q;%5=zL6TG", 
              "num_placax" => $mDataDespac["num_placax"],    
              "num_manifi" => $mDataDespac['cod_manifi'], 
              "num_despac" => $mDataDespac['num_despac'], 
              "ItineraryID" => $mDataDespac['cod_integr'], 
              "lstPointVisited" => $mDatPoint
            );

          $cHeader = array("Token: TkOET_EAL","Auth: $2dIMJMZQcHLY",
                           "Authorization: e14804819d57fc7497bb747204ce337b", 
                           "usuario: *WidetechInt3grador*", 
                           "clave: lxdG-+gJX:oYju+b5n"
                          );
          
          # Recorre las variables para concatenarlas en un solo string como si fuera un GET para enviarla por cUrl------------------------
          //$mParamsString = "inputAvansat=".json_encode($sendData);
          $mParamsString = json_encode($sendData);
  
           
          # Inicio de cURL para la API -----------------------------------------------------------------------------
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, URL_INTERF_GPSEVE."/SetVisitedPoint" ); /*"https://dev.intrared.net:8083/ap/interf/app/APIEventosGPS/"*/
          curl_setopt($ch, CURLOPT_HTTPHEADER, $cHeader);
          curl_setopt($ch, CURLOPT_VERBOSE, 1);
          curl_setopt($ch, CURLOPT_POST, true );
          curl_setopt($ch, CURLOPT_POSTFIELDS, $mParamsString);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $raw_data = curl_exec($ch);
          $error = curl_error($ch);
          $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);

           
          //   echo "<pre>mParamsString: "; print_r(URL_INTERF_GPSEVE."/setItinerary  " ); echo "</pre>"; 
          //   echo "<pre>mParamsString: "; print_r( $raw_data ); echo "</pre>"; 
          # Asigna el mensaje del response y lo retorna ------------------------------------------------------------       
          return json_decode($raw_data, true);
      } 
      catch (Exception $e) 
      {
        
      }
    }



    /*! \fn: getNovedadEvent
    *  \brief: Consulta la novedad con respecto a un codigo de evento y nit de transportadora
    *  \author: Nelson Liberato
    *  \date: 06/04/2017
    *  \param: fNomUsuari  String  Usuario
    *  \param: fPwdClavex  String  Clave
    *  \param: fNomAplica  String  Nombre aplicacion (Aplica solo para cuando se consume ws de avansat:e,c,b)
    *  \param: fCodEvento  String  codigo del evento reportador por la operadora GPS
    *  \param: fCodTransp  String  nit de la trasnportadora
    *  \return: Matriz
    */  
    function getNovedadEvent( $fNomUsuari = NULL, $fPwdClavex = NULL, $fNomAplica = NULL, $fCodEvento = NULL, $fCodTransp = NULL, $fCodNoveda = NULL )
    {

      try {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getNovedadEvent" );

        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }

        /*if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {          
          throw new Exception( "Clave y/o usuario incorrectos WS.".$fNomUsuari."...".$fPwdClavex."....".BASE_DATOS, "1002" );
          break;
        }*/

        if(   $fNomUsuari != "IntergGPSEvento" && $fPwdClavex != "0zV;Q;%5=zL6TG") {          
          throw new Exception( "Clave y/o usuario incorrectos WS.", "1002" );
          break;
        }
        
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
 
        $mSelect = " SELECT b.cod_noveda, c.nom_noveda, e.cod_etapax, e.nom_etapax, c.ind_tiempo, c.ind_manala, d.ind_apsees, d.ind_tisees, d.num_minuto, b.cod_opegps, b.cod_evento, b.nom_evento, d.fec_modifi  
                       FROM 
                            ".BASE_DATOS.".tab_aplica_filtro_perfil a, 
                            ".BASE_DATOS.".tab_perfil_noveda b, 
                            ".BASE_DATOS.".tab_genera_noveda c,
                            ".BASE_DATOS.".tab_genera_novpar d,
                            ".BASE_DATOS.".tab_genera_etapax e
                      WHERE  
                            a.cod_perfil = b.cod_perfil AND
                            b.cod_noveda = c.cod_noveda AND
                            b.cod_perfil = d.cod_perfil AND
                            b.cod_noveda = d.cod_noveda AND 
                            c.cod_etapax = e.cod_etapax AND
                            a.clv_filtro = '".$fCodTransp."'  ";
        if( $fCodEvento != NULL OR  $fCodEvento != '') 
        {
            $mSelect .= " AND b.cod_evento = '".$fCodEvento."' ";
        }                    
       
        $fConsult -> ExecuteCons( $mSelect, "R" ) ;
        $mData = $fConsult -> RetMatrix('a');
        if( sizeof($mData) > 0 )
        { 
          
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
          $fReturn['dat_noveda'] = $mData;
        } else {
          throw new Exception( "NO existe homologacion de novedad para el evento: ".$fCodEvento, "1999" );
        }

         

      } catch (Exception $e) {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fReturn['dat_noveda'] = array();
      }
          return $fReturn;
    }


    /*! \fn: getTraficoManifiesto
    *  \brief: Consulta el trafico segun nit empresa, manifiesto y placa
    *  \author: Nelson Liberato
    *  \date: 21/12/2017
    *  \param: fNomUsuari  String  Usuario
    *  \param: fPwdClavex  String  Clave
    *  \param: fCodTransp  String  nit de la trasnportadora
    *  \param: fCodManifi  String  Numero del manifiesto
    *  \param: fNumPlacax  String  Placa del VH
    *  \return: Matriz
    */  
    function getTraficoManifiesto($fNomUsuari = NULL, $fPwdClavex = NULL, $fCodTransp = NULL, $fCodManifi = NULL, $fNumPlacax = NULL)
    {
       try {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getTraficoManifiesto" );

        if( file_exists( AplKon ) )
          include_once( AplKon );
        else
        {
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }

        if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {          
          throw new Exception( "Clave y/o usuario incorrectos", "1002" );
          break;
        }

        // valida los datos de entrada
        $fDatValida = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, 
                             "cod_transp" => $fCodTransp, "num_manifi" => $fCodManifi, 
                             "num_placax" => $fNumPlacax  );

        $fValidator = new Validator( $fDatValida, "novedad_valida.txt" );
        $fMessages = $fValidator -> GetMessages();
        $fDatValidax = $fDatValida;
        unset( $fDatValida, $fValidator );
 

        if( "1000" !== $fMessages["code"] )
        {
            $fMessage = NULL;
            if( "6001" === $fMessages["code"] )
            {
              foreach( $fMessages["message"] as $fRow )
              {
                $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
              }
              throw new Exception( $fMessage , "6001" );
            }
            else {
                throw new Exception( $fMessages["message"] , $fMessages["code"] );
            }      
        }


        
        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
        // consula el numero de despacho segun nit, manifiesto y placa
        $mNumDespac = "SELECT a.num_despac 
                         FROM ".BASE_DATOS.".tab_despac_despac a INNER JOIN 
                              ".BASE_DATOS.".tab_despac_vehige b ON a.num_despac = b.num_despac AND
                                                                    a.cod_manifi = '{$fCodManifi}' AND
                                                                    b.num_placax = '{$fNumPlacax}' AND
                                                                    b.cod_transp = '{$fCodTransp}' ";

                                                       
        $fConsult -> ExecuteCons( $mNumDespac, "R" ) ;
        $mData = $fConsult -> RetMatrix('a');
        if(sizeof($mData) <= 0)
        {
          throw new Exception( "No se encontro el despacho", "1002" );
          break;
        }

        $fNumDespac = $mData[0]["num_despac"];

        $mSelect = "(
                      SELECT  
                          b.fec_creaci,
                          d.nom_sitiox AS nom_contro,
                          b.obs_contro,
                          e.nom_noveda, 
                          b.cod_contro,
                          b.usr_creaci

                          FROM  ".BASE_DATOS.".tab_despac_vehige a 
                     INNER JOIN ".BASE_DATOS.".tab_despac_contro b ON a.num_despac = '{$fNumDespac}' AND a.num_despac = b.num_despac AND b.usr_creaci NOT LIKE '%Interf%'   
                     INNER JOIN ".BASE_DATOS.".tab_genera_noveda e ON b.cod_noveda = e.cod_noveda 
                     INNER JOIN ".BASE_DATOS.".tab_genera_contro c ON b.cod_contro = c.cod_contro 
                     INNER JOIN ".BASE_DATOS.".tab_despac_sitio  d ON b.cod_sitiox = d.cod_sitiox 
                    )
                    UNION ALL
                    (
                      SELECT  
                          b.fec_creaci,
                          c.nom_contro,
                          b.des_noveda AS obs_contro, 
                          e.nom_noveda,
                          b.cod_contro,
                          b.usr_creaci
                        FROM  ".BASE_DATOS.".tab_despac_vehige a 
                  INNER JOIN  ".BASE_DATOS.".tab_despac_noveda b ON a.num_despac = '{$fNumDespac}' AND a.num_despac = b.num_despac AND b.usr_creaci NOT LIKE '%Interf%' 
                  INNER JOIN  ".BASE_DATOS.".tab_genera_noveda e ON b.cod_noveda = e.cod_noveda 
                  INNER JOIN  ".BASE_DATOS.".tab_genera_contro c ON b.cod_contro = c.cod_contro          
                    )
                    ORDER BY fec_creaci  ASC    ";                 
       
        $fConsult -> ExecuteCons( $mSelect, "R" ) ;
        $mData = $fConsult -> RetMatrix('a');
        if( sizeof($mData) > 0 )
        { 
          
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
          $fReturn['dat_trafic'] = $mData;
        } else {
          throw new Exception( "NO hay trazabilidad para el despacho: ", "1999" );
        }

         

      } catch (Exception $e) {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fReturn['dat_trafic'] = array();
      }
          return $fReturn;
    }


  function setStatusItinerary($fNomUsuari = NULL ,$fPwdClavex = NULL ,$fNomAplica = NULL ,$fCodTranps = NULL ,$fCodManifi = NULL ,$fNumPlacax = NULL ,
                              $fCodItiner = NULL ,$fMsgItiner = NULL ,$fFecItiner = NULL )
  {
    $fInputs = array( "nom_usuari" => $fNomUsuari, "pwd_clavex" => $fPwdClavex, "cod_tranps" =>  $fCodTranps, "cod_manifi" => $fCodManifi, 
                      "num_placax" => $fNumPlacax, "fec_itiner" => $fFecItiner );

    $fValidator = new Validator( $fInputs, "status_itinerary_valida.txt" );
    $fMessages = $fValidator -> GetMessages();

    unset( $fInputs, $fValidator );

    if( "1000" === $fMessages["code"] )
    {
      try
      {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "setStatusItinerary" );
        $fReturn = NULL;

        if($fNomAplica == 'satt_faro')
        {
          include_once( AplKon );
        }
        elseif ($fNomAplica == 'satt_dingps') {
          include_once('/var/www/html/ap/integradorgps/'.$fNomAplica.'/constantes.inc');
        }
        else{
          include_once('/var/www/html/ap/generadores/'.$fNomAplica.'/constantes.inc');
        }

        $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );

        if( getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
        {
          $fTercer = new Tercer( $fExcept );

          if( !$fTercer -> tercerExists( $fCodTranps ) )
            throw new Exception( "La Transportadora no esta registrada.", "6001" );

          $fQuerySelNumDes = "SELECT a.num_despac ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ".
                                "AND b.num_placax = '".$fNumPlacax."' ".
                                "AND a.ind_anulad != 'A' ".
                                "AND a.fec_llegad IS NULL";
 
          $fConsult -> ExecuteCons( $fQuerySelNumDes );
          
          
          if( 0 != $fConsult -> RetNumRows() )
          {
            //Si el manifiesto recibido esta en ruta para esa transportadora se hace el update
            $fNumDespac = $fConsult -> RetMatrix( "a" );
            $fConsult -> StartTrans();
            
            $fCodItiner = ($fCodItiner == '' || $fCodItiner == NULL ? 'NULL' : $fCodItiner );
            $fFecItiner = ($fFecItiner == '' || $fFecItiner == NULL ? 'NULL' : "'".$fFecItiner."'" );
          
            $query = "UPDATE ".BASE_DATOS.".tab_despac_vehige
                         SET  cod_itiner =  ".$fCodItiner." ,
                              msg_itiner = '".$fMsgItiner."',
                              fec_itiner =  ".$fFecItiner."
                        WHERE num_despac = '".$fNumDespac[0]["num_despac"]."'"; 
            //mail("nelson.liberato@eltransporte.org", "setStatusItinerary", $query );
            if( $fConsult -> ExecuteCons( $query, "R" ) === FALSE )
              throw new Exception( "Error en Insert.", "3001" );
            
            $fConsult -> Commit(); 
            $fReturn = ["cod_respon" => '1000', "msg_respon" => "Se actualiza el despacho con estado Itinerario de forma correcta"];
          }
          else
          {
            $fQuerySelFinali = "SELECT 1 ".
                               "FROM ".BASE_DATOS.".tab_despac_despac a, ".
                                    "".BASE_DATOS.".tab_despac_vehige b ".
                              "WHERE a.num_despac = b.num_despac ".
                                "AND a.cod_manifi = '".$fCodManifi."' ".
                                "AND b.cod_transp = '".$fCodTranps."' ".
                                "AND b.num_placax = '".$fNumPlacax."' ".
                                "AND a.ind_anulad = 'R' ".
                                "AND a.fec_llegad IS NOT NULL ".
                                " #AND a.fec_salida IS NOT NULL ";

            $fConsult -> ExecuteCons( $fQuerySelFinali );
            
            if( 0 != $fConsult -> RetNumRows() ) 
              $fReturn = ["cod_respon" => '1999', "msg_respon" => "El despacho con manifiesto ".$fCodManifi." se encuentra finalizado en Avansat GL"];
            else 
              $fReturn = ["cod_respon" => '1999', "msg_respon" => "No se encontro un despacho para el manifiesto ".$fCodManifi." trp: ".$fCodTranps." Plc: ".$fNumPlacax." en Avansat GL"];
          }
        }
        else
          throw new Exception( "Clave y/o usuario incorrectos".$fPwdClavex, "1002" );
      }
      catch( Exception $e )
      {
        if( "3001" == $e -> getCode() )
          $fReturn = ["cod_respon" => $e -> getCode(), "msg_respon" => "Se ha presentado un error el cual ya fue notificado."];
        else
        {
          $fReturn = ["cod_respon" => $e -> getCode(), "msg_respon" =>  $e -> getMessage() ];

          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                  $e -> getLine(), $fCodTranps, $fCodManifi );
        }
      }

      return $fReturn;
    }
    else
    {
      $fMessage = NULL;

      if( "6001" === $fMessages["code"] )
      {
        foreach( $fMessages["message"] as $fRow )
        {
          $fMessage .= "Campo - ".$fRow["Col"].", Detalle - ".$fRow["Message"]."| ";
        }

        return ["cod_respon" => "6001", "msg_respon" => $fMessage];
      }
      else
        return ["cod_respon" => $fMessages["code"], "msg_respon" => $fMessages["message"] ];
    }
  }

  

  /*! \fn: getDatosSolicitudes
    *  \brief: Consulta la informacion y datos para la elaboracion de graficos de todas la solicitudes de asistencia
    *  \author: Cristian Torres
    *  \date: 31/08/2020
    *  \param: fNomUsuari  String  Usuario
    *  \param: fPwdClavex  String  Clave
    *  \param: fCodTransp  String  nit de la trasnportadora
    *  \param: fCodManifi  String  Numero del manifiesto
    *  \return: Matriz
    */  
    function getDatosSolicitudes($fNomUsuari,$fPwdClavex,$fCodClient){

      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "getDatosSolicitudes" );
      
      try{
          if( file_exists( AplKon ) )
            include_once( AplKon );
          else
          {
            throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
            break;
          }
          $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
          if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
          {
            throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
            break;
          }
          
          //Informacion total de los registros de las solicitudes de asistencia
          $mSelect = "SELECT a.id,b.nom_asiste,c.nom_tercer,
                            a.nom_solici,a.fec_creaci,a.usu_creaci,
                            a.est_solici,a.fec_cancel
                      FROM ".BASE_DATOS.".tab_asiste_carret a
                      INNER JOIN ".BASE_DATOS.".tab_formul_asiste b
                      ON a.tip_solici = b.id
                      INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c
                      ON a.cod_client = c.cod_tercer
                      WHERE a.cod_client = '$fCodClient'
                      ;";
  
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $mData = cleanArray($fConsult -> RetMatrix('a'));
  
          //Informacion total de los registros de las para la vista MIS SOLICITUDES
          $mSelect = "SELECT 
                        b.id, 
                        b.tip_solici,
                        a.nom_asiste, 
                        c.nom_tercer, 
                        b.nom_solici, 
                        b.fec_creaci, 
                        b.usu_creaci, 
                      CASE WHEN b.est_solici = 1 THEN 'Por Gestionar'
                           WHEN b.est_solici = 2 THEN 'Por Aprobar Cliente'
                           WHEN b.est_solici = 3 THEN 'Por Asignacion a Proveedor'
                           WHEN b.est_solici = 4 THEN 'En proceso'
                           WHEN b.est_solici = 5 THEN 'Finalizada'
                           WHEN b.est_solici = 6 THEN 'Cancelada'
                           ELSE '-' END AS est_solici 
                      FROM 
                        ".BASE_DATOS.".tab_formul_asiste a 
                      INNER JOIN ".BASE_DATOS.".tab_asiste_carret b ON a.id = b.tip_solici AND
                      b.cod_client = '$fCodClient'
                      INNER JOIN ".BASE_DATOS.".tab_tercer_tercer c ON b.cod_client = c.cod_tercer
                      ";
  
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $mDataMisSoli = cleanArray($fConsult -> RetMatrix('a'));
  
          //Consulta Datos para Construir Graficos
  
          //Tipo de Solicitud
          $mSelect = "SELECT 
                        COUNT(*) as 'cantidad',
                        b.nom_asiste 
                      FROM 
                        ".BASE_DATOS.".tab_asiste_carret a 
                      INNER JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id 
                      WHERE a.est_solici NOT IN (5,6)
                      AND a.cod_client = '$fCodClient'
                      GROUP BY a.tip_solici";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $fDataGraph = cleanArray($fConsult -> RetMatrix('a'));
  
          //Consulta Responsable
          $mSelect = "SELECT 
                        COUNT(*) as 'cantidad','Cliente' as 'nom'
                      FROM ".BASE_DATOS.".tab_asiste_carret a 
                        INNER JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id 
                        WHERE a.est_solici = 2 AND a.cod_client = '$fCodClient'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $fDataGraph21 = cleanArray($fConsult -> RetMatrix('a'));
  
          $mSelect = "SELECT 
                        COUNT(*) as 'cantidad', 
                        'Asistencia en Carretera' as 'nom' 
                      FROM ".BASE_DATOS.".tab_asiste_carret a 
                      INNER JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id 
                      WHERE a.est_solici IN (1, 3, 4) AND a.cod_client = '$fCodClient'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $fDataGraph22 = cleanArray($fConsult -> RetMatrix('a'));
  
          $fDataGraph2= array_merge($fDataGraph21,$fDataGraph22);
  
          //Consultas estado de Solicitudes
          $mSelect = "SELECT 
                        COUNT(*) as 'cantidad','Gestion Cliente' as 'nom'
                      FROM ".BASE_DATOS.".tab_asiste_carret a 
                        INNER JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id 
                        WHERE a.est_solici = 2 AND a.cod_client = '$fCodClient'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $fDataGraph31 = cleanArray($fConsult -> RetMatrix('a'));
  
          $mSelect = "SELECT 
                        COUNT(*) as 'cantidad', 
                        'En Proceso' as 'nom' 
                      FROM ".BASE_DATOS.".tab_asiste_carret a 
                      INNER JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id 
                      WHERE a.est_solici IN (1, 3, 4) AND a.cod_client = '$fCodClient'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $fDataGraph32 = cleanArray($fConsult -> RetMatrix('a'));
  
          $mSelect = "SELECT 
                        COUNT(*) as 'cantidad', 
                        'Finalizadas' as 'nom' 
                      FROM ".BASE_DATOS.".tab_asiste_carret a 
                      INNER JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id 
                      WHERE a.est_solici = 5 AND a.cod_client = '$fCodClient'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $fDataGraph33 = cleanArray($fConsult -> RetMatrix('a'));
  
          $mSelect = "SELECT 
                        COUNT(*) as 'cantidad', 
                        'Canceladas' as 'nom' 
                      FROM ".BASE_DATOS.".tab_asiste_carret a 
                      INNER JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id 
                      WHERE a.est_solici = 5 AND a.cod_client = '$fCodClient'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $fDataGraph34 = cleanArray($fConsult -> RetMatrix('a'));
  
          $fDataGraph3= array_merge($fDataGraph31,$fDataGraph32,$fDataGraph33,$fDataGraph34);
  
  
          //Valida si el cliente esta registrado en gl
  
          $mSelect = "SELECT 
                        COUNT(*) as 'cantidad' 
                      FROM ".BASE_DATOS.".tab_tercer_tercer a 
                      WHERE a.cod_tercer = '$fCodClient';";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $can_consul = cleanArray($fConsult -> RetMatrix('a'));
          $can_consul = $can_consul[0]['cantidad'];
          $cli_regist = false;
          if($can_consul>0){
            $mSelect = "SELECT hab_asicar FROM ".BASE_DATOS.".tab_transp_tipser WHERE cod_transp = '$fCodClient' ORDER BY num_consec DESC LIMIT 1";
            $fConsult -> ExecuteCons( $mSelect, "R" ) ;
            $res_consul = cleanArray($fConsult -> RetMatrix('a'));
            $res_consul = $res_consul[0]['hab_asicar'];
  
            if(($res_consul!=NULL)OR($res_consul!="")OR($res_consul!=0)){
              $cli_regist = true;
            }
  
            if($res_consul==0){
              $cli_regist = false;
            }
          }
  
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
          $fReturn['dat_noveda'] = $mData;
          $fReturn['dat_missol'] = $mDataMisSoli;
          $fReturn['dat_graph1'] = $fDataGraph;
          $fReturn['dat_graph2'] = $fDataGraph2;
          $fReturn['dat_graph3'] = $fDataGraph3;
          $fReturn['cli_regist'] = $cli_regist;
          $fReturn['can_consul'] = $can_consul;
        }
        catch( Exception $e )
        {
          if( "3001" == $e -> getCode() )
            $fReturn = [
              "cod_respon"=>$e -> getCode(),
              "msg_respon" => "Se ha presentado un error el cual ya fue notificado."
            ];
          else
          {
            $fReturn = [
              "cod_respon"=>$e -> getCode(),
              "msg_respon" =>$e -> getMessage()
            ];
            //$fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
  
            $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                    $e -> getLine(), $fNomAplica, $fNumDespac );
          }
        }
        return json_encode($fReturn);
    }
  
     /*! \fn: getTipoSolicitud
      *  \brief: Consulta los diferentes tipos de solicitudes
      *  \author: Cristian Torres
      *  \date: 31/08/2020
      *  \param: fNomUsuari  String  Usuario
      *  \param: fPwdClavex  String  Clave
      *  \return: Matriz
      */  
    function getTipoSolicitud($fNomUsuari,$fPwdClavex){
      
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "getTipoSolicitud" );
  
      try{
          if( file_exists( AplKon ) )
            include_once( AplKon );
          else
          {
            throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
            break;
          }
          $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
          if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
          {
            throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
            break;
          }
          $mSelect = "SELECT a.id,a.nom_asiste
                      FROM ".BASE_DATOS.".tab_formul_asiste a
                      WHERE a.ind_estado = 1";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $mData = cleanArray($fConsult -> RetMatrix('a'));
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
          $fReturn['dat_noveda'] = $mData;
        }
        catch( Exception $e )
        {
          if( "3001" == $e -> getCode() )
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
          else
          {
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
  
            $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                    $e -> getLine(), $fNomAplica, $fNumDespac );
          }
        }
        return json_encode($fReturn);
    }
  
    /*! \fn: getServices
      *  \brief: Consulta los diferentes tipos de solicitudes
      *  \author: Cristian Torres
      *  \date: 31/08/2020
      *  \param: fNomUsuari  String  Usuario
      *  \param: fPwdClavex  String  Clave
      *  \return: Matriz
      */ 
    function getServices($fNomUsuari,$fPwdClavex,$fTipAsiste){
      
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "getServices" );
  
      try{
          if( file_exists( AplKon ) )
            include_once( AplKon );
          else
          {
            throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
            break;
          }
          $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
          if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
          {
            throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
            break;
          }
  
          $mSelect = "SELECT a.id,a.abr_servic
                      FROM ".BASE_DATOS.".tab_servic_asicar a
                      WHERE a.ind_estado = 1 AND a.tip_asicar='$fTipAsiste'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $mData = cleanArray($fConsult -> RetMatrix('a'));
          
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
          $fReturn['dat_servic'] = $mData;
        }
        catch( Exception $e )
        {
          if( "3001" == $e -> getCode() )
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
          else
          {
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
  
            $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                    $e -> getLine(), $fNomAplica, $fNumDespac );
          }
        }
        return json_encode($fReturn);
    }
  
    /*! \fn: getCiudades
      *  \brief: Consulta las ciudades por el nombre
      *  \author: Cristian Torres
      *  \date: 31/08/2020
      *  \param: fNomUsuari  String  Usuario
      *  \param: fPwdClavex  String  Clave
      *  \param: fnomCiudad  String  Nombre de la ciudad
      *  \return: Matriz
      */ 
    function getCiudades($fNomUsuari,$fPwdClavex,$fnomCiudad){
      
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "getCiudades" );
  
      try{
          if( file_exists( AplKon ) )
            include_once( AplKon );
          else
          {
            throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
            break;
          }
          $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
          if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
          {
            throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
            break;
          }
  
          $mSelect = "SELECT a.cod_ciudad,a.nom_ciudad 
                      FROM ".BASE_DATOS.".tab_genera_ciudad a 
                      WHERE a.ind_estado = 1 AND a.nom_ciudad LIKE '%$fnomCiudad%' 
                      ORDER BY a.nom_ciudad LIMIT 3";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $mData = cleanArray($fConsult -> RetMatrix('a'));
          
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
          $fReturn['dat_ciudad'] = $mData;
        }
        catch( Exception $e )
        {
          if( "3001" == $e -> getCode() )
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
          else
          {
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
  
            $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                    $e -> getLine(), $fNomAplica, $fNumDespac );
          }
        }
        return json_encode($fReturn);
    }
  
    /*! \fn: getCosTra
      *  \brief: Consulta el costo del trayecto entre dos ciudades
      *  \author: Cristian Torres
      *  \date: 31/08/2020
      *  \param: fNomUsuari  String  Usuario
      *  \param: fPwdClavex  String  Clave
      *  \param: fcodCiuOri  String  Codigo de la ciudad Origen
      *  \param: fcodCiuDes  String  Codigo de la ciudad Destino
      *  \return: Matriz
      */ 
    function getCosTra($fNomUsuari,$fPwdClavex,$fcodCiuOri,$fcodCiuDes){
      
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "getCosTra" );
  
      try{
          if( file_exists( AplKon ) )
            include_once( AplKon );
          else
          {
            throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
            break;
          }
          $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
          if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
          {
            throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
            break;
          }
  
          $mSelect = "SELECT a.val_tarifa
                      FROM ".BASE_DATOS.".tab_tarifa_acompa a
                      WHERE a.ciu_origen = '$fcodCiuOri'
                      AND a.ciu_destin = '$fcodCiuDes'
                      AND a.ind_estado = 1";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          if($fConsult -> RetNumRows()>0){
          $mData = $fConsult -> RetMatrix('a');
          $costTra = $mData[0]['val_tarifa'];
          
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
          $fReturn['cos_trayec'] = $costTra;
          }else{
            $fReturn['cod_respon'] = "5000";
            $fReturn['msg_respon'] = "No se encontro costo del servicio de acompañamiento entre las ciudades origen y destino";
            $fReturn['cos_trayec'] = 0;
          }
        }
        catch( Exception $e )
        {
          if( "3001" == $e -> getCode() )
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
          else
          {
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
  
            $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                    $e -> getLine(), $fNomAplica, $fNumDespac );
          }
        }
        return json_encode($fReturn);
    }
  
    /*! \fn: setNuevaAsistencia
      *  \brief: Inserta una nueva asistencia a la tabla respectiva
      *  \author: Cristian Torres
      *  \date: 31/08/2020
      *  \param: fNomUsuari  String  Usuario
      *  \param: fPwdClavex  String  Clave
      *  \param: fTipAsiste  Int Codigo tipo de asistencia 1: Asistencia, 2: Acompañamiento
      *  \param: fEstSolici  Int Codigo estado de la solicitud 1: Por gestionar, 2: Por Aprobar, 3: Asignacion a Proveedor, 4:En proceso, 5:Finalizada, 6:Cancelada
      *  \param: fCodClient  Int Codigo del Cliente
      *  \param: fNomSolici  String Nombre del Solicitante
      *  \param: fCorSolici  String Correo del Solicitante
      *  \param: fTelSolici  String Telefono del solicitante
      *  \param: fCelSolici  String Celular del solicitante
      *  \param: fAseSolici  String Aseguradora
      *  \param: fNumPoliza  String Numero de Poliza
      *  \param: fNumTransp  Int Codigo del Conductor o Transportista
      *  \param: fNomTransp  String Nombre del Conductor o Transportista
      *  \param: fAp1Transp  String Primer Apellido del Conductor o Transportista
      *  \param: fAp2Transp  String Segundo Apellido del Conductor o Transportista
      *  \param: fCodClient  String Codigo del Cliente
      *  \param: fCe1Transp  String Celular del transportista
      *  \param: fCe2Transp  String Celular 2 del transportista
      *  \param: fNumPlacax  String Numero de Placa
      *  \param: fMarVehicu  String Marca del Vehiculo
      *  \param: fColVehicu  String Color del vehiculo
      *  \param: fTipVehicu  String Tipo del Vehiculo
      *  \param: fUrlOpeGps  String Url Operador Gps
      *  \param: fNomOpeGps  String Nombre operador gps
      *  \param: fNomUsuGps  String Nombre usuario gps
      *  \param: fConVehicu  String Contraseña usuario gps
      *  \param: fUbiVehicu  String Ubicacion del vehiculo
      *  \param: fPunRefere  String Punto de referencia
      *  \param: fDesAsiste  String Descripcion de la asistencia
      *  \param: fFecServic  Date Fecha del servicio
      *  \param: fCiuOrigen  Int Codigo Ciudad de Origen
      *  \param: fDirCiuOri  String Direccion ciudad de origen
      *  \param: fCiuDestin  Int Codigo Ciudad de Destino
      *  \param: fDirCiuDes  String Direccion ciudad de Destino
      *  \param: fObsAcompa  String Observacion Solicitud de Acompañamiento
      *  \param: servicios   Array codigo de los servicios solicitados en la asistencia
      *  \return: Matriz
      */ 
    function setNuevaAsistencia(  $fNomUsuari = NULL, $fPwdClavex = NULL, $fTipAsiste = NULL,
                                  $fEstSolici = NULL, $fCodClient = NULL, $fNomSolici = NULL,
                                  $fCorSolici = NULL, $fTelSolici = NULL, $fCelSolici = NULL,
                                  $fAseSolici = NULL, $fNumPoliza = NULL, $fNumTransp = NULL,
                                  $fNomTransp = NULL, $fAp1Transp = NULL, $fAp2Transp = NULL,
                                  $fCe1Transp = NULL, $fCe2Transp = NULL, $fNumPlacax = NULL,
                                  $fMarVehicu = NULL, $fColVehicu = NULL, $fTipVehicu = NULL,
                                  $fNumRemolq = NULL, $fUrlOpeGps = NULL, $fNomOpeGps = NULL,
                                  $fNomUsuGps = NULL, $fConVehicu = NULL, $fUbiVehicu = NULL,
                                  $fPunRefere = NULL, $fDesAsiste = NULL, $fFecServic = NULL,
                                  $fCiuOrigen = NULL, $fDirCiuOri = NULL, $fCiuDestin = NULL,
                                  $fDirCiuDes = NULL, $fObsAcompa = NULL, $servicios){
      
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "setNuevaAsistencia" );                       
      try{
        if( file_exists( AplKon ) )
          include_once( AplKon );
        else{
          throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
          break;
        }
       $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );                      
       if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) ){
        throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
        break;
        }
      
      //Consulta el numero de la solicitud 
      $mSelect = "SELECT IFNULL((MAX(a.id) + 1),1) as 'num_solici' FROM ".BASE_DATOS.".tab_asiste_carret a";
      $fConsult -> ExecuteCons( $mSelect, "R" ) ;
      $mData = $fConsult -> RetMatrix('a');
      $num_solici = $mData[0]['num_solici'];
      
      //Realiza la insercion a la tabla con los datos enviados desde el formulario
      $mSelect = "INSERT INTO ".BASE_DATOS.".tab_asiste_carret(
                    id,cod_client,cod_transp,
                    tip_solici, nom_solici, cor_solici, 
                    tel_solici, cel_solici, ase_solici, 
                    num_poliza, num_transp, nom_transp, 
                    ap1_transp, ap2_transp, ce1_transp, 
                    ce2_transp, num_placax, mar_vehicu, 
                    col_vehicu, tip_vehicu, num_remolq, 
                    url_opegps, nom_opegps, nom_usuari, 
                    con_vehicu, ubi_vehicu, pun_refere, 
                    des_asiste, fec_servic, ciu_origen, 
                    dir_ciuori, ciu_destin, dir_ciudes, 
                    obs_acompa, usu_creaci, fec_creaci
                    ) VALUES (
                    '".$num_solici."','".$fCodClient."','".$fCodClient."',
                    '".$fTipAsiste."', '".$fNomSolici."', '".$fCorSolici."', 
                    '".$fTelSolici."', '".$fCelSolici."', '".$fAseSolici."', 
                    '".$fNumPoliza."', '".$fNumTransp."', '".$fNomTransp."', 
                    '".$fAp1Transp."', '".$fAp2Transp."', '".$fCe1Transp."', 
                    '".$fCe2Transp."', '".$fNumPlacax."', '".$fMarVehicu."', 
                    '".$fColVehicu."', '".$fTipVehicu."', '".$fNumRemolq."', 
                    '".$fUrlOpeGps."', '".$fNomOpeGps."', '".$fNomUsuGps."', 
                    '".$fConVehicu."', '".$fUbiVehicu."', '".$fPunRefere."', 
                    '".$fDesAsiste."', '".$fFecServic."', '".$fCiuOrigen."', 
                    '".$fDirCiuOri."', '".$fCiuDestin."', '".$fDirCiuDes."', 
                    '".$fObsAcompa."', '".$fNomUsuari."',NOW()
        )";
      $fConsult -> ExecuteCons( $mSelect, "R" ) ;
      $servicios = json_decode($servicios,true);
  
      foreach($servicios as $servicio){
        $mSelect="SELECT a.abr_servic,a.tar_diurna FROM ".BASE_DATOS.".tab_servic_asicar a
            WHERE a.id = '".$servicio['servicio']."'";
        $fConsult -> ExecuteCons( $mSelect, "R" ) ;
        $mData = $fConsult -> RetMatrix('a');
        $des_servic = $mData[0]['abr_servic'];
        $cos_servic = $mData[0]['tar_diurna'];
        $mSelect = "INSERT INTO ".BASE_DATOS.".tab_servic_solasi(
                      cod_solasi,cod_servic,des_servic,
                      tip_tarifa,can_servic,val_servic,
                      usr_creaci,fec_creaci
                    ) VALUES (
                      '".$num_solici."','".$servicio['servicio']."','".$des_servic."',
                      'diurna',1,'".$cos_servic."',
                      '".$fNomUsuari."',NOW()
                    );";
        $fConsult -> ExecuteCons( $mSelect, "R" ) ;
      }
  
  
      $det_noveda = 'Sin Novedad';
      // Registra Tarifa de acompañamiento como servicio
        if($fTipAsiste == 2){
          $mSelect="SELECT a.val_tarifa
          FROM ".BASE_DATOS.".tab_tarifa_acompa a
          WHERE a.ciu_origen = '$fCiuOrigen'
          AND a.ciu_destin = '$fCiuDestin'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $dataTarif=$fConsult -> RetMatrix('a');
          $val_tarif = $dataTarif[0]['val_tarifa'];
          $cantidad_registros = $fConsult -> RetNumRows();
          if($cantidad_registros>0){
            $datos=$fConsult -> RetMatrix('a');
            $mSelect="SELECT a.nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad a WHERE a.ind_estado = 1 AND a.cod_ciudad = '$fCiuOrigen' LIMIT 1";
            $fConsult -> ExecuteCons( $mSelect, "R" ) ;
            $dataNomci=$fConsult -> RetMatrix('a');
            $nom_ciuori = $dataNomci[0]['nom_ciudad'];
  
            $mSelect="SELECT a.nom_ciudad FROM ".BASE_DATOS.".tab_genera_ciudad a WHERE a.ind_estado = 1 AND a.cod_ciudad = '$fCiuDestin' LIMIT 1";
            $fConsult -> ExecuteCons( $mSelect, "R" ) ;
            $dataNomci=$fConsult -> RetMatrix('a');
            $nom_destin = $dataNomci[0]['nom_ciudad'];
  
            $nom_servic = 'Serv. Acomp Ruta: '.$nom_ciuori.' - '.$nom_destin;
  
            //Cambia el detalle de la novedad para la bitacora
            $det_noveda.=' Ruta Sol -> '.$nom_ciuori.' - '.$nom_destin;
  
            $mSelect="INSERT INTO ".BASE_DATOS.".tab_servic_solasi(
              cod_solasi,cod_servic,des_servic,
              tip_tarifa,can_servic,val_servic,
              usr_creaci,fec_creaci
            )
            VALUES(
              '".$num_solici."',0,'".$nom_servic."',
              'unica',1,'".$val_tarif."',
              '".$fNomUsuari."',NOW()
            );";
            $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          }else{
          }
        }
  
        $mSelect="INSERT INTO ".BASE_DATOS.".tab_seguim_solasi(
          cod_solasi, ind_estado, obs_detall,
          usr_creaci, fec_creaci
        ) 
        VALUES 
          (
            '".$num_solici."', '1', '$det_noveda',
            '".$fNomUsuari."', NOW()
          )";
        $fConsult -> ExecuteCons( $mSelect, "R" ) ;
  
        $fReturn['cod_respon'] = "1000";
        $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
        $fReturn['num_solici'] = $num_solici;    
      }catch( Exception $e ){
        if( "3001" == $e -> getCode() )
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
        else{
          $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
          $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
          $e -> getLine(), $fNomAplica, $fNumDespac );
         }
       }
  
      return json_encode($fReturn);                              
  
    }
  
    function getInfoSolicitud($fNomUsuari,$fPwdClavex,$fnumSolicit,$fCodClient){
      
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "getCosTra" );
  
      try{
          if( file_exists( AplKon ) )
            include_once( AplKon );
          else
          {
            throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
            break;
          }
          $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
          if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
          {
            throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
            break;
          }
  
          $mSelect = "SELECT *, 
                        b.cod_ciudad as 'cod_ciuori', 
                        b.nom_ciudad as 'nom_ciuori', 
                        c.cod_ciudad as 'cod_ciudes', 
                        c.nom_ciudad as 'nom_ciudes',
                        d.nom_asiste as 'nom_asiste',
                        e.nom_tercer as 'nom_client'
                      FROM 
                        ".BASE_DATOS.".tab_asiste_carret a 
                      LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad b ON a.ciu_origen = b.cod_ciudad 
                      LEFT JOIN ".BASE_DATOS.".tab_genera_ciudad c ON a.ciu_destin = c.cod_ciudad
                      INNER JOIN ".BASE_DATOS.".tab_formul_asiste d ON a.tip_solici = d.id
                      INNER JOIN ".BASE_DATOS.".tab_tercer_tercer e ON a.cod_client = e.cod_tercer
                      WHERE 
                        a.id = '$fnumSolicit' AND a.cod_client = '$fCodClient' ";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          if($fConsult -> RetNumRows()>0){
          $mData = cleanArray($fConsult -> RetMatrix('a'));
          
          $mSelect = "SELECT
                        a.id,
                        a.cod_servic, 
                        a.des_servic, 
                        a.tip_tarifa, 
                        a.val_servic, 
                        a.can_servic, 
                      IF(
                        a.tip_tarifa = 'diurna', b.tar_diurna, 
                        b.tar_noctur
                      ) as 'tar_unitar' 
                      FROM 
                      ".BASE_DATOS.".tab_servic_solasi a 
                      LEFT JOIN ".BASE_DATOS.".tab_servic_asicar b ON a.cod_servic = b.id 
                      WHERE 
                        a.cod_solasi = '$fnumSolicit'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $mServici = cleanArray($fConsult -> RetMatrix('a'));
  
          $mSelect = "SELECT 
                          CASE WHEN a.ind_estado = 1 THEN 'Por Gestionar'
                           WHEN a.ind_estado = 2 THEN 'Por Aprobar Cliente'
                           WHEN a.ind_estado = 3 THEN 'Por Asignacion a Proveedor'
                           WHEN a.ind_estado = 4 THEN 'En proceso'
                           WHEN a.ind_estado = 5 THEN 'Finalizada'
                           WHEN a.ind_estado = 6 THEN 'Cancelada'
                           ELSE '-' END AS est_solici,
                          a.obs_detall, 
                          a.usr_creaci,
                          a.fec_creaci
                        FROM 
                        ".BASE_DATOS.".tab_seguim_solasi a 
                        WHERE 
                          a.cod_solasi = '$fnumSolicit' 
                        ORDER BY 
                          a.fec_creaci ASC";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $mBitacor = cleanArray($fConsult -> RetMatrix('a'));
  
  
          $mSelect = "SELECT 
                        a.des_servic, 
                        b.val_latitu, 
                        b.val_longit,
                        b.fec_ubicac,
                        b.hor_ubicac,
                        b.usr_ubicac,
                        d.nom_formul 
                      FROM 
                      ".BASE_DATOS.".tab_servic_solasi a 
                      INNER JOIN ".BASE_DATOS.".tab_solasi_ubicac b ON a.id = b.cod_servsol 
                      INNER JOIN ".BASE_DATOS.".tab_servic_asicar c ON a.cod_servic = c.id
                      INNER JOIN ".BASE_DATOS.".tab_formul_formul d ON c.cod_formul = d.cod_consec
                      WHERE 
                        a.cod_solasi = '$fnumSolicit' 
                      ORDER BY 
                        b.id ASC";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $mDataMap = cleanArray($fConsult -> RetMatrix('a'));
  
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
          $fReturn['inf_genera'] = $mData;
          $fReturn['tot_servic'] = $mServici;
          $fReturn['dat_bitaco'] = $mBitacor;
          $fReturn['dat_maprut'] = $mDataMap;
          }else{
            $fReturn['cod_respon'] = "5000";
            $fReturn['msg_respon'] = "No se encontro registro con el numero de solicitud dado";
            $fReturn['cos_trayec'] = 0;
          }
        }
        catch( Exception $e )
        {
          if( "3001" == $e -> getCode() )
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
          else
          {
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
  
            $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                    $e -> getLine(), $fNomAplica, $fNumDespac );
          }
        }
        return json_encode($fReturn);
    }
  
  
    function getDataFormulario($fNomUsuari,$fPwdClavex,$fvalBusqueda){
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "getCosTra" );
  
      try{
          if( file_exists( AplKon ) )
            include_once( AplKon );
          else
          {
            throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
            break;
          }
          $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
          if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
          {
            throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
            break;
          }
  
          $mSelect = "SELECT * FROM ".BASE_DATOS.".tab_formul_respue a
                      WHERE a.cod_servsol = '".$fvalBusqueda."';
          ";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          if($fConsult -> RetNumRows()>0){
          $mSelect = "SELECT a.cod_campox,a.tex_respue,c.nom_campox FROM ".BASE_DATOS.".tab_formul_respue a
                      INNER JOIN ".BASE_DATOS.".tab_formul_detail b ON a.cod_campox = b.cod_campox AND
                                                                       a.cod_formul = b.cod_formul
                      INNER JOIN ".BASE_DATOS.".tab_formul_campos c ON b.cod_campox = c.cod_consec
                      WHERE a.cod_servsol = '".$fvalBusqueda."'
                      ORDER BY b.num_ordenx ASC
                      ;";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $mDataFor = cleanArray($fConsult -> RetMatrix('a'));
  
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
          $fReturn['res_formul'] = $mDataFor;
          }else{
            $fReturn['cod_respon'] = "5000";
            $fReturn['msg_respon'] = "No se encontro informacion registrada";
            $fReturn['cos_trayec'] = 0;
          }
        }
        catch( Exception $e )
        {
          if( "3001" == $e -> getCode() )
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
          else
          {
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
  
            $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                    $e -> getLine(), $fNomAplica, $fNumDespac );
          }
        }
        return json_encode($fReturn);
    }
  
    function setAprobAsitencia($fNomUsuari,$fPwdClavex,$fResAproba,$fObsAproba,$fNumSolici,$fRazFinali,$fEstadonex){
      
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "getCosTra" );
  
      try{
          if( file_exists( AplKon ) )
            include_once( AplKon );
          else
          {
            throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
            break;
          }
          $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
          if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
          {
            throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
            break;
          }
  
          if($fEstadonex==6){
            $addConsulta=', fec_cancel = NOW()';
          }
          $mSelect = "UPDATE ".BASE_DATOS.".tab_asiste_carret SET 
                        apr_servic = '$fResAproba', obs_aprsol = '$fObsAproba', est_solici = '$fEstadonex', obs_cancel = '$fRazFinali' $addConsulta
                      WHERE id = '$fNumSolici'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $obs='';
          if($fEstadonex==6){
            $obs=$fRazFinali;
          }else{
            $obs=$fObsAproba;
          }
  
          $mSelect="INSERT INTO ".BASE_DATOS.".tab_seguim_solasi(
            cod_solasi, ind_estado, obs_detall,
            usr_creaci, fec_creaci
          ) 
          VALUES 
            (
              '".$fNumSolici."', '$fEstadonex', '$obs',
              '".$fNomUsuari."', NOW()
            )";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
  
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
        }
        catch( Exception $e )
        {
          if( "3001" == $e -> getCode() )
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
          else
          {
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
  
            $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                    $e -> getLine(), $fNomAplica, $fNumDespac );
          }
        }
        return json_encode($fReturn);
    }
  
    function getDataSendEmailSolici($fNomUsuari,$fPwdClavex,$fNumSolici,$fCodClient){
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "getDataSendEmail" );
  
      try{
          if( file_exists( AplKon ) )
            include_once( AplKon );
          else
          {
            throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
            break;
          }
          $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
          if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
          {
            throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
            break;
          }
          $mData='';
          if(($fNumSolici!="")||($fNumSolici!=NULL)){
          $mSelect = "SELECT 
                        b.nom_asiste, 
                        a.nom_solici, 
                        a.cor_solici, 
                        a.cod_client 
                      FROM 
                      ".BASE_DATOS.".tab_asiste_carret a 
                      INNER JOIN ".BASE_DATOS.".tab_formul_asiste b ON a.tip_solici = b.id 
                      WHERE 
                        a.id = $fNumSolici";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $mData = cleanArray($fConsult -> RetMatrix('a'));
          }
          
          $sql="";
            if(($fCodClient!="")||($fCodClient!=NULL)){
            $sql=" OR a.num_remdes = '".$fCodClient."' ";
          }
          
          $mSelect = "SELECT 
                        a.dir_emailx 
                      FROM 
                        ".BASE_DATOS.".tab_genera_parcor a 
                      WHERE 
                        a.num_remdes = '' 
                        ".$sql.";";
                        
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $mDataEmails = cleanArray($fConsult -> RetMatrix('a'));
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
          $fReturn['dat_solici'] = $mData;
          $fReturn['dat_emails'] = $mDataEmails;
        }
        catch( Exception $e )
        {
          if( "3001" == $e -> getCode() )
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
          else
          {
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
  
            $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                    $e -> getLine(), $fNomAplica, $fNumDespac );
          }
        }
        return json_encode($fReturn);
    }
  
    function setAprobaTerminosFaro($fNomUsuari,$fPwdClavex,$fDTranspor = NULL){
      
      $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
      $fExcept -> SetUser( $fNomUsuari );
      $fExcept -> SetParams( "Faro", "setAprobaTerminosFaro" );
  
      try{
          if( file_exists( AplKon ) )
            include_once( AplKon );
          else
          {
            throw new Exception( "Aplicacion sat trafico no encontrada", "1999" );
            break;
          }
          $fConsult = new Consult( array( "server"=> Hostxx, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
          if( !getAutentica( $fNomUsuari, $fPwdClavex, $fExcept ) )
          {
            throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
            break;
          }
  
          $mSelect="INSERT INTO ".BASE_DATOS.".tab_tercer_tercer
            SET
            cod_tercer = '".$fDTranspor['cod_tercer']."', num_verifi = '".$fDTranspor['num_verifi']."', cod_tipdoc = '".$fDTranspor['cod_tipdoc']."', 
            cod_terreg = '".$fDTranspor['cod_terreg']."', nom_apell1 = '".$fDTranspor['nom_apell1']."', nom_apell2 = '".$fDTranspor['nom_apell2']."', 
            nom_tercer = '".$fDTranspor['nom_tercer']."', abr_tercer = '".$fDTranspor['abr_tercer']."', dir_domici = '".$fDTranspor['dir_domici']."', 
            num_telef1 = '".$fDTranspor['num_telef1']."', num_telef2 = '".$fDTranspor['num_telef2']."', num_telmov = '".$fDTranspor['num_telmov']."', 
            num_faxxxx = '".$fDTranspor['num_faxxxx']."', cod_paisxx = '".$fDTranspor['cod_paisxx']."', cod_depart = '".$fDTranspor['cod_depart']."', 
            cod_ciudad = '".$fDTranspor['cod_ciudad']."', dir_emailx = '".$fDTranspor['dir_emailx']."', cod_contra = '".$fDTranspor['cod_contra']."', 
            dir_urlweb = '".$fDTranspor['dir_urlweb']."', cod_estado = '".$fDTranspor['cod_estado']."', dir_ultfot = '".$fDTranspor['dir_ultfot']."', 
            obs_tercer = '".$fDTranspor['obs_tercer']."', obs_aproba = '".$fDTranspor['obs_aproba']."', ref_nombre = '".$fDTranspor['ref_nombre']."', 
            ref_telefo = '".$fDTranspor['ref_telefo']."', cod_ciiuxx = '".$fDTranspor['cod_ciiuxx']."', cod_refciiu = '".$fDTranspor['cod_refciiu']."', 
            cod_asocia = '".$fDTranspor['cod_asocia']."', fec_vencar = '".$fDTranspor['fec_vencar']."', cod_agenci = '".$fDTranspor['cod_agenci']."', 
            fec_visdom = '".$fDTranspor['fec_visdom']."', vis_domici = '".$fDTranspor['vis_domici']."', nom_epsxxx = '".$fDTranspor['nom_epsxxx']."', 
            nom_arpxxx = '".$fDTranspor['nom_arpxxx']."', nom_pensio = '".$fDTranspor['nom_pensio']."', nom_compen = '".$fDTranspor['nom_compen']."', 
            usr_creaci = '".$fNomUsuari."', fec_creaci = NOW()
            ON DUPLICATE KEY UPDATE
            cod_tercer = '".$fDTranspor['cod_tercer']."' ";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          
  
          $mSelect="INSERT INTO ".BASE_DATOS.".tab_tercer_emptra SET 
                                                                  cod_tercer = '".$fDTranspor['cod_tercer']."', cod_minins = '', num_resolu = '',
                                                                  fec_resolu = '0000-00-00', num_region = '0', ran_iniman = '',
                                                                  ran_finman = '' , ind_gracon = 'N', ind_ceriso = 'N',
                                                                  fec_ceriso = '0000-00-00', ind_cerbas = 'N', fec_cerbas = '0000-00-00',
                                                                  otr_certif = '', ind_cobnal = 'N', ind_cobint = 'N',
                                                                  nro_habnal = '', fec_resnal = '0000-00-00', nom_repleg = '',
                                                                  val_pctret = '0.01', usr_creaci = '".$fNomUsuari."', fec_creaci = NOW(),
                                                                  usr_modifi = NULL, fec_modifi = NULL
                                                                  ON DUPLICATE KEY UPDATE
                                                                  cod_tercer = '".$fDTranspor['cod_tercer']."'
                                                                  ";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
  
          $mSelect="INSERT INTO ".BASE_DATOS.".tab_tercer_activi SET cod_tercer = '".$fDTranspor['cod_tercer']."', cod_activi = '1' ON DUPLICATE KEY UPDATE
                    cod_tercer = '".$fDTranspor['cod_tercer']."'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $cod_servic = 3;
  
          $mSelect="SELECT COUNT(*) as 'can' FROM ".BASE_DATOS.".tab_transp_tipser WHERE cod_transp = '".$fDTranspor['cod_tercer']."'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $can = $fConsult -> RetMatrix('a')[0]['can'];
          $ultimo = 1;
          if($can>0){
            $mSelect="SELECT MAX(num_consec) as 'max' FROM ".BASE_DATOS.".tab_transp_tipser WHERE cod_transp = '".$fDTranspor['cod_tercer']."'";
            $fConsult -> ExecuteCons( $mSelect, "R" ) ;
            $max = $fConsult -> RetMatrix('a')[0]['max'];
            $ultimo = $max+1;
          }
  
  
          $mSelect="SELECT COUNT(*) as 'can_age' FROM ".BASE_DATOS.".tab_transp_agenci WHERE cod_transp = '".$fDTranspor['cod_tercer']."'";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          $can = $fConsult -> RetMatrix('a')[0]['can_age'];
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
  
          if($can==0){
            $mSelect="SELECT MAX(CAST(cod_agenci AS SIGNED)) as 'max' FROM ".BASE_DATOS.".tab_genera_agenci";
            $fConsult -> ExecuteCons( $mSelect, "R" ) ;
            $max = $fConsult -> RetMatrix('a')[0]['max'];
            $cod_agencia = $max+1;
  
            $mSelect="INSERT INTO ".BASE_DATOS.".tab_genera_agenci(
              cod_agenci, nom_agenci, cod_paisxx, 
              cod_depart, cod_ciudad, dir_agenci, 
              tel_agenci, con_agenci, dir_emailx, 
              num_faxxxx, cod_agesat, usr_creaci, 
              fec_creaci,cod_estado
            ) 
            VALUES 
              (
                '$cod_agencia', '".$fDTranspor['nom_tercer']."', ".$fDTranspor['cod_paisxx'].",
                ".$fDTranspor['cod_depart'].",".$fDTranspor['cod_ciudad'].",'".$fDTranspor['dir_domici']."',
                '".$fDTranspor['num_telef1']."','".$fDTranspor['nom_tercer']."','".$fDTranspor['dir_emailx']."',
                '',NULL,NOW(),'".$fNomUsuari."',1
              )
            ";
            $fConsult -> ExecuteCons( $mSelect, "R" ) ;
  
            $mSelect="INSERT INTO ".BASE_DATOS.".tab_transp_agenci(cod_transp, cod_agenci, cod_agetra) VALUES ('".$fDTranspor['cod_tercer']."','".$cod_agencia."', NULL)";
            $fConsult -> ExecuteCons( $mSelect, "R" ) ;
          }
  
          
          $mSelect="INSERT INTO ".BASE_DATOS.".tab_transp_tipser (
            num_consec, cod_transp, cod_tipser, 
            tie_contro, tie_conurb, ind_llegad, 
            ind_salaut, cod_server, ind_estado, 
            ind_notage, tie_trazab, tip_factur, 
            ind_calcon, tie_prcurb, tie_prcnac, 
            tie_prcimp, tie_prcexp, tie_prctr1, 
            tie_prctr2, tie_carurb, tie_carnac, 
            tie_carimp, tie_carexp, tie_cartr1, 
            tie_cartr2, tie_desurb, tie_desnac, 
            tie_desimp, tie_desexp, tie_destr1, 
            tie_destr2, can_llaurb, can_llanac, 
            can_llaimp, can_llaexp, can_llatr1, 
            can_llatr2, ind_excala, ind_segprc, 
            ind_segcar, ind_segtra, ind_segctr, 
            ind_segdes, ind_camrut, val_despac, 
            val_regist, fec_valreg, ind_biomet, 
            fec_iniser, hor_iniser, fec_finser, 
            hor_finser, nom_aplica, ind_planru, 
            tie_traexp, tie_traimp, tie_tratr1, 
            tie_tratr2, cod_grupox, cod_operac, 
            cod_priori, ind_conper, hor_pe1urb, 
            hor_pe2urb, hor_pe1nac, hor_pe2nac, 
            hor_pe1imp, hor_pe2imp, hor_pe1exp, 
            hor_pe2exp, hor_pe1tr1, hor_pe2tr1, 
            hor_pe1tr2, hor_pe2tr2, ind_solpol, 
            cod_asegur, num_poliza, hab_asicar, 
            fec_asicar, fec_creaci, usr_creaci, 
            dup_manifi
          ) 
          VALUES 
            (
              '".$ultimo."', '".$fDTranspor['cod_tercer']."', '".$cod_servic."', '0', '0', '0', 
              '1', NULL, '1', '0', '0', '0', NULL, 
              '0', '0', '0', '0', '0', '0', '0', '0', 
              '0', '0', '0', '0', '0', '0', '0', '0', 
              '0', '0', NULL, NULL, NULL, NULL, NULL, 
              NULL, '0', '0', '0', '0', '0', '0', '1', 
              NULL, NULL, '', '0', NULL, NULL, NULL, 
              NULL, NULL, '0', NULL, NULL, NULL, NULL, 
              NULL, NULL, NULL, '0', NULL, NULL, NULL, 
              NULL, NULL, NULL, NULL, NULL, NULL, 
              NULL, NULL, NULL, '0', '', '', '1', NOW(), 
              NOW(), '".$fNomUsuari."', '0'
            );";
          $fConsult -> ExecuteCons( $mSelect, "R" ) ;
  
          $fReturn['cod_respon'] = "1000";
          $fReturn['msg_respon'] = "Se Realizo la operacion exitosamente";
        }
        catch( Exception $e )
        {
          if( "3001" == $e -> getCode() )
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:Se ha presentado un error el cual ya fue notificado.";
          else
          {
            $fReturn = "code_resp:".$e -> getCode()."; msg_resp:".$e -> getMessage();
  
            $fExcept -> CatchError( $e -> getCode(), $e -> getMessage(), $e -> getFile(),
                                    $e -> getLine(), $fNomAplica, $fNumDespac );
          }
        }
        return json_encode($fReturn);
    }
  
    function getTransport($fNomUsuari = NULL ,$fPwdClavex = NULL ,$fNomAplica = NULL, $fValBuscar = NULL)
    {
      try {
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getTransport" );
  
        // no colocar las dos lineas en prod
        if(!defined("RootDir5")) define("RootDir5", "/var/www/html/ap/");
        if(!defined("Hostx5"))  define("Hostx5", "devbd.intrared.net:3306");
  
        $mFlagPHP = 4;
        $mServerBD = Hostxx;
        $nom_archi = $fNomAplica;
        
        if( file_exists( RootDir.$fNomAplica."/constantes.inc") )
        {
          include_once( RootDir.$fNomAplica."/constantes.inc" );
          $mServerBD =  Hostxx;
          $mFlagPHP = 4;
          $nom_archi = RootDir.$fNomAplica."/constantes.inc";
        }
        else if( file_exists( RootDir5.$fNomAplica."/constantes.inc" ) )
        {
          include_once( RootDir5.$fNomAplica."/constantes.inc" );
          $mServerBD =  Hostx5;  
          $mFlagPHP = 5;
          $nom_archi = RootDir5.$fNomAplica."/constantes.inc";
        }
        else
        { 
          throw new Exception( "Aplicacion no encontrada ->".$nom_archi, "1999" );
          break;
        }
        /*
        <------Validar con Andres Torres-------->
        if( !getAutentica( $fNomUsuari, $fPwdClavex) )
        {
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }
        */
        $fConsult = new Consult( array( "server"=> $mServerBD, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
        $mSql = "SELECT b.nom_apell1, b.nom_apell2, b.nom_tercer, b.num_telef1, b.num_telef2, b.num_telmov
                 FROM ".BASE_DATOS.".tab_tercer_conduc a
                 INNER JOIN ".BASE_DATOS.".tab_tercer_tercer b ON
                 a.cod_tercer = b.cod_tercer
                 WHERE a.cod_tercer = '$fValBuscar' AND b.cod_estado = 1 ";
       
        $fConsult -> ExecuteCons( $mSql );
        $fDat_respon = $fConsult -> RetMatrix('a');
  
        if (sizeof($fDat_respon) <= 0) {
          $fReturn['dat_respon'] = NULL;
        }else{
          $fReturn['dat_respon'] = $fDat_respon;
        }
        $fReturn['cod_respon'] = "1000";
        $fReturn['msg_respon'] = "Listado OK";
  
      } catch (Exception $e) {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fReturn['dat_respon'] = array();
      }
          return $fReturn;
    }
  
  
    function getVehicu($fNomUsuari = NULL ,$fPwdClavex = NULL ,$fNomAplica = NULL, $fValBuscar = NULL)
    {
      try {
  
        $fExcept = new Error( array( "dirlog" => LogDir, "notlog" => TRUE, "logmai" => NotMai ) );
        $fExcept -> SetUser( $fNomUsuari );
        $fExcept -> SetParams( "Faro", "getTransport" );
  
        // no colocar las dos lineas en prod
        if(!defined("RootDir5")) define("RootDir5", "/var/www/html/ap/");
        if(!defined("Hostx5"))  define("Hostx5", "devbd.intrared.net:3306");
  
        $mFlagPHP = 4;
        $mServerBD = Hostxx;
        $nom_archi = $fNomAplica;
        
        if( file_exists( RootDir.$fNomAplica."/constantes.inc") )
        {
          include_once( RootDir.$fNomAplica."/constantes.inc" );
          $mServerBD =  Hostxx;
          $mFlagPHP = 4;
          $nom_archi = RootDir.$fNomAplica."/constantes.inc";
        }
        else if( file_exists( RootDir5.$fNomAplica."/constantes.inc" ) )
        {
          include_once( RootDir5.$fNomAplica."/constantes.inc" );
          $mServerBD =  Hostx5;  
          $mFlagPHP = 5;
          $nom_archi = RootDir5.$fNomAplica."/constantes.inc";
        }
        else
        { 
          throw new Exception( "Aplicacion no encontrada ->".$nom_archi, "1999" );
          break;
        }
  
        
        /*
        <------Validar con Andres Torres-------->
        if( !getAutentica( $fNomUsuari, $fPwdClavex) )
        {
          throw new Exception( "Clave y/o usuario incorrectos.", "1002" );
          break;
        }
        */
        $fConsult = new Consult( array( "server"=> $mServerBD, "user"  => USUARIO, "passwd" => CLAVE, "db" => BD_STANDA ), $fExcept );
  
        $mSql = "SELECT b.nom_marcax, c.nom_colorx, a.num_config
                 FROM ".BASE_DATOS.".tab_vehicu_vehicu a
                 INNER JOIN ".BASE_DATOS.".tab_genera_marcas b ON
                 a.cod_marcax = b.cod_marcax
                 INNER JOIN ".BASE_DATOS.".tab_vehige_colore c ON
                 a.cod_colorx = c.cod_colorx
                 WHERE a.num_placax = '$fValBuscar' AND a.ind_estado = 1 ";
       
        $fConsult -> ExecuteCons( $mSql );
        $fDat_respon = $fConsult -> RetMatrix('a');
  
        if (sizeof($fDat_respon) <= 0) {
          $fReturn['dat_respon'] = NULL;
        }else{
          $fReturn['dat_respon'] = $fDat_respon;
        }
  
        $fReturn['cod_respon'] = "1000";
        $fReturn['msg_respon'] = "Listado OK";
  
      } catch (Exception $e) {
          $fReturn['cod_respon'] = $e -> getCode();
          $fReturn['msg_respon'] = $e -> getMessage();
          $fReturn['dat_respon'] = array();
      }
          return $fReturn;
    }
  
    
    /*! \fn: cleanArray
    *  \brief: Limpia los datos de cualquier caracter especial para corregir codificación
    *  \author: Ing. Luis Manrique
    *  \date: 03-04-2020
    *  \date modified: dd/mm/aaaa
    *  \param: $arrau => Arreglo que será analizado por la función
    *  \return: array
    */
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
             $arrayReturn[$convert($key)] = cleanArray($value);
            }else{
            //Clean value
              $arrayReturn[$convert($key)] = $convert($value);
              }
            }
          //Return array
          return $arrayReturn;
          }
  try
  {
    $server = new SoapServer( WsDirx."faro.wsdl", array('encoding'=>'ISO-8859-1') );
    $server -> addFunction( "setSoliciRuta" );
    $server -> addFunction( "setSoliciSegimientoEspecial" );
    $server -> addFunction( "setSoliciPQR" );
    $server -> addFunction( "setSoliciOtros" );
    $server -> addFunction( "setSeguim" );
    $server -> addFunction( "setSeguimFTP" );
    $server -> addFunction( "setSeguimPC" );
    $server -> addFunction( "setLlegada" );
    $server -> addFunction( "routExists" );
    $server -> addFunction( "setRout" );
    $server -> addFunction( "setPc" );
    $server -> addFunction( "setPCIntracarga" );
    $server -> addFunction( "getPCsContratados" );
    $server -> addFunction( "getTipser" );
    $server -> addFunction( "getRutas" );
    $server -> addFunction( "setNovedadPC" );
    $server -> addFunction( "setNovedadGPS" );
    $server -> addFunction( "setNovedadNC" );
    $server -> addFunction( "getNovedades" );
    $server -> addFunction( "setCambioRuta" );
    $server -> addFunction( "setAnulad" );
    $server -> addFunction( "setRevSalida" );
    $server -> addFunction( "setRevLlegada" );
    $server -> addFunction( "setRutaFaro" );
    $server -> addFunction( "setTelConduc" );
    $server -> addFunction( "setHomoloData" );
    $server -> addFunction( "setHomoloData" );
    $server -> addFunction( "getHomoloData" );
    $server -> addFunction( "setSalida" );
    $server -> addFunction( "setDespacPdf" );     
    $server -> addFunction( "RegistrarCall" );  
    $server -> addFunction( "RegistrarCallIn" );  
    $server -> addFunction( "getDestin" );  
    $server -> addFunction( "setFecAditio" );  
    $server -> addFunction( "getNovedadEvent" );  
    $server -> addFunction( "getTraficoManifiesto" ); 
    $server -> addFunction( "setStatusItinerary" ); 
    $server -> addFunction( "getTransSolici" );
    $server -> addFunction( "getDatosSolicitudes" );
    $server -> addFunction( "getTipoSolicitud" );
    $server -> addFunction( "getServices" );
    $server -> addFunction( "getCiudades" );
    $server -> addFunction( "getCosTra" );
    $server -> addFunction( "setNuevaAsistencia" );
    $server -> addFunction( "getInfoSolicitud" );
    $server -> addFunction( "setAprobAsitencia" );
    $server -> addFunction( "getDataSendEmailSolici" );
    $server -> addFunction( "setAprobaTerminosFaro" );
    $server -> addFunction( "getTransport" );
    $server -> addFunction( "getVehicu" );
    $server -> addFunction( "getDataFormulario" );
    $server -> handle();
  }
  catch( Exception $e )
  {
    mail( "soporte.ingenieros@intrared.net", "Error-webservice", "ocurrio un error: ".$e -> getMessage() );
  }
?>
