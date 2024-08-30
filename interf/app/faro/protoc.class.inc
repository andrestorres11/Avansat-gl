<?php


/*! \Class: Protocol
*  \brief: Clase de matrices de comunicacion
*  \author: Ing. Nelson Liberato
*  \date: 01/11/2015   
*  \param: mConection  -  Variable de clase que almacena la conexion de la Base de datos
*  \param: mDatBasexx  -  Variable de clase con el nombre de la base de datos
*  \param: mCodTransp  -  Nit trasnportadora
*  \param: mNumDespac  -  numero de despacho
*  \param: mNumManifi  -  numero manifiesto
*  \param: mNumPlacax  -  numero de placa
*  \param: mCodNoveda  -  codigo de la novedad
*  \param: mCodContro  -  codigo de PC
*  \param: mTimDuraci  -  tiempo generado en minutos
*  \param: mFecNoveda  -  fecha de la novedad
*  \param: mDesNoveda  -  descripcion de la novedad
*  \param: mNomNoveda  -  nombre de la novedad
*  \param: mNomContro  -  nombre puesto de control
*  \param: mNomSitiox  -  nombre del sitio de la novedad
*  \param: mNumViajex  -  número de viaje
*  \comment: Se incluyen dos librerias que se usan en los webservices para generar los logs en archivo plano
*  \         Los logs se crean por fecha ano-mes-dia (Fichero = x dia), se crea el log cuando ay una excepcion del try-catch
*  \return array
*  \Bug: tmpl_file: se trae plantilla html de satt_standa, cuando se separe el ws con el gl se puede danar el envio de correos
*/
class Protocol  
{
 
  private static $cNomUsuari = NULL;
  private static $cCodTransp = NULL; 
  private static $cNumDespac = NULL;
  private static $cNumManifi = NULL;
  private static $cNumPlacax = NULL; 
  private static $cCodNoveda = NULL; 
  private static $cCodContro = NULL; 
  private static $cTimDuraci = NULL; 
  private static $cFecNoveda = NULL; 
  private static $cDesNoveda = NULL; 
  private static $cNomNoveda = NULL; 
  private static $cNomContro = NULL;
  private static $cNomSitiox = NULL; 
  private static $cNumViajex = NULL;
  private static $cResponxxx = NULL;
  private static $cConnectxx = NULL;
 

  /*! \fn: __construct
  *  \brief: Metodo principal para la Clase - Constructor
  *  \author: Ing. Nelson Liberato
  *  \date: 02/12/2015   
  *  \param: mConection : Conexion de base de datos 
  *  \param: mNomUsuari : Usuario de autenticación
  *  \param: mCodTransp : codigo de la transportadora
  *  \param: mNumDespac : numero de despacho
  *  \param: mNumManifi : numero de manisfiesto
  *  \param: mNumPlacax : numero de la placa
  *  \param: mCodNoveda : codigo de la novedad
  *  \param: mCodContro : codigo del pruesto de control
  *  \param: mTimDuraci : tiempo generado en minutos
  *  \param: mFecNoveda : fecha de la novedad
  *  \param: mDesNoveda : descripcion de la novedad
  *  \param: mNomNoveda : Nombre de la novedad en caso que no se conosca el codigo NULL
  *  \param: mNomContro : nombre del puesto de control en caso que se conosca el codigo del PC
  *  \param: mNomSitiox : nombre del sitio de la novedad (Aplica para anten de sitio)
  *  \param: mNumViajex : Numero de  viaje de corona (lo coloco por si las moscas)
  *  \return cResponxxx : retorna mensaje por medio de function SendProtoc
  */
  function __construct( $mConection = NULL, $mNomUsuari = NULL, $mCodTransp = NULL, $mNumDespac = NULL, 
                        $mNumManifi = NULL, $mNumPlacax = NULL, $mCodNoveda = NULL, $mCodContro = NULL, 
                        $mTimDuraci = NULL, $mFecNoveda = NULL, $mDesNoveda = NULL, $mNomNoveda = NULL, 
                        $mNomContro = NULL, $mNomSitiox = NULL, $mNumViajex = NULL )
  {
      self::$cConnectxx = $mConection;
      self::$cNomUsuari = $mNomUsuari;
      self::$cCodTransp = $mCodTransp;
      self::$cNumDespac = $mNumDespac;
      self::$cNumManifi = $mNumManifi;
      self::$cNumPlacax = $mNumPlacax;
      self::$cCodNoveda = self::getHomoloNovedaCorona($mCodNoveda); // $mCodNoveda;
      self::$cCodContro = $mCodContro;
      self::$cTimDuraci = $mTimDuraci;
      self::$cFecNoveda = $mFecNoveda;
      self::$cDesNoveda = $mDesNoveda;
      self::$cNomNoveda = $mNomNoveda;
      self::$cNomContro = $mNomContro;
      self::$cNomSitiox = $mNomSitiox;
      self::$cNumViajex = $mNumViajex;

      $mVerifyProtoc = self::ValidaProtocNoveda();      
      if( $mVerifyProtoc["cod_respon"] == '1000' )  {
        self::SendProtoc();
      }


  }


  /*! \fn: getHomoloNovedaCorona
  *  \brief: Metodo para validar si la novedad ingresada tiene relacion con la transportadora y esta asignada como protocolo
  *  \author: Ing. Nelson Liberato
  *  \date: 02/12/2015   
  *  \return cResponxxx : array
  */
  protected function getHomoloNovedaCorona( $mCodNoveda = NULL   )
  {
      $mNovedad = [
                    "20000" => "329",
                    "20001" => "296",
                    "20002" => "295",
                    "20003" => "283",
                    "20004" => "281",
                    "20005" => "279",
                    "20006" => "276",
                    "20007" => "275",
                    "20008" => "270",
                    "20009" => "272",
                    "20010" => "269",
                    "20011" => "273",
                    "20012" => "274",
                    "20013" => "271",
                    "20014" => "299",
                    "20015" => "311",
                    "20016" => "304",
                    "20017" => "315",
                    "20018" => "316",
                    "20019" => "307",
                    "20020" => "309",
                    "20021" => "318",
                    "20022" => "308",
                    "20023" => "313",
                    "20024" => "303",
                    "20025" => "310",
                    "20026" => "325",
                    "20027" => "324",
                    "20028" => "312",
                    "20029" => "323",
                    "20030" => "213",
                    "20031" => "314",
                    "20032" => "322",
                    "20033" => "320",
                    "20034" => "319",
                    "20035" => "317",
                    "20036" => "306",
                    "20037" => "305",
                    "20038" => "321",
                    "20039" => "302",
                    "20040" => "301",
                    "20041" => "326",
                    "20042" => "282",
                    "20043" => "285",
                    "20044" => "284",
                    "20045" => "280",
                    "20046" => "278",
                    "20047" => "286",
                    "20048" => "287",
                    "20049" => "288",
                    "20050" => "290",
                    "20051" => "291",
                    "20052" => "292",
                    "20053" => "293",
                    "20054" => "294",
                    "20055" => "297",
                    "20056" => "277",
                    "20057" => "360",
                    "20058" => "358",
                    "20059" => "359",
                    "20060" => "328",
                    "20061" => "350",
                    "20062" => "298",
                    "20063" => "349"
                  ];
      if( $mNovedad[ $mCodNoveda ] ){
        return $mNovedad[ $mCodNoveda ];
      }
      else{
        return $mCodNoveda;
      }
  } 

