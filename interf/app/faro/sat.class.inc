<?php

/*! \Class: SAT
*  \brief: Clase de para comunicación con un avansat
*  \author: Ing. Cristian Torres
*  \date: 31/03/2023   
*  \param: mConection  -  Variable de clase que almacena la conexion de la Base de datos
*  \param: mNomUsuari - Uusuario conexion BD
*  \return array
*/
class Sat  
{
 
  private static $cNomUsuari = NULL;
  private static $cConnectxx = NULL;
 

  /*! \fn: __construct
  *  \brief: Metodo principal para la Clase - Constructor
  *  \author: Ing. Cristian Torres
  *  \date: 02/12/2015   
  *  \param: mConection : Conexion de base de datos 
  *  \param: mNomUsuari : Usuario de autenticación
  */
  function __construct( $mConection = NULL, $mNomUsuari = NULL)
  {
      self::$cConnectxx = $mConection;
      self::$cNomUsuari = $mNomUsuari;
  }


  public function getInterfaceSAT($mCode, $mCodTransp)
  {
      # Consulta los datos basicos del despacho ---------------------------------------------------------------------------
      $mQueryInterface = "SELECT a.nom_operad, a.nom_usuari, a.clv_usuari,
                                 c.nom_server, c.url_webser
                        FROM 
                              " . BASE_DATOS . ".tab_interf_parame a
                   INNER JOIN " . BASE_DATOS . ".tab_transp_tipser b ON a.cod_transp = b.cod_transp AND b.num_consec = (
                                SELECT MAX(bb.num_consec) AS num_consec
                                  FROM " . BASE_DATOS . ".tab_transp_tipser bb
                                 WHERE    bb.cod_transp = '".$mCodTransp."'
                             )
                  INNER JOIN " . BD_STANDA . ".tab_genera_server  c  ON b.cod_server = c.cod_server
                        WHERE a.cod_operad = '".$mCode."' AND
                              a.cod_transp = '".$mCodTransp."' AND
                              a.ind_estado = 1
                        LIMIT 1 ";
      self::$cConnectxx -> ExecuteCons( $mQueryInterface );
      $mDataInterface= self::$cConnectxx -> RetMatrix( "a" );         
      return $mDataInterface[0];
  }

  public function setInfoItinerary($url_webser, $mParams){
    ini_set("soap.wsdl_cache_enabled", 0);
    $mSoap = new SoapClient( $url_webser, ['trace' => 1,'exception' => 1 ] );
    $mResponse = $mSoap -> __soapCall("setStatusItinerary", $mParams);
    return $mResponse['cod_respon'];
  }
  
}


?>