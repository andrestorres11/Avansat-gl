<?php

class Proc_despac
{
 var $conexion,
     $cod_aplica,
     $usuario;

 function __construct($co, $us, $ca)
 {
  $this -> conexion = $co;
  $this -> usuario = $us;
  $this -> cod_aplica = $ca;
  $this -> principal();
 }

 function principal()
 {
  if(!isset($_REQUEST[opcion]))
    $this -> Listar();
  else
     {
      switch($_REQUEST[opcion])
       {
        case "1":
          $this -> Formulario();
          break;
        case "2":
          $this -> Insertar();
        break;
        case "3":
          $this -> BuscaDirecto();
        break;
       
        default:
          $this -> Listar();
          break;
       }//FIN SWITCH
     }// FIN ELSE GLOBALS OPCION
 }//FIN FUNCION PRINCIPAL

  function Listar()
  { 
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/despac_gps.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
      include_once( "/var/www/html/ap/interf/lib/nusoap5/lib/nusoap.php" ); // Libreria NuSoap para Tracker
    
    
    $formulario = new Formulario ("index.php","post","","form_ins");    
    // -------------------------------------------------------------------------------------
    $formulario -> nueva_tabla();
    $formulario -> linea("Consultar última ubicación Vehiculo",1,"t2");
    
    $formulario -> nueva_tabla();
    $formulario -> texto ("Despacho:","text","num_despac\" id=\"num_despacID\"  onclick=\"cambio('num_placasID', 'cod_manifiID');\" onkeypress=\"return TextInputNumeric( event )\"",0,50,10,"",$_REQUEST['num_despac']);    
    $formulario -> texto ("Manfiesto:","text","cod_manifi\" id=\"cod_manifiID\" onclick=\"cambio('num_despacID', 'num_placasID');\" onkeypress=\"return TextInputNumeric( event )\"",0,50,10,"",$_POST['cod_manifi']);    
    $formulario -> texto ("Placa:","text","num_placas\" id=\"num_placasID\"     onclick=\"cambio('num_despacID', 'cod_manifiID');\"",0,50,10,"",$_POST['num_placas']);    
   
    // -------------------------------------------------------------------------------------
    $mReset = '0';
    if($_POST['num_despac'] != '' || $_POST['num_placas'] != '' || $_POST['cod_manifi'] != ''  )
    { 
      $mReset = '1';
     if(  $this -> getExistDespac ( $_POST['num_despac'] , $_POST['num_placas'], $_POST['cod_manifi']) !== true)       
        echo "<script>alert('El Despacho no Existe'); location.href='?window=central&cod_servic=".$_REQUEST[cod_servic]."';</script>";     
      else
      {
        $mFlag = false;      
        if( $_POST['num_placas'] != NULL )
        {
          //Para placas
          $mDataDespa = $this -> getDataPlaca ( $_POST ); // Busca si ya no está en ruta 
          if( !$mDataDespa )          
            echo "<script>alert('La placa ".$_POST['num_placas']." Ya no está en ruta.'); location.href='?window=central&cod_servic=".$_REQUEST[cod_servic]."';</script>"; 
          else
          {
            $_POST['num_despac'] = $mDataDespa;
            $mDataGPS = $this -> getDataGps ( $_POST ); // datos GPS
            if( $mDataGPS == NULL)
            {
              echo "<script>alert('El despacho ".$_POST['num_despac']." no tiene datos GPS'); location.href='?window=central&cod_servic=".$_REQUEST[cod_servic]."&opcion=2&num_despac=".$_POST['num_despac']."';</script>";    
            }
          }
        }
        else if($_POST['num_despac'] != NULL)
        {
          //Para despachos
          $mDataGPS = $this -> getDataGps ( $_POST ); // datos GPS
          if( $mDataGPS == NULL)
          {
            echo "<script>alert('El despacho ".$_POST['num_despac']." no tiene datos GPS'); location.href='?window=central&cod_servic=".$_REQUEST[cod_servic]."&opcion=2&num_despac=".$_POST['num_despac']."';</script>";    
          }
        }
        else if($_POST['cod_manifi'] != NULL)
        {
          $mDataDespa = $this -> getDataPlaca ( $_POST ); // Busca por numero de manifiesto
          if( !$mDataDespa )          
            echo "<script>alert('El Manifiesto ".$_POST['cod_manifi']." Ya no está en ruta.'); location.href='?window=central&cod_servic=".$_REQUEST[cod_servic]."';</script>"; 
          else
          {
            $_POST['num_despac'] = $mDataDespa;
            $mDataGPS = $this -> getDataGps ( $_POST ); // datos GPS
            if( $mDataGPS == NULL)
            {
              echo "<script>alert('El despacho ".$_POST['num_despac']." no tiene datos GPS'); location.href='?window=central&cod_servic=".$_REQUEST[cod_servic]."';</script>";    
            }
          }
        }
       
      }
      
      $formulario -> nueva_tabla();
          $formulario -> linea("Datos GPS",1,"t2");
      // Div para mostrar mensaje actualización de datos GPS del despacho ( AJAX)
      echo "</table>";    
          echo "<div id='DataDespacGpsID' >";
            echo "<table>";
            
           echo "</table>";
          echo "</div>";
          
          $formulario -> nueva_tabla();
            //($te, $sl, $est = "", $an = 0, $col = 0, $al = "left", $color = NULL)
            $formulario -> linea("Despacho", 0, "t2",null,null, 'right');
            $formulario -> linea("<a href='?cod_servic=3302&window=central&despac=".$_POST['num_despac']."&opcion=1' target='blank'>".$_POST['num_despac']."</a>", 1, "i");
            $formulario -> texto ("Vehículo:","text","num_placax\" id=\"num_placaxID\"",0,50,6,"",$mDataGPS['num_placax']);  
           
            $formulario -> lista ("Operador:","cod_operad\" id=\"cod_operadID\" ", $this -> getOperadGps( $mDataGPS , true), 1);           
            $formulario -> texto ("Usuario:","text","nom_usrgps\" id=\"nom_usrgpsID\"",0,50,20,"",$mDataGPS['usr_gpsxxx']);    
            
            $formulario -> texto ("Clave:","text","clv_usrgps\" id=\"clv_usrgpsID\"",1,50,20,"",base64_decode($mDataGPS['clv_gpsxxx']));    
            $formulario -> texto ("ID:","text","idx_gpsxxx\" id=\"idx_gpsxxxID\"",0,50,20,"",$mDataGPS['idx_gpsxxx']);   
            
            $formulario -> botoni("Guardar","SaveDataGps('DataDespacGpsID')",0);   
            $formulario -> oculto("num_despacH\" id=\"num_despacHID\"",$_POST['num_despac'],0);
            $formulario -> oculto("Case\" id=\"CaseID\"","Update_Data_Despac_GPS",0);
       
       // Div para ocultar las operadoras GPS cuando se resetea todo el formulario 
        echo "</table>";    
          echo "<div id='inf_gpsdes' style='display:none'>";
            echo "<table>";
              $formulario -> nueva_tabla();
              $formulario -> lista ("Operador:","cod_operadHidden\" id=\"cod_operadHiddenID\" ", $this -> getOperadGps( $mDataGPS , false), "t2");
              $formulario -> lista ("Operador:","cod_operadHiddenReq\" id=\"cod_operadHiddenReqID\" ", $this -> getOperadGpsReq( $mDataGPS ), "t2");
            echo "</table>";
          echo "</div>";
      
      //----------------------------------------------------------------------------------------------------------------------------------------------------------------
      $formulario -> nueva_tabla();  
        $formulario -> linea("Ubicación Vehículo",1,"t2");      
      
      $mUbicateDespac = $this -> getUbicaDespac( $mDataGPS , $_POST);
      
      if( $mUbicateDespac[0][error] !== true )
      {
        if($mFlag == true)
        {
          $mens = new mensajes();
          $mensaje = "<table>
                      <tr><td>Error(es) Encontrados:</td></tr>
                      <tr><td><span style='color:red'>El despacho: ".$_POST['num_despac']."</span></td></tr>
                      <tr><td><span style='color:red'>ya no está en ruta</span></td></tr>
                      </table>";
          $mens -> error("Error Operador GPS - ".$mDataGPS['nom_operad'],$mensaje);
        }
        else
        {
          $mens = new mensajes();
          $mensaje = "<table>
                      <tr><td>Error(es) Encontrados:</td></tr>
                      <tr><td><span style='color:red'>".utf8_decode($mUbicateDespac[0][error])."</span></td></tr>
                      </table>";
          $mens -> error("Error Operador GPS - ".$mDataGPS['nom_operad'],$mensaje);
        }
        
      }
      else
      {
        //-------------------------------------------------------------------------------
        $formulario -> nueva_tabla();  
        $formulario -> linea("Fecha:", 0, "t");
        $formulario -> linea("Velocidad:", 0, "t");
        $formulario -> linea("Longitud:", 0, "t");
        $formulario -> linea("Latitud:", 0, "t");
        $formulario -> linea("ubicacion:", 1, "t");
        //------------------------------------------------------------------------------
        $formulario -> linea($mUbicateDespac[1]['fec_noveda'], 0, "i");
        $formulario -> linea($mUbicateDespac[1]['val_veloci'], 0, "i");
        $formulario -> linea($mUbicateDespac[1]['val_longit'], 0, "i");
        $formulario -> linea($mUbicateDespac[1]['val_latitu'], 0, "i");
        $formulario -> linea($mUbicateDespac[1]['det_ubicac'], 0, "i");
      }
      
    }
    
    $formulario -> nueva_tabla();
    $formulario -> oculto("reset\" id=\"resetID\"",$mReset,0);
    $formulario -> oculto("window","central",0);
    $formulario -> oculto("cod_servic\" id=\"cod_servicID\"",$_REQUEST[cod_servic],0);
    $formulario -> oculto("opcion\" id=\"opcionID\"",NULL,0);
    $formulario -> oculto("fStandar\" id=\"fStandarID\"",DIR_APLICA_CENTRAL,0);
    // -------------------------------------------------------------------------------------
    $formulario -> nueva_tabla();
      $formulario -> botoni("Buscar","buscar_despac()",0);     
      //$formulario -> botoni("Ingresar","document.location='?window=central&cod_servic=".$_REQUEST[cod_servic]."&opcion=3'",0);     
    if($_POST['num_despac'] != '')      
      $formulario -> botoni("Volver","document.location='?window=central&cod_servic=".$_REQUEST[cod_servic]."'",1);   
   
    $formulario -> cerrar();    
  }
  
