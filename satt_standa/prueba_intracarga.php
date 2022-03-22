<?php

    echo "<h2>Inicio Prueba PHP Intracarga</h2><br>";


    # @ Inicia Tratamiento Soap -----------------------------------------------------------------------------
    

      try
      {
        
          ini_set( "soap.wsdl_cache_enabled", "0" );

          $mParams = array(
                "nom_usuari" => "InterfIncarga",
                "pwd_clavex" => "13_trac4drga",
                "cod_tranps" => "802017639",
                "cod_manifi" => "94500055551",
                "dat_fechax" => "2014-11-28 14:40",
                "cod_ciuori" => "8001000",
                "cod_ciudes" => "13001000",
                "cod_placax" => "TAX050",
                "num_modelo" => "2013",
                "cod_marcax" => "366",
                "cod_lineax" => "8",
                "cod_colorx" => "53",
                "cod_conduc" => "13894576",
                "nom_conduc" => "Nelson liberato",
                "ciu_conduc" => "68307000",
                "tel_conduc" => "3142502585",
                "mov_conduc" => "3138880593",
                "obs_coment" => "EL VEHICULO SE ENCUENTRA CARGADO QUEDA EN EL PARQUEADERO DE EL PALENQ",
                "cod_rutaxx" => "0",
                "nom_rutaxx" => "Barranquilla Cartagena - Nelson Prueba",
                "ind_naturb" => "1",
                "num_config" => "50",
                "cod_carroc" => "213",
                "num_chasis" => "LJ11KFBD2D1000711",
                "num_motorx" => "87327166",
                "num_soatxx" => "1309119707486",
                "dat_vigsoa" => "2015-01-02",
                "nom_ciasoa" => "QBE SEGUROS S.A.",
                "num_tarpro" => "",
                "num_trayle" => "R00001",
                "cat_licenc" => "C2",
                "dir_conduc" => "BARRIO EL PALENQUE",
                "cod_poseed" => "63456508",
                "nom_poseed" => "ALBA CECILIA HERRERA MERLANO",
                "ciu_poseed" => "68276000",
                "dir_poseed" => "MIRADOR DE FATIMA CASA 21",
               "cod_agedes" => "",
               "cod_contrs" => array(0=> array("cod_contro" => "236",
                                               "val_duraci" => "20",
                                               "ind_virtua" => ""
                                               ) 
                                    )
                      );
          echo "<pre> Parametros Enviados:<br>"; print_r($mParams); echo "</pre>";

          $oSoapClient = new soapclient( 'https://ap.intrared.net:444/ap/interf/app/faro/wsdl/faro.wsdl', array( "trace" => "1", 'encoding'=>'ISO-8859-1' ) );
          $result = $oSoapClient -> __call( "setSeguim", $mParams );  
          
          echo "<pre> Retorno WebService setSeguim:<br>"; print_r($result); echo "</pre>";
      }
      catch( Exception $e )
      {
          echo "<pre>"; print_r( $e -> getMessage()); echo "</pre>";
      }
    
          


    echo "<br/>Fin Prueba";

?>