  /*! \fn: ValidaProtocNoveda
  *  \brief: Metodo para validar si la novedad ingresada tiene relacion con la transportadora y esta asignada como protocolo
  *  \author: Ing. Nelson Liberato
  *  \date: 02/12/2015   
  *  \return cResponxxx : array
  */
  protected function ValidaProtocNoveda(   )
  {
    try {     
    
 

      $_PERFIL = NULL;
      $_PROTOC = NULL;

      $fQueryNumDespac = "SELECT a.cod_respon, a.nom_respon 
                    FROM ".BASE_DATOS.".tab_genera_respon a, 
                         ".BASE_DATOS.".tab_genera_perfil b
                   WHERE a.cod_respon = b.cod_respon AND
                         b.cod_perfil IN ( SELECT xx.cod_perfil 
                                             FROM ".BASE_DATOS.".tab_genera_usuari xx 
                                            WHERE xx.cod_usuari = '".self::$cNomUsuari."' 
                                         ) ";
      
      self::$cConnectxx -> ExecuteCons( $fQueryNumDespac );
      if( 0 != self::$cConnectxx -> RetNumRows() ) {
        $_PERFIL = self::$cConnectxx -> RetMatrix( "a" );       
      }


      $mSelect = "SELECT a.cod_protoc, b.des_protoc, a.ind_notema
                    FROM ". BASE_DATOS .".tab_noveda_protoc a, 
                         ". BASE_DATOS .".tab_genera_protoc b
                   WHERE a.cod_protoc = b.cod_protoc
                     AND a.cod_transp = '".self::$cCodTransp."'
                     AND a.cod_noveda = '".self::$cCodNoveda."'";
      self::$cConnectxx -> ExecuteCons( $mSelect );
      if( 0 != self::$cConnectxx -> RetNumRows() ) {
        $_PROTOC = self::$cConnectxx -> RetMatrix( "a" );         
      }


      if( sizeof($_PROTOC) <= 0  ) {
        throw new Exception("No existe protocolo para la novedad: ".self::$cCodNoveda.". " , "3001");        
      }

      # Si tiene habilitado el envio de correo
      if( $_PROTOC[0]["ind_notema"] == 1) {
        self::$cResponxxx = array("cod_respon" => "1000", "msg_respon" => "La novedad ".self::$cCodNoveda." tiene protocolo.");
      }
      else {
        self::$cResponxxx = array("cod_respon" => "1000", "msg_respon" => "No existe protocolo para la novedad: ".self::$cCodNoveda.". Else ");
      }


    } 
    catch (Exception $e) {
      self::$cResponxxx = array("cod_respon" => $e -> getCode(), "msg_respon" => $e -> getMessage() );
    }
    self::LogFileTxt(self::$cResponxxx);
    return  self::$cResponxxx;
  }


  /*! \fn: LogFileTxt
  *  \brief: metodo para genrar log en TXT
  *  \author: Ing. Nelson Liberato
  *  \date: 24/02/2016   
  *  \param: $mMsgError      
  *  \return array
  */
  private function LogFileTxt($mLog = NULL)
  {
    try {

        
      $mFileName = date("Y-m-d")."_".self::$cNomUsuari.".txt";     
      $mFile = fopen("pdfcorona/".$mFileName, "a+");
      fwrite($mFile, date("Y-m-d H:i:s")."|".self::$cCodTransp."|".self::$cNumManifi."|".self::$cCodNoveda."|".(string)json_encode($mLog)."\n");      
      fclose($mFile);

      chmod("pdfcorona/".$mFileName, 0775);
      
    } catch (Exception $e) {
      
    }

  }