  function Insertar()
  {
     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/despac_gps.js\"></script>\n";
     echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
      
    
    $formulario = new Formulario ("index.php","post","","form_insData");    
    // -------------------------------------------------------------------------------------
    $formulario -> nueva_tabla();
    $formulario -> linea("Insertar Datos GPS Despacho N°: ".$_GET['num_despac']."",1,"t2");
    
     echo "</table>";    
      echo "<div id='DataDespacGpsID'>";
        echo "<table>";
            
          $formulario -> nueva_tabla();
                  //$formulario -> texto ("Vehículo:","text","num_placax\" id=\"num_placaxID\"",0,50,6,"",$mDataGPS['num_placax']);    
                  $formulario -> lista ("Operador:","cod_operad\" id=\"cod_operadID\" ", $this -> getOperadGps( $mDataGPS , true), "t2");           
                  $formulario -> texto ("Usuario:","text","nom_usrgps\" id=\"nom_usrgpsID\"",0,50,20,"",$mDataGPS['usr_gpsxxx']);    
                  $formulario -> texto ("Clave:","text","clv_usrgps\" id=\"clv_usrgpsID\"",1,50,20,"",base64_decode($mDataGPS['clv_gpsxxx']));    
                  $formulario -> texto ("ID:","text","idx_gpsxxx\" id=\"idx_gpsxxxID\"",0,50,20,"",$mDataGPS['idx_gpsxxx']);    
                  
                  
                  $formulario -> oculto("num_despacH\" id=\"num_despacHID\"",$_GET['num_despac'],0);
                  $formulario -> oculto("Case\" id=\"CaseID\"","Insert_Data_Despac_GPS",0);
                  
          $formulario -> nueva_tabla();        
                  $formulario -> botoni("Guardar","SaveDataGps('DataDespacGpsID')",0);     
                  $formulario -> oculto("fStandar\" id=\"fStandarID\"",DIR_APLICA_CENTRAL,0);
                  $formulario -> oculto("window","central",0);
                 
                  $formulario -> oculto("opcion\" id=\"opcionID\"","3",0);
        echo "</table>";
      echo "</div>";
      
       echo "</table>";          
           echo "<div id='Hiddens' style='display:none'>";
            echo "<table>";
          $formulario -> lista ("Operador:","cod_operadHiddenReq\" id=\"cod_operadHiddenReqID\" ", $this -> getOperadGpsReq( $mDataGPS ), "t2");
            echo "</table>";
          echo "</div>";
           $formulario -> oculto("cod_servic\" id=\"cod_servicID\"",$_REQUEST[cod_servic],0);
         /* echo "<div id='RegresarID' style='display:none'>";
            echo "<table align='center'>";
              $formulario -> oculto("cod_servic",$_REQUEST[cod_servic],0);
              $formulario -> botoni("Regresar","location.href= '?window=central&cod_servic=".$_REQUEST[cod_servic]."&num_despac=".$_GET['num_despac']."'",0);     
            echo "</table>";
          echo "</div>";*/
  }
  
