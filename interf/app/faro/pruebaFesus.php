<?php

try {

  $mParam = array(
                    "nom_usuari" => "soporte",
                    "pwd_clavex" => "avansatgl",
                    "cod_tranps" => "830075219",
                    "cod_rutasx" => "806"
                  );


  
 
        // SOAP 1.2 client
        $params = array ('encoding' => 'UTF-8', 'verifypeer' => false, 'verifyhost' => false, 'soap_version' => SOAP_1_1, 'trace' => 1, 'exceptions' => 1, "connection_timeout" => 180);




  
  $mSoap = new SoapClient("https://avansatgl.intrared.net/ap/interf/app/faro/wsdl/faro2.wsdl", $params );
  $mResult = $mSoap -> __soapCall("getHomoloData", $mParam);

  $XML = (string)$mSoap -> __getLastResponse();
  echo "<pre>xml respnse<br>"; print_r( htmlspecialchars( $XML ) ); echo "</pre>";

  echo "<pre>Result<br>"; print_r($mResult); echo "</pre>";

  $mXmlSimple = new SimpleXMLElement( $XML);
    echo "<pre>ResultXmlSimple<br>"; print_r($mXmlSimple); echo "</pre>";




    $sxe = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.example.org/server/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"><SOAP-ENV:Body><ns1:getHomoloDataResponse><response xsi:type="ns1:setRutaFaro"><cod_rutfar xsi:type="xsd:string">ddd</cod_rutfar><cod_rutbas xsi:type="xsd:string">sss</cod_rutbas><arr_homolo SOAP-ENC:arrayType="ns1:HomoloData[20]" xsi:type="ns1:arrayHomoloData"><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">29</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10029</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">30</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10025</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">31</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10027</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">38</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10024</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">44</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10020</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">45</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10022</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">57</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10007</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">68</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10019</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">69</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10021</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">72</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10023</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">76</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10026</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">80</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10028</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">94</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10000</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">97</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10003</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">108</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10001</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">176</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10002</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">177</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10004</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">178</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10005</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">180</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10006</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item><item xsi:type="ns1:HomoloData"><cod_pcxfar xsi:type="xsd:string">9999</cod_pcxfar><cod_pcxbas xsi:type="xsd:string">10010</cod_pcxbas><ind_estado xsi:type="xsd:string">1</ind_estado></item></arr_homolo><cod_respon xsi:type="xsd:string">1000</cod_respon><msg_respon xsi:type="xsd:string">Se retorno la informacion de 20 puestos satisfactoriamente</msg_respon></response></ns1:getHomoloDataResponse></SOAP-ENV:Body></SOAP-ENV:Envelope>';


    function xml2array($contents, $get_attributes = 1, $priority = 'tag')
    {
        if (!$contents) return array();
        if (!function_exists('xml_parser_create')) {
            // print "'xml_parser_create()' function not found!";
            return array();
        }
        // Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); // http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents) , $xml_values);
        xml_parser_free($parser);
        if (!$xml_values) return; //Hmm...
        // Initializations
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();
        $current = & $xml_array; //Refference
        // Go through the tags.
        $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
        foreach($xml_values as $data) {
            unset($attributes, $value); //Remove existing values, or there will be trouble
            // This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data); //We could use the array by itself, but this cooler.
            $result = array();
            $attributes_data = array();
            if (isset($value)) {
                if ($priority == 'tag') $result = $value;
                else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }
            // Set the attributes too.
            if (isset($attributes) and $get_attributes) {
                foreach($attributes as $attr => $val) {                                   
                                    if ( $attr == 'ResStatus' ) {
                                        $current[$attr][] = $val;
                                    }
                    if ($priority == 'tag') $attributes_data[$attr] = $val;
                    else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }
            // See tag status and do the needed.
                        //echo"<br/> Type:".$type;
            if ($type == "open") { //The starting of the tag '<tag>'
                $parent[$level - 1] = & $current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if ($attributes_data) $current[$tag . '_attr'] = $attributes_data;
                                        //print_r($current[$tag . '_attr']);
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    $current = & $current[$tag];
                }
                else { //There was another element with the same tag name
                    if (isset($current[$tag][0])) { //If there is a 0th element it is already an array
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                    else { //This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        ); //This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag . '_' . $level] = 2;
                        if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = & $current[$tag][$last_item_index];
                }
            }
            elseif ($type == "complete") { //Tags that ends in 1 line '<tag />'
                // See if the key is already taken.
                if (!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data) $current[$tag . '_attr'] = $attributes_data;
                }
                else { //If taken, put all things inside a list(array)
                    if (isset($current[$tag][0]) and is_array($current[$tag])) { //If it is already an array...
                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                    else { //If it is not an array...
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        ); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }
                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }
            }
            elseif ($type == 'close') { //End of tag '</tag>'
                $current = & $parent[$level - 1];
            }
        }
        return ($xml_array);
    }

    // Let's call the this above function xml2array

    $mjdjdjdj = xml2array($sxe, $get_attributes = 3, $priority = 'tag'); // it will work 100% if not ping me @skype: sapan.mohannty

    echo "<pre>mjdjdjdj<br>"; print_r($mjdjdjdj); echo "</pre>";

//  Enjoy coding

 

 
} catch (Exception $e) {
  echo "<pre>Catch<br>"; print_r($e); echo "</pre>";
}

?>