  /*! \fn: SendProtoc
  *  \brief: Metodo para enviar el protocolo segun matriz de comunicacion
  *  \author: Ing. Nelson Liberato
  *  \date: 02/12/2015   
  *  \return cResponxxx : array
  */
  public function SendProtoc()
  {
    try { 
        
        # Valida la consolidacion de un despacho
        $mDataDespac = self::getDataDespac(self::getVerifiConso() , self::$cNumDespac );

        # Consulta de correos segun novedad del despacho
        $_CENOPE     = self::getMailProtoc(  self::getVerifiConso() );

        # Consulta el nombre de la novedad para colocar en el asunto del correo
        $mSelect = "SELECT nom_noveda 
                      FROM ".BASE_DATOS.".tab_genera_noveda 
                     WHERE cod_noveda = '".self::$cCodNoveda."'";
        self::$cConnectxx -> ExecuteCons( $mSelect );       
        $mNomNoveda = self::$cConnectxx -> RetMatrix( "a" );  
        $mDataDespac['nom_noveda'] = $mNomNoveda[0]["nom_noveda"];
        $mAsunto = "Viaje - ".$mDataDespac['num_viajex']." - ".$mDataDespac['nom_noveda']." – Placa ".$mDataDespac['num_placax'];



        # Consulta nombre de usuario por medio del correo primario de la matriz de comunicaicones, asignado
        $mSelect = "SELECT nom_usuari 
                      FROM ".BASE_DATOS.".tab_genera_usuari 
                     WHERE usr_emailx = '".$_CENOPE['ema_conprX']."'"; 
        self::$cConnectxx -> ExecuteCons( $mSelect );       
        $mNomAsignado = self::$cConnectxx -> RetMatrix( "a" );



        # Datos complementarios -
        $mDataDespac['not_especi'] = 'NINGUNA';
        if( $mDataDespac["ind_insveh"] == '1' && $mDataDespac["cod_tipdes"]  == '4' ) {
          $mDataDespac['not_especi'] = 'SE DEBE REALIZAR INSPECCI&Oacute;N ANTINARC&Oacute;TICOS';
        }

        # Consulta nombre de usuario por medio del correo primario de la matriz de comunicaicones, asignado
        $mSelect = "SELECT nom_usuari 
                      FROM ".BASE_DATOS.".tab_genera_usuari 
                     WHERE cod_usuari = '".self::$cNomUsuari."'"; 
        self::$cConnectxx -> ExecuteCons( $mSelect );       
        $mNomAsignaxx = self::$cConnectxx -> RetMatrix( "a" );





        # Asignacion de datos a array de datos del despacho para enviar en la plantilla
        $mDataDespac['nom_sitiox'] = self::$cNomSitiox;
        $mDataDespac['obs_noveda'] = self::$cDesNoveda;
        $mDataDespac['fec_noveda'] = self::$cFecNoveda;
        $mDataDespac['nom_conasi'] = $mNomAsignaxx[0]["nom_usuari"];
        $mDataDespac['usr_conasi'] = self::$cNomUsuari;
        $mDataDespac['ema_asigna'] = $_CENOPE['ema_conprX'];
        $mDataDespac['nom_asigna'] = $mNomAsignado[0]['nom_usuari']; 
        self::LogFileTxt(array("dir_emailx"=>$_CENOPE, "dat_despac"=>$mDataDespac));

        if( sizeof( $_CENOPE ) > 0 )
        {
            $comp = "<br>ENVIADO A: ".( $_CENOPE['ema_conprX'] != '' ? $_CENOPE['ema_conprX'] : 'N/A' )."<br>COPIADO A: ".( $_CENOPE['ema_otrcoX'] != '' ? $_CENOPE['ema_otrcoX'] : 'N/A' )." ";
            $mUpdate = "UPDATE " . BASE_DATOS . ".tab_despac_noveda
                           SET des_noveda = CONCAT(des_noveda,'. ', '".$comp."' ) 
                         WHERE num_despac = '". $mDataDespac["num_despac"] ."'
                           AND cod_contro = '". self::$cCodContro ."'
                           AND cod_rutasx = '". $mDataDespac["cod_rutasx"] ."' 
                           AND cod_noveda = '". self::$cCodNoveda ."'
                           AND fec_noveda = '". self::$cFecNoveda ."' ";
            self::$cConnectxx -> ExecuteCons( $mUpdate );       
   
     
            # Validacion de plantilla degun transportadora
            $plantilla = "pla_notifi_corona.html";

            $mResult = array();

            $mCabece  = 'MIME-Version: 1.0' . "\r\n";
            $mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $mCabece .= 'From: Centro Logistico FARO <no-reply@eltransporte.org>' . "\r\n";

            if(self::$cCodTransp != 860068121)
            {

              $plantilla = "pla_notifi_cliente.html";

              $mCabece  = 'MIME-Version: 1.0' . "\r\n";
              $mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
              $mCabece .= 'From: Centro Logistico FARO ' . "\r\n";

              $NotifiAgenci = "SELECT ind_notage
                                 FROM ".BASE_DATOS.".tab_transp_tipser
                                WHERE cod_transp = '".self::$cCodTransp."' AND
                                  num_consec = (SELECT MAX(num_consec) FROM ".BASE_DATOS.".tab_transp_tipser WHERE cod_transp = '".self::$cCodTransp."')";
              self::$cConnectxx -> ExecuteCons( $NotifiAgenci );       
              $ind_notage = self::$cConnectxx -> RetMatrix( "a" );
              if ($ind_notage[0][0] == 1) {

                $sqlCodAgenci = "SELECT a.cod_agenci 
                                   FROM ".BASE_DATOS.".tab_despac_vehige a
                                  WHERE a.num_despac = '".$mDataDespac["num_despac"]."' ";  
                self::$cConnectxx -> ExecuteCons( $sqlCodAgenci );       
                $cod_agenci = self::$cConnectxx -> RetMatrix( "a" );
                $cod_agenci = $cod_agenci[0]['cod_agenci'];


                $mSql = "SELECT a.dir_correo 
                                   FROM ".BASE_DATOS.".tab_contac_protoc a 
                                  WHERE a.cod_transp = '".self::$cCodTransp."'
                                    AND a.cod_agenci = '".$cod_agenci."'";  
                self::$cConnectxx -> ExecuteCons( $mSql );       
                $mResult = self::$cConnectxx -> RetMatrix( "a" );

                } 
                else{

                  $mSql = "SELECT a.dir_correo 
                             FROM ".BASE_DATOS.".tab_contac_protoc a 
                            WHERE a.cod_transp = '".self::$cCodTransp."' ";  

                  self::$cConnectxx -> ExecuteCons( $mSql );       
                  $mResult = self::$cConnectxx -> RetMatrix( "a" );
                }

                $mCorreos = array();
                foreach ($mResult AS $row) {
                   $mCorreos = array_merge($mCorreos, $row);
                }

                $_CENOPE['ema_conprX'] = join(", ",$mCorreos);
                $_CENOPE['ema_otrcoX'] = join(", ",$mCorreos);                       
                
            }
           
            $num_despac = $mDataDespac['num_despac'];
            $nom_tipdes = $mDataDespac['nom_tipdes'];
            $cod_conduc = $mDataDespac['cod_conduc'];
            $nom_conduc = $mDataDespac['nom_conduc'];
            $cel_conduc = $mDataDespac['cel_conduc'];
            $tip_vehicu = $mDataDespac['tip_vehicu'];
            $nom_empres = $mDataDespac['nom_empres'];
            $num_placax = $mDataDespac['num_placax'];
            $nom_rutaxx = $mDataDespac['nom_rutaxx'];
            $nom_origen = $mDataDespac['nom_origen'];
            $nom_destin = $mDataDespac['nom_destin'];
            $cod_factur = $mDataDespac['cod_factur'];
            $nom_client = $mDataDespac['nom_client'];
            $num_viajex = $mDataDespac['num_viajex'];
            $nom_mercan = $mDataDespac['nom_mercan'];
            $num_pedido = $mDataDespac['num_pedido'];
            $num_solici = $mDataDespac['num_solici'];
            $nom_poseed = $mDataDespac['nom_poseed'];
            $nom_tiptra = $mDataDespac['nom_tiptra'];
            $not_especi = $mDataDespac['not_especi'];
            $nom_noveda = $mDataDespac['nom_noveda'];
            $nom_sitiox = $mDataDespac['nom_sitiox'];
            $obs_noveda = $mDataDespac['obs_noveda'];
            $fec_noveda = $mDataDespac['fec_noveda'];
            $nom_conasi = $mDataDespac['nom_conasi'];
            $usr_conasi = $mDataDespac['usr_conasi'];
            $ema_asigna = $mDataDespac['ema_asigna'];
            $nom_asigna = $mDataDespac['nom_asigna'];
            /*FIN VARIABLES*******************************************/  
            self::LogFileTxt(array("datos despacho para plantilla"=>$mDataDespac));

            /*INICIO ENVIO DE CORREOS***************************************/
            # incluye las plantillas del standa de GL, tal como se hace desde el Framework
            $tmpl_file = '/var/www/html/ap/satt_standa/planti/'.$plantilla;
            self::LogFileTxt(array("Plantila HTML Ruta"=>$tmpl_file));
            
            if(!file_exists($tmpl_file)) {
              throw new Exception("No existe la plantilla: ".$plantilla, 9999);              
            }

            
            $thefile = implode("", file( $tmpl_file ) );
            $thefile = addslashes($thefile);
            $thefile = "\$r_file=\"".$thefile."\";";
            eval( $thefile );
            $mHtmlxx = $r_file;
      

            $mCabece  = 'MIME-Version: 1.0' . "\r\n";
            $mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $mCabece .= 'From: Centro Logistico FARO <no-reply@eltransporte.org>' . "\r\n";

            #$_CENOPE['ema_conprX'] = 'nelson.liberato@intrared.net';
            #$_CENOPE['ema_otrcoX'] = 'maribel.pastor@eltransporte.org';

            self::LogFileTxt(array("Antes de Mail"=>array("Para"=> $_CENOPE['ema_conprX'], "Asunto" => $mAsunto, "Copia" => $_CENOPE['ema_otrcoX'], "HTMLSend" => $mHtmlxx   )));
               
            if($_SERVER["SERVER_NAME"] != "dev.intrared.net" | $_SERVER["SERVER_NAME"] != "qa.intrared.net"){
              if(!mail( $_CENOPE['ema_conprX'], $mAsunto, $mHtmlxx, $mCabece.'Cc: '.$_CENOPE['ema_otrcoX']. "\r\n") ) {
                  self::LogFileTxt(array("Log First Mail"=> "no se puedo enviar el correo") );            
              } else {
                  self::LogFileTxt(array("Log First Mail"=> "Correo enviado OK :) ") );         
              }            
              mail( MAIL_SUPERVISORES.", maribel.garcia@eltransporte.org ", $mAsunto." - Validacion Correo PARA Replica Terceros Corona INTERFAZ ", 
                    $mHtmlxx."<br><br>ENVIADO A: ".$_CENOPE['ema_conprX']."<br>COPIADO A: ".$_CENOPE['ema_otrcoX'], 
                    $mCabece);
            }


            if( self::$cCodNoveda != '242' )
            {
              $mAsigna = NULL;
               
              $ema_usu = explode( ',', $_CENOPE['ema_conprX'] );
              $mSelect = "SELECT cod_usuari
                            FROM ".BASE_DATOS.".tab_genera_usuari 
                           WHERE usr_emailx = '".trim( $ema_usu[0] )."'"; 
              self::$cConnectxx -> ExecuteCons( $mSelect );       
              $mAsigna = self::$cConnectxx -> RetMatrix( "a" );

              
              $mSelect = "SELECT MAX(num_consec) AS num_consec
                            FROM ". BASE_DATOS .".tab_protoc_asigna 
                           WHERE num_despac = '".$mDataDespac["num_despac"]."' ";
              self::$cConnectxx -> ExecuteCons( $mSelect );       
              $mConsec = self::$cConnectxx -> RetMatrix( "a" );
   
              if( sizeof( $mAsigna ) > 0   )/* && $_REQUEST['ind_activo_'] == 'S' */
              { 
                $mInsert = "INSERT INTO ". BASE_DATOS .".tab_protoc_asigna
                                      ( num_consec, num_despac, cod_contro, 
                                        cod_rutasx, cod_noveda, fec_noveda, 
                                        obs_noveda, des_htmlxx, usr_asigna,
                                        ind_ejecuc
                                      )
                                VALUES( '".( $mConsec[0]["num_consec"] + 1 )."', '".$mDataDespac["num_despac"]."', '". self::$cCodContro ."',
                                        '". $mDataDespac["cod_rutasx"] ."', '".self::$cCodNoveda."', '".self::$cFecNoveda."',
                                        '". addslashes( self::$cDesNoveda )."', '".addslashes($mHtmlxx)."', '".$mAsigna[0]["cod_usuari"]."',
                                        '0'
                                      )"; 
                 
                self::$cConnectxx -> ExecuteCons( $mInsert ); 
              }
            } 
            
                   
        }


        # Parche solicitado por mari para que se coloque los correos enviados por la matriz --------------------------------------------
        $mSql  = "SELECT obs_contro FROM ".BASE_DATOS.".tab_despac_contro WHERE num_despac = '".$mDataDespac["num_despac"]."' AND cod_contro = '".self::$cCodContro ."' AND cod_rutasx = '". $mDataDespac["cod_rutasx"] ."' AND cod_noveda = '".self::$cCodNoveda ."'  AND fec_contro = '".self::$cFecNoveda."' ";
        self::LogFileTxt(array("SELECT NOVEDAD"=> $mSql ) );  
        self::$cConnectxx -> ExecuteCons( $mSql );       
        $getNovedad = self::$cConnectxx -> RetMatrix( "a" );
        self::LogFileTxt(array("DATA NOVEDAD"=> $getNovedad ) );  


        $mWithMail = $getNovedad[0]["obs_contro"]." <br>ENVIADO A: ".$_CENOPE['ema_conprX']."<br>COPIADO A: ".$_CENOPE['ema_otrcoX'];

        $mUpdate = "UPDATE ".BASE_DATOS.".tab_despac_contro SET obs_contro = '".$mWithMail."' WHERE num_despac = '".$mDataDespac["num_despac"]."' AND cod_contro = '".self::$cCodContro ."' AND cod_rutasx = '". $mDataDespac["cod_rutasx"] ."' AND cod_noveda = '".self::$cCodNoveda ."' AND fec_contro = '".self::$cFecNoveda."' ";
        self::LogFileTxt(array("UPDATE NOVEDAD"=> $mUpdate ) );  
        self::$cConnectxx -> ExecuteCons( $mUpdate );
        # Fin  del parche de mari :) -------------------------------------------------------------------------------------------------------



        self::$cResponxxx = array("cod_respon" => "1000", 
                                  "msg_respon" => "La novedad ".self::$cCodNoveda." tiene protocolo y fue enviado con exito.");
       
    }
    catch (Exception $e) {
      self::$cResponxxx = array("cod_respon" => $e -> getCode(), "msg_respon" => $e -> getMessage() );
       self::LogFileTxt(array("Exception en plantilla"=>self::$cResponxxx));
    }
    return  self::$cResponxxx;
  }