  function BuscaDirecto ()
  {
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/despac_gps.js\"></script>\n";
    echo "<script language=\"JavaScript\" src=\"../".DIR_APLICA_CENTRAL."/js/min.js\"></script>\n";
     
    $formulario = new Formulario ("index.php","post","","form_ins");    
    // -------------------------------------------------------------------------------------
    $formulario -> nueva_tabla();
            $formulario -> linea("Consultar última ubicación Vehiculo",1,"t2");
    
    $formulario -> nueva_tabla();
            $formulario -> texto ("Vehículo:","text","num_placax\" id=\"num_placaxID\"",0,50,6,"",$mDataGPS['num_placax']);    
            $formulario -> lista ("Operador:","cod_operad\" id=\"cod_operadID\" ", $this -> getOperadGps( $mDataGPS , true), "t2");           
            $formulario -> texto ("Usuario:","text","nom_usrgps\" id=\"nom_usrgpsID\"",0,50,20,"",$mDataGPS['usr_gpsxxx']);    
            $formulario -> texto ("Clave:","text","clv_usrgps\" id=\"clv_usrgpsID\"",1,50,20,"",base64_decode($mDataGPS['clv_gpsxxx']));    
            $formulario -> texto ("ID:","text","idx_gpsxxx\" id=\"idx_gpsxxxID\"",0,50,20,"",$mDataGPS['idx_gpsxxx']);    
    $formulario -> nueva_tabla();
            $formulario -> botoni("Aceptar","SaveDataGps('DataDespacGpsID')",0);   
            $formulario -> oculto("num_despacH\" id=\"num_despacHID\"",$_POST['num_despac'],0);
            $formulario -> oculto("Case\" id=\"CaseID\"","Update_Data_Despac_GPS",0);
    
    $formulario -> cerrar(); 
  }


  
  function getOperadGps( $mData, $mOption = NULL )
  {
    $mQueryDataGpsS = "SELECT h.cod_operad, h.nom_operad
                        FROM 
                             ".BD_STANDA.".tab_genera_opegps h
                       WHERE h.cod_operad = '".$mData['cod_operad']."'  ";
    $consulta = new Consulta($mQueryDataGpsS, $this -> conexion);   
    $mDataGPSS = $consulta -> ret_matriz(); 
    
    $mQueryDataGps = "SELECT h.cod_operad, h.nom_operad
                        FROM 
                             ".BD_STANDA.".tab_genera_opegps h
                             ORDER BY 2 ";
    $consulta = new Consulta($mQueryDataGps, $this -> conexion);   
    $mDataGPS = $consulta -> ret_matriz(); 
    
    if($mOption == true)
      return array_merge($mDataGPSS,array(array('---','---')),$mDataGPS);
    else 
      return array_merge(array(array('---','---')),$mDataGPS);
  }
  
  function getOperadGpsReq( $mData = NULL )
  {
    $mQueryDataGpsReq = "SELECT h.cod_operad, h.nom_operad
                        FROM 
                             ".BD_STANDA.".tab_genera_opegps h
                       WHERE h.ind_usaidx = '1' AND
                             h.ind_estado = '1' 
                             ORDER BY 2 ";
    $consulta = new Consulta($mQueryDataGpsReq, $this -> conexion);   
    $mDataGPSReq = $consulta -> ret_matriz();   
    
    return $mDataGPSReq;
  }
  
  function getExistDespac ( $mNumDespac = NULL, $mNumPlacas = NULL , $mCodManifi = NULL)
  {
    if($mNumDespac != NULL)
      $mQueryExist = "SELECT 1 FROM ".BASE_DATOS.".tab_despac_despac WHERE num_despac = '".$mNumDespac."'";
      
    if($mCodManifi != NULL)
      $mQueryExist = "SELECT 1 FROM ".BASE_DATOS.".tab_despac_despac WHERE cod_manifi = '".$mCodManifi."'";
      
    if($mNumPlacas != NULL)
      $mQueryExist = "SELECT 1 FROM ".BASE_DATOS.".tab_despac_vehige WHERE num_placax = '".$mNumPlacas."'";
    
    $consulta = new Consulta($mQueryExist, $this -> conexion);   
    $mExists = $consulta -> ret_matriz(); 
    return  $mExists[0][0] == NULL ? false : true;
  }
  
