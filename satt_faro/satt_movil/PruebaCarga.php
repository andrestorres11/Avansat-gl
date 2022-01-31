<?php

    try 
    {
        $dataCargaAn['url_webser'] = "https://api.carga.com.co:44324/Reportes/ReportesWS.asmx?wsdl";
        
            echo "<pre><hr>"; print_r( '---------- CONSUMO SIN SALTAR SSL DE LA URL , LA SSL NO DEJA CONSUMIR LA URL-----------' );  echo "</pre>";

            $mTextXML =  xmlCarga();
 
            echo "<pre><hr>"; print_r(htmlspecialchars($mTextXML)); echo "</pre>";  
 

            $s = curl_init();
            curl_setopt($s,CURLOPT_URL, $dataCargaAn['url_webser']);
            
            curl_setopt($s,CURLOPT_HTTPHEADER,array('Content-Type: text/xml')); 
            curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($s,CURLOPT_POST,true);
            curl_setopt($s,CURLOPT_POSTFIELDS,$mTextXML);
            $mResponse   = curl_exec($s);
            $mHttpStatus = curl_getinfo($s,CURLINFO_HTTP_CODE);
            $curl_error = curl_error($s);
            curl_close($s);

            echo "<pre>url_webser: "; print_r($dataCargaAn['url_webser']); echo "</pre>";  
            echo "<pre>mHttpStatus: "; print_r($mHttpStatus); echo "</pre>";  
            echo "<pre>curl_error: <span style='color:red;'>"; print_r($curl_error); echo "<span></pre>";  
            echo "<pre>mResponse: "; print_r( var_dump($mResponse) ); echo "</pre>";  

            // ------------------------------------------------------------------------------------------------------------------------------------------------------
            // ------------------------------------------------------------------------------------------------------------------------------------------------------
            // ------------------------------------------------------------------------------------------------------------------------------------------------------
            // ------------------------------------------------------------------------------------------------------------------------------------------------------


            echo "<pre><hr>"; print_r( '---------- CONSUMO SALTANDO LA SSL DE LA URL , SI CONSUME LA URL SALTANDO LA VALIDACION DE SSL -----------' );  echo "</pre>";

             
        
            /*
            $mTextXML =  xmlOet();
 
            echo "<pre><hr>"; print_r(htmlspecialchars($mTextXML)); echo "</pre>";  
 

            $s = curl_init();
            curl_setopt($s,CURLOPT_URL, $dataCargaAn['url_webser']);
            
            curl_setopt($s,CURLOPT_HTTPHEADER,array('Content-Type: text/xml')); 
            curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($s,CURLOPT_POST,true);
            curl_setopt($s,CURLOPT_POSTFIELDS,$mTextXML);


            // curl_setopt ($s, CURLOPT_SSL_VERIFYHOST, 0); // ****** ACA SALTA LA SSL CON EL CURL *********
            // curl_setopt ($s, CURLOPT_SSL_VERIFYPEER, 0); // ****** ACA SALTA LA SSL CON EL CURL *********


            $mResponse   = curl_exec($s);
            $mHttpStatus = curl_getinfo($s,CURLINFO_HTTP_CODE);
            $curl_error = curl_error($s);
            curl_close($s);

            echo "<pre>url_webser: "; print_r($dataCargaAn['url_webser']); echo "</pre>";  
            echo "<pre>mHttpStatus: "; print_r($mHttpStatus); echo "</pre>";  
            echo "<pre>curl_error: "; print_r($curl_error); echo "</pre>";  
            echo "<pre>mResponse: "; print_r( var_dump($mResponse) ); echo "</pre>";  
            */

    } 
    catch (Exception $e) 
    {
        echo "<pre>Exception<span style='color: red;'>"; print_r($e); echo "</span></pre>";  
    }


    function xmlCarga()
    {
        return '<?xml version="1.0" encoding="utf-8"?>
                <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://carga.local/">
                    <SOAP-ENV:Header>
                        <ns1:Credenciales>
                            <ns1:Username>OET_Int</ns1:Username>
                            <ns1:Password>Carg@OET_5A015WTWR</ns1:Password>
                        </ns1:Credenciales>
                    </SOAP-ENV:Header>
                    <SOAP-ENV:Body>
                        <ns1:IngresarReporte>
                            <ns1:reporte>
                                <ns1:Manifiesto>1-3-15-146491</ns1:Manifiesto>
                                <ns1:Placa>XID-378</ns1:Placa>
                                <ns1:CodigoPuestoControlOET>14585</ns1:CodigoPuestoControlOET>
                                <ns1:FechaNovedad>2021-12-08 06:54:00</ns1:FechaNovedad>
                                <ns1:Observacion>REPORTA SIN NOVEDAD A LAS 6:09</ns1:Observacion>
                                <ns1:Lugar></ns1:Lugar>
                                <ns1:Sitio>1</ns1:Sitio>
                            </ns1:reporte>
                        </ns1:IngresarReporte>
                    </SOAP-ENV:Body>
                </SOAP-ENV:Envelope> ';
    }    

    function xmlOet()
    {
        return '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://carga.local/">
                    <SOAP-ENV:Header>
                        <ns1:Credenciales>
                            <ns1:Username>OET_Int</ns1:Username>
                            <ns1:Password>Carg@OET_5A015WTWR</ns1:Password>
                        </ns1:Credenciales>
                    </SOAP-ENV:Header>
                    <SOAP-ENV:Body>
                        <ns1:IngresarReporte>
                            <ns1:reporte>
                                <ns1:Manifiesto>1-3-55-155738</ns1:Manifiesto>
                                <ns1:Placa>TMV-617</ns1:Placa>
                                <ns1:CodigoPuestoControlOET>19150</ns1:CodigoPuestoControlOET>
                                <ns1:FechaNovedad>2021-10-21 16:10:37</ns1:FechaNovedad>
                                <ns1:Observacion>'.strip_tags('Registrado desde <br>Dispositivo Movil win32. <br>IP:186.148.188.59').'</ns1:Observacion>
                                <ns1:Lugar>OAL GRANADA</ns1:Lugar>
                                <ns1:Sitio>1</ns1:Sitio>
                            </ns1:reporte>
                        </ns1:IngresarReporte>
                    </SOAP-ENV:Body>
                </SOAP-ENV:Envelope>';
    }

?>