  /*! \fn: getVerifiConso
  *  \brief: Metodo para verificar si el despacho es un consolidado
  *  \author: Ing. Nelson Liberato
  *  \date: 02/12/2015   
  *  \return cResponxxx : array
  */
  private function getVerifiConso()
  {
    try {
      $mCodDeshij = NULL;
      $mSql = " SELECT a.cod_deshij
                  FROM ".BASE_DATOS.".tab_consol_despac a 
                 WHERE a.cod_despad = '".self::$cNumDespac."' 
                 LIMIT 0, 1 ";

      self::$cConnectxx -> ExecuteCons( $mSql );
      if( 0 != self::$cConnectxx -> RetNumRows() ) {
        $mCodDeshij = self::$cConnectxx -> RetMatrix( "a" );         
      }
   
      if($mCodDeshij == NULL){
        return self::$cNumDespac;
      }else{
        return $mCodDeshij[0]["cod_deshij"];
      }
      
    } 
    catch (Exception $e) {
      return self::$cNumDespac;
    }
      
  }

  /*! \fn: getResponse
  *  \brief: Metodo para retornar los mensajes de la ejecucion de matrices
  *  \author: Ing. Nelson Liberato
  *  \date: 02/12/2015   
  *  \return cResponxxx : array
  */
  public function getDataDespac( $mNumDespac = NULL, $mNumDespacOri = NULL )
  {
      $mReturn = array();

      # Consulta los datos basicos del despacho ---------------------------------------------------------------------------
      $mQuery = "SELECT a.num_despac, a.cod_manifi, 
                   IF( b.nom_conduc IS NOT NULL, b.nom_conduc, c.abr_tercer) AS abr_tercer, c.cod_tercer,
                   IF( a.con_telmov IS NULL OR a.con_telmov = '', c.num_telmov, a.con_telmov ) AS telmov,
                   IF( a.con_telef1 IS NULL OR a.con_telef1 = '', c.num_telef1, a.con_telef1),ind_defini,
                       e.nom_operad, f.nom_califi, a.tie_contra, a.ind_tiemod, a.obs_tiemod, b.num_placax,
                       b.cod_transp, UPPER( z.abr_tercer ) AS nom_transp,
                       x.nom_tipdes, m.tip_vehicu, a.cod_tipdes, b.cod_rutasx
                  FROM " . BASE_DATOS . ".tab_despac_despac a 
             LEFT JOIN " . BASE_DATOS . ".tab_despac_corona m 
                    ON a.num_despac = m.num_dessat,
                       " . BASE_DATOS . ".tab_despac_vehige b,
                       " . BASE_DATOS . ".tab_genera_tipdes x,
                       " . BASE_DATOS . ".tab_tercer_tercer c,
                       " . BASE_DATOS . ".tab_tercer_tercer z,
                       ". BASE_DATOS .".tab_tercer_conduc d 
             LEFT JOIN ".  BASE_DATOS .".tab_genera_califi f ON f.cod_califi = d.cod_califi
             LEFT JOIN ".  BASE_DATOS .".tab_operad_operad e ON e.cod_operad = d.cod_operad
                 WHERE a.num_despac = b.num_despac AND
                       b.cod_transp = z.cod_tercer AND
                       a.cod_tipdes = x.cod_tipdes AND
                       b.cod_conduc = c.cod_tercer AND
                       d.cod_tercer = c.cod_tercer AND
                       a.num_despac = '".$mNumDespac."'";
      self::$cConnectxx -> ExecuteCons( $mQuery );
      $_DATACON = self::$cConnectxx -> RetMatrix( "a" );         
     

      # Consulta los datos de la ruta del despacho --------------------------------------------------------------------------------------
      $mQuery = "SELECT e.nom_rutasx,a.cod_ciuori,a.cod_ciudes,
                    if(b.fec_llegpl Is Null,'SIN CONFIRMAR',DATE_FORMAT(b.fec_llegpl ,'%H:%i %d-%m-%Y')),
           DATE_FORMAT(a.fec_creaci,'%H:%i %d-%m-%Y'),DATE_FORMAT(a.fec_llegad,'%H:%i %d-%m-%Y')
                  FROM " . BASE_DATOS . ".tab_despac_vehige b,
                       " . BASE_DATOS . ".tab_genera_rutasx e,
                       " . BASE_DATOS . ".tab_genera_agenci g,
                       " . BASE_DATOS . ".tab_despac_despac a  
                 WHERE a.num_despac = b.num_despac AND
                       b.cod_rutasx = e.cod_rutasx AND
                       b.cod_agenci = g.cod_agenci AND
                       a.num_despac = '" . $mNumDespac . "'"; 
      self::$cConnectxx -> ExecuteCons( $mQuery );
      $_DATARUT = self::$cConnectxx -> RetMatrix( "a" );         

   
      # Consulta datos de las facturas y datos de los clientes -------------------------------------------------------------------------
      $_FACTUR = "";
      $_CLIENT = "";
      $mQuery = "SELECT num_despac, num_docume, nom_destin
                    FROM " . BASE_DATOS . ".tab_despac_destin
                   WHERE num_despac = '".$mNumDespacOri."' 
                   ORDER BY 1"; 
      self::$cConnectxx -> ExecuteCons( $mQuery );
      $mFacturas = self::$cConnectxx -> RetMatrix( "a" );  

      if( sizeof( $mFacturas ) > 0 )
      {
        foreach( $mFacturas AS $mFactur )
        {
          $_FACTUR .= $_FACTUR != '' ? ','.$mFactur['num_docume'] : $mFactur['num_docume'];
          $_CLIENT .= $_CLIENT != '' ? ','.$mFactur['nom_destin'] : $mFactur['nom_destin'];
          
        }
      }
      else
      {
        $_FACTUR = "N/A";
        $_CLIENT = "N/A";
      }


      # Consulta detalles numero de viaje externo (Vj---) --------------------------------------------------------------------------------------
      $mQuery = "SELECT a.num_despac, a.num_desext, a.num_pedido, 
                       a.num_solici, a.num_consol, a.tip_transp, 
                       b.nom_produc, c.nom_poseed
                  FROM " . BASE_DATOS . ".tab_despac_sisext a 
             LEFT JOIN " . BASE_DATOS . ".tab_genera_produc b 
                    ON b.cod_produc = a.cod_mercan
             LEFT JOIN " . BASE_DATOS . ".tab_despac_corona c
                    ON a.num_despac = c.num_dessat
         WHERE a.num_despac = '".self::$cNumDespac."' ";
      self::$cConnectxx -> ExecuteCons( $mQuery );
      $mNumDesext = self::$cConnectxx -> RetMatrix( "a" );  
         
      if( $mNumDesext[0]['tip_transp'] == '1' )
        $tip_transp = 'FLOTA PROPIA';
      elseif( $mNumDesext[0]['tip_transp'] == '2' )
        $tip_transp = 'TERCEROS';
      elseif( $mNumDesext[0]['tip_transp'] == '3' )
        $tip_transp = 'EMPRESAS';
      else
        $tip_transp = 'DESCONOCIDO';


      # Consulta indicador para inspeccion vehicular
      $mQuery = "SELECT   ind_insveh  
                   FROM " . BASE_DATOS . ".tab_genera_noveda
                  WHERE cod_noveda = '" . self::$cCodNoveda . "' ";
      self::$cConnectxx -> ExecuteCons( $mQuery );
      $mInsVehi = self::$cConnectxx -> RetMatrix( "a" ); 
           
      
      # Crea array con los datos de las consultas anteriores 
      $mReturn['num_despac'] = $_DATACON[0]['num_despac'];
      $mReturn['nom_tipdes'] = $_DATACON[0]['nom_tipdes'];
      $mReturn['cod_conduc'] = $_DATACON[0]['cod_tercer'];
      $mReturn['nom_conduc'] = $_DATACON[0]['abr_tercer'];
      $mReturn['cel_conduc'] = $_DATACON[0]['telmov'];
      $mReturn['tip_vehicu'] = $_DATACON[0]['tip_vehicu'];
      $mReturn['nom_empres'] = $_DATACON[0]['nom_transp'];
      $mReturn['num_placax'] = $_DATACON[0]['num_placax'];
      $mReturn['cod_tipdes'] = $_DATACON[0]['cod_tipdes'];
      $mReturn['nom_rutaxx'] = $_DATARUT[0]['nom_rutasx'];
      $mReturn['cod_rutasx'] = $_DATACON[0]['cod_rutasx'];
      $mReturn['ind_insveh'] = $mInsVehi[0]['ind_insveh'];
      $mReturn['nom_origen'] = self::getCiudad( $_DATARUT[0]['cod_ciuori'], TRUE );
      $mReturn['nom_destin'] = self::getCiudad( $_DATARUT[0]['cod_ciudes'], TRUE );
      $mReturn['cod_factur'] = $_FACTUR;
      $mReturn['nom_client'] = $_CLIENT;
      $mReturn['num_viajex'] = $mNumDesext[0]['num_desext'] != '' ? $mNumDesext[0]['num_desext'] : 'N/A';
      $mReturn['nom_mercan'] = $mNumDesext[0]['nom_produc'] != '' ? $mNumDesext[0]['nom_produc'] : 'N/A';
      $mReturn['num_pedido'] = $mNumDesext[0]['num_pedido'] != '' ? $mNumDesext[0]['num_pedido'] : 'N/A';
      $mReturn['num_solici'] = $mNumDesext[0]['num_solici'] != '' ? $mNumDesext[0]['num_solici'] : 'N/A';
      $mReturn['nom_poseed'] = $mNumDesext[0]['nom_poseed'] != '' ? $mNumDesext[0]['nom_poseed'] : 'N/A';
      $mReturn['nom_tiptra'] = $tip_transp; 
 
      return $mReturn;
  }