  function getDataGps ( $mNumDespac = NULL)
  { 
    if($mNumDespac['num_despac'] != NULL)
      $mQuery = "a.num_despac = '".$mNumDespac['num_despac']."'";
    
    $mQueryDataGps = "SELECT g.num_despac,  
                                 d.num_placax,
                                 g.cod_opegps AS cod_operad,
                                 IF(g.idx_gpsxxx IS NULL , 'NULL', g.idx_gpsxxx) AS idx_gpsxxx , 
                                 g.nom_usrgps AS usr_gpsxxx,  
                                 g.clv_usrgps AS clv_gpsxxx, 
                                 d.cod_transp, a.cod_manifi,
                                 h.nom_operad
                            FROM 
                                 " .BASE_DATOS. ".tab_despac_despac a LEFT JOIN " .BASE_DATOS. ".tab_tercer_tercer n ON n.cod_tercer = a.cod_asegur,
                                 " .BASE_DATOS. ".tab_despac_vehige d,
                                 " .BASE_DATOS. ".tab_tercer_tercer e,
                                 " .BASE_DATOS. ".tab_tercer_tercer f,
                                 " .BASE_DATOS. ".tab_despac_gpsxxx g,
                                 " .BD_STANDA.".tab_genera_opegps h
                                 
                           WHERE a.num_despac = d.num_despac AND
                                 d.cod_conduc = e.cod_tercer AND
                                 d.cod_transp = f.cod_tercer AND
                                 a.num_despac = g.num_despac AND
                                 g.cod_opegps = h.cod_operad AND
                                 $mQuery
                                 GROUP BY 1 ORDER BY g.fec_creaci DESC LIMIT 1             
                                 ";
    //echo "<pre>"; print_r( $mQueryDataGps ); echo "</pre>";
    $consulta = new Consulta($mQueryDataGps, $this -> conexion);   
    $mDataGPS = $consulta -> ret_matriz(); 
    return $mDataGPS[0];
  }
  /*
  * Busca si el despacho está en ruta
  *
  */
  function getDataDespac ( $mNumDespac )
  {
    if($mNumDespac['num_despac'] != NULL)
      $mQuery = "a.num_despac = '".$mNumDespac['num_despac']."'";
    
    if($mNumDespac['num_placas'] != NULL)
      $mQuery = "d.num_placax = '".$mNumDespac['num_placas']."'";
    
    $mQueryDataGps = "SELECT g.num_despac,  
                                 d.num_placax,
                                 g.cod_opegps AS cod_operad,
                                 IF(g.idx_gpsxxx IS NULL , 'NULL', g.idx_gpsxxx) AS idx_gpsxxx , 
                                 g.nom_usrgps AS usr_gpsxxx,  
                                 g.clv_usrgps AS clv_gpsxxx, 
                                 d.cod_transp, a.cod_manifi,
                                 h.nom_operad
                            FROM 
                                 " .BASE_DATOS. ".tab_despac_despac a LEFT JOIN " .BASE_DATOS. ".tab_tercer_tercer n ON n.cod_tercer = a.cod_asegur,
                                 " .BASE_DATOS. ".tab_despac_vehige d,
                                 " .BASE_DATOS. ".tab_tercer_tercer e,
                                 " .BASE_DATOS. ".tab_tercer_tercer f,
                                 " .BASE_DATOS. ".tab_despac_gpsxxx g,
                                 " .BD_STANDA.".tab_genera_opegps h
                                 
                           WHERE a.num_despac = d.num_despac AND
                                 d.cod_conduc = e.cod_tercer AND
                                 d.cod_transp = f.cod_tercer AND
                                 a.num_despac = g.num_despac AND
                                 g.cod_opegps = h.cod_operad AND
                                 a.fec_salida Is NOT NULL AND
                                 a.fec_llegad IS NULL AND
                                 a.ind_anulad = 'R' AND 
                                 a.ind_planru = 'S' AND 
                                 $mQuery 
                                 GROUP BY 1            
                                 ";
    
    $consulta = new Consulta($mQueryDataGps, $this -> conexion);   
    $mDataGPS = $consulta -> ret_matriz(); 
    
    if(count($mDataGPS) <= 0)  
      $mReturn = false;
    else
      $mReturn = true;
      
    return $mReturn;
  }
  function getDataPlaca ( $mNumDespac )
  {
    if($mNumDespac['num_placas'] != NULL)
      $mQuery = "d.num_placax = '".$mNumDespac['num_placas']."'";
      
    if($mNumDespac['cod_manifi'] != NULL)
      $mQuery = "a.cod_manifi = '".$mNumDespac['cod_manifi']."'";
    
    $mQueryDataGps = "SELECT a.num_despac 
                                 
                            FROM 
                                 " .BASE_DATOS. ".tab_despac_despac a,
                                 " .BASE_DATOS. ".tab_despac_vehige d
                                 
                                 
                           WHERE a.num_despac = d.num_despac AND
                                 
                                 a.fec_salida Is NOT NULL AND
                                 a.fec_llegad IS NULL AND
                                 a.ind_anulad = 'R' AND 
                                 a.ind_planru = 'S' AND 
                                 $mQuery 
                                 GROUP BY 1            
                                 ";
    
    $consulta = new Consulta($mQueryDataGps, $this -> conexion);   
    $mDataGPS = $consulta -> ret_matriz(); 
    return $mDataGPS[0]['num_despac'];
  }
  
