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
*  \param: mNumViajex  -  nÃºmero de viaje
*  \comment: Se incluyen dos librerias que se usan en los webservices para generar los logs en archivo plano
*  \         Los logs se crean por fecha ano-mes-dia (Fichero = x dia), se crea el log cuando ay una excepcion del try-catch
*  \return array
*  \Bug: tmpl_file: se trae plantilla html de satt_standa, cuando se separe el ws con el gl se puede danar el envio de correos
*/
class Notificacion  
{
 
  private static $cNomUsuari = NULL;
  private static $cCodTransp = NULL; 
  private static $cNumDespac = NULL;
  private static $cCodNoveda = NULL; 
  private static $cFecNoveda = NULL; 
  private static $cDesNoveda = NULL; 
  private static $cNomSitiox = NULL; 
  private static $cResponxxx = NULL;
  private static $cConnectxx = NULL;
 

  /*! \fn: __construct
  *  \brief: Metodo principal para la Clase - Constructor
  *  \author: Ing. Nelson Liberato
  *  \date: 02/12/2015   
  *  \param: mConection : Conexion de base de datos 
  *  \param: mNomUsuari : Usuario de autenticaciÃ³n
  *  \param: mCodTransp : codigo de la transportadora
  *  \param: mNumDespac : numero de despacho
  *  \param: mCodNoveda : codigo de la novedad
  *  \param: mFecNoveda : fecha de la novedad
  *  \param: mDesNoveda : descripcion de la novedad
  *  \param: mNomSitiox : nombre del sitio de la novedad (Aplica para anten de sitio)
  *  \return cResponxxx : retorna mensaje por medio de function SendProtoc
  */
  function __construct( $mConection = NULL, $mNomUsuari = NULL, $mCodTransp = NULL, $mNumDespac = NULL, 
                        $mCodNoveda = NULL, $mFecNoveda = NULL, $mDesNoveda = NULL, $mNomSitiox = NULL)
  {
      self::$cConnectxx = $mConection;
      self::$cNomUsuari = $mNomUsuari;
      self::$cCodTransp = $mCodTransp;
      self::$cNumDespac = $mNumDespac;
      self::$cCodNoveda = $mCodNoveda;
      self::$cFecNoveda = $mFecNoveda;
      self::$cDesNoveda = $mDesNoveda;
      self::$cNomSitiox = $mNomSitiox;

      self::SendNotifi();
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

  /*! \fn: SendNotifi
  *  \brief: Metodo para enviar la notificacion via email
  *  \author: Ing. Cristian Andrés Torres
  *  \date: 02/12/2022   
  *  \return cResponxxx : array
  */
  public function SendNotifi()
  {
    try { 
        # Valida la consolidacion de un despacho, num_despac, cod_noveda
        $mDataDespac = self::getDataDespac();
        $sEmails = self::getEmailsNotifi();
        $html_body=' 
                        <div class="row-table">
                            <div style="text-align: right;  width:20%; margin-right:5px;">
                                <strong>Transportadora:</strong>
                            </div>
                            <div style="text-align: left;  width:30%; ">
                                '.$mDataDespac['abr_tercer'].'
                            </div>
                        </div>
                        <div class="row-table">
                            <div style="text-align: right;  width:20%; margin-right:5px;">
                                <strong>No Despacho:</strong>
                            </div>
                            <div style="text-align: left;  width:30%; ">
                                '.$mDataDespac['num_despac'].'
                            </div>
                            <div style="text-align: right;  width:20%; margin-right:5px;">
                                <strong>Conductor:</strong>
                            </div>
                            <div style="text-align: left;  width:30%; ">
                                '.$mDataDespac['nom_conduc'].' '.$mDataDespac['ap11_conduc'].' '.$mDataDespac['ap12_conduc'].'
                            </div>
                        </div>
                        <div class="row-table">
                            <div style="text-align: right;  width:20%; margin-right:5px;">
                                <strong>Origen:</strong>
                            </div>
                            <div style="text-align: left;  width:30%; ">
                                '.$mDataDespac['nom_ciuori'].'
                            </div>
                            <div style="text-align: right;  width:20%; margin-right:5px;">
                                <strong>Celular:</strong>
                            </div>
                            <div style="text-align: left;  width:30%; ">
                                '.$mDataDespac['num_telmov'].' - '.$mDataDespac['num_telef1'].' - '.$mDataDespac['num_telef2'].'
                            </div>
                        </div>
                        <div class="row-table">
                            <div style="text-align: right;  width:20%; margin-right:5px;">
                                <strong>Destino:</strong>
                            </div>
                            <div style="text-align: left;  width:30%; ">
                                '.$mDataDespac['nom_ciudes'].'
                            </div>
                            <div style="text-align: right;  width:20%; margin-right:5px;">
                                <strong>Placa:</strong>
                            </div>
                            <div style="text-align: left;  width:30%; ">
                                '.$mDataDespac['num_placax'].'
                            </div>
                        </div>

                        <div class="header-table">
                            <div style="text-align: center; width:100%; ">
                                DATOS DE LA NOVEDAD
                            </div>
                        </div>
                        <div class="row-table">
                            <div style="text-align: right;  width:20%; margin-right:5px;">
                                <strong>Fecha y hora:</strong>
                            </div>
                            <div style="text-align: left;  width:80%; ">
                                '.self::$cFecNoveda.'
                            </div>
                        </div>
                        <div class="row-table">
                            <div style="text-align: right;  width:20%; margin-right:5px;">
                                <strong>Novedad:</strong>
                            </div>
                            <div style="text-align: left;  width:80%; ">
                                '.$mDataDespac['nom_noveda'].'
                            </div>
                        </div>
                        <div class="row-table">
                            <div style="text-align: right;  width:20%; margin-right:5px;">
                                <strong>Ubicación:</strong>
                            </div>
                            <div style="text-align: left;  width:80%; ">
                                '.self::$cNomSitiox.'
                            </div>
                        </div>
                        <div class="row-table">
                            <div style="text-align: right;  width:20%; margin-right:5px;">
                                <strong>Observación:</strong>
                            </div>
                            <div style="text-align: left;  width:80%; ">
                                INTERFAZ '.str_replace("'", "", self::$cDesNoveda).'
                            </div>
                        </div>';
        $base_mensaje = utf8_encode($html_body);
        $mAsunto = utf8_encode("Notificación Novedad Especial");
        $year=date('Y');
        if($sEmails != ''){
            $titulo = $mAsunto;
            $plantilla = "pla_notifi_novgps.html";
            $mResult = array();
            $mCabece  = 'MIME-Version: 1.0' . "\r\n";
            $mCabece .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $mCabece .= 'From: Novedades Centro Logistico FARO <supervisores@faro.com.co>' . "\r\n";
            $tmpl_file = '/var/www/html/ap/satt_standa/planti/'.$plantilla;
            $logo = "https://".$_SERVER['SERVER_NAME']."/ap/satt_faro/logos/LogoCLFaro.png";
            if(!file_exists($tmpl_file)) {
                throw new Exception("No existe la plantilla: ".$plantilla, 9999);              
            }
            $thefile = implode("", file( $tmpl_file ) );
            $thefile = addslashes($thefile);
            $thefile = "\$r_file=\"".$thefile."\";";
            eval( $thefile );
            $mHtmlxx = $r_file;
            mail($sEmails, $mAsunto, $mHtmlxx, $mCabece);
        }

        self::$cResponxxx = array("cod_respon" => "1000", 
                                  "msg_respon" => "La novedad ".self::$cCodNoveda." es especial y fue notificada via email.");
    }
    catch (Exception $e) {
      self::$cResponxxx = array("cod_respon" => $e -> getCode(), "msg_respon" => $e -> getMessage() );
    }
    return  self::$cResponxxx;
  }

  /*! \fn: getEmailsNotifi
  *  \brief: Obtiene los emails para notificaciones
  *  \author: Ing. Cristian Andrés Torres
  *  \date: 02/12/2022   
  *  \return emails : String
  */
  public function getEmailsNotifi(){
    $emailsSend = array();
    //Consulta los correos para notificar a la empresa transportadora
    $mQueryEmpTra = "SELECT a.dir_emailx
                FROM " . BASE_DATOS. ".tab_genera_concor a
                WHERE a.num_remdes = '".self::$cCodTransp."'
                    AND a.ind_nfarox = 1";
    $consulta = self::$cConnectxx -> ExecuteCons( $mQueryEmpTra );
    $mEmaEmpTra = self::$cConnectxx -> RetMatrix( );
    foreach($mEmaEmpTra as $emails){
        $mEmails = explode(",", $emails['dir_emailx']);
        foreach($mEmails as $key => $value){
            array_push($emailsSend, $value);
        }
    }
    //Consulta los correos parametrizados del CL Faro
    $mQueryNotFar = "SELECT a.dir_emailx
                FROM " . BASE_DATOS. ".tab_genera_concor a
                WHERE a.num_remdes = ''
                    AND a.ind_nfarox = 1";
    $consulta = self::$cConnectxx -> ExecuteCons( $mQueryNotFar );
    $mEmaNotFar = self::$cConnectxx -> RetMatrix( );
    foreach($mEmaNotFar as $emails){
            $mEmails = explode(",", $emails['dir_emailx']);
            foreach($mEmails as $key => $value){
                array_push($emailsSend, $value);
            }
    }
    $emails = implode(', ', $emailsSend);
    return $emails;
  }

  /*! \fn: getDataDespac
  *  \brief: Metodo para retornar la información del despacho
  *  \author: Ing. Cristian Andrés Torres
  *  \date: 02/12/2022   
  *  \return cResponxxx : array
  */
  public function getDataDespac()
  {
      # Consulta los datos basicos del despacho ---------------------------------------------------------------------------
      $mQueryDespac = "SELECT a.num_despac, c.nom_tercer as 'nom_conduc', c.nom_apell1 as 'ap11_conduc', c.nom_apell2 as 'ap12_conduc',
                        d.nom_ciudad as 'nom_ciuori', e.nom_ciudad as 'nom_ciudes', c.num_telef1, c.num_telef2, c.num_telmov,
                        b.num_placax, f.nom_noveda, g.abr_tercer
                        FROM " . BASE_DATOS . ".tab_despac_despac a
                    INNER JOIN " . BASE_DATOS . ".tab_despac_vehige b ON a.num_despac = b.num_despac
                    INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer c ON b.cod_conduc = c.cod_tercer
                    INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad d ON a.cod_ciuori = d.cod_ciudad AND a.cod_depori = d.cod_depart AND a.cod_paiori = d.cod_paisxx
                    INNER JOIN " . BASE_DATOS . ".tab_genera_ciudad e ON a.cod_ciudes = e.cod_ciudad AND a.cod_depdes = e.cod_depart AND a.cod_paides = e.cod_paisxx
                    INNER JOIN " . BASE_DATOS . ".tab_genera_noveda f ON f.cod_noveda = '".self::$cCodNoveda."'
                    INNER JOIN " . BASE_DATOS . ".tab_tercer_tercer g ON b.cod_transp = g.cod_tercer
                        WHERE a.num_despac = '".self::$cNumDespac."'
                        LIMIT 1 ";
      self::$cConnectxx -> ExecuteCons( $mQueryDespac );
      $mDataDespac = self::$cConnectxx -> RetMatrix( "a" );         
      return $mDataDespac[0];
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