  /*! \fn: getCiudad
  *  \brief: Metodo consultar datos de una ciudad por el codigo
  *  \author: Ing. Nelson Liberato
  *  \date: 02/12/2015   
  *  \return cResponxxx : array
  */
  function getCiudad( $mCodCiudad, $mIndNamexx = FALSE )
  {
    $mQuery = "SELECT a.cod_ciudad,CONCAT(a.abr_ciudad,' (',LEFT(b.abr_depart,4),') - ',LEFT(c.nom_paisxx,3),' - ',a.cod_ciudad) AS nom_ciudad, UPPER( a.abr_ciudad ) AS abr_ciudad
        FROM " . BASE_DATOS . ".tab_genera_ciudad a,
             " . BASE_DATOS . ".tab_genera_depart b,
             " . BASE_DATOS . ".tab_genera_paises c
       WHERE a.cod_depart = b.cod_depart AND
             a.cod_paisxx = b.cod_paisxx AND
             b.cod_paisxx = c.cod_paisxx AND
             a.cod_ciudad = '" . $mCodCiudad . "' 
       GROUP BY 1 ORDER BY 2";
    self::$cConnectxx -> ExecuteCons( $mQuery );
    $mDatCiudad = self::$cConnectxx -> RetMatrix( "a" );  

  
    if($mIndNamexx === TRUE )
      return $mDatCiudad[0]["nom_ciudad"];
    else
      return $mDatCiudad[0]; 
  }