  function getUbicaDespac ( $mData, $mFilter = NULL )
  { 
    
    if ( $mFilter['cod_operad'] != NULL && $mFilter['num_placax'] != NULL && $mFilter['nom_usrgps'] != NULL && $mFilter['clv_usrgps'] != NULL )
    {
      $mData['cod_operad'] = $mFilter['cod_operad'];
      $mData['num_placax'] = $mFilter['num_placax'] == NULL ? $mFilter['num_placas'] : $mFilter['num_placax'];
      $mData['usr_gpsxxx'] = $mFilter['nom_usrgps'];
      $mData['clv_gpsxxx'] = base64_encode($mFilter['clv_usrgps']);
      $mData['idx_gpsxxx'] = $mFilter['idx_gpsxxx'];
    }      
      
    $mError  = array();
    $mDatax[] = $mData;

    foreach($mDatax as $fVehiculo)
    {   
        unset( $novedaGPS );
        $novedaGPS = array();
        //----------------------------------------------------Satrack------------------------------------------------------------------
        if( $fVehiculo['cod_operad'] == '8300596993' )
        {
          try 
          {
            ini_set( "soap.wsdl_cache_enabled", "0" ); 
            if (!class_exists('SoapClient'))
            {
              die ("No se encuentra instalado el módulo PHP-SOAP.");
            }
            //Se obtiene la ultima novedad gps
            $oSoapClient = new soapclient( 'http://ww3.satrack.com/webserviceeventos/getEvents.asmx?WSDL', array( "trace" => "1", 'encoding' => 'ISO-8859-1' ) );
           
            $mParametros = new StdClass();
            $mParametros -> UserName = $fVehiculo['usr_gpsxxx'];
            $mParametros -> Password = base64_decode( $fVehiculo['clv_gpsxxx'] );
            $mParametros -> PhysicalID = $fVehiculo['num_placax'];

            //Ese método retorna <NewDataSet /> cuando hay error de autenticacion o no retorna datos
            $result = $oSoapClient -> getLastEventString( $mParametros );  
           
            $response = utf8_encode( $result -> getLastEventStringResult );       
            
            if( $response != NULL )
            { 
              $xmlObject = new SimpleXMLElement( $response );
              if( count( $xmlObject->children() ) > 0 )
              {
                //Se extrae del xml retornado la informacion de la novedad si retorna LastEvents(hijo)
                $Ubicacion = utf8_encode( 'Ubicación' );
                $novedaGPS['num_placax'] = (string) $xmlObject -> LastEvents -> Placa;
                $novedaGPS['fec_noveda'] = date("Y-m-d H:i", strtotime( (string) $xmlObject -> LastEvents -> Fecha_x0020_GPS ) );
                $novedaGPS['val_veloci'] = utf8_decode( (string) $xmlObject -> LastEvents -> Velocidad_x0020_y_x0020_Sentido );
                $novedaGPS['val_longit'] = (string) $xmlObject -> LastEvents -> Longitud;
                $novedaGPS['val_latitu'] = (string) $xmlObject -> LastEvents -> Latitud;
                $novedaGPS['det_ubicac'] = utf8_decode( (string) $xmlObject -> LastEvents -> $Ubicacion );
                $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
                $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
                $mError[error] = true;

              }
              else              
                $mError[error] = "No hay reporte de ubicaciÃ³n.";
                       
            }
            else                     
              $mError[error] = "No hay respuesta de la Interfaz.";             
            
          }
          catch(SoapFault $e)
          {            
             $mError[error] = $e->getMessage(); 
          }         
        }
        
        //---------------------------------------------------Servientrega------------------------------------------------------------------
        elseif( $fVehiculo['cod_operad'] == '860512330' ) 
        {
          //Si el operador es Servientrega se obtiene la ultima novedad GPS
          $data = file_get_contents("http://200.31.212.16/servientrega/servicio2.php?placa=".$fVehiculo['num_placax']);
          $data = explode( '<?xml version="1.0" encoding="ISO-8859-1"?>', $data );
          $response = utf8_encode( $data[1] );
          
          $xmlObject = new SimpleXMLElement( $response );
          
          if( count( $xmlObject->children() ) > 0 && (string) $xmlObject -> estado != 520 && (string) $xmlObject -> posicion != 'nodata' )
          {
            //Se extrae del xml retornado la informacion de la novedad si retorna algo
            //El codigo de estado 520 es retornado cuando la placa no retorna datos
            $novedaGPS['fec_noveda'] = date("Y-m-d H:i", strtotime( (string) $xmlObject -> tiempo_gps ) );
            $novedaGPS['val_veloci'] = (string) $xmlObject -> velocidad;
            $novedaGPS['val_longit'] = (string) $xmlObject -> longitud;
            $novedaGPS['val_latitu'] = (string) $xmlObject -> latitud;
            $novedaGPS['det_ubicac'] = (string) $xmlObject -> posicion;
            $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
            $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
            $mError[error] = true;
          }
          else
          {
            $mError[error] = (string)$xmlObject -> msg;
          }
        }
        
        //---------------------------------------------------Rastrelital------------------------------------------------------------------
         elseif( $fVehiculo['cod_operad'] == '9004892' )
        {
          $oSoapClient = new soapclient( 'http://www.rastrea.net/web%20services/utrax.ws_SAT/ws_SAT.asmx?WSDL', array( "trace" => 1, 'encoding'=>'ISO-8859-1' ) );
          $split1=substr($fVehiculo[num_placax], 0, 3);  
          $split2=substr($fVehiculo[num_placax], 3, 6);
          $Plates = $split1." ".$split2;

          $mParams = array
                    (
                      "User" => $fVehiculo['usr_gpsxxx'],
                      "Password" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                      "Plates" => $Plates
                    );                         

          $result = $oSoapClient -> GetLastPosition( $mParams );

          if( $result -> GetLastPositionResult -> eCode == 1 )
          {
            $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $result -> GetLastPositionResult -> DateTime_GPS) );
            $novedaGPS['val_latitu'] = $result -> GetLastPositionResult -> Latitude;
            $novedaGPS['val_longit'] = $result -> GetLastPositionResult -> Longitude;
            $novedaGPS['val_veloci'] = $result -> GetLastPositionResult -> Speed;
            $novedaGPS['det_ubicac'] = utf8_decode( (string) $result -> GetLastPositionResult -> Reference);        
            
            $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
            $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
            $mError[error] = true;
          }
          else
          {
            $mError[error] = (string)$result;
          }
        }
        //---------------------------------------------------Rastrelital------------------------------------------------------------------
         elseif( $fVehiculo['cod_operad'] == '9004892' )
        {
          $oSoapClient = new soapclient( 'http://www.rastrea.net/web%20services/utrax.ws_SAT/ws_SAT.asmx?WSDL', array( "trace" => 1, 'encoding'=>'ISO-8859-1' ) );
          $split1=substr($fVehiculo[num_placax], 0, 3);  
          $split2=substr($fVehiculo[num_placax], 3, 6);
          $Plates = $split1." ".$split2;

          $mParams = array
                    (
                      "User" => $fVehiculo['usr_gpsxxx'],
                      "Password" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                      "Plates" => $Plates
                    );                         

          $result = $oSoapClient -> GetLastPosition( $mParams );

          if( $result -> GetLastPositionResult -> eCode == 1 )
          {
            $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $result -> GetLastPositionResult -> DateTime_GPS) );
            $novedaGPS['val_latitu'] = $result -> GetLastPositionResult -> Latitude;
            $novedaGPS['val_longit'] = $result -> GetLastPositionResult -> Longitude;
            $novedaGPS['val_veloci'] = $result -> GetLastPositionResult -> Speed;
            $novedaGPS['det_ubicac'] = utf8_decode( (string) $result -> GetLastPositionResult -> Reference);        
            
