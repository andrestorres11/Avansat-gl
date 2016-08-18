<?php
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);


    echo "<h2>Inicio Prueba PHP 5</h2><br>";
    //require_once("/var/www/html4/ap/satt_standa/lib/nusoap095/nusoap.php");
 

    #Conexion a BD
    $link = mysql_connect("localhost", "usr_consul", "U4fddM9kdmyMayo");
    mysql_select_db("satt_faro");
     
    # Datos de autenticacion
    $query = "SELECT a.cod_operad, a.nom_operad, a.nom_usuari, a.clv_usuari, a.val_timtra, a.url_webser  
               FROM satt_faro.tab_interf_parame a  
              WHERE a.ind_operad = '0' AND 
                    a.ind_estado = '1' AND 
                    a.cod_transp = '830101959' ";
    $mDataLoguin = RetMatriz($query, $link );

 
    $parametros = array();
    $parametros["usuario"]="faro";
    $parametros["clave"]="F4r0=s4t!";
    $parametros["planilla"] = "13936"; //$regist["despac"];
    $parametros["placa"] ="TKE708";
    $parametros["ind_naturaleza"] = "1";
    $parametros["fecha_reporte"]= "2014-09-16";
    $parametros["hora_reporte"] = "15:53:00";
    $parametros["cod_puesto"] = 0;
    $parametros["nom_punto"] = "SITIO DE CARGUE";
    $parametros["cod_novedad"] = 69;
    $parametros["novedad"] ="Inicia Pernoctación Extensa";
    $parametros["observacion"] = "OK. Tiempo Generado: 785 Minutos  . Se Envio Correos a : oscar@gmtcarga.com,traficoyseguridad@gmtcarga.com";
    echo "<pre>";
      print_r( $parametros );
    echo "</pre>";
    
    
    $oSoapClient = new SoapClient($mDataLoguin["url_webser"],array('encoding'=>'ISO-8859-1') );
    $respuesta = $oSoapClient -> __call( 'set_Novedad', $parametros );
    echo "<hr>";
    print_r( $respuesta );
    
    echo "<hr><pre>";
       print_r($oSoapClient->request);
    echo "</pre>";
   
    
    if ( $oSoapClient->fault ) {
      echo '<h2>Falla</h2><pre>';
        print_r($respuesta);
      echo '</pre>';
    }
    
    echo "<br/>Fin Prueba";
    

    
    
    mysql_close($link);

    function RetMatriz( $mQuery = NULL, $mConection = NULL)
    {
      //echo $mQuery."<br>";
      $mMatrix = array();
      $mResult = mysql_query($mQuery, $mConection);
      while ($mRow = mysql_fetch_assoc($mResult)) {
         $mMatrix = $mRow;
      }

      
      return $mMatrix;
    }



?>