  /*! \fn: getMailProtoc
  *  \brief: Metodo para retornar los correos de contacto segun los criterios(Llaves) del despacho
  *  \author: Ing. Nelson Liberato
  *  \date: 02/12/2015   
  *  \return cResponxxx : array
  */
  private function getMailProtoc( $mNumDespac = NULL )
  {
      #Obtiene los criterios ------------------------------------------------------------------
      $mQuery = "SELECT cod_criter, nom_criter 
                    FROM ".BASE_DATOS.".tab_config_modcom 
                   ORDER BY 1";
      self::$cConnectxx -> ExecuteCons( $mQuery );
      $aCriter = self::$cConnectxx -> RetMatrix( "a" );  


      $mCriter = array();
      foreach ($aCriter as $row ) {
        $mCriter[ $row['nom_criter'] ] = $row['cod_criter'];
      }
      #-----------------------------------------------------------------------------------------

      # Datos del despacho (llaves) para buscar los correos que tengan esa configuracion -------
      $mQuery = "SELECT a.cod_ciuori, b.cod_mercan AS cod_produc, a.cod_ciudes,
                           a.cod_tipdes, b.cod_instal AS cod_zonaxx, c.cod_canalx,
                           b.tip_transp AS cod_tiptra, '" . self::$cCodNoveda . "' AS cod_noveda, a.num_despac, 
                           -- a.nom_sitcar AS cod_deposi
                           -- d.des_sitcar AS cod_deposi
                           e.cod_deposi
                      FROM " . BASE_DATOS . ".tab_despac_despac a 
                 LEFT JOIN " . BASE_DATOS . ".tab_despac_sisext b 
                        ON a.num_despac = b.num_despac 
                 LEFT JOIN " . BASE_DATOS . ".tab_despac_destin c 
                        ON a.num_despac = c.num_despac 
                 LEFT JOIN ". BASE_DATOS .".tab_despac_corona d 
                        ON a.num_despac = d.num_dessat
                 LEFT JOIN ".BASE_DATOS.".tab_genera_deposi e 
                        ON d.des_sitcar = e.nom_deposi
                     WHERE a.num_despac = '" . $mNumDespac . "' 
                     GROUP BY a.num_despac ";
      self::$cConnectxx -> ExecuteCons( $mQuery );
      $aDespac = self::$cConnectxx -> RetMatrix( "a" );  
      # ------------------------------------------------------------------------------------------




      # Consecutivo canal ya que no viaja por el wsdl de astrans ---------------------------------
      $mQuery = "SELECT con_consec 
                    FROM ".BASE_DATOS.".tab_genera_canalx 
                   WHERE cod_canalx = '".$aDespac[0]['cod_canalx']."' AND 
                         cod_produc = '".$aDespac[0]['cod_produc']."' ";
      self::$cConnectxx -> ExecuteCons( $mQuery );
      $aCanalx = self::$cConnectxx -> RetMatrix( "a" ); 
      # -------------------------------------------------------------------------------------------
    
      $aDespac[0]['cod_canalx'] = $aCanalx[0]['con_consec'] != '' ? $aCanalx[0]['con_consec'] : '';
    
      $mDespac = array();
      foreach ($aDespac[0] as $nom_campox => $val_campox) {
          if ($val_campox != '')
              $mDespac[$nom_campox] = $val_campox;
      }

      $mCompar = array();
      foreach ($mCriter as $nom_criter => $val_criter) {
          if ($mDespac[$nom_criter] != '')
              $mCompar[$val_criter] = $mDespac[$nom_criter];
      }

      $mCiuori = $mCompar[1];
      $mMercan = $mCompar[2];
      $mCiudes = $mCompar[3];
      $mTipdes = $mCompar[4];
      $mZonaxx = $mCompar[5];
      $mCanalx = $mCompar[6];
      $mTiptra = $mCompar[7];
      $mDeposi = $mCompar[8];

      /**************************************************************************/
      #Nelson Nuevo Matriz comunicacion ----------------------------------------------------
      $mMailTo = self::getDataListCriter( $aDespac[0], 'P' );
      $mMailCC = self::getDataListCriter( $aDespac[0], 'S' );
      # ------------------------------------------------------------------------------------

      # Valida que exista un correo o si no coloca opoveda por default --------------------- 
      $mQuery = "SELECT usr_emailx FROM ".BASE_DATOS.".tab_genera_usuari WHERE cod_usuari = 'povedad.a' ";
      self::$cConnectxx -> ExecuteCons( $mQuery );
      $mUsuari = self::$cConnectxx -> RetMatrix( "a" );              
   

      $mMailTo = $mMailTo[0]["usr_emailx"] == ''? $mUsuari[0]['usr_emailx'] : $mMailTo[0]["usr_emailx"];
      $mMailCC = $mMailCC[0]["usr_emailx"];
      #-------------------------------------------------------------------------------------
   
      $RESULTADO = array('ema_conprX' => $mMailTo,    'ema_otrcoX' => $mMailCC   );
      return $RESULTADO;  
  }