            $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
            $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
            $mError[error] = true;
          }
          else
          {
            $mError[error] = (string)$result;
          }
        }
         // ---- Autos SURA 
        elseif(  $fVehiculo['cod_operad'] == '811036875' ) 
        {
          //echo "Sonar AVL System ( Autos SURA )";
          try
          {
            $oSoapClient = new soapclient( 'https://www.sonaravl.com/b2bsura/Service.asmx?WSDL' , array( "trace" => TRUE, 'encoding' => 'ISO-8859-1' ) );
            $params3 = array(
                              "User"     => $fVehiculo['usr_gpsxxx'],
                              "Password" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                              "mId"      => $fVehiculo['idx_gpsxxx']
                            );
                            
            $mResult = $oSoapClient -> GET_LastLocation( $params3 ); 
             
             if( $mResult -> GET_LastLocationResult -> status == 'OK' )
              {
                $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $mResult -> GET_LastLocationResult -> evtList -> EventLocation -> gps_GMT ) );
                $date_r = getdate(strtotime( $novedaGPS['fec_noveda'] ));
                $date_result = date('Y-m-d H:i', mktime(($date_r["hours"]-5),($date_r["minutes"]+0),($date_r["seconds"]+0),($date_r["mon"]+0),($date_r["mday"]+0),($date_r["year"]+0)));
                $novedaGPS['fec_noveda'] = $date_result;

                $novedaGPS['val_latitu'] = (string) $mResult -> GET_LastLocationResult -> evtList -> EventLocation -> latitude;
                $novedaGPS['val_longit'] = (string) $mResult -> GET_LastLocationResult -> evtList -> EventLocation -> longitude;
                $novedaGPS['val_veloci'] = (string) $mResult -> GET_LastLocationResult -> evtList -> EventLocation -> speed;
                $novedaGPS['det_ubicac'] = iconv( 'UTF-8', 'ISO-8859-1', (string) $mResult -> GET_LastLocationResult -> evtList -> EventLocation -> address );
                
                $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
                $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
                $mError[error] = true;
              }
              else
              {
                $mError[error] = (string)$result;
              }
          }
          catch(SoapFault $e )
          {
              $mError[error] = $e -> faultstring;
              //echo "<pre>"; print_r($error); echo "</pre>";
          } 
        }
        //---------------------------------------------------24satelital------------------------------------------------------------------
        elseif(  $fVehiculo['cod_operad'] == '900014002' )
        {
          try
          {
            $oSoapClient = new soapclient( 'http://www.24satelital.net/ws/server.php?wsdl', array( "trace" => "1" , 'encoding' => 'UTF-8') );
            $mParam = array( "user"  => $fVehiculo['usr_gpsxxx'],
                             "pws"   => $fVehiculo['clv_gpsxxx'],
                             "placa" => $fVehiculo['num_placax']
                            );
            
            $result = $oSoapClient -> __call ( "UltByPlaca", $mParam );      
            $mDataEmpresa = simplexml_load_string( base64_decode($result));
            
            if( $mDataEmpresa->error[0] == 'error' )     
             throw new Exception ($mDataEmpresa->errorDescription[0]);     
            else 
            {
              $novedaGPS['num_placax'] = (string) $mDataEmpresa->placa;
              $novedaGPS['fec_noveda'] = date("Y-m-d H:i", strtotime( (string) $mDataEmpresa->tiempo_gps ) );
              $novedaGPS['val_veloci'] = utf8_decode( (string) $mDataEmpresa->velocidad );
              $novedaGPS['val_longit'] = (string) $mDataEmpresa->longitud;
              $novedaGPS['val_latitu'] = (string) $mDataEmpresa->latitud;
              $novedaGPS['det_ubicac'] = utf8_decode( (string) $mDataEmpresa->posicion );
              $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
              $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];    
              //echo "<pre>"; print_r($novedaGPS); echo "</pre>";    
            }
          }
          catch( Exception $e )
          {
            /*echo '<br/>Hubo un error: '.$e -> getMessage().'<hr>';
            echo "<pre>soapofalut object e<br/>";
              var_dump( $e );
            echo "</pre>";*/
          }
          
        }
        //---------------------------------------------------Rilsa------------------------------------------------------------------
        elseif(  $fVehiculo['cod_operad'] == '900013074' )
        {
          try
          {
            $oSoapClient = new soapclient( 'http://web1ws.shareservice.co/HST/wsHistoryGetByPlate.asmx?WSDL', array( "trace" => "1" , 'encoding' => 'UTF-8') );
            $mParam = array( "sLogin"  => $fVehiculo['usr_gpsxxx'],
                             "sPassword"   => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                             "sPlate" => $fVehiculo['num_placax']
                            );
            //echo "<pre>"; print_r($mParam); echo "</pre>";    
            $mResult = $oSoapClient -> HistoyDataLastLocationByPlate( $mParam );                  
            $mXml = $mResult -> HistoyDataLastLocationByPlateResult -> any;
         
            $xmlObject = new SimpleXMLElement( $mXml );
           
            if( $xmlObject->Response->Status -> code != '100' )     
               $mError[error] = $mDataEmpresa->errorDescription[0];     
            else 
            {
              $plate_array = (array) $xmlObject->Response->Plate;
              
              $novedaGPS['num_placax'] = (string) $plate_array['@attributes']['id'];
              $novedaGPS['fec_noveda'] = date("Y-m-d H:i", strtotime( (string) $xmlObject->Response->Plate->hst-> DateTimeGPS ) );
              $novedaGPS['val_veloci'] = (string) $xmlObject->Response->Plate->hst->Speed;
              $novedaGPS['val_longit'] = (string) $xmlObject->Response->Plate->hst->Longitude;
              $novedaGPS['val_latitu'] = (string) $xmlObject->Response->Plate->hst->Latitude;
              $novedaGPS['det_ubicac'] = (string) $xmlObject->Response->Plate->hst->Location;
              $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
              $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];    
              echo "<pre>"; print_r($novedaGPS); echo "</pre>";    
            }
          }
          catch( Exception $e )
          {
            /*echo '<br/>Hubo un error: '.$e -> getMessage().'<hr>';
            echo "<pre>soapofalut object e<br/>";
              var_dump( $e );
            echo "</pre>";*/
          }
          
        }
        //---------------------------------------------------Omnitracs------------------------------------------------------------------
        elseif( $fVehiculo['cod_operad'] == '88372172' ) //88372172 
        {
          $oSoapClient = new soapclient( 'https://www.omnitracsportal.com/oet/Integration.asmx?WSDL', array( "trace" => 1, 'encoding'=>'ISO-8859-1' ) );

          $mParams = array
                    (
                      "Usuario" => $fVehiculo['usr_gpsxxx'],
                      "Clave" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                      "Placa" => $fVehiculo[num_placax]
                    );      
          //echo "<pre>"; print_r($mParams); echo "</pre>"; 
          
          $result = $oSoapClient -> GetLastPosition( $mParams );
          $result = $result -> GetLastPositionResult -> any;          
          $xmlObject = new SimpleXMLElement( $result );    
          
          if( $xmlObject -> getName() != 'error_gps' )
          {
            $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $xmlObject -> tiempo_gps ) );
            $novedaGPS['val_latitu'] = (string) $xmlObject -> latitud;
            $novedaGPS['val_longit'] = (string) $xmlObject -> longitud;
            $novedaGPS['val_veloci'] = (string) $xmlObject -> velocidad;
            $novedaGPS['det_ubicac'] = iconv( 'UTF-8', 'ISO-8859-1', (string) $xmlObject -> posicion );
            $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
            $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
            $mError[error] = true;
          }
          else
          {
            $mError[error] = (string)$xmlObject -> msg;
          }          
        }
        //---------------------------------------------------WideTech-----------------------------------------------------------------------------
        elseif( $fVehiculo['cod_operad'] == '9001387' ) 
        {
          try
          { 
            $oSoapClient = new soapclient( 'http://ws.widetech.com.co/wsHistoryGetByPlate.asmx?WSDL', array( "trace" => "1", 'encoding' => 'ISO-8859-1' ) );
           
           $mParams = array
                      (
                        "sLogin" => $fVehiculo['usr_gpsxxx'],
                        "sPassword" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                        "sPlate" => $fVehiculo[num_placax]
                      );
           
            $result = $oSoapClient -> HistoyDataLastLocationByPlate( $mParams );
            $result = $result -> HistoyDataLastLocationByPlateResult -> any;          
            $xmlObject = new SimpleXMLElement( $result );
           
            if( $xmlObject -> Response -> Status -> code == '100' )
            {
              $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $xmlObject -> Response -> Plate -> hst -> DateTimeGPS ) );
              $novedaGPS['val_latitu'] = (string) $xmlObject -> Response -> Plate -> hst -> Latitude;
              $novedaGPS['val_longit'] = (string) $xmlObject -> Response -> Plate -> hst -> Longitude;
              $novedaGPS['val_veloci'] = (string) $xmlObject -> Response -> Plate -> hst -> Speed;
              $novedaGPS['det_ubicac'] = iconv( 'UTF-8', 'ISO-8859-1', (string) $xmlObject -> Response -> Plate -> hst -> Location -> B );
              $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
              $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
            }
            else
              $mError[error] = (string)"Code: ".$xmlObject -> Response -> Status -> code." - MSG: ".$xmlObject -> Response -> Status -> description;
            
                          
          }
          catch(SoapFault $e )
          {
              $error = $e -> faultstring;
              echo "<pre>"; print_r($error); echo "</pre>";
          } 
        }
        
        //---------------------------------------------------Tracker---------------------------------------------------
        elseif( $fVehiculo['cod_operad'] == '830141109'  )
        {
          try
          {
            $oSoapClient = new soapclientnusoap( 'http://www.tracker.com.co/gps/ws/servicio_v2.php?wsdl',true );
            $mParams = array
                      (
                        "usuario" => $fVehiculo['usr_gpsxxx'],
                        "clave" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                        "campos" => "Velocidad",
                        "placa" => $fVehiculo['num_placax']
                      );
            $result = $oSoapClient -> call ( "ultimo_punto", $mParams );
           
            if( $result !== FALSE )
            {
              $pos1 = strpos( $result, "<estado>", 1 );
              $pos2 = strpos( $result, "</movil>", 1 );
              $result = substr( $result, $pos1, ( $pos2 - $pos1 ) );
              $result = "<root>".$result."</root>";
              $xmlObject = new SimpleXMLElement( $result );
              
              if( count( $xmlObject->children() ) > 0 && (string) $xmlObject -> estado == 'OK' )
              {
                $novedaGPS['fec_noveda'] = (string) $xmlObject -> fecha_gps;
                $novedaGPS['val_veloci'] = (string) $xmlObject -> velocidad;
                $novedaGPS['val_longit'] = (string) $xmlObject -> longitud;
                $novedaGPS['val_latitu'] = (string) $xmlObject -> latitud;
                $novedaGPS['det_ubicac'] = (string) $xmlObject -> georef;
                $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
                $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
                $mError[error] = true;
              }
              else             
                $mError[error] = (string) $xmlObject -> estado;
              
            }
            else            
              $mError[error] = "El Servidor Tracker no responde.";            
          
          }
          catch(SoapFault $e)
          {
              $error = $e -> faultstring;
              $mError[error] = (string)$error;
          } 
          
          
        
        }
        //---------------------------------------------------INTEGRA GPS-------------------------------------------------------------
        elseif( $fVehiculo['cod_operad'] == '830045348'  )
        {
          try
          { 
            
            $oSoapClient = new soapclient( 'http://190.145.109.121/wsintegra/WSRatreo.asmx?wsdl', array( "trace" => 1, 'encoding'=>'UTF-8' ) );
            $mParams = array
                      (
                        "user" => $fVehiculo['usr_gpsxxx'],
                        "password" => base64_decode( $fVehiculo['clv_gpsxxx'] ),
                        "placa" => $fVehiculo['num_placax']
                      );
            //print_r( $mParams );          
            $result = $oSoapClient -> getPosition( $mParams );
            if($result == NULL)
              throw new Exception("El Servidor No Responde");
              
            $result = $result -> getPositionResult -> any;
            
            $xmlObject = new SimpleXMLElement( $result );
            
            if( $xmlObject -> getName() != 'error' )
            {
              $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', strtotime( (string) $xmlObject -> tiempo_gps ) );
              $novedaGPS['val_latitu'] = (string) $xmlObject -> latitude;
              $novedaGPS['val_longit'] = (string) $xmlObject -> longitud;
              $novedaGPS['val_veloci'] = (string) $xmlObject -> velocidad;
              $novedaGPS['det_ubicac'] = iconv( 'UTF-8', 'ISO-8859-1', (string) $xmlObject -> posicion );
              $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
              $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
              $mError[error] = true;
            }
            else
              throw new SoapFault("Datos erroneos o el Servidor No Responde");
            
          }
          catch (SoapFault  $e)
          {
            $mError[error] = $e->getMessage(); 
          }
          
         
          
          //print_r( $novedaGPS );
        }
        
        //---------------------------------------------------Rastrack - Grupo OET------------------------------------------------------------
        elseif( $fVehiculo['cod_operad'] == '830126626' || $fVehiculo['cod_operad'] == '830076669'  )
        {
          if( $fp = fsockopen( "200.31.91.229", 60, $errno, $errstr, 4 ) )
          {
            //Se elabora el comando a enviar mediante la Interfaz TCP/IP con Rastrack
            $out =  "<Rastrac>";
            $out .= "<RastracMessage>";
            $out .= "<messagetype>RastracCommand</messagetype>";
            $out .= "<seqnum>124</seqnum>";
            $out .= "<command>GetVehicleState</command>";
            $out .= "<id>".$fVehiculo['idx_gpsxxx']."</id>";
            $out .= "</RastracMessage>";
            $out .= "</Rastrac>";
            //echo "\nComando enviado<br>".htmlspecialchars( $out, ENT_QUOTES );
            //Se envia el comando al servidor de Ratrack
            fwrite( $fp, $out );
            
            $caracter = array();
            //Se captura la respuesta del servidor de Rastrack
            for( $i = 0; $i <= 2000; $i++ )
            {
              $caracter[$i] = fgetc( $fp );
              //Se determina el fin de la respuesta se tuvo que hacer de esta forma porque esa respuesta no tiene fin y no tiene una longitud fija
              if( $caracter[$i] == '>' && $caracter[$i-9] == '<' && $caracter[$i-8] == '/' && $caracter[$i-7] == 'R' && $caracter[$i-6] == 'a' && $caracter[$i-5] == 's' && $caracter[$i-4] == 't' && $caracter[$i-3] == 'r' && $caracter[$i-2] == a && $caracter[$i-1] == 'c' )
                break;
            }
            
            $result = implode( '', $caracter );
            
            //echo "\nRespuesta<br>".htmlspecialchars( $result, ENT_QUOTES );
            
            //Se cierra la conexión al servidor Rastrack
            fclose($fp);
            
            //Se lee la respuesta segun la estructura xml
            $result = utf8_encode( $result );
            $xmlObject = new SimpleXMLElement( $result );
            
            $arrayNoReporta = array( 75, 76, 77, 78, 79 );
            
            if( count( $xmlObject -> children() ) > 0 && !in_array( (string)$xmlObject -> RastracMessage -> Event, $arrayNoReporta) && !(string)$xmlObject -> RastracMessage -> ErrorMessage && (string) $xmlObject -> RastracMessage -> Latitude && (string) $xmlObject -> RastracMessage -> Longitude )
            {
              //Se calcula la fecha del reporte GPS reconstruyendo las piezas
              $day = (string) $xmlObject -> RastracMessage -> Day;
              $month = (string) $xmlObject -> RastracMessage -> Month;
              $year = (string) $xmlObject -> RastracMessage -> Year;
              //El tiempo para Rastrack es la cantidad de segundos transcurridos desde las 00:00
              $time = (string) $xmlObject -> RastracMessage -> Time;
              $hourInt = intval( $time / 3600 );
              $hourDecim = $time / 3600;
              $minInt = intval( ( $hourDecim - $hourInt ) * 60 );
              $minDecim = ( $hourDecim - $hourInt ) * 60;
              $segInt = intval( ( $minDecim - $minInt ) * 60 );
              
              $novedaGPS['fec_noveda'] = date( 'Y-m-d H:i', mktime( $hourInt, $minInt, $segInt, $month, $day, $year ) );
              $novedaGPS['val_latitu'] = (string) $xmlObject -> RastracMessage -> Latitude;
              $novedaGPS['val_longit'] = (string) $xmlObject -> RastracMessage -> Longitude;
              $novedaGPS['val_veloci'] = (string) $xmlObject -> RastracMessage -> Speed;
              $novedaGPS['det_ubicac'] = utf8_decode( (string) $xmlObject -> RastracMessage -> StreetName . ' ' . (string) $xmlObject -> RastracMessage -> ExAddr );
              $novedaGPS['all_infgps'] = "Ubicacion: ".$novedaGPS['det_ubicac'];
              $novedaGPS['all_infgps'] .= ". Velocidad: ".$novedaGPS['val_veloci'];
            }
            else
            {
              //echo "\nEl vehiculo no esta reportando GPS";
              //echo "\nError: ".(string)$xmlObject -> RastracMessage -> ErrorMessage.'. '.(string)$xmlObject -> RastracMessage -> Result;
              $mError[error] = "El vehiculo no esta reportando GPS <br>".(string)$xmlObject -> RastracMessage -> ErrorMessage.'. '.(string)$xmlObject -> RastracMessage -> Result;
            }
          }
          else
          {
            //echo 'Error al abrir la conexion a Rastrack<br>';
            //echo 'Numero de error: '.$errno;
            $mError[error] = $errstr;
          }
        }
        
    }    
    $mReturn[0]= $mError;
    $mReturn[1]= $novedaGPS;
    //echo "<pre>"; print_r( $mReturn ); echo "</pre>";     
    return $mReturn;
  }
  
  

  function Formulario()
  {
    echo "</table>";    
    echo "<div id='inf_gpsdes'>";
    echo "<table>";
      $formulario -> nueva_tabla();
      $formulario -> linea("Datos GPS",1,"t2");

      $formulario -> nueva_tabla();
      $formulario -> texto ("Vehiculo:","text","num_placax",1,50,5,"","");
      $formulario -> texto ("Usuario:","text","usr_gpsxxx",0,50,5,"","");
      $formulario -> texto ("Clave:","text","clv_gpsxxx",0,50,5,"","");
      $formulario -> texto ("ID GPS:","text","idx_gpsxxx",0,50,5,"","");

    echo "</table>";
    echo "</div>";
  }//FIN FUNCION FORMULARIO



}//FIN CLASE PROC_DESPAC

   $proceso = new Proc_despac($this -> conexion, $this -> usuario_aplicacion, $this-> codigo);

?>