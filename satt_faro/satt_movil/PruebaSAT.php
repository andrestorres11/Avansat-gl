<?php
try {
            $url_webser = 'https://avansat4.intrared.net:8083/ap/interf/app/sat/wsdl/sat.wsdl';
            $url_webser = 'https://avansat5.intrared.net:8083/ap/interf/app/sat/wsdl/sat.wsdl';

            $url_webser = 'https://avansat3.intrared.net:8083/ap/interf/app/sat/wsdl/sat.wsdl';

            $parametros = array("nom_usuari" => $data['nom_usuari'],
                            "pwd_clavex" => $data['clv_usuari'],
                            "nom_aplica" => $data['nom_operad'],
                            "num_manifi" => $_POST[$i]["num_manifi"],
                            "num_placax" => $_POST["placa"],
                            "cod_novbas" => 0,
                            "cod_conbas" => $mCodPcxbas[0]['cod_pcxbas'],
                            "tim_duraci" => $mParams["tim_duraci"],
                            "fec_noveda" => date('Y-m-d H:i', strtotime($mParams["fec_noveda"])),
                            "des_noveda" => $mParams["des_noveda"],
                            "nom_contro" => $mNomPc['nom_contro'],
                            "nom_sitiox" => substr($mNomPc['nom_contro'], 0, 50),
                            "cod_confar" => NULL,
                            'cod_novfar' => $mParams['cod_noveda'],
                            'nom_noveda' => $mNomNov['nom_noveda'],
                            'ind_alarma' => $mNomNov['ind_alarma'],
                            'ind_tiempo' => $mNomNov['ind_tiempo'],
                            'nov_especi_' => $mNomNov['nov_especi'],
                            'ind_manala' => $mNomNov['ind_manala'],
                            'bin_fotcon' => base64_encode($_REQUEST["img_foto01"]),
                            'bin_fotpre' => base64_encode($_REQUEST["img_foto02"])
                        );


            
          $oSoapClient = new soapclient($url_webser, array(
                                                     "trace" => 1,
                                                     'encoding' => 'ISO-8859-1',
                                                     "stream_context" => stream_context_create(
                                                         array(
                                                             'ssl' => array(
                                                                 'verify_peer' => false,
                                                                 'allow_self_signed' => true,
                                                             ),
                                                         )
                                                     ),
                                                 ));
          $respuesta = $oSoapClient->__call("setNovedadPC", $parametros);

          echo "<pre>Respuesta Normal <br>"; print_r($respuesta); echo "</pre>";


} catch (Exception $e) {
  echo "<pre>Catch<br>"; print_r($e); echo "</pre>";
}


?>