  /*! \fn: getDataListCriter
  *  \brief: Metodo consultar los correos segun los criterios(Llaves) del despacho 
  *  \author: Ing. Nelson Liberato
  *  \date: 02/12/2015   
  *  \return cResponxxx : array
  */
  function getDataListCriter( $mData = NULL, $mTipCorreo = 'P' )
  {

   

    $mListCr = '
          SELECT  
                 y.usr_emailx AS usr_emailx, 
                 "' . $mData["num_despac"] . '" AS num_despac,
                     z.cod_usuari


          FROM ( 

                SELECT aa.cod_usuari , 
                     aa.val_criter AS ValCriter1, aa.nom_criter AS NomCriter1, 
                     bb.val_criter AS ValCriter2, bb.nom_criter AS NomCriter2, 
                     cc.val_criter AS ValCriter3, cc.nom_criter AS NomCriter3,
                     dd.val_criter AS ValCriter4, dd.nom_criter AS NomCriter4,
                     ee.val_criter AS ValCriter5, ee.nom_criter AS NomCriter5,
                     ff.val_criter AS ValCriter6, ff.nom_criter AS NomCriter6,
                     gg.val_criter AS ValCriter7, gg.nom_criter AS NomCriter7,
                     hh.val_criter AS ValCriter8, hh.nom_criter AS NomCriter8

              FROM (
                            SELECT  a.cod_usuari, a.cod_consec
                              FROM   '.BASE_DATOS.'.tab_genera_modcom a 
                            WHERE  1 = 1 GROUP BY a.cod_usuari
                   ) xx
                    INNER JOIN 
                   (
                            SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, b.nom_ciudad AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a ,
                                  '.BASE_DATOS.'.tab_genera_ciudad b
                            WHERE a.cod_criter = "1" AND  
                                  a.val_criter = b.cod_ciudad AND                                                                         
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"   
                   ) aa
                    ON xx.cod_usuari = aa.cod_usuari '.($mData["cod_ciuori"] != '' ? ' AND  aa.val_criter = "'.$mData["cod_ciuori"].'" /*Origen*/ ' : '').'
                    LEFT JOIN
                   (
                            SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bc.nom_produc AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a, 
                                  '.BASE_DATOS.'.tab_genera_produc bc 
                            WHERE a.cod_criter = "2" AND       
                                  a.val_criter = bc.cod_produc AND                                                                    
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) bb
                    ON xx.cod_usuari = bb.cod_usuari '.($mData["cod_produc"] != '' ? ' AND  bb.val_criter = "'.$mData["cod_produc"].'" /*PRODUC*/ ' : '').'
                    LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bd.nom_ciudad AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_ciudad bd
                            WHERE a.cod_criter = "3" AND     
                                  a.val_criter = bd.cod_ciudad AND                                                                      
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) cc
                   ON xx.cod_usuari = cc.cod_usuari '.( $mData["cod_ciudes"] != '' ? ' AND  cc.val_criter = "'.$mData["cod_ciudes"].'" /*DESTIN*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, be.nom_tipdes AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_tipdes be
                            WHERE a.cod_criter = "4" AND  
                                  a.val_criter = be.cod_tipdes AND                                                                         
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'" 
                   ) dd
                   ON xx.cod_usuari = dd.cod_usuari '.($mData["cod_tipdes"] != '' ? ' AND  dd.val_criter = "'.$mData["cod_tipdes"].'" /*TIPDES*/ ' : '').'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bf.nom_canalx AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_canalx bf 
                            WHERE a.cod_criter = "6" AND
                                  a.val_criter = bf.con_consec AND                                                                           
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) ff
                   ON xx.cod_usuari = ff.cod_usuari '.( $mData["cod_canalx"] != '' ? ' AND  ff.val_criter = "'.$mData["cod_canalx"].'" /*Zona*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bg.nom_zonaxx AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_zonasx bg 
                            WHERE a.cod_criter = "5" AND 
                                  a.val_criter = bg.cod_zonaxx AND                                                                          
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) ee
                   ON xx.cod_usuari = ee.cod_usuari '.( $mData["cod_zonaxx"] != '' ? ' AND  ee.val_criter = "'.$mData["cod_zonaxx"].'" /*Canal*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bh.nom_tiptra AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_tiptra bh
                            WHERE a.cod_criter = "7" AND  
                                  a.val_criter = bh.cod_tiptra AND                                                                         
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) gg
                   ON xx.cod_usuari = gg.cod_usuari '.( $mData["cod_tiptra"] != '' ? ' AND  gg.val_criter = "'.$mData["cod_tiptra"].'" /*Tiptra*/ ' : ''   ).'
                   LEFT JOIN 
                   (
                           SELECT  a.cod_usuari, a.cod_noveda, a.cod_criter, a.val_criter, bi.nom_deposi AS nom_criter
                             FROM '.BASE_DATOS.'.tab_detail_modcom a,
                                  '.BASE_DATOS.'.tab_genera_deposi bi 
                            WHERE a.cod_criter = "8" AND  
                                  a.val_criter = bi.cod_deposi AND                                                                         
                                  a.cod_noveda = "'.$mData["cod_noveda"].'" AND 
                                  a.ind_tipres = "'.$mTipCorreo.'"  
                   ) hh
                   ON xx.cod_usuari = hh.cod_usuari '.( $mData["cod_deposi"] != '' ? ' AND  hh.val_criter = "'.$mData["cod_deposi"].'" /*DEPOSI*/ ' : ''   ).' '  ;
        $mListCr .= ' GROUP BY aa.cod_usuari ';


