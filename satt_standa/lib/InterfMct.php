<?php

#ini_set('display_errors', true);
#error_reporting(E_ALL & ~E_NOTICE);

  /*! \Class: InterfS3Amazon
  *  \brief: Clase encargada de hacer la respectiva conexion a servidor S3 de Amazon
  *  \author: Ing. Nelson Liberato
  *  \date: 09/06/2015   
  *  \param: $cBucket  -  Nombre del bucket
  *  \param: $cUserxx  -  NOmbre usuario de conexion al bucket
  *  \param: $cAcckey  -  Llave publica
  *  \param: $cPrikey  -  Llave Privada
  *  \param: $cConexion  -  Variable de la conexion para la clase en general
  *  \param: $cMsgError  -  Variable de mensaje en caso de error
  *  \return array
  */
  class InterfMctPhp
  {
     
    function __construct(  )
    {  
         
       

        $mDataMCT = array(
                          'manifiesto_codigo' => "0115036271",            
                          'ptoc_codigo' => "19150",
                          'ptoc_nombre' => "EAL GRANADA",
                          'ptoc_fecha' => "2015-08-03 08:00:00",
                          'ptoc_observacion' => "Prueba de novedad NO Framework, Script Aparte OET",
                        );               

        include("InterfMct.inc");
        $mMct = new InterfMct($conexion, $mDataMCT);
        echo "<pre>Data Response:<br>"; print_r($mMct -> getResponMct() ); echo "</pre>";
    }
 
  }

  $S3Amazon = new InterfMctPhp();

?>