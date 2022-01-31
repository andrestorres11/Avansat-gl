<?php
    ini_set('display_errors', true);
    error_reporting(E_ALL & ~E_NOTICE);


    echo "<h2>Inicio Prueba PHP 5</h2><br>";
    //require_once("/var/www/html4/ap/satt_standa/lib/nusoap095/nusoap.php");
    $mNumDespac = "1081110";
    $regist["contro"] = "94";
    $regist["noveda"] = "52";
    $regist["observ"] = "1- PRUEBA";
    
    
    

    #Conexion a BD
    $link = mysql_connect("localhost", "usr_consul", "U4fddM9kdmyMayo");
    mysql_select_db("satt_faro");
     
    # Datos de autenticacion
    $query = "SELECT a.cod_operad, a.nom_operad, a.nom_usuari, a.clv_usuari, a.val_timtra, a.url_webser  
               FROM satt_faro.tab_interf_parame a  
              WHERE a.ind_operad = '0' AND 
                    a.ind_estado = '1' AND 
                    a.cod_transp = '800148973' ";
    $mDataLoguin = RetMatriz($query, $link );

     

    $query = "SELECT  a.cod_manifi, b.num_placax, a.cod_tipdes  
                FROM  satt_faro.tab_despac_despac a,  
                      satt_faro.tab_despac_vehige b  
               WHERE a.num_despac = b.num_despac AND   
                     a.num_despac = '" . $mNumDespac . "' ";
    $mSalida = RetMatriz($query, $link );
     

    
    # Fecha Reporte
    $mFecha = explode(" ", "2014-09-16 11:04:25");                
    
    #Puestos de control Novedad o reporte     
    $query = "SELECT  a.cod_contro
                FROM satt_faro.tab_homolo_pcxeal a 
               WHERE a.cod_homolo = '".$regist["contro"]."'
                 OR a.cod_contro = '".$regist["contro"]."'";
    
    $mControPadre = RetMatriz($query, $link );
    if( !$mControPadre )
      $mControPadre["cod_contro"] = $regist["contro"];
                
    // Consulta Nombre PC
    $query = "SELECT  a.nom_contro, IF(a.ind_virtua = 1 ,NULL,a.cod_contro) AS cod_puecon
                FROM satt_faro.tab_genera_contro a 
               WHERE a.cod_contro = '".$mControPadre["cod_contro"]."'";     
    $mNomContro  = RetMatriz($query, $link );

    # Nombre de la novedad     
    $query = "SELECT  a.nom_noveda
                FROM satt_faro.tab_genera_noveda a 
               WHERE a.cod_noveda = '".$regist["noveda"]."'";    
    $mNomNoveda = RetMatriz($query, $link );
     


     

    /*
    $parametros = array();
    $parametros["usuario"]=$mDataLoguin["nom_usuari"];
    $parametros["clave"]=$mDataLoguin["clv_usuari"];
    $parametros["planilla"] = $mSalida["cod_manifi"]; //$regist["despac"];
    $parametros["placa"] =$mSalida["num_placax"];
    $parametros["ind_naturaleza"] = (int)$mSalida["cod_tipdes"];
    $parametros["fecha_reporte"]= $mFecha[0];
    $parametros["hora_reporte"] = $mFecha[1];
    $parametros["cod_puesto"] = (int)$mNomContro["cod_puecon"];
    $parametros["nom_punto"] = $mNomContro["nom_contro"];
    $parametros["cod_novedad"] = (int)$regist["noveda"];
    $parametros["novedad"] =$mNomNoveda["nom_noveda"];
    $parametros["observacion"] = $regist["observ"]; */

    $parametros["usuario"]         = $mDataLoguin["nom_usuari"];           
    $parametros["clave"]           = $mDataLoguin["clv_usuari"];        
    $parametros["planilla"]        = "240199" ;
    $parametros["placa"]           = "WCO586";        
    $parametros["ind_naturaleza"]  = 1;       
    $parametros["fecha_reporte"]   = "2014-09-17";
    $parametros["hora_reporte"]    = "10:21:00";
    $parametros["cod_puesto"]      = 0;       
    $parametros["nom_punto"]       = "SITIO DE CARGUE"; 
    $parametros["cod_novedad"]     = 68;
    $parametros["novedad"]         = "COMENTARIO";      
    $parametros["observacion"]     = "1> PRUEBA";

    echo "<pre>";
      print_r( $parametros );
    echo "</pre>";
    
    $array = var_export($parametros, true);
     
     echo  $array;
     die();
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