        $mListCr .= ' ) z, '.BASE_DATOS.'.tab_genera_usuari y WHERE z.cod_usuari = y.cod_usuari AND y.ind_estado = 1';

    

       
        if( $mData["cod_tipdes"] != '4' )
        $Order = array( '7' => '7', '4' => '4', '2' => '2', '1' => '1', '5' => '5', '6' => '6', '8' => '8', '3' => '3' );
        else
        $Order = array( '7' => '7', '4' => '4', '2' => '2', '3' => '3', '1' => '1', '5' => '5', '6' => '6', '8' => '8' );
      
        /*
        foreach ($Order AS $key => $value) 
        {
          switch ($key) 
          {
              case '7':
                $mListCr .= " \n AND z.ValCriter7 = '".$mData["cod_tiptra"]."' "; 
              break; 
              case '4':
               $mListCr .= " \n AND z.ValCriter7 = '".$mData["cod_tiptra"]."' ";
               $mListCr .= " \n AND z.ValCriter4 = '".$mData["cod_tipdes"]."' "; 
              break;
              case '2':
               $mListCr .= " \n AND z.ValCriter7 = '".$mData["cod_tiptra"]."' ";
               $mListCr .= " \n AND z.ValCriter4 = '".$mData["cod_tipdes"]."' ";
               $mListCr .= " \n AND z.ValCriter2 = '".$mData["cod_produc"]."' ";
               #$mListCr .= " \n AND z.ValCriter6 = '".$mData["cod_canalx"]."' "; 
              break;
              case '1':
               $mListCr .= " \n AND z.ValCriter7 = '".$mData["cod_tiptra"]."' ";
               $mListCr .= " \n AND z.ValCriter4 = '".$mData["cod_tipdes"]."' ";
               $mListCr .= " \n AND z.ValCriter2 = '".$mData["cod_produc"]."' ";
               $mListCr .= " \n AND z.ValCriter1 = '".$mData["cod_ciuori"]."' ";
               #$mListCr .=  $mData["cod_canalx"] != '' && $mData["cod_canalx"] != '0' && $mData["cod_canalx"] != NULL  ? " \n AND z.ValCriter6 = '".$mData["cod_canalx"]."' " :  "";
              break;             
          }
        }
        */

        // nueva forma de filtrar saltando las prioridades de los filtros $Order ----------------------------
        $mListCr .= $mData["cod_tiptra"] != '' && count($mData["cod_tiptra"]) >= 1 ? " \n AND z.ValCriter7 = '" . $mData["cod_tiptra"] . "' " : "";
        $mListCr .= $mData["cod_tipdes"] != '' && count($mData["cod_tipdes"]) >= 1 ? " \n AND z.ValCriter4 = '" . $mData["cod_tipdes"] . "' " : "";
        $mListCr .= $mData["cod_produc"] != '' && count($mData["cod_produc"]) >= 1 ? " \n AND z.ValCriter2 = '" . $mData["cod_produc"] . "' " : "";
        $mListCr .= $mData["cod_ciuori"] != '' && count($mData["cod_ciuori"]) >= 2 ? " \n AND z.ValCriter1 = '" . $mData["cod_ciuori"] . "' " : "";
        $mListCr .= $mData["cod_deposi"] != '' && count($mData["cod_deposi"]) >= 2 ? " \n AND z.ValCriter8 = '" . $mData["cod_deposi"] . "' " : "";

        $mListCr .= " ORDER BY 1 ";

        $mQueryCanal = $mListCr . " \n AND z.ValCriter6 = '" . $mData["cod_canalx"] . "' ";
 
        self::$cConnectxx -> ExecuteCons( $mListCr );
        $_Data = self::$cConnectxx -> RetMatrix( "a" );


        # consulta donde se reemplaza el usuario en la tabla                 
        for ($i = 0; $i < sizeof($_Data); $i++) {
            $mSelCor = "SELECT a.cod_reempl, a.cod_usuari, b.usr_emailx
                          FROM tab_restri_modcom a,
                               tab_genera_usuari b                             
                         WHERE a.cod_usuari = '" . $_Data[$i]['cod_usuari'] . "'
                           AND NOW() BETWEEN a.fec_inicia AND a.fec_finali
                           AND a.cod_reempl = b.cod_usuari";
            self::$cConnectxx -> ExecuteCons( $mSelCor );
            $aRemplaCorre = self::$cConnectxx -> RetMatrix( "a" );

            if ($_Data[$i]["cod_usuari"] == $aRemplaCorre[0]["cod_usuari"]) {
                $_Data[$i]["cod_usuari"] = $aRemplaCorre[0]["cod_reempl"];
                $_Data[$i]["usr_emailx"] = $aRemplaCorre[0]["usr_emailx"];
            }
        }

        # Crea lista de array de los correos y usuarios        
        for ($i = 0; $i < sizeof($_Data); $i++) {
            $mCor[] = $_Data[$i]["usr_emailx"];
            $mUsr[] = $_Data[$i]["cod_usuari"];
        }

        # Crea string separados por coma, correos y usuarios
        $mArrTotCor = join(',', $mCor);
        $mArrTotUsr = join(',', $mUsr);

        # Reasigna el array que retorna con los tres campos
        $_Data = null;
        $_Data[0]["usr_emailx"] = $mArrTotCor;
        $_Data[0]["num_despac"] = $mData["num_despac"];
        $_Data[0]["cod_usuari"] = $mArrTotUsr;

    
 
        return $_Data;
  }

  /*! \fn: getResponse
  *  \brief: Metodo para retornar los mensajes de la ejecucion de matrices
  *  \author: Ing. Nelson Liberato
  *  \date: 02/12/2015   
  *  \return cResponxxx : array
  */
  public function getResponse()
  {
    return self::$cResponxxx;
  }
   
}


?>